@extends('layouts.app')
@section('title','MRK Online Work Hub | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<section class="page-hero">
  <div class="container narrow">
    <span class="eyebrow">MRK DIGITAL • CENTRAL CONTROL</span>
    <h1>Online Work Hub</h1>
    <p>تمام ڈیجیٹل اور آن لائن کام ایک جگہ سے کھولیں اور منظم کریں۔</p>
    <div class="actions"><a class="button primary" href="{{ route('dashboard') }}">Main Dashboard</a><a class="button secondary" href="{{ route('fees.public') }}">Public Government Fee List</a></div>
  </div>
</section>
<section class="section">
<div class="container">
  <div class="table-card" style="padding:1.1rem;margin-bottom:1.25rem">
    <label for="mrk-hub-search"><strong>کسی کام کو تلاش کریں</strong></label>
    <input id="mrk-hub-search" type="search" placeholder="مثلاً NADRA، فیس، کسٹمر، انوائس، ویب سائٹ..." style="width:100%;margin-top:.5rem;padding:.85rem;border:1px solid #cbd5e1;border-radius:10px">
    <p id="mrk-hub-count" aria-live="polite" style="margin:.55rem 0 0;color:#64748b">تمام دستیاب سیکشنز دکھائے جا رہے ہیں۔</p>
  </div>

  <div class="stats" style="margin-bottom:1.5rem">
    <div><span>مرکزی نظام</span><strong>01 Hub</strong></div>
    <div><span>سرکاری خدمات</span><strong>NADRA • FBR • Passport</strong></div>
    <div><span>کاروباری انتظام</span><strong>Clients • Orders</strong></div>
    <div><span>ذاتی ٹولز</span><strong>Fitness • Cloud</strong></div>
  </div>

  <div class="table-card mrk-hub-group" style="padding:1.2rem;margin-bottom:1.25rem">
    <h2>1. سرکاری اور آن لائن سروسز</h2>
    <p>سرکاری فیس، سروس چارجز اور کسٹمر کے کام کا مرکزی انتظام۔</p>
    <div class="mrk-hub-grid">
      <a class="mrk-hub-card" href="{{ route('admin.fees.index') }}" data-search="government fees fee manager nadra passport fbr driving licence police verification سرکاری فیس">
        <strong>Government Fee Manager</strong><span>NADRA، Passport، FBR، Driving Licence، Police Verification اور دیگر فیس ریکارڈز</span><b>فیس منظم کریں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('fees.public') }}" data-search="public fee list customer fee calculator سرکاری فیس عوامی فہرست">
        <strong>Public Government Fee List</strong><span>کسٹمر کو سرکاری فیس اور MRK سروس چارجز دکھائیں</span><b>فیس لسٹ کھولیں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('admin.customers.index') }}" data-search="customers clients کسٹمر گاہک">
        <strong>Customers / Clients</strong><span>کسٹمرز کے ریکارڈ اور رابطہ معلومات</span><b>کسٹمرز کھولیں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('admin.quotes.index') }}" data-search="quotation estimate quote تخمینہ کوٹیشن">
        <strong>Quotations</strong><span>کام شروع کرنے سے پہلے قیمت کا تخمینہ</span><b>کوٹیشن کھولیں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('admin.invoices.index') }}" data-search="invoice bill payment ادائیگی بل رسید">
        <strong>Invoices & Payments</strong><span>بل، ادائیگی اور بقایا جات کا انتظام</span><b>انوائس کھولیں →</b>
      </a>
    </div>
  </div>

  <div class="table-card mrk-hub-group" style="padding:1.2rem;margin-bottom:1.25rem">
    <h2>2. MRK Digital کاروباری انتظام</h2>
    <p>اپنی ویب سائٹ، سروسز، پروجیکٹس اور مواد ایک جگہ سے سنبھالیں۔</p>
    <div class="mrk-hub-grid">
      <a class="mrk-hub-card" href="{{ route('admin.services.index') }}" data-search="services service catalog سروسز خدمات">
        <strong>Manage Services</strong><span>ویب سائٹ پر دکھائی جانے والی سروسز</span><b>سروسز منظم کریں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('admin.projects.index') }}" data-search="projects portfolio پروجیکٹس پورٹ فولیو">
        <strong>Projects & Portfolio</strong><span>اپنے مکمل اور جاری پروجیکٹس</span><b>پروجیکٹس کھولیں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('admin.blog.index') }}" data-search="blog posts articles بلاگ مضامین">
        <strong>Blog & Articles</strong><span>مضامین اور ویب سائٹ کا مواد</span><b>بلاگ کھولیں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('admin.settings') }}" data-search="settings configuration website سیٹنگز ویب سائٹ">
        <strong>Website Settings</strong><span>ویب سائٹ کی بنیادی اور SEO سیٹنگز</span><b>سیٹنگز کھولیں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('tools') }}" data-search="free tools qr converter seo tools فری ٹولز">
        <strong>Free Digital Tools</strong><span>QR، SEO اور دیگر دستیاب آن لائن ٹولز</span><b>ٹولز کھولیں →</b>
      </a>
    </div>
  </div>

  <div class="table-card mrk-hub-group" style="padding:1.2rem;margin-bottom:1.25rem">
    <h2>3. روزمرہ آپریشنز اور ذاتی نظام</h2>
    <p>روزانہ کے کام، درخواستیں اور اندرونی ڈیش بورڈز۔</p>
    <div class="mrk-hub-grid">
      <a class="mrk-hub-card" href="{{ route('dashboard') }}#enquiries" data-search="enquiries leads requests درخواستیں نئے کام">
        <strong>New Enquiries / Work Requests</strong><span>نئے کلائنٹس کی درخواستیں، اسٹیٹس اور فالو اپ</span><b>درخواستیں دیکھیں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('madrasa') }}" data-search="madrasa students attendance madrasa management مدرسہ طلبہ حاضری">
        <strong>Madrasa Management</strong><span>طلبہ، حاضری اور مدرسہ ریکارڈ</span><b>مدرسہ پورٹل →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('fitness.index') }}" data-search="fitness running workout صحت ورزش">
        <strong>Fitness Dashboard</strong><span>ورزش، دوڑ اور فٹنس ریکارڈ</span><b>فٹنس کھولیں →</b>
      </a>
      <a class="mrk-hub-card" href="{{ route('cloud.index') }}" data-search="personal cloud files documents storage فائلیں کلاؤڈ">
        <strong>Personal Cloud</strong><span>اپنی فائلیں اور دستاویزات محفوظ رکھیں</span><b>کلاؤڈ کھولیں →</b>
      </a>
    </div>
  </div>

  <div class="alert" style="margin-top:1rem">
    <strong>اہم نوٹ:</strong> یہ مرکزی Hub موجودہ ماڈیولز تک ایک جگہ سے رسائی دیتا ہے۔ ہر کارڈ متعلقہ اصل مینیجر کھولتا ہے؛ اس سے موجودہ ڈیٹا، اجازتیں اور سکیورٹی برقرار رہتی ہیں۔ سرکاری فیس کی رقم شائع کرنے سے پہلے آفیشل ذریعے سے تصدیق ضروری ہے۔
  </div>
</div>
</section>
<style>
.mrk-hub-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(230px,1fr));gap:1rem;margin-top:1rem}
.mrk-hub-card{display:flex;flex-direction:column;gap:.55rem;padding:1rem;border:1px solid #dbe3ef;border-radius:14px;background:#fff;color:#102b4e;text-decoration:none;min-height:145px;transition:transform .15s ease,border-color .15s ease,box-shadow .15s ease}
.mrk-hub-card:hover,.mrk-hub-card:focus{transform:translateY(-2px);border-color:#2876c7;box-shadow:0 8px 24px rgba(15,45,80,.09);outline:none}
.mrk-hub-card strong{font-size:1.02rem}
.mrk-hub-card span{color:#53657a;font-size:.92rem;line-height:1.5;flex:1}
.mrk-hub-card b{color:#1765ad;font-size:.88rem}
.mrk-hub-group h2{margin-top:0}
@media(max-width:600px){.mrk-hub-grid{grid-template-columns:1fr}.mrk-hub-card{min-height:unset}}
</style>
<script>
(function(){
  const input=document.getElementById('mrk-hub-search');
  if(!input)return;
  const cards=Array.from(document.querySelectorAll('.mrk-hub-card'));
  const groups=Array.from(document.querySelectorAll('.mrk-hub-group'));
  const count=document.getElementById('mrk-hub-count');
  input.addEventListener('input',function(){
    const q=this.value.trim().toLocaleLowerCase();
    let visible=0;
    cards.forEach(card=>{
      const match=!q || (card.innerText+' '+(card.dataset.search||'')).toLocaleLowerCase().includes(q);
      card.hidden=!match;
      if(match)visible++;
    });
    groups.forEach(group=>group.hidden=!group.querySelector('.mrk-hub-card:not([hidden])'));
    count.textContent=q ? visible+' کام ملے' : 'تمام دستیاب سیکشنز دکھائے جا رہے ہیں۔';
  });
})();
</script>
@endsection
