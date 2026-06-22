@extends('vshop.seller.main')

@section('title', 'Edit Product')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">
                
                <!-- Page Header -->
                <div class="dashboardTop">
                    <div class="dashboardTopLeft">
                        <h2>Edit {{ $product->type == 'travel' ? 'Tour Package' : 'Product' }}</h2>
                    </div>
                    <div class="dashboardTopRight">
                        <a href="{{ route('seller.products.index') }}" class="backBtn">
                            <i class="fa-light fa-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <!-- Edit Form -->
                <div class="formCard">
                    <form action="{{ route('seller.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Basic Information -->
                            <div class="col-md-8">
                                <div class="formSection">
                                    <h3>Basic Information</h3>
                                    
                                    <div class="mb-3">
                                        <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                               id="name" name="name" value="{{ old('name', $product->name) }}" required>
                                        @error('name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    @if($product->type != 'travel')
                                    <div class="mb-3">
                                        <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                               id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                                        @error('sku')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endif

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="category_id" class="form-label">Category <span class="text-danger">*</span></label>
                                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                                    id="category_id" name="category_id" required>
                                                <option value="">Select Category</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('category_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="col-md-6 mb-3">
                                            <label for="brand_id" class="form-label">{{ $product->type == 'travel' ? 'Location' : 'Brand' }} <span class="text-danger">*</span></label>
                                            <select class="form-select @error('brand_id') is-invalid @enderror" 
                                                    id="brand_id" name="brand_id" required>
                                                <option value="">Select {{ $product->type == 'travel' ? 'Location' : 'Brand' }}</option>
                                                @foreach($brands as $brand)
                                                    <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>
                                                        {{ $brand->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('brand_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    @if($product->type != 'travel')
                                    <div class="mb-3">
                                        <label for="product_type" class="form-label">Product Type <span class="text-danger">*</span></label>
                                        <select class="form-select @error('product_type') is-invalid @enderror" 
                                                id="product_type" name="product_type" required>
                                            <option value="">Select Type</option>
                                            <option value="simple" {{ old('product_type', $product->product_type) == 'simple' ? 'selected' : '' }}>Simple Product</option>
                                            <option value="attribute" {{ old('product_type', $product->product_type) == 'attribute' ? 'selected' : '' }}>Variable Product (Size/Color)</option>
                                            <option value="combo" {{ old('product_type', $product->product_type) == 'combo' ? 'selected' : '' }}>Combo Product</option>
                                        </select>
                                        @error('product_type')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    @endif

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Description</label>
                                        <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $product->description) }}</textarea>
                                    </div>
                                </div>
                            </div>

                            <!-- Pricing & Media -->
                            <div class="col-md-4">
                                <!-- Pricing -->
                                <div class="formSection mb-4">
                                    <h3>Pricing</h3>
                                    
                                    <div class="mb-3">
                                        <label for="price" class="form-label">Selling Price <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" 
                                                   id="price" name="price" value="{{ old('price', $product->price) }}" required>
                                        </div>
                                        @error('price')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="regular_price" class="form-label">Regular Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" class="form-control" 
                                                   id="regular_price" name="regular_price" value="{{ old('regular_price', $product->regular_price) }}">
                                        </div>
                                    </div>

                                    @if($product->type != 'travel')
                                    <div class="mb-3">
                                        <label for="discount_price" class="form-label">Discount Price</label>
                                        <div class="input-group">
                                            <span class="input-group-text">₹</span>
                                            <input type="number" step="0.01" class="form-control" 
                                                   id="discount_price" name="discount_price" value="{{ old('discount_price', $product->discount_price) }}">
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                <!-- Travel Specific Fields -->
                                @if($product->type == 'travel')
                                <div class="formSection mb-4">
                                    <h3>Tour Details</h3>
                                    
                                    <div class="mb-3">
                                        <label for="duration" class="form-label">Duration <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="duration" name="duration" 
                                               value="{{ old('duration', $product->duration) }}" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="tour_type" class="form-label">Tour Type <span class="text-danger">*</span></label>
                                        <select class="form-select" id="tour_type" name="tour_type" required>
                                            <option value="">Select Type</option>
                                            <option value="domestic" {{ old('tour_type', $product->tour_type) == 'domestic' ? 'selected' : '' }}>Domestic</option>
                                            <option value="international" {{ old('tour_type', $product->tour_type) == 'international' ? 'selected' : '' }}>International</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="highlights" class="form-label">Highlights</label>
                                        <textarea class="form-control" id="highlights" name="highlights" rows="3">{{ old('highlights', $product->highlights) }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="itinerary" class="form-label">Itinerary</label>
                                        <textarea class="form-control" id="itinerary" name="itinerary" rows="3">{{ old('itinerary', $product->itinerary) }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="inclusions" class="form-label">Inclusions</label>
                                        <textarea class="form-control" id="inclusions" name="inclusions" rows="2">{{ old('inclusions', $product->inclusions) }}</textarea>
                                    </div>

                                    <div class="mb-3">
                                        <label for="exclusions" class="form-label">Exclusions</label>
                                        <textarea class="form-control" id="exclusions" name="exclusions" rows="2">{{ old('exclusions', $product->exclusions) }}</textarea>
                                    </div>
                                </div>
                                @endif

                                <!-- Media -->
                                <div class="formSection mb-4">
                                    <h3>Media</h3>
                                    
                                    <!-- Current Thumbnail -->
                                    @if($product->thumbnail)
                                    <div class="mb-3">
                                        <label class="form-label">Current Thumbnail</label>
                                        <div>
                                            <img src="{{ asset($product->thumbnail) }}" alt="Thumbnail" style="max-width: 150px; border-radius: 5px;">
                                        </div>
                                    </div>
                                    @endif
                                    
                                    <div class="mb-3">
                                        <label for="thumbnail" class="form-label">Update Thumbnail Image</label>
                                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                                        <small class="text-muted">Leave empty to keep current image</small>
                                    </div>

                                    <!-- Current Gallery -->
                                    @if($product->images)
                                        @php $gallery = json_decode($product->images, true); @endphp
                                        @if(!empty($gallery))
                                        <div class="mb-3">
                                            <label class="form-label">Current Gallery Images</label>
                                            <div class="galleryPreview">
                                                @foreach($gallery as $image)
                                                    <img src="{{ asset($image) }}" alt="Gallery" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 5px; margin-bottom: 5px;">
                                                @endforeach
                                            </div>
                                        </div>
                                        @endif
                                    @endif

                                    <div class="mb-3">
                                        <label for="images" class="form-label">Add More Gallery Images</label>
                                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="formSection mb-4">
                                    <h3>Status</h3>
                                    
                                    <div class="mb-3">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="status" name="status" value="1" {{ old('status', $product->status) == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="status">Active</label>
                                        </div>
                                    </div>

                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $product->is_featured) == 1 ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">Mark as Featured</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Section -->
                        <div class="formSection mt-4">
                            <h3>SEO Settings</h3>
                            
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="meta_title" class="form-label">Meta Title</label>
                                    <input type="text" class="form-control" id="meta_title" name="meta_title" value="{{ old('meta_title', $product->meta_title) }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="meta_keywords" class="form-label">Meta Keywords</label>
                                    <input type="text" class="form-control" id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $product->meta_keywords) }}">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label for="meta_description" class="form-label">Meta Description</label>
                                    <textarea class="form-control" id="meta_description" name="meta_description" rows="1">{{ old('meta_description', $product->meta_description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="formActions mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-light fa-save"></i> Update {{ $product->type == 'travel' ? 'Tour' : 'Product' }}
                            </button>
                            <a href="{{ route('seller.products.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection