@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Available Pads</h2>
        <form method="GET" action="{{ route('tenant.pads.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search pads..."
                            value="{{ request('search') }}">
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
                    @foreach($pads->pluck('padLocation')->map(function($loc) {
                        $parts = explode(',', $loc);
                        return isset($parts[2]) ? trim($parts[2]) : trim($loc);
                    })->unique()->sort() as $city)
                        <option value="{{ $city }}" {{ request('location_filter') == $city ? 'selected' : '' }}>{{ $city }}</option>
                    @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="price_filter" class="form-select" onchange="this.form.submit()">
                        <option value="">All Prices</option>
                        <option value="below_1000" {{ request('price_filter') == 'below_1000' ? 'selected' : '' }}>Below
                            ₱1,000</option>
                        <option value="1000_2000" {{ request('price_filter') == '1000_2000' ? 'selected' : '' }}>₱1,000 -
                            ₱2,000</option>
                        <option value="2000_3000" {{ request('price_filter') == '2000_3000' ? 'selected' : '' }}>₱2,000 -
                            ₱3,000</option>
                        <option value="above_3000" {{ request('price_filter') == 'above_3000' ? 'selected' : '' }}>Above
                            ₱3,000</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <a href="{{ route('tenant.pads.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                    @if($pads->isEmpty())
                        <div class="col-12">
                            <div class="alert alert-info text-center">No pads found.</div>
                        </div>
                    @endif
                </div>
            </div>
        </form>

        <div class="row">
            @foreach($pads as $pad)
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <a href="{{ route('tenant.pads.show', $pad->padID) }}" style="text-decoration: none; color: inherit;">
                            @if($pad->padImage)
                                <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top pad-img"
                                    alt="{{ $pad->padName }}">
                            @else
                                <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top pad-img"
                                    alt="No Image">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $pad->padName }}</h5>
                                <p class="card-text">{{ $pad->padDescription }}</p>
                                <p class="card-text"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                                <p class="card-text"><strong>Rent:</strong> ₱{{ number_format($pad->padRent, 2) }}</p>
                                <p class="card-text"><strong>Landlord:</strong> {{ $pad->landlord->first_name ?? 'N/A' }}
                                    {{ $pad->landlord->last_name ?? '' }}</p>
                                @php
                                    $statusDisplay = [
                                        'Available' => 'Available',
                                        'Fullyoccupied' => 'Fully Occupied',
                                        'Maintenance' => 'Maintenance'
                                    ];
                                @endphp
                                <p class="card-text"><strong>Status:</strong> {{ $statusDisplay[$pad->padStatus] ?? $pad->padStatus }}</p>
                            </div>
                        </a>
                    </div>

                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $pads->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>
@endsection