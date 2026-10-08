@extends('layouts.app')
@section('title','Edit '.$project->title.' | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')<section class="page-hero"><div class="container narrow"><span class="eyebrow">ADMIN CMS</span><h1>Edit project.</h1><p>Update content, SEO-friendly summary, technology and publication status.</p></div></section><section class="section"><div class="container narrow">@include('admin.projects.form')</div></section>@endsection
