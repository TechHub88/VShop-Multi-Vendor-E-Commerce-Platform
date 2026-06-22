@extends('vshop.seller.main')
<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        width: 35px;
        height: 38px;
    }
    .bulk-btn {
        background-color : #004392 !important;
        border-color: #004392 !important;
        color: #fff !important;
        padding: 8px 8px !important;
        height: 36px !important;
    }
    .ai-btn {
        display: flex !important;
        align-items: center !important;
        font-size: 9px !important; 
        font-weight: 700 !important;
        gap: 6px !important;
        white-space: nowrap;
        margin-top: 28px;
        padding: 8px 18px !important;
        border-radius: 60px !important;
        border: 0 !important;
        background: linear-gradient(180deg, #d7f1ff 0%, #9fd0ff 25%, #7bb8ff 55%, #6aa8ff 75%, #8fd4ff 100%);
        color: #fff !important;
        letter-spacing: 2px;
        line-height: 14px;
        text-transform: uppercase;
    }
    .ai-btn:hover {
        box-shadow: inset 0 4px 10px rgba(255, 255, 255, 0.6), inset 0 -6px 12px rgba(0, 0, 0, 0.12), 0 0 20px rgba(120, 190, 255, 0.8);
    }
    .ai-btn svg {
        width: 22px;
        height: 22px;
    }
    .ai-btn i {
        color: orange;
        font-size: 14px;
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
</style>

@section('content')

<div class="container-fluid">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>

        <h5 class="mb-0 d-flex align-items-center gap-2"
            style="
                color:#0d6efd;
                font-weight:600;
            ">
            Customer Reviews
        </h5>
    </div>

    <div class="filter-buttons">
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

{{-- CARD --}}
<div class="card review-card">

<div class="table-responsive">

<table class="table align-middle review-table">

<thead>
<tr>
<th>#</th>
<th>Product</th> {{-- ✅ NEW --}}
<th>User</th>
<th>Rating</th>
<th>Review</th>
<th>Date</th>
<th>Status</th>
<th>Reply</th>
</tr>
</thead>

<tbody>

@forelse($reviews as $review)

<tr>

<td>{{ $loop->iteration }}</td>

{{-- ✅ PRODUCT NAME --}}
<td>
<div class="product-info">
<strong class="text-primary">
{{ $review->product->name ?? 'N/A' }}
</strong>

<br>

<small class="text-muted">
{{ $review->product->category->name ?? '' }}
</small>
</div>
</td>

<td>
<div class="user-info">
<strong>{{ $review->user_name }}</strong>
</div>
</td>

<td>
<span class="rating-badge 
@if($review->rating >=4) good
@elseif($review->rating <=2) bad
@else normal
@endif
">
{{ $review->rating }} ★
</span>
</td>

<td>
@if($review->comment)
<a href="javascript:void(0)" class="view-review" data-comment="{{ $review->comment }}">
View
</a>
@else
<span class="text-muted">No Review</span>
@endif
</td>

<td>{{ $review->created_at->format('d M Y') }}</td>

<td>
@if($review->is_approved)
<span class="badge bg-success">Active</span>
@else
<span class="badge bg-secondary">Inactive</span>
@endif
</td>

{{-- SELLER REPLY --}}
<td>
@if($review->reply)
<span class="text-success fw-semibold">Replied</span>
<br>
<small>{{ $review->reply }}</small>
@else
<button class="btn btn-sm btn-outline-primary reply-btn"
data-id="{{ $review->id }}">
Reply
</button>
@endif
</td>

</tr>

{{-- REPLY BOX --}}
<tr class="reply-row" id="reply-box-{{ $review->id }}" style="display:none;">
<td colspan="8">

<div class="reply-box">

<textarea class="form-control mb-2"
id="reply-text-{{ $review->id }}"
placeholder="Write your clarification / response..."></textarea>

<button class="btn btn-primary btn-sm"
onclick="submitReply({{ $review->id }})">
Submit Reply
</button>

</div>

</td>
</tr>

@empty

<tr>
<td colspan="8" class="text-center">No Reviews Found</td>
</tr>

@endforelse

</tbody>

</table>

</div>
</div>

</div>


{{-- MODAL FOR FULL REVIEW --}}
<div id="reviewModal" class="review-modal">
<div class="review-content">
<span class="close-modal">&times;</span>
<p id="reviewText"></p>
</div>
</div>


{{-- ================= STYLE ================= --}}
<style>

/* TITLE */
.page-title{
    font-weight:600;
}

/* CARD */
.review-card{
    border-radius:12px;
    padding:20px;
    border:1px solid #e6eaf0;
}

/* TABLE */
.review-table th{
    background:#f8fafc;
    font-weight:600;
}

/* PRODUCT */
.product-info strong{
    font-size:14px;
}

/* USER */
.user-info{
    font-size:14px;
}

/* RATING */
.rating-badge{
    padding:4px 10px;
    border-radius:20px;
    font-weight:600;
}

.rating-badge.good{
    background:#dcfce7;
    color:#15803d;
}

.rating-badge.bad{
    background:#fee2e2;
    color:#b91c1c;
}

.rating-badge.normal{
    background:#e2e8f0;
}

/* VIEW REVIEW */
.view-review{
    color:#2563eb;
    cursor:pointer;
    font-weight:500;
}

/* REPLY BOX */
.reply-box{
    background:#f9fafb;
    padding:15px;
    border-radius:8px;
}

/* MODAL */
.review-modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.5);
    z-index:999;
}

.review-content{
    background:#fff;
    padding:20px;
    border-radius:10px;
    width:400px;
    margin:10% auto;
    position:relative;
}

.close-modal{
    position:absolute;
    top:10px;
    right:15px;
    cursor:pointer;
}

</style>


{{-- ================= SCRIPT ================= --}}
<script>

// VIEW REVIEW MODAL
document.querySelectorAll('.view-review').forEach(el=>{
    el.addEventListener('click',function(){
        document.getElementById('reviewText').innerText = this.dataset.comment;
        document.getElementById('reviewModal').style.display='block';
    });
});

// CLOSE MODAL
document.querySelector('.close-modal').onclick=function(){
    document.getElementById('reviewModal').style.display='none';
};

// SHOW REPLY BOX
document.querySelectorAll('.reply-btn').forEach(btn=>{
    btn.addEventListener('click',function(){
        let id = this.dataset.id;
        document.getElementById('reply-box-'+id).style.display='table-row';
    });
});

// SUBMIT REPLY
function submitReply(id){
    let text = document.getElementById('reply-text-'+id).value;

    if(!text){
        alert('Enter reply');
        return;
    }

    // TODO: AJAX
    alert('Reply Submitted (Integrate backend)');
}

</script>

@endsection