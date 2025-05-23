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
                        <div class="col-5 text-muted fw-bold">Vacant:</div>
                        <div class="col-7">
                            @if($pad->number_of_boarders >= $pad->vacancy)
                                Fully Occupied
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
                        data-longitude="{{ $pad->longitude }}" data-vacancy="{{ $pad->vacancy }}"
                        data-bs-toggle="modal" data-bs-target="#editPadModal"
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

            <div class="d-flex">
                <a href="{{ route('admin.pads.index') }}" class="btn btn-outline-secondary">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="card-title mb-3">Map Location</h4>
        <div id="map" style="height: 400px; width: 100%; border-radius: 10px;"></div>
    </div>
</div>

<!-- Edit Pad Modal -->
<div class="modal fade" id="editPadModal" tabindex="-1" aria-labelledby="editPadModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" id="editPadForm" enctype="multipart/form-data" action="{{ route('admin.pads.update', $pad->padID) }}">
            @csrf
            @method('PUT')
            <input type="hidden" name="padID" value="{{ $pad->padID }}">
            <input type="hidden" name="userID" value="{{ $pad->userID }}">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPadModalLabel">Edit Pad</h5>
                </div>
                <div class="modal-body">
                    <div id="mapStepEdit">
                        <div id="editMap" style="height: 400px;"></div>
                        <input type="hidden" name="latitude" id="editLatitude" value="{{ $pad->latitude }}">
                        <input type="hidden" name="longitude" id="editLongitude" value="{{ $pad->longitude }}">
                        <div class="mb-3">
                            <label>Location</label>
                            <input type="text" name="padLocation" id="editPadLocation" class="form-control" value="{{ $pad->padLocation }}" required>
                        </div>
                    </div>
                    <div id="formStepEdit" style="display: none;">
                        <div class="mb-3">
                            <label>Name</label>
                            <input type="text" name="padName" class="form-control" value="{{ $pad->padName }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Description</label>
                            <textarea name="padDescription" class="form-control">{{ $pad->padDescription }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label>Rent</label>
                            <input type="number" name="padRent" class="form-control" value="{{ $pad->padRent }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Vacancy</label>
                            <input type="number" name="vacancy" class="form-control" value="{{ $pad->vacancy }}" required>
                        </div>
                        <div class="mb-3">
                            <label>Status</label>
                            <select name="padStatus" class="form-select" required>
                                <option value="available" {{ $pad->padStatus == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="occupied" {{ $pad->padStatus == 'occupied' ? 'selected' : '' }}>Occupied</option>
                                <option value="maintenance" {{ $pad->padStatus == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Image</label>
                            <input type="file" name="padImage" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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
        <form method="POST" action="{{ route('admin.pads.destroy', $pad->padID) }}" id="deletePadForm">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePadModalLabel">Delete Pad</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete <strong>{{ $pad->padName }}</strong>?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete</button>
                </div>
            </div>
        </form>
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

            // Edit Modal Map Logic
            let editMap, editMarker;
            const editPadModal = document.getElementById('editPadModal');
            const editMapContainer = document.getElementById('editMap');
            const editLatitudeInput = document.getElementById('editLatitude');
            const editLongitudeInput = document.getElementById('editLongitude');
            const editPadLocationInput = document.getElementById('editPadLocation');

            editPadModal.addEventListener('shown.bs.modal', function () {
                if (!editMap) {
                    editMap = L.map('editMap').setView([lat, lng], 16);
                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(editMap);

                    editMarker = L.marker([lat, lng]).addTo(editMap);

                    editMap.on('click', function (e) {
                        if (editMarker) editMap.removeLayer(editMarker);
                        editMarker = L.marker(e.latlng).addTo(editMap);
                        editLatitudeInput.value = e.latlng.lat;
                        editLongitudeInput.value = e.latlng.lng;
                        // Optionally, reverse geocode to update the location input
                        fetch(`https://nominatim.openstreetmap.org/reverse?lat=${e.latlng.lat}&lon=${e.latlng.lng}&format=json`)
                            .then(response => response.json())
                            .then(data => {
                                if (data && data.display_name) {
                                    editPadLocationInput.value = data.display_name;
                                }
                            });
                    });
                } else {
                    editMap.invalidateSize();
                }
            });

            // Handle the two-step process
            const nextButtonEdit = document.getElementById('nextButtonEdit');
            const submitButtonEdit = document.getElementById('submitButtonEdit');
            const mapStepEdit = document.getElementById('mapStepEdit');
            const formStepEdit = document.getElementById('formStepEdit');

            nextButtonEdit.addEventListener('click', function () {
                const locationVal = editPadLocationInput.value.trim();
                if (!locationVal) {
                    alert('Please select a location before proceeding.');
                    return;
                }
                mapStepEdit.style.display = 'none';
                formStepEdit.style.display = 'block';
                nextButtonEdit.style.display = 'none';
                submitButtonEdit.style.display = 'inline-block';
            });

            submitButtonEdit.addEventListener('click', function () {
                document.getElementById('editPadForm').submit();
            });
        });
    </script>
@endpush
@endsection
