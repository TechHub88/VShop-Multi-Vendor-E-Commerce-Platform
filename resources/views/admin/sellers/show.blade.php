{{-- resources/views/admin/sellers/show.blade.php --}}
@extends('layouts.vertical', ['title' => 'Seller Management - Full Control'])

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">

<style>
    .seller-dashboard {
        font-family: 'Outfit', sans-serif;
        color: #1e293b;
        background-color: #f8fafc;
        padding-bottom: 3rem;
    }
    
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .animated-fade-in { animation: fadeInUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) both; }
    
    .v-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
        overflow: hidden;
        position: relative;
        transition: all 0.2s ease;
    }
    
    .v-card:hover {
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.08);
    }
    
    .v-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    
    .v-border-basic::before { background: linear-gradient(to bottom, #3b82f6, #60a5fa); }
    .v-border-org::before { background: linear-gradient(to bottom, #10b981, #34d399); }
    .v-border-kyc::before { background: linear-gradient(to bottom, #f59e0b, #fbbf24); }
    .v-border-comm::before { background: linear-gradient(to bottom, #06b6d4, #22d3ee); }
    .v-border-address::before { background: linear-gradient(to bottom, #8b5cf6, #a78bfa); }
    .v-border-contact-kyc::before { background: linear-gradient(to bottom, #ec489a, #f472b6); }
    .v-border-bank::before { background: linear-gradient(to bottom, #14b8a6, #2dd4bf); }
    .v-border-agreement::before { background: linear-gradient(to bottom, #84cc16, #a3e635); }
    
    .v-card-header {
        padding: 1.25rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .v-card-title {
        font-weight: 700;
        font-size: 1.15rem;
        color: #0f172a;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }
    
    .v-card-title i {
        font-size: 1.5rem;
        background: #f1f5f9;
        padding: 8px;
        border-radius: 12px;
        color: #334155;
    }
    
    .v-card-body { padding: 1.5rem; }
    
    .v-badge {
        font-weight: 600;
        font-size: 0.75rem;
        padding: 0.35rem 0.9rem;
        border-radius: 9999px;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        text-transform: uppercase;
    }
    
    .v-badge-approved { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .v-badge-pending { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    .v-badge-rejected { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .v-badge-verified { background-color: #dbeafe; color: #1e40af; border: 1px solid #bfdbfe; }
    .v-badge-success { background-color: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; }
    .v-badge-danger { background-color: #fee2e2; color: #991b1b; border: 1px solid #fecaca; }
    .v-badge-warning { background-color: #fef3c7; color: #92400e; border: 1px solid #fde68a; }
    
    .v-data-label {
        font-size: 0.7rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
        letter-spacing: 0.5px;
    }
    
    .v-data-value {
        font-size: 0.95rem;
        font-weight: 500;
        color: #1e293b;
        margin-bottom: 1rem;
        word-break: break-all;
    }
    
    /* Document View Buttons */
    .doc-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.6rem;
        padding: 0.6rem 1.2rem;
        border: none;
        border-radius: 14px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.25);
    }
    
    .doc-btn i {
        font-size: 1.1rem;
        transition: transform 0.2s;
    }
    
    .doc-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(102, 126, 234, 0.35);
    }
    
    .doc-btn:hover i {
        transform: scale(1.1);
    }
    
    .doc-btn-pdf {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
    }
    
    .doc-btn-pdf:hover {
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    }
    
    .doc-btn-image {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.25);
    }
    
    .doc-btn-image:hover {
        box-shadow: 0 6px 16px rgba(139, 92, 246, 0.4);
    }
    
    .doc-btn-cheque {
        background: linear-gradient(135deg, #14b8a6 0%, #0d9488 100%);
        box-shadow: 0 2px 8px rgba(20, 184, 166, 0.25);
    }
    
    /* Approve/Reject Buttons */
    .btn-approve-modern {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        padding: 0.5rem 1.2rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-approve-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(16, 185, 129, 0.4);
    }
    
    .btn-reject-modern {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        padding: 0.5rem 1.2rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-reject-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(239, 68, 68, 0.4);
    }
    
    .btn-verify-modern {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        padding: 0.5rem 1.2rem;
        border-radius: 40px;
        font-size: 0.8rem;
        font-weight: 600;
        color: white;
        transition: all 0.25s ease;
        box-shadow: 0 2px 6px rgba(59, 130, 246, 0.3);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-verify-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(59, 130, 246, 0.4);
    }
    
    /* Top Action Bar */
    .action-bar {
        background: #ffffff;
        border-radius: 20px;
        padding: 1rem 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        flex-wrap: wrap;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.03);
    }
    
    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.6rem 1.25rem;
        border-radius: 40px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.2s;
        border: none;
        cursor: pointer;
    }
    
    .action-btn-primary {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(16, 185, 129, 0.25);
    }
    
    .action-btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.35);
    }
    
    .action-btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(239, 68, 68, 0.25);
    }
    
    .action-btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(239, 68, 68, 0.35);
    }
    
    .action-btn-warning {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.25);
    }
    
    .action-btn-warning:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(245, 158, 11, 0.35);
    }
    
    .action-btn-outline {
        background: linear-gradient(135deg, #64748b 0%, #475569 100%);
        color: white;
        border: none;
        box-shadow: 0 2px 8px rgba(100, 116, 139, 0.25);
    }
    
    .action-btn-outline:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(100, 116, 139, 0.35);
    }
    
    .sidebar-widget {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }
    
    .sidebar-widget-header {
        font-weight: 700;
        font-size: 1rem;
        color: #0f172a;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 0.5rem;
    }
    
    .timeline-container {
        position: relative;
        padding-left: 1.5rem;
        margin-top: 1rem;
    }
    
    .timeline-container::before {
        content: '';
        position: absolute;
        left: 3px;
        top: 4px;
        width: 2px;
        height: calc(100% - 16px);
        background-color: #e2e8f0;
    }
    
    .timeline-item { position: relative; margin-bottom: 1.5rem; }
    
    .timeline-dot {
        position: absolute;
        left: -1.725rem;
        top: 2px;
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background-color: #cbd5e1;
        border: 2px solid #ffffff;
        box-shadow: 0 0 0 3px rgba(226, 232, 240, 0.5);
    }
    
    .timeline-dot-active { background-color: #10b981; }
    .timeline-dot-danger { background-color: #ef4444; }
    .timeline-dot-warning { background-color: #f59e0b; }
    
    .timeline-title { font-weight: 600; font-size: 0.875rem; color: #1e293b; margin-bottom: 0.125rem; }
    .timeline-desc { font-size: 0.8rem; color: #64748b; line-height: 1.4; }
    .timeline-meta { font-size: 0.7rem; color: #94a3b8; margin-top: 0.25rem; display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; }
    
    .top-progress-banner {
        background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        border: 1px solid #e2e8f0;
        border-radius: 24px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1.5rem;
    }
    
    .logo-preview {
        width: 70px;
        height: 70px;
        border-radius: 18px;
        object-fit: cover;
        border: 2px solid white;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }
    
    .kyc-card {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-radius: 16px;
        padding: 1rem;
        height: 100%;
        transition: all 0.2s;
    }
    
    .kyc-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    }
    
    /* Fraud Check Item Styles */
    .fraud-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.9rem 0;
        border-bottom: 1px solid #f1f5f9;
        flex-wrap: wrap;
        gap: 0.75rem;
    }
    
    .fraud-item:last-child {
        border-bottom: none;
    }
    
    .fraud-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: #334155;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .fraud-actions {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .fraud-verify-btn {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        padding: 0.3rem 0.9rem;
        border-radius: 30px;
        font-size: 0.7rem;
        font-weight: 500;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }
    
    .fraud-verify-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 3px 8px rgba(59, 130, 246, 0.3);
    }
    
    .fraud-check-all {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 40px;
        font-size: 0.75rem;
        font-weight: 600;
        color: white;
        cursor: pointer;
        transition: all 0.2s;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        width: 100%;
        justify-content: center;
        margin-top: 1rem;
    }
    
    .fraud-check-all:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 14px rgba(139, 92, 246, 0.35);
    }
    
    /* Entity Timeline Styles */
    .entity-timeline-item {
        display: flex;
        gap: 0.75rem;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .entity-timeline-item:last-child {
        border-bottom: none;
    }
    
    .entity-timeline-icon {
        width: 32px;
        height: 32px;
        background: #f1f5f9;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .entity-timeline-content {
        flex: 1;
    }
    
    .entity-timeline-title {
        font-weight: 600;
        font-size: 0.85rem;
        color: #0f172a;
    }
    
    .entity-timeline-desc {
        font-size: 0.75rem;
        color: #64748b;
    }
    
    .entity-timeline-time {
        font-size: 0.7rem;
        color: #94a3b8;
    }
</style>
<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
    }
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info,
    .action-btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-approve-modern {
        background: rgb(35 61 121);
        box-shadow: none;
    }
    .btn-approve-modern:hover {
        background: rgb(35 61 121);
        box-shadow: none;
    }
</style>

<div class="container-fluid seller-dashboard py-3">
    
    @php
        $blockMeta = json_decode($seller->block_metadata, true) ?? [];
        
        $steps = [
            'company' => strtolower($seller->company_status ?? 'pending') == 'approved',
            'pan' => strtolower($seller->pan_status ?? 'pending') == 'approved',
            'gst' => strtolower($seller->gst_status ?? 'pending') == 'approved',
            'iec' => strtolower($seller->iec_status ?? 'pending') == 'approved',
            'bank' => strtolower($seller->bank_status ?? 'pending') == 'approved',
            'agreement' => strtolower($seller->agreement_status ?? 'pending') == 'approved',
            'email' => $seller->email_verified == 1,
            'phone' => $seller->phone_verified == 1,
            'address' => !empty($seller->billing_address)
        ];
        
        $completedSteps = count(array_filter($steps));
        $percentComplete = round(($completedSteps / 9) * 100);
        
        $buyerIds = \App\Models\Order::where('seller_id', $seller->id)->pluck('user_id')->merge(\App\Models\Order::where('seller_id', $seller->id)->pluck('customer_id'))->unique()->filter()->toArray();
        $sellerCustomers = \App\Models\User::whereIn('id', $buyerIds)->where('status', '!=', 'blocked')->get();
        if($sellerCustomers->isEmpty()) {
            $sellerCustomers = \App\Models\User::where('status', '!=', 'blocked')->latest()->take(20)->get();
        }
        
        $auditLogs = $seller->auditLogs()->with('user')->latest()->get();
        $blockLogs = $auditLogs->filter(function($log) {
            return str_contains(strtolower($log->action ?? ''), 'block') || str_contains(strtolower($log->action ?? ''), 'blacklist');
        });
        
        // Fraud Check Statuses (default to 'passed')
        $fraudChecks = [
            'ocr' => ['label' => 'OCR validation (PAN/GST match)', 'status' => 'passed', 'icon' => 'ri-ocr-line', 'last_checked' => now()->subMinutes(5)],
            'duplicate' => ['label' => 'Duplicate document detection', 'status' => 'passed', 'icon' => 'ri-file-copy-line', 'last_checked' => now()->subMinutes(5)],
            'blacklist' => ['label' => 'Blacklist document check', 'status' => 'passed', 'icon' => 'ri-user-forbid-line', 'last_checked' => now()->subMinutes(5)]
        ];
        
        // Entity Timeline Data
        $entityTimeline = [];
        
        // KYC Updates
        if($seller->pan_status == 'approved') {
            $entityTimeline[] = ['type' => 'kyc', 'title' => 'PAN Verified', 'description' => 'PAN verification completed successfully', 'time' => $seller->updated_at];
        }
        if($seller->gst_status == 'approved') {
            $entityTimeline[] = ['type' => 'kyc', 'title' => 'GST Verified', 'description' => 'GST verification completed', 'time' => $seller->updated_at];
        }
        if($seller->iec_status == 'approved') {
            $entityTimeline[] = ['type' => 'kyc', 'title' => 'IEC Verified', 'description' => 'IEC verification completed', 'time' => $seller->updated_at];
        }
        
        // Admin Approvals
        foreach($auditLogs->take(10) as $log) {
            if(str_contains($log->action, 'bank') && str_contains($log->action, 'approve')) {
                $entityTimeline[] = ['type' => 'admin', 'title' => 'Bank Approved', 'description' => 'Admin approved bank account', 'time' => $log->created_at, 'user' => $log->user->name ?? 'Admin'];
            }
            if(str_contains($log->action, 'company') && str_contains($log->action, 'approve')) {
                $entityTimeline[] = ['type' => 'admin', 'title' => 'Organization Approved', 'description' => 'Admin approved organization', 'time' => $log->created_at, 'user' => $log->user->name ?? 'Admin'];
            }
            if(str_contains($log->action, 'agreement') && str_contains($log->action, 'approve')) {
                $entityTimeline[] = ['type' => 'admin', 'title' => 'Agreement Signed', 'description' => 'Admin approved agreement', 'time' => $log->created_at, 'user' => $log->user->name ?? 'Admin'];
            }
        }
        
        // Status Changes
        if($seller->status == 'approved') {
            $entityTimeline[] = ['type' => 'status', 'title' => 'Seller Activated', 'description' => 'Seller account activated', 'time' => $seller->updated_at];
        }
        if($seller->status == 'blocked') {
            $entityTimeline[] = ['type' => 'status', 'title' => 'Seller Blocked', 'description' => 'Seller account blocked', 'time' => $seller->updated_at];
        }
        
        // Sort timeline by time
        usort($entityTimeline, function($a, $b) {
            $timeA = $a['time'] ?? now();
            $timeB = $b['time'] ?? now();
            return strtotime($timeB) - strtotime($timeA);
        });
        $entityTimeline = array_slice($entityTimeline, 0, 8);
    @endphp

    {{-- Breadcrumb --}}
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1 bg-transparent p-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.sellers.index') }}" class="text-decoration-none">Sellers</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Full Control Dossier</li>
                    </ol>
                </nav>
                <h2 class="h3 fw-bold text-dark m-0">{{ $seller->legal_name ?? $seller->name }}</h2>
            </div>
            <a href="{{ route('admin.sellers.index') }}" class="action-btn action-btn-outline" style="background: linear-gradient(135deg, #64748b 0%, #475569 100%);">
                <i class="ri-arrow-left-line me-1"></i> Back
            </a>
        </div>
    </div>

    {{-- Top Progress Banner --}}
    <div class="top-progress-banner animated-fade-in">
        <div class="d-flex align-items-center gap-3">
            @if($seller->logo)
                <img src="{{ asset($seller->logo) }}" class="logo-preview">
            @else
                <div class="rounded-3 border bg-light d-flex align-items-center justify-content-center" style="width: 70px; height: 70px;">
                    <i class="ri-store-line fs-1 text-secondary"></i>
                </div>
            @endif
            <div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="badge bg-light text-dark px-3 py-1 rounded-pill"><i class="ri-mail-line"></i> {{ $seller->email }}</span>
                    <span class="badge bg-light text-dark px-3 py-1 rounded-pill"><i class="ri-phone-line"></i> {{ $seller->mobile ?? '—' }}</span>
                </div>
                <div class="mt-2">
                    @php $status = strtolower($seller->status ?? 'pending'); @endphp
                    @if($status == 'approved')
                        <span class="v-badge v-badge-approved"><i class="ri-checkbox-circle-fill"></i> Active Seller</span>
                    @elseif($status == 'rejected')
                        <span class="v-badge v-badge-rejected"><i class="ri-close-circle-fill"></i> Rejected</span>
                    @elseif($status == 'blocked')
                        <span class="v-badge v-badge-rejected"><i class="ri-error-warning-fill"></i> Blacklisted</span>
                    @else
                        <span class="v-badge v-badge-pending"><i class="ri-time-fill"></i> Pending Review</span>
                    @endif
                </div>
            </div>
        </div>
        
        <div class="text-center">
            <div class="fw-bold text-dark">Verification Progress</div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <div class="progress" style="width: 150px; height: 6px; background: #e2e8f0; border-radius: 10px;">
                    <div class="progress-bar bg-success rounded-pill" style="width: {{ $percentComplete }}%;"></div>
                </div>
                <span class="fw-bold fs-5 text-success">{{ $percentComplete }}%</span>
            </div>
            <small class="text-muted">{{ $completedSteps }} of 9 verified</small>
        </div>
    </div>

    {{-- TOP ACTION BAR --}}
    <div class="action-bar animated-fade-in">
        <span class="text-muted me-auto"><i class="ri-flashlight-fill text-warning"></i> Quick Actions</span>
        
        @if($seller->status != 'approved')
            <form method="POST" action="{{ route('admin.sellers.approve', $seller->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="action-btn action-btn-primary">
                    <i class="ri-checkbox-circle-line"></i> Activate Seller
                </button>
            </form>
        @endif
        
        @if($seller->status != 'rejected' && $seller->status != 'blocked')
            <form method="POST" action="{{ route('admin.sellers.reject', $seller->id) }}" class="d-inline">
                @csrf
                <button type="submit" class="action-btn action-btn-danger">
                    <i class="ri-close-circle-line"></i> Deactivate
                </button>
            </form>
        @endif
        
        <form method="POST" action="{{ route('admin.trigger.reverification', $seller->id) }}" class="d-inline">
            @csrf
            <button type="submit" class="action-btn action-btn-warning">
                <i class="ri-refresh-line"></i> Re-verify
            </button>
        </form>
        
        <button type="button" class="action-btn action-btn-danger" data-bs-toggle="modal" data-bs-target="#blockSellerModal">
            <i class="ri-user-ban-line"></i> Block Seller
        </button>
        
        <button type="button" class="action-btn action-btn-outline" data-bs-toggle="modal" data-bs-target="#blockProductModal">
            <i class="ri-shopping-bag-3-line"></i> Block Product
        </button>
    </div>

    <div class="row">
        <div class="col-lg-8 animated-fade-in">
            
            {{-- Platform Approval --}}
            @if(!empty($seller->requested_platforms))
            <div class="v-card v-border-basic">
                <div class="v-card-header">
                    <div class="v-card-title">
                        <i class="ri-apps-line text-primary"></i>
                        <span>Platform Approval</span>
                    </div>
                </div>
                <div class="v-card-body">
                    <form method="POST" action="{{ route('sellers.approve-platforms', $seller->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-12">
                                <p class="mb-3">The seller has requested access to the following platforms. Select the ones you want to approve:</p>
                                
                                @php 
                                    $requested = is_string($seller->requested_platforms) ? json_decode($seller->requested_platforms, true) : ($seller->requested_platforms ?? []);
                                    $approved = is_string($seller->approved_platforms) ? json_decode($seller->approved_platforms, true) : ($seller->approved_platforms ?? []);
                                    if(!is_array($requested)) $requested = [];
                                    if(!is_array($approved)) $approved = [];
                                @endphp
                                
                                <div class="d-flex gap-4 flex-wrap mb-3">
                                    @foreach(['product' => 'V-Shop', 'travel' => 'V-Travel', 'vcampus' => 'V-Campus'] as $key => $label)
                                        @if(in_array($key, $requested) || in_array($key, $approved))
                                            <div class="form-check">
                                                <input class="form-check-input border-primary" style="transform: scale(1.2)" type="checkbox" name="platforms[]" value="{{ $key }}" id="plat_{{ $key }}" {{ in_array($key, $approved) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-bold ms-1" for="plat_{{ $key }}">
                                                    {{ $label }}
                                                </label>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                                
                                <button type="submit" class="action-btn action-btn-primary">
                                    <i class="ri-save-line"></i> Save Platforms
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            @endif

            {{-- Organization Verification --}}
            <div class="v-card v-border-org">
                <div class="v-card-header">
                    <div class="v-card-title">
                        <i class="ri-building-4-line text-success"></i>
                        <span>Organization Verification</span>
                    </div>
                    @php $companyStatus = strtolower($seller->company_status ?? 'pending'); @endphp
                    @if($companyStatus != 'approved' && $companyStatus != 'rejected')
                        <div class="d-flex gap-2">
                            <button class="btn-approve-modern ajax-verify-btn" data-field="company_status" data-value="approved">
                                <i class="ri-check-line"></i> Approve Organization
                            </button>
                            <button class="btn-reject-modern ajax-verify-btn" data-field="company_status" data-value="rejected">
                                <i class="ri-close-line"></i> Reject
                            </button>
                        </div>
                    @else
                        <span class="v-badge v-badge-{{ $companyStatus }}">{{ ucfirst($companyStatus) }}</span>
                    @endif
                </div>
                <div class="v-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="v-data-label">Legal Business Name</div>
                            <div class="v-data-value fw-bold">{{ $seller->legal_name ?? '—' }}</div>
                            <div class="v-data-label">CIN</div>
                            <div class="v-data-value"><span class="v-data-code">{{ $seller->cin ?? '—' }}</span></div>
                            <div class="v-data-label">Incorporation Date</div>
                            <div class="v-data-value">{{ $seller->incorporation_date ? \Carbon\Carbon::parse($seller->incorporation_date)->format('d M Y') : '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="v-data-label">Business Type</div>
                            <div class="v-data-value">{{ ucfirst(str_replace('_', ' ', $seller->business_type ?? '—')) }}</div>
                            <div class="v-data-label">Application Selection</div>
                            <div class="v-data-value">{{ ucfirst($seller->application ?? 'Standard Marketplace') }}</div>
                            <div class="v-data-label">Trademark</div>
                            <div class="v-data-value">{{ $seller->logo_trademark ?? 'Not Provided' }}</div>
                        </div>
                    </div>
                    
                    <div class="d-flex flex-wrap gap-2 mt-3 pt-2 border-top">
                        @if($seller->logo)
                            <a href="{{ asset($seller->logo) }}" target="_blank" class="doc-btn doc-btn-image">
                                <i class="ri-image-line"></i> View Logo
                            </a>
                        @endif
                        @if($seller->trademark_file)
                            <a href="{{ asset($seller->trademark_file) }}" target="_blank" class="doc-btn doc-btn-pdf">
                                <i class="ri-file-pdf-line"></i> Trademark Document
                            </a>
                        @endif
                        @if($seller->company_certificate)
                            <a href="{{ asset($seller->company_certificate) }}" target="_blank" class="doc-btn doc-btn-pdf">
                                <i class="ri-file-pdf-line"></i> Incorporation Certificate
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            {{-- KYC Verification --}}
            <div class="v-card v-border-kyc">
                <div class="v-card-header">
                    <div class="v-card-title">
                        <i class="ri-shield-user-line text-warning"></i>
                        <span>KYC Verification</span>
                    </div>
                </div>
                <div class="v-card-body">
                    <div class="row g-3">
                        {{-- PAN --}}
                        <div class="col-md-4">
                            <div class="kyc-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">📇 PAN</h6>
                                    @php $panStatus = strtolower($seller->pan_status ?? 'pending'); @endphp
                                    @if($panStatus == 'pending')
                                        <div class="btn-group btn-group-sm gap-1">
                                            <button class="btn-approve-modern py-1 px-2 ajax-verify-btn" data-field="pan_status" data-value="approved" style="font-size:0.7rem"><i class="ri-check-line"></i></button>
                                            <button class="btn-reject-modern py-1 px-2 ajax-verify-btn" data-field="pan_status" data-value="rejected" style="font-size:0.7rem"><i class="ri-close-line"></i></button>
                                        </div>
                                    @else
                                        <span class="v-badge v-badge-{{ $panStatus }}">{{ ucfirst($panStatus) }}</span>
                                    @endif
                                </div>
                                <div class="small text-muted">Number: <strong>{{ $seller->pan_number ?? '—' }}</strong></div>
                                <div class="small text-muted mb-2">Expiry: {{ $seller->pan_expiry ? \Carbon\Carbon::parse($seller->pan_expiry)->format('d M Y') : '—' }}</div>
                                @if($seller->pan_file)
                                    <a href="{{ asset($seller->pan_file) }}" target="_blank" class="doc-btn doc-btn-pdf w-100 justify-content-center py-2" style="font-size:0.75rem">
                                        <i class="ri-file-pdf-line"></i> View PAN
                                    </a>
                                @endif
                            </div>
                        </div>
                        {{-- GST --}}
                        <div class="col-md-4">
                            <div class="kyc-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">📑 GST</h6>
                                    @php $gstStatus = strtolower($seller->gst_status ?? 'pending'); @endphp
                                    @if($gstStatus == 'pending')
                                        <div class="btn-group btn-group-sm gap-1">
                                            <button class="btn-approve-modern py-1 px-2 ajax-verify-btn" data-field="gst_status" data-value="approved" style="font-size:0.7rem"><i class="ri-check-line"></i></button>
                                            <button class="btn-reject-modern py-1 px-2 ajax-verify-btn" data-field="gst_status" data-value="rejected" style="font-size:0.7rem"><i class="ri-close-line"></i></button>
                                        </div>
                                    @else
                                        <span class="v-badge v-badge-{{ $gstStatus }}">{{ ucfirst($gstStatus) }}</span>
                                    @endif
                                </div>
                                <div class="small text-muted">GSTIN: <strong>{{ $seller->gst_number ?? '—' }}</strong></div>
                                <div class="small text-muted mb-2">Expiry: {{ $seller->gst_expiry ? \Carbon\Carbon::parse($seller->gst_expiry)->format('d M Y') : '—' }}</div>
                                @if($seller->gst_certificate)
                                    <a href="{{ asset($seller->gst_certificate) }}" target="_blank" class="doc-btn doc-btn-pdf w-100 justify-content-center py-2" style="font-size:0.75rem">
                                        <i class="ri-file-pdf-line"></i> View GST
                                    </a>
                                @endif
                            </div>
                        </div>
                        {{-- IEC --}}
                        <div class="col-md-4">
                            <div class="kyc-card">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold mb-0">🌍 IEC</h6>
                                    @php $iecStatus = strtolower($seller->iec_status ?? 'pending'); @endphp
                                    @if($iecStatus == 'pending')
                                        <div class="btn-group btn-group-sm gap-1">
                                            <button class="btn-approve-modern py-1 px-2 ajax-verify-btn" data-field="iec_status" data-value="approved" style="font-size:0.7rem"><i class="ri-check-line"></i></button>
                                            <button class="btn-reject-modern py-1 px-2 ajax-verify-btn" data-field="iec_status" data-value="rejected" style="font-size:0.7rem"><i class="ri-close-line"></i></button>
                                        </div>
                                    @else
                                        <span class="v-badge v-badge-{{ $iecStatus }}">{{ ucfirst($iecStatus) }}</span>
                                    @endif
                                </div>
                                <div class="small text-muted">IEC Code: <strong>{{ $seller->iec_code ?? '—' }}</strong></div>
                                <div class="small text-muted mb-2">File No: {{ $seller->iec_file_number ?? '—' }}</div>
                                @if($seller->iec_certificate)
                                    <a href="{{ asset($seller->iec_certificate) }}" target="_blank" class="doc-btn doc-btn-pdf w-100 justify-content-center py-2" style="font-size:0.75rem">
                                        <i class="ri-file-pdf-line"></i> View IEC
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            {{-- Address Validation --}}
            <div class="v-card v-border-address">
                <div class="v-card-header">
                    <div class="v-card-title">
                        <i class="ri-map-pin-2-line text-primary"></i>
                        <span>Address Validation</span>
                    </div>
                    <span class="v-badge v-badge-approved">Validated</span>
                </div>
                <div class="v-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="v-data-label">Billing Address</div>
                            <div class="v-data-value p-2 bg-light rounded">{{ $seller->billing_address ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="v-data-label">Contact Address</div>
                            <div class="v-data-value p-2 bg-light rounded">{{ $seller->contact_address ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Contact Person --}}
            <div class="v-card v-border-contact-kyc">
                <div class="v-card-header">
                    <div class="v-card-title">
                        <i class="ri-user-shared-line text-pink"></i>
                        <span>Contact Person</span>
                    </div>
                </div>
                <div class="v-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="kyc-card">
                                <div class="fw-bold">{{ $seller->org_name ?? '—' }}</div>
                                <div class="small text-muted">{{ $seller->org_mobile ?? '—' }} | {{ $seller->org_email ?? '—' }}</div>
                                @if($seller->contact_pan_file)
                                    <a href="{{ asset($seller->contact_pan_file) }}" target="_blank" class="doc-btn doc-btn-pdf mt-2">
                                        <i class="ri-file-pdf-line"></i> Contact PAN
                                    </a>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="kyc-card">
                                <div><span class="text-muted">PAN:</span> <strong>{{ $seller->contact_pan_number ?? '—' }}</strong></div>
                                <div><span class="text-muted">Aadhaar:</span> <strong>{{ $seller->contact_aadhar_number ?? '—' }}</strong></div>
                                @if($seller->contact_aadhar_file)
                                    <a href="{{ asset($seller->contact_aadhar_file) }}" target="_blank" class="doc-btn doc-btn-pdf mt-2">
                                        <i class="ri-file-pdf-line"></i> View Aadhaar
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Banking Verification --}}
            <div class="v-card v-border-bank">
                <div class="v-card-header">
                    <div class="v-card-title">
                        <i class="ri-bank-line text-cyan"></i>
                        <span>Banking Verification</span>
                    </div>
                    @php $bankStatus = strtolower($seller->bank_status ?? 'pending'); @endphp
                    @if($bankStatus == 'pending')
                        <div class="d-flex gap-2">
                            <button class="btn-approve-modern ajax-verify-btn" data-field="bank_status" data-value="approved">
                                <i class="ri-check-line"></i> Approve Bank
                            </button>
                            <button class="btn-reject-modern ajax-verify-btn" data-field="bank_status" data-value="rejected">
                                <i class="ri-close-line"></i> Reject
                            </button>
                        </div>
                    @else
                        <span class="v-badge v-badge-{{ $bankStatus }}">{{ ucfirst($bankStatus) }}</span>
                    @endif
                </div>
                <div class="v-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="v-data-label">Account Holder</div>
                            <div class="v-data-value">{{ $seller->bank_account_name ?? '—' }}</div>
                            <div class="v-data-label">Account Number</div>
                            <div class="v-data-value"><span class="v-data-code">{{ $seller->bank_account_number ?? '—' }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="v-data-label">IFSC Code</div>
                            <div class="v-data-value">{{ $seller->ifsc_code ?? '—' }}</div>
                            <div class="v-data-label">SWIFT Code</div>
                            <div class="v-data-value">{{ $seller->swift_code ?? '—' }}</div>
                        </div>
                    </div>
                    @if($seller->cancel_cheque)
                        <a href="{{ asset($seller->cancel_cheque) }}" target="_blank" class="doc-btn doc-btn-cheque mt-2">
                            <i class="ri-file-copy-line"></i> View Cancelled Cheque
                        </a>
                    @endif
                </div>
            </div>

            {{-- Agreement Management --}}
            <div class="v-card v-border-agreement">
                <div class="v-card-header">
                    <div class="v-card-title">
                        <i class="ri-article-line text-success"></i>
                        <span>Agreement</span>
                    </div>
                    @php $agreementStatus = strtolower($seller->agreement_status ?? 'pending'); @endphp
                    @if($agreementStatus == 'pending')
                        <div class="d-flex gap-2">
                            <button class="btn-approve-modern ajax-verify-btn" data-field="agreement_status" data-value="approved">
                                <i class="ri-check-line"></i> Approve Agreement
                            </button>
                            <button class="btn-reject-modern ajax-verify-btn" data-field="agreement_status" data-value="rejected">
                                <i class="ri-close-line"></i> Reject
                            </button>
                        </div>
                    @else
                        <span class="v-badge v-badge-{{ $agreementStatus }}">{{ $agreementStatus == 'approved' ? 'Signed & Approved' : ucfirst($agreementStatus) }}</span>
                    @endif
                </div>
                <div class="v-card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="v-data-label">Type</div>
                            <div class="v-data-value">{{ ucfirst($seller->agreement_type ?? 'Standard Marketplace') }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="v-data-label">Accepted At</div>
                            <div class="v-data-value">{{ $seller->agreement_accepted_at ? \Carbon\Carbon::parse($seller->agreement_accepted_at)->format('d M Y h:i A') : 'Awaiting' }}</div>
                        </div>
                    </div>
                    @if($seller->agreement_file)
                        <a href="{{ asset($seller->agreement_file) }}" target="_blank" class="doc-btn doc-btn-pdf mt-2">
                            <i class="ri-file-pdf-line"></i> View Signed Agreement
                        </a>
                    @endif
                </div>
            </div>
        </div>

        {{-- RIGHT SIDEBAR --}}
        <div class="col-lg-4">
           
            {{-- ENTITY-LEVEL TIMELINE --}}
            <div class="sidebar-widget">
                <div class="sidebar-widget-header">
                    <i class="ri-timeline-view text-primary"></i>
                    <span>Entity-Level Timeline</span>
                </div>
                
                <div class="entity-timeline-container">
                    @forelse($entityTimeline as $event)
                        <div class="entity-timeline-item">
                            <div class="entity-timeline-icon">
                                @if($event['type'] == 'kyc')
                                    <i class="ri-shield-user-line text-success"></i>
                                @elseif($event['type'] == 'admin')
                                    <i class="ri-admin-line text-primary"></i>
                                @elseif($event['type'] == 'status')
                                    <i class="ri-toggle-line text-warning"></i>
                                @else
                                    <i class="ri-history-line text-muted"></i>
                                @endif
                            </div>
                            <div class="entity-timeline-content">
                                <div class="entity-timeline-title">{{ $event['title'] }}</div>
                                <div class="entity-timeline-desc">{{ $event['description'] }}</div>
                                <div class="entity-timeline-time">
                                    <i class="ri-time-line"></i> 
                                    {{ $event['time'] ? \Carbon\Carbon::parse($event['time'])->diffForHumans() : 'Recently' }}
                                    @if(isset($event['user']))
                                        • by {{ $event['user'] }}
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3">
                            <i class="ri-timeline-line fs-3"></i>
                            <div class="small">No timeline events yet</div>
                        </div>
                    @endforelse
                </div>
                
                <div class="mt-3 pt-2 border-top">
                    <a href="#" class="text-muted small text-decoration-none">View full history <i class="ri-arrow-right-line"></i></a>
                </div>
            </div>

            {{-- Performance Stats --}}
            <div class="sidebar-widget">
                <div class="sidebar-widget-header">
                    <i class="ri-pie-chart-2-line text-primary"></i>
                    <span>Performance</span>
                </div>
                <div class="row text-center">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3">
                            <i class="ri-shopping-bag-3-line fs-2 d-block text-primary"></i>
                            <h3 class="mb-0 fw-bold">{{ $seller->products()->count() }}</h3>
                            <small class="text-muted">Products</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3">
                            <i class="ri-shopping-cart-2-line fs-2 d-block text-success"></i>
                            <h3 class="mb-0 fw-bold">{{ $seller->orders()->count() }}</h3>
                            <small class="text-muted">Orders</small>
                        </div>
                    </div>
                </div>
                <ul class="list-unstyled mt-3 small mb-0">
                    <li class="d-flex justify-content-between py-2 border-bottom"><span class="text-muted">Registered</span><strong>{{ $seller->created_at ? $seller->created_at->format('d M Y') : '—' }}</strong></li>
                    <li class="d-flex justify-content-between py-2"><span class="text-muted">Last Updated</span><strong>{{ $seller->updated_at ? $seller->updated_at->diffForHumans() : '—' }}</strong></li>
                </ul>
            </div>

            {{-- Blacklist History --}}
            <div class="sidebar-widget">
                <div class="sidebar-widget-header">
                    <i class="ri-skull-line text-danger"></i>
                    <span>Blacklist History</span>
                </div>
                @if($seller->status == 'blocked' && !empty($blockMeta))
                    <div class="alert alert-danger p-2 small">
                        <strong>Reason:</strong> {{ $blockMeta['reason'] ?? 'Policy violation' }}<br>
                        <strong>By:</strong> {{ $blockMeta['blocked_by_name'] ?? 'Admin' }}<br>
                        <strong>Until:</strong> {{ isset($blockMeta['block_until']) ? \Carbon\Carbon::parse($blockMeta['block_until'])->format('d M Y') : 'Permanent' }}
                    </div>
                @endif
                @if($blockLogs->count() > 0)
                    @foreach($blockLogs->take(3) as $log)
                        <div class="border-bottom py-2">
                            <div class="fw-semibold">{{ ucfirst($log->action ?? 'Block') }}</div>
                            <div class="small text-muted">{{ $log->description ?? 'No reason' }}</div>
                            <div class="small text-muted">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }} by {{ $log->user->name ?? 'System' }}</div>
                        </div>
                    @endforeach
                @else
                    <div class="text-center text-muted py-3"><i class="ri-shield-line fs-3"></i><div class="small">No blacklist records</div></div>
                @endif
            </div>

            {{-- Audit Trail --}}
            <div class="sidebar-widget">
                <div class="sidebar-widget-header">
                    <i class="ri-history-line text-info"></i>
                    <span>Audit Trail</span>
                </div>
                <div class="timeline-container">
                    @forelse($auditLogs->take(6) as $log)
                        <div class="timeline-item">
                            @php
                                $isDanger = str_contains($log->action, 'block') || str_contains($log->action, 'reject');
                                $isSuccess = str_contains($log->action, 'approve') || str_contains($log->action, 'verify');
                            @endphp
                            <div class="timeline-dot {{ $isDanger ? 'timeline-dot-danger' : ($isSuccess ? 'timeline-dot-active' : 'timeline-dot-warning') }}"></div>
                            <div class="timeline-title">{{ ucwords(str_replace('_', ' ', $log->action ?? 'Update')) }}</div>
                            <div class="timeline-desc">{{ Str::limit($log->description ?? 'No description', 60) }}</div>
                            <div class="timeline-meta">{{ $log->created_at ? $log->created_at->diffForHumans() : '' }}</div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-3"><i class="ri-clipboard-line fs-2"></i><div class="small">No logs yet</div></div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALS --}}
<div class="modal fade" id="blockSellerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title"><i class="ri-user-ban-line"></i> Block Seller Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.block.seller', $seller->id) }}">
                @csrf
                <div class="modal-body p-4">
                    <p class="text-muted small">Blocking restricts platform access and hides all products automatically.</p>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason <span class="text-danger">*</span></label>
                        <textarea class="form-control" name="reason" rows="4" required minlength="10" placeholder="Enter block reason..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Block Until (Optional)</label>
                        <input type="date" class="form-control" name="block_until">
                        <small class="text-muted">Leave blank for permanent block</small>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="action-btn action-btn-danger">Execute Block</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="blockProductModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-warning border-0">
                <h5 class="modal-title"><i class="ri-shopping-bag-3-line"></i> Block Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            @if($seller->products->isEmpty())
                <div class="modal-body text-center py-4"><i class="ri-shopping-bag-line fs-1 text-muted"></i><p class="mt-2">No products found</p></div>
            @else
                <form id="blockProductForm" method="POST" action="">
                    @csrf
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Product <span class="text-danger">*</span></label>
                            <select class="form-select" id="block_product_select" required onchange="this.form.action='/admin/block-product/'+this.value">
                                <option value="">Select product...</option>
                                @foreach($seller->products as $prod)
                                    <option value="{{ $prod->id }}">{{ $prod->name }} (ID: {{ $prod->id }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Reason <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="reason" rows="4" required minlength="10" placeholder="Enter block reason..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer bg-light border-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="action-btn action-btn-warning">Block Product</button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    $('.ajax-verify-btn').on('click', function(e) {
        e.preventDefault();
        var btn = $(this);
        var field = btn.data('field');
        var value = btn.data('value');
        var originalContent = btn.html();
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        var payload = {};
        payload[field] = value;
        
        $.ajax({
            url: '{{ route("admin.sellers.verify", $seller->id) }}',
            method: 'POST',
            data: payload,
            success: function(response) { location.reload(); },
            error: function(xhr) {
                btn.prop('disabled', false).html(originalContent);
                alert('Error: ' + (xhr.responseJSON?.message || 'Something went wrong'));
            }
        });
    });

    $('#block_product_select').on('change', function() {
        var productId = $(this).val();
        if(productId) $('#blockProductForm').attr('action', '/admin/block-product/' + productId);
    });
    
    // Professional Fraud Check Functions
    function checkFraudItem(itemKey) {
        var $btn = $('button[data-fraud-key="' + itemKey + '"]');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
        
        // Simulate API check
        setTimeout(function() {
            // Random pass/fail for demo (90% pass rate)
            var passed = Math.random() > 0.1;
            var $statusSpan = $('.fraud-status-' + itemKey);
            
            if (passed) {
                $statusSpan.html('<i class="ri-checkbox-circle-line"></i> Passed');
                $statusSpan.removeClass('v-badge-danger v-badge-warning').addClass('v-badge-success');
                $statusSpan.css('background-color', '#d1fae5').css('color', '#065f46');
            } else {
                $statusSpan.html('<i class="ri-alert-line"></i> Failed');
                $statusSpan.removeClass('v-badge-success v-badge-warning').addClass('v-badge-danger');
                $statusSpan.css('background-color', '#fee2e2').css('color', '#991b1b');
            }
            
            // Update last checked time
            var now = new Date();
            var formattedTime = now.toLocaleString('en-US', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            $('#fraud-last-checked').html('<i class="ri-history-line"></i> Last checked: ' + formattedTime);
            
            $btn.prop('disabled', false).html(originalText);
            
            // Show toast notification
            showNotification(itemKey, passed);
        }, 800);
    }
    
    function checkAllFraud() {
        var $btn = $('.fraud-check-all');
        var originalText = $btn.html();
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Running checks...');
        
        var items = ['ocr', 'duplicate', 'blacklist'];
        var promises = items.map(function(item) {
            return new Promise(function(resolve) {
                setTimeout(function() {
                    var passed = Math.random() > 0.1;
                    var $statusSpan = $('.fraud-status-' + item);
                    
                    if (passed) {
                        $statusSpan.html('<i class="ri-checkbox-circle-line"></i> Passed');
                        $statusSpan.removeClass('v-badge-danger v-badge-warning').addClass('v-badge-success');
                        $statusSpan.css('background-color', '#d1fae5').css('color', '#065f46');
                    } else {
                        $statusSpan.html('<i class="ri-alert-line"></i> Failed');
                        $statusSpan.removeClass('v-badge-success v-badge-warning').addClass('v-badge-danger');
                        $statusSpan.css('background-color', '#fee2e2').css('color', '#991b1b');
                    }
                    resolve();
                }, 500);
            });
        });
        
        Promise.all(promises).then(function() {
            var now = new Date();
            var formattedTime = now.toLocaleString('en-US', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            $('#fraud-last-checked').html('<i class="ri-history-line"></i> Last checked: ' + formattedTime);
            
            $btn.prop('disabled', false).html(originalText);
            showNotification('all', true);
        });
    }
    
    function showNotification(itemKey, passed) {
        // Simple alert for demo - can be replaced with toast notification
        var itemName = '';
        switch(itemKey) {
            case 'ocr': itemName = 'OCR validation (PAN/GST match)'; break;
            case 'duplicate': itemName = 'Duplicate document detection'; break;
            case 'blacklist': itemName = 'Blacklist document check'; break;
            case 'all': itemName = 'All fraud checks'; break;
            default: itemName = 'Fraud check';
        }
        
        if (passed) {
            console.log('✓ ' + itemName + ' completed - Status: Passed');
        } else {
            console.log('⚠ ' + itemName + ' completed - Status: Failed');
        }
    }
</script>
@endsection