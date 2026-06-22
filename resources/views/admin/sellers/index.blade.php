@extends('layouts.vertical', ['title' => 'Seller Management'])

@section('content')

<div class="container-fluid pt-3">

    <!-- Page Title -->
    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Seller Management</h4>

            <div>
                <a href="{{ route('admin.sellers.index') }}" class="btn btn-light btn-warning">All</a>
                <a href="{{ route('admin.sellers.index', ['status'=>'pending']) }}" class="btn btn-warning">Pending</a>
                <a href="{{ route('admin.sellers.index', ['status'=>'approved']) }}" class="btn btn-success">Approved</a>
                <a href="{{ route('admin.sellers.index', ['status'=>'rejected']) }}" class="btn btn-danger">Rejected</a>
                <a href="{{ route('admin.sellers.index', ['status'=>'active']) }}" class="btn btn-info">Active</a>
                <a href="{{ route('admin.sellers.index', ['status'=>'inactive']) }}" class="btn btn-info">Inactive</a>
            </div>
        </div>
    </div>

    <!-- Card -->
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle table-nowrap mb-0">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Seller</th>
                            <th>Contact</th>
                            <th>KYC Status</th>
                            <th>Account Status</th>
                            <th>Joined</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($sellers as $key => $seller)
                        <tr>
                            <td>{{ $sellers->firstItem() + $key }}</td>

                            <!-- Seller Info -->
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-light rounded me-2 d-flex align-items-center justify-content-center">
                                        <i class="mdi mdi-store fs-20 text-primary"></i>
                                    </div>
                                    <div>
                                       <h6 class="mb-0">
                                            <a href="{{ route('admin.sellers.show',$seller->id) }}" 
                                               class="text-dark text-decoration-none">
                                                {{ $seller->name }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">{{ $seller->legal_name ?? 'N/A' }}</small>
                                    </div>
                                </div>
                            </td>

                            <!-- Contact -->
                            <td>
                                <div>{{ $seller->email }}</div>
                                <small class="text-muted">{{ $seller->mobile }}</small>
                            </td>

                            <!-- KYC Status with Reason on next line -->
                            <td>
                                @if($seller->status == 'approved')
                                    <span class="badge bg-success-subtle text-success px-2 py-1">Approved</span>
                                @elseif($seller->status == 'pending')
                                    <span class="badge bg-warning-subtle text-warning px-2 py-1">Pending</span>
                                @elseif($seller->status == 'rejected')
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1">Rejected</span>
                                @elseif($seller->status == 'blocked')
                                    <span class="badge bg-dark-subtle text-dark px-2 py-1">Blocked</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ ucfirst($seller->status) }}</span>
                                @endif
                                
                            
                                
                                <!-- Display block reason if seller is blocked -->
                                @if($seller->status == 'blocked' && isset($seller->block_metadata))
                                    @php
                                        $blockMeta = is_string($seller->block_metadata) ? json_decode($seller->block_metadata, true) : $seller->block_metadata;
                                    @endphp
                                    @if(isset($blockMeta['reason']))
                                        <div class="text-muted small mt-1" style="font-size: 11px; max-width: 150px;">
                                            <i class="mdi mdi-lock"></i> {{ $blockMeta['reason'] }}
                                        </div>
                                    @endif
                                @endif
                             </td>

                            <!-- Account Active/Inactive Status -->
                            <td>
                                @php
                                    $isActive = $seller->is_active ?? 1;
                                @endphp
                                @if($isActive == 1)
                                    <span class="badge bg-success-subtle text-success px-2 py-1">
                                        <i class="mdi mdi-check-circle"></i> Active
                                    </span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger px-2 py-1">
                                        <i class="mdi mdi-close-circle"></i> Inactive
                                    </span>
                                @endif
                                
                                    <!-- Display deactivation reason if exists -->
                                @if(isset($seller->deactivated_reason) && $seller->deactivated_reason)
                                    <div class="text-muted small mt-1" style="font-size: 11px; max-width: 150px;">
                                        <i class="mdi mdi-information-outline"></i> {{ $seller->deactivated_reason }}
                                    </div>
                                @endif
                             </td>

                            <!-- Date -->
                            <td>
                                {{ $seller->created_at ? $seller->created_at->format('d M Y') : '-' }}
                             </td>

                            <!-- Action -->
                            <td class="text-end">
                                <div class="d-flex gap-1 justify-content-end">
                                    <a href="{{ route('admin.sellers.show',$seller->id) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="View Seller Details"
                                       data-bs-toggle="tooltip" 
                                       data-bs-placement="top">
                                        <i class="mdi mdi-eye"></i>
                                    </a>
                                    
                                    <!-- Active/Inactive Toggle Button -->
                                    @if(isset($seller->is_active) && ($seller->is_active == 0 || $seller->is_active == false))
                                        <button class="btn btn-sm btn-success activateSellerBtn" 
                                                data-id="{{ $seller->id }}" 
                                                data-name="{{ $seller->name }}"
                                                title="Activate Seller Account"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top">
                                            <i class="mdi mdi-play-circle"></i>
                                        </button>
                                    @else
                                        <button class="btn btn-sm btn-warning deactivateSellerBtn" 
                                                data-id="{{ $seller->id }}" 
                                                data-name="{{ $seller->name }}"
                                                title="Deactivate Seller Account"
                                                data-bs-toggle="tooltip" 
                                                data-bs-placement="top">
                                            <i class="mdi mdi-pause-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="mdi mdi-store-off fs-1 mb-2 d-block"></i>
                                No sellers found
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                 </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $sellers->links() }}
            </div>

        </div>
    </div>
</div>

<!-- Deactivate Modal -->
<div class="modal fade" id="deactivateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Deactivate Seller</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong class="text-danger">deactivate</strong> the seller <strong id="deactivateSellerName"></strong>?</p>
                <div class="mb-3">
                    <label class="form-label">Reason for deactivation (optional)</label>
                    <textarea id="deactivateReason" class="form-control" rows="2" placeholder="Enter reason..."></textarea>
                </div>
                <p class="text-muted small mb-0">Deactivated sellers will not be able to access their dashboard or list products.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirmDeactivateBtn">Deactivate</button>
            </div>
        </div>
    </div>
</div>

<!-- Activate Modal -->
<div class="modal fade" id="activateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Activate Seller</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to <strong class="text-success">activate</strong> the seller <strong id="activateSellerName"></strong>?</p>
                <p class="text-muted small mb-0">Activated sellers will be able to access their dashboard and list products.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmActivateBtn">Activate</button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('script')
<!-- jQuery CDN -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Bootstrap Tooltip JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<style>
    /* Tooltip styling for better visibility */
    [data-bs-toggle="tooltip"] {
        cursor: pointer;
    }
    
    /* Ensure long reason text wraps properly */
    .table td .text-muted {
        word-break: break-word;
        white-space: normal;
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
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
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
</style>

<script>
$(document).ready(function() {
    // Initialize all tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    let sellerId = null;
    let sellerName = null;
    
    // Deactivate Seller
    $(document).on('click', '.deactivateSellerBtn', function() {
        sellerId = $(this).data('id');
        sellerName = $(this).data('name');
        $('#deactivateSellerName').text(sellerName);
        $('#deactivateReason').val('');
        $('#deactivateModal').modal('show');
    });
    
    $('#confirmDeactivateBtn').click(function() {
        let reason = $('#deactivateReason').val();
        
        $.ajax({
            url: "{{ route('admin.sellers.deactivate') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: sellerId,
                reason: reason
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire('Success', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
                $('#deactivateModal').modal('hide');
            },
            error: function(xhr) {
                let errorMsg = 'Something went wrong';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
                $('#deactivateModal').modal('hide');
            }
        });
    });
    
    // Activate Seller
    $(document).on('click', '.activateSellerBtn', function() {
        sellerId = $(this).data('id');
        sellerName = $(this).data('name');
        $('#activateSellerName').text(sellerName);
        $('#activateModal').modal('show');
    });
    
    $('#confirmActivateBtn').click(function() {
        $.ajax({
            url: "{{ route('admin.sellers.activate') }}",
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: sellerId
            },
            beforeSend: function() {
                Swal.fire({
                    title: 'Processing...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if(response.success) {
                    Swal.fire('Success', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
                $('#activateModal').modal('hide');
            },
            error: function(xhr) {
                let errorMsg = 'Something went wrong';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error', errorMsg, 'error');
                $('#activateModal').modal('hide');
            }
        });
    });
});
</script>
@endsection