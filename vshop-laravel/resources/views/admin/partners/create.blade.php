@extends('layouts.vertical', ['title' => 'Add Partners'])

@section('content')
<h3>Add New Partners</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.partners.store') }}" method="POST" enctype="multipart/form-data">
    @csrf

    <div class="mb-3">
        <label class="form-label">Partner Logos</label>
        <input type="file" name="partner_logo[]" class="form-control" id="partnerLogoInput" multiple required>
        @error('partner_logo.*')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Partner Names (Optional)</label>
        <input type="text" name="partner_name[]" class="form-control" placeholder="Enter name for each logo, optional" multiple>
        @error('partner_name.*')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    {{-- Preview Selected Logos --}}
    <div class="mb-3" id="previewContainer" style="display:flex; flex-wrap:wrap; gap:10px;"></div>

    <button class="btn btn-primary">Add Partners</button>
</form>
@endsection

@section('scripts')
<script>
    const partnerLogoInput = document.getElementById('partnerLogoInput');
    const previewContainer = document.getElementById('previewContainer');

    partnerLogoInput.addEventListener('change', function() {
        previewContainer.innerHTML = ''; // Clear previous previews
        const files = this.files;

        if(files){
            Array.from(files).forEach(file => {
                const reader = new FileReader();
                reader.onload = function(e){
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.style.width = '120px';
                    img.style.height = '80px';
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
