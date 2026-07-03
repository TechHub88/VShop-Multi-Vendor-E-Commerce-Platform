@extends('vshop.seller.main')

<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        font-size: 4px;
        width: 35px;
        height: 38px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.2s;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .action-btn {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #eef3fb;
        border-radius: 6px;
        color: #0b4da1;
        text-decoration: none;
    }
    .action-btn i {
        font-size: 11px !important;
    }
</style>

@section('content')

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/flag-icons@6.6.6/css/flag-icons.min.css">

<div class="container-fluid py-4">

@include('vshop.seller.partials.marketplace-header',[
'title'=>'Area of Operation'
])

<div class="marketplace-wrapper">

{{-- ================= LEFT ================= --}}
<div class="marketplace-left">

<div class="d-flex justify-content-between align-items-center mb-4">

<div class="d-flex top-back-heading-sec">
    <a href="{{ url('seller/marketplace/template') }}"
    class="nav-btn">
    
    <i class="fa-solid fa-arrow-left"></i>
    
    </a>
   <h5 class="mb-0">Saved Area of Operation</h5>
</div>

<a href="{{ route('seller.marketplace.area') }}"
class="add-btn">
+
</a>
</div>

   {{-- SUCCESS --}}
            @if(session('success'))

            <div class="alert alert-success successMessage">

                {{ session('success') }}

            </div>

            @endif


{{-- ================= GROUP LIST ================= --}}
@forelse($areas as $groupId => $countries)

<div class="marketplace-card">

{{-- ================= LEFT ================= --}}
<div class="card-info">

<div class="country-flex">

@foreach($countries as $countryId => $records)

@php

$records = collect($records);

$country = optional($records->first()->country);

$iso = strtolower($country->shortname ?? '');

$hasAll = $records->where('location_type','all')->count();

$groupStates = $records
->where('location_type','specific')
->groupBy('state_id');

@endphp


<div class="country-box">

<h6 class="mb-2">

@if($iso)
<span class="fi fi-{{ $iso }} me-2"></span>
@else
🌍
@endif

{{ $country->name ?? 'N/A' }}

</h6>

<p class="text-muted small mb-2">
<strong>States and Cities</strong>
</p>

@if($hasAll)

<p class="small text-success mb-0">
All Locations Selected
</p>

@else

@foreach($groupStates as $stateId => $rows)

@php

$stateName = \App\Models\State::where('id',$stateId)->value('name');

$cityIds = collect($rows)->pluck('city_id')->filter()->unique();

$cities = \App\Models\City::whereIn('id',$cityIds)->pluck('name');

@endphp

@if($stateName)

<div class="state-row">

<strong>{{ $stateName }}</strong>

<span class="district-list">
→ {{ $cities->count() ? $cities->implode(', ') : 'No city selected' }}
</span>

</div>

@endif

@endforeach

@endif

</div>

@endforeach

</div>

</div>


<div class="card-action d-flex align-items-center gap-1 flex-nowrap">

    <a href="{{ route('seller.marketplace.area.edit',$groupId) }}"
       class="btn btn-primary btn-sm d-flex align-items-center justify-content-center p-0 action-btn">
        <i class="fa fa-edit"></i>
    </a>

    <a href="{{ route('seller.marketplace.area.delete',$groupId) }}"
       class="btn btn-danger btn-sm d-flex align-items-center justify-content-center p-0 action-btn"
       onclick="return confirm('Delete this area?')">
        <i class="fa fa-trash"></i>
    </a>

</div>

</div>

@empty

<div class="alert alert-info">
No Area Added Yet
</div>

@endforelse

</div>


{{-- ================= RIGHT ================= --}}
<div class="marketplace-right">
@include('vshop.seller.partials.marketplace-progress')
</div>

</div>

</div>


<script>

/* ================= SUCCESS AUTO HIDE ================= */

document.addEventListener('DOMContentLoaded', function () {

    let successBox =
    document.querySelector('.successMessage');

    if(successBox){

        setTimeout(function(){

            successBox.style.transition =
            "all 0.5s ease";

            successBox.style.opacity = "0";

            successBox.style.transform =
            "translateY(-10px)";

            setTimeout(function(){

                successBox.remove();

            },500);

        },2500);

    }

});

</script>
@endsection


<style>

.card-action{
    display:flex;
    gap:4px;
    align-items:center;
    flex-wrap:nowrap;
}

/* FIXED ICON BUTTON SIZE */
.action-btn{
    width:26px;
    height:26px;
    font-size:12px;
    padding:0;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:4px;
}

/* prevent wrapping in card */
.marketplace-card{
    align-items:center;
}

/* ===== LAYOUT ===== */
.marketplace-wrapper{
display:flex;
gap:25px;
align-items:flex-start;
}

.marketplace-left{
flex:1;
}

.marketplace-right{
width:300px;
position:sticky;
top:20px;
}


/* ===== CARD ===== */
.marketplace-card{
background:#fff;
padding:18px;
border-radius:12px;
margin-bottom:18px;
box-shadow:0 3px 12px rgba(0,0,0,.05);

display:flex;
justify-content:space-between;
align-items:flex-start;
gap:20px;
}


/* ===== COUNTRY FLEX ===== */
.country-flex{
display:flex;
gap:20px;
flex-wrap:wrap;
}


/* ===== COUNTRY BOX ===== */
.country-box{
min-width:280px;
border:1px solid #eee;
border-radius:10px;
padding:15px;
background:#fafafa;
}


/* ===== STATE ===== */
.state-row{
display:flex;
gap:8px;
margin-bottom:6px;
font-size:13px;
flex-wrap:wrap;
}


/* ===== CITY ===== */
.district-list{
color:#6c757d;
}


/* ===== ACTION ===== */
.card-action{
min-width:90px;
}


/* ===== ADD BUTTON ===== */
.add-btn{
width:38px;
height:38px;
border-radius:50%;
background:#0d6efd;
display:flex;
align-items:center;
justify-content:center;
font-size:22px;
text-decoration:none;
color:#fff;
}

.add-btn:hover{
color:#fff;
}



</style>