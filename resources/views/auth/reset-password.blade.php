@extends('layouts.app')
@section('title','Choose New Password | MRK Digital')
@section('content')
<section class="auth-section"><div class="auth-card"><span class="eyebrow">MRK PORTAL</span><h1>Choose a new password</h1><form method="POST" action="{{ route('password.update') }}" class="form-card compact">@csrf<input type="hidden" name="token" value="{{ $token }}"><label>Email address<input name="email" type="email" value="{{ old('email', $email) }}" autocomplete="email" required></label><label>New password<input name="password" type="password" autocomplete="new-password" required minlength="8"></label><label>Confirm new password<input name="password_confirmation" type="password" autocomplete="new-password" required minlength="8"></label><button class="button primary" type="submit">Save new password</button></form><p class="auth-link"><a href="{{ route('login') }}">Back to login</a></p></div></section>
@endsection
