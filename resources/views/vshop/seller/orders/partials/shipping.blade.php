@extends('vshop.seller.main')

@section('content')

<meta name="csrf-token" content="{{ csrf_token() }}">

<style>

/* ===== GLOBAL ===== */
body{
    background:#f6f8fc;
    font-family:'Inter',sans-serif;
}

/* ===== CARD ===== */
.cardx{
    background:#fff;
    border-radius:16px;
    padding:20px;
    box-shadow:0 8px 25px rgba(0,0,0,0.05);
    transition:0.3s;
}
.cardx:hover{
    transform:translateY(-2px);
}

/* ===== TITLE ===== */
.section-title{
    font-weight:600;
    font-size:15px;
    margin-bottom:12px;
}

/* ===== INPUT ===== */
.form-control{
    border-radius:10px;
    border:1px solid #e3e6f0;
    padding:10px;
    height:45px;
}

.form-control:focus{
    border-color:#4e73df;
    box-shadow:0 0 0 2px rgba(78,115,223,0.1);
}

/* ===== BUTTON ===== */
.btn-primary{
    background:linear-gradient(135deg,#4e73df,#224abe);
    border:none;
    border-radius:10px;
    font-weight:500;
    height:45px;
}

.btn-dark{
    border-radius:10px;
}

/* ===== KPI ===== */
.kpi{
    font-size:24px;
    font-weight:700;
    color:#4e73df;
}

.small{
    font-size:12px;
    color:#888;
}

/* ===== TABLE ===== */
.table-modern{
    width:100%;
    border-collapse:separate;
    border-spacing:0 10px;
}

.table-modern tr{
    background:#fff;
    box-shadow:0 2px 6px rgba(0,0,0,0.04);
}

.table-modern td,
.table-modern th{
    padding:10px;
    font-size:13px;
}

.table-modern tr:hover{
    background:#f9fbff;
}

/* ===== BADGES ===== */
.badge-success{
    background:#e6f7ee;
    color:#1cc88a;
    padding:6px 12px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
}

.badge-danger{
    background:#fdeaea;
    color:#e74a3b;
    padding:6px 12px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
}

.badge-warning{
    background:#fff7e6;
    color:#f6a609;
    padding:6px 12px;
    border-radius:8px;
    font-size:12px;
    font-weight:600;
}

/* ===== SHIPPING DETAILS ===== */
.shipping-box p{
    margin-bottom:10px;
    font-size:14px;
}

.shipping-box strong{
    color:#111827;
    min-width:140px;
    display:inline-block;
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
table {
    border-collapse: collapse !important;
}
table th {
    font-weight: 600;
    color: #fff;
    background: #004392 !important;
    border: 1px solid #fff;
}
table tbody td {
    border: 1px solid #dee2e6;
}
.btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }

</style>

<div class="container-fluid py-3">
<div class="d-flex top-back-heading-sec mb-3">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h4 class="fw-bold">
    Shipping & Delivery Settings
    </h4>
</div>

{{-- SUCCESS MESSAGE --}}
@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="row g-3">

<!-- LEFT -->
<div class="col-lg-4">

<div class="cardx">

<div class="section-title">
Shipping Rules
</div>

<div class="d-flex gap-3 mb-3">

<label>
<input type="radio"
       name="shipping_type"
       value="free">
 Free
</label>

<label>
<input type="radio"
       name="shipping_type"
       value="paid">
 Paid
</label>

</div>

<input type="number"
       id="threshold"
       class="form-control mt-2"
       placeholder="Free Shipping Threshold ₹">

<hr>

<div class="section-title">
Auto Zone Detection
</div>

<input type="text"
       id="pin_code"
       class="form-control mb-2"
       placeholder="Enter Buyer PIN">

<input type="text"
       id="zone"
       class="form-control mb-2"
       readonly
       placeholder="Zone">

<input type="text"
       id="cost"
       class="form-control mb-2"
       readonly
       placeholder="Shipping Cost">

<input type="text"
       id="eta"
       class="form-control"
       readonly
       placeholder="Estimated Delivery">

</div>

</div>

<!-- CENTER -->
<div class="col-lg-5">

<div class="cardx">

<div class="section-title">
Courier Integration
</div>

<select id="shipping_mode"
        class="form-control mb-2">

<option value="manual">
Manual
</option>

<option value="shiprocket">
Shiprocket API
</option>

</select>

<button onclick="checkShipping()"
        class="btn btn-primary w-100 mb-3">

Check Shipping

</button>

<div id="courier_list"></div>

<hr>

<div class="section-title">
Manual Tracking
</div>

<input type="text"
       class="form-control mb-2"
       placeholder="Courier Name">

<input type="text"
       class="form-control"
       placeholder="Tracking ID">

</div>

</div>

<!-- RIGHT -->
<div class="col-lg-3">

<div class="cardx text-center">

<div class="section-title">
SLA Metrics
</div>

<div class="kpi">
{{ $onTimeRate }}%
</div>

<div class="small">
On-Time
</div>

<hr>

<div class="kpi text-danger">
{{ $delayed }}
</div>

<div class="small">
Delayed
</div>

<hr>

<div class="kpi text-warning">
{{ $breached }}
</div>

<div class="small">
Breached
</div>

</div>

</div>

</div>

{{-- ================= ORDER SHIPPING SECTION ================= --}}
<div class="row mt-4">

<div class="col-12">

@if($order)
<div class="cardx shipping-box">

<div class="d-flex justify-content-between align-items-center mb-3">

<h5 class="fw-bold mb-0">
Order Shipping Details
</h5>

@if(($order->delivery_status ?? '') == 'delivered')

<span class="badge-success">
Delivered
</span>

@elseif(($order->delivery_status ?? '') == 'shipped')

<span class="badge-warning">
Shipped
</span>

@else

<span class="badge-danger">
Pending
</span>

@endif

</div>

<div class="row">

<div class="col-md-6">

<p>
<strong>Shipping Type :</strong>
{{ $order->shipping_type ?? 'Free/Paid' }}
</p>

<p>
<strong>Shipping Zone :</strong>
{{ $order->shipping_zone ?? '-' }}
</p>

<p>
<strong>Courier Name :</strong>
{{ $order->courier_name ?? '-' }}
</p>

</div>

<div class="col-md-6">

<p>
<strong>Tracking ID :</strong>
{{ $order->tracking_id ?? '-' }}
</p>

<p>
<strong>Estimated Delivery :</strong>
{{ $order->estimated_delivery ?? '-' }}
</p>

<p>
<strong>Delivery Status :</strong>

@if(($order->delivery_status ?? '') == 'delivered')

<span class="badge-success">
Delivered
</span>

@elseif(($order->delivery_status ?? '') == 'shipped')

<span class="badge-warning">
Shipped
</span>

@else

<span class="badge-danger">
Pending
</span>

@endif

</p>

</div>

</div>

<hr>

<form method="POST"
      action="{{ route('seller.orders.update-tracking', $order->id) }}">

@csrf

<div class="row g-2">

<div class="col-md-4">

<input type="text"
       name="courier_name"
       class="form-control"
       value="{{ $order->courier_name ?? '' }}"
       placeholder="Courier Name">

</div>

<div class="col-md-3">

<input type="text"
       name="tracking_id"
       class="form-control"
       value="{{ $order->tracking_id ?? '' }}"
       placeholder="Enter Tracking ID">

</div>

<div class="col-md-3">

<select name="delivery_status"
        class="form-control">

<option value="pending"
{{ ($order->delivery_status ?? '') == 'pending' ? 'selected' : '' }}>
Pending
</option>

<option value="shipped"
{{ ($order->delivery_status ?? '') == 'shipped' ? 'selected' : '' }}>
Shipped
</option>

<option value="out_for_delivery"
{{ ($order->delivery_status ?? '') == 'out_for_delivery' ? 'selected' : '' }}>
Out For Delivery
</option>

<option value="delivered"
{{ ($order->delivery_status ?? '') == 'delivered' ? 'selected' : '' }}>
Delivered
</option>

</select>

</div>

<div class="col-md-2">

<button class="btn btn-primary w-100">
Update
</button>

</div>

</div>

</form>

</div>
@else
<div class="cardx shipping-box p-4 text-center">
    <h6 class="text-muted mb-0">No recent orders available to update tracking.</h6>
</div>
@endif

</div>

</div>

<!-- BOTTOM TABLES -->
<div class="row mt-4">

<div class="col-lg-6">

<div class="cardx">

<div class="section-title">
Activity Log
</div>

<div class="table-responsive">

<table class="table-modern">

<tr>
<th>Order</th>
<th>Action</th>
<th>Status</th>
<th>Time</th>
</tr>

@foreach($logs as $log)

<tr>

<td>
#{{ $log->order_id }}
</td>

<td>
{{ $log->activity_type }}
</td>

<td>

<span class="{{ $log->new_status == 'completed' ? 'badge-success' : 'badge-danger' }}">

{{ $log->new_status }}

</span>

</td>

<td>
{{ $log->created_at }}
</td>

</tr>

@endforeach

</table>

</div>

</div>

</div>

<div class="col-lg-6">

<div class="cardx">

<div class="section-title">
Payment Attempts
</div>

<div class="table-responsive">

<table class="table-modern">

<tr>
<th>Date</th>
<th>Method</th>
<th>Status</th>
</tr>

@foreach($payments as $p)

<tr>

<td>
{{ $p->created_at }}
</td>

<td>
{{ $p->payment_method }}
</td>

<td>

<span class="{{ $p->payment_status == 'paid' ? 'badge-success' : 'badge-danger' }}">

{{ $p->payment_status }}

</span>

</td>

</tr>

@endforeach

</table>

</div>

</div>

</div>

</div>

</div>

<script>

$('#pin_code').on('keyup', function(){

let pin = $(this).val();

if(pin.length === 6){

$.post("/seller/check-shipping-live",{

pincode:pin,
mode:'manual',
_token:$('meta[name="csrf-token"]').attr('content')

}, function(res){

if(res.status){

$('#zone').val(res.zone);
$('#cost').val("₹"+res.cost);
$('#eta').val(res.eta+" Days");

}else{

$('#zone').val("Not Serviceable");
$('#cost').val("");
$('#eta').val("");

}

});

}

});

function checkShipping(){

let pin = $('#pin_code').val();
let mode = $('#shipping_mode').val();

$.post("/seller/check-shipping-live",{

pincode:pin,
mode:mode,
_token:$('meta[name="csrf-token"]').attr('content')

}, function(res){

if(res.type === 'api'){

let html = `
<table class="table-modern">

<tr>
<th>Courier</th>
<th>Cost</th>
<th>ETA</th>
</tr>`;

$.each(res.couriers,function(i,c){

html += `
<tr>

<td>${c.courier_name}</td>

<td>₹${c.rate}</td>

<td>${c.estimated_delivery_days} days</td>

</tr>`;

});

html += "</table>";

$('#courier_list').html(html);

}

});

}

</script>

@endsection