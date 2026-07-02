@extends('layouts.vertical', ['title' => 'System Settings'])

@section('css')
<link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
<style>
    :root {
        --primary-color: #004392;
        --primary-light: rgba(0, 67, 146, 0.05);
        --primary-hover: #002d62;
        --success-color: #10b981;
        --danger-color: #ef4444;
        --warning-color: #f59e0b;
        --bg-light: #f8fafc;
        --border-color: #e2e8f0;
    }
    
    .tab-pane { padding-top: 1.5rem; }
    .form-switch .form-check-input { width: 2.5em; height: 1.25em; cursor: pointer; }
    
    .nav-tabs-custom {
        border-bottom: 2px solid var(--border-color);
    }
    .nav-tabs-custom .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 14px 24px;
        border-bottom: 3px solid transparent;
        transition: all 0.2s ease-in-out;
        font-size: 0.95rem;
    }
    .nav-tabs-custom .nav-link:hover {
        color: var(--primary-color);
        background: var(--primary-light);
    }
    .nav-tabs-custom .nav-link.active {
        color: var(--primary-color) !important;
        border-bottom-color: var(--primary-color);
        background: rgba(0, 67, 146, 0.07);
    }
    
    .settings-card {
        border: 1px solid var(--border-color);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
        border-radius: 12px;
        transition: transform 0.2s, box-shadow 0.2s;
        background: #fff;
    }
    .settings-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.08), 0 4px 6px -2px rgba(0, 0, 0, 0.04);
    }
    
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 8px;
        margin-bottom: 16px;
    }
    
    .table-custom th {
        background: #f1f5f9;
        color: #475569;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.5px;
    }
    
    .badge-status {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 6px;
    }
    .badge-pending { background-color: #fef3c7; color: #d97706; }
    .badge-approved { background-color: #d1fae5; color: #059669; }
    .badge-rejected { background-color: #fee2e2; color: #dc2626; }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Breadcrumb -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 text-primary fw-bold">System Settings</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Settings</a></li>
                        <li class="breadcrumb-item active">System Settings</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Notifications -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bx bx-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bx bx-error-circle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card settings-card mt-3">
        <div class="card-body p-4">
            <!-- Tabs Menu -->
            <ul class="nav nav-tabs nav-tabs-custom" id="systemSettingsTab" role="tablist">
                <li class="nav-item">
                    <button class="nav-link active" id="security-tab" data-bs-toggle="tab" data-bs-target="#security" type="button">
                        <i class="bx bx-shield-alt-2 me-1"></i> Security Layer
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="data-tab" data-bs-toggle="tab" data-bs-target="#data" type="button">
                        <i class="bx bx-data me-1"></i> Data Control
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="platform-tab" data-bs-toggle="tab" data-bs-target="#platform" type="button">
                        <i class="bx bx-cog me-1"></i> Platform Settings
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link" id="invoice-logs-tab" data-bs-toggle="tab" data-bs-target="#invoice-logs" type="button">
                        <i class="bx bx-receipt me-1"></i> Invoice Logs
                    </button>
                </li>
            </ul>

            <div class="tab-content mt-3">
                <!-- SECURITY TAB -->
                <div class="tab-pane fade show active" id="security">
                    <form method="POST" action="{{ route('admin.settings.system.security.save') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="section-title"><i class="bx bx-toggle-right me-1"></i> Feature Toggles</h5>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="two_factor_auth" id="two_factor_auth_checkbox" value="1" {{ $security['two_factor_auth'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">2FA Authentication</label>
                                </div>
                                <input type="hidden" name="security_2fa_email" id="security_2fa_email" value="{{ $security['security_2fa_email'] ?? '' }}">
                                <input type="hidden" name="security_2fa_phone" id="security_2fa_phone" value="{{ $security['security_2fa_phone'] ?? '' }}">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="suspicious_detection" value="1" {{ $security['suspicious_detection'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">Suspicious Activity Detection</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="abnormal_conversion" value="1" {{ $security['abnormal_conversion'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">Abnormal Conversion Detection</label>
                                </div>
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" name="ip_abuse_detection" value="1" {{ $security['ip_abuse_detection'] ? 'checked' : '' }}>
                                    <label class="form-check-label fw-semibold">IP Abuse Detection</label>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h5 class="section-title"><i class="bx bx-lock-open me-1"></i> Threat Parameters</h5>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Failed Attempts Limit</label>
                                    <input type="number" name="failed_attempts_limit" class="form-control" value="{{ $security['failed_attempts_limit'] }}" min="1" max="20">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Block Duration (hours)</label>
                                    <input type="number" name="block_duration_hours" class="form-control" value="{{ $security['block_duration_hours'] }}" min="1" max="720">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-semibold">Whitelist IPs (one per line)</label>
                                    <textarea name="whitelist_ips" class="form-control" rows="4" placeholder="192.168.1.1">{{ $security['whitelist_ips'] }}</textarea>
                                    <small class="text-muted">IP addresses that will bypass threat blocks.</small>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary px-4"><i class="bx bx-save me-1"></i> Save Security Settings</button>
                        </div>
                    </form>
                </div>

                <!-- DATA CONTROL TAB -->
                <div class="tab-pane fade" id="data">
                    <div class="row mb-4">
                        <!-- Left Column: Configure & Manual Trigger Actions -->
                        <div class="col-md-5">
                            <div class="card p-3 border mb-3 bg-light">
                                <h5 class="section-title"><i class="bx bx-slider me-1"></i> Data Retention Settings</h5>
                                <form method="POST" action="{{ route('admin.settings.system.data.save') }}">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Backup Frequency</label>
                                        <select name="backup_frequency" class="form-select">
                                            <option value="daily" {{ $dataControl['backup_frequency'] == 'daily' ? 'selected' : '' }}>Daily</option>
                                            <option value="weekly" {{ $dataControl['backup_frequency'] == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                            <option value="monthly" {{ $dataControl['backup_frequency'] == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Backup Retention (days)</label>
                                        <input type="number" name="backup_retention_days" class="form-control" value="{{ $dataControl['backup_retention_days'] }}" min="1" max="365">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Data Retention (months)</label>
                                        <input type="number" name="data_retention_months" class="form-control" value="{{ $dataControl['data_retention_months'] }}" min="1" max="120">
                                        <small class="text-muted">Delete logs and transient records older than this period.</small>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" name="deletion_approval" value="1" {{ $dataControl['deletion_approval'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold">Require Approval for User Data Deletion</label>
                                    </div>
                                    <button type="submit" class="btn btn-primary btn-sm w-100"><i class="bx bx-save me-1"></i> Save Configuration</button>
                                </form>
                            </div>

                            <div class="card p-3 border mb-3">
                                <h5 class="section-title"><i class="bx bx-cloud-download me-1"></i> Export & Purge Tools</h5>
                                <div class="d-grid gap-2">
                                    <!-- Export settings -->
                                    <a href="{{ route('admin.settings.system.export') }}" class="btn btn-outline-primary"><i class="bx bx-export me-1"></i> Export System Config JSON</a>
                                    
                                    <!-- Purge Logs -->
                                    <form method="POST" action="{{ route('admin.settings.system.purge') }}" onsubmit="return confirm('Are you sure you want to permanently delete old logs based on the data retention period?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger w-100"><i class="bx bx-trash me-1"></i> Purge Old Database Logs Now</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column: Database Backups & Restore -->
                        <div class="col-md-7">
                            <div class="card p-3 border bg-light">
                                <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
                                    <h5 class="m-0 fw-bold"><i class="bx bx-history me-1"></i> System Backup Manager</h5>
                                    <form method="POST" action="{{ route('admin.settings.system.backup.create') }}">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm"><i class="bx bx-plus me-1"></i> Create Backup Now</button>
                                    </form>
                                </div>

                                <!-- Backup upload -->
                                <form method="POST" action="{{ route('admin.settings.system.restore') }}" enctype="multipart/form-data" class="mb-3">
                                    @csrf
                                    <label class="form-label fw-semibold">Restore from Custom Backup File (.json)</label>
                                    <div class="input-group">
                                        <input type="file" name="backup_file" class="form-control" required accept=".json">
                                        <button type="submit" class="btn btn-warning" onclick="return confirm('Restoring system configuration might overwrite current settings. Proceed?');"><i class="bx bx-upload me-1"></i> Upload & Restore</button>
                                    </div>
                                </form>

                                <!-- Backup Files Table -->
                                <div class="table-responsive" style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-bordered table-sm table-custom align-middle">
                                        <thead>
                                            <tr>
                                                <th>Filename</th>
                                                <th>Size</th>
                                                <th>Created At</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($backupFiles as $backup)
                                                <tr>
                                                    <td class="font-monospace">{{ $backup['filename'] }}</td>
                                                    <td>{{ $backup['size'] }}</td>
                                                    <td>{{ $backup['created_at'] }}</td>
                                                    <td class="text-center">
                                                        <div class="btn-group">
                                                            <!-- Restore -->
                                                            <form method="POST" action="{{ route('admin.settings.system.restore') }}" style="display:inline;" onsubmit="return confirm('Restore settings from this backup?');">
                                                                @csrf
                                                                <input type="hidden" name="filename" value="{{ $backup['filename'] }}">
                                                                <button type="submit" class="btn btn-xs btn-outline-warning py-0"><i class="bx bx-reset"></i></button>
                                                            </form>
                                                            <!-- Delete -->
                                                            <form method="POST" action="{{ route('admin.settings.system.backup.delete') }}" style="display:inline;" onsubmit="return confirm('Permanently delete this backup file?');">
                                                                @csrf
                                                                <input type="hidden" name="filename" value="{{ $backup['filename'] }}">
                                                                <button type="submit" class="btn btn-xs btn-outline-danger py-0 ms-1"><i class="bx bx-trash"></i></button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">No system backup files found in storage.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User Data Deletion Requests -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card p-3 border">
                                <h5 class="section-title"><i class="bx bx-user-minus me-1"></i> User Data Deletion Requests (Compliance / GDPR)</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover align-middle table-custom">
                                        <thead>
                                            <tr>
                                                <th>Request ID</th>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Role</th>
                                                <th>Reason</th>
                                                <th>Status</th>
                                                <th class="text-center">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($deletionRequests as $req)
                                                <tr>
                                                    <td class="fw-bold">#{{ $req->id }}</td>
                                                    <td>{{ $req->name }}</td>
                                                    <td>{{ $req->email }}</td>
                                                    <td><span class="badge bg-secondary">{{ ucfirst($req->user_type) }}</span></td>
                                                    <td><small>{{ $req->reason ?? 'N/A' }}</small></td>
                                                    <td>
                                                        <span class="badge-status badge-{{ $req->status }}">
                                                            {{ ucfirst($req->status) }}
                                                        </span>
                                                    </td>
                                                    <td class="text-center">
                                                        @if($req->status === 'pending')
                                                            <form method="POST" action="{{ route('admin.settings.system.deletion.approve', $req->id) }}" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-success py-1 px-2" onclick="return confirm('Approve deletion? This will clear/anonymize user profile.');"><i class="bx bx-check"></i> Approve</button>
                                                            </form>
                                                            <form method="POST" action="{{ route('admin.settings.system.deletion.reject', $req->id) }}" style="display:inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-danger py-1 px-2 ms-1" onclick="return confirm('Reject deletion request?');"><i class="bx bx-x"></i> Reject</button>
                                                            </form>
                                                        @else
                                                            <span class="text-muted">—</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="7" class="text-center text-muted py-3">No compliance deletion requests found.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- PLATFORM SETTINGS TAB (Advanced Tax Rates) -->
                <div class="tab-pane fade" id="platform">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bx bx-percentage me-1"></i> Tax Rates & Rules</h5>
                        <button type="button" class="btn btn-sm btn-primary" id="addTaxRateBtn">
                            <i class="bx bx-plus"></i> Add Tax Rate
                        </button>
                    </div>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered table-hover align-middle tax-rate-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Name</th><th>Rate (%)</th><th>Region/Country</th><th>Type</th><th>Default</th><th>Status</th><th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="taxRatesTableBody">
                                @foreach($taxRates as $index => $rate)
                                <tr data-index="{{ $index }}">
                                    <td>{{ $rate['name'] }}</td>
                                    <td>{{ $rate['rate'] }}%</td>
                                    <td>{{ $rate['region'] ?? 'Global' }}</td>
                                    <td>{{ $rate['type'] }}</td>
                                    <td>
                                        @if($rate['is_default'] ?? false)
                                            <span class="badge bg-success">Default</span>
                                        @else
                                            <button class="btn btn-sm btn-outline-secondary set-default-tax" data-index="{{ $index }}">Set Default</button>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input tax-status-toggle" type="checkbox" data-index="{{ $index }}" {{ ($rate['status'] ?? 'Active') == 'Active' ? 'checked' : '' }}>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-icon btn-light text-primary edit-tax-rate" data-index="{{ $index }}"><i class="bx bx-edit"></i></button>
                                        <button class="btn btn-sm btn-icon btn-light text-danger delete-tax-rate" data-index="{{ $index }}"><i class="bx bx-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                                @if(count($taxRates) == 0)
                                <tr><td colspan="7" class="text-center text-muted py-3">No tax rates defined. Click "Add Tax Rate" to create one.</td></tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    <!-- Global Tax Inclusive Setting -->
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <form method="POST" action="{{ route('admin.settings.system.platform.save') }}">
                                @csrf
                                <div class="card p-3 border">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="tax_inclusive" id="tax_inclusive" value="1" {{ $platform['tax_inclusive'] ? 'checked' : '' }}>
                                        <label class="form-check-label fw-semibold" for="tax_inclusive">Prices are Tax Inclusive</label>
                                    </div>
                                    <small class="text-muted">When enabled, product catalog prices already include calculated tax.</small>
                                    <div class="d-flex justify-content-end mt-2">
                                        <button type="submit" class="btn btn-primary btn-sm"><i class="bx bx-save"></i> Save Tax Inclusion</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- INVOICE LOGS TAB -->
                <div class="tab-pane fade" id="invoice-logs">
                    <!-- Filters and Search Bar -->
                    <div class="row mb-3 g-2">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.settings.system') }}#invoice-logs" id="invoiceSearchForm">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Search Invoice #, module, reference ID, generator..." value="{{ request('search') }}">
                                    @if(request('module'))
                                        <input type="hidden" name="module" value="{{ request('module') }}">
                                    @endif
                                    <button type="submit" class="btn btn-primary"><i class="bx bx-search"></i> Search</button>
                                    @if(request()->has('search') || request()->has('module'))
                                        <a href="{{ route('admin.settings.system') }}#invoice-logs" class="btn btn-outline-secondary">Clear</a>
                                    @endif
                                </div>
                            </form>
                        </div>
                        <div class="col-md-4">
                            <select class="form-select" id="invoiceModuleFilter" onchange="filterInvoiceModule(this.value)">
                                <option value="">All Modules / Types</option>
                                <option value="Seller Commission Invoice" {{ request('module') == 'Seller Commission Invoice' ? 'selected' : '' }}>Seller Commission</option>
                                <option value="Payout Invoice" {{ request('module') == 'Payout Invoice' ? 'selected' : '' }}>Payout</option>
                                <option value="Customer Order Invoice" {{ request('module') == 'Customer Order Invoice' ? 'selected' : '' }}>Customer Order</option>
                                <option value="Seller Order Invoice" {{ request('module') == 'Seller Order Invoice' ? 'selected' : '' }}>Seller Order</option>
                                <option value="Admin Verified Invoice" {{ request('module') == 'Admin Verified Invoice' ? 'selected' : '' }}>Admin Verified</option>
                                <option value="Admin Viewed Invoice" {{ request('module') == 'Admin Viewed Invoice' ? 'selected' : '' }}>Admin Viewed</option>
                            </select>
                        </div>
                    </div>

                    <!-- Invoice Log Table -->
                    <div class="table-responsive">
                        <table class="table table-hover table-striped align-middle invoice-log-table">
                            <thead>
                                <tr>
                                    <th>Invoice #</th><th>Module / Type</th><th>Ref ID</th><th>Amount</th><th>Tax</th><th>Generated By</th><th>Date</th><th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($invoiceLogs as $log)
                                <tr>
                                    <td><span class="fw-bold">{{ $log->invoice_number }}</span></td>
                                    <td><span class="badge bg-secondary">{{ $log->module }}</span></td>
                                    <td>{{ $log->reference_id }}</td>
                                    <td>₹{{ number_format($log->amount, 2) }}</td>
                                    <td>₹{{ number_format($log->tax_amount, 2) }}</td>
                                    <td>{{ $log->generated_by ?? 'System' }}</td>
                                    <td>{{ $log->created_at->format('d M Y h:i A') }}</td>
                                    <td class="text-center">
                                        @if($log->file_path)
                                            <a href="{{ asset('storage/' . $log->file_path) }}" class="btn btn-sm btn-outline-primary" target="_blank"><i class="bx bx-download"></i> Download</a>
                                        @else
                                            <span class="text-muted">Generated Online</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="8" class="text-center text-muted py-4">No invoice logs found matching parameters.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    @if(method_exists($invoiceLogs, 'links'))
                        <div class="mt-3">{{ $invoiceLogs->links() }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Add/Edit Tax Rate -->
<div class="modal fade" id="taxRateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taxRateModalTitle">Add Tax Rate</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="tax_rate_index">
                <div class="mb-3">
                    <label class="form-label">Tax Name</label>
                    <input type="text" class="form-control" id="tax_name" required placeholder="e.g., GST, VAT">
                </div>
                <div class="mb-3">
                    <label class="form-label">Rate (%)</label>
                    <input type="number" step="0.01" class="form-control" id="tax_rate_value" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Region / Country</label>
                    <input type="text" class="form-control" id="tax_region" placeholder="e.g., India, Global, EU">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tax Type</label>
                    <select class="form-select" id="tax_type">
                        <option value="GST">GST</option>
                        <option value="VAT">VAT</option>
                        <option value="Sales Tax">Sales Tax</option>
                        <option value="Custom">Custom</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveTaxRateBtn">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- 2FA Setup Modal -->
<div class="modal fade" id="admin2FAModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-primary"><i class="bx bx-shield-quarter me-1"></i> Enable 2FA Authentication</h5>
                <button type="button" class="btn-close" id="cancel2FABtnClose"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted">To turn on Two-Factor Authentication, please provide the email and phone number where verification codes should be sent.</p>
                <div class="mb-3">
                    <label class="form-label fw-bold">Admin Email Address</label>
                    <input type="email" id="modal_2fa_email" class="form-control" placeholder="admin@example.com" value="{{ $security['security_2fa_email'] ?: (auth()->user()->email ?? '') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Admin Phone Number</label>
                    <input type="text" id="modal_2fa_phone" class="form-control" placeholder="+1234567890" value="{{ $security['security_2fa_phone'] ?: (auth()->user()->phone ?? '') }}" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancel2FABtn">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirm2FABtn">Confirm</button>
            </div>
        </div>
    </div>
</div>
<script>
    let currentEditIndex = null;
    let taxRateModal = null;

    window.addEventListener('load', function() {
        // Initialize modal after DOM is ready
        const modalElement = document.getElementById('taxRateModal');
        if (modalElement) {
            taxRateModal = new bootstrap.Modal(modalElement);
        }

        // Add button click
        const addBtn = document.getElementById('addTaxRateBtn');
        if (addBtn) {
            addBtn.addEventListener('click', function() {
                showAddTaxRateModal();
            });
        }

        // Save button
        const saveBtn = document.getElementById('saveTaxRateBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                saveTaxRate();
            });
        }

        // Tab hash handling
        let hash = window.location.hash;
        if (hash) {
            let tabButton = document.querySelector(`button[data-bs-target="${hash}"]`);
            if (tabButton) new bootstrap.Tab(tabButton).show();
        }

        // 2FA Modal Handler
        const twoFactorCheckbox = document.getElementById('two_factor_auth_checkbox');
        const twoFaModalElement = document.getElementById('admin2FAModal');
        let twoFaModal = null;
        if (twoFaModalElement) {
            twoFaModal = new bootstrap.Modal(twoFaModalElement);
        }

        if (twoFactorCheckbox) {
            twoFactorCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    if (twoFaModal) twoFaModal.show();
                } else {
                    document.getElementById('security_2fa_email').value = '';
                    document.getElementById('security_2fa_phone').value = '';
                }
            });
        }

        const handleCancel = function() {
            if (twoFactorCheckbox) {
                twoFactorCheckbox.checked = false;
            }
            if (twoFaModal) twoFaModal.hide();
        };

        const cancel2FaBtn = document.getElementById('cancel2FABtn');
        if (cancel2FaBtn) cancel2FaBtn.addEventListener('click', handleCancel);

        const cancel2FaBtnClose = document.getElementById('cancel2FABtnClose');
        if (cancel2FaBtnClose) cancel2FaBtnClose.addEventListener('click', handleCancel);

        const confirm2FaBtn = document.getElementById('confirm2FABtn');
        if (confirm2FaBtn) {
            confirm2FaBtn.addEventListener('click', function() {
                const emailVal = document.getElementById('modal_2fa_email').value.trim();
                const phoneVal = document.getElementById('modal_2fa_phone').value.trim();

                if (!emailVal || !phoneVal) {
                    alert('Please fill out both Email and Phone fields.');
                    return;
                }

                if (!emailVal.includes('@')) {
                    alert('Please enter a valid email address.');
                    return;
                }

                document.getElementById('security_2fa_email').value = emailVal;
                document.getElementById('security_2fa_phone').value = phoneVal;

                if (twoFaModal) twoFaModal.hide();
            });
        }
    });

    function filterInvoiceModule(module) {
        let url = new URL(window.location.href);
        url.hash = 'invoice-logs';
        if (module) {
            url.searchParams.set('module', module);
        } else {
            url.searchParams.delete('module');
        }
        window.location.href = url.toString();
        location.reload();
    }

    function showAddTaxRateModal() {
        currentEditIndex = null;
        document.getElementById('taxRateModalTitle').innerText = 'Add Tax Rate';
        document.getElementById('tax_rate_index').value = '';
        document.getElementById('tax_name').value = '';
        document.getElementById('tax_rate_value').value = '';
        document.getElementById('tax_region').value = '';
        document.getElementById('tax_type').value = 'GST';
        if (taxRateModal) taxRateModal.show();
        else alert('Modal not initialized');
    }

    function editTaxRate(index) {
        const row = document.querySelector(`tr[data-index="${index}"]`);
        if (!row) return;
        const cells = row.querySelectorAll('td');
        currentEditIndex = index;
        document.getElementById('taxRateModalTitle').innerText = 'Edit Tax Rate';
        document.getElementById('tax_rate_index').value = index;
        document.getElementById('tax_name').value = cells[0].innerText.trim();
        document.getElementById('tax_rate_value').value = cells[1].innerText.replace('%', '');
        document.getElementById('tax_region').value = cells[2].innerText.trim();
        document.getElementById('tax_type').value = cells[3].innerText.trim();
        if (taxRateModal) taxRateModal.show();
    }

    function saveTaxRate() {
        const index = document.getElementById('tax_rate_index').value;
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('index', index);
        formData.append('name', document.getElementById('tax_name').value);
        formData.append('rate', document.getElementById('tax_rate_value').value);
        formData.append('region', document.getElementById('tax_region').value);
        formData.append('type', document.getElementById('tax_type').value);
        formData.append('status', 'Active');

        fetch('{{ route("admin.settings.tax-rate.save") }}', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) location.reload();
            else alert('Error saving tax rate');
        })
        .catch(() => alert('Server error'));
    }

    // Event delegation for dynamic buttons
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-tax-rate')) {
            const btn = e.target.closest('.edit-tax-rate');
            const index = btn.getAttribute('data-index');
            editTaxRate(index);
        }
        if (e.target.closest('.delete-tax-rate')) {
            if (!confirm('Delete this tax rate?')) return;
            const btn = e.target.closest('.delete-tax-rate');
            const index = btn.getAttribute('data-index');
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('index', index);
            fetch('{{ route("admin.settings.tax-rate.delete") }}', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => { if (data.success) location.reload(); });
        }
        if (e.target.closest('.tax-status-toggle')) {
            const toggle = e.target.closest('.tax-status-toggle');
            const index = toggle.getAttribute('data-index');
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('index', index);
            fetch('{{ route("admin.settings.tax-rate.toggle") }}', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => { if (data.success) location.reload(); });
        }
        if (e.target.closest('.set-default-tax')) {
            const btn = e.target.closest('.set-default-tax');
            const index = btn.getAttribute('data-index');
            const formData = new FormData();
            formData.append('_token', '{{ csrf_token() }}');
            formData.append('index', index);
            fetch('{{ route("admin.settings.tax-rate.set-default") }}', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => { if (data.success) location.reload(); });
        }
    });
</script>
@endsection