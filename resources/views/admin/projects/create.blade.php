@extends('layouts.app')
@section('title','Add Project | MRK Digital')
@section('robots','noindex,nofollow')
@section('content')<section class="page-hero"><div class="container narrow"><span class="eyebrow">ADMIN CMS</span><h1>Add a project.</h1><p>Create a portfolio item, keep it as a draft, or publish it immediately.</p></div></section><section class="section"><div class="container narrow">@include('admin.projects.form')</div></section>@endsection
