@extends('layouts.vertical', ['title' => 'Merge Duplicate Customers'])

@section('content')
<!-- Select2 CSS CDN for high-end searchable dropdowns -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* Modern, Premium Minimalist Styling */
.content {
    padding-top: 10px !important;
}
.container-fluid {
    padding-top: 0 !important;
}

/* Steps Process Guide */
.step-number {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background-color: #6c757d;
    color: #ffffff;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    margin-right: 8px;
}
.step-card {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    transition: all 0.2s ease;
}
.step-card:hover {
    background-color: #ffffff;
    border-color: #dee2e6;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
}

/* Selective elegant backgrounds (Not too colorful) */
.source-card-subtle {
    background-color: #fafbfc;
    border: 1px solid #e2e8f0 !important;
}
.source-card-subtle .card-header {
    background-color: #f1f5f9;
    border-bottom: 1px solid #e2e8f0;
    color: #475569;
}
.target-card-subtle {
    background-color: #fcfdfe;
    border: 1px solid #cbd5e1 !important;
}
.target-card-subtle .card-header {
    background-color: #f8fafc;
    border-bottom: 1px solid #cbd5e1;
    color: #1e293b;
}

.records-transfer-badge {
    background-color: #f1f5f9;
    border: 1px solid #e2e8f0;
    color: #334155;
    font-weight: 600;
    border-radius: 8px;
}

.fs-12 { font-size: 12px; }
.fs-13 { font-size: 13px; }
.fs-11 { font-size: 11px; }
.rounded-4 { border-radius: 12px !important; }

/* Customizing Select2 to match premium theme */
.select2-container--default .select2-selection--single {
    height: 40px !important;
    line-height: 40px !important;
    border: 1px solid #dee2e6 !important;
    border-radius: 8px !important;
    padding-top: 5px !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
}
.select2-dropdown {
    border: 1px solid #dee2e6 !important;
    border-radius: 8px !important;
    box-shadow: 0 4px 15px rgba(0,0,0,0.05);
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-2 pb-2 mb-3 border-bottom">
        <h4 class="fw-bold text-dark mb-0">🔗 Merge Duplicate Customers</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Merge Duplicates</li>
            </ol>
        </nav>
    </div>

    <!-- Step-by-Step Flow Guide (Highly User Friendly) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-3"><i class="fas fa-question-circle text-muted me-1"></i> How It Works</h5>
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 rounded-3 step-card h-100">
                        <div class="d-flex align-items-center mb-2">
                            <span class="step-number" style="background-color: #6366f1;">1</span>
                            <h6 class="fw-bold text-dark mb-0">Select Accounts</h6>
                        </div>
                        <p class="mb-0 text-muted fs-13">
                            Choose the duplicate account you want to remove (Source) and the primary account you want to keep (Target).
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-3 step-card h-100">
                        <div class="d-flex align-items-center mb-2">
                            <span class="step-number" style="background-color: #0ea5e9;">2</span>
                            <h6 class="fw-bold text-dark mb-0">Review Data Flow</h6>
                        </div>
                        <p class="mb-0 text-muted fs-13">
                            Inspect the comparison panel below to see exactly how many orders, addresses, and reviews will be moved.
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 rounded-3 step-card h-100">
                        <div class="d-flex align-items-center mb-2">
                            <span class="step-number" style="background-color: #10b981;">3</span>
                            <h6 class="fw-bold text-dark mb-0">Execute Merge</h6>
                        </div>
                        <p class="mb-0 text-muted fs-13">
                            Confirm the operation. The records transfer instantly, and the duplicate source is permanently deleted.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Selection Controls Card (Step 1) -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-dark mb-4"><span class="step-number" style="background-color: #6366f1; width:22px; height:22px; font-size:11px;">1</span> Select Duplicate Customer Profiles</h5>
            <form method="GET" action="{{ route('admin.customers.merge') }}" id="selectionForm" class="row g-3 align-items-end">
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary fs-12 uppercase mb-1">DUPLICATE ACCOUNT (WILL BE DELETED)</label>
                    <select name="source_id" id="source_id" class="form-select select2" onchange="this.form.submit()">
                        <option value="">-- Search & Choose Duplicate Customer --</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}" {{ request('source_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->name }} ({{ $c->email }})
                            </option>
                        @endforeach
                    </select>
                    <span class="text-muted fs-11 mt-1.5 d-block"><i class="fas fa-info-circle"></i> This profile will be deleted after all of its historical data is moved.</span>
                </div>
                <div class="col-md-2 d-flex align-items-center justify-content-center pb-2">
                    <div class="rounded-circle bg-light p-2 border d-none d-md-flex align-items-center justify-content-center shadow-sm" style="width: 44px; height: 44px;">
                        <i class="fas fa-arrow-right text-muted"></i>
                    </div>
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold text-secondary fs-12 uppercase mb-1">PRIMARY ACCOUNT (WILL BE KEPT)</label>
                    <select name="target_id" id="target_id" class="form-select select2" onchange="this.form.submit()">
                        <option value="">-- Search & Choose Primary Customer --</option>
                        @foreach($customers as $c)
                            @if($c->id != request('source_id'))
                                <option value="{{ $c->id }}" {{ request('target_id') == $c->id ? 'selected' : '' }}>
                                    {{ $c->name }} ({{ $c->email }})
                                </option>
                            @endif
                        @endforeach
                    </select>
                    <span class="text-muted fs-11 mt-1.5 d-block"><i class="fas fa-check-circle text-success"></i> This profile will absorb all the source data and be preserved.</span>
                </div>
            </form>
        </div>
    </div>

    @if($source && $target)
        <!-- Step 2: Compare Profiles & Review Data Flow -->
        <div class="row mb-4">
            <!-- Source Card -->
            <div class="col-md-5">
                <div class="card source-card-subtle shadow-sm rounded-4 h-100">
                    <div class="card-header py-2.5">
                        <h6 class="card-title mb-0 fw-bold"><i class="fas fa-trash-alt me-1.5 text-danger"></i> Duplicate Profile (To Be Deleted)</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle bg-light text-secondary p-3 border mx-0 me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <span class="fw-bold fs-16">{{ strtoupper(substr($source->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $source->name }}</h5>
                                <span class="badge bg-secondary fs-10 px-2 py-1 mt-1">ID: #{{ $source->id }}</span>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0 py-2.5">
                                <span class="text-muted fs-13"><i class="far fa-envelope me-1.5"></i> Email</span>
                                <span class="fw-semibold text-dark fs-13">{{ $source->email }}</span>
                            </li>
                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0 py-2.5">
                                <span class="text-muted fs-13"><i class="fas fa-phone me-1.5"></i> Phone</span>
                                <span class="fw-semibold text-dark fs-13">{{ $source->phone ?? 'Not Set' }}</span>
                            </li>
                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0 py-2.5">
                                <span class="text-muted fs-13"><i class="far fa-calendar-alt me-1.5"></i> Registered</span>
                                <span class="fw-semibold text-dark fs-13">{{ $source->created_at->format('M d, Y') }}</span>
                            </li>
                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0 py-2.5">
                                <span class="text-muted fs-13"><i class="fas fa-map-marker-alt me-1.5"></i> City / State</span>
                                <span class="fw-semibold text-dark fs-13">{{ $source->city ?? 'Not Set' }} / {{ $source->state ?? 'Not Set' }}</span>
                            </li>
                        </ul>

                        <!-- Records to Transfer Stats -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-secondary mb-3 fs-12 uppercase"><i class="fas fa-share-square me-1"></i> Data to be Transferred</h6>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="p-2.5 records-transfer-badge text-center">
                                        <h4 class="fw-bold mb-0 text-dark">{{ $source->orders_count }}</h4>
                                        <small class="text-muted d-block uppercase fs-10 fw-semibold">Orders</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2.5 records-transfer-badge text-center">
                                        <h4 class="fw-bold mb-0 text-dark">{{ $source->reviews_count }}</h4>
                                        <small class="text-muted d-block uppercase fs-10 fw-semibold">Reviews</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2.5 records-transfer-badge text-center">
                                        <h4 class="fw-bold mb-0 text-dark">{{ $source->addresses_count }}</h4>
                                        <small class="text-muted d-block uppercase fs-10 fw-semibold">Addresses</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Flow Indicator -->
            <div class="col-md-2 d-flex flex-column align-items-center justify-content-center py-4">
                <i class="fas fa-angle-double-right fa-2x text-muted d-none d-md-block mb-2"></i>
                <i class="fas fa-angle-double-down fa-2x text-muted d-md-none mb-2"></i>
                <span class="badge bg-light text-secondary border px-2 py-1.5 fs-10 uppercase shadow-sm">TRANSFERS TO</span>
            </div>

            <!-- Target Card -->
            <div class="col-md-5">
                <div class="card target-card-subtle shadow-sm rounded-4 h-100">
                    <div class="card-header py-2.5">
                        <h6 class="card-title mb-0 fw-bold"><i class="fas fa-check-circle me-1.5 text-success"></i> Primary Profile (To Be Kept)</h6>
                    </div>
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-4">
                            <div class="rounded-circle bg-light text-dark p-3 border mx-0 me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                <span class="fw-bold fs-16">{{ strtoupper(substr($target->name, 0, 2)) }}</span>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">{{ $target->name }}</h5>
                                <span class="badge bg-dark fs-10 px-2 py-1 mt-1">ID: #{{ $target->id }}</span>
                            </div>
                        </div>

                        <ul class="list-group list-group-flush bg-transparent">
                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0 py-2.5">
                                <span class="text-muted fs-13"><i class="far fa-envelope me-1.5"></i> Email</span>
                                <span class="fw-semibold text-dark fs-13">{{ $target->email }}</span>
                            </li>
                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0 py-2.5">
                                <span class="text-muted fs-13"><i class="fas fa-phone me-1.5"></i> Phone</span>
                                <span class="fw-semibold text-dark fs-13">{{ $target->phone ?? 'Not Set' }}</span>
                            </li>
                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0 py-2.5">
                                <span class="text-muted fs-13"><i class="far fa-calendar-alt me-1.5"></i> Registered</span>
                                <span class="fw-semibold text-dark fs-13">{{ $target->created_at->format('M d, Y') }}</span>
                            </li>
                            <li class="list-group-item bg-transparent d-flex justify-content-between px-0 py-2.5">
                                <span class="text-muted fs-13"><i class="fas fa-map-marker-alt me-1.5"></i> City / State</span>
                                <span class="fw-semibold text-dark fs-13">{{ $target->city ?? 'Not Set' }} / {{ $target->state ?? 'Not Set' }}</span>
                            </li>
                        </ul>

                        <!-- Target Current Stats -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="fw-bold text-secondary mb-3 fs-12 uppercase"><i class="fas fa-database me-1"></i> Current Primary Data</h6>
                            <div class="row g-2">
                                <div class="col-4">
                                    <div class="p-2.5 records-transfer-badge text-center">
                                        <h4 class="fw-bold mb-0 text-dark">{{ $target->orders_count }}</h4>
                                        <small class="text-muted d-block uppercase fs-10 fw-semibold">Orders</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2.5 records-transfer-badge text-center">
                                        <h4 class="fw-bold mb-0 text-dark">{{ $target->reviews_count }}</h4>
                                        <small class="text-muted d-block uppercase fs-10 fw-semibold">Reviews</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-2.5 records-transfer-badge text-center">
                                        <h4 class="fw-bold mb-0 text-dark">{{ $target->addresses_count }}</h4>
                                        <small class="text-muted d-block uppercase fs-10 fw-semibold">Addresses</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Step 3: Action Panel -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold text-dark mb-4"><span class="step-number" style="background-color: #10b981; width:22px; height:22px; font-size:11px;">2</span> Review & Confirm Merge</h5>
                
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="border rounded-3 p-4 bg-light mb-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-exchange-alt me-1.5 text-muted"></i> Data Merger Summary Check</h6>
                            
                            <div class="d-flex align-items-start mb-3">
                                <i class="fas fa-check-circle text-success fs-18 me-2.5 mt-0.5"></i>
                                <div>
                                    <span class="fw-semibold text-dark d-block">Consolidate History</span>
                                    <span class="text-muted fs-13">All <strong>{{ $source->orders_count }} orders</strong>, <strong>{{ $source->reviews_count }} reviews</strong>, and <strong>{{ $source->addresses_count }} addresses</strong> belonging to {{ $source->name }} will transfer to {{ $target->name }}.</span>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start mb-3">
                                <i class="fas fa-check-circle text-success fs-18 me-2.5 mt-0.5"></i>
                                <div>
                                    <span class="fw-semibold text-dark d-block">Preserve Primary Profile</span>
                                    <span class="text-muted fs-13">The primary profile ({{ $target->name }}) will absorb empty profile attributes (phone, address, etc.) where applicable.</span>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-start border-top pt-3">
                                <i class="fas fa-minus-circle text-danger fs-18 me-2.5 mt-0.5"></i>
                                <div>
                                    <span class="fw-semibold text-danger d-block">Permanently Delete Duplicate</span>
                                    <span class="text-muted fs-13">The duplicate profile ({{ $source->name }}) will be permanently deleted from the database. <strong>This is irreversible.</strong></span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center">
                            <form method="POST" action="{{ route('admin.customers.merge.submit') }}" onsubmit="return confirmMerge()">
                                @csrf
                                <input type="hidden" name="source_id" value="{{ $source->id }}">
                                <input type="hidden" name="target_id" value="{{ $target->id }}">
                                
                                <button type="submit" class="btn btn-dark btn-lg px-5 shadow-sm rounded-3 py-2.5 fs-14 fw-bold">
                                    <i class="fas fa-compress-alt me-2"></i> Confirm & Complete Merge
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @elseif(request('source_id') || request('target_id'))
        <div class="card border-0 shadow-sm rounded-4 py-5 text-center">
            <div class="card-body">
                <i class="fas fa-users-cog fa-3x text-muted mb-3"></i>
                <h5 class="text-dark fw-bold">Select both customer profiles to proceed</h5>
                <p class="text-muted fs-13">Please choose both the duplicate profile and the primary profile to load the comparison panel.</p>
            </div>
        </div>
    @endif
</div>

<!-- Select2 JavaScript CDN & Custom Initialization -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
$(document).ready(function() {
    // Initialize premium Select2 on the dropdowns for easy live searching
    $('.select2').select2({
        width: '100%'
    });
});

function confirmMerge() {
    return confirm("⚠️ WARNING: This will merge all selected source data into target and completely delete the source account. This action cannot be undone.\n\nAre you absolutely sure you want to proceed?");
}
</script>
@endsection
