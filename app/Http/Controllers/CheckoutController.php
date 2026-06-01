<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\PaymentStatus;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Request $request): View
    {
        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cart->load('items.product.primaryImage');
        $subtotal = $cart->items->sum(fn (CartItem $item): float => (float) $item->price * $item->quantity);
        $shippingFee = $subtotal >= 150 ? 0 : 5;
        $coupons = Coupon::where('start_date', '<=', now())->where('end_date', '>=', now())->get();

        return view('shop.checkout', [
            'cart' => $cart,
            'coupons' => $coupons,
            'couponPreviews' => $coupons
                ->filter(fn (Coupon $coupon): bool => $this->couponCanApply($coupon, $subtotal))
                ->mapWithKeys(fn (Coupon $coupon): array => [
                    $coupon->code => [
                        'discount' => $this->discountFor($coupon, $subtotal),
                    ],
                ]),
            'items' => $cart->items,
            'paymentMethods' => PaymentMethod::orderBy('name')->get(),
            'shippingFee' => $shippingFee,
            'subtotal' => $subtotal,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:20'],
            'payment_method_id' => ['required', 'exists:payment_methods,id'],
            'coupon_code' => ['nullable', 'string', 'max:50'],
        ]);

        $cart = Cart::firstOrCreate(['user_id' => $request->user()->id]);
        $cart->load('items.product');

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('status', 'Your cart is empty.');
        }

        $order = DB::transaction(function () use ($cart, $request, $validated): Order {
            $subtotal = $cart->items->sum(fn (CartItem $item): float => (float) $item->price * $item->quantity);
            $shippingFee = $subtotal >= 150 ? 0 : 5;
            $coupon = $this->validCoupon($validated['coupon_code'] ?? null, $subtotal);
            $discount = $coupon ? $this->discountFor($coupon, $subtotal) : 0;
            $total = max(0, $subtotal - $discount + $shippingFee);

            $order = Order::create([
                'user_id' => $request->user()->id,
                'status_id' => OrderStatus::firstOrCreate(['name' => 'Processing'])->id,
                'payment_status_id' => PaymentStatus::firstOrCreate(['name' => 'Paid'])->id,
                'payment_method_id' => $validated['payment_method_id'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'phone' => $validated['phone'],
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shippingFee,
                'total' => $total,
            ]);

            foreach ($cart->items as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'subtotal' => (float) $item->price * $item->quantity,
                ]);

                $item->product->decrement('stock', min($item->quantity, max(0, $item->product->stock)));
            }

            if ($coupon) {
                $order->coupons()->attach($coupon->id, ['discount' => $discount]);
                $coupon->increment('used_count');
            }

            Payment::create([
                'order_id' => $order->id,
                'amount' => $total,
                'payment_method_id' => $validated['payment_method_id'],
                'payment_status_id' => $order->payment_status_id,
                'paid_at' => now(),
            ]);

            $cart->items()->delete();

            return $order;
        });

        return redirect()->route('orders.show', $order)->with('status', 'Order placed successfully.');
    }

    private function validCoupon(?string $code, float $subtotal): ?Coupon
    {
        if (! $code) {
            return null;
        }

        return Coupon::where('code', $code)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->where(function ($query): void {
                $query->whereNull('usage_limit')->orWhereColumn('used_count', '<', 'usage_limit');
            })
            ->where(function ($query) use ($subtotal): void {
                $query->whereNull('min_order')->orWhere('min_order', '<=', $subtotal);
            })
            ->first();
    }

    private function couponCanApply(Coupon $coupon, float $subtotal): bool
    {
        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return false;
        }

        if ($coupon->min_order !== null && (float) $coupon->min_order > $subtotal) {
            return false;
        }

        return true;
    }

    private function discountFor(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->type === 'percent') {
            return round($subtotal * ((float) $coupon->value / 100), 2);
        }

        return min((float) $coupon->value, $subtotal);
    }
}
