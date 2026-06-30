@extends('vshop.seller.main')
<style>

    .btn-primary {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">

@include('vshop.seller.partials.marketplace-header',['title'=>'Area of Operation'])


<div class="marketplace-wrapper">

<div class="marketplace-left">

<form method="POST" action="{{route('seller.marketplace.area.save')}}" id="areaForm">
@csrf

<div class="premium-form-container mb-4">

<div class="header-action-row">
    <div class="top-back-heading-sec">
       <a href="{{ route('seller.marketplace.seo') }}" class="premium-nav-btn">
           <i class="fa-solid fa-arrow-left"></i>
       </a>
        <h5>Select Category <span class="text-danger">*</span></h5>
    </div>
    <div>
        <a href="{{ route('seller.marketplace.area.list') }}" class="premium-list-btn" title="Area List">
            <i class="bi bi-list"></i>
        </a>
    </div>
</div>

<div class="mb-2">
    <small>Status:
        <span id="saveStatus" class="badge bg-secondary">Idle</span>
    </small>
</div>

<select id="countrySelect" class="form-control mb-3">
<option value="">Select Country</option>
@foreach($countries as $country)
<option value="{{$country->id}}">{{$country->name}}</option>
@endforeach
</select>

<div id="selectedCountries"></div>

<div class="text-end mt-4">
    <button class="premium-btn-save w-100 mt-3">Save</button>
</div>

</div>
</form>

</div>

<div class="marketplace-right">
@include('vshop.seller.partials.marketplace-progress')



</div>

</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<script>

let i = 0;
const DRAFT_KEY = "area_draft_v1";

/* SAVE */
function saveDraft(){
    $('#saveStatus').removeClass().addClass('badge bg-warning').text('Saving...');

    localStorage.setItem(DRAFT_KEY, $('#areaForm').serialize());

    setTimeout(()=>{
        $('#saveStatus').removeClass().addClass('badge bg-success').text('Saved');
        updateProgress();
    }, 500);
}

/* LOAD */
$(document).ready(function(){
    loadDraft();
});

function loadDraft(){
    let data = localStorage.getItem(DRAFT_KEY);
    if(!data) return;

    $('#saveStatus').removeClass().addClass('badge bg-info').text('Draft Restored');
    updateProgress();
}

/* COUNTRY */
$('#countrySelect').change(function(){

let cid = $(this).val();
let cname = $("#countrySelect option:selected").text();

if(!cid) return;

i++;

$.get("{{url('seller/marketplace/get-states')}}/"+cid,function(states){

let html = `
<div class="country-card">

<input type="hidden" name="addresses[${i}][country_id]" value="${cid}">

<div class="country-header">
<h6>${cname}</h6>
<button type="button" class="btn btn-danger btn-sm removeCountry">Remove</button>
</div>

<div class="location-type">

<label>
<input type="radio" name="addresses[${i}][location_type]" value="all" checked>
All Location
</label>

<label>
<input type="radio" name="addresses[${i}][location_type]" value="specific" class="spec" data-i="${i}">
Specific Location
</label>

</div>

<div id="state_${i}" class="state-box" style="display:none">`;

/* FIXED STATES LOOP */
states.forEach(s=>{

html+=`
<div class="state-item">

<label>
<input type="checkbox" class="state" data-i="${i}" data-id="${s.id}">
${s.name}
</label>

<div id="city_${i}_${s.id}" class="city-box"></div>

</div>`;
});

html+=`</div></div>`;

$('#selectedCountries').append(html);

// CHANGE PLACEHOLDER
$('#countrySelect option:first').text('Select More Countries');

// RESET SELECT BOX
$('#countrySelect').val('');

saveDraft();
updateProgress();

});

});

/* LOCATION TYPE */
$(document).on('change','input[name*="[location_type]"]',function(){

let card = $(this).closest('.country-card');
let index = card.find('.spec').data('i');

if($(this).val() === 'specific'){
$('#state_'+index).slideDown();
}else{
$('#state_'+index).slideUp();
$('#state_'+index).find('input').prop('checked', false);
$('#state_'+index).find('.city-box').html('');
}

saveDraft();
});

/* STATE → CITY FIXED */
$(document).on('change','.state',function(){

let i = $(this).data('i');
let sid = $(this).data('id');
let box = $('#city_'+i+'_'+sid);

if(this.checked){

$.get("{{url('seller/marketplace/get-districts')}}/"+sid,function(cities){

let html = `
<div class="city-checkbox-list">

<div class="all-city">
    <input type="checkbox" class="all-city-checkbox" id="all_${sid}">
    <label for="all_${sid}"><b>All Cities</b></label>
</div>
`;

cities.forEach(city=>{
html+=`
<div class="city-item">
    <input type="checkbox"
        class="city-check"
        id="city_${sid}_${city.id}"
        name="addresses[${i}][districts][${sid}][]"
        value="${city.id}">
    <label for="city_${sid}_${city.id}">
        ${city.name}
    </label>
</div>`;
});

html+=`</div>`;

box.html(html);

saveDraft();

});

}else{
box.html('');
saveDraft();
}

});

/* ALL CITIES */
$(document).on('change','.all-city-checkbox',function(){
let container = $(this).closest('.city-checkbox-list');
container.find('.city-check').prop('checked', this.checked);
saveDraft();
});

/* CITY */
$(document).on('change','.city-check',function(){
let container = $(this).closest('.city-checkbox-list');

let total = container.find('.city-check').length;
let checked = container.find('.city-check:checked').length;

container.find('.all-city-checkbox').prop('checked', total === checked);

saveDraft();
});

/* REMOVE */
$(document).on('click','.removeCountry',function(){
$(this).closest('.country-card').remove();
saveDraft();
updateProgress();
});

/* PROGRESS */
function updateProgress(){
let total = $('.country-card').length;
let percent = total * 20;
if(percent > 100) percent = 100;

$('#verifyBar').css('width', percent+'%');
$('#verifyText').text(percent+'% Completed');
}

</script>

<style>

.city-item,.all-city{
    display:flex;
    align-items:center;
    gap:10px;
    margin-bottom:6px;
}

.city-item input,.all-city input{
    width:16px;
    height:16px;
    accent-color:#0d6efd;
}

.marketplace-wrapper{display:flex;gap:25px;}
.marketplace-left{flex:1;}
.marketplace-right{width:300px;position:sticky;top:20px;}

.area-card{padding:25px;border-radius:12px;box-shadow:0 6px 18px rgba(0,0,0,.06);}
.country-card{background:#f9fbff;padding:18px;border-radius:12px;margin-bottom:15px;border:1px solid #e5e7eb;}
.country-header{display:flex;justify-content:space-between;margin-bottom:10px;}
.location-type{display:flex;gap:25px;margin-bottom:10px;}
.state-box{max-height:260px;overflow:auto;padding:10px;border:1px solid #ddd;border-radius:10px;background:#fff;}
.city-checkbox-list{margin-top:8px;padding:10px;background:#fafafa;border-radius:8px;border:1px solid #eee;}

input[type="checkbox"]{
    -webkit-appearance: checkbox !important;
    appearance: auto !important;

    width:18px !important;
    height:18px !important;

    display:inline-block !important;
    opacity:1 !important;
    visibility:visible !important;

    accent-color:#0d6efd !important;
    position:relative !important;
    z-index:9999 !important;
}

</style>

@endsection