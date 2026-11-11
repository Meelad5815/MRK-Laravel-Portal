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
            <h2>Create a folder</h2>
            <p>Organize your cloud files into folders such as Study, Work, Projects or Personal.</p>
            <form method="POST" action="{{ route('cloud.folders.store') }}" class="dashboard-filters">
                @csrf
                <label>Folder name<input type="text" name="name" maxlength="60" required placeholder="e.g. Study Notes"></label>
                <button class="button secondary" type="submit">Create folder</button>
            </form>
            @error('name')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="table-card" style="padding:1.25rem;margin-bottom:1.5rem">
            <h2>Upload a file</h2>
            <p>Choose a document, image, PDF, archive or another file up to 5 MB.</p>
            <form method="POST" action="{{ route('cloud.store') }}" enctype="multipart/form-data" class="dashboard-filters">
                @csrf
                <label>Choose file<input type="file" name="file" required></label>
                <label>Folder
                    <select name="folder_id">
                        <option value="">No folder</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}" @selected(old('folder_id') == $folder->id)>{{ $folder->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Category
                    <select name="category">
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected(old('category', 'other') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="button primary" type="submit">Upload to my cloud</button>
            </form>
            @error('file')<p class="form-error">{{ $message }}</p>@enderror
            @error('folder_id')<p class="form-error">{{ $message }}</p>@enderror
            @error('category')<p class="form-error">{{ $message }}</p>@enderror
        </div>

        <div class="table-card">
            <div class="table-head"><h2>My files</h2><span>{{ $files->total() }} total</span></div>
            <form method="GET" action="{{ route('cloud.index') }}" class="dashboard-filters">
                <label>Search files<input type="search" name="q" value="{{ request('q') }}" placeholder="Search by filename"></label>
                <label>Folder
                    <select name="folder">
                        <option value="">All folders</option>
                        @foreach($folders as $folder)
                            <option value="{{ $folder->id }}" @selected((string) request('folder') === (string) $folder->id)>{{ $folder->name }}</option>
                        @endforeach
                    </select>
                </label>
                <label>Category
                    <select name="category">
                        <option value="">All categories</option>
                        @foreach($categories as $key => $label)
                            <option value="{{ $key }}" @selected(request('category') === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </label>
                <button class="button primary" type="submit">Filter</button>
                <a class="button secondary" href="{{ route('cloud.index') }}">Clear</a>
            </form>
            @if($files->count())
                <div class="table-wrap">
                    <table>
                        <thead><tr><th>File name</th><th>Folder</th><th>Category</th><th>Type</th><th>Size</th><th>Uploaded</th><th>Actions</th></tr></thead>
                        <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td><strong>{{ $file->original_name }}</strong></td>
                                <td>{{ $file->folder?->name ?? '—' }}</td>
                                <td>{{ $categories[$file->category] ?? 'Other' }}</td>
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
        <p class="muted" style="margin-top:1rem">Backup note: this version stores files on the website host. Folder organization is not a backup. Keep a separate copy of important files. A downloadable ZIP backup will be added only after checking ZIP support and available hosting storage.</p>
    </div>
</section>
@endsection
