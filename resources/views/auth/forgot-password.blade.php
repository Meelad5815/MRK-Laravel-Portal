@extends('layouts.app')
@section('title','Forgot Password | MRK Digital')
@section('content')
<section class="auth-section"><div class="auth-card"><span class="eyebrow">MRK PORTAL</span><h1>Reset your password</h1><p>Enter the email address linked to your account. If it exists, we will send you a secure password reset link.</p><form method="POST" action="{{ route('password.email') }}" class="form-card compact">@csrf<label>Email address<input name="email" type="email" value="{{ old('email') }}" autocomplete="email" required></label><button class="button primary" type="submit">Send reset link</button></form><p class="auth-link"><a href="{{ route('login') }}">Back to login</a></p></div></section>
@endsection
