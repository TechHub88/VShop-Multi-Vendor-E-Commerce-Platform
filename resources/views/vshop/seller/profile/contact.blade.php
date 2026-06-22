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
.marketplace-wrapper {
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }
    .marketplace-left {
        flex: 1;
    }
    .marketplace-right {
        width: 300px;
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
$contacts = $seller->contact_persons ?? [];

$editIndex = request()->edit;
$editContact = isset($contacts[$editIndex]) ? $contacts[$editIndex] : null;

$address = $editContact['address'] ?? [];
@endphp

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
   <button onclick="window.location.href='{{ route('seller.organization') }}'" 
        class="nav-btn">
    <i class="fa-solid fa-arrow-left"></i>
</button>
</div>
<div class="marketplace-wrapper">
<div class="marketplace-left">

<form method="POST" action="{{ route('seller.contact') }}" enctype="multipart/form-data">
@csrf

{{-- Include edit index for update --}}
@if($editContact)
    <input type="hidden" name="edit_index" value="{{ $editIndex }}">
@endif



<div class="card shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between mb-4">
<h5 class="text-primary">Contact & KYC Verification</h5>
<a href="{{ route('seller.contact.list') }}" class="btn btn-outline-primary btn-sm">Go to List</a>
</div>

{{-- ORGANIZATION SECTION --}}
<div class="border p-3 mb-4 bg-light">
    <h6 class="text-primary mb-3">Organization Details <span class="text-danger">*</span></h6>
    <div class="row">
        <div class="col-md-4 mb-2">
            <label>Organization Name <span class="text-danger">*</span></label>
            <input type="text" name="org_name" class="form-control @error('org_name') is-invalid @enderror" value="{{ old('org_name', $seller->org_name ?? '') }}">
            @error('org_name')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-4 mb-2">
            <label>Mobile <span class="text-danger">*</span></label>
            <input type="text" name="org_mobile" class="form-control mobileInput @error('org_mobile') is-invalid @enderror" value="{{ old('org_mobile', $seller->org_mobile ?? '') }}">
            @error('org_mobile')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-4 mb-2">
            <label>Email <span class="text-danger">*</span></label>
            <input type="email" name="org_email" class="form-control @error('org_email') is-invalid @enderror" value="{{ old('org_email', $seller->org_email ?? '') }}">
            @error('org_email')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

{{-- ADDRESS SECTION --}}
<div class="border p-3 mb-4 bg-light">
    <h6 class="text-primary mb-3">Address Details <span class="text-danger">*</span></h6>

    <div class="row mb-3">
        <div class="col-md-4 mb-2">
            <label>Country <span class="text-danger">*</span></label>
            <select name="contact_country[0]" id="country" class="form-control @error('contact_country.0') is-invalid @enderror">
                <option value="">Select Country</option>
                @foreach($countries as $c)
                    <option value="{{ $c->id }}" {{ (string)$c->id === (string)($address['country'] ?? '') ? 'selected' : '' }}>
                        {{ $c->name }}
                    </option>
                @endforeach
            </select>
            @error('contact_country.0')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-4 mb-2">
            <label>State <span class="text-danger">*</span></label>
            <select name="contact_state[0]" id="state" class="form-control @error('contact_state.0') is-invalid @enderror">
                <option value="">Select State</option>
            </select>
            @error('contact_state.0')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="col-md-4 mb-2">
            <label>City <span class="text-danger">*</span></label>
            <select name="contact_city[0]" id="city" class="form-control @error('contact_city.0') is-invalid @enderror">
                <option value="">Select City</option>
            </select>
            @error('contact_city.0')
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="mb-3">
        <label>Street Address <span class="text-danger">*</span></label>
        <textarea name="contact_address[0]" class="form-control @error('contact_address.0') is-invalid @enderror" rows="2">{{ $address['street'] ?? '' }}</textarea>
        @error('contact_address.0')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

{{-- CONTACTS SECTION --}}
<div id="contactWrapper">
    @php
        $oldNames = old('contact_name');
        
        if($errors->any() && $oldNames && is_array($oldNames)){
            $loopContacts = [];
            foreach($oldNames as $idx => $v){
                $loopContacts[$idx] = [
                    'name' => old("contact_name.$idx"),
                    'mobile' => old("contact_mobile.$idx"),
                    'email' => old("contact_email.$idx"),
                    'pan' => old("contact_pan_number.$idx"),
                    'aadhaar' => old("contact_aadhar_number.$idx"),
                    'photo' => old("contact_photo.$idx"),
                ];
            }
        } else {
            $loopContacts = $contacts ?: [null];
        }
    @endphp

    @foreach($loopContacts as $i => $c)
        <div class="contact-block border p-3 mb-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-secondary mb-0">Contact Person {{ $loop->iteration }}</h6>
                @if($loop->iteration > 1)
                    <button type="button" class="btn btn-danger btn-sm removeContact">Delete</button>
                @endif
            </div>

            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <label>Name <span class="text-danger">*</span></label>
                    <input name="contact_name[{{ $i }}]" class="form-control @error("contact_name.$i") is-invalid @enderror" value="{{ $c['name'] ?? '' }}">
                    @error("contact_name.$i")
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

              <div class="col-md-3 mb-2">

<label>
Photo
<span class="text-danger">*</span>
</label>

<div class="img-wrapper">

    <input type="file"
           name="contact_photo[{{ $i }}]"
           class="form-control photoInput
           @if($errors->has('contact_photo') || $errors->has('contact_photo.'.$i)) is-invalid @endif"
           accept="image/*">

    @php
        $photo = $c['photo'] ?? null;
    @endphp

    <img src="{{ (!empty($photo) && file_exists(public_path($photo)))
        ? asset($photo)
        : asset('default.png') }}"
         class="previewImg mt-1">

    <span class="remove-image {{ empty($photo) ? 'd-none' : '' }}">
        ×
    </span>

    <input type="hidden"
           name="remove_photo[{{ $i }}]"
           class="removePhotoInput">

</div>

@if($errors->has('contact_photo'))
<div class="text-danger small d-block mt-1">
    {{ $errors->first('contact_photo') }}
</div>
@endif

@if($errors->has('contact_photo.'.$i))
<div class="text-danger small d-block mt-1">
    {{ $errors->first('contact_photo.'.$i) }}
</div>
@endif

</div>

                <div class="col-md-3 mb-2">
                    <label>Mobile <span class="text-danger">*</span></label>
                    <input name="contact_mobile[{{ $i }}]" class="form-control mobileInput @error("contact_mobile.$i") is-invalid @enderror" value="{{ $c['mobile'] ?? '' }}">
                    @error("contact_mobile.$i")
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                <div class="col-md-3 mb-2">
                    <label>Email <span class="text-danger">*</span></label>
                    <input name="contact_email[{{ $i }}]" class="form-control @error("contact_email.$i") is-invalid @enderror" value="{{ $c['email'] ?? '' }}">
                    @error("contact_email.$i")
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <hr>

            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label>PAN Number <span class="text-danger">*</span></label>
                    <input name="contact_pan_number[{{ $i }}]" class="form-control panInput @error("contact_pan_number.$i") is-invalid @enderror" value="{{ $c['pan'] ?? '' }}" maxlength="10" placeholder="ABCDE1234F">
                    @error("contact_pan_number.$i")
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

             <div class="col-md-6 mb-2">

<label>
PAN File (PDF)
<span class="text-danger">*</span>
</label>

<input type="file"
       name="contact_pan_file[{{ $i }}]"
       class="form-control pdfOnly
       @if($errors->has('contact_pan_file') || $errors->has('contact_pan_file.'.$i)) is-invalid @endif"
       accept="application/pdf">

<small class="text-muted d-block">
Only PDF allowed, Max 2MB
</small>

@if($errors->has('contact_pan_file'))
<div class="text-danger small d-block mt-1">
    {{ $errors->first('contact_pan_file') }}
</div>
@endif

@if($errors->has('contact_pan_file.'.$i))
<div class="text-danger small d-block mt-1">
    {{ $errors->first('contact_pan_file.'.$i) }}
</div>
@endif

</div>
            </div>

            <hr>

            @php
                $aadhaar = $c['aadhaar'] ?? '';
                $masked = ($aadhaar && strlen($aadhaar) == 12) ? 'XXXXXXXX' . substr($aadhaar, -4) : '';
            @endphp

            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label>Aadhaar Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control aadharInput @error("contact_aadhar_number.$i") is-invalid @enderror" value="{{ $masked }}" maxlength="12" placeholder="Enter 12 digit Aadhaar">
                    <input type="hidden" name="contact_aadhar_number[{{ $i }}]" class="aadharReal" value="{{ $aadhaar }}">
                    @error("contact_aadhar_number.$i")
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

               <div class="col-md-6 mb-2">

<label>
Aadhaar File (PDF)
<span class="text-danger">*</span>
</label>

<input type="file"
       name="contact_aadhar_file[{{ $i }}]"
       class="form-control pdfOnly
       @if($errors->has('contact_aadhar_file') || $errors->has('contact_aadhar_file.'.$i)) is-invalid @endif"
       accept="application/pdf">

<small class="text-muted d-block">
Only PDF allowed, Max 2MB
</small>

@if($errors->has('contact_aadhar_file'))
<div class="text-danger small d-block mt-1">
    {{ $errors->first('contact_aadhar_file') }}
</div>
@endif

@if($errors->has('contact_aadhar_file.'.$i))
<div class="text-danger small d-block mt-1">
    {{ $errors->first('contact_aadhar_file.'.$i) }}
</div>
@endif

</div>
            </div>
        </div>
    @endforeach
</div>

<button type="button" id="addContactBtn" class="btn btn-outline-primary mb-3">+ Add Another Contact</button>

<div class="text-end">
    <button type="submit" class="btn btn-success px-4">{{ $editContact ? 'Update & Continue' : 'Save & Continue' }}</button>
</div>

</div>
</div>

</form>
</div>

<div class="marketplace-right">
@include('vshop.seller.partials.profile-progress')
</div>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
let selectedCountry = "{{ $address['country'] ?? '' }}";
let selectedState   = "{{ $address['state'] ?? '' }}";
let selectedCity    = "{{ $address['city'] ?? '' }}";
let editMode        = {{ $editContact ? 'true' : 'false' }};
</script>

<script>
// ======================
// PHOTO PREVIEW & REMOVE
// ======================
$(document).on('change', '.photoInput', function() {
    let file = this.files[0];
    let wrap = $(this).closest('.img-wrapper');
    
    if (!file) return;
    
    let reader = new FileReader();
    reader.onload = function(e) {
        wrap.find('.previewImg').attr('src', e.target.result);
        wrap.find('.remove-image').removeClass('d-none');
    };
    reader.readAsDataURL(file);
});

$(document).on('click', '.remove-image', function() {
    let wrap = $(this).closest('.img-wrapper');
    wrap.find('.previewImg').attr('src', '/default.png');
    wrap.find('input[type="file"]').val('');
    wrap.find('.removePhotoInput').val(1);
    $(this).addClass('d-none');
});

// ======================
// MOBILE VALIDATION
// ======================
$(document).on('input', '.mobileInput', function() {
    this.value = this.value.replace(/\D/g, '').slice(0, 10);
});

// ======================
// PAN VALIDATION
// ======================
$(document).on('input', '.panInput', function() {
    this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 10);
});

// ======================
// AADHAAR MASKING
// ======================
$(document).on('focus', '.aadharInput', function() {
    let realValue = $(this).siblings('.aadharReal').val();
    if (realValue && realValue.length === 12) {
        $(this).val(realValue);
    } else {
        $(this).val('');
    }
});

$(document).on('blur', '.aadharInput', function() {
    let val = $(this).val().replace(/\D/g, '');
    if (val.length === 12) {
        $(this).siblings('.aadharReal').val(val);
        $(this).val('XXXXXXXX' + val.slice(-4));
    } else {
        $(this).siblings('.aadharReal').val('');
        if (val.length > 0 && val.length !== 12) {
            $(this).closest('.mb-2').find('.text-danger.small').remove();
            $(this).after('<span class="text-danger small">Aadhaar must be 12 digits</span>');
        }
    }
});

// ======================
// ADD NEW CONTACT
// ======================
$('#addContactBtn').click(function() {
    let i = $('.contact-block').length;
    let newIndex = i;
    
    $('#contactWrapper').append(`
        <div class="contact-block border p-3 mb-3 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="text-secondary mb-0">Contact Person ${i+1}</h6>
                <button type="button" class="btn btn-danger btn-sm removeContact">Delete</button>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-3 mb-2">
                    <label>Name <span class="text-danger">*</span></label>
                    <input name="contact_name[${newIndex}]" class="form-control" value="">
                </div>
                
                <div class="col-md-3 mb-2">
                    <label>Photo <span class="text-danger">*</span></label>
                    <div class="img-wrapper">
                        <input type="file" name="contact_photo[${newIndex}]" class="form-control photoInput" accept="image/*">
                        <img src="/default.png" class="previewImg mt-1">
                        <span class="remove-image d-none">×</span>
                        <input type="hidden" name="remove_photo[${newIndex}]" class="removePhotoInput">
                    </div>
                </div>
                
                <div class="col-md-3 mb-2">
                    <label>Mobile <span class="text-danger">*</span></label>
                    <input name="contact_mobile[${newIndex}]" class="form-control mobileInput" value="">
                </div>
                
                <div class="col-md-3 mb-2">
                    <label>Email <span class="text-danger">*</span></label>
                    <input name="contact_email[${newIndex}]" class="form-control" value="">
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label>PAN Number <span class="text-danger">*</span></label>
                    <input name="contact_pan_number[${newIndex}]" class="form-control panInput" maxlength="10" placeholder="ABCDE1234F">
                </div>
                
                <div class="col-md-6 mb-2">
                    <label>PAN File (PDF) <span class="text-danger">*</span></label>
                    <input type="file" name="contact_pan_file[${newIndex}]" class="form-control pdfOnly" accept="application/pdf">
                    <small class="text-muted">Only PDF allowed, Max 2MB</small>
                </div>
            </div>
            
            <hr>
            
            <div class="row mb-3">
                <div class="col-md-6 mb-2">
                    <label>Aadhaar Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control aadharInput" maxlength="12" placeholder="Enter 12 digit Aadhaar">
                    <input type="hidden" name="contact_aadhar_number[${newIndex}]" class="aadharReal" value="">
                </div>
                
                <div class="col-md-6 mb-2">
                    <label>Aadhaar File (PDF) <span class="text-danger">*</span></label>
                    <input type="file" name="contact_aadhar_file[${newIndex}]" class="form-control pdfOnly" accept="application/pdf">
                    <small class="text-muted">Only PDF allowed, Max 2MB</small>
                </div>
            </div>
        </div>
    `);
});

// ======================
// REMOVE CONTACT
// ======================
$(document).on('click', '.removeContact', function () {

    if ($('.contact-block').length === 1) {

        alert('At least one contact is required');
        return;
    }

    $(this).closest('.contact-block').remove();

    refreshContactNumbers();

});

function refreshContactNumbers() {

    $('.contact-block').each(function(index){

        $(this).find('h6').text('Contact Person ' + (index + 1));

    });

}

// ======================
// PDF FILE VALIDATION
// ======================

$(document).on('change', '.pdfOnly', function () {

    let file = this.files[0];
    let $this = $(this);

    // remove only custom error
    $this.parent().find('.custom-file-error').remove();

    if (file) {

        let fileType = file.type;
        let fileSize = file.size / 1024 / 1024;

        if (fileType !== 'application/pdf') {

            $this.after(`
                <div class="text-danger small custom-file-error">
                    Only PDF files are allowed
                </div>
            `);

            $this.val('');

        } else if (fileSize > 2) {

            $this.after(`
                <div class="text-danger small custom-file-error">
                    File size must be less than 2MB
                </div>
            `);

            $this.val('');
        }
    }f

});

// ======================
// LOCATION DROPDOWNS
// ======================
function loadStates(country, selectedStateVal = null) {
    if (!country) return;
    
    $.get("/seller/organization-states/" + country, function(data) {
        let opt = '<option value="">Select State</option>';
        data.forEach(s => {
            let sel = (String(s.id) === String(selectedStateVal)) ? 'selected' : '';
            opt += `<option value="${s.id}" ${sel}>${s.name}</option>`;
        });
        $('#state').html(opt);
        
        if (selectedStateVal) {
            loadCities(selectedStateVal, selectedCity);
        }
    }).fail(function() {
        console.error('Error loading states');
    });
}

function loadCities(state, selectedCityVal = null) {
    if (!state) return;
    
    $.get("/seller/organization-cities/" + state, function(data) {
        let opt = '<option value="">Select City</option>';
        data.forEach(c => {
            let sel = (String(c.id) === String(selectedCityVal)) ? 'selected' : '';
            opt += `<option value="${c.id}" ${sel}>${c.name}</option>`;
        });
        $('#city').html(opt);
    }).fail(function() {
        console.error('Error loading cities');
    });
}

// Initialize location dropdowns
$(document).ready(function() {
    if (selectedCountry) {
        $('#country').val(selectedCountry);
        loadStates(selectedCountry, selectedState);
    }
    
    // Country change event
    $('#country').on('change', function() {
        let newCountry = this.value;
        $('#state').html('<option value="">Loading...</option>');
        $('#city').html('<option value="">Select City</option>');
        loadStates(newCountry);
    });
    
    // State change event
    $('#state').on('change', function() {
        let newState = this.value;
        loadCities(newState);
    });
    
  $(document).on('input change', 'input, select, textarea', function () {

    $(this).removeClass('is-invalid');

    // remove validation text from same field only
    $(this)
        .closest('.mb-2')
        .find('.text-danger.small')
        .remove();

    // remove custom js file errors
    $(this)
        .parent()
        .find('.custom-file-error')
        .remove();

});
});
</script>

<style>
.img-wrapper {
    position: relative;
    display: inline-block;
}

.previewImg {
    width: 70px;
    height: 70px;
    border-radius: 6px;
    object-fit: cover;
    display: block;
    margin-top: 8px;
    border: 1px solid #ddd;
}

.remove-image {
    position: absolute;
    top: 28px;
    left: 50px;
    background: red;
    color: #fff;
    border-radius: 50%;
    width: 20px;
    height: 20px;
    text-align: center;
    line-height: 18px;
    font-size: 12px;
    cursor: pointer;
    font-weight: bold;
    z-index: 10;
}

.remove-image:hover {
    background: darkred;
}

.error-msg {
    font-size: 12px;
    color: red;
}

.is-invalid {
    border-color: #dc3545 !important;
}

.contact-block {
    transition: all 0.2s ease;
    border-radius: 8px;
}

.contact-block:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

hr {
    margin: 15px 0;
}

.text-danger.small {
    font-size: 11px;
    margin-top: 4px;
}

.btn-outline-primary:hover {
    background-color: #0d6efd;
    color: white;
}

.card {
    border-radius: 12px;
}

.form-control:focus {
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,0.15);
}

.bg-light {
    background-color: #f8f9fc !important;
}
</style>

@endsection