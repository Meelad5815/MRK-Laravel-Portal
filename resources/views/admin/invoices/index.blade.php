@extends('layouts.app')
@section('title','Invoices | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<div class="section"><div class="section-heading"><div><span class="eyebrow">BUSINESS</span><h1>Invoices</h1><p>Manage invoices from the MRK Digital admin portal.</p></div><a class="button primary" href="{{ route('admin.invoices.create') }}">Create</a></div>
<form class="filter-bar" method="get"><input name="search" value="{{ request('search') }}" placeholder="Search number or title"><button class="button secondary">Search</button></form>
<div class="table-wrap table-card"><table><thead><tr><th>Number</th><th>Title</th><th>Customer</th><th>Total</th><th>Status</th><th>Actions</th></tr></thead><tbody>
@forelse($invoices as $item)<tr><td><strong>{{ $item->number }}</strong></td><td>{{ $item->title }}</td><td>{{ $item->customer?->name ?: '—' }}</td><td>PKR {{ number_format($item->total,2) }}</td><td>{{ ucfirst($item->status) }}</td><td><a class="text-link" href="{{ route('admin.invoices.show',$item) }}">View</a> · <a class="text-link" href="{{ route('admin.invoices.edit',$item) }}">Edit</a> · <form class="inline-form" method="post" action="{{ route('admin.invoices.destroy',$item) }}" onsubmit="return confirm('Delete this record?')">@csrf @method('DELETE')<button class="danger-button">Delete</button></form></td></tr>@empty<tr><td colspan="6" class="empty">No records yet.</td></tr>@endforelse
</tbody></table></div>{{ $invoices->links() }}</div>
@endsection