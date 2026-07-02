@extends('layouts.vertical', ['title' => 'Combo Products'])

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Combo Products</h4>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-primary btn-sm">Back to Products</a>
                </div>
                <div class="card-body">
                    @if($combos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>SKU</th>
                                        <th>Name</th>
                                        <th>Main Product</th>
                                        <th>Products in Combo</th>
                                        <th>Price</th>
                                        <th>Regular Price</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($combos as $combo)
                                        <tr>
                                            <td>{{ $combo->id }}</td>
                                            <td>{{ $combo->sku }}</td>
                                            <td>{{ $combo->name }}</td>
                                            <td>
                                                @if($combo->mainProduct)
                                                    {{ $combo->mainProduct->name }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($combo->items->count() > 0)
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($combo->items as $item)
                                                            <li>
                                                                <small>{{ $item->comboProduct->name ?? 'N/A' }}</small>
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <span class="text-muted">No products</span>
                                                @endif
                                            </td>
                                            <td>₹{{ number_format($combo->price, 2) }}</td>
                                            <td>₹{{ number_format($combo->regular_price, 2) }}</td>
                                            <td>
                                                <span class="badge bg-{{ $combo->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ $combo->status }}
                                                </span>
                                            </td>
                                            <td>{{ $combo->created_at->format('M d, Y H:i') }}</td>
                                            <td>
                                                <button class="btn btn-sm btn-info">View</button>
                                                <button class="btn btn-sm btn-warning">Edit</button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <h5>No combos found!</h5>
                            <p class="mb-0">Create your first combo by going to a product page and using the "Create Combo" feature.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection