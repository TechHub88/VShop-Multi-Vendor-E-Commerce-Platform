@extends('vshop.seller.main')
@section('content')

<style>
.card{
    border-radius:16px;
    border:none;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}
.badge{ font-size:11px; }
.filter-box .form-control,
.filter-box .form-select{ height:42px; }
.table th{ font-weight:600; color:#6b7280; }
.remove-btn {background: #61b3e5 !important;color: #fff;margin-left: 5px;}
.remove-btn:hover {background: #61b3e5 !important;color: #fff;margin-left: 5px;}
.table th {
    font-weight: 600;
    color: #fff;
    background: #004392 !important;
    border: 1px solid #fff;
}
.table tbody td {
    border: 1px solid #dee2e6;
}
.table-btn-sec a,
.table-btn-sec button {
    border: 1px solid #dee2e6 !important;
    font-size: 14px;
    padding: 7px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
}
.table-btn-sec a i,
.table-btn-sec button i{
    font-size: 12px;
}
.table-btn-sec {
    display: flex;
    align-items: center;
}
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
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
.card {
    border: 1px solid rgb(201 208 218) !important; 
    overflow: hidden;
}
.btn-dark {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0;
    padding: 8px 0;
}
.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.bg-warning {
    background: #fff !important;
    border: 1px solid #ffc107;
    padding: 6px 5px;
}
</style>

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec  mb-3">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">Customer List</h5>
</div>

{{-- SUCCESS MESSAGE --}}
@if(session('success'))

<div id="successAlert"
     class="alert alert-success alert-dismissible fade show mb-3">

    <strong>
        Success!
    </strong>

    {{ session('success') }}

    <button type="button"
            class="btn-close"
            data-bs-dismiss="alert">
    </button>

</div>

<script>

setTimeout(function(){

    let alertBox = document.getElementById('successAlert');

    if(alertBox){

        alertBox.classList.remove('show');

        alertBox.classList.add('hide');

        setTimeout(function(){

            alertBox.remove();

        }, 500);

    }

}, 3000); // 3 seconds

</script>

@endif

<form method="GET" class="card p-3 mb-3 filter-box">
<div class="row g-2">

{{-- SEARCH --}}
<div class="col-md-3">
<span class="fw-bold d-block mb-1">Search</span>
<input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Name / Email / Mobile">
</div>

{{-- CATEGORY --}}
<div class="col-md-2">
<span class="fw-bold d-block mb-1">Category</span>
<select name="category" id="category" class="form-select">
<option value="">Select Category</option>
@foreach($categories as $cat)
<option value="{{ $cat->id }}" {{ request('category')==$cat->id?'selected':'' }}>
{{ $cat->name }}
</option>
@endforeach
</select>
</div>

{{-- SUBCATEGORY --}}
<div class="col-md-2">

    <span class="fw-bold d-block mb-1">
        Sub Category
    </span>

    <select name="subcategory"
            id="subcategory"
            class="form-select">

        <option value="">
            Select Sub Category
        </option>

    </select>

</div>

{{-- TYPE --}}
<div class="col-md-2">
<span class="fw-bold d-block mb-1">Type</span>
<select name="type" class="form-select">
<option value="">Select Type</option>
<option value="product" {{ request('type')=='product'?'selected':'' }}>Product</option>
<option value="service" {{ request('type')=='service'?'selected':'' }}>Service</option>
</select>
</div>


{{-- LOCATION --}}
<div class="col-md-2">
<span class="fw-bold d-block mb-1">Location</span>
<select name="location" class="form-select">
<option value="">Select Location</option>
@foreach($locations as $loc)
<option value="{{ $loc }}" {{ request('location')==$loc?'selected':'' }}>
{{ $loc }}
</option>
@endforeach
</select>
</div>

{{-- STATUS --}}
<div class="col-md-2">
<span class="fw-bold d-block mb-1">Status</span>
<select name="status" class="form-select">
<option value="">Select Status</option>
<option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
<option value="risk" {{ request('status')=='risk'?'selected':'' }}>Risk</option>
<option value="blocked" {{ request('status')=='blocked'?'selected':'' }}>Blocked</option>
</select>
</div>

<div class="col-md-2">
    <span class="fw-bold d-block mb-1">Account</span>
    <select name="account_status" class="form-select">
        <option value="">All</option>
        <option value="active" {{ request('account_status')=='active'?'selected':'' }}>Unblocked</option>
        <option value="blocked" {{ request('account_status')=='blocked'?'selected':'' }}>Blocked</option>
    </select>
</div>

{{-- SORT --}}
<div class="col-md-3">
<span class="fw-bold d-block mb-1">Sort</span>
<select name="sort" class="form-select">
<option value="">Sort By</option>
<option value="most_purchase" {{ request('sort')=='most_purchase'?'selected':'' }}>Most Purchased</option>
<option value="most_review" {{ request('sort')=='most_review'?'selected':'' }}>Most Review</option>
<option value="new" {{ request('sort')=='new'?'selected':'' }}>New Customer</option>
<option value="old" {{ request('sort')=='old'?'selected':'' }}>Old Customer</option>
<option value="returning" {{ request('sort')=='returning'?'selected':'' }}>Returning Customer</option>
<option value="bad_review" {{ request('sort')=='bad_review'?'selected':'' }}>Bad Review</option>
<option value="open" {{ request('sort')=='open'?'selected':'' }}>Open Issues</option>
</select>
</div>

<div class="col-md-2">
    <span class="fw-bold d-block mb-1">Source</span>
    <select name="source" class="form-select">
        <option value="">All</option>
        <option value="manual" {{ request('source')=='manual'?'selected':'' }}>Manual</option>
        <option value="automatic" {{ request('source')=='automatic'?'selected':'' }}>Automatic</option>
    </select>
</div>

{{-- BUTTON --}}
<div class="col-md-2 d-flex align-items-end">
<button class="btn btn-dark w-100">Apply</button>
<button class="btn remove-btn w-100">Reset</button>
</div>

</div>
</form>

{{-- ================= TABLE ================= --}}
<div class="card">
<div class="table-responsive">

<table class="table align-middle mb-0">

<thead class="table-light">
<tr>
<th>#</th>
<th>Customer</th>
<th>Purchase</th>
<th>Reviews</th>
<th>Grievance</th>
<th>Communication Log</th>
<th>Enquiry History</th>
<th>Interested Services</th>
<th>Followup Notes</th>
<th>Source</th>
<th>Action</th>
</tr>
</thead>

<tbody>

@foreach($customers as $key => $c)
<tr>

<td>{{ $key+1 }}</td>

<td>
<a href="{{ route('seller.customer.show',$c->id) }}" class="text-decoration-none text-dark">
    <b class="text-primary">{{ $c->name }}</b>
</a><br>

<small>{{ $c->email }}</small><br>
<small>{{ $c->phone }}</small>
</td>

<td>
<span class="badge bg-primary">Orders: {{ $c->orders_count }}</span><br><br>
<span class="badge bg-success">₹ {{ number_format($c->total_spent,2) }}</span>
</td>

<td>
<span class="badge bg-warning text-dark">⭐ {{ $c->avg_rating }}</span><br><br>
<span class="badge bg-info">Reviews: {{ $c->reviews_count }}</span>
</td>

<td>
<span class="badge bg-danger">{{ $c->complaints_count }} Complaints</span><br><br>
@if($c->open_complaints > 0)
<span class="badge bg-warning">Open: {{ $c->open_complaints }}</span>
@else
<span class="badge bg-success">All Resolved</span>
@endif
</td>

<td>

<div class="d-flex gap-1 flex-nowrap align-items-center">

    <button class="btn btn-primary"
        style="font-size: 10px; padding: 4px 8px; white-space: nowrap; font-weight: 600;"
        onclick="openAddLog({{ $c->id }})">
        Add Log
    </button>

    <button class="btn btn-outline-dark"
        style="font-size: 10px; padding: 4px 8px; white-space: nowrap; font-weight: 600;"
        onclick="viewLogs({{ $c->id }})">
        View
    </button>

</div>

</td>

<!-- Enquiry History Column -->
<td>
  <span class="badge bg-secondary">Total: {{ $c->enquiries->count() }}</span>
  @if($c->enquiries->count() > 0)
    <br>
    <a href="{{ route('seller.enquiry.list', ['search' => $c->email]) }}" class="badge bg-info text-decoration-none mt-1">
      <i class="fa-solid fa-eye me-1"></i>View Enquiries
    </a>
  @endif
</td>

<!-- Interested Services Column -->
<td>
  @php
    $services = $c->enquiries->pluck('product_service')->filter()->unique();
  @endphp
  @if($services->isNotEmpty())
    <div class="d-flex flex-wrap gap-1">
      @foreach($services as $service)
        <span class="badge bg-light text-dark border mb-1" style="font-size: 10px; font-weight: 500;">
          {{ $service }}
        </span>
      @endforeach
    </div>
  @else
    <span class="text-muted small">-</span>
  @endif
</td>

<!-- Followup Notes Column -->
<td>
  @php
    $latestEnquiryWithFollowup = $c->enquiries->first(function($enq) {
        return !empty($enq->followup_note) || !empty($enq->next_followup_date);
    });
  @endphp
  @if($latestEnquiryWithFollowup)
    @if($latestEnquiryWithFollowup->followup_note)
      <div class="small text-truncate" style="max-width: 150px;" title="{{ $latestEnquiryWithFollowup->followup_note }}">
        {{ $latestEnquiryWithFollowup->followup_note }}
      </div>
    @endif
    @if($latestEnquiryWithFollowup->next_followup_date)
      <small class="text-muted d-block mt-1" style="font-size: 9px; font-weight: 500;">
        <i class="fa-solid fa-calendar-day me-1"></i>Next: {{ $latestEnquiryWithFollowup->next_followup_date }} {{ $latestEnquiryWithFollowup->next_followup_time }}
      </small>
    @endif
  @else
    <span class="text-muted small">-</span>
  @endif
</td>

<td>
    @if($c->source == 'manual')
        <span class="badge bg-primary">Manual</span>
    @else
        <span class="badge bg-success">Automatic</span>
    @endif
</td>

<td>
<div class="table-btn-sec">

{{-- VIEW --}}
<a href="{{ route('seller.customer.show',$c->id) }}" 
   class="text-primary me-2" title="View">
    <i class="fas fa-eye"></i>
</a>

{{-- EDIT --}}
<a href="{{ route('seller.customer.edit',$c->id) }}" 
   class="text-warning me-2" title="Edit">
    <i class="fas fa-edit"></i>
</a>

{{-- BLOCK / UNBLOCK --}}
@if($c->is_blocked)
<a href="{{ route('seller.customer.unblock',$c->id) }}" 
   class="text-success me-2" title="Unblock">
    <i class="fas fa-unlock"></i>
</a>
@else
<a href="{{ route('seller.customer.block',$c->id) }}" 
   class="text-danger me-2" title="Block">
    <i class="fas fa-ban"></i>
</a>
@endif

{{-- DELETE --}}
<form action="{{ route('seller.customer.delete',$c->id) }}" 
      method="POST" 
      style="display:inline;"
      class="deleteCustomerForm">
    @csrf 
    @method('DELETE')

    <button type="button"
            class="border-0 bg-transparent text-danger btnDeleteCustomer"
            title="Delete">
        <i class="fas fa-trash"></i>
    </button>
</form>

</div>
</td>

</tr>
@endforeach

</tbody>
</table>

</div>
</div>

<div class="mt-3">{{ $customers->links('pagination::bootstrap-5') }}</div>

</div>

{{-- ================= ADD LOG MODAL ONLY ================= --}}
<div class="modal fade" id="addLogModal">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5>Add Communication Log</h5>

<button class="btn-close"
data-bs-dismiss="modal">
</button>

</div>

<div class="modal-body">

<form id="logForm">

<input type="hidden" id="customer_id">

{{-- DATE --}}
<div class="mb-3">

<label class="fw-bold">
Date / Time
<span class="text-danger">*</span>
</label>

<input type="datetime-local"
class="form-control"
id="log_date">

<small class="text-danger error-msg"></small>

</div>

{{-- CHANNEL --}}
<div class="mb-3">

<label class="fw-bold">
Channel
<span class="text-danger">*</span>
</label>

<select class="form-control"
id="log_channel">

<option value="">
Select Channel
</option>

<option value="Call">
Call
</option>

<option value="WhatsApp">
WhatsApp
</option>

<option value="Email">
Email
</option>

</select>

<small class="text-danger error-msg"></small>

</div>

{{-- TYPE --}}
<div class="mb-3">

<label class="fw-bold">
Type
<span class="text-danger">*</span>
</label>

<select class="form-control"
id="log_type">

<option value="">
Select Type
</option>

<option value="Follow-up">
Follow-up
</option>

<option value="Demo">
Demo
</option>

<option value="Support">
Support
</option>

</select>

<small class="text-danger error-msg"></small>

</div>

{{-- NOTES --}}
<div class="mb-3">

<label class="fw-bold">
Notes
<span class="text-danger">*</span>
</label>

<textarea class="form-control"
id="log_notes"
rows="3"></textarea>

<small class="text-danger error-msg"></small>

</div>

{{-- HANDLED BY --}}
<div class="mb-3">

<label class="fw-bold">
Handled By
<span class="text-danger">*</span>
</label>

<input type="text"
class="form-control"
id="log_user">

<small class="text-danger error-msg"></small>

</div>

<button type="button"
class="btn btn-primary w-100"
onclick="saveLog()">

Save

</button>

</form>

</div>
</div>
</div>
</div>

<div class="modal fade" id="logModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="fw-bold">Communication Logs</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" id="logContent">
                Loading...
            </div>

        </div>
    </div>
</div>


{{-- SCRIPT --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

$('#category').on('change', function(){

    let id = $(this).val();
    let selectedSub = "{{ request('subcategory') }}";

    if(!id){
        $('#subcategory').html('<option value="">Select Sub Category</option>');
        return;
    }

    $.get("{{ url('seller/customer/get-subcategories') }}/"+id, function(res){

        let html = '<option value="">Select Sub Category</option>';
        let found = false;

        res.forEach(function(item){

            let selected = '';

            if(String(item.id) === String(selectedSub)){
                selected = 'selected';
                found = true;
            }

            html += `<option value="${item.id}" ${selected}>${item.name}</option>`;
        });

        // 🔥 IF NOT FOUND → ADD IT MANUALLY
        if(selectedSub && !found){
            html += `<option value="${selectedSub}" selected>(Old / Invalid)</option>`;
        }

        $('#subcategory').html(html);

    });

});

$(document).ready(function(){

    function loadSubcategories(categoryId, selectedSub = ''){

        if(!categoryId){

            $('#subcategory').html(
                '<option value="">Select Sub Category</option>'
            );

            return;
        }

        $.get(
            "{{ url('seller/customer/get-subcategories') }}/" + categoryId,
            function(res){

                let html = '<option value="">Select Sub Category</option>';

                res.forEach(function(item){

                    let selected = '';

                    if(String(item.id) === String(selectedSub)){

                        selected = 'selected';

                    }

                    html += `
                        <option value="${item.id}" ${selected}>
                            ${item.name}
                        </option>
                    `;
                });

                $('#subcategory').html(html);

            }
        );
    }

    // CATEGORY CHANGE
    $('#category').on('change', function(){

        let categoryId = $(this).val();

        loadSubcategories(categoryId);

    });

    // PAGE LOAD SELECTED VALUE
    let selectedCategory = $('#category').val();

    let selectedSub = "{{ request('subcategory') }}";

    if(selectedCategory){

        loadSubcategories(selectedCategory, selectedSub);

    }

});
// ================= LOG MODAL =================
function openAddLog(id){
    $('#customer_id').val(id);
    $('#addLogModal').modal('show');
}




// ================= REMOVE ERROR =================
$(document).on(
'keyup change',
'#logForm input, #logForm select, #logForm textarea',
function(){

    $(this).removeClass('is-invalid');

    $(this)
    .closest('.mb-3')
    .find('.error-msg')
    .text('');

});
function saveLog(){

    let isValid = true;

    // CLEAR OLD ERRORS
    $('.error-msg').text('');

    $('#logForm .form-control')
    .removeClass('is-invalid');

    // ================= DATE =================
    if(!$('#log_date').val()){

        $('#log_date')
        .addClass('is-invalid');

        $('#log_date')
        .closest('.mb-3')
        .find('.error-msg')
        .text('Date / Time is required');

        isValid = false;
    }

    // ================= CHANNEL =================
    if(!$('#log_channel').val()){

        $('#log_channel')
        .addClass('is-invalid');

        $('#log_channel')
        .closest('.mb-3')
        .find('.error-msg')
        .text('Channel is required');

        isValid = false;
    }

    // ================= TYPE =================
    if(!$('#log_type').val()){

        $('#log_type')
        .addClass('is-invalid');

        $('#log_type')
        .closest('.mb-3')
        .find('.error-msg')
        .text('Type is required');

        isValid = false;
    }

    // ================= NOTES =================
    if(!$('#log_notes').val().trim()){

        $('#log_notes')
        .addClass('is-invalid');

        $('#log_notes')
        .closest('.mb-3')
        .find('.error-msg')
        .text('Notes is required');

        isValid = false;
    }

    // ================= HANDLED BY =================
    if(!$('#log_user').val().trim()){

        $('#log_user')
        .addClass('is-invalid');

        $('#log_user')
        .closest('.mb-3')
        .find('.error-msg')
        .text('Handled By is required');

        isValid = false;
    }

    // STOP
    if(!isValid){
        return;
    }

    // ================= AJAX =================
    $.ajax({

        url: "{{ url('seller/customer/save-log') }}",

        method: "POST",

        data: {

            _token: "{{ csrf_token() }}",

            customer_id: $('#customer_id').val(),

            log_date: $('#log_date').val(),

            channel: $('#log_channel').val(),

            type: $('#log_type').val(),

            notes: $('#log_notes').val(),

            handled_by: $('#log_user').val()

        },

        success: function(res){

            if(res.status){

                alert(res.msg);

                $('#addLogModal').modal('hide');

                $('#logForm')[0].reset();

            }else{

                alert(res.msg);

            }

        }

    });

}
</script>

<script>
function viewLogs(customerId){

    let el = document.getElementById('logContent');
    el.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <div class="mt-2 text-muted small">Loading logs...</div>
        </div>
    `;

    let myModal = new bootstrap.Modal(document.getElementById('logModal'));
    myModal.show();

    fetch('/seller/customer/logs/' + customerId)
        .then(async res => {

            if (!res.ok) {
                throw new Error('HTTP Error ' + res.status);
            }

            return await res.json();
        })
        .then(data => {

            if (!Array.isArray(data)) {
                throw new Error('Invalid response format');
            }

            let html = '';

            if (data.length === 0) {
                html = `
                    <div class="text-center py-4 text-muted">
                        <i class="far fa-comments fa-2x mb-2 d-block text-secondary"></i>
                        No communication logs found for this customer.
                    </div>
                `;
            } else {

                data.forEach(log => {

                    html += `
                        <div class="border rounded p-3 mb-2 bg-light shadow-sm">

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <small class="text-muted fw-bold">
                                    <i class="far fa-clock me-1"></i>${log.date ?? log.created_at ?? '-'}
                                </small>

                                <span class="badge bg-primary">
                                    ${log.channel ?? 'N/A'}
                                </span>
                            </div>

                            <div class="mt-1">
                                <span class="badge bg-secondary mb-2">${log.type ?? '-'}</span>
                            </div>

                            <div class="text-dark small mt-1 p-2 bg-white rounded border">
                                ${log.notes ?? ''}
                            </div>

                            <div class="text-end mt-2">
                                <small class="text-secondary fw-bold">
                                    <i class="far fa-user me-1"></i>Handled By: ${log.handled_by ?? '-'}
                                </small>
                            </div>

                        </div>
                    `;
                });
            }

            el.innerHTML = html;

        })
        .catch(err => {

            console.error(err);

            el.innerHTML = `
                <div class="alert alert-danger m-0">
                    <i class="fas fa-exclamation-triangle me-2"></i>Logs load nahi ho raha. Please check route or server response.
                </div>
            `;
        });
}
</script>

<script>

$(document).on('click', '.btnDeleteCustomer', function(e){

    e.preventDefault();

    let form = $(this).closest('form');

    Swal.fire({
        title: 'Are you sure?',
        text: "Do you really want to delete this customer?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if(result.isConfirmed){

            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            form.submit();
        }

    });

});

</script>
@endsection