@extends('vshop.seller.main', ['title' => 'Manage Order'])

@section('content')

<style>
.section-card {
    border-radius:12px;
    padding:18px;
    background:#fff;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    margin-bottom:18px;
}

/* HEADER */
.order-header {
    background:#fff;
    padding:15px 20px;
    border-radius:12px;
    box-shadow:0 4px 12px rgba(0,0,0,0.05);
    margin-bottom:15px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

/* TIMELINE */
.timeline {
    border-left:3px solid #e5e7eb;
    padding-left:15px;
}
.timeline-step {
    margin-bottom:12px;
    position:relative;
}
.timeline-step::before {
    content:'';
    position:absolute;
    left:-10px;
    top:4px;
    width:10px;
    height:10px;
    background:#3b82f6;
    border-radius:50%;
}

/* RIGHT PANEL */
.right-panel {
    position:sticky;
    top:15px;
}

/* BADGES */
.badge-soft {
    padding:5px 10px;
    border-radius:20px;
    font-size:11px;
}

/* TABLE */
.table th {
    background:#f9fafb;
}
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    font-size: 4px;
    width: 35px;
    height: 38px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.order-header,
.section-card {
    border: 1px solid rgb(201 208 218) !important;
}

.table th {
    font-weight: 600;
    color: #fff;
    background: #004392 !important;
    border: 1px solid #fff;
}
.table tbody td {
    border: 1px solid #dee2e6;
}
.btn-dark {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0;
    padding: 8px 6px;
}
</style>

<div class="container-fluid py-3">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
</div>

{{-- HEADER --}}
<div class="order-header">
    <div>
        <h5 class="mb-1">Order #{{ $order->id }}</h5>
        <small class="text-muted">{{ $order->created_at->format('d M Y, h:i A') }}</small>
    </div>

    <div>
        <span class="badge bg-success">{{ ucfirst($order->payment_status) }}</span>
        <span class="badge bg-primary">{{ ucfirst($order->status) }}</span>
    </div>
</div>

<div class="row">

{{-- LEFT --}}
<div class="col-lg-8">

{{-- CUSTOMER --}}
<div class="section-card">
<h6 class="mb-3">Customer Details</h6>

<div class="row">
<div class="col-md-6">
<p class="mb-1"><strong>Name:</strong></p>
<p>{{ $order->customer->name ?? '-' }}</p>
</div>

<div class="col-md-6">
<p class="mb-1"><strong>Email:</strong></p>
<p>{{ $order->customer->email ?? '-' }}</p>
</div>
</div>

</div>

{{-- ITEMS --}}
<div class="section-card">
<h6 class="mb-3">Order Items</h6>

<table class="table align-middle">
<thead>
<tr>
<th>Image</th>
<th>Product</th>
<th>Qty</th>
<th>Price</th>
<th>Total</th>
</tr>
</thead>

<tbody>
@foreach($order->items as $item)
<tr>
<td width="80">
<img src="{{ asset($item->product->thumbnail ?? 'images/no-image.png') }}" width="60" class="rounded">
</td>

<td>
<strong>{{ $item->product->name ?? '-' }}</strong>
</td>

<td>{{ $item->quantity }}</td>
<td>₹{{ number_format($item->price,2) }}</td>
<td><strong>₹{{ number_format($item->price * $item->quantity,2) }}</strong></td>
</tr>
@endforeach
</tbody>
</table>

</div>




{{-- TIMELINE --}}
<div class="section-card">
<h6 class="mb-3">Order Timeline</h6>

<div class="timeline">

<div class="timeline-step">
<strong>Order Created</strong>
<div class="text-muted small">{{ $order->created_at }}</div>
</div>

@if($order->status == 'processing')
<div class="timeline-step">Processing Started</div>
@endif

@if($order->status == 'dispatch')
<div class="timeline-step">Dispatched</div>
@endif

@if($order->status == 'completed')
<div class="timeline-step text-success">Delivered</div>
@endif

@if($order->status == 'return_requested')
<div class="timeline-step text-warning">Return Requested</div>
@endif

@if($order->refund_status == 'completed')
<div class="timeline-step text-success">Refund Completed</div>
@endif

</div>

</div>

{{-- ACTIVITY --}}
<div class="section-card">
<h6 class="mb-3">Activity Log</h6>

@forelse($order->logs ?? [] as $log)
<div class="border-bottom pb-2 mb-2">
<strong>{{ $log->action }}</strong><br>
<small class="text-muted">{{ $log->user }} • {{ $log->created_at }}</small>
</div>
@empty
<p class="text-muted">No activity found</p>
@endforelse

</div>

</div>

{{-- RIGHT PANEL --}}
<div class="col-lg-4">
<div class="right-panel">

{{-- PAYMENT --}}
<div class="section-card">
<h6 class="mb-3">Payment</h6>

<p>Status:
<span class="badge bg-{{ $order->payment_status=='success'?'success':($order->payment_status=='failed'?'danger':'warning') }}">
{{ ucfirst($order->payment_status) }}
</span>
</p>

<p>Type: {{ $order->payment_type ?? 'Full/Partial' }}</p>
<p>Retry Count: {{ $order->retry_count ?? 0 }}</p>

</div>

{{-- REFUND --}}
<div class="section-card">
<h6 class="mb-3">Refund</h6>

<p>Status: {{ ucfirst($order->refund_status ?? '-') }}</p>
<p>Reason: {{ $order->return_reason ?? '-' }}</p>

@if($order->return_requested_at)
@php $days = now()->diffInDays($order->return_requested_at); @endphp

@if($days <= 7)
<span class="badge bg-success">Within SLA</span>
@else
<span class="badge bg-danger">SLA Breached</span>
@endif
@endif

</div>

{{-- SUPPLIER --}}
<div class="section-card">
<h6 class="mb-3">Supplier (Dropshipping)</h6>

<p>Status: {{ $order->supplier_status ?? '-' }}</p>
<p>Tracking ID: {{ $order->supplier_tracking_id ?? '-' }}</p>
<p>Auto Sync: {{ $order->auto_sync_status ?? 'Pending' }}</p>

</div>

{{-- PAYMENT HISTORY --}}
<div class="section-card">
<h6 class="mb-3">Payment Attempts</h6>

@forelse($order->paymentAttempts ?? [] as $p)
<div class="border-bottom pb-2 mb-2">
{{ ucfirst($p->status) }}<br>
<small class="text-muted">{{ $p->created_at }}</small>
</div>
@empty
<p class="text-muted">No payment attempts</p>
@endforelse

</div>

{{-- INVOICE --}}
<div class="section-card">
<h6 class="mb-3">Invoice</h6>

<p>GST No: {{ $order->gst_no ?? '-' }}</p>

<a href="{{ route('seller.orders.invoice',$order->id) }}" class="btn btn-dark btn-sm w-100">
Download Invoice
</a>

</div>

</div>
</div>

</div>

</div>

@endsection
