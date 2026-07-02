@extends('layouts.vertical', ['title' => 'Categories'])

@section('content')
    <div class="container-fluid">
        <h1>Assign Enquiry to Sales Rep</h1>

        <!-- Dummy Enquiry Data -->
        <p><strong>Enquiry from:</strong> Mark Lee (marklee@example.com)</p>

        <form action="{{ route('admin.enquiries.assign', 3) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="sales_rep_id">Assign to Sales Rep:</label>
                <select name="sales_rep_id" id="sales_rep_id" class="form-control">
                    <!-- Dummy Sales Reps -->
                    <option value="1">Mike Johnson</option>
                    <option value="2">Sarah Connor</option>
                    <option value="3">David Smith</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Assign</button>
        </form>
    </div>
@endsection
