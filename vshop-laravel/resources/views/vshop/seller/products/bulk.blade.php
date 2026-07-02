@extends('vshop.seller.main')

@section('title','Bulk Product Management')

@section('content')

<style>
.required::after {
    content: " *";
    color: red;
    font-weight: bold;
}
input {
    display: block !important;
}
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
    background: #233d78 !important;
    border: 0 !important;
}
.btn-primary {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.btn-outline-primary {
    border-color:#233d78 !important;
    color: #233d78 !important;
}
.btn-outline-primary:hover {
    background:#233d78 !important;
    color: #fff !important;
}
.btn-danger {
    background: #61b3e5 !important;
    color: #fff !important;
    border: 0 !important;
}
.table th {
    font-weight: 600;
    color: #fff !important;
    background-color: #004392 !important;
    border: 1px solid #fff;
}


</style>

<div class="container-fluid py-4">

<div class="card shadow border-0">
<div class="card-body">

{{-- HEADER --}}
<div class="d-flex justify-content-between align-items-center mb-3">

    <div class="d-flex top-back-heading-sec">
        <button onclick="goBack()" class="nav-btn">
            <i class="fa-solid fa-arrow-left"></i>
        </button>
        <h5 class="text-primary mb-0">Bulk Product Operations</h5>
    </div>

    <div class="d-flex gap-2">

        <a href="{{ route('seller.products.sample') }}" 
           class="btn btn-outline-primary">
             Sample Excel
        </a>

        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
            <i class="fa-solid fa-pen-to-square me-1"></i> Bulk Edit
        </button>

    </div>

</div>

{{-- ================= UPLOAD ================= --}}
<form action="{{ route('seller.products.bulk.upload') }}" method="POST" enctype="multipart/form-data" class="mb-4">
@csrf

<div class="row g-2 align-items-center">

    <div class="col-md-6">

        {{-- LABEL WITH REQUIRED --}}
        <label class="form-label required fw-bold">Upload Excel File</label>

        <input type="file" 
               name="file" 
               class="form-control @error('file') is-invalid @enderror"
               id="fileInput">
               
               <small class="text-danger">Unique Name & SKU per row</small>

        {{-- ERROR --}}
        @error('file')
        <small class="text-danger errorMsg d-block">{{ $message }}</small>
        @enderror

        {{-- ROW ERRORS --}}
        @if(session('errorRows'))
            <div class="mt-2 errorMsg">
                @foreach(session('errorRows') as $err)
                    <small class="text-danger d-block">{{ $err }}</small>
                @endforeach
            </div>
        @endif

    </div>

    <div class="col-md-3">
        <button class="btn btn-success w-100" style="margin-top: 30px;">
            ⬆ Upload & Process
        </button>
    </div>

</div>

</form>

<hr>

{{-- ================= BULK ACTION ================= --}}
<form id="bulkForm" method="POST">
@csrf

<div class="d-flex gap-3 mb-3">

    <button formaction="{{ route('seller.products.bulk.update') }}"
        class="btn btn-success px-4">
        ✅ Update Selected
    </button>

    <button formaction="{{ route('seller.products.bulk.delete') }}"
        class="btn btn-danger px-4"
        onclick="return confirm('Are you sure to delete selected products?')">
        🗑 Delete Selected
    </button>

</div>

{{-- TABLE --}}
<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark text-center">
<tr>
<th width="50">
    <input type="checkbox" id="checkAll">
</th>
<th>ID</th>
<th>Name</th>
<th>Category</th>
<th>Boost</th>
<th width="120">Price</th>
<th width="160">Status</th>
<th width="120">Stock</th>
</tr>
</thead>

<tbody>

@forelse($products as $p)
<tr>

<td class="text-center">
    <input type="checkbox"
           name="ids[]"
           value="{{ $p->id }}"
           class="rowCheckbox">
</td>

<td>{{ $p->id }}</td>

<td>
    <strong>{{ $p->name }}</strong><br>
    <small class="text-muted">{{ $p->product_code }}</small>
</td>

<td>
    <select name="category_id[{{ $p->id }}]" class="form-select form-select-sm">
        <option value="">No Category</option>
        @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ $p->category_id == $cat->id ? 'selected' : '' }}>
                {{ $cat->name }}
            </option>
        @endforeach
    </select>
</td>

<td>
    <input type="text"
           class="form-control form-control-sm text-center"
           name="boost[{{ $p->id }}]"
           value="{{ $p->boost }}">
</td>

<td>
    <input type="number"
           class="form-control form-control-sm text-center"
           name="price[{{ $p->id }}]"
           value="{{ $p->price }}">
</td>

<td>
    <select name="status[{{ $p->id }}]" class="form-select form-select-sm">
        <option value="in_progress" {{ $p->status=='in_progress'?'selected':'' }}>In Progress</option>
        <option value="not_verified" {{ $p->status=='not_verified'?'selected':'' }}>Not Verified</option>
        <option value="verified" {{ $p->status=='verified'?'selected':'' }}>Verified</option>
        <option value="unavailable" {{ $p->status=='unavailable'?'selected':'' }}>Unavailable</option>
    </select>
</td>

<td>
    <input type="number"
           class="form-control form-control-sm text-center"
           name="stock[{{ $p->id }}]"
           value="{{ $p->inventory }}">
</td>

</tr>
@empty
<tr>
<td colspan="8" class="text-center text-muted py-4">
    No products found
</td>
</tr>
@endforelse

</tbody>

</table>

</div>

<div class="text-center mt-3">
    {{ $products->links('pagination::bootstrap-5') }}
</div>

<!-- Bulk Edit Modal -->
<div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" style="max-width: 480px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius: 12px; overflow: hidden;">
      <div class="modal-header bg-gradient-primary text-white border-0 py-3" style="background: linear-gradient(135deg, #0d6efd, #0056b3);">
        <h6 class="modal-title fw-bold" id="bulkEditModalLabel">
          <i class="fa-solid fa-pen-to-square me-2"></i>Bulk Edit Selected Products
        </h6>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-4 text-start bg-light">
        <div class="alert alert-warning py-2 px-3 small border-0 mb-3" style="background-color: #fff3cd; color: #856404; border-radius: 8px;">
          <i class="fa-solid fa-triangle-exclamation me-1"></i> Leave fields blank/unselected to keep current values.
        </div>
        
        <div class="row g-3">
          <!-- Category Group -->
          <div class="col-6">
            <label class="form-label small fw-bold text-secondary mb-1" for="bulk_category_id">Category</label>
            <select id="bulk_category_id" class="form-select form-select-sm" name="bulk_category_id" style="border-radius: 6px;">
              <option value="">No Change</option>
              @foreach($categories as $cat)
                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
              @endforeach
            </select>
          </div>

          <!-- Status Group -->
          <div class="col-6">
            <label class="form-label small fw-bold text-secondary mb-1" for="bulk_status">Status</label>
            <select id="bulk_status" class="form-select form-select-sm" name="bulk_status" style="border-radius: 6px;">
              <option value="">No Change</option>
              <option value="in_progress">In Progress</option>
              <option value="not_verified">Not Verified</option>
              <option value="verified">Verified</option>
              <option value="unavailable">Unavailable</option>
            </select>
          </div>

          <!-- Price Group -->
          <div class="col-6">
            <label class="form-label small fw-bold text-secondary mb-1" for="bulk_price">Price</label>
            <input type="number" step="0.01" id="bulk_price" class="form-control form-control-sm" name="bulk_price" placeholder="No Change" style="border-radius: 6px;">
          </div>

          <!-- Stock Group -->
          <div class="col-6">
            <label class="form-label small fw-bold text-secondary mb-1" for="bulk_stock">Stock</label>
            <input type="number" id="bulk_stock" class="form-control form-control-sm" name="bulk_stock" placeholder="No Change" style="border-radius: 6px;">
          </div>

          <!-- Boost Group -->
          <div class="col-12">
            <label class="form-label small fw-bold text-secondary mb-1" for="bulk_boost">Boost</label>
            <input type="text" id="bulk_boost" class="form-control form-control-sm" name="bulk_boost" placeholder="Enter boost value (No Change)" style="border-radius: 6px;">
          </div>
        </div>
      </div>
      <div class="modal-footer bg-light border-0 py-3 px-4">
        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal" style="border-radius: 20px;">Cancel</button>
        <button type="submit" id="btnApplyBulkEdit" formaction="{{ route('seller.products.bulk.update') }}" class="btn btn-sm btn-primary px-4" style="border-radius: 20px; background: #0d6efd;">Save Changes</button>
      </div>
    </div>
  </div>
</div>

</form>

</div>
</div>

</div>

{{-- ================= STYLE ================= --}}
<style>
input[type="checkbox"] {
    appearance: auto !important;
    width: 18px !important;
    height: 18px !important;
    cursor: pointer;
}
.table td, .table th {
    vertical-align: middle !important;
}
</style>

{{-- ================= SCRIPT ================= --}}
<script>

// SELECT ALL
document.getElementById('checkAll').addEventListener('click', function(){
    document.querySelectorAll('.rowCheckbox').forEach(cb => {
        cb.checked = this.checked;
    });
});

// SYNC CHECKBOX
document.querySelectorAll('.rowCheckbox').forEach(cb => {
    cb.addEventListener('change', function(){
        let total = document.querySelectorAll('.rowCheckbox').length;
        let checked = document.querySelectorAll('.rowCheckbox:checked').length;
        document.getElementById('checkAll').checked = (total === checked);
    });
});

// BULK VALIDATION
document.getElementById('bulkForm').addEventListener('submit', function(e){
    let checked = document.querySelectorAll('.rowCheckbox:checked');

    if(checked.length === 0){
        alert('⚠ Please select at least one product');
        e.preventDefault();
    }
});

// Validation for Bulk Edit Apply
document.getElementById('btnApplyBulkEdit').addEventListener('click', function(e) {
    let hasFilled = false;
    ['bulk_category_id', 'bulk_boost', 'bulk_price', 'bulk_status', 'bulk_stock'].forEach(id => {
        let el = document.getElementById(id);
        if (el && el.value !== '') {
            hasFilled = true;
        }
    });

    if (!hasFilled) {
        alert('⚠ Please fill/select at least one field to update in the bulk edit popup.');
        e.preventDefault();
        return;
    }
    
    let checkedProducts = document.querySelectorAll('.rowCheckbox:checked');
    if (checkedProducts.length === 0) {
        alert('⚠ Please select at least one product from the table first.');
        e.preventDefault();
        return;
    }
    
    // Append a hidden input to indicate it's a bulk edit popup submit
    let hiddenInput = document.createElement('input');
    hiddenInput.type = 'hidden';
    hiddenInput.name = 'bulk_edit_apply';
    hiddenInput.value = '1';
    document.getElementById('bulkForm').appendChild(hiddenInput);
});

// ROW CLICK
document.querySelectorAll('tbody tr').forEach(row => {
    row.addEventListener('click', function(e){
        if(e.target.tagName !== 'INPUT' && e.target.tagName !== 'SELECT'){
            let cb = this.querySelector('.rowCheckbox');
            cb.checked = !cb.checked;
        }
    });
});

// ================= AUTO HIDE ERROR =================
setTimeout(() => {
    document.querySelectorAll('.errorMsg').forEach(el => {
        el.style.transition = "0.5s";
        el.style.opacity = "0";
        setTimeout(() => el.remove(), 500);
    });
}, 2500);

// REMOVE ERROR ON FILE SELECT
document.getElementById('fileInput')?.addEventListener('change', function(){
    document.querySelectorAll('.errorMsg').forEach(el => el.remove());
    this.classList.remove('is-invalid');
});

</script>

@endsection