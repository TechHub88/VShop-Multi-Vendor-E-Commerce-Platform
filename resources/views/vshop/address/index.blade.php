@extends('vshop.main')

@section('title', 'Select Delivery Address')
<style>
    @media(min-width: 1400px) {
        .address-header-left ul li {
            font-size: 14px;
            padding: 0 75px;
        }
        .address-header-left ul li {
            width: 162px !important;
            padding: 0 !important;
            text-align: center;
        }
    }
    .address-left-bottom table tbody tr td:nth-child(3) {
        padding: 8px 12px !important;
    }
    .address-header-left ul li {
        width: 154px ;
        padding: 0 !important;
    }
    .address-table-header th:first-child {
        text-align:center;
    }
    .address-table-header th:nth-child(2) {
        text-align:center;
    }
</style>

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
                            <li><a href="{{ route('home') }}">Home</a> / </li>
                            <li><a href="{{ route('vshop.cart.index') }}">Wishlist - Cart</a> /</li>
                            <li>Payment Mode</li>
                        </ul>
                    </div>
                </div>

                {{-- ================= ADDRESS SECTION ================= --}}
                <div class="address-inner">
                    <div class="address-wrapper">

                        {{-- ===== LEFT SIDE ADDRESS LIST ===== --}}
                        <div class="address-left">
                            <div class="address-left-inner">

                                <div class="address-left-header">
                                    <div class="address-header-left">
                                        <h2>Choose Delivery & Billing Address</h2>
                                        <!--<ul>-->
                                        <!--    <li class="pl-0">Delivery Address</li>-->
                                        <!--    <li>Billing Address</li>-->
                                        <!--    <li class="pr-0">Address</li>-->
                                        <!--</ul>-->
                                    </div>

                                    <div class="address-header-right">
                                        <button class="add-address-btn"
                                            onclick="window.location.href='{{ route('vshop.address.create') }}'">
                                            Add Another Address
                                        </button>
                                        <!--<select class="add-address-btn gstn-btn">-->
                                        <!--    <option value="">GISTIN : ADKL568092D</option>-->
                                        <!--</select>-->
                                    </div>
                                </div>

                                <div class="address-left-bottom">
                                    @if(count($addresses) > 0)
                                    <table class="address-table">
                                        <thead class="address-table-header">
                                            <tr>
                                                <th>Delivery</th>
                                                <th>Billing</th>
                                                <th>Address</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($addresses as $address)
                                            <tr>
                                                {{-- DELIVERY --}}
                                                <td>
                                                    <label class="filter-radio">
                                                        <input type="radio"
                                                            name="delivery_radio"
                                                            value="{{ $address->id }}"
                                                            {{ $loop->first ? 'checked' : '' }}>
                                                        <span class="filter-checkmark"></span>
                                                    </label>
                                                </td>

                                                {{-- BILLING --}}
                                                <td>
                                                    <label class="filter-radio">
                                                        <input type="radio"
                                                            name="billing_radio"
                                                            value="{{ $address->id }}"
                                                            {{ $loop->first ? 'checked' : '' }}>
                                                        <span class="filter-checkmark"></span>
                                                    </label>
                                                </td>

                                                {{-- ADDRESS TEXT --}}
                                                <td>
                                                    <address>
                                                        <p>{{ $address->name }}, {{ $address->phone }}</p>
                                                        <p>{{ $address->address_line1 }}</p>
                                                        <p>{{ $address->city }}, {{ $address->state }} -
                                                           {{ $address->pincode }}, {{ $address->country }}</p>
                                                    </address>
                                                </td>

                                                {{-- ACTION --}}
                                                <td>
                                                    <div class="action-btn-sec">
                                                        <button class="delite-btn edit-btn"
                                                            onclick="window.location.href='{{ route('vshop.address.edit',$address->id) }}'">
                                                            <i class="fa-solid fa-pen"></i>
                                                        </button>
                                                       <form class="mb-0" action="{{ route('vshop.address.delete', $address->id) }}" method="POST"
      onsubmit="return confirm('Delete this address?')">

    @csrf
    @method('DELETE')

    <button type="submit" class="delite-btn">
        <i class="fa-solid fa-trash"></i>
    </button>

</form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    @else
                                        <div class="no-address-sec">
                                            <p>No addresses found. Please add address.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

{{-- ===== RIGHT SIDE ORDER SUMMARY ===== --}}
<div class="address-right">
    <div class="address-right-top">
        <h3>Order Summary</h3>

        {{-- ================= BUY NOW ================= --}}
        @if(isset($isBuyNow) && $isBuyNow)

            <div class="address-right-box">
                <h4>1. {{ $buyNowItem['name'] }}</h4>
                <ul>
                    <li>
                        <p>Price</p>
                        <span>₹ {{ number_format($buyNowItem['price'],2) }}</span>
                    </li>
                    <li>
                        <p>Quantity</p>
                        <span>{{ $buyNowItem['quantity'] }}</span>
                    </li>
                    <li>
                        <p>Subtotal</p>
                        <span class="buy-now-item-subtotal">
                            ₹ {{ number_format($buyNowItem['price'] * $buyNowItem['quantity'],2) }}
                        </span>
                    </li>
                </ul>
            </div>

        {{-- ================= CART ================= --}}
        @else

            @php $subtotal = 0; @endphp

            @forelse($orderSummary['items'] as $item)
                @php $subtotal += ($item['price'] * $item['quantity']); @endphp

                <div class="address-right-box">
                    <h4>{{ $loop->iteration }}. {{ $item['name'] }}</h4>
                    <ul>
                        <li>
                            <p>Price / Piece</p>
                            <span>₹ {{ number_format($item['price'],2) }}</span>
                        </li>
                        <li>
                            <p>Quantity</p>
                            <span>{{ $item['quantity'] }}</span>
                        </li>
                        <li>
                            <p>Subtotal</p>
                            <span>
                                ₹ {{ number_format($item['price'] * $item['quantity'],2) }}
                            </span>
                        </li>
                    </ul>
                </div>
            @empty
                <div class="address-right-box">
                    <p>No items in cart</p>
                </div>
            @endforelse

        @endif

        {{-- ================= PRICE SUMMARY ================= --}}
        <div class="address-right-box">

            <ul>

                {{-- SUBTOTAL --}}
                <li>
                    <p>Subtotal</p>
                    <span id="subtotal">₹ {{ number_format($subtotal ?? 0,2) }}</span>
                </li>
              
                {{-- CGST --}}
                <li>
                    <p>CGST</p>
                    <span id="cgst">
                        ₹ {{ number_format($orderSummary['cgst'] ?? 0,2) }}
                    </span>
                </li>

                {{-- SGST --}}
                <li>
                    <p>SGST</p>
                    <span id="sgst">
                        ₹ {{ number_format($orderSummary['sgst'] ?? 0,2) }}
                    </span>
                </li>

                {{-- IGST --}}
                <li>
                    <p>IGST</p>
                    <span id="igst">
                        ₹ {{ number_format($orderSummary['igst'] ?? 0,2) }}
                    </span>
                </li>

                {{-- Courier Charges --}}
                <li>
                    <p>Courier Charges</p>
                    <span id="courier_charges">
                        ₹ {{ number_format($orderSummary['courier_total'] ?? 0,2) }}
                    </span>
                </li>

            </ul>
        </div>

        {{-- ================= GRAND TOTAL ================= --}}
        <div class="address-right-box border0">
            <ul>
                <li>
                    <p>Total</p>
                    <span class="total-text blueColor" id="grand_total">
                        ₹ {{ number_format($orderSummary['grand_total'] ?? 0,2) }}
                    </span>
                </li>
            </ul>
        </div>

    </div>

    {{-- ===== BUTTONS ===== --}}
    <div class="address-right-bottom">

        <form action="{{ route('vshop.payment.index') }}" method="GET" id="addressForm" class="mb-0" style="width: 50%">
            <input type="hidden" name="delivery_id" id="delivery_id">
    <input type="hidden" name="billing_id" id="billing_id">
            @if(isset($isBuyNow) && $isBuyNow)
                <input type="hidden" name="is_buy_now" value="1">
                <input type="hidden" name="product_id" value="{{ $buyNowItem['id'] }}">
                <input type="hidden" name="quantity" value="{{ $buyNowItem['quantity'] }}">
                <input type="hidden" name="price" value="{{ $buyNowItem['price'] }}">
            @endif

            <button type="submit" class="add-tocart-btn place-btn w-100">
                Proceed To Checkout
            </button>
        </form>

        <button class="shopping-btn"
            onclick="window.location.href='{{ route('vshop.cart.index') }}'">
            Continue Shopping
        </button>

    </div>
</div>

                    </div>
                </div>

                {{-- ================= RECOMMENDED PRODUCTS SECTION ================= --}}
                @if(isset($recommendedProducts) && count($recommendedProducts) > 0)
                <div class="wishlist-sec mb-4 mt-5" style="border-top: 1px solid #eee; padding-top: 20px;">
                    <div class="wishlist-header">
                        <h2>Recommended Products For You</h2>
                    </div>
                    <div class="wishlist-bottom">
                        <div class="recommended-product-wrapper owl-carousel">
                            @foreach($recommendedProducts as $product)
                                <div class="other-product-box item position-relative">
                                    @if(isset($product->recommendation_type))
                                        <span class="badge bg-info text-white position-absolute top-0 start-0 m-2" style="z-index:1; font-size:10px; text-transform:uppercase; letter-spacing:0.5px;">
                                            {{ str_replace('_', ' ', $product->recommendation_type) }}
                                        </span>
                                    @endif
                                    <div class="product-box">
                                        <div class="product-box-img">
                                           <a href="{{ route('productmanage.show', $product->id ?? 0) }}">
                                                <img src="{{ asset($product->thumbnail ?? 'images/banner-pic-main.webp') }}" alt="{{ $product->name ?? '' }}">
                                            </a>
                                        </div>
                                        <div class="product-bottom">
                                            <h3>
                                                <a href="{{ route('productmanage.show', $product->id ?? 0) }}">
                                                    {{ $product->name ?? '' }}
                                                </a>
                                            </h3>
                                            <div class="off-sec">
                                                @if($product->discount_price && $product->price > $product->discount_price)
                                                    <p class="old-rupess">₹ {{ number_format($product->price, 2) }}</p>
                                                    <p class="off-box">
                                                        {{ round((($product->price - $product->discount_price) / $product->price) * 100) }}% Off
                                                    </p>
                                                @else
                                                    <p class="old-rupess">₹ {{ number_format($product->price * 1.14, 2) }}</p>
                                                    <p class="off-box">14% Off</p>
                                                @endif
                                            </div>
                                            <div class="product-bottom-sec">
                                                <a href="#" class="add-cart-btn recommended-add-to-cart" 
                                                   data-product-id="{{ $product->id }}">
                                                    Add to Cart
                                                </a>
                                                <p class="new-rupess">
                                                    ₹ {{ number_format($product->discount_price ?? $product->price, 2) }}
                                                </p>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

<style>
/* Compact styles for Recommended Products section */
.recommended-product-wrapper .product-box {
    max-width: 190px !important;
    height: auto !important;
    min-height: unset !important;
    margin: 0 auto !important;
    border: 1px solid #e0e0e0 !important;
    border-radius: 8px !important;
    padding: 8px !important;
    background: #fff !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05) !important;
    display: flex !important;
    flex-direction: column !important;
}
.recommended-product-wrapper .product-box-img {
    height: 130px !important;
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    overflow: hidden !important;
    padding-top: 15px !important;
}
.recommended-product-wrapper .product-box-img a {
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: 100% !important;
    height: 100% !important;
}
.recommended-product-wrapper .product-box-img img {
    max-height: 100% !important;
    max-width: 100% !important;
    object-fit: contain !important;
    margin: 0 auto !important;
}
.recommended-product-wrapper .product-bottom h3 {
    font-size: 12px !important;
    line-height: 1.3 !important;
    height: 32px !important;
    margin: 6px 0 !important;
    overflow: hidden !important;
    display: -webkit-box !important;
    -webkit-line-clamp: 2 !important;
    -webkit-box-orient: vertical !important;
}
.recommended-product-wrapper .off-sec {
    margin-bottom: 4px !important;
    display: flex !important;
    align-items: center !important;
    gap: 4px !important;
}
.recommended-product-wrapper .off-sec p {
    font-size: 10px !important;
    margin: 0 !important;
}
.recommended-product-wrapper .product-bottom-sec {
    margin-top: 6px !important;
    display: flex !important;
    justify-content: space-between !important;
    align-items: center !important;
}
.recommended-product-wrapper .product-bottom-sec .new-rupess {
    font-size: 12px !important;
    font-weight: 700 !important;
    margin: 0 !important;
}
.recommended-product-wrapper .product-bottom-sec .add-cart-btn {
    padding: 3px 6px !important;
    font-size: 10px !important;
    border-radius: 4px !important;
    line-height: 1.2 !important;
}
</style>

<script>
    // Initialize owl carousel for recommended products
    $(document).ready(function() {
        if ($(".recommended-product-wrapper").length) {
            var recItemCount = $(".recommended-product-wrapper .item").length;
            $(".recommended-product-wrapper").owlCarousel({
                loop: recItemCount > 4 ? true : false,
                margin: 20,
                nav: false,
                dots: false,
                autoplay: recItemCount > 4 ? true : false,
                autoplayTimeout: 3000,
                responsive: {
                    0: { items: 1 },
                    600: { items: 2 },
                    1000: { items: 4 }
                }
            });
        }
    });
    // ✅ BASE URL
    var BASE_URL = "{{ url('/') }}";

    // ✅ CSRF GLOBAL SETUP (IMPORTANT)
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    // ===================================================
    // 🔥 GST UPDATE FUNCTION
    // ===================================================
    function updateAddressGST() {

        let delivery = $('input[name="delivery_radio"]:checked').val();
        let billing  = $('input[name="billing_radio"]:checked').val();

        console.log("Delivery:", delivery);
        console.log("Billing:", billing);

        if (!delivery || !billing) return;

        let requestData = {
            delivery_id: delivery,
            billing_id: billing,
            _token: "{{ csrf_token() }}"
        };

        // If Buy Now flow, pass buy now parameters
        let isBuyNow = $('input[name="is_buy_now"]').val();
        if (isBuyNow) {
            requestData.is_buy_now = 1;
            requestData.product_id = $('input[name="product_id"]').val();
            requestData.quantity = $('input[name="quantity"]').val();
            requestData.price = $('input[name="price"]').val();
        }

        $.ajax({
            url: "{{ route('vshop.update.address.session') }}",
            type: "POST",
            data: requestData,

            success: function (res) {

                console.log("✅ Response:", res);

                if(res.status){
                    $('#cgst').text('₹ ' + res.cgst);
                    $('#sgst').text('₹ ' + res.sgst);
                    $('#igst').text('₹ ' + res.igst);
                    $('#courier_charges').text('₹ ' + res.courier_total);
                    $('#grand_total').text('₹ ' + res.total);

                    if(res.subtotal){
                        $('#subtotal').text('₹ ' + res.subtotal);
                    }
                }

            },

            error: function (xhr) {
                console.error("❌ AJAX ERROR:", xhr.responseText);
            }
        });
    }

    // ===================================================
    // 🔥 PAGE LOAD AUTO CALL
    // ===================================================
    $(window).on('load', function () {
        updateAddressGST();
    });

    // ===================================================
    // 🔥 ADDRESS CHANGE EVENT
    // ===================================================
    $(document).on('change', 'input[name="delivery_radio"], input[name="billing_radio"]', function () {
        updateAddressGST();
    });
    
    
    
    $('#addressForm').on('submit', function(){

    let delivery = $('input[name="delivery_radio"]:checked').val();
    let billing  = $('input[name="billing_radio"]:checked').val();

    $('#delivery_id').val(delivery);
    $('#billing_id').val(billing);

});

</script>
@endsection




