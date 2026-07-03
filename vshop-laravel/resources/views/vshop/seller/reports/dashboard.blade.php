@extends('vshop.seller.main', ['title' => 'Seller Reports'])

@section('content')

<div class="container-fluid py-4">

    {{-- ================= SUMMARY CARDS ================= --}}
    <div class="row g-3 mb-3">

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="text-muted">Total Sales</h6>
                <h4 class="text-success mb-0">₹{{ number_format($total_sales, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="text-muted">Total Orders</h6>
                <h4 class="mb-0">{{ $total_orders }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="text-muted">Total Products</h6>
                <h4 class="mb-0">{{ $total_products }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="text-muted">Conversion Rate</h6>
                <h4 class="mb-0">0%</h4>
            </div>
        </div>

    </div>


    {{-- ================= RECENT ORDERS ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0">Recent Orders</h6>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Product</th>
                            <th>Amount</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>

                        @forelse($recent_orders as $item)
                        <tr>
                            <td>#{{ $item->order_id }}</td>

                            <td>
                                {{ $item->order->customer->name ?? 'N/A' }}
                            </td>

                            <td>
                                {{ $item->product->name ?? 'N/A' }}
                            </td>

                            <td class="text-success fw-bold">
                                ₹{{ number_format($item->total, 2) }}
                            </td>

                            <td>
                                {{ $item->created_at->format('d M Y') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">
                                No recent orders found
                            </td>
                        </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>

        </div>
    </div>


    {{-- ================= SALES CHART ================= --}}
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="mb-0">Sales Overview</h6>
        </div>

        <div class="card-body">
            <div id="salesChart" style="height:300px"></div>
        </div>
    </div>


    {{-- ================= TOP PRODUCTS ================= --}}
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h6 class="mb-0">Top Selling Products</h6>
        </div>

        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Product</th>
                            <th>Units Sold</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($top_products as $p)
                        <tr>
                            <td>{{ $p->product->name ?? 'N/A' }}</td>
                            <td>{{ $p->total_qty }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="text-center text-muted">
                                No data available
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>


{{-- ================= APEX CHART ================= --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {

    new ApexCharts(document.querySelector("#salesChart"), {
        chart: { type: 'line', height: 300 },
        series: [{
            name: "Sales",
            data: @json($sales_chart->pluck('total'))
        }],
        xaxis: {
            categories: @json($sales_chart->pluck('date'))
        }
    }).render();

});
</script>

@endsection
