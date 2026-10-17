@extends('layouts.app')
@section('title','Terms of Service | MRK Digital')
@section('description','Review the general terms for enquiries and services provided by MRK Digital.')
@section('content')
<main>
<section class="page-hero"><div class="container narrow"><span class="eyebrow">LEGAL</span><h1>Terms of Service</h1><p>General terms for website enquiries and project discussions.</p></div></section>
<section class="section"><article class="container narrow rich-content">
<h2>Enquiries and quotations</h2>
<p>Submitting an enquiry does not create a contract or guarantee acceptance of a project. Scope, deliverables, timeline, fees, payment schedule and responsibilities should be agreed in writing before work begins.</p>
<h2>Project information</h2>
<p>Clients should provide accurate requirements and only share files or information they are authorized to provide. Access credentials should be shared through an appropriate secure method, not through a public contact form.</p>
<h2>Third-party platforms</h2>
<p>Some projects may depend on hosting providers, APIs, plugins, marketplaces or other third-party services. Their availability, pricing, policies and technical limitations are outside MRK Digital's direct control.</p>
<h2>Timelines and outcomes</h2>
<p>Delivery dates and expected outcomes depend on the agreed scope, access, feedback and third-party dependencies. Search rankings, traffic, sales, earnings and other business results cannot be guaranteed.</p>
<h2>Changes and support</h2>
<p>Changes outside the agreed scope, ongoing maintenance and additional support should be discussed and agreed separately.</p>
<h2>Contact</h2>
<p>For questions about a potential project, use the <a href="{{ route('contact') }}">Contact page</a> to share your requirements.</p>
<p><em>Last updated: October 17, 2026. These general terms are a starting point and should be reviewed for the specific services and applicable law.</em></p>
</article></section>
</main>
@endsection
