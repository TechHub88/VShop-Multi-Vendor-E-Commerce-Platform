@extends('vshop.main')

<style>
    .custom-product-image-wrapper-one {
        width: 80%;
        max-width: 100%;
        margin: 0;
    }
    .custom-product-image-wrapper-two {
        width: 20%;
        max-width: 100%;
        padding: 0;
        max-height: 500px;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 5px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .custom-product-image-wrapper-two::-webkit-scrollbar {
        width: 5px;
    }
    .custom-product-image-wrapper-two::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }
    .custom-product-image-wrapper-two::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    .custom-product-image-wrapper-one .product-image-one-box-inner img {
        width: 100%;
        height: auto;
        max-height: 400px;
        object-fit: contain;
        border-radius: 10px;
    }
    .custom-product-image-wrapper-two .product-image-two-box {
        width: 100%;
        margin-bottom: 5px;
    }
    .custom-product-image-wrapper-two .product-image-one-box-inner img {
        width: 100%;
        height: auto;
        border-radius: 10px;
        object-fit: cover;
        cursor: pointer;
    }

    .variant-pill {
        font-size: 13px !important;
        color: #232F34 !important;
    }

    .variant-pill:hover {
        background: #004392 !important;
        color: #fff !important;
    }

    .variant-pill:has(input:checked) {
        border: 1px solid #004392 !important;
        background: #fff;
    }

    .variant-row {
        margin-bottom: 12px !important;
    }

    .stock-status {
        margin-top: 16px !important;
    }

    .stock-status p.in-stock {
        font-size: 14px !important@;
    }

    .stock-status p i {
        font-size: 14px !important;
    }

    .product-box {
        height: auto !important;
    }
    
    .price-sec {
        box-shadow: none !important;
        border: 0 !important;
        border-radius: 0 !important;
        padding: 0 !important;
    }

    /* ========== NEW ENQUIRY BUTTONS STYLES ========== */
    .enquiry-buttons {
        margin-left: 8px;
    }

    .btn-enquiry {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        border: none;
        background: #f0f0f0;
        color: #333;
        transition: 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-enquiry i {
        font-size: 14px;
    }

    .whatsapp-enquiry {
        background: #25D366;
        color: white;
    }
    .whatsapp-enquiry:hover {
        background: #20b859;
    }

    .phone-enquiry {
        background: #004392;
        color: white;
    }
    .phone-enquiry:hover {
        background: #002e66;
    }

    .email-enquiry {
        background: #6c757d;
        color: white;
    }
    .email-enquiry:hover {
        background: #5a6268;
    }

    /* Amazon Style Rating */
    .amazon-rating-sec {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 14px;
        margin-bottom: 6px;
    }
    .amazon-rating-sec .rating-num {
        color: #0f1111;
        font-weight: 500;
    }
    .amazon-rating-sec .stars {
        display: flex;
        gap: 1px;
    }
    .amazon-rating-sec .stars i {
        font-size: 13px;
    }
    .amazon-rating-sec .stars i.text-warning {
        color: #ffa41c !important;
    }
    .amazon-rating-sec .stars i.text-secondary {
        color: #e0e0e0 !important;
    }
    .amazon-rating-sec .rating-count {
        color: #007185;
        text-decoration: none;
        margin-left: 2px;
    }
    .amazon-rating-sec .rating-count:hover {
        color: #c7511f;
        text-decoration: underline;
    }
</style>

@section('title', $product->name . ' - Product Details')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">
                <div class="bed-cum-sec">
                    <button class="back-btn" onclick="window.history.back()">
                        <i class="fa-solid fa-arrow-left"></i>
                    </button>
                    <ul>
                        <li><a href="{{ route('home') }}">Home</a> / </li>
                        <li><a href="{{ route('productmanage.index') }}">{{ $product->name }}</a> /</li>
                        <li>{{ $product->category->name ?? 'Category' }}</li>
                    </ul>
                </div>

                <div class="product-details-sec">
                    <div class="row">

                        {{-- LEFT SIDE --}}
                        <div class="col-lg-9">
                            <div class="product-details-left">
                                <div class="product-list-box">
                                    <div class="product-list-box-top">

                                        {{-- IMAGE SECTION --}}
                                        <div class="product-list-box-left">
                                            <div class="share-wishlist-sec">
                                                <a href="javascript:void(0);" class="share-btn"
                                                   data-url="{{ route('productmanage.show', $product->id) }}"
                                                   data-title="{{ $product->name }}">
                                                    <i class="fa-solid fa-share-nodes"></i>
                                                </a>
                                                <a href="#" class="wishlist-btn" data-product-id="{{ $product->id }}">
                                                    <i class="fa-solid fa-heart"></i>
                                                </a>
                                            </div>

                                            <div class="product-image-sec">
                                                {{-- MAIN SLIDER --}}
                                                <div class="custom-product-image-wrapper-one">
                                                    <div class="product-image-one-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img id="mainProductImage" src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}">
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- THUMBNAILS --}}
                                                <div class="custom-product-image-wrapper-two">
                                                    <div class="product-image-two-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img class="thumb-image active-thumb" src="{{ asset($product->thumbnail) }}" data-full="{{ asset($product->thumbnail) }}">
                                                        </div>
                                                    </div>
                                                    @foreach($galleryImages as $img)
                                                    <div class="product-image-two-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img class="thumb-image" src="{{ asset($img) }}" data-full="{{ asset($img) }}" alt="{{ $product->name }}">
                                                        </div>
                                                    </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>

                                        {{-- DETAILS SECTION --}}
                                        <div class="product-list-box-right">
                                           <div class="product-heading">
    <div class="product-heading-left">
        <h3>{{ $product->name }}</h3>
        
        {{-- Show store info only if product belongs to a seller --}}
        @if($product->seller && $product->seller->id)
        <p class="store-name">
            {{-- Use seller's ID in the route (slug removed) --}}
            <a href="{{ route('vshop.store.show', $product->seller->id) }}">
                {{ $product->seller->name }}
            </a>
        </p>
        @endif
    </div>
</div>

                                            @php
                                                $price = $product->price;
                                                $regular = $product->regular_price ?? $price;
                                                $discount = 0;
                                                if ($product->is_discount && $product->discount_value > 0) {
                                                    $discount = $product->discount_type == 'percentage' ? $product->discount_value : round(($product->discount_value / ($regular ?: 1)) * 100);
                                                } else if ($regular > $price) {
                                                    $discount = round((($regular - $price) / $regular) * 100);
                                                }
                                                $saveAmount = ($regular > $price) ? ($regular - $price) : 0;
                                            @endphp

                                            <div class="rating-sec" style="margin-bottom: 12px; display: flex; align-items: center; gap: 12px;">
                                                <div class="dropdown custom-rating-dropdown position-relative">
                                                    <a href="#" class="amazon-rating-sec dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; margin-bottom: 0; color: inherit; display: inline-flex; border: none; background: transparent; padding: 0;">
                                                        <span class="rating-num">{{ number_format($averageRating ?? 0, 1) }}</span>
                                                        <span class="stars">
                                                            @for($i=1;$i<=5;$i++)
                                                                <i class="fa-solid fa-star {{ $i <= round($averageRating ?? 0) ? 'text-warning' : 'text-secondary' }}"></i>
                                                            @endfor
                                                        </span>
                                                        <i class="fa-solid fa-chevron-down ms-1 text-muted" style="font-size: 10px;"></i>
                                                    </a>
                                                    <a href="#reviews" class="rating-count d-inline-block ms-1" style="font-size: 13px; color: #007185; text-decoration: none;">
                                                        ({{ $totalReviews ?? 0 }})
                                                    </a>
                                                
                                                    <div class="dropdown-menu p-3 shadow" style="width: 260px; border-radius: 8px; border: 1px solid #ccc; z-index: 1050;">
                                                        <div class="d-flex align-items-center mb-2">
                                                            <span class="stars me-2">
                                                                @for($i=1;$i<=5;$i++)
                                                                    <i class="fa-solid fa-star {{ $i <= round($averageRating ?? 0) ? 'text-warning' : 'text-secondary' }}" style="font-size: 14px;"></i>
                                                                @endfor
                                                            </span>
                                                            <strong style="font-size: 14px;">{{ number_format($averageRating ?? 0, 1) }} out of 5</strong>
                                                        </div>
                                                        <p class="text-muted mb-3" style="font-size: 13px;">{{ $totalReviews ?? 0 }} global ratings</p>
                                                        
                                                        @php
                                                            $totalCount = $totalReviews ?? 0;
                                                            $breakdown = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
                                                            $reviewsCollection = $product->reviews ?? collect();
                                                            
                                                            if($reviewsCollection->count() > 0) {
                                                                foreach($reviewsCollection as $r) {
                                                                    $rVal = round($r->rating);
                                                                    if($rVal >= 1 && $rVal <= 5) {
                                                                        $breakdown[$rVal]++;
                                                                    }
                                                                }
                                                            } elseif ($totalCount > 0) {
                                                                $avg = $averageRating ?? 0;
                                                                if($avg >= 4) {
                                                                    $breakdown[5] = round($totalCount * 0.7);
                                                                    $breakdown[4] = round($totalCount * 0.2);
                                                                    $breakdown[3] = $totalCount - $breakdown[5] - $breakdown[4];
                                                                } else {
                                                                    $breakdown[5] = round($totalCount * 0.4);
                                                                    $breakdown[4] = round($totalCount * 0.3);
                                                                    $breakdown[3] = round($totalCount * 0.2);
                                                                    $breakdown[2] = $totalCount - $breakdown[5] - $breakdown[4] - $breakdown[3];
                                                                }
                                                            }
                                                        @endphp
                                                
                                                        @for($star = 5; $star >= 1; $star--)
                                                            @php
                                                                $count = $breakdown[$star] ?? 0;
                                                                $percent = $totalCount > 0 ? round(($count / $totalCount) * 100) : 0;
                                                            @endphp
                                                            <div class="d-flex align-items-center mb-2" style="font-size: 13px;">
                                                                <a href="#reviews" class="text-decoration-none" style="width: 50px; color: #007185;">{{ $star }} star</a>
                                                                <div class="progress flex-grow-1 mx-2" style="height: 16px; background-color: #f0f2f2; border: 1px solid #e3e6e6; border-radius: 4px; box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);">
                                                                    <div class="progress-bar" role="progressbar" style="width: {{ $percent }}%; background-color: #de7921; border-radius: 2px;" aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                </div>
                                                                <span class="text-end" style="width: 35px; color: #007185;">{{ $percent }}%</span>
                                                            </div>
                                                        @endfor
                                                
                                                        <hr class="my-2 border-secondary opacity-25">
                                                        <a href="#reviews" class="text-center d-block text-decoration-none" style="font-size: 14px; color: #007185; font-weight: 500;">
                                                            See customer reviews <i class="fa-solid fa-chevron-right ms-1" style="font-size: 10px;"></i>
                                                        </a>
                                                    </div>
                                                </div>
                                                @if($discount > 0)
                                                    <p class="off-box" style="margin: 0; font-weight: bold; color: #cc0c39; font-size: 13px;">{{ $discount }} % Off</p>
                                                @endif
                                            </div>
                                            <div id="variantMessage" style="display:none;" class="alert mt-2"></div>

                                            @if(!empty($product->short_summary))
                                            <div class="short-summary-sec mt-3 mb-3">
                                                <p class="text-secondary mb-0" style="font-size: 14px; font-weight: 500; border-left: 3px solid #004392; padding-left: 10px;">
                                                    {{ $product->short_summary }}
                                                </p>
                                            </div>
                                            @endif

                                            <div class="price-sec" id="priceSection">
                                                <ul>
                                                    <li class="price-row">
                                                        <p class="new-rupess">₹ <span class="current-price" id="currentPrice">{{ number_format($price,2) }}</span></p>
                                                        @if($regular > $price)
                                                        <p class="old-rupess" id="oldPrice">₹ <span id="regularPrice">{{ number_format($regular,2) }}</span></p>
                                                        @endif
                                                        @if($discount > 0)
                                                        <p class="off-box" id="discountPercentage" style="display:none;">{{ $discount }}% Off</p>
                                                        @endif
                                                    </li>
                                                    @if($saveAmount > 0)
                                                    <li><span class="text-success fw-bold">You Save ₹ <span id="saveAmount">{{ number_format($saveAmount,2) }}</span></span></li>
                                                    @endif
                                                </ul>
                                            </div>

                                            {{-- DYNAMIC DETAILS SECTION (Professional / Product / Service) --}}
                                            <div class="product-dynamic-details mt-3 mb-4" >
                                                <div class="d-flex flex-wrap gap-3" style="font-size: 13px;">
                                                    
                                                    @if(in_array($product->type, ['vshop-professional', 'professional', 'vtravel-professional', 'service', 'vshop-service', 'vtravel-service', 'information_advisory', 'vtravel-information_advisory', 'tour', 'vtravel-tour']))
                                                        {{-- Professional & Service Details --}}
                                                        @if(!empty($product->session_mode))
                                                        <div class="d-flex align-items-center"><i class="fa-solid fa-video text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Mode:</span> <span class="text-dark">{{ $product->session_mode }}</span></div>
                                                        @endif
                                                        @if(!empty($product->session_type))
                                                        <div class="d-flex align-items-center"><i class="fa-solid fa-globe text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Type:</span> <span class="text-dark">{{ $product->session_type }}</span></div>
                                                        @endif
                                                        @if(!empty($product->service_duration))
                                                        <div class="d-flex align-items-center"><i class="fa-solid fa-clock text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Duration:</span> <span class="text-dark">{{ $product->service_duration }}</span></div>
                                                        @endif
                                                        @if(!empty($product->languages))
                                                        <div class="d-flex align-items-center"><i class="fa-solid fa-language text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Languages:</span> <span class="text-dark">{{ $product->languages }}</span></div>
                                                        @endif
                                                        @if(!empty($product->max_booking))
                                                        <div class="d-flex align-items-center"><i class="fa-solid fa-users text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Max Booking:</span> <span class="text-dark">{{ $product->max_booking }}</span></div>
                                                        @endif
                                                        @if(!empty($product->service_validity))
                                                        <div class="d-flex align-items-center"><i class="fa-regular fa-calendar-check text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Validity:</span> <span class="text-dark">{{ $product->service_validity }}</span></div>
                                                        @endif
                                                        @if(!empty($product->provider_name))
                                                        <div class="d-flex align-items-center"><i class="fa-solid fa-chalkboard-user text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Provider:</span> <span class="text-dark">{{ $product->provider_name }}</span></div>
                                                        @endif
                                                    @elseif(in_array($product->type, ['vshop-product', 'product']))
                                                        {{-- Product Details --}}
                                                        @if(!empty($product->color))
                                                        <div class="d-flex align-items-center"><i class="fa-solid fa-palette text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Color:</span> <span class="text-dark">{{ $product->color }}</span></div>
                                                        @endif
                                                        @if(!empty($product->size))
                                                        <div class="d-flex align-items-center"><i class="fa-solid fa-ruler text-primary me-2"></i> <span class="text-muted fw-semibold me-1">Size:</span> <span class="text-dark">{{ $product->size }}</span></div>
                                                        @endif
                                                    @endif

                                                </div>
                                            </div>

                                            <div class="variant-wrapper">
                                                @foreach($attributeOptions as $attrName => $options)
                                                <div class="variant-row">
                                                    <div class="variant-label">{{ ucfirst($attrName) }}</div>
                                                    <div class="variant-options">
                                                        @foreach($options as $option)
                                                        <label class="variant-pill">
                                                            <input type="radio" name="{{ $attrName }}" value="{{ $option['term_name'] }}"
                                                                data-term-id="{{ $option['term_id'] }}"
                                                                data-price="{{ $option['price'] }}"
                                                                data-regular-price="{{ $option['regular_price'] }}"
                                                                data-stock="{{ $option['stock'] }}"
                                                                data-combination="{{ $option['combination_key'] }}" {{ $loop->first ? 'checked' : '' }}>
                                                            <span>{{ $option['term_name'] }}</span>
                                                        </label>
                                                        @endforeach
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            
                                            <!--<div class="quantity-sec">-->
                                            <!--    <label class="quantity-box"><input type="radio" name="radio1" checked=""><span>8 mm</span></label>-->
                                            <!--    <label class="quantity-box"><input type="radio" name="radio1"><span>6 mm</span></label>-->
                                            <!--    <label class="quantity-box"><input type="radio" name="radio1"><span>4 mm</span></label>-->
                                            <!--    <label class="quantity-box"><input type="radio" name="radio1"><span>2 mm</span></label>-->
                                            <!--</div>-->
                                                                        
                                            <!--<div class="color-select-sec">-->
                                            <!--    <label class="color-box"><input type="radio" checked="" name="radio2"><span class="color-circle merun-box"></span></label>-->
                                            <!--    <label class="color-box"><input type="radio" name="radio2"><span class="color-circle green-box"></span></label>-->
                                            <!--    <label class="color-box"><input type="radio" name="radio2"><span class="color-circle chocolate-box"></span></label>-->
                                            <!--    <p>( Barbarian Red )</p>-->
                                            <!--</div>-->
                                            
                                            <div class="product-dtl-sec">
                                                <p>Expected Delivery : <span class="blackColor" id="deliveryDate"></span> /
                                                    <a href="{{ route('vshop.address.create') }}" class="change-pincode">Change Pincode</a>
                                                </p>
                                            </div>

                                            {{-- QUANTITY AND CART + ENQUIRY BUTTONS --}}
                                            <div class="action-row d-flex align-items-center gap-2 flex-wrap">
                                                <!-- Quantity -->
                                                <div class="qty-box d-flex align-items-center">
                                                    <button type="button" id="decrementQty">-</button>
                                                    <input type="text" id="quantity" value="1" readonly>
                                                    <button type="button" id="incrementQty">+</button>
                                                </div>

                                                <!-- Add to Cart -->
                                                <button class="btn-cart text-nowrap" id="addToCartBtn" data-product-id="{{ $product->id }}">
                                                    Add to Cart ₹ <span id="totalPrice">{{ number_format($price, 2) }}</span>
                                                </button>

                                                <!-- Go to Cart -->
                                                <a href="{{ route('vshop.cart.index') }}" class="btn-go-cart">Go to Cart</a>

                                                <!-- Buy Now -->
                                                <button class="btn-buy" id="buyNowBtn" data-product-id="{{ $product->id }}">Buy</button>

                                                <!-- ========== ENQUIRY BUTTONS ========== -->
                                                <div class="enquiry-buttons d-flex gap-2 ms-2">
                                                    <button class="btn-enquiry whatsapp-enquiry" data-contact="whatsapp" data-product-id="{{ $product->id }}">
                                                        <i class="fab fa-whatsapp"></i> WhatsApp
                                                    </button>
                                                    <button class="btn-enquiry phone-enquiry" data-contact="phone" data-product-id="{{ $product->id }}">
                                                        <i class="fas fa-phone-alt"></i> Phone
                                                    </button>
                                                    <button class="btn-enquiry email-enquiry" data-contact="email" data-product-id="{{ $product->id }}">
                                                        <i class="fas fa-envelope"></i> Email
                                                    </button>
                                                </div>
                                            </div>

                                            <div class="stock-status" id="stockStatus">
                                                @if(($product->stock_qty ?? 100) > 0)
                                                <p class="in-stock"><i class="fa-regular fa-check-circle"></i> In Stock (<span id="availableStock">{{ $product->stock_qty ?? '0' }}</span> available)</p>
                                                @else
                                                <p class="out-of-stock"><i class="fa-regular fa-times-circle"></i> Out of Stock</p>
                                                @endif
                                            </div>



                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT AD SECTION --}}
                        <div class="col-lg-3">
                            <div class="product-advatice-sec">
                                <div class="right-product-img">
                                    <img id="mainProductImage" src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- PRODUCT DETAILS BOTTOM SECTION (unchanged) --}}
                <div class="product-details-bottom">
                    <ul class="nav nav-tabs custom-product-tabs mb-4" id="productDetailTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details-tab-pane" type="button" role="tab" aria-controls="details-tab-pane" aria-selected="true">Details</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="specifications-tab" data-bs-toggle="tab" data-bs-target="#specifications-tab-pane" type="button" role="tab" aria-controls="specifications-tab-pane" aria-selected="false">Specifications</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="reviews-tab" data-bs-toggle="tab" data-bs-target="#reviews-tab-pane" type="button" role="tab" aria-controls="reviews-tab-pane" aria-selected="false">Reviews ({{ $totalReviews }})</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="policy-tab" data-bs-toggle="tab" data-bs-target="#policy-tab-pane" type="button" role="tab" aria-controls="policy-tab-pane" aria-selected="false">
                                @if(in_array($product->type, ['service', 'professional', 'vshop-professional', 'vtravel-professional', 'tour', 'vtravel-tour']))
                                    Cancellation Policy
                                @else
                                    Delivery & Returns
                                @endif
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="productDetailTabsContent">
                        <!-- Details Tab Pane -->
                        <div class="tab-pane fade show active" id="details-tab-pane" role="tabpanel" aria-labelledby="details-tab" tabindex="0">
                            <div class="p-3 bg-white rounded shadow-sm border">
                                @if(!empty($product->description))
                                <div class="product-description-content mb-4">
                                    {!! $product->description !!}
                                </div>
                                @endif

                                @if(!empty($product->bullet_points))
                                <div class="product-bullet-points-content mb-4 border-top pt-3">
                                    <h5 class="font-weight-bold text-dark mb-3">Key Highlights / Features</h5>
                                    <div class="text-secondary">
                                        {!! $product->bullet_points !!}
                                    </div>
                                </div>
                                @endif

                                @if(!empty($product->highlights))
                                <div class="product-highlights-content mb-4 border-top pt-3">
                                    <h5 class="font-weight-bold text-dark mb-3">Highlights</h5>
                                    <ul class="highlight-list" style="list-style: none; padding-left: 0;">
                                        @foreach((array)$product->highlights as $highlight)
                                        <li class="mb-2"><i class="fa-regular fa-star text-warning me-2"></i> {{ $highlight }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif

                                @php
                                    $sampleCodes = [];
                                    if (!empty($product->sample_codes)) {
                                        $sampleCodes = is_array($product->sample_codes) ? $product->sample_codes : json_decode($product->sample_codes, true);
                                    }
                                @endphp
                                @if(!empty($sampleCodes) && is_array($sampleCodes))
                                <div class="product-details-bottom-box mt-4 border-top pt-3">
                                    <div class="product-details-bottom-box-header mb-3"><h5 class="font-weight-bold text-dark">Sample Codes & Files</h5></div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped custom-info-table">
                                            <thead>
                                                <tr>
                                                    <th>Language</th>
                                                    <th>IDE</th>
                                                    <th>Type</th>
                                                    <th>OS</th>
                                                    <th>Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($sampleCodes as $sample)
                                                <tr>
                                                    <td>{{ $sample['language'] ?? 'N/A' }}</td>
                                                    <td>{{ $sample['ide'] ?? 'N/A' }}</td>
                                                    <td>{{ $sample['type'] ?? 'N/A' }}</td>
                                                    <td>{{ $sample['os'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if(!empty($sample['url']))
                                                            <a href="{{ asset($sample['url']) }}" target="_blank" class="btn btn-sm btn-primary"><i class="fa-solid fa-download"></i> Download</a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @php
                                    $downloadsList = [];
                                    if (!empty($product->download_data)) {
                                        $downloadsList = is_array($product->download_data) ? $product->download_data : json_decode($product->download_data, true);
                                    }
                                @endphp
                                @if(!empty($downloadsList) && is_array($downloadsList))
                                <div class="product-details-bottom-box mt-4 border-top pt-3">
                                    <div class="product-details-bottom-box-header mb-3">
                                        <h5 class="font-weight-bold text-dark">
                                            Included Digital Downloads ({{ count($downloadsList) }})
                                        </h5>
                                    </div>
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped custom-info-table">
                                            <thead>
                                                <tr>
                                                    <th>File Title</th>
                                                    <th>Release Date</th>
                                                    <th>Link</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($downloadsList as $dw)
                                                <tr>
                                                    <td>{{ $dw['title'] ?? 'N/A' }}</td>
                                                    <td>{{ $dw['date'] ?? 'N/A' }}</td>
                                                    <td>
                                                        @if(!empty($dw['url']))
                                                            <a href="{{ $dw['url'] }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-download"></i> Download</a>
                                                        @else
                                                            N/A
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                @endif

                                @php
                                    $productVideos = [];
                                    if (!empty($product->videos)) {
                                        $productVideos = is_array($product->videos) ? $product->videos : json_decode($product->videos, true);
                                    }
                                @endphp
                                @if(!empty($productVideos) && is_array($productVideos))
                                <div class="product-details-bottom-box mt-4 border-top pt-3">
                                    <div class="product-details-bottom-box-header mb-3"><h5 class="font-weight-bold text-dark">Product Videos</h5></div>
                                    <div class="row">
                                        @foreach($productVideos as $videoUrl)
                                            <div class="col-md-6 mb-3">
                                                @if(str_contains($videoUrl, 'youtube.com') || str_contains($videoUrl, 'youtu.be'))
                                                    @php
                                                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $videoUrl, $match);
                                                        $youtubeId = $match[1] ?? null;
                                                    @endphp
                                                    @if($youtubeId)
                                                        <div class="ratio ratio-16x9">
                                                            <iframe src="https://www.youtube.com/embed/{{ $youtubeId }}" title="Product Video" allowfullscreen style="border:0; border-radius:8px; width:100%; height:200px;"></iframe>
                                                        </div>
                                                    @else
                                                        <a href="{{ $videoUrl }}" target="_blank" class="btn btn-outline-danger"><i class="fa-solid fa-play"></i> Watch Video</a>
                                                    @endif
                                                @else
                                                    <div class="video-container">
                                                        <video src="{{ asset($videoUrl) }}" controls style="width:100%; border-radius:8px; max-height:240px;"></video>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif

                                @if(!empty($product->faqs))
                                <div class="product-details-bottom-box mt-4 border-top pt-3">
                                    <div class="product-details-bottom-box-header mb-3"><h5 class="font-weight-bold text-dark">Frequently Asked Questions</h5></div>
                                    <ul class="faq-list" style="list-style: none; padding-left: 0;">
                                        @foreach($product->faqs as $faq)
                                        <li class="mb-3">
                                            <h6 class="fw-bold text-dark mb-1">Q. {{ $faq['question'] ?? '' }}</h6>
                                            <p class="text-muted mb-0">A. {{ $faq['answer'] ?? '' }}</p>
                                        </li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Specifications Tab Pane -->
                        <div class="tab-pane fade" id="specifications-tab-pane" role="tabpanel" aria-labelledby="specifications-tab" tabindex="0">
                            <div class="p-3 bg-white rounded shadow-sm border">
                                {{-- 1. TECHNICAL SPECIFICATIONS (USER ENTERED) --}}
                                @if(!empty($product->specifications))
                                <div class="product-details-heading mb-3">
                                    <h4 class="text-primary font-weight-bold">Technical Specifications</h4>
                                </div>
                                <div class="product-dtl-table table-responsive mb-4">
                                    <table class="table table-bordered table-striped custom-info-table mb-0">
                                        <tbody>
                                            @foreach($product->specifications as $key => $value)
                                            <tr>
                                                <td class="info-label" style="width: 30%; font-weight: 600;">{{ $key }}</td>
                                                <td class="info-value">{!! $value !!}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                @endif

                                {{-- 2. DYNAMIC DETAILS ACCORDING TO PRODUCT TYPE --}}
                                
                                @if(in_array($product->type, ['vshop-professional', 'professional', 'vtravel-professional', 'service', 'vshop-service', 'vtravel-service', 'information_advisory', 'vtravel-information_advisory', 'tour', 'vtravel-tour']))
                                    @php
                                        $profDetails = array_filter([
                                            'Portfolio' => $product->portfolio,
                                            'Materials Included' => $product->materials_included,
                                            'Cancellation Policy' => $product->cancel_policy_prof,
                                        ]);
                                    @endphp
                                    @if(count($profDetails) > 0)
                                    <div class="product-details-heading mt-4 mb-3">
                                        <h4 class="text-primary font-weight-bold">Service & Professional Details</h4>
                                    </div>
                                    <div class="product-dtl-table table-responsive mb-4">
                                        <table class="table table-bordered table-striped custom-info-table mb-0">
                                            <tbody>
                                                @foreach($profDetails as $key => $value)
                                                <tr>
                                                    <td class="info-label" style="width: 30%; font-weight: 600; vertical-align: top;">{{ $key }}</td>
                                                    <td class="info-value" style="font-size: 13.5px; line-height: 1.6;">{!! nl2br(e($value)) !!}</td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @endif
                                @endif
                                
                                {{-- A. Product Information --}}
                                @if(in_array($product->type, ['product', 'vshop-product', 'professional', 'vshop-professional', 'vtravel-professional']) || empty($product->type))
                                    @php
                                        $productDetails = array_filter([
                                            'Brand Name' => $product->brand_name ?? ($product->brand->name ?? null),
                                            'Manufactured By' => $product->manufactured_by,
                                            'Manufacturing Date' => $product->manufacturing_date,
                                            'Country of Origin' => $product->country_origin,
                                            'Generic Name' => $product->generic_name,
                                            'ASIN' => $product->asin,
                                            'Ingredients' => $product->ingredients,
                                            'Expiry (Days)' => $product->expiry_days,
                                            'Storage Instructions' => $product->storage_instruction,
                                            'SKU' => $product->sku,
                                            'Item Code' => $product->item_code,
                                            'Color' => $product->color,
                                            'Variant' => $product->variant,
                                            'Size' => $product->size,
                                            'Quantity' => $product->quantity,
                                            'Batch Number' => $product->batch_number,
                                            'Stock Quantity' => $product->stock_qty ?? $product->inventory,
                                            'Delivery Time' => $product->delivery_time,
                                            'Delivery Hour' => $product->delivery_hour ? $product->delivery_hour . ' Hours' : null,
                                            'Low Stock Threshold' => $product->low_stock_threshold,
                                            'Auto Disable when stock = 0' => isset($product->auto_disable) ? ($product->auto_disable ? 'Yes' : 'No') : null,
                                            'Minimum Order Quantity (MOQ)' => $product->moq,
                                            'Dimensions (L x B x H)' => ($product->length || $product->breadth || $product->height) ? $product->length . ' x ' . $product->breadth . ' x ' . $product->height : $product->dimensions,
                                            'Weight' => $product->weight,
                                            'Warranty' => $product->warranty,
                                        ]);
                                    @endphp
                                    @if(!empty($productDetails))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Product Information</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($productDetails as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endif

                                {{-- B. Professional Slot Details --}}
                                @if(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional']))
                                    @php
                                        $profDetails = array_filter([
                                            'Professional Type' => $product->professional_type,
                                            'Session Duration' => $product->session_duration ? $product->session_duration . ' Mins' : null,
                                            'Service Duration' => $product->service_duration,
                                            'Session Mode' => $product->session_mode,
                                            'Session Type' => $product->session_type,
                                            'Start Time' => $product->start_time,
                                            'End Time' => $product->end_time,
                                            'Instructor / Provider Name' => $product->provider_name,
                                            'Max Participants' => $product->max_participants,
                                            'Booking Required' => isset($product->booking_required) ? ($product->booking_required ? 'Yes' : 'No') : null,
                                            'Service Validity' => $product->service_validity,
                                            'Meeting Link' => $product->meeting_link,
                                            'Materials Included' => $product->materials_included,
                                            'Languages Spoken' => $product->languages,
                                            'Max Booking Per Slot' => $product->max_booking,
                                            'Portfolio URL' => $product->portfolio,
                                        ]);
                                    @endphp
                                    @if(!empty($profDetails))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Professional Slot Details</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($profDetails as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">
                                                                @if($label === 'Portfolio URL')
                                                                    <a href="{{ $value }}" target="_blank" class="text-primary">{{ $value }}</a>
                                                                @else
                                                                    {!! $value !!}
                                                                @endif
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endif

                                {{-- C. Tour / Location / Suitability Details --}}
                                @if(in_array($product->type, ['tour', 'vtravel-tour', 'information_advisory', 'vtravel-information_advisory']))
                                    @php
                                        $serviceDaysFormatted = null;
                                        if (!empty($product->service_days)) {
                                            $days = is_array($product->service_days) ? $product->service_days : json_decode($product->service_days, true);
                                            if (is_array($days)) {
                                                $serviceDaysFormatted = implode(', ', array_map('ucfirst', $days));
                                            }
                                        }
                                        $tourDetails = array_filter([
                                            'Country' => $product->country,
                                            'State' => $product->state,
                                            'City / District' => $product->district,
                                            'Service Type' => $product->service_type,
                                            'Start Time' => $product->start_time,
                                            'End Time' => $product->end_time,
                                            'Duration' => $product->duration_hours ? $product->duration_hours . ' Hours' : null,
                                            'Fixed Type' => $product->fixed_type,
                                            'Fixed Dates' => $product->fixed_dates,
                                            'Duration (Days/Nights)' => ($product->service_days_count || $product->service_nights) ? $product->service_days_count . ' Days / ' . $product->service_nights . ' Nights' : null,
                                            'Kids Allowed' => isset($product->kids_allowed) ? ($product->kids_allowed ? 'Yes' : 'No') : null,
                                            'Adults Allowed' => isset($product->adult_allowed) ? ($product->adult_allowed ? 'Yes' : 'No') : null,
                                            'Adult Count Limit' => $product->adult_count,
                                            'Kids Count Limit' => $product->kids_count,
                                            'Active Days' => $serviceDaysFormatted,
                                        ]);
                                    @endphp
                                    @if(!empty($tourDetails))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Service & Suitability Details</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($tourDetails as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endif

                                {{-- D. Tour Extra Details --}}
                                @if(in_array($product->type, ['tour', 'vtravel-tour']))
                                    @php
                                        $tourExtraDetails = array_filter([
                                            'Guide Type' => $product->guide_service_type,
                                            'Guide Category' => $product->guide_category,
                                            'Max Capacity' => $product->capacity,
                                            'Start Point' => $product->start_point,
                                            'End Point' => $product->end_point,
                                            'Inclusions / Exclusions' => $product->inclusion,
                                            'Availability' => $product->availability,
                                        ]);
                                    @endphp
                                    @if(!empty($tourExtraDetails))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Tour Details</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($tourExtraDetails as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endif

                                {{-- E. Information Advisory Groups --}}
                                @if(in_array($product->type, ['information_advisory', 'vtravel-information_advisory']))
                                    @php
                                        $travelGroup = array_filter([
                                            'Travel Service Type' => $product->travel_service_type,
                                            'Travel Documents' => $product->travel_documents,
                                            'Travel Pricing' => $product->travel_pricing,
                                            'Trip Policy / Types' => $product->travel_policy_trip_types,
                                            'License Accreditation' => $product->travel_license,
                                            'Assistance Type' => $product->travel_rate_assistance_type,
                                            'Covered Areas' => $product->travel_areas,
                                            'Emergency Contact Info' => $product->travel_emergency,
                                        ]);
                                        $spiritualGroup = array_filter([
                                            'Dress Code' => $product->spiritual_dress_code,
                                            'Spiritual Festivals' => $product->spiritual_festivals,
                                            'Prasad Advisory' => $product->spiritual_prasad_advisory_type,
                                            'Contact Guidelines' => $product->spiritual_contact,
                                            'Rules & Guidelines' => $product->spiritual_guidelines_era,
                                            'UNESCO Heritage Status' => $product->spiritual_unesco,
                                            'Spiritual Guide Type' => $product->spiritual_guide_type,
                                            'Duration Details' => $product->spiritual_duration,
                                            'Facilities' => $product->spiritual_facilities,
                                            'Family Friendly' => $product->spiritual_family_friendly,
                                        ]);
                                        $activityGroup = array_filter([
                                            'Activity Type' => $product->activity_type,
                                            'Difficulty Level' => $product->activity_difficulty,
                                            'Required Equipment' => $product->activity_equipment,
                                            'Safety Measures' => $product->activity_safety,
                                            'Guide Requirements' => $product->activity_guide,
                                            'Group Food Option' => $product->activity_group_food_name,
                                            'Restaurant Associated' => $product->activity_restaurant,
                                            'Food Type' => $product->activity_food_type,
                                            'Must Try Specialties' => $product->activity_must_try,
                                            'Dietary Options' => $product->activity_diet_options,
                                            'Takeaway Available' => $product->activity_takeaway,
                                        ]);
                                        $vendorGroup = array_filter([
                                            'Vendor Category' => $product->vendor_category,
                                            'Famous For' => $product->vendor_famous_for,
                                            'Operational Areas' => $product->vendor_operational_areas,
                                            'Payment Methods' => $product->vendor_payment_methods,
                                            'Delivery Support' => $product->vendor_delivery,
                                            'Customization Support' => $product->vendor_customization,
                                        ]);
                                    @endphp

                                    @if(!empty($travelGroup))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Travel Information</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($travelGroup as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    @if(!empty($spiritualGroup))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Spiritual Advisory</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($spiritualGroup as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    @if(!empty($activityGroup))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Activity & Dining Info</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($activityGroup as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif

                                    @if(!empty($vendorGroup))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Vendor Details</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($vendorGroup as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endif

                                {{-- F. Other Service details --}}
                                @if(in_array($product->type, ['service', 'vshop-service', 'vtravel-service']))
                                    @php
                                        $otherServiceDetails = array_filter([
                                            'Service Type' => $product->other_service_type,
                                            'Processing Time' => $product->processing_time,
                                            'Required Documents' => $product->documents,
                                            'Validity Period' => $product->validity,
                                            'Gov Fees / Extra Charges' => $product->gov_fee,
                                        ]);
                                    @endphp
                                    @if(!empty($otherServiceDetails))
                                        <div class="product-details-heading mt-4 mb-3">
                                            <h4 class="text-primary font-weight-bold">Service Details</h4>
                                        </div>
                                        <div class="product-info-table table-responsive mb-4">
                                            <table class="table table-bordered table-striped custom-info-table">
                                                <tbody>
                                                    @foreach($otherServiceDetails as $label => $value)
                                                        <tr>
                                                            <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                            <td class="info-value">{!! $value !!}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @endif
                                @endif

                                {{-- General Service / Package Fields --}}
                                @php
                                    $generalDetails = array_filter([
                                        'Processing Time' => $product->processing_time,
                                        'Required Documents' => $product->documents,
                                        'Service Validity' => $product->validity ?? $product->service_validity,
                                        'Government Fee' => $product->gov_fee,
                                        'Slots/Schedule' => $product->schedule_slots,
                                        'Provider Name' => $product->provider_name,
                                        'Max Participants' => $product->max_participants,
                                        'Booking Required' => isset($product->booking_required) ? ($product->booking_required ? 'Yes' : 'No') : null,
                                        'Meeting Link' => $product->meeting_link,
                                        'Materials Included' => $product->materials_included,
                                        'Itinerary' => $product->itinerary,
                                        'Accommodation' => $product->accommodation,
                                        'Included/Excluded' => $product->included_excluded,
                                        'Luggage Terms' => $product->luggage,
                                        'Refund Policy' => $product->refund_policy,
                                        'Free Days' => $product->free_days,
                                        'Benefits' => $product->benefits,
                                        'Target Audience' => $product->target_audience,
                                    ]);
                                @endphp

                                @if(!empty($generalDetails))
                                    <div class="product-details-heading mt-4 mb-3">
                                        <h4 class="text-primary font-weight-bold">Additional Information</h4>
                                    </div>
                                    <div class="product-info-table table-responsive mb-4">
                                        <table class="table table-bordered table-striped custom-info-table">
                                            <tbody>
                                                @foreach($generalDetails as $label => $value)
                                                    <tr>
                                                        <td class="info-label" style="width: 30%; font-weight: 600;">{{ $label }}</td>
                                                        <td class="info-value">
                                                            @if($label === 'Meeting Link')
                                                                <a href="{{ $value }}" target="_blank" class="text-primary">{{ $value }}</a>
                                                            @else
                                                                {!! $value !!}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif

                                @if(!empty($product->features))
                                <div class="product-details-bottom-box mt-4">
                                    <div class="product-details-bottom-box-header mb-2"><h5 class="font-weight-bold text-dark">Features</h5></div>
                                    <ul class="feature-list" style="list-style: none; padding-left: 0;">
                                        @foreach($product->features as $feature)
                                        <li class="mb-2"><i class="fa-regular fa-check text-primary me-2"></i> {{ $feature }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Reviews Tab Pane -->
                        <div class="tab-pane fade" id="reviews-tab-pane" role="tabpanel" aria-labelledby="reviews-tab" tabindex="0">
                            <div class="p-3 bg-white rounded shadow-sm border">
                                <div class="product-details-bottom-box-header d-flex justify-content-between align-items-center mb-3">
                                    <h4 class="mb-0 text-dark">Customer Reviews ({{ $totalReviews }})</h4>
                                    <button class="btn btn-primary btn-sm" id="openReviewForm">Write a Review</button>
                                </div>
                                <div id="reviewFormBox" style="display:none; margin-top:20px;" class="mb-4">
                                    @auth('customer')
                                    <form action="{{ route('vtravel.review.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="seller_id" value="{{ $product->created_by_id }}">
                                        <div class="review-box p-4 shadow-sm rounded bg-light border">
                                            <div class="mb-4 d-flex align-items-center gap-3">
                                                <label class="form-label mb-0"><strong>Rate this product</strong></label>
                                                <div class="star-rating-ui">
                                                    <i class="fa-solid fa-star" data-value="1"></i>
                                                    <i class="fa-solid fa-star" data-value="2"></i>
                                                    <i class="fa-solid fa-star" data-value="3"></i>
                                                    <i class="fa-solid fa-star" data-value="4"></i>
                                                    <i class="fa-solid fa-star" data-value="5"></i>
                                                </div>
                                                <input type="hidden" name="rating" id="ratingValue">
                                            </div>
                                            <div class="mb-3"><label class="form-label">Review Title</label><input type="text" name="title" class="form-control"></div>
                                            <div class="mb-3"><label class="form-label">Your Review</label><textarea name="comment" class="form-control" rows="4"></textarea></div>
                                            <div class="mb-3">
                                                <label class="form-label d-block">Would you recommend?</label>
                                                <div class="form-check form-check-inline"><input type="radio" name="would_recommend" value="1" class="form-check-input" id="recommendYes"><label class="form-check-label" for="recommendYes">Yes</label></div>
                                                <div class="form-check form-check-inline"><input type="radio" name="would_recommend" value="0" class="form-check-input" id="recommendNo"><label class="form-check-label" for="recommendNo">No</label></div>
                                            </div>
                                            <button class="btn btn-primary w-100">Submit Review</button>
                                        </div>
                                    </form>
                                    @else
                                    <div class="alert alert-warning">Please <a href="{{ route('employeelogin') }}">login</a> to write a review.</div>
                                    @endauth
                                </div>
                                <div class="review-list">
                                    @forelse($productReviews as $review)
                                    <div class="review-card mb-3 p-3 border rounded bg-light">
                                        <div class="review-top d-flex justify-content-between align-items-center mb-2">
                                            <strong class="text-dark">{{ $review->user_name }}</strong>
                                            <span class="review-date text-muted small">{{ $review->created_at->format('d M Y') }}</span>
                                        </div>
                                        <div class="review-rating mb-2">
                                            @for($i=1;$i<=5;$i++)
                                            <i class="fa{{ $i <= $review->rating ? '-solid':'-regular' }} fa-star text-warning"></i>
                                            @endfor
                                        </div>
                                        <h6 class="fw-bold text-dark mb-1">{{ $review->title }}</h6>
                                        <p class="mb-2 text-secondary">{{ $review->comment }}</p>
                                        @if($review->would_recommend == 1)
                                            <span class="text-success small fw-semibold"><i class="fa-regular fa-circle-check"></i> Recommends this product</span>
                                        @elseif($review->would_recommend == 0)
                                            <span class="text-danger small fw-semibold"><i class="fa-regular fa-circle-xmark"></i> Doesn't recommend</span>
                                        @endif
                                    </div>
                                    @empty
                                        <p class="text-muted">No reviews yet.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Policy Tab Pane -->
                        <div class="tab-pane fade" id="policy-tab-pane" role="tabpanel" aria-labelledby="policy-tab" tabindex="0">
                            <div class="p-3 bg-white rounded shadow-sm border">
                                @if(in_array($product->type, ['service', 'professional', 'vshop-professional', 'vtravel-professional', 'tour', 'vtravel-tour']))
                                    <h4 class="mb-3 text-dark">Cancellation Policy</h4>
                                    @php
                                        $cancelPolicy = $product->cancel_policy_tour ?? ($product->cancel_policy_prof ?? $product->cancel_policy_other);
                                    @endphp
                                    @if(!empty($cancelPolicy))
                                        <div class="policy-content p-3 border rounded bg-light text-secondary">
                                            {!! $cancelPolicy !!}
                                        </div>
                                    @else
                                        <div class="policy-content p-3 border rounded bg-light">
                                            <p class="mb-2 fw-semibold text-dark">Standard Booking Cancellation Terms:</p>
                                            <ul class="mb-0 text-secondary">
                                                <li>Cancellations made 24 hours or more before the scheduled start time will receive a 100% refund.</li>
                                                <li>Cancellations made within 24 hours of the scheduled start time are non-refundable.</li>
                                                <li>To cancel your appointment or request changes, please contact customer support or submit an inquiry.</li>
                                            </ul>
                                        </div>
                                    @endif
                                @else
                                    <h4 class="mb-3 text-dark">Delivery & Returns Policy</h4>
                                    <div class="policy-content p-3 border rounded bg-light">
                                        <h6 class="fw-bold text-dark mb-2">Shipping & Delivery Policy</h6>
                                        <p class="text-secondary mb-3">Standard delivery times are between 3 to 5 business days depending on your delivery address and location. Shipping charges will be calculated at checkout. You will receive a tracking number once your package has shipped.</p>
                                        
                                        <h6 class="fw-bold text-dark mb-2">Returns & Replacement Policy</h6>
                                        <p class="text-secondary mb-0">We support a 7-day return and replacement policy for items that are damaged, defective, or different from what was ordered. Returned products must be unused, in their original packaging, and with tags attached. Please contact support or request a refund through your customer dashboard.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                    {{-- RELATED PRODUCTS --}}
                    @if($relatedProducts->isNotEmpty())
                    <div class="product-details-bottom-box">
                        <div class="product-details-bottom-box-header"><h3>Related Products</h3></div>
                        <div class="other-product-sec">
                            <div class="other-product-wrapper owl-carousel">
                                @foreach($relatedProducts as $relatedProduct)
                                <div class="other-product-box item">
                                    <div class="product-box">
                                        <div class="share-wishlist-sec">
                                            <a href="javascript:void(0);" class="share-btn" data-url="{{ route('productmanage.show', $relatedProduct->id) }}" data-title="{{ $relatedProduct->name }}"><i class="fa-solid fa-share-nodes"></i></a>
                                            <a href="#" class="wishlist-btn related-wishlist" data-product-id="{{ $relatedProduct->id }}"><i class="fa-solid fa-heart"></i></a>
                                        </div>
                                        <div class="product-box-img"><a href="{{ route('productmanage.show', $relatedProduct->id) }}"><img src="{{ asset($relatedProduct->thumbnail) }}" alt="{{ $relatedProduct->name }}" style="width: 100%; height: 160px; object-fit: cover; border-radius: 6px;"></a></div>
                                        <div class="product-bottom">
                                            <h3><a href="{{ route('productmanage.show', $relatedProduct->id) }}">{{ $relatedProduct->name }}</a></h3>
                                            <div class="off-sec">
                                                @if($relatedProduct->regular_price && $relatedProduct->regular_price > $relatedProduct->price)
                                                <p class="old-rupess">₹ {{ number_format($relatedProduct->regular_price,2) }}</p>
                                                <p class="off-box">{{ round((($relatedProduct->regular_price - $relatedProduct->price) / $relatedProduct->regular_price) * 100) }}% Off</p>
                                                @endif
                                            </div>
                                            <div class="product-bottom-sec">
                                                <a href="#" class="add-cart-btn related-add-cart" data-product-id="{{ $relatedProduct->id }}" data-price="{{ $relatedProduct->price }}">Add to Cart</a>
                                                <p class="new-rupess">₹ {{ number_format($relatedProduct->price, 2) }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<div id="shareModal" class="share-modal">
    <div class="share-content">
        <div class="share-header"><h4>Share Product</h4><span class="close-share">&times;</span></div>
        <div class="share-options">
            <button class="share-item whatsapp"><i class="fa-brands fa-whatsapp"></i><span>WhatsApp</span></button>
            <button class="share-item facebook"><i class="fa-brands fa-facebook"></i><span>Facebook</span></button>
            <button class="share-item copy"><i class="fa-solid fa-link"></i><span>Copy Link</span></button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// ========== SHARE MODAL (unchanged) ==========
let shareUrl = '';
let shareTitle = '';

$(document).on('click','.share-btn',function(){
    shareUrl = $(this).data('url');
    shareTitle = $(this).data('title');
    $('#shareModal').addClass('active');
});

$(document).on('click','.close-share',function(){
    $('#shareModal').removeClass('active');
});

$(document).on('click','#shareModal',function(e){
    if(e.target.id === 'shareModal') $(this).removeClass('active');
});

$(document).on('click','.whatsapp',function(){
    let url = "https://wa.me/?text=" + encodeURIComponent(shareTitle + " " + shareUrl);
    window.open(url, '_blank');
});

$(document).on('click','.facebook',function(){
    let url = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(shareUrl);
    window.open(url, '_blank');
});

$(document).on('click','.copy',function(){
    navigator.clipboard.writeText(shareUrl);
    alert("Link copied!");
});

// ========== ENQUIRY POPUP (NEW) ==========
$(document).ready(function() {
    $(document).on('click', '.btn-enquiry', function(e) {
        e.preventDefault();

        const contactType = $(this).data('contact');
        const productId = $(this).data('product-id');
        let contactLabel = '';

        switch(contactType) {
            case 'whatsapp': contactLabel = 'WhatsApp'; break;
            case 'phone': contactLabel = 'Phone Call'; break;
            case 'email': contactLabel = 'Email'; break;
        }

        Swal.fire({
            title: `Enquiry via ${contactLabel}`,
            html: `<textarea id="enquiryReason" class="swal2-textarea" rows="4" placeholder="Please describe your enquiry or question..."></textarea>`,
            showCancelButton: true,
            confirmButtonText: 'Send Enquiry',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#28a745',
            preConfirm: () => {
                const reason = document.getElementById('enquiryReason').value.trim();
                if (!reason) {
                    Swal.showValidationMessage('Please enter a reason/ message');
                    return false;
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Submitting...', text: 'Please wait', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                $.ajax({
                    url: "{{ route('vshop.enquiry.store') }}",
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId,
                        reason: result.value,
                        contact_type: contactType
                    },
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({ icon: 'success', title: 'Enquiry Sent!', text: response.message, confirmButtonColor: '#28a745' });
                        } else if (response.login_required) {
                            Swal.fire({ icon: 'warning', title: 'Login Required', text: response.message, confirmButtonColor: '#004392', confirmButtonText: 'Login Now' })
                                .then(() => { window.location.href = "{{ route('vshoplogin') }}?redirect=" + encodeURIComponent(window.location.href); });
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: response.message || 'Something went wrong', confirmButtonColor: '#dc3545' });
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 401) {
                            Swal.fire({ icon: 'warning', title: 'Login Required', text: 'Please login to submit enquiry.', confirmButtonColor: '#004392', confirmButtonText: 'Login Now' })
                                .then(() => { window.location.href = "{{ route('vshoplogin') }}?redirect=" + encodeURIComponent(window.location.href); });
                        } else {
                            let msg = xhr.responseJSON?.message || 'Server error. Please try again.';
                            Swal.fire({ icon: 'error', title: 'Failed', text: msg, confirmButtonColor: '#dc3545' });
                        }
                    }
                });
            }
        });
    });
});

// ========== THE REST OF YOUR EXISTING JAVASCRIPT (product, cart, wishlist, etc.) ==========
// (Paste your complete existing JavaScript here – unchanged)
// ...

// For brevity, the full original JS is not repeated here – but you MUST keep all your existing JS.
// The code below is a placeholder – in your final file, replace it with the exact JS you already have.

</script>


<script>

let shareUrl = '';
let shareTitle = '';

$(document).on('click','.share-btn',function(){

    shareUrl = $(this).data('url');
    shareTitle = $(this).data('title');

    $('#shareModal').addClass('active'); // ✅ FIX
});

$(document).on('click','.close-share',function(){
    $('#shareModal').removeClass('active'); // ✅ FIX
});

// click outside close
$(document).on('click','#shareModal',function(e){
    if(e.target.id === 'shareModal'){
        $(this).removeClass('active');
    }
});

// WhatsApp
$(document).on('click','.whatsapp',function(){
    let url = "https://wa.me/?text=" + encodeURIComponent(shareTitle + " " + shareUrl);
    window.open(url, '_blank');
});

// Facebook
$(document).on('click','.facebook',function(){
    let url = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(shareUrl);
    window.open(url, '_blank');
});

// Copy Link
$(document).on('click','.copy',function(){
    navigator.clipboard.writeText(shareUrl);
    alert("Link copied!");
});

</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {


        // ================= BUY NOW (FINAL HYBRID LOGIC) =================
        const buyNowBtn = document.getElementById("buyNowBtn");

        if (buyNowBtn) {
            buyNowBtn.addEventListener("click", function (e) {
                e.preventDefault();

                let selectedTermIds = [];
                let selectedData = {};

                // ================= GET SELECTED ATTRIBUTES =================
                document.querySelectorAll('.variant-pill input:checked').forEach(el => {
                    selectedTermIds.push(parseInt(el.dataset.termId));

                    selectedData[el.name] = {
                        id: el.dataset.termId,
                        value: el.value
                    };
                });

                let matchedKey = null;
                let comboMap = {};

                // ================= BUILD COMBO MAP =================
                productConfig.combinations.forEach(c => {
                    if (!comboMap[c.combination_key]) {
                        comboMap[c.combination_key] = [];
                    }
                    comboMap[c.combination_key].push(parseInt(c.attribute_term_id));
                });

                // ================= FULL MATCH =================
                for (let key in comboMap) {
                    let attrs = comboMap[key];

                    let fullMatch = selectedTermIds.length === attrs.length &&
                        selectedTermIds.every(id => attrs.includes(id));

                    if (fullMatch) {
                        matchedKey = key;
                        break;
                    }
                }

                // ================= PARTIAL MATCH (SINGLE ATTRIBUTE) =================
                if (!matchedKey) {
                    for (let key in comboMap) {
                        let attrs = comboMap[key];

                        let partialMatch = selectedTermIds.some(id => attrs.includes(id));

                        if (partialMatch) {
                            matchedKey = key;
                            break;
                        }
                    }
                }

                // ================= FINAL FALLBACK =================
                if (!matchedKey && productConfig.combinations.length > 0) {
                    matchedKey = productConfig.combinations[0].combination_key;
                }

                // ================= STOCK CHECK =================
                let record = productConfig.combinations.find(c => c.combination_key == matchedKey);

                if (record && record.product_stock !== undefined && record.product_stock <= 0) {
                    showToast("Out of stock", "error");
                    return;
                }

                @auth('customer')

                // ================= GET FINAL DATA =================
                const productId = this.dataset.productId;
                const quantity = productConfig.quantity;
                const price = parseFloat(
                    document.getElementById("currentPrice")?.textContent.replace(/,/g, '')
                    || productConfig.basePrice
                );

                let params = new URLSearchParams();

                params.append("product_id", productId);
                params.append("quantity", quantity);
                params.append("price", price);

                if (matchedKey) {
                    params.append("combination_key", matchedKey);
                }

                // ================= ADD ALL ATTRIBUTES =================
                Object.keys(selectedData).forEach(attr => {
                    params.append(attr + "_id", selectedData[attr].id);
                    params.append(attr + "_name", selectedData[attr].value);
                });

                // ================= SELLER SUPPORT =================
                @if ($product -> created_by == 'seller' && $product -> seller)
                    params.append("seller_id", "{{ $product->seller->id }}");
                @endif

                // ================= REDIRECT =================
                window.location.href = "{{ route('vshop.address.index') }}?" + params.toString();

                @else
                showToast("Login required", "error");
                setTimeout(() => {
                    window.location = "{{ route('vshoplogin') }}";
                }, 1500);
                @endauth
            });
        }

        // Product configuration object
        const productConfig = {
            basePrice: {{ $product-> price
    }},
        baseRegularPrice: {{ $product-> regular_price ?? $product -> price }},
        quantity: 1,
        selectedAttributes: {},
        combinations: @json($combinations)
    };

    // Initialize the page
    init();

    // ================= DYNAMIC ATTRIBUTE SELECT =================

    document.querySelectorAll('.variant-pill input').forEach(el => {

        el.addEventListener('change', function () {

            productConfig.selectedAttributes[this.name] = {
                termId: this.dataset.termId,
                value: this.value
            };

            updatePrice();

        });

    });
    updateDeliveryDate();

    // ================= INIT =================
    function init() {
        const s = document.querySelector('input[name="size"]:checked');
        const c = document.querySelector('input[name="color"]:checked');

        if (s) setSize(s);
        if (c) setColor(c);

        updatePrice();

        // Load wishlist status if logged in
        @auth('customer')
        loadWishlistStatus();
        @endauth
    }

    // ================= SIZE SELECT =================
    document.querySelectorAll('input[name="size"]').forEach(radio => {
        radio.addEventListener('change', function () {
            setSize(this);
            updatePrice();
        });
    });

    function setSize(el) {
        productConfig.currentSize = {
            value: el.value,
            termId: el.dataset.termId,
            price: parseFloat(el.dataset.price) || productConfig.basePrice,
            regularPrice: parseFloat(el.dataset.regularPrice) || productConfig.baseRegularPrice,
            combination: el.dataset.combination || ''
        };
    }

    // ================= COLOR SELECT =================
    document.querySelectorAll('input[name="color"]').forEach(radio => {
        radio.addEventListener('change', function () {
            setColor(this);
            updateSelectedColorName(this.value);
            updatePrice();
        });
    });

    function setColor(el) {
        productConfig.currentColor = {
            value: el.value,
            termId: el.dataset.termId,
            price: parseFloat(el.dataset.price) || productConfig.basePrice,
            regularPrice: parseFloat(el.dataset.regularPrice) || productConfig.baseRegularPrice,
            combination: el.dataset.combination || ''
        };
    }

    // ================= UPDATE SELECTED COLOR NAME =================
    function updateSelectedColorName(name) {
        const el = document.getElementById('selectedColorName');
        if (el) el.textContent = name;
    }

    // ================= PRICE LOGIC WITH COMBINATION CHECK =================
    function updatePrice() {

        let selectedTermIds = [];

        Object.values(productConfig.selectedAttributes).forEach(attr => {
            selectedTermIds.push(parseInt(attr.termId));
        });

        if (selectedTermIds.length === 0) return;

        let matchedKey = null;

        const combos = productConfig.combinations;

        let comboMap = {};

        // group combination attributes
        combos.forEach(c => {
            if (!comboMap[c.combination_key]) {
                comboMap[c.combination_key] = [];
            }

            comboMap[c.combination_key].push(parseInt(c.attribute_term_id));
        });

        // find matching combination
        for (let key in comboMap) {

            let attrs = comboMap[key];

            let match = selectedTermIds.every(id => attrs.includes(id));

            if (match) {
                matchedKey = key;
                break;
            }

        }

        if (matchedKey) {

            let record = combos.find(c => c.combination_key == matchedKey);

            if (!record) return;

            let price = parseFloat(record.price);
            let regular = parseFloat(record.regular_price ?? record.price);

            updatePriceUI(price, regular);

        }

    }

    // ================= UPDATE STOCK STATUS =================
    function updateStockStatus(stock, combinationValid) {
        const stockStatus = document.getElementById('stockStatus');
        if (!stockStatus) return;

        if (!combinationValid && productConfig.currentSize && productConfig.currentColor) {
            stockStatus.innerHTML = `<p class="out-of-stock">
                <i class="fa-regular fa-times-circle"></i> 
                Combination Not Available
            </p>`;
        } else if (stock > 0) {
            stockStatus.innerHTML = `<p class="in-stock">
                <i class="fa-regular fa-check-circle"></i> 
                In Stock (${stock} available)
            </p>`;
        } else if (stock === 0 && productConfig.currentSize && productConfig.currentColor) {
            stockStatus.innerHTML = `<p class="out-of-stock">
                <i class="fa-regular fa-times-circle"></i> 
                Out of Stock
            </p>`;
        } else {
            // Default stock from product
            stockStatus.innerHTML = `<p class="in-stock">
                <i class="fa-regular fa-check-circle"></i> 
                In Stock ({{ $product->stock_qty ?? 100 }} available)
            </p>`;
        }
    }

  function updatePriceUI(price, regular) {

    const currentPriceEl = document.getElementById('currentPrice');
    const regularPriceEl = document.getElementById('regularPrice');
    const discountEl = document.getElementById('discountPercentage');
    const saveEl = document.getElementById('saveAmount');
    const oldPriceRow = document.getElementById('oldPrice');

    if (currentPriceEl) {
        currentPriceEl.textContent = formatNumber(price);
    }

    if (regular > price) {

        // OLD PRICE
        if (regularPriceEl) {
            regularPriceEl.textContent = formatNumber(regular);
        }

        // DISCOUNT %
        const discount = Math.round(((regular - price) / regular) * 100);

        if (discountEl) {
            discountEl.textContent = discount + "% Off";
            discountEl.style.display = 'inline-block';
        }

        // SAVE AMOUNT
        if (saveEl) {
            saveEl.textContent = formatNumber(regular - price);
        }

        // SHOW OLD PRICE
        if (oldPriceRow) {
            oldPriceRow.style.display = 'inline-block';
        }

    } else {

        // HIDE DISCOUNT
        if (discountEl) discountEl.style.display = 'none';

        // HIDE OLD PRICE
        if (oldPriceRow) oldPriceRow.style.display = 'none';

        // RESET SAVE
        if (saveEl) saveEl.textContent = '0';
    }

    updateTotalPrice();
}

    // ================= UPDATE TOTAL PRICE =================
    function updateTotalPrice() {
        const currentPrice = parseFloat(document.getElementById('currentPrice')?.textContent.replace(/,/g, '') || productConfig.basePrice);
        const total = currentPrice * productConfig.quantity;

        const totalPriceEl = document.getElementById('totalPrice');
        if (totalPriceEl) {
            totalPriceEl.textContent = formatNumber(total);
        }

        // Update add to cart button text
        const addToCartBtn = document.getElementById('addToCartBtn');
        if (addToCartBtn) {
            addToCartBtn.innerHTML = `Add to Cart · ₹ <span id="totalPrice">${formatNumber(total)}</span>`;
        }
    }

    // ================= QUANTITY CONTROLS =================
    const decrementBtn = document.getElementById('decrementQty');
    const incrementBtn = document.getElementById('incrementQty');
    const qtyInput = document.getElementById('quantity');

    if (decrementBtn) {
        decrementBtn.addEventListener('click', function () {
            if (productConfig.quantity > 1) {
                productConfig.quantity--;
                qtyInput.value = productConfig.quantity;
                updateTotalPrice();
            }
        });
    }

    if (incrementBtn) {
        incrementBtn.addEventListener('click', function () {
            // Check stock limit if combination selected
            let maxStock = 999;

            if (productConfig.currentSize && productConfig.currentColor) {
                const sizeTermId = productConfig.currentSize.termId;
                const colorTermId = productConfig.currentColor.termId;

                const sizeCombinations = new Set();
                const colorCombinations = new Set();

                productConfig.combinations.forEach(comb => {
                    if (comb.attribute_term_id == sizeTermId) {
                        sizeCombinations.add(comb.combination_key);
                    }
                    if (comb.attribute_term_id == colorTermId) {
                        colorCombinations.add(comb.combination_key);
                    }
                });

                const validCombinationKeys = [...sizeCombinations].filter(key => colorCombinations.has(key));

                if (validCombinationKeys.length > 0) {
                    const combinationKey = validCombinationKeys[0];
                    const combinationRecord = productConfig.combinations.find(c => c.combination_key === combinationKey);
                    if (combinationRecord && combinationRecord.product_stock) {
                        maxStock = parseInt(combinationRecord.product_stock);
                    }
                }
            }

            if (productConfig.quantity < maxStock) {
                productConfig.quantity++;
                qtyInput.value = productConfig.quantity;
                updateTotalPrice();
            } else {
                showToast(`Only ${maxStock} items available`, "error");
            }
        });
    }

    // ================= DELIVERY DATE =================
    function updateDeliveryDate() {
        const d = new Date();
        d.setDate(d.getDate() + 5);
        const deliveryEl = document.getElementById('deliveryDate');
        if (deliveryEl) {
            deliveryEl.textContent = d.toLocaleDateString('en-IN', {
                day: 'numeric',
                month: 'short',
                year: '2-digit'
            });
        }
    }

    // ================= COMBINATION ERROR HANDLING =================
    function showCombinationError(message) {
        const box = document.getElementById("variantMessage");
        if (!box) return;

        box.classList.remove("alert-success");
        box.classList.add("alert-danger");
        box.innerHTML = `<i class="fa-regular fa-circle-exclamation"></i> ${message}`;
        box.style.display = "block";
    }

    function hideCombinationError() {
        const box = document.getElementById("variantMessage");
        if (box) {
            box.style.display = "none";
        }
    }

    // ================= TOAST FUNCTION =================
    function showToast(message, type = "success") {
        const box = document.getElementById("variantMessage");
        if (!box) return;

        box.classList.remove("alert-success", "alert-danger");
        box.classList.add(type === "error" ? "alert-danger" : "alert-success");
        box.innerHTML = type === "success" ?
            `<i class="fa-regular fa-check-circle"></i> ${message}` :
            `<i class="fa-regular fa-circle-exclamation"></i> ${message}`;
        box.style.display = "block";

        setTimeout(() => {
            box.style.display = "none";
        }, 3000);
    }

    // ================= FORMAT NUMBER =================
    function formatNumber(num) {
        return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // ================= WISHLIST FUNCTIONALITY =================
    document.querySelectorAll(".wishlist-btn").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            @auth('customer')

            const productId = this.dataset.productId;
            const icon = this.querySelector("i");

            icon.className = "fa fa-spinner fa-spin";

            fetch("{{ route('vshop.wishlist.toggle') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ product_id: productId })
            })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) {
                        showToast(data.message || "Wishlist error", "error");
                        icon.className = "fa-regular fa-heart";
                        return;
                    }

                    if (data.status === "added") {
                        icon.className = "fa-solid fa-heart text-danger";
                        showToast("Added to wishlist", "success");
                    } else {
                        icon.className = "fa-regular fa-heart";
                        showToast("Removed from wishlist", "success");
                    }
                })
                .catch(() => {
                    icon.className = "fa-solid fa-heart";
                    showToast("Wishlist error", "error");
                });

            @else
            showToast("Login required", "error");
            window.location = "{{ route('vshoplogin') }}";
            @endauth
        });
    });

    // ================= LOAD WISHLIST STATUS =================
    @auth('customer')
    function loadWishlistStatus() {
        fetch("{{ route('vshop.wishlist.items') }}", {
            method: "GET",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "X-Requested-With": "XMLHttpRequest"
            }
        })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.items) {
                    data.items.forEach(productId => {
                        document.querySelectorAll(`.wishlist-btn[data-product-id="${productId}"] i`).forEach(icon => {
                            icon.className = "fa-solid fa-heart text-danger";
                        });
                    });
                }
            })
            .catch(err => console.log('Error loading wishlist:', err));
    }
    @endauth

    // ================= ADD TO CART (MAIN PRODUCT) =================
    const mainAddBtn = document.getElementById("addToCartBtn");

    if (mainAddBtn) {
        mainAddBtn.addEventListener("click", function (e) {
            e.preventDefault();

            // Check if combination is valid
            if (productConfig.currentSize && productConfig.currentColor) {
                const sizeTermId = productConfig.currentSize.termId;
                const colorTermId = productConfig.currentColor.termId;

                const sizeCombinations = new Set();
                const colorCombinations = new Set();

                productConfig.combinations.forEach(comb => {
                    if (comb.attribute_term_id == sizeTermId) {
                        sizeCombinations.add(comb.combination_key);
                    }
                    if (comb.attribute_term_id == colorTermId) {
                        colorCombinations.add(comb.combination_key);
                    }
                });

                const validCombinationKeys = [...sizeCombinations].filter(key => colorCombinations.has(key));

                if (validCombinationKeys.length === 0) {
                    showToast("This size & color combination is not available", "error");
                    return;
                }

                // Check stock
                const combinationKey = validCombinationKeys[0];
                const combinationRecord = productConfig.combinations.find(c => c.combination_key === combinationKey);
                if (combinationRecord && combinationRecord.product_stock <= 0) {
                    showToast("This combination is out of stock", "error");
                    return;
                }
            }

            @auth('customer')

            const btn = this;
            const originalText = btn.innerHTML;

            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Adding...';
            btn.disabled = true;

            // Get selected options
            const selectedSize = document.querySelector('input[name="size"]:checked');
            const selectedColor = document.querySelector('input[name="color"]:checked');

            const payload = {
                product_id: btn.dataset.productId,
                quantity: productConfig.quantity,
                price: parseFloat(document.getElementById("currentPrice").textContent.replace(/,/g, '')),
                _token: "{{ csrf_token() }}"
            };

            // Add size_id if selected
            if (selectedSize) {
                payload.size_id = selectedSize.dataset.termId;
                payload.size_name = selectedSize.value;
            }

            // Add color_id if selected
            if (selectedColor) {
                payload.color_id = selectedColor.dataset.termId;
                payload.color_name = selectedColor.value;
            }

            // Find combination key if both selected
            if (selectedSize && selectedColor) {
                const sizeTermId = selectedSize.dataset.termId;
                const colorTermId = selectedColor.dataset.termId;

                const sizeCombinations = new Set();
                const colorCombinations = new Set();

                productConfig.combinations.forEach(comb => {
                    if (comb.attribute_term_id == sizeTermId) {
                        sizeCombinations.add(comb.combination_key);
                    }
                    if (comb.attribute_term_id == colorTermId) {
                        colorCombinations.add(comb.combination_key);
                    }
                });

                const validCombinationKeys = [...sizeCombinations].filter(key => colorCombinations.has(key));

                if (validCombinationKeys.length > 0) {
                    payload.combination_key = validCombinationKeys[0];
                }
            }

            // For seller products, add seller_id if available
            @if ($product -> created_by == 'seller' && $product -> seller)
                payload.seller_id = "{{ $product->seller->id }}";
            @endif

            fetch("{{ route('vshop.cart.add') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast("Added to cart", "success");

                        // Update cart count in header
                        const cartCount = document.querySelector("#cartCount, .cart-count");
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;

                            // Add animation
                            cartCount.classList.add("cart-bounce");
                            setTimeout(() => cartCount.classList.remove("cart-bounce"), 500);
                        }
                    } else {
                        showToast(data.message || "Cart error", "error");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast("Failed to add to cart", "error");
                })
                .finally(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                });

            @else
            showToast("Login required", "error");
            setTimeout(() => {
                window.location = "{{ route('vshoplogin') }}";
            }, 1500);
            @endauth
        });
    }

    // ================= RELATED PRODUCT ADD TO CART =================
    document.querySelectorAll(".related-add-cart").forEach(btn => {
        btn.addEventListener("click", function (e) {
            e.preventDefault();

            @auth('customer')

            const productId = this.dataset.productId;
            const price = this.dataset.price;
            const originalText = this.innerHTML;

            this.innerHTML = '<i class="fa fa-spinner fa-spin"></i>';
            this.disabled = true;

            const payload = {
                product_id: productId,
                quantity: 1,
                price: price,
                _token: "{{ csrf_token() }}"
            };

            // Check if it's a seller product (you might need to add data-seller-id attribute)
            const sellerId = this.dataset.sellerId;
            if (sellerId) {
                payload.seller_id = sellerId;
            }

            fetch("{{ route('vshop.cart.add') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": "{{ csrf_token() }}",
                    "X-Requested-With": "XMLHttpRequest",
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(payload)
            })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        showToast("Added to cart", "success");

                        const cartCount = document.querySelector("#cartCount, .cart-count");
                        if (cartCount) {
                            cartCount.textContent = data.cart_count;
                            cartCount.classList.add("cart-bounce");
                            setTimeout(() => cartCount.classList.remove("cart-bounce"), 500);
                        }
                    } else {
                        showToast(data.message || "Cart error", "error");
                    }
                })
                .catch(() => showToast("Cart error", "error"))
                .finally(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                });

            @else
            showToast("Login required", "error");
            setTimeout(() => {
                window.location = "{{ route('vshoplogin') }}";
            }, 1500);
            @endauth
        });
    });
    
    
      document.getElementById("openReviewForm").addEventListener("click",function(){

let form=document.getElementById("reviewFormBox");

if(form.style.display==="none"){

form.style.display="block";

}else{

form.style.display="none";

}

});

    // ================= ADD CSS FOR CART ANIMATION =================
    const style = document.createElement('style');
    style.textContent = `
        .cart-bounce {
            animation: bounce 0.5s ease;
        }
        
        @keyframes bounce {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.3); }
        }
        
        #variantMessage {
            transition: all 0.3s ease;
            border-radius: 5px;
            padding: 10px 15px;
            margin: 10px 0;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .add-cart-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            pointer-events: none;
        }
        
        .fa-spinner {
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .in-stock {
            color: #28a745;
            font-weight: 500;
        }
        
        .out-of-stock {
            color: #dc3545;
            font-weight: 500;
        }
    `;
    document.head.appendChild(style);

});





    let selectedTags = [];

    document.querySelectorAll('.tag-pill').forEach(tag => {
        tag.addEventListener('click', function () {

            let value = this.dataset.value;

            this.classList.toggle('active');

            if (selectedTags.includes(value)) {
                selectedTags = selectedTags.filter(t => t !== value);
            } else {
                selectedTags.push(value);
            }

            document.getElementById('selectedTags').value = selectedTags.join(',');
        });
    });
</script>
<script>
    document.querySelectorAll('.star-rating-ui i').forEach(star => {

    star.addEventListener('click', function(){

        let value = this.getAttribute('data-value');

        // set hidden input
        document.getElementById('ratingValue').value = value;

        // reset all
        document.querySelectorAll('.star-rating-ui i').forEach(s => {
            s.classList.remove('active');
        });

        // highlight till clicked
        document.querySelectorAll('.star-rating-ui i').forEach(s => {
            if(s.getAttribute('data-value') <= value){
                s.classList.add('active');
            }
        });

    });

});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const mainImage = document.getElementById("mainProductImage");
    const rightImage = document.getElementById("rightProductImage");
    const thumbnails = document.querySelectorAll(".thumb-image");

    thumbnails.forEach(img => {
        img.addEventListener("click", function () {

            let newSrc = this.getAttribute("data-full");

            // ✅ SAFETY CHECK
            if(!newSrc || newSrc === ''){
                console.error("Image src missing");
                return;
            }

            // ✅ PRELOAD IMAGE (IMPORTANT FIX)
            let tempImg = new Image();
            tempImg.src = newSrc;

            tempImg.onload = function () {

                mainImage.src = newSrc;
                if(rightImage){
                    rightImage.src = newSrc;
                }

            };

            tempImg.onerror = function () {
                console.error("Image failed to load:", newSrc);
            };

            // Active class
            thumbnails.forEach(i => i.classList.remove("active-thumb"));
            img.classList.add("active-thumb");

        });
    });

});
</script>

<style>
/* Existing styles remain – only added enquiry button styles above */
.star-rating-ui { display: flex; justify-content: center; gap: 8px; font-size: 28px; cursor: pointer; }
.star-rating-ui i { color: #ccc; transition: 0.2s; }
.star-rating-ui i.active { color: #ffc107; }
.star-rating-ui i:hover { transform: scale(1.2); }
.thumb-image { cursor: pointer; border: 2px solid transparent; border-radius: 6px; transition: 0.3s; }
.thumb-image:hover { border-color: #0d6efd; }
.active-thumb { border-color: #0d6efd !important; }
.share-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.6); display: none; justify-content: center; align-items: center; z-index: 9999; backdrop-filter: blur(6px); }
.share-modal.active { display: flex; }
.share-content { background: #fff; width: 100%; max-width: 480px; border-radius: 18px; padding: 25px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); transform: translateY(30px) scale(0.9); opacity: 0; transition: 0.3s ease; }
.share-modal.active .share-content { transform: translateY(0) scale(1); opacity: 1; }
.share-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
.share-header h4 { font-size: 18px; font-weight: 600; color: #0d47a1; }
.close-share { font-size: 22px; cursor: pointer; color: #999; transition: 0.3s; }
.close-share:hover { color: #000; }
.share-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; }
.share-item { border: none; background: #f8f9fa; padding: 18px 10px; border-radius: 14px; cursor: pointer; transition: 0.3s; display: flex; flex-direction: column; align-items: center; gap: 8px; }
.share-item:hover { background: #eef3ff; transform: translateY(-5px); }
.share-item i { font-size: 28px; }
.share-item span { font-size: 13px; font-weight: 500; }
.whatsapp i { color: #25D366; }
.facebook i { color: #1877f2; }
.copy i { color: #333; }
.action-row { display: flex; align-items: center; gap: 8px; margin-top: 10px; flex-wrap: nowrap; }
.qty-box { display: flex; align-items: center; border: 1px solid #ddd; border-radius: 6px; overflow: hidden; height: 36px; }
.qty-box button { width: 30px; height: 36px; border: none; background: #f5f5f5; cursor: pointer; font-size: 16px; }
.qty-box input { width: 35px; text-align: center; border: none; font-size: 13px; background: #fff; }
.btn-cart { flex: 1; height: 36px; border: none; border-radius: 6px; background: #004392; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.3s; }
.btn-cart:hover { background: #002e66; }
.btn-buy { width: 90px; height: 36px; border: none; border-radius: 6px; background: #28a745; color: #fff; font-size: 13px; font-weight: 600; cursor: pointer; transition: 0.3s; }
.btn-buy:hover { background: #218838; }
.variant-wrapper { margin-top: 15px; }
.variant-row { margin-bottom: 15px; }
.variant-label { font-weight: 600; margin-bottom: 6px; font-size: 14px; color: #111; }
.variant-options { display: flex; flex-wrap: wrap; gap: 8px; }
.variant-pill { border: 1px solid #ddd; padding: 5px 14px; border-radius: 20px; background: #fff; cursor: pointer; font-size: 13px; transition: .2s; }
.variant-pill input { display: none; }
.variant-pill:has(input:checked) { border: 2px solid #004392; background: #fff; font-weight: 600; }
.variant-pill:hover { border-color: #004392; }
.right-product-img img { width: 100%; height: 180px; object-fit: contain; border-radius: 8px; background: #fff; padding: 5px; }
.right-product-img { display: flex; align-items: center; justify-content: center; background: #f8f8f8; border-radius: 8px; }
.btn-go-cart { background: #ffc107; color: #000; text-decoration: none; height: 38px; padding: 0 14px; border-radius: 6px; font-weight: 600; display: flex; align-items: center; justify-content: center; }
.btn-go-cart:hover { background: #e0a800; color: #000; }

/* ================= CUSTOM TABS & DATA TABLES STYLE ================= */
.custom-product-tabs {
    border-bottom: 2px solid #eef2f5 !important;
    margin-bottom: 24px;
    display: flex;
    gap: 8px;
}

.custom-product-tabs .nav-link {
    border: none !important;
    background: transparent !important;
    color: #4f5f73 !important;
    font-size: 15px !important;
    font-weight: 500 !important;
    padding: 12px 20px !important;
    position: relative !important;
    transition: color 0.2s ease-in-out !important;
}

.custom-product-tabs .nav-link:hover {
    color: #004392 !important;
}

.custom-product-tabs .nav-link.active {
    color: #004392 !important;
    font-weight: 600 !important;
}

.custom-product-tabs .nav-link.active::after {
    content: '' !important;
    position: absolute !important;
    bottom: -2px !important;
    left: 0 !important;
    width: 100% !important;
    height: 3px !important;
    background-color: #004392 !important;
    border-radius: 3px 3px 0 0 !important;
}

.custom-info-table {
    border-collapse: separate !important;
    border-spacing: 0 !important;
    border: 1px solid #eef2f5 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    width: 100% !important;
}

.custom-info-table th, .custom-info-table td {
    padding: 12px 16px !important;
    border: 1px solid #eef2f5 !important;
}

.custom-info-table tbody tr:nth-of-type(odd) {
    background-color: #fcfdfe !important;
}

.custom-info-table .info-label {
    background-color: #f8fafc !important;
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 14px !important;
}

.custom-info-table .info-value {
    color: #1e293b !important;
    font-size: 14px !important;
}
</style>
@endsection