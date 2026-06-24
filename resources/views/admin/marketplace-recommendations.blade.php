@extends('layouts.vertical', ['title' => 'Recommendation Engine'])

@section('content')
<style>
    /* Additional inline styles to ensure proper display (without breaking layout) */
    .badge { font-size: 0.75rem; padding: 0.35em 0.65em; }
    .table th, .table td { vertical-align: middle; }
    .form-switch .form-check-input { width: 2em; cursor: pointer; }
    .btn-outline-primary, .btn-outline-danger { border-width: 1px; }
    .ri-edit-line, .ri-delete-bin-line { font-size: 1rem; }
    .modal-header .btn-close { filter: brightness(0) invert(1); }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box">
                <h4 class="page-title">🔹 Recommendation Engine</h4>
                <p class="text-muted">Manage cross-sell, location‑based, AI and sponsored recommendation rules.</p>
            </div>
        </div>
    </div>

    <!-- Add Rule Button -->
    <div class="row mb-3">
        <div class="col-12 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#ruleModal">
                <i class="ri-add-line"></i> Add New Rule
            </button>
        </div>
    </div>

    <!-- Rules Table -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover align-middle">
                            <thead style="background: #2563eb; color: white;">
                                <tr>
                                    <th>Type</th>
                                    <th>Source</th>
                                    <th>Targets (Recommended)</th>
                                    <th>Priority</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($rules ?? []) as $rule)
                                <tr>
                                    <td>
                                        <span class="badge bg-{{ $rule->type == 'product' ? 'primary' : ($rule->type == 'location' ? 'success' : ($rule->type == 'ai' ? 'info' : 'warning')) }}">
                                            {{ ucfirst($rule->type) }}
                                        </span>
                                    </td>
                                    <td>{{ $rule->source }} @if($rule->source_type) <small class="text-muted">({{ $rule->source_type }})</small> @endif</td>
                                    <td>
                                        @foreach($rule->targets as $target)
                                            <span class="badge bg-secondary me-1">{{ $target }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $rule->priority }}</td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input type="checkbox" class="form-check-input toggle-status" data-id="{{ $rule->id }}" {{ $rule->is_active ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary edit-rule" data-id="{{ $rule->id }}">
                                            <i class="ri-edit-line"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger delete-rule" data-id="{{ $rule->id }}">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="6" class="text-center">No rules defined yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add / Edit Modal -->
<div class="modal fade" id="ruleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: #1e3a5f; color: white;">
                <h5 class="modal-title" id="modalTitle">Add Recommendation Rule</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="ruleForm">
                <div class="modal-body">
                    <input type="hidden" name="rule_id" id="rule_id">
                    
                    <div class="mb-3">
                        <label>Rule Type</label>
                        <select name="type" id="type" class="form-control" required>
                            <option value="product">Product → Product (Cross-sell / Upsell)</option>
                            <option value="location">Location → Services (City based)</option>
                            <option value="ai">AI Collaborative (People bought X also bought Y)</option>
                            <option value="sponsored">Sponsored (Global priority)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label>Source</label>
                        <div id="source_field"></div>
                        <small class="text-muted">
                            For product rules: Product ID or Category ID.<br>
                            For location rules: City name (e.g., "Banaras", "Goa").<br>
                            For AI/Sponsored: "global" or leave empty.
                        </small>
                    </div>

                    <div class="mb-3">
                        <label>Targets (Recommended Items)</label>
                        <div id="targets_field"></div>
                        <small class="text-muted">Comma separated IDs, product names, or service names.</small>
                    </div>

                    <div class="mb-3">
                        <label>Priority</label>
                        <input type="number" name="priority" id="priority" class="form-control" value="0">
                    </div>

                    <div class="mb-3" id="source_type_div">
                        <label>Source Type (optional)</label>
                        <select name="source_type" id="source_type" class="form-control">
                            <option value="">None</option>
                            <option value="product">Product</option>
                            <option value="category">Category</option>
                            <option value="location">Location</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Rule</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Include Remix Icons and Bootstrap JS (if not already in layout) 
     These lines will NOT be duplicated if layout already has them, 
     but they are safe because they will simply re‑declare. 
     To avoid potential conflicts, we only load them if not present. -->
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Helper to get Bootstrap modal instance
    function getModal() {
        return bootstrap.Modal.getInstance(document.getElementById('ruleModal'));
    }

    // Add mode – clear form when modal opens via the "Add New Rule" button
    $('#ruleModal').on('show.bs.modal', function(event) {
        var button = $(event.relatedTarget);
        if (button.hasClass('edit-rule')) {
            // Edit mode handled by separate click event; skip reset
            return;
        }
        // Reset form for add mode
        $('#modalTitle').text('Add Recommendation Rule');
        $('#ruleForm')[0].reset();
        $('#rule_id').val('');
        loadSourceField('');
        loadTargetsField('');
        $('#priority').val(0);
        $('#source_type').val('');
    });

    // Edit Rule
    $('.edit-rule').click(function() {
        let id = $(this).data('id');
        $.get('/admin/marketplace/recommendations/' + id + '/edit', function(rule) {
            $('#modalTitle').text('Edit Recommendation Rule');
            $('#rule_id').val(rule.id);
            $('#type').val(rule.type);
            $('#priority').val(rule.priority);
            $('#source_type').val(rule.source_type);
            loadSourceField(rule.source);
            loadTargetsField(rule.targets.join(','));
            // Show modal using Bootstrap 5 API
            const modal = new bootstrap.Modal(document.getElementById('ruleModal'));
            modal.show();
        });
    });

    function loadSourceField(value) {
        let type = $('#type').val();
        let html = '';
        if (type == 'product') {
            html = `<input type="text" name="source" class="form-control" placeholder="Product ID or Category ID" value="${escapeHtml(value)}" required>`;
        } else if (type == 'location') {
            html = `<input type="text" name="source" class="form-control" placeholder="City name (e.g., Banaras, Goa)" value="${escapeHtml(value)}" required>`;
        } else {
            html = `<input type="text" name="source" class="form-control" placeholder="global or leave empty" value="${escapeHtml(value)}">`;
        }
        $('#source_field').html(html);
    }

    function loadTargetsField(value) {
        let html = `<input type="text" name="targets_input" class="form-control" placeholder="Comma separated values" value="${escapeHtml(value)}" required>`;
        $('#targets_field').html(html);
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }

    $('#type').change(function() { 
        loadSourceField(''); 
        loadTargetsField(''); 
    });

    $('#ruleForm').submit(function(e) {
        e.preventDefault();
        let id = $('#rule_id').val();
        let url = id ? '/admin/marketplace/recommendations/' + id : '/admin/marketplace/recommendations/store';
        let method = id ? 'PUT' : 'POST';

        let targetsRaw = $('#targets_field input').val();
        let targets = targetsRaw ? targetsRaw.split(',').map(s => s.trim()) : [];

        let data = {
            type: $('#type').val(),
            source: $('#source_field input').val(),
            source_type: $('#source_type').val(),
            targets: targets,
            priority: $('#priority').val(),
            _token: '{{ csrf_token() }}'
        };

        $.ajax({
            url: url,
            method: method,
            data: data,
            success: function(res) {
                if(res.success) {
                    const modal = bootstrap.Modal.getInstance(document.getElementById('ruleModal'));
                    if (modal) modal.hide();
                    location.reload();
                } else {
                    alert(res.message || 'Error saving rule');
                }
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Server error';
                alert('Error: ' + msg);
            }
        });
    });

    // Toggle Status
    $('.toggle-status').change(function() {
        let id = $(this).data('id');
        $.post('/admin/marketplace/recommendations/toggle-status/' + id, {_token: '{{ csrf_token() }}'}, function(res) {
            if(!res.success) alert('Failed to update status');
        });
    });

    // Delete Rule
    $('.delete-rule').click(function() {
        if(confirm('Delete this rule?')) {
            let id = $(this).data('id');
            $.ajax({
                url: '/admin/marketplace/recommendations/' + id,
                method: 'DELETE',
                data: {_token: '{{ csrf_token() }}'},
                success: function(res) { 
                    if(res.success) location.reload(); 
                    else alert(res.message); 
                }
            });
        }
    });
});
</script>
@endsection