@extends('vshop.seller.main')

<style>
    .card {
        transition: 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .remove-btn {
        background: rgb(220 53 69) !important;
        color: #fff !important;
        margin-left: 5px !important;
    }
    .nav-btn {
        border: 1px solid #004392;
        color: #004392;
        background: transparent;
        border-radius: 5px;
        font-size: 4px;
        width: 35px;
        height: 38px;
    }
    .grivence-btn-sec {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .grivence-btn-sec .btn-primary {
        background: #004392;
        border-color: #004392;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .grivence-btn-sec .btn-primary :hover {
        background-color: #004392 !important;
        border-color: #004392 !important;
    }
    .grivence-btn-sec .btn-primary i {
        font-size: 16px;
    }
    .table th {
    font-weight: 600;
    color: #fff !important;
    background-color: #212529 !important;
    border: 1px solid #fff;
}
.table tbody td {
    border: 1px solid #dee2e6;
}
.table-btn-sec a,
.table-btn-sec button {
    border: 1px solid #dee2e6 !important;
    font-size: 14px;
    padding: 7px 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
}
.table-btn-sec a i,
.table-btn-sec button i{
    font-size: 12px;
}
.table-btn-sec {
    display: flex;
    align-items: center;
}
.nav-btn {
    border: 1px solid #004392;
    color: #004392;
    background: transparent;
    border-radius: 5px;
    font-size: 4px;
    width: 35px;
    height: 38px;
}
.top-back-heading-sec {
    align-items: center;
    gap: 10px;
}
.top-back-heading-sec h5 {
    color: #0f172a !important;
}
.table th {
    font-weight: 600;
    color: #fff !important;
    background-color: #004392 !important;
    border: 1px solid #fff;
}
.btn-dark {
    background: linear-gradient(180deg, rgba(44, 127, 195, 1) 0%, rgba(35, 61, 121, 1) 100%);
    border: 0 !important;
}
.remove-btn {
    background: #61b3e5 !important;
    color: #fff !important;
    margin-left: 5px;
}
.btn-outline-dark {
    border-color: #233d78 !important;
    color: #233d78 !important;
}
.btn-outline-dark:hover {
    background: #233d78 !important;
    color: #fff !important;
}
</style>

@section('content')

<div class="container-fluid py-4">

    {{-- HEADER --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex top-back-heading-sec">
            <button onclick="goBack()" class="nav-btn">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <div>
                <h5 class="mb-0">Grievance Management</h5>
                <small class="text-muted">Manage and track all your support tickets</small>
            </div>
        </div>
        
        <div class="grivence-btn-sec">
            <a href="{{ route('seller.grievance.create') }}" class="btn btn-primary shadow-sm">
                <i class="fa fa-plus"></i> Create Ticket
            </a>
        </div>
    </div>

    {{-- SUMMARY --}}
    <div class="row mb-4">

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <small class="text-muted">Open Tickets</small>
                <h5 class="text-danger mb-0">{{ $tickets->where('status','open')->count() }}</h5>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <small class="text-muted">In Progress</small>
                <h5 class="text-warning mb-0">{{ $tickets->where('status','in_progress')->count() }}</h5>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm p-3">
                <small class="text-muted">Resolved</small>
                <h5 class="text-success mb-0">{{ $tickets->where('status','resolved')->count() }}</h5>
            </div>
        </div>

    </div>

    {{-- FILTER --}}
    <div class="card border-0 shadow-sm mb-3 p-3">
        <form method="GET">
            <div class="row g-2">

                <div class="col-md-4">
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control" placeholder="Search by subject...">
                </div>

                <div class="col-md-3">
                    <select name="status" class="form-control">
                        <option value="">All Status</option>
                        <option value="open" {{ request('status')=='open'?'selected':'' }}>Open</option>
                        <option value="in_progress" {{ request('status')=='in_progress'?'selected':'' }}>In Progress</option>
                        <option value="resolved" {{ request('status')=='resolved'?'selected':'' }}>Resolved</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <select name="priority" class="form-control">
                        <option value="">All Priority</option>
                        <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
                        <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
                        <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <div class="d-flex">
                        <button class="btn btn-dark w-100">Filter</button>
                        <button type="submit" class="btn remove-btn btn-sm w-100">Reset</button>
                    </div>
                </div>

            </div>
        </form>
    </div>

    {{-- TABLE --}}
    <div class="card border-0 shadow-sm">

        <div class="table-responsive">
            <table class="table align-middle mb-0">

                <thead class="bg-light">
                    <tr>
                        <th>#ID</th>
                        <th>Subject</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Priority</th>
                        <th>Created</th>
                        <th>Assign To</th> {{-- NEW --}}
                        <th class="text-end">Action</th>
                    </tr>
                </thead>

                <tbody>

                @forelse($tickets as $t)

                    <tr>
                        <td>#{{ $t->id }}</td>

                        <td><strong>{{ $t->subject }}</strong></td>

                        <td>
                            <span class="badge bg-info">{{ ucfirst($t->type) }}</span>
                        </td>

                        <td>
                            <span class="badge bg-{{ 
                                $t->status == 'open' ? 'danger' :
                                ($t->status == 'in_progress' ? 'warning' :
                                ($t->status == 'resolved' ? 'success' : 'secondary'))
                            }}">
                                {{ ucfirst(str_replace('_',' ',$t->status)) }}
                            </span>
                        </td>

                        <td>
                            <span class="badge bg-{{ 
                                $t->priority == 'high' ? 'danger' :
                                ($t->priority == 'medium' ? 'warning' : 'secondary')
                            }}">
                                {{ ucfirst($t->priority) }}
                            </span>
                        </td>

                        <td>
                            <small class="text-muted">
                                {{ $t->created_at->format('d M Y') }}
                            </small>
                        </td>

                        {{-- ASSIGN TO --}}
                        <td>
                            <div style="font-size:13px; color:#e74a3b; line-height:1.4;">
                                {{ $t->assigned_to ?? ''  }}
                                
                            </div>
                        </td>

                        <td class="text-end">
                            <a href="{{ route('seller.grievance.view',$t->id) }}" 
                               class="btn btn-sm btn-outline-dark">
                                View
                            </a>
                        </td>
                    </tr>

                @empty

                    <tr>
                        <td colspan="8" class="text-center py-5">

                            <img src="https://cdn-icons-png.flaticon.com/512/4076/4076549.png" width="80">

                            <p class="mt-3 text-muted">No grievance tickets found</p>

                            <a href="{{ route('seller.grievance.create') }}" class="btn btn-primary btn-sm">
                                Create First Ticket
                            </a>

                        </td>
                    </tr>

                @endforelse

                </tbody>

            </table>
        </div>

    </div>

</div>

@endsection
