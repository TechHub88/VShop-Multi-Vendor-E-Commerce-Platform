@extends('frontend.layout')

<style>
    @media(max-width: 767px){
        .blog-listing-box {
            margin-bottom: 24px;
        }
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
                                <span>Blog</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

<section class="blog-page blog-listing-sec pb-60">

<div class="container ">
  <div class="blog-listing-sec-inner">
  <div class="row rowBox">
        @forelse ($blogs as $blog)
		<div class="col-xxl- col-xl-3 col-lg-3 col-md-6 col-sm-12 col-12 columnBox blog-listing-box">	
			<div class="homeBlogBox">	
			    <div class="homeBlogBoxinner">
				<div class="thumbnail">
						<a href="{{ route('blogs.details', $blog->slug) }}" >
						    @if($blog->featured_image)
							    <img data-src="{{ asset('assets/images/blog-img-1.jpg')}}" class="img-fluid lazyload" alt="{{ $blog->title }}"> 
    						@else
    							<img src="{{ asset('assets/images/blog-1-1.jpg') }}" class="img-fluid" alt="{{ $blog->title }}">
    						@endif
						</a>
				</div>
				<div class="content">
					<ul class="d-flex btmList">
						<li class="d-flex align-items-center">
						    <span class="icon mr-6"><i class="fa-regular fa-calendar"></i></span>
						    <span class="txt">{{ $blog->published_at->format('M d, Y') }}</span>
						 </li>
					</ul>
					<h4 class="title"><a href="{{ route('blogs.details', $blog->slug) }}">{{ Str::limit($blog->title, 50) }}</a></h4>
					<p class="mb-0 para">Adipisicing dolor esse voluptate occaecat laborum fugiat adipisicing laboris id cupidatat deserunt exercitation et velit consectetur eiusmod pariatur ullamco enim ut nulla qui minim sunt minim amet non culpa aliqua</p>
				</div>
				</div>
			</div>	
		</div>
        @empty
		<div class="col-12">
			<div class="alert alert-info text-center">
				<h4>No blog posts found</h4>
				<p>Check back later for new blog posts.</p>
			</div>
		</div>
        @endforelse
    </div>
	</div>
	
    <div class="mt-4">
        {{ $blogs->links() }}
    </div>
</div>


 </section>


@endsection
