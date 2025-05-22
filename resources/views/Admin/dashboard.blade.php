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

        
    
    <div class="mt-4" id="map" style="height: 600px;"></div>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var map = L.map('map').setView([7.9092, 125.0949], 15); // Default center

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors'
            }).addTo(map);

            @foreach ($pads as $pad)
                @if($pad->latitude && $pad->longitude)
                    L.marker([{{ $pad->latitude }}, {{ $pad->longitude }}])
                        .addTo(map)
                        .bindPopup(`
                                    <strong>{{ $pad->padName }}</strong><br>
                                    Location: {{ $pad->padLocation }}<br>
                                    Rent: ₱{{ $pad->padRent }}<br>
                                    <a href="{{ route('admin.pads.show', $pad->padID) }}" target="_blank">View</a>
                                `);
                @endif
            @endforeach

            // Custom Reset View Button
            L.Control.ResetView = L.Control.extend({
                onAdd: function (map) {
                    const btn = L.DomUtil.create('button', 'leaflet-bar leaflet-control leaflet-control-custom');
                    btn.innerHTML = '⤾'; // Use an icon or symbol
                    btn.title = "Return to Default View";

                    btn.style.backgroundColor = 'white';
                    btn.style.width = '34px';
                    btn.style.height = '34px';
                    btn.style.cursor = 'pointer';
                    btn.style.textAlign = 'center';
                    btn.style.lineHeight = '34px';

                    btn.onclick = function () {
                        map.setView([7.9092, 125.0949], 15); // Reset to default
                    };

                    return btn;
                }
            });

            L.control.resetView = function (opts) {
                return new L.Control.ResetView(opts);
            }

            L.control.resetView({ position: 'topleft' }).addTo(map);
        });
    </script>

@endsection