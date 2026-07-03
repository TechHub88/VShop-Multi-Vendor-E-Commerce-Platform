@extends('frontend.layout')

@section('content')

<main>
    <section class="bedcum-sec" style="padding-top: 120px;">
        <div class="container">
            <div class="bedcum-inner">  
                <div class="bed-cum-list">
                    <ul>
                        <li><a href="#">Shop</a></li>
                        <li><i class="fa-regular fa-angle-right"></i></li>
                        <li><span>Order List</span></li>
                        <li><i class="fa-regular fa-angle-right"></i></li>
                        <li><span>Order no : {{ $order->id }}</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <section class="order-place-sec">
        <div class="container">
            <div class="order-place-inner">
                <!-- TOP SECTION -->
                <div class="dispatch-top">
                    <div class="order-list-wrapper">
                        <div class="order-list-box">
                            <h3><a href="#">Order no : {{ $order->id }}</a></h3>
                            @php
                                $orderTotalItems = 0;
                                $orderTotalQuantity = 0;
                                foreach($order->items as $orderItem) {
                                    if($orderItem->combo_id) {
                                        $orderTotalItems++;
                                        $orderTotalQuantity += ($orderItem->main_product_quantity ?? 1) + ($orderItem->combo_product_quantity ?? 1);
                                    } else {
                                        $orderTotalItems++;
                                        $orderTotalQuantity += $orderItem->quantity ?? 1;
                                    }
                                }
                            @endphp
                            <div class="total-order-sec">
                                <p>Total Items : {{ $orderTotalItems }}</p>
                                <p>Total Quantity : {{ $orderTotalQuantity }}</p>
                            </div>
                        </div>
                        <div class="order-list-box">
                            <div class="total-time-sec">
                                @if(!empty($order->created_at))
                                    <div class="order-time-box">
                                        <div class="order-time-text total-time-sec">
                                            <p class="order-time">{{ \Carbon\Carbon::parse($order->created_at)->format('h:i A') }}</p>
                                            <p class="order-date">{{ \Carbon\Carbon::parse($order->created_at)->format('D, d M Y') }}</p>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-muted">Order time not available</p>
                                @endif
                            </div>

                            <p class="order-status">
                                Order Status : 
                                <span class="badge rounded-pill {{ $order->status == 'completed' ? 'grenColor' : 'yellowColor' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </p>
                        </div>
                    <div class="order-list-box">
    <div class="total-time-sec justify-content-end">
        <div class="estimated-delivery-box">
            <p class="label-text">
                Estimated Delivery:
                <span class="ms-2 estimated-date {{ $order->end_date ? 'delivered' : 'pending' }} grenColor">
                    @if(!empty($order->end_date))
                        {{ \Carbon\Carbon::parse($order->end_date)->format('D, d M Y \a\t h:i A') }}
                    @else
                        -
                    @endif
                </span>
            </p>
        </div>
    </div>
	<div class="text-end">
    <p class="d-inline-block grandtotal mb-0">
        Grand Total : <span class="blueColor">₹{{ number_format($order->total_amount, 2) }}</span>
    </p>
							<!-- ACTION BUTTONS -->
                            <div class="order-action-buttons d-inline-flex justify-content-center gap-3 flex-wrap mt-0">
                                @if(in_array($order->status, ['Completed']))
                                    <button class="return-btn order-action-button" onclick="openReturnModal()" style="    font-size: 14px;
    line-height: 18px;
    padding: 6px 10px;
    height: auto;">
                                        <i class="fa-solid fa-rotate-left me-2"></i> Return / Refund
                                    </button>
                                @endif

                                @if(in_array($order->status, ['Confirmed', 'Processing', 'pending', 'Out For Delivery']))
                                    <button class="cancel-order-btn order-action-button" onclick="openCancelModal()" style="    font-size: 14px;
    line-height: 18px;
    padding: 6px 10px;
    height: auto;">
                                        <i class="fa-solid fa-ban me-2"></i> Cancel Order
                                    </button>
                                @endif
                            </div>
							</div>

</div>

                    </div>
                </div>

                <!-- CENTER SECTION -->
                <div class="dispatch-center">
                    <div class="diapatch-center-box">
                        <p>Payment Method : {{ ucfirst($order->payment_method) }}</p>
                    </div>
                    <div class="diapatch-center-box">
                        <p>Payment Receipt no : {{ $order->payment_receipt_no ?? 'N/A' }}</p>
                    </div>
                    <div class="diapatch-center-box">
                        @if($order->coupon_code)
                        <p class="coupon-applied">
                            <i class="fa-solid fa-tag me-1"></i>
                            Coupon Applied: <strong>{{ $order->coupon_code }}</strong>
                        </p>
                        @endif
                    </div>
                    <div class="diapatch-center-box">
                        <div class="dispatch-right">
							
						
                            <button class="order-btn">
                                Track Order <i class="fa-regular fa-truck-fast grenColor"></i>
                            </button>
                            <a href="{{ route('frontend.order.invoice', $order->id) }}" class="add-tocart-btn invoice-btn">
                                Download Invoice
                            </a>
							
							 
							
							
							
							
                        </div>
                    </div>
                </div>

                <!-- ORDER ITEMS -->
                <div class="dispatch-bottom">
                    <div class="cart-details-inner">
                        <ul>
                        @foreach($order->items as $item)
                        @php
                            $mainProductAttributes = $item->main_product_attributes ? json_decode($item->main_product_attributes, true) : [];
                            $comboProductAttributes = $item->combo_product_attributes ? json_decode($item->combo_product_attributes, true) : [];
                            
                            $comboProduct = null;
                            if ($item->combo_id) {
                                $comboProduct = \App\Models\Product::find($item->combo_id);
                            }
                            
                            if($item->combo_id && $comboProduct) {
                                $mainProductPrice = $item->main_product_price ?? 0;
                                $mainProductQuantity = $item->main_product_quantity ?? 1;
                                $comboProductPrice = $item->combo_product_price ?? 0;
                                $comboProductQuantity = $item->combo_product_quantity ?? 1;
                                
                                $mainProductTotal = $mainProductPrice * $mainProductQuantity;
                                $comboProductTotal = $comboProductPrice * $comboProductQuantity;
                                $totalUnitPrice = $mainProductTotal + $comboProductTotal;
                                $gstPercent = $item->gst_percent ?? 5;
                                $gstAmount = $totalUnitPrice * ($gstPercent / 100);
                                $courierCharge = $item->courier_charge ?? 0;
                                $subtotal = $totalUnitPrice + $gstAmount + $courierCharge;
                            } else {
                                $unitPrice = $item->price ?? 0;
                                $quantity = $item->quantity ?? 1;
                                $totalUnitPrice = $unitPrice * $quantity;
                                $gstPercent = $item->gst_percent ?? 5;
                                $gstAmount = $totalUnitPrice * ($gstPercent / 100);
                                $courierCharge = $item->courier_charge ?? 0;
                                $subtotal = $totalUnitPrice + $gstAmount + $courierCharge;
                            }
                        @endphp
                            <li class="cart-details-list-box" data-id="{{ $item->id }}" data-combo-id="{{ $item->combo_id }}" data-is-combo="{{ $item->combo_id ? 'true' : 'false' }}">
                                <div class="d-block cart-details-list">
                                    <div class="d-block cart-details-list-left">
                                        <div class="main-product-display">
                                            <div class="cart-img mb-2">
                                                <a href="{{ route('frontend.productdetails', $item->product->sku ?? $item->product_id) }}">
                                                    <img src="{{ asset($item->product->thumbnail ?? 'images/default-product.png') }}"
                                                        class="img-fluid"
                                                        alt="{{ $item->product?->name ?? 'No Image' }}"
                                                        style="width: 100%; height: 100%; object-fit:contain; border-radius:6px;">
                                                </a>
                                            </div>
                                            
                                            <div class="cart-iteam-right-box">
                                                <div class="d-flex justify-content-between cart-info-box">
                                                    <div class="w-100 leftSide">
                                                        <h3>{{ $item->product?->name ?? 'Product not found' }}</h3>
                                                        
                                                        <p class="mb-0 mt-0 categoryText">
                                                            Category / Brand &gt; 
                                                            <span class="blackColor">
                                                                {{ $item->product?->category?->name ?? '-' }} 
                                                            </span>
                                                        </p>

                                                        @php
                                                            $mainColor = null;
                                                            $mainSize = null;
                                                            $mainFabric = null;
                                                            
                                                            if (!empty($mainProductAttributes)) {
                                                                foreach ($mainProductAttributes as $attr) {
                                                                    $slug = $attr['attribute_slug'] ?? '';
                                                                    if (in_array($slug, ['colour', 'color'])) {
                                                                        $mainColor = $attr['term_name'] ?? null;
                                                                    } elseif ($slug === 'size') {
                                                                        $mainSize = $attr['term_name'] ?? null;
                                                                    } elseif (in_array($slug, ['febric', 'fabric'])) {
                                                                        $mainFabric = $attr['term_name'] ?? null;
                                                                    }
                                                                }
                                                            }
                                                            
                                                            if (!$mainColor && $item->color) {
                                                                $mainColor = $item->color->term;
                                                            }
                                                            if (!$mainSize && $item->size) {
                                                                $mainSize = $item->size->term;
                                                            }
                                                        @endphp
                                                        
                                                        @if($mainColor)
                                                        <div class="color-bottom">
                                                            <div class="color-right ms-0">
                                                                <p class="mt-0 pb-0 d-inline-flex colorMention">
                                                                    <span class="ttl">Color Name: </span>
                                                                    <span class="blackColor" id="selectedColorName_{{ $item->id }}">
                                                                        <span class="colorIcon {{ $mainColor }}"> </span>
                                                                        <span class="colorText {{ $mainColor }}"> {{ $mainColor }}</span>
                                                                    </span>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        @endif

                                                        <div class="size-delite-sec d-flex align-items-center mt-0">
                                                            @if($mainSize)
                                                            <div class="proSize"><strong>Size</strong> : {{ $mainSize }}</div>
                                                            @endif
                                                            
                                                            @if($mainFabric)
                                                            <div class="proSize ms-2"><strong>Fabric</strong> : {{ $mainFabric }}</div>
                                                            @endif
                                                            
                                                            @if(isset($mainProductAttributes['Booking Slots']))
                                                            <div class="proSize mt-1"><strong>Booking Slots</strong> : {{ $mainProductAttributes['Booking Slots']['name'] ?? '' }}</div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="rightSide">
                                                    </div>
                                                </div>

                                                @if($item->combo_id && $comboProduct)
                                                <div class="combo-product-display mt-4" style="margin-left: 0; padding-left: 0;">
                                                    <div class="mb-2">
                                                        <span class="badge bg-warning text-dark" style="font-size: 12px; padding: 6px 12px; font-weight: 600; border-radius: 4px;">COMBO PACKAGE</span>
                                                    </div>
                                                    
                                                    <div class="combo-product-card" style="background-color: #f5f5f5; border-radius: 8px; padding: 15px; margin-top: 10px;">
                                                        <div class="d-flex align-items-center" style="gap: 15px;">
                                                            <div class="combo-img-wrapper" style="flex-shrink: 0;">
                                                                <a href="{{ route('frontend.productdetails', $comboProduct->sku ?? $comboProduct->id) }}">
                                                                    <img src="{{ $comboProduct->thumbnail ? asset($comboProduct->thumbnail) : asset('images/default-product.png') }}"
                                                                        class="img-fluid rounded-circle"
                                                                        alt="{{ $comboProduct->name ?? 'No Image' }}"
                                                                        style="width: 70px; height: 70px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                                                </a>
                                                            </div>
                                                            
                                                            <div class="combo-product-details flex-grow-1">
                                                                <h6 class="mb-1" style="font-size: 14px; font-weight: 600; color: #000; margin-bottom: 5px !important;">
                                                                    {{ $comboProduct->name ?? 'Combo Product' }}
                                                                </h6>
                                                                
                                                                <div class="combo-product-price mb-2">
                                                                    <span class="text-dark fw-bold" style="font-size: 16px; color: #000;">₹{{ number_format($item->combo_product_price ?? 0, 2) }}</span>
                                                                </div>
                                                                
                                                                @php
                                                                    $comboColor = null;
                                                                    $comboSize = null;
                                                                    $comboFabric = null;
                                                                    
                                                                    if (!empty($comboProductAttributes)) {
                                                                        foreach ($comboProductAttributes as $attr) {
                                                                            $slug = $attr['attribute_slug'] ?? '';
                                                                            if (in_array($slug, ['colour', 'color'])) {
                                                                                $comboColor = $attr['term_name'] ?? null;
                                                                            } elseif ($slug === 'size') {
                                                                                $comboSize = $attr['term_name'] ?? null;
                                                                            } elseif (in_array($slug, ['febric', 'fabric'])) {
                                                                                $comboFabric = $attr['term_name'] ?? null;
                                                                            }
                                                                        }
                                                                    }
                                                                @endphp
                                                                
                                                                <div class="combo-attributes mb-2" style="font-size: 12px; color: #666;">
                                                                    @php
                                                                        $attrParts = [];
                                                                        if($comboColor) {
                                                                            $attrParts[] = 'Color: <strong style="color: #000;">' . $comboColor . '</strong>';
                                                                        }
                                                                        if($comboFabric) {
                                                                            $attrParts[] = 'Fabric: <strong style="color: #000;">' . $comboFabric . '</strong>';
                                                                        }
                                                                        if($comboSize) {
                                                                            $attrParts[] = 'Size: <strong style="color: #000;">' . $comboSize . '</strong>';
                                                                        }
                                                                    @endphp
                                                                    <span>{!! implode(' | ', $attrParts) !!}</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                            </div>
                                            
                                            @if($item->courier_charge)
                                            <div class="cart-delivery-sec">
                                                <p class="mb-0">Expected Delivery : <span class="blackColor">{{ $item->expected_delivery ?? 'N/A' }}</span></p>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="cart-price-sec">
                                    <div class="cart-peice-box">
                                        <ul>
                                            @if($item->combo_id && $comboProduct)
                                                <li>
                                                    <p>Price</p>
                                                    <span class="rupess">₹{{ number_format($mainProductPrice, 2) }}</span>
                                                </li>
                                                <li>
                                                    <p>Quantity</p>
                                                    <span>X {{ $mainProductQuantity }}</span>
                                                </li>
                                                <li>
                                                    <p>Combo Price</p>
                                                    <span class="rupess">₹{{ number_format($comboProductPrice, 2) }}</span>
                                                </li>
                                                <li>
                                                    <p>Combo Quantity</p>
                                                    <span>X {{ $comboProductQuantity }}</span>
                                                </li>
                                                <li class="justify-content-end mb-0">
                                                    <p class="rupess">₹{{ number_format($totalUnitPrice, 2) }}</p>
                                                </li>
                                            @else
                                                <li>
                                                    <p>Price</p>
                                                    <span class="rupess">₹{{ number_format($item->price, 2) }}</span>
                                                </li>
                                                <li>
                                                    <p>Quantity</p>
                                                    <span>X {{ $item->quantity }}</span>
                                                </li>
                                                <li class="justify-content-end mb-0">
                                                    <p class="rupess">₹{{ number_format($totalUnitPrice, 2) }}</p>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="cart-peice-box border0">
                                        <ul>
                                            <li>
                                                <p>GST {{ $gstPercent }}%</p>
                                                <span>+₹ {{ number_format($gstAmount, 2) }}</span>
                                            </li>
                                            @if($courierCharge > 0)
                                            <li>
                                                <p>Courier Charge</p>
                                                <span>+₹ {{ number_format($courierCharge, 2) }}</span>
                                            </li>
                                            @endif
                                             
                                            <li class="mb-0">
                                                <p class="subColor">Sub</p>
                                                <span class="rupess">₹{{ number_format($subtotal, 2) }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                        </ul>

                        
                    </div>
                </div>

                <!-- ORDER ACTIVITY -->
                <section class="order-activity-sec py-3">
                    <div class="container">
                        <div class="order-activity-inner">
                            <h5 class="mb-2">Order Activity</h5>

                            <div class="order-tracker">
                                <div class="progress-line"></div>

                                <!-- ORDER CONFIRMED -->
                                <div class="step {{ in_array($order->status, ['Approved']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fa-solid fa-clipboard-check"></i></div>
                                    <p class="step-title">Order Confirmed</p>
                                    <p class="step-date">
                                        @if($order->start_date)
                                            Confirmed on {{ \Carbon\Carbon::parse($order->created_at)->format('D, d M Y \a\t h:i A') }}
                                        @else
                                            Created on {{ $order->created_at->format('D, d M Y \a\t h:i A') }}
                                        @endif
                                    </p>
                                </div>

                                <!-- SHIPPED -->
                                <div class="step {{ in_array($order->status, ['Shipped','Processing']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fa-solid fa-box"></i></div>
                                    <p class="step-title">Shipped</p>
                                    <p class="step-date">
                                        @if($order->start_date)
                                            Shipped on {{ \Carbon\Carbon::parse($order->start_date)->format('D, d M Y \a\t h:i A') }}
                                        @elseif(in_array($order->status, ['Shipped','Processing']))
                                            Updated on {{ $order->created_at->format('D, d M Y \a\t h:i A') }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>

                                <!-- OUT FOR DELIVERY -->
                                <div class="step {{ in_array($order->status, ['Out For Delivery','Delivered']) ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fa-solid fa-truck"></i></div>
                                    <p class="step-title">Out for Delivery</p>
                                    <p class="step-date">
                                        @if($order->start_date)
                                            Out for delivery on {{ \Carbon\Carbon::parse($order->start_date)->format('D, d M Y \a\t h:i A') }}
                                        @elseif(in_array($order->status, ['Out For Delivery','Delivered']))
                                            Updated on {{ $order->start_date->format('D, d M Y \a\t h:i A') }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>

                                <!-- DELIVERED -->
                                <div class="step {{ $order->status == 'Completed' ? 'active' : '' }}">
                                    <div class="step-icon"><i class="fa-solid fa-circle-check"></i></div>
                                    <p class="step-title">Delivered</p>
                                    <p class="step-date">
                                        @if($order->end_date)
                                            Delivered on {{ \Carbon\Carbon::parse($order->end_date)->format('D, d M Y \a\t h:i A') }}
                                        @elseif($order->status == 'Delivered')
                                            Updated on {{ $order->updated_at->format('D, d M Y \a\t h:i A') }}
                                        @else
                                            -
                                        @endif
                                    </p>
                                </div>
                            </div>

                           
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </section>
</main>

<!-- 🔹 Cancel Order Modal -->
<div id="cancelModal" class="return-modal">
    <div class="return-modal-content" 
         style="display:flex; flex-direction:column; align-items:center;">

        <span class="close-modal" onclick="closeCancelModal()" 
              style="position:absolute; top:10px; right:15px; cursor:pointer;">
            &times;
        </span>

        <h4 class="mb-3" 
            style="text-align:center; width:100%; margin-top:20px;">
            Cancel Order
        </h4>

        <p style="text-align:center; width:100%;">
            Your request will be solved in 48 hours
        </p>

        <p class="text-muted" style="align-self:flex-start; margin-top:10px;">
            Choose a Reason for Order Cancellation:
        </p>

        <form id="cancelForm" method="POST" 
              action="{{ route('frontend.order.cancel', $order->id) }}"
              style="width:100%;">
            @csrf

            <div class="reason-list" style="display:flex; flex-direction:column; gap:6px;">
                <label><input type="radio" name="reason" value="Ordered by mistake" required> Ordered by mistake</label>
                <label><input type="radio" name="reason" value="Found a better price elsewhere"> Found a better price elsewhere</label>
                <label><input type="radio" name="reason" value="Delivery time too long"> Delivery time too long</label>
                <label><input type="radio" name="reason" value="Payment issue"> Payment issue</label>
                <label><input type="radio" name="reason" value="Other"> Other</label>
            </div>

            <div class="form-group mt-3">
                <label for="cancel_note" class="form-label">Leave a comment</label>
                <textarea name="note" id="cancel_note" class="form-control" rows="3" placeholder="Add any comments..."></textarea>
            </div>

            <div class="return-buttons mt-4" 
                 style="display:flex; justify-content:space-between;">
                <button type="button" class="cancel-btn" onclick="closeCancelModal()">Close</button>
                <button type="submit" class="return-btn">Submit Cancellation</button>
            </div>
        </form>
    </div>
</div>

<!-- ✅ Return / Refund Modal -->
<div id="returnModal" class="return-modal">
    <div class="return-modal-content">
        <span class="close-modal" onclick="closeReturnModal()">&times;</span>
        <h4 class="mb-3">Return / Refund Request</h4>

        <p class="text-muted">Please tell us why you want to return this order:</p>

        <form action="{{ route('frontend.order.return', $order->id) }}" method="POST">
            @csrf
            <div class="reason-list">
                <label><input type="radio" name="reason" value="Received damaged product" required> Received damaged product</label>
                <label><input type="radio" name="reason" value="Wrong item delivered"> Wrong item delivered</label>
                <label><input type="radio" name="reason" value="Product quality not as expected"> Product quality not as expected</label>
                <label><input type="radio" name="reason" value="Size/Fit issue"> Size / Fit issue</label>
                <label><input type="radio" name="reason" value="Other"> Other</label>
            </div>

            <div class="form-group mt-3">
                <label for="note" class="form-label">Additional Note (optional)</label>
                <textarea name="note" id="note" class="form-control" rows="3" placeholder="Add any comments..."></textarea>
            </div>

            <div class="return-buttons mt-4">
                <button type="button" class="cancel-btn" onclick="closeReturnModal()">Cancel</button>
                <button type="submit" class="return-btn">Submit Request</button>
            </div>
        </form>
    </div>
</div>

{{-- 🔹 Styles and Scripts --}}
<style>
    /* Order Activity Tracker Horizontal */
    .order-tracker {
        display: flex;
        justify-content: space-between;
        position: relative;
        padding: 5px 0;
        flex-wrap: wrap;
    }

    .order-tracker .progress-line {
        position: absolute;
        top: 25px !important;
        left: 0;
        width: 100%;
        height: 4px;
        background: #e0e0e0;
        z-index: 1;
    }

    .order-tracker .step {
        position: relative;
        text-align: center;
        width: 25%;
        z-index: 2;
        padding-top: 0px;
    }

    .order-tracker .step.active .step-icon {
        background: #2874f0;
        color: #fff;
        border-color: #2874f0;
    }

    .order-tracker .step-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #ccc;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 5px;
        font-size: 18px;
        background: #fff;
        color: #ccc;
        transition: all 0.3s ease;
    }

    .order-tracker .step-title {
        font-weight: 600;
        font-size: 14px;
        line-height: 20px;
        margin-bottom: 0px;
    }

    .order-tracker .step-date {
        font-size: 12px;
        line-height: 18px;
        color: #777;
        margin-bottom: 0px;
    }

    /* Order Summary Styles */
    .address-right {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
        border: 1px solid #e9ecef;
    }

    .address-right-top h3 {
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 15px;
        color: #333;
        padding-bottom: 10px;
        border-bottom: 1px solid #ddd;
    }

    .address-right-box {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }

    .address-right-box.border0 {
        border-bottom: none;
        padding-bottom: 0;
        margin-bottom: 0;
    }

    .address-right-box h4 {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 5px;
    }

    .address-right-box ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .address-right-box ul li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
        font-size: 14px;
    }

    .address-right-box ul li p {
        margin: 0;
        color: #555;
    }

    .address-right-box ul li span {
        color: #333;
        font-weight: 500;
    }

    .address-right-box ul li.text-success p,
    .address-right-box ul li.text-success span {
        color: #28a745 !important;
        font-weight: 600;
    }

    .address-right-box ul li .total-text {
        font-size: 18px;
        color: #2874f0;
    }

    /* Coupon Applied Styling */
    .coupon-applied {
        background: #d4edda;
        color: #155724;
        padding: 8px 15px;
        border-radius: 6px;
        border: 1px solid #c3e6cb;
        font-size: 14px;
        display: inline-flex;
        align-items: center;
        margin: 0;
    }

    .coupon-applied i {
        color: #28a745;
    }

    /* Modal Styles */
    .return-modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0,0,0,0.55);
        backdrop-filter: blur(2px);
        justify-content: center;
        align-items: center;
    }

    .return-modal-content {
        background: #fff;
        padding: 25px 30px;
        border-radius: 10px;
        width: 100%;
        max-width: 500px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.2);
        position: relative;
        animation: popUp 0.3s ease;
    }

    @keyframes popUp {
        from { transform: scale(0.8); opacity: 0; }
        to { transform: scale(1); opacity: 1; }
    }

    .close-modal {
        position: absolute;
        top: 10px;
        right: 15px;
        font-size: 22px;
        cursor: pointer;
        color: #333;
    }

    .reason-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-top: 15px;
    }

    .reason-list label {
        background: #f8f9fa;
        padding: 10px 15px;
        border-radius: 6px;
        border: 1px solid #ddd;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 15px;
    }

    .reason-list label:hover {
        background: #eef3ff;
        border-color: #2874f0;
    }

    .reason-list input { margin-right: 10px; }

    .return-buttons {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        flex-wrap: wrap;
    }

    .return-buttons .cancel-btn {
        background: #f8f9fa;
        color: #25262B;
        padding: 10px 24px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .return-buttons .cancel-btn:hover { 
        background: #e9ecef;
        border-color: #d0d7de;
        color: #25262B;
        transform: translateY(-1px);
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .return-buttons .return-btn {
        background: #2874f0;
        color: #fff;
        padding: 10px 24px;
        border: 1px solid #2874f0;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .return-buttons .return-btn:hover {
        background: #0b5ed7;
        border-color: #0b5ed7;
        transform: translateY(-1px);
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.15);
    }

    /* Order Action Buttons */
    .order-action-buttons {
        display: flex;
        justify-content: center;
        gap: 15px;
        flex-wrap: wrap;
        margin-top: 20px;
    }

    .order-action-button {
        font-size: 16px;
        padding: 12px 24px;
        display: inline-flex;
        align-items: center;
        text-transform: uppercase;
        color: #fff;
        border: none;
        border-radius: 8px;
        height: 45px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0px 2px 4px rgba(0, 0, 0, 0.1);
    }

    .order-action-button:hover {
        transform: translateY(-2px);
        box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.15);
    }

    .return-btn {
        background-color: #2874f0;
    }

    .cancel-order-btn {
        background-color: #dc3545;
    }

    @media (max-width: 768px) {
        .order-tracker {
            flex-direction: column;
            align-items: flex-start;
        }
        .order-tracker .step {
            width: 100%;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            text-align: left;
        }
        .order-tracker .step-title {
            margin-left: 15px;
        }
        .order-tracker .step-date {
            margin-left: 15px;
        }
        .order-tracker .progress-line {
            display: none;
        }
        
        .order-action-buttons {
            flex-direction: column;
            width: 100%;
        }
        
        .order-action-button {
            width: 100%;
            max-width: 300px;
        }
    }
    .cancel-order-small-btn,
.return-order-small-btn {
    background: #000;
    color: #fff;
    border: none;
    padding: 6px 14px;
    font-size: 13px;
    border-radius: 4px;
    display: inline-flex;
    align-items: center;
    margin-top: 5px;
    cursor: pointer;
}

.cancel-order-small-btn i,
.return-order-small-btn i {
    font-size: 12px;
}

.cancel-order-small-btn:hover,
.return-order-small-btn:hover {
    background: #333;
}

</style>

<script>
function openReturnModal() {
    document.getElementById('returnModal').style.display = 'flex';
}

function closeReturnModal() {
    document.getElementById('returnModal').style.display = 'none';
}

function openCancelModal() {
    document.getElementById('cancelModal').style.display = 'flex';
}

function closeCancelModal() {
    document.getElementById('cancelModal').style.display = 'none';
}

window.onclick = function(event) {
    const cancelModal = document.getElementById('cancelModal');
    const returnModal = document.getElementById('returnModal');
    if (event.target === cancelModal) closeCancelModal();
    if (event.target === returnModal) closeReturnModal();
};
</script>

@endsection