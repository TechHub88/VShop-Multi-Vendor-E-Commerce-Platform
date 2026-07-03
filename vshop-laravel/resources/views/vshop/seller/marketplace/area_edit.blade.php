@extends('vshop.seller.main')

<style>
    .btn-primary {
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
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .marketplace-wrapper{
        display:flex;
        gap:30px;
    }
    .marketplace-left{
        flex:1;
    }
    .marketplace-right{
        width:300px;
        position:sticky;
        top:20px;
    }
    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

@section('content')

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/flag-icons@6.6.6/css/flag-icons.min.css">

@php
$areas = $areas ?? [];
@endphp

<div class="container-fluid py-4">

<div class="marketplace-wrapper">

{{-- ================= LEFT ================= --}}
<div class="marketplace-left">

@include('vshop.seller.partials.marketplace-header',[
'title'=>'Edit Area of Operation'
])

<form method="POST"
action="{{ route('seller.marketplace.area.update',$groupId) }}">

@csrf

<div class="card shadow-sm border-0">

<div class="card-body">


<div class="d-flex top-back-heading-sec mb-3">
    <a href="{{ url('seller/marketplace/template') }}"
    class="nav-btn">
    
    <i class="fa-solid fa-arrow-left"></i>
    
    </a>
   <h5 class="mb-0">Edit Area *</h5>
</div>


{{-- COUNTRY SELECT --}}
<select id="countrySelect"
class="form-control mb-3">

<option value="">
Select Country
</option>

@foreach($countries as $c)

<option value="{{ $c->id }}">
{{ $c->name }}
</option>

@endforeach

</select>


{{-- COUNTRY BLOCK --}}
<div id="selectedCountries"></div>


<button class="btn btn-primary w-100 mt-4">
Update Area
</button>

</div>

</div>

</form>

</div>


{{-- ================= RIGHT ================= --}}
<div class="marketplace-right">

@include('vshop.seller.partials.marketplace-progress')

</div>

</div>

</div>

<style>

/* FORCE CHECKBOX */
input[type="checkbox"],
input[type="radio"]{
display:inline-block !important;
opacity:1 !important;
visibility:visible !important;
}


/* COUNTRY CARD */
.country-card{
border:1px solid #e5e7eb;
border-radius:14px;
padding:16px;
margin-bottom:18px;
background:#fff;
box-shadow:0 2px 10px rgba(0,0,0,.04);
}


/* HEADER */
.country-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:10px;
}

.country-header h6{
margin:0;
color:#0d6efd;
font-weight:600;
font-size:16px;
}


/* REMOVE */
.remove-country{
width:28px;
height:28px;
border-radius:50%;
background:#dc3545;
color:#fff;
display:flex;
align-items:center;
justify-content:center;
cursor:pointer;
font-size:14px;
font-weight:bold;
}


/* LOCATION */
.location-type{
display:flex;
gap:20px;
margin:14px 0;
}


/* STATE BOX */
.state-box{
max-height:300px;
overflow-y:auto;
border:1px solid #eee;
padding:12px;
border-radius:10px;
background:#fafafa;
}


/* STATE */
.state-item{
margin-bottom:12px;
}


/* LABEL */
.state-label,
.city-item{
display:flex;
align-items:center;
gap:8px;
cursor:pointer;
}


/* CITY */
.city-box{
margin-left:22px;
margin-top:8px;
}


/* CITY LIST */
.city-checkbox-list{
background:#f1f5f9;
padding:10px;
border-radius:8px;
}


/* ALL CITY */
.all-city-row{
font-weight:600;
margin-bottom:8px;
}

</style>


<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>

let existingAreas = @json($areas);

let index = 0;

/*
|--------------------------------------------------------------------------
| GROUP EXISTING
|--------------------------------------------------------------------------
*/
let grouped = {};

existingAreas.forEach(a => {

if(!grouped[a.country_id]){

grouped[a.country_id] = {
name: a.country?.name,
location_type: a.location_type,
states: {}
};

}

if(a.state_id){

if(!grouped[a.country_id].states[a.state_id]){
grouped[a.country_id].states[a.state_id] = [];
}

if(a.city_id){
grouped[a.country_id].states[a.state_id].push(a.city_id);
}

}

});


/*
|--------------------------------------------------------------------------
| LOAD EXISTING
|--------------------------------------------------------------------------
*/
Object.keys(grouped).forEach(countryId => {

let country = grouped[countryId];

addCountryBlock(
countryId,
country.name,
country
);

});


/*
|--------------------------------------------------------------------------
| CHECK COUNTRY EXIST
|--------------------------------------------------------------------------
*/
function isCountryAdded(id){

let exists = false;

$('.country-id').each(function(){

if($(this).val() == id){
exists = true;
}

});

return exists;

}


/*
|--------------------------------------------------------------------------
| COUNTRY SELECT
|--------------------------------------------------------------------------
*/
$('#countrySelect').change(function(){

let id = $(this).val();

let name = $(this).find('option:selected').text();

if(!id){
return;
}

if(isCountryAdded(id)){
alert('Country already added');
$(this).val('');
return;
}

addCountryBlock(id,name,null);

$(this).val('');

});


/*
|--------------------------------------------------------------------------
| ADD COUNTRY BLOCK
|--------------------------------------------------------------------------
*/
function addCountryBlock(countryId,name,areaData=null){

index++;

let checkedSpecific =
areaData?.location_type == 'specific'
? 'checked'
: '';

let checkedAll =
areaData?.location_type == 'all'
? 'checked'
: '';

if(!checkedSpecific && !checkedAll){
checkedSpecific = 'checked';
}

let html = `

<div class="country-card">

<input type="hidden"
class="country-id"
name="addresses[${index}][country_id]"
value="${countryId}">


<div class="country-header">

<h6>${name}</h6>

<div class="remove-country">
✕
</div>

</div>


<div class="location-type">

<label>
<input type="radio"
name="addresses[${index}][location_type]"
value="specific"
${checkedSpecific}>
Specific
</label>

<label>
<input type="radio"
name="addresses[${index}][location_type]"
value="all"
${checkedAll}>
All
</label>

</div>


<div class="state-box"
id="stateBox_${index}">

Loading...

</div>

</div>

`;

$('#selectedCountries').append(html);

loadStates(countryId,index,areaData);

}


/*
|--------------------------------------------------------------------------
| LOAD STATES
|--------------------------------------------------------------------------
*/
function loadStates(countryId,index,areaData){

$.get(
"{{ url('seller/marketplace/get-states') }}/"+countryId,
function(states){

let html='';

states.forEach(s=>{

let checked =
areaData?.states?.[s.id]
? 'checked'
: '';

html += `

<div class="state-item">

<label class="state-label">

<input type="checkbox"
class="state"
data-index="${index}"
data-id="${s.id}"
${checked}>

<span>${s.name}</span>

</label>

<div id="city_${index}_${s.id}"
class="city-box"></div>

</div>

`;

});

$('#stateBox_'+index).html(html);


/*
|--------------------------------------------------------------------------
| LOAD EXISTING CITY
|--------------------------------------------------------------------------
*/
states.forEach(s=>{

if(areaData?.states?.[s.id]){

$.get(
"{{ url('seller/marketplace/get-districts') }}/"+s.id,
function(cities){

let selectedCities =
areaData.states[s.id];

let html =
`<div class="city-checkbox-list">`;

html += `
<label class="city-item all-city-row">
<input type="checkbox"
class="all-city">
<span>All Cities</span>
</label>
`;

cities.forEach(c=>{

let checked =
selectedCities.includes(c.id)
? 'checked'
: '';

html += `
<label class="city-item">

<input type="checkbox"
name="addresses[${index}][districts][${s.id}][]"
value="${c.id}"
${checked}>

<span>${c.name}</span>

</label>
`;

});

html += `</div>`;

$('#city_'+index+'_'+s.id).html(html);

});

}

});

});

}


/*
|--------------------------------------------------------------------------
| STATE CLICK
|--------------------------------------------------------------------------
*/
$(document).on('change','.state',function(){

let index = $(this).data('index');

let sid = $(this).data('id');

if(this.checked){

$.get(
"{{ url('seller/marketplace/get-districts') }}/"+sid,
function(cities){

let html =
`<div class="city-checkbox-list">`;

html += `
<label class="city-item all-city-row">
<input type="checkbox"
class="all-city">
<span>All Cities</span>
</label>
`;

cities.forEach(c=>{

html += `
<label class="city-item">

<input type="checkbox"
name="addresses[${index}][districts][${sid}][]"
value="${c.id}">

<span>${c.name}</span>

</label>
`;

});

html += `</div>`;

$('#city_'+index+'_'+sid).html(html);

});

}else{

$('#city_'+index+'_'+sid).html('');

}

});


/*
|--------------------------------------------------------------------------
| ALL CITY
|--------------------------------------------------------------------------
*/
$(document).on('change','.all-city',function(){

$(this)
.closest('.city-checkbox-list')
.find('input[type=checkbox]')
.prop('checked',this.checked);

});


/*
|--------------------------------------------------------------------------
| REMOVE COUNTRY
|--------------------------------------------------------------------------
*/
$(document).on('click','.remove-country',function(){

$(this)
.closest('.country-card')
.remove();

});

</script>

@endsection