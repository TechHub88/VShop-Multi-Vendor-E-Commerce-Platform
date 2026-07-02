@extends('layouts.vertical', ['title' => 'Verify Seller: ' . $seller->name])

@section('content')
<div class="container-fluid pt-3">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('admin.marketplace-control') }}">Marketplace</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Verify Seller</li>
                </ol>
            </nav>
            <h1 class="h3 mb-0 text-slate-800 fw-bold">Verify Seller: {{ $seller->name }}</h1>
            <p class="text-muted mb-0">Review, approve, or reject resources uploaded by this seller.</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-sm-0">
            <a href="{{ route('admin.marketplace-control') }}" class="btn btn-light border d-flex align-items-center gap-1">
                <iconify-icon icon="solar:arrow-left-outline" width="18"></iconify-icon> Back to List
            </a>
        </div>
    </div>

    <!-- Seller Info Card -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden bg-white text-slate-800 border-start border-primary border-4">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-auto mb-3 mb-md-0">
                    <div class="avatar-lg bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold fs-1" style="width: 72px; height: 72px;">
                        {{ strtoupper(substr($seller->name, 0, 2)) }}
                    </div>
                </div>
                <div class="col-md">
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <h4 class="mb-0 fw-bold text-slate-900">{{ $seller->name }}</h4>
                        <span class="badge bg-primary-light text-primary text-uppercase" style="font-size:10px;">{{ $seller->business_type ?? 'Generic Business' }}</span>
                        @php
                            $statusClass = 'warning';
                            if ($seller->status === 'approved') $statusClass = 'success';
                            elseif (in_array($seller->status, ['rejected', 'blocked'])) $statusClass = 'danger';
                        @endphp
                        <span class="badge bg-{{ $statusClass }}-light text-{{ $statusClass }} text-uppercase" style="font-size:10px;">{{ ucfirst($seller->status ?? 'pending') }}</span>
                    </div>
                    <div class="row mt-3">
                        <div class="col-sm-3 mb-2"><small class="text-muted d-block fw-semibold">EMAIL</small><span class="fw-semibold text-slate-700">{{ $seller->email ?? 'N/A' }}</span></div>
                        <div class="col-sm-3 mb-2"><small class="text-muted d-block fw-semibold">PHONE</small><span class="fw-semibold text-slate-700">{{ $seller->phone ?? 'N/A' }}</span></div>
                        <div class="col-sm-3 mb-2"><small class="text-muted d-block fw-semibold">MEMBER SINCE</small><span class="fw-semibold text-slate-700">{{ $seller->created_at ? $seller->created_at->format('Y-m-d') : 'N/A' }}</span></div>
                        <div class="col-sm-3"><small class="text-muted d-block fw-semibold">TOTAL PRODUCTS</small><span class="fw-bold fs-5 text-primary">{{ $totalProducts }}</span></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Action Alert -->
    <div id="actionAlert" class="alert alert-dismissible fade show d-none mb-4" role="alert">
        <span class="alert-message"></span>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>

    <!-- Scrollable Tabs -->
    <div class="tabs-wrapper mb-4">
        <div class="scroll-btn scroll-left" onclick="scrollTabs('left')" style="display:none;">
            <iconify-icon icon="solar:arrow-left-outline" width="18"></iconify-icon>
        </div>
        <ul class="nav nav-tabs nav-tabs-scroll border-0" id="verifyTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="categories-tab" data-bs-toggle="tab" data-bs-target="#categories" type="button">
                    <iconify-icon icon="solar:tag-bold-duotone" class="me-1 align-middle"></iconify-icon> Categories
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="banners-tab" data-bs-toggle="tab" data-bs-target="#banners" type="button">
                    <iconify-icon icon="solar:gallery-bold-duotone" class="me-1 align-middle"></iconify-icon> Banners
                </button>
            </li>
          
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="templates-tab" data-bs-toggle="tab" data-bs-target="#templates" type="button">
                    <iconify-icon icon="solar:palette-bold-duotone" class="me-1 align-middle"></iconify-icon> Templates
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="seo-tab" data-bs-toggle="tab" data-bs-target="#seo" type="button">
                    <iconify-icon icon="solar:chart-2-bold-duotone" class="me-1 align-middle"></iconify-icon> SEO Monitoring
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="operation-tab" data-bs-toggle="tab" data-bs-target="#operation" type="button">
                    <iconify-icon icon="solar:map-point-bold-duotone" class="me-1 align-middle"></iconify-icon> Operation Areas
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pages-tab" data-bs-toggle="tab" data-bs-target="#pages" type="button">
                    <iconify-icon icon="solar:document-bold-duotone" class="me-1 align-middle"></iconify-icon> Dynamic Pages
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="campaigns-tab" data-bs-toggle="tab" data-bs-target="#campaigns" type="button">
                    <iconify-icon icon="solar:megaphone-bold-duotone" class="me-1 align-middle"></iconify-icon> Campaigns
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="ads-tab" data-bs-toggle="tab" data-bs-target="#ads" type="button">
                    <iconify-icon icon="solar:tv-bold-duotone" class="me-1 align-middle"></iconify-icon> Advertisements
                </button>
            </li>
        </ul>
        <div class="scroll-btn scroll-right" onclick="scrollTabs('right')">
            <iconify-icon icon="solar:arrow-right-outline" width="18"></iconify-icon>
        </div>
    </div>

    <!-- Tab Contents -->
    <div class="tab-content" id="verifyTabContent">

        <!-- ===== 1. CATEGORIES ===== -->
        <div class="tab-pane fade show active" id="categories" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-slate-800">Category Management</h5>
                        <small class="text-muted">Create/Edit/Deactivate categories and subcategories. Mapping to apps (vShop, vTravel).</small>
                    </div>
                    <span class="badge bg-primary">{{ $categories->count() }} categories</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted" style="width:50px;">ID</th>
                                    <th class="py-3 text-muted">Category Name</th>
                                    <th class="py-3 text-muted">App Mapping</th>
                                    <th class="py-3 text-muted">Subcategories</th>
                                    <th class="py-3 text-muted text-center">Status</th>
                                    <th class="py-3 text-muted">Rejection Reason</th>
                                    <th class="px-4 py-3 text-end text-muted">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr id="category-row-{{ $category->id }}">
                                    <td class="px-4 text-muted fw-semibold">#{{ $category->id }}</td>
                                    <td><strong>{{ $category->name }}</strong></td>
                                    <td>
                                        @php $appMapping = json_decode($category->app_mapping ?? '[]', true); @endphp
                                        @if(is_array($appMapping) && count($appMapping) > 0)
                                            @foreach($appMapping as $app)<span class="badge bg-primary-light text-primary me-1">{{ $app }}</span>@endforeach
                                        @else
                                            <span class="text-muted">Not Assigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @foreach($category->subcategories as $sub)
                                            <span class="badge bg-secondary me-1">{{ $sub->name }}</span>
                                        @endforeach
                                        @if($category->subcategories->count() == 0)<span class="text-muted small">None</span>@endif
                                    </td>
                                    <td class="text-center" id="category-status-cell-{{ $category->id }}">
                                        @if($category->status == 1 || $category->status === 'active')
                                            <span class="badge bg-success-light text-success px-2 py-1 rounded text-uppercase" style="font-size:10px;">Active</span>
                                        @elseif($category->status === 0 || $category->status === 'inactive')
                                            <span class="badge bg-danger-light text-danger px-2 py-1 rounded text-uppercase" style="font-size:10px;">Rejected</span>
                                        @else
                                            <span class="badge bg-warning-light text-warning px-2 py-1 rounded text-uppercase" style="font-size:10px;">Pending</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small" id="category-reason-cell-{{ $category->id }}">
                                        {{ $category->rejection_reason ?? '—' }}
                                    </td>
                                    <td class="px-4 text-end">
                                        <button class="btn btn-sm btn-primary btn-approve me-1" data-entity="category" data-id="{{ $category->id }}">
                                            <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>Approve
                                        </button>
                                        <button class="btn btn-sm btn-outline-primary btn-reject" data-entity="category" data-id="{{ $category->id }}">
                                            <iconify-icon icon="solar:close-circle-bold" class="align-middle me-1"></iconify-icon>Reject
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted">No categories created by this seller.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

 <div class="tab-pane fade" id="banners" role="tabpanel">
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-slate-800">Banner Moderation</h5>
                <small class="text-muted">Approve or reject banners. Add remarks on rejection.</small>
            </div>
            <span class="badge bg-primary">{{ $banners->count() }} Total Banners</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted">Banner Image</th>
                            <th class="py-3 text-muted">Title</th>
                            <th class="py-3 text-muted">Sub Title</th>
                            <th class="py-3 text-muted text-center">Status</th>
                            <th class="py-3 text-muted">Admin Remark</th>
                            <th class="px-4 py-3 text-end text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                        <tr id="banner-row-{{ $banner->id }}">
                            <td class="px-4">
                                @if($banner->banner_image)
                                    @php
                                        // Initialize image path
                                        $imagePath = '';
                                        
                                        // Handle different types of banner_image
                                        if (is_string($banner->banner_image)) {
                                            // Check if it's a JSON string
                                            if (str_starts_with($banner->banner_image, '[')) {
                                                $images = json_decode($banner->banner_image, true);
                                                $imagePath = is_array($images) && !empty($images) ? $images[0] : '';
                                            } else {
                                                $imagePath = $banner->banner_image;
                                            }
                                        } elseif (is_array($banner->banner_image)) {
                                            // If it's already an array
                                            $imagePath = !empty($banner->banner_image) ? $banner->banner_image[0] : '';
                                        }
                                        
                                        // Only process if we have a valid string path
                                        if (!empty($imagePath) && is_string($imagePath)) {
                                            // Clean the path - remove escaped slashes
                                            $imagePath = str_replace(['\\', '\/'], '/', $imagePath);
                                            // Remove any leading slashes
                                            $imagePath = ltrim($imagePath, '/');
                                        } else {
                                            $imagePath = '';
                                        }
                                    @endphp
                                    
                                    @if(!empty($imagePath))
                                        <img src="{{ asset($imagePath) }}" 
                                             class="rounded shadow-sm" 
                                             style="max-height:70px;max-width:130px;object-fit:cover;cursor:pointer;" 
                                             alt="Banner"
                                             onclick="window.open('{{ asset($imagePath) }}', '_blank')"
                                             onerror="this.src='https://via.placeholder.com/130x70?text=Image+Not+Found'">
                                    @else
                                        <span class="text-muted">Invalid Image Path</span>
                                    @endif
                                @else
                                    <span class="text-muted">No Image</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-bold">{{ $banner->banner_title ?? 'Untitled Banner' }}</div>
                            </td>
                            <td>
                                <small class="text-muted">{{ $banner->banner_sub_title ?? '—' }}</small>
                            </td>
                            <td class="text-center" id="banner-status-cell-{{ $banner->id }}">
                                @php 
                                    $bStatus = $banner->status ?? 'pending'; 
                                    $bClass = $bStatus === 'approved' ? 'success' : ($bStatus === 'rejected' ? 'danger' : 'warning'); 
                                @endphp
                                <span class="badge bg-{{ $bClass }}-light text-{{ $bClass }} px-2 py-1 rounded text-uppercase" style="font-size:10px;">{{ ucfirst($bStatus) }}</span>
                            </td>
                            <td class="text-muted small" id="banner-reason-cell-{{ $banner->id }}">
                                {{ $banner->admin_remark ?? '—' }}
                            </td>
                            <td class="px-4 text-end">
                                <button class="btn btn-sm btn-success btn-approve me-1" data-entity="banner" data-id="{{ $banner->id }}">
                                    <iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>Approve
                                </button>
                                <button class="btn btn-sm btn-danger btn-reject" data-entity="banner" data-id="{{ $banner->id }}">
                                    <iconify-icon icon="solar:close-circle-bold" class="align-middle me-1"></iconify-icon>Reject
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="text-center py-5 text-muted">No banners uploaded by this seller.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

       
        <!-- ===== 4. WEBSITE TEMPLATES ===== -->
        <div class="tab-pane fade" id="templates" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-slate-800">Website Template Control</h5>
                    <small class="text-muted">Upload/manage templates, assign to sellers, preview control.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted">Template ID</th>
                                    <th class="py-3 text-muted">Template Name</th>
                                    <th class="py-3 text-muted">App Type</th>
                                    <th class="py-3 text-muted">Created Date</th>
                                    <th class="py-3 text-muted text-center">Status</th>
                                  
                                
                                </tr>
                            </thead>
                            <tbody>
                              @forelse($assignedTemplates as $assigned)
<tr id="template-row-{{ $assigned->id }}">
    <td class="px-4">#{{ $assigned->template->id ?? 'N/A' }}</td>
    <td><strong>{{ $assigned->template->name ?? 'N/A' }}</strong></td>
    <td><span class="badge bg-primary">{{ ucfirst($assigned->template->app_type ?? 'N/A') }}</span></td>
    <td>{{ ($assigned->template && $assigned->template->created_at) ? $assigned->template->created_at->format('Y-m-d') : 'N/A' }}</td>
    <td class="text-center" id="template-status-cell-{{ $assigned->id }}">
        @php $tStatus = $assigned->status ?? 'pending'; $tClass = $tStatus === 'approved' ? 'success' : ($tStatus === 'rejected' ? 'danger' : 'warning'); @endphp
        <span class="badge bg-{{ $tClass }}-light text-{{ $tClass }} px-2 py-1 rounded text-uppercase" style="font-size:10px;">{{ $tStatus }}</span>
    </td>
  
   
</tr>
@empty
<tr><td colspan="7" class="text-center py-5 text-muted">No website templates currently assigned to this seller.</td></tr>
@endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== 5. SEO MONITORING ===== -->
        <div class="tab-pane fade" id="seo" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-slate-800">SEO Monitoring</h5>
                    <small class="text-muted">Review seller SEO inputs, suggest corrections, block spam keywords.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted">SEO Title</th>
                                    <th class="py-3 text-muted">Meta Description</th>
                                    <th class="py-3 text-muted">Meta Keywords</th>
                                    <th class="py-3 text-muted text-center">Status</th>
                                 
                                  
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sellerProducts as $seo)
                                <tr id="seo-row-{{ $seo->id }}">
                                    <td class="px-4 fw-bold text-slate-800">{{ $seo->home_title ?? 'Untitled Title' }}</td>
                                    <td>{{ Str::limit($seo->meta_description ?? 'No description', 70) }}</td>
                                    <td>
                                        @foreach(explode(',', $seo->meta_keywords ?? '') as $kw)
                                            @if(trim($kw))<span class="badge bg-light text-dark border me-1">{{ trim($kw) }}</span>@endif
                                        @endforeach
                                    </td>
                                    <td class="text-center" id="seo-status-cell-{{ $seo->id }}">
                                        @php $sStatus = $seo->status ?? 'pending'; $sClass = $sStatus === 'approved' ? 'success' : ($sStatus === 'rejected' ? 'danger' : 'warning'); @endphp
                                        <span class="badge bg-{{ $sClass }}-light text-{{ $sClass }} px-2 py-1 rounded text-uppercase" style="font-size:10px;">{{ $sStatus }}</span>
                                    </td>
                                    <td class="text-muted small" id="seo-reason-cell-{{ $seo->id }}">{{ $seo->rejection_reason ?? '—' }}</td>
                                   
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">No SEO configurations found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== 6. OPERATION AREAS ===== -->
        <div class="tab-pane fade" id="operation" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h5 class="mb-0 fw-bold text-slate-800">Area of Operation Validation</h5>
                    <small class="text-muted">Country/State/District logic validation. Prevent invalid coverage claims.</small>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted">Country</th>
                                    <th class="py-3 text-muted">State</th>
                                    <th class="py-3 text-muted">City</th>
                                    <th class="py-3 text-muted text-center">Status</th>
                                 
                                  
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($operationAreas as $area)
                                <tr id="area-row-{{ $area->id }}">
                                    <td class="px-4">{{ $area->country->name ?? 'N/A' }}</td>
                                    <td>{{ $area->state->name ?? 'N/A' }}</td>
                                    <td>{{ $area->city->name ?? 'N/A' }}</td>
                                    <td class="text-center" id="area-status-cell-{{ $area->id }}">
                                        @if($area->is_validated == 1)
                                            <span class="badge bg-success-light text-success px-2 py-1 rounded text-uppercase" style="font-size:10px;">Validated</span>
                                        @else
                                            <span class="badge bg-warning-light text-warning px-2 py-1 rounded text-uppercase" style="font-size:10px;">Pending</span>
                                        @endif
                                    </td>
                                  
                                  
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center py-5 text-muted">No operation areas added by this seller.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== 7. DYNAMIC PAGES ===== -->
        <div class="tab-pane fade" id="pages" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-slate-800">Dynamic Page Management</h5>
                        <small class="text-muted">Page Approval Workflow: Draft → Pending Review → Approved → Published / Rejected.</small>
                    </div>
                    <span class="badge bg-primary">{{ $dynamicPages->count() }} pages</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted">ID</th>
                                    <th class="py-3 text-muted">Page Title</th>
                                    <th class="py-3 text-muted">Slug / URL</th>
                                    <th class="py-3 text-muted">App Type</th>
                                    <th class="py-3 text-muted text-center">Status</th>
                                  
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dynamicPages as $page)
                                <tr id="page-row-{{ $page->id }}">
                                    <td class="px-4 text-muted fw-semibold">#{{ $page->id }}</td>
                                    <td><strong>{{ $page->title ?? 'Untitled Page' }}</strong></td>
                                    <td><code class="text-muted small">/{{ $page->slug ?? '—' }}</code></td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($page->app_type ?? 'all') }}</span></td>
                                    <td class="text-center" id="page-status-cell-{{ $page->id }}">
                                        @php
                                            $pgStatus = $page->status ?? 'draft';
                                            $pgClass  = match($pgStatus) { 'approved','published' => 'success', 'rejected' => 'danger', 'pending' => 'warning', default => 'secondary' };
                                        @endphp
                                        <span class="badge bg-{{ $pgClass }}-light text-{{ $pgClass }} px-2 py-1 rounded text-uppercase" style="font-size:10px;">{{ $pgStatus }}</span>
                                    </td>
                                  
                                   
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted">No dynamic pages found for this seller.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== 8. CAMPAIGNS ===== -->
        <div class="tab-pane fade" id="campaigns" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-slate-800">Advertisement, Campaign &amp; Promotion Management</h5>
                        <small class="text-muted">Promotion campaigns: approve/reject/end campaigns with reason.</small>
                    </div>
                    <span class="badge bg-primary">{{ $campaigns->count() }} campaigns</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted">ID</th>
                                    <th class="py-3 text-muted">Campaign Name</th>
                                    <th class="py-3 text-muted">Discount</th>
                                    <th class="py-3 text-muted">Start Date</th>
                                    <th class="py-3 text-muted">End Date</th>
                                    <th class="py-3 text-muted text-center">Status</th>
                                 
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                <tr id="campaign-row-{{ $campaign->id }}">
                                    <td class="px-4 text-muted fw-semibold">#{{ $campaign->id }}</td>
                                    <td><strong>{{ $campaign->name ?? 'Unnamed Campaign' }}</strong><br><small class="text-muted">{{ Str::limit($campaign->description ?? '', 40) }}</small></td>
                                    <td><span class="badge bg-success-light text-success">{{ $campaign->discount ?? 0 }}% OFF</span></td>
                                    <td class="small text-muted">{{ $campaign->start_date ? \Carbon\Carbon::parse($campaign->start_date)->format('d M Y') : '—' }}</td>
                                    <td class="small text-muted">{{ $campaign->end_date ? \Carbon\Carbon::parse($campaign->end_date)->format('d M Y') : '—' }}</td>
                                    <td class="text-center" id="campaign-status-cell-{{ $campaign->id }}">
                                        @php $cStatus = $campaign->status ?? 'pending'; $cClass = $cStatus === 'active' ? 'success' : ($cStatus === 'inactive' ? 'danger' : 'warning'); @endphp
                                        <span class="badge bg-{{ $cClass }}-light text-{{ $cClass }} px-2 py-1 rounded text-uppercase" style="font-size:10px;">{{ $cStatus }}</span>
                                    </td>
                                  
                                   
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center py-5 text-muted">No campaigns found for this seller.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ===== 9. ADVERTISEMENTS ===== -->
        <div class="tab-pane fade" id="ads" role="tabpanel">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0 fw-bold text-slate-800">Advertisement Banners</h5>
                        <small class="text-muted">Approve/reject advertisement banners submitted by this seller.</small>
                    </div>
                    <span class="badge bg-primary">{{ $advertisements->count() }} ads</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted">Ad Image</th>
                                    <th class="py-3 text-muted">Title</th>
                                    <th class="py-3 text-muted">Position</th>
                                    <th class="py-3 text-muted">Link</th>
                                    <th class="py-3 text-muted text-center">Status</th>
                                  
                                  
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($advertisements as $ad)
                                <tr id="ad-row-{{ $ad->id }}">
                                    <td class="px-4">
                                        @if(!empty($ad->image))
                                            <img src="{{ asset('storage/' . $ad->image) }}" class="rounded shadow-sm" style="max-height:60px;max-width:110px;object-fit:cover;">
                                        @else
                                            <span class="text-muted small">No Image</span>
                                        @endif
                                    </td>
                                    <td><strong>{{ $ad->title ?? 'Untitled Ad' }}</strong></td>
                                    <td><span class="badge bg-light text-dark border">{{ ucfirst($ad->position ?? '—') }}</span></td>
                                    <td><a href="{{ $ad->link ?? '#' }}" class="text-primary small" target="_blank">{{ Str::limit($ad->link ?? '—', 30) }}</a></td>
                                    <td class="text-center" id="ad-status-cell-{{ $ad->id }}">
                                        @php $adStatus = $ad->status ?? 'pending'; $adClass = $adStatus === 'active' ? 'success' : ($adStatus === 'inactive' ? 'danger' : 'warning'); @endphp
                                        <span class="badge bg-{{ $adClass }}-light text-{{ $adClass }} px-2 py-1 rounded text-uppercase" style="font-size:10px;">{{ $adStatus }}</span>
                                    </td>
                                  
                                  
                                </tr>
                                @empty
                                <tr><td colspan="7" class="text-center py-5 text-muted">No advertisements found for this seller.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end tab-content -->
</div><!-- end container -->

<!-- =============================================
     REJECTION REASON MODAL (Shared for all tabs)
     ============================================= -->
<div class="modal fade" id="rejectReasonModal" tabindex="-1" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header py-3" style="background:linear-gradient(135deg,#0d6efd,#004392);">
                <h6 class="modal-title text-white fw-bold" id="rejectReasonModalLabel">
                    <iconify-icon icon="solar:close-circle-bold" class="align-middle me-2"></iconify-icon>
                    Reject — Enter Reason
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning py-2 fs-8 mb-3">
                    <iconify-icon icon="solar:danger-triangle-bold-duotone" class="align-middle me-1"></iconify-icon>
                    This reason will be saved and shown to the seller. Please be specific and professional.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Rejection Reason <span class="text-danger">*</span></label>
                    <textarea id="rejectionReasonText" class="form-control" rows="4" placeholder="e.g. Image quality is too low, please re-upload with minimum 1200×600px resolution..."></textarea>
                    <div class="invalid-feedback" id="reasonError">Please enter a rejection reason.</div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-semibold small text-muted">Quick Reasons</label>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="Low quality image/content">Low quality</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="Violates marketplace policy">Policy violation</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="Incomplete information provided">Incomplete info</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="Duplicate entry already exists">Duplicate</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-reason" data-reason="Spam or misleading content detected">Spam/Misleading</button>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 bg-light">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary btn-sm px-4 fw-semibold" id="confirmRejectBtn">
                    <iconify-icon icon="solar:close-circle-bold" class="align-middle me-1"></iconify-icon>Confirm Rejection
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Premium CSS -->
<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap');
    body, .card, .table, .modal { font-family: 'Outfit', sans-serif !important; }

    .bg-warning-light  { background-color: rgba(245,158,11,0.1) !important; }
    .bg-success-light  { background-color: rgba(16,185,129,0.1) !important; }
    .bg-danger-light   { background-color: rgba(239,68,68,0.1) !important; }
    .bg-primary-light  { background-color: rgba(13,110,253,0.1) !important; }
    .bg-secondary-light{ background-color: rgba(100,116,139,0.1) !important; }

    .bg-success-light.text-success { color: #10b981 !important; }
    .bg-warning-light.text-warning { color: #f59e0b !important; }
    .bg-danger-light.text-danger   { color: #ef4444 !important; }
    .bg-primary-light.text-primary { color: #0d6efd !important; }

    .text-slate-800 { color: #1e293b; }

    /* Tab bar */
    .tabs-wrapper { position: relative; }
    .nav-tabs-scroll {
        display: flex; flex-wrap: nowrap; overflow-x: auto; overflow-y: hidden;
        white-space: nowrap; -webkit-overflow-scrolling: touch;
        scrollbar-width: none; gap: 0.25rem;
        border-bottom: 2px solid #e2e8f0;
    }
    .nav-tabs-scroll::-webkit-scrollbar { display: none; }
    .nav-tabs-scroll .nav-item { display: inline-block; }
    .nav-tabs-scroll .nav-link {
        white-space: nowrap; border-radius: 8px 8px 0 0;
        padding: 0.65rem 1.1rem; color: #64748b;
        font-weight: 500; border: none; background: transparent; transition: all 0.2s;
    }
    .nav-tabs-scroll .nav-link:hover { color: #0f172a; background-color: #f1f5f9; }
    .nav-tabs-scroll .nav-link.active {
        color: #0d6efd; border-bottom: 3px solid #0d6efd;
        font-weight: 600; background: transparent;
    }
    .scroll-btn {
        position: absolute; top: 50%; transform: translateY(-50%);
        width: 32px; height: 32px; border-radius: 50%; background: white;
        border: 1px solid #e2e8f0; box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        cursor: pointer; display: flex; align-items: center; justify-content: center;
        z-index: 10; transition: background-color 0.2s;
    }
    .scroll-btn:hover { background-color: #f8fafc; }
    .scroll-left { left: -15px; }
    .scroll-right { right: -15px; }
    .table tbody tr { transition: background-color 0.15s ease-in-out; }
    .table tbody tr:hover { background-color: rgba(248,250,252,0.8) !important; }
    .btn-approve:hover { transform: scale(1.02); }
    .btn-reject:hover  { transform: scale(1.02); }
    .quick-reason:hover { background: #dc3545; color:#fff; border-color:#dc3545; }
</style>

<script>
// ─── Tab Scroll ───────────────────────────────────────────────
function scrollTabs(direction) {
    const container = document.querySelector('.nav-tabs-scroll');
    container.scrollLeft += direction === 'left' ? -200 : 200;
    updateScrollButtons();
}
function updateScrollButtons() {
    const container = document.querySelector('.nav-tabs-scroll');
    const leftBtn   = document.querySelector('.scroll-left');
    const rightBtn  = document.querySelector('.scroll-right');
    if (!container) return;
    leftBtn.style.display  = container.scrollLeft <= 5 ? 'none' : 'flex';
    rightBtn.style.display = (container.scrollLeft + container.clientWidth >= container.scrollWidth - 5) ? 'none' : 'flex';
}

// ─── Quick Reason Chips ───────────────────────────────────────
document.querySelectorAll('.quick-reason').forEach(btn => {
    btn.addEventListener('click', () => {
        const ta = document.getElementById('rejectionReasonText');
        ta.value = (ta.value ? ta.value + ' ' : '') + btn.dataset.reason;
        ta.classList.remove('is-invalid');
    });
});

// ─── Reject Modal State ───────────────────────────────────────
let pendingRejectEntity = null;
let pendingRejectId     = null;

// ─── Approve + Reject Button Handlers ─────────────────────────
$(document).ready(function () {
    setTimeout(updateScrollButtons, 150);
    $('.nav-tabs-scroll').on('scroll', updateScrollButtons);
    $(window).on('resize', updateScrollButtons);

    // Save/restore active tab
    $('#verifyTab button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
        let tabId = $(e.target).attr('id');
        if (tabId) localStorage.setItem('verifyActiveTab', tabId);
    });
    let savedTab = localStorage.getItem('verifyActiveTab');
    if (savedTab) {
        let tabEl = document.getElementById(savedTab);
        if (tabEl && window.bootstrap) new bootstrap.Tab(tabEl).show();
    }

    // CSRF header setup
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

    // ── APPROVE ──────────────────────────────────────────────
    $(document).on('click', '.btn-approve', function () {
        const btn    = $(this);
        const entity = btn.data('entity');
        const id     = btn.data('id');
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Approving...');

        $.ajax({
            url: `/admin/marketplace/approve/${entity}/${id}`,
            type: 'POST',
            success: function (res) {
                btn.prop('disabled', false).html('<iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>Approve');
                if (res.success) {
                    showPageAlert(res.message || 'Approved successfully.', 'success');
                    updateStatusUI(entity, id, 'approve', '');
                } else {
                    showPageAlert(res.message || 'An error occurred.', 'danger');
                }
            },
            error: function () {
                btn.prop('disabled', false).html('<iconify-icon icon="solar:check-circle-bold" class="align-middle me-1"></iconify-icon>Approve');
                showPageAlert('Request failed. Please try again.', 'danger');
            }
        });
    });

    // ── REJECT — open modal ───────────────────────────────────
    $(document).on('click', '.btn-reject', function () {
        pendingRejectEntity = $(this).data('entity');
        pendingRejectId     = $(this).data('id');
        document.getElementById('rejectionReasonText').value = '';
        document.getElementById('rejectionReasonText').classList.remove('is-invalid');
        new bootstrap.Modal(document.getElementById('rejectReasonModal')).show();
    });

    // ── CONFIRM REJECT (from modal) ───────────────────────────
    $('#confirmRejectBtn').on('click', function () {
        const reason = $('#rejectionReasonText').val().trim();
        if (!reason) {
            $('#rejectionReasonText').addClass('is-invalid');
            return;
        }
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Rejecting...');

        $.ajax({
            url: `/admin/marketplace/reject/${pendingRejectEntity}/${pendingRejectId}`,
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ reason: reason }),
            success: function (res) {
                btn.prop('disabled', false).html('<iconify-icon icon="solar:close-circle-bold" class="align-middle me-1"></iconify-icon>Confirm Rejection');
                bootstrap.Modal.getInstance(document.getElementById('rejectReasonModal')).hide();
                if (res.success) {
                    showPageAlert(res.message || 'Rejected successfully.', 'danger');
                    updateStatusUI(pendingRejectEntity, pendingRejectId, 'reject', reason);
                } else {
                    showPageAlert(res.message || 'An error occurred.', 'danger');
                }
            },
            error: function () {
                btn.prop('disabled', false).html('<iconify-icon icon="solar:close-circle-bold" class="align-middle me-1"></iconify-icon>Confirm Rejection');
                showPageAlert('Request failed. Please try again.', 'danger');
            }
        });
    });
});

// ─── Page Alert Banner ────────────────────────────────────────
function showPageAlert(msg, type) {
    const al = $('#actionAlert');
    al.removeClass('d-none alert-success alert-danger alert-warning')
      .addClass('d-block alert-' + (type || 'success'));
    al.find('.alert-message').text(msg);
    $('html, body').animate({ scrollTop: 0 }, 'fast');
    setTimeout(() => al.addClass('d-none').removeClass('d-block'), 5000);
}

// ─── Dynamic Status Cell Update ───────────────────────────────
function updateStatusUI(entity, id, action, reason) {
    const isApproved = action === 'approve';

    // Map entity → status cell ID prefix
    const prefix = entity === 'product-boost' ? 'product-boost' : entity;
    const cell   = $(`#${prefix}-status-cell-${id}`);
    const reasonCell = $(`#${prefix}-reason-cell-${id}`);

    let approveHtml, rejectHtml;

    switch(entity) {
        case 'category':
            approveHtml = badge('success', 'Active');
            rejectHtml  = badge('danger', 'Rejected');
            break;
        case 'banner':
        case 'product':
        case 'template':
        case 'seo':
        case 'page':
            approveHtml = badge('success', 'APPROVED');
            rejectHtml  = badge('danger',  'REJECTED');
            break;
        case 'product-boost':
            approveHtml = badge('success', 'Boost Active');
            rejectHtml  = '<span class="badge bg-light text-dark border px-2 py-1 rounded" style="font-size:10px;">Regular</span>';
            break;
        case 'area':
            approveHtml = badge('success', 'Validated');
            rejectHtml  = badge('warning', 'Pending Validation');
            break;
        case 'campaign':
            approveHtml = badge('success', 'ACTIVE');
            rejectHtml  = badge('danger',  'INACTIVE');
            break;
        case 'ad':
            approveHtml = badge('success', 'ACTIVE');
            rejectHtml  = badge('danger',  'INACTIVE');
            break;
        default:
            approveHtml = badge('success', 'APPROVED');
            rejectHtml  = badge('danger',  'REJECTED');
    }

    cell.html(isApproved ? approveHtml : rejectHtml);

    if (reasonCell.length) {
        reasonCell.text(isApproved ? '—' : (reason || '—'));
    }
}

function badge(type, text) {
    return `<span class="badge bg-${type}-light text-${type} px-2 py-1 rounded text-uppercase" style="font-size:10px;">${text}</span>`;
}
</script>
@endsection
