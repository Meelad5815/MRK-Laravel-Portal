@extends('layouts.app')
@section('title','Government Service Fees & MRK Charges | MRK Digital')
@section('description','Government service fees and separate MRK Digital assistance charges. Verify official amounts before payment.')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">MRK DIGITAL ONLINE SERVICES CENTER</span><h1>Government Fees & Service Charges</h1><p>Official government fee and MRK Digital assistance charge are shown separately. Rates marked pending must be confirmed with the department before payment.</p></div></section>
<section class="section"><div class="container">
<form method="GET" class="dashboard-filters"><label>Search service<input name="search" value="{{ request('search') }}" placeholder="B-Form, passport, NTN..."></label><label>Department<select name="department"><option value="">All departments</option>@foreach($departments as $department)<option @selected(request('department')===$department)>{{ $department }}</option>@endforeach</select></label><button class="button primary">Search</button></form>
<div class="table-card"><div class="table-wrap"><table><thead><tr><th>Department / Service</th><th>Priority</th><th>Government fee</th><th>MRK service charge</th><th>Total</th><th>Rate status</th></tr></thead><tbody>
@forelse($fees as $fee)
<tr><td><strong>{{ $fee->service_name }}</strong><br><small>{{ $fee->department }} · {{ $fee->category }}</small></td><td>{{ $fee->priority }}</td><td>{{ $fee->government_fee !== null ? number_format((float)$fee->government_fee,2).' '.$fee->currency : 'Confirm with department' }}</td><td>{{ number_format((float)$fee->mrk_service_fee,2).' '.$fee->currency }}</td><td>{{ $fee->government_fee !== null ? number_format((float)$fee->government_fee+(float)$fee->mrk_service_fee,2).' '.$fee->currency : 'Not calculated' }}</td><td>{{ $fee->verification_status==='verified' ? 'Verified' : 'Pending verification' }}<br><small>{{ $fee->last_verified_at?->format('d M Y') ?? 'No verification date' }}</small></td></tr>
@empty<tr><td colspan="6">No services found.</td></tr>@endforelse
</tbody></table></div></div>
<p class="muted">Official fees may vary by applicant, age, location, service priority, taxes, courier, vehicle details or assessment. MRK service charges are private assistance charges, not government fees. Always use the official portal for final payable amount.</p>
</div></section>
@endsection
