@extends('layouts.app')
@section('title','Dashboard | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">MRK PORTAL</span><h1>Dashboard</h1><p>Welcome, {{ auth()->user()->name }}. Manage project enquiries from one place.</p></div></section>
<section class="section"><div class="container">
<div class="actions dashboard-actions"><a class="button primary" href="{{ route('admin.projects.index') }}">Manage Projects</a><a class="button secondary" href="{{ route('projects.index') }}">View Portfolio</a></div>
<div class="stats"><div><span>Total enquiries</span><strong>{{ $stats['total'] }}</strong></div><div><span>New</span><strong>{{ $stats['new'] }}</strong></div><div><span>In progress</span><strong>{{ $stats['progress'] }}</strong></div><div><span>Completed</span><strong>{{ $stats['completed'] }}</strong></div></div>
<form method="GET" action="{{ route('dashboard') }}" class="dashboard-filters">
<label>Search<input name="search" value="{{ request('search') }}" placeholder="Name, email, phone or project"></label>
<label>Status<select name="status"><option value="">All statuses</option><option value="new" @selected(request('status') === 'new')>New</option><option value="in_progress" @selected(request('status') === 'in_progress')>In progress</option><option value="completed" @selected(request('status') === 'completed')>Completed</option></select></label>
<button class="button primary" type="submit">Filter</button><a class="button secondary" href="{{ route('dashboard') }}">Reset</a>
</form>
<div class="table-card"><div class="table-head"><h2>Project enquiries</h2><span>{{ $leads->count() }} shown</span></div>
@if($leads->count())<div class="table-wrap"><table><thead><tr><th>Name</th><th>Project</th><th>Contact</th><th>Requirement</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>
@foreach($leads as $lead)<tr>
<td><strong>{{ $lead->name }}</strong></td><td>{{ $lead->project_type }}</td>
<td><div class="contact-actions">@if($lead->email)<a href="mailto:{{ $lead->email }}">Email</a>@endif @if($lead->phone)<a href="tel:{{ preg_replace('/\s+/', '', $lead->phone) }}">Call</a>@endif</div></td>
<td class="requirement-cell">{{ Illuminate\Support\Str::limit($lead->message, 90) }}</td>
<td><span class="status {{ $lead->status }}">{{ ucfirst(str_replace('_',' ',$lead->status)) }}</span></td><td>{{ $lead->created_at->format('d M Y') }}</td>
<td><div class="action-stack"><form method="POST" action="{{ route('leads.status', $lead) }}" class="status-form">@csrf @method('PATCH')<select name="status" onchange="this.form.submit()">@foreach(['new' => 'New', 'in_progress' => 'In progress', 'completed' => 'Completed'] as $value => $label)<option value="{{ $value }}" @selected($lead->status === $value)>{{ $label }}</option>@endforeach</select></form>
<form method="POST" action="{{ route('leads.destroy', $lead) }}" class="inline-form" onsubmit="return confirm('Delete this enquiry?')">@csrf @method('DELETE')<button class="danger-button" type="submit">Delete</button></form></div></td>
</tr>@endforeach</tbody></table></div>
@else<div class="empty">No enquiries match the current filters.</div>@endif
</div></div></section>
@endsection