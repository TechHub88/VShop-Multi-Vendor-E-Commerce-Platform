@extends('layouts.app')

@section('content')
<style>
    .chat-sidebar {
        height: calc(100vh - 150px);
        overflow-y: auto;
        border-right: 1px solid #e5e7eb;
    }
    .chat-main {
        height: calc(100vh - 150px);
        display: flex;
        flex-direction: column;
    }
    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }
    .chat-input {
        border-top: 1px solid #e5e7eb;
        padding: 15px;
    }
    .message-bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 10px;
        margin-bottom: 10px;
    }
    .message-sent {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        margin-left: auto;
    }
    .message-received {
        background: #f3f4f6;
        color: #1f2937;
    }
    .conversation-item {
        padding: 15px;
        border-bottom: 1px solid #e5e7eb;
        cursor: pointer;
        transition: background 0.2s;
    }
    .conversation-item:hover {
        background: #f9fafb;
    }
    .conversation-item.active {
        background: #eef2ff;
        border-left: 3px solid #667eea;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">Seller ↔ Admin Chat</h4>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newConversationModal">
                        <i class="fas fa-plus me-2"></i>New Conversation
                    </button>
                </div>
                <div class="card-body p-0">
                    <div class="row">
                        <!-- Conversation List -->
                        <div class="col-md-4 chat-sidebar">
                            <div class="p-3 border-bottom">
                                <input type="text" class="form-control" placeholder="Search conversations..." id="search-conversations">
                            </div>
                            <div id="conversation-list">
                                @foreach($conversations as $conversation)
                                <div class="conversation-item" data-conversation-id="{{ $conversation->id }}">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <img src="https://ui-avatars.com/api/?name={{ $conversation->seller->name ?? 'Admin' }}&background=random" class="rounded-circle" width="40" height="40">
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $conversation->seller->name ?? 'Admin' }}</h6>
                                            <small class="text-muted">{{ $conversation->messages->first()->message ?? 'No messages yet' }}</small>
                                        </div>
                                        <small class="text-muted">{{ $conversation->last_message_at ? $conversation->last_message_at->diffForHumans() : '' }}</small>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        
                        <!-- Chat Window -->
                        <div class="col-md-8 chat-main">
                            <div class="p-3 border-bottom">
                                <h6 class="mb-0" id="chat-header">Select a conversation</h6>
                            </div>
                            <div class="chat-messages" id="chat-messages">
                                <div class="text-center text-muted mt-5">
                                    <i class="fas fa-comments fa-3x mb-3"></i>
                                    <p>Select a conversation to start chatting</p>
                                </div>
                            </div>
                            <div class="chat-input" id="chat-input-area" style="display: none;">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Type a message..." id="message-input">
                                    <button class="btn btn-primary" id="send-message">
                                        <i class="fas fa-paper-plane"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- New Conversation Modal -->
<div class="modal fade" id="newConversationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Start New Conversation</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="new-conversation-form">
                    <div class="mb-3">
                        <label class="form-label">Select Seller</label>
                        <select class="form-select" name="seller_id" required>
                            <option value="">Select seller...</option>
                            <!-- Add seller options dynamically -->
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subject (Optional)</label>
                        <input type="text" class="form-control" name="subject">
                    </div>
                    <button type="submit" class="btn btn-primary">Start Conversation</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Conversation Selection
document.querySelectorAll('.conversation-item[data-conversation-id]').forEach(item => {
    item.addEventListener('click', function() {
        document.querySelectorAll('.conversation-item').forEach(i => i.classList.remove('active'));
        this.classList.add('active');
        
        const conversationId = this.dataset.conversationId;
        loadConversation(conversationId);
    });
});

async function loadConversation(conversationId) {
    try {
        const response = await fetch(`/chat/conversations/${conversationId}`);
        const conversation = await response.json();
        
        document.getElementById('chat-header').textContent = conversation.seller?.name || conversation.admin?.name || 'Chat';
        document.getElementById('chat-input-area').style.display = 'block';
        
        const messagesHtml = conversation.messages.map(msg => `
            <div class="message-bubble ${msg.sender_id === {{ auth()->id() }} ? 'message-sent' : 'message-received'}">
                <p class="mb-1">${msg.message}</p>
                <small class="${msg.sender_id === {{ auth()->id() }} ? 'text-white-50' : 'text-muted'}">
                    ${new Date(msg.created_at).toLocaleTimeString()}
                </small>
            </div>
        `).join('');
        
        document.getElementById('chat-messages').innerHTML = messagesHtml;
    } catch (error) {
        console.error('Error loading conversation:', error);
    }
}

// Send Message
document.getElementById('send-message').addEventListener('click', async function() {
    const messageInput = document.getElementById('message-input');
    const message = messageInput.value.trim();
    
    if (!message) return;
    
    const conversationId = document.querySelector('.conversation-item.active')?.dataset.conversationId;
    if (!conversationId) return;
    
    try {
        const response = await fetch('/chat/messages/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                conversation_id: conversationId,
                message: message
            })
        });
        
        const result = await response.json();
        messageInput.value = '';
        loadConversation(conversationId);
    } catch (error) {
        console.error('Error sending message:', error);
    }
});

// New Conversation Form
document.getElementById('new-conversation-form').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    try {
        const response = await fetch('/chat/conversations/create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        alert('Conversation created successfully!');
        bootstrap.Modal.getInstance(document.getElementById('newConversationModal')).hide();
        this.reset();
        location.reload();
    } catch (error) {
        console.error('Error creating conversation:', error);
    }
});
</script>
@endsection
