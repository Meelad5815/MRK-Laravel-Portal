@extends('layouts.app')

@section('title', 'Hifz-e-Quran Offline Muraja’ah | MRK Digital')
@section('description', 'Free, on-device Quran recitation practice and word-by-word review.')
@section('html_lang', 'ur')

@section('content')
<section class="section">
  <div class="container" style="max-width:1000px">
    <div style="text-align:center;margin-bottom:2rem">
      <p class="eyebrow">MRK Quran Learning</p>
      <h1>حفظِ قرآن — آف لائن دہرائی</h1>
      <p class="muted">مفت قرآن ریکگنیشن ماڈل آپ کے فون پر چلتا ہے۔ مائیک کی آواز کسی سرور کو نہیں بھیجی جاتی۔</p>
    </div>

    <div class="card" style="padding:1.25rem;margin-bottom:1rem">
      <h2 style="margin-top:0">1۔ سورت اور آیات منتخب کریں</h2>
      <div class="grid" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(190px,1fr));gap:1rem">
        <label>سورت منتخب کریں
          <select id="surahSelect" class="form-control" style="width:100%;padding:.7rem;margin-top:.35rem"><option value="">سورتیں لوڈ ہو رہی ہیں…</option></select>
        </label>
        <label>شروع آیت
          <select id="fromAyah" class="form-control" style="width:100%;padding:.7rem;margin-top:.35rem" disabled></select>
        </label>
        <label>آخری آیت
          <select id="toAyah" class="form-control" style="width:100%;padding:.7rem;margin-top:.35rem" disabled></select>
        </label>
      </div>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap;margin-top:1rem">
        <button id="loadRange" class="btn btn-primary" type="button">آیات لوڈ کریں</button>
        <button id="resetProgress" class="btn btn-secondary" type="button">دوبارہ شروع کریں</button>
      </div>
      <p id="loadStatus" class="muted" role="status" style="margin:.75rem 0 0">قرآن متن لوڈ کیا جا رہا ہے۔</p>
    </div>

    <div class="card" style="padding:1.25rem;margin-bottom:1rem">
      <h2 style="margin-top:0">منتخب قرآنِ مجید کا متن</h2>
      <div id="quranText" dir="rtl" lang="ar" style="font-family:serif;font-size:1.7rem;line-height:2.2;text-align:justify;min-height:100px">سورت منتخب کریں۔</div>
      <p id="position" class="muted" aria-live="polite">ابھی کوئی آیت منتخب نہیں۔</p>
    </div>

    <div class="card" style="padding:1.25rem;margin-bottom:1rem">
      <h2 style="margin-top:0">2۔ مفت آف لائن ماڈل کی خودکار تیاری</h2>
      <p class="muted">صفحہ کھلتے ہی ماڈل خودکار طور پر لوڈ اور براؤزر میں محفوظ ہونا شروع ہوگا؛ الگ سے ڈاؤن لوڈ بٹن دبانے کی ضرورت نہیں۔ پہلی مرتبہ تقریباً 50 MB یا اس سے زیادہ ڈیٹا استعمال ہو سکتا ہے۔ تیاری مکمل ہونے کے بعد ماڈل آف لائن بھی دستیاب ہوگا۔</p>
      <button id="loadModel" class="btn btn-secondary" type="button">ماڈل لوڈ نہ ہو تو دوبارہ کوشش کریں</button>
      <p id="modelStatus" class="muted" role="status" aria-live="polite" style="margin:.75rem 0 0">ماڈل خودکار طور پر تیار ہو رہا ہے…</p>
    </div>

    <div class="card" style="padding:1.25rem;margin-bottom:1rem">
      <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap">
        <h2 style="margin:0">3۔ اپنی تلاوت چیک کریں</h2>
        <span id="micState" class="badge">مائیک بند ہے</span>
      </div>
      <p class="muted">ماڈل تیار ہونے کے بعد مائیک شروع کریں۔ مختصر وقفے پر سسٹم تلاوت کو پہچان کر منتخب آیات سے ملائے گا۔</p>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <button id="startMic" class="btn btn-primary" type="button" disabled>🎙️ تلاوت شروع کریں</button>
        <button id="testMic" class="btn btn-secondary" type="button">مائیک کی اجازت چیک کریں</button>
        <button id="stopMic" class="btn btn-secondary" type="button" disabled>تلاوت روکیں</button>
      </div>
      <div style="margin-top:1rem;padding:1rem;background:var(--surface,#f5f7fa);border-radius:12px">
        <strong>آپ کی پہچانی گئی تلاوت:</strong>
        <p id="liveTranscript" dir="rtl" lang="ar" style="font-size:1.25rem;min-height:2rem">—</p>
      </div>
      <div id="feedback" role="status" aria-live="assertive" style="margin-top:1rem;padding:1rem;border-radius:12px;background:#eef6ff;color:#123b66">
        پہلے سورت اور آیات منتخب کریں، پھر آف لائن ماڈل لوڈ کریں۔
      </div>
      <div id="wordFeedback" dir="rtl" lang="ar" style="margin-top:1rem;padding:1rem;border:1px solid var(--border,#ddd);border-radius:12px;line-height:2.2">
        تلاوت کے بعد لفظی موازنہ یہاں دکھایا جائے گا۔
      </div>
    </div>

    <div class="card" style="padding:1.25rem">
      <h2 style="margin-top:0">رنگوں کی وضاحت</h2>
      <p><span class="mrk-word mrk-word-correct">درست لفظ</span> <span class="mrk-word mrk-word-wrong">ممکنہ غلط لفظ</span> <span class="mrk-word mrk-word-missing">چھوٹا ہوا لفظ</span> <span class="mrk-word mrk-word-extra">اضافی لفظ</span></p>
      <ul>
        <li>یہ معاون ٹول ہے؛ مستند قاری، استاد یا مصحف کا متبادل نہیں۔ ہر نتیجہ قرآنِ مجید سے ضرور چیک کریں۔</li>
        <li>یہ فیچر الفاظ کی شناخت اور موازنہ کرتا ہے؛ تجوید، مخارج، وقف و ابتدا یا قراءت کی صحت کی مکمل تصدیق نہیں کرتا۔</li>
        <li>مائیک کی آواز فون کے براؤزر میں مقامی طور پر پروسیس ہوتی ہے؛ MRK سرور پر ریکارڈ یا اپ لوڈ نہیں ہوتی۔</li>
        <li>پہلی مرتبہ ماڈل ڈاؤن لوڈ کرنے کے لیے انٹرنیٹ درکار ہے۔ قرآن متن بھی پہلی مرتبہ انٹرنیٹ سے آتا ہے اور براؤزر میں محفوظ کیا جاتا ہے۔</li>
      </ul>
    </div>
  </div>
</section>

<style>
  .mrk-word { display:inline-block; margin:.08rem .12rem; padding:0 .25rem; border-radius:5px; }
  .mrk-word-correct { background:#dcfce7; color:#166534; }
  .mrk-word-wrong { background:#fee2e2; color:#991b1b; }
  .mrk-word-missing { background:#ffedd5; color:#9a3412; text-decoration:underline; }
  .mrk-word-extra { background:#fef3c7; color:#92400e; }
</style>
<script src="{{ asset('js/quran-murajaah-offline.js') }}" defer></script>
@endsection
