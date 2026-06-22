@extends('layouts.vertical', ['title' => 'Partners & Content'])

@section('content')
<h3>Partners & Content Management</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    {{-- PARTNER SECTION --}}
    <div class="col-md-6">
        <h4>Add New Partners</h4>
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
                <input type="text" name="partner_name[]" class="form-control" placeholder="Enter name for each logo" multiple>
            </div>

            {{-- Preview Selected Logos --}}
            <div class="mb-3" id="previewContainer" style="display:flex; flex-wrap:wrap; gap:10px;"></div>

            <button class="btn btn-primary">Add Partners</button>
        </form>

        <h4 class="mt-4">Existing Partners</h4>
        <table class="table table-bordered">
            <tr>
                <th>Logo</th>
                <th>Name</th>
                <th>Actions</th>
            </tr>
            @foreach($partners as $partner)
            <tr>
                <td>
                    <img src="{{ asset('uploads/partners/'.$partner->partner_logo) }}" width="80" alt="logo">
                </td>
                <td>{{ $partner->partner_name }}</td>
                <td>
                    <a href="{{ route('admin.partners.edit', $partner->id) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.partners.destroy', $partner->id) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this partner?')">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </table>
    </div>

    {{-- PARTNERS CONTENT SECTION --}}
    <div class="col-md-6">
        <h4>Add / Update Partners Content</h4>
        @php
            $content = $contents->first(); // Assuming only one content entry
        @endphp

        <form action="{{ $content ? route('admin.partners-content.update', $content->id) : route('admin.partners-content.store') }}" method="POST">
            @csrf
            @if($content)
                @method('PUT')
            @endif

            <div class="mb-3">
                <label class="form-label">Title</label>
                <input type="text" name="title" class="form-control" value="{{ $content->title ?? '' }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Tagline</label>
                <input type="text" name="tagline" class="form-control" value="{{ $content->tagline ?? '' }}">
            </div>
            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="5">{{ $content->description ?? '' }}</textarea>
            </div>
            <button class="btn btn-success">{{ $content ? 'Update Content' : 'Add Content' }}</button>
        </form>
    </div>
</div>
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
