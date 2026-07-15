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
        <button class="master-tab-btn" data-tab="data-control">
            <i class='bx bx-data'></i> Data Control
        </button>
        <button class="master-tab-btn" data-tab="fraud-protection">
            <i class='bx bx-shield-x'></i> Fraud Protection
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
                    <ul class="nav nav-tabs w-100" id="revenueEngineSubTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="subtab-lead-revenue-btn" data-bs-toggle="tab" data-bs-target="#subtab-lead-revenue" type="button" role="tab" aria-controls="subtab-lead-revenue" aria-selected="true"><i class="bx bx-wallet"></i> Total Lead Revenue</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subtab-seller-revenue-btn" data-bs-toggle="tab" data-bs-target="#subtab-seller-revenue" type="button" role="tab" aria-controls="subtab-seller-revenue" aria-selected="false"><i class="bx bx-group"></i> Revenue Per Seller</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subtab-category-revenue-btn" data-bs-toggle="tab" data-bs-target="#subtab-category-revenue" type="button" role="tab" aria-controls="subtab-category-revenue" aria-selected="false"><i class="bx bx-category"></i> Revenue Per Category</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="subtab-app-revenue-btn" data-bs-toggle="tab" data-bs-target="#subtab-app-revenue" type="button" role="tab" aria-controls="subtab-app-revenue" aria-selected="false"><i class="bx bx-mobile-alt"></i> Revenue Per App</button>
                        </li>
                    </ul>
                </div>
                <div class="section-body p-3">
                    <div class="tab-content" id="revenueEngineSubTabContent">
                        <!-- Sub-tab 1: Total Lead Revenue Table -->
                        <div class="tab-pane fade show active" id="subtab-lead-revenue" role="tabpanel" aria-labelledby="subtab-lead-revenue-btn">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Campaign Name</th>
                                            <th>Seller</th>
                                            <th>Boost Amount</th>
                                            <th>Revenue Generated</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($settingsData['revenue']['transactions'] ?? [] as $t)
                                            <tr>
                                                <td>{{ $t['date'] }}</td>
                                                <td>{{ $t['campaign'] }}</td>
                                                <td>{{ $t['seller'] }}</td>
                                                <td class="money text-danger" data-amount="{{ $t['spent'] }}">₹{{ number_format($t['spent'], 2) }}</td>
                                                <td class="money text-success" data-amount="{{ $t['revenue'] }}">₹{{ number_format($t['revenue'], 2) }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $t['status'] === 'active' ? 'success' : 'secondary' }}">
                                                        {{ ucfirst($t['status']) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center">No transactions available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Sub-tab 2: Revenue Per Seller Table -->
                        <div class="tab-pane fade" id="subtab-seller-revenue" role="tabpanel" aria-labelledby="subtab-seller-revenue-btn">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Seller Name</th>
                                            <th>Total Boost Spent</th>
                                            <th>Total Revenue Generated</th>
                                            <th>ROI</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($settingsData['revenue']['sellers'] ?? [] as $s)
                                            <tr>
                                                <td class="fw-bold">{{ $s['name'] }}</td>
                                                <td class="money text-danger" data-amount="{{ $s['spent'] }}">₹{{ number_format($s['spent'], 2) }}</td>
                                                <td class="money text-success" data-amount="{{ $s['revenue'] }}">₹{{ number_format($s['revenue'], 2) }}</td>
                                                <td class="fw-bold text-info">{{ $s['roi'] }}x</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No seller analytics available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Sub-tab 3: Revenue Per Category Table -->
                        <div class="tab-pane fade" id="subtab-category-revenue" role="tabpanel" aria-labelledby="subtab-category-revenue-btn">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Category Name</th>
                                            <th>Total Revenue Generated</th>
                                            <th>Share Percentage</th>
                                            <th>Trend</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($settingsData['revenue']['categories'] ?? [] as $c)
                                            <tr>
                                                <td class="fw-bold">{{ $c['name'] }}</td>
                                                <td class="money text-success" data-amount="{{ $c['revenue'] }}">₹{{ number_format($c['revenue'], 2) }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <span class="me-2">{{ $c['percent'] }}%</span>
                                                        <div class="progress w-100" style="height: 6px;">
                                                            <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $c['percent'] }}%" aria-valuenow="{{ $c['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light-{{ $c['trend'] === 'up' ? 'success text-success' : 'danger text-danger' }}">
                                                        <i class="bx bx-trending-{{ $c['trend'] }}"></i> {{ ucfirst($c['trend']) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No category analytics available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Sub-tab 4: Revenue Per App Table -->
                        <div class="tab-pane fade" id="subtab-app-revenue" role="tabpanel" aria-labelledby="subtab-app-revenue-btn">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>App / Channel Name</th>
                                            <th>Platform</th>
                                            <th>Commission Revenue</th>
                                            <th>Share Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($settingsData['revenue']['apps'] ?? [] as $a)
                                            <tr>
                                                <td class="fw-bold"><i class="bx bx-devices"></i> {{ $a['name'] }}</td>
                                                <td>{{ $a['platform'] }}</td>
                                                <td class="money text-success" data-amount="{{ $a['revenue'] }}">₹{{ number_format($a['revenue'], 2) }}</td>
                                                <td class="fw-bold text-primary">{{ $a['share'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">No platform analytics available.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== BOOST REVENUE ENGINE TAB ========== -->
        <div class="tab-pane active" id="tab-boost-revenue">
            <div class="metrics-grid">
                <div class="metric-card card-success">
                    <div class="metric-icon-box"><i class='bx bx-money-withdraw'></i></div>
                    <div class="metric-info">
                        <div class="metric-value money" id="boost-total-amount" data-amount="0">₹0</div>
                        <div class="metric-label">Total Boost Revenue</div>
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
                    <h2 class="section-title"><i class='bx bx-plus-circle'></i> Add New Boost</h2>
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
                                <th>Product</th>
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
                                        <div class="d-flex align-items-center gap-3">
                                            @if($product && $product->thumbnail)
                                                <img src="{{ asset($product->thumbnail) }}" alt="{{ $product->name }}" class="rounded" style="width: 48px; height: 48px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                    <i class="bx bx-image text-muted fs-4"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-bold text-dark">{{ $product->name ?? 'All Products' }}</div>
                                                <div class="fs-12 text-muted">{{ $seller->name ?? 'Global' }} &bull; {{ $category->name ?? 'Any Category' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="fw-semibold text-success"><span class="money" data-amount="{{ $boost['per_day'] ?? 0 }}">₹{{ number_format($boost['per_day'] ?? 0, 2) }}</span></td>
                                    <td>{{ $boost['days'] ?? 1 }}</td>
                                    <td class="fw-semibold"><span class="money" data-amount="{{ $boost['boost_amount'] }}">₹{{ number_format($boost['boost_amount'], 2) }}</span></td>
                                    <td>
                                        <span class="status-badge status-{{ strtolower($boost['status']) }}" id="status-badge-{{ $boost['id'] }}">{{ $boost['status'] }}</span>
                                        @if($boost['is_suggestion'] ?? false)
                                            <span class="badge bg-warning ms-1" style="font-size:0.7em;">Suggestion</span>
                                        @endif
                                    </td>
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

            <!-- Top Spending Sellers -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-trophy'></i> Top Spending Sellers</h2>
                </div>
                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Seller Name</th>
                                <th>Active Campaigns</th>
                                <th>Total Boost Revenue</th>
                                <th>Total Revenue Generated</th>
                                <th>Avg ROI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $topSellers = collect($settingsData['boosts'] ?? [])
                                    ->groupBy('seller_id')
                                    ->map(function($boosts) use ($settingsData) {
                                        $sellerId = $boosts->first()['seller_id'];
                                        $sellerName = $sellerId ? ($settingsData['sellers']->firstWhere('id', $sellerId)->name ?? 'Unknown') : 'Global Admin';
                                        return [
                                            'name' => $sellerName,
                                            'campaigns' => $boosts->count(),
                                            'spend' => $boosts->sum('boost_amount'),
                                            'revenue' => $boosts->sum('revenue_generated'),
                                            'roi' => $boosts->sum('boost_amount') > 0 ? round($boosts->sum('revenue_generated') / $boosts->sum('boost_amount'), 2) : 0
                                        ];
                                    })
                                    ->sortByDesc('spend')
                                    ->take(5);
                            @endphp

                            @forelse($topSellers as $seller)
                                <tr>
                                    <td class="fw-bold">{{ $seller['name'] }}</td>
                                    <td>{{ $seller['campaigns'] }}</td>
                                    <td class="money text-danger fw-semibold" data-amount="{{ $seller['spend'] }}">₹{{ number_format($seller['spend'], 2) }}</td>
                                    <td class="money text-success fw-bold" data-amount="{{ $seller['revenue'] }}">₹{{ number_format($seller['revenue'], 2) }}</td>
                                    <td class="text-info fw-bold">{{ $seller['roi'] }}x</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No data available for top spending sellers.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Campaign Revenue -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-line-chart'></i> Campaign Revenue</h2>
                </div>
                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Campaign / Target</th>
                                <th>Seller</th>
                                <th>Spend</th>
                                <th>Revenue</th>
                                <th>Performance Indicator</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $campaignRevenues = collect($settingsData['boosts'] ?? [])
                                    ->sortByDesc('revenue_generated')
                                    ->take(5);
                            @endphp

                            @forelse($campaignRevenues as $campaign)
                                @php
                                    $seller = $settingsData['sellers']->firstWhere('id', $campaign['seller_id']);
                                    $product = $settingsData['products']->firstWhere('id', $campaign['product_id']);
                                    $roi = $campaign['boost_amount'] > 0 ? ($campaign['revenue_generated'] / $campaign['boost_amount']) * 100 : 0;
                                @endphp
                                <tr>
                                    <td class="fw-bold">{{ $product->name ?? 'All Products/Category' }}</td>
                                    <td class="text-muted">{{ $seller->name ?? 'Global' }}</td>
                                    <td class="money text-danger" data-amount="{{ $campaign['boost_amount'] }}">₹{{ number_format($campaign['boost_amount'], 2) }}</td>
                                    <td class="money text-success fw-bold" data-amount="{{ $campaign['revenue_generated'] }}">₹{{ number_format($campaign['revenue_generated'], 2) }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2 text-dark fw-semibold">{{ round($roi, 0) }}%</span>
                                            <div class="progress w-100" style="height: 6px;">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ min(100, $roi) }}%" aria-valuenow="{{ min(100, $roi) }}" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No campaign revenue data available.</td>
                                </tr>
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

        <!-- ========== DATA CONTROL TAB ========== -->
        <div class="tab-pane" id="tab-data-control">

            <!-- Stats Row -->
            <div class="metrics-grid">
                <div class="metric-card card-primary">
                    <div class="metric-icon-box"><i class='bx bx-download'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="dc-export-count">5</div>
                        <div class="metric-label">Export Types</div>
                        <div class="metric-badge" style="background:rgba(59,130,246,0.1);color:#3b82f6">CSV Ready</div>
                    </div>
                </div>
                <div class="metric-card card-success">
                    <div class="metric-icon-box"><i class='bx bx-server'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="dc-backup-count">0</div>
                        <div class="metric-label">Backups Available</div>
                        <div class="metric-badge" style="background:rgba(16,185,129,0.1);color:#10b981">Local Storage</div>
                    </div>
                </div>
                <div class="metric-card card-warning">
                    <div class="metric-icon-box"><i class='bx bx-user-x'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="dc-deletion-count">0</div>
                        <div class="metric-label">Deletion Requests</div>
                        <div class="metric-badge" style="background:rgba(245,158,11,0.1);color:#f59e0b">Pending</div>
                    </div>
                </div>
                <div class="metric-card card-info">
                    <div class="metric-icon-box"><i class='bx bx-time'></i></div>
                    <div class="metric-info">
                        <div class="metric-value">Auto</div>
                        <div class="metric-label">Retention Policy</div>
                        <div class="metric-badge" style="background:rgba(14,165,233,0.1);color:#0ea5e9">Configured</div>
                    </div>
                </div>
            </div>

            <!-- Feature 1: Data Export -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-export'></i> Export Data</h2>
                    <span class="status-badge status-active">100% Free</span>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-4">Download complete data as CSV files. All data is exported in real-time from the database.</p>
                    <div class="row g-3">
                        @foreach([['sellers','Sellers','bx-group','Export all seller profiles, contact info and status.'],['products','Products','bx-box','Export all product listings with pricing and stock.'],['orders','Orders','bx-cart','Export all orders with payment and delivery status.'],['customers','Customers','bx-user','Export all customer accounts and activity.'],['enquiries','Enquiries','bx-message-square-detail','Export all product enquiries and leads.']] as [$type, $label, $icon, $desc])
                        <div class="col-md-4">
                            <div class="p-4 border rounded-3 d-flex gap-3 align-items-start" style="background:#f8fafc;">
                                <div style="width:44px;height:44px;border-radius:10px;background:rgba(59,130,246,0.1);color:#3b82f6;display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0;"><i class="bx {{ $icon }}"></i></div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold text-dark mb-1">{{ $label }}</div>
                                    <div class="text-muted fs-12 mb-3">{{ $desc }}</div>
                                    <a href="{{ route('admin.settings.data.export') }}?type={{ $type }}" class="btn btn-sm btn-primary fw-semibold" style="border-radius:6px;">
                                        <i class="bx bx-download me-1"></i> Download CSV
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Feature 2: Data Retention -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-time-five'></i> Data Retention Policy</h2>
                    <span class="status-badge status-active">Auto CRON</span>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-4">Set how long different types of data should be kept. Old data beyond the limit will be automatically cleaned up by scheduled jobs.</p>
                    <form action="{{ route('admin.settings.data.retention.save') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label">Activity Logs Retention (Days)</label>
                                <input type="number" class="form-control" name="logs_retain_days" value="365" min="30" max="3650">
                                <div class="text-muted fs-12 mt-1"><i class='bx bx-info-circle'></i> Seller activity logs older than this will be deleted.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Enquiries Retention (Days)</label>
                                <input type="number" class="form-control" name="enquiries_retain_days" value="365" min="30" max="3650">
                                <div class="text-muted fs-12 mt-1"><i class='bx bx-info-circle'></i> Archived enquiries older than this will be removed.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Fraud Logs Retention (Days)</label>
                                <input type="number" class="form-control" name="fraud_logs_retain_days" value="180" min="30" max="3650">
                                <div class="text-muted fs-12 mt-1"><i class='bx bx-info-circle'></i> Fraud detection records older than this will be purged.</div>
                            </div>
                        </div>
                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn-primary-custom"><i class="bx bx-save"></i> Save Settings</button>
                            <button type="button" id="run-retention-btn" class="btn btn-warning fw-semibold" style="border-radius:8px;padding:10px 20px;">
                                <i class="bx bx-trash"></i> Run Cleanup Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Feature 3: User Data Deletion -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-user-x'></i> User Data Deletion (GDPR)</h2>
                    <span class="status-badge status-rule">Privacy Compliance</span>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-4">Users who request account deletion are listed here. Approving will anonymize their personal data while preserving transaction records for audit purposes.</p>
                    <div class="table-container">
                        <table class="table" id="deletion-requests-table">
                            <thead>
                                <tr>
                                    <th>#</th><th>User Name</th><th>Email</th><th>Requested On</th><th>Status</th><th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="deletion-tbody">
                                <tr><td colspan="6" class="text-center text-muted py-4"><i class='bx bx-loader-alt bx-spin me-2'></i>Loading requests...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Feature 4: Backup Management -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-cloud-upload'></i> Backup Management</h2>
                    <button id="create-backup-btn" class="btn-primary-custom" style="font-size:0.85rem;padding:8px 16px;">
                        <i class="bx bx-plus"></i> Create Backup Now
                    </button>
                </div>
                <div class="section-body">
                    <div id="backup-status" class="mb-3"></div>
                    <p class="text-muted small mb-4">Database backups are stored on the server. Download or delete old backups from here. <strong>Note:</strong> For production, configure automated cloud backups.</p>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr><th>Filename</th><th>Size</th><th>Created</th><th class="text-end">Actions</th></tr>
                            </thead>
                            <tbody id="backup-list-tbody">
                                <tr><td colspan="4" class="text-center text-muted py-4">No backups found. Click "Create Backup Now" to generate one.</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ========== FRAUD PROTECTION TAB ========== -->
        <div class="tab-pane" id="tab-fraud-protection">

            <!-- Stats Row -->
            <div class="metrics-grid" id="fraud-stats-grid">
                <div class="metric-card card-danger">
                    <div class="metric-icon-box"><i class='bx bx-bug'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="stat-total-fraud">--</div>
                        <div class="metric-label">Total Fraud Records</div>
                        <div class="metric-badge" style="background:rgba(239,68,68,0.1);color:#ef4444">All Time</div>
                    </div>
                </div>
                <div class="metric-card card-warning">
                    <div class="metric-icon-box"><i class='bx bx-calendar-exclamation'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="stat-fraud-month">--</div>
                        <div class="metric-label">This Month</div>
                        <div class="metric-badge" style="background:rgba(245,158,11,0.1);color:#f59e0b">30 Days</div>
                    </div>
                </div>
                <div class="metric-card card-dark">
                    <div class="metric-icon-box"><i class='bx bx-block'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="stat-blocked-ips">--</div>
                        <div class="metric-label">Blocked IPs</div>
                        <div class="metric-badge" style="background:rgba(15,23,42,0.1);color:#0f172a">Active</div>
                    </div>
                </div>
                <div class="metric-card card-info">
                    <div class="metric-icon-box"><i class='bx bx-error-circle'></i></div>
                    <div class="metric-info">
                        <div class="metric-value" id="stat-suspicious">--</div>
                        <div class="metric-label">Suspicious Alerts</div>
                        <div class="metric-badge" style="background:rgba(14,165,233,0.1);color:#0ea5e9">Live</div>
                    </div>
                </div>
            </div>

            <!-- Fraud Settings -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-slider'></i> Fraud Detection Thresholds</h2>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-4">Configure the sensitivity of fraud detection. Lower values = more strict detection.</p>
                    <div class="row g-3" id="fraud-settings-form">
                        <div class="col-md-4">
                            <label class="form-label">High Fraud Seller: Cancel Rate % Threshold</label>
                            <input type="number" class="form-control" id="fraud-cancel-threshold" value="30" min="1" max="100">
                            <div class="text-muted fs-12 mt-1">Sellers with cancel/return rate above this % will be flagged.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Suspicious Click: Threshold per Day</label>
                            <input type="number" class="form-control" id="fraud-click-threshold" value="20" min="1" max="1000">
                            <div class="text-muted fs-12 mt-1">IPs with more than this many clicks per day are flagged.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Spam Enquiry: Threshold per Day per IP</label>
                            <input type="number" class="form-control" id="fraud-enquiry-threshold" value="5" min="1" max="500">
                            <div class="text-muted fs-12 mt-1">IPs sending more enquiries than this per day are flagged.</div>
                        </div>
                        <div class="col-12">
                            <button id="save-fraud-settings-btn" class="btn-primary-custom">
                                <i class="bx bx-save"></i> Save Fraud Settings
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature 5: High Fraud Sellers -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-user-check'></i> High Fraud Seller Alert</h2>
                    <span class="status-badge status-rule">Auto Detected</span>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-3">Sellers with abnormally high cancel/return rates in the last 30 days.</p>
                    <div class="table-container">
                        <table class="table">
                            <thead><tr><th>Seller</th><th>Email</th><th>Total Orders</th><th>Bad Orders</th><th>Cancel Rate</th><th>Action</th></tr></thead>
                            <tbody id="fraud-sellers-tbody"><tr><td colspan="6" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i> Loading...</td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Feature 6 & 10: Suspicious IPs + IP Blocker -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-mouse-alt'></i> Suspicious Click Patterns & IP Management</h2>
                    <span class="status-badge status-active">Live</span>
                </div>
                <div class="section-body">
                    <div class="row g-3 mb-4">
                        <div class="col-md-8">
                            <label class="form-label">Manually Block an IP Address</label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="manual-block-ip" placeholder="e.g. 192.168.1.100">
                                <input type="text" class="form-control" id="block-reason" placeholder="Reason (optional)">
                                <button class="btn btn-danger" id="block-ip-btn"><i class="bx bx-block"></i> Block IP</button>
                            </div>
                        </div>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-danger"><i class='bx bx-trending-up me-1'></i>Suspicious Click IPs (Last 24h)</h6>
                            <div class="table-container">
                                <table class="table table-sm">
                                    <thead><tr><th>IP Address</th><th>Click Count</th><th>Last Seen</th><th>Action</th></tr></thead>
                                    <tbody id="suspicious-ips-tbody"><tr><td colspan="4" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-dark"><i class='bx bx-block me-1'></i>Currently Blocked IPs</h6>
                            <div class="table-container">
                                <table class="table table-sm">
                                    <thead><tr><th>IP</th><th>Reason</th><th>Blocked On</th><th>Action</th></tr></thead>
                                    <tbody id="blocked-ips-tbody"><tr><td colspan="4" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature 7: Duplicate Sellers -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-copy-alt'></i> Duplicate Seller Detection</h2>
                    <span class="status-badge status-rule">PAN / Bank Match</span>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-3">Sellers sharing the same PAN number or bank account, which may indicate duplicate/fake registrations.</p>
                    <div class="table-container">
                        <table class="table">
                            <thead><tr><th>Seller</th><th>Email</th><th>Duplicate Of</th><th>Reason</th></tr></thead>
                            <tbody id="duplicate-sellers-tbody"><tr><td colspan="4" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Duplicate Product Upload Detection -->
            <div class="section-card">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-copy'></i> Duplicate Product Upload Detection</h2>
                    <span class="status-badge status-rule">Title & Desc Match</span>
                </div>
                <div class="section-body">
                    <p class="text-muted small mb-3">Products that share extremely high similarity in name, description, or price. Uploading duplicates is restricted to sellers.</p>
                    <div class="table-container">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product A (Seller)</th>
                                    <th>Product B (Seller)</th>
                                    <th>Similarity</th>
                                    <th>Type</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="duplicate-products-tbody">
                                <tr><td colspan="5" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i> Loading...</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Feature 8 & 9: Abnormal Conversions + Spam Enquiries -->
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="section-card mb-0">
                        <div class="section-header">
                            <h2 class="section-title" style="font-size:1rem;"><i class='bx bx-trending-up'></i> Abnormal Conversion Detection</h2>
                        </div>
                        <div class="section-body">
                            <p class="text-muted fs-12 mb-3">Products with 90%+ or 0% conversion rate (out of 20+ enquiries) in last 30 days.</p>
                            <div class="table-container">
                                <table class="table table-sm">
                                    <thead><tr><th>Product</th><th>Enquiries</th><th>Orders</th><th>Rate</th></tr></thead>
                                    <tbody id="abnormal-tbody"><tr><td colspan="4" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-card mb-0">
                        <div class="section-header">
                            <h2 class="section-title" style="font-size:1rem;"><i class='bx bx-envelope-open'></i> Fake Enquiry / Spam Lead Detection</h2>
                        </div>
                        <div class="section-body">
                            <p class="text-muted fs-12 mb-3">IPs that sent multiple enquiries in the last 24 hours (potential spam bots).</p>
                            <div class="table-container">
                                <table class="table table-sm">
                                    <thead><tr><th>IP Address</th><th>Count</th><th>Last Time</th><th>Action</th></tr></thead>
                                    <tbody id="spam-enquiry-tbody"><tr><td colspan="4" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature 11: Duplicate & Fraud Customer Detection -->
            <div class="section-card mt-4">
                <div class="section-header">
                    <h2 class="section-title"><i class='bx bx-user-pin'></i> Duplicate & Fraud Customer Detection</h2>
                    <span class="status-badge status-rule">High Risk / Duplicates</span>
                </div>
                <div class="section-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-danger"><i class='bx bx-error me-1'></i>High Risk Customers</h6>
                            <p class="text-muted fs-12 mb-3">Customers flagged with High or Critical risk level.</p>
                            <div class="table-container">
                                <table class="table table-sm">
                                    <thead><tr><th>Name</th><th>Email / Phone</th><th>Risk Level</th></tr></thead>
                                    <tbody id="high-risk-customers-tbody"><tr><td colspan="3" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6 class="fw-bold mb-3 text-warning"><i class='bx bx-copy me-1'></i>Duplicate Customers</h6>
                            <p class="text-muted fs-12 mb-3">Different accounts sharing the same email or phone number.</p>
                            <div class="table-container">
                                <table class="table table-sm">
                                    <thead><tr><th>Account 1</th><th>Account 2</th><th>Shared Info</th></tr></thead>
                                    <tbody id="duplicate-customers-tbody"><tr><td colspan="3" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature 12 & 13: Payment Fraud + Invoice Validation -->
            <div class="row g-4 mt-2">
                <div class="col-md-6">
                    <div class="section-card mb-0">
                        <div class="section-header">
                            <h2 class="section-title" style="font-size:1rem;"><i class='bx bx-credit-card-front'></i> Payment Fraud Alerts</h2>
                        </div>
                        <div class="section-body">
                            <p class="text-muted fs-12 mb-3">Orders with 3 or more failed payment attempts (last 30 days).</p>
                            <div class="table-container">
                                <table class="table table-sm">
                                    <thead><tr><th>Order ID</th><th>Customer</th><th>Amount</th><th>Attempts</th></tr></thead>
                                    <tbody id="payment-fraud-tbody"><tr><td colspan="4" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="section-card mb-0">
                        <div class="section-header">
                            <h2 class="section-title" style="font-size:1rem;"><i class='bx bx-receipt'></i> Invoice Validation Anomalies</h2>
                        </div>
                        <div class="section-body">
                            <p class="text-muted fs-12 mb-3">Completed orders missing invoices or having amount mismatches.</p>
                            <div class="table-container">
                                <table class="table table-sm">
                                    <thead><tr><th>Order ID</th><th>Status</th><th>Order Amt</th><th>Invoice Amt</th></tr></thead>
                                    <tbody id="invoice-validation-tbody"><tr><td colspan="4" class="text-center text-muted py-3"><i class='bx bx-loader-alt bx-spin'></i></td></tr></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
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

    // Check URL for tab parameter
    document.addEventListener('DOMContentLoaded', () => {
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) {
            const tabBtn = document.querySelector(`.master-tab-btn[data-tab="${tabParam}-revenue"]`) || document.querySelector(`.master-tab-btn[data-tab="${tabParam}"]`);
            if (tabBtn) tabBtn.click();
        }
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
                let action = form.getAttribute('data-original-action');
                if (!action) {
                    action = form.getAttribute('action');
                    form.setAttribute('data-original-action', action);
                }
                form.setAttribute('action', action.replace('__ID__', id));


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

    // ========== DATA CONTROL: BACKUP MANAGEMENT ==========
    const backupCreateUrl  = '{{ route("admin.settings.backup.create") }}';
    const retentionRunUrl  = '{{ route("admin.settings.data.retention.run") }}';
    const fraudDashUrl     = '{{ route("admin.settings.fraud.dashboard") }}';
    const blockIpUrl       = '{{ route("admin.settings.fraud.block.ip") }}';
    const unblockIpUrl     = '{{ route("admin.settings.fraud.unblock.ip") }}';
    const fraudSaveUrl     = '{{ route("admin.settings.fraud.settings.save") }}';
    const csrfToken        = '{{ csrf_token() }}';

    function showAlert(msg, type = 'success') {
        const el = document.createElement('div');
        el.className = `alert alert-${type} alert-dismissible fade show`;
        el.style.cssText = 'position:fixed;top:80px;right:20px;z-index:9999;min-width:300px;border-radius:12px;box-shadow:0 8px 24px rgba(0,0,0,0.15);';
        el.innerHTML = `<i class="bx bx-${type === 'success' ? 'check-circle' : 'error-circle'} me-2"></i>${msg}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.body.appendChild(el);
        setTimeout(() => el.remove(), 4000);
    }

    // Backup: Create
    document.getElementById('create-backup-btn')?.addEventListener('click', function () {
        this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Creating...';
        this.disabled = true;
        const btn = this;
        fetch(backupCreateUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = '<i class="bx bx-plus"></i> Create Backup Now';
            btn.disabled = false;
            if (data.success) {
                showAlert(data.message);
                const tbody = document.getElementById('backup-list-tbody');
                const row = `<tr id="bk-${data.filename}"><td><i class='bx bx-file-blank me-1 text-muted'></i>${data.filename}</td><td>${data.size}</td><td>${data.date}</td><td class="text-end"><button onclick="deleteBackup('${data.filename}')" class="btn btn-sm btn-light border"><i class="bx bx-trash text-danger"></i></button></td></tr>`;
                if (tbody.querySelector('td[colspan]')) tbody.innerHTML = '';
                tbody.insertAdjacentHTML('afterbegin', row);
                document.getElementById('dc-backup-count').innerText = tbody.querySelectorAll('tr').length;
            } else { showAlert(data.message, 'danger'); }
        })
        .catch(() => { btn.innerHTML = '<i class="bx bx-plus"></i> Create Backup Now'; btn.disabled = false; showAlert('Failed to create backup.', 'danger'); });
    });

    function deleteBackup(filename) {
        if (!confirm('Delete this backup?')) return;
        fetch('{{ url("admin/settings/backup/delete") }}/' + filename, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('bk-' + filename)?.remove();
                showAlert('Backup deleted.');
            } else { showAlert(data.message, 'danger'); }
        });
    }

    // Data Retention: Run Now
    document.getElementById('run-retention-btn')?.addEventListener('click', function () {
        if (!confirm('Run data cleanup now? This will permanently delete old records.')) return;
        this.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i> Running...';
        this.disabled = true;
        const btn = this;
        fetch(retentionRunUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            btn.innerHTML = '<i class="bx bx-trash"></i> Run Cleanup Now';
            btn.disabled = false;
            showAlert(data.message, data.success ? 'success' : 'danger');
        })
        .catch(() => { btn.innerHTML = '<i class="bx bx-trash"></i> Run Cleanup Now'; btn.disabled = false; });
    });

    // User Deletion Requests: Load
    function loadDeletionRequests() {
        fetch('{{ url("admin/settings/system/deletion") }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken }
        })
        .then(r => r.json())
        .then(data => {
            const tbody = document.getElementById('deletion-tbody');
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4"><i class="bx bx-check-circle text-success me-1"></i>No pending deletion requests.</td></tr>';
                return;
            }
            tbody.innerHTML = data.map((req, i) => `
                <tr>
                    <td>${i + 1}</td>
                    <td>${req.customer_name || 'Unknown'}</td>
                    <td>${req.customer_email || '-'}</td>
                    <td>${req.created_at || '-'}</td>
                    <td><span class="status-badge ${req.status === 'pending' ? 'status-rule' : req.status === 'approved' ? 'status-active' : 'status-inactive'}">${req.status}</span></td>
                    <td class="text-end">
                        ${req.status === 'pending' ? `
                        <button onclick="processDeletion(${req.id}, 'approve')" class="btn btn-sm btn-success me-1">Approve</button>
                        <button onclick="processDeletion(${req.id}, 'reject')" class="btn btn-sm btn-light border">Reject</button>` : '-'}
                    </td>
                </tr>`).join('');
            document.getElementById('dc-deletion-count').innerText = data.filter(r => r.status === 'pending').length;
        })
        .catch(() => {
            document.getElementById('deletion-tbody').innerHTML = '<tr><td colspan="6" class="text-center text-muted py-4">Unable to load requests.</td></tr>';
        });
    }

    function processDeletion(id, action) {
        if (!confirm(action === 'approve' ? 'This will anonymize user data. Continue?' : 'Reject this request?')) return;
        fetch(`{{ url('admin/settings/user-deletion/process') }}/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ action })
        })
        .then(r => r.json())
        .then(data => { showAlert(data.message, data.success ? 'success' : 'danger'); loadDeletionRequests(); });
    }

    // ========== FRAUD PROTECTION DASHBOARD ==========
    function loadFraudDashboard() {
        fetch(fraudDashUrl, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } })
        .then(r => r.json())
        .then(data => {
            if (!data.success) { showAlert(data.message, 'danger'); return; }

            // Stats
            document.getElementById('stat-total-fraud').innerText = data.stats.total_fraud;
            document.getElementById('stat-fraud-month').innerText = data.stats.fraud_this_month;
            document.getElementById('stat-blocked-ips').innerText = data.stats.blocked_ips;
            document.getElementById('stat-suspicious').innerText = data.stats.suspicious_alerts;

            // Feature 5: High Fraud Sellers
            const sellersTbody = document.getElementById('fraud-sellers-tbody');
            sellersTbody.innerHTML = data.high_fraud_sellers.length === 0
                ? '<tr><td colspan="6" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No high-fraud sellers detected.</td></tr>'
                : data.high_fraud_sellers.map(s => `<tr>
                    <td><div class="fw-bold">${s.name}</div></td>
                    <td class="text-muted">${s.email}</td>
                    <td>${s.total_orders}</td>
                    <td class="text-danger">${s.bad_orders}</td>
                    <td><span class="status-badge ${s.cancel_rate > 50 ? 'bg-danger text-white' : 'status-rule'}">${s.cancel_rate}%</span></td>
                    <td><a href="/admin/sellers/${s.id}" class="btn btn-sm btn-light border">View</a></td>
                </tr>`).join('');

            // Feature 6: Suspicious IPs
            const sipTbody = document.getElementById('suspicious-ips-tbody');
            sipTbody.innerHTML = data.suspicious_ips.length === 0
                ? '<tr><td colspan="4" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No suspicious clicks.</td></tr>'
                : data.suspicious_ips.map(ip => `<tr>
                    <td><code>${ip.customer_ip}</code></td>
                    <td><span class="fw-bold text-danger">${ip.click_count}</span></td>
                    <td class="text-muted fs-12">${ip.last_seen}</td>
                    <td><button onclick="blockIp('${ip.customer_ip}', 'Suspicious clicks')" class="btn btn-sm btn-danger"><i class="bx bx-block"></i></button></td>
                </tr>`).join('');

            // Feature 7: Duplicate Sellers
            const dupTbody = document.getElementById('duplicate-sellers-tbody');
            dupTbody.innerHTML = data.duplicate_sellers.length === 0
                ? '<tr><td colspan="4" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No duplicate sellers found.</td></tr>'
                : data.duplicate_sellers.map(s => `<tr>
                    <td>${s.name} <span class="text-muted">#${s.id}</span></td>
                    <td>${s.email}</td>
                    <td>${s.duplicate_name} <span class="text-muted">#${s.duplicate_id}</span></td>
                    <td><span class="status-badge status-rule">${s.reason}</span></td>
                </tr>`).join('');

            // Feature 8: Abnormal Conversions
            const abnTbody = document.getElementById('abnormal-tbody');
            abnTbody.innerHTML = data.abnormal_conversions.length === 0
                ? '<tr><td colspan="4" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No abnormal conversions.</td></tr>'
                : data.abnormal_conversions.map(p => `<tr>
                    <td>${p.name}</td>
                    <td>${p.enquiries}</td>
                    <td>${p.orders}</td>
                    <td><span class="status-badge ${p.conversion_rate >= 90 ? 'bg-danger text-white' : 'status-inactive'}">${p.conversion_rate ?? 0}%</span></td>
                </tr>`).join('');

            // Feature 9: Spam Enquiries
            const spamTbody = document.getElementById('spam-enquiry-tbody');
            spamTbody.innerHTML = data.fake_enquiries.length === 0
                ? '<tr><td colspan="4" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No spam detected.</td></tr>'
                : data.fake_enquiries.map(e => `<tr>
                    <td><code>${e.ip_address}</code></td>
                    <td class="fw-bold text-warning">${e.count}</td>
                    <td class="text-muted fs-12">${e.last_time}</td>
                    <td><button onclick="blockIp('${e.ip_address}', 'Spam enquiries')" class="btn btn-sm btn-warning"><i class="bx bx-block"></i></button></td>
                </tr>`).join('');

            // Feature 11: Customer Fraud
            const riskCustTbody = document.getElementById('high-risk-customers-tbody');
            riskCustTbody.innerHTML = data.high_risk_customers.length === 0
                ? '<tr><td colspan="3" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No high-risk customers.</td></tr>'
                : data.high_risk_customers.map(c => `<tr>
                    <td>${c.name}</td>
                    <td class="fs-12">${c.email}<br>${c.phone}</td>
                    <td><span class="status-badge bg-danger text-white">${c.risk_level}</span></td>
                </tr>`).join('');
                
            const dupCustTbody = document.getElementById('duplicate-customers-tbody');
            dupCustTbody.innerHTML = data.duplicate_customers.length === 0
                ? '<tr><td colspan="3" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No duplicate accounts found.</td></tr>'
                : data.duplicate_customers.map(c => `<tr>
                    <td>${c.name} <span class="text-muted">#${c.id}</span></td>
                    <td>${c.duplicate_name} <span class="text-muted">#${c.duplicate_id}</span></td>
                    <td class="fs-12">${c.email} / ${c.phone}</td>
                </tr>`).join('');

            // Feature 12: Payment Fraud
            const payFraudTbody = document.getElementById('payment-fraud-tbody');
            payFraudTbody.innerHTML = data.payment_fraud.length === 0
                ? '<tr><td colspan="4" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No payment fraud detected.</td></tr>'
                : data.payment_fraud.map(o => `<tr>
                    <td><a href="/admin/orders/${o.id}">#${o.id}</a></td>
                    <td>${o.name || 'Guest'}</td>
                    <td>₹${o.total_amount}</td>
                    <td><span class="fw-bold text-danger">${o.payment_attempts} attempts</span></td>
                </tr>`).join('');

            // Feature 13: Invoice Validation
            const invValTbody = document.getElementById('invoice-validation-tbody');
            invValTbody.innerHTML = data.invoice_validation.length === 0
                ? '<tr><td colspan="4" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>All invoices valid.</td></tr>'
                : data.invoice_validation.map(i => `<tr>
                    <td><a href="/admin/orders/${i.order_id}">#${i.order_id}</a></td>
                    <td><span class="status-badge status-active">${i.status}</span></td>
                    <td>₹${i.order_amount}</td>
                    <td>${i.invoice_amount ? '<span class="text-danger">₹' + i.invoice_amount + '</span>' : '<span class="badge bg-danger">Missing</span>'}</td>
                </tr>`).join('');

            // Duplicate Products
            const dupProdTbody = document.getElementById('duplicate-products-tbody');
            dupProdTbody.innerHTML = data.duplicate_products.length === 0
                ? '<tr><td colspan="5" class="text-center text-success py-3"><i class="bx bx-check-circle me-1"></i>No duplicate products found.</td></tr>'
                : data.duplicate_products.map(p => `<tr>
                    <td>
                        <div class="fw-bold">${p.product_1.name} <span class="text-muted">#${p.product_1.id}</span></div>
                        <div class="text-muted fs-12">${p.product_1.seller} (Status: ${p.product_1.status})</div>
                    </td>
                    <td>
                        <div class="fw-bold">${p.product_2.name} <span class="text-muted">#${p.product_2.id}</span></div>
                        <div class="text-muted fs-12">${p.product_2.seller} (Status: ${p.product_2.status})</div>
                    </td>
                    <td><span class="badge bg-warning text-dark">${p.similarity}% match</span></td>
                    <td><span class="status-badge status-rule">${p.reason}</span></td>
                    <td class="text-end">
                        <div class="d-flex gap-2 justify-content-end">
                            ${p.product_1.status !== 'inactive' ? `<button onclick="flagProductDuplicate(${p.product_1.id})" class="btn btn-xs btn-danger">Flag Prod A</button>` : ''}
                            ${p.product_2.status !== 'inactive' ? `<button onclick="flagProductDuplicate(${p.product_2.id})" class="btn btn-xs btn-danger">Flag Prod B</button>` : ''}
                        </div>
                    </td>
                </tr>`).join('');

            // Feature 10: Blocked IPs
            renderBlockedIps(data.blocked_ips_list);
        })
        .catch(err => console.error('Fraud dashboard error:', err));
    }

    function renderBlockedIps(list) {
        const tbody = document.getElementById('blocked-ips-tbody');
        tbody.innerHTML = list.length === 0
            ? '<tr><td colspan="4" class="text-center text-muted py-3">No IPs blocked.</td></tr>'
            : list.map(item => `<tr>
                <td><code>${item.ip}</code></td>
                <td class="text-muted fs-12">${item.reason || '-'}</td>
                <td class="text-muted fs-12">${item.blocked_at || '-'}</td>
                <td><button onclick="unblockIp('${item.ip}')" class="btn btn-sm btn-light border text-success"><i class="bx bx-lock-open-alt"></i></button></td>
            </tr>`).join('');
    }

    function blockIp(ip, reason = '') {
        fetch(blockIpUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ ip, reason })
        })
        .then(r => r.json())
        .then(data => { showAlert(data.message, data.success ? 'success' : 'danger'); if (data.success) loadFraudDashboard(); });
    }

    function unblockIp(ip) {
        fetch(unblockIpUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ ip })
        })
        .then(r => r.json())
        .then(data => { showAlert(data.message, data.success ? 'success' : 'danger'); if (data.success) loadFraudDashboard(); });
    }

    function flagProductDuplicate(id) {
        if (!confirm('Flag this product as duplicate and deactivate it?')) return;
        fetch(`{{ url('admin/settings/fraud/flag-duplicate') }}/${id}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            showAlert(data.message, data.success ? 'success' : 'danger');
            if (data.success) loadFraudDashboard();
        });
    }

    // Manual IP Block button
    document.getElementById('block-ip-btn')?.addEventListener('click', function () {
        const ip = document.getElementById('manual-block-ip').value.trim();
        const reason = document.getElementById('block-reason').value.trim();
        if (!ip) { showAlert('Please enter an IP address.', 'warning'); return; }
        blockIp(ip, reason || 'Manually blocked');
        document.getElementById('manual-block-ip').value = '';
        document.getElementById('block-reason').value = '';
    });

    // Save Fraud Settings
    document.getElementById('save-fraud-settings-btn')?.addEventListener('click', function () {
        const cancelT = document.getElementById('fraud-cancel-threshold').value;
        const clickT  = document.getElementById('fraud-click-threshold').value;
        const enquiryT = document.getElementById('fraud-enquiry-threshold').value;
        fetch(fraudSaveUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({
                fraud_cancel_threshold: cancelT,
                fraud_click_threshold: clickT,
                fraud_enquiry_threshold: enquiryT
            })
        })
        .then(r => r.json())
        .then(data => showAlert(data.message, data.success ? 'success' : 'danger'));
    });

    // Tab switch: load fraud data when fraud tab clicked
    document.querySelectorAll('.master-tab-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            if (btn.dataset.tab === 'fraud-protection') loadFraudDashboard();
            if (btn.dataset.tab === 'data-control') loadDeletionRequests();
        });
    });
</script>
@endsection
