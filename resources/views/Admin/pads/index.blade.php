@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>All Pads (Admin View)</h2>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.pads.index') }}" class="btn btn-outline-secondary">Reset Filters</a>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPadModal">
                    Create New Pad
                </button>
            </div>
        </div>

        <form method="GET" action="{{ route('admin.pads.index') }}" class="mb-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control" placeholder="Search pads by name, location, landlord..." value="{{ request('search') }}">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="landlord_filter" class="form-select" onchange="this.form.submit()">
                        <option value="">All Landlords</option>
                        @foreach($landlords as $landlord)
                            <option value="{{ $landlord->id }}" {{ request('landlord_filter') == $landlord->id ? 'selected' : '' }}>
                                {{ $landlord->first_name }} {{ $landlord->last_name }}
                            </option>
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
                        <option value="below_1000" {{ request('price_filter') == 'below_1000' ? 'selected' : '' }}>Below ₱1,000</option>
                        <option value="1000_2000" {{ request('price_filter') == '1000_2000' ? 'selected' : '' }}>₱1,000 - ₱2,000</option>
                        <option value="2000_3000" {{ request('price_filter') == '2000_3000' ? 'selected' : '' }}>₱2,000 - ₱3,000</option>
                        <option value="above_3000" {{ request('price_filter') == 'above_3000' ? 'selected' : '' }}>Above ₱3,000</option>
                    </select>
                </div>
            </div>
        </form>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Whoops! Something went wrong.</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            @forelse($pads as $pad)
                <div class="col-md-3 mb-4" id="pad-card-{{ $pad->padID }}">
                    <div class="card h-100 d-flex flex-column shadow-sm">
                        <a href="{{ route('admin.pads.show', $pad->padID) }}" style="text-decoration: none; color: inherit;">
                            @if($pad->main_image)
                                <img src="{{ asset('storage/' . $pad->main_image) }}" class="card-img-top"
                                    style="height: 160px; object-fit: cover;" alt="{{ $pad->padName }}">
                            @else
                                <img src="https://via.placeholder.com/300x160?text=No+Image" class="card-img-top"
                                    style="height: 160px; object-fit: cover;" alt="No Image">
                            @endif
                            <div class="card-body pb-5">
                                <h5 class="card-title">{{ $pad->padName }}</h5>
                                <p class="card-text">{{ Str::limit($pad->padDescription, 50) }}</p>
                                <p class="card-text"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                                <p class="card-text text-muted mb-1">₱{{ number_format($pad->padRent, 2) }}</p>
                                <p class="card-text text-muted mb-1">Status: 
                                    {{ $statusDisplay[$pad->padStatus] ?? $pad->padStatus }}
                                </p>
                                <p class="card-text text-muted mb-1">Landlord: {{ $pad->landlord->first_name ?? 'N/A' }}
                                    {{ $pad->landlord->last_name ?? '' }}
                                </p>

                                @if ($pad->number_of_boarders >= $pad->vacancy)
                                    <p class="card-text text-muted mb-1"><strong>Vacant:</strong> {{ $pad->number_of_boarders ?? 0 }}/{{ $pad->vacancy ?? 0 }} (Fully Occupied)</p>
                                @else
                                    <p class="card-text text-muted mb-1"><strong>Vacant:</strong> {{ $pad->number_of_boarders ?? 0 }}/{{ $pad->vacancy ?? 0 }}</p>
                                @endif
                            </div>
                        </a>
                        {{-- data-image-url="{{ $pad->padImage ? asset('storage/' . $pad->padImage) : '' }}"> --}}
                        <div class="card-footer bg-white border-0 d-flex align-items-end gap-2 justify-content-end position-absolute w-100" style="bottom: 0; right: 0; min-height: 40px; background: transparent;">
                            <button type="button" class="btn btn-warning btn-sm editPadBtn p-1" data-bs-toggle="modal"
                                data-bs-target="#editPadModal" data-id="{{ $pad->padID }}" data-name="{{ $pad->padName }}"
                                data-description="{{ $pad->padDescription }}" data-location="{{ $pad->padLocation }}"
                                data-rent="{{ $pad->padRent }}" data-status="{{ $pad->padStatus }}"
                                data-latitude="{{ $pad->latitude }}" data-longitude="{{ $pad->longitude }}" data-vacancy="{{ $pad->vacancy }}"
                                data-landlord-id="{{ $pad->userID }}" style="color:#000;">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm deletePadBtn p-1" data-id="{{ $pad->padID }}"
                                data-name="{{ $pad->padName }}" data-bs-toggle="modal" data-bs-target="#deletePadModal"
                                style="color:#fff;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">No pads found.</div>
                </div>
            @endforelse
        </div>

        @if($pads->hasPages())
            <div class="mt-3">
                {{ $pads->appends(request()->query())->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Create Pad Modal -->
    <div class="modal fade" id="createPadModal" tabindex="-1" aria-labelledby="createPadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <form method="POST" action="{{ route('admin.pads.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPadModalLabel">Create New Pad - Select Location</h5>
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
                                <input type="number" name="vacancy" id="createVacancy" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label>Status</label>
                                <input type="text" class="form-control" id="createPadStatusDisplay" value="Available" readonly>
                                <input type="hidden" name="padStatus" id="createPadStatus" value="Available">
                            </div>
                            <div class="mb-3">
                                <label for="createPadLandlord" class="form-label">Assign to Landlord <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="createPadLandlord" name="userID" required>
                                    <option value="">Select a Landlord</option>
                                    @if(isset($landlords))
                                    @foreach($landlords as $landlord)
                                    <option value="{{ $landlord->id }}" {{ old('userID')==$landlord->id ? 'selected' : ''
                                        }}>
                                        {{ $landlord->first_name }} {{ $landlord->last_name }} ({{ $landlord->email }})
                                    </option>
                                    @endforeach
                                    @else
                                    <option value="" disabled>No landlords available</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Images (Max 3)</label>
                                
                                <!-- Image Preview Container for Admin Create -->
                                <div id="imagePreviewContainerAdminCreate" class="mb-3">
                                    <div class="d-flex gap-2 flex-wrap">
                                        <!-- Will be populated by JavaScript when images are selected -->
                                    </div>
                                </div>
                                
                                <!-- Add Images -->
                                <input type="file" name="padImages[]" id="imageInputAdminCreate" class="form-control" multiple accept="image/*" max="3">
                                <small class="form-text text-muted">You can select up to 3 images. The first image will be the main image.</small>
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
                <input type="hidden" name="form_type" value="edit">
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
                            <input type="hidden" name="redirect_to" value="index">
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
                                <label for="editPadLandlord" class="form-label">Assign to Landlord <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="editPadLandlord" name="userID" required>
                                    <option value="">Select a Landlord</option>
                                    @if(isset($landlords))
                                        @foreach($landlords as $landlord)
                                            <option value="{{ $landlord->id }}">
                                                {{ $landlord->first_name }} {{ $landlord->last_name }} ({{ $landlord->email }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No landlords available</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label>Images (Max 3)</label>
                                
                                <!-- Current Images Display -->
                                <div id="currentImagesEditAdmin" class="mb-3">
                                    <div class="d-flex gap-2 flex-wrap" id="imagePreviewContainerAdmin">
                                        <!-- Will be populated by JavaScript -->
                                    </div>
                                </div>
                                
                                <!-- Add Images -->
                                <input type="file" name="padImages[]" id="imageInputAdmin" class="form-control" multiple accept="image/*" max="3">
                                <small class="form-text text-muted">You can select up to 3 images. The first image will be the main image.</small>
                                
                                <!-- Hidden inputs for removed images -->
                                <div id="removedImagesInputsAdmin"></div>
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
                        <p>Are you sure you want to delete pad: <strong id="deletePadName"></strong>?</p>
                        <p>This action cannot be undone.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Pad</button>
                    </div>
                </div>
            </form>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {

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
                        const defaultLatLng = [7.9042, 125.0928];
                        map = L.map('map', {
                            zoomControl: true,
                            zoomControlOptions: {
                                position: 'topright'
                            }
                        }).setView(defaultLatLng, 15);

                        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                            attribution: '© OpenStreetMap contributors'
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
                                    map.setView(defaultLatLng, 15);
                                });
                                
                                return container;
                            },
                            onRemove: function(map) {}
                        });

                        // Add reset control to map
                        new L.Control.ResetView({ position: 'topleft' }).addTo(map);

                        // Geocoder
                        L.Control.geocoder({
                            defaultMarkGeocode: false,
                            position: 'topright'
                        })
                            .on('markgeocode', function (e) {
                                const center = e.geocode.center;
                                map.setView(center, 15);

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
                        const defaultLatLng = [7.9042, 125.0928];
                        map.setView(defaultLatLng, 15);
                    }

                // Reset form fields (including hidden latitude, longitude, location input)
                document.getElementById('latitude').value = '';
                document.getElementById('longitude').value = '';
                document.getElementById('padLocation').value = '';

                // Reset status fields to default
                document.getElementById('createPadStatusDisplay').value = 'Available';
                document.getElementById('createPadStatus').value = 'Available';

                // Clear image previews for admin create modal
                const imagePreviewContainerAdminCreate = document.getElementById('imagePreviewContainerAdminCreate');
                if (imagePreviewContainerAdminCreate) {
                    imagePreviewContainerAdminCreate.innerHTML = '<div class="d-flex gap-2 flex-wrap"></div>';
                }

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

            // Add vacancy change listener for create modal
            const createVacancyInput = document.getElementById('createVacancy');
            const createPadStatusDisplay = document.getElementById('createPadStatusDisplay');
            const createPadStatusHidden = document.getElementById('createPadStatus');

            if (createVacancyInput && createPadStatusDisplay && createPadStatusHidden) {
                createVacancyInput.addEventListener('input', function () {
                    const vacancy = parseInt(createVacancyInput.value, 10);
                    if (vacancy === 0) {
                        createPadStatusDisplay.value = 'Fully Occupied';
                        createPadStatusHidden.value = 'Fullyoccupied';
                    } else {
                        createPadStatusDisplay.value = 'Available';
                        createPadStatusHidden.value = 'Available';
                    }
                });
            }

            // Edit Pad Modal
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
                    editMap = L.map('editMap', {
                        zoomControl: true,
                        zoomControlOptions: {
                            position: 'topright'
                        }
                                            }).setView([lat, lng], 15);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(editMap);

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
                    new L.Control.ResetView({ position: 'topleft' }).addTo(editMap);

                    // Add geocoder control for searching locations in edit modal
                    L.Control.geocoder({
                        defaultMarkGeocode: false,
                        position: 'topright'
                    })
                        .on('markgeocode', function (e) {
                            const center = e.geocode.center;
                            editMap.setView(center, 15);
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
                    editMap.setView([lat, lng], 15);
                    if (editMarker) editMap.removeLayer(editMarker);
                    editMarker = L.marker([lat, lng]).addTo(editMap);
                }
                editMap.invalidateSize();
            }

            // Function to load current images for admin editing
            function loadCurrentImagesAdmin(padId) {
                // Fetch current images from the server
                fetch(`/admin/pads/${padId}/images`)
                    .then(response => response.json())
                    .then(data => {
                        const imagePreviewContainer = document.getElementById('imagePreviewContainerAdmin');
                        imagePreviewContainer.innerHTML = '';
                        
                        if (data.images && data.images.length > 0) {
                            data.images.forEach((image, index) => {
                                const imageDiv = document.createElement('div');
                                imageDiv.className = 'position-relative';
                                imageDiv.style.cssText = 'width: 100px; height: 100px;';
                                imageDiv.innerHTML = `
                                    <img src="/storage/${image}" class="img-fluid rounded" style="width: 100px; height: 100px; object-fit: cover;">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle" 
                                            style="width: 25px; height: 25px; padding: 0; font-size: 12px; transform: translate(50%, -50%);"
                                            onclick="removeImageAdmin(${index}, '${image}')">
                                        ×
                                    </button>
                                    ${index === 0 ? '<small class="position-absolute bottom-0 start-0 bg-primary text-white px-1 rounded" style="font-size: 10px;">Main</small>' : ''}
                                `;
                                imagePreviewContainer.appendChild(imageDiv);
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error loading images:', error);
                    });
            }

            // Function to remove image for admin
            window.removeImageAdmin = function(index, imagePath) {
                // Add hidden input to track removed images
                const removedImagesContainer = document.getElementById('removedImagesInputsAdmin');
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'removed_images[]';
                hiddenInput.value = imagePath;
                removedImagesContainer.appendChild(hiddenInput);
                
                // Remove the image preview
                event.target.parentElement.remove();
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
                        longitude: this.dataset.longitude || '',
                        landlordId: this.dataset.landlordId || ''
                    };

                    // Fill form fields
                    document.getElementById('editPadId').value = this.dataset.id || '';
                    document.getElementById('editPadName').value = this.dataset.name || '';
                    document.getElementById('editPadDescription').value = this.dataset.description || '';
                    document.getElementById('editPadLocation').value = this.dataset.location || '';
                    document.getElementById('editPadRent').value = this.dataset.rent || '';
                    document.getElementById('editPadVacancy').value = this.dataset.vacancy || '';
                    document.getElementById('editLatitude').value = this.dataset.latitude || '';
                    document.getElementById('editLongitude').value = this.dataset.longitude || '';
                    document.getElementById('editPadLandlord').value = this.dataset.landlordId || '';
                    document.getElementById('editPadForm').action = '/admin/pads/' + this.dataset.id;

                    // Set initial status based on vacancy
                    const vacancy = parseInt(this.dataset.vacancy || '0', 10);
                    if (vacancy === 0) {
                        document.getElementById('editPadStatusDisplay').value = 'Fully Occupied';
                        document.getElementById('editPadStatus').value = 'Fullyoccupied';
                    } else {
                        document.getElementById('editPadStatusDisplay').value = 'Available';
                        document.getElementById('editPadStatus').value = 'Available';
                    }

                    // Load current images
                    loadCurrentImagesAdmin(this.dataset.id);

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

            // Delete Pad buttons
            document.querySelectorAll('.deletePadBtn').forEach(function (button) {
                button.addEventListener('click', function () {
                    const nameElem = document.getElementById('deletePadName');
                    const formElem = document.getElementById('deletePadForm');
                    if (nameElem && formElem) {
                        nameElem.textContent = this.dataset.name;
                        formElem.action = '/admin/pads/' + this.dataset.id;
                    }
                });
            });

    });

    // Handle image input preview for admin edit modal
    const imageInputAdmin = document.getElementById('imageInputAdmin');
    if (imageInputAdmin) {
        imageInputAdmin.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const imagePreviewContainer = document.getElementById('imagePreviewContainerAdmin');
            
            // Count existing images (not new previews)
            const existingImages = imagePreviewContainer.querySelectorAll('div:not(.new-image-preview)').length;
            
            // Check if total would exceed 3
            if (existingImages + files.length > 3) {
                showImageLimitModal(existingImages, files.length);
                e.target.value = ''; // Clear the file input
                return;
            }
            
            // Clear existing previews of new files
            const existingPreviews = imagePreviewContainer.querySelectorAll('.new-image-preview');
            existingPreviews.forEach(preview => preview.remove());
            
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const imageDiv = document.createElement('div');
                        imageDiv.className = 'position-relative new-image-preview';
                        imageDiv.style.cssText = 'width: 100px; height: 100px;';
                        imageDiv.innerHTML = `
                            <img src="${e.target.result}" class="img-fluid rounded" style="width: 100px; height: 100px; object-fit: cover;">
                            <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle" 
                                    style="width: 25px; height: 25px; padding: 0; font-size: 12px; transform: translate(50%, -50%);"
                                    onclick="removeNewImageAdmin(this)">
                                ×
                            </button>
                            <small class="position-absolute bottom-0 start-0 bg-success text-white px-1 rounded" style="font-size: 10px;">New</small>
                        `;
                        imagePreviewContainer.appendChild(imageDiv);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }

    // Handle image input preview for admin create modal
    const imageInputAdminCreate = document.getElementById('imageInputAdminCreate');
    if (imageInputAdminCreate) {
        imageInputAdminCreate.addEventListener('change', function(e) {
            const files = Array.from(e.target.files);
            const imagePreviewContainer = document.getElementById('imagePreviewContainerAdminCreate');
            
            // Check if total would exceed 3
            if (files.length > 3) {
                showImageLimitModal(0, files.length);
                e.target.value = ''; // Clear the file input
                return;
            }
            
            // Clear existing previews
            imagePreviewContainer.innerHTML = '<div class="d-flex gap-2 flex-wrap"></div>';
            const flexContainer = imagePreviewContainer.querySelector('.d-flex');
            
                         files.forEach((file, index) => {
                 if (file.type.startsWith('image/')) {
                     const reader = new FileReader();
                     reader.onload = function(e) {
                         const imageDiv = document.createElement('div');
                         imageDiv.className = 'position-relative new-image-preview';
                         imageDiv.style.cssText = 'width: 100px; height: 100px;';
                         imageDiv.innerHTML = `
                             <img src="${e.target.result}" class="img-fluid rounded" style="width: 100px; height: 100px; object-fit: cover;">
                             <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 rounded-circle" 
                                     style="width: 25px; height: 25px; padding: 0; font-size: 12px; transform: translate(50%, -50%);"
                                     onclick="removeNewImageAdminCreate(this)">
                                 ×
                             </button>
                         `;
                         flexContainer.appendChild(imageDiv);
                         
                         // Update badges after adding image
                         updateImageBadgesAdminCreate();
                     };
                     reader.readAsDataURL(file);
                 }
             });
        });
    }

    // Function to remove new image preview for admin
    window.removeNewImageAdmin = function(button) {
        button.parentElement.remove();
        // Reset file input
        document.getElementById('imageInputAdmin').value = '';
    }

    // Function to remove new image preview for admin create modal
    window.removeNewImageAdminCreate = function(button) {
        button.parentElement.remove();
        // Reset file input
        document.getElementById('imageInputAdminCreate').value = '';
    }

    // Function to make image main for admin create modal
    window.makeMainImageAdminCreate = function(clickedImage) {
        const container = document.getElementById('imagePreviewContainerAdminCreate').querySelector('.d-flex');
        const allImages = Array.from(container.querySelectorAll('.new-image-preview'));
        
        // Find the clicked image index
        const clickedIndex = allImages.indexOf(clickedImage);
        
        if (clickedIndex > 0) {
            // Remove clicked image from its current position
            const imageToMove = allImages[clickedIndex];
            container.removeChild(imageToMove);
            
            // Insert it at the beginning
            container.insertBefore(imageToMove, container.firstChild);
            
            // Update all badges
            updateImageBadgesAdminCreate();
        }
    }

    // Function to update image badges for admin create modal
    function updateImageBadgesAdminCreate() {
        const container = document.getElementById('imagePreviewContainerAdminCreate').querySelector('.d-flex');
        const allImages = container.querySelectorAll('.new-image-preview');
        
        allImages.forEach((imageDiv, index) => {
            // Remove existing badge
            const existingBadge = imageDiv.querySelector('small');
            if (existingBadge) {
                existingBadge.remove();
            }
            
            // Add new badge
            if (index === 0) {
                const mainBadge = document.createElement('small');
                mainBadge.className = 'position-absolute bottom-0 start-0 bg-primary text-white px-1 rounded';
                mainBadge.style.fontSize = '10px';
                mainBadge.style.cursor = 'pointer';
                mainBadge.textContent = 'Main';
                mainBadge.onclick = function() { makeMainImageAdminCreate(imageDiv); };
                imageDiv.appendChild(mainBadge);
            } else {
                const numberBadge = document.createElement('small');
                numberBadge.className = 'position-absolute bottom-0 start-0 bg-secondary text-white px-1 rounded';
                numberBadge.style.fontSize = '10px';
                numberBadge.style.cursor = 'pointer';
                numberBadge.textContent = index + 1;
                numberBadge.onclick = function() { makeMainImageAdminCreate(imageDiv); };
                imageDiv.appendChild(numberBadge);
            }
        });
    }

    // Function to show image limit modal
    function showImageLimitModal(existingCount, selectedCount) {
        const modalHtml = `
            <div class="modal fade" id="imageLimitModal" tabindex="-1" aria-labelledby="imageLimitModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-sm">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-warning text-white border-0">
                            <h6 class="modal-title" id="imageLimitModalLabel">
                                <i class="fas fa-exclamation-triangle me-2"></i>Image Limit Exceeded
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body text-center py-3">
                            <p class="mb-0">Maximum of 3 images allowed</p>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if any
        const existingModal = document.getElementById('imageLimitModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Add modal to body
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('imageLimitModal'));
        modal.show();
        
        // Remove modal from DOM after it's hidden
        document.getElementById('imageLimitModal').addEventListener('hidden.bs.modal', function() {
            this.remove();
        });
    }

    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
@endsection


@push('scripts')
    <style>
        .pad-img {
            /* Copied from your original admin index if you had it */
            width: 100%;
            height: 160px;
            /* Or your preferred height */
            object-fit: cover;
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

@php
    $statusDisplay = [
        'Available' => 'Available',
        'Fullyoccupied' => 'Fully Occupied',
        'Maintenance' => 'Maintenance'
    ];
@endphp