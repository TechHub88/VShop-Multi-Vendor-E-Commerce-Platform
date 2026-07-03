@extends('layouts.vertical', ['title' => 'Sitemap Generation'])

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h5>Sitemap Generation</h5>
            <p class="text-muted mb‑0">Generate and download sitemap.xml for your website.</p>
        </div>
        <div class="card-body">
            @if(session('success'))
              <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('admin.settings.sitemap.generate') }}" method="POST">
                @csrf
                <div class="mb‑3">
                  <label class="form‑label">Domain</label>
                  <input type="text" name="domain" class="form-control" value="{{ old('domain', config('app.url')) }}" placeholder="https://your‑domain.com">
                </div>
                <div class="mb‑3">
                  <label class="form‑label">Output File Name</label>
                  <input type="text" name="filename" class="form-control" value="sitemap.xml">
                </div>
                <div class="mb‑3">
                  <label class="form‑label">Include Models / Extra URLs (optional)</label>
                  <textarea name="extra_urls" class="form-control" rows="3" placeholder="Enter URLs each on new line"></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Generate Sitemap</button>
            </form>
        </div>
    </div>
</div>
@endsection
