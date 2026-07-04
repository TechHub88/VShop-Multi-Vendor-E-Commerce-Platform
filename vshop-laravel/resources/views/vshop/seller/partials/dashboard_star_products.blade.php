        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Sold</th>
                        <th>Rating</th>
                        <th>Complaints</th>
                    </tr>
                </thead>
                <tbody id="starTable">
                    @forelse($starProducts as $p)
                        <tr>
                            <td>{{$p->name}}</td>
                            <td>{{$p->total_sold ?? 0}}</td>
                            <td>{{round($p->reviews_avg_rating,1) ?? 0}}</td>
                            <td>{{$p->complaints_count ?? 0}}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No star products data available</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if(method_exists($starProducts, 'links') && $starProducts->hasPages())
        <div class="card-footer bg-transparent">
            {{ $starProducts->appends(request()->except(['star_filter', 'page']))->links('pagination::bootstrap-5') }}
        </div>
        @endif
