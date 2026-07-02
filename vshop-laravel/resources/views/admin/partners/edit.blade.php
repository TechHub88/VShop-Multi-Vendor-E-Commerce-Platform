@extends('layouts.vertical', ['title' => 'Edit Partner'])

@section('content')
<h3>Edit Partner</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form action="{{ route('admin.partners.update', $partner->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="mb-3">
        <label class="form-label">Partner Logo</label>
        <input type="file" name="partner_logo" class="form-control" id="partnerLogoInput">
        @if($partner->partner_logo)
            <img id="logoPreview" src="{{ asset('uploads/partners/'.$partner->partner_logo) }}" class="mt-2" style="width:120px;height:80px;object-fit:cover;">
        @endif
        @error('partner_logo')
            <span class="text-danger">{{ $message }}</span>
        @enderror
    </div>

    <div class="mb-3">
        <label class="form-label">Partner Name</label>
        <input type="text" name="partner_name" class="form-control" value="{{ $partner->partner_name }}">
    </div>

    <button class="btn btn-success">Update Partner</button>
</form>
@endsection

@section('scripts')
<script>
    const partnerLogoInput = document.getElementById('partnerLogoInput');
    const logoPreview = document.getElementById('logoPreview');

    partnerLogoInput.addEventListener('change', function() {
        const file = this.files[0];
        if(file){
            const reader = new FileReader();
            reader.onload = function(e){
                logoPreview.src = e.target.result;
                logoPreview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        }
    });
</script>
@endsection
