@extends('layouts.vertical', ['title' => 'Create Page'])

@section('content')
<style>
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .nav-tabs .nav-link {
        color: #495057;
        font-weight: 500;
        cursor: pointer;
    }
    .nav-tabs .nav-link.active {
        color: #2c7fc3;
        font-weight: 600;
        border-bottom: 2px solid #2c7fc3;
    }
    .tab-content {
        padding: 20px 0;
    }
    .section-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    .tab-pane {
        display: none;
    }
    .tab-pane.active {
        display: block;
    }
    .btn-warning,
    .btn-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
</style>

<div class="container-fluid pt-2">
    <h4 class="mb-3">Create New Page</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.pages.store') }}" enctype="multipart/form-data">
                @csrf

                <ul class="nav nav-tabs" id="pageTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-tab="basic" href="javascript:void(0);">Basic Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-tab="seo" href="javascript:void(0);">SEO & Meta</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-tab="travel" href="javascript:void(0);">Travel Details</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-tab="cta" href="javascript:void(0);">CTA & Links</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-tab="approval" href="javascript:void(0);">Approval Workflow</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-tab="features" href="javascript:void(0);">Features & Blocks</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-tab="ads" href="javascript:void(0);">Ads & Banners</a>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- BASIC INFO TAB -->
                    <div class="tab-pane active" id="basic-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Page Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control mb-3" placeholder="Page Title" required>
                            </div>

                            <div class="col-md-6">
                                <label>Custom Slug (URL)</label>
                                <input type="text" name="slug" class="form-control mb-3" placeholder="example-page-url">
                                <small class="text-muted">Leave empty to auto-generate from title</small>
                            </div>

                            <div class="col-md-6">
                                <label>Site Type <span class="text-danger">*</span></label>
                                <select name="site_type" class="form-control mb-3" required>
                                    <option value="vshop">VShop</option>
                                    <option value="vtravel">VTravel</option>
                                    <option value="both">Both</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Status</label>
                                <select name="status" class="form-control mb-3">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label>Page Content <span class="text-danger">*</span></label>
                                <textarea name="content" id="editor" class="form-control mb-3" required></textarea>
                            </div>

                           

                            <div class="col-md-12">
                                <label>Upload Images</label>
                                <input type="file" name="images[]" id="imageInput" multiple class="form-control mb-3">
                                <div id="preview" class="d-flex flex-wrap gap-2 mb-3"></div>
                            </div>
                        </div>
                    </div>

                    <!-- SEO TAB -->
                    <div class="tab-pane" id="seo-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Meta Title</label>
                                <input type="text" name="meta_title" class="form-control mb-3" placeholder="Meta Title">
                            </div>

                            <div class="col-md-6">
                                <label>Meta Description</label>
                                <textarea name="meta_description" class="form-control mb-3" placeholder="Meta Description" rows="2"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label>SEO Keywords</label>
                                <input type="text" name="seo_keywords" class="form-control mb-3" placeholder="keyword1, keyword2, keyword3">
                            </div>

                            <div class="col-md-6">
                                <label>SEO Robots</label>
                                <select name="seo_robots" class="form-control mb-3">
                                    <option value="index,follow">Index, Follow</option>
                                    <option value="index,nofollow">Index, No Follow</option>
                                    <option value="noindex,follow">No Index, Follow</option>
                                    <option value="noindex,nofollow">No Index, No Follow</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Canonical URL</label>
                                <input type="url" name="seo_canonical_url" class="form-control mb-3" placeholder="https://example.com/canonical-url">
                            </div>

                            <div class="col-md-12">
                                <h5 class="mt-3">Open Graph (Facebook/LinkedIn)</h5>
                                <hr>
                            </div>

                            <div class="col-md-6">
                                <label>OG Title</label>
                                <input type="text" name="og_title" class="form-control mb-3" placeholder="Open Graph Title">
                            </div>

                            <div class="col-md-6">
                                <label>OG Description</label>
                                <textarea name="og_description" class="form-control mb-3" placeholder="Open Graph Description" rows="2"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label>OG Image</label>
                                <input type="file" name="og_image" class="form-control mb-3" accept="image/*">
                            </div>

                            <div class="col-md-12">
                                <h5 class="mt-3">Twitter Card</h5>
                                <hr>
                            </div>

                            <div class="col-md-6">
                                <label>Twitter Card Type</label>
                                <select name="twitter_card" class="form-control mb-3">
                                    <option value="summary">Summary</option>
                                    <option value="summary_large_image">Summary Large Image</option>
                                    <option value="app">App</option>
                                    <option value="player">Player</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Twitter Title</label>
                                <input type="text" name="twitter_title" class="form-control mb-3" placeholder="Twitter Title">
                            </div>

                            <div class="col-md-6">
                                <label>Twitter Description</label>
                                <textarea name="twitter_description" class="form-control mb-3" placeholder="Twitter Description" rows="2"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label>Twitter Image</label>
                                <input type="file" name="twitter_image" class="form-control mb-3" accept="image/*">
                            </div>

                            <div class="col-md-12">
                                <label>Schema Markup (JSON-LD)</label>
                                <textarea name="schema_markup" class="form-control mb-3" placeholder='{"@context":"https://schema.org","@type":"WebPage"}' rows="5"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- TRAVEL DETAILS TAB -->
                    <div class="tab-pane" id="travel-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="section-card">
                                    <h5>Travel Package Details</h5>
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Price</label>
                                            <input type="number" name="travel_price" class="form-control mb-3" placeholder="0.00" step="0.01">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Duration</label>
                                            <input type="text" name="travel_duration" class="form-control mb-3" placeholder="e.g., 5 Days / 4 Nights">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Start Date</label>
                                            <input type="date" name="travel_start_date" class="form-control mb-3">
                                        </div>

                                        <div class="col-md-4">
                                            <label>End Date</label>
                                            <input type="date" name="travel_end_date" class="form-control mb-3">
                                        </div>

                                        <div class="col-md-12">
                                            <label>Destinations</label>
                                            <input type="text" name="destinations" class="form-control mb-3" placeholder="Destination 1, Destination 2, Destination 3">
                                        </div>

                                        <div class="col-md-6">
                                            <label>Inclusions</label>
                                            <textarea name="travel_inclusions" class="form-control mb-3" placeholder="- Hotel Accommodation&#10;- Daily Breakfast&#10;- Airport Transfers" rows="4"></textarea>
                                        </div>

                                        <div class="col-md-6">
                                            <label>Exclusions</label>
                                            <textarea name="travel_exclusions" class="form-control mb-3" placeholder="- Flight Tickets&#10;- Personal Expenses&#10;- Travel Insurance" rows="4"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>

                           <!-- SELLER & PRODUCT MAPPING SECTION - Updated with Checkboxes -->
<div class="col-md-12">
    <div class="section-card">
        <h5>Seller & Product Mapping</h5>
        <hr>
        
        <!-- Mapped Sellers with Checkboxes -->
        <div class="row mb-4">
            <div class="col-md-12">
                <label class="fw-bold mb-2">Select Sellers</label>
                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="selectAllSellers">
                        <label class="form-check-label fw-bold" for="selectAllSellers">Select All Sellers</label>
                    </div>
                    <hr>
                    @php
                        $sellers = \DB::table('sellers')->get();
                    @endphp
                    @foreach($sellers as $seller)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="mapped_sellers[]" class="form-check-input seller-checkbox" value="{{ $seller->id }}" id="seller_{{ $seller->id }}">
                            <label class="form-check-label" for="seller_{{ $seller->id }}">
                                {{ $seller->name }} 
                                @if($seller->email)
                                    <small class="text-muted">({{ $seller->email }})</small>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Mapped Categories with Checkboxes -->
        <div class="row mb-4">
            <div class="col-md-12">
                <label class="fw-bold mb-2">Select Categories</label>
                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="selectAllCategories">
                        <label class="form-check-label fw-bold" for="selectAllCategories">Select All Categories</label>
                    </div>
                    <hr>
                    @php
                        $categories = \App\Models\MarketplaceCategory::where('status',1)->get();
                    @endphp
                    @foreach($categories as $category)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="mapped_categories[]" class="form-check-input category-checkbox" value="{{ $category->id }}" id="category_{{ $category->id }}">
                            <label class="form-check-label" for="category_{{ $category->id }}">
                                {{ $category->name }}
                                @if($category->parent_id)
                                    <small class="text-muted">(Subcategory)</small>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Mapped Products with Checkboxes -->
        <div class="row mb-3">
            <div class="col-md-12">
                <label class="fw-bold mb-2">Select Products</label>
                <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                    <div class="form-check mb-2">
                        <input type="checkbox" class="form-check-input" id="selectAllProducts">
                        <label class="form-check-label fw-bold" for="selectAllProducts">Select All Products</label>
                    </div>
                    <hr>
                    @php
                        $products = \DB::table('products')
                            ->leftJoin('sellers', 'products.created_by_id', '=', 'sellers.id')
                            ->select('products.*', 'sellers.name as seller_name')
                          
                            ->get();
                    @endphp
                    @foreach($products as $product)
                        <div class="form-check mb-2">
                            <input type="checkbox" name="mapped_products[]" class="form-check-input product-checkbox" value="{{ $product->id }}" id="product_{{ $product->id }}">
                            <label class="form-check-label" for="product_{{ $product->id }}">
                                <strong>{{ $product->name }}</strong>
                                @if($product->seller_name)
                                    <small class="text-muted">- by {{ $product->seller_name }}</small>
                                @endif
                                @if($product->price)
                                    <small class="text-success"> (${{ number_format($product->price, 2) }})</small>
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Selected Count Display -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="alert alert-info">
                    <strong>Selected:</strong>
                    <span id="selectedSellersCount">0</span> Sellers | 
                    <span id="selectedCategoriesCount">0</span> Categories | 
                    <span id="selectedProductsCount">0</span> Products
                </div>
            </div>
        </div>
    </div>
</div>
                        </div>
                    </div>

                    <!-- CTA TAB -->
                    <div class="tab-pane" id="cta-tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="section-card">
                                    <h5>Primary CTA Button</h5>
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-md-4">
                                            <label>Button Text</label>
                                            <input type="text" name="primary_cta_text" class="form-control mb-3" placeholder="Book Now">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Button URL</label>
                                            <input type="url" name="primary_cta_url" class="form-control mb-3" placeholder="https://example.com/book">
                                        </div>

                                        <div class="col-md-4">
                                            <label>Position</label>
                                            <select name="primary_cta_position" class="form-control mb-3">
                                                <option value="top">Top</option>
                                                <option value="middle">Middle</option>
                                                <option value="bottom">Bottom</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="section-card">
                                    <h5>Secondary CTA Button</h5>
                                    <hr>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label>Button Text</label>
                                            <input type="text" name="secondary_cta_text" class="form-control mb-3" placeholder="Learn More">
                                        </div>

                                        <div class="col-md-6">
                                            <label>Button URL</label>
                                            <input type="url" name="secondary_cta_url" class="form-control mb-3" placeholder="https://example.com/learn">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- APPROVAL WORKFLOW TAB -->
                    <div class="tab-pane" id="approval-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Approval Status</label>
                                <select name="approval_status" class="form-control mb-3">
                                    <option value="draft">Draft</option>
                                    <option value="pending_review">Pending Review</option>
                                    <option value="approved">Approved</option>
                                    <option value="published">Published</option>
                                    <option value="rejected">Rejected</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label>Schedule Publishing</label>
                                <div class="form-check mb-3">
                                    <input type="checkbox" name="is_scheduled" class="form-check-input" value="1" id="isScheduled">
                                    <label class="form-check-label" for="isScheduled">Schedule this page for future publishing</label>
                                </div>
                            </div>

                            <div class="col-md-6" id="scheduleDateDiv" style="display:none;">
                                <label>Publish Date & Time</label>
                                <input type="datetime-local" name="schedule_publish_at" class="form-control mb-3">
                            </div>

                            <div class="col-md-12">
                                <label>Rejection Reason (if rejected)</label>
                                <textarea name="rejection_reason" class="form-control mb-3" placeholder="Reason for rejection..." rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- FEATURES & BLOCKS TAB -->
                    <div class="tab-pane" id="features-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="section-card">
                                    <h5>Featured & Sponsored</h5>
                                    <hr>
                                    
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="is_featured" class="form-check-input" value="1" id="isFeatured">
                                        <label class="form-check-label" for="isFeatured">Mark as Featured Page</label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="is_sponsored" class="form-check-input" value="1" id="isSponsored">
                                        <label class="form-check-label" for="isSponsored">Mark as Sponsored</label>
                                    </div>

                                    <div id="sponsoredFields" style="display:none;">
                                        <label>Sponsored Until</label>
                                        <input type="date" name="sponsored_until" class="form-control mb-3">
                                    </div>

                                    <label>Featured Order</label>
                                    <input type="number" name="featured_order" class="form-control mb-3" placeholder="1, 2, 3...">
                                </div>

                                <div class="section-card">
                                    <h5>AI Features</h5>
                                    <hr>
                                    
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="enable_ai_blocks" class="form-check-input" value="1" id="enableAI">
                                        <label class="form-check-label" for="enableAI">Enable AI Recommendation Blocks</label>
                                    </div>

                                    <div id="aiFields" style="display:none;">
                                        <label>AI Recommendation Type</label>
                                        <select name="ai_recommendation_type" class="form-control mb-3">
                                            <option value="related">Related Content</option>
                                            <option value="popular">Popular Content</option>
                                            <option value="personalized">Personalized</option>
                                        </select>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="auto_internal_linking" class="form-check-input" value="1" id="autoLinking">
                                        <label class="form-check-label" for="autoLinking">Auto Internal Linking</label>
                                    </div>

                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="enable_smart_suggestions" class="form-check-input" value="1" id="smartSuggestions">
                                        <label class="form-check-label" for="smartSuggestions">Enable Smart Suggestions</label>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="section-card">
                                    <h5>Analytics & Tracking</h5>
                                    <hr>
                                    
                                    <label>Analytics ID</label>
                                    <input type="text" name="analytics_id" class="form-control mb-3" placeholder="GA4, Facebook Pixel ID, etc.">

                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="track_conversions" class="form-check-input" value="1" id="trackConversions">
                                        <label class="form-check-label" for="trackConversions">Track Conversions</label>
                                    </div>

                                    <label>Conversion Goals (JSON)</label>
                                    <textarea name="conversion_goals" class="form-control mb-3" placeholder='[{"goal":"purchase","value":100}]' rows="3"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ADS & BANNERS TAB -->
                    <div class="tab-pane" id="ads-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="section-card">
                                    <h5>Advertisement Settings</h5>
                                    <hr>
                                    
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="show_ads" class="form-check-input" value="1" id="showAds" checked>
                                        <label class="form-check-label" for="showAds">Show Ads on this Page</label>
                                    </div>

                                    <label>Ad Position</label>
                                    <select name="ad_position" class="form-control mb-3">
                                        <option value="top">Top</option>
                                        <option value="sidebar">Sidebar</option>
                                        <option value="bottom">Bottom</option>
                                        <option value="between_content">Between Content</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="section-card">
                                    <h5>Banner Settings</h5>
                                    <hr>
                                    
                                    <div class="form-check mb-3">
                                        <input type="checkbox" name="show_banners" class="form-check-input" value="1" id="showBanners" checked>
                                        <label class="form-check-label" for="showBanners">Show Banners on this Page</label>
                                    </div>

                                    <label>Banner Position</label>
                                    <select name="banner_position" class="form-control mb-3">
                                        <option value="header">Header</option>
                                        <option value="sidebar">Sidebar</option>
                                        <option value="footer">Footer</option>
                                        <option value="popup">Popup</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button class="btn btn-success">Save Page</button>
                        <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // Tab switching functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Get all tab links
        const tabLinks = document.querySelectorAll('.nav-tabs .nav-link');
        const tabPanes = document.querySelectorAll('.tab-pane');

        // Function to switch tab
        function switchTab(tabId) {
            // Remove active class from all tabs and panes
            tabLinks.forEach(link => {
                link.classList.remove('active');
            });
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
            });

            // Add active class to selected tab and pane
            const selectedTab = document.querySelector(`.nav-link[data-tab="${tabId}"]`);
            const selectedPane = document.getElementById(`${tabId}-tab`);
            
            if (selectedTab) selectedTab.classList.add('active');
            if (selectedPane) selectedPane.classList.add('active');
        }

        // Add click event to each tab
        tabLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const tabId = this.getAttribute('data-tab');
                switchTab(tabId);
            });
        });

        // Initialize CKEditor
        if (document.querySelector('#editor')) {
            ClassicEditor
                .create(document.querySelector('#editor'), {
                    height: '400px'
                })
                .catch(error => {
                    console.error(error);
                });
        }

        // Image preview
        const imageInput = document.getElementById('imageInput');
        if (imageInput) {
            imageInput.addEventListener('change', function(e) {
                let preview = document.getElementById('preview');
                if (preview) {
                    preview.innerHTML = '';
                    Array.from(e.target.files).forEach(file => {
                        let reader = new FileReader();
                        reader.onload = function(event) {
                            let img = document.createElement('img');
                            img.src = event.target.result;
                            img.style.width = '100px';
                            img.style.height = '100px';
                            img.style.objectFit = 'cover';
                            img.style.borderRadius = '6px';
                            img.style.margin = '5px';
                            preview.appendChild(img);
                        }
                        reader.readAsDataURL(file);
                    });
                }
            });
        }

        // Show/hide schedule date field
        const scheduledCheckbox = document.getElementById('isScheduled');
        if (scheduledCheckbox) {
            scheduledCheckbox.addEventListener('change', function() {
                const scheduleDiv = document.getElementById('scheduleDateDiv');
                if (scheduleDiv) {
                    scheduleDiv.style.display = this.checked ? 'block' : 'none';
                }
            });
        }

        // Show/hide sponsored fields
        const sponsoredCheckbox = document.getElementById('isSponsored');
        if (sponsoredCheckbox) {
            sponsoredCheckbox.addEventListener('change', function() {
                const sponsoredDiv = document.getElementById('sponsoredFields');
                if (sponsoredDiv) {
                    sponsoredDiv.style.display = this.checked ? 'block' : 'none';
                }
            });
        }

        // Show/hide AI fields
        const aiCheckbox = document.getElementById('enableAI');
        if (aiCheckbox) {
            aiCheckbox.addEventListener('change', function() {
                const aiDiv = document.getElementById('aiFields');
                if (aiDiv) {
                    aiDiv.style.display = this.checked ? 'block' : 'none';
                }
            });
        }
    });
    
    // Seller & Product Mapping Checkbox Functionality
document.addEventListener('DOMContentLoaded', function() {
    
    // Update selected counts function
    function updateSelectedCounts() {
        const selectedSellers = document.querySelectorAll('.seller-checkbox:checked').length;
        const selectedCategories = document.querySelectorAll('.category-checkbox:checked').length;
        const selectedProducts = document.querySelectorAll('.product-checkbox:checked').length;
        
        document.getElementById('selectedSellersCount').textContent = selectedSellers;
        document.getElementById('selectedCategoriesCount').textContent = selectedCategories;
        document.getElementById('selectedProductsCount').textContent = selectedProducts;
    }
    
    // Select All Sellers
    const selectAllSellers = document.getElementById('selectAllSellers');
    if (selectAllSellers) {
        selectAllSellers.addEventListener('change', function() {
            const sellerCheckboxes = document.querySelectorAll('.seller-checkbox');
            sellerCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllSellers.checked;
            });
            updateSelectedCounts();
        });
    }
    
    // Select All Categories
    const selectAllCategories = document.getElementById('selectAllCategories');
    if (selectAllCategories) {
        selectAllCategories.addEventListener('change', function() {
            const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
            categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllCategories.checked;
            });
            updateSelectedCounts();
        });
    }
    
    // Select All Products
    const selectAllProducts = document.getElementById('selectAllProducts');
    if (selectAllProducts) {
        selectAllProducts.addEventListener('change', function() {
            const productCheckboxes = document.querySelectorAll('.product-checkbox');
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = selectAllProducts.checked;
            });
            updateSelectedCounts();
        });
    }
    
    // Individual checkbox listeners
    function addIndividualListeners() {
        const sellerCheckboxes = document.querySelectorAll('.seller-checkbox');
        sellerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCounts);
        });
        
        const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCounts);
        });
        
        const productCheckboxes = document.querySelectorAll('.product-checkbox');
        productCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateSelectedCounts);
        });
    }
    
    addIndividualListeners();
    updateSelectedCounts();
    
    // Search/filter functionality for sellers
    const addSearchFilter = () => {
        // Add search input for sellers
        const sellersDiv = document.querySelector('.seller-checkbox')?.closest('.border');
        if (sellersDiv && !document.getElementById('sellerSearch')) {
            const searchHtml = `
                <div class="mb-3">
                    <input type="text" id="sellerSearch" class="form-control" placeholder="Search sellers...">
                </div>
            `;
            sellersDiv.insertAdjacentHTML('afterbegin', searchHtml);
            
            document.getElementById('sellerSearch')?.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const sellerItems = document.querySelectorAll('.seller-checkbox');
                sellerItems.forEach(checkbox => {
                    const label = checkbox.nextElementSibling;
                    const text = label.textContent.toLowerCase();
                    const container = checkbox.closest('.form-check');
                    if (container) {
                        container.style.display = text.includes(searchTerm) ? '' : 'none';
                    }
                });
            });
        }
        
        // Add search input for categories
        const categoriesDiv = document.querySelector('.category-checkbox')?.closest('.border');
        if (categoriesDiv && !document.getElementById('categorySearch')) {
            const searchHtml = `
                <div class="mb-3">
                    <input type="text" id="categorySearch" class="form-control" placeholder="Search categories...">
                </div>
            `;
            categoriesDiv.insertAdjacentHTML('afterbegin', searchHtml);
            
            document.getElementById('categorySearch')?.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const categoryItems = document.querySelectorAll('.category-checkbox');
                categoryItems.forEach(checkbox => {
                    const label = checkbox.nextElementSibling;
                    const text = label.textContent.toLowerCase();
                    const container = checkbox.closest('.form-check');
                    if (container) {
                        container.style.display = text.includes(searchTerm) ? '' : 'none';
                    }
                });
            });
        }
        
        // Add search input for products
        const productsDiv = document.querySelector('.product-checkbox')?.closest('.border');
        if (productsDiv && !document.getElementById('productSearch')) {
            const searchHtml = `
                <div class="mb-3">
                    <input type="text" id="productSearch" class="form-control" placeholder="Search products...">
                    <div class="mt-2">
                        <button type="button" id="filterInStock" class="btn btn-sm btn-outline-success">In Stock</button>
                        <button type="button" id="filterLowStock" class="btn btn-sm btn-outline-warning">Low Stock</button>
                        <button type="button" id="showAllProducts" class="btn btn-sm btn-outline-secondary">Show All</button>
                    </div>
                </div>
            `;
            productsDiv.insertAdjacentHTML('afterbegin', searchHtml);
            
            document.getElementById('productSearch')?.addEventListener('keyup', function() {
                const searchTerm = this.value.toLowerCase();
                const productItems = document.querySelectorAll('.product-checkbox');
                productItems.forEach(checkbox => {
                    const label = checkbox.nextElementSibling;
                    const text = label.textContent.toLowerCase();
                    const container = checkbox.closest('.form-check');
                    if (container) {
                        container.style.display = text.includes(searchTerm) ? '' : 'none';
                    }
                });
            });
        }
    };
    
    // Call search filter after a short delay
    setTimeout(addSearchFilter, 500);
});
</script>

// Add this script to your create.blade.php file
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-remove error messages when user starts typing
    const formInputs = document.querySelectorAll('input, select, textarea');
    
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            // Remove error message for this specific field
            const errorDiv = document.getElementById(`${this.name}-error`);
            if (errorDiv) {
                errorDiv.remove();
            }
            
            // Remove error class from input
            this.classList.remove('is-invalid');
            
            // Remove the general error message for this field
            const fieldError = document.querySelector(`.invalid-feedback[data-field="${this.name}"]`);
            if (fieldError) {
                fieldError.remove();
            }
        });
        
        // For select elements
        input.addEventListener('change', function() {
            const errorDiv = document.getElementById(`${this.name}-error`);
            if (errorDiv) {
                errorDiv.remove();
            }
            this.classList.remove('is-invalid');
        });
    });
    
    // Remove all validation errors after 5 seconds of no interaction
    let errorTimeout;
    const removeErrorsAfterDelay = () => {
        clearTimeout(errorTimeout);
        errorTimeout = setTimeout(() => {
            const allErrors = document.querySelectorAll('.invalid-feedback, .text-danger');
            allErrors.forEach(error => {
                if (!error.closest('.alert')) {
                    error.style.opacity = '0';
                    setTimeout(() => {
                        if (error.parentNode) error.remove();
                    }, 300);
                }
            });
            const invalidInputs = document.querySelectorAll('.is-invalid');
            invalidInputs.forEach(input => {
                input.classList.remove('is-invalid');
            });
        }, 5000);
    };
    
    // Track user activity
    const activityEvents = ['keydown', 'click', 'change', 'input'];
    activityEvents.forEach(event => {
        document.addEventListener(event, removeErrorsAfterDelay);
    });
    
    // Display validation errors nicely
    const validationErrors = document.querySelectorAll('.invalid-feedback');
    if (validationErrors.length > 0) {
        // Scroll to first error
        const firstError = document.querySelector('.is-invalid');
        if (firstError) {
            firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
            firstError.focus();
        }
        
        // Show error summary
        const errorSummary = document.createElement('div');
        errorSummary.className = 'alert alert-danger alert-dismissible fade show';
        errorSummary.innerHTML = `
            <strong><i class="fas fa-exclamation-triangle"></i> Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                ${Array.from(validationErrors).map(error => `<li>${error.textContent}</li>`).join('')}
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        `;
        
        const form = document.querySelector('form');
        if (form && !document.querySelector('.alert-danger')) {
            form.insertBefore(errorSummary, form.firstChild);
        }
        
        // Auto-dismiss error summary after 10 seconds
        setTimeout(() => {
            const alert = document.querySelector('.alert-danger');
            if (alert) {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }
        }, 10000);
    }
});
</script>

<style>
/* Error styling */
.is-invalid {
    border-color: #dc3545 !important;
    border-width: 2px !important;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block !important;
}

/* Add error styles for form groups */
.form-group.has-error .form-control {
    border-color: #dc3545;
}

/* Fade out animation for errors */
.invalid-feedback {
    transition: opacity 0.3s ease;
}

/* Success animation for valid fields */
.form-control.is-valid {
    border-color: #28a745;
    transition: border-color 0.3s ease;
}

/* Loading state for submit button */
.btn-submit.loading {
    opacity: 0.7;
    pointer-events: none;
}

.btn-submit.loading:after {
    content: " ...";
    animation: dots 1.5s steps(5, end) infinite;
}

@keyframes dots {
    0%, 20% { content: " ."; }
    40% { content: " .."; }
    60%, 100% { content: " ..."; }
}
</style>


@endsection
