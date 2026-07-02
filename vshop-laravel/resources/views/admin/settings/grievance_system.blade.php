@extends('layouts.vertical', ['title' => 'Grievance & Dispute System Settings'])

@section('content')
<div class="container-fluid">
    <!-- Breadcrumbs -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0" style="color: #004392; font-weight: 700;">Grievance & Dispute System Settings</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                        <li class="breadcrumb-item active">Grievance System</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #e6f7f0; color: #10b981;">
            <i class="bx bx-check-circle me-2 fs-5 align-middle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center" style="color: #004392;">
                        <i class="bx bx-info-circle me-2 fs-4"></i> Grievance & Dispute Overview
                    </h5>
                </div>
                <div class="card-body p-4">
                    <section class="mb-4">
                        <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: #004392; letter-spacing: 0.5px;">
                            <span class="badge bg-primary bg-opacity-10 text-primary me-2 py-2px px-8px">1</span> System Types
                        </h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-transfer-alt text-primary me-3 fs-5"></i>
                                <div>
                                    <strong class="d-block text-dark">Seller ↔ Buyer</strong>
                                    <span class="text-muted fs-12">Disputes between buyer and seller regarding products or shipments.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-building text-primary me-3 fs-5"></i>
                                <div>
                                    <strong class="d-block text-dark">Seller ↔ Platform</strong>
                                    <span class="text-muted fs-12">Disputes regarding commissions, policies, payouts or suspensions.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-rupee text-primary me-3 fs-5"></i>
                                <div>
                                    <strong class="d-block text-dark">Lead Charge Dispute</strong>
                                    <span class="text-muted fs-12">Refund requests for fake or invalid lead generation charges.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-error text-primary me-3 fs-5"></i>
                                <div>
                                    <strong class="d-block text-dark">Fake Enquiry Complaint</strong>
                                    <span class="text-muted fs-12">Complaints filed against spam or competitor-generated queries.</span>
                                </div>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-mouse-pointer text-primary me-3 fs-5"></i>
                                <div>
                                    <strong class="d-block text-dark">Click Fraud Complaint</strong>
                                    <span class="text-muted fs-12">Reporting suspicious click patterns costing advertisement credits.</span>
                                </div>
                            </li>
                        </ul>
                    </section>

                    <section>
                        <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: #004392; letter-spacing: 0.5px;">
                            <span class="badge bg-primary bg-opacity-10 text-primary me-2 py-2px px-8px">2</span> Workflow Actions
                        </h6>
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-plus-circle text-success me-3 fs-5"></i>
                                <span class="text-dark">Ticket generation &amp; categorization</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-user-plus text-success me-3 fs-5"></i>
                                <span class="text-dark">Manual or automatic assignment to moderation teams</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-time text-success me-3 fs-5"></i>
                                <span class="text-dark">Real-time status tracking and messaging timeline</span>
                            </li>
                            <li class="list-group-item d-flex align-items-center border-light px-0">
                                <i class="bx bx-check-shield text-success me-3 fs-5"></i>
                                <span class="text-dark">Resolution note logging and ticket closure</span>
                            </li>
                        </ul>
                    </section>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow: hidden;">
                <div class="card-header bg-white border-bottom border-light py-3">
                    <h5 class="card-title mb-0 fw-bold d-flex align-items-center" style="color: #004392;">
                        <i class="bx bx-cog me-2 fs-4"></i> Escalation Matrix Configuration
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.settings.save_grievance_settings') }}" method="POST">
                        @csrf

                        @php
                            $hours = \App\Models\Setting::getByKey('grievance_escalation_hours', 24);
                            $orderValue = \App\Models\Setting::getByKey('grievance_escalation_order_value', 5000);
                            $agent = \App\Models\Setting::getByKey('grievance_escalation_agent', 'Senior Admin');
                            $priority = \App\Models\Setting::getByKey('grievance_escalation_priority', 'high');
                            $staffMembers = \App\Models\User::whereIn('type', ['admin', 'product', 'travel'])->get();
                        @endphp

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: #004392; letter-spacing: 0.5px;">
                                <i class="bx bx-bolt-circle text-warning me-2 fs-5"></i> Auto-Escalation Rules
                            </h6>
                            
                            <div class="mb-3">
                                <label for="grievance_escalation_hours" class="form-label text-dark fw-medium fs-13">Auto Escalate if No Response in (Hours):</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="grievance_escalation_hours" name="grievance_escalation_hours" value="{{ $hours }}" required min="1" style="border-radius: 8px 0 0 8px;">
                                    <span class="input-group-text bg-light border-start-0" style="border-radius: 0 8px 8px 0;">hours</span>
                                </div>
                                <small class="text-muted d-block mt-1">If the ticket receives no updates or replies for this duration, it triggers auto-escalation.</small>
                            </div>

                            <div class="mb-3">
                                <label for="grievance_escalation_order_value" class="form-label text-dark fw-medium fs-13">Auto Escalate High-value Order Threshold:</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="grievance_escalation_order_value" name="grievance_escalation_order_value" value="{{ $orderValue }}" required min="0" style="border-radius: 8px 0 0 8px;">
                                    <span class="input-group-text bg-light border-start-0" style="border-radius: 0 8px 8px 0;">INR</span>
                                </div>
                                <small class="text-muted d-block mt-1">Grievances tied to orders exceeding this value are automatically escalated upon opening.</small>
                            </div>
                        </div>

                        <hr class="border-light my-4">

                        <div class="mb-4">
                            <h6 class="fw-bold mb-3 d-flex align-items-center" style="color: #004392; letter-spacing: 0.5px;">
                                <i class="bx bx-user-check text-success me-2 fs-5"></i> Priority Tagging &amp; Assignments
                            </h6>

                            <div class="mb-3">
                                <label for="grievance_escalation_priority" class="form-label text-dark fw-medium fs-13">Escalation Priority Tag:</label>
                                <select class="form-select" id="grievance_escalation_priority" name="grievance_escalation_priority" required style="border-radius: 8px; height: auto; padding: 0.45rem 0.9rem;">
                                    <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ $priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                    <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                <small class="text-muted d-block mt-1">The system priority tag attached to auto-escalated tickets.</small>
                            </div>

                            <div class="mb-3">
                                <label for="grievance_escalation_agent" class="form-label text-dark fw-medium fs-13">Assign Escalated Tickets to (Senior / Admin):</label>
                                <select class="form-select" id="grievance_escalation_agent" name="grievance_escalation_agent" required style="border-radius: 8px; height: auto; padding: 0.45rem 0.9rem;">
                                    <option value="Senior Admin" {{ $agent == 'Senior Admin' ? 'selected' : '' }}>Senior Admin (Default Pool)</option>
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->name }}" {{ $agent == $staff->name ? 'selected' : '' }}>{{ $staff->name }} ({{ ucfirst($staff->type) }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted d-block mt-1">The default senior moderator/admin who will be assigned to handle escalated tickets.</small>
                            </div>
                        </div>

                        <div class="text-end pt-3">
                            <button type="submit" class="btn px-4 py-2 text-white fw-medium border-0 shadow-sm" style="background-color: #004392; border-radius: 8px; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.9" onmouseout="this.style.opacity=1">
                                <i class="bx bx-save me-1 fs-16 align-middle"></i> Save Configurations
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
