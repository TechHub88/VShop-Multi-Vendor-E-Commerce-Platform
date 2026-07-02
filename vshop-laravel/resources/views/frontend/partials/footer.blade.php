<footer class="web-footer"
    @if($about && $about->bgimage)
        style="background-image: url({{ asset('uploads/about/'.$about->bgimage) }});"
    @endif
>
    <div class="container">
        <div class="web-footer-inner">
            {{-- Footer content remains the same --}}
            <div class="webFooterTopArea">
                <div class="row rowBox">
                    <div class="col-lg-9 col-md-12 col-sm-12 col-12 columnBox firstColumn">
                        <div class="row rowBox2">

                            {{-- Quick Links --}}
                            <div class="col-lg-2 col-md-2 col-sm-6 col-12 columnBox2 footerBox">
                                <div class="footerBoxinner">
                                    <h4 class="footerTitle">Quick Links</h4>
                                    <ul class="usefulLinks">
                                        <li><a href="#">Home</a></li>
                                        <li><a href="#">About Us</a></li>
                                        <li><a href="{{ route('frontend.our.services') }}">Our Services</a></li>
                                        <li><a href="#">FAQ</a></li>
                                        <li><a href="#">Testimonials</a></li>
                                        <li><a href="{{ route('blogs.list') }}">Blogs</a></li>
                                    </ul>
                                </div>
                            </div>

                         {{-- Company --}}
<div class="col-lg-3 col-md-3 col-sm-6 col-12 columnBox2 footerBox">
    <div class="footerBoxinner">
        <h4 class="footerTitle">Company</h4>
        <ul class="usefulLinks">
            <li><a href="#">Privacy Policy</a></li>
            <li><a href="#">Return & Exchange Policy</a></li>
            <li><a href="#">Terms</a></li>

            {{-- Added New Links --}}
            <li><a href="{{ route('frontend.employeelogin') }}">Login</a></li>
            <li><a href="{{ route('frontend.register') }}">Register</a></li>
            <li><a href="{{ route('brandlogin') }}">Brand Login</a></li>
        </ul>
    </div>
</div>


                            {{-- Category --}}
                      <div class="col-lg-3 col-md-3 col-sm-5 col-12 columnBox2 footerBox">
                        <div class="footerBoxinner">
                            <h4 class="footerTitle">Category</h4>
                            <ul class="usefulLinks">
                                @foreach($categories as $category)
                                    <li>
                                        <a href="{{ route('frontend.product.details', ['slug' => $category->slug]) }}">
                                            {{ \Illuminate\Support\Str::limit($category->name, 20, '...') }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>



                            {{-- Contact --}}
                            <div class="col-lg-4 col-md-4 col-sm-7 col-12 columnBox2 footerBox">
                                <div class="footerBoxinner">
                                    <h4 class="footerTitle">Contact Us</h4>
                                    <div class="footerContactArea">
                                        <div class="footerContactItem">
                                            <p class="mb-0">{{ $footer->address ?? '' }}</p>
                                        </div>
                                        <div class="footerContactItem">
                                            <p class="mb-0">
                                                <a href="tel:{{ $footer->phone ?? '' }}" class="d-inline-block me-3">
                                                    Tel : {{ $footer->phone ?? '' }}
                                                </a>
                                                <a href="#" class="d-inline-block">
                                                    Fax : {{ $footer->fax ?? '' }}
                                                </a>
                                            </p>
                                        </div>
                                        <div class="footerContactItem">
                                            <p class="mb-0">
                                                <a href="mailto:{{ $footer->email ?? '' }}">
                                                    Email: {{ $footer->email ?? '' }}
                                                </a>
                                            </p>
                                        </div>
                                    </div>                
                                </div>
                            </div>

                        </div>
                    </div>    

                    {{-- Logo & Social Links --}}
                    <div class="col-lg-3 col-md-12 col-sm-12 col-12 columnBox secondColumn">
                        <div class="aboutFooterArea">
                            <div class="footerLogo">
                               <img src="{{ $footer && $footer->footer_logo 
            ? asset('uploads/footer/'.$footer->footer_logo) 
            : asset('assets/images/logo.png') }}" 
     class="img-fluid" alt="">

                            </div>
                          <ul class="ftsocialLinks">
				<li><a href="#"><i class="fa fa-facebook"></i></a></li>
				<li><a href="#"><i class="fa fa-linkedin"></i></a></li>
				<li><a href="#"><i class="fa fa-youtube-play"></i></a></li>
				<li><a href="#"><i class="fa fa-instagram"></i></a></li>			
				</ul>
                        </div>
                    </div>      

                </div>  
            </div>  

            {{-- Footer Bottom --}}
            <div class="webFooterBottomArea">
                <p class="mb-0 text-center copyRightText">© 2025 All rights reserved for Mafatlal</p>
            </div>  
        </div>
    </div>
</footer>
<div class="mob-footer">
<div class="mob-footer-area">
<ul class="mob-footer-list">
<li class="active">
<a href="#" class="">
<span class="icon"><i class="fa-light fa-house"></i></span>
<span class="txt">Home</span>
</a>
</li>
<li>
<a href="#" class="">
<span class="icon"><i class="fa-light fa-shirt"></i></span>
<span class="txt">Product</span>
</a>
</li>
<li>
<a href="#" class="">
<span class="icon"><i class="fa-light fa-user"></i></span>
<span class="txt">Profile</span>
</a>
</li>
<li>
<a href="#" class="">
<span class="icon"><i class="fa-light fa-bag-shopping"></i></span>
<span class="txt">Cart</span>
</a>
</li>
</ul>
</div>
</div>

<style>

.product-img-fixed {
    width: 100%;
    height: 260px;       /* adjust height as needed */
    object-fit: cover;   /* keeps image proportional and cropped nicely */
    display: block;
    border-radius: 6px;  /* optional */
}

</style>

    <!-- jQuery is now loaded in the head section of layout.blade.php -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.umd.js"></script>
    <script src="https://unpkg.com/swiper@11/swiper-bundle.min.js"></script>
    <script src="{{ asset('assets/js/sal.min.js')}}"></script>	
    <script src="{{ asset('assets/js/tilt.min.js')}}"></script>	
    <script src="{{ asset('assets/js/main.js')}}"></script>
