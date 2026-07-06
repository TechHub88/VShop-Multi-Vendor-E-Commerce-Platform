@extends('vtravel.main')

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
                            <li><a href="{{ route('vtravel.order.list') }}">Orders</a> /</li>
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
                                    <p><i class="fa-solid fa-clock me-1"></i>{{ $order->created_at->format('h:i A') }}</p>
                                    <p><i class="fa-solid fa-calendar me-1"></i>{{ $order->created_at->format('d-m-Y') }}</p>
                                </div>
                                <p class="order-status">
                                    Order Status : 
                                    <span class="badge {{ $statusClasses[$order->status] ?? 'badge-secondary' }} p-2">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="order-list-box">
                                <div class="total-time-sec justify-content-end">
                                    <p class="blackColor">Estimated Delivery Date :</p>
                                    <p class="grenColor fw-bold">
                                        {{ $order->estimated_delivery_date ? $order->estimated_delivery_date->format('d-m-Y') : 'N/A' }}
                                    </p>
                                </div>
                                <p class="grandtotal">
                                    Grand Total : <span class="blueColor fw-bold">₹ {{ number_format($order->total_amount, 2) }}</span>
                                </p>
                            </div>
                        </div>

                        {{-- ================= PAYMENT DETAILS ================= --}}
                        <div class="payment-method-sec">
                            <div class="row">
                                <div class="col-md-4">
                                    <p><strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}</p>
                                </div>
                                <div class="col-md-4">
                                    <p><strong>Payment Status:</strong> 
                                        <span class="badge  text-black" {{ $paymentStatusClasses[$order->payment_status] ?? 'badge-secondary' }}">
                                            {{ ucfirst($order->payment_status) }}
                                        </span>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <button class="invoice-btn" onclick="downloadInvoice({{ $order->id }})">
                                        <i class="fa-solid fa-file-pdf me-2"></i>Download Invoice
                                    </button>
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
                                                    @if($item->price > 0)
                                                    <li class="blackColor">
                                                        <p class="old-rupess">₹ {{ number_format($item->price * 1.14, 2) }}</p>
                                                    </li>
                                                    <li>
                                                        <p class="off-box">14% Off</p>
                                                    </li>
                                                    @endif
                                                    <li>
                                                        <p class="new-rupess">₹ {{ number_format($item->price, 2) }}</p>
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
                                        {{-- Delivery Address --}}
<div class="product-dtl-sec mt-3">
    <p>Delivery to : 
        <a href="#" class="track-order-link" data-order="{{ $order->id }}">
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
                                            <span class="ml-5">{{ $order->estimated_delivery_date ? $order->estimated_delivery_date->format('d M Y') : 'N/A' }}</span>
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
                                            <li>
                                                <p>Courier</p>
                                                <span>+ ₹ {{ number_format($order->courier_charge ?? 220, 2) }}</span>
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
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                            <i class="fa-solid fa-star"></i>
                                        </span>
                                    </li>
                                    <li>
                                        <button class="re-order-btn" onclick="reorderItem({{ $item->product_id }})">
                                            <i class="fa-solid fa-rotate-right me-2"></i>Re-Order
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
                                                        <i class="fa-solid fa-star"></i>
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



                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<style>
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
</style>
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


// Track order function
$('.track-order-link').click(function(e) {
    e.preventDefault();
    var orderId = $(this).data('order');
    // Implement tracking logic here
    alert('Order tracking feature - Order #' + orderId);
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
