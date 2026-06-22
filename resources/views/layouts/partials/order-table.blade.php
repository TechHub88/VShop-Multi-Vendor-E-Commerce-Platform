

<div class="container-fluid py-4">
    <div class="card shadow-sm">
        <div class="card-body">
            
            

            <!-- ORDERS TABLE -->
            <table id="ordersTable" class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th data-column="order_no">ORDER NO.</th>
                        <th data-column="full_name">FULL NAME</th>
                        <th data-column="phone">PHONE</th>
                        <th data-column="total_amount">TOTAL AMOUNT</th>
                        <th data-column="share">SHARE</th>
                        <th data-column="seller">SELLER</th>
                        <th data-column="referrer">REFERRER</th>
                        <th data-column="platform">PLATFORM</th>
                        <th data-column="date">DATE</th>
                        <th data-column="payment">PAYMENT</th>
                        <th data-column="status">STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($orders as $order)
                    <tr data-order-id="{{ $order->id }}">
                        <td data-column="order_no">{{ $order->order_no ?? 'FAD' . str_pad($order->id, 8, '0', STR_PAD_LEFT) }}</td>
                        <td data-column="full_name">{{ $order->customer->name ?? 'N/A' }}</td>
                        <td data-column="phone">{{ $order->customer->phone ?? 'N/A' }}</td>
                        <td data-column="total_amount">{{ number_format($order->total_amount, 2) }}</td>
                        <td data-column="date">{{ $order->created_at->format('Y-m-d H:i:s') }}</td>
                        <td data-column="payment">
                            @if($order->payment_status == 'paid')
                                <span class="badge bg-success">Paid</span>
                            @else
                                <span class="badge bg-warning text-dark">Unpaid</span>
                            @endif
                        </td>
                        <td data-column="status">
                            <form class="statusForm" data-id="{{ $order->id }}" action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                                @csrf
                                <select name="prev_status" class="form-select order-status">
                                    <option value="Pending" {{ $order->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Processing" {{ $order->status == 'Processing' ? 'selected' : '' }}>Processing</option>
                                    <option value="Approved" {{ $order->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="Out For Delivery" {{ $order->status == 'Out For Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                                    <option value="Completed" {{ $order->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Refund" {{ $order->status == 'Refund' ? 'selected' : '' }}>Refund</option>
                                    <option value="Declined" {{ $order->status == 'Declined' ? 'selected' : '' }}>Declined</option>
                                    <option value="Cancelled" {{ $order->status == 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                                </select>
                            </form>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    Action
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('admin.orders.edit', $order->id) }}">Manage Order</a></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.orders.invoice', $order->id) }}">Invoice</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item text-danger" href="#">Delete</a></li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ORDER STATUS UPDATE MODAL -->
<div class="modal fade" id="statusModal" tabindex="-1" aria-labelledby="statusModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="statusModalForm" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="statusModalLabel">Update Order Status</h5>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="prev_status" id="prev_status">
                    <div class="mb-3">
                        <label class="form-label">Update Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Select Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Processing">Processing</option>
                            <option value="Confirmed">Confirmed</option>
                            <option value="Shipped">Shipped</option>
                            <option value="Out For Delivery">Out for Delivery</option>
                            <option value="Delivered">Delivered</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Start Date</label>
                                <input type="datetime-local" name="start_date" id="start_date" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">End Date</label>
                                <input type="datetime-local" name="end_date" id="end_date" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea class="form-control" name="remarks" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-block">Privacy</label>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="privacy" id="public" value="public" checked>
                            <label class="form-check-label" for="public">Public</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="privacy" id="private" value="private">
                            <label class="form-check-label" for="private">Private</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- ✅ JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function getLocalDateTime() {
    const now = new Date();
    now.setMinutes(now.getMinutes() - now.getTimezoneOffset());
    return now.toISOString().slice(0, 16);
}

$(function () {
    // ======================================
    // FIX DROPDOWNS POSITION IN TABLE
    // ======================================
    $('body').on('show.bs.dropdown', '.dropdown', function () {
        const $dropdown = $(this).find('.dropdown-menu');
        $('body').append($dropdown.detach());
        const eOffset = $(this).offset();
        $dropdown.css({
            'display': 'block',
            'top': eOffset.top + $(this).outerHeight(),
            'left': eOffset.left
        });
    }).on('hide.bs.dropdown', '.dropdown', function () {
        $(this).append($('.dropdown-menu', this).detach());
    });

    // ======================================
    // FIELD DROPDOWN TOGGLE
    // ======================================
    const fieldDropdownToggle = document.getElementById('fieldDropdownToggle');
    const fieldDropdown = document.getElementById('fieldDropdown');
    
    // Toggle dropdown
    fieldDropdownToggle.addEventListener('click', function(e) {
        e.stopPropagation();
        fieldDropdown.classList.toggle('show');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!fieldDropdown.contains(e.target) && !fieldDropdownToggle.contains(e.target)) {
            fieldDropdown.classList.remove('show');
        }
    });
    
    // Prevent dropdown from closing when clicking inside
    fieldDropdown.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // ======================================
    // SELECT/DESELECT ALL FIELDS
    // ======================================
    document.getElementById('selectAllFields').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.field-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
    });
    
    document.getElementById('deselectAllFields').addEventListener('click', function() {
        const checkboxes = document.querySelectorAll('.field-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
    });

    // ======================================
    // TABLE SEARCH FUNCTIONALITY
    // ======================================
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('#ordersTable tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // ======================================
    // FILTER FUNCTIONALITY
    // ======================================
    function applyFilters() {
        const statusFilter = $('#filterStatus').val().toLowerCase();
        const paymentFilter = $('#filterPayment').val();
        const dateFilter = $('#filterDate').val();
        const today = new Date();
        
        $('#ordersTable tbody tr').each(function() {
            let show = true;
            const $row = $(this);
            
            // Status filter
            if (statusFilter) {
                const status = $row.find('.order-status').val().toLowerCase();
                if (status !== statusFilter) show = false;
            }
            
            // Payment filter
            if (paymentFilter) {
                const paymentText = $row.find('td:nth-child(6)').text().toLowerCase();
                if (paymentFilter === 'paid' && !paymentText.includes('paid')) show = false;
                if (paymentFilter === 'unpaid' && !paymentText.includes('unpaid')) show = false;
            }
            
            // Date filter
            if (dateFilter) {
                const dateText = $row.find('td:nth-child(5)').text();
                const rowDate = new Date(dateText);
                
                switch(dateFilter) {
                    case 'today':
                        if (!isSameDay(rowDate, today)) show = false;
                        break;
                    case 'yesterday':
                        const yesterday = new Date(today);
                        yesterday.setDate(yesterday.getDate() - 1);
                        if (!isSameDay(rowDate, yesterday)) show = false;
                        break;
                    case 'this_week':
                        const weekStart = getWeekStart(today);
                        if (rowDate < weekStart) show = false;
                        break;
                    case 'last_week':
                        const lastWeekStart = new Date(today);
                        lastWeekStart.setDate(lastWeekStart.getDate() - 7 - today.getDay());
                        const lastWeekEnd = new Date(lastWeekStart);
                        lastWeekEnd.setDate(lastWeekStart.getDate() + 6);
                        if (rowDate < lastWeekStart || rowDate > lastWeekEnd) show = false;
                        break;
                    case 'this_month':
                        if (rowDate.getMonth() !== today.getMonth() || 
                            rowDate.getFullYear() !== today.getFullYear()) show = false;
                        break;
                    case 'last_month':
                        const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
                        const nextMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                        if (rowDate < lastMonth || rowDate >= nextMonth) show = false;
                        break;
                    case 'this_year':
                        if (rowDate.getFullYear() !== today.getFullYear()) show = false;
                        break;
                    case 'last_year':
                        if (rowDate.getFullYear() !== today.getFullYear() - 1) show = false;
                        break;
                }
            }
            
            $row.toggle(show);
        });
    }
    
    function isSameDay(date1, date2) {
        return date1.getDate() === date2.getDate() &&
               date1.getMonth() === date2.getMonth() &&
               date1.getFullYear() === date2.getFullYear();
    }
    
    function getWeekStart(date) {
        const d = new Date(date);
        const day = d.getDay();
        const diff = d.getDate() - day + (day === 0 ? -6 : 1);
        return new Date(d.setDate(diff));
    }
    
    // Apply filters on change
    $('#filterStatus, #filterPayment, #filterDate').on('change', applyFilters);

    // ======================================
    // OPEN MODAL ON STATUS CHANGE
    // ======================================
    $(document).on('change', '.order-status', function() {
        const status = $(this).val();
        const form = $(this).closest('form');
        const action = form.attr('action');

        $('#prev_status').val(status);
        $('#status').val(status);
        $('#statusModalForm').attr('action', action);

        const formattedStatus = status.replace(/_/g, ' ').replace(/\b\w/g, c => c.toUpperCase());
        $('#statusModalLabel').text('Update Order Status: ' + formattedStatus);

        const now = getLocalDateTime();
        $('#start_date').val('');
        $('#end_date').val('');

        if (['Processing', 'Approved', 'Shipped', 'Out For Delivery'].includes(status)) {
            $('#start_date').val(now);
        }

        if (['Completed', 'Cancelled', 'Declined', 'Refund'].includes(status)) {
            $('#end_date').val(now);
        }

        const modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('statusModal'));
        modal.show();
    });

    // ======================================
    // SUBMIT MODAL (AJAX)
    // ======================================
    $('#statusModalForm').on('submit', function(e) {
        e.preventDefault();

        if ($('#status').val() === "" || $('#status').val() === null) {
            $('#status').val($('#prev_status').val());
        }

        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: $(this).serialize(),
            success: function() {
                bootstrap.Modal.getInstance(document.getElementById('statusModal')).hide();
                Swal.fire({
                    icon: 'success',
                    title: 'Order status updated successfully!',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                }).then(() => location.reload());
            },
            error: function(xhr) {
                let msg = xhr.responseJSON?.message || 'Something went wrong. Please try again.';
                Swal.fire({
                    icon: 'error',
                    title: 'Failed to update order status',
                    text: msg
                });
            }
        });
    });
});

// ======================================
// CSV EXPORT FUNCTION WITH COLUMN SELECTION
// ======================================
function exportToCSV() {
    // Get selected columns from dropdown
    const selectedColumns = [];
    const checkboxes = document.querySelectorAll('.field-checkbox:checked');
    
    if (checkboxes.length === 0) {
        Swal.fire({
            icon: 'error',
            title: 'No Columns Selected',
            text: 'Please select at least one column to export.',
            confirmButtonText: 'OK'
        });
        return;
    }
    
    checkboxes.forEach(checkbox => {
        selectedColumns.push(checkbox.value);
    });
    
    // Close dropdown
    document.getElementById('fieldDropdown').classList.remove('show');
    
    // Show confirmation
    Swal.fire({
        title: 'Export to CSV',
        text: `Export ${selectedColumns.length} selected column(s)?`,
        icon: 'info',
        showCancelButton: true,
        confirmButtonText: 'Yes, Export',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Get table data
            const table = document.getElementById('ordersTable');
            const rows = table.querySelectorAll('tr');
            const csvData = [];
            
            // Process header row
            const headerRow = [];
            const headerCells = rows[0].querySelectorAll('th');
            
            headerCells.forEach((cell, index) => {
                if (index < headerCells.length - 1) { // Skip ACTION column
                    const columnName = cell.getAttribute('data-column');
                    if (selectedColumns.includes(columnName)) {
                        let cellText = cell.innerText || cell.textContent || '';
                        cellText = cellText.replace(/\n/g, ' ').trim();
                        if (cellText.includes(',') || cellText.includes('"') || cellText.includes('\n')) {
                            cellText = '"' + cellText.replace(/"/g, '""') + '"';
                        }
                        headerRow.push(cellText);
                    }
                }
            });
            
            csvData.push(headerRow.join(','));
            
            // Process data rows
            for (let i = 1; i < rows.length; i++) {
                const row = rows[i];
                if (row.style.display !== 'none') { // Only include visible rows
                    const rowData = [];
                    const cells = row.querySelectorAll('td');
                    
                    cells.forEach((cell, index) => {
                        if (index < cells.length - 1) { // Skip ACTION column
                            const columnName = cell.getAttribute('data-column');
                            if (selectedColumns.includes(columnName)) {
                                let cellText = cell.innerText || cell.textContent || '';
                                
                                // Clean up the text
                                cellText = cellText.replace(/\n/g, ' ').trim();
                                
                                // Check if text contains comma or quote, wrap in quotes
                                if (cellText.includes(',') || cellText.includes('"') || cellText.includes('\n')) {
                                    cellText = '"' + cellText.replace(/"/g, '""') + '"';
                                }
                                
                                rowData.push(cellText);
                            }
                        }
                    });
                    
                    csvData.push(rowData.join(','));
                }
            }
            
            // Create CSV content
            const csvContent = csvData.join('\n');
            
            // Create and trigger download
            const filename = 'inhouse_orders_' + new Date().toISOString().split('T')[0] + '.csv';
            downloadCSV(csvContent, filename);
            
            // Show success message
            Swal.fire({
                icon: 'success',
                title: 'CSV Downloaded!',
                text: 'Your CSV file has been downloaded successfully.',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
}

function downloadCSV(content, filename) {
    // Create a Blob with the CSV content
    const blob = new Blob(['\ufeff' + content], { type: 'text/csv;charset=utf-8;' });
    
    // Create download link
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    
    // Add to document, click, and remove
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    // Release the object URL
    setTimeout(() => URL.revokeObjectURL(url), 100);
}

function printTable() {
    const printContent = document.getElementById('ordersTable').outerHTML;
    
    // Open a new window
    const printWindow = window.open('', '_blank');

    printWindow.document.write(`
        <html>
            <head>
                <title>Inhouse Orders Report</title>
                <style>
                    body { font-family: Arial, sans-serif; margin: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
                    th { background-color: #f2f2f2; font-weight: bold; }
                    .badge { padding: 4px 8px; border-radius: 4px; font-size: 12px; }
                    .bg-success { background-color: #28a745 !important; color: white; }
                    .bg-warning { background-color: #ffc107 !important; color: black; }
                    @media print {
                        @page { size: landscape; margin: 0; }
                        body { margin: 1.6cm; }
                    }
                </style>
            </head>
            <body>
                <h2>Inhouse Orders Report</h2>
                <p><strong>Generated on:</strong> ${new Date().toLocaleString()}</p>
                <p><strong>Total Orders:</strong> ${document.querySelectorAll('#ordersTable tbody tr').length}</p>
                ${printContent}
                <script>
                    window.onload = function() {
                        window.print();
                        setTimeout(() => window.close(), 100);
                    }
                <\/script>
            </body>
        </html>
    `);

    printWindow.document.close();
    printWindow.focus();
}

</script>