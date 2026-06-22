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
        <div>
            <button class="btn btn-primary" style="background:#5b45f4; border:none; padding:10px 20px; border-radius:8px; font-weight:600;">+ New Campaign</button>
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
                <div class="metric-label">Total Clicks</div>
                <div class="metric-value">245.8K</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Conversions</div>
                <div class="metric-value">18.2K</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Conversion Rate</div>
                <div class="metric-value" style="color:#10b981;">7.4%</div>
            </div>
            <div class="metric-box">
                <div class="metric-label">Avg. Acquisition Cost</div>
                <div class="metric-value">₹120</div>
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
                    <tr>
                        <td><strong>Summer Vacation Special</strong></td>
                        <td>Travel Package</td>
                        <td>45,200</td>
                        <td>3,100</td>
                        <td>₹1.2L</td>
                        <td><span class="status-active">Active</span></td>
                        <td><a href="#" style="color:#5b45f4; font-weight:600; text-decoration:none;">View Report</a></td>
                    </tr>
                    <tr>
                        <td><strong>Electronics Mega Sale</strong></td>
                        <td>Product specific</td>
                        <td>88,500</td>
                        <td>9,400</td>
                        <td>₹3.8L</td>
                        <td><span class="status-active">Active</span></td>
                        <td><a href="#" style="color:#5b45f4; font-weight:600; text-decoration:none;">View Report</a></td>
                    </tr>
                    <tr>
                        <td><strong>New User Sign-up Bonus</strong></td>
                        <td>Referral</td>
                        <td>12,300</td>
                        <td>1,250</td>
                        <td>₹62K</td>
                        <td><span class="status-active">Active</span></td>
                        <td><a href="#" style="color:#5b45f4; font-weight:600; text-decoration:none;">View Report</a></td>
                    </tr>
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
            data: [23000, 31000, 22000, 45000, 32000, 52000, 38000]
        }, {
            name: 'Conversions',
            type: 'area',
            data: [1800, 2400, 1500, 3500, 2200, 4100, 2700]
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
        labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
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
