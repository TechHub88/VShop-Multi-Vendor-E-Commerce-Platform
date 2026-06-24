
<!-- FONT AWESOME (IMPORTANT) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<meta name="csrf-token" content="{{ csrf_token() }}">


@php
/*
|--------------------------------------------------------------------------
| MODULE TYPES
|--------------------------------------------------------------------------
*/
$productTypes = ['product','professional'];
$travelTypes = ['service','tour'];
$alertTypes = [];
if(($moduleType ?? '') == 'product'){
    $alertTypes = $productTypes;
}elseif(($moduleType ?? '') == 'service'){
    $alertTypes = $travelTypes;
}elseif(($moduleType ?? '') == 'course'){
    $alertTypes = ['course'];
}else{
    $alertTypes = array_merge($productTypes, $travelTypes);
}

/*
|--------------------------------------------------------------------------
| DELAYED ORDER COUNT TYPEWISE
|--------------------------------------------------------------------------
*/
$dashboardDelayedOrders = \App\Models\Order::where(
    'seller_id',
    auth()->guard('seller')->id()
)
->whereHas('items.product', function($q) use ($alertTypes){
    $q->whereIn('type', $alertTypes);
})
->where('status','!=','delivered')
->where('created_at','<', now()->subDays(3))
->count();

/*
|--------------------------------------------------------------------------
| DOCUMENT EXPIRY ALERT
|--------------------------------------------------------------------------
*/
$seller = auth()->guard('seller')->user();
$today = \Carbon\Carbon::today();
$expiryAlerts = [];

if ($seller) {
    if (!empty($seller->pan_expiry)) {
        $panDate = \Carbon\Carbon::parse($seller->pan_expiry);
        $days = (int) $today->diffInDays($panDate, false);
        if ($days <= 30) {
            $expiryAlerts[] = [
                'document_name' => 'PAN Card',
                'expiry_date' => $panDate->format('d M Y'),
                'days_left' => $days,
                'status' => $days < 0 ? 'expired' : 'expiring',
            ];
        }
    }

    if (!empty($seller->gst_expiry)) {
        $gstDate = \Carbon\Carbon::parse($seller->gst_expiry);
        $days = (int) $today->diffInDays($gstDate, false);
        if ($days <= 30) {
            $expiryAlerts[] = [
                'document_name' => 'GST Certificate',
                'expiry_date' => $gstDate->format('d M Y'),
                'days_left' => $days,
                'status' => $days < 0 ? 'expired' : 'expiring',
            ];
        }
    }

    $referralSettings = is_array($seller->referral_settings ?? null) 
        ? $seller->referral_settings 
        : json_decode($seller->referral_settings ?? '[]', true);
}
@endphp

<header class="dashboard-top-bar">
    <div class="dashboard-top-header">
        <div class="dashboardTopBar">
            <div class="container-fluid">
                <div class="d-flex justify-content-between align-items-center dashboardTopBarinner">
                    
                    <div class="logo-sec">
                        <img src="https://vshop.zelofx.com/public/images/new-logo-vridhee.png" alt="Boost Now" style="height: 52px; width: auto; object-fit: contain; display: block;">
                    </div>

                    <!-- SEARCH BAR (Inline) -->
                    <div class="d-flex align-items-center gap-3">
                        <div class="dashboard-search-sec position-relative">
                            <input type="text"
                                   id="globalSearch"
                                   class="search-input"
                                   placeholder="Search products, orders, customers, enquiries..."
                                   autocomplete="off">
                            <button class="search-submit-btn" onclick="doSearch()">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            <div id="searchResults" class="search-dropdown"></div>
                        </div>
                    </div>

                    <!-- RIGHT SIDE -->
                    <div class="d-flex align-items-center gap-3">
                        <!-- Deliver to Location Section -->
                        <div class="header-location-sec d-flex align-items-center cursor-pointer" id="headerLocationBtn" onclick="detectLocation()" title="Click to detect location" style="cursor: pointer; user-select: none;">
                            <i class="fa-solid fa-location-dot me-2" style="font-size: 16px;"></i>
                            <div class="d-flex flex-column" style="line-height: 1.2;">
                                <span class="text-muted" style="font-size: 9px;">Deliver to</span>
                                <span class="fw-bold text-dark" style="font-size: 12px;" id="headerLocationName">Detect Location</span>
                            </div>
                        </div>

                        {{-- FLOATING REFERRAL BUTTON --}}
                        <button type="button" class="boost-btn" title="Boost Performance">
                            <img src="https://vshop.zelofx.com/public/images/boost_icon.png" alt="Boost Now" style="width: 28px; height: 28px; object-fit: contain; display: block;">
                            <span id="liveTrackingDot" class="refer-toggle-sec" style="display: none;">Boost Now</span>
                        </button>
                        <button type="button" class="ref-floating-btn" onclick="openReferralModal()" title="Refer & Earn">
                            <i class="fa-solid fa-gift"></i>
                            <span id="liveTrackingDot" class="refer-toggle-sec" style="display: none;">Refer & Earn</span>
                        </button>
                        {{-- Alert Bell Icon with Hover Dropdown --}}
                        <div class="alert-bell-wrapper" id="alertBellWrapper">
                            <div class="alert-bell-icon {{ !empty($expiryAlerts) ? 'has-alerts' : '' }}" id="alertBellIcon" title="Alerts" onclick="toggleAlertDropdown(event)">
                                <div class="bell-siren-icon">
                                    <div class="siren-light">
                                        <div class="siren-inner"></div>
                                    </div>
                                    <div class="siren-base"></div>
                                </div>
                                <span class="alert-count" id="alertCountBadge" style="display:none;"></span>
                            </div>
                            
                            <div class="alert-dropdown-menu" id="alertDropdownMenu">
                                <div class="alert-dropdown-header-bar">
                                    <strong>Alerts</strong>
                                    <button id="clearAlerts" class="clear-btn-pro">
                                        <i class="fa-solid fa-trash"></i>
                                        Clear All
                                    </button>
                                </div>
                                <div class="alert-dropdown-body">
                                    {{-- DOCUMENT EXPIRY ALERT --}}
                                    @if(!empty($expiryAlerts))
                                        @foreach($expiryAlerts as $alert)
                                        <div class="alert-dropdown-item document-alert-item" data-href="{{ route('seller.organization') }}">
                                            <div class="alert-item-icon document-icon">
                                                <div class="mini-siren-icon">
                                                    <div class="mini-siren-light">
                                                        <div class="mini-siren-inner"></div>
                                                    </div>
                                                    <div class="mini-siren-base"></div>
                                                </div>
                                            </div>
                                            <div class="alert-item-content">
                                                <div class="alert-item-title">
                                                    Expiry Alert: {{ $alert['document_name'] }} ({{ $alert['status'] }})
                                                </div>
                                                <div style="font-size: 11px; color: #888;">{{ $alert['days_left'] }} days left - Action Required</div>
                                            </div>
                                            <div class="alert-item-arrow">
                                                <i class="fa fa-chevron-right"></i>
                                            </div>
                                        </div>
                                        @endforeach
                                    @endif

                                    {{-- SYSTEM ALERTS (Loaded via JS) --}}
                                    <div id="systemAlertsList">
                                        <div class="alert-dropdown-empty">
                                            <i class="fa fa-circle-notch fa-spin"></i>
                                            <p>Loading...</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- 🔔 NOTIFICATION -->
                        <div class="notification-wrapper position-relative">
                            <button class="notif-btn" onclick="toggleNotification()" title="Notifications">
                                <i class="fa-solid fa-bell"></i>
                                <span class="notif-badge" id="notifCount" style="display:none;"></span>
                            </button>
                            <div class="notif-dropdown" id="notifDropdown">
                                <div class="notif-header d-flex justify-content-between">
                                    <strong>Notifications</strong>
                                </div>
                                <ul class="notif-list" id="notifList">
                                    <li class="text-center text-muted p-3">No new notifications</li>
                                </ul>
                            </div>
                        </div>

                        <!-- PROFILE -->
                        <div class="dashboard-profile-sec">
                            @if(Auth::guard('seller')->check())
                            <div class="profile-dropdown">
                                <button class="dropdown-toggle profile-box" type="button" data-bs-toggle="dropdown" title="Profile">
                                    <img src="{{ 
                                        Auth::guard('seller')->user()->profile_picture
                                        ? asset(Auth::guard('seller')->user()->profile_picture)
                                        : asset('vtravelassets/images/avatar.png')
                                    }}" class="w-100" style="border-radius: 50%;">
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('seller.profile') }}">
                                            <i class="fa-solid fa-user me-2"></i>
                                            My Profile
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a href="javascript:void(0);" class="dropdown-item" id="sellerLogoutBtn">
                                            <i class="fa-solid fa-sign-out-alt me-2"></i>
                                            Logout
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    {{-- REFERRAL SETTINGS MODAL POPUP --}}
    <div class="ref-modal-overlay" id="referralSettingsModal">
        <div class="ref-modal-card">
            <div class="ref-modal-header">
                <h5 class="ref-modal-title">
                    <i class="fa-solid fa-gift text-warning"></i>
                    v-Shop Refer & Earn
                </h5>
                <button type="button" class="ref-close-btn" onclick="closeReferralModal()">&times;</button>
            </div>
            <form id="referralSettingsForm" onsubmit="saveReferralSettings(event)">
                @csrf
                <div class="ref-modal-body">
                    <div class="ref-toggle-container">
                        <span class="ref-toggle-label">Enable Refer & Earn</span>
                        <label class="ref-switch">
                            <input type="checkbox" id="refEnabled" name="enabled" value="1" {{ ($referralSettings['enabled'] ?? false) ? 'checked' : '' }} onchange="toggleReferralInputs()">
                            <span class="ref-slider">
                                <span class="ref-slider-text"></span>
                            </span>
                        </label>
                    </div>

                    <div class="ref-form-group">
                        <label class="ref-form-label">Referral Reward Type & Amount</label>
                        <div class="ref-radio-group mb-2">
                            <label class="ref-radio-option">
                                <input type="radio" name="reward_type" id="rewardTypePercentage" value="percentage" {{ ($referralSettings['reward_type'] ?? 'percentage') === 'percentage' ? 'checked' : '' }} onchange="toggleReferralInputs()">
                                % or Fixed Amount
                            </label>
                            <label class="ref-radio-option">
                                <input type="radio" name="reward_type" id="rewardTypeFixed" value="fixed" {{ ($referralSettings['reward_type'] ?? 'percentage') === 'fixed' ? 'checked' : '' }} onchange="toggleReferralInputs()">
                                Fixed Amount
                            </label>
                        </div>

                        <div class="ref-inputs-row">
                            <div class="ref-form-group">
                                <label class="small fw-bold text-muted">Percentage Value</label>
                                <div class="ref-input-wrapper has-suffix" id="wrapperPercentage">
                                    <input type="number" step="0.01" min="0" max="100" id="refPercentage" name="percentage" class="ref-input-field" value="{{ $referralSettings['percentage'] ?? 5 }}">
                                    <span class="ref-input-suffix">%</span>
                                </div>
                            </div>
                            <div class="ref-form-group">
                                <label class="small fw-bold text-muted">Fixed Reward Amount</label>
                                <div class="ref-input-wrapper has-prefix" id="wrapperFixed">
                                    <span class="ref-input-prefix">₹</span>
                                    <input type="number" step="0.01" min="0" id="refFixedAmount" name="fixed_amount" class="ref-input-field" value="{{ $referralSettings['fixed_amount'] ?? 10 }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="ref-form-group">
                        <button type="button" class="ref-generate-btn" onclick="generateReferralLink()">
                            <span>Generate Referral/Share Link</span>
                            <i class="fa-solid fa-share-nodes"></i>
                        </button>
                    </div>

                    <div class="ref-preview-box" id="refPreviewBox">
                        <span class="ref-preview-title">Live Preview Link</span>
                        <div class="ref-preview-link-sec">
                            <a href="#" target="_blank" class="ref-preview-link" id="refPreviewLink"></a>
                            <button type="button" class="ref-copy-btn" onclick="copyReferralLink()" title="Copy Link">
                                <i class="fa-regular fa-copy" id="refCopyIcon"></i>
                            </button>
                        </div>
                    </div>

                    <p class="ref-helper-text">
                        <strong>v-Shop Refer & Earn</strong> program allows you to offer rewards to other sellers when they sign up and start listing products on the platform using your link.
                    </p>
                </div>
                <div class="ref-modal-footer">
                    <button type="button" class="ref-btn-cancel" onclick="closeReferralModal()">Cancel</button>
                    <button type="submit" class="ref-btn-save">Save Settings</button>
                </div>
            </form>
        </div>
    </div>
</header>

<style>
/* =========================================================
   HEADER MAIN LAYOUT OVERRIDES (Corporate White & Blue)
========================================================= */
.dashboard-top-bar {
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05) !important;
    height: auto !important;
    padding: 0 !important;
}

.dashboard-top-header {
    background: #ffffff !important;
}

.dashboardTopBar {
    background: #ffffff !important;
    padding: 8px 0 !important;
    height: auto !important;
}

.dashboardTopBarinner {
    height: 68px !important;
}

.logo-sec {
    height: 54px !important;
    display: flex !important;
    align-items: center !important;
}

/* =========================================================
   SIREN ICON STYLES (Scaled Down)
========================================================= */
.bell-siren-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.siren-light {
    width: 14px;
    height: 16px;
    background: linear-gradient(180deg, #ff2d2d, #d90429);
    border-radius: 8px 8px 3px 3px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset -1px 0 0 rgba(0,0,0,.08), 0 1px 2px rgba(255,0,0,.18);
}

.siren-inner {
    width: 3px;
    height: 6px;
    background: #fff;
    border-radius: 2px;
    position: relative;
}

.siren-inner::after {
    content: '';
    width: 3px;
    height: 3px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    bottom: -4px;
    left: 0;
}

.siren-base {
    width: 18px;
    height: 4px;
    background: #555;
    border-radius: 2px;
    margin-top: 1px;
    position: relative;
}

.siren-base::after {
    content: '';
    position: absolute;
    top: -2px;
    left: 3px;
    width: 12px;
    height: 3px;
    background: #bdbdbd;
    border-radius: 1px;
}

.alert-bell-icon.has-alerts .bell-siren-icon {
    animation: bellRing 1.5s ease infinite;
    transform-origin: center center;
}

@keyframes bellRing {
    0% { transform: rotate(0deg); }
    10% { transform: rotate(8deg); }
    20% { transform: rotate(-6deg); }
    30% { transform: rotate(4deg); }
    40% { transform: rotate(-2deg); }
    50% { transform: rotate(0deg); }
    100% { transform: rotate(0deg); }
}

/* Mini Siren inside Dropdown */
.mini-siren-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.mini-siren-light {
    width: 10px;
    height: 12px;
    background: linear-gradient(180deg, #ff2d2d, #d90429);
    border-radius: 5px 5px 2px 2px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mini-siren-inner {
    width: 2px;
    height: 4px;
    background: #fff;
    border-radius: 1px;
    position: relative;
}

.mini-siren-inner::after {
    content: '';
    width: 2px;
    height: 2px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    bottom: -3px;
    left: 0;
}

.mini-siren-base {
    width: 14px;
    height: 3px;
    background: #555;
    border-radius: 2px;
    margin-top: 1px;
}

/* Sidebar Trigger */
.sidebar-trigger {
    width: 36px !important;
    height: 36px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: transparent !important;
    border: none !important;
    border-radius: 6px !important;
    color: #2c7fc3 !important;
    font-size: 16px !important;
    cursor: pointer !important;
    transition: background 0.2s, color 0.2s !important;
}
.sidebar-trigger:hover {
    background: #f1f5f9 !important;
    color: #233d79 !important;
}

/* =========================================================
   AMAZON-STYLE SCOPED SEARCH BAR
========================================================= */
.dashboard-search-sec {
    display: flex !important;
    align-items: center !important;
    width: 480px !important;
    height: 42px !important;
    background: #ffffff !important;
    border: 1.5px solid #dbe2f0 !important;
    border-radius: 8px !important;
    overflow: hidden !important;
    transition: border-color 0.2s ease, box-shadow 0.2s ease !important;
}

.dashboard-search-sec:focus-within {
    border-color: #2c7fc3 !important;
    box-shadow: 0 0 0 3px rgba(44, 127, 195, 0.15) !important;
}

/* Scoped Dropdown Select */
.search-scope-select {
    width: 105px !important;
    height: 100% !important;
    background-color: #f3f4f6 !important;
    color: #4b5563 !important;
    font-size: 12px !important;
    font-weight: 500 !important;
    border: none !important;
    border-right: 1px solid #dbe2f0 !important;
    border-radius: 0 !important;
    padding: 0 24px 0 10px !important;
    outline: none !important;
    cursor: pointer !important;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%234b5563' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 8px center !important;
    background-size: 10px 10px !important;
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
}
.search-scope-select:focus {
    box-shadow: none !important;
    border-color: #dbe2f0 !important;
}

/* Search input field */
.search-input {
    flex: 1 !important;
    height: 100% !important;
    border: none !important;
    border-radius: 0 !important;
    padding: 0 12px !important;
    font-size: 13px !important;
    outline: none !important;
    background: #ffffff !important;
    color: #1e293b !important;
}
.search-input:focus {
    box-shadow: none !important;
    border: none !important;
    background: #ffffff !important;
}

/* Search submit button */
.search-submit-btn {
    width: 46px !important;
    height: 100% !important;
    background: #2c7fc3 !important;
    color: #ffffff !important;
    border: none !important;
    border-radius: 0 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    font-size: 16px !important;
    transition: background 0.2s ease !important;
}
.search-submit-btn i {
    color: #ffffff !important;
    font-size: 16px !important;
}
.search-submit-btn:hover {
    background: #233d79 !important;
}

/* Search Dropdown list autocomplete */
.search-dropdown {
    position: absolute !important;
    top: 42px !important;
    left: 0 !important;
    width: 100% !important;
    max-height: 350px !important;
    overflow-y: auto !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    display: none;
    z-index: 9999 !important;
}

.search-item {
    padding: 10px 12px !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
    gap: 10px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    transition: all 0.2s !important;
}
.search-item:hover {
    background: #f8fafc !important;
}
.search-item.active {
    background: #f0f7ff !important;
}
.search-group-title {
    font-size: 10px !important;
    font-weight: 700 !important;
    color: #64748b !important;
    padding: 6px 12px !important;
    background: #f8fafc !important;
    border-bottom: 1px solid #e2e8f0 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.5px !important;
}
.search-item-icon {
    width: 24px !important;
    height: 24px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    background: #f1f5f9 !important;
    border-radius: 6px !important;
    font-size: 12px !important;
}
.search-item-content {
    flex: 1 !important;
}
.search-item-title {
    font-size: 13px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
}
.search-item-subtitle {
    font-size: 11px !important;
    color: #64748b !important;
    margin-top: 1px !important;
}

/* =========================================================
   GEOLOCATION ACCESS SECTION
========================================================= */
.header-location-sec {
    display: flex !important;
    align-items: center !important;
    padding: 4px 12px !important;
    height: 42px !important;
    background: transparent !important;
    border: 1px solid #dbe2f0 !important;
    border-radius: 8px !important;
    cursor: pointer !important;
    user-select: none !important;
    transition: all 0.2s ease !important;
}
.header-location-sec:hover {
    background: #f8fafc !important;
    border-color: #2c7fc3 !important;
    transform: translateY(-1px) !important;
}
.header-location-sec i {
    color: #2c7fc3 !important;
    transition: transform 0.2s ease !important;
}
.header-location-sec:hover i {
    transform: scale(1.1) !important;
}

/* =========================================================
   UNIFORM BORDERLESS RIGHT SIDE CONTROLS
========================================================= */
.boost-btn,
.ref-floating-btn,
.alert-bell-icon,
.notif-btn,
.profile-box {
    width: 42px !important;
    height: 42px !important;
    min-width: 42px !important;
    min-height: 42px !important;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    cursor: pointer !important;
    padding: 0 !important;
    margin: 0 !important;
    position: relative !important;
    transition: color 0.2s ease, background-color 0.2s ease, transform 0.2s ease !important;
}

.boost-btn:hover,
.ref-floating-btn:hover,
.alert-bell-icon:hover,
.notif-btn:hover {
    background: #f1f5f9 !important;
    border-radius: 8px !important;
}

.ref-floating-btn i {
    font-size: 20px !important;
    color: #f59e0b !important;
    transition: transform 0.2s ease !important;
}

.notif-btn i {
    font-size: 18px !important;
    color: #000000 !important;
    transition: transform 0.2s ease !important;
}

.boost-btn img {
    transition: transform 0.2s ease !important;
}

.boost-btn:hover img,
.ref-floating-btn:hover i,
.notif-btn:hover i,
.alert-bell-icon:hover .bell-siren-icon {
    transform: scale(1.05) !important;
}

/* Profile image styling */
.profile-box {
    border-radius: 50% !important;
}
.profile-box img {
    width: 30px !important;
    height: 30px !important;
    border-radius: 50% !important;
    border: 2px solid #2c7fc3 !important;
    object-fit: cover !important;
    transition: all 0.2s ease !important;
}
.profile-box:hover img {
    border-color: #233d79 !important;
    transform: scale(1.05) !important;
}

/* =========================================================
   INDICATOR BADGES
========================================================= */
.alert-count,
.notif-badge {
    position: absolute !important;
    top: 2px !important;
    right: 2px !important;
    background: #ef4444 !important;
    color: #ffffff !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    min-width: 15px !important;
    height: 15px !important;
    border-radius: 50% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    padding: 0 3px !important;
    box-shadow: 0 0 0 2px #ffffff !important;
    z-index: 10 !important;
}

/* Sleek Scale Pulse Animation for Alerts */
.alert-bell-icon.has-alerts .bell-siren-icon {
    animation: alertPulse 2s infinite ease-in-out !important;
}
@keyframes alertPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); }
    100% { transform: scale(1); }
}

/* Tooltip hovering text for Referral & Boost Performance */
.boost-btn:hover .refer-toggle-sec,
.ref-floating-btn:hover .refer-toggle-sec {
    display: block !important;
    position: absolute !important;
    bottom: -28px !important;
    left: 50% !important;
    transform: translateX(-50%) !important;
    font-size: 9px !important;
    font-weight: 700 !important;
    color: #2c7fc3 !important;
    white-space: nowrap !important;
    background: #f0f7ff !important;
    padding: 2px 8px !important;
    border-radius: 4px !important;
    border: 1px solid #dbe2f0 !important;
    z-index: 100 !important;
}

/* =========================================================
   DROPDOWN MENUS (Alerts & Notifications)
========================================================= */
.alert-dropdown-menu {
    position: absolute !important;
    top: 44px !important;
    right: 0 !important;
    width: 240px !important;
    max-height: 280px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 8px !important;
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1) !important;
    display: none;
    z-index: 1000 !important;
    overflow: hidden !important;
}
.alert-dropdown-menu.show {
    display: flex !important;
    flex-direction: column !important;
}
.notif-dropdown {
    position: absolute !important;
    top: 44px !important;
    right: 0 !important;
    width: 300px !important;
    max-height: 350px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 10px !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    display: none;
    z-index: 1000 !important;
    overflow: hidden !important;
}

.alert-dropdown-header-bar,
.notif-header {
    padding: 10px 14px !important;
    border-bottom: 1px solid #f1f5f9 !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    background: #ffffff !important;
    font-weight: 600 !important;
    font-size: 13px !important;
    color: #1e293b !important;
}

.clear-btn-pro {
    display: inline-flex !important;
    align-items: center !important;
    gap: 4px !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    color: #ef4444 !important;
    font-size: 11px !important;
    font-weight: 500 !important;
    padding: 4px 8px !important;
    border-radius: 6px !important;
    cursor: pointer !important;
    transition: all 0.2s ease !important;
}
.clear-btn-pro:hover {
    background: #fef2f2 !important;
    border-color: #fca5a5 !important;
}

.alert-dropdown-body,
.notif-list {
    flex: 1 !important;
    overflow-y: auto !important;
    list-style: none !important;
    margin: 0 !important;
    padding: 0 !important;
}

.alert-dropdown-item,
.notif-list li {
    display: flex !important;
    align-items: flex-start !important;
    gap: 10px !important;
    padding: 12px 14px !important;
    border-bottom: 1px solid #f8fafc !important;
    cursor: pointer !important;
    transition: background-color 0.2s ease !important;
}
.alert-dropdown-item:hover,
.notif-list li:hover {
    background-color: #f8fafc !important;
}

.alert-item-icon {
    width: 24px !important;
    height: 24px !important;
    border-radius: 6px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    font-size: 11px !important;
}
.alert-item-icon.delayed-icon {
    background: #fffbeb !important;
    color: #d97706 !important;
}
.alert-item-icon.document-icon {
    background: #fef2f2 !important;
    color: #ef4444 !important;
}

.alert-item-content {
    flex: 1 !important;
}
.alert-item-title {
    font-size: 12px !important;
    font-weight: 500 !important;
    color: #1e293b !important;
    line-height: 1.4 !important;
}

.alert-dropdown-empty {
    padding: 24px !important;
    text-align: center !important;
    color: #64748b !important;
}
.alert-dropdown-empty i {
    font-size: 24px !important;
    color: #cbd5e1 !important;
    margin-bottom: 6px !important;
}

/* =========================================================
   REFERRAL SETTINGS MODAL & SLIDER
========================================================= */
.ref-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.4);
    backdrop-filter: blur(4px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 999999;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.ref-modal-overlay.show {
    display: flex;
    opacity: 1;
}

.ref-modal-card {
    background: #ffffff;
    border-radius: 16px;
    width: 100%;
    max-width: 480px;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.3s ease;
    border: 1px solid #e2e8f0;
}

.ref-modal-overlay.show .ref-modal-card {
    transform: translateY(0);
}

.ref-modal-header {
    background: #f8fafc;
    padding: 16px 20px;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ref-modal-title {
    font-size: 16px;
    font-weight: 700;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.ref-close-btn {
    background: transparent;
    border: none;
    font-size: 18px;
    color: #64748b;
    cursor: pointer;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: background-color 0.2s;
}
.ref-close-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.ref-modal-body {
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.ref-toggle-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    padding: 12px 16px;
    border-radius: 10px;
    border: 1px solid #e2e8f0;
}

.ref-toggle-label {
    font-weight: 600;
    font-size: 14px;
    color: #1e293b;
}

.ref-switch {
    position: relative;
    display: inline-block;
    width: 48px;
    height: 24px;
}
.ref-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.ref-slider {
    position: absolute;
    cursor: pointer;
    top: 0; left: 0; right: 0; bottom: 0;
    background-color: #cbd5e1;
    transition: 0.3s;
    border-radius: 24px;
}
.ref-slider:before {
    position: absolute;
    content: "";
    height: 18px;
    width: 18px;
    left: 3px;
    bottom: 3px;
    background-color: white;
    transition: 0.3s;
    border-radius: 50%;
}
.ref-switch input:checked + .ref-slider {
    background-color: #2c7fc3;
}
.ref-switch input:checked + .ref-slider:before {
    transform: translateX(24px);
}

.ref-radio-group {
    display: flex;
    gap: 16px;
}
.ref-radio-option {
    display: flex;
    align-items: center;
    gap: 6px;
    cursor: pointer;
    font-size: 13px;
    font-weight: 500;
    color: #475569;
}

.ref-inputs-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.ref-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}
.ref-input-prefix,
.ref-input-suffix {
    position: absolute;
    background: #f8fafc;
    color: #64748b;
    padding: 0 10px;
    height: 100%;
    display: flex;
    align-items: center;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    font-size: 13px;
}
.ref-input-prefix {
    left: 0;
    border-radius: 6px 0 0 6px;
    border-right: none;
}
.ref-input-suffix {
    right: 0;
    border-radius: 0 6px 6px 0;
    border-left: none;
}
.ref-input-field {
    width: 100%;
    padding: 8px 12px;
    font-size: 14px;
    border: 1px solid #cbd5e1;
    border-radius: 6px;
}
.ref-input-wrapper.has-prefix .ref-input-field {
    padding-left: 32px;
}
.ref-input-wrapper.has-suffix .ref-input-field {
    padding-right: 32px;
}

.ref-generate-btn {
    background: #2c7fc3;
    color: #ffffff;
    border: none;
    padding: 10px 16px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: background-color 0.2s;
    width: 100%;
}
.ref-generate-btn:hover {
    background: #233d79;
}

.ref-preview-box {
    display: none;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 12px;
    border-radius: 8px;
    flex-direction: column;
    gap: 6px;
}
.ref-preview-title {
    font-size: 11px;
    font-weight: 700;
    color: #15803d;
    text-transform: uppercase;
}
.ref-preview-link-sec {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
}
.ref-preview-link {
    font-size: 12px;
    font-weight: 600;
    color: #166534;
    word-break: break-all;
}
.ref-copy-btn {
    background: #ffffff;
    border: 1px solid #bbf7d0;
    color: #16a34a;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
.ref-copy-btn:hover {
    background: #dcfce7;
}

.ref-helper-text {
    font-size: 11px;
    color: #64748b;
    line-height: 1.4;
    margin: 0;
}

.ref-modal-footer {
    padding: 12px 20px;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 10px;
    background: #f8fafc;
}

.ref-btn-save {
    background: #2c7fc3;
    color: #ffffff;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
}
.ref-btn-save:hover {
    background: #233d79;
}
.ref-btn-cancel {
    background: #e2e8f0;
    color: #475569;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-weight: 600;
    font-size: 13px;
    cursor: pointer;
}
.ref-btn-cancel:hover {
    background: #cbd5e1;
}

.ref-toast {
    position: fixed;
    bottom: 24px;
    right: 24px;
    background: #1e293b;
    color: #ffffff;
    padding: 10px 18px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 10px 15px -3px rgba(0,0,0,0.3);
    z-index: 9999999;
    transform: translateY(100px);
    opacity: 0;
    transition: 0.3s ease;
}
.ref-toast.show {
    transform: translateY(0);
    opacity: 1;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

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

<script>
$(document).on('click', '.alert-dropdown-item', function() {
    let href = $(this).data('href');
    if(href) {
        window.location.href = href;
    }
});

// Alert Bell Click Toggle
function toggleAlertDropdown(e) {
    e.stopPropagation();
    var menu = document.getElementById('alertDropdownMenu');
    menu.classList.toggle('show');
    // Close notification dropdown if open
    $('#notifDropdown').hide();
}

// Close alert dropdown when clicking outside
$(document).on('click', function(e) {
    if (!$(e.target).closest('#alertBellWrapper').length) {
        $('#alertDropdownMenu').removeClass('show');
    }
});

$(document).on('click', '#sellerLogoutBtn', function(e) {
    e.preventDefault();
    $.ajax({
        url: "{{ route('seller.logout') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}"
        },
        success: function() {
            localStorage.clear();
            sessionStorage.clear();
            window.location.replace("{{ url('/seller/login') }}");
        },
        error: function() {
            window.location.replace("{{ url('/seller/login') }}");
        }
    });
});

window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});

function openReferralModal() {
    const modal = document.getElementById('referralSettingsModal');
    modal.style.display = 'flex';
    setTimeout(() => {
        modal.classList.add('show');
    }, 10);
    toggleReferralInputs();
}

function closeReferralModal() {
    const modal = document.getElementById('referralSettingsModal');
    modal.classList.remove('show');
    setTimeout(() => {
        modal.style.display = 'none';
    }, 300);
}

function toggleReferralInputs() {
    const isEnabled = document.getElementById('refEnabled').checked;
    const rewardTypePercentage = document.getElementById('rewardTypePercentage');
    const rewardTypeFixed = document.getElementById('rewardTypeFixed');
    
    const percentageInput = document.getElementById('refPercentage');
    const fixedInput = document.getElementById('refFixedAmount');
    
    const wrapperPercentage = document.getElementById('wrapperPercentage');
    const wrapperFixed = document.getElementById('wrapperFixed');
    
    if (!isEnabled) {
        rewardTypePercentage.disabled = true;
        rewardTypeFixed.disabled = true;
        percentageInput.disabled = true;
        fixedInput.disabled = true;
        
        wrapperPercentage.style.opacity = '0.5';
        wrapperFixed.style.opacity = '0.5';
        document.querySelectorAll('.ref-radio-option').forEach(el => el.style.opacity = '0.5');
    } else {
        rewardTypePercentage.disabled = false;
        rewardTypeFixed.disabled = false;
        document.querySelectorAll('.ref-radio-option').forEach(el => el.style.opacity = '1');
        
        if (rewardTypePercentage.checked) {
            percentageInput.disabled = false;
            fixedInput.disabled = true;
            wrapperPercentage.style.opacity = '1';
            wrapperFixed.style.opacity = '0.5';
        } else {
            percentageInput.disabled = true;
            fixedInput.disabled = false;
            wrapperPercentage.style.opacity = '0.5';
            wrapperFixed.style.opacity = '1';
        }
    }
}

function generateReferralLink() {
    const slug = "{{ $seller->slug ?? $seller->username ?? $seller->id ?? '332994' }}";
    const refUrl = window.location.origin + '/refer/' + slug;
    
    const previewBox = document.getElementById('refPreviewBox');
    const previewLink = document.getElementById('refPreviewLink');
    
    previewLink.href = refUrl;
    previewLink.innerText = refUrl;
    
    previewBox.style.display = 'block';
    setTimeout(() => {
        previewBox.classList.add('show');
    }, 10);
}

function copyReferralLink() {
    const previewLink = document.getElementById('refPreviewLink');
    if (!previewLink.innerText) return;
    
    navigator.clipboard.writeText(previewLink.innerText).then(() => {
        const copyIcon = document.getElementById('refCopyIcon');
        copyIcon.className = 'fa-solid fa-check text-success';
        
        showToast('Referral link copied to clipboard!');
        
        setTimeout(() => {
            copyIcon.className = 'fa-regular fa-copy';
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy text: ', err);
    });
}

function showToast(message) {
    const toast = document.getElementById('refToast');
    const toastMsg = document.getElementById('refToastMessage');
    
    toastMsg.innerText = message;
    toast.style.display = 'flex';
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);
    
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => {
            toast.style.display = 'none';
        }, 300);
    }, 3000);
}

function saveReferralSettings(event) {
    event.preventDefault();
    
    const isEnabled = document.getElementById('refEnabled').checked ? 1 : 0;
    const rewardType = document.querySelector('input[name="reward_type"]:checked').value;
    const percentage = document.getElementById('refPercentage').value;
    const fixedAmount = document.getElementById('refFixedAmount').value;
    
    const saveBtn = document.querySelector('.ref-btn-save');
    const originalText = saveBtn.innerText;
    saveBtn.disabled = true;
    saveBtn.innerText = 'Saving...';
    
    fetch("{{ route('seller.products.save.referral.settings') }}", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            enabled: isEnabled,
            reward_type: rewardType,
            percentage: percentage,
            fixed_amount: fixedAmount
        })
    })
    .then(res => res.json())
    .then(data => {
        saveBtn.disabled = false;
        saveBtn.innerText = originalText;
        
        if (data.success) {
            showToast('Referral settings saved successfully!');
            setTimeout(() => {
                closeReferralModal();
            }, 1000);
        } else {
            alert('Failed to save settings: ' + data.message);
        }
    })
    .catch(err => {
        saveBtn.disabled = false;
        saveBtn.innerText = originalText;
        console.error('Error saving settings:', err);
        alert('An error occurred. Please try again.');
    });
}

// Call on load to sync initial inputs
document.addEventListener('DOMContentLoaded', () => {
    toggleReferralInputs();
});
</script>

<script>
function toggleNotification() {
    $('#notifDropdown').toggle();
}

function getIcon(type) {
    switch (type) {
        case 'order': return '🛒';
        case 'product': return '📦';
        case 'enquiry': return '📩';
        case 'payment': return '💰';
        case 'grievance': return '⚠️';
        case 'review': return '⭐';
        case 'lead': return '👥';
        case 'conversion': return '📈';
        case 'contact': return '👁️';
        case 'reminder': return '⏰';
        case 'ai_suggestion': return '💡';
        default: return '🔔';
    }
}

function loadNotifications() {
    $.get('/seller/notifications', function(data) {
        let systemAlertsHtml = '';
        let bellHtml = '';
        let unread = 0;
        let systemUnread = 0;
        
        let expiryAlertsCount = @json($expiryAlerts ?? []).length;

        const sirenIcon = `<div class="mini-siren-icon" style="margin-right:12px; min-width:24px; height:24px; border-radius:4px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <div class="mini-siren-light" style="width:10px; height:12px; background:linear-gradient(180deg, #ff2d2d, #d90429); border-radius:5px 5px 2px 2px; display:flex; align-items:center; justify-content:center;">
                    <div class="mini-siren-inner" style="width:2px; height:4px; background:#fff; border-radius:1px; position:relative;">
                        <div style="width:2px; height:2px; background:#fff; border-radius:50%; position:absolute; bottom:-3px; left:0;"></div>
                    </div>
                </div>
            </div>`;

        if (data.length === 0) {
            systemAlertsHtml = `<div class="alert-dropdown-empty" style="padding:15px; text-align:center;"><i class="fa fa-check-circle" style="font-size:24px; color:#ddd; margin-bottom:10px;"></i><p style="margin:0; color:#888;">No system alerts</p></div>`;
            bellHtml = `<li class="text-center text-muted p-3">No Notifications</li>`;
        } else {
            data.forEach(function(n) {
                if (n.is_read == 0) unread++;
                if (n.is_read == 0) systemUnread++;

                // Build System Alerts HTML (For Alert Bell)
                systemAlertsHtml += `
                    <div class="alert-dropdown-item system-alert-item" style="padding: 12px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: flex-start;" onclick="markAndRedirect(${n.id}, '${n.link}')">
                        ${sirenIcon}
                        <div class="alert-item-content" style="flex:1;">
                            <div class="alert-item-title" style="${n.is_read == 0 ? 'font-weight:600; color:#1f2937;' : 'font-weight:normal; color:#4b5563;'} font-size:13px; line-height:1.4;">${n.message}</div>
                            <div style="font-size:11px; color:#888; margin-top:2px;">System Alert • ${timeAgo(n.created_at)}</div>
                        </div>
                    </div>`;

                // Build Normal Notifications HTML (For Notification Bell)
                bellHtml += `
                    <li class="${n.is_read == 0 ? 'fw-bold bg-light' : ''} d-flex align-items-start p-3" 
                        onclick="markAndRedirect(${n.id}, '${n.link}')" style="border-bottom:1px solid #eee; cursor:pointer;">
                        <span style="font-size: 16px; margin-right: 12px; margin-top: 2px;">${getIcon(n.type)}</span>
                        <div style="flex:1;">
                            <div style="font-size:13px; color:#1f2937; line-height:1.4; ${n.is_read == 0 ? 'font-weight:600;' : 'font-weight:normal;'}">${n.message}</div>
                            <small style="font-size:11px; color:#9ca3af; font-weight:normal;">${timeAgo(n.created_at)}</small>
                        </div>
                    </li>`;
            });
        }
        
        // Update Alert Bell (Siren)
        $('#systemAlertsList').html(systemAlertsHtml);
        let totalAlerts = expiryAlertsCount + systemUnread;
        if(totalAlerts > 0){
            $('#alertBellIcon').addClass('has-alerts');
            $('#alertCountBadge').text(totalAlerts).show();
        } else {
            $('#alertBellIcon').removeClass('has-alerts');
            $('#alertCountBadge').hide();
        }

        // Update Notification Bell
        $('#notifList').html(bellHtml);
        if (unread > 0) {
            $('#notifCount').text(unread).show();
        } else {
            $('#notifCount').hide();
        }
    });
}

function markAndRedirect(id, link) {
    if (typeof id === 'number' || !isNaN(id)) {
        $.post('/seller/notifications/read', {
            _token: '{{ csrf_token() }}',
            id: id
        });
    }
    window.location.href = link;
}

$(document).on('click', '#clearAlerts', function(e) {
    e.preventDefault();
    if (!confirm('Are you sure you want to clear all alerts?')) {
        return;
    }
    $.ajax({
        url: '/seller/notifications/clear',
        type: 'POST',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.status) {
                $('#systemAlertsList').html('<div class="alert-dropdown-empty"><i class="fa fa-check-circle" style="font-size:24px; color:#ddd; margin-bottom:10px;"></i><p style="margin:0; color:#888;">No system alerts</p></div>');
                $('#alertCountBadge').hide();
                $('#alertBellIcon').removeClass('has-alerts');
            } else {
                alert(response.message || 'Something went wrong');
            }
        },
        error: function(xhr) {
            console.log(xhr.responseText);
            alert('Failed to clear notifications');
        }
    });
});



function timeAgo(date) {
    let seconds = Math.floor((new Date() - new Date(date)) / 1000);
    let interval = seconds / 3600;
    if (interval > 1) return Math.floor(interval) + " hrs ago";
    interval = seconds / 60;
    if (interval > 1) return Math.floor(interval) + " min ago";
    return "Just now";
}

setInterval(loadNotifications, 5000);

$(document).ready(function() {
    loadNotifications();

    const sellerId = "{{ auth()->guard('seller')->id() }}";
    if (sellerId && window.fRef) {
        const sellerNotifsRef = window.fRef(window.db, "notifications/seller/" + sellerId);
        const watchSellerLast = window.fQuery(sellerNotifsRef, window.fLimit(1));
        let lastSellerNotifId = null;
        let initialSellerLoad = true;
        const dingSound = new Audio("https://assets.mixkit.co/sfx/preview/mixkit-software-interface-back-2576.mp3");

        window.fOnChildAdded(watchSellerLast, (snapshot) => {
            const data = snapshot.val();
            if (!data) return;
            const notifKey = snapshot.key;
            if (initialSellerLoad) {
                initialSellerLoad = false;
                lastSellerNotifId = notifKey;
                return;
            }
            if (notifKey === lastSellerNotifId) return;
            lastSellerNotifId = notifKey;

            // Increment the notification count badge
            let count = parseInt($('#notifCount').text()) || 0;
            $('#notifCount').text(++count).show();

            // Also increment alert count badge
            let alertCount = parseInt($('#alertCountBadge').text()) || 0;
            $('#alertCountBadge').text(++alertCount).show();
            $('#alertBellIcon').addClass('has-alerts');

            // Play sound and show toast
            dingSound.play();
            if (typeof toastr !== 'undefined') {
                toastr.success(data.message, data.type ? data.type.toUpperCase() : 'Notification');
            }

            // Prepend to notifications list in UI
            const newNotifHtml = `
                <li class="fw-bold bg-light d-flex align-items-start p-3" 
                    onclick="markAndRedirect('${notifKey}', '${data.link || '#'}')" style="border-bottom:1px solid #eee; cursor:pointer;">
                    <span style="font-size: 16px; margin-right: 12px; margin-top: 2px;">${getIcon(data.type)}</span>
                    <div style="flex:1;">
                        <div style="font-size:13px; color:#1f2937; line-height:1.4; font-weight:600;">${data.message}</div>
                        <small style="font-size:11px; color:#9ca3af; font-weight:normal;">Just now</small>
                    </div>
                </li>`;
            
            // Remove empty placeholder if any
            $('#notifList').find('.text-muted').remove();
            $('#notifList').find('li:contains("No new notifications")').remove();
            $('#notifList').prepend(newNotifHtml);

            // Also prepend to systemAlertsList
            const sirenIcon = `<div class="mini-siren-icon" style="margin-right:12px; min-width:24px; height:24px; border-radius:4px; display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                <div class="mini-siren-light" style="width:10px; height:12px; background:linear-gradient(180deg, #ff2d2d, #d90429); border-radius:5px 5px 2px 2px; display:flex; align-items:center; justify-content:center;">
                    <div class="mini-siren-inner" style="width:2px; height:4px; background:#fff; border-radius:1px; position:relative;">
                        <div style="width:2px; height:2px; background:#fff; border-radius:50%; position:absolute; bottom:-3px; left:0;"></div>
                    </div>
                </div>
            </div>`;
            const newSystemAlertHtml = `
                <div class="alert-dropdown-item system-alert-item" style="padding: 12px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: flex-start;" onclick="markAndRedirect('${notifKey}', '${data.link || '#'}')">
                    ${sirenIcon}
                    <div class="alert-item-content" style="flex:1;">
                        <div class="alert-item-title" style="font-weight:600; color:#1f2937; font-size:13px; line-height:1.4;">${data.message}</div>
                        <div style="font-size:11px; color:#888; margin-top:2px;">System Alert • Just now</div>
                    </div>
                </div>`;
            $('#systemAlertsList').find('.alert-dropdown-empty').remove();
            $('#systemAlertsList').prepend(newSystemAlertHtml);
        });
    }
});

$(document).click(function(e) {
    if (!$(e.target).closest('.notification-wrapper').length) {
        $('#notifDropdown').hide();
    }
});

function goBack() { window.history.back(); }
function goForward() { window.history.forward(); }
</script>

<script>
let searchTimer;
let selectedIndex = -1;
let currentResults = [];

// Complete Seller Panel Route Mappings
const sellerRoutes = {
    // Dashboard
    'dashboard': '{{ route("seller.dashboard") }}',
    'seller dashboard': '{{ route("seller.dashboard") }}',
    
    // Products/Services
    'product': '{{ route("seller.products.mis.dashboard") }}',
    'products': '{{ route("seller.products.mis.dashboard") }}',
    'service': '{{ route("seller.products.mis.dashboard") }}',
    'services': '{{ route("seller.products.mis.dashboard") }}',
    'course': '{{ route("seller.products.mis.dashboard") }}',
    'courses': '{{ route("seller.products.mis.dashboard") }}',
    'mis': '{{ route("seller.products.mis.dashboard") }}',
    
    // Categories
    'categories': '{{ route("seller.categories.index") }}',
    'category list': '{{ route("seller.categories.index") }}',
    'add category': '{{ route("seller.categories.create") }}',
    
    // Customers
    'customer': '{{ route("seller.customer.mis") }}',
    'customers': '{{ route("seller.customer.mis") }}',
    'customer mis': '{{ route("seller.customer.mis") }}',
    'customer list': '{{ route("seller.customer.list") }}',
    'add customer': '{{ route("seller.customer.create") }}',
    
    // Orders
    'order': '{{ route("seller.orders.mis") }}',
    'orders': '{{ route("seller.orders.mis") }}',
    'order mis': '{{ route("seller.orders.mis") }}',
    'all orders': '{{ route("seller.orders.index") }}',
    'payment handling': '{{ route("seller.paymenthandle") }}',
    'shipping': '{{ route("seller.shipping") }}',
    
    // Seller Profile
    'profile': '{{ route("seller.organization") }}',
    'seller profile': '{{ route("seller.organization") }}',
    'organization': '{{ route("seller.organization") }}',
    
    // Market Place
    'marketplace': '{{ route("seller.marketplace.category") }}',
    'market place': '{{ route("seller.marketplace.category") }}',
    
    // Enquiry
    'enquiry': '{{ route("seller.enquiry.mis") }}',
    'enquiries': '{{ route("seller.enquiry.mis") }}',
    'enquiry mis': '{{ route("seller.enquiry.mis") }}',
    'enquiry list': '{{ route("seller.enquiry.list") }}',
    
    // Finance
    'finance': '{{ route("seller.financedashboard") }}',
    'earnings': '{{ route("seller.financedashboard") }}',
    'earnings dashboard': '{{ route("seller.financedashboard") }}',
    'transactions': '{{ route("seller.finance.transactions") }}',
    'transaction history': '{{ route("seller.finance.transactions") }}',
    'payout': '{{ route("seller.finance.payouts") }}',
    'payout management': '{{ route("seller.finance.payouts") }}',
    'commission': '{{ route("seller.finance.commission") }}',
    'wallet settlement': '{{ route("seller.finance.wallet") }}',
    
    // Analytics
    'analytics': '{{ route("seller.analytics.sales") }}',
    'sales overview': '{{ route("seller.analytics.sales") }}',
    'conversion funnel': '{{ route("seller.analytics.funnel") }}',
    'customer insights': '{{ route("seller.analytics.customers") }}',
    'campaign roi': '{{ route("seller.analytics.campaigns") }}',
    
    // Grievance
    'grievance': '{{ route("seller.grievance.list") }}'
};

// Get section icon
function getSectionIcon(section) {
    const icons = {
        'dashboard': '🏠',
        'product': '📦',
        'products': '📦',
        'categories': '📂',
        'customer': '👥',
        'order': '🛒',
        'profile': '👤',
        'marketplace': '🏪',
        'enquiry': '📧',
        'finance': '💰',
        'analytics': '📊',
        'grievance': '⚠️'
    };
    return icons[section] || '🔍';
}

// Get group title
function getGroupTitle(type) {
    const titles = {
        'menu': '📋 MENU',
        'order': '🛒 ORDERS',
        'customer': '👥 CUSTOMERS',
        'product': '📦 PRODUCTS',
        'enquiry': '📧 ENQUIRIES',
        'finance': '💰 FINANCE',
        'analytics': '📊 ANALYTICS'
    };
    return titles[type] || '🔍 RESULTS';
}

// Find direct matches from sidebar menu
function findDirectMatches(searchTerm) {
    const matches = [];
    const term = searchTerm.toLowerCase().trim();
    
    for (const [keyword, route] of Object.entries(sellerRoutes)) {
        if (keyword.includes(term) || term.includes(keyword) || 
            (term.length > 2 && keyword.indexOf(term) !== -1)) {
            matches.push({
                type: 'menu',
                name: keyword.charAt(0).toUpperCase() + keyword.slice(1),
                route: route,
                icon: getSectionIcon(keyword.split(' ')[0])
            });
        }
    }
    
    // Remove duplicates
    const uniqueMatches = [];
    const seenRoutes = new Set();
    for (const match of matches) {
        if (!seenRoutes.has(match.route)) {
            seenRoutes.add(match.route);
            uniqueMatches.push(match);
        }
    }
    
    return uniqueMatches.slice(0, 8);
}

// Perform search
function performSearch(query) {
    if (query.length < 2) {
        $('#searchResults').hide();
        return;
    }
    
    // First check for direct menu matches
    const directMatches = findDirectMatches(query);
    
    if (directMatches.length > 0) {
        displayResults({ menu: directMatches });
        return;
    }
    
    // Then fetch from backend
    $('#searchResults').html(`<div class="loading-results"><i class="fa-solid fa-spinner"></i> Searching...</div>`).show();
    
    $.get("{{ route('seller.global.search') }}", { q: query }, function(data) {
        displayResults(data);
    }).fail(function() {
        $('#searchResults').html(`<div class="no-results"><i class="fa-solid fa-exclamation-circle"></i> Search failed. Please try again.</div>`).show();
    });
}

// Display search results
function displayResults(data) {
    let html = '';
    let hasResults = false;
    
    // Organize results by type
    const groups = {
        menu: [],
        order: [],
        customer: [],
        product: [],
        enquiry: [],
        finance: [],
        analytics: []
    };
    
    if (data.menu && data.menu.length > 0) {
        groups.menu = data.menu;
        hasResults = true;
    }
    
    if (data.orders && data.orders.length > 0) {
        groups.order = data.orders;
        hasResults = true;
    }
    
    if (data.customers && data.customers.length > 0) {
        groups.customer = data.customers;
        hasResults = true;
    }
    
    if (data.products && data.products.length > 0) {
        groups.product = data.products;
        hasResults = true;
    }
    
    if (data.enquiries && data.enquiries.length > 0) {
        groups.enquiry = data.enquiries;
        hasResults = true;
    }
    
    // Render each group
    for (const [group, items] of Object.entries(groups)) {
        if (items.length > 0) {
            html += `<div class="search-group-title">${getGroupTitle(group)}</div>`;
            items.slice(0, 5).forEach((item, idx) => {
                html += `
                    <div class="search-item" data-url="${item.route}" data-index="${idx}">
                        <div class="search-item-icon">
                            <span>${item.icon || getSectionIcon(group)}</span>
                        </div>
                        <div class="search-item-content">
                            <div class="search-item-title">${highlightText(item.name, $('#globalSearch').val())}</div>
                            <div class="search-item-subtitle">${item.subtitle || ''}</div>
                        </div>
                        <i class="fa-solid fa-chevron-right" style="font-size: 12px; color: #ccc;"></i>
                    </div>
                `;
            });
        }
    }
    
    if (!hasResults) {
        html = `<div class="no-results"><i class="fa-solid fa-search"></i> No results found for "${$('#globalSearch').val()}"</div>`;
    }
    
    $('#searchResults').html(html).show();
    
    // Add click handlers
    $('.search-item').on('click', function() {
        const url = $(this).data('url');
        if (url) {
            window.location.href = url;
        }
    });
}

// Highlight matching text
function highlightText(text, search) {
    if (!search || !text) return text;
    const regex = new RegExp(`(${search.replace(/[.*+?^${}()|[\]\\]/g, '\\$&')})`, 'gi');
    return text.replace(regex, `<strong style="background: #ffeb3b; padding: 0 2px; border-radius: 3px;">$1</strong>`);
}

// Handle input with debounce
$('#globalSearch').on('keyup', function(e) {
    const query = $(this).val().trim();
    
    if (e.which === 13) { // Enter key
        doSearch();
        return;
    }
    
    if (e.which === 27) { // Escape key
        $('#searchResults').hide();
        $(this).blur();
        return;
    }
    
    clearTimeout(searchTimer);
    
    if (query.length < 2) {
        $('#searchResults').hide();
        return;
    }
    
    searchTimer = setTimeout(() => {
        performSearch(query);
    }, 300);
});

// Handle keyboard navigation
$('#globalSearch').on('keydown', function(e) {
    const items = $('.search-item');
    const currentActive = $('.search-item.active');
    
    if (e.which === 38) { // Up arrow
        e.preventDefault();
        if (selectedIndex <= 0) {
            selectedIndex = -1;
            items.removeClass('active');
        } else {
            selectedIndex--;
            items.removeClass('active');
            $(items[selectedIndex]).addClass('active');
            $(items[selectedIndex])[0]?.scrollIntoView({ block: 'nearest' });
        }
    } else if (e.which === 40) { // Down arrow
        e.preventDefault();
        if (selectedIndex >= items.length - 1) {
            selectedIndex = items.length - 1;
        } else {
            selectedIndex++;
        }
        items.removeClass('active');
        $(items[selectedIndex]).addClass('active');
        $(items[selectedIndex])[0]?.scrollIntoView({ block: 'nearest' });
    } else if (e.which === 13 && selectedIndex >= 0) { // Enter on selected
        e.preventDefault();
        const activeItem = $('.search-item.active');
        if (activeItem.length) {
            const url = activeItem.data('url');
            if (url) window.location.href = url;
        }
    }
});

// Close dropdown when clicking outside
$(document).click(function(e) {
    if (!$(e.target).closest('.dashboard-search-sec').length) {
        $('#searchResults').hide();
        selectedIndex = -1;
    }
});

// Focus handler
$('#globalSearch').on('focus', function() {
    const query = $(this).val().trim();
    if (query.length >= 2) {
        performSearch(query);
    }
});

// Main search function
function doSearch() {
    let keyword = $('#globalSearch').val().trim();
    if (keyword === '') {
        alert('Please enter search keyword');
        return;
    }
    window.location.href = "{{ route('seller.global.search') }}?q=" + encodeURIComponent(keyword);
}

// Geolocation Detection
function detectLocation() {
    if (!navigator.geolocation) {
        alert("Geolocation is not supported by your browser.");
        return;
    }

    const locationNameEl = document.getElementById('headerLocationName');
    const originalText = locationNameEl.textContent;
    locationNameEl.textContent = "Detecting...";

    navigator.geolocation.getCurrentPosition(
        (position) => {
            const lat = position.coords.latitude;
            const lon = position.coords.longitude;
            
            // Call OpenStreetMap Nominatim reverse lookup
            fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lon}&zoom=10&addressdetails=1`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.address) {
                        const address = data.address;
                        const city = address.city || address.town || address.village || address.suburb || address.county || "Unknown";
                        const state = address.state || "";
                        const locationStr = state ? `${city}, ${state}` : city;
                        
                        locationNameEl.textContent = locationStr;
                        localStorage.setItem('vshop_seller_location', locationStr);
                    } else {
                        locationNameEl.textContent = "Location not found";
                        setTimeout(() => { locationNameEl.textContent = originalText; }, 3000);
                    }
                })
                .catch(err => {
                    console.error("Error geocoding location:", err);
                    locationNameEl.textContent = "Error detecting";
                    setTimeout(() => { locationNameEl.textContent = originalText; }, 3000);
                });
        },
        (error) => {
            console.error("Error getting location:", error);
            locationNameEl.textContent = "Access Denied";
            setTimeout(() => { locationNameEl.textContent = originalText; }, 3000);
        }
    );
}

// Load cached location
$(document).ready(function() {
    const cachedLocation = localStorage.getItem('vshop_seller_location');
    if (cachedLocation) {
        $('#headerLocationName').text(cachedLocation);
    }
});
</script>