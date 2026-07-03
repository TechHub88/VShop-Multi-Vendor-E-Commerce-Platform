@extends('vshop.main')

@section('title', 'Add / Edit Address')

<style>
    .address-type-options .address-type-label {
        position: relative;
        width: 112px;
        height: 80px;
    }
    .address-type-options {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .address-type-options .address-type-label input {
      position: absolute;
      opacity: 0;
      cursor: pointer;
    }
    .address-type-options .address-type-label .address-type-box {
        width: 100%;
        height: 100%;
        display: inline-block;
        position: absolute;
        left: 0;
        top: 0;
        border: 1px solid #8692A6;
        padding: 8px;
        border-radius: 8px;
    }
    .address-type-options .address-type-label .address-type-box i {
        font-size: 15px;
        margin-bottom: 8px;
    }
    .address-type-options .address-type-label .address-type-box span {
        display: inline-block;
        font-size: 14px;
    }
    .typecheckmark {
        position: absolute;
        top: 5px;
        right: 6px;
        height: 20px;
        width: 20px;
        box-shadow: 0px 1px 2px #00000029;
        border: 1px solid #BABABA;
        border-radius: 50%;
    }
    .typecheckmark:after {
      content: "";
      position: absolute;
      display: none;
    }
    
    .address-type-options .address-type-label input:checked ~ .typecheckmark:after {
      display: block;
    }
    
    .address-type-options .address-type-label .typecheckmark:after {
     	top: 1px;
        left: 1px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #0356CB;
    }
    .address-btn-sec {
        display: flex;
        align-items: center;
        padding-top: 20px;
        gap: 15px !important;
        justify-content: center !important;
    }
    .address-edit-wrapper {
        width: 100% !important;
        padding: 35px !important;
    }
    .address-type-options .address-type-label {
    position: relative;
        width: 188px;
    }
        .address-type-options .address-type-label .address-type-box span {
        width: 100%;
    }
    .address-type-options .address-type-label .address-type-box {
        display: flex !important;
        flex-direction: column;
        justify-content: center;
    }
    .address-type-label input:checked ~ .address-type-box {
      background-color: #61b3e51f;
    }
    .address-type-options .address-type-label .typecheckmark:after {
        top: 3px;
        left: 3px;
        width: 12px;
        height: 12px;
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
                            <li><a href="{{ route('vtravelhome') }}">Home</a> / </li>
                            <li><a href="{{ route('vshop.cart.index') }}">Wishlist - Cart</a> /</li>
                            <li><a href="{{ route('vshop.address.index') }}">Addresses</a> /</li>
                            <li>Add New Address</li>
                        </ul>
                    </div>
                </div>

                {{-- ================= SUCCESS/ERROR MESSAGES ================= --}}
                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif

                {{-- ================= ADD/EDIT ADDRESS FORM ================= --}}
                <div class="address-edit-sec">
                    <div class="address-edit-wrapper">
                        <div class="address-edit-header">
                            <h2>{{ isset($address) ? 'Edit' : 'Add New' }} Address</h2>
                        </div>
                        <div class="address-edit-bottom">
                            <form action="{{ isset($address) ? route('vshop.address.update', $address->id) : route('vshop.address.store') }}" 
                                  method="POST" 
                                  id="addressForm">
                                @csrf
                                @if(isset($address))
                                    @method('PUT')
                                @endif
                                
                                <div class="address-edit-bottom-inner">
                                    <div class="row">
                                        {{-- Name Field --}}
                                        <div class="col-lg-4">
                                            <div class="address-input-box">
                                                <label for="name">Full Name <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       name="name" 
                                                       id="name" 
                                                       placeholder="Enter your full name"
                                                       value="{{ old('name', $address->name ?? '') }}"
                                                       class="form-control @error('name') is-invalid @enderror"
                                                       required>
                                                @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Email Address Field --}}
                                        <div class="col-lg-4">
                                            <div class="address-input-box">
                                                <label for="email">Email Address <span class="text-danger">*</span></label>
                                                <input type="email" 
                                                       name="email" 
                                                       id="email" 
                                                       placeholder="your@email.com"
                                                       value="{{ old('email', $address->email ?? Auth::guard('customer')->user()->email ?? '') }}"
                                                       class="form-control @error('email') is-invalid @enderror"
                                                       required>
                                                @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Phone Number --}}
                                        <div class="col-lg-4">
                                            <div class="address-input-box">
                                                <label for="phone">Phone Number <span class="text-danger">*</span></label>
                                                <div class="country-phone-box">
                                                    <img src="{{ asset('images/flag-img.png') }}" alt="" class="flag-icon">
                                                    <select name="country_code" id="country_code" class="form-select" style="width: auto; min-width: 80px;">
                                                        <option value="+91" {{ old('country_code', $address->country_code ?? '+91') == '+91' ? 'selected' : '' }}>+91</option>
                                                        <option value="+1" {{ old('country_code', $address->country_code ?? '') == '+1' ? 'selected' : '' }}>+1</option>
                                                        <option value="+44" {{ old('country_code', $address->country_code ?? '') == '+44' ? 'selected' : '' }}>+44</option>
                                                        <option value="+61" {{ old('country_code', $address->country_code ?? '') == '+61' ? 'selected' : '' }}>+61</option>
                                                        <option value="+971" {{ old('country_code', $address->country_code ?? '') == '+971' ? 'selected' : '' }}>+971</option>
                                                    </select>
                                                    <input type="text" 
                                                           name="phone" 
                                                           id="phone" 
                                                           placeholder="9876543210"
                                                           value="{{ old('phone', $address->phone ?? '') }}"
                                                           class="form-control @error('phone') is-invalid @enderror"
                                                           required
                                                           maxlength="10">
                                                </div>
                                                @error('phone')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                                @error('country_code')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Company Name (Optional) --}}
                                        <div class="col-lg-6">
                                            <div class="address-input-box">
                                                <label for="company_name">Company Name (Optional)</label>
                                                <input type="text" 
                                                       name="company_name" 
                                                       id="company_name" 
                                                       placeholder="Company name"
                                                       value="{{ old('company_name', $address->company_name ?? '') }}"
                                                       class="form-control @error('company_name') is-invalid @enderror">
                                                @error('company_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- GSTIN Number (Optional) --}}
                                        <div class="col-lg-6">
                                            <div class="address-input-box">
                                                <label for="gstin">GSTIN Number (Optional)</label>
                                                <input type="text" 
                                                       name="gstin" 
                                                       id="gstin" 
                                                       placeholder="22AAAAA0000A1Z5"
                                                       value="{{ old('gstin', $address->gstin ?? '') }}"
                                                       class="form-control @error('gstin') is-invalid @enderror">
                                                @error('gstin')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Address Type Selection (IMPORTANT) --}}
                                        <div class="col-lg-12">
                                            <div class="address-input-box">
                                                <label for="address_type">Address Type <span class="text-danger">*</span></label>
                                                <div class="address-type-options">
                                                    <label class="address-type-label">
                                                        <input type="radio" 
                                                               name="address_type" 
                                                               value="shipping" 
                                                               {{ old('address_type', $address->address_type ?? 'shipping') == 'shipping' ? 'checked' : '' }}
                                                               class="address-type-radio">
                                                        <span class="address-type-box">
                                                            <i class="fa-solid fa-truck"></i>
                                                            <span>Shipping Address</span>
                                                        </span>
                                                        <span class="typecheckmark"></span>
                                                    </label>
                                                    <label class="address-type-label">
                                                        <input type="radio" 
                                                               name="address_type" 
                                                               value="billing" 
                                                               {{ old('address_type', $address->address_type ?? '') == 'billing' ? 'checked' : '' }}
                                                               class="address-type-radio">
                                                        <span class="address-type-box">
                                                            <i class="fa-solid fa-receipt"></i>
                                                            <span>Billing Address</span>
                                                        </span>
                                                        <span class="typecheckmark"></span>
                                                    </label>
                                                    <label class="address-type-label">
                                                        <input type="radio" 
                                                               name="address_type" 
                                                               value="both" 
                                                               {{ old('address_type', $address->address_type ?? '') == 'both' ? 'checked' : '' }}
                                                               class="address-type-radio">
                                                        <span class="address-type-box">
                                                            <i class="fa-solid fa-check"></i>
                                                            <span>Both (Shipping & Billing)</span>
                                                        </span>
                                                        <span class="typecheckmark"></span>
                                                    </label>
                                                </div>
                                                @error('address_type')
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Address Line 1 --}}
                                        <div class="col-lg-6">
                                            <div class="address-input-box">
                                                <label for="address_line1">Address Line 1 <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       name="address_line1" 
                                                       id="address_line1" 
                                                       placeholder="House/Flat No., Building Name, Street"
                                                       value="{{ old('address_line1', $address->address_line1 ?? '') }}"
                                                       class="form-control @error('address_line1') is-invalid @enderror"
                                                       required>
                                                @error('address_line1')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Address Line 2 (Optional) --}}
                                        <div class="col-lg-6">
                                            <div class="address-input-box">
                                                <label for="address_line2">Address Line 2 (Optional)</label>
                                                <input type="text" 
                                                       name="address_line2" 
                                                       id="address_line2" 
                                                       placeholder="Area, Landmark, etc."
                                                       value="{{ old('address_line2', $address->address_line2 ?? '') }}"
                                                       class="form-control @error('address_line2') is-invalid @enderror">
                                                @error('address_line2')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- City / District / Town --}}
                                        <div class="col-lg-3">
                                            <div class="address-input-box">
                                                <label for="city">City <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       name="city" 
                                                       id="city" 
                                                       placeholder="Enter city"
                                                       value="{{ old('city', $address->city ?? '') }}"
                                                       class="form-control @error('city') is-invalid @enderror"
                                                       required>
                                                @error('city')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- State / Province --}}
                                        <div class="col-lg-3">
                                            <div class="address-input-box">
                                                <label for="state">State <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       name="state" 
                                                       id="state" 
                                                       placeholder="Enter state"
                                                       value="{{ old('state', $address->state ?? '') }}"
                                                       class="form-control @error('state') is-invalid @enderror"
                                                       required>
                                                @error('state')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Pincode / ZIP Code --}}
                                        <div class="col-lg-3">
                                            <div class="address-input-box">
                                                <label for="pincode">Pincode <span class="text-danger">*</span></label>
                                                <input type="text" 
                                                       name="pincode" 
                                                       id="pincode" 
                                                       placeholder="6 digit pincode"
                                                       value="{{ old('pincode', $address->pincode ?? '') }}"
                                                       class="form-control @error('pincode') is-invalid @enderror"
                                                       required
                                                       maxlength="6">
                                                @error('pincode')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Country --}}
                                        <div class="col-lg-3">
                                            <div class="address-input-box">
                                                <label for="country">Country <span class="text-danger">*</span></label>
                                                <select name="country" 
                                                        id="country" 
                                                        class="form-select @error('country') is-invalid @enderror"
                                                        required>
                                                    <option value="">Select Country</option>
                                                    <option value="India" {{ old('country', $address->country ?? 'India') == 'India' ? 'selected' : '' }}>India</option>
                                                    <option value="USA" {{ old('country', $address->country ?? '') == 'USA' ? 'selected' : '' }}>USA</option>
                                                    <option value="UK" {{ old('country', $address->country ?? '') == 'UK' ? 'selected' : '' }}>UK</option>
                                                    <option value="Canada" {{ old('country', $address->country ?? '') == 'Canada' ? 'selected' : '' }}>Canada</option>
                                                    <option value="Australia" {{ old('country', $address->country ?? '') == 'Australia' ? 'selected' : '' }}>Australia</option>
                                                </select>
                                                @error('country')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                            </div>
                                        </div>

                                        {{-- Set as Default Address --}}
                                        <div class="col-lg-6">
                                            <div class="address-input-box">
                                                <label class="checkbox-container">
                                                    <input type="checkbox" name="is_default" value="1" {{ old('is_default', $address->is_default ?? false) ? 'checked' : '' }}>
                                                    <span class="checkmark"></span>
                                                    Set as default address
                                                </label>
                                            </div>
                                        </div>

                                        {{-- Form Buttons --}}
                                        <div class="col-lg-12">
                                            <div class="address-btn-sec">
                                                <button type="submit" class="submit-btn">
                                                    {{ isset($address) ? 'Update Address' : 'Save Address' }}
                                                </button>
                                                <button type="button" class="reset-btn" onclick="window.location.href='{{ route('vshop.address.index') }}'">Cancel</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection



@section('scripts')
<script>
$(document).ready(function() {
    
    // Form validation before submit
    $('#addressForm').on('submit', function(e) {
        var isValid = true;
        
        // Check required fields
        $(this).find('[required]').each(function() {
            if (!$(this).val().trim()) {
                $(this).addClass('is-invalid');
                isValid = false;
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Check address type is selected
        if (!$('input[name="address_type"]:checked').val()) {
            $('.address-type-options').addClass('has-error');
            isValid = false;
        } else {
            $('.address-type-options').removeClass('has-error');
        }
        
        // Validate phone number (10 digits)
        var phone = $('#phone').val();
        if (phone && !/^\d{10}$/.test(phone)) {
            $('#phone').addClass('is-invalid');
            if ($('#phone').next('.invalid-feedback').length === 0) {
                $('#phone').after('<div class="invalid-feedback d-block">Please enter a valid 10-digit phone number</div>');
            }
            isValid = false;
        }
        
        // Validate pincode (6 digits)
        var pincode = $('#pincode').val();
        if (pincode && !/^\d{6}$/.test(pincode)) {
            $('#pincode').addClass('is-invalid');
            isValid = false;
        }
        
        // Validate email
        var email = $('#email').val();
        if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            $('#email').addClass('is-invalid');
            isValid = false;
        }
        
        if (!isValid) {
            e.preventDefault();
            toastr.error('Please fill all required fields correctly');
        }
    });
    
    // Remove validation class on input
    $('input, select, textarea').on('input change', function() {
        $(this).removeClass('is-invalid');
        $('.address-type-options').removeClass('has-error');
    });
    
    // Phone number validation - only numbers
    $('#phone').on('keypress', function(e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });
    
    // Pincode validation - only numbers
    $('#pincode').on('keypress', function(e) {
        var charCode = (e.which) ? e.which : e.keyCode;
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            return false;
        }
        return true;
    });
    
    // Auto-capitalize first letter
    $('#city, #state, #address_line1, #address_line2').on('blur', function() {
        var val = $(this).val();
        if (val && val.length > 0) {
            $(this).val(val.replace(/\b\w/g, l => l.toUpperCase()));
        }
    });
    
});
</script>

<style>
/* Address Type Options */
.address-type-options {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    margin-top: 5px;
}

.address-type-label {
    flex: 1;
    min-width: 150px;
    cursor: pointer;
}

.address-type-label input[type="radio"] {
    display: none;
}

.address-type-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px 15px;
    background: #f8f9fa;
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    transition: all 0.3s;
    text-align: center;
}

.address-type-box i {
    font-size: 24px;
    margin-bottom: 10px;
    color: #666;
}

.address-type-box span {
    font-size: 14px;
    font-weight: 500;
    color: #333;
}

.address-type-label input[type="radio"]:checked + .address-type-box {
    background: #e3f2fd;
    border-color: #0066b3;
}

.address-type-label input[type="radio"]:checked + .address-type-box i,
.address-type-label input[type="radio"]:checked + .address-type-box span {
    color: #0066b3;
}

.has-error .address-type-box {
    border-color: #dc3545;
}

/* Country Phone Box */
.country-phone-box {
    display: flex;
    gap: 10px;
    align-items: center;
}

.country-phone-box .flag-icon {
    width: 30px;
    height: 20px;
    object-fit: cover;
    border-radius: 3px;
}

.country-phone-box select {
    width: auto;
    min-width: 80px;
}

.country-phone-box input {
    flex: 1;
}

/* Checkbox Container */
.checkbox-container {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    user-select: none;
    position: relative;
    padding-left: 30px;
}

.checkbox-container input {
    position: absolute;
    opacity: 0;
    cursor: pointer;
    height: 0;
    width: 0;
}

.checkmark {
    position: absolute;
    left: 0;
    top: 0;
    height: 20px;
    width: 20px;
    background-color: #fff;
    border: 2px solid #ddd;
    border-radius: 4px;
}

.checkbox-container:hover input ~ .checkmark {
    border-color: #0066b3;
}

.checkbox-container input:checked ~ .checkmark {
    background-color: #0066b3;
    border-color: #0066b3;
}

.checkmark:after {
    content: "";
    position: absolute;
    display: none;
}

.checkbox-container input:checked ~ .checkmark:after {
    display: block;
}

.checkbox-container .checkmark:after {
    left: 6px;
    top: 2px;
    width: 5px;
    height: 10px;
    border: solid white;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}

/* Form Buttons */
.address-btn-sec {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    padding-top: 20px;
    border-top: 1px solid #eee;
}

.submit-btn {
    background: #0066b3;
    color: white;
    border: none;
    padding: 12px 30px;
    border-radius: 5px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}

.submit-btn:hover {
    background: #005299;
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,102,179,0.2);
}

.reset-btn {
    background: #f8f9fa;
    color: #333;
    border: 1px solid #ddd;
    padding: 12px 30px;
    border-radius: 5px;
    font-weight: 600;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.3s;
}

.reset-btn:hover {
    background: #e9ecef;
    border-color: #999;
}

/* Responsive */
@media (max-width: 768px) {
    .address-edit-wrapper {
        padding: 20px;
    }
    
    .address-edit-header h2 {
        font-size: 18px;
    }
    
    .address-type-options {
        flex-direction: column;
    }
    
    .address-type-label {
        min-width: 100%;
    }
    
    .address-btn-sec {
        flex-direction: column;
    }
    
    .submit-btn, .reset-btn {
        width: 100%;
    }
    
    .country-phone-box {
        flex-wrap: wrap;
    }
    
    .country-phone-box select {
        width: 100%;
    }
}

/* Alert Styles */
.alert {
    border-radius: 5px;
    padding: 15px 20px;
    margin-bottom: 20px;
    border: none;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 12px;
    margin-top: 5px;
}
</style>
@endsection