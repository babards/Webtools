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
                    <input type="text" name="search" class="form-control" placeholder="Search pads by name, location, landlord..." value="{{ request('search') }}">
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
                    <option value="below_1000" {{ request('price_filter') == 'below_1000' ? 'selected' : '' }}>Below ₱1,000</option>
                    <option value="1000_2000" {{ request('price_filter') == '1000_2000' ? 'selected' : '' }}>₱1,000 - ₱2,000</option>
                    <option value="2000_3000" {{ request('price_filter') == '2000_3000' ? 'selected' : '' }}>₱2,000 - ₱3,000</option>
                    <option value="above_3000" {{ request('price_filter') == 'above_3000' ? 'selected' : '' }}>Above ₱3,000</option>
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
                            <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top" style="height: 160px; object-fit: cover;" alt="{{ $pad->padName }}">
                        @else
                            <img src="https://via.placeholder.com/300x160?text=No+Image" class="card-img-top" style="height: 160px; object-fit: cover;" alt="No Image">
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $pad->padName }}</h5>
                            <p class="card-text">{{ Str::limit($pad->padDescription, 50) }}</p>
                            <p class="card-text"><strong>Location:</strong> {{ $pad->padLocation }}</p>
                            <p class="card-text text-muted mb-1">₱{{ number_format($pad->padRent, 2) }}</p>
                            <p class="card-text text-muted mb-1">Status: {{ ucfirst($pad->padStatus) }}</p>
                            <p class="card-text text-muted mb-1">Landlord: {{ $pad->landlord->first_name ?? 'N/A' }} {{ $pad->landlord->last_name ?? '' }}</p>
                            <p class="card-text text-muted mb-1"><strong>Boarders:</strong> {{ $pad->number_of_boarders ?? 0 }}</p> 
                        </div>
                    </a>
                    <div class="card-footer bg-white border-0 d-flex gap-2 mt-3">
                        <button 
                            type="button"
                            class="btn btn-warning btn-sm"
                            data-bs-toggle="modal"
                            data-bs-target="#editPadModal"
                            data-id="{{ $pad->padID }}"
                            data-name="{{ $pad->padName }}"
                            data-description="{{ $pad->padDescription }}"
                            data-location="{{ $pad->padLocation }}"
                            data-rent="{{ $pad->padRent }}"
                            data-status="{{ $pad->padStatus }}"
                            data-landlord-id="{{ $pad->userID }}"
                            data-image-url="{{ $pad->padImage ? asset('storage/' . $pad->padImage) : '' }}"
                        >
                            Edit
                        </button>
                        <form action="{{ route('landlord.pads.destroy', $pad->padID) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this pad?');">
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
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.pads.store') }}" enctype="multipart/form-data">
      @csrf
      <input type="hidden" name="form_type" value="create">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPadModalLabel">Create New Pad</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="createPadName" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="padName" id="createPadName" class="form-control" value="{{ old('padName') }}" required>
          </div>
          <div class="mb-3">
            <label for="createPadDescription" class="form-label">Description</label>
            <textarea name="padDescription" id="createPadDescription" class="form-control" rows="3">{{ old('padDescription') }}</textarea>
          </div>
          <div class="mb-3">
            <label for="createPadLocation" class="form-label">Location <span class="text-danger">*</span></label>
            <input type="text" name="padLocation" id="createPadLocation" class="form-control" value="{{ old('padLocation') }}" required>
          </div>
          <div class="mb-3">
            <label for="createPadRent" class="form-label">Rent (₱) <span class="text-danger">*</span></label>
            <input type="number" name="padRent" id="createPadRent" class="form-control" step="0.01" value="{{ old('padRent') }}" required>
          </div>
          <div class="mb-3">
            <label for="createPadStatus" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="padStatus" id="createPadStatus" class="form-select" required>
              <option value="available" {{ old('padStatus') == 'available' ? 'selected' : '' }}>Available</option>
              <option value="occupied" {{ old('padStatus') == 'occupied' ? 'selected' : '' }}>Occupied</option>
              <option value="maintenance" {{ old('padStatus') == 'maintenance' ? 'selected' : '' }}>Maintenance</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="createPadLandlord" class="form-label">Assign to Landlord <span class="text-danger">*</span></label>
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
            <input type="file" name="padImage" id="createPadImage" class="form-control" accept="image/*">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Pad</button>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Edit Pad Modal -->
<div class="modal fade" id="editPadModal" tabindex="-1" aria-labelledby="editPadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
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
          {{-- <input type="hidden" name="padID" id="editPadId"> --}} {{-- Not needed if action URL has ID --}}
          <div class="mb-3">
            <label for="editPadNameModal" class="form-label">Name <span class="text-danger">*</span></label>
            <input type="text" name="padName" id="editPadNameModal" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="editPadDescriptionModal" class="form-label">Description</label>
            <textarea name="padDescription" id="editPadDescriptionModal" class="form-control" rows="3"></textarea>
          </div>
          <div class="mb-3">
            <label for="editPadLocationModal" class="form-label">Location <span class="text-danger">*</span></label>
            <input type="text" name="padLocation" id="editPadLocationModal" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="editPadRentModal" class="form-label">Rent (₱) <span class="text-danger">*</span></label>
            <input type="number" name="padRent" id="editPadRentModal" class="form-control" step="0.01" required>
          </div>
          <div class="mb-3">
            <label for="editPadStatusModal" class="form-label">Status <span class="text-danger">*</span></label>
            <select name="padStatus" id="editPadStatusModal" class="form-select" required>
              <option value="available">Available</option>
              <option value="occupied">Occupied</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
          <div class="mb-3">
            <label for="editPadLandlordModal" class="form-label">Assign to Landlord <span class="text-danger">*</span></label>
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
            <img id="currentPadImageModal" src="#" alt="Current Image" class="img-thumbnail mt-2" style="max-height: 100px; display: none;"/>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Save Changes</button>
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
    .pad-img { /* Copied from your original admin index if you had it */
        width: 100%;
        height: 160px; /* Or your preferred height */
        object-fit: cover;
    }
</style>
<script>
document.addEventListener('DOMContentLoaded', function () {
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
        editPadModalEl.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget; // Button that triggered the modal
            if (!button) return; // Don't run if modal is shown programmatically without a button (e.g., on error)

            const padID = button.getAttribute('data-id');
            const form = editPadModalEl.querySelector('#editPadForm');
            form.action = `{{ url('admin/pads') }}/${padID}`; // Use url() for safety if route names change

            // Add padID to form data if needed for error repopulation
            let padIdInput = form.querySelector('input[name="padID_for_edit"]');
            if(!padIdInput) {
                padIdInput = document.createElement('input');
                padIdInput.type = 'hidden';
                padIdInput.name = 'padID_for_edit';
                form.appendChild(padIdInput);
            }
            padIdInput.value = padID;

            // Populate from data attributes
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
            document.getElementById('editPadImageModal').value = ''; // Clear file input
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
@endpush
