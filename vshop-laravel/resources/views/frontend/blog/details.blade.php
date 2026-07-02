@extends('frontend.layout')

<style>
    .single-blog-title {
        font-size: 32px !important;
    }
    .single-blog-content h2 {
        font-size: 24px;
    }
    .introdus {
        margin-bottom: 30px;
    }
    .single-blog-content h3 {
        font-size: 23px;
    }
    .single-blog-content h4 {
        font-size: 20px;
    }
    .blog-meta {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .social-media-sec {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .social-media-sec a {
        font-size: 14px;
        line-height: 40px;
        display: inline-block;
        width: 30px;
        height: 30px;
        background-color: #d7d7d7;
        color: #fff;
        border-radius: 50%;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .blog-user-box {
        display: flex;
        align-items: center;
    }
    .blog-img-box {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        overflow: hidden;
    }
    .blog-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .blog-info-box {
        padding-left: 10px;
    }
    .blog-info-box h4 {
        font-size: 16px;
        margin: 0;
    }
    .blog-info-box p {
        margin: 0;
        color: #7e7e7e;
    }
    .single-blog-cont-box {
        margin-bottom: 25px;
    }
    .single-blog-cont-box:last-child {
        margin-bottom: 0px;
    }
</style>

@section('content')


<section class="bedcum-sec" style="padding-top: 120px;">
            <div class="container">
                <div class="bedcum-inner">	
                    <div class="bed-cum-list">
                        <ul>
                            <li>
                                <a href="#">Home</a>
                            </li>
                            <li>
                                <i class="fa-regular fa-angle-right"></i>
                            </li>
                            <li>
                                <span>Blog Details</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

<section class="singleBlogPage pb-60">
   <div class="container">
      <div class="row rowBox">
         <!-- LEFT CONTENT -->
         <div class="col-lg-8 col-md-8 col-sm-12 col-12 columnBox leftColumnBox">
            <div class="single-blog-details-area">
                <!-- Title -->
               <h1 class="single-blog-title">{{ $blog->title }}</h1>
               <div class="introdus">
                   <p>In today’s competitive business environment, appearance plays a vital role in shaping brand perception. A professional dress code helps organizations project trust, discipline, and consistency. This is why businesses rely on experienced corporate uniform manufacturers to deliver high-quality, comfortable, and visually consistent uniforms. Among the well-known names in the industry, Mafatlal Uniforms stands out for its legacy, fabric expertise, and reliable corporate uniform solutions.</p>
                   <p>From startups to large enterprises, choosing the right corporate uniform suppliers ensures employees look professional while representing the brand effectively.</p>
                </div>
               <!-- Featured Image -->
               <div class="single-blog-image">
					<!--<img src="{{ asset('uploads/blogs/'.$blog->image) }}" class="w-100 img-fluid" alt="Thumb">-->
                  @if($blog->featured_image)
						<img src="{{ asset($blog->featured_image) }}" class="w-100 img-fluid" alt="{{ $blog->title }}">
					@else
						<img src="{{ asset('assets/images/blog-1-1.jpg') }}" class="w-100 img-fluid" alt="{{ $blog->title }}">
					@endif
               </div>
               <!-- Blog Meta -->
               <div class="blog-meta">
                   <div class="blog-user-box">
                       <div class="blog-img-box">
                           <img src="https://kidzz.in/mafatlal/public/uploads/category_banners/1764319933_categoryThumnail6.png">
                       </div>
                       <div class="blog-info-box">
                           <h4>By {{ $blog->author->name ?? 'Admin' }}</h4>
                           <p>Jan 13, 2026</p>
                       </div>
                   </div>
                  
                  <div class="social-media-sec">
                      <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
                      <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
                      <a href="#"><i class="fa-regular fa-share-nodes"></i></a>
                  </div>
               </div>
               <!-- Content -->
               <div class="single-blog-content">
				    <div class="single-blog-cont-box">
				    <h2>Why Corporate Uniforms Matter for Your Brand</h2>
                    <p>Corporate uniforms are more than just clothing; they are a direct reflection of your company’s identity.</p>
                    <h4>Builds a Professional Identity</h4>
                    <p>Uniforms create a cohesive and polished appearance across teams, making your business look structured and credible. Established brands like Mafatlal Uniforms understand how professional attire influences brand perception.</p>
                    <h4>Improves Employee Confidence</h4>
                    <p>Well-designed corporate uniforms for office staff improve comfort and confidence, allowing employees to focus on performance rather than appearance.</p>
                    <h4>Strengthens Brand Recognition</h4>
                    <p>Uniforms featuring brand colors and logos help reinforce brand recall. Trusted company uniform manufacturers ensure every detail aligns with your branding guidelines.</p>
                    </div>
                    <div class="single-blog-cont-box">
                    <h2>What Corporate Uniform Manufacturers Offer</h2>
                    <p>Leading corporate uniform manufacturers provide a comprehensive range of uniform solutions tailored to office environments.</p>
                    <h4>Office Shirts & Trousers</h4>
                    <p>Formally designed shirts and trousers made with breathable, durable fabrics suitable for daily wear.</p>
                    <h4>Blazers & Formal Wear</h4>
                    <p>Premium blazers and suits for management teams, front-office staff, and client-facing roles an area where Mafatlal Uniforms is particularly known for quality tailoring.</p>
                    <h4>Corporate Uniforms for Ladies</h4>
                    <p>Elegant and comfortable designs that balance professionalism and style for women employees. Reliable corporate uniform suppliers focus on long-lasting quality and consistent production standards.</p>
                    </div>
                    <div class="single-blog-cont-box">
                    <h2>How to Choose the Best Corporate Uniform Manufacturer</h2>
                    <p>Selecting the right company uniform manufacturer is crucial for long-term value and consistency.</p>
                    <h4>Fabric Durability & Comfort</h4>
                    <p>High-quality fabrics ensure uniforms remain comfortable throughout long working hours. Manufacturers like Mafatlal Uniforms leverage decades of textile expertise to deliver superior fabric performance.</p>
                    <h4>Custom Branding Options</h4>
                    <p>Look for manufacturers offering embroidery, logo printing, and precise brand color matching.</p>
                    <h4>Consistent Sizing Standards</h4>
                    <p>Uniform sizing consistency across bulk orders ensures every employee receives a proper fit.</p>
                    <h4>Timely Bulk Delivery</h4>
                    <p>Professional corporate uniform manufacturers are equipped to handle large orders while meeting strict delivery timelines.</p>
                    <h4>Custom Corporate Uniform Design Process</h4>
                    <p>A structured design process ensures the final uniforms meet brand and comfort expectations.</p>
                    <h4>Brand Color Matching</h4>
                    <p>Colors are matched accurately to your corporate identity for a consistent visual appearance.</p>
                    <h4>Logo Placement</h4>
                    <p>Logos are strategically placed for visibility without compromising the uniform’s design.</p>
                    <h4>Trial Samples & Approvals</h4>
                    <p>Sample uniforms are shared for fit and quality checks before mass production begins a standard practice followed by reputed names like Mafatlal Uniforms.</p>
                    </div>
                    <div class="single-blog-cont-box">
                    <h2>Conclusion</h2>
                    <p>Investing in professional uniforms is an investment in your brand image and workforce confidence. Partnering with experienced corporate uniform manufacturers, including trusted names like Mafatlal Uniforms, ensures your employees look polished, professional, and comfortable throughout the workday.</p>
                    <p>By choosing reliable corporate uniform suppliers and a proven company uniform manufacturer, businesses can create uniforms that enhance brand recognition, support employee comfort, and deliver long-term value.</p>
                    </div>
               </div>
               <!-- COMMENTS AREA -->
               <!--<div id="comments-section" class="single-blog-comments-area">-->
               <!--   <h3 class="comments-title">-->
               <!--      {{ $commentCount ?? 0 }} {{ Str::plural('Comment', $commentCount ?? 0) }}-->
               <!--   </h3>-->
                  <!-- Comments List -->
               <!--   <ul class="comment-list">-->
               <!--      @forelse($comments ?? [] as $comment)-->
               <!--      <li class="comment">-->
               <!--         <div class="comment-body">-->
               <!--            <div class="comment-author">-->
               <!--               <strong>{{ $comment->name }}</strong>-->
               <!--               @if($comment->website)-->
               <!--               <a href="{{ $comment->website }}" target="_blank" rel="nofollow">{{ $comment->website }}</a>-->
               <!--               @endif-->
               <!--            </div>-->
               <!--            <div class="comment-meta">-->
               <!--               <time datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->format('F j, Y \a\t g:i a') }}</time>-->
               <!--            </div>-->
               <!--            <div class="comment-content">-->
               <!--               <p>{{ $comment->comment }}</p>-->
               <!--            </div>-->
                          
               <!--         </div>-->
               <!--         @if($comment->replies->count() > 0)-->
               <!--         <ul class="children">-->
               <!--            @foreach($comment->replies as $reply)-->
               <!--            <li class="comment">-->
               <!--               <div class="comment-body">-->
               <!--                  <div class="comment-author">-->
               <!--                     <strong>{{ $reply->name }}</strong>-->
               <!--                     @if($reply->website)-->
               <!--                     <a href="{{ $reply->website }}" target="_blank" rel="nofollow">{{ $reply->website }}</a>-->
               <!--                     @endif-->
               <!--                  </div>-->
               <!--                  <div class="comment-meta">-->
               <!--                     <time datetime="{{ $reply->created_at->toIso8601String() }}">{{ $reply->created_at->format('F j, Y \a\t g:i a') }}</time>-->
               <!--                  </div>-->
               <!--                  <div class="comment-content">-->
               <!--                     <p>{{ $reply->comment }}</p>-->
               <!--                  </div>-->
               <!--               </div>-->
               <!--            </li>-->
               <!--            @endforeach-->
               <!--         </ul>-->
               <!--         @endif-->
               <!--      </li>-->
               <!--      @empty-->
               <!--      <li class="comment">-->
               <!--         <p>No comments yet. Be the first to comment!</p>-->
               <!--      </li>-->
               <!--      @endforelse-->
               <!--   </ul>-->
                  <!-- Comment Form -->
               <!--   <div class="comment-form-area">-->
               <!--      @if(session('success'))-->
               <!--      <div class="alert alert-success">-->
               <!--         {{ session('success') }}-->
               <!--      </div>-->
               <!--      @endif-->
               <!--      @if($errors->any())-->
               <!--      <div class="alert alert-danger">-->
               <!--         <ul class="mb-0">-->
               <!--            @foreach($errors->all() as $error)-->
               <!--            <li>{{ $error }}</li>-->
               <!--            @endforeach-->
               <!--         </ul>-->
               <!--      </div>-->
               <!--      @endif-->
               <!--      <div id="respond" class="comment-respond">-->
               <!--         <h3 class="comment-reply-title">Leave a Reply <small><a rel="nofollow" id="cancel-comment-reply-link" href="#respond" style="display:none;">Cancel reply</a></small></h3>-->
               <!--         <form action="{{ route('blogs.comment.store', $blog->slug) }}" method="POST" class="comment-form" id="comment-form">-->
               <!--            @csrf-->
               <!--            <input type="hidden" name="parent_id" id="comment_parent" value="">-->
               <!--            <p class="comment-notes"><span id="email-notes">Your email address will not be published.</span> <span class="required-field-message">Required fields are marked <span class="required">*</span></span></p>-->
               <!--            <p class="comment-form-comment"><label for="comment">Comment <span class="required">*</span></label> <textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="">{{ old('comment') }}</textarea></p>-->
               <!--            <p class="comment-form-author"><label for="author">Name <span class="required">*</span></label> <input id="author" name="name" type="text" value="{{ old('name') }}" size="30" maxlength="245" autocomplete="name" required=""></p>-->
               <!--            <p class="comment-form-email"><label for="email">Email <span class="required">*</span></label> <input id="email" name="email" type="email" value="{{ old('email') }}" size="30" maxlength="100" aria-describedby="email-notes" autocomplete="email" required=""></p>-->
               <!--            <p class="comment-form-url"><label for="url">Website</label> <input id="url" name="website" type="url" value="{{ old('website') }}" size="30" maxlength="200" autocomplete="url"></p>-->
               <!--            <p class="comment-form-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes" checked=""> <label for="wp-comment-cookies-consent">Save my name, email, and website in this browser for the next time I comment.</label></p>-->
               <!--            <p class="form-submit"><input name="submit" type="submit" id="submit" class="submit" value="Post Comment"></p>-->
               <!--         </form>-->
               <!--      </div>-->
                     <!-- #respond -->
               <!--   </div>-->
               <!--</div>-->
            </div>
         </div>
         <div class="col-lg-4 col-md-4 col-sm-12 col-12 columnBox rightColumnBox">
            <!-- SIDEBAR -->
            <div class="single-blog-sidebar">
               <!-- Related Posts -->
               <div class="single-blog-related-posts-Area">
                  <div class="single-blog-related-posts-widget">
                     <h3 class="single-blog-related-title">Related Posts</h3>
                     <div class="single-blog-related-items">
                        @forelse($relatedBlogs ?? [] as $relatedBlog)
                        <div class="single-blog-related-item mb-3">
                           <a href="{{ route('blogs.details', $relatedBlog->slug) }}" class="d-flex gap-2 single-blog-related-item-inner">
                              <div class="single-blog-related-thumb" style="">
                                 @if($relatedBlog->featured_image)
                                    <img width="300" height="169" src="{{ asset($relatedBlog->featured_image) }}" class="img-fluid w-100 wp-post-image" alt="{{ $relatedBlog->title }}">
                                 @else
                                    <img width="300" height="169" src="{{ asset('assets/images/blog-1-1.jpg') }}" class="img-fluid w-100 wp-post-image" alt="{{ $relatedBlog->title }}">
                                 @endif
                              </div>
                              <div class="single-blog-related-info">
                                 <h5 class="single-blog-related-post-title">{{ Str::limit($relatedBlog->title, 50) }}</h5>
                                 <p class="mb-0 single-blog-related-date">{{ $relatedBlog->published_at ? $relatedBlog->published_at->format('M d, Y') : $relatedBlog->created_at->format('M d, Y') }}</p>
                              </div>
                           </a>
                        </div>
                        @empty
                        <p>No related posts found.</p>
                        @endforelse
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</section>


<!--
<div class="container py-5">

    <h2 class="fw-bold mb-3">{{ $blog->title }}</h2>

    <div class="mb-4 text-muted">
        Published on {{ $blog->created_at->format('d M, Y') }}
    </div>

    <img src="{{ asset('uploads/blogs/'.$blog->image) }}" class="img-fluid mb-4 rounded">

    <div class="blog-description">
        {!! $blog->description !!}
    </div>

</div>-->

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Scroll to comments section if success message is present
    @if(session('success'))
    setTimeout(function() {
        var commentsSection = document.getElementById('comments-section');
        if (commentsSection) {
            commentsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    }, 100);
    @endif

    // Handle reply links
    document.querySelectorAll('.comment-reply-link').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            var parentId = this.getAttribute('data-parent-id');
            document.getElementById('comment_parent').value = parentId;
            document.getElementById('cancel-comment-reply-link').style.display = 'inline';
            document.getElementById('respond').scrollIntoView({ behavior: 'smooth' });
        });
    });

    // Handle cancel reply
    var cancelLink = document.getElementById('cancel-comment-reply-link');
    if (cancelLink) {
        cancelLink.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('comment_parent').value = '';
            this.style.display = 'none';
        });
    }

    // Clear parent_id if it's 0 or empty before form submission
    var commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            var parentIdInput = document.getElementById('comment_parent');
            if (parentIdInput && (parentIdInput.value === '0' || parentIdInput.value === '')) {
                parentIdInput.value = '';
            }
        });
    }
});
</script>
@endpush
