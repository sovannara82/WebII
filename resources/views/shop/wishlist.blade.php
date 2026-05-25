@extends('layouts.app')

@section('title', 'Wishlist - KRUY')

@section('content')
    <section class="container py-5">
        <h1 class="h2 fw-bold mb-4">Wishlist</h1>
        <div class="product-grid">
            @forelse ($wishlists as $wishlist)
                <article class="product-card">
                    <a href="{{ route('shop.show', $wishlist->product) }}" class="product-image-wrap">
                        <img src="{{ $wishlist->product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=700&q=80' }}" alt="{{ $wishlist->product->name }}">
                    </a>
                    <div class="product-body">
                        <h3><a href="{{ route('shop.show', $wishlist->product) }}">{{ $wishlist->product->name }}</a></h3>
                        <div class="product-actions">
                            <strong>${{ number_format((float) $wishlist->product->price, 2) }}</strong>
                            <form action="{{ route('wishlist.toggle', $wishlist->product) }}" method="POST">
                                @csrf
                                <button class="btn btn-outline-danger" type="submit">Remove</button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-panel">Your wishlist is empty.</div>
            @endforelse
        </div>
    </section>
@endsection
