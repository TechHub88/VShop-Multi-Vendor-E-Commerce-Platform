{{-- resources/views/admin/analytics/dropshipping-analytics.blade.php --}}
@extends('layouts.vertical', ['title' => 'Dropshipping Analytics'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">📦 Dropshipping Analytics - Supplier Performance</h4>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="GET" action="{{ route('admin.analytics.dropshipping') }}" class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate->format('Y-m-d') ?? date('Y-m-d', strtotime('-30 days')) }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate->format('Y-m-d') ?? date('Y-m-d') }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="submit" class="btn btn-primary form-control">Apply Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Cards - Blue Shades -->
    <div class="row">
        <div class="col-md-4">
            <div class="card" style="background: linear-gradient(135deg, #3b7ddd 0%, #2563eb 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ri-money-dollar-circle-line fs-2"></i>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0">Total Dropship Revenue</h6>
                            <h2 class="mb-0">₹{{ number_format($dropshippingRevenue ?? 0, 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="background: linear-gradient(135deg, #2c5f8a 0%, #1a3a52 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ri-shopping-cart-line fs-2"></i>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0">Total Orders</h6>
                            <h2 class="mb-0">{{ number_format($totalDropshipOrders ?? 0) }}</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card" style="background: linear-gradient(135deg, #4a90c4 0%, #2c6288 100%); color: white;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="ri-truck-line fs-2"></i>
                        </div>
                        <div class="text-end">
                            <h6 class="mb-0">Delivery Success Rate</h6>
                            <h2 class="mb-0">{{ number_format($overallDeliveryRate ?? 85.5, 1) }}%</h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Performance -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%); color: white;">
                    <h5 class="mb-0">🏭 Supplier-wise Performance</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead style="background: #2563eb; color: white;">
                                <tr>
                                    <th>Supplier Name</th>
                                    <th>Total Orders</th>
                                    <th>Total Revenue</th>
                                    <th>Delivery Success Rate</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($supplierPerformance ?? [] as $supplier)
                                <tr>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ number_format($supplier->total_orders ?? 0) }}</td>
                                    <td>₹{{ number_format($supplier->total_revenue ?? 0, 0) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: {{ $supplier->delivery_success_rate ?? 0 }}%; background: {{ ($supplier->delivery_success_rate ?? 0) >= 90 ? '#28a745' : (($supplier->delivery_success_rate ?? 0) >= 70 ? '#ffc107' : '#dc3545') }};">
                                                {{ number_format($supplier->delivery_success_rate ?? 0, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php $rate = $supplier->delivery_success_rate ?? 0; @endphp
                                        @if($rate >= 90)
                                            <span class="badge" style="background: #28a745;">Excellent</span>
                                        @elseif($rate >= 70)
                                            <span class="badge" style="background: #ffc107; color: #1e3a5f;">Average</span>
                                        @else
                                            <span class="badge" style="background: #dc3545;">Needs Review</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button class="btn btn-sm" style="background: #3b7ddd; color: white; border: none;">Review</button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center">No supplier data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Returns by Supplier -->
    <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header" style="background: linear-gradient(135deg, #4a90c4 0%, #2c6288 100%); color: white;">
                    <h5 class="mb-0">🔄 Returns by Supplier</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead style="background: #1e3a5f; color: white;">
                                <tr>
                                    <th>Supplier</th>
                                    <th>Total Orders</th>
                                    <th>Returns Count</th>
                                    <th>Return %</th>
                                    <th>Risk Level</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($returnsBySupplier ?? [] as $supplier)
                                <tr>
                                    <td>{{ $supplier->name }}</td>
                                    <td>{{ number_format($supplier->total_orders ?? 0) }}</td>
                                    <td><span class="text-danger">{{ number_format($supplier->return_count ?? 0) }}</span></td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar" style="width: {{ $supplier->return_percentage ?? 0 }}%; background: #dc3545;">
                                                {{ number_format($supplier->return_percentage ?? 0, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php $retPct = $supplier->return_percentage ?? 0; @endphp
                                        @if($retPct >= 20)
                                            <span class="badge" style="background: #dc3545;">High Risk</span>
                                        @elseif($retPct >= 10)
                                            <span class="badge" style="background: #ffc107; color: #1e3a5f;">Medium Risk</span>
                                        @else
                                            <span class="badge" style="background: #28a745;">Low Risk</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($retPct >= 10)
                                            <button class="btn btn-sm" style="background: #fd7e14; color: white; border: none;">Quality Check</button>
                                        @else
                                            <button class="btn btn-sm" style="background: #3b7ddd; color: white; border: none;">View Details</button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center">No returns data available</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: rgba(59, 125, 221, 0.05);
    }
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .progress {
        background-color: #e9ecef;
        border-radius: 10px;
    }
</style>
@endsection