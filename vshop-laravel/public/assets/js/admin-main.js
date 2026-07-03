

$( ".modal" ).insertAfter( ".adminBodySec" );


/*=============================================*/
/*--------------- [_Accordion] ----------------*/
/*=============================================*/
$('.accordion').find('.accordion-header').on('click', function() {
	// Adds Active Class
	$(this).toggleClass('active');
	// Expand or Collapse This Panel
	$(this).next().slideToggle(300, "swing");
	// Hide The Other Panels
	$('.accordion-body').not($(this).next()).slideUp(300, "swing");
	// Removes Active Class From Other Titles
	$('.accordion-header').not($(this)).removeClass('active');
});  

$( ".modal22" ).insertAfter( ".modal_elementer_inner" );


  
function openPopupById(id) {
  $('#' + id).toggleClass('open');
  $('#overlay_' + id).toggleClass('open');
  $('body').toggleClass('pageBodyhiden');
  //$('body').css('overflow', 'hidden');	
}

function closePopupById(id) {
  $('#' + id).removeClass('open');
  $('#overlay_' + id).removeClass('open');
  $('body').removeClass('pageBodyhiden');
  //$('body').css('overflow', 'auto');
}

// Optional: Click on overlay to close

$('.customeOverlayPopup').on('click', function () {
  var popupId = this.id.replace('overlay_', '');
  closePopupById(popupId);
  //$('body').css('overflow', 'auto');
});
/*$('.customeOverlayPopup').on('click', function () {
  var popupId = this.id.replace('overlay_', '');
  closePopupById(popupId);  
});*/





$(".sideMenuBtn").click(function(){
$(this).toggleClass("open");	
  $(".sideMenubar").toggleClass("open");
  $(".sideMenubarOverlay").toggleClass("open");
  $('.adminPagesContainer').toggleClass("open");
  //$('body').css('overflow', 'hidden');	  
});


$(".sideMenubarOverlay").click(function(){
 $(".sideMenuBtn").removeClass("open"); 	
  $(".sideMenubar").removeClass("open");  
  $(".sideMenubarOverlay").removeClass("open"); 
  $(".adminPagesContainer").removeClass("open"); 
  //$('body').css('overflow', 'auto');
});	

$(".menuBackBtn").click(function(){
	 $(".sideMenuBtn").removeClass("open"); 
  $(".sideMenubar").removeClass("open");  
  $(".sideMenubarOverlay").removeClass("open");  
  $(".adminPagesContainer").removeClass("open"); 
  //$('body').css('overflow', 'auto');  
});



  // toggle passowrd
  
$(".toggle-password").click(function () {
  $(this).toggleClass("fa-eye fa-eye-slash");
  var input = $($(this).attr("toggle"));
  if (input.attr("type") === "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});

