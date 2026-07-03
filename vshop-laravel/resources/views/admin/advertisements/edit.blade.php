@extends('layouts.vertical', ['title' => 'Edit Advertisement'])

@section('content')

<style>
    .modern-card {
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(13, 110, 253, 0.08);
        background: #ffffff;
        overflow: hidden;
    }
    .modern-card-header {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        color: white;
        padding: 20px 25px;
        border-bottom: none;
    }
    .modern-card-header h4 {
        margin: 0;
        font-weight: 600;
        font-size: 1.25rem;
    }
    .modern-card-body {
        padding: 30px;
    }
    .form-group-modern {
        margin-bottom: 1.5rem;
    }
    .form-group-modern label {
        font-weight: 500;
        color: #495057;
        margin-bottom: 8px;
        display: block;
    }
    .form-control-modern {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 10px 15px;
        transition: all 0.3s ease;
        background-color: #f8f9fa;
    }
    .form-control-modern:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        background-color: #ffffff;
    }
    .btn-modern-primary {
        background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
        border: none;
        border-radius: 8px;
        padding: 12px 25px;
        font-weight: 600;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        color: white !important;
    }
    .btn-modern-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
        background: linear-gradient(135deg, #0b5ed7 0%, #084298 100%);
        color: white !important;
    }
    .section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #0d6efd;
        margin-bottom: 20px;
        padding-bottom: 10px;
        border-bottom: 2px solid #e9ecef;
    }
    .instruction-badge {
        display: inline-block;
        background-color: #e7f1ff;
        color: #0d6efd;
        padding: 5px 12px;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-top: 8px;
        border: 1px solid #b6d4fe;
    }
</style>

<div class="container-fluid" style="padding-top: 20px;">
    
    <div class="card modern-card mb-4">
        <div class="modern-card-header d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fas fa-edit me-2" style="font-size: 1.3rem;"></i>
                <h4>Edit Advertisement</h4>
            </div>
            <a href="{{ route('admin.ads.index') }}" class="btn btn-light btn-sm rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i> Back to List
            </a>
        </div>
        
        <div class="card-body modern-card-body">
            <form method="POST" action="{{ route('admin.ads.update', $ad->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <div class="section-title">
                    <i class="fas fa-info-circle me-2"></i> Basic Information
                </div>
                
                <div class="row">
                    <div class="col-md-6 form-group-modern">
                        <label>Campaign Title</label>
                        <input type="text" name="title" class="form-control form-control-modern" value="{{ $ad->title }}" placeholder="Enter an engaging title" required>
                    </div>

                    <div class="col-md-6 form-group-modern">
                        <label>Target Audience</label>
                        <select name="target_type" id="target_type" class="form-control form-control-modern">
                            <option value="product" {{ $ad->target_type == 'product' ? 'selected' : '' }}>Vshop (Products)</option>
                            <option value="service" {{ $ad->target_type == 'service' ? 'selected' : '' }}>Vtravel (Services)</option>
                            <option value="both" {{ $ad->target_type == 'both' ? 'selected' : '' }}>Both</option>
                        </select>
                    </div>

                    <div class="col-md-6 form-group-modern product-box">
                        <label>Select Category</label>
                        <select name="category_id" id="categorySelect" class="form-control form-control-modern">
                            <option value="" data-name="">Select Target Category</option>
                            @php
                                $uniqueCategories = $categories->unique('name');
                            @endphp
                            @foreach($uniqueCategories as $c)
                                <option value="{{ $c->id }}" data-name="{{ $c->name }}" {{ $ad->category_id == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6 form-group-modern product-box">
                        <label>Select Product (Optional)</label>
                        <select name="product_id" id="productSelect" class="form-control form-control-modern">
                            <option value="">Select Target Product</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" data-category-name="{{ $p->category ? $p->category->name : '' }}" {{ $ad->product_id == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ ucfirst($p->type ?? 'product') }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Current Media -->
                @if($ad->media && count($ad->media) > 0)
                    <div class="section-title mt-4">
                        <i class="fas fa-images me-2"></i> Current Media
                    </div>
                    <div class="row mb-3">
                        @foreach($ad->media as $m)
                            <div class="col-md-3 mb-3 media-item-box" id="media-card-{{ $m->id }}">
                                <div class="card shadow-sm h-100 border-0" style="border-radius: 10px; overflow: hidden; background: #f8f9fa; border: 1px solid #e9ecef !important;">
                                    <div style="height: 140px; display: flex; align-items: center; justify-content: center; overflow: hidden; background: #212529;">
                                        @if($m->media_type == 'video' || Str::endsWith(strtolower($m->file_path), ['.mp4', '.webm', '.mov']))
                                            <video src="{{ asset($m->file_path) }}" style="max-height: 100%; max-width: 100%;" controls></video>
                                        @else
                                            <img src="{{ asset($m->file_path) }}" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                        @endif
                                    </div>
                                    <div class="card-body p-2 text-center">
                                        <button type="button" class="btn btn-danger btn-sm w-100 remove-media-btn" data-id="{{ $m->id }}">
                                            <i class="fa fa-trash me-1"></i> Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Media & Layout -->
                <div class="section-title mt-4">
                    <i class="fas fa-photo-video me-2"></i> Add New Media & Layout
                </div>
                
                <div class="row">
                    <div class="col-md-6 form-group-modern">
                        <label>Advertisement Type</label>
                        <select name="type" class="form-control form-control-modern">
                            <option value="banner" {{ $ad->type == 'banner' ? 'selected' : '' }}>Banner</option>
                            <option value="video" {{ $ad->type == 'video' ? 'selected' : '' }}>Video</option>
                            <option value="campaign" {{ $ad->type == 'campaign' ? 'selected' : '' }}>Campaign</option>
                            <option value="featured" {{ $ad->type == 'featured' ? 'selected' : '' }}>Featured</option>
                        </select>
                        <div id="type_instruction" class="instruction-badge">
                            <i class="fas fa-crop-alt me-1"></i> <span>Size as per Banner standard</span>
                        </div>
                    </div>

                    <div class="col-md-6 form-group-modern">
                        <label>Upload Additional Images</label>
                        <input type="file" name="images[]" multiple class="form-control form-control-modern">
                        <small class="text-muted d-block mt-2" style="font-size: 11px; line-height: 1.4;">
                            <i class="fas fa-info-circle text-primary me-1"></i> <strong>Formats:</strong> JPG, PNG, WEBP. Max 2MB per image.<br>
                            - <strong>Banner:</strong> 1200 x 380 px<br>
                            - <strong>Campaign:</strong> 1600 x 88 px (Full-width divider)<br>
                            - <strong>Featured:</strong> 263 x 162 px (Sidebar slider)
                        </small>
                    </div>

                    <div class="col-md-6 form-group-modern">
                        <label>Upload Additional Videos</label>
                        <input type="file" name="videos[]" multiple class="form-control form-control-modern">
                        <small class="text-muted d-block mt-2" style="font-size: 11px; line-height: 1.4;">
                            <i class="fas fa-info-circle text-primary me-1"></i> <strong>Formats:</strong> MP4 (H.264), WEBM. Max 10MB.<br>
                            - <strong>Dimensions:</strong> Vertical 9:16 (1080 x 1920 px or 720 x 1280 px)<br>
                            - <strong>Duration:</strong> 10 to 15 seconds (Max 30 seconds)
                        </small>
                    </div>
                </div>

                <!-- Schedule & Description -->
                <div class="section-title mt-4">
                    <i class="fas fa-calendar-alt me-2"></i> Schedule & Details
                </div>
                
                <div class="row">
                    <div class="col-md-6 form-group-modern">
                        <label>Start Date</label>
                        <input type="datetime-local" name="start_date" class="form-control form-control-modern" value="{{ $ad->start_date ? date('Y-m-d\TH:i', strtotime($ad->start_date)) : '' }}">
                    </div>

                    <div class="col-md-6 form-group-modern">
                        <label>End Date</label>
                        <input type="datetime-local" name="end_date" class="form-control form-control-modern" value="{{ $ad->end_date ? date('Y-m-d\TH:i', strtotime($ad->end_date)) : '' }}">
                    </div>

                    <div class="col-md-12 form-group-modern">
                        <label>Description (Optional)</label>
                        <textarea name="description" class="form-control form-control-modern" rows="3" placeholder="Additional details about this campaign...">{{ $ad->description }}</textarea>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-modern-primary btn-lg">
                        <i class="fas fa-save me-2"></i> Update Advertisement
                    </button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
// Dynamic dimensions text
const typeSelect = document.querySelector('select[name="type"]');
const instructionEl = document.querySelector('#type_instruction span');

function updateInstruction() {
    let type = typeSelect.value;
    let text = "";
    if(type === 'video') text = "Recommended: Vertical 9:16 (e.g. 1080 x 1920 px), Max 10MB, Max 30s";
    else if(type === 'campaign') text = "Recommended: 1600 x 88 px (Thin horizontal strip)";
    else if(type === 'banner') text = "Recommended: 1200 x 380 px (Standard Hero Banner)";
    else if(type === 'featured') text = "Recommended: 263 x 162 px (Sidebar card slider)";
    
    instructionEl.innerText = text;
}

typeSelect.addEventListener('change', updateInstruction);
updateInstruction();

// Dependent Product Dropdown Logic
const catSelect = document.getElementById('categorySelect');
const prodSelect = document.getElementById('productSelect');
const originalProdOptions = Array.from(prodSelect.querySelectorAll('option'));

catSelect.addEventListener('change', function() {
    let selectedOption = this.options[this.selectedIndex];
    let catName = selectedOption.getAttribute('data-name');
    
    prodSelect.innerHTML = '';
    
    originalProdOptions.forEach(opt => {
        if(opt.value === "") {
            prodSelect.appendChild(opt);
            return;
        }
        
        let pCatName = opt.getAttribute('data-category-name');
        
        if(!catName || pCatName === catName) {
            prodSelect.appendChild(opt);
        }
    });
});

// Remove Media AJAX Logic
document.querySelectorAll('.remove-media-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        let mediaId = this.getAttribute('data-id');
        let card = document.getElementById('media-card-' + mediaId);
        
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'Are you sure?',
                text: "You want to remove this media file?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, remove it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    deleteMediaAjax(mediaId, card);
                }
            });
        } else if (confirm('Are you sure you want to remove this media file?')) {
            deleteMediaAjax(mediaId, card);
        }
    });
});

function deleteMediaAjax(mediaId, card) {
    let url = "{{ route('admin.ads.delete.media', ':id') }}".replace(':id', mediaId);
    fetch(url, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            if (card) card.remove();
            if (typeof Swal !== 'undefined') {
                Swal.fire('Removed!', data.message, 'success');
            } else {
                alert(data.message);
            }
        } else {
            if (typeof Swal !== 'undefined') {
                Swal.fire('Error!', data.message || 'Something went wrong.', 'error');
            } else {
                alert(data.message || 'Something went wrong.');
            }
        }
    })
    .catch(err => {
        console.error(err);
        if (typeof Swal !== 'undefined') {
            Swal.fire('Error!', 'Something went wrong.', 'error');
        } else {
            alert('Something went wrong.');
        }
    });
}
</script>

@endsection