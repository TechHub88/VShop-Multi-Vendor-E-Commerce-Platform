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
                data-amount="245000">

                ₹245000

            </h3>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="kpi-card kpi-green">

            <p>Total Orders</p>

            <h3>
                1,245
            </h3>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="kpi-card kpi-orange">

            <p>Pending Settlements</p>

            <h3 class="money"
                data-amount="35400">

                ₹35400

            </h3>

        </div>

    </div>

    <div class="col-md-3 mb-3">

        <div class="kpi-card kpi-red">

            <p>Disputes Raised</p>

            <h3>
                18
            </h3>

        </div>

    </div>

</div>

{{-- ================= COMMISSION STRUCTURE ================= --}}
<div class="card shadow-sm mb-4">

    <div class="card-header fw-bold">
        📊 Commission Structure
    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <h6 class="mb-3">
                    Category-wise Commission
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
                        Based on promotion_discount
                    </div>

                </div>

                <span class="fw-bold text-success fs-5">
                    32%
                </span>

            </div>

            <div class="progress">

                <div class="progress-bar bg-success"
                     style="width:32%;">
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
                    data-original="4500"
                    data-amount="4500">

                    ₹4500

                </h4>

            </div>

            <div class="col-md-3">

                <h6>Gateway</h6>

                <h4 class="text-warning fee-breakdown money"
                    data-original="3200"
                    data-amount="3200">

                    ₹3200

                </h4>

            </div>

            <div class="col-md-3">

                <h6>Platform Fee</h6>

                <h4 class="text-info fee-breakdown money"
                    data-original="5600"
                    data-amount="5600">

                    ₹5600

                </h4>

            </div>

            <div class="col-md-3">

                <h6>Tax</h6>

                <h4 class="text-primary fee-breakdown money"
                    data-original="2800"
                    data-amount="2800">

                    ₹2800

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

                    <li class="list-group-item d-flex justify-content-between">
                        Electronics
                        <span class="badge bg-primary">42%</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        Fashion
                        <span class="badge bg-success">28%</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        Grocery
                        <span class="badge bg-warning">18%</span>
                    </li>

                    <li class="list-group-item d-flex justify-content-between">
                        Services
                        <span class="badge bg-danger">12%</span>
                    </li>

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

            <span class="badge bg-dark static-badge">
                Static Data
            </span>

        </div>

        <a href="{{ route('seller.finance.export') }}"
           class="btn btn-sm btn-outline-primary">

            Export Monthly Report

        </a>

    </div>

    <div class="table-responsive">

        <table class="table">

            <thead>

            <tr>
                <th>Order</th>
                <th>Product</th>
                <th>Amount</th>
                <th>Commission</th>
                <th>Fees</th>
                <th>Net</th>
                <th>Status</th>
               
            </tr>

            </thead>

            <tbody>

            {{-- ROW 1 --}}
            <tr class="fee-row"
                data-amount="4500"
                data-commission="450"
                data-logistics="100"
                data-gateway="50"
                data-platform="30"
                data-tax="20">

                <td>#1025</td>

                <td>Wireless Headphone</td>

                <td class="money"
                    data-amount="4500">
                    ₹4500
                </td>

                <td class="money"
                    data-amount="450">
                    ₹450
                </td>

                <td class="table-fees money"
                    data-original="200"
                    data-amount="200">
                    ₹200
                </td>

                <td class="table-net money"
                    data-original="3850"
                    data-amount="3850">
                    ₹3850
                </td>

                <td>
                    <span class="badge bg-success">
                        Matched
                    </span>
                </td>

             

            </tr>

            {{-- ROW 2 --}}
            <tr class="fee-row"
                data-amount="6200"
                data-commission="620"
                data-logistics="120"
                data-gateway="70"
                data-platform="60"
                data-tax="50">

                <td>#1026</td>

                <td>Fashion Combo</td>

                <td class="money"
                    data-amount="6200">
                    ₹6200
                </td>

                <td class="money"
                    data-amount="620">
                    ₹620
                </td>

                <td class="table-fees money"
                    data-original="300"
                    data-amount="300">
                    ₹300
                </td>

                <td class="table-net money"
                    data-original="5280"
                    data-amount="5280">
                    ₹5280
                </td>

                <td>
                    <span class="badge bg-warning">
                        Mismatch
                    </span>
                </td>

               

            </tr>

            {{-- ROW 3 --}}
            <tr class="fee-row"
                data-amount="2400"
                data-commission="240"
                data-logistics="40"
                data-gateway="20"
                data-platform="20"
                data-tax="20">

                <td>#1027</td>

                <td>Digital Service</td>

                <td class="money"
                    data-amount="2400">
                    ₹2400
                </td>

                <td class="money"
                    data-amount="240">
                    ₹240
                </td>

                <td class="table-fees money"
                    data-original="100"
                    data-amount="100">
                    ₹100
                </td>

                <td class="table-net money"
                    data-original="2060"
                    data-amount="2060">
                    ₹2060
                </td>

                <td>
                    <span class="badge bg-success">
                        Matched
                    </span>
                </td>

             
            </tr>

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
            data:[
                'Electronics',
                'Fashion',
                'Groceries',
                'Services'
            ]
        },

        yAxis:{
            type:'value'
        },

        series:[{
            type:'bar',
            data:[12,18,9,15],
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
            data:[
                {value:65,name:'Product'},
                {value:35,name:'Service'}
            ]
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
            data:[
                {value:55,name:'Dropship'},
                {value:45,name:'Inventory'}
            ]
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
            data:[
                'Jan',
                'Feb',
                'Mar',
                'Apr',
                'May',
                'Jun'
            ]
        },

        yAxis:{
            type:'value'
        },

        series:[{
            type:'line',
            smooth:true,
            areaStyle:{},
            data:[
                12000,
                18000,
                15000,
                24000,
                28000,
                32000
            ]
        }]

    });

});

/* ================= BACK ================= */

function goBack(){

    window.history.back();

}

</script>

@endsection