<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
:root {
    --bg-primary: #1e3a8a;
    --bg-secondary: #2563eb;
    --accent-blue: #2563eb;
    --accent-light: #eff6ff;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --card-bg: #ffffff;
    --card-border: #e2e8f0;
}

body {
    margin: 0;
    min-height: 100vh;
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%);
    font-family: 'Plus Jakarta Sans', sans-serif;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
    overflow-x: hidden;
}

/* Subtle glow */
body::before {
    content: '';
    position: absolute;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255,255,255,0) 70%);
    top: -200px;
    left: -200px;
    pointer-events: none;
    z-index: 0;
}

.seller-card {
    width: 100%;
    max-width: 440px;
    background: var(--card-bg);
    padding: 45px 35px;
    border-radius: 24px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
    z-index: 10;
    position: relative;
}

.seller-card h3 {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 32px;
    color: var(--bg-primary);
    margin-bottom: 8px;
}

.form-label {
    font-size: 14px;
    margin-bottom: 6px;
    font-weight: 600;
    color: var(--text-main);
}

.form-control {
    background: #ffffff;
    border: 1px solid var(--card-border);
    color: var(--text-main);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-control:focus {
    background: #ffffff;
    border-color: var(--bg-secondary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    color: var(--text-main);
}

.form-control::placeholder {
    color: #94a3b8;
}

.input-group-text {
    background: #ffffff !important;
    border: 1px solid var(--card-border) !important;
    border-left: none !important;
    color: var(--text-muted) !important;
    border-top-right-radius: 12px !important;
    border-bottom-right-radius: 12px !important;
    padding: 12px 16px;
}

.form-control.border-end-0 {
    border-top-right-radius: 0 !important;
    border-bottom-right-radius: 0 !important;
}

.verify-btn {
    width: 100%;
    background: var(--accent-blue);
    color: #ffffff;
    padding: 14px;
    border-radius: 50px;
    border: none;
    margin-top: 15px;
    font-weight: 700;
    font-size: 15px;
    letter-spacing: 0.5px;
    transition: all 0.3s;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
}

.verify-btn:hover {
    background: var(--bg-primary);
    box-shadow: 0 10px 25px rgba(30, 58, 138, 0.35);
    transform: translateY(-2px);
}

.verify-btn:active {
    transform: translateY(1px);
}

.verify-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
    box-shadow: none !important;
}

/* Links & Dividers */
a {
    color: var(--accent-blue);
    text-decoration: none;
    font-size: 14px;
    font-weight: 600;
    transition: all 0.3s;
}

a:hover {
    color: var(--bg-primary);
    text-decoration: underline;
}

.divider {
    text-align: center;
    margin: 25px 0;
    font-size: 13px;
    color: var(--text-muted);
    position: relative;
}

.divider::before, .divider::after {
    content: '';
    position: absolute;
    top: 50%;
    width: 42%;
    height: 1px;
    background: var(--card-border);
}
.divider::before { left: 0; }
.divider::after { right: 0; }

.btn-outline-primary {
    border: 2px solid var(--accent-blue);
    color: var(--accent-blue);
    background: transparent;
    border-radius: 50px;
    padding: 12px;
    font-weight: 700;
    font-size: 14px;
    transition: all 0.3s;
    display: inline-block;
    width: 100%;
}

.btn-outline-primary:hover {
    background: var(--accent-light);
    border-color: var(--accent-blue);
    color: var(--accent-blue);
    transform: translateY(-2px);
}

.btn-outline-primary:active {
    transform: translateY(1px);
}

/* OTP Info & Alert Box */
.otp-info {
    background: var(--accent-light);
    border-left: 4px solid var(--accent-blue);
    padding: 12px 16px;
    border-radius: 8px;
    margin-top: 15px;
    font-size: 13.5px;
    color: var(--bg-secondary);
}

.resend-link {
    font-size: 13.5px;
    cursor: pointer;
    color: var(--accent-blue);
    text-decoration: underline;
    border: none;
    background: none;
    margin-top: 8px;
    font-weight: 600;
}

.resend-link:disabled {
    color: var(--text-muted);
    text-decoration: none;
    cursor: not-allowed;
}

.alert-msg {
    font-size: 13.5px;
    padding: 12px 16px;
    border-radius: 12px;
    margin-top: 15px;
    display: none;
}

.switch-btn {
    width: 48%;
    border-radius: 25px;
    font-size: 14px;
    font-weight: 600;
    padding: 8px;
}

.error-text {
    color: #dc2626;
    font-size: 13.5px;
    margin-top: 4px;
}

.success-text {
    color: #16a34a;
    font-size: 13.5px;
    margin-top: 4px;
}

.password-wrapper {
    position: relative;
}
.password-wrapper i {
    position: absolute;
    right: 12px;
    top: 12px;
    cursor: pointer;
    color: #64748b;
}

.mobile-row {
    display: flex;
    gap: 8px;
}
.mobile-row select {
    width: 90px;
    border-radius: 12px;
}

.step-indicator {
    display: flex;
    justify-content: center;
    gap: 8px;
    margin-bottom: 20px;
}
.step-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    background: #e2e8f0;
    transition: 0.3s;
}
.step-dot.active {
    background: var(--accent-blue);
    transform: scale(1.2);
}

/* Toast */
.toast-msg {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #0f172a;
    color: #fff;
    padding: 14px 24px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.3);
    z-index: 999999;
    transform: translateY(100px);
    opacity: 0;
    transition: 0.4s ease;
}
.toast-msg.show {
    transform: translateY(0);
    opacity: 1;
}
.toast-msg i {
    color: #22c55e;
    font-size: 18px;
}

/* Proper OTP Box Styles */
.otp-inputs-wrapper {
    display: flex;
    justify-content: center;
    gap: 12px;
    margin-top: 15px;
    margin-bottom: 20px;
}

.otp-input-field {
    width: 50px;
    height: 50px;
    font-size: 22px;
    font-weight: 700;
    text-align: center;
    border: 2px solid var(--card-border);
    border-radius: 12px;
    background: #ffffff;
    color: var(--text-main);
    transition: all 0.2s ease;
    padding: 0;
}

.otp-input-field:focus {
    border-color: var(--bg-secondary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
    outline: none;
}
</style>
</head>

<body>

<div class="seller-card">

    <!-- TOAST -->
    <div class="toast-msg" id="successToast">
        <i class="bi bi-check-circle-fill"></i>
        <span id="toastText">Password updated successfully</span>
    </div>

    <!-- STEP INDICATOR -->
    <div class="step-indicator">
        <div class="step-dot active" id="dot1"></div>
        <div class="step-dot" id="dot2"></div>
        <div class="step-dot" id="dot3"></div>
    </div>

    <h3 class="text-center mb-3">Forgot Password</h3>

    <!-- ================= STEP 1: ENTER PHONE OR EMAIL ================= -->
    <div id="step1">

        <!-- SWITCH TABS -->
        <div class="d-flex justify-content-between mb-3">
            <button class="btn btn-primary switch-btn" id="phoneTabBtn">Phone</button>
            <button class="btn btn-outline-primary switch-btn" id="emailTabBtn">Email</button>
        </div>

        <!-- PHONE INPUT -->
        <div id="phoneInput">
            <label class="form-label">Mobile Number</label>
            <div class="mobile-row">
                <select id="country_code" class="form-control">
                    <option value="+91">IN +91</option>
                    <option value="+1">US +1</option>
                    <option value="+44">UK +44</option>
                </select>
                <input type="text" id="mobileInput" class="form-control" placeholder="Enter mobile number" maxlength="10" inputmode="numeric">
            </div>
        </div>

        <!-- EMAIL INPUT -->
        <div id="emailInput" style="display:none;">
            <label class="form-label">Email Address</label>
            <input type="email" id="emailInputField" class="form-control" placeholder="Enter your email">
        </div>

        <div class="error-text" id="step1Error"></div>

        <button id="sendOtpBtn" class="verify-btn">Send OTP</button>

    </div>

    <!-- ================= STEP 2: OTP VERIFICATION ================= -->
    <div id="step2" style="display:none;">

        <div class="otp-info">
            <i class="bi bi-envelope-check"></i>
            <span id="otpInfoText">OTP has been sent to your registered email.</span>
        </div>

        <div class="mt-3 text-center">
            <label class="form-label d-block">Enter OTP</label>
            <div class="otp-inputs-wrapper">
                <input type="text" class="otp-input-field" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                <input type="text" class="otp-input-field" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                <input type="text" class="otp-input-field" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                <input type="text" class="otp-input-field" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
            </div>
            <input 
                type="hidden" 
                id="otpInput" 
                class="otp-hidden-val"
            >
        </div>

        <div class="error-text" id="otpError"></div>

        <button id="verifyOtpBtn" class="verify-btn">Verify OTP</button>

        <div class="text-center mt-2">
            <button class="resend-link" id="resendOtpBtn" disabled>
                Resend OTP <span id="resendTimer">(30s)</span>
            </button>
        </div>

        <div class="text-center mt-2">
            <a href="javascript:void(0)" id="backToStep1" style="font-size:13px;">← Back</a>
        </div>

        </div>

    <!-- ================= STEP 3: RESET PASSWORD ================= -->
    <div id="step3" style="display:none;">

        <div class="otp-info" style="background:#dcfce7; border-color:#16a34a; color:#166534;">
            <i class="bi bi-check-circle"></i>
            OTP verified successfully. Please set your new password.
        </div>

        <div class="mb-3 mt-3">
            <label class="form-label">New Password</label>
            <div class="password-wrapper">
                <input type="password" id="newPassword" class="form-control" placeholder="Enter new password">
                <i class="bi bi-eye-slash togglePass" data-target="#newPassword"></i>
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <div class="password-wrapper">
                <input type="password" id="confirmPassword" class="form-control" placeholder="Confirm new password">
                <i class="bi bi-eye-slash togglePass" data-target="#confirmPassword"></i>
            </div>
        </div>

        <div class="error-text" id="passwordError"></div>

        <button id="resetBtn" class="verify-btn">Reset Password</button>

    </div>

    <div class="text-center mt-3">
        <a href="/seller/login"><i class="bi bi-arrow-left"></i> Back to Login</a>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>
$(document).ready(function(){

    let currentTab = 'phone'; // 'phone' or 'email'

    // ── TAB SWITCH ──
    $('#phoneTabBtn').click(function(){
        currentTab = 'phone';
        $('#phoneInput').show();
        $('#emailInput').hide();
        $(this).addClass('btn-primary').removeClass('btn-outline-primary');
        $('#emailTabBtn').addClass('btn-outline-primary').removeClass('btn-primary');
        $('#step1Error').text('');
    });

    $('#emailTabBtn').click(function(){
        currentTab = 'email';
        $('#emailInput').show();
        $('#phoneInput').hide();
        $(this).addClass('btn-primary').removeClass('btn-outline-primary');
        $('#phoneTabBtn').addClass('btn-outline-primary').removeClass('btn-primary');
        $('#step1Error').text('');
    });

    // ── MOBILE VALIDATION ──
    $('#mobileInput').on('input', function(){
        this.value = this.value.replace(/[^0-9]/g, '');
    });

    function getInput() {
        if (currentTab === 'phone') {
            return $('#mobileInput').val().trim();
        } else {
            return $('#emailInputField').val().trim();
        }
    }

    function validateInput() {
        let val = getInput();

        if (currentTab === 'phone') {
            let regex = /^[6-9]\d{9}$/;
            if (!regex.test(val)) {
                $('#step1Error').text('Enter valid 10-digit mobile number');
                return false;
            }
        } else {
            let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(val)) {
                $('#step1Error').text('Enter valid email address');
                return false;
            }
        }

        $('#step1Error').text('');
        return true;
    }

    // ── STEP DOTS ──
    function setStep(step) {
        $('#dot1, #dot2, #dot3').removeClass('active');
        for (let i = 1; i <= step; i++) {
            $('#dot' + i).addClass('active');
        }
    }

    // ── RESEND TIMER ──
    function startResendTimer() {
        let seconds = 30;
        let btn = $('#resendOtpBtn');
        let timerSpan = $('#resendTimer');
        
        btn.prop('disabled', true);
        timerSpan.text('(' + seconds + 's)');

        let interval = setInterval(function(){
            seconds--;
            timerSpan.text('(' + seconds + 's)');

            if (seconds <= 0) {
                clearInterval(interval);
                btn.prop('disabled', false);
                timerSpan.text('');
            }
        }, 1000);
    }

    // ── PASSWORD TOGGLE ──
    $('.togglePass').click(function(){
        let targetId = $(this).data('target');
        let input = $(targetId);
        let icon = $(this);
        
        if (input.attr('type') === 'password') {
            input.attr('type', 'text');
            icon.removeClass('bi-eye-slash text-secondary').addClass('bi-eye text-primary');
        } else {
            input.attr('type', 'password');
            icon.removeClass('bi-eye text-primary').addClass('bi-eye-slash text-secondary');
        }
    });

    // ── STEP 1: SEND OTP ──
    $('#sendOtpBtn').click(function(){

        if (!validateInput()) return;

        let btn = $(this);
        btn.prop('disabled', true).text('Sending OTP...');

        $.post('/seller/forgot-password/send-otp', {
            _token: "{{ csrf_token() }}",
            input: getInput()
        }, function(res){

            // Show email info
            $('#otpInfoText').html('OTP has been sent to <strong>' + (res.email || 'your registered email') + '</strong>. Please check your inbox.');

            // Clear fields first
            $('#step2 .otp-input-field').val('');
            $('#otpInput').val('');

            // Go to Step 2
            $('#step1').slideUp(300, function(){
                $('#step2').slideDown(300);
                // Focus the first OTP field
                $('#step2 .otp-input-field').first().focus();
            });
            setStep(2);
            startResendTimer();

        }).fail(function(err){
            $('#step1Error').text(err.responseJSON?.message || 'Failed to send OTP');
            btn.prop('disabled', false).text('Send OTP');
        });

    });

    // ── RESEND OTP ──
    $('#resendOtpBtn').click(function(){
        let btn = $(this);
        btn.prop('disabled', true);

        $.post('/seller/forgot-password/send-otp', {
            _token: "{{ csrf_token() }}",
            input: getInput()
        }, function(res){
            $('#otpError').removeClass('error-text').addClass('success-text').text('OTP resent to ' + (res.email || 'your email'));
            startResendTimer();
        }).fail(function(err){
            $('#otpError').removeClass('success-text').addClass('error-text').text(err.responseJSON?.message || 'Resend failed');
            btn.prop('disabled', false);
        });
    });

    // ── BACK TO STEP 1 ──
    $('#backToStep1').click(function(){
        $('#step2').slideUp(300, function(){
            $('#step1').slideDown(300);
            $('#sendOtpBtn').prop('disabled', false).text('Send OTP');
        });
        setStep(1);
    });

    // ── STEP 2: VERIFY OTP ──
    $('#verifyOtpBtn').click(function(){

        let otp = $('#otpInput').val().trim();

        if (otp.length !== 4) {
            $('#otpError').removeClass('success-text').addClass('error-text').text('Enter valid 4-digit OTP');
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).text('Verifying...');

        $.post('/seller/forgot-password/verify-otp', {
            _token: "{{ csrf_token() }}",
            otp: otp
        }, function(res){

            // Go to Step 3
            $('#step2').slideUp(300, function(){
                $('#step3').slideDown(300);
            });
            setStep(3);

        }).fail(function(err){
            $('#otpError').removeClass('success-text').addClass('error-text').text(err.responseJSON?.message || 'Invalid OTP');
            btn.prop('disabled', false).text('Verify OTP');
        });

    });

    // ── STEP 3: RESET PASSWORD ──
    $('#resetBtn').click(function(){

        let pass = $('#newPassword').val();
        let confirm = $('#confirmPassword').val();

        let strongRegex = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d@$!%*#?&]{6,}$/;

        if (!strongRegex.test(pass)) {
            $('#passwordError').text('Password must be 6+ chars with letters & numbers');
            return;
        }

        if (pass !== confirm) {
            $('#passwordError').text('Passwords do not match');
            return;
        }

        $('#passwordError').text('');

        let btn = $(this);
        btn.prop('disabled', true).text('Resetting...');

        $.post('/seller/forgot-password/reset', {
            _token: "{{ csrf_token() }}",
            password: pass,
            password_confirmation: confirm
        }, function(res){

            // Show toast
            $('#toastText').text(res.message || 'Password updated successfully');
            $('#successToast').addClass('show');

            // Redirect to dashboard after 1.5s
            setTimeout(function(){
                window.location.href = res.redirect || '/seller/dashboard';
            }, 1500);

        }).fail(function(err){
            $('#passwordError').text(err.responseJSON?.message || 'Reset failed');
            btn.prop('disabled', false).text('Reset Password');
        });

    });

});
// Proper OTP Auto-focus & Navigation behavior
$(document).ready(function() {
    $('.otp-input-field').on('input', function(e) {
        let val = $(this).val();
        // Allow only numeric digits
        val = val.replace(/[^0-9]/g, '');
        $(this).val(val);

        if (val.length === 1) {
            $(this).next('.otp-input-field').focus();
        }
        
        updateOtpValue(this);
    });

    $('.otp-input-field').on('keydown', function(e) {
        if (e.key === 'Backspace') {
            if ($(this).val().length === 0) {
                $(this).prev('.otp-input-field').focus();
            } else {
                $(this).val('');
            }
            updateOtpValue(this);
            e.preventDefault();
        }
    });

    $('.otp-input-field').on('paste', function(e) {
        let clipboardData = e.originalEvent.clipboardData || window.clipboardData;
        let pastedData = clipboardData.getData('Text').replace(/[^0-9]/g, '').substring(0, 4);
        
        if (pastedData.length > 0) {
            let fields = $(this).parent().find('.otp-input-field');
            fields.each(function(index) {
                if (index < pastedData.length) {
                    $(this).val(pastedData[index]);
                }
            });
            
            let nextFocusIndex = Math.min(pastedData.length, fields.length - 1);
            fields.eq(nextFocusIndex).focus();
            
            updateOtpValue(this);
            e.preventDefault();
        }
    });

    function updateOtpValue(element) {
        let container = $(element).closest('#step2');
        let hiddenInput = container.find('.otp-hidden-val');
        let otp = '';
        container.find('.otp-input-field').each(function() {
            otp += $(this).val();
        });
        hiddenInput.val(otp);
    }
});

</script>

</body>
</html>
