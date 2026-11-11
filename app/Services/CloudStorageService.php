<?php

namespace App\Services;

use App\Models\CloudConnection;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

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
            $response = Http::withToken($token)->timeout(45)->get('https://www.googleapis.com/drive/v3/files', [
                'q' => 'trashed = false',
                'pageSize' => 100,
                'orderBy' => 'modifiedTime desc',
                'fields' => 'files(id,name,mimeType,size,modifiedTime)',
            ]);
            $this->ensureSuccess($response, 'Google Drive list');
            return collect($response->json('files', []))->map(fn ($file) => [
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
        $pages = 1;
        while ($hasMore && $cursor && $pages < 5) {
            $next = Http::withToken($token)->acceptJson()->timeout(45)
                ->post('https://api.dropboxapi.com/2/files/list_folder/continue', ['cursor' => $cursor]);
            $this->ensureSuccess($next, 'Dropbox list continuation');
            $entries = array_merge($entries, $next->json('entries', []));
            $cursor = $next->json('cursor');
            $hasMore = (bool) $next->json('has_more');
            $pages++;
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

    public function download(CloudConnection $connection, string $fileId): Response
    {
        $token = $this->accessToken($connection);
        $response = $connection->provider === 'google'
            ? Http::withToken($token)->timeout(60)->get('https://www.googleapis.com/drive/v3/files/'.rawurlencode($fileId), ['alt' => 'media'])
            : Http::withToken($token)->withHeaders(['Dropbox-API-Arg' => json_encode(['path' => $fileId])])
                ->timeout(60)->get('https://content.dropboxapi.com/2/files/download');

        $this->ensureSuccess($response, 'Cloud file download');
        return $response;
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
