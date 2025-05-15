@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Available Pads</h2>

    <form method="GET" action="{{ route('tenant.pads.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search pads..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <div class="row">
        @foreach($pads as $pad)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    <a href="{{ route('tenant.pads.show', $pad->padID) }}" style="text-decoration: none; color: inherit;">
                        @if($pad->padImage)
                            <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top pad-img" alt="{{ $pad->padName }}">
                        @else
                            <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top pad-img" alt="No Image">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $pad->padName }}</h5>
                            <p class="card-text">{{ $pad->padDescription }}</p>
                            <p class="card-text"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                            <p class="card-text"><strong>Rent:</strong> ₱{{ number_format($pad->padRent, 2) }}</p>
                            <p class="card-text"><strong>Landlord:</strong> {{ $pad->landlord->first_name ?? 'N/A' }} {{ $pad->landlord->last_name ?? '' }}</p>
                            <p class="card-text"><strong>Number of Boarders:</strong> {{ $pad->number_of_boarders ?? 0 }}</p>
                        </div>
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    {{ $pads->links() }}
</div>
@endsection
