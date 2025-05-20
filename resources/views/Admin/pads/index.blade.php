@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h2>All Pads (Admin View)</h2>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPadModal">
                Create New Pad
            </button>
        </div>

        <form method="GET" action="{{ route('admin.pads.index') }}" class="mb-3">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" name="search" class="form-control"
                            placeholder="Search pads by name, location, landlord..." value="{{ request('search') }}">
                        <button class="btn btn-outline-secondary" type="submit">Search</button>
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
                        @foreach($pads->pluck('padLocation')->unique() as $location)
                            <option value="{{ $location }}" {{ request('location_filter') == $location ? 'selected' : '' }}>
                                {{ $location }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="price_filter" class="form-select" onchange="this.form.submit()">
                        <option value="">All Prices</option>
                        <option value="below_1000" {{ request('price_filter') == 'below_1000' ? 'selected' : '' }}>Below
                            ₱1,000</option>
                        <option value="1000_2000" {{ request('price_filter') == '1000_2000' ? 'selected' : '' }}>₱1,000 -
                            ₱2,000
                        </option>
                        <option value="2000_3000" {{ request('price_filter') == '2000_3000' ? 'selected' : '' }}>₱2,000 -
                            ₱3,000
                        </option>
                        <option value="above_3000" {{ request('price_filter') == 'above_3000' ? 'selected' : '' }}>Above
                            ₱3,000</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <a href="{{ route('admin.pads.index') }}" class="btn btn-outline-secondary w-100">Reset Filters</a>
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
                    <div class="card h-100 shadow-sm">
                        <a href="{{ route('admin.pads.show', $pad->padID) }}" style="text-decoration: none; color: inherit;">
                            @if($pad->padImage)
                                <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top"
                                    style="height: 160px; object-fit: cover;" alt="{{ $pad->padName }}">
                            @else
                                <img src="https://via.placeholder.com/300x160?text=No+Image" class="card-img-top"
                                    style="height: 160px; object-fit: cover;" alt="No Image">
                            @endif
                            <div class="card-body">
                                <h5 class="card-title">{{ $pad->padName }}</h5>
                                <p class="card-text">{{ Str::limit($pad->padDescription, 50) }}</p>
                                <p class="card-text"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                                <p class="card-text text-muted mb-1">₱{{ number_format($pad->padRent, 2) }}</p>
                                <p class="card-text text-muted mb-1">Status: {{ ucfirst($pad->padStatus) }}</p>
                                <p class="card-text text-muted mb-1">Landlord: {{ $pad->landlord->first_name ?? 'N/A' }}
                                    {{ $pad->landlord->last_name ?? '' }}
                                </p>
                                <p class="card-text text-muted mb-1"><strong>Boarders:</strong>
                                    {{ $pad->number_of_boarders ?? 0 }}</p>
                            </div>
                        </a>
                        <div class="card-footer bg-white border-0 d-flex gap-2 mt-3">
                            <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editPadModal" data-id="{{ $pad->padID }}" data-name="{{ $pad->padName }}"
                                data-description="{{ $pad->padDescription }}" data-location="{{ $pad->padLocation }}"
                                data-rent="{{ $pad->padRent }}" data-status="{{ $pad->padStatus }}"
                                data-landlord-id="{{ $pad->userID }}"
                                data-image-url="{{ $pad->padImage ? asset('storage/' . $pad->padImage) : '' }}">
                                Edit
                            </button>
                            <form action="{{ route('admin.pads.destroy', $pad->padID) }}" method="POST"
                                onsubmit="return confirm('Are you sure you want to delete this pad?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col">
                    <div class="text-center text-muted py-3">No pads found.</div>
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
                <input type="hidden" name="form_type" value="create">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createPadModalLabel">Create New Pad - Select Location</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Step 1: Map -->
                        <div id="mapStep">
                            <div id="map" style="height: 600px;"></div>
                            <input type="hidden" name="latitude" id="latitude">
                            <input type="hidden" name="longitude" id="longitude">
                            <div class="mb-3">
                                <label for="createPadLocation" class="form-label">Location <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="padLocation" id="createPadLocation" class="form-control" required>
                            </div>
                        </div>

                        <!-- Step 2: Form -->
                        <div id="formStep" style="display: none;">
                            <div class="mb-3">
                                <label for="createPadName" class="form-label">Name <span
                                        class="text-danger">*</span></label>
                                <input type="text" name="padName" id="createPadName" class="form-control"
                                    value="{{ old('padName') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="createPadDescription" class="form-label">Description</label>
                                <textarea name="padDescription" id="createPadDescription" class="form-control"
                                    rows="3">{{ old('padDescription') }}</textarea>
                            </div>
                            <div class="mb-3">
                                <label for="createPadRent" class="form-label">Rent (₱) <span
                                        class="text-danger">*</span></label>
                                <input type="number" name="padRent" id="createPadRent" class="form-control" step="0.01"
                                    value="{{ old('padRent') }}" required>
                            </div>
                            <div class="mb-3">
                                <label for="createPadStatus" class="form-label">Status <span
                                        class="text-danger">*</span></label>
                                <select name="padStatus" id="createPadStatus" class="form-select" required>
                                    <option value="available" {{ old('padStatus') == 'available' ? 'selected' : '' }}>
                                        Available</option>
                                    <option value="occupied" {{ old('padStatus') == 'occupied' ? 'selected' : '' }}>Occupied
                                    </option>
                                    <option value="maintenance" {{ old('padStatus') == 'maintenance' ? 'selected' : '' }}>
                                        Maintenance</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="createPadLandlord" class="form-label">Assign to Landlord <span
                                        class="text-danger">*</span></label>
                                <select class="form-select" id="createPadLandlord" name="userID" required>
                                    <option value="">Select a Landlord</option>
                                    @if(isset($landlords))
                                        @foreach($landlords as $landlord)
                                            <option value="{{ $landlord->id }}" {{ old('userID') == $landlord->id ? 'selected' : '' }}>
                                                {{ $landlord->first_name }} {{ $landlord->last_name }} ({{ $landlord->email }})
                                            </option>
                                        @endforeach
                                    @else
                                        <option value="" disabled>No landlords available</option>
                                    @endif
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="createPadImage" class="form-label">Pad Image</label>
                                <input type="file" name="padImage" id="createPadImage" class="form-control"
                                    accept="image/*">
                            </div>
                        </div>
                        <div class="modal-footer">
                        <button type="button" class="btn btn-danger" id="cancelButtonCreate">Cancel</button>
                        <button type="button" class="btn btn-secondary" id="backButton" style="display: none;">Back</button>
                        <button type="button" class="btn btn-primary" id="nextButton">Next</button>
                        <button type="submit" class="btn btn-primary" id="submitButton"
                            style="display: none;">Create New Pad</button>
                    </div>

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
                        <h5 class="modal-title" id="editPadModalLabel">Edit Pad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        {{-- Step 1: Location --}}
                        <div id="mapStepEdit">
                            <div id="editMap" style="height: 600px;"></div>
                            <input type="hidden" name="latitude" id="editLatitude">
                            <input type="hidden" name="longitude" id="editLongitude">
                            <div class="mb-3">
                                <label for="editPadLocationModal" class="form-label">Location <span
                                    class="text-danger">*</span></label>
                                <input type="text" name="padLocation" id="editPadLocationModal" class="form-control" required>
                            </div>
                        </div>

                        {{-- <input type="hidden" name="padID" id="editPadId"> --}} {{-- Not needed if action URL has ID
                        --}}
                         {{-- Step 2: Form --}}
                        <div id="formStepEdit" style="display: none;">
                            <div class="mb-3">
                            <label for="editPadNameModal" class="form-label">Name <span class="text-danger">*</span></label>
                            <input type="text" name="padName" id="editPadNameModal" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="editPadDescriptionModal" class="form-label">Description</label>
                            <textarea name="padDescription" id="editPadDescriptionModal" class="form-control"
                                rows="3"></textarea>
                        </div>
                        {{-- <div class="mb-3">
                            <label for="editPadLocationModal" class="form-label">Location <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="padLocation" id="editPadLocationModal" class="form-control" required>
                        </div> --}}
                        <div class="mb-3">
                            <label for="editPadRentModal" class="form-label">Rent (₱) <span
                                    class="text-danger">*</span></label>
                            <input type="number" name="padRent" id="editPadRentModal" class="form-control" step="0.01"
                                required>
                        </div>
                        <div class="mb-3">
                            <label for="editPadStatusModal" class="form-label">Status <span
                                    class="text-danger">*</span></label>
                            <select name="padStatus" id="editPadStatusModal" class="form-select" required>
                                <option value="available">Available</option>
                                <option value="occupied">Occupied</option>
                                <option value="maintenance">Maintenance</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="editPadLandlordModal" class="form-label">Assign to Landlord <span
                                    class="text-danger">*</span></label>
                            <select class="form-select" id="editPadLandlordModal" name="userID" required>
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
                            <label for="editPadImageModal" class="form-label">New Pad Image (Optional)</label>
                            <input type="file" name="padImage" id="editPadImageModal" class="form-control" accept="image/*">
                            <img id="currentPadImageModal" src="#" alt="Current Image" class="img-thumbnail mt-2"
                                style="max-height: 100px; display: none;" />
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
                        <p>Are you sure you want to delete pad: <strong id="padNameToDelete"></strong>?</p>
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
    </style>


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

                                reverseGeocode(center.lat, center.lng, 'createPadLocation');
                            })
                            .addTo(map);

                        map.on('click', function (e) {
                            if (marker) map.removeLayer(marker);
                            marker = L.marker(e.latlng).addTo(map);

                            document.getElementById('latitude').value = e.latlng.lat;
                            document.getElementById('longitude').value = e.latlng.lng;

                            reverseGeocode(e.latlng.lat, e.latlng.lng, 'createPadLocation');
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
                    createPadModalLabel.innerText = 'Create New Pad - Fill Details';
                });

                backButton.addEventListener('click', function () {
                    mapStep.style.display = 'block';
                    formStep.style.display = 'none';
                    nextButton.style.display = 'inline-block';
                    submitButton.style.display = 'none';
                    backButton.style.display = 'none';
                    createPadModalLabel.innerText = 'Create New Pad - Select Location';

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

                createPadModalLabel.innerText = 'Create New Pad - Select Location';

                // Hide the modal (Bootstrap 5)
                const modalInstance = bootstrap.Modal.getInstance(createPadModal);
                if (modalInstance) {
                    modalInstance.hide();
                }
            });


            // Re-show modal on validation errors
            @if ($errors->any())
                @if (old('form_type') === 'create')
                    var createModal = new bootstrap.Modal(document.getElementById('createPadModal'));
                    createModal.show();
                @elseif (old('form_type') === 'edit' && old('padID_for_edit'))
                    // For edit, repopulate and show
                    var editModalEl = document.getElementById('editPadModal');
                    var editModal = new bootstrap.Modal(editModalEl);
                    const padID = '{{ old("padID_for_edit") }}'; // Get failed padID from old input

                    // Set form action (important if it wasn't set before page reload with error)
                    editModalEl.querySelector('#editPadForm').action = `{{ url('admin/pads') }}/${padID}`;

                    // Repopulate fields with old input if available, otherwise use data from button if modal was triggered by button before error
                    // This part is tricky because data-attributes are not available after a page reload due to validation error
                    // So, relying purely on old() input is more robust here for error recovery.
                    document.getElementById('editPadNameModal').value = '{{ old("padName", "") }}';
                    document.getElementById('editPadDescriptionModal').value = '{{ old("padDescription", "") }}';
                    document.getElementById('editPadLocationModal').value = '{{ old("padLocation", "") }}';
                    document.getElementById('editPadRentModal').value = '{{ old("padRent", "") }}';
                    document.getElementById('editPadStatusModal').value = '{{ old("padStatus", "available") }}';
                    document.getElementById('editPadLandlordModal').value = '{{ old("userID", "") }}';
                    // Image cannot be repopulated in file input for security reasons
                    editModal.show();
                @endif
            @endif

        // Edit Pad Modal
        const editPadModalEl = document.getElementById('editPadModal');
        if (editPadModalEl) {
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
                        reverseGeocode(e.latlng.lat, e.latlng.lng, 'editPadLocationModal');
                    });
                } else {
                    editMap.setView([lat, lng], 14);
                    if (editMarker) editMap.removeLayer(editMarker);
                    editMarker = L.marker([lat, lng]).addTo(editMap);
                }
                editMap.invalidateSize();
            }

            editPadModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                const padID = button.getAttribute('data-id');
                const form = editPadModalEl.querySelector('#editPadForm');
                form.action = `{{ url('admin/pads') }}/${padID}`;

                // Hidden input
                let padIdInput = form.querySelector('input[name="padID_for_edit"]');
                if (!padIdInput) {
                    padIdInput = document.createElement('input');
                    padIdInput.type = 'hidden';
                    padIdInput.name = 'padID_for_edit';
                    form.appendChild(padIdInput);
                }
                padIdInput.value = padID;

                // Set values
                document.getElementById('editPadNameModal').value = button.getAttribute('data-name');
                document.getElementById('editPadDescriptionModal').value = button.getAttribute('data-description');
                document.getElementById('editPadLocationModal').value = button.getAttribute('data-location');
                document.getElementById('editPadRentModal').value = button.getAttribute('data-rent');
                document.getElementById('editPadStatusModal').value = button.getAttribute('data-status');
                document.getElementById('editPadLandlordModal').value = button.getAttribute('data-landlord-id');

                const currentImage = document.getElementById('currentPadImageModal');
                const imageUrl = button.getAttribute('data-image-url');
                if (imageUrl) {
                    currentImage.src = imageUrl;
                    currentImage.style.display = 'block';
                } else {
                    currentImage.style.display = 'none';
                }
                document.getElementById('editPadImageModal').value = '';

                // Add map-related values
                originalEditPadData = {
                    id: padID || '',
                    name: button.getAttribute('data-name') || '',
                    description: button.getAttribute('data-description') || '',
                    location: button.getAttribute('data-location') || '',
                    rent: button.getAttribute('data-rent') || '',
                    status: button.getAttribute('data-status') || '',
                    latitude: button.getAttribute('data-latitude') || '',
                    longitude: button.getAttribute('data-longitude') || ''
                };

                document.getElementById('editLatitude').value = originalEditPadData.latitude || '';
                document.getElementById('editLongitude').value = originalEditPadData.longitude || '';

                const lat = parseFloat(originalEditPadData.latitude) || 7.9092;
                const lng = parseFloat(originalEditPadData.longitude) || 125.0949;

                mapStepEdit.style.display = 'block';
                formStepEdit.style.display = 'none';
                nextButtonEdit.style.display = 'inline-block';
                backButtonEdit.style.display = 'none';
                submitButtonEdit.style.display = 'none';
                EditPadModalLabel.innerText = 'Edit Pad - Update Location';

                setTimeout(() => {
                    setMarkerOnMap(lat, lng);
                }, 300);
            });

            const cancelEditBtn = document.getElementById('cancelButtonEdit');
            if (cancelEditBtn) {
                cancelEditBtn.addEventListener('click', function () {
                    document.getElementById('editPadNameModal').value = originalEditPadData.name;
                    document.getElementById('editPadDescriptionModal').value = originalEditPadData.description;
                    document.getElementById('editPadLocationModal').value = originalEditPadData.location;
                    document.getElementById('editPadRentModal').value = originalEditPadData.rent;
                    document.getElementById('editPadStatusModal').value = originalEditPadData.status;
                    document.getElementById('editLatitude').value = originalEditPadData.latitude;
                    document.getElementById('editLongitude').value = originalEditPadData.longitude;


                    EditPadModalLabel.innerText = 'Edit Pad - Update Location';
                    const modal = bootstrap.Modal.getInstance(document.getElementById('editPadModal'));
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
                EditPadModalLabel.innerText = 'Edit Pad - Update Details';
            });

            backButtonEdit.addEventListener('click', function () {
                mapStepEdit.style.display = 'block';
                formStepEdit.style.display = 'none';
                nextButtonEdit.style.display = 'inline-block';
                backButtonEdit.style.display = 'none';
                submitButtonEdit.style.display = 'none';
                EditPadModalLabel.innerText = 'Edit Pad - Update Location';

                const lat = parseFloat(document.getElementById('editLatitude').value) || 7.9092;
                const lng = parseFloat(document.getElementById('editLongitude').value) || 125.0949;
                setMarkerOnMap(lat, lng);
            });
        }


                // Delete Pad Modal
        const deletePadModalEl = document.getElementById('deletePadModal');
        if (deletePadModalEl) {
            deletePadModalEl.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                const padID = button.getAttribute('data-id');
                const padName = button.getAttribute('data-name');
                const form = deletePadModalEl.querySelector('#deletePadForm');

                form.action = `{{ url('admin/pads') }}/${padID}`;
                deletePadModalEl.querySelector('#padNameToDelete').textContent = padName;
            });
        }
    });
    </script>

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
@endpush