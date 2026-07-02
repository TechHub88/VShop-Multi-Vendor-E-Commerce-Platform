@extends('layouts.vertical', ['title' => 'Search & Discovery Control'])

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
    :root {
        --primary-color: #2563eb;
        --dark-color: #1f2937;
        --gray-color: #6b7280;
    }
    
    .page-header {
        background: white;
        border-radius: 24px;
        padding: 28px 32px;
        margin-bottom: 28px;
        border: 1px solid #e5e7eb;
    }
    
    .stat-card {
        background: white;
        border-radius: 20px;
        padding: 24px 20px;
        text-align: left;
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        cursor: pointer;
    }
    
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 24px -12px rgba(0,0,0,0.15);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 16px;
        font-size: 24px;
    }
    
    .stat-number {
        font-size: 32px;
        font-weight: 800;
        color: var(--dark-color);
        margin-bottom: 4px;
    }
    
    .stat-label {
        font-size: 14px;
        color: var(--gray-color);
        font-weight: 500;
    }
    
    .badge-boost {
        padding: 6px 14px;
        border-radius: 30px;
        font-weight: 600;
        font-size: 12px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    
    .badge-featured { background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe; }
    .badge-sponsored { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }
    .badge-pinned { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }
    .badge-boosted { background: #faf5ff; color: #7c3aed; border: 1px solid #ddd6fe; }
    
    .product-image {
        width: 52px;
        height: 52px;
        border-radius: 12px;
        object-fit: cover;
        background: #f3f4f6;
        border: 1px solid #e5e7eb;
    }
    
    .card-modern {
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        background: white;
        overflow: hidden;
    }
    
    .card-header-modern {
        background: white;
        padding: 20px 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    
    .btn-primary-custom {
        background: var(--primary-color);
        color: white;
        border: none;
        padding: 8px 20px;
        border-radius: 40px;
        font-weight: 500;
    }
    
    .btn-primary-custom:hover {
        background: #1d4ed8;
    }
    
    .product-checkbox {
        width: 20px;
        height: 20px;
        margin-right: 15px;
        cursor: pointer;
    }
    
    .selected-count-badge {
        background: var(--primary-color);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }
    
    .product-item {
        padding: 12px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        margin-bottom: 10px;
        background: white;
        transition: all 0.2s;
    }
    
    .product-item:hover {
        background: #fafafa;
        border-color: var(--primary-color);
    }
    
    .switch {
        position: relative;
        display: inline-block;
        width: 44px;
        height: 24px;
    }
    
    .switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: 0.3s;
        border-radius: 34px;
    }
    
    .slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }
    
    input:checked + .slider {
        background-color: var(--primary-color);
    }
    
    input:checked + .slider:before {
        transform: translateX(20px);
    }
    
    .dataTables_wrapper {
        padding: 20px;
    }
    
    .dataTables_filter input {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 6px 12px;
        margin-left: 8px;
    }
    
    .alert-custom {
        border-radius: 12px;
        padding: 12px 20px;
    }
</style>

<div class="container-fluid py-4">
    
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-bold mb-2">
                    <i class="bi bi-rocket-takeoff me-2" style="color: var(--primary-color);"></i>
                    Search & Discovery Control
                </h1>
                <p class="text-muted mb-0">Manage product visibility, boost rankings, and control search placements</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-primary-custom" data-bs-toggle="modal" data-bs-target="#addBoostModal">
                    <i class="bi bi-plus-lg me-2"></i>Add Boosted Product
                </button>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card" onclick="filterByType('featured')">
                <div class="stat-icon" style="background: #eff6ff;">
                    <i class="bi bi-star-fill" style="color: #2563eb;"></i>
                </div>
                <div class="stat-number">{{ $featuredProducts->count() }}</div>
                <div class="stat-label">Featured Products</div>
                <small class="text-muted">30% visibility boost</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" onclick="filterByType('sponsored')">
                <div class="stat-icon" style="background: #fef2f2;">
                    <i class="bi bi-megaphone-fill" style="color: #dc2626;"></i>
                </div>
                <div class="stat-number">{{ $sponsoredProducts->count() }}</div>
                <div class="stat-label">Sponsored Listings</div>
                <small class="text-muted">Prime ad slots</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" onclick="filterByType('pinned')">
                <div class="stat-icon" style="background: #f0fdf4;">
                    <i class="bi bi-pin-fill" style="color: #16a34a;"></i>
                </div>
                <div class="stat-number">{{ $pinnedProducts->count() }}</div>
                <div class="stat-label">Pinned Products</div>
                <small class="text-muted">Top of search results</small>
            </div>
        </div>
    </div>
    
    <div id="actionAlert" class="alert alert-custom d-none mb-4" role="alert"></div>
    
    <div class="card-modern">
        <div class="card-header-modern d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0">
                    <i class="bi bi-grid-3x3-gap-fill me-2" style="color: var(--primary-color);"></i>
                    Boosted Products Management
                </h5>
                <p class="text-muted small mb-0 mt-1">Manage all boosted products across categories</p>
            </div>
        </div>
        <div class="p-0">
            <div class="table-responsive">
                <table id="boostedProductsTable" class="table align-middle mb-0" style="width: 100%;">
                    <thead class="table-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">Image</th>
                            <th width="25%">Product Name</th>
                            <th width="15%">Seller</th>
                            <th width="10%">Price</th>
                            <th width="15%">Boost Type</th>
                            <th width="10%">Status</th>
                            <th width="10%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($boostedProducts as $index => $product)
                        <tr id="row-{{ $product->id }}">
                            <td>{{ $index + 1 }}</td>
                            <td>
                                @if($product->thumbnail)
                                    <img src="{{ asset($product->thumbnail) }}" class="product-image" alt="{{ $product->name }}">
                                @else
                                    <div class="product-image d-flex align-items-center justify-content-center bg-light">
                                        <i class="bi bi-image text-muted"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <small class="text-muted">SKU: {{ $product->sku ?? 'N/A' }}</small>
                            </td>
                            <td>
                                <div>{{ $product->seller->name ?? 'Admin' }}</div>
                                <small class="text-muted">ID: {{ $product->seller->id ?? 'N/A' }}</small>
                            </td>
                            <td class="fw-bold">${{ number_format($product->price, 2) }}</td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($product->is_pinned)
                                        <span class="badge-boost badge-pinned"><i class="bi bi-pin-fill"></i> Pinned</span>
                                    @endif
                                    @if($product->is_featured)
                                        <span class="badge-boost badge-featured"><i class="bi bi-star-fill"></i> Featured</span>
                                    @endif
                                    @if($product->is_sponsored)
                                        <span class="badge-boost badge-sponsored"><i class="bi bi-megaphone-fill"></i> Sponsored</span>
                                    @endif
                                    @if($product->is_boosted)
                                        <span class="badge-boost badge-boosted"><i class="bi bi-rocket-takeoff-fill"></i> Boosted</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <label class="switch">
                                    <input type="checkbox" class="toggle-status" data-id="{{ $product->id }}" @if($product->status == 'active') checked @endif>
                                    <span class="slider"></span>
                                </label>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-danger" onclick="removeBoost({{ $product->id }})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Boost Modal -->
<div class="modal fade" id="addBoostModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Boosted Products</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-body">
                                <h6>Select Boost Type</h6>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="boost_type" value="featured" id="boostFeatured">
                                    <label class="form-check-label" for="boostFeatured">Featured (30% boost)</label>
                                </div>
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="boost_type" value="sponsored" id="boostSponsored">
                                    <label class="form-check-label" for="boostSponsored">Sponsored (Prime slots)</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="boost_type" value="pinned" id="boostPinned">
                                    <label class="form-check-label" for="boostPinned">Pinned (Top results)</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-3">
                                    <h6>Select Products</h6>
                                    <span class="selected-count-badge" id="selectedCount">0 Selected</span>
                                </div>
                                <input type="text" class="form-control mb-3" id="productSearch" placeholder="Search products...">
                                <div class="mb-2">
                                    <label>
                                        <input type="checkbox" id="selectAllProducts"> Select All
                                    </label>
                                </div>
                                <div id="productList" style="max-height: 400px; overflow-y: auto;">
                                    @foreach($allProducts as $product)
                                    <div class="product-item">
                                        <div class="d-flex align-items-center">
                                            <input type="checkbox" class="product-checkbox" data-id="{{ $product->id }}">
                                            <div class="ms-2">
                                                <div><strong>{{ $product->name }}</strong></div>
                                                <small>{{ $product->seller->name ?? 'Admin' }} | ${{ number_format($product->price, 2) }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAddBoost">Add Selected</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function() {
    // Initialize DataTable
    if ($.fn.DataTable) {
        $('#boostedProductsTable').DataTable({
            pageLength: 25,
            order: [[0, 'asc']],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries"
            }
        });
    }
    
    // CSRF token
    var csrfToken = $('meta[name="csrf-token"]').attr('content');
    
    // Update selected count
    function updateSelectedCount() {
        let count = $('.product-checkbox:checked').length;
        $('#selectedCount').text(count + ' Selected');
        let total = $('.product-checkbox').length;
        $('#selectAllProducts').prop('checked', total > 0 && count === total);
    }
    
    // Select all
    $('#selectAllProducts').change(function() {
        $('.product-checkbox').prop('checked', $(this).is(':checked'));
        updateSelectedCount();
    });
    
    // Individual selection
    $(document).on('change', '.product-checkbox', updateSelectedCount);
    
    // Search
    $('#productSearch').on('keyup', function() {
        let value = $(this).val().toLowerCase();
        $('.product-item').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Reset modal
    $('#addBoostModal').on('show.bs.modal', function() {
        $('.product-checkbox').prop('checked', false);
        $('#selectAllProducts').prop('checked', false);
        $('input[name="boost_type"]').prop('checked', false);
        updateSelectedCount();
    });
    
    // Add boost
    $('#confirmAddBoost').click(function() {
        let selectedProducts = [];
        $('.product-checkbox:checked').each(function() {
            selectedProducts.push($(this).data('id'));
        });
        let boostType = $('input[name="boost_type"]:checked').val();
        
        if(selectedProducts.length === 0) {
            showAlert('warning', 'Please select at least one product');
            return;
        }
        if(!boostType) {
            showAlert('warning', 'Please select boost type');
            return;
        }
        
        let btn = $(this);
        btn.html('Processing...').prop('disabled', true);
        
        $.ajax({
            url: '/admin/marketplace/add-multiple-boosts',
            method: 'POST',
            data: {
                product_ids: selectedProducts,
                boost_type: boostType,
                _token: csrfToken
            },
            success: function(response) {
                if(response.success) {
                    $('#addBoostModal').modal('hide');
                    showAlert('success', response.message);
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert('danger', response.message || 'Failed to add boosts');
                    btn.html('Add Selected').prop('disabled', false);
                }
            },
            error: function(xhr) {
                showAlert('danger', 'Error: ' + (xhr.responseJSON?.message || 'Server error'));
                btn.html('Add Selected').prop('disabled', false);
            }
        });
    });
    
    // Toggle status
    $(document).on('change', '.toggle-status', function() {
        let productId = $(this).data('id');
        let status = $(this).is(':checked') ? 'active' : 'inactive';
        let checkbox = $(this);
        
        $.ajax({
            url: '/admin/marketplace/toggle-product-status/' + productId,
            method: 'POST',
            data: { status: status, _token: csrfToken },
            success: function(response) {
                if(response.success) {
                    showAlert('success', response.message);
                } else {
                    showAlert('danger', response.message || 'Failed');
                    checkbox.prop('checked', !checkbox.is(':checked'));
                }
            },
            error: function() {
                showAlert('danger', 'Server error');
                checkbox.prop('checked', !checkbox.is(':checked'));
            }
        });
    });
});

// Filter by type
function filterByType(type) {
    var table = $('#boostedProductsTable').DataTable();
    table.search(type).draw();
    $('.stat-card').css('border', '1px solid #e5e7eb');
    if(event && event.currentTarget) {
        $(event.currentTarget).css('border', '2px solid #2563eb');
    }
}

// Remove boost - FIXED with POST method
function removeBoost(productId) {
    if(confirm('Are you sure you want to remove boost from this product?')) {
        var csrfToken = $('meta[name="csrf-token"]').attr('content');
        
        $.ajax({
            url: '/admin/marketplace/remove-boost/' + productId,
            method: 'POST',  // ← IMPORTANT: Using POST method
            data: { 
                type: 'all',
                _token: csrfToken 
            },
            success: function(response) {
                if(response.success) {
                    showAlert('success', response.message);
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showAlert('danger', response.message || 'Failed to remove boost');
                }
            },
            error: function(xhr) {
                if(xhr.status === 405) {
                    showAlert('danger', 'Method not allowed. Please check route configuration.');
                } else if(xhr.status === 419) {
                    showAlert('danger', 'Session expired. Please refresh the page.');
                } else {
                    showAlert('danger', 'Server error: ' + (xhr.responseJSON?.message || 'Please try again'));
                }
            }
        });
    }
}

// Show alert
function showAlert(type, message) {
    let alertBox = $('#actionAlert');
    alertBox.removeClass('d-none alert-success alert-danger alert-warning');
    
    if(type === 'success') {
        alertBox.addClass('alert-success').html('<i class="bi bi-check-circle-fill me-2"></i> ' + message);
    } else if(type === 'danger') {
        alertBox.addClass('alert-danger').html('<i class="bi bi-exclamation-triangle-fill me-2"></i> ' + message);
    } else if(type === 'warning') {
        alertBox.addClass('alert-warning').html('<i class="bi bi-exclamation-circle-fill me-2"></i> ' + message);
    }
    
    setTimeout(() => {
        alertBox.addClass('d-none');
    }, 3000);
}
</script>

@endsection