@extends('layouts.app')
@section('title','Contact | MRK Digital')
@section('content')
<section class="page-hero"><div class="container narrow"><span class="eyebrow">CONTACT</span><h1>Tell us what you want to build.</h1><p>Share your project requirement and MRK Digital will receive a structured enquiry.</p></div></section>
<section class="section"><div class="container contact-grid"><div><span class="eyebrow">PROJECT BRIEF</span><h2>Start with the basics.</h2><div class="contact-points"><div><b>Website / Web App</b><span>Describe the pages, features or workflow you need.</span></div><div><b>Automation</b><span>Share the controller, sensors, inputs, outputs and desired operation.</span></div><div><b>Business Support</b><span>Explain the online or digital task you want to simplify.</span></div><div><b>Phone / WhatsApp</b><span><a href="tel:+923270447263">0327 0447263</a></span></div><div><b>Email</b><span><a href="mailto:hafizmuhammadmeeladraza@gmail.com">hafizmuhammadmeeladraza@gmail.com</a></span></div></div></div>
<form method="POST" action="{{ route('contact.store') }}" class="form-card">@csrf
<input type="text" name="website" value="" autocomplete="off" tabindex="-1" aria-hidden="true" class="honeypot">
<label>Name<input name="name" type="text" value="{{ old('name') }}" required></label>
<label>Email<input name="email" type="email" value="{{ old('email') }}"></label>
<label>Phone / WhatsApp<input name="phone" type="text" value="{{ old('phone') }}"></label>
<label>Project type<select name="project_type" required><option value="">Select</option>
@foreach(['Website / Laravel Web App','WordPress Website or Blog','Product Listing / Virtual Assistant','Graphic Design / Print Artwork','Video Editing','NADRA / PakID Guidance','FBR / NTN / Tax Return Guidance','Passport / Visa Form Guidance','Punjab Government Forms / Dastak','Vehicle Token Tax / Driving Licence Guidance','Utility Bill / Online Services','PLC / Arduino / ESP32 Automation','Mobile Software Support','SEO / Search Console','Office Documents / Data Entry','Other'] as $type)<option value="{{ $type }}" @selected(old('project_type') === $type)>{{ $type }}</option>@endforeach
</select></label>
<label>Requirement<textarea name="message" rows="7" required>{{ old('message') }}</textarea></label>
<button class="button primary" type="submit">Send Enquiry</button>
</form></div></section>
@endsection