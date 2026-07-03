@php
    $steps = [
        'category' => 'Category',
        'banner' => 'Banner',
        'template' => 'Website Template',
        'seo' => 'SEO',
        'area' => 'Area of Operation',
    ];

    $totalSteps = count($steps);

    $completedCount = collect($progress ?? [])
        ->filter(fn($v) => $v == true)
        ->count();

    $percentage = $totalSteps ? round(($completedCount / $totalSteps) * 100) : 0;
@endphp


<div class="marketplace-header">

    {{-- LEFT: BREADCRUMB --}}
    <div class="header-left">
        <a href="{{ route('seller.marketplace.category') }}">
            Market Place
        </a>
        <span>/</span>
        <span class="active">{{ $title ?? 'Dashboard' }}</span>
    </div>




    {{-- RIGHT: STATUS --}}
    <div class="header-right">

        <div class="auto-save">
            <span class="dot"></span>
            <div>
                <strong>Auto-Save Enabled</strong>
                <small>Saving automatically</small>
            </div>
        </div>

        <div class="draft-badge">
            Draft: {{ $percentage }}%
        </div>

    </div>

</div>













<style>
    .marketplace-header {
        background: #eef2f7;
        padding: 14px 18px;
        border-radius: 10px;
        margin-bottom: 20px;

        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 20px;
    }

    /* LEFT */
    .header-left a {
        text-decoration: none;
        color: #0b4da1;
        font-weight: 600;
    }

    .header-left span {
        margin: 0 5px;
    }

    .header-left .active {
        color: #6c757d;
    }

    /* CENTER (MAIN FOCUS) */
    .header-center {
        flex: 1;
        text-align: center;
        max-width: 420px;
    }

    .progress-title {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #6c757d;
        margin-bottom: 6px;
    }

    .progress-title strong {
        color: #0b4da1;
        font-size: 13px;
    }

    .progress-bar-main {
        height: 12px;
        background: #dfe6ee;
        border-radius: 30px;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 1px 3px rgba(0, 0, 0, .08);
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #0b4da1, #1fa463);
        border-radius: 30px;
        transition: .4s;
    }

    .progress-percent {
        font-size: 12px;
        margin-top: 4px;
        font-weight: 600;
        color: #0b4da1;
    }

    /* RIGHT SIDE */
    .header-right {
        display: flex;
        align-items: center;
        gap: 15px;
    }

    /* AUTO SAVE */
    .auto-save {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 11px;
    }

    .auto-save strong {
        display: block;
        font-size: 11px;
        color: #0b4da1;
    }

    .auto-save small {
        font-size: 10px;
        color: #6c757d;
    }

    .dot {
        width: 8px;
        height: 8px;
        background: #1fa463;
        border-radius: 50%;
        animation: pulse 1.5s infinite;
    }

    @keyframes pulse {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.4);
        }

        100% {
            transform: scale(1);
        }
    }

    /* DRAFT */
    .draft-badge {
        background: #fff;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        color: #0b4da1;
        font-weight: 600;
    }
    
    /* SMOOTH SPA LOAD & GRID LAYOUT */
    @keyframes smoothFadeIn {
        from { opacity: 0; transform: translateY(12px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .marketplace-wrapper {
        animation: smoothFadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1) forwards;
        display: grid !important;
        grid-template-columns: 1fr 340px !important;
        gap: 30px !important;
        align-items: start !important;
        width: 100% !important;
    }
    .marketplace-left {
        width: 100% !important;
        min-width: 0 !important;
    }
    .marketplace-right {
        width: 100% !important;
        position: sticky !important;
        top: 20px !important;
    }
    
    /* Ensure content cards span full width of the left grid column */
    .category-card, .market-card, .area-card, .template-wrapper, .category-box, .card {
        width: 100% !important;
        box-sizing: border-box !important;
    }
    
    /* PREMIUM FORM CONTAINER (Applies to all forms) */
    .premium-form-container {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 4px 20px rgba(0, 67, 146, 0.05);
        border: 1px solid #e2e8f0;
        padding: 30px;
        transition: all 0.3s ease;
    }
    
    .premium-form-container .form-control, .premium-form-container .form-select {
        border-radius: 12px;
        border: 1px solid #cbd5e1;
        padding: 12px 16px;
        font-size: 14px;
        color: #334155;
        transition: all 0.2s ease;
        background-color: #f8fafc;
    }
    
    .premium-form-container .form-control:focus, .premium-form-container .form-select:focus {
        border-color: #0b4da1;
        box-shadow: 0 0 0 3px rgba(11, 77, 161, 0.1);
        background-color: #ffffff;
        outline: none;
    }
    
    .premium-form-container label {
        font-weight: 600;
        color: #475569;
        margin-bottom: 8px;
        font-size: 14px;
    }
    
    .premium-form-container .section-title {
        font-size: 18px;
        font-weight: 800;
        color: #0f172a;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 1px solid #f1f5f9;
    }

    .premium-btn-save {
        background: linear-gradient(135deg, #0b4da1 0%, #1a73e8 100%);
        border: none;
        color: white;
        padding: 12px 28px;
        border-radius: 12px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 12px rgba(26, 115, 232, 0.2);
    }
    .premium-btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(26, 115, 232, 0.3);
        color: white;
    }

    /* STANDARDIZED HEADER BUTTONS */
    .premium-nav-btn {
        width: 38px; height: 38px; background: #fff; color: #0b4da1; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 16px; border: 1px solid #e2e8f0;
        transition: 0.3s; text-decoration: none; box-shadow: 0 2px 4px rgba(0,0,0,0.02);
        cursor: pointer;
    }
    .premium-nav-btn:hover { background: #f8fafc; color: #1a73e8; transform: translateY(-2px); }

    .premium-list-btn {
        width: 38px; height: 38px; border-radius: 10px; display: flex; align-items: center; justify-content: center;
        font-size: 20px; box-shadow: 0 4px 10px rgba(11, 77, 161, 0.2); transition: 0.3s; text-decoration: none; color: white;
        background: linear-gradient(135deg, #0b4da1 0%, #1a73e8 100%);
        cursor: pointer;
    }
    .premium-list-btn:hover { transform: translateY(-2px); color: white; }
    
    .header-action-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }
    .top-back-heading-sec {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .top-back-heading-sec h5 {
        color: #1e293b !important;
        font-weight: 700 !important;
        margin: 0 !important;
        font-size: 1.1rem;
    }
</style>
