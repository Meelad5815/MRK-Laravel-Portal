@extends('layouts.app')
@section('title','Privacy Policy | MRK Digital')
@section('description','Read how MRK Digital handles information submitted through website enquiries.')
@section('content')
<main>
<section class="page-hero"><div class="container narrow"><span class="eyebrow">LEGAL</span><h1>Privacy Policy</h1><p>How information submitted to MRK Digital is handled.</p></div></section>
<section class="section"><article class="container narrow rich-content">
<h2>Information you provide</h2>
<p>When you submit the contact form, you may provide your name, email address, phone number, project type and message. Please provide only information needed to discuss your request.</p>
<h2>How information is used</h2>
<p>Information submitted through the website is used to review and respond to enquiries, clarify project requirements, prepare quotations and manage related business communication.</p>
<h2>Data sharing</h2>
<p>MRK Digital does not ask you to submit passwords, payment card details or other highly sensitive account credentials through the enquiry form. Information may be processed by the hosting and technical service providers required to operate this website.</p>
<h2>Retention and security</h2>
<p>Reasonable technical and organizational measures should be used to protect enquiry records. Information may be retained as needed to respond to requests, maintain business records or meet applicable legal obligations.</p>
<h2>External services</h2>
<p>This website may link to third-party websites. Their privacy practices are governed by their own policies, which should be reviewed separately.</p>
<h2>Contact</h2>
<p>For a question about this policy or a request concerning information you submitted, use the <a href="{{ route('contact') }}">Contact page</a>.</p>
<p><em>Last updated: October 17, 2026. This general notice should be reviewed and adapted to the actual tools, data practices and legal requirements of the business.</em></p>
</article></section>
</main>
@endsection
