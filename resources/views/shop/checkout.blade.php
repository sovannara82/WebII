@extends('layouts.app')

@section('title', 'Checkout - KRUY')

@section('content')
    <section class="container py-5">
        <h1 class="h2 fw-bold mb-4">Checkout</h1>
        <div class="row g-4">
            <div class="col-lg-7">
                <form class="admin-panel" action="{{ route('checkout.store') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label" for="address">Address</label>
                            <input class="form-control" id="address" name="address" value="{{ old('address', Auth::user()->address) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="city">City</label>
                            <input class="form-control" id="city" name="city" value="{{ old('city', Auth::user()->city) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="phone">Phone</label>
                            <input class="form-control" id="phone" name="phone" value="{{ old('phone', Auth::user()->phone) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="payment_method_id">Payment Method</label>
                            <select class="form-select" id="payment_method_id" name="payment_method_id" required>
                                @foreach ($paymentMethods as $method)
                                    <option value="{{ $method->id }}">{{ $method->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label" for="coupon_code">Coupon</label>
                            <input class="form-control" id="coupon_code" name="coupon_code" list="coupon-list" placeholder="Optional code">
                            <datalist id="coupon-list">
                                @foreach ($coupons as $coupon)
                                    <option value="{{ $coupon->code }}">{{ $coupon->type }} {{ $coupon->value }}</option>
                                @endforeach
                            </datalist>
                        </div>
                    </div>
                    <button class="btn btn-gold btn-lg mt-4" type="submit">Place Order</button>
                </form>
            </div>
            <aside class="col-lg-5">
                <div class="summary-panel">
                    <h2 class="h5 fw-bold mb-3">Order Summary</h2>
                    @foreach ($items as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item->product->name }} x {{ $item->quantity }}</span>
                            <strong>${{ number_format((float) $item->price * $item->quantity, 2) }}</strong>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <strong>${{ number_format($subtotal, 2) }}</strong>
                    </div>
                </div>
            </aside>
        </div>
    </section>
@endsection
