{{-- resources/views/admin/approval/verify-product.blade.php --}}
@extends('layouts.vertical', ['title' => 'Product Verification'])

@section('content')

<style>
    .info-row {
        display: flex;
        padding: 8px 0;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
    }
    .info-label {
        width: 160px;
        font-weight: 600;
        color: #666;
    }
    .info-value {
        flex: 1;
        color: #333;
    }
    .product-image {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        margin: 5px;
        border: 1px solid #eee;
    }
    .badge-status {
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
    }
    .activity-log-item {
        padding: 12px;
        border-left: 3px solid;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-radius: 6px;
        transition: all 0.2s ease;
    }
    .activity-log-item:hover {
        background: #f1f5f9;
        transform: translateX(2px);
    }
    .activity-log-item.price { border-left-color: #ffc107; }
    .activity-log-item.description { border-left-color: #17a2b8; }
    .activity-log-item.status { border-left-color: #28a745; }
    .activity-log-item.rejected { border-left-color: #dc3545; }
    .small-card {
        border-radius: 8px;
        padding: 12px;
        margin-bottom: 12px;
        background: #fff;
        border: 1px solid #e9ecef;
    }
    .top-actions-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 12px 15px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    .nav-pills .nav-link {
        font-size: 12px;
        padding: 6px 10px;
        color: #475569;
        font-weight: 500;
    }
    .nav-pills .nav-link.active {
        background-color: #4f46e5;
        color: white;
    }
    .version-diff {
        background: #f8fafc;
        border-radius: 6px;
        padding: 8px;
        font-family: monospace;
        font-size: 12px;
    }
    .diff-added {
        background-color: #dcfce7;
        color: #166534;
    }
    .diff-removed {
        background-color: #fee2e2;
        color: #991b1b;
        text-decoration: line-through;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="pt-2 pb-2 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 border-bottom pb-2">
        <div>
            <h5 class="fw-bold m-0 text-dark">Product / Service Verification</h5>
            <p class="text-muted mb-0 small">Review & moderate product details</p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <button type="button" class="btn btn-danger rounded-3 px-3 btn-sm" onclick="submitVerification('rejected')">
                <i class="fa-solid fa-circle-xmark me-1"></i> Reject
            </button>
            <button type="button" class="btn btn-warning rounded-3 px-3 text-white btn-sm" onclick="submitVerification('not-verified')">
                <i class="fa-solid fa-clock me-1"></i> Not Verified
            </button>
            <button type="button" class="btn btn-success rounded-3 px-3 btn-sm" onclick="submitVerification('verified')">
                <i class="fa-solid fa-circle-check me-1"></i> Approve & Verify
            </button>
            <a href="{{ route('admin.approval.products') }}" class="btn btn-outline-secondary rounded-3 px-3 btn-sm">
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

    <!-- Top Actions Card - Admin Note, Order Flow, Activity Logs -->
    <div class="top-actions-card mb-3">
        <div class="row g-2">
            <!-- Admin Notes Section -->
            <div class="col-md-4">
                <div class="small-card h-100">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-pen-to-square text-primary me-1"></i> Admin Notes</h6>
                    <textarea id="adminNotes" class="form-control form-control-sm" rows="2" placeholder="Add verification notes...">{{ $product->admin_notes }}</textarea>
                    <button class="btn btn-link btn-sm p-0 mt-1 text-primary" onclick="saveAdminNotes()"><i class="fa-solid fa-save"></i> Save Notes</button>
                </div>
            </div>
            
            <!-- Order Flow Monitoring -->
            <div class="col-md-4">
                <div class="small-card h-100">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-truck-fast text-primary me-1"></i> Order Flow Monitoring</h6>
                    <div class="small mb-2">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fa-solid fa-bolt text-warning me-1"></i> Auto Order Trigger</span>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="validateAutoOrder()">Validate</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span><i class="fa-solid fa-truck text-info me-1"></i> Supplier Dispatch</span>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="trackDispatch()">Track</button>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fa-solid fa-clock text-success me-1"></i> Delivery SLA</span>
                            <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="monitorSLA()">Monitor</button>
                        </div>
                    </div>
                    <div class="text-muted small mt-1 pt-1 border-top">
                        <span>Status: <span class="badge bg-success">Active</span></span>
                        <span class="ms-2">Auto Order: {{ $product->auto_order ? 'Enabled' : 'Disabled' }}</span>
                    </div>
                </div>
            </div>
            
            <!-- Recent Activity Logs -->
            <div class="col-md-4">
                <div class="small-card h-100">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-chart-line text-primary me-1"></i> Recent Activity</h6>
                    <div id="recentActivity" style="max-height: 100px; overflow-y: auto;">
                        @if(isset($versionHistory) && count($versionHistory) > 0)
                            @foreach($versionHistory->take(3) as $log)
                                @php
                                    $icon = 'fa-edit';
                                    $color = 'text-secondary';
                                    if(str_contains($log->action, 'price')) { $icon = 'fa-tag'; $color = 'text-warning'; }
                                    elseif(str_contains($log->action, 'description')) { $icon = 'fa-file-alt'; $color = 'text-info'; }
                                    elseif(str_contains($log->action, 'approved')) { $icon = 'fa-check-circle'; $color = 'text-success'; }
                                    elseif(str_contains($log->action, 'rejected')) { $icon = 'fa-times-circle'; $color = 'text-danger'; }
                                    elseif(str_contains($log->action, 'removed') || str_contains($log->action, 'blocked')) { $icon = 'fa-trash'; $color = 'text-danger'; }
                                @endphp
                                <div class="small mb-1"><i class="fa-solid {{ $icon }} {{ $color }} me-1"></i> {{ ucfirst(str_replace('_', ' ', $log->action)) }} - {{ $log->created_at->diffForHumans() }}</div>
                            @endforeach
                        @else
                            <div class="small text-muted">No recent activity</div>
                        @endif
                    </div>
                    <button class="btn btn-link btn-sm p-0 mt-1 small" onclick="$('#verificationTabs a[href=\"#activityLogs\"]').tab('show')">View all activity →</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Basic Info Banner -->
    <div class="card border-0 shadow-sm rounded-3 mb-3">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <span class="badge bg-primary-subtle text-primary mb-1 px-2 py-1 rounded-pill">{{ ucfirst($product->type) }}</span>
                    <h5 class="fw-bold text-dark mb-1">{{ $product->name }}</h5>
                    <p class="text-muted small mb-0 d-flex gap-3 flex-wrap">
                        <span><strong>Code:</strong> {{ $product->product_code ?? 'N/A' }}</span>
                        <span><strong>Seller:</strong> {{ $product->seller->name ?? 'N/A' }}</span>
                        <span><strong>Category:</strong> {{ $product->category->name ?? 'N/A' }}</span>
                        <span><strong>Status:</strong> <span class="badge {{ $product->status == 'verified' || $product->status == 'active' ? 'bg-success' : ($product->status == 'rejected' ? 'bg-danger' : 'bg-warning') }}">{{ ucfirst($product->status) }}</span></span>
                    </p>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <div class="d-inline-block text-center p-2 rounded-4 bg-light border" style="min-width: 100px;">
                        <h4 class="fw-bold text-primary m-0">{{ $performanceData['product_score'] ?? 0 }}</h4>
                        <span class="text-muted small">Product Score</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form id="verificationForm">
        @csrf
        <input type="hidden" name="admin_notes" id="adminNotesInput">
        
        <!-- Tabs Navigation -->
        <ul class="nav nav-pills nav-justified mb-3 gap-1" id="verificationTabs" role="tablist" style="background: #f1f5f9; padding: 4px; border-radius: 10px;">
            <li class="nav-item"><a class="nav-link active py-1" data-bs-toggle="tab" href="#basicCheck">Basic</a></li>
            <li class="nav-item"><a class="nav-link py-1" data-bs-toggle="tab" href="#mediaValidation">Media</a></li>
            <li class="nav-item"><a class="nav-link py-1" data-bs-toggle="tab" href="#detailsValidation">Details</a></li>
            <li class="nav-item"><a class="nav-link py-1" data-bs-toggle="tab" href="#seoCheck">SEO</a></li>
            <li class="nav-item"><a class="nav-link py-1" data-bs-toggle="tab" href="#pricingValidation">Pricing</a></li>
            <li class="nav-item"><a class="nav-link py-1" data-bs-toggle="tab" href="#contentModeration">Content Mod</a></li>
            <li class="nav-item"><a class="nav-link py-1" data-bs-toggle="tab" href="#activityLogs">Activity</a></li>
            <li class="nav-item"><a class="nav-link py-1" data-bs-toggle="tab" href="#versionHistoryTab">Versions</a></li>
            <li class="nav-item"><a class="nav-link py-1" data-bs-toggle="tab" href="#performanceTab">Performance</a></li>
        </ul>

        <div class="tab-content mb-3">
            
            <!-- TAB 1: BASIC CHECK -->
            <div class="tab-pane fade show active" id="basicCheck">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="p-2 border rounded-2 small">
                                    <div class="d-flex justify-content-between"><span class="fw-bold">Title Quality</span><span class="badge bg-primary">{{ strlen($product->name ?? '') }} chars</span></div>
                                    <select name="title_quality" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['basic_check']['title_quality'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['basic_check']['title_quality'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 border rounded-2 small">
                                    <div class="d-flex justify-content-between"><span class="fw-bold">Description Quality</span><span class="badge bg-primary">{{ str_word_count($product->description ?? '') }} words</span></div>
                                    <select name="description_quality" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['basic_check']['description_quality'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['basic_check']['description_quality'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Category Correctness</span>
                                    <select name="category_correctness" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['basic_check']['category_correctness'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['basic_check']['category_correctness'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Brand/Organizer Authenticity</span>
                                    <select name="organizer_authenticity" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['basic_check']['organizer_authenticity'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['basic_check']['organizer_authenticity'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row g-2 mt-2">
                            <div class="col-12"><hr class="my-2"><small class="text-muted">Product Information</small></div>
                            <div class="col-md-6"><div class="info-row"><span class="info-label">Product Code:</span><span class="info-value">{{ $product->product_code ?? 'N/A' }}</span></div></div>
                            <div class="col-md-6"><div class="info-row"><span class="info-label">Seller:</span><span class="info-value">{{ $product->seller->name ?? 'N/A' }}</span></div></div>
                            <div class="col-md-6"><div class="info-row"><span class="info-label">Type:</span><span class="info-value"><span class="badge bg-info-subtle text-info">{{ ucfirst($product->type) }}</span></span></div></div>
                            <div class="col-md-6"><div class="info-row"><span class="info-label">Manufactured By:</span><span class="info-value">{{ $product->manufactured_by ?? 'N/A' }}</span></div></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: MEDIA VALIDATION -->
            <div class="tab-pane fade" id="mediaValidation">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Image Quality</span>
                                    <select name="image_quality" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['media_validation']['image_quality'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['media_validation']['image_quality'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Video Link Validity</span>
                                    <select name="video_link_validity" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['media_validation']['video_link_validity'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['media_validation']['video_link_validity'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Content Compliance</span>
                                    <select name="content_compliance" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['media_validation']['content_compliance'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['media_validation']['content_compliance'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if(count($images) > 0)
                        <div class="mt-3">
                            <small class="text-muted">Images:</small>
                            <div class="d-flex gap-1 mt-1 flex-wrap">
                                @foreach($images as $image)
                                    <img src="{{ asset($image) }}" class="product-image" style="width: 60px; height: 60px; object-fit: cover;" onerror="this.src='{{ asset('images/placeholder.png') }}'">
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if(count($videos) > 0)
                        <div class="mt-2">
                            <small class="text-muted">Video Links:</small>
                            <div class="d-flex gap-2 mt-1 flex-wrap">
                                @foreach($videos as $video)
                                    <a href="{{ $video }}" target="_blank" class="small text-primary"><i class="fa-solid fa-video me-1"></i> Watch Video</a>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB 3: DETAILS VALIDATION -->
            <div class="tab-pane fade" id="detailsValidation">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Itinerary Clarity</span>
                                    <select name="itinerary_clarity" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['details_validation']['itinerary_clarity'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['details_validation']['itinerary_clarity'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Inclusion/Exclusion</span>
                                    <select name="inclusion_exclusion" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['details_validation']['inclusion_exclusion'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['details_validation']['inclusion_exclusion'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Refund Policy</span>
                                    <select name="refund_policy_clarity" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['details_validation']['refund_policy_clarity'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['details_validation']['refund_policy_clarity'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2"><small class="text-muted">Inclusions:</small><p class="small mb-1">{{ Str::limit($product->inclusion ?? 'N/A', 200) }}</p></div>
                        <div><small class="text-muted">Exclusions:</small><p class="small mb-1">{{ Str::limit($product->exclusion ?? 'N/A', 200) }}</p></div>
                        <div><small class="text-muted">Refund Policy:</small><p class="small mb-0">{{ Str::limit($product->refund_policy ?? 'N/A', 200) }}</p></div>
                    </div>
                </div>
            </div>

            <!-- TAB 4: SEO CHECK -->
            <div class="tab-pane fade" id="seoCheck">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Tags Relevance</span>
                                    <select name="tags_relevance" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['seo_check']['tags_relevance'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['seo_check']['tags_relevance'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Keyword Quality</span>
                                    <select name="keyword_quality" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['seo_check']['keyword_quality'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['seo_check']['keyword_quality'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">No Spam Content</span>
                                    <select name="no_spam" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['seo_check']['no_spam'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['seo_check']['no_spam'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2"><small class="text-muted">SEO Tags:</small><p class="small mb-0">{{ $product->seo_tags ?? 'N/A' }}</p></div>
                        <div class="mt-2"><small class="text-muted">Meta Description:</small><p class="small mb-0">{{ $product->meta_description ?? 'N/A' }}</p></div>
                    </div>
                </div>
            </div>

            <!-- TAB 5: PRICING VALIDATION -->
            <div class="tab-pane fade" id="pricingValidation">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Price Reasonability</span>
                                    <select name="price_reasonability" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['pricing_validation']['price_reasonability'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['pricing_validation']['price_reasonability'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Discount Logic</span>
                                    <select name="discount_logic" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['pricing_validation']['discount_logic'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['pricing_validation']['discount_logic'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Tax Calculation</span>
                                    <select name="tax_calculation" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['pricing_validation']['tax_calculation'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['pricing_validation']['tax_calculation'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="p-2 border rounded-2 small">
                                    <span class="fw-bold">Net Total</span>
                                    <select name="net_total_correctness" class="form-select form-select-sm mt-1">
                                        <option value="">Select</option>
                                        <option value="pass" {{ ($checklist['pricing_validation']['net_total_correctness'] ?? '') == 'pass' ? 'selected' : '' }}>✓ Pass</option>
                                        <option value="fail" {{ ($checklist['pricing_validation']['net_total_correctness'] ?? '') == 'fail' ? 'selected' : '' }}>✗ Fail</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mt-2 p-2 bg-light rounded-2 small row">
                            <div class="col-3"><strong>Price:</strong> ₹{{ number_format($product->price ?? 0, 2) }}</div>
                            <div class="col-3"><strong>Final:</strong> ₹{{ number_format($product->final_price ?? $product->price ?? 0, 2) }}</div>
                            <div class="col-3"><strong>Discount:</strong> {{ $product->discount ?? 0 }}%</div>
                            <div class="col-3"><strong>Margin:</strong> ₹{{ number_format(($product->final_price ?? $product->price ?? 0) - ($product->supplier_cost ?? 0), 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 6: CONTENT MODERATION - DYNAMIC -->
            <div class="tab-pane fade" id="contentModeration">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-shield-halved text-primary me-1"></i> Content Moderation</h6>
                        
                        <!-- Remove Misleading Content - Dynamic -->
                        <div class="border rounded-2 p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fa-solid fa-trash text-danger me-2"></i> 
                                    <strong>Remove Misleading Content</strong>
                                    <br><small class="text-muted">Remove false, deceptive, or inaccurate information</small>
                                    <div class="mt-2">
                                        <select id="misleadingField" class="form-select form-select-sm w-auto d-inline-block me-2" style="width: auto;">
                                            <option value="name">Title</option>
                                            <option value="description">Description</option>
                                            <option value="seo_tags">SEO Tags</option>
                                            <option value="inclusion">Inclusions</option>
                                            <option value="exclusion">Exclusions</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMisleadingContent()">
                                    <i class="fa-solid fa-trash-can me-1"></i> Remove
                                </button>
                            </div>
                        </div>
                        
                        <!-- Flag Duplicate Listings - Dynamic -->
                        <div class="border rounded-2 p-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fa-solid fa-copy text-warning me-2"></i> 
                                    <strong>Flag Duplicate Listings</strong>
                                    <br><small class="text-muted">Mark this as a duplicate of an existing product/service</small>
                                    <div class="mt-2">
                                        <input type="text" id="duplicateProductId" class="form-control form-control-sm d-inline-block me-2" placeholder="Product ID / URL" style="width: 200px;">
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="flagDuplicate()">
                                    <i class="fa-solid fa-flag me-1"></i> Flag as Duplicate
                                </button>
                            </div>
                        </div>
                        
                        <!-- Block Inappropriate Services - Dynamic -->
                        <div class="border rounded-2 p-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <i class="fa-solid fa-ban text-danger me-2"></i> 
                                    <strong>Block Inappropriate Services</strong>
                                    <br><small class="text-muted">Block if violates platform policies, has prohibited content, or is fraudulent</small>
                                    <div class="mt-2">
                                        <select id="blockReason" class="form-select form-select-sm w-auto d-inline-block me-2" style="width: auto;">
                                            <option value="Prohibited content">Prohibited content</option>
                                            <option value="Fraudulent activity">Fraudulent activity</option>
                                            <option value="Policy violation">Policy violation</option>
                                            <option value="Spam/Misleading">Spam/Misleading</option>
                                        </select>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="blockInappropriate()">
                                    <i class="fa-solid fa-ban me-1"></i> Block Service
                                </button>
                            </div>
                        </div>

                        <!-- Seller Removal Request (if exists) -->
                        @if(isset($product->seller_removal_requested) && $product->seller_removal_requested)
                        <div class="alert alert-warning py-2 px-3 small mt-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span><i class="fa-solid fa-flag me-1"></i> Seller removal request pending</span>
                                <div>
                                    <button class="btn btn-sm btn-success me-1" onclick="approveSellerRemoval()">Approve</button>
                                    <button class="btn btn-sm btn-danger" onclick="rejectSellerRemoval()">Reject</button>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- TAB 7: ACTIVITY LOGS -->
            <div class="tab-pane fade" id="activityLogs">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-history text-primary me-1"></i> Complete Activity Logs</h6>
                        <div id="fullActivityLogs" style="max-height: 500px; overflow-y: auto;">
                            @if(isset($versionHistory) && count($versionHistory) > 0)
                                @foreach($versionHistory as $log)
                                    @php
                                        $icon = 'fa-edit';
                                        $borderClass = 'border-secondary';
                                        if(str_contains($log->action, 'price')) { $icon = 'fa-tag'; $borderClass = 'border-warning'; }
                                        elseif(str_contains($log->action, 'description')) { $icon = 'fa-file-alt'; $borderClass = 'border-info'; }
                                        elseif(str_contains($log->action, 'approved') || str_contains($log->action, 'verified')) { $icon = 'fa-check-circle'; $borderClass = 'border-success'; }
                                        elseif(str_contains($log->action, 'rejected')) { $icon = 'fa-times-circle'; $borderClass = 'border-danger'; }
                                        elseif(str_contains($log->action, 'category')) { $icon = 'fa-folder'; $borderClass = 'border-primary'; }
                                        elseif(str_contains($log->action, 'removed') || str_contains($log->action, 'blocked')) { $icon = 'fa-trash'; $borderClass = 'border-danger'; }
                                        elseif(str_contains($log->action, 'rollback')) { $icon = 'fa-undo'; $borderClass = 'border-secondary'; }
                                    @endphp
                                    <div class="activity-log-item {{ $borderClass }}" style="border-left-width: 3px; border-left-style: solid; padding: 12px; margin-bottom: 10px; background: #f8f9fa; border-radius: 8px;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <i class="fa-solid {{ $icon }} me-2"></i>
                                                <strong>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</strong>
                                                <div class="small text-muted mt-1">
                                                    <i class="fa-regular fa-clock me-1"></i>{{ $log->created_at->format('d M Y, h:i A') }}
                                                    <span class="mx-2">|</span>
                                                    <i class="fa-regular fa-user me-1"></i>{{ $log->user->name ?? 'System' }}
                                                </div>
                                                @php 
                                                    $changeData = is_array($log->change_data) ? $log->change_data : json_decode($log->change_data, true); 
                                                @endphp
                                                @if(isset($changeData['old_status']) && isset($changeData['new_status']))
                                                    <div class="small mt-2"><span class="badge bg-secondary">Status: {{ ucfirst($changeData['old_status']) }} → {{ ucfirst($changeData['new_status']) }}</span></div>
                                                @endif
                                                @if(isset($changeData['field']) && isset($changeData['reason']))
                                                    <div class="small mt-2"><span class="badge bg-danger">Field: {{ $changeData['field'] }} - Reason: {{ $changeData['reason'] }}</span></div>
                                                @endif
                                                @if(isset($changeData['reason']))
                                                    <div class="small mt-1 text-muted">Reason: {{ $changeData['reason'] }}</div>
                                                @endif
                                            </div>
                                            <span class="badge bg-secondary">{{ $log->created_at->diffForHumans() }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center py-4">No activity logs found</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 8: VERSION HISTORY -->
            <div class="tab-pane fade" id="versionHistoryTab">
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3"><i class="fa-solid fa-code-branch text-primary me-1"></i> Version History & Rollback</h6>
                        <div id="versionHistoryList" style="max-height: 600px; overflow-y: auto;">
                            @if(isset($versionHistory) && count($versionHistory) > 0)
                                @foreach($versionHistory as $index => $log)
                                    @php
                                        $snapshot = is_array($log->snapshot) ? $log->snapshot : json_decode($log->snapshot, true);
                                        $changeData = is_array($log->change_data) ? $log->change_data : json_decode($log->change_data, true);
                                    @endphp
                                    <div class="border rounded-2 p-3 mb-3 version-item" data-version-id="{{ $log->id }}">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <div>
                                                <span class="badge bg-primary me-2">v{{ count($versionHistory) - $index }}</span>
                                                <strong>{{ ucfirst(str_replace('_', ' ', $log->action)) }}</strong>
                                                <span class="text-muted ms-2 small">by {{ $log->user->name ?? 'System' }}</span>
                                            </div>
                                            <span class="text-muted small">{{ $log->created_at->format('d M Y, h:i A') }}</span>
                                        </div>
                                        
                                        @if($changeData)
                                            <div class="version-diff small mb-2">
                                                @if(isset($changeData['old_status']) && isset($changeData['new_status']))
                                                    <div>Status: <span class="diff-removed">{{ $changeData['old_status'] }}</span> → <span class="diff-added">{{ $changeData['new_status'] }}</span></div>
                                                @endif
                                                @if(isset($changeData['old_category']) && isset($changeData['new_category']))
                                                    <div>Category: <span class="diff-removed">{{ $changeData['old_category'] }}</span> → <span class="diff-added">{{ $changeData['new_category'] }}</span></div>
                                                @endif
                                                @if(isset($changeData['field']) && isset($changeData['old_value']))
                                                    <div>{{ ucfirst($changeData['field']) }}: <span class="diff-removed">{{ Str::limit($changeData['old_value'], 100) }}</span></div>
                                                @endif
                                            </div>
                                        @endif
                                        
                                        <div class="mt-2">
                                            <button class="btn btn-sm btn-outline-secondary me-2" onclick="viewVersion({{ $log->id }})">
                                                <i class="fa-solid fa-eye me-1"></i> View
                                            </button>
                                            @if($index > 0)
                                                <button class="btn btn-sm btn-outline-warning" onclick="rollbackVersion({{ $log->id }})">
                                                    <i class="fa-solid fa-undo me-1"></i> Rollback
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <p class="text-muted text-center py-4">No version history available</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 9: PERFORMANCE -->
            <div class="tab-pane fade" id="performanceTab">
                <div class="row g-2 mb-3">
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 rounded-3 p-2 text-center">
                            <span class="text-muted small">Lead Quality</span>
                            <h6 class="fw-bold text-primary m-0">{{ $performanceData['lead_quality_score'] ?? 85 }}%</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 rounded-3 p-2 text-center">
                            <span class="text-muted small">Lead Conversion</span>
                            <h6 class="fw-bold text-success m-0">{{ $performanceData['lead_conversion_score'] ?? 72 }}%</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 rounded-3 p-2 text-center">
                            <span class="text-muted small">Seller Score</span>
                            <h6 class="fw-bold text-info m-0">{{ $performanceData['seller_score'] ?? 91 }}/100</h6>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card shadow-sm border-0 rounded-3 p-2 text-center">
                            <span class="text-muted small">Product Score</span>
                            <h6 class="fw-bold text-warning m-0">{{ $performanceData['product_score'] ?? 88 }}/100</h6>
                        </div>
                    </div>
                </div>
                <div class="card border-0 shadow-sm rounded-3">
                    <div class="card-body p-3">
                        <h6 class="fw-bold mb-3">Performance Metrics</h6>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Average Rating</span>
                                    <span class="fw-bold">{{ number_format($performanceData['metrics']['ratings'] ?? 4.8, 1) }} / 5.0</span>
                                </div>
                                <div class="progress mb-3" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ (($performanceData['metrics']['ratings'] ?? 4.8) / 5) * 100 }}%"></div>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Complaints</span>
                                    <span class="fw-bold text-danger">{{ $performanceData['metrics']['complaints'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Returns</span>
                                    <span class="fw-bold text-danger">{{ $performanceData['metrics']['returns'] ?? 0 }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Avg. Delivery Days</span>
                                    <span class="fw-bold">{{ $performanceData['metrics']['delivery_time'] ?? 3 }} days</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Sales</span>
                                    <span class="fw-bold">{{ $performanceData['metrics']['total_sales'] ?? 0 }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">Total Views</span>
                                    <span class="fw-bold">{{ $performanceData['metrics']['views'] ?? 0 }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Version View Modal -->
<div class="modal fade" id="versionViewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Version Snapshot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="versionSnapshotContent">
                <pre class="bg-light p-3 rounded" style="max-height: 500px; overflow: auto;"></pre>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
let productId = {{ $product->id }};
let csrfToken = '{{ csrf_token() }}';

// Submit Verification
function submitVerification(status) {
    let adminNotes = document.getElementById('adminNotes').value;
    document.getElementById('adminNotesInput').value = adminNotes;
    
    let formData = new FormData(document.getElementById('verificationForm'));
    formData.append('verification_status', status);
    formData.append('_token', csrfToken);
    
    if(confirm(`Are you sure you want to ${status} this product?`)) {
        $.ajax({
            url: "{{ route('admin.approval.submit.verification', $product->id) }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    toastr.success('Verification completed successfully!');
                    window.location.href = response.redirect;
                } else {
                    toastr.error('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                toastr.error('Error: ' + (xhr.responseJSON?.message || xhr.responseText));
            }
        });
    }
}

// Save Admin Notes
function saveAdminNotes() {
    let notes = $('#adminNotes').val();
    $.ajax({
        url: `/admin/approval/product/${productId}/save-notes`,
        method: "POST",
        data: { admin_notes: notes, _token: csrfToken },
        success: function(response) {
            if(response.success) {
                toastr.success('Notes saved successfully');
            }
        },
        error: function() {
            toastr.error('Failed to save notes');
        }
    });
}

// Order Flow Functions
function validateAutoOrder() {
    $.ajax({
        url: `/admin/approval/product/${productId}/validate-auto-order`,
        method: "GET",
        success: function(response) {
            if(response.success) {
                toastr.info(response.message || 'Auto order validation completed');
            }
        }
    });
}

function trackDispatch() {
    $.ajax({
        url: `/admin/approval/product/${productId}/track-dispatch`,
        method: "GET",
        success: function(response) {
            toastr.info(response.message || 'Dispatch tracking information loaded');
        }
    });
}

function monitorSLA() {
    $.ajax({
        url: `/admin/approval/product/${productId}/monitor-sla`,
        method: "GET",
        success: function(response) {
            toastr.info(response.message || 'SLA monitoring data loaded');
        }
    });
}

// Content Moderation Functions
function removeMisleadingContent() {
    let field = $('#misleadingField').val();
    let reason = prompt('Reason for removal:');
    if(reason) {
        $.ajax({
            url: `/admin/approval/product/${productId}/remove-misleading`,
            method: "POST",
            data: { field_to_remove: field, removal_reason: reason, _token: csrfToken },
            success: function(response) {
                if(response.success) {
                    toastr.success('Misleading content removed from ' + field);
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
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
        toastr.warning('Please enter duplicate product ID or URL');
        return;
    }
    if(confirm('Flag this as duplicate listing?')) {
        $.ajax({
            url: `/admin/approval/product/${productId}/flag-duplicate`,
            method: "POST",
            data: { duplicate_id: duplicateId, _token: csrfToken },
            success: function(response) {
                if(response.success) {
                    toastr.success('Product flagged as duplicate');
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }
}

function blockInappropriate() {
    let reason = $('#blockReason').val();
    if(confirm(`Block this service? Reason: ${reason}`)) {
        $.ajax({
            url: `/admin/approval/product/${productId}/block-inappropriate`,
            method: "POST",
            data: { reason: reason, _token: csrfToken },
            success: function(response) {
                if(response.success) {
                    toastr.success('Service blocked successfully');
                    location.reload();
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }
}

// Seller Removal Functions
function approveSellerRemoval() {
    if(confirm('Approve seller removal request?')) {
        $.ajax({
            url: `/admin/approval/product/${productId}/approve-seller-removal`,
            method: "POST",
            data: { _token: csrfToken },
            success: function(response) {
                if(response.success) {
                    toastr.success('Seller removal approved');
                    location.reload();
                }
            }
        });
    }
}

function rejectSellerRemoval() {
    if(confirm('Reject seller removal request?')) {
        $.ajax({
            url: `/admin/approval/product/${productId}/reject-seller-removal`,
            method: "POST",
            data: { _token: csrfToken },
            success: function(response) {
                if(response.success) {
                    toastr.success('Seller removal rejected');
                    location.reload();
                }
            }
        });
    }
}

// Version History Functions
function viewVersion(versionId) {
    $.ajax({
        url: `/admin/approval/product/${productId}/version/${versionId}`,
        method: "GET",
        success: function(response) {
            if(response.success) {
                let snapshot = response.snapshot;
                $('#versionSnapshotContent pre').text(JSON.stringify(snapshot, null, 2));
                $('#versionViewModal').modal('show');
            }
        }
    });
}

function rollbackVersion(versionId) {
    if(confirm('Are you sure you want to rollback to this version? This action cannot be undone.')) {
        $.ajax({
            url: `/admin/approval/product/${productId}/rollback/${versionId}`,
            method: "POST",
            data: { _token: csrfToken },
            success: function(response) {
                if(response.success) {
                    toastr.success('Rollback successful! Refreshing page...');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    toastr.error(response.message);
                }
            }
        });
    }
}

// Load Activity Logs dynamically
function loadActivityLogs() {
    $.ajax({
        url: `/admin/approval/product/${productId}/activity-logs`,
        method: "GET",
        success: function(response) {
            if(response.success && response.logs) {
                // Update recent activity
                let recentHtml = '';
                response.logs.slice(0, 3).forEach(log => {
                    recentHtml += `<div class="small mb-1"><i class="fa-solid fa-${log.icon || 'edit'} me-1"></i> ${log.action} - ${new Date(log.created_at).toLocaleDateString()}</div>`;
                });
                $('#recentActivity').html(recentHtml || '<div class="small text-muted">No recent activity</div>');
            }
        }
    });
}

$(document).ready(function() {
    $('#verificationTabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });
    
    // Refresh activity logs every 30 seconds
    setInterval(loadActivityLogs, 30000);
});
</script>

<!-- Toastr Notifications -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
@endsection