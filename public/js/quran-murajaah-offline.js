(() => {
  'use strict';

  const API = 'https://api.quran.com/api/v4';
  const ASR_BASE = '/quran-asr/';
  const $ = (id) => document.getElementById(id);

  const surahSelect = $('surahSelect');
  const fromAyah = $('fromAyah');
  const toAyah = $('toAyah');

  let allVerses = [];
  let selectedVerses = [];
  let expectedWords = [];
  let cursor = 0;
  let recognizer = null;
  let engineLoading = false;
  let mediaStream = null;
  let audioContext = null;
  let sourceNode = null;
  let processorNode = null;
  let muteNode = null;
  let sourceRate = 16000;
  let segmentChunks = [];
  let segmentSamples = 0;
  let speechStarted = false;
  let silenceSamples = 0;
  let decoding = false;

  const SAMPLE_RATE = 16000;
  const SILENCE_RMS = 0.008;
  const MAX_SEGMENT_SAMPLES = SAMPLE_RATE * 10;
  const SILENCE_END_SAMPLES = Math.round(SAMPLE_RATE * 0.85);
  const MIN_SEGMENT_SAMPLES = Math.round(SAMPLE_RATE * 0.8);

  function status(message, kind = 'info') {
    const el = $('feedback');
    if (!el) return;
    el.textContent = message;
    el.dataset.kind = kind;
    el.style.background = kind === 'success' ? '#eaf8ef' : kind === 'warning' ? '#fff4df' : '#eef6ff';
    el.style.color = kind === 'success' ? '#14532d' : kind === 'warning' ? '#754600' : '#123b66';
  }

  function setModelStatus(message) {
    const el = $('modelStatus');
    if (el) el.textContent = message;
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
      const data = await getJson(API + '/chapters?language=ar', 'mrk_quran_chapters_v1');
      surahSelect.innerHTML = '<option value="">سورت منتخب کریں</option>';
      data.chapters.forEach((chapter) => {
        const option = document.createElement('option');
        option.value = chapter.id;
        option.textContent = chapter.id + '. ' + chapter.name_arabic + ' — ' + chapter.name_simple;
        surahSelect.appendChild(option);
      });
      $('loadStatus').textContent = 'سورت منتخب کریں۔ محفوظ شدہ سورتیں آف لائن بھی کھل سکتی ہیں۔';
    } catch (error) {
      $('loadStatus').textContent = error.message;
    }
  }

  async function loadSurah(id) {
    $('loadStatus').textContent = 'سورت کی آیات لوڈ ہو رہی ہیں…';
    $('startMic').disabled = true;
    try {
      const cacheKey = 'mrk_quran_surah_v1_' + id;
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
    $('position').textContent = 'منتخب آیات: ' + from + ' تا ' + to + ' — کل ' + expectedWords.length + ' الفاظ';
    $('liveTranscript').textContent = '—';
    $('wordFeedback').textContent = 'تلاوت کے بعد لفظی موازنہ یہاں دکھایا جائے گا۔';
    $('startMic').disabled = !recognizer;
    status('آیات منتخب ہیں۔ آف لائن ماڈل تیار ہونے کے بعد تلاوت شروع کریں۔');
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
        dp[i][j] = Math.min(
          dp[i - 1][j] + 1,
          dp[i][j - 1] + 1,
          dp[i - 1][j - 1] + cost
        );
      }
    }
    const ops = [];
    let i = expected.length;
    let j = spoken.length;
    while (i > 0 || j > 0) {
      if (i > 0 && j > 0 && dp[i][j] === dp[i - 1][j - 1] + (expected[i - 1] === spoken[j - 1] ? 0 : 1)) {
        ops.unshift({
          type: expected[i - 1] === spoken[j - 1] ? 'match' : 'wrong',
          expected: expected[i - 1],
          spoken: spoken[j - 1]
        });
        i--; j--;
      } else if (i > 0 && dp[i][j] === dp[i - 1][j] + 1) {
        ops.unshift({ type: 'missing', expected: expected[i - 1], spoken: '' });
        i--;
      } else {
        ops.unshift({ type: 'extra', expected: '', spoken: spoken[j - 1] });
        j--;
      }
    }
    return { cost: dp[expected.length][spoken.length], ops };
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
    const expectedLabel = document.createElement('strong');
    expectedLabel.textContent = 'قرآن کے الفاظ: ';
    expectedLine.appendChild(expectedLabel);
    const spokenLabel = document.createElement('strong');
    spokenLabel.textContent = 'پہچانی گئی تلاوت: ';
    spokenLine.appendChild(spokenLabel);

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
    if (!alignment) {
      status('تلاوت کا موازنہ نہیں ہو سکا۔', 'warning');
      return;
    }
    showAlignment(alignment);
    const matches = alignment.ops.filter((op) => op.type === 'match').length;
    const ratio = matches / Math.max(spoken.length, alignment.length, 1);
    if (ratio >= 0.45) {
      cursor = Math.max(cursor, alignment.start + alignment.length);
      $('position').textContent = 'متن میں پیش رفت: ' + Math.min(cursor, expectedWords.length) + ' / ' + expectedWords.length + ' الفاظ';
      status('تلاوت کا موازنہ مکمل۔ رنگوں کو دیکھ کر مصحف سے تصدیق کریں۔', matches === spoken.length ? 'success' : 'warning');
    } else {
      status('پہچانی گئی آواز منتخب آیات سے واضح طور پر نہیں ملی۔ دوبارہ پڑھیں یا مصحف سے جانچیں۔', 'warning');
    }
  }

  function loadScript(src) {
    return new Promise((resolve, reject) => {
      const script = document.createElement('script');
      script.src = src;
      script.async = false;
      script.onload = resolve;
      script.onerror = () => reject(new Error('آف لائن انجن کی فائل لوڈ نہیں ہوئی: ' + src));
      document.head.appendChild(script);
    });
  }

  async function loadOfflineModel() {
    if (recognizer || engineLoading) return;
    engineLoading = true;
    $('loadModel').disabled = true;
    setModelStatus('آف لائن انجن لوڈ ہو رہا ہے۔ پہلی مرتبہ تقریباً 50 MB یا اس سے زیادہ فائلیں ڈاؤن لوڈ ہوں گی۔');
    status('آف لائن ماڈل تیار کیا جا رہا ہے۔ اس دوران صفحہ بند نہ کریں۔');
    try {
      if ('serviceWorker' in navigator) {
        await navigator.serviceWorker.register('/sw-quran.js', { scope: '/' });
        await navigator.serviceWorker.ready;
      }
      await loadScript(ASR_BASE + 'sherpa-onnx-asr.js');

      window.Module = {
        locateFile: (path) => ASR_BASE + path,
        setStatus: (message) => {
          if (message) setModelStatus(message);
        },
        onAbort: (reason) => {
          setModelStatus('آف لائن انجن لوڈ نہیں ہو سکا: ' + String(reason));
          status('آف لائن انجن لوڈ نہیں ہو سکا۔ انٹرنیٹ اور فون کی خالی اسٹوریج چیک کرکے دوبارہ کوشش کریں۔', 'warning');
          $('loadModel').disabled = false;
        },
        onRuntimeInitialized: () => {
          try {
            const config = {
              featConfig: { sampleRate: SAMPLE_RATE, featureDim: 80 },
              modelConfig: {
                zipformerCtc: { model: './assets/zipformer-ctc.onnx' },
                tokens: './assets/tokens.txt',
                numThreads: 2,
                provider: 'cpu',
                debug: 0
              },
              decodingMethod: 'greedy_search',
              maxActivePaths: 4
            };
            recognizer = new OfflineRecognizer(config, window.Module);
            setModelStatus('آف لائن قرآن ماڈل تیار ہے۔ اب انٹرنیٹ بند کرکے بھی تلاوت چیک کی جا سکتی ہے، بشرطیکہ فائلیں کیش ہو چکی ہوں۔');
            $('loadModel').textContent = 'ماڈل تیار ہے';
            $('startMic').disabled = selectedVerses.length === 0;
            status('آف لائن ماڈل تیار ہے۔ آواز فون پر ہی پروسیس ہوگی۔', 'success');
          } catch (error) {
            setModelStatus('ماڈل شروع نہیں ہو سکا: ' + error.message);
            status('آف لائن ماڈل شروع نہیں ہو سکا۔ دوبارہ کوشش کریں۔', 'warning');
            $('loadModel').disabled = false;
          }
        }
      };
      await loadScript(ASR_BASE + 'sherpa-onnx-wasm-main-vad-asr.js');
    } catch (error) {
      setModelStatus(error.message);
      status(error.message, 'warning');
      $('loadModel').disabled = false;
    } finally {
      engineLoading = false;
    }
  }

  function downsample(input, inputRate) {
    if (inputRate === SAMPLE_RATE) return new Float32Array(input);
    const ratio = inputRate / SAMPLE_RATE;
    const length = Math.round(input.length / ratio);
    const output = new Float32Array(length);
    for (let i = 0; i < length; i++) {
      const start = Math.floor(i * ratio);
      const end = Math.min(input.length, Math.floor((i + 1) * ratio));
      let sum = 0;
      for (let j = start; j < end; j++) sum += input[j];
      output[i] = sum / Math.max(1, end - start);
    }
    return output;
  }

  function rms(samples) {
    let sum = 0;
    for (let i = 0; i < samples.length; i++) sum += samples[i] * samples[i];
    return Math.sqrt(sum / Math.max(1, samples.length));
  }

  function joinChunks(chunks, total) {
    const result = new Float32Array(total);
    let offset = 0;
    chunks.forEach((chunk) => {
      result.set(chunk, offset);
      offset += chunk.length;
    });
    return result;
  }

  async function decodeCurrentSegment() {
    if (decoding || !recognizer || segmentSamples < MIN_SEGMENT_SAMPLES) {
      segmentChunks = [];
      segmentSamples = 0;
      speechStarted = false;
      silenceSamples = 0;
      return;
    }
    decoding = true;
    const samples = joinChunks(segmentChunks, segmentSamples);
    segmentChunks = [];
    segmentSamples = 0;
    speechStarted = false;
    silenceSamples = 0;
    try {
      setMicState('تلاوت پروسیس ہو رہی ہے');
      const stream = recognizer.createStream();
      stream.acceptWaveform(SAMPLE_RATE, samples);
      recognizer.decode(stream);
      const result = recognizer.getResult(stream);
      stream.free();
      const text = (result && result.text ? result.text : '').trim();
      if (text) {
        $('liveTranscript').textContent = text;
        compareRecitation(text);
      } else {
        status('اس حصے میں الفاظ پہچانے نہیں گئے۔ واضح آواز سے دوبارہ پڑھیں۔', 'warning');
      }
    } catch (error) {
      status('آواز کی مقامی شناخت میں مسئلہ: ' + error.message, 'warning');
    } finally {
      decoding = false;
      if (mediaStream) setMicState('مائیک فعال ہے — صرف فون پر شناخت');
    }
  }

  function handleAudio(event) {
    if (!recognizer || !mediaStream) return;
    const raw = event.inputBuffer.getChannelData(0);
    const samples = downsample(raw, sourceRate);
    const level = rms(samples);
    if (level >= SILENCE_RMS) {
      speechStarted = true;
      silenceSamples = 0;
      segmentChunks.push(samples);
      segmentSamples += samples.length;
      if (segmentSamples >= MAX_SEGMENT_SAMPLES) decodeCurrentSegment();
    } else if (speechStarted) {
      segmentChunks.push(samples);
      segmentSamples += samples.length;
      silenceSamples += samples.length;
      if (silenceSamples >= SILENCE_END_SAMPLES || segmentSamples >= MAX_SEGMENT_SAMPLES) decodeCurrentSegment();
    }
  }

  async function testMicrophone() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      status('اس براؤزر میں مائیک ٹیسٹ دستیاب نہیں۔ HTTPS اور تازہ Chrome استعمال کریں۔', 'warning');
      return;
    }
    $('testMic').disabled = true;
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
      stream.getTracks().forEach((track) => track.stop());
      status('مائیک کی اجازت درست ہے۔ آواز کی شناخت کے لیے پہلے آف لائن ماڈل لوڈ کریں۔', 'success');
    } catch (error) {
      status('مائیک کی اجازت یا دستیابی کا مسئلہ: ' + error.name + '۔ Chrome Site Settings اور Android App Permissions چیک کریں۔', 'warning');
    } finally {
      $('testMic').disabled = false;
    }
  }

  async function startMicrophone() {
    if (!recognizer) {
      status('پہلے مفت آف لائن ماڈل لوڈ کریں۔', 'warning');
      return;
    }
    if (!selectedVerses.length) {
      status('پہلے سورت اور آیات منتخب کریں۔', 'warning');
      return;
    }
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      status('اس براؤزر میں مائیک دستیاب نہیں۔ HTTPS پر تازہ Chrome استعمال کریں۔', 'warning');
      return;
    }
    try {
      mediaStream = await navigator.mediaDevices.getUserMedia({
        audio: { channelCount: 1, echoCancellation: false, noiseSuppression: false, autoGainControl: false }
      });
      audioContext = new (window.AudioContext || window.webkitAudioContext)({ sampleRate: SAMPLE_RATE });
      if (audioContext.state === 'suspended') await audioContext.resume();
      sourceRate = audioContext.sampleRate;
      sourceNode = audioContext.createMediaStreamSource(mediaStream);
      processorNode = audioContext.createScriptProcessor(4096, 1, 1);
      muteNode = audioContext.createGain();
      muteNode.gain.value = 0;
      processorNode.onaudioprocess = handleAudio;
      sourceNode.connect(processorNode);
      processorNode.connect(muteNode);
      muteNode.connect(audioContext.destination);
      $('startMic').disabled = true;
      $('stopMic').disabled = false;
      $('testMic').disabled = true;
      setMicState('مائیک فعال ہے — صرف فون پر شناخت');
      status('تلاوت شروع کریں۔ وقفے پر الفاظ خودکار طور پر پہچانے اور ملائے جائیں گے۔', 'success');
    } catch (error) {
      mediaStream = null;
      status('مائیک شروع نہیں ہو سکا: ' + error.name + '۔ Chrome اور Android میں مائیک اجازت چیک کریں۔', 'warning');
    }
  }

  async function stopMicrophone() {
    if (segmentSamples >= MIN_SEGMENT_SAMPLES) await decodeCurrentSegment();
    if (processorNode) {
      processorNode.onaudioprocess = null;
      try { processorNode.disconnect(); } catch (_) {}
    }
    if (sourceNode) {
      try { sourceNode.disconnect(); } catch (_) {}
    }
    if (muteNode) {
      try { muteNode.disconnect(); } catch (_) {}
    }
    if (mediaStream) mediaStream.getTracks().forEach((track) => track.stop());
    if (audioContext) {
      try { await audioContext.close(); } catch (_) {}
    }
    mediaStream = null;
    audioContext = null;
    sourceNode = null;
    processorNode = null;
    muteNode = null;
    segmentChunks = [];
    segmentSamples = 0;
    speechStarted = false;
    silenceSamples = 0;
    $('startMic').disabled = !recognizer || !selectedVerses.length;
    $('stopMic').disabled = true;
    $('testMic').disabled = false;
    setMicState('مائیک بند ہے');
    status('تلاوت روک دی گئی۔ آواز کی کوئی فائل محفوظ یا اپ لوڈ نہیں کی گئی۔');
  }

  $('surahSelect').addEventListener('change', (event) => {
    if (event.target.value) loadSurah(event.target.value);
  });
  $('loadRange').addEventListener('click', renderRange);
  $('resetProgress').addEventListener('click', () => {
    cursor = 0;
    renderRange();
  });
  $('loadModel').addEventListener('click', loadOfflineModel);
  $('testMic').addEventListener('click', testMicrophone);
  $('startMic').addEventListener('click', startMicrophone);
  $('stopMic').addEventListener('click', stopMicrophone);

  $('startMic').disabled = true;
  $('stopMic').disabled = true;
  loadSurahs();
})();
