@extends('layouts.app')
@section('title','Manage Services | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">ADMIN CMS</span><h1>Services</h1><p>Manage the service catalogue shown on the public website.</p></div></section>
<section class="section"><div class="container">
<div class="actions dashboard-actions">
<a class="button primary" href="{{ route('admin.services.create') }}">Add Service</a>
<a class="button secondary" href="{{ route('services') }}">View Services</a>
</div>
<form method="GET" class="dashboard-filters" action="{{ route('admin.services.index') }}">
<label>Search<input name="search" value="{{ request('search') }}" placeholder="Title or category"></label>
<label>Status<select name="status"><option value="">All statuses</option><option value="published" @selected(request('status') === 'published')>Published</option><option value="draft" @selected(request('status') === 'draft')>Draft</option></select></label>
<button class="button primary" type="submit">Filter</button>
<a class="button secondary" href="{{ route('admin.services.index') }}">Reset</a>
</form>
<div class="table-card"><div class="table-head"><h2>Service catalogue</h2><span>{{ $services->count() }} shown</span></div>
@if($services->count())
<div class="table-wrap"><table><thead><tr><th>Service</th><th>Category</th><th>Status</th><th>Featured</th><th>Order</th><th>Actions</th></tr></thead><tbody>
@foreach($services as $service)
<tr>
<td><strong>{{ $service->title }}</strong><br><small>{{ $service->summary }}</small></td>
<td>{{ $service->category }}</td>
<td>{{ ucfirst($service->status) }}</td>
<td>{{ $service->featured ? 'Yes' : 'No' }}</td>
<td>{{ $service->sort_order }}</td>
<td class="action-stack">
<a class="text-link" href="{{ route('admin.services.edit',$service) }}">Edit</a>
<form method="POST" action="{{ route('admin.services.destroy',$service) }}" onsubmit="return confirm('Delete this service?')">@csrf @method('DELETE')<button class="danger-button" type="submit">Delete</button></form>
</td>
</tr>
@endforeach
</tbody></table></div>
@else
<div class="empty">No services found.</div>
@endif
</div>
</div></section>
@endsection
