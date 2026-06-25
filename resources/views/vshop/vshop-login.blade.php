<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSHOP Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; }
        .login-body {
            width: 100%;
            height: 100dvh;
            position: relative;
        }
        .login-body-inner {
            width: 100%;
            height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
        }
        .login-body-left {
            width: 50%;
            height: 100%;
            padding: 80px;
            background-image: url(https://img.freepik.com/free-photo/copy-space-cosmetic-skin-care-products_23-2148317615.jpg?t=st=1773403401~exp=1773407001~hmac=c55fcfca1d3f05bd276fbc991cfac47f632ceb6f1db723cb5bf047ee2aeb5b4b&w=1060);
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
            top: 0; left: 0;
            width: 100%; height: 100%;
            background-color: #00000099;
        }
        .login-info { position: relative; }
        .login-body-right {
            width: 50%;
            height: 100%;
            background: #fff;
            padding: 40px 125px;
            display: flex;
            align-items: center;
        }
        .login-form-sec { width: 100%; }
        .login-info h2 { font-size: 33px; color: #fff; margin-bottom: 18px; }
        .login-info p { font-size: 15px; color: #fff; margin-bottom: 20px; }
        .follow-box p { margin-bottom: 8px; color: #fff; font-size: 14px; }
        .follow-box ul { display: flex; align-items: center; gap: 5px; list-style: none; padding: 0; }
        .follow-box ul li a {
            display: flex; align-items: center; justify-content: center;
            width: 30px; height: 30px;
            color: #fff; border: 1px solid #fff; border-radius: 50%;
            text-decoration: none; transition: 0.3s;
        }
        .follow-box ul li a:hover { background: #1a73e8; border-color: #1a73e8; }
        .follow-box ul li a i { font-size: 13px; }
        .top-area { text-align: center; margin-bottom: 20px; }
        .top-area a { display: inline-block; margin-bottom: 18px; }
        .top-area h2 { font-size: 26px; margin-bottom: 7px; color: #1a73e8; font-weight: 700; }
        .top-area p { font-size: 15px; color: #666; }
        .fotm-input-box { position: relative; margin-bottom: 15px; }
        .fotm-input-box i { position: absolute; left: 12px; top: 13px; font-size: 16px; color: #888; }
        .fotm-input-box input {
            width: 100%;
            border-radius: 8px;
            outline: none;
            border: 2px solid #dee2e6;
            padding: 11px 12px 11px 42px;
            font-size: 15px;
            transition: border-color 0.3s;
        }
        .fotm-input-box input:focus { border-color: #1a73e8; }
        .remind-sec {
            display: flex; align-items: center;
            justify-content: space-between; margin-bottom: 15px;
        }
        .remind-left { display: flex; align-items: center; }
        .remind-left input { display: block; width: 16px; height: 16px; margin-right: 8px; cursor: pointer; }
        .remind-left label { color: #596477; font-size: 14px; cursor: pointer; }
        .remind-right a { color: #596477; font-size: 14px; text-decoration: none; }
        .remind-right a:hover { color: #1a73e8; }
        .sign-btn {
            width: 100%;
            background: #1a73e8;
            color: #fff;
            border: 0;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 0;
            margin-bottom: 20px;
            transition: 0.3s;
            cursor: pointer;
        }
        .sign-btn:hover { background: #1557b0; }
        .new-text p { text-align: center; font-size: 14px; color: #666; }
        .new-text p a { color: #1a73e8; font-weight: 600; text-decoration: none; }
        .vshop-badge {
            display: inline-block;
            background: #1a73e8;
            color: #fff;
            padding: 5px 15px;
            border-radius: 30px;
            font-size: 13px;
            margin-bottom: 12px;
        }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
        .alert-danger { background: #fde8e8; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-success { background: #e8f4fd; border: 1px solid #bee5eb; color: #0c5460; }

        @media(max-width: 991px) {
            .login-body-left { display: none; }
            .login-body-right { width: 100%; padding: 40px 160px; }
        }
        @media(max-width: 575px) { .login-body-right { padding: 40px 100px; } }
        @media(max-width: 480px) { .login-body-right { padding: 40px 20px; } }
    </style>
</head>

<body>
    <div class="login-body">
        <div class="login-body-outer">
            <div class="login-body-inner">
                <div class="login-body-left">
                    <div class="login-info">
                        <span class="vshop-badge">Welcome to VSHOP</span>
                        <h2>Shop the Best Digital Products at V-Shop</h2>
                        <p>Discover thousands of premium digital products, instant downloads, and exclusive deals. Your one-stop destination for all digital needs.</p>
                        <div class="follow-box">
                            <p>Follow us on</p>
                            <ul>
                                <li><a href="#"><i class="fa-brands fa-facebook-f"></i></a></li>
                                <li><a href="#"><i class="fa-brands fa-x-twitter"></i></a></li>
                                <li><a href="#"><i class="fa-brands fa-youtube"></i></a></li>
                                <li><a href="#"><i class="fa-brands fa-linkedin-in"></i></a></li>
                                <li><a href="#"><i class="fa-brands fa-instagram"></i></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="login-body-right">
                    <div class="login-form-sec">
                        <div class="top-area">
                            <a href="#">
                                <img src="{{ asset('images/new-logo-vridhee.png') }}" alt="Vridhee Logo">
                            </a>
                            <h2>Sign In to VSHOP</h2>
                            <p>Access your account to manage orders and downloads</p>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        <div class="bottom-area">
                            <form action="{{ route('vshop.customer.login') }}" method="POST">
                                @csrf
                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input
                                        type="email"
                                        name="email"
                                        value="{{ old('email', 'sayaniroy.webmantra@gmail.com') }}"
                                        placeholder="Enter your email"
                                        class="@error('email') is-invalid @enderror"
                                        required
                                    >
                                    @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-lock"></i>
                                    <input
                                        type="password"
                                        name="password"
                                        value="12341234"
                                        placeholder="Enter your password"
                                        required
                                    >
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>

                                <div class="remind-sec">
                                    <div class="remind-left">
                                        <input type="checkbox" id="remember" name="remember">
                                        <label for="remember">Remember me</label>
                                    </div>
                                    <div class="remind-right">
                                        <a href="{{ route('vshop.forgot.form') }}">Forgot password?</a>
                                    </div>
                                </div>

                                <button type="submit" class="sign-btn">Sign In to VSHOP</button>

                                <div class="new-text">
                                    <p>New to VSHOP? <a href="{{ route('vshop.customer.register.form') }}">Create an account</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>