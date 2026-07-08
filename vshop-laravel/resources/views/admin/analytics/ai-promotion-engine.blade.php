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
                @if(count($recommendations) > 0)
                @php $topRec = $recommendations[0]; @endphp
                <div class="card-header" style="background: linear-gradient(135deg, #2c5f8a 0%, #1a3a52 100%); color: white;">
                    <h5 class="mb-0"><i class="ri-lightbulb-line me-2"></i> AI Promotion Recommendation for {{ Str::limit($topRec['product']->name, 35) }}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>AI Analyst Insights</h6>
                            <div class="mb-3">
                                <span class="badge bg-primary me-1">Category Popularity</span> <span class="fw-semibold">{{ number_format($topRec['factors']['category_popularity'] * 10, 0) }}%</span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $topRec['factors']['category_popularity'] * 10 }}%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-info me-1">Conversion Rate</span> <span class="fw-semibold">{{ number_format($topRec['factors']['conversion_rate'] * 10, 0) }}%</span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-info" style="width: {{ $topRec['factors']['conversion_rate'] * 10 }}%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-secondary me-1">Seasonality Match</span> <span class="fw-semibold">{{ number_format($topRec['factors']['seasonality'] * 10, 0) }}%</span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-secondary" style="width: {{ $topRec['factors']['seasonality'] * 10 }}%"></div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <span class="badge bg-warning text-dark me-1">Overall AI Score</span> <span class="fw-semibold">{{ number_format($topRec['ai_score'] * 2, 0) }}%</span>
                                <div class="progress mt-1" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: {{ $topRec['ai_score'] * 2 }}%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="text-muted">Recommended Spend</label>
                                <h3 class="fw-bold text-primary">₹{{ number_format($topRec['recommended_boost']) }}</h3>
                                <small>Suggested Budget</small>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Expected Reach</label>
                                <h3 class="fw-bold">{{ number_format($topRec['expected_reach']) }}</h3>
                                <small>Users</small>
                            </div>
                            <div class="mb-3">
                                <label class="text-muted">Expected Leads</label>
                                <h3 class="fw-bold">{{ number_format($topRec['expected_enquiries']) }}</h3>
                                <small>Leads</small>
                            </div>
                            <div>
                                <label class="text-muted">Expected Conversion</label>
                                <h3 class="fw-bold text-success">{{ number_format($topRec['expected_conversions']) }}</h3>
                                <small>Orders</small>
                            </div>
                        </div>
                    </div>
                @else
                <div class="card-header" style="background: linear-gradient(135deg, #2c5f8a 0%, #1a3a52 100%); color: white;">
                    <h5 class="mb-0"><i class="ri-lightbulb-line me-2"></i> AI Promotion Engine Gathering Data</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted py-5 text-center">Not enough data to generate recommendations yet. Run some campaigns to train the AI engine.</p>
                @endif
                    <hr>
                    <div class="text-end">
                        <button class="btn btn-primary" onclick="boostProduct({{ $topRec['product']->id ?? 0 }}, {{ $topRec['recommended_boost'] ?? 0 }})"><i class="ri-rocket-line me-1"></i> Apply AI Recommendation</button>
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
                    @if($poorCampaigns->count() > 0)
                        @foreach($poorCampaigns as $poor)
                            <div class="alert alert-warning">
                                <i class="ri-error-warning-line me-2"></i> Campaign <strong>{{ Str::limit($poor['product']->name, 15) }}</strong>: High spend but low ROI ({{ number_format($poor['roi'], 1) }}%).
                            </div>
                        @endforeach
                    @else
                        <div class="alert alert-success">
                            <i class="ri-shield-check-line me-2"></i> All campaigns are performing at acceptable levels.
                        </div>
                    @endif
                    
                    @if(count($recommendations) > 0 && $recommendations[0]['priority'] == 'High')
                        <div class="alert alert-info">
                            <i class="ri-lightbulb-line me-2"></i> Growth Suggestion: Boost <strong>{{ Str::limit($recommendations[0]['product']->name, 15) }}</strong> in {{ $recommendations[0]['best_cities'][0] ?? 'top cities' }}.
                        </div>
                    @endif
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
                                @forelse($recommendations as $rec)
                                <tr>
                                    <td>{{ Str::limit($rec['product']->name, 30) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $rec['ai_score'] > 40 ? 'bg-success' : ($rec['ai_score'] > 30 ? 'bg-warning' : 'bg-danger') }}" style="width: {{ min(100, $rec['ai_score'] * 2) }}%;">{{ number_format($rec['ai_score'], 1) }}/50</div>
                                        </div>
                                    </td>
                                    <td><span class="badge {{ $rec['priority'] == 'High' ? 'bg-danger' : ($rec['priority'] == 'Medium' ? 'bg-warning text-dark' : 'bg-info') }}">{{ $rec['priority'] }}</span></td>
                                    <td class="fw-bold">₹{{ number_format($rec['recommended_boost']) }}</td>
                                    <td>{{ number_format($rec['expected_reach']) }}</td>
                                    <td>{{ number_format($rec['expected_enquiries']) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($rec['expected_conversions']) }}</td>
                                    <td>
                                        @foreach($rec['best_cities'] as $city)
                                            <span class="badge bg-secondary">{{ $city }}</span>
                                        @endforeach
                                    </td>
                                    <td><span class="badge" style="background: #1e3a5f;">{{ $rec['best_timing'] }}</span></td>
                                    <td><button class="btn btn-sm btn-primary" onclick="boostProduct({{ $rec['product']->id }}, {{ $rec['recommended_boost'] }})"><i class="ri-rocket-line"></i> Boost</button></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="10" class="text-center py-4 text-muted">No recommendations generated at the moment.</td>
                                </tr>
                                @endforelse
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
