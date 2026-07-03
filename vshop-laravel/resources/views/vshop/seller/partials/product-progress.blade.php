

<div class="profile-progress">

    @php
        use Illuminate\Support\Facades\DB;
        use Illuminate\Support\Facades\Route;

        /*
|--------------------------------------------------------------------------
| STEPS
|--------------------------------------------------------------------------
*/

        $productType = request()->type ?? ($product->type ?? '');

        $specificationLabel = in_array($productType, ['product']) ? 'Specifications' : 'Details';

        $steps = [
            'basic' => 'Basic Details',
            'media' => 'Images / Videos',

            'specifications' => $specificationLabel,

            'seo' => 'SEO',
            'price' => 'Price',
            'faq' => 'FAQ',
            'download' => 'Download',
            'sample' => 'Sample Code',
            'review' => 'Reviews',
            'list' => 'Preview',
            'log' => 'Activity Log',
        ];
     

        $filled = function ($value) {
            if (is_array($value)) {
                return count($value) > 0;
            }

            if (is_string($value)) {
                return trim($value) !== '' && $value !== '[]';
            }

            return !empty($value);
        };

        $images = is_array($product->images ?? null) ? $product->images : json_decode($product->images ?? '[]', true);

        $faqs = is_array($product->faqs ?? null) ? $product->faqs : json_decode($product->faqs ?? '[]', true);

        $downloads = is_array($product->download_data ?? null)
            ? $product->download_data
            : json_decode($product->download_data ?? '[]', true);

        $isSaved = !empty($product->id);

        /*
|--------------------------------------------------------------------------
| REVIEW COUNT
|--------------------------------------------------------------------------
*/

        $reviewCount = 0;

        if ($isSaved) {
            $reviewCount = DB::table('product_reviews')
                ->where('product_id', $product->id)
                ->where('status', 'active')
                ->count();
        }

        /*
|--------------------------------------------------------------------------
| PROGRESS
|--------------------------------------------------------------------------
*/

        $progress = [
            'basic' =>
                $isSaved &&
                $filled($product->name ?? null) &&
                $filled($product->category_id ?? null) &&
                $filled($product->short_summary ?? null) &&
                $filled($product->description ?? null),

            'media' => $isSaved && $filled($product->thumbnail ?? null) && !empty($images),

            'specifications' => $isSaved && $filled($product->description ?? null),

            'seo' => $isSaved && $filled($product->meta_title ?? null) && $filled($product->meta_description ?? null),

            'price' => $isSaved && $filled($product->price ?? null),

            'faq' => !empty($faqs),

            'download' => !empty($downloads),

            'sample' => $filled($product->sample_download_url ?? null),

            'review' => $reviewCount > 0,

            'list' => $filled($product->price ?? null),

            'log' => $isSaved,
        ];

        /*
|--------------------------------------------------------------------------
| CURRENT STEP
|--------------------------------------------------------------------------
*/

        $routeName = request()->route()->getName();

        $currentStep = collect(array_keys($steps))->first(fn($step) => str_contains($routeName, $step));

        $stepKeys = array_keys($steps);

        /*
|--------------------------------------------------------------------------
| AUTO SAVE STATUS
|--------------------------------------------------------------------------
*/

        $mandatorySteps = ['basic', 'media', 'specifications', 'seo', 'price', 'faq'];
        $totalSteps = count($mandatorySteps);

        $completedSteps = collect($mandatorySteps)->filter(function($k) use ($progress) {
            return !empty($progress[$k]);
        })->count();

        $draftPercent = $totalSteps > 0 ? round(($completedSteps / $totalSteps) * 100) : 0;

        $draftClass = $draftPercent >= 100 ? 'success' : 'warning';

    @endphp



    <div class="onboarding-card-vertical">
        <div class="card-body-vertical">

            {{-- HEADER --}}
            <div class="vertical-header">
                <div>
                    <h5 class="fw-bold mb-1">Product Setup</h5>
                    <small class="text-muted">Complete product details</small>
                </div>
                <div class="progress-badge-vertical">{{ $draftPercent }}%</div>
            </div>

            {{-- VERTICAL PROGRESS BAR CONTAINER --}}
            <div class="vertical-progress-container">

                {{-- LEFT SIDE - VERTICAL BAR --}}
                <div class="vertical-progress-bar-wrap">
                    <div class="vertical-progress-bar">
                        <div class="vertical-progress-fill" style="height: {{ $draftPercent }}%"></div>
                    </div>
                </div>

                {{-- RIGHT SIDE - STEPS LIST --}}
                <div class="vertical-steps-list">
                    @foreach ($steps as $key => $label)
                        @php
                            $completed = $progress[$key] ?? false;
                            $active = $currentStep === $key;
                            $isOptional = !in_array($key, $mandatorySteps);

                            $enabled = true;
                            $currentIndex = array_search($key, $stepKeys);

                            $link = '#';
                            if ($enabled && $isSaved) {
                                switch ($key) {
                                    case 'basic':
                                        if (Route::has('seller.products.basic.edit')) {
                                            $link = route('seller.products.basic.edit', $product->id);
                                        }
                                        break;
                                    case 'faq':
                                        if (Route::has('seller.products.faq')) {
                                            $link = route('seller.products.faq', $product->id);
                                        }
                                        break;
                                    case 'download':
                                        if (Route::has('seller.products.download')) {
                                            $link = route('seller.products.download', $product->id);
                                        }
                                        break;
                                    case 'sample':
                                        if (Route::has('seller.products.samplecode')) {
                                            $link = route('seller.products.samplecode', $product->id);
                                        }
                                        break;
                                    case 'review':
                                        if (Route::has('seller.products.productsreview')) {
                                            $link = route('seller.products.productsreview', $product->id);
                                        }
                                        break;
                                    case 'list':
                                        if (Route::has('seller.products.list')) {
                                            $link = route('seller.products.list', $product->id);
                                        }
                                        break;
                                    case 'log':
                                        if (Route::has('seller.products.log')) {
                                            $link = route('seller.products.log', $product->id);
                                        } elseif (Route::has('seller.products.logs')) {
                                            $link = route('seller.products.logs', $product->id);
                                        }
                                        break;
                                    default:
                                        if (Route::has('seller.products.' . $key . '.edit')) {
                                            $link = route('seller.products.' . $key . '.edit', $product->id);
                                        } elseif (Route::has('seller.products.' . $key)) {
                                            $link = route('seller.products.' . $key, $product->id);
                                        }
                                        break;
                                }
                            }
                        @endphp

                        <div
                            class="vertical-step-item {{ $completed ? 'completed' : '' }} {{ $active ? 'active' : '' }} {{ !$enabled ? 'disabled' : '' }}">
                            <div class="vertical-step-circle"></div>
                            <div class="vertical-step-content">
                                @if ($enabled && $link !== '#')
                                    <a href="{{ $link }}">
                                        <div class="vertical-step-title">{{ $label }}</div>
                                        @if ($completed)
                                            <small>✓ Completed</small>
                                        @elseif($active)
                                            <small>● In Progress</small>
                                        @elseif($isOptional)
                                            <small>○ Optional</small>
                                        @else
                                            <small>○ Pending</small>
                                        @endif
                                    </a>
                                @else
                                    <div class="vertical-step-title">{{ $label }}</div>
                                    @if ($completed)
                                        <small>✓ Completed</small>
                                    @elseif($isOptional)
                                        <small>○ Optional</small>
                                    @else
                                        <small>🔒 Locked</small>
                                    @endif
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>

    <style>
        .profile-progress {
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
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
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
    </style>

    </style>
    
    <style>
        .onboarding-card-vertical {
            border-radius: 6px;
        }
    </style>



    <script>
        $(document).on(
            'input change',
            '#basicForm input, #basicForm textarea, #basicForm select',
            function() {

                $('.step-box.active').addClass('active');

            }
        );
    </script>
