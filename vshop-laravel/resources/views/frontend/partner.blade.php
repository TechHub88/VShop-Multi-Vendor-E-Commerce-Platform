@extends('frontend.layout')

@section('content')

<section class="portfolio-banner-sec service-banner-sec">
    <img src="{{ asset('assets/images/our-service-bg.jpeg') }}" class="img-fluid" alt="">
    <div class="portfolio-banner-overlay service-overlay">
        <div class="container">
            <div class="portfolio-banner-inner">
                <h2>Become a Partner</h2>
              
            </div>
        </div>
    </div>
</section>

<section class="pt-80 service-sec">
    <div class="container">
        <div class="testimonials-header">
            <h2>Why us ?</h2>
            <p>" Why India’s Top Institutions Trust Mafatlal for Uniforms"</p>
        </div>

        <div class="service-inner">
            <div class="service-wrapper">

                <!-- BOX 1 -->
                <div class="service-box">
                    <div class="service-img">
                        <img src="{{ asset('assets/images/OurservicesbdImage1.jpg') }}" class="img-fluid" alt="">
                        <div class="sevice-image-heading">
                            <h3>Built to Endure. Designed to Represent.</h3>
                        </div>
                    </div>
                    <div class="service-box-bottom">
                        <p>Our high-performance uniforms are made...</p>
                    </div>
                </div>

                <!-- BOX 2 -->
                <div class="service-box">
                    <div class="service-img">
                        <img src="{{ asset('assets/images/OurservicesbdImage2.jpg') }}" class="img-fluid" alt="">
                        <div class="sevice-image-heading">
                            <h3>High-Value Uniform Solutions</h3>
                        </div>
                    </div>
                    <div class="service-box-bottom">
                        <p>Engineered for repeated wear...</p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection
