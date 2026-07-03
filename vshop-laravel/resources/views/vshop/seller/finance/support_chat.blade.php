@extends('vshop.seller.main')

@section('content')

<div class="container-fluid">

    <div class="card shadow-sm">

        {{-- HEADER --}}
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Support Chat</h5>
            <span class="badge bg-primary">Order ID: {{ $orderId }}</span>
        </div>

        {{-- CHAT BODY --}}
        <div class="card-body" style="height:400px; overflow-y:auto;" id="chatBox">

            @forelse($messages as $msg)

                @if($msg->sender == 'seller')
                    <div class="d-flex justify-content-end mb-3">
                        <div class="bg-primary text-white p-2 rounded" style="max-width:70%;">
                            {{ $msg->message }}
                            <div class="small text-end">{{ $msg->created_at }}</div>
                        </div>
                    </div>
                @else
                    <div class="d-flex justify-content-start mb-3">
                        <div class="bg-light p-2 rounded border" style="max-width:70%;">
                            <strong>Support:</strong> {{ $msg->message }}
                            <div class="small text-muted">{{ $msg->created_at }}</div>
                        </div>
                    </div>
                @endif

            @empty
                <p class="text-center text-muted">No messages yet</p>
            @endforelse

        </div>

        {{-- INPUT BOX --}}
        <div class="card-footer">

            <form action="{{ route('seller.finance.send.message') }}" method="POST">
                @csrf

                <input type="hidden" name="order_id" value="{{ $orderId }}">

                <div class="input-group">
                    <input type="text" name="message" class="form-control" placeholder="Type your message..." required>
                    <button class="btn btn-primary">Send</button>
                </div>

            </form>

        </div>

    </div>

</div>

@endsection