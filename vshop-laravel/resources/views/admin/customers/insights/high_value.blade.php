@extends('layouts.vertical', ['title' => 'Insights: High-Value Buyers'])

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h4 class="fw-bold text-dark mb-0">High-Value Buyers</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Buyers</a></li>
                <li class="breadcrumb-item">Insights</li>
                <li class="breadcrumb-item active" aria-current="page">High-Value</li>
            </ol>
        </nav>
    </div>



    <!-- High-Value Buyers Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light py-3">
                        <tr>
                            <th class="ps-4">Rank & Buyer</th>
                            <th>Completed Orders</th>
                            <th>Average Order Value</th>
                            <th>Total Spent</th>
                            <th class="pe-4 text-end">Segment Badge</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $index => $customer)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <!-- Rank Badge -->
                                        <div class="rank-badge me-3 d-flex align-items-center justify-content-center rounded-circle fw-bold
                                            {{ $index === 0 ? 'bg-warning text-dark border border-warning' : ($index === 1 ? 'bg-secondary bg-opacity-25 text-dark' : ($index === 2 ? 'bg-bronze text-dark' : 'bg-light text-muted')) }}" 
                                            style="width: 28px; height: 28px; font-size: 13px;">
                                            {{ $customers->firstItem() + $index }}
                                        </div>
                                        
                                        <!-- User details -->
                                        <div class="rounded-circle bg-light p-2 border me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <span class="fw-bold text-primary">{{ strtoupper(substr($customer->name, 0, 2)) }}</span>
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $customer->name }}</h6>
                                            <small class="text-muted"><i class="far fa-envelope me-1"></i> {{ $customer->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-semibold text-dark">{{ $customer->orders_count }}</h6>
                                    <small class="text-muted fs-11">Orders placed</small>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-semibold text-muted">
                                        ₹{{ number_format($customer->orders_count > 0 ? ($customer->total_spent / $customer->orders_count) : 0, 2) }}
                                    </h6>
                                    <small class="text-muted fs-11">AOV</small>
                                </td>
                                <td>
                                    <h5 class="mb-0 fw-bold text-dark">₹{{ number_format($customer->total_spent, 2) }}</h5>
                                    <small class="text-success fs-11 fw-semibold"><i class="fas fa-check-double"></i> Verified Revenue</small>
                                </td>
                                <td class="pe-4 text-end">
                                    @if($customer->total_spent >= 15000)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-20 px-3 py-1.5 fs-11 rounded-3">
                                            VIP Gold Tier
                                        </span>
                                    @elseif($customer->total_spent >= 5000)
                                        <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-20 px-3 py-1.5 fs-11 rounded-3">
                                            Elite Silver Tier
                                        </span>
                                    @else
                                        <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-20 px-3 py-1.5 fs-11 rounded-3">
                                            Star Buyer
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <i class="fas fa-gem fa-3x mb-3 text-muted"></i>
                                    <h5 class="text-dark">No high-value buyers found</h5>
                                    <p class="text-muted">High-value segments populate as buyers complete orders.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light bg-opacity-50">
                <small class="text-muted">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} results</small>
                <div>
                    {{ $customers->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-purple { background-color: #6f42c1; }
.text-purple { color: #6f42c1; }
.border-purple { border-color: #6f42c1; }
.fs-20 { font-size: 20px; }
.fs-13 { font-size: 13px; }
.fs-11 { font-size: 11px; }
.fs-10 { font-size: 10px; }
.rounded-4 { border-radius: 12px !important; }
.bg-bronze { background-color: #CD7F32; color: white !important; }

@keyframes sparkle {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.7; transform: scale(1.1); }
}
.animate-sparkle {
    animation: sparkle 1.5s infinite ease-in-out;
}

.table th {
        background: #004392 !important;
        color: #fff !important;
    }
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>
@endsection
