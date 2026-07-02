@extends('manager.layout')
@section('content')
<div class="dashboard_card">
    <div class="dashboard_card_header d-flex justify-content-between align-items-center">
        <h3>Tickets</h3>
        <a href="{{ route('manager.tickets.create') }}" class="btn btn-primary">New Ticket</a>
    </div>

    <!-- Search Bar -->
    
    <div class="dashboard_card_body table-responsive">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ticket Name</th>
                    <th>Custmer name</th>
                     <th>Custmer email</th>
                     <th>Contact</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Category</th>
                    <th>Desicription</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                <tr>
                    <td>{{ $ticket->id }}</td>
                    <td>{{ \Illuminate\Support\Str::limit($ticket->ticket_title, 50) }}</td>
                    <td>{{ $ticket->full_name }}</td>
                     <td>{{ $ticket->email }}</td>
                      <td>{{ $ticket->phone_number }}</td>
                    
                    <td>{{ $ticket->ticket_status }}</td>
                    <td>{{ $ticket->priority }}</td>
                    <td>{{ $ticket->category }}</td>
                    <td>{{ $ticket->issue_description }}</td>
                    <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                    <td>
                        <!-- Edit Button -->
                        <a href="{{ route('manager.tickets.edit', $ticket->id) }}" class="btn btn-warning btn-sm">
                            <i class="fas fa-edit"></i>
                        </a>
                        
                        <!-- Delete Button -->
                        <form action="{{ route('manager.tickets.destroy', $ticket->id) }}" method="POST" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this ticket?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="text-center">No tickets found</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        {{ $tickets->links() }}
    </div>
</div>
@endsection
