@extends('layouts.app')

@section('title', 'Order #'.$order->id.' - KRUY')

@section('content')
    <section class="container py-5">
        <h1 class="h2 fw-bold mb-4">Order #{{ $order->id }}</h1>
        <div class="row g-4">
            <div class="col-lg-8">
                @foreach ($order->items as $item)
                    <div class="cart-line">
                        <img src="{{ $item->product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=300&q=80' }}" alt="{{ $item->product->name }}">
                        <div>
                            <strong>{{ $item->product->name }}</strong>
                            <p class="admin-muted mb-0">Qty {{ $item->quantity }} x ${{ number_format((float) $item->price, 2) }}</p>
                        </div>
                        <span class="ms-auto fw-bold">${{ number_format((float) $item->subtotal, 2) }}</span>
                    </div>
                @endforeach
            </div>
            <aside class="col-lg-4">
                <div class="summary-panel">
                    <p>Status: <span class="badge-premium">{{ $order->status->name }}</span></p>
                    <p>Payment: {{ $order->paymentStatus->name }} via {{ $order->paymentMethod?->name }}</p>
                    @foreach ($order->coupons as $coupon)
                        <p>Coupon {{ $coupon->code }}: -${{ number_format((float) $coupon->pivot->discount, 2) }}</p>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between"><span>Subtotal</span><strong>${{ number_format((float) $order->subtotal, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Discount</span><strong>${{ number_format((float) $order->discount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between"><span>Shipping</span><strong>${{ number_format((float) $order->shipping_fee, 2) }}</strong></div>
                    <div class="d-flex justify-content-between h4 mt-3"><span>Total</span><strong>${{ number_format((float) $order->total, 2) }}</strong></div>
                </div>
            </aside>
        </div>
    </section>
@endsection
