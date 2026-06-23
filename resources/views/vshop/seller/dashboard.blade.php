@extends('vshop.seller.main')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
@endpush

@section('title','Dashboard')

@section('content')


<div class="container-fluid seller-dashboard">
    
    {{-- Currency Switcher + Alert Icon Side by Side --}}
    <div class="d-flex justify-content-end mb-3">
        <div class="d-flex align-items-center gap-3">
            {{-- Currency Switcher --}}
            <div class="d-flex align-items-center gap-2">
                <label class="fw-semibold small mb-0">Currency:</label>
                <select id="currencySwitcher" class="form-select form-select-sm" style="width:120px;">
                    <option value="INR">₹ INR</option>
                    <option value="USD">$ USD</option>
                    <option value="EUR">€ EUR</option>
                </select>
            </div>

        </div>
    </div>

  
  {{-- ================= TOP CARDS ================= --}}
<div class="dashboard-card-wrapper-sec mb-4">
    <div class="dashboard-card-wrapper-left">
    <div class="row g-3 mb-4">
        <!-- Row 1: 5 cards -->
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>Total Sales</p>
                <h4 class="money" data-amount="{{ $totalSales ?? 0 }}">
                    ₹ {{ number_format($totalSales ?? 0, 2) }}
                </h4>
                <div class="pro-card-meta">
                    <span class="badge-success">▲ 16.8%</span>
                    <span class="meta-text">Since last month</span>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>Total Orders</p>
                <h4>{{ $totalOrders ?? 0 }}</h4>
                <div class="pro-card-meta">
                    <span class="badge-success">▲ 3.5%</span>
                    <span class="meta-text">Since last month</span>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>New Customers</p>
                <h4>{{ $newCustomers ?? 0 }}</h4>
                <div class="pro-card-meta">
                    <span class="badge-danger">▼ 6.8%</span>
                    <span class="meta-text">Since last month</span>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>Total Income</p>
                <h4 class="money" data-amount="{{ $totalIncome ?? 0 }}">
                    ₹ {{ number_format($totalIncome ?? 0, 2) }}
                </h4>
                <div class="pro-card-meta">
                    <span class="badge-success">▲ 9.1%</span>
                    <span class="meta-text">Since last month</span>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>Total Returns</p>
                <h4 class="money" data-amount="{{ $totalReturns ?? 0 }}">
                    ₹ {{ number_format($totalReturns ?? 0, 2) }}
                </h4>
                <div class="pro-card-meta">
                    <span class="badge-success">▲ 16.8%</span>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row g-3">
        <!-- Row 2: next 5 cards -->
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>Active Enquiries</p>
                <h4>{{ $activeEnquiries ?? 0 }}</h4>
                <div class="pro-card-meta">
                    <span class="badge-neutral">Current</span>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>Payment Success Rate</p>
                <h4>{{ $paymentSuccessRate ?? '0%' }}</h4>
                <div class="pro-card-meta">
                    <span class="badge-success">Transactions Success</span>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>SLA Performance</p>
                <h4>{{ $slaPerformance ?? '0%' }}</h4>
                <div class="pro-card-meta">
                    <span class="badge-neutral">Dispatch / Delivery</span>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>Refund Efficiency</p>
                <h4>{{ $refundEfficiency ?? '0%' }}</h4>
                <div class="pro-card-meta">
                    <span class="badge-success">Processed Quickly</span>
                </div>
            </div>
        </div>
    
        <div class="col-xl-2-4 col-lg-3 col-md-4 col-sm-6">
            <div class="pro-card">
                <p>Delayed Orders</p>
                <h4 class="text-danger">{{ $delayedOrders ?? 0 }}</h4>
                <div class="pro-card-meta">
                    <span class="badge-danger">Requires Attention</span>
                </div>
            </div>
        </div>
    </div>
    </div>

    <div class="boost-card-container">
        <!-- Row 3: Boost Performance -->
        <div class="pro-card boost-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <p class="mb-0 fw-bold">BOOST PERFORMANCE</p>
                <i class="fa fa-question-circle text-muted" style="font-size: 15px; cursor: pointer;"></i>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>Current Reach</span>
                <span class="fw-bold">({{ $currentReach ?? 15 }}k)</span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>Estimated Reach</span>
                <span class="fw-bold">({{ $estimatedReach ?? 55 }}k)</span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span>Boosted Enquiries</span>
                <span class="fw-bold text-success">(+{{ $boostedEnquiries ?? 42 }})</span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <span>Boost ROI</span>
                <span class="fw-bold">({{ $boostROI ?? 3.1 }}x)</span>
            </div>
            
            <div class="d-flex justify-content-center mt-3">
                <button type="button" class="btn p-0 border-0 bg-transparent shadow-none boost-btn">
                    <div class="boost-icon-wrapper">
                        <img src="{{ asset('images/boost_icon.png') }}" alt="Boost Now" style="width: 100%; height: auto; display: block;">
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

    {{-- Based on seller type, show/hide sections --}}
    @php
        $sellerType = session('seller_type', 'vshop');
        $isVshop = ($sellerType == 'vshop' || $sellerType == 'both');
        $isVtravel = ($sellerType == 'vtravel' || $sellerType == 'both');
    @endphp

    {{-- ================= SALES (Only for vshop) ================= --}}
    @if($isVshop)
    <div class="card dashboard-card mb-4" id="sales-overview-section">
        <div class="card-header">Sales Overview (Last 8 Months)</div>
        <div class="card-body">
            <canvas id="salesOverview"></canvas>
        </div>
    </div>
    @endif

    {{-- ================= REAL HEATMAP GRID (Only for vshop) ================= --}}
    @if($isVshop)
    <div class="card dashboard-card mb-4" id="heatmap-section">
        <div class="card-header">Order Activity Heatmap (Hourly / Day Wise)</div>
        <div class="card-body">
            <div class="heatmap-grid">
               @foreach(($heatmapMatrix ?? []) as $day => $hours)
                    <div class="heatmap-row">
                        <div class="heatmap-day">{{ $day }}</div>
                        @foreach($hours as $hour => $value)
                            <div class="heatmap-cell"
                                 data-value="{{ $value }}"
                                 title="{{ $hour }} → {{ $value }} orders">
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- ================= STAR PRODUCTS (Only for vshop) ================= --}}
    @if($isVshop)
    <div class="card dashboard-card mb-4" id="star-products-section">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="mb-0">Star Products</span>
            <form method="GET" class="filter-form" data-section="star-products-section">
                <select name="star_filter" class="form-select w-auto filter-dropdown" data-section="star-products-section">
                    <option value="sold" {{ request('star_filter')=='sold'?'selected':'' }}>Most Sold</option>
                    <option value="rating" {{ request('star_filter')=='rating'?'selected':'' }}>Highest Review</option>
                    <option value="grievance" {{ request('star_filter')=='grievance'?'selected':'' }}>Most Complaints</option>
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Sold</th>
                        <th>Rating</th>
                        <th>Complaints</th>
                    </tr>
                </thead>
                <tbody id="starTable">
                    @forelse($starProducts as $p)
                        <tr>
                            <td>{{$p->name}}</td>
                            <td>{{$p->total_sold ?? 0}}</td>
                            <td>{{round($p->reviews_avg_rating,1) ?? 0}}</td>
                            <td>{{$p->complaints_count ?? 0}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No star products data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($starProducts, 'links') && $starProducts->hasPages())
        <div class="card-footer bg-transparent">
            {{ $starProducts->appends(request()->except(['star_filter', 'page']))->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
    @endif

    {{-- ================= SLOW MOVERS (Only for vshop) ================= --}}
    @if($isVshop)
    <div class="card dashboard-card mb-4" id="slow-movers-section">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="mb-0">Slow Movers</span>
            <form method="GET" class="filter-form" data-section="slow-movers-section">
                <select name="slow_filter" class="form-select w-auto filter-dropdown" data-section="slow-movers-section">
                    <option value="latest" {{ request('slow_filter')=='latest'?'selected':'' }}>Latest</option>
                    <option value="oldest" {{ request('slow_filter')=='oldest'?'selected':'' }}>Oldest</option>
                    <option value="least_popular" {{ request('slow_filter')=='least_popular'?'selected':'' }}>Least Popular</option>
                    <option value="lowest_review" {{ request('slow_filter')=='lowest_review'?'selected':'' }}>Lowest Review</option>
                    <option value="lowest_sold" {{ request('slow_filter')=='lowest_sold'?'selected':'' }}>Lowest Sold</option>
                    <option value="highest_returned" {{ request('slow_filter')=='highest_returned'?'selected':'' }}>Highest Returned</option>
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slowProducts as $item)
                        <tr>
                            <td>{{ $item->name ?? '-' }}</td>
                            <td>{{ $item->comment ?? '-' }}</td>
                            <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No slow movers data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($slowProducts, 'links') && $slowProducts->hasPages())
        <div class="card-footer bg-transparent">
            {{ $slowProducts->appends(request()->except(['slow_filter', 'page']))->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
    @endif

    {{-- ================= TOUR PACKAGES / SERVICES (Only for vtravel) ================= --}}
    @if($isVtravel)
    <div class="card dashboard-card mb-4" id="tour-services-section">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="mb-0">Tour Packages / Services</span>
            <a href="{{ route('seller.tours.index') }}" class="btn btn-sm btn-primary">Manage Tours</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>Package/Service Name</th>
                            <th>Bookings</th>
                            <th>Revenue</th>
                            <th>Rating</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tourPackages ?? [] as $tour)
                        <tr>
                            <td>{{ $tour->name ?? '-' }}</td>
                            <td>{{ $tour->total_bookings ?? 0 }}</td>
                            <td class="money" data-amount="{{ $tour->revenue ?? 0 }}">₹ {{ number_format($tour->revenue ?? 0, 2) }}</td>
                            <td>{{ $tour->avg_rating ?? 'N/A' }}</td>
                            <td>
                                <span class="badge {{ ($tour->status ?? 'active') == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($tour->status ?? 'active') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">No tour packages or services available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Booking Trends for vtravel --}}
    <div class="card dashboard-card mb-4" id="booking-trends-section">
        <div class="card-header">Booking Trends (Last 8 Months)</div>
        <div class="card-body">
            <canvas id="bookingTrends"></canvas>
        </div>
    </div>
    @endif

    {{-- ================= GRIEVANCE (Both vshop and vtravel) ================= --}}
    <div class="card dashboard-card mb-4" id="grievances-section">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="mb-0">Grievances</span>
            <form method="GET" class="filter-form" data-section="grievances-section">
                <select name="grievance_filter" class="form-select w-auto filter-dropdown" data-section="grievances-section">
                    <option value="latest" {{ request('grievance_filter')=='latest'?'selected':'' }}>Latest</option>
                    <option value="oldest" {{ request('grievance_filter')=='oldest'?'selected':'' }}>Oldest</option>
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Product/Service</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($grievances as $g)
                        <tr>
                            <td>{{$g->product->name ?? $g->tour->name ?? '-'}}</td>
                            <td>{{$g->message}}</td>
                            <td>{{$g->created_at->format('d M Y')}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No grievances available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($grievances, 'links') && $grievances->hasPages())
        <div class="card-footer bg-transparent">
            {{ $grievances->appends(request()->except(['grievance_filter', 'page']))->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

    {{-- ================= REVIEWS (Both vshop and vtravel) ================= --}}
    <div class="card dashboard-card mb-4" id="reviews-section">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span class="mb-0">Reviews</span>
            <form method="GET" class="filter-form" data-section="reviews-section">
                <select name="review_filter" class="form-select form-select-sm filter-dropdown" data-section="reviews-section">
                    <option value="latest" {{ request('review_filter')=='latest'?'selected':'' }}>Latest</option>
                    <option value="asked_remove" {{ request('review_filter')=='asked_remove'?'selected':'' }}>Asked to Remove</option>
                </select>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Product/Service</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $r)
                        <tr>
                            <td>{{$r->product->name ?? $r->tour->name ?? '-'}}</td>
                            <td>{{$r->rating}}</td>
                            <td>{{$r->comment}}</td>
                            <td>{{$r->created_at->format('d M Y')}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No reviews available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($reviews, 'links') && $reviews->hasPages())
        <div class="card-footer bg-transparent">
            {{ $reviews->appends(request()->except(['review_filter', 'page']))->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
/* =========================================================
   SELLER DASHBOARD GLOBAL & FONTS
========================================================= */
.seller-dashboard {
    background: #f8fafc;
    padding: 24px;
    position: relative;
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

.seller-dashboard * {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
}

/* =========================================================
   DASHBOARD CARD WRAPPER & GRID
========================================================= */
.dashboard-card-wrapper-sec {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: 20px;
    align-items: stretch;
}

.dashboard-card-wrapper-left {
    min-width: 0;
}

.boost-card-container {
    width: 280px;
}

@media (max-width: 1200px) {
    .dashboard-card-wrapper-sec {
        grid-template-columns: 1fr;
    }
    .boost-card-container {
        width: 100%;
    }
}

/* =========================================================
   METRIC CARD (PRO CARD)
========================================================= */
.pro-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 20px;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    justify-content: space-between;
    height: 100%;
    position: relative;
}

.pro-card:hover {
    transform: translateY(-4px);
    border-color: #3b82f6 !important;
    box-shadow: 0 10px 15px -3px rgba(59, 130, 246, 0.08), 0 4px 6px -4px rgba(59, 130, 246, 0.08);
}

.pro-card p {
    margin: 0 0 6px 0;
    color: #64748b;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.pro-card h4 {
    margin: 0 0 8px 0;
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    font-family: 'Outfit', sans-serif !important;
}

/* =========================================================
   METRIC CARD BADGES
========================================================= */
.pro-card-meta {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    margin-top: auto;
}

.badge-success {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background-color: #f0fdf4;
    color: #166534;
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
    border: 1px solid #dcfce7;
}

.badge-danger {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background-color: #fef2f2;
    color: #991b1b;
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
    border: 1px solid #fee2e2;
}

.badge-neutral {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background-color: #f1f5f9;
    color: #475569;
    padding: 2px 8px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 11px;
    border: 1px solid #e2e8f0;
}

.meta-text {
    font-size: 11px;
    color: #64748b;
    font-weight: 500;
}

/* =========================================================
   BOOST PERFORMANCE CARD
========================================================= */
.boost-card {
    border: 1px solid #e2e8f0 !important;
    background: linear-gradient(to bottom, #ffffff, #f8fafc) !important;
    border-radius: 16px !important;
    padding: 24px 20px !important;
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.boost-card p.fw-bold {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 12px !important;
    letter-spacing: 0.5px;
    color: #475569 !important;
    text-transform: uppercase;
}

.boost-card span {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 13px !important;
    color: #64748b;
}

.boost-card .fw-bold {
    color: #0f172a !important;
    font-family: 'Outfit', sans-serif !important;
}

.boost-card .text-success {
    color: #166534 !important;
}

.boost-icon-wrapper {
    background: white;
    padding: 10px;
    border-radius: 16px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.06);
    border: 1px solid #f0f0f0;
    width: 105px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.boost-icon-wrapper:hover {
    transform: scale(1.08);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    border-color: #3b82f6;
}

/* =========================================================
   DASHBOARD SECTION CARDS
========================================================= */
.dashboard-card {
    background: #ffffff;
    border-radius: 16px;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05), 0 1px 2px -1px rgba(0, 0, 0, 0.05);
    overflow: hidden;
    margin-bottom: 24px;
}

.dashboard-card .card-header {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-weight: 600;
    font-size: 15px;
    color: #0f172a;
    background: #ffffff;
    border-bottom: 1px solid #f1f5f9;
    padding: 18px 24px;
}

.dashboard-card .card-body {
    padding: 24px;
}

.dashboard-card .card-footer {
    background: #ffffff;
    border-top: 1px solid #f1f5f9;
    padding: 14px 24px;
}

/* =========================================================
   HEATMAP
========================================================= */
.heatmap-grid {
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.heatmap-row {
    display: flex;
    align-items: center;
    gap: 6px;
}

.heatmap-day {
    width: 60px;
    font-size: 12px;
    font-weight: 500;
    color: #64748b;
}

.heatmap-cell {
    width: 22px;
    height: 22px;
    border-radius: 6px;
    background: #f1f5f9;
    transition: all 0.2s ease;
}

.heatmap-cell:hover {
    transform: scale(1.25);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    z-index: 10;
}

/* =========================================================
   CANVAS & CHARTS
========================================================= */
canvas {
    max-height: 320px;
}

#salesOverview, #bookingTrends {
    height: 300px !important;
}

/* =========================================================
   PREMIUM TABLES
========================================================= */
.table-responsive {
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    margin-bottom: 0;
}

.table {
    width: 100%;
    margin-bottom: 0;
    border-collapse: collapse;
}

.table th {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-weight: 600 !important;
    font-size: 11px !important;
    letter-spacing: 0.5px !important;
    text-transform: uppercase !important;
    color: #004392 !important;
    background-color: #f0f7ff !important;
    border: none !important;
    border-bottom: 2px solid #dbeafe !important;
    padding: 14px 20px !important;
}

.table tbody td {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 13px !important;
    color: #334155 !important;
    border: none !important;
    border-bottom: 1px solid #f1f5f9 !important;
    padding: 14px 20px !important;
    vertical-align: middle !important;
    background: #ffffff !important;
}

.table tbody tr:last-child td {
    border-bottom: none !important;
}

.table tbody tr {
    transition: background-color 0.2s ease;
}

.table tbody tr:hover td {
    background-color: #f8fafc !important;
}

/* =========================================================
   5 CARDS COLUMNS
========================================================= */
.col-xl-2-4 {
    width: 20%;
}

@media(max-width:1200px) {
    .col-xl-2-4 {
        width: 25%;
    }
}

@media(max-width:992px) {
    .col-xl-2-4 {
        width: 33.33%;
    }
}

@media(max-width:768px) {
    .col-xl-2-4 {
        width: 50%;
    }
}

@media(max-width:576px) {
    .col-xl-2-4 {
        width: 100%;
    }
}

/* =========================================================
   FORM ELEMENTS & SELECTS
========================================================= */
.form-select-sm {
    font-family: 'Plus Jakarta Sans', sans-serif !important;
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #334155 !important;
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    padding: 6px 32px 6px 12px !important;
    transition: all 0.2s ease !important;
    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05) !important;
    height: auto !important;
}

.form-select-sm:focus {
    border-color: #3b82f6 !important;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1) !important;
    outline: none !important;
}

.filter-dropdown {
    cursor: pointer;
    min-width: 150px;
}

.filter-form {
    margin: 0;
}
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function(){
    @if($isVshop)
    const canvas = document.getElementById('salesOverview');
    if(canvas){
        const ctx = canvas.getContext('2d');
        let salesData = @json($salesData ?? []);
        let months = @json($months ?? []);
        let hasData = Array.isArray(salesData) && salesData.some(v => v > 0);
        if(!hasData){
            canvas.parentElement.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fa fa-chart-bar fa-2x mb-2"></i><br>
                    <b>No sales data available</b><br>
                    <small>Start selling to see analytics 📈</small>
                </div>
            `;
        } else {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, '#3b82f6');
            gradient.addColorStop(1, '#93c5fd');
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Sales (₹)',
                        data: salesData,
                        backgroundColor: gradient,
                        hoverBackgroundColor: '#2563eb',
                        borderRadius: 8,
                        borderSkipped: false
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
                                color: '#64748b'
                            }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            border: { dash: [5, 5] },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11 },
                                color: '#64748b',
                                beginAtZero: true
                            }
                        }
                    }
                }
            });
        }
    }
    @endif

    @if($isVtravel)
    const bookingCanvas = document.getElementById('bookingTrends');
    if(bookingCanvas){
        const ctx = bookingCanvas.getContext('2d');
        let bookingData = @json($bookingTrendsData ?? []);
        let months = @json($months ?? []);
        let hasData = Array.isArray(bookingData) && bookingData.some(v => v > 0);
        if(!hasData){
            bookingCanvas.parentElement.innerHTML = `
                <div class="text-center py-5 text-muted">
                    <i class="fa fa-calendar-alt fa-2x mb-2"></i><br>
                    <b>No booking data available</b><br>
                    <small>Start accepting bookings to see trends 📈</small>
                </div>
            `;
        } else {
            const fillGradient = ctx.createLinearGradient(0, 0, 0, 300);
            fillGradient.addColorStop(0, 'rgba(59, 130, 246, 0.2)');
            fillGradient.addColorStop(1, 'rgba(59, 130, 246, 0.0)');
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: months,
                    datasets: [{
                        label: 'Bookings',
                        data: bookingData,
                        borderColor: '#3b82f6',
                        borderWidth: 3,
                        backgroundColor: fillGradient,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#ffffff',
                        pointBorderColor: '#3b82f6',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            backgroundColor: '#0f172a',
                            titleFont: { family: 'Plus Jakarta Sans', size: 13, weight: 'bold' },
                            bodyFont: { family: 'Plus Jakarta Sans', size: 12 },
                            padding: 12,
                            cornerRadius: 8,
                            displayColors: false
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11, weight: '500' },
                                color: '#64748b'
                            }
                        },
                        y: {
                            grid: { color: '#f1f5f9' },
                            border: { dash: [5, 5] },
                            ticks: {
                                font: { family: 'Plus Jakarta Sans', size: 11 },
                                color: '#64748b',
                                beginAtZero: true,
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }
    @endif

    @if($isVshop)
    document.querySelectorAll('.heatmap-cell').forEach(cell => {
        let value = parseInt(cell.dataset.value || 0);
        let color = '#f1f5f9';
        if(value > 0 && value <= 10) color = '#dbeafe';
        else if(value <= 30) color = '#93c5fd';
        else if(value <= 60) color = '#3b82f6';
        else if(value > 60) color = '#1d4ed8';
        cell.style.background = color;
    });
    @endif

    document.querySelectorAll('.alert-dropdown-item').forEach(item => {
        item.addEventListener('click', function() {
            const url = this.getAttribute('data-href');
            if (url) {
                window.location.href = url;
            }
        });
    });
});

// Currency Switcher
document.addEventListener("DOMContentLoaded", function(){
    let currency = localStorage.getItem('currency') || 'INR';
    const rates = { INR: 1, USD: 0.012, EUR: 0.011 };
    const symbols = { INR: '₹', USD: '$', EUR: '€' };
    const switcher = document.getElementById('currencySwitcher');
    if(switcher){
        switcher.value = currency;
        function formatMoney(amount){
            return amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        function updateCurrency(){
            document.querySelectorAll('.money').forEach(el => {
                let amount = parseFloat(el.getAttribute('data-amount')) || 0;
                let converted = amount * rates[currency];
                el.innerHTML = symbols[currency] + ' ' + formatMoney(converted);
            });
        }
        updateCurrency();
        switcher.addEventListener('change', function(){
            currency = this.value;
            localStorage.setItem('currency', currency);
            updateCurrency();
        });
    }
});

// Filter handling
document.addEventListener("DOMContentLoaded", function(){
    document.querySelectorAll('.filter-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const sectionId = this.dataset.section;
            const select = this.querySelector('.filter-dropdown');
            const filterName = select.name;
            const filterValue = select.value;
            if (!sectionId) return;
            const targetSection = document.getElementById(sectionId);
            if (!targetSection) return;
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.set(filterName, filterValue);
            urlParams.delete('page');
            fetch(window.location.pathname + '?' + urlParams.toString(), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newSection = doc.getElementById(sectionId);
                if (newSection) {
                    targetSection.innerHTML = newSection.innerHTML;
                    attachFormListeners(targetSection);
                } else {
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                }
            })
            .catch(() => {
                window.location.href = window.location.pathname + '?' + urlParams.toString();
            });
        });
        
        const select = form.querySelector('.filter-dropdown');
        if (select) {
            select.addEventListener('change', function() {
                form.dispatchEvent(new Event('submit'));
            });
        }
    });
    
    function attachFormListeners(container) {
        container.querySelectorAll('.filter-form').forEach(form => {
            const newForm = form.cloneNode(true);
            form.parentNode.replaceChild(newForm, form);
            newForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const sectionId = this.dataset.section;
                const select = this.querySelector('.filter-dropdown');
                const filterName = select.name;
                const filterValue = select.value;
                if (!sectionId) return;
                const targetSection = document.getElementById(sectionId);
                if (!targetSection) return;
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set(filterName, filterValue);
                urlParams.delete('page');
                fetch(window.location.pathname + '?' + urlParams.toString(), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newSection = doc.getElementById(sectionId);
                    if (newSection) {
                        targetSection.innerHTML = newSection.innerHTML;
                        attachFormListeners(targetSection);
                    } else {
                        window.location.href = window.location.pathname + '?' + urlParams.toString();
                    }
                })
                .catch(() => {
                    window.location.href = window.location.pathname + '?' + urlParams.toString();
                });
            });
            const newSelect = newForm.querySelector('.filter-dropdown');
            if (newSelect) {
                newSelect.addEventListener('change', function() {
                    newForm.dispatchEvent(new Event('submit'));
                });
            }
        });
    }
    
    const sections = ['star-products-section', 'slow-movers-section', 'grievances-section', 'reviews-section', 'tour-services-section', 'booking-trends-section'];
    sections.forEach(sectionId => {
        const section = document.getElementById(sectionId);
        if (section) attachFormListeners(section);
    });
});
</script>
@endpush