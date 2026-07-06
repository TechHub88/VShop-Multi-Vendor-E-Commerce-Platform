@extends('layouts.vertical', ['title' => 'Add Smart Question'])

@section('content')

<style>
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

<div class="container-fluid pt-2">

<h4>Add Smart Question</h4>

<form method="POST" action="{{ route('admin.smart.store') }}">
@csrf

<textarea name="question" class="form-control mb-3" placeholder="Enter Question"></textarea>

<select name="type" class="form-control mb-3">
    <option value="general">General</option>
    <option value="product">Product</option>
    <option value="service">Service</option>
    <option value="marketing">Marketing</option>
</select>

<select name="status" class="form-control mb-3">
    <option value="1">Active</option>
    <option value="0">Inactive</option>
</select>

<button class="btn btn-success">Save</button>

</form>

</div>

@endsection
