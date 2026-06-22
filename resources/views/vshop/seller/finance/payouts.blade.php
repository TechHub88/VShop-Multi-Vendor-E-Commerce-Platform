@extends('vshop.seller.main')
<style>
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
    .card,
    .payout-card, 
    .settlement-card{
        border: 1px solid rgb(201 208 218) !important;
    }
    .card-body {
        overflow: auto;
    }
    .action-custom-btn i {
        font-size: 15px;
    }
    table th {
        font-weight: 600;
        color: #fff !important;
        background-color: #004392 !important;
        border: 1px solid #fff;
    }
    .save-btn {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
        border: 0 !important;
        font-size: 16px !important;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
        border: 0 !important;
        font-size: 16px !important;
    }
    .btn-danger,
    .pdf-btn {
        background: #233d78 !important;
        border-color: #233d78 !important;
        color: #fff !important;
    }
    .btn-success,
    .excel-btn {
        background: #61b3e5 !important;
        border-color: #61b3e5 !important;
        color: #fff !important;
    }
    #editAccountBtn,
    #addAccountBtn,
    #reverifyBtn {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
        border: 0 !important;
        color: #fff !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">


{{-- ================= HEADER FILTER SECTION ================= --}}
<div class="d-flex justify-content-between align-items-start mb-4 flex-wrap gap-3 payout-header-wrap">

    {{-- LEFT --}}
    <div class="d-flex top-back-heading-sec">

        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <div>
            <h4 class="fw-bold mb-1" style="color: #0f172a !important;font-size: 20px;">
                Payout Management
            </h4>

            <small class="text-muted">
                Manage payouts, GST reports & settlement tracking
            </small>
        </div>

    </div>

    {{-- RIGHT FILTER BOX --}}
    <div class="top-payout-fixed-box">

        {{-- DEFAULT REGION --}}
        <div class="mini-top-box">

            <small>Default Region</small>

            @php

                $countryIds = \DB::table('seller_operation_areas')
                    ->where('seller_id', $seller->id)
                    ->whereNotNull('country_id')
                    ->pluck('country_id')
                    ->unique();

                $countries = \DB::table('countries')
                    ->whereIn('id', $countryIds)
                    ->get();

            @endphp

            <select id="countryFilter"
                    class="form-select top-filter-select">

                @foreach($countries as $country)

                    <option value="{{ $country->id }}"
                        {{ $selectedCountry == $country->id ? 'selected' : '' }}>

                        {{ $country->name }}

                    </option>

                @endforeach

            </select>

        </div>

        {{-- LAST UPDATED --}}
        <div class="mini-top-box">

            <small>Last Updated</small>

            <h6 class="filter-value-text">
                {{ $seller->updated_at
                    ? \Carbon\Carbon::parse($seller->updated_at)->diffForHumans()
                    : 'N/A'
                }}
            </h6>

        </div>


{{-- NEXT PAYOUT - DYNAMIC --}}
<div class="mini-top-box" id="nextPayoutHeaderBox">

    <small>Next Payout Date</small>

    <h6 class="filter-value-text" id="headerNextPayoutDate">

        {{ $seller->manual_next_date ? \Carbon\Carbon::parse($seller->manual_next_date)->addDay()->format('d M Y') : 'Not Set' }}

    </h6>

</div>
        {{-- PAYOUT SCHEDULE --}}
<div class="mini-top-box">

    <div class="mini-box-content full-width">

        <small>Payout Schedule</small>

        <select id="scheduleFilter"
                class="form-select top-filter-select">

            <option value="">
                All
            </option>

            <option value="auto"
                {{ request('schedule') == 'auto' ? 'selected' : '' }}>
                Auto
            </option>

            <option value="manual"
                {{ request('schedule') == 'manual' ? 'selected' : '' }}>
                Manual
            </option>

        </select>

    </div>

</div>

        {{-- CURRENCY --}}
        <div class="mini-top-box">

            <small>Currency</small>

            <select id="currencySwitcher"
                    class="form-select top-filter-select">

                <option value="INR"
                    {{ $selectedCurrency == 'INR' ? 'selected' : '' }}>
                    ₹ INR
                </option>

                <option value="USD"
                    {{ $selectedCurrency == 'USD' ? 'selected' : '' }}>
                    $ USD
                </option>

                <option value="EUR"
                    {{ $selectedCurrency == 'EUR' ? 'selected' : '' }}>
                    € EUR
                </option>

            </select>

        </div>

    </div>

</div>




{{-- ================= SUMMARY ================= --}}
<div class="row g-3 mb-4">

@foreach([
['Available Balance',$availableBalance,'success'],
['Pending',$pendingPayouts->sum('debit'),'warning'],
['Completed',$completedPayouts->sum('debit'),'primary'],
['Failed',$failedPayouts->sum('debit'),'danger']
] as $card)

<div class="col-md-3">
<div class="card shadow-sm text-center p-3">
<h6>{{$card[0]}}</h6>
<h4 class="text-{{$card[2]}} money" data-amount="{{$card[1]}}">
</h4>
</div>
</div>

@endforeach

</div>

<div class="row g-3 mb-3 payout-row">

    {{-- ================= PAYOUT SETTINGS ================= --}}
    <div class="col-lg-6 d-flex">

        <div class="card shadow-sm border-0 payout-card w-100">

            <div class="card-header bg-white fw-bold border-0 py-3">
                Payout Settings
            </div>

            <div class="card-body">

                <form method="POST"
                      action="{{ route('seller.payout.settings') }}"
                      id="payoutSettingsForm">

                    @csrf

                    <div class="row payout-settings-vertical">

                        {{-- PAYOUT CYCLE --}}
                        <div class="col-12">

                            <label class="fw-semibold mb-2 d-block">
                                Payout Cycle
                            </label>

                            <input type="text"
                                   name="manual_cycle"
                                   id="manualCycle"
                                   class="form-control modern-input"

                                   value="{{ $cycleStart }} → {{ $cycleEnd }}">

                        </div>

                        {{-- NEXT PAYOUT DATE INPUT --}}
                       {{-- NEXT PAYOUT DATE INPUT --}}
<div class="col-12">

    <label class="fw-semibold mb-2 d-block">
        Next Payout Date
    </label>

   <input type="date"
       name="manual_next_date"
       id="manualNextDate"
       class="form-control modern-input"
       value="{{ $seller->manual_next_date ? date('Y-m-d', strtotime($seller->manual_next_date)) : '' }}">
</div>

                        {{-- SCHEDULE --}}
                        <div class="col-12">

                            <label class="fw-semibold mb-2 d-block">
                                Schedule
                            </label>

                            <div class="d-flex gap-2 align-items-center">

                                <select name="payout_mode"
                                        id="payoutMode"
                                        class="form-control modern-input">

                                    <option value="auto"
                                        {{ $payoutMode=='auto' ? 'selected' : '' }}>
                                        Auto
                                    </option>

                                    <option value="manual"
                                        {{ $payoutMode=='manual' ? 'selected' : '' }}>
                                        Manual
                                    </option>

                                </select>

                                <button type="submit"
                                        class="btn btn-success save-btn">

                                    Save

                                </button>

                            </div>

                        </div>

                    </div>

                </form>

                <div class="summary-box">
                    <div class="summary-item">
                        <span>Available Balance</span>
                        <strong class="money" data-amount="{{ $availableBalance }}"></strong>
                    </div>
                    <div class="summary-item">
                        <span>Pending Settlement</span>
                        <strong class="money" data-amount="{{ $pendingPayouts->sum('debit') }}"></strong>
                    </div>
                    <div class="summary-item border-0 pb-0 mb-0">
                        <span>Next Payout</span>
                        <strong id="summaryNextPayout">{{ $nextPayoutDate && $nextPayoutDate != 'Withdraw Anytime'
                            ? \Carbon\Carbon::parse($nextPayoutDate)->format('d M Y')
                            : '10 June 2026' }}
                        </strong>
                    </div>
                </div>

            </div>

        </div>

    </div>

    {{-- ================= SETTLEMENT TRACKER ================= --}}
    <div class="col-lg-6 d-flex">

        <div class="card shadow-sm border-0 settlement-card w-100">

            <div class="card-body text-center">

                <h5 class="fw-bold mb-3 settlement-title">
                    Settlement Delay Tracker
                </h5>

                {{-- GAUGE --}}
                <div id="gaugeChart"></div>

                {{-- DAYS --}}
                <div class="settlement-days">
                    {{ $delayDays }} Days
                </div>

                {{-- STATUS --}}
                <div class="settlement-status-wrap">

                    <div>
                        Cycle: {{ $cycleDays }} Days
                    </div>

                    @if($delayDays > 0)
                        <span class="text-danger fw-bold">Delayed</span>
                    @else
                        <span class="text-success fw-bold">On Time</span>
                    @endif

                </div>

                {{-- STATUS BOX --}}
                <div class="status-box">

                    <div class="status-item">
                        <i class="fa fa-check-circle text-success"></i>
                        Account Status
                    </div>

                    <div class="status-item">
                        <i class="fa fa-check-circle text-success"></i>
                        Bank Verified
                    </div>

                    <div class="status-item">
                        <i class="fa fa-check-circle text-success"></i>
                        GST Registered
                    </div>

                    <div class="status-item">
                        <i class="fa fa-check-circle text-success"></i>
                        PAN Verified
                    </div>

                    <div class="status-item border-0 pb-0 mb-0">
                        <i class="fa fa-check-circle text-success"></i>
                        KYC Verified
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>


{{-- ================= PAYOUT MANAGEMENT TABLE ================= --}}
<div class="card shadow-sm mb-4 border-0 payout-main-card">

    <div class="card-header bg-white border-0">

        <div class="d-flex justify-content-between align-items-center flex-wrap">

            <div>

                <h4 class="fw-bold mb-3 d-flex align-items-center gap-2" style="color: #0f172a !important;font-size: 20px;">
                    <i class="fa-solid fa-wallet text-primary"></i>
                    Payout Management
                </h4>

                <div class="d-flex payout-tabs">

                    <button class="tab-btn active"
                            data-tab="upcoming">

                        Upcoming Payouts

                    </button>

                    <button class="tab-btn"
                            data-tab="completed">

                        Completed Payouts

                    </button>

                    <button class="tab-btn"
                            data-tab="failed">

                        Failed Payouts

                    </button>

                </div>

            </div>

        </div>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table class="table align-middle payout-table">

                <thead>

                    <tr>

                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Amount</th>
                        <th>Mode</th>
                        <th>Method</th>
                        <th>UTR</th>
                        <th>Transaction ID</th>
                        <th>Status</th>
                        <th>Invoice</th>

                    </tr>

                </thead>

                <tbody>

                    {{-- ================= UPCOMING ================= --}}
                    @forelse($pendingPayouts as $p)

                    <tr class="tab-row upcoming-row">

                        <td>
                            {{ $p->created_at->format('d M y') }}
                        </td>

                        <td>
                            {{ $p->reference_no ?? ('ORD-'.$p->id) }}
                        </td>

                        <td class="money"
                            data-amount="{{ $p->debit }}">

                            ₹ {{ number_format($p->debit,2) }}

                        </td>

                        <td class="text-capitalize">
                            {{ $p->mode ?? '-' }}
                        </td>

                        <td class="text-uppercase">
                            {{ $p->payout_method ?? 'BANK' }}
                        </td>

                        <td>
                            {{ $p->utr_number ?? '-' }}
                        </td>

                        <td>
                            {{ $p->transaction_id ?? '-' }}
                        </td>

                        <td>

                            <span class="status-badge upcoming">

                                Upcoming

                            </span>

                        </td>

                        <td>

                            <a href="{{ route('seller.invoice.download',$p->id) }}"
                               class="invoice-icon">

                                <i class="fa-regular fa-file-lines"></i>

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr class="tab-row upcoming-row">

                        <td colspan="10"
                            class="text-center py-4 text-muted">

                            No upcoming payouts found

                        </td>

                    </tr>

                    @endforelse


                    {{-- ================= COMPLETED ================= --}}
                    @forelse($completedPayouts as $p)

                    <tr class="tab-row completed-row d-none">

                        <td>
                            {{ $p->created_at->format('d M y') }}
                        </td>

                        <td>
                            {{ $p->reference_no ?? ('ORD-'.$p->id) }}
                        </td>

                        <td class="money"
                            data-amount="{{ $p->debit }}">

                            ₹ {{ number_format($p->debit,2) }}

                        </td>

                        <td class="text-capitalize">
                            {{ $p->mode ?? '-' }}
                        </td>

                        <td class="text-uppercase">
                            {{ $p->payout_method ?? 'BANK' }}
                        </td>

                        <td>
                            {{ $p->utr_number ?? '-' }}
                        </td>

                        <td>
                            {{ $p->transaction_id ?? '-' }}
                        </td>

                        <td>

                            <span class="status-badge success">

                                Completed

                            </span>

                        </td>

                        <td>

                            <a href="{{ route('seller.invoice.download',$p->id) }}"
                               class="invoice-icon">

                                <i class="fa-regular fa-file-lines"></i>

                            </a>

                        </td>

                    </tr>

                    @empty

                    <tr class="tab-row completed-row d-none">

                        <td colspan="10"
                            class="text-center py-4 text-muted">

                            No completed payouts found

                        </td>

                    </tr>

                    @endforelse


                    {{-- ================= FAILED ================= --}}
                    @forelse($failedPayouts as $p)

                    <tr class="tab-row failed-row d-none">

                        <td>
                            {{ $p->created_at->format('d M y') }}
                        </td>

                        <td>
                            {{ $p->reference_no ?? ('ORD-'.$p->id) }}
                        </td>

                        <td class="money"
                            data-amount="{{ $p->debit }}">

                            ₹ {{ number_format($p->debit,2) }}

                        </td>

                        <td class="text-capitalize">
                            {{ $p->mode ?? '-' }}
                        </td>

                        <td class="text-uppercase">
                            {{ $p->payout_method ?? 'BANK' }}
                        </td>

                        <td>
                            {{ $p->utr_number ?? '-' }}
                        </td>

                        <td>
                            {{ $p->transaction_id ?? '-' }}
                        </td>

                        <td>

                            <span class="status-badge failed">

                                Failed

                            </span>

                        </td>

                        <td>

                            <button class="action-btn">

                                Retry

                            </button>

                        </td>

                    </tr>

                    @empty

                    <tr class="tab-row failed-row d-none">

                        <td colspan="10"
                            class="text-center py-4 text-muted">

                            No failed payouts found

                        </td>

                    </tr>

                    @endforelse

                </tbody>

            </table>

        </div>


        {{-- ================= BOTTOM INFO ================= --}}
        <div class="bottom-payout-info">

            <div>

                <strong>
                    Payout Schedule:
                </strong>

                <span class="text-primary fw-semibold payout-mode-text text-capitalize">

                    {{ request('schedule') ?? ucfirst($payoutMode ?? 'auto') }}

                </span>

            </div>

            <div>

                <i class="fa-solid fa-circle-info text-primary"></i>

                <span id="bottomPayoutText">

                    @if((request('schedule') ?? $payoutMode ?? 'auto') == 'manual')

                        Manual payout mode is currently enabled

                    @else

                        Next payout will be automatically initiated on

                        <strong id="bottomNextPayoutDate">

                            {{ $nextPayoutDate && $nextPayoutDate != 'Withdraw Anytime'
                                ? \Carbon\Carbon::parse($nextPayoutDate)->format('d M Y')
                                : '10 June 2026'
                            }}

                        </strong>

                    @endif

                </span>

            </div>

        </div>

    </div>

</div>
{{-- =========================================================
   WITHDRAWAL & BANK DETAILS
========================================================= --}}
<div class="card shadow-sm mb-4 border-0 withdraw-bank-wrapper">

    <div class="card-body p-4">

        {{-- HEADER --}}
        <div class="d-flex align-items-center gap-3 mb-4">

            <div class="withdraw-icon-box">
                <i class="fa-solid fa-building-columns"></i>
            </div>

            <div>

                <h4 class="fw-bold mb-1" style="color: #0f172a !important;font-size: 20px;">
                    Withdrawal & Bank Details
                </h4>

                <small class="text-muted">
                    Withdrawal & Bank Details
                </small>

            </div>

        </div>

        <div class="row g-4">

{{-- LEFT : DYNAMIC BANK ACCOUNT --}}
<div class="col-lg-4">

    @php
        $defaultAccount = $bankAccounts->where('is_primary',1)->first();
        if(!$defaultAccount){
            $defaultAccount = $bankAccounts->last();
        }
    @endphp

    <div class="mini-info-card h-100">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Bank Account</h6>
            <span class="badge bg-success" id="accountVerifyBadge">
                {{ ucfirst($defaultAccount->status ?? 'verified') }}
            </span>
        </div>

        <div id="selectedAccountDetails">
            <div class="bank-detail-item">
                <small>Bank Name</small>
                <strong id="displayBankName">{{ $defaultAccount->bank_name ?? '-' }}</strong>
            </div>
            <div class="bank-detail-item mt-3">
                <small>Account Holder</small>
                <strong id="displayAccountHolder">{{ $defaultAccount->account_name ?? '-' }}</strong>
            </div>
            <div class="bank-detail-item mt-3">
                <small>Account Number</small>
                <strong id="displayAccountNumber">
                    @if($defaultAccount && $defaultAccount->account_number)
                        XXXX XXXX {{ substr($defaultAccount->account_number,-4) }}
                    @else - @endif
                </strong>
            </div>
            <div class="bank-detail-item mt-3">
                <small>IFSC Code</small>
                <strong id="displayIfsc">{{ $defaultAccount->ifsc_code ?? '-' }}</strong>
            </div>
            <div class="bank-detail-item mt-3">
                <small>Payment Method</small>
                <strong id="displayMethod">{{ strtoupper($defaultAccount->method ?? 'BANK') }}</strong>
            </div>
            <div class="bank-detail-item mt-3" id="upiDisplayWrap" style="{{ ($defaultAccount && $defaultAccount->method == 'upi') ? '' : 'display:none' }}">
                <small>UPI ID</small>
                <strong id="displayUpi">{{ $defaultAccount->upi_id ?? '-' }}</strong>
            </div>
        </div>

        <div class="d-flex gap-2 flex-wrap mt-4">
            <button type="button" id="editAccountBtn" class="btn btn-light-primary btn-sm action-custom-btn">
                <i class="fa fa-pen me-1"></i> Edit Account
            </button>
            <button type="button" id="addAccountBtn" class="btn btn-light-blue btn-sm action-custom-btn">
                <i class="fa fa-plus me-1"></i> Add New Account
            </button>
            <button type="button" id="reverifyBtn" class="btn btn-light-warning btn-sm action-custom-btn">
                <i class="fa fa-shield-halved me-1"></i> Re-verify
            </button>
        </div>

    </div>

</div>

{{-- WITHDRAWAL REQUEST --}}
<div class="col-lg-4">

    <div class="mini-info-card h-100">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0">Withdrawal Request</h6>
            <small class="text-muted" id="minWithdrawText">Minimum Withdrawal ₹100</small>
        </div>

        <form method="POST" action="{{ route('seller.finance.withdraw') }}" id="withdrawForm">
            @csrf

            <div class="mb-3">
                <input type="number" name="amount" class="form-control withdraw-input" placeholder="Enter Amount" min="100" required>
            </div>

            <div class="account-selection-box">
                <div class="select-all-row d-flex justify-content-between align-items-center">
                    <label class="d-flex align-items-center gap-2 mb-0">
                        <input type="checkbox" id="selectAllWithdrawAccounts">
                        <span class="fw-semibold">Select All Accounts</span>
                    </label>
                </div>

                <div id="accountsListContainer">
                @forelse($bankAccounts as $acc)
                <div class="account-item-wrapper" data-account-id="{{ $acc->id }}">
                    <label class="account-option withdraw-select-account"
                           data-id="{{ $acc->id }}"
                           data-method="{{ $acc->method }}"
                           data-bank_name="{{ $acc->bank_name }}"
                           data-account_name="{{ $acc->account_name }}"
                           data-account_number="{{ $acc->account_number }}"
                           data-ifsc="{{ $acc->ifsc_code }}"
                           data-upi="{{ $acc->upi_id }}"
                           data-status="{{ $acc->status ?? 'Verified' }}">
                        <div class="d-flex align-items-center gap-3">
                            <div class="account-check-wrap">
                                <input type="checkbox" name="accounts[]" value="{{ $acc->id }}" class="withdraw-account-check">
                            </div>
                            <div>
                                @if($acc->method == 'bank')
                                    <span class="method-pill bank">BANK</span>
                                @else
                                    <span class="method-pill upi">UPI</span>
                                @endif
                            </div>
                        </div>
                        <div class="account-short-info">
                            @if($acc->method == 'bank')
                                <div class="fw-semibold">XXXX{{ substr($acc->account_number,-4) }}</div>
                                <small class="text-muted">{{ $acc->account_name }}</small>
                            @else
                                <div class="fw-semibold">{{ $acc->upi_id }}</div>
                            @endif
                        </div>
                    </label>
                    <button type="button" class="delete-account-btn" data-account-id="{{ $acc->id }}" data-account-name="{{ $acc->account_name ?? $acc->upi_id }}" title="Delete Account">
                        <i class="fa fa-trash-alt"></i>
                    </button>
                </div>
                @empty
                <div class="text-muted small p-3 text-center" id="noAccountsMsg">
                    <i class="fa fa-info-circle me-1"></i> No bank accounts available. Please add an account.
                </div>
                @endforelse
                </div>
            </div>

            <div class="selected-account-info mt-3">
                Selected Accounts : <strong id="selectedAccountCount">0</strong>
            </div>

            <button type="submit" class="btn btn-primary request-btn w-100 mt-3">
                <i class="fa fa-money-bill-wave me-1"></i> Request Withdrawal
            </button>
        </form>

    </div>

</div>

{{-- RIGHT : WITHDRAWAL HISTORY --}}
<div class="col-lg-4">

    <div class="mini-info-card h-100">

        <h6 class="fw-bold mb-3">Withdrawal History</h6>

        <div class="table-responsive">
            <table class="table table-sm history-table mb-0">
                <thead>
                    <tr><th>Date</th><th>Amount</th><th>Status</th></tr>
                </thead>
                <tbody>
                    @forelse($payouts->take(5) as $p)
                    <tr>
                        <td>{{ $p->created_at->format('d M Y') }}</td>
                        <td class="money" data-amount="{{ $p->debit }}"></td>
                        <td>
                            @if($p->transaction_type == 'payout_success')
                                <span class="badge bg-success">Success</span>
                            @elseif($p->transaction_type == 'payout_failed')
                                <span class="badge bg-danger">Failed</span>
                            @else
                                <span class="badge bg-warning text-dark">Processing</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center text-muted">No history found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>

</div>


{{-- =====================================================
   BANK & UPI FORM (FIXED - No Cancel Cheque for UPI)
====================================================== --}}
<div class="bank-form-wrapper mt-4">
    <div class="bank-form-title">Bank & UPI Details</div>
    <div style="padding: 20px">
    <form method="POST" action="{{ route('seller.bank.save') }}" enctype="multipart/form-data" id="bankForm">
        @csrf
        <div class="row g-3">
            
            {{-- METHOD SELECT --}}
            <div class="col-md-3">
                <label class="form-label">Select Method <span class="text-danger">*</span></label>
                <select name="method" id="methodSelect" class="form-control" required>
                    <option value="">Select</option>
                    <option value="bank">Bank</option>
                    <option value="upi">UPI</option>
                </select>
            </div>

            {{-- BANK FIELDS (shown only when Bank is selected) --}}
            <div id="bankFieldsContainer" style="display: none;">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Bank Name <span class="text-danger">*</span></label>
                        <input type="text" name="bank_name" class="form-control" placeholder="Enter bank name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control" placeholder="Enter account holder name">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Account Number <span class="text-danger">*</span></label>
                        <input type="text"
                               name="account_number"
                               class="form-control"
                               placeholder="Enter account number"
                               maxlength="18"
                               minlength="8"
                               pattern="[0-9]{8,18}"
                               inputmode="numeric"
                               oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                        <small class="text-muted">Account number must be 8 to 18 digits only</small>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">IFSC Code <span class="text-danger">*</span></label>
                        <input type="text" name="ifsc" class="form-control text-uppercase" placeholder="Enter IFSC code">
                        <small class="text-muted">Example: SBIN0001234</small>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Cancel Cheque</label>
                        <input type="file" name="cancel_cheque" class="form-control" accept="image/*,application/pdf">
                        <small class="text-muted">Upload cancelled cheque (JPG, PNG, PDF) - Optional</small>
                    </div>
                </div>
            </div>

            {{-- UPI FIELDS (shown only when UPI is selected) - NO CANCEL CHEQUE FIELD --}}
            <div id="upiFieldsContainer" style="display: none;">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Account Name</label>
                        <input type="text" name="account_name_upi" class="form-control" placeholder="Enter account holder name">
                        <small class="text-muted">Optional</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">UPI ID <span class="text-danger">*</span></label>
                        <input type="text" name="upi" class="form-control" placeholder="example@upi">
                        <small class="text-muted">Example: username@bankname</small>
                    </div>
                </div>
            </div>

            {{-- SAVE BUTTON --}}
            <div class="col-md-3">
                <button type="submit" class="btn btn-success save-btn w-100 mt-4" id="formSaveBtn">Save</button>
            </div>

        </div>
    </form>
    </div>
</div>

</div>

</div>



{{-- ================= GST BREAKDOWN ================= --}}
<div class="card shadow-sm border-0 gst-card mb-4">

    <div class="card-body">

        <div class="d-flex align-items-center gap-2 mb-2">

            <h5 class="fw-bold mb-0" style="color: #0f172a !important;font-size: 20px;">
                GST Breakdown
            </h5>

        </div>

        <h6 class="fw-bold mt-4 mb-3">
            Transaction-wise GST Breakdown
        </h6>

        <div class="table-responsive">

            <table class="table gst-table align-middle">

                <thead>

                    <tr>

                        <th>Date</th>
                        <th>Order ID</th>
                        <th>Amount</th>
                        <th>GST (18%)</th>
                        <th>Platform Fee</th>
                        <th>Total</th>

                    </tr>

                </thead>

                <tbody>

                    @foreach($completedPayouts->take(3) as $p)

                    @php

                        $gst = $p->debit * 0.18;

                        // PLATFORM FEE (5%)
                        $platformFee = $p->debit * 0.05;

                        $total = $p->debit + $gst + $platformFee;

                    @endphp

                    <tr>

                        <td>
                            {{ $p->created_at->format('d M Y') }}
                        </td>

                        <td>
                            ORD{{ $p->id }}
                        </td>

                        <td class="money"
                            data-amount="{{ $p->debit }}">

                        </td>

                        <td class="money"
                            data-amount="{{ $gst }}">

                        </td>

                        <td class="money"
                            data-amount="{{ $platformFee }}">

                        </td>

                        <td class="money"
                            data-amount="{{ $total }}">

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- ================= SUMMARY ================= --}}
        <div class="row g-3 mt-2">

            {{-- TOTAL REVENUE --}}
            <div class="col-md-3">

                <div class="summary-box blue">

                    <small>
                        Total Revenue
                    </small>

                    <h4 class="money"
                        data-amount="{{ $completedPayouts->sum('debit') }}">

                    </h4>

                </div>

            </div>


            {{-- GST --}}
            <div class="col-md-3">

                <div class="summary-box green">

                    <small>
                        Total GST Collected
                    </small>

                    <h4 class="money"
                        data-amount="{{ $completedPayouts->sum('debit') * 0.18 }}">

                    </h4>

                </div>

            </div>


            {{-- PLATFORM FEES --}}
            <div class="col-md-3">

                <div class="summary-box purple">

                    <small>
                        Platform Fees
                    </small>

                    <h4 class="money"
                        data-amount="{{ $completedPayouts->sum('debit') * 0.05 }}">

                    </h4>

                </div>

            </div>


            {{-- NET TAX --}}
            <div class="col-md-3">

                <div class="summary-box orange">

                    <small>
                        Net Tax Payable
                    </small>

                    <h4 class="money"
                        data-amount="{{ $completedPayouts->sum('debit') * 0.15 }}">

                    </h4>

                </div>

            </div>

        </div>


        {{-- ================= EXPORT BUTTONS ================= --}}
        <div class="export-btn-wrap">

            <a href="{{ route('seller.payout.export','pdf') }}"
               class="btn export-btn pdf-btn">

                <i class="fa-regular fa-file-pdf"></i>

                Export PDF

            </a>

            <a href="{{ route('seller.payout.export','excel') }}"
               class="btn export-btn excel-btn">

                <i class="fa-regular fa-file-excel"></i>

                Export Excel

            </a>

        </div>

    </div>

</div>



{{-- ================= MONTHLY TAX SUMMARY ================= --}}
<div class="col-md-12">
    <div class="card shadow-sm mb-4 border-0 tax-summary-card">
        <div class="card-header fw-bold bg-white" style="color: #0f172a !important;font-size: 20px;">Monthly Tax Summary</div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 tax-summary-table">
                <thead><tr><th>Month</th><th>Total Revenue</th><th>Total GST Collected</th><th>Net Tax Payable</th></tr>
                </thead>
                <tbody>
                    @php $monthly = $completedPayouts->groupBy(fn($i)=>$i->created_at->format('M Y')); @endphp
                    @foreach($monthly as $month => $rows)
                    @php $total = $rows->sum('debit'); $gst = $total * 0.18; $netTax = $total * 0.15; @endphp
                    <tr>
                        <td>{{ $month }}</td>
                        <td class="money" data-amount="{{ $total }}"></td>
                        <td class="money" data-amount="{{ $gst }}"></td>
                        <td class="money" data-amount="{{ $netTax }}"></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ========================================================= TAX RULES TABLE ========================================================== --}}
<div class="card shadow-sm border-0 mb-4 tax-summary-card modern-tax-card">

    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">

        <h5 class="fw-bold mb-0 text-uppercase" style="color: #0f172a !important;font-size: 20px;">
            Tax Rules
        </h5>

        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
            Static 
        </span>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table modern-tax-table mb-0">

                <thead>

                    <tr>

                        <th>Region</th>
                        <th>Tax Type</th>
                        <th>Rate</th>
                        <th>Mode</th>
                        <th>Applies To</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>India</td>
                        <td>GST</td>
                        <td>18%</td>
                        <td>Inclusive</td>
                        <td>All Products</td>

                    </tr>

                    <tr>

                        <td>United States</td>
                        <td>Sales Tax</td>
                        <td>8%</td>
                        <td>Exclusive</td>
                        <td>Physical Goods</td>

                    </tr>

                    <tr>

                        <td>United Kingdom</td>
                        <td>VAT</td>
                        <td>20%</td>
                        <td>Inclusive</td>
                        <td>Services</td>

                    </tr>

                    <tr>

                        <td>UAE</td>
                        <td>VAT</td>
                        <td>5%</td>
                        <td>Exclusive</td>
                        <td>Marketplace Orders</td>

                    </tr>

                    <tr>

                        <td>Australia</td>
                        <td>GST</td>
                        <td>10%</td>
                        <td>Inclusive</td>
                        <td>Digital Products</td>

                    </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>


{{-- ========================================================= REGION BREAKDOWN TABLE ========================================================== --}}
<div class="card shadow-sm border-0 mb-4 tax-summary-card modern-tax-card">

    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">

        <h5 class="fw-bold mb-0 text-uppercase" style="color: #0f172a !important;font-size: 20px;">
            Region-wise Breakdown
        </h5>

        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-3 py-2">
            Static 
        </span>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table modern-tax-table mb-0">

                <thead>

                    <tr>

                        <th>Region</th>
                        <th>Tax Type</th>
                        <th>Tax Rate</th>
                        <th>Tax Collected</th>

                    </tr>

                </thead>

                <tbody>

                    <tr>

                        <td>India</td>
                        <td>GST</td>
                        <td>18%</td>
                        <td>₹ 12,500} -->

                    </td>

                </tr>

                <tr>

                    <td>United States</td>
                    <td>Sales Tax</td>
                    <td>8%</td>
                    <td>$ 4,200</td>

                </tr>

                <tr>

                    <td>United Kingdom</td>
                    <td>VAT</td>
                    <td>20%</td>
                    <td>£ 3,100</td>

                </tr>

                <tr>

                    <td>UAE</td>
                    <td>VAT</td>
                    <td>5%</td>
                    <td>د.إ 1,800</td>

                </tr>

                <tr>

                    <td>Australia</td>
                    <td>GST</td>
                    <td>10%</td>
                    <td>A$ 2,450</td>

                </tr>

            </tbody>

        </table>

    </div>

</div>

</div>

{{-- ================= EXPORT ================= --}}
<div class="card shadow-sm mb-4">
<div class="card-body text-end">
<a href="{{ route('seller.payout.export','pdf') }}" class="btn btn-danger">Export PDF</a>
<a href="{{ route('seller.payout.export','excel') }}" class="btn btn-success">Export Excel</a>
</div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/echarts@5.4.3/dist/echarts.min.js"></script>

<style>
/* ========================================= CSS COMBINED - FIXED HOVER ISSUE ========================================= */

/* Base Styles - REMOVED card hover transform */
.chart-box { height: 260px; }
.card { transition: none !important; }
.card:hover { transform: none !important; }

/* Navigation */
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    width: 35px;
    height: 38px;
    transition: all 0.2s ease;
}
.nav-btn:hover {
    background: #004392;
    color: #fff;
}
.top-back-heading-sec { align-items: center; gap: 10px; }

/* Top Payout Fixed Box */
.top-payout-fixed-box { display: flex; align-items: stretch; gap: 12px; flex-wrap: wrap; }
.mini-top-box {
    width: 170px; min-width: 170px; height: auto; background: #f7f8fc;
    border: 1px solid #e5e7ef; border-radius: 14px; padding: 12px 14px;
    display: flex; flex-direction: column; justify-content: center;
    transition: none !important;
}
.mini-top-box small { display: block; font-size: 11px; font-weight: 600; color: #8d8d8d; margin-bottom: 5px; }
.mini-top-box h6 { margin: 0; font-size: 15px; font-weight: 700; color: #3f5bd8; line-height: 1.2; }

/* Payout Row */
.payout-row { align-items: stretch; }
.payout-row > div { display: flex; }

/* Payout & Settlement Cards - REMOVED hover effects */
.payout-card, .settlement-card { 
    border-radius: 22px; 
    overflow: hidden; 
    background: #fff; 
    width: 100%; 
    height: 460px; 
    min-height: 460px; 
    display: flex; 
    flex-direction: column; 
    box-shadow: 0 4px 18px rgba(0,0,0,0.05);
    transition: none !important;
}
.payout-card:hover, .settlement-card:hover {
    transform: none !important;
    box-shadow: 0 4px 18px rgba(0,0,0,0.05) !important;
}
.payout-card .card-body, .settlement-card .card-body { flex: 1; display: flex; flex-direction: column; padding: 18px; }
.payout-card .card-header, .settlement-card .card-header { background: #fff; border-bottom: none; font-weight: 700; padding: 18px 22px; color: #0f172a; font-size: 16px; }

/* Form Inputs */
.modern-input { border-radius: 14px; min-height: 46px; border: 1px solid #e5e7eb; box-shadow: none !important; font-size: 14px; padding: 10px 14px; }
.modern-input:focus { border-color: #22c55e; }
.save-btn { border-radius: 14px; min-width: 90px; height: 46px; font-weight: 600; border: none; background: #16a34a; transition: background 0.2s ease; }
.save-btn:hover { background: #15803d; transform: none !important; }

/* Summary Box */
.summary-box { background: #fafafa; border-radius: 18px; padding: 16px; margin-top: 16px; flex: 1; min-height: 170px; border: 1px solid #f1f1f1; transition: none !important; }
.summary-box:hover { transform: none !important; }
.summary-item { display: flex; justify-content: space-between; align-items: center; padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px dashed #ddd; font-size: 13px; }
.summary-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.summary-item strong { font-size: 14px; font-weight: 700; }

/* Settlement Card */
.settlement-card .card-body { text-align: center; }
#gaugeChart { width: 100%; height: 170px !important; min-height: 170px !important; margin-top: -10px; }
.settlement-days { font-size: 24px; font-weight: 700; color: #111827; margin-top: -8px; margin-bottom: 6px; }
.settlement-status-wrap { font-size: 14px; margin-bottom: 20px; }

/* Status Box */
.status-box { margin-top: 16px; background: #fafafa; border-radius: 18px; padding: 16px; text-align: left; flex: 1; min-height: 170px; border: 1px solid #f1f1f1; transition: none !important; }
.status-box:hover { transform: none !important; }
.status-item { display: flex; align-items: center; gap: 10px; padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px dashed #ddd; font-size: 13px; font-weight: 500; color: #111827; }
.status-item:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
.status-item i { font-size: 14px; }

/* Payout Main Card */
.payout-main-card { border-radius: 20px; border: none; overflow: hidden; background: #fff; transition: none !important; }
.payout-main-card:hover { transform: none !important; }
.payout-tabs { display: flex; align-items: center; gap: 40px; border-bottom: 1px solid #ececec; }
.tab-btn { border: none; background: none; padding: 0 0 14px; font-size: 15px; font-weight: 600; color: #666; position: relative; transition: color 0.2s ease; }
.tab-btn:hover { color: #3f5bd8; transform: none !important; }
.tab-btn.active { color: #3f5bd8; }
.tab-btn.active::after { content: ''; position: absolute; left: 0; bottom: -1px; width: 100%; height: 3px; background: #3f5bd8; border-radius: 30px; }

/* Payout Table */
.payout-table { width: 100%; margin-bottom: 0; }
.payout-table thead th { background: #f7f7f9; font-size: 13px; font-weight: 700; color: #333; border: none; padding: 15px; white-space: nowrap; }
.payout-table tbody td { padding: 16px 15px; vertical-align: middle; border-top: 1px solid #f0f0f0; font-size: 14px; color: #222; white-space: nowrap; }
.payout-table tbody tr:hover { background: #fafcff; }

/* Status Badges */
.status-badge { padding: 7px 14px; border-radius: 30px; font-size: 12px; font-weight: 700; display: inline-block; }
.status-badge.upcoming { background: #fff1c9; color: #c98b00; }
.status-badge.success { background: #daf7e4; color: #148844; }
.status-badge.failed { background: #ffdada; color: #d43c3c; }

/* Action Button */
.action-btn { border: 1px solid #5977f7; background: #fff; color: #5977f7; border-radius: 9px; padding: 6px 16px; font-size: 13px; font-weight: 700; transition: all 0.2s ease; }
.action-btn:hover { background: #5977f7; color: #fff; transform: none !important; }
.invoice-icon { font-size: 18px; color: #5977f7; transition: color 0.2s ease; }
.invoice-icon:hover { color: #2f4fe0; transform: none !important; }

/* Bottom Payout Info */
.bottom-payout-info { background: #f5f7ff; border-radius: 14px; padding: 16px 20px; margin-top: 20px; display: flex; align-items: center; justify-content: space-between; gap: 10px; flex-wrap: wrap; font-size: 14px; }
.bottom-payout-info strong { font-weight: 700; }

/* GST Card */
.gst-card { width: 100%; border: none; border-radius: 20px; overflow: hidden; background: #fff; transition: none !important; }
.gst-card:hover { transform: none !important; }
.gst-card .card-body { padding: 24px; }
.gst-table { width: 100%; margin-bottom: 0; }
.gst-table thead th { background: #f5f5f7; border: none; padding: 14px; font-size: 13px; font-weight: 700; color: #333; white-space: nowrap; }
.gst-table tbody td { border-top: 1px solid #f0f0f0; padding: 14px; font-size: 14px; color: #222; white-space: nowrap; }
.gst-table tbody tr:hover { background: #fafcff; }

/* Summary Box Colors */
.summary-box.blue { background: #eef2ff; color: #3151d3; }
.summary-box.green { background: #e8f8ee; color: #159447; }
.summary-box.orange { background: #fff1e3; color: #ef8a16; }
.summary-box.purple { background: #f3e8ff; color: #6d28d9; border:1px solid #d8b4fe; }
.summary-box.purple small { color:#7c3aed; }
.summary-box.purple h4 { color:#5b21b6; }

/* Export Buttons */
.export-btn-wrap { display: flex; align-items: center; gap: 15px; margin-top: 25px; flex-wrap: wrap; }
.export-btn { min-width: 180px; padding: 12px 20px; border-radius: 12px; display: flex; align-items: center; justify-content: center; gap: 10px; font-size: 14px; font-weight: 700; transition: all 0.2s ease; }
.export-btn:hover { transform: none !important; }
.pdf-btn { border: 1px solid #ffbcbc; background: #fff5f5; color: #e04c4c; }
.pdf-btn:hover { background: #e04c4c; color: #fff; }
.excel-btn { border: 1px solid #bdecc8; background: #f3fff6; color: #17984a; }
.excel-btn:hover { background: #17984a; color: #fff; }

/* Tax Summary */
.tax-summary-card { border-radius: 18px; overflow: hidden; transition: none !important; }
.tax-summary-card:hover { transform: none !important; }
.tax-summary-table thead th { background: #f7f8fc; font-size: 13px; font-weight: 700; color: #333; padding: 14px; white-space: nowrap; }
.tax-summary-table tbody td { padding: 14px; font-size: 14px; vertical-align: middle; }
.tax-summary-table tbody tr:hover { background: #fafcff; }

/* Withdraw Bank UI */
.withdraw-bank-wrapper { border-radius: 20px; transition: none !important; }
.withdraw-bank-wrapper:hover { transform: none !important; }
.withdraw-icon-box { width: 48px; height: 48px; border-radius: 14px; background: #eef2ff; color: #3151d3; display: flex; align-items: center; justify-content: center; font-size: 20px; }
.mini-info-card { border: 1px solid #edf0f5; border-radius: 16px; padding: 18px; background: #fff; transition: none !important; }
.mini-info-card:hover { transform: none !important; }
.bank-detail-item small { display: block; color: #888; font-size: 12px; }
.bank-detail-item strong { font-size: 14px; color: #222; }
.btn-light-primary { background: #edf1ff; color: #3151d3; border: none; transition: all 0.2s ease; }
.btn-light-primary:hover { background: #dce4ff; transform: none !important; }
.btn-light-blue { background: #eef7ff; color: #0077ff; border: none; transition: all 0.2s ease; }
.btn-light-blue:hover { background: #ddeeff; transform: none !important; }
.btn-light-warning { background: #fff4df; color: #db8a00; border: none; transition: all 0.2s ease; }
.btn-light-warning:hover { background: #ffe8c4; transform: none !important; }
.withdraw-input { height: 46px; border-radius: 10px; }
.request-btn { height: 46px; border-radius: 10px; font-weight: 600; transition: background 0.2s ease; }
.request-btn:hover { transform: none !important; }

/* Account Selection */
.account-selection-box { border: 1px solid #edf0f5; border-radius: 14px; overflow: hidden; background: #fff; }
.select-all-row { padding: 12px 14px; background: #fafcff; border-bottom: 1px solid #edf0f5; }
.account-option { display: flex; align-items: center; justify-content: space-between; padding: 14px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: background 0.2s ease; }
.account-option:last-child { border-bottom: none; }
.account-option:hover { background: #fafcff; transform: none !important; }
.method-pill { padding: 5px 12px; border-radius: 30px; font-size: 11px; font-weight: 700; }
.method-pill.bank { background: #e9f1ff; color: #2155d6; }
.method-pill.upi { background: #f4ebff; color: #7c35d4; }
.account-short-info { text-align: right; font-size: 13px; color: #555; }
.selected-account-info { font-size: 14px; color: #555; }
.account-check-wrap { display: flex; align-items: center; justify-content: center; min-width: 22px; }
.withdraw-account-check { appearance: auto !important; -webkit-appearance: checkbox !important; -moz-appearance: checkbox !important; display: inline-block !important; width: 18px !important; height: 18px !important; opacity: 1 !important; visibility: visible !important; cursor: pointer; accent-color: #2155d6; position: relative !important; z-index: 99; }

/* Delete Account Button */
.account-item-wrapper { position: relative; display: flex; align-items: center; border-bottom: 1px solid #f0f0f0; }
.account-item-wrapper:last-child { border-bottom: none; }
.account-option { flex: 1; display: flex; align-items: center; justify-content: space-between; padding: 12px 14px; cursor: pointer; transition: background 0.2s ease; }
.account-option:hover { background: #fafcff; transform: none !important; }
.delete-account-btn { background: transparent; border: none; color: #dc3545; cursor: pointer; padding: 8px 12px; margin-right: 8px; border-radius: 6px; transition: all 0.2s ease; opacity: 0.6; }
.delete-account-btn:hover { opacity: 1; background: #ffeaea; transform: none !important; }
.delete-account-btn i { font-size: 14px; }

/* History Table */
.history-table thead th { background: #f7f8fc; border: none; font-size: 12px; }
.history-table tbody td { font-size: 13px; vertical-align: middle; }

/* Bank Form - REMOVED ALL TRANSITIONS to prevent hover movement */
.bank-form-wrapper { 
    border: 1px solid #edf0f5; 
    border-radius: 16px; 
    overflow: hidden; 
    transition: none !important; 
}
.bank-form-wrapper:hover { 
    transform: none !important; 
    box-shadow: none !important; 
}
.bank-form-title { background: #f7f8fc; padding: 14px 18px; font-weight: 700; border-bottom: 1px solid #edf0f5; }

/* Active Account Display */
.active-account-display { background: #eef2ff; border-left: 3px solid #3f5bd8; }

/* Bank/UPI Form Toggle Styles - REMOVED transitions */
#bankFieldsContainer, #upiFieldsContainer {
    transition: none !important;
}

#bankFieldsContainer .form-label,
#upiFieldsContainer .form-label {
    font-weight: 600;
    font-size: 13px;
    margin-bottom: 6px;
    color: #333;
}

#bankFieldsContainer .form-control,
#upiFieldsContainer .form-control {
    border-radius: 10px;
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    font-size: 14px;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

#bankFieldsContainer .form-control:focus,
#upiFieldsContainer .form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59,130,246,0.1);
    outline: none;
    transform: none !important;
}

#bankFieldsContainer small,
#upiFieldsContainer small {
    font-size: 11px;
    margin-top: 5px;
    display: block;
}

/* Filter Styles */
.top-filter-select{
    height: 42px !important;
    border-radius: 12px !important;
    border: 1px solid #dbe4f2 !important;
    font-size: 13px !important;
    font-weight: 600 !important;
    box-shadow: none !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}
.top-filter-select:focus{
    border-color: #2563eb !important;
    box-shadow: 0 0 0 0.15rem rgba(37,99,235,.15) !important;
    transform: none !important;
}
.filter-value-text{
    font-size: 18px;
    font-weight: 700;
    color: #2563eb;
    margin: 0;
}
.mini-box-content.full-width{ width:100%;}

/* Responsive */
@media (max-width: 991px) {
    .payout-card, .settlement-card { height: auto; min-height: auto; }
    .summary-box, .status-box { min-height: auto; }
    #gaugeChart { height: 220px !important; min-height: 220px !important; }
    .settlement-days { margin-top: 0; font-size: 22px; }
    .save-btn { width: 100%; }
    .top-payout-fixed-box { width: 100%; }
    .mini-top-box { width: 100%; min-width: 100%; }
    .payout-tabs { gap: 20px; overflow: auto; white-space: nowrap; }
    .bottom-payout-info { flex-direction: column; align-items: flex-start; }
    .summary-box { margin-bottom: 15px; }
    .export-btn-wrap { flex-direction: column; align-items: stretch; }
    .export-btn { width: 100%; }
    .mini-info-card { margin-bottom: 15px; }
}
@media (max-width: 768px) { 
    .payout-title { font-size: 22px; } 
    .gst-title { font-size: 22px; } 
    .summary-box h4 { font-size: 24px; } 
}
@media (max-width: 576px) { 
    .payout-title { font-size: 22px; } 
    .gst-title { font-size: 22px; } 
    .summary-box h4 { font-size: 24px; } 
}

/* Fix for any remaining hover movements */
* {
    -webkit-tap-highlight-color: transparent;
}

button, a, .btn, .tab-btn, .action-btn, .delete-account-btn {
    transform: translateZ(0);
    backface-visibility: hidden;
}

/* Ensure no transform on any card or container */
.card, .payout-card, .settlement-card, .summary-box, .status-box, 
.mini-info-card, .bank-form-wrapper, .gst-card, .tax-summary-card,
.withdraw-bank-wrapper {
    will-change: auto;
}
</style>


<script>
// ========================================= ALL JAVASCRIPT COMBINED (FIXED) =========================================

document.addEventListener("DOMContentLoaded", function() {

    // ==================== GAUGE CHART ====================
    var chart = echarts.init(document.getElementById('gaugeChart'));
    chart.setOption({
        series: [{
            type: 'gauge',
            startAngle: 220,
            endAngle: -40,
            min: 0,
            max: 10,
            splitNumber: 5,
            axisLine: { lineStyle: { width: 10, color: [[0.3, '#2ecc71'], [0.7, '#f39c12'], [1, '#e74c3c']] } },
            pointer: { show: true, length: '70%', width: 4 },
            axisTick: { distance: -10, length: 6, lineStyle: { color: '#999' } },
            splitLine: { distance: -10, length: 12, lineStyle: { color: '#666', width: 2 } },
            axisLabel: { color: '#666', distance: 15, fontSize: 10 },
            title: { show: true, offsetCenter: [0, '60%'], fontSize: 14, color: '#333', fontWeight: 'bold', text: 'Settlement Delay' },
            detail: { valueAnimation: true, fontSize: 20, offsetCenter: [0, '30%'], formatter: function(value) { return value + ' Days'; }, color: '#000' },
            data: [{ value: {{ $delayDays ?? 0 }} }]
        }]
    });

    // ==================== METHOD TOGGLE (FIXED) ====================
    const methodSelect = document.getElementById('methodSelect');
    const bankFieldsContainer = document.getElementById('bankFieldsContainer');
    const upiFieldsContainer = document.getElementById('upiFieldsContainer');
    const formSaveBtn = document.getElementById('formSaveBtn');

    function toggleMethodFields() {
        const selectedMethod = methodSelect ? methodSelect.value : '';
        
        if(bankFieldsContainer) bankFieldsContainer.style.display = 'none';
        if(upiFieldsContainer) upiFieldsContainer.style.display = 'none';
        
        // Remove required attributes from all fields first
        document.querySelectorAll('#bankForm input, #bankForm select, #bankForm textarea').forEach(field => {
            if(field.name !== 'method') {
                field.removeAttribute('required');
            }
        });
        
        if(selectedMethod === 'bank') {
            if(bankFieldsContainer) bankFieldsContainer.style.display = 'block';
            // Add required attributes for bank fields
            const bankFields = ['bank_name', 'account_name', 'account_number', 'ifsc'];
            bankFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if(field && field.closest('#bankFieldsContainer')) {
                    field.setAttribute('required', 'required');
                }
            });
            if(formSaveBtn) formSaveBtn.innerHTML = '<i class="fa fa-save me-1"></i> Save Bank Account';
        } 
        else if(selectedMethod === 'upi') {
            if(upiFieldsContainer) upiFieldsContainer.style.display = 'block';
            // Add required attributes for UPI fields
            const upiField = document.querySelector('[name="upi"]');
            if(upiField) upiField.setAttribute('required', 'required');
            if(formSaveBtn) formSaveBtn.innerHTML = '<i class="fa fa-save me-1"></i> Save UPI Account';
        }
        else {
            if(formSaveBtn) formSaveBtn.innerHTML = '<i class="fa fa-save me-1"></i> Save';
        }
    }

    if(methodSelect) {
        toggleMethodFields();
        methodSelect.addEventListener('change', toggleMethodFields);
    }

    // ==================== BANK FORM VALIDATION & SUBMIT (FIXED) ====================
    const bankForm = document.getElementById('bankForm');
    if(bankForm) {
        bankForm.addEventListener('submit', function(e) {
            const selectedMethod = methodSelect ? methodSelect.value : '';
            
            console.log("Form submitted with method:", selectedMethod); // Debug
            
            if(selectedMethod === 'bank') {
                let bankName = document.querySelector('[name="bank_name"]')?.value;
                let accountName = document.querySelector('#bankFieldsContainer [name="account_name"]')?.value;
                let accountNumber = document.querySelector('[name="account_number"]')?.value;
                let ifsc = document.querySelector('[name="ifsc"]')?.value;
                
                console.log("Bank Data:", {bankName, accountName, accountNumber, ifsc}); // Debug
                
                if(!bankName || bankName.trim() === '') {
                    alert('Please enter Bank Name');
                    e.preventDefault();
                    return false;
                }
                if(!accountName || accountName.trim() === '') {
                    alert('Please enter Account Holder Name');
                    e.preventDefault();
                    return false;
                }
                if(!accountNumber || accountNumber.trim() === '') {
                    alert('Please enter Account Number');
                    e.preventDefault();
                    return false;
                }
                if(!/^[0-9]{8,18}$/.test(accountNumber)) {
                    alert('Invalid Account Number (must be 8-18 digits)');
                    e.preventDefault();
                    return false;
                }
                if(!ifsc || ifsc.trim() === '') {
                    alert('Please enter IFSC Code');
                    e.preventDefault();
                    return false;
                }
                if(!/^[A-Z]{4}0[A-Z0-9]{6}$/.test(ifsc.toUpperCase())) {
                    alert('Invalid IFSC Code format (e.g., SBIN0001234)');
                    e.preventDefault();
                    return false;
                }
            }
            
            if(selectedMethod === 'upi') {
                let upiAccountName = document.querySelector('[name="account_name_upi"]')?.value;
                let upiId = document.querySelector('[name="upi"]')?.value;
                
                console.log("UPI Data:", {upiAccountName, upiId}); // Debug
                
                if(!upiId || upiId.trim() === '') {
                    alert('Please enter UPI ID');
                    e.preventDefault();
                    return false;
                }
                if(!/^[\w.-]+@[\w.-]+$/.test(upiId)) {
                    alert('Invalid UPI ID format (example: username@bankname)');
                    e.preventDefault();
                    return false;
                }
                
                // For UPI, we need to rename account_name_upi to account_name for the controller
                // Create a hidden input with the correct name
                let hiddenAccountName = document.getElementById('hiddenAccountName');
                if(!hiddenAccountName) {
                    hiddenAccountName = document.createElement('input');
                    hiddenAccountName.type = 'hidden';
                    hiddenAccountName.id = 'hiddenAccountName';
                    hiddenAccountName.name = 'account_name';
                    this.appendChild(hiddenAccountName);
                }
                hiddenAccountName.value = upiAccountName || '';
            }
            
            if(!selectedMethod || selectedMethod === '') {
                alert('Please select a method (Bank or UPI)');
                e.preventDefault();
                return false;
            }
            
            // Show loading state but don't disable form submission
            const submitBtn = document.querySelector('#formSaveBtn');
            if(submitBtn) {
                submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin me-1"></i> Saving...';
                submitBtn.disabled = true;
            }
            
            // Allow form to submit
            return true;
        });
    }

    // ==================== CURRENCY SETUP ====================
    let currency = localStorage.getItem('currency') || 'INR';
    const rates = { INR: 1, USD: 0.012, EUR: 0.011 };
    const symbols = { INR: '₹', USD: '$', EUR: '€' };
    const switcher = document.getElementById('currencySwitcher');

    function formatMoney(val) { return new Intl.NumberFormat('en-IN', { minimumFractionDigits: 2 }).format(val); }
    function updateCurrency() {
        document.querySelectorAll('.money').forEach(el => {
            let amount = parseFloat(el.dataset.amount || 0);
            let val = amount * rates[currency];
            el.innerHTML = symbols[currency] + ' ' + formatMoney(val);
        });
    }
    function updateMinWithdraw() {
        let baseAmount = 100, converted = baseAmount * rates[currency];
        let el = document.getElementById('minWithdrawText');
        if(el) el.innerHTML = "Minimum withdrawal " + symbols[currency] + " " + formatMoney(converted);
    }
    if(switcher) {
        switcher.value = currency;
        switcher.addEventListener('change', function() {
            currency = this.value;
            localStorage.setItem('currency', currency);
            updateCurrency();
            updateMinWithdraw();
        });
    }
    updateCurrency();
    updateMinWithdraw();

    // ==================== WITHDRAW ALERT ====================
    const withdrawForm = document.querySelector('form[action*="withdraw"]');
    if(withdrawForm) {
        withdrawForm.addEventListener('submit', function(e) {
            let amount = parseFloat(document.querySelector('[name="amount"]')?.value || 0);
            let balanceEl = document.querySelector('.money');
            let balance = balanceEl ? parseFloat(balanceEl.dataset.amount) : 0;
            if(amount > balance) { alert("❌ Not enough balance"); e.preventDefault(); return; }
            if(amount < 100) { alert("Minimum withdrawal is 100"); e.preventDefault(); return; }
        });
    }

    // ==================== PAYOUT MODE TOGGLE ====================
    const payoutMode = document.getElementById('payoutMode');
    const manualCycle = document.getElementById('manualCycle');
    const nextPayoutDate = document.getElementById('manualNextDate');

    function togglePayoutFields() {
        if(payoutMode && payoutMode.value === 'auto') {
            if(manualCycle) { manualCycle.setAttribute('readonly', true); manualCycle.classList.add('bg-light'); }
            if(nextPayoutDate) { nextPayoutDate.setAttribute('readonly', true); nextPayoutDate.classList.add('bg-light'); }
        } else {
            if(manualCycle) { manualCycle.removeAttribute('readonly'); manualCycle.classList.remove('bg-light'); }
            if(nextPayoutDate) { nextPayoutDate.removeAttribute('readonly'); nextPayoutDate.classList.remove('bg-light'); }
        }
    }
    if(payoutMode) { togglePayoutFields(); payoutMode.addEventListener('change', togglePayoutFields); }

    // ==================== TABS ====================
    const tabs = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            tabs.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            let type = this.dataset.tab;
            document.querySelectorAll('.tab-row').forEach(row => row.classList.add('d-none'));
            if(type === 'upcoming') document.querySelectorAll('.upcoming-row').forEach(row => row.classList.remove('d-none'));
            if(type === 'completed') document.querySelectorAll('.completed-row').forEach(row => row.classList.remove('d-none'));
            if(type === 'failed') document.querySelectorAll('.failed-row').forEach(row => row.classList.remove('d-none'));
        });
    });

    // ==================== SELECT ALL ACCOUNTS ====================
    const selectAll = document.getElementById('selectAllWithdrawAccounts');
    const checks = document.querySelectorAll('.withdraw-account-check');
    const countBox = document.getElementById('selectedAccountCount');

    function updateSelectedCount() {
        let checked = document.querySelectorAll('.withdraw-account-check:checked').length;
        if(countBox) countBox.innerText = checked;
    }
    if(selectAll) {
        selectAll.addEventListener('change', function() {
            checks.forEach(check => { if(check) check.checked = this.checked; });
            updateSelectedCount();
        });
    }
    checks.forEach(check => {
        if(check) {
            check.addEventListener('change', function() {
                updateSelectedCount();
                let total = checks.length;
                let checked = document.querySelectorAll('.withdraw-account-check:checked').length;
                if(selectAll) selectAll.checked = total === checked;
            });
        }
    });

    // ==================== ACCOUNT MANAGEMENT ====================
    let activeAccount = null;
    const bankFormWrapper = document.querySelector('.bank-form-wrapper');
    const bankFormElem = document.getElementById('bankForm');
    const accountNumberInput = document.querySelector('[name="account_number"]');
    const ifscInput = document.querySelector('[name="ifsc"]');
    const upiInput = document.querySelector('[name="upi"]');
    const cancelChequeInput = document.querySelector('[name="cancel_cheque"]');
    const saveBtn = document.querySelector('#formSaveBtn');

    const displayBankName = document.getElementById('displayBankName');
    const displayAccountHolder = document.getElementById('displayAccountHolder');
    const displayAccountNumber = document.getElementById('displayAccountNumber');
    const displayIfsc = document.getElementById('displayIfsc');
    const displayMethod = document.getElementById('displayMethod');
    const displayUpi = document.getElementById('displayUpi');
    const verifyBadge = document.getElementById('accountVerifyBadge');
    const upiDisplayWrap = document.getElementById('upiDisplayWrap');

    function updateDisplay(account) {
        if(displayBankName) displayBankName.innerHTML = account.bank_name || '-';
        if(displayAccountHolder) displayAccountHolder.innerHTML = account.account_name || '-';
        if(displayIfsc) displayIfsc.innerHTML = account.ifsc_code || '-';
        if(displayMethod) displayMethod.innerHTML = (account.method || '').toUpperCase();
        if(verifyBadge) verifyBadge.innerHTML = account.status || 'Verified';
        if(displayAccountNumber) {
            if(account.account_number) {
                let accNum = account.account_number.toString();
                if(accNum.length > 4) displayAccountNumber.innerHTML = 'XXXX XXXX ' + accNum.slice(-4);
                else displayAccountNumber.innerHTML = accNum;
            } else { displayAccountNumber.innerHTML = '-'; }
        }
        if(upiDisplayWrap) {
            if(account.method === 'upi') {
                upiDisplayWrap.style.display = 'block';
                if(displayUpi) displayUpi.innerHTML = account.upi_id || '-';
            } else { upiDisplayWrap.style.display = 'none'; }
        }
    }

    function fillFormWithAccountData(account) {
        if(methodSelect) methodSelect.value = account.method || '';
        if(methodSelect) methodSelect.dispatchEvent(new Event('change', { bubbles: true }));
        setTimeout(() => {
            if(account.method === 'bank') {
                // Clear UPI fields
                if(upiInput) upiInput.value = '';
                // Set bank fields
                const bankNameInput = document.querySelector('[name="bank_name"]');
                const accountNameInput = document.querySelector('#bankFieldsContainer [name="account_name"]');
                if(bankNameInput) bankNameInput.value = account.bank_name || '';
                if(accountNameInput) accountNameInput.value = account.account_name || '';
                if(accountNumberInput) accountNumberInput.value = account.account_number || '';
                if(ifscInput) ifscInput.value = account.ifsc_code || '';
            } else if(account.method === 'upi') {
                // Clear bank fields
                const bankNameInput = document.querySelector('[name="bank_name"]');
                if(bankNameInput) bankNameInput.value = '';
                if(accountNumberInput) accountNumberInput.value = '';
                if(ifscInput) ifscInput.value = '';
                // Set UPI fields
                const upiAccountNameInput = document.querySelector('[name="account_name_upi"]');
                if(upiAccountNameInput) upiAccountNameInput.value = account.account_name || '';
                if(upiInput) upiInput.value = account.upi_id || '';
            }
        }, 50);
    }

    function handleAccountClick(accountElement) {
        const accountData = {
            id: accountElement.dataset.id,
            method: accountElement.dataset.method,
            bank_name: accountElement.dataset.bank_name,
            account_name: accountElement.dataset.account_name,
            account_number: accountElement.dataset.account_number,
            ifsc_code: accountElement.dataset.ifsc,
            upi_id: accountElement.dataset.upi,
            status: accountElement.dataset.status || 'Verified'
        };
        activeAccount = accountData;
        document.querySelectorAll('.withdraw-select-account').forEach(el => el.classList.remove('active-account-display'));
        accountElement.classList.add('active-account-display');
        updateDisplay(activeAccount);
    }

    const accountOptions = document.querySelectorAll('.withdraw-select-account');
    accountOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            if(e.target.type !== 'checkbox') handleAccountClick(this);
        });
    });

    const accountChecks = document.querySelectorAll('.withdraw-account-check');
    accountChecks.forEach(check => {
        check.addEventListener('change', function() {
            const parentOption = this.closest('.withdraw-select-account');
            if(parentOption && this.checked) handleAccountClick(parentOption);
        });
    });

    // EDIT BUTTON
    const editBtn = document.getElementById('editAccountBtn');
    if(editBtn) {
        editBtn.addEventListener('click', function() {
            if(!activeAccount) { alert('Please select an account first'); return; }
            if(bankFormWrapper) bankFormWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            fillFormWithAccountData(activeAccount);
            if(saveBtn) saveBtn.innerHTML = '<i class="fa fa-save me-1"></i> Update Account';
            let old = document.getElementById('editAccountId');
            if(old) old.remove();
            let hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'account_id';
            hidden.id = 'editAccountId';
            hidden.value = activeAccount.id;
            if(bankFormElem) bankFormElem.appendChild(hidden);
        });
    }

    // REVERIFY BUTTON
    const reverifyBtn = document.getElementById('reverifyBtn');
    if(reverifyBtn) {
        reverifyBtn.addEventListener('click', function() {
            if(!activeAccount) { alert('Please select an account first'); return; }
            if(bankFormWrapper) bankFormWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            fillFormWithAccountData(activeAccount);
            if(saveBtn) saveBtn.innerHTML = '<i class="fa fa-shield me-1"></i> Reverify Account';
            let old = document.getElementById('editAccountId');
            if(old) old.remove();
            let reverifyHidden = document.getElementById('reverifyHidden');
            if(!reverifyHidden) {
                reverifyHidden = document.createElement('input');
                reverifyHidden.type = 'hidden';
                reverifyHidden.name = 'reverify_request';
                reverifyHidden.id = 'reverifyHidden';
                reverifyHidden.value = '1';
                if(bankFormElem) bankFormElem.appendChild(reverifyHidden);
            }
            let accIdHidden = document.createElement('input');
            accIdHidden.type = 'hidden';
            accIdHidden.name = 'account_id';
            accIdHidden.id = 'reverifyAccountId';
            accIdHidden.value = activeAccount.id;
            if(bankFormElem) bankFormElem.appendChild(accIdHidden);
        });
    }

    // ADD NEW ACCOUNT BUTTON
    const addAccountBtn = document.getElementById('addAccountBtn');
    if(addAccountBtn) {
        addAccountBtn.addEventListener('click', function() {
            if(bankFormWrapper) bankFormWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
            if(bankFormElem) bankFormElem.reset();
            if(methodSelect) methodSelect.value = '';
            toggleMethodFields();
            if(saveBtn) saveBtn.innerHTML = '<i class="fa fa-save me-1"></i> Save Account';
            let oldEdit = document.getElementById('editAccountId');
            if(oldEdit) oldEdit.remove();
            let oldReverify = document.getElementById('reverifyHidden');
            if(oldReverify) oldReverify.remove();
            let oldReverifyAcc = document.getElementById('reverifyAccountId');
            if(oldReverifyAcc) oldReverifyAcc.remove();
            let hiddenAccountName = document.getElementById('hiddenAccountName');
            if(hiddenAccountName) hiddenAccountName.remove();
            if(cancelChequeInput) cancelChequeInput.value = '';
            document.querySelectorAll('.withdraw-select-account').forEach(el => el.classList.remove('active-account-display'));
            activeAccount = null;
        });
    }

    // ==================== DELETE ACCOUNT ====================
    function deleteAccount(accountId, accountName) {
        if(confirm(`Are you sure you want to delete account "${accountName}"?`)) {
            let deleteBtn = document.querySelector(`.delete-account-btn[data-account-id="${accountId}"]`);
            if(!deleteBtn) deleteBtn = document.querySelector(`.delete-account-btn[data-id="${accountId}"]`);
            if(deleteBtn) { deleteBtn.innerHTML = '<i class="fa fa-spinner fa-spin"></i>'; deleteBtn.disabled = true; }
            const deleteUrl = `{{ route("seller.bank.delete", "") }}/${accountId}`;
            fetch(deleteUrl, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    const wrapper = document.querySelector(`.delete-account-btn[data-account-id="${accountId}"]`)?.closest('.account-item-wrapper');
                    if(wrapper) wrapper.remove();
                    updateSelectedCount();
                    const remainingAccounts = document.querySelectorAll('.account-item-wrapper').length;
                    if(remainingAccounts === 0) {
                        const container = document.getElementById('accountsListContainer');
                        if(container) container.innerHTML = '<div class="text-muted small p-3 text-center"><i class="fa fa-info-circle me-1"></i>No bank accounts available. Please add an account.</div>';
                        if(displayBankName) displayBankName.innerHTML = '-';
                        if(displayAccountHolder) displayAccountHolder.innerHTML = '-';
                        if(displayAccountNumber) displayAccountNumber.innerHTML = '-';
                        if(displayIfsc) displayIfsc.innerHTML = '-';
                        if(displayMethod) displayMethod.innerHTML = '-';
                        if(displayUpi) displayUpi.innerHTML = '-';
                        if(upiDisplayWrap) upiDisplayWrap.style.display = 'none';
                    }
                    alert(data.message);
                } else { alert(data.message); if(deleteBtn) { deleteBtn.innerHTML = '<i class="fa fa-trash-alt"></i>'; deleteBtn.disabled = false; } }
            })
            .catch(error => { console.error('Error:', error); alert('Something went wrong. Please try again.'); if(deleteBtn) { deleteBtn.innerHTML = '<i class="fa fa-trash-alt"></i>'; deleteBtn.disabled = false; } });
        }
    }

    document.querySelectorAll('.delete-account-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const accountId = this.dataset.accountId || this.dataset.id;
            const accountName = this.dataset.accountName || this.dataset.name;
            deleteAccount(accountId, accountName);
        });
    });

    // ==================== PAYOUT DATE SYNC (NEW FEATURE) ====================
    const manualNextDateInput = document.getElementById('manualNextDate');
    const headerNextPayout = document.getElementById('headerNextPayoutDate');
    const summaryNextPayout = document.getElementById('summaryNextPayout');
    const bottomNextPayoutDate = document.getElementById('bottomNextPayoutDate');
    const payoutSettingsForm = document.getElementById('payoutSettingsForm');

    function formatDateToDisplay(dateString) {
        if (!dateString) return '10 June 2026';
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return '10 June 2026';
        return date.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' }).replace(/ /g, ' ');
    }

    function updateAllPayoutDates(dateValue) {
        const displayDate = formatDateToDisplay(dateValue);
        if (headerNextPayout) headerNextPayout.innerText = displayDate;
        if (summaryNextPayout) summaryNextPayout.innerText = displayDate;
        if (bottomNextPayoutDate) bottomNextPayoutDate.innerText = displayDate;
    }

    if (manualNextDateInput) {
        // Initial sync on page load
        updateAllPayoutDates(manualNextDateInput.value);
        
        // Sync when date changes
        manualNextDateInput.addEventListener('change', function() {
            updateAllPayoutDates(this.value);
        });
    }

    // Also update when form is submitted (in case server updates the date)
    if (payoutSettingsForm) {
        payoutSettingsForm.addEventListener('submit', function() {
            if (manualNextDateInput) {
                const submitDate = manualNextDateInput.value;
                if (submitDate) {
                    updateAllPayoutDates(submitDate);
                }
            }
        });
    }

});

// ==================== GO BACK FUNCTION ====================
function goBack() { window.history.back(); }

// ==================== FILTER SCRIPT ====================
document.addEventListener('DOMContentLoaded', function () {
    const countryFilter  = document.getElementById('countryFilter');
    const scheduleFilter = document.getElementById('scheduleFilter');
    const currencyFilter = document.getElementById('currencySwitcher');

    function applyFilters() {
        let url = new URL(window.location.href);
        if(countryFilter && countryFilter.value !== '') {
            url.searchParams.set('country', countryFilter.value);
        } else {
            url.searchParams.delete('country');
        }
        if(scheduleFilter && scheduleFilter.value !== '') {
            url.searchParams.set('schedule', scheduleFilter.value);
        } else {
            url.searchParams.delete('schedule');
        }
        if(currencyFilter && currencyFilter.value !== '') {
            url.searchParams.set('currency', currencyFilter.value);
        } else {
            url.searchParams.delete('currency');
        }
        window.location.href = url.toString();
    }

    if(countryFilter) countryFilter.addEventListener('change', applyFilters);
    if(scheduleFilter) scheduleFilter.addEventListener('change', applyFilters);
    if(currencyFilter) currencyFilter.addEventListener('change', applyFilters);
});
</script>

@endsection