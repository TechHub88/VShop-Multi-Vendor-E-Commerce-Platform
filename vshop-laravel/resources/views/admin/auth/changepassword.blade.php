@extends('layouts.vertical', ['title' => 'Change Password'])

@section('content')

<style>
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
</style>
<div class="page-content">

    <div class="container-fluid pt-3">

        {{-- Page Title --}}
        <div class="d-flex align-items-center mb-4">
            <h3 class="mb-0">Change Password</h3>
        </div>

        <div class="bg-white shadow-sm rounded p-4">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <form action="{{ route('admin.update.password') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Old Password</label>
                        <input type="password" name="old_password" class="form-control form-control-lg" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control form-control-lg" required>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control form-control-lg" required>
                    </div>
                </div>

                <button class="btn btn-primary btn-lg px-5">Update Password</button>
            </form>

        </div>

    </div>
</div>
@endsection
