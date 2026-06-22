@extends('vshop.seller.main')

<style>
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        display: flex !important;
        align-items: center;
        padding: 9px !important;
    }
    .btn-primary i {
        font-size: 16px;
        margin-right: 8px !important;
    }
    .b
</style>

@section('content')

@php
$seller = $seller ?? auth()->guard('seller')->user();

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
@endphp

<div class="container-fluid py-4">

<div class="marketplace-wrapper">

{{-- ================= LEFT CONTENT ================= --}}
<div class="marketplace-left">

@include('vshop.seller.partials.onboarding-header')

<div class="card shadow-sm border-0">
<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">

<h5 class="text-primary mb-0">
Bank Account Details
</h5>

<a href="{{ route('seller.bank') }}"
class="btn btn-primary btn-sm">
<i class="fa fa-plus me-1"></i>
Add / Edit Bank
</a>

</div>

@if($seller->bank_account_name)

<table class="table table-bordered align-middle">

<tr>
<th width="220">Account Name</th>
<td>{{ $seller->bank_account_name }}</td>
</tr>

<tr>
<th>Account Number</th>
<td>{{ $seller->bank_account_number }}</td>
</tr>

<tr>
<th>IFSC Code</th>
<td>{{ $seller->ifsc_code ?? '-' }}</td>
</tr>

{{-- ✅ NEW BANK ADDRESS --}}
<tr>
<th>Bank Address</th>
<td>
    {{ $country ?? '-' }}
    @if($state) , {{ $state }} @endif
    @if($city) , {{ $city }} @endif
</td>
</tr>

<tr>
<th>SWIFT Code</th>
<td>{{ $seller->swift_code ?? '-' }}</td>
</tr>

<tr>
<th>BIC Code</th>
<td>{{ $seller->bic_code ?? '-' }}</td>
</tr>
<tr>
<th>Branch Name</th>
<td>{{ $seller->branch_name ?? '-' }}</td>
</tr>
<tr>
<th>Cancel Cheque</th>
<td>
@if($seller->cancel_cheque)
<a href="{{ asset($seller->cancel_cheque) }}"
target="_blank">
View File
</a>
@else
-
@endif
</td>
</tr>

<tr>
<th>Action</th>

<td>

<a href="{{ route('seller.bank') }}"
class="btn btn-warning btn-sm">
<i class="fa fa-edit"></i>
</a>

<form method="POST"
action="{{ route('seller.bank.delete', $seller->id) }}"
class="d-inline">
@csrf
@method('DELETE')

<button type="submit"
onclick="return confirm('Delete bank details?')"
class="btn btn-danger btn-sm">
<i class="fa fa-trash"></i>
</button>

</form>

</td>
</tr>

</table>

@else

<div class="alert alert-warning mb-0">
No bank account added yet.
Please add bank details.
</div>

@endif

</div>
</div>

</div>

{{-- ================= RIGHT SIDEBAR ================= --}}
<div class="marketplace-right">

<div class="profile-right">
@include('vshop.seller.partials.profile-progress')
</div>

</div>

</div>
</div>

<style>

/* =========================================
   MARKETPLACE LAYOUT
========================================= */

.marketplace-wrapper{
    display:flex;
    align-items:flex-start;
    gap:24px;
}

/* =========================================
   LEFT SIDE
========================================= */

.marketplace-left{
    flex:1;
    min-width:0;
}

/* =========================================
   RIGHT SIDEBAR
========================================= */

.marketplace-right{
    width:320px;
    min-width:320px;
    position:sticky;
    top:20px;
    align-self:flex-start;
}

/* =========================================
   PROFILE RIGHT
========================================= */

.profile-right{
    position:sticky;
    top:20px;
}

/* =========================================
   MOBILE
========================================= */

@media(max-width:991px){

    .marketplace-wrapper{
        flex-direction:column;
    }

    .marketplace-right{
        width:100%;
        min-width:100%;
        position:relative;
        top:auto;
    }

    .profile-right{
        position:relative;
        top:auto;
    }

}

</style>

@endsection