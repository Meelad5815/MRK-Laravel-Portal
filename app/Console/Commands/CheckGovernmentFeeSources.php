<?php

namespace App\Console\Commands;

use App\Models\GovernmentFee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class CheckGovernmentFeeSources extends Command
{
    protected $signature = 'mrk:check-government-fees';
    protected $description = 'Check official government fee source pages and flag changes for human review';

    public function handle(): int
    {
        $checked = 0;
        $changed = 0;
        $failed = 0;

        $urls = GovernmentFee::whereNotNull('official_source_url')
            ->distinct()->pluck('official_source_url');

        foreach ($urls as $url) {
            try {
                $response = Http::timeout(15)->withHeaders([
                    'User-Agent' => 'MRK-Digital-FeeMonitor/1.0',
                ])->get($url);

                $records = GovernmentFee::where('official_source_url', $url)->get();
                if (!$response->successful()) {
                    GovernmentFee::where('official_source_url', $url)->update([
                        'last_checked_at' => now(),
                        'verification_status' => 'source_unavailable',
                    ]);
                    $failed += $records->count();
                    continue;
                }

                $body = Str::limit(strip_tags($response->body()), 200000, '');
                $hash = hash('sha256', $body);
                foreach ($records as $record) {
                    $hasChanged = $record->source_hash && !hash_equals($record->source_hash, $hash);
                    $record->update([
                        'source_hash' => $hash,
                        'last_checked_at' => now(),
                        'verification_status' => $hasChanged ? 'review_required' : (
                            $record->verification_status === 'source_unavailable'
                                ? 'needs_verification'
                                : $record->verification_status
                        ),
                    ]);
                    $checked++;
                    if ($hasChanged) $changed++;
                }
            } catch (\Throwable $exception) {
                report($exception);
                GovernmentFee::where('official_source_url', $url)->update([
                    'last_checked_at' => now(),
                    'verification_status' => 'source_unavailable',
                ]);
                $failed += GovernmentFee::where('official_source_url', $url)->count();
            }
        }

        $this->info("Checked {$checked} records; {$changed} need review; {$failed} source checks failed.");
        return self::SUCCESS;
    }
}
