@extends('layouts.vertical', ['title' => 'Wallet Center'])

@section('content')
<style>
    .bg-primary {
        background: #004392 !important;
    }
</style>

<div class="container-fluid">

    
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-flex align-items-center justify-content-between">
                <div>
                    <h4 class="page-title mb-0 fw-bold" id="page-heading">Wallet Dashboard</h4>
                </div>
               
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         TAB NAVIGATION
    ══════════════════════════════════════════════════════════════ --}}
    <ul class="nav wallet-nav-tabs mb-4" id="walletTabs" role="tablist">
        <li class="nav-item"><button class="nav-link active" id="tab-dashboard" data-bs-toggle="tab" data-bs-target="#pane-dashboard" type="button" data-title="Wallet Dashboard"><i class="mdi mdi-view-dashboard-outline me-1"></i> Dashboard</button></li>
        <li class="nav-item"><button class="nav-link" id="tab-transactions" data-bs-toggle="tab" data-bs-target="#pane-transactions" type="button" data-title="Wallet Transactions"><i class="mdi mdi-swap-horizontal-bold me-1"></i> Transactions</button></li>
        <li class="nav-item"><button class="nav-link" id="tab-withdrawals" data-bs-toggle="tab" data-bs-target="#pane-withdrawals" type="button" data-title="Withdrawal Management"><i class="mdi mdi-bank-transfer-out me-1"></i> Withdrawals</button></li>
        <li class="nav-item"><button class="nav-link" id="tab-refunds" data-bs-toggle="tab" data-bs-target="#pane-refunds" type="button" data-title="Buyer Refunds"><i class="mdi mdi-cash-refund me-1"></i> Refunds</button></li>
        <li class="nav-item"><button class="nav-link" id="tab-controls" data-bs-toggle="tab" data-bs-target="#pane-controls" type="button" data-title="Wallet Controls"><i class="mdi mdi-cog-outline me-1"></i> Controls</button></li>
        <li class="nav-item"><button class="nav-link" id="tab-salary" data-bs-toggle="tab" data-bs-target="#pane-salary" type="button" data-title="Team Salary & Payouts"><i class="mdi mdi-currency-usd me-1"></i> Salary & Payouts</button></li>
    </ul>

    {{-- ══════════════════════════════════════════════════════════════
         TAB CONTENT
    ══════════════════════════════════════════════════════════════ --}}
    <div class="tab-content" id="walletTabContent">

        {{-- DASHBOARD TAB --}}
        <div class="tab-pane fade show active" id="pane-dashboard" role="tabpanel">
            <div class="row g-3 mb-3">
                <div class="col-xl-4 col-md-6"><div class="wallet-stat-card stat-total"><div class="stat-icon-wrap"><div class="stat-icon"><i class="mdi mdi-wallet-outline"></i></div></div><div class="stat-info"><span class="stat-label">Total Balance</span><h3 class="stat-value money" id="totalBalance" data-amount="{{ $totalBalance }}">₹{{ number_format($totalBalance, 2) }}</h3></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="wallet-stat-card stat-seller"><div class="stat-icon-wrap"><div class="stat-icon"><i class="mdi mdi-store-outline"></i></div></div><div class="stat-info"><span class="stat-label">Seller Balance</span><h3 class="stat-value money" id="sellerBalance" data-amount="{{ $sellerBalance }}">₹{{ number_format($sellerBalance, 2) }}</h3></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="wallet-stat-card stat-buyer"><div class="stat-icon-wrap"><div class="stat-icon"><i class="mdi mdi-account-cash-outline"></i></div></div><div class="stat-info"><span class="stat-label">Buyer Balance</span><h3 class="stat-value money" id="buyerBalance" data-amount="{{ $buyerBalance }}">₹{{ number_format($buyerBalance, 2) }}</h3></div></div></div>
            </div>
            <div class="row g-3 mb-4">
                <div class="col-xl-4 col-md-6"><div class="wallet-stat-card stat-frozen"><div class="stat-icon-wrap"><div class="stat-icon"><i class="mdi mdi-snowflake"></i></div></div><div class="stat-info"><span class="stat-label">Frozen Amount</span><h3 class="stat-value money" id="frozenAmount" data-amount="{{ $frozenAmount }}">₹{{ number_format($frozenAmount, 2) }}</h3></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="wallet-stat-card stat-pending"><div class="stat-icon-wrap"><div class="stat-icon"><i class="mdi mdi-clock-outline"></i></div></div><div class="stat-info"><span class="stat-label">Pending Withdrawals</span><h3 class="stat-value money" id="pendingWithdrawalsAmount" data-amount="{{ $pendingWithdrawals }}">₹{{ number_format($pendingWithdrawals, 2) }}</h3><span class="stat-badge" id="pendingWithdrawalsCount">{{ $pendingWithdrawalsCount }} Requests</span></div></div></div>
                <div class="col-xl-4 col-md-6"><div class="wallet-stat-card" style="background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); color: #fff;"><div class="stat-icon-wrap"><div class="stat-icon" style="background: rgba(255,255,255,0.2);"><i class="mdi mdi-bank" style="color: #fff;"></i></div></div><div class="stat-info"><span class="stat-label" style="color: rgba(255,255,255,0.8);">Platform Wallet</span><h3 class="stat-value money" id="platformBalance" style="color: #fff;" data-amount="{{ $platformBalance ?? 0 }}">₹{{ number_format($platformBalance ?? 0, 2) }}</h3><span class="stat-badge" style="background: rgba(255,255,255,0.2); color: #fff;">Commission Earnings</span></div></div></div>
            </div>
            <div class="row g-3"><div class="col-xl-5 col-lg-6"><div class="card wallet-card h-100"><div class="card-header"><h5 class="card-title mb-0">Wallet Summary Overview</h5></div><div class="card-body"><div id="walletDonutChart" style="width:100%;min-height:320px;"></div></div></div></div>
            <div class="col-xl-7 col-lg-6"><div class="card wallet-card h-100"><div class="card-header"><h5 class="card-title mb-0">Wallet Balance Trend (Last 7 Days)</h5></div><div class="card-body"><div id="walletTrendChart" style="width:100%;min-height:320px;"></div></div></div></div></div>
        </div>

        {{-- TRANSACTIONS TAB --}}
        <div class="tab-pane fade" id="pane-transactions" role="tabpanel">
            <div class="card wallet-card">
                <div class="card-header pb-0"><div class="row g-2 align-items-end mb-3">
                    <div class="col-md-2"><label class="form-label small mb-1">User Type</label><select class="form-select form-select-sm" id="filterUserType"><option value="">All</option><option value="seller">Seller</option><option value="customer">Buyer</option></select></div>
                    <div class="col-md-2"><label class="form-label small mb-1">User</label><select class="form-select form-select-sm" id="filterUserId"><option value="">All Users</option></select></div>
                    <div class="col-md-2"><label class="form-label small mb-1">Transaction Type</label><select class="form-select form-select-sm" id="filterTransactionType"><option value="">All</option><option value="payout">Payout</option><option value="refund">Refund</option><option value="commission">Commission</option><option value="add_money">Add Money</option><option value="hold">Hold</option><option value="payment">Payment</option><option value="manual_credit">Manual Credit</option><option value="manual_debit">Manual Debit</option></select></div>
                    <div class="col-md-3"><label class="form-label small mb-1">Date Range</label><div class="d-flex gap-1"><input type="date" class="form-control form-control-sm" id="startDate" value="{{ date('Y-m-d', strtotime('-30 days')) }}"><input type="date" class="form-control form-control-sm" id="endDate" value="{{ date('Y-m-d') }}"></div></div>
                    <div class="col-md-3"><div class="d-flex gap-2"><button type="button" class="btn btn-primary btn-sm px-3" id="filterBtn"><i class="mdi mdi-filter-outline me-1"></i>Filter</button><button type="button" class="btn btn-outline-secondary btn-sm px-3" id="resetFilterBtn">Reset</button></div></div>
                </div></div>
                <div class="card-body pt-2"><div class="table-responsive"><table class="table table-hover table-sm align-middle wallet-table"><thead><tr><th>Date & Time</th><th>User</th><th>User Type</th><th>Type</th><th>Credit <span class="currency-label">(₹)</span></th><th>Debit <span class="currency-label">(₹)</span></th><th>Balance <span class="currency-label">(₹)</span></th><th>Description</th><th>Reference ID</th></tr></thead><tbody id="transactionsTableBody">@foreach($transactions as $transaction)</td><td class="text-nowrap"><small>{{ $transaction->created_at->format('d M Y h:i A') }}</small></td><td>{{ $transaction->user_name }}</td><td><span class="badge bg-soft-{{ $transaction->user_type == 'seller' ? 'primary' : 'success' }}">{{ ucfirst($transaction->user_type) }}</span></td><td><span class="badge bg-soft-{{ $transaction->badge_color }}">{{ ucfirst(str_replace('_', ' ', $transaction->transaction_type)) }}</span></td><td>@if($transaction->credit > 0)<span class="text-success fw-semibold money" data-amount="{{ $transaction->credit }}">₹{{ number_format($transaction->credit, 2) }}</span>@else-@endif</td><td>@if($transaction->debit > 0)<span class="text-danger fw-semibold money" data-amount="{{ $transaction->debit }}">₹{{ number_format($transaction->debit, 2) }}</span>@else-@endif</td><td><span class="money" data-amount="{{ $transaction->balance_after }}">₹{{ number_format($transaction->balance_after, 2) }}</span></td><td>{{ $transaction->description }}</td><td><code>{{ $transaction->reference_id ?? 'N/A' }}</code></td>
                </tr>@endforeach</tbody></table></div>
                <div class="d-flex justify-content-between align-items-center mt-3"><small class="text-muted" id="transactionInfo">Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() ?? 0 }} entries</small><nav><ul class="pagination pagination-sm mb-0" id="transactionPagination">{{ $transactions->links('pagination::bootstrap-4') }}</ul></nav></div></div>
            </div>
        </div>

        {{-- WITHDRAWAL MANAGEMENT TAB --}}
        <div class="tab-pane fade" id="pane-withdrawals" role="tabpanel">
            <div class="card wallet-card"><div class="card-header"><ul class="nav nav-tabs card-header-tabs withdrawal-sub-tabs" id="withdrawalTabs"><li class="nav-item"><button class="nav-link active" data-status="pending" data-bs-toggle="tab" data-bs-target="#wd-pending">Pending <span class="badge bg-danger ms-1" id="pendingBadge">{{ $pendingWithdrawalsCount }}</span></button></li><li class="nav-item"><button class="nav-link" data-status="approved" data-bs-toggle="tab" data-bs-target="#wd-approved">Approved</button></li><li class="nav-item"><button class="nav-link" data-status="rejected" data-bs-toggle="tab" data-bs-target="#wd-rejected">Rejected</button></li></ul></div>
            <div class="card-body"><div class="tab-content"><div class="tab-pane fade show active" id="wd-pending"><div class="table-responsive"><table class="table table-hover table-sm align-middle wallet-table"><thead><tr><th>Request ID</th><th>User</th><th>Type</th><th>Amount</th><th>Requested Date</th><th>Bank Details</th><th>Status</th><th>Actions</th></tr></thead><tbody id="pendingWithdrawalsBody">@foreach($pendingWithdrawalsList as $withdrawal)<tr><td><code>{{ $withdrawal->request_id }}</code></td><td>{{ $withdrawal->user_name }}</td><td><span class="badge bg-soft-primary">{{ ucfirst($withdrawal->user_type) }}</span></td><td class="fw-semibold"><span class="money" data-amount="{{ $withdrawal->amount }}">₹{{ number_format($withdrawal->amount, 2) }}</span></td><td><small>{{ $withdrawal->created_at->format('d M Y h:i A') }}</small></td><td><small>{{ $withdrawal->bank_name }}<br>{{ $withdrawal->bank_account_number }}</small></td><td><span class="badge bg-soft-warning">Pending</span></td><td><div class="d-flex gap-1"><button class="btn btn-sm btn-success approve-withdrawal" data-id="{{ $withdrawal->id }}">Approve</button><button class="btn btn-sm btn-danger reject-withdrawal" data-id="{{ $withdrawal->id }}">Reject</button></div></td>
                </tr>@endforeach</tbody></table></div><div class="d-flex justify-content-between mt-3"><small class="text-muted" id="pendingWithdrawalInfo">Showing {{ $pendingWithdrawalsList->firstItem() ?? 0 }} to {{ $pendingWithdrawalsList->lastItem() ?? 0 }} of {{ $pendingWithdrawalsList->total() ?? 0 }} entries</small><nav><ul class="pagination pagination-sm mb-0" id="pendingWithdrawalPagination">{{ $pendingWithdrawalsList->links('pagination::bootstrap-4') }}</ul></nav></div></div>
            <div class="tab-pane fade" id="wd-approved"><div class="table-responsive"><table class="table table-hover table-sm align-middle wallet-table"><thead><tr><th>Request ID</th><th>User</th><th>Type</th><th>Amount</th><th>Requested Date</th><th>Approved Date</th><th>Bank Details</th><th>Status</th></tr></thead><tbody id="approvedWithdrawalsBody"><tr><td colspan="8" class="text-center py-5 text-muted"><i class="mdi mdi-check-circle-outline" style="font-size:48px"></i><h5>Loading...</h5></td></tr></tbody></table></div></div>
            <div class="tab-pane fade" id="wd-rejected"><div class="table-responsive"><table class="table table-hover table-sm align-middle wallet-table"><thead><tr><th>Request ID</th><th>User</th><th>Type</th><th>Amount</th><th>Requested Date</th><th>Rejected Date</th><th>Bank Details</th><th>Status</th></tr></thead><tbody id="rejectedWithdrawalsBody"><tr><td colspan="8" class="text-center py-5 text-muted"><i class="mdi mdi-close-circle-outline" style="font-size:48px"></i><h5>Loading...</h5></td></tr></tbody></table></div></div>
            </div></div></div>
        </div>

        {{-- BUYER REFUNDS TAB --}}
        <div class="tab-pane fade" id="pane-refunds" role="tabpanel">
            <div class="card wallet-card"><div class="card-header"><div class="d-flex justify-content-between"><h5 class="card-title mb-0">Buyer Refunds Management</h5><button class="btn btn-sm btn-outline-secondary" id="exportRefundsBtn"><i class="mdi mdi-export me-1"></i> Export</button></div></div>
            <div class="card-header pb-0 border-bottom-0"><div class="row g-2 align-items-end mb-3"><div class="col-md-2"><label class="form-label small mb-1">Status</label><select class="form-select form-select-sm" id="refundStatusFilter"><option value="">All</option><option value="pending">Pending</option><option value="approved">Approved</option><option value="rejected">Rejected</option><option value="refunded">Refunded</option></select></div>
            <div class="col-md-2"><label class="form-label small mb-1">Buyer</label><select class="form-select form-select-sm" id="refundBuyerFilter"><option value="">All Buyers</option>@foreach($buyers as $buyer)<option value="{{ $buyer->id }}">{{ $buyer->name }}</option>@endforeach</select></div>
            <div class="col-md-2"><label class="form-label small mb-1">Order ID</label><input type="text" class="form-control form-control-sm" id="refundOrderFilter" placeholder="Search Order ID"></div>
            <div class="col-md-3"><label class="form-label small mb-1">Date Range</label><div class="d-flex gap-1"><input type="date" class="form-control form-control-sm" id="refundStartDate" value="{{ date('Y-m-d', strtotime('-30 days')) }}"><input type="date" class="form-control form-control-sm" id="refundEndDate" value="{{ date('Y-m-d') }}"></div></div>
            <div class="col-md-3"><div class="d-flex gap-2"><button class="btn btn-primary btn-sm" id="filterRefundsBtn"><i class="mdi mdi-filter-outline me-1"></i>Filter</button><button class="btn btn-outline-secondary btn-sm" id="resetRefundsFilterBtn">Reset</button></div></div></div></div>
            <div class="card-body pt-2"><div class="table-responsive"><table class="table table-hover table-sm align-middle wallet-table"><thead><tr><th><input type="checkbox" id="selectAllRefunds"></th><th>Order ID</th><th>Buyer ID</th><th>Buyer Name</th><th>Amount <span class="currency-label">(₹)</span></th><th>Reason</th><th>Request Date</th><th>Status</th><th>Actions</th></tr></thead><tbody id="refundsTableBody">
            @forelse($refundsData as $refund)
            <tr>
                <td><input type="checkbox" class="refund-checkbox" data-id="{{ $refund->id }}"></td>
                <td><code class="order-id-clickable" data-order-id="{{ $refund->order_id }}" data-refund-id="{{ $refund->id }}" style="cursor:pointer; color:#4e73df; text-decoration:underline;">#{{ $refund->order_id }}</code></td>
                <td><code>#CUST-{{ str_pad($refund->customer_id, 3, '0', STR_PAD_LEFT) }}</code></td>
                <td>{{ $refund->buyer_name ?? 'Unknown' }}</td>
                <td class="fw-semibold"><span class="money" data-amount="{{ $refund->total_amount }}">₹{{ number_format($refund->total_amount, 2) }}</span></td>
                <td><span class="badge bg-soft-secondary">{{ $refund->reason ?? 'N/A' }}</span></td>
                <td><small>{{ \Carbon\Carbon::parse($refund->created_at)->format('d M Y h:i A') }}</small></td>
                <td>
                    @if($refund->status == 'requested')
                        <span class="badge bg-soft-warning">Requested</span>
                    @elseif($refund->status == 'approved')
                        <span class="badge bg-soft-info">Pending Refund</span>
                    @elseif($refund->status == 'rejected')
                        <span class="badge bg-soft-danger">Rejected</span>
                    @elseif($refund->status == 'refunded')
                        <span class="badge bg-soft-success">Refunded</span>
                    @else
                        <span class="badge bg-soft-secondary">{{ ucfirst($refund->status) }}</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                        @if($refund->status == 'approved')
                            <button class="btn btn-sm btn-success approve-refund" data-id="{{ $refund->id }}">Issue Refund</button>
                            <button class="btn btn-sm btn-danger decline-refund" data-id="{{ $refund->id }}">Decline</button>
                        @endif
                        <button class="btn btn-sm btn-info view-refund-detail" data-order-id="{{ $refund->order_id }}" data-refund-id="{{ $refund->id }}"><i class="mdi mdi-eye me-1"></i>View</button>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center py-5 text-muted">No refund requests found</td></tr>
            @endforelse
            </tbody></table></div>
            <div class="d-flex justify-content-between align-items-center mt-3"><div><small class="text-muted" id="refundInfo">Showing {{ $refundsData->firstItem() ?? 0 }} to {{ $refundsData->lastItem() ?? 0 }} of {{ $refundsData->total() ?? 0 }} entries</small></div><nav>{{ $refundsData->links('pagination::bootstrap-4') }}</nav></div></div></div>
        </div>

        {{-- WALLET CONTROLS TAB --}}
        <div class="tab-pane fade" id="pane-controls" role="tabpanel">
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card wallet-card control-card">
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#freeze-content"><i class="mdi mdi-snowflake me-1"></i> Freeze</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#unfreeze-content"><i class="mdi mdi-lock-open-variant-outline me-1"></i> Unfreeze</button></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="freeze-content">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><label class="form-label small">User Type</label><select class="form-select form-select-sm" id="freezeUserType"><option value="">Select</option><option value="seller">Seller</option><option value="customer">Buyer</option></select></div>
                                        <div class="col-6"><label class="form-label small">User</label><select class="form-select form-select-sm" id="freezeUserId"><option value="">Select User</option></select></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label small">Reason</label><input type="text" class="form-control form-control-sm" id="freezeReason" placeholder="Enter reason"></div>
                                    <button class="btn btn-danger w-100 btn-sm" id="freezeWalletBtn"><i class="mdi mdi-snowflake me-1"></i> Freeze Wallet</button>
                                </div>
                                <div class="tab-pane fade" id="unfreeze-content">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><label class="form-label small">User Type</label><select class="form-select form-select-sm" id="unfreezeUserType"><option value="">Select</option><option value="seller">Seller</option><option value="customer">Buyer</option></select></div>
                                        <div class="col-6"><label class="form-label small">User</label><select class="form-select form-select-sm" id="unfreezeUserId"><option value="">Select User</option></select></div>
                                    </div>
                                    <div class="mb-3"><label class="form-label small">Reason</label><input type="text" class="form-control form-control-sm" id="unfreezeReason" placeholder="Enter reason"></div>
                                    <button class="btn btn-success w-100 btn-sm" id="unfreezeWalletBtn"><i class="mdi mdi-lock-open-variant-outline me-1"></i> Unfreeze Wallet</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="card wallet-card control-card">
                        <div class="card-body">
                            <ul class="nav nav-tabs mb-3">
                                <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#credit-content"><i class="mdi mdi-plus-circle-outline me-1"></i> Credit</button></li>
                                <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#debit-content"><i class="mdi mdi-minus-circle-outline me-1"></i> Debit</button></li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="credit-content">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><label class="form-label small">User Type</label><select class="form-select form-select-sm" id="creditUserType"><option value="">Select</option><option value="seller">Seller</option><option value="customer">Buyer</option></select></div>
                                        <div class="col-6"><label class="form-label small">User</label><select class="form-select form-select-sm" id="creditUserId"><option value="">Select User</option></select></div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6"><label class="form-label small">Amount (₹)</label><input type="number" class="form-control form-control-sm" id="creditAmount" placeholder="Enter amount"></div>
                                        <div class="col-6"><label class="form-label small">Remark</label><input type="text" class="form-control form-control-sm" id="creditRemark" placeholder="Enter remark"></div>
                                    </div>
                                    <button class="btn btn-info w-100 btn-sm text-white" id="creditBtn"><i class="mdi mdi-plus-circle-outline me-1"></i> Credit Amount</button>
                                </div>
                                <div class="tab-pane fade" id="debit-content">
                                    <div class="row g-2 mb-2">
                                        <div class="col-6"><label class="form-label small">User Type</label><select class="form-select form-select-sm" id="debitUserType"><option value="">Select</option><option value="seller">Seller</option><option value="customer">Buyer</option></select></div>
                                        <div class="col-6"><label class="form-label small">User</label><select class="form-select form-select-sm" id="debitUserId"><option value="">Select User</option></select></div>
                                    </div>
                                    <div class="row g-2 mb-3">
                                        <div class="col-6"><label class="form-label small">Amount (₹)</label><input type="number" class="form-control form-control-sm" id="debitAmount" placeholder="Enter amount"></div>
                                        <div class="col-6"><label class="form-label small">Remark</label><input type="text" class="form-control form-control-sm" id="debitRemark" placeholder="Enter remark"></div>
                                    </div>
                                    <button class="btn w-100 btn-sm text-white" style="background:#e74a3b" id="debitBtn"><i class="mdi mdi-minus-circle-outline me-1"></i> Debit Amount</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card wallet-card">
                        <div class="card-header"><div class="d-flex justify-content-between"><h5 class="card-title mb-0"><i class="mdi mdi-history me-1"></i> Freeze/Unfreeze History</h5><button class="btn btn-sm btn-outline-secondary" id="refreshHistoryBtn"><i class="mdi mdi-refresh me-1"></i> Refresh</button></div></div>
                        <div class="card-body"><div class="table-responsive"><table class="table table-hover table-sm align-middle wallet-table"><thead><tr><th>ID</th><th>User</th><th>Action</th><th>Reason</th><th>Performed By</th><th>Date</th></tr></thead><tbody id="historyLogsBody"><tr><td colspan="6" class="text-center py-5 text-muted">No history found</td></tr></tbody></table></div></div>
                    </div>
                </div>
            </div>
        </div>

        {{-- TEAM SALARY & PAYOUTS TAB --}}
        <div class="tab-pane fade" id="pane-salary" role="tabpanel">
            <div class="row g-4">
           <div class="col-lg-6">
    <div class="card wallet-card control-card h-100">
        <div class="card-header py-2">
            <h5 class="card-title mb-0"><i class="mdi mdi-bank-transfer me-1"></i> Self Withdrawal</h5>
            <small class="text-muted">Withdraw your own earnings to bank account</small>
        </div>
        <div class="card-body" style="max-height: 450px; overflow-y: auto;">
            <!-- Available Balance Alert -->
            <div class="alert alert-info alert-sm mb-3">
                <i class="mdi mdi-wallet me-1"></i>
                Available Balance: <strong id="selfAvailableBalance" class="money" data-amount="{{ $adminBalance ?? 0 }}">₹{{ number_format($adminBalance ?? 0, 2) }}</strong>
                | Minimum Withdrawal: <span class="money" data-amount="500">₹500.00</span>
            </div>
            
            <!-- Select Bank Account Dropdown -->
            <div class="mb-3">
                <label class="form-label small">Select Bank Account</label>
                <select class="form-select form-select-sm" id="selfWithdrawBank">
                    <option value="">-- Select Bank Account --</option>
                </select>
                <div class="mt-2">
                    <button type="button" class="btn btn-link btn-sm p-0 text-primary" id="showAddBankBtn">
                        <i class="mdi mdi-plus-circle me-1"></i> Add New Bank Account
                    </button>
                </div>
            </div>
            
            <!-- Add New Bank Account Form (Hidden by default) -->
            <div id="addBankForm" style="display: none;" class="mb-3 p-3 border rounded bg-light">
                <h6 class="mb-2 fw-bold">Add New Bank Account</h6>
                <div class="row g-2">
                    <div class="col-12">
                        <input type="text" class="form-control form-control-sm" id="bankAccountHolder" placeholder="Account Holder Name">
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control form-control-sm" id="bankAccountName" placeholder="Bank Name">
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control form-control-sm" id="bankAccountNumber" placeholder="Account Number">
                    </div>
                    <div class="col-12">
                        <input type="text" class="form-control form-control-sm" id="bankIfscCode" placeholder="IFSC Code">
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-success btn-sm w-100" id="saveNewBankBtn">
                            <i class="mdi mdi-check me-1"></i> Save Account
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-secondary btn-sm w-100" id="cancelAddBankBtn">
                            <i class="mdi mdi-close me-1"></i> Cancel
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Withdrawal Form -->
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label small">Amount (₹)</label>
                    <input type="number" class="form-control form-control-sm" id="selfWithdrawAmount" placeholder="Enter amount (Min ₹500)">
                </div>
                <div class="col-12">
                    <label class="form-label small">Remarks (Optional)</label>
                    <input type="text" class="form-control form-control-sm" id="selfWithdrawRemark" placeholder="Enter remarks">
                </div>
                <div class="col-12">
                    <button class="btn btn-primary w-100 btn-sm" id="selfWithdrawBtn">
                        <i class="mdi mdi-bank-transfer me-1"></i> Request Withdrawal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
                <div class="col-lg-6">
                    <div class="card wallet-card control-card h-100">
                        <div class="card-header py-2">
                            <h5 class="card-title mb-0"><i class="mdi mdi-account-group me-1"></i> Team Salary Disbursement</h5>
                            <small class="text-muted">Pay salaries to team members (Multi-select supported)</small>
                        </div>
                        <div class="card-body" style="max-height: 450px; overflow-y: auto;">
                            <div class="alert alert-warning alert-sm mb-3">
                                <i class="mdi mdi-currency-inr me-1"></i>
                                Total Salary Pool: <strong id="salaryPool">₹5,00,000.00</strong>
                            </div>
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small">Search Team Members</label>
                                    <input type="text" class="form-control form-control-sm" id="searchTeamMember" placeholder="Type to search team members...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Select Team Members (Click to multi-select)</label>
                                    <div class="border rounded p-2 team-member-container" style="max-height: 200px; overflow-y: auto; background: #f8f9fa;">
                                        <div id="teamMemberList">
                                            @php
                                                $staffMembers = DB::table('admin_staffs')
                                                    ->select('id', 'first_name', 'last_name', 'email')
                                                    ->where('status', 'Active')
                                                    ->orderBy('first_name', 'asc')
                                                    ->get();
                                            @endphp
                                            @if($staffMembers->count() > 0)
                                                @foreach($staffMembers as $member)
                                                <div class="form-check mb-2">
                                                    <input type="checkbox" class="form-check-input team-checkbox" 
                                                        id="staff_{{ $member->id }}" 
                                                        value="{{ $member->id }}" 
                                                        data-name="{{ $member->first_name }} {{ $member->last_name }}">
                                                    <label class="form-check-label small" for="staff_{{ $member->id }}">
                                                        {{ $member->first_name }} {{ $member->last_name }} 
                                                        <span class="text-muted">({{ $member->email }})</span>
                                                    </label>
                                                </div>
                                                @endforeach
                                            @else
                                                <div class="text-center text-muted py-3">No active staff members found</div>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-between">
                                        <small class="text-muted" id="selectedCountDisplay">Selected: 0 members</small>
                                        <div>
                                            <button type="button" class="btn btn-xs btn-link p-0 me-2" id="selectAllMembers">Select All</button>
                                            <button type="button" class="btn btn-xs btn-link p-0" id="deselectAllMembers">Deselect All</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Salary Amount per Member (₹)</label>
                                    <input type="number" class="form-control form-control-sm" id="salaryAmount" placeholder="Enter salary amount per member">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Total Amount to Disburse</label>
                                    <input type="text" class="form-control form-control-sm bg-light" id="totalDisburseAmount" readonly placeholder="0.00">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Salary Month</label>
                                    <input type="month" class="form-control form-control-sm" id="salaryMonth" value="{{ date('Y-m') }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Payment Type</label>
                                    <select class="form-select form-select-sm" id="salaryPaymentType">
                                        <option value="bank">Bank Transfer</option>
                                        <option value="wallet">Wallet Credit</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small">Remarks</label>
                                    <input type="text" class="form-control form-control-sm" id="salaryRemark" placeholder="Enter remarks">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-success w-100 btn-sm" id="disburseSalaryBtn">
                                        <i class="mdi mdi-cash-multiple me-1"></i> Disburse Salary to Selected Members
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card wallet-card">
                        <div class="card-header py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0"><i class="mdi mdi-history me-1"></i> Salary & Payout History</h5>
                                <div><button type="button" class="btn btn-sm btn-outline-secondary" id="exportSalaryBtn"><i class="mdi mdi-export me-1"></i> Export</button></div>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive" style="max-height: 300px; overflow-y: auto;">
                                <table class="table table-hover table-sm align-middle wallet-table mb-0">
                                    <thead style="position: sticky; top: 0; background: #f8fafc; z-index: 1;">
                                        <tr><th style="width: 12%">Transaction ID</th><th style="width: 15%">Date & Time</th><th style="width: 10%">Type</th><th style="width: 20%">From/To</th><th style="width: 12%">Amount (₹)</th><th style="width: 12%">Payment Method</th><th style="width: 8%">Status</th><th style="width: 15%">Remarks</th><th style="width: 8%">Actions</th></tr>
                                    </thead>
                                    <tbody id="salaryHistoryBody"></tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 p-2">
                                <small class="text-muted" id="salaryHistoryInfo">Showing 0 to 0 of 0 entries</small>
                                <nav><ul class="pagination pagination-sm mb-0" id="salaryHistoryPagination"></ul></nav>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
let donutChart, trendChart;
let salaryTransactions = [];
let refundModal = null;

// Global currency helper - reads from localStorage (set by topbar currency switcher)
const _exchangeRates = { INR: 1, USD: 0.012, EUR: 0.011, GBP: 0.0096 };
const _currencySymbols = { INR: '₹', USD: '$', EUR: '€', GBP: '£' };
function getActiveCurrency() { return localStorage.getItem('selectedCurrency') || 'INR'; }
function getActiveSymbol() { return _currencySymbols[getActiveCurrency()] || '₹'; }
function getActiveRate() { return _exchangeRates[getActiveCurrency()] || 1; }
function formatMoney(amountINR) {
    let c = getActiveCurrency(), r = getActiveRate(), s = getActiveSymbol();
    let converted = parseFloat(amountINR) * r;
    return s + ' ' + converted.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
function moneySpan(amountINR, extraClass) {
    extraClass = extraClass || '';
    return '<span class="money ' + extraClass + '" data-amount="' + amountINR + '">' + formatMoney(amountINR) + '</span>';
}
// Listen for currency change from topbar and re-render dynamic JS content
window.addEventListener('currencyChanged', function(e) {
    // Re-convert all .money elements (topbar already does this)
    // Also update table column headers
    document.querySelectorAll('.currency-label').forEach(function(el) {
        el.textContent = '(' + e.detail.symbol + ')';
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('refundDetailModal');
    if (modalElement) refundModal = new bootstrap.Modal(modalElement);
    
    let salaryHistoryData = [
        { id: 'SAL001', date: '2024-06-01 10:00:00', type: 'Salary', to: 'John Doe (Admin)', amount: 50000, method: 'Bank Transfer', status: 'completed', remarks: 'June 2024 Salary', members: 1 },
        { id: 'SAL002', date: '2024-06-01 10:05:00', type: 'Salary', to: 'Jane Smith (Manager)', amount: 35000, method: 'Bank Transfer', status: 'completed', remarks: 'June 2024 Salary', members: 1 },
        { id: 'SAL003', date: '2024-06-01 10:10:00', type: 'Salary', to: 'Mike Johnson, Sarah Wilson, David Brown', amount: 93000, method: 'Bank Transfer', status: 'completed', remarks: 'June 2024 Bulk Salary (3 members)', members: 3 },
        { id: 'WDR001', date: '2024-05-28 15:30:00', type: 'Withdrawal', to: 'Self (Admin)', amount: 25000, method: 'Bank Transfer', status: 'completed', remarks: 'Self withdrawal', members: 1 },
        { id: 'WDR002', date: '2024-05-25 11:45:00', type: 'Withdrawal', to: 'Self (Admin)', amount: 50000, method: 'Bank Transfer', status: 'pending', remarks: 'Monthly payout', members: 1 }
    ];
    salaryTransactions = salaryHistoryData;

    initializeDonutChart({!! json_encode($donutData) !!});
    initializeTrendChart({!! json_encode($trendData) !!});

    document.querySelectorAll('#walletTabs .nav-link').forEach(btn => {
        btn.addEventListener('shown.bs.tab', function() {
            document.getElementById('page-heading').textContent = this.dataset.title;
            if (this.id === 'tab-transactions') loadTransactions();
            else if (this.id === 'tab-withdrawals') loadWithdrawals('pending');
            else if (this.id === 'tab-refunds') loadRefunds();
            else if (this.id === 'tab-salary') renderSalaryHistory(salaryTransactions);
        });
    });

    $('#showAddBankForm').click(function() { $('#addBankForm').slideToggle(); });
    $('#cancelBankBtn').click(function() { $('#addBankForm').slideUp(); $('#addBankForm input').val(''); });
    
    $('#saveBankBtn').click(function() {
        let accountName = $('#bankAccountName').val(), bankName = $('#bankName').val(), accountNumber = $('#bankAccountNumber').val(), ifsc = $('#bankIfsc').val();
        if (!accountName || !bankName || !accountNumber || !ifsc) { Swal.fire('Error!', 'Please fill all bank details', 'error'); return; }
        let maskedNumber = '****' + accountNumber.slice(-4);
        $('#selfWithdrawBank').append(`<option value="${Date.now()}">${bankName} - ${maskedNumber}</option>`);
        $('#selfWithdrawBank').val($('#selfWithdrawBank option:last').val());
        $('#addBankForm').slideUp(); $('#addBankForm input').val('');
        Swal.fire('Success!', 'Bank account added successfully', 'success');
    });
    
    $('#selfWithdrawBtn').click(function() {
        let amount = $('#selfWithdrawAmount').val(), bank = $('#selfWithdrawBank').val();
        if (!amount || amount < 500) { Swal.fire('Error!', 'Amount must be at least ' + formatMoney(500), 'error'); return; }
        if (!bank) { Swal.fire('Error!', 'Select bank account', 'error'); return; }
        Swal.fire({ title: 'Confirm Withdrawal?', text: `Request withdrawal of ${formatMoney(amount)}?`, icon: 'question', showCancelButton: true }).then(result => {
            if (result.isConfirmed) {
                let newTransaction = { id: 'WDR' + Date.now(), date: new Date().toISOString().slice(0,19).replace('T',' '), type: 'Withdrawal', to: 'Self (Admin)', amount: parseFloat(amount), method: $('#selfWithdrawBank option:selected').text(), status: 'pending', remarks: $('#selfWithdrawRemark').val() || 'Self withdrawal request', members: 1 };
                salaryTransactions.unshift(newTransaction);
                renderSalaryHistory(salaryTransactions);
                Swal.fire('Success!', 'Withdrawal request submitted', 'success');
                $('#selfWithdrawAmount, #selfWithdrawRemark').val('');
            }
        });
    });

    $('#searchTeamMember').on('keyup', function() {
        let searchTerm = $(this).val().toLowerCase();
        $('#teamMemberList .form-check').each(function() { $(this).toggle($(this).text().toLowerCase().indexOf(searchTerm) > -1); });
    });
    
    function updateSelectedCountAndTotal() {
        let selectedCount = $('.team-checkbox:checked').length;
        let perMemberAmount = parseFloat($('#salaryAmount').val()) || 0;
        $('#selectedCountDisplay').text(`Selected: ${selectedCount} member${selectedCount !== 1 ? 's' : ''}`);
        $('#totalDisburseAmount').val((selectedCount * perMemberAmount).toLocaleString('en-IN'));
    }
    
    $(document).on('change', '.team-checkbox', updateSelectedCountAndTotal);
    $('#salaryAmount').on('keyup change', updateSelectedCountAndTotal);
    
    $('#selectAllMembers').click(function() { $('.team-checkbox').prop('checked', true).trigger('change'); });
    $('#deselectAllMembers').click(function() { $('.team-checkbox').prop('checked', false).trigger('change'); });
    
    $('#disburseSalaryBtn').click(function() {
        let selectedMembers = [];
        $('.team-checkbox:checked').each(function() { 
            selectedMembers.push($(this).val());
        });
        
        if (selectedMembers.length === 0) { 
            Swal.fire('Error!', 'Please select at least one team member', 'error'); 
            return; 
        }
        
        let perMemberAmount = $('#salaryAmount').val();
        if (!perMemberAmount || perMemberAmount <= 0) { 
            Swal.fire('Error!', 'Enter valid salary amount', 'error'); 
            return; 
        }
        
        let month = $('#salaryMonth').val();
        if (!month) { 
            Swal.fire('Error!', 'Select salary month', 'error'); 
            return; 
        }
        
        let totalAmount = selectedMembers.length * parseFloat(perMemberAmount);
        let memberNames = [];
        $('.team-checkbox:checked').each(function() {
            memberNames.push($(this).next('label').text().trim());
        });
        
        Swal.fire({
            title: 'Confirm Salary Disbursement?',
            html: `<div class="text-start">
                <p><strong>Selected Members:</strong> ${selectedMembers.length}</p>
                <p><strong>Per Member Amount:</strong> ₹${Number(perMemberAmount).toLocaleString('en-IN')}</p>
                <p><strong>Total Amount:</strong> ₹${totalAmount.toLocaleString('en-IN')}</p>
                <p><strong>Month:</strong> ${month}</p>
            </div>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745'
        }).then(result => {
            if (result.isConfirmed) {
                Swal.fire({ title: 'Processing...', text: 'Disbursing salary...', allowOutsideClick: false, didOpen: () => { Swal.showLoading(); } });
                
                $.ajax({
                    url: '{{ route("admin.wallet.disburse-salary") }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        selected_members: selectedMembers,
                        per_member_amount: perMemberAmount,
                        total_amount: totalAmount,
                        month: month,
                        payment_type: $('#salaryPaymentType').val(),
                        remarks: $('#salaryRemark').val() || `${month} Salary for ${selectedMembers.length} members`
                    },
                    success: function(response) {
                        if (response.success) {
                            let newTransaction = {
                                id: 'SAL' + Date.now(),
                                date: new Date().toISOString().slice(0,19).replace('T',' '),
                                type: 'Salary',
                                to: memberNames.join(', '),
                                amount: totalAmount,
                                method: $('#salaryPaymentType').val() === 'bank' ? 'Bank Transfer' : ($('#salaryPaymentType').val() === 'wallet' ? 'Wallet Credit' : 'Cheque'),
                                status: 'completed',
                                remarks: $('#salaryRemark').val() || `${month} Salary for ${selectedMembers.length} members`,
                                members: selectedMembers.length
                            };
                            salaryTransactions.unshift(newTransaction);
                            renderSalaryHistory(salaryTransactions);
                            
                            Swal.fire('Success!', response.message, 'success');
                            
                            $('.team-checkbox').prop('checked', false);
                            $('#salaryAmount').val('');
                            $('#salaryRemark').val('');
                            $('#totalDisburseAmount').val('');
                            $('#selectedCountDisplay').text('Selected: 0 members');
                            
                            let currentPool = parseFloat($('#salaryPool').text().replace(/[^0-9.-]/g, '')) || 500000;
                            $('#salaryPool').text('₹' + (currentPool - totalAmount).toLocaleString('en-IN'));
                            refreshDashboardSummary();
                        } else {
                            Swal.fire('Error!', response.message, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire('Error!', 'Something went wrong. Please try again.', 'error');
                    }
                });
            }
        });
    });
    
    $('#exportSalaryBtn').click(function() {
        let csv = "Transaction ID,Date,Type,From/To,Amount,Payment Method,Status,Remarks\n";
        salaryTransactions.forEach(t => { csv += `${t.id},${t.date},${t.type},${t.to},${t.amount},${t.method},${t.status},${t.remarks}\n`; });
        let blob = new Blob([csv], { type: 'text/csv' }), link = document.createElement('a');
        link.href = URL.createObjectURL(blob); link.download = `salary_report_${new Date().toISOString().slice(0,10)}.csv`; link.click();
        Swal.fire('Export Started', 'Salary report downloaded', 'success');
    });
    
    $(document).on('click', '.view-salary-detail', function() {
        let transaction = salaryTransactions.find(t => t.id === $(this).data('id'));
        if (transaction) Swal.fire({ title: 'Transaction Details', html: `<div class="text-start"><p><strong>Transaction ID:</strong> ${transaction.id}</p><p><strong>Date:</strong> ${new Date(transaction.date).toLocaleString()}</p><p><strong>Type:</strong> ${transaction.type}</p><p><strong>From/To:</strong> ${transaction.to}</p><p><strong>Amount:</strong> ${formatMoney(transaction.amount)}</p><p><strong>Payment Method:</strong> ${transaction.method}</p><p><strong>Status:</strong> ${transaction.status.toUpperCase()}</p><p><strong>Remarks:</strong> ${transaction.remarks}</p></div>`, icon: 'info' });
    });
    
    function renderSalaryHistory(transactions) {
        let html = '';
        if (transactions && transactions.length > 0) {
            transactions.forEach(t => {
                html += `<tr>
                    <td><code>${t.id}</code></td>
                    <td><small>${new Date(t.date).toLocaleString()}</small></td>
                    <td>${t.type === 'Salary' ? '<span class="badge bg-soft-primary">Salary</span>' : '<span class="badge bg-soft-info">Withdrawal</span>'}</td>
                    <td>${t.to}${t.members > 1 ? ` <span class="badge bg-soft-secondary">${t.members} members</span>` : ''}</td>
                    <td class="fw-semibold">${moneySpan(t.amount)}</td>
                    <td><small>${t.method}</small></td>
                    <td>${t.status === 'completed' ? '<span class="badge bg-soft-success">Completed</span>' : '<span class="badge bg-soft-warning">Pending</span>'}</td>
                    <td><small>${t.remarks}</small></td>
                    <td><button class="btn btn-sm btn-secondary view-salary-detail" data-id="${t.id}"><i class="mdi mdi-eye"></i></button></td>
                </tr>`;
            });
        } else html = '<tr><td colspan="9" class="text-center py-5 text-muted">No salary transactions found</td></tr>';
        $('#salaryHistoryBody').html(html);
        $('#salaryHistoryInfo').text(`Showing 1 to ${transactions.length} of ${transactions.length} entries`);
    }

    function loadTransactions(page = 1) { $.ajax({ url: '{{ route("admin.wallet.transactions") }}', method: 'GET', data: { page: page }, beforeSend: () => $('#transactionsTableBody').html('<tr><td colspan="9" class="text-center"><div class="spinner-border"></div></td></tr>'), success: response => response.success && renderTransactions(response.data) }); }
    function renderTransactions(data) { let html = ''; data.data.forEach(t => { html += `<tr>
        <td><small>${new Date(t.created_at).toLocaleString()}</small></td>
        <td>${t.user_name}</td>
        <td><span class="badge bg-soft-${t.user_type == 'seller' ? 'primary' : 'success'}">${t.user_type}</span></td>
        <td><span class="badge bg-soft-${t.badge_color || 'secondary'}">${t.transaction_type.replace('_', ' ').toUpperCase()}</span></td>
        <td>${t.credit > 0 ? moneySpan(t.credit, 'text-success') : '-'}</td>
        <td>${t.debit > 0 ? moneySpan(t.debit, 'text-danger') : '-'}</td>
        <td>${moneySpan(t.balance_after)}</td>
        <td>${t.description || '-'}</td>
        <td><code>${t.reference_id || 'N/A'}</code></td>
    </tr>`; }); $('#transactionsTableBody').html(html); $('#transactionInfo').text(`Showing ${data.from || 0} to ${data.to || 0} of ${data.total || 0} entries`); }
    $('#filterBtn').click(() => loadTransactions()); $('#resetFilterBtn').click(() => { $('#filterUserType, #filterTransactionType').val(''); $('#filterUserId').html('<option value="">All Users</option>'); $('#startDate, #endDate').val(''); loadTransactions(); });
    $('#filterUserType').change(function() { loadUsers($(this).val(), '#filterUserId'); });
    function loadUsers(userType, targetSelect) { if (!userType) { $(targetSelect).html('<option value="">All Users</option>'); return; } $.ajax({ url: '{{ route("admin.wallet.users") }}', method: 'GET', data: { user_type: userType }, success: res => { if (res.success) { let opts = '<option value="">All Users</option>'; res.data.forEach(u => opts += `<option value="${u.id}">${u.text}</option>`); $(targetSelect).html(opts); } } }); }

    function loadWithdrawals(status, page = 1) { $.ajax({ url: '{{ route("admin.wallet.withdrawals") }}', method: 'GET', data: { status: status, page: page }, success: response => response.success && renderWithdrawals(response.data, status) }); }
    function renderWithdrawals(data, status) {
        if (status === 'pending') { let html = ''; if(data.data && data.data.length) { data.data.forEach(w => { html += `<tr>
            <td><code>${w.request_id}</code></td>
            <td>${w.user_name}</td>
            <td><span class="badge bg-soft-primary">${w.user_type}</span></td>
            <td class="fw-semibold">${moneySpan(w.amount)}</td>
            <td><small>${new Date(w.created_at).toLocaleString()}</small></td>
            <td><small>${w.bank_name}<br>${w.bank_account_number}</small></td>
            <td><span class="badge bg-soft-warning">Pending</span></td>
            <td><div class="d-flex gap-1"><button class="btn btn-sm btn-success approve-withdrawal" data-id="${w.id}">Approve</button><button class="btn btn-sm btn-danger reject-withdrawal" data-id="${w.id}">Reject</button></div></td>
        </tr>`; }); } else html = '<tr><td colspan="8" class="text-center">No pending withdrawals</td></tr>'; $('#pendingWithdrawalsBody').html(html); $('#pendingBadge').text(data.total || 0); }
        else if (status === 'approved') { let html = ''; if(data.data && data.data.length) { data.data.forEach(w => { html += `<tr>
            <td><code>${w.request_id}</code></td>
            <td>${w.user_name}</td>
            <td><span class="badge bg-soft-primary">${w.user_type}</span></td>
            <td>${moneySpan(w.amount)}</td>
            <td><small>${new Date(w.created_at).toLocaleString()}</small></td>
            <td><small>${w.processed_at ? new Date(w.processed_at).toLocaleString() : '-'}</small></td>
            <td><small>${w.bank_name}</small></td>
            <td><span class="badge bg-soft-success">Approved</span></td>
        </tr>`; }); } else html = '<tr><td colspan="8" class="text-center py-4 text-muted">No approved withdrawals found</td></tr>'; $('#approvedWithdrawalsBody').html(html); }
        else if (status === 'rejected') { let html = ''; if(data.data && data.data.length) { data.data.forEach(w => { html += `<tr>
            <td><code>${w.request_id}</code></td>
            <td>${w.user_name}</td>
            <td><span class="badge bg-soft-primary">${w.user_type}</span></td>
            <td>${moneySpan(w.amount)}</td>
            <td><small>${new Date(w.created_at).toLocaleString()}</small></td>
            <td><small>${w.processed_at ? new Date(w.processed_at).toLocaleString() : '-'}</small></td>
            <td><small>${w.bank_name}</small></td>
            <td><span class="badge bg-soft-danger">Rejected</span></td>
        </tr>`; }); } else html = '<tr><td colspan="8" class="text-center py-4 text-muted">No rejected withdrawals found</td></tr>'; $('#rejectedWithdrawalsBody').html(html); }
    }
    $(document).on('click', '.approve-withdrawal', function() { confirmWithdrawalAction($(this).data('id'), 'approve'); });
    $(document).on('click', '.reject-withdrawal', function() { confirmWithdrawalAction($(this).data('id'), 'reject'); });
    function confirmWithdrawalAction(id, action) { Swal.fire({ title: 'Are you sure?', text: `Do you want to ${action} this withdrawal?`, icon: 'question', showCancelButton: true }).then(result => { if (result.isConfirmed) { $.ajax({ url: `{{ url("admin/wallet/withdrawal") }}/${id}/${action}`, method: 'POST', data: { _token: '{{ csrf_token() }}' }, success: res => { if (res.success) { Swal.fire('Success!', `Withdrawal ${action}d`, 'success'); loadWithdrawals('pending'); refreshDashboardSummary(); } else Swal.fire('Error!', res.message, 'error'); } }); } }); }
    $('#withdrawalTabs button').click(function() { loadWithdrawals($(this).data('status')); });

    function loadRefunds(page = 1) { $('#refundsTableBody').html('<tr><td colspan="9" class="text-center"><div class="spinner-border"></div></td></tr>'); $.ajax({ url: '{{ route("admin.wallet.refunds") }}', method: 'GET', data: { page: page }, success: res => res.success && renderRefunds(res.data) }); }
    function renderRefunds(data) { let html = ''; if (data.data && data.data.length) { data.data.forEach(r => { let actionButtons = `<div class="d-flex gap-1">`; if (r.status === 'pending') { actionButtons += `<button class="btn btn-sm btn-success approve-refund" data-id="${r.id}">Issue Refund</button><button class="btn btn-sm btn-danger decline-refund" data-id="${r.id}">Decline</button>`; } actionButtons += `<button class="btn btn-sm btn-info view-refund-detail" data-order-id="${r.order_id}" data-refund-id="${r.id}"><i class="mdi mdi-eye me-1"></i>View Order</button></div>`; html += `<tr>
            <td><input type="checkbox" class="refund-checkbox" data-id="${r.id}"></td>
            <td><code class="order-id-clickable" data-order-id="${r.order_id}" data-refund-id="${r.id}" style="cursor:pointer; color:#4e73df; text-decoration:underline;">${r.order_id}</code></td>
            <td><code>${r.buyer_id}</code></td>
            <td>${r.buyer_name}</td>
            <td class="fw-semibold">${moneySpan(r.amount)}</td>
            <td><span class="badge bg-soft-${r.reason_color || 'secondary'}">${r.reason}</span></td>
            <td><small>${new Date(r.created_at).toLocaleString()}</small></td>
            <td>${r.status === 'pending' ? '<span class="badge bg-soft-warning">Pending</span>' : (r.status === 'refunded' ? '<span class="badge bg-soft-success">Refunded</span>' : '<span class="badge bg-soft-danger">Rejected</span>')}</td>
            <td>${actionButtons}</td>
        </tr>`; }); } else html = '<tr><td colspan="9" class="text-center py-5 text-muted">No refund requests found</td></tr>'; $('#refundsTableBody').html(html); $('#refundInfo').text(`Showing ${data.from || 0} to ${data.to || 0} of ${data.total || 0} entries`); }
    
    // SINGLE WORKING EVENT HANDLER FOR MODAL - FIXED VERSION
    $(document).on('click', '.view-refund-detail, .order-id-clickable', function(e) {
        e.preventDefault();
        e.stopPropagation();
        let orderId = $(this).data('order-id');
        let refundId = $(this).data('refund-id');
        if (!orderId) { Swal.fire('Error', 'Order ID not found', 'error'); return; }
        
        let modalElement = document.getElementById('refundDetailModal');
        if (!modalElement) { Swal.fire('Error', 'Modal not found', 'error'); return; }
        
        $('#refundModalBody').html('<div class="text-center py-5"><div class="spinner-border text-primary"></div><p class="mt-2">Loading...</p></div>');
        let modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
        modal.show();

        $.get(`{{ url("admin/wallet/get-order-details") }}`, { order_id: orderId, refund_id: refundId }, function(res) {
            if (res.success && res.data) {
                let d = res.data;
                let orderDate = d.order_date ? new Date(d.order_date).toLocaleString() : 'N/A';
                let refundDate = d.refund_request_date ? new Date(d.refund_request_date).toLocaleString() : 'N/A';
                let statusClass = d.refund_status === 'approved' ? 'bg-info' : (d.refund_status === 'refunded' ? 'bg-success' : 'bg-warning');
                
                let itemsHtml = '';
                let total = 0;
                if (d.items && d.items.length) {
                    d.items.forEach(i => { let t = i.quantity * i.price; total += t;
                        itemsHtml += `<tr><td><img src="${i.image}" class="rounded" width="45" height="45" style="object-fit:cover"></td><td><strong>${i.name}</strong><br><small class="text-muted">SKU: ${i.sku}</small></td><td class="text-center">${i.quantity}</td><td class="text-end">${formatMoney(i.price)}</td><td class="text-end">${formatMoney(t)}</td></tr>`;
                    });
                } else { itemsHtml = '<tr><td colspan="5" class="text-center text-muted">No items</td></tr>'; }

                $('#refundModalBody').html(`
                <div class="row g-3">
                    <div class="col-md-6"><div class="card border mb-0"><div class="card-header bg-light py-2"><strong>📦 Order Info</strong></div><div class="card-body p-3">
                        <p class="mb-1"><strong>Order ID:</strong> <code>#${d.order_id}</code></p>
                        <p class="mb-1"><strong>Date:</strong> ${orderDate}</p>
                        <p class="mb-1"><strong>Total:</strong> <span class="fw-bold text-primary">${formatMoney(d.total_amount)}</span></p>
                        <p class="mb-1"><strong>Payment:</strong> ${d.payment_method || 'N/A'} <span class="badge bg-${d.payment_status==='paid'?'success':'warning'} ms-1">${d.payment_status||'N/A'}</span></p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-info">${d.order_status||'N/A'}</span></p>
                    </div></div></div>
                    <div class="col-md-6"><div class="card border mb-0"><div class="card-header bg-light py-2"><strong>👤 Customer</strong></div><div class="card-body p-3">
                        <p class="mb-1"><strong>Name:</strong> ${d.customer?.name||'N/A'}</p>
                        <p class="mb-1"><strong>Email:</strong> ${d.customer?.email||'N/A'}</p>
                        <p class="mb-1"><strong>Phone:</strong> ${d.customer?.phone||'N/A'}</p>
                        <p class="mb-0"><strong>Address:</strong> ${d.customer?.address||'N/A'}</p>
                    </div></div></div>
                    <div class="col-md-6"><div class="card border mb-0"><div class="card-header bg-light py-2"><strong>🔄 Refund Info</strong></div><div class="card-body p-3">
                        <p class="mb-1"><strong>Refund ID:</strong> <code>${d.refund_id||'N/A'}</code></p>
                        <p class="mb-1"><strong>Date:</strong> ${refundDate}</p>
                        <p class="mb-1"><strong>Reason:</strong> ${d.refund_reason||'N/A'}</p>
                        <p class="mb-1"><strong>Refund Amount:</strong> <span class="fw-bold text-success">${formatMoney(d.refund_amount||0)}</span></p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge ${statusClass}">${d.refund_status||'N/A'}</span></p>
                        ${d.refund_attachment ? `<p class="mb-0 mt-2"><strong>Attachment:</strong> <a href="/public/${d.refund_attachment}" target="_blank" class="text-primary"><i class="fa-solid fa-image"></i> View Image</a></p>` : ''}
                    </div></div></div>
                    <div class="col-md-6"><div class="card border mb-0"><div class="card-header bg-light py-2"><strong>💰 Commission</strong></div><div class="card-body p-3">
                        <p class="mb-1"><strong>Platform:</strong> <span class="badge bg-primary">${d.commission?.platform_pct||0}%</span> = <span class="text-danger fw-bold">${formatMoney(d.commission?.platform_cut||0)}</span></p>
                        <p class="mb-1"><strong>Seller:</strong> <span class="badge bg-success">${d.commission?.seller_pct||0}%</span></p>
                        <p class="mb-1"><strong>Referrer:</strong> <span class="badge bg-info">${d.commission?.referrer_pct||0}%</span></p>
                        <hr class="my-2"><p class="mb-1"><strong>Customer Gets:</strong> <span class="text-success fw-bold fs-6">${formatMoney(d.refund_amount||0)}</span></p>
                        <p class="mb-0"><strong>Platform Keeps:</strong> <span class="text-danger fw-bold">${formatMoney(d.commission?.platform_cut||0)}</span></p>
                    </div></div></div>
                    <div class="col-12"><div class="card border mb-0"><div class="card-header bg-light py-2"><strong>🛒 Order Items</strong></div><div class="card-body p-0"><div class="table-responsive"><table class="table table-sm mb-0"><thead><tr><th>Image</th><th>Product</th><th class="text-center">Qty</th><th class="text-end">Price</th><th class="text-end">Total</th></tr></thead><tbody>${itemsHtml}</tbody><tfoot><tr><td colspan="4" class="text-end fw-bold">Total:</td><td class="text-end fw-bold text-primary">${formatMoney(total)}</td></tr></tfoot></table></div></div></div></div>
                </div>`);
            } else {
                $('#refundModalBody').html('<div class="alert alert-danger text-center">Failed to load: '+(res.message||'Unknown error')+'</div>');
            }
        }).fail(function() {
            $('#refundModalBody').html('<div class="alert alert-danger text-center">Error fetching order details.</div>');
        });
    });

    // Issue Refund button
    $(document).on('click', '.approve-refund', function() {
        let id = $(this).data('id');
        Swal.fire({ title: 'Issue Refund?', text: 'Platform commission will be deducted and remaining amount credited to buyer wallet.', icon: 'question', showCancelButton: true, confirmButtonText: 'Yes, Issue Refund' }).then(result => {
            if (result.isConfirmed) {
                $.ajax({ url: `{{ url("admin/wallet/refund") }}/${id}/approve`, method: 'POST', data: { _token: '{{ csrf_token() }}' },
                    success: (res) => { Swal.fire('Success!', res.message || 'Refund issued successfully', 'success').then(() => location.reload()); },
                    error: (xhr) => { let msg = xhr.responseJSON?.message || 'Failed to process refund'; Swal.fire('Error', msg, 'error'); }
                });
            }
        });
    });

    // Decline Refund button
    $(document).on('click', '.decline-refund', function() {
        let id = $(this).data('id');
        Swal.fire({ title: 'Decline Refund?', text: 'This refund request will be rejected.', icon: 'warning', showCancelButton: true, confirmButtonText: 'Yes, Decline' }).then(result => {
            if (result.isConfirmed) { Swal.fire('Declined', 'Refund declined', 'success').then(() => location.reload()); }
        });
    });
    
    $('#filterRefundsBtn').click(() => location.reload());
    $('#resetRefundsFilterBtn').click(() => { $('#refundStatusFilter, #refundBuyerFilter, #refundOrderFilter').val(''); $('#refundStartDate, #refundEndDate').val(''); location.reload(); });
    
    function refreshDashboardSummary() { $.ajax({ url: '{{ route("admin.wallet.dashboard-summary") }}', method: 'GET', success: res => { if (res.success) { 
        $('#totalBalance').attr('data-amount', res.data.total_balance).text(formatMoney(res.data.total_balance)); 
        $('#sellerBalance').attr('data-amount', res.data.seller_balance).text(formatMoney(res.data.seller_balance)); 
        $('#buyerBalance').attr('data-amount', res.data.buyer_balance).text(formatMoney(res.data.buyer_balance)); 
        $('#frozenAmount').attr('data-amount', res.data.frozen_amount).text(formatMoney(res.data.frozen_amount)); 
        $('#pendingWithdrawalsAmount').attr('data-amount', res.data.pending_withdrawals).text(formatMoney(res.data.pending_withdrawals)); 
        $('#pendingWithdrawalsCount').text(res.data.pending_withdrawals_count + ' Requests'); 
        
        // Update persistent balance card if it exists
        if ($('#persistentTotalBalance').length) {
            $('#persistentTotalBalance').attr('data-amount', res.data.total_balance).text(formatMoney(res.data.total_balance));
            $('#persistentSellerBalance').attr('data-amount', res.data.seller_balance).text(formatMoney(res.data.seller_balance));
            $('#persistentBuyerBalance').attr('data-amount', res.data.buyer_balance).text(formatMoney(res.data.buyer_balance));
            $('#persistentFrozenAmount').attr('data-amount', res.data.frozen_amount).text(formatMoney(res.data.frozen_amount));
        }
        
        if (donutChart) donutChart.updateSeries([res.data.donut_data.seller, res.data.donut_data.buyer, res.data.donut_data.frozen, res.data.donut_data.pending]); 
        if (trendChart) trendChart.updateSeries([{ data: res.data.trend_data.map(i => i.balance) }]); 
    } } }); }

    $('#freezeUserType, #unfreezeUserType, #creditUserType, #debitUserType').change(function() { let target = { freezeUserType: '#freezeUserId', unfreezeUserType: '#unfreezeUserId', creditUserType: '#creditUserId', debitUserType: '#debitUserId' }[$(this).attr('id')]; if (target) loadControlUsers($(this).val(), target); });
    function loadControlUsers(userType, target) { if (!userType) { $(target).html('<option value="">Select User</option>'); return; } $.ajax({ url: '{{ route("admin.wallet.users") }}', method: 'GET', data: { user_type: userType }, success: res => { if (res.success) { let opts = '<option value="">Select User</option>'; res.data.forEach(u => opts += `<option value="${u.id}">${u.text} (ID: ${u.id})</option>`); $(target).html(opts); } } }); }
    
    $('#freezeWalletBtn').click(() => processWalletAction('freeze', '#freezeUserType', '#freezeUserId', '#freezeReason'));
    $('#unfreezeWalletBtn').click(() => processWalletAction('unfreeze', '#unfreezeUserType', '#unfreezeUserId', '#unfreezeReason'));
    $('#creditBtn').click(() => processWalletAction('credit', '#creditUserType', '#creditUserId', null, '#creditAmount', '#creditRemark'));
    $('#debitBtn').click(() => processWalletAction('debit', '#debitUserType', '#debitUserId', null, '#debitAmount', '#debitRemark'));
    
    function processWalletAction(action, userTypeId, userIdId, reasonId, amountId = null, remarkId = null) { let userType = $(userTypeId).val(), userId = $(userIdId).val(); if (!userType || !userId) { Swal.fire('Error!', 'Please select user', 'error'); return; } let data = { _token: '{{ csrf_token() }}', user_type: userType, user_id: userId }; if (reasonId && action !== 'credit' && action !== 'debit') { let reason = $(reasonId).val(); if (!reason) { Swal.fire('Error!', 'Enter reason', 'error'); return; } data.reason = reason; } if (amountId) { let amount = $(amountId).val(); if (!amount || amount <= 0) { Swal.fire('Error!', 'Enter valid amount', 'error'); return; } data.amount = amount; } if (remarkId) { let remark = $(remarkId).val(); if (!remark) { Swal.fire('Error!', 'Enter remark', 'error'); return; } data.remark = remark; } let url = action === 'freeze' ? '{{ route("admin.wallet.freeze") }}' : (action === 'unfreeze' ? '{{ route("admin.wallet.unfreeze") }}' : (action === 'credit' ? '{{ route("admin.wallet.credit") }}' : '{{ route("admin.wallet.debit") }}')); $.ajax({ url: url, method: 'POST', data: data, success: res => { if (res.success) { Swal.fire('Success!', res.message, 'success'); $(userTypeId).val(''); $(userIdId).html('<option value="">Select User</option>'); if (reasonId && action !== 'credit' && action !== 'debit') $(reasonId).val(''); if (amountId) $(amountId).val(''); if (remarkId) $(remarkId).val(''); refreshDashboardSummary(); if ($('#tab-transactions').hasClass('active')) loadTransactions(); loadHistoryLogs(); } } }); }
    
    function loadHistoryLogs() { $.ajax({ url: '{{ route("admin.wallet.history") }}', method: 'GET', success: res => res.success && renderHistoryLogs(res.data) }); }
    function renderHistoryLogs(data) { let html = ''; if (data.data && data.data.length) { data.data.forEach(log => { html += `<tr>
        <td>${log.id}</td>
        <td>${log.user_name} <span class="badge bg-soft-${log.user_type === 'seller' ? 'primary' : 'success'}">${log.user_type}</span></td>
        <td>${log.action === 'freeze' ? '<span class="badge bg-soft-danger">Freeze</span>' : '<span class="badge bg-soft-success">Unfreeze</span>'}</td>
        <td>${log.reason}</td>
        <td>${log.performed_by_name || 'System'}</td>
        <td><small>${new Date(log.created_at).toLocaleString()}</small></td>
    </tr>`; }); } else html = '<tr><td colspan="6" class="text-center py-5 text-muted">No history found</td></tr>'; $('#historyLogsBody').html(html); }
    
    $('#tab-controls').on('shown.bs.tab', function() { loadHistoryLogs(); });
    $('#refreshHistoryBtn').click(function() { loadHistoryLogs(); Swal.fire('Refreshed!', 'History updated', 'success'); });
    $('#tab-salary').on('shown.bs.tab', function() { renderSalaryHistory(salaryTransactions); });
});

// Load bank accounts on page load
function loadBankAccounts() {
    $.ajax({
        url: '{{ route("admin.wallet.get-bank-accounts") }}',
        method: 'GET',
        success: function(response) {
            if (response.success && response.data.length > 0) {
                let html = '<option value="">-- Select Bank Account --</option>';
                response.data.forEach(function(bank) {
                    let maskedNumber = bank.account_number ? '****' + bank.account_number.slice(-4) : '****';
                    html += `<option value="${bank.id}" 
                        data-bank-name="${bank.bank_name}" 
                        data-account-number="${bank.account_number}" 
                        data-ifsc="${bank.ifsc_code}" 
                        data-holder="${bank.account_holder_name}">
                        ${bank.bank_name} - ${maskedNumber}
                    </option>`;
                });
                $('#selfWithdrawBank').html(html);
            } else {
                $('#selfWithdrawBank').html('<option value="">-- No Bank Accounts --</option>');
            }
        },
        error: function() {
            $('#selfWithdrawBank').html('<option value="">-- Error loading accounts --</option>');
        }
    });
}

// Show Add Bank Form
$('#showAddBankBtn').click(function() {
    $('#addBankForm').slideToggle('fast');
    if ($('#addBankForm').is(':visible')) {
        $('#bankAccountHolder, #bankAccountName, #bankAccountNumber, #bankIfscCode').val('');
    }
});

// Cancel Add Bank
$('#cancelAddBankBtn').click(function() {
    $('#addBankForm').slideUp('fast');
    $('#bankAccountHolder, #bankAccountName, #bankAccountNumber, #bankIfscCode').val('');
});

// Save New Bank Account
$('#saveNewBankBtn').click(function() {
    let accountHolder = $('#bankAccountHolder').val().trim();
    let bankName = $('#bankAccountName').val().trim();
    let accountNumber = $('#bankAccountNumber').val().trim();
    let ifscCode = $('#bankIfscCode').val().trim();
    
    if (!accountHolder || !bankName || !accountNumber || !ifscCode) {
        Swal.fire('Error!', 'Please fill all bank details', 'error');
        return;
    }
    
    Swal.fire({
        title: 'Processing...',
        text: 'Saving bank account...',
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    $.ajax({
        url: '{{ route("admin.wallet.add-bank-account") }}',
        method: 'POST',
        data: {
            _token: '{{ csrf_token() }}',
            account_holder_name: accountHolder,
            bank_name: bankName,
            account_number: accountNumber,
            ifsc_code: ifscCode
        },
        success: function(response) {
            if (response.success) {
                let maskedNumber = '****' + accountNumber.slice(-4);
                let newOption = `<option value="${response.bank_id}" 
                    data-bank-name="${bankName}" 
                    data-account-number="${accountNumber}" 
                    data-ifsc="${ifscCode}" 
                    data-holder="${accountHolder}">
                    ${bankName} - ${maskedNumber}
                </option>`;
                $('#selfWithdrawBank').append(newOption);
                $('#selfWithdrawBank').val(response.bank_id);
                $('#addBankForm').slideUp('fast');
                $('#bankAccountHolder, #bankAccountName, #bankAccountNumber, #bankIfscCode').val('');
                Swal.fire('Success!', 'Bank account added successfully', 'success');
            } else {
                Swal.fire('Error!', response.message || 'Failed to add bank account', 'error');
            }
        },
        error: function(xhr) {
            let errorMsg = 'Failed to add bank account';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMsg = xhr.responseJSON.message;
            }
            Swal.fire('Error!', errorMsg, 'error');
        }
    });
});

// Self Withdraw
$('#selfWithdrawBtn').click(function() {
    let amount = $('#selfWithdrawAmount').val();
    let selectedBank = $('#selfWithdrawBank').find('option:selected');
    let bankId = $('#selfWithdrawBank').val();
    
    if (!amount || amount < 500) {
        Swal.fire('Error!', 'Amount must be at least ₹500', 'error');
        return;
    }
    
    if (!bankId) {
        Swal.fire('Error!', 'Please select a bank account first', 'error');
        return;
    }
    
    let bankName = selectedBank.data('bank-name');
    let accountNumber = selectedBank.data('account-number');
    let ifscCode = selectedBank.data('ifsc');
    let accountHolder = selectedBank.data('holder');
    
    Swal.fire({
        title: 'Confirm Withdrawal?',
        html: `<div class="text-start">
            <p><strong>Amount:</strong> ₹${Number(amount).toLocaleString('en-IN')}</p>
            <p><strong>Bank:</strong> ${bankName}</p>
            <p><strong>Account:</strong> ${accountNumber ? '****' + accountNumber.slice(-4) : 'N/A'}</p>
            <p><strong>Holder:</strong> ${accountHolder}</p>
        </div>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Withdraw',
        cancelButtonText: 'Cancel'
    }).then(result => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Processing...',
                text: 'Submitting withdrawal request...',
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });
            
            $.ajax({
                url: '{{ route("admin.wallet.self-withdraw") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    amount: amount,
                    bank_id: bankId,
                    bank_name: bankName,
                    bank_account_number: accountNumber,
                    ifsc_code: ifscCode,
                    account_holder_name: accountHolder,
                    remarks: $('#selfWithdrawRemark').val() || 'Self withdrawal request'
                },
                success: function(response) {
                    if (response.success) {
                        let newTransaction = {
                            id: 'WDR' + Date.now(),
                            date: new Date().toISOString().slice(0, 19).replace('T', ' '),
                            type: 'Withdrawal',
                            to: 'Self Withdrawal',
                            amount: parseFloat(amount),
                            method: 'Bank Transfer',
                            status: 'pending',
                            remarks: $('#selfWithdrawRemark').val() || 'Self withdrawal request',
                            members: 1
                        };
                        
                        if (typeof salaryTransactions !== 'undefined') {
                            salaryTransactions.unshift(newTransaction);
                            if (typeof renderSalaryHistory === 'function') {
                                renderSalaryHistory(salaryTransactions);
                            }
                        }
                        
                        $('#selfAvailableBalance').text('₹' + Number(response.new_balance).toLocaleString('en-IN'));
                        $('#selfWithdrawAmount').val('');
                        $('#selfWithdrawRemark').val('');
                        
                        if (typeof refreshDashboardSummary === 'function') {
                            refreshDashboardSummary();
                        }
                        
                        Swal.fire('Success!', response.message, 'success');
                    } else {
                        Swal.fire('Error!', response.message, 'error');
                    }
                },
                error: function(xhr) {
                    let errorMsg = 'Something went wrong. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    Swal.fire('Error!', errorMsg, 'error');
                }
            });
        }
    });
});

// Load bank accounts when salary tab opens
$('#tab-salary').on('shown.bs.tab', function() {
    loadBankAccounts();
    if (typeof renderSalaryHistory === 'function') {
        renderSalaryHistory(salaryTransactions);
    }
});

// Initial load
loadBankAccounts();

function initializeDonutChart(data) { const el = document.getElementById('walletDonutChart'); if (el) donutChart = new ApexCharts(el, { chart: { type: 'donut', height: 320 }, series: [data.seller, data.buyer, data.frozen, data.pending], labels: ['Seller', 'Buyer', 'Frozen', 'Pending'], colors: ['#4e73df', '#1cc88a', '#e74a3b', '#f6c23e'], plotOptions: { pie: { donut: { size: '68%', labels: { value: { formatter: val => '₹' + Number(val).toLocaleString('en-IN') }, total: { formatter: w => '₹' + w.globals.seriesTotals.reduce((a,b) => a+b, 0).toLocaleString('en-IN') } } } } }, legend: { position: 'bottom' }, tooltip: { y: { formatter: val => '₹' + Number(val).toLocaleString('en-IN') } } }).render(); }
function initializeTrendChart(data) { const el = document.getElementById('walletTrendChart'); if (el) trendChart = new ApexCharts(el, { chart: { type: 'area', height: 320, toolbar: { show: false } }, series: [{ name: 'Balance', data: data.map(i => i.balance) }], colors: ['#4e73df'], fill: { type: 'gradient' }, stroke: { curve: 'smooth', width: 3 }, markers: { size: 5 }, xaxis: { categories: data.map(i => i.date) }, yaxis: { labels: { formatter: val => '₹' + (val / 100000).toFixed(1) + 'L' } }, tooltip: { y: { formatter: val => '₹' + Number(val).toLocaleString('en-IN') } } }).render(); }
</script>

<style>
.wallet-nav-tabs { display: flex; gap: 6px; border: none; background: #fff; padding: 6px; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.06); }
.wallet-nav-tabs .nav-link { border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; font-size: 13px; color: #5a6a85; background: transparent; transition: all .25s ease; }
.wallet-nav-tabs .nav-link:hover { color: #4e73df; background: #eef2ff; }
.wallet-nav-tabs .nav-link.active { color: #fff; background: linear-gradient(135deg, #4e73df 0%, #224abe 100%); box-shadow: 0 3px 10px rgba(78,115,223,.35); }
.wallet-stat-card { display: flex; align-items: center; padding: 22px 24px; border-radius: 14px; background: #fff; border-left: 4px solid transparent; box-shadow: 0 1px 6px rgba(0,0,0,.06); transition: transform .2s; }
.wallet-stat-card:hover { transform: translateY(-3px); box-shadow: 0 8px 25px rgba(0,0,0,.1); }
.stat-total { border-left-color: #4e73df; }
.stat-seller { border-left-color: #1cc88a; }
.stat-buyer { border-left-color: #f6c23e; }
.stat-frozen { border-left-color: #e74a3b; }
.stat-pending { border-left-color: #6f42c1; }
.stat-icon-wrap { flex-shrink: 0; margin-right: 18px; }
.stat-icon { width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 26px; }
.stat-total .stat-icon { background: #eef2ff; color: #4e73df; }
.stat-seller .stat-icon { background: #e6f9f0; color: #1cc88a; }
.stat-buyer .stat-icon { background: #fef5e0; color: #f6c23e; }
.stat-frozen .stat-icon { background: #fce8e6; color: #e74a3b; }
.stat-pending .stat-icon { background: #ece5f6; color: #6f42c1; }
.stat-label { font-size: 12px; text-transform: uppercase; letter-spacing: .5px; color: #94a3b8; font-weight: 600; }
.stat-value { font-size: 22px; font-weight: 700; margin: 4px 0 2px; color: #1e293b; }
.stat-badge { display: inline-block; font-size: 11px; padding: 2px 10px; border-radius: 20px; background: #ece5f6; color: #6f42c1; font-weight: 600; margin-bottom: 4px; }
.wallet-card { border: none; border-radius: 14px; box-shadow: 0 1px 6px rgba(0,0,0,.06); }
.wallet-card .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; padding: 16px 20px; border-radius: 14px 14px 0 0; }
.wallet-card .card-title { font-weight: 700; font-size: 15px; color: #1e293b; }
.wallet-table thead th { background: #f8fafc; font-weight: 600; font-size: 12px; text-transform: uppercase; color: #5a6a85; border-bottom: 2px solid #e2e8f0; padding: 10px 12px; white-space: nowrap; }
.wallet-table tbody td { padding: 10px 12px; font-size: 13px; color: #334155; border-bottom: 1px solid #f1f5f9; }
.wallet-table tbody tr:hover { background: #f8fafd; }
.wallet-table code { background: #f1f5f9; color: #475569; padding: 2px 8px; border-radius: 4px; font-size: 12px; }
.badge.bg-soft-primary { background: #e8eefb !important; color: #4e73df !important; }
.badge.bg-soft-success { background: #e6f9f0 !important; color: #1cc88a !important; }
.badge.bg-soft-warning { background: #fef5e0 !important; color: #d4a017 !important; }
.badge.bg-soft-danger { background: #fce8e6 !important; color: #e74a3b !important; }
.badge.bg-soft-info { background: #e0f4ff !important; color: #36b9cc !important; }
.withdrawal-sub-tabs .nav-link { border: none; color: #5a6a85; font-size: 13px; font-weight: 600; padding: 8px 18px; border-radius: 6px 6px 0 0; }
.withdrawal-sub-tabs .nav-link.active { color: #4e73df; background: #fff; border-bottom: 2px solid #4e73df; }
.control-card { border: 1px solid #e2e8f0; transition: box-shadow .2s; }
.control-card:hover { box-shadow: 0 4px 15px rgba(0,0,0,.08); }
.control-card .nav-tabs { border-bottom: 1px solid #e2e8f0; gap: 8px; }
.control-card .nav-tabs .nav-link { border: none; color: #5a6a85; font-size: 13px; font-weight: 600; padding: 8px 16px; border-radius: 6px; background: transparent; }
.control-card .nav-tabs .nav-link:hover { color: #4e73df; background: #eef2ff; }
.control-card .nav-tabs .nav-link.active { color: #4e73df; background: #eef2ff; border-bottom: 2px solid #4e73df; }
.pagination .page-link { border-radius: 6px; margin: 0 2px; border: 1px solid #e2e8f0; color: #5a6a85; font-size: 12px; padding: 4px 10px; }
.pagination .page-item.active .page-link { background: #4e73df; border-color: #4e73df; color: #fff; }
.form-check { margin-bottom: 8px; }
#teamMemberList .form-check { padding-left: 1.5em; }
#teamMemberList .form-check-input { margin-left: -1.5em; }
#selectedCountDisplay { font-size: 12px; }
@media (max-width: 768px) { .wallet-nav-tabs { flex-wrap: wrap; } .wallet-nav-tabs .nav-link { font-size: 12px; padding: 8px 14px; } .stat-value { font-size: 18px; } .wallet-stat-card { padding: 16px; } }
</style>



<div class="modal fade" id="refundDetailModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Refund Details</h5>
                <button type="button"
                        class="btn-close btn-close-white"
                        data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="refundModalBody">
                Loading...
            </div>

            <div class="modal-footer">
                <button type="button"
                        class="btn btn-secondary"
                        data-bs-dismiss="modal">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
@endsection