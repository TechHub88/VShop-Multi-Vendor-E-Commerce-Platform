@extends('layouts.vertical', ['title' => auth()->user()->type == 'travel' ? 'Locations' : 'Brands'])

@section('content')
<style>
.salesReportSearchArea{padding:15px 15px 15px;}
.container-fluid {
    padding-left: 15px;
    padding-right: 15px;
}
@media(max-width:575px){
.container-fluid {
    padding-left: 5px;
    padding-right: 5px;
}
</style>

@php
    $userType = auth()->user()->type;
    $isTravel = $userType == 'travel';
    $isProduct = $userType == 'product';
    
    $title = $isTravel ? 'Locations' : 'Brands';
    $singularTitle = $isTravel ? 'Location' : 'Brand';
    $createRoute = $isTravel ? 'admin.location.create' : 'admin.clients.create';
    $editRoute = $isTravel ? 'admin.location.edit' : 'admin.clients.edit';
    $destroyRoute = $isTravel ? 'admin.location.destroy' : 'admin.clients.destroy';
    $searchRoute = $isTravel ? 'admin.location.search' : 'admin.clients.search';
    $productsRoute = 'admin.products.index';
@endphp

<div class="container-fluid">

    <div class="pt-3 pb-1 d-flex align-items-center justify-content-between">
        <h4 class="fs-18 fw-semibold">{{ $title }}</h4>
        <a href="{{ route($createRoute) }}" class="btn btn-primary btn-sm">+ Add {{ $singularTitle }}</a>
    </div>

    <div class="card">
        <div class="card-body" style="padding:15px 15px;">
            <!-- Search, Filter and Export Controls - All in One Row -->
            <div class="filter-area mb-3a p-41a bg-light1 rounded salesReportSearchArea">
                <div class="d-flex flex-wrap flex-nowrap_1 gap-1 align-items-center salesReportSearchAreainner" >
                    <!-- Search keyword -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="keywordSearch" class="form-control" placeholder="Search {{ strtolower($title) }}...">
                        </div>
                    </div>
                    
                    <!-- Name Dropdown Filter -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <select id="nameFilter" class="form-select">
                                <option value="">All {{ $title }}</option>
                                @foreach($clients as $client)
                                    @if($client->name)
                                        <option value="{{ $client->name }}">{{ $client->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Status Filter (Show with/without logo) -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-image"></i></span>
                            <select id="logoFilter" class="form-select">
                                <option value="">Logo Status</option>
                                <option value="with_logo">With Logo</option>
                                <option value="without_logo">Without Logo</option>
                            </select>
                        </div>
                    </div>
                    
                    @if($isProduct)
                    <!-- Product Count Filter (Only for Product Admin) -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-cube"></i></span>
                            <select id="productCountFilter" class="form-select">
                                <option value="">All Products</option>
                                <option value="has_products">Has Products</option>
                                <option value="no_products">No Products</option>
                                <option value="5+">5+ Products</option>
                                <option value="10+">10+ Products</option>
                                <option value="20+">20+ Products</option>
                            </select>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Sort By -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-sort"></i></span>
                            <select id="sortFilter" class="form-select">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                                @if($isProduct)
                                <option value="products_asc">Products (Low to High)</option>
                                <option value="products_desc">Products (High to Low)</option>
                                @endif
                            </select>
                        </div>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <button type="button" id="applySearch" class="btn btn-success btn-sm">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                    
                    <!-- Divider -->
                    <div class="flex-shrink-0 salesReportSearchBox" style="width: 1px; height: 24px; background-color: #dee2e6;"></div>
                    
                    <!-- Select Fields Dropdown -->
                    <div class="flex-shrink-0 dropdown-container">
                        <button type="button" id="fieldDropdownBtn" class="btn btn-outline-primary btn-sm fieldDropdownBtn" 
                                style="background-color:#fff;border-color: #6f42c1; color: #6f42c1;">
                            <i class="fas fa-columns me-1"></i> Fields
                            <span id="selectedCount" class="badge bg-primary ms-1">{{ $isProduct ? '6' : '5' }}</span>
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                        <div id="fieldDropdown" class="dropdown-menu">
                            <div class="dropdown-title">Select Fields to Export</div>
                            <div class="dropdown-list">
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="sl" data-label="SL" checked>
                                    SL
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="name" data-label="{{ $singularTitle }} Name" checked>
                                    {{ $singularTitle }} Name
                                </label>
                                
                                @if($isProduct)
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="products_count" data-label="Product Count" checked>
                                    Product Count
                                </label>
                                @endif
                                
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="logo" data-label="Logo" checked>
                                    Logo
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="created_at" data-label="Created At" checked>
                                    Created At
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="updated_at" data-label="Updated At" checked>
                                    Updated At
                                </label>
                            </div>
                            <div class="dropdown-footer">
                                <button type="button" id="selectAllFields" class="btn btn-sm btn-link p-0 me-2">Select All</button>
                                <button type="button" id="deselectAllFields" class="btn btn-sm btn-link p-0">Deselect All</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export CSV Button -->
                    <div class="flex-shrink-0">
                        <button type="button" id="exportCsvBtn" class="btn btn-outline-primary btn-sm exportCsvBtn" 
                                style="background-color:#fff;border-color: #6f42c1; color: #6f42c1;">
                            <i class="fas fa-file-csv me-1"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading {{ strtolower($title) }}...</p>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="clientsTable" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th data-column="sl">#</th>
                            <th data-column="name">{{ $singularTitle }} Name</th>
                            
                            @if($isProduct)
                            <th data-column="products_count">Product Count</th>
                            @endif
                            
                            <th data-column="logo">Logo</th>
                            <th data-column="created_at">Created At</th>
                            <th data-column="updated_at">Updated At</th>
                            <th width="180">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="clientsTableBody">
                        @forelse($clients as $client)
                        @php
                            $productCount = $client->products_count ?? $client->products()->count();
                        @endphp
                        <tr>
                            <td data-column="sl">{{ $loop->iteration }}</td>
                            <td data-column="name">{{ $client->name ?? '-' }}</td>
                            
                            @if($isProduct)
                            <td data-column="products_count">
                                @if($productCount > 0)
                                    <a href="{{ route($productsRoute, ['brand_id' => $client->id]) }}" 
                                       class="product-count-link"
                                       target="_blank">
                                        <span class="badge bg-primary">
                                            <i class="fas fa-cube me-1"></i>
                                            {{ $productCount }} Product(s)
                                        </span>
                                    </a>
                                @else
                                    <span class="badge bg-secondary">No Products</span>
                                @endif
                            </td>
                            @endif
                            
                            <td data-column="logo">
                                @if($client->file && file_exists(public_path($client->file)))
                                    <img src="{{ asset($client->file) }}" alt="Logo" width="40" class="brand-logo" style="border-radius:4px; border:1px solid #ddd;">
                                @else
                                    <span class="text-muted">No Logo</span>
                                @endif
                            </td>
                            <td data-column="created_at">
                                {{ $client->created_at ? $client->created_at->format('d M, Y') : '-' }}
                            </td>
                            <td data-column="updated_at">
                                {{ $client->updated_at ? $client->updated_at->format('d M, Y') : '-' }}
                            </td>
                            <td>
                                <!-- Edit Button -->
                                <a href="{{ route($editRoute, $client->id) }}" class="btn btn-sm btn-warning" title="Edit {{ $singularTitle }}">
                                    <i class="fas fa-edit"></i>
                                </a>

                                @if($isProduct && $productCount > 0)
                                <!-- View Products Button -->
                                <a href="{{ route($productsRoute, ['brand_id' => $client->id]) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="View Products"
                                   target="_blank">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endif

                                <!-- Delete Button -->
                                <form action="{{ route($destroyRoute, $client->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this {{ strtolower($singularTitle) }}?')" title="Delete {{ $singularTitle }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="{{ $isProduct ? 7 : 6 }}" class="text-center text-muted py-4">
                                <i class="fas {{ $isTravel ? 'fa-map-marker-alt' : 'fa-tags' }} fa-2x mb-2"></i>
                                <p>No {{ strtolower($title) }} found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<style>
/* Single Line Filter Styles */
.filter-area .d-flex {
    flex-wrap: nowrap;
    white-space: nowrap;
}

.filter-area .flex-shrink-0 {
    flex-shrink: 0;
}

/* Custom scrollbar for single line layout */
.filter-area .d-flex::-webkit-scrollbar {
    height: 4px;
}

.filter-area .d-flex::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.filter-area .d-flex::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.filter-area .d-flex::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Form control styling */
.input-group-sm {
    height: 32px;
}

.input-group-sm .form-control,
.input-group-sm .form-select {
    height: 32px;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.input-group-sm .input-group-text {
    height: 32px;
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

/* Dropdown styling */
.dropdown-container { 
    position: relative; 
    display: inline-block; 
}

#fieldDropdown {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 280px;
    background: white;
    border: 1px solid #ddd;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 8px;
    z-index: 1050;
    padding: 0;
    margin-top: 5px;
    overflow: hidden;
}

#fieldDropdown.show {
    display: block !important;
}

.dropdown-title {
    font-weight: bold;
    margin: 0;
    padding: 12px 15px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
    background-color: #f8f9fa;
    color: #495057;
}

.dropdown-list {
    padding: 8px 0;
    max-height: 300px;
    overflow-y: auto;
}

.dropdown-list .dropdown-item {
    display: flex;
    align-items: center;
    width: 100%;
    margin: 0;
    padding: 10px 15px;
    cursor: pointer;
    font-size: 14px;
    border: none;
    background: none;
    text-align: left;
    transition: background-color 0.2s;
}

.dropdown-list .dropdown-item:hover {
    background-color: #f8f9fa;
}

.dropdown-list .dropdown-item input[type="checkbox"] {
    margin-right: 10px;
    cursor: pointer;
}

.dropdown-footer {
    margin: 0;
    padding: 12px 15px;
    border-top: 1px solid #eee;
    background-color: #f8f9fa;
    display: flex;
    justify-content: space-between;
}

/* Logo styling */
.brand-logo {
    border: 1px solid #dee2e6;
    border-radius: 4px;
    padding: 2px;
    background: white;
}

/* Product Count Link Styling */
.product-count-link {
    text-decoration: none;
    display: inline-block;
}

.product-count-link .badge {
    padding: 0.4em 0.8em;
    font-size: 0.85em;
    transition: all 0.2s;
}

.product-count-link:hover .badge {
    background-color: #0d6efd !important;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Button hover effects */
.btn-outline-primary[style*="border-color: #6f42c1"]:hover {
    background-color: #6f42c1;
    color: white;
    border-color: #6f42c1;
}

.btn-success:hover {
    background-color: #198754;
    border-color: #198754;
}

/* Responsive design */
@media (max-width: 1200px) {
    .filter-area .d-flex {
        overflow-x: auto;
        padding-bottom: 15px;
    }
    
    .filter-area .flex-shrink-0 {
        min-width: auto !important;
    }
}

@media (max-width: 768px) {
    .filter-area {
        padding: 15px !important;
    }
    
    .filter-area .d-flex {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-area .flex-shrink-0 {
        flex: 1 0 calc(50% - 10px);
        min-width: calc(50% - 10px) !important;
    }
    
    /* Hide divider on mobile */
    .filter-area .flex-shrink-0[style*="background-color"] {
        display: none;
    }
    
    /* Adjust dropdown on mobile */
    #fieldDropdown {
        width: 100%;
        left: 0;
        right: 0;
    }
}

@media (max-width: 576px) {
    .filter-area .flex-shrink-0 {
        flex: 1 0 100%;
        min-width: 100% !important;
    }
    
    .btn-sm i {
        margin-right: 0 !important;
    }
    
    .btn-sm span:not(.badge) {
        display: none;
    }
}

/* Loading indicator */
#loadingIndicator {
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    margin: 20px 0;
}

/* Table styling */
.table {
    margin-bottom: 0;
}

.table thead th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
    padding: 12px 15px;
}

.table tbody td {
    padding: 12px 15px;
    vertical-align: middle;
}

/* Card styling */
.card {
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.card-body {
    background-color: #fff;
}

.filter-area {
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

/* Badge styling for product count */
.badge {
    font-size: 0.85em;
    font-weight: 500;
}

.btn-group .btn {
    margin-right: 5px;
}

.btn-group .btn:last-child {
    margin-right: 0;
}
</style>

<!-- Load jQuery and SweetAlert -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    console.log('{{ $title }} page loaded');
    
    // ================= 1. DROPDOWN TOGGLE FUNCTIONALITY =================
    $('#fieldDropdownBtn').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('#fieldDropdown').toggleClass('show');
    });
    
    // Close dropdown when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.dropdown-container').length) {
            $('#fieldDropdown').removeClass('show');
        }
    });
    
    // Prevent dropdown from closing when clicking inside
    $('#fieldDropdown').click(function(e) {
        e.stopPropagation();
    });
    
    // ================= 2. UPDATE SELECTED COUNT =================
    function updateSelectedCount() {
        var count = $('.field-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count === 0) {
            $('#selectedCount').removeClass('bg-primary').addClass('bg-danger');
            $('#fieldDropdownBtn').addClass('border-danger');
        } else {
            $('#selectedCount').removeClass('bg-danger').addClass('bg-primary');
            $('#fieldDropdownBtn').removeClass('border-danger');
        }
    }
    
    // ================= 3. CHECKBOX FUNCTIONALITY =================
    $('.field-checkbox').on('change', function(e) {
        e.stopPropagation();
        updateSelectedCount();
    });
    
    // Select All
    $('#selectAllFields').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.field-checkbox').prop('checked', true);
        updateSelectedCount();
    });
    
    // Deselect All
    $('#deselectAllFields').click(function(e) {
        e.preventDefault();
        e.stopPropagation();
        $('.field-checkbox').prop('checked', false);
        updateSelectedCount();
    });
    
    // ================= 4. SEARCH FUNCTIONALITY =================
    $('#applySearch').click(function() {
        performSearch();
    });
    
    // Enter key support
    $('#keywordSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            performSearch();
        }
    });
    
    // ================= 5. PERFORM SEARCH =================
    function performSearch() {
        // Get search parameters
        var keyword = $('#keywordSearch').val();
        var nameFilter = $('#nameFilter').val();
        var logoFilter = $('#logoFilter').val();
        var sortFilter = $('#sortFilter').val();
        
        // Add product count filter only for product admin
        var data = {
            _token: '{{ csrf_token() }}',
            keyword: keyword,
            name: nameFilter,
            logo_filter: logoFilter,
            sort_filter: sortFilter
        };
        
        @if($isProduct)
        var productCountFilter = $('#productCountFilter').val();
        data.product_count_filter = productCountFilter;
        @endif
        
        console.log('Searching {{ strtolower($title) }} with:', data);
        
        // Show loading
        $('#loadingIndicator').show();
        $('#clientsTableBody').html('');
        
        // Send AJAX request
        $.ajax({
            url: '{{ route($searchRoute) }}',
            type: 'POST',
            data: data,
            success: function(response) {
                console.log('Search response:', response);
                $('#loadingIndicator').hide();
                
                if (response.success && response.data) {
                    displaySearchResults(response.data);
                } else {
                    showNoResults(response.message || 'No {{ strtolower($title) }} found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                $('#loadingIndicator').hide();
                
                let errorMessage = 'An error occurred while searching. Please try again.';
                
                if (xhr.status === 404) {
                    errorMessage = 'Route not found. Please define the route {{ $searchRoute }}';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // Simple alert for error
                alert('Error: ' + errorMessage);
                
                showNoResults();
            }
        });
    }
    
    // ================= 6. DISPLAY SEARCH RESULTS =================
    function displaySearchResults(data) {
        var tbody = $('#clientsTableBody');
        tbody.html('');
        
        if (data.length > 0) {
            console.log(`Found ${data.length} {{ strtolower($singularTitle) }}(s)`);
            
            // Create table rows
            $.each(data, function(index, item) {
                var logoHtml = item.logo_url 
                    ? `<img src="${item.logo_url}" alt="Logo" width="40" class="brand-logo" style="border-radius:4px; border:1px solid #ddd;">`
                    : '<span class="text-muted">No Logo</span>';
                
                // Product count badge (only for product admin)
                var productCountHtml = '';
                @if($isProduct)
                var productCount = item.products_count || 0;
                if (productCount > 0) {
                    productCountHtml = `<td data-column="products_count">
                        <a href="${item.products_url || '#'}" class="product-count-link" title="View ${productCount} product(s)">
                            <span class="badge bg-primary">
                                <i class="fas fa-cube me-1"></i>
                                ${productCount} Product(s)
                            </span>
                        </a>
                    </td>`;
                } else {
                    productCountHtml = '<td data-column="products_count"><span class="badge bg-secondary">No Products</span></td>';
                }
                @endif
                
                var row = '<tr>' +
                    '<td data-column="sl">' + (index + 1) + '</td>' +
                    '<td data-column="name">' + (item.name || '-') + '</td>' +
                    
                    @if($isProduct)
                    productCountHtml +
                    @endif
                    
                    '<td data-column="logo">' + logoHtml + '</td>' +
                    '<td data-column="created_at">' + (item.created_at_formatted || '-') + '</td>' +
                    '<td data-column="updated_at">' + (item.updated_at_formatted || '-') + '</td>' +
                    '<td>' +
                        '<a href="' + (item.edit_url || '#') + '" class="btn btn-sm btn-warning" title="Edit {{ $singularTitle }}">' +
                            '<i class="fas fa-edit"></i>' +
                        '</a>' +
                        
                        @if($isProduct)
                        (productCount > 0 ? 
                            '<a href="' + (item.products_url || '#') + '" class="btn btn-sm btn-info" title="View Products">' +
                                '<i class="fas fa-eye"></i>' +
                            '</a>' : '') +
                        @endif
                        
                        '<form action="' + (item.delete_url || '#') + '" method="POST" style="display:inline;">' +
                            '<input type="hidden" name="_token" value="{{ csrf_token() }}">' +
                            '<input type="hidden" name="_method" value="DELETE">' +
                            '<button class="btn btn-sm btn-danger" onclick="return confirm(\'Are you sure you want to delete this {{ strtolower($singularTitle) }}?\')" title="Delete {{ $singularTitle }}">' +
                                '<i class="fas fa-trash"></i>' +
                            '</button>' +
                        '</form>' +
                    '</td>' +
                '</tr>';
                
                tbody.append(row);
            });
            
        } else {
            showNoResults();
        }
    }
    
    // ================= 7. SHOW NO RESULTS =================
    function showNoResults(message) {
        var tbody = $('#clientsTableBody');
        var msg = message || 'No {{ strtolower($title) }} found for the selected criteria';
        
        tbody.html(
            '<tr>' +
                '<td colspan="{{ $isProduct ? 7 : 6 }}" class="text-center text-muted py-4">' +
                    '<i class="fas {{ $isTravel ? 'fa-map-marker-alt' : 'fa-tags' }} fa-2x mb-2"></i>' +
                    '<p>' + msg + '</p>' +
                '</td>' +
            '</tr>'
        );
    }
    
    // ================= 8. EXPORT CSV =================
    $('#exportCsvBtn').click(function() {
        // Get selected fields
        var selectedFields = [];
        $('.field-checkbox:checked').each(function() {
            selectedFields.push({
                value: $(this).val(),
                label: $(this).data('label')
            });
        });
        
        if (selectedFields.length === 0) {
            alert('Please select at least one field to export.');
            return;
        }
        
        // Show confirmation
        var confirmMessage = `You are about to export ${selectedFields.length} field(s):\n\n`;
        selectedFields.forEach(function(field, index) {
            confirmMessage += `${index + 1}. ${field.label}\n`;
        });
        confirmMessage += "\nThe export will include all {{ strtolower($title) }} matching your current search filters.";
        
        if (!confirm(confirmMessage + "\n\nClick OK to export CSV, or Cancel to abort.")) {
            return;
        }
        
        // Show loading
        var originalText = $(this).html();
        $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Exporting...').prop('disabled', true);
        
        // Get current search parameters
        var keyword = $('#keywordSearch').val();
        var nameFilter = $('#nameFilter').val();
        var logoFilter = $('#logoFilter').val();
        var sortFilter = $('#sortFilter').val();
        
        // Prepare data
        var data = {
            _token: '{{ csrf_token() }}',
            keyword: keyword,
            name: nameFilter,
            logo_filter: logoFilter,
            sort_filter: sortFilter,
            export: true
        };
        
        @if($isProduct)
        var productCountFilter = $('#productCountFilter').val();
        data.product_count_filter = productCountFilter;
        @endif
        
        // Get data for export
        $.ajax({
            url: '{{ route($searchRoute) }}',
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success && response.data) {
                    exportToCSV(response.data, selectedFields);
                    alert('Export successful! CSV file has been downloaded.');
                } else {
                    alert('No data available to export.');
                }
            },
            error: function(xhr, status, error) {
                console.error('Export error:', error);
                alert('Error exporting data. Please try again.');
            },
            complete: function() {
                $('#exportCsvBtn').html(originalText).prop('disabled', false);
            }
        });
    });
    
    // ================= 9. EXPORT TO CSV FUNCTION =================
    function exportToCSV(data, selectedFields) {
        // Build CSV content with BOM for UTF-8
        var csvContent = '\ufeff'; // UTF-8 BOM
        
        // Headers
        var headers = selectedFields.map(function(field) {
            return field.label;
        });
        csvContent += headers.join(',') + '\n';
        
        // Data rows
        $.each(data, function(index, item) {
            var row = [];
            $.each(selectedFields, function(i, field) {
                var value = '';
                switch(field.value) {
                    case 'sl':
                        value = index + 1;
                        break;
                    case 'name':
                        value = item.name || '-';
                        break;
                    case 'products_count':
                        value = item.products_count || 0;
                        break;
                    case 'logo':
                        value = item.logo_url ? 'Has Logo' : 'No Logo';
                        break;
                    case 'created_at':
                        value = item.created_at_formatted || '-';
                        break;
                    case 'updated_at':
                        value = item.updated_at_formatted || '-';
                        break;
                }
                // Escape CSV special characters
                if (value.toString().includes(',') || value.toString().includes('"') || value.toString().includes('\n')) {
                    value = '"' + value.toString().replace(/"/g, '""') + '"';
                }
                row.push(value);
            });
            csvContent += row.join(',') + '\n';
        });
        
        // Create and download CSV
        var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        var link = document.createElement('a');
        var url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', '{{ strtolower($title) }}_' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up
        setTimeout(() => URL.revokeObjectURL(url), 100);
    }
    
    // ================= 10. ADDITIONAL ENHANCEMENTS =================
    
    // Auto-close dropdown when clicking export button
    $('#exportCsvBtn').click(function() {
        $('#fieldDropdown').removeClass('show');
    });
    
    // Initialize selected count
    updateSelectedCount();
    
    console.log('{{ $title }} functionality initialized');
});
</script>
@endsection