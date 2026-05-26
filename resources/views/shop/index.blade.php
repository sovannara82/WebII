@extends('layouts.customer')

@section('title', 'Infinity Figures - Collectible Figure Shop')

@section('content')
    <section class="shop-hero" id="home">
        <div class="container hero-inner">
            <div class="row align-items-center g-5">
                <div class="col-xl-6 col-lg-7">
                    <span class="hero-kicker">Anime Figure Shop</span>
                    <h1 class="hero-title">Bring Your Favorite Characters To Life</h1>
                    <p class="hero-copy">Premium anime, game, and movie figures with collectible-grade detail, real inventory, and a catalog built for serious display shelves.</p>
                    <form action="{{ route('shop.index') }}" class="hero-search">
                        <input class="form-control form-control-lg" name="search" value="{{ request('search') }}" placeholder="Search character, series, or product">
                        <button class="btn btn-gold btn-lg px-5" type="submit">Search</button>
                    </form>
                </div>
                <div class="col-xl-5 offset-xl-1 col-lg-5">
                    <div class="hero-showcase">
                        @forelse ($featuredProducts as $product)
                            <a class="hero-mini-card" href="{{ route('shop.show', $product) }}">
                                <img src="{{ $product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1608889825205-eebdb9fc5806?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $product->name }}">
                                <span>
                                    <small>{{ $product->series }}</small>
                                    {{ $product->character_name ?? $product->name }}
                                </span>
                            </a>
                        @empty
                            <div class="hero-mini-card empty-state">Add products to feature them here.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="promo-section">
        <div class="container-fluid shop-wide">
            @forelse ($banners as $banner)
                @if ($loop->first)
                    <div class="section-heading promo-heading">
                        <span>Collector Campaigns</span>
                        <h2>Fresh drops and deals</h2>
                    </div>
                    <div class="promo-grid">
                @endif

                @php
                    $bannerImage = Illuminate\Support\Str::startsWith($banner->image, ['http://', 'https://'])
                        ? $banner->image
                        : asset($banner->image);
                @endphp

                <article class="promo-banner {{ $loop->first ? 'promo-banner-featured' : '' }}" style="--banner-image: url('{{ $bannerImage }}')">
                    <div class="promo-banner-media" aria-hidden="true"></div>
                    <div class="promo-banner-content">
                        <span>{{ $banner->eyebrow ?: ($loop->first ? 'Featured Campaign' : 'Limited Run') }}</span>
                        <h2>{{ $banner->title }}</h2>
                        <p>{{ $banner->subtitle ?: ($loop->first ? 'Explore the newest collector promotion and featured arrivals before they leave the shelf.' : 'A curated offer for collectors looking for their next display piece.') }}</p>
                    </div>
                    <a class="btn btn-gold btn-lg" href="{{ $banner->link ?? route('shop.index') }}#products">
                        {{ $banner->button_text ?: 'Explore' }}
                    </a>
                </article>

                @if ($loop->last)
                    </div>
                @endif
            @empty
                <div class="section-heading promo-heading">
                    <span>Collector Campaigns</span>
                    <h2>Fresh drops and deals</h2>
                </div>
                <div class="promo-grid">
                    <article class="promo-banner promo-banner-featured">
                        <div class="promo-banner-media" aria-hidden="true"></div>
                        <div class="promo-banner-content">
                            <span>Featured Campaign</span>
                            <h2>Mega Anime Sale</h2>
                            <p>Get up to 30% off selected scale figures and action figure collections.</p>
                        </div>
                        <a class="btn btn-gold btn-lg" href="{{ route('shop.index') }}#products">Explore</a>
                    </article>
                </div>
            @endforelse
        </div>
    </section>

    <section class="shop-section" id="categories">
        <div class="container-fluid shop-wide">
            <div class="section-heading">
                <span>Browse</span>
                <h2>Popular Categories</h2>
            </div>

            <div class="category-grid">
                @foreach ($categories as $category)
                    <a class="category-box" href="{{ route('shop.index', ['category' => $category->id]) }}">
                        <i class="bi {{ $loop->index % 4 === 0 ? 'bi-stars' : ($loop->index % 4 === 1 ? 'bi-lightning-charge' : ($loop->index % 4 === 2 ? 'bi-controller' : 'bi-gem')) }}"></i>
                        <strong>{{ $category->name }}</strong>
                        <span>{{ $category->products_count }} figures</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="shop-section pt-0" id="products">
        <div class="container-fluid shop-wide">
            <div class="catalog-shell">
                <div class="catalog-topbar">
                    <div>
                        <span>Trending Figures</span>
                        <h2>Available figures</h2>
                    </div>
                    <span>{{ $products->total() }} products</span>
                </div>

                <form class="filter-panel" action="{{ route('products.index') }}">
                    <div class="filter-search">
                        <label class="form-label" for="search">Search figures</label>
                        <input class="form-control" id="search" name="search" value="{{ request('search') }}" placeholder="Name, character, or series">
                    </div>

                    <div>
                        <label class="form-label" for="category">Category</label>
                        <select class="form-select" id="category" name="category">
                            <option value="">All categories</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>
                                    {{ $category->name }} ({{ $category->products_count }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="availability">Availability</label>
                        <select class="form-select" id="availability" name="availability">
                            <option value="">Any stock</option>
                            <option value="in_stock" @selected(request('availability') === 'in_stock')>In stock</option>
                            <option value="low_stock" @selected(request('availability') === 'low_stock')>Low stock</option>
                            <option value="sold_out" @selected(request('availability') === 'sold_out')>Sold out</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label" for="min_price">Min price</label>
                        <input class="form-control" id="min_price" name="min_price" type="number" min="0" step="0.01" value="{{ request('min_price') }}" placeholder="$0">
                    </div>

                    <div>
                        <label class="form-label" for="max_price">Max price</label>
                        <input class="form-control" id="max_price" name="max_price" type="number" min="0" step="0.01" value="{{ request('max_price') }}" placeholder="$500">
                    </div>

                    <div>
                        <label class="form-label" for="brand">Brand</label>
                        <select class="form-select" id="brand" name="brand">
                            <option value="">All brands</option>
                            @foreach ($brands as $brand)
                                <option value="{{ $brand->id }}" @selected((string) request('brand') === (string) $brand->id)>{{ $brand->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="filter-actions">
                        <button class="btn btn-gold" type="submit"><i class="bi bi-funnel"></i> Apply</button>
                        <a class="btn btn-outline-light" href="{{ route('products.index') }}">Reset</a>
                    </div>
                </form>

                <div class="product-grid">
                    @forelse ($products as $product)
                        <article class="product-card">
                            <div class="wishlist-btn" aria-label="Add to wishlist">
                                @auth
                                    <form action="{{ route('wishlist.toggle', $product) }}" method="POST">
                                        @csrf
                                        <button class="wishlist-icon" type="submit"><i class="bi bi-heart-fill"></i></button>
                                    </form>
                                @else
                                    <i class="bi bi-heart-fill"></i>
                                @endauth
                            </div>
                            <a href="{{ route('shop.show', $product) }}" class="product-image-wrap">
                                <img src="{{ $product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=700&q=80' }}" alt="{{ $product->name }}">
                                <span class="stock-badge {{ $product->stock <= 0 ? 'sold-out' : ($product->stock <= 6 ? 'low-stock' : '') }}">
                                    {{ $product->stock <= 0 ? 'Sold out' : ($product->stock <= 6 ? 'Low stock' : 'In stock') }}
                                </span>
                            </a>
                            <div class="product-body">
                                <div class="product-meta">
                                    <span>{{ $product->category->name }}</span>
                                    <span>{{ $product->brand?->name ?? 'Studio' }}</span>
                                </div>
                                <h3>
                                    <a href="{{ route('shop.show', $product) }}">{{ $product->name }}</a>
                                </h3>
                                <p>{{ $product->series }} {{ $product->height ? '- '.$product->height.' cm' : '' }}</p>
                                <div class="product-actions">
                                    <strong>${{ number_format((float) $product->price, 2) }}</strong>
                                    <div class="quick-actions">
                                        <a class="btn btn-outline-light icon-action" href="{{ route('shop.show', $product) }}" aria-label="View details">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <form action="{{ route('cart.store', $product) }}" method="POST">
                                            @csrf
                                            <button class="btn btn-gold add-cart" type="submit" @disabled($product->stock <= 0)>Add Cart</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="empty-panel">No products match the current filters.</div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </section>

    <footer class="shop-footer">
        <div class="container-fluid shop-wide">
            <div class="footer-top">
                <div class="footer-brand">
                    <a class="footer-logo" href="{{ route('shop.index') }}">Infinity Figures</a>
                    <p>Premium anime figure online store for collectors who care about sculpt, paint, packaging, and display presence.</p>
                    <div class="footer-socials" aria-label="Social links">
                        <a href="#" aria-label="Facebook"><i class="bi bi-facebook"></i></a>
                        <a href="#" aria-label="Instagram"><i class="bi bi-instagram"></i></a>
                        <a href="#" aria-label="TikTok"><i class="bi bi-tiktok"></i></a>
                        <a href="#" aria-label="Telegram"><i class="bi bi-telegram"></i></a>
                    </div>
                </div>

                <div class="footer-column">
                    <h4>Quick Links</h4>
                    <a href="{{ route('shop.index') }}">Home</a>
                    <a href="{{ route('shop.index') }}#products">Products</a>
                    <a href="{{ route('shop.index') }}#categories">Categories</a>
                    <a href="{{ route('cart.index') }}">Cart</a>
                </div>

                <div class="footer-column">
                    <h4>Collector Care</h4>
                    <a href="{{ route('orders.index') }}">Order Tracking</a>
                    <a href="{{ route('wishlist.index') }}">Wishlist</a>
                    <a href="{{ route('checkout.create') }}">Checkout</a>
                    <a href="{{ route('login') }}">Account Login</a>
                </div>

                <div class="footer-contact">
                    <h4>Store Info</h4>
                    <p><i class="bi bi-geo-alt"></i> Phnom Penh, Cambodia</p>
                    <p><i class="bi bi-telephone"></i> +855 968276484</p>
                    <p><i class="bi bi-envelope"></i> support@infinity-figures.test</p>
                    <div class="payment-row">
                        <span>ABA</span>
                        <span>Visa</span>
                        <span>Cash</span>
                    </div>
                </div>

                <div class="footer-newsletter">
                    <h4>Newsletter</h4>
                    <p>Get restock alerts, preorder drops, and collector-only coupon codes.</p>
                    <div class="input-group">
                        <input class="form-control" placeholder="Your email">
                        <button class="btn btn-gold" type="button">Subscribe</button>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <span>© {{ now()->year }} Infinity Figures. All rights reserved.</span>
                <div>
                    <a href="#">Privacy</a>
                    <a href="#">Terms</a>
                    <a href="#">Returns</a>
                </div>
            </div>
        </div>
    </footer>
@endsection
