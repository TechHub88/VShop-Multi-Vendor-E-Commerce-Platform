@extends('layouts.vertical', ['title' => 'Smart AI Suggestions'])

@section('content')
<div class="container-fluid pt-3">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h1 class="h3 mb-0 text-slate-800 fw-bold">Smart AI Suggestions</h1>
            <p class="text-muted mb-0">AI-powered insights and listing optimizations based on customer orders and seller analytics.</p>
        </div>
    </div>

    <!-- AI Suggestions Cards -->
    <div class="row">
        <!-- vShop Suggestions Card -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-primary-light text-primary rounded-circle d-inline-flex">
                        <iconify-icon icon="solar:shop-bold-duotone" width="20"></iconify-icon>
                    </div>
                    <h5 class="mb-0 fw-bold text-slate-800">vShop Product Optimization</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-primary">Combo Builder</span>
                            <span class="small text-muted">AIO recommendation</span>
                        </div>
                        <h6 class="fw-bold text-slate-800">Seasonal Clothing Package suggestion</h6>
                        <p class="small text-muted mb-0">Based on past shopping records during summer months, combining tops and matching accessories increases sales conversion rate by 24%.</p>
                    </div>

                    <div class="p-3 bg-light rounded mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark">SEO Audit</span>
                            <span class="small text-muted">AIO recommendation</span>
                        </div>
                        <h6 class="fw-bold text-slate-800">Auto-inject internal links on electronics pages</h6>
                        <p class="small text-muted mb-0">Link high-ranking mobile review blogs directly to product detail pages of corresponding brands to build authority.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- vTravel Suggestions Card -->
        <div class="col-lg-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3 border-bottom d-flex align-items-center gap-2">
                    <div class="p-2 bg-success-light text-success rounded-circle d-inline-flex">
                        <iconify-icon icon="solar:globus-bold-duotone" width="20"></iconify-icon>
                    </div>
                    <h5 class="mb-0 fw-bold text-slate-800">vTravel Service Optimization</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-success">Tour Bundling</span>
                            <span class="small text-muted">AIO recommendation</span>
                        </div>
                        <h6 class="fw-bold text-slate-800">Local Tour Guide mapping</h6>
                        <p class="small text-muted mb-0">Cross-sell local tour guiding services dynamically inside confirmation page of hotel bookings located in high-traffic destinations.</p>
                    </div>

                    <div class="p-3 bg-light rounded mb-3">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-info text-white">Pricing Rule</span>
                            <span class="small text-muted">AIO recommendation</span>
                        </div>
                        <h6 class="fw-bold text-slate-800">Early-bird weekend package pricing</h6>
                        <p class="small text-muted mb-0">Suggest travel agencies to offer a 10% discount for bookings made 14 days in advance to secure optimal bookings.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-primary-light { background-color: rgba(13, 110, 253, 0.1); }
    .bg-success-light { background-color: rgba(16, 185, 129, 0.1); }
    .text-primary { color: #0d6efd; }
    .text-success { color: #10b981; }
</style>
@endsection
