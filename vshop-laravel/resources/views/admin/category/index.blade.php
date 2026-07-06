@extends('layouts.vertical', ['title' => auth()->user()->type == 'travel' ? 'Travel Categories' : 'Product Categories'])

@section('content')
<style>
.salesReportSearchArea{padding:15px 15px 15px;}
.container-fluid {
    padding-left: 15px;
    padding-right: 15px;
}
@media(max-width:575px){
.container-fluid {
    padding-left: 5px;
    padding-right: 5px;
}
</style>

@php
    // CORRECTED: Get 'type' column from user table
    $userType = auth()->user()->type; // Changed from user_type to type
    $isTravel = $userType == 'travel';
    $isProduct = $userType == 'product';
    $isAdmin = !$isTravel && !$isProduct;
    
    $title = $isTravel ? 'Travel Categories' : ($isProduct ? 'Product Categories' : 'All Categories');
    $singularTitle = $isTravel ? 'Travel Category' : ($isProduct ? 'Product Category' : 'Category');
    $createRoute = 'admin.categories.create';
    $editRoute = 'admin.categories.edit';
    $destroyRoute = 'admin.categories.destroy';
    $searchRoute = 'admin.categories.search';
    $productsRoute = 'admin.products.index';
    $toursRoute = 'admin.tours.index';
@endphp

<div class="container-fluid">

    <div class="pt-3 pb-1 d-flex align-items-center justify-content-between">
        <h4 class="fs-18 fw-semibold">{{ $title }}</h4>
        <a href="{{ route($createRoute) }}" class="btn btn-primary btn-sm">+ Add {{ $singularTitle }}</a>
    </div>

    <div class="card">
        <div class="card-body" style="padding:15px 15px;">
            
           
            
            <!-- Search, Filter and Export Controls - All in One Row -->
            <div class="filter-area mb-3a p-41a bg-light1 rounded salesReportSearchArea">
                <div class="d-flex flex-wrap flex-nowrap_1 gap-1 align-items-center salesReportSearchAreainner" >
                    <!-- Search keyword -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" id="keywordSearch" class="form-control" placeholder="Search {{ strtolower($title) }}...">
                        </div>
                    </div>
                    
                    <!-- Name Dropdown Filter -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-tag"></i></span>
                            <select id="nameFilter" class="form-select">
                                <option value="">All {{ $title }}</option>
                                @foreach($categories as $cat)
                                    @if($cat->name)
                                        <option value="{{ $cat->name }}">{{ $cat->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <!-- Category Type Filter (Only for Admin) -->
                    @if($isAdmin)
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                            <select id="typeFilter" class="form-select">
                                <option value="">All Types</option>
                                <option value="travel">Travel</option>
                                <option value="product">Product</option>
                            </select>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Travel-specific Filters -->
                    @if($isTravel || $isAdmin)
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-plane"></i></span>
                            <select id="tourTypeFilter" class="form-select">
                                <option value="">All Tour Types</option>
                                <option value="domestic">Domestic</option>
                                <option value="international">International</option>
                            </select>
                        </div>
                    </div>
                    @endif
                    
                    <!-- Status Filter -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            <select id="statusFilter" class="form-select">
                                <option value="">All Status</option>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Featured Filter -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-star"></i></span>
                            <select id="featuredFilter" class="form-select">
                                <option value="">Featured</option>
                                <option value="featured">Featured</option>
                                <option value="not_featured">Not Featured</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sort By -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text"><i class="fas fa-sort"></i></span>
                            <select id="sortFilter" class="form-select">
                                <option value="newest">Newest First</option>
                                <option value="oldest">Oldest First</option>
                                <option value="name_asc">Name (A-Z)</option>
                                <option value="name_desc">Name (Z-A)</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Search Button -->
                    <div class="flex-shrink-0 salesReportSearchBox">
                        <button type="button" id="applySearch" class="btn btn-success btn-sm">
                            <i class="fas fa-search me-1"></i> Search
                        </button>
                    </div>
                    
                    <!-- Divider -->
                    <div class="flex-shrink-0 salesReportSearchBox" style="width: 1px; height: 24px; background-color: #dee2e6;"></div>
                    
                    <!-- Select Fields Dropdown -->
                    <div class="flex-shrink-0 dropdown-container">
                        <button type="button" id="fieldDropdownBtn" class="btn btn-outline-primary btn-sm fieldDropdownBtn" 
                                style="background-color:#fff;border-color: #6f42c1; color: #6f42c1;">
                            <i class="fas fa-columns me-1"></i> Fields
                            <span id="selectedCount" class="badge bg-primary ms-1">
                                {{ $isAdmin ? '10' : ($isTravel ? '9' : '7') }}
                            </span>
                            <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                        <div id="fieldDropdown" class="dropdown-menu">
                            <div class="dropdown-title">Select Fields to Export</div>
                            <div class="dropdown-list">
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="sl" data-label="SL" checked>
                                    SL
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="name" data-label="{{ $singularTitle }} Name" checked>
                                    {{ $singularTitle }} Name
                                </label>
                                
                                @if($isAdmin)
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="type" data-label="Type" checked>
                                    Type
                                </label>
                                @endif
                                
                                @if($isTravel || $isAdmin)
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="tour_type" data-label="Tour Type" {{ $isTravel ? 'checked' : '' }}>
                                    Tour Type
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="ideal_for" data-label="Ideal For" {{ $isTravel ? 'checked' : '' }}>
                                    Ideal For
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="duration" data-label="Duration" {{ $isTravel ? 'checked' : '' }}>
                                    Duration
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="best_season" data-label="Best Season" {{ $isTravel ? 'checked' : '' }}>
                                    Best Season
                                </label>
                                @endif
                                
                                @if($isProduct || $isAdmin)
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="show_in_store" data-label="Show in Store" {{ $isProduct ? 'checked' : '' }}>
                                    Show in Store
                                </label>
                                @endif
                                
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="featured" data-label="Featured" checked>
                                    Featured
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="status" data-label="Status" checked>
                                    Status
                                </label>
                                <label class="dropdown-item">
                                    <input type="checkbox" class="field-checkbox me-2" value="created_at" data-label="Created At" checked>
                                    Created At
                                </label>
                            </div>
                            <div class="dropdown-footer">
                                <button type="button" id="selectAllFields" class="btn btn-sm btn-link p-0 me-2">Select All</button>
                                <button type="button" id="deselectAllFields" class="btn btn-sm btn-link p-0">Deselect All</button>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Export CSV Button -->
                    <div class="flex-shrink-0">
                        <button type="button" id="exportCsvBtn" class="btn btn-outline-primary btn-sm exportCsvBtn" 
                                style="background-color:#fff;border-color: #6f42c1; color: #6f42c1;">
                            <i class="fas fa-file-csv me-1"></i> Export CSV
                        </button>
                    </div>
                </div>
            </div>

            <!-- Loading Indicator -->
            <div id="loadingIndicator" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading {{ strtolower($title) }}...</p>
            </div>

            <!-- Table -->
            <div class="table-responsive">
                <table id="categoriesTable" class="table table-bordered table-striped align-middle">
                    <thead>
                        <tr>
                            <th data-column="sl">#</th>
                            <th data-column="name">{{ $singularTitle }} Name</th>
                            
                            @if($isAdmin)
                            <th data-column="type">Type</th>
                            @endif
                            
                            @if($isTravel || $isAdmin)
                            <th data-column="tour_type">Tour Type</th>
                            <th data-column="ideal_for">Ideal For</th>
                            <th data-column="duration">Duration</th>
                            <th data-column="best_season">Best Season</th>
                            @endif
                            
                            @if($isProduct || $isAdmin)
                            <th data-column="show_in_store">Show in Store</th>
                            @endif
                            
                            <th data-column="featured">Featured</th>
                            <th data-column="status">Status</th>
                            <th data-column="created_at">Created At</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>

                    <tbody id="categoriesTableBody">
                        @forelse($categories as $cat)
                        <tr>
                            <td data-column="sl">{{ $loop->iteration }}</td>
                            <td data-column="name">{{ $cat->name ?? '-' }}</td>
                            
                            @if($isAdmin)
                            <td data-column="type">
                                <span class="badge {{ $cat->type === 'travel' ? 'bg-info' : 'bg-warning' }}">
                                    {{ ucfirst($cat->type ?? '-') }}
                                </span>
                            </td>
                            @endif
                            
                            @if($isTravel || $isAdmin)
                            <td data-column="tour_type">
                                @if($cat->tour_type)
                                <span class="badge {{ $cat->tour_type === 'international' ? 'bg-info' : 'bg-success' }}">
                                    {{ ucfirst($cat->tour_type) }}
                                </span>
                                @else
                                —
                                @endif
                            </td>
                            <td data-column="ideal_for">{{ $cat->ideal_for ?? '-' }}</td>
                            <td data-column="duration">{{ $cat->duration_range ?? '-' }}</td>
                            <td data-column="best_season">{{ $cat->best_season ?? '-' }}</td>
                            @endif
                            
                            @if($isProduct || $isAdmin)
                            <td data-column="show_in_store">
                                @if($cat->type == 'product' || $isAdmin)
                                <span class="badge {{ $cat->show_in_store ? 'bg-success' : 'bg-secondary' }}">
                                    {{ $cat->show_in_store ? 'Yes' : 'No' }}
                                </span>
                                @else
                                —
                                @endif
                            </td>
                            @endif
                            
                            <td data-column="featured">
                                @if($cat->featured)
                                    <span class="badge bg-success">Yes</span>
                                @else
                                    <span class="badge bg-secondary">No</span>
                                @endif
                            </td>
                            
                            <td data-column="status">
                                @if($cat->status)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-danger">Inactive</span>
                                @endif
                            </td>
                            
                            <td data-column="created_at">
                                {{ $cat->created_at ? $cat->created_at->format('d M, Y') : '-' }}
                            </td>
                            
                            <td>
                                <!-- Edit Button -->
                                <a href="{{ route($editRoute, $cat->id) }}" class="btn btn-sm btn-warning" title="Edit {{ $singularTitle }}">
                                    <i class="fas fa-edit"></i>
                                </a>

                             

                                <!-- Delete Button -->
                                <form action="{{ route($destroyRoute, $cat->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="
                                {{ ($isAdmin ? 11 : ($isTravel ? 10 : 8)) }}
                            " class="text-center text-muted py-4">
                                <i class="fas {{ $isTravel ? 'fa-plane' : 'fa-tag' }} fa-2x mb-2"></i>
                                <p>No {{ strtolower($title) }} found</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

</div>

<!-- ... rest of your CSS and JavaScript code remains the same ... -->
@endsection
