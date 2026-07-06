@extends('vshop.seller.main')

@php
/* ================= ADDRESS FETCH ================= */
$country = null;
$state   = null;
$city    = null;

if($seller->bank_country){
    $country = \DB::table('countries')->where('id',$seller->bank_country)->value('name');
}
if($seller->bank_state){
    $state = \DB::table('states')->where('id',$seller->bank_state)->value('name');
}
if($seller->bank_city){
    $city = \DB::table('cities')->where('id',$seller->bank_city)->value('name');
}

$contacts = $seller->contact_persons ?? [];
@endphp
<style>
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    font-size: 4px;
    width: 35px;
    height: 38px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.marketplace-wrapper {
        display: flex;
        gap: 25px;
        align-items: flex-start;
    }
    .marketplace-left {
        flex: 1;
    }
    .marketplace-right {
        width: 300px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        display: flex !important;
        align-items: center;
        padding: 9px !important;
    }
    .btn-success {
        background: #233d78 !important;
        color: #fff !important;
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-4">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">Seller Profile</h5>
</div>


{{-- ================= ONBOARDING ================= --}}
<div class="card mb-4 shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-bold">Seller Onboarding</h5>

    <div>
        @if($seller->status == 'approved')
            <span class="badge bg-success">Approved</span>
        @elseif($seller->status == 'pending')
            <span class="badge bg-warning text-dark">Under Review</span>
        @else
            <span class="badge bg-secondary">Incomplete</span>
        @endif

        <a href="{{ route('seller.staff.create') }}" class="btn btn-success btn-sm ms-2">
            + Staff
        </a>
    </div>
</div>

@php
    $completed = collect($progress)->filter()->count();
    $total = count($progress);
    $percent = ($completed / $total) * 100;
@endphp

<div class="progress mb-4" style="height:8px;">
    <div class="progress-bar bg-success" style="width: {{ $percent }}%"></div>
</div>

<div class="d-flex align-items-center justify-content-between flex-wrap gap-2">

@foreach($progress as $step => $done)
<div class="text-center">
    <div class="rounded-circle {{ $done ? 'bg-success text-white' : 'bg-light' }}"
         style="width:40px;height:40px;line-height:40px;">
        {{ $loop->iteration }}
    </div>
    <small>{{ ucfirst($step) }}</small><br>
    <small class="{{ $done ? 'text-success' : 'text-warning' }}">
        {{ $done ? 'Done' : 'Pending' }}
    </small>
</div>
@endforeach

</div>

</div>
</div>

{{-- ================= MAIN CARD ================= --}}
<div class="card shadow-sm">
<div class="card-body">

<ul class="nav nav-tabs mb-4">
<li class="nav-item"><a class="nav-link active" data-bs-toggle="tab" href="#basic">Basic</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#organization">Organization</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#contact">Contact</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#bank">Bank</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#payment">Payment</a></li>
<li class="nav-item"><a class="nav-link" data-bs-toggle="tab" href="#agreement">Agreement</a></li>
</ul>

<div class="tab-content">

{{-- ================= BASIC (EDITABLE) ================= --}}
<div class="tab-pane fade show active" id="basic">

<form id="basicForm" method="POST" action="{{ route('seller.basic.update') }}">
@csrf

<div class="d-flex justify-content-end mb-2">
    <button type="button" id="editBtn" class="btn btn-primary btn-sm">Edit</button>
    <button type="submit" id="saveBtn" class="btn btn-success btn-sm d-none">Save</button>
</div>

<div class="row">
<div class="col-md-3 text-center">
<img src="{{ $seller->logo ? asset($seller->logo) : asset('vtravelassets/images/avatar.png') }}"
class="img-fluid rounded mb-3 shadow-sm" width="120">
</div>

<div class="col-md-9">

<table class="table table-bordered">
<tr>
    <th>Name</th>
    <td>
        <span class="view-mode">{{ $seller->name }}</span>
        <input type="text" name="name" value="{{ $seller->name }}" class="form-control edit-mode d-none">
    </td>
</tr>

<tr>
    <th>Email</th>
    <td>
        <span class="view-mode">{{ $seller->email }}</span>
        <input type="email" name="email" value="{{ $seller->email }}" class="form-control edit-mode d-none">
    </td>
</tr>

<tr>
    <th>Mobile</th>
    <td>
        <span class="view-mode">{{ $seller->mobile }}</span>
        <input type="text" name="mobile" value="{{ $seller->mobile }}" class="form-control edit-mode d-none">
    </td>
</tr>

<tr>
<th>Status</th>
<td>
<span class="badge bg-success">{{ $seller->status }}</span>
</td>
</tr>

</table>

</div>
</div>

</form>

</div>

{{-- ================= READ ONLY SECTIONS ================= --}}
<div class="tab-pane fade" id="organization">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-buildings me-2"></i>Organization Details</h6>
        <a href="{{ route('seller.organization') }}" class="btn btn-primary btn-sm"><i class="fa fa-edit me-1"></i> Edit Organization</a>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <tr><th width="220">Application</th><td>{{ $seller->application }}</td></tr>
            <tr><th>Legal Name</th><td>{{ $seller->legal_name }}</td></tr>
            <tr><th>Business Type</th><td>{{ $seller->business_type }}</td></tr>
            <tr><th>Hide Price</th><td>{{ $seller->hide_price }}</td></tr>
            <tr><th>CIN</th><td>{{ $seller->cin }}</td></tr>
            <tr><th>Incorporation Date</th><td>{{ $seller->incorporation_date }}</td></tr>
            <tr><th>PAN Number</th><td>{{ $seller->pan_number }}</td></tr>
            <tr><th>PAN Expiry</th><td>{{ $seller->pan_expiry }}</td></tr>
            <tr><th>GST Number</th><td>{{ $seller->gst_number }}</td></tr>
            <tr><th>GST Expiry</th><td>{{ $seller->gst_expiry }}</td></tr>
            <tr><th>IEC</th><td>{{ $seller->has_iec }}</td></tr>
            <tr><th>IEC Code</th><td>{{ $seller->iec_code }}</td></tr>
            <tr><th>Phone</th><td>{{ $seller->phone }}</td></tr>
            <tr><th>Email</th><td>{{ $seller->email }}</td></tr>
            <tr><th>Billing Address</th><td>{{ $seller->billing_address }}<br>ZIP: {{ $seller->billing_zip }}</td></tr>
            <tr><th>Contact Address</th><td>{{ $seller->contact_address ?? '-' }}</td></tr>
            <tr>
                <th>Logo</th>
                <td>
                    @if($seller->logo)
                    <img src="{{ asset($seller->logo) }}" width="80" class="rounded">
                    @else - @endif
                </td>
            </tr>
        </table>
    </div>
</div>

<div class="tab-pane fade" id="contact">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-person-lines-fill me-2"></i>Contact Persons</h6>
        <a href="{{ route('seller.contact') }}" class="btn btn-primary btn-sm"><i class="fa fa-plus me-1"></i> Add / Edit Contact</a>
    </div>
    
    @if(!empty($contacts) && count($contacts) > 0)
        @foreach($contacts as $index => $c)
            @php $countryName = \App\Models\Country::where('id',$c['address']['country'] ?? null)->value('name'); @endphp
            <div class="card shadow-sm border-0 mb-3 bg-light">
                <div class="card-header border-bottom d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fa-solid fa-user text-muted me-2"></i> {{ $c['first_name'] }} {{ $c['last_name'] }}</span>
                    <span class="badge bg-primary rounded-pill">{{ $c['designation'] ?? 'Contact' }}</span>
                </div>
                <div class="card-body bg-white">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <small class="text-muted d-block">Mobile</small>
                            <span class="fw-semibold">{{ $c['mobile'] ?? '-' }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">WhatsApp</small>
                            <span class="fw-semibold">{{ $c['whatsapp'] ?? '-' }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Email</small>
                            <span class="fw-semibold">{{ $c['email'] ?? '-' }}</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted d-block">Passport</small>
                            <span class="fw-semibold">{{ $c['passport'] ?? '-' }}</span>
                        </div>
                        <div class="col-md-12">
                            <small class="text-muted d-block">Address</small>
                            <span class="fw-semibold">
                                {{ $c['address']['street'] ?? '' }} 
                                {{ $c['address']['city'] ?? '' }} 
                                {{ $c['address']['state'] ?? '' }} 
                                {{ $countryName ?? '' }} 
                                {{ $c['address']['zip'] ?? '' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-secondary text-center">No contact persons added yet.</div>
    @endif
</div>

<div class="tab-pane fade" id="bank">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-bank me-2"></i>Bank Details</h6>
        <a href="{{ route('seller.bank') }}" class="btn btn-primary btn-sm"><i class="fa fa-edit me-1"></i> Add / Edit Bank</a>
    </div>
    
    @if($seller->bank_account_name)
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <tr><th width="220">Account Name</th><td>{{ $seller->bank_account_name }}</td></tr>
            <tr><th>Account Number</th><td>{{ $seller->bank_account_number }}</td></tr>
            <tr><th>IFSC Code</th><td>{{ $seller->ifsc_code ?? '-' }}</td></tr>
            <tr>
                <th>Bank Address</th>
                <td>{{ $country ?? '-' }} @if($state) , {{ $state }} @endif @if($city) , {{ $city }} @endif</td>
            </tr>
            <tr><th>SWIFT Code</th><td>{{ $seller->swift_code ?? '-' }}</td></tr>
        </table>
    </div>
    @else
        <div class="alert alert-secondary text-center">No bank details added yet.</div>
    @endif
</div>

<div class="tab-pane fade" id="payment">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-credit-card me-2"></i>Payment Gateway</h6>
        <a href="{{ route('seller.payment') }}" class="btn btn-primary btn-sm"><i class="fa fa-edit me-1"></i> Add / Edit Payment</a>
    </div>
    
    @if($seller->paypal_email || $seller->api_key)
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <tr>
                <th width="220"><i class="bi bi-paypal text-primary me-2"></i>PayPal Email</th>
                <td>{{ $seller->paypal_email ?? '-' }}</td>
            </tr>
            <tr>
                <th><i class="bi bi-key-fill text-warning me-2"></i>API Key</th>
                <td>{{ $seller->api_key ?? '-' }}</td>
            </tr>
            <tr>
                <th>Check / Money Order</th>
                <td>{!! nl2br(e($seller->check_money_order)) !!}</td>
            </tr>
            <tr>
                <th>Wire Transfer</th>
                <td>{!! nl2br(e($seller->wire_transfer)) !!}</td>
            </tr>
        </table>
    </div>
    @else
        <div class="alert alert-secondary text-center">No payment gateway details added yet.</div>
    @endif
</div>

<div class="tab-pane fade" id="agreement">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-file-earmark-text me-2"></i>Agreement details</h6>
        <a href="{{ route('seller.agreement') }}" class="btn btn-primary btn-sm"><i class="fa fa-edit me-1"></i> Edit Agreement</a>
    </div>
    
    <div class="table-responsive">
        <table class="table table-bordered">
            <tr>
                <th width="220">Seller Agreement Terms</th>
                <td>
                    <div style="max-height: 200px; overflow-y: auto; background: #f9f9f9; padding: 10px; border-radius: 5px; font-size: 13px;">
                        {!! $seller->agreement_terms ?? 'Standard terms applied.' !!}
                    </div>
                </td>
            </tr>
            <tr>
                <th>Accepted Agreement</th>
                <td>
                    @if($seller->is_agreed)
                        <span class="badge bg-success">Yes, Agreed</span>
                    @else
                        <span class="badge bg-danger">Not Agreed</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th>Signature Image</th>
                <td>
                    @if($seller->signature_image)
                        <img src="{{ asset($seller->signature_image) }}" style="max-width:200px; border:1px solid #ddd; padding:5px; border-radius:4px;">
                    @else
                        <span class="text-muted">No Signature</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>
</div>

</div>
</div>
</div>

</div>

{{-- ================= SCRIPT ================= --}}
<script>
document.getElementById('editBtn').addEventListener('click', function(){

    document.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
    document.querySelectorAll('.edit-mode').forEach(el => el.classList.remove('d-none'));

    document.getElementById('editBtn').classList.add('d-none');
    document.getElementById('saveBtn').classList.remove('d-none');

});
</script>

@endsection
