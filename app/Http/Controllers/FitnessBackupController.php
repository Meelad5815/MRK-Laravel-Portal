<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

class FitnessBackupController extends Controller
{
    /**
     * Read-only feed for the Google Apps Script daily backup.
     * Protected by a dedicated bearer token; never expose this route in public links.
     */
    public function feed(Request $request): JsonResponse
    {
        $configuredToken = (string) config('services.fitness_sheets.token');
        $providedToken = (string) $request->bearerToken();

        abort_if($configuredToken === '' || strlen($configuredToken) < 32, 503, 'Fitness backup is not configured.');
        abort_unless(hash_equals($configuredToken, $providedToken), 401);

        $userId = (int) config('services.fitness_sheets.user_id');
        abort_if($userId < 1, 503, 'Fitness backup owner is not configured.');

        $entries = DB::table('fitness_entries')
            ->where('user_id', $userId)
            ->orderBy('entry_date')
            ->orderBy('id')
            ->get(['id', 'type', 'entry_date', 'title', 'distance_km', 'duration_minutes', 'weight_kg', 'notes', 'created_at', 'updated_at']);

        return response()->json([
            'schema_version' => 1,
            'generated_at' => now()->toIso8601String(),
            'count' => $entries->count(),
            'entries' => $entries,
        ])->header('Cache-Control', 'no-store, private');
    }

    /**
     * Ask the Apps Script web app to run a backup immediately.
     */
    public function syncNow(Request $request): RedirectResponse
    {
        $url = (string) config('services.fitness_sheets.web_app_url');
        $token = (string) config('services.fitness_sheets.token');

        if ($url === '' || $token === '' || strlen($token) < 32) {
            return back()->withErrors(['backup' => 'Google Sheets backup is not configured yet. Complete the one-time setup first.']);
        }

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(25)
                ->post($url, ['token' => $token, 'action' => 'sync_now']);

            $payload = $response->json();

            if (! $response->successful() || ! is_array($payload) || ($payload['ok'] ?? false) !== true) {
                report(new \RuntimeException('Fitness Sheets sync failed with HTTP '.$response->status()));
                return back()->withErrors(['backup' => 'Google Sheets نے Sync مکمل نہیں کیا۔ Apps Script deployment اور hosting outbound access چیک کریں۔']);
            }

            return back()->with('success', 'Google Sheets backup sync completed. '.((int) ($payload['count'] ?? 0)).' records synced.');
        } catch (\Throwable $exception) {
            report($exception);
            return back()->withErrors(['backup' => 'Google Sheets سے رابطہ نہیں ہو سکا۔ آپ کے اصل Fitness records MySQL میں محفوظ ہیں؛ دوبارہ کوشش کریں۔']);
        }
    }
}
