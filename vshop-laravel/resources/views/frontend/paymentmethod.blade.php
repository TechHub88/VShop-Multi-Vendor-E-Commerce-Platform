@extends('frontend.layout')

@section('content')


<section class="bedcum-sec" style="padding-top: 130px;">
    <div class="container">
        <div class="bedcum-inner">	
            <div class="bed-cum-list">
                <ul>
                    <li><a href="#">Shop</a></li>
                    <li><i class="fa-regular fa-angle-right"></i></li>
                    <li><span>Cart</span></li>
                    <li><i class="fa-regular fa-angle-right"></i></li>
                    <li><span>Payment Method</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="address-sec payment-mode-sec" style="">
    <div class="container">
        <div class="address-inner payment-mode-secinner">
            <div class="address-wrapper">
                
                <!-- LEFT PANEL: Payment Method Selection -->
                <div class="address-left payment-left" style="flex: 1.2;">
                    <div class="h-auto address-left-inner" style="border: 0;box-shadow: none;">
                        <form action="{{ route('frontend.order.store') }}" method="POST" id="paymentForm">
                            @csrf
                            <input type="hidden" name="payment_method" id="payment_method" value="OTHER">

                            <!-- Choose Payment Mode Section -->
                            <div class="payment-mode-section">
                                <div class="payment-mode-header">
                                    <h4>Choose Payment Mode</h4>
                                </div>

                                <div class="payment-options-list">
                                    <!-- Cash On Delivery -->
                                    <div class="payment-option-item" data-payment="OTHER">
                                        <label class="payment-option-label">
                                            <input type="radio" name="payment" value="OTHER" checked>
                                            <span class="payment-radio-custom"></span>
                                            <i class="fa-solid fa-money-bill-wave payment-icon"></i>
                                            <span class="payment-name">Cash On Delivery (Cash/UPI)</span>
                                        </label>
                                    </div>

                                    <!-- UPI Payment -->
                                    <div class="payment-option-item" data-payment="UPI">
                                        <label class="payment-option-label">
                                            <input type="radio" name="payment" value="UPI">
                                            <span class="payment-radio-custom"></span>
                                            <i class="fa-solid fa-mobile-screen-button payment-icon"></i>
                                            <span class="payment-name">UPI Payment</span>
                                        </label>
                                    </div>

                                    <!-- Credit/Debit Card -->
                                    <div class="payment-option-item" data-payment="CARD">
                                        <label class="payment-option-label">
                                            <input type="radio" name="payment" value="CARD">
                                            <span class="payment-radio-custom"></span>
                                            <i class="fa-solid fa-credit-card payment-icon"></i>
                                            <span class="payment-name">Credit/Debit Card</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Recommended Payment Options Sub-panel -->
                                <div class="recommended-payment-panel" id="recommendedPanel">
                                    <h5>Recommended Payment Options</h5>
                                    <div class="selected-payment-details" id="selectedPaymentDetails">
                                        <label class="selected-payment-label">
                                            <input type="radio" name="payment" value="OTHER" checked>
                                            <span class="payment-radio-custom"></span>
                                            <i class="fa-solid fa-money-bill-wave selected-payment-icon"></i>
                                            <span class="selected-payment-name">Cash on Delivery (Cash/UPI)</span>
                                        </label>
                                        <p class="selected-payment-desc">You can pay via Cash/UPI on delivery.</p>
                                    </div>
                                    <button type="submit" class="place-order-btn" id="placeOrderButton">
                                        Place Order
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- RIGHT PANEL: Order Summary -->
            <div class="address-right checkout-right-box">
                        <div class="address-right-top checkout-details">
                            <h3>Order Summary ({{ $totalUniqueItems }})</h3>

                            @foreach ($cartItems as $item)
                                <div class="address-right-box">
                                    <!-- Product Image and Name Section -->
                                    <div class="d-flex justify-content-between align-items-start mb-2 address-right-box-inner">
										<div class="d-flex leftPart">
                                        <!-- Product Image -->
                                        @if ($item->product->thumbnail)
                                            <div class="cart-image" style="">
                                                <img src="{{ asset($item->product->thumbnail) }}"
                                                    alt="{{ $item->product->name }}"
                                                    style="">
                                            </div>
                                        @else
                                            <div class="cart-icon" style="">
                                                <i class="fas fa-image" style=""></i>
                                            </div>
                                        @endif

                                        <!-- Product Name and Combo -->
                                        <div class="flex-grow-1 cart-content">											
                                            <h4 class="mb-1">{{ $loop->iteration }}. {{ $item->product->name }}</h4>
											<ul class="cart-content-list">
												<li>
													<div class="d-flex justify-content-between align-items-center">
														<div>
															<p class="mb-0 proPriceQuantity">
																₹{{ number_format($item->main_product_price ?? 0, 2) }} ×
																{{ $item->main_product_quantity ?? 1 }}
																@if ($item->combo_product_price)
																	<br>
																	<small class="text-muted">
																		+ Combo:
																		₹{{ number_format($item->combo_product_price, 2) }} ×
																		{{ $item->combo_product_quantity }}
																	</small>
																@endif
															</p>
														</div>
														
													</div>
												</li>
											</ul>
											</div>
											</div>
											<div class="rightPart">
											<div class="fw-medium proPrice">
															₹{{ number_format(
																$item->main_product_price * $item->main_product_quantity +
																	$item->combo_product_price * $item->combo_product_quantity,
																2,
															) }}
														</div>
											</div>
											</div>
                                            <!-- Combo display -->
                                            @if ($item->combo_id)
                                                @php $comboProduct = \App\Models\Product::find($item->combo_id); @endphp
                                                @if ($comboProduct)
                                                    <div class="d-flex align-items-start mt-1">
                                                        @if ($comboProduct->thumbnail)
                                                            <div class="me-2"
                                                                style="width: 40px; height: 40px; flex-shrink: 0;">
                                                                <img src="{{ asset($comboProduct->thumbnail) }}"
                                                                    alt="{{ $comboProduct->name }}"
                                                                    style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">
                                                            </div>
                                                        @endif
                                                        <p class="mb-0 text-muted" style="font-size: 12px;">+
                                                            {{ $comboProduct->name }}</p>
                                                    </div>
                                                @endif
                                            @endif
                                        
										

                                    <!-- Pricing Details -->
                                    
                                </div>
                            @endforeach

                            <!-- PRICING SUMMARY -->
                            <div class="address-right-box">
                                <ul>
                                    <li>
                                        <p>Subtotal</p>
                                        <span>₹{{ number_format($totalSubtotal, 2) }}</span>
                                    </li>

                                    @if ($appliedCoupon)
                                        <li>
                                            <p>Coupon ({{ $appliedCoupon->coupon_code }})</p>
                                            <span class="text-success">- ₹{{ number_format($totalDiscount, 2) }}</span>
                                        </li>
                                    @endif

                                    @if (($totalEmbroideryPrice ?? 0) > 0)
                                        <li>
                                            <p>Total Embroidery</p>
                                            <span>+ ₹{{ number_format($totalEmbroideryPrice, 2) }}</span>
                                        </li>
                                    @endif

                                    <li>
                                        <p>GST ({{ number_format($gstPercent, 2) }}%)</p>
                                        <span>+ ₹{{ number_format($totalGST, 2) }}</span>
                                    </li>

                                    <li class="js-shipping-charge-li" style="{{ ($shippingCharge ?? 0) > 0 || ($selectedAddress ?? null) ? '' : 'display: none;' }}">
                                        <p>Shipping</p>
                                        <span class="js-shipping-charge-amount">+ ₹{{ number_format(($shippingCharge ?? 0), 2) }}</span>
                                    </li>

                                    <li>
                                        <p><strong>Total</strong></p>
                                        <span id="order-total"><strong>₹{{ number_format($grandTotal, 2) }}</strong></span>
                                    </li>
                                </ul>
                            </div>

                            <!-- FINAL TOTAL + YOU SAVE -->
                            <div class="address-right-box border0">
                                <ul>
                                    <li>
                                        <p><strong>Total</strong></p>
                                        <span
                                            class="total-text" id="final-total"><strong>₹{{ number_format($grandTotal, 2) }}</strong></span>
                                    </li>

                                    @if ($youSave > 0)
                                        <li>
                                            <p class="text-success"><strong>You Save</strong></p>
                                            <span
                                                class="text-success"><strong>₹{{ number_format($youSave, 2) }}</strong></span>
                                        </li>
                                    @endif
                                </ul>
                            </div>
                        </div>

                        

                    </div>

            </div>
        </div>
    </div>
</section>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentRadios = document.querySelectorAll('input[name="payment"]');
    const paymentMethodField = document.getElementById('payment_method');
    const placeOrderButton = document.getElementById('placeOrderButton');
    const paymentForm = document.getElementById('paymentForm');
    const paymentOptionItems = document.querySelectorAll('.payment-option-item');
    const recommendedPanel = document.getElementById('recommendedPanel');
    const selectedPaymentDetails = document.getElementById('selectedPaymentDetails');

    const baseTotalAfterDiscount = {{ json_encode((float) ($baseTotalAfterDiscount ?? 0)) }};
    const selectedAddressId = {{ json_encode($selectedAddressId ?? null) }};
    const selectedPostcode = {{ json_encode($selectedAddress?->pincode ?? null) }};
    const csrfToken = {{ json_encode(csrf_token()) }};
    const shippingCalculateUrl = {{ json_encode(route('frontend.calculate-shipping-charge')) }};
    const saveShippingUrl = {{ json_encode(route('frontend.save-shipping-charge')) }};

    let shippingCharge = {{ json_encode((float) ($shippingCharge ?? 0)) }};
    let shippingRequestId = 0;

    // Payment option data
    const paymentOptions = {
        'OTHER': {
            name: 'Cash on Delivery (Cash/UPI)',
            icon: 'fa-money-bill-wave',
            description: 'You can pay via Cash/UPI on delivery.',
            color: '#ff6b9d'
        },
        'UPI': {
            name: 'UPI Payment',
            icon: 'fa-mobile-screen-button',
            description: 'Pay instantly using any UPI app.',
            color: '#007bff'
        },
        'CARD': {
            name: 'Credit/Debit Card',
            icon: 'fa-credit-card',
            description: 'Pay securely with your card.',
            color: '#007bff'
        }
    };

    // Initialize
    updatePaymentSelection();
    updatePlaceOrderButton();

    updateTotals();
    if (selectedPostcode) {
        fetchShippingCharge(selectedPostcode);
    }

    // Add event listeners to payment radio buttons
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            paymentMethodField.value = this.value;
            updatePaymentSelection();
            updatePlaceOrderButton();
        });
    });

    // Add click handlers to payment option items
    paymentOptionItems.forEach(item => {
        item.addEventListener('click', function(e) {
            if (e.target.type !== 'radio' && e.target.tagName !== 'LABEL' && !e.target.closest('label')) {
                const radio = this.querySelector('input[type="radio"]');
                if (radio) {
                    radio.checked = true;
                    paymentMethodField.value = radio.value;
                    updatePaymentSelection();
                    updatePlaceOrderButton();
                }
            }
        });
    });

    function updatePaymentSelection() {
        const selectedPayment = document.querySelector('input[name="payment"]:checked').value;
        const option = paymentOptions[selectedPayment];
        
        // Update payment option items styling
        paymentOptionItems.forEach(item => {
            const radio = item.querySelector('input[type="radio"]');
            if (radio && radio.value === selectedPayment) {
                item.classList.add('selected');
            } else {
                item.classList.remove('selected');
            }
        });

        // Update recommended panel
        if (recommendedPanel && option && selectedPaymentDetails) {
            const detailsHtml = `
                <label class="selected-payment-label">
                    <input type="radio" name="payment" value="${selectedPayment}" checked>
                    <span class="payment-radio-custom"></span>
                    <i class="fa-solid ${option.icon} selected-payment-icon"></i>
                    <span class="selected-payment-name">${option.name}</span>
                </label>
                <p class="selected-payment-desc">${option.description}</p>
            `;
            selectedPaymentDetails.innerHTML = detailsHtml;
            
            // Update icon color
            const icon = selectedPaymentDetails.querySelector('.selected-payment-icon');
            if (icon) {
                icon.style.color = option.color;
            }
            
            // Re-attach event listener to new radio
            const newRadio = selectedPaymentDetails.querySelector('input[type="radio"]');
            if (newRadio) {
                newRadio.addEventListener('change', function() {
                    paymentMethodField.value = this.value;
                    updatePaymentSelection();
                    updatePlaceOrderButton();
                });
            }
        }
    }

    function updatePlaceOrderButton() {
        const selectedPayment = document.querySelector('input[name="payment"]:checked').value;
        
        // Enable button only for Cash on Delivery (OTHER)
        if (selectedPayment === 'OTHER') {
            placeOrderButton.disabled = false;
        } else {
            placeOrderButton.disabled = true;
        }
    }

    function formatCurrency(amount) {
        const value = Number(amount || 0);
        const formatted = value.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        return '₹' + formatted;
    }

    function updateTotals() {
        const total = Number(baseTotalAfterDiscount) + Number(shippingCharge || 0);
        const orderTotalEl = document.querySelector('#order-total strong');
        const finalTotalEl = document.querySelector('#final-total strong');
        if (orderTotalEl) orderTotalEl.textContent = formatCurrency(total);
        if (finalTotalEl) finalTotalEl.textContent = formatCurrency(total);
    }

    function setShippingDisplay(text, show) {
        const li = document.querySelector('.js-shipping-charge-li');
        const amountEl = document.querySelector('.js-shipping-charge-amount');
        if (!li || !amountEl) return;
        amountEl.textContent = text;
        li.style.display = show ? '' : 'none';
    }

    async function fetchShippingCharge(postcode) {
        shippingRequestId += 1;
        const requestId = shippingRequestId;

        shippingCharge = 0;
        setShippingDisplay('Calculating...', true);
        updateTotals();

        try {
            const formData = new FormData();
            formData.append('delivery_postcode', postcode);
            formData.append('_token', csrfToken);

            const response = await fetch(shippingCalculateUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (requestId !== shippingRequestId) return;

            const data = await response.json();
            if (data && data.status === 'success') {
                shippingCharge = Number(data.shipping_charge || 0);
                setShippingDisplay('+ ' + formatCurrency(shippingCharge), true);
                updateTotals();
                if (selectedAddressId) {
                    saveShippingCharge(selectedAddressId, shippingCharge);
                }
            } else {
                shippingCharge = 0;
                setShippingDisplay('Add address to calculate', false);
                updateTotals();
            }
        } catch (e) {
            if (requestId !== shippingRequestId) return;
            shippingCharge = 0;
            setShippingDisplay('Add address to calculate', false);
            updateTotals();
        }
    }

    async function saveShippingCharge(addressId, charge) {
        try {
            const formData = new FormData();
            formData.append('address_id', addressId);
            formData.append('shipping_charge', charge);
            formData.append('_token', csrfToken);

            await fetch(saveShippingUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
        } catch (e) {
        }
    }

    // Handle form submission
    paymentForm.addEventListener('submit', function(e) {
        const selectedPayment = document.querySelector('input[name="payment"]:checked').value;
        
        if (selectedPayment !== 'OTHER') {
            e.preventDefault();
            alert('Please select Cash on Delivery to place your order.');
            return false;
        }
        
        return true;
    });

    // Continue Shopping button
    const shoppingBtn = document.querySelector('.shopping-btn-custom');
    if (shoppingBtn) {
        shoppingBtn.addEventListener('click', function() {
            window.location.href = "{{ route('frontend.cart') }}";
        });
    }
});
</script>

<style>

</style>

@endsection
