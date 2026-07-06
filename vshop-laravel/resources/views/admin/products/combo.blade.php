@extends('layouts.vertical', ['title' => 'Create Combo'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Left Side: Search and Selected Products -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Create Combo</h4>
                </div>
                <div class="card-body">
                    <!-- Search Box -->
                    <div class="mb-3">
                        <label for="productSearch" class="form-label">Search Products</label>
                        <input type="text" id="productSearch" class="form-control" 
                               placeholder="Type product name or SKU (min 2 characters)..." 
                               autocomplete="off">
                    </div>

                    <!-- Suggestions Box -->
                    <div id="suggestions" class="border rounded mb-3" 
                         style="display:none; max-height:300px; overflow-y:auto; position:absolute; z-index:1000; background:white; width:calc(100% - 2rem);">
                    </div>

                    <!-- Selected Products -->
                    <div class="mt-4">
                        <h6>Selected Products for Combo</h6>
                        <div id="selectedProducts" class="mt-2">
                            <div class="alert alert-info" id="noProductsAlert">
                                No products selected yet. Search and add products above.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Main Product Preview and Existing Combos -->
        <div class="col-lg-6">
            <!-- Main Product Card -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Main Product</h5>
                </div>
                <div class="card-body">
                    <!-- Product Info -->
                    <div class="d-flex align-items-start gap-3 mb-4">
                        <!-- Thumbnail -->
                        <img src="{{ $product->thumbnail ? asset($product->thumbnail) : 'https://via.placeholder.com/150x150?text=No+Image' }}" 
                             class="img-fluid rounded"
                             style="width:120px; height:120px; object-fit:cover;" 
                             alt="{{ $product->name }}"
                             onerror="this.src='https://via.placeholder.com/150x150?text=No+Image'">

                        <!-- Product Details -->
                        <div class="flex-grow-1">
                            <h4 class="mb-2">{{ $product->name }}</h4>
                            @if($product->sku)
                                <p class="text-muted mb-1">SKU: {{ $product->sku }}</p>
                            @endif
                            @if($product->price)
                                <p class="mb-1"><strong>Price: ₹{{ number_format($product->price, 2) }}</strong></p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <!-- Existing Combos Card -->
        <!-- Existing Combos Card -->
<div class="card mt-3">
    <div class="card-header">
        <h5 class="card-title mb-0">Existing Combos for This Product</h5>
    </div>
    <div class="card-body">
        <div id="existingCombos">
            @if($existingCombos && $existingCombos->count() > 0)
                @foreach($existingCombos as $combo)
                    <div class="combo-item card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-start gap-3">
                                <!-- Combo Thumbnail -->
                                <div class="flex-shrink-0">
                                    <img src="{{ $product->thumbnail ? asset($product->thumbnail) : 'https://via.placeholder.com/80x80?text=No+Image' }}" 
                                         class="rounded"
                                         style="width:80px; height:80px; object-fit:cover;"
                                         alt="{{ $combo->name }}"
                                         onerror="this.src='https://via.placeholder.com/80x80?text=No+Image'">
                                </div>
                                
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $combo->name }}</h6>
                                            <p class="text-muted mb-1 small">Main Product: {{ $product->name }}</p>
                                            
                                            <!-- Products in Combo with Images -->
                                            <div class="mt-3">
                                                <small class="text-muted fw-semibold">Products included:</small>
                                                <div class="d-flex flex-wrap gap-2 mt-2">
                                                    @foreach($combo->items as $item)
                                                        @if($item->comboProduct)
                                                            <div class="product-badge d-flex align-items-center bg-light rounded p-2 border">
                                                                <img src="{{ $item->comboProduct->thumbnail ? asset($item->comboProduct->thumbnail) : 'https://via.placeholder.com/30x30?text=No+Img' }}" 
                                                                     class="rounded me-2"
                                                                     style="width:30px; height:30px; object-fit:cover;"
                                                                     alt="{{ $item->comboProduct->name }}"
                                                                     onerror="this.src='https://via.placeholder.com/30x30?text=No+Img'">
                                                                <div>
                                                                    <small class="text-dark fw-medium">{{ $item->comboProduct->name }}</small>
                                                                    <br>
                                                                    <small class="text-muted">₹{{ number_format($item->comboProduct->price ?? 0, 2) }}</small>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="flex-shrink-0 text-end">
                                            <span class="badge bg-success mb-2">
                                                Active
                                            </span>
                                            <div class="mt-1">
                                                <small class="text-muted d-block">{{ $combo->created_at->format('M d, Y') }}</small>
                                                <small class="text-muted">{{ $combo->created_at->format('h:i A') }}</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Combo Actions -->
                                    <div class="mt-3 pt-2 border-top">
                                        <div class="d-flex gap-2">
                                            <!-- Simple Delete Link -->
                                            @php
                                                $firstItemId = $combo->items->first()->id ?? null;
                                                $comboTimestamp = $combo->created_at->format('Y-m-d H:i:s');
                                            @endphp
                                            <form action="{{ route('admin.combos.destroy', $firstItemId) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <input type="hidden" name="combo_timestamp" value="{{ $comboTimestamp }}">
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="return confirm('Are you sure you want to delete this combo? This action cannot be undone.')">
                                                    <i class="ri-delete-bin-line"></i> Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="alert alert-info mb-0 text-center">
                    <div class="py-3">
                        <i class="ri-inbox-line display-4 text-muted mb-3"></i>
                        <h6 class="text-muted">No combos created yet</h6>
                        <p class="mb-0 small">Create your first combo by selecting products above and clicking "Save Combo"</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

           
          

            <!-- Combo Actions -->
            <div class="card mt-3">
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary" id="saveCombo">
                            Save Combo
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            Cancel
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<!-- Toastr JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    // Configure Toastr
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": true,
        "progressBar": true,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "5000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    };
    
$(document).ready(function() {
    const searchInput = $('#productSearch');
    const suggestionsBox = $('#suggestions');
    const selectedProducts = $('#selectedProducts');
    const noProductsAlert = $('#noProductsAlert');
    const existingCombos = $('#existingCombos');
    
    let searchTimer;

    // Search functionality with debounce
    searchInput.on('keyup', function() {
        const query = $(this).val().trim();
        clearTimeout(searchTimer);

        if (query.length < 2) {
            suggestionsBox.hide().empty();
            return;
        }

        searchTimer = setTimeout(() => {
            performSearch(query);
        }, 300);
    });

    function performSearch(query) {
        console.log('Searching for:', query);
        
        // Show loading
        suggestionsBox.html(`
            <div class="p-3 text-center">
                <div class="spinner-border spinner-border-sm me-2"></div>
                Searching for "${query}"...
            </div>
        `).show();

        $.ajax({
            url: '{{ route("admin.products.search") }}',
            type: 'GET',
            data: { query: query },
            success: function(response) {
                console.log('Search response:', response);
                
                if (!response || response.length === 0) {
                    suggestionsBox.html(`
                        <div class="p-3 text-center text-muted">
                            No products found for "<strong>${query}</strong>"
                        </div>
                    `).show();
                    return;
                }

                let html = '';
                response.forEach(function(product) {
                    const safeName = product.name.replace(/'/g, "&#39;").replace(/"/g, "&quot;");
                    html += `
                        <div class="suggestion-item p-3 border-bottom" 
                             onclick="selectProduct(${product.id}, '${safeName}', '${product.thumbnail}')"
                             style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <img src="${product.thumbnail}" 
                                     class="rounded me-3"
                                     style="width:50px; height:50px; object-fit:cover;"
                                     onerror="this.src='https://via.placeholder.com/50'">
                                <div class="flex-grow-1">
                                    <div class="fw-semibold">${product.name}</div>
                                    <small class="text-muted">SKU: ${product.sku}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });

                suggestionsBox.html(html).show();
            },
            error: function(xhr, status, error) {
                console.error('Search error:', error);
                suggestionsBox.html(`
                    <div class="p-3 text-center text-danger">
                        <i class="ri-error-warning-line me-2"></i>
                        Search failed. Please try again.
                    </div>
                `).show();
            }
        });
    }

    // Close suggestions when clicking outside
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#productSearch, #suggestions').length) {
            suggestionsBox.hide();
        }
    });

    // Save combo functionality
    $('#saveCombo').on('click', function() {
        const selectedIds = [];
        
        // Get all selected product IDs
        $('[name="combo_products[]"]').each(function() {
            selectedIds.push($(this).val());
        });

        console.log('Selected product IDs:', selectedIds);
        console.log('Main product ID:', {{ $product->id }});

        const mainProductId = {{ $product->id }};
        
        if (selectedIds.length === 0) {
            toastr.error('Please add at least one product to the combo.', 'No Products Selected', {
                timeOut: 5000,
                positionClass: 'toast-top-right'
            });
            return;
        }

        // Client-side validation: Check if main product is in the list
        if (selectedIds.includes(mainProductId.toString())) {
            toastr.error('You cannot add the main product as a combo product of itself.', 'Invalid Selection', {
                timeOut: 5000,
                positionClass: 'toast-top-right'
            });
            return;
        }

        // Client-side validation: Check for duplicates
        const uniqueIds = [...new Set(selectedIds)];
        if (selectedIds.length !== uniqueIds.length) {
            toastr.error('Duplicate products detected. Each product can only be added once to the combo.', 'Duplicate Products', {
                timeOut: 5000,
                positionClass: 'toast-top-right'
            });
            return;
        }

        // Show loading
        const btn = $(this);
        btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span> Saving...');

        // Use the correct route URL
        const url = '{{ route("admin.products.save-combo", ["id" => $product->id]) }}';
        console.log('Saving to URL:', url);

        // Send AJAX request
        $.ajax({
            url: url,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}',
                combo_products: selectedIds
            },
            success: function(response) {
                console.log('Success response:', response);
                if (response.success) {
                    // Show success message
                    toastr.success(response.message || 'Combo created successfully!', 'Success', {
                        timeOut: 3000,
                        positionClass: 'toast-top-right'
                    });
                    
                    // Clear selected products
                    clearSelectedProducts();
                    
                    // Reload the page to show the new combo after a short delay
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    // Show error message from server
                    toastr.error(response.message || 'Failed to create combo.', 'Error', {
                        timeOut: 5000,
                        positionClass: 'toast-top-right'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', xhr.status, error);
                
                let errorMessage = 'Failed to save combo.';
                
                if (xhr.status === 422) {
                    // Validation error
                    const response = xhr.responseJSON;
                    errorMessage = response.message || 'Validation error. Please check your input.';
                } else if (xhr.status === 404) {
                    errorMessage = 'Route not found. Please check if the route is defined.';
                } else if (xhr.status === 419) {
                    errorMessage = 'CSRF token mismatch. Please refresh the page.';
                } else if (xhr.status === 500) {
                    const response = xhr.responseJSON;
                    errorMessage = response.message || 'Server error. Please try again.';
                }
                
                toastr.error(errorMessage, 'Error', {
                    timeOut: 5000,
                    positionClass: 'toast-top-right'
                });
            },
            complete: function() {
                btn.prop('disabled', false).html('Save Combo');
            }
        });
    });


    // Function to clear selected products after saving
    function clearSelectedProducts() {
        $('[name="combo_products[]"]').closest('.card').remove();
        $('#noProductsAlert').show();
    }
});

// Function to select product from suggestions
function selectProduct(id, name, thumbnail) {
    const mainProductId = {{ $product->id }};
    
    // Hide suggestions and clear search
    $('#suggestions').hide().empty();
    $('#productSearch').val('');

    // Validation: Check if trying to add main product as combo product
    if (id == mainProductId) {
        toastr.error('You cannot add the main product as a combo product of itself.', 'Invalid Selection', {
            timeOut: 5000,
            positionClass: 'toast-top-right'
        });
        return;
    }

    // Validation: Check if product already selected
    if ($(`#selected-product-${id}`).length > 0) {
        toastr.warning('This product is already in the combo. Each product can only be added once.', 'Duplicate Product', {
            timeOut: 5000,
            positionClass: 'toast-top-right'
        });
        return;
    }

    // Hide no products alert
    $('#noProductsAlert').hide();

    // Create selected product card
    const productHtml = `
        <div class="card mb-2" id="selected-product-${id}">
            <div class="card-body py-2">
                <div class="d-flex align-items-center">
                    <img src="${thumbnail}" 
                         class="rounded me-3"
                         style="width:50px; height:50px; object-fit:cover;"
                         onerror="this.src='https://via.placeholder.com/50'">
                    <div class="flex-grow-1">
                        <h6 class="mb-0">${name}</h6>
                        <small class="text-muted">ID: ${id}</small>
                    </div>
                    <input type="hidden" name="combo_products[]" value="${id}">
                    <button type="button" class="btn btn-sm btn-outline-danger" 
                            onclick="removeProduct(${id})">
                        <i class="ri-close-line"></i> Remove
                    </button>
                </div>
            </div>
        </div>
    `;

    $('#selectedProducts').append(productHtml);
}

// Function to remove selected product
function removeProduct(id) {
    $(`#selected-product-${id}`).remove();
    
    // Show no products alert if none left
    if ($('[name="combo_products[]"]').length === 0) {
        $('#noProductsAlert').show();
    }
}
</script>
@endsection
