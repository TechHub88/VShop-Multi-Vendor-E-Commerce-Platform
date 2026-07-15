
<!-- FONT AWESOME (IMPORTANT) -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<!-- SWEETALERT2 (for boost modal alerts) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<meta name="csrf-token" content="{{ csrf_token() }}">


@php
/*
|--------------------------------------------------------------------------
| WALLET BALANCES SYNC
|--------------------------------------------------------------------------
*/
$seller = auth()->guard('seller')->user();
if ($seller) {
    $sellerWallet = \App\Models\Wallet::where('seller_id', $seller->id)->where('user_type', 'seller')->first();
    if ($sellerWallet) {
        session(['seller_dummy_wallet' => $sellerWallet->balance]);
    } else {
        $sellerWallet = \App\Models\Wallet::create([
            'seller_id' => $seller->id,
            'user_type' => 'seller',
            'balance' => 15450.00,
            'frozen_balance' => 0
        ]);
        session(['seller_dummy_wallet' => 15450.00]);
    }
}

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
                        <img src="https://vshop.zelofx.com/public/images/new-logo-vridhee.png" alt="Boost Now" style="width: 100%; height: 60px; display: block;">
                    </div>

                    <!-- LEFT SIDE -->
                    <div class="d-flex align-items-center">
                        <div class="dashboard-search-sec position-relative" style="border-radius: 6px !important;">
                            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #6c757d;"></i>
                            <input type="text"
                                   id="globalSearch"
                                   class="form-control"
                                   placeholder="Search products, orders, customers, enquiries..."
                                   autocomplete="off"
                                   style="border-radius: 6px !important; padding-left: 40px;">
                            <!-- 🔥 SEARCH DROPDOWN -->
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

                        <!-- WALLET -->
                        @php
                        $walletBalance = 0;
                        if (auth()->guard('seller')->check()) {
                            $wallet = \App\Models\Wallet::where('seller_id', auth()->guard('seller')->id())->first();
                            if ($wallet) {
                                $walletBalance = $wallet->balance;
                            }
                        }
                        @endphp
                        <div class="header-wallet-sec d-flex align-items-center cursor-pointer me-2" title="Wallet Balance" style="background: #f8f9fa; padding: 5px 12px; border-radius: 20px; border: 1px solid #e0e0e0;">
                            <i class="fa-solid fa-wallet text-primary me-2" style="font-size: 16px;"></i>
                            <div class="d-flex flex-column" style="line-height: 1.2;">
                                <span class="text-muted" style="font-size: 9px; text-transform: uppercase;">Wallet Balance</span>
                                <span class="fw-bold text-dark" id="headerWalletBalance" style="font-size: 13px;">₹ {{ number_format($walletBalance, 2) }}</span>
                            </div>
                        </div>

                        {{-- FLOATING REFERRAL BUTTON --}}
                        <button type="button" class="boost-btn" title="Boost Performance" onclick="openBoostModal()">
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
                            <button class="notif-btn" onclick="toggleNotification()">
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
                                <button class="dropdown-toggle profile-box" type="button" data-bs-toggle="dropdown">
                                    <img src="{{ 
                                        Auth::guard('seller')->user()->profile_picture
                                        ? asset(Auth::guard('seller')->user()->profile_picture)
                                        : asset('vtravelassets/images/avatar.png')
                                    }}" class="w-100">
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

                    {{-- YOUR REFERRAL CODE --}}
                    <div class="ref-form-group" style="margin-top: 15px;">
                        <label class="ref-form-label" style="margin-bottom: 8px; display: block;">Your Referral Code</label>
                        <div style="display: flex; align-items: center; gap: 10px; background: #f0f9ff; border: 2px dashed #2563eb; border-radius: 10px; padding: 12px 16px;">
                            <i class="fa-solid fa-ticket" style="font-size: 20px; color: #2563eb;"></i>
                            <span id="sellerReferralCode" style="font-size: 18px; font-weight: 700; color: #1e3a8a; letter-spacing: 2px; flex: 1;">{{ $seller->referral_code ?? 'N/A' }}</span>
                            <button type="button" onclick="copyReferralCode()" style="background: #2563eb; color: #fff; border: none; border-radius: 8px; padding: 8px 14px; cursor: pointer; font-size: 13px; font-weight: 600; display: flex; align-items: center; gap: 5px; transition: all 0.2s;" onmouseover="this.style.background='#1d4ed8'" onmouseout="this.style.background='#2563eb'">
                                <i class="fa-regular fa-copy" id="refCodeCopyIcon"></i> Copy
                            </button>
                        </div>
                        <p style="font-size: 11px; color: #64748b; margin-top: 6px;">Share this code with other sellers to earn rewards when they register.</p>
                    </div>

                    <div class="ref-form-group" style="display: none !important;">
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

<!-- ============================================== -->
<!-- BOOST MODAL -->
<!-- ============================================== -->
<div class="modal fade" id="boostModal" tabindex="-1" aria-labelledby="boostModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-light">
        <h5 class="modal-title fw-bold" id="boostModalLabel">
            <i class="fa-solid fa-rocket text-primary me-2"></i> Boost Your Products
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p class="text-muted small mb-3">Select the products you want to boost. Boosted products will appear in the Search & Discovery admin page.</p>
        
        <!-- Summary Status Bar -->
        <div class="alert alert-info d-flex flex-wrap justify-content-between align-items-center py-2 px-3 mb-3 gap-2" style="font-size: 13px; border-radius: 8px;">
            <div>
                <strong>Wallet Balance:</strong> <span id="boostModalWalletBalance" class="fw-bold">₹0.00</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <strong>Boost Duration:</strong>
                <select id="boostModalDays" class="form-select form-select-sm d-inline-block w-auto" onchange="updateBoostCostCalculation()">
                    <option value="1">1 Day</option>
                    <option value="3">3 Days</option>
                    <option value="7" selected>7 Days</option>
                    <option value="15">15 Days</option>
                    <option value="30">30 Days</option>
                </select>
            </div>
            <div>
                <strong>Total Selected:</strong> <span id="boostModalSelectedCount" class="badge bg-primary fs-12">0</span>
            </div>
            <div>
                <strong>Total Cost:</strong> <span id="boostModalTotalCost" class="fw-bold text-danger">₹0.00</span>
            </div>
        </div>

        <div id="boostLoading" class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2 text-muted">Loading products...</p>
        </div>

        <form id="boostForm" style="display: none;">
            <div class="table-responsive">
                <table class="table table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <input class="form-check-input" type="checkbox" id="boostSelectAll" style="appearance: auto !important; position: static !important; opacity: 1 !important; width: 1.2rem; height: 1.2rem; cursor: pointer; display: inline-block;">
                                    </div>
                                    <span>Product Info</span>
                                </div>
                            </th>
                            <th width="120" class="text-center">Per Day Cost</th>
                            <th width="120" class="text-center">Duration</th>
                            <th width="120" class="text-center">Current Status</th>
                        </tr>
                    </thead>
                    <tbody id="boostProductList">
                        <!-- Products will be injected here via AJAX -->
                    </tbody>
                </table>
            </div>
        </form>
      </div>
      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="btnSubmitBoost" onclick="submitBoostProducts()">
            Apply Boost <i class="fa-solid fa-bolt ms-1"></i>
        </button>
      </div>
    </div>
  </div>
</div>

<style>
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

.alert-bell-icon.has-alerts .bell-siren-icon {
    animation: alertPulse 2s infinite ease-in-out !important;
}
@keyframes alertPulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.15); }
    100% { transform: scale(1); }
}

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

.alert-dropdown-menu.show {
    display: flex !important;
    flex-direction: column !important;
}

.clear-btn-pro {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: #fff;
    border: 1px solid #e5e7eb;
    color: #dc3545;
    font-size: 12px;
    font-weight: 500;
    padding: 5px 10px;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.clear-btn-pro:hover {
    background: #dc3545;
    color: #fff;
    border-color: #dc3545;
}

.clear-btn-pro i {
    font-size: 11px;
}

/* =========================================================
   ALERT BELL ICON STYLES
========================================================= */
.alert-bell-wrapper {
    position: relative;
    display: inline-block;
}

.bell-siren-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.siren-light {
    width: 18px;
    height: 20px;
    background: linear-gradient(180deg, #ff2d2d, #d90429);
    border-radius: 10px 10px 4px 4px;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: inset -1px 0 0 rgba(0,0,0,.08), 0 1px 3px rgba(255,0,0,.18);
}

.siren-inner {
    width: 4px;
    height: 8px;
    background: #fff;
    border-radius: 3px;
    position: relative;
}

.siren-inner::after {
    content: '';
    width: 4px;
    height: 4px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    bottom: -6px;
    left: 0;
}

.siren-base {
    width: 22px;
    height: 6px;
    background: #555;
    border-radius: 3px;
    margin-top: 2px;
    position: relative;
}

.siren-base::after {
    content: '';
    position: absolute;
    top: -3px;
    left: 3px;
    width: 16px;
    height: 4px;
    background: #bdbdbd;
    border-radius: 2px;
}

.alert-bell-icon {
    position: relative;
    width: 48px;
    height: 48px;
    background: #fff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.alert-bell-icon:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.alert-bell-icon.has-alerts {
    background: #fff5f5;
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

.alert-count {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #ff3b30;
    color: white;
    font-size: 11px;
    font-weight: bold;
    min-width: 18px;
    height: 18px;
    border-radius: 9px;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 0 5px;
    font-family: monospace;
    box-shadow: 0 1px 3px rgba(0,0,0,0.2);
}

.alert-dropdown-menu {
    position: absolute;
    top: 55px;
    right: 0;
    width: 300px;
    max-height: 350px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.25s ease;
    z-index: 1000;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.alert-bell-wrapper:hover .alert-dropdown-menu {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.alert-dropdown-header {
    padding: 12px 16px;
    background: linear-gradient(135deg, #ff3b30, #dc2626);
    color: white;
    font-weight: 600;
    font-size: 13px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.alert-dropdown-header i {
    font-size: 14px;
}

.alert-dropdown-body {
    flex: 1;
    max-height: 300px;
    overflow-y: auto;
}

.alert-dropdown-body::-webkit-scrollbar {
    width: 5px;
}

.alert-dropdown-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.alert-dropdown-body::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.alert-dropdown-body::-webkit-scrollbar-thumb:hover {
    background: #aaa;
}

.alert-dropdown-header-bar {
    padding: 10px 14px;
    border-bottom: 1px solid #eee;
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #fff;
    position: sticky;
    top: 0;
    z-index: 1;
}

.alert-dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 14px 16px;
    border-bottom: 1px solid #f0f0f0;
    transition: background 0.2s ease;
    cursor: pointer;
}

.alert-dropdown-item:hover {
    background: #fef2f2;
}

.alert-item-icon {
    width: 32px;
    height: 32px;
    min-width: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
}

.alert-item-icon.delayed-icon {
    background: #fff3e0;
    color: #f59e0b;
}

.alert-item-icon.document-icon {
    background: #fee2e2;
}

.mini-siren-icon {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.mini-siren-light {
    width: 12px;
    height: 14px;
    background: linear-gradient(180deg, #ff2d2d, #d90429);
    border-radius: 7px 7px 3px 3px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.mini-siren-inner {
    width: 3px;
    height: 5px;
    background: #fff;
    border-radius: 2px;
    position: relative;
}

.mini-siren-inner::after {
    content: '';
    width: 3px;
    height: 2px;
    background: #fff;
    border-radius: 50%;
    position: absolute;
    bottom: -4px;
    left: 0;
}

.mini-siren-base {
    width: 14px;
    height: 3px;
    background: #555;
    border-radius: 2px;
    margin-top: 1px;
}

.alert-item-content {
    flex: 1;
}

.alert-item-title {
    font-weight: 600;
    font-size: 13px;
    color: #1f2937;
}

.alert-item-arrow {
    color: #9ca3af;
    font-size: 12px;
}

.alert-dropdown-empty {
    padding: 30px 20px;
    text-align: center;
    color: #9ca3af;
}

.alert-dropdown-empty i {
    font-size: 32px;
    margin-bottom: 8px;
    color: #10b981;
}

.alert-dropdown-empty p {
    margin: 0;
    font-size: 13px;
}

.notification-wrapper {
    display: flex;
    align-items: center;
}

.notif-btn {
    background: transparent;
    border: none;
    font-size: 18px;
    cursor: pointer;
    position: relative;
}

.notif-badge {
    position: absolute;
    top: -5px;
    right: -8px;
    background: red;
    color: #fff;
    font-size: 10px;
    padding: 3px 6px;
    border-radius: 50%;
}

.notif-dropdown {
    position: absolute;
    top: 35px;
    right: 0;
    width: 320px;
    max-height: 350px;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    display: none;
    z-index: 999;
    overflow: hidden;
}

.notif-header {
    padding: 10px;
    border-bottom: 1px solid #eee;
}

.notif-list {
    list-style: none;
    margin: 0;
    padding: 0;
    max-height: 280px;
    overflow-y: auto;
}

.notif-list li {
    padding: 10px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
}

.notif-list li:hover {
    background: #f5f5f5;
}

.notif-list::-webkit-scrollbar {
    width: 5px;
}

.notif-list::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 10px;
}

.order-badge {
    background: #dc3545;
    color: #fff;
    font-size: 12px;
    padding: 2px 6px;
    border-radius: 50px;
    margin-left: 5px;
}

.search-dropdown {
    position: absolute;
    top: 42px;
    left: 0;
    width: 320px;
    max-height: 400px;
    overflow-y: auto;
    background: #fff;
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    display: none;
    z-index: 9999;
}

.search-item {
    padding: 10px 12px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
    border-bottom: 1px solid #f0f0f0;
    transition: all 0.2s;
}

.search-item:hover {
    background: #f5f5f5;
}

.search-item.active {
    background: #e7f1ff;
}

.search-group-title {
    font-size: 11px;
    font-weight: 700;
    color: #6c757d;
    padding: 8px 12px;
    background: #f8f9fa;
    border-bottom: 1px solid #eee;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.search-item-icon {
    width: 28px;
    height: 28px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f0f2f5;
    border-radius: 8px;
    font-size: 14px;
}

.search-item-content {
    flex: 1;
}

.search-item-title {
    font-size: 13px;
    font-weight: 500;
    color: #1e293b;
}

.search-item-subtitle {
    font-size: 11px;
    color: #6c757d;
    margin-top: 2px;
}

.search-item-badge {
    font-size: 10px;
    padding: 2px 6px;
    border-radius: 4px;
    background: #e9ecef;
    color: #495057;
}

.no-results {
    padding: 20px;
    text-align: center;
    color: #6c757d;
}

.loading-results {
    padding: 20px;
    text-align: center;
    color: #6c757d;
}

.loading-results i {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}


/* Floating top corner button styling */
.ref-floating-btn {
    position: relative !important;
    top: 0px !important;
    right: 0px !important;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff !important;
    border: none;
    border-radius: 50px;
    padding: 11px 22px;
    font-weight: 700;
    font-size: 13px;
    box-shadow: 0 10px 15px -3px rgba(217, 119, 6, 0.3), 0 4px 6px -2px rgba(217, 119, 6, 0.15);
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 999;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: refPulse 2s infinite;
}

.ref-floating-btn:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 15px 20px -5px rgba(217, 119, 6, 0.4), 0 8px 8px -5px rgba(217, 119, 6, 0.2);
    background: linear-gradient(135deg, #fbbf24, #d97706);
}

.ref-floating-btn i {
    font-size: 16px;
}

/* =========================================
   REFERRAL SETTINGS MODAL
   ========================================= */
.ref-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
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
    border-radius: 20px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.3s ease;
    border: 1px solid #e2e8f0;
}

.ref-modal-overlay.show .ref-modal-card {
    transform: translateY(0);
}

.ref-modal-header {
    background: #f0f7ff;
    padding: 20px 24px;
    border-bottom: 1px solid #e0f2fe;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ref-modal-title {
    font-size: 18px;
    font-weight: 800;
    color: #0369a1;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ref-modal-title i {
    font-size: 20px;
}

.ref-close-btn {
    background: transparent;
    border: none;
    font-size: 20px;
    color: #64748b;
    cursor: pointer;
    transition: 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.ref-close-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.ref-modal-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.ref-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.ref-form-label {
    font-weight: 700;
    font-size: 14px;
    color: #1e293b;
    margin: 0;
}

/* Slide toggle */
.ref-toggle-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.ref-toggle-label {
    font-weight: 700;
    font-size: 15px;
    color: #0f172a;
}

.ref-switch {
    position: relative;
    display: inline-block;
    width: 58px;
    height: 28px;
}

.ref-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.ref-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .4s;
    border-radius: 34px;
}

.ref-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.ref-switch input:checked + .ref-slider {
    background-color: #16a34a;
}

.ref-switch input:checked + .ref-slider:before {
    transform: translateX(30px);
}

.ref-slider-text {
    position: absolute;
    right: 8px;
    top: 5px;
    font-size: 10px;
    font-weight: 800;
    color: #475569;
    pointer-events: none;
    text-transform: uppercase;
}

.ref-switch input:checked + .ref-slider .ref-slider-text {
    color: #ffffff;
    right: auto;
    left: 8px;
    content: "ON";
}

/* Radio Group */
.ref-radio-group {
    display: flex;
    gap: 16px;
}

.ref-radio-option {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #475569;
}

.ref-radio-option input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: #0284c7;
}

/* Input Fields row */
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

.ref-input-prefix, .ref-input-suffix {
    position: absolute;
    background: #f1f5f9;
    color: #475569;
    padding: 0 12px;
    height: 100%;
    display: flex;
    align-items: center;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    font-size: 14px;
}

.ref-input-prefix {
    left: 0;
    border-radius: 8px 0 0 8px;
    border-right: none;
}

.ref-input-suffix {
    right: 0;
    border-radius: 0 8px 8px 0;
    border-left: none;
}

.ref-input-field {
    width: 100%;
    padding: 10px 14px;
    font-size: 15px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    transition: 0.2s;
}

.ref-input-field:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.ref-input-wrapper.has-prefix .ref-input-field {
    padding-left: 36px;
}

.ref-input-wrapper.has-suffix .ref-input-field {
    padding-right: 36px;
}

/* Generate Link Section */
.ref-generate-btn {
    background: #0284c7;
    color: #ffffff;
    border: none;
    padding: 12px 18px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: 0.2s;
    width: 100%;
}

.ref-generate-btn:hover {
    background: #0369a1;
}

.ref-preview-box {
    display: none;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 14px;
    border-radius: 12px;
    flex-direction: column;
    gap: 8px;
}

.ref-preview-title {
    font-size: 12px;
    font-weight: 800;
    color: #15803d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ref-preview-link-sec {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.ref-preview-link {
    font-size: 13px;
    font-weight: 700;
    color: #166534;
    word-break: break-all;
    text-decoration: underline;
}

.ref-copy-btn {
    background: #ffffff;
    border: 1px solid #bbf7d0;
    color: #16a34a;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.2s;
}

.ref-copy-btn:hover {
    background: #dcfce7;
}

.ref-helper-text {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

.ref-modal-footer {
    padding: 18px 24px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #f8fafc;
}

.ref-btn-save {
    /*background: #16a34a;*/
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}

.ref-btn-save:hover {
    /*background: #15803d;*/
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
}

.ref-btn-cancel {
    background: #e2e8f0;
    color: #475569;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}

.ref-btn-cancel:hover {
    background: #cbd5e1;
}

/* Custom Alert Toast */
.ref-toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #0f172a;
    color: #ffffff;
    padding: 14px 24px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    z-index: 9999999;
    transform: translateY(100px);
    opacity: 0;
    transition: 0.3s ease;
}

.ref-toast.show {
    transform: translateY(0);
    opacity: 1;
}

.ref-toast i {
    color: #22c55e;
}

/* Floating top corner button styling */
.ref-floating-btn {
    position: absolute;
    top: 25px;
    right: 25px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff !important;
    border: none;
    border-radius: 50px;
    padding: 11px 22px;
    font-weight: 700;
    font-size: 13px;
    box-shadow: 0 10px 15px -3px rgba(217, 119, 6, 0.3), 0 4px 6px -2px rgba(217, 119, 6, 0.15);
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 999;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: refPulse 2s infinite;
}

.ref-floating-btn:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 15px 20px -5px rgba(217, 119, 6, 0.4), 0 8px 8px -5px rgba(217, 119, 6, 0.2);
    background: linear-gradient(135deg, #fbbf24, #d97706);
}

.ref-floating-btn i {
    font-size: 16px;
}

.ref-floating-btn {
    width: 47px;
    height: 47px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e0e0e0;
    background: #fff;
    box-shadow: 0 12px 18px -1px rgb(0 0 0 / 7%), 0 2px 20px -2px rgb(0 0 0 / 7%);
    color: #f59e0b !important;
}

.ref-floating-btn:hover {
    background: #fff;
}

.ref-floating-btn:hover .refer-toggle-sec {
    display: block !important;
    position: absolute;
    bottom: -26px;
    left: -22%;
    font-size: 10px;
    font-weight: 700;
    color: #4f46e5;
    white-space: nowrap;
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px solid #c7d2fe;
    letter-spacing: 0.2px;
    z-index: 100;
}

.boost-btn {
    width: 47px;
    height: 47px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #e0e0e0;
    border-radius: 50%;
    background: #fff;
    position: relative;
}

.boost-btn:hover .refer-toggle-sec {
    display: block !important;
    position: absolute;
    bottom: -26px;
    left: -22%;
    font-size: 10px;
    font-weight: 700;
    color: #4f46e5;
    white-space: nowrap;
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px solid #c7d2fe;
    letter-spacing: 0.2px;
    z-index: 100;
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
    const slug = "{{ $seller->referral_code ?? $seller->slug ?? $seller->username ?? $seller->id ?? '332994' }}";
    const refUrl = window.location.origin + '/seller/register?ref=' + encodeURIComponent(slug);
    
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

                let linkToUse = n.link;
                if (n.message && n.message.includes('Return Request')) {
                    linkToUse = "{{ route('seller.paymenthandle') }}";
                }

                // Build System Alerts HTML (For Alert Bell)
                systemAlertsHtml += `
                    <div class="alert-dropdown-item system-alert-item" style="padding: 12px; border-bottom: 1px solid #eee; cursor: pointer; display: flex; align-items: flex-start;" onclick="markAndRedirect(${n.id}, '${linkToUse}')">
                        ${sirenIcon}
                        <div class="alert-item-content" style="flex:1;">
                            <div class="alert-item-title" style="${n.is_read == 0 ? 'font-weight:600; color:#1f2937;' : 'font-weight:normal; color:#4b5563;'} font-size:13px; line-height:1.4;">${n.message}</div>
                            <div style="font-size:11px; color:#888; margin-top:2px;">System Alert • ${timeAgo(n.created_at)}</div>
                        </div>
                    </div>`;

                // Build Normal Notifications HTML (For Notification Bell)
                bellHtml += `
                    <li class="${n.is_read == 0 ? 'fw-bold bg-light' : ''} d-flex align-items-start p-3" 
                        onclick="markAndRedirect(${n.id}, '${linkToUse}')" style="border-bottom:1px solid #eee; cursor:pointer;">
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

// DISABLED: Polling every 5s was causing excessive DB connections (max_connections_per_hour limit).
// Firebase real-time listener (onChildAdded) already handles live notification updates.
// setInterval(loadNotifications, 5000);

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

// =========================================================
// ALERT BELL & SIREN INTERACTION
// =========================================================
function toggleAlertDropdown(e) {
    e.stopPropagation();
    var menu = document.getElementById('alertDropdownMenu');
    menu.classList.toggle('show');
    // Close notification dropdown if open
    $('#notifDropdown').hide();
}

$(document).on('click', function(e) {
    if (!$(e.target).closest('#alertBellWrapper').length) {
        $('#alertDropdownMenu').removeClass('show');
    }
});

// =========================================================
// GEOLOCATION DETECTION
// =========================================================
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

$(document).ready(function() {
    const cachedLocation = localStorage.getItem('vshop_seller_location');
    if (cachedLocation) {
        $('#headerLocationName').text(cachedLocation);
    }
});

// =========================================================
// PRODUCT BOOST INTERACTION & CALCULATION
// =========================================================
window.boostCurrentWalletBalance = 0;

function openBoostModal() {
    var modal = new bootstrap.Modal(document.getElementById('boostModal'));
    modal.show();
    loadBoostProducts();
}

function loadBoostProducts() {
    $('#boostLoading').show();
    $('#boostForm').hide();
    $('#boostProductList').empty();
    $('#boostSelectAll').prop('checked', false);

    $.ajax({
        url: "{{ route('seller.products.boost.products') }}",
        type: "GET",
        success: function(res) {
            $('#boostLoading').hide();
            $('#boostForm').show();

            if (res.wallet_balance !== undefined) {
                window.boostCurrentWalletBalance = parseFloat(res.wallet_balance);
                let fmtWallet = window.boostCurrentWalletBalance.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                $('#boostModalWalletBalance').text('₹' + fmtWallet);
            }

            if (res.success && res.data.length > 0) {
                let html = '';
                res.data.forEach(function(p) {
                    let isChecked = p.is_boosted ? 'checked disabled' : '';
                    let badge = p.is_boosted 
                                ? '<span class="badge bg-success"><i class="fa-solid fa-check"></i> Boosted</span>'
                                : '<span class="badge bg-secondary">Normal</span>';
                    if (p.is_top) {
                        badge += ' <span class="badge bg-warning text-dark ms-1"><i class="fa-solid fa-fire"></i> Top Enquiries</span>';
                    }
                    let price = parseFloat(p.price || 0).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    let perDay = p.per_day !== null && p.per_day !== undefined ? parseFloat(p.per_day) : null;
                    let fmtPerDay = perDay !== null ? '₹' + perDay.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) : '';
                    let isDisabled = perDay === null ? 'disabled' : '';
                    let days = p.days !== null && p.days !== undefined ? parseInt(p.days) : 7;

                    html += `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        <input class="form-check-input boost-item-checkbox" type="checkbox" name="product_ids[]" value="${p.id}" data-per-day="${perDay || 0}" data-days="${days}" ${isChecked} ${isDisabled} style="appearance: auto !important; position: static !important; opacity: 1 !important; width: 1.2rem; height: 1.2rem; cursor: pointer; display: inline-block;">
                                    </div>
                                    <div style="width: 45px; height: 45px; overflow: hidden; border-radius: 6px; border: 1px solid #e0e0e0; margin-right: 12px;">
                                        <img src="${p.image_url}" alt="Product" style="width: 100%; height: 100%; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark fs-14">${p.name}</div>
                                        <div class="text-muted" style="font-size: 11px;">SKU: ${p.sku || 'N/A'} - Price: ₹${price}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fw-bold text-dark">${fmtPerDay}</td>
                            <td class="text-center fw-bold text-primary">${days} Days</td>
                            <td class="text-center status-badge-cell">${badge}</td>
                        </tr>
                    `;
                });
                $('#boostProductList').html(html);
                updateBoostCostCalculation();
            } else {
                $('#boostProductList').html('<tr><td colspan="4" class="text-center text-muted py-4">No products found.</td></tr>');
            }
        },
        error: function(err) {
            $('#boostLoading').hide();
            Swal.fire('Error', 'Could not load products. Please try again.', 'error');
        }
    });
}

function updateBoostCostCalculation() {
    let count = $('.boost-item-checkbox:checked:not(:disabled)').length;
    let totalCost = 0;
    
    $('.boost-item-checkbox:checked:not(:disabled)').each(function() {
        let perDay = parseFloat($(this).data('per-day')) || 0;
        let days = parseInt($(this).data('days')) || 7;
        totalCost += (perDay * days);
    });
    
    $('#boostModalSelectedCount').text(count);
    $('#boostModalTotalCost').text('₹' + totalCost.toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
    
    let walletBalance = window.boostCurrentWalletBalance || 0;
    if (totalCost > walletBalance && count > 0) {
        $('#btnSubmitBoost').prop('disabled', true).addClass('btn-danger').removeClass('btn-primary').html('Insufficient Wallet <i class="fa-solid fa-triangle-exclamation ms-1"></i>');
    } else {
        $('#btnSubmitBoost').prop('disabled', count === 0).addClass('btn-primary').removeClass('btn-danger').html('Apply Boost <i class="fa-solid fa-bolt ms-1"></i>');
    }
}

$(document).on('change', '.boost-item-checkbox', function() {
    updateBoostCostCalculation();
    
    let row = $(this).closest('tr');
    let statusTd = row.find('.status-badge-cell');
    if (this.checked) {
        statusTd.html('<span class="badge bg-success"><i class="fa-solid fa-check"></i> Boosted</span>');
    } else {
        statusTd.html('<span class="badge bg-secondary">Normal</span>');
    }
});

$(document).on('change', '#boostSelectAll', function() {
    $('.boost-item-checkbox:not(:disabled)').prop('checked', this.checked).trigger('change');
});

function submitBoostProducts() {
    let selectedIds = [];
    $('.boost-item-checkbox:checked:not(:disabled)').each(function() {
        selectedIds.push($(this).val());
    });

    $('#btnSubmitBoost').prop('disabled', true).html('<i class="fa-solid fa-spinner fa-spin"></i> Saving...');

    let selectedDays = parseInt($('#boostModalDays').val()) || 1;

    $.ajax({
        url: "{{ route('seller.products.boost.submit') }}",
        type: "POST",
        data: {
            _token: "{{ csrf_token() }}",
            product_ids: selectedIds,
            days: selectedDays
        },
        success: function(res) {
            $('#btnSubmitBoost').prop('disabled', false).html('Apply Boost <i class="fa-solid fa-bolt ms-1"></i>');
            if (res.success) {
                $('#boostModal').modal('hide');
                
                // Dynamically update the wallet balance in the header
                if (res.new_balance !== undefined) {
                    let fmtHeaderWallet = parseFloat(res.new_balance).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    $('#headerWalletBalance').text('₹ ' + fmtHeaderWallet);
                }

                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: res.message,
                    timer: 2000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire('Error', res.message, 'error');
            }
        },
        error: function(err) {
            $('#btnSubmitBoost').prop('disabled', false).html('Apply Boost <i class="fa-solid fa-bolt ms-1"></i>');
            Swal.fire('Error', 'Failed to boost products. Please try again.', 'error');
        }
    });
}
</script>
