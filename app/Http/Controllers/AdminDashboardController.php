<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        return view('admin.dashboard', [
            'brandsCount' => Brand::count(),
            'categoriesCount' => Category::count(),
            'customersCount' => User::count(),
            'latestOrders' => Order::with(['user', 'status'])->latest()->take(6)->get(),
            'lowStockProducts' => Product::with(['category', 'brand'])->where('stock', '<=', 6)->orderBy('stock')->take(6)->get(),
            'productsCount' => Product::count(),
            'revenue' => Order::sum('total'),
        ]);
    }
}
