@extends('layouts.auth')

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
                                    <!-- Brand Logo / Name -->
                                </div>
                            </div>

                            <div class="auth-title-section mb-4 text-center">
                                <h3 class="text-primary fw-semibold mb-1" style="color: #003a80 !important;">
                                    Two-Factor Authentication
                                </h3>
                                <p class="text-muted mt-2">
                                    Please enter the 6-digit OTP code to verify your identity.
                                </p>
                            </div>

                            <div class="pt-0">
                                @if(session('2fa_otp'))
                                    <div class="alert alert-info text-center">
                                        <strong>Debug OTP (Manual Mode):</strong>
                                        <h4 class="mb-0 mt-1 letter-spacing-2 fw-bold text-dark">{{ session('2fa_otp') }}</h4>
                                        <small class="text-muted">Enter this code below to proceed.</small>
                                    </div>
                                @endif

                                <form method="POST" action="{{ route('admin.2fa.verify') }}" class="my-4">
                                    @csrf
                                    
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            @foreach ($errors->all() as $error)
                                                <p class="mb-0">{{ $error }}</p>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="form-group mb-4">
                                        <label for="otp" class="form-label">Verification Code (OTP)</label>
                                        <input class="form-control text-center fs-4 fw-bold font-monospace" 
                                               type="text" name="otp" id="otp" 
                                               maxlength="6" required autocomplete="off"
                                               placeholder="XXXXXX" autofocus>
                                    </div>
        
                                    <div class="form-group mb-0">
                                        <div class="d-grid">
                                            <button class="btn btn-primary fw-semibold verify-btn" type="submit"> 
                                               Verify & Login
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <div class="text-center mt-3">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-link text-decoration-none text-muted btn-sm">
                                            Cancel & Back to Login
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.verify-btn {
    padding: 12px;
    border-radius: 8px;
    font-size: 16px;
    background-color: #004392 !important;
    border-color: #004392 !important;
    transition: all 0.3s ease;
}

.verify-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.letter-spacing-2 {
    letter-spacing: 0.15rem;
}
</style>
@endsection
