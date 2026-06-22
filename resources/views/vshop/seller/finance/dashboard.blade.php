@extends('vshop.seller.main')

@section('content')

<style>
/*.container-fluid{padding:25px;background:#f5f6fa;font-weight:600;}*/
.card{background:#fff;border-radius:12px;padding:18px;box-shadow:0 5px 15px rgba(0,0,0,0.05);}
.big-kpi{font-size:26px;color:#000;}
.sub-text{font-size:13px;color:#666;}
.table td{font-size:13px;}
.badge-warning{background:#fff5e6;color:#f59e0b;padding:5px 10px;border-radius:6px;}
.badge-danger{background:#ffeaea;color:#dc3545;padding:5px 10px;border-radius:6px;}
.chart-box{height:260px;}

.switch{position:relative;width:50px;height:26px;}
.switch input{display:none;}
.slider{position:absolute;background:#ccc;border-radius:50px;width:100%;height:100%;}
.slider:before{content:"";position:absolute;width:20px;height:20px;top:3px;left:3px;background:#fff;border-radius:50%;transition:.3s;}
input:checked + .slider{background:#0d6efd;}
input:checked + .slider:before{transform:translateX(24px);}

.earn-tabs{
    display:flex;
    gap:10px;
    flex-wrap:wrap;
}
.earn-tabs button{
    border:none;
    padding:6px 12px;
    border-radius:6px;
    background:#eee;
    font-size:13px;
    cursor:pointer;
}
.earn-tabs button.active{
    background:#0d6efd;
    color:#fff;
}

.card {
    transition: 0.3s;
}
.card:hover {
    transform: translateY(-4px);
}
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    font-size: 4px;
    width: 35px;
    height: 38px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.chart-box div {
    width: 100% !important;
    height: 100% !important;
}
.chart-box div canvas {
    width: 100% !important;
    height: 100% !important;
    object-fit: contain;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
.earn-tabs button.active {
    background: #233d78;
    color: #fff;
}
.apply-btn {
    min-width: auto;
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    color: #fff;
    border: 0;
    padding: 8px 15px;
    border-radius: 5px;
}
#customRangeBox input {
    height: 40px;
    border-radius: 5px;
    padding: 4px 6px;
}

/* Commission Table Styles */
.commission-card,
.commission-export-card{
    border:1px solid #dbe3ef;
    border-radius:16px;
    background:#fff;
    box-shadow:0 8px 22px rgba(15,23,42,0.05);
    padding: 20px;
}

.commission-filter{
    min-width:140px;
    border-radius:10px;
    border:1px solid #d0d9e5;
    font-size:13px;
    height:38px;
}

.commission-table{
    margin-bottom:0;
}

.commission-table thead th{
    background:#f3f6fb !important;
    color:#1e293b !important;
    font-size:12px;
    font-weight:700;
    border-bottom:1px solid #dbe3ef !important;
    white-space:nowrap;
}

.commission-table tbody td{
    font-size:13px;
    vertical-align:middle;
    border-color:#edf2f7 !important;
    white-space:nowrap;
}

.commission-table tbody tr{
    transition:.2s;
}

.commission-table tbody tr:hover{
    background:#f8fbff;
}

.source-img{
    width:34px;
    height:34px;
    border-radius:10px;
    background:#f5f7fb;
    border:1px solid #e2e8f0;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}

.source-img img{
    width:20px;
    height:20px;
    object-fit:contain;
}

.order-link{
    color:#2563eb;
    text-decoration:none;
    font-weight:700;
}

.order-link:hover{
    text-decoration:underline;
}

.export-btn{
    border:1px solid #dbe3ef;
    background:#fff;
    border-radius:14px;
    padding:14px 16px;
    display:flex;
    align-items:center;
    justify-content:space-between;
    transition:.25s;
    font-weight:600;
    width: 100%;
    cursor: pointer;
}

.export-btn:hover{
    transform:translateY(-2px);
    box-shadow:0 8px 20px rgba(0,0,0,0.08);
}

.export-left{
    display:flex;
    align-items:center;
    gap:12px;
}

.export-icon{
    width:42px;
    height:42px;
    border-radius:12px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-size:18px;
    color:#fff;
}

.csv-btn .export-icon{
    background:linear-gradient(180deg,#3b82f6,#1d4ed8);
}

.excel-btn .export-icon{
    background:linear-gradient(180deg,#16a34a,#15803d);
}

.pdf-btn .export-icon{
    background:linear-gradient(180deg,#ef4444,#b91c1c);
}

.csv-btn:hover{
    border-color:#3b82f6;
}

.excel-btn:hover{
    border-color:#16a34a;
}

.pdf-btn:hover{
    border-color:#ef4444;
}

.dispute-table {
    font-size: 13px;
}
.dispute-table td {
    padding: 10px 5px;
}

@media(max-width:991px){
    .commission-filter{
        width:100%;
    }
    .commission-table{
        min-width:1000px;
    }
}
</style>

<div class="container-fluid py-4">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h5 class="fw-bold m-0">Earning Dashboard</h5>
    </div>

    <div class="d-flex align-items-center gap-3">
        <select id="regionSwitcher" class="form-select w-auto">
            <option value="IN" {{ ($region ?? 'IN') == 'IN' ? 'selected' : '' }}>India</option>
            <option value="US" {{ ($region ?? 'IN') == 'US' ? 'selected' : '' }}>USA</option>
            <option value="EU" {{ ($region ?? 'IN') == 'EU' ? 'selected' : '' }}>Europe</option>
        </select>

        <select id="currencySwitcher" class="form-select w-auto">
            <option value="INR" {{ ($currency ?? 'INR') == 'INR' ? 'selected' : '' }}>₹ INR</option>
            <option value="USD" {{ ($currency ?? 'INR') == 'USD' ? 'selected' : '' }}>$ USD</option>
            <option value="EUR" {{ ($currency ?? 'INR') == 'EUR' ? 'selected' : '' }}>€ EUR</option>
        </select>

        <div class="d-flex align-items-center gap-2">
            <label class="switch m-0">
                <input type="checkbox" id="taxToggle" {{ ($taxEnabled ?? false) ? 'checked' : '' }}>
                <span class="slider"></span>
            </label>
            <small id="taxLabel" class="fw-semibold">Tax Exclusive</small>
        </div>
    </div>
</div>

{{-- EARNINGS SUMMARY --}}
<div class="card mb-3">
    <b>Earnings Summary</b>

    <div class="earn-tabs mt-2">
        <button data-range="daily" class="{{ ($range ?? 'monthly') == 'daily' ? 'active' : '' }}">Daily</button>
        <button data-range="weekly" class="{{ ($range ?? 'monthly') == 'weekly' ? 'active' : '' }}">Weekly</button>
        <button data-range="monthly" class="{{ (!isset($range) || $range == 'monthly') ? 'active' : '' }}">Monthly</button>
        <button data-range="custom" class="{{ ($range ?? '') == 'custom' ? 'active' : '' }}">Custom Range</button>
    </div>

    <div id="customRangeBox" style="display:{{ ($range ?? '') == 'custom' ? 'block' : 'none' }};margin-top:10px;">
        <input type="date" id="fromDate" value="{{ $fromDate ?? '' }}">
        <input type="date" id="toDate" value="{{ $toDate ?? '' }}">
        <button onclick="applyCustom()" class="apply-btn">Apply</button>
    </div>
</div>

<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <b>NET EARNINGS (After Deductions)</b>
            <div class="big-kpi money" data-base="{{ $balance ?? 0 }}" id="netEarnings"></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <b>Refund Deduction Impact</b>
            <div class="big-kpi text-danger money" data-base="{{ $refund ?? 0 }}" id="refundImpact"></div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card">
            <b>Platform Fees</b>
            <div class="big-kpi money" data-base="{{ $fees ?? 0 }}" id="platformFees"></div>
        </div>
    </div>
</div>

{{-- ANALYTICS CHARTS ROW --}}
<div class="row g-3 mt-2">
    <div class="col-md-8">
        <div class="card">
            <b>Revenue vs Net Earnings</b>
            <div id="trendChart" class="chart-box"></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <b>Category Distribution</b>
            <div id="categoryChart" class="chart-box"></div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card">
            <b>Net Earnings Trend</b>
            <div id="netChart" class="chart-box"></div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <b>Performance</b>
            <div id="barChart" class="chart-box"></div>
        </div>
    </div>
</div>

{{-- REVENUE BREAKDOWN & DISPUTE WALLET HOLD TRACKER (SIDE BY SIDE) --}}
<div class="row g-3 mt-2">
    <div class="col-md-6">
        <div class="card">
            <b>Revenue Breakdown</b>
            <div id="pieChart" class="chart-box"></div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <b>DISPUTE WALLET HOLD TRACKER</b>
            
            <div class="row mt-3">
                <div class="col-6">
                    <div class="big-kpi money" data-base="{{ $wallet_hold ?? 0 }}" id="walletHold"></div>
                    <div class="sub-text">Wallet Hold</div>
                </div>
                <div class="col-6">
                    <div class="big-kpi money" data-base="{{ $dispute_amount ?? 0 }}" id="disputeAmount"></div>
                    <div class="sub-text">Dispute Amount</div>
                </div>
            </div>

            <table class="table mt-3 dispute-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody id="disputeTableBody">
                    @foreach($disputes ?? [] as $d)
                    <tr>
                        <td>{{ $d->order_id ?? '-' }}</td>
                        <td class="money-dispute" data-base="{{ $d->debit ?? 0 }}">{{ $d->debit ?? 0 }}</td>
                        <td>
                            @if(($d->status ?? '') == 'hold')
                            <span class="badge-warning">On Hold</span>
                            @else
                            <span class="badge-danger">Under Review</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ================= COMMISSION BREAKDOWN VIEW ================= --}}
<div class="row g-3 mt-3">
    <div class="col-lg-9">
        <div class="commission-card">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h6 class="fw-bold mb-0">COMMISSION BREAKDOWN VIEW</h6>
                    <small class="text-muted">Detailed commission & deduction tracking</small>
                </div>

                <div class="d-flex gap-2">
                    <select id="commissionDateFilter" class="form-select form-select-sm commission-filter">
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_year">This Year</option>
                        <option value="all">All Time</option>
                    </select>

                    <select id="commissionSourceFilter" class="form-select form-select-sm commission-filter">
                        <option value="all">All Sources</option>
                        <option value="direct">Direct</option>
                        <option value="referral">Referral</option>
                        <option value="ads">Ads</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table commission-table align-middle" id="commissionTable">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Source</th>
                            <th>Order ID</th>
                            <th>Revenue</th>
                            <th>Referral Income</th>
                            <th>Lead Charges</th>
                            <th>Boost Spend</th>
                            <th>Refund Deduction</th>
                            <th>Net Income</th>
                        </tr>
                    </thead>
                    <tbody id="commissionTableBody"></tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-3">
        <div class="commission-export-card">
            <div class="mb-3">
                <h6 class="fw-bold mb-0">COMMISSION EXPORT</h6>
                <small class="text-muted">Download earning reports</small>
            </div>

            <div class="d-grid gap-3">
                <button class="export-btn csv-btn" onclick="exportData('csv')">
                    <div class="export-left">
                        <div class="export-icon">
                            <i class="fa-regular fa-file-lines"></i>
                        </div>
                        <span>Export: CSV</span>
                    </div>
                    <i class="fa-solid fa-download"></i>
                </button>

                <button class="export-btn excel-btn" onclick="exportData('excel')">
                    <div class="export-left">
                        <div class="export-icon">
                            <i class="fa-regular fa-file-excel"></i>
                        </div>
                        <span>Export: Excel</span>
                    </div>
                    <i class="fa-solid fa-download"></i>
                </button>

                <button class="export-btn pdf-btn" onclick="exportData('pdf')">
                    <div class="export-left">
                        <div class="export-icon">
                            <i class="fa-regular fa-file-pdf"></i>
                        </div>
                        <span>Export: PDF</span>
                    </div>
                    <i class="fa-solid fa-download"></i>
                </button>
            </div>
        </div>
    </div>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<script>
/* ================= STATE ================= */
let currency = '{{ $currency ?? "INR" }}';
let currentRegion = '{{ $region ?? "IN" }}';
let taxEnabled = {{ isset($taxEnabled) && $taxEnabled ? 'true' : 'false' }};

/* ================= SETTINGS ================= */
const rates = {INR: 1, USD: 0.012, EUR: 0.011};
const symbols = {INR: '₹', USD: '$', EUR: '€'};
const regionTax = {IN: 0.18, US: 0.07, EU: 0.20};

// Commission Data Store
let commissionData = [
    {date: '01 Nov 2023', source: 'Product A', order_id: '#12345', revenue: 1500, referral: 10, lead_charges: 0, boost_spend: 0, refund: 7, net: 1250},
    {date: '15 Nov 2023', source: 'Product B', order_id: '#12346', revenue: 1350, referral: 9, lead_charges: 0, boost_spend: 0, refund: 7, net: 1350},
    {date: '20 Nov 2023', source: 'Product C', order_id: '#12301', revenue: 1350, referral: 7, lead_charges: 0, boost_spend: 0, refund: 7, net: 1350},
    {date: '25 Nov 2023', source: 'Product B', order_id: '#12345', revenue: 1350, referral: 7, lead_charges: 0, boost_spend: 0, refund: 7, net: 1350},
    {date: '28 Nov 2023', source: 'Product A', order_id: '#12301', revenue: 1450, referral: 6, lead_charges: 0, boost_spend: 0, refund: 7, net: 1450},
    {date: '05 Dec 2023', source: 'Product D', order_id: '#12389', revenue: 2000, referral: 15, lead_charges: 0, boost_spend: 0, refund: 10, net: 2005},
    {date: '10 Dec 2023', source: 'Product E', order_id: '#12390', revenue: 800, referral: 5, lead_charges: 0, boost_spend: 0, refund: 3, net: 802}
];

/* ================= MONEY UPDATE FUNCTION ================= */
function updateMoney() {
    let taxRate = regionTax[currentRegion] || 0;
    let finalRate = rates[currency] || 1;
    let symbol = symbols[currency] || '₹';

    document.querySelectorAll('.money').forEach(function(el) {
        let base = parseFloat(el.dataset.base || 0);
        let val = taxEnabled ? base * (1 + taxRate) : base;
        let converted = val * finalRate;
        el.innerHTML = symbol + " " + converted.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    });

    document.querySelectorAll('.money-dispute').forEach(function(el) {
        let base = parseFloat(el.dataset.base || 0);
        let val = taxEnabled ? base * (1 + taxRate) : base;
        let converted = val * finalRate;
        el.innerHTML = symbol + " " + converted.toLocaleString(undefined, {minimumFractionDigits: 2, maximumFractionDigits: 2});
    });

    updateCommissionTableDisplay(symbol, finalRate, taxRate);
}

function updateCommissionTableDisplay(symbol, rate, taxRate) {
    let rows = document.querySelectorAll('#commissionTableBody tr');
    rows.forEach(function(row) {
        let revenueCell = row.querySelector('.rev-amount');
        let referralCell = row.querySelector('.ref-amount');
        let leadCell = row.querySelector('.lead-amount');
        let boostCell = row.querySelector('.boost-amount');
        let refundCell = row.querySelector('.refund-amount');
        let netCell = row.querySelector('.net-amount');
        
        if(revenueCell) {
            let base = parseFloat(revenueCell.dataset.base);
            let val = taxEnabled ? base * (1 + taxRate) : base;
            revenueCell.innerHTML = symbol + " " + (val * rate).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
        if(referralCell) {
            let base = parseFloat(referralCell.dataset.base);
            referralCell.innerHTML = symbol + " " + (base * rate).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
        if(leadCell) {
            let base = parseFloat(leadCell.dataset.base);
            leadCell.innerHTML = symbol + " " + (base * rate).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
        if(boostCell) {
            let base = parseFloat(boostCell.dataset.base);
            boostCell.innerHTML = symbol + " " + (base * rate).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
        if(refundCell) {
            let base = parseFloat(refundCell.dataset.base);
            refundCell.innerHTML = symbol + " " + (base * rate).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
        if(netCell) {
            let base = parseFloat(netCell.dataset.base);
            let val = taxEnabled ? base * (1 + taxRate) : base;
            netCell.innerHTML = symbol + " " + (val * rate).toLocaleString(undefined, {minimumFractionDigits: 2});
        }
    });
}

function updateTaxLabel() {
    document.getElementById('taxLabel').innerHTML = taxEnabled ? "Tax Inclusive" : "Tax Exclusive";
}

/* ================= COMMISSION TABLE FILTERS ================= */
function renderCommissionTable() {
    let dateFilter = document.getElementById('commissionDateFilter').value;
    let sourceFilter = document.getElementById('commissionSourceFilter').value;
    
    let filteredData = [...commissionData];
    
    let now = new Date();
    let currentYear = now.getFullYear();
    let currentMonth = now.getMonth();
    
    if(dateFilter !== 'all') {
        filteredData = filteredData.filter(function(item) {
            let itemDate = new Date(item.date);
            if(dateFilter === 'this_month') {
                return itemDate.getMonth() === currentMonth && itemDate.getFullYear() === currentYear;
            } else if(dateFilter === 'last_month') {
                let lastMonth = currentMonth === 0 ? 11 : currentMonth - 1;
                let lastMonthYear = currentMonth === 0 ? currentYear - 1 : currentYear;
                return itemDate.getMonth() === lastMonth && itemDate.getFullYear() === lastMonthYear;
            } else if(dateFilter === 'this_year') {
                return itemDate.getFullYear() === currentYear;
            }
            return true;
        });
    }
    
    if(sourceFilter !== 'all') {
        filteredData = filteredData.filter(function(item) {
            return item.source.toLowerCase().includes(sourceFilter);
        });
    }
    
    let tbody = document.getElementById('commissionTableBody');
    tbody.innerHTML = '';
    
    let symbol = symbols[currency] || '₹';
    let rate = rates[currency] || 1;
    let taxRate = regionTax[currentRegion] || 0;
    
    filteredData.forEach(function(item) {
        let revenueVal = taxEnabled ? item.revenue * (1 + taxRate) : item.revenue;
        let netVal = taxEnabled ? item.net * (1 + taxRate) : item.net;
        
        let row = tbody.insertRow();
        row.innerHTML = `
            <td>${item.date}</td>
            <td>
                <div class="d-flex align-items-center gap-2">
                    <div class="source-img">
                        <img src="https://cdn-icons-png.flaticon.com/512/3081/3081559.png" alt="icon">
                    </div>
                    <span>${item.source}</span>
                </div>
            </td>
            <td><a href="#" class="order-link">${item.order_id}</a></td>
            <td class="text-dark fw-semibold rev-amount" data-base="${item.revenue}">${symbol} ${(revenueVal * rate).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            <td class="text-success fw-bold ref-amount" data-base="${item.referral}">${symbol} ${(item.referral * rate).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            <td class="text-danger fw-bold lead-amount" data-base="${item.lead_charges}">${symbol} ${(item.lead_charges * rate).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            <td class="text-success fw-bold boost-amount" data-base="${item.boost_spend}">${symbol} ${(item.boost_spend * rate).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            <td class="text-danger fw-bold refund-amount" data-base="${item.refund}">${symbol} ${(item.refund * rate).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
            <td class="text-success fw-bold net-amount" data-base="${item.net}">${symbol} ${(netVal * rate).toLocaleString(undefined, {minimumFractionDigits: 2})}</td>
        `;
    });
}

/* ================= EXPORT FUNCTIONALITY ================= */
function exportData(type) {
    let headers = ['Date', 'Source', 'Order ID', 'Revenue', 'Referral Income', 'Lead Charges', 'Boost Spend', 'Refund Deduction', 'Net Income'];
    let rows = [];
    
    let trs = document.querySelectorAll('#commissionTableBody tr');
    trs.forEach(function(tr) {
        let row = [];
        let tds = tr.querySelectorAll('td');
        tds.forEach(function(td) {
            let text = td.innerText || '';
            row.push(text.trim());
        });
        rows.push(row);
    });
    
    if(type === 'csv') {
        let csvRows = [headers, ...rows];
        let csvContent = csvRows.map(function(row) {
            return row.join(',');
        }).join('\n');
        let blob = new Blob([csvContent], {type: 'text/csv'});
        let link = document.createElement('a');
        link.href = URL.createObjectURL(blob);
        link.download = 'commission_report.csv';
        link.click();
        URL.revokeObjectURL(link.href);
    } 
    else if(type === 'excel') {
        let wsData = [headers, ...rows];
        let ws = XLSX.utils.aoa_to_sheet(wsData);
        let wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Commission Report');
        XLSX.writeFile(wb, 'commission_report.xlsx');
    }
    else if(type === 'pdf') {
        const { jsPDF } = window.jspdf;
        let doc = new jsPDF('landscape');
        doc.autoTable({
            head: [headers],
            body: rows,
            theme: 'striped',
            styles: { fontSize: 8, cellPadding: 2 },
            headStyles: { fillColor: [0, 67, 146], textColor: 255 }
        });
        doc.save('commission_report.pdf');
    }
}

/* ================= SWITCHERS ================= */
document.getElementById('currencySwitcher').onchange = function() {
    currency = this.value;
    let url = new URL(window.location.href);
    url.searchParams.set('currency', currency);
    url.searchParams.set('region', currentRegion);
    window.location.href = url.toString();
};

document.getElementById('regionSwitcher').onchange = function() {
    currentRegion = this.value;
    let url = new URL(window.location.href);
    url.searchParams.set('region', currentRegion);
    url.searchParams.set('currency', currency);
    window.location.href = url.toString();
};

document.getElementById('taxToggle').onchange = function() {
    taxEnabled = this.checked;
    updateTaxLabel();
    updateMoney();
};

document.getElementById('commissionDateFilter').addEventListener('change', renderCommissionTable);
document.getElementById('commissionSourceFilter').addEventListener('change', renderCommissionTable);

/* ================= SUMMARY TABS ================= */
document.querySelectorAll(".earn-tabs button").forEach(function(btn) {
    btn.addEventListener("click", function() {
        let range = this.dataset.range;
        if(range === "custom") {
            document.getElementById("customRangeBox").style.display = "block";
            return;
        }
        let url = new URL(window.location.href);
        url.searchParams.set('range', range);
        url.searchParams.set('currency', currency);
        url.searchParams.set('region', currentRegion);
        window.location.href = url.toString();
    });
});

function applyCustom() {
    let from = document.getElementById('fromDate').value;
    let to = document.getElementById('toDate').value;
    if(!from || !to) {
        alert("Select date range");
        return;
    }
    let url = new URL(window.location.href);
    url.searchParams.set('range', 'custom');
    url.searchParams.set('from', from);
    url.searchParams.set('to', to);
    url.searchParams.set('currency', currency);
    url.searchParams.set('region', currentRegion);
    window.location.href = url.toString();
}

function goBack() {
    window.history.back();
}

/* ================= CHARTS INITIALIZATION ================= */
let trendData = @json($trend ?? []);
let pieChartData = @json($pie ?? []);

if(trendData.length > 0) {
    let chartDates = trendData.map(function(d) { return d.date; });
    let revenueValues = trendData.map(function(d) { return d.total; });
    let netValues = trendData.map(function(d) { return d.total * 0.8; });
    
    echarts.init(document.getElementById('trendChart')).setOption({
        tooltip: {trigger: 'axis'},
        xAxis: {type: 'category', data: chartDates},
        yAxis: {type: 'value'},
        series: [
            {type: 'bar', data: revenueValues, itemStyle: {color: '#4e73df'}, name: 'Revenue'},
            {type: 'line', data: netValues, smooth: true, areaStyle: {}, itemStyle: {color: '#1cc88a'}, name: 'Net Earnings'}
        ]
    });
    
    echarts.init(document.getElementById('netChart')).setOption({
        xAxis: {type: 'category', data: chartDates},
        yAxis: {type: 'value'},
        series: [{data: netValues, type: 'line', smooth: true, areaStyle: {}, name: 'Net Earnings'}]
    });
}

echarts.init(document.getElementById('barChart')).setOption({
    xAxis: {type: 'category', data: ['Daily', 'Weekly', 'Monthly']},
    yAxis: {type: 'value'},
    series: [{data: [{{ $daily ?? 0 }}, {{ $weekly ?? 0 }}, {{ $monthly ?? 0 }}], type: 'bar'}]
});

if(pieChartData.length > 0) {
    let pieFormatted = pieChartData.map(function(p) {
        return {value: p.total, name: p.label};
    });
    
    echarts.init(document.getElementById('categoryChart')).setOption({
        series: [{type: 'pie', radius: ['50%', '70%'], data: pieFormatted}]
    });
    
    echarts.init(document.getElementById('pieChart')).setOption({
        series: [{type: 'pie', radius: ['50%', '70%'], data: pieFormatted}]
    });
}

/* ================= INITIAL RENDER ================= */
setTimeout(function() {
    updateTaxLabel();
    renderCommissionTable();
    updateMoney();
}, 100);

</script>

@endsection