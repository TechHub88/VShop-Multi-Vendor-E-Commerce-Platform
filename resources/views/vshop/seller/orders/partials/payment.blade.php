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
}
.table th {
    font-weight: 600;
    color: #fff !important;
    background: #004392 !important;
    border: 1px solid #fff;
}
.table tbody td {
    border: 1px solid #dee2e6;
}
.btn-sm {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%) !important;
    color: #fff !important;
    border: 0 !important;
    padding: 8px 6px;
}
</style>

@section('content')

<div class="container-fluid py-4">
<div class="d-flex top-back-heading-sec mb-3">
    <button onclick="goBack()" class="nav-btn">
        <i class="fa-solid fa-arrow-left"></i>
    </button>
    <h5 class="fw-bold">Payment Handling</h5>
</div>

{{-- ================= TOP TOGGLE BAR ================= --}}
<div class="card mb-4 p-2">

    <div class="payment-bar d-flex">

        <div class="payment-tab active w-50 text-center" data-type="full">
            Full Payment (Advance / Full)
        </div>

        <div class="payment-tab w-50 text-center" data-type="partial">
            <div>Partial Payment</div>
            <small>Allowed only for v-Travel / v-Campus</small>
        </div>

    </div>

</div>

{{-- ================= TRANSACTION ================= --}}
<div class="card mb-4">
<div class="card-header">Transaction List & Payment Status</div>

<div class="table-responsive">
<table class="table align-middle table-bordered">
<thead class="table-light">
<tr>
<th>ID</th>
<th>Customer</th>
<th>Date</th>
<th>Total</th>
<th>Paid</th>
<th>Balance</th>
<th>Status</th>
<th>Reminder</th>
<th>Actions</th>
</tr>
</thead>

<tbody id="transactionBody"></tbody>

</table>
</div>
</div>

{{-- ================= REFUND ================= --}}
<div class="card p-3 mb-4">

<h6>Return / Refund Stages</h6>

<br>

<!-- ================= DYNAMIC STAGES ================= -->
<div class="refund-steps-full" id="refundStages">

    <div class="step" data-step="initiated">
        1. Initiated
    </div>

    <div class="arrow">→</div>

    <div class="step" data-step="processing">
        2. Processing
    </div>

    <div class="arrow">→</div>

    <div class="step success" data-step="completed">
        3. Completed ✔
    </div>

    <div class="arrow">→</div>

    <div class="step failed" data-step="failed">
        4. Failed ✖
    </div>

</div>

<br>

<div class="row">

    <!-- ================= LEFT ================= -->
    <div class="col-md-6">

        <h6>Return Workflow</h6>

        <table class="table">
            <thead>
                <tr>
                    <th>Order</th>
                    <th>Reason</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="workflowBody"></tbody>

        </table>

    </div>

    <!-- ================= RIGHT ================= -->
    <div class="col-md-6">

        <h6>Refund Request</h6>

        <!-- SELECTED ORDER -->
        <div class="alert alert-info py-2 mb-2">
            Selected Order :
            <strong id="selectedRefundOrder">None</strong>
        </div>

        <input type="hidden" id="refundOrderId">

        <textarea
            id="refundReason"
            class="form-control mb-2"
            placeholder="Enter refund reason"
            rows="3"></textarea>

        <button class="btn btn-warning btn-sm"
                onclick="submitRefundReason()">
            Submit Reason
        </button>

        <small id="refundMsg"
               class="text-success d-block mt-2"></small>

    </div>

</div>

<!-- ================= SLA ================= -->
<hr>

<h6>SLA Tracker</h6>

<div class="progress">
    <div id="slaBar"
         class="progress-bar bg-warning">
    </div>
</div>

<small id="slaText"></small>

</div>

<div class="card mb-4">

<div class="card-header fw-semibold">
Order & Shipping Details
</div>

<div class="table-responsive">
<table class="table align-middle">

<thead class="table-light">
<tr>
<th>Order ID</th>
<th>Product</th>
<th>Customer</th>
<th>Supplier Status</th>
<th>Supplier Tracking ID</th>
<th>Auto-Sync</th>
<th>Payment Status</th>
<th>Refund Status</th>
<th>GST</th>
<th>Invoice</th>

</tr>
</thead>

<tbody id="shippingBody"></tbody>

</table>
</div>

</div>
</div>



</div>

{{-- ================= REMINDER MODAL ================= --}}
<div class="modal fade" id="reminderModal">
<div class="modal-dialog">
<div class="modal-content">

<div class="modal-header">
<h5>Send Reminder</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="hidden" id="reminderId">
<textarea id="reminderNote" class="form-control" placeholder="Enter reminder"></textarea>
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-warning" onclick="submitReminder()">Send</button>
</div>

</div>
</div>
</div>

{{-- ================= STYLE ================= --}}
<style>

.payment-bar{
    border:1px solid #ddd;
    border-radius:8px;
    overflow:hidden;
    background:#f8f9fa;
}

.payment-tab{
    padding:12px;
    cursor:pointer;
    font-weight:500;
}

.payment-tab.active{
    background: #469cd5;
    color: #fff;
    font-weight: 600;
}

.refund-steps{
    display:flex;
    align-items:center;
    flex-wrap:wrap;
}

.step{
    padding:8px 14px;
    border-radius:6px;
    background:#e9ecef;
    font-weight:500;
}

.step.success{
    background:#d4edda;
    color:#155724;
}

.step.failed{
    background:#f8d7da;
    color:#721c24;
}

.arrow{
    margin:0 10px;
    font-weight:bold;
    color:#999;
}


.refund-steps-full{
    display:flex;
    width:100%;
    align-items:center;
}

/* STEP FULL WIDTH */
.refund-steps-full .step{
    flex:1; /* 🔥 THIS MAKES FULL WIDTH */
    text-align:center;
    padding:10px;
    border-radius:6px;
    background:#e9ecef;
    font-weight:500;
}

/* SUCCESS */
.refund-steps-full .step.success{
    background:#d4edda;
    color:#155724;
}

/* FAILED */
.refund-steps-full .step.failed{
    background:#f8d7da;
    color:#721c24;
}

/* ARROW */
.refund-steps-full .arrow{
    margin:0 8px;
    font-weight:bold;
    color:#999;
}
</style>

<script>

let currentOrderId = null;

// ================= INVOICE URL =================
let invoiceBaseUrl = "{{ route('seller.orders.invoice', ':id') }}";

// ================= FORMAT DATE =================
function formatDate(d){

    if(!d) return '-';

    return new Date(d).toLocaleString();

}

// ================= STATUS → STAGE =================
function mapStatusToStage(status, refundStatus){

    if(status === 'return_requested')
        return 'initiated';

    if(status === 'return_approved' || refundStatus === 'processing')
        return 'processing';

    if(refundStatus === 'completed')
        return 'completed';

    if(refundStatus === 'failed')
        return 'failed';

    return 'initiated';
}

// ================= REFUND STAGE UI =================
function updateRefundStages(stage){

    // RESET
    $('#refundStages .step').css({
        background:'#e9ecef',
        color:'#000'
    });

    // INITIATED
    if(stage === 'initiated'){

        $('[data-step="initiated"]').css({
            background:'#06b6d4',
            color:'#fff'
        });

    }

    // PROCESSING
    if(stage === 'processing'){

        $('[data-step="initiated"]').css({
            background:'#06b6d4',
            color:'#fff'
        });

        $('[data-step="processing"]').css({
            background:'#facc15',
            color:'#000'
        });

    }

    // COMPLETED
    if(stage === 'completed'){

        $('[data-step="initiated"]').css({
            background:'#06b6d4',
            color:'#fff'
        });

        $('[data-step="processing"]').css({
            background:'#facc15',
            color:'#000'
        });

        $('[data-step="completed"]').css({
            background:'#16a34a',
            color:'#fff'
        });

    }

    // FAILED
    if(stage === 'failed'){

        $('[data-step="initiated"]').css({
            background:'#06b6d4',
            color:'#fff'
        });

        $('[data-step="processing"]').css({
            background:'#facc15',
            color:'#000'
        });

        $('[data-step="failed"]').css({
            background:'#ef4444',
            color:'#fff'
        });

    }

}

// ================= LOAD DATA =================
function loadData(type='full'){

fetch(`?type=${type}`,{
headers:{
'X-Requested-With':'XMLHttpRequest'
}
})
.then(res=>res.json())
.then(data=>{

// =====================================================
// TRANSACTION TABLE
// =====================================================
let rows='';

data.orders.forEach(o=>{

let badge='bg-warning';

if(o.payment_status=='paid')
badge='bg-success';

if(o.payment_status=='failed')
badge='bg-danger';

if(o.payment_status=='pending')
badge='bg-secondary';

let paid =
o.payment_status === 'paid'
? o.total_amount
: 0;

let balance =
o.payment_status === 'paid'
? 0
: o.total_amount;

rows += `
<tr>

<td>#${o.id}</td>

<td>${o.customer_name ?? ''}</td>

<td>${formatDate(o.created_at)}</td>

<td>₹${o.total_amount}</td>

<td>₹${paid}</td>

<td>₹${balance}</td>

<td>
<span class="badge ${badge}">
${o.payment_status}
</span>
</td>

<td>
<span class="badge bg-secondary">
${o.reminder_count ?? 0}
</span>
</td>

<td>

<button class="btn btn-sm btn-primary"
onclick="retry(${o.id})">
Retry
</button>

<button class="btn btn-sm btn-warning"
onclick="openReminder(${o.id})">
Send Reminder
</button>

</td>

</tr>
`;

});

$('#transactionBody').html(rows);


// =====================================================
// RETURN WORKFLOW TABLE
// =====================================================
let wf='';

data.orders.forEach(o=>{

// ONLY SHOW REAL REFUND REQUESTS
if(
    o.refund_reason ||
    o.return_requested_at
){

let status='';

// REQUESTED
if(o.status == 'return_requested'){

status = `
<span class="badge bg-warning">
Requested
</span>
`;

}

// APPROVED
if(o.status == 'return_approved'){

status = `
<span class="badge bg-info">
Approved
</span>
`;

}

// PROCESSING
if(o.refund_status == 'processing'){

status = `
<span class="badge bg-primary">
Processing
</span>
`;

}

// COMPLETED
if(o.refund_status == 'completed'){

status = `
<span class="badge bg-success">
Refunded
</span>
`;

}

// FAILED
if(o.refund_status == 'failed'){

status = `
<span class="badge bg-danger">
Rejected
</span>
`;

}

wf += `
<tr>

<td>
<strong>#${o.id}</strong>
</td>

<td>
${o.refund_reason
? o.refund_reason
: '-'}
</td>

<td>
${o.return_requested_at
? formatDate(o.return_requested_at)
: '-'}
</td>

<td>

<div class="mb-1">
${status}
</div>

<div class="d-flex gap-1 flex-wrap">

<button class="btn btn-primary btn-sm"
onclick="setOrder(${o.id})">
Select
</button>

<button class="btn btn-success btn-sm"
onclick="setOrder(${o.id}); approve(${o.id})">
Approve
</button>

<button class="btn btn-danger btn-sm"
onclick="setOrder(${o.id}); reject(${o.id})">
Reject
</button>

</div>

</td>

</tr>
`;

}

});

// EMPTY STATE
if(wf == ''){

wf = `
<tr>
<td colspan="4"
class="text-center text-muted">
No refund requests found
</td>
</tr>
`;

}

$('#workflowBody').html(wf);


// =====================================================
// SHIPPING TABLE
// =====================================================
let ship='';

data.orders.forEach(o=>{

let supplierStatus =
o.supplier_status || 'Pending';

let autoSync =
o.auto_sync
? 'Enabled'
: 'Syncing';

let paymentBadge =
o.payment_status=='paid'
? 'bg-success'
: 'bg-secondary';

let refundBadge='bg-warning';

if(o.status=='completed')
refundBadge='bg-success';

if(o.status=='cancelled')
refundBadge='bg-danger';

if(o.status=='return_requested')
refundBadge='bg-warning';

if(o.status=='return_approved')
refundBadge='bg-info';

ship += `
<tr>

<td>
<b>#${o.id}</b>
</td>

<td>
${o.product_name ?? '-'}
</td>

<td>
<div style="font-weight:500">
${o.customer_name ?? ''}
</div>

<small style="color:#888">
${o.customer_email ?? ''}
</small>
</td>

<td>

<span class="badge ${
supplierStatus=='Shipped'
? 'bg-success'
: 'bg-warning'
}">
${supplierStatus}
</span>

</td>

<td>

${
o.tracking_id
? `<span class="badge bg-dark">
${o.tracking_id}
</span>`
: `<span style="color:#bbb;">-</span>`
}

</td>

<td>
<span class="badge bg-info">
${autoSync}
</span>
</td>

<td>
<span class="badge ${paymentBadge}">
${o.payment_status}
</span>
</td>

<td>
<span class="badge ${refundBadge}">
${o.status ?? '-'}
</span>
</td>

<td>

${
o.gstin
? `<span style="
background:#eef2ff;
color:#4e73df;
padding:4px 8px;
border-radius:6px;
font-size:12px;
">
${o.gstin}
</span>`
: `<span style="color:#bbb;">
N/A
</span>`
}

</td>

<td>

<a href="${invoiceBaseUrl.replace(':id', o.id)}"
class="btn btn-sm"
style="
background:linear-gradient(
135deg,
#4e73df,
#224abe
);
color:#fff;
border-radius:8px;
">
⬇ Invoice
</a>

</td>

</tr>
`;

});

$('#shippingBody').html(ship);


// =====================================================
// REFUND STAGE AUTO SYNC
// =====================================================
let selectedOrder =
data.orders.find(
o => o.id == currentOrderId
);

if(!selectedOrder)
selectedOrder = data.orders[0];

if(selectedOrder){

let stage = mapStatusToStage(
selectedOrder.status,
selectedOrder.refund_status
);

updateRefundStages(stage);

}


// =====================================================
// SLA
// =====================================================
let total = data.orders.length;

let pct = total
? 100 - Math.round(
(data.slaBreached / total) * 100
)
: 0;

$('#slaBar').css('width', pct+'%');

$('#slaText').text(
pct+'% within SLA'
);


// =====================================================
// LOGS
// =====================================================
loadLogs();

});
}


// ================= SELECT ORDER =================
function setOrder(id){

currentOrderId = id;

// HIDDEN INPUT
$('#refundOrderId').val(id);

// SELECTED ORDER TEXT
$('#selectedRefundOrder').html('#' + id);

// ROW HIGHLIGHT
$('#workflowBody tr')
.removeClass('table-warning');

$('#workflowBody tr').each(function(){

let rowText = $(this)
.find('td:first')
.text()
.trim();

if(rowText === '#' + id){

$(this).addClass('table-warning');

}

});

// FOCUS
$('#refundReason').focus();

}


// ================= REMINDER =================
function openReminder(id){

$('#reminderId').val(id);

$('#reminderNote').val('');

$('#reminderModal').modal('show');

}

function submitReminder(){

let id = $('#reminderId').val();

let note = $('#reminderNote').val();

if(!note.trim()){

alert('Enter reminder');

return;

}

$.post('/seller/orders/reminder',{

id:id,

note:note,

_token:$('meta[name="csrf-token"]').attr('content')

},function(res){

if(res.status){

$('#reminderModal').modal('hide');

loadData($('.payment-tab.active').data('type'));

}

});

}


// ================= APPROVE / REJECT =================
function approve(id){

actionCall('/seller/orders/return/approve',id);

}

function reject(id){

actionCall('/seller/orders/return/reject',id);

}

function verify(id){

actionCall('/seller/orders/return/verify',id);

}

function actionCall(url,id){

$.post(url,{

id:id,

_token:$('meta[name="csrf-token"]').attr('content')

},function(){

loadData($('.payment-tab.active').data('type'));

});

}


// ================= LOAD LOGS =================
function loadLogs(){

fetch('/orders/logs')
.then(r=>r.json())
.then(d=>{

let rows='';

d.forEach(l=>{

rows += `
<tr>

<td>#${l.order_id}</td>

<td>${l.message}</td>

<td>${formatDate(l.created_at)}</td>

</tr>
`;

});

$('#logBody').html(rows);

});

}


// ================= RETRY =================
function retry(id){

alert('Retry coming soon');

}


// ================= TOGGLE =================
$('.payment-tab').click(function(){

$('.payment-tab').removeClass('active');

$(this).addClass('active');

loadData($(this).data('type'));

});


// ================= REFUND REQUEST =================
function submitRefundReason(){

let id = $('#refundOrderId').val();

let reason = $('#refundReason').val();

if(!id){

alert('Please select order');

return;

}

if(!reason.trim()){

alert('Enter refund reason');

return;

}

$.post('/seller/orders/refund-reason',{

id:id,

reason:reason,

_token:$('meta[name="csrf-token"]').attr('content')

},function(res){

if(res.status){

$('#refundMsg')
.removeClass('text-danger')
.addClass('text-success')
.text('Reason saved successfully');

$('#refundReason').val('');

loadData($('.payment-tab.active').data('type'));

}else{

$('#refundMsg')
.removeClass('text-success')
.addClass('text-danger')
.text(res.message ?? 'Failed');

}

}).fail(function(xhr){

$('#refundMsg')
.removeClass('text-success')
.addClass('text-danger')
.text('Server error');

console.log(xhr.responseText);

});

}


// ================= INIT =================
loadData();

</script>

@endsection