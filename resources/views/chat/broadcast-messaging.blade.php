@extends('layouts.app')

@section('content')
<style>
    .broadcast-form {
        background: #f9fafb;
        padding: 20px;
        border-radius: 10px;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Broadcast Messaging</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-4">Send Broadcast Message</h5>
                            <form id="broadcast-form">
                                <div class="mb-3">
                                    <label class="form-label">Select Audience</label>
                                    <select class="form-select" name="audience" required>
                                        <option value="">Select audience...</option>
                                        <option value="all_sellers">All Sellers</option>
                                        <option value="active_sellers">Active Sellers</option>
                                        <option value="all_admins">All Admins</option>
                                        <option value="specific_users">Specific Users</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Select Channel</label>
                                    <div class="btn-group w-100" role="group">
                                        <input type="radio" class="btn-check" name="channel" value="email" id="email" checked>
                                        <label class="btn btn-outline-primary" for="email">
                                            <i class="fas fa-envelope me-2"></i>Email
                                        </label>
                                        <input type="radio" class="btn-check" name="channel" value="sms" id="sms">
                                        <label class="btn btn-outline-primary" for="sms">
                                            <i class="fas fa-sms me-2"></i>SMS
                                        </label>
                                        <input type="radio" class="btn-check" name="channel" value="whatsapp" id="whatsapp">
                                        <label class="btn btn-outline-primary" for="whatsapp">
                                            <i class="fab fa-whatsapp me-2"></i>WhatsApp
                                        </label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Message</label>
                                    <textarea class="form-control" name="message" rows="5" maxlength="500" required placeholder="Type your broadcast message..."></textarea>
                                    <small class="text-muted"><span id="char-count">0</span>/500 characters</small>
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Send Broadcast
                                </button>
                            </form>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-4">Broadcast History</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Channel</th>
                                            <th>Audience</th>
                                            <th>Status</th>
                                            <th>Recipients</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($broadcasts as $broadcast)
                                        <tr>
                                            <td>
                                                <i class="fas fa-{{ $broadcast->channel === 'email' ? 'envelope' : ($broadcast->channel === 'sms' ? 'sms' : 'whatsapp') }} me-2"></i>
                                                {{ ucfirst($broadcast->channel) }}
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $broadcast->audience)) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $broadcast->status === 'sent' ? 'success' : ($broadcast->status === 'pending' ? 'warning' : 'danger') }}">
                                                    {{ ucfirst($broadcast->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $broadcast->delivered_count }}/{{ $broadcast->total_recipients }}</td>
                                            <td>{{ $broadcast->created_at->format('M d, Y H:i') }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{ $broadcasts->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Character Count for Broadcast
document.querySelector('textarea[name="message"]').addEventListener('input', function() {
    document.getElementById('char-count').textContent = this.value.length;
});

// Broadcast Form
document.getElementById('broadcast-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/chat/broadcast/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        alert('Broadcast sent successfully!');
        this.reset();
        location.reload();
    } catch (error) {
        console.error('Error sending broadcast:', error);
    }
});
</script>
@endsection
