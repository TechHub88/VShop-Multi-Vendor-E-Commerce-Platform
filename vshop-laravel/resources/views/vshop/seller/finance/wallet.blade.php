@extends('vshop.seller.main')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<style>
/* ================= GLOBAL DESIGN SYSTEM ================= */
body {
    background-color: #f6f8fa !important;
}
.wallet-container {
    font-family: 'Outfit', sans-serif !important;
    color: #2D3748;
}
.font-outfit {
    font-family: 'Outfit', sans-serif !important;
}
.card-premium {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e9ecef;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.02);
    transition: all 0.3s ease;
}
.card-premium:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.05);
}

/* ================= HEADER CONTROLS ================= */
.search-pill {
    background: #ffffff;
    border-radius: 30px;
    border: 1px solid #e2e8f0;
    padding: 6px 18px;
    display: flex;
    align-items: center;
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.01);
}
.search-pill input {
    border: none;
    outline: none;
    font-size: 14px;
    width: 280px;
    margin-left: 8px;
}
.control-badge {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 20px;
    padding: 6px 12px;
    font-size: 14px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    transition: all 0.2s;
}
.control-badge:hover {
    border-color: #94a3b8;
    background: #f8fafc;
}
.my-customers-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 600;
    font-size: 13px;
    color: #475569;
}

/* ================= TAB BUTTONS ================= */
.tabs-navigation {
    display: flex;
    gap: 8px;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 8px;
    margin-bottom: 24px;
}
.tab-btn-custom {
    padding: 10px 20px;
    font-weight: 600;
    font-size: 14px;
    color: #64748b;
    background: transparent;
    border: none;
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.2s;
}
.tab-btn-custom.active {
    background: #ffffff;
    color: #1e293b;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    border: 1px solid #e2e8f0;
}
.tab-btn-custom:hover:not(.active) {
    color: #334155;
    background: #f1f5f9;
}
.tab-pane-custom {
    display: none;
    animation: slideUp 0.3s ease;
}
.tab-pane-custom.active {
    display: block;
}
@keyframes slideUp {
    from { transform: translateY(10px); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}

/* ================= SPECIALIZED WALLET CARDS ================= */
.wallet-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: 12px;
}
.wallet-box {
    border-radius: 12px;
    padding: 14px;
    position: relative;
    border: 1px solid transparent;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 105px;
    transition: all 0.2s;
}
.wallet-box:hover {
    transform: translateY(-2px);
}
.wallet-box small {
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.wallet-box h4 {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
}

/* Color palettes matching mockup */
.wb-available {
    background: #e8f0fe;
    color: #1967d2;
    border-color: #d2e3fc;
}
.wb-hold {
    background: #f1f3f4;
    color: #3c4043;
    border-color: #e8eaed;
}
.wb-released {
    background: #e6f4ea;
    color: #137333;
    border-color: #ceead6;
}
.wb-lead {
    background: #fce8e6;
    color: #c5221f;
    border-color: #fad2cf;
}
.wb-boost {
    background: #fef7e0;
    color: #b06000;
    border-color: #feebc8;
}

/* Tooltip style */
.info-tooltip {
    position: absolute;
    top: 6px;
    right: 8px;
    font-size: 12px;
    color: currentColor;
    cursor: help;
}

/* ================= DEDUCTION CARDS ================= */
.deduction-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 12px;
    text-align: center;
    height: 90px;
    display: flex;
    flex-direction: column;
    justify-content: center;
}
.deduction-box small {
    font-weight: 600;
    color: #64748b;
    font-size: 11px;
    text-transform: uppercase;
}
.deduction-box h5 {
    font-size: 17px;
    font-weight: 700;
    color: #1e293b;
    margin-top: 4px;
    margin-bottom: 0;
}

/* ================= ACTION CTA BUTTONS ================= */
.btn-recharge {
    background-color: #004392;
    color: #ffffff;
    font-weight: 600;
    font-size: 14px;
    border-radius: 8px;
    padding: 10px 24px;
    border: none;
    transition: all 0.2s;
}
.btn-recharge:hover {
    background-color: #003370;
    transform: translateY(-1px);
}
.btn-auto-deduction {
    color: #2f855a;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: color 0.2s;
}
.btn-auto-deduction:hover {
    color: #22543d;
}

/* ================= PREMIUM TABLES ================= */
.table th {
    font-weight: 600;
    color: #1e293b !important;
    background-color: #f1f5f9 !important;
    border-bottom: 2px solid #e2e8f0 !important;
    font-size: 12px;
    text-transform: uppercase;
    padding: 12px !important;
}
.table td {
    padding: 12px !important;
    font-size: 13px;
    color: #334155;
    border-bottom: 1px solid #f1f5f9;
}
.badge-deducted {
    background-color: #fee2e2;
    color: #991b1b;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
}
.badge-pending {
    background-color: #fef3c7;
    color: #92400e;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
}
.badge-refunded {
    background-color: #d1fae5;
    color: #065f46;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
}
.badge-credit {
    background-color: #dbeafe;
    color: #1e40af;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
}
.badge-released {
    background-color: #d1fae5;
    color: #065f46;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
}
.badge-onhold {
    background-color: #fef3c7;
    color: #92400e;
    padding: 4px 10px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
}

/* ================= RIGHT SIDEBAR ================= */
.sidebar-section-title {
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    color: #64748b;
    letter-spacing: 0.8px;
    margin-bottom: 12px;
}
.alert-card-warning {
    background: #fff5f5;
    border: 1px solid #feb2b2;
    border-radius: 8px;
    padding: 10px 14px;
    color: #c53030;
    font-size: 12px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
}
.attraction-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 10px 14px;
    display: flex;
    align-items: center;
    gap: 12px;
    transition: all 0.2s;
}
.attraction-card:hover {
    border-color: #cbd5e1;
    transform: translateX(2px);
}
.attraction-icon {
    font-size: 24px;
}
.rule-item {
    display: flex;
    align-items: flex-start;
    gap: 8px;
    font-size: 12px;
    color: #475569;
    background: #f8fafc;
    border-radius: 8px;
    padding: 10px;
    margin-bottom: 8px;
    border-left: 3px solid #3182ce;
}

/* ================= SWITCH TOGGLE ================= */
.form-switch .form-check-input {
    width: 2.8em;
    height: 1.5em;
    cursor: pointer;
}

/* ================= SMALL CENTERED MODAL STYLES ================= */
.modal-recharge {
    font-family: 'Outfit', sans-serif;
}
.modal-recharge .modal-dialog {
    max-width: 500px;
    margin: 1.75rem auto;
}
.modal-recharge .modal-content {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}
.modal-recharge .modal-header {
    border-bottom: 1px solid #e2e8f0;
    padding: 16px 20px;
    background: linear-gradient(135deg, #004392 0%, #0056b3 100%);
    color: white;
    border-radius: 20px 20px 0 0;
}
.modal-recharge .modal-header .btn-close {
    filter: brightness(0) invert(1);
}
.modal-recharge .modal-body {
    padding: 20px;
}
.modal-recharge .modal-footer {
    border-top: 1px solid #e2e8f0;
    padding: 12px 20px;
}
.recharge-amount-btn {
    transition: all 0.2s;
    border-radius: 10px;
    padding: 8px;
    text-align: center;
    cursor: pointer;
    border: 2px solid #e2e8f0;
    background: white;
    font-weight: 600;
    font-size: 13px;
}
.recharge-amount-btn:hover, .recharge-amount-btn.active {
    border-color: #004392;
    background: #e8f0fe;
    color: #004392;
}
.upload-area {
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    padding: 15px;
    text-align: center;
    cursor: pointer;
    transition: all 0.2s;
}
.upload-area:hover {
    border-color: #004392;
    background: #f8fafc;
}
.upload-area.drag-over {
    border-color: #004392;
    background: #e8f0fe;
}
.file-list-item {
    background: #f8fafc;
    border-radius: 8px;
    padding: 8px 12px;
    margin-top: 8px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}
.preview-image {
    max-width: 100%;
    max-height: 150px;
    border-radius: 8px;
    margin-top: 10px;
}
.chart-box div {
    width: 100% !important;
    height: 100% !important;
}
.chart-box div canvas {
    width: 100% !important;
    height: 100% !important;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}

.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    font-size: 4px;
    width: 35px;
    height: 38px;
}

.card,
.kpi-card {
    border: 1px solid rgb(201 208 218) !important;
}

/* Deduction Summary Styles */
.deduction-summary-card {
    background: #f8fafc;
    border-radius: 12px;
    padding: 16px;
    margin-top: 20px;
}
.deduction-summary-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 0;
    border-bottom: 1px solid #e2e8f0;
}
.deduction-summary-item:last-child {
    border-bottom: none;
}

/* Small form elements for modal */
.modal-body .form-label {
    font-size: 13px;
    margin-bottom: 6px;
}
.modal-body .form-control, .modal-body .form-select {
    font-size: 13px;
    padding: 6px 10px;
}
.modal-body .alert {
    padding: 10px;
    font-size: 12px;
    margin-bottom: 12px;
}
</style>

<div class="container-fluid py-4 wallet-container">

    {{-- ================= REDESIGNED HEADER BAR ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        
        <div class="d-flex align-items-center top-back-heading-sec gap-3">
            <button onclick="window.history.back()" class="nav-btn" style="display: flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <div>
                <h5 class="fw-bold text-dark mb-0 font-outfit" style="letter-spacing: -0.5px;">
                    Wallet & Settlement
                </h5>
            </div>
        </div>

        {{-- SEARCH & CONTROLS --}}
        <div class="d-flex align-items-center flex-wrap gap-3">
            
            {{-- Middle Search Box --}}
            <div class="search-pill">
                <i class="fa-solid fa-magnifying-glass text-muted"></i>
                <input type="text" id="tableSearchInput" placeholder="Search Enquiries, Services, Sources...">
            </div>

            {{-- Currency Selector Pill --}}
            <div class="control-badge" title="Currency Selector">
                <select id="currencySwitcher" class="border-0 bg-transparent fw-bold" style="outline: none; cursor: pointer; padding: 4px 8px;">
                    <option value="INR">₹ INR</option>
                    <option value="USD">$ USD</option>
                    <option value="EUR">€ EUR</option>
                </select>
            </div>

            {{-- Help Center Button --}}
            <a href="{{ route('seller.grievance.list') }}" class="btn btn-warning font-outfit text-dark" style="font-weight: 600; border-radius: 8px; font-size: 13px; padding: 8px 16px; text-decoration: none;">
                <i class="fa-solid fa-lightbulb me-1"></i> Help Center
            </a>

        </div>

    </div>

    {{-- ================= REDESIGNED TAB SELECTOR ================= --}}
    <div class="tabs-navigation">
        <button class="tab-btn-custom active" data-target="overview-tab-pane">
            Overview
        </button>
        <button class="tab-btn-custom" data-target="deduction-tab-pane">
            Deduction History
        </button>
        <button class="tab-btn-custom" data-target="billing-tab-pane">
            Click & Lead Billing
        </button>
        <button class="tab-btn-custom" data-target="transactions-tab-pane">
            Wallet Transactions
        </button>
        <button class="tab-btn-custom" data-target="referral-tab-pane">
            Referral Management
        </button>
    </div>

    {{-- ================= MAIN COLUMN STRUCTURE ================= --}}
    <div class="row g-4">

        {{-- LEFT MAIN AREA (col-lg-8) --}}
        <div class="col-lg-8">

            {{-- TAB PANE: OVERVIEW --}}
            <div id="overview-tab-pane" class="tab-pane-custom active">
                
                <div class="row g-3 mb-4">
                    
                    {{-- 1. Wallet Balances Grid (col-md-9) --}}
                    <div class="col-md-9">
                        <div class="card-premium p-3">
                            
                            <h6 class="fw-bold mb-3 font-outfit" style="font-size: 13px; color: #475569;">
                                WALLET BALANCES (Total: <span class="money" data-amount="{{ $total_balances ?? 0 }}">₹{{ number_format($total_balances ?? 0, 2) }}</span>)
                            </h6>

                            <div class="wallet-grid">
                                
                                {{-- Available Box --}}
                                <div class="wallet-box wb-available">
                                    <small>Available</small>
                                    <h4 class="money" data-amount="{{ $available ?? 0 }}">
                                        ₹{{ number_format($available ?? 0, 2) }}
                                    </h4>
                                </div>

                                {{-- Hold Box --}}
                                <div class="wallet-box wb-hold">
                                    <small>Hold</small>
                                    <h4 class="money" data-amount="{{ $hold ?? 0 }}">
                                        ₹{{ number_format($hold ?? 0, 2) }}
                                    </h4>
                                </div>

                                {{-- Released Box --}}
                                <div class="wallet-box wb-released">
                                    <small>Released</small>
                                    <h4 class="money" data-amount="{{ $released ?? 0 }}">
                                        ₹{{ number_format($released ?? 0, 2) }}
                                    </h4>
                                    <span class="info-tooltip" title="Released conform to your configure the surface click and deduction wallet.">
                                        <i class="fa-solid fa-circle-info"></i>
                                    </span>
                                </div>

                                {{-- Lead Deduction Box --}}
                                <div class="wallet-box wb-lead">
                                    <small>Lead Deduction</small>
                                    <h4 class="money" data-amount="{{ $lead_deduction_wallet ?? 0 }}">
                                        ₹{{ number_format($lead_deduction_wallet ?? 0, 2) }}
                                    </h4>
                                </div>

                                {{-- Boost Spend Box --}}
                                <div class="wallet-box wb-boost">
                                    <small>Boost Spend</small>
                                    <h4 class="money" data-amount="{{ $boost_spend_wallet ?? 0 }}">
                                        ₹{{ number_format($boost_spend_wallet ?? 0, 2) }}
                                    </h4>
                                </div>

                            </div>

                        </div>
                    </div>

                    {{-- 2. Deduction Summary Side Grid (col-md-3) --}}
                    <div class="col-md-3">
                        <div class="card-premium p-3 d-flex flex-column justify-content-between" style="height: 100%;">
                            
                            <h6 class="fw-bold mb-2 font-outfit text-truncate" style="font-size: 13px; color: #475569;">
                                DEDUCTION SUMMARY
                            </h6>

                            <div class="d-flex flex-column gap-2">
                                
                                {{-- Today's Deduction --}}
                                <div class="deduction-box">
                                    <small>Today's Deduction</small>
                                    <h5 class="money text-danger" data-amount="{{ $today_deduction ?? 0 }}">
                                        ₹{{ number_format($today_deduction ?? 0, 2) }}
                                    </h5>
                                </div>

                                {{-- Monthly Deduction --}}
                                <div class="deduction-box">
                                    <small>Monthly Deduction</small>
                                    <h5 class="money text-dark" data-amount="{{ $monthly_deduction ?? 0 }}">
                                        ₹{{ number_format($monthly_deduction ?? 0, 2) }}
                                    </h5>
                                </div>

                            </div>

                        </div>
                    </div>

                </div>

                {{-- Action CTA Row --}}
                <div class="card-premium p-3 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-secondary font-outfit" style="padding: 6px 12px; font-size: 12px; background-color: #cbd5e1 !important; color: #334155;">
                            Action Row
                        </span>
                        <button class="btn btn-recharge font-outfit" id="rechargeWalletBtn">
                            <i class="fa-solid fa-circle-plus me-1"></i> Recharge Wallet/Account
                        </button>
                    </div>

                    <a href="#" class="btn-auto-deduction">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        Auto Deduction History
                    </a>
                </div>

                {{-- CLICK & LEAD BILLING LOGS TABLE --}}
                <div class="card-premium p-4 mb-4">
                    
                    <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 gap-2">
                        <div>
                            <h5 class="fw-bold mb-1 font-outfit">
                                <i class="fa-solid fa-bell text-primary me-1" style="font-size: 16px;"></i>
                                CLICK & LEAD BILLING HISTORY
                            </h5>
                            <span class="text-muted" style="font-size: 12px; font-weight: 500;">
                                Purpose: transparency and seller can audit charges
                            </span>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Listing Name</th>
                                    <th>Interaction Type</th>
                                    <th>Buyer Source</th>
                                    <th>Charge Amount</th>
                                    <th>Wallet</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($clickLeadBilling ?? [] as $log)
                                <tr>
                                    <td>{{ $log->date }}</td>
                                    <td class="fw-bold">{{ $log->listing_name }}</td>
                                    <td>
                                        @if($log->interaction_type == 'View Contact')
                                            <i class="fa-solid fa-address-book text-info me-1"></i> View Contact
                                        @elseif($log->interaction_type == 'WhatsApp Click')
                                            <i class="fa-brands fa-whatsapp text-success me-1"></i> WhatsApp Click
                                        @elseif($log->interaction_type == 'Call Click')
                                            <i class="fa-solid fa-phone text-primary me-1"></i> Call Click
                                        @elseif($log->interaction_type == 'Website Visit')
                                            <i class="fa-solid fa-globe text-secondary me-1"></i> Website Visit
                                        @elseif($log->interaction_type == 'Brochure Download')
                                            <i class="fa-solid fa-file-pdf text-danger me-1"></i> Brochure Download
                                        @else
                                            <i class="fa-solid fa-envelope text-warning me-1"></i> Enquiry Submit
                                        @endif
                                    </td>
                                    <td class="fw-semibold">
                                        @if($log->buyer_source == 'Google')
                                            <span class="badge bg-danger-subtle text-danger rounded-circle px-2 py-1 me-1">G</span> Google
                                        @elseif($log->buyer_source == 'Social')
                                            <i class="fa-solid fa-share-nodes text-primary me-1"></i> Social
                                        @else
                                            <i class="fa-solid fa-mobile-screen text-success me-1"></i> App
                                        @endif
                                    </td>
                                    <td class="fw-bold">
                                        <span class="money" data-amount="{{ $log->charge }}">
                                            ₹{{ number_format($log->charge, 2) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $log->wallet_deducted }}</span>
                                    </td>
                                    <td>
                                        @if($log->status == 'Deducted')
                                            <span class="badge-deducted">Deducted</span>
                                        @elseif($log->status == 'Pending')
                                            <span class="badge-pending">Pending</span>
                                        @else
                                            <span class="badge-refunded">Refunded</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No click or lead logs found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                         </table>
                    </div>

                    <div class="mt-3 p-3 bg-light rounded-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <small class="text-muted fw-semibold">
                            Note: Invalid click ret will be invalid by refund of dramaocetartwed not click refunds.
                        </small>
                    </div>

                </div>

            </div>

            {{-- TAB PANE: DEDUCTION HISTORY (ALL TRANSACTIONS & ORDERS) --}}
            <div id="deduction-tab-pane" class="tab-pane-custom">
                
                {{-- Settlement Info Box --}}
                <div class="card-premium p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                        <div>
                            <h5 class="fw-bold mb-1 font-outfit">Settlement Details</h5>
                            <p class="text-muted mb-0" style="font-size: 13px;">Configure your payout holding and settlement preferences</p>
                        </div>
                        <button type="button" class="btn btn-primary btn-sm font-outfit" id="editSettlementBtn" style="border-radius: 8px; padding: 6px 14px;">
                            <i class="fa-solid fa-pen-to-square me-1"></i> Edit Settlement
                        </button>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Settlement Period</small>
                                <div id="settlementView" class="mt-2">
                                    <h5 class="fw-bold" id="settlementCycleText">{{ $currentSettlementCycle ?? 15 }} Day Settlement Period</h5>
                                    <p class="text-muted mb-0" style="font-size: 12px;">Settlement = Delivery Date + Refund/Return Period + {{ $currentSettlementCycle ?? 15 }} days</p>
                                </div>
                                <div id="settlementEdit" class="mt-2" style="display:none;">
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="settlementCycleInput" min="0" max="30" value="{{ $currentSettlementCycle ?? 15 }}">
                                        <span class="input-group-text">Days</span>
                                    </div>
                                    <div class="d-flex gap-2 mt-2">
                                        <button class="btn btn-success btn-sm" id="saveSettlementBtn">Save</button>
                                        <button class="btn btn-light border btn-sm" id="cancelSettlementBtn">Cancel</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 border">
                                <small class="text-muted fw-bold text-uppercase" style="font-size: 11px;">Release Rule</small>
                                <h5 class="fw-bold mt-2 text-dark" style="font-size: 15px;">Deduct from available balance</h5>
                                <p class="text-muted mb-0" style="font-size: 12px;">Refunds and adjustments are deducted directly from your available wallet balance.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- All Transactions Subtabs inside --}}
                <div class="card-premium p-4 mb-4">
                    <h5 class="fw-bold mb-3 font-outfit">📋 Ledger & Transactions</h5>
                    
                    <ul class="nav nav-tabs mb-3" id="ledgerSubTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#sub-trans">All Transactions</button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#sub-orders">All Orders</button>
                        </li>
                    </ul>

                    <div class="tab-content">
                        
                        {{-- Subtab: Transactions --}}
                        <div class="tab-pane fade show active" id="sub-trans">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Reference</th>
                                            <th>Type</th>
                                            <th>Credit</th>
                                            <th>Debit</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($transactions ?? [] as $t)
                                        <tr>
                                            <td>{{ date('d M Y', strtotime($t->created_at)) }}</td>
                                            <td>#{{ $t->reference_no ?? 'N/A' }}</td>
                                            <td><span class="badge bg-info">{{ $t->transaction_type }}</span></td>
                                            <td class="text-success fw-bold">
                                                @if($t->credit > 0)
                                                    + <span class="money" data-amount="{{ $t->credit }}">₹{{ number_format($t->credit, 2) }}</span>
                                                @endif
                                            </td>
                                            <td class="text-danger fw-bold">
                                                @if($t->debit > 0)
                                                    - <span class="money" data-amount="{{ $t->debit }}">₹{{ number_format($t->debit, 2) }}</span>
                                                @endif
                                            </td>
                                            <td><span class="badge bg-secondary">{{ $t->status }}</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center text-muted">No Data Found</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ ($transactions ?? collect())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>

                        {{-- Subtab: Orders --}}
                        <div class="tab-pane fade" id="sub-orders">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Order ID</th>
                                            <th>Price</th>
                                            <th>Order Status</th>
                                            <th>Delivery Date</th>
                                            <th>Settlement Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($orders ?? [] as $order)
                                        <tr>
                                            <td>{{ date('d M Y', strtotime($order->created_at)) }}</td>
                                            <td><a href="#" target="_blank">#{{ $order->id }}</a></td>
                                            <td class="fw-bold">
                                                <span class="money" data-amount="{{ $order->total_amount ?? 0 }}">₹{{ number_format($order->total_amount ?? 0, 2) }}</span>
                                            </td>
                                            <td><span class="badge bg-info">{{ $order->status }}</span></td>
                                            <td>{{ $order->delivered_at ? date('d M Y', strtotime($order->delivered_at)) : 'Not Delivered' }}</td>
                                            <td><span class="badge bg-success">Settled</span></td>
                                        </tr>
                                        @empty
                                        <tr><td colspan="6" class="text-center text-muted">No Orders Found</td></tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-3">
                                {{ ($orders ?? collect())->links('pagination::bootstrap-5') }}
                            </div>
                        </div>

                    </div>
                </div>

            </div>

            {{-- TAB PANE: CLICK & LEAD BILLING --}}
            <div id="billing-tab-pane" class="tab-pane-custom">
                <div class="card-premium p-4 mb-4">
                    <h5 class="fw-bold mb-3 font-outfit">🤝 Referral Finance Details</h5>
                    
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Referral Reward Deductions</th>
                                    <th>Referral-Generated Sales</th>
                                    <th>Remaining Referral Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($referralFinance ?? [] as $rf)
                                <tr>
                                    <td>{{ date('Y-m-d', strtotime($rf->date)) }}</td>
                                    <td>{{ $rf->name }}</td>
                                    <td class="fw-bold text-danger">
                                        - <span class="money" data-amount="{{ $rf->deduction }}">₹{{ number_format($rf->deduction, 2) }}</span>
                                    </td>
                                    <td class="fw-bold text-success">
                                        + <span class="money" data-amount="{{ $rf->sales }}">₹{{ number_format($rf->sales, 2) }}</span>
                                    </td>
                                    <td class="fw-bold text-primary">
                                        <span class="money" data-amount="{{ $rf->budget }}">₹{{ number_format($rf->budget, 2) }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center text-muted">No Referral Finance Logs Found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- TAB PANE: WALLET TRANSACTIONS --}}
            <div id="transactions-tab-pane" class="tab-pane-custom">
                <div class="card-premium p-4 mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold mb-0 font-outfit">💰 All Wallet Transactions</h5>
                        <button class="btn btn-sm btn-outline-primary" id="refreshWalletTransactions">
                            <i class="fa-solid fa-rotate-right"></i> Refresh
                        </button>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Transaction Type</th>
                                    <th>Credit / Debit</th>
                                    <th>Amount</th>
                                    <th>Balance After</th>
                                    <th>Reference ID</th>
                                    <th>Description / Remarks</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="walletTransactionsTable">
                                @forelse($walletTransactions ?? [] as $wt)
                                @php
                                    $isCredit = (float)($wt->credit ?? 0) > 0;
                                    $amount = $isCredit ? (float)$wt->credit : (float)($wt->debit ?? 0);
                                @endphp
                                <tr>
                                    <td>{{ date('d M Y, h:i A', strtotime($wt->created_at)) }}</td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-2 py-1 fw-bold text-uppercase" style="font-size: 11px;">
                                            {{ str_replace('_', ' ', $wt->transaction_type ?? 'transaction') }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($isCredit)
                                            <span class="badge-credit"><i class="fa-solid fa-circle-up"></i> Credit</span>
                                        @else
                                            <span class="badge-deducted"><i class="fa-solid fa-circle-down"></i> Debit</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold {{ $isCredit ? 'text-success' : 'text-danger' }}">
                                        <span class="money" data-amount="{{ $amount }}">
                                            {{ $isCredit ? '+' : '-' }} ₹{{ number_format($amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="fw-bold text-slate-700">
                                        <span class="money" data-amount="{{ $wt->balance_after ?? 0 }}">
                                            ₹{{ number_format($wt->balance_after ?? 0, 2) }}
                                        </span>
                                    </td>
                                    <td><small class="text-primary fw-bold">{{ $wt->reference_id ?? 'N/A' }}</small></td>
                                    <td><small class="text-muted">{{ $wt->description ?? '-' }}</small></td>
                                    <td>
                                        @if($wt->status == 'completed' || $wt->status == 'success')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($wt->status == 'pending')
                                            <span class="badge-pending">Pending</span>
                                        @elseif($wt->status == 'hold')
                                            <span class="badge bg-warning text-dark">On Hold</span>
                                        @else
                                            <span class="badge-deducted">Failed</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">No wallet transactions found</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ ($walletTransactions ?? collect())->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>

            {{-- TAB PANE: REFERRAL MANAGEMENT --}}
            <div id="referral-tab-pane" class="tab-pane-custom">
                
                {{-- Referral Commission History Table --}}
                <div class="card-premium p-4 mb-4">
                    <h5 class="fw-bold mb-3 font-outfit">
                        <i class="fa-solid fa-chart-line text-primary me-2"></i>
                        REFERRAL COMMISSION HISTORY (Commission Hold Status)
                    </h5>
                    <small class="text-muted d-block mb-3">Purpose: Transparency</small>
                    
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Source</th>
                                    <th>Commission Amount</th>
                                    <th>Status</th>
                                    <th>Release Date</th>
                                    <th>Hold Reason</th>
                                    <th>Deduction Summary</th>
                                                            <tbody>
                                @forelse($referralFinance ?? [] as $rf)
                                <tr>
                                    <td>{{ date('d M Y', strtotime($rf->date)) }}</td>
                                    <td>{{ $rf->name }}</td>
                                    <td class="fw-bold money" data-amount="{{ $rf->deduction }}">₹{{ number_format($rf->deduction, 2) }}</td>
                                    <td><span class="badge-released">Released</span></td>
                                    <td>{{ date('d M Y', strtotime($rf->date)) }}</td>
                                    <td>Sale Completed</td>
                                    <td>Deduction Summary - {{ $rf->name }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">No referral commission history found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{-- Deduction Summaries Section --}}
                    <div class="deduction-summary-card">
                        <h6 class="fw-bold mb-3">Deduction Summaries</h6>
                        <div class="deduction-summary-item">
                            <span>Today's Deduction:</span>
                            <strong class="text-danger money" data-amount="{{ $today_deduction ?? 0 }}">-₹{{ number_format($today_deduction ?? 0, 2) }}</strong>
                        </div>
                        <div class="deduction-summary-item">
                            <span>Monthly Deduction:</span>
                            <strong class="text-danger money" data-amount="{{ $monthly_deduction ?? 0 }}">-₹{{ number_format($monthly_deduction ?? 0, 2) }}</strong>
                        </div>
                        <div class="deduction-summary-item">
                            <span>Total Remaining Referral Budget:</span>
                            <strong class="text-success money" data-amount="{{ $available ?? 0 }}">₹{{ number_format($available ?? 0, 2) }}</strong>
                        </div>
                    </div>
                </div>

                {{-- Referral Finance Table --}}
                <div class="card-premium p-4 mb-4">
                    <h5 class="fw-bold mb-3 font-outfit">
                        <i class="fa-solid fa-hand-holding-usd text-success me-2"></i>
                        Referral Finance
                    </h5>
                    
                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Referral Reward Deductions</th>
                                    <th>Referral-Generated Sales</th>
                                    <th>Remaining Referral Budget</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($referralFinance ?? [] as $rf)
                                <tr>
                                    <td>{{ date('Y-m-d', strtotime($rf->date)) }}</td>
                                    <td>{{ $rf->name }}</td>
                                    <td class="fw-bold text-danger money" data-amount="{{ $rf->deduction }}">-₹{{ number_format($rf->deduction, 2) }}</td>
                                    <td class="fw-bold text-success money" data-amount="{{ $rf->sales }}">+₹{{ number_format($rf->sales, 2) }}</td>
                                    <td class="fw-bold text-primary money" data-amount="{{ $rf->budget }}">₹{{ number_format($rf->budget, 2) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No referral finance data found.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        </div>

        {{-- RIGHT SIDEBAR (col-lg-4) --}}
        <div class="col-lg-4">

            {{-- DYNAMIC SECTIONS CARD --}}
            <div class="card-premium p-4 mb-4">
                
                <h6 class="fw-bold mb-3 font-outfit" style="font-size: 13px; color: #475569; border-bottom: 2px solid #cbd5e1; padding-bottom: 6px;">
                    DYNAMIC SECTIONS
                </h6>

                {{-- Section: Settlement & Alerts --}}
                <div class="mb-4">
                    <h6 class="sidebar-section-title d-flex justify-content-between align-items-center">
                        SETTLEMENT & ALERTS <i class="fa-solid fa-circle-info text-muted" style="cursor:help;"></i>
                    </h6>
                    
                    <div class="d-flex flex-column gap-3">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <span style="font-size: 13px; font-weight: 500;">Settlement Cycle:</span>
                            <span class="badge bg-primary rounded-pill font-outfit px-2 py-1_5 fw-bold">{{ $currentSettlementCycle ?? 15 }}-day</span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2">
                            <div style="font-size: 13px; font-weight: 500;">
                                Low Balance Alert:
                                <div class="text-muted" style="font-size: 11px;">Lead Deduction Wallet &lt; 5,000</div>
                            </div>
                            <div class="form-check form-switch m-0">
                                <input class="form-check-input" type="checkbox" role="switch" id="lowBalanceToggle" {{ ($lead_deduction_wallet ?? 0) < 5000 ? 'checked' : '' }}>
                            </div>
                        </div>

                        {{-- Alert Callout --}}
                        @if(($lead_deduction_wallet ?? 0) < 5000)
                            <div class="alert-card-warning">
                                <i class="fa-solid fa-circle-exclamation fs-5"></i>
                                <div>Minimum balance alert before lead deduction fails (Current: ₹{{ number_format($lead_deduction_wallet ?? 0, 2) }})</div>
                            </div>
                        @else
                            <div class="alert-card-warning" style="background: #f0fdf4; border-color: #bbf7d0; color: #166534;">
                                <i class="fa-solid fa-circle-check fs-5 text-success"></i>
                                <div>Wallet balance is healthy (Current: ₹{{ number_format($lead_deduction_wallet ?? 0, 2) }})</div>
                            </div>
                        @endif

                    </div>
                </div>

                {{-- Section: Interesting Small Attractions --}}
                <div class="mb-4">
                    <h6 class="sidebar-section-title">
                        INTERESTING SMALL ATTRACTIONS <small class="text-muted text-lowercase" style="font-weight: 400;">(dynamic based on logic)</small>
                    </h6>

                    <div class="d-flex flex-column gap-2">
                        @forelse($interestingAttractions ?? [] as $attraction)
                            <div class="attraction-card">
                                <span class="attraction-icon">🏬</span>
                                <div>
                                    <h6 class="fw-bold mb-0 font-outfit" style="font-size: 13px;">{{ $attraction->name ?? 'Featured Product' }}</h6>
                                    <span class="text-muted" style="font-size: 11px;">(₹{{ number_format($attraction->price ?? $attraction->unit_price ?? 0, 2) }})</span>
                                </div>
                            </div>
                        @empty
                            <div class="attraction-card">
                                <span class="attraction-icon">🏬</span>
                                <div>
                                    <h6 class="fw-bold mb-0 font-outfit" style="font-size: 13px;">Local Pawn Shop</h6>
                                    <span class="text-muted" style="font-size: 11px;">(e.g., Banarasi Pawn)</span>
                                </div>
                            </div>
                            <div class="attraction-card">
                                <span class="attraction-icon">👗</span>
                                <div>
                                    <h6 class="fw-bold mb-0 font-outfit" style="font-size: 13px;">Famous Saree Center</h6>
                                    <span class="text-muted" style="font-size: 11px;">(e.g., Traditional Silks)</span>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Section: Wallet Deduction Rule Explainer --}}
                <div class="mb-4">
                    <h6 class="sidebar-section-title">
                        WALLET DEDUCTION RULE EXPLAINER
                    </h6>

                    <div class="rule-item">
                        <i class="fa-solid fa-circle-info mt-0_5"></i>
                        <div>Refund to wallet when click and enquiry is invalid (e.g., duplicate, bot, wrong number)</div>
                    </div>

                    <div class="rule-item">
                        <i class="fa-solid fa-circle-info mt-0_5"></i>
                        <div>Refund to wallet when click and enquiry is in recllet (e.g., chat name)</div>
                    </div>
                </div>

                {{-- Section: Settlement Cycle Details --}}
                <div>
                    <h6 class="sidebar-section-title">
                        SETTLEMENT CYCLE DETAILS
                    </h6>

                    <div class="d-flex flex-column gap-3">
                        
                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Settlement Cycle</label>
                            <input type="text" class="form-control form-control-sm font-outfit fw-bold bg-light" value="{{ $currentSettlementCycle ?? 15 }}-day" readonly>
                        </div>

                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Balancy Type</label>
                            <select class="form-select form-select-sm font-outfit fw-semibold">
                                <option value="lead" selected>Lead Forwriter</option>
                                <option value="standard">Standard Account</option>
                            </select>
                        </div>

                        <div>
                            <label class="form-label text-muted fw-bold text-uppercase mb-1" style="font-size: 10px;">Low Balance Alert</label>
                            <input type="text" class="form-control form-control-sm font-outfit fw-bold bg-light" value="< 5,000" readonly>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>

</div>

{{-- ================= SMALL CENTERED RECHARGE WALLET MODAL ================= --}}
<div class="modal fade modal-recharge" id="rechargeWalletModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fa-solid fa-circle-plus me-2"></i> Recharge Wallet
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="rechargeWalletForm">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount (₹)</label>
                        <div class="row g-2 mb-2">
                            <div class="col-4">
                                <div class="recharge-amount-btn" data-amount="500">₹500</div>
                            </div>
                            <div class="col-4">
                                <div class="recharge-amount-btn" data-amount="1000">₹1,000</div>
                            </div>
                            <div class="col-4">
                                <div class="recharge-amount-btn" data-amount="2000">₹2,000</div>
                            </div>
                            <div class="col-4">
                                <div class="recharge-amount-btn" data-amount="5000">₹5,000</div>
                            </div>
                            <div class="col-4">
                                <div class="recharge-amount-btn" data-amount="10000">₹10,000</div>
                            </div>
                            <div class="col-4">
                                <div class="recharge-amount-btn" data-amount="25000">₹25,000</div>
                            </div>
                        </div>
                        <input type="number" class="form-control" id="rechargeAmount" name="amount" placeholder="Custom amount" step="1" min="1">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Method</label>
                        <select class="form-select" name="payment_method" id="paymentMethod" required>
                            <option value="">Select</option>
                            <option value="bank_transfer">🏦 Bank Transfer</option>
                            <option value="upi">📱 UPI</option>
                            <option value="card">💳 Card</option>
                        </select>
                    </div>

                    <div id="bankDetailsSection" style="display: none;">
                        <div class="alert alert-info p-2 small">Bank: Example Bank<br>Ac: XXXXXXXXXX<br>IFSC: XXXXXX</div>
                    </div>

                    <div id="upiDetailsSection" style="display: none;">
                        <div class="alert alert-info p-2 small">UPI ID: example@bank</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Transaction ID</label>
                        <input type="text" class="form-control" name="transaction_id" placeholder="Enter transaction ID" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Proof</label>
                        <div class="upload-area" id="uploadArea" style="padding: 10px;">
                            <i class="fa-solid fa-cloud-upload-alt fa-lg mb-1 text-muted"></i>
                            <p class="mb-0 small">Click to upload</p>
                            <input type="file" id="attachmentInput" name="attachment" accept="image/*,application/pdf" style="display: none;">
                        </div>
                        <div id="filePreview" class="mt-1"></div>
                    </div>

                    <div class="alert alert-warning p-2 small mb-0">
                        <i class="fa-solid fa-clock me-1"></i> After verification
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-recharge btn-sm" id="submitRechargeBtn">
                        <i class="fa-solid fa-paper-plane me-1"></i> Submit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function(){

    // Currency Conversion
    let currency = localStorage.getItem('currency') || 'INR';
    const rates = { INR: 1, USD: 0.012, EUR: 0.011 };
    const symbols = { INR: '₹', USD: '$', EUR: '€' };
    const switcher = document.getElementById('currencySwitcher');
    if(switcher) switcher.value = currency;

    function formatMoney(amount){
        return amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }
    
    function updateCurrency(){
        document.querySelectorAll('.money').forEach(el => {
            let amount = parseFloat(el.dataset.amount || 0);
            let converted = amount * rates[currency];
            el.innerHTML = symbols[currency] + ' ' + formatMoney(converted);
        });
    }

    updateCurrency();
    
    if(switcher){
        switcher.addEventListener('change', function(){
            currency = this.value;
            localStorage.setItem('currency', currency);
            updateCurrency();
        });
    }

    // Tab Switching
    const tabButtons = document.querySelectorAll('.tab-btn-custom');
    const tabPanes = document.querySelectorAll('.tab-pane-custom');
    
    function switchTab(targetPaneId) {
        tabPanes.forEach(pane => pane.classList.remove('active'));
        tabButtons.forEach(btn => btn.classList.remove('active'));
        const activePane = document.getElementById(targetPaneId);
        if(activePane) activePane.classList.add('active');
        const activeBtn = document.querySelector(`.tab-btn-custom[data-target="${targetPaneId}"]`);
        if(activeBtn) activeBtn.classList.add('active');
        localStorage.setItem('activeWalletTabMain', targetPaneId);
    }
    
    tabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            switchTab(this.getAttribute('data-target'));
        });
    });
    
    const savedTab = localStorage.getItem('activeWalletTabMain') || 'overview-tab-pane';
    switchTab(savedTab);

    // Edit Settlement
    $('#editSettlementBtn').on('click', function(){
        $('#settlementView').hide();
        $('#settlementEdit').fadeIn();
    });

    $('#cancelSettlementBtn').on('click', function(){
        $('#settlementEdit').hide();
        $('#settlementView').fadeIn();
    });

    $('#saveSettlementBtn').on('click', function(){
        let cycle = $('#settlementCycleInput').val();
        if(cycle === '' || cycle < 0){
            alert('Please enter valid settlement duration');
            return;
        }
        $('#settlementCycleText').html(cycle + ' Day Settlement Period');
        $('#settlementEdit').hide();
        $('#settlementView').fadeIn();
        alert('Settlement period updated to ' + cycle + ' days');
    });

    // Country Filter
    const countryFilter = document.getElementById('countryFilter');
    if(countryFilter){
        countryFilter.addEventListener('change', function(){
            let url = new URL(window.location.href);
            url.searchParams.set('country', this.value);
            window.location.href = url.toString();
        });
    }

    // Table Search
    const searchInput = document.getElementById('tableSearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            document.querySelectorAll('.wallet-container table tbody').forEach(tbody => {
                const rows = tbody.querySelectorAll('tr');
                let visibleRows = 0;
                rows.forEach(row => {
                    if (row.classList.contains('no-results-row') || (row.cells.length === 1 && row.textContent.includes('No '))) {
                        row.style.display = 'none';
                        return;
                    }
                    const text = row.textContent.toLowerCase();
                    if (text.includes(query)) {
                        row.style.display = '';
                        visibleRows++;
                    } else {
                        row.style.display = 'none';
                    }
                });
                let fallbackRow = tbody.querySelector('.no-results-row');
                if (query !== '' && visibleRows === 0) {
                    if (!fallbackRow) {
                        fallbackRow = document.createElement('tr');
                        fallbackRow.className = 'no-results-row';
                        const colCount = tbody.closest('table').querySelectorAll('thead th').length || 7;
                        fallbackRow.innerHTML = `<td colspan="${colCount}" class="text-center text-muted py-4">No matching records found for "${query}"</td>`;
                        tbody.appendChild(fallbackRow);
                    } else {
                        fallbackRow.style.display = '';
                    }
                } else if (fallbackRow && query !== '') {
                    fallbackRow.style.display = 'none';
                } else if (query === '' && fallbackRow) {
                    fallbackRow.remove();
                    rows.forEach(row => row.style.display = '');
                }
            });
        });
    }

    // Recharge Modal
    const rechargeBtn = document.getElementById('rechargeWalletBtn');
    const rechargeModal = new bootstrap.Modal(document.getElementById('rechargeWalletModal'));
    
    if(rechargeBtn) {
        rechargeBtn.addEventListener('click', function() {
            rechargeModal.show();
        });
    }
    
    document.querySelectorAll('.recharge-amount-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const amount = this.dataset.amount;
            document.getElementById('rechargeAmount').value = amount;
            document.querySelectorAll('.recharge-amount-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    const paymentMethodSelect = document.getElementById('paymentMethod');
    const bankDetailsSection = document.getElementById('bankDetailsSection');
    const upiDetailsSection = document.getElementById('upiDetailsSection');
    
    if(paymentMethodSelect) {
        paymentMethodSelect.addEventListener('change', function() {
            bankDetailsSection.style.display = 'none';
            upiDetailsSection.style.display = 'none';
            if(this.value === 'bank_transfer') {
                bankDetailsSection.style.display = 'block';
            } else if(this.value === 'upi') {
                upiDetailsSection.style.display = 'block';
            }
        });
    }
    
    const uploadArea = document.getElementById('uploadArea');
    const attachmentInput = document.getElementById('attachmentInput');
    const filePreview = document.getElementById('filePreview');
    
    if(uploadArea) {
        uploadArea.addEventListener('click', () => attachmentInput.click());
        attachmentInput.addEventListener('change', function() {
            if(this.files && this.files[0]) {
                filePreview.innerHTML = `<div class="file-list-item p-1 small"><i class="fa-solid fa-file"></i> ${this.files[0].name}</div>`;
            }
        });
    }
    
    const rechargeForm = document.getElementById('rechargeWalletForm');
    if(rechargeForm) {
        rechargeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                icon: 'success',
                title: 'Request Submitted!',
                text: 'Your recharge request has been submitted for verification.',
                confirmButtonColor: '#004392'
            }).then(() => {
                rechargeModal.hide();
                rechargeForm.reset();
                filePreview.innerHTML = '';
            });
        });
    }
    
    document.getElementById('refreshWalletTransactions')?.addEventListener('click', function() {
        location.reload();
    });
    
    document.getElementById('lowBalanceToggle')?.addEventListener('change', function() {
        localStorage.setItem('lowBalanceAlert', this.checked);
    });
    
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection