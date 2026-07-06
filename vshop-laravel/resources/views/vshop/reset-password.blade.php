<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSHOP - Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; }
        .login-body { width: 100%; height: 100dvh; position: relative; }
        .login-body-inner { width: 100%; height: 100vh; display: flex; align-items: center; position: relative; }
        .login-body-left { width: 50%; height: 100%; padding: 80px; background-image: url(https://img.freepik.com/free-photo/copy-space-cosmetic-skin-care-products_23-2148317615.jpg?t=st=1773403401~exp=1773407001~hmac=c55fcfca1d3f05bd276fbc991cfac47f632ceb6f1db723cb5bf047ee2aeb5b4b&w=1060); background-repeat: no-repeat; background-size: cover; background-position: top; position: relative; display: flex; align-items: center; }
        .login-body-left::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #00000099; }
        .login-info { position: relative; }
        .login-body-right { width: 50%; height: 100%; background: #fff; padding: 40px 125px; display: flex; align-items: center; }
        .login-form-sec { width: 100%; }
        .login-info h2 { font-size: 33px; color: #fff; margin-bottom: 18px; }
        .login-info p { font-size: 15px; color: #fff; margin-bottom: 20px; }
        .follow-box p { margin-bottom: 8px; color: #fff; font-size: 14px; }
        .follow-box ul { display: flex; align-items: center; gap: 5px; list-style: none; padding: 0; }
        .follow-box ul li a { display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; color: #fff; border: 1px solid #fff; border-radius: 50%; text-decoration: none; transition: 0.3s; }
        .follow-box ul li a:hover { background: #1a73e8; border-color: #1a73e8; }
        .follow-box ul li a i { font-size: 13px; }
        .top-area { text-align: center; margin-bottom: 20px; }
        .top-area a { display: inline-block; margin-bottom: 18px; }
        .top-area h2 { font-size: 26px; margin-bottom: 7px; color: #1a73e8; font-weight: 700; }
        .top-area p { font-size: 15px; color: #666; }
        .fotm-input-box { position: relative; margin-bottom: 15px; }
        .fotm-input-box i { position: absolute; left: 12px; top: 13px; font-size: 16px; color: #888; }
        .fotm-input-box input { width: 100%; border-radius: 8px; outline: none; border: 2px solid #dee2e6; padding: 11px 12px 11px 42px; font-size: 15px; transition: border-color 0.3s; }
        .fotm-input-box input:focus { border-color: #1a73e8; }
        .sign-btn { width: 100%; background: #1a73e8; color: #fff; border: 0; border-radius: 8px; font-size: 16px; font-weight: 600; padding: 12px 0; margin-bottom: 20px; transition: 0.3s; cursor: pointer; }
        .sign-btn:hover { background: #1557b0; }
        .vshop-badge { display: inline-block; background: #1a73e8; color: #fff; padding: 5px 15px; border-radius: 30px; font-size: 13px; margin-bottom: 12px; }
        .verified-badge { display: inline-flex; align-items: center; gap: 6px; background: #e8f5e9; color: #2e7d32; border: 1px solid #a5d6a7; padding: 8px 16px; border-radius: 8px; font-size: 14px; font-weight: 600; margin-bottom: 15px; }
        .verified-badge i { color: #2e7d32; }
        .back-link { text-align: center; margin-top: 15px; }
        .back-link a { color: #6c757d; text-decoration: none; font-size: 14px; }
        .back-link a:hover { color: #1a73e8; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
        .alert-danger { background: #fde8e8; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-success { background: #e8f4fd; border: 1px solid #bee5eb; color: #0c5460; }
        @media(max-width: 991px) { .login-body-left { display: none; } .login-body-right { width: 100%; padding: 40px 160px; } }
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
                        <span class="vshop-badge">Secure Reset</span>
                        <h2>Create New Password</h2>
                        <p>Set a strong new password to secure your VSHOP account.</p>
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
                            <a href="{{ route('vshoplogin') }}">
                                <img src="{{ asset('images/vshop-logo.png') }}" alt="VSHOP Logo">
                            </a>
                            <h2>Set New Password</h2>
                            <p>OTP verified! Create your new password below.</p>
                        </div>
                        <div class="bottom-area">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if(session('error'))
                                <div class="alert alert-danger">{{ session('error') }}</div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger">{{ $errors->first() }}</div>
                            @endif

                            <div class="verified-badge">
                                <i class="fa-solid fa-circle-check"></i> Email verified: {{ session('reset_email') }}
                            </div>

                            <form action="{{ route('vshop.reset.update') }}" method="POST">
                                @csrf
                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" name="password" placeholder="New Password (min 6 characters)" required>
                                    @error('password')
                                        <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </div>
                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" name="password_confirmation" placeholder="Confirm New Password" required>
                                </div>
                                <button type="submit" class="sign-btn">Reset Password</button>
                                <div class="back-link">
                                    <a href="{{ route('vshoplogin') }}">← Back to Sign In</a>
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
