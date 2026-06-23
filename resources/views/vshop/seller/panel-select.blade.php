<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Select Panel</title>

<!-- Google Fonts -->
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/remixicon@4.2.0/fonts/remixicon.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>

:root {
    --bg-primary: #1e3a8a;
    --bg-secondary: #2563eb;
    --accent-blue: #2563eb;
    --accent-light: #eff6ff;
    --text-main: #0f172a;
    --text-muted: #64748b;
    --card-bg: #ffffff;
    --card-border: #e2e8f0;
}

body{
    margin:0;
    min-height:100vh;
    background: linear-gradient(135deg, #1e40af 0%, #2563eb 50%, #3b82f6 100%);
    font-family: 'Plus Jakarta Sans', sans-serif;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:20px;
    position: relative;
}

/* Subtle glow */
body::before {
    content: '';
    position: absolute;
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(255, 255, 255, 0.15) 0%, rgba(255,255,255,0) 70%);
    top: -200px;
    left: -200px;
    pointer-events: none;
}

.panel-wrapper{
    width:100%;
    max-width:900px;
    z-index: 10;
}

.panel-container{
    position:relative;
    background: var(--card-bg);
    border-radius:24px;
    padding:45px 40px;
    box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15);
}

.back-btn{
    position:absolute;
    top:25px;
    left:25px;
    width:42px;
    height:42px;
    border-radius:12px;
    background: #f1f5f9;
    color: #475569;
    display:flex;
    align-items:center;
    justify-content:center;
    cursor:pointer;
    transition: all 0.3s;
}

.back-btn:hover{
    background: #e2e8f0;
    color: #1e293b;
    transform: translateX(-3px);
}

.panel-title{
    text-align:center;
    font-family: 'Outfit', sans-serif;
    font-weight:800;
    font-size:32px;
    color: #0a1931;
    margin-bottom:8px;
}

.panel-subtitle{
    text-align:center;
    color: var(--text-muted);
    font-size: 15px;
    margin-bottom:40px;
}

.panel-card{
    position:relative;
    background: #f8fafc;
    border: 2px solid #f1f5f9;
    border-radius:20px;
    padding:35px 25px;
    text-align:center;
    cursor:pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    height:100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.panel-card:hover{
    transform:translateY(-5px);
    border-color: #cbd5e1;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
}

.panel-card.active{
    background: var(--accent-light);
    border-color: var(--accent-blue);
    box-shadow: 0 10px 25px rgba(37, 99, 235, 0.08);
}

.panel-card.disabled{
    opacity:0.55;
    cursor:not-allowed;
}

.panel-card.disabled:hover {
    transform: none;
    box-shadow: none;
    border-color: #f1f5f9;
}

/* Checkbox Design */
.custom-checker {
    position: absolute;
    top: 15px;
    right: 15px;
    width: 22px;
    height: 22px;
    border-radius: 6px;
    border: 2px solid #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    background: #ffffff;
}

.panel-card.active .custom-checker {
    background: var(--accent-blue);
    border-color: var(--accent-blue);
}

.custom-checker i {
    color: #ffffff;
    font-size: 12px;
    opacity: 0;
    transform: scale(0.6);
    transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.panel-card.active .custom-checker i {
    opacity: 1;
    transform: scale(1);
}

.panel-card input{
    display: none;
}

/* Icon Boxes */
.icon-box{
    width: 65px;
    height: 65px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 18px;
    font-size: 28px;
    transition: all 0.3s;
}

.icon-vshop {
    background: rgba(255, 107, 107, 0.1);
    color: #ff6b6b;
}
.icon-vtravel {
    background: rgba(78, 205, 196, 0.1);
    color: #4ecdc4;
}
.icon-vcampus {
    background: rgba(162, 89, 255, 0.1);
    color: #a259ff;
}
.icon-vedge {
    background: rgba(255, 209, 102, 0.1);
    color: #ffd166;
}

.panel-card.active .icon-vshop {
    background: #ff6b6b;
    color: #ffffff;
    box-shadow: 0 6px 15px rgba(255, 107, 107, 0.25);
}
.panel-card.active .icon-vtravel {
    background: #4ecdc4;
    color: #ffffff;
    box-shadow: 0 6px 15px rgba(78, 205, 196, 0.25);
}
.panel-card.active .icon-vcampus {
    background: #a259ff;
    color: #ffffff;
    box-shadow: 0 6px 15px rgba(162, 89, 255, 0.25);
}

.title{
    font-size:19px;
    font-weight:700;
    color: var(--text-main);
    margin-bottom: 6px;
}

.desc{
    font-size:13.5px;
    color: var(--text-muted);
    line-height: 1.45;
}

/* Gradient Continue Button */
.continue-btn{
    margin-top:35px;
    background: var(--accent-blue);
    color:#ffffff;
    border:none;
    padding:14px 45px;
    border-radius:50px;
    font-weight:700;
    font-size: 15px;
    letter-spacing: 0.5px;
    transition: all 0.3s;
    box-shadow: 0 6px 20px rgba(37, 99, 235, 0.25);
}

.continue-btn:hover{
    background: var(--bg-primary);
    box-shadow: 0 10px 25px rgba(30, 58, 138, 0.35);
    transform: translateY(-2px);
}

.continue-btn:active {
    transform: translateY(1px);
}

/* Alert Styling */
.alert-custom {
    border-radius: 12px;
    font-size: 14px;
}

.error-box{
    display:none;
    margin-top:20px;
    border-radius: 12px;
}

</style>

</head>

<body>

<div class="panel-wrapper">

<div class="panel-container">

<div class="back-btn" onclick="history.back()">
    <i class="ri-arrow-left-line"></i>
</div>

<h2 class="panel-title">
    Choose Your Workspace
</h2>

<p class="panel-subtitle">
    Select the modules you wish to continue with
</p>

@php
    $requested = is_string($seller->requested_platforms) ? json_decode($seller->requested_platforms, true) : ($seller->requested_platforms ?? []);
    $approved = is_string($seller->approved_platforms) ? json_decode($seller->approved_platforms, true) : ($seller->approved_platforms ?? []);
    if (!is_array($requested)) $requested = [];
    if (!is_array($approved)) $approved = [];
    $isPending = !empty($requested) && empty($approved);
    
    function getCardStatus($platform, $requested, $approved, $isPending) {
        if ($isPending) {
            return in_array($platform, $requested) ? 'pending' : 'disabled';
        } elseif (!empty($approved)) {
            return in_array($platform, $approved) ? 'selectable-card' : 'disabled';
        }
        return 'selectable-card';
    }
@endphp

@if(session('success'))
    <div class="alert alert-success alert-custom mb-4">{{ session('success') }}</div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-custom mb-4">{{ session('error') }}</div>
@endif
@if(session('info'))
    <div class="alert alert-info alert-custom mb-4">{{ session('info') }}</div>
@endif

@if($isPending)
    <div class="alert alert-warning alert-custom text-center mb-4 p-3">
        <i class="ri-time-line me-2 fs-5 align-middle"></i>
        <strong>Pending Approval!</strong> Your request is pending with admin. Within 24 hours you will get an update.
    </div>
@endif

<form method="POST"
      action="{{ route('seller.switch.panel.multi') }}"
      id="panelForm">

    @csrf

    <div class="row g-4">

        <!-- VSHOP -->
        <div class="col-md-6">
            @php $status = getCardStatus('product', $requested, $approved, $isPending); @endphp
            <div class="panel-card {{ $status === 'selectable-card' ? 'selectable-card' : 'disabled' }}"
                 {!! $status === 'selectable-card' ? 'onclick="toggleCard(this)"' : '' !!}>

                @if($status === 'pending')
                    <span class="badge bg-warning text-dark position-absolute" style="top: 20px; left: 20px; border-radius: 8px;">Pending</span>
                @endif
                
                @if($status === 'selectable-card')
                    <div class="custom-checker">
                        <i class="ri-check-line"></i>
                    </div>
                @endif

                <input type="checkbox"
                       name="panels[]"
                       value="product" {{ $status !== 'selectable-card' ? 'disabled' : '' }}>

                <div class="icon-box icon-vshop">
                    <i class="ri-shopping-cart-2-line"></i>
                </div>

                <div class="title">
                    V-Shop
                </div>

                <div class="desc">
                    Manage products & orders
                </div>

            </div>

        </div>

        <!-- VTRAVEL -->
        <div class="col-md-6">
            @php $status = getCardStatus('travel', $requested, $approved, $isPending); @endphp
            <div class="panel-card {{ $status === 'selectable-card' ? 'selectable-card' : 'disabled' }}"
                 {!! $status === 'selectable-card' ? 'onclick="toggleCard(this)"' : '' !!}>

                @if($status === 'pending')
                    <span class="badge bg-warning text-dark position-absolute" style="top: 20px; left: 20px; border-radius: 8px;">Pending</span>
                @endif
                
                @if($status === 'selectable-card')
                    <div class="custom-checker">
                        <i class="ri-check-line"></i>
                    </div>
                @endif

                <input type="checkbox"
                       name="panels[]"
                       value="travel" {{ $status !== 'selectable-card' ? 'disabled' : '' }}>

                <div class="icon-box icon-vtravel">
                    <i class="ri-plane-line"></i>
                </div>

                <div class="title">
                    V-Travel
                </div>

                <div class="desc">
                    Travel services
                </div>

            </div>

        </div>

        <!-- VCAMPUS -->
        <div class="col-md-6">
            @php $status = getCardStatus('vcampus', $requested, $approved, $isPending); @endphp
            <div class="panel-card {{ $status === 'selectable-card' ? 'selectable-card' : 'disabled' }}"
                 {!! $status === 'selectable-card' ? 'onclick="toggleCard(this)"' : '' !!}>

                @if($status === 'pending')
                    <span class="badge bg-warning text-dark position-absolute" style="top: 20px; left: 20px; border-radius: 8px;">Pending</span>
                @endif
                
                @if($status === 'selectable-card')
                    <div class="custom-checker">
                        <i class="ri-check-line"></i>
                    </div>
                @endif

                <input type="checkbox"
                       name="panels[]"
                       value="vcampus" {{ $status !== 'selectable-card' ? 'disabled' : '' }}>

                <div class="icon-box icon-vcampus">
                    <i class="ri-graduation-cap-line"></i>
                </div>

                <div class="title">
                    V-Campus
                </div>

                <div class="desc">
                    Courses & Campus Management
                </div>

            </div>

        </div>

        <!-- VEDGE -->
        <div class="col-md-6">

            <div class="panel-card disabled">

                <div class="icon-box icon-vedge">
                    <i class="ri-cpu-line"></i>
                </div>

                <div class="title">
                    V-Edge
                </div>

                <div class="desc">
                    Coming Soon
                </div>

            </div>

        </div>

    </div>

    <div class="text-center">

        <div class="alert alert-danger error-box"
             id="panelError">
            <i class="ri-error-warning-line me-2"></i>Please select at least one workspace
        </div>

        @if(!$isPending)
        <button type="submit"
                class="continue-btn">
            Continue
        </button>
        @endif

    </div>

</form>

</div>

</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>

function toggleCard(el){

    if(el.classList.contains('disabled')){
        return;
    }

    let checkbox = el.querySelector('input');

    checkbox.checked = !checkbox.checked;

    if(checkbox.checked){
        el.classList.add('active');
    }else{
        el.classList.remove('active');
    }

    $('#panelError').hide();
}

$(document).ready(function() {
    $('#panelForm').submit(function(e){

        let checked = $('input[name="panels[]"]:checked').length;

        if(checked === 0){

            e.preventDefault();

            $('#panelError').fadeIn();

            return false;
        }
    });
});

</script>

</body>
</html>
