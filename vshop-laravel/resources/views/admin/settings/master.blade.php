@extends('layouts.vertical', ['title' => 'Master System Control'])

@section('css')
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
    /* Base Variables & Container */
    :root {
        --primary-dark: #0f172a;
        --primary-blue: #1e3a8a;
        --accent-blue: #3b82f6;
        --text-muted: #64748b;
        --border-color: #e2e8f0;
        --bg-light: #f8fafc;
        --surface-white: #ffffff;
    }
    .master-container {
        font-family: 'Inter', sans-serif;
        background-color: #f1f5f9;
        padding: 24px;
        min-height: calc(100vh - 70px);
    }
    
    /* Page Title */
    .master-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .master-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    /* Nav Tabs */
    .master-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 24px;
        background: var(--surface-white);
        padding: 8px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        border: 1px solid var(--border-color);
        flex-wrap: wrap;
    }
    .master-tab-btn {
        flex: 1;
        min-width: 150px;
        padding: 12px 20px;
        border: none;
        background: transparent;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.95rem;
        color: var(--text-muted);
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
    }
    .master-tab-btn i {
        font-size: 1.5rem;
        transition: transform 0.3s ease;
    }
    .master-tab-btn:hover {
        background-color: var(--bg-light);
        color: var(--primary-dark);
    }
    .master-tab-btn.active {
        background: linear-gradient(135deg, var(--primary-dark), var(--primary-blue));
        color: white;
        box-shadow: 0 4px 12px rgba(30, 58, 138, 0.2);
    }
    .master-tab-btn.active i {
        transform: scale(1.1);
    }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; animation: fadeIn 0.4s ease-in-out; }
    
    /* Metric Cards */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }
    .metric-card {
        background: var(--surface-white);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        border: 1px solid var(--border-color);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .metric-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        border-radius: 4px 0 0 4px;
    }
    .metric-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .metric-icon-box {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        flex-shrink: 0;
    }
    .metric-info {
        flex-grow: 1;
    }
    .metric-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin-bottom: 2px;
        line-height: 1.2;
    }
    .metric-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .metric-badge {
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 20px;
        font-weight: 600;
        margin-top: 6px;
        display: inline-block;
    }
    
    /* Card Variants */
    .card-primary::before { background: var(--accent-blue); }
    .card-primary .metric-icon-box { background: rgba(59, 130, 246, 0.1); color: var(--accent-blue); }
    
    .card-success::before { background: #10b981; }
    .card-success .metric-icon-box { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    
    .card-warning::before { background: #f59e0b; }
    .card-warning .metric-icon-box { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    
    .card-danger::before { background: #ef4444; }
    .card-danger .metric-icon-box { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    
    .card-info::before { background: #0ea5e9; }
    .card-info .metric-icon-box { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }
    
    .card-dark::before { background: var(--primary-dark); }
    .card-dark .metric-icon-box { background: rgba(15, 23, 42, 0.1); color: var(--primary-dark); }
    
    /* Section Cards */
    .section-card {
        background: var(--surface-white);
        border-radius: 16px;
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 24px;
        overflow: hidden;
    }
    .section-header {
        padding: 20px 24px;
        border-bottom: 1px solid var(--border-color);
        background: rgba(248, 250, 252, 0.5);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--primary-dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .section-body {
        padding: 24px;
    }
    
    /* Forms & Inputs */
    .form-label {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--primary-dark);
        margin-bottom: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .form-control, .form-select {
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        padding: 10px 14px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.02);
    }
    .form-control:focus, .form-select:focus {
        border-color: var(--accent-blue);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
        outline: none;
    }
    
    /* Buttons */
    .btn-primary-custom {
        background: linear-gradient(135deg, var(--primary-blue), var(--accent-blue));
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 4px 6px rgba(59, 130, 246, 0.2);
    }
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(59, 130, 246, 0.3);
        color: white;
    }
    
    /* Tables */
    .table-container {
        overflow-x: auto;
    }
    .table {
        margin-bottom: 0;
        border-collapse: separate;
        border-spacing: 0;
        width: 100%;
    }
    .table th {
        background: var(--bg-light);
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 14px 16px;
        border-bottom: 1px solid var(--border-color);
        white-space: nowrap;
    }
    .table td {
        padding: 16px;
        vertical-align: middle;
        font-size: 0.9rem;
        color: var(--primary-dark);
        border-bottom: 1px solid var(--border-color);
    }
    .table tbody tr:hover {
        background-color: #f8fafc;
    }
    .table tbody tr:last-child td {
        border-bottom: none;
    }
    
    /* Badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
        display: inline-block;
    }
    .status-active { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .status-inactive { background: rgba(100, 116, 139, 0.1); color: #64748b; }
    .status-rule { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    
    /* Animations */
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection

@section('content')
<div class="master-container">
    <div class="master-header">
        <h1 class="master-title"><i class='bx bx-slider-alt'></i> Master System Control</h1>
        <div class="breadcrumb text-muted fs-14">
            <span>Admin</span> <i class='bx bx-chevron-right mx-1'></i>
            <span>Settings</span> <i class='bx bx-chevron-right mx-1'></i>
            <span class="fw-semibold text-dark">Master Control</span>
        </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="master-tabs">
        <button class="master-tab-btn" data-tab="overview">
            <i class='bx bx-home-alt'></i> System Overview
        </button>
        <button class="master-tab-btn" data-tab="revenue-engine">
            <i class='bx bx-line-chart'></i> Revenue Engine
        </button>
        <button class="master-tab-btn active" data-tab="boost-revenue">
            <i class='bx bx-rocket'></i> Boost Revenue Engine
        </button>
    </div>

    <div class="tab-content-wrapper">
        <!-- ========== OVERVIEW TAB ========== -->
        <div class="tab-pane" id="tab-overview">
            <div class="metrics-grid">
                <div class="metric-card card-primary">
                    <div class="metric-icon-box"><i class='bx bx-cog'></i></div>
                    <div class="metric-info">
                        <div class="metric-value">0</div>
                        <div class="metric-label">Rule Engine</div>
                        <div class="metric-badge bg-primary bg-opacity-10 text-primary">0 Active Rules</div>
                    </div>
                </div>
                <div class="metric-card card-info">
                    <div class="metric-icon-box"><i class='bx bx-user-circle'></i></div>
                    <div class="metric-info">
                        <div class="metric-value">0</div>
                        <div class="metric-label">Admin Role</div>
                        <div class="metric-badge bg-info bg-opacity-10 text-info">Roles</div>
                    </div>
                </div>
                <div class="metric-card card-warning">
                    <div class="metric-icon-box"><i class='bx bx-shield-quarter'></i></div>
                    <div class="metric-info">
                        <div class="metric-value">0</div>
                        <div class="metric-label">Compliance</div>
                        <div class="metric-badge bg-warning bg-opacity-10 text-warning">Policies</div>
                    </div>
                </div>
                <div class="metric-card card-danger">
                    <div class="metric-icon-box"><i class='bx bx-plug'></i></div>
                    <div class="metric-info">
                        <div class="metric-value">0</div>
                        <div class="metric-label">Integrations</div>
                        <div class="metric-badge bg-danger bg-opacity-10 text-danger">Active</div>
                    </div>
                </div>
                <div class="metric-card card-success">
                    <div class="metric-icon-box"><i class='bx bx-check-circle text-white'></i></div>
                    <div class="metric-info">
                        <div class="metric-value text-success">Good</div>
                        <div class="metric-label">System Health</div>
                        <div class="metric-badge bg-success text-white">All Systems Operational</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== REVENUE ENGINE TAB ========== -->
        <div class="tab-pane" id="tab-revenue-engine">
            <div class="metrics-grid">
                <div class="metric-card card-primary">
                    <div class="metric-icon-box"><i class='bx bx-wallet'></i></div>
                    <div class="metric-info">
                        <div class="metric-value money" id="total-lead-revenue" data-amount="0">₹0</div>
                        <div class="metric-label">Total Lead Revenue</div>
                    </div>
                </div>
                <div class="metric-card card-success">
                    <div class="metric-icon-box"><i class='bx bx-group'></i></div>
                    <div class="metric-info">
                        <div class="metric-value money" id="revenue-per-seller" data-amount="0">₹0</div>
                        <div class="metric-label">Revenue Per Seller</div>
                    </div>
                </div>
                <div class="metric-card card-info">
                    <div class="metric-icon-box"><i class='bx bx-category'></i></div>
                    <div class="metric-info">
                        <div class="metric-value money" id="revenue-per-category" data-amount="0">₹0</div>
                        <div class="metric-label">Revenue Per Category</div>
                    </div>
                </div>
                <div class="metric-card card-warning">
                    <div class="metric-icon-box"><i class='bx bx-mobile-alt'></i></div>
                    <div class="metric-info">
                        <div class="metric-value money" id="revenue-per-app" data-amount="0">₹0</div>
                        <div class="metric-label">Revenue Per App</div>
                    </div>
                </div>
            </div>

            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-line-chart'></i> Revenue Trends (Last 30 Days)</h2>
                </div>
                <div class="section-body">
                    <canvas id="revenueTrendsChart" style="width:100%;height:300px;"></canvas>
                </div>
            </div>
        </div>

        <!-- ========== BOOST REVENUE ENGINE TAB ========== -->
        <div class="tab-pane active" id="tab-boost-revenue">
            <div class="metrics-grid">
                <div class="metric-card card-primary">
                    <div class="metric-icon-box"><i class='bx bx-trending-up'></i></div>
                    <div class="metric-info">
                        <div class="metric-value money" id="boost-total-revenue" data-amount="0">₹0</div>
                        <div class="metric-label">Total Boost Revenue</div>
                    </div>
                </div>
                <div class="metric-card card-success">
                    <div class="metric-icon-box"><i class='bx bx-money-withdraw'></i></div>
                    <div class="metric-info">
                        <div class="metric-value money" id="boost-total-amount" data-amount="0">₹0</div>
                        <div class="metric-label">Total Boost Spend</div>
                    </div>
                </div>
                <div class="metric-card card-info">
                    <div class="metric-icon-box"><i class='bx bx-pie-chart-alt-2'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="boost-roi">0.00x</div>
                        <div class="metric-label">Avg ROI (Rev/Spend)</div>
                    </div>
                </div>
                <div class="metric-card card-warning">
                    <div class="metric-icon-box"><i class='bx bx-broadcast'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="boost-active-count">0</div>
                        <div class="metric-label">Active Boosts</div>
                    </div>
                </div>
            </div>

            <!-- Add Boost Form -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-plus-circle'></i> Add New Boost Rule / Campaign</h2>
                </div>
                <div class="section-body">
                    @if ($errors->any())
                        <div class="alert alert-danger rounded-3 border-0 shadow-sm">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form action="{{ route('admin.settings.boost.store') }}" method="POST">
                        @csrf
                        <div id="boost-rows-container">
                            <div class="row g-3 align-items-end boost-input-row mb-3 pb-3 border-bottom border-light">
                                <div class="col-md-2">
                                    <label class="form-label">Seller</label>
                                    <select class="form-select boost-seller-select" name="seller_id[]" onchange="filterDependentOptionsRow(this)">
                                        <option value="">All Sellers (Global)</option>
                                        @foreach($settingsData['sellers'] ?? [] as $seller)
                                            <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Category</label>
                                    <select class="form-select boost-category-select" name="category_id[]" onchange="filterDependentOptionsRow(this)">
                                        <option value="">Select (Optional)</option>
                                        @foreach($settingsData['categories'] as $category)
                                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Product</label>
                                    <select class="form-select boost-product-select" name="product_id[]">
                                        <option value="">Select (Optional)</option>
                                        @foreach($settingsData['products'] as $product)
                                            <option value="{{ $product->id }}" data-category-name="{{ $product->category->name ?? '' }}" data-seller="{{ $product->created_by_id }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">Per Day Amount *</label>
                                    <input type="number" class="form-control" name="per_day[]" min="0" step="0.01" required placeholder="0.00">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Days</label>
                                    <input type="number" class="form-control" name="days[]" min="1" placeholder="e.g. 7">
                                </div>
                                <div class="col-md-1">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status[]">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                        <option value="rule" selected>Rule</option>
                                    </select>
                                </div>
                                <div class="col-md-1 text-end">
                                    <button type="button" class="btn btn-danger w-100 remove-boost-row d-none" onclick="this.closest('.boost-input-row').remove()" style="padding: 10px; border-radius: 8px;">
                                        <i class="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <div>
                                <button type="button" class="btn btn-light border fw-semibold" onclick="addBoostRow()">
                                    <i class="bx bx-plus"></i> Add Row
                                </button>
                                <div class="text-muted mt-2 fs-12">
                                    <i class='bx bx-info-circle'></i> Leave <b>Days</b> empty to create a <b>Pricing Rule</b>. Rules define the per-day cost for sellers when they choose to boost.
                                </div>
                            </div>
                            <button type="submit" class="btn-primary-custom px-4"><i class="bx bx-save fs-5"></i> Save Configurations</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Boosts Table -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-list-ul'></i> All Boosts & Rules</h2>
                    <a href="{{ request()->fullUrl() }}" class="btn btn-light btn-sm border fw-semibold"><i class="bx bx-refresh me-1"></i> Refresh List</a>
                </div>
                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Target</th>
                                <th>Per Day</th>
                                <th>Days</th>
                                <th>Total Spend</th>
                                <th>Status</th>
                                <th>Performance (Imp/Clk/Enq)</th>
                                <th>Revenue</th>
                                <th>Date</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($settingsData['boosts'] as $boost)
                                @php
                                    $seller = $settingsData['sellers']->firstWhere('id', $boost['seller_id']);
                                    $category = $settingsData['categories']->firstWhere('id', $boost['category_id']);
                                    $product = $settingsData['products']->firstWhere('id', $boost['product_id']);
                                @endphp
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $product->name ?? 'All Products' }}</div>
                                        <div class="fs-12 text-muted">{{ $seller->name ?? 'Global' }} &bull; {{ $category->name ?? 'Any Category' }}</div>
                                    </td>
                                    <td class="fw-semibold text-success"><span class="money" data-amount="{{ $boost['per_day'] ?? 0 }}">₹{{ number_format($boost['per_day'] ?? 0, 2) }}</span></td>
                                    <td>{{ $boost['days'] ?? 1 }}</td>
                                    <td class="fw-semibold"><span class="money" data-amount="{{ $boost['boost_amount'] }}">₹{{ number_format($boost['boost_amount'], 2) }}</span></td>
                                    <td><span class="status-badge status-{{ strtolower($boost['status']) }}" id="status-badge-{{ $boost['id'] }}">{{ $boost['status'] }}</span></td>
                                    <td>
                                        <div class="d-flex gap-2 text-muted fs-12">
                                            <span><i class='bx bx-show'></i> {{ $boost['impressions'] ?? 0 }}</span>
                                            <span><i class='bx bx-mouse'></i> {{ $boost['clicks'] ?? 0 }}</span>
                                            <span><i class='bx bx-message-square-detail'></i> {{ $boost['enquiries'] ?? 0 }}</span>
                                        </div>
                                    </td>
                                    <td class="fw-bold text-primary"><span class="money" data-amount="{{ $boost['revenue_generated'] ?? 0 }}">₹{{ number_format($boost['revenue_generated'] ?? 0, 2) }}</span></td>
                                    <td class="fs-12 text-muted">{{ $boost['created_at'] ?? '' }}</td>
                                    <td class="text-end">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <button class="btn btn-light border btn-sm" title="Edit"
                                                    data-bs-toggle="modal" data-bs-target="#boostEditModal"
                                                    data-id="{{ $boost['id'] }}" data-seller="{{ $boost['seller_id'] }}"
                                                    data-category="{{ $boost['category_id'] }}" data-product="{{ $boost['product_id'] }}"
                                                    data-per-day="{{ $boost['per_day'] ?? 0 }}" data-days="{{ $boost['days'] ?? 1 }}"
                                                    data-status="{{ $boost['status'] }}" data-impressions="{{ $boost['impressions'] ?? 0 }}"
                                                    data-clicks="{{ $boost['clicks'] ?? 0 }}" data-enquiries="{{ $boost['enquiries'] ?? 0 }}"
                                                    data-conversions="{{ $boost['conversions'] ?? 0 }}" data-revenue="{{ $boost['revenue_generated'] ?? 0 }}">
                                                <i class="bx bx-edit text-primary"></i>
                                            </button>
                                            <form action="{{ route('admin.settings.boost.toggle', $boost['id']) }}" method="POST" class="m-0 toggle-boost-form">
                                                @csrf
                                                <button type="submit" class="btn btn-light border btn-sm" title="Toggle Status"><i class="bx bx-power-off text-warning"></i></button>
                                            </form>
                                            <form action="{{ route('admin.settings.boost.delete', $boost['id']) }}" method="POST" class="m-0" onsubmit="return confirm('Delete this boost?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-light border btn-sm" title="Delete"><i class="bx bx-trash text-danger"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="9" class="text-center text-muted py-4">No boosts or rules found. Create one above!</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ROI Chart -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-bar-chart-alt-2'></i> ROI Tracking (Last 10 Boosts)</h2>
                </div>
                <div class="section-body">
                    <canvas id="roiTrackingChart" style="width:100%;height:300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ========== EDIT BOOST MODAL ========== -->
<div class="modal fade" id="boostEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light border-bottom-0">
                <h5 class="modal-title fw-bold text-dark"><i class='bx bx-edit text-primary me-2'></i> Edit Boost Configuration</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.settings.boost.update', ['id' => '__ID__']) }}" method="POST" id="boostEditForm">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <input type="hidden" name="id" id="edit-boost-id">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Seller</label>
                            <select class="form-select" id="edit-boost-seller" name="seller_id" onchange="filterEditDependentOptions()">
                                <option value="">All Sellers (Global)</option>
                                @foreach($settingsData['sellers'] ?? [] as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" id="edit-boost-category" name="category_id" onchange="filterEditDependentOptions()">
                                <option value="">Select (Optional)</option>
                                @foreach($settingsData['categories'] as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label">Product</label>
                            <select class="form-select" id="edit-boost-product" name="product_id">
                                <option value="">Select (Optional)</option>
                                @foreach($settingsData['products'] as $product)
                                    <option value="{{ $product->id }}" data-category-name="{{ $product->category->name ?? '' }}" data-seller="{{ $product->created_by_id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-12 mt-4 mb-2"><h6 class="fw-bold text-muted border-bottom pb-2">Pricing & Status</h6></div>
                        
                        <div class="col-md-4">
                            <label class="form-label">Per Day Amount</label>
                            <input type="number" class="form-control" id="edit-boost-per-day" name="per_day" min="0" step="0.01" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Days</label>
                            <input type="number" class="form-control" id="edit-boost-days" name="days" min="1" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit-boost-status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                                <option value="rule">Rule</option>
                            </select>
                        </div>

                        <div class="col-12 mt-4 mb-2"><h6 class="fw-bold text-muted border-bottom pb-2">Analytics Override</h6></div>

                        <div class="col-md-3">
                            <label class="form-label">Impressions</label>
                            <input type="number" class="form-control" id="edit-boost-impressions" name="impressions" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Clicks</label>
                            <input type="number" class="form-control" id="edit-boost-clicks" name="clicks" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Enquiries</label>
                            <input type="number" class="form-control" id="edit-boost-enquiries" name="enquiries" min="0">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Conversions</label>
                            <input type="number" class="form-control" id="edit-boost-conversions" name="conversions" min="0">
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label">Revenue Generated Amount</label>
                            <input type="number" class="form-control" id="edit-boost-revenue" name="revenue_generated" min="0" step="0.01">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0 bg-light p-4 rounded-bottom">
                    <button type="button" class="btn btn-light border fw-semibold px-4" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-primary-custom px-4"><i class="bx bx-check-circle"></i> Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // ========== CURRENCY FORMATTING ==========
    function formatLocalMoneyElements() {
        const savedCurrency = localStorage.getItem('selectedCurrency') || 'INR';
        const rates = { INR: 1, USD: 0.012, EUR: 0.011, GBP: 0.0096 };
        const symbols = { INR: '₹', USD: '$', EUR: '€', GBP: '£' };
        const r = rates[savedCurrency] || 1, s = symbols[savedCurrency] || '₹';
        document.querySelectorAll('.money').forEach(el => {
            let amt = parseFloat(el.getAttribute('data-amount'));
            if (!isNaN(amt)) {
                el.innerText = s + ' ' + (amt * r).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        });
    }

    // Listen for global currency changes from topbar
    window.addEventListener('currencyChanged', function(e) {
        document.querySelectorAll('.money').forEach(el => {
            let amt = parseFloat(el.getAttribute('data-amount'));
            if (!isNaN(amt)) {
                el.innerText = e.detail.symbol + ' ' + (amt * e.detail.rate).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            }
        });
    });

    // ========== TAB SWITCHING LOGIC ==========
    document.querySelectorAll('.master-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            document.querySelectorAll('.master-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            
            btn.classList.add('active');
            document.getElementById('tab-' + btn.dataset.tab).classList.add('active');
        });
    });

    // ========== DYNAMIC FORM OPTIONS ==========
    const originalBoostCategories = [];
    document.querySelectorAll('.boost-category-select').item(0)?.querySelectorAll('option').forEach(opt => {
        originalBoostCategories.push({ value: opt.value, text: opt.text });
    });

    const originalBoostProducts = [];
    document.querySelectorAll('.boost-product-select').item(0)?.querySelectorAll('option').forEach(opt => {
        originalBoostProducts.push({
            value: opt.value, text: opt.text,
            categoryName: opt.getAttribute('data-category-name'),
            seller: opt.getAttribute('data-seller')
        });
    });

    function addBoostRow() {
        const container = document.getElementById('boost-rows-container');
        const firstRow = container.querySelector('.boost-input-row');
        const newRow = firstRow.cloneNode(true);
        
        newRow.querySelectorAll('input').forEach(input => input.value = '');
        newRow.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
        
        const removeBtn = newRow.querySelector('.remove-boost-row');
        if (removeBtn) removeBtn.classList.remove('d-none');
        
        const categorySelect = newRow.querySelector('.boost-category-select');
        const productSelect = newRow.querySelector('.boost-product-select');
        
        categorySelect.innerHTML = '<option value="">Select (Optional)</option>';
        originalBoostCategories.forEach(cat => {
            if (cat.value === '') return;
            const opt = document.createElement('option');
            opt.value = cat.value; opt.text = cat.text;
            categorySelect.appendChild(opt);
        });
        
        productSelect.innerHTML = '<option value="">Select (Optional)</option>';
        originalBoostProducts.forEach(prod => {
            if (prod.value === '') return;
            const opt = document.createElement('option');
            opt.value = prod.value; opt.text = prod.text;
            opt.setAttribute('data-category-name', prod.categoryName);
            opt.setAttribute('data-seller', prod.seller);
            productSelect.appendChild(opt);
        });

        container.appendChild(newRow);
    }

    function filterDependentOptionsRow(element) {
        const row = element.closest('.boost-input-row');
        const sellerId = row.querySelector('.boost-seller-select')?.value;
        const categorySelect = row.querySelector('.boost-category-select');
        const productSelect = row.querySelector('.boost-product-select');
        
        const currentCategory = categorySelect.value;
        const currentProduct = productSelect.value;
        
        categorySelect.innerHTML = '<option value="">Select (Optional)</option>';
        originalBoostCategories.forEach(cat => {
            if (cat.value === '') return;
            const opt = document.createElement('option');
            opt.value = cat.value; opt.text = cat.text;
            if (cat.value == currentCategory) opt.selected = true;
            categorySelect.appendChild(opt);
        });

        const categoryName = categorySelect.options[categorySelect.selectedIndex]?.text;
        productSelect.innerHTML = '<option value="">Select (Optional)</option>';
        originalBoostProducts.forEach(product => {
            if (product.value === '') return;
            let matchSeller = !sellerId || product.seller == sellerId;
            let matchCategory = !currentCategory || product.categoryName === categoryName;
            
            if (matchSeller && matchCategory) {
                const opt = document.createElement('option');
                opt.value = product.value; opt.text = product.text;
                opt.setAttribute('data-category-name', product.categoryName);
                opt.setAttribute('data-seller', product.seller);
                if (product.value == currentProduct) opt.selected = true;
                productSelect.appendChild(opt);
            }
        });
    }

    const originalEditBoostCategories = Array.from(document.getElementById('edit-boost-category')?.options || []).map(opt => ({
        value: opt.value, text: opt.text
    }));

    const originalEditBoostProducts = Array.from(document.getElementById('edit-boost-product')?.options || []).map(opt => ({
        value: opt.value, text: opt.text,
        categoryName: opt.getAttribute('data-category-name'),
        seller: opt.getAttribute('data-seller')
    }));

    function filterEditDependentOptions() {
        const sellerId = document.getElementById('edit-boost-seller').value;
        const categorySelect = document.getElementById('edit-boost-category');
        const productSelect = document.getElementById('edit-boost-product');
        
        const currentCategory = categorySelect.value;
        const currentProduct = productSelect.value;
        
        categorySelect.innerHTML = '<option value="">Select (Optional)</option>';
        originalEditBoostCategories.forEach(cat => {
            if (cat.value === '') return;
            const opt = document.createElement('option');
            opt.value = cat.value; opt.text = cat.text;
            if (cat.value == currentCategory) opt.selected = true;
            categorySelect.appendChild(opt);
        });

        const categoryName = categorySelect.options[categorySelect.selectedIndex]?.text;
        productSelect.innerHTML = '<option value="">Select (Optional)</option>';
        originalEditBoostProducts.forEach(product => {
            if (product.value === '') return;
            let matchSeller = !sellerId || product.seller == sellerId;
            let matchCategory = !currentCategory || product.categoryName == categoryName;
            if (matchSeller && matchCategory) {
                const opt = document.createElement('option');
                opt.value = product.value; opt.text = product.text;
                opt.setAttribute('data-category-name', product.categoryName);
                opt.setAttribute('data-seller', product.seller);
                if (product.value == currentProduct) opt.selected = true;
                productSelect.appendChild(opt);
            }
        });
    }

    // ========== EDIT MODAL POPULATE ==========
    document.addEventListener('DOMContentLoaded', function() {
        formatLocalMoneyElements(); // initial currency format

        const editModal = document.getElementById('boostEditModal');
        if(editModal) {
            editModal.addEventListener('show.bs.modal', function(event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                document.getElementById('edit-boost-id').value = id;

                const form = document.getElementById('boostEditForm');
                const action = form.getAttribute('action');
                form.setAttribute('action', action.replace('__ID__', id).replace(/\d+$/, id));

                document.getElementById('edit-boost-seller').value = button.getAttribute('data-seller') || '';
                document.getElementById('edit-boost-category').value = button.getAttribute('data-category') || '';
                filterEditDependentOptions();
                document.getElementById('edit-boost-product').value = button.getAttribute('data-product') || '';
                document.getElementById('edit-boost-per-day').value = button.getAttribute('data-per-day');
                document.getElementById('edit-boost-days').value = button.getAttribute('data-days');
                document.getElementById('edit-boost-status').value = button.getAttribute('data-status');
                document.getElementById('edit-boost-impressions').value = button.getAttribute('data-impressions');
                document.getElementById('edit-boost-clicks').value = button.getAttribute('data-clicks');
                document.getElementById('edit-boost-enquiries').value = button.getAttribute('data-enquiries');
                document.getElementById('edit-boost-conversions').value = button.getAttribute('data-conversions');
                document.getElementById('edit-boost-revenue').value = button.getAttribute('data-revenue');
            });
        }

        // ========== METRICS & CHARTS ==========
        const boosts = @json($settingsData['boosts'] ?? []);
        updateMetrics(boosts);
        renderROIChart(boosts);
        renderRevenueChart();
    });

    function updateMetrics(boosts) {
        let totalRevenue = 0, totalAmount = 0, active = 0;
        boosts.forEach(b => {
            totalRevenue += parseFloat(b.revenue_generated || 0);
            totalAmount += parseFloat(b.boost_amount || 0);
            if (b.status === 'active') active++;
        });
        const revEl = document.getElementById('boost-total-revenue');
        const amtEl = document.getElementById('boost-total-amount');
        if(revEl) { revEl.setAttribute('data-amount', totalRevenue); }
        if(amtEl) { amtEl.setAttribute('data-amount', totalAmount); }
        
        formatLocalMoneyElements();

        const roi = totalAmount > 0 ? (totalRevenue / totalAmount) : 0;
        const roiEl = document.getElementById('boost-roi');
        if(roiEl) roiEl.innerHTML = roi.toFixed(2) + 'x';
        const activeEl = document.getElementById('boost-active-count');
        if(activeEl) activeEl.innerHTML = active;
    }

    function renderROIChart(boosts) {
        const last10 = boosts.slice(-10);
        const labels = last10.map((_, i) => 'Boost ' + (i+1));
        const values = last10.map(b => {
            const amt = parseFloat(b.boost_amount || 0);
            const rev = parseFloat(b.revenue_generated || 0);
            return amt > 0 ? (rev / amt) : 0;
        });
        const canvas = document.getElementById('roiTrackingChart');
        if(!canvas) return;
        const ctx = canvas.getContext('2d');
        if (window.roiChartInstance) window.roiChartInstance.destroy();
        window.roiChartInstance = new Chart(ctx, {
            type: 'bar',
            data: { labels, datasets: [{ label: 'ROI (x)', data: values, backgroundColor: '#3b82f6', borderRadius: 4 }] },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#e2e8f0' } }, x: { grid: { display: false } } } }
        });
    }

    function renderRevenueChart() {
        const canvas = document.getElementById('revenueTrendsChart');
        if(!canvas) return;
        const ctx = canvas.getContext('2d');
        if (window.revenueChartInstance) window.revenueChartInstance.destroy();
        window.revenueChartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{ label: 'Revenue', data: [1200, 1900, 3000, 5000, 4000, 6000], borderColor: '#1e3a8a', backgroundColor: 'rgba(30, 58, 138, 0.1)', fill: true, tension: 0.4, borderWidth: 3, pointBackgroundColor: '#ffffff', pointBorderColor: '#1e3a8a', pointBorderWidth: 2, pointRadius: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, grid: { borderDash: [2, 4], color: '#e2e8f0' } }, x: { grid: { display: false } } } }
        });
    }

    // ========== AJAX TOGGLE BOOST STATUS ==========
    document.querySelectorAll('.toggle-boost-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const url = this.getAttribute('action');
            const token = this.querySelector('input[name="_token"]').value;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const urlParts = url.split('/');
                    const id = urlParts[urlParts.length - 1];
                    const badge = document.getElementById('status-badge-' + id);
                    if(badge) {
                        badge.innerText = data.status;
                        badge.className = 'status-badge status-' + data.status.toLowerCase();
                    }
                    if(typeof showToast === 'function') {
                        showToast("Success", data.message);
                    }
                }
            })
            .catch(err => console.error("Error toggling status", err));
        });
    });
</script>
@endsection