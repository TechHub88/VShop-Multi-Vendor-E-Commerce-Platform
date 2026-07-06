@extends('layouts.vertical', ['title' => 'Data Validation: Duplicate Detection'])

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h4 class="fw-bold text-dark mb-0">Duplicate Detection</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="{{ route('admin.customers.index') }}">Customers</a></li>
                <li class="breadcrumb-item">Validation</li>
                <li class="breadcrumb-item active" aria-current="page">Duplicate Detection</li>
            </ol>
        </nav>
    </div>

    <!-- Info banner -->
    <div class="alert alert-warning border-0 shadow-sm rounded-4 mb-4 text-warning bg-opacity-10" style="background-color: rgba(255, 193, 7, 0.08);">
        <div class="d-flex">
            <div class="me-3 fs-20 text-warning">
                <i class="fas fa-clone"></i>
            </div>
            <div>
                <h6 class="alert-heading fw-bold mb-1" style="color: #856404;">Duplicate Detection Engine</h6>
                <p class="mb-0 fs-13 text-muted">
                    Consolidating duplicate accounts prevents fragmented orders, address conflicts, and reporting discrepancies. This suite automatically identifies accounts that share the **exact same email address**, **phone number**, or **name** so you can quickly merge them.
                </p>
            </div>
        </div>
    </div>

    <!-- Duplicates Detection Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light py-3">
                        <tr>
                            <th class="ps-4">Customer Details</th>
                            <th>Duplicate Match Criteria</th>
                            <th>Registration Date</th>
                            <th class="pe-4 text-end">Consolidation Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customers as $customer)
                            @php
                                $otherSameEmailCount = \App\Models\Customer::where('email', $customer->email)->where('id', '!=', $customer->id)->count();
                                $otherSamePhoneCount = !empty($customer->phone) ? \App\Models\Customer::where('phone', $customer->phone)->where('id', '!=', $customer->id)->count() : 0;
                                $otherSameNameCount = \App\Models\Customer::where('name', $customer->name)->where('id', '!=', $customer->id)->count();
                                
                                $matchReasons = [];
                                if($otherSameEmailCount > 0) $matchReasons[] = "Shared Email Match ({$otherSameEmailCount} other account(s))";
                                if($otherSamePhoneCount > 0) $matchReasons[] = "Shared Phone Match ({$otherSamePhoneCount} other account(s))";
                                if($otherSameNameCount > 0) $matchReasons[] = "Identical Name Match ({$otherSameNameCount} other account(s))";
                                $matchReasonsText = !empty($matchReasons) ? implode(' · ', $matchReasons) : 'No direct duplicates, but you can still merge manually if needed.';
                            @endphp
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-light p-2 border me-3 d-flex align-items-center justify-content-center fw-bold text-primary" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($customer->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-semibold text-dark">{{ $customer->name }}</h6>
                                            <small class="text-muted"><i class="far fa-envelope me-1"></i> {{ $customer->email }}</small>
                                            @if($customer->phone)
                                                <br><small class="text-muted"><i class="fas fa-phone-alt me-1"></i> {{ $customer->phone }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-inline-flex flex-wrap gap-1">
                                        @if($otherSameEmailCount > 0)
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 py-1 px-2 fs-10">
                                                <i class="far fa-envelope me-1"></i> Shared Email Match ({{ $otherSameEmailCount + 1 }})
                                            </span>
                                        @endif
                                        @if($otherSamePhoneCount > 0)
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 py-1 px-2 fs-10" style="color: #856404 !important;">
                                                <i class="fas fa-phone-alt me-1"></i> Shared Phone Match ({{ $otherSamePhoneCount + 1 }})
                                            </span>
                                        @endif
                                        @if($otherSameNameCount > 0)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 py-1 px-2 fs-10">
                                                <i class="far fa-user me-1"></i> Identical Name Match ({{ $otherSameNameCount + 1 }})
                                            </span>
                                        @endif
                                        @if($otherSameEmailCount == 0 && $otherSamePhoneCount == 0 && $otherSameNameCount == 0)
                                            <span class="text-muted small">No duplicates detected</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <small class="text-muted d-block"><i class="far fa-calendar-alt me-1"></i> {{ $customer->created_at->format('M d, Y') }}</small>
                                    <small class="text-muted fs-11"><i class="far fa-clock me-1"></i> {{ $customer->created_at->format('h:i A') }}</small>
                                </td>
                                <td class="pe-4 text-end">
                                    <button type="button" 
                                        class="btn btn-sm btn-primary rounded-3 merge-account-btn"
                                        data-customer-id="{{ $customer->id }}"
                                        data-customer-name="{{ addslashes($customer->name) }}"
                                        data-customer-email="{{ $customer->email }}"
                                        data-customer-phone="{{ $customer->phone ?? 'N/A' }}"
                                        data-match-reasons="{{ addslashes($matchReasonsText) }}"
                                        data-merge-url="{{ route('admin.customers.merge', ['source_id' => $customer->id]) }}">
                                        <i class="fas fa-compress-alt me-1"></i> Merge Account
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5">
                                    <i class="fas fa-user-check fa-3x mb-3 text-success"></i>
                                    <h5 class="text-dark">Zero duplicates detected!</h5>
                                    <p class="text-muted mb-0">Your customer database is fully consolidated and unique.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center p-3 border-top bg-light bg-opacity-50">
                <small class="text-muted">Showing {{ $customers->firstItem() ?? 0 }} to {{ $customers->lastItem() ?? 0 }} of {{ $customers->total() }} results</small>
                <div>{{ $customers->links() }}</div>
            </div>
        </div>
    </div>
</div>

<!-- SIMPLE MODAL - Working 100% -->
<div id="mergeModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999; align-items: center; justify-content: center;">
    <div style="background: white; border-radius: 16px; width: 500px; max-width: 90%; box-shadow: 0 20px 35px rgba(0,0,0,0.2);">
        <div style="background: linear-gradient(135deg, #004392 0%, #002255 100%); padding: 20px; border-radius: 16px 16px 0 0; color: white;">
            <h5 style="margin: 0; font-weight: bold;">
                <i class="fas fa-shield-alt me-2"></i> Verify Merge Action
            </h5>
        </div>
        <div style="padding: 25px;">
            <div style="text-align: center; margin-bottom: 20px;">
                <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #ffc107;"></i>
            </div>
            <p style="font-weight: 600; text-align: center; font-size: 16px;" id="modalCustomerNameText">Loading...</p>
            <div style="background: #f8f9fa; border: 1px solid #dee2e6; border-radius: 8px; padding: 15px; margin-top: 15px;">
                <strong><i class="fas fa-clipboard-list me-1 text-primary"></i> Duplicate criteria:</strong>
                <p style="margin-top: 8px; margin-bottom: 0; font-size: 13px; color: #6c757d;" id="modalMatchReasonText"></p>
            </div>
            <div style="margin-top: 15px; padding: 15px; border: 1px solid #dee2e6; border-radius: 8px;">
                <div style="margin-bottom: 10px;">
                    <span style="font-size: 13px; color: #6c757d;"><i class="far fa-envelope me-1"></i> Email:</span>
                    <span style="float: right;" id="modalEmailText">-</span>
                </div>
                <div>
                    <span style="font-size: 13px; color: #6c757d;"><i class="fas fa-phone-alt me-1"></i> Phone:</span>
                    <span style="float: right;" id="modalPhoneText">-</span>
                </div>
            </div>
            <div style="margin-top: 20px;">
                <label style="font-weight: 600; margin-bottom: 8px; display: block;"><i class="fas fa-check-circle me-1 text-primary"></i> Verification required</label>
                <input type="text" id="verifyCodeInput" class="form-control" placeholder="Enter VERIFY123" style="width: 100%; padding: 10px; border: 1px solid #ced4da; border-radius: 6px;">
                <small style="color: #6c757d; margin-top: 8px; display: block;">Type <strong style="color: #004392;">VERIFY123</strong> to confirm merge action.</small>
            </div>
        </div>
        <div style="padding: 15px 25px 25px; text-align: right; border-top: 1px solid #dee2e6;">
            <button id="cancelMergeBtn" style="background: transparent; border: 1px solid #dee2e6; padding: 8px 20px; border-radius: 6px; margin-right: 10px; cursor: pointer;">Cancel</button>
            <button id="confirmMergeBtnModal" style="background: linear-gradient(180deg, #2c7fc3 0%, #233d79 100%); border: none; padding: 8px 20px; border-radius: 6px; color: white; cursor: pointer;">Confirm Merge</button>
        </div>
    </div>
</div>

<style>
.bg-purple { background-color: #6f42c1; }
.text-purple { color: #6f42c1; }
.border-purple { border-color: #6f42c1; }
.fs-20 { font-size: 20px; }
.fs-13 { font-size: 13px; }
.fs-11 { font-size: 11px; }
.fs-10 { font-size: 10px; }
.rounded-4 { border-radius: 12px !important; }

.table th {
    background: #004392 !important;
    color: #fff !important;
}
.btn-primary,
.btn-warning,
.btn-danger,
.btn-info {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('mergeModal');
    let currentMergeUrl = '';
    
    // Show modal function
    function showModal() {
        modal.style.display = 'flex';
    }
    
    // Hide modal function
    function hideModal() {
        modal.style.display = 'none';
        document.getElementById('verifyCodeInput').value = '';
        document.getElementById('verifyCodeInput').style.borderColor = '#ced4da';
    }
    
    // All merge buttons
    const mergeBtns = document.querySelectorAll('.merge-account-btn');
    
    mergeBtns.forEach(function(btn) {
        btn.addEventListener('click', function() {
            const customerId = this.getAttribute('data-customer-id');
            const customerName = this.getAttribute('data-customer-name');
            const email = this.getAttribute('data-customer-email');
            const phone = this.getAttribute('data-customer-phone');
            const matchReasons = this.getAttribute('data-match-reasons');
            currentMergeUrl = this.getAttribute('data-merge-url');
            
            document.getElementById('modalCustomerNameText').innerHTML = '<i class="fas fa-user-circle me-2 text-primary"></i> <strong>' + customerName + '</strong> <span style="color: #6c757d; font-size: 13px;">(ID: ' + customerId + ')</span>';
            document.getElementById('modalMatchReasonText').innerText = matchReasons || 'Potential duplicate accounts detected.';
            document.getElementById('modalEmailText').innerHTML = email || '-';
            document.getElementById('modalPhoneText').innerHTML = phone || '-';
            
            showModal();
        });
    });
    
    // Confirm button
    document.getElementById('confirmMergeBtnModal').addEventListener('click', function() {
        const code = document.getElementById('verifyCodeInput').value.trim();
        
        if (code === 'VERIFY123') {
            hideModal();
            window.location.href = currentMergeUrl;
        } else {
            document.getElementById('verifyCodeInput').style.borderColor = '#dc3545';
            alert('Invalid verification code! Please enter VERIFY123');
        }
    });
    
    // Cancel button
    document.getElementById('cancelMergeBtn').addEventListener('click', function() {
        hideModal();
    });
    
    // Close on outside click
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal();
        }
    });
    
    // Enter key press
    document.getElementById('verifyCodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            document.getElementById('confirmMergeBtnModal').click();
        }
    });
});
</script>
@endsection
