@extends('layouts.app')
@section('title',$page->seo_title ?: $page->title.' | MRK Digital')
@section('description',$page->seo_description ?: $page->excerpt)
@section('content')
<main>
<section class="page-hero"><div class="container narrow"><span class="eyebrow">{{ $page->category ?: 'RESOURCE' }}</span><h1>{{ $page->title }}</h1><p>{{ $page->excerpt }}</p></div></section>
<section class="section"><article class="container narrow document-card rich-content">{!! \Illuminate\Support\Str::markdown(str_replace(['\\r\\n', '\\n', '\\r'], ["\n", "\n", "\n"], $page->content)) !!}<p><a class="button primary" href="{{ route('contact') }}">Discuss a project</a></p><p><a class="text-link" href="{{ route('pages.index') }}">← All guides and resources</a></p></article></section>
</main>
@endsection
