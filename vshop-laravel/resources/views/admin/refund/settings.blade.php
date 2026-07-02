@extends('layouts.vertical', ['title' => 'Refund Settings'])

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center py-3">
        <h4 class="fw-semibold fs-20">Refund Settings</h4>
    </div>

    <div class="card mt-2">
        <div class="card-body">
            <form action="{{ route('admin.refund.settings.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="refund_days" class="form-label">Refund Request Days Limit</label>
                    <input type="number" name="refund_days" id="refund_days" class="form-control"
                        value="{{ $settings->refund_days ?? 7 }}" required>
                </div>

                <div class="mb-3">
                    <label for="auto_approve" class="form-label">Auto Approve Refund</label>
                    <select name="auto_approve" id="auto_approve" class="form-select">
                        <option value="1" {{ ($settings->auto_approve ?? 0) ? 'selected' : '' }}>Yes</option>
                        <option value="0" {{ !($settings->auto_approve ?? 0) ? 'selected' : '' }}>No</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Update Settings</button>
            </form>
        </div>
    </div>

</div>
@endsection
