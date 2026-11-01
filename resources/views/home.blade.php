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
@forelse($services as $service)
<article><div class="icon">{{ $service->icon ?: 'MRK' }}</div><h3>{{ $service->title }}</h3><p>{{ $service->summary }}</p><a class="text-link" href="{{ route('services.show', $service->slug) }}">Learn more</a></article>
@empty
<article><div class="icon">MRK</div><h3>Let’s discuss your project</h3><p>Tell us what you want to build and we’ll help define a suitable solution.</p><a class="text-link" href="{{ route('contact') }}">Start a project</a></article>
@endforelse
</div></div></section>
<section class="section platform-directory"><div class="container">
<div class="section-heading"><span class="eyebrow">MRK UNIVERSAL PLATFORM</span><h2>Explore the digital workspace</h2><p>Practical tools and services for everyday digital work, business operations, learning and development.</p></div>
<div class="cards">
<article><div class="icon">01</div><h3>Online Services</h3><p>Explore website development, digital support and available online service categories.</p><a class="text-link" href="{{ route('services') }}">Browse services</a></article>
<article><div class="icon">02</div><h3>Free Tools</h3><p>Use browser-based utilities for images, files, text, SEO and developer tasks.</p><a class="text-link" href="{{ route('tools') }}">Open free tools</a></article>
<article><div class="icon">03</div><h3>Projects & Portfolio</h3><p>View published projects, implementation examples and technology work.</p><a class="text-link" href="{{ route('projects.index') }}">Explore projects</a></article>
<article><div class="icon">04</div><h3>Knowledge & Resources</h3><p>Read guides, tutorials and practical resources for digital skills and technology.</p><a class="text-link" href="{{ route('pages.index') }}">Browse resources</a></article>
<article><div class="icon">05</div><h3>Articles & Updates</h3><p>Explore published articles, announcements and learning content.</p><a class="text-link" href="{{ route('blog.index') }}">Read the blog</a></article>
<article><div class="icon">06</div><h3>Client Support</h3><p>Send a project enquiry or request help with a digital requirement.</p><a class="text-link" href="{{ route('contact') }}">Contact MRK</a></article>
</div></div></section>
<section class="cta"><div class="container cta-box"><div><span class="eyebrow">HAVE A PROJECT?</span><h2>Let's turn your idea into a working system.</h2></div><a class="button primary" href="{{ route('contact') }}">Contact MRK Digital</a></div></section>
</main>
@endsection