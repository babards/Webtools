@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="sidebar d-flex flex-column align-items-start py-4 position-fixed" style="top:0; left:0; height:100vh; width:240px; background-color:#f8f9fa; border-right:1px solid #dee2e6; z-index:1030;">
            <div class="text-center mb-4 w-100">
                <h4 class="fw-bold" style="letter-spacing:1px;">FindMyPad</h4>
            </div>
            <nav class="nav flex-column w-100">
                <a class="nav-link px-4 py-2" href="#pads-section">
                    <i class="fas fa-list me-2"></i>View Listings
                </a>
                <a class="nav-link px-4 py-2" href="#map-section">
                    <i class="fas fa-map-marked-alt me-2"></i>View Map
                </a>
                @guest
                    <a class="nav-link px-4 py-2" href="{{ route('login') }}">
                        <i class="fas fa-sign-in-alt me-2"></i>Login
                    </a>
                    <a class="nav-link px-4 py-2" href="{{ route('register') }}">
                        <i class="fas fa-user-plus me-2"></i>Register
                    </a>
                @else
                    <a class="nav-link px-4 py-2" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                @endguest
            </nav>
        </div>
        <!-- Main Content -->
        <div class="main-content" style="margin-left:240px; padding: 40px 20px; background-color: #fff; min-height: 100vh; width:calc(100% - 240px);">
            <div id="pads-section">
                <h1 class="mb-4 text-center">FindMyPad - Available Pads</h1>
                <form method="GET" action="{{ route('welcome') }}" class="mb-4">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search pads..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <select name="location_filter" class="form-select" onchange="this.form.submit()">
                                <option value="">All Locations</option>
                                @foreach($pads->pluck('padLocation')->map(function($loc) {
                                    $parts = explode(',', $loc);
                                    return isset($parts[2]) ? trim($parts[2]) : trim($loc);
                                })->unique()->sort() as $city)
                                    <option value="{{ $city }}" {{ request('location_filter') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="price_filter" class="form-select" onchange="this.form.submit()">
                                <option value="">All Prices</option>
                                <option value="below_1000" {{ request('price_filter') == 'below_1000' ? 'selected' : '' }}>Below ₱1,000</option>
                                <option value="1000_2000" {{ request('price_filter') == '1000_2000' ? 'selected' : '' }}>₱1,000 - ₱2,000</option>
                                <option value="2000_3000" {{ request('price_filter') == '2000_3000' ? 'selected' : '' }}>₱2,000 - ₱3,000</option>
                                <option value="above_3000" {{ request('price_filter') == 'above_3000' ? 'selected' : '' }}>Above ₱3,000</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <a href="{{ route('welcome') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
                <div class="row justify-content-center">
                    @forelse($pads as $pad)
                        <div class="col-md-4 mb-4">
                            <a href="{{ route('guest.pads.show', ['pad' => $pad->padID]) }}" style="text-decoration: none; color: inherit;">
                                <div class="card h-100 shadow-sm">
                                    @if($pad->padImage)
                                        <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top pad-img" alt="{{ $pad->padName }}">
                                    @else
                                        <img src="https://via.placeholder.com/400x200?text=No+Image" class="card-img-top pad-img" alt="No Image">
                                    @endif
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $pad->padName }}</h5>
                                        <p class="card-text mb-1"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                                        <p class="card-text mb-1"><strong>Rent:</strong> ₱{{ number_format($pad->padRent, 2) }}</p>
                                        <p class="card-text mb-1"><strong>Boarders:</strong> {{ $pad->number_of_boarders ?? 0 }}</p>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @empty
                        <div class="col-12">
                            <div class="alert alert-info text-center">No pads available at the moment.</div>
                        </div>
                    @endforelse
                </div>
                <div class="d-flex justify-content-center mt-4">
                    {{ $pads->appends(request()->except('page'))->links('pagination::bootstrap-5') }}
                </div>
            </div>
            <div id="map-section" class="mt-5">
                <h2 class="text-center mb-4">Available Pads Map</h2>
                <div id="padsMap" style="height: 500px; width: 100%; border-radius: 10px;"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    body { background: #f7f8fa; }
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
    .sidebar {
        min-height: 100vh;
        background-color: #f8f9fa;
        padding-top: 20px;
        border-right: 1px solid #dee2e6;
    }
    .sidebar .nav-link {
        color: #333;
        padding: 10px 20px;
        margin: 5px 0;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    .sidebar .nav-link:hover {
        background-color: #e9ecef;
        color: #0d6efd;
    }
    .sidebar .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }
    .sidebar h4 {
        font-size: 1.5rem;
        letter-spacing: 1px;
    }
    .main-content {
        background: #fff;
    }
    @media (max-width: 991.98px) {
        .sidebar {
            position: static !important;
            width: 100% !important;
            min-height: auto !important;
            border-right: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
        }
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map with the default coordinates and zoom
    const map = L.map('padsMap').setView([7.9092, 125.0949], 15);

    // Add the tile layer (OpenStreetMap)
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Add markers for each pad
    @foreach($pads as $pad)
        @if($pad->latitude && $pad->longitude)
            const marker{{ $pad->padID }} = L.marker([{{ $pad->latitude }}, {{ $pad->longitude }}]).addTo(map);
            const popupContent{{ $pad->padID }} = `
                <div>
                    <h5>{{ $pad->padName }}</h5>
                    <p><strong>Location:</strong> {{ $pad->padLocation }}</p>
                    <p><strong>Rent:</strong> ₱{{ number_format($pad->padRent, 2) }}</p>
                </div>
            `;
            marker{{ $pad->padID }}.bindPopup(popupContent{{ $pad->padID }});
        @endif
    @endforeach

    // Smooth scroll for navigation
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
</script>
@endpush