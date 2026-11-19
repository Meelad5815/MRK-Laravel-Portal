(() => {
  'use strict';

  const API = 'https://api.quran.com/api/v4';
  const $ = (id) => document.getElementById(id);
  const surahSelect = $('surahSelect');
  const fromAyah = $('fromAyah');
  const toAyah = $('toAyah');
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

  let allVerses = [];
  let selectedVerses = [];
  let expectedWords = [];
  let cursor = 0;
  let recognition = null;
  let listening = false;
  let restarting = false;
  let processedFinalTranscript = '';

  function status(message, kind = 'info') {
    const el = $('feedback');
    if (!el) return;
    el.textContent = message;
    el.dataset.kind = kind;
    el.style.background = kind === 'success' ? '#eaf8ef' : kind === 'warning' ? '#fff4df' : '#eef6ff';
    el.style.color = kind === 'success' ? '#14532d' : kind === 'warning' ? '#754600' : '#123b66';
  }

  function setMicState(message) {
    const el = $('micState');
    if (el) el.textContent = message;
  }

  function normalize(value) {
    return (value || '')
      .normalize('NFKD')
      .replace(/[\u064B-\u065F\u0670\u0640]/g, '')
      .replace(/[أإآٱ]/g, 'ا')
      .replace(/ى/g, 'ي')
      .replace(/ؤ/g, 'و')
      .replace(/ئ/g, 'ي')
      .replace(/[^\u0621-\u064A\s]/g, ' ')
      .replace(/\s+/g, ' ')
      .trim();
  }

  function wordsOf(text) {
    return normalize(text).split(' ').filter(Boolean);
  }

  async function getJson(url, cacheKey) {
    try {
      const response = await fetch(url, { headers: { Accept: 'application/json' } });
      if (!response.ok) throw new Error('HTTP ' + response.status);
      const data = await response.json();
      try { localStorage.setItem(cacheKey, JSON.stringify(data)); } catch (_) {}
      return data;
    } catch (error) {
      const cached = localStorage.getItem(cacheKey);
      if (cached) {
        try { return JSON.parse(cached); } catch (_) {}
      }
      throw new Error('قرآن متن لوڈ نہیں ہو سکا۔ انٹرنیٹ کنکشن چیک کریں۔');
    }
  }

  async function loadSurahs() {
    try {
      const data = await getJson(API + '/chapters?language=ar', 'mrk_quran_chapters_v2');
      surahSelect.innerHTML = '<option value="">سورت منتخب کریں</option>';
      data.chapters.forEach((chapter) => {
        const option = document.createElement('option');
        option.value = chapter.id;
        option.textContent = chapter.id + '. ' + chapter.name_arabic + ' — ' + chapter.name_simple;
        surahSelect.appendChild(option);
      });
      $('loadStatus').textContent = 'آن لائن سورت منتخب کریں۔';
    } catch (error) {
      $('loadStatus').textContent = error.message;
    }
  }

  async function loadSurah(id) {
    $('loadStatus').textContent = 'سورت کی آیات لوڈ ہو رہی ہیں…';
    try {
      const cacheKey = 'mrk_quran_surah_v2_' + id;
      let all = [];
      const cached = localStorage.getItem(cacheKey);
      if (cached) {
        try { all = JSON.parse(cached); } catch (_) { all = []; }
      }
      if (!all.length) {
        let page = 1;
        let totalPages = 1;
        do {
          const data = await getJson(
            API + '/verses/by_chapter/' + id + '?language=ar&words=false&fields=text_uthmani&per_page=50&page=' + page,
            cacheKey + '_page_' + page
          );
          all = all.concat(data.verses || []);
          totalPages = data.pagination ? data.pagination.total_pages : 1;
          page += 1;
        } while (page <= totalPages);
        try { localStorage.setItem(cacheKey, JSON.stringify(all)); } catch (_) {}
      }
      allVerses = all;
      [fromAyah, toAyah].forEach((select) => {
        select.innerHTML = '';
        allVerses.forEach((verse) => {
          const option = document.createElement('option');
          option.value = verse.verse_number;
          option.textContent = verse.verse_number;
          select.appendChild(option);
        });
        select.disabled = false;
      });
      toAyah.value = String(allVerses.length);
      $('loadStatus').textContent = allVerses.length + ' آیات دستیاب ہیں۔';
      renderRange();
    } catch (error) {
      $('loadStatus').textContent = error.message;
    }
  }

  function renderRange() {
    const from = Number(fromAyah.value || 1);
    const to = Number(toAyah.value || allVerses.length);
    if (!allVerses.length || from > to) {
      $('loadStatus').textContent = 'آیات کی درست حد منتخب کریں۔';
      return;
    }
    selectedVerses = allVerses.filter((verse) => verse.verse_number >= from && verse.verse_number <= to);
    $('quranText').textContent = selectedVerses.map((verse) => verse.text_uthmani + ' ۝' + verse.verse_number).join(' ');
    expectedWords = wordsOf(selectedVerses.map((verse) => verse.text_uthmani).join(' '));
    cursor = 0;
    processedFinalTranscript = '';
    $('position').textContent = 'منتخب آیات: ' + from + ' تا ' + to + ' — کل ' + expectedWords.length + ' الفاظ';
    $('liveTranscript').textContent = '—';
    $('wordFeedback').textContent = 'تلاوت کے بعد لفظی موازنہ یہاں دکھایا جائے گا۔';
    updateMicButton();
    status('آیات منتخب ہیں۔ مائیک شروع کریں۔');
  }

  function levenshteinAlignment(expected, spoken) {
    const rows = expected.length + 1;
    const cols = spoken.length + 1;
    const dp = Array.from({ length: rows }, () => new Array(cols).fill(0));
    for (let i = 0; i < rows; i++) dp[i][0] = i;
    for (let j = 0; j < cols; j++) dp[0][j] = j;
    for (let i = 1; i < rows; i++) {
      for (let j = 1; j < cols; j++) {
        const cost = expected[i - 1] === spoken[j - 1] ? 0 : 1;
        dp[i][j] = Math.min(dp[i - 1][j] + 1, dp[i][j - 1] + 1, dp[i - 1][j - 1] + cost);
      }
    }
    const ops = [];
    let i = expected.length;
    let j = spoken.length;
    while (i > 0 || j > 0) {
      if (i > 0 && j > 0 && dp[i][j] === dp[i - 1][j - 1] + (expected[i - 1] === spoken[j - 1] ? 0 : 1)) {
        ops.unshift({ type: expected[i - 1] === spoken[j - 1] ? 'match' : 'wrong', expected: expected[i - 1], spoken: spoken[j - 1] });
        i--; j--;
      } else if (i > 0 && dp[i][j] === dp[i - 1][j] + 1) {
        ops.unshift({ type: 'missing', expected: expected[i - 1], spoken: '' });
        i--;
      } else {
        ops.unshift({ type: 'extra', expected: '', spoken: spoken[j - 1] });
        j--;
      }
    }
    return { cost: dp[expected.length][spoken.length], ops, length: spoken.length };
  }

  function findBestAlignment(spoken) {
    const lower = Math.max(0, cursor - 2);
    const upper = Math.min(expectedWords.length - 1, cursor + 7);
    let best = null;
    for (let start = lower; start <= upper; start++) {
      const minLength = Math.max(1, spoken.length - 2);
      const maxLength = Math.min(expectedWords.length - start, spoken.length + 3);
      for (let length = minLength; length <= maxLength; length++) {
        const candidate = expectedWords.slice(start, start + length);
        const aligned = levenshteinAlignment(candidate, spoken);
        const score = aligned.cost / Math.max(candidate.length, spoken.length, 1) + Math.abs(start - cursor) * 0.035;
        if (!best || score < best.score) best = { ...aligned, start, length, score };
      }
    }
    return best;
  }

  function appendToken(parent, text, type) {
    const span = document.createElement('span');
    span.className = 'mrk-word mrk-word-' + type;
    span.textContent = text;
    parent.appendChild(span);
  }

  function showAlignment(alignment) {
    const box = $('wordFeedback');
    box.textContent = '';
    const expectedLine = document.createElement('div');
    const spokenLine = document.createElement('div');
    expectedLine.appendChild(Object.assign(document.createElement('strong'), { textContent: 'قرآن کے الفاظ: ' }));
    spokenLine.appendChild(Object.assign(document.createElement('strong'), { textContent: 'پہچانی گئی تلاوت: ' }));
    alignment.ops.forEach((op) => {
      if (op.expected) appendToken(expectedLine, op.expected + ' ', op.type === 'match' ? 'correct' : op.type === 'wrong' ? 'wrong' : 'missing');
      if (op.spoken) appendToken(spokenLine, op.spoken + ' ', op.type === 'match' ? 'correct' : op.type === 'wrong' ? 'wrong' : 'extra');
    });
    box.appendChild(expectedLine);
    box.appendChild(spokenLine);
    const summary = document.createElement('p');
    const correct = alignment.ops.filter((op) => op.type === 'match').length;
    const missing = alignment.ops.filter((op) => op.type === 'missing').length;
    const wrong = alignment.ops.filter((op) => op.type === 'wrong').length;
    const extra = alignment.ops.filter((op) => op.type === 'extra').length;
    summary.textContent = 'درست: ' + correct + '، ممکنہ غلط: ' + wrong + '، چھوٹے: ' + missing + '، اضافی: ' + extra + '۔';
    box.appendChild(summary);
  }

  function compareRecitation(transcript) {
    const spoken = wordsOf(transcript);
    if (!spoken.length || !expectedWords.length) return;
    const alignment = findBestAlignment(spoken);
    if (!alignment) return;
    showAlignment(alignment);
    const matches = alignment.ops.filter((op) => op.type === 'match').length;
    const ratio = matches / Math.max(spoken.length, alignment.length, 1);
    if (ratio >= 0.45) {
      cursor = Math.max(cursor, alignment.start + alignment.length);
      $('position').textContent = 'متن میں پیش رفت: ' + Math.min(cursor, expectedWords.length) + ' / ' + expectedWords.length + ' الفاظ';
      status('تلاوت کی آن لائن شناخت مکمل ہوئی۔ رنگوں کو دیکھ کر مصحف سے تصدیق کریں۔', matches === spoken.length ? 'success' : 'warning');
    } else {
      status('پہچانی گئی آواز منتخب آیات سے واضح طور پر نہیں ملی۔ دوبارہ پڑھیں۔', 'warning');
    }
  }

  function setupRecognition() {
    if (!SpeechRecognition) return null;
    const engine = new SpeechRecognition();
    engine.lang = 'ar-SA';
    engine.continuous = true;
    engine.interimResults = true;
    engine.maxAlternatives = 1;

    engine.onstart = () => {
      listening = true;
      setMicState('مائیک فعال ہے — آن لائن شناخت');
      $('startMic').disabled = true;
      $('stopMic').disabled = false;
      status('تلاوت شروع کریں۔ آواز براؤزر کی آن لائن شناخت سروس کو بھیجی جا سکتی ہے۔', 'success');
    };

    engine.onresult = (event) => {
      let interim = '';
      let finalText = '';
      for (let i = event.resultIndex; i < event.results.length; i++) {
        const text = event.results[i][0].transcript;
        if (event.results[i].isFinal) finalText += text + ' ';
        else interim += text;
      }
      const display = (finalText + interim).trim();
      if (display) $('liveTranscript').textContent = display;
      if (finalText.trim() && finalText.trim() !== processedFinalTranscript) {
        processedFinalTranscript = finalText.trim();
        compareRecitation(finalText);
      }
    };

    engine.onerror = (event) => {
      const messages = {
        'not-allowed': 'مائیک اجازت نہیں ملی۔ براؤزر کی سائٹ ترتیبات میں مائیک اجازت دیں۔',
        'audio-capture': 'مائیک نہیں ملا یا کسی دوسری ایپ میں استعمال ہو رہا ہے۔',
        'no-speech': 'کچھ آواز نہیں سنی گئی۔ قریب سے واضح آواز میں دوبارہ پڑھیں۔',
        'network': 'آن لائن شناخت سروس سے رابطہ نہیں ہو سکا۔ انٹرنیٹ چیک کریں۔',
        'aborted': 'تلاوت کی شناخت روک دی گئی۔'
      };
      if (event.error !== 'aborted' || listening) {
        status(messages[event.error] || ('مائیک شناخت کی خرابی: ' + event.error), 'warning');
      }
    };

    engine.onend = () => {
      if (listening && !restarting) {
        restarting = true;
        window.setTimeout(() => {
          restarting = false;
          if (listening) {
            try { engine.start(); } catch (_) {}
          }
        }, 250);
      }
    };

    return engine;
  }

  function updateMicButton() {
    $('startMic').disabled = listening || !SpeechRecognition || !selectedVerses.length;
  }

  async function testMicrophone() {
    if (!window.isSecureContext) {
      status('مائیک کے لیے HTTPS یا localhost درکار ہے۔', 'warning');
      return;
    }
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      status('یہ براؤزر مائیک اجازت فراہم نہیں کرتا۔ Chrome یا Edge استعمال کریں۔', 'warning');
      return;
    }
    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true }
      });
      stream.getTracks().forEach((track) => track.stop());
      status('مائیک کی اجازت درست ہے۔ اب تلاوت شروع کریں۔', 'success');
    } catch (error) {
      status('مائیک کی اجازت یا دستیابی کا مسئلہ: ' + error.name + '۔ Chrome/Site Settings میں مائیک Allow کریں۔', 'warning');
    }
  }

  async function startMicrophone() {
    if (!SpeechRecognition) {
      status('یہ براؤزر آن لائن آواز شناخت معاونت نہیں کرتا۔ Chrome یا Edge استعمال کریں۔', 'warning');
      return;
    }
    if (!selectedVerses.length) {
      status('پہلے س��رت اور آیات منتخب کریں۔', 'warning');
      return;
    }
    if (!window.isSecureContext) {
      status('مائیک کے لیے HTTPS یا localhost درکار ہے۔', 'warning');
      return;
    }
    try {
      const stream = await navigator.mediaDevices.getUserMedia({
        audio: { echoCancellation: true, noiseSuppression: true, autoGainControl: true }
      });
      stream.getTracks().forEach((track) => track.stop());
      recognition = recognition || setupRecognition();
      processedFinalTranscript = '';
      recognition.start();
    } catch (error) {
      listening = false;
      status('مائیک شروع نہیں ہو سکا: ' + error.name + '۔ Chrome/Site Settings میں مائیک اجازت Allow کریں۔', 'warning');
      updateMicButton();
    }
  }

  function stopMicrophone() {
    listening = false;
    if (recognition) {
      try { recognition.stop(); } catch (_) {}
    }
    setMicState('مائیک بند ہے');
    $('startMic').disabled = !selectedVerses.length || !SpeechRecognition;
    $('stopMic').disabled = true;
    status('تلاوت روک دی گئی۔');
  }

  surahSelect.addEventListener('change', (event) => {
    if (event.target.value) loadSurah(event.target.value);
  });
  $('loadRange').addEventListener('click', renderRange);
  $('resetProgress').addEventListener('click', () => {
    cursor = 0;
    processedFinalTranscript = '';
    renderRange();
  });
  $('testMic').addEventListener('click', testMicrophone);
  $('startMic').addEventListener('click', startMicrophone);
  $('stopMic').addEventListener('click', stopMicrophone);

  $('startMic').disabled = true;
  $('stopMic').disabled = true;
  if (!SpeechRecognition) {
    status('یہ براؤزر آن لائن آواز شناخت معاونت نہیں کرتا۔ Chrome یا Edge استعمال کریں۔', 'warning');
  }
  loadSurahs();
})();
