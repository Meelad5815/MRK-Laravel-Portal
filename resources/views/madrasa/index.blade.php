@extends('layouts.app')
@section('title', 'MRK Madrasa Portal')
@section('robots', 'noindex,nofollow')
@section('content')
<section class="page-hero"><div class="container narrow">
    <span class="eyebrow">MRK MADRASA 58</span>
    <h1>مدرسۃ المدینہ مینجمنٹ پورٹل</h1>
    <p>طلبہ، اساتذہ، حاضری اور تعلیمی پیش رفت کا محفوظ انتظام</p>
    <p><strong>آپ کا کردار:</strong> {{ ['admin' => 'منتظم', 'qari' => 'قاری / معلم', 'parent' => 'والدین'][$role] ?? $role }}</p>
</div></section>
<section class="section"><div class="container">
@if(session('temporary_account_password'))
<div class="alert success"><strong>عارضی پاس ورڈ (صرف ابھی دکھایا جا رہا ہے):</strong><code dir="ltr">{{ session('temporary_account_password') }}</code><p>اسے متعلقہ صارف تک محفوظ طریقے سے پہنچائیں اور پہلی بار لاگ اِن کے بعد تبدیل کروائیں۔</p></div>
@endif
@if($role === 'admin')
<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>نیا طالب علم داخل کریں</h2>
<form method="POST" action="{{ route('madrasa.students.store') }}" class="form-grid">@csrf
<label>طالب علم کا نام<input name="full_name" maxlength="150" required></label>
<label>سرپرست کا نام<input name="guardian_name" maxlength="150" required></label>
<label>سرپرست کا فون<input name="guardian_phone" maxlength="32" inputmode="tel"></label>
<label>تعلیمی شعبہ<select name="program" required><option value="hifz">حفظ</option><option value="nazra">ناظرہ</option><option value="qaida">قاعدہ</option></select></label>
<label>داخلہ کی تاریخ<input type="date" name="admission_date"></label>
<div><button class="button primary" type="submit">داخلہ محفوظ کریں</button></div>
</form></div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>قاری یا والدین کا اکاؤنٹ بنائیں</h2>
<form method="POST" action="{{ route('madrasa.accounts.store') }}" class="form-grid">@csrf
<label>پورا نام<input name="name" required maxlength="100"></label>
<label>ای میل<input type="email" name="email" required maxlength="255" dir="ltr"></label>
<label>کردار<select name="madrasa_role"><option value="qari">قاری</option><option value="parent">والدین</option></select></label>
<div><button class="button primary" type="submit">اکاؤنٹ بنائیں</button></div>
</form></div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>قاری کو طالب علم تفویض کریں</h2>
<form method="POST" action="{{ route('madrasa.assign-qari') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>قاری<select name="qari_user_id" required>@foreach($qariUsers as $qari)<option value="{{ $qari->id }}">{{ $qari->name }} ({{ $qari->email }})</option>@endforeach</select></label>
<div><button class="button primary" type="submit">تفویض کریں</button></div>
</form></div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>والدین کا تصدیق شدہ تعلق قائم کریں</h2>
<form method="POST" action="{{ route('madrasa.link-parent') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>والدین کا اکاؤنٹ<select name="parent_user_id" required>@foreach($parentUsers as $parent)<option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->email }})</option>@endforeach</select></label>
<div><button class="button primary" type="submit">تعلق تصدیق کریں</button></div>
</form></div>
@endif

@if(in_array($role, ['admin','qari'], true))
<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>روزانہ حاضری درج کریں</h2>
<form method="POST" action="{{ route('madrasa.attendance.store') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
<label>تاریخ<input type="date" name="attendance_date" value="{{ now()->toDateString() }}" required></label>
<label>سیشن<select name="session"><option value="morning">صبح</option><option value="afternoon">دوپہر</option><option value="evening">شام</option></select></label>
<label>حاضری<select name="status"><option value="present">حاضر</option><option value="absent">غیر حاضر</option><option value="late">تاخیر سے</option><option value="leave">رخصت</option></select></label>
<label>نوٹ<input name="remarks" maxlength="500"></label>
<div><button class="button primary" type="submit">حاضری محفوظ کریں</button></div>
</form></div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>حفظ و ناظرہ کی روزانہ پیش رفت</h2>
<form method="POST" action="{{ route('madrasa.progress.store') }}" class="form-grid">@csrf
<label>طالب علم<select name="student_id" required>@foreach($studentRows as $student)<option value="{{ $student->id }}">{{ $student->student_code }} — {{ $student->full_name }}</option>@endforeach</select></label>
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
<h2>طلبہ کا ریکارڈ</h2>
@if($studentRows->isEmpty())
<p class="empty">ابھی اس پورٹل کے لیے کوئی طالب علم دستیاب نہیں۔</p>
@else
<div class="table-wrap"><table><thead><tr><th>کوڈ</th><th>نام</th><th>سرپرست</th><th>شعبہ</th><th>حیثیت</th></tr></thead><tbody>
@foreach($studentRows as $student)
<tr><td>{{ $student->student_code }}</td><td>{{ $student->full_name }}</td><td>{{ $role === 'admin' ? $student->guardian_name : '—' }}</td><td>{{ ['hifz'=>'حفظ','nazra'=>'ناظرہ','qaida'=>'قاعدہ'][$student->program] ?? $student->program }}</td><td>{{ $student->status === 'active' ? 'فعال' : $student->status }}</td></tr>
@endforeach
</tbody></table></div>
@endif
</div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>حاضری کا ریکارڈ</h2>
@if($attendance->isEmpty())<p class="empty">حاضری کا ریکارڈ موجود نہیں۔</p>
@else
<div class="table-wrap"><table><thead><tr><th>طالب علم</th><th>تاریخ</th><th>سیشن</th><th>حیثیت</th></tr></thead><tbody>
@foreach($attendance as $item)
@php($student = $studentRows->firstWhere('id', $item->student_id))
<tr><td>{{ $student?->full_name ?? 'طالب علم' }}</td><td>{{ $item->attendance_date }}</td><td>{{ ['morning'=>'صبح','afternoon'=>'دوپہر','evening'=>'شام'][$item->session] ?? $item->session }}</td><td>{{ ['present'=>'حاضر','absent'=>'غیر حاضر','late'=>'تاخیر','leave'=>'رخصت'][$item->status] ?? $item->status }}</td></tr>
@endforeach
</tbody></table></div>
@endif
</div>

<div class="table-card" style="padding:1.25rem;margin-bottom:1.25rem">
<h2>روزانہ تعلیمی پیش رفت</h2>
@if($progress->isEmpty())<p class="empty">پیش رفت کا ریکارڈ موجود نہیں۔</p>
@else
<div class="table-wrap"><table><thead><tr><th>طالب علم</th><th>تاریخ</th><th>سبق</th><th>سبقی</th><th>منزل</th><th>والدین کو دکھائیں</th></tr></thead><tbody>
@foreach($progress as $item)
@php($student = $studentRows->firstWhere('id', $item->student_id))
<tr><td>{{ $student?->full_name ?? 'طالب علم' }}</td><td>{{ $item->progress_date }}</td><td>{{ $item->sabaq ?: '—' }}</td><td>{{ $item->sabqi ?: '—' }}</td><td>{{ $item->manzil ?: '—' }}</td><td>
@if($role === 'admin')
<form method="POST" action="{{ route('madrasa.progress.visibility', $item->id) }}">@csrf<input type="hidden" name="parent_visible" value="{{ $item->parent_visible ? 0 : 1 }}"><button class="button secondary" type="submit">{{ $item->parent_visible ? 'منظوری واپس لیں' : 'والدین کو دکھائیں' }}</button></form>
@else{{ $item->parent_visible ? 'منظور شدہ' : 'انتظامیہ کی منظوری باقی' }}@endif
</td></tr>
@endforeach
</tbody></table></div>
@endif
</div>
</div></section>
@endsection
