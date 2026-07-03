@extends('layouts.vertical', ['title' => 'Add Role & Permissions'])

@section('content')
<div class="container-fluid">

    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Roles permissions form</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.roles.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" name="name" class="form-control" placeholder="Role name" required>
                </div>

                <h5 class="mb-2">Permissions</h5>

                <div class="mb-2">
                    <label><input type="checkbox" id="checkAll"> All</label>
                </div>

                <div class="row">
                    @foreach($permissions as $group => $perms)
                        <div class="col-md-3 border rounded p-2 mb-3">
                            <strong>{{ ucfirst($group) }}</strong><br>
                            @foreach($perms as $permission)
                                <label class="d-block">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->name }}">
                                    {{ ucwords(str_replace('-', ' ', $permission->name)) }}
                                </label>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="text-end mt-3">
                    <button type="submit" name="save_and_back" class="btn btn-primary">Save & Exit</button>
                    <button type="submit" class="btn btn-success">Save</button>
                </div>

            </form>
        </div>
    </div>

</div>

<script>
document.getElementById('checkAll').onclick = function() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = this.checked);
}
</script>

@endsection
