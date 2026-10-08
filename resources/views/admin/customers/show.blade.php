@extends('layouts.app')

@section('title', $customer->name . ' | Customers | MRK Digital')
@section('robots','noindex,nofollow')

@section('content')
<div class="section">
    <div class="section-heading">
        <div>
            <span class="eyebrow">CRM / CUSTOMER</span>
            <h1>{{ $customer->name }}</h1>
            <p>{{ $customer->company ?: 'Customer profile and communication history.' }}</p>
        </div>
        <div class="actions">
            <a class="button secondary" href="{{ route('admin.customers.edit', $customer) }}">Edit</a>
            <a class="button secondary" href="{{ route('admin.customers.index') }}">All Customers</a>
        </div>
    </div>

    @if(session('success'))<div class="alert">{{ session('success') }}</div>@endif

    <div class="customer-grid">
        <article class="customer-card">
            <h2>Contact</h2>
            <div class="customer-detail-list">
                <div><span>Email</span><strong>{{ $customer->email ?: '—' }}</strong></div>
                <div><span>Phone</span><strong>{{ $customer->phone ?: '—' }}</strong></div>
                <div><span>Company</span><strong>{{ $customer->company ?: '—' }}</strong></div>
                <div><span>Source</span><strong>{{ $customer->source ?: '—' }}</strong></div>
            </div>
            <div class="contact-actions large">
                @if($customer->email)<a class="button secondary" href="mailto:{{ $customer->email }}">Email</a>@endif
                @if($customer->phone)
                    <a class="button secondary" href="tel:{{ preg_replace('/\s+/', '', $customer->phone) }}">Call</a>
                    <a class="button secondary" target="_blank" rel="noopener" href="https://wa.me/{{ preg_replace('/\D+/', '', $customer->phone) }}">WhatsApp</a>
                @endif
            </div>
        </article>

        <article class="customer-card">
            <h2>Follow-up</h2>
            <div class="customer-detail-list">
                <div><span>Status</span><strong>{{ ucfirst($customer->status) }}</strong></div>
                <div><span>Priority</span><strong>{{ ucfirst($customer->priority) }}</strong></div>
                <div><span>Last contacted</span><strong>{{ $customer->last_contacted_at?->format('d M Y H:i') ?? '—' }}</strong></div>
                <div><span>Next follow-up</span><strong>{{ $customer->next_follow_up_at?->format('d M Y H:i') ?? 'Not scheduled' }}</strong></div>
            </div>
            <a class="button primary" href="{{ route('admin.customers.edit', $customer) }}">Update follow-up</a>
        </article>
    </div>

    <article class="customer-card notes-card">
        <h2>Notes</h2>
        <p class="rich-content">{{ $customer->notes ?: 'No notes recorded yet.' }}</p>
    </article>

    <article class="table-card">
        <div class="table-head"><h2>Lead history</h2><span>{{ $customer->leads->count() }} linked</span></div>
        @if($customer->leads->count())
            <div class="table-wrap"><table>
                <thead><tr><th>Project</th><th>Requirement</th><th>Status</th><th>Date</th></tr></thead>
                <tbody>@foreach($customer->leads as $lead)
                    <tr><td><strong>{{ $lead->project_type }}</strong></td><td class="requirement-cell">{{ IlluminateSupport\Str::limit($lead->message, 120) }}</td><td>{{ ucfirst(str_replace('_',' ',$lead->status)) }}</td><td>{{ $lead->created_at->format('d M Y') }}</td></tr>
                @endforeach</tbody>
            </table></div>
        @else
            <div class="empty">No linked leads yet.</div>
        @endif
    </article>
</div>
@endsection
