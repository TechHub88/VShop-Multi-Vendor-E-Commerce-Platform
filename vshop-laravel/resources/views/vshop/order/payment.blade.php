@extends('vshop.main')

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
                            <li><a href="{{ route('home') }}">Home</a> / </li>
                            <li><a href="{{ route('vshop.cart.index') }}">Wishlist - Cart</a> /</li>
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
                <form id="payment-form" action="{{ route('vshop.order.place') }}" method="POST">
                @csrf
                
                @if(isset($isBuyNow) && $isBuyNow)
    <input type="hidden" name="is_buy_now" value="1">
    <input type="hidden" name="product_id" value="{{ request('product_id') }}">
    <input type="hidden" name="quantity" value="{{ request('quantity') }}">
    <input type="hidden" name="price" value="{{ request('price') }}">
@endif

                <div class="address-inner">
                    <div class="address-wrapper">

                        {{-- LEFT SIDE PAYMENT METHODS --}}
                        <div class="address-left payment-left">
                            <div class="address-left-inner">

                                @php
                                    $couponCode = $orderSummary['coupon_code'] ?? null;
                                    $couponDiscount = $orderSummary['coupon_discount'] ?? 0;
                                    $appliedPoints = $orderSummary['reward_points'] ?? 0;
                                    $rewardDiscount = $orderSummary['reward_discount'] ?? 0;
                                @endphp

                                 <div class="address-left-header border0 pb-0" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 10px;">
                                     <h2 style="margin: 0;">Choose Payment Method</h2>
                                     <div class="d-flex align-items-center gap-2 flex-wrap">
                                         {{-- Reward Points Group --}}
                                         <div id="rewards_promo_group" class="{{ $rewardDiscount > 0 ? 'd-none' : '' }}">
                                             <button type="button" id="open_rewards_btn" class="btn btn-warning" style="color: #fff; background-color: #f39c12; border-color: #f39c12; font-weight: 600; font-size: 10px; padding: 3px 8px; border-radius: 4px; line-height: 1.2;">
                                                 <i class="fa-solid fa-star me-1" style="font-size: 9px;"></i> Apply Rewards
                                             </button>
                                         </div>
                                         <div id="applied_rewards_box" class="bg-light border rounded d-flex align-items-center gap-2 {{ $rewardDiscount > 0 ? '' : 'd-none' }}" style="padding: 3px 6px; border-radius: 4px;">
                                             <i class="fa-solid fa-circle-check text-success" style="font-size: 11px;"></i>
                                             <span class="badge bg-warning text-dark" id="applied_rewards_points" style="font-size: 10px; padding: 2px 5px;">{{ $appliedPoints }} Points</span>
                                             <span class="text-success" style="font-size: 10px; font-weight: 600;">Applied!</span>
                                             <button type="button" id="remove_rewards_btn" class="btn btn-sm btn-link text-danger p-0 ms-1" style="font-size: 10px; text-decoration: none; font-weight: 600;">Remove</button>
                                         </div>

                                         {{-- Coupon Group --}}
                                         <div id="coupon_promo_group" class="{{ $couponDiscount > 0 ? 'd-none' : '' }}">
                                             <button type="button" id="open_coupon_btn" class="btn btn-primary" style="color: #fff; background-color: #004392; border-color: #004392; font-weight: 600; font-size: 10px; padding: 3px 8px; border-radius: 4px; line-height: 1.2;">
                                                 <i class="fa-solid fa-tag me-1" style="font-size: 9px;"></i> Apply Coupon
                                             </button>
                                         </div>
                                         <div id="applied_coupon_box" class="bg-light border rounded d-flex align-items-center gap-2 {{ $couponDiscount > 0 ? '' : 'd-none' }}" style="padding: 3px 6px; border-radius: 4px;">
                                             <i class="fa-solid fa-circle-check text-success" style="font-size: 11px;"></i>
                                             <span class="badge bg-primary" id="applied_code" style="font-size: 10px; background-color: #004392 !important; padding: 2px 5px;">{{ $couponCode }}</span>
                                             <span class="text-success" style="font-size: 10px; font-weight: 600;" id="applied_msg">Applied!</span>
                                             <button type="button" id="remove_coupon_btn" class="btn btn-sm btn-link text-danger p-0 ms-2" style="font-size: 11px; text-decoration: none; font-weight: 600;">Remove</button>
                                         </div>

                                         {{-- Referral Code Group --}}
                                         @php
                                             $sessionReferral = session('referral_code') ?? request()->cookie('referral_code');
                                             $hasReferrer = !empty(auth('customer')->user()->referred_by) || !empty($sessionReferral);
                                             $displayRefCode = !empty(auth('customer')->user()->referred_by) 
                                                 ? (\App\Models\Customer::find(auth('customer')->user()->referred_by)->referral_code ?? 'Applied') 
                                                 : $sessionReferral;
                                         @endphp
                                         <input type="hidden" name="referral_code" id="hidden_referral_code" value="{{ $displayRefCode }}">

                                         <div id="referral_promo_group" class="{{ $hasReferrer ? 'd-none' : '' }}">
                                             <button type="button" id="open_referral_btn" class="btn btn-info" style="color: #fff; background-color: #7E22CE; border-color: #7E22CE; font-weight: 600; font-size: 10px; padding: 3px 8px; border-radius: 4px; line-height: 1.2;">
                                                 <i class="fa-solid fa-gift me-1" style="font-size: 9px;"></i> Apply Referral
                                             </button>
                                         </div>
                                         <div id="applied_referral_box" class="bg-light border rounded d-flex align-items-center gap-2 {{ $hasReferrer ? '' : 'd-none' }}" style="padding: 3px 6px; border-radius: 4px;">
                                             <i class="fa-solid fa-circle-check text-success" style="font-size: 11px;"></i>
                                             <span class="badge bg-purple" id="applied_referral_code" style="font-size: 10px; background-color: #7E22CE !important; padding: 2px 5px; color: #fff;">{{ $displayRefCode }}</span>
                                             <span class="text-success" style="font-size: 10px; font-weight: 600;">Applied!</span>
                                             @if(empty(auth('customer')->user()->referred_by))
                                             <button type="button" id="remove_referral_btn" class="btn btn-sm btn-link text-danger p-0 ms-2" style="font-size: 11px; text-decoration: none; font-weight: 600;">Remove</button>
                                             @endif
                                         </div>
                                     </div>
                                 </div>

                                <div class="address-left-bottom payment-table" style="margin-top: 20px;">
                                    <table>
                                        <thead class="payment-table-top">
                                            <tr>
                                                <th>Select</th>
                                                <th>Payment Mode</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            {{-- Wallet Checkbox --}}
                                            <tr class="payment-row" id="wallet-row">
                                                <td>
                                                    <label class="checkbox-container">
                                                        <input type="checkbox" id="use_wallet" name="use_wallet" value="1" {{ $walletBalance <= 0 ? 'disabled' : '' }}>
                                                        <span class="checkbox-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td class="payment-title">
                                                    Use Wallet Balance
                                                    <span class="d-block text-muted small" style="font-size: 11px; margin-top: 4px;">
                                                        Available Balance: ₹{{ number_format($walletBalance, 2) }}
                                                        @if($walletBalance <= 0)
                                                            <span class="text-danger fw-bold ms-2">(No Balance Available)</span>
                                                        @endif
                                                    </span>
                                                </td>
                                                <td class="payment-icon text-center">
                                                    <i class="fa-solid fa-wallet text-warning" style="font-size: 26px; transition: 0.2s ease;"></i>
                                                </td>
                                            </tr>

                                            {{-- UPI --}}
                                            <tr class="payment-row" id="upi-row">
                                                <td>
                                                    <label class="checkbox-container">
                                                        <input type="checkbox" name="payment_method[]" value="upi" checked>
                                                        <span class="checkbox-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td class="payment-title">UPI / Online Pay</td>
                                                <td class="payment-icon text-center">
                                                    <img src="{{ asset('images/upi-img.png') }}" alt="UPI">
                                                </td>
                                            </tr>

                                            {{-- CARD --}}
                                            <tr class="payment-row" id="card-row">
                                                <td>
                                                    <label class="checkbox-container">
                                                        <input type="checkbox" name="payment_method[]" value="card">
                                                        <span class="checkbox-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td class="payment-title">Card / PayPal</td>
                                                <td class="payment-icon text-center">
                                                    <img src="{{ asset('images/paypal-img.png') }}" alt="Paypal">
                                                </td>
                                            </tr>

                                            {{-- COD --}}
                                            <tr class="payment-row" id="cod-row">
                                                <td>
                                                    <label class="checkbox-container">
                                                        <input type="checkbox" name="payment_method[]" value="cod">
                                                        <span class="checkbox-checkmark"></span>
                                                    </label>
                                                </td>
                                                <td class="payment-title">Cash on Delivery</td>
                                                <td class="payment-icon text-center"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>

                        {{-- ===== RIGHT SIDE ORDER SUMMARY ===== --}}
<div class="address-right">
    <div class="address-right-top">
        <h3>Order Summary</h3>

        {{-- ================= ITEMS ================= --}}
        @forelse($orderSummary['items'] as $item)
            <div class="address-right-box">
                <h4>{{ $loop->iteration }}. {{ $item['name'] }}</h4>
                <ul>
                    <li>
                        <p>Price</p>
                        <span>₹ {{ number_format($item['price'],2) }}</span>
                    </li>
                    <li>
                        <p>Quantity</p>
                        <span>{{ $item['quantity'] ?? 1 }}</span>
                    </li>
                    <li>
                        <p>Subtotal</p>
                        <span>
                            ₹ {{ number_format(($item['price'] * ($item['quantity'] ?? 1)),2) }}
                        </span>
                    </li>
                </ul>
            </div>
        @empty
            <div class="address-right-box">
                <p>No items found</p>
            </div>
        @endforelse

        {{-- ================= TAX & SUMMARY ================= --}}
        <div class="address-right-box">
            <ul>
                {{-- Subtotal --}}
                <li>
                    <p>Subtotal</p>
                    <span id="subtotal">₹ {{ number_format($orderSummary['subtotal'] ?? 0,2) }}</span>
                </li>

                {{-- CGST --}}
                <li>
                    <p>CGST</p>
                    <span id="cgst">₹ {{ number_format($orderSummary['cgst'] ?? 0,2) }}</span>
                </li>

                {{-- SGST --}}
                <li>
                    <p>SGST</p>
                    <span id="sgst">₹ {{ number_format($orderSummary['sgst'] ?? 0,2) }}</span>
                </li>

                {{-- IGST --}}
                <li>
                    <p>IGST</p>
                    <span id="igst">₹ {{ number_format($orderSummary['igst'] ?? 0,2) }}</span>
                </li>

                {{-- Courier Charges Removed --}}

                {{-- Coupon Discount --}}
                @php
                    $couponCode = $orderSummary['coupon_code'] ?? null;
                    $couponDiscount = $orderSummary['coupon_discount'] ?? 0;
                @endphp
                 <li id="coupon_discount_row" class="{{ $couponDiscount > 0 ? '' : 'd-none' }}">
                     <p class="text-success" style="font-weight: 600;">Coupon Discount <span id="coupon_label_code">{{ $couponCode ? '('.$couponCode.')' : '' }}</span></p>
                     <span class="text-success" id="coupon_discount" style="font-weight: 600;">- ₹ {{ number_format($couponDiscount, 2) }}</span>
                 </li>

                 {{-- Referral Discount --}}
                 @php
                     $referralDiscount = $orderSummary['referral_discount'] ?? 0;
                 @endphp
                 <li id="referral_discount_row" class="{{ $referralDiscount > 0 ? '' : 'd-none' }}">
                     <p class="text-success" style="font-weight: 600;">Referral Discount</p>
                     <span class="text-success" id="referral_discount" style="font-weight: 600;">- ₹ {{ number_format($referralDiscount, 2) }}</span>
                 </li>

                 <li id="rewards_discount_row" class="{{ $rewardDiscount > 0 ? '' : 'd-none' }}">
                     <p class="text-success" style="font-weight: 600;">Rewards Discount <span id="rewards_label_code">{{ $appliedPoints ? '('.$appliedPoints.' Pts)' : '' }}</span></p>
                     <span class="text-success" id="rewards_discount" style="font-weight: 600;">- ₹ {{ number_format($rewardDiscount, 2) }}</span>
                 </li>
                 <li id="wallet_deduction_row" class="d-none">
                     <p class="text-success" style="font-weight: 600;">Wallet Deduction</p>
                     <span class="text-success" id="wallet_deduction" style="font-weight: 600;">- ₹ 0.00</span>
                 </li>
                 <li id="wallet_balance_row" class="{{ $walletBalance > 0 ? '' : 'd-none' }}">
                     <p class="text-primary" style="font-weight:600;">Wallet Balance <span id="wallet_balance" style="font-weight:600;">₹ {{ number_format($walletBalance, 2) }}</span></p>
                 </li>
            </ul>
        </div>

        {{-- ================= TOTAL ================= --}}
        <div class="address-right-box border0">
            <ul>
                <li>
                    <p>Total</p>
                    <span class="total-text blueColor" id="grand_total">
                        ₹ {{ number_format($orderSummary['grand_total'] ?? 0,2) }}
                    </span>
                </li>
            </ul>
        </div>
    </div>

    {{-- ===== BUTTONS ===== --}}
    <div class="address-right-bottom">

        <button type="submit" class="add-tocart-btn place-btn w-100">
            Proceed To Checkout
        </button>

        <button type="button" class="shopping-btn place-btn w-100"
            onclick="window.location.href='{{ route('vshop.cart.index') }}'">
            Continue Shopping
        </button>

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
.payment-row:hover .payment-icon img,
.payment-row:hover .payment-icon i {
    transform: scale(1.08);
    opacity: 1;
}

/* Selected row highlight */
.payment-row input:checked + .filter-checkmark,
.payment-row:has(input:checked) {
    background-color: #f3f7ff;
}

/* Custom checkbox container */
.checkbox-container {
    display: inline-block;
    position: relative;
    padding-left: 25px;
    cursor: pointer;
    font-size: 14px;
    user-select: none;
    line-height: 25px;
    height: 25px;
}
.checkbox-container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}
.checkbox-checkmark {
    position: absolute;
    top: 2px;
    left: 0;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border: 2px solid #ccc;
    border-radius: 4px;
    transition: all 0.2s ease;
}
.checkbox-container:hover input ~ .checkbox-checkmark {
    border-color: #004392;
}
.checkbox-container input:checked ~ .checkbox-checkmark {
    background-color: #004392;
    border-color: #004392;
}
.checkbox-checkmark:after {
    content: "";
    position: absolute;
    display: none;
}
.checkbox-container input:checked ~ .checkbox-checkmark:after {
    display: block;
}
.checkbox-container .checkbox-checkmark:after {
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
</style>

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // CSRF token setup
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // Global tracking of values
    let appliedCouponDiscount = parseFloat("{{ $couponDiscount }}") || 0;
    let appliedRewardsDiscount = parseFloat("{{ $rewardDiscount }}") || 0;
    let walletBalance = parseFloat("{{ $walletBalance }}") || 0;

    // Recalculate total dynamically in JS
    function recalculateGrandTotal() {
        let subtotal = parseFloat("{{ $orderSummary['subtotal'] }}");
        let cgst = parseFloat("{{ $orderSummary['cgst'] }}");
        let sgst = parseFloat("{{ $orderSummary['sgst'] }}");
        let igst = parseFloat("{{ $orderSummary['igst'] }}");

        let baseTotal = subtotal + cgst + sgst + igst - appliedCouponDiscount - appliedRewardsDiscount;
        if (baseTotal < 0) baseTotal = 0;

        let walletDeduction = 0;
        let finalTotal = baseTotal;

        if ($('#use_wallet').is(':checked')) {
            walletDeduction = Math.min(walletBalance, baseTotal);
            finalTotal = baseTotal - walletDeduction;

            // Show deduction row in summary
            $('#wallet_deduction').text('- ₹ ' + walletDeduction.toFixed(2));
            $('#wallet_deduction_row').removeClass('d-none');
        } else {
            $('#wallet_deduction_row').addClass('d-none');
        }

        $('#grand_total').text('₹ ' + finalTotal.toFixed(2).replace(/\d(?=(\d{3})+\.)/g, '$&,'));

        // Handle other payment methods availability
        if (finalTotal === 0 && $('#use_wallet').is(':checked')) {
            // Paid fully by wallet - disable radio options
            $('input[name="payment_method[]"]').prop('disabled', true).prop('checked', false);
            $('#upi-row, #card-row, #cod-row').addClass('opacity-50');
        } else {
            $('input[name="payment_method[]"]').prop('disabled', false);
            $('#upi-row, #card-row, #cod-row').removeClass('opacity-50');
            // Ensure one of them is checked if none is currently selected
            if ($('input[name="payment_method[]"]:checked').length === 0) {
                $('input[value="upi"]').prop('checked', true);
            }
        }
    }

    // Toggle checkboxes/radios when payment rows are clicked
    $(document).on('click', '.payment-row', function(e) {
        if ($(e.target).is('input, span, label')) {
            return; // Let browser do natural action
        }
        let radio = $(this).find('input[type="radio"]');
        if (radio.length && !radio.prop('disabled')) {
            radio.prop('checked', true).trigger('change');
        }
        let checkbox = $(this).find('input[type="checkbox"]');
        if (checkbox.length && !checkbox.prop('disabled')) {
            checkbox.prop('checked', !checkbox.is(':checked')).trigger('change');
        }
    });

    // Event listener for checkbox & radio changes
    $(document).on('change', '#use_wallet, input[name="payment_method[]"]', function() {
        recalculateGrandTotal();
    });

    // Helper: apply coupon process
    function runCouponApply(couponCode) {
        let subtotalVal = "{{ $orderSummary['subtotal'] ?? 0 }}";

        if (!couponCode) {
            Swal.fire({
                icon: 'warning',
                title: 'Required',
                text: 'Please enter a coupon code',
                confirmButtonColor: '#004392'
            });
            return;
        }

        // Show loading state in Swal (optional, but UI indicates it)
        Swal.showLoading();

        $.ajax({
            url: "{{ route('vshop.coupon.apply') }}",
            type: "POST",
            data: {
                coupon_code: couponCode,
                subtotal: subtotalVal
            },
            success: function(res) {
                if (res.success) {
                    // Hide the Modal
                    Swal.close();
                    
                    // Show applied coupon box
                    $('#applied_code').text(res.coupon_code);
                    $('#applied_msg').text('Applied successfully!');
                    $('#applied_coupon_box').removeClass('d-none').addClass('d-flex');
                    $('#coupon_promo_group').addClass('d-none');

                    // Update summary calculations
                    $('#coupon_label_code').text('(' + res.coupon_code + ')');
                    $('#coupon_discount').text('- ₹ ' + res.discount);
                    $('#coupon_discount_row').removeClass('d-none');

                    appliedCouponDiscount = parseFloat(res.discount) || 0;

                    recalculateGrandTotal();

                    // SweetAlert Success Popup
                    Swal.fire({
                        icon: 'success',
                        title: 'Coupon Applied!',
                        text: 'You saved ₹ ' + res.discount + ' with coupon ' + res.coupon_code,
                        confirmButtonColor: '#004392'
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Coupon',
                        text: res.message,
                        confirmButtonColor: '#004392'
                    });
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#004392'
                });
            }
        });
    }

    // Open Coupon SweetAlert
    $(document).on('click', '#open_coupon_btn', function(e) {
        e.preventDefault();
        
        let availableCoupons = @json($availableCoupons);
        let couponsHtml = `
            <div class="swal-coupon-container" style="text-align: left; font-family: 'Inter', sans-serif;">
                <!-- Manual Input -->
                <div class="swal-input-group" style="display: flex; margin-bottom: 20px; border: 1px solid #ced4da; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <span style="display: flex; align-items: center; padding: 0 15px; background: #fff; color: #6c757d;">
                        <i class="fa-solid fa-keyboard"></i>
                    </span>
                    <input type="text" id="swal_coupon_code" class="form-control" placeholder="Type Coupon Code" style="border: none; text-transform: uppercase; font-weight: 700; font-size: 14px; height: 48px; flex: 1; outline: none; padding-left: 5px; box-shadow: none;">
                    <button id="swal_apply_btn" style="background-color: #004392; color: #fff; border: none; font-weight: 700; padding: 0 25px; font-size: 14px; cursor: pointer; transition: background 0.2s;">APPLY</button>
                </div>
                
                <h6 style="font-weight: 700; color: #555; font-size: 13px; margin-bottom: 15px; display: flex; align-items: center; gap: 6px;">
                    <i class="fa-solid fa-tags" style="color: #004392;"></i>
                    Available Coupons
                </h6>
                
                <div class="swal-coupons-list" style="max-height: 300px; overflow-y: auto; padding-right: 5px; margin-right: -10px;">
        `;
        
        if (availableCoupons.length === 0) {
            couponsHtml += `<p style="color: #888; font-size: 13px; text-align: center; padding: 20px 0;">No coupons available right now.</p>`;
        } else {
            availableCoupons.forEach(function(coupon) {
                let amountStr = coupon.discount_type === 'percentage' ? coupon.coupon_amount + '%' : '₹ ' + parseFloat(coupon.coupon_amount).toFixed(2);
                let desc = coupon.discount_type === 'percentage' 
                    ? `Get <strong>${coupon.coupon_amount}% OFF</strong> on your order.` 
                    : `Get <strong>₹ ${parseFloat(coupon.coupon_amount).toFixed(2)} OFF</strong> on your order.`;
                let minSpend = parseFloat(coupon.minimum_spend).toFixed(2);
                
                couponsHtml += `
                    <div class="coupon-card" style="border: 1px dashed #004392; border-radius: 8px; background-color: #f7faff; padding: 12px 15px; margin-bottom: 12px; display: flex; justify-content: space-between; align-items: center; position: relative;">
                        <div style="flex: 1; padding-right: 15px;">
                            <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 6px;">
                                <span class="coupon-code-badge" style="font-size: 12px; font-weight: 800; color: #004392; background: #eef5ff; border: 1px dashed #004392; padding: 3px 8px; border-radius: 4px; letter-spacing: 0.5px; text-transform: uppercase;">
                                    ${coupon.discount_code}
                                </span>
                            </div>
                            <h6 style="margin: 0 0 4px 0; font-size: 13px; font-weight: 700; color: #333;">${coupon.name || 'Special Discount'}</h6>
                            <p style="margin: 0; font-size: 11px; color: #666; line-height: 1.4;">
                                ${desc}<br>
                                <span style="color: #888;">Min purchase of ₹ ${minSpend} required.</span>
                            </p>
                        </div>
                        <div>
                            <button class="swal-apply-coupon-btn" data-code="${coupon.discount_code}" style="background-color: #004392; color: #fff; font-weight: 700; font-size: 12px; padding: 6px 14px; border-radius: 4px; border: none; cursor: pointer; box-shadow: 0 2px 4px rgba(0,67,146,0.2);">
                                APPLY
                            </button>
                        </div>
                    </div>
                `;
            });
        }
        
        couponsHtml += `
                </div>
            </div>
        `;
        
        Swal.fire({
            title: 'Apply Coupon',
            html: couponsHtml,
            showConfirmButton: false,
            showCloseButton: true,
            width: '500px',
            padding: '20px',
            customClass: {
                title: 'text-primary font-weight-bold',
                closeButton: 'text-dark'
            },
            didOpen: () => {
                $('#swal_apply_btn').on('click', function() {
                    let code = $('#swal_coupon_code').val().trim();
                    if (code) {
                        runCouponApply(code);
                    } else {
                        Swal.showValidationMessage('Please enter a coupon code');
                    }
                });
                
                $('.swal-apply-coupon-btn').on('click', function() {
                    let code = $(this).data('code');
                    runCouponApply(code);
                });
            }
        });
    });

    // Remove Coupon
    $(document).on('click', '#remove_coupon_btn', function(e) {
        e.preventDefault();

        $.ajax({
            url: "{{ route('coupon.remove') }}",
            type: "POST",
            success: function(res) {
                if (res.success) {
                    // Reset inputs & boxes
                    $('#applied_coupon_box').addClass('d-none').removeClass('d-flex');
                    $('#coupon_promo_group').removeClass('d-none');

                    // Hide discount row
                    $('#coupon_discount_row').addClass('d-none');
                    $('#coupon_discount').text('- ₹ 0.00');

                    appliedCouponDiscount = 0;

                    recalculateGrandTotal();

                    // SweetAlert Remove Success Popup
                    Swal.fire({
                        icon: 'success',
                        title: 'Coupon Removed',
                        text: 'Coupon discount has been removed.',
                        confirmButtonColor: '#004392',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to remove coupon.',
                    confirmButtonColor: '#004392'
                });
            }
        });
    });

    // Open Referral Code SweetAlert
    $(document).on('click', '#open_referral_btn', function(e) {
        e.preventDefault();
        
        let referralHtml = `
            <div class="swal-referral-container" style="text-align: left; font-family: 'Inter', sans-serif;">
                <div class="swal-input-group" style="display: flex; margin-bottom: 10px; border: 1px solid #ced4da; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 10px rgba(0,0,0,0.05);">
                    <span style="display: flex; align-items: center; padding: 0 15px; background: #fff; color: #6c757d;">
                        <i class="fa-solid fa-gift"></i>
                    </span>
                    <input type="text" id="swal_referral_code" class="form-control" placeholder="Type Referral Code" style="border: none; text-transform: uppercase; font-weight: 700; font-size: 14px; height: 48px; flex: 1; outline: none; padding-left: 5px; box-shadow: none;">
                    <button id="swal_apply_ref_btn" style="background-color: #7E22CE; color: #fff; border: none; font-weight: 700; padding: 0 25px; font-size: 14px; cursor: pointer; transition: background 0.2s;">APPLY</button>
                </div>
                <p style="font-size: 11px; color: #666; margin: 0 5px;">Enter a buyer's referral code to support them.</p>
            </div>
        `;
        
        Swal.fire({
            title: 'Apply Referral Code',
            html: referralHtml,
            showConfirmButton: false,
            showCloseButton: true,
            width: '450px',
            padding: '20px',
            didOpen: () => {
                $('#swal_apply_ref_btn').on('click', function() {
                    let code = $('#swal_referral_code').val().trim();
                    if (code) {
                        runReferralApply(code);
                    } else {
                        Swal.showValidationMessage('Please enter a referral code');
                    }
                });
            }
        });
    });

    function runReferralApply(code) {
        $.ajax({
            url: "{{ route('vshop.referral.apply') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                referral_code: code
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Applied!',
                        text: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    $('#hidden_referral_code').val(code);
                    $('#applied_referral_code').text(code);
                    $('#referral_promo_group').addClass('d-none');
                    $('#applied_referral_box').removeClass('d-none').addClass('d-flex');
                } else {
                    Swal.showValidationMessage(response.message);
                }
            },
            error: function(xhr) {
                Swal.showValidationMessage('Failed to apply referral code. Please check code validity.');
            }
        });
    }

    // Remove Referral
    $(document).on('click', '#remove_referral_btn', function(e) {
        e.preventDefault();
        $.ajax({
            url: "{{ route('vshop.referral.remove') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}"
            },
            success: function(response) {
                if (response.success) {
                    $('#hidden_referral_code').val('');
                    $('#applied_referral_box').addClass('d-none').removeClass('d-flex');
                    $('#referral_promo_group').removeClass('d-none');
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Referral Removed',
                        text: 'Referral code has been removed.',
                        confirmButtonColor: '#7E22CE',
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
            }
        });
    });

    // Open Rewards SweetAlert
    $(document).on('click', '#open_rewards_btn', function(e) {
        e.preventDefault();
        
        let rewardPoints = parseInt("{{ $rewardPoints }}");
        let rewardTiers = @json($rewardTiers ?? []);
        
        let tiersHtml = '';
        if (rewardTiers.length > 0) {
            rewardTiers.forEach(function(tier) {
                tiersHtml += `
                    <div class="reward-card-option border rounded p-3 d-flex justify-content-between align-items-center bg-white" style="transition: all 0.2s;">
                        <div>
                            <strong style="color: #333; font-size: 14px; display: block; margin-bottom: 2px;">Redeem ${tier.points_required} Points</strong>
                            <span style="font-size: 12px; color: #666;">Get ${parseFloat(tier.discount_percentage).toFixed(2)}% discount on your order</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-warning select-reward-tier" data-points="${tier.points_required}" style="font-weight: 700; font-size: 11px; padding: 5px 12px; border-color: #f39c12; color: #f39c12; background: transparent; border-radius: 4px; border: 1px solid #f39c12;">REDEEM</button>
                    </div>
                `;
            });
        } else {
            tiersHtml = `<p class="text-center text-muted" style="font-size: 13px;">No reward redemption tiers configured.</p>`;
        }

        let rewardsHtml = `
            <div class="swal-reward-container" style="text-align: left; font-family: 'Inter', sans-serif;">
                <div style="text-align: center; margin-bottom: 20px;">
                    <i class="fa-solid fa-star text-warning" style="font-size: 40px; margin-bottom: 10px;"></i>
                    <h5 style="font-weight: 700; color: #333; margin-bottom: 5px;">Redeem up to 5%!</h5>
                    <span class="badge bg-secondary p-2" style="font-size: 13px; color: #fff; background-color: #6c757d !important; border-radius: 20px; padding: 5px 15px !important;">Available: <strong>${rewardPoints} Points</strong></span>
                </div>
                
                <p class="text-muted" style="font-size: 12.5px; line-height: 1.5; margin-bottom: 20px; text-align: center;">Choose a tier to redeem and get a percentage discount on your order:</p>
                
                <div class="swal-reward-list" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 20px;">
                    ${tiersHtml}
                </div>
                
            </div>
        `;
        
        Swal.fire({
            title: '',
            html: rewardsHtml,
            showConfirmButton: false,
            showCloseButton: true,
            width: '450px',
            padding: '20px',
            customClass: {
                closeButton: 'text-dark'
            },
            didOpen: () => {
                $('.select-reward-tier').on('click', function() {
                    let points = $(this).data('points');
                    if (rewardPoints >= points) {
                        Swal.close();
                        runRewardsApply(points);
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Insufficient Points',
                            text: 'You do not have enough reward points for this tier.',
                            confirmButtonColor: '#004392'
                        });
                    }
                });
            }
        });
    });

    // Helper: apply rewards process
    function runRewardsApply(selectedPoints) {
        Swal.showLoading();

        $.ajax({
            url: "{{ route('vshop.rewards.apply') }}",
            type: "POST",
            data: {
                points: selectedPoints
            },
            success: function(res) {
                if (res.success) {
                    window.location.reload();
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Insufficient Points',
                        text: res.message,
                        confirmButtonColor: '#004392'
                    });
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong. Please try again.',
                    confirmButtonColor: '#004392'
                });
            }
        });
    }

    // Remove Reward Points
    $(document).on('click', '#remove_rewards_btn', function(e) {
        e.preventDefault();
        Swal.showLoading();

        $.ajax({
            url: "{{ route('vshop.rewards.remove') }}",
            type: "POST",
            success: function(res) {
                if (res.success) {
                    window.location.reload();
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to remove reward points.',
                    confirmButtonColor: '#004392'
                });
            }
        });
    });

    // Intercept form submission for Razorpay
    $('#payment-form').on('submit', function(e) {
        let useWallet = $('#use_wallet').is(':checked');
        let paymentMethod = $('input[name="payment_method[]"]:checked').first().val();

        // Calculate final total to decide if online payment is needed
        let subtotal = parseFloat("{{ $orderSummary['subtotal'] }}");
        let cgst = parseFloat("{{ $orderSummary['cgst'] }}");
        let sgst = parseFloat("{{ $orderSummary['sgst'] }}");
        let igst = parseFloat("{{ $orderSummary['igst'] }}");
        let courier = parseFloat("{{ $orderSummary['courier_total'] }}");

        let baseTotal = subtotal + cgst + sgst + igst + courier - appliedCouponDiscount - appliedRewardsDiscount;
        if (baseTotal < 0) baseTotal = 0;
        let walletDeduction = useWallet ? Math.min(walletBalance, baseTotal) : 0;
        let finalTotal = baseTotal - walletDeduction;

        if (finalTotal === 0) {
            // Order is fully paid using wallet balance. Direct submit.
            // Append a hidden payment_method input to pass controller validation
            $('<input>').attr({
                type: 'hidden',
                name: 'payment_method[]',
                value: 'wallet'
            }).appendTo($(this));
            return true;
        }

        if (paymentMethod === 'upi' || paymentMethod === 'card') {
            e.preventDefault(); // Stop normal submission

            // Check if Razorpay fields are already populated (so we don't loop)
            if ($('#razorpay_payment_id').length > 0 && $('#razorpay_payment_id').val() !== '') {
                return true;
            }

            Swal.fire({
                title: 'Initializing Payment Gateway',
                text: 'Please wait while we initialize the checkout...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Create Razorpay Order via AJAX
            let form = $(this);
            let isBuyNow = $('input[name="is_buy_now"]').val();
            let productId = $('input[name="product_id"]').val();
            let quantity = $('input[name="quantity"]').val();
            let price = $('input[name="price"]').val();

            $.ajax({
                url: "{{ route('vshop.payment.razorpay.create-order') }}",
                type: "POST",
                data: {
                    is_buy_now: isBuyNow,
                    product_id: productId,
                    quantity: quantity,
                    price: price,
                    use_wallet: useWallet ? 1 : 0
                },
                success: function(res) {
                    if (res.success) {
                        Swal.close();

                        // Launch Razorpay Checkout
                        let options = {
                            "key": res.key,
                            "amount": res.amount,
                            "currency": "INR",
                            "name": "{{ config('app.name', 'VShop') }}",
                            "description": "Order Payment",
                            "order_id": res.order_id,
                            "handler": function (response) {
                                // Payment successful
                                // Append inputs and submit
                                $('<input>').attr({
                                    type: 'hidden',
                                    id: 'razorpay_payment_id',
                                    name: 'razorpay_payment_id',
                                    value: response.razorpay_payment_id
                                }).appendTo(form);

                                $('<input>').attr({
                                    type: 'hidden',
                                    id: 'razorpay_order_id',
                                    name: 'razorpay_order_id',
                                    value: response.razorpay_order_id
                                }).appendTo(form);

                                $('<input>').attr({
                                    type: 'hidden',
                                    id: 'razorpay_signature',
                                    name: 'razorpay_signature',
                                    value: response.razorpay_signature
                                }).appendTo(form);

                                // Submit form
                                form.off('submit').submit();
                            },
                            "prefill": {
                                "name": "{{ Auth::guard('customer')->user()->name ?? '' }}",
                                "email": "{{ Auth::guard('customer')->user()->email ?? '' }}",
                                "contact": "{{ Auth::guard('customer')->user()->phone ?? '' }}"
                            },
                            "theme": {
                                "color": "#004392"
                            },
                            "modal": {
                                "ondismiss": function() {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'Payment Cancelled',
                                        text: 'You have cancelled the payment checkout.',
                                        confirmButtonColor: '#004392'
                                    });
                                }
                            }
                        };
                        let rzp1 = new Razorpay(options);
                        
                        // Handle Payment Failure
                        rzp1.on('payment.failed', function (response){
                            Swal.fire({
                                icon: 'error',
                                title: 'Payment Failed',
                                text: response.error.description || 'Your payment could not be completed.',
                                footer: 'Reason: ' + (response.error.reason || 'Unknown error from bank'),
                                confirmButtonColor: '#004392'
                            });
                        });

                        rzp1.open();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: res.message,
                            confirmButtonColor: '#004392'
                        });
                    }
                },
                error: function(xhr) {
                    console.error(xhr.responseText);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to initiate Razorpay transaction. Please try again.',
                        confirmButtonColor: '#004392'
                    });
                }
            });
        }
    });

    // Run initial wallet balance check
    recalculateGrandTotal();
});
</script>
@endpush
@endsection
