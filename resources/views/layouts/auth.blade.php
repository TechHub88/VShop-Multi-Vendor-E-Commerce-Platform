<!DOCTYPE html>
<html lang="en">

<head>
    @include('layouts.partials/title-meta')
    @include('layouts.partials/head-css')
    
    <style>
        .account-page {
            background-image: url(https://kidzz.in/mafatlal/public/assets/images/bgimage.jpg);
            background-repeat: no-repeat;
            background-size: cover;
            position: relative;
        }
        .account-page::before {
            content: "";
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #0000007d;
            top: 0;
            left: 0;
        }
        .form-check-input:checked {
            background-color: #25262B !important;
            border-color: #25262B !important;
        }
        .login-btn {
            background-color: #25262B !important;
            border-color: #25262B !important;
        }
        .register-text {
            color: #25262B !important;
        }
    </style>
</head>

<body>

    <!-- Begin page -->
    <div class="account-page">
        <div class="container-fluid p-0 account-page-inner">

            <div class="row align-items-center g-0 px-3 py-3 vh-100">
                @yield('content')
            </div>
        </div>
    </div>
    </div>

    @include('layouts.partials/vendor')

</body>

</html>