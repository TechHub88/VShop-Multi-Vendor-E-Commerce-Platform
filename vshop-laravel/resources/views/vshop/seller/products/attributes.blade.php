@extends('vshop.seller.main')

@section('title', 'Manage Variants')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">
                
                <!-- Page Header -->
                <div class="dashboardTop">
                    <div class="dashboardTopLeft">
                        <h2>Manage Variants: {{ $product->name }}</h2>
                    </div>
                    <div class="dashboardTopRight">
                        <a href="{{ route('seller.products.index') }}" class="backBtn">
                            <i class="fa-light fa-arrow-left"></i> Back to Products
                        </a>
                    </div>
                </div>

                <!-- Info Alert -->
                <div class="alert alert-info">
                    <i class="fa-light fa-info-circle"></i> 
                    Create size and color combinations for your product. Each combination becomes a unique variant with its own price and stock.
                </div>

                <!-- Variants Form -->
                <div class="formCard">
                    <form action="{{ route('seller.products.attributes.store', $product->id) }}" method="POST">
                        @csrf

                        <div id="variantsContainer">
                            <!-- Existing Variants -->
                            @if($variants->count() > 0)
                                @foreach($variants as $key => $variantGroup)
                                    @php
                                        $sizeVariant = $variantGroup->firstWhere('attribute.name', 'Size');
                                        $colorVariant = $variantGroup->firstWhere('attribute.name', 'Color');
                                        $sizeTerm = $sizeVariant ? $sizeVariant->term : null;
                                        $colorTerm = $colorVariant ? $colorVariant->term : null;
                                    @endphp
                                    
                                    <div class="variantRow card mb-3">
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <label class="form-label">Size</label>
                                                   <select name="variants[{{ $loop->index }}][size]" class="form-control">
                                                <option value="">Select Size</option>
                                                @foreach(($attributes['size']->terms ?? []) as $term)
                                                    <option value="{{ $term->id }}"
                                                        {{ $sizeTerm && $sizeTerm->id == $term->id ? 'selected' : '' }}>
                                                        {{ $term->term }}
                                                    </option>
                                                @endforeach
                                            </select>

                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label">Color</label>
<select name="variants[{{ $loop->index }}][color]" class="form-select" required>
    <option value="">Select Color</option>
    @foreach(($attributes['color']->terms ?? []) as $term)
        <option value="{{ $term->id }}"
            {{ $colorTerm && $colorTerm->id == $term->id ? 'selected' : '' }}>
            {{ $term->term }}
        </option>
    @endforeach
</select>

                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Regular Price</label>
                                                    <input type="number" step="0.01" name="variants[{{ $loop->index }}][regular_price]" class="form-control" 
                                                           value="{{ $variantGroup->first()->regular_price ?? 0 }}">
                                                </div>
                                                <div class="col-md-2">
                                                    <label class="form-label">Selling Price</label>
                                                    <input type="number" step="0.01" name="variants[{{ $loop->index }}][price]" class="form-control" 
                                                           value="{{ $variantGroup->first()->price ?? 0 }}" required>
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">Stock</label>
                                                    <input type="number" name="variants[{{ $loop->index }}][stock]" class="form-control" 
                                                           value="{{ $variantGroup->first()->product_stock ?? 0 }}" required>
                                                </div>
                                                <div class="col-md-1">
                                                    <label class="form-label">&nbsp;</label>
                                                    <button type="button" class="btn btn-danger removeVariant form-control">
                                                        <i class="fa-light fa-times"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        <!-- Add Variant Button -->
                        <button type="button" id="addVariant" class="btn btn-outline-primary mb-3">
                            <i class="fa-light fa-plus"></i> Add Another Variant
                        </button>

                        <!-- Submit Button -->
                        <div class="formActions mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-light fa-save"></i> Save All Variants
                            </button>
                        </div>

                    </form>
                </div>
                
                
                {{-- ================= EXISTING VARIANTS TABLE ================= --}}
<h5 class="mt-4">Saved Variants</h5>

@if($variants->count())
<table class="table table-bordered">
    <thead>
        <tr>
            <th>Size</th>
            <th>Color</th>
            <th>Regular Price</th>
            <th>Selling Price</th>
            <th>Stock</th>
        </tr>
    </thead>
    <tbody>

    @foreach($variants as $combinationKey => $items)

        @php
            $size = '';
            $color = '';
            $regular_price = '';
            $price = '';
            $stock = '';

            foreach ($items as $row) {

                if (strtolower($row->attribute->name) === 'size') {
                    $size = $row->term->term ?? '';
                }

                if (strtolower($row->attribute->name) === 'color') {
                    $color = $row->term->term ?? '';
                }

                // same for whole combination
                $regular_price = $row->regular_price;
                $price = $row->price;
                $stock = $row->product_stock;
            }
        @endphp

        <tr>
            <td>{{ $size }}</td>
            <td>{{ $color }}</td>
            <td>{{ $regular_price }}</td>
            <td>{{ $price }}</td>
            <td>{{ $stock }}</td>
        </tr>

    @endforeach

    </tbody>
</table>
@else
<div class="alert alert-info">No variants added yet.</div>
@endif


            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.dashboardTop {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 25px;
}
.backBtn {
    background: #6c757d;
    color: white;
    padding: 8px 16px;
    border-radius: 6px;
    text-decoration: none;
}
.backBtn:hover {
    background: #5a6268;
    color: white;
}
.formCard {
    background: white;
    border-radius: 12px;
    padding: 25px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.05);
}
.variantRow {
    border: 1px solid #dee2e6;
    transition: all 0.3s;
}
.variantRow:hover {
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    let variantIndex = {{ $variants->count() }};
    
    // Add new variant row
    $('#addVariant').click(function() {
        let html = `
            <div class="variantRow card mb-3">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <label class="form-label">Size</label>
                            <select name="variants[${variantIndex}][size]" class="form-select" required>
                                <option value="">Select Size</option>
                                @if(isset($attributes['size']) && $attributes['size']->terms)
                                    @foreach($attributes['size']->terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->term }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Color</label>
                            <select name="variants[${variantIndex}][color]" class="form-select" required>
                                <option value="">Select Color</option>
                                @if(isset($attributes['color']) && $attributes['color']->terms)
                                    @foreach($attributes['color']->terms as $term)
                                        <option value="{{ $term->id }}">{{ $term->term }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Regular Price</label>
                            <input type="number" step="0.01" name="variants[${variantIndex}][regular_price]" class="form-control" value="0">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Selling Price</label>
                            <input type="number" step="0.01" name="variants[${variantIndex}][price]" class="form-control" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Stock</label>
                            <input type="number" name="variants[${variantIndex}][stock]" class="form-control" required>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" class="btn btn-danger removeVariant form-control">
                                <i class="fa-light fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        $('#variantsContainer').append(html);
        variantIndex++;
    });
    
    // Remove variant row
    $(document).on('click', '.removeVariant', function() {
        $(this).closest('.variantRow').remove();
    });
});
</script>
@endpush
