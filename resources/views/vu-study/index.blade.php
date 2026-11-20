@extends('layouts.app')
@section('title', 'VU Smart Study Dashboard | MRK Digital')
@section('robots', 'noindex,nofollow')
@section('content')
<section class="page-hero"><div class="container narrow">
<span class="eyebrow">MRK DIGITAL · VIRTUAL UNIVERSITY</span>
<h1>VU Smart Study Dashboard</h1>
<p>اپنے اعلانات، اسائنمنٹس، کوئزز، GDB اور امتحانات ایک جگہ منظم کریں۔</p>
<a class="button secondary" href="https://vulms.vu.edu.pk/" target="_blank" rel="noopener">Open Official VULMS</a>
</div></section>
<section class="section"><div class="container">
<div class="stats">
<div><span>اگلے 14 دن کی ڈیڈ لائنز</span><strong>{{ $stats['upcoming'] }}</strong></div>
<div><span>Announcements</span><strong>{{ $stats['announcements'] }}</strong></div>
<div><span>Quizzes</span><strong>{{ $stats['quizzes'] }}</strong></div>
<div><span>GDB</span><strong>{{ $stats['gdb'] }}</strong></div>
</div>
<div class="table-card" style="padding:1rem;margin:1rem 0">
<h2>نیا Study Item شامل کریں</h2>
<p>جب تک محفوظ خودکار Sync فعال نہیں ہوتا، یہاں اپنی سرگرمیاں دستی طور پر درج کر سکتے ہیں۔</p>
<form method="POST" action="{{ route('vu-study.store') }}" class="dashboard-filters">@csrf
<label>Course Code<input name="course_code" placeholder="CS101"></label>
<label>Course Name<input name="course_title" placeholder="Introduction to Computing"></label>
<label>Type<select name="item_type" required><option value="announcement">Announcement</option><option value="assignment">Assignment</option><option value="quiz">Quiz</option><option value="gdb">GDB</option><option value="exam">Exam</option><option value="grade">Grade</option></select></label>
<label>Title<input name="title" required maxlength="255"></label>
<label>Opening Date<input type="datetime-local" name="opens_at"></label>
<label>Deadline<input type="datetime-local" name="due_at"></label>
<label style="grid-column:1/-1">Details<textarea name="description" rows="3"></textarea></label>
<button class="button primary" type="submit">Save Study Item</button>
</form>
</div>
<div class="table-card"><div class="table-head"><h2>Study Timeline</h2><span>{{ $items->total() }} items</span></div>
@if($items->count())
<div class="table-wrap"><table><thead><tr><th>Course</th><th>Type</th><th>Title</th><th>Deadline</th><th>Status</th><th>Update</th></tr></thead><tbody>
@foreach($items as $item)
<tr><td>{{ $item->course_code }}<br>{{ $item->course_title }}</td><td>{{ strtoupper($item->item_type) }}</td><td><strong>{{ $item->title }}</strong>@if($item->description)<br><small>{{ \Illuminate\Support\Str::limit($item->description, 120) }}</small>@endif @if($item->source_url)<br><a href="{{ $item->source_url }}" target="_blank" rel="noopener">Open source</a>@endif</td><td>{{ $item->due_at?->format('d M Y, h:i A') ?? 'Not specified' }}</td><td>{{ ucfirst($item->status) }}</td><td><form method="POST" action="{{ route('vu-study.status', $item) }}">@csrf @method('PATCH')<select name="status" onchange="this.form.submit()">@foreach(['detected'=>'Detected','read'=>'Read','completed'=>'Completed','submitted'=>'Submitted','manual'=>'Manual'] as $key=>$label)<option value="{{ $key }}" @selected($item->status===$key)>{{ $label }}</option>@endforeach</select></form></td></tr>
@endforeach
</tbody></table></div>{{ $items->links() }}
@else<div class="empty">ابھی کوئی Study Item موجود نہیں۔ اوپر فارم سے اپنی پہلی سرگرمی شامل کریں۔</div>@endif
</div>
<p style="margin-top:1rem">اہم: اصل ڈیڈ لائن اور ہدایات ہمیشہ <a href="https://vulms.vu.edu.pk/" target="_blank" rel="noopener">سرکاری VULMS</a> سے تصدیق کریں۔ یہ MRK کا غیر سرکاری ذاتی مطالعہ معاون ہے۔</p>
</div></section>
@endsection