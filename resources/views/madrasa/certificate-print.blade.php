<!doctype html>
<html lang="ur" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex,nofollow">
<title>تعلیمی سند | {{ $certificate->full_name }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}
:root{--navy:#102b49;--blue:#1d496d;--gold:#b89450;--pale:#f8f4e9;--ink:#24384b;--muted:#66788a}
body{margin:0;padding:20px;background:#e9edf2;color:var(--ink);font-family:"Noto Nastaliq Urdu",serif}
.toolbar{max-width:1180px;margin:0 auto 14px;display:flex;justify-content:space-between;align-items:center;gap:10px}
.toolbar a,.toolbar button{font:600 14px Arial,sans-serif;padding:11px 18px;border:0;border-radius:8px;background:var(--navy);color:#fff;text-decoration:none;cursor:pointer}
.sheet{width:1120px;min-height:790px;margin:0 auto;background:#fff;padding:18px;box-shadow:0 12px 42px #142c4524}
.frame{position:relative;min-height:754px;overflow:hidden;text-align:center;padding:25px 55px 18px;border:7px double var(--navy);outline:2px solid var(--gold);outline-offset:-15px;background:radial-gradient(ellipse at center,#fff 0%,#fff 68%,#fcfaf4 100%)}
.frame:before,.frame:after{content:"";position:absolute;width:190px;height:190px;border:2px solid var(--gold);transform:rotate(45deg);opacity:.25;pointer-events:none}
.frame:before{top:-135px;right:-135px}.frame:after{bottom:-135px;left:-135px}
.corner{position:absolute;width:76px;height:76px;border-color:var(--gold);border-style:double;opacity:.75}
.c1{top:20px;right:20px;border-width:0 3px 3px 0}.c2{top:20px;left:20px;border-width:0 0 3px 3px}.c3{bottom:20px;right:20px;border-width:3px 3px 0 0}.c4{bottom:20px;left:20px;border-width:3px 0 0 3px}
.topline{display:flex;justify-content:space-between;align-items:center;color:var(--muted);font:11px Arial,sans-serif;direction:ltr;letter-spacing:1.3px}
.brand{display:flex;align-items:center;justify-content:center;gap:15px;margin:12px auto 3px}
.emblem{width:74px;height:74px;flex:0 0 74px;border:3px double var(--gold);outline:1px solid #d8c18d;outline-offset:4px;border-radius:50%;display:grid;place-items:center;color:var(--navy);font:bold 21px Georgia,serif;letter-spacing:1px;background:var(--pale)}
.brandtext{text-align:right}
.institute{font-size:27px;font-weight:700;color:var(--navy);line-height:1.8}
.tagline{font-size:12px;color:var(--muted);margin-top:-4px}
.bismillah{font:25px Amiri,serif;color:#9a7738;margin:4px 0 0}
.rule{height:2px;background:linear-gradient(90deg,transparent,var(--gold),var(--navy),var(--gold),transparent);margin:12px auto;width:82%}
.eng{font:700 15px Georgia,serif;letter-spacing:4px;color:#9a7738;direction:ltr}
.heading{font-size:37px;font-weight:700;color:var(--navy);line-height:1.8;margin:0}
.intro{font-size:17px;margin:8px 0 0}
.student{font-size:34px;font-weight:700;color:#173f63;display:inline-block;min-width:52%;padding:3px 35px 10px;border-bottom:2px solid var(--gold);margin:0 0 8px}
.bodycopy{font-size:17px;line-height:2.15;max-width:860px;margin:6px auto 8px}
.highlight{color:#8b672a;font-weight:700}
.meta{display:flex;justify-content:center;gap:10px;flex-wrap:wrap;margin:12px auto}
.pill{border:1px solid #d9c79e;border-radius:30px;padding:3px 15px;font-size:12px;color:var(--navy);background:#fcfaf4}
.bottom{display:grid;grid-template-columns:1fr 138px 1fr;gap:24px;align-items:end;margin:12px auto 0;max-width:820px}
.sign{padding-top:8px;border-top:1px solid #718398;font-size:12px;min-height:54px}
.sign strong{display:block;color:var(--navy);font-size:15px}
.qrbox{font:10px Arial,sans-serif;color:var(--muted);text-align:center}
.qrbox img{width:94px;height:94px;display:block;margin:0 auto 4px;border:4px solid #fff;outline:1px solid #d6c18e}
.seal{position:absolute;bottom:118px;left:64px;width:72px;height:72px;border:2px solid #b28a3d;border-radius:50%;display:grid;place-items:center;color:#98732d;font-size:11px;line-height:1.5;transform:rotate(-12deg);opacity:.78;background:#fff9}
.number{font:10px Arial,sans-serif;direction:ltr;color:var(--muted);margin-top:7px;letter-spacing:.4px}
.number strong{color:var(--navy)}
.revoked{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:5;pointer-events:none;font:bold 72px Arial,sans-serif;color:#b42318;opacity:.17;transform:rotate(-23deg)}
@media(max-width:1150px){.sheet{width:100%;min-height:0}.frame{min-height:720px}}
@media(max-width:700px){body{padding:8px}.sheet{padding:7px}.frame{padding:22px 15px;min-height:0}.brand{gap:10px}.emblem{width:55px;height:55px;flex-basis:55px;font-size:16px}.institute{font-size:19px}.heading{font-size:27px}.student{font-size:26px;min-width:80%}.bodycopy{font-size:15px}.bottom{grid-template-columns:1fr 100px 1fr;gap:8px}.qrbox img{width:76px;height:76px}.seal{display:none}.topline{font-size:8px}}
@media print{
 @page{size:A4 landscape;margin:5mm}
 body{padding:0;background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
 .toolbar{display:none}.sheet{width:100%;min-height:0;margin:0;padding:0;box-shadow:none}
 .frame{min-height:190mm;height:190mm;padding:6mm 12mm;break-inside:avoid}
}
</style>
</head>
<body>
@php
$typeLabels = ['hifz'=>'حفظ القرآن الکریم','nazra'=>'ناظرہ قرآنِ کریم','qaida'=>'قاعدہ بغدادی','participation'=>'تعلیمی و تربیتی پروگرام میں شرکت','achievement'=>'نمایاں تعلیمی کارکردگی'];
$typeTitles = ['hifz'=>'سندِ تکمیلِ حفظ القرآن','nazra'=>'سندِ تکمیلِ ناظرہ قرآن','qaida'=>'سندِ تکمیلِ قاعدہ','participation'=>'سندِ شرکت','achievement'=>'سندِ اعزاز و کارکردگی'];
$typeCopy = [
'hifz'=>'جنہوں نے قرآنِ کریم حفظ کرنے کی سعادت حاصل کی۔ اللہ تعالیٰ ان کے حفظ کو مضبوط، عمل کو مقبول اور زندگی کو قرآن کے نور سے منور فرمائے۔',
'nazra'=>'جنہوں نے قرآنِ کریم کی ناظرہ تعلیم مکمل کی۔ اللہ تعالیٰ انہیں قرآنِ کریم پڑھنے، سمجھنے اور اس پر عمل کرنے کی توفیق عطا فرمائے۔',
'qaida'=>'جنہوں نے قاعدہ کی بنیادی تعلیم مکمل کی اور قرآنِ کریم کی درست قرأت سیکھنے کی جانب اہم پیش رفت کی۔',
'participation'=>'جنہوں نے ادارے کے تعلیمی و تربیتی پروگرام میں شرکت کی۔ ادارہ ان کی دلچسپی اور محنت کو سراہتا ہے۔',
'achievement'=>'جنہوں نے اپنی نمایاں تعلیمی کارکردگی، محنت اور لگن سے امتیازی کامیابی حاصل کی۔ ادارہ ان کی حوصلہ افزائی کرتا ہے۔'
];
@endphp
<div class="toolbar"><a href="{{ route('madrasa') }}">پورٹل پر واپس</a><button onclick="window.print()">پرنٹ کریں / PDF محفوظ کریں (Save as PDF)</button></div>
<main class="sheet"><section class="frame">
<span class="corner c1"></span><span class="corner c2"></span><span class="corner c3"></span><span class="corner c4"></span>
<div class="topline"><span>MRK MADRASA MANAGEMENT SYSTEM 58</span><span>OFFICIAL ACADEMIC CERTIFICATE</span></div>
@if($certificate->status !== 'valid')<div class="revoked">منسوخ شدہ</div>@endif
<div class="brand"><div class="emblem">MRK<br><small>58</small></div><div class="brandtext"><div class="institute">مدرسۃ المدینہ 58</div><div class="tagline">قرآنِ کریم کی تعلیم، تربیت اور کردار سازی</div></div></div>
<div class="bismillah">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
<div class="rule"></div>
<div class="eng">CERTIFICATE OF {{ $certificate->certificate_type === 'participation' ? 'PARTICIPATION' : ($certificate->certificate_type === 'achievement' ? 'ACHIEVEMENT' : 'COMPLETION') }}</div>
<div class="heading">{{ $typeTitles[$certificate->certificate_type] ?? 'سندِ تعلیمی کامیابی' }}</div>
<div class="intro">یہ سند نہایت مسرت اور اعزاز کے ساتھ عطا کی جاتی ہے</div>
<div class="student">{{ $certificate->full_name }}</div>
<div class="bodycopy">
{{ $typeCopy[$certificate->certificate_type] ?? 'ادارہ طالب علم کی محنت اور کامیابی کو سراہتا ہے۔' }}
@if($certificate->external_institution)
<br>سابقہ ادارہ: <span class="highlight">{{ $certificate->external_institution }}</span>
@endif
</div>
<div class="meta">
<span class="pill">تاریخِ اجراء: {{ date('d-m-Y', strtotime($certificate->issued_on)) }}</span>
<span class="pill">طالب علم کوڈ: {{ $certificate->student_code }}</span>
<span class="pill">شعبہ: {{ ['hifz'=>'حفظ','nazra'=>'ناظرہ','qaida'=>'قاعدہ'][$certificate->program] ?? 'عمومی' }}</span>
<span class="pill">حیثیت: {{ $certificate->status === 'valid' ? 'معتبر' : 'منسوخ' }}</span>
</div>
<div class="bottom">
<div class="sign"><strong>{{ $certificate->issued_by_name ?: 'مہتمم / ناظم' }}</strong>دستخط و نامِ جاری کنندہ</div>
<div class="qrbox"><img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&margin=2&data={{ urlencode(route('madrasa.certificates.verify', $certificate->verification_token)) }}" alt="سرٹیفکیٹ تصدیقی QR"><span>QR اسکین کرکے اصلیت کی تصدیق کریں</span></div>
<div class="sign"><strong>مدرسۃ المدینہ 58</strong>دستخط و مہرِ ادارہ</div>
</div>
<div class="seal">مہرِ ادارہ<br>MRK 58</div>
<div class="number"><strong>CERTIFICATE NO:</strong> {{ $certificate->certificate_number }}</div>
<div class="number">VERIFY: {{ route('madrasa.certificates.verify', $certificate->verification_token) }}</div>
</section></main>
</body></html>
