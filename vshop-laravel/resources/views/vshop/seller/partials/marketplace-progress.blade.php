<div class="marketplace-progress">

@php
$steps = [
'category' => 'Category',
'banner'   => 'Banner',
'template' => 'Website Template',
'seo'      => 'SEO',
'area'     => 'Area of Operation'
];

$totalSteps = count($steps);

$completedCount =
collect($progress)
->filter(fn($v) => $v == true)
->count();

$percentage =
$totalSteps
? round(($completedCount / $totalSteps) * 100)
: 0;
@endphp

<div class="onboarding-card-vertical">
    <div class="card-body-vertical">
        
        {{-- HEADER --}}
        <div class="vertical-header">
            <div>
                <h5 class="fw-bold mb-1">Marketplace Setup</h5>
                <small class="text-muted">Configure storefront details</small>
            </div>
            <div class="progress-badge-vertical" id="draftPercentage">{{ $percentage }}%</div>
        </div>

        {{-- VERTICAL PROGRESS BAR CONTAINER --}}
        <div class="vertical-progress-container">
            
            {{-- LEFT SIDE - VERTICAL BAR --}}
            <div class="vertical-progress-bar-wrap">
                <div class="vertical-progress-bar">
                    <div class="vertical-progress-fill" id="draftFill" style="height: {{ $percentage }}%"></div>
                </div>
            </div>

            {{-- RIGHT SIDE - STEPS LIST --}}
            <div class="vertical-steps-list">
                @foreach($steps as $key => $label)
                @php
                $completed = $progress[$key] ?? false;
                $active    = $currentStep == $key;
                $enabled   = $completed || $active;
                @endphp

                <div class="vertical-step-item {{ $completed ? 'completed' : '' }} {{ $active ? 'active' : '' }} {{ !$enabled ? 'disabled' : '' }}">
                    <div class="vertical-step-circle"></div>
                    <div class="vertical-step-content">
                        @if($enabled)
                            <a href="{{ route('seller.marketplace.'.$key) }}">
                                <div class="vertical-step-title">{{ $label }}</div>
                                @if($completed)
                                    <small>✓ All fields complete</small>
                                @elseif($active)
                                    <small>● Please fill all fields</small>
                                @else
                                    <small>○ Please fill previous step</small>
                                @endif
                            </a>
                        @else
                            <div class="vertical-step-title">{{ $label }}</div>
                            <small>🔒 Please fill previous step</small>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        
    </div>
</div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

<style>
.marketplace-progress {
    width: 100%;
    max-width: 320px;
    position: sticky;
    top: 20px;
}

/* ========================================
   VERTICAL ONBOARDING CARD
======================================== */

.onboarding-card-vertical {
    border-radius: 18px;
    overflow: hidden;
    background: #fff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    margin-bottom: 24px;
}

.card-body-vertical {
    padding: 24px;
}

/* Header - Same as sample */
.vertical-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 1px solid #e5e7eb;
}

.vertical-header h5 {
    font-size: 18px;
    margin: 0;
    color: #111827;
    font-weight: 700;
}

.vertical-header small {
    font-size: 12px;
    color: #6b7280;
}

.progress-badge-vertical {
    background: #dcfce7;
    color: #15803d;
    padding: 8px 16px;
    border-radius: 30px;
    font-size: 13px;
    font-weight: 700;
}

/* ========================================
   VERTICAL PROGRESS CONTAINER
======================================== */

.vertical-progress-container {
    display: flex;
    gap: 20px;
    position: relative;
}

/* ========================================
   LEFT SIDE - VERTICAL BAR
======================================= */

.vertical-progress-bar-wrap {
    position: absolute;
    left: 8.5px;
    top: 10px;
    bottom: 10px;
    width: 3px;
    display: flex;
    justify-content: center;
    z-index: 1;
}

.vertical-progress-bar {
    width: 3px;
    height: 100%;
    background: #e5e7eb;
    border-radius: 30px;
    overflow: hidden;
    position: relative;
}

.vertical-progress-fill {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
    border-radius: 30px;
    transition: height 0.5s ease;
}

/* ========================================
   RIGHT SIDE - STEPS LIST
======================================== */

.vertical-steps-list {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.vertical-step-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    position: relative;
    transition: all 0.3s ease;
}

/* Step Circle - SMALLER SIZE */
.vertical-step-circle {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #fff;
    border: 2.5px solid #e5e7eb;
    flex-shrink: 0;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
}

/* Completed Step Circle */
.vertical-step-item.completed .vertical-step-circle {
    background: #16a34a;
    border-color: #16a34a;
    box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.2);
}

/* Active Step Circle */
.vertical-step-item.active:not(.completed) .vertical-step-circle {
    border-color: #16a34a;
    box-shadow: 0 0 0 2px rgba(22, 163, 74, 0.15);
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(22, 163, 74, 0.4);
    }
    70% {
        box-shadow: 0 0 0 6px rgba(22, 163, 74, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(22, 163, 74, 0);
    }
}

/* Step Content */
.vertical-step-content {
    flex: 1;
}

.vertical-step-content a {
    text-decoration: none;
    display: block;
}

.vertical-step-title {
    font-size: 14px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 3px;
    transition: all 0.3s ease;
}

.vertical-step-item.completed .vertical-step-title {
    color: #16a34a !important;
}

.vertical-step-item.active:not(.completed) .vertical-step-title {
    color: #111827 !important;
    font-weight: 700;
}

.vertical-step-content small {
    font-size: 11px;
    color: #cbd5e1;
    font-weight: 500;
}

.vertical-step-item.active small {
    color: #6b7280 !important;
}

.vertical-step-item.completed small {
    color: #6b7280;
}

/* Disabled Step */
.vertical-step-item.disabled {
    opacity: 0.6;
    pointer-events: none;
}

.vertical-step-item.disabled .vertical-step-title {
    color: #cbd5e1;
}

/* Hover Effects - ONLY FOR ACTIVE STEP */
.vertical-step-item.active:not(.disabled):hover {
    transform: translateX(6px);
}

.vertical-step-item.active:not(.disabled):hover .vertical-step-title {
    color: #059669 !important;
}

/* Responsive */
@media (max-width: 768px) {
    .card-body-vertical {
        padding: 16px;
    }
    
    .vertical-step-title {
        font-size: 13px;
    }
    
    .vertical-step-circle {
        width: 18px;
        height: 18px;
        border-width: 2px;
    }
    
    .progress-badge-vertical {
        padding: 6px 12px;
        font-size: 12px;
    }
}

/* =========================================
VERIFICATION WIDGET
========================================= */

.verification-widget{
background:#fff;
padding:18px;
border-radius:18px;
margin-top:20px;
margin-bottom:18px;
box-shadow:0 2px 10px rgba(0,0,0,.05);
}

.verify-top{
display:flex;
justify-content:space-between;
align-items:center;
gap:15px;
margin-bottom:15px;
}

.verify-top h5{
margin:0;
font-size:15px;
font-weight:700;
color:#0b4da1;
}

.verify-top small{
color:#777;
font-size:12px;
}

.verify-circle{
width:58px;
height:58px;
border-radius:50%;
background:#0b4da1;
color:#fff;
display:flex;
align-items:center;
justify-content:center;
font-size:13px;
font-weight:700;
}

.verify-progress{
height:10px;
background:#edf1f5;
border-radius:30px;
overflow:hidden;
}

.verify-fill{
height:100%;
background:#1fa463;
border-radius:30px;
transition:.4s;
}

/* =========================================
AUTO SAVE
========================================= */

.autosave-widget{
background:#fff;
padding:18px;
border-radius:18px;
margin-bottom:18px;
box-shadow:0 2px 10px rgba(0,0,0,.05);
display:flex;
justify-content:space-between;
align-items:center;
gap:10px;
}

.autosave-left{
display:flex;
align-items:center;
gap:12px;
}

.autosave-dot{
width:12px;
height:12px;
border-radius:50%;
background:#1fa463;
animation:pulse 1.5s infinite;
flex-shrink:0;
}

@keyframes pulse{
0%{transform:scale(1);}
50%{transform:scale(1.3);}
100%{transform:scale(1);}
}

.autosave-widget h6{
margin:0;
font-size:14px;
font-weight:700;
color:#0b4da1;
}

.autosave-widget small{
font-size:12px;
color:#777;
display:block;
}

.save-status{
background:#dff1e5;
color:#1fa463;
padding:5px 12px;
border-radius:20px;
font-size:12px;
font-weight:600;
transition:.3s;
}

/* =========================================
DRAFT STATUS
========================================= */

.draft-widget{
background:#fff;
padding:18px;
border-radius:18px;
margin-bottom:20px;
box-shadow:0 2px 10px rgba(0,0,0,.05);
}

.draft-header{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:12px;
}

.draft-header h6{
margin:0;
font-size:14px;
font-weight:700;
color:#0b4da1;
}

.draft-header small{
font-size:12px;
color:#777;
}

.draft-badge{
background:#eef3fb;
color:#0b4da1;
padding:5px 10px;
border-radius:20px;
font-size:12px;
font-weight:700;
}

.draft-progress{
height:10px;
background:#edf1f5;
border-radius:30px;
overflow:hidden;
}

.draft-fill{
height:100%;
background:#0b4da1;
border-radius:30px;
transition:.4s;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('marketplaceForm');
    if (!form) return;

    /* =========================================
       STORAGE KEY
    ========================================= */
    const storageKey = 'marketplace_autosave_' + window.location.pathname;
    const statusBox = document.getElementById('autosaveStatus');
    const statusText = document.getElementById('autosaveText');

    /* =========================================
       SAVE FORM DATA
    ========================================= */
    function saveFormData() {
        let formData = {};
        form.querySelectorAll('input, textarea, select').forEach(field => {
            if (!field.name) return;
            if (field.type === 'checkbox') {
                if (!formData[field.name]) formData[field.name] = [];
                if (field.checked) formData[field.name].push(field.value);
            } else if (field.type === 'radio') {
                if (field.checked) formData[field.name] = field.value;
            } else if (field.type === 'file') {
                // skip
            } else {
                formData[field.name] = field.value;
            }
        });
        localStorage.setItem(storageKey, JSON.stringify(formData));
        updateStatus('Saving...');
        setTimeout(() => updateStatus('Saved'), 600);
    }

    /* =========================================
       RESTORE FORM DATA
    ========================================= */
    function restoreFormData() {
        let savedData = localStorage.getItem(storageKey);
        if (!savedData) return;
        savedData = JSON.parse(savedData);
        form.querySelectorAll('input, textarea, select').forEach(field => {
            if (!field.name) return;
            if (field.type === 'checkbox') {
                if (Array.isArray(savedData[field.name]) && savedData[field.name].includes(field.value)) {
                    field.checked = true;
                }
            } else if (field.type === 'radio') {
                if (savedData[field.name] == field.value) field.checked = true;
            } else if (field.type === 'file') {
                // skip
            } else {
                if (savedData[field.name] !== undefined) field.value = savedData[field.name];
            }
        });
    }

    /* =========================================
       STATUS UPDATE (visual feedback only)
    ========================================= */
    function updateStatus(text) {
        if (!statusBox) return;
        statusBox.innerText = text;
        if (text === 'Saving...') {
            statusBox.style.background = '#fff3cd';
            statusBox.style.color = '#b78103';
            if (statusText) statusText.innerText = 'Saving your changes...';
        } else if (text === 'Saved') {
            statusBox.style.background = '#dff1e5';
            statusBox.style.color = '#1fa463';
            if (statusText) statusText.innerText = 'Your progress is saved locally';
        } else if (text === 'Submitting...') {
            statusBox.style.background = '#e7edf7';
            statusBox.style.color = '#0b4da1';
            if (statusText) statusText.innerText = 'Submitting marketplace data';
        }
    }

    /* =========================================
       AUTO SAVE EVENT LISTENERS
    ========================================= */
    form.querySelectorAll('input, textarea, select').forEach(field => {
        field.addEventListener('input', saveFormData);
        field.addEventListener('change', saveFormData);
    });

    /* =========================================
       RESTORE SAVED DATA ON PAGE LOAD
    ========================================= */
    restoreFormData();

    /* =========================================
       FORM SUBMIT - CLEAR STORAGE
    ========================================= */
    form.addEventListener('submit', function () {
        updateStatus('Submitting...');
        localStorage.removeItem(storageKey);
    });
});
</script>
