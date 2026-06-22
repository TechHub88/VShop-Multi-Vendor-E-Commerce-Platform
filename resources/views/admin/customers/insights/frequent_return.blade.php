@extends('layouts.vertical', ['title' => 'Insights: Frequent Return Users'])

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h4 class="fw-bold text-dark mb-0"> Frequent Return Users</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item">Insights</li>
                <li class="breadcrumb-item active" aria-current="page">Frequent Returns</li>
            </ol>
        </nav>
    </div>


    <!-- Return Users Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light py-3">
                        <tr>
                            <th class="ps-4">Customer</th>
                            <th>Brand</th>
                            <th>Total Orders Placed</th>
                            <th>Returned Orders</th>
                            <th>Return Rate Ratio</th>
                            <th class="pe-4 text-end">Risk Assessment</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            @php
                                $returnRate = $customer->orders_count > 0 ? ($customer->returns_count / $customer->orders_count) * 100 : 0;
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
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
                                    <span class="badge bg-purple bg-opacity-10 text-purple border border-purple border-opacity-25 px-2.5 py-1">
                                        {{ $customer->brand->name ?? 'No Brand' }}
                                    </span>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-semibold text-dark">{{ $customer->orders_count }}</h6>
                                    <small class="text-muted fs-11">Total orders</small>
                                </td>
                                <td>
                                    <h6 class="mb-0 fw-semibold text-danger">{{ $customer->returns_count }}</h6>
                                    <small class="text-muted fs-11">Returns logged</small>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="flex-grow-1 me-2" style="max-width: 100px;">
                                            <div class="progress" style="height: 6px; border-radius: 3px;">
                                                <div class="progress-bar bg-{{ $returnRate > 40 ? 'danger' : ($returnRate > 20 ? 'warning' : 'info') }}" 
                                                     role="progressbar" 
                                                     style="width: {{ min($returnRate, 100) }}%;" 
                                                     aria-valuenow="{{ $returnRate }}" 
                                                     aria-valuemin="0" 
                                                     aria-valuemax="100">
                                                </div>
                                            </div>
                                        </div>
                                        <span class="fw-bold text-{{ $returnRate > 40 ? 'danger' : ($returnRate > 20 ? 'warning' : 'info') }}">
                                            {{ number_format($returnRate, 1) }}%
                                        </span>
                                    </div>
                                </td>
                                <td class="pe-4 text-end">
                                    @if($returnRate > 40)
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-1.5 fs-11 rounded-3">
                                            <i class="fas fa-exclamation-triangle me-1"></i> High Return Risk
                                        </span>
                                    @elseif($returnRate > 20)
                                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 px-3 py-1.5 fs-11 rounded-3" style="color: #856404 !important;">
                                            <i class="fas fa-shield-alt me-1"></i> Moderate Risk
                                        </span>
                                    @else
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1.5 fs-11 rounded-3">
                                            <i class="fas fa-check-circle me-1"></i> Normal Behavior
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-undo-alt fa-3x mb-3 text-muted"></i>
                                    <h5 class="text-dark">No returns analytics recorded</h5>
                                    <p class="text-muted">Customers who return products will appear here for return risk analysis.</p>
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
.rounded-4 { border-radius: 12px !important; }

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
