@extends('layouts.vertical', ['title' => 'Edit Product Attributes'])

@section('content')
<div class="container-fluid">

    <h4>Product: {{ $product->name }}</h4>
    <p class="text-muted">Edit Attribute Combination</p>

    <div class="row">
        <!-- Edit Attributes Form -->
        <div class="col-md-12">
            <div class="card mb-3">
                <div class="card-header">
                    <h5>Edit Attributes</h5>
                    <small class="text-muted">Combination Key: <code>{{ $combinationKey }}</code></small>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.products.attributes.update', ['product' => $product->id, 'combinationKey' => $combinationKey]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row align-items-end mb-3">
                            @foreach($attributes as $attribute)
                            <div class="col-md-3">
                                <label class="form-label">Choose {{ $attribute->name }}</label>
                                <select name="attribute_term_id[{{ $attribute->id }}]" class="form-select">
                                    <option value="">Select {{ $attribute->name }}...</option>
                                    @foreach($attribute->terms as $term)
                                        <option value="{{ $term->id }}" 
                                            {{ isset($selectedTerms[$attribute->id]) && $selectedTerms[$attribute->id] == $term->id ? 'selected' : '' }}>
                                            {{ $term->term }}
                                        </option>
                                    @endforeach
                                </select>
                                @error("attribute_term_id.$attribute->id")
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @endforeach
                        </div>

                        <div class="row align-items-end mt-3">
                            <div class="col-md-2">
                                <label class="form-label">Regular Price</label>
                                <input type="number" step="0.01" name="regular_price" class="form-control" 
                                       value="{{ $firstAttribute->regular_price ?? '' }}" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Price</label>
                                <input type="number" step="0.01" name="price" class="form-control" 
                                       value="{{ $firstAttribute->price ?? '' }}" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Stock</label>
                                <input type="number" name="product_stock" class="form-control" 
                                       value="{{ $firstAttribute->product_stock ?? '' }}" placeholder="e.g. 50" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Unit</label>
                                <input type="text" name="product_unit" class="form-control" 
                                       value="{{ $firstAttribute->product_unit ?? '' }}" placeholder="e.g. pcs, kg" required>
                            </div>

                            <div class="col-md-2">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="Y" {{ ($firstAttribute->status ?? 'Y') == 'Y' ? 'selected' : '' }}>Active</option>
                                    <option value="N" {{ ($firstAttribute->status ?? 'Y') == 'N' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>

                            <div class="col-md-2 text-end">
                                <button type="submit" class="btn btn-primary mt-4 w-100">Update Attributes</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Current Combination Details -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header"><h5>Current Combination Details</h5></div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-12">
                            <strong>Attributes:</strong>
                            @foreach($combinationAttributes as $attr)
                                <span class="badge bg-secondary me-1">
                                    {{ $attr->attribute->name ?? 'N/A' }}: {{ $attr->term->term ?? 'N/A' }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <strong>Regular Price:</strong><br>
                            ₹{{ number_format($firstAttribute->regular_price ?? 0, 2) }}
                        </div>
                        <div class="col-md-2">
                            <strong>Price:</strong><br>
                            ₹{{ number_format($firstAttribute->price ?? 0, 2) }}
                        </div>
                        <div class="col-md-2">
                            <strong>Stock:</strong><br>
                            {{ $firstAttribute->product_stock ?? '0' }}
                        </div>
                        <div class="col-md-2">
                            <strong>Unit:</strong><br>
                            {{ $firstAttribute->product_unit ?? '-' }}
                        </div>
                        <div class="col-md-2">
                            <strong>Status:</strong><br>
                            <span class="badge {{ $firstAttribute->status == 'Y' ? 'bg-success' : 'bg-danger' }}">
                                {{ $firstAttribute->status == 'Y' ? 'Active' : 'Inactive' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="col-md-12 mt-3">
            <a href="{{ route('admin.products.attributes', $product->id) }}" class="btn btn-secondary">
                <i class="mdi mdi-arrow-left"></i> Back to Attributes
            </a>
        </div>

    </div>

</div>

@if(session('error'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '{{ session('error') }}'
        });
    });
</script>
@endif

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endsection

