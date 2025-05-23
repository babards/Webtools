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
                <a class="nav-link px-4 py-2" href="{{ route('welcome') }}#pads-section">View Listings</a>
                <a class="nav-link px-4 py-2" href="{{ route('welcome') }}#map-section">View Map</a>
                @guest
                    <a class="nav-link px-4 py-2" href="{{ route('login') }}">Login</a>
                    <a class="nav-link px-4 py-2" href="{{ route('register') }}">Register</a>
                @else
                    <a class="nav-link px-4 py-2" href="{{ route('dashboard') }}">Dashboard</a>
                @endguest
            </nav>
        </div>
        <!-- Main Content -->
        <div class="main-content" style="margin-left:240px; padding: 40px 20px; background-color: #fff; min-height: 100vh; width:calc(100% - 240px);">
            <div class="container py-4">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm mb-4">
                            @if($pad->padImage)
                                <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top rounded-top" style="object-fit:cover; max-height:320px;">
                            @else
                                <img src="https://via.placeholder.com/600x320?text=No+Image" class="card-img-top rounded-top" style="object-fit:cover; max-height:320px;">
                            @endif
                            <div class="card-body">
                                <h2 class="card-title mb-3">{{ $pad->padName }}</h2>
                                <div class="row mb-2">
                                    <div class="col-5 text-muted fw-bold">Description:</div>
                                    <div class="col-7">{{ $pad->padDescription ?? '-' }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-muted fw-bold">Location:</div>
                                    <div class="col-7">{{ $pad->padLocation }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-muted fw-bold">Rent:</div>
                                    <div class="col-7">₱{{ number_format($pad->padRent, 2) }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-muted fw-bold">Status:</div>
                                    <div class="col-7">
                                        <span class="badge 
                                            @if($pad->padStatus == 'available') bg-success
                                            @elseif($pad->number_of_boarders >= $pad->vacancy) bg-danger
                                            @elseif($pad->padStatus == 'occupied') bg-danger
                                            @else bg-warning text-dark
                                            @endif
                                        ">
                                            @if($pad->number_of_boarders >= $pad->vacancy)
                                                Fully Occupied
                                            @else
                                                {{ ucfirst($pad->padStatus) }}
                                            @endif
                                        </span>
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-muted fw-bold">Landlord:</div>
                                    <div class="col-7">
                                        {{ $pad->landlord->first_name ?? 'N/A' }} {{ $pad->landlord->last_name ?? '' }}
                                    </div>
                                </div>
                                <div class="row mb-2">
                                    @if ($pad->number_of_boarders >= $pad->vacancy)
                                        <div class="col-5 text-muted fw-bold">Vacant:</div>
                                        <div class="col-7">Fully Occupied</div>
                                    @else
                                        <div class="col-5 text-muted fw-bold">Vacant:</div>
                                        <div class="col-7">{{ $pad->number_of_boarders ?? 0 }}/{{ $pad->vacancy ?? 0 }}</div>
                                    @endif
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-muted fw-bold">Created At:</div>
                                    <div class="col-7">{{ $pad->padCreatedAt }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-muted fw-bold">Updated At:</div>
                                    <div class="col-7">{{ $pad->padUpdatedAt }}</div>
                                </div>
                            </div>
                        </div>
                        @if($pad->padStatus == 'available')
                            <div class="d-flex gap-2 mt-3">
                                @guest
                                    <a href="{{ route('register') }}" class="btn btn-primary">
                                        Apply for this Pad
                                    </a>
                                @else
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyPadModal">
                                        Apply for this Pad
                                    </button>
                                @endguest
                                <a href="{{ route('welcome') }}" class="btn btn-secondary">
                                    Back to Listings
                                </a>
                            </div>
                        @else
                            <div class="mt-3">
                                <a href="{{ route('welcome') }}" class="btn btn-secondary">
                                    Back to Listings
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
                @auth
                <!-- Apply Pad Modal -->
                <div class="modal fade" id="applyPadModal" tabindex="-1" aria-labelledby="applyPadModalLabel" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="{{ route('guest.pads.apply', ['padId' => $pad->padID]) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                          <h5 class="modal-title w-100 text-center" id="applyPadModalLabel">Apply Pad</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          <div class="mb-3">
                            <label for="guest_name" class="form-label">Your Name:</label>
                            <input type="text" name="guest_name" id="guest_name" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label for="guest_email" class="form-label">Your Email:</label>
                            <input type="email" name="guest_email" id="guest_email" class="form-control" required>
                          </div>
                          <div class="mb-3">
                            <label for="message" class="form-label">Message:</label>
                            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
                          </div>
                        </div>
                        <div class="modal-footer justify-content-center">
                          <button type="button" class="btn btn-danger me-2" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-success">Apply</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
                @endauth
                <!-- Map at the bottom -->
                <div class="card shadow-sm mb-4 mt-5">
                    <div class="card-body">
                        <h4 class="card-title mb-3">Map Location</h4>
                        <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
<style>
    body { background: #f7f8fa; }
    .sidebar .nav-link {
        color: #333;
        padding: 10px 20px;
        margin: 5px 0;
        border-radius: 5px;
        transition: all 0.3s ease;
        font-size: 1.08rem;
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
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var lat = {{ $pad->latitude ?? 0 }};
            var lng = {{ $pad->longitude ?? 0 }};

            var map = L.map('map').setView([lat, lng], 16);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);

            L.marker([lat, lng]).addTo(map)
                .bindPopup("{{ $pad->padName }}")
                .openPopup();
        });
    </script>
@endpush 