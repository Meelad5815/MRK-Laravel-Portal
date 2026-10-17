@extends('layouts.app')
@section('title','Guides and Resources | MRK Digital')
@section('description','Practical reference pages about websites, web applications, SEO, e-commerce, online services, data workflows and automation.')
@section('content')
<main>
<section class="page-hero"><div class="container narrow"><span class="eyebrow">RESOURCE CENTER</span><h1>Guides and practical resources</h1><p>Reference pages to help plan digital projects, improve workflows and prepare better requirements.</p></div></section>
<section class="section"><div class="container"><div class="cards">
@forelse($pages as $page)
<article class="card"><span class="eyebrow">{{ $page->category ?: 'RESOURCE' }}</span><h2><a href="{{ route('pages.show', $page->slug) }}">{{ $page->title }}</a></h2><p>{{ $page->excerpt }}</p><a class="text-link" href="{{ route('pages.show', $page->slug) }}">Read guide →</a></article>
@empty
<div class="card"><h2>Resources are being prepared</h2><p>Contact us if you need help planning a digital project.</p><a class="button primary" href="{{ route('contact') }}">Contact MRK Digital</a></div>
@endforelse
</div>{{ $pages->links() }}</div></section>
</main>
@endsection
