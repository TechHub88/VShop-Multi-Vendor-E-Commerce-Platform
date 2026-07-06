@extends('layouts.auth', ['title' => 'Register'])

@section('content')

<div class="col-xl-12">
    <div class="row">
        <div class="col-md-4 mx-auto">

            <div class="card">
                <div class="card-body">
                    <div class="mb-0 p-0 p-lg-3">
                        <div class="mb-0 border-0 p-md-4 p-lg-0">
                            <div class="mb-3 p-0 text-center">
                              <div class="auth-brand">
                                    <a href="{{ route('second', [ 'dashboard' , 'index']) }}" class="logo logo-light">
                                        <span class="logo-lg">
                                           <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="50">
                                        </span>
                                    </a>
                                    <a href="{{ route('second', [ 'dashboard' , 'index']) }}" class="logo logo-dark">
                                        <span class="logo-lg">
                                            <img src="{{ asset('assets/images/logo.png') }}" alt="Logo" height="50">
                                        </span>
                                    </a>
                                </div>
                            </div>

                            <div class="auth-title-section mb-4 text-center"> 
                                <h3 class="text-primary fw-semibold mb-1" style="color: #25262B !important;">Create New Account</h3>
                                <p class="text-muted fs-14 mb-0">Sign up to unlock exclusive content, special offers, and be the first to know about exciting updates.</p>
                            </div>

                            <div class="pt-0">
                              <form action="{{ route('register') }}" method="POST" class="my-4">
    @csrf
                                    <div class="form-group mb-3">
                                        <label for="username" class="form-label">Username</label>
                                        <input class="form-control" name="username" type="text" id="username" required="" placeholder="Enter your Username">
                                    </div>

                                    <div class="form-group mb-3">
                                        <label for="emailaddress" class="form-label">Email address</label>
                                        <input class="form-control" type="email" id="emailaddress" required="" placeholder="Enter your email">
                                    </div>
        
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">Password</label>
                                        <input class="form-control" type="password" required="" id="password" placeholder="Enter your password">
                                    </div>
        
                                    <div class="form-group d-flex mb-3">
                                        <div class="col-12">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" id="checkbox-signin">
                                                <label class="form-check-label" for="checkbox-signin">I agree to the <a href="#" class="text-primary fw-medium register-text"> Terms and Conditions</a></label>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-0 row">
                                        <div class="col-12">
                                            <div class="d-grid">
                                                <button class="btn btn-primary login-btn" type="submit"> Register</button>
                                            </div>
                                        </div>
                                    </div>
                                </form>

                            </div>

                            <!--<div class="saprator my-4"><span>or continue with email</span></div>-->

                            
                            <div class="text-center text-muted mt-4">
                                <p class="mb-0">Already have an account ?<a class='text-primary ms-2 fw-medium register-text' href="{{ route('second', [ 'auth' , 'login']) }}">Login here</a></p>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!--<div class="col-xl-7 d-none d-xl-inline-block">-->
<!--    <div class="account-page-bg rounded-4">-->
<!--        <div class="text-center">-->
<!--            <div class="auth-image">-->
<!--                 <img src="{{ asset('assets/images/bgimage.jpg') }}" class="mx-auto img-fluid" alt="images">-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->

@endsection
