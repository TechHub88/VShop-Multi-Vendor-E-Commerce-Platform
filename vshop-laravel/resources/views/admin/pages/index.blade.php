@extends('layouts.vertical', ['title' => 'Page Management'])

@section('content')

<style>
    /* White/Blue Theme */
    .card {
        border: none;
        box-shadow: 0 0 20px rgba(0,0,0,0.05);
        border-radius: 12px;
    }
    
    .card-header {
        background: white;
        border-bottom: 1px solid #eef2f7;
        padding: 1rem 1.5rem;
        border-radius: 12px 12px 0 0 !important;
    }
    
    .table th {
        background: #f8faff !important;
        color: #1e293b !important;
        font-weight: 600;
        border-bottom: 2px solid #e2e8f0;
    }
    
    .table td {
        vertical-align: middle;
        color: #334155;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #2c7fc3 0%, #233d79 100%);
        border: 0;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 500;
    }
    
    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 5px 15px rgba(44,127,195,0.3);
    }
    
    .btn-warning, .btn-danger, .btn-info {
        border: 0;
        padding: 5px 10px;
        border-radius: 6px;
    }
    
    .btn-warning { background: #f59e0b; color: white; }
    .btn-danger { background: #ef4444; color: white; }
    .btn-info { background: #3b82f6; color: white; }
    
    .btn-warning:hover, .btn-danger:hover, .btn-info:hover {
        transform: translateY(-1px);
    }
    
    /* KPI Cards */
    .kpi-card {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #eef2f7;
        transition: all 0.3s ease;
    }
    
    .kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
    }
    
    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .kpi-value {
        font-size: 28px;
        font-weight: 700;
        color: #1e293b;
        line-height: 1.2;
    }
    
    .kpi-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 500;
        letter-spacing: 0.3px;
    }
    
    /* Badges */
    .badge {
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 500;
        font-size: 11px;
    }
    
    .badge-success { background: #10b981; color: white; }
    .badge-danger { background: #ef4444; color: white; }
    .badge-warning { background: #f59e0b; color: white; }
    .badge-info { background: #3b82f6; color: white; }
    .badge-secondary { background: #64748b; color: white; }
    .badge-primary { background: #2c7fc3; color: white; }
    
    /* Filter Bar */
    .filter-bar {
        background: #f8faff;
        padding: 1rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }
    
    .filter-bar select, .filter-bar input {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 13px;
    }
    
    /* Modal */
    .modal-content {
        border-radius: 16px;
        border: none;
    }
    
    .modal-header {
        border-bottom: 1px solid #eef2f7;
        background: #f8faff;
        border-radius: 16px 16px 0 0;
    }
    
    .detail-section {
        background: #f8faff;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .data-detail-label {
        font-weight: 600;
        color: #2c7fc3;
        min-width: 140px;
        display: inline-block;
        font-size: 13px;
    }
    
    /* Table */
    .table-responsive {
        border-radius: 12px;
    }
    
    .action-buttons {
        display: flex;
        gap: 6px;
        flex-wrap: wrap;
    }
    
    .action-buttons .btn {
        padding: 5px 10px;
        font-size: 12px;
    }
</style>

<div class="container-fluid pt-3">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold" style="color: #1e293b;">Page Management</h4>
            <p class="text-muted mb-0">Manage all your website pages from one place</p>
        </div>
        <a href="{{ route('admin.pages.create') }}" class="btn btn-primary">
            <i class="mdi mdi-plus-circle me-1"></i> Add New Page
        </a>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3" role="alert">
            <i class="mdi mdi-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KPI CARDS --}}
    @php
        // Calculate stats safely for both Collection and Paginator
        $totalPagesCount = $pages->count();
        $activePagesCount = $pages->where('status', 1)->count();
        $pendingPagesCount = $pages->where('approval_status', 'pending_review')->count();
        $featuredPagesCount = $pages->where('is_featured', 1)->count();
    @endphp
    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value">{{ $totalPagesCount }}</div>
                        <div class="kpi-label">Total Pages</div>
                    </div>
                    <div class="kpi-icon" style="background: #e6f0ff;">
                        <i class="mdi mdi-file-document" style="color: #2c7fc3; font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value">{{ $activePagesCount }}</div>
                        <div class="kpi-label">Active Pages</div>
                    </div>
                    <div class="kpi-icon" style="background: #e6f9ed;">
                        <i class="mdi mdi-check-circle" style="color: #10b981; font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value">{{ $pendingPagesCount }}</div>
                        <div class="kpi-label">Pending Review</div>
                    </div>
                    <div class="kpi-icon" style="background: #fef3e6;">
                        <i class="mdi mdi-clock-outline" style="color: #f59e0b; font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-value">{{ $featuredPagesCount }}</div>
                        <div class="kpi-label">Featured Pages</div>
                    </div>
                    <div class="kpi-icon" style="background: #fee6e6;">
                        <i class="mdi mdi-star" style="color: #ef4444; font-size: 24px;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FILTER BAR --}}
    <div class="filter-bar">
        <div class="row g-2 align-items-center">
            <div class="col-md-2">
                <select id="filterSiteType" class="form-select form-select-sm">
                    <option value="">All Site Types</option>
                    <option value="vshop">VShop</option>
                    <option value="vtravel">VTravel</option>
                    <option value="both">Both</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filterApprovalStatus" class="form-select form-select-sm">
                    <option value="">All Approval Status</option>
                    <option value="draft">Draft</option>
                    <option value="pending_review">Pending Review</option>
                    <option value="approved">Approved</option>
                    <option value="published">Published</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filterStatus" class="form-select form-select-sm">
                    <option value="">All Status</option>
                    <option value="1">Active</option>
                    <option value="0">Inactive</option>
                </select>
            </div>
            <div class="col-md-2">
                <select id="filterFeatured" class="form-select form-select-sm">
                    <option value="">All Pages</option>
                    <option value="1">Featured Only</option>
                    <option value="0">Non-Featured</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="Search by title or slug...">
                    <button id="resetFilters" class="btn btn-sm btn-secondary">Reset</button>
                </div>
            </div>
        </div>
    </div>

    {{-- DATA TABLE --}}
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="pagesTable">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Title</th>
                            <th>Slug</th>
                            <th>Site Type</th>
                            <th>Approval</th>
                            <th>Status</th>
                            <th width="60">Featured</th>
                            <th>Price</th>
                            <th width="140">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pages as $key => $p)
                            <tr>
                                <td class="page-id">{{ $loop->iteration }}</td>
                                <td>
                                    <strong>{{ $p->title }}</strong>
                                    @if($p->is_featured)
                                        <span class="badge bg-warning ms-1">Featured</span>
                                    @endif
                                    @if($p->is_sponsored)
                                        <span class="badge badge-info ms-1">Sponsored</span>
                                    @endif
                                </td>
                                <td><code class="text-muted">/{{ $p->slug }}</code></td>
                                <td>
                                    @if($p->site_type == 'vshop')
                                        <span class="badge badge-primary">VShop</span>
                                    @elseif($p->site_type == 'vtravel')
                                        <span class="badge badge-success">VTravel</span>
                                    @else
                                        <span class="badge badge-secondary">Both</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'draft' => 'secondary',
                                            'pending_review' => 'warning',
                                            'approved' => 'info',
                                            'published' => 'success',
                                            'rejected' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'draft' => 'Draft',
                                            'pending_review' => 'Pending',
                                            'approved' => 'Approved',
                                            'published' => 'Published',
                                            'rejected' => 'Rejected'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$p->approval_status] ?? 'secondary' }}">
                                        {{ $statusLabels[$p->approval_status] ?? ucfirst($p->approval_status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($p->status)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($p->is_featured)
                                        <i class="mdi mdi-star fs-5" style="color: #f59e0b;"></i>
                                    @else
                                        <i class="mdi mdi-star-outline fs-5 text-muted"></i>
                                    @endif
                                </td>
                                <td>
                                    @if($p->travel_price)
                                        <span class="fw-semibold" style="color: #10b981;">${{ number_format($p->travel_price, 2) }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-sm btn-info view-page" 
                                                data-id="{{ $p->id }}"
                                                data-title="{{ $p->title }}"
                                                data-slug="{{ $p->slug }}"
                                                data-content="{{ Str::limit(strip_tags($p->content), 200) }}"
                                                data-site-type="{{ $p->site_type }}"
                                                data-status="{{ $p->status }}"
                                                data-approval-status="{{ $p->approval_status }}"
                                                data-meta-title="{{ $p->meta_title }}"
                                                data-meta-description="{{ $p->meta_description }}"
                                                data-seo-keywords="{{ $p->seo_keywords }}"
                                                data-travel-price="{{ $p->travel_price }}"
                                                data-travel-duration="{{ $p->travel_duration }}"
                                                data-travel-start-date="{{ $p->travel_start_date }}"
                                                data-travel-end-date="{{ $p->travel_end_date }}"
                                                data-destinations="{{ $p->destinations }}"
                                                data-travel-inclusions="{{ $p->travel_inclusions }}"
                                                data-travel-exclusions="{{ $p->travel_exclusions }}"
                                                data-is-featured="{{ $p->is_featured }}"
                                                data-is-sponsored="{{ $p->is_sponsored }}"
                                                data-sponsored-until="{{ $p->sponsored_until }}"
                                                data-show-ads="{{ $p->show_ads }}"
                                                data-show-banners="{{ $p->show_banners }}"
                                                data-created-at="{{ $p->created_at }}"
                                                data-updated-at="{{ $p->updated_at }}"
                                                data-og-title="{{ $p->og_title }}"
                                                data-og-description="{{ $p->og_description }}"
                                                data-twitter-title="{{ $p->twitter_title }}"
                                                data-twitter-description="{{ $p->twitter_description }}"
                                                data-primary-cta-text="{{ $p->primary_cta_text }}"
                                                data-primary-cta-url="{{ $p->primary_cta_url }}"
                                                data-secondary-cta-text="{{ $p->secondary_cta_text }}"
                                                data-secondary-cta-url="{{ $p->secondary_cta_url }}"
                                                data-schedule-publish-at="{{ $p->schedule_publish_at }}"
                                                data-rejection-reason="{{ $p->rejection_reason }}"
                                                data-featured-order="{{ $p->featured_order }}"
                                                data-enable-ai-blocks="{{ $p->enable_ai_blocks }}"
                                                data-analytics-id="{{ $p->analytics_id }}"
                                                title="Quick View">
                                            <i class="mdi mdi-eye"></i>
                                        </button>
                                        <a href="{{ route('admin.pages.edit', $p->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="mdi mdi-pencil"></i>
                                        </a>
                                        <form action="{{ route('admin.pages.delete', $p->id) }}" method="POST" style="display:inline-block;" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this page?')" title="Delete">
                                                <i class="mdi mdi-delete"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5">
                                    <i class="mdi mdi-file-document-outline fs-1 d-block mb-2 text-muted"></i>
                                    <p class="text-muted mb-0">No pages found. Click "Add New Page" to create one.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Pagination - Only show if using paginator --}}
            @if(method_exists($pages, 'links') && $pages->hasPages())
                <div class="p-3 border-top">
                    {{ $pages->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

{{-- QUICK VIEW MODAL (same as before) --}}
<div class="modal fade" id="quickViewModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Page Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ul class="nav nav-tabs" id="viewTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#viewBasic">Basic Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#viewSEO">SEO & Meta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#viewTravel">Travel Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#viewCTA">CTA & Links</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#viewFeatures">Features</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#viewApproval">Approval</a>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <div class="tab-pane fade show active" id="viewBasic">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Page Information</h6>
                                    <p><span class="data-detail-label">Title:</span> <span id="viewTitle"></span></p>
                                    <p><span class="data-detail-label">Slug:</span> <span id="viewSlug"></span></p>
                                    <p><span class="data-detail-label">Site Type:</span> <span id="viewSiteType"></span></p>
                                    <p><span class="data-detail-label">Status:</span> <span id="viewStatus"></span></p>
                                    <p><span class="data-detail-label">Created At:</span> <span id="viewCreatedAt"></span></p>
                                    <p><span class="data-detail-label">Last Updated:</span> <span id="viewUpdatedAt"></span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Content Preview</h6>
                                    <div id="viewContent" class="p-2 bg-white rounded border" style="max-height: 300px; overflow-y: auto;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="viewSEO">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Meta Information</h6>
                                    <p><span class="data-detail-label">Meta Title:</span> <span id="viewMetaTitle"></span></p>
                                    <p><span class="data-detail-label">Meta Description:</span> <span id="viewMetaDescription"></span></p>
                                    <p><span class="data-detail-label">SEO Keywords:</span> <span id="viewSeoKeywords"></span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Open Graph</h6>
                                    <p><span class="data-detail-label">OG Title:</span> <span id="viewOgTitle"></span></p>
                                    <p><span class="data-detail-label">OG Description:</span> <span id="viewOgDescription"></span></p>
                                </div>
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Twitter Card</h6>
                                    <p><span class="data-detail-label">Twitter Title:</span> <span id="viewTwitterTitle"></span></p>
                                    <p><span class="data-detail-label">Twitter Description:</span> <span id="viewTwitterDescription"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="viewTravel">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Travel Package Details</h6>
                                    <p><span class="data-detail-label">Price:</span> $<span id="viewTravelPrice"></span></p>
                                    <p><span class="data-detail-label">Duration:</span> <span id="viewTravelDuration"></span></p>
                                    <p><span class="data-detail-label">Start Date:</span> <span id="viewTravelStartDate"></span></p>
                                    <p><span class="data-detail-label">End Date:</span> <span id="viewTravelEndDate"></span></p>
                                    <p><span class="data-detail-label">Destinations:</span> <span id="viewDestinations"></span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Inclusions & Exclusions</h6>
                                    <p><span class="data-detail-label">Inclusions:</span></p>
                                    <div id="viewInclusions" class="bg-white p-2 rounded border mb-2" style="white-space: pre-wrap; font-size: 13px;"></div>
                                    <p><span class="data-detail-label">Exclusions:</span></p>
                                    <div id="viewExclusions" class="bg-white p-2 rounded border" style="white-space: pre-wrap; font-size: 13px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="viewCTA">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Primary CTA</h6>
                                    <p><span class="data-detail-label">Text:</span> <span id="viewPrimaryCtaText"></span></p>
                                    <p><span class="data-detail-label">URL:</span> <span id="viewPrimaryCtaUrl"></span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Secondary CTA</h6>
                                    <p><span class="data-detail-label">Text:</span> <span id="viewSecondaryCtaText"></span></p>
                                    <p><span class="data-detail-label">URL:</span> <span id="viewSecondaryCtaUrl"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="viewFeatures">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Featured & Sponsored</h6>
                                    <p><span class="data-detail-label">Featured:</span> <span id="viewIsFeatured"></span></p>
                                    <p><span class="data-detail-label">Sponsored:</span> <span id="viewIsSponsored"></span></p>
                                    <p><span class="data-detail-label">Sponsored Until:</span> <span id="viewSponsoredUntil"></span></p>
                                    <p><span class="data-detail-label">Featured Order:</span> <span id="viewFeaturedOrder"></span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">AI & Analytics</h6>
                                    <p><span class="data-detail-label">Enable AI Blocks:</span> <span id="viewEnableAiBlocks"></span></p>
                                    <p><span class="data-detail-label">Analytics ID:</span> <span id="viewAnalyticsId"></span></p>
                                    <p><span class="data-detail-label">Show Ads:</span> <span id="viewShowAds"></span></p>
                                    <p><span class="data-detail-label">Show Banners:</span> <span id="viewShowBanners"></span></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="viewApproval">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Approval Information</h6>
                                    <p><span class="data-detail-label">Approval Status:</span> <span id="viewApprovalStatus"></span></p>
                                    <p><span class="data-detail-label">Scheduled Publish:</span> <span id="viewSchedulePublishAt"></span></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-section">
                                    <h6 class="mb-3 fw-semibold">Rejection Reason</h6>
                                    <div id="viewRejectionReason" class="bg-white p-2 rounded border" style="white-space: pre-wrap; font-size: 13px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="editPageLink" class="btn btn-primary">Edit Page</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Filter functionality
    function filterTable() {
        var siteType = $('#filterSiteType').val();
        var approvalStatus = $('#filterApprovalStatus').val();
        var status = $('#filterStatus').val();
        var featured = $('#filterFeatured').val();
        var searchTerm = $('#searchInput').val().toLowerCase();
        
        $('#pagesTable tbody tr').each(function() {
            var row = $(this);
            var rowSiteType = row.find('td:nth-child(4) .badge').text().trim();
            var rowApprovalStatus = row.find('td:nth-child(5) .badge').text().trim().toLowerCase();
            var approvalMap = {'draft':'draft','pending':'pending_review','approved':'approved','published':'published','rejected':'rejected'};
            var rowApprovalKey = '';
            if(rowApprovalStatus === 'draft') rowApprovalKey = 'draft';
            else if(rowApprovalStatus === 'pending') rowApprovalKey = 'pending_review';
            else if(rowApprovalStatus === 'approved') rowApprovalKey = 'approved';
            else if(rowApprovalStatus === 'published') rowApprovalKey = 'published';
            else if(rowApprovalStatus === 'rejected') rowApprovalKey = 'rejected';
            
            var rowStatus = row.find('td:nth-child(6) .badge').text().trim() === 'Active' ? '1' : '0';
            var rowFeatured = row.find('td:nth-child(7) i').hasClass('text-warning') ? '1' : '0';
            var rowTitle = row.find('td:nth-child(2)').text().toLowerCase();
            var rowSlug = row.find('td:nth-child(3)').text().toLowerCase();
            
            var show = true;
            if (siteType && !rowSiteType.toLowerCase().includes(siteType.toLowerCase())) show = false;
            if (approvalStatus && rowApprovalKey !== approvalStatus) show = false;
            if (status !== '' && rowStatus !== status) show = false;
            if (featured !== '' && rowFeatured !== featured) show = false;
            if (searchTerm && !rowTitle.includes(searchTerm) && !rowSlug.includes(searchTerm)) show = false;
            
            row.toggle(show);
        });
    }
    
    $('#filterSiteType, #filterApprovalStatus, #filterStatus, #filterFeatured, #searchInput').on('change keyup', function() {
        filterTable();
    });
    
    $('#resetFilters').click(function() {
        $('#filterSiteType').val('');
        $('#filterApprovalStatus').val('');
        $('#filterStatus').val('');
        $('#filterFeatured').val('');
        $('#searchInput').val('');
        filterTable();
    });
    
    // Quick View Modal
    $('.view-page').click(function() {
        var data = $(this).data();
        
        // Basic Info
        $('#viewTitle').text(data.title || '-');
        $('#viewSlug').text('/' + (data.slug || '-'));
        
        var siteTypeText = '';
        if (data.siteType == 'vshop') siteTypeText = '<span class="badge badge-primary">VShop</span>';
        else if (data.siteType == 'vtravel') siteTypeText = '<span class="badge badge-success">VTravel</span>';
        else siteTypeText = '<span class="badge badge-secondary">Both</span>';
        $('#viewSiteType').html(siteTypeText);
        
        var statusText = data.status ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>';
        $('#viewStatus').html(statusText);
        
        $('#viewCreatedAt').text(data.createdAt || '-');
        $('#viewUpdatedAt').text(data.updatedAt || '-');
        $('#viewContent').html(data.content || 'No content available');
        
        // SEO
        $('#viewMetaTitle').text(data.metaTitle || '-');
        $('#viewMetaDescription').text(data.metaDescription || '-');
        $('#viewSeoKeywords').text(data.seoKeywords || '-');
        $('#viewOgTitle').text(data.ogTitle || '-');
        $('#viewOgDescription').text(data.ogDescription || '-');
        $('#viewTwitterTitle').text(data.twitterTitle || '-');
        $('#viewTwitterDescription').text(data.twitterDescription || '-');
        
        // Travel
        $('#viewTravelPrice').text(data.travelPrice ? parseFloat(data.travelPrice).toFixed(2) : '-');
        $('#viewTravelDuration').text(data.travelDuration || '-');
        $('#viewTravelStartDate').text(data.travelStartDate || '-');
        $('#viewTravelEndDate').text(data.travelEndDate || '-');
        $('#viewDestinations').text(data.destinations || '-');
        $('#viewInclusions').html((data.travelInclusions || '-').replace(/\n/g, '<br>'));
        $('#viewExclusions').html((data.travelExclusions || '-').replace(/\n/g, '<br>'));
        
        // CTA
        $('#viewPrimaryCtaText').text(data.primaryCtaText || '-');
        $('#viewPrimaryCtaUrl').html(data.primaryCtaUrl ? '<a href="' + data.primaryCtaUrl + '" target="_blank" class="text-primary">' + data.primaryCtaUrl + '</a>' : '-');
        $('#viewSecondaryCtaText').text(data.secondaryCtaText || '-');
        $('#viewSecondaryCtaUrl').html(data.secondaryCtaUrl ? '<a href="' + data.secondaryCtaUrl + '" target="_blank" class="text-primary">' + data.secondaryCtaUrl + '</a>' : '-');
        
        // Features
        $('#viewIsFeatured').html(data.isFeatured ? '<span class="badge badge-warning">Yes</span>' : '<span class="badge badge-secondary">No</span>');
        $('#viewIsSponsored').html(data.isSponsored ? '<span class="badge badge-info">Yes</span>' : '<span class="badge badge-secondary">No</span>');
        $('#viewSponsoredUntil').text(data.sponsoredUntil || '-');
        $('#viewFeaturedOrder').text(data.featuredOrder || '-');
        $('#viewEnableAiBlocks').html(data.enableAiBlocks ? '<span class="badge badge-success">Enabled</span>' : '<span class="badge badge-secondary">Disabled</span>');
        $('#viewAnalyticsId').text(data.analyticsId || '-');
        $('#viewShowAds').html(data.showAds ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>');
        $('#viewShowBanners').html(data.showBanners ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-secondary">No</span>');
        
        // Approval
        var approvalStatusColors = {draft: 'secondary', pending_review: 'warning', approved: 'info', published: 'success', rejected: 'danger'};
        var approvalStatusLabels = {draft: 'Draft', pending_review: 'Pending Review', approved: 'Approved', published: 'Published', rejected: 'Rejected'};
        $('#viewApprovalStatus').html('<span class="badge bg-' + (approvalStatusColors[data.approvalStatus] || 'secondary') + '">' + (approvalStatusLabels[data.approvalStatus] || data.approvalStatus) + '</span>');
        $('#viewSchedulePublishAt').text(data.schedulePublishAt || '-');
        $('#viewRejectionReason').html((data.rejectionReason || '-').replace(/\n/g, '<br>'));
        
        // Edit link
        $('#editPageLink').attr('href', '{{ url("admin/pages/edit") }}/' + data.id);
        
        $('#quickViewModal').modal('show');
    });
    
    // Auto-dismiss alerts
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 3000);
});
</script>

@endsection
