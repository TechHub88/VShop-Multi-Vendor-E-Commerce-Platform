<div class="profile-progress">

    @php
        $seller = auth()->guard('seller')->user();

        $contacts = $seller->contact_persons ?? [];

        $progress = [
            'organization' => !empty($seller->legal_name),
            'contact' => !empty($contacts),
            'bank' => !empty($seller->bank_account_number),
            'payment' =>
                !empty($seller->paypal_email) ||
                (!empty($seller->razorpay_key) && !empty($seller->razorpay_secret)) ||
                (!empty($seller->stripe_key) && !empty($seller->stripe_secret)) ||
                (!empty($seller->api_key) && !empty($seller->api_secret)),
            'agreement' => !empty($seller->agreement_file),
            'logs' => true,
        ];

        $currentStep = $currentStep ?? 'logs';

        $steps = [
            'organization' => 'Organization',
            'contact' => 'Contact Person',
            'bank' => 'Banking',
            'payment' => 'Payment Gateway',
            'agreement' => 'Agreement',
            'logs' => 'Logs',
        ];

        $completedCount = collect($progress)->filter()->count();
        $totalSteps = count($steps);
        $percent = round(($completedCount / $totalSteps) * 100);
        $unlock = true;
    @endphp

    <div class="onboarding-card-vertical">
        <div class="card-body-vertical">

            {{-- HEADER --}}
            <div class="vertical-header">
                <div>
                    <h5 class="fw-bold mb-1">Seller Onboarding</h5>
                    <small class="text-muted">Complete seller setup process</small>
                </div>
                <div class="progress-badge-vertical">{{ $percent }}%</div>
            </div>

            {{-- VERTICAL PROGRESS BAR CONTAINER --}}

            <div class="vertical-progress-container">

                {{-- LEFT SIDE - VERTICAL BAR --}}
                <div class="vertical-progress-bar-wrap">
                    <div class="vertical-progress-bar">
                        <div class="vertical-progress-fill" style="height: {{ $percent }}%"></div>
                    </div>
                </div>

                {{-- RIGHT SIDE - STEPS LIST --}}
                <div class="vertical-steps-list">
                    @foreach ($steps as $key => $label)
                        @php
                            $completed = $progress[$key] ?? false;
                            $active = $currentStep == $key;
                            $enabled = $unlock;
                        @endphp

                        <div
                            class="vertical-step-item {{ $completed ? 'completed' : '' }} {{ $active ? 'active' : '' }} {{ !$enabled ? 'disabled' : '' }}">
                            <div class="vertical-step-circle"></div>
                            <div class="vertical-step-content">
                                @if ($enabled)
                                    <a href="{{ route('seller.' . $key) }}">
                                        <div class="vertical-step-title">{{ $label }}</div>
                                        @if ($completed)
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

                        @php
                            if (!$completed && $key !== 'logs') {
                                $unlock = false;
                            }
                        @endphp
                    @endforeach
                </div>
            </div>

        </div>
    </div>

</div>

<style>
    /* ========================================
   VERTICAL ONBOARDING CARD
======================================== */

    .onboarding-card-vertical {
        border-radius: 18px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }

    .card-body-vertical {
        padding: 24px;
        border: 1px solid rgb(201 208 218) !important;
        
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
    
    .onboarding-card-vertical {
        border-radius: 10px;
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
</style>
