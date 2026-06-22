<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- owl carousel slider -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <!-- slick slider -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v7.1.0/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/rs-spacing-min.css') }}">
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/responsive.css') }}">
    <style>
        .login-body {
            width: 100%;
            min-height: 100dvh;
            position: relative;
        }
        .login-body-inner {
            width: 100%;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            position: relative;
        }
        .login-body-left {
            width: 50%;
            height: 100%;
            min-height: 100dvh;
            padding: 80px;
            background-image: url(images/login-bg.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            background-position: top;
            position: relative;
            display: flex;
            align-items: center;
        }
        .login-body-left::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #00000099;
        }
        .login-info {
            position: relative;
        }
        .login-body-right {
            width: 50%;
            height: 100%;
            background: #fff;
            padding: 40px 125px;
            display: flex;
            align-items: center;
        }
        .login-form-sec {
            width: 100%;
        }
        .login-info h2 {
            font-size: 33px;
            color: #fff;
            margin-bottom: 18px;
        }
        .login-info p {
            font-size: 15px;
            color: #fff;
            margin-bottom: 20px;
        }
        .follow-box p {
            margin-bottom: 8px;
        }
        .follow-box ul {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .follow-box ul li a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            font-size: 10px;
            color: #fff;
            border: 1px solid #fff;
            border-radius: 50%;
        }
        .follow-box ul li a i {
            font-size: 15px;
        }
        .top-area {
            text-align: center;
            margin-bottom: 16px;
        }
        .top-area a {
            display: inline-block;
            margin-bottom: 18px;
        }
        .top-area h2 {
            font-size: 26px;
            margin-bottom: 7px;
        }
        .top-area p {
            font-size: 15px;
        }
        .fotm-input-box {
            position: relative;
            margin-bottom: 15px;
        }
        .fotm-input-box i {
            position: absolute;
            left: 10px;
            top: 13px;
            font-size: 18px;
        }
        .fotm-input-box input {
            width: 100%;
            border-radius: 5px;
            outline: none;
            border: 2px solid #dee2e6;
            padding: 10px;
            padding-left: 42px;
        }
        .remind-sec {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }
        .remind-left {
            display: flex;
            align-items: center;
        }
        .remind-left input {
            display: block;
            width: 18px;
            height: 18px;
            margin-right: 8px;
            border-radius: 3px;
        }
        .remind-left label {
            color: #596477;
            font-size: 15px;
        }
        .remind-right a {
            color: #596477;
        }
        .sign-btn {
            width: 100%;
            background: #0356CB;
            color: #fff;
            border: 0;
            border-radius: 5px;
            font-size: 18px;
            padding: 9px 0;
            margin-bottom: 30px;
        }
        .new-text p {
            text-align: center;
        }
        .new-text p a {
            color: #596477;
        }

        @media(max-width: 991px) {
            .login-body-left {
                display: none;
            }
            .login-body-right {
                width: 100%;
            }
            .login-body-right {
                padding: 40px 160px;
            }
        }

        @media(max-width: 575px) {
            .login-body-right {
                padding: 40px 100px;
            }
        }

        @media(max-width: 480px) {
            .login-body-right {
                padding: 40px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-body">
        <div class="login-body-outer">
            <div class="login-body-inner">
                <div class="login-body-left">
                    <div class="login-info">
                        <h2>Trust the Degital product Experts at v-Shop</h2>
                        <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Provident magni blanditiis nam hic, tempore reprehenderit ipsum maxime beatae repudiandae ratione.</p>
                        <div class="follow-box">
                            <p>Follow us on</p>
                            <ul>
                                <li>
                                    <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa-brands fa-youtube"></i></a>
                                </li>
                                <li>
                                    <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="login-body-right">
                    <div class="login-form-sec">
                        <div class="top-area">
                            <a href="#">
                                <img src="images/logo.png" alt="">
                            </a>
                            <h2>Sign Up</h2>
                            <p>Enter details to create your account</p>
                        </div>
                        <div class="bottom-area">
                          <form method="POST" action="{{ route('customer.register') }}">
                    @csrf

                    <div class="fotm-input-box">
                        <i class="fa-regular fa-user"></i>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="Full Name">
                    </div>

                    <div class="fotm-input-box">
                        <i class="fa-regular fa-phone"></i>
                        <input type="text" name="phone" value="{{ old('phone') }}" placeholder="Phone Number">
                    </div>

                    <div class="fotm-input-box">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Email Address">
                    </div>

                    <div class="fotm-input-box">
                        <i class="fa-regular fa-lock"></i>
                        <input type="password" name="password" placeholder="Password">
                    </div>

                    <div class="fotm-input-box">
                        <i class="fa-regular fa-lock"></i>
                        <input type="password" name="password_confirmation" placeholder="Confirm Password">
                    </div>

                    <div class="fotm-input-box">
                        <i class="fa-regular fa-gift"></i>
                        <input type="text" name="referral_code" value="{{ request()->query('ref') }}" placeholder="Referral Code (Optional)">
                    </div>

                    <button class="sign-btn">Create Account</button>

                    <div class="new-text">
                        <p>Already have an account?
                            <a href="{{ route('employeelogin') }}">Sign In</a>
                        </p>
                    </div>
                </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

 <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="{{ asset('vtravelassets/js/main.js') }}"></script>
</body>

</html>