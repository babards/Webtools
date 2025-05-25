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
                            @php
                                $statusDisplay = [
                                    'Available' => 'Available',
                                    'Fullyoccupied' => 'Fully Occupied',
                                    'Maintenance' => 'Maintenance'
                                ];
                            @endphp
                            <span class="badge 
                                @if($pad->padStatus == 'Available') bg-success
                                @elseif($pad->padStatus == 'Fullyoccupied') bg-danger
                                @else bg-warning text-dark
                                @endif
                            ">
                                {{ $statusDisplay[$pad->padStatus] ?? $pad->padStatus }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted fw-bold">Vacant:</div>
                        <div class="col-7">
                            @if ($pad->number_of_boarders >= $pad->vacancy)
                                {{ $pad->number_of_boarders ?? 0 }}/{{ $pad->vacancy ?? 0 }} (Fully Occupied)
                            @else
                                {{ $pad->number_of_boarders ?? 0 }}/{{ $pad->vacancy ?? 0 }}
                            @endif
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted fw-bold">Applications:</div>
                        <div class="col-7">
                            {{ $pad->applications->count() ?? 0 }}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted fw-bold">Landlord:</div>
                        <div class="col-7">
                            {{ $pad->landlord->first_name ?? 'N/A' }} {{ $pad->landlord->last_name ?? '' }}
                        </div>
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
                <div class="d-flex gap-2 justify-content-end px-3 pb-3">
                    <button class="btn btn-warning btn-sm editPadBtn" data-id="{{ $pad->padID }}"
                        data-name="{{ $pad->padName }}" data-description="{{ $pad->padDescription }}"
                        data-location="{{ $pad->padLocation }}" data-rent="{{ $pad->padRent }}"
                        data-status="{{ $pad->padStatus }}" data-latitude="{{ $pad->latitude }}"
                        data-longitude="{{ $pad->longitude }}" data-vacancy="{{ $pad->vacancy }}" data-bs-toggle="modal" data-bs-target="#editPadModal"
                        style="color:#000;">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-danger btn-sm deletePadBtn" data-id="{{ $pad->padID }}"
                        data-name="{{ $pad->padName }}" data-bs-toggle="modal" data-bs-target="#deletePadModal"
                        style="color:#fff;">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>

            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#applicationsModal">
                    View Applications
                </button>
                <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#boardersModal">
                    View Boarders
                </button>
                <a href="{{ route('landlord.pads.index') }}" class="btn btn-outline-secondary">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="card-title mb-3">Map Location</h4>
        <div id="staticMap" style="height: 400px; width: 100%; border-radius: 10px;"></div>
    </div>
</div>

<!-- Applications Modal -->
<div class="modal fade" id="applicationsModal" tabindex="-1" aria-labelledby="applicationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="applicationsModalLabel">Tenant Applications for {{ $pad->padName }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @if($pad->applications->count())
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Application Date</th>
                                    <th>Status</th>
                                    <th>Message</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pad->applications as $application)
                                    <tr>
                                        <td>{{ $application->tenant->first_name ?? 'N/A' }} {{ $application->tenant->last_name ?? '' }}</td>
                                        <td>{{ $application->application_date ? $application->application_date->format('Y-m-d') : '' }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($application->status == 'approved') bg-success
                                                @elseif($application->status == 'rejected') bg-danger
                                                @elseif($application->status == 'pending') bg-warning text-dark
                                                @else bg-secondary
                                                @endif
                                            ">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        </td>
                                        <td style="max-width: 200px; word-break: break-word;">
                                            {{ $application->message ?? 'No message' }}
                                        </td>
                                        <td>
                                            @if($application->status == 'pending')
                                                <form action="{{ route('landlord.applications.approve', $application->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-success btn-sm">Approve</button>
                                                </form>
                                                <form action="{{ route('landlord.applications.reject', $application->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                                </form>
                                            @else
                                                <span class="text-muted">No action required</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No applications found for this pad.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Boarders Modal -->
<div class="modal fade" id="boardersModal" tabindex="-1" aria-labelledby="boardersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="boardersModalLabel">Boarders for {{ $pad->padName }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $boarders = isset($boarders) ? $boarders : (\App\Models\PadBoarder::with('tenant')->where('pad_id', $pad->padID)->get());
                @endphp
                @if($boarders->count())
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Tenant</th>
                                    <th>Duration</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($boarders as $boarder)
                                    @php
                                        $start = \Carbon\Carbon::parse($boarder->created_at);
                                        $now = \Carbon\Carbon::now();
                                        $diff = $start->diff($now);
                                    @endphp
                                    <tr>
                                        <td>{{ $boarder->tenant->first_name ?? 'N/A' }} {{ $boarder->tenant->last_name ?? '' }}</td>
                                        <td>{{ $diff->m }} months and {{ $diff->d }} days</td>
                                        <td>
                                            <span class="badge 
                                                @if($boarder->status == 'active') bg-success
                                                @elseif($boarder->status == 'left') bg-danger
                                                @elseif($boarder->status == 'kicked') bg-warning text-dark
                                                @else bg-secondary
                                                @endif
                                            ">
                                                {{ ucfirst($boarder->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($boarder->status == 'active')
                                                <form action="{{ route('landlord.boarders.kicked', $boarder->id) }}" method="POST" style="display:inline;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-danger btn-sm">Kick Out</button>
                                                </form>
                                            @else
                                                <span class="text-muted">No action required</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>No approved boarders for this pad yet.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Edit Pad Modal (Two-Step: Map + Form) -->
<div class="modal fade" id="editPadModal" tabindex="-1" aria-labelledby="editPadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editPadForm" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPadModalLabel">Edit Pad - Update Location</h5>
                </div>
                <div class="modal-body">
                    <!-- Step 1: Location -->
                    <div id="mapStepEdit">
                        <div id="editMap" style="height: 400px;"></div>
                        <input type="hidden" name="latitude" id="editLatitude">
                        <input type="hidden" name="longitude" id="editLongitude">
                        <div class="mb-3">
                            <label>Location</label>
                            <input type="text" name="padLocation" id="editPadLocation" class="form-control" required>
                        </div>
                    </div>
                    <!-- Step 2: Form -->
                    <div id="formStepEdit" style="display: none;">
                        <input type="hidden" name="padID" id="editPadId">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="padName" id="editPadName" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="padDescription" id="editPadDescription" class="form-control"></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Rent</label>
                            <input type="number" name="padRent" id="editPadRent" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Vacancy</label>
                            <input type="number" name="vacancy" id="editPadVacancy" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <input type="text" class="form-control" id="editPadStatusDisplay" readonly>
                            <input type="hidden" name="padStatus" id="editPadStatus">
                        </div>
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="padImage" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" id="cancelButtonEdit">Cancel</button>
                    <button type="button" class="btn btn-secondary" id="backButtonEdit" style="display: none;">Back</button>
                    <button type="button" class="btn btn-primary" id="nextButtonEdit">Next</button>
                    <button type="submit" class="btn btn-primary" id="submitButtonEdit" style="display: none;">Update</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Delete Pad Modal -->
<div class="modal fade" id="deletePadModal" tabindex="-1" aria-labelledby="deletePadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="deletePadForm" method="POST" action="">
      @csrf
      @method('DELETE')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deletePadModalLabel">Delete Pad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete <span id="deletePadName"></span>?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    var lat = {{ $pad->latitude ?? 0 }};
    var lng = {{ $pad->longitude ?? 0 }};
    var map = L.map('staticMap', {
        zoomControl: true,
        zoomControlOptions: {
            position: 'topright'
        }
    }).setView([lat, lng], 16);
    
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    // Add custom reset control
    L.Control.ResetView = L.Control.extend({
        onAdd: function(map) {
            const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
            container.innerHTML = '<a href="#" title="Reset View" role="button" aria-label="Reset View">⌂</a>';
            container.style.backgroundColor = 'white';
            container.style.width = '30px';
            container.style.height = '30px';
            container.style.display = 'flex';
            container.style.alignItems = 'center';
            container.style.justifyContent = 'center';
            
            container.onclick = function(e) {
                e.preventDefault();
                map.setView([lat, lng], 16);
            }
            
            return container;
        },
        onRemove: function(map) {}
    });

    // Add reset control to map
    new L.Control.ResetView({ position: 'topleft' }).addTo(map);

    L.marker([lat, lng]).addTo(map)
        .bindPopup("{{ $pad->padName }}");
});
</script>

@push('scripts')
    @parent
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Edit Modal Map Logic ---
        let editMap, editMarker;
        const mapStepEdit = document.getElementById('mapStepEdit');
        const formStepEdit = document.getElementById('formStepEdit');
        const nextButtonEdit = document.getElementById('nextButtonEdit');
        const backButtonEdit = document.getElementById('backButtonEdit');
        const submitButtonEdit = document.getElementById('submitButtonEdit');
        const cancelButtonEdit = document.getElementById('cancelButtonEdit');
        const editPadModal = document.getElementById('editPadModal');

        // Helper: Reverse geocode
        function reverseGeocode(lat, lng, inputId) {
            fetch(`https://nominatim.openstreetmap.org/reverse?lat=${lat}&lon=${lng}&format=json`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.display_name) {
                        document.getElementById(inputId).value = data.display_name;
                    } else {
                        document.getElementById(inputId).value = '';
                        alert('Unable to fetch address. Please enter manually.');
                    }
                })
                .catch(() => {
                    document.getElementById(inputId).value = '';
                    alert('Failed to fetch address. Please enter manually.');
                });
        }

        // Show modal and pre-fill data
        document.querySelectorAll('.editPadBtn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = btn.getAttribute('data-id');
                document.getElementById('editPadForm').action = `/landlord/pads/${id}`;
                document.getElementById('editPadId').value = id;
                document.getElementById('editPadName').value = btn.getAttribute('data-name');
                document.getElementById('editPadDescription').value = btn.getAttribute('data-description');
                document.getElementById('editPadRent').value = btn.getAttribute('data-rent');
                document.getElementById('editPadVacancy').value = btn.getAttribute('data-vacancy');
                document.getElementById('editPadLocation').value = btn.getAttribute('data-location');
                document.getElementById('editLatitude').value = btn.getAttribute('data-latitude');
                document.getElementById('editLongitude').value = btn.getAttribute('data-longitude');

                // Set initial status based on vacancy
                const vacancy = parseInt(btn.getAttribute('data-vacancy') || '0', 10);
                if (vacancy === 0) {
                    document.getElementById('editPadStatusDisplay').value = 'Fully Occupied';
                    document.getElementById('editPadStatus').value = 'Fullyoccupied';
                } else {
                    document.getElementById('editPadStatusDisplay').value = 'Available';
                    document.getElementById('editPadStatus').value = 'Available';
                }

                // Step logic
                mapStepEdit.style.display = 'block';
                formStepEdit.style.display = 'none';
                nextButtonEdit.style.display = 'inline-block';
                backButtonEdit.style.display = 'none';
                submitButtonEdit.style.display = 'none';

                // Map logic
                setTimeout(function() {
                    if (!editMap) {
                        editMap = L.map('editMap', {
                            zoomControl: true,
                            zoomControlOptions: {
                                position: 'topright'
                            }
                        }).setView([
                            btn.getAttribute('data-latitude') || 7.9092,
                            btn.getAttribute('data-longitude') || 125.0949
                        ], 16);
                        
                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(editMap);

                        // Add custom reset control for edit map
                        L.Control.ResetViewEdit = L.Control.extend({
                            onAdd: function(map) {
                                const container = L.DomUtil.create('div', 'leaflet-bar leaflet-control leaflet-control-custom');
                                container.innerHTML = '<a href="#" title="Reset View" role="button" aria-label="Reset View">⌂</a>';
                                container.style.backgroundColor = 'white';
                                container.style.width = '30px';
                                container.style.height = '30px';
                                container.style.display = 'flex';
                                container.style.alignItems = 'center';
                                container.style.justifyContent = 'center';
                                
                                container.onclick = function(e) {
                                    e.preventDefault();
                                    map.setView([7.9092, 125.0949], 16);
                                }
                                
                                return container;
                            },
                            onRemove: function(map) {}
                        });

                        // Add reset control to edit map
                        new L.Control.ResetViewEdit({ position: 'topleft' }).addTo(editMap);

                        L.Control.geocoder({ 
                            defaultMarkGeocode: false,
                            position: 'topright'
                        })
                            .on('markgeocode', function (e) {
                                const center = e.geocode.center;
                                editMap.setView(center, 16);
                                if (editMarker) editMap.removeLayer(editMarker);
                                editMarker = L.marker(center).addTo(editMap);
                                document.getElementById('editLatitude').value = center.lat;
                                document.getElementById('editLongitude').value = center.lng;
                                reverseGeocode(center.lat, center.lng, 'editPadLocation');
                            })
                            .addTo(editMap);
                        editMap.on('click', function (e) {
                            if (editMarker) editMap.removeLayer(editMarker);
                            editMarker = L.marker(e.latlng).addTo(editMap);
                            document.getElementById('editLatitude').value = e.latlng.lat;
                            document.getElementById('editLongitude').value = e.latlng.lng;
                            reverseGeocode(e.latlng.lat, e.latlng.lng, 'editPadLocation');
                        });
                    } else {
                        editMap.invalidateSize();
                        // Move marker to current location
                        const lat = btn.getAttribute('data-latitude') || 7.9092;
                        const lng = btn.getAttribute('data-longitude') || 125.0949;
                        editMap.setView([lat, lng], 16);
                        if (editMarker) editMap.removeLayer(editMarker);
                        editMarker = L.marker([lat, lng]).addTo(editMap);
                    }
                }, 300);
            });
        });

        // Add vacancy change listener for edit modal
        const editVacancyInput = document.getElementById('editPadVacancy');
        const editPadStatusDisplay = document.getElementById('editPadStatusDisplay');
        const editPadStatusHidden = document.getElementById('editPadStatus');

        if (editVacancyInput && editPadStatusDisplay && editPadStatusHidden) {
            editVacancyInput.addEventListener('input', function () {
                const vacancy = parseInt(editVacancyInput.value, 10);
                if (vacancy === 0) {
                    editPadStatusDisplay.value = 'Fully Occupied';
                    editPadStatusHidden.value = 'Fullyoccupied';
                } else {
                    editPadStatusDisplay.value = 'Available';
                    editPadStatusHidden.value = 'Available';
                }
            });
        }

        // Navigation for edit modal
        nextButtonEdit.addEventListener('click', function () {
            const lat = document.getElementById('editLatitude').value;
            const lng = document.getElementById('editLongitude').value;
            if (!lat || !lng) {
                alert('Please select a location on the map first.');
                return;
            }
            mapStepEdit.style.display = 'none';
            formStepEdit.style.display = 'block';
            nextButtonEdit.style.display = 'none';
            submitButtonEdit.style.display = 'inline-block';
            backButtonEdit.style.display = 'inline-block';
        });
        backButtonEdit.addEventListener('click', function () {
            mapStepEdit.style.display = 'block';
            formStepEdit.style.display = 'none';
            nextButtonEdit.style.display = 'inline-block';
            submitButtonEdit.style.display = 'none';
            backButtonEdit.style.display = 'none';
            if (editMap) setTimeout(() => editMap.invalidateSize(), 100);
        });
        cancelButtonEdit.addEventListener('click', function () {
            // Reset modal to initial state
            mapStepEdit.style.display = 'block';
            formStepEdit.style.display = 'none';
            nextButtonEdit.style.display = 'inline-block';
            submitButtonEdit.style.display = 'none';
            backButtonEdit.style.display = 'none';
            if (editMap) setTimeout(() => editMap.invalidateSize(), 100);
            const modalInstance = bootstrap.Modal.getInstance(editPadModal);
            if (modalInstance) modalInstance.hide();
        });
    });
    </script>
@endpush
@endsection
