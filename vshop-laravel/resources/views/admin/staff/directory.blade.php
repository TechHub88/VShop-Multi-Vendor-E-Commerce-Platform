@extends('layouts.vertical', ['title' => 'Staff Directory'])

@section('content')

<style>
    /*.container-fluid {*/
    /*    padding: 1.5rem 2rem 2rem 2rem !important;*/
    /*}*/
    
    .table-custom thead th {
        background: #004392 !important;
        color: #fff !important;
        border: 1px solid #fff;
    }
    
    .main-card {
        border: none !important;
        box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.05) !important;
        border-radius: 1.25rem !important;
        background: #ffffff;
        overflow: hidden;
    }
    
    .stat-card {
        background: #ffffff;
        border: none;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
    }
    
    .action-icon {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 34px;
        height: 34px;
        border-radius: 8px;
        background: #f8f9fa;
        color: #6c757d;
        transition: all 0.2s ease;
        text-decoration: none;
        margin: 0 2px;
        cursor: pointer;
        border: none;
    }
    
    .action-icon:hover {
        background: #0d6efd;
        color: white;
        transform: translateY(-2px);
    }
    
    .status-toggle {
        position: relative;
        display: inline-block;
        width: 50px;
        height: 24px;
    }
    
    .status-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }
    
    .toggle-slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #dc3545;
        transition: 0.3s;
        border-radius: 34px;
    }
    
    .toggle-slider:before {
        position: absolute;
        content: "";
        height: 18px;
        width: 18px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: 0.3s;
        border-radius: 50%;
    }
    
    input:checked + .toggle-slider {
        background-color: #198754;
    }
    
    input:checked + .toggle-slider:before {
        transform: translateX(26px);
    }
    
    .staff-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
    }
    
    .table-custom {
        margin-bottom: 0;
    }
    
    .table-custom thead th {
        background: #f8fafc;
        color: #1e293b;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 1rem;
        border-bottom: 2px solid #eef2f7;
    }
    
    .table-custom tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: #334155;
        font-size: 0.85rem;
    }
    
    .badge-status {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    
    .badge-active {
        background: #d1e7dd;
        color: #0f5132;
    }
    
    .badge-inactive {
        background: #f8d7da;
        color: #842029;
    }
    
    .badge-suspended {
        background: #fff3cd;
        color: #856404;
    }
    
    .form-label-custom {
        font-size: 0.7rem;
        font-weight: 700;
        color: #1e2a3e;
        margin-bottom: 0.45rem;
        text-transform: uppercase;
        letter-spacing: 0.6px;
    }
    
    .form-control, .form-select {
        border: 1px solid #e2e8f0;
        border-radius: 0.6rem;
        padding: 0.55rem 0.9rem;
        font-size: 0.85rem;
    }
    
    .btn-royal-blue {
        background: #0d6efd;
        border: none;
        color: white;
        font-weight: 500;
        padding: 0.5rem 1.2rem;
        border-radius: 0.6rem;
    }
    
    .btn-royal-blue:hover {
        background: #0b5ed7;
        color: #fff;
    }
    
    .btn-outline-royal {
        border: 1.5px solid #0d6efd;
        color: #0d6efd;
        background: transparent;
        font-weight: 500;
        padding: 0.5rem 1.2rem;
        border-radius: 0.6rem;
    }
    
    .btn-outline-royal:hover {
        background: #0d6efd;
        color: white;
    }
    
    @media (max-width: 768px) {
        .container-fluid {
            padding: 1rem !important;
        }
        .action-icon {
            width: 28px;
            height: 28px;
        }
    }
</style>

<div class="container-fluid py-3">

    <div class="row mb-4 align-items-center">
        <div class="col-md-6">
            <h4 class="fw-bold text-dark mb-1">Staff Directory</h4>
            <p class="text-muted mb-0 small">Manage employees, payroll and access permissions</p>
        </div>
        <div class="col-md-6 text-md-end">
            <a href="{{ route('admin.staff.create') }}" class="btn btn-royal-blue rounded-3">
                <i class="bi bi-person-plus me-2"></i> Add New Staff
            </a>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Total Staff</span>
                        <h2 class="fw-bold mb-0 mt-1">{{ $totalStaff ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon" style="background: #e7f1ff; color: #0d6efd;">
                        <i class="bi bi-people-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Active Staff</span>
                        <h2 class="fw-bold mb-0 mt-1 text-success">{{ $activeStaff ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon" style="background: #d1e7dd; color: #198754;">
                        <i class="bi bi-person-check-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Inactive Staff</span>
                        <h2 class="fw-bold mb-0 mt-1">{{ $inactiveStaff ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon" style="background: #f8d7da; color: #dc3545;">
                        <i class="bi bi-person-x-fill"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small">Departments</span>
                        <h2 class="fw-bold mb-0 mt-1">{{ $departmentsCount ?? 0 }}</h2>
                    </div>
                    <div class="stat-icon" style="background: #fff3cd; color: #ffc107;">
                        <i class="bi bi-building"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Card -->
    <div class="card main-card">
        
        <!-- Filter Section -->
        <div class="card-body border-bottom" style="background: #fafcff;">
            <form method="GET" action="{{ route('admin.staff.directory') }}" id="filterForm">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label-custom">SEARCH</label>
                        <input type="text" name="search" class="form-control" placeholder="Name, email, employee ID..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-custom">DEPARTMENT</label>
                        <select name="department" class="form-select">
                            <option value="">All Departments</option>
                            @foreach($departments ?? [] as $dept)
                                <option value="{{ $dept }}" {{ request('department') == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-custom">DESIGNATION</label>
                        <select name="designation" class="form-select">
                            <option value="">All Designations</option>
                            @foreach($designations ?? [] as $desig)
                                <option value="{{ $desig }}" {{ request('designation') == $desig ? 'selected' : '' }}>{{ $desig }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label-custom">STATUS</label>
                        <select name="status" class="form-select">
                            <option value="">All Status</option>
                            <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="Suspended" {{ request('status') == 'Suspended' ? 'selected' : '' }}>Suspended</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-royal-blue">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                        <a href="{{ route('admin.staff.directory') }}" class="btn btn-outline-royal ms-2">
                            <i class="bi bi-arrow-repeat me-1"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Staff Table -->
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-custom" id="staffTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Photo</th>
                            <th>Name</th>
                            <th>Department</th>
                            <th>Designation</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Joining Date</th>
                            <th>Salary</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($staffs ?? [] as $staff)
                        <tr id="staff-row-{{ $staff->id }}">
                            <td>{{ $staff->employee_id }}</td>
                            <td>
                                <img src="{{ $staff->profile_photo ? asset($staff->profile_photo) : asset('assets/images/users/avatar-default.jpg') }}" 
                                     class="staff-avatar" 
                                     alt="{{ $staff->first_name }}">
                            </td>
                            <td>{{ $staff->first_name }} {{ $staff->last_name }}</td>
                            <td>{{ $staff->department ?? '-' }}</td>
                            <td>{{ $staff->designation ?? '-' }}</td>
                            <td>{{ $staff->email }}</td>
                            <td>{{ $staff->phone ?? '-' }}</td>
                            <td>{{ $staff->joining_date ? \Carbon\Carbon::parse($staff->joining_date)->format('d M Y') : '-' }}</td>
                            <td>
                                ₹{{ number_format(($staff->basic_salary ?? 0) + ($staff->hra ?? 0) + ($staff->allowance ?? 0), 2) }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge-status badge-{{ strtolower($staff->status) }}" id="status-badge-{{ $staff->id }}">
                                        {{ $staff->status }}
                                    </span>
                                    <label class="status-toggle">
                                        <input type="checkbox" class="status-toggle-input" data-id="{{ $staff->id }}" 
                                               {{ $staff->status == 'Active' ? 'checked' : '' }}>
                                        <span class="toggle-slider"></span>
                                    </label>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('admin.staff.show', $staff->id) }}" class="action-icon" data-tooltip="View Details">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.staff.edit', $staff->id) }}" class="action-icon" data-tooltip="Edit Staff">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="action-icon delete-staff-btn" 
                                            data-id="{{ $staff->id }}"
                                            data-name="{{ $staff->first_name }} {{ $staff->last_name }}"
                                            data-tooltip="Delete Staff">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center py-5">
                                <i class="bi bi-people fs-1 text-muted"></i>
                                <p class="mt-2 text-muted">No staff members found</p>
                                <a href="{{ route('admin.staff.create') }}" class="btn btn-royal-blue btn-sm">
                                    <i class="bi bi-person-plus"></i> Add First Staff
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if(isset($staffs) && $staffs->hasPages())
        <div class="card-footer bg-transparent border-top">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-muted small">
                    Showing {{ $staffs->firstItem() ?? 0 }} to {{ $staffs->lastItem() ?? 0 }} of {{ $staffs->total() }} entries
                </div>
                <div>
                    {{ $staffs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    
    // Status Toggle Function
    $(document).on('change', '.status-toggle-input', function() {
        var $toggle = $(this);
        var staffId = $toggle.data('id');
        var newStatus = $toggle.prop('checked') ? 'Active' : 'Inactive';
        var $badge = $('#status-badge-' + staffId);
        
        Swal.fire({
            title: 'Change Status?',
            text: 'Are you sure you want to change status to ' + newStatus + '?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#0d6efd',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, change it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                $toggle.prop('disabled', true);
                
                $.ajax({
                    url: '{{ route("admin.staff.update-status") }}',
                    type: 'POST',
                    data: {
                        id: staffId,
                        status: newStatus,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $badge.text(newStatus);
                            $badge.removeClass('badge-active badge-inactive');
                            $badge.addClass(newStatus === 'Active' ? 'badge-active' : 'badge-inactive');
                            
                            Swal.fire({
                                icon: 'success',
                                title: 'Updated!',
                                text: 'Status changed to ' + newStatus,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            $toggle.prop('checked', !$toggle.prop('checked'));
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: response.message || 'Failed to update status'
                            });
                        }
                        $toggle.prop('disabled', false);
                    },
                    error: function(xhr) {
                        $toggle.prop('checked', !$toggle.prop('checked'));
                        $toggle.prop('disabled', false);
                        
                        let errorMsg = 'Something went wrong!';
                        if (xhr.status === 419) {
                            errorMsg = 'Session expired. Please refresh the page.';
                        } else if (xhr.status === 404) {
                            errorMsg = 'API endpoint not found. Please check route.';
                        }
                        
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                });
            } else {
                $toggle.prop('checked', !$toggle.prop('checked'));
            }
        });
    });
    
    // Delete Staff Function
    $(document).on('click', '.delete-staff-btn', function() {
        var $btn = $(this);
        var staffId = $btn.data('id');
        var staffName = $btn.data('name');
        
        Swal.fire({
            title: 'Are you sure?',
            html: 'You are about to delete <strong>' + staffName + '</strong><br><br>This action cannot be undone!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'Deleting...',
                    text: 'Please wait',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                
                $.ajax({
                    url: '{{ route("admin.staff.destroy", "") }}/' + staffId,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: 'Staff member deleted successfully.',
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Failed!',
                                text: response.message || 'Failed to delete staff.'
                            });
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Something went wrong!';
                        if (xhr.status === 419) {
                            errorMsg = 'Session expired. Please refresh the page.';
                        }
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: errorMsg,
                            confirmButtonColor: '#0d6efd'
                        });
                    }
                });
            }
        });
    });
});
</script>
@endsection
