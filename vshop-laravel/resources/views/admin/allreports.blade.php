@extends('layouts.vertical', ['title' => 'Reports & Analytics'])

@section('content')
<style>
.salesReportSearchArea{padding:10px 15px 15px;}
.container-fluid {
    padding-left: 15px;
    padding-right: 15px;
}
@media(max-width:575px){
.container-fluid {
    padding-left: 5px;
    padding-right: 5px;
}

	
}

.salesReportSearchBox {
    width: 14.28%;
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

<div class="container-fluid py-4a px-4a pt-2 pb-2">

    <div class="row g-3a mb-3">

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="mb-2 text-muted">Total Sales</h6>
                <h4 class="text-success mb-0">₹{{ number_format($total_sales, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="mb-2 text-muted">Paid Sales</h6>
                <h4 class="text-primary mb-0">₹{{ number_format($paid_sales, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="mb-2 text-muted">Unpaid Sales</h6>
                <h4 class="text-danger mb-0">₹{{ number_format($unpaid_sales, 2) }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="mb-2 text-muted">Total Orders</h6>
                <h4 class="mb-0">{{ $total_orders }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="mb-2 text-muted">Total Customers</h6>
                <h4 class="mb-0">{{ $total_customers }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="mb-2 text-muted">Total Products</h6>
                <h4 class="mb-0">{{ $total_products }}</h4>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-4 shadow-sm text-center">
                <h6 class="mb-2 text-muted">Conversion Rate</h6>
                <h4 class="mb-0">{{ $conversion_rate }}%</h4>
            </div>
        </div>

    </div>
    
       <!-- Recent Orders with Search and Filter -->
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header" style="padding:15px 15px;background-color: #fff;">
                    <h6 class="mb-0">Recent Orders</h6>
                </div>
                <div class="card-body" style="padding:15px 15px;">
                    <!-- Search and Filter Section -->
                    <div class="filter-area mb-3 p-41 bg-light1 rounded salesReportSearchArea">
                        <div class="d-flex flex-wrap flex-nowrap_1 gap-1 align-items-center salesReportSearchAreainner">
                            <div class="flex-shrink-0 salesReportSearchBox">
                                <label class="form-label small text-muted mb-1">Search keyword</label>
                                <input type="text" id="keywordSearch" class="form-control form-control-sm" 
                                       placeholder="Search keyword...">
                            </div>
                            <div class="flex-shrink-0 salesReportSearchBox">
                                <label class="form-label small text-muted mb-1">From</label>
                                <input type="date" id="dateFrom" class="form-control form-control-sm">
                            </div>
                            <div class="flex-shrink-0 salesReportSearchBox">
                                <label class="form-label small text-muted mb-1">To</label>
                                <input type="date" id="dateTo" class="form-control form-control-sm">
                            </div>
                            <div class="flex-shrink-0 salesReportSearchBox">
                                <label class="form-label small text-muted mb-1">Status</label>
                                <select id="statusFilter" class="form-select form-select-sm">
                                    <option value="">All Status</option>
                                    <option value="pending">Pending</option>
                                    <option value="processing">Processing</option>
                                    <option value="completed">Completed</option>
                                    <option value="cancelled">Cancelled</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                </select>
                            </div>
                            <div class="flex-shrink-0 salesReportSearchBox">
                                <button type="button" id="applySearch" class="btn btn-success btn-sm w-100 searchBtn" style="margin-top: 25px;">
                                    <i class="fas fa-search me-1"></i> Search
                                </button>
                            </div>
							<div class="flex-shrink-0 salesReportSearchBox" style="width: 1px; height: 24px; background-color: #dee2e6;margin-top: 25px;"></div>
							                   
                        <div class="salesReportSearchBtns">                           
                            <div class="dropdown-container fieldDropdown">
                                <button type="button" id="fieldDropdownBtn" class="btn btn-outline-primary btn-sm fieldDropdownBtn" style="background-color:#fff;border-color: #6f42c1; color: #6f42c1;margin-top: 25px;">
                                    Select Fields <i class="fas fa-chevron-down ms-1"></i>
                                    <span id="selectedCount" class="badge bg-primary ms-2">5</span>
                                </button>
                                <div id="fieldDropdown" class="dropdown-menu">
                                    <div class="dropdown-title">Select Fields to Export</div>
                                    <div class="dropdown-list">
                                        <label>
                                            <input type="checkbox" class="field-checkbox" value="order_id" data-label="Order ID" checked>
                                            Order ID
                                        </label>
                                        <label>
                                            <input type="checkbox" class="field-checkbox" value="customer" data-label="Customer" checked>
                                            Customer
                                        </label>
                                        <label>
                                            <input type="checkbox" class="field-checkbox" value="amount" data-label="Amount" checked>
                                            Amount
                                        </label>
                                        <label>
                                            <input type="checkbox" class="field-checkbox" value="status" data-label="Status" checked>
                                            Status
                                        </label>
                                        <label>
                                            <input type="checkbox" class="field-checkbox" value="date" data-label="Date" checked>
                                            Date
                                        </label>
                                    </div>
                                    <div class="dropdown-footer">
                                        <button type="button" id="selectAllFields" class="btn btn-sm btn-link p-0 me-2">Select All</button>
                                        <button type="button" id="deselectAllFields" class="btn btn-sm btn-link p-0">Deselect All</button>
                                    </div>
                                </div>
                            </div>

                            <!-- Export CSV Button -->
                            <button type="button" id="exportCsvBtn" class="btn btn-outline-primary btn-sm exportCsvBtn" style="background-color:#fff;border-color: #6f42c1; color: #6f42c1;margin-top: 25px;">
                                <i class="fas fa-file-csv me-1"></i> Export CSV
                            </button>
                            
                            <button class="btn reset-btn btn-sm" style="margin-top: 25px;">Reset</button>
                        </div>
                  
							
							
							
							
                        </div>
                    </div>

                    

                    <!-- Loading Indicator -->
                    <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading orders...</p>
                    </div>

                    <!-- Orders Table -->
                    <div class="table-responsive mt-3">
                        <table id="ordersTable" class="table table-bordered table-hover" style="width:100%;">
                            <thead class="table-light">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="ordersTableBody">
                                @foreach($latest_orders as $order)
                                <tr>
                                    <td><strong>#{{ $order->id }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-2">
                                                <div class="avatar-title bg-light rounded">
                                                    {{ substr($order->customer->name ?? 'N/A', 0, 1) }}
                                                </div>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ $order->customer->name ?? 'N/A' }}</h6>
                                                <small class="text-muted">{{ $order->customer->email ?? '' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-success">₹{{ number_format($order->total_amount, 2) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'pending' => 'warning',
                                                'processing' => 'info',
                                                'completed' => 'success',
                                                'cancelled' => 'danger',
                                                'shipped' => 'primary',
                                                'delivered' => 'success'
                                            ];
                                            $color = $statusColors[$order->status] ?? 'secondary';
                                        @endphp
                                        <span class="badge bg-{{ $color }}">{{ ucfirst($order->status) }}</span>
                                    </td>
                                    <td>
                                        {{ $order->created_at->format('M d, Y') }}
                                        <br>
                                        <small class="text-muted">{{ $order->created_at->format('h:i A') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group1 btn-group-sm1">
                                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                                               class="btn btn-outline-primary" style="padding: 3px 8px;" title="View Order">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.orders.edit', $order->id) }}" 
                                               class="btn btn-outline-info" style="padding: 3px 8px;" title="Edit Order">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                
                                @if($latest_orders->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center text-muted py-4">
                                        <i class="fas fa-inbox fa-2x mb-2"></i>
                                        <p>No orders found</p>
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                    
                
                </div>
            </div>
        </div>



    <!-- ================== MAIN CHART ================== -->
    <div class="card shadow-sm mb-4">
        <div class="card-header d-flex justify-content-between align-items-center" style="padding:15px 15px;background-color: #fff;">
            <h5 class="mb-0">Sales Report</h5>
            <button type="button" id="alertButton" class="btn btn-primary">
                <i class="fas fa-bell me-1"></i> Show Alert
            </button>
        </div>
        <div class="card-body " style="padding:15px 15px;">
            <div id="salesChart" style="height:300px"></div>
        </div>
    </div>


    <div class="row g-4">

        <!-- Customers Chart -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header" style="padding:15px 15px;background-color: #fff;"><h6 class="mb-0">Customers</h6></div>
                <div class="card-body" style="padding:15px 15px;"><div id="customersChart" style="height:200px"></div></div>
            </div>
        </div>

        <!-- Orders Chart -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header" style="padding:15px 15px;background-color: #fff;"><h6 class="mb-0">Orders</h6></div>
                <div class="card-body" style="padding:15px 15px;"><div id="ordersChart" style="height:200px"></div></div>
            </div>
        </div>

        <!-- Customer Retention -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header" style="padding:15px 15px;background-color: #fff;"><h6 class="mb-0">Customer Retention</h6></div>
                <div class="card-bod" style="padding:15px 15px;"><div id="retentionChart" style="height:220px"></div></div>
            </div>
        </div>

        <!-- Product Categories -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header" style="padding:15px 15px;background-color: #fff;"><h6 class="mb-0">Product Categories</h6></div>
                <div class="card-body" style="padding:15px 15px;"><div id="productChart" style="height:220px"></div></div>
            </div>
        </div>

        <!-- Payment Methods -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header" style="padding:15px 15px;background-color: #fff;"><h6 class="mb-0">Payment Methods</h6></div>
                <div class="card-body" style="padding:15px 15px;"><div id="paymentChart" style="height:220px"></div></div>
            </div>
        </div>

        <!-- Shipping Methods -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header" style="padding:15px 15px;background-color: #fff;"><h6 class="mb-0">Shipping Methods</h6></div>
                <div class="card-body" style="padding:15px 15px;"><div id="shippingChart" style="height:220px"></div></div>
            </div>
        </div>
        
     
    </div>

</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<style>
/* Enhanced spacing and padding */


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

/* Simple styling */
.dropdown-container { position: relative; display: inline-block; }
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 260px;
    background: white;
    border: 1px solid #ddd;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 8px;
    z-index: 1000;
    padding: 10px;
}
.dropdown-title {
    font-weight: bold;
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
}
.dropdown-list label {
    display: block;
    margin-bottom: 6px;
    cursor: pointer;
}
.dropdown-footer {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #eee;
    text-align: right;
}
.avatar-sm {
    width: 32px;
    height: 32px;
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
.dropdown-container {
    position: relative;
    display: inline-block;
}
.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    width: 260px;
    background: white;
    border: 1px solid #ddd;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 8px;
    z-index: 1000;
    padding: 15px;
    margin-top: 5px;
}
.dropdown-title {
    font-weight: bold;
    margin-bottom: 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #eee;
    font-size: 14px;
}
.dropdown-list label {
    display: block;
    margin-bottom: 8px;
    cursor: pointer;
    font-size: 14px;
    padding: 4px 0;
}
.dropdown-list label:hover {
    background-color: #f8f9fa;
    padding-left: 5px;
    border-radius: 4px;
}
.dropdown-list input[type="checkbox"] {
    margin-right: 8px;
    cursor: pointer;
}
.dropdown-footer {
    margin-top: 12px;
    padding-top: 12px;
    border-top: 1px solid #eee;
    display: flex;
    justify-content: space-between;
}
.btn-outline-primary[style*="border-color: #6f42c1"]:hover {
    background-color: #6f42c1;
    color: white;
    border-color: #6f42c1;
}

/* Additional spacing improvements */
.filter-area .form-label {
    font-weight: 500;
    color: #495057;
}

.card-header h5, .card-header h6 {
    color: #212529;
}

#summarySection .card {
    border: 1px solid #e9ecef;
    transition: transform 0.2s, box-shadow 0.2s;
}

#summarySection .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}
</style>

<!-- Load ApexCharts FIRST (doesn't depend on jQuery) -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<!-- Load jsPDF (doesn't depend on jQuery) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

<!-- 1. jQuery FIRST -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<!-- Button Alert Handler -->
<script>
$(document).ready(function() {
    $('#alertButton').click(function() {
        alert('This is an alert message triggered by jQuery!');
    });
    
    // Update selected fields count
    function updateSelectedCount() {
        var count = $('.field-checkbox:checked').length;
        $('#selectedCount').text(count);
        if (count === 0) {
            $('#selectedCount').removeClass('bg-primary').addClass('bg-danger');
        } else {
            $('#selectedCount').removeClass('bg-danger').addClass('bg-primary');
        }
    }

    // Initialize field selection
    function initFieldSelection() {
        // Update count on checkbox change
        $('.field-checkbox').on('change', function() {
            updateSelectedCount();
        });

        // Select All
        $('#selectAllFields').click(function() {
            $('.field-checkbox').prop('checked', true);
            updateSelectedCount();
        });

        // Deselect All
        $('#deselectAllFields').click(function() {
            $('.field-checkbox').prop('checked', false);
            updateSelectedCount();
        });

        // Dropdown toggle
        $('#fieldDropdownBtn').click(function(e) {
            e.preventDefault();
            e.stopPropagation();
            $('#fieldDropdown').toggle();
        });

        // Close dropdown when clicking outside
        $(document).click(function(e) {
            if (!$(e.target).closest('.dropdown-container').length) {
                $('#fieldDropdown').hide();
            }
        });

        // Initialize count
        updateSelectedCount();
    }

    // Get selected fields
    function getSelectedFields() {
        var selectedFields = [];
        $('.field-checkbox:checked').each(function() {
            selectedFields.push({
                value: $(this).val(),
                label: $(this).data('label')
            });
        });
        return selectedFields;
    }

    // Export CSV function
    function exportToCSV() {
        var selectedFields = getSelectedFields();
        
        if (selectedFields.length === 0) {
            alert('Please select at least one field to export.');
            return;
        }

        // Get current filter values
        var keyword = $('#keywordSearch').val();
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var status = $('#statusFilter').val();

        // Show loading
        var originalText = $('#exportCsvBtn').html();
        $('#exportCsvBtn').html('<i class="fas fa-spinner fa-spin me-1"></i> Exporting...').prop('disabled', true);

        // Get all filtered orders
        $.ajax({
            url: '{{ route("admin.reports.searchOrders") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                keyword: keyword,
                date_from: dateFrom,
                date_to: dateTo,
                status: status,
                length: 10000, // Get all results
                start: 0
            },
            success: function(response) {
                if (!response.data || response.data.length === 0) {
                    alert('No data to export.');
                    $('#exportCsvBtn').html(originalText).prop('disabled', false);
                    return;
                }

                // Build CSV content
                var csvContent = '';
                
                // Headers
                var headers = selectedFields.map(function(field) {
                    return field.label;
                });
                csvContent += headers.join(',') + '\n';

                // Data rows
                $.each(response.data, function(index, order) {
                    var row = [];
                    $.each(selectedFields, function(i, field) {
                        var value = '';
                        switch(field.value) {
                            case 'order_id':
                                value = order.id;
                                break;
                            case 'customer':
                                value = (order.customer || 'N/A');
                                break;
                            case 'amount':
                                // Remove currency symbol and commas, keep just the number
                                value = order.amount.replace('₹', '').replace(/,/g, '').trim();
                                break;
                            case 'status':
                                value = (order.status || 'N/A');
                                break;
                            case 'date':
                                value = order.date + (order.time ? ' ' + order.time : '');
                                break;
                        }
                        // Escape commas, quotes, and newlines in CSV
                        if (value.toString().includes(',') || value.toString().includes('"') || value.toString().includes('\n')) {
                            value = '"' + value.toString().replace(/"/g, '""') + '"';
                        }
                        row.push(value);
                    });
                    csvContent += row.join(',') + '\n';
                });

                // Create and download CSV file
                var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
                var link = document.createElement('a');
                var url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', 'orders_export_' + new Date().toISOString().split('T')[0] + '.csv');
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);

                $('#exportCsvBtn').html(originalText).prop('disabled', false);
                alert('CSV exported successfully!');
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);
                alert('An error occurred while exporting CSV. Please try again.');
                $('#exportCsvBtn').html(originalText).prop('disabled', false);
            }
        });
    }

    // Initialize field selection on page load
    initFieldSelection();

    // Export CSV button click
    $('#exportCsvBtn').click(function() {
        exportToCSV();
    });

    // AJAX Search and Filter Functionality
    function searchOrders() {
        var keyword = $('#keywordSearch').val();
        var dateFrom = $('#dateFrom').val();
        var dateTo = $('#dateTo').val();
        var status = $('#statusFilter').val();
        
        // Show loading indicator
        $('#loadingIndicator').show();
        $('#ordersTableBody').html('');
        
        $.ajax({
            url: '{{ route("admin.reports.searchOrders") }}',
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                keyword: keyword,
                date_from: dateFrom,
                date_to: dateTo,
                status: status,
                length: 1000, // Get all filtered results
                start: 0
            },
            success: function(response) {
                $('#loadingIndicator').hide();
                
                var tbody = $('#ordersTableBody');
                tbody.html('');
                
                if (response.data && response.data.length > 0) {
                    var totalAmount = 0;
                    var ordersCount = response.data.length;
                    
                    $.each(response.data, function(index, order) {
                        // Parse amount correctly (remove ₹ and commas)
                        var amountValue = parseFloat(order.amount.replace(/₹/g, '').replace(/,/g, '').trim());
                        totalAmount += amountValue;
                        
                        var statusColors = {
                            'pending': 'warning',
                            'processing': 'info',
                            'completed': 'success',
                            'cancelled': 'danger',
                            'shipped': 'primary',
                            'delivered': 'success'
                        };
                        var statusLower = (order.status || '').toLowerCase();
                        var color = statusColors[statusLower] || 'secondary';
                        var statusText = (order.status || 'N/A').charAt(0).toUpperCase() + (order.status || 'N/A').slice(1);
                        var statusBadge = '<span class="badge bg-' + color + '">' + statusText + '</span>';
                        
                        var customerInitial = order.customer ? order.customer.charAt(0).toUpperCase() : 'N';
                        var customerEmail = order.customer_email || '';
                        
                        // Use formatted date from server, or format if not available
                        var formattedDate = order.date || '';
                        var formattedTime = order.time || '';
                        var dateDisplay = formattedDate;
                        if (formattedTime) {
                            dateDisplay = formattedDate + '<br><small class="text-muted">' + formattedTime + '</small>';
                        }
                        
                        var row = '<tr>' +
                            '<td><strong>#' + order.id + '</strong></td>' +
                            '<td>' +
                                '<div class="d-flex align-items-center">' +
                                    '<div class="avatar-sm me-2">' +
                                        '<div class="avatar-title bg-light rounded">' + customerInitial + '</div>' +
                                    '</div>' +
                                    '<div>' +
                                        '<h6 class="mb-0">' + (order.customer || 'N/A') + '</h6>' +
                                        (customerEmail ? '<small class="text-muted">' + customerEmail + '</small>' : '') +
                                    '</div>' +
                                '</div>' +
                            '</td>' +
                            '<td class="text-end"><span class="fw-bold text-success">' + order.amount + '</span></td>' +
                            '<td>' + statusBadge + '</td>' +
                            '<td>' + dateDisplay + '</td>' +
                            '<td>' +
                                '<div class="btn-group btn-group-sm">' +
                                    '<a href="/admin/orders/' + order.id + '" class="btn btn-outline-primary" title="View Order">' +
                                        '<i class="fas fa-eye"></i>' +
                                    '</a>' +
                                    '<a href="/admin/orders/' + order.id + '/manage" class="btn btn-outline-info" title="Edit Order">' +
                                        '<i class="fas fa-edit"></i>' +
                                    '</a>' +
                                '</div>' +
                            '</td>' +
                        '</tr>';
                        
                        tbody.append(row);
                    });
                    
                    // Update summary
                    var averageOrder = ordersCount > 0 ? totalAmount / ordersCount : 0;
                    $('#totalAmount').text('₹' + totalAmount.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                    $('#ordersCount').text(ordersCount);
                    $('#averageOrder').text('₹' + averageOrder.toLocaleString('en-IN', {minimumFractionDigits: 2, maximumFractionDigits: 2}));
                } else {
                    tbody.html('<tr><td colspan="6" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2"></i><p>No orders found</p></td></tr>');
                    $('#totalAmount').text('₹0.00');
                    $('#ordersCount').text('0');
                    $('#averageOrder').text('₹0.00');
                }
            },
            error: function(xhr, status, error) {
                $('#loadingIndicator').hide();
                console.error('Error:', error);
                alert('An error occurred while searching orders. Please try again.');
            }
        });
    }
    
    // Search button click
    $('#applySearch').click(function() {
        searchOrders();
    });
    
    // Enter key in search field
    $('#keywordSearch').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            searchOrders();
        }
    });
});
</script>

<!-- IMPORTANT: DO NOT load jQuery here if it's already in your layout -->
<!-- Debug script -->
<script>
console.log("=== DEBUG INFO ===");
console.log("jQuery loaded:", typeof jQuery !== 'undefined');
console.log("jQuery version:", jQuery?.fn?.jquery || "Not loaded");
console.log("jQuery.noConflict exists:", typeof jQuery.noConflict !== 'undefined');
console.log("$ defined:", typeof $ !== 'undefined');
</script>

<!-- Load DataTables with jQuery compatibility -->
<script>
// Save the original $ reference if it exists
var originalJQuery = window.jQuery;
var original$ = window.$;

// Load DataTables
(function(d, script) {
    script = d.createElement('script');
    script.type = 'text/javascript';
    script.async = true;
    script.onload = function(){
        // DataTables loaded
        console.log("DataTables loaded successfully");
        
    };
    script.src = 'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js';
    d.getElementsByTagName('head')[0].appendChild(script);
}(document));
</script>

<script>


function loadJsPDF() {
    // Load jsPDF
    (function(d, script) {
        script = d.createElement('script');
        script.type = 'text/javascript';
        script.async = true;
        script.onload = function(){
            console.log("jsPDF loaded successfully");
            initAll();
        };
        script.src = 'https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js';
        d.getElementsByTagName('head')[0].appendChild(script);
    }(document));
}

function initAll() {
    console.log("=== INITIALIZING ALL COMPONENTS ===");
    
    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeEverything);
    } else {
        initializeEverything();
    }
}

function initializeEverything() {
    console.log("Starting initialization...");
    
    // ================= INITIALIZE CHARTS =================
    setTimeout(initializeCharts, 500);
}

function initializeCharts() {
    console.log("Initializing charts...");
    
    // Check if ApexCharts is loaded
    if (typeof ApexCharts === 'undefined') {
        console.error("ApexCharts not loaded!");
        return;
    }
    
    try {
        // Sales Chart
        if (document.querySelector("#salesChart")) {
            console.log("Creating sales chart...");
            var salesChart = new ApexCharts(document.querySelector("#salesChart"), {
                chart: { 
                    type: 'line', 
                    height: 300,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Sales',
                    data: @json($sales_chart->pluck('total'))
                }],
                xaxis: {
                    categories: @json($sales_chart->pluck('date'))
                }
            });
            salesChart.render();
        }

        // Customers Chart
        if (document.querySelector("#customersChart")) {
            var customersChart = new ApexCharts(document.querySelector("#customersChart"), {
                chart: { 
                    type: 'area', 
                    height: 200,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Customers',
                    data: @json($customers_chart->pluck('total'))
                }],
                xaxis: {
                    categories: @json($customers_chart->pluck('date'))
                }
            });
            customersChart.render();
        }

        // Orders Chart
        if (document.querySelector("#ordersChart")) {
            var ordersChart = new ApexCharts(document.querySelector("#ordersChart"), {
                chart: { 
                    type: 'bar', 
                    height: 200,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Orders',
                    data: @json($orders_chart->pluck('total'))
                }],
                xaxis: {
                    categories: @json($orders_chart->pluck('date'))
                }
            });
            ordersChart.render();
        }

        // Product Chart
        if (document.querySelector("#productChart")) {
            var productChart = new ApexCharts(document.querySelector("#productChart"), {
                chart: { 
                    type: 'line', 
                    height: 220,
                    toolbar: { show: false }
                },
                series: [{
                    name: 'Products Added',
                    data: @json($product_chart->pluck('total'))
                }],
                xaxis: {
                    categories: @json($product_chart->pluck('date'))
                }
            });
            productChart.render();
        }

        // Payment Chart
        if (document.querySelector("#paymentChart")) {
            var paymentChart = new ApexCharts(document.querySelector("#paymentChart"), {
                chart: { 
                    type: 'pie', 
                    height: 220,
                    toolbar: { show: false }
                },
                series: @json($payment_methods->pluck('total')),
                labels: @json($payment_methods->pluck('payment_method'))
            });
            paymentChart.render();
        }

        // Shipping Chart
        if (document.querySelector("#shippingChart")) {
            var shippingChart = new ApexCharts(document.querySelector("#shippingChart"), {
                chart: { 
                    type: 'donut', 
                    height: 220,
                    toolbar: { show: false }
                },
                series: @json($shipping_methods->pluck('total')),
                labels: @json($shipping_methods->pluck('shipping_method'))
            });
            shippingChart.render();
        }

        console.log("All charts initialized successfully!");
        
    } catch (error) {
        console.error("Error initializing charts:", error);
    }
}
</script>
<!-- ApexCharts for graphs -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // ... [Keep your ApexCharts code exactly as it was] ...
    
    // ================= SALES CHART =================
    var salesOptions = {
        chart: { type: 'line', height: 300 },
        series: [{
            name: 'Sales',
            data: @json($sales_chart->pluck('total'))
        }],
        xaxis: {
            categories: @json($sales_chart->pluck('date'))
        }
    };
    new ApexCharts(document.querySelector("#salesChart"), salesOptions).render();

    // ================= CUSTOMERS CHART =================
    var customersOptions = {
        chart: { type: 'area', height: 200 },
        series: [{
            name: 'Customers',
            data: @json($customers_chart->pluck('total'))
        }],
        xaxis: {
            categories: @json($customers_chart->pluck('date'))
        }
    };
    new ApexCharts(document.querySelector("#customersChart"), customersOptions).render();

    // ================= ORDERS CHART =================
    var ordersOptions = {
        chart: { type: 'bar', height: 200 },
        series: [{
            name: 'Orders',
            data: @json($orders_chart->pluck('total'))
        }],
        xaxis: {
            categories: @json($orders_chart->pluck('date'))
        }
    };
    new ApexCharts(document.querySelector("#ordersChart"), ordersOptions).render();

    // ================= PRODUCT CHART =================
    var productOptions = {
        chart: { type: 'line', height: 220 },
        series: [{
            name: 'Products Added',
            data: @json($product_chart->pluck('total'))
        }],
        xaxis: {
            categories: @json($product_chart->pluck('date'))
        }
    };
    new ApexCharts(document.querySelector("#productChart"), productOptions).render();

    // ================= PAYMENT METHOD PIE =================
    var paymentOptions = {
        chart: { type: 'pie', height: 220 },
        series: @json($payment_methods->pluck('total')),
        labels: @json($payment_methods->pluck('payment_method'))
    };
    new ApexCharts(document.querySelector("#paymentChart"), paymentOptions).render();

    // ================= SHIPPING METHOD PIE =================
    var shippingOptions = {
        chart: { type: 'donut', height: 220 },
        series: @json($shipping_methods->pluck('total')),
        labels: @json($shipping_methods->pluck('shipping_method'))
    };
    new ApexCharts(document.querySelector("#shippingChart"), shippingOptions).render();
    
    // ================= TOP SELLING PRODUCTS CHART =================
    var topProductsOptions = {
        chart: { type: 'bar', height: 300 },
        series: [{
            name: 'Units Sold',
            data: @json($top_selling_products->pluck('total_qty'))
        }],
        xaxis: {
            categories: @json($top_selling_products->pluck('product.name'))
        },
        colors: ['#008FFB']
    };
    new ApexCharts(document.querySelector("#topProductsChart"), topProductsOptions).render();

});
</script>

<!-- Add jsPDF for PDF export -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

@endsection
