<header class="dashboard-top-bar">

@php
    $customerLoggedIn = Auth::guard('customer')->check();
@endphp

<div class="dashboard-top-header">
    <div class="dashboardTopBar">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center dashboardTopBarinner">

                {{-- LEFT SECTION --}}
                <div class="d-flex align-items-center">

                    <button class="sidebar-trigger toggle-tooltips">
                        <i class="fa-solid fa-bars"></i>
                    </button>

                    <div class="dashboard-search-sec">
                        <i class="fa-solid fa-magnifying-glass"></i>
                        <input type="text" class="form-control" placeholder="Search...">
                    </div>

                    <button class="search-btn">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                </div>

                {{-- PROFILE SECTION --}}
                <div class="dashboard-profile-sec">

                    @if($customerLoggedIn)

                        {{-- LOGGED IN CUSTOMER --}}
                        <div class="profile-dropdown">
                            <button class="dropdown-toggle profile-box"
                                    type="button"
                                    data-bs-toggle="dropdown">

                                <img src="{{ 
                                    Auth::guard('customer')->user()->profile_picture
                                    ? asset(Auth::guard('customer')->user()->profile_picture)
                                    : asset('vtravelassets/images/avatar.png')
                                }}" class="w-100">
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">

                                <li>
                                    <a class="dropdown-item" href="{{ route('customer.profile') }}">
                                        <i class="fa-solid fa-user me-2"></i>
                                        My Profile
                                    </a>
                                </li>

                                <li><hr class="dropdown-divider"></li>

                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fa-solid fa-sign-out-alt me-2"></i>
                                            Logout
                                        </button>
                                    </form>
                                </li>

                            </ul>
                        </div>

                    @else

                        {{-- GUEST USER --}}
                        <div class="profile-dropdown">
                            <button class="dropdown-toggle profile-box"
                                    type="button"
                                    data-bs-toggle="dropdown">

                                <img src="{{ asset('vtravelassets/images/avatar.png') }}" class="w-100">
                            </button>

                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a href="{{ route('employeelogin') }}" class="dropdown-item">
                                        <i class="fa-solid fa-sign-in-alt me-2"></i>
                                        Login
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


{{-- ================= TOP MENU ================= --}}
<div class="dashboard-top-bottom">

    {{-- CATEGORY LINKS --}}
    <div class="top-bottom-sec">
        <ul>

            <li>
                <a href="{{ route('vtravelproduct.index', ['filter' => 'trending']) }}"
                   class="{{ request('filter') == 'trending' ? 'active' : '' }}">
                   Top Trending
                </a>
            </li>

            <li>
                <a href="{{ route('vtravelproduct.index', ['filter' => 'bestseller']) }}"
                   class="{{ request('filter') == 'bestseller' ? 'active' : '' }}">
                   Bestsellers
                </a>
            </li>

            <li>
                <a href="{{ route('vtravelproduct.index', ['filter' => 'discount']) }}"
                   class="{{ request('filter') == 'discount' ? 'active' : '' }}">
                   Best Discounts
                </a>
            </li>

        </ul>
    </div>


    {{-- SELLER LOGIN --}}
<div class="top-bottom-sec">
    <a href="{{ route('seller.login') }}" class="seller-login-btn">
        Seller Login
    </a>
</div>



    {{-- ORDERS / WISHLIST / CART --}}
    <div class="top-bottom-sec">
        <ul>

            {{-- Orders --}}
            <li>
                <a href="{{ $customerLoggedIn 
                        ? route('vtravel.order.list') 
                        : route('employeelogin') }}"
                   class="order-text">

                    Orders (<span id="orderCount">{{ $orderCount ?? 0 }}</span>)
                </a>
            </li>


            {{-- Wishlist --}}
            <li class="cart-icon">
                <a href="{{ $customerLoggedIn 
                        ? route('vtravel.cart.index') 
                        : route('employeelogin') }}">

                    <i class="fa-solid fa-heart"></i>
                    <span id="wishlistCount">{{ $wishlistCount ?? 0 }}</span>
                </a>
            </li>


            {{-- Cart --}}
            <li class="cart-icon">
                <a href="{{ $customerLoggedIn 
                        ? route('vtravel.cart.index') 
                        : route('employeelogin') }}">

                    <i class="fa-solid fa-cart-arrow-down"></i>
                    <span id="cartCount">{{ $cartCount ?? 0 }}</span>
                </a>
            </li>

        </ul>
    </div>

</div>

</header>