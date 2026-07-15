<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Seller Register</title>

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

.form-control, .form-select {
    background: #ffffff;
    border: 1px solid var(--card-border);
    color: var(--text-main);
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 14px;
    transition: all 0.3s;
}

.form-control:focus, .form-select:focus {
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

            <div class="mb-3">
                <label class="form-label">Referral Code <span style="font-size: 12px; color: #94a3b8;">(Optional)</span></label>
                <div class="d-flex gap-2 align-items-center">
                    <i class="bi bi-gift" style="font-size: 18px; color: #2563eb;"></i>
                    <input type="text" id="regReferralCode" name="referral_code" class="form-control" placeholder="Enter referral code (e.g. VSHOP-XXXXXX)" maxlength="20" style="text-transform: uppercase;" value="{{ request('ref') }}">
                </div>
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
                id="registerOtp" 
                class="otp-hidden-val"
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
    let referralCode = $('#regReferralCode').val().trim().toUpperCase();

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
        password: password,
        referral_code: referralCode
    }, function(res){
        
        if(res.status) {
            $('.otp-info').html('<i class="bi bi-info-circle"></i> ' + res.message);
            
            // Clear fields first
            $('#otpSection .otp-input-field').val('');
            $('#registerOtp').val('');

            $('#registerSection').slideUp(300, function(){
                $('#otpSection').slideDown(300);
                // Focus first OTP field
                $('#otpSection .otp-input-field').first().focus();
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

</body>
</html>
