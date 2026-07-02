@extends('vtravel.main')

@section('title', 'My Profile')

@section('content')

<div class="dashboardPagesContent">
    <div class="dashboardPagesContentonner dashboardPosPagesLeftContent">
        <div class="dashboardPosPagesLeftContentonner">

            <div class="dashboardDtlsArea">
                <div class="dashboardDtlsAreaBody">

                    {{-- Breadcrumb --}}
                    <div class="bed-cum-sec justify-content-between">
                        <div class="bedcum-left">
                            <button class="back-btn" onclick="history.back()">
                                <i class="fa-solid fa-arrow-left"></i>
                            </button>
                            <ul>
                                <li><a href="{{ route('vtravelhome') }}">Home</a> / </li>
                                <li><a href="#">My Profile</a></li>
                            </ul>
                        </div>
                    </div>

                    {{-- Profile Section --}}
                    <div class="address-edit-sec">
                        <div class="address-edit-wrapper">

                            <div class="address-edit-header">
                                <h2>My Profile</h2>
                            </div>

                            <div class="address-edit-bottom">
                                <form method="POST" action="#" enctype="multipart/form-data">
                                    @csrf

                                    <div class="address-edit-bottom-inner">
                                        <div class="row">

                                            {{-- Full Name --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>Full Name</label>
                                                    <input type="text"
                                                           name="name"
                                                           value="{{ Auth::guard('customer')->user()->name }}"
                                                           placeholder="Enter your name">
                                                </div>
                                            </div>

                                            {{-- Email --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>Email Address</label>
                                                    <input type="email"
                                                           name="email"
                                                           value="{{ Auth::guard('customer')->user()->email }}"
                                                           placeholder="Enter email address"
                                                           readonly>
                                                </div>
                                            </div>

                                            {{-- Phone --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>Contact Number</label>
                                                    <input type="tel"
                                                           name="phone"
                                                           value="{{ Auth::guard('customer')->user()->phone }}"
                                                           placeholder="Enter number">
                                                </div>
                                            </div>

                                            {{-- Profile Picture --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>Profile Picture</label>
                                                    <input type="file" name="profile_picture">
                                                </div>
                                            </div>

                                            {{-- GSTIN --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>GSTIN Number</label>
                                                    <input type="text"
                                                           name="gstin"
                                                           value="{{ Auth::guard('customer')->user()->gstin }}"
                                                           placeholder="Enter GSTIN number">
                                                </div>
                                            </div>

                                            {{-- Country --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>Country</label>
                                                    <select name="country" class="form-select">
                                                        <option value="">Select Country</option>
                                                        <option value="India" selected>India</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- State --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>State</label>
                                                    <select name="state" class="form-select">
                                                        <option value="">Select State</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- City --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>City / District</label>
                                                    <select name="city" class="form-select">
                                                        <option value="">Select City / District</option>
                                                    </select>
                                                </div>
                                            </div>

                                            {{-- Address --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>Street / Address</label>
                                                    <input type="text"
                                                           name="address_line1"
                                                           value="{{ Auth::guard('customer')->user()->address_line1 }}"
                                                           placeholder="Enter address">
                                                </div>
                                            </div>

                                            {{-- Pincode --}}
                                            <div class="col-lg-6">
                                                <div class="address-input-box">
                                                    <label>Pincode</label>
                                                    <input type="text"
                                                           name="pincode"
                                                           value="{{ Auth::guard('customer')->user()->pincode }}"
                                                           placeholder="Enter pincode">
                                                </div>
                                            </div>

                                            {{-- Buttons --}}
                                            <div class="col-lg-12">
                                                <div class="address-btn-sec">
                                                    <button type="submit" class="submit-btn">
                                                        Update Profile
                                                    </button>
                                                    <button type="reset" class="reset-btn">
                                                        Reset
                                                    </button>
                                                    <a href="{{ route('vtravelhome') }}" class="reset-btn">
                                                        Back
                                                    </a>
                                                </div>
                                            </div>

                                        </div>
                                    </div>

                                </form>
                            </div>

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@endsection
