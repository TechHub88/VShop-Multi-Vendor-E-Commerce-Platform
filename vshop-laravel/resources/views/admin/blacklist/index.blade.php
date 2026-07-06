@extends('layouts.vertical', ['title' => 'Blacklist Management'])

@section('content')

<style>
    .table th {
        background: #004392 !important;
        color: #fff !important;
        border-color: #fff;
    }
    .btn-primary,
    .btn-warning,
    .btn-danger,
    .btn-info {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-light border {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
        color: #fff !important;
    }
    .btn-secondary {
        background: #61b3e5 !important;
        color: #fff;
        border: 0 !important;
    }
    .btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Blacklist Control Panel</h4>
                    <div class="dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Filter by Type
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.blacklist.index') }}">All</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.blacklist.index', ['type' => 'seller']) }}">Blocked Sellers</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.blacklist.index', ['type' => 'product']) }}">Blocked Products</a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.blacklist.index', ['type' => 'customer']) }}">Blocked Customers</a></li>
                        </ul>
                    </div>
                </div>
                <p class="card-title-desc">Manage blocked sellers, products, and customers with detailed reasons and history.</p>
            </div>
            <div class="card-body">
                
                @if($type == 'customer')
                <!-- Blocked Customers Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Customer Name</th>
                                <th>Email</th>
                                <th>Block Reason</th>
                                <th>Blocked By</th>
                                <th>Blocked At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blockedCustomers as $customer)
                            <tr>
                                <td>{{ $customer->id }}</td>
                                <td><strong>{{ $customer->name }}</strong></td>
                                <td>{{ $customer->email }}</td>
                                <td>{{ Str::limit($customer->block_reason, 50) }}</td>
                                <td>{{ $customer->blocked_by_name ?? 'Admin' }}</td>
                                <td>{{ $customer->blocked_at ? \Carbon\Carbon::parse($customer->blocked_at)->format('d M Y, h:i A') : 'N/A' }}</td>
                                <td><span class="badge bg-danger">Blocked</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewCustomerModal{{ $customer->id }}">
                                        <i class="ri-eye-line"></i> View
                                    </button>
                                    <form action="{{ route('admin.unblock.customer', $customer->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Unblock this customer?')">
                                            <i class="ri-lock-unlock-line"></i> Unblock
                                        </button>
                                    </form>
                                 </td>
                             </tr>
                            
                            <!-- View Customer Modal -->
                            <div class="modal fade" id="viewCustomerModal{{ $customer->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-info text-white">
                                            <h5 class="modal-title">Block Details - {{ $customer->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Reason:</strong></p>
                                            <p class="alert alert-secondary">{{ $customer->block_reason }}</p>
                                            <hr>
                                            <p><strong>Blocked By:</strong> {{ $customer->blocked_by_name ?? 'System' }}</p>
                                            <p><strong>Blocked At:</strong> {{ $customer->blocked_at ? \Carbon\Carbon::parse($customer->blocked_at)->format('d M Y, h:i A') : 'N/A' }}</p>
                                            <p><strong>Customer Email:</strong> {{ $customer->email }}</p>
                                            <p><strong>Customer Phone:</strong> {{ $customer->phone ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No blocked customers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $blockedCustomers->links() }}
                </div>

                @elseif($type == 'product')
                <!-- Blocked Products Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Product ID</th>
                                <th>Product Name</th>
                                <th>Seller</th>
                                <th>Reason</th>
                                <th>Blocked By</th>
                                <th>Blocked At</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blockedProducts as $product)
                            <tr>
                                <td>{{ $product['product_id'] }}</td>
                                <td><strong>{{ $product['product_name'] }}</strong></td>
                                <td>{{ $product['seller_name'] ?? 'N/A' }}</td>
                                <td>{{ Str::limit($product['reason'], 50) }}</td>
                                <td>{{ $product['blocked_by_name'] ?? 'System' }}</td>
                                <td>{{ isset($product['blocked_at']) ? \Carbon\Carbon::parse($product['blocked_at'])->format('d M Y, h:i A') : 'N/A' }}</td>
                                <td><span class="badge bg-warning">Blocked</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewProductModal{{ $product['product_id'] }}">
                                        <i class="ri-eye-line"></i> View
                                    </button>
                                    <form action="{{ route('admin.unblock.product', $product['product_id']) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Unblock this product?')">
                                            <i class="ri-lock-unlock-line"></i> Unblock
                                        </button>
                                    </form>
                                 </td>
                             </tr>
                            
                            <!-- View Product Modal -->
                            <div class="modal fade" id="viewProductModal{{ $product['product_id'] }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title">Product Block Details</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p><strong>Product:</strong> {{ $product['product_name'] }}</p>
                                            <p><strong>Product ID:</strong> {{ $product['product_id'] }}</p>
                                            <p><strong>Reason:</strong></p>
                                            <p class="alert alert-secondary">{{ $product['reason'] }}</p>
                                            <hr>
                                            <p><strong>Blocked By:</strong> {{ $product['blocked_by_name'] ?? 'System' }}</p>
                                            <p><strong>Blocked At:</strong> {{ isset($product['blocked_at']) ? \Carbon\Carbon::parse($product['blocked_at'])->format('d M Y, h:i A') : 'N/A' }}</p>
                                            <p><strong>Seller:</strong> {{ $product['seller_name'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">No blocked products found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @else
                <!-- Blocked Sellers Table (Default) -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Seller/Business</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Block Reason</th>
                                <th>Blocked By</th>
                                <th>Duration</th>
                                <th>Blocked On</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($blockedSellers as $seller)
                            @php
                                $blockMeta = json_decode($seller->block_metadata, true);
                            @endphp
                            <tr>
                                <td>{{ $seller->id }}</td>
                                <td>
                                    <strong>{{ $seller->org_name ?? $seller->name }}</strong><br>
                                    <small class="text-muted">ID: {{ $seller->id }}</small>
                                 </td>
                                <td>{{ $seller->email }}</td>
                                <td>{{ $seller->mobile }}</td>
                                <td>{{ Str::limit($blockMeta['reason'] ?? 'No reason provided', 50) }}</td>
                                <td>{{ $blockMeta['blocked_by_name'] ?? 'System' }}</td>
                                <td>
                                    @if(isset($blockMeta['block_until']))
                                        <span class="badge bg-warning">Until {{ \Carbon\Carbon::parse($blockMeta['block_until'])->format('d M Y') }}</span>
                                    @else
                                        <span class="badge bg-danger">Permanent</span>
                                    @endif
                                 </td>
                                <td>{{ isset($blockMeta['blocked_at']) ? \Carbon\Carbon::parse($blockMeta['blocked_at'])->format('d M Y') : $seller->updated_at->format('d M Y') }}</td>
                                <td><span class="badge bg-danger">Blocked</span></td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewSellerModal{{ $seller->id }}">
                                        <i class="ri-eye-line"></i> View
                                    </button>
                                    <form action="{{ route('admin.unblock.seller', $seller->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('Unblock this seller? This will set the seller status to pending.')">
                                            <i class="ri-lock-unlock-line"></i> Unblock
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.seller.timeline', $seller->id) }}" class="btn btn-sm btn-secondary">
                                        <i class="ri-history-line"></i> Timeline
                                    </a>
                                 </td>
                             </tr>
                            
                            <!-- View Seller Modal -->
                            <div class="modal fade" id="viewSellerModal{{ $seller->id }}" tabindex="-1">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Seller Block Details - {{ $seller->org_name ?? $seller->name }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <p><strong>Business Name:</strong> {{ $seller->org_name ?? $seller->name }}</p>
                                                    <p><strong>Email:</strong> {{ $seller->email }}</p>
                                                    <p><strong>Phone:</strong> {{ $seller->mobile }}</p>
                                                </div>
                                                <div class="col-md-6">
                                                    <p><strong>Status:</strong> <span class="badge bg-danger">Blocked</span></p>
                                                    <p><strong>Blocked By:</strong> {{ $blockMeta['blocked_by_name'] ?? 'System' }}</p>
                                                    <p><strong>Blocked At:</strong> {{ isset($blockMeta['blocked_at']) ? \Carbon\Carbon::parse($blockMeta['blocked_at'])->format('d M Y, h:i A') : 'N/A' }}</p>
                                                </div>
                                            </div>
                                            <hr>
                                            <p><strong>Block Reason:</strong></p>
                                            <div class="alert alert-secondary">
                                                {{ $blockMeta['reason'] ?? 'No reason provided' }}
                                            </div>
                                            @if(isset($blockMeta['block_until']))
                                            <p><strong>Block Until:</strong> {{ \Carbon\Carbon::parse($blockMeta['block_until'])->format('d M Y') }}</p>
                                            @endif
                                            
                                            @php
                                                $productBlocks = json_decode($seller->product_blocks ?? '[]', true);
                                            @endphp
                                            @if(count($productBlocks) > 0)
                                            <hr>
                                            <h6>Blocked Products ({{ count($productBlocks) }}):</h6>
                                            <ul>
                                                @foreach($productBlocks as $pb)
                                                <li>{{ $pb['product_name'] ?? 'Product #'.$pb['product_id'] }} - {{ $pb['reason'] }}</li>
                                                @endforeach
                                            </ul>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="10" class="text-center">No blocked sellers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $blockedSellers->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('script')
<script>
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut('slow');
    }, 5000);
</script>
@endsection
