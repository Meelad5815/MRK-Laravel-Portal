<!doctype html>
<html lang="en">
<head>
@php($siteSettings = \App\Models\SiteSetting::whereIn('key',['site_name','seo_title','seo_description','tagline'])->pluck('value','key'))
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title',$siteSettings['seo_title'] ?? ($siteSettings['site_name'] ?? 'MRK Digital'))</title>
<meta name="description" content="@yield('description',$siteSettings['seo_description'] ?? 'MRK Digital — professional websites, web applications, automation and digital services.')">
<meta name="robots" content="@yield('robots','index,follow')">
<link rel="canonical" href="{{ url()->current() }}">
<meta property="og:type" content="website">
<meta property="og:title" content="@yield('title','MRK Digital')">
<meta property="og:description" content="@yield('description','MRK Digital — professional websites, web applications, automation and digital services.')">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary">
@vite(['resources/css/app.css','resources/js/app.js'])
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'ProfessionalService','name'=>'MRK Digital','description'=>'Professional websites, web applications, automation and digital services.','url'=>url('/')], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
<script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'WebSite','name'=>'MRK Digital','url'=>url('/')], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
</head>
<body>
<header class="site-header"><div class="container nav">
<a class="brand" href="{{ route('home') }}" aria-label="MRK Digital home"><span>MRK</span> Digital</a>
<button class="menu-toggle" type="button" aria-controls="primary-navigation" aria-expanded="false">Menu</button>
<nav id="primary-navigation" class="primary-navigation">
<a class="{{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a>
<a class="{{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a>
<a class="{{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">Services</a>
<a class="{{ request()->routeIs('projects.*') ? 'active' : '' }}" href="{{ route('projects.index') }}">Projects</a>
<a class="{{ request()->routeIs('blog.*') ? 'active' : '' }}" href="{{ route('blog.index') }}">Blog</a>
<a class="{{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a>
@auth
<a class="{{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">Dashboard</a>
<form class="inline-form" method="POST" action="{{ route('logout') }}">@csrf<button class="nav-button" type="submit">Logout</button></form>
@else
<a class="{{ request()->routeIs('login') ? 'active' : '' }}" href="{{ route('login') }}">Login</a>
<a class="nav-cta {{ request()->routeIs('register') ? 'active' : '' }}" href="{{ route('register') }}">Register</a>
@endauth
</nav></div></header>
@if(session('success'))<div class="container"><div class="alert success">{{ session('success') }}</div></div>@endif
@if($errors->any())<div class="container"><div class="alert error"><strong>Please fix the following:</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div></div>@endif
@yield('content')
<footer><div class="container footer-inner"><span>© {{ date('Y') }} {{ $siteSettings['site_name'] ?? 'MRK Digital' }}</span><span>{{ $siteSettings['tagline'] ?? 'Full Stack Website Developer' }}</span></div></footer>
</body></html>