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
        <div class="custom-tab active">Product Rules</div>
        <div class="custom-tab">Location Rules</div>
        <div class="custom-tab">Sponsored Recommendations</div>
        <div class="custom-tab">AI Recommendation Rules</div>
    </div>

    <!-- 1. Product Rules -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">
                1. Product Rules <span>(What to recommend with which product)</span>
            </div>
            <button class="btn-add"><i class="ri-add-line"></i> Add Rule</button>
        </div>
        
        <div class="d-flex align-items-end mb-3">
            <div class="rule-col-header" style="width: 150px;">If Buyer Views / Buys</div>
            <div class="rule-col-header" style="margin-left: 50px;">Then Recommend</div>
        </div>

        <div class="rule-row">
            <div class="product-box">
                <div class="product-img">
                    <i class="ri-smartphone-line" style="font-size: 40px; color: #4b5563;"></i>
                </div>
                <div class="product-name">Mobile</div>
            </div>

            <i class="ri-arrow-right-line arrow-icon"></i>

            <div class="recommendations-wrapper">
                <div class="product-box">
                    <div class="product-img">
                        <i class="ri-plug-line" style="font-size: 32px; color: #9ca3af;"></i>
                    </div>
                    <div class="product-name">Charger</div>
                </div>
                <div class="product-box">
                    <div class="product-img">
                        <i class="ri-smartphone-line" style="font-size: 32px; color: #9ca3af; transform: scaleX(-1);"></i>
                    </div>
                    <div class="product-name">Mobile Cover</div>
                </div>
                <div class="product-box">
                    <div class="product-img">
                        <i class="ri-customer-service-2-line" style="font-size: 32px; color: #9ca3af;"></i>
                    </div>
                    <div class="product-name">Earbuds</div>
                </div>
            </div>

            <div class="action-icons">
                <i class="ri-edit-2-line"></i>
                <i class="ri-delete-bin-line" style="color:#ef4444;"></i>
            </div>
        </div>
    </div>

    <!-- 2. Location Based Rules -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">
                2. Location Based Rules <span>(Show recommendations by location)</span>
            </div>
            <button class="btn-add"><i class="ri-add-line"></i> Add Rule</button>
        </div>

        <div class="rule-row">
            <div class="location-box">
                <i class="ri-map-pin-line location-icon"></i>
                Banaras
            </div>

            <i class="ri-arrow-right-line arrow-icon"></i>

            <div class="recommendations-wrapper" style="border:none; padding:0; background:transparent;">
                <div class="product-box" style="width: auto;">
                    <div class="location-img">
                        <img src="https://images.unsplash.com/photo-1571536802807-30451e3955d8?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Boat Ride">
                    </div>
                    <div class="product-name mt-2">Boat Ride</div>
                </div>
                <div class="product-box" style="width: auto;">
                    <div class="location-img">
                        <img src="https://images.unsplash.com/photo-1600055106192-386d34e628fc?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Ganga Aarti">
                    </div>
                    <div class="product-name mt-2">Ganga Aarti</div>
                </div>
                <div class="product-box" style="width: auto;">
                    <div class="location-img">
                        <img src="https://images.unsplash.com/photo-1589301760014-d929f3979dbc?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Food Walk">
                    </div>
                    <div class="product-name mt-2">Food Walk</div>
                </div>
            </div>
        </div>

        <div class="rule-row">
            <div class="location-box">
                <i class="ri-map-pin-line location-icon"></i>
                Goa
            </div>

            <i class="ri-arrow-right-line arrow-icon"></i>

            <div class="recommendations-wrapper" style="border:none; padding:0; background:transparent;">
                <div class="product-box" style="width: auto;">
                    <div class="location-img">
                        <img src="https://images.unsplash.com/photo-1544550581-5f7ceaf7f992?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Water Sports">
                    </div>
                    <div class="product-name mt-2">Water Sports</div>
                </div>
                <div class="product-box" style="width: auto;">
                    <div class="location-img">
                        <img src="https://images.unsplash.com/photo-1599839619722-39751411ea63?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Cruise">
                    </div>
                    <div class="product-name mt-2">Cruise</div>
                </div>
                <div class="product-box" style="width: auto;">
                    <div class="location-img">
                        <img src="https://images.unsplash.com/photo-1520108253160-c3d5e219fb09?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="Parasailing">
                    </div>
                    <div class="product-name mt-2">Parasailing</div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. Sponsored Recommendations -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">
                3. Sponsored Recommendations <span>(Paid priority placement)</span>
            </div>
            <button class="btn-add"><i class="ri-add-line"></i> Add Campaign</button>
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
                    <tr>
                        <td class="fw-medium">iPhone Accessories</td>
                        <td>Product</td>
                        <td>iPhone 14</td>
                        <td>CellCity</td>
                        <td class="status-active">Active</td>
                        <td>
                            <div class="d-flex gap-2">
                                <i class="ri-edit-2-line" style="cursor:pointer; color:#9ca3af;"></i>
                                <i class="ri-delete-bin-line" style="cursor:pointer; color:#ef4444;"></i>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Summer Sale Boost</td>
                        <td>Category</td>
                        <td>Travel & Tours</td>
                        <td>Goa Trips</td>
                        <td class="status-active">Active</td>
                        <td>
                            <div class="d-flex gap-2">
                                <i class="ri-edit-2-line" style="cursor:pointer; color:#9ca3af;"></i>
                                <i class="ri-delete-bin-line" style="cursor:pointer; color:#ef4444;"></i>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Featured Cruises</td>
                        <td>Service</td>
                        <td>Cruise Ride</td>
                        <td>Oceanic Travels</td>
                        <td class="status-paused">Paused</td>
                        <td>
                            <div class="d-flex gap-2">
                                <i class="ri-edit-2-line" style="cursor:pointer; color:#9ca3af;"></i>
                                <i class="ri-delete-bin-line" style="cursor:pointer; color:#ef4444;"></i>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. AI Recommendation Rules -->
    <div class="section-card">
        <div class="section-header">
            <div class="section-title">
                4. AI Recommendation Rules <span>(Behavior based recommendations)</span>
            </div>
            <button class="btn-add"><i class="ri-add-line"></i> Add Rule</button>
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
                    <tr>
                        <td class="fw-medium">People bought A → Recommend B</td>
                        <td>If customer buys Product A, show Product B</td>
                        <td class="status-active">Active</td>
                        <td class="fw-medium">High</td>
                        <td><button class="btn-action-sm"><i class="ri-more-fill"></i></button></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Frequently Bought Together</td>
                        <td>Show items frequently bought together</td>
                        <td class="status-active">Active</td>
                        <td class="fw-medium">High</td>
                        <td><button class="btn-action-sm"><i class="ri-more-fill"></i></button></td>
                    </tr>
                    <tr>
                        <td class="fw-medium">Recently Viewed</td>
                        <td>Show recently viewed similar items</td>
                        <td class="status-active">Active</td>
                        <td class="fw-medium">Medium</td>
                        <td><button class="btn-action-sm"><i class="ri-more-fill"></i></button></td>
                    </tr>
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
@endsection

