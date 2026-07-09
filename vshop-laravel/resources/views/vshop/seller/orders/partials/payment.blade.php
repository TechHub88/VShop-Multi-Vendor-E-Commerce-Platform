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
.btn-approve-wallet {
    background: #10b981 !important;
    color: #fff !important;
    border: none !important;
    transition: all 0.2s ease;
    border-radius: 4px;
}
.btn-approve-wallet:hover {
    background: #059669 !important;
    transform: translateY(-1px);
}
.btn-approve-bank {
    background: #3b82f6 !important;
    color: #fff !important;
    border: none !important;
    transition: all 0.2s ease;
    border-radius: 4px;
}
.btn-approve-bank:hover {
    background: #2563eb !important;
    transform: translateY(-1px);
}
.btn-reject {
    background: #ef4444 !important;
    color: #fff !important;
    border: none !important;
    transition: all 0.2s ease;
    border-radius: 4px;
}
.btn-reject:hover {
    background: #dc2626 !important;
    transform: translateY(-1px);
}
.btn-select {
    background: #6b7280 !important;
    color: #fff !important;
    border: none !important;
    transition: all 0.2s ease;
    border-radius: 4px;
}
.btn-select:hover {
    background: #4b5563 !important;
    transform: translateY(-1px);
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

    <!-- ================= RETURN WORKFLOW ================= -->
    <div class="col-md-12" id="return-workflow">

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

{{-- ================= ACTION MODAL ================= --}}
<div class="modal fade" id="returnActionModal" tabindex="-1">
<div class="modal-dialog modal-dialog-centered modal-sm">
<div class="modal-content border-0 shadow" style="border-radius: 12px;">

<div class="modal-body text-center p-4">
<button type="button" class="btn-close position-absolute" style="top: 15px; right: 15px;" data-bs-dismiss="modal"></button>

<div class="mb-3" style="color: #6366f1;">
    <i class="fa-solid fa-circle-question fa-3x"></i>
</div>

<h5 class="fw-bold mb-2">Process Return</h5>

<input type="hidden" id="modalReturnId">
<input type="hidden" id="modalOrderId">

<p class="small text-muted mb-4">Please choose how you want to process this return request.</p>

<div class="d-flex flex-column gap-2" id="modalActionButtons">
    <!-- Buttons will be dynamically inserted here based on status -->
</div>

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

if (data.returnRequests && data.returnRequests.length > 0) {
    data.returnRequests.forEach(rr=>{
        let statusBadge = '';
        if(rr.status === 'requested') statusBadge = '<span class="badge bg-warning">Requested</span>';
        else if(rr.status === 'pickup_initiated') statusBadge = '<span class="badge bg-primary">Pickup Initiated</span>';
        else if(rr.status === 'approved') statusBadge = '<span class="badge bg-info">Approved</span>';
        else if(rr.status === 'rejected') statusBadge = '<span class="badge bg-danger">Rejected</span>';
        else if(rr.status === 'refunded') statusBadge = '<span class="badge bg-success">Refunded</span>';

        let actionButtons = '';
        if(rr.status === 'requested') {
            actionButtons = `
                <div class="d-flex flex-column gap-1">
                    <span class="small fw-bold">Pickup Initiated?</span>
                    <div class="d-flex gap-1">
                        <button class="btn btn-success btn-sm py-0 px-2" onclick="initiatePickup(${rr.id})">Yes</button>
                        <button class="btn btn-danger btn-sm py-0 px-2" onclick="cancelPickupAndRefund(${rr.id})">No</button>
                    </div>
                </div>
            `;
        } else if(rr.status === 'pickup_initiated') {
            actionButtons = `
                <div class="d-flex flex-column gap-1">
                    <span class="small fw-bold">Pickup Success?</span>
                    <div class="d-flex gap-1">
                        <button class="btn btn-success btn-sm py-0 px-2" onclick="approveReturn(${rr.id})">Yes</button>
                        <button class="btn btn-danger btn-sm py-0 px-2" onclick="openActionModal(${rr.id}, ${rr.order_id}, 'requested')">No</button>
                    </div>
                </div>
            `;
        } else if(rr.status === 'approved') {
            actionButtons = `
                <span class="badge bg-secondary p-2"><i class="fa-solid fa-clock"></i> Waiting for Admin Refund</span>
            `;
        } else {
            actionButtons = `<span class="text-muted small"><i class="fa-solid fa-circle-info"></i> Processed</span>`;
        }

        let attachmentHtml = '';
        if (rr.attachment) {
            attachmentHtml = `<br><a href="/public/${rr.attachment}" target="_blank" class="text-primary small"><i class="fa-solid fa-paperclip"></i> View Image</a>`;
        }

        wf += `
        <tr>
            <td>
                <strong>#${rr.order_id}</strong>
                <div class="small text-muted">${rr.customer_name ?? ''}</div>
            </td>
            <td>
                <div><b>Reason:</b> ${rr.reason}</div>
                ${rr.note ? `<div class="small text-muted"><b>Note:</b> ${rr.note}</div>` : ''}
                ${attachmentHtml}
            </td>
            <td>${formatDate(rr.created_at)}</td>
            <td>
                <div class="mb-1">${statusBadge}</div>
                <div class="d-flex gap-1 flex-wrap align-items-center">${actionButtons}</div>
            </td>
        </tr>
        `;
    });
}

// EMPTY STATE
if(wf == ''){
    wf = `
    <tr>
    <td colspan="4" class="text-center text-muted">
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
function initiatePickup(id){
    $.post('/seller/orders/return/initiate-pickup',{
        id: id,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function(res){
        if(res.status) {
            loadData($('.payment-tab.active').data('type'));
        } else {
            alert(res.msg || 'Error initiating pickup');
        }
    }).fail(function(err) {
        alert('Server error: ' + err.statusText);
    });
}

function cancelPickupAndRefund(id){
    if(confirm('Are you sure you want to cancel the pickup and immediately refund the buyer from your wallet?')){
        $.post('/seller/orders/return/cancel-pickup-refund',{
            id: id,
            _token: $('meta[name="csrf-token"]').attr('content')
        }, function(res){
            if(res.status) {
                loadData($('.payment-tab.active').data('type'));
            } else {
                alert(res.msg || 'Error cancelling pickup');
            }
        }).fail(function(err) {
            alert('Server error: ' + err.statusText);
        });
    }
}

function approveReturn(id, refundMode){
    $.post('/seller/orders/return/approve',{
        id: id,
        refund_mode: refundMode || 'wallet',
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function(res){
        if(res.status) {
            loadData($('.payment-tab.active').data('type'));
        } else {
            alert(res.msg || 'Error approving return');
        }
    }).fail(function(err) {
        alert('Server error: ' + err.statusText);
    });
}

function rejectReturn(id){
    $.post('/seller/orders/return/reject',{
        id: id,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function(){
        loadData($('.payment-tab.active').data('type'));
    });
}

function verifyReturn(id){
    $.post('/seller/orders/return/verify',{
        id: id,
        _token: $('meta[name="csrf-token"]').attr('content')
    }, function(){
        loadData($('.payment-tab.active').data('type'));
    });
}

// Legacy compatibility
function approve(id){ approveReturn(id, 'wallet'); }
function reject(id){ rejectReturn(id); }
function verify(id){ verifyReturn(id); }

// ================= ACTION MODAL =================
function openActionModal(returnId, orderId, status) {
    $('#modalReturnId').val(returnId);
    $('#modalOrderId').val(orderId);
    
    let buttonsHtml = '';
    
    if (status === 'requested') {
        buttonsHtml = `
            <button class="btn btn-success btn-sm w-100 mb-2" onclick="modalApproveReturn()"><i class="fa-solid fa-check"></i> Approve Return</button>
            <button class="btn btn-danger btn-sm w-100" onclick="modalRejectReturn()"><i class="fa-solid fa-xmark"></i> Reject Return</button>
        `;
    }
    
    $('#modalActionButtons').html(buttonsHtml);
    $('#returnActionModal').modal('show');
}

function modalApproveReturn() {
    let returnId = $('#modalReturnId').val();
    approveReturn(returnId);
    $('#returnActionModal').modal('hide');
}

function modalRejectReturn() {
    let returnId = $('#modalReturnId').val();
    rejectReturn(returnId);
    $('#returnActionModal').modal('hide');
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


// ================= INIT =================
loadData();

</script>

@endsection
