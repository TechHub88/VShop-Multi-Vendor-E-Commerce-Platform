@extends('layouts.app')

@section('content')
<style>
    .support-sidebar {
        height: calc(100vh - 150px);
        overflow-y: auto;
        border-right: 1px solid #e5e7eb;
    }
    .support-main {
        height: calc(100vh - 150px);
        overflow-y: auto;
        padding: 20px;
    }
    .ticket-item {
        padding: 15px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background 0.2s;
    }
    .ticket-item:hover {
        background: #f9fafb;
    }
    .ticket-item.active {
        background: #eef2ff;
        border-left: 3px solid #667eea;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Internal Support Chat</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newTicketModal">
                        <i class="fas fa-plus me-2"></i>New Ticket
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="row">
                        <!-- Support List -->
                        <div class="col-md-4 support-sidebar">
                            <div class="p-3 border-bottom">
                                <input type="text" class="form-control" placeholder="Search tickets..." id="search-tickets">
                                <div class="mt-2">
                                    <select class="form-select form-select-sm" id="filter-department">
                                        <option value="">All Departments</option>
                                        <option value="support">Support</option>
                                        <option value="finance">Finance</option>
                                        <option value="technical">Technical</option>
                                    </select>
                                </div>
                            </div>
                            <div id="support-list">
                                @foreach($supports as $support)
                                <div class="ticket-item" data-ticket-id="{{ $support->id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            <span class="badge bg-{{ $support->priority === 'urgent' ? 'danger' : ($support->priority === 'high' ? 'warning' : 'info') }}">
                                                {{ strtoupper($support->priority) }}
                                            </span>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $support->ticket_number }}</h6>
                                            <small class="text-muted">{{ $support->subject }}</small>
                                        </div>
                                        <span class="badge bg-{{ $support->status === 'open' ? 'success' : ($support->status === 'in_progress' ? 'warning' : 'secondary') }}">
                                            {{ ucfirst($support->status) }}
                                        </span>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Ticket Details -->
                        <div class="col-md-8 support-main" id="ticket-details">
                            <div class="text-center text-muted mt-5">
                                <i class="fas fa-ticket-alt fa-3x mb-3"></i>
                                <p>Select a ticket to view details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Create New Support Ticket</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="new-ticket-form">
                    <div class="mb-3">
                        <label class="form-label">Department</label>
                        <select class="form-select" name="department" required>
                            <option value="">Select department...</option>
                            <option value="support">Support</option>
                            <option value="finance">Finance</option>
                            <option value="technical">Technical</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Priority</label>
                        <select class="form-select" name="priority" required>
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject</label>
                        <input type="text" class="form-control" name="subject" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Create Ticket</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Ticket Selection
document.querySelectorAll('.ticket-item[data-ticket-id]').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.ticket-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        
        const ticketId = this.dataset.ticketId;
        loadTicketDetails(ticketId);
    });
});

async function loadTicketDetails(ticketId) {
    try {
        const response = await fetch(`/chat/internal-support/${ticketId}`);
        const ticket = await response.json();
        
        const detailsHtml = `
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">${ticket.ticket_number}</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Department:</strong> ${ticket.department}</p>
                            <p><strong>Priority:</strong> ${ticket.priority}</p>
                            <p><strong>Status:</strong> ${ticket.status}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Created By:</strong> ${ticket.created_by?.name || 'N/A'}</p>
                            <p><strong>Assigned To:</strong> ${ticket.assigned_to?.name || 'Unassigned'}</p>
                            <p><strong>Created At:</strong> ${new Date(ticket.created_at).toLocaleString()}</p>
                        </div>
                    </div>
                    <hr>
                    <h6>Subject</h6>
                    <p>${ticket.subject}</p>
                    <h6>Description</h6>
                    <p>${ticket.description}</p>
                    
                    <div class="mt-3">
                        <label class="form-label">Update Status</label>
                        <select class="form-select" id="update-status">
                            <option value="open" ${ticket.status === 'open' ? 'selected' : ''}>Open</option>
                            <option value="in_progress" ${ticket.status === 'in_progress' ? 'selected' : ''}>In Progress</option>
                            <option value="resolved" ${ticket.status === 'resolved' ? 'selected' : ''}>Resolved</option>
                            <option value="closed" ${ticket.status === 'closed' ? 'selected' : ''}>Closed</option>
                        </select>
                        <button class="btn btn-primary mt-2" onclick="updateTicket(${ticketId})">Update</button>
                    </div>
                </div>
            </div>
        `;
        
        document.getElementById('ticket-details').innerHTML = detailsHtml;
    } catch (error) {
        console.error('Error loading ticket details:', error);
    }
}

async function updateTicket(ticketId) {
    const status = document.getElementById('update-status').value;
    
    try {
        const response = await fetch(`/chat/internal-support/${ticketId}/update`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status })
        });
        
        const result = await response.json();
        alert('Ticket updated successfully!');
        loadTicketDetails(ticketId);
    } catch (error) {
        console.error('Error updating ticket:', error);
    }
}

// New Ticket Form
document.getElementById('new-ticket-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/chat/internal-support/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        alert('Ticket created successfully!');
        bootstrap.Modal.getInstance(document.getElementById('newTicketModal')).hide();
        this.reset();
        location.reload();
    } catch (error) {
        console.error('Error creating ticket:', error);
    }
});
</script>
@endsection
