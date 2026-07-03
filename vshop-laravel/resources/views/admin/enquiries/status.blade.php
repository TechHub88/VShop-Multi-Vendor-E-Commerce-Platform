@extends('layouts.vertical', ['title' => 'Categories'])

@section('content')
    <div class="container-fluid">
        <h1>Change Status of Enquiry</h1>

        <!-- Dummy Enquiry Data -->
        <p><strong>Enquiry from:</strong> John Doe (johndoe@example.com)</p>

        <form action="{{ route('admin.enquiries.status', 1) }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="status">Select Status:</label>
                <select name="status" id="status" class="form-control">
                    <!-- Dummy Statuses -->
                    <option value="new" selected>New</option>
                    <option value="contacted">Contacted</option>
                    <option value="quoted">Quoted</option>
                    <option value="closed">Closed</option>
                </select>
            </div>

            <button type="submit" class="btn btn-success">Update Status</button>
        </form>
    </div>
@endsection
