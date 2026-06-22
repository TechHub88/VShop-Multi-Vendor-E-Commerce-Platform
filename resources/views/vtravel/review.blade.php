@extends('vtravel.main')

@section('title', 'Product Reviews')

@section('content')
<div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
    <div class="dashboardPosPagesLeftContentonner">
        <div class="dashboardDtlsArea">
            <div class="dashboardDtlsAreaBody">
                <div class="bed-cum-sec">
                    <button class="back-btn">
                        <i class="fa-regular fa-arrow-left"></i>
                    </button>
                    <ul>
                        <li>Home / </li>
                        <li>Wellness /</li>
                        <li>Mat /</li>
                        <li>Selinium Yoga Mat</li>
                    </ul>
                </div>

                <div class="product-details-sec">
                    <div class="row">
                        <div class="col-lg-9">
                            <div class="product-details-left">
                                <div class="product-list-box">
                                    <div class="product-list-box-top">
                                        <div class="product-list-box-left">
                                            <div class="share-wishlist-sec">
                                                <a href="#">
                                                    <i class="fa-regular fa-share-nodes"></i>
                                                </a>
                                                <a href="#">
                                                    <i class="fa-regular fa-heart"></i>
                                                </a>
                                            </div>
                                            <div class="product-image-sec">
                                                <div class="product-image-wrapper-one">
                                                    <div class="product-image-one-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="product-image-one-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="product-image-one-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="product-image-one-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="product-image-wrapper-two">
                                                    <div class="product-image-two-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="product-image-two-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="product-image-two-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                                        </div>
                                                    </div>
                                                    <div class="product-image-two-box">
                                                        <div class="product-image-one-box-inner">
                                                            <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="product-list-box-right">
                                            <div class="product-heading">
                                                <div class="product-heading-left">
                                                    <h3>Yoga Mat, 8 mm thick, 173 x 61 cm</h3>
                                                    <ul>
                                                        <li>
                                                            <a href="#">Visit Selinium Yoga store</a>
                                                        </li>
                                                        <li>/</li>
                                                        <li>Yoga Accessories</li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="rating-sec">
                                                <div class="rating-left">
                                                    <ul>
                                                        <li>
                                                            4.6
                                                            <i class="fa-solid fa-star"></i>
                                                        </li>
                                                        <li class="blackColor">
                                                            <p class="old-rupess">₹ 6,700.00</p>
                                                        </li>
                                                        <li>
                                                            <p class="off-box">14 % Off</p>
                                                        </li>
                                                        <li>
                                                            <p class="new-rupess">₹ 5,900.00</p>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="quantity-sec">
                                                <label class="quantity-box">
                                                    <input type="radio" name="radio1" checked>
                                                    <span>8 mm</span>
                                                </label>
                                                <label class="quantity-box">
                                                    <input type="radio" name="radio1">
                                                    <span>6 mm</span>
                                                </label>
                                                <label class="quantity-box">
                                                    <input type="radio" name="radio1">
                                                    <span>4 mm</span>
                                                </label>
                                                <label class="quantity-box">
                                                    <input type="radio" name="radio1">
                                                    <span>2 mm</span>
                                                </label>
                                            </div>
                                            <div class="color-select-sec">
                                                <label class="color-box">
                                                    <input type="radio" checked name="radio2">
                                                    <span class="color-circle merun-box"></span>
                                                </label>
                                                <label class="color-box">
                                                    <input type="radio" name="radio2">
                                                    <span class="color-circle green-box"></span>
                                                </label>
                                                <label class="color-box">
                                                    <input type="radio" name="radio2">
                                                    <span class="color-circle chocolate-box"></span>
                                                </label>
                                                <p>( Barbarian Red )</p>
                                            </div>
                                            <div class="product-dtl-sec">
                                                <p>Expected Delivery : <span class="blackColor">16 - 19 June 25</span> / <a href="#">500024 - Change Add</a></p>
                                            </div>
                                            <div class="product-col-bottom-sec">
                                                <div class="quantity-sub-sec">
                                                    <button type="button" id="sub" class="sub">-</button>
                                                    <input class="count" type="text" id="1" value="1" min="1" max="100">
                                                    <button type="button" id="add" class="add">+</button>
                                                </div>
                                                <a href="#" class="add-cart-btn">Add to Cart</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3">
                            <div class="product-advatice-sec">
                                <a href="#">
                                    <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="product-list-top-sec">
                    <div class="product-list-top-left company-top-info-left">
                        <h2>128 User Reviews</h2>
                        <div class="review-rating-box">
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <i class="fa-solid fa-star"></i>
                            <p>4.7 / 5</p>
                        </div>
                        <div class="star-review-select">
                            <select name="" id="" class="form-select">
                                <option value="">4 Star Reviews</option>
                            </select>
                        </div>
                        <div class="product-search">
                            <i class="fa-light fa-magnifying-glass"></i>
                            <input type="text" placeholder="Search...">
                        </div>
                    </div>
                    <div class="product-list-top-right company-top-info-right">
                        <div class="dropdown high-low-dropdown">
                            <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <span style="color: #5F6368;">Sort by</span> : With Images
                                <i class="fa-regular fa-filter"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">With Images</a></li>
                                <li><a class="dropdown-item" href="#">Most Helpful</a></li>
                                <li><a class="dropdown-item" href="#">Most Recent</a></li>
                                <li><a class="dropdown-item" href="#">Highest Rated</a></li>
                                <li><a class="dropdown-item" href="#">Lowest Rated</a></li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="user-review-sec">
                    <ul class="review-list">
                        <li>
                            <div class="review-top">
                                <div class="review-top-left">
                                    <h3>Syed Habeeb Mujtaba</h3>
                                    <div class="review-top-lft-bottom">
                                        <p>4.6 <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></p>
                                        <span>19-06-2024</span>
                                        <button class="like-btn">
                                            <i class="fa-regular fa-thumbs-up"></i>
                                            07
                                        </button>
                                        <button class="like-btn">
                                            <i class="fa-regular fa-thumbs-down"></i>
                                            02
                                        </button>
                                    </div>
                                </div>
                                <div class="review-top-right">
                                    <a href="#" class="report-link">Report this</a>
                                </div>
                            </div>
                            <p>Its dense, closed-cell construction provides excellent grip and cushion, making it ideal for all levels. The mat is durable, eco-friendly, and comes with a lifetime guarantee. While it's a bit pricier, the long-term value and performance make it a worthwhile investment for serious yogis.</p>
                            <div class="review-img-sec">
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="review-top">
                                <div class="review-top-left">
                                    <h3>Syed Habeeb Mujtaba</h3>
                                    <div class="review-top-lft-bottom">
                                        <p>4.6 <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></p>
                                        <span>19-06-2024</span>
                                        <button class="like-btn">
                                            <i class="fa-regular fa-thumbs-up"></i>
                                            07
                                        </button>
                                        <button class="like-btn">
                                            <i class="fa-regular fa-thumbs-down"></i>
                                            02
                                        </button>
                                    </div>
                                </div>
                                <div class="review-top-right">
                                    <a href="#" class="report-link">Report this</a>
                                </div>
                            </div>
                            <p>Its dense, closed-cell construction provides excellent grip and cushion, making it ideal for all levels. The mat is durable, eco-friendly, and comes with a lifetime guarantee. While it's a bit pricier, the long-term value and performance make it a worthwhile investment for serious yogis.</p>
                            <div class="review-img-sec">
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                            </div>
                            <div class="report-sec" style="display: none;">
                                <div class="row">
                                    <div class="col-lg-6 mb-3">
                                        <div class="report-box">
                                            <label for="">Report this for :</label>
                                            <select name="" id="" class="form-select">
                                                <option value="">Select reason for reporting</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mb-3">
                                        <div class="report-box mb-0">
                                            <p>by : Syed Habeeb Mujtaba</p>
                                            <p>09:45 Pm / 24-10-2024</p>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="description-box">
                                            <label for="">Description :</label>
                                            <textarea name="" id="" placeholder="This Review is not about this product, Wrong review posted"></textarea>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="report-btn-sec">
                                            <button class="cancel-btn">Cancel</button>
                                            <button class="apply-btn">Apply</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                        <li>
                            <div class="review-top">
                                <div class="review-top-left">
                                    <h3>Syed Habeeb Mujtaba</h3>
                                    <div class="review-top-lft-bottom">
                                        <p>4.6 <i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i><i class="fa-solid fa-star"></i></p>
                                        <span>19-06-2024</span>
                                        <button class="like-btn">
                                            <i class="fa-regular fa-thumbs-up"></i>
                                            07
                                        </button>
                                        <button class="like-btn">
                                            <i class="fa-regular fa-thumbs-down"></i>
                                            02
                                        </button>
                                    </div>
                                </div>
                                <div class="review-top-right">
                                    <a href="#" class="report-link">Report this</a>
                                </div>
                            </div>
                            <p>Its dense, closed-cell construction provides excellent grip and cushion, making it ideal for all levels. The mat is durable, eco-friendly, and comes with a lifetime guarantee. While it's a bit pricier, the long-term value and performance make it a worthwhile investment for serious yogis.</p>
                            <div class="review-img-sec">
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                                <div class="review-img-box">
                                    <a href="#">
                                        <img src="{{ asset('vtravelassets/images/banner-pic-main.webp') }}" alt="">
                                    </a>
                                </div>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Back button functionality
    $('.back-btn').click(function() {
        window.history.back();
    });

    // Initialize image sliders for product gallery
    $('.product-image-wrapper-one').slick({
        slidesToShow: 1,
        slidesToScroll: 1,
        arrows: false,
        fade: true,
        asNavFor: '.product-image-wrapper-two'
    });
    
    $('.product-image-wrapper-two').slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: '.product-image-wrapper-one',
        dots: false,
        arrows: false,
        focusOnSelect: true,
        responsive: [
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3
                }
            },
            {
                breakpoint: 576,
                settings: {
                    slidesToShow: 2
                }
            }
        ]
    });

    // Quantity increment/decrement
    $('.add').click(function() {
        var input = $(this).siblings('.count');
        var value = parseInt(input.val());
        if (value < 100) {
            input.val(value + 1);
        }
    });
    
    $('.sub').click(function() {
        var input = $(this).siblings('.count');
        var value = parseInt(input.val());
        if (value > 1) {
            input.val(value - 1);
        }
    });

    // Size selection
    $('.quantity-box input[type="radio"]').change(function() {
        $('.quantity-box').removeClass('active');
        $(this).parent().addClass('active');
    });

    // Color selection
    $('.color-box input[type="radio"]').change(function() {
        $('.color-box').removeClass('active');
        $(this).parent().addClass('active');
    });

    // Add to cart button
    $('.add-cart-btn').click(function(e) {
        e.preventDefault();
        alert('Product added to cart!');
    });

    // Change address link
    $('a[href*="Change Add"]').click(function(e) {
        e.preventDefault();
        alert('Address selection would open here');
    });

    // Review search functionality
    $('.product-search input').on('keyup', function() {
        var searchText = $(this).val().toLowerCase();
        $('.review-list li').each(function() {
            var reviewText = $(this).find('p').text().toLowerCase();
            var userName = $(this).find('h3').text().toLowerCase();
            
            if (reviewText.indexOf(searchText) !== -1 || userName.indexOf(searchText) !== -1 || searchText === '') {
                $(this).show();
            } else {
                $(this).hide();
            }
        });
    });

    // Star review filter
    $('.star-review-select select').change(function() {
        var selectedRating = $(this).val();
        alert('Filtering by: ' + selectedRating);
    });

    // Sort by dropdown
    $('.high-low-dropdown .dropdown-item').click(function(e) {
        e.preventDefault();
        var sortBy = $(this).text();
        $('.high-low-dropdown .dropdown-toggle span').text('Sort by : ' + sortBy);
        alert('Sorting reviews by: ' + sortBy);
    });

    // Like/Dislike buttons
    $('.like-btn').click(function() {
        var currentCount = parseInt($(this).text());
        $(this).toggleClass('active');
        
        if($(this).hasClass('active')) {
            $(this).text(currentCount + 1);
        } else {
            $(this).text(currentCount - 1);
        }
    });

    // Report this link
    $('.report-link').click(function(e) {
        e.preventDefault();
        var reportSection = $(this).closest('li').find('.report-sec');
        
        if(reportSection.is(':visible')) {
            reportSection.slideUp();
        } else {
            $('.report-sec').slideUp();
            reportSection.slideDown();
        }
    });

    // Report form buttons
    $('.cancel-btn').click(function(e) {
        e.preventDefault();
        $(this).closest('.report-sec').slideUp();
        $(this).closest('.report-sec').find('select, textarea').val('');
    });

    $('.apply-btn').click(function(e) {
        e.preventDefault();
        var reportReason = $(this).closest('.report-sec').find('select').val();
        var reportDescription = $(this).closest('.report-sec').find('textarea').val();
        
        if(!reportReason) {
            alert('Please select a reason for reporting');
            return;
        }
        
        alert('Review reported successfully!');
        $(this).closest('.report-sec').slideUp();
        $(this).closest('.report-sec').find('select, textarea').val('');
    });

    // Product image click (lightbox effect)
    $('.review-img-box a').click(function(e) {
        e.preventDefault();
        var imgSrc = $(this).find('img').attr('src');
        alert('Image preview: ' + imgSrc);
        // In real application, this would open a lightbox/modal
    });

    // Visit store link
    $('a[href*="Visit Selinium"]').click(function(e) {
        e.preventDefault();
        alert('Redirecting to Selinium Yoga store');
    });
});
</script>

<style>
.quantity-box.active,
.color-box.active {
    border-color: #3498db;
    background-color: #f0f8ff;
}

.like-btn.active {
    color: #004392;
    background-color: #f0f8ff;
}

.report-sec {
    background-color: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-top: 15px;
    border: 1px solid #dee2e6;
}
</style>
@endpush