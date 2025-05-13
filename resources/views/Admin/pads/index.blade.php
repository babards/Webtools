@extends('layouts.app')

@section('content')
<div class="container">
    <h2>All Pads (Admin View)</h2>
    
    <form method="GET" action="{{ route('admin.pads.index') }}" class="mb-3">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search pads..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>

    <div class="row">
        @foreach($pads as $pad)
            <div class="col-md-4 mb-4">
                <div class="card h-100">
                    @if($pad->padImage)
                        <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top" alt="{{ $pad->padName }}">
                    @else
                        <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top" alt="No Image">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $pad->padName }}</h5>
                        <p class="card-text">{{ $pad->padDescription }}</p>
                        <p class="card-text"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                        <p class="card-text"><strong>Rent:</strong> ₱{{ number_format($pad->padRent, 2) }}</p>
                        <p class="card-text"><strong>Status:</strong> {{ ucfirst($pad->padStatus) }}</p>
                        <p class="card-text"><strong>Landlord:</strong> {{ $pad->landlord->first_name }} {{ $pad->landlord->last_name }}</p>
                    </div>
                    <div class="card-footer">
                        <a href="{{ route('pads.show', $pad->padID) }}" class="btn btn-primary">View Details</a>
                        <button class="btn btn-warning editPadBtn" data-id="{{ $pad->padID }}">Edit</button>
                        <button class="btn btn-danger deletePadBtn" data-id="{{ $pad->padID }}">Delete</button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $pads->links() }}
</div>
@endsection
