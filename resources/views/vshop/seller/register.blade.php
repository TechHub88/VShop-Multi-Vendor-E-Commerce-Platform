<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>
    
    {{-- CSS --}}
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    {{-- Owl Carousel --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    
    {{-- Slick Slider --}}
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
    
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v7.1.0/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    
    {{-- LOCAL CSS --}}
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/rs-spacing-min.css') }}">
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/responsive.css') }}">
    
    @stack('styles')
    
    <style>
        .dashboardPagesContentonner {
            width: 100%;
            height: 100vh;
            background-image: url(https://kidzz.in/vtravel/public/vtravelassets/images/login-big-bg-img.jpg);
            background-size: cover;
            background-repeat: no-repeat;
            position: relative;
        }
        .dashboardPagesContentonner::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: #0043929e;
        } 
        .dashboardPagesContentonner {
            width: 100%;
        }
        
        .dashboardPosPagesLeftContentonner {
            width: 100%;
            height: 100%;
            position: relative;
        }
        
        .dashboardDtlsArea {
            width: 100%;
            height: 100%;
        }
        
        .dashboardDtlsAreaBody {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .seller-card h2 {
            text-align: center;
            color: #004392;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 21px;
        }
        
        .verify-btn {
            background: #004392 !important;
            border-color: #004392 !important;
            color: #fff;
        }
    </style>
</head>
<body>
    
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
<div class="dashboardPosPagesLeftContentonner">
<div class="dashboardDtlsArea">
<div class="dashboardDtlsAreaBody">

<div class="seller-auth-wrapper">

    {{-- LOGO --}}
    <!--<div class="seller-logo">-->
    <!--    <img src="{{ asset('vtravelassets/images/logo.png') }}" alt="logo">-->
    <!--    <p>Seller Central India</p>-->
    <!--</div>-->

    {{-- CARD --}}
    <div class="seller-card">

        <h2>Create Account</h2>

    <form method="POST" action="{{ route('seller.register.store') }}" id="registerForm">
    @csrf

    {{-- NAME --}}
    <div class="form-group">
        <label>Your name</label>
        <input type="text" name="name" placeholder="First and last name" required minlength="3">
    </div>

    {{-- EMAIL (NEW ADDED) --}}
    <div class="form-group">
        <label>Email address</label>
        <input 
            type="email" 
            name="email" 
            placeholder="Enter your email"
            required
        >
    </div>

    {{-- MOBILE --}}
    <div class="form-group mobile-group">
        <label>Mobile number</label>
        <div class="mobile-row">

            <select name="country_code" required>
                <option value="+91">🇮🇳 +91</option>
                <option value="+1">🇺🇸 +1</option>
                <option value="+44">🇬🇧 +44</option>
                <option value="+61">🇦🇺 +61</option>
                <option value="+971">🇦🇪 +971</option>
            </select>

            <input 
                type="tel" 
                name="mobile" 
                id="mobile"
                placeholder="Enter 10 digit number"
                required
                maxlength="10"
                inputmode="numeric"
                pattern="[0-9]{10}"
                oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            >
        </div>
        <small>Example: 9876543210</small>
    </div>

    {{-- PASSWORD --}}
    <div class="form-group">
        <label>Password</label>
        <input 
            type="password" 
            name="password" 
            id="password"
            placeholder="At least 6 characters" 
            required 
            minlength="6"
        >
        <small>Password must be at least 6 characters.</small>
    </div>

   
    {{-- SUBMIT --}}
    <button type="submit" class="verify-btn">
        Create Account
    </button>

</form>
        <div class="signin-link">
            Already have an account?
            <a href="{{ route('seller.login') }}">Sign in</a>
        </div>

    </div>
</div>

</div>
</div>
</div>
</div>
<style>
/* ===== SELLER AUTH UI ===== */
.seller-auth-wrapper{
    max-width:420px;
    margin:40px auto;
    text-align:center;
}

.seller-logo img{
    height:40px;
}
.seller-logo p{
    font-size:13px;
    margin-top:5px;
    color:#555;
}

.seller-card{
    border:1px solid #ddd;
    padding:25px;
    border-radius:8px;
    background:#fff;
    text-align:left;
    margin-top:15px;
}

.seller-card h2{
    font-size:24px;
    margin-bottom:20px;
}

.form-group{
    margin-bottom:15px;
}

.form-group label{
    font-weight:600;
    display:block;
    margin-bottom:5px;
}

.form-group input,
.form-group select{
    width:100%;
    padding:10px;
    border:1px solid #ccc;
    border-radius:6px;
}

.mobile-row{
    display:flex;
    gap:10px;
}

.mobile-row select{
    width:90px;
}

.form-group small{
    font-size:12px;
    color:#555;
}

.verify-btn{
    width:100%;
    background:#ffd814;
    border:1px solid #fcd200;
    padding:12px;
    border-radius:25px;
    font-weight:600;
    cursor:pointer;
    margin-top:10px;
}

.verify-btn:hover{
    background:#f7ca00;
}

.signin-link{
    text-align:center;
    margin-top:15px;
    font-size:14px;
}
</style>
</body>
</html>
<script>
document.getElementById('registerForm').addEventListener('submit', function(e) {

    let mobile = document.getElementById('mobile').value;

    // Strict Indian mobile validation
    let regex = /^[6-9]\d{9}$/;

    if (!regex.test(mobile)) {
        e.preventDefault();
        alert("Enter valid 10-digit mobile number starting with 6-9");
        return false;
    }

});
</script>









