@extends('layouts.vertical', ['title' => $pageTitle ?? 'Product Approvals'])

@section('content')

@php
    $panel = session('admin_panel', 'product');
    
    // Get selected panels from session
    $selectedPanels = session('selected_panels', []);
    $isVshop = in_array('product', $selectedPanels);
    $isVtravel = in_array('travel', $selectedPanels);
    $isVcampus = in_array('vcampus', $selectedPanels);
    
    // Define title based on passed variables or calculate
    if (!isset($pageTitle)) {
        if ($isVshop && $isVtravel) {
            $pageTitle = 'All Items Approvals (Products, Services, Tours & Professionals)';
        } elseif ($isVtravel || $panel == 'travel') {
            $pageTitle = 'Travel & Service Approvals';
        } elseif ($isVcampus || $panel == 'vcampus') {
            $pageTitle = 'Course & Campus Approvals';
        } else {
            $pageTitle = 'Product & Professional Approvals';
        }
    }
    
    if (!isset($pageSubtitle)) {
        if ($isVshop && $isVtravel) {
            $pageSubtitle = '<i class="fa-solid fa-store"></i> Vshop + <i class="fa-solid fa-plane"></i> Vtravel';
        } elseif ($isVtravel || $panel == 'travel') {
            $pageSubtitle = '<i class="fa-solid fa-plane"></i> Managing: Tour Guidance, Information & Advisory, Professional Services';
        } elseif ($isVcampus || $panel == 'vcampus') {
            $pageSubtitle = '<i class="fa-solid fa-graduation-cap"></i> Managing: Courses & Campus Items';
        } else {
            $pageSubtitle = '<i class="fa-solid fa-box"></i> Managing: Products & Professional Services';
        }
    }
    
    // Define valid types based on selected panels
    $validTypes = [];
    if ($isVshop || $panel == 'product') {
        $validTypes = array_merge($validTypes, ['product', 'vshop-product', 'vshop-professional', 'service']);
    }
    if ($isVtravel || $panel == 'travel') {
        $validTypes = array_merge($validTypes, ['vtravel-professional', 'tour', 'vtravel-information_advisory', 'service']);
    }
    if ($isVcampus || $panel == 'vcampus') {
        $validTypes = array_merge($validTypes, ['campus']);
    }
    
    // Type display mapping
    $typeDisplayMap = [
        'product' => 'Product',
        'vshop-product' => 'Vshop Product',
        'vshop-professional' => 'Professional (Vshop)',
        'vtravel-professional' => 'Professional (Vtravel)',
        'tour' => 'Tour Guidance',
        'vtravel-information_advisory' => 'Information & Advisory',
        'service' => 'Service / Others',
        'campus' => 'Course / Campus',
        'others' => 'Others'
    ];
    
    // Type badge colors
    $typeBadgeColors = [
        'product' => 'primary',
        'vshop-product' => 'primary',
        'vshop-professional' => 'info',
        'vtravel-professional' => 'info',
        'tour' => 'warning',
        'vtravel-information_advisory' => 'secondary',
        'service' => 'success',
        'campus' => 'dark',
        'others' => 'secondary'
    ];
    
    // Get categories for bulk category change
    $categories = App\Models\MarketplaceCategory::where('type', 'product')->get();
@endphp

<div class="container-fluid">

    <!-- HEADER with Single Bulk Action Dropdown Button -->
    <div class="pt-3 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h4 class="fw-semibold">{{ $pageTitle }}</h4>
            <p class="text-muted mb-0">
                {!! $pageSubtitle !!}
            </p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <!-- Single Bulk Actions Dropdown -->
            <div class="dropdown" id="bulkDropdownWrapper" style="display: none;">
                <button class="btn btn-primary dropdown-toggle" type="button" id="bulkActionsDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #0d6efd; border: none;">
                    <i class="fa-solid fa-layer-group"></i> Bulk Actions (<span id="bulkSelectedCount">0</span>)
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="bulkActionsDropdownBtn">
                    <li><a class="dropdown-item" href="#" id="bulkApproveItem"><i class="fa-solid fa-check-circle text-success me-2"></i> Bulk Approve</a></li>
                    <li><a class="dropdown-item" href="#" id="bulkRejectItem"><i class="fa-solid fa-times-circle text-danger me-2"></i> Bulk Reject</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" id="bulkEnableItem"><i class="fa-solid fa-play-circle text-primary me-2"></i> Bulk Enable</a></li>
                    <li><a class="dropdown-item" href="#" id="bulkDisableItem"><i class="fa-solid fa-ban text-warning me-2"></i> Bulk Disable</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" id="bulkCategoryItem"><i class="fa-solid fa-tags text-info me-2"></i> Change Category</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs mb-3" id="approvalTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                <i class="fa-solid fa-box"></i> Products / Services
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="dropshipping-tab" data-bs-toggle="tab" data-bs-target="#dropshipping" type="button" role="tab">
                <i class="fa-solid fa-truck-fast"></i> Dropshipping Management
            </button>
        </li>
    </ul>

    <div class="tab-content">
        
        <!-- TAB 1: PRODUCTS / SERVICES -->
        <div class="tab-pane fade show active" id="products" role="tabpanel">
            <!-- KPI CARDS ROW -->
            <div class="row g-3 mb-4">
                <div class="col-md">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small mb-1 fw-medium">{{ session('admin_panel') == 'travel' ? 'Total Services' : 'Total Products' }}</span>
                                    <h3 class="fw-bold text-dark m-0 fs-24">{{ number_format($totalProducts) }}</h3>
                                </div>
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #e0e7ff; color: #4f46e5;">
                                    <i class="fa-solid fa-boxes-stacked fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small mb-1 fw-medium">Verified</span>
                                    <h3 class="fw-bold text-success m-0 fs-24">{{ number_format($verifiedProducts) }}</h3>
                                </div>
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #d1fae5; color: #059669;">
                                    <i class="fa-solid fa-circle-check fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small mb-1 fw-medium">Not Verified</span>
                                    <h3 class="fw-bold text-secondary m-0 fs-24">{{ number_format($notVerifiedProducts) }}</h3>
                                </div>
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #f1f5f9; color: #64748b;">
                                    <i class="fa-solid fa-circle-minus fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small mb-1 fw-medium">In Progress</span>
                                    <h3 class="fw-bold text-warning m-0 fs-24">{{ number_format($inProgressProducts) }}</h3>
                                </div>
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #fef3c7; color: #d97706;">
                                    <i class="fa-solid fa-spinner fa-spin fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <span class="text-muted d-block small mb-1 fw-medium">Rejected</span>
                                    <h3 class="fw-bold text-danger m-0 fs-24">{{ number_format($rejectedProducts) }}</h3>
                                </div>
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #ffe4e6; color: #e11d48;">
                                    <i class="fa-solid fa-circle-xmark fs-20"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Bar -->
            <div class="card mb-3">
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-md-3">
                            <label class="form-label">Type</label>
                            <select id="typeFilter" class="form-select">
                                <option value="">All Types</option>
                                @foreach($validTypes as $type)
                                    <option value="{{ $type }}">{{ $typeDisplayMap[$type] ?? ucfirst(str_replace('_', ' ', $type)) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Status</label>
                            <select id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="pending">Pending</option>
                                <option value="in-progress">In Progress</option>
                                <option value="not-verified">Not Verified</option>
                                <option value="approved">Approved / Verified</option>
                                <option value="rejected">Rejected</option>
                                <option value="active">Active</option>
                                <option value="deactivated">Deactivated</option>
                            </select>
                        </div>
                        <!-- 🔥 NEW: Seller Filter Dropdown -->
                        <div class="col-md-3">
                            <label class="form-label">All Seller</label>
                            <select id="sellerFilter" class="form-select">
                                <option value="">All Sellers</option>
                                @foreach($sellers as $seller)
                                    <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                        {{ $seller->name ?? $seller->email }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Search</label>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search by name, code or SKU...">
                        </div>
                        <div class="col-md-12 d-flex justify-content-end mt-2">
                            <button id="resetFilters" class="btn btn-primary" style="background-color: #0d6efd; color: white; border: none;">
                                <i class="fa-solid fa-times"></i> Reset Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Data Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle" id="approvalTable">
                            <thead>
                                <tr>
                                    <th width="40"><input type="checkbox" id="selectAll"></th>
                                    <th>#</th>
                                    <th>Item Name</th>
                                    <th>Category</th>
                                    <th>Type</th>
                                    <!-- 🔥 NEW: Seller Column -->
                                    <th>Seller</th>
                                    <th>Price</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th width="250">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $product)
                                <tr id="row-{{ $product->id }}" 
                                    data-id="{{ $product->id }}"
                                    data-type="{{ $product->type }}" 
                                    data-status="{{ $product->status }}" 
                                    data-category-id="{{ $product->category_id ?? 0 }}"
                                    data-seller-id="{{ $product->created_by_id }}"
                                    data-name="{{ strtolower($product->name ?? '') }} {{ strtolower($product->product_code ?? '') }} {{ strtolower($product->sku ?? '') }}">
                                    <td><input type="checkbox" class="productCheckbox" value="{{ $product->id }}"></td>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>
                                        <strong>{{ $product->name ?? '-' }}</strong><br>
                                        <small class="text-muted">
                                            @if($product->product_code) Code: {{ $product->product_code }}
                                            @elseif($product->sku) SKU: {{ $product->sku }}
                                            @else ID: {{ $product->id }}
                                            @endif
                                        </small>
                                    </td>
                                    <td>
                                        <span class="category-name">{{ $product->category->name ?? '-' }}</span>
                                        @if($product->subcategory)<br><small class="text-muted">→ {{ $product->subcategory->name ?? '-' }}</small>@endif
                                    </td>
                                    <td>
                                        @php $displayType = $typeDisplayMap[$product->type] ?? ucfirst(str_replace('_', ' ', $product->type)); $badgeColor = $typeBadgeColors[$product->type] ?? 'secondary'; @endphp
                                        <span class="badge bg-{{ $badgeColor }}">{{ $displayType }}</span>
                                    </td>
                                    <!-- 🔥 NEW: Seller Column Data -->
                                    <td>
                                        @if($product->seller)
                                            {{ $product->seller->name ?? $product->seller->email }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($product->price)<div class="fw-bold">₹ {{ number_format($product->price ?? 0, 2) }}</div>@else <span class="text-muted">-</span> @endif
                                    </td>
                                    <td>
                                        @php 
                                            $statusColors = ['pending'=>'warning','approved'=>'success','rejected'=>'danger','in-progress'=>'info','not-verified'=>'secondary','verified'=>'primary','active'=>'success','deactivated'=>'primary']; 
                                            $statusColor = $statusColors[$product->status] ?? 'secondary'; 
                                            $statusDisplay = ucfirst(str_replace('-', ' ', $product->status)); 
                                            if($statusDisplay == 'Approved') $statusDisplay = 'Verified'; 
                                        @endphp
                                        <span class="badge bg-{{ $statusColor }} status-badge">{{ $statusDisplay }}</span>
                                        
                                        @if($product->status == 'deactivated' && $product->force_disabled_reason)
                                            <br>
                                            <small class="text-danger d-block mt-1" style="font-size: 10px;">
                                                <i class="fa-solid fa-circle-exclamation"></i> {{ Str::limit($product->force_disabled_reason, 50) }}
                                            </small>
                                        @endif
                                        @if($product->status == 'rejected' && $product->admin_notes)
                                            <br>
                                            <small class="text-danger d-block mt-1" style="font-size: 10px;">
                                                <i class="fa-solid fa-circle-exclamation"></i> {{ Str::limit($product->admin_notes, 50) }}
                                            </small>
                                        @endif
                                    </td>
                                    <td>{{ $product->created_at ? $product->created_at->format('d M, Y') : '-' }}</td>
                                    <td>
                                        <div class="d-flex gap-1 flex-wrap">
                                            <a href="{{ route('admin.approval.verify', $product->id) }}" class="btn btn-info btn-sm icon-action-btn" title="Verify"><i class="fa-solid fa-clipboard-check"></i></a>
                                            @if(in_array($product->status, ['pending', 'in-progress', 'not-verified']))
                                                <button class="btn btn-success btn-sm icon-action-btn approveSingleBtn" data-id="{{ $product->id }}"><i class="fa-solid fa-check"></i></button>
                                                <button class="btn btn-danger btn-sm icon-action-btn rejectSingleBtn" data-id="{{ $product->id }}"><i class="fa-solid fa-xmark"></i></button>
                                            @endif
                                            @if($product->status == 'approved' || $product->status == 'verified' || $product->status == 'active')
                                                <button class="btn btn-warning btn-sm icon-action-btn disableSingleBtn" data-id="{{ $product->id }}"><i class="fa-solid fa-ban"></i></button>
                                            @endif
                                            @if($product->status == 'deactivated')
                                                <button class="btn btn-success btn-sm icon-action-btn enableSingleBtn" data-id="{{ $product->id }}"><i class="fa-solid fa-power-off"></i></button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="10" class="text-center text-muted py-5"><i class="fa-solid fa-inbox fa-3x mb-3"></i><p>No items found for approval</p></td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-center">
                        {{ $items->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: DROPSHIPPING MANAGEMENT -->
        <div class="tab-pane fade" id="dropshipping" role="tabpanel">
            <!-- ... (keep your existing dropshipping content) ... -->
            <div class="row">
                <!-- Suppliers List -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="fw-bold mb-0"><i class="fa-solid fa-store text-primary me-2"></i> Supplier Management</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <input type="text" id="supplierSearch" class="form-control" placeholder="Search suppliers...">
                            </div>
                            <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                                <table class="table table-sm table-hover">
                                    <thead>
                                        <tr>
                                            <th>Supplier Name</th>
                                            <th>Status</th>
                                            <th>KYC</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="suppliersTableBody">
                                        @php
                                            $suppliers = App\Models\Seller::with(['products'])->orderBy('created_at', 'desc')->get();
                                        @endphp
                                        @forelse($suppliers as $supplier)
                                        <tr data-supplier-name="{{ strtolower($supplier->name ?? '') }}">
                                            <td>
                                                <strong>{{ $supplier->name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $supplier->email ?? '' }}</small>
                                            </td>
                                            <td>
                                                <span class="badge {{ ($supplier->supplier_approved ?? false) ? 'bg-success' : 'bg-warning' }}" id="supplierStatus-{{ $supplier->id }}">
                                                    {{ ($supplier->supplier_approved ?? false) ? 'Approved' : 'Pending' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="badge {{ ($supplier->kyc_status ?? 'pending') == 'verified' ? 'bg-success' : (($supplier->kyc_status ?? 'pending') == 'rejected' ? 'bg-danger' : 'bg-secondary') }}" id="supplierKyc-{{ $supplier->id }}">
                                                    {{ ucfirst($supplier->kyc_status ?? 'Pending') }}
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    @if(!($supplier->supplier_approved ?? false))
                                                        <button class="btn btn-success" onclick="approveSupplier({{ $supplier->id }})" title="Approve Supplier">
                                                            <i class="fa-solid fa-check"></i>
                                                        </button>
                                                        <button class="btn btn-danger" onclick="rejectSupplier({{ $supplier->id }})" title="Reject Supplier">
                                                            <i class="fa-solid fa-times"></i>
                                                        </button>
                                                    @endif
                                                    <button class="btn btn-primary" onclick="showKYCModal({{ $supplier->id }}, '{{ $supplier->kyc_status ?? 'pending' }}', '{{ addslashes($supplier->kyc_notes ?? '') }}')" title="Update KYC">
                                                        <i class="fa-solid fa-id-card"></i>
                                                    </button>
                                                    <button class="btn btn-info" onclick="viewSupplierDetails({{ $supplier->id }})" title="View Details">
                                                        <i class="fa-solid fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="4" class="text-center text-muted py-3">No suppliers found</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Product Sync Monitoring -->
                <div class="col-md-6">
                    <div class="card shadow-sm border-0 rounded-4 mb-3">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="fw-bold mb-0"><i class="fa-solid fa-link text-primary me-2"></i> Product Sync Monitoring</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label">Select Product to Sync</label>
                                <select id="syncProductSelect" class="form-select">
                                    <option value="">-- Select Product --</option>
                                    @foreach($items as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->product_code ?? 'ID: '.$product->id }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div id="syncProductDetails" style="display: none;">
                                <div class="border rounded-2 p-3 bg-light">
                                    <h6 class="fw-bold mb-2">Product Mapping Details</h6>
                                    <div class="info-row">
                                        <span class="info-label">Supplier Product ID:</span>
                                        <span class="info-value" id="displaySupplierProductId">-</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Supplier Cost:</span>
                                        <span class="info-value" id="displaySyncSupplierCost">-</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Selling Price:</span>
                                        <span class="info-value" id="displaySyncSellingPrice">-</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Margin:</span>
                                        <span class="info-value" id="displaySyncMargin">-</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">Auto Order:</span>
                                        <span class="info-value" id="displaySyncAutoOrder">-</span>
                                    </div>
                                    <div class="mt-2">
                                        <button class="btn btn-sm btn-primary" onclick="openSyncModal()">
                                            <i class="fa-solid fa-sync"></i> Update Mapping
                                        </button>
                                        <button class="btn btn-sm btn-outline-info" onclick="validatePriceSyncForProduct()">
                                            <i class="fa-solid fa-chart-simple"></i> Validate Price
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Supplier Performance Overview -->
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-white border-0 pt-3">
                            <h5 class="fw-bold mb-0"><i class="fa-solid fa-chart-line text-primary me-2"></i> Supplier Performance Overview</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-4">
                                    <div class="p-2 border rounded">
                                        <small class="text-muted">On-Time Delivery</small>
                                        <h6 class="fw-bold text-success mb-0" id="overallOnTime">95%</h6>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded">
                                        <small class="text-muted">Order Fulfillment</small>
                                        <h6 class="fw-bold text-primary mb-0" id="overallFulfillment">98%</h6>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2 border rounded">
                                        <small class="text-muted">Total Suppliers</small>
                                        <h6 class="fw-bold text-info mb-0" id="totalSuppliers">{{ $suppliers->count() }}</h6>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- KYC Modal -->
<div class="modal fade" id="kycModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-id-card"></i> Update KYC Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="kycSupplierId">
                <div class="mb-3">
                    <label class="form-label">KYC Status</label>
                    <select id="kycStatusSelect" class="form-select">
                        <option value="pending">Pending</option>
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">KYC Notes</label>
                    <textarea id="kycNotesText" class="form-control" rows="3" placeholder="Enter KYC verification notes..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="updateKYC()">Update KYC</button>
            </div>
        </div>
    </div>
</div>

<!-- Sync Product Modal -->
<div class="modal fade" id="syncModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fa-solid fa-sync"></i> Sync Product Mapping</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="syncProductId">
                <div class="mb-3">
                    <label class="form-label">Supplier Product ID</label>
                    <input type="text" id="syncSupplierProductId" class="form-control" placeholder="Enter supplier's product ID">
                </div>
                <div class="mb-3">
                    <label class="form-label">Supplier Cost (₹)</label>
                    <input type="number" id="syncSupplierCost" class="form-control" step="0.01" placeholder="0.00">
                </div>
                <div class="mb-3">
                    <label class="form-label">Auto Order</label>
                    <select id="syncAutoOrder" class="form-select">
                        <option value="0">Disabled</option>
                        <option value="1">Enabled</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Shipping Time (Days)</label>
                    <input type="number" id="syncShippingTime" class="form-control" placeholder="3">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" onclick="saveProductMapping()">Save Mapping</button>
            </div>
        </div>
    </div>
</div>

<!-- Bulk Category Change Modal -->
<div class="modal fade" id="bulkCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #0d6efd; color: white;">
                <h5 class="modal-title"><i class="fa-solid fa-tags"></i> Bulk Change Category</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="fa-solid fa-info-circle"></i> Selected Items: <strong id="bulkCatSelectedCount">0</strong>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Select New Category</label>
                    <select id="bulkNewCategory" class="form-select">
                        <option value="">-- Select Category --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Subcategory (Optional)</label>
                    <select id="bulkNewSubcategory" class="form-select">
                        <option value="">None</option>
                    </select>
                </div>
                <div class="alert alert-warning">
                    <i class="fa-solid fa-triangle-exclamation"></i> This will change the category for ALL selected items.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn" id="confirmBulkCategory" style="background-color: #0d6efd; color: white;">
                    <i class="fa-solid fa-save"></i> Update Categories
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
/* (Keep your existing styles – we'll add only new ones) */
.dropdown-toggle::after {
    display: none;
}
.dropdown-menu {
    position: absolute;
    inset: 0px auto auto 0px;
    margin: 0;
    transform: translate(0px, 40px);
}
.btn-group, .dropdown {
    position: relative;
    display: inline-block;
}
.icon-action-btn {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 6px;
    font-size: 13px;
    transition: transform 0.15s ease, box-shadow 0.15s ease;
}
.icon-action-btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 3px 8px rgba(0,0,0,0.18);
}
.btn:hover {
    opacity: 0.85;
}
.info-row {
    display: flex;
    padding: 6px 0;
    font-size: 13px;
    border-bottom: 1px solid #e9ecef;
}
.info-label {
    width: 140px;
    font-weight: 600;
    color: #666;
}
.info-value {
    flex: 1;
    color: #333;
}
.table th {
    background: #004392 !important;
    color: #fff !important;
}
.btn-primary,
.btn-warning,
.btn-danger,
.btn-info {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.btn-light border {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
    color: #fff !important;
}
.btn-secondary,
.btn-outline-secondary {
    background: #61b3e5 !important;
    color: #fff;
    border: 0 !important;
}
.btn-success {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.nav-tabs .nav-link.active {
    background: hsl(200.73deg 78.57% 72.55%);
    border: 0 !important;
    color: #fff !important;
    border-radius: 5px !important;
}
</style>

<script>
let selectedIds = [];
let currentProductId = null;

// Update bulk button visibility and selected count
function updateBulkUI() {
    selectedIds = [];
    $('.productCheckbox:checked').each(function() { 
        selectedIds.push($(this).val()); 
    });
    let count = selectedIds.length;
    if(count > 0) {
        $('#bulkDropdownWrapper').show();
        $('#bulkSelectedCount').text(count);
    } else {
        $('#bulkDropdownWrapper').hide();
    }
}

// Fix for dropdown initialization
$(document).ready(function() {
    // Initialize all dropdowns to ensure they work
    $('[data-bs-toggle="dropdown"]').dropdown();
    
    // Handle any dynamically added dropdowns
    $(document).on('click', '[data-bs-toggle="dropdown"]', function(e) {
        e.stopPropagation();
        $(this).dropdown('toggle');
    });
});

// Select All
$('#selectAll').change(function() {
    $('.productCheckbox').prop('checked', $(this).prop('checked'));
    updateBulkUI();
});

$(document).on('change', '.productCheckbox', function() { 
    updateBulkUI(); 
});

// ==========================================
// FILTER TABLE – INCLUDING SELLER FILTER
// ==========================================
function filterTable() {
    let typeFilter = $('#typeFilter').val();
    let statusFilter = $('#statusFilter').val().toLowerCase();
    let sellerFilter = $('#sellerFilter').val();
    let searchTerm = $('#searchInput').val().toLowerCase();
    
    $('#approvalTable tbody tr').each(function() {
        let row = $(this);
        let typeMatch = !typeFilter || row.data('type') === typeFilter;
        let statusMatch = !statusFilter || row.data('status') === statusFilter;
        let sellerMatch = !sellerFilter || row.data('seller-id') == sellerFilter;
        let searchMatch = !searchTerm || row.data('name').includes(searchTerm);
        row.toggle(typeMatch && statusMatch && sellerMatch && searchMatch);
    });
}

$('#typeFilter, #statusFilter, #sellerFilter, #searchInput').on('change keyup', filterTable);

// Reset Filters – include seller dropdown
$('#resetFilters').on('click', function() { 
    $('#typeFilter,#statusFilter,#sellerFilter,#searchInput').val(''); 
    filterTable(); 
    Swal.fire('Success', 'Filters reset successfully', 'success');
});

// Load subcategories based on category selection
$('#bulkNewCategory').change(function() {
    let categoryId = $(this).val();
    if(categoryId) {
        $.ajax({
            url: "{{ route('admin.approval.subcategories.by-category') }}",
            method: "GET",
            data: { category_id: categoryId },
            success: function(response) {
                let subcategorySelect = $('#bulkNewSubcategory');
                subcategorySelect.empty();
                subcategorySelect.append('<option value="">None</option>');
                if(response.subcategories && response.subcategories.length > 0) {
                    $.each(response.subcategories, function(key, subcat) {
                        subcategorySelect.append('<option value="' + subcat.id + '">' + subcat.name + '</option>');
                    });
                }
            },
            error: function() {
                $('#bulkNewSubcategory').empty().append('<option value="">None</option>');
            }
        });
    } else {
        $('#bulkNewSubcategory').empty().append('<option value="">None</option>');
    }
});

// ==========================================
// BULK ACTIONS FROM DROPDOWN
// ==========================================

// Bulk Approve
$('#bulkApproveItem').off('click').on('click', function(e) {
    e.preventDefault();
    if(selectedIds.length === 0) { 
        Swal.fire('Warning', 'No items selected', 'warning'); 
        return; 
    }
    Swal.fire({
        title: 'Bulk Approve Items',
        text: `Are you sure you want to approve ${selectedIds.length} selected item(s)?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0d6efd',
        confirmButtonText: 'Yes, Approve All',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed) {
            executeBulkAction('approve', null);
        }
    });
});

// Bulk Reject
$('#bulkRejectItem').off('click').on('click', function(e) {
    e.preventDefault();
    if(selectedIds.length === 0) { 
        Swal.fire('Warning', 'No items selected', 'warning'); 
        return; 
    }
    Swal.fire({
        title: 'Bulk Reject Items',
        text: `You are about to reject ${selectedIds.length} item(s).`,
        input: 'textarea',
        inputPlaceholder: 'Enter reason for rejection...',
        showCancelButton: true,
        confirmButtonColor: '#0b5ed7',
        confirmButtonText: 'Yes, Reject All',
        cancelButtonText: 'Cancel',
        preConfirm: (reason) => {
            if(!reason || !reason.trim()) {
                Swal.showValidationMessage('Please provide a reason for rejection');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if(result.isConfirmed && result.value) {
            executeBulkAction('reject', result.value);
        }
    });
});

// Bulk Enable
$('#bulkEnableItem').off('click').on('click', function(e) {
    e.preventDefault();
    if(selectedIds.length === 0) { 
        Swal.fire('Warning', 'No items selected', 'warning'); 
        return; 
    }
    Swal.fire({
        title: 'Bulk Enable Items',
        text: `Are you sure you want to enable ${selectedIds.length} selected item(s)?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0a58ca',
        confirmButtonText: 'Yes, Enable All',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if(result.isConfirmed) {
            executeBulkAction('enable', null);
        }
    });
});

// Bulk Disable
$('#bulkDisableItem').off('click').on('click', function(e) {
    e.preventDefault();
    if(selectedIds.length === 0) { 
        Swal.fire('Warning', 'No items selected', 'warning'); 
        return; 
    }
    Swal.fire({
        title: 'Bulk Disable Items',
        text: `You are about to disable ${selectedIds.length} item(s).`,
        input: 'textarea',
        inputPlaceholder: 'Enter reason for disabling...',
        showCancelButton: true,
        confirmButtonColor: '#084298',
        confirmButtonText: 'Yes, Disable All',
        cancelButtonText: 'Cancel',
        preConfirm: (reason) => {
            if(!reason || !reason.trim()) {
                Swal.showValidationMessage('Please provide a reason for disabling');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if(result.isConfirmed && result.value) {
            executeBulkAction('disable', result.value);
        }
    });
});

// Bulk Category - Open Modal
$('#bulkCategoryItem').off('click').on('click', function(e) {
    e.preventDefault();
    if(selectedIds.length === 0) { 
        Swal.fire('Warning', 'No items selected', 'warning'); 
        return; 
    }
    $('#bulkCatSelectedCount').text(selectedIds.length);
    $('#bulkNewCategory').val('');
    $('#bulkNewSubcategory').empty().append('<option value="">None</option>');
    $('#bulkCategoryModal').modal('show');
});

$('#confirmBulkCategory').off('click').on('click', function() {
    let newCategory = $('#bulkNewCategory').val();
    if(!newCategory) { 
        Swal.fire('Error', 'Please select a category', 'error'); 
        return; 
    }
    let newSubcategory = $('#bulkNewSubcategory').val() || null;
    
    $.ajax({
        url: "{{ route('admin.approval.bulk.category') }}", 
        method: "POST",
        data: { 
            _token: "{{ csrf_token() }}", 
            product_ids: selectedIds, 
            category_id: newCategory, 
            subcategory_id: newSubcategory 
        },
        beforeSend: function() {
            Swal.fire({ title: 'Updating Categories...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        },
        success: function(res) { 
            if(res.success) { 
                Swal.fire('Success', res.message, 'success').then(() => location.reload()); 
            } else { 
                Swal.fire('Error', res.message, 'error'); 
            } 
        },
        error: function(xhr) { 
            Swal.fire('Error', xhr.responseJSON?.message || 'Bulk category update failed', 'error'); 
        }
    });
    $('#bulkCategoryModal').modal('hide');
});

function executeBulkAction(action, reason) {
    let url = "{{ route('admin.approval.bulk.action') }}";
    let payload = { 
        _token: "{{ csrf_token() }}", 
        action: action, 
        product_ids: selectedIds 
    };
    if(reason) payload.reason = reason;
    
    $.ajax({
        url: url, 
        method: "POST", 
        data: payload,
        beforeSend: function() {
            Swal.fire({ title: 'Processing...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        },
        success: function(response) {
            if(response.success) {
                Swal.fire('Success', response.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Bulk action failed', 'error');
        }
    });
}

// ==========================================
// SINGLE ITEM ACTIONS
// ==========================================

// Approve single item
$(document).off('click', '.approveSingleBtn').on('click', '.approveSingleBtn', function() { 
    let id = $(this).data('id'); 
    Swal.fire({ 
        title: 'Approve Item?', 
        text: "Are you sure?", 
        icon: 'question', 
        showCancelButton: true, 
        confirmButtonColor: '#28a745', 
        confirmButtonText: 'Yes, Approve' 
    }).then((result) => {
        if(result.isConfirmed) {
            updateSingleStatus(id, 'approved');
        }
    });
});

// Reject single item
$(document).off('click', '.rejectSingleBtn').on('click', '.rejectSingleBtn', function() { 
    let id = $(this).data('id'); 
    Swal.fire({ 
        title: 'Reject Item', 
        input: 'textarea', 
        inputPlaceholder: 'Enter reason for rejection...', 
        showCancelButton: true, 
        confirmButtonColor: '#dc3545', 
        confirmButtonText: 'Yes, Reject',
        preConfirm: (reason) => { 
            if(!reason || !reason.trim()) {
                Swal.showValidationMessage('Reason required');
                return false;
            }
            return reason; 
        }
    }).then((result) => {
        if(result.isConfirmed && result.value) {
            updateSingleStatus(id, 'rejected', result.value);
        }
    });
});

// DISABLE single item
$(document).off('click', '.disableSingleBtn').on('click', '.disableSingleBtn', function() { 
    let id = $(this).data('id'); 
    Swal.fire({ 
        title: 'Disable Product', 
        text: "Please provide a reason for disabling this product:",
        input: 'textarea',
        inputPlaceholder: 'Enter reason for disabling...',
        showCancelButton: true, 
        confirmButtonColor: '#ffc107', 
        confirmButtonText: 'Yes, Disable',
        preConfirm: (reason) => { 
            if(!reason || !reason.trim()) {
                Swal.showValidationMessage('Reason is required');
                return false;
            }
            return reason; 
        }
    }).then((result) => {
        if(result.isConfirmed && result.value) {
            $.ajax({
                url: `/admin/approval/force-disable/${id}`,
                method: "POST",
                data: { 
                    _token: "{{ csrf_token() }}", 
                    reason: result.value 
                },
                beforeSend: function() {
                    Swal.fire({ title: 'Disabling...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
                },
                success: function(res) { 
                    if(res.success) {
                        Swal.fire('Success', res.message, 'success').then(() => location.reload());
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(xhr) {
                    Swal.fire('Error', xhr.responseJSON?.message || 'Disable failed', 'error');
                }
            });
        }
    });
});

// Enable single item
$(document).off('click', '.enableSingleBtn').on('click', '.enableSingleBtn', function() { 
    let id = $(this).data('id'); 
    Swal.fire({ 
        title: 'Enable Item?', 
        text: "Are you sure you want to enable this item?", 
        icon: 'question', 
        showCancelButton: true, 
        confirmButtonColor: '#28a745', 
        confirmButtonText: 'Yes, Enable' 
    }).then((result) => {
        if(result.isConfirmed) {
            updateSingleStatus(id, 'active');
        }
    });
});

// Generic status update function
function updateSingleStatus(id, status, reason = null) {
    $.ajax({
        url: "{{ route('admin.approval.update') }}", 
        method: "POST",
        data: { 
            _token: "{{ csrf_token() }}", 
            id: id, 
            type: 'product', 
            status: status, 
            reason: reason 
        },
        beforeSend: function() {
            Swal.fire({ title: 'Updating...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        },
        success: function(res) { 
            if(res.success) {
                Swal.fire('Success', 'Status updated', 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Update failed', 'error');
        }
    });
}

// ==========================================
// DROPSHIPPING FUNCTIONS (unchanged)
// ==========================================

function approveSupplier(supplierId) {
    $.ajax({
        url: `/admin/approval/supplier/${supplierId}/approve`,
        method: "POST",
        data: { _token: "{{ csrf_token() }}" },
        success: function(response) {
            if(response.success) {
                Swal.fire('Success', 'Supplier approved successfully', 'success');
                $(`#supplierStatus-${supplierId}`).removeClass('bg-warning').addClass('bg-success').text('Approved');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}

function rejectSupplier(supplierId) {
    Swal.fire({
        title: 'Reject Supplier',
        input: 'textarea',
        inputPlaceholder: 'Enter reason for rejection...',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: 'Yes, Reject',
        preConfirm: (reason) => {
            if(!reason || !reason.trim()) {
                Swal.showValidationMessage('Reason required');
                return false;
            }
            return reason;
        }
    }).then((result) => {
        if(result.isConfirmed && result.value) {
            $.ajax({
                url: `/admin/approval/supplier/${supplierId}/reject`,
                method: "POST",
                data: { reason: result.value, _token: "{{ csrf_token() }}" },
                success: function(response) {
                    if(response.success) {
                        Swal.fire('Success', 'Supplier rejected', 'success');
                        $(`#supplierStatus-${supplierId}`).removeClass('bg-warning').addClass('bg-danger').text('Rejected');
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}

function showKYCModal(supplierId, currentStatus, currentNotes) {
    $('#kycSupplierId').val(supplierId);
    $('#kycStatusSelect').val(currentStatus);
    $('#kycNotesText').val(currentNotes);
    $('#kycModal').modal('show');
}

function updateKYC() {
    let supplierId = $('#kycSupplierId').val();
    let kycStatus = $('#kycStatusSelect').val();
    let kycNotes = $('#kycNotesText').val();
    
    $.ajax({
        url: `/admin/approval/supplier/${supplierId}/kyc`,
        method: "POST",
        data: { kyc_status: kycStatus, kyc_notes: kycNotes, _token: "{{ csrf_token() }}" },
        success: function(response) {
            if(response.success) {
                Swal.fire('Success', 'KYC updated successfully', 'success');
                let badgeClass = kycStatus == 'verified' ? 'bg-success' : (kycStatus == 'rejected' ? 'bg-danger' : 'bg-secondary');
                $(`#supplierKyc-${supplierId}`).removeClass('bg-success bg-danger bg-secondary').addClass(badgeClass).text(kycStatus.charAt(0).toUpperCase() + kycStatus.slice(1));
                $('#kycModal').modal('hide');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}

function viewSupplierDetails(supplierId) {
    window.location.href = `/admin/sellers/${supplierId}`;
}

// Product Sync Functions
$('#syncProductSelect').change(function() {
    let productId = $(this).val();
    if(productId) {
        $.ajax({
            url: `/admin/approval/product/${productId}/details`,
            method: "GET",
            success: function(response) {
                if(response.success) {
                    currentProductId = productId;
                    $('#displaySupplierProductId').text(response.product.supplier_product_id || '-');
                    $('#displaySyncSupplierCost').text('₹' + (parseFloat(response.product.supplier_cost) || 0).toLocaleString());
                    $('#displaySyncSellingPrice').text('₹' + (parseFloat(response.product.price) || 0).toLocaleString());
                    let margin = (response.product.price || 0) - (response.product.supplier_cost || 0);
                    $('#displaySyncMargin').html(`<span class="${margin > 0 ? 'text-success' : 'text-danger'}">₹${margin.toLocaleString()}</span>`);
                    $('#displaySyncAutoOrder').text(response.product.auto_order ? 'Enabled' : 'Disabled');
                    $('#syncProductDetails').show();
                }
            }
        });
    } else {
        $('#syncProductDetails').hide();
    }
});

function openSyncModal() {
    if(!currentProductId) {
        Swal.fire('Warning', 'Please select a product first', 'warning');
        return;
    }
    
    $.ajax({
        url: `/admin/approval/product/${currentProductId}/details`,
        method: "GET",
        success: function(response) {
            if(response.success) {
                $('#syncProductId').val(currentProductId);
                $('#syncSupplierProductId').val(response.product.supplier_product_id || '');
                $('#syncSupplierCost').val(response.product.supplier_cost || 0);
                $('#syncAutoOrder').val(response.product.auto_order ? 1 : 0);
                $('#syncShippingTime').val(response.product.shipping_time || 3);
                $('#syncModal').modal('show');
            }
        }
    });
}

function saveProductMapping() {
    let productId = $('#syncProductId').val();
    let supplierProductId = $('#syncSupplierProductId').val();
    let supplierCost = $('#syncSupplierCost').val();
    let autoOrder = $('#syncAutoOrder').val();
    let shippingTime = $('#syncShippingTime').val();
    
    if(!supplierProductId) {
        Swal.fire('Warning', 'Please enter supplier product ID', 'warning');
        return;
    }
    
    $.ajax({
        url: `/admin/approval/product/${productId}/sync-mapping`,
        method: "POST",
        data: {
            product_id: productId,
            supplier_product_id: supplierProductId,
            supplier_cost: supplierCost,
            auto_order: autoOrder,
            shipping_time: shippingTime,
            _token: "{{ csrf_token() }}"
        },
        success: function(response) {
            if(response.success) {
                Swal.fire('Success', 'Product mapping synced successfully', 'success');
                $('#syncModal').modal('hide');
                $('#syncProductSelect').trigger('change');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        }
    });
}

function validatePriceSyncForProduct() {
    if(!currentProductId) {
        Swal.fire('Warning', 'Please select a product first', 'warning');
        return;
    }
    
    $.ajax({
        url: `/admin/approval/product/${currentProductId}/validate-price`,
        method: "GET",
        success: function(response) {
            if(response.success) {
                let v = response.validation;
                Swal.fire({
                    title: 'Price Validation Result',
                    html: `
                        <table class="table table-sm">
                            <tr><td class="text-start"><strong>Supplier Cost:</strong></td><td class="text-end">₹${v.supplier_price}</td></tr>
                            <tr><td class="text-start"><strong>Selling Price:</strong></td><td class="text-end">₹${v.selling_price}</td></tr>
                            <tr><td class="text-start"><strong>Margin:</strong></td><td class="text-end ${v.margin > 0 ? 'text-success' : 'text-danger'}">₹${v.margin} (${v.margin_percentage}%)</td></tr>
                            <tr><td class="text-start"><strong>Status:</strong></td><td class="text-end">${v.message}</td></tr>
                        </table>
                    `,
                    icon: v.is_profitable ? (v.margin_percentage >= 10 ? 'success' : 'warning') : 'error'
                });
            }
        }
    });
}

// Supplier search
$('#supplierSearch').on('keyup', function() {
    let searchTerm = $(this).val().toLowerCase();
    $('#suppliersTableBody tr').each(function() {
        let supplierName = $(this).data('supplier-name') || '';
        $(this).toggle(supplierName.includes(searchTerm));
    });
});

updateBulkUI();
</script>
@endsection
