{{-- resources/views/admin/analytics/customer-analytics.blade.php --}}
@extends('layouts.vertical', ['title' => 'Customer Analytics'])

@section('css')
<style>
    :root{
        --ca-bg: #f4f6fb;
        --ca-panel: #ffffff;
        --ca-border: #e6ebf2;
        --ca-text: #1f2a37;
        --ca-muted: #7c8798;
        --ca-primary: #4a78ff;
        --ca-primary-soft: rgba(74, 120, 255, 0.12);
        --ca-success: #21c16b;
        --ca-danger: #ef5b5b;
        --ca-warning: #f5b93f;
        --ca-shadow: 0 8px 24px rgba(15, 23, 42, 0.06);
        --ca-radius: 14px;
    }

    .customer-analytics-shell{
        background: var(--ca-bg);
        border: 1px solid var(--ca-border);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 8px 30px rgba(20, 33, 61, 0.06);
    }

    .ca-main{
        padding: 18px 18px 20px;
        background: #f8fafc;
    }

    .ca-topbar{
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 16px;
        margin-bottom: 16px;
        flex-wrap: wrap;
    }

    .ca-title{
        font-size: 28px;
        line-height: 1.1;
        font-weight: 800;
        letter-spacing: -.02em;
        color: #1f2d3d;
        margin: 0;
    }

    .ca-actions{
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .ca-chip,
    .ca-icon-btn{
        display: inline-flex;
        align-items: center;
        gap: 8px;
        height: 40px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1px solid var(--ca-border);
        background: #fff;
        color: #334155;
        font-size: 13px;
        font-weight: 600;
        box-shadow: 0 2px 8px rgba(15, 23, 42, 0.03);
    }

    .ca-icon-btn{
        width: 40px;
        justify-content: center;
        padding: 0;
    }

    .ca-chip i,
    .ca-icon-btn i{
        font-size: 16px;
        color: #64748b;
    }

    .ca-kpi-grid{
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 16px;
    }

    .ca-kpi-card{
        background: #fff;
        border: 1px solid var(--ca-border);
        border-radius: var(--ca-radius);
        padding: 16px;
        box-shadow: var(--ca-shadow);
        min-height: 102px;
    }

    .ca-kpi-label{
        color: #7b8794;
        font-size: 12px;
        font-weight: 600;
        margin-bottom: 8px;
    }

    .ca-kpi-value{
        font-size: 28px;
        font-weight: 800;
        color: #172554;
        line-height: 1;
        margin-bottom: 8px;
    }

    .ca-kpi-change{
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 12px;
        font-weight: 700;
    }

    .ca-kpi-change.up{
        color: var(--ca-success);
    }

    .ca-kpi-change.down{
        color: var(--ca-danger);
    }

    .ca-chart-grid{
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 14px;
    }

    .ca-chart-card{
        background: #fff;
        border: 1px solid var(--ca-border);
        border-radius: var(--ca-radius);
        box-shadow: var(--ca-shadow);
        padding: 14px 16px 10px;
    }

    .ca-chart-title{
        font-size: 14px;
        font-weight: 800;
        color: #243247;
        margin-bottom: 12px;
    }

    .ca-chart-wrap{
        position: relative;
        height: 220px;
    }

    @media (max-width: 1399.98px){
        .ca-kpi-grid{
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }
    }

    @media (max-width: 991.98px){
        .ca-kpi-grid{
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .ca-chart-grid{
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767.98px){
        .ca-kpi-grid{
            grid-template-columns: 1fr;
        }

        .ca-title{
            font-size: 22px;
        }
    }
</style>
@endsection

@section('content')
@php
    $totalCustomers     = $totalCustomers ?? 25430;
    $newCustomers       = $newCustomers ?? 15230;
    $returningCustomers = $returningCustomers ?? 10200;
    $retentionRate      = $retentionRate ?? 40.1;
    $churnRate          = $churnRate ?? 6.2;

    $startDateValue = isset($startDate) ? $startDate->format('d M Y') : '01 May 2024';
    $endDateValue   = isset($endDate) ? $endDate->format('d M Y') : '31 May 2024';
@endphp

<div class="container-fluid">
    <div class="customer-analytics-shell">
        <section class="ca-main">
            <div class="ca-topbar">
                <h1 class="ca-title">CUSTOMER ANALYTICS</h1>

                <div class="ca-actions">
                    <div class="ca-chip">
                        <i class="ri-calendar-line"></i>
                        <span>{{ $startDateValue }} - {{ $endDateValue }}</span>
                    </div>

                    <div class="ca-chip">
                        <i class="ri-filter-3-line"></i>
                        <span>Filter</span>
                    </div>

                    <div class="ca-chip">
                        <i class="ri-download-2-line"></i>
                        <span>Export</span>
                    </div>

                    <button type="button" class="ca-icon-btn">
                        <i class="ri-settings-3-line"></i>
                    </button>
                </div>
            </div>

            <div class="ca-kpi-grid">
                <div class="ca-kpi-card">
                    <div class="ca-kpi-label">Total Customers</div>
                    <div class="ca-kpi-value">{{ number_format($totalCustomers) }}</div>
                    <div class="ca-kpi-change up">
                        <i class="ri-arrow-up-line"></i> +13.2%
                    </div>
                </div>

                <div class="ca-kpi-card">
                    <div class="ca-kpi-label">New Customers</div>
                    <div class="ca-kpi-value">{{ number_format($newCustomers) }}</div>
                    <div class="ca-kpi-change up">
                        <i class="ri-arrow-up-line"></i> +16.4%
                    </div>
                </div>

                <div class="ca-kpi-card">
                    <div class="ca-kpi-label">Returning Customers</div>
                    <div class="ca-kpi-value">{{ number_format($returningCustomers) }}</div>
                    <div class="ca-kpi-change up">
                        <i class="ri-arrow-up-line"></i> +6.7%
                    </div>
                </div>

                <div class="ca-kpi-card">
                    <div class="ca-kpi-label">Retention Rate</div>
                    <div class="ca-kpi-value">{{ number_format($retentionRate, 1) }}%</div>
                    <div class="ca-kpi-change up">
                        <i class="ri-arrow-up-line"></i> +3.8%
                    </div>
                </div>

                <div class="ca-kpi-card">
                    <div class="ca-kpi-label">Churn Rate</div>
                    <div class="ca-kpi-value">{{ number_format($churnRate, 1) }}%</div>
                    <div class="ca-kpi-change down">
                        <i class="ri-arrow-down-line"></i> -1.2%
                    </div>
                </div>
            </div>

            <div class="ca-chart-grid">
                <div class="ca-chart-card">
                    <div class="ca-chart-title">Customer Segmentation</div>
                    <div class="ca-chart-wrap">
                        <canvas id="segmentChart"></canvas>
                    </div>
                </div>

                <div class="ca-chart-card">
                    <div class="ca-chart-title">Retention Rate Over Time</div>
                    <div class="ca-chart-wrap">
                        <canvas id="retentionChart"></canvas>
                    </div>
                </div>

                <div class="ca-chart-card">
                    <div class="ca-chart-title">Churn Rate Trend</div>
                    <div class="ca-chart-wrap">
                        <canvas id="churnChart"></canvas>
                    </div>
                </div>

                <div class="ca-chart-card">
                    <div class="ca-chart-title">Repeat Purchase Rate</div>
                    <div class="ca-chart-wrap">
                        <canvas id="repeatChart"></canvas>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection

@section('script')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May'];
    const rootStyle = getComputedStyle(document.documentElement);
    const primary = rootStyle.getPropertyValue('--ca-primary').trim();

    function areaGradient(ctx, chartArea) {
        const gradient = ctx.createLinearGradient(0, chartArea.top, 0, chartArea.bottom);
        gradient.addColorStop(0, 'rgba(74, 120, 255, 0.30)');
        gradient.addColorStop(1, 'rgba(74, 120, 255, 0.02)');
        return gradient;
    }

    const segmentCtx = document.getElementById('segmentChart');
    if (segmentCtx) {
        new Chart(segmentCtx, {
            type: 'doughnut',
            data: {
                labels: ['New Customers', 'Returning Customers', 'VIP Customers', 'Inactive Customers'],
                datasets: [{
                    data: [46.3, 31.0, 15.4, 7.3],
                    backgroundColor: ['#4a78ff', '#45c77c', '#f6c453', '#f59f66'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            boxWidth: 8,
                            boxHeight: 8,
                            padding: 14,
                            color: '#4b5563',
                            font: {
                                size: 11,
                                weight: '600'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.label}: ${context.raw}%`;
                            }
                        }
                    }
                }
            }
        });
    }

    const retentionCanvas = document.getElementById('retentionChart');
    if (retentionCanvas) {
        new Chart(retentionCanvas, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    data: [31, 34, 33, 37, 40],
                    borderColor: primary,
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.42,
                    fill: true,
                    backgroundColor: (context) => {
                        const {ctx, chartArea} = context.chart;
                        if (!chartArea) return 'rgba(74, 120, 255, 0.08)';
                        return areaGradient(ctx, chartArea);
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { size: 11 } }
                    },
                    y: {
                        min: 0,
                        max: 60,
                        ticks: {
                            stepSize: 20,
                            color: '#6b7280',
                            callback: (value) => value + '%',
                            font: { size: 11 }
                        },
                        grid: { color: '#edf2f7' }
                    }
                }
            }
        });
    }

    const churnCanvas = document.getElementById('churnChart');
    if (churnCanvas) {
        new Chart(churnCanvas, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    data: [8.8, 6.9, 7.8, 7.1, 6.2],
                    borderColor: primary,
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.42,
                    fill: true,
                    backgroundColor: (context) => {
                        const {ctx, chartArea} = context.chart;
                        if (!chartArea) return 'rgba(74, 120, 255, 0.08)';
                        return areaGradient(ctx, chartArea);
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { size: 11 } }
                    },
                    y: {
                        min: 0,
                        max: 12,
                        ticks: {
                            stepSize: 4,
                            color: '#6b7280',
                            callback: (value) => value + '%',
                            font: { size: 11 }
                        },
                        grid: { color: '#edf2f7' }
                    }
                }
            }
        });
    }

    const repeatCanvas = document.getElementById('repeatChart');
    if (repeatCanvas) {
        new Chart(repeatCanvas, {
            type: 'line',
            data: {
                labels: months,
                datasets: [{
                    data: [24, 28, 41, 45, 49],
                    borderColor: primary,
                    borderWidth: 2,
                    pointRadius: 0,
                    tension: 0.42,
                    fill: true,
                    backgroundColor: (context) => {
                        const {ctx, chartArea} = context.chart;
                        if (!chartArea) return 'rgba(74, 120, 255, 0.08)';
                        return areaGradient(ctx, chartArea);
                    }
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#6b7280', font: { size: 11 } }
                    },
                    y: {
                        min: 0,
                        max: 60,
                        ticks: {
                            stepSize: 20,
                            color: '#6b7280',
                            callback: (value) => value + '%',
                            font: { size: 11 }
                        },
                        grid: { color: '#edf2f7' }
                    }
                }
            }
        });
    }
});
</script>
@endsection
