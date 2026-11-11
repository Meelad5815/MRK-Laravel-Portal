<?php

namespace App\Services;

use App\Models\CloudConnection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class CloudStorageService
{
    public function accessToken(CloudConnection $connection): string
    {
        if ($connection->token_expires_at && $connection->token_expires_at->gt(now()->addMinutes(2))) {
            return (string) $connection->access_token;
        }

        if (!$connection->refresh_token) {
            if ($connection->token_expires_at && $connection->token_expires_at->isPast()) {
                throw new RuntimeException('This cloud connection has expired. Disconnect and reconnect the account.');
            }

            return (string) $connection->access_token;
        }

        $provider = $connection->provider;
        $settings = config('services.cloud_storage.'.$provider, []);
        $response = Http::asForm()->acceptJson()->timeout(30)->post($settings['token_url'], [
            'grant_type' => 'refresh_token',
            'refresh_token' => $connection->refresh_token,
            'client_id' => $settings['client_id'],
            'client_secret' => $settings['client_secret'],
        ]);

        if (!$response->successful() || !$response->json('access_token')) {
            throw new RuntimeException('The cloud provider could not refresh its access token. Reconnect the account.');
        }

        $data = $response->json();
        $connection->forceFill([
            'access_token' => $data['access_token'],
            'refresh_token' => $data['refresh_token'] ?? $connection->refresh_token,
            'token_expires_at' => isset($data['expires_in']) ? now()->addSeconds((int) $data['expires_in']) : null,
            'scopes' => isset($data['scope']) ? preg_split('/\s+/', trim($data['scope'])) : $connection->scopes,
        ])->save();

        return (string) $connection->access_token;
    }

    public function listFiles(CloudConnection $connection): array
    {
        $token = $this->accessToken($connection);

        if ($connection->provider === 'google') {
            $files = [];
            $pageToken = null;
            $seenPageTokens = [];

            do {
                $parameters = [
                    'q' => "trashed = false and mimeType != 'application/vnd.google-apps.folder'",
                    'pageSize' => 100,
                    'orderBy' => 'modifiedTime desc',
                    'fields' => 'nextPageToken,files(id,name,mimeType,size,modifiedTime)',
                ];
                if ($pageToken !== null) {
                    $parameters['pageToken'] = $pageToken;
                }

                $response = Http::withToken($token)->timeout(45)->get('https://www.googleapis.com/drive/v3/files', $parameters);
                $this->ensureSuccess($response, 'Google Drive list');
                $files = array_merge($files, $response->json('files', []));
                $nextPageToken = $response->json('nextPageToken');

                if ($nextPageToken !== null && isset($seenPageTokens[$nextPageToken])) {
                    throw new RuntimeException('Google Drive returned a repeated page token. Sync stopped to avoid an incomplete result.');
                }
                if ($nextPageToken !== null) {
                    $seenPageTokens[$nextPageToken] = true;
                }
                $pageToken = $nextPageToken;
            } while ($pageToken !== null);

            return collect($files)->map(fn ($file) => [
                'id' => (string) $file['id'],
                'name' => (string) ($file['name'] ?? 'Untitled'),
                'mime_type' => $file['mimeType'] ?? null,
                'size' => (int) ($file['size'] ?? 0),
                'modified_at' => $file['modifiedTime'] ?? null,
            ])->all();
        }

        $response = Http::withToken($token)->acceptJson()->timeout(45)->post('https://api.dropboxapi.com/2/files/list_folder', [
            'path' => '',
            'recursive' => true,
            'include_deleted' => false,
            'limit' => 200,
        ]);
        $this->ensureSuccess($response, 'Dropbox list');

        $entries = $response->json('entries', []);
        $cursor = $response->json('cursor');
        $hasMore = (bool) $response->json('has_more');
        $seenCursors = [];

        while ($hasMore) {
            if (!$cursor) {
                throw new RuntimeException('Dropbox indicated more files but did not return a continuation cursor. Sync did not complete.');
            }
            if (isset($seenCursors[$cursor])) {
                throw new RuntimeException('Dropbox returned a repeated continuation cursor. Sync stopped to avoid an incomplete result.');
            }
            $seenCursors[$cursor] = true;

            $next = Http::withToken($token)->acceptJson()->timeout(45)
                ->post('https://api.dropboxapi.com/2/files/list_folder/continue', ['cursor' => $cursor]);
            $this->ensureSuccess($next, 'Dropbox list continuation');
            $entries = array_merge($entries, $next->json('entries', []));
            $cursor = $next->json('cursor');
            $hasMore = (bool) $next->json('has_more');
        }

        $mimeTypes = [
            'jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png',
            'gif' => 'image/gif', 'webp' => 'image/webp', 'pdf' => 'application/pdf',
            'mp4' => 'video/mp4', 'mov' => 'video/quicktime', 'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav', 'zip' => 'application/zip',
        ];

        return collect($entries)->filter(fn ($file) => ($file['.tag'] ?? '') === 'file')->map(function ($file) use ($mimeTypes) {
            $name = (string) ($file['name'] ?? 'Untitled');
            $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));

            return [
                'id' => (string) $file['id'],
                'name' => $name,
                'mime_type' => $mimeTypes[$extension] ?? null,
                'size' => (int) ($file['size'] ?? 0),
                'modified_at' => $file['server_modified'] ?? null,
            ];
        })->values()->all();
    }

    public function upload(CloudConnection $connection, string $name, string $mimeType, string $contents): array
    {
        $token = $this->accessToken($connection);

        if ($connection->provider === 'dropbox') {
            $path = '/MRK/'.$name;
            $response = Http::withToken($token)
                ->withHeaders([
                    'Dropbox-API-Arg' => json_encode(['path' => $path, 'mode' => 'add', 'autorename' => true, 'mute' => false]),
                    'Content-Type' => 'application/octet-stream',
                ])->timeout(60)->withBody($contents, 'application/octet-stream')
                ->post('https://content.dropboxapi.com/2/files/upload');
            $this->ensureSuccess($response, 'Dropbox upload');
            $file = $response->json();
            return [
                'id' => (string) $file['id'],
                'name' => (string) $file['name'],
                'mime_type' => $mimeType,
                'size' => (int) ($file['size'] ?? strlen($contents)),
                'modified_at' => $file['server_modified'] ?? now()->toIso8601String(),
            ];
        }

        $boundary = 'mrk_'.Str::random(24);
        $metadata = json_encode(['name' => $name], JSON_UNESCAPED_SLASHES);
        $body = '--'.$boundary."\r\nContent-Type: application/json; charset=UTF-8\r\n\r\n"
            .$metadata."\r\n--".$boundary."\r\nContent-Type: ".$mimeType."\r\n\r\n"
            .$contents."\r\n--".$boundary."--";
        $response = Http::withToken($token)
            ->withHeaders(['Content-Type' => 'multipart/related; boundary='.$boundary])
            ->timeout(60)->withBody($body, 'multipart/related; boundary='.$boundary)
            ->post('https://www.googleapis.com/upload/drive/v3/files?uploadType=multipart&fields=id,name,mimeType,size,modifiedTime');
        $this->ensureSuccess($response, 'Google Drive upload');
        $file = $response->json();

        return [
            'id' => (string) $file['id'],
            'name' => (string) ($file['name'] ?? $name),
            'mime_type' => $file['mimeType'] ?? $mimeType,
            'size' => (int) ($file['size'] ?? strlen($contents)),
            'modified_at' => $file['modifiedTime'] ?? now()->toIso8601String(),
        ];
    }

    public function downloadToTemporaryFile(CloudConnection $connection, string $fileId, ?string $mimeType = null): array
    {
        $token = $this->accessToken($connection);
        $path = tempnam(sys_get_temp_dir(), 'mrk-cloud-');
        if ($path === false) {
            throw new RuntimeException('The server could not allocate temporary storage for this download.');
        }

        try {
            $request = Http::withToken($token)->timeout(120)->withOptions([
                'sink' => $path,
                'progress' => static function ($downloadTotal, $downloaded): void {
                    if ($downloaded > 50 * 1024 * 1024) {
                        throw new RuntimeException('Remote downloads are limited to 50 MB on this hosting plan.');
                    }
                },
            ]);

            if ($connection->provider === 'google') {
                if (str_starts_with((string) $mimeType, 'application/vnd.google-apps.')) {
                    $exportTypes = [
                        'application/vnd.google-apps.document' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.google-apps.spreadsheet' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                        'application/vnd.google-apps.presentation' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
                        'application/vnd.google-apps.drawing' => 'image/png',
                    ];
                    $exportType = $exportTypes[$mimeType] ?? null;
                    if (!$exportType) {
                        throw new RuntimeException('This Google Workspace file type cannot be exported by MRK yet.');
                    }
                    $response = $request->get(
                        'https://www.googleapis.com/drive/v3/files/'.rawurlencode($fileId).'/export',
                        ['mimeType' => $exportType]
                    );
                } else {
                    $response = $request->get(
                        'https://www.googleapis.com/drive/v3/files/'.rawurlencode($fileId),
                        ['alt' => 'media']
                    );
                }
            } else {
                $response = $request->withHeaders([
                    'Dropbox-API-Arg' => json_encode(['path' => $fileId]),
                ])->get('https://content.dropboxapi.com/2/files/download');
            }

            $this->ensureSuccess($response, 'Cloud file download');
            clearstatcache(true, $path);
            if (filesize($path) > 50 * 1024 * 1024) {
                throw new RuntimeException('Remote downloads are limited to 50 MB on this hosting plan.');
            }

            return [
                'path' => $path,
                'content_type' => $response->header('Content-Type') ?: ($mimeType ?: 'application/octet-stream'),
                'size' => (int) filesize($path),
            ];
        } catch (Throwable $exception) {
            if (is_file($path)) {
                @unlink($path);
            }
            throw $exception;
        }
    }

    public function delete(CloudConnection $connection, string $fileId): void
    {
        $token = $this->accessToken($connection);
        $response = $connection->provider === 'google'
            ? Http::withToken($token)->delete('https://www.googleapis.com/drive/v3/files/'.rawurlencode($fileId))
            : Http::withToken($token)->acceptJson()->post('https://api.dropboxapi.com/2/files/delete_v2', ['path' => $fileId]);

        if (!$response->successful() && $response->status() !== 204) {
            $this->ensureSuccess($response, 'Cloud file delete');
        }
    }

    private function ensureSuccess(Response $response, string $operation): void
    {
        if (!$response->successful()) {
            report(new RuntimeException($operation.' failed with HTTP '.$response->status()));
            throw new RuntimeException($operation.' failed. Check the cloud connection and try again.');
        }
    }
}
