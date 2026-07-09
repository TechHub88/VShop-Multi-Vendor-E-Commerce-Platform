@extends('layouts.vertical', ['title' => 'Recommendation Engine'])

@section('content')
<style>
    :root {
        --primary-purple: #5b4efb;
        --text-dark: #1f2937;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
        --bg-light: #f9fafb;
    }
    
    body {
        background-color: #f3f4f6;
    }

    .page-header {
        margin-bottom: 24px;
    }

    .page-title {
        color: var(--text-dark);
        font-weight: 700;
        font-size: 1.5rem;
        margin-bottom: 4px;
    }

    .page-subtitle {
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    /* Tabs */
    .custom-tabs {
        display: flex;
        gap: 24px;
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 30px;
    }

    .custom-tab {
        padding: 12px 0;
        color: var(--text-muted);
        font-weight: 600;
        font-size: 0.95rem;
        cursor: pointer;
        position: relative;
    }

    .custom-tab.active {
        color: var(--primary-purple);
    }

    .custom-tab.active::after {
        content: '';
        position: absolute;
        bottom: -1px;
        left: 0;
        width: 100%;
        height: 3px;
        background-color: var(--primary-purple);
        border-top-left-radius: 3px;
        border-top-right-radius: 3px;
    }

    /* Section Cards */
    .section-card {
        background: #ffffff;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #f3f4f6;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--text-dark);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .section-title span {
        font-weight: 400;
        color: var(--text-muted);
        font-size: 0.9rem;
    }

    .btn-add {
        background-color: var(--primary-purple);
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.2s;
    }

    .btn-add:hover {
        background-color: #4b3eeb;
        color: white;
    }

    /* Product Rules Section */
    .rule-row {
        display: flex;
        align-items: center;
        padding: 20px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .rule-row:last-child {
        border-bottom: none;
        padding-bottom: 0;
    }

    .rule-col-header {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.9rem;
        margin-bottom: 16px;
    }

    .product-box {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        width: 80px;
    }

    .product-img {
        width: 70px;
        height: 70px;
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 8px;
        padding: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    
    .product-img img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
    }

    .product-name {
        font-size: 0.8rem;
        font-weight: 600;
        color: var(--text-dark);
        text-align: center;
    }

    .arrow-icon {
        font-size: 1.5rem;
        color: #9ca3af;
        margin: 0 30px;
    }

    .recommendations-wrapper {
        display: flex;
        gap: 16px;
        background: #fdfdfd;
        padding: 16px;
        border: 1px dashed var(--border-color);
        border-radius: 8px;
    }

    .action-icons {
        margin-left: auto;
        display: flex;
        gap: 12px;
    }

    .action-icons i {
        font-size: 1.1rem;
        cursor: pointer;
        color: #9ca3af;
    }
    
    .action-icons .ri-edit-2-line:hover { color: var(--primary-purple); }
    .action-icons .ri-delete-bin-line:hover { color: #ef4444; }

    /* Location Rules Section */
    .location-box {
        display: flex;
        align-items: center;
        gap: 8px;
        min-width: 120px;
        font-weight: 600;
        color: var(--text-dark);
    }

    .location-icon {
        color: var(--primary-purple);
        font-size: 1.2rem;
    }

    .location-img {
        width: 100px;
        height: 60px;
        border-radius: 6px;
        overflow: hidden;
    }
    
    .location-img img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    /* Tables */
    .custom-table {
        width: 100%;
        margin-bottom: 0;
    }
    
    .custom-table th {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 0.85rem;
        border-bottom: 2px solid #f3f4f6;
        padding: 12px 16px;
        background-color: transparent;
    }
    
    .custom-table td {
        padding: 16px;
        font-size: 0.9rem;
        color: var(--text-dark);
        vertical-align: middle;
        border-bottom: 1px solid #f3f4f6;
    }

    .custom-table tr:last-child td {
        border-bottom: none;
    }

    .status-active {
        color: #10b981;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .status-paused {
        color: #ef4444;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .btn-action-sm {
        background: #f3f4f6;
        border: none;
        border-radius: 4px;
        padding: 4px 10px;
        color: var(--text-muted);
        font-size: 1rem;
        cursor: pointer;
    }

    /* Overview Section */
    .overview-title {
        font-weight: 700;
        color: var(--primary-purple);
        font-size: 1.1rem;
        margin-bottom: 20px;
    }

    .stat-card {
        background: #ffffff;
        border: 1px solid #f3f4f6;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 1px 2px rgba(0,0,0,0.02);
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--text-muted);
        font-weight: 600;
        margin-bottom: 8px;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    .stat-change {
        font-size: 0.85rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .text-success-custom { color: #10b981; }

    .footer-note {
        font-size: 0.8rem;
        color: var(--text-muted);
        margin-top: 10px;
    }

</style>

<div class="container-fluid py-4">
    <div class="page-header">
        <h1 class="page-title">Recommendation Engine</h1>
        <p class="page-subtitle">Configure platform-wide recommendation rules and logic.</p>
    </div>

    <div class="custom-tabs">
        <div class="custom-tab active" data-target="#tab-product">Product Rules</div>
        <div class="custom-tab" data-target="#tab-location">Location Rules</div>
        <div class="custom-tab" data-target="#tab-sponsored">Sponsored Recommendations</div>
        <div class="custom-tab" data-target="#tab-ai">AI Recommendation Rules</div>
    </div>

    <!-- 1. Product Rules -->
    <div class="section-card tab-section" id="tab-product">
        <div class="section-header">
            <div class="section-title">
                1. Product Rules <span>(What to recommend with which product)</span>
            </div>
            
        </div>
        
        <div class="d-flex align-items-end mb-3">
            <div class="rule-col-header" style="width: 150px;">If Buyer Views / Buys</div>
            <div class="rule-col-header" style="margin-left: 50px;">Then Recommend</div>
        </div>

        @forelse($productsWithRecs as $item)
        <div class="rule-row">
            <div class="product-box">
                <div class="product-img">
                    @if($item['product']->thumbnail)
                        <img src="{{ asset($item['product']->thumbnail) }}" alt="{{ $item['product']->name }}">
                    @else
                        <i class="ri-smartphone-line" style="font-size: 40px; color: #4b5563;"></i>
                    @endif
                </div>
                <div class="product-name" title="{{ $item['product']->name }}">
                    {{ Str::limit($item['product']->name, 15) }}
                </div>
            </div>

            <i class="ri-arrow-right-line arrow-icon"></i>

            <div class="recommendations-wrapper">
                @forelse($item['recommended_products'] as $rec)
                <div class="product-box">
                    <div class="product-img">
                        @if($rec->thumbnail)
                            <img src="{{ asset($rec->thumbnail) }}" alt="{{ $rec->name }}">
                        @else
                            <i class="ri-plug-line" style="font-size: 32px; color: #9ca3af;"></i>
                        @endif
                    </div>
                    <div class="product-name" title="{{ $rec->name }}">
                        {{ Str::limit($rec->name, 15) }}
                    </div>
                </div>
                @empty
                <div class="text-muted small">No active targets found</div>
                @endforelse
            </div>

            <div class="action-buttons ms-auto d-flex gap-2">
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="editRule({{ $item['product']->id }})">Edit Rule</button>
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteRule({{ $item['product']->id }})">Delete Rule</button>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">No product recommendation rules configured yet.</div>
        @endforelse
    </div>

    <!-- 2. Location Based Rules -->
    <div class="section-card tab-section d-none" id="tab-location">
        <div class="section-header">
            <div class="section-title">
                2. Location Based Rules <span>(Show recommendations by location)</span>
            </div>
            
        </div>

        @forelse($locationRules as $locRule)
        <div class="rule-row">
            <div class="location-box">
                <i class="ri-map-pin-line location-icon"></i>
                {{ ucfirst($locRule['location']) }}
            </div>

            <i class="ri-arrow-right-line arrow-icon"></i>

            <div class="recommendations-wrapper" style="border:none; padding:0; background:transparent;">
                @forelse($locRule['recommended_products'] as $rec)
                <div class="product-box" style="width: auto; max-width: 120px;">
                    <div class="location-img">
                        @if($rec->thumbnail)
                            <img src="{{ asset($rec->thumbnail) }}" alt="{{ $rec->name }}">
                        @else
                            <img src="https://images.unsplash.com/photo-1571536802807-30451e3955d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Placeholder">
                        @endif
                    </div>
                    <div class="product-name mt-2 text-wrap">{{ Str::limit($rec->name, 25) }}</div>
                </div>
                @empty
                <div class="text-muted small">No active targets found</div>
                @endforelse
            </div>
            
            <div class="action-icons">
                <i class="ri-edit-2-line" onclick="editRule({{ $locRule['rule_id'] }})"></i>
                <i class="ri-delete-bin-line" style="color:#ef4444;" onclick="deleteRule({{ $locRule['rule_id'] }})"></i>
            </div>
        </div>
        @empty
        <div class="text-center text-muted py-4">No location recommendation rules configured yet.</div>
        @endforelse
    </div>

    <!-- 3. Sponsored Recommendations -->
    <div class="section-card tab-section d-none" id="tab-sponsored">
        <div class="section-header">
            <div class="section-title">
                3. Sponsored Recommendations <span>(Paid priority placement)</span>
            </div>
            <a href="{{ url('/admin/referral-system') }}?tab=campaigns" class="btn-add" style="text-decoration:none;"><i class="ri-add-line"></i> Add Campaign</a>
        </div>

        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>Campaign Name</th>
                        <th>Type</th>
                        <th>Product/Service</th>
                        <th>Seller</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sponsoredRules as $rule)
                    <tr>
                        <td class="fw-medium">{{ $rule->source }}</td>
                        <td>{{ ucfirst($rule->source_type ?? 'Campaign') }}</td>
                        <td>{{ implode(', ', is_array($rule->targets) ? $rule->targets : json_decode($rule->targets, true) ?? []) }}</td>
                        <td>-</td>
                        <td class="{{ $rule->is_active ? 'status-active' : 'status-paused' }}">{{ $rule->is_active ? 'Active' : 'Paused' }}</td>
                        <td>
                            <div class="d-flex gap-2">
                                <i class="ri-edit-2-line" style="cursor:pointer; color:#9ca3af;" onclick="editRule({{ $rule->id }})"></i>
                                <i class="ri-delete-bin-line" style="cursor:pointer; color:#ef4444;" onclick="deleteRule({{ $rule->id }})"></i>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No sponsored campaigns configured.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. AI Recommendation Rules -->
    <div class="section-card tab-section d-none" id="tab-ai">
        <div class="section-header">
            <div class="section-title">
                4. AI Recommendation Rules <span>(Behavior based recommendations)</span>
            </div>
            
        </div>

        <div class="table-responsive">
            <table class="table custom-table">
                <thead>
                    <tr>
                        <th>Rule Name</th>
                        <th>Logic</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($aiRules as $rule)
                    <tr>
                        <td class="fw-medium">{{ $rule->source }}</td>
                        <td>{{ implode(', ', is_array($rule->targets) ? $rule->targets : json_decode($rule->targets, true) ?? []) }}</td>
                        <td class="{{ $rule->is_active ? 'status-active' : 'status-paused' }}">{{ $rule->is_active ? 'Active' : 'Paused' }}</td>
                        <td class="fw-medium">{{ $rule->priority == 1 ? 'High' : ($rule->priority == 0 ? 'Medium' : 'Low') }}</td>
                        <td><button class="btn-action-sm" onclick="editRule({{ $rule->id }})"><i class="ri-more-fill"></i></button></td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted">No AI recommendation rules configured.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Overview Section -->
    <div class="section-card" style="background-color: #fcfcfc;">
        <h4 class="overview-title">Recommendation Engine Overview</h4>
        
        <div class="row g-3">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Total Active Rules</div>
                    <div class="stat-value">24</div>
                    <div class="stat-change text-success-custom">
                        <i class="ri-arrow-up-fill"></i> 12%
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Sponsored Campaigns</div>
                    <div class="stat-value">18</div>
                    <div class="stat-change text-success-custom">
                        <i class="ri-arrow-up-fill"></i> 8%
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">CTR Increase</div>
                    <div class="stat-value">28.5%</div>
                    <div class="stat-change text-success-custom">
                        <i class="ri-arrow-up-fill"></i> 15%
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-label">Revenue Impact</div>
                    <div class="stat-value">₹12,45,000</div>
                    <div class="stat-change text-success-custom">
                        <i class="ri-arrow-up-fill"></i> 22%
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-note">
        Note: Changes may take up to 30 minutes to reflect on the buyer panel.
    </div>
</div>



<!-- Recommendations Modal -->
<div class="modal fade" id="recommendationsModal" tabindex="-1" aria-labelledby="recommendationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
            <div class="modal-header text-white" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%); border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <h5 class="modal-title fw-bold d-flex align-items-center text-white" id="recommendationsModalLabel">
                    <i class="fa fa-star me-2 text-warning"></i> Configure Product Recommendations
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body bg-light-subtle p-4" style="background-color: #f8fafc;">
                
                <!-- Source Product Info -->
                <div class="card p-3 mb-4 border-0 shadow-sm bg-white" style="border-radius: 12px; border: 1px solid #e2e8f0 !important;">
                    <div class="d-flex align-items-center gap-3">
                        <img id="source_product_img" src="" class="rounded" style="width: 80px; height: 80px; object-fit: cover; border: 1px solid #cbd5e1;">
                        <div>
                            <h5 id="source_product_name" class="fw-bold mb-1 text-slate-800" style="color: #0f172a;"></h5>
                            <div class="text-muted small mb-1">
                                <span>SKU: <strong id="source_product_sku"></strong></span> | 
                                <span class="badge bg-success border border-success ms-1" style="font-size: 10px;">Active</span>
                            </div>
                            <div id="source_product_price" class="text-primary fw-semibold fs-5" style="color: #004392 !important;"></div>
                        </div>
                    </div>
                </div>

                <p class="text-muted small mb-4">
                    Add related products/services that will be shown to buyers on product pages, cart, checkout, etc.
                </p>

                <!-- Form sections for 4 types of recommendations -->
                <form id="recommendationsForm">
                    @csrf
                    <input type="hidden" id="rec_product_id" name="product_id">

                    <!-- Section 1: Related Products -->
                    <div class="recommendation-section mb-4 p-3 bg-white rounded-4 shadow-sm border border-slate-100" style="border-radius: 14px;">
                        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">1</span> Related Products <span class="text-muted fw-normal ms-1" style="font-size: 12px;">(Complementary items)</span>
                        </h6>
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control autocomplete-input" data-category="related" placeholder="Search products..." style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <button class="btn btn-primary add-selected-btn" data-category="related" type="button" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">Add Selected</button>
                            </div>
                            <div class="autocomplete-suggestions shadow-sm rounded-3 border bg-white d-none" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <!-- Selected items list -->
                        <div class="selected-items-container d-flex flex-wrap gap-2 mb-3" id="selected_related"></div>

                        <!-- Popular Suggestions -->
                        <div class="suggestions-section">
                            <span class="text-muted small fw-semibold d-block mb-2">Popular Suggestions</span>
                            <div class="suggestions-container row g-2" id="suggestions_related"></div>
                        </div>
                    </div>

                    <!-- Section 2: Upsell Products -->
                    <div class="recommendation-section mb-4 p-3 bg-white rounded-4 shadow-sm border border-slate-100" style="border-radius: 14px;">
                        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">2</span> Upsell Products <span class="text-muted fw-normal ms-1" style="font-size: 12px;">(High value upgrade options)</span>
                        </h6>
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control autocomplete-input" data-category="upsell" placeholder="Search products..." style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <button class="btn btn-primary add-selected-btn" data-category="upsell" type="button" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">Add Selected</button>
                            </div>
                            <div class="autocomplete-suggestions shadow-sm rounded-3 border bg-white d-none" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <div class="selected-items-container d-flex flex-wrap gap-2 mb-3" id="selected_upsell"></div>

                        <div class="suggestions-section">
                            <span class="text-muted small fw-semibold d-block mb-2">Popular Suggestions</span>
                            <div class="suggestions-container row g-2" id="suggestions_upsell"></div>
                        </div>
                    </div>

                    <!-- Section 3: Cross-Sell Products -->
                    <div class="recommendation-section mb-4 p-3 bg-white rounded-4 shadow-sm border border-slate-100" style="border-radius: 14px;">
                        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">3</span> Cross-Sell Products <span class="text-muted fw-normal ms-1" style="font-size: 12px;">(Other useful items)</span>
                        </h6>
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control autocomplete-input" data-category="cross_sell" placeholder="Search products..." style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <button class="btn btn-primary add-selected-btn" data-category="cross_sell" type="button" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">Add Selected</button>
                            </div>
                            <div class="autocomplete-suggestions shadow-sm rounded-3 border bg-white d-none" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <div class="selected-items-container d-flex flex-wrap gap-2 mb-3" id="selected_cross_sell"></div>

                        <div class="suggestions-section">
                            <span class="text-muted small fw-semibold d-block mb-2">Popular Suggestions</span>
                            <div class="suggestions-container row g-2" id="suggestions_cross_sell"></div>
                        </div>
                    </div>

                    <!-- Section 4: Frequently Bought Together -->
                    <div class="recommendation-section mb-4 p-3 bg-white rounded-4 shadow-sm border border-slate-100" style="border-radius: 14px;">
                        <h6 class="fw-bold text-dark d-flex align-items-center mb-3">
                            <span class="badge bg-primary me-2" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">4</span> Frequently Bought Together <span class="text-muted fw-normal ms-1" style="font-size: 12px;">(Bundles)</span>
                        </h6>
                        <div class="position-relative mb-3">
                            <div class="input-group">
                                <input type="text" class="form-control autocomplete-input" data-category="bought_together" placeholder="Search products..." style="border-top-left-radius: 10px; border-bottom-left-radius: 10px;">
                                <button class="btn btn-primary add-selected-btn" data-category="bought_together" type="button" style="border-top-right-radius: 10px; border-bottom-right-radius: 10px; background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%) !important;">Add Selected</button>
                            </div>
                            <div class="autocomplete-suggestions shadow-sm rounded-3 border bg-white d-none" style="max-height: 200px; overflow-y: auto;"></div>
                        </div>

                        <div class="selected-items-container d-flex flex-wrap gap-2 mb-3" id="selected_bought_together"></div>

                        <div class="suggestions-section">
                            <span class="text-muted small fw-semibold d-block mb-2">Popular Suggestions</span>
                            <div class="suggestions-container row g-2" id="suggestions_bought_together"></div>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer bg-light border-0" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px;">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal" style="border-radius: 8px;">Close</button>
                <button type="button" class="btn btn-success px-4" id="saveRecommendationsBtn" style="border-radius: 8px; background: linear-gradient(180deg, #10b981 0%, #059669 100%) !important; border: 0 !important; color: white !important;">Save Changes</button>
            </div>
        </div>
    </div>
</div>
<style>
/* Recommendations Modal Custom CSS Styles */
.selected-product-chip {
    display: flex;
    align-items: center;
    gap: 10px;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 10px;
    padding: 8px 12px;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.05);
    min-width: 160px;
    max-width: 220px;
}
.selected-product-chip img {
    width: 36px;
    height: 36px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #f1f5f9;
}
.selected-product-chip-info {
    flex: 1;
    min-width: 0;
    line-height: 1.3;
}
.selected-product-chip-info .name {
    font-size: 11.5px;
    font-weight: 600;
    color: #1e293b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
}
.selected-product-chip-info .price {
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
}
.selected-product-chip .remove-chip-btn {
    background: none;
    border: none;
    color: #94a3b8;
    cursor: pointer;
    font-size: 14px;
    padding: 2px;
    line-height: 1;
    transition: color 0.15s ease;
}
.selected-product-chip .remove-chip-btn:hover {
    color: #ef4444;
}

.suggestion-card {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 12px;
    padding: 12px 10px;
    text-align: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    height: 100%;
}
.suggestion-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
}
.suggestion-card img {
    width: 50px;
    height: 50px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 8px;
    border: 1px solid #f1f5f9;
}
.suggestion-card .name {
    font-size: 11px;
    font-weight: 600;
    color: #334155;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    display: block;
    margin-bottom: 2px;
}
.suggestion-card .price {
    font-size: 10px;
    color: #64748b;
    font-weight: 600;
    margin-bottom: 8px;
    display: block;
}
.suggestion-card .btn-add-suggestion {
    font-size: 10px;
    font-weight: 600;
    padding: 3px 12px;
    border-radius: 6px;
    border-width: 1px;
    transition: all 0.15s ease;
}

.autocomplete-suggestions {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    max-height: 200px;
    overflow-y: auto;
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    z-index: 1050;
    margin-top: 4px;
}
.autocomplete-suggestion-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    cursor: pointer;
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s ease;
}
.autocomplete-suggestion-item:last-child {
    border-bottom: none;
}
.autocomplete-suggestion-item:hover {
    background: #f8fafc;
}
.autocomplete-suggestion-item img {
    width: 32px;
    height: 32px;
    object-fit: cover;
    border-radius: 4px;
}

.recommendation-section {
    border: 1px solid #cbd5e1 !important;
    background-color: #ffffff;
    transition: border-color 0.2s ease;
}
.recommendation-section:hover {
    border-color: #004392 !important;
}
</style>
@endsection

@section('script-bottom')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.custom-tab');
        const sections = document.querySelectorAll('.tab-section');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                sections.forEach(s => s.classList.add('d-none'));

                const target = document.querySelector(tab.getAttribute('data-target'));
                if (target) {
                    target.classList.remove('d-none');
                }
            });
        });
    });
</script>
<script type="module">
$(document).ready(function() {
    let currentProductId = null;
    let currentRecommendations = {
        related: [],
        upsell: [],
        cross_sell: [],
        bought_together: []
    };
    let popularProductsList = [];
    let autocompleteResultsCache = {};

    function resetModal() {
        currentRecommendations = {
            related: [],
            upsell: [],
            cross_sell: [],
            bought_together: []
        };
        autocompleteResultsCache = {};
        $('.autocomplete-input').val('');
        $('.autocomplete-suggestions').addClass('d-none').empty();
        $('.selected-items-container').empty();
        $('.suggestions-container').empty();
    }

    function escapeHtml(text) {
        if (!text) return '';
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function renderSelectedItems(cat) {
        const container = $(`#selected_${cat}`);
        container.empty();
        
        const items = currentRecommendations[cat];
        if (items.length === 0) {
            container.html('<span class="text-muted small italic">No products selected</span>');
            return;
        }
        
        items.forEach(item => {
            const chip = `
                <div class="selected-product-chip" data-id="${item.id}">
                    <img src="${item.thumbnail_url}">
                    <div class="selected-product-chip-info">
                        <span class="name" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</span>
                        <span class="price">₹${parseFloat(item.price || 0).toLocaleString('en-IN')}</span>
                    </div>
                    <button type="button" class="remove-chip-btn" data-category="${cat}" data-id="${item.id}">
                        <i class="fa fa-times"></i>
                    </button>
                </div>
            `;
            container.append(chip);
        });
    }

    function renderSuggestions(cat) {
        const container = $(`#suggestions_${cat}`);
        container.empty();
        
        const selectedIds = currentRecommendations[cat].map(item => parseInt(item.id));
        const availableSuggestions = popularProductsList.filter(item => !selectedIds.includes(parseInt(item.id))).slice(0, 4);
        
        if (availableSuggestions.length === 0) {
            container.html('<div class="col-12"><span class="text-muted small">No suggestions available</span></div>');
            return;
        }
        
        availableSuggestions.forEach(item => {
            const card = `
                <div class="col-3 col-md-3">
                    <div class="suggestion-card">
                        <img src="${item.thumbnail_url}">
                        <span class="name" title="${escapeHtml(item.name)}">${escapeHtml(item.name)}</span>
                        <span class="price">₹${parseFloat(item.price || 0).toLocaleString('en-IN')}</span>
                        <button type="button" class="btn btn-sm btn-outline-primary btn-add-suggestion w-100" data-category="${cat}" data-id="${item.id}">
                            Add
                        </button>
                    </div>
                </div>
            `;
            container.append(card);
        });
    }

    function addProductToCategory(cat, item) {
        const selectedIds = currentRecommendations[cat].map(p => parseInt(p.id));
        if (!selectedIds.includes(parseInt(item.id))) {
            currentRecommendations[cat].push(item);
            renderSelectedItems(cat);
            renderSuggestions(cat);
        }
    }

    function removeProductFromCategory(cat, id) {
        currentRecommendations[cat] = currentRecommendations[cat].filter(p => parseInt(p.id) !== parseInt(id));
        renderSelectedItems(cat);
        renderSuggestions(cat);
    }

    function loadDefaultSuggestions() {
        $.ajax({
            url: '/admin/marketplace/recommendations/search-products',
            method: 'GET',
            data: { exclude_id: currentProductId },
            success: function(products) {
                popularProductsList = products;
                const categories = ['related', 'upsell', 'cross_sell', 'bought_together'];
                categories.forEach(cat => {
                    renderSuggestions(cat);
                });
            }
        });
    }

    // Click Recommendations action button
    $(document).on('click', '.open-recommendations-btn', function() {
        currentProductId = $(this).data('product-id');
        $('#rec_product_id').val(currentProductId);
        
        resetModal();
        
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching product details',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/admin/marketplace/recommendations/${currentProductId}/get`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                $('#source_product_name').text(response.product.name);
                $('#source_product_sku').text(response.product.sku || 'N/A');
                $('#source_product_price').text('₹ ' + parseFloat(response.product.price || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                $('#source_product_img').attr('src', response.product.thumbnail_url);
                
                const recs = response.recommendations;
                for (const cat in recs) {
                    currentRecommendations[cat] = recs[cat];
                    renderSelectedItems(cat);
                }
                
                loadDefaultSuggestions();
                
                $('#recommendationsModal').modal('show');
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load product recommendations.'
                });
            }
        });
    });

    // Autocomplete Input Search
    $(document).on('input', '.autocomplete-input', function() {
        const input = $(this);
        const cat = input.data('category');
        const query = input.val().trim();
        const suggestionsBox = input.closest('.position-relative').find('.autocomplete-suggestions');
        
        if (query.length < 2) {
            suggestionsBox.addClass('d-none').empty();
            return;
        }
        
        $.ajax({
            url: '/admin/marketplace/recommendations/search-products',
            method: 'GET',
            data: { q: query, exclude_id: currentProductId },
            success: function(products) {
                suggestionsBox.empty();
                if (products.length === 0) {
                    suggestionsBox.html('<div class="p-2 text-muted small text-center">No products found</div>').removeClass('d-none');
                    return;
                }
                
                const selectedIds = currentRecommendations[cat].map(item => parseInt(item.id));
                const filteredProducts = products.filter(p => !selectedIds.includes(parseInt(p.id)));
                
                if (filteredProducts.length === 0) {
                    suggestionsBox.html('<div class="p-2 text-muted small text-center">All matching products already added</div>').removeClass('d-none');
                    return;
                }
                
                filteredProducts.forEach(item => {
                    autocompleteResultsCache[item.id] = item;
                    const row = `
                        <div class="autocomplete-suggestion-item" data-category="${cat}" data-id="${item.id}">
                            <img src="${item.thumbnail_url}">
                            <div style="flex: 1; min-width: 0;">
                                <div class="fw-semibold text-slate-800 text-truncate" style="font-size: 12px; color: #1e293b;">${escapeHtml(item.name)}</div>
                                <div class="text-muted" style="font-size: 10px;">SKU: ${item.sku || 'N/A'} | ₹${parseFloat(item.price || 0).toLocaleString('en-IN')}</div>
                            </div>
                        </div>
                    `;
                    suggestionsBox.append(row);
                });
                suggestionsBox.removeClass('d-none');
            }
        });
    });

    // Close suggestions box when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('.position-relative').length) {
            $('.autocomplete-suggestions').addClass('d-none').empty();
        }
    });

    // Click suggestion item in autocomplete box
    $(document).on('click', '.autocomplete-suggestion-item', function() {
        const cat = $(this).data('category');
        const id = parseInt($(this).data('id'));
        const item = autocompleteResultsCache[id];
        
        if (item) {
            addProductToCategory(cat, item);
        }
        
        const relativeDiv = $(this).closest('.position-relative');
        relativeDiv.find('.autocomplete-input').val('');
        relativeDiv.find('.autocomplete-suggestions').addClass('d-none').empty();
    });

    // Click Popular Suggestion "Add"
    $(document).on('click', '.btn-add-suggestion', function() {
        const cat = $(this).data('category');
        const id = parseInt($(this).data('id'));
        const item = popularProductsList.find(p => parseInt(p.id) === id);
        if (item) {
            addProductToCategory(cat, item);
        }
    });

    // Remove selected product chip
    $(document).on('click', '.remove-chip-btn', function() {
        const cat = $(this).data('category');
        const id = $(this).data('id');
        removeProductFromCategory(cat, id);
    });

    // Add Selected Button
    $(document).on('click', '.add-selected-btn', function() {
        const cat = $(this).data('category');
        const relativeDiv = $(this).closest('.position-relative');
        const firstItem = relativeDiv.find('.autocomplete-suggestion-item').first();
        if (firstItem.length) {
            firstItem.click();
        } else {
            Swal.fire({
                icon: 'info',
                title: 'No Selection',
                text: 'Please type to search and select a product from suggestions.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });

    // Save recommendations for admin
    $(document).on('click', '#saveRecommendationsBtn', function() {
        let postData = {
            _token: '{{ csrf_token() }}',
            related: currentRecommendations.related.map(p => p.id),
            upsell: currentRecommendations.upsell.map(p => p.id),
            cross_sell: currentRecommendations.cross_sell.map(p => p.id),
            bought_together: currentRecommendations.bought_together.map(p => p.id)
        };
        
        Swal.fire({
            title: 'Saving...',
            text: 'Updating product recommendations',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: `/admin/marketplace/recommendations/${currentProductId}/save`,
            method: 'POST',
            data: postData,
            success: function(response) {
                Swal.close();
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved!',
                        text: 'Product recommendations updated successfully.',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: response.message || 'Failed to save recommendations.'
                    });
                }
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while saving.'
                });
            }
        });
    });
    // Admin Panel editRule override
    window.editRule = function(productId) {
        currentProductId = productId;
        
        // Reset modal
        $('#recommendationsForm')[0].reset();
        for (let cat in currentRecommendations) {
            currentRecommendations[cat] = [];
            renderSelectedItems(cat);
        }
        $('.autocomplete-suggestions').addClass('d-none').empty();
        $('.autocomplete-input').val('');
        
        Swal.fire({
            title: 'Loading...',
            text: 'Fetching product recommendations',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        $.ajax({
            url: `/admin/marketplace/recommendations/${currentProductId}/get`,
            method: 'GET',
            success: function(response) {
                Swal.close();
                $('#source_product_name').text(response.product.name);
                $('#source_product_sku').text(response.product.sku || 'N/A');
                $('#source_product_price').text('₹ ' + parseFloat(response.product.price || 0).toLocaleString('en-IN', {minimumFractionDigits: 2}));
                $('#source_product_img').attr('src', response.product.thumbnail_url);
                
                const recs = response.recommendations;
                for (const cat in recs) {
                    currentRecommendations[cat] = recs[cat];
                    renderSelectedItems(cat);
                }
                
                loadDefaultSuggestions();
                $('#recommendationsModal').modal('show');
            },
            error: function(xhr) {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to load product recommendations.'
                });
            }
        });
    };

    window.deleteRule = function(productId) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will remove all recommendations for this product.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#9ca3af',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `/admin/marketplace/recommendations/${productId}`,
                    method: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire(
                                'Deleted!',
                                'Recommendations have been deleted.',
                                'success'
                            ).then(() => location.reload());
                        } else {
                            Swal.fire('Error!', response.message || 'Failed to delete', 'error');
                        }
                    },
                    error: function() {
                        Swal.fire('Error!', 'Something went wrong', 'error');
                    }
                });
            }
        });
    };
}); // End of document.ready
</script>
@endsection
