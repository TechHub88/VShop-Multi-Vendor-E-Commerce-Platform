@extends('vshop.seller.main')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --sage-bg: #eef2f0;
        --card-bg: #ffffff;
        --text-primary: #1e293b;
        --text-secondary: #64748b;
        --border-color: #e2e8f0;
        --success: #16a34a;
        --danger: #ef4444;
        --warning: #f59e0b;
        --info: #06b6d4;
        --primary: #2563eb;
    }

    .analytics-body {
        font-family: 'Outfit', sans-serif;
        background-color: var(--sage-bg);
        color: var(--text-primary);
        padding: 20px;
        min-height: 100vh;
    }

    .glass-card {
        background: var(--card-bg);
        border: 1px solid rgba(255, 255, 255, 0.8);
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
        padding: 20px;
        height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .glass-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
    }

    .kpi-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--text-secondary);
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
    }

    .kpi-value {
        font-size: 32px;
        font-weight: 800;
        color: var(--text-primary);
        letter-spacing: -0.5px;
    }

    .kpi-subtext {
        font-size: 13px;
        color: var(--text-secondary);
        font-weight: 500;
    }

    .trend-indicator {
        font-size: 13px;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 4px;
    }

    .trend-up { color: var(--success); }
    .trend-down { color: var(--danger); }

    /* Custom Table styling */
    .billing-table-wrapper {
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid var(--border-color);
    }

    .billing-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
    }

    .billing-table th {
        background: #f8fafc;
        padding: 14px 16px;
        font-size: 13px;
        font-weight: 600;
        color: var(--text-secondary);
        text-align: left;
        border-bottom: 1px solid var(--border-color);
    }

    .billing-table td {
        padding: 14px 16px;
        font-size: 14px;
        color: var(--text-primary);
        border-bottom: 1px solid var(--border-color);
        vertical-align: middle;
    }

    .billing-table tr:last-child td {
        border-bottom: none;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        text-align: center;
    }

    .badge-deducted {
        background-color: #fee2e2;
        color: #b91c1c;
    }

    .badge-refunded {
        background-color: #dcfce7;
        color: #15803d;
    }

    .badge-pending {
        background-color: #fef3c7;
        color: #b45309;
    }

    .icon-box-interaction {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }

    .icon-box-interaction i {
        font-size: 16px;
    }

    /* Top navigation header mock from screenshot */
    .mock-top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        flex-wrap: wrap;
        gap: 15px;
    }

    .mock-search-box {
        position: relative;
        width: 320px;
    }

    .mock-search-box input {
        width: 100%;
        padding: 8px 16px 8px 40px;
        border-radius: 50px;
        border: 1px solid var(--border-color);
        background: #fff;
        font-size: 14px;
        font-family: inherit;
        outline: none;
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.02);
    }

    .mock-search-box i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--text-secondary);
        font-size: 14px;
    }

    .top-action-pills {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .top-action-capsule {
        background: #fff;
        border: 1px solid var(--border-color);
        border-radius: 50px;
        padding: 6px 16px;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        user-select: none;
        transition: background-color 0.2s, border-color 0.2s;
    }

    .top-action-capsule:hover {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    .dropdown-container {
        position: relative;
    }

    .dropdown-popover {
        display: none;
        position: absolute;
        top: calc(100% + 8px);
        right: 0;
        background: #ffffff;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        padding: 10px;
        width: 170px;
        z-index: 1000;
        animation: slideDownFade 0.2s ease-out;
    }

    @keyframes slideDownFade {
        from { opacity: 0; transform: translateY(-6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .popover-card {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        text-align: left;
    }

    .popover-label {
        font-size: 11px;
        font-weight: 600;
        color: #94a3b8;
        margin-bottom: 4px;
    }

    .popover-select-wrapper {
        position: relative;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .popover-select {
        font-size: 13px;
        font-weight: 700;
        color: #1e293b;
        background: transparent;
        border: none;
        width: 100%;
        outline: none;
        cursor: pointer;
        padding: 0;
        appearance: none;
        -webkit-appearance: none;
    }

    .popover-select-wrapper i {
        position: absolute;
        right: 0;
        font-size: 10px;
        color: #64748b;
        pointer-events: none;
    }

    .switch-toggle {
        position: relative;
        display: inline-block;
        width: 34px;
        height: 20px;
    }

    .switch-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider-toggle {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #cbd5e1;
        transition: .4s;
        border-radius: 34px;
    }

    .slider-toggle:before {
        position: absolute;
        content: "";
        height: 14px; width: 14px;
        left: 3px; bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }

    input:checked + .slider-toggle {
        background-color: var(--primary);
    }

    input:checked + .slider-toggle:before {
        transform: translateX(14px);
    }

    .avatar-mock {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background-color: #cbd5e1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #fff;
        overflow: hidden;
        border: 2px solid #fff;
    }

    /* Map Card Overlay Styling */
    .map-overlay-container {
        position: relative;
        height: 140px;
        background: #e2e8f0;
        border-radius: 12px;
        overflow: hidden;
        margin-bottom: 12px;
        background-image: radial-gradient(#cbd5e1 1.5px, transparent 1.5px);
        background-size: 15px 15px;
    }

    .map-pin {
        position: absolute;
        width: 24px;
        height: 24px;
        background: rgba(239, 68, 68, 0.2);
        border: 2px solid #ef4444;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: pulseMap 2s infinite;
    }

    .map-pin::after {
        content: '';
        width: 6px;
        height: 6px;
        background: #ef4444;
        border-radius: 50%;
    }

    @keyframes pulseMap {
        0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
        70% { transform: scale(1.1); box-shadow: 0 0 0 8px rgba(239, 68, 68, 0); }
        100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    .help-center-btn {
        background: #fef3c7;
        border: 1px solid #fde68a;
        color: #b45309;
        font-weight: 600;
        font-size: 13px;
        padding: 10px 16px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        cursor: pointer;
        width: 100%;
        margin-top: 15px;
        transition: background-color 0.2s;
    }
    .help-center-btn:hover {
        background: #fde68a;
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
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .chart-box div {
        width: 100% !important;
        height: 100% !important;
    }
    .chart-box div canvas {
        width: 100% !important;
        height: 100% !important;
    }
</style>

<div class="container-fluid py-4">
    {{-- MOCK HEADER (Exactly as screenshot) --}}
    <div class="mock-top-bar">
        <div class="d-flex top-back-heading-sec">

            <button onclick="goBack()" class="nav-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
    
            <h5 class="fw-bold mb-0">
                Lead Analytics
            </h5>
    
        </div>

        {{-- SEARCH BAR --}}
        <!--<div class="mock-search-box">-->
        <!--    <i class="fa-solid fa-magnifying-glass"></i>-->
        <!--    <input type="text" placeholder="Search Enquiries, Services, Sources...">-->
        <!--</div>-->

        {{-- CONTROL PILLS --}}
        <div class="top-action-pills">
            {{-- Flag/Region Dropdown --}}
            <div class="dropdown-container">
                <div class="top-action-capsule" id="regionPillBtn">
                    <span id="selectedRegionText">IN</span>
                    <i class="fa-solid fa-angle-down" style="font-size:10px; font-weight:800;"></i>
                </div>
                <div class="dropdown-popover" id="regionPopover">
                    <div class="popover-card">
                        <div class="popover-label">Default Region</div>
                        <div class="popover-select-wrapper">
                            <select class="popover-select" id="regionSelector">
                                <option value="IN" selected>India</option>
                                <option value="US">United States</option>
                                <option value="NP">Nepal</option>
                                <option value="SEA">Southeast Asia</option>
                            </select>
                            <i class="fa-solid fa-angle-down"></i>
                        </div>
                    </div>
                </div>
            </div>
            
            {{-- Currency Dropdown --}}
            <div class="dropdown-container">
                <div class="top-action-capsule" id="currencyPillBtn">
                    <span id="selectedCurrencyText"> Currency</span>
                    <i class="fa-solid fa-angle-down" style="font-size:10px; font-weight:800;"></i>
                </div>
                <div class="dropdown-popover" id="currencyPopover">
                    <div class="popover-card">
                     
                        <div class="popover-select-wrapper">
                            <select class="popover-select" id="currencySelector">
                                <option value="₹, $" selected>choose currency</option>
                                <option value="₹">₹ (INR)</option>
                                <option value="$">$ (USD)</option>
                                <option value="€">€ (EUR)</option>
                            </select>
                            <i class="fa-solid fa-angle-down"></i>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- FOUR CARD KPI ROWS --}}
    <div class="row g-3 mb-4">
        {{-- Total Enquiries --}}
        <div class="col-md-3">
            <div class="glass-card d-flex flex-column justify-content-between">
                <div>
                    <div class="kpi-title">
                        Total Enquiries
                        <i class="fa-solid fa-circle-info" style="font-size:12px; color:var(--text-secondary);"></i>
                    </div>
                    <div class="kpi-value">{{ number_format($totalEnquiries) }}</div>
                    <div class="kpi-subtext mt-1">Enquiries</div>
                </div>
                <div style="height: 50px; margin-top: 10px;">
                    <div id="sparklineChart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Lead Conversion % --}}
        <div class="col-md-3">
            <div class="glass-card d-flex flex-column justify-content-between">
                <div>
                    <div class="kpi-title">
                        Lead Conversion %
                        <i class="fa-solid fa-circle-info" style="font-size:12px; color:var(--text-secondary);"></i>
                    </div>
                    <div class="kpi-value">{{ $conversionRate }}%</div>
                    <div class="trend-indicator trend-up">
                        Trend <i class="fa-solid fa-arrow-trend-up"></i>
                    </div>
                </div>
                <div style="height: 60px;">
                    <div id="gaugeChart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Top Locations --}}
        <div class="col-md-3">
            <div class="glass-card">
                <div class="kpi-title">
                    Top Locations
                    <i class="fa-solid fa-circle-info" style="font-size:12px; color:var(--text-secondary);"></i>
                </div>
                
                {{-- Mock World Map overlay --}}
                <div class="map-overlay-container">
                    {{-- Pin in India --}}
                    <div class="map-pin" style="top: 60%; left: 60%;"></div>
                    {{-- Pin in Nepal --}}
                    <div class="map-pin" style="top: 55%; left: 62%;"></div>
                    {{-- Pin in Southeast Asia --}}
                    <div class="map-pin" style="top: 75%; left: 75%;"></div>
                </div>

                <div class="w-100">
                    <table class="w-100" style="font-size: 13px; font-weight: 600;">
                        <thead>
                            <tr style="color:var(--text-secondary); border-bottom:1px solid var(--border-color);">
                                <th class="pb-1 text-start">Top 3 Locations</th>
                                <th class="pb-1 text-end">Enquiry</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($locations->take(3) as $index => $loc)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td class="py-1 text-start" style="font-weight: 500;">{{ $index+1 }}. {{ $loc->label }}</td>
                                <td class="py-1 text-end" style="font-weight: 700;">{{ number_format($loc->value) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="2" class="text-center text-muted py-2 fw-normal">No location data found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Most Enquired Services --}}
        <div class="col-md-3">
            <div class="glass-card d-flex flex-column justify-content-between">
                <div>
                    <div class="kpi-title">
                        Most Enquired Services
                        <i class="fa-solid fa-circle-info" style="font-size:12px; color:var(--text-secondary);"></i>
                    </div>
                </div>
                <div style="height: 180px;">
                    <div id="servicesChart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHART AND INFO ROW --}}
    <div class="row g-3 mb-4">
        {{-- Enquiries vs Converted Leads --}}
        <div class="col-md-9">
            <div class="glass-card">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span style="font-weight: 700; font-size:15px; text-transform: uppercase;">Enquiries Vs Converted Leads</span>
                    <i class="fa-solid fa-circle-info" style="font-size:12px; color:var(--text-secondary);"></i>
                </div>
                <div style="height: 280px;">
                    <div id="mainTrendChart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Help Center Card --}}
        <div class="col-md-3">
            <div class="glass-card d-flex flex-column justify-content-between text-center" style="background: linear-gradient(135deg, #ffffff, #f1f5f9);">
                <div>
                    <i class="fa-solid fa-circle-question" style="font-size: 40px; color: var(--warning); margin-top: 20px;"></i>
                    <h5 class="fw-bold mt-3" style="font-weight: 700;">Need Help?</h5>
                    <p class="text-muted" style="font-size: 13px; line-height: 1.5;">Check out our guide for maximizing your conversion strategy and resolving audits.</p>
                </div>
                <a href="{{ route('seller.grievance.list') }}" class="help-center-btn" style="text-decoration: none;">
                    <i class="fa-solid fa-lightbulb"></i> Help Center
                </a>
            </div>
        </div>
    </div>

    {{-- BILLING TABLE ROW --}}
    <div class="glass-card">
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <h4 class="fw-bold m-0" style="font-size: 16px; text-transform: uppercase; letter-spacing: 0.5px;">
                    Click & Lead Billing History
                </h4>
                <i class="fa-solid fa-bell" style="color: var(--primary); font-size: 14px;"></i>
            </div>
            <span style="font-size: 13px; color: var(--text-secondary); font-weight: 600; font-style: italic;">
                Purpose: transparency and seller ca audit charges
            </span>
        </div>

        <div class="billing-table-wrapper">
            <table class="billing-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Service</th>
                        <th>Platform</th>
                        <th>Buyer Source</th>
                        <th>Charge Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($billingHistory as $b)
                    <tr>
                        <td>{{ $b->date }}</td>
                        <td style="font-weight: 600;">{{ $b->service }}</td>
                        <td>
                            <span class="badge bg-primary rounded-pill text-white" style="font-weight: 500; padding: 5px 12px;">Vshop</span>
                        </td>
                        <td>
                            <span class="fw-bold" style="display:inline-flex; align-items:center; gap:6px;">
                                @if($b->buyer_source == 'Google')
                                    <i class="fa-brands fa-google" style="color:#4285F4;"></i> Google
                                @elseif($b->buyer_source == 'Social')
                                    <i class="fa-solid fa-share-nodes" style="color:#00c6ff;"></i> Social
                                @elseif($b->buyer_source == 'App')
                                    <i class="fa-solid fa-mobile-screen-button" style="color:#111;"></i> App
                                @else
                                    <i class="fa-solid fa-link" style="color:#aaa;"></i> Direct
                                @endif
                            </span>
                        </td>
                        <td style="font-weight: 700;">₹{{ number_format($b->charge) }}</td>
                        <td>
                            <span class="badge-status 
                                @if($b->status == 'Deducted') badge-deducted
                                @elseif($b->status == 'Refunded') badge-refunded
                                @else badge-pending @endif">
                                {{ $b->status }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4 fw-semibold">No lead or click billing history found for this seller.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Apache ECharts script --}}
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
    // ECharts Data injection
    const servicesData = @json($services);
    const trendData = @json($trend);

    // Sparkline Trend Line Chart
    if (document.getElementById('sparklineChart')) {
        const sparkChart = echarts.init(document.getElementById('sparklineChart'));
        sparkChart.setOption({
            grid: { left: 0, right: 0, top: 0, bottom: 0 },
            xAxis: { type: 'category', show: false },
            yAxis: { type: 'value', show: false },
            series: [{
                data: [820, 932, 901, 934, 1290, 1330, 1320],
                type: 'line',
                smooth: true,
                symbol: 'none',
                lineStyle: { color: '#2563eb', width: 2 },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(37, 99, 235, 0.2)' },
                        { offset: 1, color: 'rgba(37, 99, 235, 0)' }
                    ])
                }
            }]
        });
    }

    // Semi-circle progress gauge
    if (document.getElementById('gaugeChart')) {
        const gaugeChart = echarts.init(document.getElementById('gaugeChart'));
        const conversionRate = parseFloat(@json($conversionRate));
        gaugeChart.setOption({
            series: [{
                type: 'gauge',
                startAngle: 180,
                endAngle: 0,
                radius: '130%',
                center: ['50%', '85%'],
                min: 0,
                max: 100,
                splitNumber: 5,
                progress: {
                    show: true,
                    roundCap: true,
                    width: 8,
                    itemStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 1, 0, [
                            { offset: 0, color: '#f59e0b' },
                            { offset: 1, color: '#16a34a' }
                        ])
                    }
                },
                pointer: { show: false },
                axisLine: {
                    roundCap: true,
                    lineStyle: { width: 8, color: [[1, '#e2e8f0']] }
                },
                axisTick: { show: false },
                splitLine: { show: false },
                axisLabel: { show: false },
                title: { show: false },
                detail: { show: false },
                data: [{ value: conversionRate }]
            }]
        });
    }

    // Services Horizontal Bar Chart
    if (document.getElementById('servicesChart')) {
        const servicesChart = echarts.init(document.getElementById('servicesChart'));
        const names = servicesData.map(x => x.label).reverse();
        const values = servicesData.map(x => x.value).reverse();

        servicesChart.setOption({
            grid: { left: '3%', right: '12%', top: '3%', bottom: '3%', containLabel: true },
            xAxis: { type: 'value', show: false },
            yAxis: {
                type: 'category',
                data: names,
                axisLine: { show: false },
                axisTick: { show: false },
                axisLabel: {
                    color: '#64748b',
                    fontSize: 11,
                    fontWeight: 600,
                    width: 100,
                    overflow: 'truncate'
                }
            },
            series: [{
                data: values,
                type: 'bar',
                barWidth: 10,
                itemStyle: {
                    color: '#0284c7',
                    borderRadius: 5
                },
                label: {
                    show: true,
                    position: 'right',
                    color: '#1e293b',
                    fontWeight: 'bold',
                    fontSize: 11
                }
            }]
        });
    }

    // Enquiries vs Converted Leads Dual Curve Chart
    if (document.getElementById('mainTrendChart')) {
        const mainChart = echarts.init(document.getElementById('mainTrendChart'));
        const months = trendData.map(x => x.month);
        const enqValues = trendData.map(x => x.enquiries);
        const convValues = trendData.map(x => x.converted);

        mainChart.setOption({
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(30, 41, 59, 0.95)',
                borderWidth: 0,
                textStyle: { color: '#fff' }
            },
            legend: {
                data: ['Total Enquiries', 'Total Converted Leads'],
                icon: 'circle',
                bottom: 0,
                textStyle: { color: '#64748b', fontWeight: 600 }
            },
            grid: { left: '3%', right: '4%', top: '5%', bottom: '15%', containLabel: true },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: months,
                axisLine: { lineStyle: { color: '#cbd5e1' } },
                axisLabel: { color: '#64748b', fontWeight: 600 }
            },
            yAxis: {
                type: 'value',
                splitLine: { lineStyle: { color: '#f1f5f9' } },
                axisLabel: { color: '#64748b', fontWeight: 600 }
            },
            series: [
                {
                    name: 'Total Enquiries',
                    type: 'line',
                    smooth: true,
                    symbolSize: 6,
                    lineStyle: { width: 3, color: '#2563eb' },
                    itemStyle: { color: '#2563eb' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(37, 99, 235, 0.15)' },
                            { offset: 1, color: 'rgba(37, 99, 235, 0)' }
                        ])
                    },
                    data: enqValues
                },
                {
                    name: 'Total Converted Leads',
                    type: 'line',
                    smooth: true,
                    symbolSize: 6,
                    lineStyle: { width: 3, color: '#f59e0b' },
                    itemStyle: { color: '#f59e0b' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(245, 158, 11, 0.15)' },
                            { offset: 1, color: 'rgba(245, 158, 11, 0)' }
                        ])
                    },
                    data: convValues
                }
            ]
        });
    }

    // Handle Popover Dropdown Toggles
    const regionPill = document.getElementById('regionPillBtn');
    const regionPopover = document.getElementById('regionPopover');
    const regionSelector = document.getElementById('regionSelector');
    const selectedRegionText = document.getElementById('selectedRegionText');

    const currencyPill = document.getElementById('currencyPillBtn');
    const currencyPopover = document.getElementById('currencyPopover');
    const currencySelector = document.getElementById('currencySelector');
    const selectedCurrencyText = document.getElementById('selectedCurrencyText');

    // Toggle Region
    regionPill.addEventListener('click', function (e) {
        e.stopPropagation();
        regionPopover.style.display = regionPopover.style.display === 'block' ? 'none' : 'block';
        currencyPopover.style.display = 'none';
    });

    // Toggle Currency
    currencyPill.addEventListener('click', function (e) {
        e.stopPropagation();
        currencyPopover.style.display = currencyPopover.style.display === 'block' ? 'none' : 'block';
        regionPopover.style.display = 'none';
    });

    // Select Region
    regionSelector.addEventListener('change', function () {
        selectedRegionText.textContent = this.value;
        regionPopover.style.display = 'none';
    });

    // Select Currency
    currencySelector.addEventListener('change', function () {
        selectedCurrencyText.textContent = this.value;
        currencyPopover.style.display = 'none';
    });

    // Close on clicking outside
    document.addEventListener('click', function () {
        regionPopover.style.display = 'none';
        currencyPopover.style.display = 'none';
    });

    // Stop propagation inside popover cards to allow select interactions
    regionPopover.addEventListener('click', function(e) { e.stopPropagation(); });
    currencyPopover.addEventListener('click', function(e) { e.stopPropagation(); });

    // Handle responsiveness
    window.addEventListener('resize', function () {
        const ids = ['sparklineChart', 'gaugeChart', 'servicesChart', 'mainTrendChart'];
        ids.forEach(id => {
            const chart = echarts.getInstanceByDom(document.getElementById(id));
            if (chart) chart.resize();
        });
    });
</script>
@endsection
