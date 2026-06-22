@extends('layouts.vertical', ['title' => 'Notification Settings'])

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h5>System Notification Settings</h5>
        </div>
        <div class="card-body">
            <!-- Form to update notification settings -->
            <form action="{{ route('admin.settings.notifications.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-12">
                    <label class="form-label">Select Notification Settings</label>
                    <table class="table table-striped" id="notificationTable">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Notification Type</th>
                                <th>Email</th>
                                <th>SMS</th>
                                <th>Web</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($settings as $setting)
                                <tr>
                                    <td>{{ $setting->id }}</td>
                                    <td>{{ $setting->message }}</td>
                                    <td>
                                        <input type="checkbox" name="notify_email[{{ $setting->key }}]" 
                                               value="1" {{ $setting->notify_email ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="notify_sms[{{ $setting->key }}]" 
                                               value="1" {{ $setting->notify_sms ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <input type="checkbox" name="notify_web[{{ $setting->key }}]" 
                                               value="1" {{ $setting->notify_web ? 'checked' : '' }}>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.settings.notifications.edit', $setting->id) }}" class="btn btn-warning">Edit</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="col-12 text-end mt-3">
                    <button type="submit" class="btn btn-primary">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

</div>

<!-- Initialize DataTable -->
<script>
    $(document).ready(function() {
        $('#notificationTable').DataTable({
            "paging": true,       // Enables pagination
            "searching": true,    // Enables search
            "ordering": true,     // Enables sorting
            "info": true,         // Enables info display (like "Showing 1 to 10 of 25 entries")
        });
    });
</script>

@endsection
