@extends('layouts.vertical', ['title' => 'Profile Page'])

@section('content')

<!-- Start Content-->
<div class="container-fluid">
    <div class="py-3 d-flex align-items-sm-center flex-sm-row flex-column">
        <div class="flex-grow-1">
            <h4 class="fs-18 fw-semibold m-0">Admin Profile </h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">System</a></li>
                <li class="breadcrumb-item active">Profile</li>
            </ol>
        </div>
    </div>

    {{-- TOP USER BANNER CARD --}}
    <div class="row">
        <div class="col-12">
            <div class="card overflow-hidden" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05); background-color: #004392;">
                <div class="card-body p-4">
                    <div class="d-flex flex-sm-row flex-column align-items-center text-sm-start text-center">
                        {{-- Profile Headline Details --}}
                        <div class="flex-grow-1">
                            <h4 class="m-0 text-white fw-bold fs-24">
                                @if($currentUser)
                                    {{ $currentUser->name ?? ($currentUser->first_name . ' ' . $currentUser->last_name) }}
                                @else
                                    Guest Admin
                                @endif
                            </h4>
                            <p class="text-white-50 fs-14 my-2 d-flex align-items-center flex-wrap justify-content-sm-start justify-content-center gap-2">
                                <span class="badge bg-white text-primary px-2.5 py-1 fs-12 fw-semibold text-uppercase" style="background-color: rgba(255,255,255,0.9) !important;">
                                    {{ $currentUser->role ?? 'Super Admin' }}
                                </span>
                                <span>•</span>
                                <span class="text-white">{{ $currentUser->email ?? 'no-email@vshop.com' }}</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TAB DETAILS CARD --}}
    <div class="row">
        <div class="col-12">
            <div class="card border" style="border-radius: 12px; border: 1px solid #e2e8f0; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
                <div class="card-body pt-0">
                    
                    {{-- Tab Navigation --}}
                    <ul class="nav nav-underline border-bottom pt-2 mb-3" id="profile-tabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <a class="nav-link active fw-semibold py-2.5 px-3 fs-14" id="my-profile-tab" data-bs-toggle="tab" href="#my-profile" role="tab" aria-controls="my-profile" aria-selected="true">
                                <i class="mdi mdi-account-circle me-1"></i> My Profile
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link fw-semibold py-2.5 px-3 fs-14" id="admin-staff-tab" data-bs-toggle="tab" href="#admin-staff" role="tab" aria-controls="admin-staff" aria-selected="false">
                                <i class="mdi mdi-account-group me-1"></i> Admin Staff Members ({{ $staffs->count() }})
                            </a>
                        </li>
                        <li class="nav-item" role="presentation">
                            <a class="nav-link fw-semibold py-2.5 px-3 fs-14" id="login-history-tab" data-bs-toggle="tab" href="#login-history" role="tab" aria-controls="login-history" aria-selected="false">
                                <i class="mdi mdi-history me-1"></i> Admin Login History
                            </a>
                        </li>
                    </ul>

                    {{-- Tab Contents --}}
                    <div class="tab-content text-muted" id="profile-tabs-content">
                        
                        {{-- 1. MY PROFILE TAB --}}
                        <div class="tab-pane fade show active py-3" id="my-profile" role="tabpanel" aria-labelledby="my-profile-tab">
                            <div class="row">
                                
                                <div class="col-md-6 col-12">
                                    <h5 class="fs-16 text-dark fw-bold mb-3"><i class="mdi mdi-card-account-details-outline text-primary me-1"></i> Account Specifications</h5>
                                    
                                    <div class="table-responsive">
                                        <table class="table table-sm table-borderless fs-14">
                                            <tbody>
                                                <tr class="border-bottom border-light">
                                                    <td class="text-muted py-2" style="width: 150px;">First & Last Name</td>
                                                    <td class="text-dark fw-semibold py-2">: {{ $currentUser->name ?? ($currentUser->first_name . ' ' . $currentUser->last_name ?? 'N/A') }}</td>
                                                </tr>
                                                <tr class="border-bottom border-light">
                                                    <td class="text-muted py-2">Email Address</td>
                                                    <td class="text-dark fw-semibold py-2">: {{ $currentUser->email ?? 'N/A' }}</td>
                                                </tr>
                                                <tr class="border-bottom border-light">
                                                    <td class="text-muted py-2">Phone Number</td>
                                                    <td class="text-dark fw-semibold py-2">: {{ $currentUser->phone ?? 'N/A' }}</td>
                                                </tr>
                                                <tr class="border-bottom border-light">
                                                    <td class="text-muted py-2">Control Role</td>
                                                    <td class="text-dark fw-semibold py-2">: 
                                                        <span class="text-capitalize">{{ $currentUser->role ?? 'N/A' }}</span>
                                                    </td>
                                                </tr>
                                                <tr class="border-bottom border-light">
                                                    <td class="text-muted py-2">Account Status</td>
                                                    <td class="py-2">: 
                                                        @if(($currentUser->status ?? '') == 'Active' || ($currentUser->status ?? '') == 'active')
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5">Active</span>
                                                        @else
                                                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5">{{ ucfirst($currentUser->status ?? 'Active') }}</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @if(isset($currentUser->joining_date))
                                                <tr class="border-bottom border-light">
                                                    <td class="text-muted py-2">Date of Joining</td>
                                                    <td class="text-dark fw-semibold py-2">: {{ \Carbon\Carbon::parse($currentUser->joining_date)->format('d M, Y') }}</td>
                                                </tr>
                                                @endif
                                                @if(isset($currentUser->employee_id))
                                                <tr class="border-bottom border-light">
                                                    <td class="text-muted py-2">Employee ID</td>
                                                    <td class="text-dark fw-semibold py-2">: {{ $currentUser->employee_id }}</td>
                                                </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- 2. ADMIN STAFF TAB --}}
                        <div class="tab-pane fade py-3" id="admin-staff" role="tabpanel" aria-labelledby="admin-staff-tab">
                            <h5 class="fs-16 text-dark fw-bold mb-3"><i class="mdi mdi-account-group text-primary me-1"></i> Admin Staff Members</h5>
                            <p class="text-muted fs-13 mb-3">All staff profiles currently configured in the database, including employee numbers, payroll structures, status checks, and access privileges.</p>
                            
                            <div class="table-responsive border rounded-3 overflow-hidden">
                                <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 13.5px;">
                                    <thead class="table-light fs-13 text-dark fw-bold">
                                        <tr>
                                            <th>Emp ID</th>
                                            <th>Staff Member Name</th>
                                            <th>Email Address</th>
                                            <th>Phone</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($staffs as $staff)
                                            <tr>
                                                <td class="fw-semibold text-dark">{{ $staff->employee_id }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                         <img src="{{ $staff->profile_photo ? (str_starts_with($staff->profile_photo, 'uploads/') ? asset($staff->profile_photo) : asset('storage/' . $staff->profile_photo)) : asset('assets/images/users/avatar-1.jpg') }}" 
                                                              class="rounded-circle me-2 border" 
                                                             style="width: 32px; height: 32px; object-fit: cover;" 
                                                             alt="">
                                                        <span class="fw-semibold text-dark">{{ $staff->first_name }} {{ $staff->last_name }}</span>
                                                    </div>
                                                </td>
                                                <td>{{ $staff->email }}</td>
                                                <td>{{ $staff->phone ?? 'N/A' }}</td>
                                                <td>
                                                    <span class="badge bg-light text-dark border px-2 py-1">
                                                        {{ $staff->role ?? 'Staff' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @if($staff->status == 'Active')
                                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Active</span>
                                                    @else
                                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">{{ $staff->status }}</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" data-bs-toggle="modal" data-bs-target="#staffModal{{ $staff->id }}">
                                                        <i class="mdi mdi-eye-outline me-1"></i> View Details
                                                    </button>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="7" class="text-center py-4 text-muted fs-14">
                                                    <i class="mdi mdi-account-off-outline fs-24 text-muted d-block mb-2"></i>
                                                    No admin staff members found in the system.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- 3. LOGIN HISTORY TAB --}}
                        <div class="tab-pane fade py-3" id="login-history" role="tabpanel" aria-labelledby="login-history-tab">
                            <h5 class="fs-16 text-dark fw-bold mb-3"><i class="mdi mdi-history text-primary me-1"></i> System Access Log</h5>
                            <p class="text-muted fs-13 mb-3">Logs of latest login activities for admins and staff members including IP Addresses, user agent data, and session status.</p>

                            <div class="table-responsive border rounded-3 overflow-hidden">
                                <table class="table table-hover align-middle mb-0 text-nowrap" style="font-size: 13.5px;">
                                    <thead class="table-light fs-13 text-dark fw-bold">
                                        <tr>
                                            <th>Timestamp</th>
                                            <th>User Name</th>
                                            <th>Activity Details</th>
                                            <th>IP Address</th>
                                            <th>Device / Browser Info</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($loginLogs as $log)
                                            <tr>
                                                <td class="text-muted fw-semibold">
                                                    {{ \Carbon\Carbon::parse($log->created_at)->format('d M, Y H:i:s') }}
                                                </td>
                                                <td class="text-dark fw-semibold">
                                                    @if(isset($log->metadata) && is_array($log->metadata) && isset($log->metadata['name']))
                                                        {{ $log->metadata['name'] }}
                                                    @elseif(isset($log->user) && $log->user)
                                                        {{ $log->user->name }}
                                                    @else
                                                        System User
                                                    @endif
                                                </td>
                                                <td>
                                                    <span class="fw-medium text-dark">{{ $log->description }}</span>
                                                </td>
                                                <td>
                                                    <code>{{ $log->ip_address ?? '127.0.0.1' }}</code>
                                                </td>
                                                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $log->user_agent }}">
                                                    <span class="text-muted">{{ $log->user_agent ?? 'Unknown Client' }}</span>
                                                </td>
                                                <td>
                                                    @if(str_contains($log->activity_type ?? '', 'failed'))
                                                        <span class="badge bg-danger text-white px-2 py-0.5">Failed</span>
                                                    @else
                                                        <span class="badge bg-success text-white px-2 py-0.5">Success</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4 text-muted fs-14">
                                                    <i class="mdi mdi-alert-circle-outline fs-24 text-muted d-block mb-2"></i>
                                                    No access logs found in database.
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODALS FOR ADMIN STAFF DETAILS --}}
@foreach($staffs as $staff)
<div class="modal fade" id="staffModal{{ $staff->id }}" tabindex="-1" aria-labelledby="staffModalLabel{{ $staff->id }}" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content" style="border-radius: 12px; overflow: hidden;">
            <div class="modal-header bg-dark text-white py-3">
                <h5 class="modal-title fs-16 fw-bold" id="staffModalLabel{{ $staff->id }}">
                    <i class="mdi mdi-card-account-details-outline me-1"></i> Detailed Staff Card: {{ $staff->first_name }} {{ $staff->last_name }}
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="max-height: 75vh; overflow-y: auto;">
                
                {{-- Top Summary --}}
                <div class="d-flex align-items-center border-bottom pb-3 mb-4">
                    <img src="{{ $staff->profile_photo ? (str_starts_with($staff->profile_photo, 'uploads/') ? asset($staff->profile_photo) : asset('storage/' . $staff->profile_photo)) : asset('assets/images/users/avatar-1.jpg') }}" 
                         class="rounded-circle border me-3 img-thumbnail" 
                         style="width: 80px; height: 80px; object-fit: cover;" 
                         alt="">
                    <div>
                        <h4 class="text-dark fw-bold m-0 fs-18">{{ $staff->first_name }} {{ $staff->last_name }}</h4>
                        <p class="text-muted m-0 fs-13">Employee Number: <strong>{{ $staff->employee_id }}</strong> | Type: {{ $staff->employee_type ?? 'Full Time' }}</p>
                        <p class="m-0 mt-1">
                            <span class="badge bg-primary text-uppercase px-2.5 py-0.5 fs-11">{{ $staff->role ?? 'Staff' }}</span>
                            @if($staff->status == 'Active')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-0.5 fs-11 ms-1">Active</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2.5 py-0.5 fs-11 ms-1">{{ $staff->status }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                <div class="row">
                    
                    {{-- 1. EMPLOYEE INFO --}}
                    <div class="col-md-6 mb-4">
                        <h6 class="text-dark fw-bold border-bottom pb-1.5 mb-2"><i class="mdi mdi-account-circle-outline me-1"></i> Employment Info</h6>
                        <table class="table table-sm table-borderless fs-13">
                            <tbody>
                                <tr>
                                    <td class="text-muted py-1" style="width: 130px;">Gender / DOB</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->gender ?? 'N/A' }} / {{ $staff->dob ? \Carbon\Carbon::parse($staff->dob)->format('d M, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Email ID</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->email }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Phone Number</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->phone ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Date Joined</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->joining_date ? \Carbon\Carbon::parse($staff->joining_date)->format('d M, Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Probation End</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->probation_end_date ? \Carbon\Carbon::parse($staff->probation_end_date)->format('d M, Y') : 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 2. BANK INFO --}}
                    <div class="col-md-6 mb-4">
                        <h6 class="text-dark fw-bold border-bottom pb-1.5 mb-2"><i class="mdi mdi-bank-outline me-1"></i> Bank Account Info</h6>
                        <table class="table table-sm table-borderless fs-13">
                            <tbody>
                                <tr>
                                    <td class="text-muted py-1" style="width: 140px;">Holder Name</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->account_holder_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Bank Name</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->bank_name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Account Number</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->account_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">IFSC Code</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->ifsc_code ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Branch Location</td>
                                    <td class="text-dark fw-semibold py-1">: {{ $staff->branch_location ?? 'N/A' }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 3. PAYROLL STRUCTURE --}}
                    <div class="col-md-6 mb-4">
                        <h6 class="text-dark fw-bold border-bottom pb-1.5 mb-2"><i class="mdi mdi-cash-multiple me-1"></i> Payroll & Salary</h6>
                        <table class="table table-sm table-borderless fs-13">
                            <tbody>
                                <tr>
                                    <td class="text-muted py-1" style="width: 130px;">Basic Salary</td>
                                    <td class="text-dark fw-semibold py-1">: ₹{{ number_format($staff->basic_salary, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">House Rent (HRA)</td>
                                    <td class="text-dark fw-semibold py-1">: ₹{{ number_format($staff->hra, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Allowances</td>
                                    <td class="text-dark fw-semibold py-1">: ₹{{ number_format($staff->allowance, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Bonus</td>
                                    <td class="text-dark fw-semibold py-1">: ₹{{ number_format($staff->bonus, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-muted py-1">Professional Tax</td>
                                    <td class="text-dark fw-semibold py-1">: ₹{{ number_format($staff->professional_tax, 2) }}</td>
                                </tr>
                                <tr class="border-top border-light">
                                    <td class="text-dark fw-bold py-1.5">Net Salary</td>
                                    <td class="text-primary fw-bold py-1.5">: ₹{{ number_format($staff->net_salary ?? $staff->net_pay, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    {{-- 4. SYSTEM PERMISSIONS & DOCUMENTS --}}
                    <div class="col-md-6 mb-4">
                        <h6 class="text-dark fw-bold border-bottom pb-1.5 mb-2"><i class="mdi mdi-shield-lock-outline me-1"></i> Access Permissions</h6>
                        <div class="d-flex flex-wrap gap-1.5 mb-3 fs-12">
                            <span class="badge {{ $staff->dashboard_access ? 'bg-success' : 'bg-light text-muted border' }}">Dashboard</span>
                            <span class="badge {{ $staff->seller_management_access ? 'bg-success' : 'bg-light text-muted border' }}">Sellers</span>
                            <span class="badge {{ $staff->customer_management_access ? 'bg-success' : 'bg-light text-muted border' }}">Customers</span>
                            <span class="badge {{ $staff->order_management_access ? 'bg-success' : 'bg-light text-muted border' }}">Orders</span>
                            <span class="badge {{ $staff->finance_access ? 'bg-success' : 'bg-light text-muted border' }}">Finance</span>
                            <span class="badge {{ $staff->reports_access ? 'bg-success' : 'bg-light text-muted border' }}">Reports</span>
                            <span class="badge {{ $staff->settings_access ? 'bg-success' : 'bg-light text-muted border' }}">Settings</span>
                        </div>

                        <h6 class="text-dark fw-bold border-bottom pb-1.5 mb-2"><i class="mdi mdi-file-document-outline me-1"></i> Attached Documents</h6>
                        <div class="d-flex flex-column gap-1">
                            @if($staff->pan_card_file)
                                <a href="{{ asset('storage/' . $staff->pan_card_file) }}" target="_blank" class="text-primary fs-13"><i class="mdi mdi-file-pdf-box text-danger"></i> PAN Card File <i class="mdi mdi-open-in-new small"></i></a>
                            @else
                                <span class="text-muted fs-13"><i class="mdi mdi-file-remove text-muted"></i> No PAN Card uploaded</span>
                            @endif

                            @if($staff->aadhaar_file)
                                <a href="{{ asset('storage/' . $staff->aadhaar_file) }}" target="_blank" class="text-primary fs-13"><i class="mdi mdi-file-pdf-box text-danger"></i> Aadhaar File <i class="mdi mdi-open-in-new small"></i></a>
                            @else
                                <span class="text-muted fs-13"><i class="mdi mdi-file-remove text-muted"></i> No Aadhaar uploaded</span>
                            @endif

                            @if($staff->cancelled_cheque)
                                <a href="{{ asset('storage/' . $staff->cancelled_cheque) }}" target="_blank" class="text-primary fs-13"><i class="mdi mdi-file-pdf-box text-danger"></i> Cancelled Cheque <i class="mdi mdi-open-in-new small"></i></a>
                            @else
                                <span class="text-muted fs-13"><i class="mdi mdi-file-remove text-muted"></i> No Cheque uploaded</span>
                            @endif
                        </div>
                    </div>
                </div>

                @if($staff->payroll_notes)
                    <div class="row">
                        <div class="col-12 mt-2">
                            <h6 class="text-dark fw-bold border-bottom pb-1.5 mb-2"><i class="mdi mdi-note-text-outline me-1"></i> Payroll Notes</h6>
                            <p class="bg-light p-2.5 rounded text-dark fs-13 mb-0 border" style="white-space: pre-line;">{{ $staff->payroll_notes }}</p>
                        </div>
                    </div>
                @endif

            </div>
            <div class="modal-footer bg-light py-2.5">
                <button type="button" class="btn btn-secondary px-4" data-bs-modal="dismiss" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endforeach

@endsection
