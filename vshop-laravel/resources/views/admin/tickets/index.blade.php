@extends('layouts.vertical', ['title' => 'Help Tickets'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>Help Tickets</h4>
        <a href="{{ route('admin.tickets.create') }}" class="btn btn-primary">+ Create New Ticket</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">

            <!-- Filters -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="new">New</option>
                        <option value="open">Open</option>
                        <option value="pending">Pending</option>
                        <option value="resolved">Resolved</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select id="priorityFilter" class="form-select">
                        <option value="">All Priority</option>
                        <option value="low">Low</option>
                        <option value="medium">Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <input id="searchInput" class="form-control" placeholder="Search by title or ID...">
                </div>

                <div class="col-md-3">
                    <button id="resetFilters" class="btn btn-secondary w-100">Reset Filters</button>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Ticket ID</th>
                            <th>Type</th>
                            <th>Title</th>
                            <th>Customer</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Updated</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($tickets as $key => $ticket)
                        <tr class="ticket-row"
                            data-status="{{ $ticket->ticket_status }}"
                            data-priority="{{ $ticket->priority }}"
                            data-search="{{ strtolower($ticket->ticket_title . ' ' . $ticket->id) }}">

                            <td>{{ $key+1 }}</td>
                            <td><strong>#{{ $ticket->id }}</strong></td>
                            <td>
                                @if($ticket->type == 'product')
                                    <span class="badge bg-info">Product</span>
                                @else
                                    <span class="badge bg-warning text-dark">Travel</span>
                                @endif
                            </td>
                            <td>{{ Str::limit($ticket->ticket_title, 40) }}</td>
                            <td>
                                <div>{{ $ticket->full_name }}</div>
                                <small>{{ $ticket->email }}</small>
                            </td>
                            <td><span class="badge bg-secondary">{{ ucfirst($ticket->category) }}</span></td>
                            <td>
                                @php $p = ['low'=>'success','medium'=>'primary','high'=>'warning','urgent'=>'danger']; @endphp
                                <span class="badge bg-{{ $p[$ticket->priority] }}">{{ ucfirst($ticket->priority) }}</span>
                            </td>
                            <td>
                                @php $s = ['new'=>'primary','open'=>'info','pending'=>'warning','resolved'=>'success','closed'=>'secondary']; @endphp
                                <span class="badge bg-{{ $s[$ticket->ticket_status] }}">{{ ucfirst($ticket->ticket_status) }}</span>
                            </td>
                            <td>{{ $ticket->created_at->format('d M Y h:i A') }}</td>
                            <td>{{ $ticket->updated_at->format('d M Y h:i A') }}</td>
                            <td>
                                <button class="btn btn-info btn-sm viewTicket" data-id="{{ $ticket->id }}" data-bs-toggle="modal" data-bs-target="#ticketModal">View</button>

                                <a href="{{ route('admin.tickets.edit',$ticket->id) }}" class="btn btn-warning btn-sm">Edit</a>
                                <form method="POST" action="{{ route('admin.tickets.destroy',$ticket->id) }}" class="deleteForm" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-danger btn-sm deleteBtn">Del</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="11" class="text-center">No Tickets Found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $tickets->links() }}
            </div>

        </div>
    </div>

</div>

<!-- Single Modal for all tickets -->
<div class="modal fade" id="ticketModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
      <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title">Ticket Details</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div id="ticketData">
                  <!-- Ticket details will be loaded here -->
              </div>
              <hr>
              <!-- Reply Form -->
              <form method="POST" action="{{ route('admin.ticket.reply') }}">
                  @csrf
                  <input type="hidden" id="reply_ticket_id" name="ticket_id">
                  <textarea class="form-control" name="reply" rows="4" required placeholder="Type reply..."></textarea>
                  <button type="submit" class="btn btn-primary mt-3 w-100">Submit Reply</button>
              </form>
          </div>
      </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
// VIEW TICKET - only for loading ticket details dynamically
$(document).on("click", ".viewTicket", function () {
    let id = $(this).data("id");
    
    $.get("/admin/tickets/get/" + id, function(res) {
        $("#ticketData").html(res.html); // ticket details HTML from backend
        $("#reply_ticket_id").val(id);    // set ticket id in hidden input
    });
});

// DELETE CONFIRMATION
$(document).on("click", ".deleteBtn", function() {
    let form = $(this).closest("form");
    Swal.fire({
        title: "Are you sure?",
        text: "This ticket will be permanently deleted!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, Delete!"
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
});

// FILTERING
$(document).ready(function() {
    const statusFilter = $('#statusFilter');
    const priorityFilter = $('#priorityFilter');
    const searchInput = $('#searchInput');
    const resetBtn = $('#resetFilters');
    const rows = $('.ticket-row');

    function filterTickets() {
        const status = statusFilter.val();
        const priority = priorityFilter.val();
        const search = searchInput.val().toLowerCase();

        rows.each(function() {
            const $row = $(this);
            const rowStatus = $row.data('status');
            const rowPriority = $row.data('priority');
            const rowSearch = $row.data('search');

            let show = true;

            if (status && rowStatus !== status) show = false;
            if (priority && rowPriority !== priority) show = false;
            if (search && !rowSearch.includes(search)) show = false;

            $row.toggle(show);
        });
    }

    statusFilter.on('change', filterTickets);
    priorityFilter.on('change', filterTickets);
    searchInput.on('keyup', filterTickets);

    resetBtn.on('click', function() {
        statusFilter.val('');
        priorityFilter.val('');
        searchInput.val('');
        filterTickets();
    });
});
</script>

@endsection
