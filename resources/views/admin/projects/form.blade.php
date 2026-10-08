<form method="POST" action="{{ $project->exists ? route('admin.projects.update',$project) : route('admin.projects.store') }}" class="form-card">
@csrf
@if($project->exists) @method('PUT') @endif
<label>Project title<input name="title" value="{{ old('title',$project->title) }}" required maxlength="160"></label>
<label>Short summary<textarea name="summary" rows="3" required maxlength="300">{{ old('summary',$project->summary) }}</textarea></label>
<label>Full description<textarea name="description" rows="8" required>{{ old('description',$project->description) }}</textarea></label>
<label>Category<input name="category" value="{{ old('category',$project->category) }}" placeholder="Web Development / Automation" required></label>
<label>Technologies <small>Comma-separated</small><input name="technologies" value="{{ old('technologies',$project->technologies) }}" placeholder="Laravel, PHP, MySQL"></label>
<label>Project URL <small>Optional public URL</small><input type="url" name="project_url" value="{{ old('project_url',$project->project_url) }}" placeholder="https://example.com"></label>
<label>Customer<select name="customer_id"><option value="">No linked customer</option>@foreach($customers as $customer)<option value="{{ $customer->id }}" @selected(old('customer_id',$project->customer_id)==$customer->id)>{{ $customer->name }}</option>@endforeach</select></label>
<label>Start date<input type="date" name="start_date" value="{{ old('start_date',$project->start_date?->format('Y-m-d')) }}"></label>
<label>Due date<input type="date" name="due_date" value="{{ old('due_date',$project->due_date?->format('Y-m-d')) }}"></label>
<label>Progress %<input type="number" name="progress" min="0" max="100" value="{{ old('progress',$project->progress ?? 0) }}"></label>
<label>Completed date<input type="date" name="completed_at" value="{{ old('completed_at',$project->completed_at?->format('Y-m-d')) }}"></label>
<label>Status<select name="status"><option value="draft" @selected(old('status',$project->status)==='draft')>Draft</option><option value="published" @selected(old('status',$project->status)==='published')>Published</option></select></label>
<label class="checkbox-label"><input type="checkbox" name="featured" value="1" @checked(old('featured',$project->featured))> Feature this project</label>
<div class="actions"><button class="button primary" type="submit">{{ $project->exists ? 'Save Changes' : 'Create Project' }}</button><a class="button secondary" href="{{ route('admin.projects.index') }}">Cancel</a></div>
</form>
