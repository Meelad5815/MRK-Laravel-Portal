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
<div class="table-card vu-form-card vu-companion-card">
<div class="table-head"><h2>Android Companion اتصال</h2><span>{{ $hasActiveCompanionToken ? 'Token active' : 'Not connected' }}</span></div>
<p>Android ایپ کے لیے ذاتی API Token بنائیں۔ یہ Token صرف ایک مرتبہ دکھایا جائے گا؛ MRK صرف اس کا محفوظ Hash رکھتا ہے۔</p>
@if(session('companion_token_once'))
<div class="vu-token-once">
<strong>آپ کا نیا Companion Token — ابھی کاپی کریں</strong>
<code>{{ session('companion_token_once') }}</code>
<p>اسے صرف اپنی MRK VU Companion ایپ میں درج کریں۔ کسی دوسرے شخص کو نہ بھیجیں۔ صفحہ بند ہونے کے بعد یہ Token دوبارہ نہیں دکھایا جائے گا۔</p>
</div>
@endif
<div class="vu-token-actions">
<form method="POST" action="{{ route('vu-study.companion-token.create') }}">@csrf
<button class="button primary" type="submit">{{ $hasActiveCompanionToken ? 'Generate new token (revoke old)' : 'Generate Companion Token' }}</button>
</form>
@if($hasActiveCompanionToken)
<form method="POST" action="{{ route('vu-study.companion-token.revoke') }}">@csrf @method('DELETE')
<button class="button secondary" type="submit">Revoke token</button>
</form>
@endif
</div>
<small>API endpoint: <code>/api/vu-study/sync</code> · HTTPS only · Token expires after one year.</small>
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
<label class="vu-wide">Details<textarea name="description" rows="3" maxlength="10000"></textarea></label>
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
@endsection
