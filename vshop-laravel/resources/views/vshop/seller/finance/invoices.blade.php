@extends('vshop.seller.main')

@section('content')

<div class="container-fluid">

    <h4 class="mb-4">Invoices</h4>

    {{-- SUCCESS --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- ================= GENERATE ================= --}}
    <div class="card mb-4 p-3">
        <form action="{{ route('seller.finance.generate.invoice') }}" method="POST">
            @csrf
            <button class="btn btn-primary">Generate Invoice</button>
        </form>
    </div>

    {{-- ================= TABLE ================= --}}
    <div class="card p-3">

        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Invoice No</th>
                    <th>Total</th>
                    <th>GST</th>
                    <th>Net</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($invoices as $inv)
                <tr>
                    <td>{{ $inv->invoice_no }}</td>
                    <td class="text-primary">₹ {{ $inv->total_amount }}</td>
                    <td class="text-warning">₹ {{ $inv->gst_amount }}</td>
                    <td class="text-success">₹ {{ $inv->net_amount }}</td>
                    <td>{{ $inv->invoice_date }}</td>
                    <td>
                        <a href="{{ route('finance.invoice.download',$inv->id) }}"
                           class="btn btn-success btn-sm">
                           Download
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center">No invoices found</td>
                </tr>
                @endforelse
            </tbody>

        </table>

    </div>

</div>

@endsection
