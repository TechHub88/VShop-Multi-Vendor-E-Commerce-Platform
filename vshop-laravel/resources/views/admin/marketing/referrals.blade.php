@extends('layouts.vertical', ['title' => 'Referral System'])

@section('content')
<style>
    /* Base styles */
    .ref-container {
        font-family: 'Inter', sans-serif;
        background-color: #f0f2f8;
        padding: 24px;
        min-height: 100vh;
    }
    .ref-page-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        border-radius: 14px;
        padding: 22px 30px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 4px 20px rgba(15,23,42,0.25);
    }
    .ref-page-title {
        color: white;
        font-size: 21px;
        font-weight: 800;
        letter-spacing: 1px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    /* ── KPI Stat Cards ── */
    .ref-stats-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 18px;
        margin-bottom: 28px;
    }
    @media (max-width: 1200px) { .ref-stats-grid { grid-template-columns: repeat(3, 1fr); } }
    @media (max-width: 768px) { .ref-stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 480px) { .ref-stats-grid { grid-template-columns: 1fr; } }

    .ref-stat-card {
        background: #fff;
        border-radius: 14px;
        padding: 22px 20px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        position: relative;
        overflow: hidden;
    }
    .ref-stat-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 5px;
        border-radius: 14px 0 0 14px;
    }
    .ref-stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 24px rgba(0,0,0,0.1);
    }
    .ref-stat-card .stat-icon-box {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .ref-stat-card .stat-icon-box i {
        font-size: 26px;
        color: #fff;
    }
    .ref-stat-info { flex: 1; }
    .ref-stat-value {
        font-size: 26px;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }
    .ref-stat-label {
        font-size: 12px;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 4px;
    }

    /* Card accent colors matching sidebar theme */
    .ref-stat-card.card-referrals::before { background: #3b82f6; }
    .ref-stat-card.card-referrals .stat-icon-box { background: linear-gradient(135deg, #3b82f6, #1d4ed8); }
    .ref-stat-card.card-active::before { background: #10b981; }
    .ref-stat-card.card-active .stat-icon-box { background: linear-gradient(135deg, #10b981, #059669); }
    .ref-stat-card.card-orders::before { background: #8b5cf6; }
    .ref-stat-card.card-orders .stat-icon-box { background: linear-gradient(135deg, #8b5cf6, #6d28d9); }
    .ref-stat-card.card-revenue::before { background: #f59e0b; }
    .ref-stat-card.card-revenue .stat-icon-box { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .ref-stat-card.card-payouts::before { background: #ef4444; }
    .ref-stat-card.card-payouts .stat-icon-box { background: linear-gradient(135deg, #ef4444, #dc2626); }

    /* ── Tabs ── */
    .ref-tabs {
        display: flex;
        gap: 6px;
        border-bottom: 2px solid #e2e8f0;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .ref-tab-btn {
        padding: 11px 22px;
        background: none;
        border: none;
        font-size: 13.5px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border-radius: 10px 10px 0 0;
        transition: all 0.2s;
        position: relative;
    }
    .ref-tab-btn:hover { color: #1e3a8a; background: #f1f5f9; }
    .ref-tab-btn.active {
        color: #1e3a8a;
        background: #fff;
        border-bottom: 3px solid #1e3a8a;
    }
    .ref-tab-pane { display: none; }
    .ref-tab-pane.active { display: block; }
    .btn-primary-custom {
        background: linear-gradient(135deg, #0f172a, #1e3a8a);
        color: white;
        border: none;
        padding: 8px 18px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 600;
        transition: all 0.2s;
    }
    .btn-primary-custom:hover { background: linear-gradient(135deg, #1e3a8a, #3b82f6); transform: translateY(-1px); }
    .table-action-icons { display: flex; gap: 10px; }
    .table-action-icons iconify-icon { font-size: 18px; cursor: pointer; color: #94a3b8; }
    .table-action-icons iconify-icon:hover { color: #1e3a8a; }
    .status-badge { padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: 600; display: inline-block; }
    .status-active { background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; }
    .status-inactive { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
    .fraud-metric { background: #f8fafc; border-radius: 12px; padding: 16px; margin-bottom: 20px; }
    .fraud-metric h4 { font-size: 14px; font-weight: 700; margin-bottom: 12px; }
    canvas { max-height: 300px; }

    /* Card & table refinements */
    .card { border: none; border-radius: 14px; box-shadow: 0 2px 12px rgba(0,0,0,0.05); }
    .card-header { background: #fff !important; border-bottom: 1px solid #e2e8f0; padding: 16px 20px; font-size: 15px; }
    .table th { font-size: 12px; text-transform: uppercase; color: #64748b; letter-spacing: 0.5px; font-weight: 700; }
    .table td { font-size: 13.5px; vertical-align: middle; }
</style>

<div class="ref-container">
    <!-- Header -->
    <div class="ref-page-header">
        <h2 class="ref-page-title">
            <iconify-icon icon="solar:hand-shake-bold-duotone"></iconify-icon>
            Referral System
        </h2>
    </div>

    <!-- KPI Stats Cards -->
    <div class="ref-stats-grid" id="statsGrid">
        <div class="ref-stat-card card-referrals">
            <div class="stat-icon-box"><i class="mdi mdi-account-group-outline"></i></div>
            <div class="ref-stat-info">
                <div class="ref-stat-value" id="totalReferrals">0</div>
                <div class="ref-stat-label">Total Referrals</div>
            </div>
        </div>
        <div class="ref-stat-card card-active">
            <div class="stat-icon-box"><i class="mdi mdi-account-check-outline"></i></div>
            <div class="ref-stat-info">
                <div class="ref-stat-value" id="activeReferrers">0</div>
                <div class="ref-stat-label">Active Referrers</div>
            </div>
        </div>
        <div class="ref-stat-card card-orders">
            <div class="stat-icon-box"><i class="mdi mdi-cart-check"></i></div>
            <div class="ref-stat-info">
                <div class="ref-stat-value" id="ordersViaReferral">0</div>
                <div class="ref-stat-label">Orders via Referral</div>
            </div>
        </div>
        <div class="ref-stat-card card-revenue">
            <div class="stat-icon-box"><i class="mdi mdi-cash-multiple"></i></div>
            <div class="ref-stat-info">
                <div class="ref-stat-value money" id="referralRevenue" data-amount="0">₹0</div>
                <div class="ref-stat-label">Referral Revenue</div>
            </div>
        </div>
        <div class="ref-stat-card card-payouts">
            <div class="stat-icon-box"><i class="mdi mdi-bank-transfer-out"></i></div>
            <div class="ref-stat-info">
                <div class="ref-stat-value money" id="payoutsMade" data-amount="0">₹0</div>
                <div class="ref-stat-label">Payouts Made</div>
            </div>
        </div>
    </div>

    <!-- Main Tabs -->
    <div class="ref-tabs">
        <button class="ref-tab-btn active" data-tab="overview">Overview & Top Referrers</button>
        <button class="ref-tab-btn" data-tab="reward-config">Reward Configuration</button>
        <button class="ref-tab-btn" data-tab="campaigns">Campaign Management</button>
        <button class="ref-tab-btn" data-tab="fraud-analytics">Fraud Analytics</button>
        <button class="ref-tab-btn" data-tab="transactions">Referral Transactions</button>
        <button class="ref-tab-btn" data-tab="configurations">Referral Configurations</button>
    </div>

    <!-- ==================== TAB 1: OVERVIEW ==================== -->
    <div id="tab-overview" class="ref-tab-pane active">
        <div class="card">
            <div class="card-header bg-white fw-bold">🏆 Top Referrers</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>User</th><th>Parent</th><th>Level</th></tr>
                    </thead>
                    <tbody id="topReferrersTable">
                        <tr><td colspan="3" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== TAB: TRANSACTIONS ==================== -->
    <div id="tab-transactions" class="ref-tab-pane">
        <div class="card">
            <div class="card-header bg-white fw-bold">Detailed Referral Transactions</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Referrer (Buyer A)</th>
                            <th>Customer (Buyer B)</th>
                            <th>Seller</th>
                            <th>Order ID</th>
                            <th>Commission</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="referralTransactionsTable">
                        <tr><td colspan="7" class="text-center">Loading transactions...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ==================== TAB 2: REWARD CONFIGURATION ==================== -->
    <div id="tab-reward-config" class="ref-tab-pane">

        <!-- Customer Reward Redemption Tiers -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">💎 Customer Reward Redemption Tiers</h5>
            <button class="btn-primary-custom" id="addTierBtn" data-bs-toggle="modal" data-bs-target="#addRewardTierModal">+ Add Tier</button>
        </div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Points Required</th><th>Discount %</th><th>Expiry (Days)</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody id="rewardTiersTable">
                    <tr><td colspan="5" class="text-center">Loading...</td></tr>
                </tbody>
            </table>
        </div>

        <!-- Category Rewards Section -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">📁 Category-wise Rewards</h5>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addCategoryRewardModal">+ Add Category Reward</button>
        </div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Category</th><th>Reward Type</th><th>Reward Value</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody id="categoryRewardsTable"></tbody>
            </table>
        </div>

        <!-- Product Campaigns Section -->
        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <h5 class="mb-0">🎯 Product-wise Campaigns</h5>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addProductCampaignModal">+ Add Campaign</button>
        </div>
        <div class="table-responsive mb-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Product</th><th>Reward (₹)</th><th>Start Date</th><th>End Date</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody id="productCampaignsTable"></tbody>
            </table>
        </div>

        <!-- Level Commissions -->
        <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
            <h5 class="mb-0">📊 Multi‑Level Payout (Commission)</h5>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addLevelModal">+ Add Level</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Level</th><th>Commission (%)</th><th>Description</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody id="levelCommissionTable"></tbody>
            </table>
        </div>
    </div>

    <!-- ==================== TAB 3: CAMPAIGN MANAGEMENT ==================== -->
    <div id="tab-campaigns" class="ref-tab-pane">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>📢 Active Referral Campaigns</h5>
            <button class="btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addCampaignModal">+ Create Campaign</button>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr><th>Campaign Name</th><th>Type</th><th>Start Date</th><th>End Date</th><th>Budget</th><th>Conversions</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody id="campaignsTable">
                    <tr><td colspan="8" class="text-center">Loading campaigns...</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- ==================== TAB 4: FRAUD ANALYTICS ==================== -->
    <div id="tab-fraud-analytics" class="ref-tab-pane">
        <div class="row">
            <div class="col-md-6">
                <div class="fraud-metric">
                    <h4>⚠️ Suspicious Referrals (Last 30 Days)</h4>
                    <canvas id="fraudChart" width="400" height="200"></canvas>
                </div>
            </div>
            <div class="col-md-6">
                <div class="fraud-metric">
                    <h4>🔍 Top Fraud Indicators</h4>
                    <ul id="fraudIndicatorsList" class="list-group">
                        <li class="list-group-item">Loading...</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="fraud-metric">
            <h4>📋 Recent Flagged Referrals</h4>
            <table class="table table-sm">
                <thead><tr><th>Referrer</th><th>Referred User</th><th>Flag Reason</th><th>Date</th></tr></thead>
                <tbody id="flaggedReferralsTable"></tbody>
            </table>
        </div>
    </div>

    <!-- ==================== TAB 5: REFERRAL CONFIGURATIONS ==================== -->
    <div id="tab-configurations" class="ref-tab-pane">
        <div class="card">
            <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
                <span>⚙️ Referral Configurations (Buyer-to-Buyer / Seller-to-Seller)</span>
                <button type="button" class="btn btn-sm btn-primary" onclick="addRefConfigRow()">+ Add Row</button>
            </div>
            <div class="card-body p-0">
                <form id="refConfigForm">
                    <table class="table table-hover mb-0" id="refConfigTable">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Target (Seller/Product)</th>
                                <th>Referrer Reward (Buyer A / Seller)</th>
                                <th>Referee Discount (Buyer B)</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Dynamic rows will be inserted here -->
                        </tbody>
                    </table>
                    <div class="p-3 text-end">
                        <button type="button" class="btn btn-success" onclick="saveRefConfigs()">Save Configurations</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header bg-white fw-bold">Existing Configurations</div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Type</th>
                            <th>Target Seller (ID & Name)</th>
                            <th>Referrer Reward</th>
                            <th>Referee Discount</th>
                        </tr>
                    </thead>
                    <tbody id="existingRefConfigsList">
                        <tr><td colspan="4" class="text-center">Loading...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ========== MODALS ========== -->

<!-- Reward Tier Modal -->
<div class="modal fade" id="addRewardTierModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Reward Tier</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" id="tierEditId">
                <div class="mb-3"><label>Points Required</label><input type="number" id="tierPoints" class="form-control" placeholder="e.g. 100"></div>
                <div class="mb-3"><label>Discount Percentage (%)</label><input type="number" step="0.01" id="tierDiscount" class="form-control" placeholder="e.g. 5"></div>
                <div class="mb-3"><label>Expiry Days</label><input type="number" id="tierExpiry" class="form-control" value="180"></div>
                <div class="mb-3"><label>Status</label><select id="tierStatus" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn-primary-custom" id="saveRewardTierBtn">Save</button></div>
        </div>
    </div>
</div>

<!-- Category Reward Modal -->
<div class="modal fade" id="addCategoryRewardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Add Category Reward</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" id="catEditId">
                <div class="mb-3"><label>Category</label><select id="catSelect" class="form-select"><option>Loading...</option></select></div>
                <div class="mb-3"><label>Reward Type</label><div><label><input type="radio" name="catRewardType" value="fixed" checked> Fixed Amount</label>&nbsp;&nbsp;<label><input type="radio" name="catRewardType" value="percentage"> Percentage</label></div></div>
                <div class="mb-3"><label>Reward Value</label><input type="number" id="catRewardValue" class="form-control"></div>
                <div class="mb-3"><label>Status</label><select id="catStatus" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn-primary-custom" id="saveCategoryRewardBtn">Save</button></div>
        </div>
    </div>
</div>

<!-- Product Campaign Modal -->
<div class="modal fade" id="addProductCampaignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Add Product Campaign</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" id="prodEditId">
                <div class="mb-3"><label>Product</label><select id="prodSelect" class="form-select"><option>Loading...</option></select></div>
                <div class="mb-3"><label>Reward Value</label><input type="number" id="prodReward" class="form-control"></div>
                <div class="mb-3"><label>Start Date</label><input type="date" id="prodStart" class="form-control"></div>
                <div class="mb-3"><label>End Date</label><input type="date" id="prodEnd" class="form-control"></div>
                <div class="mb-3"><label>Status</label><select id="prodStatus" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn-primary-custom" id="saveProductCampaignBtn">Save</button></div>
        </div>
    </div>
</div>

<!-- Level Commission Modal -->
<div class="modal fade" id="addLevelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Add Commission Level</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <input type="hidden" id="levelEditId">
                <div class="mb-3"><label>Level Name</label><input type="text" id="levelName" class="form-control" placeholder="e.g., Level 4"></div>
                <div class="mb-3"><label>Commission (%)</label><input type="number" id="levelCommission" class="form-control"></div>
                <div class="mb-3"><label>Description</label><input type="text" id="levelDesc" class="form-control"></div>
                <div class="mb-3"><label>Status</label><select id="levelStat" class="form-select"><option value="active">Active</option><option value="inactive">Inactive</option></select></div>
            </div>
            <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn-primary-custom" id="saveLevelBtn">Save</button></div>
        </div>
    </div>
</div>

<!-- Campaign Modal -->
<div class="modal fade" id="addCampaignModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white"><h5 class="modal-title">Create Referral Campaign</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div>
            <div class="modal-body">
                <div class="mb-3"><label>Campaign Name</label><input type="text" id="campaignName" class="form-control"></div>
                <div class="mb-3"><label>Type</label><select id="campaignType" class="form-select"><option>Standard</option><option>Limited Time</option><option>First Purchase</option></select></div>
                <div class="mb-3"><label>Start Date</label><input type="date" id="campaignStart" class="form-control"></div>
                <div class="mb-3"><label>End Date</label><input type="date" id="campaignEnd" class="form-control"></div>
                <div class="mb-3"><label>Budget Value</label><input type="number" id="campaignBudget" class="form-control"></div>
                <div class="mb-3"><label>Reward Per Referral</label><input type="number" id="campaignReward" class="form-control"></div>
            </div>
            <div class="modal-footer"><button class="btn btn-light" data-bs-dismiss="modal">Cancel</button><button class="btn-primary-custom" id="saveCampaignBtn">Save Campaign</button></div>
        </div>
    </div>
</div>

<script src="https://code.iconify.design/iconify-icon/1.0.7/iconify-icon.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ========== Global variables ==========
var refCsrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
var baseUrl = '/admin/referral-api'; 

// ========== Helper ==========
async function apiRequest(endpoint, method, data = null) {
    const opts = { method, headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': refCsrfToken, 'Accept': 'application/json' } };
    if (data) opts.body = JSON.stringify(data);
    const res = await fetch(baseUrl + endpoint, opts);
    if (!res.ok) throw new Error(await res.text());
    return res.json();
}

function showToast(msg, isErr = false) {
    let toast = document.getElementById('toastMsg');
    if (!toast) { 
        toast = document.createElement('div'); 
        toast.id = 'toastMsg'; 
        toast.style.cssText = 'position:fixed; top:20px; right:20px; background:#198754; color:white; padding:12px 24px; border-radius:8px; z-index:9999; display:none; font-weight:500; box-shadow: 0 4px 6px rgba(0,0,0,0.1);'; 
        document.body.appendChild(toast); 
    }
    toast.textContent = msg;
    toast.style.backgroundColor = isErr ? '#dc2626' : '#198754';
    toast.style.display = 'block';
    setTimeout(() => toast.style.display = 'none', 3000);
}

// ========== Helper: Format Money ==========
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

// ========== Load Overview Data ==========
async function loadStatsAndTopReferrers() {
    try {
        const res = await fetch(baseUrl + '/system-stats');
        if (res.ok) {
            const data = await res.json();
            document.getElementById('totalReferrals').innerText = data.total_referrals || '0';
            document.getElementById('activeReferrers').innerText = data.active_referrers || '0';
            document.getElementById('ordersViaReferral').innerText = data.orders_via_referral || '0';
            
            // Set raw amount for currency conversion
            const revenueEl = document.getElementById('referralRevenue');
            const payoutsEl = document.getElementById('payoutsMade');
            revenueEl.setAttribute('data-amount', data.referral_revenue_raw || 0);
            payoutsEl.setAttribute('data-amount', data.payouts_made_raw || 0);
            revenueEl.innerText = data.referral_revenue || '₹0';
            payoutsEl.innerText = data.payouts_made || '₹0';
            
            formatLocalMoneyElements();
        }
    } catch(e) { console.warn(e); }
    try {
        const res2 = await fetch(baseUrl + '/top-referrers');
        if (res2.ok) {
            const referrers = await res2.json();
            const tbody = document.getElementById('topReferrersTable');
            tbody.innerHTML = '';
            referrers.forEach(r => {
                tbody.innerHTML += `<tr><td>${r.user}</td><td>${r.parent}</td><td>${r.level}</td></tr>`;
            });
        }
    } catch(e) { console.warn(e); }
}

// ========== Reward Configuration CRUD ==========
let categories = [], products = [];

async function loadCategories() {
    const res = await fetch(baseUrl + '/categories');
    if (res.ok) categories = await res.json();
    const sel = document.getElementById('catSelect');
    sel.innerHTML = '<option value="">Select Category</option>';
    categories.forEach(c => sel.appendChild(new Option(c.name, c.id)));
}
async function loadProducts() {
    const res = await fetch(baseUrl + '/products');
    if (res.ok) products = await res.json();
    const sel = document.getElementById('prodSelect');
    sel.innerHTML = '<option value="">Select Product</option>';
    products.forEach(p => sel.appendChild(new Option(p.name, p.id)));
}

async function loadCategoryRewards() {
    const data = await apiRequest('/category-rewards', 'GET');
    const tbody = document.getElementById('categoryRewardsTable');
    tbody.innerHTML = '';
    data.forEach(item => {
        const cat = categories.find(c => c.id == item.category_id);
        const catName = cat ? cat.name : item.category_name;
        const rewardText = item.reward_type === 'fixed' ? `<span class="money" data-amount="${item.reward_value}">₹${item.reward_value}</span>` : `${item.reward_value}%`;
        tbody.innerHTML += `<tr>
            <td>${catName}</td>
            <td>${item.reward_type === 'fixed' ? 'Fixed' : 'Percentage'}</td>
            <td>${rewardText}</td>
            <td><span class="status-badge status-${item.status}">${item.status}</span></td>
            <td class="table-action-icons">
                <iconify-icon icon="mdi:pencil" class="edit-cat" data-id="${item.id}" data-cat="${item.category_id}" data-type="${item.reward_type}" data-value="${item.reward_value}" data-status="${item.status}"></iconify-icon>
                <iconify-icon icon="mdi:delete" class="delete-cat" data-id="${item.id}"></iconify-icon>
            </td>
        </tr>`;
    });
    formatLocalMoneyElements();
    attachCatActions();
}

async function loadRewardTiers() {
    try {
        const data = await apiRequest('/reward-tiers-list', 'GET');
        const tbody = document.getElementById('rewardTiersTable');
        tbody.innerHTML = '';
        data.forEach(item => {
            tbody.innerHTML += `<tr>
                <td>${item.points_required}</td>
                <td>${parseFloat(item.discount_percentage || 0).toFixed(2)}%</td>
                <td>${item.expiry_days || 0} days</td>
                <td><span class="status-badge status-${item.status}">${item.status}</span></td>
                <td class="table-action-icons">
                    <iconify-icon icon="mdi:pencil" class="edit-tier" data-id="${item.id}" data-pts="${item.points_required}" data-disc="${item.discount_percentage}" data-exp="${item.expiry_days}" data-status="${item.status}"></iconify-icon>
                    <iconify-icon icon="mdi:delete" class="delete-tier" data-id="${item.id}"></iconify-icon>
                </td>
            </tr>`;
        });
        attachTierActions();
    } catch(e) {
        console.warn(e);
        document.getElementById('rewardTiersTable').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Failed to load tiers</td></tr>';
    }
}

function attachTierActions() {
    document.querySelectorAll('.edit-tier').forEach(el => {
        el.onclick = () => {
            document.getElementById('tierEditId').value = el.dataset.id;
            document.getElementById('tierPoints').value = el.dataset.pts;
            document.getElementById('tierDiscount').value = el.dataset.disc;
            document.getElementById('tierExpiry').value = el.dataset.exp || 180;
            document.getElementById('tierStatus').value = el.dataset.status;
            new bootstrap.Modal(document.getElementById('addRewardTierModal')).show();
        };
    });
    document.querySelectorAll('.delete-tier').forEach(el => {
        el.onclick = async () => {
            if(confirm('Are you sure you want to delete this reward tier?')) {
                await apiRequest(`/reward-tier/${el.dataset.id}/delete`, 'POST');
                loadRewardTiers();
            }
        };
    });
}

document.getElementById('addTierBtn').onclick = () => {
    document.getElementById('tierEditId').value = '';
    document.getElementById('tierPoints').value = '';
    document.getElementById('tierDiscount').value = '';
    document.getElementById('tierExpiry').value = '180';
    document.getElementById('tierStatus').value = 'active';
    new bootstrap.Modal(document.getElementById('addRewardTierModal')).show();
};

document.getElementById('saveRewardTierBtn').onclick = async () => {
    try {
        const id = document.getElementById('tierEditId').value;
        const payload = {
            points_required: document.getElementById('tierPoints').value,
            discount_percentage: document.getElementById('tierDiscount').value,
            expiry_days: document.getElementById('tierExpiry').value,
            status: document.getElementById('tierStatus').value
        };
        if (id) await apiRequest(`/reward-tier/${id}`, 'POST', payload);
        else await apiRequest('/reward-tier', 'POST', payload);
        bootstrap.Modal.getInstance(document.getElementById('addRewardTierModal')).hide();
        loadRewardTiers();
        showToast('Reward tier saved successfully!');
    } catch(e) {
        alert('Error: ' + e.message);
    }
};

async function loadProductCampaigns() {
    const data = await apiRequest('/product-campaigns', 'GET');
    const tbody = document.getElementById('productCampaignsTable');
    tbody.innerHTML = '';
    data.forEach(item => {
        tbody.innerHTML += `<tr>
            <td>${item.product_name}</td>
            <td>₹${item.reward_value}</td>
            <td>${item.start_date || '-'}</td>
            <td>${item.end_date || '-'}</td>
            <td><span class="status-badge status-${item.status}">${item.status}</span></td>
            <td class="table-action-icons">
                <iconify-icon icon="mdi:pencil" class="edit-prod" data-id="${item.id}" data-prod="${item.product_id}" data-value="${item.reward_value}" data-start="${item.start_date}" data-end="${item.end_date}" data-status="${item.status}"></iconify-icon>
                <iconify-icon icon="mdi:delete" class="delete-prod" data-id="${item.id}"></iconify-icon>
            </td>
        </tr>`;
    });
    attachProdActions();
}
async function loadLevelCommissions() {
    const data = await apiRequest('/levels', 'GET');
    const tbody = document.getElementById('levelCommissionTable');
    tbody.innerHTML = '';
    data.forEach(item => {
        tbody.innerHTML += `<tr>
            <td>${item.level_name}</td>
            <td>${item.commission_percentage}%</td>
            <td>${item.description || ''}</td>
            <td><span class="status-badge status-${item.status}">${item.status}</span></td>
            <td class="table-action-icons">
                <iconify-icon icon="mdi:pencil" class="edit-level" data-id="${item.id}" data-name="${item.level_name}" data-perc="${item.commission_percentage}" data-desc="${item.description}" data-status="${item.status}"></iconify-icon>
                <iconify-icon icon="mdi:delete" class="delete-level" data-id="${item.id}"></iconify-icon>
            </td>
        </tr>`;
    });
    attachLevelActions();
}

// attach event handlers for edit/delete
function attachCatActions() {
    document.querySelectorAll('.edit-cat').forEach(el => {
        el.onclick = () => {
            document.getElementById('catEditId').value = el.dataset.id;
            document.getElementById('catSelect').value = el.dataset.cat;
            document.querySelector(`input[name="catRewardType"][value="${el.dataset.type}"]`).checked = true;
            document.getElementById('catRewardValue').value = el.dataset.value;
            document.getElementById('catStatus').value = el.dataset.status;
            new bootstrap.Modal(document.getElementById('addCategoryRewardModal')).show();
        };
    });
    document.querySelectorAll('.delete-cat').forEach(el => {
        el.onclick = async () => { if(confirm('Delete?')) { await apiRequest(`/category-rewards/${el.dataset.id}`, 'DELETE'); loadCategoryRewards(); } };
    });
}
function attachProdActions() {
    document.querySelectorAll('.edit-prod').forEach(el => {
        el.onclick = () => {
            document.getElementById('prodEditId').value = el.dataset.id;
            document.getElementById('prodSelect').value = el.dataset.prod;
            document.getElementById('prodReward').value = el.dataset.value;
            document.getElementById('prodStart').value = el.dataset.start || '';
            document.getElementById('prodEnd').value = el.dataset.end || '';
            document.getElementById('prodStatus').value = el.dataset.status;
            new bootstrap.Modal(document.getElementById('addProductCampaignModal')).show();
        };
    });
    document.querySelectorAll('.delete-prod').forEach(el => {
        el.onclick = async () => { if(confirm('Delete?')) { await apiRequest(`/product-campaigns/${el.dataset.id}`, 'DELETE'); loadProductCampaigns(); } };
    });
}
function attachLevelActions() {
    document.querySelectorAll('.edit-level').forEach(el => {
        el.onclick = () => {
            document.getElementById('levelEditId').value = el.dataset.id;
            document.getElementById('levelName').value = el.dataset.name;
            document.getElementById('levelCommission').value = el.dataset.perc;
            document.getElementById('levelDesc').value = el.dataset.desc;
            document.getElementById('levelStat').value = el.dataset.status;
            new bootstrap.Modal(document.getElementById('addLevelModal')).show();
        };
    });
    document.querySelectorAll('.delete-level').forEach(el => {
        el.onclick = async () => { if(confirm('Delete?')) { await apiRequest(`/levels/${el.dataset.id}`, 'DELETE'); loadLevelCommissions(); } };
    });
}

// Save handlers
document.getElementById('saveCategoryRewardBtn').onclick = async () => {
    const id = document.getElementById('catEditId').value;
    const payload = { category_id: document.getElementById('catSelect').value, reward_type: document.querySelector('input[name="catRewardType"]:checked').value, reward_value: document.getElementById('catRewardValue').value, status: document.getElementById('catStatus').value };
    if (id) await apiRequest(`/category-rewards/${id}`, 'PUT', payload);
    else await apiRequest('/category-rewards', 'POST', payload);
    bootstrap.Modal.getInstance(document.getElementById('addCategoryRewardModal')).hide();
    loadCategoryRewards();
};
document.getElementById('saveProductCampaignBtn').onclick = async () => {
    const id = document.getElementById('prodEditId').value;
    const payload = { product_id: document.getElementById('prodSelect').value, reward_value: document.getElementById('prodReward').value, start_date: document.getElementById('prodStart').value, end_date: document.getElementById('prodEnd').value, status: document.getElementById('prodStatus').value };
    if (id) await apiRequest(`/product-campaigns/${id}`, 'PUT', payload);
    else await apiRequest('/product-campaigns', 'POST', payload);
    bootstrap.Modal.getInstance(document.getElementById('addProductCampaignModal')).hide();
    loadProductCampaigns();
};
document.getElementById('saveLevelBtn').onclick = async () => {
    const id = document.getElementById('levelEditId').value;
    const payload = { level_name: document.getElementById('levelName').value, commission_percentage: document.getElementById('levelCommission').value, description: document.getElementById('levelDesc').value, status: document.getElementById('levelStat').value };
    if (id) await apiRequest(`/levels/${id}`, 'PUT', payload);
    else await apiRequest('/levels', 'POST', payload);
    bootstrap.Modal.getInstance(document.getElementById('addLevelModal')).hide();
    loadLevelCommissions();
};

// ========== Campaign Management (dynamic endpoints) ==========
async function loadCampaigns() {
    try {
        const res = await fetch(baseUrl + '/campaigns');
        if (res.ok) {
            const campaigns = await res.json();
            const tbody = document.getElementById('campaignsTable');
            tbody.innerHTML = '';
            if (campaigns.length === 0) {
                tbody.innerHTML = '<tr><td colspan="8" class="text-center">No campaigns found. Create one!</td></tr>';
            } else {
                campaigns.forEach(c => {
                    tbody.innerHTML += `<tr><td>${c.name}</td><td>${c.type}</td><td>${c.start_date}</td><td>${c.end_date || '-'}</td><td><span class="money" data-amount="${c.budget}">₹${c.budget}</span></td><td>${c.conversions}</td><td><span class="status-badge status-${c.status}">${c.status}</span></td><td class="table-action-icons"><iconify-icon icon="mdi:pencil"></iconify-icon><iconify-icon icon="mdi:delete"></iconify-icon></td></tr>`;
                });
            }
            formatLocalMoneyElements();
        }
    } catch(e) { console.warn(e); }
}
document.getElementById('saveCampaignBtn').onclick = async () => {
    const payload = { name: document.getElementById('campaignName').value, type: document.getElementById('campaignType').value, start_date: document.getElementById('campaignStart').value, end_date: document.getElementById('campaignEnd').value, budget: document.getElementById('campaignBudget').value, reward: document.getElementById('campaignReward').value };
    await fetch(baseUrl + '/campaigns', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': refCsrfToken }, body: JSON.stringify(payload) });
    bootstrap.Modal.getInstance(document.getElementById('addCampaignModal')).hide();
    loadCampaigns();
};

// ========== Transactions Management ==========
async function loadTransactions() {
    try {
        const res = await fetch(baseUrl + '/transactions');
        if (res.ok) {
            const txns = await res.json();
            const tbody = document.getElementById('referralTransactionsTable');
            tbody.innerHTML = '';
            if (txns.length === 0) {
                tbody.innerHTML = '<tr><td colspan="7" class="text-center">No transactions found.</td></tr>';
            } else {
                txns.forEach(t => {
                    tbody.innerHTML += `<tr>
                        <td>${new Date(t.created_at).toLocaleDateString()}</td>
                        <td><div class="fw-bold">${t.referrer?.name || 'N/A'}</div><div class="small">${t.referrer?.email || ''}</div></td>
                        <td><div class="fw-bold">${t.buyer?.name || 'N/A'}</div><div class="small">${t.buyer?.email || ''}</div></td>
                        <td><div class="fw-bold">${t.seller?.company_name || t.seller?.contact_persons || 'N/A'}</div></td>
                        <td>#${t.order_id}</td>
                        <td class="text-success fw-bold"><span class="money" data-amount="${t.amount}">₹${parseFloat(t.amount).toFixed(2)}</span></td>
                        <td><span class="badge bg-success">${t.status}</span></td>
                    </tr>`;
                });
            }
            formatLocalMoneyElements();
        }
    } catch(e) { console.warn(e); }
}

// ========== Fraud Analytics ==========
async function loadFraudAnalytics() {
    // Dummy chart data – replace with real API
    const ctx = document.getElementById('fraudChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: { labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'], datasets: [{ label: 'Suspicious Referrals', data: [5, 12, 8, 3], borderColor: '#ef4444' }] }
    });
    document.getElementById('fraudIndicatorsList').innerHTML = '<li class="list-group-item">Multiple referrals from same IP</li><li class="list-group-item">Unusual conversion speed</li><li class="list-group-item">Duplicate accounts</li>';
    document.getElementById('flaggedReferralsTable').innerHTML = '<tr><td>john_doe</td><td>jane_doe</td><td>Same device</td><td>2025-03-15</td></tr>';
}

// ========== Tab Switching ==========
document.querySelectorAll('.ref-tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.ref-tab-btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const tabId = btn.dataset.tab;
        document.querySelectorAll('.ref-tab-pane').forEach(p => p.classList.remove('active'));
        document.getElementById(`tab-${tabId}`).classList.add('active');
        if (tabId === 'campaigns') loadCampaigns();
        if (tabId === 'fraud-analytics') loadFraudAnalytics();
        if (tabId === 'transactions') loadTransactions();
    });
});

// ========== Initial Load ==========
(async function init() {
    await loadStatsAndTopReferrers();
    await loadCategories();
    await loadProducts();
    await loadRewardTiers();
    await loadCategoryRewards();
    await loadProductCampaigns();
    await loadLevelCommissions();
    document.querySelectorAll('.ref-tab-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.ref-tab-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.ref-tab-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById('tab-' + this.dataset.tab).classList.add('active');
            if (this.dataset.tab === 'configurations') {
                loadExistingRefConfigs();
            }
        });
    });

    window.sellersList = [];
    window.loadSellers = async function() {
        if (window.sellersList.length === 0) {
            try {
                const res = await fetch(baseUrl + '/sellers');
                if (res.ok) window.sellersList = await res.json();
            } catch(e) { console.warn(e); }
        }
    };

    window.addRefConfigRow = async function() {
        await window.loadSellers();
        let sellerOptions = '<option value="">Select Seller</option>';
        window.sellersList.forEach(s => {
            sellerOptions += `<option value="${s.id}">${s.name || s.org_name || 'Seller ' + s.id}</option>`;
        });

        const tbody = document.querySelector('#refConfigTable tbody');
        const rowCount = tbody.children.length;
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <select name="configs[${rowCount}][type]" class="form-select form-select-sm ref-config-type" required>
                    <option value="buyer_to_buyer">Buyer to Buyer</option>
                    <option value="seller_to_seller">Seller to Seller</option>
                </select>
            </td>
            <td>
                <select name="configs[${rowCount}][reference_id]" class="form-select form-select-sm" required>
                    ${sellerOptions}
                </select>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="configs[${rowCount}][commission_percentage]" class="form-control" placeholder="Reward % (e.g. 3)" required>
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td>
                <div class="input-group input-group-sm">
                    <input type="number" name="configs[${rowCount}][referee_discount]" class="form-control" placeholder="Discount % (e.g. 3)" required>
                    <span class="input-group-text">%</span>
                </div>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-danger" onclick="this.closest('tr').remove()"><i class="mdi mdi-delete"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
    };

    window.saveRefConfigs = async function() {
        const form = document.getElementById('refConfigForm');
        const formData = new FormData(form);
        
        try {
            const res = await fetch(baseUrl + '/referral-configurations/save', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': refCsrfToken,
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();
            if (res.ok) {
                showToast(data.message || 'Configurations saved successfully');
                document.querySelector('#refConfigTable tbody').innerHTML = ''; // clear form
                window.loadExistingRefConfigs();
            } else {
                showToast(data.message || 'Failed to save', true);
            }
        } catch(e) {
            console.error(e);
            showToast('Something went wrong', true);
        }
    };

    window.loadExistingRefConfigs = async function() {
        try {
            await window.loadSellers();
            const res = await fetch(baseUrl + '/referral-configurations');
            const data = await res.json();
            let html = '';
            if (data.length) {
                data.forEach(item => {
                    let extra = {};
                    try { extra = JSON.parse(item.description) || {}; } catch(e) {}
                    
                    let targetDisplay = item.reference_id || 'N/A';
                    if (item.reference_id) {
                        const seller = window.sellersList.find(s => s.id == item.reference_id);
                        if (seller) {
                            const sName = seller.name || seller.org_name || '';
                            targetDisplay = sName ? `${item.reference_id} - ${sName}` : item.reference_id;
                        }
                    }

                    html += `<tr>
                        <td>${item.type === 'buyer_to_buyer' ? 'Buyer to Buyer' : (item.type === 'seller_to_seller' ? 'Seller to Seller' : item.type)}</td>
                        <td>${targetDisplay}</td>
                        <td>${item.commission_percentage || 0}%</td>
                        <td>${extra.referee_discount || 0}%</td>
                    </tr>`;
                });
            } else {
                html = '<tr><td colspan="4" class="text-center text-muted">No configurations found</td></tr>';
            }
            document.getElementById('existingRefConfigsList').innerHTML = html;
        } catch(e) {
            console.error(e);
            document.getElementById('existingRefConfigsList').innerHTML = '<tr><td colspan="4" class="text-center text-danger">Failed to load</td></tr>';
        }
    };

    // Add initial row on load
    window.addRefConfigRow();
})();
</script>
@endsection