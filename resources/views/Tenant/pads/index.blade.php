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
                        <option value="">All Locations</option>
                        @foreach(config('app.valencia_barangays') as $barangay)
                            <option value="{{ $barangay }}" {{ request('location_filter') == $barangay ? 'selected' : '' }}>{{ $barangay }}</option>
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
                </div>
            </div>
        </form>

        @if($pads->isEmpty())
            <div class="alert alert-info text-center">
                <i class="fas fa-info-circle me-2"></i>No pads found matching your criteria.
            </div>
        @endif

        <div class="row">
            @foreach($pads as $pad)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('tenant.pads.show', $pad->padID) }}" style="text-decoration: none; color: inherit;">
                            @if($pad->main_image)
                                <img src="{{ asset('storage/' . $pad->main_image) }}" class="card-img-top pad-img"
                                    alt="{{ $pad->padName }}">
                            @else
                                <img src="https://via.placeholder.com/300x200?text=No+Image" class="card-img-top pad-img"
                                    alt="No Image">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $pad->padName }}</h5>
                                <p class="card-text">{{ Str::limit($pad->padDescription, 80) }}</p>
                                <p class="card-text mb-1"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                                <p class="card-text mb-1"><strong>Rent:</strong> ₱{{ number_format($pad->padRent, 2) }}</p>
                                <p class="card-text mb-1"><strong>Landlord:</strong> {{ $pad->landlord->first_name ?? 'N/A' }}
                                    {{ $pad->landlord->last_name ?? '' }}</p>
                                @php
                                    $statusDisplay = [
                                        'Available' => 'Available',
                                        'Fullyoccupied' => 'Fully Occupied',
                                        'Maintenance' => 'Maintenance'
                                    ];
                                @endphp
                                <p class="card-text mb-1"><strong>Status:</strong> {{ $statusDisplay[$pad->padStatus] ?? $pad->padStatus }}</p>
                            </div>
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-3">
            {{ $pads->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>

        <!-- Map Section -->
        <div class="mt-5">
            <h3 class="text-center mb-4">Available Pads Map</h3>
            <div id="padsMap" style="height: 500px; width: 100%; border-radius: 10px;"></div>
        </div>
    </div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    .pad-img {
        height: 200px;
        object-fit: cover;
        border-top-left-radius: 0.5rem;
        border-top-right-radius: 0.5rem;
    }
    #padsMap {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .leaflet-popup-content {
        margin: 10px;
    }
    .leaflet-popup-content h5 {
        margin-bottom: 5px;
    }
    .leaflet-popup-content p {
        margin-bottom: 5px;
    }
    .card {
        transition: transform 0.2s ease-in-out;
    }
    .card:hover {
        transform: translateY(-2px);
    }
    .leaflet-control-zoom-reset {
        font-size: 18px;
        line-height: 26px;
        text-align: center;
        text-decoration: none;
        color: black;
        display: block;
        width: 26px;
        height: 26px;
    }
    .leaflet-control-zoom-reset:hover {
        background-color: #f4f4f4;
        color: black;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map automatically
    const map = L.map('padsMap', {
            zoomControl: true,
            zoomControlOptions: {
                position: 'topright'
            }
        }).setView([7.9042, 125.0928], 15);

        // Add the tile layer (OpenStreetMap)
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Add custom reset control
        L.Control.ResetView = L.Control.extend({
            onAdd: function(map) {
                const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control');
                const link = L.DomUtil.create('a', 'leaflet-control-zoom-reset', container);
                link.innerHTML = '⌂';
                link.href = '#';
                link.title = 'Reset View';
                link.setAttribute('role', 'button');
                link.setAttribute('aria-label', 'Reset View');
                
                L.DomEvent.on(link, 'click', function(e) {
                    L.DomEvent.preventDefault(e);
                    map.setView([7.9042, 125.0928], 15);
                });
                
                return container;
            },
            onRemove: function(map) {}
        });

        // Add reset control to map
        new L.Control.ResetView({ position: 'topleft' }).addTo(map);

        // Add geocoder control for searching locations
        L.Control.geocoder({
            defaultMarkGeocode: false,
            position: 'topright'
        })
                            .on('markgeocode', function (e) {
                    const center = e.geocode.center;
                    map.setView(center, 15);
                })
            .addTo(map);

        // Add markers for each pad
        @foreach($pads as $pad)
            @if($pad->latitude && $pad->longitude)
                const marker{{ $pad->padID }} = L.marker([{{ $pad->latitude }}, {{ $pad->longitude }}]).addTo(map);
                const popupContent{{ $pad->padID }} = `
                    <div style="min-width: 200px;">
                        <h5 style="margin-bottom: 8px; color: #333;">{{ $pad->padName }}</h5>
                        <p style="margin-bottom: 5px;"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                        <p style="margin-bottom: 5px;"><strong>Rent:</strong> ₱{{ number_format($pad->padRent, 2) }}</p>
                        <p style="margin-bottom: 10px;"><strong>Status:</strong> {{ $statusDisplay[$pad->padStatus] ?? $pad->padStatus }}</p>
                        <a href="{{ route('tenant.pads.show', $pad->padID) }}" class="btn btn-primary btn-sm" target="_blank">
                            <i class="fas fa-eye me-1"></i>View Details
                        </a>
                    </div>
                `;
                marker{{ $pad->padID }}.bindPopup(popupContent{{ $pad->padID }});
            @endif
        @endforeach

    // Keep the map centered on Valencia City instead of auto-fitting to all markers
});
</script>
@endpush