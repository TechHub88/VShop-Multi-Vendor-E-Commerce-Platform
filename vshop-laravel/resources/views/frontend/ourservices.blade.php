@extends('frontend.layout')

<style>
    .ourKeyServiceBoxinner {
        width: 100%;
        height: 100%;
        border: 1px solid rgba(34, 31, 32, .1);
        border-radius: 22px;
        box-shadow: 0 0 14.604644775390625px #221f2014;
        position: relative;
        background: #fff;
        padding: 18px;
        text-align: center;
        transition: all .3s ease-in-out;
    }
    .ourKeyServiceBoxinner:hover {
        transform: translateY(-8px);
        border-color: #ed3237;
    }
    .ourKeyServiceBoxinner .content .title {
        font-size: 17px;
        margin-top: 15px;
    }
    .solution-box {
        width: 100%;
        height: 100%;
        border: 1px solid rgba(34, 31, 32, .1);
        border-radius: 22px;
        box-shadow: 0 0 14.604644775390625px #221f2014;
        position: relative;
        background: #fff;
        padding: 24px;
        text-align: left;
        transition: all .3s ease-in-out;
    }
    .solution-box h3 {
        font-size: 20px;
        line-height: 28px;
        font-weight: 600;
        margin-bottom: 12px;
    }
    .solution-box p {
        font-size: 16px;
        margin: 0;
    }
    .solution-box svg {
        margin-bottom: 15px;
    }
    .solution-box-outer {
        margin-bottom: 24px;
    }
    
    @media(max-width: 991px) {
        .ourKeyServiceBox {
            margin-bottom: 20px;
        }
    }
    
    @media(max-width: 767px) {
        .solution-sec {
            padding-top: 0 !important;
        }
    }
</style>

@section('content')

<section class="portfolio-banner-sec service-banner-sec">
    <img src="{{ asset('assets/images/our-service-bg.jpeg') }}" class="img-fluid" alt="">
    <div class="portfolio-banner-overlay service-overlay">
        <div class="container">
            <div class="portfolio-banner-inner">
                <h1>Why Choose Mafatlal Uniforms Services</h1>
                <p>With over 120 years of expertise, Mafatlal delivers refined uniform services and custom uniforms for corporate, industrial, school, and hospitality sectors, ensuring superior quality, compliance, and a global delivery footprint.
</p>
            </div>
        </div>
    </div>
</section>



<section class="pt-80 pb-80 ourKeyServiceSec">
    <div class="container">
			<div class="mb-40 text-center section-header">
				<h2 class="mb-0 title" >Our Key Uniform Services</h2>	
            </div>
        <div class="ourKeyServiceSecinner">
		<div class="row rowBox">
		
		<div class="col-lg-2 col-md-3 col-sm-4 col-6 columnBox ourKeyServiceBox">
		<div class="ourKeyServiceBoxinner">
		<div class="icon">
		    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50" x="0" y="0" viewBox="0 0 661.333 661" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M10 248.832h55.477a148.72 148.72 0 0 0 14.085 33.883l-39.265 39.25a10.002 10.002 0 0 0 0 14.148l42.422 42.434c3.906 3.898 10.234 3.898 14.14 0l39.27-39.274A148.62 148.62 0 0 0 170 353.355v55.477c0 5.523 4.477 10 10 10h60c5.523 0 10-4.477 10-10v-55.477a148.62 148.62 0 0 0 33.871-14.082l39.27 39.274c3.968 3.75 10.175 3.75 14.14 0l42.43-42.434a10.002 10.002 0 0 0 0-14.148l-39.274-39.25a148.72 148.72 0 0 0 14.086-33.883H410c5.523 0 10-4.477 10-10v-60c0-5.52-4.477-10-10-10h-55.477a148.764 148.764 0 0 0-14.086-33.879l39.266-39.25a10.002 10.002 0 0 0 0-14.148L337.281 39.12c-3.906-3.902-10.234-3.902-14.14 0l-39.27 39.274A149.046 149.046 0 0 0 250 64.312V8.832c0-5.52-4.477-10-10-10h-60c-5.523 0-10 4.48-10 10v55.48a149.046 149.046 0 0 0-33.871 14.083L96.859 39.12c-3.968-3.75-10.175-3.75-14.14 0l-42.43 42.434a10.002 10.002 0 0 0 0 14.148l39.273 39.25a148.764 148.764 0 0 0-14.085 33.879H10c-5.523 0-10 4.48-10 10v60c0 5.523 4.477 10 10 10zm10-60h53.3c4.7 0 8.766-3.27 9.77-7.86a128.657 128.657 0 0 1 17.52-42.14 10.002 10.002 0 0 0-1.352-12.469L61.5 88.625l28.29-28.293 37.73 37.738A10.003 10.003 0 0 0 140 99.426a128.86 128.86 0 0 1 42.14-17.52 10.01 10.01 0 0 0 7.86-9.773V18.832h40v53.3c0 4.7 3.273 8.766 7.86 9.774A128.86 128.86 0 0 1 280 99.426a10.003 10.003 0 0 0 12.48-1.356l37.73-37.738 28.282 28.293-37.73 37.727a10.003 10.003 0 0 0-1.352 12.48 128.623 128.623 0 0 1 17.508 42.148 10.008 10.008 0 0 0 9.77 7.864H400v40h-53.313c-4.695 0-8.761 3.27-9.769 7.86a128.501 128.501 0 0 1-17.508 42.128 10.007 10.007 0 0 0 1.352 12.473l37.738 37.73-28.29 28.3-37.73-37.741a10.007 10.007 0 0 0-12.48-1.352 128.561 128.561 0 0 1-42.129 17.52c-4.594 1-7.871 5.07-7.871 9.77v53.3h-40v-53.3c0-4.7-3.277-8.77-7.871-9.77A128.561 128.561 0 0 1 140 318.242a10.007 10.007 0 0 0-12.48 1.352l-37.73 37.742-28.282-28.293 37.73-37.73a10.002 10.002 0 0 0 1.352-12.481 128.697 128.697 0 0 1-17.52-42.148 9.995 9.995 0 0 0-9.77-7.852H20zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M210 288.832c44.184 0 80-35.812 80-80 0-44.184-35.816-80-80-80s-80 35.816-80 80c.047 44.16 35.84 79.953 80 80zm0-140c33.133 0 60 26.867 60 60 0 33.137-26.867 60-60 60s-60-26.863-60-60c.035-33.125 26.875-59.965 60-60zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M210 318.832c60.75 0 110-49.246 110-110 0-60.75-49.25-110-110-110s-110 49.25-110 110c.074 60.723 49.277 109.93 110 110zm0-200c49.707 0 90 40.293 90 90s-40.293 90-90 90-90-40.293-90-90c.055-49.68 40.316-89.945 90-90zM570 458.832v-80c0-5.52-4.477-10-10-10h-20v-80c0-5.52-4.477-10-10-10h-90c-5.523 0-10 4.48-10 10v80h-50c-5.523 0-10 4.48-10 10v80H50c-27.602.035-49.965 22.398-50 50v20c.035 27.605 22.398 49.969 50 50h520c27.602-.031 49.965-22.395 50-50v-20c-.035-27.602-22.398-49.965-50-50zm-20 0h-70v-70h70zm-100-160h70v70h-70zm-60 90h70v70h-70zm210 140c0 16.57-13.434 30-30 30H50c-16.566 0-30-13.43-30-30v-20c0-16.566 13.434-30 30-30h520c16.566 0 30 13.434 30 30zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M310 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zM210 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zM410 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zM110 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zM510 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
        </div>
		<div class="content">
		<h4 class="mb-0 title">Bulk Manufacturing</h4>
        </div>
        </div>
        </div>
		<div class="col-lg-2 col-md-3 col-sm-4 col-6 columnBox ourKeyServiceBox">
		<div class="ourKeyServiceBoxinner">
		<div class="icon">
		    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50" x="0" y="0" viewBox="0 0 682.667 682.667" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#000000" opacity="1" data-original="#000000"></path></clipPath><clipPath id="b" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#000000" opacity="1" data-original="#000000"></path></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0h381v32.33h-497V0h81" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(123.5 7.5)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path></g><path d="M45.886 39.825h80.813v123.24H45.886ZM159.024 39.825h80.813v171.728h-80.813zM272.163 39.825h80.813v232.337h-80.813z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.33333 0 0 -1.33333 0 682.667)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v-124.51h-80.81v272.74H0V35" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.33333 0 0 -1.33333 621.48 463.547)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><g clip-path="url(#b)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0h23.98l95.21 103.86a16.15 16.15 0 0 0 11.91 5.24h67.05L283 193.95h-41.79v32.33h80.81c8.93 0 16.16-7.24 16.16-16.16V129.3h-32.32v41.8l-89.59-89.59a16.159 16.159 0 0 0-11.43-4.73h-66.63L43.01-27.08a16.171 16.171 0 0 0-11.92-5.24H-83.05V0H-35" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(127.93 278.22)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path></g></g></svg>
        </div>
		<div class="content">
		<h4 class="mb-0 title">Custom Development</h4>
        </div>
        </div>
        </div>
		<div class="col-lg-2 col-md-3 col-sm-4 col-6 columnBox ourKeyServiceBox">
		<div class="ourKeyServiceBoxinner">
		<div class="icon">
		    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50" x="0" y="0" viewBox="0 0 16.933 16.933" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g stroke-linejoin="round"><path d="M2.238 7.424.96 7.865a.643.643 0 0 0-.43.602c0 .247.157.507.43.601l5.289 1.825a6.81 6.81 0 0 0 4.438 0l5.289-1.825a.643.643 0 0 0 .43-.601.643.643 0 0 0-.43-.602l-1.28-.441-.174.5 1.282.441c.061.021.072.055.072.102 0 .047-.01.08-.072.101l-5.291 1.825a6.277 6.277 0 0 1-4.092 0L1.13 8.568c-.06-.02-.071-.054-.071-.101 0-.047.01-.08.072-.102l1.281-.441z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M2.238 9.01.96 9.45a.646.646 0 0 0-.43.604c0 .247.157.507.43.601l5.289 1.824a6.81 6.81 0 0 0 4.438 0l5.289-1.824a.643.643 0 0 0 .43-.601.646.646 0 0 0-.43-.604l-1.28-.441-.174.5 1.282.441c.061.021.072.056.072.104 0 .047-.01.08-.072.101l-5.291 1.824a6.277 6.277 0 0 1-4.092 0l-5.29-1.824c-.06-.02-.071-.054-.071-.101 0-.048.01-.083.072-.104l1.281-.441z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M8.463 4.082c-.749 0-1.5.123-2.218.371L.957 6.275a.645.645 0 0 0-.429.604c0 .248.156.51.429.603l5.288 1.823a6.806 6.806 0 0 0 4.436 0l5.287-1.823a.647.647 0 0 0 .43-.603.647.647 0 0 0-.43-.604l-5.287-1.822a6.805 6.805 0 0 0-2.218-.371zm0 .53c.692 0 1.382.112 2.045.341l5.289 1.824c.062.022.072.055.072.102 0 .047-.01.08-.072.101l-5.289 1.825a6.275 6.275 0 0 1-4.091 0L1.13 6.98c-.062-.02-.072-.054-.072-.101 0-.047.01-.08.072-.102l5.287-1.824a6.28 6.28 0 0 1 2.046-.342z" fill="#000000" opacity="1" data-original="#000000"></path><g stroke-linecap="round"><path d="M11.906 2.117c0 .928-.369 1.536-.613 2.25s-.333 1.498.389 2.653a.265.265 0 1 0 .449-.282c-.605-.967-.557-1.56-.338-2.199s.643-1.354.643-2.422h-.53z" fill="#000000" opacity="1" data-original="#000000"></path><path d="m11.984 1.93-.795.793a.265.265 0 0 0 0 .375.265.265 0 0 0 .375 0l.608-.606.605.606a.265.265 0 0 0 .375 0 .265.265 0 0 0 0-.375l-.795-.793a.265.265 0 0 0-.373 0zM8.201 1.588c0 .928-.369 1.536-.613 2.25s-.326 1.506.39 2.652a.265.265 0 0 0 .448-.281c-.609-.974-.557-1.56-.338-2.2s.642-1.353.642-2.421h-.529z" fill="#000000" opacity="1" data-original="#000000"></path><path d="m8.28 1.4-.794.793a.265.265 0 0 0 0 .375.265.265 0 0 0 .373 0l.608-.605.607.605a.265.265 0 0 0 .373 0 .265.265 0 0 0 0-.375L8.654 1.4a.265.265 0 0 0-.375 0zM4.498 2.117c0 .928-.37 1.536-.615 2.25s-.327 1.505.39 2.653a.265.265 0 1 0 .45-.282c-.603-.964-.557-1.56-.338-2.199.218-.639.642-1.354.642-2.422h-.529z" fill="#000000" opacity="1" data-original="#000000"></path><path d="m4.576 1.93-.795.793a.265.265 0 0 0 0 .375.265.265 0 0 0 .375 0l.606-.606.607.606a.265.265 0 0 0 .375 0 .265.265 0 0 0 0-.375L4.95 1.93a.265.265 0 0 0-.373 0zM11.55 11.92c.181.448.34.827.362 1.238.022.411-.08.874-.482 1.518a.265.265 0 0 0 .447.281c.443-.709.592-1.3.564-1.826-.028-.526-.22-.969-.398-1.41l-.492.199zM7.938 12.67c.156.393.274.74.273 1.123-.001.384-.118.822-.486 1.412a.265.265 0 1 0 .449.281c.405-.649.565-1.198.566-1.691.002-.494-.15-.915-.312-1.32 0 0-.49.2-.49.195zM4.025 11.615c.2.532.412.962.467 1.41.055.449-.024.936-.47 1.65a.265.265 0 0 0 .447.282c.491-.786.618-1.43.549-1.996-.07-.566-.312-1.034-.498-1.531l-.495.185z" fill="#000000" opacity="1" data-original="#000000"></path></g></g></g></svg>
        </div>
		<div class="content">
		<h4 class="mb-0 title">Complete Fabric Solutions</h4>
        </div>
        </div>
        </div>
		<div class="col-lg-2 col-md-3 col-sm-4 col-6 columnBox ourKeyServiceBox">
		<div class="ourKeyServiceBoxinner">
		<div class="icon">
		    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M481.005 434.003a7.999 7.999 0 0 1-7.334 8.614l-50.409 4.045a7.999 7.999 0 0 1-8.613-8.615l4.044-50.408a8 8 0 1 1 15.948 1.279l-2.274 28.354C472.803 373.112 495 316.093 495 256c0-48.985-14.728-96.077-42.591-136.185a8 8 0 1 1 13.141-9.129C495.283 153.486 511 203.735 511 256c0 64.54-23.999 125.754-67.686 173.001l29.078-2.333c4.409-.359 8.26 2.932 8.613 7.335zM17 256c0-60.094 22.197-117.111 62.634-161.272l-2.275 28.354a7.999 7.999 0 1 0 15.947 1.279l4.044-50.408a8.002 8.002 0 0 0-2.317-6.297 8.038 8.038 0 0 0-6.296-2.317l-50.409 4.045a8 8 0 0 0-7.334 8.614c.354 4.404 4.218 7.694 8.614 7.335L68.687 83C25 130.246 1 191.459 1 256c0 52.266 15.717 102.515 45.451 145.315a7.99 7.99 0 0 0 6.577 3.437 8.002 8.002 0 0 0 6.564-12.566C31.728 352.079 17 304.986 17 256zM119.814 59.592C159.922 31.728 207.014 17 256 17c60.093 0 117.111 22.198 161.273 62.634l-28.354-2.275c-4.405-.357-8.261 2.931-8.614 7.335a7.999 7.999 0 0 0 7.334 8.614l50.408 4.044a7.999 7.999 0 0 0 8.613-8.615l-4.045-50.408a8 8 0 1 0-15.948 1.279L429 68.686C381.753 25 320.54 1 256 1c-52.265 0-102.514 15.717-145.314 45.451a8.001 8.001 0 0 0 9.128 13.141zm272.371 392.817C352.079 480.272 304.986 495 256 495c-60.094 0-117.112-22.198-161.273-62.633l28.355 2.275c4.399.362 8.261-2.931 8.614-7.335a7.999 7.999 0 0 0-7.334-8.614l-50.408-4.044a7.998 7.998 0 0 0-8.613 8.614l4.045 50.408A8 8 0 0 0 78 481.006a8 8 0 0 0 7.334-8.614l-2.333-29.077C130.246 487.001 191.46 511 256 511c52.266 0 102.514-15.717 145.314-45.45a8 8 0 0 0-9.129-13.141zm27.31-196.408c0 90.151-73.344 163.495-163.495 163.495S92.504 346.152 92.504 256.001 165.848 92.506 256 92.506s163.495 73.344 163.495 163.495zm-16 0c0-81.329-66.166-147.495-147.495-147.495s-147.495 66.166-147.495 147.495S174.67 403.496 256 403.496s147.495-66.166 147.495-147.495zm-53.047 78.3a8 8 0 0 1-8 8H169.551a8 8 0 0 1-8-8c0-40.836 26.052-75.698 62.408-88.849-13.728-9.953-22.682-26.107-22.682-44.322 0-30.174 24.548-54.723 54.723-54.723s54.723 24.549 54.723 54.723c0 18.215-8.954 34.369-22.682 44.322 36.355 13.151 62.407 48.013 62.407 88.849zm-94.474-94.448h.052c21.34-.014 38.698-17.379 38.698-38.723 0-21.352-17.371-38.723-38.723-38.723s-38.723 17.371-38.723 38.723c-.001 21.344 17.357 38.709 38.696 38.723zm78.069 86.448c-4.02-39.505-37.477-70.438-78.024-70.448h-.04c-40.547.01-74.004 30.942-78.024 70.448z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
        </div>
		<div class="content">
		<h4 class="mb-0 title">Logo Personalization</h4>
        </div>
        </div>
        </div>
		<div class="col-lg-2 col-md-3 col-sm-4 col-6 columnBox ourKeyServiceBox">
		<div class="ourKeyServiceBoxinner">
		<div class="icon">
		    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50" x="0" y="0" viewBox="0 0 682.667 682.667" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#000000" opacity="1" data-original="#000000" class=""></path></clipPath></defs><path d="M0 0h65.441" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.33333 0 0 -1.33333 540.637 59.67)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h7.977" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.33333 0 0 -1.33333 491.088 59.67)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h102.604" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.33333 0 0 -1.33333 491.088 106.645)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0v-30.687c0-12.675-10.275-22.95-22.951-22.95h-18.918" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(315.666 331.334)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h-354.19c-8.907 0-16.127-7.22-16.127-16.127 0-8.906 7.22-16.126 16.127-16.126H0c8.907 0 16.127 7.22 16.127 16.126C16.127-7.22 8.907 0 0 0Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(377.817 40.066)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0c0 13.216 4.952 25.922 13.794 35.744l.198.221c7.231 8.09 11.628 18.766 11.628 30.471 0 17.057-9.337 31.933-23.179 39.797-2.626 1.493-5.887-.384-5.887-3.404V68.798c0-5.704-4.624-10.328-10.329-10.328h-12.696c-5.704 0-10.329 4.624-10.329 10.328v34.031c0 3.02-3.262 4.897-5.888 3.404-13.841-7.864-23.178-22.74-23.178-39.797 0-11.705 4.397-22.381 11.628-30.471l.198-.221C-45.198 25.922-40.246 13.216-40.246 0" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(220.845 40.066)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 1.588-29.267" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(46.165 69.333)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="m0 0 7.433 136.942c.715 13.186-9.783 24.27-22.989 24.27h-274.827c-13.205 0-23.704-11.084-22.988-24.27l3.954-72.866" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(353.691 40.066)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0v31.417c0 30.26-10.514 58.433-28.362 80.688" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(387.943 36.482)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0c-17.848-22.255-28.362-50.428-28.362-80.688v-31.417" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(41.863 148.588)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h-.841A8.428 8.428 0 0 0-9.27 8.428c0 13.783-11.172 24.956-24.955 24.956h-79.429c-35.349 0-64.004-28.655-64.004-64.004v-42.297a14.6 14.6 0 0 1 9.799-13.789l15.765-5.489c5.511-1.919 11.266 2.178 11.257 8.013l-.035 29.826c-.06 14.505 11.507 26.314 25.837 26.376l.312.001a41.45 41.45 0 0 0 12.869-1.994c7.342-2.359 14.95-3.789 22.662-3.756h5.681c7.712-.033 15.32 1.397 22.662 3.756a41.45 41.45 0 0 0 12.869 1.994l.312-.001c14.33-.062 25.897-11.871 25.837-26.376l-.029-29.828c-.008-5.835 5.747-9.93 11.256-8.011l15.761 5.489a14.6 14.6 0 0 1 9.798 13.788v47.962C24.955-11.173 13.782 0 0 0Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(277.074 442.673)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h.14c.552-21.754 9.672-42.657 26.049-56.809C39.375-68.203 57.883-78.118 82.278-78.29c24.392.38 42.818 10.453 55.907 21.959 15.937 14.008 24.825 34.443 25.522 55.738" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(118.87 312.475)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0c-7.757-1.428-13.638-8.212-13.638-16.382v-28.486c0-9.208 7.464-16.672 16.672-16.672h16.42v41.104" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(99.415 373.042)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0v-41.102h16.419c9.208 0 16.672 7.464 16.672 16.671V4.055c0 8.17-5.881 14.955-13.638 16.382" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(282.576 352.604)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0h-18.822c-7.181 0-13.003 5.821-13.003 13.002 0 7.182 5.822 13.003 13.003 13.003H0c7.181 0 13.002-5.821 13.002-13.003C13.002 5.821 7.181 0 0 0Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(257.323 265.924)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0c-6.891 3.736-11.441 11.002-11.441 19.185v25.141" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(253.911 201.278)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0v-25.219c0-8.323-4.683-15.498-11.464-19.185" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(158.975 245.682)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path><path d="M0 0v14.516c0 12.046-9.765 21.811-21.812 21.811h-126.141c-12.047 0-21.812-9.765-21.812-21.811v-63.088c0-12.046 9.765-21.811 21.812-21.811 7.035 0 12.738-5.703 12.738-12.738v-39.015c0-4.953 6.523-6.759 9.07-2.511l28.831 48.077a12.738 12.738 0 0 0 10.924 6.187h64.578C-9.765-70.383 0-60.618 0-48.572v13.508" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(504.5 467.86)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000" class=""></path></g></g></svg>
        </div>
		<div class="content">
		<h4 class="mb-0 title">Design Support</h4>
        </div>
        </div>
        </div>
		<div class="col-lg-2 col-md-3 col-sm-4 col-6 columnBox ourKeyServiceBox">
		<div class="ourKeyServiceBoxinner">
		<div class="icon">
		    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="50" height="50" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M395.051 340.428c34.549-35.192 55.895-83.388 55.895-136.484C450.945 96.452 363.493 9 256 9S61.055 96.452 61.055 203.945c0 53.095 21.346 101.291 55.895 136.484l-52.093 90.227a7 7 0 0 0 7.447 10.361l70.109-14.152 22.801 67.793a7 7 0 0 0 12.697 1.269l56.704-98.216c7.025.77 14.158 1.178 21.385 1.178s14.359-.408 21.384-1.178L336.151 499.5a7 7 0 0 0 12.697-1.269l22.8-67.793 70.11 14.151a7 7 0 0 0 7.447-10.361zM173.54 475.498l-19.825-58.947a7.004 7.004 0 0 0-8.02-4.63l-60.96 12.305 42.667-73.9c25.702 22.607 57.38 38.564 92.328 45.167zM75.055 203.945C75.055 104.171 156.227 23 256 23s180.945 81.171 180.945 180.945S355.773 384.89 256 384.89 75.055 303.718 75.055 203.945zm293.311 211.549a7.002 7.002 0 0 0-8.02 4.63l-19.824 58.946-48.254-83.578c34.948-6.603 66.628-22.56 92.33-45.167l44.73 77.473zM256 59.139c-79.846 0-144.806 64.959-144.806 144.806S176.154 348.75 256 348.75s144.806-64.959 144.806-144.806S335.846 59.139 256 59.139zm0 275.611c-72.127 0-130.806-58.679-130.806-130.806S183.873 73.139 256 73.139s130.806 58.679 130.806 130.806S328.127 334.75 256 334.75zm94.157-152.613a7.002 7.002 0 0 0-6.23-4.824l-59.553-3.637-21.86-55.514a7.002 7.002 0 0 0-13.028 0l-21.86 55.514-59.553 3.637a7 7 0 0 0-4.025 12.389l46.041 37.946-14.943 57.761a7 7 0 0 0 10.538 7.657L256 261.003l50.316 32.062a6.992 6.992 0 0 0 7.876-.24 6.998 6.998 0 0 0 2.662-7.417l-14.943-57.761 46.041-37.946a6.996 6.996 0 0 0 2.205-7.564zm-60.593 37.544a7 7 0 0 0-2.325 7.155l11.609 44.871-39.086-24.907a6.993 6.993 0 0 0-7.524 0l-39.087 24.907 11.609-44.871a7.002 7.002 0 0 0-2.325-7.155l-35.767-29.478 46.263-2.825a7 7 0 0 0 6.087-4.422L256 139.832l16.981 43.125a7 7 0 0 0 6.087 4.422l46.263 2.825z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
        </div>
		<div class="content">
		<h4 class="mb-0 title">Brand Alignment</h4>
        </div>
        </div>
        </div>
		
		
        </div>
        </div>        
    </div>
</section>

<section class="pt-60 pb-60 solution-sec">
    <div class="container">
        <div class="mb-40 text-center section-header slideInUp" data-animation="slideInUp" data-animated="true">
            <!-- Take first category for section title -->
            <h2 class="title">End-to-End Uniform Solutions by Mafatlal</h2>
            <p class="mb-0 para">Mafatlal Industries Limited provides a sophisticated supply chain for corporates, industries, and government institutions. We combine century-old textile wisdom with modern logistics to serve enterprise requirements across India and global markets.</p>
        </div>
        <div class="solution-inner">
            <div class="row">
                <div class="col-lg-6 col-md-6 solution-box-outer">
                    <div class="solution-box">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 510 510" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class=""><g><path d="M446.259 487h32.088c4.415 0 8 3.585 8 8s-3.585 8-8 8H31.653c-4.415 0-8-3.585-8-8s3.585-8 8-8h32.088V307.44h.027a17.565 17.565 0 0 1-.027-.974c0-6.698 3.779-13.269 11.184-18.29 8.262-5.602 21.537-9.473 36.567-9.473s28.305 3.871 36.567 9.473c7.405 5.021 11.184 11.592 11.184 18.29 0 .325-.009.65-.027.974h.027V487h48.006V220.228c0-6.697 3.779-13.269 11.184-18.29 8.262-5.602 21.537-9.472 36.567-9.472s28.305 3.87 36.567 9.472c7.405 5.021 11.184 11.593 11.184 18.29V487h48.006V117.931c0-6.697 3.779-13.268 11.184-18.289 8.262-5.602 21.537-9.473 36.567-9.473s28.305 3.871 36.567 9.473c7.405 5.021 11.184 11.592 11.184 18.289zm-366.518 0h63.502V327.543c-8.178 4.058-19.369 6.685-31.751 6.685s-23.573-2.627-31.751-6.685zm143.508 0h63.502V241.306c-8.178 4.057-19.369 6.684-31.751 6.684s-23.573-2.627-31.751-6.684zm143.508 0h63.502V139.009c-8.178 4.058-19.369 6.685-31.751 6.685s-23.573-2.627-31.751-6.685zm-80.006-266.772c0-2.044-1.903-3.515-4.163-5.047-6.211-4.211-16.29-6.715-27.588-6.715s-21.377 2.504-27.588 6.715c-2.26 1.532-4.163 3.003-4.163 5.047s1.903 3.514 4.163 5.047c6.211 4.211 16.29 6.715 27.588 6.715s21.377-2.504 27.588-6.715c2.26-1.533 4.163-3.003 4.163-5.047zm143.508-102.297c0-2.043-1.903-3.514-4.163-5.046-6.211-4.211-16.29-6.716-27.588-6.716s-21.377 2.505-27.588 6.716c-2.259 1.532-4.163 3.003-4.163 5.046 0 2.044 1.904 3.515 4.163 5.047 6.211 4.211 16.29 6.716 27.588 6.716s21.377-2.505 27.588-6.716c2.26-1.532 4.163-3.003 4.163-5.047zM111.492 294.703c-11.298 0-21.377 2.505-27.588 6.716-2.26 1.532-4.163 3.003-4.163 5.047s1.903 3.514 4.163 5.046c6.211 4.211 16.29 6.716 27.588 6.716s21.377-2.505 27.588-6.716c2.259-1.532 4.163-3.002 4.163-5.046s-1.904-3.515-4.163-5.047c-6.211-4.211-16.29-6.716-27.588-6.716zM404.37 35.431C306.148 119.3 198.667 172.279 97.025 206.523c-4.184 1.41-8.726-.843-10.136-5.027s.843-8.726 5.027-10.135c99.5-33.523 204.718-85.273 300.941-167.14l-15.374.82a8.004 8.004 0 0 1-8.414-7.563 8.003 8.003 0 0 1 7.562-8.415l38.472-2.052a7.998 7.998 0 0 1 8.338 9.178l-5.55 36.933a8.003 8.003 0 0 1-9.1 6.722 8.003 8.003 0 0 1-6.722-9.099z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                        <h3>Scalable Bulk Manufacturing and Supply</h3>
                        <p>We deliver high-volume custom uniforms with an annual fabric capacity of 100+ million meters. From corporate uniforms and industrial uniforms to school uniforms and hospitality uniforms, we ensure consistent quality, fit, and color across every order whether for a single location or a global workforce.</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 solution-box-outer">
                    <div class="solution-box">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 1000 1000" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M500.39 866H399.61a16 16 0 0 1-16-16V747.16a253.41 253.41 0 0 1-61.42-25.46l-72.74 72.72a16 16 0 0 1-22.63 0l-71.24-71.24a16 16 0 0 1 0-22.63l72.72-72.74a253.41 253.41 0 0 1-25.46-61.42H100a16 16 0 0 1-16-16V449.61a16 16 0 0 1 16-16h102.84a253.41 253.41 0 0 1 25.46-61.42l-72.72-72.74a16 16 0 0 1 0-22.63l71.24-71.24a16 16 0 0 1 22.63 0l72.74 72.72a253.41 253.41 0 0 1 61.42-25.46V150a16 16 0 0 1 16-16h100.78a16 16 0 0 1 16 16v700a16 16 0 0 1-16 16zm-84.78-32h68.78V166h-68.78v99.44A16 16 0 0 1 403 281.09 221.8 221.8 0 0 0 328.48 312a16 16 0 0 1-20-2.12l-70.33-70.32-48.62 48.62 70.32 70.33a16 16 0 0 1 2.12 20A221.8 221.8 0 0 0 231.09 453a16 16 0 0 1-15.65 12.65H116v68.78h99.44A16 16 0 0 1 231.09 547 221.8 221.8 0 0 0 262 621.52a16 16 0 0 1-2.12 20l-70.32 70.33 48.62 48.62 70.33-70.32a16 16 0 0 1 20-2.12A221.8 221.8 0 0 0 403 718.91a16 16 0 0 1 12.65 15.65z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M451.75 673.5c-95.67 0-173.5-77.83-173.5-173.5s77.83-173.5 173.5-173.5a173 173 0 0 1 53.59 8.5 16 16 0 0 1 11.05 15.21v299.62A16 16 0 0 1 505.34 665a173 173 0 0 1-53.59 8.5zm0-315c-78 0-141.5 63.48-141.5 141.5s63.48 141.5 141.5 141.5a141.47 141.47 0 0 0 32.64-3.79V362.29a141.47 141.47 0 0 0-32.64-3.79z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M835 516.12H500.39a16 16 0 0 1 0-32H835a16 16 0 0 1 0 32zM650.15 306.12H500.39a16 16 0 0 1 0-32h133.76V165a16 16 0 0 1 32 0v125.12a16 16 0 0 1-16 16zM650.15 851a16 16 0 0 1-16-16V726.12H500.39a16 16 0 0 1 0-32h149.76a16 16 0 0 1 16 16V835a16 16 0 0 1-16 16z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M650.15 181a48.5 48.5 0 1 1 48.5-48.5 48.56 48.56 0 0 1-48.5 48.5zm0-65a16.5 16.5 0 1 0 16.5 16.5 16.52 16.52 0 0 0-16.5-16.5zM500.39 411.12a16 16 0 0 1 0-32h238.78l95-95a16 16 0 1 1 22.62 22.63l-99.7 99.7a16 16 0 0 1-11.31 4.69z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M867.5 321.89a48.48 48.48 0 1 1 34.29-14.18 48.31 48.31 0 0 1-34.29 14.18zm0-65a16.5 16.5 0 1 0 11.67 4.83 16.44 16.44 0 0 0-11.67-4.8zM650.19 916a48.5 48.5 0 1 1 48.5-48.5 48.56 48.56 0 0 1-48.5 48.5zm0-65a16.5 16.5 0 1 0 16.5 16.5 16.52 16.52 0 0 0-16.5-16.5zM844.52 720.82a15.91 15.91 0 0 1-11.31-4.69l-95-95H500.39a16 16 0 0 1 0-32h244.43a16 16 0 0 1 11.31 4.68l99.7 99.71a16 16 0 0 1-11.31 27.31z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M867.5 776.28a48.49 48.49 0 1 1 34.29-82.77 48.48 48.48 0 0 1-34.3 82.77zm0-65a16.49 16.49 0 1 0 11.67 4.82 16.48 16.48 0 0 0-11.67-4.79zM867.5 548.62a48.5 48.5 0 1 1 48.5-48.5 48.56 48.56 0 0 1-48.5 48.5zm0-65a16.5 16.5 0 1 0 16.5 16.5 16.52 16.52 0 0 0-16.5-16.5z" fill="#000000" opacity="1" data-original="#000000"></path></g></svg>
                        <h3>Advanced Fabric Technology and Safety Features</h3>
                        <p>To meet the demands of modern workspaces, we integrate advanced textile technologies like Hexarel to create high-performance custom uniforms. Our uniform service supports corporate uniforms, industrial uniforms, school uniforms, and hospitality uniforms with features such as anti-microbial protection, thermal comfort, and enhanced safety for everyday use.</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 solution-box-outer">
                    <div class="solution-box">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m61.305 25.292-6.043-5.04c-.947-.79-2.076-1.276-3.179-1.369-1.282-.103-2.433.332-3.162 1.205l-4.703 5.639-17.945 21.519.012.01a1.482 1.482 0 0 0-.359.982l.237 11.671a1.499 1.499 0 0 0 1.799 1.44l11.227-2.283c.34-.069.628-.261.842-.518l.011.009 17.86-21.416 4.703-5.639c1.413-1.697.841-4.424-1.3-6.21zm-32.18 32.757-.135-6.622 6.505 5.326zm9.563-2.557-9.143-7.487 16.016-19.204 9.075 7.568zM60.3 29.579l-3.742 4.486-9.075-7.568 3.742-4.487c.09-.108.326-.16.606-.137.487.041 1.037.29 1.508.683l6.043 5.04c.941.784 1.13 1.73.918 1.983z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M20.971 49.408a1.538 1.538 0 0 0-.235-.121c-.009-.004-.017-.01-.026-.013a1.477 1.477 0 0 0-.537-.104l-.028-.002a1.517 1.517 0 0 0-.537.104l-.026.008a1.506 1.506 0 0 0-.487.323l-.003.002-.003.003-.007.007-3.797 3.796a2.148 2.148 0 0 1-3.026.004l-1.05-1.05a2.146 2.146 0 0 1 0-3.021l3.802-3.802.004-.004.005-.005.003-.005c.14-.141.248-.306.322-.484.004-.009.005-.019.009-.028a1.51 1.51 0 0 0 .104-.535l-.002-.028a1.48 1.48 0 0 0-.105-.538l-.011-.02a1.461 1.461 0 0 0-.123-.239 18.61 18.61 0 0 1-2.604-6.277c-.005-.023-.015-.043-.021-.066a1.54 1.54 0 0 0-.069-.215c-.015-.035-.037-.066-.054-.1-.028-.053-.054-.106-.087-.155-.022-.032-.049-.059-.073-.088-.037-.046-.073-.093-.115-.134-.022-.022-.048-.039-.071-.059-.05-.043-.1-.087-.155-.123-.02-.013-.042-.022-.063-.034a1.355 1.355 0 0 0-.193-.1c-.025-.01-.051-.015-.076-.024-.066-.023-.131-.046-.2-.059-.048-.009-.097-.01-.146-.015-.047-.005-.093-.014-.142-.014H5.763a2.145 2.145 0 0 1-2.143-2.142v-1.47c0-1.181.961-2.142 2.143-2.142h5.387l.009-.002a1.49 1.49 0 0 0 .445-.077c.024-.008.048-.013.072-.022.137-.052.263-.123.378-.21.022-.016.041-.033.062-.051.112-.096.213-.204.293-.328.01-.016.017-.033.026-.049.08-.135.143-.282.178-.44l.003-.008a18.64 18.64 0 0 1 2.604-6.278c.05-.077.089-.157.123-.239l.01-.02c.07-.175.103-.357.105-.539l.002-.027a1.512 1.512 0 0 0-.104-.536l-.009-.027a1.506 1.506 0 0 0-.323-.487l-.002-.003-.003-.003-.009-.009-3.795-3.795a2.148 2.148 0 0 1-.004-3.026l1.05-1.05a2.147 2.147 0 0 1 3.022 0l3.811 3.811c.006.006.015.008.021.014.128.124.274.217.432.287.036.016.071.028.107.041.161.057.327.096.498.097h.004c.169 0 .338-.039.502-.098.049-.017.093-.042.14-.064.058-.028.119-.044.174-.08a18.613 18.613 0 0 1 6.276-2.604c.018-.004.034-.012.052-.017.079-.02.156-.042.229-.073.038-.016.071-.039.106-.058.05-.027.101-.051.148-.083.04-.028.075-.061.112-.093.036-.031.075-.059.108-.093.034-.034.061-.073.091-.111.031-.038.064-.073.09-.114.026-.039.044-.081.066-.123.023-.044.05-.086.069-.132.02-.05.033-.102.048-.154.012-.041.028-.081.037-.124.014-.071.018-.145.022-.218.001-.025.008-.049.008-.075V7.765c0-1.181.961-2.142 2.143-2.142h1.469c1.182 0 2.143.961 2.143 2.142v5.385c0 .033.008.063.01.096.004.066.007.132.02.196.01.049.027.094.042.141.014.046.024.093.042.137.02.05.048.095.074.142.021.038.038.078.062.114.028.042.061.079.093.117.029.036.056.074.089.108s.071.062.108.092c.038.032.073.066.114.094.044.03.093.053.14.079.038.021.074.045.114.062.067.029.139.049.211.068.024.006.045.017.069.022a18.648 18.648 0 0 1 6.278 2.604c.054.035.115.051.171.079.048.023.093.048.142.065.164.059.332.098.502.098.172 0 .34-.04.501-.097.036-.013.071-.025.107-.041.158-.07.304-.163.432-.287.006-.006.015-.008.022-.014l3.806-3.807a2.15 2.15 0 0 1 3.026-.004l1.05 1.05c.572.572.739 1.392.523 2.121.116.003.23-.001.347.008a7.89 7.89 0 0 1 2.559.687c.448-1.711.026-3.604-1.308-4.937l-1.05-1.05c-1.996-1.996-5.253-1.998-7.265 0l-2.991 2.992a21.523 21.523 0 0 0-5.039-2.09v-4.23a5.148 5.148 0 0 0-5.143-5.142h-1.469a5.15 5.15 0 0 0-5.143 5.142v4.229a21.548 21.548 0 0 0-5.039 2.09L17.4 11.088c-2.007-1.994-5.266-1.992-7.26.004l-1.05 1.05c-1.996 1.995-1.997 5.252 0 7.265l2.991 2.991a21.565 21.565 0 0 0-2.09 5.039H5.763A5.15 5.15 0 0 0 .62 32.579v1.47a5.148 5.148 0 0 0 5.143 5.142h4.228a21.53 21.53 0 0 0 2.09 5.039l-2.995 2.996c-1.994 2.007-1.992 5.264.004 7.26l1.05 1.05c.998.998 2.312 1.497 3.627 1.497s2.632-.498 3.637-1.497l2.992-2.991a21.588 21.588 0 0 0 3.145 1.467l-.066-3.255a18.781 18.781 0 0 1-2.504-1.349z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M20.391 33.314c0-6.022 4.899-10.921 10.921-10.921 3.582 0 6.758 1.742 8.751 4.414l1.97-2.362c-2.556-3.084-6.412-5.052-10.721-5.052-7.676 0-13.921 6.245-13.921 13.921 0 5.207 2.878 9.749 7.123 12.137l1.966-2.357c-3.602-1.788-6.089-5.494-6.089-9.78z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                        <h3>Customization Across Diverse Industries</h3>
                        <p>Our custom uniforms are engineered to meet sector-specific needs—combining hygiene and professional aesthetics for hospitality and healthcare with rugged durability and compliance for industrial and defence environments.</p>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 solution-box-outer">
                    <div class="solution-box">
                        <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 60 52" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><g fill="#000" fill-rule="nonzero"><path d="M55 0H5a5.006 5.006 0 0 0-5 5v36a5.006 5.006 0 0 0 5 5h30v5a1 1 0 0 0 1.707.707L40 48.414l3.293 3.293A1 1 0 0 0 45 51v-5h10a5.006 5.006 0 0 0 5-5V5a5.006 5.006 0 0 0-5-5zM32.31 19.565a.532.532 0 0 1-.168-.844 2.541 2.541 0 0 0 0-3.441.532.532 0 0 1 .169-.845 2.544 2.544 0 0 0 1.316-3.18.532.532 0 0 1 .478-.716 2.543 2.543 0 0 0 2.434-2.434.532.532 0 0 1 .717-.478 2.549 2.549 0 0 0 3.18-1.317.53.53 0 0 1 .843-.168c.973.895 2.47.895 3.442 0a.531.531 0 0 1 .844.169 2.545 2.545 0 0 0 3.179 1.316.532.532 0 0 1 .717.478A2.545 2.545 0 0 0 51.9 10.54a.531.531 0 0 1 .478.715 2.543 2.543 0 0 0 1.317 3.18.532.532 0 0 1 .168.844 2.541 2.541 0 0 0 0 3.441.532.532 0 0 1-.169.845 2.544 2.544 0 0 0-1.316 3.18.532.532 0 0 1-.478.716 2.543 2.543 0 0 0-2.439 2.439.532.532 0 0 1-.717.478 2.548 2.548 0 0 0-3.18 1.317.514.514 0 0 1-.377.3.524.524 0 0 1-.466-.131 2.543 2.543 0 0 0-3.442 0 .525.525 0 0 1-.466.131.516.516 0 0 1-.378-.3 2.544 2.544 0 0 0-3.179-1.316.532.532 0 0 1-.717-.478A2.545 2.545 0 0 0 34.1 23.46a.531.531 0 0 1-.478-.715 2.543 2.543 0 0 0-1.312-3.18zm10.984 18.727-.294.294v-9.4c.134 0 .263.05.361.141a2.5 2.5 0 0 0 2.216.626 2.5 2.5 0 0 0 1.8-1.426.534.534 0 0 1 .669-.276c.304.108.626.156.949.143v10.192l-2.293-2.293a1 1 0 0 0-1.414 0zM43 48.586l-2.293-2.293a1 1 0 0 0-1.414 0L37 48.586V28.392c.323.013.645-.035.949-.143a.532.532 0 0 1 .668.275 2.5 2.5 0 0 0 1.806 1.427c.19.038.383.054.577.049v11a1 1 0 0 0 1.707.707l.293-.293zM58 41a3 3 0 0 1-3 3H45v-4.586l1-1 3.293 3.293A1 1 0 0 0 51 41V27.307c.28-.392.439-.856.459-1.337a.535.535 0 0 1 .512-.511 2.532 2.532 0 0 0 2.277-3.409.533.533 0 0 1 .276-.667 2.532 2.532 0 0 0 .8-4.022.534.534 0 0 1 0-.723 2.531 2.531 0 0 0-.8-4.021.532.532 0 0 1-.277-.667 2.532 2.532 0 0 0-2.277-3.409.535.535 0 0 1-.512-.511 2.531 2.531 0 0 0-3.408-2.279.529.529 0 0 1-.668-.275 2.532 2.532 0 0 0-4.022-.8.533.533 0 0 1-.722 0 2.531 2.531 0 0 0-4.021.8.531.531 0 0 1-.669.276 2.531 2.531 0 0 0-3.408 2.279.535.535 0 0 1-.512.511 2.532 2.532 0 0 0-2.277 3.409.533.533 0 0 1-.276.667 2.532 2.532 0 0 0-.8 4.022.534.534 0 0 1 0 .723 2.531 2.531 0 0 0 .8 4.021.532.532 0 0 1 .277.667 2.532 2.532 0 0 0 2.277 3.409c.277.012.5.234.512.511.02.48.18.944.459 1.336V44H5a3 3 0 0 1-3-3V5a3 3 0 0 1 3-3h50a3 3 0 0 1 3 3z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M11 36a3 3 0 0 1-3-3V13a3 3 0 0 1 3-3h18a1 1 0 0 0 0-2H11a5.006 5.006 0 0 0-5 5v20a5.006 5.006 0 0 0 5 5h19a1 1 0 0 0 0-2z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M12 24h14a1 1 0 0 0 0-2H12a1 1 0 0 0 0 2zM12 20h14a1 1 0 0 0 0-2H12a1 1 0 0 0 0 2zM12 16h14a1 1 0 0 0 0-2H12a1 1 0 0 0 0 2zM11.083 33.4a1 1 0 0 0 1.317.517 7.687 7.687 0 0 0 2.506-1.529 4.7 4.7 0 0 0 .705-.9c.382.43.83.794 1.329 1.08.695.454 1.56.565 2.347.3.302-.117.575-.298.8-.53.453.283.951.484 1.473.595l.756.179c1.543.452 3.146.664 4.754.63a1 1 0 1 0-.144-1.995c-1.403.018-2.8-.177-4.144-.58l-.773-.183c-.933-.215-1.173-.591-1.272-.856a1 1 0 0 0-1.934.421.505.505 0 0 1-.223.451.922.922 0 0 1-.66-.17 4.1 4.1 0 0 1-1.72-2.176 1 1 0 0 0-1.936.408 2.587 2.587 0 0 1-.774 1.907 5.933 5.933 0 0 1-1.89 1.114 1 1 0 0 0-.517 1.317zM43 25a8 8 0 1 0-8-8 8.009 8.009 0 0 0 8 8zm0-14a6 6 0 1 1 0 12 6 6 0 0 1 0-12z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></g></svg>
                        <h3>Quality Certifications and Institutional Trust</h3>
                        <p>Mafatlal is a trusted partner to Indian Defence Forces and government bodies, delivering uniforms manufactured under strict quality controls and certified by ISO and OEKO-TEX to meet the highest global standards of safety and sustainability.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>



<!--
<section class="pt-80 service-sec">
    <div class="container">
        <div class="testimonials-header">
            <h2>Why us ?</h2>
            <p>" Why India’s Top Institutions Trust Mafatlal for Uniforms"</p>
        </div>

        <div class="service-inner">
            <div class="service-wrapper">
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
-->
@endsection
