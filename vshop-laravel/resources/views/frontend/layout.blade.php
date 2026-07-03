<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mafatlals</title>
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png')}}" type="image/x-icon">

    <!-- font awesome link -->
    <link rel="stylesheet" href="https://site-assets.fontawesome.com/releases/v7.1.0/css/all.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">    
    <!--<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.css" />-->  
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui/dist/fancybox.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <link rel="stylesheet"href="https://unpkg.com/swiper@11/swiper-bundle.min.css"/>
	<link rel="stylesheet" href="{{ asset('assets/css/rs-spacing-min.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css')}}">
    <link rel="stylesheet" href="{{ asset('assets/css/responsive.css')}}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    
    <!-- Load jQuery early so it's available for all scripts -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

</head>
<body>

    @include('frontend.partials.header')

    <main>
        @yield('content')
    </main>

    @include('frontend.partials.footer')
        <div id="bottomToast" class="bottom-toast"></div>

<style>
.bottom-toast {
    position: fixed;
    bottom: 25px;
    right: 25px; /* 👈 Move to RIGHT bottom */
    transform: translateY(150%);
    background: #28a745; /* 👈 Green color */
    color: #fff;
    padding: 10px 16px;
    border-radius: 6px;
    font-size: 14px;
    max-width: 250px;
    opacity: 0;
    z-index: 9999;
    transition: all 0.4s ease;
    box-shadow: 0px 3px 10px rgba(0,0,0,0.3);
}

/* visible */
.bottom-toast.show {
    opacity: 1;
    transform: translateY(0);
}
</style>



<script>
function showBottomToast(message) {
    const toast = document.getElementById("bottomToast");
    if (toast) {
        toast.innerText = message;
        toast.classList.add("show");

        setTimeout(() => {
            toast.classList.remove("show");
        }, 2500); // hide after 2.5 sec
    }
}
</script>

@if(session('success'))
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const toast = document.getElementById("bottomToast");
        if (toast) {
            showBottomToast("{{ session('success') }}");
        }
    });
</script>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

</body>
</html>
