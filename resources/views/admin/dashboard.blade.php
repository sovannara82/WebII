@extends('layouts.admin')

@section('title', 'Admin Dashboard - Infinity Figures')
@section('admin-title', 'Operations Dashboard')
@section('admin-subtitle', 'Inventory, orders, and sales at a glance.')

@section('content')
    <section class="admin-hero-panel mb-4">
        <div>
            <span>Today at Infinity Figures</span>
            <h2>Keep the customer pipeline moving.</h2>
            <p>Review new orders, protect stock levels, and jump into the data screens your team uses most.</p>
        </div>
        <div class="admin-hero-stats">
            <div>
                <small>This Month</small>
                <strong>${{ number_format((float) $monthRevenue, 2) }}</strong>
                <span>
                    @if ($monthlyRevenueChange === null)
                        New baseline
                    @else
                        {{ $monthlyRevenueChange >= 0 ? '+' : '' }}{{ number_format($monthlyRevenueChange, 1) }}% vs last month
                    @endif
                </span>
            </div>
            <div>
                <small>Average Order</small>
                <strong>${{ number_format((float) $averageOrderValue, 2) }}</strong>
                <span>{{ $ordersCount }} total orders</span>
            </div>
        </div>
    </section>

    <div class="admin-metric-grid mb-4">
        <div class="metric-card">
            <div class="card-icon"><i class="bi bi-currency-dollar"></i></div>
            <span>Total Sales</span>
            <strong>${{ number_format((float) $revenue, 2) }}</strong>
        </div>
        <div class="metric-card">
            <div class="card-icon"><i class="bi bi-box-seam"></i></div>
            <span>Total Products</span>
            <strong>{{ $productsCount }}</strong>
        </div>
        <div class="metric-card">
            <div class="card-icon"><i class="bi bi-receipt"></i></div>
            <span>Total Orders</span>
            <strong>{{ $ordersCount }}</strong>
        </div>
        <div class="metric-card">
            <div class="card-icon"><i class="bi bi-people-fill"></i></div>
            <span>Customers</span>
            <strong>{{ $customersCount }}</strong>
        </div>
        <div class="metric-card danger-card">
            <div class="card-icon"><i class="bi bi-exclamation-triangle"></i></div>
            <span>Low Stock Alerts</span>
            <strong>{{ $lowStockProducts->count() }}</strong>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-8">
            <div class="admin-panel admin-table-panel">
                <div class="panel-heading">
                    <div>
                        <span>Order Flow</span>
                        <h2>Recent Orders</h2>
                    </div>
                    <a class="btn btn-outline-light btn-sm" href="{{ route('admin.data', ['table' => 'orders']) }}">View All</a>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle admin-dashboard-table">
                        <thead>
                            <tr>
                                <th>Order</th>
                                <th>Customer</th>
                                <th>Status</th>
                                <th>Payment</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestOrders as $order)
                                <tr>
                                    <td>
                                        <strong>#{{ $order->id }}</strong>
                                        <small>{{ $order->created_at?->format('M d, Y') }}</small>
                                    </td>
                                    <td>{{ $order->user->name }}</td>
                                    <td><span class="badge-premium">{{ $order->status->name }}</span></td>
                                    <td><span class="admin-muted">{{ $order->paymentStatus->name }}</span></td>
                                    <td class="text-end fw-bold">${{ number_format((float) $order->total, 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="admin-muted">No orders yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="admin-panel h-100">
                <div class="panel-heading compact">
                    <div>
                        <span>Inventory</span>
                        <h2>Low stock</h2>
                    </div>
                    <a class="btn btn-outline-light btn-sm" href="{{ route('admin.data', ['table' => 'products']) }}">Products</a>
                </div>

                @forelse ($lowStockProducts as $product)
                    <div class="stock-line enhanced-stock-line">
                        <div>
                            <strong>{{ $product->name }}</strong>
                            <p class="admin-muted mb-2 small">{{ $product->category->name }} / {{ $product->brand?->name ?? 'Studio' }}</p>
                            <div class="stock-meter">
                                <span style="width: {{ min(100, max(8, $product->stock * 12)) }}%"></span>
                            </div>
                        </div>
                        <span class="badge {{ $product->stock <= 2 ? 'text-bg-danger' : 'text-bg-warning' }}">{{ $product->stock }}</span>
                    </div>
                @empty
                    <p class="admin-muted mb-0">Inventory levels look healthy.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-xl-7">
            <div class="admin-panel">
                <div class="panel-heading">
                    <div>
                        <span>Catalog</span>
                        <h2>Latest products</h2>
                    </div>
                    <a class="btn btn-outline-light btn-sm" href="{{ route('admin.data', ['table' => 'products']) }}">Manage catalog</a>
                </div>

                <div class="admin-product-list">
                    @forelse ($recentProducts as $product)
                        <a href="{{ route('shop.show', $product) }}">
                            <img src="{{ $product->primaryImage?->image ?? 'https://images.unsplash.com/photo-1618336753974-aae8e04506aa?auto=format&fit=crop&w=300&q=80' }}" alt="{{ $product->name }}">
                            <span>
                                <strong>{{ $product->name }}</strong>
                                <small>{{ $product->category->name }} / {{ $product->brand?->name ?? 'Studio' }}</small>
                            </span>
                            <em>${{ number_format((float) $product->price, 2) }}</em>
                        </a>
                    @empty
                        <p class="admin-muted mb-0">No products yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-xl-5">
            <div class="admin-panel">
                <div class="panel-heading compact">
                    <div>
                        <span>Shortcuts</span>
                        <h2>Quick actions</h2>
                    </div>
                </div>

                <div class="admin-action-grid">
                    <a href="{{ route('admin.data', ['table' => 'products']) }}"><i class="bi bi-box-seam"></i> Products</a>
                    <a href="{{ route('admin.data', ['table' => 'orders']) }}"><i class="bi bi-receipt"></i> Orders</a>
                    <a href="{{ route('admin.data', ['table' => 'categories']) }}"><i class="bi bi-tags-fill"></i> Categories</a>
                    <a href="{{ route('admin.data', ['table' => 'banners']) }}"><i class="bi bi-images"></i> Banners</a>
                    <a href="{{ route('admin.data', ['table' => 'coupons']) }}"><i class="bi bi-percent"></i> Coupons</a>
                    <a href="{{ route('shop.index') }}"><i class="bi bi-shop"></i> View shop</a>
                </div>
            </div>
        </div>
    </div>
@endsection
