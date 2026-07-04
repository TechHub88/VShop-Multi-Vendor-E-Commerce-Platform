<!-- Footer Start -->
<style>
    .content-page .footer{display:block !important;}
    .ai-btn {
        width: auto;
        display: flex !important;
        align-items: center !important;
        font-size: 9px !important;
        font-weight: 700 !important;
        gap: 6px !important;
        white-space: nowrap;
        padding: 8px 18px !important;
        border-radius: 60px !important;
        border: 0 !important;
        background: linear-gradient(180deg, #d7f1ff 0%, #9fd0ff 25%, #7bb8ff 55%, #6aa8ff 75%, #8fd4ff 100%);
        color: #fff !important;
        letter-spacing: 2px;
        line-height: 14px;
        text-transform: uppercase;
        position: fixed;
        bottom: 12px;
        right: 26px;
        z-index: 9999;
        transition: all .3s ease-in-out;
        width: 85px;
        height: 85px;
        padding: 0px !important;
        justify-content: center;
    }
    .ai-btn img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }
    .ai-btn span {
        top: 27px;
        position: absolute;
        left: -58px;
        transform: translateX(-50%);
        font-size: 10px;
        font-weight: 700;
        color: #4f46e5 !important;
        white-space: nowrap;
        opacity: 0; 
        pointer-events: none;
        transition: opacity 0.22s ease, transform 0.22s ease;
        background: linear-gradient(135deg, #eef2ff, #e0e7ff);
        padding: 4px 11px;
        border-radius: 6px;
        border: 1px solid #c7d2fe;
        letter-spacing: 0.2px;
        z-index: 100;
        text-align: center;
    }
    .ai-btn:hover {
        background: linear-gradient(180deg, #8fd4ff 0%, #6aa8ff 25%, #7bb8ff 55%, #9fd0ff 75%, #d7f1ff 100%);
    }
    .ai-btn:hover span {
        opacity: 1;
        transform: translateX(-50%) translateY(2px);
    }

    /* Chat Popup Widget Styles */
    .support-chat-widget {
        position: fixed;
        bottom: 100px;
        right: 26px;
        width: 380px;
        height: 420px;
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        z-index: 10000;
        display: none;
        flex-direction: column;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif;
    }
    .support-chat-widget.active {
        display: flex;
    }
    .chat-widget-header {
        background: linear-gradient(135deg, #1e3a5f 0%, #0d2137 100%);
        color: white;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: move;
        user-select: none;
    }
    .chat-widget-header h6 {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .chat-widget-header .close-widget {
        background: none;
        border: none;
        color: white;
        font-size: 20px;
        cursor: pointer;
        line-height: 1;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        transition: background 0.2s;
    }
    .chat-widget-header .close-widget:hover {
        background: rgba(255,255,255,0.2);
    }
    .chat-layout {
        display: flex;
        flex: 1;
        min-height: 0;
    }
    .seller-list {
        width: 35%;
        border-right: 1px solid #e2e8f0;
        background: #f8fafc;
        overflow-y: auto;
    }
    .seller-item {
        padding: 10px 12px;
        border-bottom: 1px solid #e2e8f0;
        cursor: pointer;
        transition: background 0.2s;
    }
    .seller-item:hover {
        background: #e2e8f0;
    }
    .seller-item.active {
        background: #e0f2fe;
        border-left: 3px solid #3b82f6;
    }
    .seller-name {
        font-weight: 600;
        font-size: 13px;
    }
    .chat-area {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: white;
    }
    .chat-header {
        padding: 10px 12px;
        border-bottom: 1px solid #e2e8f0;
        background: white;
        font-weight: 600;
        font-size: 13px;
    }
    .chat-messages-area {
        flex: 1;
        overflow-y: auto;
        padding: 12px;
        background: #f8fafc;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .chat-message {
        padding: 8px 12px;
        border-radius: 12px;
        max-width: 85%;
        word-break: break-word;
        font-size: 13px;
    }
    .chat-message.sent {
        background: #3b82f6;
        color: white;
        align-self: flex-end;
        border-bottom-right-radius: 2px;
    }
    .chat-message.received {
        background: white;
        border: 1px solid #e2e8f0;
        align-self: flex-start;
        border-bottom-left-radius: 2px;
    }
    .chat-message small {
        font-size: 9px;
        opacity: 0.7;
        display: block;
        margin-top: 4px;
    }
    .chat-input-container {
        padding: 12px;
        border-top: 1px solid #e2e8f0;
        background: white;
    }
    .chat-input-container textarea {
        width: 100%;
        border: 1px solid #cbd5e1;
        border-radius: 12px;
        padding: 8px 12px;
        font-size: 13px;
        resize: vertical;
        font-family: inherit;
    }
    .send-btn {
        margin-top: 8px;
        background: #3b82f6;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        cursor: pointer;
        width: 100%;
    }
    .send-btn:hover {
        background: #2563eb;
    }
    .chat-footer-note {
        font-size: 10px;
        text-align: center;
        padding: 6px;
        background: #f1f5f9;
        color: #64748b;
        border-top: 1px solid #e2e8f0;
    }
    .loading {
        text-align: center;
        padding: 20px;
        color: #64748b;
    }
</style>

<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <button type="button" class="btn btn-outline-primary btn-sm ai-btn" id="openSupportChat">
                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s">
                <span>V-Chat</span>
            </button>
        </div>
    </div>
</footer>

<!-- Floating Support Chat Popup (VAI Chat) -->
<div id="supportChatWidget" class="support-chat-widget">
    <div class="chat-widget-header" id="chatWidgetHeader">
        <h6><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s" style="width: 24px; height: 24px; border-radius: 50%; object-fit: cover; margin-right: 4px;"> Chat with Ashmita </h6>
        <button class="close-widget" id="closeSupportChat">&times;</button>
    </div>
    <div class="chat-layout">
        <!-- Chat Area (Full width) -->
        <div class="chat-area">
            <div class="chat-messages-area" id="chatMessagesArea">
                <div class="chat-message received">
                    Hi this is Ashmita, how can I help you?
                    <small>Just now</small>
                </div>
            </div>
            <div class="chat-input-container">
                <textarea id="chatMessageText" rows="2" placeholder="Type your message..."></textarea>
                <button class="send-btn" id="sendChatMessageBtn">Send Message</button>
            </div>
        </div>
    </div>
    <div class="chat-footer-note">
        Powered by VAI
    </div>
</div>

<script>
    // DOM elements
    const chatWidget = document.getElementById('supportChatWidget');
    const openBtn = document.getElementById('openSupportChat');
    const closeBtn = document.getElementById('closeSupportChat');
    const messagesArea = document.getElementById('chatMessagesArea');
    const messageInput = document.getElementById('chatMessageText');
    const sendBtn = document.getElementById('sendChatMessageBtn');

    // Draggable functionality
    const header = document.getElementById('chatWidgetHeader');
    let isDragging = false, offsetX, offsetY;
    header.addEventListener('mousedown', (e) => {
        if (e.target === header || header.contains(e.target)) {
            isDragging = true;
            offsetX = e.clientX - chatWidget.offsetLeft;
            offsetY = e.clientY - chatWidget.offsetTop;
            chatWidget.style.position = 'fixed';
            chatWidget.style.margin = '0';
            document.body.style.userSelect = 'none';
        }
    });
    document.addEventListener('mousemove', (e) => {
        if (isDragging) {
            chatWidget.style.left = (e.clientX - offsetX) + 'px';
            chatWidget.style.top = (e.clientY - offsetY) + 'px';
            chatWidget.style.right = 'auto';
            chatWidget.style.bottom = 'auto';
        }
    });
    document.addEventListener('mouseup', () => {
        isDragging = false;
        document.body.style.userSelect = '';
    });

    // Send a message
    function sendMessage() {
        const message = messageInput.value.trim();
        if (!message) return;
        
        // Add sent message to UI
        const sentHtml = `
            <div class="chat-message sent">
                ${escapeHtml(message)}
                <small>Just now</small>
            </div>
        `;
        messagesArea.insertAdjacentHTML('beforeend', sentHtml);
        messageInput.value = '';
        messagesArea.scrollTop = messagesArea.scrollHeight;
        
        // Simulate response
        setTimeout(() => {
            const receivedHtml = `
                <div class="chat-message received">
                    I am currently a demo UI. Your message was: "${escapeHtml(message)}"
                    <small>Just now</small>
                </div>
            `;
            messagesArea.insertAdjacentHTML('beforeend', receivedHtml);
            messagesArea.scrollTop = messagesArea.scrollHeight;
        }, 1000);
    }

    // Helper to escape HTML
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Open/Close widget
    openBtn.addEventListener('click', () => {
        chatWidget.classList.add('active');
        messageInput.focus();
        if (!chatWidget.style.left && !chatWidget.style.right) {
            chatWidget.style.right = '26px';
            chatWidget.style.bottom = '100px';
            chatWidget.style.left = 'auto';
            chatWidget.style.top = 'auto';
        }
    });
    
    closeBtn.addEventListener('click', () => {
        chatWidget.classList.remove('active');
    });

    // Send on button click
    sendBtn.addEventListener('click', sendMessage);
    
    // Send on Enter (Shift+Enter for new line)
    messageInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });
</script>
<!-- end Footer -->