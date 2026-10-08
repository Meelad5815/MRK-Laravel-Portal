@extends('layouts.app')
@section('title', $service->seo_title ?: $service->title . ' | MRK Digital')
@section('description', $service->seo_description ?: $service->summary)
@section('content')
<main>
<section class="page-hero">
<div class="container narrow">
<span class="eyebrow">{{ $service->category }}</span>
<h1>{{ $service->title }}</h1>
<p>{{ $service->summary }}</p>
</div>
</section>
<section class="section">
<div class="container service-detail">
<div class="service-detail-main">
<div class="service-detail-icon">{{ $service->icon ?: 'MRK' }}</div>
<div class="rich-content">{!! nl2br(e($service->description)) !!}</div>
</div>
<aside class="service-detail-side">
<span class="eyebrow">READY TO START?</span>
<h2>Build this solution around your requirement.</h2>
<p>Send the details and we can define the scope, technology and next steps.</p>
<a class="button primary" href="{{ $service->cta_url ?: route('contact') }}">{{ $service->cta_label ?: 'Start a Project' }}</a>
<a class="text-link" href="{{ route('services') }}">← Back to services</a>
</aside>
</div>
</section>
</main>
@endsection
