<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VSHOP - Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', sans-serif; }
        .login-body { width: 100%; height: 100dvh; position: relative; }
        .login-body-inner { width: 100%; height: 100vh; display: flex; align-items: center; position: relative; }
        .login-body-left {
            width: 50%; height: 100%; padding: 80px;
            background-image: url(https://img.freepik.com/free-photo/copy-space-cosmetic-skin-care-products_23-2148317615.jpg?t=st=1773403401~exp=1773407001~hmac=c55fcfca1d3f05bd276fbc991cfac47f632ceb6f1db723cb5bf047ee2aeb5b4b&w=1060);
            background-repeat: no-repeat; background-size: cover; background-position: top;
            position: relative; display: flex; align-items: center;
        }
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
        .sign-btn:disabled { background: #93c5fd; cursor: not-allowed; }
        .vshop-badge { display: inline-block; background: #1a73e8; color: #fff; padding: 5px 15px; border-radius: 30px; font-size: 13px; margin-bottom: 12px; }
        .back-link { text-align: center; margin-top: 15px; }
        .back-link a { color: #6c757d; text-decoration: none; font-size: 14px; }
        .back-link a:hover { color: #1a73e8; }
        .alert { padding: 12px 15px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; }
        .alert-danger { background: #fde8e8; border: 1px solid #f5c6cb; color: #721c24; }
        .alert-success { background: #e8f4fd; border: 1px solid #bee5eb; color: #0c5460; }

        /* ── OTP Popup Overlay ── */
        .otp-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.55); z-index: 9999; align-items: center; justify-content: center; }
        .otp-overlay.active { display: flex; }
        .otp-popup { background: #fff; border-radius: 16px; padding: 40px 35px; width: 420px; max-width: 92vw; text-align: center; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: popIn 0.3s ease; position: relative; }
        @keyframes popIn { from { transform: scale(0.8); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .otp-popup .otp-icon { width: 70px; height: 70px; border-radius: 50%; background: #e8f0fe; display: flex; align-items: center; justify-content: center; margin: 0 auto 18px; }
        .otp-popup .otp-icon i { font-size: 30px; color: #1a73e8; }
        .otp-popup h3 { font-size: 22px; font-weight: 700; color: #1a73e8; margin-bottom: 6px; }
        .otp-popup .otp-subtitle { font-size: 14px; color: #666; margin-bottom: 22px; line-height: 1.5; }
        .otp-popup .otp-subtitle strong { color: #333; }
        .otp-input-group { display: flex; gap: 10px; justify-content: center; margin-bottom: 20px; }
        .otp-input-group input {
            width: 48px; height: 54px; text-align: center; font-size: 22px; font-weight: 700;
            border: 2px solid #dee2e6; border-radius: 10px; outline: none; transition: 0.3s;
            color: #1a73e8;
        }
        .otp-input-group input:focus { border-color: #1a73e8; box-shadow: 0 0 0 3px rgba(26,115,232,0.15); }
        .otp-verify-btn { width: 100%; background: #1a73e8; color: #fff; border: 0; border-radius: 10px; font-size: 16px; font-weight: 600; padding: 13px 0; cursor: pointer; transition: 0.3s; margin-bottom: 12px; }
        .otp-verify-btn:hover { background: #1557b0; }
        .otp-verify-btn:disabled { background: #93c5fd; cursor: not-allowed; }
        .otp-resend { font-size: 13px; color: #888; }
        .otp-resend a { color: #1a73e8; text-decoration: none; font-weight: 600; cursor: pointer; }
        .otp-resend a:hover { text-decoration: underline; }
        .otp-error { color: #dc3545; font-size: 13px; margin-bottom: 10px; display: none; }
        .otp-timer { font-size: 12px; color: #999; margin-top: 8px; }
        .otp-close { position: absolute; top: 14px; right: 18px; background: none; border: none; font-size: 20px; color: #aaa; cursor: pointer; }
        .otp-close:hover { color: #333; }

        @media(max-width: 991px) { .login-body-left { display: none; } .login-body-right { width: 100%; padding: 40px 160px; } }
        @media(max-width: 575px) { .login-body-right { padding: 40px 100px; } .otp-popup { padding: 30px 20px; } .otp-input-group input { width: 42px; height: 48px; font-size: 20px; } }
        @media(max-width: 480px) { .login-body-right { padding: 40px 20px; } }
    </style>
</head>

<body>
    <div class="login-body">
        <div class="login-body-outer">
            <div class="login-body-inner">
                <div class="login-body-left">
                    <div class="login-info">
                        <span class="vshop-badge">Reset Password</span>
                        <h2>Forgot Your Password?</h2>
                        <p>Enter your registered email address and we'll send you an OTP to reset your password securely.</p>
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
                            <h2>Forgot Password</h2>
                            <p>Enter your email to receive OTP</p>
                        </div>
                        <div class="bottom-area">
                            <div id="alert-box"></div>

                            <form id="forgot-form">
                                @csrf
                                <div class="fotm-input-box">
                                    <i class="fa-solid fa-envelope"></i>
                                    <input type="email" name="email" id="forgot-email" placeholder="Enter your registered email" required>
                                </div>
                                <button type="submit" class="sign-btn" id="send-otp-btn">Send OTP to Email</button>
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

    <!-- ── OTP Verification Popup ── -->
    <div class="otp-overlay" id="otp-overlay">
        <div class="otp-popup">
            <button class="otp-close" id="otp-close">&times;</button>
            <div class="otp-icon">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h3>Verify OTP</h3>
            <p class="otp-subtitle">We've sent a 6-digit OTP to<br><strong id="otp-email-display"></strong></p>
            <div class="otp-error" id="otp-error"></div>
            <div class="otp-input-group" id="otp-inputs">
                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit" data-index="0" autofocus>
                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit" data-index="1">
                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit" data-index="2">
                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit" data-index="3">
                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit" data-index="4">
                <input type="text" maxlength="1" inputmode="numeric" class="otp-digit" data-index="5">
            </div>
            <button class="otp-verify-btn" id="verify-otp-btn" disabled>Verify OTP</button>
            <p class="otp-resend">
                Didn't receive? <a id="resend-otp-btn">Resend OTP</a>
            </p>
            <p class="otp-timer" id="otp-timer"></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    (function() {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const forgotForm = document.getElementById('forgot-form');
        const sendBtn = document.getElementById('send-otp-btn');
        const alertBox = document.getElementById('alert-box');
        const overlay = document.getElementById('otp-overlay');
        const otpInputs = document.querySelectorAll('.otp-digit');
        const verifyBtn = document.getElementById('verify-otp-btn');
        const resendBtn = document.getElementById('resend-otp-btn');
        const otpError = document.getElementById('otp-error');
        const otpTimer = document.getElementById('otp-timer');
        const emailDisplay = document.getElementById('otp-email-display');
        const closeBtn = document.getElementById('otp-close');

        let userEmail = '';
        let timerInterval = null;

        // ── Show alert ──
        function showAlert(msg, type = 'danger') {
            alertBox.innerHTML = `<div class="alert alert-${type}">${msg}</div>`;
        }

        // ── Timer ──
        function startTimer(seconds) {
            clearInterval(timerInterval);
            let remaining = seconds;
            resendBtn.style.pointerEvents = 'none';
            resendBtn.style.opacity = '0.5';
            otpTimer.textContent = `Resend available in ${remaining}s`;

            timerInterval = setInterval(() => {
                remaining--;
                otpTimer.textContent = `Resend available in ${remaining}s`;
                if (remaining <= 0) {
                    clearInterval(timerInterval);
                    otpTimer.textContent = '';
                    resendBtn.style.pointerEvents = 'auto';
                    resendBtn.style.opacity = '1';
                }
            }, 1000);
        }

        // ── OTP input auto-focus & gather ──
        otpInputs.forEach((input, i) => {
            input.addEventListener('input', (e) => {
                const val = e.target.value.replace(/\D/g, '');
                e.target.value = val;
                if (val && i < 5) otpInputs[i + 1].focus();
                checkOtpFilled();
            });
            input.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && !e.target.value && i > 0) {
                    otpInputs[i - 1].focus();
                }
            });
            // Handle paste
            input.addEventListener('paste', (e) => {
                e.preventDefault();
                const pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
                pasted.split('').forEach((ch, idx) => {
                    if (otpInputs[idx]) otpInputs[idx].value = ch;
                });
                if (otpInputs[Math.min(pasted.length, 5)]) otpInputs[Math.min(pasted.length, 5)].focus();
                checkOtpFilled();
            });
        });

        function checkOtpFilled() {
            const otp = getOtp();
            verifyBtn.disabled = otp.length !== 6;
        }

        function getOtp() {
            return Array.from(otpInputs).map(i => i.value).join('');
        }

        function clearOtpInputs() {
            otpInputs.forEach(i => i.value = '');
            verifyBtn.disabled = true;
            otpError.style.display = 'none';
        }

        function openPopup(email) {
            userEmail = email;
            emailDisplay.textContent = email;
            clearOtpInputs();
            overlay.classList.add('active');
            setTimeout(() => otpInputs[0].focus(), 300);
            startTimer(60);
        }

        function closePopup() {
            overlay.classList.remove('active');
            clearInterval(timerInterval);
        }

        closeBtn.addEventListener('click', closePopup);

        // ── Step 1: Send OTP ──
        forgotForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const email = document.getElementById('forgot-email').value.trim();
            if (!email) return;

            sendBtn.disabled = true;
            sendBtn.textContent = 'Sending OTP...';
            alertBox.innerHTML = '';

            try {
                const res = await fetch("{{ route('vshop.forgot.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    openPopup(email);
                } else {
                    showAlert(data.message || 'Failed to send OTP. Please try again.');
                }
            } catch (err) {
                showAlert('Network error. Please try again.');
            }

            sendBtn.disabled = false;
            sendBtn.textContent = 'Send OTP to Email';
        });

        // ── Step 2: Verify OTP ──
        verifyBtn.addEventListener('click', async () => {
            const otp = getOtp();
            if (otp.length !== 6) return;

            verifyBtn.disabled = true;
            verifyBtn.textContent = 'Verifying...';
            otpError.style.display = 'none';

            try {
                const res = await fetch("{{ route('vshop.forgot.verify') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email: userEmail, otp })
                });

                const data = await res.json();

                if (res.ok && data.success) {
                    // OTP verified — redirect to reset password page
                    window.location.href = data.redirect || "{{ route('vshop.reset.form') }}";
                } else {
                    otpError.textContent = data.message || 'Invalid OTP. Try again.';
                    otpError.style.display = 'block';
                    clearOtpInputs();
                    otpInputs[0].focus();
                }
            } catch (err) {
                otpError.textContent = 'Network error. Please try again.';
                otpError.style.display = 'block';
            }

            verifyBtn.disabled = false;
            verifyBtn.textContent = 'Verify OTP';
        });

        // ── Resend OTP ──
        resendBtn.addEventListener('click', async () => {
            if (!userEmail) return;
            otpError.style.display = 'none';
            clearOtpInputs();

            try {
                const res = await fetch("{{ route('vshop.forgot.send') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ email: userEmail })
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    startTimer(60);
                    otpError.textContent = 'New OTP sent to your email!';
                    otpError.style.color = '#0c5460';
                    otpError.style.display = 'block';
                    setTimeout(() => { otpError.style.display = 'none'; otpError.style.color = '#dc3545'; }, 3000);
                }
            } catch (err) {
                otpError.textContent = 'Failed to resend OTP.';
                otpError.style.display = 'block';
            }
        });

        // Close popup on overlay click
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) closePopup();
        });

        // Enter key on last OTP input triggers verify
        otpInputs[5].addEventListener('keydown', (e) => {
            if (e.key === 'Enter') verifyBtn.click();
        });
    })();
    </script>
</body>

</html>