@extends('vshop.seller.main')

@section('content')

<div class="container-fluid">

<h4 class="fw-bold mb-4 text-primary">Create Staff / Manager</h4>

<form method="POST" action="{{ route('seller.staff.store') }}">
@csrf

<div class="card shadow-sm">
<div class="card-body">

<div class="row">

<div class="col-md-6 mb-3">
<label class="fw-semibold">Name</label>
<input type="text" name="name" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="fw-semibold">Email</label>
<input type="email" name="email" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="fw-semibold">Mobile</label>
<input type="text" name="mobile" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="fw-semibold">Password</label>
<input type="password" name="password" class="form-control" required>
</div>

<div class="col-md-6 mb-3">
<label class="fw-semibold">Role</label>
<select name="role" class="form-control">
<option value="manager">Manager</option>
<option value="staff">Staff</option>
</select>
</div>

</div>

<hr>

<h5 class="fw-bold mb-3 text-primary">Permissions</h5>

<div class="row">

<!-- QUICK ROLE -->
<div class="col-md-6">

<label class="fw-semibold mb-2">Quick Role</label>

<div class="custom-check-box">
    <input type="radio" class="role-select" name="quick_role" value="all" id="roleAll">
    <label for="roleAll">Full Access (Manager)</label>
</div>

<div class="custom-check-box">
    <input type="radio" class="role-select" name="quick_role" value="limited" id="roleLimited">
    <label for="roleLimited">Limited Access (Staff)</label>
</div>

</div>

<!-- CUSTOM PERMISSIONS -->
<div class="col-md-6">

<label class="fw-semibold mb-2">Custom Permissions</label>

<div class="custom-check-box">
    <input type="checkbox" class="permission-checkbox" name="can_manage_orders" value="1" id="orders">
    <label for="orders">Manage Orders</label>
</div>

<div class="custom-check-box">
    <input type="checkbox" class="permission-checkbox" name="can_view_finance" value="1" id="finance">
    <label for="finance">View Finance</label>
</div>

<div class="custom-check-box">
    <input type="checkbox" class="permission-checkbox" name="can_manage_products" value="1" id="products">
    <label for="products">Manage Products</label>
</div>

</div>

</div>

<hr>

<button class="btn btn-primary px-4">
<i class="fa-solid fa-user-plus me-1"></i>
Create Staff
</button>

</div>
</div>

</form>

</div>

@endsection


@section('scripts')

<script>
document.querySelectorAll('.role-select').forEach(radio => {

    radio.addEventListener('change', function(){

        if(this.value === 'all'){
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
        }

        if(this.value === 'limited'){
            document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        }

    });

});
</script>

@endsection


@section('styles')

<style>

/* IMPORTANT: FORCE SHOW */
.custom-check-box input {
    position: absolute;
    opacity: 0;
}

/* LABEL */
.custom-check-box {
    margin-bottom: 10px;
}

.custom-check-box label {
    position: relative;
    padding-left: 30px;
    cursor: pointer;
    font-weight: 500;
}

/* BOX */
.custom-check-box label::before {
    content: "";
    position: absolute;
    left: 0;
    top: 2px;
    width: 18px;
    height: 18px;
    border: 2px solid #0d6efd;
    border-radius: 4px;
    background: #fff;
}

/* CHECK */
.custom-check-box input:checked + label::after {
    content: "✔";
    position: absolute;
    left: 3px;
    top: 0px;
    font-size: 14px;
    color: #0d6efd;
}

/* RADIO */
.custom-check-box input[type="radio"] + label::before {
    border-radius: 50%;
}

.custom-check-box input[type="radio"]:checked + label::after {
    content: "";
    width: 10px;
    height: 10px;
    background: #0d6efd;
    border-radius: 50%;
    position: absolute;
    left: 4px;
    top: 6px;
}

</style>

@endsection