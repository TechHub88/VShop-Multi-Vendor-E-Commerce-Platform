@extends('frontend.layout')

@section('content')
<main>
<div class="innerPage-app-header">
<div class="container">
<div class="d-flex justify-content-between innerPage-app-headerinner">
<div class="w-100 d-flex leftPart">
<a href="#" class="d-inline-flex me-2 backBtn"></a>
<div class="w-100 leftContent">
<div class="w-100 d-flex delivery-bar" data-bs-toggle="modal" data-bs-target="#chooseAddressModal">
  <div class="w-100 d-flex delivery-left">
    <span class="location-icon">📍</span>
    <div class="delivery-text">
      <div class="delivery-title">
        Delivery to: <strong>700091 - Godrej Genesis Building...</strong>
      </div>
      <div class="delivery-sub">
        Get by <strong>9th January, 2026</strong> at <strong>700091</strong>
      </div>
    </div>
  </div>
  <div class="delivery-arrow">▼</div>
</div>
</div>
</div>

<div class="rightPart">

</div>
</div>
</div>
</div>

<!-- Choose Address Modal -->
<div class="modal customeAppModal chooseAddressModal" id="chooseAddressModal">
  <div class="modal-dialog customeAppModalDiolog">
    <div class="modal-content customeAppModalContent">      
      <div class="modal-header customeAppModalHeader">
        <h4 class="modal-title">Saved Address</h4>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>     
      <div class="modal-body customeAppModalBody">
	  <div class="chooseAddressArea">
       <div class="choose-address-wrapper">
		  <label class="choose-address-card">
			<input type="radio" name="address" checked>
			<div class="choose-address-content">
			  <p class="para">
				Godrej Genesis Building, Salt Lake, Sector V Near College More,
				Kolkata, Sector V, Bidhannagar, Kolkata, North 24 Parganas,
				Kolkata, West Bengal 700091
			  </p>
			  <a href="#" class="edit-link">Edit</a>
			</div>
		  </label>
		  <label class="choose-address-card">
			<input type="radio" name="address">
			<div class="choose-address-content">
			  <h4 class="title">TAMAL BAGCHI</h4>
			  <p class="para">
				BH 19/C Tirupati Tirumala Apartment,
				Vidyasagarpally, Kolkata, West Bengal
			  </p>
			</div>
		  </label>		
		</div>
		<div class="choose-add-address-btn-area">	
		  <button class="btn btn-primary choose-add-address-btn" data-bs-toggle="modal" data-bs-target="#addAdressModal">
			<span>+</span> Add New Address
		  </button>
		</div>
		
		</div>
		
      </div>
    </div>
  </div>
</div>

							<!-- Add Address Modal -->
							<div class="modal customeAppModal addAdressModal" id="addAdressModal">
							  <div class="modal-dialog customeAppModalDiolog addAdressModalDiolog">
								<div class="modal-content customeAppModalContent addAdressModalContent">

								  <!-- Modal Header -->
								  <div class="modal-header customeAppModalHeader addAdressModalHedear">
									<h4 class="modal-title">Add New Address</h4>
									<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
								  </div>		
									<form action="{{ route('frontend.add-address.store') }}" method="POST" id="addressForm">
									<div class="modal-body customeAppModalBody addAdressModalBody">
										@csrf
										<input type="hidden" name="user_id" value="{{ auth('customer')->id() }}">
										<input type="hidden" name="address_id" id="addressId" value="">
										
										<div class="address-add-form">
											<div class="address-add-from-inner">
												<div class="row rowBox">
													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="name">Name <span class="text-danger">*</span></label>
															<input type="text" name="name" id="name" required value="{{ old('name') }}">
															@error('name') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="email">Email Address</label>
															<input type="email" name="email" id="email" value="{{ old('email') }}">
															@error('email') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="company_name">Company Name</label>
															<input type="text" name="company_name" id="company_name" value="{{ old('company_name') }}">
															@error('company_name') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="gstin">GSTIN Number</label>
															<input type="text" name="gstin" id="gstin" value="{{ old('gstin') }}">
															@error('gstin') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="phone">Contact Number <span class="text-danger">*</span></label>
															<input type="tel" name="phone" id="phone" required value="{{ old('phone') }}">
															@error('phone') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="country">Country <span class="text-danger">*</span></label>
															<select name="country" id="country" class="form-select" required>
																<option value="">Select Country</option>
																<option value="India">India</option>
															</select>
															@error('country') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="state">State <span class="text-danger">*</span></label>
															<select name="state" id="state" class="form-select" required>
																<option value="">Select State</option>
															</select>
															@error('state') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="city">City / District <span class="text-danger">*</span></label>
															<select name="city" id="city" class="form-select" required>
																<option value="">Select City</option>
															</select>
															@error('city') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="address_line1">Street Address <span class="text-danger">*</span></label>
															<input type="text" name="address_line1" id="address_line1" required value="{{ old('address_line1') }}">
															@error('address_line1') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="pincode">Pincode <span class="text-danger">*</span></label>
															<input type="text" name="pincode" id="pincode" required value="{{ old('pincode') }}">
															@error('pincode') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>

													<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox address-form-box">
														<div class="form-group">
															<label for="address_type">Address Type <span class="text-danger">*</span></label>
															<select name="address_type" id="address_type" class="form-select" required>
																<option value="">Select Type</option>
																<option value="shipping">Shipping Address</option>
																<option value="billing">Billing Address</option>
																<option value="both">Both Shipping & Billing</option>
															</select>
															@error('address_type') <small class="text-danger">{{ $message }}</small> @enderror
														</div>
													</div>
												</div>
											</div>
										</div>
										</div>	
										<div class="modal-footer customeAppModalFooter">
										<div class="address-add-btn-sec">
													<button class="add-tocart-btn" type="submit" id="submitBtn">
														<i class="fa-solid fa-plus"></i> Add Address
													</button>

													<button type="button" class="shopping-btn reset-btn" onclick="resetForm()">Reset</button>

													<button type="button" class="btn btn-outline-secondary" id="cancelEditBtn" style="display:none;">
														<i class="fa-solid fa-times me-2"></i> Cancel Edit
													</button>
												</div>
										</div>
									</form>
								  </div>							  

								</div>
							  </div>
							



<section class="bedcum-sec" style="padding-top: 120px;">
        <div class="container">
            <div class="bedcum-inner">    
                <div class="bed-cum-list">
                    <ul>
                        <li><a href="#">Shop</a></li>
                        <li><i class="fa-regular fa-angle-right"></i></li>
                        <li><span>Cart</span></li>
                    </ul>
                </div>
            </div>
        </div>
    </section>
	
	<div class="proCartSec">
	<div class="container">
	<div class="proCartSecinner">
	<div class="address-wrapper rowBox">
	<div class="address-left cartRightSideColumn">
	<div class="proCartLeftDtlsArea">
	

    <section class="cart-details-sec">
        <div class="container">
            @if($cartItems->count() > 0)
            <ul>
                @foreach($cartItems as $item)
                @php
                    // Parse JSON attributes
                    $mainProductAttributes = $item->main_product_attributes ? json_decode($item->main_product_attributes, true) : [];
                    $comboProductAttributes = $item->combo_product_attributes ? json_decode($item->combo_product_attributes, true) : [];
                    
                    // Get combo product if combo_id exists
                    $comboProduct = null;
                    if ($item->combo_id) {
                        $comboProduct = \App\Models\Product::find($item->combo_id);
                    }
                    
                    // Calculate prices properly
                    if($item->combo_id && $comboProduct) {
                        // Combo product: use new columns
                        $mainProductPrice = $item->main_product_price ?? 0;
                        $mainProductQuantity = $item->main_product_quantity ?? 1;
                        $comboProductPrice = $item->combo_product_price ?? 0;
                        $comboProductQuantity = $item->combo_product_quantity ?? 1;
                        
                        $mainProductTotal = $mainProductPrice * $mainProductQuantity;
                        $comboProductTotal = $comboProductPrice * $comboProductQuantity;
                        $unitPrice = $mainProductPrice + $comboProductPrice; // Combined unit price
                        $totalUnitPrice = $mainProductTotal + $comboProductTotal;
                        $subtotal = $totalUnitPrice + ($totalUnitPrice * ($item->gst_percent ?? 0)/100) + ($item->courier_charge ?? 0);
                    } else {
                        // Individual product: use main_product_price and main_product_quantity
                        $unitPrice = $item->main_product_price ?? 0;
                        $quantity = $item->main_product_quantity ?? 1;
                        $totalUnitPrice = $unitPrice * $quantity;
                        $gstPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                        $gstAmount = $totalUnitPrice * ($gstPercent / 100);
                        $courierCharge = $item->courier_charge ?? 0;
                        $subtotal = $totalUnitPrice + $gstAmount + $courierCharge;
                    }
                @endphp
                
                <li class="cart-details-list-box" data-id="{{ $item->id }}" data-combo-id="{{ $item->combo_id }}" data-is-combo="{{ $item->combo_id ? 'true' : 'false' }}" data-unit-price="{{ $unitPrice }}" data-gst-percent="{{ $item->gst_percent ?? 5 }}" data-courier-charge="{{ $item->courier_charge ?? 0 }}" data-main-product-price="{{ $item->main_product_price ?? 0 }}" data-combo-product-price="{{ $item->combo_product_price ?? 0 }}" data-main-product-quantity="{{ $item->main_product_quantity ?? 1 }}" data-combo-product-quantity="{{ $item->combo_product_quantity ?? 1 }}" data-price="{{ $item->main_product_price ?? 0 }}" data-quantity="{{ $item->main_product_quantity ?? 1 }}" data-embroidery-total-price="{{ $item->embroidery_total_price ?? 0 }}">
                    <div class="d-block cart-details-list">
                        <div class="d-block cart-details-list-left">
                          <button type="button" class="delite-btn delete-cart" data-cart-item-id="{{ $item->id }}" data-product-name="{{ $item->product?->name ?? 'Product' }}">
        <i class="fa-solid fa-trash"></i>
    </button>
                            
                            {{-- MAIN PRODUCT DISPLAY --}}
                            <div class="main-product-display">
                                <div class="cart-img mb-2">
                                    <a href="{{ route('frontend.productdetails', $item->product->sku) }}">
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

                                    {{-- Main Product Attributes from JSON --}}
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
                                        
                                        // Fallback to legacy color_id/size_id if JSON not available
                                        if (!$mainColor && $item->color) {
                                            $mainColor = $item->color->term;
                                        }
                                        if (!$mainSize && $item->size) {
                                            $mainSize = $item->size->term;
                                        }
                                    @endphp
                                    
                                    {{-- Color Selection --}}
                                    @if($mainColor)
                                    <div class="color-bottom">
                                     <div class="color-right ms-0">
                                        <p class="mt-0 mb-0 pb-0 d-inline-flex colorMention">
                                            <span class="ttl">Color Name: </span>
                                            <span class="blackColor" id="selectedColorName_{{ $item->id }}">
                                               <span class="colorIcon {{ $mainColor }}"> </span>
											   <span class="colorText {{ $mainColor }}"> {{ $mainColor }}</span>
                                            </span>
                                        </p>
                                    </div>
                                    </div>
                                    @endif
									
									{{-- Dynamic Size Selection with Quantities --}}
									@php
										// Get product attributes for this item
										$itemAttributes = $productAttributesMap[$item->product_id] ?? [];
										
										// Find size attribute
										$sizeAttribute = null;
										foreach ($itemAttributes as $attr) {
											if (in_array($attr['attribute_slug'], ['size'])) {
												$sizeAttribute = $attr;
												break;
											}
										}
										
										// Get existing sizes from cart_item_sizes
										$existingSizes = [];
										if ($item->sizes && $item->sizes->count() > 0) {
											foreach ($item->sizes as $sizeItem) {
												$existingSizes[$sizeItem->size_id] = $sizeItem->quantity;
											}
										}
										
										// Check for embroidery/logo in attributes
										$hasEmbroidery = false;
										$logoImage = null;
										foreach ($mainProductAttributes as $attr) {
											$slug = $attr['attribute_slug'] ?? '';
											if (in_array($slug, ['embroidery', 'logo']) || 
												(isset($attr['term_name']) && stripos($attr['term_name'], 'logo') !== false)) {
												$hasEmbroidery = true;
												// Try to get logo image from term or attribute
												if (isset($attr['term_name'])) {
													$logoImage = $attr['term_name'];
												}
												break;
											}
										}
									@endphp
									
									@if($sizeAttribute && !empty($sizeAttribute['terms']))
									<div class="selectSizeListArea mt-2 mb-2">
										<div class="mb-2 d-flex justify-content-between attributeHeadingSec">
											<div class="leftPart">
												<p class="mb-0 blackColor">Select Size:</p>
											</div>
										</div>
										<div class="selectSizeList">
											@foreach($sizeAttribute['terms'] as $term)
												@php
													$sizeId = $term['term_id'];
													$sizeName = $term['term_name'];
													$existingQty = $existingSizes[$sizeId] ?? 0;
												@endphp
												<div class="selectSizeListItem">
													<input class="radioInput" type="radio" name="productSize_{{ $item->id }}" 
														id="size_{{ $item->id }}_{{ $sizeId }}" 
														value="{{ $sizeId }}"
														data-cart-item-id="{{ $item->id }}"
														data-size-id="{{ $sizeId }}"
														{{ $existingQty > 0 ? 'checked' : '' }}>
													<label class="selectSizeListIteminner" for="size_{{ $item->id }}_{{ $sizeId }}">
														<div class="sizeName">{{ $sizeName }}</div>
														<input type="number" min="0" value="{{ $existingQty }}" 
															step="1" inputmode="numeric" pattern="[0-9]*" 
															class="qtyInput cart-size-qty" 
															data-cart-item-id="{{ $item->id }}"
															data-size-id="{{ $sizeId }}"
															placeholder="0">
													</label>
												</div>
											@endforeach
										</div>
									</div>
									@endif
									
									{{-- Fabric Display --}}
									@if($mainFabric)
									<div class="mt-2 mb-2">
										<p class="mb-0"><strong>Fabric:</strong> <span class="blackColor">{{ $mainFabric }}</span></p>
									</div>
									@endif
									
									{{-- Embroidery/Logo Display - Show if embroidery_logo exists in cart item --}}
									@if($item->embroidery_logo)
									<div class="cartEmroideryArea mt-2">
										<h5 class="ttl" style="font-size: 14px;"><strong>Embroidery :</strong> YES</h5>
										<div class="d-flex align-items-center cartEmroideryArea">
											<label class="labelName" style="font-size: 14px;font-weight: 600;">Logo :</label>
											<div class="cartEmroideryThumnail" style="width:64px;">
												@php
													$logoPath = $item->embroidery_logo;
													$logoUrl = $logoPath ? asset('storage/' . $logoPath) : null;
												@endphp
												@if($logoUrl && file_exists(public_path('storage/' . $logoPath)))
													<img src="{{ $logoUrl }}" class="img-fluid" alt="Logo" style="width: 100%; height: 100%; object-fit:contain; border-radius:6px;">
												@else
													<img src="{{ asset($item->product->thumbnail ?? 'images/default-product.png') }}" class="img-fluid" alt="Logo" style="width: 100%; height: 100%; object-fit:contain; border-radius:6px;">
												@endif
											</div>
										</div>
									</div>
									@endif
									

                                    {{-- Size Selection --}}
                                    <!--<div class="size-delite-sec d-flex align-items-center mt-0">
                                        @if($mainSize)
                                        <div class="proSize"><strong>Size</strong> : {{ $mainSize }}</div>
                                        @endif
                                        
                                        @if($mainFabric)
                                        <div class="proSize ms-2"><strong>Fabric</strong> : {{ $mainFabric }}</div>
                                        @endif

                                       {{-- Quantity (Plus/Minus Only) --}}
                                    <div class="product-quantity d-flex align-items-center" data-id="{{ $item->id }}">
                                        <button type="button" class="sub update-qty"><i class="fa-light fa-minus"></i></button>
                                        <span class="count qty-display">
                                            {{ $item->main_product_quantity ?? 1 }}
                                        </span>
                                        <button type="button" class="add update-qty"><i class="fa-light fa-plus"></i></button>
                                    </div>

                                        
                                    </div>-->
									
								<div>	
                                </div>
                            </div>
                            
							
						        <div class="rightSide">
                                        </div>

							
                                            </div>

                                {{-- COMBO PRODUCT DISPLAY - Below Main Product --}}
                            @if($item->combo_id && $comboProduct)
                            <div class="combo-product-display mt-4" style="margin-left: 0; padding-left: 0;">
                                {{-- COMBO PACKAGE Badge --}}
                                <div class="mb-2">
                                    <span class="badge bg-warning text-dark" style="font-size: 12px; padding: 6px 12px; font-weight: 600; border-radius: 4px;">COMBO PACKAGE</span>
                                </div>
                                
                                {{-- Combo Product Card --}}
                                <div class="combo-product-card" style="background-color: #f5f5f5; border-radius: 8px; padding: 15px; margin-top: 10px;">
                                    <div class="d-flex align-items-center" style="gap: 15px;">
                                        {{-- Combo Product Image (Circular) --}}
                                        <div class="combo-img-wrapper" style="flex-shrink: 0;">
                                            <a href="{{ route('frontend.productdetails', $comboProduct->sku) }}">
                                                <img src="{{ $comboProduct->thumbnail ? asset($comboProduct->thumbnail) : asset('images/default-product.png') }}"
                                                     class="img-fluid rounded-circle"
                                                     alt="{{ $comboProduct->name ?? 'No Image' }}"
                                                     style="width: 70px; height: 70px; object-fit: cover; border: 2px solid #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            </a>
                                        </div>
                                        
                                        {{-- Combo Product Details --}}
                                        <div class="combo-product-details flex-grow-1">
                                            {{-- Combo Product Name --}}
                                            <h6 class="mb-1" style="font-size: 14px; font-weight: 600; color: #000; margin-bottom: 5px !important;">
                                                {{ $comboProduct->name ?? 'Combo Product' }}
                                            </h6>
                                            
                                            {{-- Combo Product Price --}}
                                            <div class="combo-product-price mb-2">
                                                <span class="text-dark fw-bold" style="font-size: 16px; color: #000;">₹{{ number_format($item->combo_product_price ?? 0, 2) }}</span>
                                            </div>
                                            
                                            {{-- Combo Product Attributes from JSON --}}
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
                                            
                                            {{-- Combo Product Attributes Display --}}
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
                                            
                                            {{-- Combo Product Quantity Control --}}
                                            <div class="combo-quantity-control d-flex align-items-center">
                                                <span class="me-2" style="font-size: 12px; color: #666; font-weight: 500;">Qty:</span>
                                                <div class="product-quantity combo-product-quantity d-flex align-items-center" data-id="{{ $item->id }}" data-is-combo="true">
                                                    <button type="button" class="sub update-combo-qty" style="border: 1px solid #ddd; background: #fff; padding: 5px 12px; cursor: pointer; border-radius: 4px 0 0 4px; transition: background-color 0.2s; font-size: 12px;">
                                                        <i class="fa-light fa-minus"></i>
                                                    </button>
                                                    <span class="count combo-qty-display" style=" border-top: 1px solid #ddd; border-bottom: 1px solid #ddd; min-width: 45px; text-align: center; background: #fff; font-weight: 600; font-size: 13px;">
                                                        {{ $item->combo_product_quantity ?? 1 }}
                                                    </span>
                                                    <button type="button" class="add update-combo-qty" style="border: 1px solid #ddd; background: #fff; padding: 5px 12px; cursor: pointer; border-radius: 0 4px 4px 0; transition: background-color 0.2s; font-size: 12px;">
                                                        <i class="fa-light fa-plus"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                                            
                                </div>
                    						
                                        {{-- Price Section --}}
                    
                                            
                    
                                        </div>
					
					 </div>
				 </div>
					
                </li>
                @endforeach
            </ul>

            {{-- Totals --}}
            @php
                $grandTotal = 0;
                $totalUniqueItems = 0;
                $totalQuantity = 0;
                $totalDiscount = 0;
                $appliedCoupon = null;
                $couponAppliedToAll = false;
                
                foreach($cartItems as $item) {
                    if($item->combo_id) {
                        // Combo product: use new columns
                        $mainProductTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
                        $comboProductTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
                        $totalCombinedPrice = $mainProductTotal + $comboProductTotal;
                        
                        // Add embroidery price to subtotal before calculating GST
                        $embroideryPrice = $item->embroidery_total_price ?? 0;
                        $subtotalWithEmbroidery = $totalCombinedPrice + $embroideryPrice;
                        
                        $gstPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                        $courierCharge = $item->courier_charge ?? 0;
                        $gstAmount = $subtotalWithEmbroidery * ($gstPercent / 100);
                        
                        $itemTotal = $subtotalWithEmbroidery + $gstAmount + $courierCharge;
                        
                        // Apply coupon discount if exists
                        if($item->discount_amount) {
                            $itemTotal -= $item->discount_amount;
                            $totalDiscount += $item->discount_amount;
                        }
                        
                        $grandTotal += $itemTotal;
                        $totalUniqueItems++;
                        $totalQuantity += ($item->main_product_quantity ?? 1) + ($item->combo_product_quantity ?? 1);
                        
                        // Check if coupon is applied
                        if($item->coupon_id) {
                            $appliedCoupon = $item;
                        }
                    } else {
                        // Individual product: use main_product_price and main_product_quantity
                        $totalUniqueItems++;
                        $itemPrice = $item->main_product_price ?? 0;
                        $itemQuantity = $item->main_product_quantity ?? 1;
                        $itemSubtotal = $itemPrice * $itemQuantity;
                        
                        // Add embroidery price to subtotal before calculating GST
                        $embroideryPrice = $item->embroidery_total_price ?? 0;
                        $subtotalWithEmbroidery = $itemSubtotal + $embroideryPrice;
                        
                        $gstPercent = $item->gst_percent ?? 5; // Default to 5% if not set
                        $courierCharge = $item->courier_charge ?? 0;
                        $gstAmount = $subtotalWithEmbroidery * ($gstPercent / 100);
                        
                        $itemTotal = $subtotalWithEmbroidery + $gstAmount + $courierCharge;
                        
                        // Apply coupon discount if exists
                        if($item->discount_amount) {
                            $itemTotal -= $item->discount_amount;
                            $totalDiscount += $item->discount_amount;
                        }
                        
                        $grandTotal += $itemTotal;
                        $totalQuantity += $itemQuantity;
                        
                        // Check if coupon is applied
                        if($item->coupon_id) {
                            $appliedCoupon = $item;
                        }
                    }
                }
                
                // Check if all items have the same coupon (coupon applied to all)
                $uniqueCoupons = $cartItems->whereNotNull('coupon_id')->pluck('coupon_code')->unique();
                $couponAppliedToAll = $uniqueCoupons->count() == 1;
            @endphp
            
            <div class="total-sec d-flex justify-content-between mt-0">
                <div class="total-left">
                    <p class="mb-0">Total Items : {{ $totalUniqueItems }}</p>
                    <p class="mb-0">Total Quantity : {{ $totalQuantity }}</p>
                </div>
                <div class="cart-price-sec">
                    <div class="cart-peice-box border0">
                        <ul>
                            <li class="mb-0">
                                <p class="me-2 subColor">Total :</p>
                                <span class="rupess grand-total">₹{{ number_format($grandTotal, 2) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

           
        </div>
    </section>
	
	</div>
	</div>
	<div class="address-right cartRightSideColumn">
	<div class="cartRightSideAreainner">
	
	<!-- Apply Coupons Section -->
	<div class="coupon-section mb-3" style="">
		<div class="applyCopunBox">
		<div class="d-flex align-items-center justify-content-between applyCopunBoxinner">
			<div class="d-flex align-items-center applyCopunInputGroup" style="">
				<iconify-icon icon="solar:ticket-bold-duotone" class="ticketIcon" style="font-size: 24px; color: #6f42c1;"></iconify-icon>
				<a href="javascript:void(0);" class="apply-coupon-link" data-bs-toggle="modal" data-bs-target="#applyCouponModal" style="">
					Apply Coupons
				</a>
			</div>
			<!--<button type="button" class="btn apply-coupon-btn" data-bs-toggle="modal" data-bs-target="#applyCouponModal" style="">
				APPLY
			</button>-->
			<button type="button" class="btn apply-coupon-btn" data-bs-toggle="modal" data-bs-target="#applyCouponCodeModal" style="">
				APPLY
			</button>
			
			
		</div>
		</div>
		
		<!-- Apply Coupon Code Modal -->
		<div class="modal customeAppModal applyCouponCodeModal" id="applyCouponCodeModal">
		  <div class="modal-dialog customeAppModalDiolog">
			<div class="modal-content customeAppModalContent">			  
			  <div class="modal-header customeAppModalHeader">
				<h4 class="modal-title">Apply Coupon</h4>
				<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
			  </div>			  
			  <div class="modal-body customeAppModalBody">
				<div class="applyCouponCodeArea">
                <div class="applyCoupo-cart-box">
                  <p class="cart-text">Your cart: <strong>₹{{ number_format($grandTotal ?? 0, 2) }}</strong></p>
				  <div class="coupon-box">
					<input type="text" placeholder="Enter Coupon Code">
					<button>APPLY</button>
				  </div>
				</div>		
				  <div class="applyCoupon-card flex">
					<div class="leftPart">
					<span class="badge">PAY2500</span>
					  <h3>PAY 2500 GET 3000</h3>
					<p class="desc">Enjoy hassle-free payments and exclusive offers with your wallet balance!</p>
					<span class="tc">T&C ▼</span>
					</div>
					<button class="btn recharge-btn">Apply</button>
				  </div>				 
				  <div class="applyCoupon-card flex">
					<div class="leftPart">
					  <span class="badge">PAY2500</span>
					  <h3>PAY 2500 GET 3000</h3>
					  <p class="desc">Enjoy hassle-free payments and exclusive offers with your wallet balance!</p>
					  <span class="tc">T&C ▼</span>
					</div>
					<button class="btn recharge-btn">Delete</button>
				  </div>	
				<!--<div class="applyCoupon-btn-area">
				<button class="w-100 btn btn-primary applyCoupon-btn">APPLY</button>
				</div>-->		
				
				</div>
			  </div>	
			</div>
		  </div>
		</div>
		
		@if($appliedCoupon && $couponAppliedToAll)
		<div class="mt-3 applied-coupon-alert" style="background-color: #d4edda; border: 1px solid #c3e6cb; border-radius: 6px; padding: 10px 15px;">
			<div class="d-flex justify-content-between align-items-center">
				<div>
					<strong style="color: #155724;">Coupon Applied: {{ $appliedCoupon->coupon_code }}</strong>
					<p class="mb-0" style="color: #0c5460; font-size: 14px;">You saved: <strong>₹{{ number_format($totalDiscount, 2) }}</strong></p>
				</div>
				<button type="button" class="btn btn-sm btn-outline-danger remove-coupon-btn" style="padding: 2px 10px; font-size: 12px;">
					Remove
				</button>
			</div>
		</div>
		@endif
	</div>
	
	<div class="address-right-top">
	   <h3>Order Summary ({{ $totalUniqueItems }})</h3>
	   
	   @php
	       $totalGST = 0;
	       $totalSubtotal = 0;
	       $totalCourierCharge = 0;
	       $totalEmbroideryPrice = 0;
	       $calculatedTotal = 0;
	       
	       foreach($cartItems as $item) {
	           if($item->combo_id) {
	               $mainProductTotal = ($item->main_product_price ?? 0) * ($item->main_product_quantity ?? 1);
	               $comboProductTotal = ($item->combo_product_price ?? 0) * ($item->combo_product_quantity ?? 1);
	               $itemSubtotal = $mainProductTotal + $comboProductTotal;
	           } else {
	               $itemPrice = $item->main_product_price ?? 0;
	               $itemQuantity = $item->main_product_quantity ?? 1;
	               $itemSubtotal = $itemPrice * $itemQuantity;
	           }
	           $gstPercent = $item->gst_percent ?? 5;
	           $courierCharge = $item->courier_charge ?? 0;
	           
	           // Add embroidery total price
	           $embroideryPrice = $item->embroidery_total_price ?? 0;
	           $totalEmbroideryPrice += $embroideryPrice;
	           
	           // Add embroidery to item subtotal before calculating GST (matching controller logic)
	           $itemSubtotalWithEmbroidery = $itemSubtotal + $embroideryPrice;
	           
	           $totalSubtotal += $itemSubtotal;
	           // GST is calculated on (subtotal + embroidery) to match controller
	           $totalGST += $itemSubtotalWithEmbroidery * ($gstPercent / 100);
	           $totalCourierCharge += $courierCharge;
	           $calculatedTotal += $itemSubtotalWithEmbroidery + ($itemSubtotalWithEmbroidery * ($gstPercent / 100)) + $courierCharge;
	       }
	       
	       // Calculate discount per item if coupon is applied to all
	       $discountPerItem = 0;
	       if($couponAppliedToAll && $appliedCoupon) {
	           $discountPerItem = $totalDiscount / $cartItems->count();
	       }
	       
	       // Subtract discount from calculated total to match grandTotal
	       $calculatedTotal = $calculatedTotal - $totalDiscount;
	   @endphp
	   
	   <div class="address-right-box">
		  <ul>
			 <li>
				<p>Subtotal</p>
				<span>₹{{ number_format($totalSubtotal, 2) }}</span>
			 </li>
			 
			 @if($totalDiscount > 0)
			 <li>
				<p>Coupon Discount</p>
				<span class="text-success">- ₹{{ number_format($totalDiscount, 2) }}</span>
			 </li>
			 @endif
			 
			 @if($totalEmbroideryPrice > 0)
			 <li>
				<p>Total Embroidery</p>
				<span>+ ₹{{ number_format($totalEmbroideryPrice, 2) }}</span>
			 </li>
			 @endif
			 
			 <li>
				<p>GST ({{ $cartItems->first()->gst_percent ?? 5 }}%)</p>
				<span>+ ₹{{ number_format($totalGST, 2) }}</span>
			 </li>
			 
			 @if($totalCourierCharge > 0)
			 <li>
				<p>Shipping</p>
				<span>+ ₹{{ number_format($totalCourierCharge, 2) }}</span>
			 </li>
			 @endif
		  </ul>
	   </div>
	   <div class="address-right-box border0">
		  <ul>
            <li>
				<p><strong>Total</strong></p>
				<span class="total-text"><strong>₹{{ number_format($grandTotal, 2) }}</strong></span>
			 </li>
			 
			 @if($totalDiscount > 0)
			 <li class="text-success" style="border-top: 1px dashed #e0e0e0; padding-top: 10px;">
				<p><strong>You Save</strong></p>
				<span class="total-text"><strong>₹{{ number_format($totalDiscount, 2) }}</strong></span>
			 </li>
			 @endif
		  </ul>
	   </div>
	</div>
	
			<div class="place-btn-sec mt-0">
                <a href="{{ route('frontend.address.index') }}" class="w-100 text-center add-tocart-btn">Proceed to Checkout</a>
                @if(!Auth::guard('customer')->check())
                <a href="" class="w-100 text-center add-tocart-btn mt-3" data-bs-toggle="modal" data-bs-target="#loginModal">Place Order</a>
                @endif
            </div>
			
			<!-- Login Modal -->
			<div class="modal customeAppModal loginModal" id="loginModal">
			  <div class="modal-dialog customeAppModalDiolog">
				<div class="modal-content customeAppModalContent">
				<div class="login-step-one d-none">		
				  <div class="modal-header customeAppModalHeader">
					<h4 class="modal-title">Enter Your Phone Number</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				  </div>			
				  <div class="modal-body customeAppModalBody">
					<div class="login-otp-wrapper">
					<p class="login-otp-info-text">
						You will receive OTP via SMS & WhatsApp
					</p>
					<div class="form-group">
					<label class="input-label">Phone number</label>					
					<div class="login-input-box">
						<span class="country-code">+91</span>
						<input type="tel" class="form-control" placeholder="Enter phone number">
					</div>
					</div>
					<div class="login-otp-btn-area">
					<button class="btn btn-primary login-otp-btn" disabled>Get OTP</button>
					</div>	
					<p class="login-otp-terms">
						By signing in you agree to our
						<a href="#">terms of service</a> and
						<a href="#">privacy policy</a>
					</p>
					</div>
				  </div>
				  </div>
				  
				  <div class="login-step-two">		
				  <div class="modal-header customeAppModalHeader">
					<h4 class="modal-title">Verify Number</h4>
					<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
				  </div>			
				  <div class="modal-body customeAppModalBody">
					<div class="login-otp-wrapper">
					<p class="login-otp-info-text">
						OTP Sent to 8961311235 <a href="#" class="editBtn">Edit</a>
					</p>
					<div class="form-group">
					<label class="input-label">OTP</label>					
					<div class="p-0 login-input-box">						
						<input type="number" class="form-control" placeholder="Enter OTP Number" min="0" max="4">
					</div>
					<div class="mt-1 otpMesage">
					<p class="mb-0"><small><strong>OTP sent via SMS and Whatsapp</strong></small></p>		
					</div>					
					</div>
					<div class="form-group">
					<p class="mb-0">Did't Receive OTP ? <a href="#" class="resendBtn">Resend</a></p>		
					</div>
					<div class="login-otp-btn-area">
					<button class="btn btn-primary login-otp-btn" disabled>Verify Number</button>
					</div>	
					<p class="login-otp-terms">
						By signing in you agree to our
						<a href="#">terms of service</a> and
						<a href="#">privacy policy</a>
					</p>
					</div>
				  </div>
				  </div>

				</div>
			  </div>
			</div>
			
			
			

            @else
            <div class="text-center py-5">
                <h4>Your Cart is Empty 😔</h4>
                <a href="{{ url('/frontend/dashboard') }}" class="w-100 text-center btn btn-outline-primary mt-3 continue-shopping-btn">Continue Shopping</a>
            </div>
            @endif
	

<!-- Apply Coupon Modal -->
<div class="modal fade applyCouponModal cartApplyCouponModal" id="applyCouponModal" tabindex="-1" aria-labelledby="applyCouponModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content applyCouponModalContent" style="position: relative; z-index: 1056;">
            <div class="modal-header applyCouponModalHeader" style="border-bottom: 1px solid #e0e0e0; padding: 20px; position: relative; z-index: 1;">
                <h5 class="modal-title fw-bold" id="applyCouponModalLabel" style="font-size: 18px;">APPLY COUPON</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" style="position: relative; z-index: 10;"></button>
            </div>
            <div class="modal-body applyCouponModalBody" style="padding: 20px; position: relative; z-index: 1;">
                <!-- Coupon Code Input -->
                <div class="d-flex gap-2 mb-4 applyCouponModalInputGroup" style="position: relative; z-index: 10;">
                    <input type="text" class="form-control" id="couponCodeInput" placeholder="Enter coupon code" style="flex: 1; position: relative; z-index: 10;">
                    <button type="button" id="checkCouponBtn" class="btn" style="background: #e91e63; color: white; border: none; padding: 8px 20px; font-weight: 600; position: relative; z-index: 10;">CHECK</button>
                </div>
                
                <div id="couponMessage" class="alert applyCouponModalAlert" style="display: none; margin-bottom: 20px;"></div>
                
                <!-- Available Coupons List -->
                <div id="availableCouponsList" class="availableCouponsList" data-cart-total="{{ $grandTotal }}">
                    @php
                        $cartTotal = $grandTotal; // Use the calculated grand total
                        
                        // Get user info
                        $isGuest = !Auth::guard('customer')->check();
                        $customerStatus = null;
                        $isApprovedCustomer = false;
                        
                        if (!$isGuest) {
                            $customer = Auth::guard('customer')->user();
                            $customerStatus = $customer->status ?? null;
                            $isApprovedCustomer = ($customerStatus === 'approved');
                        }
                        
                        // Filter coupons based on customer status
                        $filteredCoupons = $coupons->filter(function($coupon) use ($isGuest, $isApprovedCustomer) {
                            // Guests can only see public coupons
                            if ($isGuest) {
                                return $coupon->coupon_type === 'public';
                            }
                            
                            // Approved customers can only see partner coupons
                            if ($isApprovedCustomer) {
                                return $coupon->coupon_type === 'partner';
                            }
                            
                            // Non-approved customers can only see public coupons
                            return $coupon->coupon_type === 'public';
                        });
                    @endphp
                    
                    @if($filteredCoupons->count() > 0)
                        @foreach($filteredCoupons as $coupon)
                            @php
                                $isValid = $coupon->isValid();
                                $canApply = $coupon->canApplyTo($cartTotal);
                                $discountAmount = $coupon->calculateDiscount($cartTotal);
                                $shortfall = $coupon->minimum_spend > $cartTotal ? ($coupon->minimum_spend - $cartTotal) : 0;
                                $isPartnerCoupon = $coupon->coupon_type === 'partner';
                            @endphp
                            
                            <div class="coupon-item mb-3" 
                                 data-coupon-id="{{ $coupon->id }}" 
                                 data-discount-amount="{{ $discountAmount }}" 
                                 data-discount-type="{{ $coupon->discount_type }}"
                                 data-coupon-amount="{{ $coupon->coupon_amount }}"
                                 data-coupon-type="{{ $coupon->coupon_type }}"
                                 data-applicable="{{ $isValid && $canApply ? 'true' : 'false' }}" 
                                 style="border: 1px dashed {{ $isValid && $canApply ? ($isPartnerCoupon ? '#28a745' : '#e91e63') : '#ccc' }}; border-radius: 8px; padding: 15px; {{ !$isValid || !$canApply ? 'opacity: 0.6; position: relative;' : '' }}">
                                @if(!$isValid || !$canApply)
                                    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(255,255,255,0.8); z-index: 1; border-radius: 8px; pointer-events: none;"></div>
                                @endif
                                
                                <div class="d-flex align-items-start" style="position: relative; z-index: 10; pointer-events: auto;">
                                    <div class="me-3" style="position: relative; z-index: 100;">
                                        <input type="radio" name="selectedCoupon" value="{{ $coupon->id }}" id="coupon_{{ $coupon->id }}" class="coupon-radio" {{ $isValid && $canApply ? '' : 'disabled' }} style="width: 20px; height: 20px; accent-color: {{ $isPartnerCoupon ? '#28a745' : '#e91e63' }}; cursor: pointer; position: relative; z-index: 100; pointer-events: auto;">
                                    </div>
                                    <div class="flex-grow-1">
                                        <!-- Coupon Type Badge -->
                                        @if($isPartnerCoupon)
                                            <span class="badge bg-success mb-2" style="font-size: 11px; padding: 4px 8px;">PARTNER EXCLUSIVE</span>
                                        @else
                                            <span class="badge bg-primary mb-2" style="font-size: 11px; padding: 4px 8px;">PUBLIC</span>
                                        @endif
                                        
                                        <div class="coupon-code-box mb-2" style="border: 1px dashed {{ $isPartnerCoupon ? '#28a745' : '#e91e63' }}; border-radius: 4px; padding: 10px; display: inline-block; background: #fff;">
                                            <strong style="color: {{ $isPartnerCoupon ? '#28a745' : '#e91e63' }}; font-size: 16px;">{{ $coupon->discount_code }}</strong>
                                        </div>
                                        
                                        @if($isValid && $canApply)
                                            <div class="mb-2">
                                                <strong style="color: {{ $isPartnerCoupon ? '#28a745' : '#e91e63' }};">Save ₹{{ number_format($discountAmount, 2) }}</strong>
                                            </div>
                                        @endif
                                        
                                        <div class="coupon-details mb-2" style="font-size: 14px; color: #666;">
                                            @if($coupon->discount_type === 'percentage')
                                                {{ $coupon->coupon_amount }}% off on minimum purchase of Rs. {{ number_format($coupon->minimum_spend, 2) }}.
                                            @else
                                                Rs. {{ number_format($coupon->coupon_amount, 2) }} off on minimum purchase of Rs. {{ number_format($coupon->minimum_spend, 2) }}.
                                            @endif
                                        </div>
                                        
                                        <div class="coupon-expiry mb-2" style="font-size: 12px; color: #999;">
                                            Expires on: {{ $coupon->expiry_date->format('jS F Y') }} | {{ $coupon->expiry_date->format('h:i A') }}
                                        </div>
                                        
                                        @if(!$canApply && $shortfall > 0)
                                            <div class="text-danger" style="font-size: 13px;">
                                                Shop for Rs. {{ number_format($shortfall, 2) }} more to apply.
                                            </div>
                                        @elseif(!$isValid)
                                            <div class="text-danger" style="font-size: 13px;">
                                                @if($coupon->status !== 'active')
                                                    This coupon is currently inactive.
                                                @elseif($coupon->expiry_date < now())
                                                    This coupon has expired.
                                                @elseif($coupon->usage_limit && $coupon->used_count >= $coupon->usage_limit)
                                                    This coupon has reached its usage limit.
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-4 text-muted">
                            @if($isGuest)
                                <p>No public coupons available at the moment.</p>
                                <p class="small">Login to see more coupon options.</p>
                            @elseif($isApprovedCustomer)
                                <p>No partner coupons available at the moment.</p>
                                <p class="small">Contact support for partner coupon availability.</p>
                            @else
                                <p>No public coupons available at the moment.</p>
                                <p class="small">Get your account approved to access partner coupons.</p>
                            @endif
                        </div>
                    @endif
                </div>
                
                <!-- Customer Status Info -->
                @if(Auth::guard('customer')->check())
                    <div class="user-status-info mt-3 p-3" style="background-color: #f8f9fa; border-radius: 6px; border-left: 4px solid {{ $isApprovedCustomer ? '#28a745' : '#ffc107' }};">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                @if($isApprovedCustomer)
                                    <i class="fa-solid fa-check-circle text-success" style="font-size: 20px;"></i>
                                @else
                                    <i class="fa-solid fa-clock text-warning" style="font-size: 20px;"></i>
                                @endif
                            </div>
                            <div>
                                <p class="mb-1" style="font-size: 14px; font-weight: 600;">
                                    {{ Auth::guard('customer')->user()->name }}
                                    @if($isApprovedCustomer)
                                        <span class="badge bg-success ms-2">Approved</span>
                                    @else
                                        <span class="badge bg-warning text-dark ms-2">
                                            {{ ucfirst($customerStatus ?? 'Pending') }}
                                        </span>
                                    @endif
                                </p>
                                <p class="mb-0" style="font-size: 12px; color: #666;">
                                    @if($isApprovedCustomer)
                                        ✓ Approved account - You can apply partner coupons only
                                    @else
                                        ⓘ {{ ucfirst($customerStatus ?? 'Pending') }} account - You can apply public coupons only
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="user-status-info mt-3 p-3" style="background-color: #f8f9fa; border-radius: 6px; border-left: 4px solid #6c757d;">
                        <div class="d-flex align-items-center">
                            <div class="me-3">
                                <i class="fa-solid fa-user text-secondary" style="font-size: 20px;"></i>
                            </div>
                            <div>
                                <p class="mb-1" style="font-size: 14px; font-weight: 600;">
                                    Guest User
                                    <span class="badge bg-secondary ms-2">Guest</span>
                                </p>
                                <p class="mb-0" style="font-size: 12px; color: #666;">
                                    ⓘ You can only apply public coupons. 
                                    <a href="{{ route('frontend.customer.login') }}" class="text-primary text-decoration-none">Login</a> or 
                                    <a href="{{ route('frontend.customer.register') }}" class="text-primary text-decoration-none">Register</a> to access partner coupons.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Maximum Savings and Apply Button -->
                <div class="d-flex justify-content-between align-items-center mt-4 pt-3" style="border-top: 1px solid #e0e0e0; position: relative; z-index: 10;">
                    <div>
                        <strong>Maximum savings: <span id="maxSavings" style="color: #e91e63;">₹0.00</span></strong>
                    </div>
                    <button type="button" id="applySelectedCouponBtn" class="btn" style="background: #e91e63; color: white; border: none; padding: 10px 30px; font-weight: 600; font-size: 16px; position: relative; z-index: 10; cursor: pointer;">
                        APPLY
                    </button>
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
	
	
	
	
</main>

<style>
    /* Combo Product Display Styling */
    .combo-product-display {
        margin-top: 20px;
        padding-top: 15px;
        border-top: 1px solid #e0e0e0;
    }
    
    .combo-product-card {
        background-color: #f5f5f5;
        border-radius: 8px;
        padding: 15px;
        transition: box-shadow 0.2s;
    }
    
    .combo-product-card:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .combo-product-display .update-combo-qty:hover {
        background-color: #f0f0f0 !important;
    }
    
    .combo-product-display .update-combo-qty:active {
        background-color: #e0e0e0 !important;
    }
    
    /* Ensure combo package is properly aligned under main product */
    .main-product-display + .combo-product-display {
        margin-left: 0;
        margin-top: 20px;
    }
    
    /* Circular image styling */
    .combo-img-wrapper img {
        transition: transform 0.2s;
    }
    
    .combo-img-wrapper:hover img {
        transform: scale(1.05);
    }
    
    /* Coupon Modal Styling */
    .coupon-item {
        transition: all 0.3s ease;
    }
    
    .coupon-item:hover {
        box-shadow: 0 2px 8px rgba(233, 30, 99, 0.1);
    }
    
    .coupon-radio:checked + .flex-grow-1 .coupon-code-box {
        background: #fff5f8;
        border-color: #e91e63;
        border-width: 2px;
    }
    
    .coupon-code-box {
        transition: all 0.2s ease;
    }
    
    
    
    /* Ensure footer stays below modal */
    footer.web-footer {
        z-index: 1 !important;
        position: relative;
    }
    
    /* Fix overlay blocking clicks */
    .coupon-item .d-flex {
        pointer-events: auto !important;
    }
    
    .coupon-radio {
        pointer-events: auto !important;
        position: relative;
        z-index: 100;
    }
    
    /* Ensure all interactive elements are clickable */
    #applyCouponModal input,
    #applyCouponModal button,
    #applyCouponModal .coupon-radio,
    #applyCouponModal .btn-close {
        pointer-events: auto !important;
        position: relative;
        z-index: 1000 !important;
        cursor: pointer !important;
    }
    
    /* Ensure modal body is interactive */
    #applyCouponModal .modal-body {
        pointer-events: auto !important;
    }
    
    /* Ensure modal header is interactive */
    #applyCouponModal .modal-header {
        pointer-events: auto !important;
    }
    
    /* Remove pointer events from backdrop only */
    body.modal-open .modal-backdrop {
        pointer-events: none !important;
    }
</style>

<!-- Add SweetAlert CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<!-- Toastr CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>

<!-- jQuery is now loaded in the head section of layout.blade.php -->
<!-- Add SweetAlert JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>



<script>
$(document).ready(function() {

    // -------------- STATE → CITY DATA -----------------
    const indiaStateCityMap = {
        "Andhra Pradesh":["Visakhapatnam","Vijayawada","Guntur"],
        "Arunachal Pradesh":["Itanagar","Tawang"],
        "Assam":["Guwahati","Jorhat"],
        "Bihar":["Patna","Gaya"],
        "Chhattisgarh":["Raipur","Bilaspur"],
        "Goa":["Panaji","Margao"],
        "Gujarat":["Ahmedabad","Surat"],
        "Haryana":["Gurugram","Faridabad"],
        "Himachal Pradesh":["Shimla","Manali"],
        "Jharkhand":["Ranchi","Jamshedpur"],
        "Karnataka":["Bengaluru","Mysore"],
        "Kerala":["Kochi","Kozhikode"],
        "Madhya Pradesh":["Indore","Bhopal"],
        "Maharashtra":["Mumbai","Pune","Nagpur"],
        "Manipur":["Imphal"],
        "Meghalaya":["Shillong"],
        "Mizoram":["Aizawl"],
        "Nagaland":["Kohima"],
        "Odisha":["Bhubaneswar","Cuttack"],
        "Punjab":["Amritsar","Ludhiana"],
        "Rajasthan":["Jaipur","Udaipur"],
        "Sikkim":["Gangtok"],
        "Tamil Nadu":["Chennai","Coimbatore"],
        "Telangana":["Hyderabad","Warangal"],
        "Tripura":["Agartala"],
        "Uttar Pradesh":["Lucknow","Kanpur","Noida"],
        "Uttarakhand":["Dehradun","Haridwar"],
        "West Bengal":["Kolkata","Howrah"],
        "Delhi":["New Delhi"],
        "Jammu & Kashmir":["Srinagar","Jammu"],
        "Ladakh":["Leh"],
        "Chandigarh":["Chandigarh"]
    };

    // Populate States
    $.each(indiaStateCityMap, function(state) {
        $("#state").append(`<option value="${state}">${state}</option>`);
    });

    // On State Change → Load Cities
    $("#state").on("change", function() {
        let state = $(this).val();
        let cities = indiaStateCityMap[state] || [];
        $("#city").empty().append('<option value="">Select City</option>');
        $.each(cities, function(i, city) {
            $("#city").append(`<option value="${city}">${city}</option>`);
        });
    });

    // ---------------------- EDIT BUTTON FUNCTION ----------------------
    $(document).on("click", ".edit-address-btn", function () {

        $("#formTitle").text("Edit Address");
        $("#submitBtn").html('Update Address');
        $("#cancelEditBtn").show();

        $("#addressId").val($(this).data("id"));
        $("#name").val($(this).data("name"));
        $("#email").val($(this).data("email"));
        $("#company_name").val($(this).data("company"));
        $("#gstin").val($(this).data("gstin"));
        $("#phone").val($(this).data("phone"));
        $("#country").val($(this).data("country"));

        let state = $(this).data("state");
        let city = $(this).data("city");

        $("#state").val(state).trigger("change");

        setTimeout(function () {
            $("#city").val(city);
        }, 200);

        $("#address_line1").val($(this).data("address"));
        $("#pincode").val($(this).data("pincode"));
        $("#address_type").val($(this).data("address_type"));

        $('html, body').animate({
            scrollTop: $(".address-form-wrapper").offset().top - 120
        }, 400);
    });

    // ---------------------- CANCEL EDIT ----------------------
    $("#cancelEditBtn").on("click", function () {
        resetForm();
    });

});

// Reset Form Function
function resetForm() {
    $("#addressForm")[0].reset();
    $("#addressId").val("");
    $("#formTitle").text("Add New Address");
    $("#submitBtn").html('<i class="fa-solid fa-plus"></i> Add Address');
    $("#cancelEditBtn").hide();
}
</script>



<script>
$(document).ready(function(){

    // Function to format number with commas (like PHP number_format)
    function formatNumber(num) {
        var numStr = parseFloat(num).toFixed(2);
        var parts = numStr.split('.');
        parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        return parts.join('.');
    }
    
    // Function to format currency with rupee symbol
    function formatCurrency(num) {
        return '₹' + formatNumber(num);
    }

    // Increment / Decrement Quantity for Main Product
    $(document).on('click', '.update-qty', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        var row = $(this).closest('li.cart-details-list-box');
        var id = row.data('id');
        var isCombo = row.data('is-combo') === 'true';
        var comboId = row.data('combo-id');
        var action = $(this).hasClass('add') ? 'increment' : 'decrement';
        
        // Get current quantity
        var qtySpan = row.find('.qty-display');
        var currentQty = parseInt(qtySpan.text()) || 1;
        
        // Prevent going below 1
        if (action === 'decrement' && currentQty <= 1) {
            return;
        }
  
        // Prepare data object
        var ajaxData = {
            _token: "{{ csrf_token() }}",
            id: id,
            action: action,
            is_combo: isCombo,
            combo_id: comboId,
            update_type: 'main'
        };
        
        $.ajax({
            url: "{{ route('frontend.cart.ajaxUpdateQuantity') }}",
            type: "POST",
            data: ajaxData,
            success: function(res){
                console.log('AJAX Response (Main):', res);
                if(res.status === 'success'){
                    if(isCombo && comboId) {
                        // Update combo product display (main product quantity changed)
                        updateComboCartItemDisplay(row, res, res.subtotal);
                    } else {
                        // For individual products - use main_product_quantity
                        var mainQty = res.main_product_quantity || res.quantity || 1;
                        updateCartItemDisplay(row, mainQty, res.subtotal, res);
                    }
                    
                    // Update grand total from server response
                    if(res.grand_total !== undefined) {
                        updateGrandTotalFromServer(
                            res.grand_total, 
                            res.total_items, 
                            res.total_quantity,
                            res.total_subtotal,
                            res.total_gst,
                            res.total_courier_charge
                        );
                    } else {
                        updateGrandTotal();
                    }
                } else if(res.status === 'removed'){
                    row.remove();
                    updateGrandTotal();
                    
                    // Check if cart is empty
                    if($('li.cart-details-list-box').length === 0) {
                        location.reload();
                    }
                }
            },
            error: function(xhr, status, error){
                console.error('AJAX Error (Main):', error);
                console.error('Response:', xhr.responseJSON);
                console.error('Request Data:', ajaxData);
                alert('Unable to update item. Please try again.');
            }
        });
    });

    // Increment / Decrement Quantity for Combo Product
    $(document).on('click', '.update-combo-qty', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        var row = $(this).closest('li.cart-details-list-box');
        var id = row.data('id');
        var isCombo = row.data('is-combo') === 'true';
        var comboId = row.data('combo-id');
        var action = $(this).hasClass('add') ? 'increment' : 'decrement';

        $.ajax({
            url: "{{ route('frontend.cart.ajaxUpdateQuantity') }}",
            type: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: id,
                action: action,
                is_combo: isCombo,
                combo_id: comboId,
                update_type: 'combo' // Only update combo product quantity
            },
            success: function(res){
                console.log('AJAX Response (Combo):', res);
                if(res.status === 'success'){
                    // Update combo product display (combo product quantity changed)
                    updateComboCartItemDisplay(row, res, res.subtotal);
                    
                    // Update grand total from server response
                    if(res.grand_total !== undefined) {
                        updateGrandTotalFromServer(
                            res.grand_total, 
                            res.total_items, 
                            res.total_quantity,
                            res.total_subtotal,
                            res.total_gst,
                            res.total_courier_charge
                        );
                    } else {
                        updateGrandTotal();
                    }
                } else if(res.status === 'removed'){
                    row.remove();
                    updateGrandTotal();
                    
                    // Check if cart is empty
                    if($('li.cart-details-list-box').length === 0) {
                        location.reload();
                    }
                }
            },
            error: function(xhr, status, error){
                console.error('AJAX Error (Combo):', error);
                console.error('Response:', xhr.responseJSON);
                alert('Unable to update combo item. Please try again.');
            }
        });
    });

    // Function to update cart item display for individual products
    function updateCartItemDisplay(row, quantity, subtotal, res) {
        // Update quantity display
        var qtyEl = row.find('.qty-display');
        if(qtyEl.length) {
            qtyEl.text(quantity);
        }
        
        // Update data attributes - use main_product_quantity and main_product_price
        row.data('main-product-quantity', quantity);
        if(res && res.main_product_price !== undefined) {
            row.data('main-product-price', res.main_product_price);
            row.data('unit-price', res.main_product_price); // Also update unit-price for compatibility
        }
        
        // Get price data from data attributes - use main_product_price
        var mainPrice = parseFloat(row.data('main-product-price') || row.data('unit-price') || 0);
        
        // Calculate total unit price
        var totalUnitPrice = mainPrice * quantity;
        
        // Update unit price display if element exists
        var unitPriceEl = row.find('.unit-price');
        if(unitPriceEl.length) {
            var unitPriceHtml = formatCurrency(mainPrice);
            if(quantity > 1) {
                unitPriceHtml += '<small class="text-muted" style="font-size: 12px;font-weight: 500;">× ' + quantity + ' = ' + formatCurrency(totalUnitPrice) + '</small>';
            }
            unitPriceEl.html(unitPriceHtml);
        }
        
        // Update subtotal if element exists
        var subtotalEl = row.find('.item-subtotal');
        if(subtotalEl.length) {
            subtotalEl.html('<span class="ttl">Subtotal : </span>' + formatCurrency(subtotal));
        }
    }
    
    // Function to update combo cart item display
    function updateComboCartItemDisplay(row, res, subtotal) {
        console.log('Updating combo display:', res);
        
        // Update main product quantity
        var mainQty = res.main_product_quantity || 1;
        var mainQtyEl = row.find('.qty-display');
        if(mainQtyEl.length) {
            mainQtyEl.text(mainQty);
            console.log('Updated main qty display to:', mainQty);
        } else {
            console.warn('Main qty display element (.qty-display) not found in row');
        }
        
        // Update combo product quantity (always update from response)
        var comboQty = res.combo_product_quantity || 1;
        var comboQtyEl = row.find('.combo-qty-display');
        if(comboQtyEl.length) {
            comboQtyEl.text(comboQty);
            console.log('Updated combo qty display to:', comboQty);
        }
        
        // Update data attributes
        row.data('main-product-quantity', mainQty);
        row.data('combo-product-quantity', comboQty);
        
        // Get prices from response or data attributes
        var mainPrice = parseFloat(res.main_product_price || row.data('main-product-price') || 0);
        var comboPrice = parseFloat(res.combo_product_price || row.data('combo-product-price') || 0);
        var mainQtyInt = parseInt(mainQty);
        var comboQtyInt = parseInt(comboQty);
        
        // Calculate totals
        var mainTotal = mainPrice * mainQtyInt;
        var comboTotal = comboPrice * comboQtyInt;
        
        // Update unit price display if element exists
        var unitPriceEl = row.find('.unit-price');
        if(unitPriceEl.length) {
            var unitPriceHtml = 'Main: ' + formatCurrency(mainPrice) + ' × ' + mainQtyInt + ' = ' + formatCurrency(mainTotal) + '<br>';
            unitPriceHtml += 'Combo: ' + formatCurrency(comboPrice) + ' × ' + comboQtyInt + ' = ' + formatCurrency(comboTotal);
            unitPriceEl.html(unitPriceHtml);
        }
        
        // Update subtotal if element exists
        var subtotalEl = row.find('.item-subtotal');
        if(subtotalEl.length) {
            subtotalEl.html('<span class="ttl">Subtotal : </span>' + formatCurrency(subtotal));
        }
    }

    // Delete item with SweetAlert confirmation
    $(document).on('click', '.delete-cart', function(e){
        e.preventDefault();
        e.stopPropagation();
        
        var button = $(this);
        var row = button.closest('li.cart-details-list-box');
        var id = row.data('id') || button.data('cart-item-id');
        var isCombo = row.data('is-combo') === 'true';
        var comboId = row.data('combo-id');
        var productName = button.data('product-name') || row.find('h3').text().trim() || 'this item';
        
        // SweetAlert confirmation
        Swal.fire({
            title: 'Remove from Cart?',
            html: `Are you sure you want to remove <strong>${productName}</strong> from your cart?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            reverseButtons: true,
            width: '400px'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Removing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                // AJAX call to delete
                $.ajax({
                    url: "{{ url('cart/delete') }}/"+id,
                    type: "DELETE",
                    data: {
                        _token: "{{ csrf_token() }}",
                        is_combo: isCombo,
                        combo_id: comboId
                    },
                    success: function(res){
                        Swal.close();
                        
                        if(res.status === 'removed'){
                            // Success message
                            Swal.fire({
                                title: 'Removed!',
                                text: 'Item has been removed from your cart',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            // Remove row with animation
                            row.fadeOut(300, function(){
                                $(this).remove();
                                updateGrandTotal();
                                
                                // Check if cart is empty
                                if($('li.cart-details-list-box').length === 0) {
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1500);
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: 'Unable to remove item. Please try again.',
                                icon: 'error',
                                confirmButtonColor: '#d33'
                            });
                        }
                    },
                    error: function(xhr, status, error){
                        Swal.close();
                        console.error('Delete error:', error);
                        
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while removing the item. Please try again.',
                            icon: 'error',
                            confirmButtonColor: '#d33'
                        });
                    }
                });
            }
        });
        
        return false;
    });

    // Calculate cart total for coupon validation
    function getCartTotal() {
        var total = 0;
        $('li.cart-details-list-box').each(function(){
            var row = $(this);
            var isCombo = row.data('is-combo') === 'true';
            var gstPercent = parseFloat(row.data('gst-percent')) || 5;
            var courierCharge = parseFloat(row.data('courier-charge')) || 0;
            
            if (isCombo) {
                var mainPrice = parseFloat(row.data('main-product-price') || 0);
                var comboPrice = parseFloat(row.data('combo-product-price') || 0);
                var mainQty = parseInt(row.data('main-product-quantity') || 1);
                var comboQty = parseInt(row.data('combo-product-quantity') || 1);
                
                var mainTotal = mainPrice * mainQty;
                var comboTotal = comboPrice * comboQty;
                var itemSubtotal = mainTotal + comboTotal;
                var itemGST = itemSubtotal * (gstPercent / 100);
                total += itemSubtotal + itemGST + courierCharge;
            } else {
                var mainPrice = parseFloat(row.data('main-product-price') || 0);
                var mainQty = parseInt(row.find('.qty-display').text()) || parseInt(row.data('main-product-quantity') || 1);
                
                var itemSubtotal = mainPrice * mainQty;
                var itemGST = itemSubtotal * (gstPercent / 100);
                total += itemSubtotal + itemGST + courierCharge;
            }
        });
        return total;
    }
    
    // Update maximum savings when coupon is selected
    function updateMaxSavings() {
        var selectedCoupon = $('input[name="selectedCoupon"]:checked');
        var maxSavings = 0;
        
        if (selectedCoupon.length > 0) {
            var couponItem = selectedCoupon.closest('.coupon-item');
            // Use the cart total from data attribute or calculate it
            var cartTotal = parseFloat($('#availableCouponsList').data('cart-total')) || getCartTotal();
            var discountType = couponItem.data('discount-type');
            var couponAmount = parseFloat(couponItem.data('coupon-amount') || 0);
            
            // Recalculate discount based on current cart total
            if (discountType === 'percentage') {
                // For percentage: calculate discount on cart total
                maxSavings = (cartTotal * couponAmount) / 100;
            } else {
                // For fixed: use the coupon amount, but not more than cart total
                maxSavings = Math.min(couponAmount, cartTotal);
            }
        }
        
        $('#maxSavings').text('₹' + maxSavings.toFixed(2));
    }
    
    // Handle coupon radio button selection
    $(document).on('change', '.coupon-radio', function() {
        updateMaxSavings();
    });
    
// Update the checkCouponBtn click handler
$('#checkCouponBtn').on('click', function() {
    var couponCode = $('#couponCodeInput').val().trim().toUpperCase();
    var $messageDiv = $('#couponMessage');
    
    if (!couponCode) {
        $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text('Please enter a coupon code.').show();
        return;
    }
    
    // Check if coupon exists in the filtered list
    var found = false;
    $('.coupon-item').each(function() {
        var couponCodeText = $(this).find('.coupon-code-box strong').text().trim();
        if (couponCodeText === couponCode) {
            var $radio = $(this).find('.coupon-radio');
            var couponType = $(this).data('coupon-type');
            var isPartnerCoupon = couponType === 'partner';
            
            // Check user status and coupon type compatibility
            @if(!Auth::guard('customer')->check())
                // Guest user - can only use public coupons
                if (isPartnerCoupon) {
                    $messageDiv.removeClass('alert-success').addClass('alert alert-danger').html('Partner coupons require login. Please <a href="{{ route("frontend.customer.login") }}" class="alert-link">login</a> to apply this coupon.').show();
                    found = true;
                    return false;
                }
            @elseif(Auth::guard('customer')->check())
                // Approved customers - can only use partner coupons
                @if($isApprovedCustomer)
                    if (!isPartnerCoupon) {
                        $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text('This is a public coupon. Only non-approved accounts can use it.').show();
                        found = true;
                        return false;
                    }
                @else
                    // Non-approved customers - can only use public coupons
                    if (isPartnerCoupon) {
                        $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text('This is a partner coupon. Only approved accounts can use it.').show();
                        found = true;
                        return false;
                    }
                @endif
            @endif
            
            if (!$radio.prop('disabled')) {
                $radio.prop('checked', true);
                updateMaxSavings();
                $messageDiv.removeClass('alert-danger').addClass('alert alert-success').text('Coupon found and selected!').show();
                found = true;
                return false;
            } else {
                $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text('This coupon is not applicable to your cart.').show();
                found = true;
                return false;
            }
        }
    });
    
    if (!found) {
        $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text('Coupon code not found.').show();
    }
});

// Update applySelectedCouponBtn to validate user status
$('#applySelectedCouponBtn').on('click', function() {
    var selectedCoupon = $('input[name="selectedCoupon"]:checked');
    var $messageDiv = $('#couponMessage');
    var $applyBtn = $(this);
    
    if (selectedCoupon.length === 0) {
        $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text('Please select a coupon to apply.').show();
        return;
    }
    
    var couponItem = selectedCoupon.closest('.coupon-item');
    var couponCode = couponItem.find('.coupon-code-box strong').text().trim();
    var couponType = couponItem.data('coupon-type');
    var isPartnerCoupon = couponType === 'partner';
    
    // Validate user status vs coupon type
    @if(!Auth::guard('customer')->check())
        // Guest user
        if (isPartnerCoupon) {
            $messageDiv.removeClass('alert-success').addClass('alert alert-danger').html('Partner coupons require login. Please <a href="{{ route("frontend.customer.login") }}" class="alert-link">login</a> to apply this coupon.').show();
            return;
        }
    @elseif(Auth::guard('customer')->check())
        // Approved customers can only use partner coupons
        @if($isApprovedCustomer)
            if (!isPartnerCoupon) {
                $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text('This is a public coupon. Only non-approved accounts can use it.').show();
                return;
            }
        @else
            // Non-approved customers can only use public coupons
            if (isPartnerCoupon) {
                $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text('This is a partner coupon. Only approved accounts can use it.').show();
                return;
            }
        @endif
    @endif
    
    // Disable button and show loading
    $applyBtn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Applying...');
    $messageDiv.hide();
    
    // Send only coupon_code as per your working backend code
    $.ajax({
        url: '{{ route("frontend.cart.applyCoupon") }}',
        type: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            coupon_code: couponCode
        },
        success: function(response) {
            if (response.success) {
                $messageDiv.removeClass('alert-danger').addClass('alert alert-success').text(response.message || 'Coupon applied successfully!').show();
                
                setTimeout(function() {
                    $('#applyCouponModal').modal('hide');
                    location.reload(); // Reload to show updated totals
                }, 1500);
            } else {
                $messageDiv.removeClass('alert-success').addClass('alert alert-danger').text(response.message || 'Failed to apply coupon.').show();
                $applyBtn.prop('disabled', false).html('APPLY');
            }
        },
        error: function(xhr) {
            var errorMessage = 'Failed to apply coupon. Please try again.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            } else if (xhr.responseJSON && xhr.responseJSON.errors) {
                // Display validation errors
                var errors = xhr.responseJSON.errors;
                errorMessage = Object.values(errors).flat().join('<br>');
            }
            $messageDiv.removeClass('alert-success').addClass('alert alert-danger').html(errorMessage).show();
            $applyBtn.prop('disabled', false).html('APPLY');
        }
    });
});
    // Remove coupon button
    $(document).on('click', '.remove-coupon-btn', function() {
        Swal.fire({
            title: 'Remove Coupon?',
            text: 'Are you sure you want to remove the applied coupon?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'Removing...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '{{ route("frontend.cart.removeCoupon") }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log(response)
                        Swal.close();
                        
                        if (response.success) {
                            Swal.fire({
                                title: 'Coupon Removed!',
                                text: response.message || 'Coupon has been removed successfully.',
                                icon: 'success',
                                timer: 1500,
                                showConfirmButton: false
                            });
                            
                            // Reload page to show updated totals without coupon
                            setTimeout(function() {
                                location.reload();
                            }, 1500);
                        } else {
                            Swal.fire({
                                title: 'Error!',
                                text: response.message || 'Failed to remove coupon.',
                                icon: 'error'
                            });
                        }
                    },
                    error: function() {
                        Swal.close();
                        Swal.fire({
                            title: 'Error!',
                            text: 'An error occurred while removing the coupon.',
                            icon: 'error'
                        });
                    }
                });
            }
        });
    });
    
    // Fix backdrop blocking issue
    $('#applyCouponModal').on('shown.bs.modal', function() {
        // Make backdrop non-interactive but visible
        $('.modal-backdrop').css({
            'pointer-events': 'none',
            'z-index': '9998'
        });
        
        // Ensure modal content is fully interactive and above footer
        $(this).css({
            'z-index': '9999',
            'pointer-events': 'auto'
        });
        
        $(this).find('.modal-dialog').css({
            'z-index': '10000',
            'pointer-events': 'auto'
        });
        
        $(this).find('.modal-content').css({
            'pointer-events': 'auto',
            'z-index': '10001'
        });
    });
    
    // Clean up when modal is hidden
    $('#applyCouponModal').on('hidden.bs.modal', function() {
        // Reset form
        $('#couponCodeInput').val('');
        $('input[name="selectedCoupon"]').prop('checked', false);
        $('#couponMessage').hide().removeClass('alert-success alert-danger');
        $('#maxSavings').text('₹0.00');
        $('#applySelectedCouponBtn').prop('disabled', false).html('APPLY');
    });
    
    // Enter key on coupon code input
    $('#couponCodeInput').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            $('#checkCouponBtn').click();
        }
    });
    
    // Prevent clicks on modal from closing it
    $('#applyCouponModal .modal-content').on('click', function(e) {
        e.stopPropagation();
    });
    
    // Allow closing by clicking backdrop
    $(document).on('click', '.modal-backdrop', function() {
        $('#applyCouponModal').modal('hide');
    });

    // Update Grand Total from server response
    function updateGrandTotalFromServer(grandTotal, totalItems, totalQuantity, totalSubtotal, totalGST, totalCourierCharge) {
        // Update left section totals
        $('.grand-total').text(formatCurrency(grandTotal));
        $('.total-left p:first').text('Total Items : ' + (totalItems || 0));
        $('.total-left p:last').text('Total Quantity : ' + (totalQuantity || 0));
        
        // Update right section order summary (bottom total)
        $('.total-text strong').text(formatCurrency(grandTotal));
        if ($('.address-right-top').length) {
            $('.address-right-top h3').text('Order Summary (' + (totalItems || 0) + ')');
        }
        
        // Update order summary details if provided
        if (totalSubtotal !== undefined) {
            $('.address-right-box:not(.border0) ul li:first-child span').text(formatCurrency(totalSubtotal));
        }
        if (totalGST !== undefined) {
            $('.address-right-box:not(.border0) ul li:nth-child(2) span').text('+ ' + formatCurrency(totalGST));
        }
        if (totalCourierCharge !== undefined && totalCourierCharge > 0) {
            $('.address-right-box:not(.border0) ul li:nth-child(3) span').text('+ ' + formatCurrency(totalCourierCharge));
        }
        
        // Update bottom total (in border0 box)
        $('.total-text strong').text(formatCurrency(grandTotal));
    }
    
    // Update Grand Total (client-side calculation)
    function updateGrandTotal(){
        var total = 0;
        var totalItems = 0;
        var totalQuantity = 0;
        var totalDiscount = 0;
        var processedCombos = [];
        var totalSubtotal = 0;
        var totalGST = 0;
        var totalCourierCharge = 0;
        var totalEmbroideryPrice = 0;

        $('li.cart-details-list-box').each(function(){
            var row = $(this);
            var isCombo = row.data('is-combo') === 'true';
            var comboId = row.data('combo-id');
            var gstPercent = parseFloat(row.data('gst-percent')) || 5; // Default to 5%
            var courierCharge = parseFloat(row.data('courier-charge')) || 0;
            
            var itemSubtotal = 0;
            var itemGST = 0;
            
            var embroideryTotalPrice = parseFloat(row.data('embroidery-total-price') || 0);

            if (isCombo && comboId) {
                // Calculate subtotal for combo products
                var mainPrice = parseFloat(row.data('main-product-price') || 0);
                var comboPrice = parseFloat(row.data('combo-product-price') || 0);
                var mainQty = parseInt(row.data('main-product-quantity') || 1);
                var comboQty = parseInt(row.data('combo-product-quantity') || 1);

                var mainTotal = mainPrice * mainQty;
                var comboTotal = comboPrice * comboQty;
                itemSubtotal = mainTotal + comboTotal;

                // Add embroidery to subtotal before GST calculation (matching controller logic)
                var subtotalWithEmbroidery = itemSubtotal + embroideryTotalPrice;
                itemGST = subtotalWithEmbroidery * (gstPercent / 100);

                // Count combo only once
                if (!processedCombos.includes(comboId)) {
                    totalItems++;
                    processedCombos.push(comboId);
                }
                // For combo, add both main and combo quantities
                totalQuantity += mainQty + comboQty;
            } else {
                // Calculate subtotal for individual products using main_product_price and main_product_quantity
                var mainPrice = parseFloat(row.data('main-product-price') || 0);
                var mainQty = parseInt(row.find('.qty-display').text()) || parseInt(row.data('main-product-quantity') || 1);

                itemSubtotal = mainPrice * mainQty;

                // Add embroidery to subtotal before GST calculation (matching controller logic)
                var subtotalWithEmbroidery = itemSubtotal + embroideryTotalPrice;
                itemGST = subtotalWithEmbroidery * (gstPercent / 100);

                // Count individual product
                totalItems++;
                totalQuantity += mainQty;
            }

            // Add to totals (total includes embroidery in GST calculation)
            totalSubtotal += itemSubtotal;
            totalEmbroideryPrice += embroideryTotalPrice;
            totalGST += itemGST;
            totalCourierCharge += courierCharge;
            total += subtotalWithEmbroidery + itemGST + courierCharge;
        });
        
        // Apply discount if any
        total = total - totalDiscount;
        
        $('.grand-total').text(formatCurrency(total));
        $('.total-text strong').text(formatCurrency(total));
        $('.total-left p:first').text('Total Items : ' + totalItems);
        $('.total-left p:last').text('Total Quantity : ' + totalQuantity);
        
        // Update order summary if it exists
        if ($('.address-right-top').length) {
            $('.address-right-top h3').text('Order Summary (' + totalItems + ')');
            
            // Update subtotal - first address-right-box (not border0)
            $('.address-right-box:not(.border0) ul li:first-child span').text(formatCurrency(totalSubtotal));

            // Update embroidery total if exists
            var $embroideryLi = $('.address-right-box:not(.border0) ul li p:contains("Total Embroidery")').parent();
            if (totalEmbroideryPrice > 0) {
                if ($embroideryLi.length === 0) {
                    // Insert after subtotal
                    $('.address-right-box:not(.border0) ul li:first-child').after('<li><p>Total Embroidery</p><span>+ ' + formatCurrency(totalEmbroideryPrice) + '</span></li>');
                } else {
                    $embroideryLi.find('span').text('+ ' + formatCurrency(totalEmbroideryPrice));
                }
            } else {
                $embroideryLi.remove();
            }

            // Update GST - find GST li by text content
            $('.address-right-box:not(.border0) ul li').each(function() {
                var $li = $(this);
                if ($li.find('p').text().trim().includes('GST')) {
                    $li.find('span').text('+ ' + formatCurrency(totalGST));
                    return false; // break loop
                }
            });
            
            // Update shipping if exists - first address-right-box (not border0)
            var courierLi = $('.address-right-box:not(.border0) ul li').filter(function() {
                return $(this).find('p').text().trim() === 'Shipping';
            });
            if (totalCourierCharge > 0) {
                if (courierLi.length === 0) {
                    // Insert shipping as the last item in the first box
                    $('.address-right-box:not(.border0) ul').append(
                        '<li><p>Shipping</p><span>+ ' + formatCurrency(totalCourierCharge) + '</span></li>'
                    );
                } else {
                    courierLi.find('span').text('+ ' + formatCurrency(totalCourierCharge));
                }
            } else {
                courierLi.remove();
            }
            
            // Update bottom total (in border0 box)
            $('.total-text strong').text(formatCurrency(total));
        }
    }

});
</script>

<!-- Add SweetAlert custom styles -->
<style>
.swal2-popup-custom {
    border-radius: 12px;
    padding: 20px;
}

.swal2-confirm-btn {
    background-color: #dc3545 !important;
    border-radius: 6px !important;
    padding: 10px 24px !important;
    font-weight: 600 !important;
}

.swal2-cancel-btn {
    background-color: #6c757d !important;
    border-radius: 6px !important;
    padding: 10px 24px !important;
    font-weight: 600 !important;
}

.swal2-title {
    font-size: 22px !important;
    font-weight: 700 !important;
    color: #333 !important;
}

.swal2-html-container {
    font-size: 16px !important;
    color: #555 !important;
    margin-top: 10px !important;
}

.swal2-icon.swal2-warning {
    border-color: #ffc107;
    color: #ffc107;
}

.swal2-icon.swal2-success {
    border-color: #28a745;
    color: #28a745;
}

.swal2-icon.swal2-error {
    border-color: #dc3545;
    color: #dc3545;
}

.swal2-icon.swal2-question {
    border-color: #17a2b8;
    color: #17a2b8;
}

.applied-coupon-alert {
    animation: fadeIn 0.5s;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

/* Cart size input styling */
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
// Configure Toastr
toastr.options = {
    "closeButton": true,
    "debug": false,
    "newestOnTop": true,
    "progressBar": true,
    "positionClass": "toast-top-right",
    "preventDuplicates": false,
    "onclick": null,
    "showDuration": "300",
    "hideDuration": "1000",
    "timeOut": "3000",
    "extendedTimeOut": "1000",
    "showEasing": "swing",
    "hideEasing": "linear",
    "showMethod": "fadeIn",
    "hideMethod": "fadeOut"
};

$(document).ready(function() {
    // Debounce function to limit API calls
    let updateTimeout = {};
    let isUpdating = {};
    
    // Sanitize quantity inputs (only allow numbers)
    $(document).on('input keyup paste', '.cart-size-qty', function() {
        let value = $(this).val();
        value = value.replace(/[^0-9]/g, '');
        if (value === '') {
            $(this).val('');
        } else {
            $(this).val(Math.floor(Number(value)));
        }
    });
    
    // Handle size quantity input changes on keyup
    $(document).on('keyup', '.cart-size-qty', function(e) {
        // Don't trigger on arrow keys, tab, shift, etc.
        if ([37, 38, 39, 40, 16, 17, 18, 9].indexOf(e.which) !== -1) {
            return;
        }
        
        const $input = $(this);
        const cartItemId = $input.data('cart-item-id');
        
        if (!cartItemId || isUpdating[cartItemId]) {
            return;
        }
        
        // Clear previous timeout for this cart item
        if (updateTimeout[cartItemId]) {
            clearTimeout(updateTimeout[cartItemId]);
        }
        
        // Debounce: wait 800ms after user stops typing
        updateTimeout[cartItemId] = setTimeout(function() {
            updateCartItemSizes(cartItemId);
        }, 800);
    });
    
    // Also update on blur (when user leaves the input field)
    $(document).on('blur', '.cart-size-qty', function() {
        const $input = $(this);
        const cartItemId = $input.data('cart-item-id');
        
        if (!cartItemId || isUpdating[cartItemId]) {
            return;
        }
        
        // Clear any pending timeout for this cart item
        if (updateTimeout[cartItemId]) {
            clearTimeout(updateTimeout[cartItemId]);
        }
        
        // Update immediately when user leaves the field
        updateCartItemSizes(cartItemId);
    });
    
    // Also update on Enter key
    $(document).on('keypress', '.cart-size-qty', function(e) {
        if (e.which === 13) { // Enter key
            e.preventDefault();
            const $input = $(this);
            const cartItemId = $input.data('cart-item-id');
            
            if (!cartItemId || isUpdating[cartItemId]) {
                return;
            }
            
            // Clear any pending timeout
            if (updateTimeout[cartItemId]) {
                clearTimeout(updateTimeout[cartItemId]);
            }
            
            // Update immediately
            updateCartItemSizes(cartItemId);
            $(this).blur(); // Remove focus
        }
    });
    
    // Function to update cart item sizes
    function updateCartItemSizes(cartItemId) {
        if (isUpdating[cartItemId]) {
            return;
        }
        
        const $cartItem = $('li.cart-details-list-box[data-id="' + cartItemId + '"]');
        
        if ($cartItem.length === 0) {
            console.error('Cart item not found:', cartItemId);
            toastr.error('Cart item not found. Please refresh the page.');
            return;
        }
        
        const sizeQuantities = {};
        
        // Collect all size quantities for this cart item
        $cartItem.find('.cart-size-qty').each(function() {
            const sizeId = $(this).data('size-id');
            const quantity = parseInt($(this).val(), 10) || 0;
            if (sizeId) {
                sizeQuantities[sizeId] = quantity;
            }
        });
        
        // Set updating flag for this cart item
        isUpdating[cartItemId] = true;
        
        // Show loading indicator
        $cartItem.css('opacity', '0.6').css('pointer-events', 'none');
        $cartItem.find('.cart-size-qty').prop('disabled', true);
        
        // Send AJAX request to update cart
        $.ajax({
            url: '{{ route("frontend.cart.updateSizes") }}',
            method: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                cart_item_id: cartItemId,
                size_quantities: sizeQuantities
            },
            success: function(response) {
                isUpdating[cartItemId] = false;
                
                // Re-enable inputs
                $cartItem.find('.cart-size-qty').prop('disabled', false);
                $cartItem.css('opacity', '1').css('pointer-events', 'auto');
                
                if (response.success) {
                    // Update cart item data attributes
                    if (response.total_quantity !== undefined) {
                        $cartItem.attr('data-main-product-quantity', response.total_quantity);
                        $cartItem.attr('data-quantity', response.total_quantity);
                    }
                    
                    // Update cart count if provided
                    if (response.cart_count !== undefined) {
                        $('.cart-count').text(response.cart_count);
                        if (typeof sessionStorage !== 'undefined') {
                            sessionStorage.setItem('cart_count', response.cart_count);
                        }
                    }
                    
                    // Update totals from server response
                    if (response.grand_total !== undefined) {
                        $('.grand-total').text('₹' + formatNumber(response.grand_total));
                        $('.total-text strong').text('₹' + formatNumber(response.grand_total));
                    }
                    
                    if (response.total_items !== undefined) {
                        $('.total-left p:first').text('Total Items : ' + response.total_items);
                    }
                    
                    if (response.total_quantity_all !== undefined) {
                        $('.total-left p:last').text('Total Quantity : ' + response.total_quantity_all);
                    }
                    
                    if (response.total_subtotal !== undefined) {
                        $('.address-right-box li:contains("Subtotal") span').text('₹' + formatNumber(response.total_subtotal));
                    }
                    
                    if (response.total_gst !== undefined) {
                        $('.address-right-box li:contains("GST") span').text('+ ₹' + formatNumber(response.total_gst));
                    }
                    
                    if (response.total_courier_charge !== undefined && response.total_courier_charge > 0) {
                        var $courierLi = $('.address-right-box li:contains("Shipping")');
                        if ($courierLi.length) {
                            $courierLi.find('span').text('+ ₹' + formatNumber(response.total_courier_charge));
                        }
                    }
                    
                    // Update Total Embroidery in Order Summary
                    if (response.total_embroidery_price !== undefined) {
                        var $embroideryLi = $('.address-right-box li:contains("Total Embroidery")');
                        if (response.total_embroidery_price > 0) {
                            if ($embroideryLi.length) {
                                // Update existing embroidery line
                                $embroideryLi.find('span').text('+ ₹' + formatNumber(response.total_embroidery_price));
                            } else {
                                // Insert embroidery line after coupon discount (if exists) or after subtotal
                                var $insertAfter = $('.address-right-box li:contains("Coupon Discount")');
                                if ($insertAfter.length === 0) {
                                    $insertAfter = $('.address-right-box li:contains("Subtotal")');
                                }
                                if ($insertAfter.length) {
                                    $insertAfter.after('<li><p>Total Embroidery</p><span>+ ₹' + formatNumber(response.total_embroidery_price) + '</span></li>');
                                }
                            }
                        } else {
                            // Remove embroidery line if total is 0
                            if ($embroideryLi.length) {
                                $embroideryLi.remove();
                            }
                        }
                    }
                    
                    // Embroidery price and total embroidery display removed from product section
                    // Only shown in Order Summary now
                    
                    // Show toastr success message
                    toastr.success('Cart updated successfully!', 'Success', {
                        timeOut: 3000,
                        positionClass: 'toast-top-right'
                    });
                } else {
                    console.error('Update failed:', response.message);
                    toastr.error(response.message || 'Failed to update cart. Please try again.', 'Error');
                }
            },
            error: function(xhr) {
                isUpdating[cartItemId] = false;
                
                // Re-enable inputs
                $cartItem.find('.cart-size-qty').prop('disabled', false);
                $cartItem.css('opacity', '1').css('pointer-events', 'auto');
                
                console.error('AJAX error:', xhr);
                let errorMsg = 'Failed to update cart. Please try again.';
                
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                } else if (xhr.status === 404) {
                    errorMsg = 'Cart item not found. Please refresh the page.';
                } else if (xhr.status === 422) {
                    const errors = xhr.responseJSON?.errors || {};
                    errorMsg = 'Validation error: ' + Object.values(errors).flat().join(', ');
                } else if (xhr.status === 500) {
                    errorMsg = 'Server error. Please try again later.';
                } else if (xhr.status === 0) {
                    errorMsg = 'Network error. Please check your connection.';
                }
                
                toastr.error(errorMsg, 'Error');
            }
        });
    }
    
    // Function to update cart totals dynamically
    function updateCartTotals() {
        var grandTotal = 0;
        var totalItems = 0;
        var totalQuantity = 0;
        var totalSubtotal = 0;
        var totalGST = 0;
        var totalCourierCharge = 0;
        var totalDiscount = 0;
        var processedCombos = [];
        
        $('li.cart-details-list-box').each(function() {
            var $row = $(this);
            var isCombo = $row.data('is-combo') === 'true';
            var gstPercent = parseFloat($row.data('gst-percent')) || 5;
            var courierCharge = parseFloat($row.data('courier-charge')) || 0;
            
            if (isCombo) {
                var mainPrice = parseFloat($row.data('main-product-price')) || 0;
                var mainQty = parseInt($row.data('main-product-quantity')) || 1;
                var comboPrice = parseFloat($row.data('combo-product-price')) || 0;
                var comboQty = parseInt($row.data('combo-product-quantity')) || 1;
                
                var mainTotal = mainPrice * mainQty;
                var comboTotal = comboPrice * comboQty;
                var itemSubtotal = mainTotal + comboTotal;
                
                var comboId = $row.data('combo-id');
                if (comboId && processedCombos.indexOf(comboId) === -1) {
                    totalItems++;
                    processedCombos.push(comboId);
                }
                totalQuantity += mainQty + comboQty;
            } else {
                var itemPrice = parseFloat($row.data('main-product-price')) || 0;
                
                // Calculate quantity from size inputs if they exist
                var itemQty = 0;
                var hasSizeInputs = $row.find('.cart-size-qty').length > 0;
                
                if (hasSizeInputs) {
                    $row.find('.cart-size-qty').each(function() {
                        var qty = parseInt($(this).val(), 10) || 0;
                        itemQty += qty;
                    });
                } else {
                    itemQty = parseInt($row.data('main-product-quantity')) || 1;
                }
                
                var itemSubtotal = itemPrice * itemQty;
                totalItems++;
                totalQuantity += itemQty;
            }
            
            var itemGST = itemSubtotal * (gstPercent / 100);
            var itemTotal = itemSubtotal + itemGST + courierCharge;
            
            // Check for discount
            var discountEl = $row.find('.discount-amount');
            if (discountEl.length) {
                var discount = parseFloat(discountEl.text().replace(/[^0-9.]/g, '')) || 0;
                itemTotal -= discount;
                totalDiscount += discount;
            }
            
            totalSubtotal += itemSubtotal;
            totalGST += itemGST;
            totalCourierCharge += courierCharge;
            grandTotal += itemTotal;
        });
        
        // Apply coupon discount if exists
        var couponDiscount = parseFloat($('.text-success:contains("Coupon Discount")').next().text().replace(/[^0-9.]/g, '')) || 0;
        if (couponDiscount > 0) {
            grandTotal -= couponDiscount;
            totalDiscount += couponDiscount;
        }
        
        // Update display
        $('.grand-total').text('₹' + formatNumber(grandTotal));
        $('.total-text strong').text('₹' + formatNumber(grandTotal));
        $('.total-left p:first').text('Total Items : ' + totalItems);
        $('.total-left p:last').text('Total Quantity : ' + totalQuantity);
        
        // Update order summary
        $('.address-right-box li:contains("Subtotal") span').text('₹' + formatNumber(totalSubtotal));
        $('.address-right-box li:contains("GST") span').text('+ ₹' + formatNumber(totalGST));
        if (totalCourierCharge > 0) {
            $('.address-right-box li:contains("Shipping") span').text('+ ₹' + formatNumber(totalCourierCharge));
        }
    }
    
    // Helper function to format numbers
    function formatNumber(num) {
        return parseFloat(num).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }
});
</script>
@endsection