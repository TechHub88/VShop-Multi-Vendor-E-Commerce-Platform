@extends('layouts.vertical', ['title' => 'Active Customers'])

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
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center pt-3 pb-1">
        <h4 class="mb-0 fw-semibold fs-20">Active Customer</h4>

        
    </div>

    <div class="card mt-2">
        <div class="card-body" style="padding:15px 15px;">

            <!-- Search, Filter and Export Controls - All in One Row -->
            <div class="filter-area mb-3 p-41 bg-light1 rounded salesReportSearchArea">
                <div class="d-flex flex-wrap flex-nowrap_1 gap-1 align-items-center salesReportSearchAreainner">
                    <!-- Search keyword -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="keywordSearch" class="form-control" placeholder="Search customers...">
                        </div>
                    </div>
                    
                    <!-- Date Range -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            <input type="date" id="dateFrom" class="form-control" placeholder="From Date">
                            <span class="input-group-text">to</span>
                            <input type="date" id="dateTo" class="form-control" placeholder="To Date">
                        </div>
                    </div>
                    
                    <!-- Wallet Balance Filter -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-wallet"></i></span>
                            <select id="walletFilter" class="form-select">
                                <option value="">Wallet Balance</option>
                                <option value="high">High Balance</option>
                                <option value="medium">Medium Balance</option>
                                <option value="low">Low Balance</option>
                                <option value="zero">Zero Balance</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sort By -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-sort"></i></span>
                            <select id="sortFilter" class="form-select">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                                <option value="balance_high">Balance High to Low</option>
                                <option value="balance_low">Balance Low to High</option>
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
                    <div class="flex-shrink-0" style="width: 1px; height: 24px; background-color: #dee2e6;"></div>
                    
                    <!-- Select Fields Dropdown -->
                    <div class="flex-shrink-0 dropdown-container">
                        <button type="button" id="fieldDropdownBtn" class="btn btn-outline-primary btn-sm fieldDropdownBtn" 
                                style="background-color:#fff;border-color: #6f42c1; color: #6f42c1;">
                            <i class="fas fa-columns me-1"></i> Fields
                            <span id="selectedCount" class="badge bg-primary ms-1">7</span>
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
                                    <input type="checkbox" class="field-checkbox me-2" value="avatar" data-label="Avatar" checked>
                                    Avatar
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
                                    <input type="checkbox" class="field-checkbox me-2" value="phone" data-label="Phone" checked>
                                    Phone
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="active_status" data-label="Active Status" checked>
                                    Active Status
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="wallet_balance" data-label="Wallet Balance" checked>
                                    Wallet Balance
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
                <p class="mt-2 text-muted">Loading customers...</p>
            </div>

            <div class="d-flex justify-content-between mb-3">
                <h5 class="fw-bold fs-15">Active Customer List</h5>
            </div>

            <!-- Customers Table -->
            <div class="table-responsive">
                <table id="customersTable" class="table table-striped align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th data-column="sl">SL</th>
                            <th data-column="avatar">Avatar</th>
                            <th data-column="name">Name</th>
                            <th data-column="email">Email</th>
                            <th data-column="phone">Phone</th>
                            <th data-column="active_status">Is Active</th>
                            <th data-column="wallet_balance">Wallet Balance</th>
                        </tr>
                    </thead>

                    <tbody id="customersTableBody">
                        @foreach($customers as $index => $customer)
                        <tr>
                            <td data-column="sl">{{ $index + 1 }}</td>

                            <td data-column="avatar">
                                <img src="{{ $customer->avatar ?? asset('assets/default-avatar.png') }}"
                                     width="40" height="40" style="border-radius: 50%;">
                            </td>

                            <td data-column="name">{{ $customer->name }}</td>
                            <td data-column="email">{{ $customer->email }}</td>
                            <td data-column="phone">{{ $customer->phone ?? '---' }}</td>

                            <td data-column="active_status">
                                <label class="switch">
                                    <input type="checkbox" checked disabled>
                                    <span class="slider round"></span>
                                </label>
                            </td>

                            <td data-column="wallet_balance">${{ number_format($customer->wallet_balance ?? 0, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<style>
/* Switch styling */
.switch {
    position: relative; display: inline-block; width: 45px; height: 22px;
}
.switch input { opacity: 0; width: 0; height: 0; }
.slider {
    position: absolute; cursor: pointer; top: 0; left: 0;
    right: 0; bottom: 0; background-color: #ccc; transition: .4s; border-radius: 34px;
}
.slider:before {
    position: absolute; content: ""; height: 18px; width: 18px;
    left: 3px; bottom: 2px; background-color: white; transition: .4s; border-radius: 50%;
}
input:checked + .slider { background-color: #7c4dff; }
input:checked + .slider:before { transform: translateX(22px); }

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

/* Date inputs styling */
.input-group-sm .form-control[type="date"] {
    min-width: 100px;
}

.input-group-sm .form-control[type="date"]::-webkit-calendar-picker-indicator {
    padding: 0;
    cursor: pointer;
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

/* Avatar image styling */
.table img[style*="border-radius: 50%"] {
    object-fit: cover;
}
</style>

<!-- Load jQuery and SweetAlert -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('Active Customers page loaded');
    
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
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var walletFilter = $('#walletFilter').val();
        var sortFilter = $('#sortFilter').val();
        
        console.log('Searching active customers with:', { keyword, dateFrom, dateTo, walletFilter, sortFilter });
        
        // Show loading
        $('#loadingIndicator').show();
        $('#customersTableBody').html('');
        
        // Send AJAX request
        $.ajax({
            url: '{{ route("admin.customers.search.active") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                keyword: keyword,
                date_from: dateFrom,
                date_to: dateTo,
                wallet_filter: walletFilter,
                sort_filter: sortFilter
            },
            success: function(response) {
                console.log('Search response:', response);
                $('#loadingIndicator').hide();
                
                if (response.success && response.data) {
                    displaySearchResults(response.data);
                } else {
                    showNoResults(response.message || 'No active customers found');
                }
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                $('#loadingIndicator').hide();
                
                let errorMessage = 'An error occurred while searching. Please try again.';
                
                if (xhr.status === 404) {
                    errorMessage = 'Route not found. Please define the route admin.customers.search.active';
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                
                // SweetAlert for error
                Swal.fire({
                    icon: 'error',
                    title: 'Search Error',
                    text: errorMessage,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6f42c1',
                    background: '#fff',
                    color: '#212529'
                });
                
                showNoResults();
            }
        });
    }
    
    // ================= 6. DISPLAY SEARCH RESULTS =================
    function displaySearchResults(data) {
        var tbody = $('#customersTableBody');
        tbody.html('');
        
        if (data.length > 0) {
            // Show success message for search
            Swal.fire({
                icon: 'success',
                title: 'Search Complete',
                text: `Found ${data.length} active customer(s)`,
                timer: 1500,
                showConfirmButton: false,
                background: '#fff',
                color: '#212529'
            });
            
            // Create table rows
            $.each(data, function(index, customer) {
                var avatarUrl = customer.avatar || '{{ asset("assets/default-avatar.png") }}';
                var phone = customer.phone ? customer.phone : '---';
                var walletBalance = customer.wallet_balance ? '$' + parseFloat(customer.wallet_balance).toFixed(2) : '$0.00';
                
                var row = '<tr>' +
                    '<td data-column="sl">' + (index + 1) + '</td>' +
                    '<td data-column="avatar">' +
                        '<img src="' + avatarUrl + '" width="40" height="40" style="border-radius: 50%;">' +
                    '</td>' +
                    '<td data-column="name">' + (customer.name || '-') + '</td>' +
                    '<td data-column="email">' + (customer.email || '-') + '</td>' +
                    '<td data-column="phone">' + phone + '</td>' +
                    '<td data-column="active_status">' +
                        '<label class="switch">' +
                            '<input type="checkbox" checked disabled>' +
                            '<span class="slider round"></span>' +
                        '</label>' +
                    '</td>' +
                    '<td data-column="wallet_balance">' + walletBalance + '</td>' +
                '</tr>';
                
                tbody.append(row);
            });
            
        } else {
            showNoResults();
        }
    }
    
    // ================= 7. SHOW NO RESULTS =================
    function showNoResults(message) {
        var tbody = $('#customersTableBody');
        var msg = message || 'No active customers found for the selected criteria';
        
        tbody.html(
            '<tr>' +
                '<td colspan="7" class="text-center text-muted py-4">' +
                    '<i class="fas fa-users fa-2x mb-2"></i>' +
                    '<p>' + msg + '</p>' +
                '</td>' +
            '</tr>'
        );
        
        // Show info message
        Swal.fire({
            icon: 'info',
            title: 'No Results',
            text: msg,
            confirmButtonText: 'OK',
            confirmButtonColor: '#6f42c1',
            background: '#fff',
            color: '#212529'
        });
    }
    
    // ================= 8. CLIENT-SIDE SEARCH (FALLBACK) =================
    function performClientSideSearch() {
        var keyword = $('#keywordSearch').val().toLowerCase();
        var walletFilter = $('#walletFilter').val();
        var sortFilter = $('#sortFilter').val();
        
        var rows = $('#customersTable tbody tr');
        var visibleCount = 0;
        
        rows.each(function() {
            var $row = $(this);
            var nameText = $row.find('td[data-column="name"]').text().toLowerCase();
            var emailText = $row.find('td[data-column="email"]').text().toLowerCase();
            var phoneText = $row.find('td[data-column="phone"]').text().toLowerCase();
            var balanceText = $row.find('td[data-column="wallet_balance"]').text();
            var balance = parseFloat(balanceText.replace('$', '').replace(',', '')) || 0;
            
            var show = true;
            
            // Keyword filter (search in name, email, phone)
            if (keyword && 
                nameText.indexOf(keyword) === -1 && 
                emailText.indexOf(keyword) === -1 && 
                phoneText.indexOf(keyword) === -1) {
                show = false;
            }
            
            // Wallet balance filter
            if (walletFilter === 'high' && balance < 100) {
                show = false;
            } else if (walletFilter === 'medium' && (balance < 50 || balance >= 100)) {
                show = false;
            } else if (walletFilter === 'low' && (balance < 10 || balance >= 50)) {
                show = false;
            } else if (walletFilter === 'zero' && balance > 0) {
                show = false;
            }
            
            if (show) {
                $row.show();
                visibleCount++;
            } else {
                $row.hide();
            }
        });
        
        if (visibleCount === 0) {
            showNoResults();
        } else {
            Swal.fire({
                icon: 'success',
                title: 'Search Complete',
                text: `Found ${visibleCount} active customer(s)`,
                timer: 1500,
                showConfirmButton: false
            });
        }
    }
    
    // ================= 9. EXPORT CSV WITH SWEETALERT =================
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
            Swal.fire({
                icon: 'warning',
                title: 'No Fields Selected',
                text: 'Please select at least one field to export.',
                confirmButtonText: 'OK',
                confirmButtonColor: '#6f42c1',
                background: '#fff',
                color: '#212529'
            });
            return;
        }
        
        // Show SweetAlert confirmation
        Swal.fire({
            title: 'Export Active Customers',
            html: `
                <div class="text-start">
                    <p class="mb-2">You are about to export <strong>${selectedFields.length} field(s)</strong>:</p>
                    <div class="selected-fields-list bg-light p-2 rounded small mb-2" style="max-height: 150px; overflow-y: auto;">
                        ${selectedFields.map(field => `<div class="mb-1"><i class="fas fa-check-circle text-success me-2"></i>${field.label}</div>`).join('')}
                    </div>
                    <p class="text-muted small mb-0">The export will include all active customers matching your current search filters.</p>
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-file-csv me-1"></i> Export CSV',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#6f42c1',
            cancelButtonColor: '#6c757d',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve, reject) => {
                    // Get current search parameters
                    var keyword = $('#keywordSearch').val();
                    var dateFrom = $('#dateFrom').val();
                    var dateTo = $('#dateTo').val();
                    var walletFilter = $('#walletFilter').val();
                    var sortFilter = $('#sortFilter').val();
                    
                    // Get data for export
                    $.ajax({
                        url: '{{ route("admin.customers.search.active") }}',
                        type: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            keyword: keyword,
                            date_from: dateFrom,
                            date_to: dateTo,
                            wallet_filter: walletFilter,
                            sort_filter: sortFilter,
                            export: true
                        },
                        success: function(response) {
                            if (response.success && response.data) {
                                exportToCSV(response.data, selectedFields);
                                resolve();
                            } else {
                                reject('No data available to export.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Export error:', error);
                            let errorMsg = 'Error exporting data. Please try again.';
                            
                            // If AJAX fails, use client-side data for export
                            if (xhr.status === 404 || xhr.status === 500) {
                                try {
                                    var tableData = getTableDataForExport(selectedFields);
                                    if (tableData.length > 0) {
                                        exportToCSV(tableData, selectedFields);
                                        resolve();
                                    } else {
                                        reject('No data available to export.');
                                    }
                                } catch (e) {
                                    reject('Error: ' + e.message);
                                }
                            } else {
                                if (xhr.responseJSON && xhr.responseJSON.message) {
                                    errorMsg = xhr.responseJSON.message;
                                }
                                reject(errorMsg);
                            }
                        }
                    });
                });
            },
            allowOutsideClick: () => !Swal.isLoading(),
            background: '#fff',
            color: '#212529'
        }).then((result) => {
            if (result.isConfirmed) {
                // Success message
                Swal.fire({
                    icon: 'success',
                    title: 'Export Successful!',
                    html: `
                        <div class="text-center">
                            <i class="fas fa-file-csv fa-3x text-success mb-3"></i>
                            <p class="mb-2">Your CSV file has been downloaded.</p>
                            <p class="text-muted small mb-0">File: <code>active_customers_${new Date().toISOString().split('T')[0]}.csv</code></p>
                        </div>
                    `,
                    showConfirmButton: true,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6f42c1',
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#fff',
                    color: '#212529'
                });
            }
        }).catch((error) => {
            if (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Export Failed',
                    text: error,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#6f42c1',
                    background: '#fff',
                    color: '#212529'
                });
            }
        });
    });
    
    // ================= 10. GET TABLE DATA FOR EXPORT (FALLBACK) =================
    function getTableDataForExport(selectedFields) {
        var data = [];
        var visibleRows = $('#customersTableBody tr:visible');
        
        visibleRows.each(function(index) {
            var row = $(this);
            var customer = {
                name: row.find('td[data-column="name"]').text().trim(),
                email: row.find('td[data-column="email"]').text().trim(),
                phone: row.find('td[data-column="phone"]').text().trim(),
                wallet_balance: row.find('td[data-column="wallet_balance"]').text().trim(),
                avatar: row.find('td[data-column="avatar"] img').attr('src') || null,
                is_active: true // Since this is active customers page
            };
            data.push(customer);
        });
        
        return data;
    }
    
    // ================= 11. EXPORT TO CSV FUNCTION =================
    function exportToCSV(data, selectedFields) {
        // Build CSV content with BOM for UTF-8
        var csvContent = '\ufeff'; // UTF-8 BOM
        
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
                    case 'sl':
                        value = index + 1;
                        break;
                    case 'name':
                        value = customer.name || '-';
                        break;
                    case 'email':
                        value = customer.email || '-';
                        break;
                    case 'phone':
                        value = customer.phone || '---';
                        break;
                    case 'avatar':
                        value = customer.avatar ? 'Has Avatar' : 'No Avatar';
                        break;
                    case 'active_status':
                        value = customer.is_active ? 'Active' : 'Inactive';
                        break;
                    case 'wallet_balance':
                        value = customer.wallet_balance || '$0.00';
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
        link.setAttribute('download', 'active_customers_' + new Date().toISOString().split('T')[0] + '.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Clean up
        setTimeout(() => URL.revokeObjectURL(url), 100);
    }
    
    // ================= 12. ADDITIONAL ENHANCEMENTS =================
    
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
    
    // Adjust dropdown position on mobile
    $(window).resize(function() {
        if ($(window).width() <= 768) {
            $('#fieldDropdown').css({
                'width': '100%',
                'left': '0',
                'right': '0'
            });
        } else {
            $('#fieldDropdown').css({
                'width': '',
                'left': '',
                'right': ''
            });
        }
    });
    
    // Check if route exists, if not use client-side search
    $.ajax({
        url: '{{ route("admin.customers.search.active") }}',
        type: 'HEAD',
        error: function() {
            console.log('AJAX route not found, using client-side search');
            $('#applySearch').off('click').on('click', performClientSideSearch);
        }
    });
    
    // Initialize selected count
    updateSelectedCount();
    
    console.log('Active Customers functionality initialized');
});
</script>
@endsection
