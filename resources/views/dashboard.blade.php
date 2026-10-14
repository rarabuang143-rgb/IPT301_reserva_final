@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <p class="text-success fw-semibold mb-1">ONLINE FOOD DELIVERY SYSTEM</p>
            <h1 class="page-heading display-6 mb-1">Operations dashboard</h1>
            <p class="text-secondary mb-0">A quick view of your restaurants, menu, customers, and orders.</p>
        </div>
        <a href="{{ route('orders.create') }}" class="btn btn-primary">＋ Create an order</a>
    </div>

    <div class="row g-3 mb-4">
        @foreach ([['Customers', $customerCount, 'customers.index'], ['Restaurants', $restaurantCount, 'restaurants.index'], ['Food & items', $foodCount, 'foods.index'], ['Orders', $orderCount, 'orders.index'], ['Deliveries', $deliveryCount, 'deliveries.index']] as [$label, $count, $route])
            <div class="col-6 col-lg">
                <a href="{{ route($route) }}" class="text-decoration-none text-reset">
                    <div class="card h-100"><div class="card-body">
                        <div class="text-secondary small">{{ $label }}</div>
                        <div class="display-6 fw-bold">{{ $count }}</div>
                        <span class="small text-success">View records →</span>
                    </div></div>
                </a>
            </div>
        @endforeach
    </div>

    <div class="card">
        <div class="card-header bg-white border-0 p-4 d-flex flex-wrap justify-content-between gap-2">
            <div><h2 class="h5 fw-bold mb-1">Recent orders</h2><p class="small text-secondary mb-0">Latest order activity across your restaurants.</p></div>
            <a href="{{ route('orders.index') }}" class="btn btn-outline-success btn-sm align-self-center">All orders</a>
        </div>
        <div class="table-responsive">
            <table class="table mb-0">
                <thead class="table-light"><tr><th>Order</th><th>Customer</th><th>Item</th><th>Restaurant</th><th>Total</th><th>Status</th></tr></thead>
                <tbody>
                @forelse ($recentOrders as $order)
                    <tr>
                        <td><a href="{{ route('orders.show', $order) }}" class="fw-semibold text-success">#{{ $order->id }}</a></td>
                        <td>{{ $order->customer->name }}</td><td>{{ $order->food->name }} × {{ $order->quantity }}</td>
                        <td>{{ $order->food->restaurant->name }}</td><td>${{ number_format((float) $order->total, 2) }}</td>
                        <td><span class="badge badge-soft text-capitalize">{{ str_replace('_', ' ', $order->status) }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-secondary py-5">No orders yet. Create your first order to get started.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
