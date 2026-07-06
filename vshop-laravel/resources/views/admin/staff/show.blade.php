@extends('layouts.vertical', ['title' => 'Staff Details'])

@section('content')

<style>
    .btn-royal-blue {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%) !important;
        border: none !important;
        color: #ffffff !important;
        font-weight: 600;
        padding: 0.6rem 1.5rem;
        border-radius: 0.6rem;
        transition: all 0.2s;
    }
    
    .btn-royal-blue:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(13, 110, 253, 0.3);
    }
    
    .btn-outline-royal {
        border: 1.5px solid #0d6efd;
        color: #0d6efd;
        background: transparent;
        font-weight: 600;
        padding: 0.6rem 1.2rem;
        border-radius: 0.6rem;
        transition: all 0.2s;
    }
    
    .btn-outline-royal:hover {
        background: #0d6efd;
        color: white;
    }
    
    .main-card {
        border: none !important;
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
        border-radius: 1.25rem !important;
        background: #ffffff;
        overflow: hidden;
    }
    
    .info-section {
        background: #f8fafc;
        border-radius: 1rem;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    
    .info-section:hover {
        background: #e7f1ff;
        transform: translateX(3px);
    }
    
    .info-label {
        font-size: 0.7rem;
        font-weight: 700;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }
    
    .info-value {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0;
    }
    
    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #0d6efd;
        margin-bottom: 1.25rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid #e9ecef;
    }
    
    .staff-avatar-large {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid #ffffff;
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
    }
    
    .status-badge {
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }
    
    .status-active {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .status-inactive {
        background: #f8d7da;
        color: #842029;
    }
    
    .status-suspended {
        background: #fff3cd;
        color: #856404;
    }
    
    .permission-badge {
        display: inline-block;
        padding: 3px 10px;
        border-radius: 15px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .badge-full {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .badge-view {
        background: #cff4fc;
        color: #055160;
    }
    
    .badge-edit {
        background: #fff3cd;
        color: #856404;
    }
    
    .badge-none {
        background: #f8d7da;
        color: #842029;
    }
    
    .salary-card {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border-radius: 1rem;
        padding: 1.25rem;
        color: white;
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
    
    .nav-tabs-custom {
        border-top: 2px solid #eef2f7;
        background: #ffffff;
        gap: 0.25rem;
        padding: 0 1.5rem;
        margin-bottom: 0;
        margin-top: 20px;
        border-bottom: 0;
    }
    
    .flow-sec {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 30px;
    }
    
    .flow-box {
        width: 25%;
        text-align: center;
        position: relative;
        padding: 0 20px;
    }
    
    .flow-box .long-arrow {
        font-size: 24px;
        position: absolute;
        right: -11px;
        top: 50%;
        transform: translate(0, -50%);
    }
    
    .flow-box h3 {
        font-size: 18px;
        color: #000;
        font-weight: 600;
    }
    
    .flow-box p {
        font-size: 16px;
        margin: 0;
    }
    
    .flow-box .icon {
        margin-bottom: 20px;
    }
    
    .flow-wrapper {
        margin-top: 40px;
    }
    
    .flow-wrapper h2 {
        font-size: 24px;
        color: #000;
    }
    
    .sidebar-permission-group {
        margin-bottom: 1.5rem;
        border: 1px solid #e9ecef;
        border-radius: 0.75rem;
        overflow: hidden;
    }
    
    .sidebar-permission-header {
        background: #f8fafc;
        padding: 0.75rem 1rem;
        font-weight: 700;
        font-size: 0.85rem;
        color: #0a3a6b;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-bottom: 1px solid #e9ecef;
    }
    
    .sidebar-permission-items {
        padding: 0.75rem 1rem;
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        background: #ffffff;
    }
    
    .sub-permission-view {
        background: #f1f5f9;
        padding: 0.3rem 0.8rem;
        border-radius: 2rem;
        font-size: 0.75rem;
        font-weight: 500;
        color: #1e293b;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem !important;
        }
        .staff-avatar-large {
            width: 70px;
            height: 70px;
        }
        .info-value {
            font-size: 0.85rem;
        }
        .flow-sec {
            flex-wrap: wrap;
        }
        .flow-box {
            width: 50%;
            margin-bottom: 20px;
        }
        .flow-box .long-arrow {
            display: none;
        }
    }
    @media(max-width: 480px) {
        .flow-box {
            width: 100%;
        }
    }
</style>

<div class="container-fluid py-3">

    <!-- Header -->
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h4 class="fw-bold text-dark mb-1">Staff Details</h4>
            <p class="text-muted mb-0 small">Complete employee information</p>
        </div>
        <div class="col-md-4 text-md-end text-start mt-3 mt-md-0">
            <a href="{{ route('admin.staff.directory') }}" class="btn btn-outline-royal rounded-3">
                <i class="bi bi-arrow-left me-1"></i> Back to Directory
            </a>
        </div>
    </div>

    <!-- Profile Header Card -->
    <div class="card main-card mb-4">
        <div class="card-body p-4 pb-0">
            <div class="row align-items-center">
                <div class="col-md-auto text-center text-md-start">
                    <img src="{{ $staff->profile_photo ? Storage::url($staff->profile_photo) : asset('assets/images/users/avatar-1.jpg') }}" 
                         class="staff-avatar-large" 
                         alt="{{ $staff->first_name }}">
                </div>
                <div class="col-md">
                    <h2 class="fw-bold mb-1">{{ $staff->first_name }} {{ $staff->last_name }}</h2>
                    <p class="text-muted mb-2">
                        <i class="bi bi-envelope me-1"></i> {{ $staff->email }} &nbsp;|&nbsp;
                        <i class="bi bi-phone me-1"></i> {{ $staff->phone ?? 'N/A' }} &nbsp;|&nbsp;
                        <i class="bi bi-person-badge me-1"></i> {{ $staff->employee_id }}
                    </p>
                    <span class="status-badge 
                        @if($staff->status == 'Active') status-active
                        @elseif($staff->status == 'Inactive') status-inactive
                        @else status-suspended @endif">
                        {{ $staff->status }}
                    </span>
                </div>
                <div class="col-md-auto text-md-end mt-3 mt-md-0">
                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="btn btn-royal-blue">
                        <i class="bi bi-pencil me-1"></i> Edit Profile
                    </a>
                </div>
            </div>
            
            <!-- Tabs Navigation -->
            <div class="card-header bg-transparent border-bottom p-0 mt-3">
                <ul class="nav nav-tabs nav-tabs-custom" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#personal" type="button">Personal</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#bank" type="button">Bank & Payroll</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#employment" type="button">Employee</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#salary" type="button">Salary</button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#permissions" type="button">Permissions</button></li>
                </ul>
            </div>
        </div>
    </div>
    
    <div class="card main-card">
        <div class="tab-content">
            <!-- ========== PERSONAL DETAILS TAB ========== -->
            <div class="tab-pane fade show active" id="personal">
                <div class="card-body p-4">
                    <h5 class="section-title">
                        <i class="bi bi-person-circle me-2"></i> Personal Information
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Full Name</div>
                                <div class="info-value">{{ $staff->first_name }} {{ $staff->last_name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Employee ID</div>
                                <div class="info-value">{{ $staff->employee_id }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Email Address</div>
                                <div class="info-value">{{ $staff->email }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Phone Number</div>
                                <div class="info-value">{{ $staff->phone ?? 'Not Provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Gender</div>
                                <div class="info-value">{{ $staff->gender ?? 'Not Specified' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Date of Birth</div>
                                <div class="info-value">{{ $staff->dob ? date('d M Y', strtotime($staff->dob)) : 'Not Provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">System Status</div>
                                <div class="info-value">
                                    <span class="status-badge 
                                        @if($staff->status == 'Active') status-active
                                        @elseif($staff->status == 'Inactive') status-inactive
                                        @else status-suspended @endif">
                                        {{ $staff->status }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ========== BANK & PAYROLL TAB ========== -->
            <div class="tab-pane fade" id="bank">
                <div class="card-body p-4">
                    <h5 class="section-title">
                        <i class="bi bi-bank2 me-2"></i> Bank Details
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Account Holder Name</div>
                                <div class="info-value">{{ $staff->account_holder_name ?? 'Not Provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Bank Name</div>
                                <div class="info-value">{{ $staff->bank_name ?? 'Not Provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Account Number</div>
                                <div class="info-value">
                                    @if($staff->account_number)
                                        {{ substr($staff->account_number, 0, 4) . str_repeat('*', strlen($staff->account_number) - 8) . substr($staff->account_number, -4) }}
                                    @else
                                        Not Provided
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">IFSC / Routing Code</div>
                                <div class="info-value">{{ $staff->ifsc_code ?? 'Not Provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Branch Location</div>
                                <div class="info-value">{{ $staff->branch_location ?? 'Not Provided' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Account Type</div>
                                <div class="info-value">{{ $staff->account_type ?? 'Not Provided' }}</div>
                            </div>
                        </div>
                    </div>
                    
                    @if($staff->payroll_notes)
                    <h5 class="section-title mt-3">
                        <i class="bi bi-file-text me-2"></i> Payroll Notes
                    </h5>
                    <div class="info-section">
                        <div class="info-value">{{ $staff->payroll_notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
            
            <!-- ========== EMPLOYMENT TAB ========== -->
            <div class="tab-pane fade" id="employment">
                <div class="card-body p-4">
                    <h5 class="section-title">
                        <i class="bi bi-briefcase me-2"></i> Employment Details
                    </h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Department</div>
                                <div class="info-value">{{ $staff->department ?? 'Not Assigned' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Designation</div>
                                <div class="info-value">{{ $staff->designation ?? 'Not Assigned' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Employee Type</div>
                                <div class="info-value">{{ $staff->employee_type ?? 'Not Specified' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">Joining Date</div>
                                <div class="info-value">{{ $staff->joining_date ? date('d M Y', strtotime($staff->joining_date)) : 'Not Set' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-section">
                                <div class="info-label">System Role</div>
                                <div class="info-value">{{ $staff->role_name ?? 'Not Assigned' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- ========== SALARY TAB ========== -->
        <!-- ========== SALARY TAB ========== -->
<div class="tab-pane fade" id="salary">
    <div class="card-body p-4">
        <h5 class="section-title">
            <i class="bi bi-currency-rupee me-2"></i> Salary Structure
        </h5>
        
        @php
            $totalEarnings = ($staff->basic_salary ?? 0) + ($staff->hra ?? 0) + ($staff->allowance ?? 0) + ($staff->bonus ?? 0);
            $totalDeductions = ($staff->professional_tax ?? 0) + (($staff->basic_salary ?? 0) * ($staff->pf_percent ?? 0) / 100);
            $netSalary = $totalEarnings - $totalDeductions;
        @endphp
        
        <div class="row">
            <div class="col-md-6">
                <div class="info-section">
                    <div class="info-label">Basic Salary</div>
                    <div class="info-value">₹ {{ number_format($staff->basic_salary ?? 0, 2) }}</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-section">
                    <div class="info-label">HRA</div>
                    <div class="info-value">₹ {{ number_format($staff->hra ?? 0, 2) }}</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-section">
                    <div class="info-label">Allowance</div>
                    <div class="info-value">₹ {{ number_format($staff->allowance ?? 0, 2) }}</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-section">
                    <div class="info-label">Bonus</div>
                    <div class="info-value">₹ {{ number_format($staff->bonus ?? 0, 2) }}</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-section">
                    <div class="info-label">PF ({{ $staff->pf_percent ?? 0 }}%)</div>
                    <div class="info-value text-danger">- ₹ {{ number_format(($staff->basic_salary ?? 0) * ($staff->pf_percent ?? 0) / 100, 2) }}</div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-section">
                    <div class="info-label">Professional Tax</div>
                    <div class="info-value text-danger">- ₹ {{ number_format($staff->professional_tax ?? 0, 2) }}</div>
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="info-section" style="background: #0d6efd; color: white;">
                    <div class="info-label" >Net Salary</div>
                    <div class="info-value" style="color: white; font-size: 1.2rem;">₹ {{ number_format($netSalary, 2) }}</div>
                </div>
            </div>
            
            @if($staff->last_salary_amount ?? 0)
            <div class="col-md-12">
                <div class="info-section" style="background:  #0d6efd; color: white;">
                    <div class="info-label" style="color: rgba(255,255,255,0.9);">Last Salary</div>
                    <div class="info-value" style="color: white;">₹ {{ number_format($staff->last_salary_amount, 2) }}</div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
            
            <!-- ========== PERMISSIONS TAB (Dynamic Sidebar Based) ========== -->
            <div class="tab-pane fade" id="permissions">
                <div class="card-body p-4">
                    <h5 class="section-title">
                        <i class="bi bi-shield-lock me-2"></i> Access Permissions
                    </h5>
                    
                    @php
                        $permissions = is_array($staff->permissions) ? $staff->permissions : [];
                        
                        $sidebarPermissions = [
                            'dashboard' => ['label' => 'Dashboard', 'icon' => 'solar:home-2-bold-duotone'],
                            'product_management' => ['label' => session('admin_panel') == 'travel' ? 'Service Management' : 'Product Management', 'icon' => 'solar:checklist-bold-duotone', 'subitems' => [
                                'product_verification' => session('admin_panel') == 'travel' ? 'Service Verification' : 'Product Verification',
                                'product_categories' => session('admin_panel') == 'travel' ? 'Service Categories' : 'Product Categories'
                            ]],
                            'order_management' => ['label' => 'Order Management', 'icon' => 'solar:cart-large-4-bold-duotone', 'subitems' => [
                                'all_orders' => 'All Orders',
                                'payment_monitoring' => 'Payment Monitoring',
                                'order_lifecycle' => 'Order Lifecycle',
                                'cancellation_returns' => 'Cancellation & Returns'
                            ]],
                            'seller_management' => ['label' => 'Seller Management', 'icon' => 'solar:shop-bold-duotone', 'subitems' => [
                                'all_sellers' => 'All Sellers',
                                'blacklist_control' => 'Blacklist Control',
                                'logs_audit' => 'Logs & Audit'
                            ]],
                            'customer_management' => ['label' => 'Customer Management', 'icon' => 'solar:users-group-rounded-bold-duotone', 'subitems' => [
                                'customer_control' => 'Customer Control (View/Block/Merge)',
                                'customer_insights' => 'Insights (High-Value/Frequent/Bad Reviews)',
                                'customer_validation' => 'Data Validation (Address/Duplicates)'
                            ]],
                            'finance' => ['label' => 'Finance Management', 'icon' => 'solar:wallet-money-bold-duotone'],
                            'marketplace' => ['label' => 'Marketplace Control', 'icon' => 'solar:shop-2-bold-duotone', 'subitems' => [
                                'seller_verification' => 'Seller Verification',
                                'advertisements' => 'Advertisements',
                                'promotion_campaigns' => 'Promotion Campaigns',
                                'search_discovery' => 'Search & Discovery',
                                'smart_suggestions' => 'Smart Suggestions',
                                'dynamic_pages' => 'Dynamic Pages'
                            ]],
                            'reports' => ['label' => 'Reports & Analytics', 'icon' => 'solar:graph-bold-duotone', 'subitems' => [
                                'seller_product_report' => 'Seller Product Report',
                                'top_selling' => 'Top Selling Products',
                                'seller_negative_reviews' => 'Seller Negative Reviews',
                                'highest_rating' => 'Highest Rating Report',
                                'lowest_rating' => 'Lowest Rating Report',
                                'seller_report' => 'Seller Report',
                                'total_sales_report' => 'Total Sales Report'
                            ]],
                            'settings' => ['label' => 'Master Settings', 'icon' => 'solar:settings-bold-duotone', 'subitems' => [
                                'staff_management' => 'Staff Management',
                                'website_templates' => 'Website Templates',
                                'api_integrations' => 'API & Integrations',
                                'coupon_management' => 'Coupon Management',
                                'qr_management' => 'QR Code Management',
                                'sitemap' => 'Sitemap Generation'
                            ]],
                            'commission' => ['label' => 'Commission Management', 'icon' => 'solar:wallet-bold-duotone', 'subitems' => [
                                'commission_report' => 'Commission Report',
                                'commission_settings' => 'Commission Settings'
                            ]],
                            'coupons' => ['label' => session('admin_panel') == 'travel' ? 'Service Coupons' : 'Coupons', 'icon' => 'solar:ticket-bold-duotone', 'subitems' => [
                                'all_coupons' => 'All Coupons',
                                'add_coupon' => 'Add Coupon'
                            ]],
                            'tickets' => ['label' => session('admin_panel') == 'travel' ? 'Service Tickets' : 'Tickets', 'icon' => 'solar:ticket-outline', 'subitems' => [
                                'all_tickets' => 'All Tickets',
                                'create_ticket' => 'Create Ticket'
                            ]],
                            'refunds' => ['label' => 'Refunds', 'icon' => 'solar:hand-cash-bold-duotone', 'subitems' => [
                                'refund_requests' => 'Refund Requests',
                                'refund_reasons' => 'Refund Reasons'
                            ]],
                            'reviews' => ['label' => 'Reviews & Ratings', 'icon' => 'solar:chat-round-like-bold-duotone', 'subitems' => [
                                'all_reviews' => 'All Reviews',
                                'approved_reviews' => 'Approved Reviews',
                                'pending_reviews' => 'Pending Reviews'
                            ]],
                            'activity_log' => ['label' => 'Activity Log', 'icon' => 'solar:clock-circle-bold-duotone']
                        ];
                    @endphp
                    
                    <div class="row">
                        @foreach($sidebarPermissions as $key => $section)
                            <div class="col-12 mb-3">
                                <div class="sidebar-permission-group">
                                    <div class="sidebar-permission-header">
                                        <iconify-icon icon="{{ $section['icon'] }}" width="18" height="18"></iconify-icon>
                                        <span>{{ $section['label'] }}</span>
                                    </div>
                                    <div class="sidebar-permission-items">
                                        @if(isset($section['subitems']))
                                            @foreach($section['subitems'] as $permKey => $permLabel)
                                                <span class="sub-permission-view">
                                                    <i class="bi {{ isset($permissions[$permKey]) && $permissions[$permKey] == '1' ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} me-1"></i>
                                                    {{ $permLabel }}
                                                </span>
                                            @endforeach
                                        @else
                                            <span class="sub-permission-view">
                                                <i class="bi {{ isset($permissions[$key]) && $permissions[$key] == '1' ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} me-1"></i>
                                                Full Access
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Action Buttons Footer -->
  
   
</div>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>

@endsection
