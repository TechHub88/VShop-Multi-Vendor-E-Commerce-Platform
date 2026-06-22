<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSHOP Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; }
        .login-body { width: 100%; min-height: 100dvh; position: relative; }
        .login-body-inner { width: 100%; min-height: 100dvh; display: flex; align-items: center; position: relative; }
        .login-body-left {
            width: 50%; height: 100%; min-height: 100dvh; padding: 80px;
            background-image: url(https://img.freepik.com/free-photo/copy-space-cosmetic-skin-care-products_23-2148317615.jpg?t=st=1773403401~exp=1773407001~hmac=c55fcfca1d3f05bd276fbc991cfac47f632ceb6f1db723cb5bf047ee2aeb5b4b&w=1060);
            background-repeat: no-repeat; background-size: cover; background-position: top;
            position: relative; display: flex; align-items: center;
        }
        .login-body-left::before { content: ""; position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: #00000099; }
        .login-info { position: relative; }
        .login-body-right { width: 50%; height: 100%; background: #fff; padding: 40px 80px; display: flex; align-items: center; }
        .login-form-sec { width: 100%; }
        .login-info h2 { font-size: 33px; color: #fff; margin-bottom: 18px; }
        .login-info p { font-size: 15px; color: #fff; margin-bottom: 20px; }
        .follow-box p { margin-bottom: 8px; color: #fff; font-size: 14px; }
        .follow-box ul { display: flex; align-items: center; gap: 5px; list-style: none; padding: 0; }
        .follow-box ul li a { display: flex; align-items: center; justify-content: center; width: 30px; height: 30px; color: #fff; border: 1px solid #fff; border-radius: 50%; text-decoration: none; transition: 0.3s; }
        .follow-box ul li a:hover { background: #1a73e8; border-color: #1a73e8; }
        .follow-box ul li a i { font-size: 13px; }
        .top-area { text-align: center; margin-bottom: 18px; }
        .top-area a { display: inline-block; margin-bottom: 14px; }
        .top-area h2 { font-size: 24px; margin-bottom: 6px; color: #1a73e8; font-weight: 700; }
        .top-area p { font-size: 14px; color: #666; }
        .fotm-input-box { position: relative; margin-bottom: 14px; }
        .fotm-input-box i { position: absolute; left: 12px; top: 13px; font-size: 15px; color: #888; }
        .fotm-input-box input { width: 100%; border-radius: 8px; outline: none; border: 2px solid #dee2e6; padding: 11px 12px 11px 42px; font-size: 14px; transition: border-color 0.3s; }
        .fotm-input-box input:focus { border-color: #1a73e8; }
        .remind-sec { display: flex; align-items: center; justify-content: space-between; margin-bottom: 15px; }
        .remind-left { display: flex; align-items: center; }
        .remind-left input { display: block; width: 16px; height: 16px; margin-right: 8px; cursor: pointer; }
        .remind-left label { color: #596477; font-size: 14px; cursor: pointer; }
        .remind-left label a { color: #1a73e8; text-decoration: none; }
        .sign-btn { width: 100%; background: #1a73e8; color: #fff; border: 0; border-radius: 8px; font-size: 16px; font-weight: 600; padding: 12px 0; margin-bottom: 15px; transition: 0.3s; cursor: pointer; }
        .sign-btn:hover { background: #1557b0; }
        .new-text p { text-align: center; font-size: 14px; color: #666; }
        .new-text p a { color: #1a73e8; font-weight: 600; text-decoration: none; }
        .vshop-badge { display: inline-block; background: #1a73e8; color: #fff; padding: 5px 15px; border-radius: 30px; font-size: 13px; margin-bottom: 12px; }
        .error-text { color: #dc3545; font-size: 12px; margin-top: 4px; padding-left: 42px; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
        .alert-danger { background: #fde8e8; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-success { background: #e8f4fd; border: 1px solid #bee5eb; color: #0c5460; }

        /* OTP Modal */
        .modal-title { color: #1a73e8; font-weight: 700; }
        .otp-info-box {
            background: #e8f4fd;
            border: 1px solid #1a73e8;
            border-radius: 8px;
            padding: 12px 15px;
            text-align: center;
            margin-bottom: 15px;
            font-size: 14px;
            color: #0c5460;
        }
        .otp-info-box i { color: #1a73e8; margin-right: 6px; }
        .countdown-timer { text-align: center; font-size: 13px; color: #666; margin-bottom: 12px; }
        #verifyOtpBtn { background: #1a73e8; color: #fff; border: none; padding: 8px 20px; border-radius: 6px; cursor: pointer; }
        #verifyOtpBtn:hover { background: #1557b0; }
        #resendOtpBtn { color: #1a73e8; background: none; border: none; text-decoration: underline; cursor: pointer; font-size: 14px; }

        @media(max-width: 991px) { .login-body-left { display: none; } .login-body-right { width: 100%; padding: 40px 160px; } }
        @media(max-width: 575px) { .login-body-right { padding: 40px 60px; } }
        @media(max-width: 480px) { .login-body-right { padding: 30px 20px; } }
    </style>
</head>

<body>
    <div class="login-body">
        <div class="login-body-outer">
            <div class="login-body-inner">
                <div class="login-body-left">
                    <div class="login-info">
                        <span class="vshop-badge">Join VSHOP Today</span>
                        <h2>Start Your Digital Product Journey with V-Shop</h2>
                        <p>Create an account to access thousands of premium digital products, exclusive deals, and instant downloads.</p>
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
                            <h2>Create VSHOP Account</h2>
                            <p>Enter your details to start shopping</p>
                        </div>

                        <div id="errorAlert" class="alert alert-danger" style="display:none;"></div>
                        <div id="successAlert" class="alert alert-success" style="display:none;"></div>

                        <div class="bottom-area">
                            <form method="POST" action="{{ route('vshop.customer.register') }}" id="registerForm">
                                @csrf

                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-user"></i>
                                    <input type="text" name="name" id="name" value="{{ old('name') }}" placeholder="Full Name" required>
                                    <div class="error-text" id="nameError"></div>
                                </div>

                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-phone"></i>
                                    <input type="text" name="phone" id="phone" value="{{ old('phone') }}" placeholder="Phone Number" required maxlength="10" pattern="[0-9]{10}">
                                    <div class="error-text" id="phoneError"></div>
                                </div>

                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" name="email" id="email" value="{{ old('email') }}" placeholder="Email Address" required>
                                    <div class="error-text" id="emailError"></div>
                                </div>

                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" name="password" id="password" placeholder="Password" required minlength="6">
                                    <div class="error-text" id="passwordError"></div>
                                </div>

                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-lock"></i>
                                    <input type="password" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password" required>
                                </div>

                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-gift"></i>
                                    <input type="text" name="referral_code" id="referral_code" value="{{ request()->query('ref') }}" placeholder="Referral Code (Optional)">
                                    <div class="error-text" id="referral_codeError"></div>
                                </div>

                                <div class="remind-sec">
                                    <div class="remind-left">
                                        <input type="checkbox" id="terms" name="terms" required>
                                        <label for="terms">I agree to the <a href="#">Terms &amp; Conditions</a></label>
                                    </div>
                                </div>

                                <button type="submit" class="sign-btn" id="submitBtn">Create VSHOP Account</button>

                                <div class="new-text">
                                    <p>Already have an account? <a href="{{ route('vshoplogin') }}">Sign In to VSHOP</a></p>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- OTP Verification Modal -->
    <div class="modal fade" id="otpModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fa-solid fa-shield-halved me-2"></i>Verify Your Email</h5>
                </div>
                <div class="modal-body">
                    <div class="otp-info-box">
                        <i class="fa-solid fa-envelope-circle-check"></i>
                        OTP sent to <strong id="displayEmail"></strong><br>
                        <small>Please check your inbox and spam folder.</small>
                    </div>

                    <div class="countdown-timer" id="countdownTimer">OTP expires in 05:00</div>

                    <div class="fotm-input-box">
                        <i class="fa-regular fa-key"></i>
                        <input type="text" id="otpInput" placeholder="Enter 6-digit OTP" maxlength="6" autocomplete="off">
                    </div>
                    <div id="otpError" class="error-text" style="display:none;"></div>
                </div>
                <div class="modal-footer d-flex justify-content-between align-items-center">
                    <button type="button" id="resendOtpBtn" style="display:none;">Resend OTP</button>
                    <div>
                        <button type="button" class="btn btn-secondary btn-sm me-2" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" id="verifyOtpBtn">Verify &amp; Register</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            let timerInterval;
            let timeLeft = 300;
            let currentEmail = '';
            let currentPhone = '';

            /* ---- Timer ---- */
            function startCountdown() {
                timeLeft = 300;
                $('#countdownTimer').css('color', '#666').text('OTP expires in 05:00');
                clearInterval(timerInterval);
                timerInterval = setInterval(function () {
                    timeLeft--;
                    let m = Math.floor(timeLeft / 60);
                    let s = timeLeft % 60;
                    $('#countdownTimer').text('OTP expires in ' + String(m).padStart(2,'0') + ':' + String(s).padStart(2,'0'));
                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        $('#countdownTimer').css('color','#dc3545').text('OTP expired! Click Resend.');
                        $('#resendOtpBtn').show();
                    }
                }, 1000);
            }

            /* ---- Register Form Submit ---- */
            $('#registerForm').on('submit', function (e) {
                e.preventDefault();
                $('.error-text').hide().text('');
                $('#errorAlert').hide().text('');
                $('#successAlert').hide().text('');
                $('#submitBtn').prop('disabled', true).text('Sending OTP...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        $('#submitBtn').prop('disabled', false).text('Create VSHOP Account');

                        if (response.success) {
                            currentEmail = response.email;
                            currentPhone = response.phone;
                            $('#displayEmail').text(currentEmail);
                            $('#resendOtpBtn').hide();
                            startCountdown();
                            $('#otpInput').val('');
                            $('#otpError').hide().text('');
                            $('#otpModal').modal('show');
                            $('#successAlert').text('OTP sent to your email. Please verify to complete registration.').show();
                        }
                    },
                    error: function (xhr) {
                        $('#submitBtn').prop('disabled', false).text('Create VSHOP Account');
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors || {};
                            $.each(errors, function (field, messages) {
                                var el = $('#' + field + 'Error');
                                if (el.length) { el.text(messages[0]).show(); }
                            });
                        } else {
                            var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Registration failed. Please try again.';
                            $('#errorAlert').text(msg).show();
                        }
                    }
                });
            });

            /* ---- Verify OTP ---- */
            $('#verifyOtpBtn').on('click', function () {
                var otp = $('#otpInput').val().trim();
                if (!otp || otp.length !== 6) {
                    $('#otpError').text('Please enter a valid 6-digit OTP.').show();
                    return;
                }
                $(this).prop('disabled', true).text('Verifying...');
                $('#otpError').hide().text('');

                $.ajax({
                    url: '{{ route("verify.otp") }}',
                    method: 'POST',
                    data: {
                        phone: currentPhone,
                        otp: otp,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function (response) {
                        $('#verifyOtpBtn').prop('disabled', false).text('Verify & Register');
                        clearInterval(timerInterval);
                        if (response.redirect) {
                            $('#otpModal').modal('hide');
                            window.location.href = response.redirect;
                        }
                    },
                    error: function (xhr) {
                        $('#verifyOtpBtn').prop('disabled', false).text('Verify & Register');
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : 'Invalid or expired OTP.';
                        if (xhr.status === 422 && xhr.responseJSON.errors && xhr.responseJSON.errors.otp) {
                            msg = xhr.responseJSON.errors.otp[0];
                        }
                        $('#otpError').text(msg).show();
                    }
                });
            });

            /* ---- Resend OTP ---- */
            $('#resendOtpBtn').on('click', function () {
                $(this).prop('disabled', true).text('Sending...');
                $('#otpError').hide();

                $.ajax({
                    url: '{{ route("resend.otp") }}',
                    method: 'POST',
                    data: { phone: currentPhone, _token: '{{ csrf_token() }}' },
                    dataType: 'json',
                    success: function (response) {
                        $('#resendOtpBtn').prop('disabled', false).text('Resend OTP').hide();
                        startCountdown();
                        $('#otpError').text('New OTP sent to your email.').css('color','#0c5460').show();
                    },
                    error: function () {
                        $('#resendOtpBtn').prop('disabled', false).text('Resend OTP');
                        $('#otpError').text('Failed to resend OTP. Please try again.').show();
                    }
                });
            });
        });
    </script>
</body>

</html>