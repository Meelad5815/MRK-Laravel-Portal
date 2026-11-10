<!doctype html>
<html lang="ur" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>MRK Madrasa Certificate</title>
<style>
*{box-sizing:border-box}body{margin:0;padding:24px;background:#f1f4f8;color:#172b45;font-family:Arial,"Noto Nastaliq Urdu",sans-serif}.toolbar{max-width:1000px;margin:0 auto 16px;display:flex;justify-content:space-between;gap:12px}.toolbar button,.toolbar a{padding:12px 18px;border:0;border-radius:8px;background:#0b4775;color:white;text-decoration:none;font-weight:bold;cursor:pointer}.certificate{max-width:1000px;min-height:680px;margin:auto;padding:54px;border:12px double #b28a36;background:#fff;text-align:center;position:relative}.brand{font-size:16px;letter-spacing:3px;color:#987329}.certificate h1{font-size:42px;margin:28px 0;color:#153b60}.subtitle{font-size:22px;margin:20px}.student{font-size:34px;font-weight:bold;color:#153b60;border-bottom:2px solid #b28a36;display:inline-block;padding:12px 36px;margin:18px}.details{font-size:18px;line-height:2.1;margin:20px auto;max-width:720px}.number{font-size:14px;color:#52677e;margin-top:28px}.signatures{display:flex;justify-content:space-between;gap:40px;margin-top:65px}.signatures div{width:40%;border-top:1px solid #60758a;padding-top:10px}@media print{body{padding:0;background:#fff}.toolbar{display:none}.certificate{max-width:none;min-height:100vh;border-width:12px;page-break-inside:avoid}@page{size:A4 landscape;margin:8mm}}</style>
</head>
<body>
<div class="toolbar"><a href="{{ route('madrasa') }}">پورٹل پر واپس</a><button onclick="window.print()">پرنٹ کریں / Save as PDF</button></div>
<main class="certificate">
<div class="brand">MRK MADRASA 58</div>
<h1>تعلیمی سند</h1>
<div class="subtitle">اس بات کی تصدیق کی جاتی ہے کہ</div>
<div class="student">{{ $certificate->full_name }}</div>
<div class="details">
نے شعبہ <strong>{{ ['hifz'=>'حفظ القرآن','nazra'=>'ناظرہ قرآن','qaida'=>'قاعدہ'][$certificate->program] ?? $certificate->program }}</strong>
میں تعلیمی تکمیل / کارکردگی کا سرٹیفکیٹ حاصل کیا ہے۔
@if($certificate->external_institution)
<br>سابقہ ادارہ: <strong>{{ $certificate->external_institution }}</strong>
@endif
@if($certificate->previous_institution)
<br>طالب علم کا سابقہ مدرسہ: <strong>{{ $certificate->previous_institution }}</strong>
@endif
<br>تاریخ اجراء: <strong>{{ $certificate->issued_on }}</strong>
</div>
<div class="number">سرٹیفکیٹ نمبر: {{ $certificate->certificate_number }}</div>
<div class="number">آن لائن تصدیق: {{ route('madrasa.certificates.verify', $certificate->verification_token) }}</div>
<div class="signatures"><div>{{ $certificate->issued_by_name ?: 'مہتمم / ناظم' }}<br>دستخط و مہر</div><div>ادارہ: مدرسۃ المدینہ 58<br>سرکاری مہر</div></div>
</main>
</body></html>