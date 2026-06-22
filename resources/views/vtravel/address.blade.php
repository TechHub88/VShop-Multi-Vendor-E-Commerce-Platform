@extends('vtravel.main')

@section('title', 'Select Delivery Address')

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
                            <li><a href="{{ route('vtravel.cart.index') }}">Wishlist - Cart</a> /</li>
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
                                        <ul>
                                            <li class="pl-0">Delivery Address</li>
                                            <li>Billing Address</li>
                                            <li class="pr-0">Address</li>
                                        </ul>
                                    </div>

                                    <div class="address-header-right">
                                        <button class="add-address-btn"
                                            onclick="window.location.href='{{ route('vtravel.address.create') }}'">
                                            Add Another Address
                                        </button>
                                        <select class="add-address-btn gstn-btn">
                                            <option value="">GISTIN : ADKL568092D</option>
                                        </select>
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
                                                        <button class="delite-btn">
                                                            <i class="fa-solid fa-trash"></i>
                                                        </button>
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

                                @foreach($orderSummary['items'] as $item)
                                <div class="address-right-box">
                                    <h4>{{ $loop->iteration }}. {{ $item['name'] }}</h4>
                                    <ul>
                                        <li>
                                            <p>Price / Piece</p>
                                            <span>₹ {{ number_format($item['price'],2) }}</span>
                                        </li>
                                    </ul>
                                </div>
                                @endforeach

                             <div class="address-right-box">
    <ul>

        <li>
            <p>CGST</p>
            <span>₹ {{ number_format($orderSummary['cgst'] ?? 0,2) }}</span>
        </li>

        <li>
            <p>SGST</p>
            <span>₹ {{ number_format($orderSummary['sgst'] ?? 0,2) }}</span>
        </li>

        <li>
            <p>IGST</p>
            <span>₹ {{ number_format($orderSummary['igst'] ?? 0,2) }}</span>
        </li>

        <li>
            <p>Courier</p>
            <span>₹ {{ number_format($orderSummary['courier_total'],2) }}</span>
        </li>

    </ul>
</div>

                                <div class="address-right-box border0">
                                    <ul>
                                        <li>
                                            <p>Total</p>
                                            <span class="total-text blueColor">
                                                ₹ {{ number_format($orderSummary['grand_total'],2) }}
                                            </span>
                                        </li>
                                    </ul>
                                </div>
                            </div>

                            {{-- ===== BUTTONS ===== --}}
                            <div class="address-right-bottom">
                           
                                
                                <form action="{{ route('vtravel.payment.index') }}" method="GET" id="addressForm" class="mb-0" style="width: 50%">
                            <input type="hidden" name="delivery_id" id="delivery_id">
                            <input type="hidden" name="billing_id" id="billing_id">
                        
                            <button type="submit" class="add-tocart-btn place-btn w-100">Proceed To Checkout</button>
                        </form>

                                <button class="shopping-btn"
                                    onclick="window.location.href='{{ route('vtravel.cart.index') }}'">
                                    Continue Shopping
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

<style>
    .shopping-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    white-space: nowrap;          /* ❗ text break band */
    padding: 10px 22px;
    border-radius: 30px;          /* pill shape */
    font-size: 14px;
    font-weight: 500;
    line-height: 1;
    border: 1px solid #1d4ed8;
    background: #fff;
    color: #1d4ed8;
    transition: 0.3s;
    min-width: 160px;             /* ❗ width fix */
}

.shopping-btn:hover {
    background: #004392;
    color: #fff;
}

</style>
{{-- ================= JS ================= --}}

@section('scripts')
<script>
$(document).ready(function () {

    $('#addressForm').on('submit', function (e) {

        let delivery = $('input[name="delivery_radio"]:checked').val();
        let billing  = $('input[name="billing_radio"]:checked').val();

        if (!delivery || !billing) {
            e.preventDefault();
            alert('Please select delivery & billing address');
            return false;
        }

        $('#delivery_id').val(delivery);
        $('#billing_id').val(billing);
    });

});
</script>
@endsection

