@extends('layouts.app')

@section('content')
<section class="auth-shell">
    <div class="auth-card">
        <div class="auth-art">
            <span>Customer access</span>
            <h1>Welcome back to Infinity Figures</h1>
            <p>Track orders, save wishlist figures, and check out faster when the next drop lands.</p>
        </div>

        <div class="auth-panel">
            <h2>{{ __('Customer Login') }}</h2>
            <p class="admin-muted">Sign in to continue your figure hunt.</p>

            <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">{{ __('Email Address') }}</label>
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">{{ __('Password') }}</label>
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                        </div>

                        <div class="d-flex justify-content-between align-items-center gap-3 mb-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                        </div>

                        <button type="submit" class="btn btn-gold btn-lg w-100">{{ __('Login') }}</button>
                        <p class="auth-switch">New customer? <a href="{{ route('register') }}">Create an account</a></p>
                    </form>
        </div>
    </div>
</section>
@endsection
