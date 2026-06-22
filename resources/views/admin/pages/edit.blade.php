@extends('layouts.vertical', ['title' => 'Edit Page'])

@section('content')
<style>
    .nav-tabs .nav-link { color: #495057; cursor: pointer; }
    .nav-tabs .nav-link.active { color: #2c7fc3; font-weight: 600; border-bottom: 2px solid #2c7fc3; }
    .tab-pane { display: none; }
    .tab-pane.active { display: block; }
    .section-card { background: #f8f9fa; border-radius: 8px; padding: 15px; margin-bottom: 20px; border: 1px solid #e9ecef; }
</style>

<div class="container-fluid pt-2">
    <h4 class="mb-3">Edit Page: {{ $page->title }}</h4>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('pages.update', $page->id) }}" enctype="multipart/form-data">
                @csrf
                @method('POST')

                <ul class="nav nav-tabs" id="pageTabs" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-tab="basic" href="javascript:void(0);">Basic Info</a></li>
                    <li class="nav-item"><a class="nav-link" data-tab="seo" href="javascript:void(0);">SEO & Meta</a></li>
                    <li class="nav-item"><a class="nav-link" data-tab="travel" href="javascript:void(0);">Travel Details</a></li>
                    <li class="nav-item"><a class="nav-link" data-tab="mapping" href="javascript:void(0);">Seller/Product Mapping</a></li>
                    <li class="nav-item"><a class="nav-link" data-tab="cta" href="javascript:void(0);">CTA & Links</a></li>
                    <li class="nav-item"><a class="nav-link" data-tab="features" href="javascript:void(0);">AI & Features</a></li>
                    <li class="nav-item"><a class="nav-link" data-tab="analytics" href="javascript:void(0);">Analytics</a></li>
                    <li class="nav-item"><a class="nav-link" data-tab="scheduling" href="javascript:void(0);">Scheduling</a></li>
                    <li class="nav-item"><a class="nav-link" data-tab="approval" href="javascript:void(0);">Approval Workflow</a></li>
                </ul>

                <div class="tab-content">
                    <!-- ==================== BASIC INFO TAB ==================== -->
                    <div class="tab-pane active" id="basic-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Page Title <span class="text-danger">*</span></label>
                                <input type="text" name="title" class="form-control mb-3" value="{{ old('title', $page->title) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label>Custom Slug (URL)</label>
                                <input type="text" name="slug" class="form-control mb-3" value="{{ old('slug', $page->slug) }}" placeholder="auto-from-title">
                            </div>
                            <div class="col-md-6">
                                <label>Page Type</label>
                                <input type="text" name="page_type" class="form-control mb-3" value="{{ old('page_type', $page->page_type) }}">
                            </div>
                            <div class="col-md-6">
                                <label>Template</label>
                                <input type="text" name="template" class="form-control mb-3" value="{{ old('template', $page->template) }}">
                            </div>
                            <div class="col-md-6">
                                <label>App Type</label>
                                <select name="app_type" class="form-control mb-3">
                                    <option value="all" {{ $page->app_type == 'all' ? 'selected' : '' }}>All</option>
                                    <option value="vshop" {{ $page->app_type == 'vshop' ? 'selected' : '' }}>VShop</option>
                                    <option value="vtravel" {{ $page->app_type == 'vtravel' ? 'selected' : '' }}>VTravel</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Page Content <span class="text-danger">*</span></label>
                                <textarea name="content" id="editor" class="form-control mb-3" required>{{ old('content', $page->content) }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label>Excerpt (Short Description)</label>
                                <textarea name="excerpt" class="form-control mb-3" rows="2">{{ old('excerpt', $page->excerpt) }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label>Featured Image</label>
                                <input type="file" name="featured_image" class="form-control mb-3">
                                @if($page->featured_image)
                                    <img src="{{ Storage::url($page->featured_image) }}" width="100" class="mt-2">
                                @endif
                            </div>
                            <div class="col-md-12">
                                <label>Upload Multiple Images</label>
                                <input type="file" name="images[]" multiple class="form-control mb-3">
                                <div class="row">
                                    @foreach($page->images as $img)
                                    <div class="col-auto">
                                        <img src="{{ Storage::url($img->image) }}" width="80" height="80" style="object-fit:cover;">
                                        <button type="button" class="btn btn-sm btn-danger remove-image" data-id="{{ $img->id }}">×</button>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== SEO TAB ==================== -->
                    <div class="tab-pane" id="seo-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <label>SEO Title</label>
                                <input type="text" name="seo_title" class="form-control mb-3" value="{{ old('seo_title', $page->seo_title) }}">
                            </div>
                            <div class="col-md-6">
                                <label>SEO Description</label>
                                <textarea name="seo_description" class="form-control mb-3" rows="2">{{ old('seo_description', $page->seo_description) }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label>SEO Keywords</label>
                                <input type="text" name="seo_keywords" class="form-control mb-3" value="{{ old('seo_keywords', $page->seo_keywords) }}">
                            </div>
                            <div class="col-md-6">
                                <label>Canonical URL</label>
                                <input type="url" name="canonical_url" class="form-control mb-3" value="{{ old('canonical_url', $page->canonical_url) }}">
                            </div>
                            <div class="col-md-6">
                                <label>Robots</label>
                                <input type="text" name="robots" class="form-control mb-3" value="{{ old('robots', $page->robots) }}">
                            </div>
                            <div class="col-md-12">
                                <h5>Open Graph (Facebook)</h5>
                                <hr>
                            </div>
                            <div class="col-md-4">
                                <label>OG Title</label>
                                <input type="text" name="og_title" class="form-control mb-3" value="{{ old('og_title', $page->og_title) }}">
                            </div>
                            <div class="col-md-4">
                                <label>OG Description</label>
                                <textarea name="og_description" class="form-control mb-3" rows="2">{{ old('og_description', $page->og_description) }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label>OG Image</label>
                                <input type="file" name="og_image" class="form-control mb-3">
                                @if($page->og_image) <img src="{{ Storage::url($page->og_image) }}" width="80"> @endif
                            </div>
                            <div class="col-md-12">
                                <h5>Twitter Card</h5>
                                <hr>
                            </div>
                            <div class="col-md-4">
                                <label>Twitter Card Type</label>
                                <input type="text" name="twitter_card" class="form-control mb-3" value="{{ old('twitter_card', $page->twitter_card) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Twitter Title</label>
                                <input type="text" name="twitter_title" class="form-control mb-3" value="{{ old('twitter_title', $page->twitter_title) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Twitter Description</label>
                                <textarea name="twitter_description" class="form-control mb-3" rows="2">{{ old('twitter_description', $page->twitter_description) }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <label>Twitter Image</label>
                                <input type="file" name="twitter_image" class="form-control mb-3">
                                @if($page->twitter_image) <img src="{{ Storage::url($page->twitter_image) }}" width="80"> @endif
                            </div>
                            <div class="col-md-12">
                                <label>Schema Markup (JSON-LD)</label>
                                <textarea name="schema_markup" class="form-control mb-3" rows="5">{{ old('schema_markup', $page->schema_markup) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== TRAVEL DETAILS TAB ==================== -->
                    <div class="tab-pane" id="travel-tab">
                        <div class="row">
                            <div class="col-md-4">
                                <label>Price</label>
                                <input type="number" step="0.01" name="travel_price" class="form-control mb-3" value="{{ old('travel_price', $page->travel_price) }}">
                            </div>
                            <div class="col-md-4">
                                <label>Duration</label>
                                <input type="text" name="travel_duration" class="form-control mb-3" value="{{ old('travel_duration', $page->travel_duration) }}">
                            </div>
                            <div class="col-md-12">
                                <label>Inclusions</label>
                                <textarea name="travel_inclusions" class="form-control mb-3" rows="3">{{ old('travel_inclusions', $page->travel_inclusions) }}</textarea>
                            </div>
                            <div class="col-md-12">
                                <label>Exclusions</label>
                                <textarea name="travel_exclusions" class="form-control mb-3" rows="3">{{ old('travel_exclusions', $page->travel_exclusions) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== MAPPING TAB ==================== -->
                    <div class="tab-pane" id="mapping-tab">
                        <div class="section-card">
                            <h5>Map Sellers, Categories, Products</h5>
                            <hr>
                            @php
                                $selectedSellers = $page->mapped_sellers ?? [];
                                $selectedCategories = $page->mapped_categories ?? [];
                                $selectedProducts = $page->mapped_products ?? [];
                            @endphp
                            <div class="row">
                                <div class="col-md-4">
                                    <label>Sellers</label>
                                    <select name="mapped_sellers[]" class="form-control" multiple size="8">
                                        @foreach($sellers as $seller)
                                            <option value="{{ $seller->id }}" {{ in_array($seller->id, $selectedSellers) ? 'selected' : '' }}>{{ $seller->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Categories</label>
                                    <select name="mapped_categories[]" class="form-control" multiple size="8">
                                        @foreach($categories as $cat)
                                            <option value="{{ $cat->id }}" {{ in_array($cat->id, $selectedCategories) ? 'selected' : '' }}>{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label>Products</label>
                                    <select name="mapped_products[]" class="form-control" multiple size="8">
                                        @foreach($products as $prod)
                                            <option value="{{ $prod->id }}" {{ in_array($prod->id, $selectedProducts) ? 'selected' : '' }}>{{ $prod->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== CTA TAB ==================== -->
                    <div class="tab-pane" id="cta-tab">
                        <div class="section-card">
                            <h5>Primary CTA Button</h5>
                            <div class="row">
                                <div class="col-md-4"><label>Text</label><input type="text" name="cta_text" class="form-control" value="{{ old('cta_text', $page->cta_text) }}"></div>
                                <div class="col-md-4"><label>URL</label><input type="url" name="cta_link" class="form-control" value="{{ old('cta_link', $page->cta_link) }}"></div>
                                <div class="col-md-4"><label>Position</label><select name="cta_position" class="form-control"><option value="top" {{ $page->cta_position=='top'?'selected':'' }}>Top</option><option value="middle" {{ $page->cta_position=='middle'?'selected':'' }}>Middle</option><option value="bottom" {{ $page->cta_position=='bottom'?'selected':'' }}>Bottom</option></select></div>
                            </div>
                        </div>
                        <div class="section-card">
                            <h5>Secondary CTA Button</h5>
                            <div class="row">
                                <div class="col-md-6"><label>Text</label><input type="text" name="secondary_cta_text" class="form-control" value="{{ old('secondary_cta_text', $page->secondary_cta_text) }}"></div>
                                <div class="col-md-6"><label>URL</label><input type="url" name="secondary_cta_link" class="form-control" value="{{ old('secondary_cta_link', $page->secondary_cta_link) }}"></div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== AI & FEATURES TAB ==================== -->
                    <div class="tab-pane" id="features-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check"><input type="checkbox" name="enable_ai_blocks" value="1" {{ $page->enable_ai_blocks ? 'checked' : '' }}> <label>Enable AI Recommendation Blocks</label></div>
                                <div class="form-check"><input type="checkbox" name="auto_internal_linking" value="1" {{ $page->auto_internal_linking ? 'checked' : '' }}> <label>Auto Internal Linking</label></div>
                                <div class="form-check"><input type="checkbox" name="enable_smart_suggestions" value="1" {{ $page->enable_smart_suggestions ? 'checked' : '' }}> <label>Smart Suggestions</label></div>
                                <div class="form-check"><input type="checkbox" name="is_featured" value="1" {{ $page->is_featured ? 'checked' : '' }}> <label>Mark as Featured Page</label></div>
                                <div class="form-check"><input type="checkbox" name="is_sponsored" value="1" {{ $page->is_sponsored ? 'checked' : '' }}> <label>Mark as Sponsored</label></div>
                                <div id="sponsoredFields" style="{{ $page->is_sponsored ? '' : 'display:none;' }}">
                                    <label>Sponsored Until</label>
                                    <input type="date" name="sponsored_until" class="form-control mb-2" value="{{ old('sponsored_until', $page->sponsored_until) }}">
                                </div>
                                <label>Featured Order</label>
                                <input type="number" name="featured_order" class="form-control" value="{{ old('featured_order', $page->featured_order) }}">
                            </div>
                        </div>
                    </div>

                    <!-- ==================== ANALYTICS TAB ==================== -->
                    <div class="tab-pane" id="analytics-tab">
                        <div class="row">
                            <div class="col-md-6"><label>Analytics ID (GA4, Pixel)</label><input type="text" name="analytics_id" class="form-control" value="{{ old('analytics_id', $page->analytics_id) }}"></div>
                            <div class="col-md-6"><div class="form-check"><input type="checkbox" name="track_conversions" value="1" {{ $page->track_conversions ? 'checked' : '' }}> <label>Track Conversions</label></div></div>
                            <div class="col-md-12"><label>Conversion Goals (JSON)</label><textarea name="conversion_goals" class="form-control" rows="3">{{ old('conversion_goals', $page->conversion_goals) }}</textarea></div>
                        </div>
                    </div>

                    <!-- ==================== SCHEDULING TAB ==================== -->
                    <div class="tab-pane" id="scheduling-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-check"><input type="checkbox" name="is_scheduled" value="1" id="isScheduled" {{ $page->scheduled_publish_at ? 'checked' : '' }}> <label>Schedule Publishing</label></div>
                                <div id="scheduleDateDiv" style="{{ $page->scheduled_publish_at ? '' : 'display:none;' }}">
                                    <label>Publish Date & Time</label>
                                    <input type="datetime-local" name="scheduled_publish_at" class="form-control" value="{{ old('scheduled_publish_at', $page->scheduled_publish_at ? \Carbon\Carbon::parse($page->scheduled_publish_at)->format('Y-m-d\TH:i') : '') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- ==================== APPROVAL WORKFLOW TAB ==================== -->
                    <div class="tab-pane" id="approval-tab">
                        <div class="row">
                            <div class="col-md-6">
                                <label>Status</label>
                                <select name="status" class="form-control">
                                    <option value="draft" {{ $page->status == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="pending" {{ $page->status == 'pending' ? 'selected' : '' }}>Pending Review</option>
                                    <option value="approved" {{ $page->status == 'approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="published" {{ $page->status == 'published' ? 'selected' : '' }}>Published</option>
                                    <option value="rejected" {{ $page->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label>Rejection Reason</label>
                                <textarea name="rejection_reason" class="form-control" rows="3">{{ old('rejection_reason', $page->rejection_reason) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-md-12">
                        <button class="btn btn-success">Update Page</button>
                        <a href="{{ route('pages.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    // Tab switching
    document.querySelectorAll('.nav-tabs .nav-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('.nav-tabs .nav-link').forEach(l => l.classList.remove('active'));
            document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            document.getElementById(this.getAttribute('data-tab') + '-tab').classList.add('active');
        });
    });
    // CKEditor
    ClassicEditor.create(document.querySelector('#editor')).catch(e => console.error(e));
    // Show/hide scheduled date
    document.getElementById('isScheduled')?.addEventListener('change', function() {
        document.getElementById('scheduleDateDiv').style.display = this.checked ? 'block' : 'none';
    });
    // Sponsored fields
    document.querySelector('input[name="is_sponsored"]')?.addEventListener('change', function() {
        document.getElementById('sponsoredFields').style.display = this.checked ? 'block' : 'none';
    });
    // Remove image (ajax)
    document.querySelectorAll('.remove-image').forEach(btn => {
        btn.addEventListener('click', function() {
            if(confirm('Delete this image?')) {
                let id = this.dataset.id;
                fetch(`/admin/pages/image/delete/${id}`, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } })
                .then(res => res.json()).then(data => { if(data.success) location.reload(); });
            }
        });
    });
</script>
@endsection