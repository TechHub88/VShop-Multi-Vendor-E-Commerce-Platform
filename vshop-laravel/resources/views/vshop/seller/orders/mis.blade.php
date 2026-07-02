@extends('vshop.seller.main')

@section('content')

<style>
body{
    background:#f5f7fb;
}

/* ===== CARDS ===== */
.card{
    border:none;
    border-radius:16px;
    background:#fff;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    transition:.25s;
}
.card:hover{
    transform:translateY(-3px);
}

/* ===== KPI ===== */
.stat-card, .kpi-card{
    padding:20px;
    text-align:center;
}
.stat-card h5, .kpi-card h5{
    font-weight:700;
}

/* ===== CHART ===== */
.chart-box{
    height:280px;
}

/* ===== TITLES ===== */
.chart-title{
    font-weight:600;
    margin-bottom:15px;
    color:#1e293b;
}

/* ===== TABLE ===== */
.table th{
    font-weight:600;
    color:#64748b;
}
.table th {
    font-weight: 600;
    color: #fff !important;
    background: #004392 !important;
    border: 1px solid #fff;
}
.table tbody td {
    border: 1px solid #dee2e6;
}

.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    width: 35px;
    height: 38px;
}

.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}

.top-back-heading-sec h5 {
    color: #0f172a !important;
}

.kpi-card {
    border: 1px solid rgb(201 208 218) !important;
}

.card {
    border: 1px solid rgb(201 208 218) !important;
}

/* ===== CURRENCY SELECT ===== */
.currency-select {
    border: 1px solid #004392;
    border-radius: 30px;
    padding: 8px 16px;
    background: white;
    font-weight: 500;
    color: #004392;
    outline: none;
    cursor: pointer;
    font-size: 14px;
}

/* ===== MODAL STYLES ===== */
.log-modal .modal-content {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}
.log-modal .modal-header {
    background: #004392;
    color: white;
    border-bottom: none;
    padding: 16px 24px;
}
.log-modal .modal-header .btn-close {
    filter: brightness(0) invert(1);
}
.log-detail-item {
    background: #f8fafc;
    border-radius: 12px;
    padding: 12px 16px;
    margin-bottom: 12px;
    border-left: 4px solid #004392;
}
.log-detail-item strong {
    color: #1e293b;
    display: inline-block;
    min-width: 130px;
}
.log-detail-item span {
    color: #334155;
}
</style>

<div class="container-fluid py-4">

    {{-- TOP SECTION WITH BACK BUTTON AND CURRENCY DROPDOWN --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex top-back-heading-sec">
            <button onclick="goBack()" class="nav-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h5 class="fw-semibold mb-0">Order MIS Dashboard</h5>
        </div>

        <div>
            <select id="currencySelector" class="currency-select">
                <option value="INR">₹ INR</option>
                <option value="USD">$ USD</option>
                <option value="EUR">€ EUR</option>
                <option value="GBP">£ GBP</option>
            </select>
        </div>
    </div>

    {{-- ================= ORDER STATUS KPI ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card kpi-card">
                <h5 class="text-warning">{{ $counts['processing'] ?? 0 }}</h5>
                <small>Processing</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <h5 class="text-success">{{ $counts['completed'] ?? 0 }}</h5>
                <small>Completed</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <h5 class="text-danger">{{ $counts['cancelled'] ?? 0 }}</h5>
                <small>Cancelled</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <h5 class="text-dark">{{ $counts['returned'] ?? 0 }}</h5>
                <small>Returned</small>
            </div>
        </div>
    </div>

    {{-- ================= TOP SUMMARY ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card">
                <h5 class="text-primary">{{ $counts['all'] ?? 0 }}</h5>
                <small>Total Orders</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card">
                <h5 class="text-success" id="totalRevenue">₹ {{ number_format($grossRevenue ?? 0,2) }}</h5>
                <small>Total Revenue</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card">
                <h5 class="text-danger" id="revenueLoss">₹ {{ number_format($revenueLoss ?? 0,2) }}</h5>
                <small>Revenue Loss</small>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card">
                <h5 class="text-dark">{{ $newCustomers ?? 0 }}</h5>
                <small>New Customers</small>
            </div>
        </div>
    </div>

    {{-- ================= SLA KPI ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card kpi-card">
                <h5 class="text-primary">{{ $sla['on_time'] ?? 0 }}</h5>
                <small>On-Time Orders</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <h5 class="text-warning">{{ $sla['delayed'] ?? 0 }}</h5>
                <small>Delayed Orders</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <h5 class="text-danger">{{ $sla['breached'] ?? 0 }}</h5>
                <small>SLA Breached</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card kpi-card">
                <h5 class="text-success" id="paymentSuccessRate">{{ $paymentSuccessRate ?? 0 }}%</h5>
                <small>Payment Success Rate</small>
            </div>
        </div>
    </div>

    {{-- ================= SECOND KPI ================= --}}
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card kpi-card">
                <h5 class="text-primary">{{ $retrySuccessRate ?? 0 }}%</h5>
                <small>Retry Success Rate</small>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card kpi-card">
                <h5 class="text-dark">{{ $refundEfficiency ?? 0 }}%</h5>
                <small>Refund Efficiency</small>
            </div>
        </div>
    </div>

    {{-- ================= SALES FUNNEL ================= --}}
    <div class="card p-4 mb-4">
        <h6 class="fw-semibold mb-3">Sales Funnel</h6>
        @php
            $total = max($funnel['enquiry'] ?? 1, 1);
            $orderRate = (($funnel['order'] ?? 0) / $total) * 100;
            $successRate = (($funnel['success'] ?? 0) / $total) * 100;
        @endphp
        <div class="progress mb-2" style="height: 10px;">
            <div class="progress-bar bg-primary" style="width: {{ $orderRate }}%"></div>
            <div class="progress-bar bg-success" style="width: {{ $successRate }}%"></div>
        </div>
        <div class="d-flex justify-content-between small text-muted">
            <span>Enquiry: {{ $funnel['enquiry'] ?? 0 }}</span>
            <span>Orders: {{ $funnel['order'] ?? 0 }}</span>
            <span>Success: {{ $funnel['success'] ?? 0 }}</span>
        </div>
    </div>

    {{-- ================= CHARTS ================= --}}
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card p-4">
                <h6 class="chart-title">Revenue Trend</h6>
                <div id="revenueChart" class="chart-box"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h6 class="chart-title">Payment Performance</h6>
                <div id="paymentChart" class="chart-box"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h6 class="chart-title">Order Status</h6>
                <div id="statusChart" class="chart-box"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card p-4">
                <h6 class="chart-title">Customer Split</h6>
                <div id="customerChart" class="chart-box"></div>
            </div>
        </div>
    </div>

    {{-- ================= COMMISSION SPLIT VIEW ================= --}}
    <div class="card mt-4 p-3">
        <h6 class="fw-semibold mb-1">COMMISSION SPLIT VIEW</h6>
        <small class="text-muted d-block mb-3">(Purpose: Transparency)</small>

        <div class="table-responsive">
            <table class="table mb-0" id="commissionTable">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Recipient</th>
                        <th>Amount</th>
                        <th>Purpose/Type</th>
                        <th>Discrepancy</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse(($orders ?? []) as $order)
                        @php
                            $split = $order->commission_split;
                            $orderLabel = '#' . ($order->id ?? 'ORD');
                            $dateStr = $order->created_at ? $order->created_at->format('d M Y') : ($order->order_date ?? 'N/A');
                        @endphp
                        <tr data-base-amount="{{ $split->seller ?? 0 }}" data-recipient="Seller" data-purpose="Core Commission ({{ $split->seller_pct ?? 60 }}%)" data-log-id="R-{{ $order->id }}-1">
                            <td>{{ $orderLabel }} <small class="text-muted d-block">{{ $dateStr }}</small></td>
                            <td>Seller</td>
                            <td class="text-success commission-amount">₹ {{ number_format($split->seller ?? 0, 2) }}</td>
                            <td>Core Commission ({{ $split->seller_pct ?? 60 }}%)</td>
                            <td><a href="#" class="view-log-btn" data-log-id="R-{{ $order->id }}-1">View Log R-{{ $order->id }}-1</a></td>
                        </tr>
                        <tr data-base-amount="{{ $split->platform ?? 0 }}" data-recipient="Platform" data-purpose="Platform Fee ({{ $split->platform_pct ?? 30 }}%)" data-log-id="R-{{ $order->id }}-2">
                            <td>{{ $orderLabel }} <small class="text-muted d-block">{{ $dateStr }}</small></td>
                            <td>Platform</td>
                            <td class="text-success commission-amount">₹ {{ number_format($split->platform ?? 0, 2) }}</td>
                            <td>Platform Fee ({{ $split->platform_pct ?? 30 }}%)</td>
                            <td><a href="#" class="view-log-btn" data-log-id="R-{{ $order->id }}-2">View Log R-{{ $order->id }}-2</a></td>
                        </tr>
                        <tr data-base-amount="{{ $split->referrer ?? 0 }}" data-recipient="Referral" data-purpose="Affiliate Fee ({{ $split->referrer_pct ?? 10 }}%)" data-log-id="R-{{ $order->id }}-3">
                            <td>{{ $orderLabel }} <small class="text-muted d-block">{{ $dateStr }}</small></td>
                            <td>Referral</td>
                            <td class="text-danger commission-amount">₹ {{ number_format($split->referrer ?? 0, 2) }}</td>
                            <td>Affiliate Fee ({{ $split->referrer_pct ?? 10 }}%)</td>
                            <td><a href="#" class="view-log-btn" data-log-id="R-{{ $order->id }}-3">View Log R-{{ $order->id }}-3</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-3">No commission split data available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ================= RECENT ORDERS TABLE ================= --}}
    <div class="card mt-4">
        <div class="card-header fw-semibold">Recent Orders</div>
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Customer</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody id="recentOrdersTable">
                @foreach(($orders ?? []) as $order)
                    <tr data-base-amount="{{ $order->total_amount ?? 0 }}">
                        <td>{{ $order->customer->name ?? '-' }}</td>
                        <td class="order-amount">₹ {{ number_format($order->total_amount ?? 0, 2) }}</td>
                        <td><span class="badge bg-light text-dark border">{{ ucfirst($order->status ?? '-') }}</span></td>
                        <td>{{ $order->created_at ? $order->created_at->format('d M Y') : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>

{{-- ================= LOG MODAL POPUP ================= --}}
<div class="modal fade log-modal" id="logModal" tabindex="-1" aria-labelledby="logModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="logModalLabel">
                    <i class="fa-solid fa-receipt me-2"></i>Commission Transaction Log
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="logModalBody">
                <div class="log-detail-item">
                    <strong>Log ID:</strong> <span id="modalLogId">--</span>
                </div>
                <div class="log-detail-item">
                    <strong>Recipient:</strong> <span id="modalRecipient">--</span>
                </div>
                <div class="log-detail-item">
                    <strong>Amount:</strong> <span id="modalAmount">--</span>
                </div>
                <div class="log-detail-item">
                    <strong>Purpose/Type:</strong> <span id="modalPurpose">--</span>
                </div>
                <div class="log-detail-item">
                    <strong>Status:</strong> <span class="badge bg-success">Settled</span>
                </div>
                <div class="log-detail-item">
                    <strong>Transaction Date:</strong> <span id="modalDate">01 Nov 2023</span>
                </div>
                <div class="log-detail-item">
                    <strong>Remarks:</strong> <span id="modalRemarks">Auto settlement completed successfully.</span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    // ==================== CHARTS INITIALIZATION ====================
    const revenueChart = echarts.init(document.getElementById('revenueChart'));
    const monthlyData = @json($monthlyRevenue->values() ?? []);
    const monthlyLabels = @json($monthlyRevenue->keys() ?? []);
    
    revenueChart.setOption({
        tooltip: { trigger: 'axis' },
        xAxis: { type: 'category', data: monthlyLabels },
        yAxis: { type: 'value', name: 'Amount' },
        series: [{
            data: monthlyData,
            type: 'line',
            smooth: true,
            areaStyle: { opacity: 0.3 },
            lineStyle: { color: '#004392', width: 3 },
            symbol: 'circle',
            symbolSize: 8
        }]
    });

    const paymentChart = echarts.init(document.getElementById('paymentChart'));
    paymentChart.setOption({
        tooltip: { trigger: 'item' },
        series: [{
            type: 'pie',
            radius: ['55%', '75%'],
            data: [
                { value: {{ $paymentSuccessRate ?? 0 }}, name: 'Success', itemStyle: { color: '#10b981' } },
                { value: {{ $retrySuccessRate ?? 0 }}, name: 'Retry', itemStyle: { color: '#f59e0b' } },
                { value: {{ 100 - ($paymentSuccessRate ?? 0) }}, name: 'Failed', itemStyle: { color: '#ef4444' } }
            ],
            label: { show: true, formatter: '{b}: {d}%' }
        }]
    });

    const statusChart = echarts.init(document.getElementById('statusChart'));
    const statusData = @json(collect($statusRevenue ?? [])->map(fn($v,$k)=>['value'=>$v,'name'=>$k])->values());
    statusChart.setOption({
        tooltip: { trigger: 'item' },
        series: [{
            type: 'pie',
            radius: ['55%', '75%'],
            data: statusData,
            label: { show: true, formatter: '{b}: {d}%' }
        }]
    });

    const customerChart = echarts.init(document.getElementById('customerChart'));
    customerChart.setOption({
        tooltip: { trigger: 'axis' },
        xAxis: { type: 'category', data: ['New', 'Returning'] },
        yAxis: { type: 'value' },
        series: [{
            type: 'bar',
            data: [{{ $newCustomers ?? 0 }}, {{ $returningCustomers ?? 0 }}],
            itemStyle: { color: '#004392', borderRadius: [8,8,0,0] },
            barWidth: '40%'
        }]
    });

    // ==================== CURRENCY CONVERSION ====================
    // Store base values in INR
    let baseValues = {
        grossRevenue: {{ $grossRevenue ?? 0 }},
        revenueLoss: {{ $revenueLoss ?? 0 }},
        commissionAmounts: [],
        recentOrderAmounts: []
    };

    // Store commission base amounts from table rows
    document.querySelectorAll('#commissionTable tbody tr').forEach(row => {
        let baseAmt = parseFloat(row.getAttribute('data-base-amount') || '0');
        baseValues.commissionAmounts.push(baseAmt);
    });

    // Store recent order base amounts
    document.querySelectorAll('#recentOrdersTable tr').forEach(row => {
        let baseAmt = parseFloat(row.getAttribute('data-base-amount') || '0');
        baseValues.recentOrderAmounts.push(baseAmt);
    });

    // Exchange rates (based on INR)
    const exchangeRates = {
        INR: 1,
        USD: 0.012,
        EUR: 0.011,
        GBP: 0.0095
    };

    function getCurrencySymbol(currency) {
        const symbols = { INR: '₹', USD: '$', EUR: '€', GBP: '£' };
        return symbols[currency] || '₹';
    }

    function convertAmount(amountINR, targetCurrency) {
        if (targetCurrency === 'INR') return amountINR;
        let rate = exchangeRates[targetCurrency] || 1;
        return amountINR * rate;
    }

    function formatCurrency(amount, currency) {
        let symbol = getCurrencySymbol(currency);
        return `${symbol} ${amount.toFixed(2)}`;
    }

    function updateAllCurrencies(selectedCurrency) {
        // Update Total Revenue
        let convertedRevenue = convertAmount(baseValues.grossRevenue, selectedCurrency);
        document.getElementById('totalRevenue').innerHTML = formatCurrency(convertedRevenue, selectedCurrency);
        
        // Update Revenue Loss
        let convertedLoss = convertAmount(baseValues.revenueLoss, selectedCurrency);
        document.getElementById('revenueLoss').innerHTML = formatCurrency(convertedLoss, selectedCurrency);
        
        // Update Commission Table Amounts
        let commissionCells = document.querySelectorAll('#commissionTable tbody tr .commission-amount');
        commissionCells.forEach((cell, index) => {
            if (baseValues.commissionAmounts[index] !== undefined) {
                let converted = convertAmount(baseValues.commissionAmounts[index], selectedCurrency);
                cell.innerHTML = formatCurrency(converted, selectedCurrency);
            }
        });
        
        // Update Recent Orders Amounts
        let orderAmountCells = document.querySelectorAll('#recentOrdersTable .order-amount');
        orderAmountCells.forEach((cell, index) => {
            if (baseValues.recentOrderAmounts[index] !== undefined) {
                let converted = convertAmount(baseValues.recentOrderAmounts[index], selectedCurrency);
                cell.innerHTML = formatCurrency(converted, selectedCurrency);
            }
        });
        
        // Update Revenue Trend Chart with converted values
        let convertedChartData = monthlyData.map(val => convertAmount(val, selectedCurrency));
        revenueChart.setOption({
            series: [{
                data: convertedChartData,
                tooltip: { valueFormatter: (value) => formatCurrency(value, selectedCurrency) }
            }],
            yAxis: { name: `Amount (${getCurrencySymbol(selectedCurrency)})` }
        });
        
        // Update Payment Performance Chart tooltip formatting
        paymentChart.setOption({
            tooltip: { valueFormatter: (value) => value + '%' }
        });
    }

    // Currency selector event listener
    const currencySelect = document.getElementById('currencySelector');
    if (currencySelect) {
        currencySelect.addEventListener('change', function(e) {
            updateAllCurrencies(e.target.value);
        });
        // Initialize with INR
        updateAllCurrencies('INR');
    }

    // ==================== VIEW LOG MODAL FUNCTIONALITY ====================
    const logModal = new bootstrap.Modal(document.getElementById('logModal'));
    const viewLogBtns = document.querySelectorAll('.view-log-btn');
    
    viewLogBtns.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const logId = this.getAttribute('data-log-id');
            const row = this.closest('tr');
            
            if (row) {
                const recipient = row.querySelector('td:nth-child(2)')?.innerText || 'Unknown';
                const purpose = row.querySelector('td:nth-child(4)')?.innerText || 'Transaction';
                const amountRaw = row.querySelector('.commission-amount')?.innerText || '$0.00';
                const currentCurrency = currencySelect ? currencySelect.value : 'INR';
                const symbol = getCurrencySymbol(currentCurrency);
                
                // Update modal content
                document.getElementById('modalLogId').innerText = logId || 'N/A';
                document.getElementById('modalRecipient').innerText = recipient;
                document.getElementById('modalAmount').innerHTML = `<span class="fw-bold">${amountRaw}</span>`;
                document.getElementById('modalPurpose').innerText = purpose;
                
                // Dynamic remarks based on log ID
                let remarks = 'Auto settlement completed successfully.';
                if (logId.endsWith('-1')) remarks = 'Commission credited to seller account. Settlement ID: TXN-' + logId;
                else if (logId.endsWith('-2')) remarks = 'Platform fee deducted as per slab rate.';
                else if (logId.endsWith('-3')) remarks = 'Affiliate commission allocated for referral payout.';
                
                document.getElementById('modalRemarks').innerText = remarks;
                document.getElementById('modalDate').innerText = new Date().toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
            }
            
            logModal.show();
        });
    });
});

// Go back function
function goBack() {
    window.history.back();
}
</script>

@endsection