@extends('layouts.vertical', ['title' => 'Recommendation Engine'])

@section('content')
<style>
    /* Professional E-commerce Admin Styles (Amazon/Flipkart inspired) */
    .page-title-box { padding-bottom: 15px; border-bottom: 1px solid #e5e7eb; margin-bottom: 20px; }
    .page-title { color: #111827; font-weight: 600; font-size: 1.25rem; }
    
    .product-group-card {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        overflow: hidden;
    }
    
    .product-group-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e5e7eb;
        background-color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    
    .main-product-info {
        display: flex;
        align-items: center;
        gap: 16px;
    }
    
    .main-img-box {
        width: 64px;
        height: 64px;
        border: 1px solid #e5e7eb;
        border-radius: 4px;
        background: #f9fafb;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        flex-shrink: 0;
    }
    
    .main-img-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .product-title {
        font-size: 1rem;
        font-weight: 600;
        color: #0f1111; /* Amazon dark */
        margin-bottom: 4px;
        line-height: 1.4;
    }
    
    .product-meta {
        font-size: 0.8125rem;
        color: #565959;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .meta-divider {
        color: #d1d5db;
        font-size: 12px;
    }
    
    .price-tag {
        font-size: 1.125rem;
        font-weight: 700;
        color: #b12704; /* Amazon price color */
    }
    
    .recommended-section {
        padding: 20px;
        background-color: #f9fafb;
    }
    
    .section-title {
        font-size: 0.8125rem;
        font-weight: 700;
        color: #374151;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .rec-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 16px;
    }
    
    .rec-item {
        background: #ffffff;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        padding: 12px;
        display: flex;
        gap: 12px;
        transition: border-color 0.2s, box-shadow 0.2s;
    }
    
    .rec-item:hover {
        border-color: #d1d5db;
        box-shadow: 0 2px 5px rgba(0,0,0,0.04);
    }
    
    .rec-img-box {
        width: 50px;
        height: 50px;
        border: 1px solid #f3f4f6;
        border-radius: 4px;
        background: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    
    .rec-img-box img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .rec-details {
        flex-grow: 1;
        min-width: 0; /* for text truncation */
    }
    
    .rec-title {
        font-size: 0.875rem;
        font-weight: 500;
        color: #007185; /* Amazon link blue */
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .rec-meta {
        font-size: 0.75rem;
        color: #6b7280;
        margin-bottom: 4px;
    }
    
    .rec-price {
        font-size: 0.875rem;
        font-weight: 600;
        color: #111827;
    }
    
    .clean-badge {
        background-color: #f3f4f6;
        color: #374151;
        border: 1px solid #e5e7eb;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }
</style>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box mt-3">
                <h4 class="page-title">Recommendation Engine</h4>
                <p class="text-muted mb-0" style="font-size: 0.875rem;">View and manage cross-sell configurations established across the marketplace catalog.</p>
            </div>
        </div>
    </div>

    <!-- Recommendations List -->
    <div class="row">
        <div class="col-12">
            @if(empty($productsWithRecs))
                <div class="text-center py-5" style="background: #fff; border: 1px dashed #d1d5db; border-radius: 8px;">
                    <i class="ri-node-tree text-muted" style="font-size: 48px;"></i>
                    <h5 class="text-muted mt-3">No recommendations configured yet.</h5>
                    <p class="text-muted" style="font-size: 0.875rem;">Recommendations set by sellers will appear here.</p>
                </div>
            @else
                @foreach($productsWithRecs as $index => $item)
                    @php
                        // Main Product Image
                        $mainProduct = $item['product'];
                        $mainThumb = null;
                        if (!empty($mainProduct->thumbnail)) {
                            $mainThumb = asset($mainProduct->thumbnail);
                        } elseif (!empty($mainProduct->images) && is_array($mainProduct->images) && count($mainProduct->images) > 0) {
                            $mainThumb = asset($mainProduct->images[0]);
                        }
                    @endphp
                    
                    <div class="product-group-card">
                        <!-- Header / Main Product -->
                        <div class="product-group-header">
                            <div class="main-product-info">
                                <div class="main-img-box">
                                    @if($mainThumb)
                                        <img src="{{ $mainThumb }}" alt="Image">
                                    @else
                                        <i class="ri-image-line text-muted" style="font-size: 24px;"></i>
                                    @endif
                                </div>
                                
                                <div>
                                    <div class="product-title">{{ $mainProduct->name }}</div>
                                    <div class="product-meta">
                                        <span class="meta-item">
                                            <span class="text-muted">SKU:</span>
                                            <span class="text-dark fw-medium">{{ $mainProduct->sku ?? 'N/A' }}</span>
                                        </span>
                                        <span class="meta-divider">•</span>
                                        <span class="meta-item">
                                            <i class="ri-store-2-line text-muted"></i>
                                            <span class="text-dark">{{ $mainProduct->seller ? $mainProduct->seller->name : 'Admin / Unknown' }}</span>
                                        </span>
                                        @if($item['category_name'])
                                            <span class="meta-divider">•</span>
                                            <span class="meta-item text-muted">{{ $item['category_name'] }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <div class="price-tag">₹{{ number_format($mainProduct->price, 2) }}</div>
                                <div class="mt-1">
                                    <span class="clean-badge">{{ count($item['recommended_products']) }} Configured</span>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Body / Recommended Products -->
                        <div class="recommended-section">
                            <div class="section-title">
                                <i class="ri-links-line text-muted"></i> Associated Recommendations
                            </div>
                            
                            <div class="rec-grid">
                                @forelse($item['recommended_products'] as $recProduct)
                                    @php
                                        // Recommended Product Image
                                        $recThumb = null;
                                        if (!empty($recProduct->thumbnail)) {
                                            $recThumb = asset($recProduct->thumbnail);
                                        } elseif (!empty($recProduct->images) && is_array($recProduct->images) && count($recProduct->images) > 0) {
                                            $recThumb = asset($recProduct->images[0]);
                                        }
                                    @endphp
                                    
                                    <div class="rec-item">
                                        <div class="rec-img-box">
                                            @if($recThumb)
                                                <img src="{{ $recThumb }}" alt="Image">
                                            @else
                                                <i class="ri-image-line text-muted" style="font-size: 20px;"></i>
                                            @endif
                                        </div>
                                        <div class="rec-details">
                                            <div class="rec-title" title="{{ $recProduct->name }}">{{ $recProduct->name }}</div>
                                            <div class="rec-meta">
                                                <span>ID: {{ $recProduct->id }}</span>
                                                <span class="mx-1">|</span>
                                                <span>SKU: {{ $recProduct->sku ?? 'N/A' }}</span>
                                            </div>
                                            <div class="rec-price">₹{{ number_format($recProduct->price, 2) }}</div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12 py-2">
                                        <span class="text-muted" style="font-size: 0.875rem;"><i class="ri-error-warning-line align-middle me-1"></i> Referenced products not found or deleted.</span>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</div>
@endsection