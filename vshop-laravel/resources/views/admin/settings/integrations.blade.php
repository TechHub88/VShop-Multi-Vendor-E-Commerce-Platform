@extends('layouts.vertical', ['title' => 'Integrations Settings'])
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h4>Payment Gateway & Courier Integrations</h4>
        </div>

        <form action="{{ route('admin.settings.integrations') }}" method="POST">
            @csrf
            <div class="card-body">

                <h6 class="mb-3">Payment Gateway API Keys</h6>

                <div class="mb-3">
                    <label>Razorpay Key</label>
                    <input type="text" class="form-control" name="razorpay_key">
                </div>

                <div class="mb-3">
                    <label>Razorpay Secret</label>
                    <input type="text" class="form-control" name="razorpay_secret">
                </div>

                <hr>

                <h6 class="mb-3">Courier Service API Keys</h6>

                <div class="mb-3">
                    <label>ShipRocket API Key</label>
                    <input type="text" class="form-control" name="shiprocket_key">
                </div>

            </div>
            <div class="card-footer text-end">
                <button class="btn btn-primary">Save Changes</button>
            </div>
        </form>

    </div>
</div>
@endsection
