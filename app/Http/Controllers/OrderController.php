<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request): View
    {
        return view('shop.orders', [
            'orders' => Order::with(['status', 'paymentStatus', 'paymentMethod'])
                ->where('user_id', $request->user()->id)
                ->latest()
                ->paginate(10),
        ]);
    }

    public function show(Request $request, Order $order): View
    {
        abort_unless($order->user_id === $request->user()->id, 403);

        $order->load(['items.product.primaryImage', 'status', 'paymentStatus', 'paymentMethod', 'payments.method', 'coupons']);

        return view('shop.order-show', ['order' => $order]);
    }
}
