@extends('layouts.app')

@section('title', 'Wishlist - Infinity Figures')

@section('content')
    <section class="container wishlist-page py-5">
        <div class="wishlist-header">
            <div>
                <span class="detail-kicker">Saved figures</span>
                <h1 class="h2 fw-bold mb-0">Wishlist</h1>
            </div>
            <span class="wishlist-count">{{ $wishlists->count() }} items</span>
        </div>

        <div class="product-grid">
            @forelse ($wishlists as $wishlist)
                <article class="product-card wishlist-card">
                    <div class="heart-pulse"><i class="bi bi-heart-fill"></i></div>
                    <a href="{{ route('shop.show', $wishlist->product) }}" class="product-image-wrap">
                        <img src="{{ $wishlist->product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=700&q=80' }}" alt="{{ $wishlist->product->name }}">
                    </a>
                    <div class="product-body">
                        <div class="product-meta">
                            <span>{{ $wishlist->product->category?->name ?? 'Figure' }}</span>
                            <span>{{ $wishlist->product->stock > 0 ? 'In stock' : 'Sold out' }}</span>
                        </div>
                        <h3><a href="{{ route('shop.show', $wishlist->product) }}">{{ $wishlist->product->name }}</a></h3>
                        <div class="product-actions">
                            <strong>${{ number_format((float) $wishlist->product->price, 2) }}</strong>
                            <div class="quick-actions">
                                <form action="{{ route('cart.store', $wishlist->product) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-gold add-cart" type="submit">Move to cart</button>
                                </form>
                                <form action="{{ route('wishlist.toggle', $wishlist->product) }}" method="POST" onsubmit="return confirm('Remove this figure from your wishlist?')">
                                    @csrf
                                    <button class="btn btn-outline-danger icon-action" type="submit" aria-label="Remove from wishlist">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-wishlist">
                    <div class="empty-illustration">
                        <i class="bi bi-heart"></i>
                        <i class="bi bi-stars"></i>
                    </div>
                    <h2>Your wishlist is empty</h2>
                    <p>Save figures you love and come back when it is time to build the next shelf lineup.</p>
                    <a class="btn btn-gold" href="{{ route('products.index') }}">Browse figures</a>
                </div>
            @endforelse
        </div>
    </section>
@endsection
