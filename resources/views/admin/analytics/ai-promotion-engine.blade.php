@extends('layouts.vertical', ['title' => 'AI Promotion Engine'])

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12">
            <h4 class="page-title">AI Promotion Engine</h4>
        </div>
    </div>

    <!-- Important Notice -->
 

    <!-- AI Recommendation Card + Insights (Two columns) -->
    <div class="row g-3 mb-4">
        <!-- Left: AI Promotion Recommendation for specific product -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header" style="background: linear-gradient(135deg, #2c5f8a 0%, #1a3a52 100%); color: white;">
                    <h5 class="mb-0"><i class="ri-lightbulb-line me-2"></i> AI Promotion Recommendation for Home Cleaning Service</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>AI Analyst Insights</h6>
                            <div class="mb-3">
                                <span class="badge bg-primary me-1">Generalists</span> <span class="fw-semibold">High</span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: 85%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-info me-1">Senior-heavy</span> <span class="fw-semibold">Good</span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: 70%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-secondary me-1">Common area</span> <span class="fw-semibold">15.8%</span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-secondary" style="width: 16%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-warning text-dark me-1">Lower-end</span> <span class="fw-semibold">High</span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 90%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">Recommended Spend</label>
                                <h3 class="fw-bold text-primary">$5,000</h3>
                                <small>Suggested Budget</small>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Expected Reach</label>
                                <h3 class="fw-bold">25,000</h3>
                                <small>Users</small>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Expected Leads</label>
                                <h3 class="fw-bold">220</h3>
                                <small>Leads</small>
                            </div>
                            <div>
                                <label class="text-muted">Expected Conversion</label>
                                <h3 class="fw-bold text-success">18</h3>
                                <small>Orders</small>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="text-end">
                        <button class="btn btn-primary" onclick="boostProduct(101, 5000)"><i class="ri-rocket-line me-1"></i> Apply AI Recommendation</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: AI Insights & Alerts -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header" style="background: linear-gradient(135deg, #4a90c4 0%, #2c6288 100%); color: white;">
                    <h5 class="mb-0"><i class="ri-alert-line me-2"></i> AI Insights & Alerts</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <i class="ri-alert-fill me-2"></i> Sales dropped <strong>38%</strong> in last 7 days.
                    </div>
                    <div class="alert alert-warning">
                        <i class="ri-error-warning-line me-2"></i> Campaign #204: High spend but low quality rate.
                    </div>
                    <div class="alert alert-info">
                        <i class="ri-lightbulb-line me-2"></i> Growth Suggestion: Boost workforce category during weekend.
                    </div>
                    <div class="alert alert-secondary">
                        <i class="ri-shield-check-line me-2"></i> Risk Alert: Seller response time is increasing.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top AI Recommendations Table (Other Products) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%); color: white;">
                    <h5 class="mb-0"><i class="ri-table-line me-2"></i> Top AI Recommendations</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #2563eb; color: white;">
                                <tr>
                                    <th>Product</th>
                                    <th>AI Score</th>
                                    <th>Priority</th>
                                    <th>Recommended Boost</th>
                                    <th>Expected Reach</th>
                                    <th>Expected Enquiries</th>
                                    <th>Expected Conv.</th>
                                    <th>Best Cities</th>
                                    <th>Best Timing</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Dummy rows -->
                                <tr>
                                    <td>Home Cleaning Service</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-success" style="width: 45%;">45/50</div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-danger">High</span></td>
                                    <td>$5,000</td>
                                    <td>25,000</td>
                                    <td>220</td>
                                    <td>18</td>
                                    <td><span class="badge bg-secondary">New York</span> <span class="badge bg-secondary">LA</span> <span class="badge bg-secondary">Chicago</span></td>
                                    <td><span class="badge" style="background: #1e3a5f;">Weekends</span></td>
                                    <td><button class="btn btn-sm btn-primary" onclick="boostProduct(101,5000)"><i class="ri-rocket-line"></i> Boost</button></td>
                                </tr>
                                <tr>
                                    <td>Plumbing Service</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-warning" style="width: 38%;">38/50</div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-warning text-dark">Medium</span></td>
                                    <td>$3,200</td>
                                    <td>18,000</td>
                                    <td>145</td>
                                    <td>12</td>
                                    <td><span class="badge bg-secondary">Dallas</span> <span class="badge bg-secondary">Houston</span></td>
                                    <td><span class="badge" style="background: #1e3a5f;">Mornings</span></td>
                                    <td><button class="btn btn-sm btn-primary" onclick="boostProduct(102,3200)"><i class="ri-rocket-line"></i> Boost</button></td>
                                </tr>
                                <tr>
                                    <td>Electrical Repair</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar bg-info" style="width: 32%;">32/50</div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info">Low</span></td>
                                    <td>$2,000</td>
                                    <td>10,500</td>
                                    <td>88</td>
                                    <td>7</td>
                                    <td><span class="badge bg-secondary">San Jose</span></td>
                                    <td><span class="badge" style="background: #1e3a5f;">Evenings</span></td>
                                    <td><button class="btn btn-sm btn-primary" onclick="boostProduct(103,2000)"><i class="ri-rocket-line"></i> Boost</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Poor Campaign Detection -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header" style="background: linear-gradient(135deg, #dc3545 0%, #b02a37 100%); color: white;">
                    <h5 class="mb-0"><i class="ri-error-warning-line me-2"></i> Poor Campaign Detection - Needs Attention</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background: #6c757d; color: white;">
                                <tr>
                                    <th>Product</th>
                                    <th>Seller</th>
                                    <th>Boost Spend</th>
                                    <th>Revenue</th>
                                    <th>ROI</th>
                                    <th>Status</th>
                                    <th>Suggestion</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Carpet Cleaning</td>
                                    <td>CleanCo</td>
                                    <td>$4,500</td>
                                    <td>$2,800</td>
                                    <td class="text-danger">-37.8%</td>
                                    <td><span class="badge bg-danger">Underperforming</span></td>
                                    <td><button class="btn btn-sm btn-warning" onclick="optimizeCampaign(201)"><i class="ri-lightbulb-line"></i> Optimize</button></td>
                                </tr>
                                <tr>
                                    <td>AC Service</td>
                                    <td>CoolAir</td>
                                    <td>$3,000</td>
                                    <td>$1,500</td>
                                    <td class="text-danger">-50%</td>
                                    <td><span class="badge bg-danger">High Spend, Low Return</span></td>
                                    <td><button class="btn btn-sm btn-warning" onclick="optimizeCampaign(202)"><i class="ri-lightbulb-line"></i> Optimize</button></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function boostProduct(productId, amount) {
    if(confirm(`Boost this product with $${amount.toLocaleString()}?`)) {
        // Simulate AJAX
        alert(`Boost request sent for product ${productId} with $${amount}. (Demo)`);
        // In real implementation, call your route:
        // $.post("{{ route('admin.products.boost') }}", { product_id: productId, amount: amount, _token: "{{ csrf_token() }}" })
    }
}

function optimizeCampaign(productId) {
    if(confirm('Get AI optimization suggestions for this campaign?')) {
        alert(`AI optimization suggestions for product ${productId}:\n- Pause campaign\n- Reduce budget by 40%\n- Target different cities`);
        // Real AJAX call here
    }
}
</script>
@endsection