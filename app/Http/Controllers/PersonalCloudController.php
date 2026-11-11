<?php

namespace App\Http\Controllers;

use App\Models\CloudFile;
use App\Models\CloudFolder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

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
            ->when($request->filled('folder'), function ($query) use ($request) {
                $query->where('cloud_folder_id', (int) $request->query('folder'));
            })
            ->when(in_array($request->query('category'), array_keys(self::CATEGORIES), true), function ($query) use ($request) {
                $query->where('category', $request->query('category'));
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'files' => CloudFile::where('user_id', $userId)->count(),
            'bytes' => (int) CloudFile::where('user_id', $userId)->sum('size'),
        ];
        $folders = CloudFolder::query()->where('user_id', $userId)->orderBy('name')->get();

        return view('cloud.index', [
            'files' => $files,
            'stats' => $stats,
            'folders' => $folders,
            'categories' => self::CATEGORIES,
        ]);
    }

    public function storeFolder(Request $request): RedirectResponse
    {
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

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:5120'],
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
            'category' => $validated['category'] ?? 'other',
            'original_name' => mb_substr($upload->getClientOriginalName(), 0, 255),
            'stored_path' => $path,
            'mime_type' => $upload->getMimeType(),
            'extension' => $extension ?: null,
            'size' => $upload->getSize(),
        ]);

        return redirect()->route('cloud.index')->with('success', 'File uploaded to your private cloud.');
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
