@extends('layouts.app')

@section('title', 'Cart - FigureVerse')

@section('content')
    <section class="container py-5">
        <h1 class="h2 fw-bold mb-4">Shopping cart</h1>

        <div class="row g-4">
            <div class="col-lg-8">
                @forelse ($items as $item)
                    <div class="cart-line">
                        <img src="{{ $item->product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=300&q=80' }}" alt="{{ $item->product->name }}">
                        <div>
                            <strong>{{ $item->product->name }}</strong>
                            <p class="admin-muted mb-0">${{ number_format((float) $item->price, 2) }}</p>
                            <form class="d-flex gap-2 mt-2" action="{{ route('cart-items.update', $item) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <input class="form-control form-control-sm" name="quantity" type="number" value="{{ $item->quantity }}" min="1" max="20" style="width: 90px">
                                <button class="btn btn-sm btn-outline-light" type="submit">Update</button>
                            </form>
                        </div>
                        <span class="ms-auto fw-bold">${{ number_format((float) $item->price * $item->quantity, 2) }}</span>
                        <form action="{{ route('cart-items.destroy', $item) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" type="submit">Remove</button>
                        </form>
                    </div>
                @empty
                    <div class="empty-panel">Your cart is empty. Add a figure from the catalog to start checkout.</div>
                @endforelse
            </div>
            <aside class="col-lg-4">
                <div class="summary-panel">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal</span>
                        <strong>${{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between admin-muted mb-3">
                        <span>Shipping</span>
                        <span>Calculated later</span>
                    </div>
                    <a class="btn btn-gold w-100" href="{{ route('checkout.create') }}">Checkout</a>
                </div>
            </aside>
        </div>
    </section>
@endsection
