@extends('layouts.vertical', ['title' => 'Edit Slider'])

@section('content')
<h3>Edit Slider</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.sliders.update', $slider->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Banner Image</label>
        <input type="file" name="banner_image" class="form-control" id="bannerInput">
        @error('banner_image')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <img id="bannerPreview" class="mt-2" style="width:150px; height:80px; object-fit:cover;"
             src="{{ asset('uploads/slider/'.$slider->banner_image) }}">
    </div>

    <div class="mb-3">
        <label class="form-label">Second Image</label>
        <input type="file" name="second_image" class="form-control" id="secondInput">
        @error('second_image')
            <span class="text-danger">{{ $message }}</span>
        @enderror
        <img id="secondPreview" class="mt-2" style="width:150px; height:80px; object-fit:cover;"
             src="{{ asset('uploads/slider/'.$slider->second_image) }}">
    </div>

    <button type="submit" class="btn btn-primary">Update Slider</button>
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
        }
    });

    const secondInput = document.getElementById('secondInput');
    const secondPreview = document.getElementById('secondPreview');
    secondInput.addEventListener('change', function(){
        const file = this.files[0];
        if(file){
            secondPreview.src = URL.createObjectURL(file);
        }
    });
</script>
@endsection
