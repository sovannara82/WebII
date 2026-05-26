<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(): View
    {
        $monthStart = Carbon::now()->startOfMonth();
        $lastMonthStart = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        $ordersCount = Order::count();
        $revenue = Order::sum('total');
        $monthRevenue = Order::where('created_at', '>=', $monthStart)->sum('total');
        $lastMonthRevenue = Order::whereBetween('created_at', [$lastMonthStart, $lastMonthEnd])->sum('total');

        return view('admin.dashboard', [
            'brandsCount' => Brand::count(),
            'categoriesCount' => Category::count(),
            'customersCount' => User::whereHas('role', fn ($query) => $query->where('name', 'Customer'))->count(),
            'latestOrders' => Order::with(['user', 'status', 'paymentStatus'])->latest()->take(8)->get(),
            'lowStockProducts' => Product::with(['category', 'brand'])->where('stock', '<=', 6)->orderBy('stock')->take(6)->get(),
            'monthRevenue' => $monthRevenue,
            'monthlyRevenueChange' => $lastMonthRevenue > 0 ? (($monthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100 : null,
            'ordersCount' => $ordersCount,
            'productsCount' => Product::count(),
            'recentProducts' => Product::with(['category', 'brand', 'primaryImage'])->latest()->take(5)->get(),
            'revenue' => $revenue,
            'averageOrderValue' => $ordersCount > 0 ? $revenue / $ordersCount : 0,
        ]);
    }
}
