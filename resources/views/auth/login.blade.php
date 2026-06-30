@extends('layouts.auth')


@section('content')
<div class="col-xl-12">
    <div class="row">
        <div class="col-md-4 mx-auto">
            
            <!-- Main Login Card -->
            <div class="card">
                <div class="card-body">

                    
                    <div class="mb-0 p-0 p-lg-3">
                        <div class="mb-0 border-0 p-md-4 p-lg-0">
                            <div class="mb-3 p-0 text-center">
                                <div class="auth-brand">
                                    
                                </div>
                            </div>

                            <div class="auth-title-section mb-4 text-center">
                                <h3 class="text-primary fw-semibold mb-1" style="color: #003a80 !important;">
                                    Admin Login
                                </h3>
                               
                                <div class="mt-2">
                                   
                                </div>
                            </div>

                            <div class="pt-0">
                                <form method="POST" action="{{ route('admin.login.store') }}" class="my-4">
                                    @csrf
                                    
                                   
                                    
                                    @if ($errors->any())
                                        <div class="alert alert-danger">
                                            @foreach ($errors->all() as $error)
                                                <p class="mb-0">{{ $error }}</p>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Email address</label>
                                        <input class="form-control" type="email" name="email" id="email" 
                                               value="{{ old('email', 'admin@gmail.com') }}"
                                               required placeholder="Enter your email">
                                       
                                    </div>
        
                                    <div class="form-group mb-3">
                                        <label for="password" class="form-label">Password</label>
                                       <input type="password"name="password"class="form-control"value="admin123"required>
                                      
                                    </div>
        
                                    <div class="form-group mb-3">
                                        <div class="form-check">
                                            <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                            <label class="form-check-label" for="remember">Remember me</label>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group mb-0">
                                        <div class="d-grid">
                                            <button class="btn btn-primary fw-semibold login-btn" type="submit"> 
                                               Login Now
                                            </button>
                                        </div>
                                    </div>
                                   
                                </form>

                                <!-- Switch between admin types -->
                                
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click to copy functionality for demo credentials
    const emailField = document.getElementById('email');
    const passwordField = document.getElementById('password');
    
    // Highlight demo credentials on focus
    emailField.addEventListener('focus', function() {
        this.select();
    });
    
    passwordField.addEventListener('focus', function() {
        this.select();
    });
    
    // Show password toggle (optional)
    const showPasswordToggle = document.createElement('span');
    showPasswordToggle.innerHTML = '<i class="mdi mdi-eye"></i>';
    showPasswordToggle.className = 'position-absolute end-0 top-50 translate-middle-y me-3 cursor-pointer';
    showPasswordToggle.style.cursor = 'pointer';
    showPasswordToggle.style.zIndex = '10';
    
    const passwordGroup = passwordField.parentElement;
    passwordGroup.style.position = 'relative';
    passwordGroup.appendChild(showPasswordToggle);
    
    showPasswordToggle.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        this.innerHTML = type === 'password' ? '<i class="mdi mdi-eye"></i>' : '<i class="mdi mdi-eye-off"></i>';
    });
});
</script>

<style>
.form-text {
    font-size: 12px;
    margin-top: 4px;
}

.alert-light {
    background-color: rgba(13, 110, 253, 0.05);
    border-color: rgba(13, 110, 253, 0.2) !important;
}

.login-btn {
    padding: 12px;
    border-radius: 8px;
    font-size: 16px;
    transition: all 0.3s ease;
}

.login-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.cursor-pointer {
    cursor: pointer;
}
</style>
<style>
    .account-page {
        background: #004392;
    }
    .account-page::before {
        background-color: #004392;
    }
    .login-btn {
        background-color: #004392 !important;
        border-color: #004392 !important;
    }
</style>
@endsection