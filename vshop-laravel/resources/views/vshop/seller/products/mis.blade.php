@extends('vshop.seller.main')

@section('content')

@php

/*
|--------------------------------------------------------------------------
| SESSION PANELS
|--------------------------------------------------------------------------
*/

$selectedPanels = session('selected_panels', []);

/*
|--------------------------------------------------------------------------
| SERVICE TYPES
|--------------------------------------------------------------------------
*/

$serviceTypes = session('service_types', []);

/*
|--------------------------------------------------------------------------
| MODULE TYPE
|--------------------------------------------------------------------------
*/

$moduleType = 'product';

if(
    in_array('product', $selectedPanels)
    &&
    in_array('travel', $selectedPanels)
){

    $moduleType = 'both';

}
elseif(
    !in_array('product', $selectedPanels)
    &&
    in_array('travel', $selectedPanels)
){

    $moduleType = 'service';

}
elseif(
    in_array('vcampus', $selectedPanels)
){

    $moduleType = 'course';

}

/*
|--------------------------------------------------------------------------
| AUTO SERVICE TYPE LOGIC
|--------------------------------------------------------------------------
*/

if(
    in_array('product', $selectedPanels)
    &&
    !in_array('travel', $selectedPanels)
){

    if(empty($serviceTypes)){

        $serviceTypes = ['professional'];

    }

}
elseif(
    !in_array('product', $selectedPanels)
    &&
    in_array('travel', $selectedPanels)
){

    if(empty($serviceTypes)){

        $serviceTypes = [
            'tour_guide',
            'others'
        ];

    }

}
elseif(
    in_array('product', $selectedPanels)
    &&
    in_array('travel', $selectedPanels)
){

    if(empty($serviceTypes)){

        $serviceTypes = [
            'professional',
            'tour_guide',
            'others'
        ];

    }

}

/*
|--------------------------------------------------------------------------
| LABELS
|--------------------------------------------------------------------------
*/

$singleLabel = 'Product';
$pluralLabel = 'Products';

if($moduleType == 'service'){

    $singleLabel = 'Service';
    $pluralLabel = 'Services';

}
elseif($moduleType == 'course'){

    $singleLabel = 'Course';
    $pluralLabel = 'Courses';

}
elseif($moduleType == 'both'){

    $singleLabel = 'Product / Service';
    $pluralLabel = 'Products / Services';

}

/*
|--------------------------------------------------------------------------
| NEW SELLER CHECK
|--------------------------------------------------------------------------
*/

$isNewSeller = (
    $totalProducts == 0 &&
    $totalServices == 0 &&
    $totalRatings == 0 &&
    $totalReviews == 0
);

// Get filter parameters from request
$currentServiceTypes = request()->get('service_types', '');
$selectedServiceTypes = !empty($currentServiceTypes) ? explode(',', $currentServiceTypes) : [];

@endphp

<style>

body{
    background:#f4f7fb;
}

/* =========================================
GLOBAL
========================================= */

.mis-wrapper{
    padding:25px;
    position: relative;
}

a{
    text-decoration:none !important;
}

button:focus,
input:focus{
    outline:none !important;
    box-shadow:none !important;
}

/* =========================================
HEADER
========================================= */

.mis-header-new{
    display:flex;
    justify-content:space-between;
    align-items:center;
    flex-wrap:wrap;
    gap:20px;
    margin-bottom:25px;
}

.header-left{
    display:flex;
    flex-direction:column;
    gap:15px;
    width:100%;
}

.top-back-heading-sec{
    display:flex;
    align-items:center;
    gap:12px;
}

.page-title{
    margin:0;
    font-size:28px;
    font-weight:800;
    color:#0f172a;
}

.nav-btn{
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    width: 35px;
    height: 38px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.page-title {
    font-size: 24px;
}


/* =========================================
BUTTONS
========================================= */

.mis-tabs{
    display:flex;
    flex-wrap:wrap;
    gap:12px;
    align-items:center;
}

.mis-tabs .btn{
    border-radius:10px;
    padding:10px 18px;
    font-weight:700;
    font-size:14px;
    border:none;
    transition:0.3s;
}

.mis-tabs .btn-success{
    /*background:#16a34a;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
}

.mis-tabs .btn-success:hover{
    /*background:#15803d;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    transform:translateY(-2px);
}

.mis-tabs .btn-warning{
    /*background:#f59e0b;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    color:#fff;
}

.mis-tabs .btn-warning:hover{
    /*background:#d97706;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    transform:translateY(-2px);
}

.mis-tabs .btn-info{
    /*background:#0ea5e9;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    color:#fff;
}

.mis-tabs .btn-info:hover{
    /*background:#0284c7;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    transform:translateY(-2px);
}

/* Active filter badge */
.filter-badge{
    /*background:#e0e7ff;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    color:#4338ca;
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    display:inline-flex;
    align-items:center;
    gap:6px;
}

.filter-badge i{
    cursor:pointer;
    font-size:10px;
}

.filter-badge i:hover{
    color:#dc2626;
}

/* =========================================
MAIN DROPDOWN
========================================= */

.main-type-dropdown-wrapper{
    position:relative;
}

.main-type-btn{
    min-width:170px;
    display:flex;
    align-items:center;
    justify-content:center;
    gap:8px;
}

.main-type-dropdown-menu{
    position:absolute;
    top:115%;
    left:0;
    width:280px;
    background:#fff;
    border-radius:14px;
    padding:12px;
    box-shadow:0 15px 40px rgba(0,0,0,0.12);
    border:1px solid #e2e8f0;
    z-index:99999;
    display:none;
}

.main-type-link{
    display:block;
    width:100%;
    padding:12px 14px;
    border-radius:10px;
    background:#f8fafc;
    color:#0f172a;
    font-weight:700;
    margin-bottom:8px;
    transition:0.3s;
    text-align:center;
}

.main-type-link:hover{
    background:#c7eafe;
    color:#233d78;
}

.service-sub-dropdown-wrapper{
    position:relative;
}

.service-sub-btn{
    width:100%;
    border:none;
    background:#f8fafc;
    padding:12px 14px;
    border-radius:10px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    font-weight:700;
    transition:0.3s;
}

.service-sub-btn:hover{
    background:#c7eafe;
    color:#233d78;
}

.service-sub-dropdown-menu{
    position:absolute;
    top:0;
    left:105%;
    width:280px;
    background:#fff;
    border-radius:14px;
    padding:15px;
    box-shadow:0 15px 40px rgba(0,0,0,0.12);
    border:1px solid #e2e8f0;
    display:none;
    z-index:999999;
}

.service-checkbox-list{
    display:flex;
    flex-direction:column;
    gap:12px;
}

.service-check-card{
    display:flex;
    align-items:center;
    gap:12px;
    border:1px solid #e2e8f0;
    border-radius:12px;
    padding:12px;
    cursor:pointer;
    transition:0.3s;
    background:#fff;
}

.service-check-card:hover{
    background:#a6dbfa;
    border-color:#233d77;
}

.service-check-card.active{
    background:#a6dbfa;
    border-color: #233d77;
}

.service-check-card input{
    width:18px;
    height:18px;
    accent-color:#16a34a;
}

.service-check-title{
    font-size:14px;
    font-weight:700;
    color:#0f172a;
}

.filter-actions{
    display:flex;
    gap:10px;
    margin-top:15px;
}

.go-btn, .reset-btn{
    flex:1;
    border:none;
    padding:10px;
    border-radius:10px;
    font-weight:700;
    transition:0.3s;
}

.go-btn{
    /*background:#16a34a;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    color:#fff;
    justify-content: center;
}

.go-btn:hover{
    /*background:#15803d;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
}

.reset-btn{
    background:#e2e8f0;
    color:#64748b;
}

.reset-btn:hover{
    background:#cbd5e1;
}

/* =========================================
STATS
========================================= */

.stats-wrapper{
    margin-top:10px;
}

.mis-card{
    background:#fff;
    border-radius:18px;
    padding:25px;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
    position:relative;
    overflow:visible;
    transition:0.3s;
    height:100%;
}

.mis-card.dropdown-active {
    z-index: 50;
}

.mis-card:hover{
    box-shadow: 0 15px 35px rgba(0,0,0,0.08);
}

.mis-card h2{
    font-size:38px;
    font-weight:800;
    margin-bottom:8px;
    color:#0f4cbd;
}

.mis-card p{
    margin:0;
    display:flex;
    align-items:center;
    justify-content:space-between;
    color:#64748b;
    font-size:15px;
    font-weight:600;
}

.mis-card span{
    cursor:pointer;
    font-size:13px;
}

/* =========================================
CARD DROPDOWN
========================================= */

.dropdown-menu-custom{
    position:absolute;
    top:90%;
    left:0;
    width:100%;
    background:#fff;
    border-radius:14px;
    box-shadow:0 15px 35px rgba(0,0,0,0.12);
    border:1px solid #e2e8f0;
    display:none;
    z-index:9999;
    overflow:hidden;
}

.dropdown-menu-custom a{
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:14px 16px;
    color:#0f172a;
    font-size:14px;
    font-weight:600;
    border-bottom:1px solid #f1f5f9;
    transition:0.3s;
}

.dropdown-menu-custom a:last-child{
    border-bottom:none;
}

.dropdown-menu-custom a:hover{
    background:#f8fafc;
    color:#2563eb;
}

/* =========================================
CHART
========================================= */

.chart-card{
    background:#fff;
    border-radius:20px;
    padding:22px;
    box-shadow:0 10px 30px rgba(0,0,0,0.06);
    height:100%;
    transition:0.3s;
}

.chart-card:hover{
    transform:translateY(-4px);
}

.chart-header-flex{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.chart-title{
    font-size:20px;
    font-weight:800;
    color:#0f172a;
}

.chart-subtitle{
    color:#94a3b8;
    font-size:13px;
}

.live-badge{
    background:#dcfce7;
    color:#15803d;
    padding:6px 12px;
    border-radius:30px;
    font-size:12px;
    font-weight:700;
}

.blue-badge{
    background:#dbeafe;
    color:#2563eb;
}

.red-badge{
    background:#fee2e2;
    color:#dc2626;
}

.purple-badge{
    background:#ede9fe;
    color:#7c3aed;
}

/* =========================================
EMPTY STATE
========================================= */

.blank-chart-box{
    min-height:320px;
    display:flex;
    flex-direction:column;
    align-items:center;
    justify-content:center;
    text-align:center;
}

.blank-chart-box i{
    font-size:60px;
    margin-bottom:18px;
    color:#cbd5e1;
}

.blank-chart-box h5{
    font-size:22px;
    font-weight:700;
    margin-bottom:10px;
    color:#475569;
}

.blank-chart-box p{
    color:#94a3b8;
    font-size:14px;
    max-width:260px;
}

/* Loading overlay — replaced with slim top progress bar */
.loading-overlay{
    display: none; /* kept for compat, not used as fullscreen */
}

/* ===== SLIM PROGRESS BAR ===== */
#misProgressBar {
    position: fixed;
    top: 0; left: 0;
    height: 3px;
    width: 0%;
    background: linear-gradient(90deg, #004392, #38bdf8, #004392);
    background-size: 200% 100%;
    z-index: 9999999;
    transition: width 0.3s ease;
    animation: progressShimmer 1.2s linear infinite;
    border-radius: 0 2px 2px 0;
    box-shadow: 0 0 8px rgba(0,67,146,0.6);
    display: none;
}
@keyframes progressShimmer {
    0%   { background-position: 200% 0; }
    100% { background-position: -200% 0; }
}

/* Stat number counter animation */
.stat-updating {
    opacity: 0.45;
    transition: opacity 0.2s;
}
.stat-updated {
    opacity: 1;
    transition: opacity 0.3s;
}

.loading-spinner{
    background:#fff;
    padding:20px 30px;
    border-radius:12px;
    display:flex;
    gap:12px;
    align-items:center;
}

.loading-spinner i{
    font-size:24px;
    animation:spin 1s linear infinite;
}

@keyframes spin{
    0%{ transform:rotate(0deg); }
    100%{ transform:rotate(360deg); }
}

.mis-tabs .btn-info {
    /*background: #16a34a !important;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
}
.mis-tabs .btn-info:hover {
    /*background: #15803d !important;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    transform: translateY(-2px);
}
.mis-tabs .btn-warning {
    /*background: #16a34a !important;  */
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
}
.mis-tabs .btn-warning:hover {
    /*background: #15803d !important;*/
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    transform: translateY(-2px);
}
.mis-tabs a,
.mis-tabs button {
    display: flex;
    align-items: center;
    gap: 8px;
}
.mis-tabs a i,
.mis-tabs button i {
    font-size: 15px;
}

.main-type-dropdown-menu {
    bottom: 0%;
    top: auto;
    left: 185px;
}

.service-sub-dropdown-menu {
    top: -145%;
    left: 110%;
}

/* =========================================
RESPONSIVE
========================================= */

@media(max-width:991px){

    .service-sub-dropdown-menu{
        position:relative;
        left:0;
        top:10px;
        width:100%;
    }

    .main-type-dropdown-menu{
        width:100%;
    }

}

@media(max-width:767px){

    .mis-wrapper{
        padding:15px;
    }

    .page-title{
        font-size:22px;
    }

    .mis-card h2{
        font-size:30px;
    }

    .chart-title{
        font-size:18px;
    }

}

/* =========================================
   REFERRAL SETTINGS MODAL
   ========================================= */
.ref-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(8px);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 999999;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.ref-modal-overlay.show {
    display: flex;
    opacity: 1;
}

.ref-modal-card {
    background: #ffffff;
    border-radius: 20px;
    width: 100%;
    max-width: 500px;
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    overflow: hidden;
    transform: translateY(20px);
    transition: transform 0.3s ease;
    border: 1px solid #e2e8f0;
}

.ref-modal-overlay.show .ref-modal-card {
    transform: translateY(0);
}

.ref-modal-header {
    background: #f0f7ff;
    padding: 20px 24px;
    border-bottom: 1px solid #e0f2fe;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.ref-modal-title {
    font-size: 18px;
    font-weight: 800;
    color: #0369a1;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ref-modal-title i {
    font-size: 20px;
}

.ref-close-btn {
    background: transparent;
    border: none;
    font-size: 20px;
    color: #64748b;
    cursor: pointer;
    transition: 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
    width: 32px;
    height: 32px;
    border-radius: 50%;
}

.ref-close-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
}

.ref-modal-body {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.ref-form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.ref-form-label {
    font-weight: 700;
    font-size: 14px;
    color: #1e293b;
    margin: 0;
}

/* Slide toggle */
.ref-toggle-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: #f8fafc;
    padding: 14px 18px;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.ref-toggle-label {
    font-weight: 700;
    font-size: 15px;
    color: #0f172a;
}

.ref-switch {
    position: relative;
    display: inline-block;
    width: 58px;
    height: 28px;
}

.ref-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}

.ref-slider {
    position: absolute;
    cursor: pointer;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-color: #cbd5e1;
    transition: .4s;
    border-radius: 34px;
}

.ref-slider:before {
    position: absolute;
    content: "";
    height: 20px;
    width: 20px;
    left: 4px;
    bottom: 4px;
    background-color: white;
    transition: .4s;
    border-radius: 50%;
}

.ref-switch input:checked + .ref-slider {
    background-color: #16a34a;
}

.ref-switch input:checked + .ref-slider:before {
    transform: translateX(30px);
}

.ref-slider-text {
    position: absolute;
    right: 8px;
    top: 5px;
    font-size: 10px;
    font-weight: 800;
    color: #475569;
    pointer-events: none;
    text-transform: uppercase;
}

.ref-switch input:checked + .ref-slider .ref-slider-text {
    color: #ffffff;
    right: auto;
    left: 8px;
    content: "ON";
}

/* Radio Group */
.ref-radio-group {
    display: flex;
    gap: 16px;
}

.ref-radio-option {
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 600;
    color: #475569;
}

.ref-radio-option input[type="radio"] {
    width: 18px;
    height: 18px;
    accent-color: #0284c7;
}

/* Input Fields row */
.ref-inputs-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}

.ref-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.ref-input-prefix, .ref-input-suffix {
    position: absolute;
    background: #f1f5f9;
    color: #475569;
    padding: 0 12px;
    height: 100%;
    display: flex;
    align-items: center;
    font-weight: 700;
    border: 1px solid #cbd5e1;
    font-size: 14px;
}

.ref-input-prefix {
    left: 0;
    border-radius: 8px 0 0 8px;
    border-right: none;
}

.ref-input-suffix {
    right: 0;
    border-radius: 0 8px 8px 0;
    border-left: none;
}

.ref-input-field {
    width: 100%;
    padding: 10px 14px;
    font-size: 15px;
    font-weight: 600;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    transition: 0.2s;
}

.ref-input-field:focus {
    border-color: #0284c7;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.15);
}

.ref-input-wrapper.has-prefix .ref-input-field {
    padding-left: 36px;
}

.ref-input-wrapper.has-suffix .ref-input-field {
    padding-right: 36px;
}

/* Generate Link Section */
.ref-generate-btn {
    background: #0284c7;
    color: #ffffff;
    border: none;
    padding: 12px 18px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    cursor: pointer;
    transition: 0.2s;
    width: 100%;
}

.ref-generate-btn:hover {
    background: #0369a1;
}

.ref-preview-box {
    display: none;
    background: #f0fdf4;
    border: 1px solid #bbf7d0;
    padding: 14px;
    border-radius: 12px;
    flex-direction: column;
    gap: 8px;
}

.ref-preview-title {
    font-size: 12px;
    font-weight: 800;
    color: #15803d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ref-preview-link-sec {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
}

.ref-preview-link {
    font-size: 13px;
    font-weight: 700;
    color: #166534;
    word-break: break-all;
    text-decoration: underline;
}

.ref-copy-btn {
    background: #ffffff;
    border: 1px solid #bbf7d0;
    color: #16a34a;
    width: 32px;
    height: 32px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: 0.2s;
}

.ref-copy-btn:hover {
    background: #dcfce7;
}

.ref-helper-text {
    font-size: 12px;
    color: #64748b;
    line-height: 1.5;
    margin: 0;
}

.ref-modal-footer {
    padding: 18px 24px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    background: #f8fafc;
}

.ref-btn-save {
    /*background: #16a34a;*/
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    color: #ffffff;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}

.ref-btn-save:hover {
    /*background: #15803d;*/
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
}

.ref-btn-cancel {
    background: #e2e8f0;
    color: #475569;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    transition: 0.2s;
}

.ref-btn-cancel:hover {
    background: #cbd5e1;
}

/* Custom Alert Toast */
.ref-toast {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #0f172a;
    color: #ffffff;
    padding: 14px 24px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 10px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    z-index: 9999999;
    transform: translateY(100px);
    opacity: 0;
    transition: 0.3s ease;
}

.ref-toast.show {
    transform: translateY(0);
    opacity: 1;
}

.ref-toast i {
    color: #22c55e;
}

/* Floating top corner button styling */
.ref-floating-btn {
    position: absolute;
    top: 25px;
    right: 25px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #ffffff !important;
    border: none;
    border-radius: 50px;
    padding: 11px 22px;
    font-weight: 700;
    font-size: 13px;
    box-shadow: 0 10px 15px -3px rgba(217, 119, 6, 0.3), 0 4px 6px -2px rgba(217, 119, 6, 0.15);
    display: flex;
    align-items: center;
    gap: 8px;
    z-index: 999;
    cursor: pointer;
    text-decoration: none !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    animation: refPulse 2s infinite;
}

.ref-floating-btn:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 15px 20px -5px rgba(217, 119, 6, 0.4), 0 8px 8px -5px rgba(217, 119, 6, 0.2);
    background: linear-gradient(135deg, #fbbf24, #d97706);
}

.ref-floating-btn i {
    font-size: 16px;
}

@keyframes refPulse {
    0% {
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.7);
    }
    70% {
        box-shadow: 0 0 0 15px rgba(245, 158, 11, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(245, 158, 11, 0);
    }
}

.mis-card {
    border: 1px solid rgb(201 208 218) !important;
}
.chart-card {
    border: 1px solid rgb(201 208 218) !important;
}
.mis-wrapper {
    padding: 15px;
}

</style>

<div class="container-fluid mis-wrapper">

    {{-- SUCCESS TOAST --}}
    <div class="ref-toast" id="refToast">
        <i class="fa-solid fa-circle-check"></i>
        <span id="refToastMessage">Settings saved successfully!</span>
    </div>

    {{-- SLIM PROGRESS BAR (smooth AJAX indicator) --}}
    <div id="misProgressBar"></div>

    {{-- Loading Overlay (hidden — replaced by progress bar) --}}
    <div class="loading-overlay" id="loadingOverlay" style="display:none;"></div>

    {{-- HEADER --}}
    <div class="mis-header-new">

        <div class="header-left">

            <div class="top-back-heading-sec">

                <button onclick="goBack()" class="nav-btn">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>

                <h4 class="page-title">

                    @if($moduleType == 'both')

                        Products / Services MIS

                    @elseif($moduleType == 'service')

                        Service MIS

                    @elseif($moduleType == 'course')

                        Course MIS

                    @else

                        Product MIS

                    @endif

                </h4>

            </div>

            <div class="mis-tabs">

                {{-- MIS DASHBOARD --}}
                <a href="{{ route('seller.products.mis.dashboard') }}"
                   class="btn btn-success">

                    MIS Dashboard

                </a>

                {{-- LIST BUTTON WITH FILTER PRESERVATION --}}
                @if($moduleType == 'both')

                    <a href="javascript:void(0)"
                       onclick="goToListWithFilters()"
                       class="btn btn-info">

                        <i class="fa-solid fa-list"></i>
                        Go to List

                    </a>

                @else

                    <a href="{{ route('seller.products.index') }}"
                       class="btn btn-info">

                        <i class="fa-solid fa-list"></i>

                        @if($moduleType == 'service')

                            Service List

                        @elseif($moduleType == 'course')

                            Course List

                        @else

                            Product List

                        @endif

                    </a>

                @endif

                {{-- CREATE BUTTON --}}
                <a href="{{ route('seller.products.basic.save') }}"
                   class="btn btn-success">

                    <i class="fa-solid fa-plus"></i>

                    @if($moduleType == 'both')

                        Create Product / Service

                    @elseif($moduleType == 'service')

                        Create Service

                    @elseif($moduleType == 'course')

                        Create Course

                    @else

                        Create Product

                    @endif

                </a>

                {{-- FILTER BUTTON FOR BOTH MODULE --}}
                @if($moduleType == 'both')
                    
                    <div class="main-type-dropdown-wrapper">

                        <button type="button"
                                class="btn btn-warning main-type-btn"
                                onclick="toggleMainTypeDropdown(event)">

                            <i class="fa-solid fa-filter"></i>
                            Type

                            <i class="fa fa-angle-down"></i>

                        </button>

                        <div class="main-type-dropdown-menu"
                             id="mainTypeDropdownMenu">

                            {{-- PRODUCT OPTION --}}
                            <a href="javascript:void(0)"
                               onclick="applyProductFilter()"
                               class="main-type-link">

                                <i class="fa-solid fa-box"></i>
                                Products

                            </a>

                            {{-- SERVICE OPTION WITH SUBTYPES --}}
                            <div class="service-sub-dropdown-wrapper">

                                <button type="button"
                                        class="service-sub-btn"
                                        onclick="toggleServiceSubDropdown(event)">

                                    <span><i class="fa-solid fa-briefcase"></i> Services</span>

                                    <i class="fa fa-angle-right"></i>

                                </button>

                                <div class="service-sub-dropdown-menu"
                                     id="serviceSubDropdownMenu">

                                    <div class="service-checkbox-list">

                                        {{-- PROFESSIONAL --}}
                                        <label class="service-check-card {{ in_array('professional', $selectedServiceTypes) ? 'active' : '' }}">

                                            <input type="checkbox"
                                                   class="serviceTypeCheckbox"
                                                   value="professional"
                                                   {{ in_array('professional', $selectedServiceTypes) ? 'checked' : '' }}>

                                            <div class="service-check-title">
                                                Professional
                                            </div>

                                        </label>

                                        {{-- TOUR --}}
                                        <label class="service-check-card {{ in_array('tour_guide', $selectedServiceTypes) ? 'active' : '' }}">

                                            <input type="checkbox"
                                                   class="serviceTypeCheckbox"
                                                   value="tour_guide"
                                                   {{ in_array('tour_guide', $selectedServiceTypes) ? 'checked' : '' }}>

                                            <div class="service-check-title">
                                                Tour Guidance
                                            </div>

                                        </label>

                                        {{-- INFORMATION & ADVISORY --}}
                                        <label class="service-check-card {{ in_array('information_advisory', $selectedServiceTypes) ? 'active' : '' }}">

                                            <input type="checkbox"
                                                   class="serviceTypeCheckbox"
                                                   value="information_advisory"
                                                   {{ in_array('information_advisory', $selectedServiceTypes) ? 'checked' : '' }}>

                                            <div class="service-check-title">
                                                Information & Advisory
                                            </div>

                                        </label>

                                        {{-- OTHERS --}}
                                        <label class="service-check-card {{ in_array('others', $selectedServiceTypes) ? 'active' : '' }}">

                                            <input type="checkbox"
                                                   class="serviceTypeCheckbox"
                                                   value="others"
                                                   {{ in_array('others', $selectedServiceTypes) ? 'checked' : '' }}>

                                            <div class="service-check-title">
                                                Others
                                            </div>

                                        </label>

                                    </div>

                                    <div class="filter-actions">

                                        <button type="button"
                                                class="go-btn"
                                                onclick="applyServiceFilter()">

                                            <i class="fa-solid fa-check"></i> Apply

                                        </button>

                                    </div>

                                </div>

                            </div>

                        </div>

                    </div>

                    {{-- Active Filters Display --}}
                    @if(!empty($selectedServiceTypes))
                        <div class="filter-badge">
                            <i class="fa-solid fa-filter"></i>
                            Active: {{ implode(', ', $selectedServiceTypes) }}
                            <i class="fa-solid fa-times-circle" onclick="resetServiceFilter()"></i>
                        </div>
                    @endif

                @endif

            </div>

        </div>

    </div>

    {{-- STATS CARDS --}}
    <div class="row stats-wrapper">

        {{-- PRODUCTS CARD --}}
        @if($moduleType == 'product' || $moduleType == 'both')

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="mis-card" data-card-type="products">

                <h2 id="totalProducts">{{ $totalProducts }}</h2>

                <p>

                    <span>Total Products</span>

                    <span onclick="toggleMenu(event,this)">▼</span>

                </p>

                <div class="dropdown-menu-custom">

                    <a href="javascript:void(0)" onclick="navigateWithFilter('approved', 'product')">
                        <span>Verified</span>
                        <span id="verifiedProducts">{{ $verifiedProducts }}</span>
                    </a>

                    <a href="javascript:void(0)" onclick="navigateWithFilter('pending', 'product')">
                        <span>Not Verified</span>
                        <span id="notVerifiedProducts">{{ $notVerifiedProducts }}</span>
                    </a>

                </div>

            </div>

        </div>

        @endif

        {{-- SERVICES CARD --}}
        @if($moduleType == 'service' || $moduleType == 'both')

        <div class="col-lg-3 col-md-6 mb-4">

            <div class="mis-card" data-card-type="services">

                <h2 id="totalServices">{{ $totalServices }}</h2>

                <p>

                    <span>Total Services</span>

                    <span onclick="toggleMenu(event,this)">▼</span>

                </p>

                <div class="dropdown-menu-custom">

                    <a href="javascript:void(0)" onclick="navigateWithFilter('approved', 'service')">
                        <span>Verified</span>
                        <span id="verifiedServices">{{ $verifiedServices }}</span>
                    </a>

                    <a href="javascript:void(0)" onclick="navigateWithFilter('pending', 'service')">
                        <span>Pending</span>
                        <span id="pendingServices">{{ $pendingServices }}</span>
                    </a>

                </div>

            </div>

        </div>

        @endif

        {{-- RATINGS CARD --}}
        <div class="col-lg-3 col-md-6 mb-4">

            <div class="mis-card">

                <h2 id="totalRatings">{{ $totalRatings }}</h2>

                <p>

                    <span>Total Ratings</span>

                    <span onclick="toggleMenu(event,this)">▼</span>

                </p>

                <div class="dropdown-menu-custom">

                    <a href="{{ route('seller.products.reviews',['status'=>'4+']) }}">
                        <span>4+ Ratings</span>
                        <span id="rating4plus">{{ $rating4plus }}</span>
                    </a>

                    <a href="{{ route('seller.products.reviews',['status'=>'below3']) }}">
                        <span>Below 3</span>
                        <span id="rating3below">{{ $rating3below }}</span>
                    </a>

                </div>

            </div>

        </div>

        {{-- REVIEWS CARD --}}
        <div class="col-lg-3 col-md-6 mb-4">

            <div class="mis-card">

                <h2 id="totalReviews">{{ $totalReviews }}</h2>

                <p>

                    <span>Total Reviews</span>

                    <span onclick="toggleMenu(event,this)">▼</span>

                </p>

                <div class="dropdown-menu-custom">

                    <a href="{{ route('seller.products.reviews',['status'=>'good']) }}">
                        <span>Good Reviews</span>
                        <span id="goodReviews">{{ $goodReviews }}</span>
                    </a>

                    <a href="{{ route('seller.products.reviews',['status'=>'bad']) }}">
                        <span>Bad Reviews</span>
                        <span id="badReviews">{{ $badReviews }}</span>
                    </a>

                </div>

            </div>

        </div>

    </div>

    {{-- TOP LISTINGS & BOOST ROW --}}
    <div class="row mb-4">
        {{-- MOST VIEWED LISTINGS --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="chart-card p-3 d-flex flex-column" style="min-height: 420px; border-radius: 16px; background: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s, box-shadow 0.2s;">
                <div class="chart-header-flex mb-3 d-flex justify-content-between align-items-center">
                    <div class="chart-title d-flex align-items-center gap-2" style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                        <span>Most Viewed Listings</span>
                        <i class="fa-solid fa-circle-info text-muted" title="Top 5 listings with most views" style="font-size: 13px; cursor: help;"></i>
                    </div>
                    <div class="text-muted small fw-bold">Total Views</div>
                </div>

                <div class="d-flex flex-column gap-3 flex-grow-1 justify-content-start mt-2">
                    @if($mostViewedListings->isNotEmpty())
                    @php
                        $maxViews = $mostViewedListings->max('views') ?: 1;
                    @endphp
                    @foreach($mostViewedListings as $listing)
                        @php
                            $width = max(($listing->views / $maxViews) * 100, 35);
                            $thumbnail = !empty($listing->thumbnail) ? (preg_match('/^https?:\/\//', $listing->thumbnail) ? $listing->thumbnail : asset($listing->thumbnail)) : 'https://placehold.co/100x100?text=Listing';
                        @endphp
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $thumbnail }}" 
                                 alt="{{ $listing->name }}" 
                                 class="rounded-circle" 
                                 style="width: 44px; height: 44px; object-fit: cover; border: 2px solid #f1f5f9; flex-shrink: 0;"
                                 onerror="this.src='https://placehold.co/100x100?text=Listing'">
                            
                            <div class="flex-grow-1 position-relative" style="height: 36px; background: #f1f5f9; border-radius: 8px; overflow: hidden;" title="Views: {{ $listing->views }}">
                                <div class="h-100 d-flex align-items-center px-3" 
                                     style="width: {{ $width }}%; background: #095982; border-radius: 8px 0 0 8px; transition: width 0.6s ease;">
                                     <span class="text-white text-truncate fw-semibold" style="font-size: 12px; max-width: 100%;">
                                         {{ $listing->name }}
                                     </span>
                                </div>
                            </div>
                            
                            <div class="fw-bold text-end text-muted" style="width: 50px; font-size: 14px;">
                                {{ $listing->views }}
                            </div>
                        </div>
                    @endforeach
                    @else
                    <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1 text-center py-4">
                        <i class="fa-solid fa-eye-slash" style="font-size: 40px; color: #cbd5e1; margin-bottom: 12px;"></i>
                        <h6 style="font-weight: 700; color: #64748b; margin-bottom: 4px;">No Views Yet</h6>
                        <p style="font-size: 13px; color: #94a3b8; margin: 0;">Views will appear once your listings get traffic.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- MOST ENQUIRED LISTINGS --}}
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="chart-card p-3 d-flex flex-column" style="min-height: 420px; border-radius: 16px; background: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s, box-shadow 0.2s;">
                <div class="chart-header-flex mb-3 d-flex justify-content-between align-items-center">
                    <div class="chart-title d-flex align-items-center gap-2" style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                        <span>Most Enquired Listings</span>
                        <i class="fa-solid fa-circle-info text-muted" title="Top 5 listings with most enquiries" style="font-size: 13px; cursor: help;"></i>
                    </div>
                    <div class="text-muted small fw-bold">Enquiry Count</div>
                </div>

                <div class="d-flex flex-column gap-3 flex-grow-1 justify-content-start mt-2">
                    @if($mostEnquiredListings->isNotEmpty())
                    @php
                        $maxEnquiries = $mostEnquiredListings->max('total_enquiries') ?: 1;
                    @endphp
                    @foreach($mostEnquiredListings as $listing)
                        @php
                            $width = max(($listing->total_enquiries / $maxEnquiries) * 100, 35);
                            $productName = $listing->product_service;
                            $thumbnail = !empty($listing->thumbnail) ? (preg_match('/^https?:\/\//', $listing->thumbnail) ? $listing->thumbnail : asset($listing->thumbnail)) : 'https://placehold.co/100x100?text=Listing';
                        @endphp
                        <div class="d-flex align-items-center gap-3">
                            <img src="{{ $thumbnail }}" 
                                 alt="{{ $productName }}" 
                                 class="rounded-circle" 
                                 style="width: 44px; height: 44px; object-fit: cover; border: 2px solid #f1f5f9; flex-shrink: 0;"
                                 onerror="this.src='https://placehold.co/100x100?text=Listing'">
                            
                            <div class="flex-grow-1 position-relative" style="height: 36px; background: #f1f5f9; border-radius: 8px; overflow: hidden;" title="Enquiry Count: {{ $listing->total_enquiries }}">
                                <div class="h-100 d-flex align-items-center px-3" 
                                     style="width: {{ $width }}%; background: #095982; border-radius: 8px 0 0 8px; transition: width 0.6s ease;">
                                     <span class="text-white text-truncate fw-semibold" style="font-size: 12px; max-width: 100%;">
                                         {{ $productName }}
                                     </span>
                                </div>
                            </div>
                            
                            <div class="fw-bold text-end text-muted" style="width: 50px; font-size: 14px;">
                                {{ $listing->total_enquiries }}
                            </div>
                        </div>
                    @endforeach
                    @else
                    <div class="d-flex flex-column align-items-center justify-content-center flex-grow-1 text-center py-4">
                        <i class="fa-solid fa-envelope-open" style="font-size: 40px; color: #cbd5e1; margin-bottom: 12px;"></i>
                        <h6 style="font-weight: 700; color: #64748b; margin-bottom: 4px;">No Enquiries Yet</h6>
                        <p style="font-size: 13px; color: #94a3b8; margin: 0;">Enquiries will appear once customers reach out.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- BOOST PERFORMANCE GRAPH --}}
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="chart-card p-3 d-flex flex-column" style="min-height: 420px; border-radius: 16px; background: #ffffff; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05); transition: transform 0.2s, box-shadow 0.2s;">
                <div class="chart-header-flex mb-2 d-flex justify-content-between align-items-center">
                    <div class="chart-title" style="font-size: 16px; font-weight: 700; color: #1e293b; margin: 0;">
                        Boost Performance Graph
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-link p-0 text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="border: none; background: transparent;">
                            <i class="fa-solid fa-ellipsis-vertical"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex-grow-1 d-flex align-items-center justify-content-center" style="position: relative; width: 100%;">
                    <div id="boostPerformanceChart" style="width: 100%; height: 320px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- CHARTS SECTION --}}
    <div class="row mt-2">

        {{-- SALES CHART --}}
        <div class="col-lg-6 mb-4">

            <div class="chart-card">

                <div class="chart-header-flex">

                    <div>

                        <div class="chart-title">
                            Sales vs Returns
                        </div>

                        <small class="chart-subtitle">
                            Seller performance analytics
                        </small>

                    </div>

                   

                </div>

                @if($isNewSeller)

                    <div class="blank-chart-box">

                        <i class="fa-solid fa-chart-column"></i>

                        <h5>No Sales Data Available</h5>

                        <p>
                            Graph will automatically appear after seller activity.
                        </p>

                    </div>

                @else

                    <div id="salesChart" style="height:320px;"></div>

                @endif

            </div>

        </div>

        {{-- RATINGS CHART --}}
        <div class="col-lg-6 mb-4">

            <div class="chart-card">

                <div class="chart-header-flex">

                    <div>

                        <div class="chart-title">
                            Ratings Overview
                        </div>

                        <small class="chart-subtitle">
                            Customer rating analytics
                        </small>

                    </div>

                    
                </div>

                @if($isNewSeller)

                    <div class="blank-chart-box">

                        <i class="fa-solid fa-star"></i>

                        <h5>No Ratings Yet</h5>

                        <p>
                            Rating graph will appear after customer reviews.
                        </p>

                    </div>

                @else

                    <div id="ratingChart" style="height:320px;"></div>

                @endif

            </div>

        </div>

        {{-- BAD REVIEWS CHART --}}
        <div class="col-lg-6 mb-4">

            <div class="chart-card">

                <div class="chart-header-flex">

                    <div>

                        <div class="chart-title">
                            Bad Reviews Distribution
                        </div>

                        <small class="chart-subtitle">
                            Negative review monitoring
                        </small>

                    </div>

                
                </div>

                @if($isNewSeller)

                    <div class="blank-chart-box">

                        <i class="fa-solid fa-comment-slash"></i>

                        <h5>No Reviews Found</h5>

                        <p>
                            Review graph will appear here.
                        </p>

                    </div>

                @else

                    <div id="badChart" style="height:320px;"></div>

                @endif

            </div>

        </div>

        {{-- GRIEVANCE CHART --}}
        <div class="col-lg-6 mb-4">

            <div class="chart-card">

                <div class="chart-header-flex">

                    <div>

                        <div class="chart-title">
                            Grievance Analytics
                        </div>

                        <small class="chart-subtitle">
                            Complaint analytics
                        </small>

                    </div>

                    
                </div>

                @if($isNewSeller)

                    <div class="blank-chart-box">

                        <i class="fa-solid fa-circle-exclamation"></i>

                        <h5>No Grievance Data</h5>

                        <p>
                            Complaint analytics will appear here.
                        </p>

                    </div>

                @else

                    <div id="grievanceChart" style="height:320px;"></div>

                @endif

            </div>

        </div>

    </div>

</div>

{{-- ECHART LIBRARY --}}
<script src="https://cdn.jsdelivr.net/npm/echarts/dist/echarts.min.js"></script>

<script>

// Chart instances
let salesChart, ratingChart, badChart, grievanceChart, boostPerformanceChart;

// Store current filters
let currentServiceFilters = @json($selectedServiceTypes);

/* =========================================
GO BACK FUNCTION
========================================= */

function goBack(){
    window.history.back();
}

/* =========================================
NAVIGATE WITH FILTERS
========================================= */

function navigateWithFilter(status, type){
    let url = "{{ route('seller.products.index') }}";
    let params = [];
    
    if(status){
        params.push('status=' + status);
    }
    
    if(type){
        params.push('type=' + type);
    }
    
    if(currentServiceFilters.length > 0){
        params.push('service_types=' + currentServiceFilters.join(','));
    }
    
    if(params.length > 0){
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

/* =========================================
GO TO LIST WITH FILTERS
========================================= */

function goToListWithFilters(){
    let url = "{{ route('seller.products.index') }}";
    let params = [];
    
    // Add service type filters if any
    if(currentServiceFilters.length > 0){
        params.push('service_types=' + currentServiceFilters.join(','));
        params.push('type=service'); // Specify service type when filters are active
    }
    
    if(params.length > 0){
        url += '?' + params.join('&');
    }
    
    window.location.href = url;
}

/* =========================================
APPLY PRODUCT FILTER
========================================= */

function applyProductFilter(){
    // Reset service filters
    currentServiceFilters = [];
    
    // Reset checkboxes
    document.querySelectorAll('.serviceTypeCheckbox').forEach(cb => {
        cb.checked = false;
        cb.closest('.service-check-card').classList.remove('active');
    });
    
    // Reload page with product filter (Items only)
    reloadWithFilters('product');
}

/* =========================================
CARD DROPDOWN TOGGLE
========================================= */

function toggleMenu(e, el){
    e.preventDefault();
    e.stopPropagation();

    let currentCard = el.closest('.mis-card');
    let currentMenu = currentCard ? currentCard.querySelector('.dropdown-menu-custom') : null;
    
    if(!currentMenu) return;

    document.querySelectorAll('.dropdown-menu-custom')
        .forEach(menu => {
            if(menu !== currentMenu){
                menu.style.display = 'none';
                let parentCard = menu.closest('.mis-card');
                if(parentCard) parentCard.classList.remove('dropdown-active');
            }
        });

    if (currentMenu.style.display === 'block') {
        currentMenu.style.display = 'none';
        if(currentCard) currentCard.classList.remove('dropdown-active');
    } else {
        currentMenu.style.display = 'block';
        if(currentCard) currentCard.classList.add('dropdown-active');
    }
}

/* =========================================
MAIN TYPE DROPDOWN TOGGLE
========================================= */

function toggleMainTypeDropdown(e){
    e.preventDefault();
    e.stopPropagation();

    let mainMenu =
        document.getElementById('mainTypeDropdownMenu');

    let subMenu =
        document.getElementById('serviceSubDropdownMenu');

    if(subMenu){
        subMenu.style.display = 'none';
    }

    mainMenu.style.display =
        mainMenu.style.display === 'block'
        ? 'none'
        : 'block';
}

/* =========================================
SERVICE SUB DROPDOWN TOGGLE
========================================= */

function toggleServiceSubDropdown(e){
    e.preventDefault();
    e.stopPropagation();

    let subMenu =
        document.getElementById('serviceSubDropdownMenu');

    subMenu.style.display =
        subMenu.style.display === 'block'
        ? 'none'
        : 'block';
}

/* =========================================
SERVICE CARD ACTIVE STATE
========================================= */

document.querySelectorAll('.service-check-card')
    .forEach(card => {
        let checkbox = card.querySelector('input[type="checkbox"]');
        
        if(checkbox){
            checkbox.addEventListener('change', function(){
                if(this.checked){
                    card.classList.add('active');
                }else{
                    card.classList.remove('active');
                }
            });
        }
    });

/* =========================================
RESET SERVICE FILTER
========================================= */

function resetServiceFilter(){
    // Clear all checkboxes
    document.querySelectorAll('.serviceTypeCheckbox').forEach(cb => {
        cb.checked = false;
        cb.closest('.service-check-card').classList.remove('active');
    });
    
    // Reset current filters
    currentServiceFilters = [];
    
    // Reload without filters
    reloadWithFilters('reset');
}

/* =========================================
APPLY SERVICE FILTER
========================================= */

function applyServiceFilter(){
    let checked = [];
    
    document.querySelectorAll('.serviceTypeCheckbox:checked')
        .forEach(el => {
            checked.push(el.value);
        });
    
    if(checked.length === 0){
        alert('Please select at least one service type');
        return;
    }
    
    currentServiceFilters = checked;
    reloadWithFilters('service');
}

/* =========================================
PROGRESS BAR HELPERS
========================================= */

function showProgress() {
    const bar = document.getElementById('misProgressBar');
    if (!bar) return;
    bar.style.display = 'block';
    bar.style.opacity  = '1';
    bar.style.width = '0%';
    requestAnimationFrame(() => {
        bar.style.transition = 'width 0.4s ease';
        bar.style.width = '30%';
        setTimeout(() => { bar.style.width = '65%'; }, 400);
        setTimeout(() => { bar.style.width = '80%'; }, 900);
    });
}
function hideProgress() {
    const bar = document.getElementById('misProgressBar');
    if (!bar) return;
    bar.style.transition = 'width 0.2s ease';
    bar.style.width = '100%';
    setTimeout(() => {
        bar.style.transition = 'opacity 0.3s';
        bar.style.opacity = '0';
        setTimeout(() => {
            bar.style.display = 'none';
            bar.style.width  = '0%';
            bar.style.opacity = '1';
        }, 350);
    }, 200);
}

/* =========================================
RELOAD WITH CURRENT FILTERS (AJAX)
========================================= */

function reloadWithFilters(filterType = 'all'){
    showProgress();
    document.querySelectorAll('.mis-card h2, .mis-card span[id]').forEach(el => el.classList.add('stat-updating'));

    let query = '';
    
    if(filterType === 'product') {
        query = 'type=product';
    } else if(filterType === 'service') {
        query = currentServiceFilters.join(',');
        if(query) {
            query = 'service_types=' + query;
        }
    } else if(filterType === 'reset') {
        query = '';
    } else {
        query = currentServiceFilters.join(',');
        if(query) {
            query = 'service_types=' + query;
        }
    }
    
    let url = "{{ route('seller.products.mis.dashboard') }}";
    
    if(query){
        url += '?' + query;
    }
    
    fetch(url, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(res => res.json())
    .then(data => {
        // Update stats
        updateStats(data.stats);
        
        // Update charts
        updateCharts(data.charts);
        
        // Update filter badge
        updateFilterBadge();
        
        // Update most viewed and most enquired listings if data available
        if(data.mostViewedListings) {
            updateMostViewedListings(data.mostViewedListings);
        }
        if(data.mostEnquiredListings) {
            updateMostEnquiredListings(data.mostEnquiredListings);
        }
        if(data.boostChartData) {
            updateBoostChart(data.boostChartData);
        }
        
        // Restore stat numbers
        document.querySelectorAll('.mis-card h2, .mis-card span[id]').forEach(el => {
            el.classList.remove('stat-updating');
            el.classList.add('stat-updated');
            setTimeout(() => el.classList.remove('stat-updated'), 400);
        });

        // Hide progress bar
        hideProgress();
        
        // Close dropdowns
        let mainMenu = document.getElementById('mainTypeDropdownMenu');
        let subMenu = document.getElementById('serviceSubDropdownMenu');
        if(mainMenu) mainMenu.style.display = 'none';
        if(subMenu) subMenu.style.display = 'none';
    })
    .catch(err => {
        console.error('Error:', err);
        hideProgress();
        // Remove dimming
        document.querySelectorAll('.mis-card h2, .mis-card span[id]').forEach(el => el.classList.remove('stat-updating'));
    });
}

/* =========================================
UPDATE STATS FUNCTION
========================================= */

function updateStats(stats){
    // Update Products
    if(document.getElementById('totalProducts')){
        document.getElementById('totalProducts').innerText = stats.totalProducts || 0;
        document.getElementById('verifiedProducts').innerText = stats.verifiedProducts || 0;
        document.getElementById('notVerifiedProducts').innerText = stats.notVerifiedProducts || 0;
    }
    
    // Update Services
    if(document.getElementById('totalServices')){
        document.getElementById('totalServices').innerText = stats.totalServices || 0;
        document.getElementById('verifiedServices').innerText = stats.verifiedServices || 0;
        document.getElementById('pendingServices').innerText = stats.pendingServices || 0;
    }
    
    // Update Ratings
    if(document.getElementById('totalRatings')){
        document.getElementById('totalRatings').innerText = stats.totalRatings || 0;
        document.getElementById('rating4plus').innerText = stats.rating4plus || 0;
        document.getElementById('rating3below').innerText = stats.rating3below || 0;
    }
    
    // Update Reviews
    if(document.getElementById('totalReviews')){
        document.getElementById('totalReviews').innerText = stats.totalReviews || 0;
        document.getElementById('goodReviews').innerText = stats.goodReviews || 0;
        document.getElementById('badReviews').innerText = stats.badReviews || 0;
    }
}

/* =========================================
UPDATE CHARTS FUNCTION
========================================= */

function updateCharts(charts){
    // Update Sales Chart
    if(salesChart && charts.sales && charts.returns){
        salesChart.setOption({
            series: [
                { data: charts.sales },
                { data: charts.returns }
            ]
        });
    }
    
    // Update Rating Chart
    if(ratingChart && charts.rating){
        ratingChart.setOption({
            series: [{
                data: [
                    {
                        value: charts.rating.good || 0,
                        name: 'Top Ratings',
                        itemStyle: { color: '#16a34a' },
                        label: { show: false }
                    },
                    {
                        value: charts.rating.bad || 0,
                        name: 'Low Ratings',
                        itemStyle: { color: '#ef4444' },
                        label: { show: false }
                    }
                ],
                label: { show: false }
            }],
            legend: { show: false }
        });
    }
    
    // Update Bad Reviews Chart
    if(badChart && charts.bad_reviews){
        badChart.setOption({
            series: [{
                data: charts.bad_reviews
            }]
        });
    }
    
    // Update Grievance Chart
    if(grievanceChart && charts.grievance){
        grievanceChart.setOption({
            series: [{
                data: charts.grievance
            }]
        });
    }
    
    // Update Boost Chart
    if(boostPerformanceChart && charts.boost){
        boostPerformanceChart.setOption({
            xAxis: [{ data: charts.boost.dates }],
            series: [
                { data: charts.boost.ad_spend },
                { data: charts.boost.conversions }
            ]
        });
    }
}

/* =========================================
UPDATE MOST VIEWED LISTINGS
========================================= */

function updateMostViewedListings(listings) {
    const container = document.querySelector('.col-lg-4 .d-flex.flex-column.gap-3');
    if(!container) return;
    
    let maxViews = Math.max(...listings.map(l => l.views), 1);
    
    let html = '';
    listings.forEach(listing => {
        let width = Math.max((listing.views / maxViews) * 100, 35);
        let thumbnail = listing.thumbnail ? (listing.thumbnail.match(/^https?:\/\//) ? listing.thumbnail : '{{ asset("") }}' + listing.thumbnail) : 'https://placehold.co/100x100?text=Listing';
        
        html += `
            <div class="d-flex align-items-center gap-3">
                <img src="${thumbnail}" 
                     alt="${listing.name}" 
                     class="rounded-circle" 
                     style="width: 44px; height: 44px; object-fit: cover; border: 2px solid #f1f5f9; flex-shrink: 0;"
                     onerror="this.src='https://placehold.co/100x100?text=Listing'">
                
                <div class="flex-grow-1 position-relative" style="height: 36px; background: #f1f5f9; border-radius: 8px; overflow: hidden;" title="Views: ${listing.views}">
                    <div class="h-100 d-flex align-items-center px-3" 
                         style="width: ${width}%; background: #095982; border-radius: 8px 0 0 8px; transition: width 0.6s ease;">
                         <span class="text-white text-truncate fw-semibold" style="font-size: 12px; max-width: 100%;">
                             ${listing.name}
                         </span>
                    </div>
                </div>
                
                <div class="fw-bold text-end text-muted" style="width: 50px; font-size: 14px;">
                    ${listing.views}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/* =========================================
UPDATE MOST ENQUIRED LISTINGS
========================================= */

function updateMostEnquiredListings(listings) {
    const container = document.querySelectorAll('.col-lg-4 .d-flex.flex-column.gap-3')[1];
    if(!container) return;
    
    let maxEnquiries = Math.max(...listings.map(l => l.total_enquiries), 1);
    
    let html = '';
    listings.forEach(listing => {
        let width = Math.max((listing.total_enquiries / maxEnquiries) * 100, 35);
        let productName = listing.product_service;
        let thumbnail = listing.thumbnail ? (listing.thumbnail.match(/^https?:\/\//) ? listing.thumbnail : '{{ asset("") }}' + listing.thumbnail) : 'https://placehold.co/100x100?text=Listing';
        
        html += `
            <div class="d-flex align-items-center gap-3">
                <img src="${thumbnail}" 
                     alt="${productName}" 
                     class="rounded-circle" 
                     style="width: 44px; height: 44px; object-fit: cover; border: 2px solid #f1f5f9; flex-shrink: 0;"
                     onerror="this.src='https://placehold.co/100x100?text=Listing'">
                
                <div class="flex-grow-1 position-relative" style="height: 36px; background: #f1f5f9; border-radius: 8px; overflow: hidden;" title="Enquiry Count: ${listing.total_enquiries}">
                    <div class="h-100 d-flex align-items-center px-3" 
                         style="width: ${width}%; background: #095982; border-radius: 8px 0 0 8px; transition: width 0.6s ease;">
                         <span class="text-white text-truncate fw-semibold" style="font-size: 12px; max-width: 100%;">
                             ${productName}
                         </span>
                    </div>
                </div>
                
                <div class="fw-bold text-end text-muted" style="width: 50px; font-size: 14px;">
                    ${listing.total_enquiries}
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

/* =========================================
UPDATE BOOST CHART
========================================= */

function updateBoostChart(boostData) {
    if(boostPerformanceChart && boostData) {
        boostPerformanceChart.setOption({
            xAxis: [{ data: boostData.dates }],
            series: [
                { data: boostData.ad_spend },
                { data: boostData.conversions }
            ]
        });
    }
}

/* =========================================
UPDATE FILTER BADGE
========================================= */

function updateFilterBadge(){
    let filterContainer = document.querySelector('.mis-tabs');
    let existingBadge = document.querySelector('.filter-badge');
    
    if(existingBadge){
        existingBadge.remove();
    }
    
    if(currentServiceFilters.length > 0){
        let badge = document.createElement('div');
        badge.className = 'filter-badge';
        badge.innerHTML = `
            <i class="fa-solid fa-filter"></i>
            Active: ${currentServiceFilters.join(', ')}
            <i class="fa-solid fa-times-circle" onclick="resetServiceFilter()" style="cursor:pointer;"></i>
        `;
        
        // Insert after the filter button
        let filterWrapper = document.querySelector('.main-type-dropdown-wrapper');
        if(filterWrapper && filterWrapper.parentNode){
            filterWrapper.parentNode.insertBefore(badge, filterWrapper.nextSibling);
        }
    }
}

/* =========================================
OUTSIDE CLICK CLOSE FUNCTIONALITY
========================================= */

document.addEventListener('click', function(e){
    // Close card dropdowns
    document.querySelectorAll('.dropdown-menu-custom')
        .forEach(menu => {
            if(!menu.contains(e.target)){
                menu.style.display = 'none';
                let parentCard = menu.closest('.mis-card');
                if(parentCard) parentCard.classList.remove('dropdown-active');
            }
        });

    let mainWrapper = document.querySelector('.main-type-dropdown-wrapper');
    let mainMenu = document.getElementById('mainTypeDropdownMenu');
    let subMenu = document.getElementById('serviceSubDropdownMenu');

    if(mainWrapper && !mainWrapper.contains(e.target)){
        if(mainMenu){
            mainMenu.style.display = 'none';
        }
        if(subMenu){
            subMenu.style.display = 'none';
        }
    }
});

/* =========================================
INITIALIZE CHARTS
========================================= */

@if(!$isNewSeller)

// Initialize Sales Chart
salesChart = echarts.init(document.getElementById('salesChart'));
salesChart.setOption({
    tooltip: { trigger: 'axis' },
    legend: { data: ['Sales', 'Returns'] },
    xAxis: {
        type: 'category',
        boundaryGap: false,
        data: {!! json_encode($months ?? []) !!}
    },
    yAxis: { type: 'value' },
    series: [
        {
            name: 'Sales',
            type: 'line',
            smooth: true,
            symbol: 'circle',
            symbolSize: 10,
            lineStyle: { width: 4, color: '#16a34a' },
            areaStyle: { opacity: 0.15, color: '#16a34a' },
            itemStyle: { color: '#16a34a' },
            data: {!! json_encode($salesData ?? []) !!}
        },
        {
            name: 'Returns',
            type: 'line',
            smooth: true,
            symbol: 'circle',
            symbolSize: 8,
            lineStyle: { width: 4, color: '#ef4444' },
            areaStyle: { opacity: 0.08, color: '#ef4444' },
            itemStyle: { color: '#ef4444' },
            data: {!! json_encode($returnData ?? []) !!}
        }
    ]
});

// Initialize Rating Chart
ratingChart = echarts.init(document.getElementById('ratingChart'));
ratingChart.setOption({
    tooltip: { trigger: 'item' },
    legend: { bottom: 0 },
    series: [{
        type: 'pie',
        radius: ['45%', '75%'],
        itemStyle: { borderRadius: 10, borderColor: '#fff', borderWidth: 4 },
        label: { show: true, formatter: '{d}%' },
        data: [
            { value: {{ $rating4plus }}, name: 'Top Ratings', itemStyle: { color: '#16a34a' } },
            { value: {{ $rating3below }}, name: 'Low Ratings', itemStyle: { color: '#ef4444' } }
        ]
    }]
});

// Initialize Bad Reviews Chart
badChart = echarts.init(document.getElementById('badChart'));
badChart.setOption({
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: ['1 Star', '2 Star', '3 Star', '4 Star', '5 Star'] },
    yAxis: { type: 'value' },
    series: [{
        type: 'bar',
        barWidth: 45,
        itemStyle: { color: '#ef4444', borderRadius: [12, 12, 0, 0] },
        label: { show: true, position: 'top' },
        data: {!! json_encode($reviewDistribution ?? []) !!}
    }]
});

// Initialize Grievance Chart
grievanceChart = echarts.init(document.getElementById('grievanceChart'));
grievanceChart.setOption({
    tooltip: { trigger: 'axis' },
    xAxis: { type: 'category', data: ['Delivery', 'Refund', 'Quality', 'Support', 'Service'] },
    yAxis: { type: 'value' },
    series: [{
        type: 'bar',
        barWidth: 45,
        itemStyle: { color: '#6366f1', borderRadius: [12, 12, 0, 0] },
        label: { show: true, position: 'top' },
        data: {!! json_encode($grievanceData ?? []) !!}
    }]
});

// Initialize Boost Performance Chart
boostPerformanceChart = echarts.init(document.getElementById('boostPerformanceChart'));
let boostChartData = @json($boostChartData);

boostPerformanceChart.setOption({
    tooltip: {
        trigger: 'axis',
        axisPointer: {
            type: 'cross',
            crossStyle: {
                color: '#999'
            }
        }
    },
    legend: {
        data: ['Ad Spend (Boost)', 'Conversions (Leads/Bookings)'],
        bottom: 0
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '12%',
        top: '15%',
        containLabel: true
    },
    xAxis: [
        {
            type: 'category',
            data: boostChartData.dates,
            axisPointer: {
                type: 'shadow'
            }
        }
    ],
    yAxis: [
        {
            type: 'value',
            name: 'Ad Spend (₹)',
            min: 0,
            max: 800,
            interval: 200,
            axisLabel: {
                formatter: '₹{value}'
            }
        },
        {
            type: 'value',
            name: 'Conversions',
            min: 0,
            max: 120,
            interval: 30,
            axisLabel: {
                formatter: '{value}'
            }
        }
    ],
    series: [
        {
            name: 'Ad Spend (Boost)',
            type: 'line',
            smooth: true,
            symbol: 'circle',
            symbolSize: 8,
            lineStyle: { width: 3, color: '#38bdf8' },
            areaStyle: {
                opacity: 0.15,
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#38bdf8' },
                    { offset: 1, color: 'rgba(56, 189, 248, 0.01)' }
                ])
            },
            itemStyle: { color: '#38bdf8' },
            data: boostChartData.ad_spend
        },
        {
            name: 'Conversions (Leads/Bookings)',
            type: 'line',
            smooth: true,
            yAxisIndex: 1,
            symbol: 'circle',
            symbolSize: 8,
            lineStyle: { width: 3, color: '#f43f5e' },
            areaStyle: {
                opacity: 0.1,
                color: new echarts.graphic.LinearGradient(0, 0, 0, 1, [
                    { offset: 0, color: '#f43f5e' },
                    { offset: 1, color: 'rgba(244, 63, 94, 0.01)' }
                ])
            },
            itemStyle: { color: '#f43f5e' },
            data: boostChartData.conversions,
            markLine: {
                symbol: ['none', 'none'],
                lineStyle: {
                    type: 'dashed',
                    width: 1.5
                },
                data: [
                    {
                        xAxis: '13 May',
                        name: 'Boost Event',
                        lineStyle: { color: '#22c55e' },
                        label: {
                            show: true,
                            position: 'end',
                            formatter: 'Boost Event',
                            backgroundColor: '#22c55e',
                            color: '#fff',
                            padding: [4, 8],
                            borderRadius: 4,
                            fontWeight: 'bold',
                            fontSize: 10
                        }
                    },
                    {
                        xAxis: '15 May',
                        name: 'ROI',
                        lineStyle: { color: '#0369a1' },
                        label: {
                            show: true,
                            position: 'end',
                            formatter: 'ROI',
                            backgroundColor: '#0369a1',
                            color: '#fff',
                            padding: [4, 8],
                            borderRadius: 4,
                            fontWeight: 'bold',
                            fontSize: 10
                        }
                    }
                ]
            }
        }
    ]
});

// Handle window resize
window.addEventListener('resize', function(){
    if(salesChart) salesChart.resize();
    if(ratingChart) ratingChart.resize();
    if(badChart) badChart.resize();
    if(grievanceChart) grievanceChart.resize();
    if(boostPerformanceChart) boostPerformanceChart.resize();
});

@endif

/* =========================================
   REFERRAL SETTINGS MODAL INTERACTIONS
   ========================================= */



</script>

@endsection
