@extends('layouts.app')
@section('title','Work, Customers & Cashbook | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<div class="section">
<div class="section-heading"><div><span class="eyebrow">MRK DIGITAL • OPERATIONS</span><h1>Work, Customers & Cashbook</h1><p>کسٹمر کا کام، اسٹیٹس، رسید، آمدنی اور اخراجات ایک جگہ۔</p></div><a class="button secondary" href="{{ route('admin.online-work') }}">Online Work Hub</a></div>

<div class="stats">
<div><span>Pending work</span><strong>{{ $pendingCount }}</strong></div>
<div><span>In progress</span><strong>{{ $progressCount }}</strong></div>
<div><span>Completed</span><strong>{{ $completedCount }}</strong></div>
<div><span>Monthly balance</span><strong>PKR {{ number_format($balance,2) }}</strong></div>
</div>
<div class="stats" style="margin-top:1rem">
<div><span>Monthly income</span><strong>PKR {{ number_format($income,2) }}</strong></div>
<div><span>Monthly expenses</span><strong>PKR {{ number_format($expense,2) }}</strong></div>
</div>

<div class="table-card" style="padding:1.25rem;margin:1.5rem 0">
<h2>نیا کسٹمر ورک آرڈر</h2>
<form method="post" action="{{ route('admin.work.store') }}" class="form-grid">@csrf
<label>Existing customer (optional)<select name="customer_id"><option value="">Walk-in / New customer</option>@foreach($customers as $c)<option value="{{ $c->id }}" @selected(old('customer_id')==$c->id)>{{ $c->name }} — {{ $c->phone }}</option>@endforeach</select></label>
<label>Customer name *<input name="customer_name" value="{{ old('customer_name') }}" required maxlength="120"></label>
<label>Mobile number<input name="customer_phone" value="{{ old('customer_phone') }}" maxlength="30"></label>
<label>Service name *<input name="service_name" value="{{ old('service_name') }}" placeholder="مثلاً NADRA / Passport / FBR" required maxlength="160"></label>
<label>Government fee (PKR)<input type="number" name="government_fee" min="0" step=".01" value="{{ old('government_fee',0) }}" required></label>
<label>MRK service fee (PKR)<input type="number" name="service_fee" min="0" step=".01" value="{{ old('service_fee',0) }}" required></label>
<label>Received amount (PKR)<input type="number" name="paid_amount" min="0" step=".01" value="{{ old('paid_amount',0) }}" required></label>
<label>Expected completion date<input type="date" name="due_date" value="{{ old('due_date') }}"></label>
<label class="full">Work details<textarea name="description" rows="2">{{ old('description') }}</textarea></label>
<label class="full">Internal notes<textarea name="notes" rows="2">{{ old('notes') }}</textarea></label>
<div class="full"><button class="button primary" type="submit">Create Work Order & Receipt</button></div>
</form>
@foreach($errors->all() as $error)<p class="alert error">{{ $error }}</p>@endforeach
</div>

<div class="table-card" style="padding:1.25rem;margin:1.5rem 0">
<h2>Work tracking / تمام درخواستیں</h2>
<form method="get" class="filter-bar"><input type="search" name="search" value="{{ request('search') }}" placeholder="نام، موبائل، سروس یا درخواست نمبر"><select name="status"><option value="">All statuses</option>@foreach(['pending','in_progress','completed','cancelled'] as $s)<option value="{{ $s }}" @selected(request('status')===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select><button class="button secondary">Search</button></form>
<div class="table-wrap"><table><thead><tr><th>Request</th><th>Customer</th><th>Service</th><th>Total / Paid</th><th>Status</th><th>Receipt</th></tr></thead><tbody>
@forelse($workOrders as $w)<tr>
<td><strong>{{ $w->reference }}</strong><br><small>{{ $w->created_at->format('d M Y') }}</small></td>
<td>{{ $w->customer_name }}<br><small>{{ $w->customer_phone }}</small></td><td>{{ $w->service_name }}</td>
<td>PKR {{ number_format($w->total_amount,2) }}<br><small>Paid {{ number_format($w->paid_amount,2) }} · Due {{ number_format($w->total_amount-$w->paid_amount,2) }}</small></td>
<td><form method="post" action="{{ route('admin.work.status',$w) }}">@csrf @method('PATCH')<select name="status" onchange="this.form.submit()">@foreach(['pending','in_progress','completed','cancelled'] as $s)<option value="{{ $s }}" @selected($w->status===$s)>{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach</select></form></td>
<td><a class="button secondary" href="{{ route('admin.work.receipt',$w) }}">Print receipt</a></td>
</tr>@empty<tr><td colspan="6">ابھی کوئی ورک آرڈر درج نہیں ہوا۔</td></tr>@endforelse
</tbody></table></div>{{ $workOrders->links() }}
</div>

<div class="table-card" style="padding:1.25rem;margin:1.5rem 0">
<h2>آمدنی / اخراجات درج کریں</h2>
<form method="post" action="{{ route('admin.cash.store') }}" class="form-grid">@csrf
<label>Type<select name="type"><option value="income">Income</option><option value="expense">Expense</option></select></label>
<label>Category<input name="category" required maxlength="100" placeholder="مثلاً Service income / Internet bill"></label>
<label>Description<input name="description" required maxlength="180"></label>
<label>Amount (PKR)<input type="number" name="amount" required min=".01" step=".01"></label>
<label>Date<input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required></label>
<label>Customer (optional)<select name="customer_id"><option value="">Not linked</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></label>
<div class="full"><button class="button primary">Save transaction</button></div>
</form>
</div>

<div class="table-card" style="padding:1.25rem;margin:1.5rem 0">
<h2>Cashbook / مالی ریکارڈ</h2>
<form method="get" class="filter-bar"><input type="month" name="month" value="{{ request('month',now()->format('Y-m')) }}"><button class="button secondary">Filter month</button><a class="button secondary" href="{{ route('admin.work.index') }}">Current month</a></form>
<div class="table-wrap"><table><thead><tr><th>Date</th><th>Type</th><th>Category</th><th>Description</th><th>Amount</th></tr></thead><tbody>
@forelse($transactions as $t)<tr><td>{{ $t->transaction_date->format('d M Y') }}</td><td>{{ ucfirst($t->type) }}</td><td>{{ $t->category }}</td><td>{{ $t->description }}</td><td>PKR {{ number_format($t->amount,2) }}</td></tr>@empty<tr><td colspan="5">No cash transactions for this period.</td></tr>@endforelse
</tbody></table></div>{{ $transactions->links() }}
</div>
</div>
<style>
.form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.form-grid label{display:flex;flex-direction:column;gap:.4rem;font-weight:600}.form-grid input,.form-grid select,.form-grid textarea{width:100%;padding:.75rem;border:1px solid #cbd5e1;border-radius:9px;background:#fff}.form-grid .full{grid-column:1/-1}.table-wrap{overflow-x:auto}.table-wrap table{min-width:760px;width:100%}
@media(max-width:640px){.form-grid{grid-template-columns:1fr}.form-grid .full{grid-column:auto}}
</style>
@endsection
