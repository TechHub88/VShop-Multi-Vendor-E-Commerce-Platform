@extends('vshop.seller.main')

<style>
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        font-size: 4px;
        width: 35px;
        height: 38px;
    }
    .top-back-heading-sec {
        align-items: center;
        gap: 10px;
    }
    .top-back-heading-sec h5 {
        color: #0f172a !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="mb-4 d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <div>
            <h5 class="mb-0">Create Grievance Ticket</h5>
            <small class="text-muted">
                Raise a support request for buyer or platform-related issues
            </small>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('seller.grievance.store') }}">
                @csrf

                <div class="row g-3">

                    {{-- TYPE --}}
                    <div class="col-md-6">
                        <label class="form-label">Ticket Type</label>
                        <select name="type" class="form-control">
                            <option value="buyer">Seller ↔ Buyer</option>
                            <option value="platform">Seller ↔ Platform</option>
                        </select>
                    </div>

                    {{-- PRIORITY --}}
                    <div class="col-md-6">
                        <label class="form-label">Priority</label>
                        <select name="priority" class="form-control">
                            <option value="low">Low</option>
                            <option value="medium">Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>

                    {{-- 🔥 ASSIGN TO (NEW FIELD) --}}
                    <div class="col-md-6">
                        <label class="form-label">Assign To</label>
                        <select name="assigned_to" class="form-control">
                            <option value="">Select Team</option>
                            <option value="support team">Support Team</option>
                            <option value="account team">Account Team</option>
                            <option value="logistic team">Logistic Team</option>
                            <option value="operation team">Operation Team</option>
                        </select>
                        @error('assigned_to')
                            <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- SUBJECT --}}
                    <div class="col-md-12">
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-control"
                               placeholder="Enter ticket subject">
                    </div>

                    {{-- MESSAGE --}}
                    <div class="col-md-12">
                        <label class="form-label">Description</label>
                        <textarea name="message" rows="5" class="form-control"
                                  placeholder="Describe your issue in detail..."></textarea>
                    </div>

                </div>

                {{-- ACTION --}}
                <div class="d-flex justify-content-end mt-4">

                    <a href="{{ route('seller.grievance.list') }}" class="btn btn-light me-2">
                        Cancel
                    </a>

                    <button class="btn btn-success px-4">
                        <i class="fa fa-paper-plane"></i> Submit Ticket
                    </button>

                </div>

            </form>

        </div>
    </div>

</div>

@endsection
