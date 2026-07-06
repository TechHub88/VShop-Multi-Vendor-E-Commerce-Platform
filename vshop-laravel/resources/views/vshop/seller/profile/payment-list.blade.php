@extends('vshop.seller.main')
<style>
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
.marketplace-wrapper {
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }
    .marketplace-left {
        flex: 1;
    }
    .marketplace-right {
        width: 300px;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        display: flex !important;
        align-items: center;
        padding: 9px !important;
    }
    .btn-primary i {
        font-size: 16px;
        margin-right: 8px !important;
    }
    .b
</style>

@section('content')

@php
$seller = auth()->guard('seller')->user();
@endphp

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
</div>

<div class="marketplace-wrapper">

{{-- LEFT --}}
<div class="marketplace-left">

@include('vshop.seller.partials.onboarding-header')

<div class="card shadow-sm border-0">

<div class="card-body">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-4">

<h5 class="text-primary mb-0">
Payment Gateway Details
</h5>

<a href="{{ route('seller.payment') }}"
class="btn btn-primary btn-sm">

<i class="fa fa-plus me-1"></i>
Add / Edit Payment

</a>

</div>

{{-- SUCCESS --}}
@if(session('success'))

<div class="alert alert-success" id="successMessage">
{{ session('success') }}
</div>

@endif

{{-- TABLE --}}
@if(
    $seller->paypal_email ||
    $seller->api_key
)

<div class="table-responsive">

<table class="table table-bordered align-middle">

<thead class="table-light">

<tr>

<th width="180">
Gateway
</th>

<th>
Details
</th>

<th width="140">
Status
</th>

<th width="180">
Action
</th>

</tr>

</thead>

<tbody>

{{-- ================= PAYPAL ================= --}}
@if($seller->payment_gateway == 'paypal')

<tr>

<td>
<strong>PayPal</strong>
</td>

<td>

<strong>Email:</strong>
{{ $seller->paypal_email }}

</td>

<td>

<span class="badge bg-success">
Configured
</span>

</td>

<td>

    <div class="d-flex align-items-center gap-2">

        {{-- EDIT BUTTON --}}
        <a href="{{ route('seller.payment') }}"
           class="btn btn-warning btn-sm rounded-pill px-3">

            <i class="fa fa-edit me-1"></i>
            Edit

        </a>

        {{-- ACTIVE / INACTIVE TOGGLE --}}
        <form action="{{ route('seller.payment.status') }}"
              method="POST"
              class="m-0">

            @csrf

            <input type="hidden"
                   name="status"
                   value="{{ $seller->payment_status == 1 ? 0 : 1 }}">

            <button type="submit"
                    class="btn btn-sm rounded-pill px-3
                    {{ $seller->payment_status == 1 ? 'btn-success' : 'btn-secondary' }}">

                <i class="fa {{ $seller->payment_status == 1 ? 'fa-toggle-on' : 'fa-toggle-off' }} me-1"></i>

                {{ $seller->payment_status == 1 ? 'Active' : 'Inactive' }}

            </button>

        </form>

    </div>

</td>

</tr>

@endif

{{-- ================= RAZORPAY ================= --}}
@if($seller->payment_gateway == 'razorpay')

<tr>

<td>
<strong>Razorpay</strong>
</td>

<td>

<div class="mb-1">

<strong>API Key:</strong>

{{ '******' . substr($seller->api_key, -4) }}

</div>

<div>

<strong>API Secret:</strong>

{{ '******' . substr($seller->api_secret, -4) }}

</div>

</td>

<td>

<span class="badge bg-success">
Configured
</span>

</td>

<td>

<div class="d-flex gap-2">

{{-- EDIT --}}
<a href="{{ route('seller.payment') }}"
class="btn btn-warning btn-sm">

<i class="fa fa-edit"></i>

</a>

{{-- DELETE --}}
<!--<form method="POST"-->
<!--action="{{ route('seller.payment.delete') }}"-->
<!--class="deletePaymentForm">-->

<!--@csrf-->
<!--@method('DELETE')-->

<!--<button type="submit"-->
<!--class="btn btn-danger btn-sm">-->

<!--<i class="fa fa-trash"></i>-->

<!--</button>-->

<!--</form>-->

</div>

</td>

</tr>

@endif

{{-- ================= STRIPE ================= --}}
@if($seller->payment_gateway == 'stripe')

<tr>

<td>
<strong>Stripe</strong>
</td>

<td>

<div class="mb-1">

<strong>API Key:</strong>

{{ '******' . substr($seller->api_key, -4) }}

</div>

<div>

<strong>API Secret:</strong>

{{ '******' . substr($seller->api_secret, -4) }}

</div>

</td>

<td>

<span class="badge bg-success">
Configured
</span>

</td>

<td>

<div class="d-flex gap-2">

{{-- EDIT --}}
<a href="{{ route('seller.payment') }}"
class="btn btn-warning btn-sm">

<i class="fa fa-edit"></i>

</a>

{{-- DELETE --}}
<form method="POST"
action="{{ route('seller.payment.delete') }}"
class="deletePaymentForm">

@csrf
@method('DELETE')

<button type="submit"
class="btn btn-danger btn-sm">

<i class="fa fa-trash"></i>

</button>

</form>

</div>

</td>

</tr>

@endif

</tbody>

</table>

</div>

@else

{{-- EMPTY --}}
<div class="alert alert-warning mb-0">

<i class="fa fa-exclamation-circle me-2"></i>

No payment gateway configured yet.
Please add payment details.

</div>

@endif

</div>

</div>

</div>

{{-- RIGHT --}}
<div class="marketplace-right">

<div class="profile-right">

@include('vshop.seller.partials.profile-progress')

</div>

</div>

</div>

</div>

{{-- JS --}}
<script>

// ================= AUTO HIDE SUCCESS =================
setTimeout(function(){

    let successBox = document.getElementById('successMessage');

    if(successBox){

        successBox.style.transition = "0.5s";
        successBox.style.opacity = "0";

        setTimeout(() => {

            successBox.remove();

        }, 500);
    }

}, 5000);


// ================= DOUBLE CONFIRM DELETE =================
document.querySelectorAll('.deletePaymentForm').forEach(form => {

    form.addEventListener('submit', function(e){

        e.preventDefault();

        let firstConfirm = confirm(
            'Are you sure you want to delete this payment method?'
        );

        if(firstConfirm){

            let secondConfirm = confirm(
                'This action cannot be undone. Confirm delete?'
            );

            if(secondConfirm){

                form.submit();

            }
        }

    });

});

</script>

<script>

// ================= AUTO HIDE SUCCESS =================
setTimeout(function () {

    let successBox = document.getElementById('successMessage');

    if (successBox) {

        successBox.style.transition = "all 0.5s ease";
        successBox.style.opacity = "0";
        successBox.style.transform = "translateY(-10px)";

        setTimeout(function () {

            successBox.remove();

        }, 500);

    }

}, 2500);

</script>

@endsection
