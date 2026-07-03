@extends('vshop.seller.main')

@section('content')

<div class="profile-wrapper">

    {{-- LEFT SIDE --}}
    <div class="profile-left">

        {{-- HEADER --}}
        <div class="profile-header mb-4">
            <h4>Seller Profile Setup</h4>

            <div class="breadcrumb-box">
                Seller Profile / Organization
            </div>
        </div>


        {{-- TABS --}}
        <ul class="nav profile-tabs">

            <li>
                <a class="active" data-bs-toggle="tab" href="#org">
                    Organization
                </a>
            </li>

            <li>
                <a data-bs-toggle="tab" href="#contact">
                    Contact
                </a>
            </li>

            <li>
                <a data-bs-toggle="tab" href="#bank">
                    Banking
                </a>
            </li>

            <li>
                <a data-bs-toggle="tab" href="#payment">
                    Payment Gateway
                </a>
            </li>

            <li>
                <a data-bs-toggle="tab" href="#agreement">
                    Agreement
                </a>
            </li>

        </ul>


        {{-- TAB CONTENT --}}
        <div class="tab-content profile-card mt-4">

            @include('vshop.seller.profile.organization')
            @include('vshop.seller.profile.contact')
            @include('vshop.seller.profile.bank')
            @include('vshop.seller.profile.payment')
            @include('vshop.seller.profile.agreement')

        </div>

    </div>


    {{-- RIGHT SIDE PROGRESS --}}
    <div class="profile-right">

        @include('vshop.seller.partials.profile-progress')

    </div>

</div>
<style>
    .profile-wrapper{
display:flex;
gap:25px;
align-items:flex-start;
}

/* LEFT FORM */
.profile-left{
flex:1;
min-width:0;
}

/* RIGHT PROGRESS */
.profile-right{
width:280px;
position:sticky;
top:80px;
}

/* Prevent form stretching */
.tab-content{
width:100%;
}
</style>

@endsection
