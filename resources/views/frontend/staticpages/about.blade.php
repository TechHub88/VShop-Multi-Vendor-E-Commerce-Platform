@extends('frontend.layout')

<style>
    .bedcum-sec {
        /*background: #000;*/
        background-image: url(https://kidzz.in/mafatlal/public/uploads/about/1762590153.jpg);
        background-position: 50% 50%;
        background-repeat: no-repeat;
        background-size: cover;
        color: #fff;
        padding: 80px 0 !important;
    }
    .bedcum-inner-sec {
        text-align: center;
    }
    .bedcum-inner-sec ul {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .bedcum-inner-sec ul li a {
        font-size: 16px;
        font-weight: 500;
        color: #ed3237;
    }
    .bedcum-inner-sec ul li {
        font-size: 16px;
        font-weight: 500;
    }
    .about-story-right {
        background-color: #fff !important;
        padding-right: 0 !important;
    }
    .about-story-right .para {
        font-size: 18px;
    }
    .about-story-sec {
        width: 100%;
        padding: 60px 0;
    }
    .about-story-left img {
        height: 410px;
        width: 100%;
        object-fit: cover;
        border-radius: 15px;
    }
    .mission-box {
        width: 100%;
        height: 100%;
        border: 1px solid rgba(34, 31, 32, .1);
        padding: 27px;
        border-radius: 22px;
        box-shadow: 0 0 14.604644775390625px #221f2014;
        position: relative;
        background: #fff;
        border-left: 4px solid #ed3237;
    }
    .mission-box h3 {
        margin-top: 16px;
    }
    .mission-box p {
        font-size: 15px;
        margin: 0;
    }
    .mission-bottom {
        padding-top: 10px;
    }
    .mission-vission-sec {
        padding-bottom: 60px;
    }
    .about-box h2 {
        font-size: 28px;
    }
    .about-box {
        margin-bottom: 25px;
    }
    .about-box ul li {
        padding-left: 26px;
        position: relative;
        font-size: 16px;
    }
    .about-box ul li::before {
        content: "";
        position: absolute;
        top: 11px;
        left: 9px;
        width: 8px;
        height: 8px;
        background-color: #ed3237;
        border-radius: 50%;
    }
    .about-text-sec {
        padding-bottom: 60px;
    }
    .mission-bottom-wrapper {
        padding-top: 40px;
    }
    .mission-row-direction {
        flex-direction: row-reverse;
        padding-top: 50px;
    }
    .industries-box {
        width: 100%;
        height: 100%;
        border: 1px solid rgba(34, 31, 32, .1);
        border-radius: 22px;
        box-shadow: 0 0 14.604644775390625px #221f2014;
        position: relative;
        background: #fff;
        padding: 18px;
        transition: all .3s ease-in-out;
    }
    .industries-box:hover {
        transform: translateY(-10px);
        border-color: #ed3237;
    }
    .industries-icon {
        font-size: 32px;
        margin-bottom: 20px;
    }
    .industries-box h4 {
        font-size: 20px;
    }
    .industries-box p {
        font-size: 16px;
    }
    
    .industries-sec {
        padding-top: 60px;
    }
    
    .industries-box-outer {
        margin-bottom: 24px;
    }
    .about-box p {
        border-left: 4px solid #ed3237;
        padding: 14px 20px;
        margin-bottom: 15px;
        font-weight: 500;
    }
    .certificate-right img {
        border-radius: 15px;
    }
    .mission-box-left {
        position: relative;
    }
    .thumb-1 img {
        width: 78%;
        height: 378px;
        border-radius: 15px;
        object-fit: cover;
    }
    .thumb-2 {
        position: absolute;
        right: 15px;
        top: 50%;
        border: 6px solid #ffffff;
        width: 38%;
        height: 223px;
        border-radius: 15px;
        overflow: hidden;
        transform: translate(0, -50%);
    }
    .thumb-2 img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .mission-cont-box {
        margin-bottom: 35px;
    }
    .mission-cont-box h3 {
        font-size: 30px;
    }
    
    @media(max-width: 991px) {
        .about-story-right {
            padding-left: 0 !important;
        }
        .thumb-2 {
            width: 56%;
        }
    }
    @media(max-width: 767px) {
        .mission-right {
            margin-top: 20px;
        }
    }
    @media(max-width: 575px) {
        .about-story-left img {
            display: none;
        }
        .about-story-sec {
            padding-bottom: 0;
        }
        .mission-bottom-wrapper {
            padding-top: 0px;
        }
        .mission-right {
            padding-top: 30px;
        }
        .about-box h2 {
            font-size: 24px;
        }
        .about-box p {
            font-size: 15px;
        }
        .about-story-sec {
            padding-bottom: 0;
            padding-top: 0px;
        }
        .home-aboutUsContent .title-2 {
            font-size: 28px;
        }
        .mission-cont-box h3 {
            font-size: 23px;
        }
    }
</style>

@section('content')

<section class="bedcum-sec">
    <div class="container">
        <div class="bedcum-inner-sec">
            <h1>About Us</h1>
            <ul>
                <li>
                    <a href="#">Home</a>
                </li>
                <li>
                    /
                </li>
                <li>About Us</li>
            </ul>
        </div>
    </div>
</section>

<section class="about-story-sec">
    <div class="container">
        <div class="about-story-inner">
            <div class="row align-items-center">
                <div class="col-lg-4 col-md-5">
                    <div class="about-story-left">
                        <img data-src="https://kidzz.in/mafatlal/public/assets/images/aboutImage.jpg" class="img-fluid lazyloaded" alt="" src="https://kidzz.in/mafatlal/public/assets/images/aboutImage.jpg">
                    </div>
                </div>
                <div class="col-lg-8 col-md-7">
                    <div class="about-story-right home-aboutUsContent">
                        <span class="about-meta">
        				    <div class="about-meta-inner"><i class="fa-light fa-uniform-martial-arts"></i> Our Story</div>
        				</span>
        				<h3 class="title-2">Brand Story</h3>
        				<p class="para">Mafatlal Uniforms is part of Mafatlal Industries Limited, one of India’s most respected textile organisations with a legacy of over 120 years. Founded in 1905, the Mafatlal Group has played a defining role in shaping India’s textile landscape through generations of innovation, integrity, and manufacturing excellence. Built on this strong foundation, Mafatlal Uniforms delivers dependable uniform solutions for institutions, enterprises, and organisations across India, backed by trusted relationships and proven expertise.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="mission-vission-sec pt-40">
    <div class="container">
        <div class="mission-vission-inner">
            <div class="mb-20 text-center section-header">
                <h2 class="mb-0 title slideInUp" data-animation="slideInUp" data-animated="true">
                    Vision & Mission
                </h2>    
                <p>To be a globally respected textile leader, setting benchmarks in design, technology, and sustainable manufacturing.</p>
            </div>
            <div class="mission-bottom-wrapper">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-md-6">
                        <div class="mission-box-left">
                            <div class="thumb-1">
                                <img data-src="https://kidzz.in/mafatlal/public/assets/images/aboutImage.jpg" class="img-fluid lazyloaded" alt="" src="https://kidzz.in/mafatlal/public/assets/images/aboutImage.jpg">
                            </div>
                            <div class="thumb-2">
                                <img data-src="https://kidzz.in/mafatlal/public/assets/images/aboutImage2.jpg" class="img-fluid lazyloaded" alt="" src="https://kidzz.in/mafatlal/public/assets/images/aboutImage2.jpg">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-6">
                        <div class="mission-right">
                            <div class="mission-cont-box">
                                <h3>Our Vision</h3>
                                <p>To be India’s most trusted uniform manufacturing partner, setting benchmarks in quality, consistency, and responsible growth.</p>
                            </div>
                            <div class="mission-cont-box mb-0">
                                <h3>Our Mission</h3>
                                <p>To deliver best-in-class uniform products and services that support professionals, institutions, and communities with reliability, comfort, and care.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="pt-50 pb-50 homeFeatureSec" data-background="https://kidzz.in/mafatlal/public/uploads/news/1762586371_news_690eef03573da.jpg">
    <div class="container">
        <div class="homeFeatureSecinner" data-animation="slideInUp">
            <div class="homeFeatureSlider owl-carousel owl-theme">
                <div class="homeFeatureSliderItem">
                    <div class="text-center homeFeatureContent">
                        <h3 class="mb-25 title">Manufacturing Capabilities</h3>
                        <p class="mb-0 para">Mafatlal Uniforms offers end-to-end uniform manufacturing solutions, combining deep fabric knowledge with large-scale production capabilities. From material selection and design support to cutting, stitching, finishing, and quality control, our processes are built to ensure consistency, durability, and timely delivery across high-volume requirements.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="industries-sec">
    <div class="container">
        <div class="mb-20 text-center section-header">
            <h2 class="mb-0 title slideInUp" data-animation="slideInUp" data-animated="true">
                Industry Expertise
            </h2>
            <p>Recognised among established uniform manufacturers in India, Mafatlal Uniforms serves diverse sectors with tailored solutions</p>
        </div>
        <div class="Industries-inner pt-3">
            <div class="row">
                <div class="col-lg-3 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 128 128" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M49.914 117.728c0 1.104-.877 2.009-1.981 2.009s-2.009-.905-2.009-2.009v-25.16c0-1.697.735-3.226 1.924-4.329a6.502 6.502 0 0 1 4.444-1.726h23.404c1.726 0 3.311.65 4.442 1.726 1.188 1.104 1.926 2.632 1.926 4.329v25.16c0 1.104-.878 2.009-1.982 2.009s-2.009-.905-2.009-2.009v-25.16c0-.537-.227-1.047-.623-1.414a2.619 2.619 0 0 0-1.754-.651h-9.708v27.226c0 1.104-.876 2.009-1.98 2.009a2.017 2.017 0 0 1-2.009-2.009V90.502h-9.707c-.679 0-1.274.256-1.726.651-.396.367-.651.877-.651 1.414v25.161zM44.283 59.684h13.074a1.97 1.97 0 0 1 1.981 1.981v12.17a1.967 1.967 0 0 1-1.981 1.979H44.283a1.967 1.967 0 0 1-1.981-1.979v-12.17a1.968 1.968 0 0 1 1.981-1.981zm0-24.197h13.074a1.97 1.97 0 0 1 1.981 1.981v12.169a1.968 1.968 0 0 1-1.981 1.981H44.283a1.97 1.97 0 0 1-1.981-1.981V37.469a1.969 1.969 0 0 1 1.981-1.982zm11.065 3.992h-9.056v8.149h9.056zm21.847 8.149c1.104 0 1.98.906 1.98 2.01s-.877 1.981-1.98 1.981h-6.537c-1.104 0-2.01-.878-2.01-1.981V37.469c0-1.104.905-1.981 2.01-1.981a1.97 1.97 0 0 1 1.981 1.981v10.159zm-6.537 12.056h13.046c1.104 0 2.009.878 2.009 1.981v12.17c0 1.104-.905 1.979-2.009 1.979H70.658c-1.104 0-2.01-.876-2.01-1.979v-12.17c0-1.103.906-1.981 2.01-1.981zm11.066 3.991H72.64v8.15h9.084zm-26.376 0h-9.056v8.15h9.056zM97.627 61.298c-.565.934-1.781 1.273-2.716.707a2.275 2.275 0 0 1-.736-.735l-17.15-29.065c-.878-1.5-1.613-3.424-2.065-5.122a21.601 21.601 0 0 1-.68-5.434 21.564 21.564 0 0 1 6.339-15.311A21.584 21.584 0 0 1 95.93 0c5.971 0 11.376 2.434 15.31 6.339 3.905 3.935 6.34 9.339 6.34 15.311 0 1.868-.255 3.679-.707 5.434-.454 1.812-1.162 3.509-2.038 5.094zm-1.697-4.925 15.451-26.178a17.584 17.584 0 0 0 2.207-8.546c0-4.868-1.979-9.282-5.178-12.48A17.609 17.609 0 0 0 95.93 3.99a17.618 17.618 0 0 0-12.508 5.179c-3.171 3.198-5.151 7.612-5.151 12.48 0 1.557.197 3.027.538 4.443.367 1.414.933 2.773 1.612 4.046z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M95.93 10.103c3.17 0 6.056 1.303 8.149 3.396a11.409 11.409 0 0 1 3.397 8.15c0 3.197-1.303 6.085-3.397 8.179-2.094 2.066-4.979 3.367-8.149 3.367a11.622 11.622 0 0 1-8.179-3.367c-2.094-2.094-3.367-4.981-3.367-8.179s1.273-6.084 3.367-8.15a11.527 11.527 0 0 1 8.179-3.396zm5.32 6.198a7.55 7.55 0 0 0-10.669 0c-1.359 1.387-2.207 3.254-2.207 5.349 0 2.094.848 3.99 2.207 5.349a7.55 7.55 0 0 0 10.669 0 7.46 7.46 0 0 0 2.235-5.349c0-2.095-.849-3.962-2.235-5.349zM105.154 97.21c-1.104 0-1.979-.905-1.979-2.009s.876-1.982 1.979-1.982h7.245c2.491 0 4.699 1.444 5.661 3.736l9.367 22.81c.396.962.537 1.925.424 2.887-.198 1.953-1.501 3.763-3.227 4.669a6.162 6.162 0 0 1-2.857.68H6.248a6.163 6.163 0 0 1-2.858-.68c-1.755-.906-3.028-2.716-3.254-4.669a6.415 6.415 0 0 1 .424-2.887l9.396-22.81c.962-2.292 3.169-3.736 5.66-3.736h7.188c1.104 0 1.981.879 1.981 1.982s-.877 2.009-1.981 2.009h-7.188c-.877 0-1.642.424-1.981 1.273l-9.367 22.809a1.832 1.832 0 0 0-.17.935c.085.651.538 1.245 1.104 1.557.311.142.65.227 1.047.227h115.52c.396 0 .735-.085 1.019-.227.595-.312 1.047-.905 1.104-1.557.028-.312 0-.622-.142-.935l-9.367-22.809c-.34-.85-1.104-1.273-1.981-1.273z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M93.921 69.872c0-1.104.905-2.01 2.009-2.01s1.981.906 1.981 2.01v47.855c0 1.104-.878 2.009-1.981 2.009H32.085c-1.104 0-1.981-.905-1.981-2.009V30.253c0-1.698.736-3.227 1.925-4.33a6.465 6.465 0 0 1 4.443-1.755h31.497c1.104 0 1.981.905 1.981 2.01 0 1.104-.878 2.009-1.981 2.009H36.472c-.708 0-1.302.227-1.754.651a1.928 1.928 0 0 0-.623 1.415v85.492h59.827V69.872z" fill="#000000" opacity="1" data-original="#000000"></path></g></svg>
                        </div>
                        <h4>Corporate Uniforms</h4>
                        <p>Designed for professional appearance, comfort, and everyday performance.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60px" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M489.011 423.627h-14.49V310.739a7.515 7.515 0 1 0-15.03 0v112.888H197.458V312.641a7.515 7.515 0 0 0-7.515-7.515H91.198a7.515 7.515 0 0 0-7.515 7.515v110.986H48.074V275.906l411.417-105.934V280.68a7.515 7.515 0 1 0 15.03 0V160.277a7.512 7.512 0 0 0-9.388-7.277l-36.75 9.462-6.987-77.464-.001-.007-3.225-35.757a7.515 7.515 0 0 0-7.484-6.84h-49.614a7.515 7.515 0 0 0-7.484 6.84l-3.225 35.759v.001l-9.008 99.877-42.705 10.996-7.516-83.334v-.004l-3.225-35.758a7.515 7.515 0 0 0-7.484-6.84H230.81a7.515 7.515 0 0 0-7.484 6.84l-3.225 35.757-.001.007-9.562 106.021-41.622 10.717-8.046-89.202v-.005l-3.225-35.757a7.515 7.515 0 0 0-7.484-6.84h-49.613a7.515 7.515 0 0 0-7.484 6.84l-6.91 76.606a7.515 7.515 0 0 0 14.969 1.351l3.059-33.914h42.347l7.641 84.72-58.999 15.191 3.25-36.028a7.515 7.515 0 0 0-14.969-1.351l-3.73 41.356-41.032 10.565a7.515 7.515 0 0 0-5.641 7.277v153.546h-10.06C10.313 423.627 0 433.94 0 446.616s10.313 22.989 22.989 22.989h220.762a7.515 7.515 0 1 0 0-15.03H22.989c-4.389 0-7.96-3.57-7.96-7.959s3.57-7.96 7.96-7.96h466.022c4.332 0 7.96 3.628 7.96 7.96s-3.628 7.959-7.96 7.959H273.81a7.515 7.515 0 1 0 0 15.03h215.201c12.676 0 22.989-10.313 22.989-22.989s-10.313-22.989-22.989-22.989zM105.534 133.32l1.878-20.823h35.88l1.878 20.823zm298.285-75.896 1.878 20.823h-39.636l1.878-20.823zm-39.113 35.852h42.347l6.582 72.983-56.831 14.633zm-91.15-8.316 1.878 20.823h-39.637l1.878-20.823zm-39.115 35.853h42.348l7.112 78.851-57.916 14.913zM98.712 320.156h83.716v103.472H98.712z" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M284.549 270.622a7.515 7.515 0 0 0-7.515-7.515h-30.012a7.515 7.515 0 0 0-7.515 7.515V308.1a7.515 7.515 0 0 0 7.515 7.515h30.012a7.515 7.515 0 0 0 7.515-7.515zm-15.029 29.964h-14.982v-22.449h14.982zM277.034 339.819h-30.012a7.515 7.515 0 0 0-7.515 7.515v37.478a7.515 7.515 0 0 0 7.515 7.515h30.012a7.515 7.515 0 0 0 7.515-7.515v-37.478a7.515 7.515 0 0 0-7.515-7.515zm-7.514 37.479h-14.982v-22.449h14.982zM360.623 270.622a7.515 7.515 0 0 0-7.515-7.515h-30.013a7.515 7.515 0 0 0-7.515 7.515V308.1a7.515 7.515 0 0 0 7.515 7.515h30.013a7.515 7.515 0 0 0 7.515-7.515zm-15.029 29.964h-14.983v-22.449h14.983zM353.109 339.819h-30.013a7.515 7.515 0 0 0-7.515 7.515v37.478a7.515 7.515 0 0 0 7.515 7.515h30.013a7.515 7.515 0 0 0 7.515-7.515v-37.478a7.516 7.516 0 0 0-7.515-7.515zm-7.515 37.479h-14.983v-22.449h14.983zM436.697 270.622a7.515 7.515 0 0 0-7.515-7.515H399.17a7.515 7.515 0 0 0-7.515 7.515V308.1a7.515 7.515 0 0 0 7.515 7.515h30.012a7.515 7.515 0 0 0 7.515-7.515zm-15.03 29.964h-14.982v-22.449h14.982zM429.182 339.819H399.17a7.515 7.515 0 0 0-7.515 7.515v37.478a7.515 7.515 0 0 0 7.515 7.515h30.012a7.515 7.515 0 0 0 7.515-7.515v-37.478a7.515 7.515 0 0 0-7.515-7.515zm-7.515 37.479h-14.982v-22.449h14.982z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                        <h4>Industrial Uniforms</h4>
                        <p>Engineered for durability, safety, and demanding work environments.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 682.667 682.667" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#000000" opacity="1" data-original="#000000"></path></clipPath><clipPath id="b" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z" fill="#000000" opacity="1" data-original="#000000"></path></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0h497" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(7.5 35.557)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v-224.452" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(488.468 260.008)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v224.452" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(23.532 35.557)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0h32.064L0 72.145h-160.323" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(472.435 260.008)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0h-160.323l-32.064-72.145h120.241L56.113 56.113 184.371-72.145h56.113" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(199.887 332.153)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path></g><path d="M0 0v88.177" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.33333 0 0 -1.33333 341.333 164.979)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0h56.113v-40.081H0" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="matrix(1.33333 0 0 -1.33333 341.333 47.409)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><g clip-path="url(#b)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0c0-26.563-21.534-48.097-48.097-48.097-26.563 0-48.096 21.534-48.096 48.097 0 26.563 21.533 48.097 48.096 48.097C-21.534 48.097 0 26.563 0 0Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(304.097 243.976)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v16.032l16.032 8.016" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(256 227.943)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v-72.145" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(256 139.766)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v32.064h128.258V0" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(191.871 35.557)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v72.145h-96.193V0" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(304.097 67.621)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0h64.128v48.097H-48.098V0h16.035" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(103.695 91.67)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M55.597 171.831h112.226v48.097H55.597Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v48.097" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(111.71 171.83)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v48.097" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(111.71 91.67)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M344.177 91.669h112.226v48.097H344.177ZM344.177 171.831h112.226v48.097H344.177Z" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v48.097" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(400.29 171.83)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path><path d="M0 0v48.097" style="stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(400.29 91.67)" fill="none" stroke="#000000" stroke-width="15" stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-dasharray="none" stroke-opacity="" data-original="#000000"></path></g></g></svg>
                        </div>
                        <h4>School Uniforms</h4>
                        <p>Crafted for long wear, comfort, and consistent quality across academic years.</p>
                    </div>
                </div>
                <div class="col-lg-3 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 48 48" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M5 44h38a1 1 0 0 0 1-1V19a1 1 0 0 0-1-1h-5V9a1 1 0 0 0-1-1h-5V7a3 3 0 0 0-3-3H19a3 3 0 0 0-3 3v1h-5a1 1 0 0 0-1 1v9H5a1 1 0 0 0-1 1v24a1 1 0 0 0 1 1zm33-24h4v22h-4zM27 42h-6v-7h6zM18 7a1 1 0 0 1 1-1h10a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H19a1 1 0 0 1-1-1zm-6 3h4v7a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-7h4v32h-7v-8a1 1 0 0 0-1-1h-8a1 1 0 0 0-1 1v8h-7zM6 20h4v22H6z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M21 13h2v2a1 1 0 0 0 2 0v-2h2a1 1 0 0 0 0-2h-2V9a1 1 0 1 0-2 0v2h-2a1 1 0 0 0 0 2zM17 25h2a1 1 0 0 0 0-2h-2a1 1 0 0 0 0 2zM20 29a1 1 0 0 0-1-1h-2a1 1 0 0 0 0 2h2a1 1 0 0 0 1-1zM23 25h2a1 1 0 0 0 0-2h-2a1 1 0 0 0 0 2zM23 30h2a1 1 0 0 0 0-2h-2a1 1 0 0 0 0 2zM29 25h2a1 1 0 0 0 0-2h-2a1 1 0 0 0 0 2zM29 30h2a1 1 0 0 0 0-2h-2a1 1 0 0 0 0 2z" fill="#000000" opacity="1" data-original="#000000"></path></g></svg>
                        </div>
                        <h4>Hospitality & Healthcare Uniforms</h4>
                        <p>Focused on hygiene, functionality, and a polished, service-oriented look.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-text-sec pt-50">
    <div class="container">
        <div class="about-text-inner">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="certificate-left">
                        <h2>Certifications & Compliance</h2>
                        <p>Backed by the standards and governance of Mafatlal Industries Limited, our manufacturing practices follow stringent quality, safety, and compliance norms. We are committed to responsible manufacturing, ethical sourcing, and processes aligned with industry and regulatory requirements.</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="certificate-right">
                        <img src="https://kidzz.in/mafatlal/public/uploads/blog-images/1766048177_6943c1b1eb953.jpg" class="w-100 img-fluid" alt="testd">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="industries-sec pt-0">
    <div class="container">
        <div class="mb-20 text-center section-header">
            <h2 class="mb-0 title slideInUp" data-animation="slideInUp" data-animated="true">
                Why Work With Us
            </h2>
            <p>Recognised among established uniform manufacturers in India, Mafatlal Uniforms serves diverse sectors with tailored solutions</p>
        </div>
        <div class="Industries-inner pt-3">
            <div class="row">
                <div class="col-lg-4 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 500 500" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path fill-rule="evenodd" d="M391.36 325.89c6.757 1.07 14.055 2.227 22.542.643-14.688 15.213-33.069 16.359-48.327.616 9.73-3.778 17.298-2.589 25.785-1.259zm70.166-90.052v-8.348a6.47 6.47 0 0 0-4.487-4.373c-2.757-.768-5.73.443-7.19 2.941-13.081 22.323-27.443 17.438-44.651 26.877 2.54-11.26 4.054-23.196 4.541-35.678 41.604-.554 56.279-37.481 39.894-74.972-2.621-5.996-11.519-4.713-12.271 1.762-2.848 24.53-17.045 26.763-28.704 40.608-1.027-10.125-2.649-20.493-4.865-31.05 5.946-11.417 7.892-26.304 5.298-41.31-2.757-16.028-10.325-30.202-20.704-38.894-3.023-2.52-7.644-1.758-9.622 1.654-6.919 11.666-9.352 27.558-6.595 43.576 2.595 15.125 9.514 28.596 19.028 37.37 8 37.932 16.217 117.19-39.624 163.815-.216.152-.378.324-.595.514-10.595 8.714-23.407 16.271-38.975 22.174-21.78 8.267-42.15 12.618-61.895 27.202-19.913-14.708-39.955-18.859-61.896-27.202-15.514-5.903-28.38-13.46-38.975-22.174a3.71 3.71 0 0 0-.595-.524C92.856 273.19 101.019 193.931 109.073 156c9.514-8.774 16.379-22.245 18.974-37.37 2.757-16.017.378-31.91-6.541-43.576-2.022-3.39-6.626-4.193-9.622-1.654-10.433 8.693-18.001 22.866-20.758 38.894-2.541 15.006-.595 29.894 5.297 41.31-2.216 10.558-3.838 20.926-4.811 31.05-12.141-14.195-25.596-15.831-28.705-40.608-.812-6.47-9.709-7.763-12.271-1.762-9.352 21.812-8.757 42.673 1.676 57.236 8.163 11.352 21.569 17.515 38.218 17.736.432 12.482 1.946 24.418 4.487 35.678-17.259-9.467-31.573-4.582-44.597-26.877-3.275-5.606-12.022-3.276-11.947 3.276.108 23.742 8.974 42.624 24.38 51.814 12.859 7.698 27.835 6.953 41.894 1.141 4.811 10.817 10.865 20.661 18.001 29.51-19.051-.558-29.331 9.086-50.327-5.114-5.428-3.671-12.199 2.235-9.406 8.081 18.065 37.821 55.303 49.841 82.815 19.212 12.472 9.36 24.446 15.433 40.165 21.379-4 1.681-7.568 3.703-10.919 5.622-9.568 5.433-17.839 10.13-34.597 6.06-6.309-1.537-10.686 6.349-5.946 10.866 16.004 15.249 35.663 23.452 54.219 18.439 13.406-3.622 23.839-13.941 29.894-29.331 7.406 3.011 14.595 6.541 21.461 11.287-10.973 11.065-18.433 25.136-22.271 41.954-.897 4.015 2.21 7.838 6.271 7.838 2.919 0 5.568-2.027 6.271-5.006 3.352-14.882 10.001-27.185 19.731-36.667 9.784 9.482 16.433 21.785 19.785 36.667.782 3.528 4.252 5.626 7.676 4.838 3.406-.773 5.622-4.211 4.811-7.671-3.784-16.817-11.298-30.888-22.271-41.954 6.919-4.746 14.055-8.276 21.461-11.287 7.571 19.246 22.215 30.796 41.029 30.796 14.325 0 29.515-6.919 43.084-19.904 4.737-4.513.458-12.408-5.892-10.866-16.812 4.06-25.083-.627-34.597-6.06-3.405-1.919-6.973-3.941-10.973-5.622 14.635-5.169 28.898-12.884 40.218-21.379 10.979 12.288 24.383 18.522 38.543 17.163 17.839-1.719 33.948-14.979 44.219-36.375 2.903-5.95-4.03-11.721-9.352-8.081-20.7 14.158-31.515 4.567-50.381 5.114 7.19-8.849 13.19-18.693 18.055-29.51 34.774 14.487 62.972-7.31 66.002-46.449zM386.224 89.666c10.325 13.687 14.055 35.224 8.865 51.592-10.378-13.693-14.108-35.234-8.865-51.592zm41.57 94.497c5.297-4.406 10.919-9.141 15.352-16.563 1.786 20.995-9.303 35.657-31.353 36.786 3.568-9.822 9.407-14.714 16.001-20.223zm-2.703 74.891c6.595-1.941 13.676-4.033 20.65-9.092-6.823 20.087-22.806 28.983-43.408 21.25 7.19-7.59 14.488-9.742 22.758-12.158zm-85.356 121.629c-19.136 9.089-36.664 3.732-45.462-16.45 19.631 0 26.387 13.072 45.462 16.45zm-154.333 3.281c-7.568 2.049-16.217.827-24.866-3.281 19.063-3.376 25.738-16.45 45.408-16.45-4.595 10.541-11.568 17.309-20.542 19.731zm-76.923-45.592c-7.838-.752-15.46-4.952-22.163-11.838 18.883 3.523 30.519-6.3 48.327.616-8.001 8.254-16.921 12.109-26.164 11.222zm-39.03-68.236c-6.757-4.022-11.892-11.103-14.92-20.174 15.572 11.298 30.183 7.346 43.354 21.25-10.757 4.037-20.487 3.697-28.434-1.076zm-6.703-78.086c-4.541-6.384-6.433-14.92-5.622-24.45 9.75 16.527 24.767 18.804 31.299 36.786-11.46-.589-20.271-4.784-25.677-12.336zm51.301-102.384c5.189 16.358 1.459 37.899-8.865 51.592-5.19-16.369-1.514-37.905 8.865-51.592zm149.522 185.086c.324 7.033 2.271 13.417 5.352 18.569h-104.98c-6.649 0-12.271-8.298-13.028-18.569zm-73.68-144.981c0-13.855 6.919-23.515 13.082-23.515h105.033c-3.46 5.692-5.46 12.914-5.46 20.839v42.008l-66.004 59.993v.005l-20.542 18.66c-4.338 3.927-1.539 11.163 4.325 11.163 1.514 0 3.027-.546 4.27-1.665l18.055-16.39c6.919 1.573 13.19 2.389 18.974 2.389 14.271 0 25.785-4.979 38.002-15.677l2.919-2.779V269.8c0 13.866-6.919 23.521-13.082 23.521-7.136 0-13.136-9.547-13.136-20.844v-4.135c0-3.552-2.865-6.417-6.379-6.417h-80.059V129.771zm138.602-23.515c6.649 0 12.271 8.292 13.028 18.574h-26.056c.757-10.282 6.433-18.574 13.028-18.574zm26.488 51.592-63.895 60.16c-11.785 10.282-21.731 13.839-36.489 12.006l61.463-55.852c12.92-9.778 25.731-15.173 38.921-16.314zM163.941 306.149c-14.541 0-25.947-14.785-25.947-33.672v-4.135a6.41 6.41 0 0 1 6.433-6.417h32.65V129.771c0-20.379 11.352-36.343 25.893-36.343h125.521c14.541 0 25.947 14.795 25.947 33.667v4.146a6.417 6.417 0 0 1-6.433 6.417h-32.651v21.331c18.163-11.693 36.975-16.185 56.165-13.352 5.305.786 7.271 7.422 3.46 11.017l-59.625 56.073V269.8c0 20.38-11.352 36.348-25.893 36.348h-125.52z" clip-rule="evenodd" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                        <p>Backed by the legacy and credibility of Mafatlal Industries Limited</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 661.333 661" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M10 248.832h55.477a148.72 148.72 0 0 0 14.085 33.883l-39.265 39.25a10.002 10.002 0 0 0 0 14.148l42.422 42.434c3.906 3.898 10.234 3.898 14.14 0l39.27-39.274A148.62 148.62 0 0 0 170 353.355v55.477c0 5.523 4.477 10 10 10h60c5.523 0 10-4.477 10-10v-55.477a148.62 148.62 0 0 0 33.871-14.082l39.27 39.274c3.968 3.75 10.175 3.75 14.14 0l42.43-42.434a10.002 10.002 0 0 0 0-14.148l-39.274-39.25a148.72 148.72 0 0 0 14.086-33.883H410c5.523 0 10-4.477 10-10v-60c0-5.52-4.477-10-10-10h-55.477a148.764 148.764 0 0 0-14.086-33.879l39.266-39.25a10.002 10.002 0 0 0 0-14.148L337.281 39.12c-3.906-3.902-10.234-3.902-14.14 0l-39.27 39.274A149.046 149.046 0 0 0 250 64.312V8.832c0-5.52-4.477-10-10-10h-60c-5.523 0-10 4.48-10 10v55.48a149.046 149.046 0 0 0-33.871 14.083L96.859 39.12c-3.968-3.75-10.175-3.75-14.14 0l-42.43 42.434a10.002 10.002 0 0 0 0 14.148l39.273 39.25a148.764 148.764 0 0 0-14.085 33.879H10c-5.523 0-10 4.48-10 10v60c0 5.523 4.477 10 10 10zm10-60h53.3c4.7 0 8.766-3.27 9.77-7.86a128.657 128.657 0 0 1 17.52-42.14 10.002 10.002 0 0 0-1.352-12.469L61.5 88.625l28.29-28.293 37.73 37.738A10.003 10.003 0 0 0 140 99.426a128.86 128.86 0 0 1 42.14-17.52 10.01 10.01 0 0 0 7.86-9.773V18.832h40v53.3c0 4.7 3.273 8.766 7.86 9.774A128.86 128.86 0 0 1 280 99.426a10.003 10.003 0 0 0 12.48-1.356l37.73-37.738 28.282 28.293-37.73 37.727a10.003 10.003 0 0 0-1.352 12.48 128.623 128.623 0 0 1 17.508 42.148 10.008 10.008 0 0 0 9.77 7.864H400v40h-53.313c-4.695 0-8.761 3.27-9.769 7.86a128.501 128.501 0 0 1-17.508 42.128 10.007 10.007 0 0 0 1.352 12.473l37.738 37.73-28.29 28.3-37.73-37.741a10.007 10.007 0 0 0-12.48-1.352 128.561 128.561 0 0 1-42.129 17.52c-4.594 1-7.871 5.07-7.871 9.77v53.3h-40v-53.3c0-4.7-3.277-8.77-7.871-9.77A128.561 128.561 0 0 1 140 318.242a10.007 10.007 0 0 0-12.48 1.352l-37.73 37.742-28.282-28.293 37.73-37.73a10.002 10.002 0 0 0 1.352-12.481 128.697 128.697 0 0 1-17.52-42.148 9.995 9.995 0 0 0-9.77-7.852H20zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M210 288.832c44.184 0 80-35.812 80-80 0-44.184-35.816-80-80-80s-80 35.816-80 80c.047 44.16 35.84 79.953 80 80zm0-140c33.133 0 60 26.867 60 60 0 33.137-26.867 60-60 60s-60-26.863-60-60c.035-33.125 26.875-59.965 60-60zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M210 318.832c60.75 0 110-49.246 110-110 0-60.75-49.25-110-110-110s-110 49.25-110 110c.074 60.723 49.277 109.93 110 110zm0-200c49.707 0 90 40.293 90 90s-40.293 90-90 90-90-40.293-90-90c.055-49.68 40.316-89.945 90-90zM570 458.832v-80c0-5.52-4.477-10-10-10h-20v-80c0-5.52-4.477-10-10-10h-90c-5.523 0-10 4.48-10 10v80h-50c-5.523 0-10 4.48-10 10v80H50c-27.602.035-49.965 22.398-50 50v20c.035 27.605 22.398 49.969 50 50h520c27.602-.031 49.965-22.395 50-50v-20c-.035-27.602-22.398-49.965-50-50zm-20 0h-70v-70h70zm-100-160h70v70h-70zm-60 90h70v70h-70zm210 140c0 16.57-13.434 30-30 30H50c-16.566 0-30-13.43-30-30v-20c0-16.566 13.434-30 30-30h520c16.566 0 30 13.434 30 30zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path><path d="M310 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zM210 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zM410 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zM110 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zM510 488.832c-16.566 0-30 13.434-30 30 0 16.57 13.434 30 30 30s30-13.43 30-30c0-16.566-13.434-30-30-30zm0 40c-5.523 0-10-4.477-10-10 0-5.52 4.477-10 10-10s10 4.48 10 10c0 5.523-4.477 10-10 10zm0 0" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                        <p>Proven expertise as reliable uniform manufacturers and suppliers in India</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 512 512" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="M168.25 409.69c-26.33-15.08-48.5-36.79-64.1-62.79-14.96-24.95-23.53-53.54-24.85-82.88h16.24c5.49 0 10.39-3.29 12.48-8.34 2.13-5.08.99-10.88-2.91-14.78l-40.99-40.96c-5.27-5.27-13.85-5.27-19.13 0l-41 40.96c-3.9 3.9-5.04 9.7-2.93 14.74 2.1 5.09 7 8.37 12.49 8.37H30.1c1.33 38.07 12.36 75.38 31.96 108.06 20.05 33.41 48.56 61.26 82.47 80.55 2.08 1.17 4.3 1.76 6.57 1.76 1.29 0 2.6-.19 3.9-.58 3.49-1.02 6.37-3.35 8.13-6.6l10.36-19.45c3.42-6.46 1.12-14.39-5.24-18.06zm-18.03 27.76c-30.54-17.86-56.24-43.26-74.45-73.6-18.6-31-28.89-66.49-29.76-102.67-.21-7.38-6.15-13.15-13.52-13.15H19.48l35.07-35.04 35.07 35.04H76.74c-3.79 0-7.15 1.43-9.73 4.13-2.58 2.66-3.92 6.16-3.79 9.81 1 32.92 10.41 65.14 27.21 93.16 16.6 27.67 40.02 50.89 67.81 67.26zM64.81 163.12l19.37 10.33c2.05 1.1 4.24 1.63 6.41 1.62 4.67 0 9.19-2.44 11.74-6.82 15.05-26.32 36.77-48.48 62.79-64.1 25.02-15 53.66-23.58 82.88-24.88v16.24c0 5.49 3.29 10.39 8.33 12.47 5.07 2.13 10.86.98 14.75-2.91l40.99-40.99c5.27-5.27 5.27-13.85 0-19.13L271.1 3.98c-3.89-3.89-9.68-5.04-14.72-2.93-5.08 2.1-8.37 7-8.37 12.49v16.54c-38.07 1.35-75.39 12.39-108.09 31.99-33.4 20.03-61.24 48.54-80.49 82.41-1.84 3.19-2.28 6.91-1.23 10.55 1.07 3.53 3.34 6.32 6.61 8.09zm83.34-87.33C179.16 57.2 214.67 46.9 250.77 46c7.29-.14 13.23-6.2 13.23-13.52V19.49l35.04 35.04L264 89.58V76.73c0-3.68-1.45-7.13-4.09-9.69-2.64-2.57-6.01-3.88-9.85-3.82-32.89 1-65.1 10.42-93.16 27.24-27.7 16.62-50.92 40.05-67.25 67.8l-15.06-8.04c17.85-30.55 43.24-56.25 73.56-74.43zm299.04 273.1-19.37-10.33c-6.39-3.43-14.52-1.1-18.12 5.2-15.04 26.3-36.77 48.46-62.83 64.1-24.97 15-53.61 23.58-82.88 24.88V416.5c0-5.46-3.26-10.36-8.34-12.49-5.08-2.1-10.87-.95-14.74 2.93l-40.99 40.99c-5.27 5.27-5.27 13.86 0 19.13l40.99 40.99c2.59 2.59 6.02 3.96 9.54 3.96 1.76 0 3.54-.34 5.24-1.05 5.05-2.11 8.31-7.01 8.31-12.48v-16.54c38.1-1.35 75.42-12.39 108.09-31.99 33.44-20.06 61.27-48.56 80.52-82.45 1.8-3.2 2.24-6.9 1.24-10.38-1.03-3.62-3.33-6.47-6.66-8.23zm-194.97 69.34-.01.01zm111.64 17.99c-30.99 18.59-66.48 28.89-102.59 29.79-7.31.14-13.26 6.2-13.26 13.52v12.99l-35.04-35.04 35.04-35.04v12.85c0 3.68 1.45 7.13 4.09 9.69l.07.07c2.66 2.52 6.01 3.85 9.77 3.75 32.94-1 65.16-10.42 93.16-27.24 27.71-16.63 50.92-40.03 67.27-67.79l15.06 8.04c-17.82 30.51-43.21 56.2-73.57 74.41zm147.1-179.89a13.509 13.509 0 0 0-12.5-8.31h-16.54c-1.32-38.08-12.35-75.4-31.96-108.08-20.02-33.41-48.53-61.25-82.45-80.51-3.17-1.81-6.86-2.26-10.44-1.24-3.58 1.03-6.49 3.4-8.16 6.61l-10.34 19.4c-3.45 6.42-1.1 14.56 5.2 18.12 26.29 15.04 48.46 36.75 64.1 62.79 14.99 25 23.56 53.59 24.85 82.91H416.5c-5.48 0-10.39 3.26-12.54 8.39a13.546 13.546 0 0 0 2.97 14.69l40.96 40.96c2.64 2.64 6.1 3.96 9.56 3.96s6.93-1.32 9.56-3.95L508 271.11c3.88-3.86 5.04-9.63 2.96-14.78zm-53.5 42.7-35.01-35.01h12.82c3.71 0 7.17-1.48 9.73-4.15 2.58-2.67 3.92-6.16 3.79-9.77-.97-32.9-10.38-65.12-27.22-93.2-16.63-27.69-40.02-50.88-67.79-67.24l8.02-15.05c30.56 17.85 56.26 43.24 74.44 73.57 18.6 31 28.89 66.49 29.76 102.67.21 7.27 6.27 13.18 13.52 13.18h12.99zm-90.54-43.01c0-61.18-49.76-110.95-110.92-110.95s-110.92 49.77-110.92 110.95c0 61.16 49.76 110.92 110.92 110.92s110.92-49.76 110.92-110.92zm-205.85 0c0-52.36 42.59-94.96 94.93-94.96s94.93 42.6 94.93 94.96c0 52.35-42.59 94.93-94.93 94.93s-94.93-42.58-94.93-94.93zm148.41-22.52-56.35 56.32c-1.56 1.56-3.61 2.34-5.65 2.34s-4.09-.78-5.65-2.34l-39.29-39.26a7.991 7.991 0 0 1 11.3-11.3l33.64 33.61 50.7-50.68a7.991 7.991 0 0 1 11.3 0c3.12 3.13 3.12 8.19 0 11.31z" fill="#000000" opacity="1" data-original="#000000"></path></g></svg>
                        </div>
                        <p>End-to-end solutions from fabric to finished garment</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 510 510" style="enable-background:new 0 0 512 512" xml:space="preserve" fill-rule="evenodd" class=""><g><path d="M446.259 487h32.088c4.415 0 8 3.585 8 8s-3.585 8-8 8H31.653c-4.415 0-8-3.585-8-8s3.585-8 8-8h32.088V307.44h.027a17.565 17.565 0 0 1-.027-.974c0-6.698 3.779-13.269 11.184-18.29 8.262-5.602 21.537-9.473 36.567-9.473s28.305 3.871 36.567 9.473c7.405 5.021 11.184 11.592 11.184 18.29 0 .325-.009.65-.027.974h.027V487h48.006V220.228c0-6.697 3.779-13.269 11.184-18.29 8.262-5.602 21.537-9.472 36.567-9.472s28.305 3.87 36.567 9.472c7.405 5.021 11.184 11.593 11.184 18.29V487h48.006V117.931c0-6.697 3.779-13.268 11.184-18.289 8.262-5.602 21.537-9.473 36.567-9.473s28.305 3.871 36.567 9.473c7.405 5.021 11.184 11.592 11.184 18.289zm-366.518 0h63.502V327.543c-8.178 4.058-19.369 6.685-31.751 6.685s-23.573-2.627-31.751-6.685zm143.508 0h63.502V241.306c-8.178 4.057-19.369 6.684-31.751 6.684s-23.573-2.627-31.751-6.684zm143.508 0h63.502V139.009c-8.178 4.058-19.369 6.685-31.751 6.685s-23.573-2.627-31.751-6.685zm-80.006-266.772c0-2.044-1.903-3.515-4.163-5.047-6.211-4.211-16.29-6.715-27.588-6.715s-21.377 2.504-27.588 6.715c-2.26 1.532-4.163 3.003-4.163 5.047s1.903 3.514 4.163 5.047c6.211 4.211 16.29 6.715 27.588 6.715s21.377-2.504 27.588-6.715c2.26-1.533 4.163-3.003 4.163-5.047zm143.508-102.297c0-2.043-1.903-3.514-4.163-5.046-6.211-4.211-16.29-6.716-27.588-6.716s-21.377 2.505-27.588 6.716c-2.259 1.532-4.163 3.003-4.163 5.046 0 2.044 1.904 3.515 4.163 5.047 6.211 4.211 16.29 6.716 27.588 6.716s21.377-2.505 27.588-6.716c2.26-1.532 4.163-3.003 4.163-5.047zM111.492 294.703c-11.298 0-21.377 2.505-27.588 6.716-2.26 1.532-4.163 3.003-4.163 5.047s1.903 3.514 4.163 5.046c6.211 4.211 16.29 6.716 27.588 6.716s21.377-2.505 27.588-6.716c2.259-1.532 4.163-3.002 4.163-5.046s-1.904-3.515-4.163-5.047c-6.211-4.211-16.29-6.716-27.588-6.716zM404.37 35.431C306.148 119.3 198.667 172.279 97.025 206.523c-4.184 1.41-8.726-.843-10.136-5.027s.843-8.726 5.027-10.135c99.5-33.523 204.718-85.273 300.941-167.14l-15.374.82a8.004 8.004 0 0 1-8.414-7.563 8.003 8.003 0 0 1 7.562-8.415l38.472-2.052a7.998 7.998 0 0 1 8.338 9.178l-5.55 36.933a8.003 8.003 0 0 1-9.1 6.722 8.003 8.003 0 0 1-6.722-9.099z" fill="#000000" opacity="1" data-original="#000000" class=""></path></g></svg>
                        </div>
                        <p>Scalable production for large institutions and growing organisations</p>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 industries-box-outer">
                    <div class="industries-box">
                        <div class="industries-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" width="60" height="60" x="0" y="0" viewBox="0 0 66 66" style="enable-background:new 0 0 512 512" xml:space="preserve" class=""><g><path d="m65.25 39.03-7.5-18.53c-.23-.57-.66-1.01-1.23-1.25-.57-.23-1.2-.22-1.76.03l-5.84 2.6c-.99.42-1.52 1.5-1.31 2.53l-1.55.87c-.52.3-1.15.41-1.76.33l-10.77-1.59a6.22 6.22 0 0 0-5.17 1.66l-.79.76h-5.98c-.27 0-.54-.1-.74-.28l-.53-.45c.37-.91.12-1.97-.68-2.6l-4.52-3.57c-.48-.38-1.08-.55-1.69-.46-.6.08-1.15.41-1.51.89L1.03 34.73c-.72.97-.55 2.33.41 3.1l4.51 3.56a2.234 2.234 0 0 0 1.7.46 2.2 2.2 0 0 0 1.5-.9l.04-.05.65.49c.38.27.6.73.6 1.21v2.69l-1.68 2.16c-1.5 1.95-1.16 4.76.78 6.26.79.62 1.75.95 2.74.95.18 0 .37-.01.55-.03.2-.03.39-.08.58-.13 0 .06-.01.11-.01.17 0 1.28.58 2.48 1.58 3.3.78.62 1.72.93 2.65.93.58 0 1.16-.13 1.7-.36v.05c.14 1.05.69 1.98 1.49 2.57l.18.14a3.915 3.915 0 0 0 2.9.73c.43-.06.82-.21 1.2-.4.21 1.07.82 1.99 1.73 2.57l.13.09a3.908 3.908 0 0 0 2.99.56c1.05-.22 1.94-.84 2.53-1.74l.17-.26c.71.54 1.56.82 2.47.82 1.07 0 2.12-.44 2.88-1.19a4.094 4.094 0 0 0 1.17-3.38 3.864 3.864 0 0 0 4.35-4.56c1.33.08 2.64-.45 3.54-1.52a4.277 4.277 0 0 0 .94-3.55c1.21.13 2.44-.29 3.34-1.17.99-.99 1.36-2.39 1.09-3.71.05-.01.1 0 .15-.01l3.22-.45c.41.37.96.58 1.51.58.31 0 .62-.06.91-.2l5.86-2.59c1.1-.48 1.62-1.76 1.17-2.89zM7.41 40.15a.513.513 0 0 1-.4-.11l-4.49-3.55a.534.534 0 0 1-.1-.73L13.3 20.97c.11-.15.26-.19.35-.2h.07c.09 0 .21.02.32.11l4.53 3.57c.22.17.26.49.1.7l-.14.19-.03.03-6.26 8.48-.02.02-1.19 1.62-2.73 3.7c-.01.01-.01.02-.01.02l-.54.74c-.1.14-.24.19-.34.2zm5.22 12.77a2.73 2.73 0 0 1-2.02-.56c-1.18-.92-1.4-2.65-.47-3.85l3.22-4.15c.54-.7 1.35-1.06 2.17-1.06.58 0 1.17.19 1.67.57.67.53 1.06 1.32 1.06 2.17 0 .61-.2 1.19-.58 1.69l-3.22 4.15c-.46.58-1.11.95-1.83 1.04zm3.44 3.71c-.6-.49-.94-1.2-.94-1.96 0-.55.19-1.09.56-1.56l3.21-4.14c.41-.54 1.01-.87 1.68-.95.1-.02.2-.02.29-.02.57 0 1.13.2 1.65.62 1.08.87 1.25 2.46.38 3.54l-3.29 4.1a2.549 2.549 0 0 1-3.54.37zm7.57 3.69c-.58.08-1.15-.06-1.56-.36l-.17-.14a2.14 2.14 0 0 1-.86-1.46 2.2 2.2 0 0 1 .46-1.66l2.44-3.02a2.17 2.17 0 0 1 1.7-.81c.45 0 .91.14 1.29.43.55.41.87 1.05.87 1.74 0 .45-.13.87-.38 1.23-.02.02-.03.05-.05.08-.01.01-.01.02-.02.02l-.04.06-2.27 3.04c-.32.47-.83.77-1.41.85zm8.53.07-1.15 1.79c-.33.51-.84.86-1.44.99-.59.12-1.19.01-1.68-.31l-.13-.09c-.53-.34-.88-.87-.99-1.48s.02-1.22.39-1.73c.6-.79 1.28-1.67 1.55-2 .05-.05.1-.11.15-.15.01 0 .01-.01.01-.01.01-.01.03-.02.04-.03.05-.04.09-.08.17-.13.75-.45 1.69-.45 2.42.02.64.41 1.03 1.13 1.03 1.91-.01.44-.13.86-.37 1.22zm17.95-13.31c-.82.81-2.15.91-3.07.24l-5.75-4.25-2.24-1.66a.874.874 0 0 0-1.21.18c-.28.38-.2.92.18 1.21l3.92 2.9 3.32 2.46c.58.43.95 1.06 1.04 1.78s-.12 1.42-.59 1.98a2.598 2.598 0 0 1-3.47.44l-6.89-4.93a.852.852 0 0 0-1.2.2c-.28.39-.19.93.2 1.2l6.59 4.71c.51.37.83.94.89 1.56.05.63-.17 1.24-.62 1.69-.75.76-1.96.83-2.81.19l-6.5-5.06a.86.86 0 0 0-1.21.15c-.29.37-.23.92.14 1.21l5.68 4.44c.54.42.88 1.05.92 1.73.05.69-.2 1.34-.69 1.83-.8.8-2.15.89-3.05.24 0 0-.05-.06-.12-.13l.02-.03c.42-.64.64-1.39.64-2.18 0-1.37-.68-2.62-1.82-3.36-.87-.56-1.91-.73-2.9-.55 0-.07.01-.14.01-.21 0-1.25-.57-2.39-1.56-3.13-.86-.64-1.89-.88-2.87-.74.2-1.44-.33-2.95-1.58-3.96a4.2 4.2 0 0 0-3.16-.92c-.15.02-.29.06-.43.09.01-.12.03-.23.03-.35 0-1.39-.62-2.67-1.72-3.53-1.65-1.28-3.92-1.21-5.5.03 0-.01 0-.01.01-.02-.02.02-.04.04-.06.05-.1.08-.2.18-.29.27-.08.08-.17.15-.25.24v-.48c0-1.04-.49-2.01-1.3-2.6l-.66-.49 1.26-1.71 1.59-2.16.5-.67c.03-.03.05-.06.08-.09l5.71-7.75.37.31c.51.45 1.18.7 1.87.7h4.18l-2.4 2.3c-.56.54-.87 1.27-.87 2.05 0 1.12.62 2.11 1.63 2.58a8.696 8.696 0 0 0 7.78-.15l1.04-.54c.98-.52 2.18-.4 3.05.3l10.15 8.13c.59.46 1.21.84 1.85 1.12.21.09.44.18.64.25.18.07.37.13.53.17.22.07.44.12.63.15.26.05.51.09.72.1.1.01.19.01.29.02l-.11.03c.28.83.08 1.78-.56 2.42zm2.22-4.2c-.33.04-.69.06-1.1.05-.17 0-.33-.01-.56-.04-.2-.01-.38-.05-.6-.09a2.51 2.51 0 0 1-.46-.11c-.14-.03-.27-.08-.4-.13-.18-.06-.37-.14-.54-.21-.5-.22-1-.52-1.47-.89l-9.63-7.72-.52-.41c-.82-.66-1.82-1-2.83-1-.72 0-1.44.17-2.09.52l-1.04.54a6.938 6.938 0 0 1-6.23.12c-.47-.22-.64-.67-.64-1.02 0-.21.06-.53.34-.8l4.99-4.78c.98-.94 2.37-1.39 3.72-1.19l10.78 1.59c.99.14 2.01-.05 2.85-.53l1.32-.74 2.12 5.24 4.56 11.24zm11.04-2.52-5.87 2.59c-.23.1-.51.02-.68-.27l-6.24-15.41-.81-1.98-.46-1.14c-.11-.26.01-.56.28-.68l5.86-2.6c.13-.06.28-.06.41-.01s.23.15.28.29l7.5 18.54c.1.25-.02.55-.27.67z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M33 21.62c5.67 0 10.28-4.61 10.28-10.28S38.67 1.05 33 1.05 22.71 5.66 22.71 11.34c.01 5.67 4.62 10.28 10.29 10.28zm0-18.84c4.72 0 8.55 3.84 8.55 8.56s-3.84 8.55-8.55 8.55c-4.72 0-8.56-3.84-8.56-8.55 0-4.72 3.84-8.56 8.56-8.56z" fill="#000000" opacity="1" data-original="#000000"></path><path d="M30.9 14.47c.16.16.38.25.61.25s.45-.09.61-.25l5.03-5.03c.34-.34.34-.88 0-1.22s-.88-.34-1.22 0l-4.42 4.42-1.43-1.43c-.34-.34-.88-.34-1.22 0s-.34.88 0 1.22z" fill="#000000" opacity="1" data-original="#000000"></path></g></svg>
                        </div>
                        <p>Consistent quality, long-term partnerships, and dependable service</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="about-text-sec pt-60">
    <div class="container">
        <div class="mb-20 text-center section-header">
            <h2 class="mb-0 title slideInUp" data-animation="slideInUp" data-animated="true">
                Leadership & The Road Ahead
            </h2>
        </div>
        <div class="about-text-inner">
            <div class="about-box">
                <h2></h2>
                <p>Under the leadership of Mr. Priyavrata Mafatlal, the fifth-generation torchbearer of the Mafatlal family, Mafatlal Uniforms continues to grow with a future-focused mindset while staying true to its core values. With a strong foundation and a commitment to continuous improvement, we remain focused on innovation, scalability, and long-term partnerships—carrying forward a legacy that continues to dress generations.</p>
            </div>
        </div>
    </div>
</section>

@endsection





