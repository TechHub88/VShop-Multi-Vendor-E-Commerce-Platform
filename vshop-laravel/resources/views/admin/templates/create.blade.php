@extends('layouts.vertical', ['title' => 'Create Website Template'])

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

<div class="container-fluid">

    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-flex justify-content-between align-items-center">
                <h4 class="page-title">Create Website Template</h4>

                <a href="{{ route('admin.website.templates.index') }}"
                   class="btn btn-light">
                    <i class="mdi mdi-arrow-left"></i> Back
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="row">
        <div class="col-lg-8">

            <div class="card">
                <div class="card-body">

                    <form method="POST"
                          action="{{ route('admin.website.templates.store') }}"
                          enctype="multipart/form-data">

                        @csrf

                        <!-- Template Name -->
                        <div class="mb-3">
                            <label class="form-label">Template Name *</label>
                            <input type="text"
                                   name="name"
                                   class="form-control"
                                   placeholder="Ex: Wellness Modern Template"
                                   required>
                        </div>

                        <!-- Application Type -->
                        <div class="mb-3">
                            <label class="form-label">Application Type *</label>

                            <select name="application_type"
                                    class="form-select"
                                    required>
                                <option value="">Select Application</option>
                                <option value="travel">Travel</option>
                                <option value="wellness">Wellness</option>
                                <option value="education">Education</option>
                                <option value="marketplace">Marketplace</option>
                            </select>
                        </div>

                        <!-- Preview Image -->
                        <div class="mb-3">
                            <label class="form-label">Preview Image *</label>

                            <input type="file"
                                   name="preview_image"
                                   class="form-control"
                                   accept="image/*"
                                   required>

                            <small class="text-muted">
                                Recommended Size: 1200x800 px
                            </small>
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label">Template Description</label>

                            <textarea name="description"
                                      class="form-control"
                                      rows="4"
                                      placeholder="Short description about template layout..."></textarea>
                        </div>

                        <!-- Status -->
                        <div class="mb-3">
                            <label class="form-label">Status</label>

                            <select name="status" class="form-select">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>

                        <!-- Submit -->
                        <div class="text-end">
                            <button type="submit"
                                    class="btn btn-primary">
                                <i class="mdi mdi-content-save"></i>
                                Save Template
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

@endsection
