@extends('layouts.app')
@section('title','Birth Certificate Case Receipt | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<div class="section" style="max-width:850px">
  <div class="table-card receipt" style="padding:2rem">
    <div style="display:flex;justify-content:space-between;gap:1rem;align-items:flex-start"><div><span class="eyebrow">MRK DIGITAL & ONLINE SERVICES CENTER</span><h1>درخواست رسید</h1><p>Birth Certificate / پیدائش سرٹیفکیٹ</p></div><strong>{{ $case->reference }}</strong></div>
    <hr>
    <p><strong>درخواست کی تاریخ:</strong> {{ $case->created_at->format('d M Y, h:i A') }}</p>
    <h3>بچے کی تفصیلات</h3><p><strong>نام:</strong> {{ $case->child_name }} &nbsp; <strong>تاریخ پیدائش:</strong> {{ $case->date_of_birth->format('d M Y') }} &nbsp; <strong>جنس:</strong> {{ ucfirst($case->gender) }}</p>
    <h3>والدین</h3><p><strong>والد:</strong> {{ $case->father_name }} &nbsp; <strong>والدہ:</strong> {{ $case->mother_name }}</p>
    <p><strong>یونین کونسل:</strong> {{ $case->union_council }} &nbsp; <strong>اندراج:</strong> {{ $case->registration_type==='late'?'تاخیر سے':'عام' }}</p>
    <hr>
    <table style="width:100%"><tr><td>سرکاری فیس</td><td style="text-align:right">PKR {{ number_format($case->government_fee,2) }}</td></tr><tr><td>MRK سروس فیس</td><td style="text-align:right">PKR {{ number_format($case->mrk_service_fee,2) }}</td></tr><tr><th>کل رقم</th><th style="text-align:right">PKR {{ number_format((float)$case->government_fee+(float)$case->mrk_service_fee,2) }}</th></tr><tr><td>وصول شدہ</td><td style="text-align:right">PKR {{ number_format($case->paid_amount,2) }}</td></tr><tr><th>بقایا</th><th style="text-align:right">PKR {{ number_format((float)$case->government_fee+(float)$case->mrk_service_fee-(float)$case->paid_amount,2) }}</th></tr></table>
    <p style="margin-top:1.5rem"><strong>اسٹیٹس:</strong> {{ ucfirst(str_replace('_',' ',$case->status)) }}</p>
    <p class="alert">یہ MRK Digital کی وصولی / درخواست رسید ہے، سرکاری Birth Certificate یا Union Council کی منظوری نہیں۔ سرکاری کارروائی اور فیس متعلقہ رجسٹرار کی تصدیق کے تابع ہے۔</p>
    <div class="no-print" style="display:flex;gap:.75rem;flex-wrap:wrap"><button class="button primary" onclick="window.print()">Print Receipt</button><a class="button secondary" href="{{ route('admin.birth-certificates.index') }}">واپس درخواستوں پر</a></div>
  </div>
</div>
<style>@media print{header,footer,.no-print{display:none!important}.section{padding:0!important}.receipt{box-shadow:none!important;border:0!important}}</style>
@endsection
