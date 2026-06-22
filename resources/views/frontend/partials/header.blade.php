<header class="web-header">
   <div class="web-header-bottom">
      <div class="container">
         <div class="web-header-bottom-inner">
            <nav class="navbar navbar-expand-lg">
               <div class="leftPart">
                  <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                     data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false"
                     aria-label="Toggle navigation">
                  <span class="nan-btn-icon"></span>
                  <span class="nan-btn-icon"></span>
                  <span class="nan-btn-icon"></span>
                  </button>
                  <a class="navbar-brand" href="{{ route('dashboard') }}">
                  <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid" alt="">
                  </a>
               </div>
               <!--<div class="web-header-search">
                  <input type="text" id="searchKeyword" class="searchInput" placeholder="Search...">
                  <span class="icon"><img src="{{ asset('assets/images/searc-icon.svg')}}" class="img-fluid" alt=""></span>
               </div>-->
               <div class="menu_overlay"></div>
               <div class="collapse navbar-collapse" id="navbarScroll">
                  <ul class="navbar-nav my-2 my-lg-0" style="--bs-scroll-height: 100px;">
                     <li class="nav-item active">
                        <a class="nav-link active" href="{{ route('dashboard') }}">Home</a>
                     </li>
					  <li class="nav-item">
                        <a class="nav-link" href="{{ route('about') }}">About Us</a>
                     </li>
					
                     <!--<li class="nav-item">
                        <a class="nav-link" href="{{ route('frontend.our.services') }}">Our Services</a>
                     </li>-->
                     <li class="nav-item">
                        <a class="nav-link" href="#">Product</a>
                        @if(isset($categories) && $categories->count())
                        <ul class="sub-menu">
                           @foreach($categories as $category)
                           <li>
                              <a href="{{ route('frontend.product.details', ['slug' => $category->slug]) }}">
                              {{ \Illuminate\Support\Str::limit($category->name, 30, '...') }}
                              </a>
                           </li>
                           @endforeach
                        </ul>
                        @endif
                     </li>
                      <li class="nav-item">
                        <a class="nav-link" href="#">Catalogue</a>
                        <ul class="sub-menu">
                           <li><a href="{{ route('frontend.schooluniform.index') }}">School Uniform Portfolio</a></li>
                           <li><a href="{{ route('frontend.hospitaluniform.index') }}">Healthcare Uniform Portfolio</a></li>
                           <li><a href="{{ route('frontend.hospitalityuniform.index') }}">Hospitality Uniform Portfolio</a></li>
                           <li><a href="{{ route('frontend.corporateuniform.index') }}">Corporate Uniform Portfolio</a></li>
                        </ul>
                     </li>
                     <li class="nav-item">
                        <a class="nav-link" href="{{ route('frontend.product.details', ['slug' => '']) }}">Shop Now</a>
                     </li>
					  <li class="nav-item">
                        <a class="nav-link" href="{{ route('contact') }}">Contact Us</a>
                     </li>
                     <!--<li class="nav-item">-->
                     <!--    <a class="nav-link" href="#">Tie Ups</a>-->
                     <!--    <ul class="sub-menu">-->
                     <!--        <li><a href="#">Sub Menu 1</a></li>-->
                     <!--        <li><a href="#">Sub Menu 2</a></li>-->
                     <!--        <li><a href="#">Sub Menu 3</a></li>-->
                     <!--        <li><a href="#">Sub Menu 4</a></li>-->
                     <!--        <li><a href="#">Sub Menu 5</a></li>-->
                     <!--    </ul>-->
                     <!--</li>-->
                  </ul>
               </div>
               <div class="d-flex justify-content-end rightPart">
				<div class="search-switcher">							
                     <span class="search-opener" data-bs-toggle="modal" data-bs-target="#searchModal">
                     <i class="fa-regular fa-magnifying-glass"></i>
                     </span>
                  </div>
                  <div class="web-header-user-dropdown">
                     <div class="web-header-user-dropdown-toogler">
                        <!--<span class="userIcon"><i class="fa-regular fa-user"></i></span>-->
                        <span class="userName">
                        @if(Auth::guard('customer')->check())
                        Hi, {{ Auth::guard('customer')->user()->name }}
                        @else
                        @endif
                        </span>
                        <!-- Only show arrow if logged in -->
                        @if(Auth::guard('customer')->check())
                        <span class="arrow"><i class="fa-regular fa-angle-down"></i></span>
                        @endif
                        @if(!Auth::guard('customer')->check())
                        <span class="d-flex align-items-center loginLink">
                           <!-- Login Button -->
                           <a href="{{ route('frontend.employeelogin') }}" class="ps-0 loginHedaerLink">
                           <span class="icon"><i class="fa-regular fa-user"></i></span> <span class="txt">Login</span>
                           </a>
                           <!-- Signup Button -->
                           <a href="{{ route('frontend.register') }}" class="registerHedaerLink">
                            <span class="icon"><i class="fa-regular fa-user-plus"></i></span> <!--<span class="txt">Sign Up</span>-->
                           </a>
                        </span>
                        @endif
                     </div>
                     <!-- Dropdown Menu ONLY for logged-in users -->
                     @if(Auth::guard('customer')->check())
                     <div class="web-header-user-dropdown-menu">
                        <div class="dropdown-item">
                           <a href="{{ route('frontend.profile') }}"><span class="icon"><i class="fa-light fa-user"></i></span> <span class="txt">My Profile</span></a>
                        </div>
                        <div class="dropdown-item">
                           <a href="{{ route('frontend.add-address') }}"><span class="icon"><i class="fa-light fa-address-book"></i></span> <span class="txt">My Address</span></a>
                        </div>
                        <div class="dropdown-item">
                           <a href="{{ route('frontend.orders') }}"><span class="icon"><i class="fa-light fa-cart-circle-check"></i></span> <span class="txt">My Orders</span></a>
                        </div>
                        <div class="dropdown-item dropdown-logout-item">
                           <form method="POST" action="{{ route('frontend.logout') }}" class="m-0">
                              @csrf
                              <button type="submit" class="btn btn-danger btn-sm logout-btn-sec">
                              <span class="icon"><i class="fa-light fa-arrow-left-from-bracket"></i></span> <span class="txt">Logout</span>
                              </button>
                           </form>
                        </div>
                     </div>
                     @endif
                  </div>
                  <!--<div class="search-switcher">							
                     <span class="search-opener" data-bs-toggle="modal" data-bs-target="#searchModal">
                     <i class="fa-regular fa-magnifying-glass"></i>
                     </span>
                  </div>-->
                  <div class="topcart d-flex align-items-center gap-3">
                     <!-- 🛒 Cart Button -->
                     <a class="cart-toggler toggle-cartside position-relative" href="{{ Auth::guard('customer')->check() ? route('frontend.cart') : route('frontend.employeelogin', ['redirect' => route('frontend.cart')]) }}">
                     <span class="content-cart d-flex align-items-center gap-2">
                     <span class="my-cart position-relative">
                     <!--<img src="{{ asset('assets/images/cart-icon.svg') }}" class="img-fluid desktopIcon" alt="Cart">-->
                     <i class="fa-regular fa-cart-shopping"></i>
                     @if(isset($cartCount) && $cartCount > 0)
                     <span class="cart-count-badge">{{ $cartCount }}</span>
                     @endif
                     </span>
                     </span>
                     </a>
                  </div>
               </div>
            </nav>
         </div>
      </div>
      <div class="webHeaderMobileSearchArea">
         <div class="container">
            <div class="webHeaderMobileSearchAreainner">
               <input type="text" class="form-control searchInput" placeholder="Search and brands and products">
               <button type="button" class="searchBtn"><i class="fa-regular fa-magnifying-glass"></i></button>
            </div>
         </div>
      </div>
   </div>
</header>
<style>
</style>
<script>
   document.querySelector('.web-header-user-dropdown-toggler').addEventListener('click', function() {
   const menu = document.querySelector('.web-header-user-dropdown-menu');
   menu.style.display = (menu.style.display === 'block' ? 'none' : 'block');
   });
   
</script>
<script>
   // JavaScript to handle the Enter key press and redirect
   let url = "{{ route('frontend.product.details', ['slug' => 'all']) }}";
   
   document.getElementById('searchKeyword').addEventListener('keypress', function(event) {
       if (event.key === 'Enter') {
           let keyword = this.value.trim();
   
           if (keyword !== '') {
               window.location.href = url + "?search=" + encodeURIComponent(keyword);
           }
       }
   });
   
</script>