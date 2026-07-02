@extends('layouts.vertical', ['title' => 'Create Advertisement'])

@section('content')

<div class="container-fluid">

    <h4 class="mb-3">Create Advertisement</h4>

    <div class="card">
        <div class="card-body">

            <form method="POST" action="{{ route('admin.ads.store') }}" enctype="multipart/form-data">
                @csrf

                <div class="row">

                    <div class="col-md-4">
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>Type</label>
                        <select name="type" class="form-control">
                            <option value="banner">Banner</option>
                            <option value="video">Video</option>
                            <option value="campaign">Campaign</option>
                            <option value="featured">Featured</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label>Target</label>
                        <select name="target_type" id="target_type" class="form-control">
                            <option value="product">Vshop</option>
                            <option value="service">Vtravel</option>
                            <option value="both">Both</option>
                        </select>
                    </div>

                    {{-- PRODUCT (COMMON FOR BOTH) --}}
                    <div class="col-md-4 mt-3 product-box">
                        <label>Select Item</label>
                        <select name="product_id" class="form-control">
                            <option value="">Select</option>

                            @foreach($products as $p)
                                <option value="{{ $p->id }}">
                                    {{ $p->name }} ({{ ucfirst($p->type ?? 'product') }})
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Images</label>
                        <input type="file" name="images[]" multiple class="form-control">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Videos</label>
                        <input type="file" name="videos[]" multiple class="form-control">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>Start Date</label>
                        <input type="datetime-local" name="start_date" class="form-control">
                    </div>

                    <div class="col-md-4 mt-3">
                        <label>End Date</label>
                        <input type="datetime-local" name="end_date" class="form-control">
                    </div>

                    <div class="col-md-12 mt-3">
                        <label>Description</label>
                        <textarea name="description" class="form-control"></textarea>
                    </div>

                    <div class="col-md-12 mt-3">
                        <button class="btn btn-success">Create Advertisement</button>
                    </div>

                </div>

            </form>

        </div>
    </div>

</div>

{{-- SCRIPT --}}
<script>
document.getElementById('target_type').addEventListener('change', function () {
    let val = this.value;

    let options = document.querySelectorAll('select[name="product_id"] option');

    options.forEach(opt => {
        if(opt.value === '') return;

        let text = opt.text.toLowerCase();

        if(val === 'both'){
            opt.style.display = 'block';
        } else {
            opt.style.display = text.includes(val) ? 'block' : 'none';
        }
    });
});
</script>

@endsection