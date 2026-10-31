@extends('layouts.app')
@section('title', 'Certificate Verification | MRK Madrasa')
@section('robots', 'noindex,nofollow')
@section('content')
<section class="section"><div class="container narrow">
<div class="table-card" style="padding:1.5rem">
<h1>سرٹیفکیٹ کی آن لائن تصدیق</h1>
@if(!$certificate)
<p class="alert">یہ تصدیقی کوڈ درست نہیں یا سرٹیفکیٹ دستیاب نہیں۔</p>
@elseif($certificate->status !== 'valid')
<p class="alert">یہ سرٹیفکیٹ اس وقت معتبر نہیں ہے۔</p>
@else
<p class="alert success">تصدیق کامیاب — یہ سرٹیفکیٹ MRK Madrasa 58 کے ریکارڈ میں موجود اور معتبر ہے۔</p>
<table><tbody>
<tr><th>طالب علم</th><td>{{ $certificate->full_name }}</td></tr>
<tr><th>سرٹیفکیٹ نمبر</th><td>{{ $certificate->certificate_number }}</td></tr>
<tr><th>قسم</th><td>{{ $certificate->certificate_type }}</td></tr>
<tr><th>اجراء کی تاریخ</th><td>{{ $certificate->issued_on }}</td></tr>
<tr><th>حیثیت</th><td>معتبر</td></tr>
</tbody></table>
@endif
</div></div></section>
@endsection
