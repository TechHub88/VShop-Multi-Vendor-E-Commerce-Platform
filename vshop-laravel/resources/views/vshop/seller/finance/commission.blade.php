@extends('vshop.seller.main')

<style>

.card{
    background:#fff;
    padding:25px;
    border-radius:14px;
    box-shadow:0 8px 25px rgba(0,0,0,0.05);
    position:relative;
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
}

/* ================= KPI ================= */

.kpi-card{
    background:#fff;
    border-radius:14px;
    padding:22px;
    position:relative;
    overflow:hidden;
    min-height:120px;
    border:1px solid #eef1f5;
    box-shadow:0 8px 25px rgba(0,0,0,0.05);
    transition:.3s;
}

.kpi-card:hover{
    transform:translateY(-5px);
}

.kpi-card p{
    margin:0;
    color:#6c757d;
    font-size:14px;
    font-weight:500;
}

.kpi-card h3{
    margin-top:10px;
    margin-bottom:0;
    font-size:30px;
    font-weight:700;
    color:#212529;
}

.kpi-blue{
    border-left:5px solid #4e73df;
}

.kpi-green{
    border-left:5px solid #1cc88a;
}

.kpi-orange{
    border-left:5px solid #f6c23e;
}

.kpi-red{
    border-left:5px solid #e74a3b;
}

/* ================= TABLE ================= */

.table th{
    font-weight:600;
    color:#fff !important;
    background-color:#212529 !important;
    border:1px solid #fff;
}

.table tbody td{
    border:1px solid #dee2e6;
    vertical-align:middle;
}

.nav-btn{
    border:1px solid #004392;
    color:#004392;
    background:transparent;
    border-radius:5px;
    font-size:14px;
    width:35px;
    height:38px;
}

.top-back-heading-sec{
    align-items:center;
    gap:10px;
}

.progress{
    height:10px;
    border-radius:20px;
}

.static-badge{
    font-size:11px;
    padding:5px 10px;
}

.chart-box div {
    width: 100% !important;
    height: 100% !important;
}
.chart-box div canvas {
    width: 100% !important;
    height: 100% !important;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}

.card,
.kpi-card {
    border: 1px solid rgb(201 208 218) !important;
}
.table th {
    font-weight: 600;
    color: #fff !important;
    background-color: #004392 !important;
    border: 1px solid #fff;
}
.bg-dark  {
    background-color: #61b3e5 !important;
    border-color: #233d78 !important;
    color: #fff !important;
}
.btn-outline-primary {
     border-color: #004392 !important;
    color: #004392 !important;
}
.btn-outline-primary:hover {
    background: #004392 !important;
    color: #fff !important;
}
    

</style>

@section('content')

<div class="container-fluid py-4">

{{-- ================= HEADER ================= --}}
<div class="d-flex justify-content-between align-items-center mb-4">

    <div class="d-flex top-back-heading-sec">

        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h5 class="fw-bold mb-0">
            Commission & Fees Dashboard
        </h5>

    </div>

    <div class="d-flex gap-2">

        {{-- CURRENCY --}}
        <select id="currencySwitcher"
                class="form-select form-select-sm"
                style="width:120px;">

            <option value="INR">₹ INR</option>
            <option value="USD">$ USD</option>
            <option value="EUR">€ EUR</option>

        </select>

        {{-- BUNDLE --}}
        <select id="bundleToggle"
                class="form-select form-select-sm"
                style="width:170px;">

            <option value="bundled">
                Bundled Fees
            </option>

            <option value="unbundled">
                Unbundled Fees
            </option>

        </select>

    </div>

</div>

{{-- ================= KPI CARDS ================= --}}
<div class="row mb-4">

    <div class="col-md-3 mb-3">

        <div class="kpi-card kpi-blue">

            <p>Total Revenue</p>

            <h3 class="money"
                data-amount="{{ $totalRevenue }}">

                ₹{{ number_format($totalRevenue, 2) }}

            </h3>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="kpi-card kpi-green">

            <p>Total Orders</p>

            <h3>
                {{ number_format($totalOrders) }}
            </h3>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="kpi-card kpi-orange">

            <p>Pending Settlements</p>

            <h3 class="money"
                data-amount="{{ $pendingSettlements }}">

                ₹{{ number_format($pendingSettlements, 2) }}

            </h3>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="kpi-card kpi-red">

            <p>Disputes Raised</p>

            <h3>
                {{ $disputesRaised }}
            </h3>

        </div>

    </div>

</div>

{{-- ================= COMMISSION STRUCTURE ================= --}}
<div class="card shadow-sm mb-4">

    <div class="card-header fw-bold d-flex justify-content-between align-items-center">
        <span>📊 Commission Structure</span>
        <span class="badge bg-primary px-3 py-2" style="font-size: 13px;">
            Seller Commission: {{ $sellerCommissionPct !== null ? number_format($sellerCommissionPct, 2) . '%' : 'N/A' }}
            @if($platformCommissionPct !== null)
                | Platform Fee: {{ number_format($platformCommissionPct, 2) }}%
            @endif
        </span>
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <h6 class="mb-3">
                    Category-wise Commission Rate (%)
                </h6>

                <div id="categoryChart"
                     style="height:260px;"></div>

            </div>

            <div class="col-md-3">

                <h6 class="mb-3">
                    Product vs Service
                </h6>

                <div id="productChart"
                     style="height:240px;"></div>

            </div>

            <div class="col-md-3">

                <h6 class="mb-3">
                    Dropship vs Inventory
                </h6>

                <div id="inventoryChart"
                     style="height:240px;"></div>

            </div>

        </div>

        {{-- PROMOTIONAL WAIVER --}}
        <div class="mt-4 p-3 rounded border bg-light">

            <div class="d-flex justify-content-between align-items-center mb-2">

                <div>

                    <strong>
                        Promotional Commission Waiver
                    </strong>

                    <div class="text-muted small">
                        Based on promotion_discount & referral allocation
                    </div>

                </div>

                <span class="fw-bold text-success fs-5">
                    {{ $promoWaiverRate }}%
                </span>

            </div>

            <div class="progress">

                <div class="progress-bar bg-success"
                     style="width:{{ min(100, max(0, $promoWaiverRate * 3)) }}%;">
                </div>

            </div>

        </div>

    </div>

</div>

{{-- ================= FEE BREAKDOWN ================= --}}
<div class="card shadow-sm mb-4">

    <div class="card-header fw-bold">
        💸 Fee Breakdown
    </div>

    <div class="card-body">

        <div class="row text-center">

            <div class="col-md-3">

                <h6>Logistics</h6>

                <h4 class="text-danger fee-breakdown money"
                    data-original="{{ $logisticsFee }}"
                    data-amount="{{ $logisticsFee }}">

                    ₹{{ number_format($logisticsFee, 2) }}

                </h4>

            </div>

            <div class="col-md-3">

                <h6>Gateway</h6>

                <h4 class="text-warning fee-breakdown money"
                    data-original="{{ $gatewayFee }}"
                    data-amount="{{ $gatewayFee }}">

                    ₹{{ number_format($gatewayFee, 2) }}

                </h4>

            </div>

            <div class="col-md-3">

                <h6>Platform Fee</h6>

                <h4 class="text-info fee-breakdown money"
                    data-original="{{ $platformFee }}"
                    data-amount="{{ $platformFee }}">

                    ₹{{ number_format($platformFee, 2) }}

                </h4>

            </div>

            <div class="col-md-3">

                <h6>Tax</h6>

                <h4 class="text-primary fee-breakdown money"
                    data-original="{{ $taxFee }}"
                    data-amount="{{ $taxFee }}">

                    ₹{{ number_format($taxFee, 2) }}

                </h4>

            </div>

        </div>

    </div>

</div>

{{-- ================= EXTRA ANALYTICS ================= --}}
<div class="row mb-4">

    <div class="col-md-6 mb-3">

        <div class="card shadow-sm">

            <div class="card-header fw-bold">
                📈 Monthly Revenue Trend
            </div>

            <div class="card-body">

                <div id="revenueTrendChart"
                     style="height:300px;"></div>

            </div>

        </div>

    </div>

    <div class="col-md-6 mb-3">

        <div class="card shadow-sm">

            <div class="card-header fw-bold">
                🏆 Top Performing Categories
            </div>

            <div class="card-body">

                <ul class="list-group">

                    @forelse($topPerformingCategories as $tc)
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            {{ $tc->name }}
                            <span class="badge {{ $tc->color_class }}">{{ $tc->percentage }}%</span>
                        </li>
                    @empty
                        <li class="list-group-item text-muted text-center">No categories found for seller</li>
                    @endforelse

                </ul>

            </div>

        </div>

    </div>

</div>

{{-- ================= TABLE ================= --}}
<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">

        <div class="d-flex align-items-center gap-2">

            <span class="fw-bold">
                🔍 Fee Reconciliation
            </span>

            @if($hasCommissionData)
                <span class="badge bg-success static-badge">
                    Seller Commission Rate: {{ number_format($sellerCommissionPct, 2) }}%
                </span>
            @else
                <span class="badge bg-danger static-badge">
                    No Commission Rule Found
                </span>
            @endif

        </div>

        <a href="{{ route('seller.finance.export') }}"
           class="btn btn-sm btn-outline-primary">

            Export Monthly Report

        </a>

    </div>

    <div class="table-responsive">

        <table class="table mb-0">

            <thead>

            <tr>
                <th>Order / Rule</th>
                <th>Product / Category</th>
                <th>Amount</th>
                <th>Seller Commission ({{ $sellerCommissionPct !== null ? number_format($sellerCommissionPct, 2).'%' : 'N/A' }})</th>
                <th>Fees</th>
                <th>Net Earnings</th>
                <th>Status</th>
            </tr>

            </thead>

            <tbody>

            @forelse($reconciliationOrders as $ord)
                @php
                    $amt = (float)($ord->total_amount ?? $ord->sub_total ?? 0);
                    $commPct = $sellerCommissionPct ?? 0;
                    $comm = round($amt * ($commPct / 100), 2);
                    $logistics = round($amt * 0.02, 2);
                    $gateway = round($amt * 0.015, 2);
                    $platform = $platformCommissionPct !== null ? round($amt * ($platformCommissionPct / 100), 2) : 0;
                    $tax = round(($logistics + $gateway + $platform) * 0.18, 2);
                    $totFees = $logistics + $gateway + $platform + $tax;
                    $net = $comm; // Seller earnings based on seller commission
                @endphp
                <tr class="fee-row"
                    data-amount="{{ $amt }}"
                    data-commission="{{ $comm }}"
                    data-logistics="{{ $logistics }}"
                    data-gateway="{{ $gateway }}"
                    data-platform="{{ $platform }}"
                    data-tax="{{ $tax }}">

                    <td>#ORD-{{ $ord->id }}</td>

                    <td>Order Item #{{ $ord->id }}</td>

                    <td class="money" data-amount="{{ $amt }}">
                        ₹{{ number_format($amt, 2) }}
                    </td>

                    <td class="money" data-amount="{{ $comm }}">
                        ₹{{ number_format($comm, 2) }}
                    </td>

                    <td class="table-fees money" data-original="{{ $totFees }}" data-amount="{{ $totFees }}">
                        ₹{{ number_format($totFees, 2) }}
                    </td>

                    <td class="table-net money" data-original="{{ $net }}" data-amount="{{ $net }}">
                        ₹{{ number_format($net, 2) }}
                    </td>

                    <td>
                        <span class="badge {{ in_array($ord->status, ['completed', 'paid', 'delivered']) ? 'bg-success' : 'bg-warning' }}">
                            {{ ucfirst($ord->status ?? 'Matched') }}
                        </span>
                    </td>
                </tr>
            @empty
                @forelse($commissionRules as $rule)
                    @php
                        $amt = (float)($rule->total_sales > 0 ? $rule->total_sales : 0);
                        $commPct = (float)($rule->seller_commission ?? 0);
                        $comm = round($amt * ($commPct / 100), 2);
                        $totFees = round($amt * 0.05, 2);
                        $net = $comm;
                    @endphp
                    <tr class="fee-row"
                        data-amount="{{ $amt }}"
                        data-commission="{{ $comm }}"
                        data-logistics="0"
                        data-gateway="0"
                        data-platform="0"
                        data-tax="0">

                        <td>Rule #{{ $rule->id }}</td>

                        <td>{{ ucfirst($rule->app_type ?? 'vshop') }} - {{ ucfirst($rule->sell_type ?? 'direct') }}</td>

                        <td class="money" data-amount="{{ $amt }}">
                            ₹{{ number_format($amt, 2) }}
                        </td>

                        <td class="money" data-amount="{{ $comm }}">
                            ₹{{ number_format($comm, 2) }} ({{ number_format($commPct, 2) }}%)
                        </td>

                        <td class="table-fees money" data-original="{{ $totFees }}" data-amount="{{ $totFees }}">
                            ₹{{ number_format($totFees, 2) }}
                        </td>

                        <td class="table-net money" data-original="{{ $net }}" data-amount="{{ $net }}">
                            ₹{{ number_format($net, 2) }}
                        </td>

                        <td>
                            <span class="badge bg-success">
                                Active Rule
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted fw-semibold">
                            <i class="fa-solid fa-circle-exclamation text-warning me-2 fs-5"></i>
                            No commission records or rules configured for this seller in <code>commission_management</code> table.
                        </td>
                    </tr>
                @endforelse
            @endforelse

            </tbody>

        </table>

    </div>

</div>

</div>

{{-- ================= ECHART ================= --}}
<script src="https://cdn.jsdelivr.net/npm/echarts@5"></script>

<script>

document.addEventListener("DOMContentLoaded",function(){

    /* ===================== CURRENCY ===================== */

    let currentCurrency = 'INR';

    const currencyRates = {
        INR:1,
        USD:0.012,
        EUR:0.011
    };

    const currencySymbols = {
        INR:'₹',
        USD:'$',
        EUR:'€'
    };

    function updateCurrency(){

        let rate = currencyRates[currentCurrency];
        let symbol = currencySymbols[currentCurrency];

        document.querySelectorAll('.money')
        .forEach(el=>{

            let amount = parseFloat(el.dataset.amount);

            if(isNaN(amount)) return;

            el.innerHTML =
                symbol +
                (amount * rate).toFixed(2);

        });

    }

    document.getElementById('currencySwitcher')
    .addEventListener('change',function(){

        currentCurrency = this.value;

        updateCurrency();

    });

    /* ===================== BUNDLE ===================== */

   function updateBundleView(type){

    let symbol = currencySymbols[currentCurrency];
    let rate = currencyRates[currentCurrency];

    document.querySelectorAll('.fee-row')
    .forEach(row=>{

        /* ================= GET VALUES ================= */

        let amount =
            parseFloat(row.dataset.amount);

        let commission =
            parseFloat(row.dataset.commission);

        let logistics =
            parseFloat(row.dataset.logistics);

        let gateway =
            parseFloat(row.dataset.gateway);

        let platform =
            parseFloat(row.dataset.platform);

        let tax =
            parseFloat(row.dataset.tax);

        let totalFees =
            logistics +
            gateway +
            platform +
            tax;

        /* ================= CELLS ================= */

        let feeCell =
            row.querySelector('.table-fees');

        let netCell =
            row.querySelector('.table-net');

        /* =====================================================
           BUNDLED MODE
        ===================================================== */

        if(type === 'bundled'){

            feeCell.innerHTML =
                '<span class="badge bg-success">Bundled</span>';

            let bundledNet =
                amount - commission;

            netCell.dataset.amount =
                bundledNet;

            netCell.innerHTML =
                symbol +
                (bundledNet * rate).toFixed(2);

        }

        /* =====================================================
           UNBUNDLED MODE
        ===================================================== */

        else{

            feeCell.dataset.amount =
                totalFees;

            feeCell.innerHTML =
                symbol +
                (totalFees * rate).toFixed(2);

            let unbundledNet =
                amount -
                commission -
                totalFees;

            netCell.dataset.amount =
                unbundledNet;

            netCell.innerHTML =
                symbol +
                (unbundledNet * rate).toFixed(2);

        }

    });

}

    document.getElementById('bundleToggle')
    .addEventListener('change',function(){

        updateBundleView(this.value);

    });

    /* ================= INITIAL ================= */

    updateCurrency();

    updateBundleView(
        document.getElementById('bundleToggle').value
    );

    /* ================= CATEGORY CHART ================= */

    echarts.init(
        document.getElementById('categoryChart')
    ).setOption({

        tooltip:{
            trigger:'axis'
        },

        xAxis:{
            type:'category',
            data: @json($categoryChartLabels)
        },

        yAxis:{
            type:'value'
        },

        series:[{
            type:'bar',
            data: @json($categoryChartData),
            color:'#4e73df',
            label:{
                show:true,
                position:'top'
            }
        }]

    });

    /* ================= PRODUCT PIE ================= */

    echarts.init(
        document.getElementById('productChart')
    ).setOption({

        tooltip:{
            trigger:'item'
        },

        series:[{
            type:'pie',
            radius:['50%','75%'],
            data: @json($productVsService)
        }]

    });

    /* ================= INVENTORY PIE ================= */

    echarts.init(
        document.getElementById('inventoryChart')
    ).setOption({

        tooltip:{
            trigger:'item'
        },

        series:[{
            type:'pie',
            radius:'70%',
            data: @json($dropshipVsInventory)
        }]

    });

    /* ================= TREND ================= */

    echarts.init(
        document.getElementById('revenueTrendChart')
    ).setOption({

        tooltip:{
            trigger:'axis'
        },

        xAxis:{
            type:'category',
            data: @json($trendLabels)
        },

        yAxis:{
            type:'value'
        },

        series:[{
            type:'line',
            smooth:true,
            areaStyle:{},
            data: @json($trendData)
        }]

    });

});

/* ================= BACK ================= */

function goBack(){

    window.history.back();

}

</script>

@endsection