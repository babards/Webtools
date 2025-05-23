@extends('layouts.app')

@section('content')
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
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applyPadModal">
                        Apply for this Pad
                    </button>
                    <a href="{{ route('tenant.pads.index') }}" class="btn btn-secondary">
                        Back to Pads
                    </a>
                </div>
            @else
                <div class="mt-3">
                    <a href="{{ route('tenant.pads.index') }}" class="btn btn-secondary">
                        Back to Pads
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Apply Pad Modal -->
<div class="modal fade" id="applyPadModal" tabindex="-1" aria-labelledby="applyPadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="{{ route('tenant.pads.apply', ['padId' => $pad->padID]) }}" method="POST">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center" id="applyPadModalLabel">Apply Pad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex justify-content-between mb-2" style="font-weight: bold;">
            <div>
              {{ Auth::user()->name ?? Auth::user()->first_name . ' ' . Auth::user()->last_name }}<br>
              <span style="font-weight: normal; font-size: 0.9em;">
                {{ Auth::user()->email }}<br>
              </span>
            </div>
            <div class="text-end">
              {{ $pad->padName }}<br>
              <span style="font-weight: normal; font-size: 0.9em;">
                {{ $pad->landlord->first_name ?? '' }} {{ $pad->landlord->last_name ?? '' }}<br>
                {{ $pad->padLocation }}
              </span>
            </div>
          </div>
          <hr>
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
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="card-title mb-3">Map Location</h4>
        <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>
    </div>
</div>
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
@endsection
