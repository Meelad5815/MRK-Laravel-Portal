@extends('layouts.app')
@section('title','Receipt '.$workOrder->reference.' | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<div class="section" style="max-width:760px;margin:auto">
<div class="table-card" style="padding:2rem">
<div style="text-align:center"><h1>MRK Digital & Online Services Center</h1><p>Customer Work Receipt</p><h2>{{ $workOrder->reference }}</h2></div>
<hr>
<p><strong>Customer:</strong> {{ $workOrder->customer_name }}</p>
<p><strong>Phone:</strong> {{ $workOrder->customer_phone ?: '—' }}</p>
<p><strong>Service:</strong> {{ $workOrder->service_name }}</p>
<p><strong>Date:</strong> {{ $workOrder->created_at->format('d M Y, h:i A') }}</p>
<table style="width:100%"><tbody>
<tr><td>Government fee</td><td style="text-align:right">PKR {{ number_format($workOrder->government_fee,2) }}</td></tr>
<tr><td>MRK service fee</td><td style="text-align:right">PKR {{ number_format($workOrder->service_fee,2) }}</td></tr>
<tr><th>Total</th><th style="text-align:right">PKR {{ number_format($workOrder->total_amount,2) }}</th></tr>
<tr><td>Paid</td><td style="text-align:right">PKR {{ number_format($workOrder->paid_amount,2) }}</td></tr>
<tr><th>Balance</th><th style="text-align:right">PKR {{ number_format($workOrder->total_amount-$workOrder->paid_amount,2) }}</th></tr>
</tbody></table>
<p><strong>Status:</strong> {{ ucfirst(str_replace('_',' ',$workOrder->status)) }}</p>
@if($workOrder->notes)<p><strong>Notes:</strong> {{ $workOrder->notes }}</p>@endif
<p style="text-align:center;margin-top:2rem">Thank you for choosing MRK Digital.</p>
<div class="actions" style="justify-content:center"><button class="button primary" onclick="window.print()">Print / Save PDF</button><a class="button secondary" href="{{ route('admin.work.index') }}">Back to Work Manager</a></div>
</div></div>
<style>@media print{header,nav,footer,.actions{display:none!important}.section{padding:0!important}.table-card{box-shadow:none!important;border:0!important}}</style>
@endsection
