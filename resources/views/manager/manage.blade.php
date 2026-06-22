@extends('layouts.vertical', ['title' => 'Add Client'])

@section('content')
<style>
    .order-details-section {
        background-color: #f8f9fa;
        padding: 40px 20px;
    }
    .order-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.08);
        padding: 25px;
        margin-bottom: 25px;
    }
    .order-card h6, .order-card h5 {
        font-weight: 600;
    }
    .table > :not(caption) > * > * {
        vertical-align: middle;
    }
    .table img {
        border-radius: 8px;
    }
</style>

<section class="order-details-section">
    <div class="container-fluid"> {{-- ✅ Full width container --}}
        <div class="row">
            <!-- LEFT CONTENT -->
            <div class="col-lg-9 mb-4">
                <!-- Order Details Card -->
                <div class="order-card">
                    <h5 class="mb-3">
                        Order #{{ $order->order_no ?? 'FAD' . str_pad($order->id, 8, '0', STR_PAD_LEFT) }} Details
                    </h5>
                    <p><strong>Payment via:</strong> {{ ucfirst($order->payment_method ?? 'Online') }}</p>

                    <div class="row mt-4">
                        <!-- General -->
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold mb-2">General</h6>
                            <p><strong>Order Created:</strong> {{ $order->created_at->format('Y-m-d H:i:s') }}</p>
                            <p><strong>Order Status:</strong>
                                <select class="form-select form-select-sm w-auto d-inline-block">
                                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="declined" {{ $order->status == 'declined' ? 'selected' : '' }}>Declined</option>
                                </select>
                            </p>
                            <p><strong>Payment Status:</strong> 
                                <select class="form-select form-select-sm w-auto d-inline-block">
                                    <option value="paid" selected>Paid</option>
                                </select>
                            </p>
                        </div>

                        <!-- Billing -->
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold mb-2">Billing</h6>
                            <p><strong>Buyer:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                            <p><strong>Address:</strong> {{ $order->billing_address ?? 'N/A' }}</p>
                            <p><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                            <p><strong>Email:</strong> {{ $order->customer->email ?? 'N/A' }}</p>
                        </div>

                        <!-- Shipping -->
                        <div class="col-md-4 mb-3">
                            <h6 class="fw-bold mb-2">Shipping</h6>
                            <p><strong>Address:</strong> {{ $order->shipping_address ?? 'N/A' }}</p>
                            <p><strong>Order Notes:</strong> {{ $order->notes ?? '—' }}</p>
                            <p><strong>Coupon Code:</strong> {{ $order->coupon_code ?? 'Not Applied' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Items -->
                <div class="order-card">
                    <h6 class="fw-bold mb-3">Items</h6>
                    <p>Total Count: {{ $order->items ? $order->items->count() : 0 }}</p>

                    <div class="table-responsive">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Product Image</th>
                                    <th>Product Name</th>
                                    <th>Cost</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Tracking Link</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <img src="{{ asset('uploads/products/' . ($item->product->thumbnail ?? 'no-image.png')) }}" width="60" alt="">
                                    </td>
                                    <td>{{ $item->product->name ?? 'N/A' }}</td>
                                    <td>₹{{ number_format($item->price, 2) }}</td>
                                    <td>x{{ $item->quantity }}</td>
                                    <td>₹{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    <td>
                                        <input type="text" class="form-control form-control-sm" placeholder="Enter tracking link">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Totals -->
                <div class="order-card">
                    <h6 class="fw-bold mb-3">Order Totals</h6>
                    <table class="table table-borderless w-auto">
                        <tr>
                            <th>Discount Applied:</th>
                            <td>- ₹{{ number_format($order->discount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Coupon Code:</th>
                            <td>{{ $order->coupon_code ?? '—' }}</td>
                        </tr>
                        <tr>
                            <th><strong>Total Payable:</strong></th>
                            <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- RIGHT SIDEBAR -->
            <div class="col-lg-3">
                <div class="order-card">
                    <h6 class="fw-bold mb-2">Billing Info</h6>
                    <p><strong>Name:</strong> {{ $order->customer->name ?? 'N/A' }}</p>
                    <p><strong>Address:</strong> {{ $order->billing_address ?? 'N/A' }}</p>
                    <p><strong>Phone:</strong> {{ $order->customer->phone ?? 'N/A' }}</p>
                </div>

                <div class="order-card text-center">
                    <h6 class="fw-bold mb-3">Create PDF</h6>
                    <a href="{{ route('admin.orders.invoice', $order->id) }}" class="btn btn-dark w-100" target="_blank">PDF INVOICE</a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
