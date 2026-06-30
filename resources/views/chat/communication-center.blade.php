@extends('layouts.app')

@section('content')
<style>
    .communication-center {
        height: calc(100vh - 100px);
        overflow: hidden;
    }
    .chat-sidebar {
        height: 100%;
        overflow-y: auto;
        border-right: 1px solid #e5e7eb;
    }
    .chat-main {
        height: 100%;
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
    .tab-content {
        display: none;
    }
    .tab-content.active {
        display: block;
    }
    .nav-tab {
        padding: 10px 20px;
        cursor: pointer;
        border-bottom: 2px solid transparent;
        transition: all 0.2s;
    }
    .nav-tab.active {
        border-bottom-color: #667eea;
        color: #667eea;
    }
    .stat-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 20px;
        border-radius: 10px;
    }
    .broadcast-form {
        background: #f9fafb;
        padding: 20px;
        border-radius: 10px;
    }
</style>

<div class="communication-center h-100 d-flex flex-column">
    <!-- Tabs -->
    <div class="bg-white border-bottom px-3 py-2">
        <ul class="nav nav-tabs" id="chatTabs">
            <li class="nav-item">
                <button class="nav-link active" data-tab="seller-chat">Seller Chat</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="internal">Internal Support</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="broadcast">Broadcast</button>
            </li>
        </ul>
    </div>

    <!-- Seller Chat Tab -->
    <div id="seller-chat" class="tab-pane active d-flex flex-column h-100">
        <div class="row g-0 h-100">
            <!-- Seller List Sidebar -->
            <div class="col-4 border-end bg-light" style="height: 100%; overflow-y: auto;">
                <div class="p-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Search sellers..." id="searchSeller">
                </div>
                <div id="sellerList" class="list-group list-group-flush">
                    @php
                        $sellers = \App\Models\User::whereHas('roles', function($q) {
                            $q->where('name', 'seller');
                        })->take(20)->get();
                    @endphp
                    @foreach($sellers as $seller)
                    <a href="#" class="list-group-item list-group-item-action seller-item" data-seller-id="{{ $seller->id }}">
                        <div class="d-flex justify-content-between">
                            <strong>{{ $seller->name }}</strong>
                            <small class="text-muted">online</small>
                        </div>
                        <small class="text-muted">Click to chat</small>
                    </a>
                    @endforeach
                </div>
            </div>
            <!-- Chat Area -->
            <div class="col-8 d-flex flex-column" style="height: 100%;">
                <div class="bg-white p-2 border-bottom">
                    <h6 id="chatWithName" class="mb-0">Select a seller</h6>
                </div>
                <div id="chatMessages" class="flex-grow-1 overflow-auto p-3" style="background: #f8f9fa;">
                    <div class="text-center text-muted">Select a seller to start chatting</div>
                </div>
                <div class="p-2 border-top bg-white">
                    <div class="input-group">
                        <textarea id="messageInput" class="form-control" rows="2" placeholder="Type your message..."></textarea>
                        <button class="btn btn-primary" id="sendMsgBtn">Send</button>
                    </div>
                    <div class="mt-1">
                        <label class="btn btn-sm btn-outline-secondary">
                            <i class="ri-attachment-line"></i> Attach
                            <input type="file" id="fileInput" style="display: none;">
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Internal Support Tab (placeholder) -->
    <div id="internal" class="tab-pane" style="display: none;">
        <div class="p-3">Internal support tickets coming soon...</div>
    </div>

    <!-- Broadcast Tab (placeholder) -->
    <div id="broadcast" class="tab-pane" style="display: none;">
        <div class="p-3">Broadcast messaging coming soon...</div>
    </div>
</div>

<script src="http://127.0.0.1:3000/socket.io/socket.io.js"></script>
<script>
    // Tab switching
    document.querySelectorAll('[data-tab]').forEach(tab => {
        tab.addEventListener('click', function() {
            const tabId = this.dataset.tab;
            document.querySelectorAll('.tab-pane').forEach(pane => pane.style.display = 'none');
            document.getElementById(tabId).style.display = 'flex';
            document.querySelectorAll('[data-tab]').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Load messages for selected seller
    let currentSellerId = null;
    let adminSocket = null;
    let activeConversationId = null;
    const currentUserId = {{ auth()->id() }};

    function connectAdminSocket(conversationId) {
        if (adminSocket) {
            adminSocket.disconnect();
            adminSocket = null;
        }

        activeConversationId = conversationId;
        const socketUrl = window.location.protocol + '//' + window.location.hostname + ':3000';
        adminSocket = io(socketUrl);

        adminSocket.on('connect', () => {
            console.log('Admin connected to socket server');
            adminSocket.emit('join', 'conversation_' + conversationId);
        });

        adminSocket.on('message', (msg) => {
            // Check if message already exists in UI
            if (document.getElementById('msg-' + msg.id)) return;
            
            appendAdminMessage(msg);
            const container = document.getElementById('chatMessages');
            container.scrollTop = container.scrollHeight;
        });

        adminSocket.on('disconnect', () => {
            console.log('Admin disconnected from socket server');
        });
    }

    function appendAdminMessage(msg) {
        const container = document.getElementById('chatMessages');
        const isAdmin = msg.sender_id == currentUserId || msg.sender_type === 'admin';
        
        // Remove empty placeholder if it exists
        if (container.querySelector('.text-muted')) {
            container.innerHTML = '';
        }

        let timeStr = 'Just now';
        if (msg.created_at) {
            timeStr = new Date(msg.created_at).toLocaleTimeString();
        }

        const msgHtml = `
            <div class="d-flex ${isAdmin ? 'justify-content-end' : 'justify-content-start'} mb-2" id="msg-${msg.id}">
                <div class="p-2 rounded ${isAdmin ? 'bg-primary text-white' : 'bg-light'} max-width-75">
                    ${escapeHtml(msg.message)}
                    <div class="small ${isAdmin ? 'text-white-50' : 'text-muted'}">${timeStr}</div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', msgHtml);
    }

    function loadMessages(sellerId) {
        currentSellerId = sellerId;
        fetch(`/chat/admin/messages/${sellerId}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('chatMessages');
                container.innerHTML = '';
                
                if (!data.messages || data.messages.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted">No messages yet. Start the conversation!</div>';
                } else {
                    data.messages.forEach(msg => {
                        appendAdminMessage(msg);
                    });
                }
                container.scrollTop = container.scrollHeight;

                if (data.conversation_id) {
                    connectAdminSocket(data.conversation_id);
                }
            })
            .catch(err => console.error(err));
    }

    document.querySelectorAll('.seller-item').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const sellerId = this.dataset.sellerId;
            const sellerName = this.querySelector('strong').innerText;
            document.getElementById('chatWithName').innerText = sellerName;
            loadMessages(sellerId);
        });
    });

    document.getElementById('sendMsgBtn').addEventListener('click', function() {
        if (!currentSellerId) {
            alert('Select a seller first');
            return;
        }
        const message = document.getElementById('messageInput').value.trim();
        if (!message) return;
        fetch('/chat/admin/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ seller_id: currentSellerId, message: message })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('messageInput').value = '';
                loadMessages(currentSellerId);
            } else {
                alert('Failed to send');
            }
        })
        .catch(err => alert('Error sending message'));
    });

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
</script>
@endsection
