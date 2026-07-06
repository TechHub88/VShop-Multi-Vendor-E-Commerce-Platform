@extends('vshop.seller.main', ['title' => 'Sales Report'])

@section('content')

<div class="container-fluid py-4">

    {{-- ================= HEADER ================= --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Seller Sales Report</h4>

        <div>
            <a href="{{ route('seller.reports.export.csv') }}" class="btn btn-sm btn-success">Export CSV</a>
            <a href="{{ route('seller.reports.export.pdf') }}" class="btn btn-sm btn-danger">Export PDF</a>
        </div>
    </div>


    {{-- ================= SUMMARY CARDS ================= --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card shadow-sm text-center p-3">
                <h6 class="text-muted">Total Sales</h6>
                <h3 class="text-success">₹{{ number_format($grandTotal, 2) }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm text-center p-3">
                <h6 class="text-muted">Total Orders</h6>
                <h3>{{ $orders->count() }}</h3>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm text-center p-3">
                <h6 class="text-muted">Average Order</h6>
                <h3>
                    ₹{{ $orders->count() ? number_format($grandTotal / $orders->count(), 2) : '0.00' }}
                </h3>
            </div>
        </div>

    </div>


    {{-- ================= CHARTS ================= --}}
    <div class="row mb-4">

    {{-- Line Chart --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header"><b>Daily Sales</b></div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="salesLineChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Pie Chart --}}
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-header"><b>Payment Status</b></div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="statusPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>


    {{-- ================= ORDER TABLE ================= --}}
    <div class="card shadow-sm">
        <div class="card-header"><b>All Orders</b></div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($orders as $order)
                        <tr>
                            <td>#{{ $order->id }}</td>

                            <td>
                                {{ $order->customer->name ?? 'N/A' }} <br>
                                <small class="text-muted">{{ $order->customer->email ?? '' }}</small>
                            </td>

                            <td class="fw-bold text-success">
                                ₹{{ number_format($order->total_amount, 2) }}
                            </td>

                            <td>
                                <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>

                            <td>
                                <span class="badge bg-info">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td>
                                {{ $order->created_at->format('d M Y') }}<br>
                                <small>{{ $order->created_at->format('h:i A') }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">No orders found</td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>
<style>
.chart-container {
    position: relative;
    height: 250px;
    width: 100%;
}
</style>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    /* ================= SMALL LINE CHART ================= */

    const salesLabels = @json($sales->pluck('date'));
    const salesData = @json($sales->pluck('amount'));

    const ctxLine = document.getElementById('salesLineChart');

    new Chart(ctxLine, {
        type: 'line',
        data: {
            labels: salesLabels,
            datasets: [{
                label: 'Sales',
                data: salesData,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78,115,223,0.1)',
                borderWidth: 2,
                fill: true,
                tension: 0.4,
                pointRadius: 3,
                pointBackgroundColor: '#4e73df'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: "rgba(0,0,0,0.05)" }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });


    /* ================= SMALL PIE CHART ================= */

    const statusLabels = @json($orders->groupBy('payment_status')->map->count()->keys());
    const statusData   = @json($orders->groupBy('payment_status')->map->count()->values());

    const ctxPie = document.getElementById('statusPieChart');

    new Chart(ctxPie, {
        type: 'doughnut',
        data: {
            labels: statusLabels,
            datasets: [{
                data: statusData,
                backgroundColor: [
                    '#36b9cc',   // teal
                    '#f6c23e',   // soft yellow
                    '#e74a3b',   // red
                    '#858796'    // grey
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            cutout: '60%',   // makes donut smaller
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12
                    }
                }
            }
        }
    });

});
</script>
@endsection
