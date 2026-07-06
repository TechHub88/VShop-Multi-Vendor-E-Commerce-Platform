@extends('layouts.vertical', ['title' => 'Customer Report'])

@section('content')

<div class="container-fluid py-4 px-4">

    <!-- Main Card -->
    <div class="card shadow-sm">
        <div class="card-header px-4 py-3">
            <h6 class="mb-0">Customer Report</h6>
        </div>
        <div class="card-body px-4 py-4">
            
            <!-- Filter Area - Single Line -->
            <div class="filter-area mb-4 p-4 bg-light rounded">
                <div class="d-flex flex-nowrap gap-3 align-items-center" style="overflow-x: auto; padding-bottom: 10px;">
                    <!-- Search keyword -->
                    <div class="flex-shrink-0" style="min-width: 180px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="keywordSearch" class="form-control" placeholder="Search customers...">
                        </div>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="flex-shrink-0" style="min-width: 280px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" id="dateFrom" class="form-control" placeholder="Start Date">
                            <span class="input-group-text">to</span>
                            <input type="date" id="dateTo" class="form-control" placeholder="End Date">
                        </div>
                    </div>
                    
                    <!-- Customer Type -->
                    <div class="flex-shrink-0" style="min-width: 160px;">
                        <select id="typeFilter" class="form-select form-select-sm">
                            <option value="">Customer Type</option>
                            <option value="new">New Customers</option>
                            <option value="repeat">Repeat Customers</option>
                            <option value="inactive">Inactive (30+ days)</option>
                            <option value="active">Active (Last 30 days)</option>
                        </select>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex-shrink-0">
                        <button type="button" id="applySearch" class="btn btn-success btn-sm">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
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
                                    <input type="checkbox" class="field-checkbox me-2" value="customer_name" data-label="Customer Name" checked>
                                    Customer Name
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="email" data-label="Email" checked>
                                    Email
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="total_orders" data-label="Total Orders" checked>
                                    Total Orders
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="total_spend" data-label="Total Spend" checked>
                                    Total Spend
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="last_purchase" data-label="Last Purchase" checked>
                                    Last Purchase
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="avg_order_value" data-label="Avg Order Value" checked>
                                    Avg Order Value
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
                <p class="mt-2 text-muted">Loading customer report...</p>
            </div>

            <!-- Customer Report Table -->
            <div class="table-responsive mt-3">
                <table id="customerReportTable" class="table table-bordered table-hover" style="width:100%;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center">#</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Total Orders</th>
                            <th>Total Spend</th>
                            <th>Last Purchase</th>
                            <th>Avg Order Value</th>
                        </tr>
                    </thead>
                    <tbody id="customerReportTableBody">
                        @foreach ($customers as $index => $cus)
                        <tr>
                            <td class="text-center fw-semibold">{{ $index + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm me-2">
                                        <div class="avatar-title bg-light rounded">
                                            {{ substr($cus->name ?? 'N/A', 0, 1) }}
                                        </div>
                                    </div>
                                    <div>
                                        <h6 class="mb-0">{{ $cus->name ?? 'N/A' }}</h6>
                                        <small class="text-muted">ID: #{{ $cus->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="text-primary">{{ $cus->email ?? '---' }}</div>
                                    <small class="text-muted">{{ $cus->phone ?? 'No phone' }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $cus->total_orders ?? 0 }}</span>
                            </td>
                            <td class="text-end">
                                <span class="fw-bold text-success">₹{{ number_format($cus->total_spend ?? 0, 2) }}</span>
                            </td>
                            <td>
                                @if($cus->last_purchase)
                                    {{ \Carbon\Carbon::parse($cus->last_purchase)->format('d M, Y') }}
                                    <br>
                                    <small class="text-muted">{{ \Carbon\Carbon::parse($cus->last_purchase)->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">---</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @php
                                    $avgValue = ($cus->total_orders ?? 0) > 0 
                                        ? ($cus->total_spend ?? 0) / ($cus->total_orders ?? 1) 
                                        : 0;
                                @endphp
                                <span class="fw-bold text-warning">₹{{ number_format($avgValue, 2) }}</span>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($customers->isEmpty())
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <p>No customers found</p>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
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

/* Single line filter items */
.filter-area .d-flex {
    flex-wrap: nowrap;
    white-space: nowrap;
}

.filter-area .flex-shrink-0 {
    flex-shrink: 0;
}

/* Make responsive */
@media (max-width: 1200px) {
    .filter-area .d-flex {
        overflow-x: auto;
        padding-bottom: 15px;
    }
    
    .filter-area .flex-shrink-0 {
        min-width: auto !important;
    }
    
    #keywordSearch, #dateFrom, #dateTo, #typeFilter {
        width: 140px !important;
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
}

@media (max-width: 576px) {
    .filter-area .flex-shrink-0 {
        flex: 1 0 100%;
        min-width: 100% !important;
    }
}

/* Compact form controls */
.input-group-sm {
    height: 32px;
}

.form-select-sm, .form-control-sm {
    height: 32px;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

/* Date inputs */
.input-group-sm .form-control[type="date"] {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
    min-width: 100px;
}

.input-group-sm .form-control[type="date"]::-webkit-calendar-picker-indicator {
    padding: 0;
    cursor: pointer;
}

/* Enhanced spacing and padding */
.container-fluid {
    padding-left: 1.5rem;
    padding-right: 1.5rem;
}

.card {
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    font-weight: 600;
}

.card-body {
    background-color: #fff;
}

.filter-area {
    margin-bottom: 20px;
    border: 1px solid #e9ecef;
}

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

/* ========== DROPDOWN STYLING ========== */
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

.btn-outline-primary[style*="border-color: #6f42c1"]:hover {
    background-color: #6f42c1;
    color: white;
    border-color: #6f42c1;
}

.btn-success:hover {
    background-color: #198754;
    border-color: #198754;
}

/* Avatar styling */
.avatar-sm {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
}
.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    color: #495057;
    border-radius: 50%;
    background: #f8f9fa;
}

/* Loading indicator */
#loadingIndicator {
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    margin: 20px 0;
}
</style>

<!-- Load jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {
    console.log('Customer Report page loaded');
    
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
        } else {
            $('#selectedCount').removeClass('bg-danger').addClass('bg-primary');
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
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var type = $('#typeFilter').val();
        
        console.log('Searching customers with:', { keyword, dateFrom, dateTo, type });
        
        // Show loading
        $('#loadingIndicator').show();
        $('#customerReportTableBody').html('');
        
        // Send AJAX request
        $.ajax({
            url: '{{ route("admin.reports.searchCustomers") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                keyword: keyword,
                date_from: dateFrom,
                date_to: dateTo,
                type: type
            },
            success: function(response) {
                console.log('Search response:', response);
                $('#loadingIndicator').hide();
                
                if (response.success && response.data) {
                    displaySearchResults(response.data);
                } else {
                    showNoResults(response.message || 'No customer data found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                $('#loadingIndicator').hide();
                
                if (xhr.status === 404) {
                    alert('Error: Route not found. Please define the route admin.reports.searchCustomers');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    alert('Error: ' + xhr.responseJSON.message);
                } else {
                    alert('An error occurred while searching. Please try again.');
                }
                
                showNoResults();
            }
        });
    }
    
    // ================= 6. DISPLAY SEARCH RESULTS =================
    function displaySearchResults(data) {
        var tbody = $('#customerReportTableBody');
        tbody.html('');
        
        if (data.length > 0) {
            // Create table rows
            $.each(data, function(index, customer) {
                // Calculate average order value
                var avgOrderValue = customer.total_orders > 0 
                    ? customer.total_spend / customer.total_orders 
                    : 0;
                
                // Format last purchase date
                var lastPurchase = customer.last_purchase 
                    ? new Date(customer.last_purchase).toLocaleDateString('en-GB', {
                        day: 'numeric', month: 'short', year: 'numeric'
                    })
                    : '---';
                
                var row = '<tr>' +
                    '<td class="text-center fw-semibold">' + (index + 1) + '</td>' +
                    '<td>' +
                        '<div class="d-flex align-items-center">' +
                            '<div class="avatar-sm me-2">' +
                                '<div class="avatar-title bg-light rounded">' + 
                                    (customer.name ? customer.name.charAt(0).toUpperCase() : 'N') + 
                                '</div>' +
                            '</div>' +
                            '<div>' +
                                '<h6 class="mb-0">' + (customer.name || 'N/A') + '</h6>' +
                                '<small class="text-muted">ID: #' + (customer.id || '') + '</small>' +
                            '</div>' +
                        '</div>' +
                    '</td>' +
                    '<td>' +
                        '<div>' +
                            '<div class="text-primary">' + (customer.email || '---') + '</div>' +
                            '<small class="text-muted">' + (customer.phone || 'No phone') + '</small>' +
                        '</div>' +
                    '</td>' +
                    '<td class="text-center">' +
                        '<span class="badge bg-info">' + (customer.total_orders || 0) + '</span>' +
                    '</td>' +
                    '<td class="text-end">' +
                        '<span class="fw-bold text-success">₹' + 
                        (customer.total_spend || 0).toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 
                        '</span>' +
                    '</td>' +
                    '<td>' + lastPurchase + '</td>' +
                    '<td class="text-end">' +
                        '<span class="fw-bold text-warning">₹' + 
                        avgOrderValue.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}) + 
                        '</span>' +
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
        var tbody = $('#customerReportTableBody');
        var msg = message || 'No customer data found for the selected criteria';
        
        tbody.html(
            '<tr>' +
                '<td colspan="7" class="text-center text-muted py-4">' +
                    '<i class="fas fa-users fa-2x mb-2"></i>' +
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
        
        // Get current search parameters
        var keyword = $('#keywordSearch').val();
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var type = $('#typeFilter').val();
        
        // Show loading
        var originalText = $(this).html();
        $(this).html('<i class="fas fa-spinner fa-spin me-1"></i> Exporting...').prop('disabled', true);
        
        // Get data for export
        $.ajax({
            url: '{{ route("admin.reports.searchCustomers") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                keyword: keyword,
                date_from: dateFrom,
                date_to: dateTo,
                type: type,
                export: true
            },
            success: function(response) {
                if (response.success && response.data) {
                    exportToCSV(response.data, selectedFields);
                } else {
                    alert('No data available to export.');
                }
            },
            error: function() {
                alert('Error exporting data. Please try again.');
            },
            complete: function() {
                $('#exportCsvBtn').html(originalText).prop('disabled', false);
            }
        });
    });
    
    // ================= 9. EXPORT TO CSV FUNCTION =================
    function exportToCSV(data, selectedFields) {
        // Build CSV content
        var csvContent = '';
        
        // Headers
        var headers = selectedFields.map(function(field) {
            return field.label;
        });
        csvContent += headers.join(',') + '\n';
        
        // Data rows
        $.each(data, function(index, customer) {
            var row = [];
            $.each(selectedFields, function(i, field) {
                var value = '';
                switch(field.value) {
                    case 'customer_name':
                        value = customer.name || 'N/A';
                        break;
                    case 'email':
                        value = customer.email || '---';
                        break;
                    case 'total_orders':
                        value = customer.total_orders || 0;
                        break;
                    case 'total_spend':
                        value = (customer.total_spend || 0);
                        break;
                    case 'last_purchase':
                        value = customer.last_purchase 
                            ? new Date(customer.last_purchase).toLocaleDateString('en-GB')
                            : '---';
                        break;
                    case 'avg_order_value':
                        var avgValue = customer.total_orders > 0 
                            ? customer.total_spend / customer.total_orders 
                            : 0;
                        value = avgValue.toFixed(2);
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
        link.setAttribute('download', 'customer_report_' + new Date().toISOString().split('T')[0] + '.csv');
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
    
    // ================= 10. INITIALIZE =================
    updateSelectedCount();
    
    console.log('Customer report functionality initialized');
});
</script>

@endsection
