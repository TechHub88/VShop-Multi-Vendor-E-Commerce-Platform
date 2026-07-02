

@extends('manager.layout')
@section('content')
<div class="adminPagesBodySec">
<div class="container-fluid">		
<div class="adminPagesBodySecinner">
		<div class="row rowBox">		
		<div class="col-xxl-8 col-xl-8 col-lg-8 col-md-12 col-sm-12 col-12 columnBox">
		<div class="dashboard_card">  
		<div class="d-flex justify-content-between align-items-center dashboard_card_header ordersDts_card_header">
		<div class="leftPart">
		<h5 class="mb-0">Order #{{ $order->id }} Details</h5>
		</div>
		<div class="rightPart">
		<div>
                                                            <a href="#!" class="btn btn-outline-success">Approve</a>
                                                            <a href="#!" class="btn btn-outline-secondary">Reject</a>
                                                            <a href="#!" class="btn btn-primary">Back to Orders</a>
                                                       </div>
		</div>
		</div>	
		<div class="dashboard_card_body ">
      <div class="mt-2">
                                                       <h5 class="fw-medium text-dark">Progress</h5>
                                                  </div>
          <div class="row row-cols-xxl-5 row-cols-md-2 row-cols-1">
   <div class="col">
      <div class="progress mt-3" style="height: 10px;">
         <div class="progress-bar progress-bar  progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="70">
         </div>
      </div>
      <p class="mb-0 mt-2">Order Confirming</p>
   </div>
   <div class="col">
      <div class="progress mt-3" style="height: 10px;">
         <div class="progress-bar progress-bar  progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="70">
         </div>
      </div>
      <p class="mb-0 mt-2">Payment Pending</p>
   </div>
   <div class="col">
      <div class="progress mt-3" style="height: 10px;">
         <div class="progress-bar progress-bar  progress-bar-striped progress-bar-animated bg-warning" role="progressbar" style="width: 60%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="70">
         </div>
      </div>
      <div class="d-flex align-items-center gap-2 mt-2">
         <p class="mb-0">Processing</p>
         <div class="spinner-border spinner-border-sm text-warning" role="status">
            <span class="visually-hidden">Loading...</span>
         </div>
      </div>
   </div>
   <div class="col">
      <div class="progress mt-3" style="height: 10px;">
         <div class="progress-bar progress-bar  progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="70">
         </div>
      </div>
      <p class="mb-0 mt-2">Shipping</p>
   </div>
   <div class="col">
      <div class="progress mt-3" style="height: 10px;">
         <div class="progress-bar progress-bar  progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: 0%" aria-valuenow="70" aria-valuemin="0" aria-valuemax="70">
         </div>
      </div>
      <p class="mb-0 mt-2">Delivered</p>
   </div>
</div>

           
			
			</div>
			</div>
			
			<div class="dashboard_card">  
			<div class="d-flex justify-content-between align-items-center dashboard_card_header ordersDts_card_header">
			<div class="leftPart">
			<h5 class="mb-0">Order Summary</h5>
			</div>
			<div class="rightPart">		
			</div>
			</div>	
			<div class="dashboard_card_body ">
			<div class="orderSummaryArea">
			<div class="table-responsive">
			   <table class="table mb-0">
				  <tbody>
					 <tr>
						<td class="px-0">
						   <p class="d-flex mb-0 align-items-center gap-1">
							  <i class="fa-light fa-clipboard-list"></i>
							  Sub Total : 
						   </p>
						</td>
						<td class="text-end text-dark fw-medium px-0">$777.00</td>
					 </tr>
					 <tr>
						<td class="px-0">
						   <p class="d-flex mb-0 align-items-center gap-1">
							  <i class="fa-light fa-ticket"></i>
							  Discount : 
						   </p>
						</td>
						<td class="text-end text-dark fw-medium px-0">-$60.00</td>
					 </tr>
					 <tr>
						<td class="px-0">
						   <p class="d-flex mb-0 align-items-center gap-1">
							  <i class="fa-light fa-person-carry-box"></i>
							  Delivery Charge : 
						   </p>
						</td>
						<td class="text-end text-dark fw-medium px-0">$00.00</td>
					 </tr>
					 <tr>
						<td class="px-0">
						   <p class="d-flex mb-0 align-items-center gap-1">
							  <i class="fa-light fa-calculator"></i>
							  Estimated Tax (15.5%) : 
						   </p>
						</td>
						<td class="text-end text-dark fw-medium px-0">$20.00</td>
					 </tr>
				  </tbody>
			   </table>
			</div>
			
			</div>
			</div>
			</div>
			
			
			</div>
			
			<div class="col-xxl-4 col-xl-4 col-lg-4 col-md-12 col-sm-12 col-12 columnBox">
			
			<div class="dashboard_card">  
			<div class="d-flex justify-content-between align-items-center dashboard_card_header ordersDts_card_header">
			<div class="leftPart">
			<h5 class="mb-0">Customer Details</h5>
			</div>
			<div class="rightPart">		
			</div>
			</div>	
			<div class="dashboard_card_body ">
			<div class="customerDetailsArea">
			<div class="row rowBox2">
			<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox customerDetailsBox mb-10">
			<div class="customerDetailsBoxinner">
			<div class="d-flex align-items-center gap-1 customerProfile">
				<div class="customerThumb">
			   <img src="https://cdn-icons-png.flaticon.com/512/11753/11753627.png" alt="" class="img-fluid avatar rounded-3 border border-light border-3" style="width:45px;">
			   </div>
			   <div class="customerInfo">
				  <p class="mb-0"> {{ $order->customer->name ?? '' }}</p>
				  <a href="#!" class="link-primary fw-medium">{{ $order->customer->email ?? '' }}</a>
			   </div>
			</div>
			</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox customerDetailsBox mb-10">
			<div class="customerDetailsBoxinner">
			<div class="d-flex justify-content-between mt-1 titleArea">
			 <div class="leftPart">
             <h6 class="ttl">Contact Number</h6>
			 </div>
            <div class="rightPart">
             <a href="#!"><i class="fa-light fa-pen-line"></i></a>
            </div>
            </div>
			<div class="d-flex justify-content-between mt-1 customerInfoArea">
			<p class="mb-0">{{ $order->customer->phone ?? '' }}</p>
			</div>
			</div>
			</div>
			<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox customerDetailsBox mb-10">
			<div class="customerDetailsBoxinner">
			<div class="d-flex justify-content-between mt-1 titleArea">
			 <div class="leftPart">
             <h6 class="ttl">Address</h6>
			 </div>
            <div class="rightPart">
             <a href="#!"><i class="fa-light fa-pen-line"></i></a>
            </div>
            </div>
			<div class="d-flex justify-content-between mt-1 customerInfoArea">
			<p class="mb-0">
				{{ $order->customer->firstAddress->address ?? '' }},
                {{ $order->customer->firstAddress->city ?? '' }},
                {{ $order->customer->firstAddress->state ?? '' }} -
                {{ $order->customer->firstAddress->pincode ?? '' }}
			</p>
			</div>
			</div>
			</div>
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox customerDetailsBox">
			<div class="customerDetailsBoxinner">
			<div class="d-flex justify-content-between mt-1 titleArea">
			 <div class="leftPart">
             <h6 class="ttl">Payment Method:</h6>
			 </div>
            <div class="rightPart">
             <!--<a href="#!"><i class="fa-light fa-pen-line"></i></a>-->
            </div>
            </div>
			<div class="d-flex justify-content-between mt-1 customerInfoArea">
			<p class="mb-0">{{ ucfirst($order->payment_method ?? '') }}</p>
			</div>
			</div>
			</div>
			
			
			</div>
			</div>
			
			
			
			</div>
			</div>
			
			<div class="dashboard_card">  
			<div class="d-flex justify-content-between align-items-center dashboard_card_header ordersDts_card_header">
			<div class="leftPart">
			<h5 class="mb-0">Payment Information</h5>
			</div>
			<div class="rightPart">		
			</div>
			</div>	
			<div class="dashboard_card_body ">
			<div class="d-flex align-items-center gap-2 mb-2 Paymen informationTopBar">
			   <div class="rounded-3 bg-light avatar d-flex align-items-center justify-content-center">
				  <img src="https://techzaa.in/larkon/admin/assets/images/card/mastercard.svg" alt="" class="img-fluid avatar-sm" style="width:45px;">
			   </div>
			   <div>
				  <p class="mb-0 text-dark fw-medium">Master Card</p>
				  <p class="mb-0 text-dark">xxxx xxxx xxxx 7812</p>
			   </div>
			   <div class="ms-auto">
				  <iconify-icon icon="solar:check-circle-broken" class="fs-22 text-success"></iconify-icon>
			   </div>
			</div>
			
			<div class="customerDetailsArea">
			<div class="row rowBox2">
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox customerDetailsBox mb-10">
			<div class="customerDetailsBoxinner">
			<div class="d-flex justify-content-between mt-1 titleArea">
			 <div class="leftPart">
             <h6 class="ttl">Transaction ID :</h6>
			 </div>
            <div class="rightPart">
             <!--<a href="#!"><i class="fa-light fa-pen-line"></i></a>-->
            </div>
            </div>
			<div class="d-flex justify-content-between mt-1 customerInfoArea">
			<p class="mb-0">#IDN768139059</p>
			</div>
			</div>
			</div>
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox customerDetailsBox">
			<div class="customerDetailsBoxinner">
			<div class="d-flex justify-content-between mt-1 titleArea">
			 <div class="leftPart">
             <h6 class="ttl">Card Holder Name : Gaston</h6>
			 </div>
            <div class="rightPart">
             <!--<a href="#!"><i class="fa-light fa-pen-line"></i></a>-->
            </div>
            </div>
			<div class="d-flex justify-content-between mt-1 customerInfoArea">
			<p class="mb-0">Lapierre</p>
			</div>
			</div>
			</div>
			
			
			</div>
			</div>
			
			
			
			</div>
			</div>
			
			
			
			
			
			</div>
			
			
			
			
			
			<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox">
			<div class="dashboard_card">       
			<div class="d-flex justify-content-between align-items-center dashboard_card_header">
		<div class="leftPart">
		<h4 class="mb-0 dashboard_card_title ">Order Items</h4>
		</div>
		<div class="rightPart">
		<div class="dropdown">
		  <button type="button" class="btn btn-defalt dropdown-toggle" data-bs-toggle="dropdown">
			This Month
		  </button>
		  <ul class="dropdown-menu">
			<li><a class="dropdown-item" href="#">Download</a></li>
			<li><a class="dropdown-item" href="#">Export</a></li>
			<li><a class="dropdown-item" href="#">Import</a></li>
		  </ul>
		</div>
		</div>
		</div>
           
			<div class="dashboard_card_body ">
			<div class="table-responsive ">
            <table class="table table-bordered orderItemsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Image</th>
                         <th>Gst Amount</th>
                        <th>Color</th>
                        <th>Size</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                             <td>{{ $item->product->name ?? '' }}</td>
                            <td>
    @if($item->product && $item->product->images)
        @php
            // Decode images array properly
            $images = is_array($item->product->images) 
                ? $item->product->images 
                : json_decode($item->product->images, true);

            $firstImage = $images[0] ?? null;
        @endphp

        @if($firstImage)
            <img src="{{ asset($firstImage) }}" 
                 width="60" height="60"
                 style="object-fit:cover; border-radius:4px;">
        @else
            <img src="https://via.placeholder.com/60x60?text=No+Image" 
                 width="60" height="60"
                 style="object-fit:cover; border-radius:4px;">
        @endif
    @else
        <img src="https://via.placeholder.com/60x60?text=No+Image" 
             width="60" height="60"
             style="object-fit:cover; border-radius:4px;">
    @endif
</td>

                            
                              <td>₹{{ number_format($order->gst_amount, 2) }}</td>
                            <td>{{ $item->color_id  ?? '' }}</td>
                            <td>{{ $item->size_id  ?? '' }}</td>

                            <td>{{ $item->quantity }}</td>
                            <td>₹{{ number_format($item->price, 2) }}</td>
                            <td>₹{{ number_format($item->quantity * $item->price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
			</div>

            <p><strong>Total Amount:</strong> ₹{{ number_format($order->total_amount,2) }}</p>
            <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
	@if($order->status == 'pending')
    <!-- Approve & Reject buttons -->
    <div style="display: flex; gap: 10px;">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#paymentModal">
            Approve
        </button>

        <form action="{{ route('manager.order.reject', $order->id) }}" method="POST">
            @csrf
            <button class="btn btn-danger">Reject</button>
        </form>
		<a href="{{ route('manager.orders') }}" class="btn btn-secondary mt-3">Back to Orders</a>
    </div>

	</div>
	</div>
	</div>
	</div>
            
       


<!-- ✅ Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="approveOrderForm" action="{{ route('manager.order.approve', $order->id) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title" id="paymentModalLabel">Select Payment Option</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">

          <!-- Payment Options -->
          <div class="mb-3">
            <label class="form-label"><strong>Payment Method:</strong></label>
            <select name="payment_method" id="paymentMethod" class="form-select" required>
              <option value="">-- Select Payment --</option>
              <option value="UPI">UPI</option>
              <option value="Cash on Delivery">Cash on Delivery</option>
              <option value="Wallet">Wallet</option>
            </select>
          </div>

          <!-- Wallet Info -->
          <div class="mb-3" id="walletInfo" style="display:none;">
            @php
              $manager = auth()->user(); // ✅ logged-in manager
              $walletBalance = $manager->wallet_balance ?? 0;
            @endphp

            <p><strong>Wallet Balance:</strong> ₹<span id="walletBalance">{{ number_format($walletBalance, 2) }}</span></p>
            <p><strong>Order Total:</strong> ₹<span id="orderTotal">{{ number_format($order->total_amount, 2) }}</span></p>

            <p id="walletWarning" class="text-danger fw-bold" style="display:none;">⚠️ Insufficient wallet balance to approve this order.</p>
          </div>

        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success" id="modalApproveBtn">Approve</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ✅ JS Logic -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    const paymentSelect = document.getElementById("paymentMethod");
    const walletInfo = document.getElementById("walletInfo");
    const walletWarning = document.getElementById("walletWarning");
    const approveBtn = document.getElementById("modalApproveBtn");

    const walletBalance = parseFloat(document.getElementById("walletBalance").innerText.replace(/,/g, ""));
    const orderTotal = parseFloat(document.getElementById("orderTotal").innerText.replace(/,/g, ""));

    paymentSelect.addEventListener("change", function () {
        const selected = this.value;

        if (selected === "Wallet") {
            walletInfo.style.display = "block";

            // Check balance
            if (walletBalance < orderTotal) {
                walletWarning.style.display = "block";
                approveBtn.disabled = true;
            } else {
                walletWarning.style.display = "none";
                approveBtn.disabled = false;
            }

        } else {
            // Hide wallet info if other method selected
            walletInfo.style.display = "none";
            walletWarning.style.display = "none";
            approveBtn.disabled = false;
        }
    });
});
</script>
   
@endif


</div>
</div>
</div>

@endsection	