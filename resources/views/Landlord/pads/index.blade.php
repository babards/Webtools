@extends('layouts.app')

@section('content')
    <div class="container">

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2>Pad Management</h2>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPadModal">
            Add New Pad
        </button>
    </div>
    <div class="row align-items-end mb-3">
            <div class="col">
                <form method="GET" action="{{ route('landlord.pads.index') }}" class="mb-4 flex-grow-1 me-3">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" placeholder="Search pad..." value="{{ request('search') }}">
                                <button class="btn btn-primary" type="submit">Search</button>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                            <a href="{{ route('landlord.pads.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
                        </div>
                    </div>
                </form>
            </div>

    <div class="row" id="pad-list">
        @foreach ($pads as $pad)
            <div class="col-md-3 mb-4" id="pad-card-{{ $pad->padID }}">
                <div class="card h-100 d-flex flex-column shadow-sm">
                    <a href="{{ route('landlord.pads.show', $pad->padID) }}" style="text-decoration: none; color: inherit;">
                        @if ($pad->padImage)
                            <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top"
                                style="height: 160px; object-fit: cover;">
                        @else
                            <img src="https://via.placeholder.com/300x160?text=No+Image" class="card-img-top"
                                style="height: 160px; object-fit: cover;">
                        @endif
                        <div class="card-body flex-grow-1">
                            <h5 class="card-title">{{ $pad->padName }}</h5>
                            <p class="card-text">{{ $pad->padLocation }}</p>
                            <p class="card-text text-muted mb-1">₱{{ number_format($pad->padRent, 2) }}</p>
                            <p class="card-text text-muted mb-1">Status: 
                                @if ($pad->number_of_boarders >= $pad->vacancy)
                                    Fully Occupied
                                @else
                                    {{ ucfirst($pad->padStatus) }}
                                @endif
                            </p>
                            @if ($pad->number_of_boarders >= $pad->vacancy)
                                <p class="card-text text-muted mb-1"><strong>Vacant:</strong> {{ $pad->number_of_boarders ?? 0 }}/{{ $pad->vacancy ?? 0 }} (Fully Occupied)</p>
                            @else
                                <p class="card-text text-muted mb-1"><strong>Vacant:</strong> {{ $pad->number_of_boarders ?? 0 }}/{{ $pad->vacancy ?? 0 }}</p>
                            @endif
                        </div>
                    </a>
                    <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2 mt-auto">
                        <button class="btn btn-warning btn-sm editPadBtn" data-id="{{ $pad->padID }}"
                            data-name="{{ $pad->padName }}" data-description="{{ $pad->padDescription }}"
                            data-location="{{ $pad->padLocation }}" data-rent="{{ $pad->padRent }}"
                            data-status="{{ $pad->padStatus }}" data-latitude="{{ $pad->latitude }}"
                            data-longitude="{{ $pad->longitude }}" data-bs-toggle="modal" data-bs-target="#editPadModal"
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
            </div>
        @endforeach
    </div>

    @if ($pads->isEmpty())
    <div class="col-12">
        <div class="alert alert-info text-center">No pads found.</div>
    </div>
    @endif

    <div class="mt-3">
        {{ $pads->links('pagination::bootstrap-5') }}
    </div>

    <!-- Create Pad Modal -->
    <div class="modal fade" id="createPadModal" tabindex="-1" aria-labelledby="createPadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('landlord.pads.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPadModalLabel">Add New Pad - Select Location</h5>
                    </div>

                    <div class="modal-body">
                        <!-- Step 1: Map -->
                        <div id="mapStep">
                            <div id="map" style="height: 400px;"></div>
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <div class="mb-3">
                                <label>Location</label>
                                <input type="text" name="padLocation" id="padLocation" class="form-control" required>
                            </div>
                        </div>

                        <!-- Step 2: Form -->
                        <div id="formStep" style="display: none;">
                            <div class="mb-3">
                                <label>Name</label>
                                <input type="text" name="padName" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Description</label>
                                <textarea name="padDescription" class="form-control"></textarea>
                            </div>
                            <div class="mb-3">
                                <label>Rent</label>
                                <input type="number" name="padRent" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Vacancy</label>
                                <input type="number" name="vacancy" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <select name="padStatus" class="form-select" required>
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Image</label>
                                <input type="file" name="padImage" class="form-control">
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="cancelButtonCreate">Cancel</button>
                        <button type="button" class="btn btn-secondary" id="backButton" style="display: none;">Back</button>
                        <button type="button" class="btn btn-primary" id="nextButton">Next</button>
                        <button type="submit" class="btn btn-primary" id="submitButton"
                            style="display: none;">Create</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Pad Modal -->
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

                        {{-- Step 1: Location --}}
                        <div id="mapStepEdit">
                            <div id="editMap" style="height: 400px;"></div>
                            <input type="hidden" name="latitude" id="editLatitude">
                            <input type="hidden" name="longitude" id="editLongitude">
                            <div class="mb-3">
                                <label>Location</label>
                                <input type="text" name="padLocation" id="editPadLocation" class="form-control" required>
                            </div>
                        </div>

                        {{-- Step 2: Form --}}
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
                                <select name="padStatus" id="editPadStatus" class="form-select" required>
                                    <option value="available">Available</option>
                                    <option value="occupied">Occupied</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Image</label>
                                <input type="file" name="padImage" class="form-control">
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="cancelButtonEdit">Cancel</button>
                        <button type="button" class="btn btn-secondary" id="backButtonEdit"
                            style="display: none;">Back</button>
                        <button type="button" class="btn btn-primary" id="nextButtonEdit">Next</button>
                        <button type="submit" class="btn btn-primary" id="submitButtonEdit"
                            style="display: none;">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Pad Modal -->
    <div class="modal fade" id="deletePadModal" tabindex="-1" aria-labelledby="deletePadModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" id="deletePadForm">
                @csrf
                @method('DELETE')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deletePadModalLabel">Delete Pad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete <strong id="deletePadName"></strong>?</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- JS for populating modals -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            // Reverse geocode helper
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

            // Elements for Add New Pad modal
            const mapStep = document.getElementById('mapStep');
            const formStep = document.getElementById('formStep');
            const nextButton = document.getElementById('nextButton');
            const backButton = document.getElementById('backButton');
            const submitButton = document.getElementById('submitButton');
            const createPadModalLabel = document.getElementById('createPadModalLabel');
            const createPadModal = document.getElementById('createPadModal');
            const cancelCreateBtn = document.getElementById('cancelButtonCreate');

            // Map and marker for Add New Pad
            let map;
            let marker;

            if (createPadModal) {
                createPadModal.addEventListener('shown.bs.modal', function () {
                    if (!map) {
                        const defaultLatLng = [7.9092, 125.0949];
                        map = L.map('map').setView(defaultLatLng, 14);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
                        }).addTo(map);

                        // Geocoder
                        L.Control.geocoder({
                            defaultMarkGeocode: false
                        })
                            .on('markgeocode', function (e) {
                                const center = e.geocode.center;
                                map.setView(center, 16);

                                if (marker) map.removeLayer(marker);
                                marker = L.marker(center).addTo(map);

                                document.getElementById('latitude').value = center.lat;
                                document.getElementById('longitude').value = center.lng;

                                reverseGeocode(center.lat, center.lng, 'padLocation');
                            })
                            .addTo(map);

                        map.on('click', function (e) {
                            if (marker) map.removeLayer(marker);
                            marker = L.marker(e.latlng).addTo(map);

                            document.getElementById('latitude').value = e.latlng.lat;
                            document.getElementById('longitude').value = e.latlng.lng;

                            reverseGeocode(e.latlng.lat, e.latlng.lng, 'padLocation');
                        });
                    }
                    setTimeout(() => map.invalidateSize(), 100);
                });
            }

            // Navigation buttons for Add New Pad modal
            if (nextButton && backButton && submitButton && mapStep && formStep && createPadModalLabel) {
                nextButton.addEventListener('click', function () {
                    const lat = document.getElementById('latitude').value;
                    const lng = document.getElementById('longitude').value;

                    if (!lat || !lng) {
                        alert('Please select a location on the map first.');
                        return;
                    }

                    mapStep.style.display = 'none';
                    formStep.style.display = 'block';
                    nextButton.style.display = 'none';
                    submitButton.style.display = 'inline-block';
                    backButton.style.display = 'inline-block';
                    createPadModalLabel.innerText = 'Add New Pad - Fill Details';
                });

                backButton.addEventListener('click', function () {
                    mapStep.style.display = 'block';
                    formStep.style.display = 'none';
                    nextButton.style.display = 'inline-block';
                    submitButton.style.display = 'none';
                    backButton.style.display = 'none';
                    createPadModalLabel.innerText = 'Add New Pad - Select Location';

                    if (map) {
                        setTimeout(() => map.invalidateSize(), 100);
                    }
                });
            }

            // Cancel button for New Pad
            cancelCreateBtn.addEventListener('click', function () {
                // Reset map markers & view
                if (typeof map !== 'undefined') {
                    map.eachLayer(function (layer) {
                        if (layer instanceof L.Marker) {
                            map.removeLayer(layer);
                        }
                    });
                    const defaultLatLng = [7.9092, 125.0949];
                    map.setView(defaultLatLng, 14);
                }

                // Reset form fields (including hidden latitude, longitude, location input)
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
                document.getElementById('padLocation').value = '';

                // Reset the whole form
                const form = createPadModal.querySelector('form');
                if (form) form.reset();

                // Reset step visibility and buttons to initial state
                document.getElementById('mapStep').style.display = 'block';
                document.getElementById('formStep').style.display = 'none';

                nextButton.style.display = 'inline-block';
                backButton.style.display = 'none';
                submitButton.style.display = 'none';

                createPadModalLabel.innerText = 'Add New Pad - Select Location';

                // Hide the modal (Bootstrap 5)
                const modalInstance = bootstrap.Modal.getInstance(createPadModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });

            // Delete Pad buttons
            document.querySelectorAll('.deletePadBtn').forEach(function (button) {
                button.addEventListener('click', function () {
                    const nameElem = document.getElementById('deletePadName');
                    const formElem = document.getElementById('deletePadForm');
                    if (nameElem && formElem) {
                        nameElem.textContent = this.dataset.name;
                        formElem.action = '/landlord/pads/' + this.dataset.id;
                    }
                });
            });

            // Store original edit pad data to reset on cancel
            let originalEditPadData = {};
            const mapStepEdit = document.getElementById('mapStepEdit');
            const formStepEdit = document.getElementById('formStepEdit');
            const nextButtonEdit = document.getElementById('nextButtonEdit');
            const backButtonEdit = document.getElementById('backButtonEdit');
            const submitButtonEdit = document.getElementById('submitButtonEdit');
            const EditPadModalLabel = document.getElementById('editPadModalLabel');

            let editMap = null;
            let editMarker = null;

            function setMarkerOnMap(lat, lng) {
                if (!editMap) {
                    editMap = L.map('editMap').setView([lat, lng], 14);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(editMap);

                    editMap.on('click', function (e) {
                        if (editMarker) editMap.removeLayer(editMarker);
                        editMarker = L.marker(e.latlng).addTo(editMap);

                        document.getElementById('editLatitude').value = e.latlng.lat;
                        document.getElementById('editLongitude').value = e.latlng.lng;
                        reverseGeocode(e.latlng.lat, e.latlng.lng, 'editPadLocation');
                    });
                } else {
                    editMap.setView([lat, lng], 14);
                    if (editMarker) editMap.removeLayer(editMarker);
                    editMarker = L.marker([lat, lng]).addTo(editMap);
                }
                editMap.invalidateSize();
            }

            // When clicking edit buttons
            document.querySelectorAll('.editPadBtn').forEach(function (button) {
                button.addEventListener('click', function () {
                    originalEditPadData = {
                        id: this.dataset.id || '',
                        name: this.dataset.name || '',
                        description: this.dataset.description || '',
                        location: this.dataset.location || '',
                        rent: this.dataset.rent || '',
                        vacancy: this.dataset.vacancy || '',
                        status: this.dataset.status || '',
                        latitude: this.dataset.latitude || '',
                        longitude: this.dataset.longitude || ''
                    };

                    // Fill form fields
                    document.getElementById('editPadId').value = this.dataset.id || '';
                    document.getElementById('editPadName').value = this.dataset.name || '';
                    document.getElementById('editPadDescription').value = this.dataset.description || '';
                    document.getElementById('editPadLocation').value = this.dataset.location || '';
                    document.getElementById('editPadRent').value = this.dataset.rent || '';
                    document.getElementById('editPadVacancy').value = this.dataset.vacancy || '';
                    document.getElementById('editPadStatus').value = this.dataset.status || '';
                    document.getElementById('editLatitude').value = this.dataset.latitude || '';
                    document.getElementById('editLongitude').value = this.dataset.longitude || '';
                    document.getElementById('editPadForm').action = '/landlord/pads/' + this.dataset.id;

                    const lat = parseFloat(this.dataset.latitude) || 7.9092;
                    const lng = parseFloat(this.dataset.longitude) || 125.0949;

                    // Show step 1 on open every time
                    mapStepEdit.style.display = 'block';
                    formStepEdit.style.display = 'none';
                    nextButtonEdit.style.display = 'inline-block';
                    backButtonEdit.style.display = 'none';
                    submitButtonEdit.style.display = 'none';

                    setTimeout(() => {
                        setMarkerOnMap(lat, lng);
                    }, 300);
                });
            });

            const cancelEditBtn = document.getElementById('cancelButtonEdit');
            if (cancelEditBtn) {
                cancelEditBtn.addEventListener('click', function () {
                    // Reset form fields to original
                    document.getElementById('editPadId').value = originalEditPadData.id;
                    document.getElementById('editPadName').value = originalEditPadData.name;
                    document.getElementById('editPadDescription').value = originalEditPadData.description;
                    document.getElementById('editPadLocation').value = originalEditPadData.location;
                    document.getElementById('editPadRent').value = originalEditPadData.rent;
                    document.getElementById('editPadVacancy').value = originalEditPadData.vacancy;
                    document.getElementById('editPadStatus').value = originalEditPadData.status;
                    document.getElementById('editLatitude').value = originalEditPadData.latitude;
                    document.getElementById('editLongitude').value = originalEditPadData.longitude;

                    editPadModalLabel.innerText = 'Edit Pad - Update Location';

                    // Close modal
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editPadModal'));
                    if (modal) modal.hide();
                });
            }

            nextButtonEdit.addEventListener('click', function () {
                const locationVal = document.getElementById('editPadLocation').value.trim();
                if (!locationVal) {
                    alert('Please enter a location before proceeding.');
                    return;
                }
                mapStepEdit.style.display = 'none';
                formStepEdit.style.display = 'block';
                nextButtonEdit.style.display = 'none';
                backButtonEdit.style.display = 'inline-block';
                submitButtonEdit.style.display = 'inline-block';
                editPadModalLabel.innerText = 'Edit Pad - Update Details';
            });

            backButtonEdit.addEventListener('click', function () {
                mapStepEdit.style.display = 'block';
                formStepEdit.style.display = 'none';

                nextButtonEdit.style.display = 'inline-block';
                backButtonEdit.style.display = 'none';
                submitButtonEdit.style.display = 'none';
                editPadModalLabel.innerText = 'Edit Pad - Update Location';

                // When going back to step 1, show marker again
                const lat = parseFloat(document.getElementById('editLatitude').value) || 7.9092;
                const lng = parseFloat(document.getElementById('editLongitude').value) || 125.0949;
                setMarkerOnMap(lat, lng);
            });

            // Optional: Reset UI and marker when modal is shown (in case user closes and reopens)
            const editPadModal = document.getElementById('editPadModal');
            if (editPadModal) {
                editPadModal.addEventListener('show.bs.modal', () => {
                    // Reset steps and buttons to step 1
                    mapStepEdit.style.display = 'block';
                    formStepEdit.style.display = 'none';
                    nextButtonEdit.style.display = 'inline-block';
                    backButtonEdit.style.display = 'none';
                    submitButtonEdit.style.display = 'none';

                    // Reset marker to current lat/lng or default
                    const lat = parseFloat(document.getElementById('editLatitude').value) || 7.9092;
                    const lng = parseFloat(document.getElementById('editLongitude').value) || 125.0949;
                    setTimeout(() => {
                        setMarkerOnMap(lat, lng);
                    }, 300);
                });
            }

        });
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
@endsection