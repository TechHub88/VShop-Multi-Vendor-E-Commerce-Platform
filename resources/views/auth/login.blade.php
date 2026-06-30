@extends('layouts.auth')


@section('content')
<div class="col-xl-12">
    <div class="row">
        <div class="col-md-4 mx-auto">
            
            <!-- Main Login Card -->
            <div class="card">
                <div class="card-body">

                    
                    <div class="mb-0 p-0 p-lg-3">
                        <div class="mb-0 border-0 p-md-4 p-lg-0">
                            <div class="mb-3 p-0 text-center">
                                <div class="auth-brand">
                                    
                                </div>
                            </div>

                            <div class="auth-title-section mb-4 text-center">
                                <h3 class="text-primary fw-semibold mb-1" style="color: #003a80 !important;">
                                    Admin Login
                                </h3>
                               
                                <div class="mt-2">
                                   
                                </div>
                            </div>

                            <div class="pt-0">
                                <!-- LOGIN SECTION -->
                                <div id="loginSection">
                                    <form id="adminLoginForm" class="my-4">
                                        @csrf
                                        <div class="alert alert-danger" id="loginAlert" style="display:none;"></div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="email" class="form-label">Email address</label>
                                            <input class="form-control" type="email" name="email" id="email" 
                                                   value="{{ old('email', 'admin@gmail.com') }}"
                                                   required placeholder="Enter your email">
                                        </div>
            
                                        <div class="form-group mb-3">
                                            <label for="password" class="form-label">Password</label>
                                            <input type="password" name="password" id="password" class="form-control" value="admin123" required>
                                        </div>
            
                                        <div class="form-group mb-3">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                                <label class="form-check-label" for="remember">Remember me</label>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group mb-0">
                                            <div class="d-grid">
                                                <button class="btn btn-primary fw-semibold login-btn" type="submit" id="loginSubmitBtn"> 
                                                   Login Now
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <!-- OTP VERIFICATION SECTION -->
                                <div id="otpSection" style="display:none;" class="my-4">
                                    <div class="alert alert-info" id="otpInfo">
                                        <i class="mdi mdi-information-outline me-1"></i> OTP sent to your email.
                                    </div>
                                    <div class="alert alert-danger" id="otpAlert" style="display:none;"></div>
                                    
                                    <div class="form-group mb-3 text-center mt-4">
                                        <label class="form-label d-block mb-3">Enter OTP</label>
                                        <div class="d-flex justify-content-center gap-2 mb-3">
                                            <input type="text" class="form-control text-center otp-input-field" maxlength="1" style="width:50px; height:50px; font-size:20px;">
                                            <input type="text" class="form-control text-center otp-input-field" maxlength="1" style="width:50px; height:50px; font-size:20px;">
                                            <input type="text" class="form-control text-center otp-input-field" maxlength="1" style="width:50px; height:50px; font-size:20px;">
                                            <input type="text" class="form-control text-center otp-input-field" maxlength="1" style="width:50px; height:50px; font-size:20px;">
                                        </div>
                                        <input type="hidden" id="loginOtp" class="otp-hidden-val">
                                    </div>

                                    <div class="form-group mb-3">
                                        <div class="d-grid">
                                            <button class="btn btn-primary fw-semibold login-btn" type="button" id="verifyOtpBtn">
                                                Verify OTP & Login
                                            </button>
                                        </div>
                                    </div>

                                    <div class="text-center mt-3">
                                        <a href="javascript:void(0)" id="backToLogin" class="text-muted"><i class="mdi mdi-arrow-left"></i> Back to Login</a>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery (Required for AJAX) -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click to copy functionality for demo credentials
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    
    // Highlight demo credentials on focus
    emailField.addEventListener('focus', function() {
        this.select();
    });
    
    passwordField.addEventListener('focus', function() {
        this.select();
    });
    
    // Show password toggle (optional)
    const showPasswordToggle = document.createElement('span');
    showPasswordToggle.innerHTML = '<i class="mdi mdi-eye"></i>';
    showPasswordToggle.className = 'position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer';
    showPasswordToggle.style.cursor = 'pointer';
    showPasswordToggle.style.zIndex = '10';
    
    const passwordGroup = passwordField.parentElement;
    passwordGroup.style.position = 'relative';
    passwordGroup.appendChild(showPasswordToggle);
    
    showPasswordToggle.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="mdi mdi-eye"></i>' : '<i class="mdi mdi-eye-off"></i>';
    });
});
</script>

<style>
.form-text {
    font-size: 12px;
    margin-top: 4px;
}

.alert-light {
    background-color: rgba(13, 110, 253, 0.05);
    border-color: rgba(13, 110, 253, 0.2) !important;
}

.login-btn {
    padding: 12px;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.cursor-pointer {
    cursor: pointer;
}
</style>
<style>
    .account-page {
        background: #004392;
    }
    .account-page::before {
        background-color: #004392;
    }
    .login-btn {
        background-color: #004392 !important;
        border-color: #004392 !important;
    }
</style>

<script>
$(document).ready(function() {
    // 1. Handle Login Form Submit
    $('#adminLoginForm').on('submit', function(e) {
        e.preventDefault();
        
        let email = $('#email').val();
        let password = $('#password').val();
        let remember = $('#remember').is(':checked') ? 1 : 0;
        let btn = $('#loginSubmitBtn');
        let originalText = btn.text();
        
        btn.prop('disabled', true).text('Verifying...');
        $('#loginAlert').hide();

        $.post("{{ route('admin.login.store') }}", {
            _token: "{{ csrf_token() }}",
            email: email,
            password: password,
            remember: remember
        }, function(res) {
            if (res.otp_required) {
                // Admin Staff - Show OTP Section
                $('#otpInfo').html('<i class="mdi mdi-information-outline me-1"></i> ' + res.message);
                
                if (res.dev_otp) {
                    console.log("Dev OTP:", res.dev_otp);
                    $('#loginOtp').val(res.dev_otp);
                    let otpStr = res.dev_otp.toString();
                    $('.otp-input-field').each(function(i) {
                        if (i < otpStr.length) $(this).val(otpStr[i]);
                    });
                }
                
                $('#loginSection').slideUp(300, function() {
                    $('#otpSection').slideDown(300);
                    $('.otp-input-field').first().focus();
                });
            } else if (res.redirect) {
                // Main Admin - Redirect
                window.location.href = res.redirect;
            }
        }).fail(function(err) {
            btn.prop('disabled', false).text(originalText);
            let msg = err.responseJSON?.message || err.responseJSON?.errors?.email?.[0] || 'Login failed';
            $('#loginAlert').text(msg).show();
        });
    });

    // 2. Handle OTP Verify
    $('#verifyOtpBtn').on('click', function() {
        let otp = $('#loginOtp').val().trim();
        if (otp.length !== 4) {
            $('#otpAlert').text('Please enter a valid 4-digit OTP.').show();
            return;
        }

        let btn = $(this);
        btn.prop('disabled', true).text('Verifying...');
        $('#otpAlert').hide();

        $.post("{{ route('admin.login.verify-otp') }}", {
            _token: "{{ csrf_token() }}",
            otp: otp
        }, function(res) {
            if (res.redirect) {
                window.location.href = res.redirect;
            }
        }).fail(function(err) {
            btn.prop('disabled', false).text('Verify OTP & Login');
            $('#otpAlert').text(err.responseJSON?.message || 'Verification failed').show();
        });
    });

    // 3. Back to Login
    $('#backToLogin').click(function() {
        $('#otpSection').slideUp(300, function() {
            $('#loginSection').slideDown(300);
            $('#loginSubmitBtn').prop('disabled', false).text('Login Now');
        });
    });

    // 4. OTP Inputs Behavior
    $('.otp-input-field').on('input', function(e) {
        let val = $(this).val().replace(/[^0-9]/g, '');
        $(this).val(val);
        if (val.length === 1) {
            $(this).next('.otp-input-field').focus();
        }
        updateOtpValue();
    });

    $('.otp-input-field').on('keydown', function(e) {
        if (e.key === 'Backspace') {
            if ($(this).val().length === 0) {
                $(this).prev('.otp-input-field').focus();
            } else {
                $(this).val('');
            }
            updateOtpValue();
            e.preventDefault();
        }
    });

    $('.otp-input-field').on('paste', function(e) {
        let pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData('Text').replace(/[^0-9]/g, '').substring(0, 4);
        if (pastedData.length > 0) {
            let fields = $('.otp-input-field');
            fields.each(function(index) {
                if (index < pastedData.length) {
                    $(this).val(pastedData[index]);
                }
            });
            fields.eq(Math.min(pastedData.length, fields.length - 1)).focus();
            updateOtpValue();
            e.preventDefault();
        }
    });

    function updateOtpValue() {
        let otp = '';
        $('.otp-input-field').each(function() {
            otp += $(this).val();
        });
        $('#loginOtp').val(otp);
    }
});
</script>

@endsection