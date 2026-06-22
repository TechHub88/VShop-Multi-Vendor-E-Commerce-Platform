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
                            <th>Title & Description</th>
                            <th>Type</th>
                            <th>Target</th>
                            <th>Category</th>      {{-- NEW --}}
                            <th>Position</th>
                            <th>Media</th>
                            <th>Schedule</th>
                            <th>Clicks / Impressions</th>
                            <th>Status</th>
                            <th width="150">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse($ads as $key => $row)
                            <tr>
                                {{-- Serial Number --}}
                                <td>{{ $key + 1 }}</td>

                                {{-- Title & Description --}}
                                <td>
                                    <strong>{{ $row->title }}</strong><br>
                                    @if($row->description)
                                        <small class="text-muted">{{ Str::limit($row->description, 50) }}</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>

                                {{-- Type --}}
                                <td>
                                    <span class="badge bg-info text-uppercase">
                                        {{ $row->type ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Target Type --}}
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ $row->target_type ?? 'N/A' }}
                                    </span>
                                </td>

                                {{-- Category (NEW) --}}
                                <td>
                                    <span class="badge bg-primary">
                                        {{ $row->category_name ?? '—' }}
                                    </span>
                                </td>

                                {{-- Position --}}
                                <td>
                                    <span class="badge bg-dark">
                                        {{ $row->position ?? '—' }}
                                    </span>
                                </td>

                                {{-- MEDIA (Images / Videos) --}}
                                  <td>
                                    @if($row->media->count())
                                        @php
                                            $first = $row->media->first();
                                        @endphp

                                        @if($first->media_type == 'image')
                                            {{-- Check if file exists in public directory --}}
                                            @php
                                                $publicPath = public_path($first->file_path);
                                                $exists = file_exists($publicPath);
                                            @endphp

                                            @if($exists)
                                                <img src="{{ asset($first->file_path) }}"
                                                     width="50" height="50"
                                                     style="object-fit:cover; border-radius:5px; border:1px solid #ddd;"
                                                     alt="Media">
                                            @else
                                                <span class="badge bg-warning text-dark">Image Missing</span>
                                            @endif
                                        @else
                                            <span class="badge bg-dark">ðŸŽ¥ Video</span>
                                        @endif

                                        {{-- Show count of extra media --}}
                                        @if($row->media->count() > 1)
                                            <span class="badge bg-light text-dark ms-1">
                                                +{{ $row->media->count() - 1 }} more
                                            </span>
                                        @endif
                                    @else
                                        <span class="text-muted">No Media</span>
                                    @endif
                                </td>

                                {{-- SCHEDULE --}}
                                <td>
                                    <small>
                                        <strong>Start:</strong><br>
                                        {{ $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('d M Y H:i') : '-' }}
                                        <br>
                                        <strong>End:</strong><br>
                                        {{ $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('d M Y H:i') : '-' }}
                                    </small>
                                </td>

                                {{-- Clicks & Impressions --}}
                                <td>
                                    <span class="badge bg-primary">Clicks: {{ $row->clicks_count ?? 0 }}</span><br>
                                    <span class="badge bg-secondary">Impressions: {{ $row->impressions_count ?? 0 }}</span>
                                </td>

                                {{-- STATUS --}}
                                <td>
                                    @if($row->is_active)
                                        <span class="badge bg-success status-badge-{{ $row->id }}">Active</span>
                                    @else
                                        <span class="badge bg-danger status-badge-{{ $row->id }}">Inactive</span>
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $row->status ?? '' }}</small>
                                </td>

                                {{-- ACTION BUTTONS --}}
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="form-check form-switch mb-0" style="padding-left: 2.5em;">
                                            <input class="form-check-input status-toggle" type="checkbox" role="switch" data-id="{{ $row->id }}" {{ $row->is_active ? 'checked' : '' }} title="Toggle Active Status" style="cursor: pointer; margin-left: -2.5em;">
                                        </div>

                                        <a href="{{ route('admin.ads.edit', $row->id) }}"
                                           class="btn btn-sm btn-warning text-white me-1">
                                            Edit
                                        </a>

                                        <form action="{{ route('admin.ads.delete', $row->id) }}"
                                              method="POST"
                                              class="m-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Delete this advertisement?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    No advertisements found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>
        </div>
    </div>

</div>

<script>
    document.querySelectorAll('.status-toggle').forEach(function(toggle) {
        toggle.addEventListener('change', function() {
            let status = this.checked ? 1 : 0;
            let id = this.getAttribute('data-id');

            fetch('{{ route('admin.ads.toggle-status') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id, status: status })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    let badge = document.querySelector('.status-badge-' + id);
                    if (badge) {
                        if (status) {
                            badge.className = 'badge bg-success status-badge-' + id;
                            badge.innerText = 'Active';
                        } else {
                            badge.className = 'badge bg-danger status-badge-' + id;
                            badge.innerText = 'Inactive';
                        }
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
</script>

@endsection