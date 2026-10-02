<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>MRK Digital | Full Stack Website Developer</title>
<meta name="description" content="MRK Digital — Laravel, WordPress, web applications, automation and digital services.">
@vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body>
<header class="site-header"><div class="container nav">
<a class="brand" href="{{ route('home') }}"><span>MRK</span> Digital</a>
<nav><a href="{{ route('home') }}">Home</a><a href="{{ route('about') }}">About</a><a href="{{ route('services') }}">Services</a><a href="{{ route('contact') }}">Contact</a></nav>
</div></header>
<main>
<section class="hero"><div class="container hero-grid">
<div><span class="eyebrow">FULL STACK WEBSITE DEVELOPER</span>
<h1>Websites, web apps & digital systems that work for your business.</h1>
<p>MRK Digital creates responsive Laravel and WordPress websites, custom web applications and practical automation solutions.</p>
<div class="actions"><a class="button primary" href="{{ route('services') }}">View Services</a><a class="button secondary" href="{{ route('contact') }}">Start a Project</a></div>
<div class="trust-row"><span>Laravel</span><span>WordPress</span><span>PHP</span><span>Automation</span></div>
</div>
<div class="hero-card"><div class="orb">MRK</div><h2>Digital Solutions</h2><p>Development • Automation • Online Services</p><div class="mini-grid"><div><b>Web</b><small>Modern & responsive</small></div><div><b>Apps</b><small>Custom systems</small></div><div><b>Automation</b><small>PLC & Arduino</small></div><div><b>Support</b><small>Practical solutions</small></div></div></div>
</div></section>
<section class="section"><div class="container"><div class="section-heading"><span class="eyebrow">CORE SERVICES</span><h2>One professional place for digital work.</h2><p>From a business website to a custom management portal, the goal is simple: clean technology that solves a real problem.</p></div>
<div class="cards">
<article><div class="icon">01</div><h3>Web Development</h3><p>Laravel, PHP, WordPress and responsive business websites.</p></article>
<article><div class="icon">02</div><h3>Web Applications</h3><p>Dashboards, management systems, portals, forms and APIs.</p></article>
<article><div class="icon">03</div><h3>Automation</h3><p>Arduino, PLC and practical industrial automation projects.</p></article>
</div></div></section>
<section class="cta"><div class="container cta-box"><div><span class="eyebrow">HAVE A PROJECT?</span><h2>Let's turn your idea into a working system.</h2></div><a class="button primary" href="{{ route('contact') }}">Contact MRK Digital</a></div></section>
</main>
<footer><div class="container footer-inner"><span>© {{ date('Y') }} MRK Digital</span><span>Full Stack Website Developer</span></div></footer>
</body></html>