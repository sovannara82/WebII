<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'KRUY Admin')</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-app">
    <section class="admin-layout">
        <aside class="admin-sidebar">
            <a class="admin-logo" href="{{ route('admin.dashboard') }}">KRUY</a>
            <nav class="admin-menu">
                <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i> Dashboard
                </a>
                <a class="{{ request('table') === 'products' ? 'active' : '' }}" href="{{ route('admin.data', ['table' => 'products']) }}">
                    <i class="bi bi-box-seam"></i> Products
                </a>
                <a class="{{ request('table') === 'categories' ? 'active' : '' }}" href="{{ route('admin.data', ['table' => 'categories']) }}">
                    <i class="bi bi-tags-fill"></i> Categories
                </a>
                <a class="{{ request('table') === 'orders' ? 'active' : '' }}" href="{{ route('admin.data', ['table' => 'orders']) }}">
                    <i class="bi bi-cart-fill"></i> Orders
                </a>
                <a class="{{ request('table') === 'users' ? 'active' : '' }}" href="{{ route('admin.data', ['table' => 'users']) }}">
                    <i class="bi bi-people-fill"></i> Users
                </a>
                <a class="{{ request('table') === 'payments' ? 'active' : '' }}" href="{{ route('admin.data', ['table' => 'payments']) }}">
                    <i class="bi bi-credit-card-fill"></i> Payments
                </a>
                <a class="{{ request('table') === 'coupons' ? 'active' : '' }}" href="{{ route('admin.data', ['table' => 'coupons']) }}">
                    <i class="bi bi-percent"></i> Coupons
                </a>
                <a href="{{ route('shop.index') }}">
                    <i class="bi bi-shop"></i> View Shop
                </a>
            </nav>
        </aside>

        <main class="admin-main">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="admin-topbar">
                <div>
                    <h1>@yield('admin-title', 'Dashboard')</h1>
                    <p>@yield('admin-subtitle', 'Manage KRUY figure shop operations.')</p>
                </div>
                <div class="admin-tools">
                    <input class="admin-search" placeholder="Search...">
                    <div class="admin-profile">
                        <i class="bi bi-person-circle"></i>
                        {{ Auth::user()->name ?? 'Admin' }}
                    </div>
                    <a class="btn btn-outline-light" href="{{ route('logout') }}"
                       onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                        Logout
                    </a>
                    <form id="admin-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>

            @yield('content')
        </main>
    </section>
</body>
</html>
