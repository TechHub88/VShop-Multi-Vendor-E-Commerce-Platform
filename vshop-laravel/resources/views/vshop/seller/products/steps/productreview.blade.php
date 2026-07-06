@extends('vshop.seller.main')

@section('content')

<style>

.review-page-wrapper{
    display:flex;
    gap:20px;
    align-items:flex-start;
}

.review-left{
    flex:1;
    min-width:0;
}

.review-right{
    width:320px;
    flex-shrink:0;
}

.review-card{
    border:none;
    border-radius:16px;
    box-shadow:0 2px 12px rgba(0,0,0,0.05);
}

.rating-progress{
    height:8px;
    border-radius:20px;
    overflow:hidden;
}

.table td,
.table th{
    vertical-align:middle;
    white-space:nowrap;
}

.review-table td.review-message{
    white-space:normal;
    min-width:180px;
}

.review-modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.45);
    z-index:9999;
}

.review-content{
    background:#fff;
    padding:24px;
    border-radius:14px;
    width:420px;
    max-width:95%;
    margin:8% auto;
    position:relative;
}

.close-modal{
    position:absolute;
    right:15px;
    top:10px;
    cursor:pointer;
    font-size:22px;
}

@media(max-width:991px){

    .review-page-wrapper{
        flex-direction:column;
    }

    .review-right{
        width:100%;
    }
}

</style>

<div class="container-fluid px-2 py-3">

<div class="review-page-wrapper">

{{-- LEFT --}}
<div class="review-left">

{{-- ================= PAGE HEADER ================= --}}
@php
    $hasId = (isset($product) && !empty($product->id));

    $backUrl = $hasId
        ? route('seller.products.samplecode', $product->id)
        : route('seller.products.samplecode');
@endphp

<div class="d-flex align-items-center gap-3 mb-4">

    <a href="{{ $backUrl }}"
       style="
            width:36px;
            height:36px;
            border:1px solid #0d6efd;
            border-radius:6px;
            display:flex;
            align-items:center;
            justify-content:center;
            text-decoration:none;
            color:#0d6efd;
            background:#fff;
            font-size:16px;
            flex-shrink:0;
       ">

        <i class="fa-solid fa-arrow-left"></i>

    </a>

    <h5 class="mb-0 fw-bold d-flex align-items-center gap-2"
        style="
            color:#0d6efd;
            font-weight:600;
        ">

    

        Product Reviews

    </h5>

</div>

{{-- ================= RATING BREAKDOWN ================= --}}
@php
$ratingCounts = [5=>0,4=>0,3=>0,2=>0,1=>0];

foreach($reviews as $r){
    if(isset($ratingCounts[$r->rating])){
        $ratingCounts[$r->rating]++;
    }
}

$total = count($reviews);
@endphp

<div class="card review-card p-4 mb-4">

<div class="d-flex justify-content-between align-items-center mb-4">

    <h5 class="fw-bold mb-0">
        Rating Breakdown
    </h5>

</div>

@if(isset($product))
@include('vshop.seller.partials.draft-status', ['product' => $product])
@endif

@foreach([5,4,3,2,1] as $star)

<div class="d-flex align-items-center mb-3">

    <div style="width:55px;font-weight:600;">
        {{ $star }} ★
    </div>

    <div class="progress rating-progress flex-grow-1 mx-3">

        <div class="progress-bar
        @if($star >= 4)
        bg-success
        @elseif($star <= 2)
        bg-danger
        @else
        bg-secondary
        @endif"

        style="width:{{ $total ? ($ratingCounts[$star]/$total)*100 : 0 }}%">

        </div>

    </div>

    <div style="width:25px;text-align:right;">
        {{ $ratingCounts[$star] }}
    </div>

</div>

@endforeach

</div>


{{-- ================= CUSTOMER REVIEWS ================= --}}
<div class="card review-card p-4">

<div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">

    <h5 class="fw-bold mb-0">
        Customer Reviews
    </h5>

    <div class="d-flex gap-2 flex-wrap">

        <a href="?rating=all"
        class="btn btn-sm {{ ($rating ?? 'all')=='all' ? 'btn-primary' : 'btn-outline-primary' }}">

        All

        </a>

        <a href="?rating=top"
        class="btn btn-sm {{ ($rating ?? '')=='top' ? 'btn-success' : 'btn-outline-success' }}">

        4+ Rating

        </a>

        <a href="?rating=low"
        class="btn btn-sm {{ ($rating ?? '')=='low' ? 'btn-danger' : 'btn-outline-danger' }}">

        Low Rating

        </a>

    </div>

</div>


<div class="table-responsive">

<table class="table review-table align-middle">

<thead class="table-light">

<tr>

<th>#</th>
<th>User</th>
<th>Rating</th>
<th>Review</th>
<th>Date</th>
<th>Status</th>
<th>Actions</th>

</tr>

</thead>

<tbody>

@forelse($reviews as $review)

<tr class="
@if($review->rating <=2)
table-danger
@elseif($review->rating >=4)
table-success
@endif
">

<td>{{ $loop->iteration }}</td>

<td>
<strong>{{ $review->user_name }}</strong>
</td>

<td>

<span class="badge
@if($review->rating >= 4)
bg-success
@elseif($review->rating <= 2)
bg-danger
@else
bg-secondary
@endif">

{{ $review->rating }} ★

</span>

</td>

<td class="review-message">

@if($review->comment)

<a href="javascript:void(0)"
class="view-review text-primary fw-semibold"
data-comment="{{ $review->comment }}">

View Review

</a>

@else

<span class="text-muted">
No Review
</span>

@endif

</td>

<td>
{{ $review->created_at->format('d M Y') }}
</td>

<td>

@if($review->is_approved)

<span class="badge bg-success">
Active
</span>

@else

<span class="badge bg-secondary">
Inactive
</span>

@endif

</td>

<td>

<div class="d-flex flex-column gap-2">

@if($review->reply)

<div>

<span class="badge bg-success mb-1">
Replied
</span>

<div style="font-size:12px;">
{{ $review->reply }}
</div>

</div>

@else

<button class="btn btn-sm btn-outline-primary reply-btn"
data-id="{{ $review->id }}">

Reply

</button>

@endif

<button class="btn btn-sm btn-outline-danger flag-btn"
data-id="{{ $review->id }}">

Flag

</button>

</div>

</td>

</tr>


{{-- REPLY BOX --}}
<tr id="reply-box-{{ $review->id }}"
style="display:none;">

<td colspan="7">

<div class="p-3 bg-light rounded">

<textarea class="form-control mb-2"
id="reply-text-{{ $review->id }}"
placeholder="Write your response..."></textarea>

<button class="btn btn-primary btn-sm"
onclick="submitReply({{ $review->id }})">

Submit Reply

</button>

</div>

</td>

</tr>

@empty

<tr>

<td colspan="7"
class="text-center text-muted py-4">

No Reviews Found

</td>

</tr>

@endforelse

</tbody>

</table>

</div>


<div class="mt-4">

<a href="{{ route('seller.products.list', $product->id) }}"
class="btn btn-primary px-4 py-2">

Preview

</a>

</div>

</div>

</div>


{{-- RIGHT --}}
<div class="review-right">

@include('vshop.seller.partials.product-progress',[
'product'=>$product
])

</div>

</div>

</div>


{{-- REVIEW MODAL --}}
<div id="reviewModal"
class="review-modal">

<div class="review-content">

<span class="close-modal">
&times;
</span>

<h6 class="fw-bold mb-3">
Customer Review
</h6>

<p id="reviewText"
class="mb-0"></p>

</div>

</div>

<link rel="stylesheet"
href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<script>

// VIEW REVIEW
document.querySelectorAll('.view-review').forEach(el=>{

    el.onclick = function(){

        document.getElementById('reviewText').innerText =
        this.dataset.comment;

        document.getElementById('reviewModal').style.display =
        'block';
    };

});


// CLOSE MODAL
document.querySelector('.close-modal').onclick = function(){

    document.getElementById('reviewModal').style.display =
    'none';
};


// REPLY BUTTON
document.querySelectorAll('.reply-btn').forEach(btn=>{

    btn.onclick = function(){

        document.getElementById(
            'reply-box-' + this.dataset.id
        ).style.display = 'table-row';
    };

});


// SUBMIT REPLY
function submitReply(id){

    let text = document.getElementById(
        'reply-text-' + id
    ).value;

    if(!text){

        alert('Enter reply');

        return;
    }

    alert('Reply submitted');

}


// FLAG
document.querySelectorAll('.flag-btn').forEach(btn=>{

    btn.onclick = function(){

        if(confirm('Flag this review?')){

            alert('Review flagged');
        }
    };

});

</script>

@endsection
