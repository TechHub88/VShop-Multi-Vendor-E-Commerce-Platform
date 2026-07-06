{{-- resources/views/admin/approval/verify-product.blade.php --}}
@extends('layouts.vertical', ['title' => 'Product Verification'])

@section('content')

<style>
    :root {
        --v-primary: #4f46e5;
        --v-primary-hover: #4338ca;
        --v-success: #10b981;
        --v-danger: #ef4444;
        --v-warning: #f59e0b;
        --v-info: #3b82f6;
        --v-bg-light: #f8fafc;
    }

    .verification-header {
        background: #ffffff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.03);
        margin-bottom: 1.5rem;
    }

    .action-btn-group .btn {
        font-weight: 600;
        letter-spacing: 0.3px;
        padding: 0.5rem 1.25rem;
        border-radius: 8px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .action-btn-group .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    /* Hero Product Banner */
    .hero-product-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.04);
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .hero-product-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 6px;
        height: 100%;
        background: linear-gradient(180deg, var(--v-primary) 0%, var(--v-info) 100%);
    }

    .score-badge-box {
        background: #ffffff;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 1rem 1.5rem;
        text-align: center;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
    }

    .score-badge-box:hover {
        border-color: var(--v-primary);
        transform: scale(1.03);
    }

    .score-value {
        font-size: 2.2rem;
        font-weight: 800;
        background: linear-gradient(135deg, var(--v-primary) 0%, var(--v-info) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Pass / Fail Custom Toggle Pills */
    .checklist-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        height: 100%;
        transition: all 0.2s ease;
    }

    .checklist-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
    }

    .toggle-pill-group {
        display: flex;
        gap: 6px;
        margin-top: 8px;
    }

    .toggle-pill-btn {
        flex: 1;
        padding: 6px 12px;
        font-size: 0.8rem;
        font-weight: 700;
        border-radius: 8px;
        border: 1px solid #cbd5e1;
        background: #f8fafc;
        color: #64748b;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        transition: all 0.2s ease;
    }

    .toggle-pill-btn:hover {
        background: #e2e8f0;
        color: #334155;
    }

    .toggle-pill-btn.pass.active {
        background: #10b981 !important;
        border-color: #10b981 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .toggle-pill-btn.fail.active {
        background: #ef4444 !important;
        border-color: #ef4444 !important;
        color: #ffffff !important;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    /* Modern Tabs Nav */
    .custom-nav-pills {
        background: #f1f5f9;
        padding: 6px;
        border-radius: 14px;
        gap: 4px;
    }

    .custom-nav-pills .nav-link {
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
        color: #64748b;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .custom-nav-pills .nav-link:hover {
        color: #1e293b;
    }

    .custom-nav-pills .nav-link.active {
        background: #ffffff;
        color: var(--v-primary);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .info-callout {
        background: #f8fafc;
        border-left: 4px solid var(--v-primary);
        border-radius: 8px;
        padding: 1rem;
        margin-top: 0.75rem;
    }

    .product-thumb-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(100px, 1px));
        gap: 12px;
        margin-top: 10px;
    }

    .product-thumb-img {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        transition: transform 0.2s ease, border-color 0.2s ease;
        cursor: pointer;
    }

    .product-thumb-img:hover {
        transform: scale(1.05);
        border-color: var(--v-primary);
    }
</style>

<div class="container-fluid py-3">

    <!-- Header Actions Bar -->
    <div class="verification-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold m-0 text-dark">Product / Service Verification</h4>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1 fs-12">Moderation Dashboard</span>
            </div>
            <p class="text-muted mb-0 small mt-1">Review parameters, run checklist evaluations, and moderate listing approval status.</p>
        </div>
        <div class="action-btn-group d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-danger text-white btn-sm" onclick="triggerVerificationSubmit('rejected')">
                <i class="fa-solid fa-circle-xmark me-1"></i> Reject
            </button>
            <button type="button" class="btn btn-warning text-dark btn-sm" onclick="triggerVerificationSubmit('not-verified')">
                <i class="fa-solid fa-clock me-1"></i> Not Verified
            </button>
            <button type="button" class="btn btn-success text-white btn-sm px-4" onclick="triggerVerificationSubmit('verified')">
                <i class="fa-solid fa-shield-check me-1"></i> Approve & Verify
            </button>
            <a href="{{ route('admin.approval.products') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
    </div>

    @php
        $checklist = json_decode($product->verification_checklist ?? '{}', true);
        $images = $product->images;
        if(is_string($images)) { $images = json_decode($images, true); }
        if(!is_array($images)) { $images = []; }
        $videos = $product->videos;
        if(is_string($videos)) { $videos = json_decode($videos, true); }
        if(!is_array($videos)) { $videos = []; }
    @endphp

    <!-- Executive KPI Grid & Admin Tools -->
    <div class="row g-3 mb-4">
        <!-- Admin Notes Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Admin Verification Notes</h6>
                    <span class="badge bg-light text-muted fw-normal fs-11">Internal Log</span>
                </div>
                <textarea id="adminNotes" class="form-control form-control-sm border-light-subtle rounded-3" rows="3" placeholder="Add custom verification remarks or seller feedback...">{{ $product->admin_notes }}</textarea>
                <div class="d-flex justify-content-between align-items-center mt-2">
                    <small class="text-muted fs-11"><i class="fa-solid fa-lock me-1"></i> Visible to admins</small>
                    <button class="btn btn-primary btn-sm rounded-pill px-3 py-1 fs-12" onclick="saveAdminNotes()">
                        <i class="fa-solid fa-floppy-disk me-1"></i> Save Notes
                    </button>
                </div>
            </div>
        </div>

        <!-- Order Flow Monitoring Card -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3">
                <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-truck-fast text-info me-2"></i> Fulfillment & SLA Workflow</h6>
                <div class="d-flex flex-column gap-2">
                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3">
                        <span class="fs-12 fw-semibold"><i class="fa-solid fa-bolt text-warning me-1"></i> Auto Order Trigger</span>
                        <button class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0 fs-11" onclick="validateAutoOrder()">Validate</button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3">
                        <span class="fs-12 fw-semibold"><i class="fa-solid fa-truck text-info me-1"></i> Supplier Dispatch</span>
                        <button class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0 fs-11" onclick="trackDispatch()">Track</button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded-3">
                        <span class="fs-12 fw-semibold"><i class="fa-solid fa-clock text-success me-1"></i> Delivery SLA Status</span>
                        <button class="btn btn-xs btn-outline-primary rounded-pill px-2 py-0 fs-11" onclick="monitorSLA()">Monitor</button>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top fs-12 text-muted">
                    <span>Integration: <strong class="text-success">Active</strong></span>
                    <span>Auto Order: <strong class="{{ $product->auto_order ? 'text-success' : 'text-danger' }}">{{ $product->auto_order ? 'Enabled' : 'Disabled' }}</strong></span>
                </div>
            </div>
        </div>

        <!-- Recent Activity Logs -->
        <div class="col-lg-4 col-md-12">
            <div class="card h-100 border-0 shadow-sm rounded-4 p-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-dark mb-0"><i class="fa-solid fa-wave-pulse text-warning me-2"></i> Recent Activity</h6>
                    <button class="btn btn-link btn-sm p-0 fs-12 text-primary text-decoration-none" onclick="switchToTab('#activityLogs')">View All →</button>
                </div>
                <div id="recentActivity" class="d-flex flex-column gap-2 overflow-auto" style="max-height: 120px;">
                    @if(isset($versionHistory) && count($versionHistory) > 0)
                        @foreach($versionHistory->take(3) as $log)
                            @php
                                $icon = 'fa-pen';
                                $badgeColor = 'bg-secondary-subtle text-secondary';
                                if(str_contains($log->action, 'price')) { $icon = 'fa-tag'; $badgeColor = 'bg-warning-subtle text-warning'; }
                                elseif(str_contains($log->action, 'description')) { $icon = 'fa-file-lines'; $badgeColor = 'bg-info-subtle text-info'; }
                                elseif(str_contains($log->action, 'approved') || str_contains($log->action, 'verified')) { $icon = 'fa-check-double'; $badgeColor = 'bg-success-subtle text-success'; }
                                elseif(str_contains($log->action, 'rejected')) { $icon = 'fa-xmark'; $badgeColor = 'bg-danger-subtle text-danger'; }
                            @endphp
                            <div class="d-flex align-items-center justify-content-between p-2 rounded-3 bg-light fs-12">
                                <div>
                                    <i class="fa-solid {{ $icon }} me-2"></i>
                                    <span class="fw-medium text-dark">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                </div>
                                <span class="text-muted fs-11">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-muted fs-12 text-center py-3">No recent activity logged</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Product Hero Banner -->
    <div class="hero-product-card">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-indigo text-white px-3 py-1 rounded-pill fs-11 text-uppercase tracking-wider">{{ ucfirst($product->type) }}</span>
                    <span id="productStatusBadge" class="badge {{ $product->status == 'verified' || $product->status == 'approved' || $product->status == 'active' ? 'bg-success-subtle text-success' : ($product->status == 'rejected' ? 'bg-danger-subtle text-danger' : 'bg-warning-subtle text-warning') }} px-3 py-1 rounded-pill fs-11 fw-bold">
                        Status: {{ ucfirst($product->status) }}
                    </span>
                </div>
                <h3 class="fw-bold text-dark mb-2">{{ $product->name }}</h3>
                <div class="d-flex flex-wrap gap-4 text-muted fs-13">
                    <span><i class="fa-solid fa-barcode text-primary me-1"></i> <strong>Code:</strong> {{ $product->product_code ?? 'N/A' }}</span>
                    <span><i class="fa-solid fa-store text-info me-1"></i> <strong>Seller:</strong> {{ $product->seller->name ?? 'N/A' }}</span>
                    <span><i class="fa-solid fa-layer-group text-warning me-1"></i> <strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</span>
                    <span><i class="fa-solid fa-industry text-secondary me-1"></i> <strong>Manufacturer:</strong> {{ $product->manufactured_by ?? 'N/A' }}</span>
                </div>
            </div>
            <div class="col-md-4 text-md-end">
                <div class="d-inline-block score-badge-box">
                    <div class="score-value" id="liveProductScore">{{ $performanceData['product_score'] ?? 60 }}</div>
                    <span class="text-muted fs-12 fw-bold text-uppercase tracking-wider">Product Score</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Verification Form -->
    <form id="verificationForm">
        @csrf
        <input type="hidden" name="admin_notes" id="adminNotesInput">
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills custom-nav-pills nav-justified mb-4" id="verificationTabs" role="tablist">
            <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#basicCheck"><i class="fa-solid fa-list-check me-1"></i> Basic</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#mediaValidation"><i class="fa-solid fa-photo-film me-1"></i> Media</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#detailsValidation"><i class="fa-solid fa-file-circle-check me-1"></i> Details</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#seoCheck"><i class="fa-solid fa-magnifying-glass me-1"></i> SEO</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#pricingValidation"><i class="fa-solid fa-coins me-1"></i> Pricing</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contentModeration"><i class="fa-solid fa-shield-cat me-1"></i> Content Mod</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#activityLogs"><i class="fa-solid fa-timeline me-1"></i> Activity</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#versionHistoryTab"><i class="fa-solid fa-code-compare me-1"></i> Versions</a></li>
            <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#performanceTab"><i class="fa-solid fa-chart-line me-1"></i> Performance</a></li>
        </ul>

        <div class="tab-content mb-4">
            
            <!-- TAB 1: BASIC CHECK -->
            <div class="tab-pane fade show active" id="basicCheck">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-clipboard-check text-primary me-2"></i> Basic Information Parameters</h5>
                    <div class="row g-3 mb-4">
                        <!-- Title Quality -->
                        <div class="col-md-6 col-lg-3">
                            <div class="checklist-card">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark fs-13">Title Quality</span>
                                    <span class="badge bg-primary-subtle text-primary fs-11">{{ strlen($product->name ?? '') }} chars</span>
                                </div>
                                <input type="hidden" name="title_quality" id="val_title_quality" value="{{ $checklist['basic_check']['title_quality'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['basic_check']['title_quality'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('title_quality', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['basic_check']['title_quality'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('title_quality', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <!-- Description Quality -->
                        <div class="col-md-6 col-lg-3">
                            <div class="checklist-card">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark fs-13">Description Quality</span>
                                    <span class="badge bg-primary-subtle text-primary fs-11">{{ str_word_count($product->description ?? '') }} words</span>
                                </div>
                                <input type="hidden" name="description_quality" id="val_description_quality" value="{{ $checklist['basic_check']['description_quality'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['basic_check']['description_quality'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('description_quality', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['basic_check']['description_quality'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('description_quality', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <!-- Category Correctness -->
                        <div class="col-md-6 col-lg-3">
                            <div class="checklist-card">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark fs-13">Category Correctness</span>
                                </div>
                                <input type="hidden" name="category_correctness" id="val_category_correctness" value="{{ $checklist['basic_check']['category_correctness'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['basic_check']['category_correctness'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('category_correctness', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['basic_check']['category_correctness'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('category_correctness', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <!-- Organizer Authenticity -->
                        <div class="col-md-6 col-lg-3">
                            <div class="checklist-card">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark fs-13">Brand/Organizer Auth</span>
                                </div>
                                <input type="hidden" name="organizer_authenticity" id="val_organizer_authenticity" value="{{ $checklist['basic_check']['organizer_authenticity'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['basic_check']['organizer_authenticity'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('organizer_authenticity', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['basic_check']['organizer_authenticity'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('organizer_authenticity', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Product Content Preview -->
                    <div class="info-callout">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-align-left text-primary me-2"></i> Product Description Preview</h6>
                        <p class="text-secondary mb-0 fs-13 lh-base">{!! nl2br(e($product->description ?? 'No description provided.')) !!}</p>
                    </div>
                </div>
            </div>

            <!-- TAB 2: MEDIA VALIDATION -->
            <div class="tab-pane fade" id="mediaValidation">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-images text-primary me-2"></i> Media & Visual Compliance</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Image Quality & Resolution</span>
                                <input type="hidden" name="image_quality" id="val_image_quality" value="{{ $checklist['media_validation']['image_quality'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['media_validation']['image_quality'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('image_quality', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['media_validation']['image_quality'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('image_quality', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Video Link Validity</span>
                                <input type="hidden" name="video_link_validity" id="val_video_link_validity" value="{{ $checklist['media_validation']['video_link_validity'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['media_validation']['video_link_validity'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('video_link_validity', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['media_validation']['video_link_validity'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('video_link_validity', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Watermark & Policy Compliance</span>
                                <input type="hidden" name="content_compliance" id="val_content_compliance" value="{{ $checklist['media_validation']['content_compliance'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['media_validation']['content_compliance'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('content_compliance', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['media_validation']['content_compliance'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('content_compliance', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Media Assets Showcase -->
                    <div class="p-3 bg-light rounded-3">
                        <h6 class="fw-bold text-dark mb-2"><i class="fa-solid fa-photo-film text-info me-2"></i> Uploaded Media Assets ({{ count($images) }} Images, {{ count($videos) }} Videos)</h6>
                        @if(count($images) > 0)
                            <div class="product-thumb-grid mb-3">
                                @foreach($images as $img)
                                    <img src="{{ asset($img) }}" class="product-thumb-img" onclick="window.open('{{ asset($img) }}', '_blank')" onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                @endforeach
                            </div>
                        @else
                            <p class="text-muted fs-12 mb-2">No uploaded images found.</p>
                        @endif

                        @if(count($videos) > 0)
                            <div class="d-flex flex-wrap gap-2 pt-2 border-top">
                                @foreach($videos as $vid)
                                    <a href="{{ $vid }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill fs-12"><i class="fa-solid fa-circle-play me-1"></i> Watch Video Link</a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB 3: DETAILS VALIDATION -->
            <div class="tab-pane fade" id="detailsValidation">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shield-check text-primary me-2"></i> Specifications & Service Conditions</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Itinerary / Spec Clarity</span>
                                <input type="hidden" name="itinerary_clarity" id="val_itinerary_clarity" value="{{ $checklist['details_validation']['itinerary_clarity'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['details_validation']['itinerary_clarity'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('itinerary_clarity', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['details_validation']['itinerary_clarity'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('itinerary_clarity', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Inclusion / Exclusion Definition</span>
                                <input type="hidden" name="inclusion_exclusion" id="val_inclusion_exclusion" value="{{ $checklist['details_validation']['inclusion_exclusion'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['details_validation']['inclusion_exclusion'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('inclusion_exclusion', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['details_validation']['inclusion_exclusion'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('inclusion_exclusion', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Refund Policy Transparency</span>
                                <input type="hidden" name="refund_policy_clarity" id="val_refund_policy_clarity" value="{{ $checklist['details_validation']['refund_policy_clarity'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['details_validation']['refund_policy_clarity'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('refund_policy_clarity', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['details_validation']['refund_policy_clarity'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('refund_policy_clarity', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-success-subtle border border-success-subtle rounded-3">
                                <h6 class="fw-bold text-success mb-1"><i class="fa-solid fa-circle-plus me-1"></i> Inclusions</h6>
                                <p class="fs-13 text-dark mb-0">{!! nl2br(e($product->inclusion ?? 'No explicit inclusions listed.')) !!}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-danger-subtle border border-danger-subtle rounded-3">
                                <h6 class="fw-bold text-danger mb-1"><i class="fa-solid fa-circle-minus me-1"></i> Exclusions</h6>
                                <p class="fs-13 text-dark mb-0">{!! nl2br(e($product->exclusion ?? 'No explicit exclusions listed.')) !!}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: SEO CHECK -->
            <div class="tab-pane fade" id="seoCheck">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-magnifying-glass-chart text-primary me-2"></i> Search Engine Optimization Evaluation</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Tags Relevance</span>
                                <input type="hidden" name="tags_relevance" id="val_tags_relevance" value="{{ $checklist['seo_check']['tags_relevance'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['seo_check']['tags_relevance'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('tags_relevance', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['seo_check']['tags_relevance'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('tags_relevance', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Keyword Density & Quality</span>
                                <input type="hidden" name="keyword_quality" id="val_keyword_quality" value="{{ $checklist['seo_check']['keyword_quality'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['seo_check']['keyword_quality'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('keyword_quality', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['seo_check']['keyword_quality'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('keyword_quality', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Spam / Keyword Stuffing Check</span>
                                <input type="hidden" name="no_spam" id="val_no_spam" value="{{ $checklist['seo_check']['no_spam'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['seo_check']['no_spam'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('no_spam', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['seo_check']['no_spam'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('no_spam', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-3 bg-light rounded-3">
                        <div class="mb-2"><strong>SEO Tags:</strong> <span class="badge bg-primary-subtle text-primary ms-1 fs-12">{{ $product->seo_tags ?? 'N/A' }}</span></div>
                        <div><strong>Meta Description:</strong> <p class="fs-13 text-secondary mb-0 mt-1">{{ $product->meta_description ?? 'No meta description configured.' }}</p></div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: PRICING VALIDATION -->
            <div class="tab-pane fade" id="pricingValidation">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-calculator text-primary me-2"></i> Pricing & Commercial Verification</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Price Reasonability</span>
                                <input type="hidden" name="price_reasonability" id="val_price_reasonability" value="{{ $checklist['pricing_validation']['price_reasonability'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['pricing_validation']['price_reasonability'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('price_reasonability', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['pricing_validation']['price_reasonability'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('price_reasonability', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Discount Logic</span>
                                <input type="hidden" name="discount_logic" id="val_discount_logic" value="{{ $checklist['pricing_validation']['discount_logic'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['pricing_validation']['discount_logic'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('discount_logic', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['pricing_validation']['discount_logic'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('discount_logic', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Tax Calculation</span>
                                <input type="hidden" name="tax_calculation" id="val_tax_calculation" value="{{ $checklist['pricing_validation']['tax_calculation'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['pricing_validation']['tax_calculation'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('tax_calculation', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['pricing_validation']['tax_calculation'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('tax_calculation', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="checklist-card">
                                <span class="fw-bold text-dark fs-13">Net Total Correctness</span>
                                <input type="hidden" name="net_total_correctness" id="val_net_total_correctness" value="{{ $checklist['pricing_validation']['net_total_correctness'] ?? '' }}">
                                <div class="toggle-pill-group">
                                    <button type="button" class="toggle-pill-btn pass {{ ($checklist['pricing_validation']['net_total_correctness'] ?? '') == 'pass' ? 'active' : '' }}" onclick="setCheckVal('net_total_correctness', 'pass', this)"><i class="fa-solid fa-check"></i> Approve</button>
                                    <button type="button" class="toggle-pill-btn fail {{ ($checklist['pricing_validation']['net_total_correctness'] ?? '') == 'fail' ? 'active' : '' }}" onclick="setCheckVal('net_total_correctness', 'fail', this)"><i class="fa-solid fa-xmark"></i> Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-3"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block mb-1">Base MRP / Price</small><h5 class="fw-bold text-dark mb-0">₹{{ number_format($product->price ?? 0, 2) }}</h5></div></div>
                        <div class="col-md-3"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block mb-1">Configured Discount</small><h5 class="fw-bold text-warning mb-0">{{ $product->discount ?? 0 }}%</h5></div></div>
                        <div class="col-md-3"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block mb-1">Final Listing Price</small><h5 class="fw-bold text-success mb-0">₹{{ number_format($product->final_price ?? $product->price ?? 0, 2) }}</h5></div></div>
                        <div class="col-md-3"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block mb-1">Platform Margin</small><h5 class="fw-bold text-primary mb-0">₹{{ number_format(($product->final_price ?? $product->price ?? 0) - ($product->supplier_cost ?? 0), 2) }}</h5></div></div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: CONTENT MODERATION -->
            <div class="tab-pane fade" id="contentModeration">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-shield-halved text-danger me-2"></i> Advanced Content Moderation Tools</h5>
                    
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-light h-100">
                                <h6 class="fw-bold text-dark"><i class="fa-solid fa-eraser text-danger me-2"></i> Remove Misleading Content</h6>
                                <p class="text-muted fs-12 mb-3">Purge false or unverified claim text from specific fields.</p>
                                <select id="misleadingField" class="form-select form-select-sm mb-2">
                                    <option value="name">Product Title</option>
                                    <option value="description">Description</option>
                                    <option value="seo_tags">SEO Tags</option>
                                    <option value="inclusion">Inclusions</option>
                                    <option value="exclusion">Exclusions</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="removeMisleadingContent()">
                                    <i class="fa-solid fa-trash-can me-1"></i> Execute Removal
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-light h-100">
                                <h6 class="fw-bold text-dark"><i class="fa-solid fa-copy text-warning me-2"></i> Flag Duplicate Listing</h6>
                                <p class="text-muted fs-12 mb-3">Mark this item as a duplicate of another catalog entry.</p>
                                <input type="text" id="duplicateProductId" class="form-control form-control-sm mb-2" placeholder="Target Product ID or URL">
                                <button type="button" class="btn btn-sm btn-outline-warning w-100 text-dark" onclick="flagDuplicate()">
                                    <i class="fa-solid fa-flag me-1"></i> Mark Duplicate
                                </button>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="border rounded-4 p-3 bg-light h-100">
                                <h6 class="fw-bold text-dark"><i class="fa-solid fa-ban text-danger me-2"></i> Block Inappropriate Service</h6>
                                <p class="text-muted fs-12 mb-3">Block listing due to direct policy or legal violation.</p>
                                <select id="blockReason" class="form-select form-select-sm mb-2">
                                    <option value="Prohibited content">Prohibited content</option>
                                    <option value="Fraudulent activity">Fraudulent activity</option>
                                    <option value="Policy violation">Policy violation</option>
                                    <option value="Spam/Misleading">Spam/Misleading</option>
                                </select>
                                <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="blockInappropriate()">
                                    <i class="fa-solid fa-lock me-1"></i> Block Listing
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 7: ACTIVITY LOGS -->
            <div class="tab-pane fade" id="activityLogs">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-history text-primary me-2"></i> Audit Trail & Action History</h5>
                    <div id="fullActivityLogs" class="d-flex flex-column gap-2" style="max-height: 450px; overflow-y: auto;">
                        @if(isset($versionHistory) && count($versionHistory) > 0)
                            @foreach($versionHistory as $log)
                                <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <strong class="text-dark fs-14">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</strong>
                                        <span class="text-muted fs-12">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                                    </div>
                                    <small class="text-secondary d-block">Triggered by user ID #{{ $log->user_id ?? 'System Admin' }}</small>
                                </div>
                            @endforeach
                        @else
                            <div class="text-muted text-center py-4 fs-13">No activity logs recorded yet.</div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB 8: VERSIONS -->
            <div class="tab-pane fade" id="versionHistoryTab">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-code-branch text-primary me-2"></i> Version Control Snapshots</h5>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Version</th>
                                    <th>Action</th>
                                    <th>Date</th>
                                    <th class="text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($versionHistory as $ver)
                                    <tr>
                                        <td><code>v{{ count($versionHistory ?? []) - $loop->index }}</code></td>
                                        <td><span class="badge bg-secondary-subtle text-secondary fs-12">{{ ucfirst($ver->action ?? 'Update') }}</span></td>
                                        <td>{{ is_object($ver->created_at ?? null) ? $ver->created_at->format('Y-m-d H:i') : ($ver->created_at ?? 'N/A') }}</td>
                                        <td class="text-end">
                                            <button type="button" class="btn btn-sm btn-outline-info me-1" onclick="viewVersion({{ $ver->id }})">View Snapshot</button>
                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="rollbackVersion({{ $ver->id }})">Rollback</button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-center text-muted py-4">No snapshot history available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 9: PERFORMANCE -->
            <div class="tab-pane fade" id="performanceTab">
                <div class="card border-0 shadow-sm rounded-4 p-4">
                    <h5 class="fw-bold text-dark mb-3"><i class="fa-solid fa-chart-line text-primary me-2"></i> Marketplace Analytics</h5>
                    <div class="row g-3">
                        <div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block">Total Impressions / Views</small><h4 class="fw-bold text-primary mb-0">{{ $performanceData['metrics']['views'] ?? 0 }}</h4></div></div>
                        <div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block">Completed Orders</small><h4 class="fw-bold text-success mb-0">{{ $performanceData['metrics']['total_sales'] ?? 0 }}</h4></div></div>
                        <div class="col-md-4"><div class="p-3 bg-light rounded-3 text-center"><small class="text-muted d-block">Verification Health Score</small><h4 class="fw-bold text-warning mb-0">{{ $performanceData['product_score'] ?? 60 }}%</h4></div></div>
                    </div>
                </div>
            </div>

        </div>
    </form>
</div>

<!-- Version Snapshot Modal -->
<div class="modal fade" id="versionViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="fa-solid fa-code me-2"></i> Version Snapshot Data</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="versionSnapshotContent">
                <pre class="bg-dark text-light p-3 rounded-3 fs-12" style="max-height: 450px; overflow: auto;"></pre>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- Toastr Notifications -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
let productId = {{ $product->id }};
let csrfToken = '{{ csrf_token() }}';

// Custom Pass / Fail Pill Selector Function
function setCheckVal(fieldName, val, btnEl) {
    let inputEl = document.getElementById('val_' + fieldName);
    if (!inputEl) return;
    
    // Toggle active state on buttons in same group
    let parentGroup = btnEl.closest('.toggle-pill-group');
    if (parentGroup) {
        let btns = parentGroup.querySelectorAll('.toggle-pill-btn');
        btns.forEach(b => b.classList.remove('active'));
    }
    
    if (inputEl.value === val) {
        inputEl.value = ''; // Deselect
    } else {
        inputEl.value = val;
        btnEl.classList.add('active');
    }

    recalculateLiveScore();
    autoSaveChecklistBackend();
}

// Auto Save Checklist to Backend Database
function autoSaveChecklistBackend() {
    let formData = new FormData(document.getElementById('verificationForm'));
    formData.append('_token', csrfToken);

    $.ajax({
        url: `/admin/approval/product/${productId}/auto-save-checklist`,
        method: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if(response.success && response.status) {
                let badge = $('#productStatusBadge');
                if(badge.length) {
                    badge.removeClass('bg-warning-subtle text-warning bg-danger-subtle text-danger bg-success-subtle text-success');
                    if(response.status === 'approved' || response.status === 'verified') {
                        badge.addClass('bg-success-subtle text-success').text('Status: Approved');
                    } else if(response.status === 'rejected') {
                        badge.addClass('bg-danger-subtle text-danger').text('Status: Rejected');
                    } else {
                        badge.addClass('bg-warning-subtle text-warning').text('Status: Not Verified');
                    }
                }
            }
        }
    });
}

// Recalculate Product Score Live
function recalculateLiveScore() {
    let inputs = document.querySelectorAll('input[id^="val_"]');
    let total = inputs.length;
    let passes = 0;
    
    inputs.forEach(i => {
        if (i.value === 'pass') passes++;
    });

    if (total > 0) {
        let score = Math.round((passes / total) * 100);
        let scoreEl = document.getElementById('liveProductScore');
        if (scoreEl) scoreEl.innerText = score;
    }
}

// Trigger Verification Submit with SweetAlert Confirmation
function triggerVerificationSubmit(status) {
    let adminNotes = document.getElementById('adminNotes').value;
    document.getElementById('adminNotesInput').value = adminNotes;
    
    let formData = new FormData(document.getElementById('verificationForm'));
    formData.append('verification_status', status);
    formData.append('_token', csrfToken);
    
    let statusLabels = {
        'verified': 'Approve & Verify',
        'rejected': 'Reject',
        'not-verified': 'Mark as Not Verified'
    };

    Swal.fire({
        title: 'Confirm Verification?',
        text: `Are you sure you want to mark this product as "${statusLabels[status]}"?`,
        icon: status === 'verified' ? 'success' : (status === 'rejected' ? 'error' : 'warning'),
        showCancelButton: true,
        confirmButtonText: 'Yes, Submit',
        confirmButtonColor: status === 'verified' ? '#10b981' : (status === 'rejected' ? '#ef4444' : '#f59e0b')
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({ title: 'Submitting...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
            
            $.ajax({
                url: "{{ route('admin.approval.submit.verification', $product->id) }}",
                method: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    Swal.close();
                    if(response.success) {
                        toastr.success('Verification submitted successfully! Status updated to ' + (response.status_text || 'Approved'));
                        
                        // Update status badge on page live
                        let badge = $('#productStatusBadge');
                        if(badge.length) {
                            badge.removeClass('bg-warning-subtle text-warning bg-danger-subtle text-danger bg-success-subtle text-success');
                            if(response.status === 'approved' || response.status === 'verified') {
                                badge.addClass('bg-success-subtle text-success').text('Status: Approved');
                            } else if(response.status === 'rejected') {
                                badge.addClass('bg-danger-subtle text-danger').text('Status: Rejected');
                            } else {
                                badge.addClass('bg-warning-subtle text-warning').text('Status: Not Verified');
                            }
                        }

                        setTimeout(() => {
                            window.location.href = response.redirect;
                        }, 1200);
                    } else {
                        toastr.error(response.message || 'Failed to submit verification');
                    }
                },
                error: function(xhr) {
                    Swal.close();
                    toastr.error(xhr.responseJSON?.message || 'Failed to submit verification');
                }
            });
        }
    });
}

// Save Admin Notes
function saveAdminNotes() {
    let notes = $('#adminNotes').val();
    $.ajax({
        url: `/admin/approval/product/${productId}/save-notes`,
        method: "POST",
        data: { admin_notes: notes, _token: csrfToken },
        success: function(response) {
            toastr.success('Admin notes saved successfully!');
        },
        error: function() {
            toastr.error('Failed to save notes');
        }
    });
}

// Order Flow Actions
function validateAutoOrder() {
    $.ajax({
        url: `/admin/approval/product/${productId}/validate-auto-order`,
        method: "GET",
        success: function(response) {
            toastr.info(response.message || 'Auto order validation checked!');
        }
    });
}

function trackDispatch() {
    $.ajax({
        url: `/admin/approval/product/${productId}/track-dispatch`,
        method: "GET",
        success: function(response) {
            toastr.info(response.message || 'Dispatch tracking telemetry active!');
        }
    });
}

function monitorSLA() {
    $.ajax({
        url: `/admin/approval/product/${productId}/monitor-sla`,
        method: "GET",
        success: function(response) {
            toastr.info(response.message || 'Delivery SLA telemetry active!');
        }
    });
}

// Content Moderation Actions
function removeMisleadingContent() {
    let field = $('#misleadingField').val();
    let reason = prompt('Enter reason for removing misleading content:');
    if(reason) {
        $.ajax({
            url: `/admin/approval/product/${productId}/remove-misleading`,
            method: "POST",
            data: { field_to_remove: field, removal_reason: reason, _token: csrfToken },
            success: function(response) {
                toastr.success('Misleading content purged successfully!');
                location.reload();
            },
            error: function() {
                toastr.error('Failed to remove content');
            }
        });
    }
}

function flagDuplicate() {
    let duplicateId = $('#duplicateProductId').val();
    if(!duplicateId) {
        toastr.warning('Please enter duplicate Product ID or URL');
        return;
    }
    $.ajax({
        url: `/admin/approval/product/${productId}/flag-duplicate`,
        method: "POST",
        data: { duplicate_id: duplicateId, _token: csrfToken },
        success: function(response) {
            toastr.success('Listing flagged as duplicate!');
        }
    });
}

function blockInappropriate() {
    let reason = $('#blockReason').val();
    if(confirm(`Block this listing? Reason: ${reason}`)) {
        $.ajax({
            url: `/admin/approval/product/${productId}/block-inappropriate`,
            method: "POST",
            data: { reason: reason, _token: csrfToken },
            success: function(response) {
                toastr.success('Listing blocked successfully!');
                location.reload();
            }
        });
    }
}

// Version Control Actions
function viewVersion(versionId) {
    $.ajax({
        url: `/admin/approval/product/${productId}/version/${versionId}`,
        method: "GET",
        success: function(response) {
            if(response.success && response.snapshot) {
                $('#versionSnapshotContent pre').text(JSON.stringify(response.snapshot, null, 2));
                $('#versionViewModal').modal('show');
            }
        }
    });
}

function rollbackVersion(versionId) {
    if(confirm('Rollback catalog entry to this previous version snapshot?')) {
        $.ajax({
            url: `/admin/approval/product/${productId}/rollback/${versionId}`,
            method: "POST",
            data: { _token: csrfToken },
            success: function(response) {
                toastr.success('Rollback completed successfully!');
                setTimeout(() => location.reload(), 1000);
            }
        });
    }
}

// Helper for Bootstrap 5 tab switching
function switchToTab(tabSelector) {
    let triggerEl = document.querySelector('#verificationTabs a[href="' + tabSelector + '"]');
    if (triggerEl) {
        let tab = bootstrap.Tab.getOrCreateInstance(triggerEl);
        tab.show();
    }
}
</script>
@endsection
