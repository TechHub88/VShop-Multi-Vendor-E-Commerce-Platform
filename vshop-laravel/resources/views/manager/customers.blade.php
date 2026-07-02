
@extends('manager.layout')
@section('content')
<div class="adminPagesBodySec">
<div class="container-fluid">		
<div class="adminPagesBodySecinner">
		<div class="row rowBox">		
		<div class="col-lg-12 col-md-12 col-sm-12 col-12 columnBox">
		<div class="dashboard_card">
		<div class="d-flex justify-content-between align-items-center dashboard_card_header">
		<div class="leftPart">
		<h4 class="mb-0 dashboard_card_title ">All Employee</h4>
		</div>
		<div class="rightPart">
		<div class="dropdown">
		  <button type="button" class="btn btn-defalt dropdown-toggle" data-bs-toggle="dropdown">
			This Month
		  </button>
		  <ul class="dropdown-menu">
			<li><a class="dropdown-item" href="#">Download</a></li>
			<li><a class="dropdown-item" href="#">Export</a></li>
			<li><a class="dropdown-item" href="#">Import</a></li>
		  </ul>
		</div>
		</div>
		</div>
		<div class="dashboard_card_body p-0">
		<div class="pt-15 pl-15 pr-15 pb-0 employeeUploadArea">
			<form action="{{ route('manager.customers.import') }}" method="POST" enctype="multipart/form-data" style="margin-bottom: 20px;">
			@csrf
			<label><strong>Import Employee Excel</strong></label><br>
			<input type="file" name="import_file" required>			
			<button type="submit" style="background:#2f3640;color:#fff;padding:6px 12px;border:none;border-radius:5px;cursor:pointer;">
				Upload
			</button>
			<a href="{{ route('manager.customers.sample') }}" 
		   style="background:#00a8ff;color:#fff;padding:6px 12px;border:none;border-radius:5px;cursor:pointer;margin-left:15px;text-decoration:none;">
		   Download Sample Excel
			</a>
			</form>
		</div>
		
		
		<div class="table-responsive">
		<table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Status</th>
                <th>Joined On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($customers as $index => $customer)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $customer->name }}</td>
                    <td>{{ $customer->email }}</td>
                    <td>{{ $customer->phone }}</td>
                    <td id="status-{{ $customer->id }}">{{ ucfirst($customer->status) }}</td>
                    <td>{{ $customer->created_at->format('Y-m-d') }}</td>
                    <td>
                        @if($customer->status == 'pending')
                          <button class="btn btn-primary btn-approve" data-id="{{ $customer->id }}" style="padding:4px 12px;border-radius: 5px;">Approve</button>

                        @else
                            <span>Approved</span>
                        @endif
                    </td>
                </tr>
            @endforeach
            @if($customers->isEmpty())
                <tr>
                    <td colspan="7" style="text-align:center;">No customers found.</td>
                </tr>
            @endif
        </tbody>
    </table>
		</div>
		</div>
		
		
		</div>
		</div>
		</div>

</div>
</div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function(){
  
    $('.btn-approve').click(function(){
        var customerId = $(this).data('id'); // Get the customer ID
        var button = $(this);

        $.ajax({
            url: '{{ url("manager/customers/approve") }}/' + customerId, // Use Laravel's url() helper to generate the base URL
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}', // CSRF token to prevent CSRF attacks
            },
            success: function(response){
                console.log(response)
                if(response.success){
                    $('#status-' + customerId).text('Approved'); // Update the status text
                    button.replaceWith('<span>Approved</span>'); // Replace the button with 'Approved' text
                }
            },
            error: function(){
                alert('Something went wrong!');
            }
        });
    });
});
</script>
<script>
$(document).ready(function() {
    $('#ordersTable').DataTable({
        "order": [[1, "desc"]],
        "columnDefs": [
            { "orderable": false, "targets": 6 }
        ]
    });
});
</script>

@endsection	


