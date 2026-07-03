// doller slider


$( ".modal" ).insertAfter( ".web-footer" );

$(".filter-btn").click(function(){
  $(".productFilterSidebar").toggleClass("open");
  $(".productFilterSidebarOverlay").toggleClass("open");
  //$('body').toggleClass("open");
  $('body').css('overflow', 'hidden');	
  //$(".dashmenu_close_btn").addClass("open");
});


$(".productFilterSidebarOverlay").click(function(){
  $(".productFilterSidebar").removeClass("open");  
  $(".productFilterSidebarOverlay").removeClass("open");
  //$(".dashmenu_close_btn").removeClass("open");
  $('body').css('overflow', 'auto');
});	

/*
$(".dashmenu_close_btn").click(function(){
  $(".productFilterSidebar").removeClass("open");  
  $(".productFilterSidebarOverlay").removeClass("open");  
  $('body').css('overflow', 'auto');
  //$(".dashmenu_close_btn").removeClass("open");
});
*/

// Thumbs
const thumbs = new Swiper(".thumbs-swiper", {
  direction: "vertical",
  slidesPerView: 5,
  spaceBetween: 10,
  watchSlidesProgress: true,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  breakpoints: {
    0: {
      direction: "horizontal",
      slidesPerView: 4,
    },
    768: {
      direction: "horizontal",
      slidesPerView: 5,
    },
    992: {
      direction: "vertical",
      slidesPerView: 5,
    },
  },
});

// Main
const main = new Swiper(".main-swiper", {
  loop: true,
  spaceBetween: 10,
  thumbs: {
    swiper: thumbs,
  },
  navigation: {
    nextEl: ".swiper-button-next-custom",
    prevEl: ".swiper-button-prev-custom",
  },
});

// 🔗 Make main-swiper follow thumbs-swiper navigation
thumbs.on('slideChange', function () {
  const targetIndex = thumbs.realIndex; // index without loop duplicates
  if (main.realIndex !== targetIndex) {
    // use slideToLoop because main has loop: true
    main.slideToLoop(targetIndex);
  }
});

Fancybox.bind('[data-fancybox="gallery"]', {
          // Gallery options
          Thumbs: {
             autoStart: true
          },
          Toolbar: {
             display: ['zoom', 'slideshow', 'fullscreen', 'close']
          }
       });



$(document).ready(function(){
  $('.hpmePanerBannerSlider').owlCarousel({
    loop: true,
    margin: 0,
    nav: false,
    dots: true,
    items: 1,
    autoplay: true,
    autoplayTimeout: 5000,
    smartSpeed: 800,
    animateOut: 'fadeOut',
    animateIn: 'fadeIn',
  dotsContainer: '.custom-dots'
  });
});
var owl = $('.hpmePanerBannerSlider').owlCarousel({
items: 1,
dots: true
});

var dots = $('.hpmePanerBannerSlider .owl-dots').detach();
$('.custom-dots').append(dots);

const accordionItemHeaders = document.querySelectorAll(
".accordion-item-header"
);

accordionItemHeaders.forEach((accordionItemHeader) => {
accordionItemHeader.addEventListener("click", (event) => {
  accordionItemHeader.classList.toggle("active");
  const accordionItemBody = accordionItemHeader.nextElementSibling;
  if (accordionItemHeader.classList.contains("active")) {
    accordionItemBody.style.maxHeight = accordionItemBody.scrollHeight + "px";
  } else {
    accordionItemBody.style.maxHeight = 0;
  }
});
});

$('.homePageBanner').owlCarousel({
          autoplay :true,
           autoplayTimeout: 5000,
          loop: false,
          nav :  false ,
          navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
          dots :  false ,
          margin:0,
     //animateOut: 'fadeOut',
    //animateIn: 'fadeIn',
    
          responsive:{
              0:{
                  items:1,                     
              },
              360:{
                  items:1,                   
              },
				480:{
                  items:1,   
                
              },                
              576:{
                  items:1,					
              },
              767:{
                  items:1,
              },
              992:{
                  items:1,
              },
              1200:{
                  items:1,
              }
              ,
              1360:{
                  items:1,
              }
      }
  });




$('.partnersSlider').owlCarousel({
          autoplay :true,
           autoplayTimeout: 5000,
          loop: true,
          nav :  false ,
          navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
          dots :  false ,
          margin:0,
     //animateOut: 'fadeOut',
    //animateIn: 'fadeIn',
    
          responsive:{
              0:{
                  items:3,                     
              },
              360:{
                  items:3,                   
              },
      480:{
                  items:3,   
                
              },                
              576:{
                  items:4,					
              },
              767:{
                  items:5,
              },
              992:{
                  items:6,
              },
              1200:{
                  items:7,
              }
              ,
              1360:{
                  items:8,
              }
      }
  });


$(document).ready(function(){
const $carousel = $(".homeFeatureSlider");
const $controlsWrapper = $(".owl-controls-wrapper");

$carousel.owlCarousel({
  autoplay: true,
  autoplayTimeout: 5000,
  loop: true,
  nav: true,
  navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
  dots: true,
  margin: 0,
  responsive: {
    0: { items: 1 },
    360: { items: 1 },
    480: { items: 1 },
    576: { items: 1 },
    767: { items: 1 },
    992: { items: 1 },
    1200: { items: 1 },
    1360: { items: 1 }
  },
  // 🔥 Hook into Owl events
  onInitialized: moveOwlControls,
  onRefreshed: moveOwlControls
});

// ✅ Move nav & dots into custom wrapper after Owl initializes
function moveOwlControls() {
  const $nav = $carousel.find('.owl-nav');
  const $dots = $carousel.find('.owl-dots');

  // Avoid duplicates
  $controlsWrapper.empty();

  // Append nav and dots to custom wrapper
  $controlsWrapper.append($nav).append($dots);
}
});





$('.productListingSlider').owlCarousel({
          autoplay :true,
          autoplayTimeout: 5000,
          loop: true,
          nav :  false ,
          navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
          dots :  false ,
          margin:0,
     //animateOut: 'fadeOut',
    //animateIn: 'fadeIn',
    
          responsive:{
              0:{
                  items:2,                     
              },
              360:{
                  items:2,                   
              },
      480:{
                  items:2,   
                
              },                
              576:{
                  items:2,					
              },
              767:{
                  items:3,
              },
              992:{
                  items:3,
              },
              1200:{
                  items:4,
              }
              ,
              1360:{
                  items:4,
              }
      }
  });


$('.similarProductListingSlider').owlCarousel({
          autoplay :true,
          autoplayTimeout: 5000,
          loop: false,
          nav :  false ,
          navText: ["<i class='fa fa-angle-left'></i>", "<i class='fa fa-angle-right'></i>"],
          dots :  false ,
          margin:10,
     //animateOut: 'fadeOut',
    //animateIn: 'fadeIn',
    
          responsive:{
              0:{
                  items:2,                     
              },
              360:{
                  items:2,                   
              },
      480:{
                  items:2,   
                
              },                
              576:{
                  items:2,					
              },
              767:{
                  items:3,
              },
              992:{
                  items:3,
              },
              1200:{
                  items:4,
              }
              ,
              1360:{
                  items:4,
              }
      }
  });

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
		
		
		
// testimonials sec

        $('.homeBlogSlider').owlCarousel({
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
                    items: 3
                },
                1200: {
                    items: 4 // if intentional — keep it
                }
            }
        });


		
		
var swiper = new Swiper(".trendsSwiper", {
  effect: "coverflow",
  grabCursor: false,
  centeredSlides: true,
  loop: true,

  coverflowEffect: {
    rotate: 0,
    stretch: 0,
    depth: 290,
    modifier: 2,
    slideShadows: true,
  },
	/*mousewheel: {
    thresholdDelta: 70 },*/	
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  }, 
  navigation: {
    nextEl: ".custom-next",
    prevEl: ".custom-prev",
  },

  breakpoints: {
    0: {
      slidesPerView: 1,
    },
	 480: {
      slidesPerView: 1,
    },
    640: {
      slidesPerView: 2,
    },
    768: {
      slidesPerView: 2, // tablet override (intentional)
    },
    1024: {
      slidesPerView: 2,
    },
    1560: {
      slidesPerView: 2,
    },
	3560: {
      slidesPerView: 2,
    },
  },
});		


var swiper = new Swiper(".homeCategorySwiper", {
  slidesPerView: 4,
  spaceBetween: 20,
  loop: true,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  navigation: {
    nextEl: ".custom-next",
    prevEl: ".custom-prev",
  },
  breakpoints:{
    0:{slidesPerView:1},
    576:{slidesPerView:2},
    992:{slidesPerView:3},
    1200:{slidesPerView:4}
  }
});

var swiper = new Swiper(".productListingSwiper", {
  slidesPerView: 4,
  spaceBetween: 20,
  loop: true,
  pagination: {
    el: ".swiper-pagination",
    clickable: true,
  },
  navigation: {
    nextEl: ".custom-next",
    prevEl: ".custom-prev",
  },
  breakpoints:{
    0:{slidesPerView:1},
    576:{slidesPerView:2},
    992:{slidesPerView:3},
    1200:{slidesPerView:4}
  }
});

var SwiperTop = new Swiper('.marquee-right', {
  spaceBetween: 0,
  centeredSlides: true,
  speed: 8000,
  loop: true,
  slidesPerView: 'auto',

  autoplay: {
    delay: 0,                 // ⚠️ must be 0 for marquee
    disableOnInteraction: false,
  },

  allowTouchMove: false,      // disable drag for smooth marquee
});

// menubar

$(".navbar-toggler").click(function () {
$(".navbar-collapse").toggleClass("menu-visible");
$(".menu_overlay").toggleClass("menu-visible");
$("body").css("overflow", "hidden");
});

$(".menu_close_btn").click(function () {
$(".navbar-collapse").removeClass("menu-visible");
$(".navbar-collapse").removeClass("show");
$(".menu_overlay").removeClass("menu-visible");
$("body").css("overflow", "auto");
});

$(".menu_overlay").click(function () {
$(".menu_overlay").removeClass("menu-visible");
$(".navbar-collapse").removeClass("show");
$(".navbar-collapse").removeClass("menu-visible");
$("body").css("overflow", "auto");
});

$( ".web-header .navbar .navbar-nav li ul" ).find( "li" ) .closest("ul") .parent("li") .addClass( 'dropdown_menu' );
$(".web-header .navbar .navbar-nav li").click(function(){
$(this).toggleClass("curent");
$('.web-header .navbar .navbar-nav li').not($(this)).removeClass('curent');
});


/*-----------------------------------
  Back To Top
-----------------------------------*/
(function () {
  var progressPath = document.querySelector(".progressParent path");
  var pathLength = progressPath?.getTotalLength();
  if (pathLength) {
    progressPath.style.transition = progressPath.style.WebkitTransition = "none";
    progressPath.style.strokeDasharray = pathLength + " " + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = "stroke-dashoffset 10ms linear";
    var updateProgress = function () {
      var scroll = $(window).scrollTop();
      var height = $(document).height() - $(window).height();
      var progress = pathLength - (scroll * pathLength) / height;
      progressPath.style.strokeDashoffset = progress;
    };
    updateProgress();
    $(window).scroll(updateProgress);
    var offset = 50;
    var duration = 550;
    jQuery(window).on("scroll", function () {
      if (jQuery(this).scrollTop() > offset) {
        jQuery(".progressParent").addClass("rn-backto-top-active");
      } else {
        jQuery(".progressParent").removeClass("rn-backto-top-active");
      }
    });
    jQuery(".progressParent").on("click", function (event) {
      event.preventDefault();
      jQuery("html, body").animate({ scrollTop: 0 }, duration);
      return false;
    });
  }
})();


/*-----------------------------------
  Pre Loader Effect
-----------------------------------*/

$(window).on('load', function() {

  "use strict";

  /*----------------------------------------------------*/
  /*	Preloader
  /*----------------------------------------------------*/
  
  var preloader = $('#loading'),
    loader = preloader.find('#loading-center');
    loader.fadeOut();
    preloader.delay(400).fadeOut('slow');		
      
});




/* -----------------------------------------------------------------------------
File:           JS Core
Version:        1.0
Last change:    00/00/00 
-------------------------------------------------------------------------------- */
(function() {

"use strict";

var OnlinePayment = {
  init: function() {
    this.Basic.init();  
  },

  Basic: {
    init: function() {
      
      this.BackgroundImage();	
    },
    
    BackgroundImage: function (){
      $('[data-background]').each(function() {
        $(this).css('background-image', 'url('+ $(this).attr('data-background') + ')');
      });
    },			

  }
}
jQuery(document).ready(function (){
  OnlinePayment.init();
});

})();


// product details slider

$('.product-details-one-wrapper').slick({
slidesToShow: 1,
slidesToScroll: 1,
arrows: false,
fade: true,
asNavFor: '.product-details-two-wrapper'
});
$('.product-details-two-wrapper').slick({
slidesToShow: 4,
slidesToScroll: 1,
arrows: false,
asNavFor: '.product-details-one-wrapper',
dots: false,
focusOnSelect: true
});

// product quantity 

$('.add').click(function () {		
  var th = $(this).closest('.product-quantity').find('.count');    	
  th.val(+th.val() + 1);
});
$('.sub').click(function () {
  var th = $(this).closest('.product-quantity').find('.count');    	
        if (th.val() > 1) th.val(+th.val() - 1);
});

// Only initialize select2 if the elements exist and select2 is loaded
if (typeof $.fn.select2 !== 'undefined') {
    if ($("#country-select").length) {
        $("#country-select").select2({});
    }
    
    if ($("#district-select").length) {
        $("#district-select").select2({});
    }
}




