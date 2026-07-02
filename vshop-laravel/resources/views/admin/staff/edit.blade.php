@extends('layouts.vertical', ['title' => 'Edit Staff'])

@section('content')

<style>
    /* Professional Royal Blue Design System */
    :root {
        --royal-blue: #0d6efd;
        --royal-blue-dark: #0b5ed7;
        --royal-blue-darker: #084298;
        --royal-blue-light: #e7f1ff;
    }
    
    .row.mb-4 {
        margin-top: 0;
        margin-bottom: 1.5rem !important;
    }
    
    .btn-royal-blue {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        letter-spacing: 0.3px;
        padding: 0.7rem 1.8rem;
        transition: all 0.25s ease;
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.25);
        border-radius: 0.6rem;
    }
    .btn-royal-blue:hover {
        background: linear-gradient(135deg, #0b5ed7 0%, #084298 100%) !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.35);
    }
    .btn-outline-royal {
        border: 1.5px solid #0d6efd;
        color: #0d6efd;
        background: transparent;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        transition: all 0.2s;
        border-radius: 0.6rem;
    }
    .btn-outline-royal:hover {
        background: #0d6efd;
        color: white;
        transform: translateY(-1px);
    }
    
    .main-card {
        border: none !important;
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
        border-radius: 1.25rem !important;
        background: #ffffff;
        overflow: hidden;
    }
    
    .form-label-custom {
        font-size: 0.7rem;
        font-weight: 700;
        color: #1e2a3e;
        margin-bottom: 0.45rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }
    
    .nav-tabs-custom {
        border-bottom: 2px solid #eef2f7;
        background: #ffffff;
        gap: 0.25rem;
        padding: 0 1.5rem;
        margin-bottom: 0;
    }
    .nav-tabs-custom .nav-link {
        border: none !important;
        color: #5a6e85;
        font-weight: 600;
        font-size: 0.85rem;
        padding: 1rem 1.4rem;
        position: relative;
        background: transparent !important;
        transition: all 0.2s;
    }
    .nav-tabs-custom .nav-link:hover {
        color: #0d6efd;
    }
    .nav-tabs-custom .nav-link.active {
        color: #0d6efd !important;
        background: transparent !important;
    }
    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        right: 0;
        height: 3px;
        background: #0d6efd;
        border-radius: 3px 3px 0 0;
    }
    
    .profile-circle-wrapper {
        position: relative;
        display: inline-block;
        cursor: pointer;
        width: 110px;
        height: 110px;
        border-radius: 50%;
    }
    .profile-preview-circle {
        width: 110px;
        height: 110px;
        border-radius: 50% !important;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        background: #f0f2f5;
        display: block;
    }
    .profile-upload-overlay {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #0d6efd;
        border-radius: 50%;
        width: 34px;
        height: 34px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        border: 3px solid white;
        cursor: pointer;
        transition: all 0.2s;
        z-index: 10;
    }
    .profile-upload-overlay:hover {
        background: #0b5ed7;
        transform: scale(1.08);
    }
    
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 0.6rem;
        padding: 0.55rem 0.9rem;
        font-size: 0.85rem;
        transition: all 0.2s;
    }
    .form-control:focus, .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
    }
    
    .is-invalid {
        border-color: #dc3545 !important;
        background-image: none;
    }
    .invalid-feedback-custom {
        font-size: 0.7rem;
        color: #dc3545;
        margin-top: 0.3rem;
        display: block;
        font-weight: 500;
    }
    
    .bank-section-wrapper {
        margin-top: 20px;
    }
    .bank-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        color: #0a58ca;
        letter-spacing: 0.5px;
        margin-bottom: 1.25rem;
        padding-bottom: 0.6rem;
    }
    
    .permission-card {
        background: #ffffff;
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        padding: 0.9rem 1rem;
        transition: all 0.2s;
    }
    .permission-card:hover {
        border-color: #0d6efd;
        box-shadow: 0 2px 8px rgba(13, 110, 253, 0.08);
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem !important;
        }
        .nav-tabs-custom {
            padding: 0 0.5rem;
        }
        .nav-tabs-custom .nav-link {
            padding: 0.7rem 0.8rem;
            font-size: 0.7rem;
        }
        .profile-circle-wrapper {
            width: 80px;
            height: 80px;
        }
        .profile-preview-circle {
            width: 80px;
            height: 80px;
        }
        .profile-upload-overlay {
            width: 28px;
            height: 28px;
            bottom: 2px;
            right: 2px;
        }
    }
    
    .alert-error-summary {
        background: #f8d7da;
        border-left: 4px solid #dc3545;
        border-radius: 0.75rem;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    h4.fw-bold {
        margin-top: 0;
        line-height: 1.3;
    }
    
    .tab-content {
        padding-top: 0.5rem;
    }
    
    .rounded-circle {
        border-radius: 50% !important;
    }
    
    .tab-heading {
        font-size: 20px;
        color: #000;
        margin-bottom: 16px;
    }

    /* Sidebar Permission Styles */
    .sidebar-menu-permission {
        background: #f8fafc;
        border-radius: 0.75rem;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border: 1px solid #eef2f8;
    }
    .sidebar-main-item {
        background: #ffffff;
        border-radius: 0.6rem;
        margin-bottom: 1rem;
        border: 1px solid #e9edf2;
        overflow: hidden;
    }
    .sidebar-main-header {
        background: #f1f5f9;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        border-bottom: 1px solid #e2e8f0;
    }
    .sidebar-main-title {
        font-weight: 700;
        font-size: 0.85rem;
        color: #0a3a6b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .sidebar-sub-items {
        padding: 0.75rem 1rem;
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        background: #ffffff;
    }
    .sub-permission-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.8rem;
        background: #f8fafc;
        padding: 0.25rem 0.75rem;
        border-radius: 2rem;
        border: 1px solid #e2e8f0;
    }
    .sub-permission-item label {
        margin-bottom: 0;
        cursor: pointer;
        font-weight: 500;
        color: #1e2a44;
    }
    .sub-permission-item input {
        cursor: pointer;
        accent-color: #0d6efd;
    }
    .select-all-group {
        font-size: 0.75rem;
        background: #eef2ff;
        padding: 0.2rem 0.6rem;
        border-radius: 1rem;
        cursor: pointer;
        margin-left: auto;
    }
    hr {
        margin: 1rem 0;
        opacity: 0.5;
    }
</style>

<div class="container-fluid py-3">

    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold text-dark mb-1" style="letter-spacing: -0.3px;">Edit Staff Member</h4>
            <p class="text-muted mb-0 small">Update profile, bank details, salary structure & access permissions</p>
        </div>
        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
            <a href="{{ route('admin.staff.directory') }}" class="btn btn-outline-royal rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Directory
            </a>
        </div>
    </div>

    <!-- Validation Errors Summary -->
    @if($errors->any())
    <div class="alert alert-error-summary alert-dismissible fade show mb-4 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2" style="color: #dc3545;"></i>
        <strong class="small">Please fix the following errors:</strong>
        <ul class="mb-0 mt-2 small">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- SINGLE MAIN CARD WITH FORM -->
    <div class="row">
        <div class="col-12">
            <form action="{{ route('admin.staff.update', $staff->id) }}" method="POST" enctype="multipart/form-data" id="staffForm">
                @csrf
                @method('PUT')
                
                <div class="card main-card">
                    
                    <!-- Header with Profile Photo -->
                    <div class="card-body p-4 border-bottom">
                        <div class="row align-items-center gy-4">
                            <div class="col-md-auto text-center text-md-start">
                                <div class="profile-circle-wrapper">
                                    <img src="{{ $staff->profile_photo ? asset($staff->profile_photo) : asset('assets/images/users/avatar-1.jpg') }}" 
                                         class="profile-preview-circle" 
                                         id="profilePreview"
                                         width="110" 
                                         height="110" 
                                         alt="Staff Photo">
                                    <label for="profile_photo_input" class="profile-upload-overlay" style="cursor: pointer;">
                                        <i class="bi bi-camera-fill" style="font-size: 14px;"></i>
                                    </label>
                                    <input type="file" name="profile_photo" id="profile_photo_input" class="d-none" accept="image/jpeg,image/png,image/jpg">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">Profile Photo</label>
                                <div class="small text-muted">Click camera icon to upload</div>
                                <div class="form-text small">JPG, PNG max 2MB</div>
                                @error('profile_photo')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-auto d-none d-md-block text-muted opacity-25 fs-3">|</div>
                            <div class="col-md-3">
                                <label class="form-label-custom">Employee ID</label>
                                <input type="text" class="form-control" value="{{ $staff->employee_id }}" disabled>
                                <small class="text-muted">Employee ID cannot be changed</small>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-custom">System Status <span class="text-danger">*</span></label>
                                <select name="status" class="form-select @error('status') is-invalid @enderror">
                                    <option value="Active" {{ old('status', $staff->status) == 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ old('status', $staff->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="Suspended" {{ old('status', $staff->status) == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback-custom">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="card-header bg-transparent border-bottom p-0">
                        <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                            <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal" type="button">Personal</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bank" type="button">Bank & Payroll</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#salary" type="button">Salary</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#documents" type="button">Documents</button></li>
                            <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#permissions" type="button">Permissions</button></li>
                        </ul>
                    </div>
                </div>
                
                <div class="card secound-card">
                    <div class="card-body p-4 p-xl-5">
                        <div class="tab-content">

                            <!-- ========== PERSONAL DETAILS ========== -->
                            <div class="tab-pane fade show active" id="personal">
                                <h3 class="tab-heading">Personal Details</h3>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label-custom">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', $staff->first_name) }}" placeholder="John">
                                        @error('first_name')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $staff->last_name) }}" placeholder="Doe">
                                        @error('last_name')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Email Address <span class="text-danger">*</span></label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $staff->email) }}" placeholder="john.doe@company.com">
                                        @error('email')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Phone Number</label>
                                        <input type="text" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $staff->phone) }}" placeholder="+1 (555) 000-0000">
                                        @error('phone')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Gender</label>
                                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                            <option value="">Select</option>
                                            <option value="Male" {{ old('gender', $staff->gender) == 'Male' ? 'selected':'' }}>Male</option>
                                            <option value="Female" {{ old('gender', $staff->gender) == 'Female' ? 'selected':'' }}>Female</option>
                                            <option value="Other" {{ old('gender', $staff->gender) == 'Other' ? 'selected':'' }}>Other</option>
                                        </select>
                                        @error('gender')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Date of Birth</label>
                                        <input type="date" class="form-control @error('dob') is-invalid @enderror" name="dob" value="{{ old('dob', $staff->dob) }}">
                                        @error('dob')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Role <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('role') is-invalid @enderror" name="role" value="{{ old('role', $staff->role) }}" placeholder="Enter role (e.g., Manager, Agent)" required>
                                        @error('role')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Login Password</label>
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Leave blank to keep current password">
                                        @error('password')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <!-- ========== BANK & PAYROLL ========== -->
                            <div class="tab-pane fade" id="bank">
                                <h3 class="tab-heading">Bank & Payroll Details</h3>
                                <div class="bank-section-wrapper">
                                    <div class="bank-section-title"><i class="bi bi-bank2 me-2"></i> Complete Bank & Financial Details</div>
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Account Holder Name</label>
                                            <input type="text" name="account_holder_name" class="form-control @error('account_holder_name') is-invalid @enderror" value="{{ old('account_holder_name', $staff->account_holder_name) }}" placeholder="Full name as per bank">
                                            @error('account_holder_name')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Bank Name</label>
                                            <input type="text" name="bank_name" class="form-control @error('bank_name') is-invalid @enderror" value="{{ old('bank_name', $staff->bank_name) }}" placeholder="Chase / HSBC / SBI">
                                            @error('bank_name')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Account Number</label>
                                            <input type="text" name="account_number" class="form-control @error('account_number') is-invalid @enderror" value="{{ old('account_number', $staff->account_number) }}" placeholder="XXXXXXXXXXXX1234">
                                            @error('account_number')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-custom">IFSC / Routing Code</label>
                                            <input type="text" name="ifsc_code" class="form-control @error('ifsc_code') is-invalid @enderror" value="{{ old('ifsc_code', $staff->ifsc_code) }}" placeholder="ABCD0123456">
                                            @error('ifsc_code')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Branch Location</label>
                                            <input type="text" name="branch_location" class="form-control @error('branch_location') is-invalid @enderror" value="{{ old('branch_location', $staff->branch_location) }}" placeholder="Downtown, NY">
                                            @error('branch_location')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label-custom">Account Type</label>
                                            <select name="account_type" class="form-select @error('account_type') is-invalid @enderror">
                                                <option value="Savings" {{ old('account_type', $staff->account_type) == 'Savings' ? 'selected':'' }}>Savings Account</option>
                                                <option value="Current" {{ old('account_type', $staff->account_type) == 'Current' ? 'selected':'' }}>Current Account</option>
                                                <option value="Salary" {{ old('account_type', $staff->account_type) == 'Salary' ? 'selected':'' }}>Salary Account</option>
                                            </select>
                                            @error('account_type')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <hr class="my-4">
                                    <div class="row">
                                        <div class="col-12">
                                            <label class="form-label-custom">Payroll / Additional Notes</label>
                                            <textarea name="payroll_notes" class="form-control @error('payroll_notes') is-invalid @enderror" rows="2" placeholder="Tax regime, deductions, special instructions...">{{ old('payroll_notes', $staff->payroll_notes) }}</textarea>
                                            @error('payroll_notes')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ========== SALARY STRUCTURE ========== -->
                            <div class="tab-pane fade" id="salary">
                                <h3 class="tab-heading">Salary Details</h3>
                                <div class="row g-4">
                                    <div class="col-md-3">
                                        <label class="form-label-custom">Basic Salary</label>
                                        <input type="number" step="0.01" name="basic_salary" class="form-control @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary', $staff->basic_salary) }}" placeholder="0.00">
                                        @error('basic_salary')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-custom">HRA</label>
                                        <input type="number" step="0.01" name="hra" class="form-control @error('hra') is-invalid @enderror" value="{{ old('hra', $staff->hra) }}" placeholder="0.00">
                                        @error('hra')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-custom">Special Allowance</label>
                                        <input type="number" step="0.01" name="allowance" class="form-control @error('allowance') is-invalid @enderror" value="{{ old('allowance', $staff->allowance) }}" placeholder="0.00">
                                        @error('allowance')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label-custom">Performance Bonus</label>
                                        <input type="number" step="0.01" name="bonus" class="form-control @error('bonus') is-invalid @enderror" value="{{ old('bonus', $staff->bonus) }}" placeholder="0.00">
                                        @error('bonus')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Provident Fund (%)</label>
                                        <input type="text" name="pf_percent" class="form-control @error('pf_percent') is-invalid @enderror" value="{{ old('pf_percent', $staff->pf_percent) }}" placeholder="12%">
                                        @error('pf_percent')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label-custom">Professional Tax</label>
                                        <input type="number" step="0.01" name="professional_tax" class="form-control @error('professional_tax') is-invalid @enderror" value="{{ old('professional_tax', $staff->professional_tax) }}" placeholder="0.00">
                                        @error('professional_tax')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <!-- ========== DOCUMENTS ========== -->
                            <div class="tab-pane fade" id="documents">
                                <h3 class="tab-heading">Documents Details</h3>
                                <div class="row g-4">
                                    <div class="col-md-4">
                                        <label class="form-label-custom">PAN Card / Tax ID</label>
                                        <input type="file" name="pan_card_file" class="form-control @error('pan_card_file') is-invalid @enderror">
                                        @if($staff->pan_card_file)
                                            <small class="text-muted d-block mt-1">Current file uploaded</small>
                                        @endif
                                        @error('pan_card_file')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-custom">Cancelled Cheque</label>
                                        <input type="file" name="cancelled_cheque" class="form-control @error('cancelled_cheque') is-invalid @enderror">
                                        @if($staff->cancelled_cheque)
                                            <small class="text-muted d-block mt-1">Current file uploaded</small>
                                        @endif
                                        @error('cancelled_cheque')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label-custom">Aadhaar / Govt ID</label>
                                        <input type="file" name="aadhaar_file" class="form-control @error('aadhaar_file') is-invalid @enderror">
                                        @if($staff->aadhaar_file)
                                            <small class="text-muted d-block mt-1">Current file uploaded</small>
                                        @endif
                                        @error('aadhaar_file')<div class="invalid-feedback-custom">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <!-- ========== PERMISSIONS (Based on Sidebar Structure) ========== -->
                            <div class="tab-pane fade" id="permissions">
                                <h3 class="tab-heading">Permission Details</h3>
                                <div class="mb-3 p-3 bg-light rounded-3">
                                    <i class="bi bi-info-circle-fill text-primary me-2"></i>
                                    <small class="text-muted">Define access levels for each sidebar menu item. Check boxes grant access to specific pages.</small>
                                </div>

                                @php
                                    $permissions = old('permissions', is_array($staff->permissions) ? $staff->permissions : []);
                                @endphp

                                <div class="sidebar-menu-permission">
                                    <!-- Dashboard Access -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
                                                <span>Dashboard</span>
                                            </div>
                                            <div class="ms-auto">
                                                <label class="sub-permission-item mb-0">
                                                    <input type="checkbox" name="permissions[dashboard]" value="1" {{ isset($permissions['dashboard']) && $permissions['dashboard'] == '1' ? 'checked' : '' }}>
                                                    <span class="ms-1">Grant Access</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Verification / Product Management -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:checklist-bold-duotone"></iconify-icon>
                                                <span>{{ session('admin_panel') == 'travel' ? 'Service Management' : 'Product Management' }}</span>
                                            </div>
                                            <div class="select-all-group" data-group="verification">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="verification">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[product_verification]" value="1" {{ isset($permissions['product_verification']) && $permissions['product_verification'] == '1' ? 'checked' : '' }}> Product/Service Verification</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[product_categories]" value="1" {{ isset($permissions['product_categories']) && $permissions['product_categories'] == '1' ? 'checked' : '' }}> {{ session('admin_panel') == 'travel' ? 'Service Categories' : 'Product Categories' }}</label>
                                        </div>
                                    </div>

                                    <!-- Order Management -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:cart-large-4-bold-duotone"></iconify-icon>
                                                <span>Order Management</span>
                                            </div>
                                            <div class="select-all-group" data-group="orders">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="orders">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[all_orders]" value="1" {{ isset($permissions['all_orders']) && $permissions['all_orders'] == '1' ? 'checked' : '' }}> All Orders</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[payment_monitoring]" value="1" {{ isset($permissions['payment_monitoring']) && $permissions['payment_monitoring'] == '1' ? 'checked' : '' }}> Payment Monitoring</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[order_lifecycle]" value="1" {{ isset($permissions['order_lifecycle']) && $permissions['order_lifecycle'] == '1' ? 'checked' : '' }}> Order Lifecycle</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[cancellation_returns]" value="1" {{ isset($permissions['cancellation_returns']) && $permissions['cancellation_returns'] == '1' ? 'checked' : '' }}> Cancellation & Returns</label>
                                        </div>
                                    </div>

                                    <!-- Seller Management -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:shop-bold-duotone"></iconify-icon>
                                                <span>Seller Management</span>
                                            </div>
                                            <div class="select-all-group" data-group="sellers">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="sellers">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[all_sellers]" value="1" {{ isset($permissions['all_sellers']) && $permissions['all_sellers'] == '1' ? 'checked' : '' }}> All Sellers</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[blacklist_control]" value="1" {{ isset($permissions['blacklist_control']) && $permissions['blacklist_control'] == '1' ? 'checked' : '' }}> Blacklist Control</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[logs_audit]" value="1" {{ isset($permissions['logs_audit']) && $permissions['logs_audit'] == '1' ? 'checked' : '' }}> Logs & Audit</label>
                                        </div>
                                    </div>

                                    <!-- Customer Management -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
                                                <span>Customer Management</span>
                                            </div>
                                            <div class="select-all-group" data-group="customers">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="customers">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[customer_control]" value="1" {{ isset($permissions['customer_control']) && $permissions['customer_control'] == '1' ? 'checked' : '' }}> Customer Control (View/Block/Merge)</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[customer_insights]" value="1" {{ isset($permissions['customer_insights']) && $permissions['customer_insights'] == '1' ? 'checked' : '' }}> Insights (High-Value/Frequent/Bad Reviews)</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[customer_validation]" value="1" {{ isset($permissions['customer_validation']) && $permissions['customer_validation'] == '1' ? 'checked' : '' }}> Data Validation (Address/Duplicates)</label>
                                        </div>
                                    </div>

                                    <!-- Finance Management -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
                                                <span>Finance Management</span>
                                            </div>
                                            <label class="sub-permission-item mb-0">
                                                <input type="checkbox" name="permissions[finance]" value="1" {{ isset($permissions['finance']) && $permissions['finance'] == '1' ? 'checked' : '' }}>
                                                <span class="ms-1">Full Access</span>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Marketplace Control -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:shop-2-bold-duotone"></iconify-icon>
                                                <span>Marketplace Control</span>
                                            </div>
                                            <div class="select-all-group" data-group="marketplace">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="marketplace">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[seller_verification]" value="1" {{ isset($permissions['seller_verification']) && $permissions['seller_verification'] == '1' ? 'checked' : '' }}> Seller Verification</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[advertisements]" value="1" {{ isset($permissions['advertisements']) && $permissions['advertisements'] == '1' ? 'checked' : '' }}> Advertisements</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[promotion_campaigns]" value="1" {{ isset($permissions['promotion_campaigns']) && $permissions['promotion_campaigns'] == '1' ? 'checked' : '' }}> Promotion Campaigns</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[search_discovery]" value="1" {{ isset($permissions['search_discovery']) && $permissions['search_discovery'] == '1' ? 'checked' : '' }}> Search & Discovery</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[smart_suggestions]" value="1" {{ isset($permissions['smart_suggestions']) && $permissions['smart_suggestions'] == '1' ? 'checked' : '' }}> Smart Suggestions</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[dynamic_pages]" value="1" {{ isset($permissions['dynamic_pages']) && $permissions['dynamic_pages'] == '1' ? 'checked' : '' }}> Dynamic Pages</label>
                                        </div>
                                    </div>

                                    <!-- Reports & Analytics -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:graph-bold-duotone"></iconify-icon>
                                                <span>Reports & Analytics</span>
                                            </div>
                                            <div class="select-all-group" data-group="reports">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="reports">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[seller_product_report]" value="1" {{ isset($permissions['seller_product_report']) && $permissions['seller_product_report'] == '1' ? 'checked' : '' }}> Seller Product Report</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[top_selling]" value="1" {{ isset($permissions['top_selling']) && $permissions['top_selling'] == '1' ? 'checked' : '' }}> Top Selling Products</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[seller_negative_reviews]" value="1" {{ isset($permissions['seller_negative_reviews']) && $permissions['seller_negative_reviews'] == '1' ? 'checked' : '' }}> Seller Negative Reviews</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[highest_rating]" value="1" {{ isset($permissions['highest_rating']) && $permissions['highest_rating'] == '1' ? 'checked' : '' }}> Highest Rating Report</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[lowest_rating]" value="1" {{ isset($permissions['lowest_rating']) && $permissions['lowest_rating'] == '1' ? 'checked' : '' }}> Lowest Rating Report</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[seller_report]" value="1" {{ isset($permissions['seller_report']) && $permissions['seller_report'] == '1' ? 'checked' : '' }}> Seller Report</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[total_sales_report]" value="1" {{ isset($permissions['total_sales_report']) && $permissions['total_sales_report'] == '1' ? 'checked' : '' }}> Total Sales Report</label>
                                        </div>
                                    </div>

                                    <!-- Master Settings (including Staff Management) -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                                                <span>Master Settings</span>
                                            </div>
                                            <div class="select-all-group" data-group="settings">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="settings">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[staff_management]" value="1" {{ isset($permissions['staff_management']) && $permissions['staff_management'] == '1' ? 'checked' : '' }}> Staff Management (Directory)</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[website_templates]" value="1" {{ isset($permissions['website_templates']) && $permissions['website_templates'] == '1' ? 'checked' : '' }}> Website Templates</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[api_integrations]" value="1" {{ isset($permissions['api_integrations']) && $permissions['api_integrations'] == '1' ? 'checked' : '' }}> API & Integrations</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[coupon_management]" value="1" {{ isset($permissions['coupon_management']) && $permissions['coupon_management'] == '1' ? 'checked' : '' }}> Coupon Management</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[qr_management]" value="1" {{ isset($permissions['qr_management']) && $permissions['qr_management'] == '1' ? 'checked' : '' }}> QR Code Management</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[sitemap]" value="1" {{ isset($permissions['sitemap']) && $permissions['sitemap'] == '1' ? 'checked' : '' }}> Sitemap Generation</label>
                                            @if(session('admin_panel') == 'product')
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[change_password]" value="1" {{ isset($permissions['change_password']) && $permissions['change_password'] == '1' ? 'checked' : '' }}> Change Password</label>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Commission Management -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:wallet-bold-duotone"></iconify-icon>
                                                <span>Commission Management</span>
                                            </div>
                                            <div class="select-all-group" data-group="commission">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="commission">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[commission_report]" value="1" {{ isset($permissions['commission_report']) && $permissions['commission_report'] == '1' ? 'checked' : '' }}> Commission Report</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[commission_settings]" value="1" {{ isset($permissions['commission_settings']) && $permissions['commission_settings'] == '1' ? 'checked' : '' }}> Commission Settings</label>
                                        </div>
                                    </div>

                                    <!-- Coupons -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:ticket-bold-duotone"></iconify-icon>
                                                <span>{{ session('admin_panel') == 'travel' ? 'Service Coupons' : 'Coupons' }}</span>
                                            </div>
                                            <div class="select-all-group" data-group="coupons">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="coupons">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[all_coupons]" value="1" {{ isset($permissions['all_coupons']) && $permissions['all_coupons'] == '1' ? 'checked' : '' }}> All Coupons</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[add_coupon]" value="1" {{ isset($permissions['add_coupon']) && $permissions['add_coupon'] == '1' ? 'checked' : '' }}> Add Coupon</label>
                                        </div>
                                    </div>

                                    <!-- Tickets -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:ticket-outline"></iconify-icon>
                                                <span>{{ session('admin_panel') == 'travel' ? 'Service Tickets' : 'Tickets' }}</span>
                                            </div>
                                            <div class="select-all-group" data-group="tickets">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="tickets">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[all_tickets]" value="1" {{ isset($permissions['all_tickets']) && $permissions['all_tickets'] == '1' ? 'checked' : '' }}> All Tickets</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[create_ticket]" value="1" {{ isset($permissions['create_ticket']) && $permissions['create_ticket'] == '1' ? 'checked' : '' }}> Create Ticket</label>
                                        </div>
                                    </div>

                                    <!-- Refunds -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:hand-cash-bold-duotone"></iconify-icon>
                                                <span>Refunds</span>
                                            </div>
                                            <div class="select-all-group" data-group="refunds">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="refunds">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[refund_requests]" value="1" {{ isset($permissions['refund_requests']) && $permissions['refund_requests'] == '1' ? 'checked' : '' }}> Refund Requests</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[refund_reasons]" value="1" {{ isset($permissions['refund_reasons']) && $permissions['refund_reasons'] == '1' ? 'checked' : '' }}> Refund Reasons</label>
                                        </div>
                                    </div>

                                    <!-- Reviews & Ratings -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:chat-round-like-bold-duotone"></iconify-icon>
                                                <span>Reviews & Ratings</span>
                                            </div>
                                            <div class="select-all-group" data-group="reviews">Select All</div>
                                        </div>
                                        <div class="sidebar-sub-items" data-group="reviews">
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[all_reviews]" value="1" {{ isset($permissions['all_reviews']) && $permissions['all_reviews'] == '1' ? 'checked' : '' }}> All Reviews</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[approved_reviews]" value="1" {{ isset($permissions['approved_reviews']) && $permissions['approved_reviews'] == '1' ? 'checked' : '' }}> Approved Reviews</label>
                                            <label class="sub-permission-item"><input type="checkbox" name="permissions[pending_reviews]" value="1" {{ isset($permissions['pending_reviews']) && $permissions['pending_reviews'] == '1' ? 'checked' : '' }}> Pending Reviews</label>
                                        </div>
                                    </div>

                                    <!-- Activity Log -->
                                    <div class="sidebar-main-item">
                                        <div class="sidebar-main-header">
                                            <div class="sidebar-main-title">
                                                <iconify-icon icon="solar:clock-circle-bold-duotone"></iconify-icon>
                                                <span>Activity Log</span>
                                            </div>
                                            <label class="sub-permission-item mb-0">
                                                <input type="checkbox" name="permissions[activity_log]" value="1" {{ isset($permissions['activity_log']) && $permissions['activity_log'] == '1' ? 'checked' : '' }}>
                                                <span class="ms-1">Grant Access</span>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-5 pt-3 border-top d-flex justify-content-end gap-3">
                            <button type="button" class="btn btn-outline-royal px-4" onclick="window.history.back();">Cancel</button>
                            <button type="submit" class="btn btn-royal-blue px-5">
                                <i class="bi bi-save2 me-2"></i> Update Staff Member
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script>
    // Profile Image Preview
    document.getElementById('profile_photo_input')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const preview = document.getElementById('profilePreview');
                if (preview) {
                    preview.src = event.target.result;
                }
            };
            reader.readAsDataURL(file);
        }
    });

    // Auto-hide validation error messages
    document.addEventListener('DOMContentLoaded', function() {
        const formInputs = document.querySelectorAll('input, select, textarea');
        
        formInputs.forEach(input => {
            input.addEventListener('input', function() {
                removeError(this);
            });
            input.addEventListener('change', function() {
                removeError(this);
            });
        });
        
        function removeError(element) {
            element.classList.remove('is-invalid');
            const errorDiv = element.parentElement?.querySelector('.invalid-feedback-custom');
            if (errorDiv) {
                errorDiv.remove();
            }
            const nextSibling = element.nextElementSibling;
            if (nextSibling && nextSibling.classList?.contains('invalid-feedback-custom')) {
                nextSibling.remove();
            }
        }
        
        const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
        tabButtons.forEach(tab => {
            tab.addEventListener('shown.bs.tab', function(e) {
                const targetTab = document.querySelector(e.target.getAttribute('data-bs-target'));
                if (targetTab) {
                    const inputsInTab = targetTab.querySelectorAll('input, select, textarea');
                    inputsInTab.forEach(input => {
                        removeError(input);
                    });
                }
            });
        });

        // Select All functionality for permission groups
        document.querySelectorAll('.select-all-group').forEach(selectAllBtn => {
            selectAllBtn.addEventListener('click', function() {
                const group = this.getAttribute('data-group');
                const groupContainer = this.closest('.sidebar-main-item').querySelector('.sidebar-sub-items');
                if (groupContainer) {
                    const checkboxes = groupContainer.querySelectorAll('input[type="checkbox"]');
                    const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                    checkboxes.forEach(cb => cb.checked = !allChecked);
                } else {
                    const checkbox = this.closest('.sidebar-main-item').querySelector('input[type="checkbox"]');
                    if (checkbox) checkbox.checked = !checkbox.checked;
                }
            });
        });
    });
</script>

<style>
    .form-control.is-invalid {
        border-color: #dc3545 !important;
        background-image: none;
    }
    .invalid-feedback-custom {
        font-size: 0.7rem;
        color: #dc3545;
        margin-top: 0.3rem;
        display: block;
        font-weight: 500;
    }
    .select-all-group {
        font-size: 0.7rem;
        background: #e2e8f0;
        padding: 0.2rem 0.7rem;
        border-radius: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .select-all-group:hover {
        background: #0d6efd;
        color: white;
    }
</style>

@endsection