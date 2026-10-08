@extends('layouts.app')
@section('title','Login | MRK Digital')
@section('content')
<section class="auth-section"><div class="auth-card"><span class="eyebrow">MRK PORTAL</span><h1>Welcome back</h1><form method="POST" action="{{ route('login.store') }}" class="form-card compact">@csrf<label>Email<input name="email" type="email" required></label><label>Secret<input name="password" type="password" required></label><button class="button primary" type="submit">Sign in</button></form><p class="auth-link">No account? <a href="{{ route('register') }}">Create one</a></p></div></section>
@endsection
