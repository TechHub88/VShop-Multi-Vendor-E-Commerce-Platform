@extends('vshop.seller.main')

@section('content')

<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');

.reviews-page {
    font-family: 'Outfit', sans-serif;
    background: #f8fafc;
    min-height: 100vh;
    padding: 24px;
}

/* ======= HEADER ======= */
.reviews-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 24px;
    flex-wrap: wrap;
    gap: 12px;
}
.reviews-title {
    font-size: 22px;
    font-weight: 700;
    color: #0f172a;
    margin: 0;
}
.btn-back-review {
    width: 38px; height: 38px;
    border-radius: 10px;
    background: #fff;
    border: 1px solid #e2e8f0;
    color: #475569;
    display: inline-flex; align-items: center; justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}
.btn-back-review:hover { background: #f1f5f9; color: #0f172a; }

/* ======= STAT CARDS ======= */
.review-stats {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
.review-stat-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 18px 22px;
    display: flex;
    align-items: center;
    gap: 16px;
    transition: all 0.25s;
}
.review-stat-card:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(0,0,0,0.06); }
.review-stat-icon {
    width: 46px; height: 46px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px; flex-shrink: 0;
}
.review-stat-info small { font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; }
.review-stat-info h3 { font-size: 26px; font-weight: 800; margin: 0; color: #0f172a; }

/* ======= FILTER BAR ======= */
.review-filter-bar {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 20px;
    flex-wrap: wrap;
}
.filter-chip {
    padding: 6px 16px;
    border-radius: 50px;
    font-size: 13px;
    font-weight: 600;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #475569;
    text-decoration: none;
    transition: all 0.2s;
    cursor: pointer;
}
.filter-chip:hover { border-color: #004392; color: #004392; }
.filter-chip.active-all  { background: #004392; color: #fff; border-color: #004392; }
.filter-chip.active-good { background: #10b981; color: #fff; border-color: #10b981; }
.filter-chip.active-bad  { background: #ef4444; color: #fff; border-color: #ef4444; }

/* ======= TABLE CARD ======= */
.review-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03);
}
.review-table { width: 100%; border-collapse: collapse; }
.review-table thead th {
    background: #f8fafc;
    padding: 12px 16px;
    font-size: 12px;
    font-weight: 700;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-bottom: 1px solid #e2e8f0;
    white-space: nowrap;
}
.review-table tbody tr {
    border-bottom: 1px solid #f1f5f9;
    transition: background 0.15s;
}
.review-table tbody tr:last-child { border-bottom: none; }
.review-table tbody tr:hover { background: #fafbfc; }
.review-table td { padding: 14px 16px; font-size: 13.5px; vertical-align: middle; color: #334155; }

/* PRODUCT CELL */
.product-cell { display: flex; align-items: center; gap: 10px; }
.product-thumb {
    width: 38px; height: 38px;
    border-radius: 8px; object-fit: cover;
    border: 1px solid #e2e8f0; flex-shrink: 0;
}
.product-name { font-weight: 600; color: #0f172a; font-size: 13px; line-height: 1.4; }
.product-cat  { font-size: 11px; color: #94a3b8; margin-top: 2px; }

/* RATING BADGE */
.rating-pill {
    display: inline-flex; align-items: center; gap: 4px;
    padding: 4px 10px;
    border-radius: 50px;
    font-size: 12px; font-weight: 700;
}
.rating-pill.good { background: #dcfce7; color: #15803d; }
.rating-pill.mid  { background: #fef9c3; color: #854d0e; }
.rating-pill.bad  { background: #fee2e2; color: #b91c1c; }

/* STATUS BADGE */
.status-dot {
    display: inline-flex; align-items: center; gap: 6px;
    font-size: 12px; font-weight: 600;
}
.status-dot::before {
    content: ''; width: 7px; height: 7px;
    border-radius: 50%;
    display: inline-block;
}
.status-dot.active::before { background: #10b981; }
.status-dot.inactive::before { background: #94a3b8; }

/* REPLY */
.btn-reply {
    padding: 5px 14px;
    border-radius: 8px;
    font-size: 12px; font-weight: 600;
    border: 1.5px solid #004392;
    color: #004392; background: transparent;
    cursor: pointer; transition: all 0.2s;
}
.btn-reply:hover { background: #004392; color: #fff; }

/* EMPTY STATE */
.empty-reviews {
    text-align: center;
    padding: 60px 30px;
}
.empty-reviews i { font-size: 48px; color: #cbd5e1; margin-bottom: 12px; display: block; }
.empty-reviews h5 { color: #475569; font-weight: 600; }
.empty-reviews p  { color: #94a3b8; font-size: 13px; }

/* PAGINATION */
.review-pagination {
    padding: 16px 20px;
    display: flex; align-items: center; justify-content: space-between;
    border-top: 1px solid #f1f5f9;
    flex-wrap: wrap; gap: 10px;
}
.review-pagination .page-link {
    border-radius: 8px !important;
    font-size: 13px; font-weight: 600;
    color: #475569; border-color: #e2e8f0;
}
.review-pagination .page-item.active .page-link {
    background: #004392; border-color: #004392;
}

/* REPLY INLINE ROW */
.reply-row td { padding: 0 !important; }
.reply-box-inner {
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 14px 20px;
    display: flex; gap: 10px; align-items: flex-start;
}
.reply-box-inner textarea {
    flex: 1;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    padding: 10px 14px;
    font-size: 13px;
    resize: vertical;
    min-height: 70px;
    font-family: 'Outfit', sans-serif;
}
.btn-submit-reply {
    padding: 8px 18px;
    border-radius: 8px;
    background: #004392;
    color: #fff;
    font-size: 13px; font-weight: 600;
    border: none; cursor: pointer;
    white-space: nowrap;
    transition: opacity 0.2s;
}
.btn-submit-reply:hover { opacity: 0.85; }

/* COMMENT MODAL */
.comment-modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(15,23,42,0.5);
    z-index: 9999;
    align-items: center; justify-content: center;
}
.comment-modal-overlay.show { display: flex; }
.comment-modal-box {
    background: #fff;
    border-radius: 16px;
    padding: 28px;
    width: 480px; max-width: 95%;
    position: relative;
    animation: modalIn 0.2s ease;
}
@keyframes modalIn {
    from { transform: scale(0.95); opacity: 0; }
    to   { transform: scale(1);    opacity: 1; }
}
.comment-modal-close {
    position: absolute; top: 14px; right: 16px;
    background: none; border: none;
    font-size: 20px; color: #94a3b8; cursor: pointer;
}
.comment-modal-close:hover { color: #0f172a; }

@media (max-width: 768px) {
    .review-stats { grid-template-columns: 1fr; }
    .reviews-page { padding: 14px; }
}

/* PROGRESS BAR */
#reviewProgressBar {
    position: fixed; top: 0; left: 0;
    height: 3px; width: 0%;
    background: linear-gradient(90deg, #004392, #38bdf8, #004392);
    background-size: 200% 100%;
    animation: rProgressShimmer 1.2s linear infinite;
    z-index: 9999999;
    border-radius: 0 2px 2px 0;
    box-shadow: 0 0 8px rgba(0,67,146,0.5);
    display: none; opacity: 1;
    transition: width 0.35s ease;
}
@keyframes rProgressShimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Table fade during load */
#reviewTableBody { transition: opacity 0.2s ease; }
.table-loading { opacity: 0.35; pointer-events: none; }
</style>

<div id="reviewProgressBar"></div>
<div class="reviews-page">

    {{-- HEADER --}}
    <div class="reviews-header">
        <div class="d-flex align-items-center gap-3">
            <button onclick="goBack()" class="btn-back-review" title="Go Back">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <div>
                <h4 class="reviews-title">Customer Reviews</h4>
                <p class="text-muted mb-0" style="font-size:13px;">Reviews for your products — seller-specific</p>
            </div>
        </div>

        {{-- FILTER CHIPS (AJAX — no page reload) --}}
        <div class="review-filter-bar" style="margin-bottom:0;">
            <button onclick="filterReviews('')"  class="filter-chip {{ !$status ? 'active-all' : '' }}" id="chip-all">
                <i class="fa-solid fa-list me-1"></i> All (<span id="cnt-total">{{ $totalCount }}</span>)
            </button>
            <button onclick="filterReviews('4plus')" class="filter-chip {{ $status==='4plus' ? 'active-good' : '' }}" id="chip-4plus">
                <i class="fa-solid fa-thumbs-up me-1"></i> 4+ Rating (<span id="cnt-good">{{ $goodCount }}</span>)
            </button>
            <button onclick="filterReviews('low')"  class="filter-chip {{ $status==='low' ? 'active-bad' : '' }}" id="chip-low">
                <i class="fa-solid fa-thumbs-down me-1"></i> Low Rating (<span id="cnt-bad">{{ $badCount }}</span>)
            </button>
        </div>
    </div>

    {{-- STAT CARDS --}}
    <div class="review-stats">
        <div class="review-stat-card">
            <div class="review-stat-icon" style="background:#004392 15;color:#004392;">
                <i class="fa-solid fa-star"></i>
            </div>
            <div class="review-stat-info">
                <small>Total Reviews</small>
                <h3>{{ $totalCount }}</h3>
            </div>
        </div>
        <div class="review-stat-card">
            <div class="review-stat-icon" style="background:#dcfce7;color:#15803d;">
                <i class="fa-solid fa-face-smile"></i>
            </div>
            <div class="review-stat-info">
                <small>Good (4★+)</small>
                <h3>{{ $goodCount }}</h3>
            </div>
        </div>
        <div class="review-stat-card">
            <div class="review-stat-icon" style="background:#fee2e2;color:#b91c1c;">
                <i class="fa-solid fa-face-frown"></i>
            </div>
            <div class="review-stat-info">
                <small>Bad (≤2★)</small>
                <h3>{{ $badCount }}</h3>
            </div>
        </div>
    </div>

    {{-- TABLE --}}
    <div class="review-card">
        <div class="table-responsive">
            <table class="review-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>User</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                      
                        <th>Reply</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($reviews as $rev)
                <tr>
                    <td class="text-muted">{{ $reviews->firstItem() + $loop->index }}</td>

                    {{-- PRODUCT --}}
                    <td>
                        <div class="product-cell">
                            @php
                                $thumb = null;
                                if ($rev->product) {
                                    $thumb = $rev->product->thumbnail ?? null;
                                    if (!$thumb && $rev->product->images) {
                                        $imgs = is_array($rev->product->images) ? $rev->product->images : json_decode($rev->product->images, true);
                                        $thumb = $imgs[0] ?? null;
                                    }
                                }
                            @endphp
                            @if($thumb)
                                <img src="{{ asset($thumb) }}" class="product-thumb" alt="">
                            @else
                                <div class="product-thumb d-flex align-items-center justify-content-center bg-light">
                                    <i class="fa-solid fa-box text-muted" style="font-size:14px;"></i>
                                </div>
                            @endif
                            <div>
                                <div class="product-name">{{ Str::limit($rev->product->name ?? 'N/A', 28) }}</div>
                                <div class="product-cat">{{ $rev->product->category->name ?? '' }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- USER --}}
                    <td>
                        <span class="fw-600">{{ $rev->user_name ?? 'Guest' }}</span>
                        @if($rev->is_verified_purchase)
                            <br><small class="text-success" style="font-size:11px;"><i class="fa-solid fa-circle-check"></i> Verified</small>
                        @endif
                    </td>

                    {{-- RATING --}}
                    <td>
                        @php
                            $r = $rev->rating;
                            $cls = $r >= 4 ? 'good' : ($r <= 2 ? 'bad' : 'mid');
                        @endphp
                        <span class="rating-pill {{ $cls }}">
                            <i class="fa-solid fa-star" style="font-size:10px;"></i> {{ $r }}/5
                        </span>
                    </td>

                    {{-- REVIEW --}}
                    <td>
                        @if($rev->comment)
                            <a href="javascript:void(0)"
                               class="text-primary fw-600 view-comment"
                               data-comment="{{ htmlspecialchars($rev->comment) }}"
                               style="font-size:13px; text-decoration:none;">
                                <i class="fa-regular fa-eye me-1"></i>View
                            </a>
                        @else
                            <span class="text-muted" style="font-size:12px;">—</span>
                        @endif
                    </td>

                    {{-- DATE --}}
                    <td style="white-space:nowrap;">{{ $rev->created_at->format('d M Y') }}</td>

                    {{-- STATUS --}}
                  

                    {{-- REPLY --}}
                    <td>
                        @if($rev->admin_reply)
                            <span class="text-success fw-600" style="font-size:12px;"><i class="fa-solid fa-check-circle me-1"></i>Replied</span>
                        @else
                            <button class="btn-reply" onclick="toggleReplyBox({{ $rev->id }})">Reply</button>
                        @endif
                    </td>
                </tr>

                {{-- REPLY ROW --}}
                <tr class="reply-row" id="reply-row-{{ $rev->id }}" style="display:none;">
                    <td colspan="8">
                        <div class="reply-box-inner">
                            <textarea id="reply-text-{{ $rev->id }}" placeholder="Type your reply here..."></textarea>
                            <button class="btn-submit-reply" onclick="submitReply({{ $rev->id }})">Send Reply</button>
                        </div>
                    </td>
                </tr>

                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty-reviews">
                            <i class="fa-regular fa-star"></i>
                            <h5>No Reviews Found</h5>
                            <p>When customers review your products, they'll appear here.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- PAGINATION --}}
        @if($reviews->hasPages())
        <div class="review-pagination">
            <span class="text-muted" style="font-size:13px;">
                Showing {{ $reviews->firstItem() }}–{{ $reviews->lastItem() }} of {{ $reviews->total() }} reviews
            </span>
            <div>
                {{ $reviews->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        </div>
        @endif
    </div>

</div>

{{-- COMMENT MODAL --}}
<div class="comment-modal-overlay" id="commentModal">
    <div class="comment-modal-box">
        <button class="comment-modal-close" onclick="closeCommentModal()">&times;</button>
        <h6 class="fw-700 mb-3" style="color:#0f172a;">Customer Review</h6>
        <p id="commentModalText" style="color:#334155; line-height:1.7; font-size:14px;"></p>
    </div>
</div>

<script>
// View comment modal
document.querySelectorAll('.view-comment').forEach(el => {
    el.addEventListener('click', function () {
        document.getElementById('commentModalText').textContent = this.dataset.comment;
        document.getElementById('commentModal').classList.add('show');
    });
});
function closeCommentModal() {
    document.getElementById('commentModal').classList.remove('show');
}
document.getElementById('commentModal').addEventListener('click', function(e){
    if(e.target === this) closeCommentModal();
});

// Toggle reply box
function toggleReplyBox(id) {
    const row = document.getElementById('reply-row-' + id);
    if (!row) return;
    const showing = row.style.display !== 'none';
    row.style.display = showing ? 'none' : 'table-row';
    if (!showing) {
        const ta = document.getElementById('reply-text-' + id);
        if (ta) ta.focus();
    }
}

// Submit reply (AJAX)
function submitReply(id) {
    const text = document.getElementById('reply-text-' + id).value.trim();
    if (!text) { alert('Please enter a reply.'); return; }

    fetch('/seller/products/review/' + id + '/reply', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({ reply: text })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            const btn = document.querySelector(`[onclick="toggleReplyBox(${id})"]`);
            if (btn) btn.outerHTML = '<span class="text-success fw-600" style="font-size:12px;"><i class="fa-solid fa-check-circle me-1"></i>Replied</span>';
            document.getElementById('reply-row-' + id).style.display = 'none';
        } else {
            alert(data.message || 'Failed to submit reply.');
        }
    })
    .catch(() => alert('Network error, please try again.'));
}
</script>

@endsection