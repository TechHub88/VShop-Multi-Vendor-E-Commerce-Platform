@extends('frontend.layout')

@section('content')
   
<section class="bedcum-sec" style="padding-top: 120px;">
    <div class="container">
        <div class="bedcum-inner">  
            <div class="bed-cum-list">
                <ul>
                    <li><a href="#">Home</a></li>
                    <li><i class="fa-regular fa-angle-right"></i></li>
                    <li><span>My Profile</span></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="address-add-sec">
    <div class="container">
        <div class="address-add-inner">
           

            <form action="{{ route('frontend.profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                @csrf
                <input type="hidden" name="user_id" value="{{ auth('customer')->id() }}">
                
                <div class="address-add-form">
                    <h2>My Profile</h2>
                    <div class="address-add-from-inner">
                       <div class="row">
                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="name">Full Name</label>
                                    <input type="text" name="name" value="{{ old('name', auth('customer')->user()->name) }}" required>
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="email">Email Address</label>
                                    <input type="email" name="email" value="{{ old('email', auth('customer')->user()->email) }}" required>
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="phone">Contact Number</label>
                                    <input type="tel" name="phone" value="{{ old('phone', auth('customer')->user()->phone) }}">
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="profile_picture">Profile Picture</label>
                                    <input type="file" name="profile_picture">
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="gstin">GSTIN Number</label>
                                    <input type="text" name="gstin" value="{{ old('gstin', auth('customer')->user()->gstin) }}">
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="country">Country</label>
                                    <select name="country" class="form-select">
                                        <option value="India" {{ auth('customer')->user()->country == 'India' ? 'selected' : '' }}>India</option>
                                    </select>
                                </div>
                            </div>

                           <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="state">State</label>
                                    <select name="state" class="form-select" required>
                                        <option value="">-- Select State --</option>
                                        <option value="West Bengal" {{ old('state', auth('customer')->user()->state) == 'West Bengal' ? 'selected' : '' }}>West Bengal</option>
                                        <option value="Maharashtra" {{ old('state', auth('customer')->user()->state) == 'Maharashtra' ? 'selected' : '' }}>Maharashtra</option>
                                        <option value="Karnataka" {{ old('state', auth('customer')->user()->state) == 'Karnataka' ? 'selected' : '' }}>Karnataka</option>
                                        <option value="Tamil Nadu" {{ old('state', auth('customer')->user()->state) == 'Tamil Nadu' ? 'selected' : '' }}>Tamil Nadu</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="city">City / District</label>
                                    <select name="city" class="form-select" required>
                                        <option value="">-- Select City / District --</option>
                                        <option value="Kolkata" {{ old('city', auth('customer')->user()->city) == 'Kolkata' ? 'selected' : '' }}>Kolkata</option>
                                        <option value="Mumbai" {{ old('city', auth('customer')->user()->city) == 'Mumbai' ? 'selected' : '' }}>Mumbai</option>
                                        <option value="Bengaluru" {{ old('city', auth('customer')->user()->city) == 'Bengaluru' ? 'selected' : '' }}>Bengaluru</option>
                                        <option value="Chennai" {{ old('city', auth('customer')->user()->city) == 'Chennai' ? 'selected' : '' }}>Chennai</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="address_line1">Street / Address</label>
                                    <input type="text" name="address_line1" value="{{ old('address_line1', auth('customer')->user()->address_line1) }}">
                                </div>
                            </div>

                            <div class="col-lg-3 mb-3">
                                <div class="address-form-box">
                                    <label for="pincode">Pincode</label>
                                    <input type="text" name="pincode" value="{{ old('pincode', auth('customer')->user()->pincode) }}">
                                </div>
                            </div>

                        </div>

                     <div class="partner-status-section mt-4 p-4 bg-white border rounded shadow-sm">
    <div class="row align-items-center">

        @php
            $customer = auth('customer')->user();
        @endphp

        <div class="col-md-8">
            <h4 class="mb-2 text-dark">
                <i class="fa-solid fa-user-tie me-2"></i>
                Partner Status
            </h4>

            <div class="partner-status-info">

                {{-- When status = approved --}}
                @if($customer->status === 'approved')
                    <div class="d-flex align-items-center">
                        <span class="badge bg-success me-3">Approved Partner</span>
                        <small class="text-muted">
                            Since: {{ $customer->updated_at->format('d M Y') }}
                        </small>
                    </div>
                    <p class="mb-0 mt-2 text-muted small">
                        <i class="fa-solid fa-check-circle text-success me-1"></i>
                        You are an approved partner with access to special benefits.
                    </p>

                {{-- When status = pending --}}
                @elseif($customer->status === 'pending')
                    <div class="d-flex align-items-center">
                        <span class="badge bg-warning me-3">Pending Approval</span>
                        <small class="text-muted">
                            Applied on: {{ $customer->updated_at->format('d M Y') }}
                        </small>
                    </div>
                    <p class="mb-0 mt-2 text-muted small">
                        <i class="fa-solid fa-clock text-warning me-1"></i>
                        Your partner application is under review.
                    </p>

                {{-- When no status found (never applied) --}}
                @else
                    <div class="d-flex align-items-center">
                        <span class="badge bg-secondary me-3">Not Applied</span>
                    </div>
                    <p class="mb-0 mt-2 text-muted small">
                        <i class="fa-solid fa-info-circle text-secondary me-1"></i>
                        Apply to become a partner and unlock exclusive benefits.
                    </p>
                @endif

            </div>
        </div>

        <div class="col-md-4 text-md-end">

            {{-- Button Logic Based on Status --}}
            @if($customer->status === 'approved')
                <button type="button" class="btn btn-success" disabled>
                    <i class="fa-solid fa-check me-2"></i> Partner Active
                </button>

            @elseif($customer->status === 'pending')
                <button type="button" class="btn btn-warning" disabled>
                    <i class="fa-solid fa-clock me-2"></i> Pending
                </button>

            @else
                <button type="button" id="applyPartnerBtn" class="btn btn-dark" style="background-color:#000; border-color:#000; color:#fff;">
                    <i class="fa-solid fa-handshake me-2"></i> Apply Partner
                </button>
            @endif

        </div>

    </div>
</div>


                        <div class="address-add-btn-sec mt-4">
                            <button class="add-tocart-btn" type="submit" name="action" value="update_profile">Update Profile</button>
                            <button class="shopping-btn reset-btn" type="reset">Reset</button>
                            <a href="{{ route('frontend.Home') }}" class="btn btn-secondary profile-back-btn reset-btn">Back</a>
                        </div>

                        <!-- HIDDEN PARTNER APPLICATION SECTION (White Background) -->
                        <div id="partnerSection" class="mt-4 partner-white-section bg-white border rounded p-4 shadow-sm" style="display:none;">
                            <div class="partner-section-header mb-4">
                                <h4 class="text-dark">
                                    <i class="fa-solid fa-handshake me-2"></i> Apply for Partnership
                                </h4>
                                <p class="text-muted mb-0">Select your brand and branch to apply as a partner</p>
                            </div>
                            
                            <div class="row g-3 align-items-end">
                                <!-- Brand Dropdown Field -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="address-form-box mb-0">
                                        <label for="brand_id" class="form-label text-dark">Select Brand <span class="text-danger">*</span></label>
                                        <select name="brand_id" id="brand_id" class="form-control">
                                            <option value="">-- Select Brand --</option>
                                            @foreach($brands as $brand)
                                                <option value="{{ $brand->id }}" 
                                                    {{ old('brand_id', auth('customer')->user()->brand_id) == $brand->id ? 'selected' : '' }}>
                                                    {{ $brand->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <!-- Branch Dropdown Field (dependent on Brand) -->
                                <div class="col-lg-4 col-md-6">
                                    <div class="address-form-box mb-0">
                                        <label for="branch_id" class="form-label text-dark">Select Branch <span class="text-danger">*</span></label>
                                        <select name="branch_id" id="branch_id" class="form-control">
                                            <option value="">-- Select Branch --</option>
                                            <!-- Will populate via AJAX -->
                                        </select>
                                    </div>
                                </div>
                                
                                <!-- Apply Partner Submit Button -->
                                <div class="col-lg-4 col-md-12">
                                    <div class="d-flex gap-2">
                                      <button type="submit" id="savePartnerBtn" class="btn btn-dark flex-grow-1" style="height: 45px; background-color: #000; border-color: #000; color: #fff;" name="action" value="apply_partner">
    <i class="fa-solid fa-check me-2"></i> Apply Partner
</button>
                                        <button type="button" id="cancelPartnerBtn" class="btn btn-outline-secondary" style="height: 45px;">
                                            Cancel
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                         
                        </div>

                    </div>
                </div>
            </form>

        </div>
    </div>
</section>

<style>
    /* Global black button styling for .add-tocart-btn */
    .add-tocart-btn {
        background-color: #000 !important;
        color: #fff !important;
        border: 1px solid #000 !important;
        white-space: nowrap;
    }

    .add-tocart-btn:hover {
        background-color: #111 !important;
        border-color: #111 !important;
        color: #fff !important;
    }

    /* Shopping button (Reset) */
    .shopping-btn {
        background-color: #6c757d !important;
        color: #fff !important;
        border: 1px solid #6c757d !important;
    }
    
    .shopping-btn:hover {
        background-color: #5a6268 !important;
        border-color: #545b62 !important;
    }

    /* Partner Status Section */
    .partner-status-section {
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
    }

    .partner-status-section h4 {
        font-weight: 600;
        color: #333;
    }

    .partner-status-info .badge {
        font-size: 14px;
        padding: 6px 12px;
        background: #AAAAAA !important;
    }

    /* Partner Application Section (White Background) */
    .partner-white-section {
        background: #fff !important;
        color: #333 !important;
        border: 1px solid #ddd !important;
    }

    .partner-white-section .partner-section-header h4 {
        color: #333;
        font-weight: 700;
    }

    .partner-white-section .text-muted {
        color: #666 !important;
    }

    .partner-white-section .address-form-box label {
        color: #333 !important;
        font-weight: 500;
    }

    .partner-white-section .form-control {
        background-color: #fff !important;
        border: 1px solid #ddd !important;
        color: #333 !important;
        border-radius: 4px;
        padding: 10px 12px;
    }

    .partner-white-section .form-control:focus {
        background-color: #fff !important;
        border-color: #007bff !important;
        color: #333 !important;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .partner-white-section .form-control option {
        background-color: #fff !important;
        color: #333 !important;
    }

    /* Partner Benefits */
    .partner-benefits ul li {
        font-size: 14px;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .partner-status-section .row {
            flex-direction: column;
            text-align: center;
        }
        
        .partner-status-section .col-md-4 {
            margin-top: 15px;
        }
        
        .partner-white-section .row.g-3 {
            flex-direction: column;
        }
        
        .partner-white-section .col-lg-4,
        .partner-white-section .col-md-6 {
            width: 100%;
            margin-bottom: 15px;
        }
    }
</style>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    $(document).ready(function() {
        // CSRF token setup
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Apply Partner Button logic
        $('#applyPartnerBtn').on('click', function(e){
            e.preventDefault();
            $('#partnerSection').slideDown();  // Show the partner section
            $(this).prop('disabled', true).text('Applying...'); // Disable the button
        });

        // Cancel Partner Button
        $('#cancelPartnerBtn').on('click', function(e){
            e.preventDefault();
            $('#partnerSection').slideUp();  // Hide the partner section
            $('#applyPartnerBtn').prop('disabled', false).html('<i class="fa-solid fa-handshake me-2"></i> Apply Partner');
        });

        // Dependent Dropdown logic for Brand and Branch
        $('#brand_id').on('change', function(){
            var brandID = $(this).val();
            $('#branch_id').html('<option value="">Loading...</option>');

            if (brandID) {
                $.ajax({
                    url: '{{ route("frontend.fetchBranches") }}',
                    type: 'GET',
                    data: { brand_id: brandID },
                    dataType: 'json',
                    success: function(data) {
                        $('#branch_id').empty().append('<option value="">-- Select Branch --</option>');
                        $.each(data, function(index, branch){
                            $('#branch_id').append('<option value="'+ branch.id +'">'+ branch.address +'</option>');
                        });
                        
                        // If user already has a branch selected, set it
                        @if(auth('customer')->user()->branch_id)
                            $('#branch_id').val('{{ auth('customer')->user()->branch_id }}');
                        @endif
                    },
                    error: function(xhr, status, error) {
                        console.error("AJAX Error:", status, error);
                        $('#branch_id').html('<option value="">-- Select Branch --</option>');
                    }
                });
            } else {
                $('#branch_id').html('<option value="">-- Select Branch --</option>');
            }
        });

       
        @if(auth('customer')->user()->brand_id)
            $('#brand_id').trigger('change');
            // Don't auto-show partner section if already applied
            $('#savePartnerBtn').hide();
            $('#cancelPartnerBtn').show();
        @endif

        // Form submission handling
        $('#profileForm').on('submit', function(e) {
            // Get which button was clicked
            var action = $('button[type="submit"]:focus').val() || $('input[name="action"]').val();
            
            if (action === 'apply_partner') {
                // Validate partner fields
                var brandID = $('#brand_id').val();
                var branchID = $('#branch_id').val();
                
                if (!brandID || !branchID) {
                    e.preventDefault();
                    alert('Please select both Brand and Branch to apply as partner.');
                    return false;
                }
            }
        });
        
        // Show success message with animation
        @if(session('success'))
            setTimeout(function() {
                $('.alert-success').fadeOut('slow');
            }, 3000);
        @endif
        
        // Auto-show partner section if user hasn't applied yet
        @if(!auth('customer')->user()->is_partner && !auth('customer')->user()->brand_id)
            // Keep hidden by default
        @endif
    });
</script>

@endsection