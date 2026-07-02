<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mafatlals - Login</title>

    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">

    <!-- External -->
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v7.1.0/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css" />  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 

    <!-- Sliders -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css" />   
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Local CSS -->
	<link rel="stylesheet" href="{{ asset('assets/css/rs-spacing-min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css') }}">
</head>

<body>

<main>

    <section class="login-sec">
        <div class="login-inner">
            <div class="login-card">
                <div class="login-card-inner">

                    {{-- Logo + Title --}}
                    <div class="login-card-inner-top text-center">
                        <img src="{{ asset('assets/images/logo.png') }}" class="img-fluid" alt="Logo">
                        <h2>Change Password</h2>
                       
                    </div>

                    {{-- Login Form --}}
                    <div class="login-card-inner-bottom">
                     <form method="POST" action="{{ route('frontend.changepassword') }}">
    @csrf

    <div class="mb-3">
        <label>Current Password</label>
        <input type="password" name="current_password" class="form-control" required>
        @error('current_password')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label>New Password</label>
        <input type="password" name="new_password" class="form-control" required>
        @error('new_password')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label>Confirm New Password</label>
        <input type="password" name="new_password_confirmation" class="form-control" required>
    </div>

    <button type="submit" class="btn btn-primary">Update Password</button>
</form>

                    </div>

                   

                </div>
            </div>	
        </div>
    </section>

</main>

<!-- Scroll Up -->
<div class="progressParent" id="back-top">
  <svg class="backCircle svg-inner" width="100%" height="100%" viewBox="-1 -1 102 102">
    <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"/>
  </svg>
</div>

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>   
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>  
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js"></script>

<script src="{{ asset('assets/js/sal.min.js') }}"></script>
<script src="{{ asset('assets/js/tilt.min.js') }}"></script>
<script src="{{ asset('assets/js/main.js') }}"></script>

</body>
</html>
