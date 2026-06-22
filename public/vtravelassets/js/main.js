
$(".sidebar-trigger").click(function () {
  if ($(window).width() >= 993) {
    $(".dashboardSidebar").toggleClass("minimiseBar");
    $(".dashboardPagesContent").toggleClass("minimiseBar");
  }
});

$(".dashboardSidebarMenu ul li ul").find("li").closest("ul").parent("li").addClass('dropdownMenuItem');
$(".dashboardSidebarMenu ul li").click(function () {
  $(this).toggleClass("show");
  $('.dashboardSidebarMenu ul li').not($(this)).removeClass('show');
});


$(".sidebar-trigger").click(function () {
  if ($(window).width() >= 993) {
    $(".dashboardSidebar").toggleClass("minimiseBar");
    $(".dashboardPagesContent").toggleClass("minimiseBar");
  }
});

$(".dashboardSidebarMenu ul li ul").find("li").closest("ul").parent("li").addClass('dropdownMenuItem');
$(".dashboardSidebarMenu ul li").click(function () {
  $(this).toggleClass("show");
  $('.dashboardSidebarMenu ul li').not($(this)).removeClass('show');
});

// banner slider

$(function () {
  var owl = $(".banner-wrapper");
  owl.owlCarousel({
    items: 1, 
    loop: true,
    nav: false,
    dots: true,
    // autoplay: true,
    margin: 15,
    //autoplayTimeout: 3000,
  });
});

// menu bar

$(".sidebar-trigger ").click(function () {
  $(".dashboardSidebar").toggleClass("open");
  $(".ddashboardSidebarOverlay").toggleClass("open");
  $("body").css("overflow", "hidden");
 });
 $(".ddashboardSidebarOverlay").click(function () {
  $(".ddashboardSidebarOverlay").removeClass("open");
  $(".dashboardSidebar").removeClass("open");    
  $("body").css("overflow", "auto");
 });
 
$(".menu_close_btn").click(function () {
  $(".navbar-collapse").removeClass("menu-visible");
  $(".navbar-collapse").removeClass("show");
  $(".menu_overlay").removeClass("menu-visible");
  $("body").css("overflow", "auto");
 });

$(".sidebar-trigger").click(function () {
  if ($(window).width() <= 992) {
    $(".dashboardSidebar").toggleClass("open");
    $(".ddashboardSidebarOverlay").toggleClass("open");
    $("body").css("overflow", "hidden");
  }
});
$(".ddashboardSidebarOverlay").click(function () {
  if ($(window).width() <= 992) {
    $(".dashboardSidebar").removeClass("open");
    $(".ddashboardSidebarOverlay").removeClass("open");
    $("body").css("overflow", "auto");
  }
});



$(".web-header .navbar .navbar-nav li ul").find("li").closest("ul").parent("li").addClass('dropdown_menu');
$(".web-header .navbar .navbar-nav li").click(function () {
  $(this).toggleClass("curent");
  $('.web-header .navbar .navbar-nav li').not($(this)).removeClass('curent');
});

// image slider

$('.product-image-wrapper-one').slick({
  slidesToShow: 1,
  slidesToScroll: 1,
  arrows: false,
  fade: true,
  asNavFor: '.product-image-wrapper-two'
});
$('.product-image-wrapper-two').slick({
  slidesToShow: 4,
  slidesToScroll: 1,
  asNavFor: '.product-image-wrapper-one',
  dots: true,
  arrows: false,
  centerMode: true,
  focusOnSelect: true
});

// menu bar

/*$(".sidebar-trigger ").click(function () {
   $(".dashboardSidebar").toggleClass("open");
   $(".ddashboardSidebarOverlay").toggleClass("open");
   $("body").css("overflow", "hidden");
 });
 $(".ddashboardSidebarOverlay").click(function () {
   $(".ddashboardSidebarOverlay").removeClass("open");
   $(".dashboardSidebar").removeClass("open");    
   $("body").css("overflow", "auto");
 });
 
$(".menu_close_btn").click(function () {
   $(".navbar-collapse").removeClass("menu-visible");
   $(".navbar-collapse").removeClass("show");
   $(".menu_overlay").removeClass("menu-visible");
   $("body").css("overflow", "auto");
 });*/

$(".sidebar-trigger").click(function () {
  if ($(window).width() <= 992) {
    $(".dashboardSidebar").toggleClass("open");
    $(".ddashboardSidebarOverlay").toggleClass("open");
    $("body").css("overflow", "hidden");
  }
});
$(".ddashboardSidebarOverlay").click(function () {
  if ($(window).width() <= 992) {
    $(".dashboardSidebar").removeClass("open");
    $(".ddashboardSidebarOverlay").removeClass("open");
    $("body").css("overflow", "auto");
  }
});



$(".web-header .navbar .navbar-nav li ul").find("li").closest("ul").parent("li").addClass('dropdown_menu');
$(".web-header .navbar .navbar-nav li").click(function () {
  $(this).toggleClass("curent");
  $('.web-header .navbar .navbar-nav li').not($(this)).removeClass('curent');
});

// other products slider

$(function () {
  var owl = $(".other-product-wrapper");
  owl.owlCarousel({
    items: 3, 
    loop: true,
    nav: false,
    dots: false,
    autoplay: false,
    margin: 15,
    //autoplayTimeout: 3000,
    responsive: {
      0: {
        items: 1, 
      },
      575: {
        items: 2, 
		slideSpeed: 10000,
      },
      767: {
        items: 3, 
      },
      1024: {
        items: 4 ,
		slideSpeed: 3000,
      }
    }
  });
});

// product quantity 

$('.add').click(function () {		
    var th = $(this).closest('.quantity-sub-sec').find('.count');    	
    th.val(+th.val() + 1);
  });
  $('.sub').click(function () {
    var th = $(this).closest('.quantity-sub-sec').find('.count');    	
          if (th.val() > 1) th.val(+th.val() - 1);
});





// dropdown js

if (document.getElementById('output')) {
    document.getElementById('output').innerHTML = location.search;
}
if ($(".chosen-select").length) {
    $(".chosen-select").chosen();
}











