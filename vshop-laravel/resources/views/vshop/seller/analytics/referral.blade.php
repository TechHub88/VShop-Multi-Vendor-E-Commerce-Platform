@extends('vshop.seller.main')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<style>
    :root {
        --ref-blue-header: #0f4c81;
        --ref-silver-sub: #bcccdc;
        --ref-bg: #d9e2ec;
        --ref-card-bg: #ffffff;
        --ref-text-primary: #102a43;
        --ref-text-secondary: #486581;
        --ref-border: #bcccdc;
        --ref-success: #244030;
        --ref-success-bg: #c6f6d5;
        --ref-success-text: #22543d;
        --ref-warning-bg: #fef3c7;
        --ref-warning-text: #b45309;
        --ref-primary: #004392;
        --ref-accent: #0582ca;
    }

    .referral-body {
        font-family: 'Outfit', sans-serif;
        background-color: var(--ref-bg);
        color: var(--ref-text-primary);
        padding: 20px;
        min-height: 100vh;
    }

    /* Premium Header Bars from Mockup */
    .ref-header-top {
        background: var(--ref-blue-header);
        color: #ffffff;
        padding: 14px 24px;
        border-radius: 8px 8px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .ref-header-title {
        font-size: 19px;
        font-weight: 700;
        letter-spacing: 0.5px;
        margin: 0;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #ffffff !important;
    }

    .ref-header-badge {
        background: #16a34a;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 700;
    }

    .ref-header-sub {
        background: var(--ref-silver-sub);
        padding: 10px 24px;
        border-radius: 0 0 8px 8px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04);
        border-bottom: 1px solid var(--ref-border);
    }

    .ref-admin-label {
        font-size: 15px;
        font-weight: 700;
        color: var(--ref-text-primary);
        margin: 0;
    }

    .ref-sub-actions {
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .badge-grievance {
        background: var(--ref-warning-bg);
        border: 1px solid #fde68a;
        color: var(--ref-warning-text);
        font-weight: 600;
        font-size: 12px;
        padding: 4px 12px;
        border-radius: 50px;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .system-icons {
        display: flex;
        align-items: center;
        gap: 12px;
        color: var(--ref-text-secondary);
        font-size: 15px;
    }

    .system-icon-btn {
        cursor: pointer;
        transition: color 0.2s;
        position: relative;
    }

    .system-icon-btn:hover {
        color: var(--ref-text-primary);
    }

    .icon-badge {
        position: absolute;
        top: -6px;
        right: -6px;
        background: #ef4444;
        color: white;
        font-size: 8px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
    }

    /* Cards */
    .ref-card {
        background: var(--ref-card-bg);
        border: 1px solid var(--ref-border);
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.02);
        padding: 18px;
        height: 100%;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .ref-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
    }

    .ref-kpi-header {
        font-size: 13px;
        font-weight: 700;
        color: var(--ref-text-secondary);
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .ref-kpi-value {
        font-size: 28px;
        font-weight: 800;
        color: var(--ref-text-primary);
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .ref-kpi-subtext {
        font-size: 12px;
        font-weight: 600;
        color: var(--ref-text-secondary);
        margin-top: 6px;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
    }

    .ref-trend-up {
        color: #16a34a;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    /* Green Alert Banner */
    .ref-alert-banner {
        background-color: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #166534;
        border-radius: 8px;
        padding: 12px 16px;
        font-size: 13px;
        font-weight: 500;
        line-height: 1.5;
        margin-bottom: 15px;
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .ref-alert-banner i {
        font-size: 16px;
        margin-top: 2px;
        color: #15803d;
    }

    /* Section titles */
    .ref-section-title {
        font-size: 13px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--ref-text-primary);
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid var(--ref-border);
        padding-bottom: 6px;
    }

    /* Form Controls */
    .ref-select-sm {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 6px;
        border: 1px solid var(--ref-border);
        background-color: #fff;
        color: var(--ref-text-secondary);
        outline: none;
        cursor: pointer;
    }

    /* Date Capsule / Pill selector */
    .ref-pill-selector {
        display: flex;
        background: #f1f5f9;
        border-radius: 6px;
        padding: 2px;
        border: 1px solid var(--ref-border);
    }

    .ref-pill-btn {
        font-size: 11px;
        font-weight: 700;
        padding: 3px 8px;
        border-radius: 4px;
        border: none;
        background: transparent;
        color: var(--ref-text-secondary);
        cursor: pointer;
    }

    .ref-pill-btn.active {
        background: #fff;
        color: var(--ref-text-primary);
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Tables styling */
    .ref-table-wrapper {
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--ref-border);
    }

    .ref-table {
        width: 100%;
        border-collapse: collapse;
        background: #ffffff;
    }

    .ref-table th {
        background: #f8fafc;
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 700;
        color: var(--ref-text-secondary);
        text-transform: uppercase;
        border-bottom: 1px solid var(--ref-border);
        text-align: left;
    }

    .ref-table td {
        padding: 10px 12px;
        font-size: 12px;
        color: var(--ref-text-primary);
        border-bottom: 1px solid var(--ref-border);
        vertical-align: middle;
    }

    .ref-table tr:last-child td {
        border-bottom: none;
    }

    /* Avatars and Thumbnails */
    .product-thumb {
        width: 28px;
        height: 28px;
        border-radius: 4px;
        background: #cbd5e1;
        object-fit: cover;
    }

    .ref-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #e2e8f0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 10px;
        color: var(--ref-text-secondary);
    }

    /* Badges */
    .status-badge {
        padding: 2px 6px;
        border-radius: 4px;
        font-size: 10px;
        font-weight: 700;
        display: inline-block;
        text-align: center;
    }

    .status-badge-pending {
        background-color: var(--ref-warning-bg);
        color: var(--ref-warning-text);
    }

    .circle-badge-earn {
        min-width: 22px;
        height: 22px;
        border-radius: 11px;
        padding: 0 6px;
        background: var(--ref-success-bg);
        color: var(--ref-success-text);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 10px;
        margin: 0 auto;
        width: auto;
    }

    /* Star rating */
    .stars-indicator {
        color: #f59e0b;
        font-size: 10px;
    }

</style>

<div class="referral-body">

    {{-- HEADER BAR TOP (Mockup style) --}}
    <div class="ref-header-top">
        <h2 class="ref-header-title">
            Referral Analytics 
            
        </h2>
    </div>

    {{-- HEADER BAR SUB (Mockup style) --}}
    <div class="ref-header-sub">
         <h2 class="ref-header-title">
           
        </h2>
        <div class="ref-sub-actions">
            {{-- Currency Selector --}}
            <div style="display: flex; flex-direction: column; align-items: flex-start; gap: 4px;">
                <span style="font-size: 11px; font-weight: 600; color: var(--ref-text-secondary); text-transform: capitalize;">Currency</span>
                <div style="position: relative; display: flex; align-items: center; background: #ffffff; border: 1.5px solid #2563eb; border-radius: 8px; padding: 4px 12px; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15); transition: border-color 0.2s;">
                    <select id="referralCurrencySelector" onchange="changeReferralCurrency(this.value)" style="font-size: 13px; font-weight: 700; color: #1e293b; border: none; background: transparent; outline: none; cursor: pointer; padding-right: 20px; appearance: none; -webkit-appearance: none; font-family: inherit;">
                        <option value="INR" @if($currency == 'INR') selected @endif>₹ INR</option>
                        <option value="USD" @if($currency == 'USD') selected @endif>$ USD</option>
                        <option value="EUR" @if($currency == 'EUR') selected @endif>€ EUR</option>
                    </select>
                    <i class="fa-solid fa-angle-down" style="position: absolute; right: 10px; font-size: 10px; color: #64748b; pointer-events: none;"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- FOUR CARD KPI ROWS --}}
    <div class="row g-3 mb-4">
        {{-- Total Referral Clicks --}}
        <div class="col-md-3">
            <div class="ref-card d-flex flex-column justify-content-between">
                <div>
                    <div class="ref-kpi-header">Total Referral Clicks</div>
                    <div class="ref-kpi-value">{{ number_format($totalClicks) }}</div>
                </div>
                <div style="height: 45px; margin-top: 8px;">
                    <div id="sparklineClicks" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Referral Conversions --}}
        <div class="col-md-3">
            <div class="ref-card d-flex flex-column justify-content-between">
                <div>
                    <div class="ref-kpi-header">Referral Conversions</div>
                    <div class="ref-kpi-value">{{ number_format($referralConversions) }}</div>
                </div>
                <div class="ref-kpi-subtext">
                    <span>Conversion Rate: <strong style="color:var(--ref-primary);">{{ $conversionRate }}%</strong></span>
                    <span class="ref-trend-up">Comparison to last month <i class="fa-solid fa-arrow-trend-up"></i> 0.15%</span>
                </div>
            </div>
        </div>

        {{-- Referral Revenue --}}
        <div class="col-md-3">
            <div class="ref-card d-flex flex-column justify-content-between">
                <div>
                    <div class="ref-kpi-header">Referral Revenue</div>
                    <div class="ref-kpi-value">{{ $symbol }}{{ number_format($referralRevenue) }}</div>
                </div>
                <div class="ref-kpi-subtext">
                    <span>Total value generated: <strong>{{ $symbol }}{{ number_format($referralRevenue) }}</strong></span>
                    <span class="ref-trend-up">Performance metrics: <i class="fa-solid fa-arrow-trend-up"></i> 3%</span>
                </div>
            </div>
        </div>

        {{-- Average Referral Order Value --}}
        <div class="col-md-3">
            <div class="ref-card d-flex flex-column justify-content-between">
                <div>
                    <div class="ref-kpi-header">Average Referral Order Value (AOV)</div>
                    <div class="ref-kpi-value">{{ $symbol }}{{ number_format($averageOrderValue) }}</div>
                </div>
                <div class="ref-kpi-subtext" style="font-style: italic;">
                    Standard AOV per referral click conversion
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS GRID ROW --}}
    <div class="row g-3 mb-4">
        {{-- Referral Clicks and Conversions over time --}}
        <div class="col-md-6">
            <div class="ref-card">
                <div class="ref-section-title">
                    <span>Referral Clicks and Conversions (Over Time)</span>
                    <div class="d-flex align-items-center gap-2">
                        <div class="ref-pill-selector">
                            <button class="ref-pill-btn active">7D</button>
                            <button class="ref-pill-btn">Date picker</button>
                        </div>
                    </div>
                </div>
                <div style="height: 250px;">
                    <div id="overtimeChart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Top Referrers leads & services --}}
        <div class="col-md-3">
            <div class="ref-card">
                <div class="ref-section-title">
                    <span>Top Referrers Leads & Service</span>
                </div>
                <div style="height: 250px;">
                    <div id="topReferrersChart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>

        {{-- Donut Chart breakdown + status --}}
        <div class="col-md-3">
            <div class="ref-card d-flex flex-column justify-content-between">
                <div>
                    {{-- Alert Box from mockup --}}
                    <div class="ref-alert-banner">
                        <i class="fa-solid fa-circle-check"></i>
                        <div>
                            <strong>Status:</strong> Referral program performance is optimal. Review Top Products and referrers below for optimization.
                        </div>
                    </div>

                    <div class="ref-section-title">
                        <span>Referral Conversion Source Breakdown</span>
                    </div>
                </div>
                <div style="height: 140px;">
                    <div id="sourceBreakdownChart" style="width: 100%; height: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLES ROW --}}
    <div class="row g-3">
        {{-- Top Referred Products --}}
        <div class="col-md-6">
            <div class="ref-card">
                <div class="ref-section-title">
                    <span>Top Referred Products & Services</span>
                    <select class="ref-select-sm">
                        <option>View from monthly</option>
                        <option>View from weekly</option>
                    </select>
                </div>
                <div class="ref-table-wrapper">
                    <table class="ref-table">
                        <thead>
                            <tr>
                                <th>Rank</th>
                                <th>Listing Name</th>
                                <th>Category</th>
                                <th>Referral Views</th>
                                <th>Referral Conversions</th>
                                <th>Referral Sales Value</th>
                                <th>Average Rating</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($topReferredProducts) && count($topReferredProducts) > 0)
                                @foreach($topReferredProducts as $p)
                                <tr>
                                    <td style="font-weight: 700;">{{ $p->rank }}</td>
                                    <td style="font-weight: 600;">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="ref-avatar" style="background:#0284c7; color:#fff;">
                                                {{ substr($p->name, 0, 1) }}
                                            </div>
                                            <span>{{ $p->name }}</span>
                                        </div>
                                    </td>
                                    <td>{{ $p->category }}</td>
                                    <td style="font-weight: 700;">{{ number_format($p->views) }}</td>
                                    <td style="font-weight: 700;">{{ number_format($p->conversions) }}</td>
                                    <td style="font-weight: 700; color: var(--ref-primary);">{{ $symbol }}{{ number_format($p->value) }}</td>
                                    <td>
                                        <span class="stars-indicator">
                                            @for($i=1; $i<=5; $i++)
                                                @if($i <= $p->rating)
                                                    <i class="fa-solid fa-star"></i>
                                                @else
                                                    <i class="fa-regular fa-star"></i>
                                                @endif
                                            @endfor
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No referred products found.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Top Referrers Leaderboard --}}
        <div class="col-md-3">
            <div class="ref-card">
                <div class="ref-section-title">
                    <span>Top Referrers Leaderboard</span>
                </div>
                <div class="ref-table-wrapper">
                    <table class="ref-table">
                        <thead>
                            <tr>
                                <th>Top Referrer</th>
                                <th>Total Clicks</th>
                                <th>Total Conversions</th>
                                <th>Total Rewards</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($leaderboard) && count($leaderboard) > 0)
                                @foreach($leaderboard as $l)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="ref-avatar">
                                                <i class="fa-solid fa-user" style="font-size:10px;"></i>
                                            </div>
                                            <span style="font-weight:600;">{{ $l->name }}</span>
                                        </div>
                                    </td>
                                    <td style="font-weight: 700;">{{ number_format($l->clicks) }}</td>
                                    <td style="font-weight: 700;">{{ number_format($l->conversions) }}</td>
                                    <td>
                                        @if($l->total_rewards > 0)
                                            <strong style="color:var(--ref-success);">{{ $symbol }}{{ number_format($l->total_rewards, 2) }}</strong>
                                        @else
                                            <span class="status-badge status-badge-pending">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No referrers active on leaderboard.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Referral Campaign Performance --}}
        <div class="col-md-3">
            <div class="ref-card">
                <div class="ref-section-title">
                    <span>Referral Campaign Performance</span>
                </div>
                <div class="ref-table-wrapper">
                    <table class="ref-table">
                        <thead>
                            <tr>
                                <th>Active Campaign</th>
                                <th>Total Clicks</th>
                                <th>Total Conversions</th>
                                <th class="text-center">Total Earn</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(isset($campaignPerformance) && count($campaignPerformance) > 0)
                                @foreach($campaignPerformance as $c)
                                <tr>
                                    <td style="font-weight: 600;">{{ $c->campaign }}</td>
                                    <td style="font-weight: 700;">{{ number_format($c->clicks) }}</td>
                                    <td style="font-weight: 700;">{{ number_format($c->conversions) }}</td>
                                    <td>
                                        <div class="circle-badge-earn">{{ $symbol }}{{ number_format($c->earn) }}</div>
                                    </td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">No campaigns currently active.</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Apache ECharts script --}}
<script src="https://cdn.jsdelivr.net/npm/echarts@5/dist/echarts.min.js"></script>
<script>
    // ECharts Data injection safely with fallback arrays
    const trendData = @json($trend ?? []);
    const topReferrersData = @json($topReferrersServices ?? []);
    const sourceData = @json($sourceBreakdown ?? []);

    // Sparkline Clicks Line Chart
    if (document.getElementById('sparklineClicks')) {
        const sparkChart = echarts.init(document.getElementById('sparklineClicks'));
        sparkChart.setOption({
            grid: { left: 0, right: 0, top: 0, bottom: 0 },
            xAxis: { type: 'category', show: false },
            yAxis: { type: 'value', show: false },
            series: [{
                data: [350, 480, 420, 600, 580, 690, 720],
                type: 'line',
                smooth: true,
                symbol: 'none',
                lineStyle: { color: '#16a34a', width: 2 },
                areaStyle: {
                    color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                        { offset: 0, color: 'rgba(22, 163, 74, 0.2)' },
                        { offset: 1, color: 'rgba(22, 163, 74, 0)' }
                    ])
                }
            }]
        });
    }

    // Overtime curve chart for Clicks and Conversions
    if (document.getElementById('overtimeChart') && trendData && trendData.length > 0) {
        const overtimeChart = echarts.init(document.getElementById('overtimeChart'));
        const labels = trendData.map(x => x.label);
        const clickVals = trendData.map(x => x.clicks);
        const convVals = trendData.map(x => x.conversions);

        overtimeChart.setOption({
            tooltip: {
                trigger: 'axis',
                backgroundColor: 'rgba(16, 42, 67, 0.95)',
                borderWidth: 0,
                textStyle: { color: '#fff' }
            },
            legend: {
                data: ['Referral Clicks', 'Referral Conversions'],
                icon: 'circle',
                bottom: 0,
                textStyle: { color: '#486581', fontWeight: 600 }
            },
            grid: { left: '3%', right: '4%', top: '5%', bottom: '15%', containLabel: true },
            xAxis: {
                type: 'category',
                boundaryGap: false,
                data: labels,
                axisLine: { lineStyle: { color: '#bcccdc' } },
                axisLabel: { color: '#486581', fontWeight: 600 }
            },
            yAxis: {
                type: 'value',
                splitLine: { lineStyle: { color: '#f0f4f8' } },
                axisLabel: { color: '#486581', fontWeight: 600 }
            },
            series: [
                {
                    name: 'Referral Clicks',
                    type: 'line',
                    smooth: true,
                    symbolSize: 6,
                    lineStyle: { width: 3, color: '#004392' },
                    itemStyle: { color: '#004392' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(0, 67, 146, 0.15)' },
                            { offset: 1, color: 'rgba(0, 67, 146, 0)' }
                        ])
                    },
                    data: clickVals
                },
                {
                    name: 'Referral Conversions',
                    type: 'line',
                    smooth: true,
                    symbolSize: 6,
                    lineStyle: { width: 3, color: '#16a34a' },
                    itemStyle: { color: '#16a34a' },
                    areaStyle: {
                        color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                            { offset: 0, color: 'rgba(22, 163, 74, 0.15)' },
                            { offset: 1, color: 'rgba(22, 163, 74, 0)' }
                        ])
                    },
                    data: convVals
                }
            ]
        });
    }

    // Top Referrers horizontal bar chart
    if (document.getElementById('topReferrersChart') && topReferrersData && topReferrersData.length > 0) {
        const referrersChart = echarts.init(document.getElementById('topReferrersChart'));
        const names = topReferrersData.map(x => x.service).reverse();
        const values = topReferrersData.map(x => x.clicks).reverse();

        referrersChart.setOption({
            grid: { left: '3%', right: '12%', top: '3%', bottom: '3%', containLabel: true },
            xAxis: { type: 'value', show: false },
            yAxis: {
                type: 'category',
                data: names,
                axisLine: { show: false },
                axisTick: { show: false },
                axisLabel: {
                    color: '#486581',
                    fontSize: 10,
                    fontWeight: 600,
                    width: 90,
                    overflow: 'truncate'
                }
            },
            series: [{
                data: values,
                type: 'bar',
                barWidth: 10,
                itemStyle: {
                    color: '#004392',
                    borderRadius: 5
                },
                label: {
                    show: true,
                    position: 'right',
                    color: '#102a43',
                    fontWeight: 'bold',
                    fontSize: 10
                }
            }]
        });
    }

    // Donut chart for source breakdown
    if (document.getElementById('sourceBreakdownChart') && sourceData && sourceData.length > 0) {
        const sourceChart = echarts.init(document.getElementById('sourceBreakdownChart'));
        const chartData = sourceData.map(x => ({
            name: x.name,
            value: x.value
        }));

        sourceChart.setOption({
            tooltip: {
                trigger: 'item',
                backgroundColor: 'rgba(16, 42, 67, 0.95)',
                borderWidth: 0,
                textStyle: { color: '#fff' }
            },
            legend: {
                orient: 'vertical',
                right: '5%',
                top: 'middle',
                icon: 'circle',
                itemWidth: 8,
                itemHeight: 8,
                textStyle: { color: '#486581', fontSize: 10, fontWeight: 600 }
            },
            series: [{
                type: 'pie',
                radius: ['50%', '80%'],
                center: ['35%', '50%'],
                avoidLabelOverlap: false,
                itemStyle: {
                    borderRadius: 4,
                    borderColor: '#fff',
                    borderWidth: 2
                },
                label: { show: false },
                emphasis: {
                    label: {
                        show: true,
                        fontSize: 12,
                        fontWeight: 'bold',
                        color: '#102a43'
                    }
                },
                labelLine: { show: false },
                color: ['#ea580c', '#0284c7', '#16a34a', '#eab308'],
                data: chartData
            }]
        });
    }

    // Change currency function
    function changeReferralCurrency(val) {
        const url = new URL(window.location.href);
        url.searchParams.set('currency', val);
        window.location.href = url.toString();
    }

    // Handle responsiveness
    window.addEventListener('resize', function () {
        const ids = ['sparklineClicks', 'overtimeChart', 'topReferrersChart', 'sourceBreakdownChart'];
        ids.forEach(id => {
            const chart = echarts.getInstanceByDom(document.getElementById(id));
            if (chart) chart.resize();
        });
    });
</script>
@endsection
