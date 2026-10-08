@extends('layouts.app')
@section('title', ($mode === 'edit' ? 'Edit Service' : 'Add Service') . ' | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">ADMIN CMS</span><h1>{{ $mode === 'edit' ? 'Edit Service' : 'Add Service' }}</h1><p>Keep service content clear, useful and SEO-ready.</p></div></section>
<section class="section"><div class="container narrow">
<form method="POST" action="{{ $mode === 'edit' ? route('admin.services.update',$service) : route('admin.services.store') }}" class="form-card">
@csrf
@if($mode === 'edit') @method('PUT') @endif
<label>Title<input name="title" value="{{ old('title',$service->title) }}" required maxlength="160"></label>
<label>Summary<input name="summary" value="{{ old('summary',$service->summary) }}" required maxlength="300"></label>
<label>Description<textarea name="description" rows="10" required maxlength="15000">{{ old('description',$service->description) }}</textarea></label>
<div class="form-grid">
<label>Category<input name="category" value="{{ old('category',$service->category) }}" required maxlength="80"></label>
<label>Icon / short code<input name="icon" value="{{ old('icon',$service->icon) }}" maxlength="30" placeholder="01"></label>
<label>Status<select name="status"><option value="published" @selected(old('status',$service->status)==='published')>Published</option><option value="draft" @selected(old('status',$service->status)==='draft')>Draft</option></select></label>
<label>Display order<input type="number" name="sort_order" value="{{ old('sort_order',$service->sort_order) }}" min="0" max="9999" required></label>
</div>
<label class="checkbox-label"><input type="checkbox" name="featured" value="1" @checked(old('featured',$service->featured))> Featured service</label>
<div class="form-grid">
<label>CTA label<input name="cta_label" value="{{ old('cta_label',$service->cta_label) }}" maxlength="80"></label>
<label>CTA URL<input name="cta_url" type="url" value="{{ old('cta_url',$service->cta_url) }}" maxlength="500" placeholder="https://..."></label>
<label>SEO title<input name="seo_title" value="{{ old('seo_title',$service->seo_title) }}" maxlength="180"></label>
<label>SEO description<input name="seo_description" value="{{ old('seo_description',$service->seo_description) }}" maxlength="300"></label>
</div>
<div class="actions"><button class="button primary" type="submit">{{ $mode === 'edit' ? 'Update Service' : 'Create Service' }}</button><a class="button secondary" href="{{ route('admin.services.index') }}">Cancel</a></div>
</form>
</div></section>
@endsection
