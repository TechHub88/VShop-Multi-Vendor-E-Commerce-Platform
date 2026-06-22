@extends('layouts.vertical', ['title' => 'Activity Log'])

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
    .btn-outline-info,
    .btn-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
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

<div class="container-fluid pt-3">

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Activity Log</h4>
                <div>
                    <!--<button class="btn btn-outline-secondary btn-sm me-2" onclick="exportToCSV()">-->
                    <!--    <i class="fas fa-download me-1"></i> Export CSV-->
                    <!--</button>-->
                   
                </div>
            </div>
            
            <!-- Filters -->
            <div class="row mt-3">
                <div class="col-md-2">
                    <select class="form-select" id="filterType">
                        <option value="">All Activity Types</option>
                        <option value="order_placed">Order Placed</option>
                        <option value="status_change">Status Change</option>
                        <option value="cancellation_request">Cancellation</option>
                        <option value="return_request">Return Request</option>
                        <option value="payment">Payment</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterCustomer">
                        <option value="">All Customers</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="date" class="form-control" id="filterDate">
                </div>
                <div class="col-md-3">
                    <div>
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search by order # or description..." id="searchInput">
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <button class="btn reset-btn w-100">Reset</button>
                </div>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Date & Time</th>
                            <th>Customer</th>
                            <th>Order #</th>
                            <th>Activity Type</th>
                            <th>Status Change</th>
                            <th>Description</th>
                            <th>Remarks</th>
                            <th>IP Address</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                            
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="fw-bold">{{ $log->created_at->format('d M, Y') }}</span>
                                    <small class="text-muted">{{ $log->created_at->format('h:i A') }}</small>
                                </div>
                            </td>
                            
                            <!-- Customer Info -->
                            <td>
                                @if($log->customer)
                                    <div class="d-flex align-items-center">
                                       
                                        <div>
                                            <strong>{{ $log->customer->name }}</strong><br>
                                            <small class="text-muted">{{ $log->customer->email }}</small><br>
                                            <small class="text-muted">{{ $log->customer->phone ?? 'N/A' }}</small>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">System / Guest</span>
                                @endif
                            </td>
                            
                            <!-- Order Info -->
                            <td>
                                @if($log->order)
                                    <a href="{{ route('admin.orders.show', $log->order_id) }}" class="fw-bold">
                                        #{{ str_pad($log->order->id, 6, '0', STR_PAD_LEFT) }}
                                    </a>
                                    <br>
                                    <small class="text-muted">
                                        ₹{{ number_format($log->order->total_amount, 2) }}
                                    </small>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            
                            <!-- Activity Type -->
                            <td>
                                @php
                                    $activityTypes = [
                                        'order_placed' => ['label' => 'Order Placed', 'class' => 'bg-primary'],
                                        'status_change' => ['label' => 'Status Change', 'class' => 'bg-info'],
                                        'cancellation_request' => ['label' => 'Cancellation', 'class' => 'bg-danger'],
                                        'return_request' => ['label' => 'Return', 'class' => 'bg-warning'],
                                        'payment' => ['label' => 'Payment', 'class' => 'bg-success'],
                                        'refund' => ['label' => 'Refund', 'class' => 'bg-secondary'],
                                    ];
                                    
                                    $type = $activityTypes[$log->activity_type] ?? ['label' => ucfirst($log->activity_type), 'class' => 'bg-secondary'];
                                @endphp
                                <span class="badge {{ $type['class'] }}">
                                    {{ $type['label'] }}
                                </span>
                            </td>
                            
                            <!-- Status Change -->
                            <td>
                                @if($log->old_status && $log->new_status)
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-light text-dark me-1">
                                            {{ ucfirst($log->old_status) }}
                                        </span>
                                        <i class="fas fa-arrow-right mx-1 text-muted"></i>
                                        <span class="badge bg-success">
                                            {{ ucfirst($log->new_status) }}
                                        </span>
                                    </div>
                                @elseif($log->new_status)
                                    <span class="badge bg-light text-dark">
                                        {{ ucfirst($log->new_status) }}
                                    </span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            
                            <!-- Description -->
                            <td>
                                <div class="text-wrap" style="max-width: 250px;">
                                    {{ $log->description }}
                                </div>
                            </td>
                            
                            <!-- Remarks -->
                            <td>
                                @if($log->remarks)
                                    <div class="text-wrap" style="max-width: 200px;">
                                        <small class="text-muted">{{ $log->remarks }}</small>
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            
                            <!-- IP Address -->
                            <td>
                                <small class="text-muted">
                                    {{ $log->ip_address ?? 'N/A' }}
                                </small>
                            </td>
                            
                            <!-- Actions -->
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#logDetailsModal{{ $log->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    
                                    @if($log->metadata)
                                        <button type="button" class="btn btn-sm btn-outline-secondary" 
                                                onclick="copyToClipboard('{{ addslashes(json_encode($log->metadata, JSON_PRETTY_PRINT)) }}')"
                                                title="Copy Metadata">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    @endif
                                    
                                    <!-- Delete Button with SweetAlert -->
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-btn" 
                                            data-id="{{ $log->id }}"
                                            data-description="{{ Str::limit($log->description, 50) }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Details Modal -->
                                <div class="modal fade" id="logDetailsModal{{ $log->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Activity Log Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row">
                                                    <!-- Basic Info -->
                                                    <div class="col-md-6">
                                                        <h6>Basic Information</h6>
                                                        <table class="table table-sm">
                                                            <tr>
                                                                <td><strong>ID:</strong></td>
                                                                <td>{{ $log->id }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Date:</strong></td>
                                                                <td>{{ $log->created_at->format('d M, Y h:i A') }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>Activity Type:</strong></td>
                                                                <td>{{ ucfirst($log->activity_type) }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>IP Address:</strong></td>
                                                                <td>{{ $log->ip_address ?? 'N/A' }}</td>
                                                            </tr>
                                                            <tr>
                                                                <td><strong>User Agent:</strong></td>
                                                                <td>
                                                                    <small>{{ $log->user_agent ?? 'N/A' }}</small>
                                                                </td>
                                                            </tr>
                                                        </table>
                                                    </div>
                                                    
                                                    <!-- Customer & Order Info -->
                                                    <div class="col-md-6">
                                                        <h6>Customer & Order</h6>
                                                        <table class="table table-sm">
                                                            @if($log->customer)
                                                            <tr>
                                                                <td><strong>Customer:</strong></td>
                                                                <td>
                                                                    {{ $log->customer->name }}<br>
                                                                    <small>{{ $log->customer->email }}</small>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                            
                                                            @if($log->user)
                                                            <tr>
                                                                <td><strong>User:</strong></td>
                                                                <td>{{ $log->user->name ?? 'System' }}</td>
                                                            </tr>
                                                            @endif
                                                            
                                                            @if($log->order)
                                                            <tr>
                                                                <td><strong>Order:</strong></td>
                                                                <td>
                                                                    <a href="{{ route('admin.orders.show', $log->order_id) }}">
                                                                        #{{ str_pad($log->order->id, 6, '0', STR_PAD_LEFT) }}
                                                                    </a>
                                                                    <br>
                                                                    <small>Amount: ₹{{ number_format($log->order->total_amount, 2) }}</small>
                                                                </td>
                                                            </tr>
                                                            @endif
                                                        </table>
                                                    </div>
                                                    
                                                    <!-- Description & Remarks -->
                                                    <div class="col-12 mt-3">
                                                        <h6>Description</h6>
                                                        <div class="card card-body bg-light">
                                                            {{ $log->description }}
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12 mt-3">
                                                        <h6>Remarks</h6>
                                                        <div class="card card-body bg-light">
                                                            {{ $log->remarks ?? 'No remarks' }}
                                                        </div>
                                                    </div>
                                                    
                                                    <!-- Metadata -->
                                                    @if($log->metadata)
                                                    <div class="col-12 mt-3">
                                                        <h6>Metadata</h6>
                                                        <div class="card card-body bg-light">
                                                            <pre class="mb-0" style="max-height: 200px; overflow: auto;"><code>{{ json_encode($log->metadata, JSON_PRETTY_PRINT) }}</code></pre>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-history fa-3x mb-3"></i>
                                    <h5>No activity logs found</h5>
                                    <p>Activities will appear here when orders are created or modified.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
            </div>
        </div>
    </div>

</div>
<!-- Include SweetAlert2 -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// CSRF Token for AJAX requests
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';

// Filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterType = document.getElementById('filterType');
    const filterCustomer = document.getElementById('filterCustomer');
    const filterDate = document.getElementById('filterDate');
    const searchInput = document.getElementById('searchInput');
    const clearFiltersBtn = document.getElementById('clearFilters');
    
    function applyFilters() {
        const params = new URLSearchParams(window.location.search);
        
        if (filterType.value) params.set('type', filterType.value);
        else params.delete('type');
        
        if (filterCustomer.value) params.set('customer', filterCustomer.value);
        else params.delete('customer');
        
        if (filterDate.value) params.set('date', filterDate.value);
        else params.delete('date');
        
        if (searchInput.value) params.set('search', searchInput.value);
        else params.delete('search');
        
        window.location.href = window.location.pathname + '?' + params.toString();
    }
    
    // Clear all filters
    function clearAllFilters() {
        Swal.fire({
            title: 'Clear Filters?',
            text: 'This will reset all filters to default values.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, clear all!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                filterType.value = '';
                filterCustomer.value = '';
                filterDate.value = '';
                searchInput.value = '';
                window.location.href = window.location.pathname;
            }
        });
    }
    
    // Attach clear filters button
    if (clearFiltersBtn) {
        clearFiltersBtn.addEventListener('click', clearAllFilters);
    }
    
    [filterType, filterCustomer, filterDate].forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });
    
    // Debounce search input
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(applyFilters, 500);
    });
    
    // Set current filter values from URL
    const urlParams = new URLSearchParams(window.location.search);
    filterType.value = urlParams.get('type') || '';
    filterCustomer.value = urlParams.get('customer') || '';
    filterDate.value = urlParams.get('date') || '';
    searchInput.value = urlParams.get('search') || '';
});

// Delete function with SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    // Attach event listeners to all delete buttons
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function() {
            const logId = this.getAttribute('data-id');
            const description = this.getAttribute('data-description');
            
            Swal.fire({
                title: 'Delete Activity Log?',
                html: `<div class="text-start">
                        <p>Are you sure you want to delete this activity log?</p>
                        <div class="alert alert-warning p-2 mb-2">
                            <strong>Description:</strong><br>
                            ${description}
                        </div>
                        <p class="text-danger"><strong>This action cannot be undone!</strong></p>
                      </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
                showLoaderOnConfirm: true,
                preConfirm: () => {
                    return fetch(`/admin/activity-logs/${logId}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.json();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Deleted!',
                        text: 'Activity log has been deleted successfully.',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Reload the page to show updated list
                        window.location.reload();
                    });
                }
            });
        });
    });
});

// Alternative simple delete function (fallback)
function deleteLog(logId, description) {
    Swal.fire({
        title: 'Delete Activity Log?',
        text: `Delete "${description}"? This action cannot be undone.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Create a form and submit it
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = `/admin/activity-logs/${logId}`;
            form.style.display = 'none';
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = '_token';
            csrfInput.value = csrfToken;
            
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            
            form.appendChild(csrfInput);
            form.appendChild(methodInput);
            document.body.appendChild(form);
            form.submit();
        }
    });
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({
            title: 'Copied!',
            text: 'Metadata copied to clipboard.',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    }).catch(err => {
        console.error('Failed to copy: ', err);
        Swal.fire({
            title: 'Error!',
            text: 'Failed to copy to clipboard.',
            icon: 'error',
            timer: 2000
        });
    });
}

// Export to CSV
function exportToCSV() {
    Swal.fire({
        title: 'Export to CSV',
        text: 'This will download all activity logs as a CSV file.',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Export',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            const table = document.querySelector('table');
            const rows = table.querySelectorAll('tr');
            let csv = [];
            
            rows.forEach(row => {
                const rowData = [];
                const cells = row.querySelectorAll('td, th');
                
                cells.forEach(cell => {
                    // Remove HTML and get text content
                    let text = cell.textContent.trim().replace(/\s+/g, ' ');
                    // Remove any icons/text from buttons
                    if (cell.querySelector('i.fa-trash, i.fa-eye, i.fa-copy')) {
                        text = '';
                    }
                    // Escape quotes
                    text = text.replace(/"/g, '""');
                    // Wrap in quotes if contains comma
                    if (text.includes(',')) {
                        text = `"${text}"`;
                    }
                    rowData.push(text);
                });
                
                csv.push(rowData.join(','));
            });
            
            const csvContent = csv.join('\n');
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `activity-logs-${new Date().toISOString().split('T')[0]}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            // Show success message
            Swal.fire({
                title: 'Exported!',
                text: 'CSV file downloaded successfully.',
                icon: 'success',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

// Quick clear for search input
function clearSearch() {
    Swal.fire({
        title: 'Clear Search?',
        text: 'This will clear the search input and refresh the page.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, clear it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('searchInput').value = '';
            applyFilters();
        }
    });
}
</script>

<style>
/* Table styling */
.table th {
    background-color: #f8f9fa;
    font-weight: 600;
    border-bottom: 2px solid #dee2e6;
}

.table tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}

/* Badge styling */
.badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
    font-weight: 600;
}

/* Status badge colors */
.bg-success {
    background-color: #28a745 !important;
}
.bg-danger {
    background-color: #dc3545 !important;
}
.bg-warning {
    background-color: #ffc107 !important;
}
.bg-info {
    background-color: #17a2b8 !important;
}
.bg-primary {
    background-color: #007bff !important;
}
.bg-secondary {
    background-color: #6c757d !important;
}

/* Avatar styling */
.rounded-circle {
    object-fit: cover;
}

/* Modal styling */
.modal-lg {
    max-width: 900px;
}

/* Pagination styling */
.pagination {
    justify-content: center;
}

/* Delete button hover effect */
.delete-btn:hover {
    background-color: #dc3545 !important;
    color: white !important;
}

/* SweetAlert customization */
.swal2-popup {
    font-size: 1rem !important;
}

.swal2-title {
    font-size: 1.5rem !important;
}

.swal2-html-container {
    text-align: left !important;
}
</style>
@endsection
