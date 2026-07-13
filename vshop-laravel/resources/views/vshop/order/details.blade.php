@extends('vshop.main')

@section('title', 'Order Details #' . $order->id)

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">
                
               
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

        <div class="col-md-3">
            <p style="font-size: 13px;"><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
        </div>

        <div class="col-md-3">
            <p style="font-size: 13px;"><strong>Payment Status:</strong> 
                <span class="badge text-black {{ $paymentStatusClasses[$order->payment_status] ?? 'badge-secondary' }}" style="font-size: 11px;">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </p>
        </div>

       <div class="col-md-6 text-md-end">
    
          <div class="d-flex justify-content-start justify-content-md-end align-items-center gap-2 flex-nowrap" style="overflow-x: auto; white-space: nowrap; padding-bottom: 5px;">

        {{-- Invoice --}}
        <button type="button" class="btn btn-primary btn-sm invoice-btn rounded-pill px-3 fw-bold d-inline-flex align-items-center" style="font-size:12px;" onclick="downloadInvoice({{ $order->id }})">
            <i class="fa-regular fa-file-pdf me-1"></i> Invoice
        </button>

        {{-- ===== CALCULATE IF ORDER HAS RETURNABLE ITEMS ===== --}}
        @php
            $hasReturnableItems = false;
            foreach($order->items as $oItem) {
                $prod = $oItem->product;
                if ($prod) {
                    $isProfOrService = in_array($prod->type, ['professional', 'vshop-professional', 'vtravel-professional', 'service', 'vshop-service', 'vtravel-service']);
                    $isCourse = false;
                    if ($prod->type === 'items') {
                        $catName = strtolower($prod->category->name ?? '');
                        $subCatName = strtolower($prod->subcategory->name ?? '');
                        if (str_contains($catName, 'course') || str_contains($subCatName, 'course')) {
                            $isCourse = true;
                        }
                    }
                    if (!$isProfOrService && !$isCourse) {
                        $hasReturnableItems = true;
                    }
                }
            }
        @endphp

        {{-- ===== CANCEL ORDER BUTTON ===== --}}
        @php
            $statusLower = strtolower($order->status);
            $canCancel = in_array($statusLower, ['pending', 'processing', 'approved', 'paid', 'confirmed']);
            $isOutForDelivery = in_array($statusLower, ['out for delivery', 'out_for_delivery', 'shipped', 'dispatched']);
            $isDelivered = in_array($statusLower, ['delivered', 'completed']);
            
            // If the order has NO returnable items (i.e. only courses or professional services), 
            // the user requested to always show Cancel button and no return/refund.
            if (!$hasReturnableItems && $statusLower !== 'cancelled') {
                $canCancel = true;
                $isOutForDelivery = false;
            }
        @endphp

        @if($canCancel)
        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold d-inline-flex align-items-center" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#cancelOrderModal">
            <i class="fa-solid fa-ban me-1"></i> Cancel
        </button>
        @elseif($isOutForDelivery)
        <button type="button" class="btn btn-danger btn-sm cancelOrderBtn rounded-pill px-3 d-inline-flex align-items-center" style="font-size:12px;" onclick="showCancelNotPossible()">
            <i class="fa-solid fa-ban me-1"></i> Cancel
        </button>
        @endif

       
         @if(in_array($order->status, ['delivered', 'shipped']))
             @if($hasReturnableItems)
             <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#returnOrderModal">
                 <i class="fa-solid fa-arrow-rotate-left me-1"></i> Return
             </button>
             @endif
             <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" style="font-size:12px;" data-bs-toggle="modal" data-bs-target="#rateOrderModal">
                 <i class="fa-solid fa-star me-1"></i> Rate
             </button>
         {{-- MODALS FOR ENTIRE ORDER --}}
         <!-- Return Modal -->
         <div class="modal fade" id="returnOrderModal" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog modal-dialog-centered">
                 <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                     <div class="modal-header border-0 pb-0 pt-4 px-4 text-center d-block position-relative">
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                         <div class="mb-3">
                             <div class="d-inline-flex align-items-center justify-content-center bg-warning text-white rounded-circle" style="width: 60px; height: 60px; font-size: 24px;">
                                 <i class="fa-solid fa-arrow-rotate-left"></i>
                             </div>
                         </div>
                         <h4 class="modal-title fw-bold">Return Items</h4>
                         <p class="text-muted small mb-0">Select the item you wish to return from this order</p>
                     </div>
                     <div class="modal-body p-4">
                         <form action="{{ route('vshop.order.action') }}" method="POST" enctype="multipart/form-data" class="ajax-action-form">
                             @csrf
                             <input type="hidden" name="order_id" value="{{ $order->id }}">
                             <input type="hidden" name="type" value="return">
                             
                             <div class="mb-3">
                                 <label class="form-label fw-semibold text-dark" style="font-size: 14px;">Select Product to Return</label>
                                 <select name="product_id" class="form-select bg-light" required style="border-radius: 8px;">
                                     <option value="">-- Choose Product --</option>
                                     @foreach($order->items as $prodItem)
                                         <option value="{{ $prodItem->product_id }}">{{ $prodItem->product_name ?? $prodItem->product->name ?? 'Item' }} (Qty: {{ $prodItem->quantity }})</option>
                                     @endforeach
                                 </select>
                             </div>

                             <div class="mb-3">
                                 <label class="form-label fw-semibold text-dark" style="font-size: 14px;">Reason for Return</label>
                                 <select name="reason" class="form-select bg-light" required style="border-radius: 8px;">
                                     <option value="">-- Select Reason --</option>
                                     <option value="Damaged Product">Damaged Product</option>
                                     <option value="Wrong Item">Wrong Item</option>
                                     <option value="Other">Other</option>
                                 </select>
                             </div>
                             
                             <div class="mb-3">
                                 <label class="form-label fw-semibold text-dark" style="font-size: 14px;">Attachment (If damaged)</label>
                                 <input type="file" name="attachment" class="form-control bg-light" style="border-radius: 8px;">
                             </div>
                             
                             <div class="mb-4">
                                 <label class="form-label fw-semibold text-dark" style="font-size: 14px;">Additional Note</label>
                                 <textarea name="note" class="form-control bg-light" rows="2" style="border-radius: 8px;" placeholder="Tell us more about the issue..."></textarea>
                             </div>
                             
                             <button type="submit" class="btn btn-warning w-100 fw-bold py-2 text-dark" style="border-radius: 8px; font-size: 16px;">Submit Return Request</button>
                         </form>
                     </div>
                 </div>
             </div>
         </div>

         <!-- Rate Modal -->
         <div class="modal fade" id="rateOrderModal" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog modal-dialog-centered">
                 <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                     <div class="modal-header border-0 pb-0 pt-4 px-4 text-center d-block position-relative">
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                         <div class="mb-3">
                             <div class="d-inline-flex align-items-center justify-content-center bg-primary text-white rounded-circle" style="width: 60px; height: 60px; font-size: 24px;">
                                 <i class="fa-solid fa-star"></i>
                             </div>
                         </div>
                         <h4 class="modal-title fw-bold">Rate a Product</h4>
                         <p class="text-muted small mb-0">Share your experience with us!</p>
                     </div>
                     <div class="modal-body p-4">
                         <form action="{{ route('vshop.order.action') }}" method="POST" class="ajax-action-form">
                             @csrf
                             <input type="hidden" name="order_id" value="{{ $order->id }}">
                             <input type="hidden" name="type" value="rate">
                             <input type="hidden" name="rating" class="rating-value" value="5">
                             
                             <div class="mb-4">
                                 <label class="form-label fw-semibold text-dark" style="font-size: 14px;">Select Product to Rate</label>
                                 <select name="product_id" class="form-select bg-light" required style="border-radius: 8px;">
                                     @foreach($order->items as $prodItem)
                                         <option value="{{ $prodItem->product_id }}">{{ $prodItem->product_name ?? $prodItem->product->name ?? 'Item' }}</option>
                                     @endforeach
                                 </select>
                             </div>

                             <div class="mb-4 text-center">
                                 <h6 class="fw-semibold mb-2 text-dark">Your Rating</h6>
                                 <span class="rating-stars d-inline-block p-2 rounded" style="font-size: 28px; cursor: pointer; background: #f8f9fa;">
                                     <i class="fa-solid fa-star text-warning" data-val="1"></i>
                                     <i class="fa-solid fa-star text-warning" data-val="2"></i>
                                     <i class="fa-solid fa-star text-warning" data-val="3"></i>
                                     <i class="fa-solid fa-star text-warning" data-val="4"></i>
                                     <i class="fa-solid fa-star text-warning" data-val="5"></i>
                                 </span>
                             </div>
                             
                             <div class="mb-4">
                                 <label class="form-label fw-semibold text-dark" style="font-size: 14px;">Write a Review</label>
                                 <textarea name="review" class="form-control bg-light" rows="3" style="border-radius: 8px;" placeholder="Tell us what you think..."></textarea>
                             </div>
                             
                             <button type="submit" class="btn btn-primary w-100 fw-bold py-2" style="border-radius: 8px; font-size: 16px;">Submit Review</button>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
         @endif

         <!-- Cancel Order Modal -->
         <div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-hidden="true">
             <div class="modal-dialog modal-dialog-centered">
                 <div class="modal-content shadow-lg border-0" style="border-radius: 16px;">
                     <div class="modal-header border-0 pb-0 pt-4 px-4 text-center d-block position-relative">
                         <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close"></button>
                         <div class="mb-3">
                             <div class="d-inline-flex align-items-center justify-content-center bg-danger text-white rounded-circle" style="width: 60px; height: 60px; font-size: 24px;">
                                 <i class="fa-solid fa-ban"></i>
                             </div>
                         </div>
                         <h4 class="modal-title fw-bold">Cancel Order</h4>
                         <p class="text-muted small mb-0">Are you sure you want to cancel this order?</p>
                     </div>
                     <div class="modal-body p-4">
                         <form action="{{ route('vshop.order.action') }}" method="POST" class="ajax-action-form">
                             @csrf
                             <input type="hidden" name="order_id" value="{{ $order->id }}">
                             <input type="hidden" name="type" value="cancel">
                             
                             <div class="mb-4">
                                 <label class="form-label fw-semibold text-dark" style="font-size: 14px;">Reason for Cancellation</label>
                                 <select name="reason" class="form-select bg-light" required style="border-radius: 8px;">
                                     <option value="">-- Select Reason --</option>
                                     <option value="Changed my mind">Changed my mind</option>
                                     <option value="Ordered by mistake">Ordered by mistake</option>
                                     <option value="Found a better price elsewhere">Found a better price elsewhere</option>
                                     <option value="Delivery is taking too long">Delivery is taking too long</option>
                                     <option value="Other">Other</option>
                                 </select>
                             </div>
                             
                             <div class="mb-4">
                                 <label class="form-label fw-semibold text-dark" style="font-size: 14px;">Additional Comments (Optional)</label>
                                 <textarea name="note" class="form-control bg-light" rows="2" style="border-radius: 8px;" placeholder="Any additional information..."></textarea>
                             </div>
                             
                             <div class="d-flex gap-2">
                                 <button type="button" class="btn btn-light w-50 fw-bold py-2" data-bs-dismiss="modal" style="border-radius: 8px; font-size: 16px;">Keep Order</button>
                                 <button type="submit" class="btn btn-danger w-50 fw-bold py-2" style="border-radius: 8px; font-size: 16px;">Confirm Cancel</button>
                             </div>
                         </form>
                     </div>
                 </div>
             </div>
         </div>
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
                                                @if(!empty($item->product->provider_name))
                                                <div class="d-flex align-items-center gap-2 mt-1 mb-2">
                                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11px; font-weight: 500; border-radius: 4px;">
                                                        <i class="fa fa-user-tie text-primary me-1"></i> {{ $item->product->provider_name }}
                                                    </span>
                                                </div>
                                                @endif
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

                                        @if(!empty($item->main_product_attributes))
                                        @php
                                            $attrs = json_decode($item->main_product_attributes, true) ?? [];
                                        @endphp
                                        @if(count($attrs) > 0)
                                        <div class="mt-2">
                                            @foreach($attrs as $key => $attr)
                                                @if($key !== 'size' && $key !== 'color')
                                                    @if($key === 'Booking Slots')
                                                    <span style="display:block;font-size:13px;color:#555;">
                                                        <strong><i class="fa fa-calendar-alt text-primary me-1"></i> Booking Slots :</strong>
                                                        <div class="d-flex flex-wrap gap-1 mt-1">
                                                            @foreach(explode(', ', $attr['name'] ?? '') as $slot)
                                                                <span class="badge bg-light text-dark border p-1" style="font-size: 11px; font-weight: 500;">
                                                                    {{ $slot }}
                                                                </span>
                                                            @endforeach
                                                        </div>
                                                    </span>
                                                    @else
                                                    <span style="display:block;font-size:13px;color:#555;">
                                                        <strong>{{ ucfirst($key) }} :</strong>
                                                        {{ $attr['name'] ?? '' }}
                                                    </span>
                                                    @endif
                                                @endif
                                            @endforeach
                                        </div>
                                        @endif
                                        @endif

                                        {{-- Cancellation / Refund Policy Removed as per request --}}

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

<style>
.professional-timeline {
    position: relative;
    padding-left: 2.5rem;
    margin-top: 1.5rem;
    margin-bottom: 1.5rem;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
}
.timeline-step {
    position: relative;
    padding-bottom: 1.5rem;
}
.timeline-step:last-child {
    padding-bottom: 0;
}
/* The Connecting Line */
.timeline-step::before {
    content: '';
    position: absolute;
    top: 24px;
    bottom: -4px;
    left: -1.35rem; 
    width: 2px;
    background: #e9ecef;
    z-index: 1;
}
.timeline-step:last-child::before {
    display: none;
}
/* The Icon */
.timeline-step .step-icon {
    position: absolute;
    left: -1.75rem;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #e9ecef;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
    z-index: 2;
}
/* Status Colors */
.timeline-step.completed .step-icon {
    background: #28a745;
    box-shadow: 0 0 0 2px #28a745;
}
.timeline-step.completed::before {
    background: #28a745;
}
/* Half-completed fix - don't color line green if next step is not completed, 
   but it's easier to just let it be green and the next gray will override? No, 
   CSS doesn't work that way. We'll use PHP logic to set line color! */
.timeline-step.completed.line-gray::before {
    background: #e9ecef;
}

/* Cancelled or Returned */
.timeline-step.danger .step-icon {
    background: #dc3545;
    box-shadow: 0 0 0 2px #dc3545;
}
.timeline-step.danger::before {
    background: #e9ecef; 
}
/* Processing/Active */
.timeline-step.processing .step-icon {
    background: #007bff;
    box-shadow: 0 0 0 2px #007bff;
}
.timeline-step.neutral .step-icon {
    background: #6c757d;
    box-shadow: 0 0 0 2px #6c757d;
}

/* Text Formatting */
.step-content h6 { margin-bottom: 2px; font-weight: 600; font-size: 15px; }
.step-content p { margin-bottom: 4px; font-size: 13px; color: #6c757d; }
.step-content .step-date { font-size: 12px; color: #8e959b; font-weight: 500; }
.text-danger-custom { color: #dc3545 !important; }
.text-success-custom { color: #28a745 !important; }
.text-primary-custom { color: #007bff !important; }
.text-muted-custom { color: #6c757d !important; }
</style>

<div class="professional-timeline px-2">
    @php
        $st = strtolower($order->status);
        $isCancelled = $st === 'cancelled';
        
        $isConfirmed = true; 
        $isPacked = in_array($st, ['processing','approved','shipped','dispatched','out for delivery','out_for_delivery','delivered','completed','return_requested','return_approved','returned','replacement_requested','replacement_approved','refunded']);
        $isShipped = in_array($st, ['shipped','dispatched','out for delivery','out_for_delivery','delivered','completed','return_requested','return_approved','returned','replacement_requested','replacement_approved','refunded']);
        $isDelivered = in_array($st, ['delivered','completed','return_requested','return_approved','returned','replacement_requested','replacement_approved','refunded']);
        
        $isReturnReq = in_array($st, ['return_requested','return_approved','returned','refunded']);
        $isReturned = in_array($st, ['returned','refunded']);
    @endphp

    {{-- Confirmed --}}
    <div class="timeline-step completed {{ !$isPacked && !$isCancelled ? 'line-gray' : '' }}">
        <div class="step-icon"></div>
        <div class="step-content">
            <h6 class="text-success-custom">Order Confirmed</h6>
            <p>Your order has been placed.</p>
            <span class="step-date">{{ $order->created_at->format('D, d M Y - h:i A') }}</span>
        </div>
    </div>

    @if($isCancelled)
    <div class="timeline-step danger">
        <div class="step-icon"></div>
        <div class="step-content">
            <h6 class="text-danger-custom">Cancelled</h6>
            <p>Your order has been cancelled.</p>
            <span class="step-date">{{ $order->updated_at->format('D, d M Y - h:i A') }}</span>
        </div>
    </div>
    @else
        {{-- Packed --}}
        <div class="timeline-step {{ $isPacked ? 'completed' : 'processing' }} {{ $isPacked && !$isShipped ? 'line-gray' : '' }}">
            <div class="step-icon"></div>
            <div class="step-content">
                <h6 class="{{ $isPacked ? 'text-success-custom' : 'text-primary-custom' }}">Packed</h6>
                <p>Seller has processed your order.</p>
                @if($isPacked)
                <span class="step-date">{{ $order->updated_at->format('D, d M Y - h:i A') }}</span>
                @endif
            </div>
        </div>

        {{-- Shipped --}}
        <div class="timeline-step {{ $isShipped ? 'completed' : ($isPacked ? 'processing' : '') }} {{ $isShipped && !$isDelivered ? 'line-gray' : '' }}">
            <div class="step-icon"></div>
            <div class="step-content">
                <h6 class="{{ $isShipped ? 'text-success-custom' : ($isPacked ? 'text-primary-custom' : 'text-muted-custom') }}">Shipped</h6>
                <p>Your item has been picked up by courier partner.</p>
                @if($isShipped && $order->shipped_at)
                <span class="step-date">{{ $order->shipped_at->format('D, d M Y - h:i A') }}</span>
                @endif
            </div>
        </div>

        {{-- Delivered --}}
        <div class="timeline-step {{ $isDelivered ? 'completed' : ($isShipped ? 'processing' : '') }} {{ $isDelivered && !$isReturnReq ? 'line-gray' : '' }}">
            <div class="step-icon"></div>
            <div class="step-content">
                <h6 class="{{ $isDelivered ? 'text-success-custom' : ($isShipped ? 'text-primary-custom' : 'text-muted-custom') }}">Delivered</h6>
                <p>Package was handed to resident.</p>
                @if($isDelivered && $order->delivered_at)
                <span class="step-date">{{ $order->delivered_at->format('D, d M Y - h:i A') }}</span>
                @endif
            </div>
        </div>
        
        {{-- Return / Refund --}}
        @if($isReturnReq)
        <div class="timeline-step danger">
            <div class="step-icon"></div>
            <div class="step-content">
                <h6 class="text-danger-custom">{{ $isReturned ? 'Refunded / Returned' : 'Return Requested' }}</h6>
                <p>{{ $isReturned ? 'Item has been returned and refund initiated.' : 'Return request has been placed.' }}</p>
                <span class="step-date">{{ $order->updated_at->format('D, d M Y - h:i A') }}</span>
            </div>
        </div>
        @endif
    @endif
    
    {{-- Detailed Activity Logs --}}
    @if($order->activityLogs && $order->activityLogs->count() > 0)
    <div class="mt-4 pt-4 border-top">
        <h6 class="fw-bold mb-4" style="font-size: 14px; color: #495057;">Detailed Activity Updates</h6>
        @foreach($order->activityLogs as $log)
        <div class="timeline-step neutral pb-3" style="padding-left: 0.5rem;">
            <div class="step-icon" style="width:12px; height:12px; left:-1.15rem; top:6px;"></div>
            <div class="step-content">
                <h6 style="font-size: 13px; margin-bottom:2px; color: #495057;">{{ $log->description }}</h6>
                @if($log->remarks)
                <p style="font-size: 12px; margin-bottom:2px; color: #6c757d;">{{ $log->remarks }}</p>
                @endif
                <span class="step-date" style="font-size: 11px;">{{ $log->created_at->format('D, d M Y - h:i A') }}</span>
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

            <form id="orderActionForm" enctype="multipart/form-data">
                @csrf

                <input type="hidden" name="order_id" id="modal_order_id">
                <input type="hidden" name="type" id="modal_action_type">

                {{-- BODY --}}
                <div class="modal-body pt-2">

                    {{-- REASON --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Select Reason</label>
                        <div class="list-group">
                            <label class="list-group-item d-flex gap-2">
                                <input class="form-check-input flex-shrink-0 reason-radio" type="radio" name="reason" value="Changed mind">
                                <span>Changed mind</span>
                            </label>
                            <label class="list-group-item d-flex gap-2">
                                <input class="form-check-input flex-shrink-0 reason-radio" type="radio" name="reason" value="Wrong item ordered">
                                <span>Wrong item ordered</span>
                            </label>
                            <label class="list-group-item d-flex gap-2">
                                <input class="form-check-input flex-shrink-0 reason-radio" type="radio" name="reason" value="Found cheaper elsewhere">
                                <span>Found cheaper elsewhere</span>
                            </label>
                            <label class="list-group-item d-flex gap-2">
                                <input class="form-check-input flex-shrink-0 reason-radio" type="radio" name="reason" value="Product damaged">
                                <span>Product damaged</span>
                            </label>
                            <label class="list-group-item d-flex gap-2">
                                <input class="form-check-input flex-shrink-0 reason-radio" type="radio" name="reason" value="Other">
                                <span>Other</span>
                            </label>
                        </div>
                    </div>

                    {{-- FILE UPLOAD (Hidden by default, shown for Damaged) --}}
                    <div class="mb-3 d-none" id="attachmentWrapper">
                        <label class="form-label fw-semibold">Attach Image/File</label>
                        <input type="file" name="attachment" class="form-control shadow-sm" accept="image/*,application/pdf">
                        <small class="text-muted">Please attach proof (image/pdf) of the damaged product.</small>
                    </div>

                    {{-- NOTE (Hidden by default, shown for Other) --}}
                    <div class="mb-2 d-none" id="noteWrapper">
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
.order-list-wrapper {
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    transition: box-shadow 0.3s ease;
}

.order-list-wrapper:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.order-list-box {
    flex: 1;
}

.order-list-box:nth-child(2) {
    text-align: center;
}

.order-list-box:nth-child(3) {
    text-align: right;
}

.order-list-box h3 a {
    color: #333;
    text-decoration: none;
    font-weight: 600;
    font-size: 16px;
}

.order-badge {
    padding: 4px 10px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
}
.order-badge.delivered { background: #d4edda; color: #155724; }
.order-badge.dispatched { background: #cce5ff; color: #004085; }
.order-badge.pending { background: #fff3cd; color: #856404; }
.order-badge.cancelled { background: #f8d7da; color: #721c24; }
.order-badge.return { background: #f8d7da; color: #721c24; }
/* SMALL BUTTONS FIX */
.invoice-btn,
.cancelOrderBtn,
.returnOrderBtn {
    height: 32px;
    padding: 0 12px;
    font-size: 13px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;
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

    // REASON RADIO CHANGE
    $('.reason-radio').change(function() {
        const val = $(this).val();
        if (val === 'Other') {
            $('#noteWrapper').removeClass('d-none');
            $('#attachmentWrapper').addClass('d-none');
        } else if (val === 'Product damaged') {
            $('#noteWrapper').addClass('d-none');
            $('#attachmentWrapper').removeClass('d-none');
        } else {
            $('#noteWrapper').addClass('d-none');
            $('#attachmentWrapper').addClass('d-none');
        }
    });

    // SUBMIT FORM (AJAX with FormData)
    $('.ajax-action-form').on('submit', function(e){
        e.preventDefault();
        
        let formData = new FormData(this);

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
            data: formData,
            contentType: false,
            processData: false,
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
        var clickedRating = parseInt($(this).attr('data-val'));
        
        // Update the visual stars
        $(this).parent().find('i').each(function() {
            var currentVal = parseInt($(this).attr('data-val'));
            if(currentVal <= clickedRating) {
                $(this).removeClass('fa-regular text-muted').addClass('fa-solid text-warning');
            } else {
                $(this).removeClass('fa-solid text-warning').addClass('fa-regular text-muted');
            }
        });
        
        // Store rating value in the hidden input of the closest form
        $(this).closest('form').find('.rating-value').val(clickedRating);
    });
});

function showCancelNotPossible() {
    Swal.fire({
        icon: 'error',
        title: 'Cancellation Not Available',
        text: 'Order cancellation is not available as your order is already out for delivery.'
    });
}

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
