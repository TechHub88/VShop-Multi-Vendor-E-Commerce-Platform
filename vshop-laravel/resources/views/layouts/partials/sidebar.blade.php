<style>
.logo-user-icon {
    display: block;
    text-align: center;
    padding-top: 15px;
}
.logo-user-icon svg {
    height: 42px;
}
.app-sidebar-menu {
    background-color: #004392 !important;
}
.menu-title,
.admin-section-title {
    color: #fff !important;
}
#sidebar-menu>ul>li>a {
    color: hsl(213deg 32.26% 87.84% / 72%) !important;
}
#sidebar-menu .tp-link.active {
    color: #ffffff !important;
    font-weight: 600;
    background: rgba(255,255,255,0.1);
    border-left: 3px solid #5cb8ff;
    padding-left: 12px;
    border-radius: 4px;
}
#sidebar-menu .nav-second-level .tp-link:hover {
    color: #ffffff !important;
    background: rgba(255,255,255,0.05);
}
</style>

<div class="app-sidebar-menu">
<div class="h-100" data-simplebar>

<div id="sidebar-menu">

<!-- USER ICON -->
<div class="logo-box">

</div>

<ul id="side-menu">

<li class="menu-title">Main Menu</li>

@php
    $adminPermissions = [];
    $isAdminHasFullAccess = false;

    if (auth('admin_staff')->check()) {
        $staffUser = auth('admin_staff')->user();
        $rawPerms = $staffUser->permissions;
        $adminPermissions = is_string($rawPerms) ? json_decode($rawPerms, true) : (is_array($rawPerms) ? $rawPerms : []);
        $adminPermissions = $adminPermissions ?: [];
        
        // Fallback for older JSON format (like {"dashboard":"full_access"})
        if (isset($adminPermissions['dashboard']) && $adminPermissions['dashboard'] === 'full_access') {
            $adminPermissions['dashboard'] = '1';
            $adminPermissions['all_orders'] = '1';
            $adminPermissions['all_sellers'] = '1';
            $adminPermissions['customer_control'] = '1';
            $adminPermissions['finance'] = '1';
            $adminPermissions['staff_management'] = '1';
            $adminPermissions['product_verification'] = '1';
            $adminPermissions['product_categories'] = '1';
            // map more if needed, but '1' is safe
        }
    } else {
        $isAdminHasFullAccess = true;
    }

    $hasPerm = function($keys) use ($adminPermissions, $isAdminHasFullAccess) {
        if ($isAdminHasFullAccess) return true;
        if (!is_array($keys)) $keys = [$keys];
        foreach ($keys as $key) {
            if (isset($adminPermissions[$key]) && $adminPermissions[$key] == '1') {
                return true;
            }
        }
        return false;
    };
    
    $activePanels = session('admin_panels', ['product']);
    if (empty($activePanels)) {
        $activePanels = ['product'];
    }
    
    if (count($activePanels) > 1) {
        $dashboardTitle = 'Dashboard';
        $managementTitle = 'Product & Service Management';
        $verificationTitle = 'Product & Service Verification';
        $categoryTitle = 'Product & Service Categories';
        $ticketTitle = 'Tickets Tracking';
    } elseif (in_array('travel', $activePanels)) {
        $dashboardTitle = 'Service Dashboard';
        $managementTitle = 'Service Management';
        $verificationTitle = 'Service Verification';
        $categoryTitle = 'Service Categories';
        $ticketTitle = 'Service Tickets';
    } elseif (in_array('campus', $activePanels)) {
        $dashboardTitle = 'Campus Dashboard';
        $managementTitle = 'Course Management';
        $verificationTitle = 'Course Verification';
        $categoryTitle = 'Course Categories';
        $ticketTitle = 'Course Tickets';
    } elseif (in_array('edge', $activePanels)) {
        $dashboardTitle = 'Edge Dashboard';
        $managementTitle = 'Digital Management';
        $verificationTitle = 'Digital Verification';
        $categoryTitle = 'Digital Categories';
        $ticketTitle = 'Digital Tickets';
    } else {
        $dashboardTitle = 'Dashboard';
        $managementTitle = 'Product Management';
        $verificationTitle = 'Product Verification';
        $categoryTitle = 'Product Categories';
        $ticketTitle = 'Support Tickets';
    }
@endphp

<!-- DASHBOARD -->
@if($hasPerm('dashboard'))
<li>
<!--<a href="#sidebarDashboards" data-bs-toggle="collapse">-->

<!--<span>Admin Dashboard </span>-->
<!--<span class="menu-arrow"></span>-->
<!--</a>-->

<a href="{{ route('second', ['dashboard', 'index']) }}" class="tp-link">
<span class="nav-icon">
<iconify-icon icon="solar:home-2-bold-duotone"></iconify-icon>
</span>
<span>{{ $dashboardTitle }}</span>
</a>
</li>
@endif


<!-- VERIFICATION -->
@if($hasPerm(['product_verification', 'product_categories']))
<li>
<a href="#sidebarApproval" data-bs-toggle="collapse">
<span class="nav-icon">
<iconify-icon icon="solar:checklist-bold-duotone"></iconify-icon>
</span>
<span>
{{ $managementTitle }}
</span>
<span class="menu-arrow"></span>
</a>

<div class="collapse {{ request()->routeIs('admin.approval.*') ? 'show' : '' }}" id="sidebarApproval">
<ul class="nav-second-level">

<li>
<a href="{{ route('admin.approval.products') }}"
class="tp-link {{ request()->routeIs('admin.approval.products') ? 'active' : '' }}">
{{ $verificationTitle }}
</a>
</li>

<li>
<a href="{{ route('admin.approval.categories') }}"
class="tp-link {{ request()->routeIs('admin.approval.categories') ? 'active' : '' }}">
{{ $categoryTitle }}
</a>
</li>

</ul>
</div>
</li>
@endif


<!-- ORDER MANAGEMENT -->
@if($hasPerm(['all_orders', 'payment_monitoring', 'order_lifecycle', 'cancellation_returns']))
<li>
    <a href="javascript:void(0);" data-bs-target="#sidebarOrders" data-bs-toggle="collapse" aria-expanded="false" aria-controls="sidebarOrders">
        <span class="nav-icon">
            <iconify-icon icon="solar:cart-large-4-bold-duotone"></iconify-icon>
        </span>
        <span>Order Management</span>
        <span class="menu-arrow"></span>
    </a>

    <div class="collapse {{ request()->routeIs('admin.orders.*') ? 'show' : '' }}" id="sidebarOrders">
        <ul class="nav-second-level">
            <li>
                <a href="{{ route('admin.orders.index') }}"
                    class="tp-link {{ request()->routeIs('admin.orders.index') ? 'active' : '' }}">
                    All Orders
                </a>
            </li>

            <li>
                <a href="{{ route('admin.orders.payment') }}"
                    class="tp-link {{ request()->routeIs('admin.orders.payment') ? 'active' : '' }}">
                    Payment Monitoring
                </a>
            </li>

            <li>
                <a href="{{ route('admin.orders.lifecycle') }}"
                    class="tp-link {{ request()->routeIs('admin.orders.lifecycle') ? 'active' : '' }}">
                    Order Lifecycle
                </a>
            </li>

            <li>
                <a href="{{ route('admin.orders.cancellation') }}"
                    class="tp-link {{ request()->routeIs('admin.orders.cancellation') ? 'active' : '' }}">
                    Cancellation & Returns
                </a>
            </li>
        </ul>
    </div>
</li>
@endif

<!-- SELLER MANAGEMENT -->
@if($hasPerm(['all_sellers', 'blacklist_control', 'logs_audit']))
<li>
    <a href="#sidebarSellers" data-bs-toggle="collapse">
        <span class="nav-icon">
            <iconify-icon icon="solar:shop-bold-duotone"></iconify-icon>
        </span>
        <span>Seller Management</span>
        <span class="menu-arrow"></span>
    </a>

    <div class="collapse {{ request()->routeIs('admin.sellers.*') ? 'show' : '' }}" id="sidebarSellers">
        <ul class="nav-second-level">
            <li>
                <a href="{{ route('admin.sellers.index') }}"
                    class="tp-link {{ request()->routeIs('admin.sellers.index') ? 'active' : '' }}">
                    All Sellers
                </a>
            </li>

         
            <!-- Blacklist Control Menu -->
            <li>
                <a href="{{ route('admin.blacklist.index') }}" 
                    class="tp-link {{ request()->routeIs('admin.blacklist.*') ? 'active' : '' }}">
                    Blacklist Control
                </a>
            </li>
            
            <!-- Logs & Audit Menu -->
            <li>
                <a href="{{ route('admin.audit-logs.index') }}" 
                    class="tp-link {{ request()->routeIs('admin.audit-logs.*') ? 'active' : '' }}">
                    Logs & Audit
                </a>
            </li>
        </ul>
    </div>
</li>
@endif

<!-- BUYERS -->
@if($hasPerm(['customer_control', 'customer_insights', 'customer_validation']))
<li>
<a href="#sidebarBuyers" data-bs-toggle="collapse">
<span class="nav-icon"><iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon></span>
<span>Buyer Management</span>
<span class="menu-arrow"></span>
</a>

<div class="collapse {{ request()->routeIs('admin.customers.*') ? 'show' : '' }}" id="sidebarBuyers">
<ul class="nav-second-level">
    <!-- Buyer Control Group -->
    <li>
        <a href="#submenuBuyerControl" data-bs-toggle="collapse" class="tp-link">
            <span>Buyer Control</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse {{ request()->routeIs('admin.customers.index') || request()->routeIs('admin.customers.block-control') || request()->routeIs('admin.customers.merge') ? 'show' : '' }}" id="submenuBuyerControl">
            <ul class="nav-second-level ps-3">
                <li><a href="{{ route('admin.customers.index') }}" class="tp-link">View All Buyers</a></li>
                <li><a href="{{ route('admin.customers.block-control') }}" class="tp-link">Block / Unblock</a></li>
               
            </ul>
        </div>
    </li>
    
    <!-- Insights Group -->
    <li>
        <a href="#submenuInsights" data-bs-toggle="collapse" class="tp-link">
            <span>Insights</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse {{ request()->routeIs('admin.customers.insights.*') ? 'show' : '' }}" id="submenuInsights">
            <ul class="nav-second-level ps-3">
                <li><a href="{{ route('admin.customers.insights.high-value') }}" class="tp-link">High-Value Buyers</a></li>
                <li><a href="{{ route('admin.customers.insights.frequent-return') }}" class="tp-link">Frequent Return Buyers</a></li>
                <li><a href="{{ route('admin.customers.insights.bad-reviews') }}" class="tp-link">Bad Review Buyers</a></li>
            </ul>
        </div>
    </li>
    
    <!-- Data Validation Group -->
    <li>
        <a href="#submenuValidation" data-bs-toggle="collapse" class="tp-link">
            <span>Data Validation</span>
            <span class="menu-arrow"></span>
        </a>
        <div class="collapse {{ request()->routeIs('admin.customers.validation.*') ? 'show' : '' }}" id="submenuValidation">
            <ul class="nav-second-level ps-3" >
                <li><a href="{{ route('admin.customers.validation.address') }}" class="tp-link">Address Validation</a></li>
                <li><a href="{{ route('admin.customers.validation.duplicates') }}" class="tp-link">Duplicate Detection</a></li>
            </ul>
        </div>
    </li>
</ul>
</div>
</li>
@endif



@if($hasPerm('finance'))
<li>
    <a href="{{ route('admin.finance-management') }}" class="tp-link">
        <span class="nav-icon">
            <iconify-icon icon="solar:wallet-money-bold-duotone"></iconify-icon>
        </span>
        <span>Finance Management</span>
    </a>
</li>
@endif

@if($hasPerm('staff_management'))
<li>
    <a href="{{ route('admin.staff.directory') }}"
       class="{{ request()->routeIs('admin.staff.*') ? 'active' : '' }}">
        <span class="nav-icon">
            <iconify-icon icon="solar:users-group-rounded-bold-duotone"></iconify-icon>
        </span>
        <span>Staff Management</span>
    </a>
</li>
@endif

<!-- MARKETPLACE CONTROL - Dropdown Menu Item -->
@if($hasPerm(['seller_verification', 'advertisements', 'promotion_campaigns', 'search_discovery', 'smart_suggestions', 'dynamic_pages']))
<li>
    <a href="#marketplaceMenu" data-bs-toggle="collapse">
        <span class="nav-icon">
            <iconify-icon icon="solar:shop-2-bold-duotone"></iconify-icon>
        </span>
        <span>Marketplace Control</span>
        <span class="menu-arrow"></span>
    </a>

    <div class="collapse {{ request()->routeIs('admin.marketplace*') || request()->routeIs('admin.categories.*') || request()->routeIs('admin.subcategories.*') || request()->routeIs('admin.banners.*') || request()->routeIs('admin.ads.*') || request()->routeIs('admin.pages.*') ? 'show' : '' }}" id="marketplaceMenu">
        <ul class="nav-second-level">
            <li>
                <a href="{{ route('admin.marketplace-control') }}" class="tp-link {{ request()->routeIs('admin.marketplace-control') ? 'active' : '' }}">
                    Seller Verification
                </a>
            </li>
         
            <li>
                <a href="{{ route('admin.ads.index') }}" class="tp-link {{ request()->routeIs('admin.ads.index') ? 'active' : '' }}">
                    Advertisements & Campaigns
                </a>
            </li>
            <!--<li>-->
            <!--    <a href="{{ route('admin.marketplace.promotions') }}" class="tp-link {{ request()->routeIs('admin.marketplace.promotions') ? 'active' : '' }}">-->
            <!--        Promotion Campaigns-->
            <!--    </a>-->
            <!--</li>-->
            <li>
                <a href="{{ route('admin.marketplace.search-discovery') }}" class="tp-link {{ request()->routeIs('admin.marketplace.search-discovery') ? 'active' : '' }}">
                    Search & Discovery
                </a>
            </li>
            <li>
    <a href="{{ route('admin.marketplace.recommendations.index') }}" class="tp-link {{ request()->routeIs('admin.marketplace.recommendations*') ? 'active' : '' }}">
        Recommendation Engine
    </a>
</li>
          
            <li>
                <a href="{{ route('admin.pages.index') }}" class="tp-link {{ request()->routeIs('admin.pages.index') ? 'active' : '' }}">
                    Dynamic Pages
                </a>
            </li>
        </ul>
    </div>
</li>
@endif


<li>
<a href="{{ route('admin.grievance.index') }}" class="tp-link {{ request()->routeIs('admin.grievance.*') ? 'active' : '' }}">
<span class="nav-icon"><iconify-icon icon="solar:shield-warning-bold-duotone"></iconify-icon></span>
<span>Grivance & Dispute Management</span>
</a>
</li>

<li>
<a href="{{ route('admin.enquiry.index') }}" class="tp-link {{ request()->routeIs('admin.enquiry.*') ? 'active' : '' }}">
<span class="nav-icon"><iconify-icon icon="solar:chat-square-call-bold-duotone"></iconify-icon></span>
<span>Enquiry Management</span>
</a>
</li>

<!-- MARKETING & GROWTH CONTROL -->
<li>
<a href="#sidebarMarketingGrowth" data-bs-toggle="collapse">
<span class="nav-icon"><iconify-icon icon="solar:graph-up-bold-duotone"></iconify-icon></span>
<span>Marketing & Growth</span>
<span class="menu-arrow"></span>
</a>

<div class="collapse {{ request()->routeIs('admin.coupons.*') || request()->routeIs('admin.tickets.*') ? 'show' : '' }}" id="sidebarMarketingGrowth">
<ul class="nav-second-level">
<li>
    <a href="{{ route('admin.coupons.index') }}" class="tp-link {{ request()->routeIs('admin.coupons.*') ? 'active' : '' }}">
        Coupon Engine
    </a>
</li>
<!--<li>-->
<!--    <a href="{{ route('admin.tickets.index') }}" class="tp-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}">-->
<!--        {{ $ticketTitle }}-->
<!--    </a>-->
<!--</li>-->
<li>
    <a href="{{ route('admin.referrals.index') }}" class="tp-link {{ request()->routeIs('admin.referrals.*') ? 'active' : '' }}">Referral System</a>
</li>
<li>
    <a href="{{ route('admin.campaigns.index') }}" class="tp-link {{ request()->routeIs('admin.campaigns.*') ? 'active' : '' }}">Campaigns Tracking</a>
</li>
</ul>
</div>
</li>

<!-- REPORTS -->
@if($hasPerm(['seller_product_report', 'top_selling', 'seller_negative_reviews', 'highest_rating', 'lowest_rating', 'seller_report', 'total_sales_report']))
<li>
<a href="#sidebarReports" data-bs-toggle="collapse">
<span class="nav-icon">
<iconify-icon icon="solar:graph-bold-duotone"></iconify-icon>
</span>
<span> Reports & Analytics </span>
<span class="menu-arrow"></span>
</a>

<div class="collapse" id="sidebarReports">
<ul class="nav-second-level">

<!-- Existing Reports -->
<!--<li><a href="{{ route('admin.reports.seller.products') }}" class="tp-link">Seller Product Report</a></li>-->
<!--<li><a href="{{ route('admin.reports.top.selling') }}" class="tp-link">Top Selling Products</a></li>-->
<!--<li><a href="{{ route('admin.reports.seller-negative') }}" class="tp-link">Seller Negative Reviews</a></li>-->
<!--<li><a href="{{ route('admin.reports.highest.rating') }}" class="tp-link">Highest Rating Report</a></li>-->
<!--<li><a href="{{ route('admin.reports.lowest.rating') }}" class="tp-link">Lowest Rating Report</a></li>-->
<!--<li><a href="{{ route('admin.reports.index') }}" class="tp-link">Seller Report</a></li>-->
<!--<li><a href="{{ route('admin.allreports') }}" class="tp-link">Total Sales Report</a></li>-->

<!-- New Analytics Sections -->
<li class="nav-divider">━━━━━━━━━━━━━━━━━</li>

<li><a href="{{ route('admin.analytics.sales') }}" class="tp-link"> Sales Analytics</a></li>
<li><a href="{{ route('admin.analytics.funnel') }}" class="tp-link"> Funnel Analytics</a></li>
<li><a href="{{ route('admin.analytics.customers') }}" class="tp-link">Buyer Analytics</a></li>
<li><a href="{{ route('admin.analytics.sellers') }}" class="tp-link">Seller Analytics</a></li>
<li><a href="{{ route('admin.analytics.promotion') }}" class="tp-link">Promotion Analytics</a></li>
<li><a href="{{ route('admin.analytics.campaign') }}" class="tp-link"> Campaign ROI</a></li>
<li><a href="{{ route('admin.analytics.leads') }}" class="tp-link">Lead Analytics</a></li>
<!--<li><a href="{{ route('admin.analytics.graphs') }}" class="tp-link">Analytics Graphs</a></li>-->
<li><a href="{{ route('admin.ai.promotion.engine') }}" class="tp-link"> AI Promotion Engine</a></li>
<li><a href="{{ route('admin.ai.insights') }}" class="tp-link">AI Insights</a></li>
<li><a href="{{ route('admin.analytics.dropshipping') }}" class="tp-link"> Dropshipping Analytics</a></li>

</ul>
</div>
</li>
@endif










@if($hasPerm('staff_management'))
<li>
    <a href="{{ route('admin.settings.master_control') }}"
       class="{{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
        <span class="nav-icon">
            <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
        </span>
        <span>Master System</span>
    </a>
</li>
@endif


<!--
<li>
    <a href="{{ route('admin.settings.system') }}" class="tp-link {{ request()->routeIs('admin.settings.system') ? 'active' : '' }}">
        <span class="nav-icon"><iconify-icon icon="solar:settings-bold-duotone"></iconify-icon></span>
        System Settings
    </a>
</li>
-->
<!-- COMMISSION -->
<!--<li>-->
<!--<a href="#sidebarPayments" data-bs-toggle="collapse">-->
<!--<span class="nav-icon">-->
<!--<iconify-icon icon="solar:wallet-bold-duotone"></iconify-icon>-->
<!--</span>-->
<!--<span>Commission Management</span>-->
<!--<span class="menu-arrow"></span>-->
<!--</a>-->

<!--<div class="collapse" id="sidebarPayments">-->
<!--<ul class="nav-second-level">-->
<!--<li><a href="{{ route('admin.commission.report') }}" class="tp-link">Commission Report</a></li>-->
<!--<li><a href="{{ route('admin.commission.settings') }}" class="tp-link">Commission Settings</a></li>-->
<!--</ul>-->
<!--</div>-->
<!--</li>-->



<!-- REFUND -->
<!--<li>-->
<!--<a href="#sidebarRefund" data-bs-toggle="collapse">-->
<!--<span class="nav-icon"><iconify-icon icon="solar:hand-cash-bold-duotone"></iconify-icon></span>-->
<!--<span>Refunds</span>-->
<!--<span class="menu-arrow"></span>-->
<!--</a>-->

<!--<div class="collapse" id="sidebarRefund">-->
<!--<ul class="nav-second-level">-->
<!--<li><a href="{{ route('admin.refund.requests') }}" class="tp-link">Refund Requests</a></li>-->
<!--<li><a href="{{ route('admin.refund.reasons') }}" class="tp-link">Refund Reasons</a></li>-->
<!--</ul>-->
<!--</div>-->
<!--</li>-->




<!-- REVIEWS -->
<li>
<a href="#sidebarReviews" data-bs-toggle="collapse">
<span class="nav-icon"><iconify-icon icon="solar:chat-round-like-bold-duotone"></iconify-icon></span>
<span>Reviews & Ratings</span>
<span class="menu-arrow"></span>
</a>

<div class="collapse" id="sidebarReviews">
<ul class="nav-second-level">
<li><a href="{{ route('admin.index') }}" class="tp-link">All Reviews</a></li>
<li><a href="{{ route('admin.index') }}?status=approved" class="tp-link">Approved Reviews</a></li>
<li><a href="{{ route('admin.index') }}?status=pending" class="tp-link">Pending Reviews</a></li>
</ul>
</div>
</li>

<!-- ACTIVITY -->
<li>
<a href="{{ route('admin.activity.log') }}">
<span class="nav-icon"><iconify-icon icon="solar:clock-circle-bold-duotone"></iconify-icon></span>
<span>Activity Log</span>
</a>
</li>

</ul>
</div>

</div>
</div>

<style>
.user-icon-circle {
width: 60px;
height: 60px;
border-radius: 50%;
display: flex;
justify-content: center;
align-items: center;
}
</style>

<!-- Left Sidebar End -->
