@extends('layouts.app')
@section('title', 'MRK Personal Cloud | MRK Digital')
@section('description', 'Private file storage area for your MRK Digital account.')
@section('robots', 'noindex,nofollow')
@section('content')
<section class="page-hero">
    <div class="container narrow">
        <span class="eyebrow">MRK DIGITAL · PRIVATE STORAGE</span>
        <h1>MRK Personal Cloud</h1>
        <p>Store and manage your personal files in your own account. Files are private and only your signed-in account can access them.</p>
    </div>
</section>
<section class="section">
    <div class="container">
        <div class="stats" style="margin-bottom:1.5rem">
            <div><span>Your files</span><strong>{{ number_format($stats['files']) }}</strong></div>
            <div><span>Stored data</span><strong>{{ number_format($stats['bytes'] / 1048576, 2) }} MB</strong></div>
            <div><span>Per-file limit</span><strong>5 MB</strong></div>
        </div>
        <div class="table-card" style="padding:1.25rem;margin-bottom:1.5rem">
            <h2>Upload a file</h2>
            <p>Choose a document, image, PDF, archive or another file up to 5 MB.</p>
            <form method="POST" action="{{ route('cloud.store') }}" enctype="multipart/form-data" class="dashboard-filters">
                @csrf
                <label>Choose file<input type="file" name="file" required></label>
                <button class="button primary" type="submit">Upload to my cloud</button>
            </form>
        </div>
        <div class="table-card">
            <div class="table-head"><h2>My files</h2><span>{{ $files->total() }} total</span></div>
            <form method="GET" action="{{ route('cloud.index') }}" class="dashboard-filters">
                <label>Search files<input type="search" name="q" value="{{ request('q') }}" placeholder="Search by filename"></label>
                <button class="button primary" type="submit">Search</button>
                <a class="button secondary" href="{{ route('cloud.index') }}">Clear</a>
            </form>
            @if($files->count())
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>File name</th><th>Type</th><th>Size</th><th>Uploaded</th><th>Actions</th></tr></thead>
                        <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td><strong>{{ $file->original_name }}</strong></td>
                                <td>{{ strtoupper($file->extension ?: 'FILE') }}</td>
                                <td>{{ $file->size_for_humans }}</td>
                                <td>{{ $file->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="action-stack">
                                        <a class="button secondary" href="{{ route('cloud.download', $file) }}">Download</a>
                                        <form method="POST" action="{{ route('cloud.destroy', $file) }}" onsubmit="return confirm('Permanently delete this file?')">
                                            @csrf @method('DELETE')
                                            <button class="danger-button" type="submit">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="padding:1rem">{{ $files->links() }}</div>
            @else
                <div class="empty">No files found. Upload your first file to get started.</div>
            @endif
        </div>
        <p class="muted" style="margin-top:1rem">Free-hosting note: this starter version stores files on the website host. Keep a separate backup of important documents. Do not upload highly sensitive identity or financial documents.</p>
    </div>
</section>
@endsection
