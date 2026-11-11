<?php

namespace App\Http\Controllers;

use App\Models\CloudConnection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class CloudConnectionController extends Controller
{
    private function settings(string $provider): array
    {
        abort_unless(in_array($provider, ['google', 'dropbox'], true), 404);
        return config('services.cloud_storage.'.$provider, []);
    }

    public function connect(Request $request, string $provider): RedirectResponse
    {
        $settings = $this->settings($provider);
        if (empty($settings['client_id']) || empty($settings['redirect'])) {
            return back()->with('error', ucfirst($provider).' connection is not configured yet. Add its OAuth credentials in hosting Environment Variables.');
        }

        $state = Str::random(48);
        $request->session()->put('cloud_oauth_state_'.$provider, $state);

        $query = [
            'client_id' => $settings['client_id'],
            'redirect_uri' => $settings['redirect'],
            'response_type' => 'code',
            'state' => $state,
        ];

        if ($provider === 'google') {
            $query['scope'] = 'openid email https://www.googleapis.com/auth/drive.file';
            $query['access_type'] = 'offline';
            $query['prompt'] = 'consent';
            $url = 'https://accounts.google.com/o/oauth2/v2/auth';
        } else {
            $query['token_access_type'] = 'offline';
            $query['scope'] = 'account_info.read files.metadata.read files.content.read files.content.write';
            $url = 'https://www.dropbox.com/oauth2/authorize';
        }

        return redirect()->away($url.'?'.http_build_query($query));
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        $settings = $this->settings($provider);
        $expectedState = (string) $request->session()->pull('cloud_oauth_state_'.$provider, '');
        $receivedState = (string) $request->query('state', '');

        if ($expectedState === '' || $receivedState === '' || !hash_equals($expectedState, $receivedState)) {
            return redirect()->route('cloud.index')->with('error', 'Cloud connection verification failed. Please try connecting again.');
        }

        if ($request->filled('error') || !$request->filled('code')) {
            return redirect()->route('cloud.index')->with('error', 'The cloud provider did not approve the connection.');
        }

        if (empty($settings['client_id']) || empty($settings['client_secret']) || empty($settings['redirect'])) {
            return redirect()->route('cloud.index')->with('error', 'Cloud OAuth credentials are incomplete in hosting settings.');
        }

        try {
            $tokenResponse = Http::asForm()->acceptJson()->post($settings['token_url'], [
                'code' => $request->query('code'),
                'client_id' => $settings['client_id'],
                'client_secret' => $settings['client_secret'],
                'redirect_uri' => $settings['redirect'],
                'grant_type' => 'authorization_code',
            ]);

            if (!$tokenResponse->successful() || !$tokenResponse->json('access_token')) {
                report(new \RuntimeException('Cloud OAuth token exchange failed for '.$provider.': '.$tokenResponse->status()));
                return redirect()->route('cloud.index')->with('error', 'Could not complete the cloud provider connection.');
            }

            $token = $tokenResponse->json();
            $profile = $provider === 'google'
                ? Http::withToken($token['access_token'])->get('https://www.googleapis.com/oauth2/v2/userinfo')
                : Http::withToken($token['access_token'])->post('https://api.dropboxapi.com/2/users/get_current_account');

            if (!$profile->successful()) {
                report(new \RuntimeException('Cloud provider profile lookup failed for '.$provider.': '.$profile->status()));
                return redirect()->route('cloud.index')->with('error', 'Connected account details could not be verified.');
            }

            $profileData = $profile->json();
            $accountId = (string) ($provider === 'google' ? ($profileData['id'] ?? '') : ($profileData['account_id'] ?? ''));
            if ($accountId === '') {
                return redirect()->route('cloud.index')->with('error', 'The provider did not return a usable account identifier.');
            }

            CloudConnection::updateOrCreate(
                ['user_id' => $request->user()->id, 'provider' => $provider],
                [
                    'provider_account_id' => $accountId,
                    'display_name' => $provider === 'google'
                        ? ($profileData['email'] ?? $profileData['name'] ?? null)
                        : ($profileData['name']['display_name'] ?? $profileData['email'] ?? null),
                    'access_token' => $token['access_token'],
                    'refresh_token' => $token['refresh_token'] ?? null,
                    'token_expires_at' => isset($token['expires_in']) ? now()->addSeconds((int) $token['expires_in']) : null,
                    'scopes' => isset($token['scope']) ? preg_split('/\s+/', trim($token['scope'])) : [],
                ]
            );

            return redirect()->route('cloud.index')->with('success', ucfirst($provider).' account connected securely.');
        } catch (Throwable $exception) {
            report($exception);
            return redirect()->route('cloud.index')->with('error', 'Cloud provider connection failed. Please try again.');
        }
    }

    public function disconnect(Request $request, string $provider): RedirectResponse
    {
        $settings = $this->settings($provider);
        $connection = CloudConnection::query()
            ->where('user_id', $request->user()->id)
            ->where('provider', $provider)
            ->first();

        if (!$connection) {
            return redirect()->route('cloud.index')->with('success', ucfirst($provider).' account is already disconnected.');
        }

        $tokenToRevoke = $connection->refresh_token ?: $connection->access_token;
        if ($tokenToRevoke) {
            try {
                $revocation = $provider === 'google'
                    ? Http::asForm()->post('https://oauth2.googleapis.com/revoke', ['token' => $tokenToRevoke])
                    : Http::withToken($tokenToRevoke)->post('https://api.dropboxapi.com/2/auth/token/revoke');

                if (!$revocation->successful()) {
                    report(new \RuntimeException('Cloud provider token revocation failed for '.$provider.': '.$revocation->status()));
                    return redirect()->route('cloud.index')->with('error', 'The provider did not confirm revocation. Your connection remains saved so you can retry safely.');
                }
            } catch (Throwable $exception) {
                report($exception);
                return redirect()->route('cloud.index')->with('error', 'Could not contact the provider to revoke access. Your connection remains saved so you can retry.');
            }
        }

        $connection->delete();

        return redirect()->route('cloud.index')->with('success', ucfirst($provider).' access revoked and account disconnected. Files in that provider account were not deleted.');
    }
}
