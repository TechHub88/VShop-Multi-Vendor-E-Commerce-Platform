@extends('layouts.vertical', ['title' => 'Attributes'])

@section('content')
<h3>Update About Section</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.about.update', $about->id) }}" enctype="multipart/form-data">
@csrf
@method('PUT') <!-- if using update -->


<div class="row">
    <div class="col-md-6">
        <label>About 1</label>
        <input type="text" name="about1" value="{{ $about->about1 }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label>About 2</label>
        <input type="text" name="about2" value="{{ $about->about2 }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label>About 3</label>
        <input type="text" name="about3" value="{{ $about->about3 }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label>About 4</label>
        <input type="text" name="about4" value="{{ $about->about4 }}" class="form-control">
    </div>

    <div class="col-md-6">
        <label>About 5</label>
        <input type="text" name="about5" value="{{ $about->about5 }}" class="form-control">
    </div>

     <div class="col-md-6">
        <label>About 6</label>
        <input type="text" name="about6" value="{{ $about->about6 }}" class="form-control">
    </div>

     <div class="col-md-6">
        <label>About 6</label>
        <input type="text" name="about6" value="{{ $about->about6 }}" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 7</label>
        <input type="text" name="about7" value="{{ $about->about7 }}" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 8</label>
        <input type="text" name="about8" value="{{ $about->about8 }}" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 9</label>
        <input type="text" name="about9" value="{{ $about->about9}}" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 10 </label>
        <input type="text" name="about10" value="{{ $about->about10 }}" class="form-control">
    </div>

    <div class="col-12">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="5">{{ $about->description }}</textarea>
    </div>

    <div class="col-12 mt-3">
        <label>Background Image</label>
        <input type="file" name="bgimage" class="form-control">
        @if($about->bgimage)
            <img src="{{ asset('uploads/about/'.$about->bgimage) }}" width="200" class="mt-2">
        @endif
    </div>
</div>

<button class="btn btn-success mt-3">Save Changes</button>
</form>
@endsection
