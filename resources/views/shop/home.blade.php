@extends('layouts.app')

@section('title', 'Infinity Figures - Collectible Figure Shop')

@section('content')
    <section class="shop-hero" id="home">
        <div class="container hero-inner">
            <div class="row align-items-center g-5">
                <div class="col-xl-6 col-lg-7">
                    <span class="hero-kicker">Anime Figure Shop</span>
                    <h1 class="hero-title">Bring Your Favorite Characters To Life</h1>
                    <p class="hero-copy">Premium anime, game, and movie figures with collectible-grade detail, real inventory, and a catalog built for serious display shelves.</p>
                    <form action="{{ route('products.index') }}" class="hero-search">
                        <input class="form-control form-control-lg" name="search" placeholder="Search character, series, or product">
                        <button class="btn btn-gold btn-lg px-5" type="submit">Search</button>
                    </form>
                    <a class="btn btn-outline-gold btn-lg mt-3" href="{{ route('products.index') }}">View All Products</a>
                </div>
                <div class="col-xl-5 offset-xl-1 col-lg-5">
                    <div class="hero-showcase">
                        @forelse ($heroProducts as $product)
                            <a class="hero-mini-card" href="{{ route('shop.show', $product) }}">
                                <img src="{{ $product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1608889825205-eebdb9fc5806?auto=format&fit=crop&w=500&q=80' }}" alt="{{ $product->name }}" loading="lazy">
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
            <div class="section-heading promo-heading">
                <span>Customer Campaigns</span>
                <h2>Fresh drops and deals</h2>
            </div>

            @if ($banners->isNotEmpty())
                <div id="promoBannerCarousel" class="carousel slide promo-carousel" data-bs-ride="carousel" data-bs-interval="4000">
                    @if ($banners->count() > 1)
                        <div class="carousel-indicators">
                            @foreach ($banners as $banner)
                                <button type="button" data-bs-target="#promoBannerCarousel" data-bs-slide-to="{{ $loop->index }}" class="{{ $loop->first ? 'active' : '' }}" aria-current="{{ $loop->first ? 'true' : 'false' }}" aria-label="Banner {{ $loop->iteration }}"></button>
                            @endforeach
                        </div>
                    @endif

                    <div class="carousel-inner">
                        @foreach ($banners as $banner)
                            @php
                                $bannerImage = Illuminate\Support\Str::startsWith($banner->image, ['http://', 'https://'])
                                    ? $banner->image
                                    : asset($banner->image);
                            @endphp

                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}">
                                <article class="promo-banner-slide" style="--banner-image: url('{{ $bannerImage }}')">
                                    <div class="promo-banner-media" aria-hidden="true"></div>
                                    <div class="promo-banner-content">
                                        <span>{{ $banner->eyebrow ?: ($loop->first ? 'Featured Campaign' : 'Limited Run') }}</span>
                                        <h2>{{ $banner->title }}</h2>
                                        <p>{{ $banner->subtitle ?: ($loop->first ? 'Explore the newest customer promotion and featured arrivals before they leave the shelf.' : 'A curated offer for customers looking for their next display piece.') }}</p>
                                        <a class="btn btn-gold btn-lg" href="{{ $banner->link ?? route('products.index') }}">
                                            {{ $banner->button_text ?: 'Explore' }}
                                        </a>
                                    </div>
                                </article>
                            </div>
                        @endforeach
                    </div>

                    @if ($banners->count() > 1)
                        <button class="carousel-control-prev promo-carousel-control" type="button" data-bs-target="#promoBannerCarousel" data-bs-slide="prev">
                            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Previous</span>
                        </button>
                        <button class="carousel-control-next promo-carousel-control" type="button" data-bs-target="#promoBannerCarousel" data-bs-slide="next">
                            <span class="carousel-control-next-icon" aria-hidden="true"></span>
                            <span class="visually-hidden">Next</span>
                        </button>
                    @endif
                </div>
            @else
                <div class="promo-carousel">
                    <article class="promo-banner-slide">
                        <div class="promo-banner-media" aria-hidden="true"></div>
                        <div class="promo-banner-content">
                            <span>Featured Campaign</span>
                            <h2>Mega Anime Sale</h2>
                            <p>Get up to 30% off selected scale figures and action figure collections.</p>
                            <a class="btn btn-gold btn-lg" href="{{ route('products.index') }}">Explore</a>
                        </div>
                    </article>
                </div>
            @endif
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
                    <a class="category-box" href="{{ route('products.index', ['category' => $category->id]) }}">
                        <i class="bi {{ $loop->index % 4 === 0 ? 'bi-stars' : ($loop->index % 4 === 1 ? 'bi-lightning-charge' : ($loop->index % 4 === 2 ? 'bi-controller' : 'bi-gem')) }}"></i>
                        <strong>{{ $category->name }}</strong>
                        <span>{{ $category->products_count }} figures</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <section class="shop-section pt-0">
        <div class="container-fluid shop-wide">
            <div class="section-heading">
                <span>Fresh Stock</span>
                <h2>New Arrivals</h2>
            </div>
            <div class="product-grid">
                @forelse ($newArrivals as $product)
                    @include('shop.partials.product-card', ['product' => $product])
                @empty
                    <div class="empty-panel">No new arrivals are available yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="shop-section pt-0">
        <div class="container-fluid shop-wide">
            <div class="catalog-topbar">
                <div>
                    <span>Popular Figures</span>
                    <h2>Best Sellers</h2>
                </div>
                <a class="btn btn-gold btn-lg" href="{{ route('products.index') }}">View All Products</a>
            </div>
            <div class="product-grid">
                @forelse ($bestSellers as $product)
                    @include('shop.partials.product-card', ['product' => $product])
                @empty
                    <div class="empty-panel">No best sellers are available yet.</div>
                @endforelse
            </div>
        </div>
    </section>

    @include('shop.partials.footer')
@endsection
