<!-- Sidebar -->
<div class="sideMenubarOverlay"></div>
<div class="sideMenubar">
    <div class="sideMenubarFldArea">
        <div class="sideMenubarFldAreainner">
            <div class="sideMenubarBody">
              <div class="sideMenubarHeader">

</div>



                <div class="sideMenuArea">
                    <ul class="sideMenuList">
                        <li class="title">General</li>
                        <li class="sideMenuListItem {{ Request::routeIs('manager.dashboard') ? 'active' : '' }}">
                            <a href="{{ route('manager.dashboard') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-house"></i></span>
                                    <span class="txt"> Dashboard </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li class="sideMenuListItem {{ Request::routeIs('manager.orders') ? 'active' : '' }}">
                            <a href="{{ route('manager.orders') }}" class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-cart-circle-plus"></i></span>
                                    <span class="txt"> All Orders </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li class="sideMenuListItem {{ Request::routeIs('manager.orders.completed') ? 'active' : '' }}">
                            <a href="{{ route('manager.orders.completed') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-cart-circle-check"></i></span>
                                    <span class="txt"> Completed Orders </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li class="sideMenuListItem {{ Request::routeIs('manager.orders.cancelled') ? 'active' : '' }}">
                            <a href="{{ route('manager.orders.cancelled') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-cart-circle-arrow-down"></i></span>
                                    <span class="txt"> Cancelled Orders </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li class="sideMenuListItem {{ Request::routeIs('manager.orders.declined') ? 'active' : '' }}">
                            <a href="{{ route('manager.orders.declined') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-cart-circle-arrow-down"></i></span>
                                    <span class="txt"> Declined Orders </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li class="sideMenuListItem {{ Request::routeIs('manager.orders.failed') ? 'active' : '' }}">
                            <a href="{{ route('manager.orders.failed') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-cart-circle-xmark"></i></span>
                                    <span class="txt"> Failed Orders </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li
                            class="sideMenuListItem {{ Request::routeIs('manager.orders.outfordelivery') ? 'active' : '' }}">
                            <a href="{{ route('manager.orders.outfordelivery') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-cart-flatbed-suitcase"></i></span>
                                    <span class="txt"> Out for Delivery </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li
                            class="sideMenuListItem {{ Request::routeIs('manager.orders.processing') ? 'active' : '' }}">
                            <a href="{{ route('manager.orders.processing') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-person-dolly"></i></span>
                                    <span class="txt"> Processing Orders </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li class="sideMenuListItem {{ Request::routeIs('manager.orders.refund') ? 'active' : '' }}">
                            <a href="{{ route('manager.orders.refund') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-house-person-return"></i></span>
                                    <span class="txt"> Refund Orders </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li class="sideMenuListItem {{ Request::routeIs('manager.customers') ? 'active' : '' }}">
                            <a href="{{ route('manager.customers') }}"
                                class="d-flex justify-content-between sideMenuLink">
                                <div class="d-flex leftPart">
                                    <span class="icon"><i class="fa-light fa-user"></i></span>
                                    <span class="txt"> Customers </span>
                                </div>
                                <div class="rightPart">
                                    <span class="rightArrow"></span>
                                </div>
                            </a>
                        </li>
                        <li class="sideMenuListItem">
                            <!-- Logout Form -->
                            <form action="{{ route('manager.logout') }}" method="POST" style="">
                                @csrf
                                <button type="submit" class="d-flex justify-content-between sideMenuLink">
                                    <div class="d-flex leftPart">
                                        <span class="icon"><i
                                                class="fa-light fa-arrow-left-from-bracket"></i></span>
                                        <span class="txt"> Logout </span>
                                    </div>
                                    <div class="rightPart">
                                        <span class="rightArrow"></span>
                                    </div>
                                </button>
                            </form>
                        </li>
                    </ul>
                    <!-- Wallet Balance -->
                    <div class="sidebarWalletInfo" style="">
                        <div class="sidebarWalletInfoinner" style="">
                            <strong>Wallet Balance:</strong> ₹{{ number_format($walletBalance, 2) }}
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
