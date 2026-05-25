@extends('layouts.admin')

@section('title', 'Admin Dashboard - FigureVerse')
@section('admin-title', 'Dashboard')
@section('admin-subtitle', 'Welcome back, '.(Auth::user()->name ?? 'Admin').'.')

@section('content')
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="card-icon"><i class="bi bi-box-seam"></i></div>
                        <span>Products</span>
                        <strong>{{ $productsCount }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="card-icon"><i class="bi bi-tags-fill"></i></div>
                        <span>Categories</span>
                        <strong>{{ $categoriesCount }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="card-icon"><i class="bi bi-gem"></i></div>
                        <span>Brands</span>
                        <strong>{{ $brandsCount }}</strong>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="metric-card">
                        <div class="card-icon"><i class="bi bi-currency-dollar"></i></div>
                        <span>Revenue</span>
                        <strong>${{ number_format((float) $revenue, 2) }}</strong>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="admin-panel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h5 fw-bold mb-0">Recent orders</h2>
                            <span class="admin-muted">{{ $customersCount }} customers</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead>
                                    <tr>
                                        <th>Order</th>
                                        <th>Customer</th>
                                        <th>Status</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($latestOrders as $order)
                                        <tr>
                                            <td>#{{ $order->id }}</td>
                                            <td>{{ $order->user->name }}</td>
                                            <td><span class="badge-premium">{{ $order->status->name }}</span></td>
                                            <td class="text-end">${{ number_format((float) $order->total, 2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="admin-muted">No orders yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="admin-panel">
                        <h2 class="h5 fw-bold mb-3">Low stock</h2>
                        @forelse ($lowStockProducts as $product)
                            <div class="stock-line">
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <p class="admin-muted mb-0 small">{{ $product->category->name }} / {{ $product->brand?->name ?? 'Studio' }}</p>
                                </div>
                                <span class="badge {{ $product->stock <= 2 ? 'text-bg-danger' : 'text-bg-warning' }}">{{ $product->stock }}</span>
                            </div>
                        @empty
                            <p class="admin-muted mb-0">Inventory levels look healthy.</p>
                        @endforelse
                    </div>
                </div>
            </div>
@endsection
