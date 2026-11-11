<!doctype html>
<html lang="ur" dir="rtl">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>تعلیمی سند | {{ $certificate->full_name }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;600;700&family=Amiri:wght@400;700&display=swap" rel="stylesheet">
<style>
*{box-sizing:border-box}
:root{--navy:#102f4d;--gold:#b28a3d;--ink:#20384e;--muted:#617488}
body{margin:0;padding:22px;background:#edf1f5;color:var(--ink);font-family:"Noto Nastaliq Urdu",serif}
.toolbar{max-width:1120px;margin:0 auto 14px;display:flex;justify-content:space-between;align-items:center;gap:10px}
.toolbar a,.toolbar button{font-family:inherit;font-size:14px;padding:9px 18px;border:0;border-radius:7px;background:var(--navy);color:#fff;text-decoration:none;cursor:pointer}
.sheet{width:1120px;min-height:790px;margin:0 auto;background:#fff;padding:20px;box-shadow:0 8px 35px #152b441c}
.frame{min-height:750px;border:5px solid var(--navy);outline:2px solid var(--gold);outline-offset:-12px;padding:26px 48px;position:relative;text-align:center;overflow:hidden}
.frame:before,.frame:after{content:"";position:absolute;width:170px;height:170px;border:2px solid var(--gold);transform:rotate(45deg);opacity:.45}
.frame:before{top:-112px;right:-112px}.frame:after{bottom:-112px;left:-112px}
.topline{display:flex;justify-content:space-between;align-items:center;color:var(--muted);font:12px Arial,sans-serif;direction:ltr}
.emblem{width:76px;height:76px;border:3px double var(--gold);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:8px auto 2px;color:var(--navy);font:bold 19px Georgia,serif;letter-spacing:1px}
.bismillah{font:26px Amiri,serif;color:var(--gold);margin:2px 0 4px}
.institute{font-size:27px;font-weight:700;color:var(--navy);line-height:1.8}
.tagline{font-size:13px;color:var(--muted);margin-top:-3px}
.rule{height:2px;background:linear-gradient(90deg,transparent,var(--gold),var(--navy),var(--gold),transparent);margin:13px auto;width:78%}
.heading{font-size:39px;font-weight:700;color:var(--navy);line-height:1.8;margin:4px 0}
.eng{font:700 17px Georgia,serif;letter-spacing:4px;color:var(--gold);direction:ltr}
.intro{font-size:19px;margin:14px 0 4px}
.student{font-size:36px;font-weight:700;color:#153c61;display:inline-block;min-width:55%;padding:4px 35px 10px;border-bottom:2px solid var(--gold);margin:4px 0 8px}
.bodycopy{font-size:18px;line-height:2.2;max-width:850px;margin:7px auto}
.highlight{color:#8c6726;font-weight:700}
.meta{display:flex;justify-content:center;gap:18px;flex-wrap:wrap;margin:12px auto}
.pill{border:1px solid #d7c18f;border-radius:30px;padding:4px 18px;font-size:14px;color:var(--navy);background:#fbf8f0}
.bottom{display:grid;grid-template-columns:1fr 150px 1fr;gap:22px;align-items:end;margin-top:20px}
.sign{padding-top:7px;border-top:1px solid #718398;font-size:14px;min-height:58px}
.sign strong{display:block;color:var(--navy);font-size:16px}
.qrbox{font:11px Arial,sans-serif;color:var(--muted);text-align:center}
.qrbox img{width:104px;height:104px;display:block;margin:0 auto 4px;border:5px solid #fff;outline:1px solid #d6c18e}
.number{font:12px Arial,sans-serif;direction:ltr;color:var(--muted);margin-top:13px}
 .revoked{position:absolute;inset:0;display:flex;align-items:center;justify-content:center;z-index:5;pointer-events:none;font:bold 76px Arial,sans-serif;color:#b42318;opacity:.16;transform:rotate(-24deg)}
.seal{position:absolute;bottom:118px;left:62px;width:78px;height:78px;border:2px solid #b28a3d;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#98732d;font-size:12px;transform:rotate(-14deg);opacity:.78}
@media(max-width:1150px){.sheet{width:100%;min-height:0}.frame{min-height:720px}}
@media print{
 @page{size:A4 landscape;margin:5mm}
 body{padding:0;background:#fff;-webkit-print-color-adjust:exact;print-color-adjust:exact}
 .toolbar{display:none}.sheet{width:100%;min-height:0;margin:0;padding:0;box-shadow:none}
 .frame{min-height:190mm;height:190mm;padding:6mm 12mm;break-inside:avoid}
}
</style>
</head>
<body>
<div class="toolbar"><a href="{{ route('madrasa') }}">پورٹل پر واپس</a><button onclick="window.print()">پرنٹ کریں / PDF محفوظ کریں</button></div>
<main class="sheet"><section class="frame">
<div class="topline"><span>MRK MADRASA MANAGEMENT SYSTEM 58</span><span>تعلیمی ریکارڈ و تصدیق</span></div>
@if($certificate->status !== 'valid')<div class="revoked">منسوخ شدہ</div>@endif
<div class="emblem">MRK</div>
<div class="bismillah">بِسْمِ اللّٰهِ الرَّحْمٰنِ الرَّحِيْمِ</div>
<div class="institute">مدرسۃ المدینہ 58</div>
<div class="tagline">قرآنِ کریم کی تعلیم، تربیت اور کردار سازی</div>
<div class="rule"></div>
<div class="eng">CERTIFICATE OF ACHIEVEMENT</div>
<div class="heading">سندِ تکمیل و اعزاز</div>
<div class="intro">یہ سند نہایت مسرت کے ساتھ عطا کی جاتی ہے</div>
<div class="student">{{ $certificate->full_name }}</div>
<div class="bodycopy">
جنہوں نے <span class="highlight">{{ ['hifz'=>'حفظ القرآن الکریم','nazra'=>'ناظرہ قرآنِ کریم','qaida'=>'قاعدہ بغدادی','participation'=>'تعلیمی و تربیتی پروگرام میں شرکت','achievement'=>'نمایاں تعلیمی کارکردگی'][$certificate->certificate_type] ?? $certificate->certificate_type }}</span>
کے سلسلے میں اپنی تعلیمی تکمیل / کامیابی حاصل کی۔ ادارہ ان کی محنت، لگن اور کامیابی کو سراہتا ہے اور ان کے روشن مستقبل کے لیے دعاگو ہے۔
@if($certificate->external_institution)
<br>سابقہ ادارہ: <span class="highlight">{{ $certificate->external_institution }}</span>
@endif
</div>
<div class="meta">
<span class="pill">تاریخِ اجراء: {{ date('d-m-Y', strtotime($certificate->issued_on)) }}</span>
<span class="pill">طالب علم کوڈ: {{ $certificate->student_code }}</span>
<span class="pill">حیثیت: {{ $certificate->status === 'valid' ? 'معتبر' : 'منسوخ' }}</span>
</div>
<div class="bottom">
<div class="sign"><strong>{{ $certificate->issued_by_name ?: 'مہتمم / ناظم' }}</strong>دستخط و نامِ جاری کنندہ</div>
<div class="qrbox"><img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&margin=2&data={{ urlencode(route('madrasa.certificates.verify', $certificate->verification_token)) }}" alt="سرٹیفکیٹ تصدیقی QR"><span>اس QR کو اسکین کرکے تصدیق کریں</span></div>
<div class="sign"><strong>مدرسۃ المدینہ 58</strong>مہرِ ادارہ</div>
</div>
<div class="seal">مہرِ ادارہ<br>MRK 58</div>
<div class="number">CERTIFICATE NO: {{ $certificate->certificate_number }}</div>
<div class="number">VERIFICATION: {{ route('madrasa.certificates.verify', $certificate->verification_token) }}</div>
</section></main>
</body></html>