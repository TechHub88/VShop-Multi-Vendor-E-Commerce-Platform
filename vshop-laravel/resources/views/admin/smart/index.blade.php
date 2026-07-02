@extends('layouts.vertical', ['title' => 'Smart Queries'])

@section('content')

<div class="container-fluid">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Smart Queries</h4>

        <a href="{{ route('admin.smart.create') }}" class="btn btn-primary">
            + Add Question
        </a>
    </div>

    {{-- SUCCESS MESSAGE --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- TABLE --}}
    <div class="card">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Question</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($questions as $key => $q)
                            <tr>

                                <td>{{ $key + 1 }}</td>

                                <td>
                                    <strong>{{ $q->question }}</strong>
                                </td>

                                <td>
                                    <span class="badge bg-info text-uppercase">
                                        {{ $q->type }}
                                    </span>
                                </td>

                                <td>
                                    @if($q->status)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>

                                <td>
                                    <a href="{{ route('admin.smart.edit',$q->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.smart.delete',$q->id) }}"
                                          method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')

                                        <button class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this question?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No questions found
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

@endsection