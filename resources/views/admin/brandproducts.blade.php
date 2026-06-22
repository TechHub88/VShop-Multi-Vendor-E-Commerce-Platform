@extends('layouts.vertical', ['title' => 'Add Category'])

@section('content')
<div class="container py-5">

    <h1 class="mb-5 text-center">Brands & Products</h1>

    @foreach($brands as $brand)
        @if(isset($brandProducts[$brand->id]) && $brandProducts[$brand->id]->count())
          <div class="brand-section mb-5">

    {{-- Brand Header --}}
    <div class="brand-header d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ $brand->name }}</h3>
        <span class="text-muted">{{ $brandProducts[$brand->id]->total() }} Products</span>
    </div>

    {{-- Products Grid --}}
    <div class="row">
        @foreach($brandProducts[$brand->id] as $product)
            <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                <div class="product-list-box border rounded p-2 h-100">

                    {{-- Thumbnail Image Only --}}
                    <div class="product-list-box-img product-img-small mb-2">
                        @if($product->thumbnail)
                            <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                <img src="{{ asset($product->thumbnail) }}" class="img-fluid" alt="{{ $product->name }}">
                            </a>
                        @else
                            <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                <img src="https://via.placeholder.com/150x150?text=No+Image" class="img-fluid" alt="No Image">
                            </a>
                        @endif
                    </div>

                    {{-- Product Name & Price --}}
                    <div class="product-list-box-info">
                        <h5 class="proTitle mb-1">
                            <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                {{ $product->name }}
                            </a>
                        </h5>
                        <p class="mb-0">₹{{ number_format($product->price, 2) }}</p>
                    </div>

                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <div class="pagination-sec mt-3">
        {{ $brandProducts[$brand->id]->links('pagination::bootstrap-5') }}
    </div>

</div>

        @endif
    @endforeach

</div>

@endsection
