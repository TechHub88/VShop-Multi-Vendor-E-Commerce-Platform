@extends('layouts.vertical', ['title' => 'Coupons Management'])

@section('content')

@section('content')
<div class="container-fluid pt-3">
    <div class="d-flex justify-content-between mb-3">
        <h4>Coupon List</h4>
        <a href="#" class="btn btn-primary">Add New Coupon</a>
    </div>

    <div class="card">
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Coupon Code</th>
                        <th>Discount</th>
                        <th>Expiry Date</th>
                        <th>Status</th>
                        <th width="120">Action</th>
                    </tr>
                </thead>
                <tbody>
                <tr>
                    <td>WELCOME50</td>
                    <td>50%</td>
                    <td>31-Dec-2025</td>
                    <td><span class="badge bg-success">Active</span></td>
                    <td>
                        <a class="btn btn-sm btn-info">Edit</a>
                        <a class="btn btn-sm btn-danger">Delete</a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
