<?php

namespace App\Http\Controllers;

use App\Models\CloudFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class PersonalCloudController extends Controller
{
    public function index(Request $request): View
    {
        $files = CloudFile::query()
            ->where('user_id', $request->user()->id)
            ->when($request->filled('q'), function ($query) use ($request) {
                $term = trim((string) $request->query('q'));
                $query->where('original_name', 'like', '%'.$term.'%');
            })
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'files' => CloudFile::where('user_id', $request->user()->id)->count(),
            'bytes' => (int) CloudFile::where('user_id', $request->user()->id)->sum('size'),
        ];

        return view('cloud.index', compact('files', 'stats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ], [
            'file.max' => 'Each file must be 5 MB or smaller on this free-hosting setup.',
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
