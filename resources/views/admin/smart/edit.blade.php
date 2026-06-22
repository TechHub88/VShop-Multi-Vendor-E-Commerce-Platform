@extends('layouts.vertical', ['title' => 'Edit Smart Question'])

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Edit Smart Question</h4>

        <a href="{{ route('admin.smart.index') }}" class="btn btn-secondary">
            ← Back
        </a>
    </div>

    {{-- ERROR --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- FORM --}}
    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.smart.update', $q->id) }}">
                @csrf

                <div class="row">

                    <div class="col-md-12">
                        <label>Question</label>
                        <textarea name="question" class="form-control mb-3" required>{{ $q->question }}</textarea>
                    </div>

                    <div class="col-md-4">
                        <label>Type</label>
                        <select name="type" class="form-control mb-3">
                            <option value="general" {{ $q->type=='general'?'selected':'' }}>General</option>
                            <option value="product" {{ $q->type=='product'?'selected':'' }}>Product</option>
                            <option value="service" {{ $q->type=='service'?'selected':'' }}>Service</option>
                            <option value="marketing" {{ $q->type=='marketing'?'selected':'' }}>Marketing</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Status</label>
                        <select name="status" class="form-control mb-3">
                            <option value="1" {{ $q->status ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$q->status ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <button class="btn btn-primary">Update Question</button>
                    </div>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection