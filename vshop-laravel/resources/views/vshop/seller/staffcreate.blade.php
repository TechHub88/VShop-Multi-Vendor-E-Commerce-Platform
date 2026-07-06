@extends('vshop.seller.main')
<style>
/* ========== ALL YOUR ORIGINAL STYLES (keep unchanged) ========== */
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    width: 35px;
    height: 38px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
    padding: 9px !important;
}
.btn-danger {
    padding: 9px !important;
}
.btn-secondary {
    padding: 9px !important;
}
.permissions-container {
    max-height: 500px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 15px;
    background: #ffffff;
}
.permissions-container::-webkit-scrollbar { width: 8px; }
.permissions-container::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
.permissions-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
.permissions-container::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
.permission-group {
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #e2e8f0;
}
.permission-group:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}
.permission-group-title {
    font-weight: 600;
    color: #1e293b;
    margin-bottom: 12px;
    display: block;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: #f8fafc;
    padding: 8px 12px;
    border-radius: 4px;
    border-left: 3px solid #3b82f6;
}
.permission-items {
    display: block;
    padding-left: 15px;
}
.custom-check {
    margin-bottom: 8px;
}
.custom-check label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    font-size: 13px;
    padding: 4px 0;
    color: #334155;
}
.custom-check input[type="checkbox"] {
    width: 16px !important;
    height: 16px !important;
    cursor: pointer !important;
    opacity: 1 !important;
    visibility: visible !important;
    display: inline-block !important;
    position: relative !important;
    margin: 0 !important;
    -webkit-appearance: checkbox !important;
    -moz-appearance: checkbox !important;
    appearance: checkbox !important;
}
.custom-check input[type="radio"] {
    width: 16px !important;
    height: 16px !important;
    cursor: pointer !important;
    opacity: 1 !important;
    visibility: visible !important;
    display: inline-block !important;
    position: relative !important;
    margin: 0 !important;
    -webkit-appearance: radio !important;
    -moz-appearance: radio !important;
    appearance: radio !important;
}
.section-select-all {
    margin-bottom: 10px;
    padding-bottom: 8px;
    border-bottom: 1px dashed #cbd5e1;
}
.section-select-all label {
    font-weight: 600;
    color: #2563eb;
    font-size: 12px;
}
.section-select-all input[type="checkbox"] {
    width: 16px !important;
    height: 16px !important;
    cursor: pointer !important;
    -webkit-appearance: checkbox !important;
    -moz-appearance: checkbox !important;
    appearance: checkbox !important;
}
.password-wrapper {
    position: relative;
}
.password-wrapper i {
    position: absolute;
    right: 10px;
    top: 10px;
    cursor: pointer;
}
small.text-danger {
    font-size: 11px;
    margin-top: 4px;
    display: block;
}
small.text-muted {
    font-size: 11px;
}
.form-control.is-invalid {
    border-color: #dc3545;
}
.form-control.is-valid {
    border-color: #198754;
}
.quick-role-section {
    background: #f8fafc;
    padding: 15px;
    border-radius: 6px;
    margin-top: 15px;
    border: 1px solid #e2e8f0;
}
.quick-role-section .custom-check {
    margin-bottom: 8px;
}
hr {
    margin: 20px 0;
}
.table td {
    vertical-align: middle;
}
.badge-count {
    font-size: 11px;
    padding: 3px 8px;
}
</style>

@section('content')

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold text-primary" id="pageHeader">
        Create Staff / Manager
    </h5>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" id="successMsg">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

@if($errors->any())
<div class="alert alert-danger">
    <ul class="mb-0">
        @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<form method="POST" action="{{ route('seller.staff.store') }}" autocomplete="off" id="staffForm">
<input type="hidden" name="id" id="staffId">
<input type="text" name="fake_email" style="display:none">
<input type="password" name="fake_password" style="display:none">

@csrf

<div class="card shadow-sm">
<div class="card-body">

<div class="row">
<div class="col-md-6 mb-3">
    <label class="fw-semibold">Full Name <span class="text-danger">*</span></label>
    <input type="text" name="name" class="form-control" placeholder="Enter full name">
    <small class="text-danger error-name"></small>
</div>

<div class="col-md-6 mb-3">
    <label class="fw-semibold">Email Address <span class="text-danger">*</span></label>
    <input type="email" name="email" id="emailInput" class="form-control" placeholder="Enter email address">
    <small class="text-danger error-email"></small>
</div>

<div class="col-md-6 mb-3">
    <label class="fw-semibold">Mobile Number <span class="text-danger">*</span></label>
    <input type="text" name="mobile" id="mobileInput" class="form-control" maxlength="10" placeholder="Enter 10 digit mobile number">
    <small class="text-danger" id="mobileError"></small>
</div>

<div class="col-md-6 mb-3">
    <label class="fw-semibold">Password <span class="text-danger">*</span></label>
    <div class="password-wrapper">
        <input type="password" name="password" id="passwordInput" class="form-control" placeholder="Enter password">
        <i class="bi bi-eye-slash" id="togglePassword"></i>
    </div>
    <small class="text-danger" id="passwordError"></small>
    <small class="text-muted">Minimum 6 characters with letters and numbers</small>
</div>

<div class="col-md-6 mb-3">
    <label class="fw-semibold">Role / Designation</label>
    <select name="role" class="form-control">
        <option value="manager">Manager</option>
        <option value="staff">Staff</option>
        <option value="supervisor">Supervisor</option>
        <option value="executive">Executive</option>
    </select>
</div>
</div>

<hr>

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-primary mb-0">Module Access Permissions</h5>
    <div>
        <button type="button" class="btn btn-sm btn-outline-primary" id="selectAllBtn">Select All</button>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="deselectAllBtn">Deselect All</button>
    </div>
</div>
<p class="text-muted small mb-3">Select which modules and features this staff member can access</p>

<div class="permissions-container">

    <!-- PRODUCT MODULE -->
    <div class="permission-group">
        <div class="section-select-all">
            <label class="custom-check">
                <input type="checkbox" class="section-select" data-section="product">
                <strong>SELECT ALL - Product / Service Module</strong>
            </label>
        </div>
        <div class="permission-items product-section">
            <div class="custom-check"><label><input type="checkbox" name="can_manage_products" value="1" class="permission-checkbox product-perm"> Manage Products / Services (Full Access)</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_product_mis" value="1" class="permission-checkbox product-perm"> View Product MIS Dashboard</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_add_products" value="1" class="permission-checkbox product-perm"> Add New Products / Services</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_edit_products" value="1" class="permission-checkbox product-perm"> Edit Products / Services</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_delete_products" value="1" class="permission-checkbox product-perm"> Delete Products / Services</label></div>
        </div>
    </div>

    <!-- ORDERS MODULE -->
    <div class="permission-group">
        <div class="section-select-all">
            <label class="custom-check">
                <input type="checkbox" class="section-select" data-section="orders">
                <strong>SELECT ALL - Orders Module</strong>
            </label>
        </div>
        <div class="permission-items orders-section">
            <div class="custom-check"><label><input type="checkbox" name="can_manage_orders" value="1" class="permission-checkbox orders-perm"> Manage Orders (Full Access)</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_order_mis" value="1" class="permission-checkbox orders-perm"> View Order MIS</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_all_orders" value="1" class="permission-checkbox orders-perm"> View All Orders</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_handle_payments" value="1" class="permission-checkbox orders-perm"> Payment Handling</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_manage_shipping" value="1" class="permission-checkbox orders-perm"> Shipping & Delivery Settings</label></div>
        </div>
    </div>

    <!-- CUSTOMERS MODULE -->
    <div class="permission-group">
        <div class="section-select-all">
            <label class="custom-check">
                <input type="checkbox" class="section-select" data-section="customers">
                <strong>SELECT ALL - Customers Module</strong>
            </label>
        </div>
        <div class="permission-items customers-section">
            <div class="custom-check"><label><input type="checkbox" name="can_manage_customers" value="1" class="permission-checkbox customers-perm"> Manage Customers (Full Access)</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_customer_mis" value="1" class="permission-checkbox customers-perm"> View Customer MIS</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_customer_list" value="1" class="permission-checkbox customers-perm"> View Customer List</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_add_customers" value="1" class="permission-checkbox customers-perm"> Add New Customers</label></div>
        </div>
    </div>

    <!-- FINANCE MODULE -->
    <div class="permission-group">
        <div class="section-select-all">
            <label class="custom-check">
                <input type="checkbox" class="section-select" data-section="finance">
                <strong>SELECT ALL - Finance Module</strong>
            </label>
        </div>
        <div class="permission-items finance-section">
            <div class="custom-check"><label><input type="checkbox" name="can_view_finance" value="1" class="permission-checkbox finance-perm"> View Finance (Full Access)</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_earnings" value="1" class="permission-checkbox finance-perm"> View Earnings Dashboard</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_transactions" value="1" class="permission-checkbox finance-perm"> View Transaction History</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_manage_payouts" value="1" class="permission-checkbox finance-perm"> Manage Payouts</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_commission" value="1" class="permission-checkbox finance-perm"> View Commission & Fees</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_wallet" value="1" class="permission-checkbox finance-perm"> View Wallet Settlement</label></div>
        </div>
    </div>

    <!-- ENQUIRY MODULE -->
    <div class="permission-group">
        <div class="section-select-all">
            <label class="custom-check">
                <input type="checkbox" class="section-select" data-section="enquiry">
                <strong>SELECT ALL - Enquiry Module</strong>
            </label>
        </div>
        <div class="permission-items enquiry-section">
            <div class="custom-check"><label><input type="checkbox" name="can_manage_enquiries" value="1" class="permission-checkbox enquiry-perm"> Manage Enquiries (Full Access)</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_enquiry_mis" value="1" class="permission-checkbox enquiry-perm"> View Enquiry MIS</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_enquiry_list" value="1" class="permission-checkbox enquiry-perm"> View Enquiry List</label></div>
        </div>
    </div>

    <!-- ANALYTICS MODULE -->
    <div class="permission-group">
        <div class="section-select-all">
            <label class="custom-check">
                <input type="checkbox" class="section-select" data-section="analytics">
                <strong>SELECT ALL - Analytics Module</strong>
            </label>
        </div>
        <div class="permission-items analytics-section">
            <div class="custom-check"><label><input type="checkbox" name="can_view_analytics" value="1" class="permission-checkbox analytics-perm"> View Analytics (Full Access)</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_lead_analytics" value="1" class="permission-checkbox analytics-perm"> Lead Analytics</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_referral_analytics" value="1" class="permission-checkbox analytics-perm"> Referral Analytics</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_sales_overview" value="1" class="permission-checkbox analytics-perm"> Sales Overview</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_conversion_funnel" value="1" class="permission-checkbox analytics-perm"> Conversion Funnel</label></div>
            <div class="custom-check"><label><input type="checkbox" name="can_view_customer_insights" value="1" class="permission-checkbox analytics-perm"> Customer Insights</label></div>
        </div>
    </div>

    <!-- MARKETPLACE MODULE -->
    <div class="permission-group">
        <div class="permission-items">
            <div class="custom-check"><label><input type="checkbox" name="can_view_marketplace" value="1" class="permission-checkbox"> View Market Place</label></div>
        </div>
    </div>

    <!-- GRIEVANCE MODULE -->
    <div class="permission-group">
        <div class="permission-items">
            <div class="custom-check"><label><input type="checkbox" name="can_view_grievance" value="1" class="permission-checkbox"> View Grievance</label></div>
        </div>
    </div>

    <!-- STAFF MANAGEMENT -->
    <div class="permission-group">
        <div class="permission-items">
            <div class="custom-check"><label><input type="checkbox" name="can_manage_staff" value="1" class="permission-checkbox"> Manage Staff (Create / Edit / Delete)</label></div>
        </div>
    </div>

    <!-- PROFILE MODULE -->
    <div class="permission-group">
        <div class="permission-items">
            <div class="custom-check"><label><input type="checkbox" name="can_view_seller_profile" value="1" class="permission-checkbox"> View Seller Profile</label></div>
        </div>
    </div>

</div>

<!-- Quick Role Selection -->
<div class="quick-role-section">
    <div class="custom-check">
        <label><input type="radio" name="quick_role" value="all" id="fullAccessRadio"> <strong>Full Access</strong> - Grant all permissions (Administrator access)</label>
    </div>
    <div class="custom-check">
        <label><input type="radio" name="quick_role" value="limited" id="customAccessRadio" checked> <strong>Custom Access</strong> - Manually select permissions below</label>
    </div>
</div>

<hr>

<div class="d-flex gap-2">
    <button type="submit" class="btn btn-primary px-4" id="submitBtn">Create Staff</button>
    <button type="button" class="btn btn-secondary px-4" onclick="resetFormToCreate()">Reset</button>
</div>

</div>
</div>

</form>

<!-- Staff List Table -->
<div class="card shadow-sm mt-4">
<div class="card-body">
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold text-primary mb-0">Staff List</h5>
    <span class="badge bg-primary">{{ count($staffs) }} Total Staff</span>
</div>

<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-light">
<tr>
    <th width="50">#</th><th>Name</th><th>Email</th><th>Mobile</th><th>Role</th><th>Permissions</th><th width="120">Action</th>
</tr>
</thead>
<tbody>
@forelse($staffs as $key => $staff)
@php
    // SAFELY DECODE PERMISSIONS (JSON string → array)
    $perms = [];
    if (!empty($staff->permissions)) {
        if (is_string($staff->permissions)) {
            $perms = json_decode($staff->permissions, true);
        } elseif (is_array($staff->permissions)) {
            $perms = $staff->permissions;
        }
    }
    if (!is_array($perms)) $perms = [];

    // Extract enabled permissions and convert to readable labels
    $enabledLabels = [];
    foreach ($perms as $permKey => $permValue) {
        if ($permValue == 1 || $permValue === true) {
            $label = str_replace('_', ' ', $permKey);
            $label = ucwords($label);
            $enabledLabels[] = $label;
        }
    }
@endphp
<tr>
    <td>{{ $key+1 }}</td>
    <td>{{ $staff->name }}</td>
    <td>{{ $staff->email }}</td>
    <td>{{ $staff->mobile }}</td>
    <td><span class="badge bg-info text-dark">{{ ucfirst($staff->role) }}</span></td>
    <td>
        @if(count($enabledLabels) > 0)
            <span class="text-muted small">{{ implode(', ', $enabledLabels) }}</span>
        @else
            <span class="badge bg-secondary badge-count">No permissions</span>
        @endif
    </td>
    <td>
        <button type="button" class="btn btn-sm btn-primary editBtn mb-1"
            data-id="{{ $staff->id }}"
            data-name="{{ $staff->name }}"
            data-email="{{ $staff->email }}"
            data-mobile="{{ $staff->mobile }}"
            data-role="{{ $staff->role }}"
            data-permissions='{{ json_encode($perms) }}'>
            Edit
        </button>
        <form action="{{ route('seller.staff.delete', $staff->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('Delete this staff? This action cannot be undone.')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-danger">Delete</button>
        </form>
    </td>
</tr>
@empty
<tr><td colspan="7" class="text-center text-muted py-4">No staff members found</td></tr>
@endforelse
</tbody>
</table>
</div>
</div>
</div>

</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<script>
document.addEventListener("DOMContentLoaded", function () {
    // Mobile validation
    const mobile = document.getElementById('mobileInput');
    const mobileError = document.getElementById('mobileError');
    if(mobile) {
        mobile.addEventListener('input', function(){
            this.value = this.value.replace(/[^0-9]/g, '').slice(0,10);
            if(this.value.length === 10) {
                mobileError.innerText = '';
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            } else if(this.value.length > 0) {
                mobileError.innerText = 'Enter valid 10 digit mobile number';
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                mobileError.innerText = '';
                this.classList.remove('is-invalid', 'is-valid');
            }
        });
    }

    // Password toggle
    const toggle = document.getElementById('togglePassword');
    const password = document.getElementById('passwordInput');
    if(toggle && password) {
        toggle.addEventListener('click', function(){
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bi-eye');
            this.classList.toggle('bi-eye-slash');
        });
    }

    // --- AUTO CLEAR ERROR MESSAGES ON INPUT ---
    const clearFieldError = (fieldSelector, errorSelector) => {
        const field = document.querySelector(fieldSelector);
        const errorSpan = document.querySelector(errorSelector);
        if (field && errorSpan) {
            field.addEventListener('input', function() {
                errorSpan.innerText = '';
                this.classList.remove('is-invalid');
                // Optionally add is-valid only if value is not empty
                if (this.value.trim() !== '') {
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                }
            });
        }
    };
    clearFieldError('[name="name"]', '.error-name');
    clearFieldError('[name="email"]', '.error-email');
    clearFieldError('[name="password"]', '#passwordError');
    // mobile is already handled above, but we can also link it:
    if (mobile && mobileError) {
        mobile.addEventListener('input', function() {
            // already clearing inside mobile validation, but ensure it also removes error on valid input
            if (this.value.length === 10) mobileError.innerText = '';
        });
    }

    // Full Access Radio
    const fullAccessRadio = document.getElementById('fullAccessRadio');
    const customAccessRadio = document.getElementById('customAccessRadio');
    if(fullAccessRadio && customAccessRadio) {
        fullAccessRadio.addEventListener('change', function(){
            if(this.checked) {
                document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
                document.querySelectorAll('.section-select').forEach(sec => sec.checked = true);
            }
        });
        customAccessRadio.addEventListener('change', function(){});
    }

    // Section Select All
    document.querySelectorAll('.section-select').forEach(selectAll => {
        selectAll.addEventListener('change', function() {
            const section = this.dataset.section;
            const checkboxes = document.querySelectorAll(`.${section}-section .permission-checkbox`);
            checkboxes.forEach(cb => cb.checked = this.checked);
        });
    });

    // Update section "select all" when individual checkbox changes
    document.querySelectorAll('.permission-checkbox').forEach(cb => {
        cb.addEventListener('change', function() {
            let section = null;
            if(this.classList.contains('product-perm')) section = 'product';
            else if(this.classList.contains('orders-perm')) section = 'orders';
            else if(this.classList.contains('customers-perm')) section = 'customers';
            else if(this.classList.contains('finance-perm')) section = 'finance';
            else if(this.classList.contains('enquiry-perm')) section = 'enquiry';
            else if(this.classList.contains('analytics-perm')) section = 'analytics';
            if(section) {
                const sectionCheckboxes = document.querySelectorAll(`.${section}-section .permission-checkbox`);
                const allChecked = Array.from(sectionCheckboxes).every(c => c.checked);
                const sectionSelectAll = document.querySelector(`.section-select[data-section="${section}"]`);
                if(sectionSelectAll) sectionSelectAll.checked = allChecked;
            }
            if(!this.checked && fullAccessRadio && fullAccessRadio.checked) {
                customAccessRadio.checked = true;
                fullAccessRadio.checked = false;
            }
        });
    });

    // Select All / Deselect All buttons
    document.getElementById('selectAllBtn')?.addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = true);
        document.querySelectorAll('.section-select').forEach(sec => sec.checked = true);
        if(customAccessRadio?.checked) { customAccessRadio.checked = false; if(fullAccessRadio) fullAccessRadio.checked = true; }
    });
    document.getElementById('deselectAllBtn')?.addEventListener('click', function() {
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.section-select').forEach(sec => sec.checked = false);
        if(fullAccessRadio?.checked) { fullAccessRadio.checked = false; if(customAccessRadio) customAccessRadio.checked = true; }
    });

    // Auto-clear password autofill
    window.addEventListener('load', () => {
        const email = document.getElementById('emailInput');
        const pwd = document.getElementById('passwordInput');
        if(email && email.value && /^[0-9]{6,}$/.test(email.value)) email.value = '';
        if(pwd && pwd.value) pwd.value = '';
        setTimeout(() => { if(email) email.value = ''; if(pwd) pwd.value = ''; }, 100);
    });
});

// Edit staff (rest remains unchanged)
document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', function(){
        window.scrollTo({ top: 0, behavior: 'smooth' });
        document.querySelector('[name="name"]').value = this.dataset.name;
        document.querySelector('[name="email"]').value = this.dataset.email;
        document.querySelector('[name="mobile"]').value = this.dataset.mobile;
        document.querySelector('[name="role"]').value = this.dataset.role;
        document.getElementById('staffId').value = this.dataset.id;
        document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
        document.querySelectorAll('.section-select').forEach(sec => sec.checked = false);
        if(this.dataset.permissions && this.dataset.permissions !== '[]' && this.dataset.permissions !== 'null') {
            try {
                const perms = JSON.parse(this.dataset.permissions);
                for(let key in perms) {
                    if(perms[key] == 1) {
                        let checkbox = document.querySelector(`[name="${key}"]`);
                        if(checkbox) checkbox.checked = true;
                    }
                }
                const sections = ['product','orders','customers','finance','enquiry','analytics'];
                sections.forEach(section => {
                    const secCheckboxes = document.querySelectorAll(`.${section}-section .permission-checkbox`);
                    const allChecked = Array.from(secCheckboxes).every(cb => cb.checked);
                    const secSelectAll = document.querySelector(`.section-select[data-section="${section}"]`);
                    if(secSelectAll) secSelectAll.checked = allChecked;
                });
                const allPerms = document.querySelectorAll('.permission-checkbox');
                const allSelected = Array.from(allPerms).every(cb => cb.checked);
                const fullRadio = document.getElementById('fullAccessRadio');
                const customRadio = document.getElementById('customAccessRadio');
                if(allSelected && fullRadio && customRadio) {
                    fullRadio.checked = true;
                    customRadio.checked = false;
                } else if(fullRadio && customRadio) {
                    fullRadio.checked = false;
                    customRadio.checked = true;
                }
            } catch(e) { console.error(e); }
        }
        document.getElementById('passwordInput').value = '';
        document.getElementById('submitBtn').innerHTML = 'Update Staff';
        document.getElementById('pageHeader').innerText = "Update Staff / Manager";
    });
});

function resetFormToCreate(){
    document.getElementById('staffId').value = '';
    document.querySelector('[name="name"]').value = '';
    document.querySelector('[name="email"]').value = '';
    document.querySelector('[name="mobile"]').value = '';
    document.querySelector('[name="password"]').value = '';
    document.querySelector('[name="role"]').value = 'manager';
    document.querySelectorAll('.permission-checkbox').forEach(cb => cb.checked = false);
    document.querySelectorAll('.section-select').forEach(sec => sec.checked = false);
    document.getElementById('submitBtn').innerHTML = 'Create Staff';
    document.getElementById('pageHeader').innerText = "Create Staff / Manager";
    document.querySelectorAll('.text-danger').forEach(err => err.innerText = '');
    document.querySelectorAll('.form-control').forEach(input => input.classList.remove('is-invalid', 'is-valid'));
    const full = document.getElementById('fullAccessRadio');
    const custom = document.getElementById('customAccessRadio');
    if(full && custom) { full.checked = false; custom.checked = true; }
}

// Form validation
const staffForm = document.getElementById('staffForm');
if(staffForm) {
    staffForm.addEventListener('submit', function(e) {
        let isValid = true;
        document.querySelectorAll('.text-danger').forEach(err => err.innerText = '');
        document.querySelectorAll('.form-control').forEach(inp => inp.classList.remove('is-invalid'));
        const name = document.querySelector('[name="name"]').value.trim();
        if(!name) {
            document.querySelector('.error-name').innerText = 'Full name is required';
            document.querySelector('[name="name"]').classList.add('is-invalid');
            isValid = false;
        }
        const email = document.querySelector('[name="email"]').value.trim();
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if(!email || !emailRegex.test(email)) {
            document.querySelector('.error-email').innerText = 'Valid email address is required';
            document.querySelector('[name="email"]').classList.add('is-invalid');
            isValid = false;
        }
        const mobile = document.getElementById('mobileInput').value;
        if(!mobile || mobile.length !== 10) {
            document.getElementById('mobileError').innerText = 'Valid 10 digit mobile number is required';
            document.getElementById('mobileInput').classList.add('is-invalid');
            isValid = false;
        }
        const staffId = document.getElementById('staffId').value;
        const password = document.getElementById('passwordInput').value;
        const passRegex = /^(?=.*[A-Za-z])(?=.*\d).{6,}$/;
        if(!staffId) {
            if(!password) {
                document.getElementById('passwordError').innerText = 'Password is required for new staff';
                document.getElementById('passwordInput').classList.add('is-invalid');
                isValid = false;
            } else if(!passRegex.test(password)) {
                document.getElementById('passwordError').innerText = 'Password must be at least 6 characters with letters and numbers';
                document.getElementById('passwordInput').classList.add('is-invalid');
                isValid = false;
            }
        } else {
            if(password && !passRegex.test(password)) {
                document.getElementById('passwordError').innerText = 'Password must be at least 6 characters with letters and numbers';
                document.getElementById('passwordInput').classList.add('is-invalid');
                isValid = false;
            }
        }
        if(!isValid) {
            e.preventDefault();
            const firstError = document.querySelector('.is-invalid');
            if(firstError) firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            return false;
        }
        return true;
    });
}

function goBack() { window.history.back(); }
setTimeout(function(){ let msg = document.getElementById('successMsg'); if(msg) msg.style.display = 'none'; }, 5000);
</script>
@endsection
