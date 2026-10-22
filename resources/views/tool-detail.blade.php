@extends('layouts.app')
@section('title', $tool['name'].' — Free Online Tool | MRK Digital')
@section('description', $tool['description'])
@section('content')
<main class="section"><div class="container" style="max-width:900px">
  <nav aria-label="Breadcrumb" style="margin-bottom:1rem"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('tools') }}">Free Online Tools</a> / <span>{{ $tool['name'] }}</span></nav>
  <article>
    <header class="section-heading">
      <span class="eyebrow">MRK DIGITAL FREE TOOL</span>
      <h1>{{ $tool['name'] }}</h1>
      <p>{{ $tool['description'] }}</p>
    </header>
    <section><h2>What this tool does</h2><p>{{ $tool['purpose'] }}</p></section>
    <section><h2>How to use {{ $tool['name'] }}</h2><ol>@foreach($tool['steps'] as $step)<li style="margin:.5rem 0">{{ $step }}</li>@endforeach</ol></section>
    <section><h2>Open the free tool</h2><p>The interactive utility is available on the MRK Digital tools page. Open it below and select the matching tool card.</p><p><a class="button primary" href="{{ route('tools') }}#{{ $tool['card'] }}">Open {{ $tool['name'] }}</a></p></section>
    <section><h2>Frequently asked question</h2><h3>What should I know about {{ $tool['name'] }}?</h3><p>{{ $tool['faq'] }}</p></section>
    <section><h2>Privacy and accuracy</h2><p>Review each tool's instructions before use. Browser-based processing may still load third-party code for some features. Do not enter confidential information into tools unless you understand how it is handled. Always verify important outputs before using them in production.</p></section>
    <p><a href="{{ route('tools') }}">← Browse all free online tools</a></p>
  </article>
</div></main>
@endsection
