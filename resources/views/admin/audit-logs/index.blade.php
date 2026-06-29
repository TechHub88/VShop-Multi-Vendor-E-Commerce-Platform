@extends('layouts.vertical', ['title' => 'Audit Logs'])

@section('content')
<div class="row mt-3">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Audit Trail & Logs</h4>
            
            </div>
            <div class="card-body">
                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form method="GET" action="{{ route('admin.audit-logs.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Filter by Seller</label>
                                <select name="seller_id" class="form-select">
                                    <option value="">All Sellers</option>
                                    @foreach($sellers as $seller)
                                    <option value="{{ $seller->id }}" {{ request('seller_id') == $seller->id ? 'selected' : '' }}>
                                        {{ $seller->business_name ?? $seller->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Action Type</label>
                                <select name="action" class="form-select">
                                    <option value="">All Actions</option>
                                    <option value="seller_approved" {{ request('action') == 'seller_approved' ? 'selected' : '' }}>Seller Approved</option>
                                    <option value="seller_rejected" {{ request('action') == 'seller_rejected' ? 'selected' : '' }}>Seller Rejected</option>
                                    <option value="seller_blocked" {{ request('action') == 'seller_blocked' ? 'selected' : '' }}>Seller Blocked</option>
                                    <option value="product_blocked" {{ request('action') == 'product_blocked' ? 'selected' : '' }}>Product Blocked</option>
                                    <option value="customer_blocked" {{ request('action') == 'customer_blocked' ? 'selected' : '' }}>Customer Blocked</option>
                                    <option value="kyc_updated" {{ request('action') == 'kyc_updated' ? 'selected' : '' }}>KYC Updated</option>
                                    <option value="bank_approved" {{ request('action') == 'bank_approved' ? 'selected' : '' }}>Bank Approved</option>
                                    <option value="reverification_triggered" {{ request('action') == 'reverification_triggered' ? 'selected' : '' }}>Re-verification Triggered</option>
                                    <option value="deactivated" {{ request('action') == 'deactivated' ? 'selected' : '' }}>Deactivated</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="ri-filter-line"></i> Apply Filters
                                </button>
                                <a href="{{ route('admin.audit-logs.index') }}" class="btn btn-secondary">
                                    <i class="ri-refresh-line"></i> Reset
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Logs Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50">#</th>
                                <th>Seller</th>
                                <th>Action</th>
                                <th>Description</th>
                                <th>Performed By</th>
                                <th>IP Address</th>
                                <th>Date & Time</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditLogs as $log)
                            <tr>
                                <td>{{ $log->id }}</td>
                                <td>
                                    @if($log->seller)
                                        <strong>{{ $log->seller->business_name ?? $log->seller->name }}</strong><br>
                                        <small class="text-muted">ID: {{ $log->seller_id }}</small>
                                    @else
                                        <span class="text-muted">System Action</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $actionBadge = match($log->action) {
                                            'seller_approved' => 'success',
                                            'seller_rejected' => 'danger',
                                            'seller_blocked' => 'dark',
                                            'product_blocked' => 'warning',
                                            'customer_blocked' => 'info',
                                            'kyc_updated' => 'primary',
                                            'bank_approved' => 'success',
                                            'reverification_triggered' => 'secondary',
                                            'deactivated' => 'danger',
                                            'platforms_approved' => 'info',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $actionBadge }}">
                                        {{ str_replace('_', ' ', ucfirst($log->action)) }}
                                    </span>
                                </td>
                                <td>{{ Str::limit($log->description, 100) }}</td>
                                <td>{{ $log->user->name ?? 'System' }}</td>
                                <td><code>{{ $log->ip_address ?? 'N/A' }}</code></td>
                                <td>{{ $log->created_at->format('d M Y, h:i A') }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewLogModal{{ $log->id }}">
                                        <i class="ri-eye-line"></i> View Details
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No audit logs found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $auditLogs->appends(request()->query())->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modals -->
@foreach($auditLogs as $log)
<div class="modal fade" id="viewLogModal{{ $log->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Audit Log Details - #{{ $log->id }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <p><strong>Action:</strong> {{ ucfirst($log->action) }}</p>
                        <p><strong>Description:</strong></p>
                        <p class="alert alert-secondary">{{ $log->description }}</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>Performed By:</strong> {{ $log->user->name ?? 'System' }}</p>
                        <p><strong>IP Address:</strong> {{ $log->ip_address ?? 'N/A' }}</p>
                        <p><strong>User Agent:</strong> <small>{{ Str::limit($log->user_agent, 50) }}</small></p>
                    </div>
                </div>
                @if($log->old_data || $log->new_data)
                <hr>
                <h6>Data Changes:</h6>
                <div class="row">
                    @if($log->old_data)
                    <div class="col-md-6">
                        <p><strong>Old Data:</strong></p>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            @php
                                $oldDataArr = json_decode($log->old_data, true);
                            @endphp
                            @if(is_array($oldDataArr))
                                <table class="table table-sm table-bordered table-striped" style="font-size: 13px;">
                                    @foreach($oldDataArr as $key => $value)
                                        <tr>
                                            <th class="text-nowrap" style="width: 30%;">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                            <td style="word-break: break-all;">{{ is_array($value) ? json_encode($value) : (is_bool($value) ? ($value ? 'true' : 'false') : $value) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                <pre class="bg-light p-2">{{ $log->old_data }}</pre>
                            @endif
                        </div>
                    </div>
                    @endif
                    @if($log->new_data)
                    <div class="col-md-6">
                        <p><strong>New Data:</strong></p>
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            @php
                                $newDataArr = json_decode($log->new_data, true);
                            @endphp
                            @if(is_array($newDataArr))
                                <table class="table table-sm table-bordered table-striped" style="font-size: 13px;">
                                    @foreach($newDataArr as $key => $value)
                                        <tr>
                                            <th class="text-nowrap" style="width: 30%;">{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                                            <td style="word-break: break-all;">{{ is_array($value) ? json_encode($value) : (is_bool($value) ? ($value ? 'true' : 'false') : $value) }}</td>
                                        </tr>
                                    @endforeach
                                </table>
                            @else
                                <pre class="bg-light p-2">{{ $log->new_data }}</pre>
                            @endif
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
    }
    
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: white !important;
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
        color: white !important;
    }
    
    .btn-outline-info {
        color: #0dcaf0;
        border-color: #0dcaf0;
    }
    
    .btn-outline-info:hover {
        background: #0dcaf0;
        color: white;
    }
    
    /* Badge Colors */
    .badge.bg-success {
        background-color: #10b981 !important;
        color: white !important;
    }
    
    .badge.bg-danger {
        background-color: #ef4444 !important;
        color: white !important;
    }
    
    .badge.bg-dark {
        background-color: #1f2937 !important;
        color: white !important;
    }
    
    .badge.bg-warning {
        background-color: #f59e0b !important;
        color: white !important;
    }
    
    .badge.bg-info {
        background-color: #06b6d4 !important;
        color: white !important;
    }
    
    .badge.bg-primary {
        background-color: #3b82f6 !important;
        color: white !important;
    }
    
    /* Re-verification Triggered - Purple color */
    .badge.bg-secondary {
        background-color: #8b5cf6 !important;
        color: white !important;
    }
    
    /* Deactivated - Dark Red/Burgundy color */
    .badge.bg-danger[class*="deactivated"] {
        background-color: #991b1b !important;
    }
    
    /* For any badge with light color */
    .badge.bg-light {
        background-color: #6c757d !important;
        color: white !important;
    }
</style>
@endsection