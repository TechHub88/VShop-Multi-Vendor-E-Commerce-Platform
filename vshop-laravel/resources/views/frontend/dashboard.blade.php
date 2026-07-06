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

		 {{-- <section class="pt-50 pb-30 homeCategorySec">
            <div class="container">
                <div class="mb-40 text-center section-header" data-animation="slideInUp">
				<h2 class="title" >Tailored Uniform Solutions, Trusted for Generations</h2>
				<!--<h3 class="title2"></h3>-->
				<p class="mb-0 para">We offer a full range of customised uniforms designed for functionality and comfort. With 120+ years of experience, we deliver more than just uniforms; we deliver reliability, consistency, and commitment. Our products are designed to meet modern needs while reflecting timeless values, making Mafatlal a preferred name among top fabric manufacturers in India.</p>
                </div>
				<div class="homeCategorySecinner">
				<div class="row rowBox">
				
				<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox homeCategoryBox mb-30" data-animation="slideInUp">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail1.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">School Uniforms</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="100ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail2.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Healthcare Uniforms</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="200ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail3.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Hospitality Uniforms</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="300ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail4.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Corporate Uniforms</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="400ms">
				<a href="#" class="homeCategoryBoxinner">
				<div class="homeCategoryThumnail">
				<img data-src="{{ asset('assets/images/categoryThumnail5.png')}}" class="img-fluid lazyload" alt="">
                </div>
				<div class="text-center homeCategoryOverlayCintent">
				<h3 class="mb-0 title">Oil, Gas & Petroleum</h3>
                </div>
                </a>
                </div>
				<div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox homeCategoryBox mb-30" data-animation="slideInUp" data-animation-delay="500ms">
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

		<section class="pt-50 pb-30 homeCategorySec">
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
            <div class="col-lg-4 col-md-4 col-sm-6 col-12 columnBox homeCategoryBox mb-30" 
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



	
	
		<section class="pt-50 pb-50 partnersSec">
    <div class="container">
        @if($partnerContent)
        <div class="mb-40 text-center section-header">
            <h2 class="mb-20 title" data-animation="slideInUp">{{ $partnerContent->title }}</h2>
            <h3 class="mb-20 title2" data-animation="slideInUp" data-animation-delay="200ms">{{ $partnerContent->tagline }}</h3>
            <p class="mb-0 para" data-animation="slideInUp" data-animation-delay="400ms">{{ $partnerContent->description }}</p>
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

        <section class="pt-50 pb-50 productListingSec homeProductListingSec">
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
