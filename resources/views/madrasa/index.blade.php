@extends('layouts.app')
@section('title', 'مدرسۃ المدینہ مینجمنٹ پورٹل | MRK')
@section('html_lang', 'ur')
@section('robots', 'noindex,nofollow')
@section('content')
<style>
.madrasa-dashboard{display:grid;gap:1rem;margin:0 0 1.5rem}.madrasa-welcome{display:flex;align-items:center;justify-content:space-between;gap:1rem;flex-wrap:wrap;padding:1.2rem;border-radius:16px;background:linear-gradient(135deg,#123c32,#17624d);color:#fff}.madrasa-welcome h2{margin:.3rem 0;color:#fff}.madrasa-welcome p{margin:0;color:#e2eee8}.role-pill{background:#fff;color:#14513f;border-radius:999px;padding:.45rem .85rem;font-weight:700}.madrasa-stats{display:grid;grid-template-columns:repeat(5,minmax(0,1fr));gap:.75rem}.madrasa-stats article{padding:1rem;border:1px solid #dce7e1;border-radius:13px;background:#fff;display:grid;gap:.25rem}.madrasa-stats article span{color:#52635b;font-size:.9rem}.madrasa-stats article strong{font-size:1.7rem;color:#14513f}.madrasa-stats article small{color:#718078}.madrasa-programs{display:flex;gap:.6rem;flex-wrap:wrap}.madrasa-programs span{padding:.5rem .8rem;background:#eef6f1;border-radius:9px;color:#214d3c}.madrasa-shortcuts{display:flex;gap:.5rem;flex-wrap:wrap}.madrasa-shortcuts a{padding:.55rem .8rem;border:1px solid #d5e2da;border-radius:9px;text-decoration:none;color:#174d3a;background:#fff;font-weight:600}.madrasa-report-heading{display:flex;justify-content:space-between;gap:1rem;align-items:center;flex-wrap:wrap}.madrasa-report-heading p{margin:.25rem 0;color:#64736b}.madrasa-month-filter{display:flex;gap:.5rem;align-items:end;flex-wrap:wrap}.madrasa-month-filter label{display:grid;gap:.25rem}.madrasa-month-filter input{padding:.6rem;border:1px solid #cbd5e1;border-radius:8px}.madrasa-report-grid{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:.7rem;margin-top:1rem}.madrasa-report-grid article{display:grid;gap:.25rem;padding:.8rem;background:#f3f8f5;border-radius:10px}.madrasa-report-grid article span{font-size:.88rem;color:#607168}.madrasa-report-grid article strong{font-size:1.3rem;color:#14513f}.madrasa-bulk-controls{display:flex;gap:1rem;flex-wrap:wrap;margin:.75rem 0}.madrasa-bulk-controls label{display:grid;gap:.3rem}.madrasa-bulk-controls input,.madrasa-bulk-controls select,.madrasa-bulk-table input,.madrasa-bulk-table select{padding:.55rem;border:1px solid #cbd5e1;border-radius:8px;max-width:100%}.madrasa-bulk-table select{min-width:125px}.madrasa-filters{display:grid;grid-template-columns:2fr 1fr 1fr auto;gap:.75rem;align-items:end;margin:1rem 0}.madrasa-filters label{display:grid;gap:.35rem}.madrasa-filters input,.madrasa-filters select{width:100%;padding:.7rem;border:1px solid #cbd5e1;border-radius:9px}.madrasa-filter-actions{display:flex;gap:.5rem;flex-wrap:wrap}.table-wrap{overflow-x:auto}.table-wrap table{min-width:680px;width:100%}html{scroll-behavior:smooth}h2[id]{scroll-margin-top:90px}
@media(max-width:850px){.madrasa-report-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.madrasa-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.madrasa-filters{grid-template-columns:1fr 1fr}.madrasa-filter-actions{grid-column:1/-1}}
@media(max-width:480px){.madrasa-stats{grid-template-columns:repeat(2,minmax(0,1fr))}.madrasa-filters{grid-template-columns:1fr}.madrasa-filter-actions{grid-column:auto}.madrasa-welcome{align-items:flex-start}}
</style>

<section class="page-hero"><div class="container narrow" dir="rtl">
    <span class="eyebrow">MRK MADRASA 58</span>
    <h1>مدرسۃ المدینہ مینجمنٹ پورٹل</h1>
    <p>طلبہ، اساتذہ، حاضری اور تعلیمی پیش رفت کا محفوظ انتظام</p>
    <p><strong>آپ کا کردار:</strong> {{ ['admin' => 'منتظم', 'qari' => 'قاری / معلم', 'parent' => 'والدین'][$role] ?? $role }}</p>
</div></section>
<section class="section"><div class="container" dir="rtl">
<div class="madrasa-dashboard">
  <div class="madrasa-welcome"><div><span class="eyebrow">MRK MADRASA 58 • تعلیمی انتظام</span><h2>السلام علیکم! {{ auth()->user()->name }}</h2><p>آج کی تعلیمی سرگرمیوں اور مدرسہ کے ریکارڈ کا خلاصہ</p></div><span class="role-pill">{{ ['admin'=>'منتظم','qari'=>'قاری / معلم','parent'=>'والدین'][$role] ?? $role }}</span></div>
  <div class="madrasa-stats">
    <article><span>کل طلبہ</span><strong>{{ $studentRows->count() }}</strong><small>آپ کے دائرۂ اختیار میں</small></article>
    <article><span>فعال طلبہ</span><strong>{{ $activeStudents }}</strong><small>فعال داخلے</small></article>
    <article><span>آج حاضر</span><strong>{{ $presentToday }}</strong><small>درج شدہ حاضری</small></article>
    <article><span>آج غیر حاضر</span><strong>{{ $absentToday }}</strong><small>درج شدہ غیر حاضری</small></article>
    <article><span>تاخیر سے</span><strong>{{ $lateToday }}</strong><small>آج کا ریکارڈ</small></article>
  </div>
  <div class="madrasa-programs"><span>حفظ: <b>{{ $programCounts['hifz'] ?? 0 }}</b></span><span>ناظرہ: <b>{{ $programCounts['nazra'] ?? 0 }}</b></span><span>قاعدہ: <b>{{ $programCounts['qaida'] ?? 0 }}</b></span></div>
  <nav class="madrasa-shortcuts" aria-label="فوری رسائی">
    <a href="#students">طلبہ</a><a href="#course-progress">نصاب کی تکمیل</a><a href="#attendance">حاضری</a><a href="#progress">روزانہ پیش رفت</a><a href="#exams">امتحانات</a><a href="#certificates">سرٹیفکیٹس</a><a href="#announcements">اعلانات</a>
    @if($role==='admin')<a href="#admissions">نیا داخلہ</a><a href="#accounts">اکاؤنٹس</a>@endif
  </nav>
</div>
<div class="table-card madrasa-monthly-report" style="padding:1.25rem;margin-bottom:1.25rem">
  <div class="madrasa-report-heading"><div><h2>ماہانہ تعلیمی کارکردگی</h2><p>منتخب مہینے کی حاضری، اسباق اور امتحانات کا خلاصہ</p></div>
  @if($role === 'admin')
<form method="GET" action="{{ route('madrasa.reports.monthly.print') }}" target="_blank" class="madrasa-month-filter" style="margin:.5rem 0"><label>پرنٹ ماہ <input type="month" name="report_month" value="{{ $reportMonth }}" required></label><button class="button secondary" type="submit">تمام فعال طلبہ کے کارڈ پرنٹ کریں</button></form>
@endif
<form method="GET" action="{{ route('madrasa') }}" class="madrasa-month-filter"><label>مہینہ <input type="month" name="report_month" value="{{ $reportMonth }}"></label><button class="button secondary" type="submit">رپورٹ دیکھیں</button></form></div>
  <div class="madrasa-report-grid">
    <article><span>حاضر</span><strong>{{ $monthlyAttendance['present'] ?? 0 }}</strong></article>
    <article><span>غیر حاضر</span><strong>{{ $monthlyAttendance['absent'] ?? 0 }}</strong></article>
    <article><span>تاخیر</span><strong>{{ $monthlyAttendance['late'] ?? 0 }}</strong></article>
    <article><span>رخصت</span><strong>{{ $monthlyAttendance['leave'] ?? 0 }}</strong></article>
    <article><span>درج شدہ اسباق</span><strong>{{ $monthlyProgressCount }}</strong></article>
    <article><span>امتحانات</span><strong>{{ $monthlyExamCount }}</strong></article>
    <article><span>اوسط امتحانی فیصد</span><strong>{{ $monthlyExamAverage !== null ? number_format((float)$monthlyExamAverage,1).'%' : '—' }}</strong></article>
  </div>
</div>
@if(session('temporary_account_password'))
<div class="alert success"><strong>عارضی پاس ورڈ (صرف ابھی دکھایا جا رہا ہے):</strong><code dir="ltr">{{ session('temporary_account_password') }}</code><p>اسے متعلقہ صارف تک محفوظ طریقے سے پہنچائیں اور پہلی بار لاگ اِن کے بعد تبدیل کروائیں۔</p></div>
@endif
@if($role === 'admin')
<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2 id="admissions">نیا طالب علم داخل کریں</h2>
<form method="POST" action="{{ route('madrasa.students.store') }}" class="form-grid">@csrf
<label>طالب علم کا نام<input name="full_name" maxlength="150" required></label>
<label>سرپرست کا نام<input name="guardian_name" maxlength="150" required></label>
<label>سرپرست کا فون<input name="guardian_phone" maxlength="32" inputmode="tel"></label>
<label>تعلیمی شعبہ<select name="program" required><option value="hifz">حفظ</option><option value="nazra">ناظرہ</option><option value="qaida">قاعدہ</option></select></label>
<label>داخلہ کی تاریخ<input type="date" name="admission_date"></label>
<label>سابقہ مدرسہ / ادارہ<input name="previous_institution" maxlength="180"></label>
<div><button class="button primary" type="submit">داخلہ محفوظ کریں</button></div>
</form></div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2 id="accounts">قاری یا والدین کا اکاؤنٹ بنائیں</h2>
<form method="POST" action="{{ route('madrasa.accounts.store') }}" class="form-grid">@csrf
<label>پورا نام<input name="name" required maxlength="100"></label>
<label>ای میل<input type="email" name="email" required maxlength="255" dir="ltr"></label>
<label>کردار<select name="madrasa_role"><option value="qari">قاری</option><option value="parent">والدین</option></select></label>
<div><button class="button primary" type="submit">اکاؤنٹ بنائیں</button></div>
</form></div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>قاری / والدین کے اکاؤنٹس</h2>
<p>اگر کسی صارف کا پاس ورڈ کام نہ کرے تو صرف اسی اکاؤنٹ کا نیا عارضی پاس ورڈ بنائیں۔ پرانا پاس ورڈ فوراً غیر مؤثر ہو جائے گا۔</p>
<div class="table-wrap"><table><thead><tr><th>نام</th><th>ای میل</th><th>کردار</th><th>پاس ورڈ</th></tr></thead><tbody>
@foreach($qariUsers->concat($parentUsers) as $account)
<tr><td>{{ $account->name }}</td><td dir="ltr">{{ $account->email }}</td><td>{{ $account->madrasa_role === 'qari' ? 'قاری' : 'والدین' }}</td><td>
<form method="POST" action="{{ route('madrasa.accounts.reset-password', $account->id) }}" onsubmit="return confirm('اس اکاؤنٹ کا موجودہ پاس ورڈ تبدیل ہو جائے گا۔ جاری رکھیں؟')">@csrf<button class="button secondary" type="submit">نیا پاس ورڈ بنائیں</button></form>
</td></tr>
@endforeach
</tbody></table></div>
</div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>قاری کو طالب علم تفویض کریں</h2>
<form method="POST" action="{{ route('madrasa.assign-qari') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows->where('status','active') as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>قاری<select name="qari_user_id" required>@foreach($qariUsers as $qari)<option value="{{ $qari->id }}">{{ $qari->name }} ({{ $qari->email }})</option>@endforeach</select></label>
<div><button class="button primary" type="submit">تفویض کریں</button></div>
</form></div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>والدین کا تصدیق شدہ تعلق قائم کریں</h2>
<form method="POST" action="{{ route('madrasa.link-parent') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows->where('status','active') as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>والدین کا اکاؤنٹ<select name="parent_user_id" required>@foreach($parentUsers as $parent)<option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>@endforeach</select></label>
<div><button class="button primary" type="submit">تعلق تصدیق کریں</button></div>
</form></div>
@endif

@if(in_array($role, ['admin','qari'], true))
<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>روزانہ حاضری درج کریں</h2>
<form method="POST" action="{{ route('madrasa.attendance.store') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows->where('status','active') as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>تاریخ<input type="date" name="attendance_date" value="{{ now()->toDateString() }}" required></label>
<label>سیشن<select name="session"><option value="morning">صبح</option><option value="afternoon">دوپہر</option><option value="evening">شام</option></select></label>
<label>حاضری<select name="status"><option value="present">حاضر</option><option value="absent">غیر حاضر</option><option value="late">تاخیر سے</option><option value="leave">رخصت</option></select></label>
<label>نوٹ<input name="remarks" maxlength="500"></label>
<div><button class="button primary" type="submit">حاضری محفوظ کریں</button></div>
</form>
<hr style="margin:1.25rem 0;border:0;border-top:1px solid #e2e8f0">
<h3>پوری کلاس کی حاضری ایک ساتھ درج کریں</h3>
<p>ہر طالب علم کے سامنے حاضر، غیر حاضر، تاخیر یا رخصت منتخب کریں۔ پہلے سے موجود حاضری اسی تاریخ اور سیشن کے لیے اپ ڈیٹ ہو جائے گی۔</p>
@if($studentRows->where('status','active')->isEmpty())
<p class="empty">اس وقت حاضری درج کرنے کے لیے کوئی فعال طالب علم موجود نہیں۔</p>
@else
<form method="POST" action="{{ route('madrasa.attendance.bulk') }}">@csrf
<div class="madrasa-bulk-controls">
<label>تاریخ<input type="date" name="attendance_date" value="{{ now()->toDateString() }}" required></label>
<label>سیشن<select name="session"><option value="morning">صبح</option><option value="afternoon">دوپہر</option><option value="evening">شام</option></select></label>
</div>
<div class="table-wrap"><table class="madrasa-bulk-table"><thead><tr><th>طالب علم</th><th>حاضری</th><th>نوٹ</th></tr></thead><tbody>
@foreach($studentRows->where('status','active') as $student)
<tr><td>{{ $student->student_code }} — {{ $student->full_name }}</td><td><select name="attendance[{{ $student->id }}]" required><option value="">منتخب کریں</option><option value="present">حاضر</option><option value="absent">غیر حاضر</option><option value="late">تاخیر</option><option value="leave">رخصت</option></select></td><td><input name="remarks[{{ $student->id }}]" maxlength="500" placeholder="اختیاری نوٹ"></td></tr>
@endforeach
</tbody></table></div>
<button class="button primary" type="submit" onclick="return confirm('منتخب طلبہ کی حاضری محفوظ کریں؟')">تمام حاضری محفوظ کریں</button>
</form>
@endif
</div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>حفظ و ناظرہ کی روزانہ پیش رفت</h2>
<form method="POST" action="{{ route('madrasa.progress.store') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows->where('status','active') as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>تاریخ<input type="date" name="progress_date" value="{{ now()->toDateString() }}" required></label>
<label>سیشن<select name="session"><option value="morning">صبح</option><option value="afternoon">دوپہر</option><option value="evening">شام</option></select></label>
<label>سبق<input name="sabaq" maxlength="100"></label>
<label>سبقی<input name="sabqi" maxlength="100"></label>
<label>منزل<input name="manzil" maxlength="100"></label>
<label>معیار<select name="quality"><option value="">منتخب کریں</option><option value="excellent">بہترین</option><option value="good">اچھا</option><option value="needs_attention">مزید توجہ درکار</option></select></label>
<label>تفصیل<input name="notes" maxlength="2000"></label>
<div><button class="button primary" type="submit">پیش رفت محفوظ کریں</button></div>
</form></div>
@endif

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2 id="students">طلبہ کا ریکارڈ</h2>
<form method="GET" action="{{ route('madrasa') }}" class="madrasa-filters">
<label>نام، طالب علم کوڈ یا سرپرست کا فون<input type="search" name="student_search" value="{{ $studentSearch }}" placeholder="مثلاً MRK58 یا طالب علم کا نام"></label>
<label>تعلیمی شعبہ<select name="program"><option value="">تمام شعبے</option><option value="hifz" @selected(request('program')==='hifz')>حفظ</option><option value="nazra" @selected(request('program')==='nazra')>ناظرہ</option><option value="qaida" @selected(request('program')==='qaida')>قاعدہ</option></select></label>
@if($role==='admin')<label>داخلے کی حیثیت<select name="student_status"><option value="">تمام</option><option value="active" @selected(request('student_status')==='active')>فعال</option><option value="inactive" @selected(request('student_status')==='inactive')>غیر فعال</option><option value="archived" @selected(request('student_status')==='archived')>آرکائیو</option></select></label>@endif
<div class="madrasa-filter-actions"><button class="button primary" type="submit">تلاش کریں</button><a class="button secondary" href="{{ route('madrasa') }}">فلٹر ختم کریں</a></div>
</form>
@if($studentRows->isEmpty())
<p class="empty">ابھی اس پورٹل کے لیے کوئی طالب علم دستیاب نہیں۔</p>
@else
<div class="table-wrap"><table><thead><tr><th>کوڈ</th><th>نام</th><th>سرپرست</th><th>شعبہ</th><th>حیثیت</th>@if($role === 'admin')<th>انتظام</th>@endif</tr></thead><tbody>
@foreach($studentRows as $student)
<tr><td>{{ $student->student_code }}</td><td>{{ $student->full_name }}</td><td>{{ $role === 'admin' ? $student->guardian_name : '—' }}</td><td>{{ ['hifz'=>'حفظ','nazra'=>'ناظرہ','qaida'=>'قاعدہ'][$student->program] ?? $student->program }}</td><td>{{ $student->status === 'active' ? 'فعال' : $student->status }}</td>
@if($role === 'admin')<td><details><summary>ترمیم</summary>
<form method="POST" action="{{ route('madrasa.students.update', $student->id) }}" class="form-grid">@csrf @method('PUT')
<label>نام<input name="full_name" value="{{ $student->full_name }}" required maxlength="150"></label>
<label>سرپرست<input name="guardian_name" value="{{ $student->guardian_name }}" required maxlength="150"></label>
<label>فون<input name="guardian_phone" value="{{ $student->guardian_phone }}" maxlength="32"></label>
<label>شعبہ<select name="program"><option value="hifz" @selected($student->program==='hifz')>حفظ</option><option value="nazra" @selected($student->program==='nazra')>ناظرہ</option><option value="qaida" @selected($student->program==='qaida')>قاعدہ</option></select></label>
<label>داخلہ تاریخ<input type="date" name="admission_date" value="{{ $student->admission_date }}"></label>
<label>سابقہ مدرسہ<input name="previous_institution" value="{{ $student->previous_institution }}" maxlength="180"></label>
<label>حیثیت<select name="status"><option value="active" @selected($student->status==='active')>فعال</option><option value="inactive" @selected($student->status==='inactive')>غیر فعال</option><option value="archived" @selected($student->status==='archived')>آرکائیو</option></select></label>
<label>نوٹس<textarea name="notes" rows="2">{{ $student->notes }}</textarea></label>
<button class="button primary" type="submit">ریکارڈ اپ ڈیٹ کریں</button></form>
<form method="POST" action="{{ route('madrasa.students.destroy', $student->id) }}" onsubmit="return confirm('طالب علم کو آرکائیو کریں؟ تمام پرانا ریکارڈ محفوظ رہے گا۔')">@csrf @method('DELETE')<button class="button secondary" type="submit">آرکائیو کریں</button></form>
</details></td>@endif</tr>
@endforeach
</tbody></table></div>
@endif
</div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2 id="course-progress">حفظ / ناظرہ / قاعدہ نصاب کی تکمیل</h2>
<p>قاری یا منتظم موجودہ پارہ / مرحلہ اور مکمل شدہ پاروں / مراحل کا اندراج کریں۔ والدین کو دکھانے سے پہلے منتظم کی منظوری ضروری ہے۔</p>
@if($studentRows->isEmpty())<p class="empty">طلبہ کا ریکارڈ موجود نہیں۔</p>@else
<div class="table-wrap"><table><thead><tr><th>طالب علم</th><th>شعبہ</th><th>مکمل پارے / مراحل</th><th>موجودہ پارہ / مرحلہ</th><th>والدین کی رسائی</th>@if(in_array($role,['admin','qari'],true))<th>اپ ڈیٹ</th>@endif</tr></thead><tbody>
@foreach($studentRows->where('status','active') as $student)
@php($course = $courseProgress->get($student->id.':'.$student->program))
<tr><td>{{ $student->full_name }}<br><small>{{ $student->student_code }}</small></td><td>{{ ['hifz'=>'حفظ','nazra'=>'ناظرہ','qaida'=>'قاعدہ'][$student->program] ?? $student->program }}</td>
<td>{{ $course?->completed_paras ?? 0 }}</td><td>{{ $course?->current_para ?? 1 }}</td>
<td>
@if($role === 'admin' && $course)
<form method="POST" action="{{ route('madrasa.course-progress.visibility',$course->id) }}">@csrf<input type="hidden" name="parent_visible" value="{{ $course->parent_visible ? 0 : 1 }}"><button class="button secondary" type="submit">{{ $course->parent_visible ? 'منظوری واپس لیں' : 'والدین کو دکھائیں' }}</button></form>
@elseif($role === 'parent')منظور شدہ
@else{{ $course?->parent_visible ? 'منظور شدہ' : 'منظوری باقی' }}@endif
</td>
@if(in_array($role,['admin','qari'],true))
<td><details><summary>پیش رفت درج کریں</summary><form method="POST" action="{{ route('madrasa.course-progress.store') }}" class="form-grid">@csrf
<input type="hidden" name="student_id" value="{{ $student->id }}"><input type="hidden" name="program" value="{{ $student->program }}">
<label>مکمل پارے / مراحل<input type="number" name="completed_paras" min="0" max="30" value="{{ $course?->completed_paras ?? 0 }}" required></label>
<label>موجودہ پارہ / مرحلہ<input type="number" name="current_para" min="1" max="30" value="{{ $course?->current_para ?? 1 }}" required></label>
<label>نوٹ<input name="notes" maxlength="1000" value="{{ $course?->notes }}"></label>
<button class="button primary" type="submit">پیش رفت محفوظ کریں</button></form></details></td>
@endif
</tr>
@endforeach
</tbody></table></div>
@endif
</div>

<h2 id="attendance">حاضری کا ریکارڈ</h2>
@if($attendance->isEmpty())<p class="empty">حاضری کا ریکارڈ موجود نہیں۔</p>
@else
<div class="table-wrap"><table><thead><tr><th>طالب علم</th><th>تاریخ</th><th>سیشن</th><th>حیثیت</th>@if($role === 'admin')<th>انتظام</th>@endif</tr></thead><tbody>
@foreach($attendance as $item)
@php($student = $studentRows->firstWhere('id', $item->student_id))
<tr><td>{{ $student?->full_name ?? 'طالب علم' }}</td><td>{{ $item->attendance_date }}</td><td>{{ ['morning'=>'صبح','afternoon'=>'دوپہر','evening'=>'شام'][$item->session] ?? $item->session }}</td><td>{{ ['present'=>'حاضر','absent'=>'غیر حاضر','late'=>'تاخیر','leave'=>'رخصت'][$item->status] ?? $item->status }}</td>
@if($role === 'admin')<td><details><summary>ترمیم</summary><form method="POST" action="{{ route('madrasa.attendance.update', $item->id) }}" class="form-grid">@csrf @method('PUT')
<label>تاریخ<input type="date" name="attendance_date" value="{{ $item->attendance_date }}" required></label>
<label>سیشن<select name="session"><option value="morning" @selected($item->session==='morning')>صبح</option><option value="afternoon" @selected($item->session==='afternoon')>دوپہر</option><option value="evening" @selected($item->session==='evening')>شام</option></select></label>
<label>حاضری<select name="status"><option value="present" @selected($item->status==='present')>حاضر</option><option value="absent" @selected($item->status==='absent')>غیر حاضر</option><option value="late" @selected($item->status==='late')>تاخیر</option><option value="leave" @selected($item->status==='leave')>رخصت</option></select></label>
<label>نوٹ<input name="remarks" value="{{ $item->remarks }}" maxlength="500"></label><button class="button primary">محفوظ کریں</button></form>
<form method="POST" action="{{ route('madrasa.attendance.destroy', $item->id) }}" onsubmit="return confirm('حاضری حذف کریں؟')">@csrf @method('DELETE')<button class="button secondary">حذف کریں</button></form></details></td>@endif</tr>
@endforeach
</tbody></table></div>
@endif
</div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2 id="progress">روزانہ تعلیمی پیش رفت</h2>
@if($progress->isEmpty())<p class="empty">پیش رفت کا ریکارڈ موجود نہیں۔</p>
@else
<div class="table-wrap"><table><thead><tr><th>طالب علم</th><th>تاریخ</th><th>سبق</th><th>سبقی</th><th>منزل</th><th>والدین کو دکھائیں</th>@if($role === 'admin')<th>انتظام</th>@endif</tr></thead><tbody>
@foreach($progress as $item)
@php($student = $studentRows->firstWhere('id', $item->student_id))
<tr><td>{{ $student?->full_name ?? 'طالب علم' }}</td><td>{{ $item->progress_date }}</td><td>{{ $item->sabaq ?: '—' }}</td><td>{{ $item->sabqi ?: '—' }}</td><td>{{ $item->manzil ?: '—' }}</td><td>
@if($role === 'admin')
<form method="POST" action="{{ route('madrasa.progress.visibility', $item->id) }}">@csrf<input type="hidden" name="parent_visible" value="{{ $item->parent_visible ? 0 : 1 }}"><button class="button secondary" type="submit">{{ $item->parent_visible ? 'منظوری واپس لیں' : 'والدین کو دکھائیں' }}</button></form>
@else{{ $item->parent_visible ? 'منظور شدہ' : 'انتظامیہ کی منظوری باقی' }}@endif
</td>
@if($role === 'admin')<td><details><summary>ترمیم</summary><form method="POST" action="{{ route('madrasa.progress.update', $item->id) }}" class="form-grid">@csrf @method('PUT')
<label>تاریخ<input type="date" name="progress_date" value="{{ $item->progress_date }}" required></label>
<label>سیشن<select name="session"><option value="morning" @selected($item->session==='morning')>صبح</option><option value="afternoon" @selected($item->session==='afternoon')>دوپہر</option><option value="evening" @selected($item->session==='evening')>شام</option></select></label>
<label>سبق<input name="sabaq" value="{{ $item->sabaq }}" maxlength="100"></label><label>سبقی<input name="sabqi" value="{{ $item->sabqi }}" maxlength="100"></label><label>منزل<input name="manzil" value="{{ $item->manzil }}" maxlength="100"></label>
<label>معیار<select name="quality"><option value="">منتخب کریں</option><option value="excellent" @selected($item->quality==='excellent')>بہترین</option><option value="good" @selected($item->quality==='good')>اچھا</option><option value="needs_attention" @selected($item->quality==='needs_attention')>مزید توجہ</option></select></label>
<label>نوٹ<textarea name="notes" rows="2">{{ $item->notes }}</textarea></label><label>والدین کو دکھائیں<select name="parent_visible"><option value="0" @selected(!$item->parent_visible)>نہیں</option><option value="1" @selected($item->parent_visible)>ہاں</option></select></label><button class="button primary">محفوظ کریں</button></form>
<form method="POST" action="{{ route('madrasa.progress.destroy', $item->id) }}" onsubmit="return confirm('پیش رفت کا ریکارڈ حذف کریں؟')">@csrf @method('DELETE')<button class="button secondary">حذف کریں</button></form></details></td>@endif</tr>
@endforeach
</tbody></table></div>
@endif
</div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2 id="exams">امتحانات اور نتائج</h2>
@if(in_array($role, ['admin','qari'], true))
<form method="POST" action="{{ route('madrasa.exams.store') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows->where('status','active') as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>امتحان کی قسم<select name="exam_type" required><option value="quarterly">سہ ماہی</option><option value="semiannual">ششماہی</option><option value="nine_monthly">نوماہی</option><option value="annual">سالانہ</option><option value="monthly">ماہانہ</option><option value="hifz">حفظ کا جائزہ</option><option value="nazra">ناظرہ کا جائزہ</option><option value="qaida">قاعدہ کا جائزہ</option></select></label>
<label>تاریخ<input type="date" name="exam_date" value="{{ now()->toDateString() }}" required></label>
<label>کل نمبر<input type="number" name="total_marks" min="1" max="99999" step="0.01" value="100" required></label>
<label>حاصل کردہ نمبر<input type="number" name="obtained_marks" min="0" max="99999" step="0.01" required></label>
<label>گریڈ<input name="grade" maxlength="20" placeholder="مثلاً A+"></label>
<label>تبصرہ<input name="remarks" maxlength="2000"></label>
<div><button class="button primary" type="submit">نتیجہ محفوظ کریں</button></div>
</form>
@endif
@if($exams->isEmpty())<p class="empty">امتحان کا ریکارڈ موجود نہیں۔</p>@else
<div class="table-wrap"><table><thead><tr><th>طالب علم</th><th>قسم</th><th>تاریخ</th><th>نمبر</th><th>گریڈ</th><th>والدین</th>@if($role === 'admin')<th>انتظام</th>@endif</tr></thead><tbody>
@foreach($exams as $exam)
@php($student = $studentRows->firstWhere('id', $exam->student_id))
<tr><td>{{ $student?->full_name ?? 'طالب علم' }}</td><td>{{ ['quarterly'=>'سہ ماہی','semiannual'=>'ششماہی','nine_monthly'=>'نوماہی','annual'=>'سالانہ','monthly'=>'ماہانہ','hifz'=>'حفظ کا جائزہ','nazra'=>'ناظرہ کا جائزہ','qaida'=>'قاعدہ کا جائزہ'][$exam->exam_type] ?? $exam->exam_type }}</td><td>{{ $exam->exam_date }}</td><td>{{ $exam->obtained_marks }} / {{ $exam->total_marks }}</td><td>{{ $exam->grade ?: '—' }}</td><td>
@if($role === 'admin')
<form method="POST" action="{{ route('madrasa.exams.visibility', $exam->id) }}">@csrf<input type="hidden" name="parent_visible" value="{{ $exam->parent_visible ? 0 : 1 }}"><button class="button secondary" type="submit">{{ $exam->parent_visible ? 'منظوری واپس لیں' : 'والدین کو دکھائیں' }}</button></form>
@else{{ $role === 'parent' ? 'منظور شدہ' : ($exam->parent_visible ? 'منظور شدہ' : 'منظوری باقی') }}@endif
</td>
@if($role === 'admin')<td><details><summary>ترمیم</summary><form method="POST" action="{{ route('madrasa.exams.update', $exam->id) }}" class="form-grid">@csrf @method('PUT')
<label>قسم<select name="exam_type" required>@foreach(['quarterly'=>'سہ ماہی','semiannual'=>'ششماہی','nine_monthly'=>'نوماہی','annual'=>'سالانہ','monthly'=>'ماہانہ','hifz'=>'حفظ کا جائزہ','nazra'=>'ناظرہ کا جائزہ','qaida'=>'قاعدہ کا جائزہ'] as $key=>$label)<option value="{{ $key }}" @selected($exam->exam_type===$key)>{{ $label }}</option>@endforeach</select></label><label>تاریخ<input type="date" name="exam_date" value="{{ $exam->exam_date }}" required></label>
<label>کل نمبر<input type="number" name="total_marks" value="{{ $exam->total_marks }}" min="1" step=".01" required></label><label>حاصل کردہ نمبر<input type="number" name="obtained_marks" value="{{ $exam->obtained_marks }}" min="0" step=".01" required></label><label>گریڈ<input name="grade" value="{{ $exam->grade }}" maxlength="20"></label><label>تبصرہ<input name="remarks" value="{{ $exam->remarks }}"></label>
<label>والدین کو دکھائیں<select name="parent_visible"><option value="0" @selected(!$exam->parent_visible)>نہیں</option><option value="1" @selected($exam->parent_visible)>ہاں</option></select></label><button class="button primary">محفوظ کریں</button></form>
<form method="POST" action="{{ route('madrasa.exams.destroy', $exam->id) }}" onsubmit="return confirm('امتحان کا ریکارڈ حذف کریں؟')">@csrf @method('DELETE')<button class="button secondary">حذف کریں</button></form></details></td>@endif
</tr>
@endforeach
</tbody></table></div>
@endif
</div>

@if($role === 'admin')
<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>سرٹیفکیٹ جاری کریں</h2>
<form method="POST" enctype="multipart/form-data" action="{{ route('madrasa.certificates.store') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows->where('status','active') as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>سرٹیفکیٹ کی قسم<select name="certificate_type"><option value="hifz">حفظ مکمل</option><option value="nazra">ناظرہ مکمل</option><option value="qaida">قاعدہ مکمل</option><option value="participation">شرکت</option></select></label>
<label>اجراء کی تاریخ<input type="date" name="issued_on" value="{{ now()->toDateString() }}" required></label>
<label>جاری کرنے والے کا نام<input name="issued_by_name" maxlength="150"></label>
<label>سابقہ ادارہ (اگر بیرونی سرٹیفکیٹ ہو)<input name="external_institution" maxlength="180"></label>
<label>پہلے سے موجود سرٹیفکیٹ PDF اپلوڈ کریں (اختیاری)<input type="file" name="certificate_pdf" accept="application/pdf"></label>
<div><button class="button primary" type="submit">سرٹیفکیٹ جاری کریں</button></div>
</form>
</div>
@endif
<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2 id="certificates">سرٹیفکیٹس اور آن لائن تصدیق</h2>
@if($certificates->isEmpty())<p class="empty">ابھی کوئی سرٹیفکیٹ جاری نہیں ہوا۔</p>@else
<div class="table-wrap"><table><thead><tr><th>طالب علم</th><th>سرٹیفکیٹ نمبر</th><th>قسم</th><th>تاریخ</th><th>حیثیت</th><th>تصدیق / انتظام</th></tr></thead><tbody>
@foreach($certificates as $cert)
@php($student = $studentRows->firstWhere('id', $cert->student_id))
<tr><td>{{ $student?->full_name ?? 'طالب علم' }}</td><td>{{ $cert->certificate_number }}</td><td>{{ $cert->certificate_type }}</td><td>{{ $cert->issued_on }}</td><td>{{ $cert->status === 'valid' ? 'معتبر' : 'منسوخ' }}</td><td><a href="{{ route('madrasa.certificates.verify', $cert->verification_token) }}" target="_blank" rel="noopener">تصدیقی صفحہ</a>
@if($cert->pdf_path)<br><a href="{{ route('madrasa.certificates.download', $cert->id) }}">PDF ڈاؤن لوڈ</a>@endif
@if($role === 'admin')
<br><a href="{{ route('madrasa.certificates.print', $cert->id) }}" target="_blank" rel="noopener">PDF تیار / پرنٹ</a>
<details><summary>ترمیم</summary><form method="POST" enctype="multipart/form-data" action="{{ route('madrasa.certificates.update', $cert->id) }}" class="form-grid">@csrf @method('PUT')
<label>قسم<input name="certificate_type" value="{{ $cert->certificate_type }}" maxlength="40" required></label>
<label>اجراء تاریخ<input type="date" name="issued_on" value="{{ $cert->issued_on }}" required></label>
<label>جاری کنندہ<input name="issued_by_name" value="{{ $cert->issued_by_name }}" maxlength="150"></label>
<label>بیرونی ادارہ<input name="external_institution" value="{{ $cert->external_institution }}" maxlength="180"></label>
<label>نئی PDF (اختیاری)<input type="file" name="certificate_pdf" accept="application/pdf"></label>
<button class="button primary">اپ ڈیٹ کریں</button></form>
@if($cert->status === 'valid')<form method="POST" action="{{ route('madrasa.certificates.destroy', $cert->id) }}" onsubmit="return confirm('سرٹیفکیٹ منسوخ کریں؟')">@csrf @method('DELETE')<button class="button secondary">سرٹیفکیٹ منسوخ کریں</button></form>@endif
</details>
@endif</td></tr>
@endforeach
</tbody></table></div>
@endif
</div>

@if($role === 'admin')
<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>اعلانات شائع کریں</h2>
<form method="POST" action="{{ route('madrasa.announcements.store') }}" class="form-grid">@csrf
<label>عنوان<input name="title" maxlength="180" required></label>
<label>کس کے لیے؟<select name="audience"><option value="all">سب صارفین</option><option value="qari">صرف قاری</option><option value="parent">صرف والدین</option></select></label>
<label style="grid-column:1/-1">اعلان<textarea name="body" rows="4" maxlength="10000" required></textarea></label>
<label><input type="checkbox" name="is_published" value="1"> ابھی شائع کریں</label>
<div><button class="button primary" type="submit">اعلان محفوظ کریں</button></div>
</form>
</div>
@endif
<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2 id="announcements">اہم اعلانات</h2>
@if($announcements->isEmpty())<p class="empty">فی الحال کوئی شائع شدہ اعلان نہیں۔</p>@else
@foreach($announcements as $announcement)
<article style="padding:.75rem 0;border-bottom:1px solid #ddd"><h3>{{ $announcement->title }}</h3><p>{{ $announcement->body }}</p><small>{{ $announcement->published_at }}</small></article>
@endforeach
@endif
</div>
</div></section>
@endsection
