@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Available Pads</h2>

    <form method="GET" action="{{ route('tenant.pads.index') }}" class="mb-3">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search pads..." value="{{ request('search') }}">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </div>
            <div class="col-md-3">
                <select name="landlord_filter" class="form-select" onchange="this.form.submit()">
                    <option value="">All Landlords</option>
                    @foreach($pads->pluck('landlord')->unique('id') as $landlord)
                        @if($landlord)
                            <option value="{{ $landlord->id }}" {{ request('landlord_filter') == $landlord->id ? 'selected' : '' }}>{{ $landlord->first_name }} {{ $landlord->last_name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="location_filter" class="form-select" onchange="this.form.submit()">
                    <option value="">All Locations</option>
                    @foreach($pads->pluck('padLocation')->unique() as $location)
                        <option value="{{ $location }}" {{ request('location_filter') == $location ? 'selected' : '' }}>{{ $location }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="price_filter" class="form-select" onchange="this.form.submit()">
                    <option value="">All Prices</option>
                    <option value="below_1000" {{ request('price_filter') == 'below_1000' ? 'selected' : '' }}>Below ₱1,000</option>
                    <option value="1000_2000" {{ request('price_filter') == '1000_2000' ? 'selected' : '' }}>₱1,000 - ₱2,000</option>
                    <option value="2000_3000" {{ request('price_filter') == '2000_3000' ? 'selected' : '' }}>₱2,000 - ₱3,000</option>
                    <option value="above_3000" {{ request('price_filter') == 'above_3000' ? 'selected' : '' }}>Above ₱3,000</option>
                </select>
            </div>
            <div class="col-md-1">
                <a href="{{ route('tenant.pads.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
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
