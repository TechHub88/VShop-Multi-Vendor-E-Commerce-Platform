@extends('frontend.layout')

@section('content')

    <section class="bedcum-sec" style="">
        <div class="container">
            <div class="bedcum-inner">
                <div class="bed-cum-list">
                    <ul>
                        <li>
                            <a href="#">Shop</a>
                        </li>
                        <li>
                            <i class="fa-regular fa-angle-right"></i>
                        </li>
                        <li>
                            <span>All Products</span>
                        </li>
                        <li>
                            <i class="fa-regular fa-angle-right"></i>
                        </li>
                        <li>
                            <span>{{ $product->name }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
    <section class="product-details-sec py-0">
        <div class="container">
            <div class="product-details-inner">
                <div class="row rowBox g-4">

                    <!-- LEFT IMAGE GALLERY -->
                    <!-- LEFT IMAGE GALLERY -->
                    <div class="col-lg-6 columnBox product-details-left-box">
                        <div class="product-details-left">

                            <div class="product-gallery">

                                <!-- LEFT SIDE: THUMBNAIL SWIPER -->
                                <div class="thumbs-wrapper">
                                    <div class="swiper thumbs-swiper">
                                        <div class="swiper-wrapper">

                                            @foreach ($images as $img)
                                                <div class="swiper-slide">
                                                    <div class="small-thumb">
                                                        <img src="{{ asset($img) }}" class="img-fluid w-100"
                                                            alt="Product Thumbnail">
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                    <!-- Up/Down navigation for thumbnails -->
                                    <div class="swiper-button-next">
                                        <i class="fa-regular fa-angle-left"></i>
                                    </div>
                                    <div class="swiper-button-prev">
                                        <i class="fa-regular fa-angle-right"></i>
                                    </div>
                                </div>


                                <!-- RIGHT SIDE: MAIN IMAGE SWIPER -->
                                <div class="main-wrapper">

                                    @if (!empty($product->fit_type))
                                        <span class="fit-pill">{{ strtoupper($product->fit_type) }}</span>
                                    @endif

                                    <div class="swiper main-swiper">
                                        <div class="swiper-wrapper">

                                            @foreach ($images as $index => $img)
                                                <div class="swiper-slide">
                                                    <div class="big-thumb">
                                                        <a href="{{ asset($img) }}" data-fancybox="gallery"
                                                            data-caption="Image {{ $index + 1 }}"
                                                            data-thumb="{{ asset($img) }}">
                                                            <img src="{{ asset($img) }}" class="img-fluid w-100"
                                                                alt="Product Image">
                                                        </a>
                                                    </div>
                                                </div>
                                            @endforeach

                                        </div>
                                    </div>

                                    <!-- Custom next/prev on main image -->
                                    <button class="swiper-button-prev-custom">
                                        <i class="fa-regular fa-angle-left"></i>
                                    </button>

                                    <button class="swiper-button-next-custom">
                                        <i class="fa-regular fa-angle-right"></i>
                                    </button>

                                </div>

                            </div>




                        </div>
                    </div>





                    <!-- RIGHT PRODUCT DETAILS -->
                    <div class="col-lg-6 columnBox product-details-right-box">
                        <div class="product-details-right">
                            <p class=" mb-2 productCat">Category : {{ $product->category->name ?? '' }}</p>
                            <h3 class="product-title mb-2">{{ $product->name }}</h3>


                            <div class="d-flex justify-content-between productPriceRatingArea">
                                <div class="mb-2 leftPart">
                                    <!--{{-- <div class="product-price text-dark fw-bold">
                                <span id="productPrice">{{ number_format($product->price, 2) }}</span>
                            </div>                           
                            <input type="hidden" name="price" id="priceInput" value="{{ $product->price }}"> --}}-->
                                   <div class="product-price-row">
    <span class="current-price secondary-price" data-base="{{ $product->price }}">
        ₹{{ number_format($product->price, 2) }}
    </span>

    <span class="old-price secondary-old-price" data-regular="{{ $product->regular_price }}">
        ₹{{ number_format($product->regular_price, 2) }}
    </span>

    <span class="discount secondary-discount">
        ₹{{ $product->price }}
    </span>
    <span class="tax-text">Inclusive of all taxes</span>
</div>



                                </div>
                                <div class="mb-2 rightPart">
                                    <!-- Review Rating Section -->
                                    <div class="mb-0 rating-sec">
                                        <p class="mb-0">{{ number_format($product->average_rating, 1) }}</p>
                                        <div class="star-rating">
                                            @php
                                                $averageRating = $product->average_rating;
                                            @endphp
                                            @for ($i = 1; $i <= 5; $i++)
                                                @if ($i <= floor($averageRating))
                                                    <i class="fa-solid fa-star"></i>
                                                @elseif($i - 0.5 <= $averageRating)
                                                    <i class="fa-solid fa-star-half-stroke"></i>
                                                @else
                                                    <i class="fa-regular fa-star"></i>
                                                @endif
                                            @endfor
                                        </div>
                                        <a href="#reviews-section" class="review-count">{{ $product->total_reviews }}
                                            Review{{ $product->total_reviews != 1 ? 's' : '' }}</a>
                                    </div>
                                </div>
                            </div>
                            <!--
           <div class="mb-2 last-bought-txt">
           <span class="txt">52 people bought this in the last 7 days</span>
           </div>
           -->

                            <form action="{{ route('frontend.addToCart') }}" method="POST" class="add-to-cart-form" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="product_id" value="{{ $product->id }}">
                                <input type="hidden" name="price" id="priceInput" value="{{ $product->price }}">
                                <input type="hidden" name="moq" id="moqInput" value="{{ $product->moq }}">
                                <input type="hidden" name="color_id" id="selectedColor"
                                    value="{{ $defaultColorTerm['term_id'] ?? '' }}">
                                <input type="hidden" name="size_id" id="selectedSize"
                                    value="">

                                <!-- Main Product Attributes (for individual products) -->
                                <input type="hidden" name="main_product_attributes" id="mainProductAttributesInput" value="">
                                <input type="hidden" name="main_product_quantity" id="mainProductQuantityInput" value="1">
                                <input type="hidden" name="main_product_price" id="mainProductPriceInput" value="{{ $product->price }}">

                                @if (!empty($attributeOptions))
                                    @foreach ($attributeOptions as $option)
                                        <input type="hidden" name="attributes[{{ $option['attribute_id'] }}]"
                                            id="attributeHidden_{{ $option['attribute_id'] }}"
                                            value="{{ $defaultSelections[$option['attribute_id']]['term_id'] ?? '' }}">
                                    @endforeach
                                @endif

                                <div class="mb-2 qualityFeatureBedgeArea">
                                    <span class="qualityBedge"><span class="txt">100% Cotton</span></span>
                                </div>

                                <!--<h3 class="product-unit-price mb-2 text-dark fw-bold">
                                    <span>Sale Price:</span>
                                    <span id="unitPriceDisplay" data-price="{{ $product->price }}">
                                        ₹{{ number_format($product->price, 2) }}
                                    </span>
                                </h3>-->
                                <div class="quantityPriceListArea">
                                <h4 class="ttl">Buy this in Bulk and get Extra Discounts</h4>
                                <div class="quantityPriceListAreainner">
                                <div class="quantityPriceList">
                                @foreach($product->samplePrices as $i => $tier)
                                    <div class="quantityPriceListItem">
                                        <div class="quantityPriceListIteminner">
                                            <h5 class="proQuantity">
                                                @if($tier->name == 1 || $tier->name === '1')
                                                    SAMPLE
                                                @else
                                                    {{ $tier->name }}+ pcs
                                                @endif
                                            </h5>
                                            <div class="prPrice">₹{{ (int)$tier->price }}</div>
                                        </div>
                                    </div>
                                @endforeach
                                </div>
                                </div>
                                </div>
								
                                {{-- Combination not available alert removed as per requirements --}}
                                <!--<p class="mb-2 total-product-unit-price">
                                        Total (<span id="quantityLabel">{{ $product->moq ?? 1 }}</span> units):
                                        <strong id="totalPriceDisplay">₹{{ number_format($product->price * ($product->moq ?? 1), 2) }}</strong>
                                    </p>-->


                                @if (!empty($attributeOptions))
                                    @foreach ($attributeOptions as $option)
                                        @php
                                            $attributeId = $option['attribute_id'];
                                            $attributeName = $option['attribute_name'];
                                            $attributeSlug = strtolower($option['attribute_slug']);
                                            $terms = $option['terms'];
                                            $defaultTerm = $defaultSelections[$attributeId] ?? null;
                                            $defaultTermName =
                                                $defaultTerm['term_name'] ?? ($terms[0]['term_name'] ?? '');
                                        @endphp

                                        @php
                                            $isSizeAttribute = strtolower($attributeSlug ?? '') === 'size' || strtolower($attributeName ?? '') === 'size' || (strtolower($attributeSlug ?? '') ?? '') === 'sizes';
                                            if ($isSizeAttribute) {
                                                $defaultTermName = 'Please select';
                                            }
                                        @endphp

                                        <div class="product-attribute-block {{ $attributeName }} mb-2">
                                            <div class="mb-2 d-flex justify-content-between attributeHeadingSec">
                                                <div class="leftPart">
                                                    @if ($isSizeAttribute)
                                                        <div id="sizeErrorMessage" class="text-danger mb-1" style="display:none;">Please select size first!</div>
                                                    @endif
                                                    <p class="mb-0 blackColor">Select {{ $attributeName }}:</p>
                                                </div>
                                                <div class="rightPart">
                                                    <a href="#" class="sizeGuideBtn" data-bs-toggle="modal"
                                                        data-bs-target="#sizeGuideModal">Size guide</a>
                                                    <p class="selectItem">Selected {{ ucfirst($attributeName) }}:
                                                        <strong
                                                            id="selectedAttributeLabel_{{ $attributeId }}">{{ $defaultTermName }}</strong>
                                                    </p>
                                                </div>
                                            </div>

                                            <!-- Size Guide Modal -->
                                            <div class="modal sizeGuideModal" id="sizeGuideModal">
                                                <div class="modal-dialog sizeGuideModalDiolog">
                                                    <div class="modal-content sizeGuideModalContent">
                                                        <div class="modal-header sizeGuideModalHeader">
                                                            <h4 class="modal-title">Size Guide</h4>
                                                            <button type="button" class="btn-close"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body sizeGuideModalBody">
                                                            <div class="text-center sizeGuideThumnail">
                                                                <img src="{{ asset('assets/images/sizeGuideImage1.jpg') }}"
                                                                    class="img-fluid" alt="Size Guide">
                                                            </div>
                                                            <div class="sizeGuideDetailsArea">
                                                                <div class="sizeGuideTabNavArea">
                                                                    <ul class="nav nav-pills">
                                                                        <li class="nav-item">
                                                                            <a class="nav-link active"
                                                                                data-bs-toggle="pill"
                                                                                href="#menu1">In</a>
                                                                        </li>
                                                                        <li class="nav-item">
                                                                            <a class="nav-link" data-bs-toggle="pill"
                                                                                href="#menu2">Cms</a>
                                                                        </li>
                                                                    </ul>
                                                                </div>
                                                                <div class="sizeGuideTabContentArea">
                                                                    <div class="d-block tab-content">

                                                                        <!-- INCHES TAB -->
                                                                        <div class="tab-pane active" id="menu1">
                                                                            <div class="table-responsive">
                                                                                <table
                                                                                    class="w-100 sizeGuide_table sizeGuide_portrait">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Size</th>
                                                                                            <th>Chest (in Inch)</th>
                                                                                            <th>Front Length (in Inch)</th>
                                                                                            <th>Sleeve Length (in Inch)</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @if ($sizeGuides && $sizeGuides->count())
                                                                                            @foreach ($sizeGuides as $size)
                                                                                                <tr>
                                                                                                    <td>{{ $size->size_label ?? '-' }}
                                                                                                    </td>
                                                                                                    <td>{{ $size->chest_in ?? '-' }}
                                                                                                    </td>
                                                                                                    <td>{{ $size->front_length_in ?? '-' }}
                                                                                                    </td>
                                                                                                    <td>{{ $size->sleeve_length_in ?? '-' }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        @else
                                                                                            <tr>
                                                                                                <td colspan="4"
                                                                                                    class="text-center">
                                                                                                    Size guide not available
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endif
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>

                                                                        <!-- CMS TAB -->
                                                                        <div class="tab-pane" id="menu2">
                                                                            <div class="table-responsive">
                                                                                <table
                                                                                    class="w-100 sizeGuide_table sizeGuide_portrait">
                                                                                    <thead>
                                                                                        <tr>
                                                                                            <th>Size</th>
                                                                                            <th>Chest (in Cms)</th>
                                                                                            <th>Front Length (in Cms)</th>
                                                                                            <th>Sleeve Length (in Cms)</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @if ($sizeGuides && $sizeGuides->count())
                                                                                            @foreach ($sizeGuides as $size)
                                                                                                <tr>
                                                                                                    <td>{{ $size->size_label ?? '-' }}
                                                                                                    </td>
                                                                                                    <td>{{ $size->chest_cm ?? '-' }}
                                                                                                    </td>
                                                                                                    <td>{{ $size->front_length_cm ?? '-' }}
                                                                                                    </td>
                                                                                                    <td>{{ $size->sleeve_length_cm ?? '-' }}
                                                                                                    </td>
                                                                                                </tr>
                                                                                            @endforeach
                                                                                        @else
                                                                                            <tr>
                                                                                                <td colspan="4"
                                                                                                    class="text-center">
                                                                                                    Size guide not available
                                                                                                </td>
                                                                                            </tr>
                                                                                        @endif
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>





                                            @if (in_array($attributeSlug, ['colour', 'color']))
                                                <div class="details-color-sec mb-0">
                                                    <div class="color-bottom">
                                                        <div class="color-left">
                                                            <div class="color-box">
                                                                @foreach ($terms as $index => $term)
                                                                    <label class="color-container">
                                                                        <input type="radio"
                                                                            class="product-attribute-option"
                                                                            name="attribute_{{ $attributeId }}"
                                                                            value="{{ $term['term_id'] }}"
                                                                            data-attribute-id="{{ $attributeId }}"
                                                                            data-attribute-slug="{{ $attributeSlug }}"
                                                                            data-term-name="{{ $term['term_name'] }}"
                                                                            data-color="{{ $term['color_code'] ?? $term['term_name'] }}"
                                                                            {{ $defaultTerm && $defaultTerm['term_id'] === $term['term_id'] ? 'checked' : (!$defaultTerm && $index === 0 ? 'checked' : '') }}>
                                                                        <span class="color-checkmark"></span>
                                                                    </label>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                        <!--<div class="color-right">
                                                                <p>{{ $attributeName }} :
                                                                    <span class="blackColor" id="selectedColorName">{{ $defaultColorTerm['term_name'] ?? $defaultTermName }}</span>
                                                                </p>
                                                            </div>-->
                                                    </div>
                                                </div>
                                                <!--<p class="mt-2">
                                                        Selected {{ ucfirst($attributeName) }}:
                                                        <strong id="selectedAttributeLabel_{{ $attributeId }}">{{ $defaultTermName }}</strong>
                                                    </p>-->
                                            @elseif($attributeSlug === 'size')
                                                <div class="selectSizeListArea mb-2">
                                                <div class="selectSizeList">

                                                @foreach($terms as $index => $term)
                                                    <div class="selectSizeListItem">
                                                        <input class="radioInput" type="radio" name="productSize" id="size_{{ $term['term_id'] }}" value="{{ $term['term_id'] }}" {{ $defaultTerm && $defaultTerm['term_id'] === $term['term_id'] ? 'checked' : (!$defaultTerm && $index === 0 ? 'checked' : '') }}>
                                                        <label class="selectSizeListIteminner" for="size_{{ $term['term_id'] }}">
                                                            <div class="sizeName">{{ $term['term_name'] }}</div>
                                                            <input type="number" min="0" value="" step="1" inputmode="numeric" pattern="[0-9]*" class="qtyInput" data-size-id="{{ $term['term_id'] }}" placeholder="">
                                                        </label>
                                                    </div>
                                                @endforeach

                                                </div>

                                                <!-- hide native number arrows for size inputs and normalize negatives -->
                                                <style>
                                                .selectSizeList .qtyInput::-webkit-outer-spin-button,
                                                .selectSizeList .qtyInput::-webkit-inner-spin-button {
                                                    -webkit-appearance: none;
                                                    margin: 0;
                                                }
                                                .selectSizeList .qtyInput {
                                                    -moz-appearance: textfield;
                                                }
                                                </style>

                                                <script>
                                                (function(){
                                                    var inputs = document.querySelectorAll('.selectSizeList .qtyInput');
                                                    function sanitizeValue(val){ return String(val).replace(/[^0-9]/g,''); }
                                                    inputs.forEach(function(input){
                                                        input.setAttribute('min','0');
                                                        input.setAttribute('inputmode','numeric');
                                                        input.setAttribute('pattern','[0-9]*');
                                                        input.setAttribute('step','1');
                                                        // allow blank initially; sanitize user input but keep empty allowed
                                                        input.addEventListener('input', function(){
                                                            var sanitized = sanitizeValue(this.value);
                                                            if (sanitized === '') { this.value = ''; return; }
                                                            this.value = String(Math.floor(Number(sanitized)));
                                                            // Hide error message when user types in quantity input
                                                            var errorMsg = document.getElementById('sizeErrorMessage');
                                                            if (errorMsg && this.value && parseInt(this.value, 10) > 0) {
                                                                errorMsg.style.display = 'none';
                                                            }
                                                        });
                                                    });
                                                })();
                                                </script>

                                                </div>
											
                                                <!--<div class="size-sec mb-2">
                                                    <div class="d-flex flex-wrap gap-2">
                                                        @foreach ($terms as $index => $term)
                                                            <label class="size-container">
                                                                <input type="radio" class="product-attribute-option"
                                                                    name="attribute_{{ $attributeId }}"
                                                                    value="{{ $term['term_id'] }}"
                                                                    data-attribute-id="{{ $attributeId }}"
                                                                    data-attribute-slug="{{ $attributeSlug }}"
                                                                    data-term-name="{{ $term['term_name'] }}"
                                                                    {{ $defaultTerm && $defaultTerm['term_id'] === $term['term_id'] ? 'checked' : (!$defaultTerm && $index === 0 ? 'checked' : '') }}>
                                                                <span
                                                                    class="size-checkmark">{{ $term['term_name'] }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                </div>-->
                                                <!--<p class="mt-2">
                                                        Selected {{ ucfirst($attributeName) }}:
                                                        <strong id="selectedAttributeLabel_{{ $attributeId }}">{{ $defaultTermName }}</strong>
                                                    </p>-->
                                            @else
                                                <div class="generic-attribute-options d-flex flex-wrap gap-2">
                                                    @foreach ($terms as $index => $term)
                                                        <label class="size-container">
                                                            <input type="radio" class="product-attribute-option"
                                                                name="attribute_{{ $attributeId }}"
                                                                value="{{ $term['term_id'] }}"
                                                                data-attribute-id="{{ $attributeId }}"
                                                                data-attribute-slug="{{ $attributeSlug }}"
                                                                data-attribute-type="{{ $isSizeAttribute ? 'size' : '' }}"
                                                                data-term-name="{{ $term['term_name'] }}"
                                                                {{ $isSizeAttribute ? '' : ($defaultTerm && $defaultTerm['term_id'] === $term['term_id'] ? 'checked' : (!$defaultTerm && $index === 0 ? 'checked' : '')) }}>
                                                            <span class="size-checkmark">{{ $term['term_name'] }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <!--<p class="mt-2">
                                                        Selected {{ ucfirst($attributeName) }}:
                                                        <strong id="selectedAttributeLabel_{{ $attributeId }}">{{ $defaultTermName }}</strong>
                                                    </p>-->
                                            @endif
                                        </div>
                                    @endforeach
                                @endif

                                <div class="mb-0 product-bottom-sec">

                                    <!--<div class="w-100 me-2 product-quantity d-flex align-items-center">
                                        <button type="button" class="btn btn-outline-secondary sub" id="decrementBtn">
                                            <i class="fa-solid fa-minus"></i>
                                        </button>
                                        <input type="text" name="moq" id="quantityDisplay"
                                            class="form-control text-center count" value="1">
                                        <button type="button" class="btn btn-outline-secondary add" id="incrementBtn">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                    </div>-->



                                    <!-- BUY NOW BUTTON Section -->
                                    @if (Auth::guard('customer')->check())
                                        <!-- Logged in → go to cart directly, no modal -->
                                        <button type="button" id="buyNowBtn"
                                            class="w-100 me-2 btn btn-primary buy-now-btn"
                                            data-target-url="{{ route('frontend.cart') }}">
                                            Buy Now
                                        </button>
                                    @else
                                        <!-- Not logged in → redirect to employee login with current page URL -->
                                        <a href="{{ route('frontend.employeelogin') }}?return_to={{ url()->current() }}"
                                            id="buyNowBtnLink"
                                            data-target-url="{{ route('frontend.employeelogin') }}?return_to={{ url()->current() }}"
                                            class="w-100 me-2 btn btn-primary buy-now-btn text-center text-decoration-none">
                                            Buy Now
                                        </a>
                                    @endif

                                    <!-- ADD TO CART BUTTON Section -->
                                    @if (Auth::guard('customer')->check())
                                        <!-- Logged in → Normal Add to Cart -->
                                        <button type="submit" id="addToCartBtn"
                                            class="w-100 me-2 btn btn-primary add-tocart-btn">Add to Cart</button>
                                    @else
                                        <!-- Not logged in → redirect to login with current page URL -->
                                        <a href="{{ route('frontend.employeelogin') }}?return_to={{ urlencode(url()->current()) }}"
                                            id="addToCartBtnLink"
                                            class="w-100 me-2 btn btn-primary add-tocart-btn text-center text-decoration-none">
                                            Add to Cart
                                        </a>
                                    @endif
									
									<a href="#"                            
                                            class="w-100 btn btn-primary add-tocart-btn text-center text-decoration-none enquiry-now-btn">
                                            Enquiry Now
                                        </a>




                                    <!--<button class="share-btn"><i class="fa-light fa-share-nodes"></i></button>-->
                                </div>
                                
                                <!-- Embroidery Section - Must be inside form -->
                                <div class="mt-2 proEmbroideryArea">
                                    <div class="d-flex proEmbroideryTopArea">
                                        <div class="leftPart">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="interestedEmbroideryCheckbox" name="interested_in_embroidery_checkbox" value="1">
                                                <label class="form-check-label" for="interestedEmbroideryCheckbox">Interested in Embroidery</label>
                                            </div>
                                            <input type="hidden" name="interested_in_embroidery" id="interestedInEmbroideryHidden" value="0">
                                        </div>
                                        <div class="rightPart"></div>
                                    </div>
                                    <div class="proEmbroideryDtlsArea" id="proEmbroideryDtlsArea" style="display:none;">
                                        <div class="form-group">
                                            <label class="labelName">Embroidery Logo File *</label>
                                            <input type="file" class="form-control" name="embroidery_logo" id="embroideryLogoFile" accept="image/*">
                                            <div id="embroideryLogoPreview" style="margin-top: 15px; display: none;">
                                                <img id="embroideryLogoPreviewImg" src="" alt="Logo Preview" style="max-width: 120px; max-height: 126px; border: 1px solid #ddd; border-radius: 4px; padding: 0px; background: #f9f9f9;">
                                            </div>
                                        </div>
                                        <div class="proEmbroideryPriceContent">
                                            @php
                                                $embTiers = collect();
                                                if (\Illuminate\Support\Facades\Schema::hasTable('product_embroidery_prices')) {
                                                    try {
                                                        $embTiers = $product->embroideryPrices ?? collect();
                                                    } catch (\Exception $e) {
                                                        $embTiers = collect();
                                                    }
                                                }
                                            @endphp
                                            @forelse($embTiers as $idx => $tier)
                                                @php
                                                    $labelName = $tier->name;
                                                    $priceVal = number_format((float)($tier->price ?? 0), 2);
                                                    $isSample = ($labelName === '1' || $labelName == 1);
                                                    $text = $isSample
                                                        ? 'per embroidery logo charge in a sample order.'
                                                        : 'per Embroidery Logo charge if order ' . $labelName . '+';
                                                    $paraClass = $loop->last ? 'mb-0 para' : 'para';
                                                @endphp
                                                <p class="{{ $paraClass }}"><strong>₹{{ $priceVal }}</strong> {{ $text }}</p>
                                            @empty
                                                <p class="para">Embroidery pricing not set.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <!-- Partner message when quantity > 50 -->
                            <div class="partner-message" id="partnerMessage" style="display: none; margin-top: 10px;">
                                <div class="alert alert-info p-2">
                                    <i class="fa-solid fa-handshake me-2"></i>
                                    <strong>You can become a partner!</strong>
                                    <a href="{{ route('partner.page') }}" class="ms-1" target="_blank">Click here</a>
                                </div>
                            </div>



                            <p class="text-muted mt-2 mb-1 deliveryText">Delivery within <strong>5 - 7 Days</strong>.</p>

                            <div class="delivery-secBox">
                                <h6 class="mb-2 title">Check for Delivery Details</h6>
                                <div class="delivery-sec">
                                    <div class="delivery-box">
                                        <input type="text" placeholder="Enter Pincode">
                                        <button class="check-btn">Check</button>
                                    </div>
                                    <!--<p>
                                        <i class="fa-regular fa-circle-info"></i>
                                        {{-- Easy 14 days return and exchange --}}
                                    </p>-->
                                </div>
                            </div>
							
                            <script>
                            document.addEventListener('DOMContentLoaded', function() {
                                var embroideryCheckbox = document.getElementById('interestedEmbroideryCheckbox');
                                var embroideryDetails = document.getElementById('proEmbroideryDtlsArea');
                                var embroideryHidden = document.getElementById('interestedInEmbroideryHidden');
                                var embroideryLogoFile = document.getElementById('embroideryLogoFile');
                                var embroideryLogoPreview = document.getElementById('embroideryLogoPreview');
                                var embroideryLogoPreviewImg = document.getElementById('embroideryLogoPreviewImg');
                                
                                if (embroideryCheckbox && embroideryDetails) {
                                    embroideryCheckbox.addEventListener('change', function() {
                                        embroideryDetails.style.display = this.checked ? '' : 'none';
                                        if (embroideryHidden) {
                                            embroideryHidden.value = this.checked ? '1' : '0';
                                        }
                                        // Hide preview when checkbox is unchecked
                                        if (!this.checked && embroideryLogoPreview) {
                                            embroideryLogoPreview.style.display = 'none';
                                            if (embroideryLogoPreviewImg) {
                                                embroideryLogoPreviewImg.src = '';
                                            }
                                        }
                                    });
                                }
                                
                                // Handle logo file selection and show preview
                                if (embroideryLogoFile && embroideryLogoPreview && embroideryLogoPreviewImg) {
                                    embroideryLogoFile.addEventListener('change', function(e) {
                                        var file = e.target.files[0];
                                        if (file) {
                                            // Check if file is an image
                                            if (file.type.match('image.*')) {
                                                var reader = new FileReader();
                                                reader.onload = function(e) {
                                                    embroideryLogoPreviewImg.src = e.target.result;
                                                    embroideryLogoPreview.style.display = 'block';
                                                };
                                                reader.readAsDataURL(file);
                                            } else {
                                                alert('Please select a valid image file.');
                                                embroideryLogoFile.value = '';
                                                embroideryLogoPreview.style.display = 'none';
                                                embroideryLogoPreviewImg.src = '';
                                            }
                                        } else {
                                            embroideryLogoPreview.style.display = 'none';
                                            embroideryLogoPreviewImg.src = '';
                                        }
                                    });
                                }
                            });
                            </script>
							</div>


                            <div class="mt-3 keyHighlight-secBox">
                                <h6 class="kh-title">Key Highlights</h6>

                                <div class="kh-grid">
                                    <div class="kh-item">
                                        <span class="kh-label">Design</span>
                                        <span class="kh-value">Graphic Print</span>
                                    </div>

                                    <div class="kh-item">
                                        <span class="kh-label">Fit</span>
                                        <span class="kh-value">Oversized Fit</span>
                                    </div>

                                    <div class="kh-item">
                                        <span class="kh-label">Neck</span>
                                        <span class="kh-value">Round Neck</span>
                                    </div>

                                    <div class="kh-item">
                                        <span class="kh-label">Occasion</span>
                                        <span class="kh-value">Casual Wear</span>
                                    </div>

                                    <div class="kh-item">
                                        <span class="kh-label">Sleeve Style</span>
                                        <span class="kh-value">Half Sleeve</span>
                                    </div>

                                    <div class="kh-item">
                                        <span class="kh-label">Wash Care</span>
                                        <span class="kh-value">Gentle Machine Wash</span>
                                    </div>
                                </div>

                            </div>


                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- PRODUCT DETAILS BOTTOM -->
    <section class="product-details-bottom py-5">
        <div class="container">

            <div class="mb-4 singleProductDtlsAccordian">
                <div class="accordion custom-accordion" id="productAccordion">


                    <!-- Accordion Item 1 - Product Details -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingOne">
                            <button class="accordion-button collapsed d-flex align-items-center" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseOne">

                                <div class="icon-box me-3">
                                    <i class="fa-light fa-clipboard-list"></i>
                                </div>

                                <div class="text-start">
                                    <div class="title">Product Details</div>
                                    <!--<div class="subtitle">Manufacture, Care and Fit</div>-->
                                </div>
                            </button>
                        </h2>

                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                            <div class="accordion-body">
                                {{ $product->description ?? 'No Description Available.' }}
                            </div>
                        </div>
                    </div>

                    <!-- Accordion Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingTwo">
                            <button class="accordion-button collapsed d-flex align-items-center" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseTwo">

                                <div class="icon-box me-3">
                                    <i class="fa-light fa-clipboard-list-check"></i>
                                </div>

                                <div class="text-start">
                                    <div class="title">Product Specification</div>
                                    <!--<div class="subtitle">Know about return & exchange policy</div>-->
                                </div>
                            </button>
                        </h2>

                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                            <div class="accordion-body">
                                <div class="speacification-table">
                                    <table>
                                        <tbody>
                                            <tr>
                                                <td class="Specification-box">Fabric</td>
                                                <td>70% Cotton, 30% Polyester - Long Lasting durable Material</td>
                                            </tr>
                                            <tr>
                                                <td class="Specification-box">Sleeves</td>
                                                <td>Full Sleeves</td>
                                            </tr>
                                            <tr>
                                                <td class="Specification-box">Slim Fit</td>
                                                <td>Yes</td>
                                            </tr>
                                            <tr>
                                                <td class="Specification-box">Stretchable</td>
                                                <td>No</td>
                                            </tr>
                                            <tr>
                                                <td class="Specification-box">Wash Care</td>
                                                <td>Dry Clean</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingThree">
                            <button class="accordion-button collapsed d-flex align-items-center" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseThree">

                                <div class="icon-box me-3">
                                    <i class="fa-light fa-clipboard-question"></i>
                                </div>

                                <div class="text-start">
                                    <div class="title">Frequently Asked Questions</div>
                                    <div class="subtitle"><a href="#">Any Query? Ask Us</a></div>
                                </div>
                            </button>
                        </h2>

                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                            <div class="accordion-body">
                                <div class="quary-sec">
                                    <ul>
                                        <li>
                                            <h3><span>Q.</span>Can this clothes be used for gym or gymnastics?</h3>
                                            <p><span>A.</span> No</p>
                                        </li>
                                        <li class="border0">
                                            <h3><span>Q.</span>Is this made up of 100% Cotton material?</h3>
                                            <p><span>A.</span> No, 70% Cotton, 30% Polyester - Long Lasting durable Material
                                            </p>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Accordion Item 4 - Reviews & Ratings -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingReviews">
                            <button class="accordion-button collapsed d-flex align-items-center" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseReviews">

                                <div class="icon-box me-3">
                                    <i class="fa-light fa-star"></i>
                                </div>

                                <div class="text-start">
                                    <div class="title">Ratings & Reviews</div>
                                    <div class="subtitle">See what customers are saying</div>
                                </div>
                            </button>
                        </h2>

                        <div id="collapseReviews" class="accordion-collapse collapse" data-bs-parent="#productAccordion">
                            <div class="accordion-body" id="reviews-section">
                                <!-- Comprehensive Review Rating Section -->
                                <div class="review-rating-section">
                                    <div class="row">
                                        <!-- Left: Rating Overview -->
                                        <div class="col-md-4">
                                            <div class="rating-overview text-center p-4 border rounded">
                                                <div class="overall-rating mb-3">
                                                    <h1 class="text-dark mb-1">
                                                        {{ number_format($product->average_rating, 1) }}</h1>
                                                    <div class="star-rating mb-2">
                                                        @php
                                                            $averageRating = $product->average_rating;
                                                        @endphp
                                                        @for ($i = 1; $i <= 5; $i++)
                                                            @if ($i <= floor($averageRating))
                                                                <i class="fa-solid fa-star text-warning"></i>
                                                            @elseif($i - 0.5 <= $averageRating)
                                                                <i class="fa-solid fa-star-half-stroke text-warning"></i>
                                                            @else
                                                                <i class="fa-regular fa-star text-warning"></i>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <p class="text-muted">{{ $product->total_reviews }}
                                                        Rating{{ $product->total_reviews != 1 ? 's' : '' }} &
                                                        {{ $product->total_reviews }}
                                                        Review{{ $product->total_reviews != 1 ? 's' : '' }}</p>
                                                </div>

                                                <!-- Rating Breakdown -->
                                                @if ($product->total_reviews > 0)
                                                    <div class="rating-breakdown">
                                                        @php
                                                            $ratingBreakdown = $product->rating_breakdown;
                                                            $totalReviews = $product->total_reviews;
                                                        @endphp

                                                        @for ($i = 5; $i >= 1; $i--)
                                                            @php
                                                                $count = $ratingBreakdown[$i] ?? 0;
                                                                $percentage =
                                                                    $totalReviews > 0
                                                                        ? ($count / $totalReviews) * 100
                                                                        : 0;
                                                            @endphp
                                                            <div class="rating-bar d-flex align-items-center mb-2">
                                                                <span class="me-2 small">{{ $i }} <i
                                                                        class="fa-solid fa-star text-warning"></i></span>
                                                                <div class="progress flex-grow-1 me-2"
                                                                    style="height: 8px;">
                                                                    <div class="progress-bar bg-dark"
                                                                        style="width: {{ $percentage }} %"></div>
                                                                </div>
                                                                <span class="small text-muted">{{ $count }}</span>
                                                            </div>
                                                        @endfor
                                                    </div>
                                                @endif
                                            </div>

                                            <!-- Write Review Button -->
                                             <div class="text-center mb-3">
    @php
        $canReview = false;
        $customer = Auth::guard('customer')->user();
        
        if ($customer) {
            // Check if customer has purchased this product with status "Completed"
            $hasPurchased = \App\Models\Order::where('customer_id', $customer->id)
                ->where('status', 'Completed') // Check for "Completed" status
                ->whereHas('items', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->exists();
            
            $canReview = $hasPurchased;
        }
        
        // Also check if customer hasn't already reviewed this product
        if ($canReview) {
            $hasReviewed = \App\Models\ProductReview::where('product_id', $product->id)
                ->where('customer_id', $customer->id)
                ->exists();
            
            $canReview = !$hasReviewed;
        }
    @endphp
    
    @if ($canReview)
        <!-- Show modal trigger button -->
        <button class="btn btn-dark w-100" data-bs-toggle="modal" data-bs-target="#writeReviewModal">
            <i class="fa-light fa-pen me-2"></i>Write a Review
        </button>
    @elseif(!Auth::guard('customer')->check())
        <!-- Not logged in: redirect to login -->
        <a href="{{ route('frontend.employeelogin') }}" class="btn btn-dark w-100">
            <i class="fa-light fa-pen me-2"></i>Write a Review
        </a>
    @else
        <!-- Logged in but hasn't purchased or already reviewed -->
        <button class="btn btn-dark w-100" disabled>
            <i class="fa-light fa-pen me-2"></i>
            @if($customer && isset($hasReviewed) && $hasReviewed)
                You have already reviewed this product
            @else
                You must purchase this product to review
            @endif
        </button>
    @endif
</div>


                                        <!-- Right: Reviews List -->
                                        <div class="col-md-8">
                                            <div class="reviews-list">
                                                <!-- Review Filters -->
                                                @if ($product->total_reviews > 0)
                                                    <div class="review-filters mb-4">
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @php
                                                                $ratingBreakdown = $product->rating_breakdown;
                                                            @endphp
                                                            <button class="btn btn-outline-dark btn-sm active"
                                                                data-filter="all">All
                                                                ({{ $product->total_reviews }})</button>
                                                            <button class="btn btn-outline-dark btn-sm" data-filter="5">5
                                                                Star ({{ $ratingBreakdown[5] ?? 0 }})</button>
                                                            <button class="btn btn-outline-dark btn-sm" data-filter="4">4
                                                                Star ({{ $ratingBreakdown[4] ?? 0 }})</button>
                                                            <button class="btn btn-outline-dark btn-sm" data-filter="3">3
                                                                Star ({{ $ratingBreakdown[3] ?? 0 }})</button>
                                                            <button class="btn btn-outline-dark btn-sm" data-filter="2">2
                                                                Star ({{ $ratingBreakdown[2] ?? 0 }})</button>
                                                            <button class="btn btn-outline-dark btn-sm" data-filter="1">1
                                                                Star ({{ $ratingBreakdown[1] ?? 0 }})</button>
                                                        </div>
                                                    </div>
                                                @endif

                                                <!-- Reviews -->
                                                <div class="reviews-container">
                                                    @forelse($reviews as $review)
                                                        <div class="review-item border-bottom pb-4 mb-4"
                                                            data-rating="{{ $review->rating }}">
                                                            <div
                                                                class="d-flex justify-content-between align-items-start mb-2">
                                                                <div>
                                                                    <h6 class="mb-1">{{ $review->user_name }}</h6>
                                                                    <div class="star-rating small">
                                                                        @for ($i = 0; $i < 5; $i++)
                                                                            @if ($i < floor($review->rating))
                                                                                <i
                                                                                    class="fa-solid fa-star text-warning"></i>
                                                                            @elseif($i < ceil($review->rating))
                                                                                <i
                                                                                    class="fa-solid fa-star-half-stroke text-warning"></i>
                                                                            @else
                                                                                <i
                                                                                    class="fa-regular fa-star text-warning"></i>
                                                                            @endif
                                                                        @endfor
                                                                    </div>
                                                                </div>
                                                                <small
                                                                    class="text-muted">{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</small>
                                                            </div>
                                                            <p class="mb-2">{{ $review->comment }}</p>

                                                            <!-- Review Helpful -->
                                                            <div class="review-helpful d-flex align-items-center">
                                                                <span class="text-muted small me-2">Was this
                                                                    helpful?</span>
                                                                <button
                                                                    class="btn btn-sm btn-outline-dark me-1 helpful-btn"
                                                                    data-review-id="{{ $review->id }}"
                                                                    data-helpful="1">
                                                                    <i class="fa-light fa-thumbs-up"></i> Yes
                                                                    ({{ $review->helpful_yes ?? 0 }})
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-dark helpful-btn"
                                                                    data-review-id="{{ $review->id }}"
                                                                    data-helpful="0">
                                                                    <i class="fa-light fa-thumbs-down"></i> No
                                                                    ({{ $review->helpful_no ?? 0 }})
                                                                </button>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="text-center py-4">
                                                            <i class="fa-light fa-comments text-muted fa-3x mb-3"></i>
                                                            <p class="text-muted">No reviews yet. Be the first to review
                                                                this product!</p>
                                                        </div>
                                                    @endforelse
                                                </div>

                                                <!-- Load More Reviews -->
                                                @if ($reviews->count() > 5)
                                                    <div class="text-center mt-4">
                                                        <button class="btn btn-outline-dark" id="loadMoreReviews">Load
                                                            More Reviews</button>
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
            <div class="product-details-bottom-inner">
                <!-- Product Details -->
                <div class="product-details-bottom-box d-none">
                    <div class="product-details-bottom-box-heading">
                        <h2>Product Details</h2>
                    </div>
                    <p>{{ $product->description ?? 'No Description Available.' }}</p>
                </div>

                <!-- Product Specification -->
                <div class="product-details-bottom-box pt-0 d-none">
                    <div class="product-details-bottom-box-heading">
                        <h2>Product Specification</h2>
                    </div>
                    <div class="speacification-table">
                        <table>
                            <tbody>
                                <tr>
                                    <td class="Specification-box">Fabric</td>
                                    <td>70% Cotton, 30% Polyester - Long Lasting durable Material</td>
                                </tr>
                                <tr>
                                    <td class="Specification-box">Sleeves</td>
                                    <td>Full Sleeves</td>
                                </tr>
                                <tr>
                                    <td class="Specification-box">Slim Fit</td>
                                    <td>Yes</td>
                                </tr>
                                <tr>
                                    <td class="Specification-box">Stretchable</td>
                                    <td>No</td>
                                </tr>
                                <tr>
                                    <td class="Specification-box">Wash Care</td>
                                    <td>Dry Clean</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- FAQ -->
                <div class="product-details-bottom-box pt-0 d-none">
                    <div class="product-details-bottom-box-heading">
                        <h2>Frequently Asked Questions</h2>
                        <a href="#">Any Query? Ask Us</a>
                    </div>
                    <div class="quary-sec">
                        <ul>
                            <li>
                                <h3><span>Q.</span>Can this clothes be used for gym or gymnastics?</h3>
                                <p><span>A.</span> No</p>
                            </li>
                            <li class="border0">
                                <h3><span>Q.</span>Is this made up of 100% Cotton material?</h3>
                                <p><span>A.</span> No, 70% Cotton, 30% Polyester - Long Lasting durable Material</p>
                            </li>
                        </ul>
                    </div>
                </div>



                <!-- Combo Products Accordion Item -->
                @if (isset($comboProducts) && $comboProducts->count() > 0)
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="headingCombos">
                            <button class="combo-offer-top d-flex align-items-center" type="button"
                                data-bs-toggle="collapse" data-bs-target="#collapseCombos">

                                <div class="icon-box me-3">
                                    <i class="fa-light fa-boxes"></i>
                                </div>

                                <div class="text-start">
                                    <div class="title">Combo Offers</div>
                                    <div class="subtitle">Bundle & Save More</div>
                                </div>

                                <!-- Combo Count Badge -->
                                <span class="badge bg-danger ms-2">{{ $comboProducts->count() }} offers</span>
                            </button>
                        </h2>

                        <div class="" data-bs-parent="#productAccordion">
                            <div class="accordion-body p-0" style="border: 0; box-shadow: none;">
                              <div class="combo-wrapper d-flex gap-3 flex-wrap">
                                    @foreach ($comboProducts as $combo)
                                        @php
                                            // Get combo products from items (all items are combo products, main product is not included)
                                            $otherProducts = $combo->items
                                                ? $combo->items
                                                    ->map(function ($item) {
                                                        return $item->comboProduct;
                                                    })
                                                    ->filter()
                                                : collect();

                                            // Calculate total individual price (main product + combo products)
                                            $totalIndividualPrice = $product->price + $otherProducts->sum('price');

                                            // Calculate discount
                                            $comboPrice = $combo->price;
                                            $regularPrice = $combo->regular_price ?? $totalIndividualPrice;
                                            $discount = 0;
                                            if (
                                                $totalIndividualPrice > 0 &&
                                                $comboPrice > 0 &&
                                                $totalIndividualPrice > $comboPrice
                                            ) {
                                                $discount = round(
                                                    (($totalIndividualPrice - $comboPrice) / $totalIndividualPrice) *
                                                        100,
                                                );
                                            }

                                            // Get combo thumbnail
                                            $comboImg = $combo->thumbnail
                                                ? asset($combo->thumbnail)
                                                : ($product->thumbnail
                                                    ? asset($product->thumbnail)
                                                    : 'https://via.placeholder.com/80x80?text=No+Image');

                                            // Get product names and IDs for modal
                                            $comboProductNames = $otherProducts->pluck('name')->filter()->implode(', ');
                                            $comboProductIds = $otherProducts->pluck('id')->implode(',');
                                        @endphp

                                        <div class="combo-offer-card p-4 mb-3"
                                            style="background: #fff; border-radius: 8px; border: 1px solid #e0e0e0;">
                                            <!-- Header Row -->
                                           
                                            <!-- COMBO PRODUCTS DISPLAY - Horizontal Layout (Only Combo Products) -->
                                            <div class="combo-products-horizontal">
                                                <div class="combo-products-flex-container d-flex align-items-stretch"
                                                    style="gap: 15px; flex-wrap: nowrap; width: 100%;">

                                                    <!-- Combo Products Only -->
                                                    @foreach ($otherProducts as $index => $item)
                                                        @if ($item)
                                                            <div class="combo-product-item text-center"
                                                                style="min-width: 180px; max-width: 200px; flex-shrink: 0; flex-grow: 0; display: flex; flex-direction: column;">
                                                                <div class="combo-product-card p-3 border rounded"
                                                                    style="background: #fff; height: 100%; display: flex; flex-direction: column;">
                                                                    <div class="mb-2">
                                                                        <a
                                                                            href="{{ route('frontend.productdetails', $item->sku ?? $item->id) }}">
                                                                            <img src="{{ $item->thumbnail ? asset($item->thumbnail) : 'https://via.placeholder.com/150x150' }}"
                                                                                class="img-fluid rounded"
                                                                                style="width: 120px; height: 150px; object-fit: cover;"
                                                                                alt="{{ $item->name }}"
                                                                                onerror="this.src='https://via.placeholder.com/150x150'">
                                                                        </a>
                                                                    </div>
                                                                <h6 class="fw-semibold"
                                                                style="font-size:14px; line-height:18px; margin:0 0 4px 0; height:36px; overflow:hidden; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical;">
                                                                {{ Str::limit($item->name, 30) }}
                                                            </h6>
                                                            
                                                            <div class="fw-semibold mb-2 combo-product-price" >
                                                                ₹{{ number_format($item->price, 2) }}
                                                            </div>

                                                                  
                                                                    <button type="button"
                                                                        class="btn btn-dark btn-sm w-100 cart-btn combo-quick-view-btn mt-auto"
                                                                        style="font-size: 12px; padding: 8px;"
                                                                        data-combo-id="{{ $combo->id }}"
                                                                        data-main-product-id="{{ $product->id }}"
                                                                        data-combo-name="{{ $combo->name }}"
                                                                        data-combo-price="{{ $comboPrice }}"
                                                                        data-combo-regular-price="{{ $regularPrice }}"
                                                                        data-combo-thumbnail="{{ $comboImg }}"
                                                                        data-combo-products="{{ $comboProductNames }}"
                                                                        data-combo-product-ids="{{ $comboProductIds }}"
                                                                        @if (Auth::guard('customer')->check()) data-bs-toggle="modal"
                                                    data-bs-target="#comboQuickViewModal"
                                                    @else
                                                    onclick="window.location.href='{{ route('frontend.employeelogin') }}?return_to={{ urlencode(url()->current()) }}'" @endif>
                                                                       
																	   <svg viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="add-to-bag-svg">
																		  <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2988 3.93606C11.2988 1.90046 9.60755 0.231445 7.5405 0.231445C5.47345 0.231445 3.7823 1.90053 3.7823 3.93593V5.46251H0.859289C0.504293 5.46251 0.23291 5.72707 0.23291 6.07314V15.6398C0.23291 17.3293 1.63178 18.693 3.3648 18.693H8.7463C8.52841 18.3161 8.34935 17.9139 8.21458 17.4919L3.36452 17.492C2.3205 17.492 1.48538 16.6778 1.48538 15.6601V6.70401L3.7823 6.70387V7.76225C3.7823 8.10833 4.05368 8.37289 4.40867 8.37289C4.76367 8.37289 5.03505 8.10833 5.03505 7.76225V6.70387H10.0461V7.76225C10.0461 8.10833 10.3175 8.37289 10.6725 8.37289C11.0275 8.37289 11.2988 8.10833 11.2988 7.76225V6.70387H13.5956V9.48248C13.7547 9.47015 13.9155 9.46387 14.0778 9.46387C14.3388 9.46387 14.5959 9.48011 14.8483 9.51163V6.07314C14.8483 5.72707 14.5769 5.46251 14.2219 5.46251L11.2988 5.46265V3.93606ZM7.5405 1.45243C8.91853 1.45243 10.046 2.57202 10.046 3.93572V5.4623H5.03498V3.93572C5.03498 2.57188 6.16246 1.45243 7.5405 1.45243Z" fill="#fff"></path>
																		  <path d="M14.0773 12.5402C13.6997 12.5402 13.3936 12.8462 13.3936 13.2239V14.9334H11.6841C11.3065 14.9334 11.0004 15.2394 11.0004 15.6171C11.0004 15.9947 11.3065 16.3008 11.6841 16.3008H13.3936V18.0103C13.3936 18.3879 13.6997 18.694 14.0773 18.694C14.455 18.694 14.761 18.3879 14.761 18.0103V16.3008H16.4705C16.8482 16.3008 17.1542 15.9947 17.1542 15.6171C17.1542 15.2394 16.8482 14.9334 16.4705 14.9334H14.761V13.2239C14.761 12.8462 14.455 12.5402 14.0773 12.5402Z" fill="#fff"></path>
																		</svg>     
																		<span class="txt">Add to Cart</span>
																	   
																	   
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <!-- Plus Sign between products (except last) -->
                                                            @if (!$loop->last)
                                                                <div class="d-flex align-items-center justify-content-center"
                                                                    style="min-width: 30px; flex-shrink: 0;">
                                                                    <i class="fa-solid fa-plus text-muted"
                                                                        style="font-size: 20px;"></i>
                                                                </div>
                                                            @endif
                                                        @endif
                                                    @endforeach

                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endif




                <!-- Similar Products Section -->
                <div class="product-details-bottom-box pt-0">
                    <div class="product-details-bottom-box-heading d-flex justify-content-between">
                        <h2>Similar Products</h2>
                    </div>
                    <div class="similar-products-sec">
                        @if ($relatedProducts && $relatedProducts->count() > 0)
                            <div class="similarProductListingSlider owl-carousel owl-theme">
                                @foreach ($relatedProducts as $item)
                                    @php
                                        $finalImg = '';
                                        if ($item->images) {
                                            $images = json_decode($item->images, true);
                                            if (
                                                json_last_error() === JSON_ERROR_NONE &&
                                                is_array($images) &&
                                                count($images) > 0
                                            ) {
                                                $finalImg = asset($images[0]);
                                            } elseif (str_contains($item->images, ',')) {
                                                $parts = explode(',', $item->images);
                                                $finalImg = asset(trim($parts[0]));
                                            } else {
                                                $finalImg = asset($item->images);
                                            }
                                        }
                                        $productRoute = $item->sku ?? $item->id;
                                    @endphp
                                    <div class="productListingSliderItem">
                                        <div class="product-list-box">
                                            <div class="product-list-box-inner">
                                                <div class="product-list-box-img">
                                                    <a href="{{ route('frontend.productdetails', $productRoute) }}">
                                                        <img src="{{ $finalImg }}" class="img-fluid"
                                                            alt="{{ $item->name }}"
                                                            onerror="this.onerror=null; this.src=''">
                                                    </a>
                                                    @if (!empty($item->fit_type))
                                                        <div class="fit-badge">{{ strtoupper($item->fit_type) }}</div>
                                                    @endif
                                                </div>
                                                <div class="product-list-box-info">
                                                    <h4 class="proTitle">
                                                        <a href="{{ route('frontend.productdetails', $productRoute) }}">
                                                            {{ strlen($item->name) > 20 ? substr($item->name, 0, 30) . '...' : $item->name }}
                                                        </a>

                                                    </h4>
													<div class="productBoxBtm">
                                                    <div class="product-price-row">
                                                        <span
                                                            class="current-price">₹{{ number_format($item->price, 2) }}</span>
                                                        @if ($item->regular_price && $item->regular_price > $item->price)
                                                            @php
                                                                $discount = round(
                                                                    (($item->regular_price - $item->price) /
                                                                        $item->regular_price) *
                                                                        100,
                                                                );
                                                            @endphp
                                                            <span
                                                                class="old-price">₹{{ number_format($item->regular_price, 2) }}</span>
                                                            <span class="discount">{{ $discount }}% OFF</span>
                                                        @endif
                                                    </div>
                                                    <div class="rupess-btn-sec">
                                                        <div class="leftPart">
														<!--
                                                            @if ($item->moq > 1)
                                                                <div class="offer-badge">
                                                                    <span class="text">MOQ: {{ $item->moq }}</span>
                                                                </div>
                                                            @endif
															-->
                                                        </div>
                                                        <div class="rightPart">
                                                            @if (Auth::guard('customer')->check())
                                                                <form action="{{ route('frontend.addToCart') }}"
                                                                    method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="product_id"
                                                                        value="{{ $item->id }}">
                                                                    <input type="hidden" name="moq" value="1">
                                                                    <input type="hidden" name="price"
                                                                        value="{{ $item->price }}">
                                                                    <button type="submit"
                                                                        class="add-tocart-btn1 cart-btn">Add to
                                                                        Cart</button>
                                                                </form>
                                                            @else
                                                                <a href="{{ route('frontend.employeelogin') }}?return_to={{ urlencode(route('frontend.productdetails', $productRoute)) }}"
                                                                    class="add-tocart-btn1 cart-btn text-decoration-none d-block">
                                                                    Add to Cart
                                                                </a>
                                                            @endif
                                                        </div>
                                                    </div>
													</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-4">
                                <p class="text-muted">No similar products found.</p>
                            </div>
                        @endif
                    </div>
                </div>




                <!-- Write Review Modal -->
                <div class="modal fade" id="writeReviewModal" tabindex="-1" aria-labelledby="writeReviewModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="writeReviewModalLabel">Write a Review</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                @if ($canReview)
                                    <form id="reviewForm" action="{{ route('frontend.reviews.store') }}"
                                        method="POST">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                                        <!-- Rating -->
                                        <div class="mb-4">
                                            <label class="form-label fw-semibold">Overall Rating *</label>
                                            <div class="star-rating-input">
                                                <div class="stars">
                                                    @for ($i = 1; $i <= 5; $i++)
                                                        <input type="radio" id="star{{ $i }}"
                                                            name="rating" value="{{ $i }}" required>
                                                        <label for="star{{ $i }}"><i
                                                                class="fa-solid fa-star"></i></label>
                                                    @endfor
                                                </div>
                                                <div class="rating-error text-danger small mt-1 d-none" id="ratingError">
                                                    Please select a rating</div>
                                            </div>
                                        </div>

                                        <!-- Review Title -->
                                        <div class="mb-3">
                                            <label for="reviewTitle" class="form-label">Review Title *</label>
                                            <input type="text" class="form-control" id="reviewTitle" name="title"
                                                placeholder="Summarize your experience" required>
                                        </div>

                                        <!-- Review Text -->
                                        <div class="mb-3">
                                            <label for="reviewText" class="form-label">Your Review *</label>
                                            <textarea class="form-control" id="reviewText" name="comment" rows="5"
                                                placeholder="Share your experience with this product..." required></textarea>
                                        </div>

                                        <!-- Would Recommend -->
                                        <div class="mb-3">
                                            <label class="form-label">Would you recommend this product?</label>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="recommend"
                                                    id="recommendYes" value="1" checked>
                                                <label class="form-check-label" for="recommendYes">Yes</label>
                                            </div>
                                            <div class="form-check form-check-inline">
                                                <input class="form-check-input" type="radio" name="recommend"
                                                    id="recommendNo" value="0">
                                                <label class="form-check-label" for="recommendNo">No</label>
                                            </div>
                                        </div>

                                        <div class="text-end">
                                            <button type="button" class="btn btn-secondary me-2"
                                                data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-dark" id="submitReviewBtn">
                                                <span class="spinner-border spinner-border-sm d-none" role="status"
                                                    aria-hidden="true"></span>
                                                Submit Review
                                            </button>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-center py-4">
                                        <p class="text-muted">You are not eligible to submit a review for this product.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>




                <!-- Combo Quick View Modal -->
                <div class="modal fade" id="comboQuickViewModal" tabindex="-1"
                    aria-labelledby="comboQuickViewModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="comboQuickViewModalLabel">Combo Offer</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body p-3">
                                <div class="container-fluid">
                                    <div class="row">
                                        <!-- LEFT IMAGE GALLERY -->
                                        <div class="col-md-6">
                                            <div class="product-gallery text-center">
                                                <!-- MAIN IMAGE -->
                                                <div class="main-image mb-3">
                                                    <img id="comboMainImage" src="" class="img-fluid rounded"
                                                        style="max-height: 400px; min-height: 300px; object-fit: contain; background: #f8f9fa;"
                                                        alt="Combo Image"
                                                        onerror="this.onerror=null; this.src=''">
                                                </div>

                                                <!-- THUMBNAILS -->
                                                <div class="thumbnail-row d-flex justify-content-center gap-2 flex-wrap">
                                                    <div class="thumb-box">
                                                        <img id="comboThumb1" src=""
                                                            class="img-fluid rounded border"
                                                            style="width:60px; height:60px; object-fit:cover; cursor: pointer;"
                                                            alt="Combo Thumbnail"
                                                            onerror="this.onerror=null; this.src=''">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- RIGHT PRODUCT DETAILS -->
                                        <div class="col-md-6">
                                            <div class="product-details-right">
                                                <p class="mb-1 productCat text-muted small">Combo Package</p>
                                                <h4 class="product-title mb-2" id="comboName" style="font-size: 1.3rem;">
                                                </h4>
                                                <div id="comboProductsList" class="mb-2"></div>

                                                <!-- Price Section -->
                                                <div class="product-price-row mb-3">
                                                    <div class="d-flex align-items-center flex-wrap gap-2">
                                                        <span class="current-price h5 mb-0 text-primary"
                                                            id="comboCurrentPrice">₹0.00</span>
                                                        <span class="old-price text-muted text-decoration-line-through"
                                                            id="comboOldPrice" style="display: none;"></span>
                                                        <span class="discount badge bg-success" id="comboDiscount"
                                                            style="display: none;"></span>
                                                    </div>
                                                    <small class="text-muted">Inclusive of all taxes</small>
                                                </div>

                                                <!-- Validation Alert -->
                                                <div class="alert alert-warning d-none" id="comboVariationAlert">
                                                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                                    <span id="comboValidationMessage">Please select all required options
                                                        before adding to cart.</span>
                                                </div>

                                                <!-- COMBO ADD TO CART FORM -->
                                                <form action="{{ route('frontend.addToCart') }}" method="POST"
                                                    id="comboAddToCartForm">
                                                    @csrf
                                                    <input type="hidden" name="combo_id" id="comboIdInput"
                                                        value="">
                                                    <input type="hidden" name="moq" id="comboMoqInput"
                                                        value="1">
                                                    <input type="hidden" name="price" id="comboPriceInput"
                                                        value="">
                                                    <input type="hidden" name="product_id" id="comboProductIdInput"
                                                        value="{{ $product->id }}">
                                                    
                                                    <!-- Main product attributes (for main product) -->
                                                    <input type="hidden" name="main_product_attributes" id="comboMainProductAttributesInput" value="">
                                                    <input type="hidden" name="main_product_quantity" id="comboMainProductQuantityInput" value="1">
                                                    <input type="hidden" name="main_product_price" id="comboMainProductPriceInput" value="{{ $product->price }}">

                                                    <!-- We'll add combo_item_color_ids and combo_item_size_ids via JavaScript -->
                                                    <div id="comboItemFieldsContainer">
                                                        <!-- Dynamic fields for each combo item will be added here -->
                                                    </div>

                                                    <!-- ATTRIBUTE SECTION -->
                                                    <style>
                                                    /* Hide color & fabric UI globally */
                                                    .product-attribute-block.colour,
                                                    .product-attribute-block.color,
                                                    .product-attribute-block.fabric,
                                                    .product-attribute-block.febric,
                                                    .details-color-sec,
                                                    #comboColorSection,
                                                    #comboFabricSection {
                                                        display: none !important;
                                                    }
                                                    /* Hide labels and selected text */
                                                    .product-attribute-block.colour .attributeHeadingSec .leftPart p.blackColor,
                                                    .product-attribute-block.color .attributeHeadingSec .leftPart p.blackColor,
                                                    .product-attribute-block.fabric .attributeHeadingSec .leftPart p.blackColor,
                                                    .product-attribute-block.febric .attributeHeadingSec .leftPart p.blackColor,
                                                    .product-attribute-block.colour .selectItem,
                                                    .product-attribute-block.color .selectItem,
                                                    .product-attribute-block.fabric .selectItem,
                                                    .product-attribute-block.febric .selectItem {
                                                        display: none !important;
                                                    }
                                                    </style>
                                                    <div id="comboAttributesContainer">
                                                        <!-- COLOR SECTION -->
                                                        <div id="comboColorSection" class="product-attribute-block mb-3"
                                                            style="display:none;">
                                                            <div
                                                                class="mb-2 d-flex justify-content-between attributeHeadingSec">
                                                                <div class="leftPart">
                                                                    <p class="mb-0 blackColor">Select Color:</p>
                                                                </div>
                                                                <div class="rightPart">
                                                                    <p class="selectItem">Selected Color:
                                                                        <strong id="selectedComboColorLabel">Please
                                                                            select</strong>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="details-color-sec mb-0">
                                                                <div class="color-bottom">
                                                                    <div class="color-left">
                                                                        <div class="color-box" id="comboColorOptions">
                                                                            <!-- Color options will be inserted here -->
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- SIZE SECTION -->
                                                        <div id="comboSizeSection" class="product-attribute-block mb-3"
                                                            style="display:none;">
                                                            <div
                                                                class="mb-2 d-flex justify-content-between attributeHeadingSec">
                                                                <div class="leftPart">
                                                                    <div id="comboSizeErrorMessage" class="text-danger mb-1" style="display:none;">Please select size first!</div>
                                                                    <p class="mb-0 blackColor">Select Size:</p>
                                                                </div>
                                                                <div class="rightPart">
                                                                    <a href="#" class="sizeGuideBtn"
                                                                        data-bs-toggle="modal"
                                                                        data-bs-target="#sizeGuideModal">Size guide</a>
                                                                    <p class="selectItem">Selected Size:
                                                                        <strong id="selectedComboSizeLabel">Please
                                                                            select</strong>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="size-sec mb-2">
                                                                <div class="d-flex flex-wrap gap-2" id="comboSizeOptions">
                                                                    <!-- Size options will be inserted here -->
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- FABRIC SECTION -->
                                                        <div id="comboFabricSection" class="product-attribute-block mb-3"
                                                            style="display:none;">
                                                            <div
                                                                class="mb-2 d-flex justify-content-between attributeHeadingSec">
                                                                <div class="leftPart">
                                                                    <p class="mb-0 blackColor">Select Fabric:</p>
                                                                </div>
                                                                <div class="rightPart">
                                                                    <p class="selectItem">Selected Fabric:
                                                                        <strong id="selectedComboFabricLabel">Please
                                                                            select</strong>
                                                                    </p>
                                                                </div>
                                                            </div>

                                                            <div class="size-sec mb-2">
                                                                <div class="d-flex flex-wrap gap-2"
                                                                    id="comboFabricOptions">
                                                                    <!-- Fabric options will be inserted here -->
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Quantity Section -->
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Quantity:</label>
                                                        <!-- Make sure this is inside the modal form -->
                                                        <div class="product-quantity d-flex align-items-center mb-3"
                                                            style="max-width: 150px;">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary btn-sm sub"
                                                                id="comboDecrementBtn" style="width: 40px;">
                                                                <i class="fa-solid fa-minus"></i>
                                                            </button>
                                                            <input type="text" id="comboQuantityDisplayInput"
                                                                class="form-control text-center count" value="1"
                                                                style="width: 60px;">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary btn-sm add"
                                                                id="comboIncrementBtn" style="width: 40px;">
                                                                <i class="fa-solid fa-plus"></i>
                                                            </button>
                                                        </div>

                                                        <!-- TO THIS: -->
                                                        <div class="partner-message" id="comboPartnerMessage"
                                                            style="display: none; margin-top: 10px;">
                                                            <div class="alert alert-info p-2">
                                                                <i class="fa-solid fa-handshake me-2"></i>
                                                                <strong>You can become a partner!</strong>
                                                                <a href="{{ route('partner.page') }}" class="ms-1"
                                                                    target="_blank">Click here</a>
                                                            </div>
                                                        </div>


                                                        <div class="row g-2">
                                                            <div class="col-12">
                                                                <button type="submit"
                                                                    class="btn btn-primary w-100 add-tocart-btn">
                                                                    <i class="fa-solid fa-cart-plus me-1"></i> Add To Cart
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>

                                                <!-- Delivery Info -->
                                                <div class="delivery-info mb-3">
                                                    <p class="text-muted mb-1">
                                                        <i class="fa-solid fa-truck me-1"></i>
                                                        Delivery within <strong>5 - 7 Days</strong>
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    #comboMainImage {
                        transition: all 0.3s ease;
                    }

                    #comboMainImage.loading {
                        opacity: 0.5;
                    }

                    .thumb-box {
                        border: 2px solid transparent;
                        border-radius: 4px;
                        transition: all 0.3s ease;
                    }

                    .thumb-box:hover {
                        border-color: #007bff;
                    }

                    .combo-products-list::-webkit-scrollbar {
                        width: 5px;
                    }

                    .combo-products-list::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 10px;
                    }

                    .combo-products-list::-webkit-scrollbar-thumb {
                        background: #888;
                        border-radius: 10px;
                    }

                    .combo-products-list::-webkit-scrollbar-thumb:hover {
                        background: #555;
                    }

                    /* Combo Products Horizontal Layout */
                    .combo-products-horizontal {
                        overflow-x: auto;
                        overflow-y: hidden;
                        padding-bottom: 10px;
                        -webkit-overflow-scrolling: touch;
                        width: 100%;
                    }

                    .combo-products-flex-container {
                        display: flex !important;
                        flex-direction: row !important;
                        flex-wrap: nowrap !important;
                        align-items: stretch;
                        width: 100%;
                    }

                    .combo-products-horizontal::-webkit-scrollbar {
                        height: 6px;
                    }

                    .combo-products-horizontal::-webkit-scrollbar-track {
                        background: #f1f1f1;
                        border-radius: 10px;
                    }

                    .combo-products-horizontal::-webkit-scrollbar-thumb {
                        background: #888;
                        border-radius: 10px;
                    }

                    .combo-products-horizontal::-webkit-scrollbar-thumb:hover {
                        background: #555;
                    }

                    .combo-product-item {
                        transition: transform 0.2s ease;
                        display: flex !important;
                        flex-direction: column;
                        flex-shrink: 0;
                        flex-grow: 0;
                    }

                    .combo-product-item:hover {
                        transform: translateY(-5px);
                    }

                    .combo-product-card {
                        transition: box-shadow 0.3s ease;
                        height: 100%;
                        display: flex;
                        flex-direction: column;
                    }

                    .combo-product-card:hover {
                        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                    }

                    @media (max-width: 768px) {
                        .combo-product-item {
                            min-width: 160px !important;
                            max-width: 180px !important;
                        }

                        .combo-product-card img {
                            width: 100px !important;
                            height: 120px !important;
                        }
                    }
                </style>
                <style>
                    /* Disabled Button Styles */
                    .btn.disabled,
                    a.btn.disabled {
                        opacity: 0.6;
                        cursor: not-allowed !important;
                        pointer-events: none;
                    }

                    #combinationNotAvailableAlert {
                        animation: slideDown 0.3s ease;
                    }

                    @keyframes slideDown {
                        from {
                            opacity: 0;
                            transform: translateY(-10px);
                        }

                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }
                </style>
                <style>
                    /* Review Rating Section Styles */
                    .review-rating-section {
                        background: #fff;
                        border-radius: 12px;
                        padding: 0;
                    }

                    .rating-overview {
                        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
                        border: 1px solid #e9ecef;
                    }

                    .overall-rating h1 {
                        font-size: 3.5rem;
                        font-weight: 700;
                    }

                    .star-rating {
                        font-size: 1.2rem;
                    }

                    .rating-bar .progress {
                        background-color: #e9ecef;
                        border-radius: 10px;
                    }

                    .rating-bar .progress-bar {
                        border-radius: 10px;
                    }

                    .review-item {
                        border-bottom: 1px solid #f1f3f4;
                    }

                    .review-item:last-child {
                        border-bottom: none;
                    }

                    /* Star Rating Input */
                    .star-rating-input .stars {
                        display: flex;
                        gap: 8px;
                    }

                    .star-rating-input input[type="radio"] {
                        display: none;
                    }

                    .star-rating-input label {
                        font-size: 2rem;
                        color: #ddd;
                        cursor: pointer;
                        transition: color 0.2s;
                    }

                    .star-rating-input input[type="radio"]:checked~label,
                    .star-rating-input label:hover,
                    .star-rating-input label:hover~label {
                        color: #ffc107;
                    }

                    .star-rating-input input[type="radio"]:checked+label {
                        color: #ffc107;
                    }

                    /* Review Filters */
                    .review-filters .btn {
                        border-radius: 20px;
                        padding: 6px 16px;
                        font-size: 0.875rem;
                    }

                    .review-filters .btn.active {
                        background-color: #007bff;
                        border-color: #007bff;
                        color: white;
                    }

                    /* Responsive Design */
                    @media (max-width: 768px) {
                        .review-rating-section .row {
                            gap: 20px;
                        }

                        .rating-overview {
                            margin-bottom: 20px;
                        }

                        .overall-rating h1 {
                            font-size: 2.5rem;
                        }

                        .star-rating {
                            font-size: 1rem;
                        }
                    }

                    /* Animation for reviews */
                    .review-item {
                        animation: fadeInUp 0.5s ease;
                    }

                    @keyframes fadeInUp {
                        from {
                            opacity: 0;
                            transform: translateY(20px);
                        }

                        to {
                            opacity: 1;
                            transform: translateY(0);
                        }
                    }

                    /* Hover effects */
                    .review-item:hover {
                        background-color: #f8f9fa;
                        border-radius: 8px;
                        padding: 15px;
                        margin: -15px;
                    }
                </style>

                <script>
                    // Star Rating Interaction
                    document.addEventListener('DOMContentLoaded', function() {
                        // Star rating input
                        const stars = document.querySelectorAll('.star-rating-input input[type="radio"]');
                        stars.forEach(star => {
                            star.addEventListener('change', function() {
                                const rating = this.value;
                                console.log('Selected rating:', rating);
                            });
                        });

                        // Review form submission
                        const reviewForm = document.getElementById('reviewForm');
                        if (reviewForm) {
                            reviewForm.addEventListener('submit', function(e) {
                                e.preventDefault();
                                // Add your form submission logic here
                                alert('Review submitted successfully!');
                                $('#writeReviewModal').modal('hide');
                            });
                        }

                        // Review filter buttons
                        const filterButtons = document.querySelectorAll('.review-filters .btn');
                        filterButtons.forEach(btn => {
                            btn.addEventListener('click', function() {
                                filterButtons.forEach(b => b.classList.remove('active'));
                                this.classList.add('active');
                                // Add filter logic here
                            });
                        });
                    });
                </script>

                <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/tinycolor2@1.6.0/dist/tinycolor-min.js"></script>
                <script>
                    $(document).ready(function() {
                        const defaultUnitPrice = parseFloat('{{ $product->price ?? 0 }}') || 0;
                        const variationMatrix = @json($variationMatrix);
                        const cachedVariations = Object.assign({}, variationMatrix || {});
                        const defaultSelections = @json($defaultSelections);
                        const attributeOptions = @json($attributeOptions);
                        const attributeSlugMap = @json($attributeSlugMap);
                        const productId = {{ $product->id }};
                        const variationEndpoint = '{{ route('frontend.product.fetchVariation') }}';

                        const attributeIdToSlug = {};
                        (attributeOptions || []).forEach(function(option) {
                            attributeIdToSlug[option.attribute_id] = option.attribute_slug;
                        });

                        // Function to check if current combination is available
                        function checkCombinationValidity() {
                            const termIds = getSelectedTermIds();
                            const hasAttributeFields = $('.product-attribute-option').length > 0 || $('.combo-attribute-option').length > 0;

                            // If there are no attribute fields, treat as valid (simple product)
                            if (!hasAttributeFields) {
                                return true;
                            }

                            // If attributes exist but none selected, invalid
                            if (termIds.length === 0) {
                                return false;
                            }

                            // Check if combination exists in variationMatrix
                            for (const key in variationMatrix) {
                                const variation = variationMatrix[key];
                                if (variation && variation.attribute_term_ids) {
                                    const variationTermIds = variation.attribute_term_ids.slice().sort((a, b) => a - b);
                                    const requestedTermIds = termIds.slice().sort((a, b) => a - b);
                                    if (JSON.stringify(variationTermIds) === JSON.stringify(requestedTermIds)) {
                                        return true;
                                    }
                                }
                            }

                            // If variation matrix is empty but attributes exist, allow fallback
                            if (Object.keys(variationMatrix || {}).length === 0) {
                                return true;
                            }

                            return false;
                        }

                        const productSku = '{{ $product->sku ?? '' }}';

                        // Function to update combination validity status
                        function updateCombinationValidity() {
                            const isValid = checkCombinationValidity();
                            const $alert = $('#combinationNotAvailableAlert');
                            const $buyNowBtn = $('#buyNowBtn');
                            const $buyNowBtnLink = $('#buyNowBtnLink');
                            const $addToCartBtn = $('#addToCartBtn');
                            const $addToCartBtnLink = $('#addToCartBtnLink');

                            // Ensure Buy Now / Add to Cart are enabled for all products
                            $alert.addClass('d-none').hide();
                            if ($buyNowBtn.length) $buyNowBtn.prop('disabled', false).removeClass('disabled');
                            if ($buyNowBtnLink.length) $buyNowBtnLink.removeClass('disabled').css('pointer-events', 'auto');
                            if ($addToCartBtn.length) $addToCartBtn.prop('disabled', false).removeClass('disabled');
                            if ($addToCartBtnLink.length) $addToCartBtnLink.removeClass('disabled').css('pointer-events', 'auto');
                            return;

                            if (isValid) {
                                // Hide alert
                                $alert.addClass('d-none').hide();

                                // Enable buttons
                                if ($buyNowBtn.length) {
                                    $buyNowBtn.prop('disabled', false).removeClass('disabled');
                                }
                                if ($buyNowBtnLink.length) {
                                    $buyNowBtnLink.removeClass('disabled').css('pointer-events', 'auto');
                                }
                                if ($addToCartBtn.length) {
                                    $addToCartBtn.prop('disabled', false).removeClass('disabled');
                                }
                                if ($addToCartBtnLink.length) {
                                    $addToCartBtnLink.removeClass('disabled').css('pointer-events', 'auto');
                                }
                            } else {
                                // Show alert
                                $alert.removeClass('d-none').show();

                                // Disable buttons
                                if ($buyNowBtn.length) {
                                    $buyNowBtn.prop('disabled', true).addClass('disabled');
                                }
                                if ($buyNowBtnLink.length) {
                                    $buyNowBtnLink.addClass('disabled').css('pointer-events', 'none');
                                }
                                if ($addToCartBtn.length) {
                                    $addToCartBtn.prop('disabled', true).addClass('disabled');
                                }
                                if ($addToCartBtnLink.length) {
                                    $addToCartBtnLink.addClass('disabled').css('pointer-events', 'none');
                                }
                            }
                        }

                        // 1. INITIALIZE QUANTITY WITH 1
                        let qty = 1;
                        $('#quantityDisplay').val(qty); // Set input to 1
                        const minQtyValue = 1; // Minimum is always 1

                        let activeVariation = null;

                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        function getUnitPrice() {
                            if (activeVariation) {
                                const candidate = parseFloat(activeVariation.price ?? activeVariation.regular_price);
                                if (!isNaN(candidate) && candidate > 0) {
                                    return candidate;
                                }
                            }
                            return defaultUnitPrice;
                        }

                       function refreshPriceDisplay() {
                            const unitPrice = getUnitPrice();
                            const totalPrice = unitPrice * qty; // This is the total for all items
                        
                            // Get regular price from active variation or default
                            let regularPrice = defaultUnitPrice;
                            if (activeVariation && activeVariation.regular_price) {
                                regularPrice = parseFloat(activeVariation.regular_price);
                            } else {
                                // Get regular price from either price section
                                const regularPriceData = $('#oldPriceDisplay').data('regular') || $('.secondary-old-price').data('regular') || defaultUnitPrice;
                                regularPrice = parseFloat(regularPriceData);
                            }
                        
                            // Update visible current price - Show TOTAL PRICE (unitPrice * qty)
                            $('.current-price, #unitPriceDisplay').each(function() {
                                $(this).text('₹' + totalPrice.toFixed(2)); // Changed from unitPrice to totalPrice
                            });
                        
                            // Update hidden input with UNIT price (for form submission)
                            $('#priceInput').val(unitPrice);
                        
                            // Update quantity label (if exists)
                            if ($('#quantityLabel').length) {
                                $('#quantityLabel').text(qty);
                            }
                        
                            // Update regular price display - BOTH SECTIONS
                            if (regularPrice && regularPrice > unitPrice) {
                                const totalRegularPrice = regularPrice * qty; // Calculate total regular price
                                
                                // Update first section (in form)
                                $('#oldPriceDisplay')
                                    .removeClass('d-none')
                                    .text('₹' + totalRegularPrice.toFixed(2)) // Show total regular price
                                    .data('regular', regularPrice); // Still store unit regular price in data
                                
                                // Update second section (outside form)
                                $('.secondary-old-price')
                                    .removeClass('d-none')
                                    .text('₹' + totalRegularPrice.toFixed(2)); // Show total regular price
                        
                                // Calculate and show discount (based on unit prices)
                                const discount = Math.round(((regularPrice - unitPrice) / regularPrice) * 100);
                                
                                // Update first discount (in form)
                                $('#discountDisplay')
                                    .removeClass('d-none')
                                    .text(discount + '% OFF');
                                    
                                // Update second discount (outside form)
                                $('.secondary-discount')
                                    .removeClass('d-none')
                                    .text(discount + '% OFF');
                            } else {
                                // Hide both sections
                                $('#oldPriceDisplay').addClass('d-none');
                                $('#discountDisplay').addClass('d-none');
                                $('.secondary-old-price').addClass('d-none');
                                $('.secondary-discount').addClass('d-none');
                            }
                        
                            // Update discount/old-price (legacy function call)
                            if (typeof updateDynamicPriceSection === 'function') {
                                updateDynamicPriceSection(unitPrice);
                            }
                        
                            // 2. PARTNER MESSAGE LOGIC - Show when quantity > 50
                            if (qty > 50) {
                                $('#partnerMessage').slideDown(300);
                            } else {
                                $('#partnerMessage').slideUp(300);
                            }
                        }

                       function updateDynamicPriceSection(unitPrice) {
    let regularPrice = parseFloat($('#oldPriceDisplay').data('regular')) || 
                       parseFloat($('.secondary-old-price').data('regular'));

    if (isNaN(regularPrice) || regularPrice <= unitPrice) {
        $('#oldPriceDisplay').addClass('d-none');
        $('#discountDisplay').addClass('d-none');
        $('.secondary-old-price').addClass('d-none');
        $('.secondary-discount').addClass('d-none');
        return;
    }

    const totalRegularPrice = regularPrice * qty; // Calculate total regular price
    
    // Update first section
    $('#oldPriceDisplay')
        .removeClass('d-none')
        .text('₹' + totalRegularPrice.toFixed(2)); // Show total regular price

    // Update second section
    $('.secondary-old-price')
        .removeClass('d-none')
        .text('₹' + totalRegularPrice.toFixed(2)); // Show total regular price

    let discount = Math.round(((regularPrice - unitPrice) / regularPrice) * 100);

    // Update first discount
    $('#discountDisplay')
        .removeClass('d-none')
        .text(discount + '% OFF');
        
    // Update second discount
    $('.secondary-discount')
        .removeClass('d-none')
        .text(discount + '% OFF');
}

                        // 3. QUANTITY INCREMENT/DECREMENT HANDLERS
                        $('#incrementBtn').on('click', function() {
                            qty += 1;
                            $('#quantityDisplay').val(qty);
                            refreshPriceDisplay();
                        });

                        $('#decrementBtn').on('click', function(event) {
                            event.preventDefault();
                            if (qty > 1) { // Can go down to 1
                                qty -= 1;
                                $('#quantityDisplay').val(qty);
                                refreshPriceDisplay();
                            }
                        });

                        // 4. MANUAL INPUT HANDLING
                        $('#quantityDisplay').on('input change blur', function() {
                            let inputQty = parseInt($(this).val(), 10);

                            // If input is empty or invalid, set to 1
                            if (isNaN(inputQty) || inputQty < 1) {
                                inputQty = 1;
                                $(this).val(1);
                            }

                            qty = inputQty;
                            refreshPriceDisplay();
                        });

                        // ... rest of your existing JavaScript code (color swatches, variation fetching, etc.) ...

                        function initializeColorSwatches() {
                            $('.product-attribute-option').each(function() {
                                const attributeSlug = $(this).data('attribute-slug');
                                if (attributeSlug === 'colour' || attributeSlug === 'color') {
                                    const colorValue = $(this).data('color');
                                    const color = tinycolor(colorValue);
                                    const appliedColor = color.isValid() ? color.toHexString() : '#cccccc';
                                    $(this).siblings('.color-checkmark').css('background-color', appliedColor);
                                    if ($(this).is(':checked')) {
                                        $(this).closest('.color-container').addClass('active');
                                    }
                                }
                            });
                        }

                        function setDefaultSelections() {
                            Object.entries(defaultSelections || {}).forEach(function([attributeId, term]) {
                                if (!term) {
                                    return;
                                }
                                const slug = attributeIdToSlug[attributeId] || '';
                                updateHiddenInputs(attributeId, term.term_id, slug, term.term_name || '');
                            });
                        }

                        function updateHiddenInputs(attributeId, termId, attributeSlug, termName) {
                            const labelEl = $('#selectedAttributeLabel_' + attributeId);
                            if (labelEl.length) {
                                labelEl.text(termName);
                            }

                            $('#attributeHidden_' + attributeId).val(termId);

                            if (attributeSlug === 'colour' || attributeSlug === 'color') {
                                $('#selectedColor').val(termId);
                                $('#selectedColorName').text(termName);

                                $('.product-attribute-option[data-attribute-id="' + attributeId + '"]').each(function() {
                                    $(this).closest('.color-container').removeClass('active');
                                });
                                $('.product-attribute-option[data-attribute-id="' + attributeId + '"][value="' + termId + '"]')
                                    .closest('.color-container')
                                    .addClass('active');
                            }

                            if (attributeSlug === 'size') {
                                $('#selectedSize').val(termId);
                            }
                        }

                        function getSelectedTermIds() {
                            const ids = [];
                            $('.product-attribute-option:checked').each(function() {
                                const termId = parseInt($(this).val(), 10);
                                if (!isNaN(termId) && termId > 0) {
                                    ids.push(termId);
                                }
                            });
                            return ids.sort((a, b) => a - b);
                        }

                        // Variation alert functions
                        function showVariationAlert() {
                            $('#variationNotFoundAlert').removeClass('d-none').slideDown(300);
                        }

                        function hideVariationAlert() {
                            $('#variationNotFoundAlert').addClass('d-none').slideUp(300);
                        }

                        // Ensure a size has been chosen when size options exist
                        function validateSizeSelection(showError = false) {
                            let hasSizeField = false;
                            let hasSelection = false;

                            // Check for size quantity inputs (selectSizeList with qtyInput)
                            const $sizeQuantityInputs = $('.selectSizeList .qtyInput');
                            if ($sizeQuantityInputs.length > 0) {
                                hasSizeField = true;
                                // Check if any quantity input has a value > 0
                                let hasQuantity = false;
                                $sizeQuantityInputs.each(function() {
                                    const value = parseInt($(this).val(), 10) || 0;
                                    if (value > 0) {
                                        hasQuantity = true;
                                        return false; // break loop
                                    }
                                });
                                
                                if (hasQuantity) {
                                    hasSelection = true;
                                    $('#sizeErrorMessage').hide();
                                } else if (showError) {
                                    $('#sizeErrorMessage').show();
                                } else {
                                    $('#sizeErrorMessage').hide();
                                }
                            }

                            // Main product size radios (fallback for other size selection methods)
                            const $sizeOptions = $('.product-attribute-option[data-attribute-slug="size"]');
                            if ($sizeOptions.length && !hasSizeField) {
                                hasSizeField = true;
                                if ($sizeOptions.is(':checked')) {
                                    hasSelection = true;
                                    $('#sizeErrorMessage').hide();
                                } else if (showError) {
                                    $('#sizeErrorMessage').show();
                                } else {
                                    $('#sizeErrorMessage').hide();
                                }
                            }

                            // Combo size radios
                            const $comboSizeOptions = $('#comboSizeSection:visible .combo-attribute-option[data-attribute-type="size"]');
                            if ($comboSizeOptions.length) {
                                hasSizeField = true;
                                if ($comboSizeOptions.is(':checked')) {
                                    hasSelection = true;
                                    $('#comboSizeErrorMessage').hide();
                                } else if (showError) {
                                    $('#comboSizeErrorMessage').show();
                                } else {
                                    $('#comboSizeErrorMessage').hide();
                                }
                            } else {
                                $('#comboSizeErrorMessage').hide();
                            }

                            // If there is no size field at all, treat as valid
                            if (!hasSizeField) return true;

                            return hasSelection;
                        }

                        // Prevent form submission if combination is invalid
                        $('.add-to-cart-form').on('submit', function(e) {
                            // Require size selection when size options exist
                            if (!validateSizeSelection(true)) {
                                e.preventDefault();
                                e.stopPropagation();
                                return false;
                            }

                            // Check if using size quantity inputs
                            const hasSizeQuantityInputs = $('.selectSizeList .qtyInput').length > 0;
                            let hasSizeQuantities = false;
                            if (hasSizeQuantityInputs) {
                                $('.selectSizeList .qtyInput').each(function() {
                                    const qty = parseInt($(this).val(), 10) || 0;
                                    if (qty > 0) {
                                        hasSizeQuantities = true;
                                        return false; // break
                                    }
                                });
                            }

                            // Only check combination validity if not using size quantity inputs
                            if (!hasSizeQuantityInputs || !hasSizeQuantities) {
                                if (!checkCombinationValidity()) {
                                    e.preventDefault();
                                    e.stopPropagation();
                                    $('#combinationNotAvailableAlert').removeClass('d-none').show();
                                    return false;
                                }
                            }
                            
                            // Collect size quantities before form submission
                            const sizeQuantities = {};
                            $('.selectSizeList .qtyInput').each(function() {
                                const sizeId = $(this).data('size-id');
                                const quantity = parseInt($(this).val(), 10) || 0;
                                if (sizeId && quantity > 0) {
                                    sizeQuantities[sizeId] = quantity;
                                }
                            });
                            
                            // Add size_quantities as hidden inputs or update existing ones
                            $('input[name^="size_quantities"]').remove();
                            if (Object.keys(sizeQuantities).length > 0) {
                                $.each(sizeQuantities, function(sizeId, qty) {
                                    $('<input>').attr({
                                        type: 'hidden',
                                        name: 'size_quantities[' + sizeId + ']',
                                        value: qty
                                    }).appendTo($('.add-to-cart-form'));
                                });
                                
                                // Update moq with total quantity
                                let totalQty = 0;
                                $.each(sizeQuantities, function(sizeId, qty) {
                                    totalQty += qty;
                                });
                                $('#moqInput').val(totalQty);
                            }
                            
                            // Ensure embroidery checkbox value is included in form submission
                            const embroideryChecked = $('#interestedEmbroideryCheckbox').is(':checked');
                            $('#interestedInEmbroideryHidden').val(embroideryChecked ? '1' : '0');
                            
                            // If combination is valid, allow form to submit normally
                        });

                        // Unified handler for Buy Now and Add to Cart (Buttons and Links)
                        $(document).on('click', '#buyNowBtn, #buyNowBtnLink, #addToCartBtn, #addToCartBtnLink', function(e) {
                            e.preventDefault();
                            const $btn = $(this);
                            const targetUrl = $btn.data('target-url') || $btn.attr('href');
                            const isLink = $btn.is('a');
                            const isBuyNow = $btn.attr('id') === 'buyNowBtn' || $btn.attr('id') === 'buyNowBtnLink';
                            
                            // Check if button is disabled
                            if ($btn.hasClass('disabled') || $btn.prop('disabled')) {
                                return false;
                            }

                            const $form = $('.add-to-cart-form');

                            // Validation (Size)
                            if (!validateSizeSelection(true)) {
                                return false;
                            }

                            // Validation (Combination)
                            const hasSizeQuantityInputs = $('.selectSizeList .qtyInput').length > 0;
                            let hasSizeQuantities = false;
                            if (hasSizeQuantityInputs) {
                                $('.selectSizeList .qtyInput').each(function() {
                                    const qty = parseInt($(this).val(), 10) || 0;
                                    if (qty > 0) {
                                        hasSizeQuantities = true;
                                        return false; // break
                                    }
                                });
                            }

                            if (!hasSizeQuantityInputs || !hasSizeQuantities) {
                                if (!checkCombinationValidity()) {
                                    $('#combinationNotAvailableAlert').removeClass('d-none').show();
                                    return false;
                                }
                            }

                            // If it's a link (not logged in), redirect after validation
                            if (isLink) {
                                if (targetUrl) {
                                    window.location.href = targetUrl;
                                }
                                return false;
                            }

                            // It's a button (logged in), proceed with AJAX submission
                            const formData = new FormData($form[0]);
                            
                            // Add size quantities manually
                            let totalQty = 0;
                            $('.selectSizeList .qtyInput').each(function() {
                                const sizeId = $(this).data('size-id');
                                const qty = parseInt($(this).val(), 10) || 0;
                                if (sizeId && qty > 0) {
                                    formData.append('size_quantities[' + sizeId + ']', qty);
                                    totalQty += qty;
                                }
                            });
                            
                            // Update MOQ input if needed
                            if (totalQty > 0) {
                                formData.set('moq', totalQty);
                            }
                            
                            // Embroidery checkbox
                            const embroideryChecked = $('#interestedEmbroideryCheckbox').is(':checked');
                            formData.set('interested_in_embroidery', embroideryChecked ? '1' : '0');
                            
                            // Main Product Attributes
                            if (typeof buildMainProductAttributes === 'function') {
                                const mainProductAttributes = buildMainProductAttributes();
                                if (mainProductAttributes && mainProductAttributes.length > 0) {
                                    formData.append('main_product_attributes', JSON.stringify(mainProductAttributes));
                                }
                            }
                            
                            // Buy Now specific
                            if (isBuyNow) {
                                formData.append('buy_now', '1');
                            }
                            
                            // Disable button
                            $btn.prop('disabled', true).addClass('disabled');
                            
                            $.ajax({
                                url: $form.attr('action'),
                                type: 'POST',
                                data: formData,
                                processData: false,
                                contentType: false,
                                success: function(response) {
                                    if (response.success) {
                                        if (response.redirect) {
                                            window.location.href = response.redirect;
                                        } else {
                                            toastr.success(response.message);
                                            // Prefer product count (distinct items). Accept multiple response shapes:
                                            // - response.cart_items_count (number)
                                            // - response.cart_items (array)
                                            // - response.cart.items (array)
                                            // Fallback to response.cart_count only if nothing else available.
                                            let productCount;
                                            if (response.cart_items_count !== undefined) {
                                                productCount = parseInt(response.cart_items_count, 10);
                                            } else if (response.cart_items && Array.isArray(response.cart_items)) {
                                                productCount = response.cart_items.length;
                                            } else if (response.cart && response.cart.items && Array.isArray(response.cart.items)) {
                                                productCount = response.cart.items.length;
                                            } else if (response.cart_count !== undefined) {
                                                // last-resort fallback (may be total qty); use only if no better info
                                                productCount = parseInt(response.cart_count, 10);
                                            }

                                            if (productCount !== undefined && !isNaN(productCount)) {
                                                $('.cart-count').text(productCount);
                                                $('.mobile-cart-count').text(productCount);

                                                const $headerBadge = $('.cart-count-badge');
                                                if ($headerBadge.length) {
                                                    $headerBadge.text(productCount);
                                                } else if (productCount > 0) {
                                                    $('.my-cart').append('<span class="cart-count-badge">' + productCount + '</span>');
                                                } else {
                                                    $('.cart-count-badge').remove();
                                                }
                                            }

                                            // Update cart total if returned
                                            if (response.cart_total !== undefined) {
                                                let totalText = response.cart_total;
                                                if (!isNaN(parseFloat(totalText))) {
                                                    totalText = '₹' + parseFloat(totalText).toFixed(2);
                                                }
                                                if ($('.cart-total-amount').length) {
                                                    $('.cart-total-amount').text(totalText);
                                                } else {
                                                    $('.my-cart').append('<span class="cart-total-amount ms-1">' + totalText + '</span>');
                                                }
                                            }
                                            // Reset loading state
                                            $btn.prop('disabled', false).removeClass('disabled');
                                        }
                                    } else {
                                        toastr.error(response.message || 'Error adding to cart');
                                        $btn.prop('disabled', false).removeClass('disabled');
                                    }
                                },
                                error: function(xhr) {
                                    console.error(xhr);
                                    let msg = 'Error adding to cart';
                                    if (xhr.responseJSON && xhr.responseJSON.message) {
                                        msg = xhr.responseJSON.message;
                                    }
                                    toastr.error(msg);
                                    $btn.prop('disabled', false).removeClass('disabled');
                                }
                            });
                            
                            return false;
                        });

                        $(document).on('change', '.product-attribute-option[data-attribute-slug="size"], #comboSizeSection .combo-attribute-option[data-attribute-type="size"]', function() {
                            validateSizeSelection(false);
                        });

                        // Hide error message when user types in quantity inputs
                        $(document).on('input', '.selectSizeList .qtyInput', function() {
                            const value = parseInt($(this).val(), 10) || 0;
                            if (value > 0) {
                                $('#sizeErrorMessage').hide();
                            }
                        });

                        //variation alert message
                        function fetchVariation(termIds) {
                            // First check if we have this combination in the cached variationMatrix
                            const legacyKey = termIds.join('_');
                            let combinationKey = null;
                            let variationData = null;

                            // Try to find in variationMatrix by combination_key or legacy key
                            for (const key in variationMatrix) {
                                const variation = variationMatrix[key];
                                if (variation && variation.attribute_term_ids) {
                                    const variationTermIds = variation.attribute_term_ids.slice().sort((a, b) => a - b);
                                    const requestedTermIds = termIds.slice().sort((a, b) => a - b);

                                    if (JSON.stringify(variationTermIds) === JSON.stringify(requestedTermIds)) {
                                        variationData = variation;
                                        combinationKey = variation.combination_key || key;
                                        break;
                                    }
                                }
                            }

                            // If found in matrix, use it directly
                            if (variationData) {
                                const cacheKey = combinationKey || legacyKey;
                                const response = {
                                    success: true,
                                    variation: variationData,
                                    data: variationData,
                                    status: 'success'
                                };
                                cachedVariations[cacheKey] = response;
                                handleVariationResponse(response, termIds);
                                return;
                            }

                            // Check cache first
                            if (cachedVariations[legacyKey]) {
                                handleVariationResponse(cachedVariations[legacyKey], termIds);
                                return;
                            }

                            // Show loading or disable buttons if needed
                            hideVariationAlert();

                            // Build attributes map for better matching
                            const attributes = {};
                            $('.product-attribute-option:checked').each(function() {
                                const attrId = $(this).data('attribute-id');
                                const termId = parseInt($(this).val(), 10);
                                if (attrId && termId) {
                                    attributes[attrId] = termId;
                                }
                            });

                            $.ajax({
                                url: variationEndpoint,
                                method: 'POST',
                                data: {
                                    product_id: productId,
                                    term_ids: termIds,
                                    attributes: attributes,
                                    combination_key: combinationKey
                                },
                                success: function(response) {
                                    const cacheKey = (response.data && response.data.combination_key) || legacyKey;
                                    cachedVariations[cacheKey] = response;
                                    handleVariationResponse(response, termIds);
                                },
                                error: function(xhr, status, error) {
                                    console.error('Variation fetch error:', error);
                                    // Show error or default to main product
                                    activeVariation = null;
                                    refreshPriceDisplay();
                                    hideVariationAlert();
                                }
                            });
                        }

                        function handleVariationResponse(response, termIds) {
                            // Handle both response formats: {success, variation} and {status, data}
                            let variation = null;

                            if (response.success && response.variation) {
                                variation = response.variation;
                            } else if (response.status === 'success' && response.data) {
                                variation = response.data;
                            } else if (response.success && response.data) {
                                variation = response.data;
                            }

                            if (variation) {
                                activeVariation = variation;
                                hideVariationAlert();

                                // Update price if variation has different price
                                if (variation.price) {
                                    $('#priceInput').val(variation.price);
                                }

                                // Update regular price display if available
                                if (variation.regular_price) {
                                    $('#oldPriceDisplay').data('regular', variation.regular_price);
                                }

                                // Update stock status if needed
                                if (typeof updateStockStatus === 'function') {
                                    updateStockStatus(variation);
                                }
                                
                                // Update main product fields (including combo modal price)
                                if (typeof updateMainProductFields === 'function') {
                                    updateMainProductFields();
                                }
                            } else {
                                // Variation not found for this combination
                                activeVariation = null;
                                showVariationAlert();
                            }

                            refreshPriceDisplay();
                        }

                        $('.product-attribute-option').on('change', function() {
                            const attributeId = $(this).data('attribute-id');
                            const attributeSlug = $(this).data('attribute-slug');
                            const termName = $(this).data('term-name');
                            const termId = parseInt($(this).val(), 10);

                            updateHiddenInputs(attributeId, termId, attributeSlug, termName);

                            const termIds = getSelectedTermIds();

                            // Check combination validity and update UI
                            updateCombinationValidity();

                            // Update main product attributes and sync to combo modal
                            if (typeof buildMainProductAttributes === 'function') {
                                const mainProductAttributes = buildMainProductAttributes();
                                // Sync attributes to combo modal if it exists
                                if ($('#comboMainProductAttributesInput').length) {
                                    $('#comboMainProductAttributesInput').val(JSON.stringify(mainProductAttributes));
                                }
                            }

                            // Only fetch variation if combination is valid
                            if (checkCombinationValidity()) {
                                fetchVariation(termIds);
                            } else {
                                // Clear active variation if invalid
                                activeVariation = null;
                                refreshPriceDisplay();
                                hideVariationAlert();
                                
                                // Still update main product fields to sync price (even if invalid, use base price)
                                if (typeof updateMainProductFields === 'function') {
                                    setTimeout(function() {
                                        updateMainProductFields();
                                    }, 100);
                                }
                            }
                        });
                        // 5. INITIALIZE EVERYTHING
                        initializeColorSwatches();
                        setDefaultSelections();
                        refreshPriceDisplay(); // This will show/hide partner message based on initial qty (1)

                        // Check initial combination validity
                        updateCombinationValidity();

                        // Fetch variation if valid combination exists
                        if (checkCombinationValidity()) {
                            fetchVariation(getSelectedTermIds());
                        }
                    });
                </script>
                <script>
                    // Define buildMainProductAttributes globally so it's accessible from all script blocks
                    function buildMainProductAttributes() {
                        const attributes = [];
                        $('.product-attribute-option:checked').each(function() {
                            const attributeId = $(this).data('attribute-id');
                            const attributeSlug = $(this).data('attribute-slug');
                            const termId = parseInt($(this).val(), 10);
                            const termName = $(this).data('term-name');
                            
                            if (attributeId && termId && termName) {
                                attributes.push({
                                    attribute_id: attributeId,
                                    attribute_slug: attributeSlug || '',
                                    term_id: termId,
                                    term_name: termName
                                });
                            }
                        });
                        
                        // Update hidden input
                        $('#mainProductAttributesInput').val(JSON.stringify(attributes));
                        
                        return attributes;
                    }
                    
                    // Define updateMainProductFields globally so it's accessible from all script blocks
                    function updateMainProductFields() {
                        const quantity = parseInt($('#quantityDisplay').val()) || 1;
                        const price = parseFloat($('#priceInput').val()) || parseFloat('{{ $product->price }}');
                        
                        // Update moq input as well
                        $('#moqInput').val(quantity);
                        
                        // Update hidden fields in main form
                        $('#mainProductQuantityInput').val(quantity);
                        $('#mainProductPriceInput').val(price);
                        
                        // Also update combo modal's main product price input (if modal is open or exists)
                        if ($('#comboMainProductPriceInput').length) {
                            $('#comboMainProductPriceInput').val(price);
                        }
                    }
                    
                    $(document).ready(function() {
                        
                        // Update attributes when attribute options change (additional handler for combo modal sync)
                        $(document).on('change', '.product-attribute-option', function() {
                            // Build and update main product attributes
                            const mainProductAttributes = buildMainProductAttributes();
                            
                            // Sync attributes to combo modal if it exists
                            if ($('#comboMainProductAttributesInput').length) {
                                $('#comboMainProductAttributesInput').val(JSON.stringify(mainProductAttributes));
                            }
                            
                            // Update main product fields (which will sync price to combo modal)
                            // Use setTimeout to ensure priceInput is updated first
                            setTimeout(function() {
                                if (typeof updateMainProductFields === 'function') {
                                    updateMainProductFields();
                                }
                            }, 150);
                        });
                        
                        // Update quantity and price when quantity changes
                        $(document).on('click', '#incrementBtn, #decrementBtn', function() {
                            setTimeout(function() {
                                updateMainProductFields();
                            }, 50);
                        });
                        
                        $(document).on('input change blur', '#quantityDisplay', function() {
                            setTimeout(function() {
                                updateMainProductFields();
                            }, 50);
                        });
                        
                        // Initialize on page load
                        setTimeout(function() {
                            buildMainProductAttributes();
                            updateMainProductFields();
                        }, 100);
                        
                        // Handle Add to Cart button click
                        $('.add-to-cart-form').submit(function(e) {
                            // Build attributes before submission
                            buildMainProductAttributes();
                            updateMainProductFields();
                            
                            // Check if combination is valid
                            const termIds = [];
                            $('.product-attribute-option:checked').each(function() {
                                const termId = parseInt($(this).val(), 10);
                                if (!isNaN(termId) && termId > 0) {
                                    termIds.push(termId);
                                }
                            });

                            // Check combination validity
                            let isValid = false;
                            const variationMatrix = @json($variationMatrix);
                            if (termIds.length > 0) {
                                for (const key in variationMatrix) {
                                    const variation = variationMatrix[key];
                                    if (variation && variation.attribute_term_ids) {
                                        const variationTermIds = variation.attribute_term_ids.slice().sort((a, b) => a -
                                            b);
                                        const requestedTermIds = termIds.slice().sort((a, b) => a - b);
                                        if (JSON.stringify(variationTermIds) === JSON.stringify(requestedTermIds)) {
                                            isValid = true;
                                            break;
                                        }
                                    }
                                }
                            }

                            if (!isValid) {
                                e.preventDefault();
                                e.stopPropagation();
                                $('#combinationNotAvailableAlert').removeClass('d-none').show();
                                return false;
                            }

                            // If the submit button exists, user is logged in - allow form submission
                            // If only the link exists, the form shouldn't be submitted anyway
                            // No need for additional auth check since blade template handles button visibility
                        });
                    });

                    document.addEventListener('DOMContentLoaded', function() {
                        const reviewForm = document.getElementById('reviewForm');
                        const submitBtn = document.getElementById('submitReviewBtn');
                        const ratingInputs = document.querySelectorAll('input[name="rating"]');
                        const ratingError = document.getElementById('ratingError');

                        // Star rating interaction
                        ratingInputs.forEach(input => {
                            input.addEventListener('change', function() {
                                updateStarDisplay(this.value);
                                ratingError.classList.add('d-none');
                            });
                        });

                        function updateStarDisplay(rating) {
                            const labels = document.querySelectorAll('.star-rating-input label');
                            labels.forEach((label, index) => {
                                const icon = label.querySelector('i');
                                if (index < rating) {
                                    icon.className = 'fa-solid fa-star text-warning';
                                } else {
                                    icon.className = 'fa-regular fa-star';
                                }
                            });
                        }

                        // Form submission
                        reviewForm.addEventListener('submit', function(e) {
                            e.preventDefault();

                            // Validate rating
                            const ratingSelected = document.querySelector('input[name="rating"]:checked');
                            if (!ratingSelected) {
                                ratingError.classList.remove('d-none');
                                return;
                            }

                            // Show loading state
                            submitBtn.disabled = true;
                            submitBtn.querySelector('.spinner-border').classList.remove('d-none');

                            // Submit form via AJAX
                            fetch(this.action, {
                                    method: 'POST',
                                    headers: {
                                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                                        'Accept': 'application/json',
                                        'Content-Type': 'application/json'
                                    },
                                    body: JSON.stringify({
                                        product_id: document.querySelector('input[name="product_id"]')
                                            .value,
                                        rating: ratingSelected.value,
                                        title: document.getElementById('reviewTitle').value,
                                        comment: document.getElementById('reviewText').value,
                                        recommend: document.querySelector('input[name="recommend"]:checked')
                                            .value
                                    })
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        // Show success message
                                        alert('Review submitted successfully!');
                                        $('#writeReviewModal').modal('hide');
                                        reviewForm.reset();
                                        updateStarDisplay(0);

                                        // Reload page or update reviews section
                                        location.reload();
                                    } else {
                                        alert(data.message || 'Error submitting review.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Error submitting review. Please try again.');
                                })
                                .finally(() => {
                                    submitBtn.disabled = false;
                                    submitBtn.querySelector('.spinner-border').classList.add('d-none');
                                });
                        });

                        // Reset form when modal is closed
                        $('#writeReviewModal').on('hidden.bs.modal', function() {
                            reviewForm.reset();
                            updateStarDisplay(0);
                            ratingError.classList.add('d-none');
                        });
                    });

                    document.addEventListener('DOMContentLoaded', function() {
                        // Review filtering
                        const filterButtons = document.querySelectorAll('.review-filters .btn');
                        const reviewItems = document.querySelectorAll('.review-item');

                        filterButtons.forEach(button => {
                            button.addEventListener('click', function() {
                                const filter = this.dataset.filter;

                                // Update active button
                                filterButtons.forEach(btn => btn.classList.remove('active'));
                                this.classList.add('active');

                                // Filter reviews
                                reviewItems.forEach(item => {
                                    if (filter === 'all' || item.dataset.rating === filter) {
                                        item.style.display = 'block';
                                    } else {
                                        item.style.display = 'none';
                                    }
                                });
                            });
                        });

                        // Helpful buttons
                        document.querySelectorAll('.helpful-btn').forEach(button => {
                            button.addEventListener('click', function() {
                                const reviewId = this.dataset.reviewId;
                                const isHelpful = this.dataset.helpful === '1';

                                // You can implement AJAX call here to update helpful counts
                                console.log(`Review ${reviewId} - Helpful: ${isHelpful}`);

                                // For now, just show a message
                                alert('Thank you for your feedback!');
                            });
                        });

                        // Load more reviews
                        const loadMoreBtn = document.getElementById('loadMoreReviews');
                        if (loadMoreBtn) {
                            loadMoreBtn.addEventListener('click', function() {
                                // Implement AJAX call to load more reviews
                                alert('Loading more reviews...');
                            });
                        }
                    });
                </script>


                <!-- Combo Quick View Modal -->

                <style>
                    /* Combo Modal Fabric Fix */
                    #comboQuickViewModal .generic-attribute-options {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 8px;
                    }

                    #comboQuickViewModal .size-container {
                        margin: 0;
                        cursor: pointer;
                        flex-shrink: 0;
                    }

                    #comboQuickViewModal .size-checkmark {
                        display: inline-block;
                        padding: 10px 16px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        transition: all 0.3s ease;
                        min-width: auto !important;
                        width: auto !important;
                        text-align: center;
                        background-color: white;
                        white-space: nowrap;
                        /* Prevent text wrapping inside box */
                        min-height: 42px;
                        line-height: 1.2;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    }

                    /* Specific fix for fabric options */
                    #comboFabricOptions .size-container {
                        flex: 0 0 auto;
                        margin-bottom: 8px;
                    }

                    #comboFabricOptions .size-checkmark {
                        padding: 10px 14px;
                        font-size: 13px;
                        min-width: auto;
                        white-space: nowrap;
                        min-height: 40px;
                    }

                    /* Responsive adjustments for fabric */
                    @media (max-width: 768px) {
                        #comboFabricOptions .size-checkmark {
                            padding: 8px 12px;
                            font-size: 12px;
                            min-height: 36px;
                        }

                        #comboQuickViewModal .generic-attribute-options {
                            gap: 6px;
                        }
                    }

                    /* For very long fabric names, allow wrapping */
                    #comboFabricOptions .size-checkmark.long-text {
                        white-space: normal;
                        word-break: break-word;
                        line-height: 1.3;
                        padding: 8px 10px;
                    }

                    /* Combo Modal Color Fix */
                    #comboQuickViewModal .color-container {
                        position: relative;
                        display: inline-block;
                        margin: 0 8px 8px 0;
                        cursor: pointer;
                    }

                    #comboQuickViewModal .color-checkmark {
                        display: block;
                        width: 40px;
                        height: 40px;
                        border-radius: 50%;
                        cursor: pointer;
                        border: 2px solid #ddd;
                        transition: all 0.3s ease;
                        background-color: #ccc;
                    }

                    #comboQuickViewModal .color-container.active .color-checkmark {
                        border-color: #000;
                        transform: scale(1.1);
                        box-shadow: 0 0 0 2px rgba(0, 0, 0, 0.1);
                    }

                    #comboQuickViewModal .color-checkmark:hover {
                        border-color: #666;
                        transform: scale(1.05);
                    }

                    #comboQuickViewModal .color-container.success .color-checkmark {
                        border-color: #28a745;
                        box-shadow: 0 0 0 2px rgba(40, 167, 69, 0.2);
                    }

                    #comboQuickViewModal .size-container {
                        margin: 0 8px 8px 0;
                        cursor: pointer;
                    }
                    .
                    #comboQuickViewModal .size-checkmark {
                        display: inline-block;
                        padding: 10px 16px;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        cursor: pointer;
                        font-size: 14px;
                        transition: all 0.3s ease;
                        min-width: 50px;
                        text-align: center;
                        background-color: white;
                    }

                    #comboQuickViewModal .size-checkmark:hover {
                        border-color: #666;
                        background-color: #f8f9fa;
                    }

                    #comboQuickViewModal .size-checkmark.active {
                        border-color: #000;
                        background-color: #000;
                        color: white;
                        font-weight: 600;
                    }

                    #comboQuickViewModal .size-checkmark.success {
                        border-color: #28a745;
                        background-color: #f8fff9;
                        color: #28a745;
                    }
                 
                    #comboQuickViewModal .color-box {
                        display: flex;
                        flex-wrap: wrap;
                        gap: 12px;
                        padding: 5px 0;
                    }

                    /* Hide radio inputs but keep them accessible */
                    #comboQuickViewModal .combo-attribute-option {
                        position: absolute;
                        opacity: 0;
                        width: 0;
                        height: 0;
                    }
                </style>
                <script>
                    $(document).ready(function() {
                        // GLOBAL STATE
                        let currentComboPrice = 0;
                        let currentComboRegularPrice = 0;
                        let currentQuantity = 1;
                        let currentComboId = 0;
                        let currentComboProducts = []; // Store combo product IDs
                        let currentComboProductId = null; // Store the combo product ID we're fetching attributes from
                        let comboVariationPrices = {}; // Store variation prices for each combo product: {productId: {price: X, regular_price: Y}}
                        let comboBasePrices = {}; // Store base prices for each combo product

                        // Store attribute data from main product
                        const attributeOptions = @json($attributeOptions);
                        const defaultSelections = @json($defaultSelections);

                        function updateComboPartnerMessage() {
                            console.log("updateComboPartnerMessage called - Quantity:", currentQuantity);

                            const comboPartnerMessage = $('#comboPartnerMessage');

                            if (currentQuantity > 50) { // Show when quantity is MORE THAN 50
                                comboPartnerMessage.slideDown(300);
                            } else {
                                comboPartnerMessage.slideUp(300);
                            }
                        }

                        // Quick View Modal Open
                        $('#comboQuickViewModal').on('show.bs.modal', function(event) {
                            let button = $(event.relatedTarget);

                            // Sync main product price from current selection before opening modal
                            // This ensures the combo modal has the correct price from product_attribute table
                            if (typeof updateMainProductFields === 'function') {
                                updateMainProductFields();
                            }
                            
                            // Also sync main product attributes
                            if (typeof buildMainProductAttributes === 'function') {
                                const mainProductAttributes = buildMainProductAttributes();
                                if ($('#comboMainProductAttributesInput').length) {
                                    $('#comboMainProductAttributesInput').val(JSON.stringify(mainProductAttributes));
                                }
                            }

                            // READ COMBO DATA
                            let comboId = button.data('combo-id');
                            // Keep combo_id as string (it's a generated identifier like "40_20251204111407")
                            if (!comboId || comboId === '' || comboId === 'undefined') {
                                console.error("Invalid combo_id from button:", button.data('combo-id'));
                                return;
                            }
                            // Convert to string to ensure it's not parsed as integer
                            comboId = String(comboId);
                            let comboName = button.data('combo-name');
                            let comboPrice = parseFloat(button.data('combo-price')) || 0;
                            let comboRegularPrice = parseFloat(button.data('combo-regular-price')) || 0;
                            let comboThumbnail = button.data('combo-thumbnail');
                            let comboProducts = button.data('combo-products');
                            let comboProductIds = button.data('combo-product-ids');

                            console.log("=== COMBO MODAL OPEN ===", {
                                comboId,
                                comboName,
                                comboPrice,
                                comboRegularPrice,
                                comboThumbnail,
                                comboProductIds
                            });

                            // SAVE STATE
                            currentComboId = comboId;
                            currentComboPrice = comboPrice;
                            currentComboRegularPrice = comboRegularPrice;
                            currentQuantity = 1;

                            // SET TEXT
                            $('#comboName').text(comboName || "Combo Package");

                            // SET INITIAL IMAGE (will be updated when combo product data loads)
                            // Try to load from combo thumbnail first
                            if (comboThumbnail && comboThumbnail !== 'undefined' && comboThumbnail.trim() !== '') {
                                loadComboImage(comboThumbnail, comboName);
                            } else {
                                // Set placeholder
                                loadComboImage('', comboName);
                            }

                            // FORM VALUES
                            // comboId is a string identifier (like "40_20251204111407")
                            $('#comboIdInput').val(comboId);
                            $('#comboPriceInput').val(comboPrice);
                            $('#comboMoqInput').val(1);
                            $('#comboQuantityDisplayInput').val(1);

                            // Clear previous combo item fields
                            $('#comboItemFieldsContainer').empty();

                            // PRODUCT LIST - Get product IDs from data attribute
                            let productIds = [];
                            if (comboProductIds) {
                                // Split comma-separated IDs
                                productIds = comboProductIds.toString().split(',').map(id => id.trim()).filter(id =>
                                id);
                                console.log("Combo product IDs from data attribute:", productIds);
                            } else {
                                // Fallback: try to extract from product names
                                let productArr = comboProducts ? comboProducts.split(', ') : [];
                                productArr.forEach((x) => {
                                    const match = x.match(/ID:\s*(\d+)/i);
                                    if (match) {
                                        productIds.push(match[1]);
                                    }
                                });
                                console.log("Combo product IDs extracted from names:", productIds);
                            }

                            // If still no IDs, use main product ID
                            if (productIds.length === 0) {
                                productIds = [{{ $product->id }}];
                                console.log("No combo product IDs found, using main product ID:", productIds);
                            }

                            console.log("Final combo product IDs to use:", productIds);

                            // Build product list HTML
                            let productArr = comboProducts ? comboProducts.split(', ') : [];
                            let html = "";
                            productArr.slice(0, 5).forEach((x) => {
                                html +=
                                    `<div class="combo-product-item mb-1"><i class="fa-solid fa-check-circle text-success me-2"></i>${x}</div>`;
                            });

                            if (productArr.length > 5) {
                                html +=
                                    `<div class="text-muted small"><i class="fa-solid fa-plus-circle me-1"></i>${productArr.length - 5} more products</div>`;
                            }

                            $('#comboProductsList').html(html || '<div class="text-muted">No products found</div>');

                            // Store product IDs for later use
                            currentComboProducts = productIds;

                            // Add hidden fields for each combo product (including fabric)
                            productIds.forEach(productId => {
                                $('#comboItemFieldsContainer').append(`
                <input type="hidden" name="combo_item_color_ids[${productId}]" id="comboItemColor_${productId}" value="">
                <input type="hidden" name="combo_item_size_ids[${productId}]" id="comboItemSize_${productId}" value="">
                <input type="hidden" name="combo_item_fabric_ids[${productId}]" id="comboItemFabric_${productId}" value="">
            `);
                            });

                            // PRICE UPDATE
                            updateComboPrice();

                            // UPDATE PARTNER MESSAGE (initial state) - FIXED: Changed to updateComboPartnerMessage
                            updateComboPartnerMessage();

                            // HIDE ALL ATTRIBUTE SECTIONS INITIALLY (will show if attributes found)
                            hideAllComboAttributeSections();

                            // LOAD ATTRIBUTE OPTIONS AND IMAGES FROM FIRST COMBO PRODUCT
                            if (productIds.length > 0) {
                                // Store the combo product ID we're fetching attributes from
                                currentComboProductId = productIds[0];
                                
                                // Initialize base prices for all combo products
                                productIds.forEach(productId => {
                                    if (!comboBasePrices[productId]) {
                                        comboBasePrices[productId] = parseFloat(comboPrice) / productIds.length; // Distribute combo price equally
                                    }
                                });
                                
                                // Use first combo product for attributes and images
                                loadComboProductData(productIds[0]);
                            } else {
                                // No combo product IDs - hide all attribute sections
                                hideAllComboAttributeSections();
                            }

                            // Reset alert
                            hideComboVariationError();
                            $('.add-tocart-btn').prop('disabled', false);
                        });

                        // LOAD COMBO PRODUCT DATA (attributes and images)
                        function loadComboProductData(productId) {
                            console.log("=== LOADING COMBO PRODUCT DATA ===");
                            console.log("Product ID:", productId);
                            console.log("Route URL:", '{{ route('frontend.product.getAttributes') }}');

                            // Validate product ID
                            if (!productId || productId === 'undefined' || productId === '') {
                                console.error("Invalid product ID:", productId);
                                hideAllComboAttributeSections();
                                return;
                            }

                            $.ajax({
                                url: '{{ route('frontend.product.getAttributes') }}',
                                method: 'POST',
                                data: {
                                    product_id: productId,
                                    _token: '{{ csrf_token() }}'
                                },
                                beforeSend: function() {
                                    console.log("Sending AJAX request for product ID:", productId);
                                },
                                success: function(response) {
                                    console.log("Combo product data received:", response);

                                    if (response.status === 'success') {
                                        // Load images - prioritize images array, then thumbnail
                                        let imageLoaded = false;

                                        if (response.images && response.images.length > 0) {
                                            // Use first image from images array
                                            loadComboImage(response.images[0], response.product.name);
                                            imageLoaded = true;

                                            // Update thumbnails if multiple images
                                            if (response.images.length > 1) {
                                                updateComboThumbnails(response.images);
                                            } else {
                                                // Single image - update thumbnail
                                                $('#comboThumb1').attr('src', response.images[0]).on('error',
                                                    function() {
                                                        $(this).attr('src',
                                                            'https://via.placeholder.com/60x60?text=No+Image'
                                                            );
                                                    });
                                            }
                                        } else if (response.thumbnail) {
                                            // Use thumbnail if no images array
                                            loadComboImage(response.thumbnail, response.product.name);
                                            $('#comboThumb1').attr('src', response.thumbnail).on('error',
                                        function() {
                                                $(this).attr('src',
                                                    'https://via.placeholder.com/60x60?text=No+Image');
                                            });
                                            imageLoaded = true;
                                        }

                                        if (!imageLoaded) {
                                            console.log("No images found in response, keeping initial thumbnail");
                                        }

                                        // Store base price for this combo product
                                        if (response.product && response.product.price) {
                                            comboBasePrices[productId] = parseFloat(response.product.price);
                                        }
                                        
                                        // Load attributes
                                        if (response.attributeOptions && response.attributeOptions.length > 0) {
                                            console.log("Loading attributes from combo product:", response
                                                .attributeOptions);
                                            loadComboAttributesWithData(response.attributeOptions, response
                                                .defaultSelections || {});
                                            
                                            // Fetch initial variation price if default selections exist
                                            setTimeout(() => {
                                                fetchComboVariationPrice(productId);
                                            }, 500);
                                        } else {
                                            // No attributes found - hide all attribute sections
                                            console.log("No attributes found for combo product - hiding all attribute sections");
                                            hideAllComboAttributeSections();
                                            
                                            // Use base price if no attributes
                                            updateComboPriceFromVariations();
                                        }
                                    } else {
                                        console.error("Error loading combo product data:", response.message);
                                        // No attributes available - hide all sections
                                        hideAllComboAttributeSections();
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("Error fetching combo product data:", {
                                        status: xhr.status,
                                        statusText: xhr.statusText,
                                        error: error,
                                        responseText: xhr.responseText
                                    });

                                    // Show error message
                                    if (xhr.status === 404) {
                                        console.error(
                                            "Route not found. Check if route 'product.getAttributes' exists."
                                            );
                                    } else if (xhr.status === 500) {
                                        console.error("Server error. Check server logs.");
                                    }

                                    // No attributes available - hide all sections
                                    hideAllComboAttributeSections();
                                }
                            });
                        }

                        // UPDATE COMBO THUMBNAILS
                        function updateComboThumbnails(images) {
                            const thumbnailRow = $('.thumbnail-row');
                            thumbnailRow.empty();

                            images.slice(0, 5).forEach((img, index) => {
                                const thumbHtml = `
                                <div class="thumb-box ${index === 0 ? 'active' : ''}" data-image-index="${index}">
                                    <img src="${img}"
                                         class="img-fluid rounded border"
                                         style="width:60px; height:60px; object-fit:cover; cursor: pointer;"
                                         alt="Thumbnail ${index + 1}"
                                         onerror="this.onerror=null; this.src='https://via.placeholder.com/60x60?text=No+Image'">
                                </div>
                            `;
                                thumbnailRow.append(thumbHtml);
                            });

                            // Add click handlers for thumbnails
                            $('.thumb-box').on('click', function() {
                                const imgSrc = $(this).find('img').attr('src');
                                $('#comboMainImage').attr('src', imgSrc);
                                $('.thumb-box').removeClass('active');
                                $(this).addClass('active');
                            });
                        }

                        // HIDE ALL COMBO ATTRIBUTE SECTIONS
                        function hideAllComboAttributeSections() {
                            console.log("Hiding all combo attribute sections - no attributes available");
                            $('#comboColorSection').hide();
                            $('#comboSizeSection').hide();
                            $('#comboFabricSection').hide();
                            
                            // Clear options
                            $('#comboColorOptions').empty();
                            $('#comboSizeOptions').empty();
                            $('#comboFabricOptions').empty();
                            
                            // Reset labels
                            $('#selectedComboColorLabel').text('Please select');
                            $('#selectedComboSizeLabel').text('Please select');
                            $('#selectedComboFabricLabel').text('Please select');
                            
                            // Clear validation
                            hideComboVariationError();
                            $('.add-tocart-btn').prop('disabled', false);
                        }

                        // LOAD ATTRIBUTES FOR COMBO WITH PROVIDED DATA
                        function loadComboAttributesWithData(comboAttributeOptions, comboDefaultSelections) {
                            console.log("Loading combo attributes with data...", comboAttributeOptions);

                            // Clear previous attributes
                            $('#comboColorOptions').empty();
                            $('#comboSizeOptions').empty();
                            $('#comboFabricOptions').empty();

                            // Hide all sections initially
                            $('#comboColorSection').hide();
                            $('#comboSizeSection').hide();
                            $('#comboFabricSection').hide();

                            // Reset labels
                            $('#selectedComboColorLabel').text('Please select');
                            $('#selectedComboSizeLabel').text('Please select');
                            $('#selectedComboFabricLabel').text('Please select');

                            // Check if we have attribute options
                            if (!comboAttributeOptions || comboAttributeOptions.length === 0) {
                                console.log("No attribute options found");
                                return;
                            }

                            // Process each attribute
                            comboAttributeOptions.forEach(function(option) {
                                const attributeId = option.attribute_id;
                                const attributeName = option.attribute_name;
                                const attributeSlug = option.attribute_slug ? option.attribute_slug.toLowerCase() : '';
                                const terms = option.terms || [];

                                console.log(`Processing: ${attributeName} (${attributeSlug})`);

                                // Skip if no terms
                                if (terms.length === 0) return;

                                // Find default term
                                const defaultTerm = comboDefaultSelections[attributeId] || null;
                                const defaultTermId = defaultTerm ? defaultTerm.term_id : terms[0].term_id;
                                const defaultTermName = defaultTerm ? defaultTerm.term_name : terms[0].term_name;

                                // COLOR ATTRIBUTE
                                if (
                                    attributeSlug === 'colour' ||
                                    attributeSlug === 'color' ||
                                    attributeName.toLowerCase().includes('colour') ||
                                    attributeName.toLowerCase().includes('color')
                                ) {

                                    console.log(`Found Color attribute: ${attributeName}`);
                                    $('#comboColorSection').show();

                                    let colorHtml = '';
                                    terms.forEach(function(term) {
                                        const isChecked = term.term_id === defaultTermId;
                                        const colorCode = term.color_code || '#cccccc';
                                        const colorValue = tinycolor(colorCode).isValid() ?
                                            tinycolor(colorCode).toHexString() :
                                            '#cccccc';
                                        const termName = term.term_name || '';

                                        colorHtml += `
                                            <label class="color-container">
                                                <input type="radio" 
                                                    class="combo-attribute-option" 
                                                    name="attribute_${attributeId}" 
                                                    value="${term.term_id}" 
                                                    ${isChecked ? 'checked' : ''}
                                                    data-attribute-id="${attributeId}"
                                                    data-attribute-slug="${attributeSlug}"
                                                    data-term-id="${term.term_id}"
                                                    data-term-name="${termName}"
                                                    data-color="${colorValue}"
                                                    data-attribute-type="color">
                                                <span class="color-checkmark"></span>
                                            </label>
                                        `;
                                    });

                                    $('#comboColorOptions').html(colorHtml);

                                    // Set default selection
                                    if (defaultTermId) {
                                        $('#selectedComboColorLabel').text(defaultTermName);
                                        // Update all combo item color fields
                                        currentComboProducts.forEach(productId => {
                                            $(`#comboItemColor_${productId}`).val(defaultTermId);
                                        });
                                    }

                                    initializeComboColorSwatches();
                                }

                                // SIZE ATTRIBUTE
                                else if (attributeSlug === 'size' || attributeName.toLowerCase().includes('size')) {
                                    console.log(`Found Size attribute: ${attributeName}`);
                                    $('#comboSizeSection').show();

                                    let sizeHtml = '';
                                    terms.forEach(function(term) {
                                        const isChecked = term.term_id === defaultTermId;
                                        const termName = term.term_name || '';

                                        sizeHtml += `
                    <label class="size-container">
                        <input type="radio" 
                               class="combo-attribute-option" 
                               name="attribute_${attributeId}" 
                               value="${term.term_id}" 
                               ${isChecked ? 'checked' : ''}
                               data-attribute-id="${attributeId}"
                               data-attribute-slug="${attributeSlug}"
                               data-term-id="${term.term_id}"
                               data-term-name="${termName}"
                               data-attribute-type="size">
                        <span class="size-checkmark ${isChecked ? 'active' : ''}">${termName}</span>
                    </label>
                `;
                                    });

                                    $('#comboSizeOptions').html(sizeHtml);

                                    // Set default selection
                                    if (defaultTermId) {
                                        $('#selectedComboSizeLabel').text(defaultTermName);
                                        // Update all combo item size fields
                                        currentComboProducts.forEach(productId => {
                                            $(`#comboItemSize_${productId}`).val(defaultTermId);
                                        });
                                    }
                                }

                                // FABRIC ATTRIBUTE - Check for fabric/material attributes
                                // This should match any attribute that is not Color or Size
                                else if (
                                    attributeSlug === 'fabric' ||
                                    attributeSlug === 'material' ||
                                    attributeSlug === 'febric' || // Common typo
                                    (attributeSlug && attributeSlug.includes('fabric')) ||
                                    (attributeSlug && attributeSlug.includes('material')) ||
                                    (attributeSlug && attributeSlug.includes('febric')) ||
                                    attributeName.toLowerCase().includes('fabric') ||
                                    attributeName.toLowerCase().includes('material') ||
                                    attributeName.toLowerCase().includes('febric')
                                ) {

                                    console.log(`Found Fabric attribute: ${attributeName} (slug: ${attributeSlug})`);
                                    $('#comboFabricSection').show();

                                    let fabricHtml = '';
                                    terms.forEach(function(term) {
                                        const isChecked = term.term_id === defaultTermId;
                                        const termName = term.term_name || '';

                                        fabricHtml += `
                    <label class="size-container">
                        <input type="radio" 
                               class="combo-attribute-option" 
                               name="attribute_${attributeId}" 
                               value="${term.term_id}" 
                               ${isChecked ? 'checked' : ''}
                               data-attribute-id="${attributeId}"
                               data-attribute-slug="${attributeSlug}"
                               data-term-id="${term.term_id}"
                               data-term-name="${termName}"
                               data-attribute-type="fabric">
                        <span class="size-checkmark ${isChecked ? 'active' : ''}">${termName}</span>
                    </label>
                `;
                                    });

                                    $('#comboFabricOptions').html(fabricHtml);

                                    // Set default selection
                                    if (defaultTermId) {
                                        $('#selectedComboFabricLabel').text(defaultTermName);
                                        // Update all combo item fabric fields
                                        currentComboProducts.forEach(productId => {
                                            $(`#comboItemFabric_${productId}`).val(defaultTermId);
                                        });
                                    }
                                }
                            });

                            // Check if any generic attributes (like Fabric) that weren't caught by specific checks
                            // Process remaining attributes that are not Color or Size
                            comboAttributeOptions.forEach(function(option) {
                                const attributeId = option.attribute_id;
                                const attributeName = option.attribute_name;
                                const attributeSlug = option.attribute_slug ? option.attribute_slug.toLowerCase() : '';
                                const terms = option.terms || [];

                                // Skip if already processed (Color or Size)
                                if ($(`#comboColorSection input[data-attribute-id="${attributeId}"]`).length > 0 ||
                                    $(`#comboSizeSection input[data-attribute-id="${attributeId}"]`).length > 0 ||
                                    $(`#comboFabricSection input[data-attribute-id="${attributeId}"]`).length > 0) {
                                    return;
                                }

                                // Skip if no terms
                                if (terms.length === 0) return;

                                // If it's not Color or Size, treat it as Fabric or generic attribute
                                const isColor = attributeSlug === 'colour' || attributeSlug === 'color' ||
                                    attributeName.toLowerCase().includes('colour') ||
                                    attributeName.toLowerCase().includes('color');
                                const isSize = attributeSlug === 'size' || attributeName.toLowerCase().includes('size');

                                if (!isColor && !isSize) {
                                    console.log(`Found generic/Fabric attribute (not Color/Size): ${attributeName} (slug: ${attributeSlug})`);
                                    
                                    // Show Fabric section for any non-Color, non-Size attribute
                                    $('#comboFabricSection').show();

                                    const defaultTerm = comboDefaultSelections[attributeId] || null;
                                    const defaultTermId = defaultTerm ? defaultTerm.term_id : terms[0].term_id;
                                    const defaultTermName = defaultTerm ? defaultTerm.term_name : terms[0].term_name;

                                    let fabricHtml = '';
                                    terms.forEach(function(term) {
                                        const isChecked = term.term_id === defaultTermId;
                                        const termName = term.term_name || '';

                                        fabricHtml += `
                    <label class="size-container">
                        <input type="radio" 
                               class="combo-attribute-option" 
                               name="attribute_${attributeId}" 
                               value="${term.term_id}" 
                               ${isChecked ? 'checked' : ''}
                               data-attribute-id="${attributeId}"
                               data-attribute-slug="${attributeSlug}"
                               data-term-id="${term.term_id}"
                               data-term-name="${termName}"
                               data-attribute-type="fabric">
                        <span class="size-checkmark ${isChecked ? 'active' : ''}">${termName}</span>
                    </label>
                `;
                                    });

                                    // Append to existing fabric options (in case multiple fabric attributes exist)
                                    $('#comboFabricOptions').append(fabricHtml);

                                    // Set default selection
                                    if (defaultTermId) {
                                        $('#selectedComboFabricLabel').text(defaultTermName);
                                        // Update all combo item fabric fields
                                        currentComboProducts.forEach(productId => {
                                            $(`#comboItemFabric_${productId}`).val(defaultTermId);
                                        });
                                    }
                                }
                            });

                            // Initialize attribute handlers
                            initializeComboAttributeHandlers();

                            // Validate initial combination
                            validateComboCombination();
                        }


                        // LOAD ATTRIBUTES FOR COMBO (using main product attributes as fallback)
                        function loadComboAttributes() {
                            console.log("Loading combo attributes from main product...", attributeOptions);

                            loadComboAttributesWithData(attributeOptions, defaultSelections);
                        }

                        // INITIALIZE COLOR SWATCHES
                        function initializeComboColorSwatches() {
                            $('#comboColorOptions .combo-attribute-option').each(function() {
                                const colorValue = $(this).data('color');
                                const color = tinycolor(colorValue);
                                const appliedColor = color.isValid() ? color.toHexString() : '#cccccc';
                                $(this).siblings('.color-checkmark').css('background-color', appliedColor);

                                if ($(this).is(':checked')) {
                                    $(this).closest('.color-container').addClass('active');
                                }
                            });
                        }

                        // Initialize attribute handlers
                        function initializeComboAttributeHandlers() {
                            $(document).off('change', '.combo-attribute-option').on('change', '.combo-attribute-option',
                                function() {
                                    const attributeId = $(this).data('attribute-id');
                                    const attributeSlug = $(this).data('attribute-slug');
                                    const attributeType = $(this).data('attribute-type') || '';
                                    const termId = $(this).val();
                                    const termName = $(this).data('term-name');

                                    console.log(
                                        `Combo attribute selected: ${attributeSlug} (${attributeType}) = ${termName}`);

                                    // Update based on attribute type
                                    if (attributeType === 'color') {
                                        $('#selectedComboColorLabel').text(termName);

                                        // Update ALL combo item color fields with this selection
                                        currentComboProducts.forEach(productId => {
                                            $(`#comboItemColor_${productId}`).val(termId);
                                        });

                                        $(this).closest('.color-container').addClass('active');
                                        $(`input[name="attribute_${attributeId}"]`).not(this)
                                            .closest('.color-container')
                                            .removeClass('active');
                                    } else if (attributeType === 'size') {
                                        $('#selectedComboSizeLabel').text(termName);

                                        // Update ALL combo item size fields with this selection
                                        currentComboProducts.forEach(productId => {
                                            $(`#comboItemSize_${productId}`).val(termId);
                                        });

                                        $(this).siblings('.size-checkmark').addClass('active');
                                        $(`input[name="attribute_${attributeId}"]`).not(this)
                                            .siblings('.size-checkmark')
                                            .removeClass('active');
                                    } else if (attributeType === 'fabric') {
                                        $('#selectedComboFabricLabel').text(termName);

                                        // Update ALL combo item fabric fields with this selection
                                        currentComboProducts.forEach(productId => {
                                            $(`#comboItemFabric_${productId}`).val(termId);
                                        });

                                        $(this).siblings('.size-checkmark').addClass('active');
                                        $(`input[name="attribute_${attributeId}"]`).not(this)
                                            .siblings('.size-checkmark')
                                            .removeClass('active');
                                    }

                                    // VALIDATE COMBINATION
                                    validateComboCombination();
                                    
                                    // FETCH VARIATION PRICE WHEN ATTRIBUTES CHANGE
                                    if (currentComboProductId) {
                                        fetchComboVariationPrice(currentComboProductId);
                                    }
                                });

                            // Set initial active states
                            setTimeout(() => {
                                $('.combo-attribute-option:checked').each(function() {
                                    const attributeType = $(this).data('attribute-type');

                                    if (attributeType === 'color') {
                                        $(this).closest('.color-container').addClass('active');
                                    } else {
                                        $(this).siblings('.size-checkmark').addClass('active');
                                    }
                                });
                            }, 100);
                        }

                        // VALIDATE ATTRIBUTE SELECTION
                        function validateComboCombination() {
                            // Check if color, size, and fabric are required and selected
                            const hasColorSection = $('#comboColorSection').is(':visible');
                            const hasSizeSection = $('#comboSizeSection').is(':visible');
                            const hasFabricSection = $('#comboFabricSection').is(':visible');

                            let isValid = true;
                            let missingFields = [];

                            if (hasColorSection) {
                                // Check if any color is selected
                                const colorSelected = $('.combo-attribute-option[data-attribute-type="color"]:checked').length >
                                    0;
                                if (!colorSelected) {
                                    isValid = false;
                                    missingFields.push('Color');
                                }
                            }

                            if (hasSizeSection) {
                                // Check if any size is selected
                                const sizeSelected = $('.combo-attribute-option[data-attribute-type="size"]:checked').length >
                                0;
                                if (!sizeSelected) {
                                    isValid = false;
                                    missingFields.push('Size');
                                }
                            }

                            if (hasFabricSection) {
                                // Check if any fabric is selected
                                const fabricSelected = $('.combo-attribute-option[data-attribute-type="fabric"]:checked')
                                    .length > 0;
                                if (!fabricSelected) {
                                    isValid = false;
                                    missingFields.push('Fabric');
                                }
                            }

                            if (!isValid && missingFields.length > 0) {
                                const message = `Please select ${missingFields.join(', ')} before adding to cart.`;
                                $('#comboValidationMessage').text(message);
                                $('#comboVariationAlert').removeClass('d-none').addClass('alert-warning').removeClass('alert-danger');
                                $('.add-tocart-btn').prop('disabled', true);
                                return false;
                            }

                            // Only hide if it's not showing combination error (alert-danger)
                            if (!$('#comboVariationAlert').hasClass('alert-danger')) {
                                $('#comboVariationAlert').addClass('d-none');
                            }
                            
                            // Enable button - combination is valid and all required attributes are selected
                            $('.add-tocart-btn, #comboAddToCartForm button[type="submit"]').prop('disabled', false).removeClass('disabled');
                            
                            return true;
                        }

                        // SET IMAGE WITH FALLBACK
                        function loadComboImage(img, alt) {
                            let placeholder = "";

                            if (!img || img === "undefined" || img.trim() === "") {
                                img = placeholder;
                            }

                            $("#comboMainImage")
                                .attr("src", img)
                                .attr("alt", alt)
                                .on("error", function() {
                                    $(this).attr("src", placeholder);
                                });

                            $("#comboThumb1")
                                .attr("src", img)
                                .attr("alt", alt)
                                .on("error", function() {
                                    $(this).attr("src", placeholder);
                                });
                        }

                        // FETCH VARIATION PRICE FOR COMBO PRODUCT
                        function fetchComboVariationPrice(productId) {
                            if (!productId) {
                                console.error("No product ID provided for fetching variation price");
                                return;
                            }

                            // Get selected term IDs from combo modal
                            const termIds = [];
                            const attributes = {};
                            
                            $('.combo-attribute-option:checked').each(function() {
                                const attrId = $(this).data('attribute-id');
                                const termId = parseInt($(this).val(), 10);
                                if (attrId && termId && !isNaN(termId) && termId > 0) {
                                    termIds.push(termId);
                                    attributes[attrId] = termId;
                                }
                            });

                            // If no attributes selected, use base price
                            if (termIds.length === 0) {
                                console.log("No attributes selected, using base price");
                                updateComboPriceFromVariations();
                                return;
                            }

                            console.log("Fetching variation price for combo product:", productId, "with term IDs:", termIds);

                            $.ajax({
                                url: '{{ route('frontend.product.fetchVariation') }}',
                                method: 'POST',
                                data: {
                                    product_id: productId,
                                    term_ids: termIds,
                                    attributes: attributes,
                                    _token: '{{ csrf_token() }}'
                                },
                                success: function(response) {
                                    console.log("Combo variation price response:", response);
                                    
                                    if (response.status === 'success' && response.data) {
                                        const variation = response.data;
                                        
                                        // Store variation price for this combo product
                                        comboVariationPrices[productId] = {
                                            price: parseFloat(variation.price || variation.regular_price || comboBasePrices[productId] || 0),
                                            regular_price: parseFloat(variation.regular_price || variation.price || comboBasePrices[productId] || 0)
                                        };
                                        
                                        console.log("Stored variation price for product", productId, ":", comboVariationPrices[productId]);
                                        
                                        // Hide error alert - combination is available
                                        hideComboVariationError();
                                        
                                        // Explicitly enable button since valid combination is found
                                        // Use setTimeout to ensure validation runs first, then enable button
                                        setTimeout(() => {
                                            // Always enable button when valid combination is found
                                            // validateComboCombination() will handle if attributes are missing
                                            $('.add-tocart-btn, #comboAddToCartForm button[type="submit"]').prop('disabled', false).removeClass('disabled');
                                            
                                            // Re-validate to ensure button state is correct
                                            validateComboCombination();
                                        }, 200);
                                    } else if (response.status === 'not_found' || !response.data) {
                                        // Variation not found - show error
                                        showComboVariationError();
                                        
                                        // Don't update price, keep previous or base price
                                        if (!comboVariationPrices[productId]) {
                                            comboVariationPrices[productId] = {
                                                price: comboBasePrices[productId] || 0,
                                                regular_price: comboBasePrices[productId] || 0
                                            };
                                        }
                                    } else {
                                        // Other status - hide error and use base price
                                        hideComboVariationError();
                                        comboVariationPrices[productId] = {
                                            price: comboBasePrices[productId] || 0,
                                            regular_price: comboBasePrices[productId] || 0
                                        };
                                    }
                                    
                                    // Update combo price display
                                    updateComboPriceFromVariations();
                                },
                                error: function(xhr, status, error) {
                                    console.error("Error fetching combo variation price:", error);
                                    
                                    // Check if it's a 404 (not found) error
                                    if (xhr.status === 404) {
                                        // Combination not available
                                        showComboVariationError();
                                    } else {
                                        // Other error - hide error message and enable button
                                        hideComboVariationError();
                                        // For other errors (not 404), still enable button (might be network issue)
                                        setTimeout(() => {
                                            if (validateComboCombination()) {
                                                $('.add-tocart-btn, #comboAddToCartForm button[type="submit"]').prop('disabled', false).removeClass('disabled');
                                            }
                                        }, 150);
                                    }
                                    
                                    // Use base price on error
                                    if (!comboVariationPrices[productId]) {
                                        comboVariationPrices[productId] = {
                                            price: comboBasePrices[productId] || 0,
                                            regular_price: comboBasePrices[productId] || 0
                                        };
                                    }
                                    updateComboPriceFromVariations();
                                }
                            });
                        }

                        // SHOW COMBO VARIATION ERROR
                        function showComboVariationError() {
                            $('#comboValidationMessage').text('This combination is not available. Please choose a different combination.');
                            $('#comboVariationAlert').removeClass('d-none').addClass('alert-danger').removeClass('alert-warning').show();
                            
                            // Disable add to cart button
                            $('.add-tocart-btn, #comboAddToCartForm button[type="submit"]').prop('disabled', true).addClass('disabled');
                        }

                        // HIDE COMBO VARIATION ERROR
                        function hideComboVariationError() {
                            // Only hide if it's showing combination error (alert-danger)
                            // Don't hide if it's showing validation warning (alert-warning)
                            if ($('#comboVariationAlert').hasClass('alert-danger')) {
                                $('#comboVariationAlert').addClass('d-none').removeClass('alert-danger');
                            }
                            
                            // Always re-validate to update button state
                            // This will enable button if combination is valid and all attributes are selected
                            validateComboCombination();
                        }

                        // UPDATE COMBO PRICE FROM VARIATION PRICES
                        function updateComboPriceFromVariations() {
                            let totalPrice = 0;
                            let totalRegularPrice = 0;
                            
                            // Calculate total price from all combo products
                            currentComboProducts.forEach(productId => {
                                if (comboVariationPrices[productId]) {
                                    totalPrice += comboVariationPrices[productId].price || 0;
                                    totalRegularPrice += comboVariationPrices[productId].regular_price || 0;
                                } else if (comboBasePrices[productId]) {
                                    // Use base price if no variation price
                                    totalPrice += comboBasePrices[productId];
                                    totalRegularPrice += comboBasePrices[productId];
                                }
                            });
                            
                            // If no variation prices, use the original combo price
                            if (totalPrice === 0) {
                                totalPrice = currentComboPrice;
                                totalRegularPrice = currentComboRegularPrice;
                            }
                            
                            // Update global price variables
                            currentComboPrice = totalPrice;
                            currentComboRegularPrice = totalRegularPrice;
                            
                            // Update display
                            updateComboPrice();
                        }

                        // PRICE UPDATE
                        function updateComboPrice() {
                            let total = currentComboPrice * currentQuantity;
                            let totalRegular = currentComboRegularPrice * currentQuantity;

                            $('#comboCurrentPrice').text("₹" + total.toFixed(2));

                            if (currentComboRegularPrice > currentComboPrice) {
                                $('#comboOldPrice').text("₹" + totalRegular.toFixed(2)).show();

                                let discount = Math.round(((totalRegular - total) / totalRegular) * 100);
                                $('#comboDiscount').text(discount + "% OFF").show();
                            } else {
                                $('#comboOldPrice, #comboDiscount').hide();
                            }

                            // UPDATE ALL HIDDEN INPUTS
                            $('#comboMoqInput').val(currentQuantity);
                            $('#comboQuantityDisplayInput').val(currentQuantity);
                            $('#comboPriceInput').val(currentComboPrice);

                            // UPDATE PARTNER MESSAGE - FIXED: Changed to updateComboPartnerMessage
                            updateComboPartnerMessage();
                        }

                        // QUANTITY +/-
                        $('#comboIncrementBtn').click(function() {
                            currentQuantity++;
                            $('#comboQuantityDisplayInput').val(currentQuantity);
                            updateComboPrice();
                            updateComboPartnerMessage(); // FIXED: Changed to updateComboPartnerMessage
                        });

                        $('#comboDecrementBtn').click(function() {
                            if (currentQuantity > 1) {
                                currentQuantity--;
                                $('#comboQuantityDisplayInput').val(currentQuantity);
                                updateComboPrice();
                                updateComboPartnerMessage(); // FIXED: Changed to updateComboPartnerMessage
                            }
                        });

                        // MANUAL INPUT HANDLING
                        $('#comboQuantityDisplayInput').on('input change blur', function() {
                            let inputQty = parseInt($(this).val(), 10);

                            if (isNaN(inputQty) || inputQty < 1) {
                                inputQty = 1;
                                $(this).val(1);
                            }

                            currentQuantity = inputQty;
                            updateComboPrice();
                            updateComboPartnerMessage(); // FIXED: Changed to updateComboPartnerMessage
                        });

                        // COLLECT MAIN PRODUCT ATTRIBUTES
                        function collectMainProductAttributes() {
                            const mainAttributes = {};
                            
                            // Get all selected attributes from main product form
                            $('.product-attribute-option:checked').each(function() {
                                const attributeId = $(this).data('attribute-id');
                                const termId = parseInt($(this).val(), 10);
                                const termName = $(this).data('term-name') || '';
                                const attributeSlug = $(this).data('attribute-slug') || '';
                                
                                if (attributeId && termId && !isNaN(termId)) {
                                    mainAttributes[attributeId] = {
                                        'term_id': termId,
                                        'term_name': termName,
                                        'attribute_slug': attributeSlug
                                    };
                                }
                            });
                            
                            return mainAttributes;
                        }

                        // COLLECT COMBO PRODUCT ATTRIBUTES
                        function collectComboProductAttributes() {
                            const comboAttributes = {};
                            
                            // Get all selected attributes from combo modal
                            $('.combo-attribute-option:checked').each(function() {
                                const attributeId = $(this).data('attribute-id');
                                const termId = parseInt($(this).val(), 10);
                                const termName = $(this).data('term-name') || '';
                                const attributeSlug = $(this).data('attribute-slug') || '';
                                
                                if (attributeId && termId && !isNaN(termId)) {
                                    comboAttributes[attributeId] = {
                                        'term_id': termId,
                                        'term_name': termName,
                                        'attribute_slug': attributeSlug
                                    };
                                }
                            });
                            
                            return comboAttributes;
                        }

                        // ADD TO CART
                        $('#comboAddToCartForm').on('submit', function(e) {
                            e.preventDefault();

                            // Validate combo_id is set (it's a string identifier, not an integer)
                            const comboIdValue = $('#comboIdInput').val();
                            if (!comboIdValue || comboIdValue === '' || comboIdValue === 'undefined') {
                                console.error("Combo ID is missing:", comboIdValue);
                                alert('Invalid combo selection. Please try again.');
                                return;
                            }
                            
                            // Ensure combo_id is a valid string (format: "productId_timestamp" or similar)
                            if (typeof comboIdValue !== 'string' || comboIdValue.trim() === '') {
                                console.error("Combo ID is not a valid string:", comboIdValue);
                                alert('Invalid combo selection. Please try again.');
                                return;
                            }
                            
                            // Keep as string - don't convert to integer
                            $('#comboIdInput').val(comboIdValue.trim());

                            // Collect main product attributes - use buildMainProductAttributes to get array format
                            const mainProductAttributes = buildMainProductAttributes();
                            // Also update combo modal's attributes input if it exists
                            if ($('#comboMainProductAttributesInput').length) {
                                $('#comboMainProductAttributesInput').val(JSON.stringify(mainProductAttributes));
                            }
                            
                            // Collect combo product attributes
                            const comboProductAttributes = collectComboProductAttributes();
                            
                            // Add combo product attributes as hidden input
                            if (!$('#comboProductAttributesInput').length) {
                                $('#comboAddToCartForm').append('<input type="hidden" name="combo_product_attributes" id="comboProductAttributesInput" value="">');
                            }
                            $('#comboProductAttributesInput').val(JSON.stringify(comboProductAttributes));
                            
                            // Get main product price based on selected attributes (size, color, fabric)
                            // The priceInput should be updated when attributes change via the fetchVariation function
                            // Get selected term IDs for main product
                            const mainProductTermIds = [];
                            $('.product-attribute-option:checked').each(function() {
                                const termId = parseInt($(this).val(), 10);
                                if (!isNaN(termId) && termId > 0) {
                                    mainProductTermIds.push(termId);
                                }
                            });
                            
                            // Get the current price from priceInput (should be updated based on selected attributes)
                            let mainProductPrice = parseFloat($('#priceInput').val());
                            
                            // If priceInput is empty or 0, or if we have selected attributes but price seems wrong,
                            // try to get price from the variation matrix or use base price
                            if (!mainProductPrice || mainProductPrice <= 0) {
                                mainProductPrice = parseFloat('{{ $product->price }}');
                                
                                // If we have selected attributes, try to find the variation price from the matrix
                                if (mainProductTermIds.length > 0) {
                                    const variationMatrix = @json($variationMatrix ?? []);
                                    for (const key in variationMatrix) {
                                        const variation = variationMatrix[key];
                                        if (variation && variation.attribute_term_ids) {
                                            // Check if this variation matches selected term IDs
                                            const variationTermIds = Array.isArray(variation.attribute_term_ids) 
                                                ? variation.attribute_term_ids 
                                                : Object.values(variation.attribute_term_ids);
                                            
                                            // Check if all selected term IDs are in this variation
                                            const matches = mainProductTermIds.every(id => variationTermIds.includes(id)) &&
                                                          variationTermIds.length === mainProductTermIds.length;
                                            
                                            if (matches && variation.price) {
                                                mainProductPrice = parseFloat(variation.price);
                                                console.log('Found matching variation price:', mainProductPrice);
                                                break;
                                            }
                                        }
                                    }
                                }
                            }
                            
                            console.log('Main product price for combo:', mainProductPrice, 'Selected term IDs:', mainProductTermIds);
                            
                            // Set main product quantity and price in combo modal
                            $('#comboMainProductQuantityInput').val(1); // Main product quantity is always 1 for combo
                            $('#comboMainProductPriceInput').val(mainProductPrice);
                            
                            // Set combo product quantity and price
                            if (!$('#comboProductQuantityInput').length) {
                                $('#comboAddToCartForm').append('<input type="hidden" name="combo_product_quantity" id="comboProductQuantityInput" value="">');
                            }
                            $('#comboProductQuantityInput').val(currentQuantity);
                            
                            if (!$('#comboProductPriceInput').length) {
                                $('#comboAddToCartForm').append('<input type="hidden" name="combo_product_price" id="comboProductPriceInput" value="">');
                            }
                            $('#comboProductPriceInput').val(currentComboPrice);

                            // LOGIN CHECK
                            if (!{{ Auth::guard('customer')->check() ? 'true' : 'false' }}) {
                                window.location.href = "{{ route('frontend.employeelogin') }}";
                                return;
                            }

                            // VALIDATE ATTRIBUTE SELECTION
                            if (!validateComboCombination()) {
                                return;
                            }

                            let btn = $(this).find(".add-tocart-btn");
                            let oldTxt = btn.html();

                            btn.html('<i class="fa-solid fa-spinner fa-spin me-1"></i> Adding...')
                                .prop("disabled", true);

                            // Debug: Log form data
                            const formData = $(this).serializeArray();
                            console.log("Form data being submitted:", formData);

                            $.ajax({
                                url: $(this).attr("action"),
                                type: "POST",
                                data: $(this).serialize(),
                                dataType: 'json',
                                success: function(res) {
                                    console.log("COMBO ADDED SUCCESS:", res);

                                    // Restore button state
                                    btn.html(oldTxt).prop("disabled", false);
                                    $('#comboQuickViewModal').modal('hide');

                                    // SweetAlert Success
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'success',
                                            title: 'Added to Cart!',
                                            text: res.message ||
                                                'Combo added to cart successfully!',
                                            timer: 2000,
                                            showConfirmButton: false,
                                            position: 'top-end',
                                            toast: true
                                        });
                                    } else {
                                        alert(res.message || 'Combo added to cart successfully!');
                                    }

                                    // Prefer product count (distinct items). Accept multiple response shapes:
                                    // - res.cart_items_count (number)
                                    // - res.cart_items (array)
                                    // - res.cart.items (array)
                                    // Fallback to res.cart_count only if nothing else available.
                                    let productCountCombo;
                                    if (res.cart_items_count !== undefined) {
                                        productCountCombo = parseInt(res.cart_items_count, 10);
                                    } else if (res.cart_items && Array.isArray(res.cart_items)) {
                                        productCountCombo = res.cart_items.length;
                                    } else if (res.cart && res.cart.items && Array.isArray(res.cart.items)) {
                                        productCountCombo = res.cart.items.length;
                                    } else if (res.cart_count !== undefined) {
                                        productCountCombo = parseInt(res.cart_count, 10);
                                    }

                                    if (productCountCombo !== undefined && !isNaN(productCountCombo)) {
                                        $('.cart-count').text(productCountCombo);
                                        $('.mobile-cart-count').text(productCountCombo);
                                        const $headerBadge = $('.cart-count-badge');
                                        if ($headerBadge.length) {
                                            $headerBadge.text(productCountCombo);
                                        } else if (productCountCombo > 0) {
                                            $('.my-cart').append('<span class="cart-count-badge">' + productCountCombo + '</span>');
                                        } else {
                                            $('.cart-count-badge').remove();
                                        }
                                    }
                                    if (res.cart_total !== undefined) {
                                        let totalText = res.cart_total;
                                        if (!isNaN(parseFloat(totalText))) {
                                            totalText = '₹' + parseFloat(totalText).toFixed(2);
                                        }
                                        if ($('.cart-total-amount').length) {
                                            $('.cart-total-amount').text(totalText);
                                        } else {
                                            $('.my-cart').append('<span class="cart-total-amount ms-1">' + totalText + '</span>');
                                        }
                                    }

                                    // Optional redirect for "Buy Now"
                                    if (res.redirect) {
                                        setTimeout(() => {
                                            window.location.href = res.redirect;
                                        }, 2000);
                                    }
                                },
                                error: function(xhr, status, error) {
                                    console.error("ADD COMBO ERROR:", xhr.responseText);
                                    btn.html(oldTxt).prop("disabled", false);

                                    let errorMsg = 'Error adding combo. Please try again.';
                                    try {
                                        const response = JSON.parse(xhr.responseText);
                                        if (response.message) {
                                            errorMsg = response.message;
                                        } else if (response.errors) {
                                            errorMsg = Object.values(response.errors).flat().join('<br>');
                                        }
                                    } catch (e) {
                                        console.log("Could not parse error response");
                                    }

                                    // SweetAlert Error
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Oops!',
                                            html: errorMsg,
                                            toast: true,
                                            position: 'top-end',
                                            timer: 4000,
                                            showConfirmButton: false
                                        });
                                    } else {
                                        alert(errorMsg);
                                    }
                                }
                            });
                        });

                        // RESET ON CLOSE
                        $('#comboQuickViewModal').on('hidden.bs.modal', function() {
                            currentComboId = 0;
                            currentComboPrice = 0;
                            currentComboRegularPrice = 0;
                            currentQuantity = 1;
                            currentComboProducts = [];

                            $('#comboMainImage').attr("src", "");
                            $('#comboThumb1').attr("src", "");
                            $('#comboItemFieldsContainer').empty();

                            $('#comboVariationAlert').addClass('d-none');
                            $('.add-tocart-btn').prop('disabled', false)
                                .html('<i class="fa-solid fa-cart-plus me-1"></i> Add To Cart');

                            $('#comboPartnerMessage').hide(); // FIXED: Changed to #comboPartnerMessage
                        });
                    });
                </script>
            @endsection
