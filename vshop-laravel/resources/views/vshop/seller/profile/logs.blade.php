@extends('vshop.seller.main')

<style>

.nav-btn{
    border:1px solid #004392;
    color:#004392;
    background:transparent;
    border-radius:5px;
    font-size:14px;
    width:35px;
    height:38px;
}

.top-back-heading-sec{
    align-items:center;
    gap:10px;
}

/* =========================================
   LAYOUT
========================================= */

.marketplace-wrapper{
    display:flex;
    gap:24px;
    align-items:flex-start;
}

.marketplace-left{
    flex:1;
    min-width:0;
}

.marketplace-right{
    width:300px;
    min-width:300px;
    position:sticky;
    top:20px;
    align-self:flex-start;
    margin:0 !important;
    padding:0 !important;
}

.marketplace-right .profile-progress{
    margin:0 !important;
    padding:0 !important;
}

/* =========================================
   TIMELINE
========================================= */

.timeline{
    position:relative;
    padding-left:20px;
}

/* line */

.timeline::before{
    content:'';
    position:absolute;
    left:8px;
    top:0;
    bottom:0;
    width:2px;
    background:#ddd;
}

/* item */

.timeline-item{
    position:relative;
    margin-bottom:20px;
}

/* dot */

.timeline-dot{
    position:absolute;
    left:-2px;
    width:14px;
    height:14px;
    background:#198754;
    border-radius:50%;
}

/* card */

.timeline-content{
    background:#f8f9fa;
    padding:12px;
    border-radius:8px;
}

/* rows */

.change-row{
    margin-bottom:8px;
}

.field{
    font-weight:600;
    display:block;
}

.values{
    font-size:13px;
}

.old{
    color:#dc3545;
}

.new{
    color:#198754;
}

/* scroll */

.scrollable-timeline{
    max-height:450px;
    overflow-y:auto;
    padding-right:6px;
}

/* scrollbar */

.scrollable-timeline::-webkit-scrollbar{
    width:6px;
}

.scrollable-timeline::-webkit-scrollbar-thumb{
    background:#c1c1c1;
    border-radius:10px;
}

.scrollable-timeline::-webkit-scrollbar-thumb:hover{
    background:#999;
}

/* sticky */

.timeline-header{
    position:sticky;
    top:0;
    background:#fff;
    z-index:2;
    padding-bottom:10px;
}

/* =========================================
   MOBILE
========================================= */

@media(max-width:991px){

    .marketplace-wrapper{
        flex-direction:column;
    }

    .marketplace-right{
        width:100%;
        min-width:100%;
        position:relative;
        top:auto;
    }

}
.btn-success {
        background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
        border: 0 !important;
    }
    .btn-outline-primary {
        border-color: #084f9d !important;
        color: #084f9d !important;
    }
    .btn-outline-primary:hover {
        background: #084f9d !important;
        color: #fff !important;
    }

</style>

@section('content')

@php

use Carbon\Carbon;

$seller = auth()->guard('seller')->user();

$lastUpdate = $seller->updated_at
    ? Carbon::parse($seller->updated_at)
        ->timezone('Asia/Kolkata')
        ->format('d M Y, h:i A')
    : 'N/A';

$status = $seller->status ?? 'pending';

$contactPersons = $seller->contact_persons;

if(is_string($contactPersons)){
    $contactPersons = json_decode($contactPersons,true);
}

$contactPersons = $contactPersons ?? [];

$contactCount = count($contactPersons);

$totalRequired = 2;

@endphp

<div class="container-fluid py-4">

    {{-- BACK --}}
    <div class="d-flex top-back-heading-sec mb-4">

        <button onclick="window.location.href='{{ route('seller.agreement') }}'"
                class="nav-btn">

            <i class="fa-solid fa-arrow-left"></i>

        </button>

    </div>

    <div class="marketplace-wrapper">

        {{-- LEFT --}}
        <div class="marketplace-left">

            {{-- HEADER --}}
          

            {{-- SUMMARY --}}
            <div class="card shadow-sm border-0">

                <div class="card-body">

                    <h5 class="text-primary mb-3">
                        Logs Summary
                    </h5>

                    <div class="d-flex justify-content-between mb-2">

                        <span>
                            <strong>Last Update:</strong>
                        </span>

                        <span>
                            {{ $lastUpdate }}
                        </span>

                    </div>

                    <div class="d-flex justify-content-between">

                        <span>
                            <strong>Contact Persons:</strong>
                        </span>

                        <span>
                            {{ $contactCount }}/{{ $totalRequired }}
                        </span>

                    </div>

                    <div class="mt-2">

                        <span class="badge
                            {{ $status=='approved'
                                ? 'bg-success'
                                : ($status=='pending'
                                    ? 'bg-warning text-dark'
                                    : 'bg-danger') }}">

                            {{ ucfirst($status) }}

                        </span>

                    </div>

                </div>

            </div>

            {{-- TIMELINE --}}
            <div class="card shadow-sm border-0 mt-4">

                <div class="card-body">

                    <div class="timeline-header">

                        <h6 class="text-primary mb-3">
                            Activity Timeline
                        </h6>

                    </div>

                    @if(!empty($logs) && count($logs))

                        <div class="timeline scrollable-timeline">

                            @foreach($logs as $log)

                                @php

                                $time = Carbon::parse($log->created_at)
                                    ->timezone('Asia/Kolkata')
                                    ->format('d M Y, h:i A');

                                $old = $log->old_value
                                    ? json_decode($log->old_value,true)
                                    : [];

                                $new = $log->new_value
                                    ? json_decode($log->new_value,true)
                                    : [];

                                @endphp

                                <div class="timeline-item">

                                    <div class="timeline-dot"></div>

                                    <div class="timeline-content">

                                        <div class="d-flex justify-content-between mb-2">

                                            <strong>
                                                {{ ucfirst($log->module) }} Updated
                                            </strong>

                                            <small class="text-muted">
                                                {{ $time }}
                                            </small>

                                        </div>

                                        @foreach($new as $key => $value)

                                            @php

                                            $oldVal = $old[$key] ?? null;

                                            $newVal = $value;

                                            @endphp

                                            @if($oldVal != $newVal)

                                                <div class="change-row">

                                                    <span class="field">

                                                        {{ ucfirst(str_replace('_',' ',$key)) }}

                                                    </span>

                                                    <div class="values">

                                                        @php

                                                        $oldDisplay = is_array($oldVal)
                                                            ? json_encode($oldVal)
                                                            : $oldVal;

                                                        $newDisplay = is_array($newVal)
                                                            ? json_encode($newVal)
                                                            : $newVal;

                                                        @endphp

                                                        <span class="old">
                                                            {{ $oldDisplay ?? '-' }}
                                                        </span>

                                                        <i class="fa fa-arrow-right mx-2"></i>

                                                        <span class="new">
                                                            {{ $newDisplay ?? '-' }}
                                                        </span>

                                                    </div>

                                                </div>

                                            @endif

                                        @endforeach

                                    </div>

                                </div>

                            @endforeach

                        </div>

                    @else

                        <div class="alert alert-light">

                            No activity logs found

                        </div>

                    @endif

                </div>

            </div>

            {{-- BUTTON --}}
            <div class="text-end mt-4">

                <a href="{{ url('seller/dashboard') }}"
                   class="btn btn-success px-4">

                    <i class="fa fa-check me-2"></i>

                    Save & Continue

                </a>

            </div>

        </div>

        {{-- RIGHT --}}
        <div class="marketplace-right">

            @include('vshop.seller.partials.profile-progress')

        </div>

    </div>

</div>

@endsection