@extends('layouts.app')

@section('title', 'Checkout - Infinity Figures')

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
                            <input class="form-control" id="coupon_code" name="coupon_code" list="coupon-list" placeholder="Optional code" data-coupon-input>
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
                    <div class="d-flex justify-content-between mt-2" data-discount-row hidden>
                        <span>Discount</span>
                        <strong>-<span data-discount-amount>$0.00</span></strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2" data-after-discount-row hidden>
                        <span>Price after discount</span>
                        <strong data-after-discount-amount>${{ number_format($subtotal, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Shipping</span>
                        <strong>{{ $shippingFee > 0 ? '$'.number_format($shippingFee, 2) : 'Free' }}</strong>
                    </div>
                    <div class="d-flex justify-content-between h4 mt-3">
                        <span>Total</span>
                        <strong data-total-amount>${{ number_format($subtotal + $shippingFee, 2) }}</strong>
                    </div>
                </div>
            </aside>
        </div>
    </section>

    <script>
        (() => {
            const subtotal = Number(@json($subtotal));
            const shippingFee = Number(@json($shippingFee));
            const coupons = @json($couponPreviews);
            const couponInput = document.querySelector('[data-coupon-input]');
            const discountRow = document.querySelector('[data-discount-row]');
            const afterDiscountRow = document.querySelector('[data-after-discount-row]');
            const discountAmount = document.querySelector('[data-discount-amount]');
            const afterDiscountAmount = document.querySelector('[data-after-discount-amount]');
            const totalAmount = document.querySelector('[data-total-amount]');
            const money = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' });

            if (! couponInput || ! discountRow || ! afterDiscountRow || ! discountAmount || ! afterDiscountAmount || ! totalAmount) {
                return;
            }

            const updateSummary = () => {
                const coupon = coupons[couponInput.value.trim()];
                const discount = coupon ? Number(coupon.discount) : 0;
                const priceAfterDiscount = Math.max(0, subtotal - discount);
                const total = priceAfterDiscount + shippingFee;

                discountRow.hidden = discount <= 0;
                afterDiscountRow.hidden = discount <= 0;
                discountAmount.textContent = money.format(discount);
                afterDiscountAmount.textContent = money.format(priceAfterDiscount);
                totalAmount.textContent = money.format(total);
            };

            couponInput.addEventListener('input', updateSummary);
            updateSummary();
        })();
    </script>
@endsection
