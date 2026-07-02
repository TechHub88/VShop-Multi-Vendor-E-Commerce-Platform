@extends('vshop.main')

@section('title', 'Visual Showcase & Video Gallery')

@push('styles')
<style>
    body {
        background-color: #f8fafc !important;
    }
    .visual-showcase-container {
        padding: 30px 20px;
        max-width: 1400px;
        margin: 0 auto;
    }
    .showcase-header {
        background: linear-gradient(135deg, #004392 0%, #0a192f 100%);
        border-radius: 20px;
        padding: 40px 30px;
        color: white;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(0, 67, 146, 0.15);
        position: relative;
        overflow: hidden;
    }
    .showcase-header::after {
        content: '';
        position: absolute;
        right: -50px;
        top: -50px;
        width: 250px;
        height: 250px;
        background: rgba(255, 255, 255, 0.08);
        border-radius: 50%;
        pointer-events: none;
    }
    .showcase-title {
        font-size: 32px;
        font-weight: 800;
        margin-bottom: 8px;
        letter-spacing: -0.5px;
        color: #ffffff !important;
    }
    .showcase-subtitle {
        font-size: 15px;
        opacity: 0.85;
        max-width: 600px;
        margin-bottom: 0;
        color: #ffffff !important;
    }
    
    /* Modern Custom Tabs */
    .showcase-nav-pills {
        display: inline-flex;
        background: #e2e8f0;
        padding: 6px;
        border-radius: 50px;
        margin-bottom: 30px;
    }
    .showcase-nav-pills .nav-link {
        color: #4a5568;
        font-weight: 700;
        font-size: 14px;
        padding: 10px 26px;
        border-radius: 50px;
        transition: all 0.3s ease;
        border: none;
    }
    .showcase-nav-pills .nav-link.active {
        background: #004392;
        color: white;
        box-shadow: 0 4px 12px rgba(0, 67, 146, 0.25);
    }
    .showcase-nav-pills .nav-link:hover:not(.active) {
        color: #004392;
        background: rgba(255, 255, 255, 0.6);
    }

    /* Video Card Grid */
    .video-gallery-card {
        background: #000;
        border-radius: 18px;
        overflow: hidden;
        position: relative;
        box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e2e8f0;
        height: 420px;
    }
    .video-gallery-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    .video-gallery-card video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    .video-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0.1) 60%, rgba(0,0,0,0) 100%);
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 24px;
        transition: background 0.3s ease;
    }
    .play-btn-circle {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(8px);
        border: 2px solid white;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        cursor: pointer;
        transition: transform 0.2s ease, background 0.2s ease;
    }
    .play-btn-circle:hover {
        transform: translate(-50%, -50%) scale(1.1);
        background: #2874f0;
        border-color: #2874f0;
    }

    /* Feature Highlights Grid */
    .feature-gallery-card {
        background: #ffffff;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 4px 18px rgba(0,0,0,0.04);
        border: 1px solid #e2e8f0;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .feature-gallery-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.08);
    }
    .feature-img-box {
        width: 100%;
        aspect-ratio: 16/10;
        overflow: hidden;
        background: #f1f5f9;
        position: relative;
    }
    .feature-img-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .feature-gallery-card:hover .feature-img-box img {
        transform: scale(1.05);
    }
    .feature-content {
        padding: 22px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }
    .feature-tag {
        display: inline-block;
        background: #eef4ff;
        color: #004392;
        font-size: 11px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 20px;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
</style>
@endpush

@section('content')
<div class="visual-showcase-container">
    {{-- Hero Banner --}}
    <div class="showcase-header d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h1 class="showcase-title"><i class="fa-solid fa-sparkles text-warning me-2"></i> Visual Showcase & Gallery</h1>
            <p class="showcase-subtitle">Explore our exclusive video advertisements, interactive product demonstrations, and curated feature highlights all in one place.</p>
        </div>
        <div>
            <a href="{{ route('home') }}" class="btn btn-light rounded-pill px-4 py-2 fw-bold" style="color: #004392; box-shadow: 0 4px 12px rgba(0,0,0,0.1);"><i class="fa-solid fa-arrow-left me-2"></i> Back to Dashboard</a>
        </div>
    </div>

    {{-- Tabs --}}
    @php
        $activeTab = request()->get('tab', 'videos');
    @endphp
    <div class="text-center">
        <ul class="nav showcase-nav-pills" id="showcaseTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab != 'features' ? 'active' : '' }}" id="videos-tab" data-bs-toggle="tab" data-bs-target="#videos-pane" type="button" role="tab" aria-controls="videos-pane" aria-selected="{{ $activeTab != 'features' ? 'true' : 'false' }}">
                    <i class="fa-solid fa-play-circle me-2"></i> Watch All Videos ({{ isset($videoAds) ? count($videoAds) : 0 }})
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link {{ $activeTab == 'features' ? 'active' : '' }}" id="features-tab" data-bs-toggle="tab" data-bs-target="#features-pane" type="button" role="tab" aria-controls="features-pane" aria-selected="{{ $activeTab == 'features' ? 'true' : 'false' }}">
                    <i class="fa-solid fa-layer-group me-2"></i> Featured Highlights ({{ isset($featuresAds) ? count($featuresAds) : 0 }})
                </button>
            </li>
        </ul>
    </div>

    {{-- Tab Content --}}
    <div class="tab-content mt-2" id="showcaseTabContent">
        
        {{-- VIDEOS PANE --}}
        <div class="tab-pane fade {{ $activeTab != 'features' ? 'show active' : '' }}" id="videos-pane" role="tabpanel" aria-labelledby="videos-tab">
            @if(isset($videoAds) && count($videoAds) > 0)
                <div class="row g-4">
                    @foreach($videoAds as $banner)
                        @if($banner->media->count() > 0)
                            @foreach($banner->media as $media)
                                @if($media->media_type == 'video' || pathinfo($media->file_path, PATHINFO_EXTENSION) == 'mp4')
                                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                        <div class="video-gallery-card">
                                            <video class="showcase-video-elem" preload="none" loop playsinline muted>
                                                <source src="{{ asset($media->file_path) }}" type="video/mp4">
                                            </video>
                                            <div class="video-overlay">
                                                <h4 class="text-white fw-bold mb-1" style="font-size: 18px;">{{ $banner->title }}</h4>
                                                @if($banner->description)
                                                    <p class="text-white-50 mb-3 small" style="line-height: 1.4;">{{ $banner->description }}</p>
                                                @endif
                                                @if($banner->redirect_url)
                                                    <div>
                                                        <a href="{{ $banner->redirect_url }}" class="btn btn-primary btn-sm rounded-pill px-3 fw-bold" style="background: #2874f0; border: none;">Shop Now <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="play-btn-circle" onclick="togglePlayVideo(this, '{{ asset($media->file_path) }}', '{{ addslashes($banner->title) }}')">
                                                <i class="fa-solid fa-play ms-1"></i>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 my-5 bg-white rounded-4 shadow-sm border">
                    <div style="font-size: 64px; color: #cbd5e1;" class="mb-3"><i class="fa-solid fa-video-slash"></i></div>
                    <h4 class="fw-bold text-dark">No Videos Available Right Now</h4>
                    <p class="text-muted mb-4">We are currently curating new video showcases for you. Check back soon!</p>
                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 py-2">Return to Shopping</a>
                </div>
            @endif
        </div>

        {{-- FEATURES PANE --}}
        <div class="tab-pane fade {{ $activeTab == 'features' ? 'show active' : '' }}" id="features-pane" role="tabpanel" aria-labelledby="features-tab">
            @if(isset($featuresAds) && count($featuresAds) > 0)
                <div class="row g-4">
                    @foreach($featuresAds as $banner)
                        @if($banner->media->count() > 0)
                            @foreach($banner->media as $media)
                                @if($media->media_type == 'image')
                                    <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                        <div class="feature-gallery-card">
                                            <div class="feature-img-box">
                                                <a href="{{ $banner->redirect_url ? $banner->redirect_url : route('discount.products') }}">
                                                    <img src="{{ asset($media->file_path) }}" alt="{{ $banner->title }}">
                                                </a>
                                            </div>
                                            <div class="feature-content">
                                                <div>
                                                    <span class="feature-tag">Featured Highlight</span>
                                                    <h4 class="fw-bold mb-2 text-dark" style="font-size: 18px;">{{ $banner->title }}</h4>
                                                    @if($banner->description)
                                                        <p class="text-muted small mb-3" style="line-height: 1.5;">{{ $banner->description }}</p>
                                                    @endif
                                                </div>
                                                <div class="pt-2 border-top">
                                                    <a href="{{ $banner->redirect_url ? $banner->redirect_url : route('discount.products') }}" class="btn btn-outline-primary btn-sm rounded-pill w-100 fw-bold" style="color: #004392; border-color: #004392;">Explore Offer <i class="fa-solid fa-arrow-right ms-1"></i></a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        @endif
                    @endforeach
                </div>
            @else
                <div class="text-center py-5 my-5 bg-white rounded-4 shadow-sm border">
                    <div style="font-size: 64px; color: #cbd5e1;" class="mb-3"><i class="fa-solid fa-layer-group"></i></div>
                    <h4 class="fw-bold text-dark">No Featured Highlights Found</h4>
                    <p class="text-muted mb-4">New highlights and exclusive features are added regularly.</p>
                    <a href="{{ route('home') }}" class="btn btn-primary rounded-pill px-4 py-2">Return to Shopping</a>
                </div>
            @endif
        </div>

    </div>
</div>

<!-- Video Player Modal -->
<div class="modal fade" id="videoShowcaseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content bg-dark text-white border-0 rounded-4 overflow-hidden shadow-lg">
      <div class="modal-header border-0 pb-1 d-flex justify-content-between align-items-center" style="background: #0d1117;">
        <h5 class="modal-title fw-bold text-truncate" id="videoModalTitle" style="font-size: 18px;">Video Player</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close" onclick="stopModalVideo()"></button>
      </div>
      <div class="modal-body p-0 position-relative" style="background: #000; aspect-ratio: 16/9; display: flex; align-items: center; justify-content: center;">
        <video id="modalVideoPlayer" class="w-100 h-100" controls autoplay style="max-height: 70vh;">
            <source src="" type="video/mp4">
            Your browser does not support HTML5 video.
        </video>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
    function togglePlayVideo(btnElem, videoUrl, videoTitle) {
        let modal = new bootstrap.Modal(document.getElementById('videoShowcaseModal'));
        let player = document.getElementById('modalVideoPlayer');
        document.getElementById('videoModalTitle').innerText = videoTitle || 'Watch Video';
        
        player.src = videoUrl;
        modal.show();
        player.play();
    }

    function stopModalVideo() {
        let player = document.getElementById('modalVideoPlayer');
        if (player) {
            player.pause();
            player.src = '';
        }
    }

    // Stop video when modal closes by clicking backdrop or ESC
    document.getElementById('videoShowcaseModal').addEventListener('hidden.bs.modal', function () {
        stopModalVideo();
    });

    // Auto preview on hover for desktop
    $(document).ready(function() {
        if (!('ontouchstart' in window)) {
            $('.video-gallery-card').hover(
                function() {
                    let vid = $(this).find('video')[0];
                    if (vid) {
                        vid.play().catch(e => {});
                    }
                },
                function() {
                    let vid = $(this).find('video')[0];
                    if (vid) {
                        vid.pause();
                        vid.currentTime = 0;
                    }
                }
            );
        }
    });
</script>
@endpush
@endsection
