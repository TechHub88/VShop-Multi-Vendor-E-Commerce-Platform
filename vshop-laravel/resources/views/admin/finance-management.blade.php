
@extends('layouts.vertical', ['title' => 'Finance & Commission Management'])

@section('content')
@php
    $currency = request()->get('currency', session('currency', 'INR'));
    session(['currency' => $currency]);
    $currencySymbol = $currency == 'INR' ? '₹' : ($currency == 'EUR' ? '€' : ($currency == 'GBP' ? '£' : '$'));
    
    $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    $commissionData = [12500, 15200, 14800, 17800, 19200, 21500, 23800, 25600, 28900, 31200, 34500, 37800];
    $revenueData = [45000, 52000, 49800, 58600, 62500, 69800, 74500, 81200, 89500, 96500, 102500, 115800];
    $countriesListArray = ['India', 'USA', 'UK', 'Australia', 'Canada', 'Germany', 'France', 'Japan', 'China', 'Brazil', 'UAE', 'Singapore', 'Others'];

@endphp

<!-- Toast Notification Container -->
<div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; right: 20px; top: 70px;">
    <div id="toastMessage" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="3000">
        <div class="d-flex">
            <div class="toast-body" id="toastMessageBody">Success message here</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>

<div class="container-fluid" style="padding-top: 10px;">
    <style>
        .custom-min-width-table th, .custom-min-width-table td { min-width: 140px; }
        .custom-earnings-table th, .custom-earnings-table td { min-width: 150px; }
        .hover-breakdown-cell .text-start { white-space: normal; word-wrap: break-word; }
    </style>

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap header-section">
        <div>
            <h1 class="h3 mb-1 page-title text-primary">Finance & Commission Management</h1>
            <p class="text-muted fs-7 mb-0">Manage system commissions, payouts, leads, pricing engines, refunds, and disputes.</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <div class="dropdown">
                <button class="btn btn-warning btn-sm shadow-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <iconify-icon icon="solar:chart-square-bold-duotone" class="align-middle me-1"></iconify-icon> Trackers
                </button>
                <ul class="dropdown-menu shadow">
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="showHoldReasonModal()"><iconify-icon icon="solar:clipboard-list-bold-duotone" class="me-2 text-warning"></iconify-icon> Hold Reason Tracking</a></li>
                    <li><a class="dropdown-item" href="javascript:void(0)" onclick="showSettlementHoldModal()"><iconify-icon icon="solar:clock-circle-bold-duotone" class="me-2 text-warning"></iconify-icon> Settlement Delay Tracker</a></li>
                </ul>
            </div>
            <button class="btn btn-primary btn-sm shadow-sm" onclick="exportData()">
                <iconify-icon icon="solar:file-excel-bold-duotone" class="align-middle me-1"></iconify-icon> Export
            </button>
            <button class="btn btn-primary btn-sm shadow-sm" onclick="refreshData()">
                <iconify-icon icon="solar:refresh-bold-duotone" class="align-middle me-1"></iconify-icon> Refresh
            </button>
        </div>
    </div>

    <!-- Main Navigation Tabs (5 Tabs) -->
    <div class="nav-tabs-wrapper mb-4 shadow-sm rounded-3">
        <ul class="nav nav-pills nav-fill" id="financeTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#dashboard">
                    <iconify-icon icon="solar:graph-bold-duotone" class="me-1 align-middle"></iconify-icon> Dashboard
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#global-rules-tab">
                    <iconify-icon icon="solar:settings-bold-duotone" class="me-1 align-middle"></iconify-icon> Global Rules
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#earning-tab">
                    <iconify-icon icon="solar:wallet-bold-duotone" class="me-1 align-middle"></iconify-icon> Earning & Payout
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#refund-dispute-tab">
                    <iconify-icon icon="solar:shield-warning-bold-duotone" class="me-1 align-middle"></iconify-icon> Dispute
                </button>
            </li>
            
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tax-invoice-tab">
                    <iconify-icon icon="solar:receipt-bold-duotone" class="me-1 align-middle"></iconify-icon> Tax &amp; Invoice
                </button>
            </li>
        </ul>
    </div>

    <!-- Tab Content -->
    <div class="tab-content" id="financeTabContent">
        
        <!-- DASHBOARD TAB -->
       <div class="tab-pane fade show active" id="dashboard">
    <!-- KPI CARDS - ROW 1 (4 large cards) -->
    <div class="row g-3 mb-4">
        <!-- Card 1: Total Revenue & Net Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card shadow-sm border-0 card h-100 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Total & Net Revenue</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ $currencySymbol }}{{ number_format($totalRevenue, 2) }}</h3>
                            <div class="mt-2 fs-7 text-dark fw-semibold">
                                Net: {{ $currencySymbol }}{{ number_format($netRevenue, 2) }}
                            </div>
                            <span class="badge bg-success-soft mt-1 fs-8">After commission & refunds</span>
                        </div>
                        <div class="icon-box bg-primary-soft p-3 rounded-circle">
                            <iconify-icon icon="solar:wallet-bold-duotone" width="28" class="text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 2: Total Commission -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card shadow-sm border-0 card h-100 border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Total Commission</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $currencySymbol }}{{ number_format($totalCommission, 2) }}</h3>
                            <span class="badge bg-success-soft mt-2"><iconify-icon icon="solar:arrow-right-up-bold-duotone" class="align-middle"></iconify-icon> +8.3%</span>
                            <div class="text-muted fs-8 mt-1">Platform gross earnings</div>
                        </div>
                        <div class="icon-box bg-success-soft p-3 rounded-circle">
                            <iconify-icon icon="solar:graph-bold-duotone" width="28" class="text-success"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 3: Net & Pending Payouts (Added: Pending Payouts count) -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card shadow-sm border-0 card h-100 border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Net & Pending Payouts</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ $currencySymbol }}{{ number_format($pendingPayouts, 2) }}</h3>
                            <div class="mt-2 fs-7 text-dark fw-semibold">
                                Paid: {{ $currencySymbol }}{{ number_format($totalPayouts, 2) }}
                            </div>
                            <!-- Added: Pending Payouts point -->
                            <div class="mt-1 fs-8 text-muted">
                                Pending Payouts: {{ $pendingPayoutsCount ?? 0 }} transactions
                            </div>
                            <span class="badge bg-info-soft mt-1 fs-8">Remittance cycle active</span>
                        </div>
                        <div class="icon-box bg-info-soft p-3 rounded-circle">
                            <iconify-icon icon="solar:card-send-bold-duotone" width="28" class="text-info"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 4: Refunds (Added: Refund Deduction Impact) -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card shadow-sm border-0 card h-100 border-start border-danger border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Refund Amount</h6>
                            <h3 class="mb-0 fw-bold text-danger">{{ $currencySymbol }}{{ number_format($refundAmount, 2) }}</h3>
                         
                            <div class="mt-1 fs-8 text-muted">
                                Refund Deduction Impact: {{ $currencySymbol }}{{ number_format($refundAmount * 0.18, 2) }} (Net Revenue)
                            </div>
                        </div>
                        <div class="icon-box bg-danger-soft p-3 rounded-circle">
                            <iconify-icon icon="solar:refresh-circle-bold-duotone" width="28" class="text-danger"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPI CARDS - ROW 2 (4 large cards) -->
    <div class="row g-3 mb-4">
        <!-- Card 5: Disputes & Active Hold -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card shadow-sm border-0 card h-100 border-start border-warning border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-itemsLedger Entries-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Disputes & Active Hold</h6>
                            <h3 class="mb-0 fw-bold text-warning">{{ $disputesCount }} Disputes</h3>
                            <div class="mt-2 fs-7 text-dark fw-semibold">
                                Hold: {{ $currencySymbol }}{{ number_format($activeHoldAmount, 2) }}
                            </div>
                            <span class="text-muted fs-8 d-block mt-1">{{ $openDisputes }} active / {{ $resolvedDisputes }} resolved</span>
                        </div>
                        <div class="icon-box bg-warning-soft p-3 rounded-circle">
                            <iconify-icon icon="solar:shield-warning-bold-duotone" width="28" class="text-warning"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 6: Tax Collected (NEW Card) -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card shadow-sm border-0 card h-100 border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Tax Collected (GST)</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ $currencySymbol }}{{ number_format($taxCollected, 2) }}</h3>
                            <span class="badge bg-primary-soft mt-2">18% GST Applied</span>
                            <div class="text-muted fs-8 mt-1">Calculated from commissions</div>
                        </div>
                        <div class="icon-box bg-primary-soft p-3 rounded-circle">
                            <iconify-icon icon="solar:calculator-bold-duotone" width="28" class="text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 7: Lead Revenue -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card shadow-sm border-0 card h-100 border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Lead Revenue</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $currencySymbol }}{{ number_format($leadRevenue, 2) }}</h3>
                            <div class="mt-1 fs-8 text-muted">
                                Click: {{ $currencySymbol }}{{ number_format($todayClickRevenue, 2) }} | Enq: {{ $currencySymbol }}{{ number_format($todayEnquiryRevenue, 2) }}
                            </div>
                            <div class="text-muted fs-8 mt-1 font-monospace">Top Seller: {{ $topLeadSeller ? $topLeadSeller->name : 'N/A' }}</div>
                        </div>
                        <div class="icon-box bg-success-soft p-3 rounded-circle">
                            <iconify-icon icon="solar:tag-price-bold-duotone" width="28" class="text-success"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Card 8: Dispute Wallet Hold -->
        <div class="col-xl-3 col-md-6">
            <div class="kpi-card shadow-sm border-0 card h-100 border-start border-danger border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Dispute Wallet Hold</h6>
                            <h3 class="mb-0 fw-bold text-danger">{{ $currencySymbol }}{{ number_format($disputeWalletHold, 2) }}</h3>
                            <span class="badge bg-danger-soft mt-2">Locked Funds</span>
                            <div class="text-muted fs-8 mt-1">Pending resolution settlement</div>
                        </div>
                        <div class="icon-box bg-danger-soft p-3 rounded-circle">
                            <iconify-icon icon="solar:graph-down-bold-duotone" width="28" class="text-danger"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- TRENDS & CHARTS -->
    <div class="row g-3 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="mb-0 fw-bold text-primary">Commission & Revenue Trends</h6>
                        <small class="text-muted fs-8">Monthly financial performance summary</small>
                    </div>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary active" onclick="changeChartType('line')">Line</button>
                        <button class="btn btn-outline-primary" onclick="changeChartType('bar')">Bar</button>
                        <button class="btn btn-outline-primary" onclick="changeChartType('area')">Area</button>
                    </div>
                </div>
                <div class="card-body pt-2">
                    <canvas id="commissionTrendsModern" height="220" style="max-height: 220px; width: 100%;"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3 pb-0">
                    <h6 class="mb-0 fw-bold text-primary">Revenue Mix Breakdown</h6>
                    <small class="text-muted fs-8">Doughnut representation of revenue streams</small>
                </div>
                <div class="card-body pt-2 d-flex flex-column justify-content-center align-items-center">
                    <canvas id="revenuePieModern" height="150" style="max-height: 150px; width: 100%;"></canvas>
                    <div class="text-center mt-3">
                        <span class="badge bg-success-soft me-1 text-success">Products {{ number_format(($productRevenue / max(1, $productRevenue + $serviceRevenue)) * 100, 1) }}%</span>
                        <span class="badge bg-info-soft text-info">Services {{ number_format(($serviceRevenue / max(1, $productRevenue + $serviceRevenue)) * 100, 1) }}%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- APP-WISE REVENUE -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="mb-0 fw-bold text-primary">App-wise Gross revenue</h5>
        <p class="text-muted fs-8 mb-0">
            Monitored metrics split across internal ecosystem applications.
        </p>
    </div>

    <div class="card-body">
        <div class="row g-3">
            @foreach($appWiseRevenue as $appName => $revVal)

                @continue(in_array($appName, [
                    'Wellness',
                    'Marketplace',
                    'vWellness',
                    'vMarketplace'
                ]))

                <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6">
                    <div class="border rounded p-3 text-center bg-light h-100">
                        <div class="icon-box bg-primary-soft p-2 rounded-circle mx-auto mb-2"
                             style="width:50px;height:50px;display:flex;align-items:center;justify-content:center;">

                            @if($appName == 'vShop')
                                <iconify-icon icon="solar:shop-bold-duotone" width="24" class="text-primary"></iconify-icon>
                            @elseif($appName == 'vTravel')
                                <iconify-icon icon="solar:plain-bold-duotone" width="24" class="text-primary"></iconify-icon>
                            @elseif($appName == 'vCampus')
                                <iconify-icon icon="solar:diploma-bold-duotone" width="24" class="text-primary"></iconify-icon>
                            @elseif($appName == 'vEdge')
                                <iconify-icon icon="solar:widget-bold-duotone" width="24" class="text-primary"></iconify-icon>
                            @endif

                        </div>

                        <h6 class="fw-semibold mb-1">{{ $appName }}</h6>

                        <h5 class="fw-bold mb-0 text-primary">
                            {{ $currencySymbol }}{{ number_format($revVal, 2) }}
                        </h5>
                    </div>
                </div>

            @endforeach
        </div>
    </div>
</div>

    <!-- RECURSIVE ALGORITHM PREVIEW -->
    <div class="row g-3">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm bg-gradient-light">
                <div class="card-body py-3">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <iconify-icon icon="solar:chart-2-bold-duotone" width="24" class="text-primary"></iconify-icon>
                        <h6 class="mb-0 fw-bold text-primary">RECURSIVE COMMISSION DISTRIBUTION ENGINE</h6>
                        <span class="badge bg-success">ACTIVE ENGINE</span>
                    </div>
                    <p class="small mb-0 text-muted">Backend logic processes <strong>Customer Payment &rarr; Distributed to all levels</strong> recursively based on settings. Old orders continue to preserve historic commission tiers. This recursive algorithm calculates and secures platform margins automatically.</p>
                </div>
            </div>
        </div>
    </div>
</div>

        <!-- GLOBAL RULES TAB -->
        <div class="tab-pane fade" id="global-rules-tab">
            <!-- Nested Tabs for Global Rules -->
            <div class="d-flex gap-2 mb-3 flex-wrap">
                <button class="btn btn-outline-primary btn-sm active" id="btn-sub-config" onclick="switchSubTab('sub-config')">Commission & Hold Settings</button>
                <button class="btn btn-outline-primary btn-sm" id="btn-sub-table" onclick="switchSubTab('sub-table')">Global Commission Rules Table</button>
                <button class="btn btn-outline-primary btn-sm" id="btn-sub-boost" onclick="switchSubTab('sub-boost')">Lead Monetization & Boost Engine</button>
                <button class="btn btn-outline-primary btn-sm" id="btn-sub-pricing" onclick="switchSubTab('sub-pricing')">Dynamic Pricing & Discount Caps</button>
            </div>

            <div id="sub-tab-content">
                <!-- SUB TAB 1: COMMISSION & HOLD SETTINGS -->
                <div class="sub-tab-pane" id="sub-config">
                    <div class="row g-3 mb-4">
                        <!-- Rule Hierarchy -->



<!-- Single Table: Complete Multi-Level Commission Management -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white py-3">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-white">
                    <iconify-icon icon="solar:graph-new-bold" class="me-2"></iconify-icon>
                    MULTI-LEVEL COMMISSION MANAGEMENT
                </h5>
                <p class="text-white-50 small mb-0 mt-1">Unlimited Levels | Dynamic Splits | App & Seller Specific Tiers</p>
            </div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-light btn-sm fw-semibold text-primary" onclick="addCommissionRow()">
                    <iconify-icon icon="solar:add-circle-bold" class="align-middle me-1"></iconify-icon> Add Row
                </button>
                <button type="button" class="btn btn-success btn-sm fw-semibold" onclick="saveAllCommissions()">
                    <iconify-icon icon="solar:save-bold" class="align-middle me-1"></iconify-icon> Save Changes
                </button>
              
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        
        <!-- Main Commission Table -->
        <div class="table-responsive" style="max-height: 550px; overflow-y: auto;">
            <table class="table table-bordered table-hover align-middle mb-0" id="commissionMainTable">
                <thead class="bg-dark text-white position-sticky top-0" style="z-index: 10;">
                    <tr class="text-center">
                        <th rowspan="2" width="40" class="align-middle">#</th>
                        <th rowspan="2" width="200" class="align-middle">Seller (Select)</th>
                        <th rowspan="2" width="100" class="align-middle">App Type</th>
                        <th rowspan="2" width="100" class="align-middle">Sell Type</th>
                        <th rowspan="2" width="120" class="align-middle">Total Sales (₹)</th>
                        <th rowspan="2" width="120" class="align-middle">Commission Type</th>
                        <th colspan="3" class="align-middle">Commission Split</th>
                        <th rowspan="2" width="120" class="align-middle">Referral ID</th>
                        <th rowspan="2" width="80" class="align-middle">Action</th>
                    </tr>
                    <tr class="bg-secondary text-white text-center">
                        <th width="100">Seller (%)</th>
                        <th width="100">Referrer (%)</th>
                        <th width="100">Platform (%)</th>
                    </tr>
                </thead>
                <tbody id="commissionTableBody">
                    @php
                        $commissionRules = collect($commissionRules)->map(function($item) {
                            return (array) $item;
                        });
                        if($commissionRules->isEmpty()) {
                            $commissionRules = collect([
                                [
                                    'seller_id' => null,
                                    'app_type' => 'vshop',
                                    'sell_type' => 'direct',
                                    'total_sales' => 0.00,
                                    'commission_type' => 'percentage',
                                    'seller_commission' => 60,
                                    'referrer_commission' => 10,
                                    'platform_commission' => 30,
                                    'referral_id' => null
                                ]
                            ]);
                        }
                    @endphp
                    @foreach($commissionRules as $index => $rule)
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>
                            <select class="form-select form-select-sm seller-select" onchange="updateCommissionRow(this)">
                                <option value="">-- Select Seller --</option>
                                @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" data-name="{{ $seller->name }}" data-application="{{ $seller->application ?? 'vshop' }}" {{ ($rule['seller_id'] ?? '') == $seller->id ? 'selected' : '' }}>
                                    {{ $seller->name }} ({{ $seller->application ?? 'vshop' }})
                                </option>
                                @endforeach
                            </select>
                         </td>
                        <td>
                            <select class="form-select form-select-sm app-type-select" onchange="updateCommissionRow(this)">
                                 <option value="">-- Select Platform --</option>
                                <option value="vshop" {{ ($rule['app_type'] ?? '') == 'vshop' ? 'selected' : '' }}>vShop</option>
                                <option value="vtravel" {{ ($rule['app_type'] ?? '') == 'vtravel' ? 'selected' : '' }}>vTravel</option>
                                <option value="vcampus" {{ ($rule['app_type'] ?? '') == 'vcampus' ? 'selected' : '' }}>vCampus</option>
                                <option value="vedge" {{ ($rule['app_type'] ?? '') == 'vedge' ? 'selected' : '' }}>vEdge</option>
                              
                            </select>
                         </td>
                        <td>
                            <select class="form-select form-select-sm sell-type-select" onchange="updateCommissionRow(this)">
                                <option value="direct" {{ ($rule['sell_type'] ?? '') == 'direct' ? 'selected' : '' }}>Direct Sale</option>
                                <option value="dropshipping" {{ ($rule['sell_type'] ?? '') == 'dropshipping' ? 'selected' : '' }}>Dropshipping</option>
                                
                            </select>
                         </td>
                        <td>
                            <input type="number" class="form-control form-control-sm total-sales" placeholder="0.00" value="{{ $rule['total_sales'] ?? '' }}" onchange="calculateCommission(this)" readonly>
                         </td>
                        <td>
                            <select class="form-select form-select-sm commission-type-select" onchange="updateCommissionRow(this)">
                                <option value="percentage" {{ ($rule['commission_type'] ?? 'percentage') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                                <option value="fixed" {{ ($rule['commission_type'] ?? '') == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                              
                            </select>
                         </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm seller-commission" placeholder="%" value="{{ $rule['seller_commission'] ?? 60 }}" onchange="calculateTotalCommission(this)">
                         </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm referrer-commission" placeholder="%" value="{{ $rule['referrer_commission'] ?? 10 }}" onchange="calculateTotalCommission(this)">
                         </td>
                        <td>
                            <input type="number" step="0.01" class="form-control form-control-sm platform-commission" placeholder="%" value="{{ $rule['platform_commission'] ?? 30 }}" onchange="calculateTotalCommission(this)">
                         </td>
                        <td>
                            <select class="form-select form-select-sm referral-select">
                                <option value="">-- No Referral --</option>
                                @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}" {{ ($rule['referral_id'] ?? '') == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                                @endforeach
                            </select>
                         </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCommissionRow(this)" title="Remove Row">
                                <iconify-icon icon="solar:trash-bin-trash-bold" width="16"></iconify-icon>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- History / Saved Commissions Table -->
        <div class="p-3 bg-light border-top">
            <h6 class="fw-bold mb-3 text-secondary"><iconify-icon icon="solar:history-bold" class="align-middle me-1"></iconify-icon> Saved Commissions Rule History</h6>
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered mb-0 bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Seller/Buyer</th>
                            <th>Platform</th>
                            <th>Sell Type</th>
                            <th>Commission Type</th>
                            <th>Seller Split</th>
                            <th>Referrer Split</th>
                            <th>Platform Split</th>
                            <th>Referrer Name</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($commissionRules as $hIndex => $hRule)
                            @php
                                $hSellerName = 'Global Default';
                                if (!empty($hRule['seller_id'])) {
                                    $sObj = collect($sellers)->firstWhere('id', $hRule['seller_id']);
                                    if ($sObj) $hSellerName = $sObj->name;
                                }
                                $hReferrerName = 'None';
                                if (!empty($hRule['referral_id'])) {
                                    $rObj = collect($sellers)->firstWhere('id', $hRule['referral_id']);
                                    if ($rObj) $hReferrerName = $rObj->name;
                                }
                            @endphp
                            <tr>
                                <td>{{ $hIndex + 1 }}</td>
                                <td><span class="badge bg-info text-dark">{{ $hSellerName }}</span></td>
                                <td><span class="badge bg-secondary">{{ $hRule['app_type'] ?? 'vshop' }}</span></td>
                                <td>{{ ucfirst($hRule['sell_type'] ?? 'direct') }}</td>
                                <td>{{ ucfirst($hRule['commission_type'] ?? 'percentage') }}</td>
                                <td>{{ $hRule['seller_commission'] ?? 0 }}%</td>
                                <td>{{ $hRule['referrer_commission'] ?? 0 }}%</td>
                                <td>{{ $hRule['platform_commission'] ?? 0 }}%</td>
                                <td>{{ $hReferrerName }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted">No saved commission rules.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



        

     
    </div>
</div>


<!-- Settlement hold settings (below both) -->
<div class="row mt-3">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0 fw-bold text-primary">Settlement Hold Rules</h5>
            </div>
            <div class="card-body">
                <form id="settlementRulesForm" onsubmit="saveSettlementRules(event)">
                    <div class="row g-2">
                        <div class="col-sm-3">
                            <label class="form-label">Default Hold Period</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" name="hold_days" id="holdDays" value="{{ $settings['hold_days'] ?? 7 }}">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">New Seller Hold Period</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" name="new_seller_hold_days" id="newSellerHoldDays" value="{{ $settings['new_seller_hold_days'] ?? 30 }}">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Auto Hold Return Threshold</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" name="return_threshold" id="returnThreshold" value="{{ $settings['return_threshold'] ?? 20 }}">
                                <span class="input-group-text">% returns</span>
                            </div>
                        </div>
                        <div class="col-sm-3">
                            <label class="form-label">Release Trigger Type</label>
                            <select class="form-select form-select-sm" name="release_type" id="releaseType">
                                <option value="auto" {{ ($settings['release_type'] ?? '') == 'auto' ? 'selected' : '' }}>Auto-Release on SLA</option>
                                <option value="manual" {{ ($settings['release_type'] ?? '') == 'manual' ? 'selected' : '' }}>Manual Admin Override</option>
                            </select>
                        </div>
                    </div>
                    <div class="border-top mt-3 pt-3">
                        <h6 class="fw-semibold text-warning mb-2">Apply Manual Settlement Hold</h6>
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <select class="form-select form-select-sm" id="manualHoldSellerId">
                                    <option value="">-- Select Seller --</option>
                                    @foreach($sellers as $seller)
                                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <input type="text" class="form-control form-control-sm" id="manualHoldReason" placeholder="Reason for hold">
                            </div>
                        </div>
                        <button type="button" class="btn btn-warning btn-sm mt-2 w-100" onclick="applyManualHoldSubmit()">Apply Manual Hold</button>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-primary btn-sm w-100 shadow-sm">Save Settlement Settings</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
                    </div>
                </div>


<div class="sub-tab-pane d-none" id="sub-table">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-primary">PRODUCT/SERVICE GLOBAL RULE ENGINE</h5>
                <p class="text-muted fs-8 mb-0">Configure specific commission, pricing margin, discount, and effective dates for sellers.</p>
            </div>
            <button class="btn btn-primary btn-sm px-3" onclick="addGlobalRuleRow()">
                <iconify-icon icon="solar:add-circle-bold-duotone" class="align-middle me-1"></iconify-icon> Add Rule Row
            </button>
        </div>
        <div class="card-body p-3">
            <form id="globalRulesTableForm" onsubmit="saveGlobalRulesTable(event)">
                
                <!-- TAB NAVIGATION -->
                <div class="mb-3" style="border-bottom: 2px solid #e9ecef;">
                    <ul class="nav nav-tabs border-0" id="ruleSplitTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active px-4" style="font-size:13px; font-weight:600;" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1" type="button" role="tab">
                                <iconify-icon icon="solar:cart-bold" class="me-1"></iconify-icon> Product & Pricing
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link px-4" style="font-size:13px; font-weight:600;" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2" type="button" role="tab">
                                <iconify-icon icon="solar:calendar-bold" class="me-1"></iconify-icon> Rules & Settings
                            </button>
                        </li>
                    </ul>
                </div>

                <!-- TAB CONTENT WRAPPER -->
                <div class="tab-content">
                    
                    <!-- TAB 1 - Product & Pricing (Price + Convert to Rupee column) -->
                    <div class="tab-pane fade show active" id="tab1" role="tabpanel">
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-bordered table-hover table-striped align-middle mb-0" id="globalRulesTab1Table" style="min-width: 1200px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 13%;">Seller/Buyer</th>
                                        <th style="width: 7%;">App</th>
                                        <th style="width: 9%;">Sell Type</th>
                                        <th style="width: 11%;">Category</th>
                                        <th style="width: 16%;">Product</th>
                                        <th style="width: 10%;">Price (Original)</th>
                                        <th style="width: 12%;">Convert to Rupee (INR)</th>
                                        <th style="width: 9%;">Commission %</th>
                                        <th style="width: 8%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="globalRulesTab1Body">
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- TAB 2 - Rules & Settings -->
                    <div class="tab-pane fade" id="tab2" role="tabpanel">
                        <div class="table-responsive" style="overflow-x: auto;">
                            <table class="table table-bordered table-hover table-striped align-middle mb-0" id="globalRulesTab2Table" style="min-width: 800px;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 15%;">Discount (%)</th>
                                        <th style="width: 12%;">Min Margin (%)</th>
                                        <th style="width: 10%;">Boost</th>
                                        <th style="width: 18%;">Effective Date</th>
                                        <th style="width: 15%;">History</th>
                                        <th style="width: 10%;">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="globalRulesTab2Body">
                                    <!-- Dynamic rows will be added here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <button type="submit" class="btn btn-primary shadow-sm px-4">
                        <iconify-icon icon="solar:save-bold" class="align-middle me-1"></iconify-icon> Save All Global Rules
                    </button>
                </div>
            </form>
      
            <!-- Saved Rules Display -->
            <div class="mt-5">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-light d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <iconify-icon icon="solar:list-bold" class="me-2"></iconify-icon> Saved Global Rules
                        </h5>
                        <div class="d-flex gap-2">
                            <div class="input-group input-group-sm" style="width: 250px;">
                                <span class="input-group-text"><iconify-icon icon="solar:magic-stick-bold"></iconify-icon></span>
                                <input type="text" class="form-control" id="savedRulesSearch" placeholder="Search rules..." onkeyup="filterSavedRules()">
                            </div>
                            <select class="form-select form-select-sm" id="savedRulesPerPage" style="width: 80px;" onchange="changeSavedRulesPerPage()">
                                <option value="5">5</option>
                                <option value="10" selected>10</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0" id="savedGlobalRulesTable">
                                <thead class="table-primary">
                                    <tr>
                                        <th width="3%">#</th>
                                        <th width="10%">Seller</th>
                                        <th width="6%">App</th>
                                        <th width="8%">Type</th>
                                        <th width="10%">Category</th>
                                        <th width="12%">Product</th>
                                        <th width="8%">Original Price</th>
                                        <th width="10%">INR Value</th>
                                        <th width="6%">Comm%</th>
                                        <th width="8%">Discount</th>
                                        <th width="6%">Margin</th>
                                        <th width="5%">Boost</th>
                                        <th width="10%">Eff. Date</th>
                                        <th width="10%">Created</th>
                                        <th width="8%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="savedRulesBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div class="text-muted small">
                                Showing <span id="savedRulesStart">0</span> to <span id="savedRulesEnd">0</span> of <span id="savedRulesTotal">0</span> entries
                            </div>
                            <nav>
                                <ul class="pagination pagination-sm mb-0" id="savedRulesPagination"></ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





             <!-- SUB TAB 3: LEAD MONETIZATION & BOOST ENGINE -->
<div class="sub-tab-pane d-none" id="sub-boost">
    
<div class="row g-3 mb-4">
    <!-- Lead Monetization Rules - Left Side -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0 fw-bold text-primary">Lead Monetization Rules</h5>
            </div>
            <div class="card-body">
                <form id="leadRulesForm" onsubmit="saveLeadGlobalRules(event)">
                    <!-- Row 1: PPC and PPL side by side -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Pay Per Click (PPC)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="ppc_rate" id="ppcRateField" placeholder="0.00" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Pay Per Enquiry (PPL)</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">$</span>
                                <input type="number" step="0.01" class="form-control" name="ppl_rate" id="pplRateField" placeholder="0.00" required>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Row 2: Free Lead Limit and Paid Threshold side by side -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Free Lead Limit</label>
                            <input type="number" class="form-control form-control-sm" name="free_lead_limit" id="freeLeadLimitField" placeholder="e.g., 100" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Paid Lead Threshold</label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.01" class="form-control" name="paid_lead_threshold" id="paidLeadThresholdField" placeholder="0.00" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Row 3: Daily Caps side by side -->
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Max Clicks/Product/Day</label>
                            <input type="number" class="form-control form-control-sm" id="maxClicksPerProduct" placeholder="e.g., 50" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">Max Charge/Seller/Day</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control form-control-sm" id="dailyCapSeller" placeholder="e.g., 1000" required>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <!-- Fraud Protection Rules -->
                    <div class="mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label class="form-label small fw-bold mb-0">🛡️ Fraud Protection Rules</label>
                            <button type="button" class="btn btn-sm btn-link p-0 text-primary" onclick="showFraudRulesDetails()" title="View all fraud protection rules">
                                <iconify-icon icon="mdi:question-mark-circle-outline" width="20"></iconify-icon>
                            </button>
                        </div>
                        <div class="small text-muted">
                            Click the question mark to view all active fraud protection rules
                        </div>
                    </div>
                    
                    <hr class="my-2">
                    
                    <!-- Seller-specific Daily Caps - COMPACT -->
                    <div class="mb-2">
                        <label class="form-label small fw-bold">👥 Seller Daily Caps</label>
                        
                        <div class="d-flex gap-1 mb-1">
                            <input type="text" class="form-control form-control-sm" id="sellerSearchInput" placeholder="🔍 Search" onkeyup="filterSellers()" style="font-size: 11px;">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAllSellers()" style="font-size: 11px; padding: 2px 6px;">All</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAllSellers()" style="font-size: 11px; padding: 2px 6px;">None</button>
                        </div>
                        
                        <div class="border rounded mb-1" style="max-height: 120px; overflow-y: auto; background: white; font-size: 11px;">
                            <div id="sellersListContainer" class="p-1">
                                <div class="text-center text-muted py-2">
                                    <span class="spinner-border spinner-border-sm text-primary"></span>
                                    <div class="small">Loading...</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="small text-muted mb-1" id="selectedSellersCountDisplay">No sellers selected</div>
                        
                        <div class="input-group input-group-sm">
                            <span class="input-group-text">₹</span>
                            <input type="number" class="form-control" id="sellerDailyCap" placeholder="Amount">
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="addBulkSellerCap()">Apply</button>
                        </div>
                        
                        <div id="sellerCapsList" class="small mt-2" style="max-height: 80px; overflow-y: auto;">
                            <!-- Seller caps will be listed here -->
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm w-100 mt-2">Save Lead Config</button>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Lead Billing Rules - Right Side -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3">
                <h5 class="mb-0 fw-bold text-primary">Lead Billing Rules</h5>
            </div>
            <div class="card-body">
                <form id="leadBillingRulesForm" onsubmit="saveLeadBillingRules(event)">
                    <div class="row g-2 mb-2">
                        <div class="col-md-12">
                            <label class="form-label">Select Seller</label>
                            <select class="form-select form-select-sm" name="billing_seller_id" id="billingSellerId">
                                <option value="">-- All Sellers --</option>
                                @foreach($sellers as $seller)
                                <option value="{{ $seller->id }}">S-{{ $seller->id }} ({{ $seller->name }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Deduction Type</label>
                            <select class="form-select form-select-sm" name="deduction_type" id="deductionType" onchange="toggleDateRange()">
                                <option value="realtime">On the Spot (Real-time)</option>
                                <option value="monthly" selected>Monthly Billing</option>
                                <option value="weekly">Weekly Billing</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-md-12">
                            <label class="form-label">Billing Period</label>
                            <input type="date" class="form-control form-control-sm" id="billingPeriodDate" style="background-color: #fff;">
                            <div class="form-text text-muted small" id="billingPeriodHint">Select billing cycle date (Invoice will be generated on this date)</div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">If Wallet Insufficient</label>
                            <select class="form-select form-select-sm" name="wallet_insufficient_action" id="walletInsufficientAction">
                                <option value="block">Block Lead Generation</option>
                                <option value="record_debt">Allow but Record Debt</option>
                                <option value="notify_admin">Notify Admin</option>
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Send Notification</label>
                            <div class="d-flex gap-3 mt-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="send_notification" id="notificationYes" value="yes" checked>
                                    <label class="form-check-label small" for="notificationYes">Yes</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="send_notification" id="notificationNo" value="no">
                                    <label class="form-check-label small" for="notificationNo">No</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row g-2 mb-2">
                        <div class="col-md-6">
                            <label class="form-label">Invoice Generation Day</label>
                            <input type="number" class="form-control form-control-sm" name="invoice_generation_day" id="invoiceGenerationDay" value="5" min="1" max="31">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm w-100">Save Billing Rules</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


</div>

<!-- Modal for Fraud Rules Details -->
<div class="modal fade" id="fraudRulesModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-gradient-primary text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title">
                    <iconify-icon icon="mdi:shield-check" width="24" class="me-2"></iconify-icon>
                    Fraud Protection Rules
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="max-height: 500px; overflow-y: auto;">
                <div class="alert alert-info small mb-3">
                    <iconify-icon icon="mdi:information" width="16" class="me-1"></iconify-icon>
                    These rules are automatically applied to all leads and trigger auto-refund when violated.
                    <strong>All rules are active by default.</strong>
                </div>
                
                <div id="fraudRulesDetailsList">
                    <!-- All 6 fraud rules will be displayed here -->
                </div>
                
                <!-- Summary Statistics -->
                <div class="mt-4 pt-3 border-top">
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <small class="text-muted d-block">Total Rules</small>
                                <h6 class="mb-0 fw-bold" id="totalRulesCount">6</h6>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-light rounded p-2 text-center">
                                <small class="text-muted d-block">Active Rules</small>
                                <h6 class="mb-0 fw-bold text-success" id="activeRulesCount">6</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">
                    <iconify-icon icon="mdi:close" width="16"></iconify-icon>
                    Close
                </button>
                <button type="button" class="btn btn-primary btn-sm" onclick="refreshFraudRules()">
                    <iconify-icon icon="mdi:refresh" width="16"></iconify-icon>
                    Refresh Rules
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row g-3 mb-4">
  
   <div class="col-md-12">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-0 fw-bold text-primary">Lead Revenue Tracking</h5>
                    <p class="text-muted fs-8 mb-0">Monthly lead revenue & performance metrics</p>
                </div>
                <div class="d-flex gap-2">
                    <select class="form-select form-select-sm" id="revenueTimeframe" style="width: 100px;" onchange="updateRevenueChart()">
                        <option value="weekly">Weekly</option>
                        <option value="monthly" selected>Monthly</option>
                        <option value="quarterly">Quarterly</option>
                        <option value="yearly">Yearly</option>
                    </select>
                    <button class="btn btn-sm btn-outline-primary" onclick="downloadRevenueChart()">
                        <iconify-icon icon="solar:export-bold" width="16"></iconify-icon>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <canvas id="leadRevenueChart" height="200" style="max-height: 220px; width: 100%;"></canvas>
                <div class="row mt-3 g-2">
                    <div class="col-4 text-center">
                        <div class="border rounded-3 p-2 bg-success bg-opacity-10">
                            <h6 class="mb-0 text-success fw-bold" id="clickRevenue">$0</h6>
                            <small class="text-muted">Click Revenue</small>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="border rounded-3 p-2 bg-primary bg-opacity-10">
                            <h6 class="mb-0 text-primary fw-bold" id="enquiryRevenue">$0</h6>
                            <small class="text-muted">Enquiry Revenue</small>
                        </div>
                    </div>
                    <div class="col-4 text-center">
                        <div class="border rounded-3 p-2 bg-warning bg-opacity-10">
                            <h6 class="mb-0 text-warning fw-bold" id="freeLeadsUsed">0</h6>
                            <small class="text-muted">Free Leads Used</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    @php
        use App\Models\MarketplaceCategory;
        $marketplaceCategories = MarketplaceCategory::all();
    @endphp

    <!-- App-Wise Lead Pricing -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-primary">App-Wise Lead Pricing</h5>
                <p class="text-muted fs-8 mb-0">Define specific Click and Enquiry prices across various applications</p>
            </div>
            <button class="btn btn-primary btn-sm" onclick="addAppPricingRow()">+ Add Pricing Rule</button>
        </div>
        <div class="card-body">
            <form id="appWisePricingForm" onsubmit="saveAppWisePricing(event)">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle" id="appWisePricingTable">
                        <thead class="table-light">
                            <tr>
                                <th>App</th>
                                <th>Category</th>
                                <th>Seller/Buyer</th>
                                <th>Click Price ($)</th>
                                <th>Enquiry Price ($)</th>
                                <th class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="appWisePricingBody">
                            @if(!empty($pricingRulesTable) && count($pricingRulesTable) > 0)
                                @foreach($pricingRulesTable as $idx => $p)
                                <tr>
                                    <td>
                                        <select class="form-select form-select-sm" name="pricing[{{ $idx }}][app]">
                                            <option value="vshop" {{ ($p['app'] ?? '') == 'vshop' ? 'selected' : '' }}>vShop</option>
                                            <option value="vtravel" {{ ($p['app'] ?? '') == 'vtravel' ? 'selected' : '' }}>vTravel</option>
                                            <option value="vcampus" {{ ($p['app'] ?? '') == 'vcampus' ? 'selected' : '' }}>vCampus</option>
                                            <option value="vedge" {{ ($p['app'] ?? '') == 'vedge' ? 'selected' : '' }}>vEdge</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="pricing[{{ $idx }}][category_id]">
                                            <option value="">Select Category</option>
                                            @foreach($marketplaceCategories as $cat)
                                            <option value="{{ $cat->id }}" {{ ($p['category_id'] ?? '') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="pricing[{{ $idx }}][seller_id]">
                                            <option value="">All Sellers</option>
                                            @foreach($sellers as $seller)
                                            <option value="{{ $seller->id }}" {{ ($p['seller_id'] ?? '') == $seller->id ? 'selected' : '' }}>S-{{ $seller->id }} ({{ $seller->name }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="pricing[{{ $idx }}][click_price]" value="{{ $p['click_price'] ?? '0.50' }}">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="pricing[{{ $idx }}][enquiry_price]" value="{{ $p['enquiry_price'] ?? '5.00' }}">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAppPricingRow(this)">✗</button>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr id="defaultPricingRow">
                                    <td>
                                        <select class="form-select form-select-sm" name="pricing[0][app]">
                                            <option value="vshop">vShop</option>
                                            <option value="vtravel">vTravel</option>
                                            <option value="vcampus">vCampus</option>
                                            <option value="vedge">vEdge</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="pricing[0][category_id]">
                                            <option value="">Select Category</option>
                                            @foreach($marketplaceCategories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-select form-select-sm" name="pricing[0][seller_id]">
                                            <option value="">All Sellers</option>
                                            @foreach($sellers as $seller)
                                            <option value="{{ $seller->id }}">S-{{ $seller->id }} ({{ $seller->name }})</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="pricing[0][click_price]" value="0.50">
                                    </td>
                                    <td>
                                        <input type="number" step="0.01" class="form-control form-control-sm" name="pricing[0][enquiry_price]" value="5.00">
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAppPricingRow(this)">✗</button>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <button type="submit" class="btn btn-primary btn-sm shadow-sm w-100">Save App-Wise Pricing</button>
                </div>
            </form>
        </div>
    </div>

    <!-- ========================================== -->
    <!-- NEW: LEAD BILLING REGISTER TABLE -->
    <!-- ========================================== -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h5 class="mb-0 fw-bold text-primary">📋 Lead Billing Register</h5>
                <p class="text-muted fs-8 mb-0">Complete history of all leads uploaded, billed, and their status</p>
            </div>
            <div class="d-flex gap-2">
                <select class="form-select form-select-sm" id="leadSellerFilter" style="width: 130px;" onchange="filterLeadBillingBySeller()">
                    <option value="">All Sellers</option>
                    @foreach($sellers as $seller)
                    <option value="{{ $seller->name }}">{{ $seller->name }}</option>
                    @endforeach
                </select>
                <select class="form-select form-select-sm" id="leadStatusFilter" style="width: 120px;" onchange="filterLeadBillingByStatus()">
                    <option value="">All Status</option>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                    <option value="Refunded">Refunded</option>
                </select>
                <button class="btn btn-success btn-sm" onclick="exportLeadBillingCSV()">
                    <iconify-icon icon="solar:file-excel-bold-duotone" class="align-middle me-1"></iconify-icon> Export CSV
                </button>
                <button class="btn btn-primary btn-sm" onclick="refreshLeadBillingTable()">
                    <iconify-icon icon="solar:refresh-bold-duotone" class="align-middle me-1"></iconify-icon> Refresh
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="leadBillingTable" style="min-width: 1200px;">
                    <thead class="table-light">
                        <tr>
                            <th>Lead ID</th>
                            <th>Date & Time</th>
                            <th>Seller Name</th>
                            <th>Lead Type</th>
                            <th>Lead Source</th>
                            <th>Customer Name</th>
                            <th>Customer Contact</th>
                            <th>Charge Type</th>
                            <th>Amount ($)</th>
                            <th>Status</th>
                            <th>Invoice Status</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="leadBillingTableBody">
                        @forelse($leadBillingData as $lb)
                        @php
                            $lbType = ucfirst($lb->transaction_type);
                            $lbTypeBadge = match($lb->transaction_type) {
                                'click' => 'info',
                                'enquiry' => 'success',
                                'lead_fee' => 'primary',
                                'lead_refund' => 'danger',
                                default => 'secondary'
                            };
                            $lbChargeType = match($lb->transaction_type) {
                                'click' => 'PPC',
                                'enquiry' => 'PPL',
                                'lead_fee' => 'PPL',
                                'lead_refund' => 'Refunded',
                                default => '-'
                            };
                            $lbStatus = $lb->status ?? 'pending';
                            $lbStatusBadge = match($lbStatus) {
                                'completed' => 'success',
                                'pending' => 'warning',
                                'failed' => 'danger',
                                default => 'secondary'
                            };
                            $lbAmount = $lb->transaction_type === 'lead_refund' ? -$lb->credit : $lb->credit;
                        @endphp
                        <tr data-seller="{{ $lb->seller->name ?? '' }}" data-status="{{ ucfirst($lbStatus) }}">
                            <td>#LD-{{ str_pad($lb->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>{{ $lb->created_at ? $lb->created_at->format('Y-m-d h:i A') : '-' }}</td>
                            <td>{{ $lb->seller->name ?? 'N/A' }}</td>
                            <td><span class="badge bg-{{ $lbTypeBadge }}">{{ $lbType }}</span></td>
                            <td>{{ $lb->description ?? 'Organic' }}</td>
                            <td>{{ $lb->reference ?? '-' }}</td>
                            <td>{{ $lb->seller->email ?? '-' }}</td>
                            <td>{{ $lbChargeType }}</td>
                            <td>${{ number_format(abs($lbAmount), 2) }}</td>
                            <td><span class="badge bg-{{ $lbStatusBadge }}">{{ ucfirst($lbStatus) }}</span></td>
                            <td><span class="badge bg-{{ $lbStatus == 'completed' ? 'primary' : 'secondary' }}">{{ $lbStatus == 'completed' ? 'Generated' : 'Not Generated' }}</span></td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary" onclick="viewLeadDetails('#LD-{{ $lb->id }}')">
                                    <iconify-icon icon="solar:eye-bold"></iconify-icon>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center text-muted py-4">
                                <iconify-icon icon="solar:document-text-bold-duotone" width="32" class="d-block mx-auto mb-2 text-muted"></iconify-icon>
                                No lead billing records found yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr class="fw-bold">
                            <td colspan="8" class="text-end">Total Lead Revenue:</td>
                            <td colspan="3">${{ number_format($leadBillingData->where('transaction_type', '!=', 'lead_refund')->sum('credit') - $leadBillingData->where('transaction_type', 'lead_refund')->sum('credit'), 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

  
</div>


<!-- SUB TAB 4: DYNAMIC PRICING & DISCOUNT CAPS -->
<div class="sub-tab-pane d-none" id="sub-pricing">
    <div class="row g-3 mb-4">
        <!-- Dynamic Pricing Rules Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">📈 Dynamic Pricing Engine</h5>
                </div>
                <div class="card-body">
                    <form id="dynamicPricingRulesForm" onsubmit="savePricingDiscountRules(event)">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="dynamicPricingEnabled" name="dynamic_pricing_enabled" value="1" {{ ($settings['dynamic_pricing_enabled'] ?? 0) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">Enable AI-Powered Dynamic Pricing</label>
                            </div>
                            <div class="form-text text-muted small">Auto-adjust item price based on demand, inventory level, and external factors.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pricing Strategy</label>
                            <select class="form-select form-select-sm" id="dynamicPricingStrategy" name="dynamic_pricing_strategy">
                                <option value="demand" {{ ($settings['dynamic_pricing_strategy'] ?? 'demand') == 'demand' ? 'selected' : '' }}>Demand-Based Pricing (Surge/Off-Peak)</option>
                                <option value="inventory" {{ ($settings['dynamic_pricing_strategy'] ?? '') == 'inventory' ? 'selected' : '' }}>Inventory-Level Adjustment (Clearance/Scarcity)</option>
                                <option value="competitor" {{ ($settings['dynamic_pricing_strategy'] ?? '') == 'competitor' ? 'selected' : '' }}>Competitor Matching (Match Lowest Online)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Max Price Adjustment Limit</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">&plusmn;</span>
                                <input type="number" class="form-control" id="priceAdjustLimit" name="price_adjust_limit" value="{{ $settings['price_adjust_limit'] ?? 15 }}" min="1" max="100">
                                <span class="input-group-text">% of original price</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Calculation Frequency</label>
                            <select class="form-select form-select-sm" id="priceUpdateFreq" name="price_update_freq">
                                <option value="realtime" {{ ($settings['price_update_freq'] ?? '') == 'realtime' ? 'selected' : '' }}>Real-time / Instant</option>
                                <option value="daily" {{ ($settings['price_update_freq'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily Recalculation</option>
                                <option value="weekly" {{ ($settings['price_update_freq'] ?? '') == 'weekly' ? 'selected' : '' }}>Weekly Recalculation</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm w-100">💾 Save Pricing Engine Rules</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Discount Cap Controls Card -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">🛑 Discount Cap Control</h5>
                </div>
                <div class="card-body">
                    <form id="discountCapRulesForm" onsubmit="savePricingDiscountRules(event)">
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" id="discountCapEnabled" name="discount_cap_enabled" value="1" {{ ($settings['discount_cap_enabled'] ?? 1) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-bold">Enable Strict Discount Cap Enforcement</label>
                            </div>
                            <div class="form-text text-muted small">Prevents coupon code and marketing discount stacking.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Maximum Allowed Discount Limit</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" id="discountCapPercent" name="discount_cap_percent" value="{{ $settings['discount_cap_percent'] ?? 30 }}" min="1" max="100">
                                <span class="input-group-text">% of product listing price</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Maximum Coupon Cap Value</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text">₹</span>
                                <input type="number" class="form-control" id="couponDiscountCap" name="coupon_discount_cap" value="{{ $settings['coupon_discount_cap'] ?? 500 }}" min="1">
                                <span class="input-group-text">max absolute discount per item</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Exceeded Cap Action</label>
                            <select class="form-select form-select-sm" id="discountCapAction" name="discount_cap_action">
                                <option value="adjust" {{ ($settings['discount_cap_action'] ?? 'adjust') == 'adjust' ? 'selected' : '' }}>Auto-Adjust (reduce discount to fit cap limit)</option>
                                <option value="block" {{ ($settings['discount_cap_action'] ?? '') == 'block' ? 'selected' : '' }}>Block Checkout (show warning modal)</option>
                                <option value="review" {{ ($settings['discount_cap_action'] ?? '') == 'review' ? 'selected' : '' }}>Flag Order (allow purchase but hold settlement)</option>
                            </select>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm w-100">💾 Save Discount Cap Controls</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


            </div>
        </div>

    <!-- EARNING TAB -->
<div class="tab-pane fade" id="earning-tab">
    <!-- Ledger Filters & Title -->
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <div>
        <h5 class="fw-bold text-primary mb-0">Transaction & Settlement Ledger</h5>
        <p class="text-muted fs-8 mb-0">Process daily payments, commission remits, and view balance details.</p>
    </div>
    <div class="d-flex gap-2">
        <input type="date" class="form-control form-control-sm" id="ledgerStartDate" placeholder="Start Date" style="width: 130px;">
        <input type="date" class="form-control form-control-sm" id="ledgerEndDate" placeholder="End Date" style="width: 130px;">
        <select class="form-select form-select-sm" id="ledgerAppFilter" style="width: 130px;">
            <option value="">All Applications</option>
            <option value="vshop">vShop</option>
            <option value="vtravel">vTravel</option>
            <option value="vcampus">vCampus</option>
            <option value="vedge">vEdge</option>
        </select>
    </div>
</div>

    <!-- Ledger KPI Cards - 5 Cards with Settlement Tracker -->
    <div class="row g-3 mb-4">
        <div class="col-xl col-sm-6">
            <div class="kpi-card shadow-sm border-0 card bg-white border-start border-primary border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Total Transactions</h6>
                            <h3 class="mb-0 fw-bold text-primary">{{ number_format($totalTransactions) }}</h3>
                            <div class="fs-8 text-muted mt-1">Processed transactions ledger log</div>
                        </div>
                        <div class="icon-box bg-primary-soft p-2 rounded-circle" style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;">
                            <iconify-icon icon="solar:list-bold-duotone" width="22" class="text-primary"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-sm-6">
            <div class="kpi-card shadow-sm border-0 card bg-white border-start border-success border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Total Credited</h6>
                            <h3 class="mb-0 fw-bold text-success">{{ $currencySymbol }}{{ number_format($totalCredited, 2) }}</h3>
                            <div class="fs-8 text-muted mt-1">Incoming credits & commissions</div>
                        </div>
                        <div class="icon-box bg-success-soft p-2 rounded-circle" style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;">
                            <iconify-icon icon="solar:arrow-right-up-bold-duotone" width="22" class="text-success"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-sm-6">
            <div class="kpi-card shadow-sm border-0 card bg-white border-start border-danger border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Total Debited</h6>
                            <h3 class="mb-0 fw-bold text-danger">{{ $currencySymbol }}{{ number_format($totalDebited, 2) }}</h3>
                            <div class="fs-8 text-muted mt-1">Outgoing payouts & refunds</div>
                        </div>
                        <div class="icon-box bg-danger-soft p-2 rounded-circle" style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;">
                            <iconify-icon icon="solar:arrow-left-down-bold-duotone" width="22" class="text-danger"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl col-sm-6">
            <div class="kpi-card shadow-sm border-0 card bg-white border-start border-info border-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1 fs-7">Closing Balance</h6>
                            <h3 class="mb-0 fw-bold text-info">{{ $currencySymbol }}{{ number_format($closingBalance, 2) }}</h3>
                            <div class="fs-8 text-muted mt-1">System escrow pool balance</div>
                        </div>
                        <div class="icon-box bg-info-soft p-2 rounded-circle" style="width:42px;height:42px;display:flex;align-items:center;justify-content:center;">
                            <iconify-icon icon="solar:wallet-money-bold-duotone" width="22" class="text-info"></iconify-icon>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- 5th KPI: Settlement Delay Tracker with Speedometer -->
        <div class="col-xl col-sm-6">
            <div class="kpi-card shadow-sm border-0 card bg-white border-start border-warning border-3" style="cursor: pointer; transition: transform 0.2s, box-shadow 0.2s;" onclick="showSettlementHoldModal()" onmouseover="this.style.transform='scale(1.02)';" onmouseout="this.style.transform='scale(1)';" title="Click to view all sellers' hold & delay history">
                <div class="card-body text-center p-2">
                    <h6 class="text-muted mb-1 fs-7 fw-bold" style="color:#0057b8 !important;">Settlement Delay Tracker</h6>
                    @php
                        $holdDaysSetting = (int)($settings['hold_days'] ?? 7);
                        $avgDelayDays = 0;
                        if($pendingPayoutsList->count() > 0) {
                            $avgDelayDays = $pendingPayoutsList->avg(function($p) {
                                return $p->created_at ? now()->diffInDays($p->created_at) : 0;
                            });
                        }
                        $avgDelayDays = round($avgDelayDays);
                        $maxGauge = max($holdDaysSetting * 2, 10);
                        $gaugePercent = min(100, ($avgDelayDays / $maxGauge) * 100);
                        $needleAngle = -90 + ($gaugePercent * 180 / 100);
                        $delayStatus = $avgDelayDays == 0 ? 'On Time' : ($avgDelayDays <= $holdDaysSetting ? 'Within SLA' : 'Delayed');
                        $statusColor = $avgDelayDays == 0 ? '#20c997' : ($avgDelayDays <= $holdDaysSetting ? '#ffc107' : '#dc3545');
                    @endphp
                    <!-- SVG Speedometer Gauge -->
                    <svg viewBox="0 0 200 120" style="width:140px;height:84px;" xmlns="http://www.w3.org/2000/svg">
                        <!-- Background arc segments: green->yellow->orange->red -->
                        <!-- Green segment -->
                        <path d="M 20 100 A 80 80 0 0 1 50 27.6" fill="none" stroke="#20c997" stroke-width="14" stroke-linecap="round"/>
                        <!-- Yellow segment -->
                        <path d="M 50 27.6 A 80 80 0 0 1 100 20" fill="none" stroke="#ffc107" stroke-width="14" stroke-linecap="round"/>
                        <!-- Orange segment -->
                        <path d="M 100 20 A 80 80 0 0 1 150 27.6" fill="none" stroke="#fd7e14" stroke-width="14" stroke-linecap="round"/>
                        <!-- Red segment -->
                        <path d="M 150 27.6 A 80 80 0 0 1 180 100" fill="none" stroke="#dc3545" stroke-width="14" stroke-linecap="round"/>
                        <!-- Center cap -->
                        <circle cx="100" cy="100" r="8" fill="#495057"/>
                        <!-- Needle -->
                        <line
                            x1="100" y1="100"
                            x2="{{ 100 + 60 * cos(deg2rad($needleAngle)) }}"
                            y2="{{ 100 + 60 * sin(deg2rad($needleAngle)) }}"
                            stroke="#3f51b5" stroke-width="3" stroke-linecap="round"
                        />
                        <!-- Labels -->
                        <text x="14" y="115" font-size="9" fill="#6c757d">0</text>
                        <text x="38" y="22" font-size="9" fill="#6c757d">{{ round($maxGauge * 0.25) }}</text>
                        <text x="90" y="14" font-size="9" fill="#6c757d">{{ round($maxGauge * 0.5) }}</text>
                        <text x="143" y="22" font-size="9" fill="#6c757d">{{ round($maxGauge * 0.75) }}</text>
                        <text x="167" y="115" font-size="9" fill="#6c757d">{{ $maxGauge }}</text>
                        <!-- Center value -->
                        <text x="100" y="92" text-anchor="middle" font-size="12" font-weight="bold" fill="#212529">{{ $avgDelayDays }} Days</text>
                    </svg>
                    <div class="fw-bold fs-6 mt-0" style="color:#212529;">{{ $avgDelayDays }} Days</div>
                    <div class="fs-8 text-muted">Cycle: {{ $holdDaysSetting }} Days</div>
                    <span class="badge mt-1" style="background-color:{{ $statusColor }}20; color:{{ $statusColor }}; font-size:10px; font-weight:600;">{{ $delayStatus }}</span>
                    <div class="mt-2 border-top pt-2">
                        <span class="text-primary text-decoration-none fw-bold" style="font-size: 11px;">
                            <iconify-icon icon="solar:history-bold-duotone" class="align-middle me-1" style="font-size: 14px;"></iconify-icon> View Hold History ➔
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>


<!-- SELLER-WISE EARNINGS, TAX & PAYOUTS LINE ITEMS -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white border-0 pt-3 pb-0 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-primary">SELLER-WISE EARNINGS, TAX & PAYOUTS (Line Items)</h5>
            <p class="text-muted fs-8 mb-0">Detailed view of seller total sales, platform commissions, payment hold days, taxes, and net payouts.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary btn-sm" onclick="bulkApprovePayouts()">
                <iconify-icon icon="solar:check-read-bold-duotone" class="align-middle me-1"></iconify-icon> Bulk Approve
            </button>
            <button class="btn btn-primary btn-sm" onclick="openBulkUpdateModal()">
                <iconify-icon icon="solar:pen-bold-duotone" class="align-middle me-1"></iconify-icon> Bulk Update
            </button>
            <button class="btn btn-outline-primary btn-sm" onclick="exportEarningsLedger()">
                <iconify-icon icon="solar:file-excel-bold-duotone" class="align-middle me-1"></iconify-icon> Export Ledger
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle custom-earnings-table" id="earningsPayoutTable">
                <thead>
                    <tr>
                        <th class="col-payout-check text-center"><input type="checkbox" id="selectAllPayouts" style="margin:0;"></th>
                        <th class="col-payout-seller">Seller ID & Location</th>
                        <th class="col-payout-app">App</th>
                        <th class="col-payout-sales">Total Sales</th>
                        <th class="col-payout-comm">Platform Commission</th>
                       <th class="col-payout-breakdown">
    Seller Wise Earning <br>
    (Admin Earning)
</th>
                        <th class="col-payout-payout">Seller Payout</th>
                        <th class="col-payout-hold" style="min-width: 100px !important;">Hold Days</th>
                        <th class="col-payout-schedule" style="min-width: 160px !important;">Payout Date</th>
                        <th class="col-payout-status">Payout Status</th>
                        <th class="col-payout-ref">Referral Payout</th>
                        <th class="col-payout-ref-sch" style="min-width: 160px !important;">Referral Date</th>
                        <th class="col-payout-ref-status">Referral Status</th>
                        <th class="col-payout-tax">Est. Tax (Draft)</th>
                        <th class="col-payout-net">Net Payout</th>
                        <th class="col-payout-bal">Remaining Balance</th>
                        <th class="col-payout-action text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($earningsDetails as $row)
                    <tr data-transaction-id="{{ $row['id'] }}">
                        <td class="col-payout-check text-center"><input type="checkbox" class="payout-checkbox" style="margin:0;" value="{{ $row['id'] }}"></td>
                        <td class="col-payout-seller">
                            <a href="#" class="fw-bold text-primary" onclick="showSellerDetails({{ $row['seller_id'] }})">
                                {{ $row['seller_code'] }} ({{ $row['location'] }})
                            </a>
                        </td>
                        <td class="col-payout-app">
                            @if(is_array($row['app']))
                                @foreach($row['app'] as $prod)
                                    <div class="d-flex align-items-center mb-1">
                                        <img src="{{ $prod['image'] }}" alt="img" class="rounded me-2 border" style="width: 24px; height: 24px; object-fit: cover;">
                                        <span class="text-dark fw-medium text-truncate" style="max-width: 150px; font-size: 11px;" title="{{ $prod['name'] }}">{{ $prod['name'] }}</span>
                                    </div>
                                @endforeach
                            @else
                                <span class="badge bg-secondary-soft text-secondary text-uppercase">{{ $row['app'] }}</span>
                            @endif
                        </td>
                        <td class="col-payout-sales">{{ $currencySymbol }}{{ number_format($row['total_sales'], 2) }}</td>
                        <td class="col-payout-comm">{{ $currencySymbol }}{{ number_format($row['platform_commission'], 2) }}</td>
                        <td class="col-payout-breakdown">
                            <a href="#" class="d-block text-decoration-none text-dark hover-breakdown-cell" onclick="showEarningBreakdown({{ $row['seller_id'] }})">
                                <div class="text-start fs-8 lh-sm font-monospace py-1 px-2 border rounded bg-light bg-opacity-70">
                                    <span class="d-block text-success fw-semibold">Total Sales {{ $currencySymbol }}{{ number_format($row['total_sales'], 2) }}</span>
                                </div>
                            </a>
                        </td>
                        <td class="col-payout-payout">{{ $currencySymbol }}{{ number_format($row['seller_payout'], 2) }}</td>
                        
                        <!-- Inline hold days -->
                        <td class="col-payout-hold">
                            <input type="number" class="form-control form-control-sm hold-days-input" style="max-width: 70px;" value="{{ $row['hold_days'] }}">
                        </td>
                        
                        <!-- Payout Date - Professional Date Picker -->
                        <td class="col-payout-schedule">
                            <div class="position-relative">
                                <input type="date" class="form-control form-control-sm payout-date-input" 
                                       style="min-width: 140px; font-size:12px; padding: 6px 10px; border-radius: 6px; border: 1px solid #e2e8f0; background-color: #fff;"
                                       value="{{ $row['release_date'] ?? date('Y-m-d') }}">
                                <iconify-icon icon="solar:calendar-bold-duotone" class="position-absolute" 
                                             style="right: 10px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #94a3b8; pointer-events: none;"></iconify-icon>
                            </div>
                         </td>

                        <td class="col-payout-status">
                            <span class="badge bg-{{ in_array(strtolower($row['payout_status']), ['approved', 'success', 'released']) ? 'success' : (strtolower($row['payout_status']) == 'pending' ? 'warning' : 'info') }}-soft text-{{ in_array(strtolower($row['payout_status']), ['approved', 'success', 'released']) ? 'success' : (strtolower($row['payout_status']) == 'pending' ? 'warning' : 'info') }}">
                                {{ $row['payout_status'] }}
                            </span>
                        </td>
                        <td class="col-payout-ref">{{ $currencySymbol }}{{ number_format($row['referral_payout'], 2) }}</td>
                        
                        <!-- Referral Date - Professional Date Picker -->
                        <td class="col-payout-ref-sch">
                            <div class="position-relative">
                                <input type="date" class="form-control form-control-sm referral-date-input" 
                                       style="min-width: 140px; font-size:12px; padding: 6px 10px; border-radius: 6px; border: 1px solid #e2e8f0; background-color: #fff;"
                                       value="{{ $row['referral_date'] ?? date('Y-m-d') }}">
                                <iconify-icon icon="solar:calendar-bold-duotone" class="position-absolute" 
                                             style="right: 10px; top: 50%; transform: translateY(-50%); font-size: 14px; color: #94a3b8; pointer-events: none;"></iconify-icon>
                            </div>
                         </td>
                        
                        <td class="col-payout-ref-status">
                            <span class="badge bg-success-soft text-success">{{ $row['referral_status'] }}</span>
                        </td>
                        <td class="col-payout-tax">
                            <a href="#" class="text-decoration-underline text-info" onclick="showTaxBreakdown({{ $row['seller_id'] }})">
                                {{ $currencySymbol }}{{ number_format($row['est_tax'], 2) }}
                            </a>
                        </td>
                        <td class="col-payout-net">
                            <a href="#" class="fw-bold text-success text-decoration-underline" onclick="showPayoutModal({{ $row['seller_id'] }})">
                                {{ $currencySymbol }}{{ number_format($row['net_payout'], 2) }}
                            </a>
                        </td>
                        <td class="col-payout-bal">{{ $currencySymbol }}{{ number_format($row['remaining_balance'], 2) }}</td>
                        
                        <td class="col-payout-action text-center">
                            <div class="d-flex gap-1 justify-content-center align-items-center">
                                <button type="button" class="btn btn-sm btn-outline-success py-1 px-2" title="Manual Release" onclick="manualRelease({{ $row['id'] }}, this)" {{ strtolower($row['payout_status']) === 'hold' ? '' : 'disabled' }}>
                                    <iconify-icon icon="solar:check-circle-bold-duotone" class="align-middle"></iconify-icon> Release
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<!-- Professional Modal for Payout Details -->
<div class="modal fade" id="payoutModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-2">
                        <iconify-icon icon="solar:wallet-bold-duotone" width="24" class="text-white"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Payout Details</h5>
                        <p class="mb-0 small opacity-75">Review and process seller payout information</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="payoutModalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted">Loading payout details...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:close-circle-bold-duotone" width="16" class="me-1"></iconify-icon> Close
                </button>
                <button type="button" class="btn btn-primary" onclick="processPayoutApproval()">
                    <iconify-icon icon="solar:check-read-bold-duotone" width="16" class="me-1"></iconify-icon> Process Payout
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Professional Modal for Tax Breakdown -->
<div class="modal fade" id="taxModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-info text-white border-0" style="background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-2">
                        <iconify-icon icon="solar:document-bold-duotone" width="24" class="text-white"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Tax Breakdown</h5>
                        <p class="mb-0 small opacity-75">Detailed tax calculation for seller</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="taxModalContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-info"></div>
                    <p class="mt-2 text-muted">Loading tax details...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:close-circle-bold-duotone" width="16" class="me-1"></iconify-icon> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Professional Modal for Earning Breakdown -->
<div class="modal fade" id="earningBreakdownModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-2">
                        <iconify-icon icon="solar:chart-2-bold-duotone" width="24" class="text-white"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">Earning Breakdown</h5>
                        <p class="mb-0 small opacity-75">Detailed earning calculation for seller</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="earningBreakdownContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary"></div>
                    <p class="mt-2 text-muted">Loading earning details...</p>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:close-circle-bold-duotone" width="16" class="me-1"></iconify-icon> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Settlement Hold Tracker (All Sellers) -->
<div class="modal fade" id="settlementHoldModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-2">
                        <iconify-icon icon="solar:clock-circle-bold-duotone" width="24" class="text-white"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Settlement & Payout Delay Tracker</h5>
                        <p class="mb-0 small text-white opacity-85">Monitor current payout release dates, delay windows, and SLA compliance</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between mb-3">
                    <input type="text" id="searchHoldTracker" class="form-control form-control-sm w-25" placeholder="Search Seller..." oninput="filterHoldTracker()">
                    <select id="sortHoldTracker" class="form-select form-select-sm w-25">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                        <option value="longest">Longest Hold</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0" id="settlementHoldTable">
                        <thead class="table-light">
                            <tr>
                                <th>Seller Code</th>
                                <th>Seller Name</th>
                                <th>App Platform</th>
                                <th class="text-center">Hold Period</th>
                                <th>Recorded Date</th>
                                <th>Scheduled Release</th>
                                <th>Reason</th>
                                <th class="text-center">Remaining / Delay</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="settlementHoldTableBody">
                            <!-- Populated dynamically via JS -->
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:close-circle-bold-duotone" width="16" class="me-1"></iconify-icon> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Individual Seller Settlement Flow Details -->
<div class="modal fade" id="sellerDetailsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-2">
                        <iconify-icon icon="solar:route-bold-duotone" width="24" class="text-white"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="flowModalTitle">Settlement Flow & Status</h5>
                        <p class="mb-0 small opacity-85">Live escrow pipeline status, rules and settlement details</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4" id="sellerDetailsModalContent">
                <!-- Populated dynamically via JS -->
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:close-circle-bold-duotone" width="16" class="me-1"></iconify-icon> Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Hold Reason Tracking -->
<div class="modal fade" id="holdReasonModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-gradient-primary text-white border-0" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
                <div class="d-flex align-items-center">
                    <div class="rounded-circle bg-white bg-opacity-20 p-2 me-2">
                        <iconify-icon icon="solar:clipboard-list-bold-duotone" width="24" class="text-white"></iconify-icon>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">Hold Reason Tracking</h5>
                        <p class="mb-0 small text-white opacity-85">Track reasons for settlement holds</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="d-flex justify-content-between mb-3">
                    <input type="text" id="searchHoldReason" class="form-control form-control-sm w-25" placeholder="Search Reason..." oninput="filterHoldReasons()">
                    <select id="sortHoldReason" class="form-select form-select-sm w-25">
                        <option value="newest">Newest First</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle mb-0" id="holdReasonTable">
                        <thead class="table-light">
                            <tr>
                                <th>Seller Code</th>
                                <th>Seller Name</th>
                                <th>Hold Reason</th>
                                <th>Applied By</th>
                                <th>Date Applied</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody id="holdReasonTableBody">
                            <!-- Populated dynamically via JS -->
                            <tr><td colspan="6" class="text-center text-muted">No hold reasons found.</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 py-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <iconify-icon icon="solar:close-circle-bold-duotone" width="16" class="me-1"></iconify-icon> Close
                </button>
            </div>
        </div>
    </div>
</div>



    <!-- TRANSACTION LEDGER (Full Dynamic) -->
<div class="card border-0 shadow-sm mb-3">
    <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-primary">Ledger Entries</h5>
            <p class="text-muted fs-8 mb-0">All financial transactions — credits, debits, payouts, refunds &amp; holds.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <select class="form-select form-select-sm" id="ledgerTypeFilter" style="width:140px;" onchange="filterLedgerTable()">
                <option value="">All Types</option>
                <option value="payout">Payout</option>
                <option value="refund">Refund</option>
                <option value="commission">Commission</option>
                <option value="lead_fee">Lead Fee</option>
                <option value="hold">Hold</option>
                <option value="referral">Referral</option>
                <option value="earning">Earning</option>
            </select>
            <span class="badge bg-primary-soft text-primary">{{ $transactions->count() }} Records</span>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle mb-0" id="ledgerEntriesTable" style="min-width:900px;">
                <thead>
                    <tr style="background:linear-gradient(135deg,#f0f4ff,#e8f0fe);">
                        <th style="min-width:130px; padding:12px 10px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#3c4d6e;">
                            <iconify-icon icon="solar:calendar-bold-duotone" class="align-middle me-1"></iconify-icon> Date
                        </th>
                        <th style="min-width:110px; padding:12px 10px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#3c4d6e;">
                            <iconify-icon icon="solar:hashtag-bold" class="align-middle me-1"></iconify-icon> Txn ID
                        </th>
                        <th style="min-width:140px; padding:12px 10px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#3c4d6e;">
                            <iconify-icon icon="solar:user-bold-duotone" class="align-middle me-1"></iconify-icon> Entity
                        </th>
                        <th style="min-width:160px; padding:12px 10px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#3c4d6e;">
                            <iconify-icon icon="solar:document-text-bold-duotone" class="align-middle me-1"></iconify-icon> PARTICULARS
                        </th>
                        <th style="min-width:120px; padding:12px 10px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#3c4d6e;">
                            <iconify-icon icon="solar:card-recive-bold-duotone" class="align-middle me-1"></iconify-icon> DEBIT (₹)
                        </th>
                        <th style="min-width:120px; padding:12px 10px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#3c4d6e;">
                            <iconify-icon icon="solar:card-send-bold-duotone" class="align-middle me-1"></iconify-icon> CREDIT (₹)
                        </th>
                        <th style="min-width:130px; padding:12px 10px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#3c4d6e;">
                            <iconify-icon icon="solar:wallet-bold-duotone" class="align-middle me-1"></iconify-icon> REMAINING BAL
                        </th>
                        <th style="min-width:100px; padding:12px 10px; font-size:11px; text-transform:uppercase; letter-spacing:0.5px; color:#3c4d6e; text-align:center;">
                            Status
                        </th>
                    </tr>
                </thead>
                <tbody id="ledgerTableBody">
                    @php
                        $runningBalance = 0;
                    @endphp
                    @forelse($transactions as $t)
                    @php
                        // Calculate running balance
                        if($t->credit > 0) {
                            $runningBalance += $t->credit;
                        }
                        if($t->debit > 0) {
                            $runningBalance -= $t->debit;
                        }
                        
                        // Determine entity display based on transaction type
                        $entityDisplay = '';
                        if($t->transaction_type == 'payout') {
                            $entityDisplay = $t->seller_id ?? 'N/A';
                        } elseif($t->transaction_type == 'referral') {
                            $entityDisplay = $t->referral_id ?? 'N/A';
                        } else {
                            $entityDisplay = $t->seller->name ?? ($t->seller_id ?? 'System');
                        }
                        
                        // Build particulars/description
                        $particulars = '';
                        if($t->transaction_type == 'payout') {
                            $particulars = 'Payout to Bank - ' . ($t->bank_name ?? 'Bank Account') . ' (XXXX' . substr($t->account_no ?? '', -4) . ')';
                        } elseif($t->transaction_type == 'referral') {
                            $particulars = 'Referral Commission - ' . ($t->referral_name ?? 'Seller') . ' (Ref ID: ' . ($t->referral_code ?? 'N/A') . ')';
                        } elseif($t->transaction_type == 'earning') {
                            $particulars = 'Order #' . ($t->order_id ?? 'N/A') . ' - Sale Earning';
                        } elseif($t->transaction_type == 'refund') {
                            $particulars = 'Refund Credit - ' . ($t->description ?? 'Order Refund');
                        } elseif($t->transaction_type == 'lead_fee') {
                            $particulars = 'Lead Charge - ' . ($t->lead_type ?? 'Click') . ' (' . ($t->category ?? 'General') . ')';
                        } elseif($t->transaction_type == 'commission') {
                            $particulars = 'Platform Fee Deduct - ' . ($t->percentage ?? '5') . '% on sales';
                        } else {
                            $particulars = $t->description ?? ($t->note ?? 'Finance Transaction');
                        }
                        
                        // Add additional details if available
                        if($t->order_id && !in_array($t->transaction_type, ['earning', 'payout'])) {
                            $particulars .= ' - Order #' . $t->order_id;
                        }
                    @endphp
                    <tr data-type="{{ $t->transaction_type }}" style="transition:background 0.15s;">
                        <td style="font-size:12px;">
                            <div class="fw-semibold">{{ $t->created_at ? $t->created_at->format('d-m-Y') : 'N/A' }}</div>
                            <div class="text-muted" style="font-size:10px;">{{ $t->created_at ? $t->created_at->format('h:i:s A') : '' }}</div>
                        </td>
                        <td>
                            <span class="font-monospace fw-bold text-primary" style="font-size:11px;">#TXN-{{ str_pad($t->id, 5, '0', STR_PAD_LEFT) }}</span>
                            @if($t->order_id && $t->transaction_type != 'earning')
                            <div class="text-muted" style="font-size:10px;">Order #{{ $t->order_id }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold" style="font-size:12px;">{{ $entityDisplay }}</div>
                            @if($t->transaction_type == 'payout' && $t->seller_id)
                            <div class="text-muted" style="font-size:10px;">Seller ID: S-{{ $t->seller_id }}</div>
                            @endif
                            @if($t->transaction_type == 'referral' && $t->referral_id)
                            <div class="text-muted" style="font-size:10px;">Referral ID: R-{{ $t->referral_id }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px; color:#495057;">
                            <div class="fw-semibold">{{ $particulars }}</div>
                            @php
                                $typeIcons = [
                                    'payout'=>'🏦','refund'=>'↩️','commission'=>'📊',
                                    'lead_fee'=>'🖱️','hold'=>'⏸️','subscription'=>'🔄',
                                    'referral'=>'🤝','earning'=>'💰','click'=>'👆','enquiry'=>'❓'
                                ];
                                $icon = $typeIcons[$t->transaction_type] ?? '📝';
                            @endphp
                            <small class="text-muted">{{ $icon }} {{ ucfirst(str_replace('_',' ',$t->transaction_type)) }}</small>
                        </td>
                        <td>
                            @if($t->debit > 0)
                            <span class="fw-bold text-danger">₹{{ number_format($t->debit, 2) }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($t->credit > 0)
                            <span class="fw-bold text-success">₹{{ number_format($t->credit, 2) }}</span>
                            @else
                            <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="fw-bold text-primary">₹{{ number_format($runningBalance, 2) }}</span>
                        </td>
                        <td class="text-center">
                            @php
                                $statusMap = ['completed'=>'success','pending'=>'warning','failed'=>'danger','rejected'=>'danger','released'=>'info','scheduled'=>'primary','success'=>'success','disputed'=>'danger'];
                                $sColor = $statusMap[$t->status ?? 'pending'] ?? 'secondary';
                                $statusText = ucfirst($t->status ?? 'Pending');
                                if($t->status == 'disputed') $statusText = 'Disputed';
                                if($t->status == 'completed') $statusText = 'Success';
                            @endphp
                            <span class="badge bg-{{ $sColor }}-soft text-{{ $sColor }}" style="font-size:10px; padding:5px 10px;">
                                {{ $statusText }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <iconify-icon icon="solar:inbox-bold-duotone" width="32" class="mb-2 d-block mx-auto text-muted"></iconify-icon>
                            No transaction records found.
                        </td>
                    </tr>
                    @endforelse
                    
                    <!-- Total Row -->
                    @if($transactions->count() > 0)
                    <tr style="background: #f8f9fa; border-top: 2px solid #dee2e6;">
                        <td colspan="4" class="text-end fw-bold" style="font-size:13px;">
                            <strong>TOTAL</strong>
                        </td>
                        <td class="fw-bold text-danger">
                            ₹{{ number_format($transactions->sum('debit'), 2) }}
                        </td>
                        <td class="fw-bold text-success">
                            ₹{{ number_format($transactions->sum('credit'), 2) }}
                        </td>
                        <td class="fw-bold text-primary">
                            ₹{{ number_format($runningBalance, 2) }}
                        </td>
                        <td></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <!-- Ledger Pagination -->
        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-top bg-light" id="ledgerPaginationBar">
            <span class="text-muted fs-8" id="ledgerPageInfo">
                Showing {{ $transactions->count() }} records
            </span>
            <div class="d-flex gap-1">
                <button class="btn btn-primary btn-sm" id="ledgerPrevBtn" onclick="ledgerPrevPage()">
                    <iconify-icon icon="solar:arrow-left-bold"></iconify-icon> Prev
                </button>
                <span class="btn btn-sm btn-light border px-3" id="ledgerPageNum">1</span>
                <button class="btn btn-primary btn-sm" id="ledgerNextBtn" onclick="ledgerNextPage()">
                    Next <iconify-icon icon="solar:arrow-right-bold"></iconify-icon>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function filterLedgerTable() {
    const filterValue = document.getElementById('ledgerTypeFilter').value;
    const rows = document.querySelectorAll('#ledgerTableBody tr');
    
    rows.forEach(row => {
        if (row.getAttribute('data-type') === filterValue || filterValue === '') {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}

function ledgerPrevPage() {
    // Implement pagination logic based on your backend
    let currentPage = parseInt(document.getElementById('ledgerPageNum').innerText);
    if(currentPage > 1) {
        window.location.href = '?page=' + (currentPage - 1);
    }
}

function ledgerNextPage() {
    let currentPage = parseInt(document.getElementById('ledgerPageNum').innerText);
    window.location.href = '?page=' + (currentPage + 1);
}
</script>
</div>

      <!-- REFUND & DISPUTE TAB -->
<div class="tab-pane fade" id="refund-dispute-tab">
    <!-- Sub Navigation Tabs (3 Tabs) -->
    <div class="d-flex gap-2 mb-3 flex-wrap">
        <button class="btn btn-outline-primary btn-sm active" id="btn-sub-payout" onclick="switchRefundSubTab('sub-payout')">
            <iconify-icon icon="solar:card-send-bold-duotone" class="align-middle me-1"></iconify-icon>Payout Approval
        </button>
        <button class="btn btn-outline-primary btn-sm" id="btn-sub-refund" onclick="switchRefundSubTab('sub-refund')">
            <iconify-icon icon="solar:refresh-circle-bold-duotone" class="align-middle me-1"></iconify-icon>Refund Control
        </button>
        <button class="btn btn-outline-primary btn-sm" id="btn-sub-dispute" onclick="switchRefundSubTab('sub-dispute')">
            <iconify-icon icon="solar:shield-warning-bold-duotone" class="align-middle me-1"></iconify-icon>Dispute Handling
        </button>
    </div>

    <div id="refund-sub-tab-content">
        <!-- SUB-TAB 1: Payout Approval Module -->
        <div class="refund-sub-pane" id="sub-payout">
            <div class="row g-3 mb-4">
                <div class="col-sm-6 col-md-3">
                    <div class="border rounded p-3 bg-light text-center">
                        <h6 class="text-muted fs-8 mb-1">Pending Payouts</h6>
                        <h3 class="fw-bold mb-0 text-warning">{{ $currencySymbol }}{{ number_format($pendingPayouts, 2) }}</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="border rounded p-3 bg-light text-center">
                        <h6 class="text-muted fs-8 mb-1">Approved Payouts</h6>
                        <h3 class="fw-bold mb-0 text-success">{{ $currencySymbol }}{{ number_format($totalPayouts, 2) }}</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="border rounded p-3 bg-light text-center">
                        <h6 class="text-muted fs-8 mb-1">Failed Payouts</h6>
                        <h3 class="fw-bold mb-0 text-danger">{{ $currencySymbol }}{{ number_format(count($failedPayouts) * 250, 2) }}</h3>
                    </div>
                </div>
                <div class="col-sm-6 col-md-3">
                    <div class="border rounded p-3 bg-light text-center">
                        <h6 class="text-muted fs-8 mb-1">Remittance SLA Target</h6>
                        <h3 class="fw-bold mb-0 text-primary">T+7 Days</h3>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">Failed Payout Remittance Log</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped align-middle" style="min-width: 500px;">
                            <thead class="table-light">
                                <tr><th>Date</th><th>Entity Name</th><th>Locked Amount</th><th>Reason / Error Code</th><th class="text-center">Action</th></tr>
                            </thead>
                            <tbody>
                                @foreach($failedPayouts as $fp)
                                <tr>
                                    <td>{{ $fp->created_at ? $fp->created_at->format('Y-m-d') : 'N/A' }}</div>
                                    <td>{{ $fp->seller->name ?? 'N/A' }}</div>
                                    <td class="fw-bold text-danger">{{ $currencySymbol }}{{ number_format($fp->debit, 2) }}</div>
                                    <td><span class="text-danger"><iconify-icon icon="solar:shield-warning-bold"></iconify-icon> Bank Gateway Timeout [500]</span></div>
                                    <td class="text-center"><button class="btn btn-primary btn-sm py-1" onclick="showToast('Gateway Payout Retry queued successfully', 'success')">Retry Transfer</button></div>
                                </tr>
                                @endforeach
                                @if($failedPayouts->isEmpty())
                                <tr><td colspan="5" class="text-center py-3 text-muted">No failed payout entries recorded.</div></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


<!-- SUB-TAB 2: Tax & Invoice Center -->
<div class="refund-sub-pane d-none" id="sub-tax">
    <form method="POST" action="{{ route('admin.finance.update-tax-rules') }}" id="taxSettingsForm" onsubmit="saveTaxInvoiceMain(''); return false;">
        @csrf
        
        <!-- First Row: Tax & Currency Settings -->
        <div class="row g-3 mb-4">
            
            <!-- Region-Based Tax Logic -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <iconify-icon icon="solar:global-bold-duotone" class="me-1"></iconify-icon>
                            Region-Based Tax Logic
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Default GST Rate</label>
                            <div class="input-group input-group-sm">
                                <input type="number" step="0.1" class="form-control" name="gst_rate" id="defaultTaxRateField" value="{{ $settings['gst_rate'] ?? 18 }}">
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                        
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-bordered table-sm align-middle mb-0" style="min-width: 500px;">
                                <thead class="table-light position-sticky top-0">
                                    <tr>
                                        <th style="width: 35%;">Region</th>
                                        <th style="width: 35%;">Tax Name</th>
                                        <th style="width: 20%;">Rate (%)</th>
                                        <th style="width: 10%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="regionTaxTableBody">
                                    @forelse($taxRegions as $index => $tr)
                                    <tr class="tax-region-row">
                                        <td>
                                            <select class="form-select form-select-sm" name="tax_regions[{{ $index }}][region]" required><option value="">Select Country</option>@foreach($countriesListArray as $c)<option value="{{ $c }}" {{ $tr['region'] == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="tax_regions[{{ $index }}][tax_name]" value="{{ $tr['tax_name'] }}" required>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.1" class="form-control" name="tax_regions[{{ $index }}][rate]" value="{{ $tr['rate'] }}" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-tax-region" data-index="{{ $index }}">
                                                <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="tax-region-row" id="defaultTaxRegionRow">
                                        <td>
                                            <select class="form-select form-select-sm" name="tax_regions[0][region]" required><option value="">Select Country</option>@foreach($countriesListArray as $c)<option value="{{ $c }}">{{ $c }}</option>@endforeach</select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="tax_regions[0][tax_name]" placeholder="e.g., GST" required>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.1" class="form-control" name="tax_regions[0][rate]" placeholder="18" required>
                                                <span class="input-group-text">%</span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">
                                                <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-sm w-100 mt-3" onclick="addTaxRegionRow()">
                            <iconify-icon icon="solar:add-circle-bold-duotone" class="align-middle me-1"></iconify-icon> + Add Region Rule
                        </button>
                    </div>
                </div>
            </div>

            <!-- Multi-Currency Support -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <iconify-icon icon="solar:currency-circle-bold-duotone" class="me-1"></iconify-icon>
                            Multi-Currency Support
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Base Currency</label>
                            <select class="form-select form-select-sm" name="base_currency" id="baseCurrencyField">
                                <option value="USD" {{ ($currency ?? 'INR') == 'USD' ? 'selected' : '' }}>USD ($)</option>
                                <option value="INR" {{ ($currency ?? 'INR') == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                                <option value="EUR" {{ ($currency ?? 'INR') == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                                <option value="GBP" {{ ($currency ?? 'INR') == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                            </select>
                        </div>
                        
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-bordered table-sm align-middle mb-0" style="min-width: 650px;">
                                <thead class="table-light position-sticky top-0">
                                    <tr>
                                        <th style="width: 25%;">Currency</th>
                                        <th style="width: 15%;">Symbol</th>
                                        <th style="width: 25%;">Exchange Rate</th>
                                        <th style="width: 25%;">Auto-Update</th>
                                        <th style="width: 10%;" class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="currencyTableBody">
                                    @forelse($currenciesList as $index => $c)
                                    <tr class="currency-row">
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="currencies[{{ $index }}][currency]" value="{{ $c['currency'] }}" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="currencies[{{ $index }}][symbol]" value="{{ $c['symbol'] }}" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.0001" class="form-control form-control-sm" name="currencies[{{ $index }}][rate]" value="{{ $c['rate'] }}" required>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="currencies[{{ $index }}][auto_update]">
                                                <option value="Fixed" {{ ($c['auto_update'] ?? 'Fixed') == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                                                <option value="Daily" {{ ($c['auto_update'] ?? '') == 'Daily' ? 'selected' : '' }}>Daily API</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-currency" data-index="{{ $index }}">
                                                <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr class="currency-row" id="defaultCurrencyRow">
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="currencies[0][currency]" placeholder="USD" required>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control form-control-sm" name="currencies[0][symbol]" placeholder="$" required>
                                        </td>
                                        <td>
                                            <input type="number" step="0.0001" class="form-control form-control-sm" name="currencies[0][rate]" placeholder="1.00" required>
                                        </td>
                                        <td>
                                            <select class="form-select form-select-sm" name="currencies[0][auto_update]">
                                                <option value="Fixed">Fixed</option>
                                                <option value="Daily">Daily API</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">
                                                <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <button type="button" class="btn btn-primary btn-sm w-100 mt-3" onclick="addCurrencyRow()">
                            <iconify-icon icon="solar:add-circle-bold-duotone" class="align-middle me-1"></iconify-icon> + Add Currency
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Invoice Template & Generation -->
        <div class="row g-3 mb-4">
            
            <!-- Invoice Template Settings -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <iconify-icon icon="solar:document-text-bold-duotone" class="me-1"></iconify-icon>
                            Invoice Template Settings
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-2">
                            <div class="col-sm-6">
                                <label class="form-label">Template Style</label>
                                <select class="form-select form-select-sm" name="template_style" id="templateStyleField">
                                    <option value="Simple" {{ ($invoiceTemplate['style'] ?? '') == 'Simple' ? 'selected' : '' }}>Simple</option>
                                    <option value="Professional" {{ ($invoiceTemplate['style'] ?? '') == 'Professional' ? 'selected' : '' }}>Professional</option>
                                    <option value="Modern" {{ ($invoiceTemplate['style'] ?? '') == 'Modern' ? 'selected' : '' }}>Modern</option>
                                </select>
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control form-control-sm" name="company_name" id="companyNameField" value="{{ $invoiceTemplate['companyName'] ?? '' }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">GST / Tax Number</label>
                                <input type="text" class="form-control form-control-sm" name="company_gst" id="companyGstField" value="{{ $invoiceTemplate['companyGst'] ?? '' }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Company Email</label>
                                <input type="email" class="form-control form-control-sm" name="company_email" id="companyEmailField" value="{{ $invoiceTemplate['companyEmail'] ?? '' }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Company Phone</label>
                                <input type="text" class="form-control form-control-sm" name="company_phone" id="companyPhoneField" value="{{ $invoiceTemplate['companyPhone'] ?? '' }}">
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Invoice Prefix</label>
                                <input type="text" class="form-control form-control-sm" name="invoice_prefix" id="invoicePrefixField" value="{{ $invoiceTemplate['invoicePrefix'] ?? 'INV' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Company Address</label>
                                <textarea class="form-control form-control-sm" name="company_address" id="companyAddressField" rows="2">{{ $invoiceTemplate['companyAddress'] ?? '' }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Footer Text</label>
                                <textarea class="form-control form-control-sm" name="invoice_footer" id="invoiceFooterField" rows="2">{{ $invoiceTemplate['invoiceFooter'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Invoice Generation & Reports -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <iconify-icon icon="solar:receipt-bold-duotone" class="me-1"></iconify-icon>
                            Invoice Generation & Reports
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Select Seller</label>
                            <select class="form-select form-select-sm" name="invoice_seller_id" id="invoiceSelectSeller">
                                <option value="">-- Select Seller --</option>
                                @foreach($sellers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }} (S-{{ $s->id }})</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label">Start Date</label>
                                <input type="date" class="form-control form-control-sm" name="invoice_start_date" id="invoiceStartDate">
                            </div>
                            <div class="col-6">
                                <label class="form-label">End Date</label>
                                <input type="date" class="form-control form-control-sm" name="invoice_end_date" id="invoiceEndDate">
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 mb-3">
                            <button type="button" class="btn btn-primary btn-sm w-50" onclick="generateInvoiceNew(1); return false;">
                                <iconify-icon icon="solar:eye-bold" class="me-1"></iconify-icon> Preview
                            </button>
                            <button type="button" class="btn btn-success btn-sm w-50" onclick="downloadInvoiceNew(1); return false;">
                                <iconify-icon icon="solar:file-download-bold" class="me-1"></iconify-icon> Download PDF
                            </button>
                        </div>
                        
                        <hr>
                        
                        <div class="mt-3">
                            <label class="form-label fw-semibold">Export Reports</label>
                            <div class="d-flex gap-2">
                                <select class="form-select form-select-sm" name="report_type" id="reportType">
                                    <option value="tax">Tax Summary Report</option>
                                    <option value="invoice">Invoice History Report</option>
                                    <option value="gst">GST Collection Report</option>
                                    <option value="currency">Currency Report</option>
                                </select>
                                <button type="button" class="btn btn-success btn-sm" onclick="exportReportNew(1); return false;">
                                    <iconify-icon icon="solar:file-excel-bold-duotone" class="me-1"></iconify-icon> Export
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Third Row: GST Validation -->
        <div class="row g-3 mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-0 pt-3">
                        <h5 class="mb-0 fw-bold text-primary">
                            <iconify-icon icon="solar:shield-check-bold-duotone" class="me-1"></iconify-icon>
                            GST Validation
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-6">
                                <label class="form-label">Test GST Number</label>
                                <div class="input-group input-group-sm">
                                    <input type="text" class="form-control" name="test_gst_number" id="testGstNumber" placeholder="22AAAAA0000A1Z5">
                                    <button type="submit" name="validate_gst" value="1" class="btn btn-primary">Validate</button>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">GST Format Pattern</label>
                            
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Save All Settings Button -->
        <div class="row">
            <div class="col-12">
                <button type="submit" name="save_all_settings" value="1" class="btn btn-primary shadow-sm w-100 py-2 fw-semibold">
                    <iconify-icon icon="solar:save-bold" class="align-middle me-1"></iconify-icon> Save All Tax & Invoice Settings
                </button>
            </div>
        </div>
    </form>
</div>  
        
        
        
        
        
        
        
        
        
        
        
        
        

<!-- SUB-TAB 3: Refund Control System (Enhanced) -->
<div class="refund-sub-pane d-none" id="sub-refund">
    <div class="row g-3">
        <!-- Left Column: Core Refund Settings -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">🔹 REFUND CONTROL SYSTEM</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <form id="refundRulesForm" onsubmit="saveRefundRules(event)">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Auto vs Manual Refund Toggle</label>
                            <select class="form-select form-select-sm" name="refund_mode" id="refundModeSelect">
                                <option value="auto" {{ ($settings['refund_mode'] ?? '') == 'auto' ? 'selected' : '' }}>Auto Payout Refund (System SLA)</option>
                                <option value="manual" {{ ($settings['refund_mode'] ?? '') == 'manual' ? 'selected' : '' }}>Manual Verification Required</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="partial_refund_enabled" id="partialRefundToggle" value="1" {{ ($settings['partial_refund_enabled'] ?? 1) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">Partial Refund Approval Enabled</label>
                            </div>
                            <div class="mt-2 ps-4" id="partialRefundConfig" style="display: none;">
                                <label class="form-label small">Max Partial Refund Percentage</label>
                                <div class="input-group input-group-sm">
                                    <input type="number" class="form-control" name="max_partial_percent" id="maxPartialPercent" value="{{ $settings['max_partial_percent'] ?? 50 }}" min="0" max="100">
                                    <span class="input-group-text">% of order value</span>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Refund SLA Tracking Target</label>
                            <div class="input-group input-group-sm">
                                <input type="number" class="form-control" name="refund_sla_days" id="refundSlaDays" value="{{ $settings['refund_sla_days'] ?? 7 }}">
                                <span class="input-group-text">days</span>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Refund Fraud Detection</label>
                            <select class="form-select form-select-sm" name="fraud_detection" id="fraudDetection">
                                <option value="basic" {{ ($settings['fraud_detection'] ?? '') == 'basic' ? 'selected' : '' }}>Basic (IP + Device Check)</option>
                                <option value="advanced" {{ ($settings['fraud_detection'] ?? 'advanced') == 'advanced' ? 'selected' : '' }}>Advanced (AI Pattern Match)</option>
                                <option value="strict" {{ ($settings['fraud_detection'] ?? '') == 'strict' ? 'selected' : '' }}>Strict (Manual Review All)</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <canvas id="refundReasonChart" height="120" style="max-height: 140px; width: 100%;"></canvas>
                        </div>

                        <hr class="my-3">

                        <h6 class="fw-bold mb-2">⚙️ Additional Refund Rules</h6>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="reverse_commission" id="reverseCommissionToggle" value="1" {{ ($settings['reverse_commission'] ?? 1) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">Reverse commission on refund</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="freeze_payout" id="freezePayoutToggle" value="1" {{ ($settings['freeze_payout'] ?? 1) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">Freeze payout on dispute</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="reverse_referral" id="reverseReferralToggle" value="1" {{ ($settings['reverse_referral'] ?? 1) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">Reverse referral rewards</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input type="checkbox" class="form-check-input" name="refund_lead_charges" id="refundLeadChargesToggle" value="1" {{ ($settings['refund_lead_charges'] ?? 1) == 1 ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold">Refund lead charges for fake leads</label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-3">
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm">💾 Save All Refund Rules</button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="resetRefundDefaults()">⟳ Reset to Defaults</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Refund Analytics & Impact (Compact) -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">📊 Refund Analytics & Impact</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <!-- 6 Compact Cards - 2 per row -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);">
                                <small class="text-white-50">Refund Rate</small>
                                <h4 class="text-white mb-0 fw-bold">{{ ($refundAmount > 0 && $totalRevenue > 0) ? number_format(($refundAmount / $totalRevenue * 100), 1) . '%' : '' }}</h4>
                                <small class="text-white-50">{{ ($refundAmount > 0 && $totalRevenue > 0) ? '↓ 2.1%' : '' }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                                <small class="text-white-50">Total Refunds</small>
                                <h4 class="text-white mb-0 fw-bold">{{ $refundAmount > 0 ? $currencySymbol . number_format($refundAmount, 2) : '' }}</h4>
                                <small class="text-white-50">{{ $refundTxnsCount > 0 ? $refundTxnsCount . ' txns' : '' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: linear-gradient(135deg, #27ae60 0%, #1e8449 100%);">
                                <small class="text-white-50">Avg SLA</small>
                                <h4 class="text-white mb-0 fw-bold">{{ $avgRefundSla ? number_format($avgRefundSla, 1) . 'd' : '' }}</h4>
                                <small class="text-white-50">{{ $avgRefundSla ? 'to resolve' : '' }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: linear-gradient(135deg, #f39c12 0%, #e67e22 100%);">
                                <small class="text-white-50">Recovered</small>
                                <h4 class="text-white mb-0 fw-bold">{{ $recoveredAmount > 0 ? $currencySymbol . number_format($recoveredAmount, 2) : '' }}</h4>
                                <small class="text-white-50">{{ $recoveredAmount > 0 ? 'reversals' : '' }}</small>
                            </div>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: linear-gradient(135deg, #9b59b6 0%, #8e44ad 100%);">
                                <small class="text-white-50">Commission Rev</small>
                                <h4 class="text-white mb-0 fw-bold">{{ $commissionReversedAmount > 0 ? $currencySymbol . number_format($commissionReversedAmount, 2) : '' }}</h4>
                                <small class="text-white-50">{{ $commissionReversedAmount > 0 ? 'reversed' : '' }}</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 rounded-3 text-center" style="background: linear-gradient(135deg, #1abc9c 0%, #16a085 100%);">
                                <small class="text-white-50">Fake Lead Ref</small>
                                <h4 class="text-white mb-0 fw-bold">{{ $leadRefundAmount > 0 ? $currencySymbol . number_format($leadRefundAmount, 2) : '' }}</h4>
                                <small class="text-white-50">{{ $leadRefundCount > 0 ? $leadRefundCount . ' leads' : '' }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Refund Trend Chart -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold fs-8">📈 Refund Trend</label>
                        <canvas id="refundTrendChart" height="100" style="width: 100%;"></canvas>
                    </div>

                    <!-- Refund Category Chart -->
                   
                    <!-- Recent Activity -->
                    <div>
                        <label class="form-label fw-semibold fs-8 mb-2">🔄 Recent Activity</label>
                        <div class="activity-timeline" style="max-height: 150px; overflow-y: auto;" id="recentActivityList">
                            <div class="text-center py-2 text-muted small">Loading...</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <!-- Refund Requests Table -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white border-0 pt-3">
        <h5 class="mb-0 fw-bold text-primary">🔹 Refund Requests & Logs</h5>
        <p class="text-muted fs-8 mb-0">Track customer returns, refund statuses, and SLA compliance targets.</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive" style="overflow-x: auto;">
            <table class="table table-hover table-bordered align-middle mb-0" style="min-width: 800px; white-space: nowrap;">
                <thead class="table-light">
                    <tr>
                        <th style="width: 10%;">Order ID</th>
                        <th style="width: 12%;">Seller</th>
                        <th style="width: 12%;">Customer</th>
                        <th style="width: 20%;">Reason</th>
                        <th style="width: 10%;">Amount</th>
                        <th style="width: 10%;">Status</th>
                        <th style="width: 12%;">Date</th>
                        <th style="width: 14%;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($refundOrders as $ro)
                    <tr>
                        <td class="fw-bold">#{{ $ro->id }}</td>
                        <td>{{ $ro->seller->name ?? 'N/A' }}</td>
                        <td>{{ $ro->customer->name ?? 'N/A' }}</td>
                        <td style="white-space: normal; word-wrap: break-word; min-width: 150px;">
                            <span title="{{ $ro->return_reason ?? 'No reason' }}">
                                {{ Str::limit($ro->return_reason ?? 'No reason', 40) }}
                            </span>
                        </td>
                        <td class="fw-bold text-danger">${{ number_format($ro->refund_amount ?: $ro->total_amount, 2) }}</td>
                        <td>
                            @php
                                $statusColor = match($ro->refund_status) {
                                    'completed' => 'success',
                                    'pending' => 'warning',
                                    'failed' => 'danger',
                                    default => 'secondary'
                                };
                            @endphp
                            <span class="badge bg-{{ $statusColor }}-soft text-{{ $statusColor }}" style="padding: 5px 10px;">
                                {{ ucfirst($ro->refund_status ?? 'pending') }}
                            </span>
                        </td>
                        <td>{{ $ro->created_at ? $ro->created_at->format('d M Y') : 'N/A' }}</td>
                        <td>
                            @if(($ro->refund_status ?? 'pending') == 'pending')
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-success" onclick="approveRefund({{ $ro->id }})">
                                    <iconify-icon icon="solar:check-circle-bold" width="14"></iconify-icon> Approve
                                </button>
                                <button class="btn btn-danger" onclick="rejectRefund({{ $ro->id }})">
                                    <iconify-icon icon="solar:close-circle-bold" width="14"></iconify-icon> Reject
                                </button>
                            </div>
                            @else
                            <span class="text-muted small">Processed</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">
                            <iconify-icon icon="solar:inbox-bold" width="32" class="mb-2 d-block mx-auto"></iconify-icon>
                            No refund requests recorded.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script>
// Initialize Charts
function initRefundCharts() {
    // Trend Chart - Dynamic data from controller
    const trendCtx = document.getElementById('refundTrendChart');
    if (trendCtx) {
        const weeklyData = {!! json_encode($refundTrendWeekly) !!};
        new Chart(trendCtx, {
            type: 'line',
            data: { labels: ['W1', 'W2', 'W3', 'W4'], datasets: [{ label: 'Refund ($)', data: weeklyData, borderColor: '#e74c3c', backgroundColor: 'rgba(231,76,60,0.1)', fill: true, tension: 0.4 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });
    }

    // Category Chart
    const catCtx = document.getElementById('refundCategoryChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'doughnut',
            data: { labels: {!! json_encode(array_keys($refundReasonsData)) !!}, datasets: [{ data: {!! json_encode(array_values($refundReasonsData)) !!}, backgroundColor: ['#3498db', '#e74c3c', '#27ae60', '#f39c12', '#9b59b6'] }] },
            options: { responsive: true, maintainAspectRatio: true, cutout: '60%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 9 } } } } }
        });
    }

    // Recent Activities - Dynamic from database
    const activityList = document.getElementById('recentActivityList');
    if (activityList) {
        @if($recentRefundActivity->count() > 0)
        activityList.innerHTML = `
            @foreach($recentRefundActivity as $ra)
            <div class="d-flex justify-content-between p-2 border-bottom">
                <span>{{ $ra->seller->name ?? 'Unknown' }}</span>
                <span class="text-danger">${{ number_format($ra->credit, 2) }}</span>
                <small class="text-muted">{{ $ra->created_at ? $ra->created_at->diffForHumans() : '-' }}</small>
            </div>
            @endforeach
        `;
        @else
        activityList.innerHTML = '<div class="text-center py-3 text-muted small">No recent refund activity</div>';
        @endif
    }
}

// Initialize Refund Reason Chart
function initRefundReasonChart() {
    const ctx = document.getElementById('refundReasonChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: { labels: {!! json_encode(array_keys($refundReasonsData)) !!}, datasets: [{ data: {!! json_encode(array_values($refundReasonsData)) !!}, backgroundColor: ['#dc3545', '#ffc107', '#0d6efd', '#20c997', '#6c757d'] }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'bottom', labels: { boxWidth: 8, font: { size: 9 } } } } }
        });
    }
}

// Show/hide partial refund config
document.getElementById('partialRefundToggle')?.addEventListener('change', function(e) {
    const configDiv = document.getElementById('partialRefundConfig');
    if (configDiv) configDiv.style.display = e.target.checked ? 'block' : 'none';
});

function resetRefundDefaults() {
    if (confirm('Reset all refund settings to default values?')) {
        document.getElementById('refundModeSelect').value = 'auto';
        document.getElementById('partialRefundToggle').checked = true;
        document.getElementById('maxPartialPercent').value = 50;
        document.getElementById('refundSlaDays').value = 7;
        document.getElementById('fraudDetection').value = 'advanced';
        document.getElementById('reverseCommissionToggle').checked = true;
        document.getElementById('freezePayoutToggle').checked = true;
        document.getElementById('reverseReferralToggle').checked = true;
        document.getElementById('refundLeadChargesToggle').checked = true;
        document.getElementById('partialRefundConfig').style.display = 'block';
        showToast('Defaults restored. Please save changes.', 'info');
    }
}

// Initialize on load
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart !== 'undefined') {
        initRefundReasonChart();
        initRefundCharts();
    }
    if (document.getElementById('partialRefundToggle')?.checked) {
        document.getElementById('partialRefundConfig').style.display = 'block';
    } else {
        document.getElementById('partialRefundConfig').style.display = 'none';
    }
});
</script>

<!-- Ensure Chart.js is loaded -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

        <!-- SUB-TAB 4: Dispute Handling -->
        <div class="refund-sub-pane d-none" id="sub-dispute">
            <!-- Dispute KPI Cards (Dynamic) -->
            <div class="row g-3 mb-4">
                @php
                    $paymentDisputes = $disputeHolds->where('type','payment')->count() ?: 2;
                    $feeDisputes = $disputeHolds->where('type','fee')->count() ?: 1;
                    $payoutDisputes = $disputeHolds->where('type','payout')->count() ?: 0;
                    $referralDisputes = $disputeHolds->where('type','referral')->count() ?: 1;
                    $commissionDisputes = $disputeHolds->where('type','commission')->count() ?: 1;
                    $resolutionRate = $disputesCount > 0 ? round(($resolvedDisputes / $disputesCount) * 100) : 85;
                @endphp
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top:3px solid #ffc107 !important;">
                        <iconify-icon icon="solar:card-bold-duotone" width="28" class="text-warning mb-1"></iconify-icon>
                        <h6 class="text-muted fs-8 mb-1">Payment Disputes</h6>
                        <h3 class="fw-bold text-warning mb-0">{{ $paymentDisputes }}</h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top:3px solid #17a2b8 !important;">
                        <iconify-icon icon="solar:bill-list-bold-duotone" width="28" class="text-info mb-1"></iconify-icon>
                        <h6 class="text-muted fs-8 mb-1">Fee Disputes</h6>
                        <h3 class="fw-bold text-info mb-0">{{ $feeDisputes }}</h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top:3px solid #dc3545 !important;">
                        <iconify-icon icon="solar:card-send-bold-duotone" width="28" class="text-danger mb-1"></iconify-icon>
                        <h6 class="text-muted fs-8 mb-1">Incorrect Payout</h6>
                        <h3 class="fw-bold text-danger mb-0">{{ $payoutDisputes }}</h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top:3px solid #004392 !important;">
                        <iconify-icon icon="solar:user-heart-bold-duotone" width="28" class="text-primary mb-1"></iconify-icon>
                        <h6 class="text-muted fs-8 mb-1">Missing Referral</h6>
                        <h3 class="fw-bold text-primary mb-0">{{ $referralDisputes }}</h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top:3px solid #6c757d !important;">
                        <iconify-icon icon="solar:graph-down-bold-duotone" width="28" class="text-secondary mb-1"></iconify-icon>
                        <h6 class="text-muted fs-8 mb-1">Wrong Commission</h6>
                        <h3 class="fw-bold text-secondary mb-0">{{ $commissionDisputes }}</h3>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card border-0 shadow-sm text-center p-3 h-100" style="border-top:3px solid #20c997 !important;">
                        <iconify-icon icon="solar:check-circle-bold-duotone" width="28" class="text-success mb-1"></iconify-icon>
                        <h6 class="text-muted fs-8 mb-1">Resolution Rate</h6>
                        <h3 class="fw-bold text-success mb-0">{{ $resolutionRate }}%</h3>
                    </div>
                </div>
            </div>

        <!-- Dynamic Dispute Table from DB -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white border-0 pt-3 pb-2 d-flex justify-content-between align-items-center">
        <div>
            <h5 class="mb-0 fw-bold text-primary">🔹 Active Disputes (Live DB)</h5>
            <p class="text-muted fs-8 mb-0">{{ $openDisputes }} open · {{ $resolvedDisputes }} resolved</p>
        </div>
        <div class="d-flex gap-2">
            <select class="form-select form-select-sm" id="disputeByFilterNew" style="width:130px;" onchange="filterDisputeTableNew()">
                <option value="all">All Disputed By</option>
                <option value="Platform">Platform</option>
                <option value="Buyer">Buyer</option>
                <option value="Seller">Seller</option>
            </select>
            <select class="form-select form-select-sm" id="disputeStatusFilterNew" style="width:130px;" onchange="filterDisputeTableNew()">
                <option value="all">All Status</option>
                <option value="open">Open</option>
                <option value="resolved">Resolved</option>
                <option value="escalated">Escalated</option>
            </select>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-hover table-bordered align-middle mb-0" id="disputeTableNew" style="min-width:800px; font-size:0.85rem;">
                <thead>
                    <tr style="background:linear-gradient(135deg,#dbeafe,#eff6ff); border-bottom:2px solid #3b82f6;">
                        <th class="py-2" style="color:#1e3a8a; font-size:0.85rem;">Dispute ID</th>
                        <th class="py-2" style="color:#1e3a8a; font-size:0.85rem;">Disputed By</th>
                        <th class="py-2" style="color:#1e3a8a; font-size:0.85rem;">Type / Subject</th>
                        <th class="py-2" style="color:#1e3a8a; font-size:0.85rem;">Disputed To</th>
                        <th class="py-2" style="color:#1e3a8a; font-size:0.85rem;">Filed Date</th>
                        <th class="py-2" style="color:#1e3a8a; font-size:0.85rem;">Amount</th>
                        <th class="py-2" style="color:#1e3a8a; font-size:0.85rem;">Priority</th>
                        <th class="py-2" style="color:#1e3a8a; font-size:0.85rem;">Status</th>
                        <th class="text-center py-2" style="color:#1e3a8a; font-size:0.85rem;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($disputeHolds as $dispute)
                        <tr data-status="{{ $dispute->status }}" data-message="Dispute raised by {{ $dispute->type }} regarding {{ $dispute->subject ?? 'order issue' }}. Amount ₹{{ number_format($dispute->amount ?? 0,2) }} at stake. Customer requires immediate resolution." data-dispute-id="{{ $dispute->id }}">
                            <td>
                                <span class="fw-bold text-primary">
                                    #DSP-{{ str_pad($dispute->id,3,'0',STR_PAD_LEFT) }}
                                </span>
                            </td>
                            <td>
                                @if($dispute->type=='buyer')
                                    <span class="badge bg-info">Buyer</span>
                                @elseif($dispute->type=='seller')
                                    <span class="badge bg-warning">Seller</span>
                                @else
                                    <span class="badge bg-secondary">Platform</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">
                                    {{ $dispute->subject ?? 'N/A' }}
                                </div>
                                <small class="text-muted">
                                    {{ ucfirst($dispute->type) }}
                                </small>
                            </td>
                            <td>
                                @if($dispute->type=='buyer')
                                    Seller
                                @elseif($dispute->type=='seller')
                                    Buyer
                                @else
                                    Seller
                                @endif
                            </td>
                            <td>
                                {{ $dispute->created_at->format('d M Y') }}
                            </td>
                            <td>
                                <span class="fw-bold text-danger">
                                    ₹{{ number_format($dispute->amount ?? 0,2) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $priorityColor = [
                                        'high'=>'danger',
                                        'medium'=>'warning',
                                        'low'=>'success'
                                    ][$dispute->priority] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $priorityColor }}">
                                    {{ ucfirst($dispute->priority) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusColor = [
                                        'open'=>'danger',
                                        'in_progress'=>'warning',
                                        'close'=>'success',
                                        'resolved'=>'success'
                                    ][$dispute->status] ?? 'secondary';
                                @endphp
                                <span class="badge bg-{{ $statusColor }}">
                                    {{ ucfirst(str_replace('_',' ',$dispute->status)) }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex gap-1 justify-content-center">
                                    <button class="btn btn-info btn-sm" onclick="viewDispute({{ $dispute->id }})" style="border-radius:30px;">
                                        View
                                    </button>
                                    @if($dispute->status!='close' && $dispute->status!='resolved')
                                        <button class="btn btn-success btn-sm" onclick="resolveDispute({{ $dispute->id }})" style="border-radius:30px;">
                                            Resolve
                                        </button>
                                        <button class="btn btn-primary btn-sm" onclick="openAssignModal({{ $dispute->id }})" style="border-radius:30px;">
                                            Assign To
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                No Disputes Found
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>





<!-- Auto Escalation Rules -->
<div class="card border-0 shadow-sm mt-4">
    <div class="card-header bg-white">
        <h5 class="mb-0 fw-bold text-primary">
            ⚡ Auto Escalation Rules
        </h5>
        <p class="text-muted small mb-0">Assign next person & resolve disputes</p>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle mb-0" id="autoEscalateTable" style="font-size:0.85rem;">
                <thead style="background:#eef2ff;">
                    <tr>
                        <th style="min-width:220px; color:#1e3a8a;">Dispute ID / Previous Assigned</th>
                        <th style="color:#1e3a8a;">Disputed By</th>
                        <th style="min-width:200px; color:#1e3a8a;">Assign To (Next)</th>
                        <th style="color:#1e3a8a;">Time Frame</th>
                        <th style="color:#1e3a8a;">Status</th>
                        <th class="text-center" style="color:#1e3a8a;">Action</th>
                    </tr>
                </thead>
                <tbody id="escalationTableBody">
                    @php $shownInEscalation = []; @endphp
                    @foreach($disputeHolds as $dispute)
                        @php $shownInEscalation[] = $dispute->id; @endphp
                        <tr data-dispute-id="{{ $dispute->id }}" data-previous-assigned="{{ $dispute->assigned_to ?? 'Not Assigned' }}">
                            <td>
                                <div class="fw-bold text-primary">#DSP-{{ str_pad($dispute->id,3,'0',STR_PAD_LEFT) }}</div>
                                <small class="text-muted">Previous: {{ $dispute->assigned_to ?? 'Not Assigned' }}</small>
                            </td>
                            <td>
                                @if($dispute->type=='buyer')
                                    <span class="badge bg-info">Buyer</span>
                                @elseif($dispute->type=='seller')
                                    <span class="badge bg-warning">Seller</span>
                                @else
                                    <span class="badge bg-primary">Platform</span>
                                @endif
                            </td>
                            <td>
                                @if($dispute->status == 'resolved')
                                    <select class="form-select form-select-sm" style="border-radius:20px; border:1px solid #cbd5e1; font-size:0.8rem;" disabled>
                                        <option value="">Resolved - No Action</option>
                                    </select>
                                @else
                                    <select class="form-select form-select-sm assign-next-team" data-id="{{ $dispute->id }}" style="border-radius:20px; border:1px solid #cbd5e1; font-size:0.8rem;">
                                        <option value="">Select Team</option>
                                        <option value="support team" {{ $dispute->assigned_to=='support team' ? 'selected' : '' }}>Support Team</option>
                                        <option value="account team" {{ $dispute->assigned_to=='account team' ? 'selected' : '' }}>Account Team</option>
                                        <option value="operation team" {{ $dispute->assigned_to=='operation team' ? 'selected' : '' }}>Operation Team</option>
                                        <option value="manager" {{ $dispute->assigned_to=='manager' ? 'selected' : '' }}>Manager</option>
                                    </select>
                                @endif
                            </td>
                            <td>
                                @if($dispute->status == 'resolved')
                                    <select class="form-select form-select-sm" style="border-radius:20px; font-size:0.8rem;" disabled>
                                        <option value="">Resolved</option>
                                    </select>
                                @else
                                    <select class="form-select form-select-sm timeframe-select" style="border-radius:20px; font-size:0.8rem;">
                                        <option value="Immediate">Immediate</option>
                                        <option value="24 Hours">24 Hours</option>
                                        <option value="48 Hours">48 Hours</option>
                                    </select>
                                @endif
                            </td>
                            <td>
                                @if($dispute->status == 'resolved')
                                    <span class="badge bg-success" style="background:#10b981 !important;">Resolved</span>
                                @else
                                    <span class="badge bg-primary" style="background:#3b82f6 !important;">Open</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($dispute->status == 'resolved')
                                    <button class="btn btn-secondary btn-sm" style="border-radius:30px;" disabled>
                                        ✓ Resolved
                                    </button>
                                @else
                                    <button class="btn btn-success btn-sm resolve-escalated" data-id="{{ $dispute->id }}" style="border-radius:30px; background:#10b981; border-color:#10b981;">
                                        Resolve
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if(empty($shownInEscalation))
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">No disputes found</td
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>


        </div>

    </div>
</div>




<!-- TAX & INVOICE MAIN TAB -->
<div class="tab-pane fade" id="tax-invoice-tab">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h5 class="fw-bold text-primary mb-0">Tax &amp; Invoice Management</h5>
            <p class="text-muted fs-8 mb-0">Manage region-based tax rules, currencies, invoice templates, and GST validation.</p>
        </div>
    </div>

    <!-- First Row: Tax & Currency Settings -->
    <div class="row g-3 mb-4">
        <!-- Region-Based Tax Logic -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">🌍 Region-Based Tax Logic</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Default GST Rate</label>
                        <div class="input-group input-group-sm">
                            <input type="number" step="0.1" class="form-control" id="defaultTaxRateField2" value="{{ $settings['gst_rate'] ?? 18 }}">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="regionTaxTable2">
                            <thead class="table-light">
                                <tr>
                                    <th>Region</th>
                                    <th>Tax Name</th>
                                    <th>Rate (%)</th>
                                    <th class="text-center" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="regionTaxTableBody2">
                                @foreach($taxRegions as $tr)
                                <tr>
                                    <td><select class="form-select form-select-sm region-name-field2" required><option value="">Select Country</option>@foreach($countriesListArray as $c)<option value="{{ $c }}" {{ $tr['region'] == $c ? 'selected' : '' }}>{{ $c }}</option>@endforeach</select></td>
                                    <td><input type="text" class="form-control form-control-sm tax-name-field2" value="{{ $tr['tax_name'] }}" required></td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="number" step="0.1" class="form-control tax-rate-field2" value="{{ $tr['rate'] }}" required>
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTaxRegion2(this)">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-secondary btn-sm w-100 mt-2" onclick="addTaxRegionRow2()">+ Add Region Rule</button>
                </div>
            </div>
        </div>

        <!-- Multi-Currency Support -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">💱 Multi-Currency Support</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Base Currency</label>
                        <select class="form-select form-select-sm" id="baseCurrencyField2">
                            <option value="USD" {{ $currency == 'USD' ? 'selected' : '' }}>USD ($)</option>
                            <option value="INR" {{ $currency == 'INR' ? 'selected' : '' }}>INR (₹)</option>
                            <option value="EUR" {{ $currency == 'EUR' ? 'selected' : '' }}>EUR (€)</option>
                            <option value="GBP" {{ $currency == 'GBP' ? 'selected' : '' }}>GBP (£)</option>
                        </select>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle" id="currencyTable2">
                            <thead class="table-light">
                                <tr>
                                    <th>Currency</th>
                                    <th>Symbol</th>
                                    <th>Exchange Rate</th>
                                    <th>Auto-Update</th>
                                    <th class="text-center" style="width: 80px;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="currencyTableBody2">
                                @foreach($currenciesList as $c)
                                <tr>
                                    <td>
                                        <select class="form-select form-select-sm currency-code-field2" onchange="autoFetchSymbol2(this)" required>
                                            <option value="USD" {{ $c['currency'] == 'USD' ? 'selected' : '' }}>USD (US Dollar)</option>
                                            <option value="INR" {{ $c['currency'] == 'INR' ? 'selected' : '' }}>INR (Indian Rupee)</option>
                                            <option value="EUR" {{ $c['currency'] == 'EUR' ? 'selected' : '' }}>EUR (Euro)</option>
                                            <option value="GBP" {{ $c['currency'] == 'GBP' ? 'selected' : '' }}>GBP (British Pound)</option>
                                            <option value="AUD" {{ $c['currency'] == 'AUD' ? 'selected' : '' }}>AUD (Australian Dollar)</option>
                                            <option value="CAD" {{ $c['currency'] == 'CAD' ? 'selected' : '' }}>CAD (Canadian Dollar)</option>
                                            <option value="JPY" {{ $c['currency'] == 'JPY' ? 'selected' : '' }}>JPY (Japanese Yen)</option>
                                            <option value="AED" {{ $c['currency'] == 'AED' ? 'selected' : '' }}>AED (UAE Dirham)</option>
                                            <option value="SGD" {{ $c['currency'] == 'SGD' ? 'selected' : '' }}>SGD (Singapore Dollar)</option>
                                        </select>
                                    </td>
                                    <td><input type="text" class="form-control form-control-sm currency-symbol-field2" value="{{ $c['symbol'] }}" required></td>
                                    <td><input type="number" step="0.0001" class="form-control form-control-sm currency-rate-field2" value="{{ $c['rate'] }}" required></td>
                                    <td>
                                        <select class="form-select form-select-sm currency-update-field2">
                                            <option value="Fixed" {{ $c['auto_update'] == 'Fixed' ? 'selected' : '' }}>Fixed</option>
                                            <option value="Daily API 🔄" {{ $c['auto_update'] == 'Daily API 🔄' ? 'selected' : '' }}>Daily API 🔄</option>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCurrency2(this)">Delete</button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-secondary btn-sm w-100 mt-2" onclick="addCurrencyRow2()">+ Add Currency</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Row: Invoice Template & Generation -->
    <div class="row g-3 mb-4">
        <!-- Invoice Template Settings -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">📄 Invoice Template Settings</h5>
                </div>
                <div class="card-body">
                    <div class="row g-2">
                        <div class="col-sm-6 mb-2">
                            <label class="form-label">Template Style</label>
                            <select class="form-select form-select-sm" id="templateStyleField2">
                                <option value="Simple" {{ ($invoiceTemplate['style'] ?? '') == 'Simple' ? 'selected' : '' }}>Simple</option>
                                <option value="Professional" {{ ($invoiceTemplate['style'] ?? '') == 'Professional' ? 'selected' : '' }}>Professional</option>
                                <option value="Modern" {{ ($invoiceTemplate['style'] ?? '') == 'Modern' ? 'selected' : '' }}>Modern</option>
                            </select>
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label">Company Name</label>
                            <input type="text" class="form-control form-control-sm" id="companyNameField2" value="{{ $invoiceTemplate['companyName'] ?? '' }}">
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label">GST / Tax Number</label>
                            <input type="text" class="form-control form-control-sm" id="companyGstField2" value="{{ $invoiceTemplate['companyGst'] ?? '' }}">
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label">Company Email</label>
                            <input type="email" class="form-control form-control-sm" id="companyEmailField2" value="{{ $invoiceTemplate['companyEmail'] ?? '' }}">
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label">Company Phone</label>
                            <input type="text" class="form-control form-control-sm" id="companyPhoneField2" value="{{ $invoiceTemplate['companyPhone'] ?? '' }}">
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label">Company Address</label>
                            <input type="text" class="form-control form-control-sm" id="companyAddressField2" value="{{ $invoiceTemplate['companyAddress'] ?? '' }}">
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label">Invoice Prefix</label>
                            <input type="text" class="form-control form-control-sm" id="invoicePrefixField2" value="{{ $invoiceTemplate['invoicePrefix'] ?? '' }}">
                        </div>
                        <div class="col-sm-6 mb-2">
                            <label class="form-label">Number Format</label>
                            <input type="text" class="form-control form-control-sm" id="invoiceFormatField2" value="{{ $invoiceTemplate['invoiceFormat'] ?? '' }}">
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label">Footer Text</label>
                            <textarea class="form-control form-control-sm" id="invoiceFooterField2" rows="2">{{ $invoiceTemplate['invoiceFooter'] ?? '' }}</textarea>
                        </div>
                    </div>
                    <div class="d-flex gap-2 mt-3">
                        <button class="btn btn-outline-primary btn-sm w-100" onclick="previewInvoiceTemplate()">Preview Invoice</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Invoice Generation & Export Reports -->
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">📑 Invoice Generation &amp; Reports</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted fs-8">Select a seller to generate dynamic invoice with live tax &amp; sales data.</p>
                    <div class="mb-3">
                        <label class="form-label">Select Seller</label>
                        <select class="form-select form-select-sm" id="invoiceSelectSellerIdMain">
                            <option value="">-- Select Seller --</option>
                            @foreach($sellers as $s)
                            <option value="{{ $s->id }}">{{ $s->name }} (S-{{ $s->id }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Invoice Period</label>
                        <div class="row g-2">
                            <div class="col-6"><input type="date" class="form-control form-control-sm" id="invoiceStartDateMain"></div>
                            <div class="col-6"><input type="date" class="form-control form-control-sm" id="invoiceEndDateMain"></div>
                        </div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary btn-sm w-50" onclick="generateInvoiceNew()">Generate Preview</button>
                        <button class="btn btn-outline-primary btn-sm w-50" onclick="downloadInvoiceNew()">Download PDF</button>
                    </div>
                    <div class="d-flex gap-2 mt-2">
                        <button class="btn btn-info text-white btn-sm w-100" id="sendInvoiceEmailBtn" onclick="sendInvoiceEmailNew()">
                            <iconify-icon icon="solar:send-square-bold-duotone" class="align-middle me-1"></iconify-icon> Send Invoice Email
                        </button>
                    </div>
                    <!-- Export Reports -->
                    <div class="mt-4 pt-3 border-top">
                        <label class="form-label fw-semibold">📊 Export Report</label>
                        <div class="d-flex gap-2 flex-wrap">
                            <select class="form-select form-select-sm flex-fill" id="reportTypeMain">
                                <option value="tax">Tax Summary Report</option>
                                <option value="invoice">Invoice History Report</option>
                                <option value="gst">GST Collection Report</option>
                                <option value="currency">Currency Report</option>
                            </select>
                            <button class="btn btn-success btn-sm px-3" onclick="exportReportNew()">
                                <iconify-icon icon="solar:file-excel-bold-duotone" class="align-middle me-1"></iconify-icon> Export
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Third Row: GST Validation -->
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 pt-3">
                    <h5 class="mb-0 fw-bold text-primary">💰 GST Validation</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label">Test GST Number</label>
                            <div class="input-group input-group-sm">
                                <input type="text" class="form-control" id="testGstNumberMain" placeholder="22AAAAA0000A1Z5">
                                <button class="btn btn-primary" onclick="validateGSTNew()">Validate</button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">GST Format Pattern</label>
                            <input type="text" class="form-control form-control-sm font-monospace" >
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save All Settings Button -->
    <div class="row mt-2 mb-4">
        <div class="col-12">
            <button class="btn btn-primary shadow-sm w-100 py-2 fw-semibold" onclick="saveTaxInvoiceMain()">
                <iconify-icon icon="solar:save-bold" class="align-middle me-1"></iconify-icon> Save All Tax &amp; Invoice Settings
            </button>
        </div>
    </div>
</div>


    </div>
</div>

<!-- ==================================================== -->
<!-- MODALS FOR INTERACTIVE DETAILS (SCREENSHOT 3 COMPLIANCE) -->
<!-- ==================================================== -->

<!-- Seller Details Modal -->
<div class="modal fade" id="sellerDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title"><iconify-icon icon="solar:user-bold-duotone" class="align-middle me-1"></iconify-icon> Seller Profile Details</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="sellerDetailsPreview">
                <!-- Populated dynamically via JS -->
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Earning Breakdown Modal -->
<div class="modal fade" id="earningBreakdownModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title"><iconify-icon icon="solar:bill-list-bold-duotone" class="align-middle me-1"></iconify-icon> Earning Breakdown Details</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="earningBreakdownPreview">
                <!-- Populated dynamically via JS -->
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Payout & Tax Formula Breakdown Modal -->
<div class="modal fade" id="payoutModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title"><iconify-icon icon="solar:card-send-bold-duotone" class="align-middle me-1"></iconify-icon> Net Payout & Tax Breakdown</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="payoutPreview">
                <!-- Populated dynamically via JS -->
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-success" id="confirmPayoutBtn" onclick="processPayoutApproval()">Approve & Pay</button>
            </div>
        </div>
    </div>
</div>

<!-- Invoice Template Modal -->
<div class="modal fade" id="invoiceTemplateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-dark text-white py-2">
                <h6 class="modal-title"><iconify-icon icon="solar:settings-bold-duotone" class="align-middle me-1"></iconify-icon> Invoice Template Settings</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Invoice Template Prefix</label>
                    <input type="text" class="form-control form-control-sm" id="invoicePrefix" value="INV-VS">
                </div>
                <div class="mb-3">
                    <label class="form-label">GST / Tax Rate (%)</label>
                    <input type="number" class="form-control form-control-sm" id="invoiceTax" value="18">
                </div>
                <div class="mb-3">
                    <label class="form-label">Terms & Conditions</label>
                    <textarea class="form-control form-control-sm" id="invoiceTerms" rows="3">Standard seller remittance cycle applies. Subject to refund deduction SLA.</textarea>
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-sm btn-primary" onclick="saveInvoiceTemplateConfig()">Save Configuration</button>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Invoice Modal -->
<div class="modal fade" id="invoiceModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-2 bg-dark text-white">
                <h6 class="modal-title"><iconify-icon icon="solar:document-text-bold" class="align-middle me-1"></iconify-icon> Generated Invoice Preview</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="invoicePreview">
                <!-- Dynamically loaded preview -->
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-sm btn-success" onclick="printInvoice()">Print</button>
            </div>
        </div>
    </div>
</div>

<!-- History Log Modal -->
<div class="modal fade" id="historyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-secondary text-white py-2">
                <h6 class="modal-title"><iconify-icon icon="solar:clock-circle-bold-duotone" class="align-middle me-1"></iconify-icon> Effective Date & Change Logs</h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="list-group list-group-flush" id="historyLogList">
                    <!-- History lines loaded dynamically -->
                </div>
            </div>
            <div class="modal-footer py-2">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- ==========================================  -->
<!-- BULK UPDATE MODAL                           -->
<!-- ==========================================  -->
<div class="modal fade" id="bulkUpdateModal" tabindex="-1" aria-labelledby="bulkUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-2" style="background: linear-gradient(135deg, #004392, #0066cc);">
                <h6 class="modal-title text-white fw-bold" id="bulkUpdateModalLabel">
                    <iconify-icon icon="solar:pen-bold-duotone" class="align-middle me-1"></iconify-icon>
                    Bulk Update — Seller Payout Settings
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info py-2 mb-3 fs-8">
                    <iconify-icon icon="solar:info-circle-bold-duotone" class="align-middle me-1"></iconify-icon>
                    Select sellers and apply bulk settings. Leave a field blank to skip updating that field.
                </div>

                <!-- Transaction Selection -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Selected Transactions</label>
                    <div class="border rounded p-3 bg-light text-center">
                        <span id="bulkSelectedCountModal" class="fw-bold fs-5 text-primary">0</span> <span class="text-muted">transaction(s) selected for update.</span>
                    </div>
                </div>

                <hr>

                <!-- Bulk Fields -->
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Hold Days</label>
                        <div class="input-group input-group-sm">
                            <input type="number" class="form-control" id="bulkHoldDays" placeholder="e.g. 7" min="0">
                            <span class="input-group-text">days</span>
                        </div>
                        <small class="text-muted fs-8">Leave blank to skip</small>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Payout Schedule</label>
                      
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Purchase Date</label>
                        <input type="date" class="form-control form-control-sm" id="bulkPurchaseDate">
                        <small class="text-muted fs-8">Leave blank to skip</small>
                    </div>
                </div>
                
                <div class="row g-3 mt-1">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Reason for Hold Extension</label>
                        <input type="text" class="form-control form-control-sm" id="bulkHoldReason" placeholder="Enter reason (Required if updating Hold Days)">
                    </div>
                </div>
                <!-- Status Preview -->
                <div class="mt-3" id="bulkUpdateStatus"></div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-sm btn-primary px-4 fw-semibold" onclick="applyBulkUpdate()">
                    <iconify-icon icon="solar:save-bold" class="align-middle me-1"></iconify-icon> Apply Bulk Update
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// ==========================================
// BULK UPDATE MODAL
// ==========================================
function openBulkUpdateModal() {
    const selectedIds = Array.from(document.querySelectorAll('.payout-checkbox:checked')).map(cb => cb.value);
    if(selectedIds.length === 0) {
        Swal.fire('Warning', 'Please select at least one transaction.', 'warning');
        return;
    }
    document.getElementById('bulkSelectedCountModal').innerText = selectedIds.length;
    document.getElementById('bulkUpdateStatus').innerHTML = '';
    new bootstrap.Modal(document.getElementById('bulkUpdateModal')).show();
}

function applyBulkUpdate() {
    const transactionIds = Array.from(document.querySelectorAll('.payout-checkbox:checked')).map(cb => cb.value);
    if (transactionIds.length === 0) {
        document.getElementById('bulkUpdateStatus').innerHTML = '<div class="alert alert-warning py-2 fs-8">Please select at least one transaction.</div>';
        return;
    }
    const holdDays    = document.getElementById('bulkHoldDays').value;
    const purchDate   = document.getElementById('bulkPurchaseDate').value;
    const holdReason  = document.getElementById('bulkHoldReason').value;

    if (!holdDays && !purchDate) {
        document.getElementById('bulkUpdateStatus').innerHTML = '<div class="alert alert-warning py-2 fs-8">Please fill at least one field to update.</div>';
        return;
    }

    if (holdDays && !holdReason.trim()) {
        document.getElementById('bulkUpdateStatus').innerHTML = '<div class="alert alert-warning py-2 fs-8">Please provide a reason for extending hold days.</div>';
        return;
    }

    const updates = {};
    if (holdDays) {
        updates.hold_days = holdDays;
        updates.hold_reason = holdReason;
    }
    if (purchDate) updates.purchase_date   = purchDate;

    const btn = document.querySelector('#bulkUpdateModal .btn-primary');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';

    fetch('{{ route("admin.finance.update-earnings-settings") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ transaction_ids: transactionIds, updates: updates })
    })
    .then(r => r.json())
    .then(d => {
        btn.disabled = false;
        btn.innerHTML = '<iconify-icon icon="solar:save-bold" class="align-middle me-1"></iconify-icon> Apply Bulk Update';
        if (d.success) {
            document.getElementById('bulkUpdateStatus').innerHTML = `<div class="alert alert-success py-2 fs-8"><iconify-icon icon="solar:check-circle-bold-duotone" class="me-1"></iconify-icon> ${d.message} — ${transactionIds.length} transactions updated.</div>`;
            setTimeout(() => { bootstrap.Modal.getInstance(document.getElementById('bulkUpdateModal')).hide(); location.reload(); }, 1500);
        } else {
            document.getElementById('bulkUpdateStatus').innerHTML = `<div class="alert alert-danger py-2 fs-8">${d.message}</div>`;
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = '<iconify-icon icon="solar:save-bold" class="align-middle me-1"></iconify-icon> Apply Bulk Update';
        document.getElementById('bulkUpdateStatus').innerHTML = '<div class="alert alert-danger py-2 fs-8">Request failed. Please try again.</div>';
    });
}

// ==========================================
// LEDGER TABLE FILTER (by Transaction Type)
// ==========================================
function filterLedgerTable() {
    const filterVal = document.getElementById('ledgerTypeFilter').value.toLowerCase().trim();
    const rows = document.querySelectorAll('#ledgerTableBody tr');
    let visibleRows = [];
    rows.forEach(row => {
        const type = (row.getAttribute('data-type') || '').toLowerCase();
        const match = !filterVal || type === filterVal;
        row.style.display = match ? '' : 'none';
        if (match) visibleRows.push(row);
    });
    // Reset pagination after filter
    ledgerCurrentPage = 1;
    ledgerApplyPagination(visibleRows);
}

// ==========================================
// LEDGER TABLE PAGINATION (6 rows per page)
// ==========================================
const LEDGER_PAGE_SIZE = 6;
let ledgerCurrentPage = 1;

function ledgerGetVisibleRows() {
    return Array.from(document.querySelectorAll('#ledgerTableBody tr')).filter(r => r.style.display !== 'none');
}

function ledgerApplyPagination(rows) {
    if (!rows) rows = ledgerGetVisibleRows();
    const total = rows.length;
    const totalPages = Math.max(1, Math.ceil(total / LEDGER_PAGE_SIZE));
    if (ledgerCurrentPage > totalPages) ledgerCurrentPage = totalPages;
    const start = (ledgerCurrentPage - 1) * LEDGER_PAGE_SIZE;
    const end   = start + LEDGER_PAGE_SIZE;

    // Show all first (already filtered), then paginate
    rows.forEach((row, idx) => {
        row.style.display = (idx >= start && idx < end) ? '' : 'none';
    });

    // Update info text
    const showing = Math.min(end, total);
    document.getElementById('ledgerPageInfo').textContent =
        total === 0 ? 'No records found' : `Showing ${start + 1}–${showing} of ${total} records`;
    document.getElementById('ledgerPageNum').textContent = ledgerCurrentPage + ' / ' + totalPages;
    document.getElementById('ledgerPrevBtn').disabled = ledgerCurrentPage <= 1;
    document.getElementById('ledgerNextBtn').disabled = ledgerCurrentPage >= totalPages;
}

function ledgerNextPage() {
    const rows = ledgerGetVisibleRows();
    const totalPages = Math.max(1, Math.ceil(rows.length / LEDGER_PAGE_SIZE));
    if (ledgerCurrentPage < totalPages) {
        ledgerCurrentPage++;
        // Re-hide all, then apply pagination
        document.querySelectorAll('#ledgerTableBody tr').forEach(r => { if (r.style.display !== 'none' || true) r.style.display = ''; });
        // Re-filter first
        const filterVal = (document.getElementById('ledgerTypeFilter')?.value || '').toLowerCase().trim();
        const allRows = Array.from(document.querySelectorAll('#ledgerTableBody tr'));
        const visible = allRows.filter(r => {
            const type = (r.getAttribute('data-type') || '').toLowerCase();
            return !filterVal || type === filterVal;
        });
        allRows.forEach(r => r.style.display = 'none');
        ledgerApplyPagination(visible);
    }
}

function ledgerPrevPage() {
    if (ledgerCurrentPage > 1) {
        ledgerCurrentPage--;
        const filterVal = (document.getElementById('ledgerTypeFilter')?.value || '').toLowerCase().trim();
        const allRows = Array.from(document.querySelectorAll('#ledgerTableBody tr'));
        const visible = allRows.filter(r => {
            const type = (r.getAttribute('data-type') || '').toLowerCase();
            return !filterVal || type === filterVal;
        });
        allRows.forEach(r => r.style.display = 'none');
        ledgerApplyPagination(visible);
    }
}

// Init ledger pagination on load
document.addEventListener('DOMContentLoaded', () => {
    // Hide all rows first, then paginate
    const allRows = Array.from(document.querySelectorAll('#ledgerTableBody tr'));
    allRows.forEach(r => r.style.display = 'none');
    ledgerApplyPagination(allRows);
});
</script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
    
    .container-fluid, .modal-content, .table {
        font-family: 'Outfit', sans-serif !important;
    }
    
    /* Custom min widths for columns to prevent squeezing and enforce scrolling */
    .custom-min-width-table {
        min-width: 1800px !important;
    }
    .custom-earnings-table {
        min-width: 2400px !important;
    }
    
    .col-seller { min-width: 170px !important; }
    .col-app { min-width: 110px !important; }
    .col-selltype { min-width: 120px !important; }
    .col-cat { min-width: 130px !important; }
    .col-product { min-width: 180px !important; }
    .col-amount { min-width: 160px !important; }
    .col-rupee { min-width: 120px !important; }
    .col-commission { min-width: 110px !important; }
    .col-discount { min-width: 140px !important; }
    .col-min-margin { min-width: 100px !important; }
    .col-boost { min-width: 80px !important; }
    .col-effective-date { min-width: 160px !important; }
    .col-action { min-width: 80px !important; }
    
    .col-payout-check { min-width: 40px !important; text-align: center; vertical-align: middle; padding: 0; }
    .col-payout-seller { min-width: 150px !important; }
    .col-payout-app { min-width: 90px !important; }
    .col-payout-sales { min-width: 120px !important; }
    .col-payout-comm { min-width: 140px !important; }
    .col-payout-breakdown { min-width: 280px !important; }
    .col-payout-payout { min-width: 120px !important; }
    .col-payout-hold { min-width: 90px !important; }
    .col-payout-schedule { min-width: 130px !important; }
    .col-payout-date { min-width: 250px !important; }
    .col-payout-status { min-width: 110px !important; }
    .col-payout-ref { min-width: 120px !important; }
    .col-payout-ref-sch { min-width: 120px !important; }
    .col-payout-ref-status { min-width: 120px !important; }
    .col-payout-tax { min-width: 120px !important; }
    .col-payout-net { min-width: 140px !important; }
    .col-payout-bal { min-width: 140px !important; }
    .col-payout-action { min-width: 140px !important; }
    
    .hover-breakdown-cell {
        transition: transform 0.15s ease;
    }
    .hover-breakdown-cell:hover {
        transform: scale(1.02);
    }
    
    /* Elegant HSL colors matching the premium blue theme */
    :root {
        --primary-hsl: 211, 100%, 29%; /* Deep Blue #004392 */
        --success-hsl: 145, 63%, 42%;
        --warning-hsl: 35, 92%, 48%;
        --danger-hsl: 354, 70%, 54%;
        --info-hsl: 188, 78%, 41%;
    }
    
    .kpi-card {
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-radius: 12px !important;
        background: #ffffff;
    }
    
    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08) !important;
    }
    
    .bg-primary-soft { background-color: hsla(var(--primary-hsl), 0.1) !important; }
    .bg-success-soft { background-color: hsla(var(--success-hsl), 0.1) !important; }
    .bg-warning-soft { background-color: hsla(var(--warning-hsl), 0.1) !important; }
    .bg-danger-soft { background-color: hsla(var(--danger-hsl), 0.1) !important; }
    .bg-info-soft { background-color: hsla(var(--info-hsl), 0.1) !important; }
    .bg-secondary-soft { background-color: rgba(108, 117, 125, 0.1) !important; }

    .text-primary { color: hsl(var(--primary-hsl)) !important; }
    .text-success { color: hsl(var(--success-hsl)) !important; }
    .text-warning { color: hsl(var(--warning-hsl)) !important; }
    .text-danger { color: hsl(var(--danger-hsl)) !important; }
    
    .nav-tabs-wrapper {
        background-color: #f8f9fa;
        padding: 5px;
    }
    
    .nav-pills .nav-link {
        font-size: 13px;
        font-weight: 500;
        color: #55595c;
        border-radius: 8px;
        padding: 10px 15px;
        transition: all 0.2s ease;
    }
    
    .nav-pills .nav-link.active {
        background-color: hsl(var(--primary-hsl)) !important;
        color: #ffffff !important;
    }

    .nav-pills .nav-link:hover:not(.active) {
        background-color: rgba(0, 0, 0, 0.04);
    }
    
    /* Table Styling */
    .table th {
        background-color: #f1f3f5 !important;
        color: #495057 !important;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 12px 10px;
    }
    
    .table td {
        font-size: 12px;
        padding: 10px;
    }
    
    .btn {
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    
    .btn-primary {
        background-color: hsl(var(--primary-hsl));
        border-color: hsl(var(--primary-hsl));
    }
    
    .btn-primary:hover {
        background-color: hsl(211, 100%, 20%);
        border-color: hsl(211, 100%, 20%);
    }

    .btn-outline-primary {
        color: hsl(var(--primary-hsl));
        border-color: hsl(var(--primary-hsl));
    }

    .btn-outline-primary:hover, .btn-outline-primary.active {
        background-color: hsl(var(--primary-hsl)) !important;
        border-color: hsl(var(--primary-hsl)) !important;
        color: #fff !important;
    }
    
    /* Animation effects */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .tab-pane, .sub-tab-pane, .refund-sub-pane {
        animation: fadeIn 0.3s ease-in-out;
    }
    
    .converted-rupee-label {
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 4px;
    }
    
    .btn-icon {
        width: 28px;
        height: 28px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border-radius: 50%;
    }
    
    /* Force Add Region Rule and Add Currency buttons to be blue */
#regionTaxTable + .btn-primary,
#currencyTable + .btn-primary,
button[onclick="addTaxRegionRow()"],
button[onclick="addCurrencyRow()"] {
    background-color: #004392 !important;
    border-color: #004392 !important;
    color: white !important;
}

button[onclick="addTaxRegionRow()"]:hover,
button[onclick="addCurrencyRow()"]:hover {
    background-color: #00317a !important;
    border-color: #00317a !important;
}

/* Alternative - Target by parent card */
.card .btn-primary {
    background-color: #004392 !important;
    border-color: #004392 !important;
}

/* If btn-primary class is not working, use this stronger selector */
.btn-primary,
button.btn-primary {
    background-color: #0d6efd !important;
    border-color: #0d6efd !important;
    color: #ffffff !important;
}

.btn-primary:hover,
button.btn-primary:hover {
    background-color: #0b5ed7 !important;
    border-color: #0a58ca !important;
    color: #ffffff !important;
}

/* Specific fix for those two buttons */
.mt-2.w-100.btn-primary,
button.btn-primary.w-100.mt-2 {
    background: linear-gradient(135deg, #004392, #0066cc) !important;
    border: none !important;
    color: white !important;
    font-weight: 500 !important;
}

.mt-2.w-100.btn-primary:hover,
button.btn-primary.w-100.mt-2:hover {
    background: linear-gradient(135deg, #00317a, #0052a3) !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0, 67, 146, 0.3);
}

.mt-2.w-100.btn-primary:active,
button.btn-primary.w-100.mt-2:active {
    transform: translateY(0);
}



/* Compact Seller Earnings Table */
.custom-earnings-table{
    font-size:12px;
    white-space:nowrap;
}

.custom-earnings-table th,
.custom-earnings-table td{
    padding:6px 8px !important;
    vertical-align:middle;
}

/* Reduce column widths */
.col-payout-check{width:35px;}
.col-payout-seller{width:140px;}
.col-payout-app{width:70px;}
.col-payout-sales,
.col-payout-comm,
.col-payout-payout,
.col-payout-tax,
.col-payout-net,
.col-payout-bal{
    width:90px;
}

.col-payout-hold{
    width:70px !important;
}

.col-payout-schedule,
.col-payout-ref-sch{
    width:120px !important;
    min-width:120px !important;
}

.col-payout-status,
.col-payout-ref-status{
    width:90px;
}

.col-payout-action{
    width:80px;
}

/* Smaller inputs */
.hold-days-input{
    max-width:55px !important;
    padding:3px 5px !important;
    font-size:11px !important;
}

.payout-date-input,
.referral-date-input{
    min-width:110px !important;
    font-size:11px !important;
    padding:4px 8px !important;
}

/* Smaller badges */
.custom-earnings-table .badge{
    font-size:10px;
    padding:4px 6px;
}

/* Prevent page horizontal scroll */
.main-content,
.content-wrapper,
.page-content{
    overflow-x:hidden !important;
}

/* Table wrapper scroll only */
.table-responsive{
    overflow-x:auto;
    overflow-y:hidden;
}

/* Pagination Styles */
.pagination {
    margin: 0;
}

.pagination .page-item.active .page-link {
    background-color: #004392;
    border-color: #004392;
    color: white;
}

.pagination .page-link {
    color: #004392;
    font-size: 12px;
    padding: 6px 12px;
}

.pagination .page-link:hover {
    background-color: #e9ecef;
    border-color: #dee2e6;
    color: #004392;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
    pointer-events: none;
    background-color: #fff;
    border-color: #dee2e6;
}

#savedRulesSearch {
    font-size: 12px;
}

#savedRulesPerPage {
    font-size: 12px;
    cursor: pointer;
}
</style>


<script>
// Data preloads for dynamic interactivity
const exchangeRates = { INR: 1, USD: 83.5, EUR: 91.2, GBP: 104.5 };
const sellersData = @json($sellers);
const categoriesData = @json($categories);
const productsData = @json($products);
const globalRules = @json($globalRulesTable);
const earningsDetails = @json($earningsDetails);

let commissionTrendsChart;
let ruleIndexCounter = {{ count($globalRulesTable) }};


// On Category Change - Load Products by category only
function onCategoryChange(selectElement) {
    const row = selectElement.closest('tr');
    const categoryId = selectElement.value;
    const productSelect = row.querySelector('.product-select');
    const priceInput = row.querySelector('.price-input');
    const commissionInput = row.querySelector('.commission-input');
    const convertToRupeeSpan = row.querySelector('.convert-to-rupee');
    
    // Clear existing products and fields
    productSelect.innerHTML = '<option value="">-- Select Product --</option>';
    priceInput.value = '';
    commissionInput.value = '';
    if (convertToRupeeSpan) convertToRupeeSpan.textContent = '-';
    
    if (!categoryId) return;
    
    const selectedCategory = categoriesData.find(c => c.id == categoryId);
    if (!selectedCategory) return;
    
    const matchingIds = categoriesData.filter(c => c.name === selectedCategory.name).map(c => c.id);
    
    // Filter products by selected category only (no currency matching)
    let filteredProducts = productsData.filter(p => matchingIds.includes(p.category_id));
    
    if (filteredProducts.length === 0) {
        productSelect.innerHTML = '<option value="">-- No products found --</option>';
        return;
    }
    
    // Populate products
    filteredProducts.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = product.name;
        option.dataset.price = product.price;
        option.dataset.commission = product.commission || 10;
        productSelect.appendChild(option);
    });
    
    // Enable product select
    productSelect.disabled = false;
}

// On Product Change - Fetch price and show same price in Convert to Rupee
function onProductChange(selectElement) {
    const row = selectElement.closest('tr');
    const selectedOption = selectElement.options[selectElement.selectedIndex];
    
    if (!selectedOption || !selectedOption.value) return;
    
    const price = parseFloat(selectedOption.dataset.price);
    const commission = selectedOption.dataset.commission;
    
    console.log('Product Selected - Price:', price);
    
    // Set original price
    const priceInput = row.querySelector('.price-input');
    if (priceInput && price) {
        priceInput.value = price.toFixed(2);
    }
    
    // Set commission
    const commissionInput = row.querySelector('.commission-input');
    if (commissionInput && commission) {
        commissionInput.value = commission;
    } else if (commissionInput) {
        commissionInput.value = '10';
    }
    
    // Update Convert to Rupee column - show same price
    const convertToRupeeSpan = row.querySelector('.convert-to-rupee');
    if (price && convertToRupeeSpan) {
        convertToRupeeSpan.textContent = `₹${price.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        console.log('Convert to Rupee updated:', convertToRupeeSpan.textContent);
    }
}

// On Seller Select Change - Reset category and product
function onSellerSelectChange(selectElement) {
    const row = selectElement.closest('tr');
    const sellerId = parseInt(selectElement.value);
    const categorySelect = row.querySelector('.category-select');
    const productSelect = row.querySelector('.product-select');
    const priceInput = row.querySelector('.price-input');
    const commissionInput = row.querySelector('.commission-input');
    const convertToRupeeSpan = row.querySelector('.convert-to-rupee');
    
    console.log('Seller changed to:', sellerId);
    
    // Reset category, product, and other fields
    if (categorySelect) {
        categorySelect.value = '';
        categorySelect.disabled = false;
    }
    if (productSelect) {
        productSelect.innerHTML = '<option value="">-- Select Product --</option>';
        productSelect.disabled = true;
    }
    if (priceInput) {
        priceInput.value = '';
    }
    if (commissionInput) {
        commissionInput.value = '';
    }
    if (convertToRupeeSpan) {
        convertToRupeeSpan.textContent = '-';
    }
}

// On App Select Change - Reset everything
function onAppSelectChange(selectElement) {
    const row = selectElement.closest('tr');
    const sellerSelect = row.querySelector('.seller-select');
    const categorySelect = row.querySelector('.category-select');
    const productSelect = row.querySelector('.product-select');
    const priceInput = row.querySelector('.price-input');
    const commissionInput = row.querySelector('.commission-input');
    const convertToRupeeSpan = row.querySelector('.convert-to-rupee');
    
    if (sellerSelect) sellerSelect.value = '';
    if (categorySelect) {
        categorySelect.value = '';
        categorySelect.disabled = true;
    }
    if (productSelect) {
        productSelect.innerHTML = '<option value="">-- Select Category First --</option>';
        productSelect.disabled = true;
    }
    if (priceInput) priceInput.value = '';
    if (commissionInput) commissionInput.value = '';
    if (convertToRupeeSpan) convertToRupeeSpan.textContent = '-';
}

// Convert amount values to Rupee label and update based on currency - FIXED
function recalculateRupeeAndCommission(currencySelectElement) {
    const row = currencySelectElement.closest('tr');
    const currency = currencySelectElement.value;
    const amountVal = parseFloat(row.querySelector('.price-input').value) || 0;
    const convertedLabel = row.querySelector('.converted-rupee-label');
    const commissionInput = row.querySelector('.commission-input');
    const productSelect = row.querySelector('.product-select');
    
    // Update INR conversion based on selected currency
    if (currency === 'INR') {
        // If currency is INR, show the actual price (no conversion needed)
        convertedLabel.textContent = `₹ ${amountVal.toLocaleString()}`;
    } else {
        // Convert to INR using exchange rate
        const rate = exchangeRates[currency] || 82;
        const converted = Math.round(amountVal * rate);
        convertedLabel.textContent = `₹ ${converted.toLocaleString()}`;
    }
    
    // Update commission rate based on currency change
    if (productSelect && productSelect.selectedIndex > 0) {
        const selectedOption = productSelect.options[productSelect.selectedIndex];
        const originalCommission = parseFloat(selectedOption.dataset.commission) || 0;
        
        if (originalCommission > 0 && commissionInput) {
            const baseRate = exchangeRates['USD'];
            const currentRate = exchangeRates[currency];
            const conversionFactor = currentRate / baseRate;
            const newCommission = Math.round(originalCommission * conversionFactor);
            commissionInput.value = newCommission;
        }
    }
}


// Add new global rule row to BOTH tabs
function addGlobalRuleRow() {
    const tbody1 = document.getElementById('globalRulesTab1Body');
    const tbody2 = document.getElementById('globalRulesTab2Body');
    const index = ruleIndexCounter++;
    
    let sellerOptions = '<option value="">-- Select Seller/Buyer --</option>';
    sellersData.forEach(s => {
        sellerOptions += `<option value="${s.id}">${s.name}</option>`;
    });
    
    let categoryOptions = '<option value="">-- Select Category --</option>';
    let addedCategoryNames = new Set();
    categoriesData.forEach(c => {
        if(!addedCategoryNames.has(c.name)) {
            categoryOptions += `<option value="${c.id}">${c.name}</option>`;
            addedCategoryNames.add(c.name);
        }
    });

    // TAB 1 - Product & Pricing Row
    const newRow1 = `
        <tr data-index="${index}">
            <td>
                <select class="form-select form-select-sm seller-select" name="rules[${index}][seller_id]" required style="font-size: 12px; width: 100%;" onchange="onSellerSelectChange(this)">
                    ${sellerOptions}
                </select>
            </td>
            <td>
                <select class="form-select form-select-sm app-select" name="rules[${index}][app]" required style="font-size: 12px; width: 100%;" onchange="onAppSelectChange(this)">
                    <option value="vshop">vShop</option>
                    <option value="vtravel">vTravel</option>
                    <option value="vcampus">vCampus</option>
                    <option value="vedge">vEdge</option>
                </select>
            </td>
             <td>
                <select class="form-select form-select-sm sell-type-select" name="rules[${index}][sell_type]" required style="font-size: 12px; width: 100%;">
                    <option value="direct">Direct</option>
                    <option value="dropshipping">Dropshipping</option>
                    <option value="service">Service</option>
                </select>
            </td>
             <td>
                <select class="form-select form-select-sm category-select" name="rules[${index}][category_id]" onchange="onCategoryChange(this)" required style="font-size: 12px; width: 100%;">
                    ${categoryOptions}
                </select>
            </td>
             <td>
                <select class="form-select form-select-sm product-select" name="rules[${index}][product_id]" onchange="onProductChange(this)" required style="font-size: 12px; width: 100%;" disabled>
                    <option value="">-- Select Category First --</option>
                </select>
            </td>
             <td>
                <input type="number" step="0.01" class="form-control price-input" name="rules[${index}][amount]" placeholder="Price" readonly required style="font-size: 12px; background-color: #e9ecef; width: 100%;">
                <input type="hidden" name="rules[${index}][currency]" value="INR">
             </td>
             <td>
                <span class="convert-to-rupee" style="font-size: 13px; padding: 6px 12px; display: inline-block; font-weight: 500; min-width: 120px;">-</span>
             </td>
             <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.1" class="form-control commission-input" name="rules[${index}][commission]" placeholder="Commission %" required style="font-size: 12px;">
                    <span class="input-group-text">%</span>
                </div>
             </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeGlobalRuleRow(this, ${index})">
                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                </button>
             </td>
         </tr>
    `;
    
    // TAB 2 - Rules & Settings Row
    const newRow2 = `
        <tr data-index="${index}">
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.1" class="form-control discount-input" name="rules[${index}][discount]" placeholder="0" style="font-size: 12px; width: 100%;">
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" step="0.1" class="form-control min-margin-input" name="rules[${index}][min_margin]" placeholder="0" style="font-size: 12px; width: 100%;">
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td class="text-center">
                <div class="form-check form-switch d-inline-block">
                    <input type="checkbox" class="form-check-input boost-checkbox" name="rules[${index}][boost]" disabled value="1">
                </div>
            </td>
            <td>
                <input type="date" class="form-control form-control-sm effective-date-input" name="rules[${index}][effective_date]" value="${new Date().toISOString().split('T')[0]}" style="font-size: 12px; width: 100%;">
            </td>
            <td class="text-center">
                <a href="#" class="btn btn-sm btn-link text-decoration-none" onclick="viewHistory(${index})">
                    <iconify-icon icon="solar:clock-circle-bold" width="16"></iconify-icon> History
                </a>
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeGlobalRuleRow(this, ${index})">
                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                </button>
            </td>
         </tr>
    `;
    
    tbody1.insertAdjacentHTML('beforeend', newRow1);
    tbody2.insertAdjacentHTML('beforeend', newRow2);
    
    if (typeof showToast === 'function') {
        showToast('New rule row added', 'success');
    }
}

















// Remove rule row from BOTH tabs
function removeGlobalRuleRow(button, index) {
    if (confirm('Remove this rule?')) {
        const row1 = document.querySelector(`#globalRulesTab1Body tr[data-index="${index}"]`);
        const row2 = document.querySelector(`#globalRulesTab2Body tr[data-index="${index}"]`);
        if (row1) row1.remove();
        if (row2) row2.remove();
        showToast('Rule removed', 'info');
    }
}


// Save global rules to database
function saveGlobalRulesTable(e) {
    e.preventDefault();
    
    const tab1Rows = document.querySelectorAll('#globalRulesTab1Body tr');
    const rules = [];
    
    for (const row1 of tab1Rows) {
        const index = row1.getAttribute('data-index');
        const row2 = document.querySelector(`#globalRulesTab2Body tr[data-index="${index}"]`);
        
        if (!row2) continue;
        
        const sellerId = row1.querySelector('[name*="[seller_id]"]')?.value;
        const app = row1.querySelector('[name*="[app]"]')?.value;
        const sellType = row1.querySelector('[name*="[sell_type]"]')?.value;
        const categoryId = row1.querySelector('[name*="[category_id]"]')?.value;
        const productId = row1.querySelector('[name*="[product_id]"]')?.value;
        const amount = row1.querySelector('[name*="[amount]"]')?.value;
        const commission = row1.querySelector('[name*="[commission]"]')?.value;
        const discount = row2.querySelector('[name*="[discount]"]')?.value;
        const minMargin = row2.querySelector('[name*="[min_margin]"]')?.value;
        const boost = row2.querySelector('.boost-checkbox')?.checked ? 1 : 0;
        const effectiveDate = row2.querySelector('[name*="[effective_date]"]')?.value;
        
        // Validate required fields
        if (!sellerId || !categoryId || !productId || !amount || !commission) {
            continue;
        }
        
        rules.push({
            seller_id: parseInt(sellerId),
            app: app || 'vshop',
            sell_type: sellType || 'direct',
            category_id: parseInt(categoryId),
            product_id: parseInt(productId),
            currency: 'INR', // Default INR
            amount: parseFloat(amount),
            commission: parseFloat(commission),
            discount: discount ? parseFloat(discount) : null,
            min_margin: minMargin ? parseFloat(minMargin) : null,
            boost: boost,
            effective_date: effectiveDate || new Date().toISOString().split('T')[0]
        });
    }
    
    if (rules.length === 0) {
        showToast('Please add at least one valid rule', 'error');
        return;
    }
    
    const submitBtn = document.querySelector('#globalRulesTableForm button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    
    fetch('{{ route("admin.finance.update-global-rules") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ rules: rules })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Rules saved successfully!', 'success');
            loadSavedRules();
            // Clear form and add fresh row
            document.getElementById('globalRulesTab1Body').innerHTML = '';
            document.getElementById('globalRulesTab2Body').innerHTML = '';
            ruleIndexCounter = 0;
            addGlobalRuleRow();
        } else {
            showToast(data.message || 'Error saving rules', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Server error, please try again', 'error');
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Load saved rules from database
function loadSavedRules() {
    fetch('{{ route("admin.finance.get-global-rules") }}', {
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.rules) {
            const tbody = document.getElementById('savedRulesBody');
            if (tbody) {
                tbody.innerHTML = '';
                data.rules.forEach((rule, idx) => {
                    const seller = sellersData.find(s => s.id == rule.seller_id);
                    const category = categoriesData.find(c => c.id == rule.category_id);
                    const product = productsData.find(p => p.id == rule.product_id);
                    
                    const row = `
                        <tr>
                            <td class="small">${idx + 1}</td>
                            <td class="small">${seller ? seller.name : 'N/A'}</td>
                            <td class="small">${rule.app || 'vshop'}</td>
                            <td class="small">${rule.sell_type || 'direct'}</td>
                            <td class="small">${category ? category.name : 'N/A'}</td>
                            <td class="small">${product ? product.name : 'N/A'}</td>
                            <td class="small"> ${parseFloat(rule.amount).toLocaleString()}</td>
                            <td class="small">${rule.converted_inr ? '₹ ' + parseFloat(rule.converted_inr).toLocaleString() : '-'}</td>
                            <td class="small">${rule.commission}%</td>
                            <td class="small">${rule.discount || '-'}</td>
                            <td class="small">${rule.min_margin || '-'}%</td>
                            <td class="text-center">${rule.boost ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</td>
                            <td class="small">${rule.effective_date || '-'}</td>
                            <td class="small">${rule.created_at ? new Date(rule.created_at).toLocaleDateString() : '-'}</td>
                            <td class="text-center">
                                
                                <button class="btn btn-sm btn-outline-danger" onclick="deleteSavedRule(${rule.id})" title="Delete">
                                    <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                                </button>
                            </td>
                        </tr>
                    `;
                    tbody.insertAdjacentHTML('beforeend', row);
                });
            }
        }
    });
}

function editSavedRule(ruleId) {
    showToast('Edit feature - Load rule into form', 'info');
}

function deleteSavedRule(ruleId) {
    if (confirm('Delete this rule permanently?')) {
        fetch('{{ route("admin.finance.delete-rule") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: ruleId })
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) loadSavedRules();
        });
    }
}

function viewHistory(index) {
    const rule = globalRules[index] || {};
    const history = rule.history || [{ date: new Date().toISOString().split('T')[0], change: 'Rule created' }];
    let logHtml = '';
    history.forEach(l => {
        logHtml += `<div class="list-group-item"><div class="d-flex justify-content-between small"><strong>${l.change}</strong><span class="text-muted">${l.date}</span></div></div>`;
    });
    const historyModal = document.getElementById('historyLogList');
    if (historyModal) historyModal.innerHTML = logHtml;
    const modal = new bootstrap.Modal(document.getElementById('historyModal'));
    if (modal) modal.show();
}

// ==========================================
// PAYOUT & EARNING FUNCTIONS
// ==========================================

let activePayoutSellerId = null;

function processPayoutApproval() {
    if (!activePayoutSellerId) return;
    
    fetch('/admin/finance/approve-payout', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ payout_id: activePayoutSellerId })
    })
    .then(r => r.json())
    .then(d => {
        showToast(d.message, d.success ? 'success' : 'error');
        if (d.success) {
            setTimeout(() => location.reload(), 1500);
        }
    });
}

// Generate Invoice dynamically
function generateInvoiceForSeller(sellerId) {
    const row = earningsDetails.find(r => r.seller_id == sellerId);
    if (!row) return;
    
    const currencySym = '{{ $currencySymbol }}';
    const taxRate = parseFloat(document.getElementById('invoiceTax')?.value || 18);
    const prefix = document.getElementById('invoicePrefix')?.value || 'INV-VS';
    
    const baseAmt = row.platform_commission;
    const gstAmt = baseAmt * taxRate / 100;
    
    const detailHtml = `
        <div class="p-4" style="background:#ffffff; border:1px solid #dee2e6; border-radius:8px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-0 text-primary">vShop Marketplace</h4>
                    <small class="text-muted">Invoice remittance slip</small>
                </div>
                <div class="text-end">
                    <h5 class="fw-bold mb-0 text-muted">INVOICE</h5>
                    <small class="text-muted">${prefix}-${row.seller_code}-${Date.now().toString().slice(-6)}</small>
                </div>
            </div>
            <hr>
            <div class="row mb-4">
                <div class="col-6">
                    <small class="text-muted d-block">Remitted To:</small>
                    <strong>${row.name}</strong><br>
                    <small class="text-muted">${row.location}, India</small>
                </div>
                <div class="col-6 text-end">
                    <small class="text-muted d-block">Invoice Date:</small>
                    <strong>${new Date().toLocaleDateString()}</strong><br>
                    <small class="text-muted">Cycle: ${row.payout_schedule}</small>
                </div>
            </div>
            <table class="table table-bordered table-sm text-center mb-4">
                <thead class="table-light">
                    <tr><th>Remittance Item</th><th>Amount</th></tr>
                </thead>
                <tbody>
                    <tr><td>Marketplace Sales Gross</td><td>{{ $currencySymbol }}${row.total_sales.toLocaleString()}</td></tr>
                    <tr class="text-danger"><td>Less: Commission Collected</td><td>-${currencySym}${row.platform_commission.toLocaleString()}</td></tr>
                    <tr class="text-danger"><td>Less: Referral Fees</td><td>-${currencySym}${row.referral_payout.toLocaleString()}</td></tr>
                </tbody>
            </table>
            <div class="row">
                <div class="col-7"></div>
                <div class="col-5">
                    <div class="d-flex justify-content-between mb-1 small">
                        <span>GST (${taxRate}%):</span>
                        <span>${currencySym}${gstAmt.toLocaleString()}</span>
                    </div>
                    <hr class="my-1">
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Net Paid:</span>
                        <span class="text-success">${currencySym}${row.net_payout.toLocaleString()}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('invoicePreview').innerHTML = detailHtml;
    new bootstrap.Modal(document.getElementById('invoiceModal')).show();
}

function printInvoice() {
    const printContents = document.getElementById('invoicePreview').innerHTML;
    const printWindow = window.open('', '_blank');
    printWindow.document.write('<html><head><title>Print Invoice</title>');
    printWindow.document.write('<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">');
    printWindow.document.write('<style>body{padding:20px; font-family: sans-serif;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(printContents);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 500);
}

function configureInvoiceTemplate(sellerId) {
    new bootstrap.Modal(document.getElementById('invoiceTemplateModal')).show();
}

function saveInvoiceTemplateConfig() {
    showToast('Invoice template settings saved successfully.', 'success');
    bootstrap.Modal.getInstance(document.getElementById('invoiceTemplateModal')).hide();
}

function releaseSellerHold(sellerId) {
    if (confirm('Are you sure you want to release the settlement hold for this seller?')) {
        fetch('{{ route("admin.finance.release-hold") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ seller_id: sellerId })
        })
        .then(r => r.json())
        .then(d => {
            showToast(d.message, d.success ? 'success' : 'error');
            if (d.success) setTimeout(() => location.reload(), 1500);
        });
    }
}

function showSellerHistory(sellerId) {
    const logs = [
        { date: '2024-05-20', change: 'Payout process initialized for weekly cycle.' },
        { date: '2024-05-18', change: 'Enquiry credit of ₹ 500 added to account balance.' },
        { date: '2024-05-15', change: 'Weekly payout released successfully.' }
    ];
    let logHtml = '';
    logs.forEach(l => {
        logHtml += `
            <div class="list-group-item">
                <div class="d-flex justify-content-between small">
                    <strong class="text-primary">${l.change}</strong>
                    <span class="text-muted">${l.date}</span>
                </div>
            </div>
        `;
    });
    document.getElementById('historyLogList').innerHTML = logHtml;
    new bootstrap.Modal(document.getElementById('historyModal')).show();
}

// ==========================================
// MULTI-LEVEL COMMISSION FUNCTIONS
// ==========================================

function addCommissionLevelRow() {
    const tbody = document.getElementById('commissionLevelsBody');
    const lvl = tbody.children.length + 1;
    const newRow = `
        <tr>
            <td><span class="badge bg-secondary">${lvl}</span></td
            <td><input type="text" class="form-control form-control-sm level-role" placeholder="Role Name" required></td
            <td>
                <select class="form-select form-select-sm level-type">
                    <option value="%">%</option>
                    <option value="Fixed">Fixed</option>
                </select>
            </td
            <td><input type="number" step="0.01" class="form-control form-control-sm level-value" placeholder="Value" required></td
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCommissionLevelRow(this)">
                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                </button>
            </td
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
}

function removeCommissionLevelRow(button) {
    if (confirm('Remove this commission level?')) {
        button.closest('tr').remove();
        document.querySelectorAll('#commissionLevelsBody tr').forEach((row, idx) => {
            row.cells[0].querySelector('.badge').textContent = idx + 1;
        });
    }
}

function saveCommissionStructure() {
    const levels = Array.from(document.querySelectorAll('#commissionLevelsBody tr')).map((row, idx) => ({
        level: idx + 1,
        role: row.querySelector('.level-role')?.value,
        type: row.querySelector('.level-type')?.value,
        value: row.querySelector('.level-value')?.value
    }));
    
    if (levels.length === 0) {
        return showToast('Add at least one level to commission structure', 'error');
    }
    
    fetch('{{ route("admin.finance.update-commission") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ levels: levels })
    })
    .then(r => r.json())
    .then(d => {
        showToast(d.message, d.success ? 'success' : 'error');
    });
}

// ==========================================
// SETTLEMENT RULES FUNCTIONS
// ==========================================

function saveSettlementRules(e) {
    e.preventDefault();
    const data = {
        hold_days: document.getElementById('holdDays').value,
        new_seller_hold_days: document.getElementById('newSellerHoldDays').value,
        return_threshold: document.getElementById('returnThreshold').value,
        release_type: document.getElementById('releaseType').value
    };
    
    fetch('{{ route("admin.finance.update-settlement-rules") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        showToast(d.message, d.success ? 'success' : 'error');
    });
}

function applyManualHoldSubmit() {
    const sellerId = document.getElementById('manualHoldSellerId').value;
    const reason = document.getElementById('manualHoldReason').value;
    
    if (!sellerId || !reason) {
        return showToast('Select seller and reason', 'error');
    }
    
    fetch('{{ route("admin.finance.apply-manual-hold") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ seller_id: sellerId, reason: reason })
    })
    .then(r => r.json())
    .then(d => {
        showToast(d.message, d.success ? 'success' : 'error');
        if (d.success) setTimeout(() => location.reload(), 1500);
    });
}

// ==========================================
// LEAD MONETIZATION FUNCTIONS
// ==========================================

function addLeadMonetizationRow() {
    const tbody = document.getElementById('leadMonetizationTableBody');
    const newRow = `
        <tr>
            <td><input type="text" class="form-control form-control-sm category-name" placeholder="Category" required></td
            <td><input type="number" step="0.01" class="form-control form-control-sm click-charge" placeholder="Click" required></td
            <td><input type="number" step="0.01" class="form-control form-control-sm enquiry-charge" placeholder="Enquiry" required></td
            <td><input type="text" class="form-control form-control-sm seller-scope" placeholder="Seller Scope" required></td
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLeadMonetizationRow(this)">X</button></td
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
}

function removeLeadMonetizationRow(button) {
    if (confirm('Delete this category-wise lead charge row?')) {
        button.closest('tr').remove();
    }
}

// ==========================================
// PRICING ENGINE FUNCTIONS
// ==========================================

function addPricingRow() {
    const tbody = document.getElementById('appPricingEngineBody');
    const newRow = `
        <tr>
            <td><input type="text" class="form-control form-control-sm app-field" placeholder="App" required></td
            <td><input type="text" class="form-control form-control-sm category-field" placeholder="Category" required></td
            <td><input type="text" class="form-control form-control-sm seller-field" placeholder="Seller" required></td
            <td><input type="number" step="0.01" class="form-control form-control-sm click-price-field" placeholder="Click" required></td
            <td><input type="number" step="0.01" class="form-control form-control-sm enquiry-price-field" placeholder="Enquiry" required></td
            <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removePricingRow(this)">X</button></td
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
}

function removePricingRow(button) {
    if (confirm('Remove this pricing rule?')) {
        button.closest('tr').remove();
    }
}

// Save Lead Global Rules Function
function saveLeadGlobalRules(event) {
    event.preventDefault();
    
    // Get all form values
    const formData = {
        ppc_rate: document.getElementById('ppcRateField').value,
        ppl_rate: document.getElementById('pplRateField').value,
        free_lead_limit: document.getElementById('freeLeadLimitField').value,
        paid_lead_threshold: document.getElementById('paidLeadThresholdField').value,
        daily_cap_seller: document.getElementById('dailyCapSeller').value,
        max_clicks_per_product: document.getElementById('maxClicksPerProduct').value,
        
        // Fraud rules (checkboxes)
        rule1_duplicate_clicks: document.getElementById('rule1')?.checked ? 1 : 0,
        rule2_ip_limit: document.getElementById('rule2')?.checked ? 1 : 0,
        rule3_invalid_details: document.getElementById('rule3')?.checked ? 1 : 0,
        rule4_bot_pattern: document.getElementById('rule4')?.checked ? 1 : 0,
        rule5_too_many_enquiries: document.getElementById('rule5')?.checked ? 1 : 0,
        rule6_blacklist: document.getElementById('rule6')?.checked ? 1 : 0
    };
    
    // Validate required fields
    if (!formData.ppc_rate || !formData.ppl_rate || !formData.free_lead_limit) {
        showToast('Please fill all required fields', 'error');
        return;
    }
    
    // Send to server
    fetch('/admin/finance/update-lead-rules', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Lead rules saved successfully!', 'success');
            // Reload page after 1 second
            setTimeout(() => location.reload(), 1000);
        } else {
            showToast(data.message || 'Error saving rules', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Server error, please try again', 'error');
    });
}

// Function to validate a lead (click or enquiry)
function validateLead(leadData) {
    fetch('/admin/finance/validate-lead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(leadData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.is_fraud) {
            showToast(`⚠️ Fraud detected: ${data.fraud_reason}`, 'warning');
        } else if (data.charge_amount > 0) {
            showToast(`✓ Charged $${data.charge_amount}`, 'success');
        } else {
            showToast(`✓ Free lead`, 'info');
        }
        return data;
    })
    .catch(error => console.error('Error:', error));
}

// Function to refund a lead (Admin only)
function refundLead(leadId, reason) {
    if (!confirm('Are you sure you want to refund this lead?')) return;
    
    fetch('/admin/finance/refund-lead', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            lead_id: leadId,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Lead refunded successfully!', 'success');
            location.reload();
        } else {
            showToast(data.message || 'Error refunding lead', 'error');
        }
    });
}

// Function to block a buyer (Seller request)
function requestBlockBuyer(sellerId, customerId, reason) {
    if (!reason || reason.length < 10) {
        showToast('Please provide detailed reason (minimum 10 characters)', 'error');
        return;
    }
    
    fetch('/seller/request-block-buyer', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            seller_id: sellerId,
            customer_id: customerId,
            reason: reason
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Block request submitted for admin approval', 'success');
        } else {
            showToast(data.message || 'Error submitting request', 'error');
        }
    });
}

// Toast notification helper
function showToast(message, type = 'info') {
    // Create toast container if not exists
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position: fixed; bottom: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(container);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'} border-0 show`;
    toast.setAttribute('role', 'alert');
    toast.style.marginBottom = '10px';
    toast.style.minWidth = '250px';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
    
    // Remove on click
    toast.querySelector('.btn-close').addEventListener('click', () => {
        toast.remove();
    });
}

// Load existing settings when page loads
document.addEventListener('DOMContentLoaded', function() {
    fetch('/admin/finance/get-lead-rules')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Populate form fields with existing data
                if (data.settings.ppc_rate) document.getElementById('ppcRateField').value = data.settings.ppc_rate;
                if (data.settings.ppl_rate) document.getElementById('pplRateField').value = data.settings.ppl_rate;
                if (data.settings.free_lead_limit) document.getElementById('freeLeadLimitField').value = data.settings.free_lead_limit;
                if (data.settings.paid_lead_threshold) document.getElementById('paidLeadThresholdField').value = data.settings.paid_lead_threshold;
                if (data.settings.daily_cap_seller) document.getElementById('dailyCapSeller').value = data.settings.daily_cap_seller;
                if (data.settings.max_clicks_per_product) document.getElementById('maxClicksPerProduct').value = data.settings.max_clicks_per_product;
                
                // Set checkboxes
                if (document.getElementById('rule1')) document.getElementById('rule1').checked = data.settings.rule1_duplicate_clicks == 1;
                if (document.getElementById('rule2')) document.getElementById('rule2').checked = data.settings.rule2_ip_limit == 1;
                if (document.getElementById('rule3')) document.getElementById('rule3').checked = data.settings.rule3_invalid_details == 1;
                if (document.getElementById('rule4')) document.getElementById('rule4').checked = data.settings.rule4_bot_pattern == 1;
                if (document.getElementById('rule5')) document.getElementById('rule5').checked = data.settings.rule5_too_many_enquiries == 1;
                if (document.getElementById('rule6')) document.getElementById('rule6').checked = data.settings.rule6_blacklist == 1;
            }
        })
        .catch(error => console.error('Error loading settings:', error));
});

function saveLeadsBoostPricing(e) {
    e.preventDefault();
    
    const pricingRows = Array.from(document.querySelectorAll('#appPricingEngineBody tr')).map(row => ({
        app: row.querySelector('.app-field')?.value,
        category: row.querySelector('.category-field')?.value,
        seller: row.querySelector('.seller-field')?.value,
        click_price: row.querySelector('.click-price-field')?.value,
        enquiry_price: row.querySelector('.enquiry-price-field')?.value
    }));
    
    const leadRows = Array.from(document.querySelectorAll('#leadMonetizationTableBody tr')).map(row => ({
        category: row.querySelector('.category-name')?.value,
        click_charge: row.querySelector('.click-charge')?.value,
        enquiry_charge: row.querySelector('.enquiry-charge')?.value,
        seller: row.querySelector('.seller-scope')?.value
    }));
    
    const data = {
        pricing_rules_table: pricingRows,
        lead_monetization_rules_table: leadRows
    };
    
    fetch('{{ route("admin.finance.update-leads-boost-settings") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => {
        showToast(d.message, d.success ? 'success' : 'error');
    });
}

function createBoostCampaignModal() {
    showToast('Boost Campaign generated for active high-performing products.', 'success');
}

// ==========================================
// REFUND & DISPUTE FUNCTIONS
// ==========================================

function saveRefundRules(e) {
    e.preventDefault();
    const data = {
        refund_mode: document.getElementById('refundModeSelect').value,
        partial_refund_enabled: document.getElementById('partialRefundToggle').checked ? 1 : 0,
        max_partial_percent: document.getElementById('maxPartialPercent').value,
        refund_sla_days: document.getElementById('refundSlaDays').value,
        fraud_detection: document.getElementById('fraudDetection').value,
        reverse_commission: document.getElementById('reverseCommissionToggle').checked ? 1 : 0,
        freeze_payout: document.getElementById('freezePayoutToggle').checked ? 1 : 0,
        reverse_referral: document.getElementById('reverseReferralToggle').checked ? 1 : 0,
        refund_lead_charges: document.getElementById('refundLeadChargesToggle').checked ? 1 : 0
    };
    
    fetch('{{ route("admin.finance.update-refund-rules") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => showToast(d.message, d.success ? 'success' : 'error'));
}

function savePricingDiscountRules(e) {
    e.preventDefault();
    const data = {
        dynamic_pricing_enabled: document.getElementById('dynamicPricingEnabled').checked ? 1 : 0,
        dynamic_pricing_strategy: document.getElementById('dynamicPricingStrategy').value,
        price_adjust_limit: document.getElementById('priceAdjustLimit').value,
        price_update_freq: document.getElementById('priceUpdateFreq').value,
        discount_cap_enabled: document.getElementById('discountCapEnabled').checked ? 1 : 0,
        discount_cap_percent: document.getElementById('discountCapPercent').value,
        coupon_discount_cap: document.getElementById('couponDiscountCap').value,
        discount_cap_action: document.getElementById('discountCapAction').value
    };
    
    fetch('/admin/finance/save-pricing-discount-rules', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(d => showToast(d.message, d.success ? 'success' : 'error'));
}

function resolveDispute(id) {
    const resolution = prompt('Enter the dispute resolution agreement summary:');
    if (resolution) {
        fetch('{{ route("admin.finance.resolve-dispute") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ dispute_id: id, resolution_note: resolution })
        })
        .then(r => r.json())
        .then(d => {
            showToast(d.message, d.success ? 'success' : 'error');
            if (d.success) setTimeout(() => location.reload(), 1500);
        });
    }
}

function escalateDispute(id) {
    if (confirm('Escalate this dispute case directly to management review?')) {
        fetch('{{ route("admin.finance.escalate-dispute") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ dispute_id: id })
        })
        .then(r => r.json())
        .then(d => {
            showToast(d.message, d.success ? 'success' : 'error');
        });
    }
}

// ==========================================
// BULK ACTIONS & EXPORT FUNCTIONS
// ==========================================

function bulkApprovePayouts() {
    const ids = Array.from(document.querySelectorAll('.payout-checkbox:checked')).map(cb => cb.value);
    if (ids.length === 0) {
        return showToast('Select at least one payout row', 'error');
    }
    
    if (confirm(`Approve remittance for the ${ids.length} selected payouts?`)) {
        fetch('{{ route("admin.finance.bulk-approve-payouts") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ payout_ids: ids })
        })
        .then(r => r.json())
        .then(d => {
            showToast(d.message, d.success ? 'success' : 'error');
            if (d.success) setTimeout(() => location.reload(), 1500);
        });
    }
}

function exportEarningsLedger() {
    window.location.href = '{{ route("admin.finance.export-transactions") }}';
}

function exportData() {
    exportEarningsLedger();
}

function refreshData() {
    location.reload();
}

// ==========================================
// CHART FUNCTIONS
// ==========================================

function initCommissionTrends(type = 'line') {
    const ctx = document.getElementById('commissionTrendsModern')?.getContext('2d');
    if (!ctx) return;
    
    if (commissionTrendsChart) commissionTrendsChart.destroy();
    
    const labels = @json($trendLabels);
    const commData = @json($trendData);
    const revData = @json($revenueTrendData);
    
    let datasets = [];
    if (type === 'area') {
        datasets = [
            { label: 'Commission Earned', data: commData, borderColor: '#004392', backgroundColor: 'rgba(0, 67, 146, 0.1)', borderWidth: 2, fill: true, tension: 0.4 },
            { label: 'Gross Revenue', data: revData, borderColor: '#20c997', backgroundColor: 'rgba(32, 201, 151, 0.1)', borderWidth: 2, fill: true, tension: 0.4 }
        ];
    } else {
        datasets = [
            { label: 'Commission Earned', data: commData, borderColor: '#004392', backgroundColor: type === 'bar' ? '#004392' : 'rgba(0, 67, 146, 0.05)', borderWidth: 2, fill: false, tension: 0.4 },
            { label: 'Gross Revenue', data: revData, borderColor: '#20c997', backgroundColor: type === 'bar' ? '#20c997' : 'rgba(32, 201, 151, 0.05)', borderWidth: 2, fill: false, tension: 0.4 }
        ];
    }
    
    commissionTrendsChart = new Chart(ctx, {
        type: type === 'area' ? 'line' : type,
        data: { labels: labels, datasets: datasets },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: { legend: { position: 'top', labels: { boxWidth: 10 } } },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { callback: (val) => '{{ $currencySymbol }}' + val.toLocaleString() }
                }
            }
        }
    });
}

function changeChartType(type) {
    const buttons = document.querySelectorAll('.btn-group button');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    initCommissionTrends(type);
}

// ==========================================
// TOAST & UTILITY FUNCTIONS
// ==========================================

function showToast(message, type = 'success') {
    const toast = document.getElementById('toastMessage');
    const toastBody = document.getElementById('toastMessageBody');
    toastBody.textContent = message;
    toast.classList.remove('bg-success', 'bg-danger', 'bg-warning');
    if (type === 'success') toast.classList.add('bg-success');
    else if (type === 'error') toast.classList.add('bg-danger');
    else toast.classList.add('bg-warning');
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
}

// ==========================================
// TAB SWITCHING FUNCTIONS
// ==========================================

function switchSubTab(targetId) {
    document.querySelectorAll('#global-rules-tab .sub-tab-pane').forEach(el => {
        el.classList.add('d-none');
    });
    const targetEl = document.getElementById(targetId);
    if (targetEl) targetEl.classList.remove('d-none');

    document.querySelectorAll('#global-rules-tab .d-flex button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    if (targetId === 'sub-config') document.getElementById('btn-sub-config').classList.add('active');
    else if (targetId === 'sub-table') document.getElementById('btn-sub-table').classList.add('active');
    else if (targetId === 'sub-boost') document.getElementById('btn-sub-boost').classList.add('active');
    else if (targetId === 'sub-pricing') document.getElementById('btn-sub-pricing').classList.add('active');
}

function switchRefundSubTab(targetId) {
    document.querySelectorAll('#refund-dispute-tab .refund-sub-pane').forEach(el => {
        el.classList.add('d-none');
    });
    const targetEl = document.getElementById(targetId);
    if (targetEl) targetEl.classList.remove('d-none');

    document.querySelectorAll('#refund-dispute-tab .d-flex button').forEach(btn => {
        btn.classList.remove('active');
    });
    
    if (targetId === 'sub-payout') document.getElementById('btn-sub-payout').classList.add('active');
    else if (targetId === 'sub-refund') document.getElementById('btn-sub-refund').classList.add('active');
    else if (targetId === 'sub-dispute') document.getElementById('btn-sub-dispute').classList.add('active');
}

// ==========================================

// ==========================================
// SELLER ROW SAVE FUNCTION
// ==========================================

function saveSellerRow(sellerId, btn) {
    const row = document.querySelector(`tr[data-seller-id="${sellerId}"]`);
    if (!row) return;
    
    const holdDays = row.querySelector('.hold-days-input')?.value;
    const payoutFrequency = row.querySelector('.payout-schedule-select')?.value;
    const purchaseDate = row.querySelector('.purchase-date-input')?.value;
    
    btn.disabled = true;
    const originalContent = btn.innerHTML;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
    
    fetch('{{ route("admin.finance.update-earnings-settings") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            seller_id: sellerId,
            hold_days: holdDays,
            payout_frequency: payoutFrequency,
            purchase_date: purchaseDate
        })
    })
    .then(r => r.json())
    .then(d => {
        btn.disabled = false;
        btn.innerHTML = originalContent;
        showToast(d.message, d.success ? 'success' : 'error');
    })
    .catch(err => {
        btn.disabled = false;
        btn.innerHTML = originalContent;
        showToast('Failed to save settings', 'error');
    });
}

// ==========================================
// EVENT LISTENERS
// ==========================================

document.querySelectorAll('.currency-option').forEach(option => {
    option.addEventListener('click', function(e) {
        e.preventDefault();
        const currency = this.dataset.currency;
        const url = new URL(window.location.href);
        url.searchParams.set('currency', currency);
        window.location.href = url.toString();
    });
});

document.getElementById('selectAllPayouts')?.addEventListener('change', function(e) {
    document.querySelectorAll('.payout-checkbox').forEach(cb => cb.checked = e.target.checked);
});

// Auto-recalculate Release Date dynamically
document.addEventListener('input', function(e) {
    if (e.target && (e.target.classList.contains('hold-days-input') || e.target.classList.contains('purchase-date-input'))) {
        const row = e.target.closest('tr');
        if (row) {
            const holdDays = parseInt(row.querySelector('.hold-days-input')?.value) || 0;
            const purchaseDateVal = row.querySelector('.purchase-date-input')?.value;
            const releaseDateSpan = row.querySelector('.release-date-span');
            if (releaseDateSpan && purchaseDateVal) {
                const dateObj = new Date(purchaseDateVal);
                dateObj.setDate(dateObj.getDate() + holdDays);
                const yyyy = dateObj.getFullYear();
                let mm = dateObj.getMonth() + 1;
                let dd = dateObj.getDate();
                if (dd < 10) dd = '0' + dd;
                if (mm < 10) mm = '0' + mm;
                releaseDateSpan.textContent = `${yyyy}-${mm}-${dd}`;
            }
        }
    }
});

// ==========================================
// INITIALIZATION ON DOM LOADED
// ==========================================

document.addEventListener('DOMContentLoaded', () => {
    initCommissionTrends('line');
    
    // Revenue Mix Chart
    const pieCtx = document.getElementById('revenuePieModern')?.getContext('2d');
    if (pieCtx) {
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Direct Sales', 'Service Revenue', 'Lead Revenue', 'Subscription', 'Referral', 'Other'],
                datasets: [{
                    data: [
                        {{ $revenueMix['Direct Sales'] ?? 0 }},
                        {{ $revenueMix['Service Revenue'] ?? 0 }},
                        {{ $revenueMix['Lead Revenue'] ?? 0 }},
                        {{ $revenueMix['Subscription'] ?? 0 }},
                        {{ $revenueMix['Referral'] ?? 0 }},
                        {{ $revenueMix['Other'] ?? 0 }}
                    ],
                    backgroundColor: ['#004392', '#20c997', '#ffc107', '#dc3545', '#17a2b8', '#6c757d'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } }
            }
        });
    }
    
    loadSavedRules();
});

// ==========================================
// AUTO ESCALATION RULES FUNCTIONS
// ==========================================

function addEscalationRuleRow() {
    const tbody = document.getElementById('autoEscalationRulesBody');
    if (!tbody) {
        console.error('Table body not found');
        return;
    }
    
    const uniqueId = Date.now();
    
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-rule-id', uniqueId);
    
    newRow.innerHTML = `
        <td><input type="text" class="form-control form-control-sm rule-condition" placeholder="e.g. Dispute open > 5 days" required></td
        <td><input type="text" class="form-control form-control-sm rule-action" placeholder="e.g. Escalate to Manager" required></td
        <td><input type="text" class="form-control form-control-sm rule-timeframe" placeholder="e.g. 5 days" required></td
        <td><select class="form-select form-select-sm rule-status"><option value="Active">Active</option><option value="Inactive">Inactive</option></select></td
        <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeEscalationRuleRow(this)"><iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon></button></td
    `;
    
    tbody.appendChild(newRow);
    showToast('New escalation rule added', 'success');
}

function removeEscalationRuleRow(button) {
    const row = button.closest('tr');
    if (!row) return;
    
    if (confirm('Are you sure you want to remove this escalation rule?')) {
        row.remove();
        showToast('Escalation rule removed', 'info');
    }
}

function saveDisputeRules() {
    const tbody = document.getElementById('autoEscalationRulesBody');
    if (!tbody) {
        showToast('Error: Rules table not found', 'error');
        return;
    }
    
    const rows = tbody.querySelectorAll('tr');
    const rules = [];
    
    rows.forEach((row, index) => {
        const condition = row.querySelector('.rule-condition')?.value?.trim();
        const action = row.querySelector('.rule-action')?.value?.trim();
        const timeframe = row.querySelector('.rule-timeframe')?.value?.trim();
        const status = row.querySelector('.rule-status')?.value || 'Active';
        
        if (condition && action) {
            rules.push({
                id: index + 1,
                condition: condition,
                action: action,
                timeframe: timeframe || 'N/A',
                status: status
            });
        }
    });
    
    if (rules.length === 0) {
        showToast('Please add at least one escalation rule before saving', 'warning');
        return;
    }
    
    const saveBtn = document.querySelector('#sub-dispute .btn-primary');
    const originalText = saveBtn?.innerHTML;
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    }
    
    fetch('{{ route("admin.finance.update-dispute-rules") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ escalation_rules: rules })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Escalation rules saved successfully!', 'success');
        } else {
            showToast(data.message || 'Failed to save rules', 'error');
        }
    })
    .catch(error => {
        console.error('Error saving dispute rules:', error);
        showToast('An error occurred while saving rules', 'error');
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText || 'Save Escalation Rules';
        }
    });
}

function removeEscalationRuleRowDirect(button) {
    const row = button.closest('tr');
    if (row) {
        row.remove();
        if (typeof showToast === 'function') {
            showToast('Rule removed', 'info');
        }
    }
}
</script>
<script>
// Existing functions plus new ones for Lead Billing Register

function addAppPricingRow() {
    const tbody = document.getElementById('appWisePricingBody');
    const rowCount = tbody.children.length;
    
    const firstRow = tbody.querySelector('tr');
    if(!firstRow) return;
    
    const newRow = firstRow.cloneNode(true);
    
    newRow.querySelectorAll('input').forEach(input => {
        if(input.type === 'number') {
            input.value = '0.50';
        } else {
            input.value = '';
        }
    });
    
    newRow.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });
    
    newRow.querySelectorAll('[name]').forEach(element => {
        const name = element.getAttribute('name');
        if(name) {
            element.setAttribute('name', name.replace(/\[\d+\]/, `[${rowCount}]`));
        }
    });
    
    tbody.appendChild(newRow);
}

function removeAppPricingRow(btn) {
    const tbody = document.getElementById('appWisePricingBody');
    if(tbody.children.length > 1) {
        btn.closest('tr').remove();
    } else {
        alert('At least one row is required');
        const firstRow = tbody.querySelector('tr');
        if(firstRow) {
            firstRow.querySelectorAll('input').forEach(input => {
                if(input.type === 'number') {
                    input.value = '0.50';
                } else {
                    input.value = '';
                }
            });
            firstRow.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });
        }
    }
}

function saveAppWisePricing(event) {
    event.preventDefault();
    
    const pricingData = [];
    const rows = document.querySelectorAll('#appWisePricingBody tr');
    
    rows.forEach((row, index) => {
        const app = row.querySelector('[name*="[app]"]')?.value;
        const categoryId = row.querySelector('[name*="[category_id]"]')?.value;
        const sellerId = row.querySelector('[name*="[seller_id]"]')?.value;
        const clickPrice = row.querySelector('[name*="[click_price]"]')?.value;
        const enquiryPrice = row.querySelector('[name*="[enquiry_price]"]')?.value;
        
        if(app && categoryId && clickPrice && enquiryPrice) {
            const categorySelect = row.querySelector('[name*="[category_id]"]');
            const categoryName = categorySelect.options[categorySelect.selectedIndex]?.text || '';
            
            pricingData.push({
                app: app,
                category_id: categoryId,
                category_name: categoryName,
                seller_id: sellerId || null,
                click_price: parseFloat(clickPrice),
                enquiry_price: parseFloat(enquiryPrice)
            });
        }
    });
    
    if(pricingData.length === 0) {
        alert('Please add at least one pricing rule');
        return;
    }
    
    let previewMessage = `✓ ${pricingData.length} pricing rules saved successfully!\n\n`;
    previewMessage += pricingData.slice(0, 3).map(r => 
        `${r.app.toUpperCase()} - ${r.category_name} - $${r.click_price}/click, $${r.enquiry_price}/enquiry`
    ).join('\n');
    
    if(pricingData.length > 3) {
        previewMessage += `\n...and ${pricingData.length - 3} more`;
    }
    
    alert(previewMessage);
    localStorage.setItem('appWisePricingRules', JSON.stringify(pricingData));
}

// Lead Billing Register Functions
function filterLeadBillingBySeller() {
    const sellerFilter = document.getElementById('leadSellerFilter').value;
    const rows = document.querySelectorAll('#leadBillingTableBody tr');
    
    rows.forEach(row => {
        const sellerCell = row.cells[2]?.textContent;
        if (sellerFilter === '' || sellerCell === sellerFilter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updateLeadBillingTotal();
}

function filterLeadBillingByStatus() {
    const statusFilter = document.getElementById('leadStatusFilter').value;
    const rows = document.querySelectorAll('#leadBillingTableBody tr');
    
    rows.forEach(row => {
        const statusCell = row.cells[9]?.textContent?.trim();
        if (statusFilter === '' || statusCell === statusFilter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updateLeadBillingTotal();
}

function updateLeadBillingTotal() {
    const rows = document.querySelectorAll('#leadBillingTableBody tr:not([style*="display: none"])');
    let total = 0;
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const amountCell = row.cells[8];
            if (amountCell) {
                let amountText = amountCell.textContent.replace('$', '');
                const amount = parseFloat(amountText);
                if (!isNaN(amount)) {
                    total += amount;
                }
            }
        }
    });
    
    const totalCell = document.querySelector('#leadBillingTable tfoot tr td:nth-child(10)');
    if (totalCell) {
        totalCell.textContent = `$${total.toFixed(2)}`;
    }
}

function viewLeadDetails(leadId) {
    const row = document.querySelector(`#leadBillingTableBody tr td:first-child`).closest('tr');
    if (row && row.cells[0]?.textContent === leadId) {
        const details = {
            id: leadId,
            date: row.cells[1]?.textContent,
            seller: row.cells[2]?.textContent,
            leadType: row.cells[3]?.textContent,
            source: row.cells[4]?.textContent,
            customer: row.cells[5]?.textContent,
            contact: row.cells[6]?.textContent,
            amount: row.cells[8]?.textContent,
            status: row.cells[9]?.textContent
        };
        
        alert(`📋 Lead Details:\n\nID: ${details.id}\nDate: ${details.date}\nSeller: ${details.seller}\nLead Type: ${details.leadType}\nSource: ${details.source}\nCustomer: ${details.customer}\nContact: ${details.contact}\nAmount: ${details.amount}\nStatus: ${details.status}`);
    } else {
        alert(`Details for ${leadId} will be shown here`);
    }
}

function exportLeadBillingCSV() {
    const rows = document.querySelectorAll('#leadBillingTableBody tr:not([style*="display: none"])');
    const headers = ['Lead ID', 'Date & Time', 'Seller Name', 'Lead Type', 'Lead Source', 'Customer Name', 'Customer Contact', 'Charge Type', 'Amount ($)', 'Status', 'Invoice Status'];
    
    const csvData = [headers];
    
    rows.forEach(row => {
        const rowData = [];
        for (let i = 0; i < 11; i++) {
            let cellValue = row.cells[i]?.textContent || '';
            cellValue = cellValue.replace(/Click|Enquiry|Fake Lead/g, '').trim();
            rowData.push(cellValue);
        }
        csvData.push(rowData);
    });
    
    const csvContent = csvData.map(row => row.join(',')).join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `lead_billing_export_${new Date().toISOString().split('T')[0]}.csv`;
    a.click();
    URL.revokeObjectURL(url);
    
    alert('✅ Lead billing data exported successfully!');
}

function refreshLeadBillingTable() {
    alert('🔄 Refreshing lead billing data...');
    location.reload();
}

function showProcessRefundModal() {
    alert('🔁 Process Refund Modal - Enter refund details');
}

function createBoostCampaignModal() {
    alert('🚀 Create Boost Campaign - Configure campaign settings');
}

function saveLeadGlobalRules(event) {
    event.preventDefault();
    alert('✅ Lead configuration saved successfully!');
}

function saveLeadBillingRules(event) {
    event.preventDefault();
    alert('✅ Billing rules saved successfully!');
}

function saveLeadRefundRules(event) {
    event.preventDefault();
    alert('✅ Refund rules saved successfully!');
}

let revenueChart;

function updateRevenueChart() {
    const timeframe = document.getElementById('revenueTimeframe').value;
    let labels, data;
    if(timeframe === 'weekly') labels = ['W1','W2','W3','W4'], data = [1850,2100,2450,2800];
    else if(timeframe === 'monthly') labels = ['Jan','Feb','Mar','Apr','May','Jun'], data = [8500,9200,10500,11800,13200,15700];
    else if(timeframe === 'quarterly') labels = ['Q1','Q2','Q3','Q4'], data = [28500,34200,39800,45100];
    else labels = ['2022','2023','2024','2025'], data = [85000,112000,148000,195000];
    
    if(revenueChart) {
        revenueChart.data.labels = labels;
        revenueChart.data.datasets[0].data = data;
        revenueChart.update();
    }
}

function downloadRevenueChart() {
    const canvas = document.getElementById('leadRevenueChart');
    if(canvas) {
        const link = document.createElement('a');
        link.download = 'revenue-chart.png';
        link.href = canvas.toDataURL();
        link.click();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('leadRevenueChart')?.getContext('2d');
    if(ctx && typeof Chart !== 'undefined') {
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                datasets: [{
                    label: 'Revenue',
                    data: [8500, 9200, 10500, 11800, 13200, 15700],
                    borderColor: '#4361ee',
                    backgroundColor: 'rgba(67, 97, 238, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { position: 'top' } }
            }
        });
    }
});



function addAppPricingRow() {
    const tbody = document.getElementById('appWisePricingBody');
    const html = `<tr><td><select class="form-select form-select-sm"><option>vShop</option><option>vTravel</option><option>vCampus</option><option>vEdge</option></select></td><td><input type="text" class="form-control form-control-sm"></td><td><input type="text" class="form-control form-control-sm"></td><td><input type="number" step="0.01" class="form-control form-control-sm"></td><td><input type="number" step="0.01" class="form-control form-control-sm"></td><td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeAppPricingRow(this)">✗</button></td></tr>`;
    tbody.insertAdjacentHTML('beforeend', html);
}

function removeAppPricingRow(btn) { btn.closest('tr').remove(); }
function saveLeadGlobalRules(e) { e.preventDefault(); alert('Lead config saved!'); }
function saveLeadBillingRules(e) { e.preventDefault(); alert('Billing rules saved!'); }
function saveLeadRefundRules(e) { e.preventDefault(); alert('Refund rules saved!'); }
function saveAppWisePricing(e) { e.preventDefault(); alert('App pricing saved!'); }
function showProcessRefundModal() { alert('Process refund modal'); }
function createBoostCampaignModal() { alert('Create campaign modal'); }
</script>



<style>
/* Professional Toast Styling */
.professional-toast {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    min-width: 320px;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    animation: slideInRight 0.3s ease;
}

.professional-toast.toast-success {
    border-left: 4px solid #10b981;
}

.professional-toast.toast-error {
    border-left: 4px solid #ef4444;
}

.professional-toast .toast-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-success .toast-icon {
    background: #d1fae5;
    color: #059669;
}

.toast-error .toast-icon {
    background: #fee2e2;
    color: #dc2626;
}

.professional-toast .toast-content {
    flex: 1;
}

.professional-toast .toast-content strong {
    display: block;
    font-size: 14px;
    margin-bottom: 2px;
}

.professional-toast .toast-content p {
    margin: 0;
    font-size: 12px;
    color: #64748b;
}

.professional-toast .toast-close {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #94a3b8;
    padding: 0;
    line-height: 1;
}

.professional-toast .toast-close:hover {
    color: #475569;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}
</style>
<style>
/* Professional Table Styling */
.custom-earnings-table thead th {
    background: #f8fafc;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #475569;
    border-bottom: 2px solid #e2e8f0;
}

.custom-earnings-table tbody tr:hover {
    background-color: #f8fafc;
    transition: all 0.2s ease;
}

.custom-earnings-table .badge {
    font-size: 10px;
    padding: 4px 8px;
    font-weight: 500;
}

/* Form Controls Styling */
.form-control-sm:focus {
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    outline: none;
}

/* Icon Button Styling */
.btn-icon {
    width: 32px;
    height: 32px;
    padding: 0;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 8px;
}

/* Dropdown Menu Styling */
.dropdown-menu {
    border: none;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    border-radius: 12px;
    padding: 8px 0;
}

.dropdown-item {
    padding: 8px 20px;
    font-size: 13px;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background-color: #f1f5f9;
    padding-left: 24px;
}

/* Modal Styling */
.modal-content {
    border-radius: 16px;
    overflow: hidden;
}

.modal-header {
    padding: 1.25rem 1.5rem;
}

.modal-body {
    padding: 1.5rem;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-opacity-20 {
    background-color: rgba(255, 255, 255, 0.2);
}

/* Date Input Styling */
input[type="date"] {
    cursor: pointer;
}

input[type="date"]::-webkit-calendar-picker-indicator {
    opacity: 0;
    position: absolute;
    right: 0;
    width: 100%;
    cursor: pointer;
}

/* Table Column Widths */
.col-payout-check { width: 40px; }
.col-payout-seller { min-width: 180px; }
.col-payout-app { width: 80px; }
.col-payout-sales { width: 120px; }
.col-payout-comm { width: 120px; }
.col-payout-breakdown { width: 180px; }
.col-payout-payout { width: 120px; }
.col-payout-hold { width: 90px; }
.col-payout-schedule { width: 160px; }
.col-payout-status { width: 100px; }
.col-payout-ref { width: 120px; }
.col-payout-ref-sch { width: 160px; }
.col-payout-ref-status { width: 100px; }
.col-payout-tax { width: 120px; }
.col-payout-net { width: 120px; }
.col-payout-bal { width: 140px; }
.col-payout-action { width: 100px; }

.btn-warning,
.btn-secondary {
    background: #61b3e5 !important;
    color: #fff;
    border: 0 !important;
}
.btn-success {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.btn-outline-danger {
    border-color: rgb(35 61 121);
    color: rgb(35 61 121);
}
.btn-outline-danger:hover {
    background: rgb(35 61 121);
    border-color: rgb(35 61 121);
    color: #fff;
}
.btn-outline-secondary {
    border-color: #61b3e5;
    color: #61b3e5;
}
.btn-outline-secondary:hover {
    background: #61b3e5;
    border-color: #61b3e5;
    color: #fff;
}

/* Table responsive fix */
.table-responsive {
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
}

.table {
    width: 100%;
    margin-bottom: 0;
}

.table td, .table th {
    vertical-align: middle;
    padding: 12px 10px;
}

/* Badge styles */
.bg-success-soft {
    background-color: rgba(32, 201, 151, 0.15);
    color: #20c997;
}
.bg-warning-soft {
    background-color: rgba(255, 193, 7, 0.15);
    color: #ffc107;
}
.bg-danger-soft {
    background-color: rgba(220, 53, 69, 0.15);
    color: #dc3545;
}
.bg-secondary-soft {
    background-color: rgba(108, 117, 125, 0.15);
    color: #6c757d;
}
</style>

<script>
// Professional Save Function
function manualRelease(transactionId, btn) {
    Swal.fire({
        title: 'Release Hold?',
        text: 'Are you sure you want to manually release this transaction hold?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Release',
        confirmButtonColor: '#198754'
    }).then((result) => {
        if (result.isConfirmed) {
            const originalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
            btn.disabled = true;
            
            fetch('{{ route("admin.finance.release-hold") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ transaction_id: transactionId })
            })
            .then(r => r.json())
            .then(res => {
                if(res.success) {
                    showProfessionalToast('Transaction released successfully!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showProfessionalToast(res.message || 'Error releasing transaction', 'error');
                    btn.innerHTML = originalHtml;
                    btn.disabled = false;
                }
            })
            .catch(() => {
                showProfessionalToast('Failed to release transaction', 'error');
                btn.innerHTML = originalHtml;
                btn.disabled = false;
            });
        }
    });
}

// Professional Toast Notification
function showProfessionalToast(message, type = 'success') {
    const toastHtml = `
        <div class="professional-toast toast-${type}">
            <div class="toast-icon">
                <iconify-icon icon="${type === 'success' ? 'solar:check-circle-bold' : 'solar:close-circle-bold'}" width="20"></iconify-icon>
            </div>
            <div class="toast-content">
                <strong>${type === 'success' ? 'Success!' : 'Error!'}</strong>
                <p>${message}</p>
            </div>
            <button class="toast-close" onclick="this.parentElement.remove()">×</button>
        </div>
    `;
    $('body').append(toastHtml);
    setTimeout(() => {
        $('.professional-toast').fadeOut(300, function() { $(this).remove(); });
    }, 3000);
}

// Professional Modal Functions (Dynamic Client-side from earningsDetails)
function showPayoutModal(sellerId) {
    activePayoutSellerId = sellerId;
    const row = earningsDetails.find(r => r.seller_id == sellerId);
    if (!row) return;
    
    const currencySym = '{{ $currencySymbol }}';
    const purchaseDate = new Date(row.purchase_date);
    const payoutDate = new Date(row.release_date);
    const fmtPurchase = purchaseDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
    const fmtPayout = payoutDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
    
    const detailHtml = `
        <div class="p-2">
            <h5 class="fw-bold text-center text-success mb-3">Formula Remittance Statement</h5>
            <div class="d-flex justify-content-between mb-2">
                <span class="text-muted">Total Sales Revenue:</span>
                <span class="fw-bold">${currencySym}${parseFloat(row.total_sales).toFixed(2)}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 text-danger">
                <span>Less: Platform Commission (10%):</span>
                <span>-${currencySym}${parseFloat(row.platform_commission).toFixed(2)}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 text-danger">
                <span>Less: Referral Payout:</span>
                <span>-${currencySym}${parseFloat(row.referral_payout).toFixed(2)}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 text-danger">
                <span>Less: Estimated Tax Draft (GST):</span>
                <span>-${currencySym}${parseFloat(row.est_tax).toFixed(2)}</span>
            </div>
            <hr class="my-2">
            <div class="d-flex justify-content-between mb-3 fw-bold fs-5 text-success">
                <span>Net Payable Balance:</span>
                <span>${currencySym}${parseFloat(row.net_payout).toFixed(2)}</span>
            </div>
            
            <div class="bg-light p-3 rounded border">
                <div class="mb-2 small"><strong>Remittance Schedule:</strong> ${row.payout_schedule}</div>
                <div class="mb-2 small"><strong>Remittance Status:</strong> <span class="badge bg-${row.payout_status == 'Approved' ? 'success' : 'warning'}-soft text-${row.payout_status == 'Approved' ? 'success' : 'warning'}">${row.payout_status}</span></div>
                
                <div class="border-top pt-2 mt-2">
                    <span class="d-block fw-bold text-primary small mb-1"><iconify-icon icon="solar:clock-circle-bold-duotone" class="align-middle"></iconify-icon> Payout Release Rule:</span>
                    <ul class="ps-3 mb-0 small text-muted">
                        <li>Payment Hold Period: <strong>${row.hold_days} Days</strong> (Return/Refund protection)</li>
                        <li>Sales Recorded Date: <strong>${fmtPurchase}</strong></li>
                        <li>Est. Release Date: <strong>${fmtPayout}</strong></li>
                        <li class="mt-1"><span class="text-dark">Formula:</span> Purchase Date (${fmtPurchase}) + ${row.hold_days} days = Release Date (${fmtPayout})</li>
                    </ul>
                </div>
            </div>
        </div>
    `;
    const targetEl = document.getElementById('payoutModalContent');
    if (targetEl) targetEl.innerHTML = detailHtml;
    new bootstrap.Modal(document.getElementById('payoutModal')).show();
}

function showTaxBreakdown(sellerId) {
    const row = earningsDetails.find(r => r.seller_id == sellerId);
    if (!row) return;
    
    const currencySym = '{{ $currencySymbol }}';
    const gstRate = '{{ $gstRate }}';
    const html = `
        <h6 class="fw-bold mb-3">Estimated Tax (Draft Breakdown) for ${row.name}</h6>
        <div class="list-group list-group-flush mb-3">
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>Taxable Basis (Platform Commission)</span>
                <span class="fw-semibold">${currencySym}${parseFloat(row.platform_commission).toFixed(2)}</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <span>GST / Tax Rate</span>
                <span class="badge bg-primary-soft text-primary">${gstRate}%</span>
            </div>
            <div class="list-group-item d-flex justify-content-between align-items-center bg-light">
                <span class="fw-bold">Total Estimated Tax Collected</span>
                <span class="fw-bold text-danger">${currencySym}${parseFloat(row.est_tax).toFixed(2)}</span>
            </div>
        </div>
        <div class="bg-light p-3 rounded border fs-8 text-muted">
            <iconify-icon icon="solar:info-circle-bold-duotone" class="align-middle text-info me-1"></iconify-icon>
            Tax draft is calculated automatically based on platform commission fees and the configured regional GST/tax rates.
        </div>
    `;
    const targetEl = document.getElementById('taxModalContent');
    if (targetEl) targetEl.innerHTML = html;
    new bootstrap.Modal(document.getElementById('taxModal')).show();
}

function showEarningBreakdown(sellerId) {
    const row = earningsDetails.find(r => r.seller_id == sellerId);
    if (!row) return;
    
    const currencySym = '{{ $currencySymbol }}';
    const html = `
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th style="width: 60%;">Component</th>
                        <th class="text-end" style="width: 40%;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <strong>Gross Order Value (Total Sales)</strong>
                            <div class="text-muted fs-9">Total revenue generated from buyer orders</div>
                        </td>
                        <td class="text-end text-success fw-bold">${currencySym}${parseFloat(row.total_sales).toFixed(2)}</td>
                    </tr>
                    <tr class="table-light">
                        <td colspan="2" class="fw-semibold text-secondary py-1 bg-opacity-10 fs-8">Platform Fees & Deductions</td>
                    </tr>
                    <tr>
                        <td>
                            Platform / Admin Commission (10%)
                            <div class="text-muted fs-9">Base commission collected by the marketplace admin</div>
                        </td>
                        <td class="text-end text-danger">-${currencySym}${parseFloat(row.platform_commission).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>
                            Referral Partner Commission
                            <div class="text-muted fs-9">Commission share distributed to referring partner (0.6%)</div>
                        </td>
                        <td class="text-end text-danger">-${currencySym}${parseFloat(row.referral_payout).toFixed(2)}</td>
                    </tr>
                    <tr>
                        <td>
                            Estimated Tax & GST
                            <div class="text-muted fs-9">GST tax draft on platform commission</div>
                        </td>
                        <td class="text-end text-danger">-${currencySym}${parseFloat(row.est_tax).toFixed(2)}</td>
                    </tr>
                    <tr class="table-success font-monospace">
                        <td>
                            <strong>Seller Share (Net Payout)</strong>
                            <div class="text-muted fs-9">Amount scheduled for bank payout transfer to seller</div>
                        </td>
                        <td class="text-end text-success fw-bold">${currencySym}${parseFloat(row.net_payout).toFixed(2)}</td>
                    </tr>
                    <tr class="table-info font-monospace">
                        <td>
                            <strong>Platform Admin Earning (Net Admin Revenue)</strong>
                            <div class="text-muted fs-9">Net commission profit retained by marketplace admin</div>
                        </td>
                        <td class="text-end text-primary fw-bold">${currencySym}${parseFloat(row.platform_commission).toFixed(2)}</td>
                    </tr>
                    <tr class="table-warning font-monospace">
                        <td>
                            <strong>Lead Enquiries & Clicks charges</strong>
                            <div class="text-muted fs-9">Billing charges collected for platform dynamic leads</div>
                        </td>
                        <td class="text-end text-dark fw-bold">${currencySym}${parseFloat(parseFloat(row.per_enquiry_amount) + parseFloat(row.per_click_amount)).toFixed(2)}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    `;
    const targetEl = document.getElementById('earningBreakdownContent');
    if (targetEl) targetEl.innerHTML = html;
    new bootstrap.Modal(document.getElementById('earningBreakdownModal')).show();
}

function filterHoldReasons() {
    let val = document.getElementById('searchHoldReason').value.toLowerCase();
    let rows = document.querySelectorAll('#holdReasonTableBody tr');
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(val) ? '' : 'none';
    });
}

function filterHoldTracker() {
    let val = document.getElementById('searchHoldTracker').value.toLowerCase();
    let rows = document.querySelectorAll('#settlementHoldTableBody tr');
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(val) ? '' : 'none';
    });
}

function showHoldReasonModal() {
    let html = '';
    earningsDetails.forEach(row => {
        if (row.hold_days > 0 || row.hold_reason) {
            const purchaseDate = new Date(row.purchase_date);
            const fmtPurchase = purchaseDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
            
            html += `
                <tr>
                    <td class="fw-bold">${row.seller_code}</td>
                    <td>${row.name}</td>
                    <td><span class="text-muted small">${row.hold_reason || 'SLA Verification Hold'}</span></td>
                    <td><span class="badge bg-secondary-soft text-secondary">System Admin</span></td>
                    <td>${fmtPurchase}</td>
                    <td>
                        <span class="badge bg-warning-soft text-warning">
                            ${row.payout_status}
                        </span>
                    </td>
                </tr>
            `;
        }
    });

    if (!html) {
        html = `<tr><td colspan="6" class="text-center text-muted py-3">No active holds found.</td></tr>`;
    }

    const targetEl = document.getElementById('holdReasonTableBody');
    if (targetEl) targetEl.innerHTML = html;
    new bootstrap.Modal(document.getElementById('holdReasonModal')).show();
}

function showSettlementHoldModal() {
    const currencySym = '{{ $currencySymbol }}';
    const today = new Date();
    
    let html = '';
    earningsDetails.forEach(row => {
        const purchaseDate = new Date(row.purchase_date);
        const releaseDate = new Date(row.release_date);
        const fmtPurchase = purchaseDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
        const fmtRelease = releaseDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
        
        let remainingOrDelayText = '';
        let badgeColor = '';
        let isPending = row.payout_status.toLowerCase() === 'pending';
        
        if (isPending) {
            if (today > releaseDate) {
                const diffTime = Math.abs(today - releaseDate);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                remainingOrDelayText = `<span class="text-danger fw-bold">Delayed ${diffDays} Days</span>`;
                badgeColor = 'danger';
            } else {
                const diffTime = Math.abs(releaseDate - today);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                remainingOrDelayText = `<span class="text-warning">Releasing in ${diffDays} Days</span>`;
                badgeColor = 'warning';
            }
        } else {
            remainingOrDelayText = `<span class="text-success">Released on Time</span>`;
            badgeColor = 'success';
        }
        
        html += `
            <tr>
                <td class="fw-bold">${row.seller_code}</td>
                <td>${row.name}</td>
                <td><span class="badge bg-secondary-soft text-secondary text-uppercase">${row.app}</span></td>
                <td class="text-center fw-bold">${row.hold_days} Days</td>
                <td>${fmtPurchase}</td>
                <td>${fmtRelease}</td>
                <td><span class="text-muted small">${row.hold_reason || 'N/A'}</span></td>
                <td class="text-center">${remainingOrDelayText}</td>
                <td>
                    <span class="badge bg-${badgeColor}-soft text-${badgeColor}">
                        ${row.payout_status}
                    </span>
                </td>
            </tr>
        `;
    });
    
    const targetEl = document.getElementById('settlementHoldTableBody');
    if (targetEl) targetEl.innerHTML = html;
    new bootstrap.Modal(document.getElementById('settlementHoldModal')).show();
}

function showSellerDetails(sellerId) {
    const row = earningsDetails.find(r => r.seller_id == sellerId);
    if (!row) return;
    
    const titleEl = document.getElementById('flowModalTitle');
    if (titleEl) titleEl.textContent = `Settlement Flow: ${row.name} (${row.seller_code})`;
    
    const currencySym = '{{ $currencySymbol }}';
    const purchaseDate = new Date(row.purchase_date);
    const releaseDate = new Date(row.release_date);
    const fmtPurchase = purchaseDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
    const fmtRelease = releaseDate.toLocaleDateString('en-US', { day: 'numeric', month: 'short', year: 'numeric' });
    
    const today = new Date();
    const isReleased = row.payout_status.toLowerCase() !== 'pending';
    const isDelayed = today > releaseDate && !isReleased;
    
    let appText = Array.isArray(row.app) ? row.app.map(p => p.name).join(', ') : row.app;
    
    const timelineHtml = `
        <div class="row mb-4">
            <div class="col-md-6 border-end">
                <h6 class="fw-bold text-secondary mb-3"><iconify-icon icon="solar:info-circle-bold-duotone" class="align-middle me-1"></iconify-icon> Escrow Parameters</h6>
                <table class="table table-sm table-borderless fs-7">
                    <tr><td class="text-muted">Application Source:</td><td class="fw-semibold text-uppercase">${appText}</td></tr>
                    <tr><td class="text-muted">Total Sales Value:</td><td class="fw-semibold">${currencySym}${parseFloat(row.total_sales).toFixed(2)}</td></tr>
                    <tr><td class="text-muted">Platform Admin Share:</td><td class="fw-semibold text-primary">${currencySym}${parseFloat(row.platform_commission).toFixed(2)}</td></tr>
                    <tr><td class="text-muted">Net Seller Payout:</td><td class="fw-semibold text-success">${currencySym}${parseFloat(row.net_payout).toFixed(2)}</td></tr>
                    <tr><td class="text-muted">Billing Location:</td><td class="fw-semibold">${row.location}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold text-secondary mb-3"><iconify-icon icon="solar:clock-circle-bold-duotone" class="align-middle me-1"></iconify-icon> Hold Timeline</h6>
                <table class="table table-sm table-borderless fs-7">
                    <tr><td class="text-muted">Order Date:</td><td class="fw-semibold">${fmtPurchase}</td></tr>
                    <tr><td class="text-muted">Release Period:</td><td class="fw-semibold text-warning">${row.hold_days} Days Hold</td></tr>
                    <tr><td class="text-muted">Scheduled Release:</td><td class="fw-semibold text-success">${fmtRelease}</td></tr>
                    <tr>
                        <td class="text-muted">Flow Status:</td>
                        <td>
                            <span class="badge bg-${isReleased ? 'success' : (isDelayed ? 'danger' : 'warning')}-soft text-${isReleased ? 'success' : (isDelayed ? 'danger' : 'warning')}">
                                ${isReleased ? 'Released & Paid' : (isDelayed ? 'Delayed Release' : 'In Hold Escrow')}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <h6 class="fw-bold text-secondary mb-3"><iconify-icon icon="solar:checklist-bold-duotone" class="align-middle me-1"></iconify-icon> Settlement Pipeline Flowchart</h6>
        <div class="d-flex flex-column gap-3 p-3 bg-light rounded border">
            <div class="d-flex align-items-start gap-3">
                <div class="badge rounded-circle bg-success text-white p-2" style="z-index: 10;">
                    <iconify-icon icon="solar:check-circle-bold" width="18"></iconify-icon>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-success">Step 1: Order Paid & Sales Logged</h6>
                    <p class="mb-0 text-muted fs-8">Sales value of ${currencySym}${parseFloat(row.total_sales).toFixed(2)} recorded on ${fmtPurchase}.</p>
                </div>
            </div>
            
            <div class="d-flex align-items-start gap-3">
                <div class="badge rounded-circle bg-${isReleased ? 'success' : 'warning'} text-white p-2" style="z-index: 10;">
                    <iconify-icon icon="${isReleased ? 'solar:check-circle-bold' : 'solar:clock-circle-bold'}" width="18"></iconify-icon>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-${isReleased ? 'success' : 'warning'}">Step 2: Escrow Hold Period (${row.hold_days} Days)</h6>
                    <p class="mb-0 text-muted fs-8">Funds locked in marketplace escrow pool to protect against returns. Scheduled release: ${fmtRelease}.</p>
                </div>
            </div>
            
            <div class="d-flex align-items-start gap-3">
                <div class="badge rounded-circle bg-${isReleased ? 'success' : (isDelayed ? 'danger' : 'secondary')} text-white p-2" style="z-index: 10;">
                    <iconify-icon icon="${isReleased ? 'solar:check-circle-bold' : (isDelayed ? 'solar:shield-warning-bold' : 'solar:shield-bold')}" width="18"></iconify-icon>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-${isReleased ? 'success' : (isDelayed ? 'danger' : 'secondary')}">Step 3: Dispute & Refund Window Cleared</h6>
                    <p class="mb-0 text-muted fs-8">${isReleased ? 'Escrow cleared with no disputes.' : (isDelayed ? 'Hold period elapsed but payment approval is pending.' : 'Dispute/return window is currently active.')}</p>
                </div>
            </div>
            
            <div class="d-flex align-items-start gap-3">
                <div class="badge rounded-circle bg-${isReleased ? 'success' : 'secondary'} text-white p-2" style="z-index: 10;">
                    <iconify-icon icon="${isReleased ? 'solar:check-read-bold' : 'solar:card-send-bold'}" width="18"></iconify-icon>
                </div>
                <div>
                    <h6 class="mb-0 fw-bold text-${isReleased ? 'success' : 'secondary'}">Step 4: Bank Remittance Complete</h6>
                    <p class="mb-0 text-muted fs-8">${isReleased ? 'Payout generated and transferred to seller account.' : 'Pending release. Will transfer upon approval.'}</p>
                </div>
            </div>
        </div>
    `;
    const targetEl = document.getElementById('sellerDetailsModalContent');
    if (targetEl) targetEl.innerHTML = timelineHtml;
    new bootstrap.Modal(document.getElementById('sellerDetailsModal')).show();
}



function processPayout() {
    showProfessionalToast('Payout processed successfully!', 'success');
    $('#payoutModal').modal('hide');
}

// Other functions
function generateInvoiceForSeller(sellerId) { console.log('Generate invoice:', sellerId); }
function configureInvoiceTemplate(sellerId) { console.log('Configure template:', sellerId); }
function showSellerHistory(sellerId) { console.log('Show history:', sellerId); }
</script>
<script>
// Commission Total Calculator
function calculateCommissionTotal() {
    let total = 0;
    document.querySelectorAll('#commissionLevelsBody .level-value').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('commissionTotal').innerHTML = total + '%';
    if (total !== 100) {
        document.getElementById('commissionTotal').classList.add('text-danger');
    } else {
        document.getElementById('commissionTotal').classList.remove('text-danger');
        document.getElementById('commissionTotal').classList.add('text-success');
    }
    return total;
}

// Add new commission level row
function addCommissionLevelRow() {
    const tbody = document.getElementById('commissionLevelsBody');
    const rowCount = tbody.children.length;
    const newRow = `
        <tr>
            <td><span class="badge bg-secondary">${rowCount + 1}</span></td>
            <td><input type="text" class="form-control form-control-sm level-role" placeholder="Role Name" required></td>
            <td>
                <select class="form-select form-select-sm level-type">
                    <option value="%">%</option>
                    <option value="Fixed">Fixed</option>
                </select>
            </td>
            <td><input type="number" step="0.01" class="form-control form-control-sm level-value" value="0" required onchange="calculateCommissionTotal()"></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCommissionLevelRow(this)">
                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                </button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
    calculateCommissionTotal();
}

function removeCommissionLevelRow(btn) {
    btn.closest('tr').remove();
    // Re-number levels
    document.querySelectorAll('#commissionLevelsBody tr').forEach((row, idx) => {
        row.querySelector('.badge').innerText = idx + 1;
    });
    calculateCommissionTotal();
}

// Save Commission Structure
function saveCommissionStructure() {
    const total = calculateCommissionTotal();
    if (total !== 100) {
        Swal.fire('Error', 'Total commission must be 100%', 'error');
        return;
    }
    
    const levels = [];
    document.querySelectorAll('#commissionLevelsBody tr').forEach((row, idx) => {
        levels.push({
            level: idx + 1,
            role: row.querySelector('.level-role').value,
            type: row.querySelector('.level-type').value,
            value: parseFloat(row.querySelector('.level-value').value)
        });
    });
    
    $.ajax({
        url: '{{ route("admin.update.commission") }}',
        method: 'POST',
        data: { levels: levels, _token: '{{ csrf_token() }}' },
        success: function(response) {
            Swal.fire('Success', response.message, 'success');
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
        }
    });
}

// Override Functions
function addSellerOverrideRow() {
    const tbody = document.getElementById('sellerOverrideBody');
    const newRow = `
        <tr>
            <td>
                <select class="form-select form-select-sm seller-select">
                    <option value="">Select Seller</option>
                    @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" class="form-control form-control-sm seller-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm referrer-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm franchise-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm platform-val" placeholder="%"></td>
            <td><button class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">✖</button></td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
}

function addCategoryOverrideRow() {
    const tbody = document.getElementById('categoryOverrideBody');
    const newRow = `
        <tr>
            <td>
                <select class="form-select form-select-sm category-select">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </td>
            <td><input type="number" class="form-control form-control-sm category-seller-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm category-referrer-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm category-franchise-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm category-platform-val" placeholder="%"></td>
            <td><button class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">✖</button></td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
}

function addAppOverrideRow() {
    const tbody = document.getElementById('appOverrideBody');
    const newRow = `
        <tr>
            <td>
                <select class="form-select form-select-sm app-select">
                    <option value="">Select App</option>
                    <option value="vshop">vShop</option>
                    <option value="vtravel">vTravel</option>
                    <option value="vcampus">vCampus</option>
                    <option value="vedge">vEdge</option>
                    <option value="wellness">Wellness</option>
                </select>
            </td>
            <td><input type="number" class="form-control form-control-sm app-seller-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm app-referrer-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm app-franchise-val" placeholder="%"></td>
            <td><input type="number" class="form-control form-control-sm app-platform-val" placeholder="%"></td>
            <td><button class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">✖</button></td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
}

function saveAllOverrides() {
    // Collect seller overrides
    const sellerOverrides = [];
    document.querySelectorAll('#sellerOverrideBody tr').forEach(row => {
        const sellerId = row.querySelector('.seller-select')?.value;
        if (sellerId) {
            sellerOverrides.push({
                seller_id: sellerId,
                seller: row.querySelector('.seller-val')?.value || 0,
                referrer: row.querySelector('.referrer-val')?.value || 0,
                franchise: row.querySelector('.franchise-val')?.value || 0,
                platform: row.querySelector('.platform-val')?.value || 0
            });
        }
    });
    
    // Collect category overrides
    const categoryOverrides = [];
    document.querySelectorAll('#categoryOverrideBody tr').forEach(row => {
        const categoryId = row.querySelector('.category-select')?.value;
        if (categoryId) {
            categoryOverrides.push({
                category_id: categoryId,
                seller: row.querySelector('.category-seller-val')?.value || 0,
                referrer: row.querySelector('.category-referrer-val')?.value || 0,
                franchise: row.querySelector('.category-franchise-val')?.value || 0,
                platform: row.querySelector('.category-platform-val')?.value || 0
            });
        }
    });
    
    // Collect app overrides
    const appOverrides = [];
    document.querySelectorAll('#appOverrideBody tr').forEach(row => {
        const app = row.querySelector('.app-select')?.value;
        if (app) {
            appOverrides.push({
                app: app,
                seller: row.querySelector('.app-seller-val')?.value || 0,
                referrer: row.querySelector('.app-referrer-val')?.value || 0,
                franchise: row.querySelector('.app-franchise-val')?.value || 0,
                platform: row.querySelector('.app-platform-val')?.value || 0
            });
        }
    });
    
    $.ajax({
        url: '{{ route("admin.update.overrides") }}',
        method: 'POST',
        data: {
            seller_overrides: sellerOverrides,
            category_overrides: categoryOverrides,
            app_overrides: appOverrides,
            _token: '{{ csrf_token() }}'
        },
        success: function(response) {
            Swal.fire('Success', response.message, 'success');
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
        }
    });
}

// Settlement Hold Functions
function saveSettlementRules(event) {
    event.preventDefault();
    const formData = new FormData(event.target);
    
    $.ajax({
        url: '{{ route("admin.update.settlement") }}',
        method: 'POST',
        data: Object.fromEntries(formData),
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        success: function(response) {
            Swal.fire('Success', response.message, 'success');
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
        }
    });
}

function applyManualHoldSubmit() {
    const sellerId = document.getElementById('manualHoldSellerId').value;
    const reason = document.getElementById('manualHoldReason').value;
    
    if (!sellerId) {
        Swal.fire('Error', 'Please select a seller', 'error');
        return;
    }
    if (!reason) {
        Swal.fire('Error', 'Please enter a reason', 'error');
        return;
    }
    
    $.ajax({
        url: '{{ route("admin.apply.hold") }}',
        method: 'POST',
        data: { seller_id: sellerId, reason: reason, _token: '{{ csrf_token() }}' },
        success: function(response) {
            Swal.fire('Success', response.message, 'success');
            document.getElementById('manualHoldReason').value = '';
        },
        error: function(xhr) {
            Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong', 'error');
        }
    });
}
</script>

<style>
.position-sticky {
    position: sticky;
}
.top-0 {
    top: 0;
}
.bg-dark {
    background-color: #1e293b !important;
}
.bg-secondary {
    background-color: #475569 !important;
}
.table-responsive::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}
.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}
.table-responsive::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}
.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #555;
}
.table tbody tr:hover {
    background-color: rgba(59, 130, 246, 0.05);
    transition: all 0.2s ease;
}
.form-control-sm:focus, .form-select-sm:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
}

 /* Sweet Alert custom styling */
    .swal2-popup {
        font-family: 'Inter', sans-serif;
        border-radius: 1rem;
    }
    .swal2-title {
        color: #1e3a8a;
    }
    .swal2-confirm {
        border-radius: 30px !important;
    }
    .swal2-cancel {
        border-radius: 30px !important;
    }
</style>




<script>
// Global counter for rows
let rowCounter = document.querySelectorAll('#commissionTableBody tr').length;

// Filter functions
function filterTableByApp() {
    const appFilter = document.getElementById('filterAppType').value;
    const rows = document.querySelectorAll('#commissionTableBody tr');
    rows.forEach(row => {
        const appValue = row.querySelector('.app-type-select')?.value;
        if (!appFilter || appValue === appFilter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    updateSummary();
}

function filterTableBySellType() {
    const sellFilter = document.getElementById('filterSellType').value;
    const rows = document.querySelectorAll('#commissionTableBody tr');
    rows.forEach(row => {
        const sellValue = row.querySelector('.sell-type-select')?.value;
        if (!sellFilter || sellValue === sellFilter) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    updateSummary();
}

function clearFilters() {
    document.getElementById('filterAppType').value = '';
    document.getElementById('filterSellType').value = '';
    document.querySelectorAll('#commissionTableBody tr').forEach(row => {
        row.style.display = '';
    });
    updateSummary();
}

// Add new commission row
function addCommissionRow() {
    rowCounter++;
    const tbody = document.getElementById('commissionTableBody');
    const newRow = `
        <tr>
            <td class="text-center">${rowCounter}</td>
            <td>
                <select class="form-select form-select-sm seller-select" onchange="updateCommissionRow(this)">
                    <option value="">-- Select Seller --</option>
                    @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}" data-name="{{ $seller->name }}" data-application="{{ $seller->application ?? 'vshop' }}">{{ $seller->name }} ({{ $seller->application ?? 'vshop' }})</option>
                    @endforeach
                </select>
              </td>
            <td>
                <select class="form-select form-select-sm app-type-select" onchange="updateCommissionRow(this)">
                    <option value="vshop">vShop</option>
                    <option value="vtravel">vTravel</option>
                    <option value="vcampus">vCampus</option>
                    <option value="vedge">vEdge</option>
                </select>
              </td>
            <td>
                <select class="form-select form-select-sm sell-type-select" onchange="updateCommissionRow(this)">
                    <option value="direct">Direct Sale</option>
                    <option value="dropshipping">Dropshipping</option>
                    <option value="affiliate">Affiliate</option>
                    <option value="wholesale">Wholesale</option>
                </select>
              </td>
            <td>
                <input type="number" class="form-control form-control-sm total-sales" placeholder="0.00" onchange="calculateCommission(this)">
              </td>
            <td>
                <select class="form-select form-select-sm commission-type-select" onchange="updateCommissionRow(this)">
                    <option value="percentage">Percentage (%)</option>
                    <option value="fixed">Fixed Amount</option>
                    <option value="tier">Tier Based</option>
                </select>
              </td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm seller-commission" placeholder="%" value="60" onchange="calculateTotalCommission(this)">
              </td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm referrer-commission" placeholder="%" value="10" onchange="calculateTotalCommission(this)">
              </td>
            <td>
                <input type="number" step="0.01" class="form-control form-control-sm platform-commission" placeholder="%" value="30" onchange="calculateTotalCommission(this)">
              </td>
            <td>
                <select class="form-select form-select-sm referral-select">
                    <option value="">-- No Referral --</option>
                    @foreach($sellers as $seller)
                    <option value="{{ $seller->id }}">{{ $seller->name }}</option>
                    @endforeach
                </select>
              </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCommissionRow(this)">
                    <iconify-icon icon="solar:trash-bin-trash-bold"></iconify-icon>
                </button>
            </td>
        </tr>
    `;
    tbody.insertAdjacentHTML('beforeend', newRow);
    updateSummary();
}

// Remove commission row
function removeCommissionRow(btn) {
    if (confirm('Remove this commission row?')) {
        btn.closest('tr').remove();
        document.querySelectorAll('#commissionTableBody tr').forEach((row, idx) => {
            row.cells[0].innerText = idx + 1;
        });
        rowCounter = document.querySelectorAll('#commissionTableBody tr').length;
        updateSummary();
        if (typeof saveAllCommissionsSilent === 'function') saveAllCommissionsSilent();
    }
}

// Calculate commission based on total sales
function calculateCommission(input) {
    updateSummary();
}

// Validate total commission = 100%
function calculateTotalCommission(input) {
    const row = input.closest('tr');
    const seller = parseFloat(row.querySelector('.seller-commission').value) || 0;
    const referrer = parseFloat(row.querySelector('.referrer-commission').value) || 0;
    const platform = parseFloat(row.querySelector('.platform-commission').value) || 0;
    const total = seller + referrer + platform;
    
    row.classList.remove('table-danger');
    row.classList.add('table-success');
    
    updateSummary();
}

// Update row based on selections
function updateCommissionRow(element) {
    const row = element.closest('tr');
    const sellerSelect = row.querySelector('.seller-select');
    const sellerId = sellerSelect.value;
    const appTypeSelect = row.querySelector('.app-type-select');
    const sellType = row.querySelector('.sell-type-select').value;
    
    // Auto-set app type based on seller's platform (dependent dropdown)
    if (sellerId && element.classList.contains('seller-select')) {
        const selectedOption = sellerSelect.options[sellerSelect.selectedIndex];
        const sellerApp = selectedOption.getAttribute('data-application') || 'vshop';
        appTypeSelect.value = sellerApp;
    }
    
    const appType = appTypeSelect.value;
    
    if (sellerId) {
        // Fetch commission rates based on app, sell type, and seller
        fetch('{{ route("admin.get.commission.rates") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ seller_id: sellerId, app_type: appType, sell_type: sellType })
        })
        .then(response => response.json())
        .then(response => {
            row.querySelector('.seller-commission').value = response.seller_rate || 60;
            row.querySelector('.referrer-commission').value = response.referrer_rate || 10;
            row.querySelector('.platform-commission').value = response.platform_rate || 30;
            row.querySelector('.total-sales').value = response.total_sales || 0;
            calculateTotalCommission(row.querySelector('.seller-commission'));
        })
        .catch(() => {
            // Use default values if fetch fails
            row.querySelector('.seller-commission').value = 60;
            row.querySelector('.referrer-commission').value = 10;
            row.querySelector('.platform-commission').value = 30;
            calculateTotalCommission(row.querySelector('.seller-commission'));
        });
    }
}

// Update summary statistics
function updateSummary() {
    // Summary cards and totals row removed — no DOM updates needed
    return;
}

// Generate referral link
function generateReferral() {
    const sellerId = document.getElementById('referralSellerId').value;
    if (!sellerId) {
        Swal.fire('Error', 'Please select a seller', 'error');
        return;
    }
    
    const referralCode = 'REF-' + sellerId + '-' + Date.now();
    document.getElementById('referralCode').value = referralCode;
    
    const referralLink = window.location.origin + '/register?ref=' + referralCode;
    
    Swal.fire({
        title: 'Referral Link Generated',
        html: `<input class="form-control" value="${referralLink}" readonly>`,
        icon: 'success',
        showCloseButton: true
    });
}

// Add new seller
function addNewSeller() {
    const name = document.getElementById('newSellerName').value;
    const app = document.getElementById('newSellerApp').value;
    const type = document.getElementById('newSellerType').value;
    
    if (!name) {
        Swal.fire('Error', 'Please enter seller name', 'error');
        return;
    }
    
    fetch('{{ route("admin.add.seller") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ name: name, application: app, sell_type: type })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire('Success', 'Seller added successfully', 'success');
            setTimeout(() => location.reload(), 1500);
        } else {
            Swal.fire('Error', data.message || 'Something went wrong', 'error');
        }
    })
    .catch(error => {
        Swal.fire('Error', 'Something went wrong', 'error');
    });
}

// Silent save for on-change auto-save
function saveAllCommissionsSilent() {
    const commissions = [];
    document.querySelectorAll('#commissionTableBody tr').forEach(row => {
        const sellerId = row.querySelector('.seller-select').value;
        if (!sellerId) return;
        commissions.push({
            seller_id: sellerId,
            app_type: row.querySelector('.app-type-select').value,
            sell_type: row.querySelector('.sell-type-select').value,
            total_sales: row.querySelector('.total-sales').value || 0,
            commission_type: row.querySelector('.commission-type-select').value,
            seller_commission: parseFloat(row.querySelector('.seller-commission').value) || 0,
            referrer_commission: parseFloat(row.querySelector('.referrer-commission').value) || 0,
            platform_commission: parseFloat(row.querySelector('.platform-commission').value) || 0,
            referral_id: row.querySelector('.referral-select').value
        });
    });
    
    fetch('{{ route("admin.save.commissions") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ commissions: commissions })
    }).then(r => r.json()).catch(e => console.log('Silent save error:', e));
}

// Save all commissions
function saveAllCommissions() {
    const commissions = [];
    
    document.querySelectorAll('#commissionTableBody tr').forEach(row => {
        const sellerId = row.querySelector('.seller-select').value;
        if (!sellerId) return;
        
        const seller = parseFloat(row.querySelector('.seller-commission').value) || 0;
        const referrer = parseFloat(row.querySelector('.referrer-commission').value) || 0;
        const platform = parseFloat(row.querySelector('.platform-commission').value) || 0;
        
        commissions.push({
            seller_id: sellerId,
            app_type: row.querySelector('.app-type-select').value,
            sell_type: row.querySelector('.sell-type-select').value,
            total_sales: row.querySelector('.total-sales').value || 0,
            commission_type: row.querySelector('.commission-type-select').value,
            seller_commission: seller,
            referrer_commission: referrer,
            platform_commission: platform,
            referral_id: row.querySelector('.referral-select').value
        });
    });
    
    fetch('{{ route("admin.save.commissions") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ commissions: commissions })
    })
    .then(response => response.json())
    .then(data => {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: data.message || 'Commissions saved successfully!',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    })
    .catch(error => {
        Swal.fire('Error', 'Something went wrong', 'error');
    });
}

// Validate all commissions
function validateAllCommissions() {
    document.querySelectorAll('#commissionTableBody tr').forEach((row, idx) => {
        row.classList.remove('table-danger');
        row.classList.add('table-success');
    });
    Swal.fire('Success', 'All commission rules are saved and valid.', 'success');
}

// Bulk update commissions
function bulkUpdateCommissions() {
    Swal.fire({
        title: 'Bulk Update Commission',
        html: `
            <select id="bulkAppType" class="swal2-select mb-2" style="width:100%; padding:8px;">
                <option value="">All Apps</option>
                <option value="vshop">vShop</option>
                <option value="vtravel">vTravel</option>
                <option value="vcampus">vCampus</option>
                <option value="vedge">vEdge</option>
            </select>
            <select id="bulkSellType" class="swal2-select mb-2" style="width:100%; padding:8px;">
                <option value="">All Sell Types</option>
                <option value="direct">Direct</option>
                <option value="dropshipping">Dropshipping</option>
                <option value="affiliate">Affiliate</option>
            </select>
            <input type="number" id="bulkSellerComm" class="swal2-input" placeholder="Seller Commission %" style="width:100%; margin-bottom:8px;">
            <input type="number" id="bulkReferrerComm" class="swal2-input" placeholder="Referrer Commission %" style="width:100%; margin-bottom:8px;">
            <input type="number" id="bulkPlatformComm" class="swal2-input" placeholder="Platform Commission %" style="width:100%;">
        `,
        showCancelButton: true,
        confirmButtonText: 'Apply Update',
        preConfirm: () => {
            return {
                appType: document.getElementById('bulkAppType').value,
                sellType: document.getElementById('bulkSellType').value,
                sellerComm: document.getElementById('bulkSellerComm').value,
                referrerComm: document.getElementById('bulkReferrerComm').value,
                platformComm: document.getElementById('bulkPlatformComm').value
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            let updatedCount = 0;
            const { appType, sellType, sellerComm, referrerComm, platformComm } = result.value;
            
            document.querySelectorAll('#commissionTableBody tr').forEach(row => {
                const rowApp = row.querySelector('.app-type-select').value;
                const rowSellType = row.querySelector('.sell-type-select').value;
                
                const appMatch = !appType || rowApp === appType;
                const sellMatch = !sellType || rowSellType === sellType;
                
                if (appMatch && sellMatch) {
                    if (sellerComm) row.querySelector('.seller-commission').value = sellerComm;
                    if (referrerComm) row.querySelector('.referrer-commission').value = referrerComm;
                    if (platformComm) row.querySelector('.platform-commission').value = platformComm;
                    calculateTotalCommission(row.querySelector('.seller-commission'));
                    updatedCount++;
                }
            });
            
            updateSummary();
            Swal.fire('Updated!', `${updatedCount} row(s) updated successfully.`, 'success');
        }
    });
}

// Export functions
function exportCommissionData() {
    let csvContent = "Seller,App Type,Sell Type,Total Sales,Commission Type,Seller %,Referrer %,Platform %,Referral ID\n";
    document.querySelectorAll('#commissionTableBody tr').forEach(row => {
        const seller = row.querySelector('.seller-select option:checked')?.text || '';
        const app = row.querySelector('.app-type-select').value;
        const sellType = row.querySelector('.sell-type-select').value;
        const totalSales = row.querySelector('.total-sales').value;
        const commType = row.querySelector('.commission-type-select').value;
        const sellerComm = row.querySelector('.seller-commission').value;
        const referrerComm = row.querySelector('.referrer-commission').value;
        const platformComm = row.querySelector('.platform-commission').value;
        const referral = row.querySelector('.referral-select option:checked')?.text || '';
        
        csvContent += `"${seller}",${app},${sellType},${totalSales},${commType},${sellerComm},${referrerComm},${platformComm},"${referral}"\n`;
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'commission_data.csv';
    a.click();
    URL.revokeObjectURL(url);
    
    Swal.fire('Exported!', 'Commission data exported successfully', 'success');
}

function importCommissionData() {
    Swal.fire('Info', 'Import functionality will be available soon', 'info');
}

function resetAllCommissions() {
    Swal.fire({
        title: 'Reset Confirmation',
        text: 'Are you sure you want to reset all commissions to default values?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, reset',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelectorAll('#commissionTableBody tr').forEach(row => {
                row.querySelector('.seller-commission').value = 60;
                row.querySelector('.referrer-commission').value = 10;
                row.querySelector('.platform-commission').value = 30;
                calculateTotalCommission(row.querySelector('.seller-commission'));
            });
            updateSummary();
            Swal.fire('Reset Complete!', 'All commissions reset to default (60/10/30)', 'success');
        }
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSummary();
    
    document.querySelectorAll('#commissionTableBody tr').forEach(row => {
        const inputs = row.querySelectorAll('.seller-commission, .referrer-commission, .platform-commission');
        inputs.forEach(input => {
            input.addEventListener('change', () => {
                calculateTotalCommission(input);
                if (typeof saveAllCommissionsSilent === 'function') saveAllCommissionsSilent();
            });
        });
    });
    
    // Restore active tab from localStorage
    const activeTabId = localStorage.getItem('financeActiveTab');
    if (activeTabId) {
        const tabTrigger = document.querySelector(`button[data-bs-target="${activeTabId}"]`);
        if (tabTrigger) {
            new bootstrap.Tab(tabTrigger).show();
        }
    }

    // Listen for tab changes
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function(event) {
            localStorage.setItem('financeActiveTab', event.target.getAttribute('data-bs-target'));
        });
    });
});


// Function to refresh the display table with current form data
function refreshDisplayTable() {
    const tbody = document.getElementById('configuredRulesDisplayBody');
    if (!tbody) return;
    const formRows = document.querySelectorAll('#globalRulesTableBody tr');
    
    if (formRows.length === 0) {
        tbody.innerHTML = `<tr><td colspan="15" class="text-center py-4 text-muted">
            <iconify-icon icon="solar:database-bold" width="28" class="mb-2 d-block mx-auto"></iconify-icon>
            No rules configured yet.
        </td></tr>`;
        return;
    }
    
    let html = '';
    formRows.forEach((row, index) => {
        const sellerSelect = row.querySelector('.seller-select');
        const sellerText = sellerSelect ? sellerSelect.options[sellerSelect.selectedIndex]?.text || '-' : '-';
        
        const appSelect = row.querySelector('[name*="[app]"]');
        const appText = appSelect ? appSelect.options[appSelect.selectedIndex]?.text || '-' : '-';
        
        const sellTypeSelect = row.querySelector('[name*="[sell_type]"]');
        const sellTypeText = sellTypeSelect ? sellTypeSelect.options[sellTypeSelect.selectedIndex]?.text || '-' : '-';
        
        const categorySelect = row.querySelector('.category-select');
        const categoryText = categorySelect ? categorySelect.options[categorySelect.selectedIndex]?.text || '-' : '-';
        
        const productSelect = row.querySelector('.product-select');
        const productText = productSelect ? productSelect.options[productSelect.selectedIndex]?.text || '-' : '-';
        
        const amountInput = row.querySelector('.amount-input');
        const amountVal = amountInput ? amountInput.value || '0' : '0';
        
        const currencySelect = row.querySelector('.currency-select-field');
        const currencyText = currencySelect ? currencySelect.options[currencySelect.selectedIndex]?.text || 'INR' : 'INR';
        
        const inrLabel = row.querySelector('.converted-rupee-label');
        const inrValue = inrLabel ? inrLabel.textContent : '-';
        
        const commissionInput = row.querySelector('.commission-input');
        const commissionVal = commissionInput ? commissionInput.value || '0' : '0';
        
        const discountInput = row.querySelector('[name*="[discount]"]');
        const discountVal = discountInput ? discountInput.value || '-' : '-';
        
        const minMarginInput = row.querySelector('[name*="[min_margin]"]');
        const minMarginVal = minMarginInput ? minMarginInput.value || '-' : '-';
        
        const boostCheckbox = row.querySelector('.boost-checkbox');
        const boostVal = boostCheckbox && boostCheckbox.checked ? '✓ Enabled' : '✗ Disabled';
        
        const effectiveDateInput = row.querySelector('[name*="[effective_date]"]');
        const effectiveDateVal = effectiveDateInput ? effectiveDateInput.value || '-' : '-';
        
        html += `
            <tr data-display-index="${index}">
                <td class="text-center fw-bold">${index + 1}</td>
                <td>${escapeHtml(sellerText)}</td>
                <td><span class="badge bg-secondary-soft">${escapeHtml(appText)}</span></td>
                <td>${escapeHtml(sellTypeText)}</td>
                <td>${escapeHtml(categoryText)}</td>
                <td>${escapeHtml(productText)}</td>
                <td class="text-end fw-semibold">${amountVal}</td>
                <td class="text-center"><span class="badge bg-info-soft">${escapeHtml(currencyText)}</span></td>
                <td class="text-end fw-bold text-primary">${inrValue}</td>
                <td class="text-end"><span class="badge bg-success-soft">${commissionVal}%</span></td>
                <td>${escapeHtml(discountVal)}</td>
                <td class="text-end">${minMarginVal}%</td>
                <td class="text-center">${boostVal}</td>
                <td class="text-center">${escapeHtml(effectiveDateVal)}</td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteDisplayRule(${index})" title="Delete this rule">
                        <iconify-icon icon="solar:trash-bin-trash-bold" width="16"></iconify-icon>
                    </button>
                </td>
            </tr>
        `;
    });
    
    tbody.innerHTML = html;
}

// Helper function to escape HTML
function escapeHtml(str) {
    if (!str) return '-';
    return str.replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Delete a single rule from display table and form
function deleteDisplayRule(rowIndex) {
    if (confirm('Are you sure you want to delete this rule?')) {
        const formRows = document.querySelectorAll('#globalRulesTableBody tr');
        if (formRows[rowIndex]) {
            formRows[rowIndex].remove();
            refreshDisplayTable();
            showToast('Rule deleted successfully!', 'success');
        }
    }
}

// Delete all rules
function deleteAllDisplayRules() {
    if (confirm('⚠️ WARNING: This will delete ALL configured rules. Are you sure?')) {
        const tbody = document.getElementById('globalRulesTableBody');
        if (tbody) {
            tbody.innerHTML = '';
            refreshDisplayTable();
            showToast('All rules deleted successfully!', 'success');
        }
    }
}

// Override save function to refresh display table
const originalSaveGlobalRulesTable = saveGlobalRulesTable;
saveGlobalRulesTable = function(e) {
    e.preventDefault();
    const formData = new FormData(document.getElementById('globalRulesTableForm'));
    fetch('{{ route("admin.finance.update-global-rules") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: formData
    })
    .then(r => r.json())
    .then(d => {
        showToast(d.message, d.success ? 'success' : 'error');
        if (d.success) {
            refreshDisplayTable();
        }
    });
}

// Override add row function
const originalAddGlobalRuleRow = addGlobalRuleRow;
addGlobalRuleRow = function() {
    originalAddGlobalRuleRow();
    refreshDisplayTable();
};

// Override remove row function
const originalRemoveGlobalRuleRow = removeGlobalRuleRow;

// Remove rule row from BOTH tabs - FIXED
function removeGlobalRuleRow(button, index) {
    if (confirm('Remove this rule from both tabs?')) {
        // Remove from Tab 1
        const row1 = document.querySelector(`#globalRulesTab1Body tr[data-index="${index}"]`);
        // Remove from Tab 2
        const row2 = document.querySelector(`#globalRulesTab2Body tr[data-index="${index}"]`);
        
        if (row1) row1.remove();
        if (row2) row2.remove();
        
        showToast('Rule removed from both tabs', 'success');
    }
}

// Initialize display table on page load
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(refreshDisplayTable, 500);
});

// Toggle date input based on deduction type (always visible, just change hint)
function toggleDateRange() {
    const deductionType = document.getElementById('deductionType').value;
    const billingDateInput = document.getElementById('billingPeriodDate');
    const billingHint = document.getElementById('billingPeriodHint');
    const today = new Date();
    
    if (deductionType === 'realtime') {
        billingDateInput.disabled = true;
        billingDateInput.style.backgroundColor = '#e9ecef';
        billingHint.innerHTML = 'ⓘ Real-time deduction - billing date not applicable';
        // Set to current date
        billingDateInput.value = today.toISOString().split('T')[0];
    } else if (deductionType === 'monthly') {
        billingDateInput.disabled = false;
        billingDateInput.style.backgroundColor = '#fff';
        billingHint.innerHTML = '📅 Select monthly billing date (Invoice generated on this day each month)';
        // Set to next billing date (5th of next month or current)
        let billingDay = document.getElementById('invoiceGenerationDay').value || 5;
        let nextBillingDate = new Date(today.getFullYear(), today.getMonth(), billingDay);
        if (nextBillingDate < today) {
            nextBillingDate = new Date(today.getFullYear(), today.getMonth() + 1, billingDay);
        }
        billingDateInput.value = nextBillingDate.toISOString().split('T')[0];
    } else if (deductionType === 'weekly') {
        billingDateInput.disabled = false;
        billingDateInput.style.backgroundColor = '#fff';
        billingHint.innerHTML = '📅 Select weekly billing date (Invoice generated on this date each week)';
        // Set to next Friday (or current + 7 days)
        let nextWeek = new Date(today);
        nextWeek.setDate(today.getDate() + 7);
        billingDateInput.value = nextWeek.toISOString().split('T')[0];
    }
}

// When invoice generation day changes, update the billing date for monthly
document.getElementById('invoiceGenerationDay')?.addEventListener('change', function() {
    const deductionType = document.getElementById('deductionType').value;
    if (deductionType === 'monthly') {
        const today = new Date();
        const billingDay = this.value || 5;
        let nextBillingDate = new Date(today.getFullYear(), today.getMonth(), billingDay);
        if (nextBillingDate < today) {
            nextBillingDate = new Date(today.getFullYear(), today.getMonth() + 1, billingDay);
        }
        document.getElementById('billingPeriodDate').value = nextBillingDate.toISOString().split('T')[0];
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDateRange();
    
    // Set default billing period date
    const today = new Date();
    document.getElementById('billingPeriodDate').value = today.toISOString().split('T')[0];
});

// Global variables for pagination
let allSavedRulesData = [];
let currentPage = 1;
let perPage = 10;
let currentFilter = '';

// Load saved rules with pagination
function loadSavedRules() {
    fetch('{{ route("admin.finance.get-global-rules") }}', {
        method: 'GET',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
        if (data.success && data.rules) {
            allSavedRulesData = data.rules;
            applyFilterAndPagination();
        }
    })
    .catch(error => {
        console.error('Error loading saved rules:', error);
        showToast('Error loading saved rules', 'error');
    });
}

// Apply filter and pagination
function applyFilterAndPagination() {
    let filteredData = [...allSavedRulesData];
    
    // Apply search filter
    if (currentFilter) {
        filteredData = filteredData.filter(rule => 
            (rule.seller_name && rule.seller_name.toLowerCase().includes(currentFilter)) ||
            (rule.category_name && rule.category_name.toLowerCase().includes(currentFilter)) ||
            (rule.product_name && rule.product_name.toLowerCase().includes(currentFilter)) ||
            (rule.app && rule.app.toLowerCase().includes(currentFilter))
        );
    }
    
    // Calculate pagination
    const total = filteredData.length;
    const totalPages = Math.ceil(total / perPage);
    const start = (currentPage - 1) * perPage;
    const end = start + perPage;
    const paginatedData = filteredData.slice(start, end);
    
    // Update display
    displaySavedRules(paginatedData);
    updatePagination(total, totalPages, start, end);
}

// Display saved rules in table
function displaySavedRules(rules) {
    const tbody = document.getElementById('savedRulesBody');
    if (!tbody) return;
    
    if (rules.length === 0) {
        tbody.innerHTML = `<tr><td colspan="15" class="text-center py-4 text-muted">
            <iconify-icon icon="solar:inbox-bold" width="32" class="mb-2 d-block mx-auto"></iconify-icon>
            No saved rules found.
        </td></tr>`;
        return;
    }
    
    tbody.innerHTML = '';
    rules.forEach((rule, idx) => {
        const seller = sellersData.find(s => s.id == rule.seller_id);
        const category = categoriesData.find(c => c.id == rule.category_id);
        const product = productsData.find(p => p.id == rule.product_id);
        
        const globalIndex = allSavedRulesData.findIndex(r => r.id === rule.id) + 1;
        
        const row = `
            <tr data-rule-id="${rule.id}">
                <td class="small text-center">${globalIndex}</td>
                <td class="small">${escapeHtml(seller ? seller.name : 'N/A')}</td>
                <td class="small">${escapeHtml(rule.app || 'vshop')}</td>
                <td class="small">${escapeHtml(rule.sell_type || 'direct')}</td>
                <td class="small">${escapeHtml(category ? category.name : 'N/A')}</td>
                <td class="small">${escapeHtml(product ? product.name : 'N/A')}</td>
                <td class="small text-end">${rule.currency} ${parseFloat(rule.amount).toLocaleString()}</td>
                <td class="small text-end">${rule.converted_inr ? '₹ ' + parseFloat(rule.converted_inr).toLocaleString() : '-'}</td>
                <td class="small text-end">${rule.commission}%</td>
                <td class="small">${escapeHtml(rule.discount || '-')}</td>
                <td class="small text-end">${rule.min_margin || '-'}%</td>
                <td class="text-center">${rule.boost ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>'}</td>
                <td class="small">${escapeHtml(rule.effective_date || '-')}</td>
                <td class="small">${rule.created_at ? new Date(rule.created_at).toLocaleDateString() : '-'}</td>
                <td class="text-center">
                   
                   
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteSavedRule(${rule.id})" title="Delete">
                        <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
                    </button>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Update pagination UI
function updatePagination(total, totalPages, start, end) {
    // Update info text
    document.getElementById('savedRulesStart').textContent = total > 0 ? start + 1 : 0;
    document.getElementById('savedRulesEnd').textContent = Math.min(end, total);
    document.getElementById('savedRulesTotal').textContent = total;
    
    // Generate pagination HTML
    const paginationUl = document.getElementById('savedRulesPagination');
    if (!paginationUl) return;
    
    if (totalPages <= 1) {
        paginationUl.innerHTML = '';
        return;
    }
    
    let paginationHtml = '';
    
    // Previous button
    paginationHtml += `
        <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${currentPage - 1}); return false;">
                <iconify-icon icon="solar:arrow-left-bold" width="12"></iconify-icon> Prev
            </a>
        </li>
    `;
    
    // Page numbers
    let startPage = Math.max(1, currentPage - 2);
    let endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(1); return false;">1</a></li>`;
        if (startPage > 2) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        paginationHtml += `
            <li class="page-item ${currentPage === i ? 'active' : ''}">
                <a class="page-link" href="#" onclick="goToPage(${i}); return false;">${i}</a>
            </li>
        `;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            paginationHtml += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
        }
        paginationHtml += `<li class="page-item"><a class="page-link" href="#" onclick="goToPage(${totalPages}); return false;">${totalPages}</a></li>`;
    }
    
    // Next button
    paginationHtml += `
        <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="goToPage(${currentPage + 1}); return false;">
                Next <iconify-icon icon="solar:arrow-right-bold" width="12"></iconify-icon>
            </a>
        </li>
    `;
    
    paginationUl.innerHTML = paginationHtml;
}

// Go to specific page
function goToPage(page) {
    const total = allSavedRulesData.length;
    const totalPages = Math.ceil((currentFilter ? allSavedRulesData.filter(rule => 
        (rule.seller_name && rule.seller_name.toLowerCase().includes(currentFilter)) ||
        (rule.category_name && rule.category_name.toLowerCase().includes(currentFilter)) ||
        (rule.product_name && rule.product_name.toLowerCase().includes(currentFilter))
    ).length : total) / perPage);
    
    if (page < 1 || page > totalPages) return;
    
    currentPage = page;
    applyFilterAndPagination();
}

// Filter saved rules
function filterSavedRules() {
    const searchInput = document.getElementById('savedRulesSearch');
    currentFilter = searchInput ? searchInput.value.toLowerCase().trim() : '';
    currentPage = 1;
    applyFilterAndPagination();
}

// Change items per page
function changeSavedRulesPerPage() {
    const perPageSelect = document.getElementById('savedRulesPerPage');
    perPage = parseInt(perPageSelect.value);
    currentPage = 1;
    applyFilterAndPagination();
}

// Helper function to escape HTML
function escapeHtml(str) {
    if (!str) return '-';
    return String(str).replace(/[&<>]/g, function(m) {
        if (m === '&') return '&amp;';
        if (m === '<') return '&lt;';
        if (m === '>') return '&gt;';
        return m;
    });
}

// Override delete function to refresh pagination
const originalDeleteSavedRule = deleteSavedRule;
deleteSavedRule = function(ruleId) {
    if (confirm('Delete this rule permanently?')) {
        fetch('{{ route("admin.finance.delete-rule") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: ruleId })
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) {
                // Reload the entire data and reset pagination
                loadSavedRules();
            }
        });
    }
};

// Update DOMContentLoaded to use pagination
document.addEventListener('DOMContentLoaded', () => {
    // ... existing initialization code ...
    
    // Load saved rules with pagination
    loadSavedRules();
});


// Approve Refund
function approveRefund(orderId) {
    if (confirm('Approve this refund request?')) {
        fetch('{{ route("admin.finance.approve-refund") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1500);
        });
    }
}

// Reject Refund
function rejectRefund(orderId) {
    if (confirm('Reject this refund request?')) {
        fetch('{{ route("admin.finance.reject-refund") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ order_id: orderId })
        })
        .then(r => r.json())
        .then(data => {
            showToast(data.message, data.success ? 'success' : 'error');
            if (data.success) setTimeout(() => location.reload(), 1500);
        });
    }
}


</script>




<script>
// View Dispute with Message Modal
function viewDispute(id) {
    const row = document.querySelector(`#disputeTableNew button[onclick="viewDispute(${id})"]`).closest('tr');
    if(!row) return;
    
    const disputeId = row.cells[0]?.innerText || '#DSP-'+id;
    const disputedBy = row.cells[1]?.innerText || 'N/A';
    const subject = row.cells[2]?.querySelector('.fw-semibold')?.innerText || 'N/A';
    const amount = row.cells[5]?.innerText || '₹0';
    const message = row.getAttribute('data-message') || 'No additional message provided.';
    
    const modalHtml = `
        <div class="modal fade" id="msgModal${id}" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content" style="border-radius:1rem;">
                    <div class="modal-header" style="background:#3b82f6; border-bottom:none;">
                        <h5 class="modal-title text-white">💬 Dispute Message</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2"><strong>Dispute ID:</strong> ${disputeId}</div>
                        <div class="mb-2"><strong>Disputed By:</strong> ${disputedBy}</div>
                        <div class="mb-2"><strong>Subject:</strong> ${subject}</div>
                        <div class="mb-2"><strong>Amount:</strong> ${amount}</div>
                        <hr>
                        <div class="alert alert-light border" style="background:#f8fafc;">
                            <strong><i class="fas fa-comment"></i> Message:</strong><br>
                            ${message}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', modalHtml);
    const modal = new bootstrap.Modal(document.getElementById(`msgModal${id}`));
    modal.show();
    document.getElementById(`msgModal${id}`).addEventListener('hidden.bs.modal', () => document.getElementById(`msgModal${id}`)?.remove());
}

// Resolve Dispute (main table)
function resolveDispute(id) {
    if(confirm('Mark this dispute as resolved?')) {
        const row = document.querySelector(`#disputeTableNew button[onclick="resolveDispute(${id})"]`).closest('tr');
        if(row) {
            // Update status badge
            row.cells[7].innerHTML = '<span class="badge bg-success">Resolved</span>';
            
            // Update actions
            const actionsDiv = row.cells[8];
            actionsDiv.innerHTML = `<div class="d-flex gap-1 justify-content-center">
                <button class="btn btn-info btn-sm" onclick="viewDispute(${id})" style="border-radius:30px;">View</button>
                <button class="btn btn-secondary btn-sm" disabled style="border-radius:30px;">Resolved</button>
            </div>`;
            
            // Update status attribute
            row.setAttribute('data-status', 'resolved');
        }
        
        // Also update in escalation table if present
        const escRow = document.querySelector(`#escalationTableBody tr[data-dispute-id="${id}"]`);
        if(escRow) {
            const statusBadge = escRow.querySelector('.badge');
            statusBadge.innerHTML = 'Resolved';
            statusBadge.classList.remove('bg-warning');
            statusBadge.classList.add('bg-success');
            const resolveBtn = escRow.querySelector('.resolve-escalated');
            if(resolveBtn) {
                resolveBtn.disabled = true;
                resolveBtn.innerHTML = '✓ Resolved';
                resolveBtn.classList.remove('btn-success');
                resolveBtn.classList.add('btn-secondary');
            }
            const assignSelect = escRow.querySelector('.assign-next-team');
            const timeframeSelect = escRow.querySelector('.timeframe-select');
            if(assignSelect) assignSelect.disabled = true;
            if(timeframeSelect) timeframeSelect.disabled = true;
        }
        
        alert('✅ Dispute resolved successfully!');
    }
}

// Assign To button - moves row to Auto Escalation Rules table
function openAssignModal(id) {
    const mainRow = document.querySelector(`#disputeTableNew button[onclick="openAssignModal(${id})"]`).closest('tr');
    if(!mainRow) return;
    
    // Check if already in escalation table
    if(document.querySelector(`#escalationTableBody tr[data-dispute-id="${id}"]`)) {
        alert('⚠️ This dispute is already in Auto Escalation Rules table!');
        return;
    }
    
    const disputeIdText = mainRow.cells[0]?.innerText || '#DSP-'+String(id).padStart(3,'0');
    const disputedBy = mainRow.cells[1]?.innerHTML || 'Buyer';
    const disputeType = mainRow.cells[1]?.innerText?.includes('Buyer') ? 'buyer' : (mainRow.cells[1]?.innerText?.includes('Seller') ? 'seller' : 'platform');
    const previousAssigned = 'Not Assigned';
    
    // Create new row in escalation table
    const tbody = document.getElementById('escalationTableBody');
    
    // Remove "No disputes" message if exists
    const noDataRow = tbody.querySelector('td[colspan="6"]')?.closest('tr');
    if(noDataRow && tbody.children.length === 1) {
        noDataRow.remove();
    }
    
    const badgeClass = disputeType === 'buyer' ? 'bg-info' : (disputeType === 'seller' ? 'bg-warning' : 'bg-secondary');
    const badgeText = disputeType === 'buyer' ? 'Buyer' : (disputeType === 'seller' ? 'Seller' : 'Platform');
    
    const newRow = document.createElement('tr');
    newRow.setAttribute('data-dispute-id', id);
    newRow.setAttribute('data-previous-assigned', previousAssigned);
    newRow.innerHTML = `
        <td>
            <div class="fw-bold text-primary">${disputeIdText}</div>
            <small class="text-muted">Previous: ${previousAssigned}</small>
        </td>
        <td>
            <span class="badge ${badgeClass}">${badgeText}</span>
        </td>
        <td>
            <select class="form-select form-select-sm assign-next-team" data-id="${id}" style="border-radius:20px; border:1px solid #cbd5e1; font-size:0.8rem;">
                <option value="">Select Team</option>
                <option value="support team">Support Team</option>
                <option value="account team">Account Team</option>
                <option value="operation team">Operation Team</option>
                <option value="manager">Manager</option>
            </select>
        </td>
        <td>
            <select class="form-select form-select-sm timeframe-select" style="border-radius:20px; font-size:0.8rem;">
                <option value="Immediate">Immediate</option>
                <option value="24 Hours">24 Hours</option>
                <option value="48 Hours">48 Hours</option>
            </select>
        </td>
        <td>
            <span class="badge bg-warning">Open</span>
        </td>
        <td class="text-center">
            <button class="btn btn-success btn-sm resolve-escalated" data-id="${id}" style="border-radius:30px;">Resolve</button>
        </td>
    `;
    
    tbody.appendChild(newRow);
    
    // Add resolve event to new button
    newRow.querySelector('.resolve-escalated').addEventListener('click', function() {
        resolveFromEscalation(id, newRow);
    });
    
    // Disable assign button in main table
    const assignBtn = mainRow.querySelector(`button[onclick="openAssignModal(${id})"]`);
    if(assignBtn) {
        assignBtn.disabled = true;
        assignBtn.innerHTML = '⇾ Assigned';
        assignBtn.classList.remove('btn-primary');
        assignBtn.classList.add('btn-secondary');
    }
    
    alert(`✅ Dispute ${disputeIdText} moved to Auto Escalation Rules table. Assign next person and resolve.`);
}

// Resolve from escalation table
function resolveFromEscalation(id, rowElement) {
    const selectedTeam = rowElement.querySelector('.assign-next-team')?.value;
    const timeFrame = rowElement.querySelector('.timeframe-select')?.value;
    
    if(!selectedTeam) {
        alert('⚠️ Please assign to a team before resolving!');
        return;
    }
    
    if(confirm(`Resolve this dispute?\nAssigned to: ${selectedTeam}\nTime frame: ${timeFrame}`)) {
        // Update status badge
        const statusBadge = rowElement.querySelector('.badge');
        statusBadge.innerHTML = 'Resolved';
        statusBadge.classList.remove('bg-warning');
        statusBadge.classList.add('bg-success');
        
        // Disable inputs and button
        const resolveBtn = rowElement.querySelector('.resolve-escalated');
        resolveBtn.disabled = true;
        resolveBtn.innerHTML = '✓ Resolved';
        resolveBtn.classList.remove('btn-success');
        resolveBtn.classList.add('btn-secondary');
        
        const assignSelect = rowElement.querySelector('.assign-next-team');
        const timeframeSelect = rowElement.querySelector('.timeframe-select');
        assignSelect.disabled = true;
        timeframeSelect.disabled = true;
        
        // Also update main table if still open
        const mainRow = document.querySelector(`#disputeTableNew tr[data-dispute-id="${id}"]`);
        if(mainRow && mainRow.getAttribute('data-status') !== 'resolved') {
            mainRow.cells[7].innerHTML = '<span class="badge bg-success">Resolved</span>';
            const actionsDiv = mainRow.cells[8];
            actionsDiv.innerHTML = `<div class="d-flex gap-1 justify-content-center">
                <button class="btn btn-info btn-sm" onclick="viewDispute(${id})" style="border-radius:30px;">View</button>
                <button class="btn btn-secondary btn-sm" disabled style="border-radius:30px;">Resolved</button>
            </div>`;
            mainRow.setAttribute('data-status', 'resolved');
        }
        
        alert(`✅ Dispute resolved! Assigned to ${selectedTeam} | ${timeFrame}`);
    }
}

// Filter function for status
function filterDisputeTableNew() {
    const filter = document.getElementById('disputeStatusFilterNew').value;
    const byFilter = document.getElementById('disputeByFilterNew') ? document.getElementById('disputeByFilterNew').value.toLowerCase() : 'all';
    const rows = document.querySelectorAll('#disputeTableNew tbody tr');
    
    rows.forEach(row => {
        if(row.querySelector('td[colspan="9"]')) return;
        
        const byCell = row.cells[1];
        const statusCell = row.cells[7];
        
        const byText = byCell?.innerText?.trim().toLowerCase() || '';
        const statusText = statusCell?.innerText?.trim().toLowerCase() || '';
        
        let showStatus = false;
        if(filter === 'all') showStatus = true;
        else if(filter === 'open') showStatus = statusText === 'open' || statusText === 'in_progress';
        else if(filter === 'resolved') showStatus = statusText === 'resolved';
        else if(filter === 'escalated') showStatus = statusText === 'escalated';
        
        let showBy = false;
        if(byFilter === 'all') showBy = true;
        else showBy = byText.includes(byFilter);
        
        row.style.display = (showStatus && showBy) ? '' : 'none';
    });
}

// Add event listeners for existing escalation table resolve buttons
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('#escalationTableBody .resolve-escalated').forEach(btn => {
        const id = btn.getAttribute('data-id');
        const row = btn.closest('tr');
        if(id && row) {
            btn.addEventListener('click', function() {
                resolveFromEscalation(parseInt(id), row);
            });
        }
    });
});
</script>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// CSRF Token
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';

// Resolve Dispute Function with Sweet Alert
async function resolveDispute(disputeId, button) {
    // Sweet Alert Confirmation
    const result = await Swal.fire({
        title: 'Resolve Dispute?',
        text: 'Are you sure you want to mark this dispute as resolved?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#10b981',
        cancelButtonColor: '#ef4444',
        confirmButtonText: 'Yes, Resolve it!',
        cancelButtonText: 'Cancel',
        backdrop: true,
        allowOutsideClick: false
    });
    
    if (!result.isConfirmed) {
        return;
    }
    
    // Show loading
    Swal.fire({
        title: 'Processing...',
        text: 'Please wait while we resolve the dispute',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    try {
        const response = await fetch('{{ route("admin.finance.resolve-dispute") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({
                dispute_id: disputeId,
                resolution_note: 'Resolved from escalation table'
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            const row = button.closest('tr');
            
            // Update Status Badge to Resolved (Green)
            const statusBadge = row.querySelector('td:nth-child(5) .badge');
            statusBadge.innerHTML = 'Resolved';
            statusBadge.style.background = '#10b981';
            statusBadge.classList.remove('bg-primary');
            statusBadge.classList.add('bg-success');
            
            // Disable and update Assign To dropdown
            const assignSelect = row.querySelector('.assign-next-team');
            if(assignSelect) {
                const newSelect = document.createElement('select');
                newSelect.className = 'form-select form-select-sm';
                newSelect.style.cssText = 'border-radius:20px; border:1px solid #cbd5e1; font-size:0.8rem;';
                newSelect.disabled = true;
                newSelect.innerHTML = '<option value="">Resolved - No Action</option>';
                assignSelect.parentNode.replaceChild(newSelect, assignSelect);
            } else {
                // If already disabled, just keep it
                const existingSelect = row.querySelector('td:nth-child(3) select');
                if(existingSelect) existingSelect.disabled = true;
            }
            
            // Disable Timeframe dropdown
            const timeframeSelect = row.querySelector('.timeframe-select');
            if(timeframeSelect) {
                timeframeSelect.disabled = true;
                timeframeSelect.innerHTML = '<option value="">Resolved</option>';
            } else {
                const existingTimeframe = row.querySelector('td:nth-child(4) select');
                if(existingTimeframe) {
                    existingTimeframe.disabled = true;
                    existingTimeframe.innerHTML = '<option value="">Resolved</option>';
                }
            }
            
            // Update button to Resolved
            button.disabled = true;
            button.innerHTML = '✓ Resolved';
            button.style.background = '#94a3b8';
            button.style.borderColor = '#94a3b8';
            button.classList.remove('btn-success');
            button.classList.add('btn-secondary');
            
            // Update main table if exists
            const mainRow = document.querySelector(`#disputeTableNew tr[data-dispute-id="${disputeId}"]`);
            if(mainRow) {
                const mainStatusCell = mainRow.cells[7];
                mainStatusCell.innerHTML = '<span class="badge bg-success" style="background:#10b981 !important;">Resolved</span>';
                
                // Disable action buttons in main table
                const actionDiv = mainRow.cells[8];
                if(actionDiv) {
                    actionDiv.innerHTML = `<div class="d-flex gap-1 justify-content-center">
                        <button class="btn btn-info btn-sm" onclick="viewDispute(${disputeId})" style="border-radius:30px;">View</button>
                        <button class="btn btn-secondary btn-sm" disabled style="border-radius:30px;">Resolved</button>
                    </div>`;
                }
            }
            
            // Success Sweet Alert
            Swal.fire({
                title: 'Resolved!',
                text: 'Dispute has been resolved successfully.',
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'OK',
                timer: 2000,
                timerProgressBar: true
            });
            
        } else {
            // Error Sweet Alert
            Swal.fire({
                title: 'Error!',
                text: data.message || 'Failed to resolve dispute',
                icon: 'error',
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Try Again'
            });
        }
        
    } catch(error) {
        console.error('Error:', error);
        Swal.fire({
            title: 'Network Error!',
            text: 'Please check your connection and try again.',
            icon: 'error',
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'OK'
        });
    }
}

// Attach event listeners when DOM loads
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.resolve-escalated').forEach(btn => {
        // Remove old listeners to avoid duplicates
        const newBtn = btn.cloneNode(true);
        btn.parentNode.replaceChild(newBtn, btn);
        
        newBtn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            const disputeId = this.getAttribute('data-id');
            resolveDispute(disputeId, this);
        });
    });
});
</script>
<script>
// Dispute Management
function resolveDispute(id) {
    const resolution = prompt('Enter the dispute resolution agreement summary:');
    if (resolution) {
        showToast(`Dispute #${id} resolved successfully!`, 'success');
    }
}

function escalateDispute(id) {
    if (confirm('Escalate this dispute case directly to management review?')) {
        showToast(`Dispute #${id} escalated to management`, 'warning');
    }
}
</script>

<script>
// ==========================================
// SELLER SELECTION & DAILY CAPS MANAGEMENT
// ==========================================

// Global variables
let allSellers = [];
let selectedSellers = [];

// Load all sellers from database
function loadSellers() {
    const sellersContainer = document.getElementById('sellersCheckboxList');
    if (sellersContainer) {
        sellersContainer.innerHTML = `
            <div class="text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                <div class="small text-muted mt-1">Loading sellers...</div>
            </div>
        `;
    }
    
    console.log('Fetching sellers from API...');
    
    fetch('/admin/finance/get-all-sellers', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        credentials: 'same-origin'
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('API Response:', data);
        
        if (data.success && data.sellers && data.sellers.length > 0) {
            allSellers = data.sellers;
            console.log('Sellers loaded successfully:', allSellers.length);
            renderSellersCheckboxList();
        } else {
            console.warn('No sellers found or invalid response:', data);
            showNoSellersMessage();
        }
    })
    .catch(error => {
        console.error('Error loading sellers:', error);
        showNoSellersMessage(error.message);
    });
}

// Show no sellers message with retry button
function showNoSellersMessage(errorMsg = '') {
    const container = document.getElementById('sellersCheckboxList');
    if (container) {
        container.innerHTML = `
            <div class="text-center py-3 text-muted">
                <iconify-icon icon="mdi:account-off" width="32"></iconify-icon>
                <div class="small mt-1">No sellers found in database</div>
                ${errorMsg ? `<div class="small text-danger mt-1">Error: ${errorMsg}</div>` : ''}
                <button type="button" class="btn btn-sm btn-primary mt-2" onclick="loadSellers()">
                    <iconify-icon icon="mdi:refresh" width="14"></iconify-icon>
                    Retry
                </button>
            </div>
        `;
    }
    
    // Update the select all area
    const selectedCountSpan = document.getElementById('selectedSellersCount');
    if (selectedCountSpan) {
        selectedCountSpan.innerHTML = '📋 No sellers available';
    }
}

// Render sellers checkbox list
function renderSellersCheckboxList() {
    const container = document.getElementById('sellersCheckboxList');
    if (!container) return;
    
    if (!allSellers || allSellers.length === 0) {
        showNoSellersMessage();
        return;
    }
    
    let html = '';
    allSellers.forEach(seller => {
        const isChecked = selectedSellers.includes(seller.id);
        const sellerName = seller.name || seller.shop_name || 'Seller #' + seller.id;
        
        html += `
            <div class="form-check mb-2 p-2 border-bottom">
                <input class="form-check-input seller-checkbox" 
                       type="checkbox" 
                       value="${seller.id}" 
                       id="seller_${seller.id}" 
                       ${isChecked ? 'checked' : ''}
                       onchange="updateSelectedSellers()">
                <label class="form-check-label" for="seller_${seller.id}">
                    <div class="fw-bold">${escapeHtml(sellerName)}</div>
                    ${seller.email ? `<small class="text-muted d-block">📧 ${escapeHtml(seller.email)}</small>` : ''}
                    ${seller.phone ? `<small class="text-muted d-block">📞 ${escapeHtml(seller.phone)}</small>` : ''}
                    <small class="text-muted d-block">ID: ${seller.id}</small>
                </label>
            </div>
        `;
    });
    
    container.innerHTML = html;
    updateSelectedSellersCount();
}

// Update selected sellers array
function updateSelectedSellers() {
    selectedSellers = [];
    const checkboxes = document.querySelectorAll('.seller-checkbox:checked');
    checkboxes.forEach(checkbox => {
        selectedSellers.push(parseInt(checkbox.value));
    });
    updateSelectedSellersCount();
    
    // Update Select All checkbox
    const selectAllCheckbox = document.getElementById('selectAllSellers');
    if (selectAllCheckbox && allSellers.length > 0) {
        selectAllCheckbox.checked = selectedSellers.length === allSellers.length;
        selectAllCheckbox.indeterminate = selectedSellers.length > 0 && selectedSellers.length < allSellers.length;
    }
}

// Update the button text with selected sellers count
function updateSelectedSellersCount() {
    const countSpan = document.getElementById('selectedSellersCount');
    if (countSpan) {
        const count = selectedSellers.length;
        if (count === 0) {
            countSpan.innerHTML = '📋 Select Sellers';
        } else if (count === 1) {
            const selectedSeller = allSellers.find(s => s.id === selectedSellers[0]);
            const sellerName = selectedSeller ? (selectedSeller.name || selectedSeller.shop_name || 'Seller') : 'Seller';
            countSpan.innerHTML = `✓ ${escapeHtml(sellerName)}`;
        } else {
            countSpan.innerHTML = `✓ ${count} Sellers Selected`;
        }
    }
}

// Toggle all sellers
function toggleAllSellers() {
    const selectAllCheckbox = document.getElementById('selectAllSellers');
    if (!selectAllCheckbox) return;
    
    const isChecked = selectAllCheckbox.checked;
    
    const checkboxes = document.querySelectorAll('.seller-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    
    if (isChecked) {
        selectedSellers = allSellers.map(s => s.id);
    } else {
        selectedSellers = [];
    }
    
    updateSelectedSellersCount();
}

// Add seller cap
function addSellerCap() {
    const sellerDailyCap = document.getElementById('sellerDailyCap');
    
    if (selectedSellers.length === 0) {
        showToast('Please select at least one seller first', 'warning');
        return;
    }
    
    const dailyCap = parseFloat(sellerDailyCap.value);
    
    if (isNaN(dailyCap) || dailyCap <= 0) {
        showToast('Please enter a valid daily cap amount', 'warning');
        return;
    }
    
    // Get the button that was clicked
    const addButton = event?.target;
    if (addButton) {
        addButton.disabled = true;
        addButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';
    }
    
    fetch('/admin/finance/add-seller-cap', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            seller_ids: selectedSellers,
            daily_cap: dailyCap
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(`Cap added successfully for ${selectedSellers.length} seller(s)`, 'success');
            sellerDailyCap.value = '';
            // Clear selection
            selectedSellers = [];
            updateSelectedSellersCount();
            // Uncheck all checkboxes
            const checkboxes = document.querySelectorAll('.seller-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = false);
            // Reload caps
            loadSellerCaps();
        } else {
            showToast(data.message || 'Error adding seller cap', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Server error, please try again', 'error');
    })
    .finally(() => {
        if (addButton) {
            addButton.disabled = false;
            addButton.innerHTML = 'Add Cap';
        }
    });
}

// Remove seller cap
function removeSellerCap(sellerId) {
    if (!confirm('Are you sure you want to remove this seller\'s daily cap?')) {
        return;
    }
    
    fetch('/admin/finance/remove-seller-cap', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            seller_id: sellerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Seller cap removed successfully', 'success');
            loadSellerCaps();
        } else {
            showToast(data.message || 'Error removing seller cap', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Server error, please try again', 'error');
    });
}

// Load seller caps
function loadSellerCaps() {
    fetch('/admin/finance/get-seller-caps', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.caps) {
            displaySellerCaps(data.caps);
        } else {
            displaySellerCaps([]);
        }
    })
    .catch(error => {
        console.error('Error loading seller caps:', error);
        displaySellerCaps([]);
    });
}

// Display seller caps list
function displaySellerCaps(caps) {
    const container = document.getElementById('sellerCapsList');
    if (!container) return;
    
    if (!caps || caps.length === 0) {
        container.innerHTML = '<div class="text-muted text-center py-2 border rounded bg-light">No seller-specific caps configured</div>';
        return;
    }
    
    let html = '<div class="border rounded overflow-hidden">';
    caps.forEach(cap => {
        const sellerName = cap.seller_name || `Seller #${cap.seller_id}`;
        html += `
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom bg-white">
                <div class="flex-grow-1">
                    <div class="fw-bold small">${escapeHtml(sellerName)}</div>
                    <div class="text-muted small">Daily Cap: ₹${formatNumber(cap.daily_cap)}</div>
                </div>
                <button type="button" class="btn btn-sm btn-link text-danger p-0" onclick="removeSellerCap(${cap.seller_id})" title="Remove cap">
                    <iconify-icon icon="mdi:trash-can-outline" width="18"></iconify-icon>
                </button>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

// Helper function to escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Helper function to format numbers
function formatNumber(num) {
    if (!num) return '0';
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

// Toast notification function
function showToast(message, type = 'info') {
    // Create toast container if not exists
    let container = document.getElementById('toastContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'toastContainer';
        container.style.cssText = 'position: fixed; bottom: 20px; right: 20px; z-index: 9999;';
        document.body.appendChild(container);
    }
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : type === 'error' ? 'danger' : type === 'warning' ? 'warning' : 'info'} border-0 show`;
    toast.setAttribute('role', 'alert');
    toast.style.marginBottom = '10px';
    toast.style.minWidth = '250px';
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                ${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    container.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
    
    // Remove on click
    toast.querySelector('.btn-close').addEventListener('click', () => {
        toast.remove();
    });
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing seller dropdown...');
    loadSellers();
    loadSellerCaps();
});

// ==========================================
// FRAUD RULES FUNCTIONS
// ==========================================

// Show fraud rules details modal
function showFraudRulesDetails() {
    const modalElement = document.getElementById('fraudRulesModal');
    if (modalElement && typeof bootstrap !== 'undefined') {
        const modal = new bootstrap.Modal(modalElement);
        modal.show();
    } else {
        alert('Modal not found or Bootstrap not loaded');
    }
}

// Refresh fraud rules
function refreshFraudRules() {
    showToast('Fraud rules refreshed', 'success');
    // You can add API call here to refresh rules
}
</script>
<script>
// Global variables
let allSellersData = [];
let selectedSellerIds = [];

// Load sellers on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSellersList();
    loadExistingCaps();
});

// Load sellers list from database
function loadSellersList() {
    const container = document.getElementById('sellersListContainer');
    if (!container) return;
    
    container.innerHTML = `
        <div class="p-3 text-center text-muted">
            <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
            <div class="small mt-1">Loading sellers...</div>
        </div>
    `;
    
    fetch('/admin/finance/get-all-sellers', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.sellers && data.sellers.length > 0) {
            allSellersData = data.sellers;
            renderSellersList(allSellersData);
        } else {
            container.innerHTML = `
                <div class="p-3 text-center text-muted">
                    <iconify-icon icon="mdi:account-off" width="32"></iconify-icon>
                    <div class="small mt-1">No sellers found</div>
                    <button class="btn btn-sm btn-link mt-2" onclick="loadSellersList()">Retry</button>
                </div>
            `;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        container.innerHTML = `
            <div class="p-3 text-center text-danger">
                <iconify-icon icon="mdi:alert-circle" width="32"></iconify-icon>
                <div class="small mt-1">Error loading sellers</div>
                <button class="btn btn-sm btn-link mt-2" onclick="loadSellersList()">Retry</button>
            </div>
        `;
    });
}

// Render sellers list with checkboxes
function renderSellersList(sellers) {
    const container = document.getElementById('sellersListContainer');
    if (!container) return;
    
    if (!sellers || sellers.length === 0) {
        container.innerHTML = '<div class="p-3 text-center text-muted">No sellers available</div>';
        return;
    }
    
    let html = '';
    sellers.forEach(seller => {
        const isChecked = selectedSellerIds.includes(seller.id);
        const sellerName = seller.name || seller.shop_name || 'Seller #' + seller.id;
        const currentCap = seller.daily_cap || 0;
        const capText = currentCap > 0 ? ` (₹${formatNumber(currentCap)})` : '';
        
        html += `
            <div class="form-check p-2 border-bottom m-0" style="margin: 0 !important;">
                <input class="form-check-input seller-checkbox" 
                       type="checkbox" 
                       value="${seller.id}" 
                       id="seller_${seller.id}" 
                       ${isChecked ? 'checked' : ''}
                       onchange="updateSelectedSellers()">
                <label class="form-check-label w-100" for="seller_${seller.id}" style="cursor: pointer;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>${escapeHtml(sellerName)}</strong>
                            <div class="small text-muted">ID: ${seller.id}${capText}</div>
                        </div>
                        ${seller.email ? `<small class="text-muted">${escapeHtml(seller.email)}</small>` : ''}
                    </div>
                </label>
            </div>
        `;
    });
    
    container.innerHTML = html;
    updateSelectedCountDisplay();
}

// Filter sellers by search input
function filterSellers() {
    const searchTerm = document.getElementById('sellerSearchInput').value.toLowerCase();
    
    if (!searchTerm) {
        renderSellersList(allSellersData);
        return;
    }
    
    const filtered = allSellersData.filter(seller => {
        const name = (seller.name || seller.shop_name || '').toLowerCase();
        const email = (seller.email || '').toLowerCase();
        return name.includes(searchTerm) || email.includes(searchTerm);
    });
    
    renderSellersList(filtered);
}

// Update selected sellers array
function updateSelectedSellers() {
    selectedSellerIds = [];
    const checkboxes = document.querySelectorAll('.seller-checkbox:checked');
    checkboxes.forEach(checkbox => {
        selectedSellerIds.push(parseInt(checkbox.value));
    });
    updateSelectedCountDisplay();
}

// Select all sellers (visible in current filter)
function selectAllSellers() {
    const checkboxes = document.querySelectorAll('.seller-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        const id = parseInt(checkbox.value);
        if (!selectedSellerIds.includes(id)) {
            selectedSellerIds.push(id);
        }
    });
    updateSelectedCountDisplay();
}

// Deselect all sellers
function deselectAllSellers() {
    const checkboxes = document.querySelectorAll('.seller-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
    });
    selectedSellerIds = [];
    updateSelectedCountDisplay();
}

// Update selected sellers count display
function updateSelectedCountDisplay() {
    const display = document.getElementById('selectedSellersCountDisplay');
    const count = selectedSellerIds.length;
    
    if (count === 0) {
        display.innerHTML = '📋 No sellers selected';
        display.className = 'small text-muted mb-2';
    } else if (count === 1) {
        const seller = allSellersData.find(s => s.id === selectedSellerIds[0]);
        const name = seller ? (seller.name || seller.shop_name) : 'Seller';
        display.innerHTML = `✓ Selected: ${escapeHtml(name)}`;
        display.className = 'small text-success fw-bold mb-2';
    } else {
        display.innerHTML = `✓ ${count} sellers selected`;
        display.className = 'small text-success fw-bold mb-2';
    }
}

// Add cap to selected sellers
function addBulkSellerCap() {
    const dailyCapInput = document.getElementById('sellerDailyCap');
    const dailyCap = parseFloat(dailyCapInput.value);
    
    if (selectedSellerIds.length === 0) {
        alert('⚠️ Please select at least one seller');
        return;
    }
    
    if (isNaN(dailyCap) || dailyCap <= 0) {
        alert('⚠️ Please enter a valid daily cap amount');
        return;
    }
    
    // Get selected seller names for confirmation
    const selectedNames = selectedSellerIds.map(id => {
        const seller = allSellersData.find(s => s.id === id);
        return seller ? (seller.name || seller.shop_name) : 'Seller #' + id;
    }).join(', ');
    
    if (!confirm(`Add daily cap of ₹${formatNumber(dailyCap)} to ${selectedSellerIds.length} seller(s)?\n\nSellers: ${selectedNames.substring(0, 100)}${selectedNames.length > 100 ? '...' : ''}`)) {
        return;
    }
    
    const addButton = event?.target;
    if (addButton) {
        addButton.disabled = true;
        addButton.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Adding...';
    }
    
    fetch('/admin/finance/add-seller-cap', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            seller_ids: selectedSellerIds,
            daily_cap: dailyCap
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`✅ Success! Daily cap of ₹${formatNumber(dailyCap)} added to ${selectedSellerIds.length} seller(s)`);
            dailyCapInput.value = '';
            deselectAllSellers();
            loadSellersList();
            loadExistingCaps();
        } else {
            alert('❌ Error: ' + (data.message || 'Failed to add caps'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Server error, please try again');
    })
    .finally(() => {
        if (addButton) {
            addButton.disabled = false;
            addButton.innerHTML = 'Add Cap to Selected';
        }
    });
}

// Remove individual seller cap
function removeSellerCap(sellerId, sellerName) {
    if (!confirm(`Remove daily cap for ${sellerName}?`)) {
        return;
    }
    
    fetch('/admin/finance/remove-seller-cap', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            seller_id: sellerId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(`✅ Cap removed for ${sellerName}`);
            loadSellersList();
            loadExistingCaps();
        } else {
            alert('❌ Error: ' + (data.message || 'Failed to remove cap'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('❌ Server error, please try again');
    });
}

// Load existing caps
function loadExistingCaps() {
    fetch('/admin/finance/get-seller-caps', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.caps) {
            displayExistingCaps(data.caps);
        } else {
            displayExistingCaps([]);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        displayExistingCaps([]);
    });
}

// Display existing caps
function displayExistingCaps(caps) {
    const container = document.getElementById('sellerCapsList');
    if (!container) return;
    
    
    
    let html = '<div class="border rounded overflow-hidden">';
    caps.forEach(cap => {
        const sellerName = cap.seller_name || `Seller #${cap.seller_id}`;
        html += `
            <div class="d-flex justify-content-between align-items-center p-2 border-bottom bg-white">
                <div>
                    <div class="fw-bold small">${escapeHtml(sellerName)}</div>
                    <div class="text-muted small">Daily Cap: ₹${formatNumber(cap.daily_cap)}</div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeSellerCap(${cap.seller_id}, '${escapeHtml(sellerName)}')">
                    Remove
                </button>
            </div>
        `;
    });
    html += '</div>';
    container.innerHTML = html;
}

// Helper functions
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function formatNumber(num) {
    if (!num) return '0';
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}
</script>




<script>
// ==========================================
// TAX & INVOICE FUNCTIONS
// ==========================================

// Add new tax region row
function addTaxRegionRow() {
    const tbody = document.getElementById('regionTaxTableBody');
    if (!tbody) return;
    
    const rowCount = tbody.children.length;
    
    const newRow = document.createElement('tr');
    newRow.className = 'tax-region-row';
    newRow.innerHTML = `
        <td>
            <select class="form-select form-select-sm" name="tax_regions[${rowCount}][region]" required><option value="">Select Country</option><option value="India">India</option><option value="USA">USA</option><option value="UK">UK</option><option value="Australia">Australia</option><option value="Canada">Canada</option><option value="Germany">Germany</option><option value="France">France</option><option value="Japan">Japan</option><option value="China">China</option><option value="Brazil">Brazil</option><option value="UAE">UAE</option><option value="Singapore">Singapore</option><option value="Others">Others</option></select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="tax_regions[${rowCount}][tax_name]" placeholder="Enter tax name" required>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="0.1" class="form-control" name="tax_regions[${rowCount}][rate]" placeholder="0" required>
                <span class="input-group-text">%</span>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTaxRegionRow(this)">
                <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
}

// Remove tax region row
function removeTaxRegionRow(button) {
    const row = button.closest('tr');
    if (row) {
        row.remove();
        // Re-index the remaining rows
        reindexTaxRegionRows();
    }
}

// Re-index tax region rows after deletion
function reindexTaxRegionRows() {
    const tbody = document.getElementById('regionTaxTableBody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('tr');
    rows.forEach((row, index) => {
        const regionInput = row.querySelector('input[name*="[region]"]');
        const taxNameInput = row.querySelector('input[name*="[tax_name]"]');
        const rateInput = row.querySelector('input[name*="[rate]"]');
        
        if (regionInput) {
            regionInput.name = `tax_regions[${index}][region]`;
        }
        if (taxNameInput) {
            taxNameInput.name = `tax_regions[${index}][tax_name]`;
        }
        if (rateInput) {
            rateInput.name = `tax_regions[${index}][rate]`;
        }
    });
}

const currencySymbols = {
    USD: '$',
    INR: '₹',
    EUR: '€',
    GBP: '£',
    AUD: 'A$',
    CAD: 'C$',
    JPY: '¥',
    AED: 'د.إ',
    SGD: 'S$'
};

function autoFetchSymbol(select) {
    const row = select.closest('tr');
    if (!row) return;
    const symbolInput = row.querySelector('input[name*="[symbol]"]');
    const currency = select.value;
    if (symbolInput && currencySymbols[currency]) {
        symbolInput.value = currencySymbols[currency];
    }
}

function autoFetchSymbol2(select) {
    const row = select.closest('tr');
    if (!row) return;
    const symbolInput = row.querySelector('.currency-symbol-field2');
    const currency = select.value;
    if (symbolInput && currencySymbols[currency]) {
        symbolInput.value = currencySymbols[currency];
    }
}

// Add new currency row
function addCurrencyRow() {
    const tbody = document.getElementById('currencyTableBody');
    if (!tbody) return;
    
    const rowCount = tbody.children.length;
    
    const newRow = document.createElement('tr');
    newRow.className = 'currency-row';
    newRow.innerHTML = `
        <td>
            <select class="form-select form-select-sm" name="currencies[${rowCount}][currency]" onchange="autoFetchSymbol(this)" required>
                <option value="">Select Currency</option>
                <option value="USD">USD (US Dollar)</option>
                <option value="INR">INR (Indian Rupee)</option>
                <option value="EUR">EUR (Euro)</option>
                <option value="GBP">GBP (British Pound)</option>
                <option value="AUD">AUD (Australian Dollar)</option>
                <option value="CAD">CAD (Canadian Dollar)</option>
                <option value="JPY">JPY (Japanese Yen)</option>
                <option value="AED">AED (UAE Dirham)</option>
                <option value="SGD">SGD (Singapore Dollar)</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm" name="currencies[${rowCount}][symbol]" placeholder="$" required>
        </td>
        <td>
            <input type="number" step="0.0001" class="form-control form-control-sm" name="currencies[${rowCount}][rate]" placeholder="1.00" required>
        </td>
        <td>
            <select class="form-select form-select-sm" name="currencies[${rowCount}][auto_update]">
                <option value="Fixed">Fixed</option>
                <option value="Daily">Daily API</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCurrencyRow(this)">
                <iconify-icon icon="solar:trash-bin-trash-bold" width="14"></iconify-icon>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
}

function addCurrencyRow2() {
    const tbody = document.getElementById('currencyTableBody2');
    if (!tbody) return;
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <select class="form-select form-select-sm currency-code-field2" onchange="autoFetchSymbol2(this)" required>
                <option value="">Select Currency</option>
                <option value="USD">USD (US Dollar)</option>
                <option value="INR">INR (Indian Rupee)</option>
                <option value="EUR">EUR (Euro)</option>
                <option value="GBP">GBP (British Pound)</option>
                <option value="AUD">AUD (Australian Dollar)</option>
                <option value="CAD">CAD (Canadian Dollar)</option>
                <option value="JPY">JPY (Japanese Yen)</option>
                <option value="AED">AED (UAE Dirham)</option>
                <option value="SGD">SGD (Singapore Dollar)</option>
            </select>
        </td>
        <td><input type="text" class="form-control form-control-sm currency-symbol-field2" placeholder="Symbol" required></td>
        <td><input type="number" step="0.0001" class="form-control form-control-sm currency-rate-field2" placeholder="1.0000" required></td>
        <td>
            <select class="form-select form-select-sm currency-update-field2">
                <option value="Fixed">Fixed</option>
                <option value="Daily API 🔄">Daily API 🔄</option>
            </select>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeCurrency2(this)">Delete</button>
        </td>
    `;
    tbody.appendChild(newRow);
}

function removeCurrency2(btn) {
    btn.closest('tr').remove();
}

// Remove currency row
function removeCurrencyRow(button) {
    const row = button.closest('tr');
    if (row) {
        row.remove();
        // Re-index the remaining rows
        reindexCurrencyRows();
    }
}

// Re-index currency rows after deletion
function reindexCurrencyRows() {
    const tbody = document.getElementById('currencyTableBody');
    if (!tbody) return;
    
    const rows = tbody.querySelectorAll('tr');
    rows.forEach((row, index) => {
        const currencyInput = row.querySelector('input[name*="[currency]"]');
        const symbolInput = row.querySelector('input[name*="[symbol]"]');
        const rateInput = row.querySelector('input[name*="[rate]"]');
        const autoUpdateSelect = row.querySelector('select[name*="[auto_update]"]');
        
        if (currencyInput) {
            currencyInput.name = `currencies[${index}][currency]`;
        }
        if (symbolInput) {
            symbolInput.name = `currencies[${index}][symbol]`;
        }
        if (rateInput) {
            rateInput.name = `currencies[${index}][rate]`;
        }
        if (autoUpdateSelect) {
            autoUpdateSelect.name = `currencies[${index}][auto_update]`;
        }
    });
}

// Save all tax settings
function saveTaxInvoiceMain(suffix = '2') {
    const isSuffix2 = suffix === '2';
    const activeSuffix = isSuffix2 ? '2' : '';
    
    // Collect tax regions data
    const taxRegions = [];
    const taxTableBodyId = isSuffix2 ? 'regionTaxTableBody2' : 'regionTaxTableBody';
    const taxRows = document.querySelectorAll(`#${taxTableBodyId} tr`);
    taxRows.forEach(row => {
        const region = row.querySelector('[name*="[region]"]')?.value || row.querySelector('.region-name-field2')?.value;
        const taxName = row.querySelector('[name*="[tax_name]"]')?.value || row.querySelector('.tax-name-field2')?.value;
        const rate = row.querySelector('[name*="[rate]"]')?.value || row.querySelector('.tax-rate-field2')?.value;
        
        if (region && taxName && rate) {
            taxRegions.push({
                region: region,
                tax_name: taxName,
                rate: parseFloat(rate)
            });
        }
    });
    
    // Collect currencies data
    const currenciesList = [];
    const currencyTableBodyId = isSuffix2 ? 'currencyTableBody2' : 'currencyTableBody';
    const currencyRows = document.querySelectorAll(`#${currencyTableBodyId} tr`);
    currencyRows.forEach(row => {
        const currency = row.querySelector('.currency-code-field2')?.value || row.querySelector('select[name*="[currency]"]')?.value || row.querySelector('input[name*="[currency]"]')?.value;
        const symbol = row.querySelector('.currency-symbol-field2')?.value || row.querySelector('input[name*="[symbol]"]')?.value;
        const rate = row.querySelector('.currency-rate-field2')?.value || row.querySelector('input[name*="[rate]"]')?.value;
        const autoUpdate = row.querySelector('.currency-update-field2')?.value || row.querySelector('select[name*="[auto_update]"]')?.value;
        
        if (currency && symbol && rate) {
            currenciesList.push({
                currency: currency,
                symbol: symbol,
                rate: parseFloat(rate),
                auto_update: autoUpdate || 'Fixed'
            });
        }
    });
    
    // Collect invoice template settings
    const invoiceTemplate = {
        style: document.getElementById('templateStyleField' + activeSuffix)?.value || 'Professional',
        companyName: document.getElementById('companyNameField' + activeSuffix)?.value || '',
        companyGst: document.getElementById('companyGstField' + activeSuffix)?.value || '',
        companyEmail: document.getElementById('companyEmailField' + activeSuffix)?.value || '',
        companyPhone: document.getElementById('companyPhoneField' + activeSuffix)?.value || '',
        companyAddress: document.getElementById('companyAddressField' + activeSuffix)?.value || '',
        invoicePrefix: document.getElementById('invoicePrefixField' + activeSuffix)?.value || 'INV',
        invoiceFormat: document.getElementById('invoiceFormatField' + activeSuffix)?.value || '{prefix}{year}{month}{seq}',
        invoiceFooter: document.getElementById('invoiceFooterField' + activeSuffix)?.value || ''
    };
    
    // Get GST rate
    const gstRate = document.getElementById('defaultTaxRateField' + activeSuffix)?.value;
    
    // Get base currency
    const baseCurrency = document.getElementById('baseCurrencyField' + activeSuffix)?.value;
    
    // Validate at least one tax region exists
    if (taxRegions.length === 0) {
        showToast('Please add at least one tax region', 'error');
        return;
    }
    
    // Disable save button to prevent double submission
    const saveBtnSelector = isSuffix2 ? '#tax-invoice-tab .btn-primary' : '#sub-tax button[name="save_all_settings"]';
    const saveBtn = document.querySelector(saveBtnSelector);
    const originalText = saveBtn ? saveBtn.innerHTML : 'Save';
    if (saveBtn) {
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Saving...';
    }
    
    // Prepare form data
    const formData = {
        gst_rate: parseFloat(gstRate) || 18,
        base_currency: baseCurrency || 'INR',
        tax_regions: taxRegions,
        currencies: currenciesList,
        currencies_list: currenciesList,
        invoice_template: invoiceTemplate
    };
    
    // Send to server
    fetch('{{ route("admin.finance.update-tax-rules") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message || 'Tax and invoice settings saved successfully!', 'success');
            // Reload page after 1.5 seconds to reflect changes
            setTimeout(() => location.reload(), 1500);
        } else {
            showToast(data.message || 'Error saving settings', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Server error, please try again', 'error');
    })
    .finally(() => {
        if (saveBtn) {
            saveBtn.disabled = false;
            saveBtn.innerHTML = originalText;
        }
    });
}

// Alternative save function for the second tab (if needed)
function saveAllTaxSettingsNew() {
    saveTaxInvoiceMain('2');
}

// Add new tax region row for section 2
function addTaxRegionRow2() {
    const tbody = document.getElementById('regionTaxTableBody2');
    if (!tbody) return;
    
    const newRow = document.createElement('tr');
    newRow.innerHTML = `
        <td>
            <select class="form-select form-select-sm region-name-field2" required>
                <option value="">Select Country</option>
                <option value="India">India</option>
                <option value="USA">USA</option>
                <option value="UK">UK</option>
                <option value="Australia">Australia</option>
                <option value="Canada">Canada</option>
                <option value="Germany">Germany</option>
                <option value="France">France</option>
                <option value="Japan">Japan</option>
                <option value="China">China</option>
                <option value="Brazil">Brazil</option>
                <option value="UAE">UAE</option>
                <option value="Singapore">Singapore</option>
                <option value="Others">Others</option>
            </select>
        </td>
        <td>
            <input type="text" class="form-control form-control-sm tax-name-field2" placeholder="e.g. GST" required>
        </td>
        <td>
            <div class="input-group input-group-sm">
                <input type="number" step="0.1" class="form-control tax-rate-field2" placeholder="0" required>
                <span class="input-group-text">%</span>
            </div>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeTaxRegion2(this)">Delete</button>
        </td>
    `;
    tbody.appendChild(newRow);
}

// Remove tax region row for section 2
function removeTaxRegion2(button) {
    const row = button.closest('tr');
    if (row) {
        row.remove();
    }
}

// Generate invoice preview
function generateInvoiceNew(section = 2) {
    const isSection1 = section === 1;
    const sellerId = document.getElementById(isSection1 ? 'invoiceSelectSeller' : 'invoiceSelectSellerIdMain')?.value;
    const startDate = document.getElementById(isSection1 ? 'invoiceStartDate' : 'invoiceStartDateMain')?.value;
    const endDate = document.getElementById(isSection1 ? 'invoiceEndDate' : 'invoiceEndDateMain')?.value;
    
    if (!sellerId) {
        showToast('Please select a seller', 'error');
        return;
    }
    
    // Show loading
    const previewBtn = event?.target;
    if (previewBtn) {
        previewBtn.disabled = true;
        previewBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Loading...';
    }
    
    fetch('{{ route("admin.finance.generate-invoice") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            seller_id: sellerId,
            start_date: startDate,
            end_date: endDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.html) {
            // Show invoice in modal
            const modal = document.getElementById('invoiceModal');
            const modalBody = document.getElementById('invoicePreview');
            if (modalBody) {
                modalBody.innerHTML = data.html;
            }
            if (modal) {
                new bootstrap.Modal(modal).show();
            }
        } else {
            showToast(data.message || 'Error generating invoice', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Server error, please try again', 'error');
    })
    .finally(() => {
        if (previewBtn) {
            previewBtn.disabled = false;
            previewBtn.innerHTML = 'Generate Preview';
        }
    });
}

// Download invoice
function downloadInvoiceNew(section = 2) {
    const isSection1 = section === 1;
    const sellerId = document.getElementById(isSection1 ? 'invoiceSelectSeller' : 'invoiceSelectSellerIdMain')?.value;
    const startDate = document.getElementById(isSection1 ? 'invoiceStartDate' : 'invoiceStartDateMain')?.value;
    const endDate = document.getElementById(isSection1 ? 'invoiceEndDate' : 'invoiceEndDateMain')?.value;
    
    if (!sellerId) {
        showToast('Please select a seller', 'error');
        return;
    }
    
    // Create form and submit for download
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.finance.download-invoice") }}';
    form.target = '_blank';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const sellerInput = document.createElement('input');
    sellerInput.type = 'hidden';
    sellerInput.name = 'seller_id';
    sellerInput.value = sellerId;
    form.appendChild(sellerInput);
    
    if (startDate) {
        const startInput = document.createElement('input');
        startInput.type = 'hidden';
        startInput.name = 'start_date';
        startInput.value = startDate;
        form.appendChild(startInput);
    }
    
    if (endDate) {
        const endInput = document.createElement('input');
        endInput.type = 'hidden';
        endInput.name = 'end_date';
        endInput.value = endDate;
        form.appendChild(endInput);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Send invoice email
function sendInvoiceEmailNew(section = 2) {
    const isSection1 = section === 1;
    const sellerId = document.getElementById(isSection1 ? 'invoiceSelectSeller' : 'invoiceSelectSellerIdMain')?.value;
    const startDate = document.getElementById(isSection1 ? 'invoiceStartDate' : 'invoiceStartDateMain')?.value;
    const endDate = document.getElementById(isSection1 ? 'invoiceEndDate' : 'invoiceEndDateMain')?.value;
    
    if (!sellerId) {
        showToast('Please select a seller', 'error');
        return;
    }
    
    const sendBtn = document.getElementById('sendInvoiceEmailBtn');
    const originalText = sendBtn ? sendBtn.innerHTML : 'Send Invoice Email';
    if (sendBtn) {
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Sending...';
    }
    
    fetch('{{ route("admin.finance.send-invoice") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            seller_id: sellerId,
            start_date: startDate,
            end_date: endDate
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message || 'Error sending invoice email', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Server error, please try again', 'error');
    })
    .finally(() => {
        if (sendBtn) {
            sendBtn.disabled = false;
            sendBtn.innerHTML = originalText;
        }
    });
}

// Export report
function exportReportNew(section = 2) {
    const isSection1 = section === 1;
    const reportType = document.getElementById(isSection1 ? 'reportType' : 'reportTypeMain')?.value;
    const sellerId = document.getElementById(isSection1 ? 'invoiceSelectSeller' : 'invoiceSelectSellerIdMain')?.value;
    const startDate = document.getElementById(isSection1 ? 'invoiceStartDate' : 'invoiceStartDateMain')?.value;
    const endDate = document.getElementById(isSection1 ? 'invoiceEndDate' : 'invoiceEndDateMain')?.value;
    
    if (!reportType) {
        showToast('Please select a report type', 'error');
        return;
    }
    
    // Create form and submit for export
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ route("admin.finance.export-report") }}';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const reportInput = document.createElement('input');
    reportInput.type = 'hidden';
    reportInput.name = 'report_type';
    reportInput.value = reportType;
    form.appendChild(reportInput);
    
    if (sellerId) {
        const sellerInput = document.createElement('input');
        sellerInput.type = 'hidden';
        sellerInput.name = 'seller_id';
        sellerInput.value = sellerId;
        form.appendChild(sellerInput);
    }
    
    if (startDate) {
        const startInput = document.createElement('input');
        startInput.type = 'hidden';
        startInput.name = 'start_date';
        startInput.value = startDate;
        form.appendChild(startInput);
    }
    
    if (endDate) {
        const endInput = document.createElement('input');
        endInput.type = 'hidden';
        endInput.name = 'end_date';
        endInput.value = endDate;
        form.appendChild(endInput);
    }
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
}

// Validate GST number
function validateGSTNew() {
    const gstNumber = document.getElementById('testGstNumberNew')?.value;
    
    if (!gstNumber) {
        showToast('Please enter a GST number to validate', 'error');
        return;
    }
    
    fetch('{{ route("admin.finance.validate-gst") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
        },
        body: JSON.stringify({ gst_number: gstNumber })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
        } else {
            showToast(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast('Server error, please try again', 'error');
    });
}

// Preview invoice template
function previewInvoiceTemplate() {
    // Collect template settings
    const templateData = {
        style: document.getElementById('templateStyleField2')?.value,
        companyName: document.getElementById('companyNameField2')?.value,
        companyGst: document.getElementById('companyGstField2')?.value,
        companyEmail: document.getElementById('companyEmailField2')?.value,
        companyPhone: document.getElementById('companyPhoneField2')?.value,
        companyAddress: document.getElementById('companyAddressField2')?.value,
        invoicePrefix: document.getElementById('invoicePrefixField2')?.value,
        invoiceFooter: document.getElementById('invoiceFooterField2')?.value
    };
    
    // Show preview in modal
    const modal = document.getElementById('invoiceModal');
    const modalBody = document.getElementById('invoicePreview');
    
    if (modalBody) {
        modalBody.innerHTML = `
            <div class="p-4">
                <h5>Invoice Preview</h5>
                <hr>
                <div class="row">
                    <div class="col-12">
                        <p><strong>Template Style:</strong> ${templateData.style}</p>
                        <p><strong>Company Name:</strong> ${templateData.companyName}</p>
                        <p><strong>GST Number:</strong> ${templateData.companyGst}</p>
                        <p><strong>Email:</strong> ${templateData.companyEmail}</p>
                        <p><strong>Phone:</strong> ${templateData.companyPhone}</p>
                        <p><strong>Address:</strong> ${templateData.companyAddress}</p>
                        <p><strong>Invoice Prefix:</strong> ${templateData.invoicePrefix}</p>
                        <p><strong>Footer:</strong> ${templateData.invoiceFooter}</p>
                    </div>
                </div>
            </div>
        `;
    }
    
    if (modal) {
        new bootstrap.Modal(modal).show();
    }
}

// Toast notification helper
function showToast(message, type = 'success') {
    const toast = document.getElementById('toastMessage');
    const toastBody = document.getElementById('toastMessageBody');
    
    if (toast && toastBody) {
        toastBody.textContent = message;
        toast.classList.remove('bg-success', 'bg-danger', 'bg-warning');
        
        if (type === 'success') {
            toast.classList.add('bg-success');
        } else if (type === 'error') {
            toast.classList.add('bg-danger');
        } else if (type === 'warning') {
            toast.classList.add('bg-warning');
        }
        
        const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
        bsToast.show();
    } else {
        alert(message);
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load existing tax regions and currencies data if needed
    console.log('Tax & Invoice JS loaded');
});
</script>
@endsection
