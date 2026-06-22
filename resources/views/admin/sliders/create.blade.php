@extends('layouts.vertical', ['title' => 'Add Slider'])

@section('content')
<h3>Add New Slider</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.sliders.store') }}" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="form-label">Banner Image</label>
        <input type="file" name="banner_image" class="form-control" id="bannerInput" required>
        @error('banner_image')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <img id="bannerPreview" class="mt-2" style="width:150px; height:80px; object-fit:cover; display:none;">
    </div>

    <div class="mb-3">
        <label class="form-label">Second Image</label>
        <input type="file" name="second_image" class="form-control" id="secondInput" required>
        @error('second_image')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <img id="secondPreview" class="mt-2" style="width:150px; height:80px; object-fit:cover; display:none;">
    </div>

    <button type="submit" class="btn btn-primary">Save Slider</button>
</form>
@endsection

@section('scripts')
<script>
    const bannerInput = document.getElementById('bannerInput');
    const bannerPreview = document.getElementById('bannerPreview');
    bannerInput.addEventListener('change', function(){
        const file = this.files[0];
        if(file){
            bannerPreview.src = URL.createObjectURL(file);
            bannerPreview.style.display = 'block';
        }
    });

    const secondInput = document.getElementById('secondInput');
    const secondPreview = document.getElementById('secondPreview');
    secondInput.addEventListener('change', function(){
        const file = this.files[0];
        if(file){
            secondPreview.src = URL.createObjectURL(file);
            secondPreview.style.display = 'block';
        }
    });
</script>
@endsection
