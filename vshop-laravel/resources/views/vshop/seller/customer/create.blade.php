@extends('vshop.seller.main')
<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        font-size: 4px;
        width: 35px;
        height: 38px;
    }
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-dark {
        background: #233d78 !important; 
        border: 0 !important;
    }

    /* ===== SMALL RED CIRCLE DELETE ICON ===== */
    .remove-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #dc3545;
        color: #fff;
        border: none;
        font-size: 12px;
        cursor: pointer;
        padding: 0;
        transition: opacity 0.2s;
        border-radius: 5px;
    }
    .remove-icon:hover {
        opacity: 0.8;
    }
    .remove-icon i {
        pointer-events: none; /* ensures click goes to button */
        font-size: 13px;
    }
    /* ======================================== */
</style>

@section('content')

<div class="container-fluid py-4">

@php
$isEdit = !empty($customer) && !empty($customer->id);
@endphp

<form method="POST"
action="{{ $isEdit ? route('seller.customer.update',$customer->id) : route('seller.customer.store') }}"
enctype="multipart/form-data">
@csrf

@if($isEdit)
<input type="hidden" name="id" value="{{ $customer->id }}">
@endif


{{-- ================= HEADER ================= --}}
<div class="d-flex top-back-heading-sec mb-3">
<button onclick="goBack()" class="nav-btn">
    <i class="fa-solid fa-arrow-left"></i>
</button>

<h5 class="mb-0">
    {{ $isEdit ? 'Edit Customer' : 'Add Customer' }}
</h5>

<div>
@if(!$isEdit)
<a href="{{ route('seller.customer.bulk.page') }}"
class="btn btn-dark text-white">
Bulk Upload
</a>
@endif
</div>

</div>


<div class="card">
<div class="card-body">

{{-- ================= BASIC ================= --}}
<div class="row g-3">

<div class="col-md-4">
<span class="fw-bold">Name <span class="text-danger">*</span></span>

<input type="text"
name="name"
class="form-control"

value="{{ old('name',$customer->name ?? '') }}">

@error('name')
<small class="text-danger">{{ $message }}</small>
@enderror
</div>

<div class="col-md-4">
<span class="fw-bold">Mobile <span class="text-danger">*</span></span>

<input type="text"
name="mobile"
class="form-control"

pattern="[0-9]{10}"
maxlength="10"
title="Enter valid 10 digit mobile"
value="{{ old('mobile',$customer->phone ?? '') }}">

@error('mobile')
<small class="text-danger">{{ $message }}</small>
@enderror
</div>

<div class="col-md-4">
<span class="fw-bold">Email</span>

<input type="email"
name="email"
class="form-control"
value="{{ old('email',$customer->email ?? '') }}">

@error('email')
<small class="text-danger">{{ $message }}</small>
@enderror
</div>

</div>

<hr>

{{-- ================= EXTRA CUSTOMER INFO ================= --}}
<div class="row g-3 mt-1">

{{-- COMPANY --}}
<div class="col-md-3">
<span class="fw-bold">
Company
</span>

<input type="text"
name="company_name"
class="form-control"
value="{{ old('company_name',$customer->company_name ?? '') }}">

@error('company_name')
<small class="text-danger">
    {{ $message }}
</small>
@enderror
</div>

{{-- GSTIN --}}
<div class="col-md-3">
<span class="fw-bold">
GSTIN
</span>

<input type="text"
name="gstin"
class="form-control"
value="{{ old('gstin',$customer->gstin ?? '') }}">

@error('gstin')
<small class="text-danger">
    {{ $message }}
</small>
@enderror
</div>

{{-- GENDER --}}
<div class="col-md-3">
<span class="fw-bold">
Gender
</span>

<select name="gender"
class="form-control">

<option value="">
Select Gender
</option>

<option value="Male"
{{ old('gender',$customer->gender ?? '')=='Male' ? 'selected' : '' }}>
Male
</option>

<option value="Female"
{{ old('gender',$customer->gender ?? '')=='Female' ? 'selected' : '' }}>
Female
</option>

<option value="Other"
{{ old('gender',$customer->gender ?? '')=='Other' ? 'selected' : '' }}>
Other
</option>

</select>

@error('gender')
<small class="text-danger">
    {{ $message }}
</small>
@enderror
</div>

{{-- DOB --}}
<div class="col-md-3">
<span class="fw-bold">
DOB
</span>

<input type="date"
name="dob"
class="form-control"
value="{{ old('dob',$customer->dob ?? '') }}">

@error('dob')
<small class="text-danger">
    {{ $message }}
</small>
@enderror
</div>

{{-- TYPE --}}
<div class="col-md-3">
<span class="fw-bold">
Customer Type
</span>

<select name="type" class="form-control">

    <option value="">
        Select Type
    </option>

    @foreach($customerTypes as $value => $label)

        <option value="{{ $value }}"
        {{ old('type',$customer->type ?? '') == $value ? 'selected' : '' }}>

            {{ $label }}

        </option>

    @endforeach

</select>

@error('type')
<small class="text-danger">
    {{ $message }}
</small>
@enderror
</div>

<div class="col-md-3">

<span>Geo Location</span>

<button type="button"
onclick="getLocation()"
class="btn btn-dark w-100">

Auto Fetch

</button>

</div>

<div class="col-md-3">

<span>Photo</span>

<input type="file"
name="photo"
id="photoInput"
class="form-control">

<div style="
position:relative;
width:90px;
height:90px;
margin-top:10px;
display:inline-block;
overflow:visible;
">

<img id="previewImg"
src="{{ !empty($customer->profile_picture) ? asset($customer->profile_picture) : '' }}"
style="
width:100%;
height:100%;
object-fit:cover;
border-radius:8px;
border:1px solid #ddd;
{{ empty($customer->profile_picture)?'display:none;':'' }}
">

{{-- SMALL RED CIRCLE DELETE ICON FOR PHOTO --}}
<span id="removeBtn"
onclick="removePhoto()"
style="
position:absolute;
top:-8px;
right:-8px;
width:24px;
height:24px;
border-radius:50%;
background:#dc3545;
color:#fff;
display:flex;
align-items:center;
justify-content:center;
font-size:12px;
cursor:pointer;
z-index:9999;
border:2px solid #fff;
box-shadow:0 2px 6px rgba(0,0,0,0.25);
{{ empty($customer->profile_picture)?'display:none;':'' }}
">
    <i class="fa fa-trash"></i>
</span>

</div>

<input type="hidden"
name="remove_photo"
id="remove_photo"
value="0">

</div>

</div>

<hr>

@php
$billingData = (isset($billing) && count($billing) > 0)
    ? $billing
    : [null];

$deliveryData = (isset($delivery) && count($delivery) > 0)
    ? $delivery
    : [null];
@endphp
{{-- ================= BILLING ================= --}}
<h6 class="fw-bold">
Customer Billing Address
<span class="text-danger">*</span>
</h6>

<div id="billingWrapper">

@foreach($billingData as $b)

<div class="billingBlock border p-3 mb-3"
data-state-id="{{ $b->state_id ?? '' }}"
data-city-id="{{ $b->city_id ?? '' }}">

<input type="hidden" name="billing_id[]" value="{{ $b->id ?? '' }}">

{{-- SMALL RED CIRCLE DELETE ICON --}}
<button type="button"
class="remove-icon float-end">
    <i class="fa fa-trash"></i>
</button>

<div class="row g-3">

{{-- COUNTRY --}}
<div class="col-md-3">

<span>
Country <span class="text-danger">*</span>
</span>

<select name="billing_country[]"
class="form-control country"
>

<option value="">Select Country</option>

@foreach($countries as $id=>$name)

<option value="{{$id}}"
{{ ($b->country_id ?? '')==$id?'selected':'' }}>

{{$name}}

</option>

@endforeach

</select>

@error('billing_country.0')
<small class="text-danger">{{ $message }}</small>
@enderror

</div>

{{-- STATE --}}
<div class="col-md-3">

<span>
State <span class="text-danger">*</span>
</span>

<select name="billing_state[]"
class="form-control state"
>

<option value="">Select State</option>

</select>

@error('billing_state.0')
<small class="text-danger">{{ $message }}</small>
@enderror

</div>

{{-- CITY --}}
<div class="col-md-3">

<span>
City <span class="text-danger">*</span>
</span>

<select name="billing_city[]"
class="form-control city"
>

<option value="">Select City</option>

</select>

@error('billing_city.0')
<small class="text-danger">{{ $message }}</small>
@enderror

</div>

<div class="col-md-3">

<span>
Pin <span class="text-danger">*</span>
</span>

<select name="billing_pincode[]"
class="form-control pincode"
>

<option value="">
Select Pincode
</option>

@if(!empty($b->pincode))
<option value="{{ $b->pincode }}" selected>
{{ $b->pincode }}
</option>
@endif

<option value="other">
Other / Manual Entry
</option>

</select>

<input type="text"
class="form-control manualPincode mt-2"
placeholder="Enter Pincode"
style="display:none;">

{{-- ERROR MESSAGE --}}
@error('billing_pincode.0')
<small class="text-danger d-block mt-1">
    {{ $message }}
</small>
@enderror

</div>

{{-- ADDRESS --}}
<div class="col-md-12">

<span>
Address <span class="text-danger">*</span>
</span>

<input type="text"
name="billing_address[]"
class="form-control address"

value="{{ $b->address_line1 ?? '' }}">

@error('billing_address.0')
<small class="text-danger">{{ $message }}</small>
@enderror

</div>

</div>
</div>

@endforeach

</div>

<button type="button"
onclick="addBilling()"
class="btn btn-outline-primary btn-sm">

+ Add Billing

</button>

<hr>

{{-- ================= DELIVERY ================= --}}
<h6 class="fw-bold">
Customer Delivery Address
<span class="text-danger">*</span>
</h6>

<div id="deliveryWrapper">
@foreach($deliveryData as $d)

<div class="deliveryBlock border p-3 mb-3"
data-state-id="{{ $d->state_id ?? '' }}"
data-city-id="{{ $d->city_id ?? '' }}">

<input type="hidden" name="delivery_id[]" value="{{ $d->id ?? '' }}">

{{-- SMALL RED CIRCLE DELETE ICON --}}
<button type="button"
class="remove-icon float-end">
    <i class="fa fa-trash"></i>
</button>

<div class="row g-3">

{{-- COUNTRY --}}
<div class="col-md-3">

<span>
Country <span class="text-danger">*</span>
</span>

<select name="delivery_country[]"
class="form-control country"
>

<option value="">Select Country</option>

@foreach($countries as $id=>$name)

<option value="{{$id}}"
{{ ($d->country_id ?? '')==$id?'selected':'' }}>

{{$name}}

</option>

@endforeach

</select>

@error('delivery_country.0')
<small class="text-danger">{{ $message }}</small>
@enderror

</div>

{{-- STATE --}}
<div class="col-md-3">

<span>
State <span class="text-danger">*</span>
</span>

<select name="delivery_state[]"
class="form-control state"
>

<option value="">Select State</option>

</select>

@error('delivery_state.0')
<small class="text-danger">{{ $message }}</small>
@enderror

</div>

{{-- CITY --}}
<div class="col-md-3">

<span>
City <span class="text-danger">*</span>
</span>

<select name="delivery_city[]"
class="form-control city"
>

<option value="">Select City</option>

</select>

@error('delivery_city.0')
<small class="text-danger">{{ $message }}</small>
@enderror

</div>

<div class="col-md-3">

<span>
Pin <span class="text-danger">*</span>
</span>

<select name="delivery_pincode[]"
class="form-control pincode"
>

<option value="">
Select Pincode
</option>

@if(!empty($d->pincode))
<option value="{{ $d->pincode }}" selected>
{{ $d->pincode }}
</option>
@endif

<option value="other">
Other / Manual Entry
</option>

</select>

<input type="text"
class="form-control manualPincode mt-2"
placeholder="Enter Pincode"
style="display:none;">

@error('delivery_pincode.0')
<small class="text-danger d-block mt-1">
    {{ $message }}
</small>
@enderror

</div>

{{-- ADDRESS --}}
<div class="col-md-12">

<span>
Address <span class="text-danger">*</span>
</span>

<input type="text"
name="delivery_address[]"
class="form-control address"

value="{{ $d->address_line1 ?? '' }}">

@error('delivery_address.0')
<small class="text-danger">{{ $message }}</small>
@enderror

</div>

</div>
</div>

@endforeach

</div>

<button type="button"
onclick="addDelivery()"
class="btn btn-outline-success btn-sm">

+ Add Delivery

</button>



<div class="text-end mt-4">

<button class="btn btn-primary">

{{ $isEdit ? 'Update Customer' : 'Create Customer' }}

</button>

</div>

</div>
</div>

</form>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

// ===================== DOCUMENT READY =====================
$(document).ready(function(){

    $('.billingBlock, .deliveryBlock').each(function(){

        loadExistingAddress($(this));

    });

});


// ===================== LOAD EXISTING EDIT DATA =====================
function loadExistingAddress(block){

    let country_id = block.find('.country').val();

    let state_id = block.attr('data-state-id');

    let city_id = block.attr('data-city-id');

    if(!country_id) return;

    // LOAD STATES
    $.get('/seller/states/' + country_id, function(states){

        let stateSelect = block.find('.state');

        stateSelect.html(`
            <option value="">Select State</option>
        `);

        $.each(states, function(id, name){

            let selected = (id == state_id)
            ? 'selected'
            : '';

            stateSelect.append(`
                <option value="${id}" ${selected}>
                    ${name}
                </option>
            `);

        });

        // LOAD CITIES
        if(state_id){

            $.get('/seller/cities/' + state_id, function(cities){

                let citySelect = block.find('.city');

                citySelect.html(`
                    <option value="">Select City</option>
                `);

                $.each(cities, function(id, name){

                    let selected = (id == city_id)
                    ? 'selected'
                    : '';

                    citySelect.append(`
                        <option value="${id}" ${selected}>
                            ${name}
                        </option>
                    `);

                });

                // AUTO FETCH PINCODE ON EDIT
                setTimeout(function(){

                    citySelect.trigger('change');

                }, 300);

            });

        }

    });

}


// ===================== COUNTRY CHANGE =====================
$(document).on('change', '.country', function(){

    let parent = $(this).closest('.row');

    let countryId = $(this).val();

    let state = parent.find('.state');

    let city = parent.find('.city');

    let pincode = parent.find('.pincode');

    // RESET
    state.html(`
        <option value="">Loading...</option>
    `);

    city.html(`
        <option value="">Select City</option>
    `);

    pincode.html(`
        <option value="">Select Pincode</option>
        <option value="other">Other / Manual Entry</option>
    `);

    if(!countryId){

        state.html(`
            <option value="">Select State</option>
        `);

        return;
    }

    // LOAD STATES
    $.get('/seller/states/' + countryId, function(res){

        state.html(`
            <option value="">Select State</option>
        `);

        $.each(res, function(i, n){

            state.append(`
                <option value="${i}">
                    ${n}
                </option>
            `);

        });

    });

});


// ===================== STATE CHANGE =====================
$(document).on('change', '.state', function(){

    let parent = $(this).closest('.row');

    let stateId = $(this).val();

    let city = parent.find('.city');

    let pincode = parent.find('.pincode');

    // RESET
    city.html(`
        <option value="">Loading...</option>
    `);

    pincode.html(`
        <option value="">Select Pincode</option>
        <option value="other">Other / Manual Entry</option>
    `);

    if(!stateId){

        city.html(`
            <option value="">Select City</option>
        `);

        return;
    }

    // LOAD CITIES
    $.get('/seller/cities/' + stateId, function(res){

        city.html(`
            <option value="">Select City</option>
        `);

        $.each(res, function(i, n){

            city.append(`
                <option value="${i}">
                    ${n}
                </option>
            `);

        });

    });

});


// ===================== CITY CHANGE =====================
$(document).on('change', '.city', function(){

    let cityId = $(this).val();

    let parent = $(this).closest('.row');

    let cityText = $(this)
    .find("option:selected")
    .text()
    .trim();

    let stateText = parent
    .find('.state option:selected')
    .text()
    .trim();

    let countryText = parent
    .find('.country option:selected')
    .text()
    .trim();

    let pincodeBox = parent.find('.pincode');

    let manualBox = parent.find('.manualPincode');

    // RESET
    manualBox.hide().val('');

    pincodeBox.html(`
        <option value="">
            Loading...
        </option>
    `);

    if(!cityId){

        pincodeBox.html(`
            <option value="">
                Select Pincode
            </option>

            <option value="other">
                Other / Manual Entry
            </option>
        `);

        return;
    }

    // FETCH API
    fetch(`https://api.postalpincode.in/postoffice/${cityText}`)

    .then(res => res.json())

    .then(data => {

        pincodeBox.html(`
            <option value="">
                Select Pincode
            </option>
        `);

        let addedPins = [];

        if(
            data &&
            data[0] &&
            data[0].Status === "Success" &&
            data[0].PostOffice &&
            data[0].PostOffice.length > 0
        ){

            data[0].PostOffice.forEach(function(po){

                let apiState =
                (po.State || '').toLowerCase();

                let apiCountry =
                (po.Country || '').toLowerCase();

                let pin =
                po.Pincode || '';

                // FILTER STATE + COUNTRY
                if(
                    apiState.includes(stateText.toLowerCase()) &&
                    apiCountry.includes(countryText.toLowerCase())
                ){

                    // REMOVE DUPLICATES
                    if(
                        pin &&
                        !addedPins.includes(pin)
                    ){

                        addedPins.push(pin);

                        pincodeBox.append(`
                            <option value="${pin}">
                                ${pin}
                            </option>
                        `);

                    }

                }

            });

        }

        // ALWAYS SHOW OTHER OPTION
        pincodeBox.append(`
            <option value="other">
                Other / Manual Entry
            </option>
        `);

    })

    .catch(err => {

        console.log(err);

        pincodeBox.html(`
            <option value="">
                Select Pincode
            </option>

            <option value="other">
                Other / Manual Entry
            </option>
        `);

    });

});


// ===================== PINCODE CHANGE =====================
$(document).on('change', '.pincode', function(){

    let parent = $(this).closest('.col-md-3');

    let manualBox = parent.find('.manualPincode');

    if($(this).val() == 'other'){

        manualBox.show();

        manualBox.attr('required', true);

    }else{

        manualBox.hide();

        manualBox.removeAttr('required');

        manualBox.val('');

    }

});


// ===================== RESET BLOCK =====================
function resetAddressBlock(block){

    // CLEAR IDS
    block.find('input[type="hidden"]').val('');

    // CLEAR ADDRESS
    block.find('.address').val('');

    // RESET COUNTRY
    block.find('.country').val('');

    // RESET STATE
    block.find('.state').html(`
        <option value="">Select State</option>
    `);

    // RESET CITY
    block.find('.city').html(`
        <option value="">Select City</option>
    `);

    // RESET PINCODE
    block.find('.pincode').html(`
        <option value="">Select Pincode</option>
        <option value="other">Other / Manual Entry</option>
    `);

    // RESET MANUAL PINCODE
    block.find('.manualPincode')
    .hide()
    .val('');

}


// ===================== ADD BILLING =====================
function addBilling(){

    let clone = $('.billingBlock:first').clone();

    resetAddressBlock(clone);

    $('#billingWrapper').append(clone);

}


// ===================== ADD DELIVERY =====================
function addDelivery(){

    let clone = $('.deliveryBlock:first').clone();

    resetAddressBlock(clone);

    $('#deliveryWrapper').append(clone);

}


// ===================== REMOVE BLOCK =====================
$(document).on('click', '.removeBlock', function(){

    // BILLING
    if($(this).closest('.billingBlock').length){

        if($('.billingBlock').length > 1){

            $(this).closest('.billingBlock').remove();

        }else{

            resetAddressBlock($(this).closest('.billingBlock'));

        }

    }

    // DELIVERY
    else{

        if($('.deliveryBlock').length > 1){

            $(this).closest('.deliveryBlock').remove();

        }else{

            resetAddressBlock($(this).closest('.deliveryBlock'));

        }

    }

});


// ===================== PHOTO PREVIEW =====================
$('#photoInput').on('change', function(e){

    let file = e.target.files[0];

    if(!file){

        removePhoto();

        return;
    }

    let reader = new FileReader();

    reader.onload = function(e){

        $('#previewImg')
        .attr('src', e.target.result)
        .show();

        $('#removeBtn').show();

        $('#remove_photo').val(0);

    };

    reader.readAsDataURL(file);

});


// ===================== REMOVE PHOTO =====================
function removePhoto(){

    $('#previewImg')
    .attr('src', '')
    .hide();

    $('#removeBtn').hide();

    $('#photoInput').val('');

    $('#remove_photo').val(1);

}


function getLocation(){

    if(!navigator.geolocation){

        alert("Geolocation not supported");

        return;
    }

    navigator.geolocation.getCurrentPosition(

        function(pos){

            let lat = pos.coords.latitude;

            let lng = pos.coords.longitude;

            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lng}`)

            .then(response => response.json())

            .then(data => {

                if(!data || !data.address){

                    alert('Location details not found');

                    return;
                }

                let addr = data.address;

                let countryName =
                addr.country || '';

                let stateName =
                addr.state ||
                addr.region ||
                '';

                let cityName = addr.city || addr.town || addr.village || addr.county || '';
                
                let possibleCities = [
                    addr.city, addr.town, addr.village, addr.county, addr.state_district, addr.suburb
                ].filter(Boolean).map(s => s.toLowerCase());

                let pin =
                addr.postcode || '';

                let fullAddress =
                data.display_name || '';

                $('.billingBlock, .deliveryBlock').each(function(){

                    let block = $(this);

                    // ================= ADDRESS =================
                    block.find('.address')
                    .val(fullAddress);

                    // ================= COUNTRY =================
                    let countrySelect =
                    block.find('.country');

                    countrySelect.val('');

                    countrySelect.find('option').each(function(){

                        let txt =
                        $(this).text().trim().toLowerCase();

                        if(
                            txt.includes(countryName.toLowerCase())
                        ){

                            $(this).prop('selected', true);

                        }

                    });

                    let countryId =
                    countrySelect.val();

                    if(!countryId) return;

                    // ================= LOAD STATES =================
                    $.get('/seller/states/' + countryId, function(states){

                        let stateSelect =
                        block.find('.state');

                        stateSelect.html(`
                            <option value="">
                                Select State
                            </option>
                        `);

                        let selectedStateId = '';

                        $.each(states, function(id, name){

                            let selected = '';

                            if(
                                stateName && (
                                    name.toLowerCase().includes(stateName.toLowerCase()) ||
                                    stateName.toLowerCase().includes(name.toLowerCase())
                                )
                            ){
                                selected = 'selected';
                                selectedStateId = id;
                            }

                            stateSelect.append(`
                                <option value="${id}" ${selected}>
                                    ${name}
                                </option>
                            `);

                        });

                        // ================= LOAD CITIES =================
                        if(selectedStateId){

                            $.get('/seller/cities/' + selectedStateId, function(cities){

                                let citySelect =
                                block.find('.city');

                                citySelect.html(`
                                    <option value="">
                                        Select City
                                    </option>
                                `);

                                let bestMatchId = '';
                                for(let i=0; i<possibleCities.length; i++){
                                    let pc = possibleCities[i];
                                    $.each(cities, function(id, name){
                                        if(bestMatchId) return;
                                        let dbName = name.toLowerCase();
                                        if(dbName === pc || dbName.includes(pc) || pc.includes(dbName)){
                                            bestMatchId = id;
                                        }
                                    });
                                    if(bestMatchId) break;
                                }

                                $.each(cities, function(id, name){

                                    let selected = (id == bestMatchId) ? 'selected' : '';

                                    citySelect.append(`
                                        <option value="${id}" ${selected}>
                                            ${name}
                                        </option>
                                    `);

                                });

                                // ================= PINCODE =================
                                let pincodeBox =
                                block.find('.pincode');

                                let manualBox =
                                block.find('.manualPincode');

                                manualBox.hide().val('');

                                pincodeBox.html(`
                                    <option value="">
                                        Select Pincode
                                    </option>
                                `);

                                let addedPins = [];

                                // CURRENT PINCODE FIRST
                                if(pin){

                                    addedPins.push(pin);

                                    pincodeBox.append(`
                                        <option value="${pin}" selected>
                                            ${pin}
                                        </option>
                                    `);

                                }

                                // FETCH CITY PINCODES
                                fetch(`https://api.postalpincode.in/postoffice/${cityName}`)

                                .then(res => res.json())

                                .then(pinData => {

                                    if(
                                        pinData &&
                                        pinData[0] &&
                                        pinData[0].Status === "Success" &&
                                        pinData[0].PostOffice
                                    ){

                                        pinData[0].PostOffice.forEach(function(po){

                                            let apiPin =
                                            po.Pincode || '';

                                            if(
                                                apiPin &&
                                                !addedPins.includes(apiPin)
                                            ){

                                                addedPins.push(apiPin);

                                                pincodeBox.append(`
                                                    <option value="${apiPin}">
                                                        ${apiPin}
                                                    </option>
                                                `);

                                            }

                                        });

                                    }

                                    // ALWAYS OTHER OPTION
                                    pincodeBox.append(`
                                        <option value="other">
                                            Other / Manual Entry
                                        </option>
                                    `);

                                    // FORCE SELECT CURRENT PINCODE
                                    if(pin){

                                        pincodeBox.val(pin);

                                    }

                                });

                            });

                        }

                    });

                });

            })

            .catch(err => {

                console.log(err);

                alert('Unable to fetch location');

            });

        },

        function(error){

            console.log(error);

            alert("Location permission denied");

        }

    );

}


// ===================== REMOVE VALIDATION ERROR =====================
$(document).on('keyup change', 'input, select, textarea', function(){

    let field = $(this);

    field.removeClass('is-invalid');

    field.closest('.col-md-3, .col-md-4, .col-md-12')
    .find('small.text-danger')
    .remove();

});

</script>
@endsection