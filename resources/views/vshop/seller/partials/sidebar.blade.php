
@php

/*
|--------------------------------------------------------------------------
| PANEL SESSION
|--------------------------------------------------------------------------
*/

$selectedPanels = session('selected_panels', ['product']);


/*
|--------------------------------------------------------------------------
| PANEL CHECKING
|--------------------------------------------------------------------------
*/

$hasProductPanel = in_array('product', $selectedPanels);

$hasTravelPanel = in_array('travel', $selectedPanels);

$hasCampusPanel = in_array('vcampus', $selectedPanels);


/*
|--------------------------------------------------------------------------
| MODULE LABELS
|--------------------------------------------------------------------------
*/

$moduleLabel = 'Product';

$modulePluralLabel = 'Products';

$moduleOrderLabel = 'Product Orders';


/*
|--------------------------------------------------------------------------
| PRODUCT ONLY
|--------------------------------------------------------------------------
*/

if($hasProductPanel && !$hasTravelPanel){

    $moduleLabel = 'Product';

    $modulePluralLabel = 'Products';

    $moduleOrderLabel = 'Product Orders';
}


/*
|--------------------------------------------------------------------------
| SERVICE ONLY
|--------------------------------------------------------------------------
*/

elseif(!$hasProductPanel && $hasTravelPanel){

    $moduleLabel = 'Service';

    $modulePluralLabel = 'Services';

    $moduleOrderLabel = 'Service Orders';
}


/*
|--------------------------------------------------------------------------
| BOTH PRODUCT + SERVICE
|--------------------------------------------------------------------------
*/

elseif($hasProductPanel && $hasTravelPanel){

    $moduleLabel = 'Product & Service';

    $modulePluralLabel = 'Products/Services';

    $moduleOrderLabel = 'Orders';
}


/*
|--------------------------------------------------------------------------
| CAMPUS ONLY
|--------------------------------------------------------------------------
*/

elseif($hasCampusPanel && !$hasProductPanel && !$hasTravelPanel){

    $moduleLabel = 'Course';

    $modulePluralLabel = 'Courses';

    $moduleOrderLabel = 'Course Orders';
}


/*
|--------------------------------------------------------------------------
| GLOBAL DYNAMIC LABELS
|--------------------------------------------------------------------------
*/

$misLabel = $moduleLabel . ' MIS';

$listLabel = $moduleLabel . ' List';

$createLabel = 'Add ' . $moduleLabel;

$analyticsLabel = $moduleLabel . ' Analytics';

$enquiryLabel = $moduleLabel . ' Enquiry';

$customerLabel = $moduleLabel . ' Customers';


/*
|--------------------------------------------------------------------------
| STAFF PERMISSION
|--------------------------------------------------------------------------
*/

$isStaff = session('login_type') === 'staff';

$canManageOrders = session('can_manage_orders');

$canViewFinance = session('can_view_finance');

$canManageProducts = session('can_manage_products');

@endphp



<style>
    .submenu {
       /* display: none;*/
    }
    .dasboardMenuList .menu-dropdown.show .submenu {
        display: block;
        transform: translateX(0);
        opacity: 1;
        visibility: visible;
    }
    .submenu li a {
        height: auto;
        background: #fff;
        color: #004392;
        font-weight: 600;
        align-items: flex-start;
        padding: 8px 21px;
    }
    .submenu {
        position: absolute;
        right: 100%;
        /*
        left: 100%;
        bottom: -0%;*/
        width: 240px;
        height: auto;
        border-radius: 10px !important;
        background: #fff;
        padding: 0;
        box-shadow: 0px 11px 45px #232F34;
        /*transform: translateX(-10px);
        opacity: 0;
        visibility: hidden;*/
        transition: all 0.25s ease;
        z-index: 99999;
    }
    .submenu li:first-child {
        border-radius: 10px 10px 0 0;
        overflow: hidden;
    }
    .submenu li:last-child {
        border-radius: 0 0 10px 10px;
        overflow: hidden;
    }
    .submenu::before {
        content: "";
        position: absolute;
        /*bottom: 20%;*/
        top:30px;
        left: -9px;
        width: 20px;
        height: 20px;
        background: #fff;
        transform: rotate(45deg);
    }
    .menu-dropdown {
        position: relative;
    }
</style>

<aside class="dashboardSidebar">
    <div class="dashboardSidebarinner">
        <div class="dashboardSidebarMenuArea scrollbar scrolldiv">
            <ul class="dasboardMenuList">

                <!-- Dashboard -->
                <li>
                    <a href="{{ route('seller.dashboard') }}">
                        <i class="fa-solid fa-house"></i> <span>Dashboard</span>
                    </a>
                </li>


@if(!$isStaff || $canManageProducts)

    {{-- PRODUCT ONLY --}}
    @if(
        $hasProductPanel
        &&
        !$hasTravelPanel
        &&
        !$hasCampusPanel
    )

        <li>
            <a href="{{ route('seller.products.mis.dashboard') }}">
                <i class="fa-solid fa-box"></i>
                Products
            </a>
        </li>

    {{-- SERVICE ONLY --}}
    @elseif(
        !$hasProductPanel
        &&
        $hasTravelPanel
        &&
        !$hasCampusPanel
    )

        <li>
            <a href="{{ route('seller.products.mis.dashboard') }}">
                <i class="fa-solid fa-plane"></i>
                Services
            </a>
        </li>

    {{-- BOTH PRODUCT + SERVICE --}}
    @elseif(
        $hasProductPanel
        &&
        $hasTravelPanel
    )

        <li>
            <a href="{{ route('seller.products.mis.dashboard') }}">
                <i class="fa-solid fa-box"></i>
               <span style="white-space:nowrap;">
    Products/Services
</span>
            </a>
        </li>

    {{-- CAMPUS ONLY --}}
    @elseif(
        !$hasProductPanel
        &&
        !$hasTravelPanel
        &&
        $hasCampusPanel
    )

        <li>
            <a href="{{ route('seller.products.mis.dashboard') }}">
                <i class="fa-solid fa-graduation-cap"></i>
                Courses
            </a>
        </li>

    @endif

@endif



                <!-- ================= CATEGORY ================= -->
                <!--<li class="menu-dropdown">-->
                <!--    <a href="javascript:void(0);" class="menu-toggle">-->
                <!--        <i class="fa-light fa-layer-group"></i> Categories-->
                <!--    </a>-->

                <!--    <ul class="submenu">-->
                <!--        <li>-->
                <!--            <a href="{{ route('seller.categories.index') }}">-->
                <!--                Category List-->
                <!--            </a>-->
                <!--        </li>-->
                <!--        <li>-->
                <!--            <a href="{{ route('seller.categories.create') }}">-->
                <!--                Add Category-->
                <!--            </a>-->
                <!--        </li>-->
                <!--    </ul>-->
                <!--</li>-->
                
               


<li class="menu-dropdown">

<a href="javascript:void(0);" class="menu-toggle">
<i class="fa-solid fa-users"></i> Customers
</a>

<ul class="submenu">

<li>
<a href="{{route('seller.customer.mis')}}">
Customer MIS
</a>
</li>

<li>
<a href="{{route('seller.customer.list')}}">
Customer List
</a>
</li>

<li>
<a href="{{route('seller.customer.create')}}">
Add Customer
</a>
</li>

</ul>

</li>


{{-- ORDERS --}}
@if(!$isStaff || $canManageOrders)

<li class="menu-dropdown">

    <a href="javascript:void(0);" class="menu-toggle">
        <i class="fa-solid fa-cart-shopping"></i>
        {{ $moduleOrderLabel }}
    </a>

    <ul class="submenu">

        <li>
            <a href="{{ route('seller.orders.mis') }}">
                Order MIS
            </a>
        </li>

        <li>
            <a href="{{ route('seller.orders.index') }}">
                All Orders
            </a>
        </li>

        <li>
            <a href="{{ route('seller.paymenthandle') }}">
                Payment Handling
            </a>
        </li>

        <li>
            <a href="{{ route('seller.shipping') }}">
                Shipping & Delivery Setting
            </a>
        </li>

    </ul>

</li>

@endif




                <!-- Profile -->
                <li>
                    <a href="{{ route('seller.organization')  }}">
                        <i class="fa-solid fa-user"></i> Seller Profile
                    </a>
                </li>
                

<li>
<a href="{{ route('seller.marketplace.category') }}">
<i class="fa-solid fa-shop"></i>
<span>Market Place</span>
</a>
</li>


 <li class="menu-dropdown">

    <a href="javascript:void(0);" class="menu-toggle">
        <i class="fa-solid fa-comments"></i> Enquiry
    </a>
    <!--<button class="menu-toggle">-->
    <!--    <i class="fa-solid fa-comments"></i> Enquiry-->
    <!--</button>-->

    <ul class="submenu">

        <li>
            <a href="{{ route('seller.enquiry.mis') }}">
                Enquiry MIS
            </a>
        </li>

        <li>
            <a href="{{ route('seller.enquiry.list') }}">
                Enquiry List
            </a>
        </li>

    </ul>

</li>
                @if(!$isStaff || $canViewFinance)
<li class="menu-dropdown">
    <a href="javascript:void(0);" class="menu-toggle">
        <i class="fa-solid fa-wallet"></i> Finance
    </a>

    <ul class="submenu">
        <li>
            <a href="{{ route('seller.financedashboard') }}">
                Earnings Dashboard
            </a>
        </li>

        <li>
            <a href="{{ route('seller.finance.transactions') }}">
                Transaction History
            </a>
        </li>

        <li>
            <a href="{{ route('seller.finance.payouts') }}">
                Payout Management
            </a>
        </li>

        <li>
            <a href="{{ route('seller.finance.commission') }}">
                Commission & Fees
            </a>
        </li>

        {{-- ✅ NEW --}}
        <li>
            <a href="{{ route('seller.finance.wallet') }}">
                 Wallet Settlement
            </a>
        </li>

    </ul>
</li>
@endif

<li class="menu-dropdown">
    <a href="javascript:void(0);" class="menu-toggle">
        <i class="fa-solid fa-chart-line"></i> Analytics
    </a>

    <ul class="submenu">

        <li><a href="{{ route('seller.analytics.lead') }}">Lead Analytics</a></li>
        <li><a href="{{ route('seller.analytics.referral') }}">Referral Analytics</a></li>
        <li><a href="{{ route('seller.analytics.sales') }}">Sales Overview</a></li>
        <li><a href="{{ route('seller.analytics.funnel') }}">Conversion Funnel</a></li>
        <li><a href="{{ route('seller.analytics.customers') }}">Customer Insights</a></li>
        <li><a href="{{ route('seller.analytics.campaigns') }}">Campaign ROI</a></li>

    </ul>
</li>

 <li>
    <a href="{{ route('seller.grievance.list') }}">
        <i class="fa-solid fa-headset"></i> Grievance
    </a>
</li>

<li>
    <a href="{{ route('seller.referrals.history') }}">
        <i class="fa-solid fa-people-arrows"></i> Referral History
    </a>
</li>


                       


            </ul>
        </div>
    </div>
</aside>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<style>
.submenu{position:fixed;}
.dashboardSidebarMenuArea.scrolldiv {
    max-height: calc(100vh - 70px);
    overflow-y: auto;
    cursor: auto;
    -webkit-overflow-scrolling: touch;
    padding-bottom: 60px;
}

.dashboardSidebarMenuArea.scrolldiv::-webkit-scrollbar {
  display: block;
}

.dashboardSidebarMenuArea.scrolldiv::-webkit-scrollbar {
  width: 6px; /* vertical scrollbar */
  height: 10px; /* horizontal scrollbar */
}

.dashboardSidebarMenuArea.scrolldiv::-webkit-scrollbar-track {
  background: #fff;
  border-radius: 10px;
}

.dashboardSidebarMenuArea.scrolldiv::-webkit-scrollbar-thumb {
  background: #8b8b8b;
  border-radius: 10px;
}

.dashboardSidebarMenuArea.scrolldiv:active{
    cursor: grabbing;
}    
.dasboardMenuList li a {
    text-align: center;
}


</style>

<script>

$( ".dashboardSidebarMenuArea ul li ul" ).find( "li" ) .closest("ul") .parent("li") .addClass( 'menu-dropdown' );
/*
$(document).ready(function () {

    // When dropdown menu clicked
    $(".dasboardMenuList li.menu-dropdown .menu-toggle").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();
        //$(".dashboardSidebarMenuArea").removeClass("scrolldiv");
        $(this).parent("li").addClass("show");
        $('.dasboardMenuList li.menu-dropdown .menu-toggle').not($(this)).parent("li").removeClass('show');		
    });

    // When clicking other menu items (non dropdown)
    $(".dasboardMenuList li:not(.menu-dropdown) a").on("click", function () {
        $(".dashboardSidebarMenuArea").addClass("scrolldiv");
        $(".dasboardMenuList li.menu-dropdown").removeClass("show");
    });

    // When clicking outside sidebar
    $(document).on("click", function (e) {
        if (!$(e.target).closest(".dashboardSidebarMenuArea").length) {
            $(".dashboardSidebarMenuArea").addClass("scrolldiv");
            $(".dasboardMenuList li.menu-dropdown").removeClass("show");
        }
    });

});
*/


$(document).ready(function(){

    $(".dasboardMenuList li.menu-dropdown .menu-toggle").on("click", function(e){
        e.preventDefault();
        e.stopPropagation();

        let submenu = $(this).next(".submenu");

        if(submenu.length){

            let pos = this.getBoundingClientRect();

            $(".submenu").hide(); // hide other submenus
            
            let windowHeight = $(window).height();
            // Temporarily show to get real height, or fallback
            submenu.css({display: 'block', visibility: 'hidden'});
            let submenuHeight = submenu.outerHeight() || 250;
            submenu.css({display: 'none', visibility: 'visible'});
            
            let topPos = pos.top;
            
            // Adjust top position if the menu overflows the bottom of the screen
            if (topPos + submenuHeight > windowHeight) {
                topPos = windowHeight - submenuHeight - 20;
            }

            submenu.css({
                top: topPos + "px",
                left: pos.right + "px",
                display: "block",
                position: "fixed"
            });

        }
    });

    // hide submenu when mouse leaves sidebar
    $(".dashboardSidebarinner").mouseleave(function(){
        $(".submenu").hide();
    });

});



/*

$(".dashboardSidebarMenuArea").on("mousedown", function(e){
    if(!$(this).hasClass("scrolldiv")) return;

    isDown = true;
    startY = e.pageY - $(this).offset().top;
    scrollTop = $(this).scrollTop();
});

$(document).on("mouseleave mouseup", function(){
    isDown = false;
});

$(document).on("mousemove", function(e){
    if(!isDown) return;

    var container = $(".dashboardSidebarMenuArea.scrolldiv");
    var y = e.pageY - container.offset().top;
    var walk = (y - startY) * 2;
    container.scrollTop(scrollTop - walk);
});

*/


</script>
