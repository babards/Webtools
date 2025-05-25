@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h2 class="mb-4">Admin Dashboard</h2>
            </div>
        </div>

        <div class="row">
            <!-- User Statistics -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">User Statistics</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Users:</span>
                            <span class="fw-bold">{{ $stats['total_users'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Landlords:</span>
                            <span class="fw-bold">{{ $stats['total_landlords'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Tenants:</span>
                            <span class="fw-bold">{{ $stats['total_tenants'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pad Statistics -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pad Statistics</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Pads:</span>
                            <span class="fw-bold">{{ $stats['total_pads'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Available Pads:</span>
                            <span class="fw-bold">{{ $stats['available_pads'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Application Statistics -->
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Application Statistics</h5>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Total Applications:</span>
                            <span class="fw-bold">{{ $stats['total_applications'] }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Pending Applications:</span>
                            <span class="fw-bold">{{ $stats['pending_applications'] }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    

        <h3 class="text-center mb-4">Pads Map</h3>
   
    <div class="mt-4" id="map" style="height: 600px;"></div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize map with enhanced controls
            var map = L.map('map', {
                zoomControl: true,
                zoomControlOptions: {
                    position: 'topright'
                }
            }).setView([7.9042, 125.0928], 15);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
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

            @foreach ($pads as $pad)
                @if($pad->latitude && $pad->longitude)
                    const marker{{ $pad->padID }} = L.marker([{{ $pad->latitude }}, {{ $pad->longitude }}]).addTo(map);
                    const popupContent{{ $pad->padID }} = `
                        <div style="min-width: 200px;">
                            <h5 style="margin-bottom: 8px; color: #333;">{{ $pad->padName }}</h5>
                            <p style="margin-bottom: 5px;"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                            <p style="margin-bottom: 5px;"><strong>Rent:</strong> ₱{{ number_format($pad->padRent, 2) }}</p>
                            @php
                                $statusDisplay = [
                                    'Available' => 'Available',
                                    'Fullyoccupied' => 'Fully Occupied',
                                    'Maintenance' => 'Maintenance'
                                ];
                            @endphp
                            <p style="margin-bottom: 10px;"><strong>Status:</strong> {{ $statusDisplay[$pad->padStatus] ?? $pad->padStatus }}</p>
                            <a href="{{ route('admin.pads.show', $pad->padID) }}" class="btn btn-primary btn-sm" target="_blank">
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

    <style>
        #map {
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            border-radius: 10px;
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

@endsection