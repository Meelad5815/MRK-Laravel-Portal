@extends('layouts.app')

@section('title', ($mode === 'create' ? 'Add Customer' : 'Edit Customer') . ' | MRK Digital')

@section('content')
<div class="section narrow">
    <div class="section-heading">
        <div><span class="eyebrow">CRM</span><h1>{{ $mode === 'create' ? 'Add Customer' : 'Edit Customer' }}</h1></div>
        <a class="button secondary" href="{{ route('admin.customers.index') }}">Back</a>
    </div>

    <form class="card form-grid" method="post" action="{{ $mode === 'create' ? route('admin.customers.store') : route('admin.customers.update', $customer) }}">
        @csrf
        @if($mode === 'edit') @method('PUT') @endif

        <label>Name <input name="name" required maxlength="120" value="{{ old('name', $customer->name) }}"></label>
        <label>Email <input type="email" name="email" maxlength="255" value="{{ old('email', $customer->email) }}"></label>
        <label>Phone <input name="phone" maxlength="30" value="{{ old('phone', $customer->phone) }}"></label>
        <label>Company <input name="company" maxlength="160" value="{{ old('company', $customer->company) }}"></label>
        <label>Source <input name="source" maxlength="50" placeholder="Website, WhatsApp, Fiverr..." value="{{ old('source', $customer->source) }}"></label>
        <label>Status <select name="status">@foreach(['active','inactive'] as $v)<option value="{{ $v }}" @selected(old('status',$customer->status)===$v)>{{ ucfirst($v) }}</option>@endforeach</select></label>
        <label>Priority <select name="priority">@foreach(['low','normal','high','urgent'] as $v)<option value="{{ $v }}" @selected(old('priority',$customer->priority)===$v)>{{ ucfirst($v) }}</option>@endforeach</select></label>
        <label>Last contacted <input type="datetime-local" name="last_contacted_at" value="{{ old('last_contacted_at', $customer->last_contacted_at?->format('Y-m-d\TH:i')) }}"></label>
        <label>Next follow-up <input type="datetime-local" name="next_follow_up_at" value="{{ old('next_follow_up_at', $customer->next_follow_up_at?->format('Y-m-d\TH:i')) }}"></label>
        <label class="full">Notes <textarea name="notes" rows="6" maxlength="10000">{{ old('notes', $customer->notes) }}</textarea></label>

        @if($errors->any())
            <div class="full"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
        @endif
        <div class="full"><button class="button primary" type="submit">{{ $mode === 'create' ? 'Create Customer' : 'Save Changes' }}</button></div>
    </form>
</div>
@endsection