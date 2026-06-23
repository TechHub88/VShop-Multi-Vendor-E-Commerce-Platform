<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Login</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
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

/* Custom Tab Styles */
.nav-tabs {
    border-bottom: 1px solid var(--card-border);
    display: flex;
    gap: 15px;
}

.nav-tabs .nav-link {
    color: var(--text-muted);
    font-weight: 600;
    border: none;
    border-bottom: 2px solid transparent;
    padding: 10px 5px;
    font-size: 15px;
    background: transparent;
    transition: all 0.3s;
}

.nav-tabs .nav-link.active {
    color: var(--accent-blue);
    border: none;
    border-bottom: 2px solid var(--accent-blue);
    background: transparent;
}

.nav-tabs .nav-link:hover {
    color: var(--text-main);
}

/* Verify/Login Button */
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

    <h3 class="text-center mb-4">Seller Login</h3>

    <!-- ================= LOGIN FORM ================= -->
    <div id="loginSection">

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="loginTab" role="tablist">
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100 active" id="email-tab" data-bs-toggle="tab" data-bs-target="#email-pane" type="button" role="tab">Email Login</button>
            </li>
            <li class="nav-item flex-fill text-center" role="presentation">
                <button class="nav-link w-100" id="mobile-tab" data-bs-toggle="tab" data-bs-target="#mobile-pane" type="button" role="tab">Mobile Login</button>
            </li>
        </ul>

        <div class="tab-content" id="loginTabContent">
            <!-- Email Login Pane -->
            <div class="tab-pane fade show active" id="email-pane" role="tabpanel">
                <div class="mb-3">
                    <label class="form-label">Email Address</label>
                    <input type="email" id="loginEmail" class="form-control" placeholder="Enter your email">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="passwordEmail" class="form-control border-end-0" placeholder="Enter password">
                        <span class="input-group-text bg-white border-start-0 togglePass" style="cursor: pointer;">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>
                <button class="verify-btn loginBtn" data-type="email">Login</button>
            </div>

            <!-- Mobile Login Pane -->
            <div class="tab-pane fade" id="mobile-pane" role="tabpanel">
                <div class="mb-3">
                    <label class="form-label">Mobile Number</label>
                    <input type="text" id="loginMobile" class="form-control" placeholder="Enter 10-digit mobile number" maxlength="15">
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input type="password" id="passwordMobile" class="form-control border-end-0" placeholder="Enter password">
                        <span class="input-group-text bg-white border-start-0 togglePass" style="cursor: pointer;">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>
                <button class="verify-btn loginBtn" data-type="mobile">Login</button>
            </div>
        </div>

        <div class="alert-msg" id="loginAlert"></div>

    </div>

    <!-- ================= OTP VERIFICATION SECTION ================= -->
    <div id="otpSection" style="display:none;">

        <div class="otp-info">
            <i class="bi bi-envelope-check"></i>
            OTP has been sent to your registered email. Please check your inbox.
        </div>

        <div class="mb-2 mt-3 text-center">
            <label class="form-label d-block">Enter OTP</label>
            <div class="otp-inputs-wrapper">
                <input type="text" class="otp-input-field" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                <input type="text" class="otp-input-field" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                <input type="text" class="otp-input-field" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
                <input type="text" class="otp-input-field" maxlength="1" inputmode="numeric" pattern="[0-9]*" autocomplete="off">
            </div>
            <input 
                type="hidden" 
                id="loginOtp" 
                class="otp-hidden-val"
            >
        </div>

        <button class="verify-btn" id="verifyOtpBtn">Verify OTP & Login</button>

        <div class="text-center mt-2">
            <button class="resend-link" id="resendLoginOtp" disabled>
                Resend OTP <span id="resendTimer">(30s)</span>
            </button>
        </div>

        <div class="text-center mt-2">
            <a href="javascript:void(0)" id="backToLogin" style="font-size:13px;">← Back to Login</a>
        </div>

        <div class="alert-msg" id="otpAlert"></div>

    </div>

    <!-- LINKS -->
    <div class="d-flex justify-content-between mt-3">
        <a href="{{ route('seller.forgot.password') }}">Forgot Password?</a>
    </div>

    <div class="divider">OR</div>

    <div class="text-center">
        <a href="{{ route('seller.register') }}" class="btn btn-outline-primary w-100">
            Create New Account
        </a>
    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>

// Global variables for resend
let currentLogin = '';
let currentPassword = '';

// PASSWORD TOGGLE
$(document).on('click','.togglePass', function(){
    let input = $(this).siblings('input');
    input.attr('type', input.attr('type') === 'password' ? 'text' : 'password');
    $(this).find('i').toggleClass('bi-eye-slash bi-eye');
});

function showAlert(el, msg, type) {
    $(el).text(msg)
         .removeClass('alert-success alert-danger alert')
         .addClass(type === 'success' ? 'alert alert-success alert-msg' : 'alert alert-danger alert-msg')
         .show();
}

// LOGIN → Send OTP to email or mobile
$('.loginBtn').click(function(){

    let type = $(this).data('type');
    let login = type === 'email' ? $('#loginEmail').val() : $('#loginMobile').val();
    let password = type === 'email' ? $('#passwordEmail').val() : $('#passwordMobile').val();

    if(!login || !password){
        showAlert('#loginAlert', 'Enter email/mobile and password', 'error');
        return;
    }

    currentLogin = login;
    currentPassword = password;

    let btn = $(this);
    let originalText = btn.text();
    btn.prop('disabled', true).text('Verifying...');

    $.post("{{ route('seller.login.post') }}", {
        _token: "{{ csrf_token() }}",
        login: login,
        password: password
    }, function(res){

        if(res.otp_required) {
            // Update the OTP info text dynamically
            $('.otp-info').html('<i class="bi bi-info-circle"></i> ' + res.message);
            
            // Clear fields first
            $('#otpSection .otp-input-field').val('');
            $('#loginOtp').val('');

            // For development without SMS provider
            if (res.dev_otp) {
                console.log('Development OTP:', res.dev_otp);
                // Auto-fill OTP in development to make it easier since no SMS gateway
                $('#loginOtp').val(res.dev_otp);
                let otpDigits = res.dev_otp.toString();
                $('#otpSection .otp-input-field').each(function(index) {
                    if (index < otpDigits.length) {
                        $(this).val(otpDigits[index]);
                    }
                });
            }

            // Show OTP section
            $('#loginSection').slideUp(300, function(){
                $('#otpSection').slideDown(300);
                // Focus the first input box
                $('#otpSection .otp-input-field').first().focus();
            });
            startResendTimer();
        } else if(res.redirect) {
            window.location.href = res.redirect;
        }

    }).fail(function(err){
        showAlert('#loginAlert', err.responseJSON?.message || 'Login failed', 'error');
        btn.prop('disabled', false).text(originalText);
    });

});

// VERIFY OTP
$('#verifyOtpBtn').click(function(){

    let otp = $('#loginOtp').val().trim();

    if(otp.length !== 4){
        showAlert('#otpAlert', 'Enter valid 4-digit OTP', 'error');
        return;
    }

    let btn = $(this);
    btn.prop('disabled', true).text('Verifying...');

    $.post("{{ route('seller.login.verify.email') }}", {
        _token: "{{ csrf_token() }}",
        otp: otp
    }, function(res){

        if(res.redirect) {
            window.location.href = res.redirect;
        }

    }).fail(function(err){
        showAlert('#otpAlert', err.responseJSON?.message || 'Verification failed', 'error');
        btn.prop('disabled', false).text('Verify OTP & Login');
    });

});

// RESEND OTP
$('#resendLoginOtp').click(function(){

    let btn = $(this);
    btn.prop('disabled', true);

    $.post("{{ route('seller.login.post') }}", {
        _token: "{{ csrf_token() }}",
        login: currentLogin,
        password: currentPassword
    }, function(res){
        showAlert('#otpAlert', res.message || 'OTP resent successfully', 'success');
        startResendTimer();
    }).fail(function(err){
        showAlert('#otpAlert', err.responseJSON?.message || 'Resend failed', 'error');
        btn.prop('disabled', false);
    });

});

// BACK TO LOGIN
$('#backToLogin').click(function(){
    $('#otpSection').slideUp(300, function(){
        $('#loginSection').slideDown(300);
        $('.loginBtn').prop('disabled', false).text('Login');
        $('#loginAlert').hide();
    });
});

// RESEND TIMER
function startResendTimer() {
    let seconds = 30;
    let btn = $('#resendLoginOtp');
    let timerSpan = $('#resendTimer');
    
    btn.prop('disabled', true);
    timerSpan.text('(' + seconds + 's)');

    let interval = setInterval(function(){
        seconds--;
        timerSpan.text('(' + seconds + 's)');

        if(seconds <= 0) {
            clearInterval(interval);
            btn.prop('disabled', false);
            timerSpan.text('');
        }
    }, 1000);
}

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
        let container = $(element).closest('#otpSection');
        let hiddenInput = container.find('.otp-hidden-val');
        let otp = '';
        container.find('.otp-input-field').each(function() {
            otp += $(this).val();
        });
        hiddenInput.val(otp);
    }
});

</script>

<script>

// 🔥 PREVENT BACK AFTER LOGOUT

(function () {

    // clear previous history state
    window.history.pushState(null, "", window.location.href);

    window.onpopstate = function () {

        // force stay on login page
        window.history.pushState(null, "", window.location.href);

        // optional hard redirect
        window.location.replace("/seller/login");

    };

})();


// 🔥 EXTRA CACHE BLOCK
window.addEventListener("pageshow", function (event) {

    if (event.persisted) {

        window.location.reload();

    }

});


// 🔥 PREVENT FORWARD/BACK CACHE
if (window.history.replaceState) {

    window.history.replaceState(null, null, window.location.href);

}

</script>

</body>
</html>