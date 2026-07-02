@extends('layouts.vertical', ['title' => 'Edit Advertisement])

@section('content')

<div class="container-fluid">

    <h4 class="mb-3">Edit Advertisement</h4>

    <div class="card">
        <div class="card-body">

            <form action="{{ route('admin.banners.update', $banner->id) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <!-- TITLE -->
                    <div class="col-md-6 mb-3">
                        <label>Title</label>
                        <input type="text" name="title" value="{{ $banner->title }}" class="form-control">
                    </div>

                    <!-- TYPE -->
                    <div class="col-md-3 mb-3">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="banner" {{ $banner->type=='banner'?'selected':'' }}>Banner</option>
                            <option value="campaign" {{ $banner->type=='campaign'?'selected':'' }}>Campaign</option>
                            <option value="feature" {{ $banner->type=='feature'?'selected':'' }}>Feature</option>
                        </select>
                    </div>

                    <!-- PLATFORM -->
                    <div class="col-md-3 mb-3">
                        <label>Platform</label>
                        <select name="platform" class="form-control">
                            <option value="vshop" {{ $banner->platform=='vshop'?'selected':'' }}>Vshop</option>
                            <option value="vtravel" {{ $banner->platform=='vtravel'?'selected':'' }}>Vtravel</option>
                            <option value="both" {{ $banner->platform=='both'?'selected':'' }}>Both</option>
                        </select>
                    </div>

                    <!-- CATEGORY -->
                    <div class="col-md-6 mb-3">
                        <label>Category</label>
                        <select name="category_id" class="form-control">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}"
                                    {{ $banner->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- DATES -->
                    <div class="col-md-3 mb-3">
                        <label>Start Date</label>
                        <input type="datetime-local" name="start_date"
                               value="{{ date('Y-m-d\TH:i', strtotime($banner->start_date)) }}"
                               class="form-control">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>End Date</label>
                        <input type="datetime-local" name="end_date"
                               value="{{ date('Y-m-d\TH:i', strtotime($banner->end_date)) }}"
                               class="form-control">
                    </div>

                    <!-- IMAGE -->
                    <div class="col-md-6 mb-3">
                        <label>Change Banner</label>
                        <input type="file" name="banner" class="form-control">
                    </div>

                    <!-- CURRENT -->
                    <div class="col-md-6 mb-3">
                        <label>Current Image</label><br>
                        <img src="{{ asset($banner->banner) }}" height="80">
                    </div>

                    <!-- DESCRIPTION -->
                    <div class="col-md-12 mb-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control">{{ $banner->description }}</textarea>
                    </div>

                </div>

                <button class="btn btn-success">Update</button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-secondary">Back</a>

            </form>

        </div>
    </div>

</div>

@endsection