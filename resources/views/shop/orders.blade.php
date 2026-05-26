@extends('layouts.customer')

@section('title', 'Orders - Infinity Figures')

@section('content')
    <section class="container py-5">
        <h1 class="h2 fw-bold mb-4">My orders</h1>
        <div class="admin-panel">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Order</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>
                            <td><span class="badge-premium">{{ $order->status->name }}</span></td>
                            <td>{{ $order->paymentStatus->name }} / {{ $order->paymentMethod?->name }}</td>
                            <td>${{ number_format((float) $order->total, 2) }}</td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-light" href="{{ route('orders.show', $order) }}">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="admin-muted">No orders yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
            {{ $orders->links() }}
        </div>
    </section>
@endsection
