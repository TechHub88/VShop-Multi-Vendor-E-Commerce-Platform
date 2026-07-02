@extends('vshop.main')

@section('title','Products')

@section('styles')

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />
@endsection

@section('content')

<style>
    /* Override dark blue header for modern Flipkart/Amazon clean look */
    .home-seller-header {
        background: transparent !important;
        color: #212121 !important;
        padding: 0 0 8px 0 !important;
        margin-bottom: 10px !important;
        border-bottom: 1px solid #f0f0f0;
        display: flex;
        align-items: center;
    }
    
    .home-seller-header h2 {
        font-weight: 600 !important;
        font-size: 15px !important;
        color: #212121 !important;
        margin: 0 !important;
    }
    
    .home-seller-header i {
        color: #2874f0 !important;
        font-size: 14px !important;
    }

    /* Clean overrides matching homepage compact sizes */
    .product-bottom-sec {
        flex-direction: column-reverse;
        align-items: stretch !important;
        gap: 4px !important;
        margin-top: 4px !important;
    }
    .product-box {
        padding: 0 !important;
        border-radius: 8px !important;
        box-shadow: 0px 2px 8px rgba(0, 0, 0, 0.04) !important;
        border: 1px solid #e2e8f0 !important;
        height: 200px !important;
        display: flex !important;
        flex-direction: column !important;
        overflow: hidden;
        position: relative;
    }
    .product-box-img {
        height: 100px !important;
        background: #fff !important;
        padding: 6px !important;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .product-box-img a {
        height: 100px !important;
        border-radius: 0 !important;
        display: flex !important;
        align-items: center;
        justify-content: center;
        width: 100%;
    }
    .product-box-img a img {
        object-fit: contain !important;
        max-width: 100% !important;
        max-height: 100% !important;
    }
    .product-bottom {
        padding: 4px 6px 6px 6px !important;
        flex-grow: 1;
        display: flex !important;
        flex-direction: column !important;
    }
    .product-bottom h3 {
        margin-bottom: 1px !important;
        height: 14px !important;
        overflow: hidden;
    }
    .product-bottom h3 a {
        font-size: 11px !important;
        line-height: 14px !important;
        font-weight: 600 !important;
        color: #2d3748 !important;
        display: -webkit-box !important;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .share-wishlist-sec {
        position: absolute;
        top: 4px;
        right: 4px;
        z-index: 10;
        display: flex;
        gap: 4px;
    }
    .share-wishlist-sec a {
        width: 20px !important;
        height: 20px !important;
        background: rgba(255, 255, 255, 0.9) !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important;
        text-decoration: none !important;
        color: #718096 !important;
    }
    .share-wishlist-sec a i {
        font-size: 9px !important;
    }
    /* Hide the large rating dropdown panel */
    .custom-rating-dropdown .dropdown-menu {
        display: none !important;
    }
    .amazon-rating-sec .fa-chevron-down {
        display: none !important;
    }
    .amazon-rating-sec {
        cursor: default !important;
    }
    .rating-size-sec {
        margin-bottom: 1px !important;
        gap: 2px !important;
    }
    .rating-num {
        font-size: 10px !important;
    }
    .amazon-rating-sec .stars i {
        font-size: 8px !important;
    }
    .rating-count {
        font-size: 10px !important;
    }
    .rating-sec {
        font-size: 9px !important;
        color: #4a5568 !important;
        gap: 2px !important;
    }
    .rating-sec i {
        font-size: 8px !important;
    }
    .color-size-sec {
        font-size: 9px !important;
        font-weight: 500 !important;
        color: #718096 !important;
    }
    .off-sec {
        margin-bottom: 1px !important;
        justify-content: flex-start !important;
        gap: 4px !important;
    }
    .old-rupess {
        font-size: 9px !important;
        font-weight: 500 !important;
    }
    .off-box {
        font-size: 9px !important;
        font-weight: 700 !important;
    }
    .new-rupess {
        font-size: 11px !important;
        font-weight: 700 !important;
        margin: 0 !important;
    }
    .add-cart-btn {
        font-size: 9px !important;
        padding: 3px 6px !important;
        border-radius: 4px !important;
        text-align: center !important;
        width: 100% !important;
    }
    .home-seller-sec {
        margin-bottom: 4px !important;
    }
    .home-seller-sec.mb-5 {
        margin-bottom: 4px !important;
    }
    .campaign-banner-sec {
        margin-bottom: 2px !important;
    }
    .campaign-banner-sec.mb-4 {
        margin-bottom: 2px !important;
    }
    .home-seller-header.mb-3 {
        margin-bottom: 4px !important;
    }
    .home-seller-bottom .row {
        margin-bottom: 0 !important;
    }
    .home-seller-bottom .row .mb-4 {
        margin-bottom: 4px !important;
    }
    .custom-pagination-wrapper {
        margin-top: 0px !important;
        margin-bottom: 2px !important;
    }
    .custom-pagination-wrapper .pagination {
        margin: 0 !important;
    }
    .custom-pagination-wrapper .page-link {
        font-size: 11px !important;
        padding: 3px 8px !important;
    }
    .product-list-right {
        padding-top: 0 !important;
    }

    /* Prevent dropdown clipping in product cards */
    .product-box, .item {
        overflow: visible !important;
        position: relative;
    }
    .product-box:hover, .product-box:focus-within, .product-box:has(.show) {
        z-index: 9999;
    }
    .product-box-img {
        overflow: hidden;
    }

    .filter-top {
        display: none;
    }
    @media(max-width: 991px) {
        .product-list-filter-sec {
             display: block; 
        }
        /* Sidebar */
        #filterSidebar {
          position: fixed;
          top: 0;
          right: -320px; /* hidden */
          width: 320px;
          height: 100%;
          background: #fff;
          box-shadow: -2px 0 10px rgba(0,0,0,0.1);
          transition: 0.3s ease;
          z-index: 1000;
        }
        
        /* Active sidebar */
        #filterSidebar.active {
          right: 0;
        }
        
        /* Overlay background */
        #overlay {
          position: fixed;
          top: 0;
          left: 0;
          width: 100%;
          height: 100%;
          background: rgba(0,0,0,0.4);
          opacity: 0;
          visibility: hidden;
          transition: 0.3s;
          z-index: 999;
        }
        
        #overlay.active {
          opacity: 1;
          visibility: visible;
        }
        .product-list-filter-sec {
            padding-left: 18px;
        }
        .filter-top {
            display: block;
            padding: 12px 0;
        }
        #closeSidebar {
            background: transparent;
            border: 0;
            color: #004392;
            font-size: 22px;
            height: auto;
            padding: 0;
        }
        svg {
            width: 20px;
            height: 20px;
        }
        .productColumnBtnArea {
            display: none;
        }
    }

    /* ===== FLIPKART STYLE STYLES ===== */
    .price-slider-wrapper {
        position: relative;
        width: 100%;
        height: 30px;
        margin-top: 15px;
    }
    .price-slider-wrapper input[type="range"] {
        position: absolute;
        width: 100%;
        height: 5px;
        top: 50%;
        transform: translateY(-50%);
        background: none;
        pointer-events: none;
        -webkit-appearance: none;
        appearance: none;
        margin: 0;
        padding: 0;
        border: none;
        z-index: 2;
    }
    .price-slider-wrapper input[type="range"]::-webkit-slider-thumb {
        height: 18px;
        width: 18px;
        border-radius: 50%;
        background: #2874f0; /* Flipkart blue */
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        cursor: pointer;
        pointer-events: auto;
        -webkit-appearance: none;
        appearance: none;
        z-index: 2;
        position: relative;
    }
    .price-slider-wrapper input[type="range"]::-moz-range-thumb {
        height: 18px;
        width: 18px;
        border-radius: 50%;
        background: #2874f0;
        border: 2px solid #fff;
        box-shadow: 0 1px 3px rgba(0,0,0,0.3);
        cursor: pointer;
        pointer-events: auto;
        z-index: 2;
        position: relative;
    }
    .slider-track {
        position: absolute;
        width: 100%;
        height: 6px;
        top: 50%;
        transform: translateY(-50%);
        background: #e0e0e0;
        border-radius: 3px;
        z-index: 1;
    }
    .price-label {
        font-size: 11px;
        color: #878787;
        margin-bottom: 3px;
        display: block;
    }
    .price-to-text {
        color: #878787;
        font-size: 14px;
    }

    /* Sidebar Filter styling */
    .filter-sec {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        box-shadow: none;
        padding: 10px;
        margin-bottom: 20px;
    }
    .filter-box {
        padding: 8px 0;
        border-bottom: 1px solid #f2f2f2;
    }
    .filter-box:last-child {
        border-bottom: none;
    }
    .filter-box h3 {
        font-size: 11px;
        font-weight: 700;
        color: #878787;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 10px;
        position: relative;
    }
    .filter-box ul {
        list-style: none;
        padding: 0;
        margin: 0;
        max-height: 200px;
        overflow-y: auto;
    }
    /* Custom Scrollbar for filter lists */
    .filter-box ul::-webkit-scrollbar {
        width: 4px;
    }
    .filter-box ul::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }
    .filter-box li {
        margin-bottom: 10px;
    }
    .filter-box li:last-child {
        margin-bottom: 0;
    }
    /* Radio and Checkbox Styling */
    .filter-list {
        display: flex;
        align-items: center;
        position: relative;
        padding-left: 28px;
        cursor: pointer;
        font-size: 12px;
        color: #212121;
        user-select: none;
        margin: 0;
    }
    .filter-list input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 0;
        width: 0;
    }
    .checkmark {
        position: absolute;
        top: 2px;
        left: 0;
        height: 18px;
        width: 18px;
        background-color: #fff;
        border: 1.5px solid #c2c2c2;
        border-radius: 2px;
    }
    .filter-list input:checked ~ .checkmark {
        background-color: #2874f0;
        border-color: #2874f0;
    }
    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }
    .filter-list input:checked ~ .checkmark:after {
        display: block;
    }
    .filter-list .checkmark:after {
        left: 5px;
        top: 2px;
        width: 5px;
        height: 9px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
    }

    /* Radio button specific style */
    .filter-list input[type="radio"] ~ .checkmark {
        border-radius: 50%;
    }
    .filter-list input[type="radio"]:checked ~ .checkmark:after {
        left: 5px;
        top: 5px;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background: white;
        border: none;
    }

    /* Color filter styling */
    .filter-color-box ul {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }
    .color-box {
        position: relative;
        cursor: pointer;
        margin: 0;
    }
    .color-box input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }
    .color-circle {
        display: inline-block;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        border: 1.5px solid #e0e0e0;
        position: relative;
        transition: 0.2s;
    }
    .color-box input:checked ~ .color-circle {
        border-color: #2874f0;
        transform: scale(1.1);
        box-shadow: 0 0 4px rgba(40,116,240,0.5);
    }
    .color-box input:checked ~ .color-circle::after {
        content: "\f00c";
        font-family: "Font Awesome 6 Free";
        font-weight: 900;
        color: #fff;
        font-size: 10px;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        text-shadow: 0 1px 2px rgba(0,0,0,0.5);
    }

    /* Product Card Styling */
    .product-box {
        background: #fff;
        /*border-radius: 8px;*/
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
    }
    .product-bottom h3 a:hover {
        color: #2874f0;
    }
    .rating-sec {
        background: none !important;
        color: #212121;
        font-size: 13px;
        font-weight: 600;
        padding: 0;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 3px;
        margin-right: 8px;
    }
    .rating-sec i {
        font-size: 12px;
    }
    .fa-star.text-warning {
        color: #2874f0 !important;
    }
    .rating-size-sec {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }
    .color-size-sec {
        font-size: 12px;
        color: #878787;
        margin: 0;
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
        font-size: 15px;
        font-weight: 600;
        color: #212121;
        margin: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        text-align: right;
    }
    .add-cart-btn {
        background: #2874f0; /* Blue */
        color: #fff;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.2s;
    }
    .add-cart-btn:hover {
        background: #004392;
    }
    .back-category-link {
        font-size: 12px;
        color: #2874f0;
        text-decoration: none;
        font-weight: 500;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-bottom: 12px;
        transition: 0.2s;
    }
    .back-category-link:hover {
        color: #004392;
        text-decoration: underline;
    }
    
    .product-dtl-banner {
        border-radius: 20px;
        overflow: hidden;
    }
    .product-bottom h3 a {
        display: -webkit-box !important;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
    }
    .add-cart-btn {
        display: inline-block;
        background: #004392;
        border-radius: 30px;
        color: #FFFFFF;
        font-size: 13px;
        font-weight: 500;
        padding: 6px 14px;
        border: 0;
        white-space: nowrap;
        flex-shrink: 0;
    }
    .product-box-img a img {
        object-fit: contain;
    }
    .btn-primary {
        background: #004392 !important;
    }
    
    .price-range-input{
    pointer-events:none;
}

.price-range-input::-webkit-slider-thumb{
    pointer-events:auto;
}

/* Amazon Style Rating */
.amazon-rating-sec {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 13px;
    margin-bottom: 4px;
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
    font-size: 11px;
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

<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
<div class="dashboardPosPagesLeftContentonner">
<div class="dashboardDtlsArea">
<div class="dashboardDtlsAreaBody">

{{-- ================= TOP BAR ================= --}}

<div class="bed-cum-sec">
<button class="back-btn" onclick="window.history.back()">
<i class="fa-solid fa-arrow-left"></i>
</button>
<ul>
<li>Home /</li>
<li>Products</li>
</ul>
</div>

        @php
            $topAds = collect();
            if(isset($bannerAds)) $topAds = $topAds->merge($bannerAds);
            
            // Duplicate to force scrolling if there's only 1 banner
            if ($topAds->count() == 1) {
                $topAds->push($topAds->first());
            }
        @endphp

        @if($topAds->count() > 0 && $topAds->contains(function($ad) { return $ad->media->count() > 0; }))
        <div class="product-dtl-banner">
            <div id="bannerCarousel" class="carousel slide" data-bs-ride="carousel">

            <div class="carousel-inner">

                @php $isActive = true; @endphp
                @foreach($bannerAds as $banner)
                    @if($banner->media->count() > 0)
                        @foreach($banner->media as $media)
                            @if($media->media_type == 'image')
                                <div class="carousel-item {{ $isActive ? 'active' : '' }}">
                                    <a href="{{ $banner->redirect_url ? $banner->redirect_url : route('discount.products') }}">
                                        <img src="{{ asset($media->file_path) }}" class="d-block w-100" alt="{{ $banner->title }}">
                                    </a>
                                </div>
                                @php $isActive = false; @endphp
                            @endif
                        @endforeach
                    @endif
                @endforeach

            </div>

        </div>
        </div>
        @endif

{{-- ================= TOP BAR ================= --}}


<div class="product-list-top-sec">
    <div class="product-list-top-left">
        <button class="filter-btn" id="filterBtn">
            <svg viewBox="0 0 16 16" role="img" aria-label="Filter" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="boost-sd__filter-tree-toggle-button-icon--open" fill-rule="evenodd" clip-rule="evenodd" d="M0.8 3.2H3.348C3.6792 4.1288 4.5584 4.8 5.6 4.8C6.6416 4.8 7.5208 4.1288 7.852 3.2H15.2C15.6424 3.2 16 2.8424 16 2.4C16 1.9576 15.6424 1.6 15.2 1.6H7.852C7.5208 0.6712 6.6416 0 5.6 0C4.5584 0 3.6792 0.6712 3.348 1.6H0.8C0.3576 1.6 0 1.9576 0 2.4C0 2.8424 0.3576 3.2 0.8 3.2ZM15.2 12.8H7.852C7.5208 11.8712 6.6416 11.2 5.6 11.2C4.5584 11.2 3.6792 11.8712 3.348 12.8H0.8C0.3576 12.8 0 13.1576 0 13.6C0 14.0424 0.3576 14.4 0.8 14.4H3.348C3.6792 15.3288 4.5584 16 5.6 16C6.6416 16 7.5208 15.3288 7.852 14.4H15.2C15.6424 14.4 16 14.0424 16 13.6C16 13.1576 15.6424 12.8 15.2 12.8ZM15.2 7.2H12.652C12.3208 6.2712 11.4416 5.6 10.4 5.6C9.3584 5.6 8.4792 6.2712 8.148 7.2H0.8C0.3576 7.2 0 7.5576 0 8C0 8.4424 0.3576 8.8 0.8 8.8H8.148C8.4792 9.7288 9.3584 10.4 10.4 10.4C11.4416 10.4 12.3208 9.7288 12.652 8.8H15.2C15.6424 8.8 16 8.4424 16 8C16 7.5576 15.6424 7.2 15.2 7.2ZM5.6 1.6C6.0416 1.6 6.4 1.9584 6.4 2.4C6.4 2.8416 6.0416 3.2 5.6 3.2C5.1584 3.2 4.8 2.8416 4.8 2.4C4.8 1.9584 5.1584 1.6 5.6 1.6ZM5.6 14.4C5.1584 14.4 4.8 14.0416 4.8 13.6C4.8 13.1584 5.1584 12.8 5.6 12.8C6.0416 12.8 6.4 13.1584 6.4 13.6C6.4 14.0416 6.0416 14.4 5.6 14.4ZM10.4 8.8C9.9584 8.8 9.6 8.4416 9.6 8C9.6 7.5584 9.9584 7.2 10.4 7.2C10.8416 7.2 11.2 7.5584 11.2 8C11.2 8.4416 10.8416 8.8 10.4 8.8Z" fill="#222222"></path>
                <mask id="mask0_8095:39389" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M0.8 3.2H3.348C3.6792 4.1288 4.5584 4.8 5.6 4.8C6.6416 4.8 7.5208 4.1288 7.852 3.2H15.2C15.6424 3.2 16 2.8424 16 2.4C16 1.9576 15.6424 1.6 15.2 1.6H7.852C7.5208 0.6712 6.6416 0 5.6 0C4.5584 0 3.6792 0.6712 3.348 1.6H0.8C0.3576 1.6 0 1.9576 0 2.4C0 2.8424 0.3576 3.2 0.8 3.2ZM15.2 12.8H7.852C7.5208 11.8712 6.6416 11.2 5.6 11.2C4.5584 11.2 3.6792 11.8712 3.348 12.8H0.8C0.3576 12.8 0 13.1576 0 13.6C0 14.0424 0.3576 14.4 0.8 14.4H3.348C3.6792 15.3288 4.5584 16 5.6 16C6.6416 16 7.5208 15.3288 7.852 14.4H15.2C15.6424 14.4 16 14.0424 16 13.6C16 13.1576 15.6424 12.8 15.2 12.8ZM15.2 7.2H12.652C12.3208 6.2712 11.4416 5.6 10.4 5.6C9.3584 5.6 8.4792 6.2712 8.148 7.2H0.8C0.3576 7.2 0 7.5576 0 8C0 8.4424 0.3576 8.8 0.8 8.8H8.148C8.4792 9.7288 9.3584 10.4 10.4 10.4C11.4416 10.4 12.3208 9.7288 12.652 8.8H15.2C15.6424 8.8 16 8.4424 16 8C16 7.5576 15.6424 7.2 15.2 7.2ZM5.6 1.6C6.0416 1.6 6.4 1.9584 6.4 2.4C6.4 2.8416 6.0416 3.2 5.6 3.2C5.1584 3.2 4.8 2.8416 4.8 2.4C4.8 1.9584 5.1584 1.6 5.6 1.6ZM5.6 14.4C5.1584 14.4 4.8 14.0416 4.8 13.6C4.8 13.1584 5.1584 12.8 5.6 12.8C6.0416 12.8 6.4 13.1584 6.4 13.6C6.4 14.0416 6.0416 14.4 5.6 14.4ZM10.4 8.8C9.9584 8.8 9.6 8.4416 9.6 8C9.6 7.5584 9.9584 7.2 10.4 7.2C10.8416 7.2 11.2 7.5584 11.2 8C11.2 8.4416 10.8416 8.8 10.4 8.8Z" fill="white"></path>
                </mask>
                <g mask="url(#mask0_8095:39389)"></g>
            </svg>
        </button>
        <div id="overlay"></div>
        <ul>
            {{-- Dynamic Rating Filter Tag --}}
            @if(request('rating'))
                @php
                    $ratingValue = request('rating');
                    $ratingText = $ratingValue . ' Star';
                @endphp
                <li>
                    {{ $ratingText }}
                    <a href="{{ route('productmanage.index', array_merge(request()->except('rating'), request()->only(['brand','thickness','color','min_price','max_price','sort']))) }}" class="closs-btn">
                        <i class="fa-solid fa-x"></i>
                    </a>
                </li>
            @endif

            {{-- Dynamic Brand Filter Tag --}}
            @if(request('brand') && isset($brands))
                @php
                    $selectedBrand = $brands->firstWhere('id', request('brand'));
                @endphp
                @if($selectedBrand)
                <li>
                    {{ $selectedBrand->name }}
                    <a href="{{ route('productmanage.index', array_merge(request()->except('brand'), request()->only(['rating','thickness','color','min_price','max_price','sort']))) }}" class="closs-btn">
                        <i class="fa-solid fa-x"></i>
                    </a>
                </li>
                @endif
            @endif

            {{-- Dynamic Thickness Filter Tag --}}
            @if(request('thickness') && isset($sizes))
                @php
                    $selectedThickness = $sizes->firstWhere('id', request('thickness'));
                @endphp
                @if($selectedThickness)
                <li>
                    {{ $selectedThickness->term }}
                    <a href="{{ route('productmanage.index', array_merge(request()->except('thickness'), request()->only(['rating','brand','color','min_price','max_price','sort']))) }}" class="closs-btn">
                        <i class="fa-solid fa-x"></i>
                    </a>
                </li>
                @endif
            @endif

            {{-- Dynamic Color Filter Tag --}}
            @if(request('color') && isset($colors))
                @php
                    $selectedColor = $colors->firstWhere('id', request('color'));
                @endphp
                @if($selectedColor)
                <li>
                    {{ $selectedColor->name ?? 'Color' }}
                    <a href="{{ route('productmanage.index', array_merge(request()->except('color'), request()->only(['rating','brand','thickness','min_price','max_price','sort']))) }}" class="closs-btn">
                        <i class="fa-solid fa-x"></i>
                    </a>
                </li>
                @endif
            @endif

            {{-- Dynamic Min Price Filter Tag --}}
            @if(request('min_price'))
                <li>
                    Min: ₹{{ number_format(request('min_price')) }}
                    <a href="{{ route('productmanage.index', array_merge(request()->except('min_price'), request()->only(['rating','brand','thickness','color','max_price','sort']))) }}" class="closs-btn">
                        <i class="fa-solid fa-x"></i>
                    </a>
                </li>
            @endif

            {{-- Dynamic Max Price Filter Tag --}}
            @if(request('max_price'))
                <li>
                    Max: ₹{{ number_format(request('max_price')) }}
                    <a href="{{ route('productmanage.index', array_merge(request()->except('max_price'), request()->only(['rating','brand','thickness','color','min_price','sort']))) }}" class="closs-btn">
                        <i class="fa-solid fa-x"></i>
                    </a>
                </li>
            @endif

            {{-- Dynamic Sort Tag --}}
            @if(request('sort'))
                @php
                    $sortLabels = [
                        'price_asc' => 'Price: Low to High',
                        'price_desc' => 'Price: High to Low',
                        'newest' => 'Newest First',
                        'popular' => 'Most Popular'
                    ];
                    $sortText = $sortLabels[request('sort')] ?? request('sort');
                @endphp
                <li>
                    {{ $sortText }}
                    <a href="{{ route('productmanage.index', request()->except(['sort', 'page'])) }}" class="closs-btn">
                        <i class="fa-solid fa-x"></i>
                    </a>
                </li>
            @endif

            {{-- If no filters are applied, show a default message --}}
            @if(!request()->hasAny(['rating','brand','thickness','color','min_price','max_price','sort']))
                <li>All Products</li>
            @endif
        </ul>
    </div>
    <div class="product-list-top-right">
        <div class="dropdown high-low-dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                @if(request('sort'))
                    @php
                        $sortLabels = [
                            'price_asc' => 'Price: Low to High',
                            'price_desc' => 'Price: High to Low',
                            'newest' => 'Newest First',
                            'popular' => 'Most Popular'
                        ];
                        echo $sortLabels[request('sort')] ?? 'Sort By';
                    @endphp
                @else
                    Sort By
                @endif
                <i class="fa-solid fa-filter"></i>
            </button>
            <ul class="dropdown-menu">
                <li>
                    <a class="dropdown-item {{ request('sort') == 'price_asc' ? 'active' : '' }}" 
                       href="{{ route('productmanage.index', array_merge(request()->except('sort'), ['sort' => 'price_asc'])) }}">
                        Price Low to High
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ request('sort') == 'price_desc' ? 'active' : '' }}" 
                       href="{{ route('productmanage.index', array_merge(request()->except('sort'), ['sort' => 'price_desc'])) }}">
                        Price High to Low
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ request('sort') == 'newest' ? 'active' : '' }}" 
                       href="{{ route('productmanage.index', array_merge(request()->except('sort'), ['sort' => 'newest'])) }}">
                        Newest First
                    </a>
                </li>
                <li>
                    <a class="dropdown-item {{ request('sort') == 'popular' ? 'active' : '' }}" 
                       href="{{ route('productmanage.index', array_merge(request()->except('sort'), ['sort' => 'popular'])) }}">
                        Most Popular
                    </a>
                </li>
            </ul>
        </div>
       <div class="productColumnBtnArea">
    <button class="productColumnBtn productColumnBtn_2 active">
        <i class="fa-sharp fa-solid fa-grid-2"></i>
    </button>
</div>
    </div>
</div>

<div class="home-seller-sec">
<div class="home-seller-bottom">
<div class="product-list-wrapper productRow" id="product-list">

<div class="row">

{{-- ======================================================
FILTER SIDEBAR
====================================================== --}}

<div class="col-lg-2">
<div class="product-list-filter-sec filter-sec" id="filterSidebar">
    
<div class="filter-top">
    <button id="closeSidebar">×</button>
</div>

<form method="GET" action="{{ route('productmanage.index') }}" id="filterForm">

{{-- Preserve Sort & Search --}}
@if(request('sort'))
    <input type="hidden" name="sort" value="{{ request('sort') }}">
@endif
@if(request('search'))
    <input type="hidden" name="search" value="{{ request('search') }}">
@endif

{{-- ================= RATING ================= --}}
<div class="filter-box">
<h3>Customer Review</h3>
<ul>
@foreach($ratings as $star => $total)
<li>
<label class="filter-list">

@for($i=1;$i<=5;$i++)
<i class="fa-solid fa-star {{ $i <= $star ? 'text-warning':'text-secondary' }}"></i>
@endfor

<input type="radio"
name="rating"
value="{{ $star }}"
{{ request('rating') == $star ? 'checked':'' }}>

<span class="checkmark"></span>
</label>
</li>
@endforeach
</ul>
</div>


{{-- ================= PRICE ================= --}}
<div class="filter-box price-filter-box">
    <h3>Price Range</h3>
    <div class="price-slider-wrapper">
        <div class="slider-track" id="slider-track"></div>
        <input type="range" min="0" max="{{ $maxSliderPrice }}" value="{{ request('min_price', 0) }}" id="slider-min" class="price-range-input">
        <input type="range" min="0" max="{{ $maxSliderPrice }}" value="{{ request('max_price', $maxSliderPrice) }}" id="slider-max" class="price-range-input">
    </div>
    <div class="price-input-row d-flex align-items-center justify-content-between mt-3">
        <div class="price-input-col">
            <span class="price-label">Min</span>
            <div class="input-group input-group-sm">
                <span class="input-group-text">₹</span>
                <input type="number" name="min_price" id="input-min" class="form-control" value="{{ request('min_price', 0) }}">
            </div>
        </div>
        <div class="price-to-text px-1 mt-4">to</div>
        <div class="price-input-col">
            <span class="price-label">Max</span>
            <div class="input-group input-group-sm">
                <span class="input-group-text">₹</span>
                <input type="number" name="max_price" id="input-max" class="form-control" value="{{ request('max_price', $maxSliderPrice) }}">
            </div>
        </div>
    </div>
</div>


{{-- ================= CATEGORY / SUBCATEGORY ================= --}}
@if(isset($selectedCategoryModel) && $selectedCategoryModel)
<div class="filter-box">
    <h3>Subcategories</h3>

    <input type="hidden" name="category" value="{{ $selectedCategoryModel->slug ?? strtolower(str_replace(' ', '-', $selectedCategoryModel->name)) }}">
    @if(count($subcategories) > 0)
        <ul>
        @foreach($subcategories as $subcat)
        <li>
        <label class="filter-list">
        {{ $subcat->name }}
        <input type="radio"
        name="subcategory"
        value="{{ $subcat->id }}"
        {{ request('subcategory') == $subcat->id ? 'checked':'' }}>
        <span class="checkmark"></span>
        </label>
        </li>
        @endforeach
        </ul>
    @else
        <p class="text-secondary text-xs" style="font-size: 13px; color: #878787; margin-top: 10px;">No subcategories available</p>
    @endif
</div>
@endif


{{-- ================= ATTRIBUTES ================= --}}
@foreach($attributes as $attr)

@if(strtolower($attr->name) == 'color')

<div class="filter-box filter-color-box">
<h3>{{ $attr->name }}</h3>
<ul>

@foreach($attributeTerms[$attr->id] ?? [] as $term)
<li>
<label class="color-box">

<input type="radio"
name="attributes[{{ $attr->id }}]"
value="{{ $term->id }}"
{{ request('attributes.'.$attr->id) == $term->id ? 'checked':'' }}>

<span class="color-circle"
style="background: {{ $term->color_code ?? '#ccc' }}">
</span>

</label>
</li>
@endforeach

</ul>
</div>

@else

<div class="filter-box">
<h3>{{ $attr->name }}</h3>
<ul>

@foreach($attributeTerms[$attr->id] ?? [] as $term)
<li>
<label class="filter-list">

{{ $term->term }}

<input type="radio"
name="attributes[{{ $attr->id }}]"
value="{{ $term->id }}"
{{ request('attributes.'.$attr->id) == $term->id ? 'checked':'' }}>

<span class="checkmark"></span>
</label>
</li>
@endforeach

</ul>
</div>

@endif

@endforeach


{{-- ================= APPLY BUTTON ================= --}}
<div class="mt-3">
<button type="submit" class="btn btn-primary w-100 py-2 font-weight-bold" style="background: #2874f0; border: none;">
Apply Filter
</button>
</div>


{{-- ================= CLEAR ================= --}}
@if(request()->hasAny(['rating','category','subcategory','min_price','max_price','attributes']))
<div class="mt-2">
<a href="{{ route('productmanage.index') }}"
class="btn btn-outline-secondary w-100">
Clear Filter
</a>
</div>
@endif

</form>

</div>
</div>

{{-- ======================================================
PRODUCT GRID
====================================================== --}}

<div class="col-lg-10">

{{-- CAMPAIGN BANNERS & VIDEOS PROCESSING --}}
@php
    $campaignMedia = [];
    if(isset($campaignAds) && count($campaignAds) > 0) {
        foreach($campaignAds as $banner) {
            if (strtolower($banner->title) === 'spin and win') {
                continue;
            }
            foreach($banner->media as $media) {
                if($media->media_type == 'image') {
                    $campaignMedia[] = ['media' => $media, 'banner' => $banner];
                }
            }
        }
    }
    if (count($campaignMedia) == 1) {
        $campaignMedia[] = $campaignMedia[0];
    }

    $campaignSectionIndex = 0;
    $totalCampaignSections = 4;

    $showCampaignCarousel = function() use (&$campaignMedia, &$campaignSectionIndex, $totalCampaignSections, $__env) {
        $sectionItems = [];
        foreach($campaignMedia as $index => $item) {
            if ($index % $totalCampaignSections === $campaignSectionIndex) {
                $sectionItems[] = $item;
            }
        }
        
        $campaignSectionIndex++;

        if (count($sectionItems) == 1) {
            $sectionItems[] = $sectionItems[0];
        }

        if (count($sectionItems) > 0) {
            @endphp
            <div class="campaign-banner-sec mb-0">
                <div class="campaign-slider-wrapper owl-carousel">
                    @foreach($sectionItems as $item)
                        <div class="campaign-banner-item item">
                            <a href="{{ ($item['banner']->redirect_url && $item['banner']->redirect_url !== '#') ? $item['banner']->redirect_url : route('discount.products') }}" class="d-block w-100">
                                <img src="{{ asset($item['media']->file_path) }}" class="w-100" style="display: block; width: 100%; height: auto; border: none !important; box-shadow: none !important; margin-bottom: 0 !important;" alt="{{ $item['banner']->title }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
            @php
        }
    };

    $videoMediaList = [];
    $allAds = collect();
    if(isset($videoAds)) $allAds = $allAds->merge($videoAds);
    if(isset($bannerAds)) $allAds = $allAds->merge($bannerAds);
    if(isset($campaignAds)) $allAds = $allAds->merge($campaignAds);
    if(isset($featuredAds)) $allAds = $allAds->merge($featuredAds);

    if($allAds->count() > 0) {
        foreach($allAds as $ad) {
            foreach($ad->media as $m) {
                if($m->media_type == 'video' || pathinfo($m->file_path, PATHINFO_EXTENSION) == 'mp4') {
                    $videoMediaList[] = ['media' => $m, 'ad' => $ad];
                }
            }
        }
    }
    $videoIndex = 0;
    $totalVideos = count($videoMediaList);

    $renderVideoCard = function() use (&$videoMediaList, &$videoIndex, $totalVideos, $__env) {
        if ($videoIndex < $totalVideos) {
            $firstVideoAd = $videoMediaList[$videoIndex];
            $videoIndex++;
            @endphp
            <div class="col-6 col-md-4 col-lg-2 mb-4 ms-auto">
                <div class="card shadow-sm border-0 overflow-hidden position-relative" style="border-radius: 4px; background: #000; height: 200px !important; border: 1px solid #f0f0f0; cursor: pointer; transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-4px)'" onmouseout="this.style.transform='none'" onclick="window.location.href='{{ route('vshop.visuals', ['tab' => 'videos']) }}'">
                    <video class="w-100 h-100 corner-video-player" style="object-fit: cover; opacity: 0.9;" autoplay muted loop playsinline preload="auto">
                        <source src="{{ asset($firstVideoAd['media']->file_path) }}" type="video/mp4">
                    </video>
                    <div class="position-absolute bottom-0 start-0 end-0 p-2" style="background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 100%); z-index: 2;">
                        <h6 class="text-white fw-bold mb-0 text-truncate" style="font-size: 11px;">{{ $firstVideoAd['ad']->title }}</h6>
                    </div>
                </div>
            </div>
            @php
            return true;
        }
        return false;
    };
@endphp

<div class="product-list-right">

    {{-- 1. TOP TRENDING --}}
    @if(isset($trendingProducts) && count($trendingProducts) > 0)
        @php $showCampaignCarousel(); @endphp
        <div class="home-seller-sec mb-2" id="trending-section">
            <div class="home-seller-header mb-3">
                <h2 style="font-size: 15px; font-weight: bold; color: #212121;"><i class="fa-solid fa-fire text-danger"></i> Top Trending</h2>
            </div>
            <div class="home-seller-bottom">
                <div class="row">
                    @php $videoRendered = false; @endphp
                    @foreach($trendingProducts as $product)
                        <div class="col-6 col-md-4 col-lg-2 mb-4 product-box-outer">
                            @include('vshop.products._product_card_partial', ['product' => $product])
                        </div>
                        @if($loop->iteration % 5 == 0)
                            @php $videoRendered = $renderVideoCard(); @endphp
                        @endif
                    @endforeach
                    @if(!$videoRendered)
                        @php $renderVideoCard(); @endphp
                    @endif
                </div>
                @if($trendingProducts->hasPages())
                    <div class="custom-pagination-wrapper">
                        {{ $trendingProducts->appends(request()->except('trending_page'))->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- 2. MOST VIEWED --}}
    @if(isset($mostViewedProducts) && count($mostViewedProducts) > 0)
        @php $showCampaignCarousel(); @endphp
        <div class="home-seller-sec mb-2" id="views-section">
            <div class="home-seller-header mb-3">
                <h2 style="font-size: 15px; font-weight: bold; color: #212121;"><i class="fa-solid fa-eye text-primary"></i> Most Viewed</h2>
            </div>
            <div class="home-seller-bottom">
                <div class="row">
                    @php $videoRendered = false; @endphp
                    @foreach($mostViewedProducts as $product)
                        <div class="col-6 col-md-4 col-lg-2 mb-4 product-box-outer">
                            @include('vshop.products._product_card_partial', ['product' => $product])
                        </div>
                        @if($loop->iteration % 5 == 0)
                            @php $videoRendered = $renderVideoCard(); @endphp
                        @endif
                    @endforeach
                    @if(!$videoRendered)
                        @php $renderVideoCard(); @endphp
                    @endif
                </div>
                @if($mostViewedProducts->hasPages())
                    <div class="custom-pagination-wrapper">
                        {{ $mostViewedProducts->appends(request()->except('views_page'))->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- 3. MOST ENQUIRED --}}
    @if(isset($mostEnquiredProducts) && count($mostEnquiredProducts) > 0)
        @php $showCampaignCarousel(); @endphp
        <div class="home-seller-sec mb-2" id="enquiries-section">
            <div class="home-seller-header mb-3">
                <h2 style="font-size: 15px; font-weight: bold; color: #212121;"><i class="fa-solid fa-circle-question text-info"></i> Most Enquired</h2>
            </div>
            <div class="home-seller-bottom">
                <div class="row">
                    @php $videoRendered = false; @endphp
                    @foreach($mostEnquiredProducts as $product)
                        <div class="col-6 col-md-4 col-lg-2 mb-4 product-box-outer">
                            @include('vshop.products._product_card_partial', ['product' => $product])
                        </div>
                        @if($loop->iteration % 5 == 0)
                            @php $videoRendered = $renderVideoCard(); @endphp
                        @endif
                    @endforeach
                    @if(!$videoRendered)
                        @php $renderVideoCard(); @endphp
                    @endif
                </div>
                @if($mostEnquiredProducts->hasPages())
                    <div class="custom-pagination-wrapper">
                        {{ $mostEnquiredProducts->appends(request()->except('enquiries_page'))->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- 4. HIGHEST RATED --}}
    @if(isset($highestRatedProducts) && count($highestRatedProducts) > 0)
        @php $showCampaignCarousel(); @endphp
        <div class="home-seller-sec mb-2" id="rating-section">
            <div class="home-seller-header mb-3">
                <h2 style="font-size: 18px; font-weight: bold; color: #212121;"><i class="fa-solid fa-star text-warning"></i> Highest Rated</h2>
            </div>
            <div class="home-seller-bottom">
                <div class="row">
                    @php $videoRendered = false; @endphp
                    @foreach($highestRatedProducts as $product)
                        <div class="col-6 col-md-4 col-lg-2 mb-4 product-box-outer">
                            @include('vshop.products._product_card_partial', ['product' => $product])
                        </div>
                        @if($loop->iteration % 5 == 0)
                            @php $videoRendered = $renderVideoCard(); @endphp
                        @endif
                    @endforeach
                    @if(!$videoRendered)
                        @php $renderVideoCard(); @endphp
                    @endif
                </div>
                @if($highestRatedProducts->hasPages())
                    <div class="custom-pagination-wrapper">
                        {{ $highestRatedProducts->appends(request()->except('rating_page'))->links('pagination::bootstrap-4') }}
                    </div>
                @endif
            </div>
        </div>
    @endif

</div>

<div id="shareModal" class="share-modal">

    <div class="share-content">

        <h4>Share Product</h4>

        <div class="share-options">

            <button class="share-item whatsapp">
                <i class="fa-brands fa-whatsapp"></i>
            </button>

            <button class="share-item facebook">
                <i class="fa-brands fa-facebook"></i>
                Facebook
            </button>

            <button class="share-item copy">
                <i class="fa-solid fa-link"></i>
                Copy Link
            </button>

        </div>

        <button class="close-share">Close</button>

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



@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
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

toastr.options = {
    closeButton: true,
    progressBar: true,
    positionClass: "toast-top-right",
    timeOut: "3000"
};

document.addEventListener('click', function(e){

    const btn = e.target.closest('.wishlist-btn');
    if(!btn) return;

    e.preventDefault();

    @auth('customer')

    let productId = btn.dataset.productId;
    let icon = btn.querySelector('i');
    const originalClass = icon.className;

    // prevent double click
    if(btn.classList.contains('loading')) return;
    btn.classList.add('loading');

    // loader
    icon.className = "fa fa-spinner fa-spin";

    fetch("{{ route('vshop.wishlist.toggle') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            product_id: parseInt(productId)
        })
    })
    .then(res => res.json())
    .then(data => {

        btn.classList.remove('loading');

        if(data.success){

            if(data.status === "added"){
                icon.className = "fa-solid fa-heart text-danger";
                toastr.success("Added to wishlist");
            }
            else if(data.status === "removed"){
                icon.className = "fa-regular fa-heart";
                toastr.info("Removed from wishlist");
            }

            // 🔥🔥🔥 COUNT UPDATE FIX
            if(data.wishlist_count !== undefined){
                let countEl = document.getElementById('wishlistCount');
                if(countEl){
                    countEl.innerText = data.wishlist_count;

                    // optional animation 🔥
                    countEl.classList.add('scale');
                    setTimeout(()=>countEl.classList.remove('scale'),300);
                }
            }

        } else {
            icon.className = originalClass;
            toastr.error(data.message || "Wishlist error");
        }

    })
    .catch(() => {
        btn.classList.remove('loading');
        icon.className = originalClass;
        toastr.error("Network error");
    });

    @else
    toastr.warning("Please login first", "", {
        onclick: () => window.location = "{{ route('customer.login') }}"
    });
    @endauth

});



</script>

<script>
// Price Slider Logic
const sliderMin = document.getElementById('slider-min');
const sliderMax = document.getElementById('slider-max');
const inputMin = document.getElementById('input-min');
const inputMax = document.getElementById('input-max');
const track = document.getElementById('slider-track');

if (sliderMin && sliderMax && inputMin && inputMax && track) {

    const RANGE_MIN = parseInt(sliderMin.min);
    const RANGE_MAX = parseInt(sliderMin.max);
    const MIN_GAP = 1000;

    function updateSlider() {

        let minVal = parseInt(sliderMin.value);
        let maxVal = parseInt(sliderMax.value);

        // Prevent crossing
        if (maxVal - minVal < MIN_GAP) {
            maxVal = minVal + MIN_GAP;
            sliderMax.value = maxVal;
        }

        const minPercent = ((minVal - RANGE_MIN) / (RANGE_MAX - RANGE_MIN)) * 100;
        const maxPercent = ((maxVal - RANGE_MIN) / (RANGE_MAX - RANGE_MIN)) * 100;

        track.style.background =
            `linear-gradient(to right,
                #e0e0e0 0%,
                #e0e0e0 ${minPercent}%,
                #2874f0 ${minPercent}%,
                #2874f0 ${maxPercent}%,
                #e0e0e0 ${maxPercent}%,
                #e0e0e0 100%)`;

        if (inputMin) inputMin.value = minVal;
        if (inputMax) inputMax.value = maxVal;
    }

    sliderMin.addEventListener("input", function () {

        let minVal = parseInt(this.value);
        let maxVal = parseInt(sliderMax.value);

        if (maxVal - minVal < MIN_GAP) {
            this.value = maxVal - MIN_GAP;
        }

        updateSlider();
    });

    sliderMax.addEventListener("input", function () {

        let minVal = parseInt(sliderMin.value);
        let maxVal = parseInt(this.value);

        if (maxVal - minVal < MIN_GAP) {
            this.value = minVal + MIN_GAP;
        }

        updateSlider();
    });

    inputMin.addEventListener("input", function () {
        let value = parseInt(this.value) || RANGE_MIN;
        value = Math.max(RANGE_MIN, value);

        if (value > parseInt(sliderMax.value) - MIN_GAP) {
            value = parseInt(sliderMax.value) - MIN_GAP;
        }

        sliderMin.value = value;
        updateSlider();
    });

    inputMax.addEventListener("input", function () {
        let value = parseInt(this.value) || RANGE_MAX;
        value = Math.min(RANGE_MAX, value);

        if (value < parseInt(sliderMin.value) + MIN_GAP) {
            value = parseInt(sliderMin.value) + MIN_GAP;
        }

        sliderMax.value = value;
        updateSlider();
    });

    // Make the track clickable
    const sliderWrapper = document.querySelector('.price-slider-wrapper');
    if (sliderWrapper) {
        sliderWrapper.addEventListener('click', function(e) {
            // If the user clicked directly on the thumb, let the default behavior (drag) happen
            if(e.target.tagName.toLowerCase() === 'input') return;

            const rect = sliderWrapper.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percentage = clickX / rect.width;
            let clickedValue = Math.round(RANGE_MIN + percentage * (RANGE_MAX - RANGE_MIN));

            let minVal = parseInt(sliderMin.value);
            let maxVal = parseInt(sliderMax.value);

            let distToMin = Math.abs(clickedValue - minVal);
            let distToMax = Math.abs(clickedValue - maxVal);

            if (distToMin < distToMax) {
                // Closer to min thumb
                if (clickedValue > maxVal - MIN_GAP) clickedValue = maxVal - MIN_GAP;
                sliderMin.value = clickedValue;
            } else {
                // Closer to max thumb
                if (clickedValue < minVal + MIN_GAP) clickedValue = minVal + MIN_GAP;
                sliderMax.value = clickedValue;
            }

            updateSlider();
        });
    }

    updateSlider();
}
</script>


<style>
.color-circle{
width:20px;
height:20px;
border-radius:50%;
display:inline-block;
border:1px solid #ddd;
}

/* ===== MODAL BACKGROUND ===== */
.share-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100vh;

    background: rgba(0,0,0,0.65);

    display: flex;
    justify-content: center;
    align-items: center;

    opacity: 0;
    visibility: hidden;

    z-index: 9999;
    transition: 0.3s ease;
}

/* ACTIVE STATE */
.share-modal.active {
    opacity: 1;
    visibility: visible;
}


/* ===== MODAL BOX ===== */
.share-content {
    background: #fff;
    width: 100%;
    max-width: 520px; /* 🔥 bigger */
    border-radius: 16px;
    padding: 30px 25px;
    text-align: center;

    box-shadow: 0 10px 40px rgba(0,0,0,0.2);

    transform: scale(0.85);
    transition: 0.3s ease;
}

.share-modal.active .share-content {
    transform: scale(1);
}


/* ===== TITLE ===== */
.share-content h4 {
    font-size: 20px;
    font-weight: 600;
    margin-bottom: 20px;
}


/* ===== OPTIONS GRID ===== */
.share-options {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 15px;
    flex-wrap: wrap;
}


/* ===== SHARE BUTTON ===== */
.share-item {
    border: none;
    background: #f8f9fa;
    padding: 18px;
    border-radius: 14px;
    width: 110px; /* 🔥 bigger */
    cursor: pointer;

    transition: 0.3s;
}

.share-item:hover {
    background: #e9ecef;
    transform: translateY(-3px);
}


/* ICON */
.share-item i {
    font-size: 26px;
    display: block;
    margin-bottom: 6px;
}


/* COLORS */
.whatsapp { color: #25D366; }
.facebook { color: #1877f2; }
.copy { color: #333; }


/* ===== CLOSE BUTTON ===== */
.close-share {
    margin-top: 25px;
    background: #dc3545;
    color: #fff;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 500;
    transition: 0.3s;
}

.close-share:hover {
    background: #bb2d3b;
}

/* ===== GRID VIEW FORCE (NO DESIGN CHANGE) ===== */
.productRow.grid-view .product-box-outer {
    width: 25%;
    float: left;
}

@media (max-width:768px){
    .productRow.grid-view .product-box-outer {
        width: 50%;
    }
}
</style>

<script>
    $(document).ready(function() {
        const filterBtn = document.getElementById("filterBtn");
        const sidebar = document.getElementById("filterSidebar");
        const overlay = document.getElementById("overlay");
        const closeBtn = document.getElementById("closeSidebar");
        
        // Open sidebar
        if (filterBtn) {
            filterBtn.addEventListener("click", () => {
              sidebar.classList.add("active");
              overlay.classList.add("active");
            });
        }
        
        // Close sidebar (X button)
        if (closeBtn) {
            closeBtn.addEventListener("click", () => {
              sidebar.classList.remove("active");
              overlay.classList.remove("active");
            });
        }
        
        // Close when clicking outside
        if (overlay) {
            overlay.addEventListener("click", () => {
              sidebar.classList.remove("active");
              overlay.classList.remove("active");
            });
        }

        // Campaign Banners Slider
        function initCampaignSliders() {
            $(".campaign-slider-wrapper").each(function() {
                let carousel = $(this);
                if (!carousel.hasClass('owl-loaded')) {
                    let itemsCount = carousel.find('.item').length;
                    carousel.owlCarousel({
                        items: 1,
                        loop: itemsCount > 1,
                        autoplay: itemsCount > 1,
                        autoplayTimeout: 2000,
                        smartSpeed: 800,
                        autoplayHoverPause: true,
                        nav: false,
                        dots: false
                    });
                }
            });
        }
        initCampaignSliders();

        // Force autoplay on load
        $('video[autoplay]').each(function() {
            this.play();
        });
    });
    </script>
@endpush

@endsection




