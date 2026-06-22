@extends('layouts.vertical', ['title' => 'Edit Product'])

@section('content')

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

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">

            <!-- LEFT FORM -->
            <div class="col-lg-8">

                <div class="card">
                    <div class="card-header"><h5 class="mb-0">Edit Product</h5></div>
                    <div class="card-body row g-3">

                        <div class="col-md-6">
                            <label class="form-label">Product Name *</label>
                            <input type="text" class="form-control" name="name" required value="{{ $product->name }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">SKU *</label>
                            <input type="text" class="form-control" name="sku" required value="{{ $product->sku }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Price *</label>
                            <input type="number" step="0.01" class="form-control" name="price" required value="{{ $product->price }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Discount Price</label>
                            <input type="number" step="0.01" class="form-control" name="discount_price" value="{{ $product->discount_price }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">MOQ</label>
                            <input type="number" class="form-control" name="moq" value="{{ $product->moq }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-select" required>
                                @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ $product->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Brand</label>
                            <select name="brand" class="form-select">
                                <option value="">Select Brand</option>
                                @foreach($brands as $brand)
                                <option value="{{ $brand->id }}" {{ $product->brand_id == $brand->id ? 'selected' : '' }}>
                                    {{ $brand->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Short Description</label>
                            <textarea name="description" class="form-control" rows="3">{{ $product->description }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Featured</label>
                            <select name="is_featured" class="form-select">
                                <option value="0" {{ $product->is_featured == 0 ? 'selected' : '' }}>No</option>
                                <option value="1" {{ $product->is_featured == 1 ? 'selected' : '' }}>Yes</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select">
                                <option value="1" {{ $product->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $product->status == 0 ? 'selected' : '' }}>Inactive</option>
                            </select>
                        </div>

                        <hr class="my-2">

                        <h6>SEO SECTION</h6>

                        <div class="col-md-6">
                            <label class="form-label">Meta Title</label>
                            <input type="text" class="form-control" name="meta_title" value="{{ $product->meta_title }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Meta Keywords</label>
                            <input type="text" class="form-control" name="meta_keywords" value="{{ $product->meta_keywords }}">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Meta Description</label>
                            <textarea class="form-control" name="meta_description" rows="3">{{ $product->meta_description }}</textarea>
                        </div>

                        <!-- Sample / Pack Tiers -->
                        <div class="col-md-12 mt-3">
                            <h6>Sample / Pack Pricing</h6>
                            <p class="text-muted small">Add sample and pack price tiers. Click + to add a row, or - to remove.</p>
                            <div id="sampleTiersContainer">
                                @php
                                    $sampleTiers = collect();
                                    if (\Illuminate\Support\Facades\Schema::hasTable('product_sample_prices')) {
                                        try {
                                            $sampleTiers = $product->samplePrices ?? collect();
                                        } catch (\Exception $e) {
                                            $sampleTiers = collect();
                                        }
                                    }
                                @endphp
                                @if($sampleTiers->isEmpty())
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
                                @else
                                    @foreach($sampleTiers as $i => $tier)
                                    <div class="sample-tier-row row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="sample_tiers[{{ $i }}][name]" class="form-control" value="{{ old('sample_tiers.'.$i.'.name', $tier->name ?? '') }}" placeholder="Name">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" step="0.01" name="sample_tiers[{{ $i }}][price]" class="form-control" value="{{ old('sample_tiers.'.$i.'.price', $tier->price ?? '') }}" placeholder="Price">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-tier-btn">-</button>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="mt-2">
                                <button type="button" id="addSampleTierBtn" class="btn btn-sm btn-primary">+ Add Tier</button>
                            </div>
                        </div>

                        <!-- Embroidery Price Tiers -->
                        <div class="col-md-12 mt-3">
                            <h6>Embroidery Price</h6>
                            <p class="text-muted small">Add embroidery price tiers. Click + to add a row, or - to remove.</p>
                            <div id="embTiersContainer">
                                @php
                                    $embTiers = collect();
                                    if (\Illuminate\Support\Facades\Schema::hasTable('product_embroidery_prices')) {
                                        try {
                                            $embTiers = $product->embroideryPrices ?? collect();
                                        } catch (\Exception $e) {
                                            $embTiers = collect();
                                        }
                                    }
                                @endphp
                                @if($embTiers->isEmpty())
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
                                @else
                                    @foreach($embTiers as $i => $tier)
                                    <div class="emb-tier-row row g-2 mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="embroidery_tiers[{{ $i }}][name]" class="form-control" value="{{ old('embroidery_tiers.'.$i.'.name', $tier->name ?? '') }}" placeholder="Name">
                                        </div>
                                        <div class="col-md-4">
                                            <input type="number" step="0.01" name="embroidery_tiers[{{ $i }}][price]" class="form-control" value="{{ old('embroidery_tiers.'.$i.'.price', $tier->price ?? '') }}" placeholder="Price">
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger remove-emb-btn">-</button>
                                        </div>
                                    </div>
                                    @endforeach
                                @endif
                            </div>
                            <div class="mt-2">
                                <button type="button" id="addEmbTierBtn" class="btn btn-sm btn-primary">+ Add Embroidery Tier</button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- RIGHT SIDE -->
        <div class="col-lg-4">
    <div class="card mb-3">
        <div class="card-header"><h5 class="mb-0">Thumbnail Image</h5></div>
        <div class="card-body text-center">
            <img id="thumbnailPreview" class="preview-thumb"
                src="{{ $product->thumbnail ? asset($product->thumbnail) : 'https://via.placeholder.com/300x200?text=Preview' }}">
            <input type="file" name="thumbnail" class="form-control mt-2" accept="image/*" onchange="showThumbnailPreview(event)">
        </div>
    </div>

    <!-- Gallery Images -->
    <div class="card">
        <div class="card-header"><h5 class="mb-0">Gallery Images</h5></div>
        <div class="card-body">
            <div class="preview-gallery d-flex flex-wrap">
                @if($product->images)
                    @php
                        $galleryImages = json_decode($product->images); // Decode JSON string to array
                    @endphp

                    @foreach($galleryImages as $img)
                    <div class="gallery-item">
                        <img src="{{ asset($img) }}" width="100" height="100" style="object-fit:cover; border-radius:4px;">
                    </div>
                    @endforeach
                @else
                    <p>No images available</p>
                @endif
            </div>

            <input type="file" name="images[]" class="form-control mt-2" accept="image/*" multiple onchange="showGalleryPreview(event)">
        </div>
    </div>
</div>


        </div>

        <div class="text-end mt-3">
            <a href="{{ route('admin.products.index') }}" class="btn btn-light">Cancel</a>
            <button class="btn btn-primary">Update Product</button>
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

@endsection
