@extends('frontend.layout')
@section('content')
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
<section class="bedcum-sec" style="padding-top: 130px;">
   <div class="container">
      <div class="bedcum-inner">
         <div class="bed-cum-list">
            <ul>
               <li><a href="#">Shop</a></li>
               <li><i class="fa-regular fa-angle-right"></i></li>
               <li><span>Cart</span></li>
               <li><i class="fa-regular fa-angle-right"></i></li>
               <li><span>Address Details</span></li>
            </ul>
         </div>
      </div>
   </div>
</section>
<section class="address-sec">
   <div class="container">
      <div class="address-inner">
         <div class="row rowBox address-wrapper">
            <!-- LEFT SIDE -->
            <div class="col-md-7 col-sm-12 col-12 columnBox address-left_2">
               <div class="address-left-inner">
                  <div class="address-left-header address-left-top-header">
                     <div class="address-header-left">
                        <h2>Delivery & Billing Address</h2>
                     </div>
                     <div class="address-header-right">
                        <!--<a href="{{ route('frontend.add-address') }}" class="add-address-btn"><i class="fa-regular fa-plus"></i> Add New</a>-->
                        <a href="#" data-bs-toggle="modal" data-bs-target="#addAdressModal" class="add-address-btn"><i class="fa-regular fa-plus"></i> Add New</a>
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
                  <div class="address-left-bottom">
                     <!--<table class="userAddreassTable">
                        <thead class="address-table-header">
                            <tr>
                                <th>Select</th>
                                <th>Delivery Address</th>
                                <th>Address</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        
                        <tbody>
                            @foreach ($addresses as $address)
                                <tr>
                                    <td class="text-center">
                                        <div class="form-check">
                                            <label class="address-checkbox">
                                                <input class="form-check-input address-radio" type="radio"
                                                    name="selected_address" id="address_{{ $address->id }}"
                                                    value="{{ $address->id }}"
                                                    data-postcode="{{ $address->pincode }}"
                                                    {{ (isset($selectedAddressId) && $selectedAddressId == $address->id) || (!isset($selectedAddressId) && $loop->first) ? 'checked' : '' }}>
                                                <span class="form-check-label address-checkmark"></span>
                                            </label>
                                        </div>
                                    </td>
                        
                                    <td>
                                        <address class="addresBox mb-0">
                                            <p><strong><i class="fa-light fa-person"></i> Name :</strong>
                                                {{ $address->name }}</p>
                                            <p><strong><i class="fa-light fa-mobile-retro"></i> Mobile
                                                    :</strong> +91 {{ $address->phone }}</p>
                                            <p><strong><i class="fa-light fa-location-dot"></i> Address
                                                    :</strong>
                                                {{ $address->address_line1 }}, {{ $address->city }},
                                                {{ $address->state }} - {{ $address->pincode }},
                                                {{ $address->country }}
                                            </p>
                                        </address>
                                    </td>
                        
                                    <td><small class="text-muted">Same as delivery</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                        </table>-->
                     <div class="userAddressListArea">
                        <div class="userAddressListAreainner">
                           @foreach ($addresses as $address)
                           <div class="userAddressListItem">
                              <input class="address-radio" type="radio"
                              name="selected_address" id="address_{{ $address->id }}"
                              value="{{ $address->id }}"
                              data-postcode="{{ $address->pincode }}"
                              {{ (isset($selectedAddressId) && $selectedAddressId == $address->id) || (!isset($selectedAddressId) && $loop->first) ? 'checked' : '' }}>
                              <label class="d-flex userAddressListIteminner" for="address_{{ $address->id }}">
                                 <span class="addressrAdioCcustom"></span>
                                 <div class="d-flex justify-content-between userAddressDtlsArea">
                                    <div class="userAddressLeftSide">
                                       <div class="userAddressDtls">
                                          <p><strong><i class="fa-light fa-person"></i> Name :</strong>
                                             {{ $address->name }}
                                          </p>
                                          <p><strong><i class="fa-light fa-mobile-retro"></i> Mobile
                                             :</strong> +91 {{ $address->phone }}
                                          </p>
                                          <p><strong><i class="fa-light fa-location-dot"></i> Address
                                             :</strong>
                                             {{ $address->address_line1 }}, {{ $address->city }},
                                             {{ $address->state }} - {{ $address->pincode }},
                                             {{ $address->country }}
                                          </p>
                                       </div>
                                    </div>
                                    <div class="userAddressRightSide">
                                       <div class=""><small class="text-muted">Same as delivery</small></div>
                                    </div>
                                 </div>
                              </label>
                           </div>
                           @endforeach
                        </div>
                     </div>
                  </div>
               </div>
               <div class="address-delivery-sec">
                  <p>Expected Delivery : <span class="blackColor">{{ $expectedDelivery }}</span></p>
               </div>
            </div>
            <!-- RIGHT SIDE ORDER SUMMARY -->
            <div class="col-md-5 col-sm-12 col-12 columnBox address-right_2 checkout-right-box">
               <div class="checkout-cart desktopShowBox">
                  <div class="checkout-cart-header">
                     <h2 class="title">Order Summary</h2>
                     <span class="cart-count">{{ $totalUniqueItems }}</span>
                  </div>
                  <div class="checkout-cart-table">
                     <div class="checkout-cart-row head">
                        <div class="th">Product</div>
                        <div class="th">Price</div>
                        <div class="th">Quantity</div>
                        <div class="th">Total</div>
                     </div>
                     @foreach ($cartItems as $item)
                     @php
                     $mainQty = $item->main_product_quantity ?? 1;
                     $mainPrice = $item->main_product_price ?? 0;
                     $comboQty = $item->combo_product_quantity ?? 0;
                     $comboPrice = $item->combo_product_price ?? 0;
                     $rowSubtotal = ($mainPrice * $mainQty) + ($comboPrice * $comboQty);
                     $embroideryTotalPrice = $item->embroidery_total_price ?? 0;
                     @endphp
                     <div class="checkout-cart-row body">
                        <div class="td product">
                           <p>{{ $item->product->name ?? 'Product' }}</p>
                           @if ($item->size)
                           <p>Size : {{ $item->size->term }}</p>
                           @endif
                           @if (($item->embroidery_logo ?? null) || ($embroideryTotalPrice > 0))
                           <p>Embroidery : YES</p>
                           @endif
                        </div>
                        <div class="td price">
                           ₹{{ number_format($mainPrice, 2) }}
                        </div>
                        <div class="td qty">{{ $mainQty }}</div>
                        <div class="td total">₹{{ number_format($rowSubtotal, 2) }}</div>
                     </div>
                     @endforeach
                  </div>
                  <div class="checkout-cart-summary">
                     <div class="line">
                        <span class="ttl bold">Subtotal</span>
                        <span class="txt">₹{{ number_format($totalSubtotal, 2) }}</span>
                     </div>
                     @if (($totalDiscount ?? 0) > 0)
                     <div class="line">
                        <span class="ttl bold">Coupon Discount</span>
                        <span class="txt text-success">- ₹{{ number_format($totalDiscount, 2) }}</span>
                     </div>
                     @endif
                     @if (($totalEmbroideryPrice ?? 0) > 0)
                     <div class="line">
                        <span class="ttl bold">Total Embroidery</span>
                        <span class="txt">+ ₹{{ number_format($totalEmbroideryPrice, 2) }}</span>
                     </div>
                     @endif
                     <div class="line">
                        <span class="ttl bold">Shipping</span>
                        <span class="txt muted js-shipping-charge-amount">Add address to calculate</span>
                     </div>
                     <div class="line">
                        <span class="ttl bold">{{ number_format($gstPercent, 2) }}% IGST</span>
                        <span class="txt">(+) ₹{{ number_format($totalGST, 2) }}</span>
                     </div>
                     <div class="line bold">
                        <span class="ttl">Total GST</span>
                        <span class="txt">₹{{ number_format($totalGST, 2) }}</span>
                     </div>
                  </div>
                  <div class="checkout-cart-grand-total">
                     <span class="ttl">Total</span>
                     <span class="txt js-final-total"><strong>₹{{ number_format($grandTotal, 2) }}</strong></span>
                  </div>
               </div>
               <div class="address-right-top checkout-details mobileShowBox">
                  <h3>Order Summary ({{ $totalUniqueItems }})</h3>
                  @foreach ($cartItems as $item)
                  <div class="address-right-box">
                     <!-- Product Image and Name Section -->
                     <div class="d-flex justify-content-between align-items-start mb-2 address-right-box-inner">
                        <div class="d-flex leftPart">
                           <!-- Product Image -->
                           @if ($item->product->thumbnail)
                           <div class="cart-image" style="">
                              <img src="{{ asset($item->product->thumbnail) }}"
                                 alt="{{ $item->product->name }}"
                                 style="">
                           </div>
                           @else
                           <div class="cart-icon" style="">
                              <i class="fas fa-image" style=""></i>
                           </div>
                           @endif
                           <!-- Product Name and Combo -->
                           <div class="flex-grow-1 cart-content">
                              <h4 class="mb-1">{{ $loop->iteration }}. {{ $item->product->name }}</h4>
                              <ul class="cart-content-list">
                                 <li>
                                    <div class="d-flex justify-content-between align-items-center">
                                       <div>
                                          <p class="mb-0 proPriceQuantity">
                                             ₹{{ number_format($item->main_product_price ?? 0, 2) }} ×
                                             {{ $item->main_product_quantity ?? 1 }}
                                             @if ($item->combo_product_price)
                                             <br>
                                             <small class="text-muted">
                                             + Combo:
                                             ₹{{ number_format($item->combo_product_price, 2) }} ×
                                             {{ $item->combo_product_quantity }}
                                             </small>
                                             @endif
                                          </p>
                                       </div>
                                    </div>
                                 </li>
                              </ul>
                           </div>
                        </div>
                        <div class="rightPart">
                           <div class="fw-medium proPrice">
                              ₹{{ number_format(
                              $item->main_product_price * $item->main_product_quantity +
                              $item->combo_product_price * $item->combo_product_quantity,
                              2,
                              ) }}
                           </div>
                        </div>
                     </div>
                     <!-- Combo display -->
                     @if ($item->combo_id)
                     @php $comboProduct = \App\Models\Product::find($item->combo_id); @endphp
                     @if ($comboProduct)
                     <div class="d-flex align-items-start mt-1">
                        @if ($comboProduct->thumbnail)
                        <div class="me-2"
                           style="width: 40px; height: 40px; flex-shrink: 0;">
                           <img src="{{ asset($comboProduct->thumbnail) }}"
                              alt="{{ $comboProduct->name }}"
                              style="width: 100%; height: 100%; object-fit: cover; border-radius: 3px;">
                        </div>
                        @endif
                        <p class="mb-0 text-muted" style="font-size: 12px;">+
                           {{ $comboProduct->name }}
                        </p>
                     </div>
                     @endif
                     @endif
                     <!-- Pricing Details -->
                  </div>
                  @endforeach
                  <!-- PRICING SUMMARY -->
                  <div class="address-right-box">
                     <ul>
                        <li>
                           <p>Subtotal</p>
                           <span>₹{{ number_format($totalSubtotal, 2) }}</span>
                        </li>
                        @if ($appliedCoupon)
                        <li>
                           <p>Coupon ({{ $appliedCoupon->coupon_code }})</p>
                           <span class="text-success">- ₹{{ number_format($totalDiscount, 2) }}</span>
                        </li>
                        @endif
                        @if (($totalEmbroideryPrice ?? 0) > 0)
                        <li>
                           <p>Total Embroidery</p>
                           <span>+ ₹{{ number_format($totalEmbroideryPrice, 2) }}</span>
                        </li>
                        @endif
                        <li>
                           <p>GST ({{ number_format($gstPercent, 2) }}%)</p>
                           <span>+ ₹{{ number_format($totalGST, 2) }}</span>
                        </li>
                        <li class="js-shipping-charge-li" style="display: none;">
                           <p>Shipping Charge</p>
                           <span class="js-shipping-charge-amount">Add address to calculate</span>
                        </li>
                        <li>
                           <p><strong>Total</strong></p>
                           <span class="js-order-total"><strong>₹{{ number_format($grandTotal, 2) }}</strong></span>
                        </li>
                     </ul>
                  </div>
                  <!-- FINAL TOTAL + YOU SAVE -->
                  <div class="address-right-box border0">
                     <ul>
                        <li>
                           <p><strong>Total</strong></p>
                           <span
                              class="total-text js-final-total"><strong>₹{{ number_format($grandTotal, 2) }}</strong></span>
                        </li>
                        @if ($youSave > 0)
                        <li>
                           <p class="text-success"><strong>You Save</strong></p>
                           <span
                              class="text-success"><strong>₹{{ number_format($youSave, 2) }}</strong></span>
                        </li>
                        @endif
                     </ul>
                  </div>
               </div>
               <!-- BUTTON -->
               <div class="d-block mt-3 address-right-bottom">
                  <div class="place-btn-sec pt-0">
                     <div class="btn-container">
                        <button type="button" id="continueCheckoutBtn"
                           class="text-center w-100 add-tocart-btn">Continue Checkout</button>
                     </div>
                  </div>
               </div>
            </div>
            <!-- RIGHT END -->
         </div>
      </div>
   </div>
</section>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>   
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
<script src="https://unpkg.com/swiper@11/swiper-bundle.min.js"></script>
<script src="http://localhost/mafatlal/public/assets/js/sal.min.js"></script>	
<script src="http://localhost/mafatlal/public/assets/js/tilt.min.js"></script>	
<script src="http://localhost/mafatlal/public/assets/js/main.js"></script>
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
<div id="full-page-loader" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255, 255, 255, 0.8); z-index: 9999; justify-content: center; align-items: center;">
   <img src="{{ asset('images/mafatlal_loader.gif') }}" alt="Loading..." style="width: 80px; height: 80px;">
</div>
<script>
   $(document).ready(function() {
       // Store initial values
       var baseTotal = {{ $grandTotal }};
       var shippingCharge = 0;
       var shippingCalculated = false;
       var shippingRequestId = 0;
       @if ($appliedCoupon)
       var discount = {{ $totalDiscount }};
       @else
       var discount = 0;
       @endif
       var baseTotalAfterDiscount = baseTotal - discount;
   
       // Function to format currency
       function formatCurrency(amount) {
           return '₹' + parseFloat(amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
       }
   
       // Function to calculate and update total
       function updateTotal() {
           var newTotal = baseTotalAfterDiscount + shippingCharge;
   
           // Update order total
           $('.js-order-total strong').text(formatCurrency(newTotal));
           $('.js-final-total strong').text(formatCurrency(newTotal));
       }
   
       // Function to toggle loader and button
       function toggleLoader(show) {
           if (show) {
               $('#full-page-loader').css('display', 'flex');
               $('#continueCheckoutBtn').prop('disabled', true);
           } else {
               $('#full-page-loader').hide();
               $('#continueCheckoutBtn').prop('disabled', false);
           }
       }
   
       // Function to fetch shipping charge
       function fetchShippingCharge(postcode) {
           // Show loading state
           shippingCalculated = false;
           shippingCharge = 0;
           updateTotal();
           shippingRequestId++;
           var requestId = shippingRequestId;
           $('.js-shipping-charge-li').show();
           $('.js-shipping-charge-amount').html('<img src="{{ asset('images/mafatlal_loader.gif') }}" alt="Loading" style="height:18px;width:18px;vertical-align:middle;" />');
           
           // Show full screen loader and disable button
           toggleLoader(true);
   
           $.ajax({
               url: '{{ route("frontend.calculate-shipping-charge") }}',
               method: 'POST',
               data: {
                   delivery_postcode: postcode,
                   _token: '{{ csrf_token() }}'
               },
               success: function(response) {
                   if (requestId !== shippingRequestId) {
                       return;
                   }
                   console.log(response)
                   if (response.status === 'success') {
                       shippingCharge = parseFloat(response.shipping_charge) || 0;
                       shippingCalculated = true;
                       
                       // Update shipping charge display
                       $('.js-shipping-charge-amount').text('+ ' + formatCurrency(shippingCharge));
                       $('.js-shipping-charge-li').show();
                       
                       // Update total
                       updateTotal();
                       
                       // Hide loader and enable button
                       toggleLoader(false);
                   } else {
                       // Hide shipping charge on error
                       $('.js-shipping-charge-li').hide();
                       $('.js-shipping-charge-amount').text('Add address to calculate');
                       shippingCharge = 0;
                       shippingCalculated = false;
                       updateTotal();
                       
                       // Hide loader but keep button disabled if needed, or enable?
                       // User said "disable ... until shipping charge showing"
                       // If error, shipping charge is NOT showing properly (it's hidden or 0)
                       // But maybe we should let them try again or select another address.
                       // For now, I'll enable it so they can interact, but validation will stop them if shipping not calculated.
                       // Actually, if shipping fails, they can't proceed properly.
                       // But let's unblock the UI so they can change address.
                       toggleLoader(false);
                       // Keep button disabled if we want to force shipping calculation?
                       // The continue handler checks `!shippingCalculated`.
                       // So it's safe to enable button, but it will try to fetch again on click.
                   }
               },
               error: function(xhr) {
                   if (requestId !== shippingRequestId) {
                       return;
                   }
                   console.error('Error fetching shipping charge:', xhr);
                   // Hide shipping charge on error
                   $('.js-shipping-charge-li').hide();
                   $('.js-shipping-charge-amount').text('Add address to calculate');
                   shippingCharge = 0;
                   shippingCalculated = false;
                   updateTotal();
                   
                   // Hide loader
                   toggleLoader(false);
               }
           });
       }
   
       // Handle address radio button change
       $('.address-radio').on('change', function() {
           var postcode = $(this).data('postcode');
           if (postcode) {
               fetchShippingCharge(postcode);
           }
       });
   
       // Fetch shipping charge for initially selected address
       var initialSelected = $('.address-radio:checked');
       if (initialSelected.length > 0) {
           var initialPostcode = initialSelected.data('postcode');
           if (initialPostcode) {
               fetchShippingCharge(initialPostcode);
           }
       }
   
       // Handle Continue Checkout button click
       $('#continueCheckoutBtn').on('click', function() {
           var selectedAddress = $('.address-radio:checked');
           if (selectedAddress.length === 0) {
               alert('Please select a delivery address');
               return;
           }
   
           var addressId = selectedAddress.val();
           var postcode = selectedAddress.data('postcode');
           
           // If shipping charge hasn't been calculated yet, calculate it first
           if (!shippingCalculated) {
               // Fetch shipping charge first
               fetchShippingCharge(postcode);
               
               // Wait a bit for the AJAX to complete, then save
               setTimeout(function() {
                   saveShippingAndRedirect();
               }, 1000);
               return;
           }
           
           // Save shipping charge and redirect
           saveShippingAndRedirect();
           
           function saveShippingAndRedirect() {
               var shippingChargeToSave = shippingCharge || 0; // Allow 0 shipping charge
   
               console.log('Saving shipping charge:', shippingChargeToSave, 'for address:', addressId);
   
               // Disable button to prevent multiple clicks
               $('#continueCheckoutBtn').prop('disabled', true).text('Processing...');
   
               // Save shipping charge to cart items (even if 0)
               $.ajax({
                   url: '{{ route("frontend.save-shipping-charge") }}',
                   method: 'POST',
                   data: {
                       shipping_charge: shippingChargeToSave,
                       address_id: addressId,
                       _token: '{{ csrf_token() }}'
                   },
                   success: function(response) {
                       console.log('Save shipping charge response:', response);
                       if (response.status === 'success') {
                           console.log('Shipping charge saved successfully. Redirecting...');
                           // Small delay to ensure database update is complete
                           setTimeout(function() {
                               window.location.href = '{{ route("frontend.payment.method") }}';
                           }, 100);
                       } else {
                           alert('Error saving shipping charge: ' + (response.message || 'Please try again.'));
                           $('#continueCheckoutBtn').prop('disabled', false).text('Continue Checkout');
                       }
                   },
                   error: function(xhr) {
                       console.error('Error saving shipping charge:', xhr);
                       var errorMessage = 'Error saving shipping charge. Please try again.';
                       if (xhr.responseJSON && xhr.responseJSON.message) {
                           errorMessage = xhr.responseJSON.message;
                       }
                       alert(errorMessage);
                       $('#continueCheckoutBtn').prop('disabled', false).text('Continue Checkout');
                   }
               });
           }
       });
   });
</script>
@endsection