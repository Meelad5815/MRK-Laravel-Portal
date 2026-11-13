@extends('layouts.app')
@section('title','Birth Certificate Manager | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<div class="section">
  <div class="section-heading"><div><span class="eyebrow">MRK DIGITAL • CIVIL REGISTRATION</span><h1>Birth Certificate Manager</h1><p>پیدائش سرٹیفکیٹ کی درخواست، والدین کا ریکارڈ، دستاویزات، فیس اور رسید۔</p></div><a class="button secondary" href="{{ route('admin.work.index') }}">Work & Cashbook</a></div>
  <div class="alert">اہم: یہ MRK کا ریکارڈ مینیجر ہے، سرکاری Union Council درخواست یا منظوری نہیں۔ سرکاری فیس اور تاخیر سے اندراج کے قواعد متعلقہ مقامی رجسٹرار سے تصدیق کرکے درج کریں۔ والدین کے CNIC نمبر encrypted صورت میں محفوظ ہوتے ہیں۔</div>
  <div class="table-card" style="padding:1.25rem;margin:1.5rem 0">
    <h2>نئی درخواست درج کریں</h2>
    <form method="post" action="{{ route('admin.birth-certificates.store') }}" class="form-grid">@csrf
      <label>بچے کا مکمل نام *<input name="child_name" value="{{ old('child_name') }}" required maxlength="160"></label>
      <label>تاریخ پیدائش *<input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" max="{{ now()->toDateString() }}" required></label>
      <label>جنس *<select name="gender" required><option value="male">لڑکا</option><option value="female">لڑکی</option><option value="other">دیگر</option></select></label>
      <label>والد کا نام *<input name="father_name" value="{{ old('father_name') }}" required maxlength="160"></label>
      <label>والد کا CNIC<input name="father_cnic" value="{{ old('father_cnic') }}" placeholder="00000-0000000-0" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}"></label>
      <label>والدہ کا نام *<input name="mother_name" value="{{ old('mother_name') }}" required maxlength="160"></label>
      <label>والدہ کا CNIC<input name="mother_cnic" value="{{ old('mother_cnic') }}" placeholder="00000-0000000-0" pattern="[0-9]{5}-[0-9]{7}-[0-9]{1}"></label>
      <label>نکاح کا ریکارڈ *<select name="marital_record"><option value="nikah_nama">نکاح نامہ موجود</option><option value="not_available">دستیاب نہیں</option><option value="other">دیگر ثبوت</option></select></label>
      <label>تاریخ نکاح (اگر معلوم ہو)<input type="date" name="nikah_date" max="{{ now()->toDateString() }}"></label>
      <label>پیدائش کی جگہ *<select name="birth_place_type"><option value="hospital">ہسپتال</option><option value="home">گھر</option><option value="other">دیگر</option></select></label>
      <label>ہسپتال / مقام کا نام<input name="hospital_name" maxlength="180"></label>
      <label>ضلع<input name="birth_district" value="{{ old('birth_district','Khanewal') }}" maxlength="100"></label>
      <label>متعلقہ یونین کونسل / میونسپل کمیٹی *<input name="union_council" value="{{ old('union_council') }}" required maxlength="160"></label>
      <label>اندراج کی نوعیت *<select name="registration_type"><option value="normal">عام اندراج</option><option value="late">تاخیر سے اندراج</option></select></label>
      <label>پیدائش کا ثبوت<select name="birth_evidence"><option value="">منتخب کریں</option><option>Hospital birth slip</option><option>Hospital record / verification</option><option>Vaccination card</option><option>School record</option><option>Affidavit / other proof</option></select></label>
      <fieldset class="full"><legend>دستاویزات کی چیک لسٹ</legend>
        @foreach(['father_cnic'=>'والد کا CNIC','mother_cnic'=>'والدہ کا CNIC','nikah_nama'=>'نکاح نامہ','hospital_record'=>'ہسپتال کا ریکارڈ / تصدیق','vaccination_card'=>'ویکسینیشن کارڈ','school_record'=>'اسکول ریکارڈ','affidavit'=>'حلف نامہ / دیگر ثبوت','council_form'=>'یونین کونسل فارم'] as $key=>$label)
          <label class="check"><input type="checkbox" name="documents[]" value="{{ $key }}" @checked(in_array($key,old('documents',[])))> {{ $label }}</label>
        @endforeach
      </fieldset>
      <label>سرکاری فیس (PKR) *<input type="number" name="government_fee" min="0" step=".01" value="{{ old('government_fee',0) }}" required></label>
      <label>MRK سروس فیس (PKR) *<input type="number" name="mrk_service_fee" min="0" step=".01" value="{{ old('mrk_service_fee',0) }}" required></label>
      <label>وصول شدہ رقم (PKR) *<input type="number" name="paid_amount" min="0" step=".01" value="{{ old('paid_amount',0) }}" required></label>
      <label class="full">نوٹس / فالو اپ<textarea name="notes" rows="3">{{ old('notes') }}</textarea></label>
      <div class="full"><button class="button primary" type="submit">درخواست محفوظ کریں اور رسید بنائیں</button></div>
    </form>
  </div>
  <div class="table-card" style="padding:1.25rem">
    <h2>تمام درخواستیں</h2>
    <form method="get" class="filter-bar"><input type="search" name="search" value="{{ request('search') }}" placeholder="درخواست نمبر، بچے یا والدین کا نام"><select name="status"><option value="">تمام اسٹیٹس</option>@foreach($statuses as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select><button class="button secondary">تلاش کریں</button></form>
    <div class="table-wrap"><table><thead><tr><th>درخواست</th><th>بچے کا نام</th><th>والدین</th><th>تاریخ پیدائش</th><th>فیس / بقایا</th><th>اسٹیٹس</th><th>رسید</th></tr></thead><tbody>
    @forelse($cases as $c)<tr><td><strong>{{ $c->reference }}</strong><br><small>{{ $c->created_at->format('d M Y') }}</small></td><td>{{ $c->child_name }}</td><td>{{ $c->father_name }}<br><small>{{ $c->mother_name }}</small></td><td>{{ $c->date_of_birth->format('d M Y') }}</td><td>PKR {{ number_format((float)$c->government_fee+(float)$c->mrk_service_fee,2) }}<br><small>بقایا: {{ number_format((float)$c->government_fee+(float)$c->mrk_service_fee-(float)$c->paid_amount,2) }}</small></td><td><form method="post" action="{{ route('admin.birth-certificates.status',$c) }}">@csrf @method('PATCH')<select name="status" onchange="this.form.submit()">@foreach($statuses as $s)<option value="{{ $s }}" @selected($c->status===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select></form></td><td><a class="button secondary" href="{{ route('admin.birth-certificates.receipt',$c) }}">رسید</a></td></tr>
    @empty<tr><td colspan="7">ابھی کوئی درخواست درج نہیں ہوئی۔</td></tr>@endforelse
    </tbody></table></div>{{ $cases->links() }}
  </div>
</div>
<style>
.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.form-grid label{display:flex;flex-direction:column;gap:.4rem;font-weight:600}.form-grid input,.form-grid select,.form-grid textarea{width:100%;padding:.75rem;border:1px solid #cbd5e1;border-radius:9px;background:#fff}.form-grid .full{grid-column:1/-1}.form-grid fieldset{border:1px solid #cbd5e1;border-radius:9px;padding:1rem;display:flex;flex-wrap:wrap;gap:1rem}.form-grid .check{display:flex;align-items:center;gap:.45rem;font-weight:400}.form-grid .check input{width:auto}.table-wrap{overflow-x:auto}.table-wrap table{min-width:900px;width:100%}@media(max-width:640px){.form-grid{grid-template-columns:1fr}.form-grid .full{grid-column:auto}}
</style>
@endsection
