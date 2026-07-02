        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Message</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slowProducts as $item)
                        <tr>
                            <td>{{ $item->name ?? '-' }}</td>
                            <td>{{ $item->comment ?? '-' }}</td>
                            <td>{{ $item->created_at ? $item->created_at->format('d M Y') : '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">No slow movers data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($slowProducts, 'links') && $slowProducts->hasPages())
        <div class="card-footer bg-transparent">
            {{ $slowProducts->appends(request()->except(['slow_filter', 'page']))->links('pagination::bootstrap-5') }}
        </div>
        @endif
