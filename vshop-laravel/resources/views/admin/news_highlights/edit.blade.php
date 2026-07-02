@extends('layouts.vertical', ['title' => 'Edit News Highlight'])

@section('content')
<h3>Edit News Highlight</h3>

<form method="POST" action="{{ route('admin.newshighlights.update', $news->id) }}" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Title</label>
        <input type="text" name="title" class="form-control" value="{{ old('title', $news->title) }}" required>
        @error('title') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="3">{{ old('description', $news->description) }}</textarea>
        @error('description') <span class="text-danger">{{ $message }}</span> @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Images</label>
        <input type="file" name="images[]" class="form-control" id="imageInput" multiple>
        @error('images.*') <span class="text-danger">{{ $message }}</span> @enderror

        {{-- Existing images --}}
        <div id="imagePreviewContainer" class="mt-2" style="display:flex; flex-wrap:wrap; gap:10px;">
            @if($news->image)
                @foreach(json_decode($news->image) as $img)
                    <img src="{{ asset('uploads/news/'.$img) }}" style="width:150px; height:100px; object-fit:cover; border:1px solid #ccc; border-radius:5px;">
                @endforeach
            @endif
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Update</button>
</form>
@endsection

@section('scripts')
<script>
const imageInput = document.getElementById('imageInput');
const previewContainer = document.getElementById('imagePreviewContainer');

imageInput.addEventListener('change', function() {
    previewContainer.innerHTML = '';
    const files = imageInput.files;

    if(files){
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = function(e){
                const img = document.createElement('img');
                img.src = e.target.result;
                img.style.width = '150px';
                img.style.height = '100px';
                img.style.objectFit = 'cover';
                img.style.border = '1px solid #ccc';
                img.style.borderRadius = '5px';
                previewContainer.appendChild(img);
            }
            reader.readAsDataURL(file);
        });
    }
});
</script>
@endsection
