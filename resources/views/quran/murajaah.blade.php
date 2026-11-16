@extends('layouts.app')

@section('title', 'Hifz-e-Quran Live Muraja’ah | MRK Digital')
@section('description', 'Practice Quran memorisation with live Arabic speech recognition and verse-by-verse review.')
@section('html_lang', 'ur')

@section('content')
<section class="section">
  <div class="container" style="max-width:1000px">
    <div style="text-align:center;margin-bottom:2rem">
      <p class="eyebrow">MRK Quran Learning</p>
      <h1>حفظِ قرآن — لائیو دہرائی</h1>
      <p class="muted">سورت اور آیات منتخب کریں، پھر زبانی تلاوت کریں۔ سسٹم پہچانے گئے الفاظ کو منتخب قرآن متن سے ملانے کی کوشش کرے گا۔</p>
    </div>

    <div class="card" style="padding:1.25rem;margin-bottom:1rem">
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
      <div style="display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap">
        <h2 style="margin:0">لائیو تلاوت</h2>
        <span id="micState" class="badge">مائیک بند ہے</span>
      </div>
      <p class="muted">مائیک شروع کریں اور منتخب آیات زبانی پڑھیں۔ بہتر نتائج کے لیے خاموش جگہ اور واضح آواز استعمال کریں۔</p>
      <div style="display:flex;gap:.75rem;flex-wrap:wrap">
        <button id="startMic" class="btn btn-primary" type="button" disabled>🎙️ تلاوت شروع کریں</button>
        <button id="stopMic" class="btn btn-secondary" type="button" disabled>تلاوت روکیں</button>
      </div>
      <div style="margin-top:1rem;padding:1rem;background:var(--surface,#f5f7fa);border-radius:12px">
        <strong>آپ کی پہچانی گئی آواز:</strong>
        <p id="liveTranscript" dir="rtl" lang="ar" style="font-size:1.25rem;min-height:2rem">—</p>
      </div>
      <div id="feedback" role="status" aria-live="assertive" style="margin-top:1rem;padding:1rem;border-radius:12px;background:#eef6ff;color:#123b66">
        تلاوت شروع کرنے کے لیے مائیک دبائیں۔
      </div>
    </div>

    <div class="card" style="padding:1.25rem">
      <h2 style="margin-top:0">ضروری وضاحت</h2>
      <ul>
        <li>یہ ابتدائی معاون ٹول ہے؛ یہ مستند قاری، استاد یا مصحف کا متبادل نہیں۔</li>
        <li>براؤزر کی آواز پہچاننے کی سہولت الفاظ غلط سن سکتی ہے؛ اس لیے ہر ممکنہ غلطی کو مصحف سے ضرور چیک کریں۔</li>
        <li>مائیک کی آواز MRK سرور پر محفوظ نہیں کی جاتی۔ براؤزر کی Speech Recognition سروس آڈیو کو اپنے سسٹم پر پروسیس کر سکتی ہے؛ اس کا طریقہ براؤزر/ڈیوائس فراہم کنندہ کے تابع ہے۔</li>
        <li>یہ ورژن الفاظ کی شناخت میں مدد دیتا ہے، تجوید، مخارج یا ہر قسم کی قراءت کی تصدیق نہیں کرتا۔</li>
      </ul>
      <p class="muted" style="font-size:.9rem">قرآن متن Quran.com API سے حاصل کیا جاتا ہے۔ انٹرنیٹ درکار ہے۔</p>
    </div>
  </div>
</section>

<script>
(() => {
  const api = 'https://api.quran.com/api/v4';
  const $ = id => document.getElementById(id);
  const surahSelect = $('surahSelect'), fromAyah = $('fromAyah'), toAyah = $('toAyah');
  let verses = [], selected = [], recognition = null, running = false, cursor = 0;

  const normalize = value => (value || '')
    .normalize('NFKD')
    .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
    .replace(/[أإآٱ]/g, 'ا').replace(/ى/g, 'ي').replace(/ؤ/g, 'و').replace(/ئ/g, 'ي')
    .replace(/[^\u0621-\u064A\s]/g, ' ')
    .replace(/\s+/g, ' ').trim();

  function setFeedback(message, kind='info') {
    const el = $('feedback');
    el.textContent = message;
    el.style.background = kind === 'warning' ? '#fff4df' : kind === 'success' ? '#eaf8ef' : '#eef6ff';
    el.style.color = kind === 'warning' ? '#754600' : kind === 'success' ? '#14532d' : '#123b66';
  }

  async function getJson(url) {
    const response = await fetch(url, {headers:{'Accept':'application/json'}});
    if (!response.ok) throw new Error('قرآن متن لوڈ نہیں ہو سکا (' + response.status + ')');
    return response.json();
  }

  async function loadSurahs() {
    try {
      const data = await getJson(api + '/chapters?language=ar');
      surahSelect.innerHTML = '<option value="">سورت منتخب کریں</option>';
      data.chapters.forEach(ch => {
        const option = document.createElement('option');
        option.value = ch.id;
        option.textContent = ch.id + '. ' + ch.name_arabic + ' — ' + ch.name_simple;
        surahSelect.appendChild(option);
      });
      $('loadStatus').textContent = 'سورت منتخب کریں۔';
    } catch (error) {
      $('loadStatus').textContent = error.message + '۔ صفحہ ریفریش کرکے دوبارہ کوشش کریں۔';
    }
  }

  async function loadSurah(id) {
    $('loadStatus').textContent = 'سورت کی آیات لوڈ ہو رہی ہیں…';
    $('startMic').disabled = true;
    try {
      let page = 1, all = [], totalPages = 1;
      do {
        const data = await getJson(api + '/verses/by_chapter/' + id + '?language=ar&words=false&fields=text_uthmani&per_page=50&page=' + page);
        all = all.concat(data.verses || []);
        totalPages = data.pagination ? data.pagination.total_pages : 1;
        page++;
      } while (page <= totalPages);
      verses = all;
      [fromAyah, toAyah].forEach(select => {
        select.innerHTML = '';
        verses.forEach(v => {
          const option = document.createElement('option');
          option.value = v.verse_number;
          option.textContent = v.verse_number;
          select.appendChild(option);
        });
        select.disabled = false;
      });
      toAyah.value = String(verses.length);
      $('loadStatus').textContent = verses.length + ' آیات لوڈ ہوگئیں۔ اب آیات کی حد منتخب کریں۔';
      renderRange();
    } catch (error) {
      $('loadStatus').textContent = error.message;
    }
  }

  function renderRange() {
    const from = Number(fromAyah.value || 1), to = Number(toAyah.value || verses.length);
    if (!verses.length || from > to) {
      $('loadStatus').textContent = 'آیات کی درست حد منتخب کریں۔';
      return;
    }
    selected = verses.filter(v => v.verse_number >= from && v.verse_number <= to);
    $('quranText').textContent = selected.map(v => v.text_uthmani + ' ۝' + v.verse_number).join(' ');
    cursor = 0;
    $('position').textContent = 'سورت ' + surahSelect.value + '، آیت ' + from + ' تا ' + to;
    $('startMic').disabled = !window.SpeechRecognition && !window.webkitSpeechRecognition;
    $('loadStatus').textContent = selected.length + ' آیات منتخب ہیں۔';
    $('liveTranscript').textContent = '—';
    setFeedback('اب مائیک شروع کرکے منتخب آیات کی تلاوت کریں۔');
  }

  function checkPhrase(phrase) {
    const spoken = normalize(phrase).split(' ').filter(Boolean);
    if (spoken.length < 2 || !selected.length) return;
    const expected = normalize(selected.map(v => v.text_uthmani).join(' ')).split(' ').filter(Boolean);
    const start = Math.max(0, cursor - 2);
    const window = expected.slice(start, Math.min(expected.length, cursor + spoken.length + 8));
    let foundAt = -1;
    outer: for (let i=0; i<=window.length-spoken.length; i++) {
      for (let j=0; j<spoken.length; j++) if (window[i+j] !== spoken[j]) continue outer;
      foundAt = start + i;
      break;
    }
    if (foundAt >= 0) {
      cursor = Math.max(cursor, foundAt + spoken.length);
      setFeedback('اچھی پیش رفت — پہچانے گئے الفاظ منتخب متن میں مل گئے۔ اگلا حصہ جاری رکھیں۔', 'success');
      $('position').textContent = 'متن میں پیش رفت: ' + cursor + ' / ' + expected.length + ' الفاظ';
      if (cursor >= expected.length) setFeedback('ماشاءاللہ! منتخب آیات مکمل ہوگئیں۔ استاد/مصحف سے آخری تصدیق ضرور کریں۔', 'success');
    } else {
      setFeedback('ممکنہ فرق یا آواز کی غلط شناخت: اس حصے کو مصحف سے چیک کریں اور ضرورت ہو تو پچھلے الفاظ دوبارہ پڑھیں۔', 'warning');
    }
  }

  async function startRecognition() {
    const Speech = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!window.isSecureContext) {
      setFeedback('مائیک کے لیے محفوظ HTTPS ویب سائٹ ضروری ہے۔ براہِ کرم اصل https لنک استعمال کریں۔', 'warning');
      return;
    }
    if (!Speech) {
      setFeedback('اس براؤزر میں Speech Recognition دستیاب نہیں۔ تازہ Google Chrome یا کسی سپورٹڈ ڈیوائس پر کوشش کریں۔', 'warning');
      return;
    }
    if (!selected.length) {
      setFeedback('پہلے سورت اور آیات منتخب کرکے لوڈ کریں۔', 'warning');
      return;
    }
    // Start SpeechRecognition directly from the user's button click.
    // Chrome handles the microphone permission prompt for the Web Speech API.
    // Avoid a separate getUserMedia() preflight: it can fail independently and
    // prevent SpeechRecognition from showing its own permission prompt.

    if (recognition) { try { recognition.abort(); } catch (_) {} }
    recognition = new Speech();
    recognition.lang = 'ar-SA';
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.onstart = () => {
      running = true;
      $('micState').textContent = 'مائیک فعال ہے';
      $('startMic').disabled = true;
      $('stopMic').disabled = false;
      setFeedback('تلاوت سننے کی کوشش جاری ہے۔ ممکنہ فرق کو مصحف سے چیک کریں۔');
    };
    recognition.onresult = event => {
      let interim = '', finalText = '';
      for (let i=event.resultIndex; i<event.results.length; i++) {
        const transcript = event.results[i][0].transcript;
        if (event.results[i].isFinal) finalText += transcript + ' ';
        else interim += transcript;
      }
      $('liveTranscript').textContent = (finalText + interim).trim() || '—';
      if (finalText.trim()) checkPhrase(finalText.trim());
    };
    recognition.onerror = event => {
      const messages = {
        'not-allowed': 'مائیکروفون کی اجازت Speech Recognition سروس نے مسترد کی۔ اگر Android اور Chrome دونوں میں Microphone Allow ہے تو Chrome/Google app اپ ڈیٹ کریں، فون ری اسٹارٹ کریں، یا کسی دوسرے سپورٹڈ براؤزر/ڈیوائس پر آزمائیں۔',
        'service-not-allowed': 'Chrome کی Speech Recognition سروس دستیاب یا مجاز نہیں۔ Chrome اور Google app اپ ڈیٹ کریں؛ ممکن ہے اس ڈیوائس پر یہ سہولت سپورٹ نہ ہو۔',
        'audio-capture': 'مائیکروفون دستیاب نہیں یا دوسری ایپ استعمال کر رہی ہے۔',
        'network': 'آواز کی شناخت کے لیے انٹرنیٹ/Google speech service سے رابطہ نہیں ہو سکا۔',
        'no-speech': 'آواز موصول نہیں ہوئی۔ مائیک کے قریب واضح آواز سے دوبارہ پڑھیں۔',
        'aborted': 'آواز کی شناخت روک دی گئی۔'
      };
      setFeedback(messages[event.error] || ('آواز کی شناخت میں مسئلہ: ' + event.error + '۔'), 'warning');
    };
    recognition.onend = () => {
      running = false;
      $('micState').textContent = 'مائیک بند ہے';
      $('startMic').disabled = false;
      $('stopMic').disabled = true;
    };
    try {
      recognition.start();
    } catch (error) {
      $('startMic').disabled = false;
      setFeedback('مائیک شروع نہیں ہو سکا۔ صفحہ ریفریش کرکے دوبارہ کوشش کریں۔', 'warning');
    }
  }

  $('surahSelect').addEventListener('change', e => { if (e.target.value) loadSurah(e.target.value); });
  $('loadRange').addEventListener('click', renderRange);
  $('resetProgress').addEventListener('click', () => { cursor = 0; renderRange(); });
  $('startMic').addEventListener('click', startRecognition);
  $('stopMic').addEventListener('click', () => { if (recognition && running) recognition.stop(); });
  loadSurahs();
})();
</script>
@endsection
