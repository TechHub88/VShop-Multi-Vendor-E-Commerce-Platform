@extends('vtravel.main')

@section('title', 'Payment Method')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">

                {{-- ================= BREADCRUMB ================= --}}
                <div class="bed-cum-sec justify-content-between">
                    <div class="bedcum-left">
                        <button class="back-btn" onclick="window.history.back()">
                            <i class="fa-solid fa-arrow-left"></i>
                        </button>
                        <ul>
                            <li><a href="{{ route('vtravelhome') }}">Home</a> / </li>
                            <li><a href="{{ route('vtravel.cart.index') }}">Wishlist - Cart</a> /</li>
                            <li>Place Order /</li>
                            <li>Payment</li>
                        </ul>
                    </div>
                </div>

                {{-- Alerts --}}
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- ================= MAIN FORM ================= --}}
                <form action="{{ route('vtravel.order.place') }}" method="POST">
                @csrf

                <div class="address-inner">
                    <div class="address-wrapper">

                        {{-- LEFT SIDE PAYMENT METHODS --}}
                        <div class="address-left payment-left">
                            <div class="address-left-inner">

                                <div class="address-left-header border0 pb-0">
                                    <div class="address-header-left">
                                        <h2>Choose Payment Method</h2>
                                    </div>
                                </div>

                                <div class="address-left-bottom payment-table">
                                    <table>
                                        <thead class="payment-table-top">
                                            <tr>
                                                <th>Select</th>
                                                <th>Payment Mode</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                       <tbody>

    {{-- UPI --}}
    <tr class="payment-row">
        <td>
            <label class="filter-radio">
                <input type="radio" name="payment_method" value="upi" checked>
                <span class="filter-checkmark"></span>
            </label>
        </td>
        <td class="payment-title">UPI / Online Pay</td>
        <td class="payment-icon">
            <img src="{{ asset('images/upi-img.png') }}" alt="UPI">
        </td>
    </tr>

    {{-- CARD --}}
    <tr class="payment-row">
        <td>
            <label class="filter-radio">
                <input type="radio" name="payment_method" value="card">
                <span class="filter-checkmark"></span>
            </label>
        </td>
        <td class="payment-title">Card / PayPal</td>
        <td class="payment-icon">
            <img src="{{ asset('images/paypal-img.png') }}" alt="Paypal">
        </td>
    </tr>

    {{-- COD --}}
    <tr class="payment-row">
        <td>
            <label class="filter-radio">
                <input type="radio" name="payment_method" value="cod">
                <span class="filter-checkmark"></span>
            </label>
        </td>
        <td class="payment-title">Cash on Delivery</td>
       
    </tr>

</tbody>

                                    </table>
                                </div>

                               
                            </div>
                        </div>

                        {{-- RIGHT SIDE ORDER SUMMARY --}}
                        <div class="address-right">
                            <div class="address-right-top">
                                <h3>Order Summary</h3>

                                @foreach($orderSummary['items'] as $item)
                                <div class="address-right-box">
                                    <h4>{{ $loop->iteration }}. {{ $item['name'] }}</h4>
                                    <ul>
                                        <li><p>Price</p><span>₹ {{ number_format($item['price'], 2) }}</span></li>
                                        <li><p>Qty</p><span>{{ $item['quantity'] }}</span></li>
                                    </ul>
                                </div>
                                @endforeach

                               <div class="address-right-box">
    <ul>

        <li>
            <p>CGST</p>
            <span>₹ {{ number_format($orderSummary['cgst'] ?? 0,2) }}</span>
        </li>

        <li>
            <p>SGST</p>
            <span>₹ {{ number_format($orderSummary['sgst'] ?? 0,2) }}</span>
        </li>

        <li>
            <p>IGST</p>
            <span>₹ {{ number_format($orderSummary['igst'] ?? 0,2) }}</span>
        </li>

        <li>
            <p>Courier</p>
            <span>₹ {{ number_format($orderSummary['courier_total'],2) }}</span>
        </li>

    </ul>
</div>
                                <div class="address-right-box border0">
                                    <ul>
                                        <li>
                                            <p>Total</p>
                                            <span class="total-text blueColor">
                                                ₹ {{ number_format($orderSummary['grand_total'], 2) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                           {{-- PLACE ORDER --}}
<div class="address-right-bottom d-flex gap-2">
    <button type="submit" class="checkout-btn primary-btn">
        Place Order
    </button>

    <a href="{{ route('vtravel.cart.index') }}" class="checkout-btn secondary-btn">
        Continue Shopping
    </a>
</div>

                        </div>

                    </div>
                </div>

                </form>

            </div>
        </div>
    </div>
</div>
<style>


/* container */
.address-right-bottom {
    display: flex;
    gap: 12px;
    margin-top: 15px;
}

/* SAME SIZE BUTTONS */
.checkout-btn {
    flex: 1;                    /* 👈 equal width */
    height: 48px;               /* 👈 equal height */
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    transition: 0.2s ease;
}

/* primary */
.primary-btn {
    background: #0d47a1;
    color: #fff;
    border: none;
}

.primary-btn:hover {
    background: #0b3d8c;
}

/* secondary */
.secondary-btn {
    background: #fff;
    color: #0d47a1;
    border: 2px solid #0d47a1;
}

.secondary-btn:hover {
    background: #f3f7ff;
}

/* mobile stack */
@media (max-width: 576px) {
    .address-right-bottom {
        flex-direction: column;
    }
}

    /* Payment table alignment */
.payment-row td {
    vertical-align: middle;
    padding: 12px 10px;
}

/* Title text */
.payment-title {
    font-weight: 500;
    color: #333;
}

/* Icon column */
.payment-icon {
    width: 80px;
    text-align: center;
}

/* SMALL CLEAN ICON */
.payment-icon img {
    max-width: 48px;   /* 👈 main size control */
    height: auto;
    object-fit: contain;
    opacity: 0.9;
    transition: 0.2s ease;
}

/* Hover effect */
.payment-row:hover .payment-icon img {
    transform: scale(1.08);
    opacity: 1;
}

/* Selected row highlight */
.payment-row input:checked + .filter-checkmark,
.payment-row:has(input:checked) {
    background: #f5f9ff;
}

</style>
@endsection
