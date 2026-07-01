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
                <button class="nav-link active" data-tab="customer-chat">Customer Support <span id="escalationBadge" class="badge bg-danger rounded-pill d-none">!</span></button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="seller-chat">Seller Chat</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="internal">Internal Support</button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-tab="broadcast">Broadcast</button>
            </li>
        </ul>
    </div>

    <!-- Customer Chat Tab -->
    <div id="customer-chat" class="tab-pane active d-flex flex-column h-100">
        <div class="row g-0 h-100">
            <!-- Customer List Sidebar -->
            <div class="col-4 border-end bg-light" style="height: 100%; overflow-y: auto;">
                <div class="p-2">
                    <input type="text" class="form-control form-control-sm" placeholder="Search escalated customers..." id="searchCustomer">
                </div>
                <div id="customerList" class="list-group list-group-flush">
                    <div class="text-center text-muted p-3 small">Escalated buyer chats will appear here in real-time.</div>
                </div>
            </div>
            <!-- Chat Area -->
            <div class="col-8 d-flex flex-column" style="height: 100%;">
                <div class="bg-white p-2 border-bottom">
                    <h6 id="customerChatWithName" class="mb-0">Select an escalated chat</h6>
                </div>
                <div id="customerChatMessages" class="flex-grow-1 overflow-auto p-3 d-flex flex-column" style="background: #f8f9fa; gap: 10px;">
                    <div class="text-center text-muted">Select a customer to start chatting</div>
                </div>
                <div class="p-2 border-top bg-white">
                    <div class="input-group">
                        <textarea id="customerMessageInput" class="form-control" rows="2" placeholder="Type your message to reply..."></textarea>
                        <button class="btn btn-primary" id="customerSendMsgBtn">Reply to Buyer</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seller Chat Tab -->
    <div id="seller-chat" class="tab-pane d-flex flex-column h-100" style="display: none !important;">
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
            document.querySelectorAll('.tab-pane').forEach(pane => {
                pane.style.display = 'none';
                pane.style.setProperty('display', 'none', 'important');
            });
            const targetPane = document.getElementById(tabId);
            targetPane.style.display = 'flex';
            targetPane.style.setProperty('display', 'flex', 'important');
            document.querySelectorAll('[data-tab]').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            if (tabId === 'customer-chat') {
                document.getElementById('escalationBadge').classList.add('d-none');
            }
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

    // ================= CUSTOMER CHAT LOGIC =================
    let currentCustomerId = null;
    let customerSocket = null;
    let adminGlobalSocket = null;

    function initAdminGlobalSocket() {
        const socketUrl = window.location.protocol + '//' + window.location.hostname + ':3000';
        adminGlobalSocket = io(socketUrl);
        
        adminGlobalSocket.on('connect', () => {
            console.log('Joined admin_global room');
            adminGlobalSocket.emit('join', 'admin_global');
        });
        
        adminGlobalSocket.on('escalated_chat', (data) => {
            console.log('Escalated chat received:', data);
            addOrUpdateCustomerSidebar(data);
            
            // Add badge to tab if we are not on customer-chat tab
            if (!document.querySelector('[data-tab="customer-chat"]').classList.contains('active')) {
                document.getElementById('escalationBadge').classList.remove('d-none');
            }
        });
    }

    function addOrUpdateCustomerSidebar(data) {
        const list = document.getElementById('customerList');
        const placeholder = list.querySelector('.text-muted');
        if (placeholder) placeholder.remove();
        
        let item = document.getElementById('customer-item-' + data.customer_id);
        if (!item) {
            const html = `
                <a href="#" class="list-group-item list-group-item-action customer-item" id="customer-item-${data.customer_id}" data-customer-id="${data.customer_id}" data-customer-name="${escapeHtml(data.customer_name)}">
                    <div class="d-flex justify-content-between">
                        <strong>${escapeHtml(data.customer_name)}</strong>
                        <span class="badge bg-danger rounded-pill">New</span>
                    </div>
                    <small class="text-muted text-truncate d-block">${escapeHtml(data.last_message)}</small>
                </a>
            `;
            list.insertAdjacentHTML('afterbegin', html);
            
            document.getElementById('customer-item-' + data.customer_id).addEventListener('click', function(e) {
                e.preventDefault();
                const badge = this.querySelector('.badge');
                if (badge) badge.remove();
                
                document.getElementById('customerChatWithName').innerText = this.dataset.customerName;
                loadCustomerMessages(this.dataset.customerId);
            });
        } else {
            item.querySelector('small').innerText = escapeHtml(data.last_message);
            list.insertBefore(item, list.firstChild);
            
            if (currentCustomerId != data.customer_id) {
                let badge = item.querySelector('.badge');
                if (!badge) {
                    item.querySelector('.d-flex').insertAdjacentHTML('beforeend', '<span class="badge bg-danger rounded-pill">New</span>');
                }
            } else {
                // If currently open, also reload messages to get the new bot reply?
                // Actually the socket will handle new messages if we are connected.
            }
        }
    }

    function connectCustomerSocket(conversationId) {
        if (customerSocket) {
            customerSocket.disconnect();
            customerSocket = null;
        }
        const socketUrl = window.location.protocol + '//' + window.location.hostname + ':3000';
        customerSocket = io(socketUrl);
        customerSocket.on('connect', () => {
            customerSocket.emit('join', 'conversation_' + conversationId);
        });
        customerSocket.on('message', (msg) => {
            if (document.getElementById('customer-msg-' + msg.id)) return;
            appendCustomerMessage(msg);
            const container = document.getElementById('customerChatMessages');
            container.scrollTop = container.scrollHeight;
        });
    }

    function appendCustomerMessage(msg) {
        const container = document.getElementById('customerChatMessages');
        const isAdmin = msg.sender_id == currentUserId && msg.sender_type === 'admin';
        const isBot = msg.sender_type === 'bot';
        
        if (container.querySelector('.text-center.text-muted')) {
            container.innerHTML = '';
        }
        
        let timeStr = msg.created_at ? new Date(msg.created_at).toLocaleTimeString() : 'Just now';
        let nameLabel = isAdmin ? 'You' : (isBot ? 'Ashmita' : 'Buyer');
        let msgHtml = '';

        if (isAdmin) {
            msgHtml = `
                <div style="display: flex; gap: 10px; align-items: flex-end; align-self: flex-end; flex-direction: row-reverse; margin-bottom: 15px;" id="customer-msg-${msg.id}">
                    <div style="background: #004392; color: white; padding: 12px 15px; border-radius: 14px; border-bottom-right-radius: 4px; max-width: 80%; font-size: 14px; text-align: left; box-shadow: 0 2px 8px rgba(0,67,146,0.2);">
                        <div style="font-size: 11px; color: rgba(255,255,255,0.7); margin-bottom: 4px;">${nameLabel} &bull; ${timeStr}</div>
                        ${escapeHtml(msg.message)}
                    </div>
                </div>
            `;
        } else {
            let avatarHtml = '';
            if (isBot) {
                avatarHtml = `<img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s" style="width:100%; height:100%; object-fit:cover;">`;
            } else {
                avatarHtml = `<i class="bi bi-person-fill"></i>`;
            }

            msgHtml = `
                <div style="display: flex; gap: 10px; align-items: flex-end; margin-bottom: 15px;" id="customer-msg-${msg.id}">
                    <div style="width: 32px; height: 32px; border-radius: 50%; background: #004392; color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,67,146,0.3); overflow: hidden;">
                        ${avatarHtml}
                    </div>
                    <div style="background: white; padding: 12px 15px; border-radius: 14px; border-bottom-left-radius: 4px; max-width: 80%; font-size: 14px; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f1f1;">
                        <div style="font-size: 11px; color: #888; margin-bottom: 4px;">${nameLabel} &bull; ${timeStr}</div>
                        ${escapeHtml(msg.message)}
                    </div>
                </div>
            `;
        }

        container.insertAdjacentHTML('beforeend', msgHtml);
    }

    function loadCustomerMessages(customerId) {
        currentCustomerId = customerId;
        fetch(`/chat/admin/customer-messages/${customerId}`)
            .then(res => res.json())
            .then(data => {
                const container = document.getElementById('customerChatMessages');
                container.innerHTML = '';
                if (!data.messages || data.messages.length === 0) {
                    container.innerHTML = '<div class="text-center text-muted">No messages found.</div>';
                } else {
                    data.messages.forEach(msg => appendCustomerMessage(msg));
                }
                container.scrollTop = container.scrollHeight;
                if (data.conversation_id) {
                    connectCustomerSocket(data.conversation_id);
                }
            })
            .catch(err => console.error(err));
    }

    document.getElementById('customerSendMsgBtn').addEventListener('click', function() {
        if (!currentCustomerId) {
            alert('Select a customer first');
            return;
        }
        const message = document.getElementById('customerMessageInput').value.trim();
        if (!message) return;
        fetch('/chat/admin/customer/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ customer_id: currentCustomerId, message: message })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                document.getElementById('customerMessageInput').value = '';
            } else {
                alert('Failed to send');
            }
        })
        .catch(err => alert('Error sending message'));
    });

    initAdminGlobalSocket();
</script>
@endsection
