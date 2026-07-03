@extends('frontend.layout')

@section('content')
   
<section class="bedcum-sec" style="padding-top: 130px;">
    <div class="container">
        <div class="bedcum-inner">	
            <div class="bed-cum-list">
                <ul>
                    <li><a href="#">Shop</a></li>
                    <li><i class="fa-regular fa-angle-right"></i></li>
                    <li><span>Cart</span></li>
                    <li><i class="fa-regular fa-angle-right"></i></li>
                    <li><span>Add Address Details</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="address-add-sec">
    <div class="container">
        <div class="address-add-inner">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
			
			<div class="address-wrapper">
			<div class="address-left">
			<div class="address-left-inner">
            <!-- Add/Edit Address Form -->
            <div class="address-form-wrapper">
				<div class="address-left-header address-left-top-header">
				<div class="address-header-left">
                <h2 id="formTitle">Add New Address</h2>
				</div>
				</div>
                <form action="{{ route('frontend.add-address.store') }}" method="POST" id="addressForm">
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
                    </div>
                </form>
            </div>
			</div>
			</div>
			
			<div class="address-right">

            <!-- Existing Saved Addresses -->
            @if($addresses->count())
            <div class="address-right-top existing-addresse">
                <h3>Saved Addresses</h3>
				
				<div class="userAddressListArea">
				@foreach($addresses as $address)
                        <div class="userAddressListItem">						 
						<div class="d-flex userAddressListIteminner">
                            <div class="radioStyle_1Box">
							<input type="radio" name="selected_address" value="{{ $address->id }}" id="{{ $address->id }}">
							<label class="radioStyleCircle" for="{{ $address->id }}"></label>
							</div>
                            <div class="d-flex justify-content-between userAddressDtlsArea">
								<div class="userAddressLeftSide">
								<div class="userAddressDtls">
                                <p><i class="fa-light fa-person"></i> {{ $address->name }}</p>
                                <p><i class="fa-light fa-mobile-retro"></i> +91 {{ $address->phone }}</p>
                                <p><i class="fa-light fa-location-dot"></i> {{ $address->address_line1 }}, {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}, {{ $address->country }}</p>
								</div>
								</div>
								<div class="userAddressRightSide">
                                  <div class="mb-1"><small class="text-muted">Same as delivery</small></div>
								  <div class="userAddressRightSideBtns">
								  <button class="btn btn-sm btn-primary edit-address-btn"
                                    data-id="{{ $address->id }}"
                                    data-name="{{ $address->name }}"
                                    data-email="{{ $address->email }}"
                                    data-company="{{ $address->company_name }}"
                                    data-gstin="{{ $address->gstin }}"
                                    data-phone="{{ $address->phone }}"
                                    data-country="{{ $address->country }}"
                                    data-state="{{ $address->state }}"
                                    data-city="{{ $address->city }}"
                                    data-address="{{ $address->address_line1 }}"
                                    data-pincode="{{ $address->pincode }}"
                                    data-address_type="{{ $address->address_type }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <form action="{{ route('frontend.delete-address', $address->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger delete-address-btn" onclick="return confirm('Are you sure?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
								</div>
								</div>
							</div>
                        </div>
						</div>
                        @endforeach
				
				</div>
				
				
				
				
                <!--<table class="table table-bordered mt-3">
                    <thead>
                        <tr>
                            <th class="text-center">Select</th>
                            <th>Address Details</th>
                            <th>Billing Info</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($addresses as $address)
                        <tr>
                            <td class="text-center">
                                <input type="radio" name="selected_address" value="{{ $address->id }}">
                            </td>

                            <td>
                                <strong>{{ $address->name }}</strong><br>
                                +91 {{ $address->phone }}<br>
                                {{ $address->address_line1 }}, {{ $address->city }}, {{ $address->state }} - {{ $address->pincode }}, {{ $address->country }}
                            </td>

                            <td><small class="text-muted">Same as delivery</small></td>

                            <td>
                                <button class="btn btn-sm btn-primary edit-address-btn"
                                    data-id="{{ $address->id }}"
                                    data-name="{{ $address->name }}"
                                    data-email="{{ $address->email }}"
                                    data-company="{{ $address->company_name }}"
                                    data-gstin="{{ $address->gstin }}"
                                    data-phone="{{ $address->phone }}"
                                    data-country="{{ $address->country }}"
                                    data-state="{{ $address->state }}"
                                    data-city="{{ $address->city }}"
                                    data-address="{{ $address->address_line1 }}"
                                    data-pincode="{{ $address->pincode }}"
                                    data-address_type="{{ $address->address_type }}">
                                    <i class="fa fa-edit"></i>
                                </button>

                                <form action="{{ route('frontend.delete-address', $address->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>

                            </td>
                        </tr>
                        @endforeach
                    </tbody>

                </table>-->
            </div>
            @endif
			
			</div>
			</div>

        </div>
    </div>
</section>

<!-- jQuery -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

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

@endsection
