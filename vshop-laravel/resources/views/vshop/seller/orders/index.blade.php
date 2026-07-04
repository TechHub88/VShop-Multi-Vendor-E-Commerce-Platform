@extends('vshop.seller.main', ['title' => 'All Orders'])

@section('content')

<style>
.page-title { font-weight:600; font-size:18px; }

.filter-card {
    background:#fff;
    border-radius:10px;
    padding:15px;
    box-shadow:0 2px 6px rgba(0,0,0,0.05);
}

.search-box { width:220px; }

.status-flow { display:flex; gap:5px; flex-wrap:wrap; }

.status-flow .step {
    padding:4px 10px;
    border-radius:20px;
    font-size:11px;
    background:#eef2f7;
}

.payment.active { background:#22c55e;color:#fff; }
.dispatch.active { background:#fb923c;color:#fff; }
.delivery.active { background:#3b82f6;color:#fff; }
.closure.active { background:#8b5cf6;color:#fff; }

.initiated.active { background:#06b6d4;color:#fff; }
.processing.active { background:#facc15;color:#000; }
.completed.active { background:#16a34a;color:#fff; }
.failed.active { background:#ef4444;color:#fff; }

.table thead { background:#f9fafb; font-size:13px; }
.table td { font-size:13px; }

.reminder-section{
    background:#fff8e1;
    border:1px solid #ffe082;
    border-radius:8px;
    padding:8px;
    min-width:180px;
}

.reminder-section .badge{
    font-size:11px;
}

.remove-btn {background: #61b3e5 !important;color: #fff;margin-left: 5px;}
.remove-btn:hover {background: #61b3e5 !important;color: #fff;margin-left: 5px;}
.table th {
    font-weight: 600;
    color: #fff;
    background: #004392 !important;
    border: 1px solid #fff;
}
.table tbody td {
    border: 1px solid #dee2e6;
}
.table-btn-sec a,
.table-btn-sec button {
    border: 1px solid #dee2e6 !important;
    font-size: 14px;
    padding: 7px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
}
.table-btn-sec a i,
.table-btn-sec button i{
    font-size: 12px;
}
.table-btn-sec {
    display: flex;
    align-items: center;
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
.filter-card,
.card {
    border: 1px solid rgb(201 208 218) !important;
}
.btn-dark {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0;
    padding: 5px 6px;
}
.btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

<div class="container-fluid py-4">

{{-- HEADER --}}
<form method="GET">

<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <div class="page-title">All Orders</div>
    </div>

    <input type="text" name="q" value="{{ request('q') }}"
           class="form-control form-control-sm search-box"
           placeholder="Search order...">
</div>

{{-- FILTER --}}
<div class="filter-card mb-3">

<div class="row g-2 align-items-end">

{{-- CATEGORY --}}
<div class="col-md-2">
<label>Category</label>
<select name="category" id="category" class="form-select form-select-sm">
<option value="">All</option>
@foreach($categories as $cat)
<option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>
{{ $cat->name }}
</option>
@endforeach
</select>
</div>

{{-- SUBCATEGORY --}}
<div class="col-md-2">
<label>Subcategory</label>
<select name="subcategory" id="subcategory" class="form-select form-select-sm">
<option value="">All</option>

@foreach($subcategories ?? [] as $sub)
<option value="{{ $sub->id }}" {{ request('subcategory')==$sub->id?'selected':'' }}>
{{ $sub->name }}
</option>
@endforeach

</select>
</div>


{{-- PAYMENT --}}
<div class="col-md-2">
<label>Payment</label>
<select name="payment_type" class="form-select form-select-sm">
<option value="">All</option>
<option value="paid" {{ request('payment_type')=='paid' ? 'selected' : '' }}>Paid</option>
<option value="pending" {{ request('payment_type')=='pending' ? 'selected' : '' }}>Pending</option>
<option value="failed" {{ request('payment_type')=='failed' ? 'selected' : '' }}>Failed</option>
</select>
</div>

{{-- STATUS --}}
<div class="col-md-2">
<label>Status</label>
<select name="status" class="form-select form-select-sm">
<option value="">All</option>
<option value="processing" {{ request('status')=='processing' ? 'selected' : '' }}>Processing</option>
<option value="completed" {{ request('status')=='completed' ? 'selected' : '' }}>Completed</option>
<option value="cancelled" {{ request('status')=='cancelled' ? 'selected' : '' }}>Cancelled</option>
<option value="return_requested" {{ request('status')=='return_requested' ? 'selected' : '' }}>Return Requested</option>
</select>
</div>

{{-- FROM --}}
<div class="col-md-2">
<label>From</label>
<input type="date" name="from_date" value="{{ request('from_date') }}" class="form-control form-control-sm">
</div>

{{-- TO --}}
<div class="col-md-2">
<label>To</label>
<input type="date" name="to_date" value="{{ request('to_date') }}" class="form-control form-control-sm">
</div>

{{-- APPLY --}}
<div class="col-md-1">
<div class="d-flex">
    <button type="submit" class="btn btn-dark btn-sm w-100 mt-4">Apply</button>
    <button type="submit" class="btn remove-btn btn-sm w-100 mt-4">Reset</button>
</div>
</div>

</div>
</div>

</form>

{{-- TABLE --}}
<div class="card">
<div class="card-body table-responsive">

<table class="table table-bordered align-middle">
<thead>
<tr>
<th>#</th>
<th>Customer</th>
<th>Products</th>
<th>Total</th>
<th>Payment</th>
<th>Date</th>
<th>Status Flow</th>
<th>Refund Flow</th>
<th>SLA</th>
<th>Reminder</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@foreach($orders as $order)

@php
$status = strtolower($order->status ?? 'processing');
$refund = strtolower($order->refund_status ?? '');
$payment = strtolower($order->payment_status ?? 'pending');
@endphp

<tr>

<td>#{{ $order->order_no }}</td>

<td>{{ optional($order->customer)->name }}</td>

<td>
@foreach($order->items as $item)
    <div class="mb-2" style="line-height: 1.2;">
        <strong>{{ $item->product->name ?? 'Unknown Product' }}</strong><br>
        <span class="text-muted" style="font-size: 11px;">Qty: {{ $item->quantity }} | ₹{{ number_format($item->price, 2) }}</span>
    </div>
@endforeach
</td>

<td>
    ₹ {{ number_format($order->total_amount, 2) }}
</td>

<td>
<span class="badge bg-{{ $payment=='success'||$payment=='paid' ? 'success' : ($payment=='failed'?'danger':'warning') }}">
{{ ucfirst($payment) }}
</span>
<div style="font-size: 11px; margin-top: 5px; font-weight: 600; color: #555;">
    {{ strtoupper(str_replace('_', ' ', $order->payment_method ?? 'N/A')) }}
</div>
</td>

<td>{{ $order->created_at->format('d M Y') }}</td>

<td>
@if($status == 'cancelled')
<span class="badge bg-danger">Cancelled</span>
@else
<div class="status-flow">
<span class="step payment {{ in_array($status,['paid','processing','dispatch','completed'])?'active':'' }}">Payment</span>
<span class="step dispatch {{ in_array($status,['dispatch','completed'])?'active':'' }}">Dispatch</span>
<span class="step delivery {{ in_array($status,['completed'])?'active':'' }}">Delivery</span>
<span class="step closure {{ $status=='completed'?'active':'' }}">Closure</span>
</div>
@endif
</td>

<td>
@if(in_array($status,['return_requested','returned']) || $refund)
<div class="status-flow mb-1">

<span class="step initiated {{ $refund=='initiated'?'active':'' }}">Initiated</span>
<span class="step processing {{ $refund=='processing'?'active':'' }}">Processing</span>
<span class="step completed {{ $refund=='completed'?'active':'' }}">Completed</span>
<span class="step failed {{ $refund=='failed'?'active':'' }}">Failed</span>

</div>

@if($order->return_reason)
<div class="text-danger small">
<strong>Reason:</strong> {{ $order->return_reason }}
</div>
@endif
@endif
</td>

<td>
<select name="sla_status" class="form-select form-select-sm sla-dropdown">

<option value="">Select</option>
<option value="on_time" {{ $order->sla_status=='on_time'?'selected':'' }}>On Time</option>
<option value="delayed" {{ $order->sla_status=='delayed'?'selected':'' }}>Delayed</option>
<option value="breached" {{ $order->sla_status=='breached'?'selected':'' }}>Breached (Due Date)</option>
<option value="not_available" {{ $order->sla_status=='not_available'?'selected':'' }}>Not Available</option>

</select>
</td>

<td>

@if($order->reminder_count > 0)

<div class="reminder-section">

    <span class="badge bg-warning text-dark mb-1">
        {{ $order->reminder_count }} Reminder(s)
    </span>

    <div class="small text-muted">
        {{ \Carbon\Carbon::parse($order->reminder_sent_at)->format('d M Y h:i A') }}
    </div>

    @if($order->last_reminder_note)
    <div class="small mt-1">
        <strong>Note:</strong>
        {{ $order->last_reminder_note }}
    </div>
    @endif

</div>

@else

<span class="text-muted small">No Reminder</span>

@endif

</td>

<td>
<a href="{{ route('seller.orders.manage',$order->id) }}" class="btn btn-sm btn-primary mb-1">View</a>
<a href="{{ route('seller.orders.invoice',$order->id) }}" class="btn btn-sm btn-dark mb-1">Invoice</a>

@if(!in_array($status,['cancelled','completed']))
<a href="{{ route('seller.orders.cancel',$order->id) }}" class="btn btn-sm btn-danger mb-1">Cancel</a>
@endif



</td>

</tr>

@endforeach
</tbody>
</table>

{{ $orders->links('pagination::bootstrap-5') }}

</div>
</div>

</div>

{{-- RETURN MODAL --}}
<div class="modal fade" id="returnModal">
<div class="modal-dialog">
<form method="POST" id="returnForm">@csrf
<div class="modal-content">
<div class="modal-header">
<h5>Return Reason</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<textarea name="return_reason" class="form-control" required></textarea>
</div>
<div class="modal-footer">
<button class="btn btn-primary">Submit</button>
</div>
</div>
</form>
</div>
</div>

{{-- FIXED SCRIPT --}}
<script>

// RETURN MODAL
document.querySelectorAll('.openReturnModal').forEach(btn=>{
btn.onclick=function(){
document.getElementById('returnForm').action='/seller/orders/return/'+this.dataset.id;
new bootstrap.Modal(document.getElementById('returnModal')).show();
}
});

const categoryEl = document.getElementById('category');
const subcategoryEl = document.getElementById('subcategory');

// store selected value from backend (initial page load)
let selectedSubcategory = "{{ request('subcategory') }}";

if (categoryEl && subcategoryEl) {

    categoryEl.addEventListener('change', function () {

        fetch('/seller/order/getsubcategories?category_id=' + this.value)
            .then(res => res.json())
            .then(data => {

                subcategoryEl.innerHTML = '<option value="">All</option>';

                data.forEach(i => {

                    let isSelected = (selectedSubcategory == i.id) ? 'selected' : '';

                    subcategoryEl.innerHTML += `
                        <option value="${i.id}" ${isSelected}>
                            ${i.name}
                        </option>
                    `;
                });

            });

    });

}

</script>

@endsection