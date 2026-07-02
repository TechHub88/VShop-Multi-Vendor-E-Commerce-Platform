@extends('layouts.vertical', ['title' => 'Categories'])

@section('content')
    <div class="container-fluid">
        <h1>Export Enquiries</h1>
        
        <p>Select the format to export the enquiries:</p>

        <form action="{{ url('admin.enquiries.export') }}" method="GET">
            <div class="form-group">
                <label for="format">Export Format:</label>
                <select name="format" id="format" class="form-control">
                    <option value="csv">CSV</option>
                    <option value="excel">Excel</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Export</button>
        </form>
    </div>
@endsection
