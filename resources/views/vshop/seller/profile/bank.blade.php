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
.btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-outline-primary {
        border-color: #084f9d !important;
        color: #084f9d !important;
    }
    .btn-outline-primary:hover {
        background: #084f9d !important;
        color: #fff !important;
    }
</style>

@section('content')

@php
$seller = auth()->guard('seller')->user();
@endphp

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
  <button onclick="window.location.href='{{ route('seller.contact') }}'" 
        class="nav-btn">
    <i class="fa-solid fa-arrow-left"></i>
</button>
</div>

    <div class="marketplace-wrapper">

        {{-- LEFT --}}
        <div class="marketplace-left">

            <div class="tab-pane fade show active" id="bank">

                <form method="POST"
                      action="{{ route('seller.bank') }}"
                      enctype="multipart/form-data"
                      id="bankForm">

                    @csrf

                   
                   

                    <div class="card shadow-sm border-0 rounded-4">

                        <div class="card-body p-4">

                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

                                <div>
                                    <h4 class="text-primary fw-bold mb-1">
                                        Bank Account Details
                                    </h4>

                                    <small class="text-muted">
                                        Logged in seller bank information
                                    </small>
                                </div>

                                <a href="{{ route('seller.bank.list') }}"
                                   class="btn btn-outline-primary rounded-pill px-4">
                                    <i class="fa fa-list me-2"></i>
                                    View Bank Details
                                </a>

                            </div>

                            <div class="row">

                                {{-- ACCOUNT NAME --}}
                                <div class="col-md-6 mb-4">

                                    <label class="fw-semibold mb-2">
                                        Account Name
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="bank_account_name"
                                           id="accountName"
                                           autocomplete="off"
                                           value="{{ old('bank_account_name',$seller->bank_account_name) }}"
                                           class="form-control form-control-lg rounded-3 @error('bank_account_name') is-invalid @enderror">

                                    @error('bank_account_name')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        Account name is required
                                    </div>
                                    @enderror

                                </div>

                                {{-- ACCOUNT NUMBER --}}
                                <div class="col-md-6 mb-4">

                                    <label class="fw-semibold mb-2">
                                        Account Number
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           id="accountNumber"
                                           maxlength="18"
                                           autocomplete="off"
                                           value="{{ $seller->bank_account_number ? 'XXXXXX'.substr($seller->bank_account_number,-4) : '' }}"
                                           class="form-control form-control-lg rounded-3 @error('bank_account_number') is-invalid @enderror">

                                    <input type="hidden"
                                           name="bank_account_number"
                                           id="realAccountNumber"
                                           value="{{ old('bank_account_number',$seller->bank_account_number) }}">

                                    <small class="text-danger d-none mt-1" id="accError"></small>

                                    @error('bank_account_number')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        Valid account number required
                                    </div>
                                    @enderror

                                </div>

                                {{-- COUNTRY --}}
                                <div class="col-md-4 mb-4">

                                    <label class="fw-semibold mb-2">
                                        Country
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select name="bank_country"
                                            id="bank_country"
                                            class="form-select form-select-lg rounded-3 @error('bank_country') is-invalid @enderror">

                                        <option value="">Select Country</option>

                                        @foreach($countries as $c)

                                            <option value="{{ $c->id }}"
                                                {{ (string)$c->id === (string)old('bank_country',$seller->bank_country) ? 'selected' : '' }}>
                                                {{ $c->name }}
                                            </option>

                                        @endforeach

                                    </select>

                                    @error('bank_country')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        Please select country
                                    </div>
                                    @enderror

                                </div>

                                {{-- STATE --}}
                                <div class="col-md-4 mb-4">

                                    <label class="fw-semibold mb-2">
                                        State
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select name="bank_state"
                                            id="bank_state"
                                            class="form-select form-select-lg rounded-3 @error('bank_state') is-invalid @enderror">

                                        <option value="">Select State</option>

                                    </select>

                                    @error('bank_state')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        Please select state
                                    </div>
                                    @enderror

                                </div>

                                {{-- CITY --}}
                                <div class="col-md-4 mb-4">

                                    <label class="fw-semibold mb-2">
                                        City
                                        <span class="text-danger">*</span>
                                    </label>

                                    <select name="bank_city"
                                            id="bank_city"
                                            class="form-select form-select-lg rounded-3 @error('bank_city') is-invalid @enderror">

                                        <option value="">Select City</option>

                                    </select>

                                    @error('bank_city')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        Please select city
                                    </div>
                                    @enderror

                                </div>

                                {{-- IFSC --}}
                                <div class="col-md-4 mb-4">

                                    <label class="fw-semibold mb-2">
                                        IFSC Code
                                        <span class="text-danger" id="ifscRequired">*</span>
                                    </label>

                                    <input type="text"
                                           name="ifsc_code"
                                           autocomplete="off"
                                           value="{{ old('ifsc_code',$seller->ifsc_code) }}"
                                           class="form-control form-control-lg rounded-3 text-uppercase @error('ifsc_code') is-invalid @enderror">

                                    @error('ifsc_code')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @enderror

                                    <div class="invalid-feedback ifsc-error">
                                        IFSC code is required
                                    </div>

                                </div>

                                {{-- SWIFT --}}
                                <div class="col-md-4 mb-4">

                                    <label class="fw-semibold mb-2">
                                        SWIFT Code
                                        <span class="text-danger"
                                              id="swiftRequired"
                                              style="display:none;">*</span>
                                    </label>

                                    <input type="text"
                                           name="swift_code"
                                           autocomplete="off"
                                           value="{{ old('swift_code',$seller->swift_code) }}"
                                           class="form-control form-control-lg rounded-3 text-uppercase @error('swift_code') is-invalid @enderror">

                                    @error('swift_code')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        Swift code required
                                    </div>
                                    @enderror

                                </div>

                                {{-- BIC --}}
                                <div class="col-md-4 mb-4">

                                    <label class="fw-semibold mb-2">
                                        BIC Code
                                        <span class="text-danger"
                                              id="bicRequired"
                                              style="display:none;">*</span>
                                    </label>

                                    <input type="text"
                                           name="bic_code"
                                           autocomplete="off"
                                           value="{{ old('bic_code',$seller->bic_code) }}"
                                           class="form-control form-control-lg rounded-3 text-uppercase @error('bic_code') is-invalid @enderror">

                                    @error('bic_code')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        BIC code required
                                    </div>
                                    @enderror

                                </div>

                                {{-- BRANCH --}}
                                <div class="col-md-6 mb-4">

                                    <label class="fw-semibold mb-2">
                                        Branch Name
                                        <span class="text-danger">*</span>
                                    </label>

                                    <input type="text"
                                           name="branch_name"
                                           autocomplete="off"
                                           value="{{ old('branch_name',$seller->branch_name) }}"
                                           class="form-control form-control-lg rounded-3 @error('branch_name') is-invalid @enderror">

                                    @error('branch_name')
                                    <div class="invalid-feedback d-block">
                                        {{ $message }}
                                    </div>
                                    @else
                                    <div class="invalid-feedback">
                                        Branch name required
                                    </div>
                                    @enderror

                                </div>

                                {{-- CHEQUE --}}
                              <div class="col-md-6 mb-4">

    <label class="fw-semibold mb-2">
        Cancel Cheque
        <span class="text-danger">*</span>
    </label>

    <input type="file"
           name="cancel_cheque"
           id="cancelCheque"
           accept=".jpg,.jpeg,.png,.pdf"
           class="form-control form-control-lg rounded-3 @error('cancel_cheque') is-invalid @enderror">

    @error('cancel_cheque')
        <div class="invalid-feedback d-block">
            {{ $message }}
        </div>
    @else
        <div class="invalid-feedback">
            Cancel cheque required
        </div>
    @enderror

    {{-- FILE VIEW LOGIC --}}
  @if(!empty($seller->cancel_cheque) && file_exists(public_path($seller->cancel_cheque)))

    <div class="mt-3 d-flex align-items-center gap-3 flex-wrap">

        <small class="text-success fw-semibold d-flex align-items-center">
            <i class="fa fa-check-circle me-1"></i>
            Current file uploaded successfully
        </small>

        <a href="{{ asset($seller->cancel_cheque) }}"
           target="_blank"
           class="btn btn-primary btn-sm px-4 py-2 rounded-3 shadow-sm d-inline-flex align-items-center">

          
            View File

        </a>

    </div>

@endif

</div>

                                {{-- STATUS --}}
                                <div class="col-md-12 mb-3">

                                    @php
                                        $bankStatus = $seller->bank_status ?? 'na';
                                    @endphp

                                    <div class="d-flex align-items-center gap-2">

                                        <span class="fw-semibold">
                                            Verification Status :
                                        </span>

                                        <span class="badge px-3 py-2 rounded-pill
                                            {{ $bankStatus=='approved' ? 'bg-success' :
                                               ($bankStatus=='rejected' ? 'bg-danger' :
                                               ($bankStatus=='pending' ? 'bg-warning text-dark' :
                                               'bg-secondary')) }}">

                                            {{ ucfirst($bankStatus) }}

                                        </span>

                                    </div>

                                </div>

                            </div>

                            <hr>

                            <div class="text-end">

                                <button type="submit"
                                        class="btn btn-success btn-lg px-5 rounded-pill">

                                    <i class="fa fa-save me-2"></i>

                                    Save Bank Details

                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="marketplace-right">

            <div class="profile-right">

                @include('vshop.seller.partials.profile-progress')

            </div>

        </div>

    </div>

</div>


<style>

.ifsc-error{
    display:none;
    width:100%;
    margin-top:5px;
    font-size:.875em;
    color:#dc3545;
}

</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>

let bankCountry = "{{ old('bank_country',$seller->bank_country) }}";
let bankState   = "{{ old('bank_state',$seller->bank_state) }}";
let bankCity    = "{{ old('bank_city',$seller->bank_city) }}";

$(document).ready(function(){

    /*
    |--------------------------------------------------------------------------
    | LOAD OLD STATE/CITY
    |--------------------------------------------------------------------------
    */

    if(bankCountry){

        $('#bank_country').val(bankCountry);

        loadBankStates(bankCountry, bankState);

    }else{

        $('#bank_state').html('<option value="">Select State</option>');

        $('#bank_city').html('<option value="">Select City</option>');
    }

    /*
    |--------------------------------------------------------------------------
    | FORM SUBMIT
    |--------------------------------------------------------------------------
    */

    $('#bankForm').on('submit', function(e){

        let valid = validateBankForm();

        if(!valid){

            e.preventDefault();

            $('html, body').animate({

                scrollTop: $('.is-invalid:first').offset().top - 120

            }, 500);
        }
    });

});


/*
|--------------------------------------------------------------------------
| ACCOUNT NUMBER MASKING
|--------------------------------------------------------------------------
*/

document.addEventListener("DOMContentLoaded", function(){

    const display = document.getElementById('accountNumber');

    const real = document.getElementById('realAccountNumber');

    const error = document.getElementById('accError');

    function valid(v){

        return /^[0-9]{6,18}$/.test(v);
    }

    function show(msg){

        display.classList.add("is-invalid");

        error.classList.remove("d-none");

        error.innerText = msg;
    }

    function clear(){

        display.classList.remove("is-invalid");

        error.classList.add("d-none");
    }

    /*
    |--------------------------------------------------------------------------
    | INPUT
    |--------------------------------------------------------------------------
    */

    display.addEventListener('input', function(){

        let value = this.value.replace(/\D/g,'');

        this.value = value;

        real.value = value;

        if(valid(value)){

            clear();

        }else{

            show("Account number must be 6 to 18 digits");
        }
    });

    /*
    |--------------------------------------------------------------------------
    | FOCUS
    |--------------------------------------------------------------------------
    */

    display.addEventListener('focus', function(){

        this.value = real.value;
    });

    /*
    |--------------------------------------------------------------------------
    | BLUR
    |--------------------------------------------------------------------------
    */

    display.addEventListener('blur', function(){

        if(valid(real.value)){

            this.value = 'XXXXXX' + real.value.slice(-4);

            clear();
        }
    });

});


/*
|--------------------------------------------------------------------------
| MAIN VALIDATION
|--------------------------------------------------------------------------
*/

function validateBankForm(){

    let isValid = true;

    /*
    |--------------------------------------------------------------------------
    | RESET ERRORS
    |--------------------------------------------------------------------------
    */

    $('.is-invalid').removeClass('is-invalid');

    $('.invalid-feedback').not('.ifsc-error').hide();

    $('.ifsc-error').hide();

    $('#accError').addClass('d-none');


    /*
    |--------------------------------------------------------------------------
    | ACCOUNT NAME
    |--------------------------------------------------------------------------
    */

    let accountName = $('input[name="bank_account_name"]');

    let accountNameVal = accountName.val().trim();

    if(accountNameVal === ''){

        accountName.addClass('is-invalid');

        accountName.closest('.mb-4, .mb-3')
                   .find('.invalid-feedback')
                   .text('Account name is required')
                   .show();

        isValid = false;

    }else if(accountNameVal.length < 3){

        accountName.addClass('is-invalid');

        accountName.closest('.mb-4, .mb-3')
                   .find('.invalid-feedback')
                   .text('Minimum 3 characters required')
                   .show();

        isValid = false;

    }else if(accountNameVal.length > 100){

        accountName.addClass('is-invalid');

        accountName.closest('.mb-4, .mb-3')
                   .find('.invalid-feedback')
                   .text('Maximum 100 characters allowed')
                   .show();

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | ACCOUNT NUMBER
    |--------------------------------------------------------------------------
    */

    let accountNumber = $('#realAccountNumber').val();

    if(accountNumber === ''){

        $('#accountNumber').addClass('is-invalid');

        $('#accError')
            .removeClass('d-none')
            .text('Account number is required');

        isValid = false;

    }else if(!/^[0-9]{6,18}$/.test(accountNumber)){

        $('#accountNumber').addClass('is-invalid');

        $('#accError')
            .removeClass('d-none')
            .text('Account number must be 6 to 18 digits');

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | COUNTRY
    |--------------------------------------------------------------------------
    */

    if($('#bank_country').val() === ''){

        $('#bank_country').addClass('is-invalid');

        $('#bank_country')
            .closest('.mb-4, .mb-3')
            .find('.invalid-feedback')
            .text('Please select country')
            .show();

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | STATE
    |--------------------------------------------------------------------------
    */

    if($('#bank_state').val() === ''){

        $('#bank_state').addClass('is-invalid');

        $('#bank_state')
            .closest('.mb-4, .mb-3')
            .find('.invalid-feedback')
            .text('Please select state')
            .show();

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | CITY
    |--------------------------------------------------------------------------
    */

    if($('#bank_city').val() === ''){

        $('#bank_city').addClass('is-invalid');

        $('#bank_city')
            .closest('.mb-4, .mb-3')
            .find('.invalid-feedback')
            .text('Please select city')
            .show();

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | BRANCH NAME
    |--------------------------------------------------------------------------
    */

    let branchName = $('input[name="branch_name"]');

    let branchVal = branchName.val().trim();

    if(branchVal === ''){

        branchName.addClass('is-invalid');

        branchName.closest('.mb-4, .mb-3')
                  .find('.invalid-feedback')
                  .text('Branch name is required')
                  .show();

        isValid = false;

    }else if(branchVal.length < 2){

        branchName.addClass('is-invalid');

        branchName.closest('.mb-4, .mb-3')
                  .find('.invalid-feedback')
                  .text('Minimum 2 characters required')
                  .show();

        isValid = false;

    }else if(branchVal.length > 100){

        branchName.addClass('is-invalid');

        branchName.closest('.mb-4, .mb-3')
                  .find('.invalid-feedback')
                  .text('Maximum 100 characters allowed')
                  .show();

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | CANCEL CHEQUE
    |--------------------------------------------------------------------------
    */

    let hasExistingFile = "{{ $seller->cancel_cheque }}" !== '';

    let hasNewFile = $('#cancelCheque')[0].files.length > 0;

    if(!hasExistingFile && !hasNewFile){

        $('#cancelCheque').addClass('is-invalid');

        $('#cancelCheque')
            .closest('.mb-4, .mb-3')
            .find('.invalid-feedback')
            .text('Cancel cheque file is required')
            .show();

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | IFSC CODE VALIDATION
    |--------------------------------------------------------------------------
    */

    let ifscInput = $('input[name="ifsc_code"]');

    let ifscValue = ifscInput.val().trim().toUpperCase();

    let ifscRegex = /^[A-Z]{4}0[A-Z0-9]{6}$/;

    if(ifscValue === ''){

        ifscInput.addClass('is-invalid');

        $('.ifsc-error')
            .text('IFSC code is required')
            .css('display','block');

        isValid = false;

    }else if(ifscValue.length !== 11){

        ifscInput.addClass('is-invalid');

        $('.ifsc-error')
            .text('IFSC code must be exactly 11 characters')
            .css('display','block');

        isValid = false;

    }else if(!ifscRegex.test(ifscValue)){

        ifscInput.addClass('is-invalid');

        $('.ifsc-error')
            .text('Invalid IFSC code format')
            .css('display','block');

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | SWIFT CODE VALIDATION
    |--------------------------------------------------------------------------
    */

    let swiftInput = $('input[name="swift_code"]');

    let swiftValue = swiftInput.val().trim().toUpperCase();

    let swiftRegex = /^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/;

    if(swiftValue === ''){

        swiftInput.addClass('is-invalid');

        swiftInput.closest('.mb-4, .mb-3')
                  .find('.invalid-feedback')
                  .text('SWIFT code is required')
                  .show();

        isValid = false;

    }else if(swiftValue.length !== 8 && swiftValue.length !== 11){

        swiftInput.addClass('is-invalid');

        swiftInput.closest('.mb-4, .mb-3')
                  .find('.invalid-feedback')
                  .text('SWIFT code must be 8 or 11 characters')
                  .show();

        isValid = false;

    }else if(!swiftRegex.test(swiftValue)){

        swiftInput.addClass('is-invalid');

        swiftInput.closest('.mb-4, .mb-3')
                  .find('.invalid-feedback')
                  .text('Invalid SWIFT code format')
                  .show();

        isValid = false;
    }


    /*
    |--------------------------------------------------------------------------
    | BIC CODE VALIDATION
    |--------------------------------------------------------------------------
    */

    let bicInput = $('input[name="bic_code"]');

    let bicValue = bicInput.val().trim().toUpperCase();

    let bicRegex = /^[A-Z]{4}[A-Z]{2}[A-Z0-9]{2}([A-Z0-9]{3})?$/;

    if(bicValue === ''){

        bicInput.addClass('is-invalid');

        bicInput.closest('.mb-4, .mb-3')
                .find('.invalid-feedback')
                .text('BIC code is required')
                .show();

        isValid = false;

    }else if(bicValue.length !== 8 && bicValue.length !== 11){

        bicInput.addClass('is-invalid');

        bicInput.closest('.mb-4, .mb-3')
                .find('.invalid-feedback')
                .text('BIC code must be 8 or 11 characters')
                .show();

        isValid = false;

    }else if(!bicRegex.test(bicValue)){

        bicInput.addClass('is-invalid');

        bicInput.closest('.mb-4, .mb-3')
                .find('.invalid-feedback')
                .text('Invalid BIC code format')
                .show();

        isValid = false;
    }

    return isValid;
}


/*
|--------------------------------------------------------------------------
| LOAD STATES
|--------------------------------------------------------------------------
*/

function loadBankStates(country, selectedState = null){

    if(!country){

        $('#bank_state').html('<option value="">Select State</option>');

        $('#bank_city').html('<option value="">Select City</option>');

        return;
    }

    $.ajax({

        url: "/seller/organization-states/" + country,

        type: "GET",

        success: function(data){

            let options = '<option value="">Select State</option>';

            data.forEach(function(state){

                let selected = String(selectedState) === String(state.id)
                               ? 'selected'
                               : '';

                options += `<option value="${state.id}" ${selected}>${state.name}</option>`;
            });

            $('#bank_state').html(options);

            if(selectedState){

                loadBankCities(selectedState, bankCity);
            }
        }
    });
}


/*
|--------------------------------------------------------------------------
| LOAD CITIES
|--------------------------------------------------------------------------
*/

function loadBankCities(state, selectedCity = null){

    if(!state){

        $('#bank_city').html('<option value="">Select City</option>');

        return;
    }

    $.ajax({

        url: "/seller/organization-cities/" + state,

        type: "GET",

        success: function(data){

            let options = '<option value="">Select City</option>';

            data.forEach(function(city){

                let selected = String(selectedCity) === String(city.id)
                               ? 'selected'
                               : '';

                options += `<option value="${city.id}" ${selected}>${city.name}</option>`;
            });

            $('#bank_city').html(options);
        }
    });
}


/*
|--------------------------------------------------------------------------
| COUNTRY CHANGE
|--------------------------------------------------------------------------
*/

$('#bank_country').on('change', function(){

    let countryId = $(this).val();

    if(countryId){

        loadBankStates(countryId);

    }else{

        $('#bank_state').html('<option value="">Select State</option>');

        $('#bank_city').html('<option value="">Select City</option>');
    }

});


/*
|--------------------------------------------------------------------------
| STATE CHANGE
|--------------------------------------------------------------------------
*/

$('#bank_state').on('change', function(){

    let stateId = $(this).val();

    if(stateId){

        loadBankCities(stateId);
    }

});


/*
|--------------------------------------------------------------------------
| AUTO REMOVE ERROR
|--------------------------------------------------------------------------
*/

$(document).on('keyup change input', 'input, select', function(){

    $(this).removeClass('is-invalid');

    $(this).closest('.mb-4, .mb-3')
           .find('.invalid-feedback')
           .hide();

    $('#accError').addClass('d-none');

    $('.ifsc-error').hide();

});


/*
|--------------------------------------------------------------------------
| AUTO UPPERCASE
|--------------------------------------------------------------------------
*/

$('input[name="ifsc_code"], input[name="swift_code"], input[name="bic_code"]').on('input', function(){

    this.value = this.value.toUpperCase();

});

</script>

<style>
    /* =========================================
   MARKETPLACE LAYOUT
========================================= */

.marketplace-wrapper{
    display:flex;
    gap:24px;
    align-items:flex-start;
}

/* LEFT CONTENT */

.marketplace-left{
    flex:1;
    min-width:0;
}

/* RIGHT SIDEBAR */

.marketplace-right{
    width:320px;
    min-width:320px;
    position:sticky;
    top:20px;
    align-self:flex-start;
}

/* PROFILE RIGHT */

.profile-right{
    position:sticky;
    top:20px;
}

/* =========================================
   MOBILE
========================================= */

@media(max-width:991px){

    .marketplace-wrapper{
        flex-direction:column;
    }

    .marketplace-right{
        width:100%;
        min-width:100%;
        position:relative;
        top:auto;
    }

    .profile-right{
        position:relative;
        top:auto;
    }
}
</style>
@endsection