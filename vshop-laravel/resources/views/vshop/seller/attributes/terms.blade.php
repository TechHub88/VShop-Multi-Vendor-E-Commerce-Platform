@extends('vshop.seller.main', ['title' => 'Configure Terms'])

@section('content')
<div class="container-fluid">

    <h4>Attribute: {{ $attribute->name }}</h4>

    {{-- ================= ADD NEW TERM ================= --}}
    <form action="{{ route('seller.attributes.terms.store', $attribute->id) }}"
          method="POST"
          class="d-flex mb-3">
        @csrf

        <input type="text"
               name="term"
               class="form-control me-2"
               placeholder="Enter term (Ex: XL, Blue)"
               required>

        <button class="btn btn-primary">Add</button>
    </form>

    <div class="alert alert-info">
        New terms will be sent for <strong>Admin Approval</strong>.
    </div>


    {{-- ================= TERMS LIST ================= --}}
    @if($attribute->terms->isNotEmpty())
        <ul class="list-group">

            @foreach($attribute->terms as $term)
                <li class="list-group-item d-flex justify-content-between align-items-center">

                    <div>
                        {{ $term->term }}

                        {{-- STATUS BADGE --}}
                        @if($term->status === 'approved')
                            <span class="badge bg-success ms-2">Approved</span>
                        @else
                            <span class="badge bg-warning text-dark ms-2">Pending</span>
                        @endif
                    </div>

                    <div class="d-flex gap-2">

                        {{-- DELETE --}}
                        <form action="{{ route('seller.attributes.terms.destroy', [$attribute->id, $term->id]) }}"
                              method="POST"
                              onsubmit="return confirm('Are you sure you want to delete this term?');">
                            @csrf
                            @method('DELETE')

                            <button type="submit" class="btn btn-danger btn-sm">
                                Delete
                            </button>
                        </form>

                    </div>
                </li>
            @endforeach

        </ul>
    @else
        <p>No terms added yet.</p>
    @endif

</div>
@endsection
