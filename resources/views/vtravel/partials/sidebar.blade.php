<aside class="dashboardSidebar">
<div class="dashboardSidebarinner">
<div class="dashboardSidebarWrapper">
<div class="dashboardSidebarMenuArea scrollbar">
<div class="dashboardSidebarMenu">
            <ul class="dasboardMenuList">

<li>
<a href="{{ route('vtravelhome') }}"
class="{{ request()->routeIs('vtravelhome') ? 'active' : '' }}">
<i class="fa-solid fa-house"></i>
Dashboard
</a>
</li>

<li>
<a href="{{ route('vtravelproduct.index') }}"
class="{{ request()->routeIs('vtravelproduct.*') ? 'active' : '' }}">
<i class="fa-solid fa-gear"></i>
Tour Package
</a>
</li>

@php
$isCustomerLoggedIn = auth()->guard('customer')->check();
$loginRoute = route('employeelogin');
@endphp

<li>
<a href="{{ $isCustomerLoggedIn ? route('vtravel.order.list') : $loginRoute }}"
class="{{ request()->routeIs('vtravel.order.*') ? 'active' : '' }}">
<i class="fa-solid fa-gamepad"></i>
Order
</a>
</li>

<li>
<a href="{{ $isCustomerLoggedIn ? '#' : $loginRoute }}"
class="{{ request()->routeIs('seller.*') ? 'active' : '' }}">
<i class="fa-solid fa-user"></i>
Seller Profile
</a>
</li>

<li>
<a href="{{ $isCustomerLoggedIn ? '#' : $loginRoute }}"
class="{{ request()->routeIs('marketplace.*') ? 'active' : '' }}">
<i class="fa-solid fa-shop"></i>
Market Place
</a>
</li>

</ul>

</div>
</div>
</div>
</div>
</aside>