@extends('layouts.vertical', ['title' => 'Generate QR Code'])

@section('content')

@section('content')

<style>
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
</style>
<div class="container-fluid pt-3">
    <div class="card">
        <div class="card-header"><h4>Generate QR Code</h4></div>

        <form action="{{ route('admin.settings.qr.generate') }}" method="POST">
            @csrf
            <div class="card-body">
                <div class="mb-3">
                    <label>Enter Text / URL</label>
                    <input type="text" name="qr_text" class="form-control">
                </div>
            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary">Generate</button>
            </div>
        </form>

    </div>
</div>
@endsection
