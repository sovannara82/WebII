@extends('layouts.app')

@section('title', 'Profile - Infinity Figures')

@section('content')
    <section class="container profile-page py-5">
        <div class="wishlist-header">
            <div>
                <span class="detail-kicker">Account center</span>
                <h1 class="h2 fw-bold mb-0">Edit profile</h1>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <form class="admin-panel" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="profile-avatar">
                        @php
                            $profileImage = null;

                            if (filled($user->image)) {
                                if (Illuminate\Support\Str::startsWith($user->image, ['http://', 'https://', '/'])) {
                                    $profileImage = $user->image;
                                } elseif (Illuminate\Support\Str::startsWith($user->image, 'storage/')) {
                                    $profileImage = asset($user->image);
                                } else {
                                    $profileImage = asset('storage/'.$user->image);
                                }

                                $profileImage .= (str_contains($profileImage, '?') ? '&' : '?').'v='.$user->updated_at?->timestamp;
                            }
                        @endphp
                        @if ($profileImage)
                            <img src="{{ $profileImage }}" alt="{{ $user->name }}">
                        @else
                            <i class="bi bi-person-circle"></i>
                        @endif
                        <div>
                            <label class="form-label" for="image">Profile image</label>
                            <input class="form-control @error('image') is-invalid @enderror" id="image" name="image" type="file" accept="image/*">
                            @error('image')
                                <span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>
                            @enderror
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label" for="name">Name</label>
                            <input class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="username">Username</label>
                            <input class="form-control @error('username') is-invalid @enderror" id="username" name="username" value="{{ old('username', $user->username) }}">
                            @error('username')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="email">Email</label>
                            <input class="form-control @error('email') is-invalid @enderror" id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Phone</label>
                            <input class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                            @error('phone')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="address">Address</label>
                            <input class="form-control @error('address') is-invalid @enderror" id="address" name="address" value="{{ old('address', $user->address) }}">
                            @error('address')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="city">City</label>
                            <input class="form-control @error('city') is-invalid @enderror" id="city" name="city" value="{{ old('city', $user->city) }}">
                            @error('city')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="province">Province</label>
                            <input class="form-control @error('province') is-invalid @enderror" id="province" name="province" value="{{ old('province', $user->province) }}">
                            @error('province')<span class="invalid-feedback"><strong>{{ $message }}</strong></span>@enderror
                        </div>
                    </div>

                    <button class="btn btn-gold mt-4" type="submit">Save profile</button>
                </form>
            </div>

            <div class="col-lg-5">
                <form class="admin-panel" action="{{ route('profile.password') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <h2 class="h4 fw-bold mb-3">Change password</h2>
                    <label class="form-label" for="current_password">Current password</label>
                    <input class="form-control @error('current_password') is-invalid @enderror mb-3" id="current_password" name="current_password" type="password" required>
                    @error('current_password')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror

                    <label class="form-label" for="password">New password</label>
                    <input class="form-control @error('password') is-invalid @enderror mb-3" id="password" name="password" type="password" required>
                    @error('password')<span class="invalid-feedback d-block"><strong>{{ $message }}</strong></span>@enderror

                    <label class="form-label" for="password_confirmation">Confirm password</label>
                    <input class="form-control mb-4" id="password_confirmation" name="password_confirmation" type="password" required>

                    <button class="btn btn-outline-light" type="submit">Update password</button>
                </form>
            </div>
        </div>
    </section>
@endsection
