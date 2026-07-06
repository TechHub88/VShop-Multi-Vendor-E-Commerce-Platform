@extends('layouts.vertical', ['title' => 'Seller Timeline - ' . ($seller->business_name ?? $seller->name)])

@section('content')
<div class="row">
    <div class="col-12">
        <!-- Seller Info Card -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title text-white mb-0">Seller Timeline: {{ $seller->business_name ?? $seller->name }}</h4>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <strong>Seller ID:</strong> {{ $seller->id }}
                    </div>
                    <div class="col-md-3">
                        <strong>Email:</strong> {{ $seller->email }}
                    </div>
                    <div class="col-md-3">
                        <strong>Status:</strong> 
                        <span class="badge bg-{{ $seller->status == 'approved' ? 'success' : ($seller->status == 'pending' ? 'warning' : 'danger') }}">
                            {{ ucfirst($seller->status) }}
                        </span>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-warning btn-sm" onclick="confirmReVerification({{ $seller->id }})">
                            <i class="ri-refresh-line"></i> Trigger Re-verification
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Sections -->
        <div class="row">
            <div class="col-md-6">
                <!-- Audit Logs Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Audit Logs & Activities</h5>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        <div class="timeline">
                            @forelse($auditLogs as $log)
                            <div class="timeline-item pb-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs">
                                            <span class="avatar-title rounded-circle bg-{{ $log->action == 'seller_approved' ? 'success' : ($log->action == 'seller_blocked' ? 'danger' : 'info') }} shadow">
                                                <i class="ri-history-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</h6>
                                        <p class="text-muted mb-1">{{ $log->description }}</p>
                                        <small class="text-muted">
                                            <i class="ri-time-line"></i> {{ $log->created_at->format('d M Y, h:i A') }}
                                            @if($log->user)
                                            • <i class="ri-user-line"></i> {{ $log->user->name }}
                                            @endif
                                            @if($log->ip_address)
                                            • <i class="ri-map-pin-line"></i> {{ $log->ip_address }}
                                            @endif
                                        </small>
                                        @if($log->old_data || $log->new_data)
                                        <button class="btn btn-link btn-sm p-0 mt-1" type="button" data-bs-toggle="collapse" data-bs-target="#logDetails{{ $log->id }}">
                                            View Changes
                                        </button>
                                        <div class="collapse mt-2" id="logDetails{{ $log->id }}">
                                            <div class="card card-body bg-light">
                                                @if($log->old_data)
                                                <strong>Before:</strong>
                                                <pre class="mb-2">{{ json_encode(json_decode($log->old_data), JSON_PRETTY_PRINT) }}</pre>
                                                @endif
                                                @if($log->new_data)
                                                <strong>After:</strong>
                                                <pre>{{ json_encode(json_decode($log->new_data), JSON_PRETTY_PRINT) }}</pre>
                                                @endif
                                            </div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @empty
                            <p class="text-center">No audit logs available</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <!-- Blacklist Records -->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title">Blacklist/Block History</h5>
                    </div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        @forelse($blacklistRecords as $record)
                        <div class="alert alert-{{ $record->status == 'active' ? 'danger' : 'secondary' }} mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>
                                    <i class="ri-block-line"></i> 
                                    {{ ucfirst($record->block_type) }} Blocked
                                </strong>
                                <span class="badge bg-{{ $record->status == 'active' ? 'danger' : 'success' }}">
                                    {{ strtoupper($record->status) }}
                                </span>
                            </div>
                            <hr>
                            <p><strong>Reason:</strong> {{ $record->reason }}</p>
                            <small>
                                <strong>Blocked By:</strong> {{ $record->blockedBy->name ?? 'System' }}<br>
                                <strong>Date:</strong> {{ $record->created_at->format('d M Y, h:i A') }}
                                @if($record->duration == 'temp' && $record->block_until)
                                <br><strong>Until:</strong> {{ \Carbon\Carbon::parse($record->block_until)->format('d M Y') }}
                                @endif
                                @if($record->unblocked_at)
                                <br><strong>Unblocked:</strong> {{ \Carbon\Carbon::parse($record->unblocked_at)->format('d M Y, h:i A') }}
                                @endif
                            </small>
                        </div>
                        @empty
                        <p class="text-center">No blacklist records found</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- KYC Verification Timeline Card -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="card-title">KYC & Document Verification Timeline</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Document Type</th>
                                <th>Status</th>
                                <th>Last Updated</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>PAN Card</td>
                                <td><span class="badge bg-{{ $seller->pan_status == 'Approved' ? 'success' : 'warning' }}">{{ $seller->pan_status }}</span></td>
                                <td>{{ $seller->updated_at->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td>GST Certificate</td>
                                <td><span class="badge bg-{{ $seller->gst_status == 'Approved' ? 'success' : 'warning' }}">{{ $seller->gst_status }}</span></td>
                                <td>{{ $seller->updated_at->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td>Bank Account</td>
                                <td><span class="badge bg-{{ $seller->bank_status == 'approved' ? 'success' : 'warning' }}">{{ ucfirst($seller->bank_status) }}</span></td>
                                <td>{{ $seller->updated_at->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td>Agreement</td>
                                <td><span class="badge bg-{{ $seller->agreement_status == 'Approved' ? 'success' : 'warning' }}">{{ $seller->agreement_status }}</span></td>
                                <td>{{ $seller->updated_at->format('d M Y') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Re-verification Form -->
<form id="reVerificationForm" action="{{ route('admin.trigger.reverification', $seller->id) }}" method="POST" style="display: none;">
    @csrf
</form>

@endsection

@section('script')
<script>
function confirmReVerification(sellerId) {
    if(confirm('⚠️ WARNING: This will reset all verification statuses for this seller.\n\nThe seller will need to re-submit all KYC documents.\n\nAre you sure you want to proceed?')) {
        document.getElementById('reVerificationForm').submit();
    }
}

// Timeline styling
document.addEventListener('DOMContentLoaded', function() {
    const timelineItems = document.querySelectorAll('.timeline-item');
    timelineItems.forEach((item, index) => {
        item.style.borderLeft = '2px solid #e9ecef';
        item.style.paddingLeft = '20px';
        item.style.marginLeft = '10px';
    });
});
</script>
@endsection
