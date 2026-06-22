<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    height: 100vh;
    background: linear-gradient(135deg, #004392, #0d6efd);
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', sans-serif;
}

.seller-card {
    width: 100%;
    max-width: 420px;
    background: #ffffff;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}

.seller-card h3 {
    font-weight: 600;
    color: #003a80;
}

.verify-btn {
    width: 100%;
    background: #004392;
    color: #fff;
    padding: 12px;
    border-radius: 30px;
    border: none;
    margin-top: 10px;
}

.verify-btn:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.form-label {
    font-size: 13px;
    margin-bottom: 3px;
    font-weight: 500;
}

.form-control {
    border-radius: 10px;
}

.password-wrapper {
    position: relative;
}
.password-wrapper i {
    position: absolute;
    right: 12px;
    top: 38px;
    cursor: pointer;
}

.divider {
    text-align: center;
    margin: 15px 0;
    font-size: 13px;
    color: #777;
}

.otp-info {
    background: #e8f4fd;
    border-left: 4px solid #004392;
    padding: 10px 14px;
    border-radius: 6px;
    margin-top: 12px;
    font-size: 13px;
    color: #003a80;
}

.resend-link {
    font-size: 13px;
    cursor: pointer;
    color: #004392;
    text-decoration: underline;
    border: none;
    background: none;
    margin-top: 8px;
}

.alert-msg {
    font-size: 13px;
    padding: 8px 12px;
    border-radius: 8px;
    margin-top: 10px;
    display: none;
}
</style>
</head>

<body>

<div class="seller-card">

<style>
/* Additional Tab Styles */
.nav-tabs .nav-link {
    color: #4a5568;
    font-weight: 500;
    border: none;
    border-bottom: 2px solid transparent;
}
.nav-tabs .nav-link.active {
    color: #004392;
    border: none;
    border-bottom: 2px solid #004392;
    background: transparent;
}
.nav-tabs {
    border-bottom: 1px solid #e2e8f0;
}
</style>

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

        <div class="mb-2 mt-3">
            <label class="form-label">Enter OTP</label>
            <input 
                type="text" 
                id="loginOtp" 
                class="form-control" 
                placeholder="Enter 4-digit OTP"
                maxlength="4"
                inputmode="numeric"
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
         .removeClass('alert-success alert-danger')
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
            
            // For development without SMS provider
            if (res.dev_otp) {
                console.log('Development OTP:', res.dev_otp);
                // Auto-fill OTP in development to make it easier since no SMS gateway
                $('#loginOtp').val(res.dev_otp);
            }

            // Show OTP section
            $('#loginSection').slideUp(300, function(){
                $('#otpSection').slideDown(300);
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