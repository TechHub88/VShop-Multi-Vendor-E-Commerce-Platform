@extends('frontend.layout')

@section('content')

	

<!--
    <div class="hpmePanerBannerSlider hero_carosel owl-carousel owl-theme">

        @if($sliders && $about)
            @foreach($sliders->take(2) as $index => $slider)
                <div class="hpmePanerBannerSliderItem" 
                     data-background="{{ asset('uploads/slider/'.$slider->banner_image) }}">
                    <div class="container">
                        <div class="hpmePanerBannerSliderIteminner">
                            <div class="row rowBox">

                                <div class="col-lg-5 col-md-5 col-sm-5 col-12 columnBox">
                                    <div class="hpmePanerBannerSliderThmnail">
                                        <img data-src="{{ asset('uploads/slider/'.$slider->second_image) }}" 
                                             class="img-fluid lazyload" alt="">
                                    </div>
                                </div>

                                <div class="col-lg-7 col-md-7 col-sm-7 col-12 columnBox">
                                    <div class="slider-content-1 slider-animated-1 hpmePanerBannerSliderContent">

                                        <h1 class="mb-15 animated title">
                                            {{ $index == 0 ? $about->about1 : $about->about3 }}
                                        </h1>

                                        <p class="mb-40 animated para">
                                            {{ $index == 0 ? $about->about2 : $about->about4 }}
                                        </p>

                                        <div class="hpmePanerBannerBtns">
                                            <a href="#" class="animated hpmePanerBannerBtn">Get Started</a>
                                        </div>

                                        <div class="custom-dots"></div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif

    </div>
</section>
-->
<section class="hpmePanerBanner">
    <div class="homePageBanner owl-carousel owl-theme">
	<div class="homePageBannerItem">
	<img data-src="{{ asset('assets/images/banner-image.png')}}" class="img-fluid lazyload" alt="">
	</div>
    </div>
</section>

<section class="pt-50 partnersSec">
    <div class="container">
        @if($partnerContent)
        <div class="mb-40 text-center section-header">
            <h2 class="mb-0 title" data-animation="slideInUp"><!--{{ $partnerContent->title }}-->Trusted by India’s leading brands</h2>
            <!--<h3 class="mb-0 title2" data-animation="slideInUp" data-animation-delay="200ms">{{ $partnerContent->tagline }}</h3> -->           
        </div>
        @endif
		<!--
        <div class="partnersSeciner" data-animation="slideInUp" data-animation-delay="600ms">
            <div class="partnersSlider owl-carousel owl-theme">
                @foreach($partners as $partner)
                <div class="partnersSliderItem">
                    <div class="partnersThumnail">
                        <img data-src="{{ asset('uploads/partners/'.$partner->partner_logo) }}" class="img-fluid lazyload" alt="{{ $partner->partner_name }}">
                    </div>
                </div>
                @endforeach
            </div>
        </div>
		-->
		<div class="clientSliderWrapper" style="">
	<div class="swiper-container marquee-right">
    <div class="swiper-wrapper">
	@foreach($partners as $partner)
      <div class="swiper-slide">
        <div class="swiper_slideinner">
          <div class="client_logo_bx">
            <div class="client_logo_bxinner">
              <div class="client_logo_bx_thumnail"> 
			  <img data-src="{{ asset('uploads/partners/'.$partner->partner_logo) }}" class="img-fluid lazyload" alt="{{ $partner->partner_name }}">
			  </div>
            </div>
          </div>
        </div>
      </div>
	   @endforeach
    </div>
  </div>
</div>		
		
		
		<p class="mb-0 mt-20 text-center btmpara" data-animation="slideInUp">
		<!--{{ $partnerContent->description }}-->
		Over the decades, Mafatlal Uniforms has partnered with institutions across sectors to deliver<br> trusted, customised uniform solutions that foster long-term relationships and responsible growth.
		</p>
    </div>
</section>

		 {{-- <section class="pt-50 pb-30 homeCategorySec">
            <div class="container">
                <div class="mb-40 text-center section-header" data-animation="slideInUp">
				<h2 class="title" >Tailored Uniform Solutions, Trusted for Generations</h2>
				<!--<h3 class="title2"></h3>-->
				<p class="mb-0 para">We offer a full range of customised uniforms designed for functionality and comfort. With 120+ years of experience, we deliver more than just uniforms; we deliver reliability, consistency, and commitment. Our products are designed to meet modern needs while reflecting timeless values, making Mafatlal a preferred name among top fabric manufacturers in India.</p>
                </div>
				<div class="homeCategorySecinner">
				<div class="row rowBox">
				
				<div class="col-lg-4 col-md-4 col-sm-6 col-6 columnBox homeCategoryBox mb-30" data-animation="slideInUp">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail1.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">School Uniforms</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-6 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="100ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail2.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Healthcare Uniforms</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-6 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="200ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail3.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Hospitality Uniforms</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-6 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="300ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail4.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Corporate Uniforms</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-6 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="400ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail5.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Oil, Gas & Petroleum</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-6 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="500ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail6.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Industrial Uniforms</h3>
                </div>
                </a>
                </div>
				
				
                </div>
                </div>
            </div>
        </section> --}}

		<section class="pt-50 homeCategorySec">
    <div class="container">
        <div class="mb-40 text-center section-header" data-animation="slideInUp">
             @if($categories && $categories->first()) <!-- Take first category for section title -->
        <h2 class="title">{{ $categories->first()->title }}</h2>
        <p class="mb-0 para">{{ $categories->first()->description }}</p>
    @else
        <h2 class="title">Tailored Uniform Solutions, Trusted for Generations</h2>
        <p class="mb-0 para">
            We offer a full range of customised uniforms designed for functionality and comfort...
        </p>
    @endif
            
          
        </div>

       <div class="homeCategorySecinner">
	    <div class="homeCategorySliderArea">
    <div class="swiper homeCategorySwiper">
    <div class="swiper-wrapper">
        @foreach($categories as $index => $category)
            <div class="swiper-slide homeCategoryBox">
                 
                <a href="{{ route('frontend.product.details', ['slug' => $category->slug]) }}" 
                   class="homeCategoryBoxinner">

                  <div class="homeCategoryThumnail">
    @if($category->banner_image)
        <img data-src="{{ asset($category->banner_image) }}" 
             class="img-fluid lazyload" 
             alt="{{ $category->name }}">
    @else
        <img data-src="{{ asset('assets/images/default-category.png') }}" 
             class="img-fluid lazyload" 
             alt="{{ $category->name }}">
    @endif
</div>


                    <!--<div class="text-center homeCategoryOverlayCintent">
                        <h3 class="mb-0 title">{{ $category->name }}</h3>
                    </div>-->
                </a>
            </div>
        @endforeach
    </div>
	<div class="swiper-pagination"></div>    
	</div>	
	<div class="swiper-navigation-area">
	<button class="custom-navigation-button custom-prev">
	  <i class="fa-light fa-angle-left"></i>
	</button>
	<button class="custom-navigation-button custom-next">
	  <i class="fa-light fa-angle-right"></i>
	</button>
	</div>
	</div>
	
	
	
</div>

    </div>
</section>
<!--
<section class="pt-50 pb-50 aboutSec"
    @if($about && $about->bgimage)
        data-background="{{ asset('uploads/about/'.$about->bgimage) }}"
    @endif
    style="color: white;"
>
    <div class="container">
        <div class="aboutSecinner">
            <div class="text-center aboutContent">
                <h3><b>About Us</b></h3>
                @if($about)
                    <p>{!! nl2br(e($about->description)) !!}</p>
                @endif
            </div>
        </div>
    </div>
</section>
-->

<section class="pt-50 pb-70 home-aboutUsSec">
    <div class="container">
        <div class="aboutUsSecinner">
			<div class="row rowBox">
			<div class="col-lg-6 col-md-6 col-sm-12 col-12 colimnBox home-aboutUsThumnailBox">
			<div class="mn-about-img" data-animation="slideInUp">
			 <img data-src="{{ asset('assets/images/aboutImage.jpg')}}" class="img-fluid lazyload" alt="">
			  <img data-src="{{ asset('assets/images/aboutImage2.jpg')}}" class="img-fluid lazyload" alt="">
			   <img data-src="{{ asset('assets/images/aboutImage3.jpg')}}" class="img-fluid lazyload" alt="">
			    <img data-src="{{ asset('assets/images/aboutImage4.jpg')}}" class="img-fluid lazyload" alt="">
			</div>			
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-12 colimnBox home-aboutUsContentBox">
            <div class="home-aboutUsContent" data-animation="slideInUp">	
				<span class="about-meta">
				<div class="about-meta-inner"><i class="fa-light fa-uniform-martial-arts"></i> Our Story</div>
				</span>
                <h2 class="title">About Mafatlal Uniforms</h2> 
				<h3 class="title-2">Textile companies in India with a 120+ year legacy</h3>	
                @if($about)
                    <p class="para">{!! nl2br(e($about->description)) !!}</p>
                @endif
				<div class="viewAllBtnArea">
				<a href="#" class="viewAllBtn">Explore the Collections</a>
				</div>
			
			
			</div>
			</div>
			</div>
        </div>
    </div>
</section>
	
	
		

		 {{-- <section class="pt-50 pb-50 homeFeatureSec" data-background="{{ asset('assets/images/featureBg.jpg')}}">
            <div class="container">
                <div class="homeFeatureSecinner" data-animation="slideInUp">

				 <div class="homeFeatureSlider owl-carousel owl-theme">
				 
				 <div class="homeFeatureSliderItem">
                 <div class="text-center homeFeatureContent">
                    <h3 class="mb-25 title">Strategic Growth: Mafatlal Scales Operations in Gujarat</h3>                   
					<p class="mb-0 para">Mafatlal Industries Ltd. (MIL) has announced plans to invest an additional ₹200 crores over the next three years to expand and upgrade its manufacturing facilities in Nadiad and Navsari, Gujarat. This follows a prior ₹100 crore investment dedicated to modernising these units. According to the Chairman of MIL, the strategic expansion is aimed at enhancing product quality and increasing production capacity to meet growing market demand.</p>
                </div>  
				</div>
				 <div class="homeFeatureSliderItem">
                 <div class="text-center homeFeatureContent">
                    <h3 class="mb-25 title">Strategic Growth: Mafatlal Scales Operations in Gujarat</h3>                   
					<p class="mb-0 para">Mafatlal Industries Ltd. (MIL) has announced plans to invest an additional ₹200 crores over the next three years to expand and upgrade its manufacturing facilities in Nadiad and Navsari, Gujarat. This follows a prior ₹100 crore investment dedicated to modernising these units. According to the Chairman of MIL, the strategic expansion is aimed at enhancing product quality and increasing production capacity to meet growing market demand.</p>
                </div>  
				</div>
				 <div class="homeFeatureSliderItem">
                 <div class="text-center homeFeatureContent">
                    <h3 class="mb-25 title">Strategic Growth: Mafatlal Scales Operations in Gujarat</h3>                   
					<p class="mb-0 para">Mafatlal Industries Ltd. (MIL) has announced plans to invest an additional ₹200 crores over the next three years to expand and upgrade its manufacturing facilities in Nadiad and Navsari, Gujarat. This follows a prior ₹100 crore investment dedicated to modernising these units. According to the Chairman of MIL, the strategic expansion is aimed at enhancing product quality and increasing production capacity to meet growing market demand.</p>
                </div>  
				</div>
				
				</div>
				
                </div>
            </div>
        </section>  --}}

		<section class="pt-50 pb-50 homeFeatureSec" 
         @if($features->first() && $features->first()->image) 
             data-background="{{ asset('uploads/news/'.$features->first()->image) }}" 
         @else
             data-background="{{ asset('assets/images/featureBg.jpg') }}"
         @endif>
    <div class="container">
        <div class="homeFeatureSecinner" data-animation="slideInUp">
            <div class="homeFeatureSlider owl-carousel owl-theme">
                @foreach($features as $feature)
                    <div class="homeFeatureSliderItem">
                        <div class="text-center homeFeatureContent">
                            <h3 class="mb-25 title">{{ $feature->title }}</h3>
                            <p class="mb-0 para">{!! nl2br(e($feature->description)) !!}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>



<script>
$(document).ready(function() {
    $('.homeFeatureSlider').owlCarousel({
        loop: true,
        margin: 30,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 5000,
        autoplayHoverPause: true,
        items: 1, // One slide at a time (matches your original design)
        responsive: {
            0: { items: 1 },
            768: { items: 1 },
            992: { items: 1 }
        }
    });
});
</script>

	

{{-- 	
		<section class="pt-50 pb-50 productListingSec homeProductListingSec">
            <div class="container">
				<div class="mb-40 text-center section-header">
				<h2 class="mb-0 title" data-animation="slideInUp">Shop the latest arrivals, handpicked and ready to be dispatched</h2>				
                </div>
				<div class="productListingSecinner">
                <div class="productListTabNav" data-animation="slideInUp" data-animation-delay="200ms">
				<ul class="nav nav-pills">
				  <li class="nav-item">
					<a class="nav-link" data-bs-toggle="pill" href="#menu1">Corporate Uniforms</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link active" data-bs-toggle="pill" href="#menu2">Oil, Gas & Petroleum</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" data-bs-toggle="pill" href="#menu3">Healthcare Uniforms</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" data-bs-toggle="pill" href="#menu4">School Uniforms</a>
				  </li>
				  <li class="nav-item">
					<a class="nav-link" data-bs-toggle="pill" href="#menu5">Industrial Uniforms</a>
				  </li>
				</ul>
                </div>
				<div class="tab-content productListTabNvContent" data-animation="slideInUp" data-animation-delay="400ms">
				 <div class="tab-pane" id="menu1">
				 </div>
				<div class="tab-pane active" id="menu2">
				<div class="productListingSlider owl-carousel owl-theme">
				
				<div class="productListingSliderItem">
				<div class="productBox">
				<div class="productBoxinner">
				<div class="productThumnail">
				<a href="#" class="d-block">
				<img data-src="images/product1.png" class="img-fluid lazyload" alt="">   
				</a>	
				</div>
				<div class="text-center productContent">
				<h4 class="proTitle"><a href="#">Navy and Light Blue HP Petroleum Uniform</a></h4>
				<div class="proPrice">
				<span class="regularPrice">Rs. 3,690.00</span>
				</div>
				</div>
				</div>
				</div>
               </div>
			   <div class="productListingSliderItem">
				<div class="productBox">
				<div class="productBoxinner">
				<div class="productThumnail">
				<a href="#" class="d-block">
				<img data-src="{{ asset('assets/images/product2.png')}}" class="img-fluid lazyload" alt="">   
				</a>	
				</div>
				<div class="text-center productContent">
				<h4 class="proTitle"><a href="#">Water Proof Monsoon HP Petroleum Uniform</a></h4>
				<div class="proPrice">
				<span class="regularPrice">Rs. 3,690.00</span>
				</div>
				</div>
				</div>
				</div>
               </div>
			   <div class="productListingSliderItem">
				<div class="productBox">
				<div class="productBoxinner">
				<div class="productThumnail">
				<a href="#" class="d-block">
				<img data-src="{{ asset('assets/images/product3.png')}}" class="img-fluid lazyload" alt="">   
				</a>	
				</div>
				<div class="text-center productContent">
				<h4 class="proTitle"><a href="#">Navy Blue Coat HP Petroleum Uniform</a></h4>
				<div class="proPrice">
				<span class="regularPrice">Rs. 1,690.00</span>
				</div>
				</div>
				</div>
				</div>
               </div>
			   <div class="productListingSliderItem">
				<div class="productBox">
				<div class="productBoxinner">
				<div class="productThumnail">
				<a href="#" class="d-block">
				<img data-src="{{ asset('assets/images/product4.png')}}" class="img-fluid lazyload" alt="">   
				</a>	
				</div>
				<div class="text-center productContent">
				<h4 class="proTitle"><a href="#">Water Proof Raincoat HP Petroleum Uniform</a></h4>
				<div class="proPrice">
				<span class="regularPrice">Rs. 3,690.00</span>
				</div>
				</div>
				</div>
				</div>
               </div>
			   <div class="productListingSliderItem">
				<div class="productBox">
				<div class="productBoxinner">
				<div class="productThumnail">
				<a href="#" class="d-block">
				<img data-src="{{ asset('assets/images/product5.png')}}" class="img-fluid lazyload" alt="">   
				</a>	
				</div>
				<div class="text-center productContent">
				<h4 class="proTitle"><a href="#">Navy Blue Tshirt HP Petroleum Uniform</a></h4>
				<div class="proPrice">
				<span class="regularPrice">Rs. 4,99.00</span>
				</div>
				</div>
				</div>
				</div>
               </div>
			   
			   
               </div>
			   </div>
				<div class="tab-pane" id="menu3">
				</div>
				<div class="tab-pane" id="menu4">
				</div>
				<div class="tab-pane" id="menu5">
				</div>
			   
			   </div>
			   <div class="mt-30 text-center viewAllBtnArea" data-animation="slideInUp" data-animation-delay="600ms">
			   <a href="" class="viewAllBtn">View All Products</a>
			   </div>
			   
			   </div>
			   
            </div>
        </section>  --}}

        <section class="pt-50 pb-20 productListingSec homeProductListingSec">
    <div class="container">
        <div class="mb-20 text-center section-header">
            <h2 class="mb-0 title" data-animation="slideInUp">
                Shop the latest arrivals, handpicked and ready to be dispatched
            </h2>                
        </div>

        <div class="productListingSecinner">
            <div class="productListTabNav" data-animation="slideInUp" data-animation-delay="200ms">
                <ul class="nav nav-pills">
                    @foreach($categories as $index => $category)
                        <li class="nav-item">
                           <a class="nav-link {{ $index == 0 ? 'active' : '' }}" data-bs-toggle="pill" href="#menu{{ $index+1 }}">
    {{ \Illuminate\Support\Str::limit($category->name, 20, '...') }}
</a>

                        </li>
                    @endforeach
                </ul>
            </div>
<div class="tab-content d-block productListTabNvContent">
		<div class="tab-pane active" id="menu1">
<div class="productListingSliderArea">          
<div class="swiper productListingSwiper">     
<div class="swiper-wrapper">	 
@foreach($products as $product)
    <div class="swiper-slide productListingSliderItem">
        <div class="productBox">
            <div class="productBoxinner">
                <div class="productThumnail">
                    <a href="{{ route('frontend.productdetails', $product->sku ) }}">
                        @if($product->thumbnail)
                            <img src="{{ asset($product->thumbnail) }}" 
                                 width="300" height="300"
                                 style="object-fit:cover; border-radius:6px;">
                        @else
                            <img src="https://via.placeholder.com/300x300?text=No+Image" 
                                 width="300" height="300"
                                 style="object-fit:cover; border-radius:6px;">
                        @endif

                    </a>
                </div>

                <div class="text-center productContent">
				<a href="{{ route('frontend.product.details', $product->id) }}">
                    <h4 class="proTitle">
                        
    {{ \Illuminate\Support\Str::of($product->name)->limit(30, '...') }}


                    </h4>
					</a>
                </div>
				
				<div class="productBoxBtm">
				<div class="product-price-row">
                                <span class="current-price">₹1,000.00</span>                                                       
                            </div>
							<a href="" class="add-tocart-btn1 cart-btn">
                             <svg viewBox="0 0 18 19" fill="none" xmlns="http://www.w3.org/2000/svg" class="add-to-bag-svg">
							  <path fill-rule="evenodd" clip-rule="evenodd" d="M11.2988 3.93606C11.2988 1.90046 9.60755 0.231445 7.5405 0.231445C5.47345 0.231445 3.7823 1.90053 3.7823 3.93593V5.46251H0.859289C0.504293 5.46251 0.23291 5.72707 0.23291 6.07314V15.6398C0.23291 17.3293 1.63178 18.693 3.3648 18.693H8.7463C8.52841 18.3161 8.34935 17.9139 8.21458 17.4919L3.36452 17.492C2.3205 17.492 1.48538 16.6778 1.48538 15.6601V6.70401L3.7823 6.70387V7.76225C3.7823 8.10833 4.05368 8.37289 4.40867 8.37289C4.76367 8.37289 5.03505 8.10833 5.03505 7.76225V6.70387H10.0461V7.76225C10.0461 8.10833 10.3175 8.37289 10.6725 8.37289C11.0275 8.37289 11.2988 8.10833 11.2988 7.76225V6.70387H13.5956V9.48248C13.7547 9.47015 13.9155 9.46387 14.0778 9.46387C14.3388 9.46387 14.5959 9.48011 14.8483 9.51163V6.07314C14.8483 5.72707 14.5769 5.46251 14.2219 5.46251L11.2988 5.46265V3.93606ZM7.5405 1.45243C8.91853 1.45243 10.046 2.57202 10.046 3.93572V5.4623H5.03498V3.93572C5.03498 2.57188 6.16246 1.45243 7.5405 1.45243Z" fill="#fff"></path>
							  <path d="M14.0773 12.5402C13.6997 12.5402 13.3936 12.8462 13.3936 13.2239V14.9334H11.6841C11.3065 14.9334 11.0004 15.2394 11.0004 15.6171C11.0004 15.9947 11.3065 16.3008 11.6841 16.3008H13.3936V18.0103C13.3936 18.3879 13.6997 18.694 14.0773 18.694C14.455 18.694 14.761 18.3879 14.761 18.0103V16.3008H16.4705C16.8482 16.3008 17.1542 15.9947 17.1542 15.6171C17.1542 15.2394 16.8482 14.9334 16.4705 14.9334H14.761V13.2239C14.761 12.8462 14.455 12.5402 14.0773 12.5402Z" fill="#fff"></path>
							</svg>               
							<span class="txt">Add to Cart</span>
                            </a>
				</div>

            </div>
        </div>
    </div>
@endforeach
</div>
<div class="swiper-pagination"></div>    
</div>
<div class="swiper-navigation-area">
	<button class="custom-navigation-button custom-prev">
	  <i class="fa-light fa-angle-left"></i>
	</button>
	<button class="custom-navigation-button custom-next">
	  <i class="fa-light fa-angle-right"></i>
	</button>
	</div>
</div>

</div>
<div class="tab-pane" id="menu2">
</div>
<div class="tab-pane" id="menu3">
</div>
<div class="tab-pane" id="menu4">
</div>
<div class="tab-pane" id="menu5">
</div>
<div class="tab-pane" id="menu6">
</div>
</div>
<div class="mt-30 text-center viewAllBtnArea" data-animation="slideInUp" data-animation-delay="600ms">
    <a href="{{ route('frontend.product.details', ['slug' => 'products']) }}" class="viewAllBtn">View All Products</a>
</div>






        </div>
   


          
        </div>
   
</section>


<section class="pt-50 trendingProductSec">
            <div class="container">                
				<div class="mb-20 text-center section-header" data-animation="slideInUp">
				<h2 class="title" >On-Trends Picks</h2>				
				<p class="mb-0 para">Explore Our Promosing Linne-up</p>
                </div>
				<div class="trendingProductSecinner">
				<div class="trendingProductSliderArea">
<div class="swiper trendsSwiper">
    <div class="swiper-wrapper">
      <a href="#" class="swiper-slide swiper-slide--one">  
		<img data-src="{{ asset('assets/images/uniform-1.png')}}" class="img-fluid lazyload" alt="">	  
      </a>
      <a href="#" class="swiper-slide swiper-slide--two"> 
		<img data-src="{{ asset('assets/images/uniform-2.png')}}" class="img-fluid lazyload" alt="">		  
      </a>
      <a href="#" class="swiper-slide swiper-slide--three">
		<img data-src="{{ asset('assets/images/uniform-3.png')}}" class="img-fluid lazyload" alt="">	
      </a>
      <a href="#" class="swiper-slide swiper-slide--one">  
		<img data-src="{{ asset('assets/images/uniform-1.png')}}" class="img-fluid lazyload" alt="">	  
      </a>
      <a href="#" class="swiper-slide swiper-slide--two"> 
		<img data-src="{{ asset('assets/images/uniform-2.png')}}" class="img-fluid lazyload" alt="">		  
      </a>
      <a href="#" class="swiper-slide swiper-slide--three">
		<img data-src="{{ asset('assets/images/uniform-3.png')}}" class="img-fluid lazyload" alt="">	
      </a>
    </div>	
    <!-- Add Pagination -->
    <div class="swiper-pagination"></div>

</div>
<div class="swiper-navigation-area">
	<button class="custom-navigation-button custom-prev">
	  <i class="fa-light fa-angle-left"></i>
	</button>
	<button class="custom-navigation-button custom-next">
	  <i class="fa-light fa-angle-right"></i>
	</button>
	</div>
</div>
</div>
    </div>
</section>


<section class="pt-50 testimonials-sec home-testimonials-sec">
            <div class="container">                
				 <div class="mb-20 text-center section-header" data-animation="slideInUp">
				<h2 class="title" >Testimonials</h2>				
				<p class="mb-0 para">" Voices of Trust, Stories of Success "</p>
                </div>
                <div class="testimonials-inner" data-animation="slideInUp">
                    <div class="testimonials-wrapper owl-carousel owl-theme">
                        <div class="testimonialsBox">
                            <div class="testimonialsBoxinner">
									<div class="user-thumb">                                            
											<img data-src="{{ asset('assets/images/testimonials-img.jfif')}}" class="img-fluid lazyload" alt="">
                                    </div>
									<div class="testimonials-content">
										<p class="mb-0">“The stitching and quality are excellent. The team paid attention to every detail, and we are very satisfied with the uniforms.”
</p>
									</div>                                   
									<div class="testimonials-Info">										
                                        <h4 class="mb-0">Rajesh Sharma</h4>										                                 
                                    </div>
                            </div>
                        </div>
                        <div class="testimonialsBox">
                            <div class="testimonialsBoxinner">
									<div class="user-thumb">                                            
											<img data-src="{{ asset('assets/images/testimonials-img.jfif')}}" class="img-fluid lazyload" alt="">
                                    </div>
									<div class="testimonials-content">
										<p>“The fit is perfect and the fabric is comfortable. Our team is happy wearing these uniforms all day.”
</p>
									</div>                                   
									<div class="testimonials-Info">										
                                        <h4 class="mb-0">Arjun Mehta</h4>										                                     
                                    </div>
                            </div>
                        </div>
                        <div class="testimonialsBox">
                            <div class="testimonialsBoxinner">
									<div class="user-thumb">                                            
											<img data-src="{{ asset('assets/images/testimonials-img.jfif')}}" class="img-fluid lazyload" alt="">
                                    </div>
									<div class="testimonials-content">
										<p>“Great quality uniforms that hold up well even after regular use. Very satisfied with the service.”
</p>
									</div>                                   
									<div class="testimonials-Info">										
                                        <h4 class="mb-0">Vikas Jain</h4>										                        
                                    </div>
                            </div>
                        </div>
                        <div class="testimonialsBox">
                            <div class="testimonialsBoxinner">
									<div class="user-thumb">                                            
											<img data-src="{{ asset('assets/images/testimonials-img.jfif')}}" class="img-fluid lazyload" alt="">
                                    </div>
									<div class="testimonials-content">
										<p>“Lab coats are comfortable, well-fitted, and of excellent quality. Our staff is very happy with them.”
</p>
									</div>                                   
									<div class="testimonials-Info">										
                                        <h4 class="mb-0"> Dr. Ananya Verma</h4>										                     
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="pt-50 pb-0 choose-sec home-choose-sec">
            <div class="container">               
			<div class="mb-40 text-center section-header">
				<h2 class="mb-0 title" data-animation="slideInUp">
					Why Choose Us ?
				</h2>                
			</div>
                <div class="choose-inner">
                    <div class="choose-wrapper">
                        <div class="choose-box" data-animation="slideInUp">
							<img data-src="{{ asset('assets/images/trust.png')}}" class="img-fluid lazyload" alt="">                             
                            <p>120+ Years of Legacy & Trust</p>
                        </div>
                        <div class="choose-box" data-animation="slideInUp">
							<img data-src="{{ asset('assets/images/fabric.png')}}" class="img-fluid lazyload" alt="">                             
                            <p>Premium Quality Fabric</p>
                        </div>
                        <div class="choose-box" data-animation="slideInUp">
							<img data-src="{{ asset('assets/images/customer-support.png')}}" class="img-fluid lazyload" alt="">  
                            <p>Customization & Branding Support</p>
                        </div>
                        <div class="choose-box" data-animation="slideInUp">
							<img data-src="{{ asset('assets/images/id-card.png')}}" class="img-fluid lazyload" alt="">                              
                            <p>Pan-India Supply & Service<!-- to improve clarity, alignment, and visual appeal--></p>
                        </div>
                        <div class="choose-box" data-animation="slideInUp">
							<img data-src="{{ asset('assets/images/exchange.png')}}" class="img-fluid lazyload" alt="">                             
                            <p>7 Days Exchange*</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
		
{{-- 
        <section class="pt-50 pb-30 handel-sec home-handel-sec">
            <div class="container">
                
				<div class="mb-40 text-center section-header" data-animation="slideInUp">
				<h2 class="mb-0 title" >How We Handle Your Orders Efficiently</h2>	
                </div>
                <div class="handel-inner">
                    <div class="handel-wrapper">
                        <div class="handel-box" data-animation="slideInUp">
                            <div class="handel-box-inner">
                                <div class="handel-top">
									<img data-src="{{ asset('assets/images/OrderIcon1.gif')}}" class="img-fluid lazyload" alt="">                           
                                    
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">1</div>
                                    <span>Submit Your Requirements</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box" data-animation="slideInUp">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon2.gif')}}" class="img-fluid lazyload" alt=""> 
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">2</div>
                                    <span>Get a Free Quote</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box handel-last-box" data-animation="slideInUp">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon3.gif')}}" class="img-fluid lazyload" alt=""> 
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">3</div>
                                    <span>Design Approval</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box mb-0" data-animation="slideInUp">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon4.gif')}}" class="img-fluid lazyload" alt=""> 
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">4</div>
                                    <span>Sample Creation</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box mb-0" data-animation="slideInUp">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon5.gif')}}" class="img-fluid lazyload" alt=""> 
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">5</div>
                                    <span>Production Begins</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box handel-last-box mb-0" data-animation="slideInUp">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon6.gif')}}" class="img-fluid lazyload" alt=""> 
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">6</div>
                                    <span>Fast Delivery</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                    </div>
                </div>
            </div>
</section>--}}
		
		
		<section class="pt-50 homeFaqSec">
        <div class="container">
		<div class="mb-40 text-center section-header" data-animation="slideInUp">
			<h2 class="mb-0 title" >Frequently Asked Questions</h2>	
        </div>
        <div class="homeFaqSecinner">	
		<div id="accordion">
			<div class="card">
			  <div class="card-header">
				<a class="btn" data-bs-toggle="collapse" href="#collapseOne" aria-expanded="true">
					<div class="leftPart">	
					  What types of uniforms does Mafatlal manufacture?
					</div> 
					<div class="rightPart">	
					 <span class="downArrow">
					 <i class="fa-light fa-angle-down"></i>
					 </span>	
					</div> 
				</a>
			  </div>
			  <div id="collapseOne" class="collapse show" data-bs-parent="#accordion">
				<div class="card-body">
				  Mafatlal manufactures a wide range of uniforms including school uniforms, corporate uniforms, industrial uniforms, hospitality uniforms, and healthcare uniforms, designed for comfort, durability, and everyday use.
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-header">
				<a class="collapsed btn" data-bs-toggle="collapse" href="#collapseTwo">
					<div class="leftPart">
					Can uniforms be customized based on specific requirements?
					</div>
					<div class="rightPart">	
					<span class="downArrow">
					 <i class="fa-light fa-angle-down"></i>
					 </span>	
					</div> 
			  </a>
			  </div>
			  <div id="collapseTwo" class="collapse" data-bs-parent="#accordion">
				<div class="card-body">
				  Yes, custom uniforms can be developed based on industry needs, with options for fabric selection, colors, fits, and branding to align with functional and aesthetic requirements.
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-header">
				<a class="collapsed btn" data-bs-toggle="collapse" href="#collapseThree">
					<div class="leftPart">
					What fabric options are used for uniform manufacturing?
					</div>
					<div class="rightPart">	
					 <span class="downArrow">
					 <i class="fa-light fa-angle-down"></i>
					 </span>	
					</div> 
				</a>
			  </div>
			  <div id="collapseThree" class="collapse" data-bs-parent="#accordion">
				<div class="card-body">
				  Uniform fabrics are developed in-house with a focus on performance, comfort, and longevity, using cotton blends and advanced textiles suited for regular wear and professional environments.
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-header">
				<a class="collapsed btn" data-bs-toggle="collapse" href="#collapseFour">
					<div class="leftPart">
					How is quality maintained across uniform manufacturing?
					</div>
					<div class="rightPart">	
					 <span class="downArrow">
					 <i class="fa-light fa-angle-down"></i>
					 </span>	
					</div> 
				</a>
			  </div>
			  <div id="collapseFour" class="collapse" data-bs-parent="#accordion">
				<div class="card-body">
				  Quality is maintained through end-to-end process control, strict quality checks, and certified manufacturing systems that ensure consistency across every batch.
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-header">
				<a class="collapsed btn" data-bs-toggle="collapse" href="#collapseFive">
					<div class="leftPart">
					How do I place an order for custom or bulk uniforms?
					</div>
					<div class="rightPart">	
					 <span class="downArrow">
					 <i class="fa-light fa-angle-down"></i>
					 </span>	
					</div> 
				</a>
			  </div>
			  <div id="collapseFive" class="collapse" data-bs-parent="#accordion">
				<div class="card-body">
				  You can reach out to Mafatlal Uniforms through our Contact Us page. Our team will get in touch with you to guide you through the next steps, including fabric selection, after which the rest of the process will be smoothly handled by us.
				</div>
			  </div>
			</div>
			<div class="card">
			  <div class="card-header">
				<a class="collapsed btn" data-bs-toggle="collapse" href="#collapseSix">
					<div class="leftPart">
					What are Mafatlal fabrics known for?
					</div>
					<div class="rightPart">
					<span class="downArrow">
					 <i class="fa-light fa-angle-down"></i>
					 </span>	
					</div> 
				</a>
			  </div>
			  <div id="collapseSix" class="collapse" data-bs-parent="#accordion">
				<div class="card-body">
				  Mafatlal uniforms are known for their high-quality fabrics that combine comfort, durability, and functionality. The fabrics are designed for all-day wear, offering breathability, ease of movement, and long-lasting performance.  
				 </div>
			  </div>
			</div>
			
			
		  </div>
		
		</div>
        </div>           
        </section>
	
	<section class="pt-50 pb-0 order-step-sec">
            <div class="container">  
			<div class="mb-40 text-center section-header" data-animation="slideInUp">
				<h2 class="mb-0 title" >How We Handle Your Orders Efficiently</h2>	
            </div>
			<div class="order-step-inner"> 
			<div class="row rowBox">     
			<div class="col-md-2 col-sm-12 col-12 columnBox order-step-box"> 
			<div class="order-step-boxinner">
                                <div class="icons-area">
                                    <div class="icons">
                                        <img data-src="{{ asset('assets/images/OrderIcon1.gif')}}" class="img-fluid lazyload" alt="">                           
                                    </div>
                                    <div class="number">
                                        01
                                    </div>
                                </div>
                                <div class="content-area">
                                   <h4 class="mb-0 title">Submit Your<br> Requirements</h4>
                                </div>
            </div>			
			</div>
			<div class="col-md-2 col-sm-12 col-12 columnBox order-step-box"> 
			<div class="order-step-boxinner">
                                <div class="icons-area">
                                    <div class="icons">
                                        <img data-src="{{ asset('assets/images/OrderIcon2.gif')}}" class="img-fluid lazyload" alt="">                       
                                    </div>
                                    <div class="number">
                                        02
                                    </div>
                                </div>
                                <div class="content-area">
                                   <h4 class="mb-0 title">Get a Free<br> Quote</h4>
                                </div>
            </div>			
			</div>
			<div class="col-md-2 col-sm-12 col-12 columnBox order-step-box"> 
			<div class="order-step-boxinner">
                                <div class="icons-area">
                                    <div class="icons">
                                         <img data-src="{{ asset('assets/images/OrderIcon3.gif')}}" class="img-fluid lazyload" alt="">                        
                                    </div>
                                    <div class="number">
                                        03
                                    </div>
                                </div>
                                <div class="content-area">
                                   <h4 class="mb-0 title">Design<br> Approval</h4>
                                </div>
            </div>			
			</div>
			<div class="col-md-2 col-sm-12 col-12 columnBox order-step-box"> 
			<div class="order-step-boxinner">
                                <div class="icons-area">
                                    <div class="icons">
                                        <img data-src="{{ asset('assets/images/OrderIcon4.gif')}}" class="img-fluid lazyload" alt="">                           
                                    </div>
                                    <div class="number">
                                        04
                                    </div>
                                </div>
                                <div class="content-area">
                                   <h4 class="mb-0 title">Sample<br> Creation</h4>
                                </div>
            </div>			
			</div>
			<div class="col-md-2 col-sm-12 col-12 columnBox order-step-box"> 
			<div class="order-step-boxinner">
                                <div class="icons-area">
                                    <div class="icons">
                                        <img data-src="{{ asset('assets/images/OrderIcon5.gif')}}" class="img-fluid lazyload" alt="">                           
                                    </div>
                                    <div class="number">
                                        05
                                    </div>
                                </div>
                                <div class="content-area">
                                   <h4 class="mb-0 title">Production<br> Begins</h4>
                                </div>
            </div>			
			</div>
			<div class="col-md-2 col-sm-12 col-12 columnBox order-step-box"> 
			<div class="order-step-boxinner">
                                <div class="icons-area">
                                    <div class="icons">
                                        <img data-src="{{ asset('assets/images/OrderIcon6.gif')}}" class="img-fluid lazyload" alt="">                           
                                    </div>
                                    <div class="number">
                                        06
                                    </div>
                                </div>
                                <div class="content-area">
                                   <h4 class="mb-0 title">Fast<br> Delivery</h4>
                                </div>
            </div>			
			</div>
			
			</div>	
			</div>			
			</div>
        </section>
		
		<section class="pt-50 pb-80 homeBlogSec">
            <div class="container">                
				<div class="mb-40 text-center section-header" data-animation="slideInUp">
				<h2 class="mb-0 title" >Blogs</h2>	
                </div>
				<div class="homeBlogSecinner" data-animation="slideInUp">
				<div class="homeBlogSlider owl-carousel owl-theme">
				
				<div class="homeBlogBox">
				<div class="homeBlogBoxinner">
				<div class="thumbnail">
				<a href="#">
				<img data-src="{{ asset('assets/images/blog-img-1.jpg')}}" class="img-fluid lazyload" alt=""> 
				</a>				
                </div>
				<div class="content">
				<ul class="d-flex btmList">
				<li class="d-flex align-items-center">
                    <span class="icon mr-6"></span>
                    <span class="txt">27th January 2026</span>
                </li>
				</ul>
				<h4 class="title"><a href="#">Listen to your Media – Standard Post with Audio</a></h4>	
				<p class="mb-0 para">Adipisicing dolor esse voluptate occaecat laborum fugiat adipisicing laboris id cupidatat deserunt exercitation et velit consectetur eiusmod pariatur ullamco enim ut nulla qui minim sunt minim amet non culpa aliqua</p>
                </div>				
                </div>
				</div>
				<div class="homeBlogBox">
				<div class="homeBlogBoxinner">
				<div class="thumbnail">
				<a href="#">
				<img data-src="{{ asset('assets/images/blog-img-2.jpg')}}" class="img-fluid lazyload" alt=""> 
				</a>				
                </div>
				<div class="content">
				<ul class="d-flex btmList">
				<li class="d-flex align-items-center">
                    <span class="icon mr-6"></span>
                    <span class="txt">27th January 2026</span>
                </li>
				</ul>
				<h4 class="title"><a href="#">Listen to your Media – Standard Post with Audio</a></h4>	
				<p class="mb-0 para">Adipisicing dolor esse voluptate occaecat laborum fugiat adipisicing laboris id cupidatat deserunt exercitation et velit consectetur eiusmod pariatur ullamco enim ut nulla qui minim sunt minim amet non culpa aliqua</p>
                </div>				
                </div>
				</div>
				<div class="homeBlogBox">
				<div class="homeBlogBoxinner">
				<div class="thumbnail">
				<a href="#">
				<img data-src="{{ asset('assets/images/blog-img-3.jpg')}}" class="img-fluid lazyload" alt=""> 
				</a>				
                </div>
				<div class="content">
				<ul class="d-flex btmList">
				<li class="d-flex align-items-center">
                    <span class="icon mr-6"></span>
                    <span class="txt">27th January 2026</span>
                </li>
				</ul>
				<h4 class="title"><a href="#">Listen to your Media – Standard Post with Audio</a></h4>	
				<p class="mb-0 para">Adipisicing dolor esse voluptate occaecat laborum fugiat adipisicing laboris id cupidatat deserunt exercitation et velit consectetur eiusmod pariatur ullamco enim ut nulla qui minim sunt minim amet non culpa aliqua</p>
                </div>				
                </div>
				</div>
				<div class="homeBlogBox">
				<div class="homeBlogBoxinner">
				<div class="thumbnail">
				<a href="#">
				<img data-src="{{ asset('assets/images/blog-img-4.jpg')}}" class="img-fluid lazyload" alt=""> 
				</a>				
                </div>
				<div class="content">
				<ul class="d-flex btmList">
				<li class="d-flex align-items-center">
                    <span class="icon mr-6"></span>
                    <span class="txt">27th January 2026</span>
                </li>
				</ul>
				<h4 class="title"><a href="#">Listen to your Media – Standard Post with Audio</a></h4>	
				<p class="mb-0 para">Adipisicing dolor esse voluptate occaecat laborum fugiat adipisicing laboris id cupidatat deserunt exercitation et velit consectetur eiusmod pariatur ullamco enim ut nulla qui minim sunt minim amet non culpa aliqua</p>
                </div>				
                </div>
				</div>
				
				</div>
				</div>
            </div>           
        </section>
		
		
		
        <section class="">
            <div class="container">
                <div class="">	
                </div>
            </div>
        </section>

       


   
		<!-- Scroll Up  -->
		<div class="progressParent" id="back-top">
		  <svg class="backCircle svg-inner" width="100%" height="100%" viewBox="-1 -1 102 102">
			<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
		  </svg>
		 
		</div>

@endsection

