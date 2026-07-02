@extends('layouts.vertical', ['title' => 'Seller Report'])

@section('content')

<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
    }
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary,
    .btn-outline-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .nav-tabs .nav-link.active {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
</style>

<div class="container-fluid pt-2 pb-2">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold">Seller Report</h4>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-white py-3">
            <h6 class="mb-0">Seller Analytics Report</h6>
        </div>

        <div class="card-body">

            <!-- SIMPLE TABLE -->
            <div class="table-responsive">
                <table class="table table-bordered table-hover">

                    <thead class="table-light">
                        <tr>
                            <th>SL</th>
                            <th>Seller Name</th>
                            <th>Mobile</th>
                            <th>Application</th>
                            <th>Business Type</th>
                            <th>GST Number</th>
                            <th>Status</th>
                            <th>Action</th>
                            <th>Verified</th>
                            <th>Created</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($sellers as $index => $seller)
                        <tr>

                            <td>{{ $index+1 }}</td>
                            <td>{{ $seller->name }}</td>
                            <td>{{ $seller->mobile }}</td>
                            <td>{{ $seller->application }}</td>
                            <td>{{ $seller->business_type }}</td>
                            <td>{{ $seller->gst_number }}</td>

                            <td>
                                @if($seller->status=='approved')
                                    <span class="badge bg-success">Approved</span>
                                @elseif($seller->status=='pending')
                                    <span class="badge bg-warning">Pending</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>

                            <td>
                               <button class="btn btn-info btn-sm viewBtn"
      data-id="{{ $seller->id }}"
    data-name="{{ $seller->name }}"
    data-email="{{ $seller->email }}"
    data-mobile="{{ $seller->mobile }}"
    data-status="{{ $seller->status }}"
    data-application="{{ $seller->application }}"
    data-legal_name="{{ $seller->legal_name }}"
    data-business="{{ $seller->business_type }}"

    data-pan="{{ $seller->pan_number }}"
    data-gst="{{ $seller->gst_number }}"
    data-iec="{{ $seller->iec_code }}"

    data-country="{{ $seller->billing_country }}"
    data-state="{{ $seller->billing_state }}"
    data-city="{{ $seller->billing_city }}"
    data-address="{{ $seller->billing_address }}"

    data-bank_name="{{ $seller->bank_account_name }}"
    data-bank_number="{{ $seller->bank_account_number }}"
    data-ifsc="{{ $seller->ifsc_code }}"
    data-swift="{{ $seller->swift_code }}"

    data-paypal="{{ $seller->paypal_email }}"

>
    View
</button>

                                @if($seller->status == 'pending')
                                    <button class="btn btn-success btn-sm approveBtn" data-id="{{ $seller->id }}">
                                        Approve
                                    </button>

                                    <button class="btn btn-danger btn-sm rejectBtn" data-id="{{ $seller->id }}">
                                        Reject
                                    </button>
                                @endif
                            </td>

                            <td>
                                @if($seller->is_verified)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($seller->created_at)->format('d M Y') }}
                            </td>

                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

        </div>
    </div>
</div>

<!-- MODAL -->
<div class="modal fade" id="sellerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">Seller Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">

                <div id="sellerDetails"></div>

                <hr>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="verifyConfirm">
                    <label class="form-check-label text-danger">
                        I confirm all details verified
                    </label>
                </div>

                <button class="btn btn-success mt-2" id="verifySellerBtn" disabled>
                    Verify Seller
                </button>

            </div>

        </div>
    </div>
</div>


<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- Bootstrap -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function(){

    // 👉 GLOBAL VARIABLE (IMPORTANT)
    let currentSellerId = null;

    // =========================
    // VIEW BUTTON CLICK
    // =========================
    $(document).on('click', '.viewBtn', function(){

        // 👉 STORE ID
        currentSellerId = $(this).data('id');

        let html = `

        <div class="container-fluid">

            <!-- BASIC -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold text-primary">Basic Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><b>Name:</b> ${$(this).data('name')}</div>
                        <div class="col-md-6"><b>Email:</b> ${$(this).data('email')}</div>

                        <div class="col-md-6 mt-2"><b>Mobile:</b> ${$(this).data('mobile')}</div>
                        <div class="col-md-6 mt-2"><b>Status:</b> ${$(this).data('status')}</div>
                    </div>
                </div>
            </div>

            <!-- ORGANIZATION -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold text-primary">Organization</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><b>Application:</b> ${$(this).data('application')}</div>
                        <div class="col-md-6"><b>Legal Name:</b> ${$(this).data('legal_name')}</div>

                        <div class="col-md-6 mt-2"><b>Business Type:</b> ${$(this).data('business')}</div>
                    </div>
                </div>
            </div>

            <!-- TAX -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold text-primary">Tax Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><b>PAN:</b> ${$(this).data('pan')}</div>
                        <div class="col-md-6"><b>GST:</b> ${$(this).data('gst')}</div>

                        <div class="col-md-6 mt-2"><b>IEC:</b> ${$(this).data('iec')}</div>
                    </div>
                </div>
            </div>

            <!-- ADDRESS -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold text-primary">Address</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><b>Country:</b> ${$(this).data('country')}</div>
                        <div class="col-md-6"><b>State:</b> ${$(this).data('state')}</div>

                        <div class="col-md-6 mt-2"><b>City:</b> ${$(this).data('city')}</div>
                        <div class="col-md-6 mt-2"><b>Address:</b> ${$(this).data('address')}</div>
                    </div>
                </div>
            </div>

            <!-- BANK -->
            <div class="card mb-3 shadow-sm">
                <div class="card-header bg-light fw-bold text-primary">Bank Details</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><b>Account Name:</b> ${$(this).data('bank_name')}</div>
                        <div class="col-md-6"><b>Account Number:</b> ${$(this).data('bank_number')}</div>

                        <div class="col-md-6 mt-2"><b>IFSC:</b> ${$(this).data('ifsc')}</div>
                        <div class="col-md-6 mt-2"><b>SWIFT:</b> ${$(this).data('swift')}</div>
                    </div>
                </div>
            </div>

            <!-- PAYMENT -->
            <div class="card mb-2 shadow-sm">
                <div class="card-header bg-light fw-bold text-primary">Payment</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6"><b>PayPal:</b> ${$(this).data('paypal')}</div>
                    </div>
                </div>
            </div>

        </div>
        `;

        $('#sellerDetails').html(html);

        let modal = new bootstrap.Modal(document.getElementById('sellerModal'));
        modal.show();
    });

    // =========================
    // VERIFY CHECKBOX ENABLE
    // =========================
    $(document).on('change', '#verifyConfirm', function(){
        $('#verifySellerBtn').prop('disabled', !this.checked);
    });

    // =========================
    // VERIFY BUTTON CLICK
    // =========================
    $(document).on('click', '#verifySellerBtn', function(){

        if(!$('#verifyConfirm').is(':checked')){
            alert('Please confirm verification first');
            return;
        }

        if(!currentSellerId){
            alert('Seller not selected');
            return;
        }

        if(confirm("Verify this seller?")){

            $.ajax({
                url: "{{ route('admin.seller.verify') }}",
                type: "POST",
                data: {
                    id: currentSellerId,
                    _token: "{{ csrf_token() }}"
                },
                success: function(res){

                    if(res.success){
                        alert(res.message);
                        location.reload();
                    }else{
                        alert(res.message);
                    }

                },
                error: function(){
                    alert('Something went wrong');
                }
            });

        }

    });

});
</script>

@endsection

