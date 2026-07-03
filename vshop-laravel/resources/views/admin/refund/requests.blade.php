@extends('layouts.vertical', ['title' => 'Registered Users'])

@section('content')

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
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary,
    .btn-outline-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .nav-tabs .nav-link.active {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .reset-btn {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .reset-btn:hover {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
</style>
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center py-3">
        <h4 class="fw-semibold">All Customers</h4>
        <button class="btn btn-light btn-sm">Clear Cache</button>
    </div>

    <!-- Search, Filter and Export Controls - All in One Row -->
   <div class="card mb-3">
    <div class="card-body">
        <div class="filter-area mb-3 p-3 bg-light rounded">
            <div class="d-flex flex-nowrap gap-3 align-items-center" style="overflow-x: auto; padding-bottom: 10px;">
                <!-- Search keyword -->
                <div class="flex-shrink-0" style="min-width: 200px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="keywordSearch" class="form-control" placeholder="Search name, email...">
                    </div>
                </div>
                
                <!-- Brand Dropdown Filter -->
                <!--<div class="flex-shrink-0" style="min-width: 180px;">-->
                <!--    <div class="input-group input-group-sm">-->
                <!--        <span class="input-group-text"><i class="fas fa-tag"></i></span>-->
                <!--        <select id="brandFilter" class="form-select">-->
                <!--            <option value="">All Brands</option>-->
                <!--            @foreach($brands as $brand)-->
                <!--                @if($brand->name)-->
                <!--                    <option value="{{ $brand->name }}">{{ $brand->name }}</option>-->
                <!--                @endif-->
                <!--            @endforeach-->
                <!--        </select>-->
                <!--    </div>-->
                <!--</div>-->
                
                <!-- Status Filter -->
                <div class="flex-shrink-0" style="min-width: 160px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-circle"></i></span>
                        <select id="statusFilter" class="form-select">
                            <option value="">All Status</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                            <option value="pending">Pending</option>
                            <option value="blocked">Blocked</option>
                        </select>
                    </div>
                </div>
                
                <!-- Sort By -->
                <div class="flex-shrink-0" style="min-width: 160px;">
                    <div class="input-group input-group-sm">
                        <span class="input-group-text"><i class="fas fa-sort"></i></span>
                        <select id="sortFilter" class="form-select">
                            <option value="newest">Newest First</option>
                            <option value="oldest">Oldest First</option>
                            <option value="name_asc">Name (A-Z)</option>
                            <option value="name_desc">Name (Z-A)</option>
                        </select>
                    </div>
                </div>
                
                <!-- Search Button -->
                <div class="flex-shrink-0">
                    <button type="button" id="applySearch" class="btn btn-success btn-sm">
                        <i class="fas fa-search me-1"></i> Search
                    </button>
                    
                    <button class="btn reset-btn btn-sm">Reset</button>
                </div>
                
                <!-- Divider -->
                <div class="flex-shrink-0" style="width: 1px; height: 24px; background-color: #dee2e6;"></div>
                
                <!-- Select Fields Dropdown -->
                <div class="flex-shrink-0 dropdown-container">
                    <button type="button" id="fieldDropdownBtn" class="btn btn-outline-primary btn-sm" 
                            style="border-color: #6f42c1; color: #6f42c1;">
                        <i class="fas fa-columns me-1"></i> Fields
                        <span id="selectedCount" class="badge bg-primary ms-1">6</span>
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
                                <input type="checkbox" class="field-checkbox me-2" value="name" data-label="Name" checked>
                                Name
                            </label>
                            <label class="dropdown-item">
                                <input type="checkbox" class="field-checkbox me-2" value="email" data-label="Email" checked>
                                Email
                            </label>
                            <label class="dropdown-item">
                                <input type="checkbox" class="field-checkbox me-2" value="brand" data-label="Brand" checked>
                                Brand
                            </label>
                            <label class="dropdown-item">
                                <input type="checkbox" class="field-checkbox me-2" value="status" data-label="Status" checked>
                                Status
                            </label>
                            <label class="dropdown-item">
                                <input type="checkbox" class="field-checkbox me-2" value="created_at" data-label="Created At" checked>
                                Created At
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
                    <button type="button" id="exportCsvBtn" class="btn btn-outline-primary btn-sm" 
                            style="border-color: #6f42c1; color: #6f42c1;">
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
            <p class="mt-2 text-muted">Loading users...</p>
        </div>
    </div>
</div>

    <!-- Table -->
    <div class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="usersTable" class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="40"><input type="checkbox" id="selectAllCheckbox"></th>
                            <th data-column="sl">SL</th>
                            <th data-column="name">Name</th>
                            <th data-column="email">Email</th>
                            <th data-column="brand" data-brand="{{ $customer->brand->name ?? '' }}">Brand</th>
                            <th data-column="status">Status</th>
                            <th data-column="created_at">Created</th>
                            <th width="100" class="text-end">Action</th>
                        </tr>
                    </thead>

                    <tbody id="usersTableBody">
                        @forelse($customers as $customer)
                            <tr>
                                <td><input type="checkbox" class="row-checkbox" value="{{ $customer->id }}"></td>
                                <td data-column="sl">{{ $loop->iteration }}</td>
                                <td data-column="name">{{ $customer->name }}</td>
                                <td data-column="email">{{ $customer->email }}</td>
                                <td data-column="brand" data-brand="{{ $customer->brand->name ?? '' }}">
                                    {{ $customer->brand->name ?? '' }}
                                </td>
                                <td data-column="status" data-status="{{ $customer->status }}">
                                    <span class="badge bg-{{ $customer->status == 'active' ? 'success' : ($customer->status == 'inactive' ? 'secondary' : ($customer->status == 'pending' ? 'warning' : 'danger')) }}">
                                        {{ $customer->status }}
                                    </span>
                                </td>
                                <td data-column="created_at">{{ $customer->created_at->format('h:i a, d M, y') }}</td>
                                <td class="text-end">
                                    <form method="POST" action="{{ route('admin.customers.destroy', $customer->id) }}" onsubmit="return confirm('Are you sure you want to delete this user?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-users fa-2x mb-2 text-muted"></i>
                                    <p class="text-muted">No users found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-3">
                {{ $customers->links() }}
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

@media (max-width: 992px) {
    .filter-area .d-flex {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-area .flex-shrink-0 {
        flex: 1 0 calc(33.333% - 10px);
        min-width: calc(33.333% - 10px) !important;
    }
    
    /* Hide divider on tablets */
    .filter-area .flex-shrink-0[style*="background-color"] {
        display: none;
    }
}

@media (max-width: 768px) {
    .filter-area {
        padding: 15px !important;
    }
    
    .filter-area .flex-shrink-0 {
        flex: 1 0 calc(50% - 10px);
        min-width: calc(50% - 10px) !important;
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

/* Status badges */
.badge.bg-success { background-color: #28a745 !important; }
.badge.bg-secondary { background-color: #6c757d !important; }
.badge.bg-warning { background-color: #ffc107 !important; color: #000; }
.badge.bg-danger { background-color: #dc3545 !important; }
</style>

<!-- Load jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    console.log('Registered Users page loaded');
    
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
    
    // ================= 4. TABLE CHECKBOX SELECTION =================
    $('#selectAllCheckbox').change(function() {
        $('.row-checkbox').prop('checked', $(this).prop('checked'));
    });
    
    $('.row-checkbox').change(function() {
        if($('.row-checkbox:checked').length === $('.row-checkbox').length) {
            $('#selectAllCheckbox').prop('checked', true);
        } else {
            $('#selectAllCheckbox').prop('checked', false);
        }
    });
    
    // ================= 5. SEARCH FUNCTIONALITY =================
    $('#applySearch').click(function() {
        performClientSideSearch();
    });
    
    // Enter key support
    $('#keywordSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            performClientSideSearch();
        }
    });
    
    // ================= 6. CLIENT-SIDE SEARCH =================
    function performClientSideSearch() {
        var keyword = $('#keywordSearch').val().toLowerCase();
        var selectedBrand = $('#brandFilter').val();
        var selectedStatus = $('#statusFilter').val();
        var selectedSort = $('#sortFilter').val();
        
        var rows = $('#usersTableBody tr');
        var visibleRows = [];
        var visibleCount = 0;
        
        rows.each(function() {
            var $row = $(this);
            var name = $row.find('td[data-column="name"]').text().toLowerCase();
            var email = $row.find('td[data-column="email"]').text().toLowerCase();
            var brand = $row.find('td[data-column="brand"]').data('brand') || $row.find('td[data-column="brand"]').text();
            var status = $row.find('td[data-column="status"]').data('status') || $row.find('td[data-column="status"] .badge').text().toLowerCase();
            
            var show = true;
            
            // Keyword filter (search in name and email)
            if (keyword) {
                var keywordMatch = name.includes(keyword) || email.includes(keyword);
                if (!keywordMatch) {
                    show = false;
                }
            }
            
            // Brand filter
            if (selectedBrand && brand !== selectedBrand) {
                show = false;
            }
            
            // Status filter
            if (selectedStatus && status !== selectedStatus) {
                show = false;
            }
            
            if (show) {
                $row.show();
                visibleCount++;
                visibleRows.push({
                    row: $row,
                    name: $row.find('td[data-column="name"]').text(),
                    created_at: $row.find('td[data-column="created_at"]').text()
                });
            } else {
                $row.hide();
            }
        });
        
        // Apply sorting
        if (selectedSort && visibleRows.length > 0) {
            applySorting(visibleRows, selectedSort);
        }
        
        // Show results message
        if (visibleCount === 0) {
            showNoResults('No users found matching your criteria');
        } else {
            // Remove any existing no results row
            $('#usersTableBody tr.no-results-row').remove();
            
            // Show success message
            console.log(`Found ${visibleCount} user(s)`);
        }
    }
    
    // ================= 7. APPLY SORTING =================
    function applySorting(visibleRows, sortFilter) {
        var tbody = $('#usersTableBody');
        
        // Sort the rows array
        visibleRows.sort(function(a, b) {
            switch(sortFilter) {
                case 'name_asc':
                    return a.name.localeCompare(b.name);
                case 'name_desc':
                    return b.name.localeCompare(a.name);
                case 'newest':
                    // Assuming newer dates are later in alphabet
                    return b.created_at.localeCompare(a.created_at);
                case 'oldest':
                    return a.created_at.localeCompare(b.created_at);
                default:
                    return 0;
            }
        });
        
        // Reorder rows in the table
        tbody.empty();
        visibleRows.forEach(function(item) {
            tbody.append(item.row);
        });
    }
    
    // ================= 8. SHOW NO RESULTS =================
    function showNoResults(message) {
        var tbody = $('#usersTableBody');
        
        // Hide all existing rows
        tbody.find('tr').hide();
        
        // Check if no results row already exists
        if (tbody.find('tr.no-results-row').length === 0) {
            var msg = message || 'No users found for the selected criteria';
            
            var noResultsRow = '<tr class="no-results-row">' +
                '<td colspan="8" class="text-center text-muted py-4">' +
                    '<i class="fas fa-users fa-2x mb-2"></i>' +
                    '<p>' + msg + '</p>' +
                '</td>' +
            '</tr>';
            
            tbody.append(noResultsRow);
        }
    }
    
    // ================= 9. RESET FILTERS =================
    function resetFilters() {
        $('#keywordSearch').val('');
        $('#brandFilter').val('');
        $('#statusFilter').val('');
        $('#sortFilter').val('newest');
        
        // Show all rows
        $('#usersTableBody tr').show();
        
        // Remove no results row if exists
        $('#usersTableBody tr.no-results-row').remove();
    }
    
    // ================= 10. EXPORT CSV =================
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
        confirmMessage += "\nThe export will include all users matching your current search filters.";
        
        if (!confirm(confirmMessage + "\n\nClick OK to export CSV, or Cancel to abort.")) {
            return;
        }
        
        // Show loading
        var originalText = $(this).html();
        $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Exporting...').prop('disabled', true);
        
        // Get visible data for export
        var tableData = getTableDataForExport(selectedFields);
        if (tableData.length > 0) {
            exportToCSV(tableData, selectedFields);
            alert('Export successful! CSV file has been downloaded.');
        } else {
            alert('No data available to export.');
        }
        
        // Reset button
        $('#exportCsvBtn').html(originalText).prop('disabled', false);
    });
    
    // ================= 11. GET TABLE DATA FOR EXPORT =================
    function getTableDataForExport(selectedFields) {
        var data = [];
        var visibleRows = $('#usersTableBody tr:visible').not('.no-results-row');
        
        visibleRows.each(function(index) {
            var row = $(this);
            var user = {
                id: row.find('.row-checkbox').val(),
                name: row.find('td[data-column="name"]').text().trim(),
                email: row.find('td[data-column="email"]').text().trim(),
                brand_name: row.find('td[data-column="brand"]').data('brand') || row.find('td[data-column="brand"]').text().trim(),
                status: row.find('td[data-column="status"]').data('status') || row.find('td[data-column="status"] .badge').text().trim().toLowerCase(),
                created_at_formatted: row.find('td[data-column="created_at"]').text().trim()
            };
            data.push(user);
        });
        
        return data;
    }
    
    // ================= 12. EXPORT TO CSV FUNCTION =================
    function exportToCSV(data, selectedFields) {
        // Build CSV content with BOM for UTF-8
        var csvContent = '\ufeff'; // UTF-8 BOM
        
        // Headers
        var headers = selectedFields.map(function(field) {
            return field.label;
        });
        csvContent += headers.join(',') + '\n';
        
        // Data rows
        $.each(data, function(index, user) {
            var row = [];
            $.each(selectedFields, function(i, field) {
                var value = '';
                switch(field.value) {
                    case 'sl':
                        value = index + 1;
                        break;
                    case 'name':
                        value = user.name || '-';
                        break;
                    case 'email':
                        value = user.email || '-';
                        break;
                    case 'brand':
                        value = user.brand_name || '';
                        break;
                    case 'status':
                        value = user.status ? user.status.charAt(0).toUpperCase() + user.status.slice(1) : 'Inactive';
                        break;
                    case 'created_at':
                        value = user.created_at_formatted || '-';
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
        link.setAttribute('download', 'users_' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up
        setTimeout(() => URL.revokeObjectURL(url), 100);
    }
    
    // ================= 13. ADDITIONAL ENHANCEMENTS =================
    
    // Auto-close dropdown when clicking export button
    $('#exportCsvBtn').click(function() {
        $('#fieldDropdown').removeClass('show');
    });
    
    // Keyboard shortcuts
    $(document).keydown(function(e) {
        // Ctrl+E for export (Windows/Linux)
        if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
            e.preventDefault();
            $('#exportCsvBtn').click();
        }
        // Escape to close dropdown
        if (e.key === 'Escape') {
            $('#fieldDropdown').removeClass('show');
        }
    });
    
    // Initialize selected count
    updateSelectedCount();
    
    console.log('Registered Users functionality initialized');
});
</script>
@endsection