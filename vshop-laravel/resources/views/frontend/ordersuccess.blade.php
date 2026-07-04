@extends('frontend.layout')

@section('content')




 <style>
.web-header{display:none;}
.web-footer{display:none;}
.mob-footer{display:none;}

.order-wrapper {
  display: flex; 
	height:100vh;
  font-family: system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
}
.order-left {
  flex: 3;
  background: #fd777a;
  padding: 50px;
  color: #fff;
  overflow: auto;
}
.success-box {
  text-align: center;
  margin-bottom: 40px;
}
.check-circle {
  width: 70px;
  height: 70px;
  background: #fff;
  color: #fd777a;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 32px;
  margin: 0 auto 20px;
}
.order-success-icon{}
.order-success-icon img{}
.success-box h2 {
    font-size: 14px;
    line-height: 20px;
    letter-spacing: 2px;
    margin-bottom: 5px;
}
.success-box h3 {
    font-size: 26px;
    line-height: 32px;
    margin: 5px 0;
}
.success-box p{font-size: 16px; line-height: 22px; margin-bottom: 5px;}
.success-box p strong{}
.tracking-box {
  background: #fff;
  color: #333;
  padding: 30px;
  border-radius: 6px;
}
.order-info {
  margin-bottom: 30px;
}
.steps {
  display: flex;
  justify-content: space-between;
  position: relative;
  margin-bottom: 25px;
}
.steps::before {
  content: "";
  position: absolute;
  top: 12px;
  left: 0;
  right: 0;
  height: 2px;
  background: #ddd;
}
.step {
  text-align: center;
  position: relative;
  width: 20%;
}
.step span {
  width: 14px;
  height: 14px;
  background: #ddd;
  border-radius: 50%;
  display: inline-block;
  margin-bottom: 2px;
  position: relative;
  z-index: 2;
}
.step.active span {
  background: #f33b3f;
}
.step p {
  font-size: 12px;
  line-height:16px;
}
.delivery-info {
  display: flex;
  justify-content: space-between;
  font-size: 14px;
}
.delivery-info a {
  color: #36c37c;
  text-decoration: none;
}
.order-right {
  flex: 1.5;
  background: #fff;
  padding: 10px 20px 20px;
overflow: auto;
}
.order-right .card {
	border:0;
  border-bottom: 1px solid #eee;
  padding: 5px 0;
}
.order-right .card h4,
.order-right .card h5 {
  margin: 0 0 8px;
  font-size: 16px;
}
.order-right .card p{font-size:14px;line-height:20px;}
.card span {
  color: #888;
}
.order-right .invoice-btn {
    background: #f33b3f;
    color: #fff;
    border: none;
    padding: 3px 12px 5px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 12px;
    line-height: 16px;
    font-weight: 600;
}
.order-right .summary .summaryItem {
  display: flex;
  justify-content: space-between;
  margin-bottom: 3px;
  font-size:14px;
  line-height:18px;
  font-weight:500;
}
.order-right .summary .summaryItem.total {
  font-weight: 600;font-size: 16px;
}
.order-right .summary .summaryItem span.txt{color:#000;}
.productSummeryTopArea{display:flex;gap:5px;margin-bottom:5px;}
.productSummeryTopArea .proThumb{width: 60px; height: 60px; flex-shrink: 0;}
.productSummeryTopArea .proThumb img{width: 100%; height: 100%; object-fit: contain; border: 1px solid #ddd;}
.productSummeryTopArea .proInfo{}
.productSummeryTopArea .proTitle{font-weight: 600; text-transform: uppercase;}
.order-sucess-btn-sec{}
.order-sucess-btn-sec .continueBtn{padding: 6px 20px 8px;background: #25262B;border-color: #25262B;border-radius: 30px;font-size: 14px;line-height: 20px;font-weight: 600;}
.order-sucess-btn-sec .viewOrderBtn{padding: 6px 20px;border-radius:4px;margin-left:10px;border-radius: 30px;font-size: 14px;line-height: 20px;font-weight: 600;}


@media (max-width: 1370px) {
.order-success-icon img{max-width: 350px; width: 100%;}
}

@media (max-width: 768px) {
  .order-wrapper {
	 height:auto; 
    flex-direction: column;
  }
  .order-wrapper .order-left{padding:15px 15px;}
  .order-wrapper .order-right{padding:15px 15px;}  
  .productSummeryTopArea .proInfo .proTitle{font-size: 14px;line-height: 20px;}
 
.order-info {
    font-size: 14px;
    line-height: 20px;
    margin-bottom: 15px;
}
.order-info .steps{margin-bottom:12px;}
.order-info .steps p {
    font-size: 10px;
    line-height: 14px;
    font-weight: 500;
	margin-bottom:0px;
}
.delivery-info {
    display: block;   
    font-size: 12px;
    line-height: 16px;
    font-weight: 500;
} 
.success-box h3 {
    font-size: 20px;
    line-height: 24px;
}
.success-box p {
    font-size: 12px;
    line-height: 16px;
}
 
 
}
	  </style>



<section class="order-success-sec_1">
    <div class="order-wrapper">
	  
       

        @if($order)
		  <div class="order-left">	
		 <div class="success-box">
			<div class="order-success-icon">
            <img src="{{ asset('assets/images/order-confirmed-img.png') }}" alt="Success" class="">
			</div>
            <h2>THANK YOU</h2>
			<h3>Your Order Is Confirmed</h3>
			<p>We will be sending you an email confirmation shortly.</p>
			<p class="mb-0">
			Order <strong>#{{ $order->id }}</strong> was placed on {{ $order->created_at->format('d M, Y h:i A') }} and is currently in progress
		  </p>
        </div>
		 <!-- Order ID -->
		<div class="tracking-box d-none">
		  <p class="order-info">
			Order <strong>#{{ $order->id }}</strong> was placed on {{ $order->created_at->format('d M, Y h:i A') }} and is currently in progress
		  </p>
		 
		  <div class="steps">
        <div class="step active">
          <span></span>
          <p>Order<br>Confirmed</p>
        </div>
        <div class="step">
          <span></span>
          <p>Start<br>Production</p>
        </div>
        <div class="step">
          <span></span>
          <p>Quality<br>Check</p>
        </div>
        <div class="step">
          <span></span>
          <p>Dispatched<br>Item</p>
        </div>
        <div class="step">
          <span></span>
          <p>Product<br>Delivered</p>
        </div>
      </div>
	   <div class="delivery-info">
        Expected Delivery Date: <strong>{{ $order->created_at->format('d M, Y h:i A') }}</strong>
        <a href="#">Track Your Order</a>
      </div>
	  </div> 
		</div>
        <!-- Order Summary -->
		<div class="order-right">
        <div class="card">
		<div class="d-flex justify-content-between oderderInfo">
			<div class="leftPart">	
		  <h4 class="mb-0">ORDER DETAIL <span class="d-block">#{{ $order->id }}</span></h4>
		  </div>
		  <div class="rightPart">
		  <button class="invoice-btn">Download Invoice</button>
		  </div>
		  </div>
           </div>
               
                
                @foreach($order->items as $item)
                @php
                    $mainProductAttributes = $item->main_product_attributes ? json_decode($item->main_product_attributes, true) : [];
                    $comboProductAttributes = $item->combo_product_attributes ? json_decode($item->combo_product_attributes, true) : [];
                    
                    $comboProduct = null;
                    if ($item->combo_id) {
                        $comboProduct = \App\Models\Product::find($item->combo_id);
                    }
                    
                    if($item->combo_id && $comboProduct) {
                        $mainProductPrice = $item->main_product_price ?? 0;
                        $mainProductQuantity = $item->main_product_quantity ?? 1;
                        $comboProductPrice = $item->combo_product_price ?? 0;
                        $comboProductQuantity = $item->combo_product_quantity ?? 1;
                        
                        $mainProductTotal = $mainProductPrice * $mainProductQuantity;
                        $comboProductTotal = $comboProductPrice * $comboProductQuantity;
                        $totalUnitPrice = $mainProductTotal + $comboProductTotal;
                        $gstPercent = $item->gst_percent ?? 5;
                        $gstAmount = $totalUnitPrice * ($gstPercent / 100);
                        $courierCharge = $item->courier_charge ?? 0;
                        $subtotal = $totalUnitPrice + $gstAmount + $courierCharge;
                    } else {
                        $unitPrice = $item->price ?? 0;
                        $quantity = $item->quantity ?? 1;
                        $totalUnitPrice = $unitPrice * $quantity;
                        $gstPercent = $item->gst_percent ?? 5;
                        $gstAmount = $totalUnitPrice * ($gstPercent / 100);
                        $courierCharge = $item->courier_charge ?? 0;
                        $subtotal = $totalUnitPrice + $gstAmount + $courierCharge;
                    }
                    
                    $mainColor = null;
                    $mainSize = null;
                    $mainFabric = null;
                    
                    if (!empty($mainProductAttributes)) {
                        foreach ($mainProductAttributes as $attr) {
                            $slug = $attr['attribute_slug'] ?? '';
                            if (in_array($slug, ['colour', 'color'])) {
                                $mainColor = $attr['term_name'] ?? null;
                            } elseif ($slug === 'size') {
                                $mainSize = $attr['term_name'] ?? null;
                            } elseif (in_array($slug, ['febric', 'fabric'])) {
                                $mainFabric = $attr['term_name'] ?? null;
                            }
                        }
                    }
                    
                    if (!$mainColor && $item->color) {
                        $mainColor = $item->color->term;
                    }
                    if (!$mainSize && $item->size) {
                        $mainSize = $item->size->term;
                    }
                @endphp
                
                
                  <div class="card">
				  <h5>Product Summary</h5>
				  <div class="productSummeryTopArea">				 
					  <div class="proThumb">
						<img src="{{ asset($item->product->thumbnail ?? 'images/default-product.png') }}" 
										 class="img-fluid rounded" 
										 alt="{{ $item->product->name ?? 'Product' }}"
										 style="">
					  </div>					
					<div class="flex-grow-1 proInfo">
                    <h5 class="mb-2 proTitle" style="">{{ $item->product->name ?? 'Product not found' }}</h5>                                
					</div>				
				  </div>
				  <div class="summary">
								<div class="summaryItem">
                                   <span class="ttl"> Category / Brand</span> <span class="txt">{{ $item->product->category->name ?? '-' }}</span>
                                </div>
                                
                                @if($mainColor)
                                <div class="summaryItem">
                                    <span class="ttl">Color Name</span> <span class="txt">{{ $mainColor }}</span>
                                </div>
                                @endif
                                
                                @if($mainSize)
                                <div class="summaryItem">
                                    <span class="ttl">Size</span> <span class="txt">{{ $mainSize }}</span>
                                </div>
                                @endif
                                
                                @if($mainFabric)
                                <div class="summaryItem">
                                    <span class="ttl">Fabric</span> <span class="txt">{{ $mainFabric }}</span>
                                </div>
                                @endif
                                
                                @if($item->courier_charge)
                                <div class="summaryItem">
                                    <span class="ttl">Expected Delivery</span> <span class="txt">{{ $item->expected_delivery ?? 'N/A' }}</span>
                                </div>
                                @endif
				  </div>
				  
                        
                    </div>
                    
                    <!-- Price Summary -->
                    <div class="card">
					<h5>Price Summary</h5>
                        <div class="summary">
                            @if($item->combo_id && $comboProduct)
                                <div class="summaryItem">
                                    <span class="ttl">Price (per unit)</span>
                                    <span class="txt">₹{{ number_format($mainProductPrice, 2) }}</span>
                                </div>
                                <div class="summaryItem">
                                    <span class="ttl">Quantity</span>
                                    <span class="txt">X {{ $mainProductQuantity }}</span>
                                </div>
                                <div class="summaryItem">
                                    <span class="ttl">Combo Price</span>
                                    <span class="txt">₹{{ number_format($comboProductPrice, 2) }}</span>
                                </div>
                                <div class="summaryItem">
                                    <span class="ttl">Combo Quantity</span>
                                    <span class="txt">X {{ $comboProductQuantity }}</span>
                                </div>
                                <div class="summaryItem">
                                    <span class="ttl">Total Price</span>
                                    <span class="txt">₹{{ number_format($totalUnitPrice, 2) }}</span>
                                </div>
                            @else
                                <div class="summaryItem">
                                    <span class="ttl">Price (per unit)</span>
                                    <span class="txt">₹{{ number_format($item->price, 2) }}</span>
                                </div>
                                <div class="summaryItem">
                                    <span class="ttl">Quantity</span>
                                    <span class="txt">X {{ $item->quantity }}</span>
                                </div>
                                <div class="summaryItem">
                                    <span class="ttl">Total Price</span>
                                    <span class="txt">₹{{ number_format($totalUnitPrice, 2) }}</span>
                                </div>
                            @endif
                            
                            <div class="summaryItem">
                                <span class="ttl">GST</span>
                                <span class="txt">{{ $gstPercent }}% (+₹{{ number_format($gstAmount, 2) }})</span>
                            </div>
                            
                            @if($courierCharge > 0)
                            <div class="summaryItem">
                                <span class="ttl">Shipping Charge</span>
                                <span class="txt">+₹{{ number_format($courierCharge, 2) }}</span>
                            </div>
                            @endif
                            
                            <div class="summaryItem">
                                <span class="ttl">Subtotal</span>
                                <span class="txt">₹{{ number_format($subtotal, 2) }}</span>
                            </div>
                        </div>
                    </div>
               
                @endforeach
                
                <!-- Total Order Summary -->
                    <div class="card">
					<h5>Order Summary</h5>
                        <div class="summary">
                             <div class="summaryItem">
                                <span class="ttl">Sub Total</span>
                                <span class="txt">₹{{ number_format($order->sub_total ?? 0, 2) }}</span>
                            </div>
                             <div class="summaryItem">
                                <span class="ttl">GST Amount</span>
                                <span class="txt">₹{{ number_format($order->gst_amount ?? 0, 2) }}</span>
                            </div>
                            @if($order->courier_charge > 0)
                             <div class="summaryItem">
                                <span class="ttl">Shipping Charge</span>
                                <span class="txt">₹{{ number_format($order->courier_charge, 2) }}</span>
                            </div>
                            @endif
                             <div class="summaryItem total">
                                <span class="ttl">Grand Total</span>
                                <span class="txt">₹{{ number_format($order->total_amount, 2) }}</span>
                            </div>
                        </div>
                    </div>
               
            
        
		
        @endif

        <div class="mt-4 text-center order-sucess-btn-sec">
            <a href="{{ route('dashboard') }}" class="btn btn-primary continueBtn" style="">
                Continue Shopping
            </a>

            <a href="{{ route('frontend.orders') }}" class="btn btn-outline-dark viewOrderBtn" style="">
                View My Orders
            </a>
        </div>
		</div>
    </div>
</section>


<script type="module">

// Firebase Imports
import { initializeApp } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-app.js";
import { getDatabase, ref, push, set } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-database.js";

// Firebase Config
const firebaseConfig = {
    apiKey: "AIzaSyCTJhosvcfXwQIpWnEEFwoXQx6rCXBMnYY",
    authDomain: "mafatlalshop-90155.firebaseapp.com",
    databaseURL: "https://mafatlalshop-90155-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "mafatlalshop-90155",
    storageBucket: "mafatlalshop-90155.firebasestorage.app",
    messagingSenderId: "858357107416",
    appId: "1:858357107416:web:80c49eb1feaf0d843d65ad",
    measurementId: "G-K3L8YLB6XG"
};

// Init Firebase
const app = initializeApp(firebaseConfig);
const db = getDatabase(app);

// Laravel data
const orderData = @json(session('firebase_order'));

// Save to Firebase only once
if (orderData) {

    // Create unique order key
    const ordersRef = ref(db, "orders");
    const newOrderRef = push(ordersRef); // generates unique key

    // Data with Firebase Key added
    const finalData = {
        firebase_order_key: newOrderRef.key, // UNIQUE KEY
        ...orderData
    };

    // Insert into Firebase
    set(newOrderRef, finalData)
        .then(() => console.log("Order saved to Firebase:", newOrderRef.key))
        .catch(err => console.error("Firebase Error:", err));
}

</script>

@endsection
