@extends('layouts.vertical', ['title' => 'Data Validation: Address Audit'])

@section('content')
<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
    }
    .btn-primary, .btn-warning, .btn-danger, .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-secondary, .btn-outline-info {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .fs-10 { font-size: 10px; }
    .fs-13 { font-size: 13px; }
    .fs-20 { font-size: 20px; }
    .rounded-4 { border-radius: 12px !important; }
    
    /* Custom Pagination Styles */
    .pagination {
        margin-bottom: 0;
        flex-wrap: wrap;
    }
    .pagination .page-link {
        color: #004392;
        border: 1px solid #dee2e6;
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
        transition: all 0.2s ease;
    }
    .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: #004392;
        transform: translateY(-1px);
    }
    .pagination .page-item.active .page-link {
        background: linear-gradient(135deg, #004392 0%, #2c7fc3 100%);
        border-color: #004392;
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        cursor: auto;
        background-color: #fff;
        border-color: #dee2e6;
    }
    .pagination .page-item:first-child .page-link {
        border-top-left-radius: 0.375rem;
        border-bottom-left-radius: 0.375rem;
    }
    .pagination .page-item:last-child .page-link {
        border-top-right-radius: 0.375rem;
        border-bottom-right-radius: 0.375rem;
    }
    
    /* Reset button */
    .reset-btn {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
        padding: 7px 7px;
        border-radius: 5px;
    }
    .reset-btn:hover {
        color: #fff;
        background: #4a9fd4 !important;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h4 class="fw-bold text-dark mb-0">Address Validation</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item">Validation</li>
                <li class="breadcrumb-item active" aria-current="page">Address Audit</li>
            </ol>
        </nav>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.customers.validation.address') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-dark mb-1">
                        <i class="fas fa-search me-1"></i> Search
                    </label>
                    <input type="text" name="search" class="form-control form-control-sm border-0 bg-light rounded-3" 
                           placeholder="Name, Email, Phone, City, Pincode..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-dark mb-1">
                        <i class="fas fa-tag me-1"></i> Address Type
                    </label>
                    <select name="address_type" class="form-select form-select-sm border-0 bg-light rounded-3">
                        <option value="">All Types</option>
                        <option value="billing" {{ request('address_type') == 'billing' ? 'selected' : '' }}>Billing</option>
                        <option value="delivery" {{ request('address_type') == 'delivery' ? 'selected' : '' }}>Delivery</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-dark mb-1">
                        <i class="fas fa-filter me-1"></i> Filter Issues
                    </label>
                    <select name="filter" class="form-select form-select-sm border-0 bg-light rounded-3">
                        <option value="">All Addresses</option>
                        <option value="no_phone" {{ request('filter') == 'no_phone' ? 'selected' : '' }}>Missing Phone</option>
                        <option value="no_address" {{ request('filter') == 'no_address' ? 'selected' : '' }}>Missing Address Line</option>
                        <option value="no_pincode" {{ request('filter') == 'no_pincode' ? 'selected' : '' }}>Missing Pincode</option>
                        <option value="no_city" {{ request('filter') == 'no_city' ? 'selected' : '' }}>Missing City</option>
                        <option value="no_email" {{ request('filter') == 'no_email' ? 'selected' : '' }}>Missing Email</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-4" style="background: linear-gradient(135deg, #004392 0%, #2c7fc3 100%); border: none;">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.customers.validation.address') }}" class="btn btn-secondary btn-sm px-3 reset-btn">
                            <i class="fas fa-undo-alt me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>




    <!-- Address Validation Table Card -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light py-3">
                        <tr>
                            <th class="ps-4">Address Details</th>
                            <th>Contact Information</th>
                            <th>Address Status</th>
                            <th>Address Type</th>
                            <th class="pe-4 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($addresses as $address)
                            @php
                                $missingPhone = empty($address->phone);
                                $missingPincode = empty($address->pincode);
                                $missingAddress = empty($address->address_line1);
                                $missingCity = empty($address->city);
                                $missingEmail = empty($address->email);
                                $invalidPincode = !empty($address->pincode) && !preg_match('/^[0-9]{6}$/', $address->pincode);
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-start">
                                        <div class="rounded-circle bg-light p-2 border me-3 d-flex align-items-center justify-content-center text-primary" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="fas fa-map-marker-alt"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $address->name ?? 'N/A' }}</h6>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-building me-1"></i> 
                                                {{ $address->address_line1 ?: 'No address line' }}
                                                @if($address->address_line2), {{ $address->address_line2 }} @endif
                                            </small>
                                            <small class="text-muted d-block">
                                                <i class="fas fa-city me-1"></i> 
                                                {{ $address->city ?: 'No city' }}, 
                                                {{ $address->state ?: 'No state' }}
                                            </small>
                                            <small class="text-muted">
                                                <i class="fas fa-map-pin me-1"></i> 
                                                Pincode: <strong>{{ $address->pincode ?: 'Not set' }}</strong>
                                                @if($invalidPincode)
                                                    <span class="text-danger">(Invalid - should be 6 digits)</span>
                                                @endif
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-globe me-1"></i> {{ $address->country ?: 'No country' }}
                                            </small>
                                        </div>
                                    </div>
                                 </td>
                                
                                <td>
                                    <div>
                                        @if(!empty($address->email))
                                            <small class="text-dark d-block">
                                                <i class="fas fa-envelope me-1"></i> {{ $address->email }}
                                            </small>
                                        @else
                                            <small class="text-danger d-block">
                                                <i class="fas fa-times-circle me-1"></i> No email
                                            </small>
                                        @endif
                                        
                                        @if(!empty($address->phone))
                                            <small class="text-dark d-block">
                                                <i class="fas fa-phone me-1"></i> {{ $address->phone }}
                                            </small>
                                        @else
                                            <small class="text-danger d-block">
                                                <i class="fas fa-times-circle me-1"></i> No phone
                                            </small>
                                        @endif
                                        
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i> Customer ID: #{{ $address->customer_id }}
                                        </small>
                                        
                                        @if($address->customer)
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-store me-1"></i> Customer: {{ $address->customer->name ?? 'N/A' }}
                                            </small>
                                        @endif
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="d-inline-flex flex-wrap gap-1">
                                        @if($missingPhone)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-times-circle me-1"></i> No Phone
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-check-circle me-1"></i> Phone Set
                                            </span>
                                        @endif

                                        @if($missingPincode)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-times-circle me-1"></i> No Pincode
                                            </span>
                                        @elseif($invalidPincode)
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-exclamation-triangle me-1"></i> Invalid Pincode
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-check-circle me-1"></i> Pincode Valid
                                            </span>
                                        @endif

                                        @if($missingAddress)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-times-circle me-1"></i> No Address Line
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-check-circle me-1"></i> Address Set
                                            </span>
                                        @endif

                                        @if($missingCity)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-times-circle me-1"></i> No City
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-check-circle me-1"></i> City Set
                                            </span>
                                        @endif
                                        
                                        @if($missingEmail)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-times-circle me-1"></i> No Email
                                            </span>
                                        @else
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 py-1 px-2 fs-10">
                                                <i class="fas fa-check-circle me-1"></i> Email Set
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                
                                <td class="text-center">
                                    @if($address->type == 'billing')
                                        <span class="badge bg-primary py-1.5 px-3 rounded-pill">
                                            <i class="fas fa-credit-card me-1"></i> Billing
                                        </span>
                                    @else
                                        <span class="badge bg-info py-1.5 px-3 rounded-pill">
                                            <i class="fas fa-truck me-1"></i> Delivery
                                        </span>
                                    @endif

                                    @if($address->is_default)
                                        <br>
                                        <small class="text-success mt-1 d-inline-block">
                                            <i class="fas fa-star me-1"></i> Default
                                        </small>
                                    @endif
                                </td>
                                
                                <td class="pe-4 text-end">
                                    <a href="mailto:{{ $address->email ?: 'admin@vshop.com' }}?subject=Missing Address Details Update Required" 
                                       class="btn btn-sm btn-outline-info rounded-3">
                                        <i class="far fa-envelope me-1"></i> Contact
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-check-double fa-3x mb-3 text-success"></i>
                                    <h5 class="text-dark">All addresses fully validated!</h5>
                                    <p class="text-muted">No incomplete address details detected in database.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light bg-opacity-50">
                <div class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Showing {{ $addresses->firstItem() ?? 0 }} to {{ $addresses->lastItem() ?? 0 }} of {{ $addresses->total() }} addresses
                </div>
                <div>
                    {{ $addresses->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Per page selector functionality
        const perPageSelect = document.getElementById('perPage');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });
        }
    });
</script>
@endpush
@endsection
