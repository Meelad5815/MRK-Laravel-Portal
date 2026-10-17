@extends('layouts.app')
@section('title','Services | MRK Digital')
@section('description','Explore MRK Digital services: Laravel and WordPress development, web applications, e-commerce product listing, virtual assistance, technical SEO and automation.')
@section('content')
<main>
<section class="page-hero"><div class="container narrow"><span class="eyebrow">SERVICES</span><h1>Digital solutions built around real requirements.</h1><p>Explore the service catalogue and choose the solution that matches your goal.</p></div></section>
<section class="section"><div class="container"><div class="service-grid">
@forelse($services as $service)
<article class="service-card"><span class="icon">{{ $service->icon ?: 'MRK' }}</span><h3>{{ $service->title }}</h3><p>{{ $service->summary }}</p><ul><li>{{ $service->category }}</li><li>Responsive delivery</li><li>Project-specific workflow</li></ul><a class="text-link" href="{{ route('services.show', $service->slug) }}">View service</a></article>
@empty
<div class="card"><h2>Services are being prepared</h2><p>Tell us what you need and we can discuss a suitable solution.</p><a class="button primary" href="{{ route('contact') }}">Discuss your project</a></div>
@endforelse
</div></div></section>
<section class="cta"><div class="container"><div class="cta-box"><div><span class="eyebrow">START A PROJECT</span><h2>Have a requirement? Let's define it.</h2><p>Send the project details and the required technology or outcome.</p></div><a class="button primary" href="{{ route('contact') }}">Contact MRK Digital</a></div></div></section>
</main>
@endsection
