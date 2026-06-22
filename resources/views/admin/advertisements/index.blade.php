@extends('layouts.vertical', ['title' => 'Advertisement Management'])

@section('content')

<div class="container-fluid" style="padding-top: 15px">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">Advertisement Management</h4>

        <a href="{{ route('admin.ads.create') }}" class="btn btn-primary">
            + Add Advertisement
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
                            <th>Title</th>
                            <th>Type</th>
                            <th>Target</th>
                            <th>Media</th>
                            <th>Schedule</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ads as $key => $row)
                            <tr>

                                <td>{{ $key + 1 }}</td>

                                <td>
                                    <strong>{{ $row->title }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($row->description, 40) }}</small>
                                </td>

                                <td>
                                    <span class="badge bg-info text-uppercase">
                                        {{ $row->type }}
                                    </span>
                                </td>

                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $row->target_type }}
                                    </span>
                                </td>

                                {{-- MEDIA --}}
                                <td>
                                    @if($row->media->count())
                                        @foreach($row->media->take(3) as $m)

                                            @if($m->media_type == 'image')
                                                <img src="{{ asset('storage/'.$m->file_path) }}"
                                                     width="45" height="45"
                                                     style="object-fit:cover;border-radius:5px;">
                                            @else
                                                <span class="badge bg-dark">🎥 Video</span>
                                            @endif

                                        @endforeach

                                        @if($row->media->count() > 3)
                                            <span class="badge bg-light text-dark">
                                                +{{ $row->media->count() - 3 }}
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">No Media</span>
                                    @endif
                                </td>

                                {{-- DATE --}}
                                <td>
                                    <small>
                                        <strong>Start:</strong><br>
                                        {{ $row->start_date ?? '-' }}<br>

                                        <strong>End:</strong><br>
                                        {{ $row->end_date ?? '-' }}
                                    </small>
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    @if($row->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-danger">Inactive</span>
                                    @endif
                                </td>

                                {{-- ACTION --}}
                                <td>

                                    <a href="{{ route('admin.ads.edit',$row->id) }}"
                                       class="btn btn-sm btn-warning">
                                        Edit
                                    </a>

                                    <form action="{{ route('admin.ads.delete',$row->id) }}"
                                          method="POST"
                                          style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')

                                        <button type="submit"
                                                class="btn btn-sm btn-danger"
                                                onclick="return confirm('Delete this advertisement?')">
                                            Delete
                                        </button>
                                    </form>

                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted">
                                    No advertisements found
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