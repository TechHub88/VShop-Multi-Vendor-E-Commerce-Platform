@extends('vshop.seller.main')
<style>
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    font-size: 4px;
    width: 35px;
    height: 38px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.bg-primary {
    background-color:#233d78 !important;
}
</style>

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="card mb-3 p-3">
        <div class="d-flex top-back-heading-sec mb-3">
            <button onclick="goBack()" class="nav-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h5>Ticket #{{ $ticket->id }}</h5>
        </div>

        <p><b>Subject:</b> {{ $ticket->subject }}</p>

        <p>
            <b>Status:</b> 
            <span class="badge bg-{{ $ticket->status == 'resolved' ? 'success' : 'warning' }}">
                {{ ucfirst($ticket->status) }}
            </span>

            <b class="ms-3">Priority:</b> 
            <span class="badge bg-danger">
                {{ ucfirst($ticket->priority) }}
            </span>
        </p>
    </div>

    {{-- CHAT BOX --}}
    <div class="card p-3 mb-3" style="height:400px; overflow-y:auto">

        @forelse($messages as $msg)

            @if($msg->sender == 'seller')
                <div class="text-end mb-2">
                    <div class="bg-primary text-white d-inline-block p-2 rounded">
                        {{ $msg->message }}
                    </div>
                </div>
            @elseif($msg->sender == 'admin')
                <div class="text-start mb-2">
                    <div class="bg-success text-white d-inline-block p-2 rounded">
                        Admin: {{ $msg->message }}
                    </div>
                </div>
            @else
                <div class="text-start mb-2">
                    <div class="bg-light border d-inline-block p-2 rounded">
                        Buyer: {{ $msg->message }}
                    </div>
                </div>
            @endif

        @empty
            <p class="text-center text-muted">No messages yet</p>
        @endforelse

    </div>

    {{-- SEND MESSAGE --}}
    <div class="card p-3">

        <form method="POST" action="{{ route('seller.grievance.message') }}">
            @csrf

            <input type="hidden" name="grievance_id" value="{{ $ticket->id }}">

            {{-- HEADING --}}
            <div class="mb-2">
                <small class="text-muted"><b>Write your reply</b></small>
            </div>

            {{-- MESSAGE + BUTTONS --}}
            <div class="d-flex gap-2 align-items-center">

                {{-- INPUT --}}
                <input type="text" 
                       name="message" 
                       id="messageBox"
                       class="form-control" 
                       placeholder="Type message..." 
                       required>

                {{-- STATUS --}}
                <div class="d-flex align-items-center gap-1">
                    <small><b>Status</b></small>
                    <select name="status" class="form-select form-select-sm" style="width:130px;">
                        <option value="open" {{ $ticket->status=='open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $ticket->status=='in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $ticket->status=='resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="close" {{ $ticket->status=='close' ? 'selected' : '' }}>Close</option>
                        <option value="escalated" {{ $ticket->status=='escalated' ? 'selected' : '' }}>Escalate to Admin</option>
                    </select>
                </div>

                {{-- CLEAR / CLOSE --}}
                <button type="button" 
                        class="btn btn-secondary"
                        onclick="closeTicket({{ $ticket->id }})">
                    Clear
                </button>

                {{-- SEND --}}
                <button type="submit" class="btn btn-primary">
                    Send
                </button>

            </div>

        </form>

    </div>

</div>

{{-- HIDDEN FORM --}}
<form id="closeTicketForm" method="POST" action="{{ route('seller.grievance.close') }}">
    @csrf
    <input type="hidden" name="grievance_id" id="close_grievance_id">
</form>

<script>
function closeTicket(id)
{
    // 1. message clear
    document.getElementById('messageBox').value = '';

    // 2. set ticket id
    document.getElementById('close_grievance_id').value = id;

    // 3. submit close form
    document.getElementById('closeTicketForm').submit();
}
</script>

@endsection
