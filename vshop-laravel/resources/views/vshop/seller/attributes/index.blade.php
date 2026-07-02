@extends('vshop.seller.main', ['title' => 'Attributes'])

@section('content')
<div class="container-fluid">

<div class="row">

    {{-- ================= ADD ATTRIBUTE ================= --}}
    <div class="col-md-4">
        <div class="card">
            <div class="card-header"><h5>Add Attribute</h5></div>
            <div class="card-body">

                <form action="{{ route('seller.attributes.store') }}" method="POST">
                    @csrf

                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>

                    <button class="btn btn-primary w-100 mt-3">Create</button>
                </form>

                {{-- NOTE --}}
                <div class="alert alert-info mt-3">
                    Attribute will be sent for <strong>Admin Approval</strong>.
                </div>

            </div>
        </div>
    </div>


    {{-- ================= MANAGE ATTRIBUTES ================= --}}
    <div class="col-md-8">
        <div class="card">
            <div class="card-header"><h5>Manage Attributes</h5></div>

            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th>Name</th>
                        <th>Terms</th>
                        <th>Status</th>
                        <th width="180">Action</th>
                    </tr>

                    @forelse($attributes as $attr)
                    <tr>
                        <td>{{ $attr->name }}</td>

                        <td>
                            @if($attr->terms->isNotEmpty())
                                {{ $attr->terms->pluck('term')->implode(', ') }}
                            @else
                                -
                            @endif
                        </td>

                        {{-- STATUS BADGE --}}
                        <td>
                            @if($attr->status === 'approved')
                                <span class="badge bg-success">Approved</span>
                            @else
                                <span class="badge bg-warning text-dark">Pending</span>
                            @endif
                        </td>

                        <td>

                            {{-- TERMS CONFIGURE --}}
                            <a href="{{ route('seller.attributes.terms', $attr->id) }}"
                               class="btn btn-sm btn-info">
                                Configure
                            </a>

                            {{-- DELETE --}}
                            <form action="{{ route('seller.attributes.destroy', $attr->id) }}"
                                  method="POST"
                                  class="d-inline delete-form">
                                @csrf
                                @method('DELETE')

                                <button type="button" class="btn btn-sm btn-danger delete-btn">
                                    Delete
                                </button>
                            </form>

                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-3">
                            No attributes found.
                        </td>
                    </tr>
                    @endforelse

                </table>
            </div>
        </div>
    </div>

</div>
</div>


{{-- ================= SWEET ALERT DELETE ================= --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', function (event) {

            let form = event.target.closest('form');

            Swal.fire({
                title: "Are you sure?",
                text: "This attribute and its terms will be deleted!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    });

});
</script>

@endsection
