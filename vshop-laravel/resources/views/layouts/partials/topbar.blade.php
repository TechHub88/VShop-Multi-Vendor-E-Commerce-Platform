<?php
// =============================================
// ADD THIS CODE AT THE VERY TOP OF YOUR TOPBAR BLADE FILE
// =============================================

use App\Models\Seller;
use App\Models\Product;
use App\Models\Order;
use App\Models\Grievance;
use App\Models\OrderItem;
use App\Models\AdminNotification;
use Illuminate\Support\Facades\DB;

// Fetch Dynamic Alert Counts from Database
$pendingKYC = Seller::where('status', 'pending')->count();
$pendingProducts = Product::where('status', 'pending')->count();
$paymentFailures = Order::where('payment_status', 'failed')->where('created_at', '>=', now()->subDays(7))->count();

// High Return Products (return rate > 15%)
$highReturnProducts = OrderItem::select('product_id')
    ->selectRaw('COUNT(*) as total_orders')
    ->selectRaw('SUM(CASE WHEN status = "returned" THEN 1 ELSE 0 END) as returned_count')
    ->groupBy('product_id')
    ->havingRaw('(returned_count * 100.0 / total_orders) > 15')
    ->count();

$pendingGrievances = Grievance::where('status', 'pending')->count();
$totalAlerts = $pendingKYC + $pendingProducts + $paymentFailures + $highReturnProducts + $pendingGrievances;

// Fetch Detailed Alerts for Dropdown
$alertDetails = [];

// 1. Pending KYC Sellers
$kycSellers = Seller::where('status', 'pending')->select('id', 'name', 'email', 'created_at')->orderBy('created_at', 'desc')->limit(5)->get();
foreach ($kycSellers as $seller) {
    $alertDetails[] = [
        'type' => 'kyc', 'id' => $seller->id, 'title' => 'KYC Pending: ' . $seller->name,
        'subtitle' => 'Email: ' . ($seller->email ?? 'No email'), 
        'route' => route('admin.sellers.show', $seller->id),
        'icon' => 'bi-person-check', 'color' => 'danger', 'severity' => 'critical', 
        'time' => $seller->created_at->diffForHumans()
    ];
}

// 2. Pending Products
$products = Product::where('status', 'pending')->with('seller')->select('id', 'name', 'created_by_id', 'created_at')->orderBy('created_at', 'desc')->limit(5)->get();
foreach ($products as $product) {
    $alertDetails[] = [
        'type' => 'product', 'id' => $product->id, 'title' => 'Product: ' . substr($product->name, 0, 40),
        'subtitle' => 'By: ' . ($product->seller->name ?? 'Unknown'), 
        'route' => route('admin.approval.verify', $product->id),
        'icon' => 'bi-box-seam', 'color' => 'warning', 'severity' => 'warning', 
        'time' => $product->created_at->diffForHumans()
    ];
}

// 3. Payment Failures
$failedPayments = Order::where('payment_status', 'failed')->where('created_at', '>=', now()->subDays(7))->with('user')->select('id', 'total_amount', 'created_at')->orderBy('created_at', 'desc')->limit(5)->get();
foreach ($failedPayments as $order) {
    $alertDetails[] = [
        'type' => 'payment',
        'title' => 'Payment Failed - Order #' . $order->id,
        'subtitle' => '₹' . number_format($order->total_amount, 2) . ' - ' . ($order->user->name ?? 'Guest'),
        'route' => route('admin.orders.show', $order->id), 'icon' => 'bi-credit-card', 
        'color' => 'danger', 'severity' => 'critical', 'time' => $order->created_at->diffForHumans()
    ];
}

// 4. High Return Products
$highReturnProductsList = OrderItem::select('product_id')->selectRaw('COUNT(*) as total_orders')->selectRaw('SUM(CASE WHEN status = "returned" THEN 1 ELSE 0 END) as returned_count')->with('product')->groupBy('product_id')->havingRaw('(returned_count * 100.0 / total_orders) > 15')->limit(5)->get();
foreach ($highReturnProductsList as $item) {
    $returnRate = round(($item->returned_count * 100.0 / $item->total_orders), 1);
    $alertDetails[] = [
        'type' => 'return', 'id' => $item->product_id, 'title' => 'High Return: ' . substr($item->product->name ?? 'Product #' . $item->product_id, 0, 35),
        'subtitle' => "Return rate: {$returnRate}% ({$item->returned_count}/{$item->total_orders} orders)",
        'route' => route('admin.products.show', $item->product_id), 'icon' => 'bi-arrow-return-left', 
        'color' => 'warning', 'severity' => 'warning', 'time' => ''
    ];
}

// 5. Pending Grievances
$grievances = Grievance::where('status', 'pending')->with('user')->select('id', 'subject', 'priority', 'created_at')->orderByRaw("FIELD(priority, 'high', 'medium', 'low')")->orderBy('created_at', 'asc')->limit(5)->get();
foreach ($grievances as $grievance) {
    $severity = $grievance->priority === 'high' ? 'critical' : 'warning';
    $color = $grievance->priority === 'high' ? 'danger' : ($grievance->priority === 'medium' ? 'warning' : 'info');
    $alertDetails[] = [
        'type' => 'grievance', 'id' => $grievance->id, 'title' => 'Ticket #' . $grievance->id,
        'subtitle' => substr($grievance->subject, 0, 50) . ' - ' . ($grievance->user->name ?? 'Guest'),
        'route' => route('admin.grievance.show', $grievance->id), 'icon' => 'bi-chat-dots', 
        'color' => $color, 'severity' => $severity, 'time' => $grievance->created_at->diffForHumans()
    ];
}

// Sort alerts by severity (critical first)
usort($alertDetails, function($a, $b) {
    $severityOrder = ['critical' => 1, 'warning' => 2];
    return ($severityOrder[$a['severity']] ?? 3) <=> ($severityOrder[$b['severity']] ?? 3);
});

// Fetch Admin notifications from local database
$adminNotifications = AdminNotification::latest()->limit(10)->get();
$unreadAdminCount = AdminNotification::where('is_read', false)->count();
?>

<!-- Topbar Start -->
<div class="topbar-custom">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Left Side -->
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center">
                <li>
                    <button class="button-toggle-menu nav-link">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                </li>
                <li class="d-none d-lg-block">
                    <form class="app-search d-none d-md-block me-auto" id="globalSearchForm">
                        <div class="position-relative topbar-search">
                            <i class="bi bi-search position-absolute text-muted top-50 translate-middle-y ms-3" style=" left: 14px; z-index: 10;"></i>
                            <input type="text" 
                                   id="globalSearchInput" 
                                   class="form-control ps-5" 
                                   placeholder="Search products, orders, sellers, customers..." 
                                   style="width: 300px;" />
                        </div>
                        <div id="searchSuggestions" class="search-suggestions-dropdown" style="display: none;">
                            <div class="suggestions-list"></div>
                        </div>
                    </form>
                </li>
            </ul>

            <!-- Right Side -->
            <ul class="list-unstyled topnav-menu mb-0 d-flex align-items-center gap-2">
                
                <!-- Live Tracking Button -->
           <li>
    <button id="liveTrackingBtn" class="btn btn-sm rounded-circle topbar-icon-btn admin-top-btn" style="background: #f1f5f9; width: 40px; height: 40px; border: none; overflow: visible; display: flex; align-items: center; justify-content: center; text-decoration: none;" title="Live Tracking" data-label="Live Tracking">
        <div class="position-relative d-flex align-items-center justify-content-center">
            <svg id="liveTrackingIcon" class="live-eye-svg" width="26" height="18" viewBox="0 0 120 80" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block;">
                <defs>
                    <linearGradient id="eyeBodyGrad" x1="0" y1="0" x2="120" y2="80" gradientUnits="userSpaceOnUse">
                        <stop offset="0%" stop-color="#1e90ff"/>
                        <stop offset="50%" stop-color="#00c9b1"/>
                        <stop offset="100%" stop-color="#00e5c8"/>
                    </linearGradient>
                </defs>
                <ellipse cx="60" cy="40" rx="58" ry="36" fill="url(#eyeBodyGrad)"/>
                <circle cx="60" cy="40" r="20" fill="white"/>
                <circle cx="60" cy="40" r="14" fill="#00b4d8" opacity="0.35"/>
                <circle cx="60" cy="40" r="8" fill="#0e7490"/>
                <circle cx="54" cy="34" r="3.5" fill="white" opacity="0.7"/>
            </svg>
            <span id="liveTrackingDot" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="display: none; width: 8px; height: 8px;"></span>
        </div>
    </button>
</li>
    <!-- Wallet Button -->
    <li>
        <a href="{{ route('admin.wallet.index') }}" class="btn btn-sm rounded-circle topbar-icon-btn admin-top-btn" data-label="Wallet">
            <i class="bi bi-wallet2 fs-5"></i>
        </a>
    </li>

                <!-- ==================== ALERTS DROPDOWN (D. Alerts Section) ==================== -->
                <li class="dropdown" style="position: relative;">
                    <button class="btn btn-sm rounded-circle dropdown-toggle position-relative topbar-icon-btn admin-top-btn" data-bs-toggle="dropdown" style="background: #f1f5f9; width: 40px; height: 40px; border: none;" data-bs-custom-class="topbar-tooltip" title="Alert" data-label="Alert">
                        <svg width="26" height="26" viewBox="0 0 96 96" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block; margin: auto;">
                            <defs>
                                <linearGradient id="sirenRedGrad" x1="48" y1="22" x2="48" y2="58" gradientUnits="userSpaceOnUse">
                                    <stop offset="0%" stop-color="#ff3b5c"/>
                                    <stop offset="100%" stop-color="#dc2626"/>
                                </linearGradient>
                            </defs>
                            <circle cx="48" cy="48" r="44" fill="#ffe4e6" opacity="0.85"/>
                            <path d="M 28 58 L 28 42 A 20 20 0 0 1 68 42 L 68 58 Z" fill="url(#sirenRedGrad)"/>
                            <path d="M 48 29 L 48 43" stroke="#ffffff" stroke-width="6" stroke-linecap="round"/>
                            <circle cx="48" cy="51.5" r="3.5" fill="#ffffff"/>
                            <rect x="24" y="58" width="48" height="6" rx="3" fill="#cbd5e1"/>
                            <rect x="18" y="64" width="60" height="8" rx="4" fill="#475569"/>
                        </svg>
                        <?php if($totalAlerts > 0): ?>
                        <span id="alertTotalBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 5px; margin-top: -5px; margin-left: -8px;">
                            <?php echo $totalAlerts > 99 ? '99+' : $totalAlerts; ?>
                        </span>
                        <?php else: ?>
                        <span id="alertTotalBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 9px; padding: 2px 5px; margin-top: -5px; margin-left: -8px; display: none;">0</span>
                        <?php endif; ?>
                        
                        <span id="liveTrackingDot" class="refer-toggle-sec" style="display: none;">Alert</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 380px; max-height: 500px; border-radius: 16px; overflow: hidden; box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);">
                        <div class="dropdown-header border-bottom py-3 px-3 bg-light d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="m-0 fs-14 fw-semibold">
                                    <i class="bi bi-exclamation-triangle-fill me-1 text-warning"></i> Critical Alerts
                                </h5>
                                <small class="text-muted">Pending actions requiring attention</small>
                            </div>
                            <button class="btn btn-sm btn-link text-primary p-0" id="refreshAlertsBtn" title="Refresh">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                        <div id="alertsDynamicList" class="alert-scroll" style="max-height: 380px; overflow-y: auto;">
                            <?php if(count($alertDetails) > 0): ?>
                                <?php foreach($alertDetails as $alert): ?>
                                <a href="<?php echo $alert['route']; ?>" class="dropdown-item alert-item border-bottom p-3" style="border-left: 3px solid <?php echo $alert['severity'] == 'critical' ? '#dc2626' : '#f59e0b'; ?>;">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="bg-<?php echo $alert['color']; ?> bg-opacity-10 rounded-circle p-2">
                                            <i class="bi <?php echo $alert['icon']; ?> fs-5 text-<?php echo $alert['color']; ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <h6 class="mb-1 fw-semibold fs-14"><?php echo htmlspecialchars($alert['title']); ?></h6>
                                                <span class="badge bg-<?php echo $alert['severity'] == 'critical' ? 'danger' : 'warning'; ?> rounded-pill" style="font-size: 9px;">
                                                    <?php echo ucfirst($alert['severity']); ?>
                                                </span>
                                            </div>
                                            <p class="small text-muted mb-1"><?php echo htmlspecialchars($alert['subtitle']); ?></p>
                                            <?php if(!empty($alert['time'])): ?>
                                            <small class="text-muted" style="font-size: 10px;">
                                                <i class="bi bi-clock"></i> <?php echo $alert['time']; ?>
                                            </small>
                                            <?php endif; ?>
                                        </div>
                                        <i class="bi bi-chevron-right text-muted"></i>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-check-circle-fill fs-1 text-success"></i>
                                    <p class="mt-2 text-muted mb-0">All clear! No pending alerts</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                    </div>
                </li>

                <!-- Quick Actions Dropdown -->
                <li class="dropdown">
                    <button class="btn btn-sm rounded-circle dropdown-toggle topbar-icon-btn admin-top-btn" data-bs-toggle="dropdown" style="background: #f1f5f9; width: 40px; height: 40px; border: none;" data-bs-custom-class="topbar-tooltip" title="Quick Action" data-label="Quick Action">
                        <svg width="26" height="26" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg" style="display:block; margin: auto;">
                            <defs>
                                <radialGradient id="targetRed" cx="50%" cy="50%" r="50%">
                                    <stop offset="0%" stop-color="#f97316"/>
                                    <stop offset="100%" stop-color="#dc2626"/>
                                </radialGradient>
                            </defs>
                            <circle cx="50" cy="50" r="46" fill="#dbeafe" opacity="0.85"/>
                            <circle cx="50" cy="50" r="38" fill="#ef4444"/>
                            <circle cx="50" cy="50" r="30" fill="white"/>
                            <circle cx="50" cy="50" r="22" fill="#f97316"/>
                            <circle cx="50" cy="50" r="14" fill="white"/>
                            <circle cx="50" cy="50" r="8" fill="#ef4444"/>
                            <circle cx="50" cy="50" r="4.5" fill="white"/>
                            <circle cx="50" cy="50" r="2.5" fill="#f97316"/>
                        </svg>
                        
                        <span id="liveTrackingDot" class="refer-toggle-sec" style="display: none; left: -35%;">Quick Action</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 280px; border-radius: 16px; box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);">
                        <div class="dropdown-header border-bottom mb-2 pb-2">
                            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-bullseye me-1 text-primary"></i> Quick Actions</h6>
                            <small class="text-muted">Frequently used actions</small>
                        </div>
                        <a href="{{ route('admin.sellers.index', ['status' => 'pending']) }}" class="dropdown-item py-2 rounded-2 d-flex align-items-center">
                            <div class="me-3 bg-success bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-person-check fs-5 text-success"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Approve KYC</div>
                                <small class="text-muted"><?php echo $pendingKYC; ?> sellers pending</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.approval.products') }}" class="dropdown-item py-2 rounded-2 d-flex align-items-center">
                            <div class="me-3 bg-primary bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-box-seam fs-5 text-primary"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Approve Products</div>
                                <small class="text-muted"><?php echo $pendingProducts; ?> products pending</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.orders.index', ['payment_status' => 'failed']) }}" class="dropdown-item py-2 rounded-2 d-flex align-items-center">
                            <div class="me-3 bg-danger bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-credit-card fs-5 text-danger"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Failed Payments</div>
                                <small class="text-muted"><?php echo $paymentFailures; ?> failed transactions</small>
                            </div>
                        </a>
                        <a href="{{ route('admin.grievance.index') }}" class="dropdown-item py-2 rounded-2 d-flex align-items-center">
                            <div class="me-3 bg-warning bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-chat-dots fs-5 text-warning"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">Pending Grievances</div>
                                <small class="text-muted"><?php echo $pendingGrievances; ?> tickets pending</small>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a href="{{ route('admin.reports.index') }}" class="dropdown-item py-2 rounded-2 d-flex align-items-center">
                            <div class="me-3 bg-info bg-opacity-10 p-2 rounded-circle">
                                <i class="bi bi-graph-up fs-5 text-info"></i>
                            </div>
                            <div>
                                <div class="fw-semibold">View Reports</div>
                                <small class="text-muted">Analytics & insights</small>
                            </div>
                        </a>
                    </div>
                </li>

                <!-- Currency Dropdown -->
                <li class="dropdown">
                    <button class="btn btn-sm dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown" style="background: #f1f5f9; height: 40px; border-radius: 20px; border: none;" id="currencyDropdown">
                        <span id="selectedCurrencySymbol" class="fw-semibold">₹</span>
                        <span id="selectedCurrencyCode" class="text-muted">INR</span>
                    </button>
                    <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 240px; border-radius: 16px;">
                        <div class="dropdown-header border-bottom mb-2 pb-2">
                            <h6 class="mb-0">Select Currency</h6>
                        </div>
                        <a class="dropdown-item currency-option rounded-2 d-flex justify-content-between align-items-center" data-currency="INR" data-symbol="₹" href="javascript:void(0);">
                            <span><i class="bi bi-flag-fill me-2 text-orange"></i> Indian Rupee</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary">₹ INR</span>
                        </a>
                        <a class="dropdown-item currency-option rounded-2 d-flex justify-content-between align-items-center" data-currency="USD" data-symbol="$" href="javascript:void(0);">
                            <span><i class="bi bi-flag-fill me-2 text-primary"></i> US Dollar</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary">$ USD</span>
                        </a>
                        <a class="dropdown-item currency-option rounded-2 d-flex justify-content-between align-items-center" data-currency="EUR" data-symbol="€" href="javascript:void(0);">
                            <span><i class="bi bi-flag-fill me-2 text-info"></i> Euro</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary">€ EUR</span>
                        </a>
                        <a class="dropdown-item currency-option rounded-2 d-flex justify-content-between align-items-center" data-currency="GBP" data-symbol="£" href="javascript:void(0);">
                            <span><i class="bi bi-flag-fill me-2 text-success"></i> British Pound</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary">£ GBP</span>
                        </a>
                    </div>
                </li>

                <!-- Notification Dropdown - FIXED BADGE POSITION -->
                <li class="dropdown notification-list topbar-dropdown" style="position: relative;">
                    <a class="nav-link dropdown-toggle p-0 admin-top-btn" data-bs-toggle="dropdown" href="#" role="button">
                        <!--<div class="position-relative d-inline-block">-->
                            <i class="bi bi-bell fs-5 text-secondary" id="notifBellIcon"></i>
                            <span class="badge bg-danger rounded-circle noti-icon-badge position-absolute top-0 start-100 translate-middle" id="notifBadgeCount" style="font-size: 9px; padding: 2px 5px; margin-top: -8px; margin-left: -8px;">{{ $unreadAdminCount }}</span>
                        <!--</div>-->
                        
                        <span id="liveTrackingDot" class="refer-toggle-sec" style="display: none; left: -35%;">Notification</span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-end p-0" style="width: 350px; border-radius: 16px; overflow: hidden;">
                        <div class="dropdown-item noti-title border-bottom py-3 px-3 bg-light">
                            <h5 class="m-0 fs-14 fw-semibold">
                                <span class="float-end">
                                    <a href="javascript:void(0);" class="text-primary fs-12" id="markAllRead">Mark all as read</a>
                                </span>
                                <i class="bi bi-bell me-1"></i> Notifications
                            </h5>
                        </div>
                        <div class="noti-scroll" id="notifList" style="max-height: 350px; overflow-y: auto;">
                            @forelse($adminNotifications as $notif)
                            @php
                                $notifUrl = $notif->url ?? '#';
                                if (strtolower($notif->type) === 'enquiry' || str_contains(strtolower($notif->message), 'enquiry')) {
                                    $notifUrl = route('admin.enquiry.index');
                                }
                                $iconClass = $notif->type === 'order' ? 'bi-cart' : ($notif->type === 'product' ? 'bi-box-seam' : (strtolower($notif->type) === 'enquiry' ? 'bi-envelope' : 'bi-bell'));
                            @endphp
                            <a href="{{ $notifUrl }}" class="dropdown-item notify-item border-bottom p-3 {{ !$notif->is_read ? 'bg-light' : '' }}" data-id="{{ $notif->id }}">
                                <div class="d-flex align-items-center">
                                    <div class="notify-icon bg-primary text-white rounded-circle p-2 me-3">
                                        <i class="bi {{ $iconClass }}"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="notify-details mb-0 fw-semibold">{{ ucfirst($notif->type) }}</p>
                                        <small class="text-muted">{{ $notif->message }}</small>
                                    </div>
                                </div>
                            </a>
                            @empty
                            <div class="text-center py-4 text-muted" id="emptyNotifMsg">No notifications</div>
                            @endforelse
                        </div>
                        <a href="{{ route('admin.activity.log') }}" class="dropdown-item text-center text-primary notify-item notify-all fw-bold border-top py-3">
                            View all activity <i class="bi bi-arrow-right ms-1"></i>
                        </a>
                    </div>
                </li>

                <!-- User Dropdown -->
                <li class="dropdown notification-list topbar-dropdown">
                   <a class="nav-link dropdown-toggle nav-user me-0" data-bs-toggle="dropdown" href="#" role="button">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="width: 40px; height: 40px;background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);">
                            <span class="fw-bold fs-14">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 2)) }}</span>
                        </div>
                   </a>
                  <div class="dropdown-menu dropdown-menu-end p-2" style="min-width: 250px; border-radius: 16px;">
    <div class="dropdown-header border-bottom mb-2 pb-2">
        <h6 class="text-overflow m-0">Welcome, {{ auth()->user()->name ?? 'Admin' }}!</h6>
        <small class="text-muted">{{ auth()->user()->email ?? 'admin@example.com' }}</small>
    </div>
    <a href="{{ route('second', [ 'utility' , 'profile']) }}" class="dropdown-item notify-item rounded-2 py-2">
        <i class="bi bi-person-circle fs-5 align-middle me-2"></i>
        <span>My Profile</span>
    </a>
    <a href="{{ route('admin.update.password')}}" class="dropdown-item notify-item rounded-2 py-2">
        <i class="bi bi-key fs-5 align-middle me-2"></i>
        <span>Change Password</span>
    </a>
    <a href="{{ route('admin.staff.directory') }}" class="dropdown-item notify-item rounded-2 py-2">
        <i class="bi bi-people fs-5 align-middle me-2"></i>
        <span>My Team</span>
    </a>
    <div class="dropdown-divider my-1"></div>
    <form method="POST" action="{{ route('admin.logout') }}">
        @csrf
        <button type="submit" class="dropdown-item notify-item rounded-2 py-2 text-danger">
            <i class="bi bi-box-arrow-right fs-5 align-middle me-2"></i>
            <span>Logout</span>
        </button>
    </form>
</div>
                </li>
            </ul>
        </div>
    </div>
</div>

<style>
.admin-top-btn {
        width: 40px;
        height: 40px;
        border: none;
        overflow: visible;
        background: #00439233 !important; 
        position: relative;
        display: inline-flex !important;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    }
    
.admin-top-btn:hover .refer-toggle-sec {
    display: block !important;
    position: absolute;
    bottom: -26px;
    left: -4%;
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
    line-height: 16px;
}

.notification-list .noti-icon-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    top: 8px !important;
    padding: 8px 8px !important;
    width: 8px !important;
    height: 8px !important;
}


/* Search Suggestions Styles */
.search-suggestions-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 20px 25px -12px rgba(0,0,0,0.15);
    z-index: 1000;
    margin-top: 8px;
    max-height: 400px;
    overflow-y: auto;
}

.suggestion-category {
    padding: 8px 12px;
    background: #f8f9fa;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
    border-bottom: 1px solid #e9ecef;
}

.suggestion-item {
    padding: 12px 15px;
    cursor: pointer;
    transition: all 0.2s;
    border-bottom: 1px solid #f1f3f4;
    display: flex;
    align-items: center;
    gap: 12px;
}

.suggestion-item:hover {
    background-color: #eef2ff;
    transform: translateX(5px);
}

.suggestion-item.active {
    background-color: #e7f1ff;
}

.suggestion-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #f1f3f4;
    font-size: 18px;
}

.suggestion-content {
    flex: 1;
}

.suggestion-title {
    font-size: 14px;
    font-weight: 500;
    color: #1e293b;
}

.suggestion-subtitle {
    font-size: 11px;
    color: #6c757d;
    margin-top: 2px;
}

.topbar-search {
    position: relative;
}

#globalSearchInput {
    width: 300px;
    transition: all 0.3s;
    border-radius: 30px;
    background: #f8fafc;
    border-color: #e2e8f0;
    padding-left: 40px;
}

#globalSearchInput:focus {
    width: 350px;
    background: white;
    border-color: #4f46e5;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.1);
}

/* Alert Dropdown Styles */
.alerts-dropdown-menu {
    width: 380px;
    max-width: 90vw;
}

.alert-item {
    transition: all 0.2s;
    text-decoration: none;
    color: inherit;
}

.alert-item:hover {
    background-color: #eef2ff;
    transform: translateX(2px);
}

.alert-scroll {
    max-height: 380px;
    overflow-y: auto;
}

/* Custom Scrollbar for Alerts */
.alert-scroll::-webkit-scrollbar {
    width: 5px;
}

.alert-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.alert-scroll::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.alert-scroll::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Bell Animation */
.blink-bell {
    animation: bellBlink 0.4s ease-in-out 4;
}
@keyframes bellBlink {
    0%   { opacity: 1; transform: scale(1); }
    50%  { opacity: 0.2; transform: scale(1.2); }
    100% { opacity: 1; transform: scale(1); }
}

/* Live Tracking Blink Animation */
.blink-live {
    animation: liveBlink 1.2s ease-in-out infinite !important;
}
@keyframes liveBlink {
    0%   { opacity: 1; transform: scale(1); }
    50%  { opacity: 0.5; transform: scale(1.1); color: #10b981 !important; }
    100% { opacity: 1; transform: scale(1); }
}

/* Dropdown Animations */
.dropdown-menu {
    animation: dropdownFade 0.2s ease-out;
}
@keyframes dropdownFade {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

/* Currency Dropdown Styling */
.currency-option {
    cursor: pointer;
    transition: all 0.2s;
    padding: 10px 12px;
}
.currency-option:hover {
    background-color: #eef2ff;
    transform: translateX(5px);
}

/* Notification List Scrollbar */
.noti-scroll::-webkit-scrollbar {
    width: 4px;
}
.noti-scroll::-webkit-scrollbar-track {
    background: #f1f5f9;
}
.noti-scroll::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

/* Button Hover Effects */
.btn-sm.rounded-circle:hover {
    transform: scale(1.05);
    transition: transform 0.2s;
}

/* Color for flag */
.text-orange {
    color: #ff9800;
}

/* ========== TOPBAR ICON TOOLTIP (BLUE THEME) ========== */
.topbar-tooltip .tooltip-inner {
    background: linear-gradient(135deg, #4f46e5, #6366f1) !important;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 12px;
    padding: 6px 14px;
    border-radius: 8px;
    letter-spacing: 0.3px;
    box-shadow: 0 4px 12px rgba(79,70,229,0.3);
}
.topbar-tooltip .tooltip-arrow::before {
    border-bottom-color: #4f46e5 !important;
}

/* ========== TOPBAR ICON BUTTON HOVER EFFECTS - BLUE ONLY ========== */
.topbar-icon-btn {
    transition: all 0.25s ease;
    position: relative;
    outline: none !important;
}
.topbar-icon-btn:hover {
    background: linear-gradient(135deg, #eef2ff, #e0e7ff) !important;
    box-shadow: 0 0 0 3px rgba(79,70,229,0.18), 0 4px 10px rgba(79,70,229,0.1);
    transform: translateY(-1px);
}
.topbar-icon-btn:active {
    transform: translateY(0);
    box-shadow: 0 0 0 2px rgba(79,70,229,0.25);
}
/* Hover name label below each icon - BLUE ONLY (fixed) */
.topbar-icon-btn::after {
    content: attr(data-label);
    position: absolute;
    bottom: -26px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 10px;
    font-weight: 700;
    color: #4f46e5 !important;
    white-space: nowrap;
    opacity: 0;
    pointer-events: none;
    transition: opacity 0.22s ease, transform 0.22s ease;
    background: linear-gradient(135deg, #eef2ff, #e0e7ff);
    padding: 2px 8px;
    border-radius: 6px;
    border: 1px solid #c7d2fe;
    letter-spacing: 0.2px;
    z-index: 100;
}
.topbar-icon-btn:hover::after {
    opacity: 1;
    transform: translateX(-50%) translateY(2px);
}
/* Make sure dropdown toggle arrow from Bootstrap doesn't show */
.topbar-icon-btn.dropdown-toggle::before {
    display: none !important;
}
.topbar-icon-btn.dropdown-toggle::after {
    display: none !important;
}

/* ========== DROPDOWN ITEMS - BLUE THEME (NO BLACK) ========== */
.dropdown-menu .dropdown-item {
    color: #475569 !important;
    transition: all 0.2s ease;
}
.dropdown-menu .dropdown-item:hover,
.dropdown-menu .dropdown-item:focus {
    color: #4f46e5 !important;
    background-color: #eef2ff !important;
}
.dropdown-menu .dropdown-item .fw-semibold {
    color: #334155;
}
.dropdown-menu .dropdown-item:hover .fw-semibold {
    color: #4f46e5 !important;
}
.dropdown-menu .dropdown-header h5,
.dropdown-menu .dropdown-header h6 {
    color: #1e40af !important;
}
.dropdown-menu .dropdown-item.text-danger:hover {
    color: #dc2626 !important;
    background-color: #fef2f2 !important;
}

/* ========== NOTIFICATION BELL - BLUE THEME ========== */
#notifBellIcon {
    color: #6366f1 !important;
    transition: color 0.2s;
}
.nav-link:hover #notifBellIcon {
    color: #4f46e5 !important;
}

/* ========== CURRENCY BUTTON - BLUE THEME ========== */
#currencyDropdown .fw-semibold {
    color: #4f46e5 !important;
}
#currencyDropdown .text-muted {
    color: #6366f1 !important;
}

/* ========== LIVE TRACKING EYE BLINK ANIMATION ========== */
.live-eye-svg {
    animation: eyeBlinkAnim 3s ease-in-out infinite;
    transform-origin: center center;
    transform-box: fill-box;
}
@keyframes eyeBlinkAnim {
    0%   { transform: scaleY(1); }
    88%  { transform: scaleY(1); }
    91%  { transform: scaleY(0.05); }
    94%  { transform: scaleY(1); }
    96%  { transform: scaleY(0.08); }
    100% { transform: scaleY(1); }
}
/* When live tracking is ON — faster blink + green tint */
.live-eye-svg.blink-live {
    animation: eyeBlinkFast 1.5s ease-in-out infinite;
    filter: drop-shadow(0 0 4px #10b981);
}
@keyframes eyeBlinkFast {
    0%   { transform: scaleY(1); }
    40%  { transform: scaleY(1); }
    44%  { transform: scaleY(0.05); }
    48%  { transform: scaleY(1); }
    100% { transform: scaleY(1); }
}

.topbar-custom {
    background: #fff;
    border-bottom: 1px solid #dee2e6;
}



</style>

<!-- Bootstrap Icons CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

<!-- Global Search JavaScript -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    let searchTimeout;
    let currentSuggestions = [];
    let selectedIndex = -1;
    
    const searchInput = document.getElementById('globalSearchInput');
    const suggestionsDropdown = document.getElementById('searchSuggestions');
    const suggestionsList = suggestionsDropdown.querySelector('.suggestions-list');
    
    const sectionRoutes = {
        'dashboard': '{{ route("second", ["dashboard", "index"]) }}',
        'products': '{{ route("admin.approval.products") }}',
        'orders': '{{ route("admin.orders.index") }}',
        'sellers': '{{ route("admin.sellers.index") }}',
        'customers': '{{ route("admin.customers.index") }}',
        'tickets': '{{ route("admin.tickets.index") }}',
        'coupons': '{{ route("admin.coupons.index") }}',
        'reports': '{{ route("admin.reports.index") }}',
        'refund': '{{ route("admin.refund.requests") }}',
        'settings': '{{ route("admin.settings.integrations") }}',
        'activity': '{{ route("admin.activity.log") }}',
        'profile': '{{ route("second", ["utility", "profile"]) }}',
    };
    
    async function performSearch(searchTerm) {
        if (!searchTerm.trim() || searchTerm.trim().length < 1) {
            suggestionsDropdown.style.display = 'none';
            return;
        }
        
        const directMatches = findDirectMatches(searchTerm);
        
        if (directMatches.length > 0) {
            displaySuggestions(directMatches);
            return;
        }
        
        try {
            const response = await fetch(`{{ route("admin.global.search") }}?search=${encodeURIComponent(searchTerm)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
            });
            const data = await response.json();
            const suggestions = formatBackendResults(data, searchTerm);
            displaySuggestions(suggestions);
        } catch (error) {
            suggestionsDropdown.style.display = 'none';
        }
    }
    
    function findDirectMatches(searchTerm) {
        const matches = [];
        const term = searchTerm.toLowerCase().trim();
        
        for (const [keyword, route] of Object.entries(sectionRoutes)) {
            if (keyword.includes(term) || term.includes(keyword)) {
                matches.push({
                    type: 'section',
                    title: keyword.charAt(0).toUpperCase() + keyword.slice(1),
                    subtitle: `Navigate to ${keyword}`,
                    url: route,
                    icon: '📁'
                });
            }
        }
        return matches.slice(0, 8);
    }
    
    function formatBackendResults(data, searchTerm) {
        const suggestions = [];
        const categories = [
            { key: 'products', title: 'Products', icon: '📦', route: '{{ route("admin.approval.products") }}', nameField: 'name' },
            { key: 'orders', title: 'Orders', icon: '🛒', route: '{{ route("admin.orders.index") }}', nameField: 'id' },
            { key: 'sellers', title: 'Sellers', icon: '🏪', route: '{{ route("admin.sellers.index") }}', nameField: 'name' },
        ];
        
        for (const category of categories) {
            const items = data[category.key] || [];
            items.slice(0, 3).forEach(item => {
                suggestions.push({
                    type: category.key.slice(0, -1),
                    title: item[category.nameField] || 'Untitled',
                    subtitle: `${category.title} - ID: ${item.id}`,
                    url: `${category.route}/${item.id}`,
                    icon: category.icon
                });
            });
        }
        return suggestions.slice(0, 10);
    }
    
    function displaySuggestions(suggestions) {
        currentSuggestions = suggestions;
        selectedIndex = -1;
        
        if (suggestions.length === 0) {
            suggestionsDropdown.style.display = 'none';
            return;
        }
        
        let html = '';
        suggestions.forEach((suggestion, index) => {
            html += `
                <div class="suggestion-item" data-url="${suggestion.url}" data-index="${index}">
                    <div class="suggestion-icon">${suggestion.icon}</div>
                    <div class="suggestion-content">
                        <div class="suggestion-title">${escapeHtml(suggestion.title)}</div>
                        <div class="suggestion-subtitle">${escapeHtml(suggestion.subtitle)}</div>
                    </div>
                    <i class="bi bi-arrow-right text-muted"></i>
                </div>
            `;
        });
        
        suggestionsList.innerHTML = html;
        suggestionsDropdown.style.display = 'block';
        
        document.querySelectorAll('.suggestion-item').forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const url = this.getAttribute('data-url');
                if (url) window.location.href = url;
            });
        });
    }
    
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    searchInput.addEventListener('input', function(e) {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => performSearch(e.target.value), 200);
    });
    
    searchInput.addEventListener('keydown', function(e) {
        const items = document.querySelectorAll('.suggestion-item');
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
            updateActiveItem(items);
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            selectedIndex = Math.max(selectedIndex - 1, -1);
            updateActiveItem(items);
        } else if (e.key === 'Enter' && selectedIndex >= 0 && items[selectedIndex]) {
            const url = items[selectedIndex].getAttribute('data-url');
            if (url) window.location.href = url;
        } else if (e.key === 'Escape') {
            suggestionsDropdown.style.display = 'none';
            searchInput.blur();
        }
    });
    
    function updateActiveItem(items) {
        items.forEach((item, idx) => {
            if (idx === selectedIndex) item.classList.add('active');
            else item.classList.remove('active');
        });
    }
    
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !suggestionsDropdown.contains(e.target)) {
            suggestionsDropdown.style.display = 'none';
        }
    });
    
    // Refresh Alerts Button
    const refreshBtn = document.getElementById('refreshAlertsBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', function(e) {
            e.preventDefault();
            location.reload();
        });
    }

    // Redirect to dashboard with live_tracking=1 if not on dashboard page
    const liveTrackingBtn = document.getElementById('liveTrackingBtn');
    if (liveTrackingBtn) {
        liveTrackingBtn.addEventListener('click', function(e) {
            if (!document.querySelector('.live-stats-grid')) {
                e.preventDefault();
                window.location.href = "{{ url('/dashboard/index') }}?live_tracking=1";
            }
        });
    }

    // Check if live tracking is active from URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const isLiveTrackingActive = urlParams.get('live_tracking') === '1';
    
    if (isLiveTrackingActive) {
        const liveTrackingIcon = document.getElementById('liveTrackingIcon');
        const liveTrackingDot = document.getElementById('liveTrackingDot');
        
        if (liveTrackingIcon) liveTrackingIcon.classList.add('blink-live');
        if (liveTrackingDot) liveTrackingDot.style.display = 'block';
        if (liveTrackingBtn) {
            liveTrackingBtn.style.background = 'linear-gradient(135deg, #d1fae5, #a7f3d0)';
            liveTrackingBtn.style.boxShadow = '0 0 0 3px rgba(16,185,129,0.3)';
        }
        startLiveTrackingInterval();
    }
});

function startLiveTrackingInterval() {
    setInterval(() => {
        const liveOrderSpan = document.getElementById('liveOrderCount');
        if (liveOrderSpan && window.location.search.includes('live_tracking=1')) {
            let current = parseInt(liveOrderSpan.innerText) || 142;
            liveOrderSpan.innerText = current + Math.floor(Math.random() * 3);
            liveOrderSpan.classList.add('blink-live');
            setTimeout(() => liveOrderSpan.classList.remove('blink-live'), 500);
        }
    }, 5000);
}

// Currency Switcher
document.addEventListener('DOMContentLoaded', function() {
    const currencyOptions = document.querySelectorAll('.currency-option');
    const selectedCurrencySymbol = document.getElementById('selectedCurrencySymbol');
    const selectedCurrencyCode = document.getElementById('selectedCurrencyCode');
    
    const exchangeRates = { INR: 1, USD: 0.012, EUR: 0.011, GBP: 0.0096 };
    const currencySymbols = { INR: '₹', USD: '$', EUR: '€', GBP: '£' };
    
    function convertAllCurrencies(currency) {
        const rate = exchangeRates[currency];
        const symbol = currencySymbols[currency];
        
        document.querySelectorAll('.money').forEach(el => {
            let amount = parseFloat(el.getAttribute('data-amount'));
            if (!isNaN(amount)) {
                el.innerText = symbol + ' ' + (amount * rate).toLocaleString(undefined, {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }
        });
        
        if (selectedCurrencySymbol) selectedCurrencySymbol.innerText = symbol;
        if (selectedCurrencyCode) selectedCurrencyCode.innerText = currency;
        localStorage.setItem('selectedCurrency', currency);
        
        window.dispatchEvent(new CustomEvent('currencyChanged', {
            detail: { currency: currency, symbol: symbol, rate: rate }
        }));
    }
    
    const savedCurrency = '{{ session('currency', 'INR') }}';
    localStorage.setItem('selectedCurrency', savedCurrency);
    convertAllCurrencies(savedCurrency);
    
    currencyOptions.forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            const currency = this.getAttribute('data-currency');
            
            // Smoothly update backend session without reloading the page
            fetch(`/admin/change-currency/${currency}`, {
                method: 'GET',
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(() => {
                localStorage.setItem('selectedCurrency', currency);
                convertAllCurrencies(currency);
            })
            .catch(error => {
                console.error("Error updating currency session", error);
            });
        });
    });
});

// Toast function
function showToast(title, message) {
    if (typeof toastr !== 'undefined') {
        toastr.options = {
            closeButton: true,
            progressBar: true,
            preventDuplicates: true,
            newestOnTop: true,
            positionClass: "toast-top-right",
            timeOut: 3000
        };
        toastr.success(message, title);
    }
}
</script>

<!-- Firebase + Realtime Order Listener -->
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
document.addEventListener("DOMContentLoaded", function () {
    const notifList = document.getElementById("notifList");
    const badgeCount = document.getElementById("notifBadgeCount");
    const bellIcon = document.getElementById("notifBellIcon");
    const dingSound = new Audio("https://assets.mixkit.co/sfx/preview/mixkit-software-interface-back-2576.mp3");
    
    // Listen to admin notifications from Firebase
    const adminNotifsRef = window.fRef(window.db, "notifications/admin/all");
    const watchAdminLast = window.fQuery(adminNotifsRef, window.fLimit(1));
    let lastAdminNotifId = null;
    let initialAdminLoad = true;

    window.fOnChildAdded(watchAdminLast, (snapshot) => {
        const data = snapshot.val();
        if (!data) return;
        const notifKey = snapshot.key;
        if (initialAdminLoad) {
            initialAdminLoad = false;
            lastAdminNotifId = notifKey;
            return;
        }
        if (notifKey === lastAdminNotifId) return;
        lastAdminNotifId = notifKey;

        let count = parseInt(badgeCount.innerText) || 0;
        badgeCount.innerText = ++count;
        bellIcon.classList.add("blink-bell");
        setTimeout(() => bellIcon.classList.remove("blink-bell"), 1200);

        // Hide empty placeholder if it exists
        const emptyMsg = document.getElementById("emptyNotifMsg");
        if (emptyMsg) {
            emptyMsg.remove();
        }

        let notifUrl = data.link || '#';
        if ((data.type && data.type.toLowerCase() === 'enquiry') || (data.message && data.message.toLowerCase().includes('enquiry'))) {
            notifUrl = "{{ route('admin.enquiry.index') }}";
        }
        const iconClass = data.type === 'order' ? 'bi-cart' : (data.type === 'product' ? 'bi-box-seam' : (data.type && data.type.toLowerCase() === 'enquiry' ? 'bi-envelope' : 'bi-bell'));
        const item = `
            <a href="${notifUrl}" class="dropdown-item notify-item border-bottom p-3 bg-light">
                <div class="d-flex align-items-center">
                    <div class="notify-icon bg-primary text-white rounded-circle p-2 me-3">
                        <i class="bi ${iconClass}"></i>
                    </div>
                    <div class="flex-grow-1">
                        <p class="notify-details mb-0 fw-semibold">${data.type ? data.type.toUpperCase() : 'Alert'}</p>
                        <small class="text-muted">${data.message}</small>
                    </div>
                </div>
            </a>
        `;
        notifList.insertAdjacentHTML("afterbegin", item);
        if (typeof showToast === 'function') showToast(data.type ? data.type.toUpperCase() : 'Notification', data.message);
        dingSound.play();
    });

    const markAllReadBtn = document.getElementById('markAllRead');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener("click", function () {
            badgeCount.innerText = "0";
            // Mark all read in backend via fetch
            fetch("{{ url('/admin/notifications/mark-all-read') }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(res => res.json())
            .then(res => {
                document.querySelectorAll('#notifList .notify-item').forEach(el => el.classList.remove('bg-light'));
            })
            .catch(err => console.error(err));
        });
    }
});
</script>

<script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
