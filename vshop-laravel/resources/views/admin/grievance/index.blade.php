@extends('layouts.vertical', ['title' => 'Grievance & Dispute Management'])

@section('css')
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
    /* ===== BASE CARDS ===== */
    .metric-card {
        border-radius: 12px;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 10px rgba(0,0,0,0.04);
    }
    .metric-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0,67,146,0.1);
    }
    .metric-icon-box {
        width: 48px; height: 48px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .badge-premium {
        padding: 5px 12px;
        border-radius: 30px;
        font-weight: 550;
        font-size: 11px;
        letter-spacing: 0.3px;
    }

    /* ===== TABS ===== */
    .grievance-main-tabs .nav-link {
        color: #6c757d;
        font-weight: 600;
        font-size: 13px;
        padding: 10px 20px;
        border-radius: 8px 8px 0 0;
        border: 1px solid transparent;
    }
    .grievance-main-tabs .nav-link.active {
        color: #0043a8;
        background: #fff;
        border-color: #dee2e6 #dee2e6 #fff;
    }

    /* ===== DONUT CHART ===== */
    .donut-legend-item { display: flex; align-items: center; margin-bottom: 6px; font-size: 13px; }
    .donut-dot { width: 12px; height: 12px; border-radius: 50%; margin-right: 8px; flex-shrink: 0; }

    /* ===== TICKET TABLE ===== */
    .table-responsive { border-radius: 10px; overflow: hidden; }
    .ticket-action-btn { width: 30px; height: 30px; border-radius: 6px; display: inline-flex; align-items: center; justify-content: center; cursor: pointer; border: none; transition: all 0.2s; }

    /* ===== CHAT ===== */
    .chat-container {
        max-height: 300px; overflow-y: auto;
        padding: 15px; background: #f8f9fa;
        border-radius: 8px; border: 1px solid #e9ecef;
        margin-bottom: 16px;
    }
    .chat-bubble { max-width: 82%; margin-bottom: 14px; padding: 11px 15px; border-radius: 12px; position: relative; }
    .chat-bubble-admin  { background:#eef2ff; color:#1e1b4b; margin-left:auto; border-bottom-right-radius:2px; }
    .chat-bubble-seller { background:#ecfdf5; color:#064e3b; margin-right:auto; border-bottom-left-radius:2px; }
    .chat-bubble-buyer  { background:#fffbeb; color:#78350f; margin-right:auto; border-bottom-left-radius:2px; }
    .chat-sender-meta { font-size:10px; font-weight:600; text-transform:uppercase; margin-bottom:3px; opacity:.7; }
    .chat-time-meta   { font-size:9px; opacity:.6; text-align:right; margin-top:3px; }

    /* ===== TIMELINE ===== */
    .ticket-timeline { position: relative; padding-left: 24px; }
    .ticket-timeline::before { content:''; position:absolute; left:8px; top:0; bottom:0; width:2px; background:#e9ecef; }
    .timeline-item { position:relative; margin-bottom:16px; }
    .timeline-dot  { position:absolute; left:-20px; top:4px; width:12px; height:12px; border-radius:50%; border:2px solid #fff; box-shadow:0 0 0 2px #dee2e6; }

    /* ===== ESCALATION BANNER ===== */
    .escalation-rule-banner {
        background: linear-gradient(135deg,#fef2f2 0%,#fee2e2 100%);
        border-left: 4px solid #ef4444;
        border-radius: 8px; padding: 12px 16px;
        color: #991b1b; font-size: 12.5px;
    }

    /* ===== ESCALATION MATRIX ===== */
    .matrix-rule-row { background:#f8f9fa; border-radius:8px; padding:10px 14px; margin-bottom:8px; font-size:13px; }
    .matrix-rule-row:hover { background:#f0f4ff; }
    .priority-badge-box { border-radius:10px; padding:14px; text-align:center; }

    /* ===== RESOLVED BOX ===== */
    #resolved_message_box { background:#e6f7f0; color:#10b981; border-radius:8px; padding:1.25rem; }

    /* ===== ATTACHMENT THUMB ===== */
    .attachment-thumb { width:70px; height:70px; border-radius:8px; object-fit:cover; border:1px solid #dee2e6; cursor:pointer; }
    .attachment-file  { width:70px; height:70px; border-radius:8px; border:1px solid #dee2e6; display:flex; flex-direction:column; align-items:center; justify-content:center; font-size:11px; color:#6c757d; background:#f8f9fa; cursor:pointer; }
</style>
@endsection

@section('content')
<div class="container-fluid pt-3">

    <!-- ===== HEADER ===== -->
    <div class="row align-items-center mb-3">
        <div class="col-sm-6">
            <h4 class="mb-0 text-dark fw-bold">Grievance & Dispute Management</h4>
            <small class="text-muted">Platform-wide ticket registry and escalation control</small>
        </div>
        <div class="col-sm-6 text-sm-end d-flex gap-2 justify-content-sm-end">
          
            <button class="btn btn-primary px-4" data-bs-toggle="modal" data-bs-target="#createComplaintModal">
                <i class="bx bx-plus-circle me-1"></i> File New Complaint
            </button>
        </div>
    </div>

    <!-- ===== SUCCESS ALERT ===== -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
        <i class="bx bx-check-circle me-2 fs-5 align-middle"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- ===== STATS ROW ===== -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon-box bg-primary bg-opacity-10 text-primary me-3"><i class="bx bx-receipt"></i></div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1" style="font-size:11px;">Open Tickets</h6>
                        <h3 class="mb-0 fw-bold">{{ \App\Models\Grievance::whereIn('status',['open','in_progress'])->count() }}</h3>
                        <small class="text-primary" style="cursor:pointer;">View all</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon-box bg-danger bg-opacity-10 text-danger me-3"><i class="bx bx-shield-quarter"></i></div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1" style="font-size:11px;">Escalated</h6>
                        <h3 class="mb-0 fw-bold text-danger">{{ \App\Models\Grievance::where('status','escalated')->count() }}</h3>
                        <small class="text-danger" style="cursor:pointer;">View all</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon-box bg-warning bg-opacity-10 text-warning me-3"><i class="bx bx-alarm-exclamation"></i></div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1" style="font-size:11px;">High Priority</h6>
                        <h3 class="mb-0 fw-bold text-warning">{{ \App\Models\Grievance::where('priority','high')->count() }}</h3>
                        <small class="text-warning" style="cursor:pointer;">View all</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card metric-card">
                <div class="card-body d-flex align-items-center">
                    <div class="metric-icon-box bg-success bg-opacity-10 text-success me-3"><i class="bx bx-check-double"></i></div>
                    <div>
                        <h6 class="text-muted text-uppercase mb-1" style="font-size:11px;">Resolved Today</h6>
                        @php
                            $resolvedToday = \App\Models\Grievance::where('status','resolved')->whereDate('updated_at', today())->count();
                        @endphp
                        <h3 class="mb-0 fw-bold text-success">{{ $resolvedToday }}</h3>
                        <small class="text-success" style="cursor:pointer;">View all</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== MAIN TABS ===== -->
    <ul class="nav nav-tabs grievance-main-tabs mb-0 border-bottom-0" id="grievanceTabs">
        <li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#tab_dashboard"><i class="bx bx-pie-chart-alt me-1"></i>Dispute Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_tickets"><i class="bx bx-list-ul me-1"></i> All Tickets</a></li>
        <li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#tab_escalation"><i class="bx bx-git-branch me-1"></i> Escalation Matrix</a></li>
    </ul>

    <div class="tab-content bg-white border border-top-0 rounded-bottom shadow-sm p-4">

        <!-- ==================== TAB 1: DISPUTE DASHBOARD ==================== -->
        <div class="tab-pane fade show active" id="tab_dashboard">
            <div class="row g-4">

                <!-- Left: Donut Chart -->
                <div class="col-lg-5">
                    <h6 class="fw-bold text-dark mb-3">Ticket Summary</h6>
                    <div class="d-flex align-items-center gap-4">
                        <div style="position:relative; width:180px; height:180px; flex-shrink:0;">
                            <canvas id="donutChart" width="180" height="180"></canvas>
                        </div>
                        <div class="flex-grow-1">
                            @php
                                $typeCounts = [
                                    'seller_buyer'    => \App\Models\Grievance::where('type','seller_buyer')->count(),
                                    'seller_platform' => \App\Models\Grievance::where('type','seller_platform')->count(),
                                    'lead_dispute'    => \App\Models\Grievance::where('type','lead_dispute')->count(),
                                    'fake_enquiry'    => \App\Models\Grievance::where('type','fake_enquiry')->count(),
                                    'click_fraud'     => \App\Models\Grievance::where('type','click_fraud')->count(),
                                ];
                                $grandTotal = array_sum($typeCounts);
                                $typeColors = ['#4f46e5','#06b6d4','#f59e0b','#ef4444','#374151'];
                                $typeLabels = ['Seller ↔ Buyer','Seller ↔ Platform','Lead Charge Disputes','Fake Enquiry Complaints','Click Fraud Complaints'];
                                $typeKeys   = array_keys($typeCounts);
                                $typeVals   = array_values($typeCounts);
                            @endphp
                            @foreach($typeLabels as $i => $lbl)
                            <div class="donut-legend-item">
                                <div class="donut-dot" style="background:{{ $typeColors[$i] }};"></div>
                                <span class="text-muted flex-grow-1">{{ $lbl }}</span>
                                <span class="fw-bold text-dark ms-2">{{ $typeVals[$i] }} <small class="text-muted">({{ $grandTotal > 0 ? round(($typeVals[$i]/$grandTotal)*100) : 0 }}%)</small></span>
                            </div>
                            @endforeach
                            <div class="mt-2 border-top pt-2">
                                <strong>Total: {{ $grandTotal }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Recent Cases -->
                <div class="col-lg-7">
                    <h6 class="fw-bold text-dark mb-3">Recent Cases</h6>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                            <thead class="table-light text-uppercase text-muted" style="font-size:11px;">
                                <tr>
                                    <th>Ticket ID</th>
                                    <th>Type</th>
                                    <th>Raised By</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Created Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(\App\Models\Grievance::with(['seller','buyer'])->latest()->take(6)->get() as $rg)
                                <tr>
                                    <td><a href="#" class="fw-bold text-primary viewGrievanceBtn" data-id="{{ $rg->id }}">#TK{{ str_pad($rg->id,5,'0',STR_PAD_LEFT) }}</a></td>
                                    <td>
                                        @php $tmap=['seller_buyer'=>'Seller ↔ Buyer','seller_platform'=>'Seller ↔ Platform','lead_dispute'=>'Lead Charge Dispute','fake_enquiry'=>'Fake Enquiry','click_fraud'=>'Click Fraud']; @endphp
                                        <small>{{ $tmap[$rg->type] ?? $rg->type }}</small>
                                    </td>
                                    <td>
                                        @if($rg->buyer) {{ $rg->buyer->name ?? 'Buyer' }} @elseif($rg->seller) {{ $rg->seller->name ?? 'Seller' }} @else Platform @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $rg->priority=='high'?'danger':($rg->priority=='medium'?'warning text-dark':'info') }} badge-premium">{{ ucfirst($rg->priority) }}</span>
                                    </td>
                                    <td>
                                        @if($rg->status=='open') <span class="badge bg-soft-danger text-danger badge-premium">Open</span>
                                        @elseif($rg->status=='in_progress') <span class="badge bg-soft-warning text-warning badge-premium">In Progress</span>
                                        @elseif($rg->status=='resolved') <span class="badge bg-soft-success text-success badge-premium">Resolved</span>
                                        @elseif($rg->status=='escalated') <span class="badge bg-danger badge-premium text-white">Escalated</span>
                                        @endif
                                    </td>
                                    <td><small class="text-muted">{{ $rg->created_at->format('d M, Y') }}</small></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- ==================== TAB 2: ALL TICKETS ==================== -->
        <div class="tab-pane fade" id="tab_tickets">

            <!-- Filter Bar -->
            <form method="GET" action="{{ route('admin.grievance.index') }}" class="mb-3">
                <div class="row g-2 align-items-center">
                    <div class="col-md-2">
                        <select name="type" class="form-select form-select-sm">
                            <option value="">All Types</option>
                            <option value="seller_buyer"    {{ request('type')=='seller_buyer'    ?'selected':'' }}>Seller ↔ Buyer</option>
                            <option value="seller_platform" {{ request('type')=='seller_platform' ?'selected':'' }}>Seller ↔ Platform</option>
                            <option value="lead_dispute"    {{ request('type')=='lead_dispute'    ?'selected':'' }}>Lead Dispute</option>
                            <option value="fake_enquiry"    {{ request('type')=='fake_enquiry'    ?'selected':'' }}>Fake Enquiry</option>
                            <option value="click_fraud"     {{ request('type')=='click_fraud'     ?'selected':'' }}>Click Fraud</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm">
                            <option value="">All Status</option>
                            <option value="open"        {{ request('status')=='open'        ?'selected':'' }}>Open</option>
                            <option value="in_progress" {{ request('status')=='in_progress' ?'selected':'' }}>In Progress</option>
                            <option value="resolved"    {{ request('status')=='resolved'    ?'selected':'' }}>Resolved</option>
                            <option value="escalated"   {{ request('status')=='escalated'   ?'selected':'' }}>Escalated</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="priority" class="form-select form-select-sm">
                            <option value="">All Priorities</option>
                            <option value="high"   {{ request('priority')=='high'   ?'selected':'' }}>High</option>
                            <option value="medium" {{ request('priority')=='medium' ?'selected':'' }}>Medium</option>
                            <option value="low"    {{ request('priority')=='low'    ?'selected':'' }}>Low</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="seller_id" class="form-select form-select-sm">
                            <option value="">All Sellers</option>
                            @foreach($sellers as $s)
                            <option value="{{ $s->id }}" {{ request('seller_id')==$s->id?'selected':'' }}>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="input-group input-group-sm">
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}" placeholder="From">
                            <span class="input-group-text">–</span>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}" placeholder="To">
                        </div>
                    </div>
                    <div class="col-md-1 d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm w-50">Filter</button>
                        <a href="{{ route('admin.grievance.index') }}" class="btn btn-light btn-sm w-50">Reset</a>
                    </div>
                </div>
            </form>

            <!-- Tickets Table -->
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size:13px;">
                    <thead class="table-light text-uppercase text-muted" style="font-size:11px;">
                        <tr>
                            <th class="ps-3">Ticket ID</th>
                            <th>Type</th>
                            <th>Raised By</th>
                            <th>Seller</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th class="text-end pe-3">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($grievances as $grv)
                        <tr>
                            <td class="ps-3">
                                <a href="#" class="fw-bold text-primary viewGrievanceBtn" data-id="{{ $grv->id }}">#TK{{ str_pad($grv->id,5,'0',STR_PAD_LEFT) }}</a>
                            </td>
                            <td>
                                @if($grv->type=='seller_buyer')    <span class="badge bg-soft-primary text-primary px-2 py-1">Seller ↔ Buyer</span>
                                @elseif($grv->type=='seller_platform') <span class="badge bg-soft-info text-info px-2 py-1">Seller ↔ Platform</span>
                                @elseif($grv->type=='lead_dispute')    <span class="badge bg-soft-warning text-warning px-2 py-1">Lead Dispute</span>
                                @elseif($grv->type=='fake_enquiry')    <span class="badge bg-soft-danger text-danger px-2 py-1">Fake Enquiry</span>
                                @elseif($grv->type=='click_fraud')     <span class="badge bg-soft-dark text-dark px-2 py-1">Click Fraud</span>
                                @endif
                            </td>
                            <td>
                                @if($grv->buyer) <span>{{ $grv->buyer->name ?? '—' }}</span><br><small class="text-muted">Buyer</small>
                                @elseif($grv->seller) <span>{{ $grv->seller->name ?? '—' }}</span><br><small class="text-muted">Seller</small>
                                @else <span class="text-muted">Platform</span>
                                @endif
                            </td>
                            <td>{{ $grv->seller->name ?? '—' }}</td>
                            <td>
                                @if($grv->priority=='high')   <span class="badge bg-danger badge-premium">High</span>
                                @elseif($grv->priority=='medium') <span class="badge bg-warning text-dark badge-premium">Medium</span>
                                @else <span class="badge bg-info badge-premium">Low</span>
                                @endif
                            </td>
                            <td>
                                @if($grv->status=='open')        <span class="badge bg-soft-danger text-danger badge-premium">Open</span>
                                @elseif($grv->status=='in_progress') <span class="badge bg-soft-warning text-warning badge-premium">In Progress</span>
                                @elseif($grv->status=='resolved')    <span class="badge bg-soft-success text-success badge-premium">Resolved</span>
                                @elseif($grv->status=='escalated')   <span class="badge bg-danger text-white badge-premium"><i class="bx bx-alarm-exclamation me-1"></i>Escalated</span>
                                @endif
                            </td>
                            <td><small class="text-muted">{{ $grv->created_at->format('d M, Y') }}</small></td>
                            <td class="text-end pe-3">
                                <div class="d-inline-flex gap-1">
                                    <!-- View -->
                                    <button class="ticket-action-btn bg-primary bg-opacity-10 text-primary viewGrievanceBtn" data-id="{{ $grv->id }}" title="View Details">
                                        <i class="bx bx-show-alt"></i>
                                    </button>
                                    <!-- Quick Assign -->
                                   
                                    <!-- Quick Resolve -->
                                    @if($grv->status !== 'resolved')
                                    <button class="ticket-action-btn bg-success bg-opacity-10 text-success quickResolveBtn" data-id="{{ $grv->id }}" title="Mark Resolved">
                                        <i class="bx bx-check"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="8" class="text-center py-5 text-muted">No Dispute Tickets Found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="px-2 py-3 border-top d-flex justify-content-between align-items-center">
                <small class="text-muted">Showing {{ $grievances->firstItem() }}–{{ $grievances->lastItem() }} of {{ $grievances->total() }} entries</small>
                {{ $grievances->links() }}
            </div>
        </div>

        <!-- ==================== TAB 4: ESCALATION MATRIX ==================== -->
        <div class="tab-pane fade" id="tab_escalation">
            <div class="row g-4">

                <!-- Left: Rules List -->
                <div class="col-lg-8">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold text-dark mb-0">Escalation Rules</h6>
                        <button class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addEscalationRuleModal">
                            <i class="bx bx-plus me-1"></i> Add Rule
                        </button>
                    </div>

                    <!-- Rule Header -->
                    <div class="row g-0 px-3 py-2 text-uppercase text-muted fw-semibold" style="font-size:11px; background:#f8f9fa; border-radius:8px;">
                        <div class="col-3">Rule</div>
                        <div class="col-3">Condition</div>
                        <div class="col-2">Action</div>
                        <div class="col-2">Assign To</div>
                        <div class="col-2">Priority</div>
                    </div>

                    @php
                        // Try to fetch from database if table exists, otherwise use hardcoded array
                        $escalationRules = [];
                        if (Schema::hasTable('escalation_rules')) {
                            $escalationRules = \App\Models\EscalationRule::orderByRaw("FIELD(priority, 'high', 'medium', 'low')")->get();
                        } else {
                            // Fallback hardcoded rules
                            $escalationRules = collect([
                                (object)['rule_name'=>'No Response Rule', 'condition'=>'No response in 24 hours', 'action'=>'Auto Escalate', 'assign_to'=>'Support Team', 'priority'=>'high'],
                                (object)['rule_name'=>'High Value Order', 'condition'=>'Order value > ₹10,000', 'action'=>'Auto Escalate', 'assign_to'=>'Senior Admin', 'priority'=>'high'],
                                (object)['rule_name'=>'Lead Dispute', 'condition'=>'Lead dispute amount > ₹500', 'action'=>'Escalate', 'assign_to'=>'Finance Team', 'priority'=>'medium'],
                                (object)['rule_name'=>'Multiple Complaints', 'condition'=>'3 complaints in 7 days', 'action'=>'Escalate', 'assign_to'=>'Operations Team', 'priority'=>'medium'],
                                (object)['rule_name'=>'General', 'condition'=>'Default Escalation', 'action'=>'Assign', 'assign_to'=>'Support Team', 'priority'=>'low'],
                            ]);
                        }
                    @endphp

                    <div class="mt-2">
                        @foreach($escalationRules as $rule)
                        <div class="matrix-rule-row row g-0 align-items-center">
                            <div class="col-3 fw-semibold text-dark">{{ $rule->rule_name ?? $rule['rule'] ?? '' }}</div>
                            <div class="col-3 text-muted">{{ $rule->condition ?? '' }}</div>
                            <div class="col-2">
                                <span class="badge bg-soft-primary text-primary">{{ $rule->action ?? '' }}</span>
                            </div>
                            <div class="col-2 text-muted">{{ $rule->assign_to ?? '' }}</div>
                            <div class="col-2">
                                @php $priority = $rule->priority ?? 'low'; @endphp
                                <span class="badge bg-{{ $priority=='high'?'danger':($priority=='medium'?'warning text-dark':'secondary') }} badge-premium">{{ ucfirst($priority) }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <!-- Priority Settings -->
                    <h6 class="fw-bold text-dark mt-4 mb-3">Priority Settings</h6>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="priority-badge-box border border-danger">
                                <h6 class="text-danger fw-bold mb-1">High</h6>
                                <small class="text-muted">Escalate Immediately</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="priority-badge-box border border-warning">
                                <h6 class="text-warning fw-bold mb-1">Medium</h6>
                                <small class="text-muted">Escalate in 24 Hours</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="priority-badge-box border border-secondary">
                                <h6 class="text-secondary fw-bold mb-1">Low</h6>
                                <small class="text-muted">Escalate in 72 Hours</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Assign To Teams -->
                <div class="col-lg-4">
                    <h6 class="fw-bold text-dark mb-3">Assign To (Teams)</h6>
                    <div class="row g-3">
                        @php
                            $teams = [
                                ['icon'=>'bx-support','label'=>'Support Team','color'=>'primary'],
                                ['icon'=>'bx-cog','label'=>'Operations Team','color'=>'warning'],
                                ['icon'=>'bx-money','label'=>'Finance Team','color'=>'success'],
                                ['icon'=>'bx-user-check','label'=>'Senior Admin','color'=>'danger'],
                            ];
                        @endphp
                        @foreach($teams as $t)
                        <div class="col-6">
                            <div class="card border-0 bg-light text-center p-3">
                                <i class="bx {{ $t['icon'] }} fs-2 text-{{ $t['color'] }} mb-1"></i>
                                <small class="fw-semibold text-dark">{{ $t['label'] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <h6 class="fw-bold text-dark mt-4 mb-2">Active Staff Members</h6>
                    <div class="list-group list-group-flush">
                        @foreach($staffMembers as $staff)
            <option value="{{ $staff->name ?? $staff->first_name . ' ' . $staff->last_name }}">
                {{ $staff->first_name }} {{ $staff->last_name }} 
                ({{ ucfirst($staff->type ?? 'Staff') }})
            </option>
        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div><!-- end tab-content -->
</div><!-- end container -->


<!-- ==================== MODALS ==================== -->

<!-- Modal: File New Complaint -->
<div class="modal fade" id="createComplaintModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <h5 class="modal-title fw-bold">File New Grievance / Dispute</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.grievance.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Dispute Category <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="seller_buyer">Seller ↔ Buyer</option>
                                <option value="seller_platform">Seller ↔ Platform</option>
                                <option value="lead_dispute">Lead Charge Dispute</option>
                                <option value="fake_enquiry">Fake Enquiry Complaint</option>
                                <option value="click_fraud">Click Fraud Complaint</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Priority Level <span class="text-danger">*</span></label>
                            <select name="priority" class="form-select" required>
                                <option value="low">Low</option>
                                <option value="medium" selected>Medium</option>
                                <option value="high">High</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Subject / Title <span class="text-danger">*</span></label>
                            <input type="text" name="subject" class="form-control" placeholder="Brief subject of the complaint" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Linked Seller</label>
                            <select name="seller_id" class="form-select">
                                <option value="">Select Seller</option>
                                @foreach($sellers as $s)
                                <option value="{{ $s->id }}">{{ $s->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Linked Buyer</label>
                            <select name="buyer_id" class="form-select">
                                <option value="">Select Buyer</option>
                                @foreach($buyers as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->email }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Linked Order</label>
                            <select name="order_id" class="form-select">
                                <option value="">Select Order</option>
                                @foreach($orders as $o)
                                <option value="{{ $o->id }}">{{ $o->transaction_id }} ({{ $o->total_amount }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Disputed Amount (INR)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" placeholder="e.g. 1500.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Escalation Timeout <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" name="no_response_hours" class="form-control" value="24" min="1" max="168" required>
                                <span class="input-group-text">Hours</span>
                            </div>
                            <small class="text-muted">Auto-escalate if no reply</small>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Grievance Description <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Enter detailed complaint information..." required></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Attachments <small class="text-muted fw-normal">(Images, PDFs, etc.)</small></label>
                            <input type="file" name="attachments[]" class="form-control" multiple accept="image/*,.pdf,.doc,.docx">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary px-4">File Complaint</button>
                </div>
            </form>
        </div>
    </div>
</div>





<!-- Modal: Add Escalation Rule (with staff dropdown) -->
<div class="modal fade" id="addEscalationRuleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0">
                <h5 class="modal-title fw-bold">Add Escalation Rule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold">Rule Name</label>
                        <input type="text" name="rule_name" id="rule_name" class="form-control" placeholder="e.g. No Response Rule" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Condition</label>
                        <input type="text" name="condition" id="condition" class="form-control" placeholder="e.g. No response in 24 hours" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Action</label>
                        <select name="action" id="action" class="form-select" required>
                            <option>Auto Escalate</option>
                            <option>Escalate</option>
                            <option>Assign</option>
                            <option>Notify</option>
                        </select>
                    </div>
                  <div class="col-md-6">
    <label class="form-label fw-semibold">Assign To</label>
    <select name="assign_to" id="assign_to" class="form-select" required>
        <option value="">Select Staff</option>
        @foreach($staffMembers as $staff)
            <option value="{{ $staff->name ?? $staff->first_name . ' ' . $staff->last_name }}">
                {{ $staff->first_name }} {{ $staff->last_name }} 
                ({{ ucfirst($staff->type ?? 'Staff') }})
            </option>
        @endforeach
    </select>
</div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Priority</label>
                        <select name="priority" id="priority" class="form-select" required>
                            <option value="high">High</option>
                            <option value="medium" selected>Medium</option>
                            <option value="low">Low</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveRuleBtn">Save Rule</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal: Ticket Details (11.3) -->
<div class="modal fade" id="detailsGrievanceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary text-white border-0 py-3">
                <div>
                    <h5 class="modal-title fw-bold mb-0">Ticket Details</h5>
                    <small class="opacity-75">Ticket ID: <span id="lbl_ticket_id_header"></span></small>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <div class="row g-0">

                    <!-- LEFT PANE: Details + Actions -->
                    <div class="col-lg-5 border-end p-4">

                        <!-- Ticket Meta -->
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <span class="fw-bold text-dark fs-6" id="lbl_ticket_id"></span>
                            <span class="badge" id="lbl_ticket_status"></span>
                        </div>

                        <!-- Escalation Banner -->
                        <div class="escalation-rule-banner mb-3 d-none" id="escalation_banner">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-alarm-exclamation fs-4 me-2"></i>
                                <div>
                                    <strong class="d-block">Escalation Protocol Triggered!</strong>
                                    <span id="lbl_escalation_reason"></span>
                                </div>
                            </div>
                        </div>

                        <table class="table table-bordered align-middle mb-3" style="font-size:13px;">
                            <tr><th class="bg-light text-muted w-35">Type</th><td id="lbl_ticket_type" class="fw-semibold"></td></tr>
                            <tr><th class="bg-light text-muted">Raised By</th><td id="lbl_raised_by"></td></tr>
                            <tr><th class="bg-light text-muted">Seller</th><td id="lbl_seller_name"></td></tr>
                            <tr><th class="bg-light text-muted">Status</th><td id="lbl_ticket_status2"></td></tr>
                            <tr><th class="bg-light text-muted">Created On</th><td id="lbl_ticket_created"></td></tr>
                            <tr><th class="bg-light text-muted">Priority</th><td id="lbl_ticket_priority"></td></tr>
                            <tr><th class="bg-light text-muted">Agent</th><td id="lbl_ticket_agent"></td></tr>
                        </table>

                        <!-- Issue Description -->
                        <h6 class="fw-bold text-dark mb-2">Issue Description</h6>
                        <div class="p-3 bg-light rounded mb-3" id="lbl_issue_description" style="font-size:13px;"></div>

                        <!-- Actions Panel -->
                        <div id="investigation_actions_panel">
                            <h6 class="fw-bold text-dark mb-2"><i class="bx bx-cog me-1 text-primary"></i>Actions</h6>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-primary btn-sm" id="btnOpenAssignPanel"><i class="bx bx-user-plus me-1"></i>Assign</button>
                                <button class="btn btn-danger btn-sm" id="btnOpenEscalatePanel"><i class="bx bx-alarm-exclamation me-1"></i>Escalate</button>
                                <button class="btn btn-success btn-sm" id="btnOpenResolvePanel"><i class="bx bx-check me-1"></i>Resolve</button>
                                <button class="btn btn-secondary btn-sm" id="btnCloseTicket"><i class="bx bx-x-circle me-1"></i>Close Ticket</button>
                            </div>

                            <!-- Assign Sub-Panel -->
                            <div class="card border-0 bg-light p-3 mt-3 d-none" id="assignSubPanel">
                                <small class="fw-semibold text-dark mb-2 d-block">Assign to Agent</small>
                                <form id="frm_assign_grievance">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <select name="assigned_to" class="form-select" required id="select_assigned_agent">
                                            <option value="">Select Agent</option>
                                            @foreach($staffMembers as $staff)
                                            <option value="{{ $staff->name }}">{{ $staff->name }} ({{ ucfirst($staff->type ?? '') }})</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-primary">Assign</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Escalate Sub-Panel -->
                            <div class="card border-0 bg-light p-3 mt-3 d-none" id="escalateSubPanel">
                                <small class="fw-semibold text-danger mb-2 d-block">Escalate Manually</small>
                                <form id="frm_escalate_grievance">
                                    @csrf
                                    <input type="text" name="escalation_reason" class="form-control form-control-sm mb-2" placeholder="Reason for senior review" required>
                                    <div class="input-group input-group-sm">
                                        <select name="escalated_to" class="form-select" required>
                                            <option value="Senior Admin">Senior Admin</option>
                                            <option value="Executive Auditor">Executive Auditor</option>
                                            <option value="Finance Team">Finance Team</option>
                                            <option value="Operations Team">Operations Team</option>
                                        </select>
                                        <button type="submit" class="btn btn-danger">Escalate</button>
                                    </div>
                                </form>
                            </div>

                            <!-- Resolve Sub-Panel -->
                            <div class="card border-0 bg-light p-3 mt-3 d-none" id="resolveSubPanel">
                                <small class="fw-semibold text-success mb-2 d-block">Resolve & Close File</small>
                                <form id="frm_resolve_grievance">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="resolution_note" class="form-control" placeholder="Resolution note..." required>
                                        <button type="submit" class="btn btn-success">Resolve</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <!-- Resolved Box -->
                        <div id="resolved_message_box" class="d-none mt-3">
                            <div class="d-flex align-items-center">
                                <i class="bx bx-check-shield me-2 fs-3 text-success"></i>
                                <div>
                                    <strong class="d-block text-success">Resolved</strong>
                                    <span class="fs-12 text-muted" id="lbl_resolution_note"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- RIGHT PANE: Conversation + Timeline + Attachments -->
                    <div class="col-lg-7 p-4 d-flex flex-column" style="background:#fafbfc;">

                        <!-- Conversation / Timeline -->
                        <h6 class="fw-bold text-dark mb-2"><i class="bx bx-message-rounded-dots me-1 text-primary"></i>Conversation / Timeline</h6>
                        <div class="chat-container flex-grow-1" id="timeline_message_stream"></div>

                        <!-- Ticket History -->
                        <h6 class="fw-bold text-dark mb-2 mt-3"><i class="bx bx-history me-1 text-primary"></i>Ticket History</h6>
                        <div class="ticket-timeline mb-3" id="ticket_history_timeline" style="max-height:130px; overflow-y:auto; font-size:12px;"></div>

                        <!-- Attachments -->
                        <h6 class="fw-bold text-dark mb-2"><i class="bx bx-paperclip me-1 text-primary"></i>Attachments</h6>
                        <div id="attachments_area" class="d-flex gap-2 flex-wrap mb-3">
                            <span class="text-muted" style="font-size:12px;">No attachments uploaded.</span>
                        </div>

                        <!-- Reply Form -->
                        <form id="frm_reply_grievance" class="mt-auto">
                            @csrf
                            <input type="hidden" name="grievance_id" id="reply_grievance_id">
                            <div class="mb-2">
                                <div class="d-flex gap-3">
                                    <div class="form-check"><input class="form-check-input" type="radio" name="sender" id="rad_admin" value="admin" checked><label class="form-check-label" for="rad_admin" style="font-size:12px;">Platform Moderator</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="sender" id="rad_seller" value="seller"><label class="form-check-label" for="rad_seller" style="font-size:12px;">Seller</label></div>
                                    <div class="form-check"><input class="form-check-input" type="radio" name="sender" id="rad_buyer" value="buyer"><label class="form-check-label" for="rad_buyer" style="font-size:12px;">Buyer</label></div>
                                </div>
                            </div>
                            <div class="input-group">
                                <textarea name="message" id="reply_message_text" class="form-control" rows="2" placeholder="Add reply, proof or statement..." required></textarea>
                                <button type="submit" class="btn btn-primary px-4 align-self-end h-100">Send</button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// ===== DONUT CHART =====
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('donutChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Seller ↔ Buyer','Seller ↔ Platform','Lead Disputes','Fake Enquiry','Click Fraud'],
                datasets: [{
                    data: [{{ implode(',', $typeVals) }}],
                    backgroundColor: ['#4f46e5','#06b6d4','#f59e0b','#ef4444','#374151'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                cutout: '70%',
                plugins: { legend: { display: false }, tooltip: { enabled: true } },
                animation: { animateRotate: true }
            }
        });
    }
});

// ===== TYPE LABELS =====
const typeLabels = {
    'seller_buyer':    'Seller ↔ Buyer',
    'seller_platform': 'Seller ↔ Platform',
    'lead_dispute':    'Lead Charge Dispute',
    'fake_enquiry':    'Fake Enquiry Complaint',
    'click_fraud':     'Click Fraud Complaint'
};

// ===== VIEW GRIEVANCE MODAL =====
$(document).on("click", ".viewGrievanceBtn", function (e) {
    e.preventDefault();
    const id = $(this).data("id");
    $('#reply_grievance_id').val(id);

    // Reset sub-panels
    $('#assignSubPanel,#escalateSubPanel,#resolveSubPanel').addClass('d-none');

    $.get("/admin/grievance/get/" + id, function (res) {
        if (!res.success) { Swal.fire('Error','Unable to retrieve ticket.','error'); return; }
        const grv = res.grievance;

        // Header
        const tkId = '#TK' + String(grv.id).padStart(5,'0');
        $('#lbl_ticket_id_header').text(tkId);
        $('#lbl_ticket_id').text(tkId);

        // Status badge
        const statusMap = {
            'open':        ['bg-soft-danger text-danger','Open'],
            'in_progress': ['bg-soft-warning text-warning','In Progress'],
            'resolved':    ['bg-soft-success text-success','Resolved'],
            'escalated':   ['bg-danger text-white','Escalated']
        };
        const [sc, sl] = statusMap[grv.status] || ['bg-secondary text-white', grv.status];
        $('#lbl_ticket_status').attr('class','badge ' + sc).text(sl);
        $('#lbl_ticket_status2').html('<span class="badge ' + sc + '">' + sl + '</span>');

        // Meta
        $('#lbl_ticket_type').text(typeLabels[grv.type] || grv.type);
        $('#lbl_raised_by').text(grv.buyer ? (grv.buyer.name || 'Buyer') : (grv.seller ? (grv.seller.name + ' (Seller)') : 'Platform'));
        $('#lbl_seller_name').text(grv.seller ? grv.seller.name : '—');
        const priority = grv.priority || 'low';
        $('#lbl_ticket_priority').html('<span class="badge bg-' + (priority==='high'?'danger':(priority==='medium'?'warning text-dark':'info')) + '">' + priority.toUpperCase() + '</span>');
        $('#lbl_ticket_agent').text(grv.assigned_to || 'Unassigned');
        $('#lbl_ticket_created').text(grv.formatted_date);
        $('#lbl_issue_description').text(grv.message);

        // Resolved / Actions toggle
        if (grv.status === 'resolved' || grv.status === 'closed') {
            $('#investigation_actions_panel').addClass('d-none');
            $('#resolved_message_box').removeClass('d-none');
            $('#lbl_resolution_note').text(grv.resolution_note || 'This ticket has been resolved.');
        } else {
            $('#investigation_actions_panel').removeClass('d-none');
            $('#resolved_message_box').addClass('d-none');
        }

        // Escalation banner
        if (grv.status === 'escalated') {
            $('#escalation_banner').removeClass('d-none');
            $('#lbl_escalation_reason').text(grv.resolution_note || 'Auto-escalation criteria met.');
        } else {
            $('#escalation_banner').addClass('d-none');
        }

        // Chat messages
        let chatHtml = '';
        if (grv.messages && grv.messages.length > 0) {
            grv.messages.forEach(function(msg) {
                let cls = 'chat-bubble-buyer', sn = 'Buyer';
                if (msg.sender === 'admin')  { cls = 'chat-bubble-admin';  sn = 'Moderator (Admin)'; }
                if (msg.sender === 'seller') { cls = 'chat-bubble-seller'; sn = (grv.seller ? grv.seller.name : 'Seller') + ' (Seller)'; }
                if (msg.sender === 'buyer')  { sn = (grv.buyer ? grv.buyer.name : 'Buyer') + ' (Buyer)'; }
                const d = new Date(msg.created_at);
                const time = d.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'});
                const date = d.toLocaleDateString([],{day:'2-digit',month:'short'});
                chatHtml += `<div class="chat-bubble ${cls}">
                    <div class="chat-sender-meta">${sn}</div>
                    <div>${msg.message}</div>
                    <div class="chat-time-meta">${date} ${time}</div>
                </div>`;
            });
        } else {
            chatHtml = '<p class="text-center text-muted py-3">No messages yet.</p>';
        }
        $('#timeline_message_stream').html(chatHtml);
        const c = document.getElementById('timeline_message_stream');
        if(c) c.scrollTop = c.scrollHeight;

        // Ticket History Timeline
        let histHtml = '';
        const historyEvents = [];
        historyEvents.push({ label: 'Ticket Created', time: grv.formatted_date, color: '#4f46e5' });
        if (grv.assigned_to) historyEvents.push({ label: 'Assigned to ' + grv.assigned_to, time: grv.formatted_updated, color: '#f59e0b' });
        if (grv.status === 'escalated') historyEvents.push({ label: 'Escalated to ' + (grv.escalated_to || 'Senior Admin'), time: grv.formatted_updated, color: '#ef4444' });
        if (grv.status === 'resolved')  historyEvents.push({ label: 'Ticket Resolved', time: grv.formatted_updated, color: '#10b981' });
        historyEvents.forEach(function(ev) {
            histHtml += `<div class="timeline-item mb-2">
                <div class="timeline-dot" style="background:${ev.color};"></div>
                <div><strong style="font-size:12px;">${ev.label}</strong><br><small class="text-muted">${ev.time}</small></div>
            </div>`;
        });
        $('#ticket_history_timeline').html(histHtml);

        // Attachments placeholder
        $('#attachments_area').html('<span class="text-muted" style="font-size:12px;">No attachments uploaded.</span>');

        if (typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('detailsGrievanceModal')).show();
        } else {
            $('#detailsGrievanceModal').modal('show');
        }
    });
});

// ===== ACTION PANEL TOGGLES =====
$('#btnOpenAssignPanel').on('click', function(){
    $('#assignSubPanel').toggleClass('d-none');
    $('#escalateSubPanel,#resolveSubPanel').addClass('d-none');
});
$('#btnOpenEscalatePanel').on('click', function(){
    $('#escalateSubPanel').toggleClass('d-none');
    $('#assignSubPanel,#resolveSubPanel').addClass('d-none');
});
$('#btnOpenResolvePanel').on('click', function(){
    $('#resolveSubPanel').toggleClass('d-none');
    $('#assignSubPanel,#escalateSubPanel').addClass('d-none');
});
$('#btnCloseTicket').on('click', function(){
    const id = $('#reply_grievance_id').val();
    Swal.fire({
        title: 'Close Ticket?',
        text: 'This will mark the ticket as closed.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Close It'
    }).then(res => {
        if (res.isConfirmed) {
            $.post("/admin/grievance/" + id + "/resolve", {
                _token: '{{ csrf_token() }}',
                resolution_note: 'Ticket closed by admin.'
            }, function(r) {
                if (r.success) { Swal.fire('Closed','Ticket closed.','success').then(()=>location.reload()); }
            });
        }
    });
});

// ===== QUICK ASSIGN BTN =====
$(document).on('click','.quickAssignBtn', function(){
    const id = $(this).data('id');
    $('#reply_grievance_id').val(id);
    $.get("/admin/grievance/get/" + id, function(res){
        if(res.success){ 
            $('#reply_grievance_id').val(id); 
            $('.viewGrievanceBtn[data-id="'+id+'"]').first().trigger('click');
        }
    });
});

// ===== QUICK RESOLVE BTN =====
$(document).on('click','.quickResolveBtn', function(){
    const id = $(this).data('id');
    Swal.fire({
        title: 'Resolve Ticket #' + id + '?',
        input: 'text',
        inputPlaceholder: 'Enter resolution note...',
        showCancelButton: true,
        confirmButtonText: 'Resolve'
    }).then(res => {
        if (res.isConfirmed && res.value) {
            $.post("/admin/grievance/" + id + "/resolve", {
                _token: '{{ csrf_token() }}',
                resolution_note: res.value
            }, function(r) {
                if (r.success) { Swal.fire('Resolved','','success').then(()=>location.reload()); }
            });
        }
    });
});

// ===== ASSIGN FORM =====
$('#frm_assign_grievance').on('submit', function(e){
    e.preventDefault();
    const id = $('#reply_grievance_id').val();
    $.post("/admin/grievance/" + id + "/assign", $(this).serialize(), function(res){
        if (res.success) Swal.fire('Assigned', res.message, 'success').then(()=>location.reload());
        else Swal.fire('Error','Unable to assign.','error');
    });
});

// ===== RESOLVE FORM =====
$('#frm_resolve_grievance').on('submit', function(e){
    e.preventDefault();
    const id = $('#reply_grievance_id').val();
    $.post("/admin/grievance/" + id + "/resolve", $(this).serialize(), function(res){
        if (res.success) Swal.fire('Resolved', res.message, 'success').then(()=>location.reload());
        else Swal.fire('Error','Unable to resolve.','error');
    });
});

// ===== ESCALATE FORM =====
$('#frm_escalate_grievance').on('submit', function(e){
    e.preventDefault();
    const id = $('#reply_grievance_id').val();
    $.post("/admin/grievance/" + id + "/escalate", $(this).serialize(), function(res){
        if (res.success) Swal.fire('Escalated', res.message, 'success').then(()=>location.reload());
        else Swal.fire('Error','Unable to escalate.','error');
    });
});

// ===== REPLY FORM =====
$('#frm_reply_grievance').on('submit', function(e){
    e.preventDefault();
    $.post("{{ route('admin.grievance.reply') }}", $(this).serialize(), function(res){
        if (res.success) {
            const sender = $('input[name=sender]:checked').val();
            let cls = 'chat-bubble-buyer', sn = 'Buyer';
            if (sender === 'admin')  { cls = 'chat-bubble-admin';  sn = 'Moderator (Admin)'; }
            if (sender === 'seller') { cls = 'chat-bubble-seller'; sn = 'Seller'; }
            const now = new Date();
            const bubble = `<div class="chat-bubble ${cls}">
                <div class="chat-sender-meta">${sn}</div>
                <div>${$('#reply_message_text').val()}</div>
                <div class="chat-time-meta">${now.toLocaleDateString([],{day:'2-digit',month:'short'})} ${now.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'})}</div>
            </div>`;
            $('#timeline_message_stream').append(bubble);
            const c = document.getElementById('timeline_message_stream');
            if(c) c.scrollTop = c.scrollHeight;
            $('#reply_message_text').val('');
            Swal.fire({icon:'success',title:'Sent!',timer:1200,showConfirmButton:false});
        } else {
            Swal.fire('Error','Unable to send reply.','error');
        }
    }).fail(xhr => {
        Swal.fire('Error', xhr.responseJSON?.message || 'Something went wrong.', 'error');
    });
});

// ===== ADD ESCALATION RULE =====
$('#saveRuleBtn').on('click', function() {
    const formData = {
        rule_name: $('#rule_name').val(),
        condition: $('#condition').val(),
        action: $('#action').val(),
        assign_to: $('#assign_to').val(),
        priority: $('#priority').val(),
        _token: '{{ csrf_token() }}'
    };
    // Validate required fields
    if (!formData.rule_name || !formData.condition || !formData.action || !formData.assign_to) {
        Swal.fire('Error', 'All fields are required', 'error');
        return;
    }
    $.ajax({
        url: '{{ route("admin.grievance.storeEscalationRule") }}',
        method: 'POST',
        data: formData,
        success: function(res) {
            if (res.success) {
                Swal.fire('Success', res.message, 'success').then(() => location.reload());
            } else {
                Swal.fire('Error', 'Could not save rule', 'error');
            }
        },
        error: function(xhr) {
            let msg = xhr.responseJSON?.message || 'Something went wrong';
            Swal.fire('Error', msg, 'error');
        }
    });
});

// ===== AUTO-OPEN MODAL FROM URL =====
$(document).ready(function(){
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('open_complaint') === '1') {
        if (typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(document.getElementById('createComplaintModal')).show();
        } else {
            $('#createComplaintModal').modal('show');
        }
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    if (urlParams.get('status') || urlParams.get('type') || urlParams.get('priority')) {
        const ticketsTab = document.querySelector('[href="#tab_tickets"]');
        if (ticketsTab) bootstrap.Tab.getOrCreateInstance(ticketsTab).show();
    }
});
</script>

@endsection
