<?php

namespace App\Http\Controllers;

use App\Models\CloudConnection;
use App\Models\CloudFile;
use App\Models\CloudFolder;
use App\Models\CloudRemoteFile;
use App\Services\CloudStorageService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;
use Throwable;

class PersonalCloudController extends Controller
{
    private const CATEGORIES = [
        'documents' => 'Documents',
        'images' => 'Images',
        'videos' => 'Videos',
        'audio' => 'Audio',
        'archives' => 'Archives',
        'other' => 'Other',
    ];

    public function index(Request $request): View
    {
        $userId = $request->user()->id;
        $files = CloudFile::query()
            ->with('folder')
            ->where('user_id', $userId)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where('original_name', 'like', '%'.$term.'%');
            })
            ->when($request->filled('folder'), fn ($query) => $query->where('cloud_folder_id', (int) $request->query('folder')))
            ->when(in_array($request->query('category'), array_keys(self::CATEGORIES), true), fn ($query) => $query->where('category', $request->query('category')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $remoteFiles = CloudRemoteFile::query()
            ->where('user_id', $userId)
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.trim((string) $request->query('q')).'%'))
            ->when(in_array($request->query('category'), array_keys(self::CATEGORIES), true), fn ($query) => $query->where('category', $request->query('category')))
            ->latest('provider_modified_at')
            ->paginate(20, ['*'], 'remote_page')
            ->withQueryString();

        $stats = [
            'files' => CloudFile::where('user_id', $userId)->count(),
            'bytes' => (int) CloudFile::where('user_id', $userId)->sum('size'),
            'remote_files' => CloudRemoteFile::where('user_id', $userId)->count(),
            'remote_bytes' => (int) CloudRemoteFile::where('user_id', $userId)->sum('size'),
        ];
        $folders = CloudFolder::query()->where('user_id', $userId)->orderBy('name')->get();

        return view('cloud.index', [
            'files' => $files,
            'remoteFiles' => $remoteFiles,
            'stats' => $stats,
            'folders' => $folders,
            'connections' => CloudConnection::query()->where('user_id', $userId)->get()->keyBy('provider'),
            'categories' => self::CATEGORIES,
        ]);
    }

    public function storeFolder(Request $request): RedirectResponse
    {
        $name = $request->input('name');
        if (is_string($name)) {
            $request->merge(['name' => trim($name)]);
        }

        $validated = $request->validate([
            'name' => [
                'required', 'string', 'min:1', 'max:60',
                Rule::unique('cloud_folders', 'name')->where('user_id', $request->user()->id),
            ],
        ]);

        CloudFolder::create([
            'user_id' => $request->user()->id,
            'name' => trim($validated['name']),
        ]);

        return redirect()->route('cloud.index')->with('success', 'Folder created.');
    }

    public function store(Request $request, CloudStorageService $storage): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:5120'],
            'destination' => ['nullable', Rule::in(['local', 'google', 'dropbox'])],
            'folder_id' => [
                'nullable',
                'integer',
                Rule::exists('cloud_folders', 'id')->where(fn ($query) => $query->where('user_id', $request->user()->id)),
            ],
            'category' => ['nullable', Rule::in(array_keys(self::CATEGORIES))],
        ], [
            'file.max' => 'Each file must be 5 MB or smaller on this free-hosting setup.',
            'folder_id.exists' => 'Choose a folder belonging to your account.',
        ]);

        $upload = $validated['file'];
        $destination = $validated['destination'] ?? 'local';
        $category = $validated['category'] ?? 'other';

        if ($destination === 'local') {
            $extension = strtolower($upload->getClientOriginalExtension() ?: '');
            $filename = (string) Str::uuid().($extension !== '' ? '.'.$extension : '');
            $directory = 'mrk-cloud/'.$request->user()->id;
            $path = $upload->storeAs($directory, $filename, 'local');

            if (!$path) {
                return back()->withErrors(['file' => 'The file could not be saved. Check hosting storage permissions.']);
            }

            CloudFile::create([
                'user_id' => $request->user()->id,
                'cloud_folder_id' => $validated['folder_id'] ?? null,
                'category' => $category,
                'original_name' => mb_substr($upload->getClientOriginalName(), 0, 255),
                'stored_path' => $path,
                'mime_type' => $upload->getMimeType(),
                'extension' => $extension ?: null,
                'size' => $upload->getSize(),
            ]);

            return redirect()->route('cloud.index')->with('success', 'File uploaded to your private MRK storage.');
        }

        $connection = CloudConnection::query()
            ->where('user_id', $request->user()->id)
            ->where('provider', $destination)
            ->first();

        if (!$connection) {
            return back()->withErrors(['destination' => 'Connect this cloud account before uploading.']);
        }

        try {
            $originalName = basename(str_replace('\\', '/', $upload->getClientOriginalName()));
            $originalName = mb_substr(preg_replace('/[\\x00-\\x1F\\x7F]/u', '', $originalName) ?: 'upload', 0, 180);
            $remote = $storage->upload(
                $connection,
                $originalName,
                $upload->getMimeType() ?: 'application/octet-stream',
                file_get_contents($upload->getRealPath())
            );

            CloudRemoteFile::updateOrCreate(
                ['cloud_connection_id' => $connection->id, 'provider_file_id' => $remote['id']],
                [
                    'user_id' => $request->user()->id,
                    'provider' => $destination,
                    'name' => mb_substr($remote['name'], 0, 255),
                    'mime_type' => $remote['mime_type'],
                    'size' => $remote['size'],
                    'category' => $category,
                    'provider_modified_at' => Carbon::parse($remote['modified_at']),
                ]
            );

            return redirect()->route('cloud.index')->with('success', 'File uploaded to '.ucfirst($destination).'.');
        } catch (Throwable $exception) {
            report($exception);
            return back()->withErrors(['file' => 'Remote upload failed. Check your cloud connection and try again.']);
        }
    }

    public function syncRemoteFiles(Request $request, CloudStorageService $storage): RedirectResponse
    {
        $connections = CloudConnection::query()->where('user_id', $request->user()->id)->get();
        if ($connections->isEmpty()) {
            return redirect()->route('cloud.index')->with('error', 'Connect Google Drive or Dropbox first.');
        }

        $synced = 0;
        foreach ($connections as $connection) {
            try {
                foreach ($storage->listFiles($connection) as $file) {
                    $mime = strtolower((string) ($file['mime_type'] ?? ''));
                    $category = str_starts_with($mime, 'image/') ? 'images'
                        : (str_starts_with($mime, 'video/') ? 'videos'
                        : (str_starts_with($mime, 'audio/') ? 'audio'
                        : (str_contains($mime, 'zip') || str_contains($mime, 'compressed') ? 'archives'
                        : ($mime !== '' ? 'documents' : 'other'))));

                    CloudRemoteFile::updateOrCreate(
                        ['cloud_connection_id' => $connection->id, 'provider_file_id' => $file['id']],
                        [
                            'user_id' => $request->user()->id,
                            'provider' => $connection->provider,
                            'name' => mb_substr($file['name'], 0, 255),
                            'mime_type' => $file['mime_type'],
                            'size' => $file['size'],
                            'category' => $category,
                            'provider_modified_at' => !empty($file['modified_at']) ? Carbon::parse($file['modified_at']) : null,
                        ]
                    );
                    $synced++;
                }
            } catch (Throwable $exception) {
                report($exception);
                return redirect()->route('cloud.index')->with('error', 'Could not sync '.ucfirst($connection->provider).'. Reconnect it if access has expired.');
            }
        }

        return redirect()->route('cloud.index')->with('success', $synced.' remote file record(s) synchronized.');
    }

    public function previewRemote(Request $request, CloudRemoteFile $remoteFile, CloudStorageService $storage)
    {
        abort_unless($remoteFile->user_id === $request->user()->id, 404);
        abort_unless($remoteFile->connection && $remoteFile->connection->user_id === $request->user()->id, 404);
        abort_unless(in_array(strtolower((string) $remoteFile->mime_type), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true), 415);
        abort_if($remoteFile->size > 10 * 1024 * 1024, 413);

        try {
            $response = $storage->download($remoteFile->connection, $remoteFile->provider_file_id, $remoteFile->mime_type);
            return response($response->body(), 200, [
                'Content-Type' => $remoteFile->mime_type,
                'Content-Disposition' => 'inline; filename="'.addcslashes(basename($remoteFile->name), '"\\').'"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control' => 'private, no-store, max-age=0',
            ]);
        } catch (Throwable $exception) {
            report($exception);
            abort(502, 'The remote image could not be loaded.');
        }
    }

    public function downloadRemote(Request $request, CloudRemoteFile $remoteFile, CloudStorageService $storage): StreamedResponse
    {
        abort_unless($remoteFile->user_id === $request->user()->id, 404);
        abort_unless($remoteFile->connection && $remoteFile->connection->user_id === $request->user()->id, 404);
        abort_if($remoteFile->size > 25 * 1024 * 1024, 413);

        try {
            $response = $storage->download($remoteFile->connection, $remoteFile->provider_file_id, $remoteFile->mime_type);
            $body = $response->body();
            $contentType = $response->header('Content-Type') ?: ($remoteFile->mime_type ?: 'application/octet-stream');
            $downloadName = $remoteFile->name;
            $googleExtensions = [
                'application/vnd.google-apps.document' => '.docx',
                'application/vnd.google-apps.spreadsheet' => '.xlsx',
                'application/vnd.google-apps.presentation' => '.pptx',
                'application/vnd.google-apps.drawing' => '.png',
            ];
            if (isset($googleExtensions[$remoteFile->mime_type]) && !str_contains($downloadName, '.')) {
                $downloadName .= $googleExtensions[$remoteFile->mime_type];
            }

            return response()->streamDownload(static function () use ($body) {
                echo $body;
            }, $downloadName, ['Content-Type' => $contentType]);
        } catch (Throwable $exception) {
            report($exception);
            abort(502, 'The remote file could not be downloaded.');
        }
    }

    public function destroyRemote(Request $request, CloudRemoteFile $remoteFile, CloudStorageService $storage): RedirectResponse
    {
        abort_unless($remoteFile->user_id === $request->user()->id, 404);
        abort_unless($remoteFile->connection && $remoteFile->connection->user_id === $request->user()->id, 404);

        try {
            $storage->delete($remoteFile->connection, $remoteFile->provider_file_id);
            $remoteFile->delete();
            return redirect()->route('cloud.index')->with('success', 'File deleted from '.ucfirst($remoteFile->provider).'.');
        } catch (Throwable $exception) {
            report($exception);
            return redirect()->route('cloud.index')->with('error', 'Remote deletion failed. The file record was kept.');
        }
    }

    public function preview(Request $request, CloudFile $cloudFile)
    {
        abort_unless($cloudFile->user_id === $request->user()->id, 404);
        abort_unless(Storage::disk('local')->exists($cloudFile->stored_path), 404);

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        abort_unless(in_array(strtolower((string) $cloudFile->mime_type), $allowedTypes, true), 415);

        $stream = Storage::disk('local')->readStream($cloudFile->stored_path);
        abort_unless(is_resource($stream), 404);

        return response()->stream(function () use ($stream) {
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type' => strtolower($cloudFile->mime_type),
            'Content-Disposition' => 'inline; filename="'.addcslashes(basename($cloudFile->original_name), '"\\').'"',
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store, max-age=0',
        ]);
    }

    public function download(Request $request, CloudFile $cloudFile): StreamedResponse
    {
        abort_unless($cloudFile->user_id === $request->user()->id, 404);
        abort_unless(Storage::disk('local')->exists($cloudFile->stored_path), 404);

        return Storage::disk('local')->download($cloudFile->stored_path, $cloudFile->original_name);
    }

    public function destroy(Request $request, CloudFile $cloudFile): RedirectResponse
    {
        abort_unless($cloudFile->user_id === $request->user()->id, 404);

        Storage::disk('local')->delete($cloudFile->stored_path);
        $cloudFile->delete();

        return redirect()->route('cloud.index')->with('success', 'File deleted from your cloud.');
    }
}
