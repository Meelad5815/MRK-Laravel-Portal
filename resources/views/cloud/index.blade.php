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
                                        @if(in_array(strtolower((string) $file->mime_type), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'], true))
                                            <a class="button secondary" href="{{ route('cloud.preview', $file) }}" target="_blank" rel="noopener">Preview</a>
                                            <button class="button secondary js-image-edit" type="button" data-image-url="{{ route('cloud.preview', $file) }}" data-image-name="{{ $file->original_name }}">Edit / Crop</button>
                                        @endif
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
        <dialog id="mrk-image-editor" style="width:min(96vw,900px);max-height:90vh;border:1px solid #d1d5db;border-radius:14px;padding:1rem">
            <div style="display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap">
                <h2 style="margin:0">Image Editor · Crop & Rotate</h2>
                <button type="button" class="button secondary" id="mrk-editor-close">Close</button>
            </div>
            <p class="muted">Drag on the image to select a crop area. Rotate if needed, then download the edited copy.</p>
            <div style="overflow:auto;max-height:55vh;margin:1rem 0;text-align:center;background:#f3f4f6">
                <canvas id="mrk-editor-canvas" style="max-width:100%;height:auto;touch-action:none;cursor:crosshair"></canvas>
            </div>
            <div style="display:flex;gap:.5rem;flex-wrap:wrap">
                <button type="button" class="button secondary" id="mrk-editor-rotate">Rotate 90°</button>
                <button type="button" class="button secondary" id="mrk-editor-reset">Reset crop</button>
                <button type="button" class="button primary" id="mrk-editor-save">Download edited image</button>
            </div>
            <p class="muted" id="mrk-editor-status" aria-live="polite"></p>
        </dialog>
        <script>
        (() => {
            const dialog = document.getElementById('mrk-image-editor');
            const canvas = document.getElementById('mrk-editor-canvas');
            const ctx = canvas.getContext('2d');
            const status = document.getElementById('mrk-editor-status');
            let source = new Image(), originalName = 'edited-image', rotation = 0, selection = null, dragStart = null;
            const draw = () => {
                if (!source.complete || !source.naturalWidth) return;
                const swapped = Math.abs(rotation % 180) === 90;
                canvas.width = swapped ? source.naturalHeight : source.naturalWidth;
                canvas.height = swapped ? source.naturalWidth : source.naturalHeight;
                ctx.save(); ctx.translate(canvas.width / 2, canvas.height / 2); ctx.rotate(rotation * Math.PI / 180);
                ctx.drawImage(source, -source.naturalWidth / 2, -source.naturalHeight / 2); ctx.restore();
                status.textContent = 'Drag across the image to choose the crop area.';
            };
            document.querySelectorAll('.js-image-edit').forEach(button => button.addEventListener('click', () => {
                originalName = button.dataset.imageName || 'edited-image'; rotation = 0; selection = null;
                source = new Image(); source.onload = () => { draw(); dialog.showModal(); };
                source.onerror = () => { status.textContent = 'Could not load this image.'; };
                source.src = button.dataset.imageUrl;
            }));
            document.getElementById('mrk-editor-close').addEventListener('click', () => dialog.close());
            document.getElementById('mrk-editor-rotate').addEventListener('click', () => { rotation = (rotation + 90) % 360; selection = null; draw(); });
            document.getElementById('mrk-editor-reset').addEventListener('click', () => { selection = null; draw(); });
            const point = event => { const rect = canvas.getBoundingClientRect(); return {x:(event.clientX-rect.left)*canvas.width/rect.width,y:(event.clientY-rect.top)*canvas.height/rect.height}; };
            const drawOverlay = () => { if (!selection) return; ctx.save(); ctx.strokeStyle='#0d9488'; ctx.lineWidth=Math.max(2,canvas.width/500); ctx.setLineDash([8,5]); ctx.strokeRect(selection.x,selection.y,selection.w,selection.h); ctx.restore(); };
            canvas.addEventListener('pointerdown', event => { dragStart = point(event); selection = {x:dragStart.x,y:dragStart.y,w:0,h:0}; canvas.setPointerCapture(event.pointerId); });
            canvas.addEventListener('pointermove', event => { if (!dragStart) return; const p=point(event); selection={x:Math.min(p.x,dragStart.x),y:Math.min(p.y,dragStart.y),w:Math.abs(p.x-dragStart.x),h:Math.abs(p.y-dragStart.y)}; draw(); drawOverlay(); });
            canvas.addEventListener('pointerup', () => { dragStart=null; });
            document.getElementById('mrk-editor-save').addEventListener('click', () => {
                const crop = selection && selection.w > 4 && selection.h > 4 ? selection : {x:0,y:0,w:canvas.width,h:canvas.height};
                const output=document.createElement('canvas'); output.width=Math.round(crop.w); output.height=Math.round(crop.h);
                output.getContext('2d').drawImage(canvas,crop.x,crop.y,crop.w,crop.h,0,0,output.width,output.height);
                const link=document.createElement('a'); link.download=originalName.replace(/\.[^.]+$/,'')+'-edited.png'; link.href=output.toDataURL('image/png'); link.click();
                status.textContent='Edited copy downloaded. Upload it again to keep it in MRK Cloud.';
            });
        })();
        </script>
        <p class="muted" style="margin-top:1rem">Backup note: this version stores files on the website host. Folder organization is not a backup. Keep a separate copy of important files. A downloadable ZIP backup will be added only after checking ZIP support and available hosting storage.</p>
    </div>
</section>
@endsection
