@extends('vshop.seller.main')

<style>

.nav-btn{
    border:1px solid #004392;
    color:#004392;
    background:transparent;
    border-radius:5px;
    font-size:14px;
    width:35px;
    height:38px;
}

.top-back-heading-sec{
    align-items:center;
    gap:10px;
}

/* =========================================
   LAYOUT
========================================= */

.marketplace-wrapper{
    display:flex;
    gap:24px;
    align-items:flex-start;
}

.marketplace-left{
    flex:1;
    min-width:0;
}

.marketplace-right{
    width:300px;
    min-width:300px;
    position:sticky;
    top:20px;
    align-self:flex-start;
    margin:0 !important;
    padding:0 !important;
}

.marketplace-right .profile-progress{
    margin:0 !important;
    padding:0 !important;
}

/* =========================================
   MOBILE
========================================= */

@media(max-width:991px){

    .marketplace-wrapper{
        flex-direction:column;
    }

    .marketplace-right{
        width:100%;
        min-width:100%;
        position:relative;
        top:auto;
    }
}

.btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-outline-primary {
        border-color: #084f9d !important;
        color: #084f9d !important;
    }
    .btn-outline-primary:hover {
        background: #084f9d !important;
        color: #fff !important;
    }

</style>

@section('content')

@php
$seller = auth()->guard('seller')->user();
@endphp

<div class="container-fluid py-4">

    {{-- BACK BUTTON --}}
    <div class="d-flex top-back-heading-sec mb-4">

        <button onclick="window.location.href='{{ route('seller.bank') }}'"
                class="nav-btn">

            <i class="fa-solid fa-arrow-left"></i>

        </button>

    </div>

    <div class="marketplace-wrapper">

        {{-- LEFT --}}
        <div class="marketplace-left">

            <form method="POST"
                  action="{{ route('seller.payment') }}"
                  id="paymentForm">

                @csrf

                <div class="card shadow-sm border-0">

                    <div class="card-body">

                        {{-- HEADER --}}
                        <div class="d-flex justify-content-between align-items-center mb-4">

                            <h5 class="text-primary mb-0">
                                Payment Settings
                            </h5>

                            <a href="{{ route('seller.payment.list') }}"
                               class="btn btn-outline-primary btn-sm">

                                <i class="fa fa-list me-1"></i>

                                Go to List Page

                            </a>

                        </div>

                        {{-- GATEWAY --}}
                        <div class="mb-3">

                            <label class="fw-semibold">

                                Payment Gateway

                                <span class="text-danger">*</span>

                            </label>

                            <select name="payment_gateway"
                                    id="payment_gateway"
                                    class="form-control @error('payment_gateway') is-invalid @enderror">

                                <option value="">
                                    Select Gateway
                                </option>

                                <option value="paypal"
                                    {{ old('payment_gateway',$seller->payment_gateway)=='paypal' ? 'selected' : '' }}>
                                    PayPal
                                </option>

                                <option value="razorpay"
                                    {{ old('payment_gateway',$seller->payment_gateway)=='razorpay' ? 'selected' : '' }}>
                                    Razorpay
                                </option>

                                <option value="stripe"
                                    {{ old('payment_gateway',$seller->payment_gateway)=='stripe' ? 'selected' : '' }}>
                                    Stripe
                                </option>

                            </select>

                            @error('payment_gateway')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                        </div>

                        {{-- PAYPAL --}}
                        <div id="paypalBox" class="mb-3">

                            <label class="fw-semibold">

                                PayPal Email

                                <span class="text-danger">*</span>

                            </label>

                            <input type="email"
                                   name="paypal_email"
                                   id="paypal_email"
                                   value="{{ old('paypal_email',$seller->paypal_email) }}"
                                   class="form-control @error('paypal_email') is-invalid @enderror"
                                   placeholder="Enter PayPal Email">

                            @error('paypal_email')
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                            @enderror

                            <div id="emailError"
                                 class="text-danger small mt-1">
                            </div>

                        </div>

                        {{-- API CONFIG --}}
                        <div id="apiBox" style="display:none;">

                            <hr>

                            <div class="d-flex justify-content-between align-items-center mb-2">

                                <h6 class="text-primary mb-0">
                                    API Configuration
                                </h6>

                                <span id="apiStatus"
                                      class="badge {{ ($seller->api_key && $seller->api_secret) ? 'bg-success' : 'bg-warning text-dark' }}">

                                    {{ ($seller->api_key && $seller->api_secret) ? 'Configured' : 'Pending' }}

                                </span>

                            </div>

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label>

                                        API Key

                                        <span class="text-danger">*</span>

                                    </label>

                                    <input type="text"
                                           name="api_key"
                                           value="{{ old('api_key',$seller->api_key) }}"
                                           class="form-control @error('api_key') is-invalid @enderror">

                                    @error('api_key')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label>

                                        API Secret

                                        <span class="text-danger">*</span>

                                    </label>

                                    <input type="text"
                                           name="api_secret"
                                           value="{{ old('api_secret',$seller->api_secret) }}"
                                           class="form-control @error('api_secret') is-invalid @enderror">

                                    @error('api_secret')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                </div>

                            </div>

                        </div>

                        {{-- BUTTON --}}
                        <div class="text-end mt-3">

                            <button type="submit"
                                    class="btn btn-success px-5">

                                <i class="fa fa-save me-2"></i>

                                Save Payment

                            </button>

                        </div>

                    </div>

                </div>

            </form>

        </div>

        {{-- RIGHT --}}
        <div class="marketplace-right">

            @include('vshop.seller.partials.profile-progress')

        </div>

    </div>

</div>

{{-- JS --}}
<script>

const gateway     = document.getElementById('payment_gateway');
const paypalBox   = document.getElementById('paypalBox');
const apiBox      = document.getElementById('apiBox');
const apiStatus   = document.getElementById('apiStatus');
const emailInput  = document.getElementById('paypal_email');
const emailError  = document.getElementById('emailError');

function toggleGateway(){

    let val = gateway.value;

    if(val === 'paypal'){

        paypalBox.style.display = 'block';

        apiBox.style.display = 'none';

    }
    else if(val === 'razorpay' || val === 'stripe'){

        paypalBox.style.display = 'none';

        apiBox.style.display = 'block';

    }
    else{

        paypalBox.style.display = 'none';

        apiBox.style.display = 'none';
    }
}

function updateAPIStatus(){

    let key = document.querySelector('[name="api_key"]')?.value;

    let secret = document.querySelector('[name="api_secret"]')?.value;

    if(key && secret){

        apiStatus.innerText = "Configured";

        apiStatus.className = "badge bg-success";

    }else{

        apiStatus.innerText = "Pending";

        apiStatus.className = "badge bg-warning text-dark";
    }
}

/* EMAIL VALIDATION */

function validateEmail(){

    let val = emailInput.value.trim();

    let regex = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;

    if(gateway.value === 'paypal'){

        if(!val){

            emailError.innerText = "Email is required";

            emailInput.classList.add("is-invalid");

            return false;
        }

        if(!regex.test(val)){

            emailError.innerText = "Enter valid email";

            emailInput.classList.add("is-invalid");

            return false;
        }
    }

    emailError.innerText = "";

    emailInput.classList.remove("is-invalid");

    return true;
}

/* EVENTS */

gateway.addEventListener('change', toggleGateway);

document.addEventListener('input', updateAPIStatus);

emailInput.addEventListener('input', function(){

    if(this.value.includes('@')){

        emailError.innerText = "";

        this.classList.remove("is-invalid");
    }
});

document.getElementById('paymentForm').addEventListener('submit', function(e){

    if(!validateEmail()){

        e.preventDefault();

        emailInput.focus();
    }
});

/* INIT */

toggleGateway();

updateAPIStatus();

</script>

@endsection