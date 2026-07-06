@extends('layouts.vertical', ['title' => 'Promotion Analytics'])

@section('content')

<div class="container-fluid">

    <div class="card analytics-wrapper border-0">

        <div class="card-body p-4">

            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-4">

                <div>
                    <h3 class="analytics-title mb-0">
                        PROMOTION ANALYTICS
                    </h3>
                </div>

                <div class="d-flex align-items-center gap-2">

                    <div class="date-box">
                        <i class="ri-calendar-line"></i>
                        {{ request('start_date', now()->subMonth()->format('d M Y')) }}
                        -
                        {{ request('end_date', now()->format('d M Y')) }}
                    </div>

                    <button class="btn btn-light border btn-sm">
                        <i class="ri-filter-line"></i>
                        Filter
                    </button>

                    <button class="btn btn-light border btn-sm">
                        <i class="ri-download-line"></i>
                        Export
                    </button>

                </div>

            </div>

            {{-- KPI ROW --}}
            <div class="row g-3 mb-4">

                <div class="col">
                    <div class="kpi-card">
                        <small>Active Campaigns</small>
                        <h3>{{ $boostStats['active_boosts'] ?? 24 }}</h3>
                    </div>
                </div>

                <div class="col">
                    <div class="kpi-card">
                        <small>Total Ad Spend</small>
                        <h3>₹{{ number_format($boostStats['total_boost_revenue'] ?? 245000) }}</h3>
                    </div>
                </div>

                <div class="col">
                    <div class="kpi-card">
                        <small>Total Impressions</small>
                        <h3>{{ number_format($boostStats['impressions'] ?? 1250000) }}</h3>
                    </div>
                </div>

                <div class="col">
                    <div class="kpi-card">
                        <small>Total Enquiries</small>
                        <h3>{{ number_format($boostStats['enquiries'] ?? 18650) }}</h3>
                    </div>
                </div>

                <div class="col">
                    <div class="kpi-card">
                        <small>Avg. CTR</small>
                        <h3>{{ number_format($boostStats['ctr'] ?? 3.45,2) }}%</h3>
                    </div>
                </div>

            </div>

            {{-- TABLE + BOOST CARD --}}
            <div class="row">

                <div class="col-lg-8">

                    <div class="analytics-box">

                        <div class="section-title">
                            Top Performing Campaigns
                        </div>

                        <table class="table table-sm align-middle analytics-table">

                            <thead>
                            <tr>
                                <th>Campaign</th>
                                <th>Spend</th>
                                <th>Impressions</th>
                                <th>Enquiries</th>
                                <th>CTR</th>
                                <th>ROI</th>
                            </tr>
                            </thead>

                            <tbody>

                            @forelse($topCampaigns as $campaign)

                                <tr>
                                    <td>{{ $campaign->name }}</td>

                                    <td>
                                        ₹{{ number_format($campaign->boost_amount ?? 0) }}
                                    </td>

                                    <td>
                                        {{ number_format($campaign->impressions ?? 0) }}
                                    </td>

                                    <td>
                                        {{ number_format($campaign->total_orders ?? 0) }}
                                    </td>

                                    <td>
                                        {{ number_format($campaign->boost_conversion_rate ?? 0,1) }}%
                                    </td>

                                    <td>
                                        <span class="roi-badge">
                                            {{ number_format($campaign->roi ?? 0,1) }}%
                                        </span>
                                    </td>
                                </tr>

                            @empty

                                <tr>
                                    <td colspan="6" class="text-center">
                                        No Campaign Found
                                    </td>
                                </tr>

                            @endforelse

                            </tbody>

                        </table>

                    </div>

                </div>

                {{-- BOOST INCREASES --}}
                <div class="col-lg-4">

                    <div class="analytics-box h-100">

                        <div class="section-title">
                            BOOST INCREASES
                        </div>

                        <ul class="boost-list">

                            <li>
                                <span>✔ Impressions</span>
                                <strong class="text-success">
                                    +{{ $boostStats['imp_growth'] ?? 18 }}%
                                </strong>
                            </li>

                            <li>
                                <span>✔ Reach</span>
                                <strong class="text-success">
                                    +{{ $boostStats['reach_growth'] ?? 25 }}%
                                </strong>
                            </li>

                            <li>
                                <span>✔ Enquiries</span>
                                <strong class="text-success">
                                    +{{ $boostStats['enq_growth'] ?? 12 }}%
                                </strong>
                            </li>

                        </ul>

                        <div class="boost-note">
                            Boost results are not guaranteed.
                        </div>

                    </div>

                </div>

            </div>

            {{-- CHART ROW --}}
            <div class="row mt-4">

                <div class="col-lg-4">

                    <div class="analytics-box">

                        <div class="section-title">
                            Campaign Performance
                        </div>

                        <canvas id="campaignChart" height="180"></canvas>

                    </div>

                </div>

                <div class="col-lg-4">

                    <div class="analytics-box">

                        <div class="section-title">
                            Boost Spend vs Enquiries
                        </div>

                        <canvas id="spendVsEnquiryChart" height="180"></canvas>

                    </div>

                </div>

                <div class="col-lg-4">

                    <div class="analytics-box">

                        <div class="section-title">
                            Most Boosted Categories
                        </div>

                        <canvas id="categoryChart" height="180"></canvas>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<style>
    .analytics-wrapper{
    border-radius:18px;
    background:#fff;
}

.analytics-title{
    font-weight:800;
    color:#1b2d4d;
    font-size:28px;
}

.date-box{
    background:#f7f8fb;
    border:1px solid #e4e8f0;
    padding:8px 14px;
    border-radius:8px;
    font-size:13px;
}

.kpi-card{
    border:1px solid #e7edf5;
    border-radius:10px;
    padding:14px;
    background:#fff;
    height:100%;
}

.kpi-card small{
    color:#7b8794;
    display:block;
    margin-bottom:6px;
}

.kpi-card h3{
    margin:0;
    font-weight:700;
    color:#1e2a4a;
}

.analytics-box{
    border:1px solid #edf2f7;
    border-radius:12px;
    background:#fff;
    padding:18px;
}

.section-title{
    font-size:15px;
    font-weight:700;
    color:#22355c;
    margin-bottom:15px;
}

.analytics-table thead{
    background:#f6f8fc;
}

.analytics-table thead th{
    border:none;
    font-size:13px;
    color:#4a5568;
}

.analytics-table tbody td{
    font-size:13px;
}

.roi-badge{
    background:#e8fff2;
    color:#00a651;
    font-weight:700;
    padding:4px 10px;
    border-radius:20px;
}

.boost-list{
    padding:0;
    margin:0;
    list-style:none;
}

.boost-list li{
    display:flex;
    justify-content:space-between;
    border-bottom:1px dashed #d8dde6;
    padding:12px 0;
}

.boost-note{
    margin-top:20px;
    color:#6b7280;
    font-size:12px;
}

.analytics-box canvas{
    max-height:220px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

new Chart(document.getElementById('campaignChart'),{
    type:'bar',
    data:{
        labels:['Summer','Home Care','Women Wear','Salon','Pest'],
        datasets:[{
            data:[80,72,68,55,42]
        }]
    }
});

new Chart(document.getElementById('spendVsEnquiryChart'),{
    type:'line',
    data:{
        labels:['Jan','Feb','Mar','Apr','May','Jun'],
        datasets:[
            {
                label:'Spend',
                data:[20,35,30,50,45,60]
            },
            {
                label:'Enquiries',
                data:[15,30,22,45,40,55]
            }
        ]
    }
});

new Chart(document.getElementById('categoryChart'),{
    type:'doughnut',
    data:{
        labels:['Home','Beauty','Appliance','Fashion'],
        datasets:[{
            data:[35,25,20,20]
        }]
    }
});

</script>
@endsection
