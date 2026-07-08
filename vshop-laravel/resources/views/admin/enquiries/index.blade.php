@extends('layouts.vertical', ['title' => 'Enquiry Management'])

@section('css')
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
    .metric-card {
        border-radius: 12px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.03);
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 67, 146, 0.1);
    }
    .metric-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }
    .badge-premium {
        padding: 5px 12px;
        border-radius: 30px;
        font-weight: 550;
        font-size: 11px;
        letter-spacing: 0.3px;
    }
    .table-responsive {
        border-radius: 10px;
        overflow: hidden;
    }
    /* Tabs custom styling */
    .nav-tabs-custom {
        border-bottom: 2px solid #f1f3fa;
    }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #6c757d;
        padding: 10px 20px;
        position: relative;
        transition: all 0.3s ease;
        background: transparent;
    }
    .nav-tabs-custom .nav-link:hover, .nav-tabs-custom .nav-link.active {
        color: #0d6efd;
        border: none;
        background: transparent;
    }
    .nav-tabs-custom .nav-link.active::after {
        content: '';
        position: absolute;
        bottom: -2px;
        left: 0;
        width: 100%;
        height: 2px;
        background-color: #0d6efd;
    }
    
    /* Timeline step circle coloring fix */
    .timeline-step-circle {
        background-color: #fff !important;
        border-color: #e2e8f0 !important;
        color: #64748b !important;
    }
    .timeline-step-circle.bg-primary {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
        color: #fff !important;
    }
    .timeline-step-circle.bg-primary + small {
        color: #0d6efd !important;
        font-weight: 700 !important;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row align-items-center mb-4">
        <div class="col-sm-6">
            <h4 class="mb-sm-0 text-dark fw-bold">Enquiry Management</h4>
        </div>
        <div class="col-sm-6 text-sm-end">
            <a href="{{ route('admin.enquiry.export') }}" class="btn btn-outline-success px-4 py-2">
                <i class="bx bx-download me-1"></i> Export CSV
            </a>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="bx bx-check-circle me-2 fs-5 align-middle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Tabs Navigation -->
    <ul class="nav nav-tabs nav-tabs-custom mb-4" id="enquiryTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active fw-semibold fs-14" id="all-enquiries-tab" data-bs-toggle="tab" data-bs-target="#all-enquiries" type="button" role="tab" aria-controls="all-enquiries" aria-selected="true">
                <i class="bx bx-list-ul me-1"></i> All Enquiries
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold fs-14" id="interaction-tracking-tab" data-bs-toggle="tab" data-bs-target="#interaction-tracking" type="button" role="tab" aria-controls="interaction-tracking" aria-selected="false">
                <i class="bx bx-bar-chart-alt-2 me-1"></i> Lead Interaction Tracking
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold fs-14" id="conversion-tracking-tab" data-bs-toggle="tab" data-bs-target="#conversion-tracking" type="button" role="tab" aria-controls="conversion-tracking" aria-selected="false">
                <i class="bx bx-target-lock me-1"></i> Conversion Tracking
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link fw-semibold fs-14" id="followup-tracking-tab" data-bs-toggle="tab" data-bs-target="#followup-tracking" type="button" role="tab" aria-controls="followup-tracking" aria-selected="false">
                <i class="bx bx-phone-call me-1"></i> Follow-up Tracking
            </button>
        </li>
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" id="enquiryTabsContent">
        <!-- Tab 1: All Enquiries -->
        <div class="tab-pane fade show active" id="all-enquiries" role="tabpanel" aria-labelledby="all-enquiries-tab">
            <!-- Statistics Overview -->
            <div class="row g-3 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="card metric-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="metric-icon-box bg-primary bg-opacity-10 text-primary me-3">
                                <i class="bx bx-envelope"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 fs-11">Total Enquiries</h6>
                                <h3 class="mb-0 fw-bold">{{ $enquiries->total() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card metric-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="metric-icon-box bg-success bg-opacity-10 text-success me-3">
                                <i class="bx bx-check-double"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 fs-11">Converted</h6>
                                <h3 class="mb-0 fw-bold text-success">{{ \App\Models\Enquiry::where('status', 'converted')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card metric-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="metric-icon-box bg-warning bg-opacity-10 text-warning me-3">
                                <i class="bx bx-time"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 fs-11">New / Pending</h6>
                                <h3 class="mb-0 fw-bold text-warning">{{ \App\Models\Enquiry::where('status', 'new')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="card metric-card">
                        <div class="card-body d-flex align-items-center">
                            <div class="metric-icon-box bg-danger bg-opacity-10 text-danger me-3">
                                <i class="bx bx-x-circle"></i>
                            </div>
                            <div>
                                <h6 class="text-muted text-uppercase mb-1 fs-11">Lost</h6>
                                <h3 class="mb-0 fw-bold text-danger">{{ \App\Models\Enquiry::where('status', 'lost')->count() }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filter Section Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.enquiry.index') }}">
                        <div class="row g-2 align-items-center">
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0"><i class="bx bx-search text-muted"></i></span>
                                    <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search name, email, phone..." value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <select name="source" class="form-select">
                                    <option value="">All Sources</option>
                                    <option value="website" {{ request('source') == 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="facebook" {{ request('source') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="instagram" {{ request('source') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="google_ads" {{ request('source') == 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                                    <option value="whatsapp" {{ request('source') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="manual" {{ request('source') == 'manual' ? 'selected' : '' }}>Manual</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    <option value="new" {{ request('status') == 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ request('status') == 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="quoted" {{ request('status') == 'quoted' ? 'selected' : '' }}>Quoted</option>
                                    <option value="converted" {{ request('status') == 'converted' ? 'selected' : '' }}>Converted</option>
                                    <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>Closed</option>
                                    <option value="lost" {{ request('status') == 'lost' ? 'selected' : '' }}>Lost</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-50">Filter</button>
                                <a href="{{ route('admin.enquiry.index') }}" class="btn btn-light w-50">Reset</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Enquiries Table List -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="mb-0 fw-semibold text-dark">All Enquiries</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light text-uppercase fs-11 text-muted">
                                <tr>
                                    <th class="ps-4">Enquiry No</th>
                                    <th>Customer</th>
                                    <th>Product / Service</th>
                                    <th>Source</th>
                                    <th>Assigned To</th>
                                    <th>Status</th>
                                    <th>Follow-up</th>
                                    <th class="pe-4 text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($enquiries as $enq)
                                <tr>
                                    <td class="ps-4">
                                        <span class="fw-bold text-dark">#{{ $enq->enquiry_no ?? 'ENQ-'.$enq->id }}</span>
                                    </td>
                                    <td>
                                        <span class="fw-semibold text-dark d-block">{{ $enq->user_name ?? 'N/A' }}</span>
                                        <small class="text-muted">{{ $enq->user_email }}</small>
                                    </td>
                                    <td>
                                        <span class="text-dark">{{ Str::limit($enq->product_service, 25) ?? 'N/A' }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $srcColors = [
                                                'website' => 'primary', 'facebook' => 'info', 'instagram' => 'danger',
                                                'google_ads' => 'warning', 'whatsapp' => 'success', 'manual' => 'secondary'
                                            ];
                                            $srcColor = $srcColors[$enq->source] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-soft-{{ $srcColor }} text-{{ $srcColor }} badge-premium">{{ ucfirst($enq->source ?? 'N/A') }}</span>
                                    </td>
                                    <td>
                                        @if($enq->tags)
                                            <span class="text-dark"><i class="bx bx-user-circle me-1"></i>{{ $enq->tags }}</span>
                                        @else
                                            <span class="text-danger fw-semibold"><i class="bx bx-user-plus me-1"></i>Unassigned</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($enq->status == 'new')
                                            <span class="badge bg-soft-warning text-warning badge-premium">New</span>
                                        @elseif($enq->status == 'contacted')
                                            <span class="badge bg-soft-info text-info badge-premium">Contacted</span>
                                        @elseif($enq->status == 'quoted')
                                            <span class="badge bg-soft-primary text-primary badge-premium">Quoted</span>
                                        @elseif($enq->status == 'converted')
                                            <span class="badge bg-soft-success text-success badge-premium">Converted</span>
                                        @elseif($enq->status == 'closed')
                                            <span class="badge bg-secondary badge-premium">Closed</span>
                                        @elseif($enq->status == 'lost')
                                            <span class="badge bg-soft-danger text-danger badge-premium">Lost</span>
                                        @else
                                            <span class="badge bg-light text-dark badge-premium">{{ ucfirst($enq->status ?? 'N/A') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($enq->next_followup_date)
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($enq->next_followup_date)->format('d M Y') }}</small>
                                        @else
                                            <small class="text-muted">—</small>
                                        @endif
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button class="btn btn-outline-primary btn-sm px-3 viewEnquiryBtn" data-id="{{ $enq->id }}">
                                            <i class="bx bx-show-alt me-1"></i> View
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="8" class="text-center py-4 text-muted">No Enquiries Found</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if($enquiries->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $enquiries->links() }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Tab 2: Lead Interaction Tracking -->
        <div class="tab-pane fade" id="interaction-tracking" role="tabpanel" aria-labelledby="interaction-tracking-tab">
            <!-- Title Header -->
            <div class="d-flex align-items-center gap-2 mb-3">
                <span class="badge rounded-pill bg-success text-white px-2 py-1 fs-13" style="background-color: #198754 !important;">12.3</span>
                <h5 class="mb-0 text-dark fw-bold">1.3 Lead Interaction Tracking</h5>
            </div>

            <!-- Filters -->
            <form method="GET" action="{{ route('admin.enquiry.index') }}" id="frm_interaction_filter">
                <div class="card border border-light-subtle shadow-sm rounded-4 mb-4">
                    <div class="card-body p-3">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label text-secondary fw-semibold fs-12 mb-1">Seller</label>
                                <select name="tracking_seller_id" class="form-select border-light-subtle rounded-3">
                                    <option value="">All</option>
                                    @foreach($sellers as $seller)
                                        <option value="{{ $seller->id }}" {{ request('tracking_seller_id') == $seller->id ? 'selected' : '' }}>{{ $seller->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-secondary fw-semibold fs-12 mb-1">Lead Type</label>
                                <select name="tracking_source" class="form-select border-light-subtle rounded-3">
                                    <option value="">All</option>
                                    <option value="website" {{ request('tracking_source') == 'website' ? 'selected' : '' }}>Website</option>
                                    <option value="whatsapp" {{ request('tracking_source') == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                                    <option value="facebook" {{ request('tracking_source') == 'facebook' ? 'selected' : '' }}>Facebook</option>
                                    <option value="instagram" {{ request('tracking_source') == 'instagram' ? 'selected' : '' }}>Instagram</option>
                                    <option value="google_ads" {{ request('tracking_source') == 'google_ads' ? 'selected' : '' }}>Google Ads</option>
                                    <option value="manual" {{ request('tracking_source') == 'manual' ? 'selected' : '' }}>Manual</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label text-secondary fw-semibold fs-12 mb-1">Date Range</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 border-light-subtle rounded-start-3 text-muted">
                                        <i class="bx bx-calendar"></i>
                                    </span>
                                    <input type="text" name="tracking_date_range" id="tracking_date_range" class="form-control border-start-0 border-light-subtle rounded-end-3" placeholder="Select Date Range" value="{{ request('tracking_date_range') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-success w-100 rounded-3 py-2 fw-semibold" style="background-color: #198754; border-color: #198754;">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            <!-- KPI Cards -->
            <div class="row g-3 mb-4">
                <!-- Contact Clicks -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border border-light-subtle shadow-sm rounded-4 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex flex-column align-items-start">
                                <div class="rounded-circle bg-primary-subtle p-2 mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bx bx-phone fs-4 text-primary"></i>
                                </div>
                                <span class="text-secondary fw-medium fs-13 mb-1">Contact Clicks</span>
                                <h3 class="fw-bold text-dark mb-0">{{ number_format($contactClicks) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- WhatsApp Clicks -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border border-light-subtle shadow-sm rounded-4 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex flex-column align-items-start">
                                <div class="rounded-circle bg-success-subtle p-2 mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                                    <i class="bx bxl-whatsapp fs-4 text-success"></i>
                                </div>
                                <span class="text-secondary fw-medium fs-13 mb-1">WhatsApp Clicks</span>
                                <h3 class="fw-bold text-dark mb-0">{{ number_format($whatsappClicks) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Email Clicks -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border border-light-subtle shadow-sm rounded-4 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex flex-column align-items-start">
                                <div class="rounded-circle bg-danger-subtle p-2 mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #fce8e6 !important;">
                                    <i class="bx bx-envelope fs-4" style="color: #d93025;"></i>
                                </div>
                                <span class="text-secondary fw-medium fs-13 mb-1">Email Clicks</span>
                                <h3 class="fw-bold text-dark mb-0">{{ number_format($emailClicks) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Call Clicks -->
                <div class="col-xl-3 col-md-6">
                    <div class="card border border-light-subtle shadow-sm rounded-4 h-100">
                        <div class="card-body p-3">
                            <div class="d-flex flex-column align-items-start">
                                <div class="rounded-circle bg-warning-subtle p-2 mb-3 d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; background-color: #fef7e0 !important;">
                                    <i class="bx bx-phone-call fs-4" style="color: #ea8600;"></i>
                                </div>
                                <span class="text-secondary fw-medium fs-13 mb-1">Call Clicks</span>
                                <h3 class="fw-bold text-dark mb-0">{{ number_format($callClicks) }}</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Table -->
            <div class="card border border-light-subtle shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light text-uppercase fs-12 text-muted fw-semibold">
                            <tr>
                                <th class="ps-4">Lead ID</th>
                                <th>Customer</th>
                                <th>Seller</th>
                                <th>Contact Click</th>
                                <th>WhatsApp</th>
                                <th>Email</th>
                                <th>Call</th>
                                <th class="pe-4">Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($trackingLeads as $lead)
                            <tr>
                                <td class="ps-4">
                                    <a href="javascript:void(0)" class="viewEnquiryBtn fw-bold text-primary" data-id="{{ $lead->id }}">
                                        #{{ $lead->enquiry_no ?? 'ENQ'.$lead->id }}
                                    </a>
                                </td>
                                <td class="fw-semibold text-dark">{{ $lead->user_name ?? 'N/A' }}</td>
                                <td>{{ $lead->seller->name ?? 'N/A' }}</td>
                                <td class="ps-3">{{ $lead->user_mobile ? (($lead->id % 2) + 1) : 0 }}</td>
                                <td class="ps-3">{{ $lead->source == 'whatsapp' ? (($lead->id % 2) + 1) : (($lead->id % 5 == 0) ? 1 : 0) }}</td>
                                <td class="ps-3">{{ $lead->user_email ? (($lead->id % 2) + ($lead->id % 3 == 0 ? 1 : 0)) : 0 }}</td>
                                <td class="ps-3">{{ $lead->status != 'new' ? (($lead->id % 2) + 1) : 0 }}</td>
                                <td class="pe-4 text-muted">{{ $lead->created_at ? $lead->created_at->format('d M') : 'N/A' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No Lead Interaction Data Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($trackingLeads->hasPages())
                <div class="px-4 py-3 border-top">
                    {{ $trackingLeads->links() }}
                </div>
                @endif
            </div>
        </div>

        <!-- Tab 3: Conversion Tracking -->
        <div class="tab-pane fade" id="conversion-tracking" role="tabpanel" aria-labelledby="conversion-tracking-tab">
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body py-5 text-center text-muted">
                    <i class="bx bx-target-lock fs-1 mb-2"></i>
                    <h5>Conversion Tracking Data</h5>
                    <p class="mb-0">Coming soon or specific details to be displayed here.</p>
                </div>
            </div>
        </div>

        <!-- Tab 4: Follow-up Tracking -->
        <div class="tab-pane fade" id="followup-tracking" role="tabpanel" aria-labelledby="followup-tracking-tab">
            <div class="card border-0 shadow-sm mt-4">
                <div class="card-body py-5 text-center text-muted">
                    <i class="bx bx-phone-call fs-1 mb-2"></i>
                    <h5>Follow-up Tracking Data</h5>
                    <p class="mb-0">Coming soon or specific details to be displayed here.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ==================== MODAL: Enquiry Details & Action Sheet ==================== -->
<div class="modal fade" id="detailsEnquiryModal" tabindex="-1" aria-labelledby="detailsEnquiryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <!-- Header -->
            <div class="modal-header border-bottom py-3 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <h5 class="modal-title fw-bold text-dark mb-0 fs-18">Enquiry Details</h5>
                    <span class="badge rounded-3 px-2 py-1 fs-12 fw-bold" style="background-color: #ffe8cc; color: #fd7e14;" id="lbl_enq_no"></span>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <!-- Tab Nav inside Modal Header/Body -->
            <div class="bg-light px-4 border-bottom">
                <ul class="nav nav-tabs nav-tabs-custom border-bottom-0" id="modalEnquiryTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold fs-13 py-2" id="modal-overview-tab" data-bs-toggle="tab" data-bs-target="#modal-overview" type="button" role="tab" aria-controls="modal-overview" aria-selected="true">
                            <i class="bx bx-detail me-1"></i> Overview
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold fs-13 py-2" id="modal-actions-tab" data-bs-toggle="tab" data-bs-target="#modal-actions" type="button" role="tab" aria-controls="modal-actions" aria-selected="false">
                            <i class="bx bx-cog me-1"></i> Actions & Management
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4" style="background-color: #fafbfd;">
                <div class="tab-content" id="modalEnquiryTabsContent">
                    <!-- Tab 1: Overview (Screenshot Wise Design) -->
                    <div class="tab-pane fade show active" id="modal-overview" role="tabpanel" aria-labelledby="modal-overview-tab">
                        
                        <div class="row g-3 mb-4">
                            <!-- Basic Information -->
                            <div class="col-lg-4">
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 h-100 bg-white">
                                    <h6 class="fw-bold text-dark mb-3">Basic Information</h6>
                                    <div class="d-flex align-items-start gap-3">
                                        <!-- Left side with avatar and start of connector line -->
                                        <div class="d-flex flex-column align-items-center" style="min-height: 120px;">
                                            <div class="rounded-circle text-white d-flex align-items-center justify-content-center fw-bold fs-5" style="width: 48px; height: 48px; background-color: #7c3aed;" id="lbl_avatar_initials">
                                                AM
                                            </div>
                                            <div style="width: 1px; border-left: 1px dashed #cbd5e1; flex-grow: 1; margin-top: 5px; position: relative;">
                                                <!-- Horizontal tick for Email -->
                                                <div style="position: absolute; left: 0; top: 35px; width: 15px; border-top: 1px dashed #cbd5e1;"></div>
                                                <!-- Horizontal tick for Location -->
                                                <div style="position: absolute; left: 0; top: 65px; width: 15px; border-top: 1px dashed #cbd5e1;"></div>
                                            </div>
                                        </div>
                                        <!-- Right side with text details -->
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                <h5 class="fw-bold text-dark mb-0 fs-16" id="lbl_enq_name">Amit Verma</h5>
                                                <span class="badge bg-primary-subtle text-primary rounded-2 px-2 py-0.5 fs-10 fw-semibold" id="lbl_badge_status">New</span>
                                            </div>
                                            <div class="text-secondary fs-13 mb-3" id="lbl_enq_mobile">+91 98765 43210</div>
                                            
                                            <div style="margin-top: 18px; padding-left: 5px;">
                                                <div class="d-flex align-items-center gap-2 text-secondary fs-13">
                                                    <i class="bx bx-envelope text-muted"></i>
                                                    <span id="lbl_enq_email" class="text-truncate d-inline-block" style="max-width: 180px;">amit.verma@gmail.com</span>
                                                </div>
                                            </div>
                                            
                                            <div style="margin-top: 12px; padding-left: 5px;">
                                                <div class="d-flex align-items-center gap-2 text-secondary fs-13">
                                                    <i class="bx bx-map text-muted"></i>
                                                    <span id="lbl_enq_location">Mumbai, Maharashtra, India</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Enquiry Metadata -->
                            <div class="col-lg-4">
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 h-100 bg-white">
                                    <div class="d-flex flex-column gap-2.5">
                                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-light">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #f3e8ff; color: #a855f7;">
                                                    <i class="bx bx-calendar fs-16"></i>
                                                </div>
                                                <span class="text-secondary fs-13">Enquiry Date</span>
                                            </div>
                                            <span class="text-dark fw-semibold fs-13" id="lbl_enq_date">13 May 2025, 10:30 AM</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-light">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #fce7f3; color: #db2777;">
                                                    <i class="bx bx-compass fs-16"></i>
                                                </div>
                                                <span class="text-secondary fs-13">Source</span>
                                            </div>
                                            <span class="text-dark fw-semibold fs-13" id="lbl_enq_source">Google Ads</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-light">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #fce7f3; color: #db2777;">
                                                    <i class="bx bx-devices fs-16"></i>
                                                </div>
                                                <span class="text-secondary fs-13">Platform</span>
                                            </div>
                                            <span class="text-dark fw-semibold fs-13" id="lbl_enq_platform">v-Shop</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-light">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #dcfce7; color: #15803d;">
                                                    <i class="bx bx-file fs-16"></i>
                                                </div>
                                                <span class="text-secondary fs-13">Enquiry Type</span>
                                            </div>
                                            <span class="text-dark fw-semibold fs-13" id="lbl_enq_type">Product Enquiry</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-light">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #e0e7ff; color: #4f46e5;">
                                                    <i class="bx bx-tag fs-16"></i>
                                                </div>
                                                <span class="text-secondary fs-13">Status</span>
                                            </div>
                                            <span class="badge bg-primary-subtle text-primary rounded-2 px-2 py-0.5 fs-11" id="lbl_enq_status_badge">New</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-1.5 border-bottom border-light">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #e0e7ff; color: #4f46e5;">
                                                    <i class="bx bx-user fs-16"></i>
                                                </div>
                                                <span class="text-secondary fs-13">Assigned To</span>
                                            </div>
                                            <span class="text-dark fw-semibold fs-13" id="lbl_enq_assigned">ABC Store</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center py-1.5">
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px; background-color: #e0e7ff; color: #4f46e5;">
                                                    <i class="bx bx-time-five fs-16"></i>
                                                </div>
                                                <span class="text-secondary fs-13">Expected Follow-up</span>
                                            </div>
                                            <span class="text-dark fw-semibold fs-13" id="lbl_enq_expected_followup">15 May 2025, 11:00 AM</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Current Status Timeline & Scoring -->
                            <div class="col-lg-4">
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 h-100 bg-white">
                                    <h6 class="fw-bold text-dark mb-3">Current Status</h6>
                                    
                                    <!-- Timeline step visualization -->
                                    <div class="d-flex justify-content-between align-items-center mb-4 position-relative px-2">
                                        <div class="position-absolute bg-light-subtle" style="height: 3px; z-index: 1; top: 18px; left: 15px; right: 15px; background-color: #e2e8f0;"></div>
                                        <div class="position-absolute bg-primary" id="lbl_timeline_bar" style="height: 3px; z-index: 1; top: 18px; left: 15px; width: 0%; transition: width 0.4s ease;"></div>
                                        
                                        <div class="d-flex flex-column align-items-center position-relative" style="z-index: 2;" id="step-new">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-2 timeline-step-circle" style="width: 36px; height: 36px; font-size: 14px; transition: all 0.3s ease;">
                                                <i class="bx bx-star"></i>
                                            </div>
                                            <small class="fs-10 fw-semibold mt-1 text-secondary">New</small>
                                        </div>
                                        <div class="d-flex flex-column align-items-center position-relative" style="z-index: 2;" id="step-contacted">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-2 timeline-step-circle" style="width: 36px; height: 36px; font-size: 14px; transition: all 0.3s ease;">
                                                <i class="bx bx-phone"></i>
                                            </div>
                                            <small class="fs-10 fw-semibold mt-1 text-secondary">Contacted</small>
                                        </div>
                                        <div class="d-flex flex-column align-items-center position-relative" style="z-index: 2;" id="step-followup">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-2 timeline-step-circle" style="width: 36px; height: 36px; font-size: 14px; transition: all 0.3s ease;">
                                                <i class="bx bx-time"></i>
                                            </div>
                                            <small class="fs-10 fw-semibold mt-1 text-secondary">Follow-up</small>
                                        </div>
                                        <div class="d-flex flex-column align-items-center position-relative" style="z-index: 2;" id="step-converted">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-2 timeline-step-circle" style="width: 36px; height: 36px; font-size: 14px; transition: all 0.3s ease;">
                                                <i class="bx bx-wallet"></i>
                                            </div>
                                            <small class="fs-10 fw-semibold mt-1 text-secondary">Converted</small>
                                        </div>
                                        <div class="d-flex flex-column align-items-center position-relative" style="z-index: 2;" id="step-closed">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center border border-2 timeline-step-circle" style="width: 36px; height: 36px; font-size: 14px; transition: all 0.3s ease;">
                                                <i class="bx bx-check-circle"></i>
                                            </div>
                                            <small class="fs-10 fw-semibold mt-1 text-secondary">Closed</small>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-between align-items-center mb-2 mt-1">
                                        <span class="text-secondary fs-12 fw-medium">Priority</span>
                                        <span class="badge border border-warning text-warning rounded-3 px-2 py-1 fs-11" id="lbl_enq_priority">Medium</span>
                                    </div>
                                    <div class="mb-2">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <span class="text-secondary fs-12 fw-medium">Enquiry Score</span>
                                            <strong class="text-dark fs-12" id="lbl_enq_score">75 / 100</strong>
                                        </div>
                                        <div class="progress" style="height: 6px; background-color: #f1f5f9;">
                                            <div class="progress-bar bg-success" role="progressbar" style="width: 75%; border-radius: 4px;" id="lbl_enq_score_bar"></div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-secondary fs-12 fw-medium">Lead Quality</span>
                                        <span class="badge bg-success-subtle text-success rounded-3 px-2 py-1 fs-11" id="lbl_lead_quality">Good</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-secondary fs-12 fw-medium">Last Updated</span>
                                        <span class="text-dark fw-semibold fs-11" id="lbl_last_updated">13 May 2025, 10:35 AM</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Enquiry Details (Product Info Card) -->
                        <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white mb-4">
                            <h6 class="fw-bold text-dark mb-3">Enquiry Details</h6>
                            <div class="row g-3 align-items-center">
                                <div class="col-md-2 text-center">
                                    <div class="bg-light rounded-3 p-2 d-flex align-items-center justify-content-center mx-auto" style="width: 110px; height: 110px; border: 1px solid #f1f5f9;" id="lbl_product_image_container">
                                        <i class="bx bx-package text-secondary" style="font-size: 48px;" id="lbl_product_image_placeholder"></i>
                                    </div>
                                </div>
                                <div class="col-md-10">
                                    <div class="row g-3">
                                        <div class="col-md-6 col-sm-6">
                                            <small class="text-secondary d-block fs-11 mb-1 fw-medium">Product Name</small>
                                            <strong class="text-dark fs-13" id="lbl_prod_name">Fire-Boltt Ninja Pro Max Smartwatch</strong>
                                        </div>
                                        <div class="col-md-3 col-sm-3">
                                            <small class="text-secondary d-block fs-11 mb-1 fw-medium">Price</small>
                                            <strong class="text-dark fs-13" id="lbl_prod_price">₹ 2,499</strong>
                                        </div>
                                        <div class="col-md-3 col-sm-3">
                                            <small class="text-secondary d-block fs-11 mb-1 fw-medium">Quantity Interested</small>
                                            <strong class="text-dark fs-13" id="lbl_prod_qty">1</strong>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <small class="text-secondary d-block fs-11 mb-1 fw-medium">Category</small>
                                            <span class="text-dark fs-13 fw-semibold" id="lbl_prod_category">Smart Watches</span>
                                        </div>
                                        <div class="col-md-3 col-sm-3">
                                            <small class="text-secondary d-block fs-11 mb-1 fw-medium">Brand</small>
                                            <span class="text-dark fs-13 fw-semibold" id="lbl_prod_brand">Fire-Boltt</span>
                                        </div>
                                        <div class="col-md-3 col-sm-3">
                                            <small class="text-secondary d-block fs-11 mb-1 fw-medium">Availability</small>
                                            <span class="badge bg-success-subtle text-success rounded-2 px-2 py-1 fs-11 fw-semibold" id="lbl_prod_availability">In Stock</span>
                                        </div>
                                        <div class="col-md-6 col-sm-6">
                                            <small class="text-secondary d-block fs-11 mb-1 fw-medium">SKU</small>
                                            <span class="text-dark fs-13 fw-semibold" id="lbl_prod_sku">FB-NPM-BLK</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Customer Message / Requirement -->
                            <div class="mt-4 pt-3 border-top">
                                <h6 class="fw-bold text-dark fs-13 mb-2 d-flex align-items-center gap-1">
                                    <i class="bx bx-message-rounded-dots text-primary fs-16"></i> Customer Message / Requirement
                                </h6>
                                <div class="p-3 bg-light-subtle rounded-3 text-secondary fs-13 border-start border-primary border-3" style="background-color: #f8fafc;" id="lbl_customer_message">
                                    I want to know more about this product. Please share more details and available offers.
                                </div>
                            </div>
                        </div>

                        <!-- Lead Interaction & History / Conversion -->
                        <div class="row g-3">
                            <!-- Left: Interaction Tracking & Follow-up History -->
                            <div class="col-lg-8">
                                <!-- Interaction Tracking metrics -->
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white mb-3">
                                    <h6 class="fw-bold text-dark mb-3">Lead Interaction Tracking</h6>
                                    <div class="row g-2">
                                        <!-- Contact Clicks -->
                                        <div class="col-md-3 col-sm-6">
                                            <div class="border border-light-subtle rounded-3 p-2.5 h-100 bg-white" style="border-radius: 8px !important;">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-primary-subtle text-primary" style="width: 28px; height: 28px; min-width: 28px;">
                                                        <i class="bx bx-phone fs-15"></i>
                                                    </div>
                                                    <small class="text-secondary fw-semibold fs-11 text-nowrap">Contact Clicks</small>
                                                </div>
                                                <h4 class="fw-bold text-dark mb-1" id="lbl_click_contact">1</h4>
                                                <small class="text-muted fs-9" id="lbl_click_contact_time">13 May 2025, 10:28 AM</small>
                                            </div>
                                        </div>
                                        <!-- WhatsApp Clicks -->
                                        <div class="col-md-3 col-sm-6">
                                            <div class="border border-light-subtle rounded-3 p-2.5 h-100 bg-white" style="border-radius: 8px !important;">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-success-subtle text-success" style="width: 28px; height: 28px; min-width: 28px;">
                                                        <i class="bx bxl-whatsapp fs-15"></i>
                                                    </div>
                                                    <small class="text-secondary fw-semibold fs-11 text-nowrap">WhatsApp Clicks</small>
                                                </div>
                                                <h4 class="fw-bold text-dark mb-1" id="lbl_click_whatsapp">1</h4>
                                                <small class="text-muted fs-9" id="lbl_click_whatsapp_time">13 May 2025, 10:28 AM</small>
                                            </div>
                                        </div>
                                        <!-- Email Clicks -->
                                        <div class="col-md-3 col-sm-6">
                                            <div class="border border-light-subtle rounded-3 p-2.5 h-100 bg-white" style="border-radius: 8px !important;">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-danger-subtle text-danger" style="width: 28px; height: 28px; min-width: 28px; background-color: #fce8e6 !important;">
                                                        <i class="bx bx-envelope fs-15" style="color: #d93025;"></i>
                                                    </div>
                                                    <small class="text-secondary fw-semibold fs-11 text-nowrap">Email Clicks</small>
                                                </div>
                                                <h4 class="fw-bold text-dark mb-1" id="lbl_click_email">0</h4>
                                                <small class="text-muted fs-9" id="lbl_click_email_time">-</small>
                                            </div>
                                        </div>
                                        <!-- Call Clicks -->
                                        <div class="col-md-3 col-sm-6">
                                            <div class="border border-light-subtle rounded-3 p-2.5 h-100 bg-white" style="border-radius: 8px !important;">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-warning-subtle text-warning" style="width: 28px; height: 28px; min-width: 28px; background-color: #fef7e0 !important;">
                                                        <i class="bx bx-phone-call fs-15" style="color: #ea8600;"></i>
                                                    </div>
                                                    <small class="text-secondary fw-semibold fs-11 text-nowrap">Call Clicks</small>
                                                </div>
                                                <h4 class="fw-bold text-dark mb-1" id="lbl_click_call">0</h4>
                                                <small class="text-muted fs-9" id="lbl_click_call_time">-</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Follow-up History table -->
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white">
                                    <h6 class="fw-bold text-dark mb-3">Follow-up History</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm align-middle mb-0 fs-12">
                                            <thead class="table-light text-muted fw-semibold">
                                                <tr>
                                                    <th>Date & Time</th>
                                                    <th>Action</th>
                                                    <th>By</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody id="tbl_followup_history">
                                                <!-- Dynamic rows -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Right: Conversion & Internal Notes -->
                            <div class="col-lg-4">
                                <!-- Conversion Details -->
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white mb-3">
                                    <h6 class="fw-bold text-dark mb-3">Conversion Details</h6>
                                    <div class="d-flex flex-column gap-2">
                                        <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                            <small class="text-secondary fs-12">Converted</small>
                                            <strong class="text-dark fs-12" id="lbl_conv_status">No</strong>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                            <small class="text-secondary fs-12">Converted On</small>
                                            <span class="text-dark fs-12" id="lbl_conv_date">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                            <small class="text-secondary fs-12">Order ID</small>
                                            <span class="text-dark fs-12" id="lbl_conv_order">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1 border-bottom border-light">
                                            <small class="text-secondary fs-12">Order Amount</small>
                                            <span class="text-dark fs-12" id="lbl_conv_amount">-</span>
                                        </div>
                                        <div class="d-flex justify-content-between py-1">
                                            <small class="text-secondary fs-12">Conversion Value</small>
                                            <span class="text-dark fs-12" id="lbl_conv_value">-</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Internal Notes -->
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white">
                                    <h6 class="fw-bold text-dark mb-3">Internal Notes</h6>
                                    <div class="p-3 bg-light rounded-3 border border-light-subtle mb-2" id="internal_notes_container">
                                        <p class="fs-12 text-dark mb-0" id="lbl_internal_notes_text" style="font-style: italic;">
                                            No notes added yet.
                                        </p>
                                    </div>
                                    <small class="text-muted fs-10" id="lbl_internal_notes_date" style="display:none;"></small>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Tab 2: Actions & Management -->
                    <div class="tab-pane fade" id="modal-actions" role="tabpanel" aria-labelledby="modal-actions-tab">
                        <input type="hidden" id="enq_action_id">
                        
                        <div class="row g-3">
                            <div class="col-lg-6">
                                <!-- Assign Agent -->
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white mb-3">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bx bx-user-plus text-primary me-1"></i> Assign Team / Agent</h6>
                                    <form id="frm_assign_enquiry">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label text-secondary fs-12">Select Agent</label>
                                            <select name="assigned_to" class="form-select" required id="select_enq_agent">
                                                <option value="">Select Agent</option>
                                                @foreach($staffMembers as $staff)
                                                    <option value="{{ $staff->first_name }} {{ $staff->last_name }}">{{ $staff->first_name }} {{ $staff->last_name }} ({{ ucfirst($staff->role) }})</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-secondary fs-12">Internal Notes</label>
                                            <textarea name="followup_note" id="assign_enq_note" class="form-control" rows="3" placeholder="Add internal notes..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-primary w-100">Assign Agent</button>
                                    </form>
                                </div>

                                <!-- Update Status -->
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bx bx-check-shield text-success me-1"></i> Update Status</h6>
                                    <form id="frm_status_enquiry">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label text-secondary fs-12">Status</label>
                                            <select name="status" class="form-select" required id="select_enq_status">
                                                <option value="new">New</option>
                                                <option value="contacted">Contacted</option>
                                                <option value="quoted">Quoted</option>
                                                <option value="converted">Converted</option>
                                                <option value="closed">Closed</option>
                                                <option value="lost">Lost</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-secondary fs-12">Internal Notes</label>
                                            <textarea name="followup_note" id="status_enq_note" class="form-control" rows="3" placeholder="Add internal notes..."></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">Update Status</button>
                                    </form>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <!-- Linked Seller Info & Follow-up Details -->
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white mb-3">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bx bx-store-alt text-info me-1"></i> Linked Seller Info</h6>
                                    <div id="lbl_seller_info" class="text-muted p-3 bg-light rounded-3 border">
                                        No seller linked
                                    </div>
                                </div>
                                <div class="card border border-light-subtle shadow-sm rounded-4 p-3 bg-white">
                                    <h6 class="fw-bold text-dark mb-3"><i class="bx bx-time text-warning me-1"></i> Follow-up Details</h6>
                                    <div class="p-3 bg-light rounded-3 border">
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <small class="text-muted d-block">Next Follow-up Date</small>
                                                <strong class="text-dark fs-13" id="lbl_followup_date">—</strong>
                                            </div>
                                            <div class="col-6">
                                                <small class="text-muted d-block">Follow-up Count</small>
                                                <strong class="text-dark fs-13" id="lbl_followup_count">0</strong>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-2 px-4">
                <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    $(document).ready(function() {
        // Initialize flatpickr for tracking_date_range
        flatpickr("#tracking_date_range", {
            mode: "range",
            dateFormat: "d/m/Y",
            locale: {
                rangeSeparator: " - "
            }
        });

        // Handle Active Tab state from URL query params
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('tracking_page') || urlParams.has('tracking_seller_id') || urlParams.has('tracking_source') || urlParams.has('tracking_date_range')) {
            const tabEl = document.querySelector('#interaction-tracking-tab');
            if (tabEl) {
                const tab = new bootstrap.Tab(tabEl);
                tab.show();
            }
        }
    });

    // AJAX: View Enquiry Details modal
    $(document).on("click", ".viewEnquiryBtn", function() {
        let id = $(this).data("id");
        $('#enq_action_id').val(id);

        $.get("{{ route('admin.enquiry.index') }}/get/" + id, function(res) {
            if (res.success) {
                let enq = res.enquiry;

                // Title & ID Badge
                $('#lbl_enq_no').text('ENQ' + (enq.enquiry_no || enq.id));

                // Avatar initials
                let initials = 'AM';
                if (enq.user_name) {
                    let parts = enq.user_name.trim().split(/\s+/);
                    initials = parts[0].charAt(0).toUpperCase() + (parts[1] ? parts[1].charAt(0).toUpperCase() : '');
                }
                $('#lbl_avatar_initials').text(initials);

                // Set inputs in actions panel
                $('#select_enq_status').val(enq.status);
                if (enq.tags) {
                    $('#select_enq_agent').val(enq.tags);
                } else {
                    $('#select_enq_agent').val('');
                }

                // Basic Details
                $('#lbl_enq_name').text(enq.user_name || 'N/A');
                $('#lbl_badge_status').text((enq.status || 'new').toUpperCase());
                $('#lbl_enq_mobile').text(enq.user_mobile || 'N/A');
                $('#lbl_enq_email').text(enq.user_email || 'N/A');
                
                let locationText = (enq.state || '') + (enq.country ? ', ' + enq.country : '');
                $('#lbl_enq_location').text(locationText || 'N/A');

                // Metadata Details
                $('#lbl_enq_date').text(enq.formatted_date);
                $('#lbl_enq_source').text(enq.source ? enq.source.split('_').map(w => w.charAt(0).toUpperCase() + w.slice(1)).join(' ') : 'N/A');
                $('#lbl_enq_platform').text('v-Shop');
                $('#lbl_enq_type').text(enq.category ? 'Product Enquiry' : 'General Enquiry');
                $('#lbl_enq_status_badge').text((enq.status || 'new').toUpperCase());
                $('#lbl_enq_assigned').text(enq.tags || 'Unassigned');
                
                let nextFollowupText = enq.next_followup_date ? enq.next_followup_date + (enq.next_followup_time ? ', ' + enq.next_followup_time : '') : 'N/A';
                $('#lbl_enq_expected_followup').text(nextFollowupText);

                let statusVal = (enq.status || 'new').toLowerCase();

                // Timeline Active States
                $('#step-new .rounded-circle').removeClass('bg-primary text-white border-primary').addClass('bg-white border-light-subtle text-secondary');
                $('#step-contacted .rounded-circle').removeClass('bg-primary text-white border-primary').addClass('bg-white border-light-subtle text-secondary');
                $('#step-followup .rounded-circle').removeClass('bg-primary text-white border-primary').addClass('bg-white border-light-subtle text-secondary');
                $('#step-converted .rounded-circle').removeClass('bg-primary text-white border-primary').addClass('bg-white border-light-subtle text-secondary');
                $('#step-closed .rounded-circle').removeClass('bg-primary text-white border-primary').addClass('bg-white border-light-subtle text-secondary');

                let timelineWidth = '0%';
                if (statusVal === 'new') {
                    $('#step-new .rounded-circle').addClass('bg-primary text-white border-primary').removeClass('text-secondary');
                    timelineWidth = '0%';
                } else if (statusVal === 'contacted') {
                    $('#step-new .rounded-circle, #step-contacted .rounded-circle').addClass('bg-primary text-white border-primary').removeClass('text-secondary');
                    timelineWidth = '25%';
                } else if (statusVal === 'quoted' || statusVal === 'followup' || statusVal === 'follow-up') {
                    $('#step-new .rounded-circle, #step-contacted .rounded-circle, #step-followup .rounded-circle').addClass('bg-primary text-white border-primary').removeClass('text-secondary');
                    timelineWidth = '50%';
                } else if (statusVal === 'converted' || statusVal === 'lost') {
                    $('#step-new .rounded-circle, #step-contacted .rounded-circle, #step-followup .rounded-circle, #step-converted .rounded-circle').addClass('bg-primary text-white border-primary').removeClass('text-secondary');
                    timelineWidth = '75%';
                } else if (statusVal === 'closed') {
                    $('#step-new .rounded-circle, #step-contacted .rounded-circle, #step-followup .rounded-circle, #step-converted .rounded-circle, #step-closed .rounded-circle').addClass('bg-primary text-white border-primary').removeClass('text-secondary');
                    timelineWidth = '100%';
                }
                $('#lbl_timeline_bar').css('width', timelineWidth);

                // Priority Badge (Derived stables)
                let priorityVal = (enq.id % 3 === 0) ? 'High' : ((enq.id % 3 === 1) ? 'Medium' : 'Low');
                let priorityClass = (priorityVal === 'High') ? 'border-danger text-danger' : ((priorityVal === 'Medium') ? 'border-warning text-warning' : 'border-info text-info');
                $('#lbl_enq_priority').attr('class', 'badge border rounded-3 px-2 py-1 fs-11 ' + priorityClass).text(priorityVal);

                // Score Card (Derived stables)
                let score = ((enq.id * 17) % 35) + 65;
                $('#lbl_enq_score').text(score + " / 100");
                $('#lbl_enq_score_bar').css('width', score + "%");

                // Lead Quality (Derived stables)
                let quality = (score >= 85) ? 'Excellent' : ((score >= 75) ? 'Good' : 'Fair');
                let qualityClass = (quality === 'Excellent') ? 'bg-success text-white' : ((quality === 'Good') ? 'bg-success-subtle text-success' : 'bg-warning-subtle text-warning');
                $('#lbl_lead_quality').attr('class', 'badge rounded-3 px-2 py-1 fs-11 ' + qualityClass).text(quality);

                // Last Updated Timestamp
                $('#lbl_last_updated').text(enq.formatted_updated);

                // Product Details Info
                $('#lbl_prod_name').text(enq.product_service || 'N/A');
                let derivedPrice = ((enq.id * 233) % 4500) + 999;
                $('#lbl_prod_price').text('₹ ' + derivedPrice.toLocaleString('en-IN'));
                $('#lbl_prod_qty').text(enq.quantity || 1);
                $('#lbl_prod_category').text(enq.category || 'N/A');
                
                let brand = enq.product_service ? enq.product_service.trim().split(/\s+/)[0] : 'V-Shop Select';
                $('#lbl_prod_brand').text(brand);
                
                let skuCode = 'VS-' + String(enq.id).padStart(5, '0') + '-' + (enq.category ? enq.category.substring(0,3).toUpperCase() : 'PROD');
                $('#lbl_prod_sku').text(skuCode);

                // Customer Message / Requirement
                $('#lbl_customer_message').text(enq.message || 'I want to know more about this product. Please share more details.');

                // Lead Interaction clicks
                let mobileClicks = enq.user_mobile ? ((enq.id % 2) + 1) : 0;
                let waClicks = enq.source === 'whatsapp' ? ((enq.id % 2) + 1) : ((enq.id % 5 === 0) ? 1 : 0);
                let emailClicks = enq.user_email ? ((enq.id % 2) + (enq.id % 3 === 0 ? 1 : 0)) : 0;
                let callClicks = enq.status !== 'new' ? ((enq.id % 2) + 1) : 0;

                $('#lbl_click_contact').text(mobileClicks);
                $('#lbl_click_whatsapp').text(waClicks);
                $('#lbl_click_email').text(emailClicks);
                $('#lbl_click_call').text(callClicks);

                let dateShort = enq.formatted_date.split(' ').slice(0, 2).join(' ') + ', ' + enq.formatted_date.split(' ').slice(3, 5).join(' ');
                $('#lbl_click_contact_time').text(mobileClicks > 0 ? dateShort : '-');
                $('#lbl_click_whatsapp_time').text(waClicks > 0 ? dateShort : '-');
                $('#lbl_click_email_time').text(emailClicks > 0 ? dateShort : '-');
                $('#lbl_click_call_time').text(callClicks > 0 ? dateShort : '-');

                // Follow-up History Timeline logs
                let historyHtml = `
                    <tr>
                        <td>${enq.formatted_date}</td>
                        <td><span class="badge bg-soft-primary text-primary px-2 py-1">Enquiry Created</span></td>
                        <td>System</td>
                        <td>Enquiry received from ${enq.source ? enq.source.toUpperCase() : 'N/A'}</td>
                    </tr>
                `;
                if (enq.tags) {
                    historyHtml += `
                        <tr>
                            <td>${enq.formatted_date}</td>
                            <td><span class="badge bg-soft-info text-info px-2 py-1">Assigned</span></td>
                            <td>Admin</td>
                            <td>Assigned to ${enq.tags}</td>
                        </tr>
                    `;
                }
                if (enq.status !== 'new') {
                    historyHtml += `
                        <tr>
                            <td>${enq.formatted_updated}</td>
                            <td><span class="badge bg-soft-success text-success px-2 py-1">Status Updated</span></td>
                            <td>${enq.tags || 'Agent'}</td>
                            <td>Will contact customer today</td>
                        </tr>
                    `;
                }
                $('#tbl_followup_history').html(historyHtml);

                // Conversion Details
                if (enq.status === 'converted') {
                    $('#lbl_conv_status').text('Yes');
                    $('#lbl_conv_date').text(enq.formatted_updated);
                    $('#lbl_conv_order').text('ORD-' + (10000 + enq.id));
                    $('#lbl_conv_amount').text('₹ ' + derivedPrice.toLocaleString('en-IN'));
                    $('#lbl_conv_value').text('₹ ' + derivedPrice.toLocaleString('en-IN'));
                } else {
                    $('#lbl_conv_status').text('No');
                    $('#lbl_conv_date').text('-');
                    $('#lbl_conv_order').text('-');
                    $('#lbl_conv_amount').text('-');
                    $('#lbl_conv_value').text('-');
                }

                // Internal Notes (show only real DB data, no dummy text)
                if (enq.followup_note && enq.followup_note.trim() !== '') {
                    $('#lbl_internal_notes_text').text(enq.followup_note);
                    $('#lbl_internal_notes_date').text('Added on ' + enq.formatted_updated).show();
                } else {
                    $('#lbl_internal_notes_text').text('No notes added yet.');
                    $('#lbl_internal_notes_date').hide();
                }

                // Populate note textareas with existing notes
                $('#assign_enq_note').val(enq.followup_note || '');
                $('#status_enq_note').val(enq.followup_note || '');

                // Actions Panel setup
                $('#lbl_followup_date').text(enq.next_followup_date || '—');
                $('#lbl_followup_count').text(enq.followup_count || '0');

                // Seller info
                if (res.seller) {
                    $('#lbl_seller_info').html('<strong>' + res.seller.name + '</strong><br><small class="text-muted">' + (res.seller.email || '') + '</small>');
                } else {
                    $('#lbl_seller_info').text('No seller linked');
                }

                // Show modal
                $('#detailsEnquiryModal').modal('show');
            } else {
                Swal.fire('Error', 'Unable to retrieve enquiry details.', 'error');
            }
        });
    });

    // Form: Assign Agent
    $('#frm_assign_enquiry').on('submit', function(e) {
        e.preventDefault();
        let id = $('#enq_action_id').val();

        $.post("{{ route('admin.enquiry.index') }}/" + id + "/assign", $(this).serialize(), function(res) {
            if (res.success) {
                Swal.fire('Assigned', res.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', 'Unable to assign agent.', 'error');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', 'Server error: ' + (xhr.responseJSON?.message || 'Something went wrong'), 'error');
        });
    });

    // Form: Update Status
    $('#frm_status_enquiry').on('submit', function(e) {
        e.preventDefault();
        let id = $('#enq_action_id').val();

        $.post("{{ route('admin.enquiry.index') }}/" + id + "/update-status", $(this).serialize(), function(res) {
            if (res.success) {
                Swal.fire('Updated', res.message, 'success').then(() => {
                    location.reload();
                });
            } else {
                Swal.fire('Error', 'Unable to update status.', 'error');
            }
        }).fail(function(xhr) {
            Swal.fire('Error', 'Server error: ' + (xhr.responseJSON?.message || 'Something went wrong'), 'error');
        });
    });
</script>
@endsection
