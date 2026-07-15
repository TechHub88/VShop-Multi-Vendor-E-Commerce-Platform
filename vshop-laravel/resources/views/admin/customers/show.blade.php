@extends('layouts.vertical', ['title' => 'Buyer Details'])

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-3">
        <h4 class="fw-semibold mb-0">Buyer Profile: {{ $customer->name }}</h4>
        <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row g-3">
        <!-- Profile Card -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 32px; font-weight: bold;">
                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                        </div>
                    </div>
                    <h5 class="fw-bold mb-1">{{ $customer->name }}</h5>
                    <p class="text-muted mb-3">Buyer ID: #{{ $customer->id }}</p>
                    
                    <span class="badge rounded-pill px-3 py-2 fs-12 {{ $customer->status == 'active' ? 'bg-success bg-opacity-10 text-success' : ($customer->status == 'blocked' ? 'bg-danger bg-opacity-10 text-danger' : 'bg-secondary bg-opacity-10 text-secondary') }}">
                        {{ ucfirst($customer->status) }}
                    </span>

                    <hr class="my-4">

                    <div class="text-start">
                        <h6 class="fw-bold text-uppercase text-muted fs-11 mb-3">Contact Details</h6>
                        <div class="d-flex mb-2 align-items-center">
                            <i class="fas fa-envelope text-primary me-2" style="width: 20px;"></i>
                            <span class="text-muted">{{ $customer->email }}</span>
                        </div>
                        <div class="d-flex mb-2 align-items-center">
                            <i class="fas fa-phone text-primary me-2" style="width: 20px;"></i>
                            <span class="text-muted">{{ $customer->phone ?? 'N/A' }}</span>
                        </div>
                        <div class="d-flex mb-3 align-items-center">
                            <i class="fas fa-calendar text-primary me-2" style="width: 20px;"></i>
                            <span class="text-muted">Joined {{ $customer->created_at->format('d M, Y') }}</span>
                        </div>

                        <h6 class="fw-bold text-uppercase text-muted fs-11 mb-3">System Context</h6>
                        <div class="d-flex mb-2 align-items-center justify-content-between">
                            <span class="text-muted">Buyer Type:</span>
                            <span class="badge {{ empty($customer->seller_id) ? 'bg-info' : 'bg-primary' }} px-2 py-1">
                                {{ empty($customer->seller_id) ? 'Direct Buyer' : 'Vshop-Buyer' }}
                            </span>
                        </div>
                        @if($customer->seller)
                        <div class="d-flex mb-2 align-items-center justify-content-between">
                            <span class="text-muted font-13">Created By Seller:</span>
                            <span class="fw-semibold">#{{ $customer->seller->id }} - {{ $customer->seller->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Orders & Stats -->
        <div class="col-lg-8">
            <!-- Stats Bar -->
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-shopping-bag fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-12">Total Orders</div>
                                <div class="fw-bold fs-5">{{ $totalOrders }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-3">
                        <div class="card-body p-3 d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                <i class="fas fa-dollar-sign fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted fs-12">Total Spent</div>
                                <div class="fw-bold fs-5">₹{{ number_format($totalSpent, 2) }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Table -->
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-transparent border-0 pt-3 pb-0">
                    <h5 class="fw-semibold mb-0">Recent Orders</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Payment</th>
                                    <th>Delivery</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($customer->orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>{{ $order->created_at->format('d M, Y') }}</td>
                                    <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                    <td>
                                        <span class="badge bg-opacity-10 {{ $order->payment_status == 'paid' ? 'bg-success text-success' : 'bg-warning text-warning' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-opacity-10 {{ $order->delivery_status == 'delivered' ? 'bg-success text-success' : 'bg-primary text-primary' }}">
                                            {{ ucfirst($order->delivery_status) }}
                                        </span>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No orders found for this buyer.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
