<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mafatlals - Login</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">

    <!-- External CSS -->
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v7.1.0/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Local CSS -->
    <link rel="stylesheet" href="{{ asset('assets/css/rs-spacing-min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
<main>
    <section class="login-sec">
        <div class="login-inner">
		<div class="leftPart">
		<div class="loginThumnail">
		<!--<div class="fxt-intro">
					<div class="sub-title">Welcome To</div>
					<h1>Our Mafatlal</h1>
					<p>Grursus mal suada faci lisis Lorem ipsum dolarorit ametion consectetur elit. Vesti ulum nec the dumm.</p>
				</div>-->
		</div>
		</div>
		<div class="rightPart">
            <div class="login-card">
                <div class="login-card-inner">

                    <!-- Logo + Title -->
                    <div class="login-card-inner-top text-center mb-3">
                        <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid" alt="Logo">
                        <h2>Login / Signup</h2>
                        <p>Join us now to be a part of Mafatlal family.</p>
                    </div>

                    <!-- Send OTP Form -->
                    <div id="login-form">
                        <form id="sendOtpForm">
                            @csrf
                            <!--<div class="mb-3">
                                <label for="phone" class="form-label">Mobile Number</label>
                                <input type="text" id="phone" name="phone" value="9749555376" class="form-control"  maxlength="10" required>
                            </div>-->
							<div class="mb-3">
							<label for="phone" class="form-label">Mobile Number</label>
							<div class="input-group mb-3">
								<div class="input-group-prepend">
								  <span class="input-group-text"><img src="{{ asset('assets/images/india-flag-round-icon.png') }}" class="img-fluid" alt=""> +91</span>
								</div>
								<input type="text" id="phone" name="phone" value="9749555376" class="form-control"  maxlength="10" required>
							  </div>
							</div>
                            <button type="submit" class="btn btn-primary w-100">Send OTP</button>
                        </form>
                    </div>

                    <!-- OTP Form -->
                    <div id="otp-form" style="display:none;">
                        <h5 class="text-center mb-2">Enter OTP</h5>
                        <p class="text-center">A 6-digit OTP has been sent to your mobile number.</p>

                        <form id="verifyOtpForm">
                            @csrf
                            <input type="hidden" id="verify_phone" name="phone">
							<div class="otpInputArea">
                            <div class="row rowBox text-center">
                                <div class="col columnBox otpInputBox"><input type="text" id="otp1" maxlength="1" class="form-control otp-input"></div>
                                <div class="col columnBox otpInputBox"><input type="text" id="otp2" maxlength="1" class="form-control otp-input"></div>
                                <div class="col columnBox otpInputBox"><input type="text" id="otp3" maxlength="1" class="form-control otp-input"></div>
                                <div class="col columnBox otpInputBox"><input type="text" id="otp4" maxlength="1" class="form-control otp-input"></div>
                                <div class="col columnBox otpInputBox"><input type="text" id="otp5" maxlength="1" class="form-control otp-input"></div>
                                <div class="col columnBox otpInputBox"><input type="text" id="otp6" maxlength="1" class="form-control otp-input"></div>
                            </div>
							</div>	
                            <button type="submit" class="btn btn-success w-100 mt-3">Verify OTP</button>
                        </form>
                    </div>
                    
                    <div class="text-center mt-3">
                        <a href="{{ route('frontend.register') }}" class="btn btn-secondary w-100">Go to Register</a>
                    </div>
					
					<p class="text-center mt-5 mb-0" style="font-size: 14px;line-height: 20px;color: #999;">By creating an account or logging in, you agree with Mafatlal's T&C and Privacy Policy</p>
					<!-- Add this hidden field in your employeelogin.blade.php (anywhere in the form) -->
<input type="hidden" id="return_to" value="{{ request()->get('return_to') }}">

                </div>
            </div>
			</div>
        </div>
    </section>
</main>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function(){

    // ✅ Send OTP
    $('#sendOtpForm').on('submit', function(e){
        e.preventDefault();
        let phone = $('#phone').val();
        let token = $('meta[name="csrf-token"]').attr('content');

        $.ajax({
            url: "{{ route('frontend.sendotp') }}",
            method: "POST",
            data: { phone: phone, _token: token },
            success: function(res){
                $('#login-form').hide();
                $('#otp-form').show();
                $('#verify_phone').val(phone);

                // ✅ Auto-Fill OTP (for testing only)
                if(res.otp){
                    let arr = res.otp.toString().split("");
                    $('#otp1').val(arr[0]);
                    $('#otp2').val(arr[1]);
                    $('#otp3').val(arr[2]);
                    $('#otp4').val(arr[3]);
                    $('#otp5').val(arr[4]);
                    $('#otp6').val(arr[5]);
                    $('#otp6').focus();
                }
            },
            error: function(xhr){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: xhr.responseJSON?.message || 'Failed to send OTP',
                });
            }
        });
    });

    // Auto move to next input
    $('.otp-input').on('keyup', function(){
        if(this.value.length == 1){
            $(this).next('.otp-input').focus();
        }
    });

 // ✅ Verify OTP

$('#verifyOtpForm').on('submit', function(e){
    e.preventDefault();

    // Collect OTP digits
    let otp = $('#otp1').val() + $('#otp2').val() + $('#otp3').val() + $('#otp4').val() + $('#otp5').val() + $('#otp6').val();
    let phone = $('#verify_phone').val();
    let token = $('meta[name="csrf-token"]').attr('content');

    // Get return URL from query parameter
    const urlParams = new URLSearchParams(window.location.search);
    const returnTo = urlParams.get('return_to');

    $.ajax({
        url: "{{ route('frontend.verifyotp') }}",
        method: "POST",
        data: { phone: phone, otp: otp, _token: token },
        success: function(res){
            // Auto closing SweetAlert (no OK button)
            Swal.fire({
                icon: 'success',
                title: 'OTP Verified Successfully!',
                text: 'Redirecting...',
                showConfirmButton: false,
                timer: 1000,
                timerProgressBar: true
            });

            // ⏳ Redirect after 1 second
            setTimeout(function(){
                // First check if we have a return URL from query parameter
                if (returnTo) {
                    window.location.href = returnTo;  // Go back to product page
                } else if(res.redirect){
                    window.location.href = res.redirect;   // Use backend redirect
                } else {
                    window.location.href = "{{ url('/frontend/Homepage') }}";  // fallback
                }
            }, 1000);
        },

        error: function(xhr){
            Swal.fire({
                icon: 'error',
                title: 'Invalid OTP',
                text: xhr.responseJSON?.message || 'Please try again.',
                showConfirmButton: true
            });
        }
    });
});


});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
