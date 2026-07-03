<div class="adminPagesHeader">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center adminPagesHeaderinner">
            <div class="d-flex align-items-center leftPart">
                <button class="sideMenuBtn">
                    <span class="desktopMenuImage"><i class="fa-regular fa-arrow-right"></i></span>
                    <span class="mobileMenuImage"><i class="fa-regular fa-bars"></i></span>
                </button>
                <div class="ml-10 pageBreadcumArea">
                    <ul class="pageBreadcum">
                        <li class="pageBreadcumItem">
                            <a href="#" class="pageBreaLink">
                                <span class="icon"><i class="fa-light fa-house"></i></span>
                                <span class="pageTitle">Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
            <!--<div class="middlePart">
 <a href="#" class="navbarBrand"><img src="images/logo.png" class="img-fluid" alt=""></a>
 </div>-->
            <div class="rightPart">
                <ul class="rightPartList">
                    <!--<li class="rightPartListItem settingsItem">-->
                    <!--<a href="#" class="settingLink"><span class="icon"><i class="fa-regular fa-gear"></i></span></a>-->
                    <!--</li>-->

                    <li class="rightPartListItem dropdown notificationItem">
                        <span class="dropdown-toggle notificationToggle" data-bs-toggle="dropdown">
                            <span class="icon"><i class="fa-light fa-bell"></i></span>
                            <span
                                class="position-absolute topbar-badge fs-10 translate-middle badge bg-danger rounded-pill noticount">3</span>
                        </span>
                        <div class="dropdown-menu dropdowNotificationMenu">
                            <div class="dropdowNotificationHeader">
                                <div
                                    class="d-flex justify-content-between align-items-center dropdowNotificationHeaderinner">
                                    <div class="leftPart">
                                        <h5 class="mb-0 title">Notification</h5>
                                    </div>
                                    <div class="rightPart">
                                        <a href="#" class="clearAllBtn">Clear All</a>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdowNotificationBody">
                                <ul class="dropdowNotificationList">
                                    @php
                                        $latestOrders = $latestOrderItems->take(5);
                                    @endphp

                                    @if ($latestOrders->count() > 0)
                                        {{-- Header with count --}}
                                        <li class="dropdowNotificationItem">
                                            <div class="dropdowNotificationIteminner">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <span class="small text-primary fw-bold">
                                                        <i class="fa-solid fa-bell me-1"></i>
                                                        {{ $latestOrders->count() }} New Order(s)
                                                    </span>
                                                    <span class="badge bg-success">Just Received</span>
                                                </div>
                                            </div>
                                        </li>

                                        {{-- Order list --}}
                                        @foreach ($latestOrders as $index => $order)
                                            <li class="dropdowNotificationItem">
                                                <div class="dropdowNotificationIteminner">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-secondary me-2">{{ $index + 1 }}</span>
                                                        <span class="small">
                                                            Order #{{ $order->id }}
                                                            <span class="text-success ms-2">
                                                                <i class="fa-solid fa-check-circle"></i> Received
                                                            </span>

                                                        </span>
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    @else
                                        <li class="dropdowNotificationItem">
                                            <p class="text-center p-2 small text-muted">No recent orders</p>
                                        </li>
                                    @endif
                                </ul>

                                <div class="dropdowNotificationFooter">
                                    <div class="dropdowNotificationFooterinner">
                                        <a href="#" class="viewAllBtn">View All Notification</a>
                                    </div>
                                </div>
                            </div>

                            <!--<a href="#" class="notificationLink"></a>-->

                    </li>
                    <li class="rightPartListItem dropdown userItem">
                        <span class="dropdown-toggle userToggle" data-bs-toggle="dropdown">
                            <span class="icon"><img src="{{ asset('assets/images/userThumb.png') }}" alt=""
                                    class="img-fluid avatar rounded-3 border border-light border-3"
                                    style=""></span>
                        </span>
                        <ul class="dropdown-menu dropdownUserMenu">
                            <li>
                                <a class="dropdown-item" href="{{ route('manager.profile') }}">
                                    <i class="fa-light fa-user"></i>
                                    <span class="txt">Profile</span>
                                </a>
                            </li>

                            <li>
                                <a class="dropdown-item" href="{{ route('manager.tickets.index') }}">
                                    <i class="fa-light fa-circle-question"></i>
                                    <span class="txt">Help</span>
                                </a>
                            </li>

                            <li>
                                <form action="{{ route('manager.logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit"
                                        class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="d-flex align-items-center">
                                            <span class="icon"><i
                                                    class="fa-light fa-arrow-left-from-bracket"></i></span>
                                            <span class="txt ms-2">Logout</span>
                                        </div>
                                        <div class="rightPart">
                                            <span class="rightArrow"></span>
                                        </div>
                                    </button>
                                </form>
                            </li>

                        </ul>


                        <!--<a href="#" class="userLink">
 <span>Welcome,
  @if (Auth::check() && Auth::user()->role == 'manager')
{{ Auth::user()->name }}
@else
Guest
@endif
 </span>
 </a>-->
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
