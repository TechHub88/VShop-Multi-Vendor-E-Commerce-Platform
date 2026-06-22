@extends('layouts.vertical', ['title' => 'Referral System'])

@section('content')
<style>
    /* Base styles (same as reward config) */
    .ref-container {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fc;
        padding: 24px;
        min-height: 100vh;
    }
    .ref-page-header {
        background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
        border-radius: 12px;
        padding: 20px 28px;
        margin-bottom: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .ref-page-title {
        color: white;
        font-size: 20px;
        font-weight: 800;
        letter-spacing: 1px;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .ref-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 28px;
    }
    .ref-stat-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        text-align: center;
    }
    .ref-stat-value {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }
    .ref-stat-label {
        font-size: 13px;
        color: #64748b;
        margin-top: 8px;
    }
    .ref-tabs {
        display: flex;
        gap: 8px;
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 24px;
        flex-wrap: wrap;
    }
    .ref-tab-btn {
        padding: 10px 20px;
        background: none;
        border: none;
        font-size: 14px;
        font-weight: 600;
        color: #64748b;
        cursor: pointer;
        border-radius: 8px 8px 0 0;
        transition: all 0.2s;
    }
    .ref-tab-btn.active {
        color: #5b45f4;
        border-bottom: 2px solid #5b45f4;
        background: #f8fafc;
    }
    .ref-tab-pane {
        display: none;
    }
    .ref-tab-pane.active {
        display: block;
    }
    .btn-primary-custom {
        background: #5b45f4;
        color: white;
        border: none;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 500;
    }
    .btn-primary-custom:hover { background: #4a36d9; }
    .table-action-icons {
        display: flex;
        gap: 10px;
    }
    .table-action-icons iconify-icon {
        font-size: 18px;
        cursor: pointer;
        color: #94a3b8;
    }
    .table-action-icons iconify-icon:hover { color: #5b45f4; }
    .status-badge {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
    }
    .status-active { background: #ecfdf5; color: #10b981; border: 1px solid #a7f3d0; }
    .status-inactive { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
    .fraud-metric {
        background: #f8fafc;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 20px;
    }
    .fraud-metric h4 { font-size: 14px; font-weight: 700; margin-bottom: 12px; }
    canvas { max-height: 300px; }
</style>

<div class="ref-container">
    <!-- Header -->
    <div class="ref-page-header">
        <h2 class="ref-page-title">
            <iconify-icon icon="solar:hand-shake-bold-duotone"></iconify-icon>
            Referral System
        </h2>
    </div>

    <!-- Stats Cards (populated from backend) -->
    <div class="ref-stats-grid" id="statsGrid">
        <div class="ref-stat-card"><div class="ref-stat-value" id="totalReferrals">-</div><div class="ref-stat-label">Total Referrals</div></div>
        <div class="ref-stat-card"><div class="ref-stat-value" id="activeReferrers">-</div><div class="ref-stat-label">Active Referrers</div></div>
        <div class="ref-stat-card"><div class="ref-stat-value" id="ordersViaReferral">-</div><div class="ref-stat-label">Orders via Referral</div></div>
        <div class="ref-stat-card"><div class="ref-stat-value" id="referralRevenue">-</div><div class="ref-stat-label">Referral Revenue</div></div>
        <div class="ref-stat-card"><div class="ref-stat-value" id="payoutsMade">-</div><div class="ref-stat-label">Payouts Made</div></div>
    </div>

    <!-- Main Tabs -->
    <div class="ref-tabs">
        <button class="ref-tab-btn active" data-tab="overview">Overview & Top Referrers</button>
        <button class="ref-tab-btn" data-tab="reward-config">Reward Configuration</button>
        <button class="ref-tab-btn" data-tab="campaigns">Campaign Management</button>
        <button class="ref-tab-btn" data-tab="fraud-analytics">Fraud Analytics</button>
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
                <div class="mb-3"><label>Reward Type</label><div><label><input type="radio" name="catRewardType" value="fixed" checked> Fixed</label>&nbsp;&nbsp;<label><input type="radio" name="catRewardType" value="percentage"> Percentage</label></div></div>
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
                <div class="mb-3"><label>Reward (₹)</label><input type="number" id="prodReward" class="form-control"></div>
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
                <div class="mb-3"><label>Budget</label><input type="number" id="campaignBudget" class="form-control"></div>
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
    if (!toast) { toast = document.createElement('div'); toast.id = 'toastMsg'; toast.style.cssText = 'position:fixed; bottom:20px; right:20px; background:#0f172a; color:white; padding:12px 24px; border-radius:8px; z-index:9999; display:none;'; document.body.appendChild(toast); }
    toast.textContent = msg;
    toast.style.backgroundColor = isErr ? '#dc2626' : '#0f172a';
    toast.style.display = 'block';
    setTimeout(() => toast.style.display = 'none', 3000);
}

// ========== Load Overview Data ==========
async function loadStatsAndTopReferrers() {
    try {
        const res = await fetch(baseUrl + '/system-stats');
        if (res.ok) {
            const data = await res.json();
            document.getElementById('totalReferrals').innerText = data.total_referrals || '0';
            document.getElementById('activeReferrers').innerText = data.active_referrers || '0';
            document.getElementById('ordersViaReferral').innerText = data.orders_via_referral || '0';
            document.getElementById('referralRevenue').innerText = data.referral_revenue || '₹0';
            document.getElementById('payoutsMade').innerText = data.payouts_made || '₹0';
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
        tbody.innerHTML += `<tr>
            <td>${catName}</td>
            <td>${item.reward_type === 'fixed' ? 'Fixed' : 'Percentage'}</td>
            <td>${item.reward_type === 'fixed' ? '₹'+item.reward_value : item.reward_value+'%'}</td>
            <td><span class="status-badge status-${item.status}">${item.status}</span></td>
            <td class="table-action-icons">
                <iconify-icon icon="mdi:pencil" class="edit-cat" data-id="${item.id}" data-cat="${item.category_id}" data-type="${item.reward_type}" data-value="${item.reward_value}" data-status="${item.status}"></iconify-icon>
                <iconify-icon icon="mdi:delete" class="delete-cat" data-id="${item.id}"></iconify-icon>
            </td>
        </tr>`;
    });
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
                    tbody.innerHTML += `<tr><td>${c.name}</td><td>${c.type}</td><td>${c.start_date}</td><td>${c.end_date || '-'}</td><td>₹${c.budget}</td><td>${c.conversions}</td><td><span class="status-badge status-${c.status}">${c.status}</span></td><td class="table-action-icons"><iconify-icon icon="mdi:pencil"></iconify-icon><iconify-icon icon="mdi:delete"></iconify-icon></td></tr>`;
                });
            }
        }
    } catch(e) { console.warn(e); }
}
document.getElementById('saveCampaignBtn').onclick = async () => {
    const payload = { name: document.getElementById('campaignName').value, type: document.getElementById('campaignType').value, start_date: document.getElementById('campaignStart').value, end_date: document.getElementById('campaignEnd').value, budget: document.getElementById('campaignBudget').value, reward: document.getElementById('campaignReward').value };
    await fetch(baseUrl + '/campaigns', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': refCsrfToken }, body: JSON.stringify(payload) });
    bootstrap.Modal.getInstance(document.getElementById('addCampaignModal')).hide();
    loadCampaigns();
};

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
})();
</script>
@endsection