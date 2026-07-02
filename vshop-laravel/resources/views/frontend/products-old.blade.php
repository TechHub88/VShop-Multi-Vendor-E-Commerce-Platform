@extends('frontend.layout')

@section('content')

  
 <section class="bedcum-sec" style="padding-top: 180px;">
            <div class="container">
                <div class="bedcum-inner">	
                    <div class="bed-cum-list">
                        <ul>
                            <li>
                                <a href="#">Shop</a>
                            </li>
                            <li>
                                <i class="fa-regular fa-angle-right"></i>
                            </li>
                            <li>
                                <span>All Products</span>
                            </li>
                        </ul>
                    </div>
                    <div class="product-list-tp-right">
                        <div class="filter-box">
                            <button class="filter-btn">
                                <i class="fa-regular fa-sliders"></i>
                            </button>
                        </div>
                        <div class="result-sec">
                            <p>Result : <span>180 Products</span></p>
                        </div>
                        <div class="short-sec">
                            <button class="dropdown-toggle short-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span>Sort by : Popularity</span>
                                <i class="fa-regular fa-filter"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Popularity</a></li>
                                <li><a class="dropdown-item" href="#">Popularity</a></li>
                                <li><a class="dropdown-item" href="#">Popularity</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

<section class="product-list-section py-5">
    <div class="container">

        {{-- Page Title --}}
        <div class="row mb-4">
            <div class="col-lg-12 text-center">
              
            </div>
        </div>

     
<div class="row">





    {{-- ✅ RIGHT PRODUCT GRID --}}
    <div class="col-lg-9 col-md-8">
        <div class="row">
            @foreach($products as $product)
                <div class="col-lg-4 col-md-6 col-sm-6 mb-4">
                    <div class="product-list-box">
                        <div class="product-list-box-inner">

                            {{-- Image --}}
                            <div class="product-list-box-img">
                                @php
                                    $images = $product->images;
                                    if (is_string($images)) {
                                        $decoded = json_decode($images, true);
                                        $images = $decoded ?: explode(',', $images);
                                    }
                                    $images = array_values(array_filter((array) $images));
                                    $firstImage = $images[0] ?? null;
                                @endphp

                                <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                    <img src="{{ $firstImage ? asset('storage/'.$firstImage) : asset('assets/no-image.png') }}"
                                         class="img-fluid" alt="{{ $product->name }}">
                                </a>
                            </div>

                            {{-- Info --}}
                            <div class="product-list-box-info">
                                <h4 class="proTitle">
                                    <a href="{{ route('frontend.productdetails', $product->sku ?? $product->id) }}">
                                        {{ $product->name }}
                                    </a>
                                </h4>

                                <div class="rupess-btn-sec">
                                    <p>₹{{ number_format($product->price, 2) }}</p>
                                    <button class="cart-btn">Add to Cart</button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-center mt-4">
            {{-- {{ $products->links() }} --}}
        </div>
    </div>

</div>


    </div>
</section>

@endsection
