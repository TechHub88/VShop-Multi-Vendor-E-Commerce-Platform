@extends('layouts.vertical', ['title' => 'Attributes'])

@section('content')

<h3>Add About Section</h3>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<form method="POST" action="{{ route('admin.about.store') }}" enctype="multipart/form-data">
@csrf

<div class="row">
    <div class="col-md-6">
        <label>About 1</label>
        <input type="text" name="about1" class="form-control">
    </div>

    <div class="col-md-6">
        <label>About 2</label>
        <input type="text" name="about2" class="form-control">
    </div>

    <div class="col-md-6">
        <label>About 3</label>
        <input type="text" name="about3" class="form-control">
    </div>

    <div class="col-md-6">
        <label>About 4</label>
        <input type="text" name="about4" class="form-control">
    </div>

    <div class="col-md-6">
        <label>About 5</label>
        <input type="text" name="about5" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 6</label>
        <input type="text" name="about6" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 7</label>
        <input type="text" name="about7" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 8</label>
        <input type="text" name="about8" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 9</label>
        <input type="text" name="about9" class="form-control">
    </div>
     <div class="col-md-6">
        <label>About 10</label>
        <input type="text" name="about10" class="form-control">
    </div>


    <div class="col-12">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="5"></textarea>
    </div>

    <div class="col-12 mt-3">
        <label>Background Image</label>
        <input type="file" name="bgimage" class="form-control">
    </div>
</div>

<button class="btn btn-success mt-3">Save</button>
</form>

@endsection
