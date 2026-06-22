@extends('layouts.vertical', ['title' => 'Master System Control'])

@section('css')
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
    /* Your existing CSS unchanged (kept as is) */
    .tab-pane { padding-top: 1.5rem; }
    .metric-card { border-radius: 10px; transition: all 0.3s ease; border: none; box-shadow: 0 2px 6px rgba(0,0,0,0.04); }
    .metric-card:hover { transform: translateY(-3px); box-shadow: 0 6px 16px rgba(0,0,0,0.1); }
    .workflow-container { display: flex; align-items: center; justify-content: center; gap: 0; overflow-x: auto; padding: 2rem 1rem; background: linear-gradient(135deg, #f8f9fc 0%, #eef1f7 100%); border-radius: 10px; margin-bottom: 2rem; }
    .workflow-step { text-align: center; position: relative; flex: 0 0 auto; min-width: 130px; padding: 0 10px; }
    .workflow-circle { width: 64px; height: 64px; border-radius: 50%; background: #fff; border: 3px solid #dee2e6; display: inline-flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.3rem; margin-bottom: 0.5rem; transition: all 0.3s ease; position: relative; z-index: 2; box-shadow: 0 2px 8px rgba(0,0,0,0.06); }
    .workflow-arrow { display: inline-flex; align-items: center; color: #adb5bd; font-size: 1.5rem; margin: 0 -5px; padding-top: 0; position: relative; top: -18px; }
    .workflow-arrow i { font-size: 24px; }
    .workflow-step.active .workflow-circle { border-color: #6c5ffc; background: linear-gradient(135deg, #f0eeff, #e8e5ff); color: #6c5ffc; }
    .workflow-step.completed .workflow-circle { border-color: #10b981; background: linear-gradient(135deg, #e6f7f0, #d4f0e4); color: #10b981; }
    .workflow-step.warning .workflow-circle { border-color: #f59e0b; background: linear-gradient(135deg, #fef3c7, #fde68a); color: #f59e0b; }
    .workflow-step.suspended .workflow-circle { border-color: #ef4444; background: linear-gradient(135deg, #fee2e2, #fecaca); color: #ef4444; }
    .nav-tabs-custom .nav-link { border: none; color: #6c757d; font-weight: 500; padding: 12px 16px; border-radius: 0; border-bottom: 3px solid transparent; font-size: 13px; transition: all 0.2s ease; }
    .nav-tabs-custom .nav-link:hover { color: #004392; background: rgba(0, 67, 146, 0.1); }
    .nav-tabs-custom .nav-link.active { color: #004392 !important; border-bottom-color: #004392; background: rgba(0, 67, 146, 0.06); font-weight: 600; }
    .nav-tabs-custom { border-bottom: 2px solid #e9ecef; flex-wrap: nowrap; overflow-x: auto; overflow-y: hidden; }
    .nav-tabs-custom::-webkit-scrollbar { height: 3px; }
    .nav-tabs-custom::-webkit-scrollbar-thumb { background: #004392; border-radius: 3px; }
    .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
    .stat-card-sm { border-radius: 10px; padding: 18px 16px; text-align: center; transition: all 0.3s ease; border: none; }
    .stat-card-sm:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08); }
    .stat-card-sm h3 { margin-bottom: 2px; font-weight: 700; }
    .stat-card-sm .stat-icon { width: 40px; height: 40px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 1.2rem; margin-bottom: 8px; }
    .nav-pills-custom .nav-link { border-radius: 6px; padding: 6px 16px; font-size: 13px; font-weight: 500; color: #495057; background: #f1f3f5; margin-right: 6px; transition: all 0.2s ease; }
    .nav-pills-custom .nav-link.active { background: #6c5ffc; color: #fff; }
    .priority-badge { width: 32px; height: 32px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-weight: 700; font-size: 14px; color: #fff; }
    .table th { font-weight: 600; letter-spacing: 0.5px; }
    .table td { vertical-align: middle; }
    .btn-icon { width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 6px; }
    .info-box { background: linear-gradient(135deg, #e8f4fd 0%, #dbeafe 100%); border-left: 4px solid #3b82f6; border-radius: 8px; padding: 16px 20px; }
    .example-box { background: linear-gradient(135deg, #fef9e7 0%, #fef3c7 100%); border-left: 4px solid #f59e0b; border-radius: 8px; padding: 16px 20px; }
    /* New revenue chart container */
    .revenue-chart-container { max-width: 100%; height: 300px; margin-bottom: 1.5rem; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Master System Control</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Master System Control</a></li>
                        <li class="breadcrumb-item active" id="breadcrumb-active">Overview</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
<br>
<br>
    <div class="card">
        <div class="card-body p-0">
            <!-- Tabs Navigation (added two new tabs) -->
            <ul class="nav nav-tabs nav-tabs-custom nav-justified" id="masterSettingsTab" role="tablist">
                <li class="nav-item"><button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button"><i class="bx bx-home-alt fs-5 d-block text-center mb-1"></i> Overview</button></li>
                <li class="nav-item"><button class="nav-link" id="rule-priority-tab" data-bs-toggle="tab" data-bs-target="#rule-priority" type="button"><i class="bx bx-sort-up fs-5 d-block text-center mb-1"></i> Rule Priority</button></li>
                <li class="nav-item"><button class="nav-link" id="validation-rules-tab" data-bs-toggle="tab" data-bs-target="#validation-rules" type="button"><i class="bx bx-check-shield fs-5 d-block text-center mb-1"></i> Validation Rules</button></li>
                <li class="nav-item"><button class="nav-link" id="workflow-rules-tab" data-bs-toggle="tab" data-bs-target="#workflow-rules" type="button"><i class="bx bx-git-branch fs-5 d-block text-center mb-1"></i> Workflow Rules</button></li>
                <li class="nav-item"><button class="nav-link" id="automation-rules-tab" data-bs-toggle="tab" data-bs-target="#automation-rules" type="button"><i class="bx bx-network-chart fs-5 d-block text-center mb-1"></i> Automation Rules</button></li>
                <!-- NEW TABS -->
                <li class="nav-item"><button class="nav-link" id="revenue-engine-tab" data-bs-toggle="tab" data-bs-target="#revenue-engine" type="button"><i class="bx bx-line-chart fs-5 d-block text-center mb-1"></i> Revenue Engine</button></li>
                <li class="nav-item"><button class="nav-link" id="boost-revenue-tab" data-bs-toggle="tab" data-bs-target="#boost-revenue" type="button"><i class="bx bx-trending-up fs-5 d-block text-center mb-1"></i> Boost Revenue Engine</button></li>
            </ul>

            <!-- Tabs Content -->
            <div class="tab-content p-4" id="masterSettingsTabContent">

                <!-- ==================== OVERVIEW TAB (unchanged) ==================== -->
                <div class="tab-pane fade show active" id="overview" role="tabpanel">
                    <div class="row g-3">
                        <div class="col-xl-2 col-md-4 col-sm-6"><div class="card metric-card border-start border-4 border-primary"><div class="card-body text-center py-3"><h6 class="text-muted text-uppercase mb-2 fs-11">Rule Engine</h6><h2 class="mb-1 text-primary fw-bold" id="metric-rules-count">0</h2><span class="badge bg-primary bg-opacity-10 text-primary fs-11" id="metric-rules-badge">0 Active Rules</span></div></div></div>
                        <div class="col-xl-2 col-md-4 col-sm-6"><div class="card metric-card border-start border-4 border-info"><div class="card-body text-center py-3"><h6 class="text-muted text-uppercase mb-2 fs-11">Admin Role</h6><h2 class="mb-1 text-info fw-bold" id="metric-roles-count">0</h2><span class="badge bg-info bg-opacity-10 text-info fs-11">Roles</span></div></div></div>
                        <div class="col-xl-2 col-md-4 col-sm-6"><div class="card metric-card border-start border-4 border-warning"><div class="card-body text-center py-3"><h6 class="text-muted text-uppercase mb-2 fs-11">Compliance</h6><h2 class="mb-1 text-warning fw-bold" id="metric-compliance-count">0</h2><span class="badge bg-warning bg-opacity-10 text-warning fs-11">Policies</span></div></div></div>
                        <div class="col-xl-2 col-md-4 col-sm-6"><div class="card metric-card border-start border-4 border-danger"><div class="card-body text-center py-3"><h6 class="text-muted text-uppercase mb-2 fs-11">Integrations</h6><h2 class="mb-1 text-danger fw-bold" id="metric-notifications-count">0</h2><span class="badge bg-danger bg-opacity-10 text-danger fs-11" id="metric-notifications-badge">Active</span></div></div></div>
                        <div class="col-xl-2 col-md-4 col-sm-6"><div class="card metric-card border-0 bg-success text-white"><div class="card-body text-center py-3"><h6 class="text-white text-uppercase mb-2 fs-11">System Health</h6><h2 class="mb-1 text-white fw-bold">Good</h2><span class="badge bg-white text-success fs-11">All Systems Operational</span></div></div></div>
                    </div>
                    <div class="card mt-3">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-semibold text-uppercase fs-12">System Control Activity</h6>
                            <div>
                                <button class="btn btn-sm btn-primary me-2" onclick="showAddActivityModal()"><i class="bx bx-plus me-1"></i> Log Activity</button>
                                <button class="btn btn-sm btn-outline-secondary" onclick="loadOverviewActivities()"><i class="bx bx-refresh fs-14 me-1"></i> Refresh Logs</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-striped mb-0 align-middle">
                                    <thead class="table-light text-uppercase fs-12">
                                        <tr><th>Activity</th><th>Module</th><th>Performed By</th><th>Time</th><th>Status</th><th>Actions</th></tr>
                                    </thead>
                                    <tbody id="overview-activity-table-body"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== RULE PRIORITY (unchanged) ==================== -->
                <div class="tab-pane fade" id="rule-priority" role="tabpanel">
                    <!-- same as before -->
                    <div class="info-box mb-4 d-flex align-items-start gap-3">
                        <i class="bx bx-info-circle fs-3 text-primary mt-1"></i>
                        <div><strong class="text-dark">Rule Priority</strong> determines which rule will be applied when multiple rules are applicable. Higher priority rules will override lower priority rules and control <strong>commission rates</strong>.</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-semibold text-uppercase mb-0">Priority List</h6>
                        <button class="btn btn-sm btn-primary" onclick="showAddPriorityModal()"><i class="bx bx-plus me-1"></i> Add Priority Level</button>
                    </div>
                    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover table-striped mb-0 align-middle"><thead class="table-light text-uppercase fs-12"><tr><th style="width:100px">Priority Order</th><th>Rule Level</th><th>Description</th><th>Status</th><th>Actions</th></tr></thead><tbody id="rule-priority-table-body"></tbody></table></div></div></div>
                    <div class="example-box mt-4 d-flex align-items-start gap-3"><i class="bx bx-bulb fs-3 text-warning mt-1"></i><div><strong class="text-dark">Example:</strong> If a seller has a <strong>specific rule</strong> and a <strong>category rule</strong>, the system will apply the <strong class="text-success">Seller Specific Rule</strong>.<br><span class="text-muted fs-12 mt-1 d-block">App commission = 20% | Category commission = 15% | Seller special = 10% → ✅ System uses Seller rule (10%)</span></div></div>
                </div>

                <!-- ==================== VALIDATION RULES (unchanged) ==================== -->
                <div class="tab-pane fade" id="validation-rules" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3"><h6 class="fw-semibold text-uppercase mb-0">Validation Configuration</h6><button class="btn btn-sm btn-primary" onclick="showAddValidationModal()"><i class="bx bx-plus me-1"></i> Add New Rule</button></div>
                    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover table-striped mb-0 align-middle"><thead class="table-light text-uppercase fs-12"><tr><th>Rule Name</th><th>Description</th><th>Applies To</th><th>Status</th><th>Action</th></tr></thead><tbody id="validation-rules-table-body"></tbody></table></div></div></div>
                </div>

                <!-- ==================== WORKFLOW RULES (unchanged) ==================== -->
                <div class="tab-pane fade" id="workflow-rules" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-4"><ul class="nav nav-pills nav-pills-custom" id="workflowSubTab" role="tablist"><li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#wf-content" type="button" onclick="loadWorkflow('seller')">Seller Workflow</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#wf-content" type="button" onclick="loadWorkflow('product')">Product Workflow</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#wf-content" type="button" onclick="loadWorkflow('order')">Order Workflow</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#wf-content" type="button" onclick="loadWorkflow('refund')">Refund Workflow</button></li><li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#wf-content" type="button" onclick="loadWorkflow('lead')">Lead Workflow</button></li></ul><button class="btn btn-sm btn-outline-primary" onclick="editWorkflowCurrent()"><i class="bx bx-edit me-1"></i> Edit Workflow Steps</button></div>
                    <div class="tab-content" id="workflowSubTabContent"><div class="tab-pane fade show active" id="wf-content" role="tabpanel"><div id="workflow-visualization" class="workflow-container"></div><div class="row g-3"><div class="col-md-7"><div class="card h-100"><div class="card-header bg-light"><h6 class="mb-0 fw-semibold text-uppercase fs-12">Workflow Description</h6></div><div class="card-body"><p class="text-muted mb-3">This workflow defines the status flow for <strong id="wf-title-display">Seller Management</strong>. Each status transition is controlled by system rules and admin actions.</p><ul class="list-unstyled mb-0" id="workflow-description-list"></ul></div></div></div><div class="col-md-5"><div class="card h-100"><div class="card-header bg-light"><h6 class="mb-0 fw-semibold text-uppercase fs-12">Workflow Settings</h6></div><div class="card-body" id="workflow-settings-list"></div></div></div></div></div></div>
                </div>

                <!-- ==================== AUTOMATION RULES (unchanged) ==================== -->
                <div class="tab-pane fade" id="automation-rules" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3"><h6 class="fw-semibold text-uppercase mb-0">System Automation Triggers</h6><button class="btn btn-sm btn-primary" onclick="showAddAutomationModal()"><i class="bx bx-plus me-1"></i> Add Automation Rule</button></div>
                    <div class="card"><div class="card-body p-0"><div class="table-responsive"><table class="table table-hover table-striped mb-0 align-middle"><thead class="table-light text-uppercase fs-12"><tr><th>Rule Name</th><th>Trigger</th><th>Action</th><th>Status</th><th>Last Run</th><th>Actions</th></tr></thead><tbody id="automation-rules-table-body"></tbody></table></div></div></div>
                </div>

                <!-- ==================== NEW: REVENUE ENGINE TAB ==================== -->
                <div class="tab-pane fade" id="revenue-engine" role="tabpanel">
                    <!-- Metric cards -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card metric-card border-start border-4 border-primary">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fs-12">Total Lead Revenue</h6>
                                    <h2 class="fw-bold mb-0" id="total-lead-revenue">₹0</h2>
                                    <small class="text-muted">from boosts</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card metric-card border-start border-4 border-success">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fs-12">Revenue Per Seller</h6>
                                    <h2 class="fw-bold mb-0" id="revenue-per-seller">₹0</h2>
                                    <small class="text-muted">average per seller</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card metric-card border-start border-4 border-info">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fs-12">Revenue Per Category</h6>
                                    <h2 class="fw-bold mb-0" id="revenue-per-category">₹0</h2>
                                    <small class="text-muted">top category average</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card metric-card border-start border-4 border-warning">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fs-12">Revenue Per App</h6>
                                    <h2 class="fw-bold mb-0" id="revenue-per-app">₹0</h2>
                                    <small class="text-muted">mobile app vs web</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Revenue Trends Chart -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 fw-semibold">Revenue Trends (Last 30 Days)</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="revenueTrendsChart" class="revenue-chart-container"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Categories Table -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                    <h6 class="mb-0 fw-semibold">Revenue by Category</h6>
                                    <button class="btn btn-sm btn-outline-primary" onclick="refreshRevenueData()"><i class="bx bx-refresh"></i> Refresh</button>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr><th>Category</th><th>Total Revenue (₹)</th><th>% Share</th><th>Trend</th></tr>
                                            </thead>
                                            <tbody id="revenue-category-table"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ==================== NEW: BOOST REVENUE ENGINE TAB ==================== -->
                <div class="tab-pane fade" id="boost-revenue" role="tabpanel">
                    <!-- Stats row -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="card metric-card border-start border-4 bg-gradient-primary">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fs-12">Total Boost Revenue</h6>
                                    <h2 class="fw-bold mb-0" id="boost-total-revenue">₹0</h2>
                                    <small>from all boosts</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card metric-card border-start border-4 bg-gradient-success">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fs-12">Total Boost Amount</h6>
                                    <h2 class="fw-bold mb-0" id="boost-total-amount">₹0</h2>
                                    <small>spent on boosts</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card metric-card border-start border-4 bg-gradient-info">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fs-12">ROI (Revenue/Spend)</h6>
                                    <h2 class="fw-bold mb-0" id="boost-roi">0.00x</h2>
                                    <small>return on investment</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card metric-card border-start border-4 bg-gradient-warning">
                                <div class="card-body">
                                    <h6 class="text-muted text-uppercase fs-12">Active Boosts</h6>
                                    <h2 class="fw-bold mb-0" id="boost-active-count">0</h2>
                                    <small>currently running</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Top Spending Sellers -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 fw-semibold">Top Spending Sellers</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr><th>Seller Name</th><th>Total Spent (₹)</th><th>Revenue Generated</th><th>ROI</th></tr>
                                            </thead>
                                            <tbody id="top-spending-sellers"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 fw-semibold">Campaign Revenue by Boost</h6>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr><th>Product</th><th>Boost Amount</th><th>Revenue</th><th>Status</th></tr>
                                            </thead>
                                            <tbody id="campaign-revenue-table"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ROI Tracking Chart -->
                    <div class="row mt-3">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0 fw-semibold">ROI Tracking (Last 10 Boosts)</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="roiTrackingChart" class="revenue-chart-container"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div><!-- end tab-content -->
        </div>
    </div>
</div>

<!-- ==================== MODALS (unchanged) ==================== -->
<!-- Activity Modal -->
<div class="modal fade" id="activityModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="activityModalLabel">Add Activity</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="activityForm" onsubmit="saveActivity(event)"><div class="modal-body"><input type="hidden" id="activity-index"><div class="mb-3"><label for="activity-text" class="form-label">Activity Description</label><input type="text" class="form-control" id="activity-text" required></div><div class="mb-3"><label for="activity-module" class="form-label">Module</label><input type="text" class="form-control" id="activity-module" required></div><div class="mb-3"><label for="activity-performed" class="form-label">Performed By</label><input type="text" class="form-control" id="activity-performed" value="Super Admin" required></div><div class="mb-3"><label for="activity-status" class="form-label">Status</label><select class="form-select" id="activity-status"><option value="Success">Success</option><option value="Failed">Failed</option><option value="Pending">Pending</option></select></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Activity</button></div></form></div></div></div>

<!-- Priority Modal -->
<div class="modal fade" id="priorityModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="priorityModalLabel">Add Priority Level</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="priorityForm" onsubmit="savePriority(event)"><div class="modal-body"><input type="hidden" id="priority-index"><div class="mb-3"><label for="priority-level" class="form-label">Rule Level</label><input type="text" class="form-control" id="priority-level" required></div><div class="mb-3"><label for="priority-desc" class="form-label">Description</label><textarea class="form-control" id="priority-desc" rows="2" required></textarea></div><div class="mb-3"><label for="priority-status" class="form-label">Status</label><select class="form-select" id="priority-status"><option value="Active">Active</option><option value="Inactive">Inactive</option></select></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button><button type="submit" class="btn btn-primary">Save Priority</button></div></form></div></div></div>

<!-- Validation Modal -->
<div class="modal fade" id="validationModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="validationModalLabel">Add Validation Rule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="validationForm" onsubmit="saveValidation(event)"><div class="modal-body"><input type="hidden" id="validation-index"><div class="mb-3"><label for="validation-name" class="form-label">Rule Name</label><input type="text" class="form-control" id="validation-name" required></div><div class="mb-3"><label for="validation-desc" class="form-label">Description</label><textarea class="form-control" id="validation-desc" rows="3" required></textarea></div><div class="mb-3"><label for="validation-applies" class="form-label">Applies To</label><select class="form-select" id="validation-applies" required><option value="Sellers">Sellers</option><option value="Products">Products</option><option value="Orders">Orders</option><option value="Refunds">Refunds</option><option value="Leads">Leads</option></select></div><div class="mb-3"><label for="validation-status" class="form-label">Status</label><select class="form-select" id="validation-status" required><option value="Active">Active</option><option value="Inactive">Inactive</option></select></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save Rule</button></div></form></div></div></div>

<!-- Automation Modal -->
<div class="modal fade" id="automationModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><h5 class="modal-title" id="automationModalLabel">Add Automation Rule</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><form id="automationForm" onsubmit="saveAutomation(event)"><div class="modal-body"><input type="hidden" id="automation-index"><div class="mb-3"><label for="automation-name" class="form-label">Rule Name</label><input type="text" class="form-control" id="automation-name" required></div><div class="mb-3"><label for="automation-trigger" class="form-label">Trigger Event</label><input type="text" class="form-control" id="automation-trigger" required placeholder="e.g. Order Delivered"></div><div class="mb-3"><label for="automation-action" class="form-label">Action to Take</label><input type="text" class="form-control" id="automation-action" required placeholder="e.g. Release payout"></div><div class="mb-3"><label for="automation-status" class="form-label">Status</label><select class="form-select" id="automation-status" required><option value="Active">Active</option><option value="Inactive">Inactive</option></select></div><div class="mb-3"><label for="automation-last-run" class="form-label">Last Run</label><input type="text" class="form-control" id="automation-last-run"></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button><button type="submit" class="btn btn-primary">Save Rule</button></div></form></div></div></div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
    // Master data from controller (includes new revenue & boost data)
    const masterData = @json($settingsData);

    document.addEventListener("DOMContentLoaded", function() {
        const tabElList = [].slice.call(document.querySelectorAll('button[data-bs-toggle="tab"]'));
        tabElList.forEach(function (tabEl) {
            tabEl.addEventListener('shown.bs.tab', function (event) {
                const tabTitle = event.target.innerText.replace(/^\d+\.\s+/, '').trim();
                document.getElementById('breadcrumb-active').innerText = tabTitle;
                // Refresh charts when revenue/boost tabs become active
                if (event.target.id === 'revenue-engine-tab') { loadRevenueData(); }
                if (event.target.id === 'boost-revenue-tab') { loadBoostData(); }
            });
        });
        const urlParams = new URLSearchParams(window.location.search);
        const tabParam = urlParams.get('tab');
        if (tabParam) { const tabButton = document.getElementById(tabParam + '-tab'); if (tabButton) { new bootstrap.Tab(tabButton).show(); } }
        loadOverviewActivities();
        loadRulePriority();
        loadValidationRules();
        loadWorkflow('seller');
        loadWorkflowSettings();
        loadAutomationRules();
        updateOverviewMetrics();
        // Preload revenue and boost data (they will be displayed when tabs are opened)
        loadRevenueData();
        loadBoostData();
    });

    // ==================== EXISTING FUNCTIONS (unchanged) ====================
    function getFormattedDate() { /* ... unchanged ... */ const d = new Date(); const months = ["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"]; const day = String(d.getDate()).padStart(2,'0'); const month = months[d.getMonth()]; const year = d.getFullYear(); let hours = d.getHours(); const minutes = String(d.getMinutes()).padStart(2,'0'); const ampm = hours >= 12 ? 'PM' : 'AM'; hours = hours % 12 || 12; return `${day} ${month} ${year} ${String(hours).padStart(2,'0')}:${minutes} ${ampm}`; }
    function logActivity(activity, moduleName) { if (!masterData.activities) masterData.activities = []; masterData.activities.unshift({ activity, module: moduleName, performed_by: 'Super Admin', time: getFormattedDate(), status: 'Success' }); if (masterData.activities.length > 15) masterData.activities.pop(); saveSettingAJAX('master_activities', masterData.activities, false); loadOverviewActivities(); }

    function loadOverviewActivities() { /* ... unchanged ... */ const tbody = document.getElementById('overview-activity-table-body'); tbody.innerHTML = ''; if (masterData.activities && masterData.activities.length) { masterData.activities.forEach((act, idx) => { tbody.innerHTML += `<tr><td><span class="fw-semibold text-dark">${escapeHtml(act.activity)}</span></td><td><span class="badge bg-light text-secondary border">${escapeHtml(act.module)}</span></td><td>${escapeHtml(act.performed_by)}</td><td><i class="bx bx-calendar me-1 text-muted"></i>${escapeHtml(act.time)}</td><td><span class="badge bg-success-subtle text-success">${escapeHtml(act.status)}</span></td><td><button class="btn btn-sm btn-icon btn-light text-primary me-1" onclick="editActivity(${idx})"><i class="bx bx-edit"></i></button><button class="btn btn-sm btn-icon btn-light text-danger" onclick="deleteActivity(${idx})"><i class="bx bx-trash"></i></button></td></tr>`; }); } else { tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No activity logs found.</td></tr>'; } }
    function showAddActivityModal(index = null) { /* ... unchanged ... */ const modal = new bootstrap.Modal(document.getElementById('activityModal')); document.getElementById('activityForm').reset(); document.getElementById('activity-index').value = ''; document.getElementById('activity-performed').value = 'Super Admin'; if (index !== null) { const act = masterData.activities[index]; document.getElementById('activity-index').value = index; document.getElementById('activity-text').value = act.activity; document.getElementById('activity-module').value = act.module; document.getElementById('activity-performed').value = act.performed_by; document.getElementById('activity-status').value = act.status; document.getElementById('activityModalLabel').innerText = 'Edit Activity'; } else { document.getElementById('activityModalLabel').innerText = 'Add Activity'; } modal.show(); }
    function saveActivity(event) { event.preventDefault(); const idx = document.getElementById('activity-index').value; const newAct = { activity: document.getElementById('activity-text').value, module: document.getElementById('activity-module').value, performed_by: document.getElementById('activity-performed').value, time: getFormattedDate(), status: document.getElementById('activity-status').value }; if (idx === '') masterData.activities.unshift(newAct); else masterData.activities[parseInt(idx)] = newAct; saveSettingAJAX('master_activities', masterData.activities); loadOverviewActivities(); bootstrap.Modal.getInstance(document.getElementById('activityModal')).hide(); logActivity(newAct.activity, newAct.module); }
    function editActivity(index) { showAddActivityModal(index); }
    function deleteActivity(index) { if (confirm('Delete this activity?')) { const removed = masterData.activities.splice(index,1); saveSettingAJAX('master_activities', masterData.activities); loadOverviewActivities(); logActivity(`Deleted activity: ${removed[0].activity}`, 'Overview'); } }

    function loadRulePriority() { const tbody = document.getElementById('rule-priority-table-body'); tbody.innerHTML = ''; const colors = ['#6c5ffc', '#10b981', '#3b82f6', '#f59e0b', '#6b7280']; (masterData.priorities || []).forEach((rule, idx) => { tbody.innerHTML += `<tr><td><span class="priority-badge" style="background:${colors[idx%colors.length]}">${idx+1}</span></td><td><span class="fw-semibold text-dark">${escapeHtml(rule.level)}</span></td><td class="text-muted">${escapeHtml(rule.description)}</td><td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" ${rule.status==='Active'?'checked':''} onchange="togglePriority(${idx})"></div></td><td><button class="btn btn-sm btn-icon btn-light text-primary me-1" onclick="editPriority(${idx})"><i class="bx bx-edit"></i></button><button class="btn btn-sm btn-icon btn-light text-danger me-1" onclick="deletePriority(${idx})"><i class="bx bx-trash"></i></button><button class="btn btn-sm btn-icon btn-light text-secondary me-1" onclick="movePriority(${idx},'up')" ${idx===0?'disabled':''}><i class="bx bx-up-arrow-alt"></i></button><button class="btn btn-sm btn-icon btn-light text-secondary" onclick="movePriority(${idx},'down')" ${idx===masterData.priorities.length-1?'disabled':''}><i class="bx bx-down-arrow-alt"></i></button></td></tr>`; }); }
    function showAddPriorityModal(index = null) { const modal = new bootstrap.Modal(document.getElementById('priorityModal')); document.getElementById('priorityForm').reset(); document.getElementById('priority-index').value = ''; if (index !== null) { const prio = masterData.priorities[index]; document.getElementById('priority-index').value = index; document.getElementById('priority-level').value = prio.level; document.getElementById('priority-desc').value = prio.description; document.getElementById('priority-status').value = prio.status; document.getElementById('priorityModalLabel').innerText = 'Edit Priority Level'; } else { document.getElementById('priorityModalLabel').innerText = 'Add Priority Level'; } modal.show(); }
    function savePriority(event) { event.preventDefault(); const idx = document.getElementById('priority-index').value; const newPrio = { level: document.getElementById('priority-level').value, description: document.getElementById('priority-desc').value, status: document.getElementById('priority-status').value }; if (idx === '') masterData.priorities.push(newPrio); else masterData.priorities[parseInt(idx)] = newPrio; saveSettingAJAX('master_priorities', masterData.priorities); loadRulePriority(); bootstrap.Modal.getInstance(document.getElementById('priorityModal')).hide(); logActivity(`${idx===''?'Added':'Updated'} priority: ${newPrio.level}`, 'Rule Priority'); }
    function editPriority(index) { showAddPriorityModal(index); }
    function deletePriority(index) { if (confirm('Delete this priority level?')) { const removed = masterData.priorities.splice(index,1); saveSettingAJAX('master_priorities', masterData.priorities); loadRulePriority(); logActivity(`Deleted priority: ${removed[0].level}`, 'Rule Priority'); } }
    function togglePriority(index) { masterData.priorities[index].status = masterData.priorities[index].status === 'Active' ? 'Inactive' : 'Active'; saveSettingAJAX('master_priorities', masterData.priorities); loadRulePriority(); logActivity(`Toggled priority: ${masterData.priorities[index].level}`, 'Rule Priority'); }
    function movePriority(index, direction) { if (direction === 'up' && index>0) [masterData.priorities[index-1], masterData.priorities[index]] = [masterData.priorities[index], masterData.priorities[index-1]]; else if (direction === 'down' && index<masterData.priorities.length-1) [masterData.priorities[index], masterData.priorities[index+1]] = [masterData.priorities[index+1], masterData.priorities[index]]; saveSettingAJAX('master_priorities', masterData.priorities); loadRulePriority(); logActivity(`Reordered priority: ${masterData.priorities[index].level}`, 'Rule Priority'); }

    function loadValidationRules() { const tbody = document.getElementById('validation-rules-table-body'); tbody.innerHTML = ''; (masterData.validation_rules || []).forEach((rule, index) => { tbody.innerHTML += `<tr><td><span class="fw-semibold text-dark">${escapeHtml(rule.name)}</span></td><td class="text-muted">${escapeHtml(rule.description)}</td><td><span class="badge bg-light text-dark border">${escapeHtml(rule.applies_to)}</span></td><td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" ${rule.status==='Active'?'checked':''} onchange="toggleRule('validation',${index})"></div></td><td><button class="btn btn-sm btn-icon btn-light text-primary me-1" onclick="editItem('validation',${index})"><i class="bx bx-edit"></i></button><button class="btn btn-sm btn-icon btn-light text-danger" onclick="deleteItem('validation',${index})"><i class="bx bx-trash"></i></button></td></tr>`; }); }
    function showAddValidationModal() { document.getElementById('validationForm').reset(); document.getElementById('validation-index').value = ''; document.getElementById('validationModalLabel').innerText = 'Add Validation Rule'; new bootstrap.Modal(document.getElementById('validationModal')).show(); }
    function saveValidation(event) { event.preventDefault(); const idx = document.getElementById('validation-index').value; const rule = { name: document.getElementById('validation-name').value, description: document.getElementById('validation-desc').value, applies_to: document.getElementById('validation-applies').value, status: document.getElementById('validation-status').value }; if (idx === '') masterData.validation_rules.push(rule); else masterData.validation_rules[parseInt(idx)] = rule; saveSettingAJAX('master_validation_rules', masterData.validation_rules); loadValidationRules(); updateOverviewMetrics(); bootstrap.Modal.getInstance(document.getElementById('validationModal')).hide(); logActivity(`${idx===''?'Added':'Updated'} Validation Rule: ${rule.name}`, 'Validation Rules'); }

    function loadAutomationRules() { const tbody = document.getElementById('automation-rules-table-body'); tbody.innerHTML = ''; (masterData.automation_rules || []).forEach((rule, index) => { tbody.innerHTML += `<tr><td><span class="fw-semibold text-dark">${escapeHtml(rule.name)}</span></td><td>${escapeHtml(rule.trigger)}</td><td><code class="text-primary">${escapeHtml(rule.action)}</code></td><td><div class="form-check form-switch"><input class="form-check-input" type="checkbox" ${rule.status==='Active'?'checked':''} onchange="toggleRule('automation',${index})"></div></td><td><i class="bx bx-time-five me-1 text-muted"></i>${escapeHtml(rule.last_run)}</td><td><button class="btn btn-sm btn-icon btn-light text-primary me-1" onclick="editItem('automation',${index})"><i class="bx bx-edit"></i></button><button class="btn btn-sm btn-icon btn-light text-danger" onclick="deleteItem('automation',${index})"><i class="bx bx-trash"></i></button></td></tr>`; }); }
    function showAddAutomationModal() { document.getElementById('automationForm').reset(); document.getElementById('automation-index').value = ''; document.getElementById('automation-last-run').value = getFormattedDate(); document.getElementById('automationModalLabel').innerText = 'Add Automation Rule'; new bootstrap.Modal(document.getElementById('automationModal')).show(); }
    function saveAutomation(event) { event.preventDefault(); const idx = document.getElementById('automation-index').value; const rule = { name: document.getElementById('automation-name').value, trigger: document.getElementById('automation-trigger').value, action: document.getElementById('automation-action').value, status: document.getElementById('automation-status').value, last_run: document.getElementById('automation-last-run').value || 'Never' }; if (idx === '') masterData.automation_rules.push(rule); else masterData.automation_rules[parseInt(idx)] = rule; saveSettingAJAX('master_automation_rules', masterData.automation_rules); loadAutomationRules(); updateOverviewMetrics(); bootstrap.Modal.getInstance(document.getElementById('automationModal')).hide(); logActivity(`${idx===''?'Added':'Updated'} Automation: ${rule.name}`, 'Automation'); }

    // Workflow functions (unchanged, keep as is)
    let currentWorkflow = 'seller';
    function loadWorkflow(type) { currentWorkflow = type; const viz = document.getElementById('workflow-visualization'); viz.innerHTML = ''; if (!masterData.workflows) masterData.workflows = {}; const steps = masterData.workflows[type] || []; if (steps.length===0) { viz.innerHTML = '<div class="text-muted text-center py-4 w-100">No steps defined. Click Edit Workflow Steps to add.</div>'; return; } steps.forEach((step, idx) => { const activeClass = step.color || 'active'; viz.innerHTML += `<div class="workflow-step ${activeClass}"><div class="workflow-circle"><i class="bx ${step.icon||'bx-right-arrow-alt'} fs-4"></i></div><h6 class="mt-2 mb-1 fw-bold text-dark fs-13">${escapeHtml(step.status)}</h6><p class="text-muted fs-11 px-1 mb-0">${escapeHtml(step.description)}</p></div>`; if (idx<steps.length-1) viz.innerHTML += `<div class="workflow-arrow"><i class="bx bx-right-arrow-alt"></i></div>`; }); const titles = { seller:'Seller Management', product:'Product Management', order:'Order Processing', refund:'Refund Processing', lead:'Lead Conversion' }; document.getElementById('wf-title-display').innerText = titles[type]||type; const descList = document.getElementById('workflow-description-list'); descList.innerHTML = ''; (masterData.workflow_settings || []).forEach(setting => { const icon = setting.enabled ? 'bx-check-circle text-success' : 'bx-x-circle text-danger'; descList.innerHTML += `<li class="mb-2"><i class="bx ${icon} me-2"></i> ${escapeHtml(setting.name)}</li>`; }); }
    function loadWorkflowSettings() { const container = document.getElementById('workflow-settings-list'); container.innerHTML = ''; (masterData.workflow_settings || []).forEach((setting, index) => { container.innerHTML += `<div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" id="wf-chk-${index}" ${setting.enabled?'checked':''} onchange="toggleWorkflowSetting(${index})"><label class="form-check-label" for="wf-chk-${index}">${escapeHtml(setting.name)}</label></div>`; }); }
    function toggleWorkflowSetting(index) { masterData.workflow_settings[index].enabled = !masterData.workflow_settings[index].enabled; saveSettingAJAX('master_workflow_settings', masterData.workflow_settings); loadWorkflow(currentWorkflow); logActivity(`Toggled workflow setting: ${masterData.workflow_settings[index].name}`, 'Workflow Engine'); }
    function editWorkflowCurrent() { const modal = new bootstrap.Modal(document.getElementById('workflowEditModal')); loadWorkflowStepsManager(); modal.show(); }
    function loadWorkflowStepsManager() { const tbody = document.getElementById('workflow-steps-list-body'); tbody.innerHTML = ''; if (!masterData.workflows) masterData.workflows = {}; const steps = masterData.workflows[currentWorkflow] || []; if (steps.length===0) { tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No steps defined. Add a step below.</td></tr>'; return; } steps.forEach((step, idx) => { tbody.innerHTML += `<tr><td><span class="badge bg-secondary">${idx+1}</span></td><td><span class="fw-semibold text-dark">${escapeHtml(step.status)}</span></td><td><span class="text-muted fs-12">${escapeHtml(step.description)}</span></td><td><code>${escapeHtml(step.icon)}</code></td><td><span class="badge bg-${step.color==='active'?'primary':(step.color==='completed'?'success':(step.color==='warning'?'warning':'danger'))}">${step.color}</span></td><td><button class="btn btn-sm btn-icon btn-light text-primary me-1" onclick="showEditWorkflowStepModal(${idx})"><i class="bx bx-edit"></i></button><button class="btn btn-sm btn-icon btn-light text-danger me-1" onclick="deleteWorkflowStep(${idx})"><i class="bx bx-trash"></i></button><button class="btn btn-sm btn-icon btn-light text-secondary me-1" onclick="moveWorkflowStep(${idx},'up')" ${idx===0?'disabled':''}><i class="bx bx-up-arrow-alt"></i></button><button class="btn btn-sm btn-icon btn-light text-secondary" onclick="moveWorkflowStep(${idx},'down')" ${idx===steps.length-1?'disabled':''}><i class="bx bx-down-arrow-alt"></i></button></td></tr>`; }); }
    function showAddWorkflowStepModal() { document.getElementById('workflowStepForm').reset(); document.getElementById('wf-step-index').value = ''; document.getElementById('workflowStepModalLabel').innerText = 'Add Workflow Step'; bootstrap.Modal.getInstance(document.getElementById('workflowEditModal')).hide(); new bootstrap.Modal(document.getElementById('workflowStepModal')).show(); }
    function showEditWorkflowStepModal(index) { const step = masterData.workflows[currentWorkflow][index]; document.getElementById('wf-step-index').value = index; document.getElementById('wf-step-status').value = step.status; document.getElementById('wf-step-desc').value = step.description; document.getElementById('wf-step-icon').value = step.icon; document.getElementById('wf-step-color').value = step.color||'active'; document.getElementById('workflowStepModalLabel').innerText = 'Edit Workflow Step'; bootstrap.Modal.getInstance(document.getElementById('workflowEditModal')).hide(); new bootstrap.Modal(document.getElementById('workflowStepModal')).show(); }
    function backToWorkflowEditModal() { bootstrap.Modal.getInstance(document.getElementById('workflowStepModal')).hide(); const modal = new bootstrap.Modal(document.getElementById('workflowEditModal')); loadWorkflowStepsManager(); modal.show(); }
    function saveWorkflowStep(event) { event.preventDefault(); const idx = document.getElementById('wf-step-index').value; const step = { status: document.getElementById('wf-step-status').value, description: document.getElementById('wf-step-desc').value, icon: document.getElementById('wf-step-icon').value, color: document.getElementById('wf-step-color').value }; if (!masterData.workflows[currentWorkflow]) masterData.workflows[currentWorkflow] = []; if (idx === '') masterData.workflows[currentWorkflow].push(step); else masterData.workflows[currentWorkflow][parseInt(idx)] = step; saveSettingAJAX('master_workflows', masterData.workflows); loadWorkflow(currentWorkflow); bootstrap.Modal.getInstance(document.getElementById('workflowStepModal')).hide(); const modal = new bootstrap.Modal(document.getElementById('workflowEditModal')); loadWorkflowStepsManager(); modal.show(); logActivity(`${idx===''?'Added':'Updated'} workflow step ${step.status} in ${currentWorkflow}`, 'Workflow'); }
    function deleteWorkflowStep(index) { if (confirm('Delete this workflow stage?')) { const stepName = masterData.workflows[currentWorkflow][index].status; masterData.workflows[currentWorkflow].splice(index,1); saveSettingAJAX('master_workflows', masterData.workflows); loadWorkflow(currentWorkflow); loadWorkflowStepsManager(); logActivity(`Deleted workflow step ${stepName} from ${currentWorkflow}`, 'Workflow'); } }
    function moveWorkflowStep(index, direction) { const steps = masterData.workflows[currentWorkflow]; if (direction==='up' && index>0) [steps[index-1], steps[index]] = [steps[index], steps[index-1]]; else if (direction==='down' && index<steps.length-1) [steps[index], steps[index+1]] = [steps[index+1], steps[index]]; saveSettingAJAX('master_workflows', masterData.workflows); loadWorkflow(currentWorkflow); loadWorkflowStepsManager(); logActivity(`Reordered workflow steps in ${currentWorkflow}`, 'Workflow'); }

    // Helper functions
    function updateOverviewMetrics() { const activeRulesCount = (masterData.validation_rules||[]).filter(r=>r.status==='Active').length + (masterData.automation_rules||[]).filter(r=>r.status==='Active').length; document.getElementById('metric-rules-count').innerText = activeRulesCount; document.getElementById('metric-rules-badge').innerText = `${(masterData.validation_rules||[]).filter(r=>r.status==='Active').length} Active Rules`; document.getElementById('metric-roles-count').innerText = (masterData.roles||[]).length; document.getElementById('metric-compliance-count').innerText = (masterData.compliance||[]).length; document.getElementById('metric-notifications-count').innerText = (masterData.notifications||[]).length; }
    function toggleRule(type, index) { let settingName, list; if (type==='validation') { settingName='master_validation_rules'; list=masterData.validation_rules; list[index].status = list[index].status==='Active'?'Inactive':'Active'; loadValidationRules(); logActivity(`Toggled Validation Rule: ${list[index].name}`, 'Validation'); } else if (type==='automation') { settingName='master_automation_rules'; list=masterData.automation_rules; list[index].status = list[index].status==='Active'?'Inactive':'Active'; loadAutomationRules(); logActivity(`Toggled Automation Rule: ${list[index].name}`, 'Automation'); } else if (type==='notification') { settingName='master_notifications'; list=masterData.notifications; list[index].status = list[index].status==='Active'?'Inactive':'Active'; loadNotificationTemplates(); logActivity(`Toggled Notification: ${list[index].name}`, 'Notification'); } saveSettingAJAX(settingName, list); updateOverviewMetrics(); }
    function editItem(type, index) { if (type==='validation') { const item = masterData.validation_rules[index]; document.getElementById('validation-index').value = index; document.getElementById('validation-name').value = item.name; document.getElementById('validation-desc').value = item.description; document.getElementById('validation-applies').value = item.applies_to; document.getElementById('validation-status').value = item.status; document.getElementById('validationModalLabel').innerText = 'Edit Validation Rule'; new bootstrap.Modal(document.getElementById('validationModal')).show(); } else if (type==='automation') { const item = masterData.automation_rules[index]; document.getElementById('automation-index').value = index; document.getElementById('automation-name').value = item.name; document.getElementById('automation-trigger').value = item.trigger; document.getElementById('automation-action').value = item.action; document.getElementById('automation-status').value = item.status; document.getElementById('automation-last-run').value = item.last_run||''; document.getElementById('automationModalLabel').innerText = 'Edit Automation Rule'; new bootstrap.Modal(document.getElementById('automationModal')).show(); } else if (type==='role') { const item = masterData.roles[index]; document.getElementById('role-index').value = index; document.getElementById('role-name').value = item.name; document.getElementById('role-desc').value = item.description; document.getElementById('role-users').value = item.users; document.getElementById('role-status').value = item.status; document.getElementById('role-last-login').value = item.last_login||''; document.getElementById('roleModalLabel').innerText = 'Edit Admin Role'; new bootstrap.Modal(document.getElementById('roleModal')).show(); } else if (type==='staff') { const item = masterData.staff[index]; document.getElementById('staff-index').value = index; document.getElementById('staff-id').value = item.id; document.getElementById('staff-name').value = item.name; document.getElementById('staff-dept').value = item.department; document.getElementById('staff-designation').value = item.designation; document.getElementById('staff-email').value = item.email; document.getElementById('staff-phone').value = item.phone||''; document.getElementById('staff-status').value = item.status; document.getElementById('staffModalLabel').innerText = 'Edit Staff Member'; new bootstrap.Modal(document.getElementById('staffModal')).show(); } else if (type==='compliance') { const item = masterData.compliance[index]; document.getElementById('compliance-index').value = index; document.getElementById('compliance-name').value = item.name; document.getElementById('compliance-desc').value = item.description; document.getElementById('compliance-category').value = item.category; document.getElementById('compliance-version').value = item.version; document.getElementById('compliance-updated').value = item.last_updated||''; document.getElementById('compliance-status').value = item.status; document.getElementById('complianceModalLabel').innerText = 'Edit Compliance Policy'; new bootstrap.Modal(document.getElementById('complianceModal')).show(); } else if (type==='notification') { const item = masterData.notifications[index]; document.getElementById('notification-index').value = index; document.getElementById('notification-name').value = item.name; document.getElementById('notification-type').value = item.type; document.getElementById('notification-subject').value = item.subject; document.getElementById('notification-status').value = item.status; document.getElementById('notificationModalLabel').innerText = 'Edit Notification Template'; new bootstrap.Modal(document.getElementById('notificationModal')).show(); } }
    function deleteItem(type, index) { let settingName, list, itemName; if (type==='validation') { settingName='master_validation_rules'; list=masterData.validation_rules; itemName=list[index].name; } else if (type==='automation') { settingName='master_automation_rules'; list=masterData.automation_rules; itemName=list[index].name; } else if (type==='role') { settingName='master_roles'; list=masterData.roles; itemName=list[index].name; } else if (type==='staff') { settingName='master_staff'; list=masterData.staff; itemName=list[index].name; } else if (type==='compliance') { settingName='master_compliance'; list=masterData.compliance; itemName=list[index].name; } else if (type==='notification') { settingName='master_notifications'; list=masterData.notifications; itemName=list[index].name; } if (confirm(`Delete "${itemName}"?`)) { list.splice(index,1); saveSettingAJAX(settingName,list); if (type==='validation') loadValidationRules(); else if (type==='automation') loadAutomationRules(); else if (type==='role') loadRoleManagement(); else if (type==='staff') loadStaffDirectory(); else if (type==='compliance') loadCompliancePolicies(); else if (type==='notification') loadNotificationTemplates(); updateOverviewMetrics(); logActivity(`Deleted ${type}: ${itemName}`, type); } }
    function saveSettingAJAX(key, value, showToastFlag = true) { fetch('{{ route("admin.settings.save_master_config") }}', { method:'POST', headers:{ 'Content-Type':'application/json', 'X-CSRF-TOKEN':'{{ csrf_token() }}' }, body: JSON.stringify({ key: key, value: JSON.stringify(value) }) }).then(res=>res.json()).then(data=>{ if(data.success) { if(showToastFlag) showToast('Saved!','success'); } else { if(showToastFlag) showToast('Error: '+data.message,'danger'); } }).catch(err=>{ console.error(err); if(showToastFlag) showToast('Network error','danger'); }); }
    function showToast(message, type='success') { const toastId = 'toast-'+Date.now(); const html = `<div id="${toastId}" class="toast align-items-center text-bg-${type} border-0 show position-fixed" style="top:20px;right:20px;z-index:9999;"><div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button></div></div>`; document.body.insertAdjacentHTML('beforeend',html); setTimeout(()=>{ const el = document.getElementById(toastId); if(el) el.remove(); },3000); }
    function escapeHtml(str) { if(!str) return ''; return str.replace(/[&<>]/g, function(m) { if(m==='&') return '&amp;'; if(m==='<') return '&lt;'; if(m==='>') return '&gt;'; return m; }); }

    // ==================== NEW REVENUE ENGINE FUNCTIONS ====================
    let revenueChart = null;
    function loadRevenueData() {
        // Data is already present in masterData.revenue (passed from controller)
        if (!masterData.revenue) return;
        const rev = masterData.revenue;
        document.getElementById('total-lead-revenue').innerHTML = '₹' + rev.total_revenue.toLocaleString();
        document.getElementById('revenue-per-seller').innerHTML = '₹' + rev.revenue_per_seller.toLocaleString();
        document.getElementById('revenue-per-category').innerHTML = '₹' + rev.revenue_per_category.toLocaleString();
        document.getElementById('revenue-per-app').innerHTML = '₹' + rev.revenue_per_app.toLocaleString();

        // Render category table
        const tbody = document.getElementById('revenue-category-table');
        tbody.innerHTML = '';
        rev.categories.forEach(cat => {
            tbody.innerHTML += `<tr><td>${escapeHtml(cat.name)}</td><td>₹${cat.revenue.toLocaleString()}</td><td>${cat.percent}%</td><td><span class="badge bg-${cat.trend==='up'?'success':'danger'}">${cat.trend==='up'?'↑ Growing':'↓ Declining'}</span></td></tr>`;
        });

        // Render revenue trends chart
        const ctx = document.getElementById('revenueTrendsChart').getContext('2d');
        if (revenueChart) revenueChart.destroy();
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: { labels: rev.trend_labels, datasets: [{ label: 'Revenue (₹)', data: rev.trend_values, borderColor: '#4f46e5', backgroundColor: 'rgba(79,70,229,0.1)', fill: true, tension: 0.3 }] },
            options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { position: 'top' } } }
        });
    }

    // ==================== NEW BOOST REVENUE ENGINE FUNCTIONS ====================
    let roiChart = null;
    function loadBoostData() {
        if (!masterData.boost) return;
        const boost = masterData.boost;
        document.getElementById('boost-total-revenue').innerHTML = '₹' + boost.total_revenue.toLocaleString();
        document.getElementById('boost-total-amount').innerHTML = '₹' + boost.total_amount.toLocaleString();
        document.getElementById('boost-roi').innerHTML = boost.roi.toFixed(2) + 'x';
        document.getElementById('boost-active-count').innerHTML = boost.active_boosts;

        // Top spending sellers table
        const sellerTable = document.getElementById('top-spending-sellers');
        sellerTable.innerHTML = '';
        boost.top_sellers.forEach(s => {
            const roiVal = s.revenue / s.spent;
            sellerTable.innerHTML += `<tr><td>${escapeHtml(s.name)}</td><td>₹${s.spent.toLocaleString()}</td><td>₹${s.revenue.toLocaleString()}</td><td>${roiVal.toFixed(2)}x</td></tr>`;
        });

        // Campaign revenue table (boosted products)
        const campaignTable = document.getElementById('campaign-revenue-table');
        campaignTable.innerHTML = '';
        boost.campaigns.forEach(c => {
            campaignTable.innerHTML += `<tr><td>${escapeHtml(c.product_name)}</td><td>₹${c.boost_amount.toLocaleString()}</td><td>₹${c.revenue.toLocaleString()}</td><td><span class="badge bg-${c.status==='active'?'success':'secondary'}">${c.status}</span></td></tr>`;
        });

        // ROI chart
        const roiCtx = document.getElementById('roiTrackingChart').getContext('2d');
        if (roiChart) roiChart.destroy();
        roiChart = new Chart(roiCtx, {
            type: 'bar',
            data: { labels: boost.roi_labels, datasets: [{ label: 'ROI (x)', data: boost.roi_values, backgroundColor: '#10b981' }] },
            options: { responsive: true, maintainAspectRatio: true, scales: { y: { beginAtZero: true, title: { display: true, text: 'Return on Investment (x)' } } } }
        });
    }

    function refreshRevenueData() { location.reload(); } // simple reload; could also fetch AJAX
</script>
@endsection