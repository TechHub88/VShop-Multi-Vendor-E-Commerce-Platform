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
</style>
