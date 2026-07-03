<!DOCTYPE html>
<html lang="en">

<head>
    <script src="https://code.iconify.design/3/3.1.0/iconify.min.js"></script>
    <script src="https://code.iconify.design/iconify-icon/2.1.0/iconify-icon.min.js"></script>
    @include('layouts.partials.title-meta', ['title' => $title])

    @include('layouts.partials.head-css')
    <link href="{{ asset('assets/css/customer-admin-style.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body data-menu-color="dark" data-sidebar="default">

    <div id="app-layout">

        @include('layouts.partials/topbar')
        @include('layouts.partials/sidebar')

        <div class="content-page">

            <div class="content">

                @yield('content')

            </div>
            
            @include('layouts.partials/footer')

        </div>

    </div>

    @include('layouts.partials/vendor')

</body>

</html>