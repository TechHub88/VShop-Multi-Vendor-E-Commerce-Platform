@extends('layouts.vertical', ['title' => $userType == 'travel' ? 'Add Tour Package' : 'Add Product'])

@section('content')
@php
    $user = auth()->user();
    $userType = $user->type;
    $isTravel = $userType == 'travel';
    $isProduct = $userType == 'product';
@endphp

<style>
.preview-thumb, .preview-gallery img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 6px;
    border: 1px solid #ddd;
}
.preview-gallery img {
    width: 80px; height: 80px; margin-right: 6px; margin-top: 6px;
    position: relative;
}
.gallery-item {
    position: relative;
    display: inline-block;
}
.delete-gallery-btn {
    position: absolute;
    top: -6px;
    right: -6px;
    background: #ff0000;
    color: #fff;
    border-radius: 50%;
    border: none;
    width: 18px;
    height: 18px;
    font-size: 12px;
    line-height: 16px;
}
</style>

<div class="container-fluid">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="product_type" value="{{ $isTravel ? 'simple' : 'simple' }}">
        
        @if($isTravel)
        <!-- Hidden field to identify travel product -->
        <input type="hidden" name="tour_type" value="domestic">
        @endif

        <div class="row">

            <!-- LEFT FORM -->
            <div class="col-lg-8">

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">{{ $isTravel ? 'Add Tour Package' : 'Add Product' }}</h5>
                    </div>
                    <div class="card-body row g-3">

                        @if($isTravel)
                            <!-- =======================
                                 TRAVEL PACKAGE FORM
                                 ======================= -->
                            <div class="col-md-6">
                                <label class="form-label">Tour Package Name *</label>
                                <input type="text" class="form-control" name="name" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Duration *</label>
                                <input type="text" class="form-control" name="duration" placeholder="5 Days / 4 Nights" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Starting Price *</label>
                                <input type="number" class="form-control" name="price" required>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Original Price</label>
                                <input type="number" class="form-control" name="regular_price">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tour Type</label>
                                <select name="tour_type" class="form-select">
                                    <option value="domestic">Domestic</option>
                                    <option value="international">International</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Destination *</label>
                                <select name="brand_id" class="form-select" required>
                                    <option value="">Select Destination</option>
                                    @foreach($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Package Category *</label>
                                <select name="category_id" class="form-select" required>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Tour Highlights</label>
                                <textarea class="form-control" name="highlights"></textarea>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Day-wise Itinerary</label>
                                <textarea class="form-control" name="itinerary" rows="5"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Inclusions</label>
                                <textarea class="form-control" name="inclusions"></textarea>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Exclusions</label>
                                <textarea class="form-control" name="exclusions"></textarea>
                            </div>

                        @else
                            <!-- =======================
                                 PRODUCT FORM
                                 ======================= -->
                            <div class="col-md-6">
                                <label class="form-label">Product Name *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">SKU</label>
                                <input type="text" class="form-control @error('sku') is-invalid @enderror" name="sku" value="{{ old('sku') }}">
                                @error('sku')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Leave empty to auto-generate</small>
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Price *</label>
                                <input type="number" step="0.01" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price') }}" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Regular Price</label>
                                <input type="number" step="0.01" class="form-control" name="regular_price" value="{{ old('regular_price') }}">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Discount Price</label>
                                <input type="number" step="0.01" class="form-control" name="discount_price" value="{{ old('discount_price') }}">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Category *</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">Select Category</option>
                                    @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Brand</label>
                                <select name="brand_id" class="form-select @error('brand_id') is-invalid @enderror">
                                    <option value="">Select Brand</option>
                                    @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                                @error('brand_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Product Type</label>
                                <select name="product_type" class="form-select">
                                    <option value="simple">Simple Product</option>
                                    <option value="attribute">Product with Attributes</option>
                                    <option value="combo">Combo Product</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">MOQ</label>
                                <input type="number" class="form-control" name="moq" value="{{ old('moq') }}">
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Short Description</label>
                                <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                            </div>

                            <!-- Sample / Pack Tiers (Only for Product Admin) -->
                            @if($isProduct)
                            <div class="col-md-12 mt-3">
                                <h6>Sample / Pack Pricing</h6>
                                <p class="text-muted small">Add sample and pack price tiers. Click + to add a row, or - to remove.</p>
                                <div id="sampleTiersContainer">
                                    <div class="sample-tier-row row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="sample_tiers[0][name]" class="form-control" value="" placeholder="Name">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" step="0.01" name="sample_tiers[0][price]" class="form-control" value="" placeholder="Price">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-tier-btn">-</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="button" id="addSampleTierBtn" class="btn btn-sm btn-primary">+ Add Tier</button>
                                </div>
                            </div>

                            <!-- Embroidery Price Tiers (Only for Product Admin) -->
                            <div class="col-md-12 mt-3">
                                <h6>Embroidery Price</h6>
                                <p class="text-muted small">Add embroidery price tiers. Click + to add a row, or - to remove.</p>
                                <div id="embTiersContainer">
                                    <div class="emb-tier-row row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="embroidery_tiers[0][name]" class="form-control" value="" placeholder="Name">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" step="0.01" name="embroidery_tiers[0][price]" class="form-control" value="" placeholder="Price">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-emb-btn">-</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <button type="button" id="addEmbTierBtn" class="btn btn-sm btn-primary">+ Add Embroidery Tier</button>
                                </div>
                            </div>
                            @endif

                        @endif

                        <!-- COMMON FIELDS -->
                        <div class="col-md-6">
                            <label class="form-label">Featured</label>
                            <select name="is_featured" class="form-select">
                                <option value="0" {{ old('is_featured') == '0' ? 'selected' : '' }}>No</option>
                                <option value="1" {{ old('is_featured') == '1' ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ old('status', '1') == '1' ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ old('status') == '0' ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <hr class="my-2">

                        <h6>SEO SECTION</h6>

                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="{{ old('meta_title') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_keywords" value="{{ old('meta_keywords') }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" name="meta_description" rows="3">{{ old('meta_description') }}</textarea>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE (Common for both) -->
            <div class="col-lg-4">
                <div class="card mb-3">
                    <div class="card-header"><h5 class="mb-0">Thumbnail Image</h5></div>
                    <div class="card-body text-center">
                        <img id="thumbnailPreview" class="preview-thumb" src="https://via.placeholder.com/300x200?text=Preview">
                        <input type="file" name="thumbnail" class="form-control mt-2" accept="image/*" onchange="showThumbnailPreview(event)">
                    </div>
                </div>

                <!-- Gallery Images -->
                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Gallery Images</h5></div>
                    <div class="card-body">
                        <div class="preview-gallery d-flex flex-wrap">
                            <p>No images uploaded</p>
                        </div>
                        <input type="file" name="images[]" class="form-control mt-2" accept="image/*" multiple onchange="showGalleryPreview(event)">
                    </div>
                </div>
            </div>

        </div>

        <div class="text-end mt-3">
            <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">
                {{ $isTravel ? 'Save Tour Package' : 'Save Product' }}
            </button>
        </div>

    </form>

</div>

<script>
function showThumbnailPreview(event) {
    document.getElementById('thumbnailPreview').src = URL.createObjectURL(event.target.files[0]);
}

function showGalleryPreview(event) {
    let preview = document.querySelector('.preview-gallery');
    preview.innerHTML = "";
    for (let file of event.target.files) {
        let img = document.createElement("img");
        img.src = URL.createObjectURL(file);
        preview.appendChild(img);
    }
}
</script>

@if($isProduct)
<script>
document.addEventListener('DOMContentLoaded', function(){
    function setupTier(containerId, addBtnId, rowClass, namePrefix, removeClass) {
        const container = document.getElementById(containerId);
        const addBtn = document.getElementById(addBtnId);
        if (!container || !addBtn) return;

        function renumber() {
            const rows = container.querySelectorAll('.' + rowClass);
            rows.forEach(function(row, idx){
                row.querySelectorAll('input').forEach(function(input){
                    const name = input.getAttribute('name');
                    const parts = name.split(']');
                    const newName = parts[0].replace(new RegExp(namePrefix + '\\[.*?\\]'), namePrefix + '['+idx+']') + (parts[1] || '');
                    input.setAttribute('name', newName);
                });
            });
        }

        function makeRemove(btn) {
            btn.addEventListener('click', function(){
                const row = this.closest('.' + rowClass);
                if (row) { row.remove(); renumber(); }
            });
        }

        container.querySelectorAll('.' + removeClass).forEach(makeRemove);

        addBtn.addEventListener('click', function(){
            const idx = container.querySelectorAll('.' + rowClass).length;
            const div = document.createElement('div');
            div.className = rowClass + ' row g-2 mb-2';
            div.innerHTML = `
                <div class="col-md-6">
                    <input type="text" name="${namePrefix}[${idx}][name]" class="form-control" value="" placeholder="Name">
                </div>
                <div class="col-md-4">
                    <input type="number" step="0.01" name="${namePrefix}[${idx}][price]" class="form-control" value="" placeholder="Price">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger ${removeClass}">-</button>
                </div>
            `;
            container.appendChild(div);
            const btnRemove = div.querySelector('.' + removeClass);
            makeRemove(btnRemove);
        });
    }

    setupTier('sampleTiersContainer', 'addSampleTierBtn', 'sample-tier-row', 'sample_tiers', 'remove-tier-btn');
    setupTier('embTiersContainer', 'addEmbTierBtn', 'emb-tier-row', 'embroidery_tiers', 'remove-emb-btn');
});
</script>
@endif

@endsection
