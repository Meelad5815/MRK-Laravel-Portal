@extends('layouts.app')
@section('title','Dashboard | MRK Digital')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">MRK PORTAL</span><h1>Dashboard</h1><p>Welcome, {{ auth()->user()->name }}. Manage project enquiries from one place.</p></div></section>
<section class="section"><div class="container"><div class="stats"><div><span>Total enquiries</span><strong>{{ $stats['total'] }}</strong></div><div><span>New</span><strong>{{ $stats['new'] }}</strong></div><div><span>In progress</span><strong>{{ $stats['progress'] }}</strong></div><div><span>Completed</span><strong>{{ $stats['completed'] }}</strong></div></div>
<div class="table-card"><div class="table-head"><h2>Recent enquiries</h2></div>@if($leads->count())<div class="table-wrap"><table><thead><tr><th>Name</th><th>Project</th><th>Contact</th><th>Status</th><th>Date</th></tr></thead><tbody>@foreach($leads as $lead)<tr><td>{{ $lead->name }}</td><td>{{ $lead->project_type }}</td><td>{{ $lead->email ?: $lead->phone ?: '—' }}</td><td><span class="status {{ $lead->status }}">{{ ucfirst(str_replace('_',' ',$lead->status)) }}</span></td><td>{{ $lead->created_at->format('d M Y') }}</td></tr>@endforeach</tbody></table></div>@else<div class="empty">No enquiries yet.</div>@endif</div></div></section>
@endsection
