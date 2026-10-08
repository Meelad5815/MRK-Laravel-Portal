@extends('layouts.app')
@section('title','Register | MRK Digital')
@section('content')
<section class="auth-section"><div class="auth-card"><span class="eyebrow">MRK PORTAL</span><h1>Create account</h1><form method="POST" action="{{ route('register.store') }}" class="form-card compact">@csrf<label>Name<input name="name" type="text" required></label><label>Email<input name="email" type="email" required></label><label>Secret<input name="password" type="password" required></label><label>Confirm secret<input name="password_confirmation" type="password" required></label><button class="button primary" type="submit">Create account</button></form><p class="auth-link">Already registered? <a href="{{ route('login') }}">Sign in</a></p></div></section>
@endsection
