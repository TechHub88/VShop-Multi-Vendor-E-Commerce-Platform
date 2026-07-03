@extends('layouts.vertical', ['title' => 'Coupon Engine'])

@section('content')
<style>
    /* Premium Coupon Engine Styling */
    .coupon-engine-container {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fc;
        padding: 24px;
        min-height: 100vh;
    }
    
    .ce-header {
        margin-bottom: 24px;
    }
    .ce-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a1d21;
        margin-bottom: 4px;
    }
    .ce-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 0;
    }
    .btn-create-coupon {
        background-color: #5b45f4;
        color: white;
        font-weight: 600;
        padding: 10px 20px;
        border-radius: 8px;
        border: none;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(91, 69, 244, 0.2);
    }
    .btn-create-coupon:hover {
        background-color: #4a36d9;
        transform: translateY(-1px);
        color: white;
    }

    /* Stats Cards */
    .ce-stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
    }
    .stat-label {
        font-size: 14px;
        color: #000;
        font-weight: 600;
        margin-bottom: 8px;
    }
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #0f172a;
    }

    /* Tabs */
    .ce-tabs {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 20px;
        display: flex;
        gap: 32px;
    }
    .ce-tab {
        padding: 12px 0;
        color: #64748b;
        font-weight: 500;
        font-size: 14px;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    .ce-tab.active {
        color: #5b45f4;
        border-bottom-color: #5b45f4;
        font-weight: 600;
    }
    .ce-tab:hover {
        color: #5b45f4;
    }

    /* Filter Row */
    .ce-filters {
        display: flex;
        gap: 16px;
        margin-bottom: 24px;
        align-items: flex-end;
    }
    .filter-group {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .filter-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
    }
    .ce-select {
        padding: 8px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        color: #0f172a;
        background: white;
        min-width: 140px;
        outline: none;
    }
    .ce-select:focus {
        border-color: #5b45f4;
        box-shadow: 0 0 0 2px rgba(91, 69, 244, 0.1);
    }
    .btn-filter {
        background-color: #5b45f4;
        color: white;
        border: none;
        padding: 8px 24px;
        border-radius: 8px;
        font-weight: 600;
        height: 38px;
    }

    /* Data Table */
    .ce-table-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        padding: 24px;
        margin-bottom: 32px;
    }
    .ce-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }
    .ce-table th {
        font-size: 13px;
        color: #000;
        font-weight: 600;
        padding: 12px 16px;
        border-bottom: 1px solid #e2e8f0;
        text-align: left;
    }
    .ce-table td {
        font-size: 14px;
        color: #334155;
        padding: 16px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }
    .ce-table tbody tr:hover {
        background-color: #f8fafc;
    }
    .code-link {
        color: #5b45f4;
        font-weight: 600;
        text-decoration: none;
    }
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-active {
        background-color: #ecfdf5;
        color: #10b981;
        border: 1px solid #a7f3d0;
    }
    .status-expired {
        background-color: #fef2f2;
        color: #ef4444;
        border: 1px solid #fecaca;
    }
    .status-scheduled {
        background-color: #fffbeb;
        color: #f59e0b;
        border: 1px solid #fde68a;
    }
    
    .action-icons {
        display: flex;
        gap: 12px;
        color: #94a3b8;
    }
    .action-icons i {
        cursor: pointer;
        transition: color 0.2s;
        font-size: 16px;
    }
    .action-icons i:hover {
        color: #5b45f4;
    }

    /* Form Section */
    .create-section {
        background: white;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        padding: 32px;
    }
    .create-title {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 24px;
    }
    .section-label {
        font-size: 15px;
        font-weight: 600;
        color: #000;
        margin-bottom: 16px;
    }
    
    .form-label {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
        margin-bottom: 6px;
    }
    .form-label span.req { color: #ef4444; }
    
    .ce-input {
        width: 100%;
        padding: 10px 14px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        color: #0f172a;
        transition: all 0.2s;
    }
    .ce-input:focus {
        border-color: #5b45f4;
        box-shadow: 0 0 0 2px rgba(91, 69, 244, 0.1);
        outline: none;
    }
    
    .radio-group {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .radio-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #334155;
    }
    .radio-item input[type="radio"] {
        accent-color: #5b45f4;
        width: 16px;
        height: 16px;
    }

    .checkbox-group {
        display: flex;
        gap: 24px;
    }
    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
        color: #334155;
    }
    .checkbox-item input[type="checkbox"] {
        accent-color: #5b45f4;
        width: 16px;
        height: 16px;
        border-radius: 4px;
    }

    /* Toggle Switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    .toggle-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 24px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider {
        background-color: #5b45f4;
    }
    input:checked + .slider:before {
        transform: translateX(20px);
    }
    
    .toggle-wrapper {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .toggle-label {
        font-size: 14px;
        font-weight: 500;
        color: #334155;
    }
</style>

<div class="coupon-engine-container">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" style="background:#ecfdf5; border-color:#a7f3d0; color:#065f46;">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background:#fef2f2; border-color:#fecaca; color:#991b1b;">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header -->
    <div class="ce-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="ce-title">Coupon Engine</h2>
            <p class="ce-subtitle">Create and manage discount coupons</p>
        </div>
        <div>
            <button class="btn btn-create-coupon" data-bs-toggle="modal" data-bs-target="#createCouponModal">
                + Create Coupon
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row ce-stats-row">
        <div class="col-md-3">
            <div class="ce-stat-card">
                <div class="stat-label">Active Coupons</div>
                <div class="stat-value">{{ $stats['active'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ce-stat-card">
                <div class="stat-label">Used Today</div>
                <div class="stat-value">{{ $stats['used_today'] ?? 0 }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ce-stat-card">
                <div class="stat-label">Revenue Generated</div>
                <div class="stat-value">₹{{ number_format($stats['revenue_generated'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="ce-stat-card">
                <div class="stat-label">Expired Coupons</div>
                <div class="stat-value">{{ $stats['expired'] ?? 0 }}</div>
            </div>
        </div>
    </div>

    <!-- Main List Container -->
    <div class="ce-table-container">
        <!-- Tabs -->
        <div class="ce-tabs">
            <a href="{{ route('admin.coupons.index', ['tab' => 'all']) }}" class="ce-tab {{ $tab == 'all' ? 'active' : '' }}">All Coupons</a>
            <a href="{{ route('admin.coupons.index', ['tab' => 'active']) }}" class="ce-tab {{ $tab == 'active' ? 'active' : '' }}">Active ({{ $stats['active'] ?? 0 }})</a>
            <a href="{{ route('admin.coupons.index', ['tab' => 'scheduled']) }}" class="ce-tab {{ $tab == 'scheduled' ? 'active' : '' }}">Scheduled ({{ $stats['scheduled'] ?? 0 }})</a>
            <a href="{{ route('admin.coupons.index', ['tab' => 'expired']) }}" class="ce-tab {{ $tab == 'expired' ? 'active' : '' }}">Expired ({{ $stats['expired'] ?? 0 }})</a>
        </div>

        <!-- Filters -->
        <form action="{{ route('admin.coupons.index') }}" method="GET" class="ce-filters">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <div class="filter-group">
                <span class="filter-label">Type</span>
                <select name="type_filter" class="ce-select">
                    <option value="All">All</option>
                    <option value="percentage" {{ request('type_filter') == 'percentage' ? 'selected' : '' }}>Percentage</option>
                    <option value="fixed" {{ request('type_filter') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                </select>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">Discount Type</span>
                <select class="ce-select">
                    <option>All</option>
                </select>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">Category</span>
                <select class="ce-select">
                    <option>All</option>
                </select>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">Seller</span>
                <select class="ce-select">
                    <option>All</option>
                </select>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">Status</span>
                <select name="status_filter" class="ce-select">
                    <option value="All">All</option>
                    <option value="active" {{ request('status_filter') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status_filter') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            
            <div class="filter-group flex-grow-1">
                <span class="filter-label">&nbsp;</span>
                <div class="position-relative">
                    <input type="text" name="search" class="ce-input" placeholder="Search coupon code or name" value="{{ request('search') }}" style="padding-left: 36px;">
                    <iconify-icon icon="solar:magnifer-linear" style="position:absolute; left:12px; top:12px; color:#94a3b8; font-size:18px;"></iconify-icon>
                </div>
            </div>
            
            <div class="filter-group">
                <span class="filter-label">&nbsp;</span>
                <button type="submit" class="btn-filter">Filter</button>
            </div>
        </form>

        <!-- Data Table -->
        <div class="table-responsive">
            <table class="ce-table">
                <thead>
                    <tr>
                        <th>Coupon Code</th>
                        <th>Type</th>
                        <th>Discount</th>
                        <th>Min. Order Value</th>
                        <th>Validity</th>
                        <th>Usage</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($coupons as $coupon)
                    <tr>
                        <td>
                            <a href="#" class="code-link">{{ $coupon->discount_code }}</a>
                        </td>
                        <td>{{ ucfirst($coupon->discount_type) }} {{ $coupon->discount_type == 'fixed' ? 'Amount' : '' }}</td>
                        <td>
                            @if($coupon->discount_type == 'percentage')
                                {{ $coupon->coupon_amount }}% OFF
                            @else
                                ₹{{ rtrim(rtrim(number_format($coupon->coupon_amount, 2), '0'), '.') }} OFF
                            @endif
                        </td>
                        <td>₹{{ rtrim(rtrim(number_format($coupon->minimum_spend, 2), '0'), '.') }}</td>
                        <td>
                            @if($coupon->expiry_date)
                                {{ $coupon->expiry_date->format('d M Y') }}
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ number_format($coupon->used_count) }}</td>
                        <td>
                            @php
                                $statusClass = 'status-active';
                                $statusText = 'Active';
                                
                                if ($coupon->status == 'inactive') {
                                    if ($coupon->start_date && $coupon->start_date > now()) {
                                        $statusClass = 'status-scheduled';
                                        $statusText = 'Scheduled';
                                    } else {
                                        $statusClass = 'status-expired';
                                        $statusText = 'Inactive';
                                    }
                                } elseif ($coupon->expiry_date && $coupon->expiry_date < now()) {
                                    $statusClass = 'status-expired';
                                    $statusText = 'Expired';
                                }
                            @endphp
                            <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                        </td>
                        <td>
                            <div class="action-icons">
                                <button type="button" class="btn-view-coupon" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#viewCouponModal"
                                    data-code="{{ $coupon->discount_code }}"
                                    data-name="{{ $coupon->name ?? 'N/A' }}"
                                    data-type="{{ ucfirst($coupon->discount_type) }}"
                                    data-amount="{{ $coupon->discount_type == 'percentage' ? $coupon->coupon_amount . '% OFF' : '₹' . number_format($coupon->coupon_amount, 2) . ' OFF' }}"
                                    data-min-spend="₹{{ number_format($coupon->minimum_spend, 2) }}"
                                    data-max-discount="{{ $coupon->max_discount_amount ? '₹' . number_format($coupon->max_discount_amount, 2) : 'No Limit' }}"
                                    data-start="{{ $coupon->start_date ? $coupon->start_date->format('d M Y') : 'N/A' }}"
                                    data-expiry="{{ $coupon->expiry_date ? $coupon->expiry_date->format('d M Y') : 'N/A' }}"
                                    data-limit="{{ $coupon->usage_limit ?? 'Unlimited' }}"
                                    data-user-limit="{{ $coupon->per_user_limit ?? 'Unlimited' }}"
                                    data-used="{{ $coupon->used_count }}"
                                    data-status="{{ ucfirst($coupon->status) }}"
                                    data-public="{{ $coupon->show_in_public ? 'Yes' : 'No' }}"
                                    data-stackable="{{ $coupon->stackable ? 'Yes' : 'No' }}"
                                    data-description="{{ $coupon->description ?? 'No description provided' }}"
                                    style="background:none; border:none; padding:0; color:#5b45f4; margin-right:8px; cursor:pointer;">
                                    <iconify-icon icon="solar:eye-bold-duotone"></iconify-icon>
                                </button>
                                <a href="{{ route('admin.coupons.edit', $coupon->id) }}" class="text-secondary" style="margin-right:8px;">
                                    <iconify-icon icon="solar:pen-bold-duotone"></iconify-icon>
                                </a>
                                <form action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this coupon?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background:none;border:none;padding:0;color:inherit;">
                                        <iconify-icon icon="solar:trash-bin-trash-bold-duotone" style="color: #ef4444;"></iconify-icon>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">No coupons found matching your criteria.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <div class="mt-4 d-flex justify-content-between align-items-center">
            <div class="text-muted" style="font-size:13px;">
                Showing {{ $coupons->firstItem() ?? 0 }} to {{ $coupons->lastItem() ?? 0 }} of {{ $coupons->total() }} entries
            </div>
            <div>
                {{ $coupons->appends(request()->query())->links('pagination::bootstrap-4') }}
            </div>
        </div>
    </div>

</div>

<!-- View Coupon Details Modal -->
<div class="modal fade" id="viewCouponModal" tabindex="-1" aria-labelledby="viewCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border:none; border-radius:12px;">
            <div class="modal-header" style="background:#5b45f4; color:white; border-radius:12px 12px 0 0; padding:16px 24px;">
                <h5 class="modal-title" id="viewCouponModalLabel" style="font-weight:700;">Coupon Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <div class="d-flex align-items-center mb-4">
                    <div style="background:#eef2ff; border:2px dashed #5b45f4; border-radius:8px; padding:8px 16px; font-weight:700; color:#5b45f4; font-size:18px; letter-spacing:1px;" id="view-coupon-code">
                        COUPONCODE
                    </div>
                    <div class="ms-3">
                        <h6 style="margin:0; font-weight:700; color:#0f172a;" id="view-coupon-name">Coupon Name</h6>
                        <span class="badge" id="view-coupon-status" style="font-size:11px; padding:4px 8px; border-radius:12px;">Active</span>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Discount Type</label>
                        <div style="font-weight:600; color:#334155;" id="view-coupon-type">Percentage</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Discount Value</label>
                        <div style="font-weight:600; color:#10b981;" id="view-coupon-amount">10% OFF</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Min. Order Value</label>
                        <div style="font-weight:600; color:#334155;" id="view-coupon-min-spend">₹0</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Max. Discount Limit</label>
                        <div style="font-weight:600; color:#334155;" id="view-coupon-max-discount">No Limit</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Start Date</label>
                        <div style="font-weight:600; color:#334155;" id="view-coupon-start">N/A</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Expiry Date</label>
                        <div style="font-weight:600; color:#334155;" id="view-coupon-expiry">N/A</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Total Usage Limit</label>
                        <div style="font-weight:600; color:#334155;" id="view-coupon-limit">Unlimited</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Per User Limit</label>
                        <div style="font-weight:600; color:#334155;" id="view-coupon-user-limit">Unlimited</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Used Count</label>
                        <div style="font-weight:600; color:#334155;" id="view-coupon-used">0</div>
                    </div>
                    <div class="col-6">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Public List / Stackable</label>
                        <div style="font-weight:600; color:#334155;">
                            Public: <span id="view-coupon-public">Yes</span> / Stack: <span id="view-coupon-stackable">No</span>
                        </div>
                    </div>
                    <div class="col-12">
                        <label class="text-muted" style="font-size:12px; font-weight:600; display:block; margin-bottom:4px;">Description</label>
                        <div style="font-size:13px; color:#475569; padding:8px 12px; background:#f8fafc; border-radius:6px; min-height:40px;" id="view-coupon-description">
                            No description provided.
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:16px 24px;">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius:8px;">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Create Coupon Modal -->
<div class="modal fade" id="createCouponModal" tabindex="-1" aria-labelledby="createCouponModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content" style="border:none; border-radius:12px;">
            <div class="modal-header" style="background:#5b45f4; color:white; border-radius:12px 12px 0 0; padding:16px 24px;">
                <h5 class="modal-title" id="createCouponModalLabel" style="font-weight:700;">Create New Coupon</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding:24px;">
                <form action="{{ route('admin.coupons.store') }}" method="POST" id="createCouponForm">
                    @csrf
                    <div class="row">
                        <!-- Left Column -->
                        <div class="col-md-8 pe-md-4">
                            <h6 style="font-weight:700; color:#0f172a; margin-bottom:16px;">Basic Information</h6>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Coupon Code <span class="req">*</span></label>
                                    <input type="text" name="discount_code" class="ce-input" placeholder="Enter coupon code" required value="{{ old('discount_code') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Coupon Name <span class="req">*</span></label>
                                    <input type="text" name="name" class="ce-input" placeholder="Enter coupon name" required value="{{ old('name') }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Coupon Type <span class="req">*</span></label>
                                    <select name="type" class="ce-select w-100">
                                        <option value="all">All</option>
                                        <option value="product">Vshop</option>
                                        <option value="travel">Vtravel</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Discount Type <span class="req">*</span></label>
                                    <select name="discount_type" class="ce-select w-100" required>
                                        <option value="percentage">Percentage</option>
                                        <option value="fixed">Fixed Amount</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label class="form-label">Discount Value <span class="req">*</span></label>
                                    <input type="number" step="0.01" name="coupon_amount" class="ce-input" placeholder="Enter value" required value="{{ old('coupon_amount') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Minimum Order Value <span class="req">*</span></label>
                                    <input type="number" step="0.01" name="minimum_spend" class="ce-input" placeholder="Enter minimum order value" required value="{{ old('minimum_spend') }}">
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <label class="form-label">Max Discount</label>
                                    <input type="number" step="0.01" name="max_discount_amount" class="ce-input" placeholder="Max discount" value="{{ old('max_discount_amount') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Total Usage Limit <span class="req">*</span></label>
                                    <input type="number" name="usage_limit" class="ce-input" placeholder="Total uses (e.g. 10)" required value="{{ old('usage_limit') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Per User Limit</label>
                                    <input type="number" name="per_user_limit" class="ce-input" placeholder="Per user limit" value="{{ old('per_user_limit') }}">
                                </div>
                            </div>
                            <hr style="border-color:#f1f5f9;">
                            <h6 style="font-weight:700; color:#0f172a; margin-bottom:16px;">Additional Settings</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="form-label">Apply On</label>
                                    <div class="checkbox-group mt-2">
                                        <label class="checkbox-item"><input type="checkbox" name="apply_vshop" checked> V-Shop</label>
                                        <label class="checkbox-item"><input type="checkbox" name="apply_vtravel"> V-Travel</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Stackable</label>
                                    <div class="toggle-wrapper mt-2">
                                        <label class="toggle-switch"><input type="checkbox" name="stackable"><span class="slider"></span></label>
                                        <span class="toggle-label">No</span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Public List</label>
                                    <div class="toggle-wrapper mt-2">
                                        <label class="toggle-switch"><input type="checkbox" name="show_in_public" checked><span class="slider"></span></label>
                                        <span class="toggle-label" style="color:#5b45f4;">Yes</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- Right Column -->
                        <div class="col-md-4 ps-md-4 border-start">
                            <h6 style="font-weight:700; color:#0f172a; margin-bottom:16px;">Validity & Availability</h6>
                            <div class="mb-3">
                                <label class="form-label">Start Date <span class="req">*</span></label>
                                <input type="date" name="start_date" class="ce-input" required value="{{ old('start_date') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">End Date <span class="req">*</span></label>
                                <input type="date" name="expiry_date" class="ce-input" required value="{{ old('expiry_date') }}">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Applicable On <span class="req">*</span></label>
                                <div class="radio-group mt-2">
                                    <label class="radio-item"><input type="radio" name="applicable_on" value="all" checked> All Products</label>
                                    <label class="radio-item"><input type="radio" name="applicable_on" value="categories"> Specific Categories</label>
                                    <label class="radio-item"><input type="radio" name="applicable_on" value="products"> Specific Products</label>
                                    <label class="radio-item"><input type="radio" name="applicable_on" value="sellers"> Specific Sellers</label>
                                </div>
                            </div>
                            <hr style="border-color:#f1f5f9;">
                            <div class="mb-3">
                                <label class="form-label">Status</label>
                                <div class="toggle-wrapper mt-2">
                                    <label class="toggle-switch">
                                        <input type="checkbox" name="status_toggle" checked onchange="document.getElementById('hiddenStatus').value = this.checked ? 'active' : 'inactive'">
                                        <span class="slider"></span>
                                    </label>
                                    <span class="toggle-label" style="color:#5b45f4;">Active</span>
                                </div>
                                <input type="hidden" name="status" id="hiddenStatus" value="active">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer" style="border-top:1px solid #f1f5f9; padding:16px 24px;">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius:8px;">Cancel</button>
                <button type="submit" form="createCouponForm" class="btn btn-create-coupon">Create Coupon</button>
            </div>
        </div>
    </div>
</div>

<!-- Iconify -->
<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
@endsection

@section('script-bottom')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle toggle labels dynamically (Yes/No, Active/Inactive)
    document.querySelectorAll('.toggle-switch input[type="checkbox"]').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            var label = this.closest('.toggle-wrapper').querySelector('.toggle-label');
            if(this.name === 'stackable' || this.name === 'show_in_public') {
                if(this.checked) {
                    label.textContent = 'Yes';
                    label.style.color = '#5b45f4';
                } else {
                    label.textContent = 'No';
                    label.style.color = '#334155';
                }
            } else if (this.name === 'status_toggle') {
                if(this.checked) {
                    label.textContent = 'Active';
                    label.style.color = '#5b45f4';
                } else {
                    label.textContent = 'Inactive';
                    label.style.color = '#334155';
                }
            }
        });
    });

    // View Coupon Detail click handler
    document.querySelectorAll('.btn-view-coupon').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('view-coupon-code').textContent = this.getAttribute('data-code');
            document.getElementById('view-coupon-name').textContent = this.getAttribute('data-name');
            document.getElementById('view-coupon-type').textContent = this.getAttribute('data-type');
            document.getElementById('view-coupon-amount').textContent = this.getAttribute('data-amount');
            document.getElementById('view-coupon-min-spend').textContent = this.getAttribute('data-min-spend');
            document.getElementById('view-coupon-max-discount').textContent = this.getAttribute('data-max-discount');
            document.getElementById('view-coupon-start').textContent = this.getAttribute('data-start');
            document.getElementById('view-coupon-expiry').textContent = this.getAttribute('data-expiry');
            document.getElementById('view-coupon-limit').textContent = this.getAttribute('data-limit');
            document.getElementById('view-coupon-user-limit').textContent = this.getAttribute('data-user-limit');
            document.getElementById('view-coupon-used').textContent = this.getAttribute('data-used');
            document.getElementById('view-coupon-public').textContent = this.getAttribute('data-public');
            document.getElementById('view-coupon-stackable').textContent = this.getAttribute('data-stackable');
            document.getElementById('view-coupon-description').textContent = this.getAttribute('data-description');

            // Set status badge style
            var status = this.getAttribute('data-status').toLowerCase();
            var statusBadge = document.getElementById('view-coupon-status');
            statusBadge.textContent = this.getAttribute('data-status');
            if (status === 'active') {
                statusBadge.style.backgroundColor = '#ecfdf5';
                statusBadge.style.color = '#10b981';
                statusBadge.style.border = '1px solid #a7f3d0';
            } else {
                statusBadge.style.backgroundColor = '#fef2f2';
                statusBadge.style.color = '#ef4444';
                statusBadge.style.border = '1px solid #fecaca';
            }
        });
    });

    // Manual Bootstrap Modal trigger fallback if data attributes fail
    document.querySelectorAll('[data-bs-toggle="modal"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var targetId = this.getAttribute('data-bs-target');
            var modalEl = document.querySelector(targetId);
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                    modal.show();
                } else {
                    modalEl.classList.add('show');
                    modalEl.style.display = 'block';
                    document.body.classList.add('modal-open');
                    var backdrop = document.createElement('div');
                    backdrop.className = 'modal-backdrop fade show';
                    document.body.appendChild(backdrop);
                }
            }
        });
    });

    // Manual Modal dismiss fallback
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            var modalEl = this.closest('.modal');
            if (modalEl) {
                if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                    var modal = bootstrap.Modal.getInstance(modalEl);
                    if (modal) {
                        modal.hide();
                    } else {
                        new bootstrap.Modal(modalEl).hide();
                    }
                } else {
                    modalEl.classList.remove('show');
                    modalEl.style.display = 'none';
                    document.body.classList.remove('modal-open');
                    var backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) {
                        backdrop.remove();
                    }
                }
            }
        });
    });
});
</script>
@endsection