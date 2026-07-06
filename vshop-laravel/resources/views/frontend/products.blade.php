@extends('frontend.layout')

@section('content')

<section class="bedcum-sec" style="">
    <div class="container">
        <div class="bedcum-inner">    
            {{-- Breadcrumb --}}
            <div class="bed-cum-list">
                <ul>
                    <li><a href="#">Shop</a></li>
                    <li><i class="fa-regular fa-angle-right"></i></li>
                    <li>
                        <span>{{ $slug ? ucfirst($slug) : 'All Products' }}</span>
                    </li>
                    
                    
                </ul>
            </div>

            {{-- Result count and sorting --}}
            <div class="product-list-tp-right">
                <div class="filter-box">
                    <button class="filter-btn">
                        <i class="fa-regular fa-sliders"></i>
                    </button>
                </div>
              <div class="result-sec">
    @php
        $mainProductCount = isset($products) && $products->count() ? $products->count() : 0;
        $otherProductCount = isset($otherProducts) && $otherProducts->count() ? $otherProducts->count() : 0;
        $totalProductCount = $mainProductCount + $otherProductCount;
    @endphp
    <p>Result : <span>{{ $totalProductCount }} Products</span></p>
</div>
             <div class="short-sec">
    <form id="sortForm" method="GET" action="{{ route('frontend.product.details', ['slug' => $slug ?? null]) }}">
        {{-- Preserve existing filters --}}
        @foreach(request()->except('sort') as $key => $value)
            @if(is_array($value))
                @foreach($value as $v)
                    @if(is_string($v))
                        <input type="hidden" name="{{ $key }}[]" value="{{ $v }}">
                    @endif
                @endforeach
            @elseif(is_string($value))
                <input type="hidden" name="{{ $key }}" value="{{ $value }}">
            @endif
        @endforeach

        <select name="sort" class="form-select" onchange="this.form.submit()">
            <option value="popularity" {{ request('sort') == 'popularity' ? 'selected' : '' }}>Popularity</option>
            <option value="price_low_high" {{ request('sort') == 'price_low_high' ? 'selected' : '' }}>Price: Low to High</option>
            <option value="price_high_low" {{ request('sort') == 'price_high_low' ? 'selected' : '' }}>Price: High to Low</option>
        </select>
    </form>
</div>

            </div>
        </div>
    </div>
</section>

<section class="product-dtl-banner">
    <img src="images/banner-pic-main.webp" alt="">
</section>

<section class="product-list-top-sec">
    <div class="product-list-top-left">
                                    <button class="filter-btn">
                                        <svg viewBox="0 0 16 16" role="img" aria-label="Filter" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path class="boost-sd__filter-tree-toggle-button-icon--open" fill-rule="evenodd" clip-rule="evenodd" d="M0.8 3.2H3.348C3.6792 4.1288 4.5584 4.8 5.6 4.8C6.6416 4.8 7.5208 4.1288 7.852 3.2H15.2C15.6424 3.2 16 2.8424 16 2.4C16 1.9576 15.6424 1.6 15.2 1.6H7.852C7.5208 0.6712 6.6416 0 5.6 0C4.5584 0 3.6792 0.6712 3.348 1.6H0.8C0.3576 1.6 0 1.9576 0 2.4C0 2.8424 0.3576 3.2 0.8 3.2ZM15.2 12.8H7.852C7.5208 11.8712 6.6416 11.2 5.6 11.2C4.5584 11.2 3.6792 11.8712 3.348 12.8H0.8C0.3576 12.8 0 13.1576 0 13.6C0 14.0424 0.3576 14.4 0.8 14.4H3.348C3.6792 15.3288 4.5584 16 5.6 16C6.6416 16 7.5208 15.3288 7.852 14.4H15.2C15.6424 14.4 16 14.0424 16 13.6C16 13.1576 15.6424 12.8 15.2 12.8ZM15.2 7.2H12.652C12.3208 6.2712 11.4416 5.6 10.4 5.6C9.3584 5.6 8.4792 6.2712 8.148 7.2H0.8C0.3576 7.2 0 7.5576 0 8C0 8.4424 0.3576 8.8 0.8 8.8H8.148C8.4792 9.7288 9.3584 10.4 10.4 10.4C11.4416 10.4 12.3208 9.7288 12.652 8.8H15.2C15.6424 8.8 16 8.4424 16 8C16 7.5576 15.6424 7.2 15.2 7.2ZM5.6 1.6C6.0416 1.6 6.4 1.9584 6.4 2.4C6.4 2.8416 6.0416 3.2 5.6 3.2C5.1584 3.2 4.8 2.8416 4.8 2.4C4.8 1.9584 5.1584 1.6 5.6 1.6ZM5.6 14.4C5.1584 14.4 4.8 14.0416 4.8 13.6C4.8 13.1584 5.1584 12.8 5.6 12.8C6.0416 12.8 6.4 13.1584 6.4 13.6C6.4 14.0416 6.0416 14.4 5.6 14.4ZM10.4 8.8C9.9584 8.8 9.6 8.4416 9.6 8C9.6 7.5584 9.9584 7.2 10.4 7.2C10.8416 7.2 11.2 7.5584 11.2 8C11.2 8.4416 10.8416 8.8 10.4 8.8Z" fill="#222222"></path>
                                            <mask id="mask0_8095:39389" maskUnits="userSpaceOnUse" x="0" y="0" width="16" height="16">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M0.8 3.2H3.348C3.6792 4.1288 4.5584 4.8 5.6 4.8C6.6416 4.8 7.5208 4.1288 7.852 3.2H15.2C15.6424 3.2 16 2.8424 16 2.4C16 1.9576 15.6424 1.6 15.2 1.6H7.852C7.5208 0.6712 6.6416 0 5.6 0C4.5584 0 3.6792 0.6712 3.348 1.6H0.8C0.3576 1.6 0 1.9576 0 2.4C0 2.8424 0.3576 3.2 0.8 3.2ZM15.2 12.8H7.852C7.5208 11.8712 6.6416 11.2 5.6 11.2C4.5584 11.2 3.6792 11.8712 3.348 12.8H0.8C0.3576 12.8 0 13.1576 0 13.6C0 14.0424 0.3576 14.4 0.8 14.4H3.348C3.6792 15.3288 4.5584 16 5.6 16C6.6416 16 7.5208 15.3288 7.852 14.4H15.2C15.6424 14.4 16 14.0424 16 13.6C16 13.1576 15.6424 12.8 15.2 12.8ZM15.2 7.2H12.652C12.3208 6.2712 11.4416 5.6 10.4 5.6C9.3584 5.6 8.4792 6.2712 8.148 7.2H0.8C0.3576 7.2 0 7.5576 0 8C0 8.4424 0.3576 8.8 0.8 8.8H8.148C8.4792 9.7288 9.3584 10.4 10.4 10.4C11.4416 10.4 12.3208 9.7288 12.652 8.8H15.2C15.6424 8.8 16 8.4424 16 8C16 7.5576 15.6424 7.2 15.2 7.2ZM5.6 1.6C6.0416 1.6 6.4 1.9584 6.4 2.4C6.4 2.8416 6.0416 3.2 5.6 3.2C5.1584 3.2 4.8 2.8416 4.8 2.4C4.8 1.9584 5.1584 1.6 5.6 1.6ZM5.6 14.4C5.1584 14.4 4.8 14.0416 4.8 13.6C4.8 13.1584 5.1584 12.8 5.6 12.8C6.0416 12.8 6.4 13.1584 6.4 13.6C6.4 14.0416 6.0416 14.4 5.6 14.4ZM10.4 8.8C9.9584 8.8 9.6 8.4416 9.6 8C9.6 7.5584 9.9584 7.2 10.4 7.2C10.8416 7.2 11.2 7.5584 11.2 8C11.2 8.4416 10.8416 8.8 10.4 8.8Z" fill="white"></path>
                                            </mask>
                                            <g mask="url(#mask0_8095:39389)"></g>
                                        </svg>
                                    </button>
                                    <ul>
                                        <li>Newest
                                            <span class="closs-btn"><i class="fa-regular fa-x"></i></span>
                                        </li>
                                        <li>4 Star
                                            <span class="closs-btn"><i class="fa-regular fa-x"></i></span>
                                        </li>
                                    </ul>
                                </div>
    <div class="product-list-top-right">
                                    <div class="dropdown high-low-dropdown">
                                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            Price Low To High
                                            <i class="fa-regular fa-filter"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                                        </ul>
                                    </div>
                                    <div class="productColumnBtnArea">
                                        <button class="productColumnBtn productColumnBtn_2 active">
                                            <i class="fa-sharp fa-regular fa-grid-2"></i>
                                        </button>
                                        <button class="productColumnBtn productColumnBtn_3">
                                            <i class="fa-solid fa-list"></i>
                                        </button>
                                    </div>
                                </div>
</section>

<section class="product-list-section py-0">
    <div class="container">
        <div class="row rowBox">

{{-- ✅ LEFT FILTER SIDEBAR --}}
<div class="col-xxl-3 col-xl-3 col-lg-3 col-md-12 col-sm-12 col-12 coluMnBox leftProductListingColumn">
<div class="productFilterSidebarOverlay"></div>
<div class="productFilterSidebar">
<div class="productFilterSidebarinner">
<div class="productFilterSidebarWrapper">
<div class="productFilterSidebarHeader">
</div>
<div class="productFilterSidebarWrapperinner">
    <form id="filterForm" action="{{ route('frontend.product.details', ['slug' => $slug ?? null]) }}" method="GET">
        <div class="product-list-filter-sec">
            <h3>Filters</h3>

            {{-- PRICE FILTER --}}
            @php
                $min = $minPrice ?? 0;
                $max = $maxPrice ?? 20000;
                $curMin = request()->get('min_price', $min);
                $curMax = request()->get('max_price', $max);
            @endphp

            <div class="product-filter-box pt-0">
                <h4>PRICE</h4>
                <div class="product-filter-box-bottom">
                   <div class="dual-range-wrap">

    <div class="range-container">
        <div class="slider-track"></div>

        <input type="range" id="rangeMin" class="range-input"
               name="min_price"
               min="0" max="5000"
               value="{{ request('min_price', 0) }}">

        <input type="range" id="rangeMax" class="range-input"
               name="max_price"
               min="1" max="5000"
               value="{{ request('max_price', 5000) }}">
    </div>

    <input type="hidden" id="minPriceInput" value="{{ request('min_price', 0) }}">
    <input type="hidden" id="maxPriceInput" value="{{ request('max_price', 5000) }}">

    <div>
        <span id="priceRangeLabel">
            ₹{{ request('min_price', 0) }} - ₹{{ request('max_price', 5000) }}+
        </span>
    </div>

</div>

                </div>
            </div>

            {{-- CATEGORY FILTER --}}
            <div class="product-filter-box pt-0">
                <h4>Category</h4>
                <div class="product-filter-box-bottom">
                    <ul>
                        @foreach($categories as $category)
                            @php
                                $selectedCategories = request()->get('category', []);
                                $categoryName = is_array($category->name) ? implode(', ', $category->name) : (string)$category->name;
                            @endphp
                            <li>
                                <label class="company-checkbox {{ $categoryName }}">
                                    {{ $categoryName }}
                                    <input type="checkbox"
                                           name="category[]"
                                           value="{{ $category->id }}"
                                           {{ (is_array($selectedCategories) && in_array($category->id, $selectedCategories)) || ($slug && $slug == $category->slug) ? 'checked' : '' }}>
                                    <span class="company-checkmark"></span>
                                </label>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            {{-- ATTRIBUTE FILTERS --}}
            @foreach($attributes as $attribute)
                @php
                    $selectedTerms = request()->input('filter.'.$attribute->id, []);
                    $attributeName = is_array($attribute->name) ? implode(', ', $attribute->name) : (string)$attribute->name;
                @endphp
                <div class="product-filter-box pt-0">
                    <h4>{{ $attributeName }}</h4>
                    <div class="product-filter-box-bottom">
                        @if(in_array(strtolower($attributeName), ['colour', 'color']))
                            <div class="color-filter-sec">
                                <div class="color-box d-flex flex-wrap gap-2">
                                    @foreach($attribute->terms as $term)
                                        @php
                                            $termName = is_array($term->term) ? implode(', ', $term->term) : (string)$term->term;
                                            $termColor = is_array($term->color_code) ? implode(', ', $term->color_code) : (string)($term->color_code ?? $termName);
                                        @endphp
                                        <label class="color-container">
                                            <input type="radio"
                                                   name="filter[{{ $attribute->id }}][]"
                                                   value="{{ $term->id }}"
                                                   {{ is_array($selectedTerms) && in_array($term->id, $selectedTerms) ? 'checked' : '' }}
                                                   data-color="{{ $termColor }}">
                                            <span class="color-checkmark"></span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @else
                            <ul class="company-size-radio-sec">
                                @foreach($attribute->terms as $term)
                                    @php
                                        $termName = is_array($term->term) ? implode(', ', $term->term) : (string)$term->term;
                                    @endphp
                                    <li>
                                        <label class="company-checkbox {{ $attributeName }}">
                                            <input type="radio"
                                                   name="filter[{{ $attribute->id }}][]"
                                                   value="{{ $term->id }}"
                                                   {{ is_array($selectedTerms) && in_array($term->id, $selectedTerms) ? 'checked' : '' }}>
                                            <span class="company-size-checkmark">{{ $termName }}</span>
                                        </label>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach

            {{-- SUBMIT BUTTON --}}
            <button type="submit" id="applyFilterBtn" class="btn btn-primary mt-2 apply-filter-btn">Apply Filters</button>
        </div>
    </form>
	</div>
</div>	
</div>		
</div>		
	
	
</div>



            {{-- RIGHT PRODUCT GRID --}}
            <div class="col-xxl-9 col-xl-9 col-lg-9 col-md-12 col-sm-12 col-12 columnBox rightProductListingColumn">

                {{-- Page Heading --}}
                <h2 class="productCatTitle mb-3" style="">
                    {{ $slug ? ucfirst($slug) : 'All Products' }}
                </h2>
                 @foreach($products as $product)
       
        @if($product->brand)
            <p class="brand-name">
<strong>{{ $product->brand->name }}</strong>
            </p>
        @endif
    @endforeach
                         

{{-- Check if user is logged in --}}
@if(!Auth::guard('customer')->check())
   
@else
    {{-- FOR LOGGED IN USERS, SHOW REGULAR PRODUCTS --}}
    {{-- MAIN PRODUCTS --}}
    @if($products->count())
        <div class="row rowBox2">
            @foreach($products as $product)
                <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-6 col-6 columnBox2 productBox">
                    <div class="product-list-box">
                        <div class="product-list-box-inner">
                            
                            {{-- Image Section --}}
                            <div class="product-list-box-img">
                                <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                    <img src="{{ $product->thumbnail ? asset($product->thumbnail) : 'https://via.placeholder.com/300x300?text=No+Image' }}"
                                         class="img-fluid"
                                         alt="{{ $product->name }}"
                                         style="">
                                </a>
                                
                                {{-- MYNTRA/FLIPKART STYLE COMBO TAG --}}
                                @if(isset($product->is_combo_product) && $product->is_combo_product)
                                    <div class="combo-tag" style="position: absolute; top: 8px; left: 8px; background: #ff3f6c; color: white; padding: 3px 8px; font-size: 10px; font-weight: 700; border-radius: 2px; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); z-index: 10;">
                                        COMBO
                                    </div>
                                @endif
                                
                                {{-- Optional badges --}}
                                <!--<div class="rating-pill">-->
                                <!--  <span class="star">★</span>-->
                                <!--  <span class="rating-value">4.3</span>-->
                                <!--</div>-->
                            </div>
                            
                            {{-- Info Section --}}
                            <div class="product-list-box-info">
                                <h4 class="proTitle">
                                    <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                        {{ $product->name }}
                                    </a>
                                </h4>
                                
                                <div class="productBoxBtm">
                                {{-- Price Section --}}
                                <div class="product-price-row">
                                    <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                                    <!--<span class="old-price">₹1,699</span>
                                    <span class="discount">58% OFF</span>-->	
                                </div>
                                
                                {{-- Button Section --}}
                                <div class="rupess-btn-sec"> 
                                    <div class="leftPart">	
                                        {{-- Optional offer badge --}}
                                        <!--<div class="offer-badge">-->
                                        <!--  <span class="icon">%</span>-->
                                        <!--  <span class="text">BUY 2 FOR 1099</span>-->
                                        <!--</div>-->
                                    </div>	
                                    <div class="rightPart">		
                                        <form action="{{ route('frontend.addToCart') }}" method="POST">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="moq" value="1">
                                            <input type="hidden" name="price" value="{{ $product->price }}">
                                            <input type="hidden" name="color_id" value="">
                                            <input type="hidden" name="size_id" value="">
                                            
                                            <button type="submit" class="add-tocart-btn1 cart-btn">
												<svg viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="add-to-bag-svg">
												  <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2988 3.93606C11.2988 1.90046 9.60755 0.231445 7.5405 0.231445C5.47345 0.231445 3.7823 1.90053 3.7823 3.93593V5.46251H0.859289C0.504293 5.46251 0.23291 5.72707 0.23291 6.07314V15.6398C0.23291 17.3293 1.63178 18.693 3.3648 18.693H8.7463C8.52841 18.3161 8.34935 17.9139 8.21458 17.4919L3.36452 17.492C2.3205 17.492 1.48538 16.6778 1.48538 15.6601V6.70401L3.7823 6.70387V7.76225C3.7823 8.10833 4.05368 8.37289 4.40867 8.37289C4.76367 8.37289 5.03505 8.10833 5.03505 7.76225V6.70387H10.0461V7.76225C10.0461 8.10833 10.3175 8.37289 10.6725 8.37289C11.0275 8.37289 11.2988 8.10833 11.2988 7.76225V6.70387H13.5956V9.48248C13.7547 9.47015 13.9155 9.46387 14.0778 9.46387C14.3388 9.46387 14.5959 9.48011 14.8483 9.51163V6.07314C14.8483 5.72707 14.5769 5.46251 14.2219 5.46251L11.2988 5.46265V3.93606ZM7.5405 1.45243C8.91853 1.45243 10.046 2.57202 10.046 3.93572V5.4623H5.03498V3.93572C5.03498 2.57188 6.16246 1.45243 7.5405 1.45243Z" fill="#fff"></path>
												  <path d="M14.0773 12.5402C13.6997 12.5402 13.3936 12.8462 13.3936 13.2239V14.9334H11.6841C11.3065 14.9334 11.0004 15.2394 11.0004 15.6171C11.0004 15.9947 11.3065 16.3008 11.6841 16.3008H13.3936V18.0103C13.3936 18.3879 13.6997 18.694 14.0773 18.694C14.455 18.694 14.761 18.3879 14.761 18.0103V16.3008H16.4705C16.8482 16.3008 17.1542 15.9947 17.1542 15.6171C17.1542 15.2394 16.8482 14.9334 16.4705 14.9334H14.761V13.2239C14.761 12.8462 14.455 12.5402 14.0773 12.5402Z" fill="#fff"></path>
												</svg>     
                                                <span class="txt">Add to Cart</span>
                                            </button>
                                        </form>
                                    </div>
                                </div>
								</div>
								
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
@endif


{{-- OTHER PRODUCTS --}}
@if(isset($otherProducts) && $otherProducts->count())
    <div class="row rowBox2">
        @foreach($otherProducts as $product)
            <div class="col-xxl-4 col-xl-4 col-lg-4 col-md-4 col-sm-6 col-6 columnBox2 productBox">
                <div class="product-list-box">
                    <div class="product-list-box-inner">
                        <div class="product-list-box-img">
                            <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                <img src="{{ $product->thumbnail ? asset($product->thumbnail) : 'https://via.placeholder.com/300x300?text=No+Image' }}"
                                     class="img-fluid"
                                     alt="{{ $product->name }}">
                            </a>

                            @if(isset($product->is_combo_product) && $product->is_combo_product)
                                <div class="combo-tag">COMBO</div>
                            @endif
                        </div>

                        <div class="product-list-box-info">
                            <h4 class="proTitle">
                                <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                    {{ $product->name }}
                                </a>
                            </h4>
							
							 <div class="productBoxBtm">

                            <div class="product-price-row">
                                <span class="current-price">₹{{ number_format($product->price, 2) }}</span>
                                <!--<span class="old-price">₹1,699</span>
                                <span class="discount">58% OFF</span>-->
                            </div>

                            <div class="rupess-btn-sec">
                                <div class="rightPart">
                                    <form action="{{ route('frontend.addToCart') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                                        <input type="hidden" name="moq" value="1">
                                        <input type="hidden" name="price" value="{{ $product->price }}">
                                        <input type="hidden" name="color_id" value="">
                                        <input type="hidden" name="size_id" value="">

                                        <button type="submit" class="add-tocart-btn1 cart-btn">
                                            <svg viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="add-to-bag-svg">
												  <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2988 3.93606C11.2988 1.90046 9.60755 0.231445 7.5405 0.231445C5.47345 0.231445 3.7823 1.90053 3.7823 3.93593V5.46251H0.859289C0.504293 5.46251 0.23291 5.72707 0.23291 6.07314V15.6398C0.23291 17.3293 1.63178 18.693 3.3648 18.693H8.7463C8.52841 18.3161 8.34935 17.9139 8.21458 17.4919L3.36452 17.492C2.3205 17.492 1.48538 16.6778 1.48538 15.6601V6.70401L3.7823 6.70387V7.76225C3.7823 8.10833 4.05368 8.37289 4.40867 8.37289C4.76367 8.37289 5.03505 8.10833 5.03505 7.76225V6.70387H10.0461V7.76225C10.0461 8.10833 10.3175 8.37289 10.6725 8.37289C11.0275 8.37289 11.2988 8.10833 11.2988 7.76225V6.70387H13.5956V9.48248C13.7547 9.47015 13.9155 9.46387 14.0778 9.46387C14.3388 9.46387 14.5959 9.48011 14.8483 9.51163V6.07314C14.8483 5.72707 14.5769 5.46251 14.2219 5.46251L11.2988 5.46265V3.93606ZM7.5405 1.45243C8.91853 1.45243 10.046 2.57202 10.046 3.93572V5.4623H5.03498V3.93572C5.03498 2.57188 6.16246 1.45243 7.5405 1.45243Z" fill="#fff"></path>
												  <path d="M14.0773 12.5402C13.6997 12.5402 13.3936 12.8462 13.3936 13.2239V14.9334H11.6841C11.3065 14.9334 11.0004 15.2394 11.0004 15.6171C11.0004 15.9947 11.3065 16.3008 11.6841 16.3008H13.3936V18.0103C13.3936 18.3879 13.6997 18.694 14.0773 18.694C14.455 18.694 14.761 18.3879 14.761 18.0103V16.3008H16.4705C16.8482 16.3008 17.1542 15.9947 17.1542 15.6171C17.1542 15.2394 16.8482 14.9334 16.4705 14.9334H14.761V13.2239C14.761 12.8462 14.455 12.5402 14.0773 12.5402Z" fill="#fff"></path>
											</svg>     
                                            <span class="txt">Add to Cart</span>
                                        </button>
                                    </form>
                                </div>
                            </div>
							
							</div>
							
							
                        </div>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

            </div>
        </div>
    </div>
</section>

<style>
/* Dual-range slider styles */
.dual-range-wrap { width: 100%; margin-top: 8px; }
.range-container { position: relative; height: 30px; width: 100%; }

/* the full background track (light gray) */
.range-container::before {
    content: "";
    position: absolute;
    left: 0;
    right: 0;
    top: 50%;
    transform: translateY(-50%);
    height: 6px;
    background: #eee;
    border-radius: 6px;
    z-index: 1;
}

/* highlighted track between handles */
.slider-track {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    height: 6px;
    background: #ff3d6f; /* pink */
    border-radius: 6px;
    z-index: 2;
    left: 0;
    right: 0;
}

/* range inputs overlapping, invisible track (we use thumbs only) */
.range-input {
    -webkit-appearance: none;
    appearance: none;
    pointer-events: none; /* allow clicks through except thumb */
    position: absolute;
    left: 0; right: 0;
    top: 0;
    height: 30px;
    margin: 0;
    z-index: 3;
    background: transparent;
}

/* thumbs */
.range-input::-webkit-slider-thumb {
    -webkit-appearance: none;
    appearance: none;
    pointer-events: auto; /* enable thumb dragging */
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #ff3d6f;
    border: 3px solid #fff;
    box-shadow: 0 0 0 2px rgba(0,0,0,0.04);
    cursor: pointer;
}
.range-input::-moz-range-thumb {
    pointer-events: auto;
    width: 18px; height: 18px; border-radius: 50%;
    background: #ff3d6f; border: 3px solid #fff; cursor: pointer;
}

/* ensure top thumb z-ordering so it remains draggable when overlapped */
#rangeMax { z-index: 4; }
#rangeMin { z-index: 3; }

/* small polish for the price label */
#priceRangeLabel { font-weight: 600; margin-top: 6px; }

/* Combo styles */
.combo-product-display {
    background: #f8f9fa;
    padding: 8px;
    border-radius: 6px;
    margin-bottom: 10px;
}

.combo-images {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-wrap: wrap;
}

.combo-img-item {
    text-align: center;
    margin: 0 5px;
}

.combo-plus {
    margin: 0 5px;
    color: #666;
}

.no-products-box {
    text-align: center;
    padding: 60px 20px;
    width: 100%;
    background: #fff;
    border-radius: 8px;
    margin-top: 20px;
}

.no-products-box h3 {
    font-size: 24px;
    font-weight: 600;
    color: #333;
}

.no-products-box p {
    font-size: 16px;
    margin-top: 6px;
    color: #777;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {

    const rangeMin = document.getElementById("rangeMin");
    const rangeMax = document.getElementById("rangeMax");
    const minInput = document.getElementById("minPriceInput");
    const maxInput = document.getElementById("maxPriceInput");
    const priceLabel = document.getElementById("priceRangeLabel");
    const track = document.querySelector(".slider-track");

    function updateSlider() {
        let minVal = parseInt(rangeMin.value);
        let maxVal = parseInt(rangeMax.value);

        if (minVal > maxVal - 1) {
            rangeMin.value = maxVal - 1;
            minVal = maxVal - 1;
        }

        if (maxVal < minVal + 1) {
            rangeMax.value = minVal + 1;
            maxVal = minVal + 1;
        }

        // update hidden inputs
        minInput.value = minVal;
        maxInput.value = maxVal;

        // update price label
        priceLabel.innerText = `₹${minVal} - ₹${maxVal}+`;

        // update track background (Myntra style)
        let percent1 = (minVal / rangeMin.max) * 100;
        let percent2 = (maxVal / rangeMax.max) * 100;

        track.style.background = `linear-gradient(
            to right,
            #d7dcdf ${percent1}%,
            #ff2d55 ${percent1}%,
            #ff2d55 ${percent2}%,
            #d7dcdf ${percent2}%
        )`;
    }

    rangeMin.addEventListener("input", updateSlider);
    rangeMax.addEventListener("input", updateSlider);

    // Call it on page load to fix the full-width issue
    updateSlider();
});
</script>

{{-- COLOR FILTER JS --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tinycolor2@1.6.0/dist/tinycolor-min.js"></script>
<script>
    $(document).ready(function () {
        function initializeFilterColorSwatches() {
            $('.color-filter-sec .color-container input[data-color]').each(function () {
                const colorValue = $(this).data('color');
                const color = tinycolor(colorValue);
                const appliedColor = color.isValid() ? color.toHexString() : '#cccccc';
                $(this).siblings('.color-checkmark').css('background-color', appliedColor);
                if ($(this).is(':checked')) {
                    $(this).closest('.color-container').addClass('active');
                }
            });
        }

        $('.color-filter-sec .color-container input').on('change', function () {
            $('.color-filter-sec .color-container').removeClass('active');
            $(this).closest('.color-container').addClass('active');
        });

        const filterForm = document.getElementById('filterForm');
        const applyButton = document.getElementById('applyFilterBtn');
        if (filterForm && applyButton) {
            applyButton.addEventListener('click', function (event) {
                event.preventDefault();
                filterForm.submit();
            });
        }

        initializeFilterColorSwatches();
    });
</script>

@endsection
