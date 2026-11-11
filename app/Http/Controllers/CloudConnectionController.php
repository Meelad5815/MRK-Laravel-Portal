<?php

namespace App\Http\Controllers;

use App\Models\CloudConnection;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    private function revokeIssuedGrant(string $provider, array $tokenData): bool
    {
        $token = $provider === 'dropbox'
            ? ($tokenData['access_token'] ?? null)
            : ($tokenData['refresh_token'] ?? $tokenData['access_token'] ?? null);

        if (!$token) {
            return false;
        }

        try {
            $response = $provider === 'google'
                ? Http::asForm()->post('https://oauth2.googleapis.com/revoke', ['token' => $token])
                : Http::withToken($token)->post('https://api.dropboxapi.com/2/auth/token/revoke');

            if (!$response->successful()) {
                report(new \RuntimeException('New cloud grant revocation failed for '.$provider.': '.$response->status()));
            }

            return $response->successful();
        } catch (Throwable $exception) {
            report($exception);
            return false;
        }
    }

    private function freshDropboxAccessToken(CloudConnection $connection, array $settings): ?string
    {
        if (!$connection->token_expires_at || $connection->token_expires_at->gt(now()->addMinute())) {
            return (string) $connection->access_token;
        }

        if (!$connection->refresh_token) {
            return null;
        }

        $response = Http::asForm()->acceptJson()->post($settings['token_url'], [
            'grant_type' => 'refresh_token',
            'refresh_token' => $connection->refresh_token,
            'client_id' => $settings['client_id'],
            'client_secret' => $settings['client_secret'],
        ]);

        if (!$response->successful() || !$response->json('access_token')) {
            report(new \RuntimeException('Dropbox access token refresh for disconnect failed: '.$response->status()));
            return null;
        }

        $token = $response->json();
        $connection->forceFill([
            'access_token' => $token['access_token'],
            'refresh_token' => $token['refresh_token'] ?? $connection->refresh_token,
            'token_expires_at' => isset($token['expires_in']) ? now()->addSeconds((int) $token['expires_in']) : null,
        ])->save();

        return (string) $connection->access_token;
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

        $tokenData = [];
        $issuedAccessToken = null;

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

            $tokenData = $tokenResponse->json();
            $issuedAccessToken = (string) $tokenData['access_token'];

            $profile = $provider === 'google'
                ? Http::withToken($issuedAccessToken)->get('https://www.googleapis.com/oauth2/v2/userinfo')
                : Http::withToken($issuedAccessToken)->post('https://api.dropboxapi.com/2/users/get_current_account');

            if (!$profile->successful()) {
                $revoked = $this->revokeIssuedGrant($provider, $tokenData);
                report(new \RuntimeException('Cloud provider profile lookup failed for '.$provider.': '.$profile->status()));
                $message = 'Connected account details could not be verified.';
                if (!$revoked) {
                    $message .= ' The provider did not confirm revocation; check its account security settings for an MRK authorization.';
                }
                return redirect()->route('cloud.index')->with('error', $message);
            }

            $profileData = $profile->json();
            $accountId = (string) ($provider === 'google' ? ($profileData['id'] ?? '') : ($profileData['account_id'] ?? ''));
            if ($accountId === '') {
                $revoked = $this->revokeIssuedGrant($provider, $tokenData);
                $message = 'The provider did not return a usable account identifier.';
                if (!$revoked) {
                    $message .= ' Check its account security settings for an MRK authorization.';
                }
                return redirect()->route('cloud.index')->with('error', $message);
            }

            // Serialize OAuth callbacks for this user before checking/writing the provider identity.
            // Locking the parent user row also covers the first-connection case where no
            // cloud_connections row exists yet and therefore cannot itself be locked.
            $connected = DB::transaction(function () use ($request, $provider, $accountId, $profileData, $issuedAccessToken, $tokenData) {
                User::query()->whereKey($request->user()->id)->lockForUpdate()->firstOrFail();

                $existingConnection = CloudConnection::query()
                    ->where('user_id', $request->user()->id)
                    ->where('provider', $provider)
                    ->lockForUpdate()
                    ->first();

                if ($existingConnection && $existingConnection->provider_account_id !== $accountId) {
                    return false;
                }

                CloudConnection::updateOrCreate(
                    ['user_id' => $request->user()->id, 'provider' => $provider],
                    [
                        'provider_account_id' => $accountId,
                        'display_name' => $provider === 'google'
                            ? ($profileData['email'] ?? $profileData['name'] ?? null)
                            : ($profileData['name']['display_name'] ?? $profileData['email'] ?? null),
                        'access_token' => $issuedAccessToken,
                        'refresh_token' => $tokenData['refresh_token'] ?? $existingConnection?->refresh_token,
                        'token_expires_at' => isset($tokenData['expires_in']) ? now()->addSeconds((int) $tokenData['expires_in']) : null,
                        'scopes' => isset($tokenData['scope']) ? preg_split('/\\s+/', trim($tokenData['scope'])) : [],
                    ]
                );

                return true;
            });

            if (!$connected) {
                $revoked = $this->revokeIssuedGrant($provider, $tokenData);
                $message = 'A different '.$provider.' account is already connected. Disconnect the existing account first; its saved remote-file records have been preserved.';
                if (!$revoked) {
                    $message .= ' The newly approved grant could not be confirmed as revoked. Check the provider account security settings for an MRK authorization.';
                }
                return redirect()->route('cloud.index')->with('error', $message);
            }

            return redirect()->route('cloud.index')->with('success', ucfirst($provider).' account connected securely.');
        } catch (Throwable $exception) {
            report($exception);
            $message = 'Cloud provider connection failed. Please try again.';
            if ($issuedAccessToken && !$this->revokeIssuedGrant($provider, $tokenData)) {
                $message .= ' The provider did not confirm revocation; check its account security settings for an MRK authorization.';
            }
            return redirect()->route('cloud.index')->with('error', $message);
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

        try {
            $tokenToRevoke = $provider === 'dropbox'
                ? $this->freshDropboxAccessToken($connection, $settings)
                : ($connection->refresh_token ?: $connection->access_token);

            if (!$tokenToRevoke) {
                return redirect()->route('cloud.index')->with('error', 'Could not obtain a valid token to revoke. Your connection remains saved so you can retry.');
            }

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

        $connection->delete();

        return redirect()->route('cloud.index')->with('success', ucfirst($provider).' access revoked and account disconnected. Files in that provider account were not deleted.');
    }
}
