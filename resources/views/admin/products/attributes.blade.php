@extends('layouts.vertical', ['title' => 'Add Product Attributes'])

@section('content')
<div class="container-fluid">

<h4>Product: {{ $product->name }}</h4>

@if(session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

{{-- ================= VARIANT FORM ================= --}}
<form action="{{ route('admin.products.attributes.store', $product->id) }}" method="POST">
@csrf

<table class="table table-bordered" id="variantTable">
<thead>
<tr>
    <th>Size</th>
    <th>Color</th>
    <th>Price</th>
    <th>Stock</th>
    <th width="80">Action</th>
</tr>
</thead>

<tbody>
<tr>
    {{-- SIZE --}}
    <td>
        <select name="variants[0][size]" class="form-control">
            <option value="">Select Size</option>
            @foreach(($attributes['size']->terms ?? []) as $term)
                <option value="{{ $term->id }}">{{ $term->term }}</option>
            @endforeach
        </select>
    </td>

    {{-- COLOR --}}
    <td>
        <select name="variants[0][color]" class="form-control">
            <option value="">Select Color</option>
            @foreach(($attributes['color']->terms ?? []) as $term)
                <option value="{{ $term->id }}">{{ $term->term }}</option>
            @endforeach
        </select>
    </td>

    <td><input type="number" name="variants[0][price]" class="form-control"></td>
    <td><input type="number" name="variants[0][stock]" class="form-control"></td>

    <td>
        <button type="button" class="btn btn-danger removeRow">×</button>
    </td>
</tr>
</tbody>
</table>

<button type="button" id="addRow" class="btn btn-secondary mb-3">+ Add Variant</button>
<br>
<button class="btn btn-primary">Save Variants</button>
</form>


{{-- ================= EXISTING VARIANTS ================= --}}
<h5 class="mt-4">Existing Variants</h5>

@if($variants->count())
<table class="table table-bordered">
<tr>
    <th>Size</th>
    <th>Color</th>
    <th>Price</th>
    <th>Stock</th>
</tr>

@foreach($variants as $combinationKey => $items)

    @php
        $size = '';
        $color = '';
        $price = '';
        $stock = '';

        foreach ($items as $row) {
            if (strtolower($row->attribute->name) == 'size') {
                $size = $row->term->term ?? '';
            }
            if (strtolower($row->attribute->name) == 'color') {
                $color = $row->term->term ?? '';
            }

            // same for all rows in combination
            $price = $row->price;
            $stock = $row->product_stock;
        }
    @endphp

<tr>
    <td>{{ $size }}</td>
    <td>{{ $color }}</td>
    <td>{{ $price }}</td>
    <td>{{ $stock }}</td>
</tr>

@endforeach
</table>
@else
<div class="alert alert-info">No variants added yet.</div>
@endif

{{-- ================= JS ================= --}}
<script>
let index = 1;

document.getElementById('addRow').onclick = function () {
    let table = document.querySelector('#variantTable tbody');
    let row = table.rows[0].cloneNode(true);

    row.querySelectorAll('select, input').forEach(el => {
        el.name = el.name.replace(/\d+/, index);
        el.value = '';
    });

    table.appendChild(row);
    index++;
};

document.addEventListener('click', function(e){
    if(e.target.classList.contains('removeRow')){
        let rows = document.querySelectorAll('#variantTable tbody tr');
        if(rows.length > 1){
            e.target.closest('tr').remove();
        }
    }
});
</script>

@endsection
