<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MRK Digital | Full Stack Website Developer</title>
    <meta name="description" content="MRK Digital — professional web development, automation and digital services.">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
<header class="site-header">
    <div class="container nav">
        <a class="brand" href="{{ route('home') }}">MRK Digital</a>
        <nav>
            <a href="{{ route('home') }}">Home</a>
            <a href="{{ route('about') }}">About</a>
            <a href="{{ route('services') }}">Services</a>
            <a href="{{ route('contact') }}">Contact</a>
        </nav>
    </div>
</header>

<main>
<section class="hero">
    <div class="container hero-grid">
        <div>
            <span class="eyebrow">FULL STACK WEBSITE DEVELOPER</span>
            <h1>Professional websites, web apps & digital solutions.</h1>
            <p>MRK Digital builds modern Laravel, WordPress and custom web solutions for businesses, professionals and online services.</p>
            <div class="actions">
                <a class="button primary" href="{{ route('services') }}">Explore Services</a>
                <a class="button secondary" href="{{ route('contact') }}">Contact Me</a>
            </div>
        </div>
        <div class="hero-card">
            <span>MRK</span>
            <strong>Digital</strong>
            <p>Web Development • Automation • Digital Services</p>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-heading">
            <span class="eyebrow">WHAT I DO</span>
            <h2>Digital solutions built for real needs.</h2>
        </div>
        <div class="cards">
            <article><h3>Web Development</h3><p>Laravel, PHP, WordPress and responsive business websites.</p></article>
            <article><h3>Web Applications</h3><p>Management systems, dashboards, forms and custom portals.</p></article>
            <article><h3>Automation</h3><p>Arduino, PLC and practical automation solutions.</p></article>
        </div>
    </div>
</section>
</main>

<footer>
    <div class="container">© {{ date('Y') }} MRK Digital. All rights reserved.</div>
</footer>
</body>
</html>
