@extends('layouts.vertical', ['title' => 'Edit Coupon'])

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header"><h5>Edit Coupon: {{ $coupon->discount_code }}</h5></div>

        <div class="card-body">
            <form action="{{ route('admin.coupons.update', $coupon->id) }}" method="POST" class="row g-3">
                @csrf
                @method('PUT')

                <!-- Discount Code -->
                <div class="col-md-6">
                    <label class="form-label">Discount Code <span class="text-danger">*</span></label>
                    <input type="text" name="discount_code" class="form-control" value="{{ old('discount_code', $coupon->discount_code) }}" required placeholder="Enter discount code">
                    @error('discount_code')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Coupon Type (Product/Travel/All) - Only for Admin -->
                @if($userType == 'admin')
                <div class="col-md-6">
                    <label class="form-label">Apply To <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="">Select Type</option>
                        <option value="product" {{ (old('type') ?? $coupon->type) == 'product' ? 'selected' : '' }}>Product Wise</option>
                        <option value="travel" {{ (old('type') ?? $coupon->type) == 'travel' ? 'selected' : '' }}>Travel Wise</option>
                        <option value="all" {{ (old('type') ?? $coupon->type) == 'all' ? 'selected' : '' }}>All (Both Product & Travel)</option>
                    </select>
                    @error('type')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                @else
                <!-- Show coupon type as read-only -->
                <div class="col-md-6">
                    <label class="form-label">Apply To <span class="text-danger">*</span></label>
                    <input type="text" class="form-control bg-light" value="{{ ucfirst($coupon->type) }} Coupon" readonly>
                    <small class="text-muted">This is a {{ $coupon->type }} coupon</small>
                    <input type="hidden" name="type" value="{{ $coupon->type }}">
                </div>
                @endif

                <!-- Coupon amount -->
                <div class="col-md-6">
                    <label class="form-label">Coupon amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="coupon_amount" class="form-control" value="{{ old('coupon_amount', $coupon->coupon_amount) }}" required placeholder="Enter amount">
                    @error('coupon_amount')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Discount type -->
                <div class="col-md-6">
                    <label class="form-label">Discount type <span class="text-danger">*</span></label>
                    <select name="discount_type" class="form-select" required>
                        <option value="">Select Discount Type</option>
                        <option value="percentage" {{ (old('discount_type') ?? $coupon->discount_type) == 'percentage' ? 'selected' : '' }}>Percentage</option>
                        <option value="fixed" {{ (old('discount_type') ?? $coupon->discount_type) == 'fixed' ? 'selected' : '' }}>Fixed Amount</option>
                    </select>
                    @error('discount_type')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Coupon Type (Public/Partner) -->
                <div class="col-md-6">
                    <label class="form-label">Coupon Type <span class="text-danger">*</span></label>
                    <select name="coupon_type" class="form-select" required>
                        <option value="">Select Coupon Type</option>
                        <option value="public" {{ (old('coupon_type') ?? $coupon->coupon_type) == 'public' ? 'selected' : '' }}>Public Coupon</option>
                        <option value="partner" {{ (old('coupon_type') ?? $coupon->coupon_type) == 'partner' ? 'selected' : '' }}>Partner Coupon</option>
                    </select>
                    @error('coupon_type')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Partner ID (Visible only when Partner type is selected) -->
                <div class="col-md-6" id="partner_id_field" style="display: {{ (old('coupon_type') ?? $coupon->coupon_type) == 'partner' ? 'block' : 'none' }};">
                    <label class="form-label">Partner ID</label>
                    <select name="partner_id" class="form-select">
                        <option value="">Select Partner</option>
                        @foreach($partners ?? [] as $partner)
                            <option value="{{ $partner->id }}" {{ (old('partner_id') ?? $coupon->partner_id) == $partner->id ? 'selected' : '' }}>
                                {{ $partner->name }} ({{ $partner->email }})
                            </option>
                        @endforeach
                    </select>
                    @error('partner_id')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Start Date -->
                <div class="col-md-6">
                    <label class="form-label">Start Date <span class="text-danger">*</span></label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $coupon->start_date->format('Y-m-d')) }}" required>
                    @error('start_date')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Expiry Date -->
                <div class="col-md-6">
                    <label class="form-label">Expiry Date <span class="text-danger">*</span></label>
                    <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date', $coupon->expiry_date->format('Y-m-d')) }}" required>
                    @error('expiry_date')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Minimum Spend -->
                <div class="col-md-6">
                    <label class="form-label">Minimum Spend</label>
                    <input type="number" step="0.01" name="minimum_spend" class="form-control" value="{{ old('minimum_spend', $coupon->minimum_spend) }}" placeholder="0.00">
                    @error('minimum_spend')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Maximum Discount Amount (For Percentage coupons) -->
                <div class="col-md-6" id="max_discount_field" style="display: {{ (old('discount_type') ?? $coupon->discount_type) == 'percentage' ? 'block' : 'none' }};">
                    <label class="form-label">Maximum Discount Amount</label>
                    <input type="number" step="0.01" name="max_discount_amount" class="form-control" value="{{ old('max_discount_amount', $coupon->max_discount_amount) }}" placeholder="Leave empty for no limit">
                    <small class="text-muted">Only applicable for percentage discounts</small>
                    @error('max_discount_amount')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label class="form-label">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ (old('status') ?? $coupon->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ (old('status') ?? $coupon->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Total Usage Limit -->
                <div class="col-md-6">
                    <label class="form-label">Total Usage Limit <span class="text-danger">*</span></label>
                    <input type="number" name="usage_limit" class="form-control" value="{{ old('usage_limit', $coupon->usage_limit) }}" placeholder="Total uses (e.g. 10)" required>
                    @error('usage_limit')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Platform (apply_on) -->
                <div class="col-md-6">
                    <label class="form-label">Platform <span class="text-danger">*</span></label>
                    <select name="apply_on" class="form-select" required>
                        <option value="both" {{ old('apply_on', $coupon->apply_on) == 'both' ? 'selected' : '' }}>Both V-Shop & V-Travel</option>
                        <option value="vshop" {{ old('apply_on', $coupon->apply_on) == 'vshop' ? 'selected' : '' }}>V-Shop Only</option>
                        <option value="vtravel" {{ old('apply_on', $coupon->apply_on) == 'vtravel' ? 'selected' : '' }}>V-Travel Only</option>
                    </select>
                    @error('apply_on')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Applicable On -->
                <div class="col-md-6">
                    <label class="form-label">Applicable On <span class="text-danger">*</span></label>
                    <select name="applicable_on" id="applicable_on_select" class="form-select" required>
                        <option value="all" {{ old('applicable_on', $coupon->applicable_on) == 'all' ? 'selected' : '' }}>All Products</option>
                        <option value="categories" {{ old('applicable_on', $coupon->applicable_on) == 'categories' ? 'selected' : '' }}>Specific Categories</option>
                        <option value="products" {{ old('applicable_on', $coupon->applicable_on) == 'products' ? 'selected' : '' }}>Specific Products</option>
                        <option value="sellers" {{ old('applicable_on', $coupon->applicable_on) == 'sellers' ? 'selected' : '' }}>Specific Sellers</option>
                    </select>
                    @error('applicable_on')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Category Select list -->
                <div class="col-12 applicability-list" id="wrapper_categories" style="display: none;">
                    <label class="form-label">Select Categories</label>
                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                        @foreach($categories ?? [] as $cat)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="applicable_ids[]" value="{{ $cat->id }}" id="cat_{{ $cat->id }}" {{ is_array(old('applicable_ids', $coupon->applicable_ids)) && in_array($cat->id, old('applicable_ids', $coupon->applicable_ids)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="cat_{{ $cat->id }}">
                                    {{ $cat->name ?? $cat->title }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Product Select list -->
                <div class="col-12 applicability-list" id="wrapper_products" style="display: none;">
                    <label class="form-label">Select Products</label>
                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                        @foreach($products ?? [] as $prod)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="applicable_ids[]" value="{{ $prod->id }}" id="prod_{{ $prod->id }}" {{ is_array(old('applicable_ids', $coupon->applicable_ids)) && in_array($prod->id, old('applicable_ids', $coupon->applicable_ids)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="prod_{{ $prod->id }}">
                                    {{ $prod->name }} (SKU: {{ $prod->sku ?? 'N/A' }})
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Seller Select list -->
                <div class="col-12 applicability-list" id="wrapper_sellers" style="display: none;">
                    <label class="form-label">Select Sellers</label>
                    <div class="border rounded p-3 bg-light" style="max-height: 200px; overflow-y: auto;">
                        @foreach($sellers ?? [] as $sel)
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="applicable_ids[]" value="{{ $sel->id }}" id="sel_{{ $sel->id }}" {{ is_array(old('applicable_ids', $coupon->applicable_ids)) && in_array($sel->id, old('applicable_ids', $coupon->applicable_ids)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="sel_{{ $sel->id }}">
                                    {{ $sel->name ?? ($sel->org_name ?? $sel->legal_name) }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Coupon Description -->
                <div class="col-12">
                    <label class="form-label">Coupon Description</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="Enter description">{{ old('description', $coupon->description) }}</textarea>
                    @error('description')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit -->
                <div class="col-12 text-end mt-3">
                    <a href="{{ route('admin.coupons.index') }}" class="btn btn-light">Cancel</a>
                    <button class="btn btn-primary">Update Coupon</button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Show/hide partner ID field based on coupon type
    const couponTypeSelect = document.querySelector('select[name="coupon_type"]');
    const partnerIdField = document.getElementById('partner_id_field');
    
    couponTypeSelect.addEventListener('change', function() {
        if (this.value === 'partner') {
            partnerIdField.style.display = 'block';
        } else {
            partnerIdField.style.display = 'none';
        }
    });

    // Show/hide max discount field based on discount type
    const discountTypeSelect = document.querySelector('select[name="discount_type"]');
    const maxDiscountField = document.getElementById('max_discount_field');
    
    discountTypeSelect.addEventListener('change', function() {
        if (this.value === 'percentage') {
            maxDiscountField.style.display = 'block';
        } else {
            maxDiscountField.style.display = 'none';
            // Clear max_discount_amount when hidden
            document.querySelector('input[name="max_discount_amount"]').value = '';
        }
    });
});
</script>
@endsection
