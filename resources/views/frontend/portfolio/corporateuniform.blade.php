@extends('frontend.layout')

@section('content')
    <main>
                        <section class="portfolio-banner-sec">
            <img data-src="{{ asset('assets/images/corporate-bg.png')}}" class="img-fluid ls-is-cached lazyloaded" alt="" src="{{ asset('assets/images/corporate-bg.png')}}">
            <div class="portfolio-banner-overlay">
                <div class="container">
                    <div class="portfolio-banner-inner">
                        <h2>Corporate Uniforms</h2>
                        <p>Whether you're a growing business or an established enterprise, Mafatlal delivers consistent quality and tailored uniform solutions that reflect and reinforce your corporate identity.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="uniformLeftRightSec">
            <div class="container">
                <div class="pt-80 pb-50 uniform-left-right-inner">
                    <div class="uniform-left-right-wrapper">
                        <div class="uniform-left">
                            <div class="uniform-img">
                                <img data-src="{{ asset('assets/images/CorportaeUniformPortfolio.jpg')}}" class="img-fluid ls-is-cached lazyloaded" alt="" src="{{ asset('assets/images/CorportaeUniformPortfolio.jpg')}}">
                            </div>
                        </div>
                        <div class="uniform-right">
                            <p>With over a century of expertise, Mafatlal is a trusted corporate uniform manufacturer in India, serving businesses of all sizes from startups to large enterprises. Our premium customised uniform solutions are designed to meet the evolving needs of modern workplaces across sectors like engineering, hospitality, manufacturing, retail, and services.</p>
                        </div>
                    </div>
                    <div class="uniform-left-right-wrapper direction-change">
                        <div class="uniform-left">
                            <div class="uniform-img">
                                <img data-src="{{ asset('assets/images/CorportaeUniformPortfolio1.jpg')}}" class="img-fluid ls-is-cached lazyloaded" alt="" src="{{ asset('assets/images/CorportaeUniformPortfolio1.jpg')}}">
                            </div>
                        </div>
                        <div class="uniform-right">
                            <p class="mb-3">We offer a curated selection of styles and over 600 fabric combinations using durable blends such as polyester-cotton, polyester-viscose, and denim, chosen for long-term wear, comfort, and easy maintenance.</p>
                            <p>As a leading uniform fabric supplier, we help businesses reinforce their brand image while ensuring employees are dressed for performance, comfort, and confidence.</p>
                        </div>
                    </div>
                    <div class="quote-btn-sec">
                        <button class="add-tocart-btn">Get a Quote</button>
                    </div>
                </div>
            </div>
        </section>

          <section class="pt-80 testimonials-sec">
        <div class="container">
            <div class="testimonials-header">
                <h2>Testimonials</h2>
                <p>" Voices of Trust, Stories of Success "</p>
            </div>
            <div class="testimonials-inner">
                <div class="testimonials-wrapper owl-carousel owl-theme">
                    <!-- Testimonials items: you can loop or static replicate -->
                    <div class="testimonials-box homeFeatureSliderItem">
                        <div class="testimonials-box-inner">
                            <div class="testimonials-top">
                                <div class="testimonials-top-left">
                                    <h4>Zaveri Shah Keerthi</h4>
                                    <div class="testimonials-border"></div>
                                </div>
                                <div class="testimonials-top-right">
                                    <div class="user-img">
                                        <img data-src="{{ asset('assets/images/testimonials-img.jfif') }}" class="img-fluid lazyloaded" alt=""
                                             src="{{ asset('assets/images/testimonials-img.jfif') }}">
                                    </div>
                                </div>
                            </div>
                            <div class="testimonials-center">
                                <p>" Mafatlal Industries have been a trusted supplier throughout our journey. Their products consistently meet & often exceed our retail standards. "</p>
                            </div>
                            <div class="testimonials-bottom">
                                <div class="testimonials-border"></div>
                                <div class="quotation-box">
                                    <img data-src="{{ asset('assets/images/quotation-icon.png') }}" class="img-fluid lazyloaded" alt=""
                                         src="{{ asset('assets/images/quotation-icon.png') }}">
                                </div>
                                <p>Director ‑ Sai Ram Textiles</p>
                            </div>
                        </div>
                    </div>
                    <!-- Add other testimonial items similarly -->
                </div>
            </div>
        </div>
    </section>

    <section class="pt-80 pb-50 choose-sec">
        <div class="container">
            <div class="testimonials-header">
                <h2>Why Choose Us ?</h2>
            </div>
            <div class="choose-inner">
                <div class="choose-wrapper">
                    <div class="choose-box">
                        <img data-src="{{ asset('assets/images/ChooseUsIcon1.svg') }}" class="img-fluid lazyloaded" alt=""
                             src="{{ asset('assets/images/ChooseUsIcon1.svg') }}">
                        <p>100% Fully Tailored Uniform Solutions</p>
                    </div>
                    <div class="choose-box">
                        <img data-src="{{ asset('assets/images/ChooseUsIcon2.svg') }}" class="img-fluid lazyloaded" alt=""
                             src="{{ asset('assets/images/ChooseUsIcon2.svg') }}">
                        <p>100% Fully Tailored Uniform Solutions</p>
                    </div>
                    <div class="choose-box">
                        <img data-src="{{ asset('assets/images/ChooseUsIcon3.svg') }}" class="img-fluid lazyloaded" alt=""
                             src="{{ asset('assets/images/ChooseUsIcon3.svg') }}">
                        <p>100% Fully Tailored Uniform Solutions</p>
                    </div>
                    <div class="choose-box">
                        <img data-src="{{ asset('assets/images/ChooseUsIcon4.svg') }}" class="img-fluid lazyloaded" alt=""
                             src="{{ asset('assets/images/ChooseUsIcon4.svg') }}">
                        <p>100% Fully Tailored Uniform Solutions</p>
                    </div>
                    <div class="choose-box">
                        <img data-src="{{ asset('assets/images/ChooseUsIcon5.svg') }}" class="img-fluid lazyloaded" alt=""
                             src="{{ asset('assets/images/ChooseUsIcon5.svg') }}">
                        <p>100% Fully Tailored Uniform Solutions</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="pt-80 pb-50 handel-sec">
        <div class="container">
            <div class="handel-header">
                <div class="handel-header-top">
                    <h2>How We Handle Your Orders Efficiently</h2>
                </div>
            </div>
            <div class="handel-inner">
                <div class="handel-wrapper">
                    <!-- Repeat boxes for steps 1‑6 -->
                    <div class="handel-box">
                        <div class="handel-box-inner">
                            <div class="handel-top">
                                <img data-src="{{ asset('assets/images/OrderIcon1.gif') }}" class="img-fluid lazyloaded" alt=""
                                     src="{{ asset('assets/images/OrderIcon1.gif') }}">
                            </div>
                            <div class="handel-bottom">
                                <div class="handel-icon">1</div>
                                <span>Submit Your Requirements</span>
                            </div>
                        </div>
                        <div class="handel-arrow">----------></div>
                    </div>
                      <div class="handel-box">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                  <img data-src="{{ asset('assets/images/OrderIcon2.gif') }}" class="img-fluid ls-is-cached lazyloaded" alt="" src="{{ asset('assets/images/OrderIcon2.gif') }}">

                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">2</div>
                                    <span>Get a Free Quote</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box handel-last-box">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon3.gif')}}" class="img-fluid ls-is-cached lazyloaded" alt="" src="{{ asset('assets/images/OrderIcon3.gif')}}">
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">3</div>
                                    <span>Design Approval</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box mb-0">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon4.gif')}}" class="img-fluid ls-is-cached lazyloaded" alt="" src="{{ asset('assets/images/OrderIcon4.gif')}}">
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">4</div>
                                    <span>Sample Creation</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box mb-0">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon5.gif')}}" class="img-fluid ls-is-cached lazyloaded" alt="" src="{{ asset('assets/images/OrderIcon5.gif')}}">
                                </div>
                                <div class="handel-bottom">
                                    <div class="handel-icon">5</div>
                                    <span>Production Begins</span>
                                </div>
                            </div>
                            <div class="handel-arrow">----------></div>
                        </div>
                        <div class="handel-box handel-last-box mb-0">
                            <div class="handel-box-inner">
                                <div class="handel-top">
                                    <img data-src="{{ asset('assets/images/OrderIcon6.gif')}}" class="img-fluid ls-is-cached lazyloaded" alt="" src="{{ asset('assets/images/OrderIcon6.gif')}}">
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

    <section class="touch-sec">
        <div class="container">
            <div class="touch-inner">
                <h2>Get In Touch</h2>
                <p>Spotlight is here to assist you through every channel of communication.</p>
                <a href="#" class="contact-btn">CONTACT OUR EXPERT</a>
            </div>
        </div>
    </section>

    <section class="pt-50 pb-50 faq-sec">
        <div class="container">
            <div class="testimonials-header faq-header">
                <h2>Frequently Asked Questions (FAQs)</h2>
            </div>
            <div class="faq-inner">
                <div class="accordion" id="accordionExample">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                <i class="fa-regular fa-plus plus-icon"></i>
                                <i class="fa-regular fa-minus minus-icon"></i>
                                <span>What is your minimum quantity?</span>
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show"
                             data-bs-parent="#accordionExample">
                            <div class="accordion-body">
                                <p>Minimum quantity for purchasing the customized dresses is minimum 200 units, whereas any regular products …</p>
                            </div>
                        </div>
                    </div>
                    <!-- Add other FAQ items similarly -->
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

       
    </main>


		<div class="progressParent" id="back-top">
		  <svg class="backCircle svg-inner" width="100%" height="100%" viewBox="-1 -1 102 102">
			<path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
		  </svg>
		 
		</div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>  
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js"></script>
	<script src="js/sal.min.js"></script>	
	<script src="js/tilt.min.js"></script>	
    <script src="js/main.js"></script>
    <script>
        // testimonials sec

        $('.testimonials-wrapper').owlCarousel({
            autoplay: false,
            autoplayTimeout: 5000,
            loop: true,
            margin: 24, // keep only one margin value
            nav: false,
            navText: [
                "<i class='fa fa-angle-left'></i>",
                "<i class='fa fa-angle-right'></i>"
            ],
            dots: false,

            // animateOut: 'fadeOut',
            // animateIn: 'fadeIn',

            responsive: {
                0: {
                    items: 1
                },
                360: {
                    items: 1
                },
                480: {
                    items: 1
                },
                576: {
                    items: 1
                },
                767: {
                    items: 2
                },
                992: {
                    items: 2
                },
                1200: {
                    items: 3 // if intentional — keep it
                }
            }
        });

    </script>
    <script>
        const accordionBtns = document.querySelectorAll(".accordion");

            accordionBtns.forEach((accordion) => {
            accordion.onclick = function () {
                this.classList.toggle("is-open");

                let content = this.nextElementSibling;
                console.log(content);

                if (content.style.maxHeight) {
                //this is if the accordion is open
                content.style.maxHeight = null;
                } else {
                //if the accordion is currently closed
                content.style.maxHeight = content.scrollHeight + "px";
                console.log(content.style.maxHeight);
                }
            };
            });
    </script>
@endsection