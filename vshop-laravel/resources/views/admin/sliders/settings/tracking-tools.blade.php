@extends('layouts.vertical', ['title' => 'Tracking Tools'])

@section('content')
<div class="container-fluid">
    <div class="card mb‑4">
        <div class="card-header d‑flex justify‑content‑between align‑items‑center">
            <h5 class="mb‑0">Website Tracking Tools</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <!-- Form to add a new tool -->
            <form action="{{ route('admin.settings.tracking-tools.store') }}" method="POST" class="row g‑3 mb‑4">
                @csrf
                <div class="col-md-6">
                    <label for="title" class="form-label">Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title') }}" placeholder="e.g., Google Analytics">
                </div>
                <div class="col-md-6">
                    <label for="code" class="form-label">Code / ID</label>
                    <input type="text" name="code" id="code" class="form-control" value="{{ old('code') }}" placeholder="e.g., G‑XXXXXXX">
                </div>
                <div class="col‑12">
                    <label for="description" class="form-label">Description</label>
                    <textarea name="description" id="description" class="form-control" rows="3" placeholder="Describe this tracking tool">{{ old('description') }}</textarea>
                </div>
                <div class="col‑12 text‑end mt‑3">
                    <button type="submit" class="btn btn-primary">Add Tracking Tool</button>
                </div>
            </form>

            <!-- Table listing tools -->
         

        </div>
    </div>
</div>

@section('scripts')
<script>
    $(document).ready(function(){
        $('#toolsTable').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true
        });
    });
</script>
@endsection

@endsection
