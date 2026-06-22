@extends('frontend.layout')

@section('content')

	


	<section class="hpmePanerBanner">
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

<section class="pt-50 partnersSec">
    <div class="container">
        @if($partnerContent)
        <div class="mb-40 text-center section-header">
            <h2 class="mb-20 title" data-animation="slideInUp">{{ $partnerContent->title }}</h2>
            <h3 class="mb-0 title2" data-animation="slideInUp" data-animation-delay="200ms">{{ $partnerContent->tagline }}</h3>            
        </div>
        @endif

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
		<p class="mb-0 text-center btmpara" data-animation="slideInUp">{{ $partnerContent->description }}</p>
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
    <div class="row rowBox">
        @foreach($categories as $index => $category)
            <div class="col-lg-4 col-md-4 col-sm-6 col-6 columnBox homeCategoryBox mb-30" 
                 data-animation="slideInUp" 
                 data-animation-delay="{{ $index * 100 }}ms">
                 
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


                    <div class="text-center homeCategoryOverlayCintent">
                        <h3 class="mb-0 title">{{ $category->name }}</h3>
                    </div>
                </a>
            </div>
        @endforeach
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
                <h2 class="title">About Us</h2>               
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
        <div class="mb-40 text-center section-header">
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

          <section class="pt-0 pb-0 productListingSec homeProductListingSec">
    <div class="container">
        <div class="mb-40 text-center section-header">
                    
        </div>
       
     <div class="productListingSlider owl-carousel owl-theme" data-animation="slideInUp" data-animation-delay="400ms">
@foreach($products as $product)
    <div class="productListingSliderItem">
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
                    <h4 class="proTitle">
                        <a href="{{ route('frontend.product.details', $product->id) }}">
    {{ \Illuminate\Support\Str::of($product->name)->limit(30, '...') }}
</a>

                    </h4>
                </div>

            </div>
        </div>
    </div>
@endforeach
</div>


<div class="mt-30 text-center viewAllBtnArea" data-animation="slideInUp" data-animation-delay="600ms">
    <a href="{{ route('frontend.product.details', ['slug' => 'products']) }}" class="viewAllBtn">View All Products</a>
</div>






        </div>
    </div>
</section>


          
        </div>
    </div>
</section>


<section class="pt-50 trendingProductSec">
            <div class="container">                
				 <div class="mb-20 text-center section-header" data-animation="slideInUp">
				<h2 class="title" >Testimonials</h2>				
				<p class="mb-0 para">" Voices of Trust, Stories of Success "</p>
                </div>
				<div class="trendingProductSecinner">
<div class="swiper">
    <div class="swiper-wrapper">
      <div class="swiper-slide swiper-slide--one">
        <div>
          <h2>Jellyfish</h2>
          <p>Jellyfish and sea jellies are the informal common names given to the medusa-phase of certain gelatinous members of the subphylum Medusozoa, a major part of the phylum Cnidaria.</p>
          <a href="https://en.wikipedia.org/wiki/Jellyfish" target="_blank">explore</a>
        </div>
      </div>
      <div class="swiper-slide swiper-slide--two">
        <div>
          <h2>Seahorse</h2>
          <p>
            Seahorses are mainly found in shallow tropical and temperate salt water throughout the world. They live in sheltered areas such as seagrass beds, estuaries, coral reefs, and mangroves. Four species are found in Pacific waters from North America to South America.
          </p>
          <a href="https://en.wikipedia.org/wiki/Seahorse" target="_blank">explore</a>
        </div>
      </div>

      <div class="swiper-slide swiper-slide--three">

        <div>
          <h2>octopus</h2>
          <p>
            Octopuses inhabit various regions of the ocean, including coral reefs, pelagic waters, and the seabed; some live in the intertidal zone and others at abyssal depths. Most species grow quickly, mature early, and are short-lived.
          </p>
          <a href="https://en.wikipedia.org/wiki/Octopus" target="_blank">explore</a>
        </div>
      </div>

      <div class="swiper-slide swiper-slide--four">

        <div>
          <h2>Shark</h2>
          <p>
            Sharks are a group of elasmobranch fish characterized by a cartilaginous skeleton, five to seven gill slits on the sides of the head, and pectoral fins that are not fused to the head.
          </p>
          <a href="https://en.wikipedia.org/wiki/Shark" target="_blank">explore</a>
        </div>
      </div>

      <div class="swiper-slide swiper-slide--five">

        <div>
          <h2>Dolphin</h2>
          <p>
            Dolphins are widespread. Most species prefer the warm waters of the tropic zones, but some, such as the right whale dolphin, prefer colder climates. Dolphins feed largely on fish and squid, but a few, such as the orca, feed on large mammals such as seals.
          </p>
          <a href="https://en.wikipedia.org/wiki/Dolphin" target="_blank">explore</a>
        </div>
      </div>
    </div>
    <!-- Add Pagination -->
    <div class="swiper-pagination"></div>

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

        <section class="pt-50 pb-80 handel-sec home-handel-sec">
            <div class="container">
                <div class="handel-header" data-animation="slideInUp">
                    <div class="handel-header-top">
                        <h2>How We Handle Your Orders Efficiently</h2>
                    </div>
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

<script id="rendered-js" >

  </script>