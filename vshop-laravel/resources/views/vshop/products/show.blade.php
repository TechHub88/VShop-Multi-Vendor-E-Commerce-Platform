@extends('vshop.main')

@section('title', $product->name . ' - Product Details')

@push('styles')
<style>
    .refer-earn-btn {
        border: 1px solid #7E22CE !important;
        color: #7E22CE !important;
        background-color: #fff !important;
        transition: all 0.3s ease;
    }
    .refer-earn-btn:hover {
        background-color: #7E22CE !important;
        color: #fff !important;
    }
    .refer-earn-btn:hover i {
        color: #fff !important;
    }
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

    /* Fixed Size & Equal Height for Related Cards */
    .other-product-wrapper .owl-stage {
        display: flex;
    }
    .other-product-wrapper .owl-item {
        display: flex;
        flex: 1 0 auto;
    }
    .other-product-wrapper .other-product-box {
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .other-product-wrapper .product-box {
        height: 100% !important;
        display: flex;
        flex-direction: column;
        width: 100%;
    }
    .other-product-wrapper .product-box-img {
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .other-product-wrapper .product-box-img img {
        max-height: 100%;
        width: auto;
        object-fit: contain;
    }
    .other-product-wrapper .product-bottom {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .other-product-wrapper .product-bottom h3 {
        margin-bottom: auto;
    }
    .other-product-wrapper .off-sec,
    .other-product-wrapper .product-bottom-sec {
        margin-top: auto;
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

    /* Product Card Styling for Related Products */
    .product-box {
        background: #fff;
        border-radius: 0px;
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .product-box:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transform: translateY(-4px);
    }
    .product-box-img {
        height: 160px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fafafa;
        overflow: hidden;
        padding: 8px;
    }
    .product-box-img img {
        max-height: 100%;
        max-width: 100%;
        object-fit: contain;
        transition: 0.3s;
    }
    .product-box:hover .product-box-img img {
        transform: scale(1.05);
    }
    .product-bottom {
        padding: 10px 12px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    .product-bottom h3 {
        font-size: 13px;
        font-weight: 500;
        line-height: 1.4;
        height: 36px;
        overflow: hidden;
        margin-bottom: 6px;
    }
    .product-bottom h3 a {
        color: #212121;
        text-decoration: none;
        display: -webkit-box !important;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }
    .product-bottom h3 a:hover {
        color: #2874f0;
    }
    .off-sec {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }
    .old-rupess {
        text-decoration: line-through;
        color: #878787;
        font-size: 13px;
        margin: 0;
    }
    .off-box {
        color: #2874f0;
        font-weight: 600;
        font-size: 12px;
        margin: 0;
    }
    .product-bottom-sec {
        margin-top: auto;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
    }
    .new-rupess {
        font-size: 20px;
        font-weight: 600;
        color: #212121;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: left;
    }
    .add-cart-btn {
        background: #004392;
        color: #fff;
        border: none;
        padding: 6px 14px;
        border-radius: 30px;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        transition: 0.2s;
        white-space: nowrap;
    }
    .add-cart-btn:hover {
        background: #003377;
    }
    
    .view-similar-btn {
        background: #ffffff;
        color: #212121;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 12px;
        border: 1px solid #d7d7d7;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        text-decoration: none;
        box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        height: auto !important;
    }
    .view-similar-btn:hover {
        background: #f7f7f7;
        border-color: #c2c2c2;
        color: #000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.08);
    }
    .view-similar-btn i {
        color: #555;
    }
    
    /* Mobile responsive fix for product layout */
    @media (max-width: 768px) {
        .product-list-box-top {
            display: flex !important;
            flex-direction: column !important;
        }
        .product-list-box-left {
            width: 100% !important;
            margin-bottom: 20px;
        }
        .product-list-box-right {
            width: 100% !important;
            padding-left: 0 !important;
        }
        
        .product-heading h3 {
            font-size: 18px !important;
        }
        
        .price-sec .new-rupess {
            font-size: 22px !important;
        }
        
        /* Ensure thumbnails container wraps */
        .custom-product-image-wrapper-two {
            overflow-x: auto;
            display: flex;
            gap: 10px;
            padding-bottom: 5px;
        }
        
        .custom-product-image-wrapper-two .product-image-two-box {
            flex-shrink: 0;
        }
    }

    /* SPA Smooth Load Animation */
    .dashboardPagesContentonner {
        animation: fadeInSPA 0.5s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
        opacity: 0;
    }
    @keyframes fadeInSPA {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

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
                        <div class="col-lg-9 col-8">
                            <div class="product-details-left">
                                <div class="product-list-box">
                                    <div class="product-list-box-top">

                                        {{-- IMAGE SECTION --}}
                                        <div class="product-list-box-left">
                                            @php
                                                $currentUser = auth()->guard('customer')->user();
                                                $referralCode = $currentUser ? $currentUser->referral_code : null;
                                            @endphp
                                            <div class="share-wishlist-sec">
                                                <a href="javascript:void(0);" class="share-btn"
                                                   data-url="{{ route('productmanage.show', $product->id) }}"
                                                   data-title="{{ $product->name }}"
                                                   data-referral-url="{{ url('/p/' . $product->id) }}"
                                                   data-referral-code="{{ $referralCode }}">
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
        
        <div class="d-flex align-items-center flex-wrap gap-2 mb-2" style="margin-top: 4px;">
            @php
                $brandName = $product->brand_name ?? ($product->brand->name ?? null);
            @endphp
            @if($brandName)
                <span class="text-primary" style="font-size: 13px; font-weight: 500;"><i class="fa-solid fa-tag"></i> Brand: {{ $brandName }}</span>
            @endif

            @if($product->seller && ($product->seller->logo_trademark ?? '') == 'Yes' && !empty($product->seller->trademark_file))
                <span class="text-success" style="font-size: 13px; font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Registered Trademark</span>
            @endif
        </div>
        
        {{-- Instructor/Provider badge (Udemy-style) for courses/services --}}
        @if(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional', 'service', 'vshop-service', 'vtravel-service']) && !empty($product->provider_name))
        <div class="d-flex align-items-center gap-2 mb-1" style="margin-top: 2px;">
            <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 12px; font-weight: 500; border-radius: 4px;">
                <i class="fa fa-user-tie text-primary me-1" style="font-size: 11px;"></i> {{ $product->provider_name }}
            </span>
        </div>
        @endif

        {{-- Course duration badge from start_date & end_date --}}
        @if(!empty($product->start_date) && !empty($product->end_date) && $product->start_date !== '0000-00-00' && $product->end_date !== '0000-00-00')
        @php
            $courseStart = \Carbon\Carbon::parse($product->start_date);
            $courseEnd = \Carbon\Carbon::parse($product->end_date);
            $totalDays = $courseStart->diffInDays($courseEnd);
            if ($totalDays >= 30) {
                $months = floor($totalDays / 30);
                $remainDays = $totalDays % 30;
                $courseDuration = $months . ' Month' . ($months > 1 ? 's' : '');
                if ($remainDays > 0) $courseDuration .= ' ' . $remainDays . ' Days';
            } elseif ($totalDays >= 7) {
                $weeks = floor($totalDays / 7);
                $remainDays = $totalDays % 7;
                $courseDuration = $weeks . ' Week' . ($weeks > 1 ? 's' : '');
                if ($remainDays > 0) $courseDuration .= ' ' . $remainDays . ' Days';
            } else {
                $courseDuration = $totalDays . ' Day' . ($totalDays > 1 ? 's' : '');
            }
        @endphp
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge text-white px-2 py-1" style="font-size: 11px; font-weight: 600; border-radius: 4px; background: linear-gradient(135deg, #667eea, #764ba2);">
                <i class="fa fa-clock me-1" style="font-size: 10px;"></i> {{ $courseDuration }}
            </span>
            <span class="text-muted" style="font-size: 11px;">{{ $courseStart->format('d M Y') }} - {{ $courseEnd->format('d M Y') }}</span>
        </div>
        @endif
        
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

                                            <div class="rating-sec" style="margin-bottom: 12px; display: flex; align-items: center; justify-content: flex-start; gap: 12px;">
                                                <div class="dropdown custom-rating-dropdown position-relative">
                                                    <button type="button" class="amazon-rating-sec dropdown-toggle text-decoration-none" data-bs-toggle="dropdown" aria-expanded="false" style="cursor: pointer; margin-bottom: 0; color: inherit; display: inline-flex; border: none; background: transparent; padding: 0;">
                                                        <span class="rating-num">{{ number_format($averageRating ?? 0, 1) }}</span>
                                                        <span class="stars">
                                                            @for($i=1;$i<=5;$i++)
                                                                <i class="fa-solid fa-star {{ $i <= round($averageRating ?? 0) ? 'text-warning' : 'text-secondary' }}"></i>
                                                            @endfor
                                                        </span>
                                                        <i class="fa-solid fa-chevron-down ms-1 text-muted" style="font-size: 10px;"></i>
                                                    </button>
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
                                                    <li class="price-row" style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                                        <p class="new-rupess">₹ <span class="current-price" id="currentPrice">{{ number_format($price,2) }}</span></p>
                                                        @if($regular > $price)
                                                        <p class="old-rupess" id="oldPrice">₹ <span id="regularPrice">{{ number_format($regular,2) }}</span></p>
                                                        @endif
                                                        @if($discount > 0)
                                                        <p class="off-box" id="discountPercentage" style="display:none;">{{ $discount }}% Off</p>
                                                        @endif
                                                        @if(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional', 'service', 'vshop-service', 'vtravel-service']))
                                                            @php $slotsCheck = json_decode($product->professional_slots, true); @endphp
                                                            @if(!empty($slotsCheck) && is_array($slotsCheck))
                                                            <div class="date-picker-wrapper position-relative" style="display: inline-flex; align-items: center; background: #f8f9fa; border-radius: 6px; padding: 6px 12px; cursor: pointer; border: 1px solid transparent; transition: all 0.2s;">
                                                                <i class="fa-regular fa-calendar-alt text-primary" style="font-size: 13px; margin-right: 6px;"></i>
                                                                <input type="text" id="slotCalendarPicker" class="slot-date-btn text-center" placeholder="Select Date" style="
                                                                    cursor: pointer; font-size: 12px; font-weight: 600; color: #333; background: transparent;
                                                                    border: none; padding: 0; width: 75px; outline: none; box-shadow: none;
                                                                " readonly>
                                                            </div>
                                                            @endif
                                                        @endif
                                                    </li>
                                                    @if($saveAmount > 0)
                                                    <li><span class="text-success fw-bold">You Save ₹ <span id="saveAmount">{{ number_format($saveAmount,2) }}</span></span></li>
                                                    @endif
                                                </ul>
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
                                            @php
                                                $courseCombinations = collect($combinations ?? [])->filter(function($c) { return !empty($c->variant_name); })->values();
                                            @endphp
                                            @if($courseCombinations->count() > 0)
                                            <div class="mb-4">
                                                <h6 class="fw-bold mb-2 text-dark"><i class="fa fa-clock text-primary me-2"></i>Select Duration</h6>
                                                <div class="d-flex flex-wrap gap-2" id="durationSelectionContainer">
                                                    @foreach($courseCombinations as $index => $c)
                                                    <label class="duration-radio-label m-0" style="cursor: pointer;">
                                                        <input type="radio" name="selected_duration" value="{{ $c->combination_key }}" class="d-none duration-radio-input" data-price="{{ $c->price }}" data-regular="{{ $c->regular_price }}" data-variant-name="{{ $c->variant_name }}" {{ $index === 0 ? 'checked' : '' }}>
                                                        <div class="duration-card p-2 border rounded text-center bg-white transition">
                                                            <div class="small fw-bold text-dark">{{ $c->variant_name }}</div>
                                                            <div class="badge bg-light text-dark border mt-1">₹{{ $c->price }}</div>
                                                        </div>
                                                    </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                            <style>
                                                .duration-radio-input:checked + .duration-card {
                                                    border-color: #0d6efd !important;
                                                    background-color: #e8f0fe !important;
                                                    box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
                                                }
                                                .duration-card { transition: all 0.2s ease-in-out; min-width: 80px;}
                                                .duration-radio-label:hover .duration-card { border-color: #0d6efd; }
                                            </style>
                                            @endif
                                             @if(in_array($product->type, ['professional', 'vshop-professional', 'vtravel-professional', 'service', 'vshop-service', 'vtravel-service']))
                                                @php
                                                    $slots = json_decode($product->professional_slots, true);
                                                @endphp
                                                @if(!empty($slots) && is_array($slots))
                                                    @php
                                                        $availableDates = array_unique(array_column($slots, 'date'));
                                                        $slotsJson = json_encode($slots);
                                                        $availableDatesJson = json_encode(array_values($availableDates));
                                                    @endphp
                                                    <div class="professional-slots-sec mb-3">
                                                        <!-- Time Slots Row (appears after date is selected) -->
                                                        <div id="dynamicSlotsContainer" style="display:none;">
                                                            <div class="d-flex flex-wrap gap-2" id="slotsForSelectedDate">
                                                                <!-- Slots will be rendered here by JS -->
                                                            </div>
                                                        </div>

                                                        <!-- Selected Slots List -->
                                                        <div id="selectedSlotsWrapper" class="mt-2" style="display:none;">
                                                            <div id="selectedSlotsList" class="d-flex flex-wrap gap-2 align-items-center">
                                                                <!-- Selected items rendered here -->
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <style>
                                                        .date-picker-wrapper:hover {
                                                            background-color: #e8f0fe !important;
                                                            border-color: #0d6efd !important;
                                                        }
                                                        .slot-date-btn:hover, .slot-date-btn:focus, .slot-date-btn.active {
                                                            background-color: transparent !important;
                                                            border-color: transparent !important;
                                                            box-shadow: none !important;
                                                        }
                                                        .slot-date-btn::placeholder {
                                                            color: #212529 !important;
                                                            opacity: 1;
                                                        }
                                                        .slot-checkbox-input:checked + .slot-card {
                                                            border-color: #0d6efd !important;
                                                            background-color: #e8f0fe !important;
                                                            box-shadow: 0 0 0 2px rgba(13, 110, 253, 0.25);
                                                        }
                                                        .slot-card { transition: all 0.2s ease-in-out; }
                                                        .slot-checkbox-label:hover .slot-card { border-color: #0d6efd; }
                                                        
                                                        /* Flatpickr - position beside button, not on top */
                                                        .date-picker-wrapper .flatpickr-calendar {
                                                            font-size: 12px;
                                                            width: 280px !important;
                                                            margin-top: 4px;
                                                        }
                                                        .flatpickr-day { max-width: 35px; height: 35px; line-height: 35px; }
                                                        .flatpickr-day.has-slots { font-weight: bold; color: #0d6efd; background-color: #e8f0fe; border-radius: 50%; }
                                                        .flatpickr-day.has-slots:hover { background-color: #0d6efd; color: #fff; }
                                                        .flatpickr-day.has-slots.selected { background-color: #0d6efd; color: #fff; border-color: #0d6efd; }
                                                    </style>
                                                @endif
                                            @else
                                                <div class="product-dtl-sec">
                                                    <p>Expected Delivery : <span class="blackColor" id="deliveryDate"></span> /
                                                        @auth('customer')
                                                            <a href="{{ route('vshop.address.create') }}" class="change-pincode">Change Pincode</a>
                                                        @else
                                                            <a href="{{ route('vshoplogin') }}" class="change-pincode">Change Pincode</a>
                                                        @endauth
                                                    </p>
                                                </div>
                                            @endif

                                             @php
                                                 $isPhysicalProduct = in_array($product->type, ['product', 'vshop-product']) || empty($product->type);
                                                 $isOutOfStock = $isPhysicalProduct && (is_null($product->stock_qty) || $product->stock_qty === '' || $product->stock_qty <= 0);
                                             @endphp

                                             {{-- QUANTITY AND CART + ENQUIRY BUTTONS --}}
                                             <div class="action-row d-flex align-items-center gap-2 flex-wrap">
                                                 <!-- Quantity -->
                                                 <div class="qty-box d-flex align-items-center" style="{{ $isOutOfStock ? 'opacity: 0.5; pointer-events: none;' : '' }}">
                                                     <button type="button" id="decrementQty" {{ $isOutOfStock ? 'disabled' : '' }}>-</button>
                                                     <input type="text" id="quantity" value="1" readonly>
                                                     <button type="button" id="incrementQty" {{ $isOutOfStock ? 'disabled' : '' }}>+</button>
                                                 </div>

                                                 <!-- Add to Cart -->
                                                 <button class="btn-cart text-nowrap" id="addToCartBtn" data-product-id="{{ $product->id }}" {{ $isOutOfStock ? 'disabled' : '' }}>
                                                     Add to Cart ₹ <span id="totalPrice">{{ number_format($price, 2) }}</span>
                                                 </button>

                                                 <!-- Go to Cart -->
                                                 <a href="{{ route('vshop.cart.index') }}" class="btn-go-cart">Go to Cart</a>

                                                 <!-- Buy Now -->
                                                 <button class="btn-buy" id="buyNowBtn" data-product-id="{{ $product->id }}" {{ $isOutOfStock ? 'disabled' : '' }}>Buy</button>

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
                                                 @if(!$isPhysicalProduct)
                                                     {{-- Do not show stock status for services/professionals --}}
                                                 @elseif($isOutOfStock)
                                                     <p class="out-of-stock text-danger"><i class="fa-regular fa-times-circle"></i> Out of Stock (No stock available)</p>
                                                 @elseif($product->low_stock_threshold !== null && $product->stock_qty <= $product->low_stock_threshold)
                                                     <p class="in-stock text-warning" style="color: #ff9900 !important; font-weight: bold;"><i class="fa-solid fa-triangle-exclamation"></i> Low Stock! Only a few left<span id="availableStock" class="d-none">{{ $product->stock_qty }}</span></p>
                                                 @else
                                                     <p class="in-stock text-success"><i class="fa-regular fa-check-circle"></i> In Stock <span id="availableStock" class="d-none">{{ $product->stock_qty }}</span></p>
                                                 @endif
                                             </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- RIGHT AD SECTION --}}
                        <div class="col-lg-3 col-4">
                            <div class="product-advatice-sec">
                                <div class="right-product-img">
                                    <img id="mainProductImage" src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}">
                                </div>
                                <a href="{{ $product->seller && $product->seller->id ? route('vshop.store.show', $product->seller->id) : '#' }}" class="btn view-similar-btn mt-2 w-100">
                                    <i class="fa-solid fa-layer-group me-1"></i> View Similar
                                </a>
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

                                @if(!empty($product->product_specification))
                                <div class="product-details-heading mt-4 mb-3">
                                    <h4 class="text-primary font-weight-bold">Product Specifications</h4>
                                </div>
                                <div class="product-specification-content text-secondary" style="font-size: 14px; line-height: 1.6;">
                                    {!! $product->product_specification !!}
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
                                    <button class="btn btn-primary btn-sm" id="openReviewForm">{{ isset($existingReview) ? 'Edit Your Review' : 'Write a Review' }}</button>
                                </div>
                                <div id="reviewFormBox" style="display:none; margin-top:20px;" class="mb-4">
                                    @auth('customer')
                                    <form id="reviewSubmitForm" action="{{ route('vtravel.review.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="seller_id" value="{{ $product->created_by_id }}">
                                        <div class="review-box p-4 shadow-sm rounded bg-light border">
                                            <div class="mb-4 d-flex align-items-center gap-3">
                                                <label class="form-label mb-0"><strong>Rate this product</strong></label>
                                                <div class="star-rating-ui">
                                                    @php $currentRating = isset($existingReview) ? $existingReview->rating : 0; @endphp
                                                    @for($i=1; $i<=5; $i++)
                                                    <i class="fa-solid fa-star {{ $i <= $currentRating ? 'active' : '' }}" data-value="{{ $i }}"></i>
                                                    @endfor
                                                </div>
                                                <input type="hidden" name="rating" id="ratingValue" value="{{ $currentRating }}">
                                            </div>
                                            <div class="mb-3"><label class="form-label">Review Title</label><input type="text" name="title" class="form-control" value="{{ isset($existingReview) ? $existingReview->title : '' }}" required></div>
                                            <div class="mb-3"><label class="form-label">Your Review</label><textarea name="comment" class="form-control" rows="4" required>{{ isset($existingReview) ? $existingReview->comment : '' }}</textarea></div>
                                            <div class="mb-3">
                                                <label class="form-label d-block">Would you recommend?</label>
                                                <div class="form-check form-check-inline"><input type="radio" name="would_recommend" value="1" class="form-check-input" id="recommendYes" {{ (isset($existingReview) && $existingReview->would_recommend == 1) ? 'checked' : '' }} required><label class="form-check-label" for="recommendYes">Yes</label></div>
                                                <div class="form-check form-check-inline"><input type="radio" name="would_recommend" value="0" class="form-check-input" id="recommendNo" {{ (isset($existingReview) && $existingReview->would_recommend == 0) ? 'checked' : '' }} required><label class="form-check-label" for="recommendNo">No</label></div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100" id="submitReviewBtn">{{ isset($existingReview) ? 'Update Review' : 'Submit Review' }}</button>
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

                                        @if(!empty($review->admin_reply))
                                            <div class="seller-reply mt-3 p-3 bg-white border rounded">
                                                <h6 class="fw-bold text-primary mb-1" style="font-size: 14px;"><i class="fa-solid fa-reply"></i> Seller Reply</h6>
                                                <p class="mb-0 text-secondary small">{{ $review->admin_reply }}</p>
                                            </div>
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
                                        @if(!empty($product->refund_policy))
                                            <p class="text-secondary mb-0">{!! $product->refund_policy !!}</p>
                                        @else
                                            <h6 class="fw-bold text-dark mb-2">Shipping & Delivery Policy</h6>
                                            <p class="text-secondary mb-3">Standard delivery times are between 3 to 5 business days depending on your delivery address and location. Shipping charges will be calculated at checkout. You will receive a tracking number once your package has shipped.</p>
                                            
                                            <h6 class="fw-bold text-dark mb-2">Returns & Replacement Policy</h6>
                                            <p class="text-secondary mb-0">We support a 7-day return and replacement policy for items that are damaged, defective, or different from what was ordered. Returned products must be unused, in their original packaging, and with tags attached. Please contact support or request a refund through your customer dashboard.</p>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                    {{-- RELATED PRODUCTS --}}
                    @if($relatedProducts->isNotEmpty())
                    <div class="product-details-bottom-box" id="related-products-section">
                        <div class="product-details-bottom-box-header"><h3>Related Products</h3></div>
                        <div class="other-product-sec">
                            <div class="other-product-wrapper owl-carousel">
                                @foreach($relatedProducts as $relatedProduct)
                                <div class="other-product-box item">
                                    <div class="product-box">
                                        <div class="share-wishlist-sec">
                                            <a href="javascript:void(0);" class="share-btn" data-url="{{ route('productmanage.show', $relatedProduct->id) }}" data-title="{{ $relatedProduct->name }}" data-referral-url="{{ url('/p/' . $relatedProduct->id) }}" data-referral-code="{{ $referralCode }}"><i class="fa-solid fa-share-nodes"></i></a>
                                            <a href="#" class="wishlist-btn related-wishlist" data-product-id="{{ $relatedProduct->id }}"><i class="fa-solid fa-heart"></i></a>
                                        </div>
                                        <div class="product-box-img"><a href="{{ route('productmanage.show', $relatedProduct->id) }}"><img src="{{ asset($relatedProduct->thumbnail) }}" alt="{{ $relatedProduct->name }}"></a></div>
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
    <div class="share-content" style="border-radius: 12px; max-width: 420px; box-shadow: 0 10px 25px rgba(0,0,0,0.15); border: 1px solid #e2e8f0; padding: 20px;">
        <div class="share-header" style="margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center;">
            <h4 style="font-weight: 700; color: #1e293b; font-size: 16px; margin: 0;">Share Product</h4>
            <span class="close-share" style="font-size: 20px; font-weight: bold; cursor: pointer; color: #64748b;">&times;</span>
        </div>
        <div class="share-options" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 15px;">
            <button class="share-item whatsapp" style="padding: 12px; border-radius: 8px; font-weight: 600;"><i class="fa-brands fa-whatsapp"></i><span>WhatsApp</span></button>
            <button class="share-item facebook" style="padding: 12px; border-radius: 8px; font-weight: 600;"><i class="fa-brands fa-facebook"></i><span>Facebook</span></button>
            <button class="share-item copy" style="padding: 12px; border-radius: 8px; font-weight: 600;"><i class="fa-solid fa-link"></i><span>Copy Link</span></button>
        </div>

        <hr style="border-top: 1px solid #e2e8f0; margin: 15px 0;">

        <!-- Refer & Earn Section -->
        <div class="refer-earn-section">
            <h5 style="font-size: 13px; font-weight: 700; color: #7e22ce; margin-top: 0; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-gift"></i> Refer & Earn
            </h5>
            @if(!empty($referralCode))
                <div class="referral-link-container" style="display: flex; gap: 8px; border: 1px solid #e2e8f0; border-radius: 8px; padding: 6px 10px; background: #faf5ff; align-items: center;">
                    <input type="text" id="shareReferralLink" readonly value="{{ url('/p/' . $product->id) . '?ref=' . $referralCode }}" style="flex: 1; border: none; background: transparent; font-size: 11px; color: #4a5568; outline: none; font-weight: 500; min-width: 0;">
                    <button class="btn btn-sm btn-purple" id="copyReferralLinkBtn" style="background: #7e22ce; color: #fff; border: none; font-size: 11px; font-weight: 600; padding: 6px 12px; border-radius: 6px; cursor: pointer; transition: 0.2s;">Copy</button>
                </div>
            @else
                <div style="background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: 8px; padding: 12px; text-align: center;">
                    <p style="font-size: 11px; color: #64748b; margin-bottom: 8px; font-weight: 500; line-height: 1.4;">Log in to generate your referral link and earn commissions!</p>
                    <a href="{{ route('vshoplogin') }}" class="btn btn-sm text-white" style="background: #7e22ce; font-size: 11px; font-weight: 600; padding: 5px 12px; border-radius: 4px; text-decoration: none; display: inline-block;">Login to Refer</a>
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<!-- Flatpickr for Slot Selection -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
@if(isset($slotsJson) && isset($availableDatesJson))
    const productSlots = {!! $slotsJson !!};
    const availableDates = {!! $availableDatesJson !!};
    const selectedSlotsData = new Map(); // Keep track of checked slots: index -> slot object

    $(document).ready(function() {
        // Group slots by date
        const slotsByDate = {};
        productSlots.forEach((s, idx) => {
            if (!slotsByDate[s.date]) slotsByDate[s.date] = [];
            slotsByDate[s.date].push({ ...s, originalIndex: idx });
        });

        // Initialize Flatpickr
        const fp = flatpickr("#slotCalendarPicker", {
            inline: false,
            position: 'below',
            onDayCreate: function(dObj, dStr, fp, dayElem) {
                // Highlight available dates in blue
                const dateStr = fp.formatDate(dayElem.dateObj, "Y-m-d");
                if (availableDates.includes(dateStr)) {
                    dayElem.classList.add('has-slots');
                }
            },
            onChange: function(selectedDates, dateStr, instance) {
                renderSlotsForDate(dateStr);
            }
        });

        function renderSlotsForDate(dateStr) {
            $('#dynamicSlotsContainer').show();
            
            const container = $('#slotsForSelectedDate');
            container.empty();

            const slots = slotsByDate[dateStr] || [];
            if (slots.length === 0) {
                container.html('<p class="text-muted small w-100 m-0">No slots available on this date.</p>');
                return;
            }

            slots.forEach(slot => {
                const isChecked = selectedSlotsData.has(slot.originalIndex) ? 'checked' : '';
                const html = `
                    <label class="slot-checkbox-label m-0" style="cursor: pointer;">
                        <input type="checkbox" name="selected_slots[]" value="${slot.originalIndex}" class="d-none slot-checkbox-input" data-date="${slot.date}" data-time="${slot.start} - ${slot.end}" ${isChecked}>
                        <div class="slot-card p-1 px-2 border rounded text-center bg-white transition" style="min-width: 80px;">
                            <div class="small fw-bold text-dark" style="font-size:11px;">${slot.start} - ${slot.end}</div>
                        </div>
                    </label>
                `;
                container.append(html);
            });
        }

        // Handle checkbox changes via event delegation
        $(document).on('change', '.slot-checkbox-input', function() {
            const index = parseInt($(this).val());
            const date = $(this).data('date');
            const time = $(this).data('time');
            
            if ($(this).is(':checked')) {
                selectedSlotsData.set(index, { date, time });
            } else {
                selectedSlotsData.delete(index);
            }
            updateSelectedSlotsUI();
        });

        function updateSelectedSlotsUI() {
            const wrapper = $('#selectedSlotsWrapper');
            const list = $('#selectedSlotsList');
            list.empty();

            if (selectedSlotsData.size === 0) {
                wrapper.hide();
                return;
            }
            
            wrapper.show();
            
            // Add title badge
            list.append('<span class="fw-bold text-success" style="font-size:12px;"><i class="fa fa-check-circle me-1"></i>Selected:</span>');
            
            // Sort keys so they appear in order
            const sortedIndexes = Array.from(selectedSlotsData.keys()).sort((a, b) => a - b);
            
            sortedIndexes.forEach(idx => {
                const s = selectedSlotsData.get(idx);
                const niceDate = flatpickr.formatDate(new Date(s.date), "d M");
                list.append(`
                    <span class="badge bg-white text-dark border border-primary d-inline-flex align-items-center px-2 py-1" style="font-size:11px; border-radius: 20px;">
                        <span class="fw-bold text-primary me-1">${niceDate}</span> 
                        <span class="text-muted me-2">${s.time}</span>
                        <i class="fa fa-times text-danger remove-selected-slot bg-light rounded-circle d-flex align-items-center justify-content-center" data-index="${idx}" style="cursor:pointer; font-size:9px; width:16px; height:16px;"></i>
                    </span>
                `);
            });
        }

        // Remove slot from selected list
        $(document).on('click', '.remove-selected-slot', function() {
            const index = parseInt($(this).data('index'));
            selectedSlotsData.delete(index);
            // Uncheck the checkbox if it's currently visible
            $(`.slot-checkbox-input[value="${index}"]`).prop('checked', false);
            updateSelectedSlotsUI();
        });
    });
@endif
</script>

<script>
// ========== SHARE MODAL ==========
let shareUrl = '';
let shareTitle = '';

$(document).on('click','.share-btn',function(){
    shareUrl = $(this).data('url');
    shareTitle = $(this).data('title');
    
    // Dynamic referral code & url update
    let referralCode = $(this).data('referral-code');
    let referralBaseUrl = $(this).data('referral-url');
    if (referralCode && referralBaseUrl) {
        let fullReferralUrl = referralBaseUrl + '?ref=' + referralCode;
        $('#shareReferralLink').val(fullReferralUrl);
    }
    
    $('#shareModal').addClass('active');
});

$(document).on('click','.close-share',function(){
    $('#shareModal').removeClass('active');
});

$(document).on('click','#shareModal',function(e){
    if(e.target.id === 'shareModal') $(this).removeClass('active');
});

$(document).on('click','.whatsapp',function(){
    let refInput = document.getElementById('shareReferralLink');
    let urlToShare = (refInput && refInput.value) ? refInput.value : shareUrl;
    let url = "https://wa.me/?text=" + encodeURIComponent(shareTitle + " " + urlToShare);
    window.open(url, '_blank');
});

$(document).on('click','.facebook',function(){
    let refInput = document.getElementById('shareReferralLink');
    let urlToShare = (refInput && refInput.value) ? refInput.value : shareUrl;
    let url = "https://www.facebook.com/sharer/sharer.php?u=" + encodeURIComponent(urlToShare);
    window.open(url, '_blank');
});

$(document).on('click','.copy',function(){
    navigator.clipboard.writeText(shareUrl).then(function() {
        showToast("Product link copied to clipboard!", "success");
    }).catch(function() {
        showToast("Failed to copy link", "error");
    });
});

$(document).on('click','#copyReferralLinkBtn',function(e){
    e.preventDefault();
    let refInput = document.getElementById('shareReferralLink');
    if (refInput && refInput.value) {
        navigator.clipboard.writeText(refInput.value).then(function() {
            showToast("Referral link copied to clipboard!", "success");
        }).catch(function() {
            refInput.select();
            document.execCommand("copy");
            showToast("Referral link copied to clipboard!", "success");
        });
    }
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

        const submitEnquiry = (reason) => {
            if (contactType !== 'phone') {
                Swal.fire({ title: 'Submitting...', text: 'Please wait', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            } else {
                Swal.fire({ title: 'Connecting...', text: 'Fetching seller number', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
            }

            $.ajax({
                url: "{{ route('vshop.enquiry.store') }}",
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    product_id: productId,
                    reason: reason,
                    contact_type: contactType
                },
                success: function(response) {
                    if (response.success) {
                        // If WhatsApp contact type AND seller has phone → open wa.me
                        if (contactType === 'whatsapp' && response.seller_phone) {
                            let waMessage = `Hi, I'm interested in *${response.product_name || 'your product'}*.\n\nEnquiry No: ${response.enquiry_no || ''}\nMessage: ${reason}\n\nPlease share more details. Thank you!`;
                            let waUrl = `https://wa.me/${response.seller_phone}?text=${encodeURIComponent(waMessage)}`;
                            
                            Swal.fire({
                                icon: 'success',
                                title: '✅ Enquiry Saved!',
                                html: `<p>Your enquiry has been saved successfully.</p>
                                       <p class="mb-1"><strong>Click below to message the seller on WhatsApp:</strong></p>
                                       <a href="${waUrl}" target="_blank" class="btn btn-success btn-lg mt-2" style="background:#25D366;border:none;font-size:16px;padding:10px 30px;border-radius:30px;">
                                           <i class="fab fa-whatsapp"></i> Chat with Seller on WhatsApp
                                       </a>`,
                                showConfirmButton: false,
                                showCloseButton: true,
                            });
                        } else if (contactType === 'phone' && response.seller_phone) {
                            let telUrl = `tel:+${response.seller_phone}`;
                            window.location.href = telUrl;
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Calling...',
                                text: 'Opening your phone dialer...',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else if (contactType === 'email') {
                            Swal.fire({ 
                                icon: 'success', 
                                title: '📧 Email Sent!', 
                                text: 'Your enquiry has been successfully emailed to the seller. They will reply to you soon.', 
                                confirmButtonColor: '#28a745' 
                            });
                        } else {
                            Swal.fire({ icon: 'success', title: 'Enquiry Sent!', text: response.message, confirmButtonColor: '#28a745' });
                        }
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
        };

        if (contactType === 'phone') {
            // Direct call: bypass popup, submit with default message, dial instantly
            submitEnquiry('Customer clicked to call the seller directly.');
        } else {
            // WhatsApp & Email: Ask for message first
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
                    submitEnquiry(result.value);
                }
            });
        }
    });
});

// ========== THE REST OF YOUR EXISTING JAVASCRIPT (product, cart, wishlist, etc.) ==========
// (Paste your complete existing JavaScript here – unchanged)
// ...

// For brevity, the full original JS is not repeated here – but you MUST keep all your existing JS.
// The code below is a placeholder – in your final file, replace it with the exact JS you already have.

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
                if (productConfig.isPhysicalProduct) {
                    if (productConfig.combinations.length > 0) {
                        let record = productConfig.combinations.find(c => c.combination_key == matchedKey);

                        if (record && record.product_stock !== undefined && record.product_stock <= 0) {
                            showToast("Out of stock", "error");
                            return;
                        }
                    } else {
                        if (productConfig.mainStock === null || productConfig.mainStock <= 0) {
                            showToast("Out of stock", "error");
                            return;
                        }
                        if (productConfig.quantity > productConfig.mainStock) {
                            showToast(`Only ${productConfig.mainStock} items available`, "error");
                            return;
                        }
                    }
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

                // Capture selected slots for professional services
                const selectedSlotBoxes = document.querySelectorAll('input[name="selected_slots[]"]:checked');
                if (document.querySelector('.professional-slots-sec')) {
                    if (selectedSlotBoxes.length === 0) {
                        showToast("Please select at least one date & time slot", "error");
                        return;
                    }
                    selectedSlotBoxes.forEach(box => {
                        params.append("selected_slots[]", box.dataset.date + " | " + box.dataset.time);
                    });
                }

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
            basePrice: {{ $product->price }},
            baseRegularPrice: {{ $product->regular_price ?? $product->price }},
            quantity: 1,
            selectedAttributes: {},
            combinations: @json($combinations),
            mainStock: {{ is_null($product->stock_qty) || $product->stock_qty === '' ? 'null' : $product->stock_qty }},
            lowStockThreshold: {{ is_null($product->low_stock_threshold) || $product->low_stock_threshold === '' ? 'null' : $product->low_stock_threshold }},
            isPhysicalProduct: {{ in_array($product->type, ['product', 'vshop-product']) || empty($product->type) ? 'true' : 'false' }}
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
        if (!s && !c) {
            updateStockStatus(productConfig.mainStock, true);
        }

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

    function updatePrice() {
        let selectedTermIds = [];
        Object.values(productConfig.selectedAttributes).forEach(attr => {
            selectedTermIds.push(parseInt(attr.termId));
        });

        const totalAttributeRows = document.querySelectorAll('.variant-row').length;

        if (selectedTermIds.length === 0) {
            updateStockStatus(productConfig.mainStock, true);
            return;
        }

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
            updateStockStatus(record.stock, true);
        } else {
            if (selectedTermIds.length === totalAttributeRows) {
                updateStockStatus(0, false);
            } else {
                updateStockStatus(productConfig.mainStock, true);
            }
        }
    }

    // ================= UPDATE STOCK STATUS =================
    function updateStockStatus(stock, combinationValid) {
        const stockStatus = document.getElementById('stockStatus');
        const addToCartBtn = document.getElementById('addToCartBtn');
        const buyNowBtn = document.getElementById('buyNowBtn');
        if (!stockStatus) return;

        if (!productConfig.isPhysicalProduct) {
            stockStatus.innerHTML = '';
            if (addToCartBtn) addToCartBtn.disabled = false;
            if (buyNowBtn) buyNowBtn.disabled = false;
            return;
        }

        if (!combinationValid && productConfig.currentSize && productConfig.currentColor) {
            stockStatus.innerHTML = `<p class="out-of-stock text-danger">
                <i class="fa-regular fa-times-circle"></i> 
                Combination Not Available
            </p>`;
            if (addToCartBtn) addToCartBtn.disabled = true;
            if (buyNowBtn) buyNowBtn.disabled = true;
        } else if (stock === null || stock === undefined || stock === '' || stock <= 0) {
            stockStatus.innerHTML = `<p class="out-of-stock text-danger">
                <i class="fa-regular fa-times-circle"></i> 
                Out of Stock
            </p>`;
            if (addToCartBtn) addToCartBtn.disabled = true;
            if (buyNowBtn) buyNowBtn.disabled = true;
        } else if (productConfig.lowStockThreshold !== null && stock <= productConfig.lowStockThreshold) {
            stockStatus.innerHTML = `<p class="in-stock text-warning" style="color: #ff9900 !important; font-weight: bold;">
                <i class="fa-solid fa-triangle-exclamation"></i> 
                Low Stock! Only <span id="availableStock">${stock}</span> left
            </p>`;
            if (addToCartBtn) addToCartBtn.disabled = false;
            if (buyNowBtn) buyNowBtn.disabled = false;
        } else {
            stockStatus.innerHTML = `<p class="in-stock text-success">
                <i class="fa-regular fa-check-circle"></i> 
                In Stock <span id="availableStock" class="d-none">${stock}</span>
            </p>`;
            if (addToCartBtn) addToCartBtn.disabled = false;
            if (buyNowBtn) buyNowBtn.disabled = false;
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
            let maxStock = 1000;

            if (productConfig.isPhysicalProduct) {
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
                        if (combinationRecord && combinationRecord.product_stock !== undefined) {
                            maxStock = parseInt(combinationRecord.product_stock);
                        }
                    }
                } else {
                    if (productConfig.mainStock !== null) {
                        maxStock = productConfig.mainStock;
                    }
                }
            }

            if (productConfig.quantity < maxStock) {
                productConfig.quantity++;
                qtyInput.value = productConfig.quantity;
                updateTotalPrice();
            } else if (productConfig.isPhysicalProduct) {
                showToast(`Only ${maxStock} items available`, "error");
            }
        });
    }

    // Handle Duration change for Course products
    const durationRadios = document.querySelectorAll('.duration-radio-input');
    if (durationRadios.length > 0) {
        // Trigger initial state
        const checkedRadio = document.querySelector('.duration-radio-input:checked');
        if(checkedRadio) {
            const price = parseFloat(checkedRadio.dataset.price);
            const regular = parseFloat(checkedRadio.dataset.regular) || price;
            updatePriceUI(price, regular);
            updateTotalPrice();
        }

        durationRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                const price = parseFloat(this.dataset.price);
                const regular = parseFloat(this.dataset.regular) || price;
                updatePriceUI(price, regular);
                updateTotalPrice();
            });
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

                    if (data.wishlist_count !== undefined) {
                        let countEl = document.getElementById('wishlistCount');
                        if (countEl) countEl.innerText = data.wishlist_count;
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

    // ================= REFER & EARN BUTTON =================
    const referEarnBtn = document.getElementById("referEarnBtn");
    if (referEarnBtn) {
        referEarnBtn.addEventListener("click", function(e) {
            e.preventDefault();
            const refCode = this.getAttribute("data-ref");
            if (!refCode) {
                showToast("Please login first to refer this product", "error");
                setTimeout(function() {
                    window.location.href = "{{ route('vshoplogin') }}";
                }, 1500);
                return;
            }
            const baseUrl = this.getAttribute("data-url");
            const referralLink = baseUrl + "?ref=" + refCode;
            
            // Copy to clipboard
            navigator.clipboard.writeText(referralLink).then(function() {
                showToast("Referral link copied to clipboard!", "success");
            }).catch(function(err) {
                // Fallback
                const tempInput = document.createElement("input");
                tempInput.value = referralLink;
                document.body.appendChild(tempInput);
                tempInput.select();
                document.execCommand("copy");
                document.body.removeChild(tempInput);
                showToast("Referral link copied to clipboard!", "success");
            });
        });
    }

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
            if (productConfig.isPhysicalProduct) {
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
                    if (combinationRecord && combinationRecord.product_stock !== undefined && combinationRecord.product_stock <= 0) {
                        showToast("This combination is out of stock", "error");
                        return;
                    }
                    if (combinationRecord && combinationRecord.product_stock !== undefined && productConfig.quantity > combinationRecord.product_stock) {
                        showToast(`Only ${combinationRecord.product_stock} items available`, "error");
                        return;
                    }
                } else {
                    if (productConfig.mainStock === null || productConfig.mainStock <= 0) {
                        showToast("This product is out of stock", "error");
                        return;
                    }
                    if (productConfig.quantity > productConfig.mainStock) {
                        showToast(`Only ${productConfig.mainStock} items available`, "error");
                        return;
                    }
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

            // Capture selected slots for professional services
            const selectedSlotBoxes = document.querySelectorAll('input[name="selected_slots[]"]:checked');
            if (document.querySelector('.professional-slots-sec')) {
                if (selectedSlotBoxes.length === 0) {
                    showToast("Please select at least one date & time slot", "error");
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    return;
                }
                payload.selected_slots = Array.from(selectedSlotBoxes).map(box => {
                    return box.dataset.date + " | " + box.dataset.time;
                });
            }

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

            // Also check for selected course duration
            const selectedDuration = document.querySelector('.duration-radio-input:checked');
            if (selectedDuration) {
                payload.combination_key = selectedDuration.value;
                payload.variant_name = selectedDuration.dataset.variantName;
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

    // Review Form AJAX submission
    const reviewForm = document.getElementById('reviewSubmitForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            let ratingValue = document.getElementById('ratingValue').value;
            if (!ratingValue || ratingValue == "0") {
                Swal.fire('Warning', 'Please select a star rating.', 'warning');
                return;
            }

            let btn = document.getElementById('submitReviewBtn');
            let originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
            btn.disabled = true;

            let formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({
                        title: 'Thank You!',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#004392'
                    }).then(() => {
                        document.getElementById('reviewFormBox').style.display = 'none';
                        document.getElementById('openReviewForm').innerText = 'Edit Your Review';
                        document.getElementById('submitReviewBtn').innerText = 'Update Review';
                    });
                } else {
                    let msg = data.message || 'Something went wrong';
                    if (data.errors) {
                        msg = Object.values(data.errors).map(val => val.join(', ')).join('<br>');
                    }
                    Swal.fire('Error', msg, 'error');
                }
            })
            .catch(error => {
                console.error(error);
                Swal.fire('Error', 'Something went wrong. Please try again.', 'error');
            })
            .finally(() => {
                if (btn.innerText.includes('Processing')) {
                    btn.innerHTML = originalText;
                }
                btn.disabled = false;
            });
        });
    }
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
.share-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.4); display: none; justify-content: center; align-items: center; z-index: 9999; }
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

<script>
    function changeMainImage(element) {
        document.getElementById('mainImage').src = element.src;
    }
</script>

<script>
    $(document).ready(function(){
        $(".other-product-wrapper").owlCarousel({
            loop: true,
            margin: 20,
            nav: false,
            dots: false,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplayHoverPause: true,
            responsive: {
                0: { items: 1 },
                600: { items: 2 },
                1000: { items: 4 }
            }
        });

        // Handle 'See customer reviews' links to open Reviews tab
        $(document).on('click', 'a[href="#reviews"]', function(e) {
            e.preventDefault();
            var reviewTabBtn = $('#reviews-tab');
            if (reviewTabBtn.length) {
                reviewTabBtn.trigger('click');
                $('html, body').animate({
                    scrollTop: $("#productDetailTabs").offset().top - 80
                }, 500);
            }
        });
    });
</script>
@endpush

@endsection
