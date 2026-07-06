

@if(request()->routeIs('seller.create') || 
    request()->routeIs('seller.products.basic') || 
    request()->routeIs('seller.products.media') || 
    request()->routeIs('seller.products.specifications') || 
    request()->routeIs('seller.products.seo') || 
    request()->routeIs('seller.products.price') ||
    request()->routeIs('seller.organization') ||
    request()->routeIs('seller.contact') ||
    request()->routeIs('seller.bank') ||
    request()->routeIs('seller.payment') ||
    request()->routeIs('seller.agreement'))
    
    @php
    $progress = [
        'Organization' => !empty($seller->legal_name),
        'Contact' => !empty($seller->contact_persons),
        'Bank' => !empty($seller->bank_account_number),
        'Payment' => (
            !empty($seller->paypal_email) ||
            (!empty($seller->razorpay_key) && !empty($seller->razorpay_secret)) ||
            (!empty($seller->stripe_key) && !empty($seller->stripe_secret)) ||
            (!empty($seller->api_key) && !empty($seller->api_secret))
        ),
        'Agreement' => !empty($seller->agreement_file),
    ];

    $completed = collect($progress)->filter()->count();
    $total = count($progress);
    $percent = $total > 0 ? round(($completed / $total) * 100) : 0;
    @endphp

    <div class="card onboarding-card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            {{-- HEADER --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-1">Seller Onboarding</h5>
                    <small class="text-muted">Complete seller setup process</small>
                </div>
                <div class="progress-badge">{{ $percent }}%</div>
            </div>

            {{-- ONLY TOP GREEN PROGRESS BAR --}}
            <div class="progress modern-progress">
                <div class="progress-bar bg-success" style="width:{{ $percent }}%"></div>
            </div>
        </div>
    </div>
@endif


<style>

.onboarding-card{
    border-radius:18px;
    overflow:hidden;
    background:#fff;
}

.progress-badge{
    background:#dcfce7;
    color:#15803d;
    padding:8px 16px;
    border-radius:30px;
    font-size:13px;
    font-weight:700;
}

.modern-progress{
    height:12px;
    border-radius:30px;
    background:#e5e7eb;
    overflow:hidden;
}

.modern-progress .progress-bar{
    border-radius:30px;
    background:linear-gradient(
        90deg,
        #22c55e,
        #16a34a
    );
}

</style>
