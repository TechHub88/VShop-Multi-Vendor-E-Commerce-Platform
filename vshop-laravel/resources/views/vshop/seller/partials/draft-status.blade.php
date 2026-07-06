@php
    $dbStatus = $product->status ?? 'pending';

    // ✅ MAP DB STATUS → UI STATUS
    if(in_array($dbStatus, ['pending','in_progress'])){
        $status = 'in_progress';
    }
    elseif(in_array($dbStatus, ['rejected','not_verified'])){
        $status = 'not_verified';
    }
    elseif(in_array($dbStatus, ['approved','verified'])){
        $status = 'verified';
    }
    else{
        $status = 'unavailable';
    }

    // ✅ ONLY 4 STEPS UI
    $steps = [
        'in_progress'  => 'In Progress',
        'not_verified' => 'Not Verified',
        'verified'     => 'Verified',
        'unavailable'  => 'Unavailable',
    ];

    $keys = array_keys($steps);
    $statusIndex = array_search($status, $keys);
@endphp

<div class="container-fluid bg-white border-bottom py-2 mb-3">
    <div class="container">

        <div class="d-flex justify-content-between align-items-center">

            @foreach($steps as $key => $label)

                @php $i = array_search($key, $keys); @endphp

                <div class="text-center flex-fill position-relative">

                    {{-- LINE --}}
                    @if(!$loop->last)
                        <div class="tracker-line {{ $statusIndex > $i ? 'active' : '' }}"></div>
                    @endif

                    {{-- CIRCLE --}}
                    <div class="tracker-circle
                        @if($status == $key)
                            current
                        @elseif($statusIndex > $i)
                            done
                        @endif
                    ">
                        {{ $loop->iteration }}
                    </div>

                    {{-- LABEL --}}
                    <div class="tracker-label">
                        {{ $label }}
                    </div>

                </div>

            @endforeach

        </div>

    </div>
</div>
<style>
    .draft-tracker {
    padding: 10px 0;
}

/* CIRCLE */
.tracker-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #dee2e6;
    color: #6c757d;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin: auto;
    position: relative;
    z-index: 2;
}

/* COMPLETED */
.tracker-circle.done {
    background: #198754;
    color: #fff;
}

/* CURRENT */
.tracker-circle.current {
    background: #0d6efd;
    color: #fff;
    box-shadow: 0 0 0 4px rgba(13,110,253,0.15);
}

/* REJECTED / BLOCKED */
.tracker-circle.blocked {
    background: #dc3545;
    color: #fff;
}

/* LINE */
.tracker-line {
    position: absolute;
    top: 16px;
    left: 50%;
    width: 100%;
    height: 3px;
    background: #dee2e6;
    z-index: 1;
}

.tracker-line.active {
    background: #198754;
}

/* LABEL */
.tracker-label {
    font-size: 11px;
    margin-top: 6px;
    color: #6c757d;
}
</style>
