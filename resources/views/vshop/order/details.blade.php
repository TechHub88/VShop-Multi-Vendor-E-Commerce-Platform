@extends('vshop.main')

@section('title', 'Order Details #' . $order->id)

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
                            <li><a href="{{ route('vshop.order.list') }}">Orders</a> /</li>
                            <li>Order No : {{ $order->id }}</li>
                        </ul>
                    </div>
                </div>

                {{-- ================= SUCCESS/ERROR MESSAGES ================= --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                <div class="order-not-deliver-sec">
                    <div class="order-not-deliver-inner">
                        
                        {{-- ================= ORDER HEADER ================= --}}
                        <div class="order-list-wrapper">
                            <div class="order-list-box">
                                <h3>
                                    <a href="#">Order no : {{ $order->id }}</a>
                                </h3>
                                <div class="total-order-sec">
                                    <p>Total Items : <span class="fw-bold">{{ $totalItems }}</span></p>
                                    <p>Total Quantity : <span class="fw-bold">{{ $totalQuantity }}</span></p>
                                </div>
                            </div>
                            <div class="order-list-box">
                                <div class="total-time-sec">
                                    <p><i class="fa-regular fa-clock me-1"></i>{{ $order->created_at->timezone('Asia/Kolkata')->format('h:i A') }}</p>
                                    <p><i class="fa-regular fa-calendar me-1"></i>{{ $order->created_at->format('d-m-Y') }}</p>
                                </div>
                                <p class="order-status">
                                    Order Status : 
                                    <span class="order-badge {{ $order->status }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="order-list-box">
                                <div class="total-time-sec justify-content-end">
                                    <p class="blackColor">Estimated Delivery Date :</p>
                                    <p class="grenColor fw-bold">
                                        @php
                                            $maxDeliveryDays = 0;
                                            foreach($order->items as $item) {
                                                $days = (int)($item->product->delivery_time ?? 0);
                                                if($days > $maxDeliveryDays) {
                                                    $maxDeliveryDays = $days;
                                                }
                                            }
                                            if($maxDeliveryDays == 0) $maxDeliveryDays = 7;
                                            $calcDeliveryDate = $order->created_at->copy()->addDays($maxDeliveryDays);
                                        @endphp
                                        {{ $order->estimated_delivery_date ? $order->estimated_delivery_date->format('d-m-Y') : $calcDeliveryDate->format('d-m-Y') }}
                                    </p>
                                </div>
                                <p class="grandtotal">
                                    Grand Total : <span class="blueColor fw-bold">₹ {{ number_format($order->total_amount, 2) }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- ================= PAYMENT DETAILS ================= --}}
                        <div class="payment-method-sec">
    <div class="row align-items-center">

        {{-- Payment Method --}}
        <div class="col-md-4">
            <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
        </div>

        {{-- Payment Status --}}
        <div class="col-md-4">
            <p><strong>Payment Status:</strong> 
                <span class="badge text-black {{ $paymentStatusClasses[$order->payment_status] ?? 'badge-secondary' }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </p>
        </div>

       <div class="col-md-4 text-md-end">
    
    <div class="d-inline-flex align-items-center gap-2">

        {{-- Invoice --}}
        <button class="invoice-btn" onclick="downloadInvoice({{ $order->id }})">
            <i class="fa-regular fa-file-pdf me-2"></i>Download Invoice
        </button>

        {{-- Cancel --}}
        @if(in_array($order->status, ['pending','paid','shipped']))
        <button class="btn btn-danger btn-sm cancelOrderBtn" data-id="{{ $order->id }}">
            Cancel Order
        </button>
        @endif

        {{-- Return --}}
        @if($order->status == 'delivered')
        <button class="btn btn-warning btn-sm returnOrderBtn" data-id="{{ $order->id }}">
            Return Order
        </button>
        @endif

    </div>

</div>

    </div>

    @if($order->transaction_id)
    <p class="mt-2"><strong>Transaction ID:</strong> {{ $order->transaction_id }}</p>
    @endif
</div>

                        {{-- ================= ORDER ITEMS ================= --}}
                        @foreach($order->items as $index => $item)
                        @php
                            $itemGst = $item->total * ($item->gst_percent / 100);
                            $itemSubtotal = $item->total + $itemGst;
                        @endphp
                        
                        <div class="order-cart-list-sec {{ $loop->first ? 'pt-0' : '' }}">
                            <div class="cart-list-sec border-0 {{ $loop->first ? 'pt-0' : '' }}">
                                
                                {{-- LEFT SIDE - PRODUCT INFO --}}
                                <div class="cart-list-left">
                                    <div class="cart-list-left-img">
                                        <img src="{{ asset($item->product->thumbnail ?? $item->product_thumbnail ?? 'vtravelassets/images/banner-pic-main.webp') }}" 
                                             alt="{{ $item->product_name }}" class="img-fluid">
                                    </div>
                                    
                                    <div class="product-list-box-right">
                                        {{-- Product Title --}}
                                        <div class="product-heading">
                                            <div class="product-heading-left">
                                                <h3>{{ $item->product_name ?? $item->product->name ?? 'Product' }}</h3>
                                            </div>
                                        </div>

                                        {{-- Price & Discount --}}
                                        <div class="rating-sec">
                                            <div class="rating-left">
                                                <ul>
                                                    @php
                                                        $regularPrice = $item->product->regular_price ?? $item->price;
                                                        $sellingPrice = $item->price;
                                                        $discountPercentage = 0;
                                                        if($regularPrice > $sellingPrice && $regularPrice > 0) {
                                                            $discountPercentage = round((($regularPrice - $sellingPrice) / $regularPrice) * 100);
                                                        }
                                                    @endphp
                                                    @if($discountPercentage > 0)
                                                    <li class="blackColor">
                                                        <p class="old-rupess">₹ {{ number_format($regularPrice, 2) }}</p>
                                                    </li>
                                                    <li>
                                                        <p class="off-box">{{ $discountPercentage }}% Off</p>
                                                    </li>
                                                    @endif
                                                    <li>
                                                        <p class="new-rupess">₹ {{ number_format($sellingPrice, 2) }}</p>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        {{-- Size, Color, Quantity --}}
                                        <div class="order-deliver-dtl-sec">
                                            @if($item->size_name || $item->size_id)
                                            <div class="">
                                                <label class="quantity-box active">
                                                    <span>{{ $item->size_name ?? $item->size->name ?? 'Size' }}</span>
                                                </label>
                                            </div>
                                            @endif

                                            @if($item->color_name || $item->color_id)
                                            <div class="order-deliver-color-sec">
                                                @php
                                                    $colorValue = '#000';
                                                    if($item->color) {
                                                        $colorValue = $item->color->color_code ?? '#000';
                                                    } elseif($item->color_name) {
                                                        $colorValue = '#' . substr(md5($item->color_name), 0, 6);
                                                    }
                                                @endphp
                                                <label class="color-box">
                                                    <span class="color-circle" style="background: {{ $colorValue }};"></span>
                                                </label>
                                                <p style="font-size: 13px;">( {{ $item->color_name ?? $item->color->name ?? 'Color' }} )</p>
                                            </div>
                                            @endif

                                            <div class="qty-box">
                                                <span>Qty</span>
                                                <input type="text" readonly value="{{ str_pad($item->quantity, 2, '0', STR_PAD_LEFT) }}">
                                            </div>
                                        </div>

                                        {{-- Delivery Address --}}
                                        <div class="product-dtl-sec mt-3">
                                            <p>Delivery to : 
                                                <a href="#" class="track-order-link" data-order="{{ $order->id }}" style="color: #349529; text-decoration: underline;">
                                                    Track Your Order
                                                </a>
                                            </p>                        
                                        </div>
                                  <p class="address-text">
                            {{ $shippingAddress->address_line1 ?? '' }},
                            {{ $shippingAddress->city ?? '' }},
                            {{ $shippingAddress->state ?? '' }} -
                            {{ $shippingAddress->pincode ?? '' }}
                        </p> 
                        </div>
                                </div>

                                {{-- RIGHT SIDE - PRICE BREAKDOWN --}}
                                <div class="cart-price-sec cart-price-box">
                                    <div class="cart-peice-box pt-0">
                                        <p class="delivery-date">
                                            Estimated Delivered On :  
                                            @php
                                                $itemDays = (int)($item->product->delivery_time ?? 7);
                                                if($itemDays == 0) $itemDays = 7;
                                                $itemDeliveryDate = $order->created_at->copy()->addDays($itemDays);
                                            @endphp
                                            <span class="ml-5">{{ $itemDeliveryDate->format('d M Y') }}</span>
                                        </p>
                                        <ul>
                                            <li>
                                                <p>Price</p>
                                                <span class="rupess">₹ {{ number_format($item->price, 2) }}</span>
                                            </li>
                                            <li>
                                                <p>Quantity</p>
                                                <span>X {{ $item->quantity }}</span>
                                            </li>
                                            <li class="justify-content-end mb-0">
                                                <p class="rupess">₹ {{ number_format($item->total, 2) }}</p>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="cart-peice-box border0 pb-0">
                                        <ul>
                                            <li>
                                                <p>GST {{ $item->gst_percent ?? 5 }}%</p>
                                                <span>+ ₹ {{ number_format($itemGst, 2) }}</span>
                                            </li>
                                           
                                            <li class="mb-0">
                                                <p class="subColor">Sub</p>
                                                <span class="rupess blueColor" style="color: #004392 !important;">
                                                    ₹ {{ number_format($itemSubtotal + ($order->courier_charge ?? 220), 2) }}
                                                </span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- ================= RETURN/REPLACE/RATING SECTION ================= --}}
                            @if(in_array($order->status, ['delivered', 'shipped']))
                            <div class="return-replace-sec">
                                <ul class="tabs">
                                    <li data-tab-target="#return-{{ $item->id }}" class="active tab">Return</li>
                                    <li data-tab-target="#replace-{{ $item->id }}" class="tab">Replace</li>
                                    <li data-tab-target="#rate-{{ $item->id }}" class="tab">
                                        Rate this Product 
                                        <span class="rating-stars">
                                            <i class="fa-regular fa-star"></i>
                                            <i class="fa-regular fa-star"></i>
                                            <i class="fa-regular fa-star"></i>
                                            <i class="fa-regular fa-star"></i>
                                            <i class="fa-regular fa-star"></i>
                                        </span>
                                    </li>
                                    <li>
                                        <button class="re-order-btn" onclick="reorderItem({{ $item->product_id }})">
                                            <i class="fa-regular fa-rotate-right me-2"></i>Re-Order
                                        </button>
                                    </li>
                                </ul>

                                <div class="tab-content">
                                    {{-- RETURN TAB --}}
                                    <div id="return-{{ $item->id }}" data-tab-content class="active">
                                        @include('vshop.order.partials.return-form', ['item' => $item, 'type' => 'return'])
                                    </div>

                                    {{-- REPLACE TAB --}}
                                    <div id="replace-{{ $item->id }}" data-tab-content>
                                        @include('vshop.order.partials.return-form', ['item' => $item, 'type' => 'replace'])
                                    </div>

                                    {{-- RATING TAB --}}
                                    <div id="rate-{{ $item->id }}" data-tab-content>
                                        @include('vshop.order.partials.rating-form', ['item' => $item])
                                    </div>
                                </div>
                            </div>
                            @endif

                            {{-- ================= RETURN STATUS (if returned) ================= --}}
                            @if($item->return_status ?? false)
                            <div class="return-status-sec">
                                <div class="return-status-left">
                                    <div class="date-review-sec">
                                        <div class="return-date-box">
                                            Return Date : {{ $item->return_date ? date('d-m-Y', strtotime($item->return_date)) : 'N/A' }}
                                        </div>
                                        <a href="#" class="review-btn" onclick="viewReview({{ $item->id }})">
                                            See Your Review
                                            <span class="rating-display">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= ($item->rating ?? 0))
                                                        <i class="fa-solid fa-star text-warning"></i>
                                                    @else
                                                        <i class="fa-regular fa-star"></i>
                                                    @endif
                                                @endfor
                                            </span>
                                        </a>
                                    </div>
                                    <p>Return Status : <span class="greenColor">{{ ucfirst($item->return_status) }}</span></p>
                                    @if($item->return_status == 'partial')
                                    <p>Return Status : <span class="redColor">Part Return</span> / <span class="blueColor">Collected</span></p>
                                    @endif
                                </div>
                                <div class="return-status-right">
                                    <p>Return Amount</p>
                                    <p class="big-ammount">- ₹ {{ number_format($item->return_amount ?? 0, 2) }}</p>
                                </div>
                            </div>
                            @endif
                        </div>
                        @endforeach

                        {{-- ================= ORDER TOTALS ================= --}}
                        <div class="total-sec pb-0">
                            <div class="total-left">
                                <p class="mb-0">Total Items : <span class="blueColor fw-bold">{{ $totalItems }}</span></p>
                                <p class="mb-0">Total Quantity : <span class="blueColor fw-bold">{{ $totalQuantity }}</span></p>
                                @if($order->coupon_code)
                                <p class="mb-0">Coupon Applied : <span class="greenColor">{{ $order->coupon_code }}</span></p>
                                @endif
                            </div>
                            <div class="cart-price-sec">
                                <div class="cart-peice-box border0 p-0">
                                    <ul>
                                        <li class="mb-2">
                                            <p>Subtotal :</p>
                                            <span class="rupess">₹ {{ number_format($order->sub_total, 2) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <p>GST Total :</p>
                                            <span class="rupess">+ ₹ {{ number_format($order->gst_amount, 2) }}</span>
                                        </li>
                                        <li class="mb-2">
                                            <p>Courier Charges :</p>
                                            <span class="rupess">+ ₹ {{ number_format($order->courier_charge ?? 0, 2) }}</span>
                                        </li>
                                        @if($order->discount_amount > 0)
                                        <li class="mb-2">
                                            <p>Discount :</p>
                                            <span class="rupess text-danger">- ₹ {{ number_format($order->discount_amount, 2) }}</span>
                                        </li>
                                        @endif
                                        <li class="mb-0 border-top pt-2">
                                            <p class="subColor fw-bold">Grand Total :</p>
                                            <span class="rupess fw-bold" style="color: #004392 !important;">
                                                ₹ {{ number_format($order->total_amount, 2) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                     

{{-- If billing address is same as shipping address, show this message instead --}}
@if($order->shippingAddress && (!$order->billingAddress || $order->billingAddress->id == $order->shippingAddress->id))
<div class="billing-address-sec mt-4">
    <div class="address-header">
        <h4><i class="fa-solid fa-file-invoice"></i>Billing Address</h4>
        <span class="address-type-badge badge-same">
            <i class="fa-solid fa-circle-check"></i>Same as Shipping
        </span>
    </div>
    
    <div class="address-content p-3 bg-light rounded">
        <p class="mb-0">
            <i class="fa-solid fa-circle-check"></i>
            Billing address is the same as shipping address.
        </p>
        @if($order->shippingAddress->gstin)
        <p class="mb-0 mt-2">
            <span class="gst-badge">
                <i class="fa-solid fa-file-invoice"></i>GSTIN: {{ $order->shippingAddress->gstin }}
            </span>
        </p>
        @endif
    </div>
</div>
@endif

<!-- TRACK ORDER MODAL -->
<div class="modal fade" id="trackOrderModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content shadow-lg border-0 rounded-3">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-truck-fast me-2 text-primary"></i>
                    Track Your Order
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="d-flex justify-content-between align-items-center mb-4 px-3 py-2 bg-light rounded">
                    <div>
                        <small class="text-muted d-block">Courier Partner</small>
                        <span class="fw-bold">{{ $order->courier_name ?? 'Standard Delivery' }}</span>
                    </div>
                    <div>
                        <small class="text-muted d-block">Tracking ID / AWB</small>
                        <span class="fw-bold text-primary">{{ $order->tracking_id ?? $order->sr_awb_code ?? 'Not assigned' }}</span>
                    </div>
                    @if($order->tracking_id || $order->sr_awb_code)
                    <div>
                        <a href="https://shiprocket.co/tracking/{{ $order->tracking_id ?? $order->sr_awb_code }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            Track Live
                        </a>
                    </div>
                    @endif
                </div>

                @if(in_array($order->status, ['dispatched', 'shipped', 'out_for_delivery']))
                <div class="alert alert-info py-2 mb-4 d-flex align-items-center">
                    <i class="fa-solid fa-shield-halved me-2 fs-4"></i>
                    <div>
                        <small class="d-block mb-1">Delivery Verification OTP</small>
                        <h4 class="mb-0 fw-bold letter-spacing-2">{{ $deliveryOtp ?? str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT) }}</h4>
                    </div>
                </div>
                @endif

                <div class="track-wrapper my-5">
                    @if($order->status === 'cancelled')
                        <div class="track-box active">
                            <div class="track-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                            <p class="mt-2 fw-semibold">Confirmed</p>
                            <small class="text-muted">{{ $order->created_at->format('d M, h:i A') }}</small>
                        </div>
                        <div class="track-box active cancelled-track">
                            <div class="track-icon" style="background:#dc3545;"><i class="fa-solid fa-circle-xmark"></i></div>
                            <p class="mt-2 fw-semibold text-danger">Cancelled</p>
                            <small class="text-muted">{{ $order->updated_at->format('d M, h:i A') }}</small>
                        </div>
                    @else
                        <div class="track-box {{ in_array($order->status, ['pending', 'paid', 'confirmed', 'processing', 'shipped', 'dispatched', 'delivered', 'return_requested', 'return_approved', 'returned', 'replacement_requested', 'replacement_approved']) ? 'active' : '' }}">
                            <div class="track-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                            <p class="mt-2 fw-semibold">Confirmed</p>
                            <small class="text-muted">{{ $order->created_at->format('d M, h:i A') }}</small>
                        </div>
                        <div class="track-box {{ in_array($order->status, ['processing', 'shipped', 'dispatched', 'delivered', 'return_requested', 'return_approved', 'returned', 'replacement_requested', 'replacement_approved']) ? 'active' : '' }}">
                            <div class="track-icon"><i class="fa-solid fa-box-open"></i></div>
                            <p class="mt-2 fw-semibold">Packed</p>
                            <small class="text-muted">{{ $order->updated_at->format('d M') }}</small>
                        </div>
                        <div class="track-box {{ in_array($order->status, ['shipped', 'dispatched', 'delivered', 'return_requested', 'return_approved', 'returned', 'replacement_requested', 'replacement_approved']) ? 'active' : '' }}">
                            <div class="track-icon"><i class="fa-solid fa-truck-fast"></i></div>
                            <p class="mt-2 fw-semibold">Shipped</p>
                            <small class="text-muted">{{ $order->shipped_at ? $order->shipped_at->format('d M, h:i A') : 'Pending' }}</small>
                        </div>
                        <div class="track-box {{ in_array($order->status, ['delivered', 'return_requested', 'return_approved', 'returned', 'replacement_requested', 'replacement_approved']) ? 'active' : '' }}">
                            <div class="track-icon"><i class="fa-solid fa-house-chimney"></i></div>
                            <p class="mt-2 fw-semibold">Delivered</p>
                            <small class="text-muted">{{ $order->delivered_at ? $order->delivered_at->format('d M, h:i A') : 'Pending' }}</small>
                        </div>
                    @endif
                </div>

                @if($order->activityLogs && $order->activityLogs->count() > 0)
                <hr>
                <h6 class="fw-bold mb-3">Timeline</h6>
                <div class="timeline px-2">
                    @foreach($order->activityLogs as $log)
                    <div class="d-flex mb-3">
                        <div class="me-3 text-end" style="width: 80px;">
                            <small class="text-muted d-block">{{ $log->created_at->format('h:i A') }}</small>
                            <small class="text-muted">{{ $log->created_at->format('d M') }}</small>
                        </div>
                        <div class="border-start border-2 border-primary ps-3 position-relative">
                            <span class="position-absolute top-0 start-0 translate-middle p-1 bg-primary border border-light rounded-circle"></span>
                            <span class="fw-semibold d-block">{{ $log->description }}</span>
                            @if($log->remarks)
                            <small class="text-muted">{{ $log->remarks }}</small>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</div>


                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- ORDER ACTION MODAL -->
<div class="modal fade" id="orderActionModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg border-0 rounded-3">

            {{-- HEADER --}}
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-rotate-left me-2 text-primary"></i>
                    Submit Request
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <form id="orderActionForm">
                @csrf

                <input type="hidden" name="order_id" id="modal_order_id">
                <input type="hidden" name="type" id="modal_action_type">

                {{-- BODY --}}
                <div class="modal-body pt-2">

                    {{-- REASON --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Reason</label>
                        <select name="reason" class="form-select shadow-sm">
                            <option value="">-- Choose Reason --</option>
                            <option value="Changed mind">Changed mind</option>
                            <option value="Wrong item ordered">Wrong item ordered</option>
                            <option value="Found cheaper elsewhere">Found cheaper elsewhere</option>
                            <option value="Product damaged">Product damaged</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    {{-- NOTE --}}
                    <div class="mb-2">
                        <label class="form-label fw-semibold">Additional Note</label>
                        <textarea name="note" class="form-control shadow-sm" rows="3"
                            placeholder="Write your reason or additional details..."></textarea>
                    </div>

                </div>

                {{-- FOOTER --}}
                <div class="modal-footer border-0 pt-0">

                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit" class="btn btn-primary px-4 d-flex align-items-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        Submit Request
                    </button>

                </div>

            </form>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>

/* SAME SIZE BUTTON FIX */
.invoice-btn,
.cancelOrderBtn {
    height: 42px;
    padding: 0 16px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
}

/* OPTIONAL: width same karna ho */
.invoice-btn,
.cancelOrderBtn {
    min-width: 140px;
}
.cart-list-left-img{
    width:200x;
    height:200px;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}

.cart-list-left-img img{
    width:100%;
    height:100%;
    object-fit:contain;
}
.address-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 10px;
}

.address-header h4 {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
    color: #333;
}

.address-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 500;
}

.badge-billing {
    background: #f3e5f5;
    color: #7b1fa2;
}

.badge-both {
    background: #e8f5e9;
    color: #2e7d32;
}

.badge-same {
    background: #e3f2fd;
    color: #0066b3;
}

.address-content {
    border: 1px solid #e0e0e0;
}

.gst-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #fff3e0;
    color: #e65100;
    border-radius: 4px;
    font-size: 0.8rem;
}

.default-badge {
    display: inline-block;
    padding: 4px 12px;
    background: #ffd700;
    color: #333;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
}

.bg-light {
    background-color: #f8f9fa !important;
}

.rounded {
    border-radius: 8px !important;
}

.order-badge{
    padding:4px 10px;
    border-radius:20px;
    font-size:13px;
    font-weight:600;
}

.order-badge.paid{
    background:#e6f7ee;
    color:#1f9254;
}

.order-badge.pending{
    background:#fff3cd;
    color:#856404;
}

.order-badge.cancelled{
    background:#fdecea;
    color:#d93025;
}

.cancelOrderBtn {
    border-radius: 30px;
}
.dashboardPagesContent {
    padding-bottom: 40px !important;
}

.track-wrapper {
    display: flex;
    align-items: center;
    max-width: 660px;
    margin: 0 auto;
}
.track-box {
    width: 25%;
    text-align: center;
    position: relative;
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 12px;
}
.track-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background: #eee;
    color: #fff;
    position: relative;
}
.track-box::before {
    content: "";
    position: absolute;
    top: 16px;
    right: -72px;
    width: 147px;
    height: 3px;
    background: #eee;
}
.track-box:last-child::before {
    display: none;
}
.track-box.active .track-icon {
    background: #28a745;
}
.track-box.active::before {
    background: #28a745;
}
.cancelled-track.active::before {
    background: #dc3545 !important;
}
/* Fixed: removed large padding to fix design
.address-content {
    padding: 50px 20px !important;
}
*/
</style>

<!-- SWEETALERT2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function(){

    // OPEN CANCEL MODAL
    $('.cancelOrderBtn').click(function(){
        $('#modal_order_id').val($(this).data('id'));
        $('#modal_action_type').val('cancel');
        $('#orderActionModal').modal('show');
    });

    // OPEN RETURN MODAL
    $('.returnOrderBtn').click(function(){
        $('#modal_order_id').val($(this).data('id'));
        $('#modal_action_type').val('return');
        $('#orderActionModal').modal('show');
    });

    // SUBMIT FORM (AJAX)
    $('#orderActionForm').submit(function(e){
        e.preventDefault();
        
        Swal.fire({
            title: 'Processing...',
            text: 'Sending your request',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading()
            }
        });

        $.ajax({
            url: "{{ route('vshop.order.action') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    $('#orderActionModal').modal('hide');
                    location.reload();
                });
            },
            error: function(xhr){
                let errorMsg = 'Failed to process request';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: errorMsg
                });
            }
        });

    });

});
</script>
<script>
$(document).ready(function() {
    // Back button functionality
    $('.back-btn').click(function() {
        window.history.back();
    });

    // Tab functionality
    const tabs = document.querySelectorAll('[data-tab-target]');
    const tabContents = document.querySelectorAll('[data-tab-content]');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            const target = document.querySelector(tab.dataset.tabTarget);
            
            // Remove active class from all tabs and contents
            tabContents.forEach(tabContent => {
                tabContent.classList.remove('active');
            });
            tabs.forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Add active class to clicked tab and its content
            tab.classList.add('active');
            target.classList.add('active');
        });
    });

    // Rating stars hover effect
    $('.rating-stars i').hover(
        function() {
            var index = $(this).index();
            $(this).parent().find('i').each(function(i) {
                if(i <= index) {
                    $(this).removeClass('fa-regular').addClass('fa-solid text-warning');
                }
            });
        },
        function() {
            $(this).parent().find('i').removeClass('fa-solid text-warning').addClass('fa-regular');
        }
    );

    // Rating stars click
    $('.rating-stars i').click(function() {
        var rating = $(this).index() + 1;
        $(this).parent().find('i').each(function(i) {
            if(i < rating) {
                $(this).removeClass('fa-regular').addClass('fa-solid text-warning');
            } else {
                $(this).removeClass('fa-solid text-warning').addClass('fa-regular');
            }
        });
        // Store rating value in a hidden input
        $(this).closest('.tab').find('.rating-value').val(rating);
    });
});

// Download invoice function
function downloadInvoice(orderId) {
    let url = "{{ route('vshop.order.invoice', ':id') }}";
    url = url.replace(':id', orderId);
    window.location.href = url;
}

// Track order function
$('.track-order-link').click(function(e) {
    e.preventDefault();
    $('#trackOrderModal').modal('show');
});

// Re-order function
function reorderItem(productId) {
    $.ajax({
        url: '{{ route("vshop.cart.add") }}',
        type: 'POST',
        data: {
            product_id: productId,
            quantity: 1,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            if(response.success) {
                alert('Product added to cart successfully!');
                window.location.href = '{{ route("vshop.cart.index") }}';
            }
        },
        error: function() {
            alert('Failed to add product to cart');
        }
    });
}

// View review function
function viewReview(itemId) {
    alert('Review details for item #' + itemId);
}



// Upload images
function uploadImages(inputId) {
    var files = $('#' + inputId)[0].files;
    if(files.length > 0) {
        // Implement image upload logic here
        alert('Uploading ' + files.length + ' images...');
    }
}

// Remove uploaded image
function removeImage(element) {
    $(element).closest('.return-img-box').remove();
}

// Apply coupon
function applyCoupon() {
    var coupon = $('#coupon_code').val();
    if(coupon) {
        alert('Coupon ' + coupon + ' applied!');
    }
}
</script>
@endpush