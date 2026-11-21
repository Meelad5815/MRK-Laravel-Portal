@extends('layouts.app')
@section('title', 'VU Smart Study Dashboard | MRK Digital')
@section('robots', 'noindex,nofollow')
@section('content')
<section class="page-hero"><div class="container narrow">
<span class="eyebrow">MRK DIGITAL · VIRTUAL UNIVERSITY</span>
<h1>VU Smart Study Dashboard</h1>
<p>اپنے اعلانات، اسائنمنٹس، کوئزز، GDB اور امتحانات ایک جگہ منظم کریں۔</p>
<div class="vu-actions">
<a class="button secondary" href="https://vulms.vu.edu.pk/" target="_blank" rel="noopener">Open Official VULMS</a>
<button class="button primary" id="install-mrk-pwa" type="button" hidden>Install MRK Study App</button>
</div>
<p class="vu-install-hint" id="install-mrk-hint">موبائل میں انسٹال کرنے کے لیے Chrome کے مینو سے “Add to Home screen” بھی منتخب کر سکتے ہیں۔</p>
</div></section>
<section class="section"><div class="container">
<div class="table-card vu-form-card vu-web-import-card">
<div class="table-head"><h2>VULMS Web Import</h2><span>Browser-based</span></div>
<p>VULMS کو دوسری ٹیب میں کھولیں، متعلقہ اعلان یا سرگرمی کا نظر آنے والا متن Copy کریں، پھر یہاں Paste کرکے MRK میں محفوظ کریں۔ آپ کا VULMS پاس ورڈ یا سیشن یہاں درکار نہیں۔</p>
<div class="vu-token-actions"><button class="button secondary" type="button" id="vu-copy-bookmarklet">ایک بار کا Auto Detect بٹن بنائیں</button></div>
<p class="vu-install-hint">Bookmarklet بنانے کے بعد اسے Chrome Bookmark کے URL میں محفوظ کریں، پھر VULMS کے متعلقہ صفحے پر Bookmark دبائیں۔ یہ صرف کھلے صفحے کا نظر آنے والا متن MRK میں منتقل کرتا ہے؛ پس منظر میں خودکار Sync نہیں کرتا۔
<div class="vu-token-actions">
<a class="button secondary" href="https://vulms.vu.edu.pk/" target="_blank" rel="noopener">VULMS نئی ٹیب میں کھولیں</a>
<button class="button secondary" type="button" id="vu-fill-from-clipboard">Clipboard سے Paste کریں</button>
</div>
<label class="vu-wide">VULMS سے کاپی کیا گیا متن
<textarea id="vu-pasted-text" rows="5" placeholder="VULMS کے اعلان، کوئز، اسائنمنٹ یا GDB کا متن یہاں Paste کریں..."></textarea>
</label>
<button class="button primary" type="button" id="vu-parse-pasted-text">متن سے فارم پُر کریں</button>
<p class="vu-install-hint" id="vu-import-feedback" aria-live="polite">متن پڑھ کر عنوان، کورس کوڈ اور تفصیل فارم میں منتقل کی جائے گی۔ تاریخ خودکار طور پر صرف واضح YYYY-MM-DD یا DD/MM/YYYY فارمیٹ میں پہچانی جائے گی؛ محفوظ کرنے سے پہلے لازماً تصدیق کریں۔</p>
</div>
<div class="table-card vu-form-card">
<div class="table-head"><h2>Android Companion App</h2><span>Secure device connection</span></div>
<p>MRK VU Study Android App کو اپنے اکاؤنٹ سے جوڑنے کے لیے ایک محفوظ Token بنائیں۔ یہ Token صرف ایک بار دکھایا جائے گا؛ اسے صرف اپنی ایپ میں محفوظ کریں۔</p>
@if(session('companion_token_once'))
<label class="vu-wide">آپ کا نیا Companion Token — ابھی Copy کریں
<textarea id="mrk-companion-token" rows="3" readonly>{{ session('companion_token_once') }}</textarea>
</label>
<button class="button secondary" type="button" id="mrk-copy-companion-token">Token Copy کریں</button>
<p class="vu-install-hint">حفاظت کے لیے یہ مکمل Token دوبارہ نہیں دکھایا جائے گا۔ اگر گم ہو جائے تو نیا Token بنائیں؛ پرانا خود بخود بند ہو جائے گا۔</p>
@endif
@if($hasActiveCompanionToken)
<p><strong>حالت:</strong> اس اکاؤنٹ کے لیے ایک فعال Android Companion Token موجود ہے۔ نیا Token بنانے سے پرانا بند ہو جائے گا۔</p>
@else
<p><strong>حالت:</strong> ابھی کوئی فعال Android Token موجود نہیں۔</p>
@endif
<form method="POST" action="{{ route('vu-study.companion-token.create') }}" class="vu-token-actions">
@csrf
<button class="button primary" type="submit">{{ $hasActiveCompanionToken ? 'نیا Token بنائیں' : 'Android App Token بنائیں' }}</button>
</form>
@if($hasActiveCompanionToken)
<form method="POST" action="{{ route('vu-study.companion-token.revoke') }}" class="vu-token-actions">
@csrf
@method('DELETE')
<button class="button secondary" type="submit">Android App کا Token بند کریں</button>
</form>
@endif
</div>
<div class="stats vu-stats">
<div><span>اگلے 14 دن</span><strong>{{ $stats['upcoming'] }}</strong><small>Open deadlines</small></div>
<div><span>Overdue</span><strong>{{ $stats['overdue'] }}</strong><small>توجہ درکار</small></div>
<div><span>Announcements</span><strong>{{ $stats['announcements'] }}</strong></div>
<div><span>Quizzes</span><strong>{{ $stats['quizzes'] }}</strong></div>
<div><span>GDB</span><strong>{{ $stats['gdb'] }}</strong></div>
</div>
<div class="table-card vu-form-card">
<div class="table-head"><h2>نیا Study Item شامل کریں</h2><span>Manual entry</span></div>
<p>اپنے VULMS سے تصدیق شدہ سرگرمی اور آخری تاریخ یہاں محفوظ کریں۔</p>
<form method="POST" action="{{ route('vu-study.store') }}" class="dashboard-filters vu-entry-form">@csrf
<label>Course Code<input name="course_code" placeholder="CS101" maxlength="30"></label>
<label>Course Name<input name="course_title" placeholder="Introduction to Computing"></label>
<label>Type<select name="item_type" required><option value="announcement">Announcement</option><option value="assignment">Assignment</option><option value="quiz">Quiz</option><option value="gdb">GDB</option><option value="exam">Exam</option><option value="grade">Grade</option></select></label>
<label>Title<input name="title" required maxlength="255"></label>
<label>Opening Date<input type="datetime-local" name="opens_at"></label>
<label>Deadline<input type="datetime-local" name="due_at"></label>
<label class="vu-wide">تفصیل / VULMS کی اضافی معلومات<textarea id="vu-description" name="description" rows="3" maxlength="10000"></textarea></label>
<button class="button primary" type="submit">Save Study Item</button>
</form>
</div>
<div class="table-card vu-timeline">
<div class="table-head"><h2>Study Timeline</h2><span>{{ $items->total() }} items</span></div>
<form method="GET" action="{{ route('vu-study.index') }}" class="vu-filter-form">
<label>Search<input type="search" name="q" value="{{ $search }}" placeholder="Course, code or title"></label>
<label>Type<select name="type"><option value="">All types</option>@foreach(['announcement'=>'Announcement','assignment'=>'Assignment','quiz'=>'Quiz','gdb'=>'GDB','exam'=>'Exam','grade'=>'Grade'] as $key=>$label)<option value="{{ $key }}" @selected($type === $key)>{{ $label }}</option>@endforeach</select></label>
<button class="button secondary" type="submit">Filter</button>
<a class="vu-clear-filter" href="{{ route('vu-study.index') }}">Clear</a>
</form>
@if($items->count())
<div class="table-wrap"><table class="vu-table"><thead><tr><th>Course</th><th>Type</th><th>Title / Details</th><th>Deadline</th><th>Status</th><th>Update</th></tr></thead><tbody>
@foreach($items as $item)
<tr>
<td data-label="Course"><strong>{{ $item->course_code ?: '—' }}</strong><br><small>{{ $item->course_title }}</small></td>
<td data-label="Type">{{ strtoupper($item->item_type) }}</td>
<td data-label="Title"><strong>{{ $item->title }}</strong>@if($item->description)<br><small>{{ \Illuminate\Support\Str::limit($item->description, 120) }}</small>@endif @if($item->source_url)<br><a href="{{ $item->source_url }}" target="_blank" rel="noopener">Open source</a>@endif</td>
<td data-label="Deadline">{{ $item->due_at?->format('d M Y, h:i A') ?? 'Not specified' }}</td>
<td data-label="Status">{{ ucfirst($item->status) }}</td>
<td data-label="Update"><form method="POST" action="{{ route('vu-study.status', $item) }}">@csrf @method('PATCH')<select name="status" aria-label="Update status" onchange="this.form.submit()">@foreach(['detected'=>'Detected','read'=>'Read','completed'=>'Completed','submitted'=>'Submitted','manual'=>'Manual'] as $key=>$label)<option value="{{ $key }}" @selected($item->status === $key)>{{ $label }}</option>@endforeach</select></form></td>
</tr>
@endforeach
</tbody></table></div>
<div class="vu-pagination">{{ $items->links() }}</div>
@else
<div class="empty">اس فلٹر کے مطابق کوئی Study Item نہیں ملا۔ اوپر فارم سے اپنی پہلی سرگرمی شامل کریں۔</div>
@endif
</div>
<p class="vu-disclaimer">اہم: اصل ڈیڈ لائن اور ہدایات ہمیشہ <a href="https://vulms.vu.edu.pk/" target="_blank" rel="noopener">سرکاری VULMS</a> سے تصدیق کریں۔ یہ MRK کا غیر سرکاری ذاتی مطالعہ معاون ہے۔</p>
</div></section>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const companionCopyButton = document.getElementById('mrk-copy-companion-token');
    companionCopyButton?.addEventListener('click', async function () {
        const tokenField = document.getElementById('mrk-companion-token');
        if (!tokenField) return;
        try {
            await navigator.clipboard.writeText(tokenField.value);
            companionCopyButton.textContent = 'Token Copy ہو گیا';
        } catch (error) {
            tokenField.focus();
            tokenField.select();
            feedback?.textContent = 'Token منتخب ہو گیا ہے؛ Copy منتخب کریں۔';
        }
    });

    const pasteBox = document.getElementById('vu-pasted-text');
    const feedback = document.getElementById('vu-import-feedback');
    const pasteButton = document.getElementById('vu-fill-from-clipboard');
    const parseButton = document.getElementById('vu-parse-pasted-text');
    const form = document.querySelector('.vu-entry-form');
    if (!pasteBox || !form) return;
    const bookmarkletButton = document.getElementById('vu-copy-bookmarklet');
    bookmarkletButton?.addEventListener('click', function () {
        const code = "javascript:(()=>{if(location.hostname!=='vulms.vu.edu.pk'){alert('Pehle VULMS ka page kholen.');return;}const text=(document.body.innerText||'').slice(0,12000);location.href='https://meeladrazakhokhar786.freehosting.dev/vu-study#vu-import='+encodeURIComponent(text);})();";
        feedback.textContent = 'Bookmarklet code neeche khul raha hai. Isay copy karke Chrome Bookmark ke URL mein paste karein.';
        window.prompt('Is code ko copy karein, phir Chrome mein Bookmark ke URL mein paste karein:', code);
    });

    if (location.hash.startsWith('#vu-import=')) {
        try {
            pasteBox.value = decodeURIComponent(location.hash.slice('#vu-import='.length));
            history.replaceState(null, '', location.pathname + location.search);
            parseButton?.click();
            feedback.textContent = 'VULMS صفحے سے متن آگیا۔ فارم چیک کریں اور Save کرنے سے پہلے قسم اور تاریخ کی تصدیق کریں۔';
        } catch (error) {
            feedback.textContent = 'متن وصول کرنے میں مسئلہ ہوا؛ دوبارہ کوشش کریں۔';
        }
    }


    pasteButton?.addEventListener('click', async function () {
        try {
            pasteBox.value = await navigator.clipboard.readText();
            feedback.textContent = 'Clipboard کا متن آ گیا ہے۔ اب “متن سے فارم پُر کریں” دبائیں۔';
        } catch (error) {
            pasteBox.focus();
            feedback.textContent = 'Clipboard اجازت دستیاب نہیں۔ VULMS سے متن Copy کرکے اس خانے میں خود Paste کریں۔';
        }
    });

    parseButton?.addEventListener('click', function () {
        const raw = pasteBox.value.trim();
        if (!raw) {
            feedback.textContent = 'پہلے VULMS سے کاپی کیا گیا متن Paste کریں۔';
            return;
        }
        const lines = raw.split(/\r?\n/).map(line => line.trim()).filter(Boolean);
        const title = form.querySelector('[name="title"]');
        const courseCode = form.querySelector('[name="course_code"]');
        const description = document.getElementById('vu-description');
        const dueAt = form.querySelector('[name="due_at"]');

        if (title && !title.value.trim()) title.value = (lines[0] || '').slice(0, 255);
        const codeMatch = raw.match(/\b[A-Z]{2,5}\s?-?\d{3}\b/i);
        if (courseCode && codeMatch && !courseCode.value.trim()) courseCode.value = codeMatch[0].replace(/\s+/g, '').replace('-', '').toUpperCase();

        const isoDate = raw.match(/\b(20\d{2})-(0[1-9]|1[0-2])-([0-2]\d|3[01])\b/);
        const slashDate = raw.match(/\b([0-3]?\d)\/([01]?\d)\/(20\d{2})\b/);
        if (dueAt && !dueAt.value && isoDate) dueAt.value = isoDate[0] + 'T23:59';
        else if (dueAt && !dueAt.value && slashDate) {
            const day = slashDate[1].padStart(2, '0');
            const month = slashDate[2].padStart(2, '0');
            dueAt.value = slashDate[3] + '-' + month + '-' + day + 'T23:59';
        }
        if (description) description.value = raw.slice(0, 10000);
        feedback.textContent = 'فارم پُر ہو گیا ہے۔ قسم، کورس، عنوان اور ڈیڈ لائن چیک کریں، پھر نیچے “Save Study Item” دبائیں۔ ایک بار میں ایک سرگرمی محفوظ ہوتی ہے۔';
        form.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });
});
</script>
\n@endsection
