@extends('layouts.admin')

@section('title', 'Admin Login - Infinity Figures')
@section('admin-title', 'Admin Portal')
@section('admin-subtitle', 'Secure access for Infinity Figures staff.')

@section('content')
    <section class="admin-login-shell">
        <div class="admin-login-card">
            <div class="admin-login-heading">
                <span>{{ config('app.name', 'Infinity Figures') }}</span>
                <h2>Admin Portal</h2>
                <p>Sign in to manage catalog, orders, customers, and storefront operations.</p>
            </div>

            @if (session('error') === 'Access denied.' || ($errors->has('email') && $errors->first('email') === 'Access denied.'))
                <div class="alert alert-danger" role="alert">
                    Access denied.
                </div>
            @endif

            <form method="POST" action="{{ route('admin.login') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label" for="email">{{ __('Email Address') }}</label>
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                    @error('email')
                        @if ($message !== 'Access denied.')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @endif
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label" for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                    @error('password')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                    <label class="form-check-label" for="remember">
                        {{ __('Remember Me') }}
                    </label>
                </div>

                <button class="btn btn-gold btn-lg w-100" type="submit">
                    Sign in
                </button>
            </form>
        </div>
    </section>
@endsection
