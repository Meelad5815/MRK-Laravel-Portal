@extends('layouts.app')
@section('title','MRK Digital | Full Stack Website Developer')
@section('description','MRK Digital builds responsive Laravel and WordPress websites, custom web applications and practical automation solutions.')
@section('content')
<main>
<section class="hero"><div class="container hero-grid">
<div><span class="eyebrow">FULL STACK WEBSITE DEVELOPER</span>
<h1>Websites, web apps & digital systems that work for your business.</h1>
<p>MRK Digital creates responsive Laravel and WordPress websites, custom web applications and practical automation solutions.</p>
<div class="actions"><a class="button primary" href="{{ route('services') }}">View Services</a><a class="button secondary" href="{{ route('contact') }}">Start a Project</a>
<a class="text-link hero-project-link" href="{{ route('projects.index') }}">Explore Portfolio →</a></div>
<div class="trust-row"><span>Laravel</span><span>WordPress</span><span>PHP</span><span>Automation</span></div>
</div>
<div class="hero-card"><div class="orb">MRK</div><h2>Digital Solutions</h2><p>Development • Automation • Online Services</p><div class="mini-grid"><div><b>Web</b><small>Modern & responsive</small></div><div><b>Apps</b><small>Custom systems</small></div><div><b>Automation</b><small>PLC & Arduino</small></div><div><b>Support</b><small>Practical solutions</small></div></div></div>
</div></section>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">CORE SERVICES</span><h2>One professional place for digital work.</h2><p>From a business website to a custom management portal, the goal is simple: clean technology that solves a real problem.</p></div>
<div class="cards">
@foreach($services as $service)
<article><div class="icon">{{ $service->icon ?: "MRK" }}</div><h3>{{ $service->title }}</h3><p>{{ $service->summary }}</p><a class="text-link" href="{{ route('services.show', $service->slug) }}">Learn more</a></article>
@endforeach
</div></div></section>
<section class="cta"><div class="container cta-box"><div><span class="eyebrow">HAVE A PROJECT?</span><h2>Let's turn your idea into a working system.</h2></div><a class="button primary" href="{{ route('contact') }}">Contact MRK Digital</a></div></section>
</main>
@endsection