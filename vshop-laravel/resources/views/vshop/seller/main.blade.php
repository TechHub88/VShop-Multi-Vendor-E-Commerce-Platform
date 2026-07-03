<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Dashboard')</title>

    {{-- CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

    {{-- Plugins --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>

    {{-- Icons --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    {{-- LOCAL CSS --}}
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/rs-spacing-min.css') }}">
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/style.css?=18') }}">
    <link rel="stylesheet" href="{{ asset('vtravelassets/css/responsive.css') }}">
    <style>
        .dashboardPagesContent .container-fluid {
            width: 100%;
            min-height: 100%;
        }
    </style>
<meta name="csrf-token" content="{{ csrf_token() }}">
    @stack('styles')
</head>

<body>

<div class="dashBoradPageBody">

    @include('vshop.seller.partials.header')

    <div class="ddashboardSidebarOverlay"></div>

    <div class="dashboard-bottom-wrapper">

        @include('vshop.seller.partials.sidebar')

        <main class="dashboardPagesContent">
            @yield('content')
        </main>

    </div>

</div>

{{-- ================= JS ================= --}}

{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

{{-- Bootstrap --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

{{-- Plugins --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>

{{-- DataTables --}}
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

{{-- ApexCharts --}}
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

{{-- ================= SAFE GLOBAL SCRIPT ================= --}}
<script>
document.addEventListener("DOMContentLoaded", function () {

    // Sidebar safe toggle
    const menuItems = document.querySelectorAll(".dasboardMenuList li");

    if(menuItems.length){
        menuItems.forEach(item => {
            item.addEventListener("click", function(e){
                e.stopPropagation();
                menuItems.forEach(el => el.classList.remove("show"));
                this.classList.add("show");
            });
        });
    }

});
</script>

{{-- ================= SAFE WRAPPER FOR main.js ================= --}}
<script>
window.safeSetHTML = function(id, value){
    let el = document.getElementById(id);
    if(el){
        el.innerHTML = value;
    }
};
</script>

{{-- MAIN JS --}}
<script src="{{ asset('vtravelassets/js/main.js') }}"></script>

@stack('scripts')

</body>
</html>