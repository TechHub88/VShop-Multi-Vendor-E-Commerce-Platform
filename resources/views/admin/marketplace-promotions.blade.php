@extends('layouts.vertical', ['title' => 'Promotions & Campaigns'])

@section('content')
<style>
    .table th {
    background: #004392 !important;
    color: #fff !important;
    border-color: #fff;
}
</style>
<div class="container-fluid pt-3">
    <!-- Meta for AJAX CSRF -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <div>
            <h1 class="h3 mb-0 text-slate-800 fw-bold">Promotions & Campaigns</h1>
            <p class="text-muted mb-0">Moderation and setup of all promotion campaigns and marketplace discounts.</p>
        </div>
        <button class="btn btn-primary d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
            <iconify-icon icon="solar:add-circle-outline" width="18"></iconify-icon> Create Campaign
        </button>
    </div>

    <!-- Alert Banner -->
    <div id="actionAlert" class="alert alert-success alert-dismissible fade show d-none mb-4" role="alert">
        <span class="alert-message">Action processed successfully.</span>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>

    <!-- Active Campaigns List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-slate-800">Campaign List</h5>
            <span class="badge bg-success-light text-success px-2 py-1">{{ $activeCampaigns->count() }} Total Campaigns</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted">Campaign Name</th>
                            <th class="py-3 text-muted">Discount Value</th>
                            <th class="py-3 text-muted">Period</th>
                            <th class="py-3 text-muted text-center">Status</th>
                            <th class="px-4 py-3 text-end text-muted">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($activeCampaigns as $campaign)
                        <tr id="campaign-row-{{ $campaign->id }}">
                            <td class="px-4">
                                <strong>{{ $campaign->name }}</strong><br>
                                <small class="text-muted">{{ $campaign->description }}</small>
                            </td>
                            <td><span class="badge bg-success-light text-success fw-bold">{{ $campaign->discount }}% OFF</span></td>
                            <td>
                                <small class="text-slate-600">
                                    {{ \Carbon\Carbon::parse($campaign->start_date)->format('M d, Y') }} to {{ \Carbon\Carbon::parse($campaign->end_date)->format('M d, Y') }}
                                </small>
                            </td>
                            <td class="text-center" id="campaign-status-cell-{{ $campaign->id }}">
                                @php
                                    $cStatus = $campaign->status ?? 'inactive';
                                    $cClass = 'secondary';
                                    if ($cStatus === 'active') $cClass = 'success';
                                    elseif ($cStatus === 'ended') $cClass = 'danger';
                                    elseif ($cStatus === 'inactive') $cClass = 'danger';
                                @endphp
                                <span class="badge bg-{{ $cClass }}-light text-{{ $cClass }} px-2 py-1 rounded text-uppercase font-size-10">{{ $cStatus }}</span>
                            </td>
                            <td class="px-4 text-end">
                                @if($campaign->status == 'active')
                                    <button class="btn btn-sm btn-outline-danger btn-end-campaign" data-id="{{ $campaign->id }}">End Campaign</button>
                                @else
                                    <span class="text-muted small">No actions</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">No promotion campaigns created yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Create Campaign Modal -->
    <div class="modal fade" id="createCampaignModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white py-3 border-bottom">
                    <h5 class="modal-title fw-bold text-slate-800">Create New Campaign</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="createCampaignForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Campaign Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Summer Festival Special" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description</label>
                            <textarea name="description" class="form-control" rows="2" placeholder="e.g. Seasonal promotion for all products..."></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Discount %</label>
                                <input type="number" name="discount" class="form-control" min="1" max="100" placeholder="e.g. 15" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">Status</label>
                                <select name="status" class="form-select">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <label class="form-label fw-semibold">Start Date</label>
                                <input type="date" name="start_date" class="form-control" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label fw-semibold">End Date</label>
                                <input type="date" name="end_date" class="form-control" required>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-2 border-top">
                        <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="btnSubmitCampaign">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-light { background-color: rgba(16, 185, 129, 0.1); }
    .bg-danger-light { background-color: rgba(239, 68, 68, 0.1); }
    .text-success { color: #10b981; }
    .text-danger { color: #ef4444; }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Submit form
        $('#createCampaignForm').submit(function(e) {
            e.preventDefault();
            var btn = $('#btnSubmitCampaign');
            btn.prop('disabled', true).text('Creating...');
            
            $.ajax({
                url: '/admin/marketplace/create-campaign',
                type: 'POST',
                data: $(this).serialize(),
                success: function(response) {
                    btn.prop('disabled', false).text('Create');
                    if (response.success) {
                        $('#createCampaignModal').modal('hide');
                        $('#actionAlert').removeClass('d-none').addClass('d-block');
                        $('#actionAlert .alert-message').text('Promotion campaign created successfully.');
                        setTimeout(function() { location.reload(); }, 1000);
                    } else {
                        alert(response.message || 'Failed to create campaign.');
                    }
                },
                error: function() {
                    btn.prop('disabled', false).text('Create');
                    alert('Request failed.');
                }
            });
        });

        // End campaign
        $('.btn-end-campaign').click(function() {
            var btn = $(this);
            var id = btn.data('id');
            btn.prop('disabled', true);
            
            $.post('/admin/marketplace/end-campaign/' + id, function(response) {
                btn.prop('disabled', false);
                if (response.success) {
                    $('#actionAlert').removeClass('d-none').addClass('d-block');
                    $('#actionAlert .alert-message').text('Campaign ended successfully.');
                    $('#campaign-status-cell-' + id).html('<span class="badge bg-danger-light text-danger px-2 py-1 rounded text-uppercase font-size-10">ended</span>');
                    btn.fadeOut();
                } else {
                    alert('Failed to end campaign.');
                }
            });
        });
    });
</script>
@endsection
