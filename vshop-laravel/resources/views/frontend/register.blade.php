<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mafatlals - Register</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v7.1.0/css/all.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

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
		<div class="fxt-intro">
					<!--<div class="sub-title">Welcome To</div>-->
					<!--<h1>Our Mafatlal</h1>-->
					<!--<p>Grursus mal suada faci lisis Lorem ipsum dolarorit ametion consectetur elit. Vesti ulum nec the dumm.</p>-->
				</div>
		</div>
		</div>
		
			<div class="rightPart">
            <div class="login-card">
                <div class="login-card-inner">

                    <div class="login-card-inner-top text-center mb-3">
                        <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid mb-2" alt="Logo">
                        <h2>Sign Up</h2>
                        <p>Enter details to create your account</p>
                    </div>

                    <!-- REGISTER FORM -->
                    <div id="register-form">
                        <form id="registerForm">
                            @csrf

                            <div class="textfield-box mb-3">
                                <input type="text" name="name" class="textfield" placeholder="Full Name" required>
                            </div>

                            <div class="textfield-box mb-3">
                                <input type="number" name="phone" class="textfield" placeholder="Phone Number" maxlength="10" required>
                            </div>

                            <div class="textfield-box mb-3">
                                <input type="email" name="email" class="textfield" placeholder="Email Address" required>
                            </div>

                            <input type="hidden" name="client_id" value="1">

                            <div class="textfield-btn-sec text-center mt-3">
                                <button type="submit" class="login-btn">Sign Up</button>
                            </div>
                        </form>

                        <p class="new-text text-center mt-3">
                            Already have an account? <a href="{{ route('frontend.employeelogin') }}">Login</a>
                        </p>
                    </div>

                    <!-- OTP FORM -->
                    <div id="otp-form" style="display:none;">
                        <h5 class="text-center mb-2">Enter OTP</h5>
                        <p class="text-center">A 6-digit OTP has been sent to your phone.</p>

                        <input type="hidden" id="verify_phone">

                        <div class="row text-center mb-3">
                            <div class="col"><input type="text" maxlength="1" class="form-control otp-input"></div>
                            <div class="col"><input type="text" maxlength="1" class="form-control otp-input"></div>
                            <div class="col"><input type="text" maxlength="1" class="form-control otp-input"></div>
                            <div class="col"><input type="text" maxlength="1" class="form-control otp-input"></div>
                            <div class="col"><input type="text" maxlength="1" class="form-control otp-input"></div>
                            <div class="col"><input type="text" maxlength="1" class="form-control otp-input"></div>
                        </div>

                        <button id="verifyOtpBtn" class="btn btn-primary w-100">Verify OTP</button>
                    </div>

                </div>
            </div>
			</div>
        </div>
    </section>
</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {

    // REGISTER FORM SUBMIT
    $('#registerForm').submit(function(e){
        e.preventDefault();

        let formData = new FormData(this);

        $.ajax({
            url: "{{ route('frontend.register') }}",
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(res){

                Swal.fire({
                    icon: "success",
                    title: "Registered Successfully!",
                    text: "Sending OTP...",
                    timer: 1500,
                    showConfirmButton: false
                });

                $('#register-form').hide();
                $('#otp-form').show();

                $('#verify_phone').val(res.phone);

                // AUTO FILL OTP (DEV MODE)
                if(res.otp){
                    let otpArr = res.otp.toString().split("");
                    $('.otp-input').each(function(index) {
                        $(this).val(otpArr[index]);
                    });

                    // ❌ Removed auto verify (important)
                }
            },

            error: function(xhr){
                Swal.fire({
                    icon: "error",
                    title: "Registration Failed",
                    text: xhr.responseJSON?.message || "Try again"
                });
            }
        });
    });

    // OTP AUTO MOVE
    $(document).on("keyup", ".otp-input", function(){
        if(this.value.length === 1){
            $(this).next(".otp-input").focus();
        }
    });

    // CLICK VERIFY BUTTON
    $(document).on('click', '#verifyOtpBtn', function() {
        verifyOtp();
    });

    // VERIFY OTP FUNCTION
    function verifyOtp() {

        let otp = "";
        $('.otp-input').each(function(){
            otp += $(this).val();
        });

        $.ajax({
            url: "{{ route('frontend.verifyotp') }}",
            method: "POST",
            data: {
                phone: $('#verify_phone').val(),
                otp: otp,
                _token: $('meta[name="csrf-token"]').attr('content')
            },

            success: function(res){
                Swal.fire({
                    icon: "success",
                    title: "OTP Verified 🎉",
                    text: "Redirecting...",
                    timer: 2000,
                    showConfirmButton: false
                });

                setTimeout(() => {
                    window.location.href = res.redirect;
                }, 1500);
            },

            error: function(){
                Swal.fire({
                    icon: "error",
                    title: "Invalid OTP",
                    text: "Please try again"
                });
            }
        });
    }

});
</script>

</body>
</html>
