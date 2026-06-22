<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Register</title>

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

.form-control, .form-select {
    border-radius: 10px;
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

    <h3 class="text-center mb-4">Create Account</h3>

    <!-- ================= REGISTER FORM ================= -->
    <div id="registerSection">
        <form id="registerForm">
            <div class="mb-3">
                <label class="form-label">Your Name</label>
                <input type="text" id="regName" name="name" class="form-control" placeholder="First and last name" required minlength="3">
            </div>

            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" id="regEmail" name="email" class="form-control" placeholder="Enter your email" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <div class="d-flex gap-2">
                    <select name="country_code" class="form-select w-auto" required>
                        <option value="+91">🇮🇳 +91</option>
                    </select>
                    <input type="tel" id="regMobile" name="mobile" class="form-control" placeholder="Enter 10 digit number" required maxlength="10" inputmode="numeric" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="regPassword" name="password" class="form-control" placeholder="At least 6 characters" required minlength="6">
            </div>

            <button type="submit" class="verify-btn" id="submitRegisterBtn">Create Account</button>
        </form>

        <div class="alert-msg" id="registerAlert"></div>

        <div class="divider">OR</div>

        <div class="text-center">
            <span style="font-size: 14px;">Already have an account? </span>
            <a href="{{ route('seller.login') }}" style="font-weight: 600; text-decoration: none; color:#004392;">Sign in</a>
        </div>
    </div>

    <!-- ================= OTP VERIFICATION SECTION ================= -->
    <div id="otpSection" style="display:none;">

        <div class="otp-info">
            <i class="bi bi-envelope-check"></i>
            OTP has been sent to your email. Please verify.
        </div>

        <div class="mb-2 mt-3">
            <label class="form-label">Enter OTP</label>
            <input 
                type="text" 
                id="registerOtp" 
                class="form-control" 
                placeholder="Enter 4-digit OTP"
                maxlength="4"
                inputmode="numeric"
            >
        </div>

        <button class="verify-btn" id="verifyOtpBtn">Verify OTP</button>

        <div class="text-center mt-2">
            <button class="resend-link" id="resendRegisterOtp" disabled>
                Resend OTP <span id="resendTimer">(30s)</span>
            </button>
        </div>

        <div class="text-center mt-2">
            <a href="javascript:void(0)" id="backToRegister" style="font-size:13px; color:#004392;">← Back to Register</a>
        </div>

        <div class="alert-msg" id="otpAlert"></div>

    </div>

</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
function showAlert(el, msg, type) {
    $(el).text(msg)
         .removeClass('alert-success alert-danger')
         .addClass(type === 'success' ? 'alert alert-success alert-msg' : 'alert alert-danger alert-msg')
         .show();
}

// REGISTER SUBMIT
$('#registerForm').submit(function(e){
    e.preventDefault();

    let name = $('#regName').val().trim();
    let email = $('#regEmail').val().trim();
    let mobile = $('#regMobile').val().trim();
    let password = $('#regPassword').val();

    let regex = /^[6-9]\d{9}$/;
    if (!regex.test(mobile)) {
        showAlert('#registerAlert', 'Enter valid 10-digit mobile number starting with 6-9', 'error');
        return false;
    }

    let btn = $('#submitRegisterBtn');
    let originalText = btn.text();
    btn.prop('disabled', true).text('Processing...');
    $('#registerAlert').hide();

    $.post("{{ route('seller.register.store') }}", {
        _token: "{{ csrf_token() }}",
        name: name,
        email: email,
        mobile: mobile,
        password: password
    }, function(res){
        
        if(res.status) {
            $('.otp-info').html('<i class="bi bi-info-circle"></i> ' + res.message);
            $('#registerSection').slideUp(300, function(){
                $('#otpSection').slideDown(300);
            });
            startResendTimer();
        }

    }).fail(function(err){
        let errorMsg = 'Registration failed';
        if (err.responseJSON && err.responseJSON.message) {
            errorMsg = err.responseJSON.message;
        }
        showAlert('#registerAlert', errorMsg, 'error');
        btn.prop('disabled', false).text(originalText);
    });

});

// VERIFY OTP
$('#verifyOtpBtn').click(function(){

    let otp = $('#registerOtp').val().trim();

    if(otp.length !== 4){
        showAlert('#otpAlert', 'Enter valid 4-digit OTP', 'error');
        return;
    }

    let btn = $(this);
    btn.prop('disabled', true).text('Verifying...');

    $.post("{{ route('seller.register.verify') }}", {
        _token: "{{ csrf_token() }}",
        otp: otp
    }, function(res){

        if(res.redirect) {
            // Automatically redirect to dashboard
            showAlert('#otpAlert', res.message, 'success');
            setTimeout(() => {
                window.location.href = res.redirect;
            }, 1000);
        }

    }).fail(function(err){
        showAlert('#otpAlert', err.responseJSON?.message || 'Verification failed', 'error');
        btn.prop('disabled', false).text('Verify OTP');
    });

});

// RESEND OTP
$('#resendRegisterOtp').click(function(){

    let btn = $(this);
    btn.prop('disabled', true);

    $.post("{{ route('seller.register.resend') }}", {
        _token: "{{ csrf_token() }}"
    }, function(res){
        showAlert('#otpAlert', res.message || 'OTP resent successfully', 'success');
        startResendTimer();
    }).fail(function(err){
        showAlert('#otpAlert', err.responseJSON?.message || 'Resend failed', 'error');
        btn.prop('disabled', false);
    });

});

// BACK TO REGISTER
$('#backToRegister').click(function(){
    $('#otpSection').slideUp(300, function(){
        $('#registerSection').slideDown(300);
        $('#submitRegisterBtn').prop('disabled', false).text('Create Account');
        $('#registerAlert').hide();
    });
});

// RESEND TIMER
function startResendTimer() {
    let seconds = 30;
    let btn = $('#resendRegisterOtp');
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

</body>
</html>
