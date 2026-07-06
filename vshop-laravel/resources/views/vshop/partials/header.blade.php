<style>
    @media(max-width: 575px) {
        .dashboard-search-sec .form-control {
            width: 100%;
            height: 35px;
            padding: 6px 14px;
            padding-left: 48px;
        }
        .dashboard-search-sec i {
            top: 10px;
            font-size: 16px;
        }
    }

    /* ===== NOTIFICATION DROPDOWN ===== */
    #notification-wrapper {
        position: relative;
        display: inline-block;
    }
    .notification-dropdown {
        position: absolute;
        right: 0;
        top: calc(100% + 10px);
        width: 320px;
        background: #fff;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        z-index: 1050;
        display: none;
        overflow: hidden;
    }
    .notification-dropdown.show {
        display: block;
    }
    .notification-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 16px;
        border-bottom: 1px solid #f0f0f0;
        font-weight: 600;
        font-size: 14px;
    }
    .notification-header button {
        background: none;
        border: none;
        color: #0d6efd;
        font-size: 12px;
        cursor: pointer;
        text-decoration: underline;
    }
    .notification-header button:hover {
        color: #004392;
    }
    .notification-list {
        max-height: 300px;
        overflow-y: auto;
        padding: 0;
        margin: 0;
        list-style: none;
    }
    .notification-list li {
        padding: 10px 16px;
        border-bottom: 1px solid #f5f5f5;
        transition: background 0.15s;
    }
    .notification-list li:hover {
        background: #f8f9fa;
    }
    .notification-list li a {
        text-decoration: none;
        display: block;
        color: #222;
    }
    .notification-list li .notif-message {
        font-size: 14px;
        margin-bottom: 2px;
    }
    .notification-list li .notif-time {
        font-size: 12px;
        color: #888;
    }
    .notification-list .empty {
        text-align: center;
        color: #999;
        padding: 20px 0;
        font-size: 14px;
    }
    .bell-icon {
        display: inline-block;
        white-space: nowrap;
        font-size: 14px;
        color: #333;
        text-decoration: none;
        padding: 5px 10px;
        font-weight: 600;
        letter-spacing: 0px;
        position: relative;
        cursor: pointer;
        background: none;
        border: none;
    }
    .bell-icon span {
        width: 18px;
        height: 18px;
        background: #E30D0D;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        border-radius: 50%;
        position: absolute;
        top: -9px;
        right: -6px;
        font-size: 11px;
        font-weight: 600;
    }
    .top-bottom-sec ul li a:hover {
        color: #004392 !important;
    }
    
    .location-box {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 4px 8px;
        border-radius: 4px;
        transition: background 0.2s;
    }
    .location-box:hover {
        background: rgba(0,0,0,0.05);
    }
    .location-box .left-part i {
        color: #004392;
        font-size: 18px;
        margin-top: 2px;
    }
    .location-box .right-part {
        display: flex;
        flex-direction: column;
    }
    .location-box .right-part .delivery-text {
        display: block;
        font-size: 11px;
        font-weight: 500;
        color: #666;
        line-height: 1.1;
        white-space: nowrap;
    }
    .location-box .right-part .location-info {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #004392;
        line-height: 1.2;
        max-width: 110px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .dashboardTopBar {
        padding-bottom: 0;
    }
    
    .dashboardTopBar {
        background: hsl(206.25deg 100% 96.86%);
    }
    .dashboard-top-bottom {
        background: hsl(206.25deg 100% 96.86%);
    }
    .dashboard-top-bar {
        display: flex;
        align-items: center;
        background: #eff8ff;
    }
    .dashboard-top-bar .left-side {
        width: 200px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    .dashboard-top-bar .right-side {
        width: calc(100% - 200px) !important;
    }
    .top-search-bar-sec {
        margin-left:0;
        margin-right: 0;
        flex-shrink: 0;   /* prevent search from shrinking */
    }
    .cart-icon a {
        display: inline-block;
        white-space: nowrap;
        font-size: 14px;
        color: #333;
        text-decoration: none;
        padding: 5px 10px;
        font-weight: 600;
        letter-spacing: 0px;
        position: relative;
    }
    .dropdown-menu {
        width: 185px;
    }
    .dropdown-menu .dropdown-item {
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 10px;
    }
    .dropdown-menu .dropdown-item i {
        font-size: 17px;
    }
    
    /* ===== NEW: Amazon Style Search ===== */
    .top-search-bar-sec {
        flex-grow: 1;
        margin: 0 20px;
    }
    .amazon-search-form {
        display: flex;
        width: 100%;
        border-radius: 4px;
        overflow: hidden;
        border: 0;
        background: #fff;
    }
    .amazon-search-form:focus-within {
        box-shadow: 0 0 0 2px #0091ffff;
    }
    .amazon-search-select {
        background: #f3f3f3;
        border: 1px solid #cdcdcd;
        border-right: none;
        border-radius: 4px 0 0 4px;
        color: #555;
        padding: 0 10px;
        font-size: 14px;
        outline: none;
        cursor: pointer;
        min-width: 130px;
    }
    .amazon-search-select:hover {
        background: #e3e6e6;
        color: #0f1111;
    }
    .amazon-search-input {
        border: 1px solid #cdcdcd;
        border-left: none;
        border-right: none;
        padding: 8px 12px;
        flex-grow: 1;
        outline: none;
        font-size: 15px;
        border-radius: 0;
    }
    .amazon-search-input:focus {
        border-color: #cdcdcd;
        box-shadow: none;
    }
    .amazon-search-btn {
        background: #004392;
        border: 1px solid #004392;
        border-radius: 0 4px 4px 0;
        padding: 0 20px;
        cursor: pointer;
        color: #fff;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .amazon-search-btn:hover {
        background: #003370;
    }
    .amazon-search-btn i {
        font-size: 16px;
        color: #fff;
    }

    /* Remove the old category links wrapper */
    .category-links-wrapper {
        display: none;
    }

    /* Responsive adjustments */
    @media (max-width: 992px) {
        .dashboard-top-bar .left-side {
            width: 140px;
        }
        .dashboard-top-bar .right-side {
            width: calc(100% - 140px) !important;
        }
    }
    @media (max-width: 768px) {
        .dashboard-top-bar {
            padding: 8px 10px;
            flex-wrap: wrap;
        }
        .dashboard-top-bar .left-side {
            width: auto;
            flex-shrink: 0;
        }
        .dashboard-top-bar .right-side {
            flex: 1;
            width: auto !important;
        }
        .dashboardTopBar .container-fluid {
            padding-left: 0;
            padding-right: 0;
        }
        .dashboardTopBar .d-flex.justify-content-between {
            flex-wrap: wrap;
            gap: 10px;
            justify-content: flex-end !important;
            width: 100%;
        }
        .top-search-bar-sec {
            display: none !important;
        }
        .left-side {
            gap: 8px !important;
        }
        .logo-sec img {
            height: 38px !important;
            width: auto !important;
            margin-top: 2px;
        }
        .right-side .gap-3 {
            gap: 8px !important;
            order: 2;
            margin-left: auto;
            justify-content: flex-end;
        }
        .location-box .right-part {
            display: none !important;
        }
        .location-box {
            padding: 0;
        }
        .location-box .left-part i {
            font-size: 16px;
        }
        .cart-icon a i, .bell-icon i {
            font-size: 16px !important;
        }
        .cart-icon a span, .bell-icon span {
            width: 15px;
            height: 15px;
            font-size: 10px;
            top: -5px;
            right: -2px;
        }
        .dashboard-profile-sec .profile-box img {
            width: 25px;
            height: 25px;
        }
    }
    
    @media (max-width: 991px) {
        .dashboardPagesContent, .dashboard-bottom-wrapper, .dashBoradPageBody {
            width: 100% !important;
            max-width: 100vw !important;
            overflow-x: hidden !important;
        }
    }
    
    /* Global fix for horizontal scroll */
    html, body {
        overflow-x: hidden;
        max-width: 100vw;
    }
</style>

<!-- Bootstrap Icons CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Firebase module loader -->
<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-app.js";
    import { getDatabase, ref, query, limitToLast, onChildAdded } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-database.js";

    const firebaseConfig = {
        apiKey: "AIzaSyCTJhosvcfXwQIpWnEEFwoXQx6rCXBMnYY",
        authDomain: "mafatlalshop-90155.firebaseapp.com",
        databaseURL: "https://mafatlalshop-90155-default-rtdb.asia-southeast1.firebasedatabase.app",
        projectId: "mafatlalshop-90155",
        storageBucket: "mafatlalshop-90155.firebasestorage.app",
        messagingSenderId: "858357107416",
        appId: "1:858357107416:web:80c49eb1feaf0d843d65ad",
        measurementId: "G-K3L8YLB6XG"
    };

    const app = initializeApp(firebaseConfig);
    const db = getDatabase(app);
    window.db = db;
    window.fRef = ref;
    window.fQuery = query; 
    window.fLimit = limitToLast;
    window.fOnChildAdded = onChildAdded;
</script>

<header class="dashboard-top-bar">

@php
use App\Models\CartItem;
use App\Models\Wishlist;
use App\Models\Order;
use App\Models\Reward;

$customerLoggedIn = Auth::guard('customer')->check();

$cartCount = 0;
$wishlistCount = 0;
$orderCount = 0;
$rewardPoints = 0;

if($customerLoggedIn){
    $customerId = Auth::guard('customer')->id();

    $cartCount = CartItem::where('customer_id', $customerId)->count();
    $wishlistCount = Wishlist::where('customer_id', $customerId)->count();
    $orderCount = Order::where('customer_id', $customerId)->count();
    
    // Sum cumulative reward points
    $rewardPoints = Reward::getValidPoints($customerId);
}
@endphp

<div class="left-side d-flex align-items-center gap-2">
    <button class="sidebar-trigger toggle-tooltips" style="background: none; border: none; font-size: 20px;">
        <i class="fa-solid fa-bars"></i>
    </button>
    <div class="logo-sec">
        <a href="{{ $customerLoggedIn ? url('/') : route('vshoplogin') }}">
            <img src="https://vshop.zelofx.com/public/images/new-logo-vridhee.png" alt="Boost Now" style="width: 100px; height: 60px; display: block;">
        </a>
    </div>
</div>

<div class="right-side">

    <div class="dashboard-top-header">
        <div class="dashboardTopBar">
            <div class="container-fluid">

                {{-- ===== SINGLE ROW: search + category links + icons ===== --}}
                <div class="d-flex justify-content-between align-items-center flex-wrap">

                    {{-- CENTER: Full Size Amazon Style Search Bar --}}
                    <div class="d-flex align-items-center top-search-bar-sec">

                        <form action="{{ route('productmanage.index') }}" method="GET" class="amazon-search-form">
                            @foreach(request()->except(['q', 'filter', 'page']) as $key => $value)
                                @if(is_array($value))
                                    @foreach($value as $k => $v)
                                        <input type="hidden" name="{{ $key }}[{{ $k }}]" value="{{ $v }}">
                                    @endforeach
                                @else
                                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                                @endif
                            @endforeach

                            <select name="filter" class="amazon-search-select" onchange="this.form.submit()">
                                <option value="">All Categories</option>
                                <option value="trending" {{ request('filter') == 'trending' ? 'selected' : '' }}>Top Trending</option>
                                <option value="bestseller" {{ request('filter') == 'bestseller' ? 'selected' : '' }}>Bestsellers</option>
                                <option value="discount" {{ request('filter') == 'discount' ? 'selected' : '' }}>Best Discounts</option>
                                <option value="new" {{ request('filter') == 'new' ? 'selected' : '' }}>New Arrival</option>
                            </select>
                            <input type="text" 
                                   name="q"
                                   class="amazon-search-input"
                                   placeholder="Search products..."
                                   value="{{ request('q') }}">
                            <button type="submit" class="amazon-search-btn">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                        </form>
                    </div>

                    {{-- RIGHT: Location, Notification, Wishlist, Cart, Profile --}}
                    <div class="d-flex align-items-center gap-2">
                        
                        <a href="{{ url('seller/register') }}" class="btn btn-sm text-white fw-bold shadow-sm" style="background: linear-gradient(90deg, #de7921, #f59a4b); border-radius: 20px; font-size: 12px; padding: 5px 12px; white-space: nowrap; letter-spacing: 0.3px; border: none;">Want to be a Seller?</a>
                        
                        <div class="location-box" id="fetchLocationBtn" style="cursor: pointer;" title="Click to update location">
                            <div class="left-part">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <div class="right-part">
                                <span class="delivery-text">Delivery to</span>
                                @php
                                    $currentLoc = session('current_location', '');
                                    if(empty($currentLoc) && $customerLoggedIn) {
                                        $dbLoc = \App\Models\Address::where('customer_id', Auth::guard('customer')->id())
                                            ->where('address_type', 'both')
                                            ->where('address_line1', 'Current Location')
                                            ->first();
                                        if($dbLoc) {
                                            $currentLoc = $dbLoc->city;
                                            if($dbLoc->state) $currentLoc .= ', ' . $dbLoc->state;
                                            if($dbLoc->pincode) $currentLoc .= ' - ' . $dbLoc->pincode;
                                            session()->put('current_location', $currentLoc);
                                        }
                                    }
                                @endphp
                                <span class="location-info" id="locationInfoText">{{ $currentLoc }}</span>
                            </div>
                        </div>

                        {{-- ===== NOTIFICATION BELL ===== --}}
                        <div id="notification-wrapper">
                            <button id="notificationButton" class="bell-icon">
                                <i class="fa-solid fa-bell"></i>
                                @php
                                    $unreadCount = 0;
                                    if ($customerLoggedIn) {
                                        $unreadCount = Auth::guard('customer')->user()
                                            ->notifications()
                                            ->where('is_read', false)
                                            ->count();
                                    }
                                @endphp
                                @if($unreadCount > 0)
                                    <span id="notificationCount">{{ $unreadCount }}</span>
                                @endif
                            </button>

                            <div id="notificationDropdown" class="notification-dropdown">
                                <div class="notification-header">
                                    <span>Notifications</span>
                                    <button id="clearNotificationsBtn">Clear All</button>
                                </div>
                                <ul class="notification-list" id="notificationList">
                                    @php
                                        $notifications = [];
                                        if ($customerLoggedIn) {
                                            $notifications = Auth::guard('customer')->user()
                                                ->notifications()
                                                ->orderBy('created_at', 'desc')
                                                ->take(5)
                                                ->get();
                                        }
                                    @endphp
                                    @forelse($notifications as $notif)
                                        <li>
                                            <a href="{{ $notif->link ?? '#' }}">
                                                <div class="notif-message">{{ $notif->message }}</div>
                                                <div class="notif-time">{{ $notif->created_at->diffForHumans() }}</div>
                                            </a>
                                        </li>
                                    @empty
                                        <li class="empty">No notifications</li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>

                        <!--<a href="{{ route('seller.login') }}" class="seller-login-btn">-->
                        <!--    Seller Login-->
                        <!--</a>-->
                        
                        {{-- Wishlist --}}
                        <span class="cart-icon">
                            <a href="{{ $customerLoggedIn 
                                    ? route('vshop.cart.index') 
                                    : route('vshoplogin') }}">
                                <i class="fa-regular fa-heart"></i>
                                <span id="wishlistCount">{{ $wishlistCount }}</span>
                            </a>
                        </span>
            
                        {{-- Rewards --}}
                        <span class="cart-icon">
                            <a href="{{ $customerLoggedIn ? route('vshop.myaccount') . '?tab=rewards' : route('vshoplogin') }}" title="Reward Points">
                                <i class="fa-solid fa-coins text-warning"></i>
                                <span id="rewardPointsCount">{{ $rewardPoints }}</span>
                            </a>
                        </span>

                        {{-- Cart --}}
                        <span class="cart-icon">
                            <a href="{{ $customerLoggedIn 
                                    ? route('vshop.cart.index') 
                                    : route('vshoplogin') }}">
                                <i class="fa-solid fa-cart-arrow-down"></i>
                                <span id="cartCount">{{ $cartCount }}</span>
                            </a>
                        </span>

                        {{-- PROFILE --}}
                        <div class="dashboard-profile-sec">

                            @if($customerLoggedIn)

                                <div class="profile-dropdown">
                                    <button class="dropdown-toggle profile-box"
                                            type="button"
                                            data-bs-toggle="dropdown">

                                        <img src="{{ 
                                            Auth::guard('customer')->user()->profile_picture
                                            ? asset(Auth::guard('customer')->user()->profile_picture)
                                            : asset('vtravelassets/images/avatar.png')
                                        }}">
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">

                                        <li>
                                            <a class="dropdown-item" href="{{ route('vshop.myaccount') }}">
                                                <i class="fa-regular fa-user me-2"></i>
                                                My Profile
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item" href="{{ $customerLoggedIn? route('vshop.order.list') : route('vshoplogin') }}">
                                                <i class="fa-solid fa-box-archive"></i>
                                                My Orders
                                            </a>
                                        </li>
                                        
                                        <li>
                                            
                                        </li>

                                        <li><hr class="dropdown-divider"></li>

                                        <li>
                                            <form action="{{ route('logout') }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item mb-0">
                                                    <i class="fa-solid fa-sign-out-alt me-2"></i>
                                                    Logout
                                                </button>
                                            </form>
                                        </li>

                                    </ul>
                                </div>

                            @else

                                <div class="profile-dropdown">
                                    <button class="dropdown-toggle profile-box"
                                            type="button"
                                            data-bs-toggle="dropdown">

                                        <img src="{{ asset('vtravelassets/images/avatar.png') }}">
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a href="{{ route('vshoplogin') }}" class="dropdown-item">
                                                <i class="fa-solid fa-sign-in-alt me-2"></i>
                                                Login
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                            @endif

                        </div>

                    </div> <!-- end right icons -->

                </div> <!-- end single row -->

            </div> <!-- container-fluid -->
        </div> <!-- dashboardTopBar -->
    </div> <!-- dashboard-top-header -->

    {{-- ===== OLD bottom row is now hidden via CSS ===== --}}
    <div class="dashboard-top-bottom" style="display:none;">
        <!-- category links moved to the row above -->
    </div>

</div> <!-- right-side -->
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Firebase customer notification listener
        const customerId = "{{ Auth::guard('customer')->check() ? Auth::guard('customer')->id() : '' }}";
        if (customerId && window.fRef) {
            const customerNotifsRef = window.fRef(window.db, "notifications/customer/" + customerId);
            const watchCustomerLast = window.fQuery(customerNotifsRef, window.fLimit(1));
            let lastCustomerNotifId = null;
            let initialCustomerLoad = true;
            const dingSound = new Audio("https://assets.mixkit.co/sfx/preview/mixkit-software-interface-back-2576.mp3");

            window.fOnChildAdded(watchCustomerLast, (snapshot) => {
                const data = snapshot.val();
                if (!data) return;
                const notifKey = snapshot.key;
                if (initialCustomerLoad) {
                    initialCustomerLoad = false;
                    lastCustomerNotifId = notifKey;
                    return;
                }
                if (notifKey === lastCustomerNotifId) return;
                lastCustomerNotifId = notifKey;

                // Increment the count badge or create it if not present
                let countBadge = document.getElementById('notificationCount');
                if (!countBadge) {
                    countBadge = document.createElement('span');
                    countBadge.id = 'notificationCount';
                    document.getElementById('notificationButton').appendChild(countBadge);
                }
                let count = parseInt(countBadge.innerText) || 0;
                countBadge.innerText = ++count;

                // Play sound and show toast
                dingSound.play();
                if (typeof toastr !== 'undefined') {
                    toastr.success(data.message, data.type ? data.type.toUpperCase() : 'Notification');
                }

                // Prepend to notifications list
                const list = document.getElementById('notificationList');
                if (list) {
                    // Remove empty placeholder
                    const emptyItem = list.querySelector('.empty');
                    if (emptyItem) emptyItem.remove();

                    const li = document.createElement('li');
                    li.innerHTML = `
                        <a href="${data.link || '#'}">
                            <div class="notif-message" style="font-weight: bold;">${data.message}</div>
                            <div class="notif-time">Just now</div>
                        </a>
                    `;
                    list.insertBefore(li, list.firstChild);
                }
            });
        }

        const btn = document.getElementById('notificationButton');
        const dropdown = document.getElementById('notificationDropdown');
        const clearBtn = document.getElementById('clearNotificationsBtn');

        // Toggle dropdown
        if (btn && dropdown) {
            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                dropdown.classList.toggle('show');
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function (e) {
                if (!dropdown.contains(e.target) && e.target !== btn && !btn.contains(e.target)) {
                    dropdown.classList.remove('show');
                }
            });
        }

        // Clear all notifications
        if (clearBtn) {
            clearBtn.addEventListener('click', function () {
                if (confirm('Clear all notifications?')) {
                    fetch('{{ route("customer.notifications.clear") }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            const list = document.getElementById('notificationList');
                            const countBadge = document.getElementById('notificationCount');
                            if (list) {
                                list.innerHTML = '<li class="empty">No notifications</li>';
                            }
                            if (countBadge) {
                                countBadge.remove();
                            }
                            dropdown.classList.remove('show');
                        }
                    })
                    .catch(err => console.error('Clear error:', err));
                }
            });
        }

        // Function to save location to backend
        function saveLocationToBackend(city, state, pincode) {
            fetch('{{ route("update.current.location") }}', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ city: city, state: state, pincode: pincode })
            })
            .then(res => res.json())
            .then(data => {
                if(data.success && data.location) {
                    document.getElementById('locationInfoText').innerHTML = data.location;
                }
            })
            .catch(err => console.error('Error saving location:', err));
        }

        // Location fetcher using OpenStreetMap Nominatim API
        const locationBtn = document.getElementById('fetchLocationBtn');
        const locationInfo = document.getElementById('locationInfoText');

        if (locationBtn && locationInfo) {
            locationBtn.addEventListener('click', function() {
                if (navigator.geolocation) {
                    const originalText = locationInfo.innerHTML;
                    locationInfo.innerHTML = '<span class="spinner-border spinner-border-sm text-primary" role="status" aria-hidden="true" style="width: 1rem; height: 1rem; margin-right: 5px;"></span> Locating...';
                    
                    navigator.geolocation.getCurrentPosition(
                        function(position) {
                            const lat = position.coords.latitude;
                            const lon = position.coords.longitude;
                            
                            fetch(`https://nominatim.openstreetmap.org/reverse?format=jsonv2&lat=${lat}&lon=${lon}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data && data.address) {
                                        const city = data.address.city || data.address.town || data.address.village || data.address.county || 'Unknown City';
                                        const state = data.address.state || '';
                                        const postcode = data.address.postcode || '';
                                        
                                        let parts = [city];
                                        if (state) parts.push(state);
                                        let locationString = parts.join(', ');
                                        if (postcode) locationString += ' - ' + postcode;
                                        
                                        locationInfo.innerHTML = locationString;
                                        saveLocationToBackend(city, state, postcode);
                                    } else {
                                        locationInfo.innerHTML = originalText;
                                        alert('Could not determine location from coordinates.');
                                    }
                                })
                                .catch(error => {
                                    console.error('Error fetching location:', error);
                                    locationInfo.innerHTML = originalText;
                                    alert('Error connecting to location service.');
                                });
                        },
                        function(error) {
                            console.error('Geolocation error:', error);
                            locationInfo.innerHTML = originalText;
                            if(error.code === error.PERMISSION_DENIED) {
                                alert("Location request denied. Please allow location access in your browser settings.");
                            } else if(error.code === error.TIMEOUT) {
                                alert("Location request timed out. Please try again.");
                            } else {
                                // Fallback to IP-based location if device location is unavailable
                                fetch('https://ipinfo.io/json')
                                    .then(res => res.json())
                                    .then(data => {
                                        if(data && data.city) {
                                            const city = data.city || 'Unknown City';
                                            const state = data.region || '';
                                            const postcode = data.postal || '';
                                            
                                            let parts = [city];
                                            if (state) parts.push(state);
                                            let locationString = parts.join(', ');
                                            if (postcode) locationString += ' - ' + postcode;
                                            
                                            locationInfo.innerHTML = locationString;
                                            saveLocationToBackend(city, state, postcode);
                                        } else {
                                            alert("Location information is unavailable right now. (Fallback failed)");
                                        }
                                    })
                                    .catch(e => {
                                        console.error("IP fallback error:", e);
                                        alert("Location information is unavailable right now. Please check your connection.");
                                    });
                            }
                        },
                        { timeout: 15000, maximumAge: 60000 }
                    );
                } else {
                    alert("Geolocation is not supported by this browser.");
                }
            });
        }
    });
</script>
