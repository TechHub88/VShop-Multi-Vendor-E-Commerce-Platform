<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    @media(max-width: 991px) {
        .dashboard-search-sec {
            margin: 0;
        }
    }
</style>

<style>
    /* parent – kept for compatibility, but submenu now commented out */
    .academics-wrapper {
        position: relative;
    }

    /* RIGHT side panel – now commented out, but CSS kept */
    .academics-panel {
        position: absolute;
        left: 100%;
        bottom: 0%;
        width: 240px;
        height: auto;
        border-radius: 10px;
        background: #fff;
        padding: 0;
        box-shadow: 0px 11px 45px #232F34;
        transform: translateX(-10px);
        opacity: 0;
        visibility: hidden;
        transition: all 0.25s ease;
        z-index: 99999;
    }
    .academics-panel::before {
        content: "";
        position: absolute;
        bottom: 20%;
        left: -9px;
        width: 20px;
        height: 20px;
        background: #fff;
        transform: rotate(45deg);
    }

    /* open state – not used anymore */
    .academics-wrapper.open .academics-panel {
        transform: translateX(0);
        opacity: 1;
        visibility: visible;
    }

    /* list – kept for when uncommented */
    .academics-panel ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .academics-panel li {
        margin-bottom: 14px;
    }

    .academics-panel a {
        color: #374151;
        text-decoration: none;
        font-size: 15px;
        font-weight: 500;
    }

    .academics-panel a.active {
        color: #2563eb;
        font-weight: 600;
    }

    .academics-panel ul li a {
        background: transparent;
        height: auto;
        font-size: 14px;
        font-weight: 600;
        color: #475569;
        align-items: flex-start;
        padding: 8px 21px;
    }

    .academics-panel ul {
        padding: 15px 0;
    }

    .academics-panel ul li {
        margin: 0;
    }

    /* Sidebar main styles */
    .submenu{position:fixed;}
    .dashboardSidebarMenuArea.scrolldiv {
        max-height: 88vh;
        overflow-y: auto;
        cursor: grabbing;
        -webkit-overflow-scrolling: touch;
         -ms-overflow-style: block;
        scrollbar-width: block;
    }

    .dashboardSidebarMenuArea.scrolldiv::-webkit-scrollbar {
      display: block;
    }

    .dashboardSidebarMenuArea::-webkit-scrollbar {
      width: 6px;
      height: 10px;
    }

    .dashboardSidebarMenuArea::-webkit-scrollbar-track {
      background: #fff;
      border-radius: 10px;
    }

    .dashboardSidebarMenuArea::-webkit-scrollbar-thumb {
      background: #8b8b8b;
      border-radius: 10px;
    }

    .dashboardSidebarMenuArea:active{
        cursor: grabbing;
    }    
    .dasboardMenuList li a {
        text-align: center;
    }

    .dashboardSidebarMenuArea {
        overflow: auto;
        height: 80vh;
    }

    .academics-panel {
        position: fixed;
        bottom: 0%;
        top: 110.6493px;
        left: 113.333px;
        height: 254px;
    }

    .dasboardMenuList li a, .dasboardMenuList li button {
        background: #eff8ff;
        color: #004392;
    }

    .dasboardMenuList li a:hover,
    .dasboardMenuList li button:hover {
        background-color: #004392;
        color: #fff;
    }

    .dasboardMenuList li a.active, .dasboardMenuList li button.active {
        background-color: #004392;
        color: #fff;
    }

    .academics-panel ul li a:hover {
        background-color: #fff;
        color: #004392;
    }

    .dashboardSidebar {
        width: 230px !important;
    }
    .dasboardMenuList li a {
        height: auto;
        flex-flow: row;
        padding: 15px 15px;
        gap: 0;
    }
    .dasboardMenuList li a i {
        margin-right: 14px;
    }
    .dasboardMenuList li a {
        border-radius: 10px;
    }

    .academics-panel {
        top: 110.6493px;
        left: 237.333px;
    }
    .academics-panel::before {
        bottom: 55%;
        left: -9px;
    }
    .dashboardSidebar {
        background: hsl(206.25deg 100% 96.86%);
    }
</style>
<aside class="dashboardSidebar">
    <div class="dashboardSidebarinner">
        <div class="dashboardSidebarWrapper">
            <div class="dashboardSidebarMenuArea scrollbar">
                <div class="dashboardSidebarMenu">

                    <ul class="dasboardMenuList">

                        {{-- Dashboard --}}
                        <li>
                            <a href="{{ route('home') }}"
                               class="{{ request()->routeIs('home') ? 'active' : '' }}">
                               <i class="fa-solid fa-house"></i> Home
                            </a>
                        </li>

                        {{-- Academics – submenu panel commented out, link kept as normal --}}
                        <li>
                            <a href="{{ route('productmanage.index', ['category' => 'Academics']) }}"
                               class="{{ request()->get('category')=='Academics' ? 'active' : '' }}">
                               <i class="fa-solid fa-microchip"></i> Academics
                            </a>
                            {{--
                            <div class="academics-panel">
                                <ul>
                                    <li><a href="{{ route('productmanage.index', ['category' => 'books']) }}" class="{{ request()->get('category')=='books' ? 'active' : '' }}">Books</a></li>
                                    <li><a href="{{ route('productmanage.index', ['category' => 'uniform']) }}" class="{{ request()->get('category')=='uniform' ? 'active' : '' }}">Uniform</a></li>
                                    <li><a href="{{ route('productmanage.index', ['category' => 'stationary']) }}" class="{{ request()->get('category')=='stationary' ? 'active' : '' }}">Stationary items</a></li>
                                    <li><a href="{{ route('productmanage.index', ['category' => 'gadgets']) }}" class="{{ request()->get('category')=='gadgets' ? 'active' : '' }}">Gadgets</a></li>
                                    <li><a href="{{ route('productmanage.index', ['category' => 'utensils']) }}" class="{{ request()->get('category')=='utensils' ? 'active' : '' }}">Utensils</a></li>
                                    <li><a href="{{ route('productmanage.index', ['category' => 'sport']) }}" class="{{ request()->get('category')=='sport' ? 'active' : '' }}">Sport Gear</a></li>
                                </ul>
                            </div>
                            --}}
                        </li>

                        {{-- Wellness --}}
                        <li>
                            <a href="{{ route('productmanage.index', ['category' => 'wellness']) }}"
                               class="{{ request()->get('category')=='wellness' ? 'active' : '' }}">
                               <i class="fa-solid fa-microchip"></i> Wellness
                            </a>
                        </li>

                        {{-- Deep Tech – fixed category name --}}
                        <li>
                            <a href="{{ route('productmanage.index', ['category' => 'DeepTech']) }}"
                               class="{{ request()->get('category')=='DeepTech' ? 'active' : '' }}">
                               <i class="fa-solid fa-microchip"></i> Deep Tech
                            </a>
                        </li>

                        {{-- Language Lab – fixed category name --}}
                        <li>
                            <a href="{{ route('productmanage.index', ['category' => 'LanguageLab']) }}"
                               class="{{ request()->get('category')=='LanguageLab' ? 'active' : '' }}">
                               <i class="fa-solid fa-language"></i> Language Lab
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('productmanage.index', ['category' => 'skills']) }}"
                               class="{{ request()->get('category')=='skills' ? 'active' : '' }}">
                                <i class="fa-solid fa-screwdriver-wrench"></i> Skills
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('productmanage.index', ['category' => 'games']) }}"
                               class="{{ request()->get('category')=='games' ? 'active' : '' }}">
                               <i class="fa-solid fa-gamepad"></i> Games
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('productmanage.index', ['category' => 'space']) }}"
                               class="{{ request()->get('category')=='space' ? 'active' : '' }}">
                               <i class="fa-solid fa-rocket"></i> Space
                            </a>
                        </li>

                        <li>
                            <a href="{{ route('productmanage.index', ['category' => 'climate']) }}"
                               class="{{ request()->get('category')=='climate' ? 'active' : '' }}">
                                <i class="fa-solid fa-earth-americas"></i> Climate
                            </a>
                        </li>

                    </ul>

                    <!-- NEW CARDS IN SIDEBAR (Styled to match screenshot) -->
                    <div class="px-3 mt-4 mb-3">

                        <!-- Features Banner (Taller & Clean without header) -->
                        @if(isset($featuredAds) && count($featuredAds) > 0)
                            <div class="sidebar-features-sec mb-3">
                                <div class="featured-wrapper owl-carousel">
                                    @foreach($featuredAds as $banner)
                                        @if($banner->media->count() > 0)
                                            @foreach($banner->media as $media)
                                                @if($media->media_type == 'image')
                                                    <div class="banner-img item shadow-sm" style="border-radius: 14px; overflow: hidden; width: 100%; height: 260px; background: #f8fafc; border: 1px solid #e2e8f0; transition: transform 0.2s ease;">
                                                        <a href="{{ ($banner->redirect_url && $banner->redirect_url !== '#') ? $banner->redirect_url : '#' }}" target="_blank" class="d-block w-100 h-100">
                                                            <img src="{{ asset($media->file_path) }}" class="w-100 h-100" style="object-fit: cover;" alt="{{ $banner->title }}">
                                                        </a>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Need Help Card -->
                        <div class="card border-0 rounded-4 mb-3 position-relative overflow-hidden" style="background-color: #f1f5fb; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center mb-2">
                                    <div class="me-3 position-relative z-2">
                                        <div style="width: 40px; height: 40px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 8px rgba(0,0,0,0.05); overflow: hidden;">
                                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s" style="width:100%; height:100%; object-fit:cover;" />
                                        </div>
                                    </div>
                                    <div class="position-relative z-2">
                                        <h6 class="fw-bold mb-0" style="font-size: 13px; color: #004392;">Need Help?</h6>
                                        <p class="mb-0 fw-semibold" style="font-size: 11px; color: #5a7184;">Chat with Ashmita</p>
                                    </div>
                                </div>
                                <p class="small mb-3 position-relative z-2" style="font-size: 11px; color: #5a7184;">Hi this is Ashmita, how can I help you?</p>
                                <button class="btn bg-white rounded-pill py-1 w-100 fw-bold sidebarChatBtn position-relative z-2" style="font-size: 12px; color: #004392; border: 1px solid rgba(0,67,146,0.15); transition: all 0.2s;">Chat with Us</button>
                                
                                <!-- Decorative element -->
                                <div style="position: absolute; right: -20px; top: -20px; width: 80px; height: 80px; background: rgba(255,255,255,0.4); border-radius: 50%; z-index: 1;"></div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</aside>


{{-- Academics sub‑menu JavaScript is commented out --}}
<script>
    // $(document).ready(function(){
    //     $(".dasboardMenuList li .academics-toggle").on("click", function(e){
    //         e.preventDefault();
    //         e.stopPropagation();
    //         let submenu = $(this).next(".academics-panel");
    //         if(submenu.length){
    //             let pos = this.getBoundingClientRect();
    //             $(".academics-panel").hide();
    //             submenu.css({
    //                 top: pos.top + "px",
    //                 left: pos.right + "px",
    //                 display: "block",
    //                 position: "fixed"
    //             });
    //         }
    //     });
    //     $(".dashboardSidebarinner").mouseleave(function(){
    //         $(".submenu").hide();
    //     });
    // });

    // document.addEventListener("DOMContentLoaded", function () {
    //     const toggle = document.querySelector(".academics-toggle");
    //     const wrapper = document.querySelector(".academics-wrapper");
    //     toggle.addEventListener("click", function (e) {
    //         e.stopPropagation();
    //         wrapper.classList.toggle("open");
    //     });
    //     document.addEventListener("click", function () {
    //         wrapper.classList.remove("open");
    //     });
    // });
</script>

<script src="{{ asset('js/socket.io.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    // Chat with Us button functionality
    const chatBtns = document.querySelectorAll('.sidebarChatBtn');
    chatBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const footerChatBtn = document.getElementById('openSupportChat');
            if (footerChatBtn) {
                footerChatBtn.click();
                return;
            }

            if(typeof Swal !== 'undefined') {
                let sidebarSocket = null;
                let conversationId = null;
                let currentUserId = {{ Auth::guard('customer')->id() ?? Auth::id() ?? 0 }};

                Swal.fire({
                    title: '<div style="text-align: left; font-size: 18px; display: flex; align-items: center; justify-content: space-between; width: 100%;"><div style="display: flex; align-items: center;"><img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; margin-right: 10px;">Chat with Ashmita</div><button id="clearChatBtn" style="font-size:12px; padding:5px 12px; border:1px solid #e0e0e0; background:#f8f9fa; color:#6c757d; border-radius:20px; font-weight:600; cursor:pointer; display:flex; align-items:center; gap:6px; transition:0.2s;" title="Clear Chat History" onmouseover="this.style.background=\'#dc3545\';this.style.color=\'#fff\';this.style.borderColor=\'#dc3545\'" onmouseout="this.style.background=\'#f8f9fa\';this.style.color=\'#6c757d\';this.style.borderColor=\'#e0e0e0\'"><i class="bi bi-eraser-fill"></i> Clear</button></div>',
                    html: `
                        <div id="liveChatWindow" class="scrollbar" style="height: 320px; overflow-y: auto; background: #f8f9fa; border-radius: 12px; padding: 15px; margin-bottom: 15px; text-align: left; box-shadow: inset 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #e9ecef;">
                            <div style="display: flex; flex-direction: column; gap: 12px;" id="liveChatContainer">
                                <div class="text-center text-muted py-5">Loading conversation...</div>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px;">
                            <input type="text" id="liveChatInput" class="form-control rounded-pill px-4" placeholder="Type your message..." style="border: 1px solid #dee2e6; box-shadow: none; font-size: 14px; background: #f8f9fa;">
                            <button id="liveChatSendBtn" class="btn btn-primary rounded-circle" style="width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; background-color: #004392; border: none; box-shadow: 0 4px 10px rgba(0,67,146,0.3);"><i class="bi bi-send-fill fs-5"></i></button>
                        </div>
                    `,
                    showConfirmButton: false,
                    showCloseButton: false,
                    width: '450px',
                    padding: '20px',
                    customClass: {
                        popup: 'rounded-4 border-0 shadow-lg'
                    },
                    didOpen: async () => {
                        const input = document.getElementById('liveChatInput');
                        const sendBtn = document.getElementById('liveChatSendBtn');
                        const chatWindow = document.getElementById('liveChatWindow');
                        const chatContainer = document.getElementById('liveChatContainer');

                        let socketUrl;
                        let socketOptions = {};
                        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
                            socketUrl = 'http://' + window.location.hostname + ':3000';
                        } else {
                            socketUrl = 'https://api.vridhee.com';
                            socketOptions = { secure: true, rejectUnauthorized: false };
                        }
                        sidebarSocket = io(socketUrl, socketOptions);
                        await new Promise((resolve) => {
                            if (sidebarSocket.connected) resolve();
                            else sidebarSocket.on('connect', resolve);
                        });
                        const socketId = sidebarSocket.id;
                        
                        function escapeHtml(text) {
                            if (!text) return '';
                            const div = document.createElement('div');
                            div.textContent = text;
                            return div.innerHTML;
                        }

                        function appendMsg(msg) {
                            const isSent = (msg.sender_id == currentUserId || msg.sender_type === 'seller' || msg.sender_type === 'customer') && msg.sender_type !== 'bot';
                            
                            // Render user sent message on right
                            if (isSent) {
                                chatContainer.innerHTML += `
                                    <div style="display: flex; gap: 10px; align-items: flex-end; align-self: flex-end; flex-direction: row-reverse;" id="msg-${msg.id}">
                                        <div style="background: #004392; color: white; padding: 12px 15px; border-radius: 14px; border-bottom-right-radius: 4px; max-width: 80%; font-size: 14px; text-align: left; box-shadow: 0 2px 8px rgba(0,67,146,0.2);">
                                            ${escapeHtml(msg.message)}
                                        </div>
                                    </div>
                                `;
                            } else {
                                chatContainer.innerHTML += `
                                    <div style="display: flex; gap: 10px; align-items: flex-end;" id="msg-${msg.id}">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #004392; color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,67,146,0.3); overflow: hidden;">
                                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s" style="width:100%; height:100%; object-fit:cover;">
                                        </div>
                                        <div style="background: white; padding: 12px 15px; border-radius: 14px; border-bottom-left-radius: 4px; max-width: 80%; font-size: 14px; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f1f1;">
                                            ${escapeHtml(msg.message)}
                                        </div>
                                    </div>
                                `;
                            }
                        }

                        // Load history
                        try {
                            const res = await fetch('{{ route('chat.ashmita.history') }}?socket_id=' + socketId, {
                                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                            });

                            if (res.status === 401 || res.redirected) {
                                chatContainer.innerHTML = `
                                    <div style="display: flex; gap: 10px; align-items: flex-end;">
                                        <div style="width: 32px; height: 32px; border-radius: 50%; background: #004392; color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,67,146,0.3); overflow: hidden;">
                                            <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s" style="width:100%; height:100%; object-fit:cover;">
                                        </div>
                                        <div style="background: white; padding: 12px 15px; border-radius: 14px; border-bottom-left-radius: 4px; max-width: 80%; font-size: 14px; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f1f1;">
                                            Hi this is Ashmita! Please <a href="{{ route('vshoplogin') }}" style="color:#004392; font-weight:600;">log in</a> to start chatting with me.
                                        </div>
                                    </div>
                                `;
                                return;
                            }

                            if (!res.ok) {
                                throw new Error('Server returned ' + res.status);
                            }

                            const data = await res.json();
                            if (data.success) {
                                conversationId = data.conversation_id;
                                chatContainer.innerHTML = '';
                                
                                if (data.messages && data.messages.length > 0) {
                                    data.messages.forEach(msg => appendMsg(msg));
                                } else {
                                    chatContainer.innerHTML = `
                                        <div style="display: flex; gap: 10px; align-items: flex-end;">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #004392; color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,67,146,0.3); overflow: hidden;">
                                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s" style="width:100%; height:100%; object-fit:cover;">
                                            </div>
                                            <div style="background: white; padding: 12px 15px; border-radius: 14px; border-bottom-left-radius: 4px; max-width: 80%; font-size: 14px; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f1f1;">
                                                Hi this is Ashmita, how can I help you?
                                            </div>
                                        </div>
                                    `;
                                }
                                chatWindow.scrollTop = chatWindow.scrollHeight;

                                // Join room and listen for messages
                                sidebarSocket.emit('join', 'conversation_' + conversationId);
                                sidebarSocket.on('message', (msg) => {
                                    if (document.getElementById('msg-' + msg.id)) return;
                                    appendMsg(msg);
                                    chatWindow.scrollTop = chatWindow.scrollHeight;
                                });
                            } else {
                                throw new Error(data.error || 'Unknown error');
                            }
                        } catch (err) {
                            console.error('Chat history error:', err);
                            chatContainer.innerHTML = '<div class="text-danger p-3">Failed to load chat history. Please try again.</div>';
                        }

                        const sendMessage = async () => {
                            const msg = input.value.trim();
                            if(msg) {
                                input.value = '';
                                try {
                                    const response = await fetch('{{ route('chat.ashmita.message') }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                        },
                                        body: JSON.stringify({ message: msg, socket_id: socketId })
                                    });
                                    
                                    const data = await response.json();
                                    if(data.success) {
                                        if (data.buyer_message && !document.getElementById('msg-' + data.buyer_message.id)) {
                                            appendMsg(data.buyer_message);
                                            chatWindow.scrollTop = chatWindow.scrollHeight;
                                        }
                                        if (data.bot_message && !document.getElementById('msg-' + data.bot_message.id)) {
                                            appendMsg(data.bot_message);
                                            chatWindow.scrollTop = chatWindow.scrollHeight;
                                        }
                                    }
                                } catch (err) {
                                    console.error(err);
                                }
                            }
                        };
                        
                        const clearChatBtn = document.getElementById('clearChatBtn');
                        if (clearChatBtn) {
                            clearChatBtn.addEventListener('click', async () => {
                                if (confirm('Are you sure you want to clear your chat history?')) {
                                    try {
                                        await fetch('{{ route('chat.ashmita.clear') }}?socket_id=' + socketId, {
                                            method: 'DELETE',
                                            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
                                        });
                                        chatContainer.innerHTML = `
                                        <div style="display: flex; gap: 10px; align-items: flex-end;">
                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #004392; color: white; display: flex; align-items: center; justify-content: center; font-size: 16px; flex-shrink: 0; box-shadow: 0 2px 5px rgba(0,67,146,0.3); overflow: hidden;">
                                                <img src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcR_bFKmpUN1M-jPxmVF5h-DSr1Vta2W3nOnOQ&s" style="width:100%; height:100%; object-fit:cover;">
                                            </div>
                                            <div style="background: white; padding: 12px 15px; border-radius: 14px; border-bottom-left-radius: 4px; max-width: 80%; font-size: 14px; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.04); border: 1px solid #f1f1f1;">
                                                Conversation cleared. Ask me anything!
                                            </div>
                                        </div>`;
                                    } catch (err) {
                                        console.error(err);
                                    }
                                }
                            });
                        }
                        
                        sendBtn.addEventListener('click', sendMessage);
                        input.addEventListener('keypress', (e) => {
                            if(e.key === 'Enter') sendMessage();
                        });
                        
                        input.focus();
                    },
                    willClose: () => {
                        if (sidebarSocket) {
                            sidebarSocket.disconnect();
                        }
                    }
                });
            } else {
                alert('Chat support is currently offline.');
            }
        });
    });
    
    // Featured Banners Smooth Scroller
    if ($(".featured-wrapper").length) {
        $(".featured-wrapper").owlCarousel({
            items: 1,
            margin: 0,
            nav: false,
            dots: false,
            loop: true,
            autoplay: true,
            autoplayTimeout: 3000,
            autoplaySpeed: 800,
            autoplayHoverPause: true
        });
    }
});
</script>
