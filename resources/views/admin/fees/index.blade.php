@extends('layouts.app')
@section('title','Government Fee Manager | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">MRK DIGITAL ADMIN</span><h1>Government Fee Manager</h1><p>Manage official charges separately from MRK service charges. Unverified fees are never presented as confirmed.</p></div></section>
<section class="section"><div class="container">
<div class="actions dashboard-actions">
<a class="button secondary" href="{{ route('fees.public') }}">Public Rate List</a>
<form method="POST" action="{{ route('admin.fees.check') }}">@csrf<button class="button primary" type="submit">Check official sources now</button></form>
</div>
<p class="muted">Source checking downloads official pages and detects content changes. It does not assume that a changed page means a changed fee; review and verify the amount before marking it verified.</p>
<div class="table-card"><div class="table-head"><h2>Add a service / rate</h2></div>
<form method="POST" action="{{ route('admin.fees.store') }}" class="form-grid">@csrf
<label>Department<input name="department" required placeholder="NADRA / FBR / Passport"></label><label>Category<input name="category" required></label><label>Service name<input name="service_name" required></label><label>Priority<input name="priority" value="Standard" required></label><label>Government fee (PKR)<input type="number" step="0.01" min="0" name="government_fee"></label><label>MRK service fee (PKR)<input type="number" step="0.01" min="0" name="mrk_service_fee" value="0" required></label><label>Currency<select name="currency"><option>PKR</option><option>USD</option></select></label><label>Official source URL<input type="url" name="official_source_url" placeholder="https://..."></label><label class="full">Notes<textarea name="source_note"></textarea></label><div class="full"><button class="button primary" type="submit">Add fee record</button></div>
</form></div>
<form method="GET" class="dashboard-filters"><label>Search<input name="search" value="{{ request('search') }}" placeholder="Service, department"></label><label>Verification<select name="status"><option value="">All statuses</option>@foreach(['verified','needs_verification','review_required','source_unavailable'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ str_replace('_',' ',ucfirst($s)) }}</option>@endforeach</select></label><button class="button primary">Filter</button></form>
@foreach($fees as $fee)
<div class="table-card" style="margin:1rem 0;padding:1rem">
<form method="POST" action="{{ route('admin.fees.update',$fee) }}" class="form-grid">@csrf @method('PUT')
<div class="full"><strong>{{ $fee->department }} — {{ $fee->service_name }}</strong> <span class="eyebrow">{{ str_replace('_',' ',$fee->verification_status) }}</span></div>
<label>Department<input name="department" value="{{ $fee->department }}" required></label><label>Category<input name="category" value="{{ $fee->category }}" required></label><label>Service<input name="service_name" value="{{ $fee->service_name }}" required></label><label>Priority<input name="priority" value="{{ $fee->priority }}" required></label><label>Official fee<input type="number" step="0.01" min="0" name="government_fee" value="{{ $fee->government_fee }}"></label><label>MRK charge<input type="number" step="0.01" min="0" name="mrk_service_fee" value="{{ $fee->mrk_service_fee }}" required></label><label>Currency<select name="currency"><option value="PKR" @selected($fee->currency==='PKR')>PKR</option><option value="USD" @selected($fee->currency==='USD')>USD</option></select></label><label>Official source<input type="url" name="official_source_url" value="{{ $fee->official_source_url }}"></label><label>Verification<select name="verification_status"><option value="needs_verification">Needs verification</option><option value="verified" @selected($fee->verification_status==='verified')>Verified (I checked official source)</option></select></label><label class="full">Notes<textarea name="source_note">{{ $fee->source_note }}</textarea></label><label><input type="checkbox" name="is_active" value="1" @checked($fee->is_active)> Active / show publicly</label><div class="full"><button class="button primary" type="submit">Save record</button> <small>Last checked: {{ $fee->last_checked_at?->format('d M Y H:i') ?? 'Never' }} | Last verified: {{ $fee->last_verified_at?->format('d M Y') ?? 'Not verified' }}</small></div>
</form><form method="POST" action="{{ route('admin.fees.destroy',$fee) }}" onsubmit="return confirm('Delete this fee record?')">@csrf @method('DELETE')<button class="danger-button" type="submit">Delete</button></form>
</div>
@endforeach
{{ $fees->links() }}
</div></section>
@endsection
