@extends('layouts.vertical', ['title' => 'Insights: Bad Review Customers'])

@section('content')
<style>
.reset-btn {
    background: #61b3e5 !important;
    color: #fff;
    border: 0 !important;
    padding: 7px 7px;
    border-radius: 5px;
}
.reset-btn:hover {
    color: #fff;
}
.product-row {
    transition: all 0.2s ease;
    cursor: pointer;
}
.product-row:hover {
    background-color: #f8f9fa !important;
    transform: translateX(2px);
}
.seller-separator td {
    padding: 0 !important;
}
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h4 class="fw-bold text-dark mb-0"><i class="fas fa-chart-line me-2"></i> Customer Insights - Bad Review Products</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item">Insights</li>
                <li class="breadcrumb-item active" aria-current="page">Bad Reviews</li>
            </ol>
        </nav>
    </div>

    <!-- Filter Section -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body py-3">
            <form method="GET" action="{{ route('admin.customers.insights.bad-reviews') }}" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-dark mb-1">
                        <i class="fas fa-store me-1"></i> Business Type
                    </label>
                    <select name="type" class="form-select form-select-sm border-0 bg-light rounded-3">
                        <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>All Types</option>
                        <option value="direct" {{ request('type') == 'direct' ? 'selected' : '' }}>Direct</option>
                        <option value="dropshipping" {{ request('type') == 'dropshipping' ? 'selected' : '' }}>Dropshipping</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-dark mb-1">
                        <i class="fas fa-calendar me-1"></i> Date Range
                    </label>
                    <select name="date_range" class="form-select form-select-sm border-0 bg-light rounded-3">
                        <option value="all" {{ request('date_range') == 'all' ? 'selected' : '' }}>All Time</option>
                        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
                        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>Last 30 Days</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-dark mb-1">
                        <i class="fas fa-search me-1"></i> Search
                    </label>
                    <input type="text" name="search" class="form-control form-control-sm border-0 bg-light rounded-3" 
                           placeholder="Seller name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm px-4" style="background: linear-gradient(135deg, #004392 0%, #2c7fc3 100%); border: none;">
                            <i class="fas fa-filter me-1"></i> Apply Filters
                        </button>
                        <a href="{{ route('admin.customers.insights.bad-reviews') }}" class="btn btn-secondary btn-sm px-3 reset-btn" style="background: #6c757d; border: none;">
                            <i class="fas fa-undo-alt me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Per Page Selector -->
    <div class="d-flex justify-content-end mb-3">
        <div class="d-flex align-items-center gap-2">
            <label class="text-muted fs-12 mb-0">Show:</label>
            <select id="perPage" class="form-select form-select-sm w-auto border-0 bg-light rounded-3" style="cursor: pointer;">
                <option value="5" {{ request('per_page') == 5 ? 'selected' : '' }}>5</option>
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10</option>
                <option value="20" {{ request('per_page') == 20 ? 'selected' : '' }}>20</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50</option>
            </select>
            <span class="text-muted fs-12">entries per page</span>
        </div>
    </div>

    <!-- Product Wise Bad Review Table -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-0 overflow-hidden">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0 w-100" style="min-width: 1200px;">
                            <thead>
                                <tr>
                                    <th class="py-3 px-4 fw-bold" style="background: #004392; color: #fff; border-radius: 12px 0 0 0; width: 20%;">
                                        <i class="fas fa-store me-2"></i> Seller Details
                                    </th>
                                    <th class="py-3 px-4 fw-bold" style="background: #004392; color: #fff; width: 30%;">
                                        <i class="fas fa-box me-2"></i> Product Details
                                    </th>
                                    <th class="py-3 px-4 fw-bold" style="background: #004392; color: #fff; width: 15%;">
                                        <i class="fas fa-globe me-2"></i> Platform Name
                                    </th>
                                    <th class="py-3 px-4 fw-bold" style="background: #004392; color: #fff; width: 15%;">
                                        <i class="fas fa-tag me-2"></i> Business Type
                                    </th>
                                    <th class="py-3 px-4 fw-bold text-center" style="background: #004392; color: #fff; border-radius: 0 12px 0 0; width: 20%;">
                                        <i class="fas fa-star-half-alt me-2"></i> Bad Reviews
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($sellerBadReviews as $seller)
                                    @foreach($seller['bad_products'] as $index => $product)
                                        <tr class="product-row" 
                                           
                                            style="border-bottom: 1px solid #f0f0f0;">
                                            
                                            <!-- Seller Details (show only for first product of each seller) -->
                                            @if($index == 0)
                                                <td class="px-4 py-3" style="background: white; vertical-align: top;" rowspan="{{ count($seller['bad_products']) }}">
                                                    <div class="d-flex align-items-start">
                                                        <div class="rounded-circle text-white p-2 me-2 d-flex align-items-center justify-content-center" 
                                                             style="width: 40px; height: 40px; font-size: 14px; background: linear-gradient(135deg, #004392 0%, #2c7fc3 100%); flex-shrink: 0;">
                                                            {{ strtoupper(substr($seller['seller_name'] ?? 'N/A', 0, 2)) }}
                                                        </div>
                                                        <div style="min-width: 0;">
                                                            <div class="fw-semibold text-dark">{{ $seller['seller_name'] ?? 'N/A' }}</div>
                                                            <small class="text-muted fs-11">
                                                                <i class="fas fa-envelope me-1"></i> {{ $seller['seller_email'] ?? 'N/A' }}
                                                            </small>
                                                            <br>
                                                            <small class="text-muted fs-11">
                                                                <i class="fas fa-id-card me-1"></i> ID: #{{ $seller['seller_id'] ?? 'N/A' }}
                                                            </small>
                                                            <br>
                                                            <small class="text-muted fs-11">
                                                                <i class="fas fa-boxes me-1"></i> Total Products: {{ $seller['total_products'] ?? 0 }}
                                                            </small>
                                                            <br>
                                                            <small class="text-muted fs-11">
                                                                <i class="fas fa-star-half-alt me-1"></i> Total Bad Reviews: <strong class="text-danger">{{ $seller['bad_review_count'] ?? 0 }}</strong>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                            @endif

                                            <!-- Product Details -->
                                            <td class="px-4 py-3" style="background: white;">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-primary bg-opacity-10 p-2 me-2" style="flex-shrink: 0;">
                                                        <i class="fas fa-box text-primary fs-12"></i>
                                                    </div>
                                                    <div>
                                                        <div class="fw-semibold text-dark">
                                                            <a href="{{ url('admin/reviews') }}?product_id={{ $product['product_id'] }}&rating[]=1&rating[]=2" 
                                                               style="color: #004392; text-decoration: none;"
                                                               onclick="event.stopPropagation()">
                                                                {{ $product['product_name'] ?? 'Unknown Product' }}
                                                            </a>
                                                        </div>
                                                        <small class="text-muted fs-11">
                                                            <i class="fas fa-id-card me-1"></i> Product ID: #{{ $product['product_id'] }}
                                                        </small>
                                                        <br>
                                                        <small class="text-muted fs-11">
                                                            <i class="fas fa-star me-1"></i> Avg Rating: {{ number_format($product['avg_rating'] ?? 0, 1) }}/5.0
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Platform Name -->
                                            <td class="px-4 py-3" style="background: white;">
                                                <div class="d-flex align-items-center">
                                                    <div class="rounded-circle bg-info bg-opacity-10 p-2 me-2" style="flex-shrink: 0;">
                                                        <i class="fas fa-globe text-info fs-12"></i>
                                                    </div>
                                                    <div>
                                                        <span class="fw-semibold text-dark">{{ $seller['platform_name'] ?? 'N/A' }}</span>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Business Type -->
                                            <td class="px-4 py-3" style="background: white;">
                                                @if($seller['business_type'] == 'dropshipping')
                                                    <span class="badge px-3 py-2 rounded-3 fs-11 fw-bold" style="background: rgba(23, 162, 184, 0.15); color: #17a2b8;">
                                                        <i class="fas fa-truck me-1"></i> Dropshipping
                                                    </span>
                                                @else
                                                    <span class="badge px-3 py-2 rounded-3 fs-11 fw-bold" style="background: rgba(40, 167, 69, 0.15); color: #28a745;">
                                                        <i class="fas fa-boxes me-1"></i> Direct
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- Bad Review Count -->
                                            <td class="px-4 py-3 text-center" style="background: white;">
                                                <div class="d-flex flex-column align-items-center">
                                                    <span class="badge px-3 py-2 rounded-3 fs-13 fw-bold mb-1" 
                                                          style="background: rgba(220, 53, 69, 0.15); color: #dc3545;">
                                                        <i class="fas fa-star-half-alt me-1"></i> {{ $product['bad_review_count'] ?? 0 }} Bad Reviews
                                                    </span>
                                                    @if(isset($product['total_reviews']) && $product['total_reviews'] > 0)
                                                        <small class="text-muted fs-10">
                                                            out of {{ $product['total_reviews'] }} total reviews
                                                        </small>
                                                        @if(isset($product['bad_review_percentage']))
                                                            <small class="text-danger fs-10">
                                                                {{ $product['bad_review_percentage'] }}% are bad
                                                            </small>
                                                        @endif
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    
                                    <!-- Add a separator between sellers -->
                                    @if(!$loop->last)
                                        <tr class="seller-separator">
                                            <td colspan="5" style="padding: 0; border-bottom: 2px solid #e9ecef;"></td>
                                        </tr>
                                    @endif
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-smile fa-3x text-muted mb-3 animate-bounce"></i>
                                                <h5 class="text-dark">Hooray! No bad reviews found</h5>
                                                <p class="text-muted mb-0">Your customers are extremely satisfied. Keep up the amazing work!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white py-3 rounded-bottom-4 border-top">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="mb-2 mb-sm-0">
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i> 
                                Showing {{ $sellerBadReviews->firstItem() ?? 0 }} to {{ $sellerBadReviews->lastItem() ?? 0 }} of {{ $sellerBadReviews->total() ?? 0 }} sellers
                            </small>
                        </div>
                        <div>
                            {{ $sellerBadReviews->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Per page selector functionality
    document.addEventListener('DOMContentLoaded', function() {
        const perPageSelect = document.getElementById('perPage');
        if (perPageSelect) {
            perPageSelect.addEventListener('change', function() {
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', this.value);
                url.searchParams.set('page', 1);
                window.location.href = url.toString();
            });
        }
        
        // Prevent event bubbling for links inside product rows
        document.querySelectorAll('.product-row a').forEach(link => {
            link.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        });
    });
</script>
@endpush
@endsection
