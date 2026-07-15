@extends('layouts.vertical', ['title' => 'Products'])

@section('content')
@php
    $user = auth()->user();
    $userType = $user->type;
    $isProduct = $userType == 'product';
@endphp

<div class="container-fluid">

    <div class="py-3 d-flex align-items-center justify-content-between">
        <h4 class="fs-18 fw-semibold">
    @if(isset($brand) && $brand)
        Products of Brand: {{ $brand->name }}
    @else
        All Products
    @endif
</h4>

        <a href="{{ route('admin.products.create') }}" class="btn btn-primary btn-sm">+ Add Product</a>
    </div>

    <div class="card">
        <div class="card-body">

            <!-- Search, Filter and Export Controls - All in One Row -->
            <form method="GET" action="{{ route('admin.products.index') }}" class="filter-area mb-4 p-4 bg-light rounded">
                <div class="d-flex flex-nowrap gap-3 align-items-center" style="overflow-x: auto; padding-bottom: 10px;">
                    <!-- Search keyword -->
                    <div class="flex-shrink-0" style="min-width: 180px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" name="search" id="keywordSearch" class="form-control" placeholder="Search products..." value="{{ request('search') }}">
                        </div>
                    </div>
                    
                    <!-- Brand Filter -->
                    <div class="flex-shrink-0" style="min-width: 180px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <select name="brand_id" id="brandFilter" class="form-select">
                                <option value="">All Brands</option>
                                @foreach($brands as $b)
                                    <option value="{{ $b->id }}" {{ request('brand_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Category Filter -->
                    <div class="flex-shrink-0" style="min-width: 180px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                            <select name="category_id" id="categoryFilter" class="form-select">
                                <option value="">All Categories</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Product Type Filter -->
                    <div class="flex-shrink-0" style="min-width: 160px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-cube"></i></span>
                            <select name="product_type" id="typeFilter" class="form-select">
                                <option value="">All Types</option>
                                <option value="simple" {{ request('product_type') == 'simple' ? 'selected' : '' }}>Simple</option>
                                <option value="attribute" {{ request('product_type') == 'attribute' ? 'selected' : '' }}>Attribute</option>
                                <option value="combo" {{ request('product_type') == 'combo' ? 'selected' : '' }}>Combo</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sort By -->
                    <div class="flex-shrink-0" style="min-width: 160px;">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-sort"></i></span>
                            <select name="sort" id="sortFilter" class="form-select">
                                <option value="">Default</option>
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest First</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest First</option>
                                <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name (A-Z)</option>
                                <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name (Z-A)</option>
                                <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Price High to Low</option>
                                <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Price Low to High</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex-shrink-0 d-flex gap-2">
                        <button type="submit" class="btn btn-success btn-sm">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading products...</p>
            </div>

            <!-- Products Table -->
            <div class="table-responsive">
                <table id="productsTable" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Brand</th>
                            <th>Category</th>
                            <th>Type</th>
                            <th>Price</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody">
                        @forelse($products as $product)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <!-- IMAGE COLUMN -->
                                <td>
                                    @if($product->thumbnail)
                                        <img src="{{ asset($product->thumbnail) }}" width="60" height="60"
                                             style="object-fit:cover; border-radius:4px;">
                                    @else
                                        <img src="https://via.placeholder.com/60x60?text=No+Image" width="60" height="60"
                                             style="object-fit:cover; border-radius:4px;">
                                    @endif
                                </td>

                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td data-brand="{{ $product->brand->name ?? '-' }}">{{ $product->brand->name ?? '-' }}</td>
                                <td data-category="{{ $product->category->name ?? '-' }}">{{ $product->category->name ?? '-' }}</td>
                                <td data-type="{{ $product->product_type }}">
                                    <span class="badge 
                                        @if($product->product_type === 'simple') bg-info
                                        @elseif($product->product_type === 'attribute') bg-warning
                                        @elseif($product->product_type === 'combo') bg-success
                                        @else bg-secondary @endif">
                                        {{ ucfirst($product->product_type) }}
                                    </span>
                                </td>
                                <td data-price="{{ $product->price }}">₹{{ number_format($product->price, 2) }}</td>
                                
                                <td>
                                    <div class="d-flex gap-2">
                                        <!-- Edit Icon -->
                                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>

                                        <!-- Attribute Icon (only for attribute products) -->
                                        @if($product->product_type === 'attribute' && $isProduct)
                                            <a href="{{ route('admin.products.attributes', $product->id) }}" 
                                               class="btn btn-sm btn-outline-info"
                                               title="Attributes">
                                                <i class="fa-solid fa-sliders-h"></i>
                                            </a>
                                        @endif

                                        <!-- Combo Icon -->
                                        @if(($product->product_type === 'simple' || $product->product_type === 'attribute') && $isProduct)
                                            <a href="{{ route('admin.products.combo', $product->id) }}" 
                                               class="btn btn-sm btn-outline-success"
                                               title="Create Combo">
                                                <i class="fa-solid fa-layer-group"></i>
                                            </a>
                                        @endif

                                        <!-- Delete Icon -->
                                        <form action="{{ route('admin.products.destroy', $product->id) }}" method="POST" class="delete-form" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger delete-btn" title="Delete">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center">No products found</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">
                {{ $products->links() }}
            </div>


        </div>
    </div>

</div>

<style>
/* Single Line Filter Styles */
.filter-area .d-flex {
    flex-wrap: nowrap;
    white-space: nowrap;
}

.filter-area .flex-shrink-0 {
    flex-shrink: 0;
}

/* Custom scrollbar for single line layout */
.filter-area .d-flex::-webkit-scrollbar {
    height: 4px;
}

.filter-area .d-flex::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.filter-area .d-flex::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.filter-area .d-flex::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Form control styling */
.input-group-sm {
    height: 32px;
}

.input-group-sm .form-control,
.input-group-sm .form-select {
    height: 32px;
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}

.input-group-sm .input-group-text {
    height: 32px;
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
}

/* Price inputs styling */
.input-group-sm .form-control[type="number"] {
    min-width: 80px;
}

/* Button hover effects */
.btn-success:hover {
    background-color: #198754;
    border-color: #198754;
}

.btn-outline-secondary:hover {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

/* Responsive design */
@media (max-width: 1200px) {
    .filter-area .d-flex {
        overflow-x: auto;
        padding-bottom: 15px;
    }
    
    .filter-area .flex-shrink-0 {
        min-width: auto !important;
    }
}

@media (max-width: 992px) {
    .filter-area .d-flex {
        flex-wrap: wrap;
        gap: 10px;
    }
    
    .filter-area .flex-shrink-0 {
        flex: 1 0 calc(33.333% - 10px);
        min-width: calc(33.333% - 10px) !important;
    }
}

@media (max-width: 768px) {
    .filter-area {
        padding: 15px !important;
    }
    
    .filter-area .flex-shrink-0 {
        flex: 1 0 calc(50% - 10px);
        min-width: calc(50% - 10px) !important;
    }
}

@media (max-width: 576px) {
    .filter-area .flex-shrink-0 {
        flex: 1 0 100%;
        min-width: 100% !important;
    }
    
    .btn-sm i {
        margin-right: 0 !important;
    }
    
    .btn-sm span:not(.badge) {
        display: none;
    }
}

/* Loading indicator */
#loadingIndicator {
    background-color: rgba(255, 255, 255, 0.9);
    border-radius: 8px;
    margin: 20px 0;
}

/* Badge styling for product types */
.badge {
    padding: 0.25em 0.6em;
    font-size: 0.75em;
    font-weight: 500;
}

/* Action buttons spacing */
.d-flex.gap-2 {
    gap: 0.5rem !important;
}
</style>

<!-- Load jQuery and SweetAlert -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    console.log('Products page loaded');
    
    // ================= 1. ATTACH DELETE HANDLERS =================
    function attachDeleteHandlers() {
        $('.delete-btn').off('click').on('click', function(e) {
            e.preventDefault();
            var form = $(this).closest('form');
            
            Swal.fire({
                title: 'Are you sure?',
                text: "This product will be permanently deleted!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    }
    
    // ================= 2. FILTER CHANGE EVENTS =================
    $('#brandFilter, #categoryFilter, #typeFilter, #sortFilter').on('change', function() {
        $(this).closest('form').submit();
    });
    
    // ================= 3. INITIALIZE =================
    attachDeleteHandlers();
    console.log('Products functionality initialized');
});
</script>
@endsection
