@extends('manager.layout')
@section('content')
<div class="adminPagesBodySec">
    <div class="container-fluid">		
        <div class="adminPagesBodySecinner">
            <div class="row rowBox">		
                <div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox">
                    <div class="dashboard_card">
                        <div class="d-flex justify-content-between dashboard_card_header">
                            <div class="leftPart">
                                <h4 class="mb-0 dashboard_card_title">Manager Orders</h4>
                            </div>
                            <div class="rightPart"></div>
                        </div>
                        <div class="dashboard_card_body p-0">
                            <div class="table-responsive">
                                <table class="table" id="ordersTable">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Order ID</th>
                                            <th>Customer Name</th>
                                            <th>Phone</th>
                                            <th>Email</th>
                                            <th>Address</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($orders as $index => $order)
                                            <tr>
                                                <td>{{ ($orders->currentPage() - 1) * $orders->perPage() + $index + 1 }}</td>
                                                <td>{{ $order->id }}</td>
                                                <td>{{ $order->customer->name ?? 'N/A' }}</td>
                                                <td>{{ $order->customer->phone ?? 'N/A' }}</td>
                                                <td>{{ $order->customer->email ?? 'N/A' }}</td>
                                                <td>
                                                    {{ $order->customer->firstAddress->address ?? '' }},
                                                    {{ $order->customer->firstAddress->city ?? '' }},
                                                    {{ $order->customer->firstAddress->state ?? '' }} -
                                                    {{ $order->customer->firstAddress->pincode ?? '' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('manager.order.details', $order->id) }}" class="btn btn-primary btn-sm">View Order</a>
                                                </td>
                                            </tr>
                                        @endforeach

                                        @if($orders->isEmpty())
                                            <tr>
                                                <td colspan="7" class="text-center">No orders found.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $orders->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Optional: Table sorting without overriding pagination -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const getCellValue = (tr, idx) => tr.children[idx].innerText || tr.children[idx].textContent;

        const comparer = (idx, asc) => (a, b) => ((v1, v2) => 
            v1 !== '' && v2 !== '' && !isNaN(v1) && !isNaN(v2) ? v1 - v2 : v1.toString().localeCompare(v2)
        )(getCellValue(asc ? a : b, idx), getCellValue(asc ? b : a, idx));

        document.querySelectorAll('th').forEach(th => th.addEventListener('click', (() => {
            const table = th.closest('table');
            Array.from(table.querySelectorAll('tr:nth-child(n+2)'))
                .sort(comparer(Array.from(th.parentNode.children).indexOf(th), this.asc = !this.asc))
                .forEach(tr => table.appendChild(tr) );
        })));
    });
</script>

@endsection
