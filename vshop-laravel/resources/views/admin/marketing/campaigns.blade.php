@extends('layouts.vertical', ['title' => 'Campaigns Tracking'])

@section('content')
<!-- ApexCharts -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<style>
    .camp-container {
        font-family: 'Inter', sans-serif;
        background-color: #f8f9fc;
        padding: 24px;
        min-height: 100vh;
    }
    .camp-header {
        margin-bottom: 24px;
    }
    .camp-title {
        font-size: 24px;
        font-weight: 700;
        color: #1a1d21;
        margin-bottom: 4px;
    }
    .camp-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 0;
    }

    /* Cards generic */
    .dash-card {
        background: white;
        border-radius: 12px;
        padding: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 24px;
        height: 100%;
    }
    .card-title {
        font-size: 16px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Metrics Grid */
    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }
    .metric-box {
        padding: 20px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
    }
    .metric-label {
        font-size: 13px;
        color: #64748b;
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .metric-value {
        font-size: 28px;
        font-weight: 800;
        color: #0f172a;
    }

    /* Filters */
    .filter-btn {
        padding: 6px 16px;
        background: white;
        border: 1px solid #cbd5e1;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 500;
        color: #334155;
        cursor: pointer;
    }
    .filter-btn:hover { background: #f1f5f9; }

    /* Table */
    .camp-table {
        width: 100%;
        border-collapse: collapse;
    }
    .camp-table th {
        text-align: left;
        padding: 12px 16px;
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        font-size: 13px;
        border-bottom: 1px solid #e2e8f0;
    }
    .camp-table td {
        padding: 16px;
        color: #334155;
        font-size: 14px;
        border-bottom: 1px solid #f1f5f9;
    }
    .status-active {
        background: #ecfdf5;
        color: #10b981;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
</style>

<div class="camp-container">
    <div class="camp-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="camp-title">Campaign Performance Tracking</h2>
            <p class="camp-subtitle">Monitor and analyze the ROI of your marketing campaigns</p>
        </div>
      
    </div>

    <div class="dash-card">
        <div class="card-title">
            <span>Performance Overview</span>
            <div style="display:flex; gap:10px;">
                <button class="filter-btn">7 Days</button>
                <button class="filter-btn">30 Days</button>
                <button class="filter-btn">This Year</button>
            </div>
        </div>

        <div class="metrics-grid">
            <div class="metric-box">
                <div class="metric-label">Total Clicks (Unique Users)</div>
                <div class="metric-value">{{ $formatNumber($totalClicks) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Conversions (Orders)</div>
                <div class="metric-value">{{ $formatNumber($totalConversions) }}</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Conversion Rate</div>
                <div class="metric-value" style="color:#10b981;">{{ number_format($conversionRate, 1) }}%</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Avg. Acquisition Cost</div>
                <div class="metric-value">₹{{ number_format($avgCost, 2) }}</div>
            </div>
        </div>

        <div id="detailedCampaignChart" style="height: 350px;"></div>
    </div>

    <div class="dash-card">
        <div class="card-title">Active Campaigns List</div>
        <div class="table-responsive">
            <table class="camp-table">
                <thead>
                    <tr>
                        <th>Campaign Name</th>
                        <th>Type</th>
                        <th>Clicks</th>
                        <th>Conversions</th>
                        <th>Spend/Reward</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($campaigns as $camp)
                        <tr>
                            <td><strong>{{ $camp->name }}</strong></td>
                            <td>{{ $camp->formatted_type }}</td>
                            <td>{{ $formatNumber($camp->unique_users) }}</td>
                            <td>{{ $formatNumber($camp->total_orders) }}</td>
                            <td>₹{{ number_format($camp->total_discount_given, 2) }}</td>
                            <td>
                                @if($camp->status === 'active')
                                    <span class="status-active">Active</span>
                                @elseif($camp->status === 'scheduled')
                                    <span class="status-active" style="background:#fff7ed; color:#ea580c;">Scheduled</span>
                                @else
                                    <span class="status-active" style="background:#f1f5f9; color:#64748b;">{{ $camp->formatted_status }}</span>
                                @endif
                            </td>
                            <td><a href="#" style="color:#5b45f4; font-weight:600; text-decoration:none;">View Report</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">No campaigns found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var options = {
        series: [{
            name: 'Clicks',
            type: 'column',
            data: @json($chartClicks)
        }, {
            name: 'Conversions',
            type: 'area',
            data: @json($chartConversions)
        }],
        chart: {
            height: 350,
            type: 'line',
            toolbar: { show: false },
            fontFamily: 'Inter, sans-serif'
        },
        colors: ['#cbd5e1', '#5b45f4'],
        stroke: {
            curve: 'smooth',
            width: [0, 3]
        },
        fill: {
            type: ['solid', 'gradient'],
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.4,
                opacityTo: 0.05,
                stops: [0, 100]
            }
        },
        labels: @json($chartDates),
        xaxis: {
            type: 'category',
        },
        yaxis: [{
            title: {
                text: 'Clicks',
            },
        }, {
            opposite: true,
            title: {
                text: 'Conversions',
            }
        }]
    };

    var chart = new ApexCharts(document.querySelector("#detailedCampaignChart"), options);
    chart.render();
});
</script>
@endsection
