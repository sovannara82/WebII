<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Infinity Figures Admin')</title>

    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=poppins:300,400,500,600,700,800" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
</head>
<body class="admin-app">
    @php
        $adminMenuGroups = [
            'Catalog' => [
                ['table' => 'products', 'label' => 'Products', 'icon' => 'bi-box-seam'],
                ['table' => 'categories', 'label' => 'Categories', 'icon' => 'bi-tags-fill'],
                ['table' => 'brands', 'label' => 'Brands', 'icon' => 'bi-bookmark-star-fill'],
                ['table' => 'product_images', 'label' => 'Product Images', 'icon' => 'bi-images'],
                ['table' => 'tags', 'label' => 'Tags', 'icon' => 'bi-tag-fill'],
                ['table' => 'product_tags', 'label' => 'Product Tags', 'icon' => 'bi-diagram-3-fill'],
            ],
            'Sales' => [
                ['table' => 'orders', 'label' => 'Orders', 'icon' => 'bi-cart-fill'],
                ['table' => 'order_items', 'label' => 'Order Items', 'icon' => 'bi-receipt-cutoff'],
                ['table' => 'payments', 'label' => 'Payments', 'icon' => 'bi-credit-card-fill'],
                ['table' => 'coupons', 'label' => 'Coupons', 'icon' => 'bi-percent'],
                ['table' => 'order_coupons', 'label' => 'Order Coupons', 'icon' => 'bi-ticket-perforated-fill'],
            ],
            'Customers' => [
                ['table' => 'users', 'label' => 'Users', 'icon' => 'bi-people-fill'],
                ['table' => 'carts', 'label' => 'Carts', 'icon' => 'bi-bag-fill'],
                ['table' => 'cart_items', 'label' => 'Cart Items', 'icon' => 'bi-basket-fill'],
                ['table' => 'wishlists', 'label' => 'Wishlists', 'icon' => 'bi-heart-fill'],
                ['table' => 'reviews', 'label' => 'Reviews', 'icon' => 'bi-star-fill'],
            ],
            'Settings' => [
                ['table' => 'roles', 'label' => 'Roles', 'icon' => 'bi-shield-lock-fill'],
                ['table' => 'order_statuses', 'label' => 'Order Statuses', 'icon' => 'bi-list-check'],
                ['table' => 'payment_statuses', 'label' => 'Payment Statuses', 'icon' => 'bi-check-circle-fill'],
                ['table' => 'payment_methods', 'label' => 'Payment Methods', 'icon' => 'bi-wallet2'],
                ['table' => 'banners', 'label' => 'Banners', 'icon' => 'bi-card-image'],
            ],
        ];

        $availableTables = $tables ?? collect($adminMenuGroups)->flatten(1)->pluck('table')->all();
    @endphp

    <section class="admin-layout">
        <aside class="admin-sidebar">
            <a class="admin-logo" href="{{ route('admin.dashboard') }}">Infinity Figures</a>
            <nav class="admin-menu">
                <div class="admin-menu-section">
                    <span class="admin-menu-label">Main</span>
                    <a class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                        <i class="bi bi-grid-1x2-fill"></i> Dashboard
                    </a>
                </div>

                @foreach ($adminMenuGroups as $group => $links)
                    <div class="admin-menu-section">
                        <span class="admin-menu-label">{{ $group }}</span>
                        @foreach ($links as $link)
                            @continue(! in_array($link['table'], $availableTables, true))

                            <a class="{{ request('table') === $link['table'] ? 'active' : '' }}" href="{{ route('admin.data', ['table' => $link['table']]) }}">
                                <i class="bi {{ $link['icon'] }}"></i> {{ $link['label'] }}
                            </a>
                        @endforeach
                    </div>
                @endforeach

                <div class="admin-menu-section admin-menu-section-shop">
                    <span class="admin-menu-label">Storefront</span>
                    <a href="{{ route('shop.index') }}">
                        <i class="bi bi-shop"></i> View Shop
                    </a>
                </div>
            </nav>
        </aside>

        <main class="admin-main">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <div class="admin-topbar">
                <div>
                    <h1>@yield('admin-title', 'Dashboard')</h1>
                    <p>@yield('admin-subtitle', 'Manage Infinity Figures operations.')</p>
                </div>
                <div class="admin-tools">
                    
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
