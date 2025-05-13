@extends('layouts.app')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-3">
    <form method="GET" action="{{ route('landlord.pads.index') }}" class="flex-grow-1 me-3" style="max-width: 500px;">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search pad..." value="{{ request('search') }}">
            <button class="btn btn-primary" type="submit">Search</button>
        </div>
    </form>
    <a href="#" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createPadModal">
        Add New Pad
    </a>
</div>

<div class="row" id="pad-list">
    @foreach($pads as $pad)
        <div class="col-md-3 mb-4" id="pad-card-{{ $pad->padID }}">
            <div class="card h-100 shadow-sm">
                <a href="{{ route('pads.show', $pad->padID) }}" style="text-decoration: none; color: inherit;">
                    @if($pad->padImage)
                        <img src="{{ asset('storage/' . $pad->padImage) }}" class="card-img-top" style="height: 160px; object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/300x160?text=No+Image" class="card-img-top" style="height: 160px; object-fit: cover;">
                    @endif
                    <div class="card-body">
                        <h5 class="card-title">{{ $pad->padName }}</h5>
                        <p class="card-text">{{ $pad->padLocation }}</p>
                        <p class="card-text text-muted mb-1">₱{{ number_format($pad->padRent, 2) }}</p>
                        <p class="card-text text-muted mb-1">Status: {{ ucfirst($pad->padStatus) }}</p>
                    </div>
                </a>
                <div class="card-footer bg-white border-0 d-flex justify-content-between">
                    <button class="btn btn-sm btn-warning editPadBtn"
                        data-id="{{ $pad->padID }}"
                        data-name="{{ $pad->padName }}"
                        data-description="{{ $pad->padDescription }}"
                        data-location="{{ $pad->padLocation }}"
                        data-rent="{{ $pad->padRent }}"
                        data-status="{{ $pad->padStatus }}"
                        data-bs-toggle="modal" data-bs-target="#editPadModal">
                        Edit
                    </button>
                    <button class="btn btn-sm btn-danger deletePadBtn"
                        data-id="{{ $pad->padID }}"
                        data-name="{{ $pad->padName }}"
                        data-bs-toggle="modal" data-bs-target="#deletePadModal">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endforeach
</div>

@if($pads->isEmpty())
    <div class="text-center text-muted py-3">No pads found.</div>
@endif

<div class="mt-3">
    {{ $pads->links('pagination::bootstrap-5') }}
</div>

<!-- Create Pad Modal -->
<div class="modal fade" id="createPadModal" tabindex="-1" aria-labelledby="createPadModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('pads.store') }}" enctype="multipart/form-data">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createPadModalLabel">Add New Pad</h5>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Name</label>
            <input type="text" name="padName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Description</label>
            <textarea name="padDescription" class="form-control"></textarea>
          </div>
          <div class="mb-3">
            <label>Location</label>
            <input type="text" name="padLocation" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Rent</label>
            <input type="number" name="padRent" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Status</label>
            <select name="padStatus" class="form-select" required>
              <option value="available">Available</option>
              <option value="occupied">Occupied</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Image</label>
            <input type="file" name="padImage" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create</button>
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
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editPadModalLabel">Edit Pad</h5>
        </div>
        <div class="modal-body">
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
            <label>Location</label>
            <input type="text" name="padLocation" id="editPadLocation" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Rent</label>
            <input type="number" name="padRent" id="editPadRent" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Status</label>
            <select name="padStatus" id="editPadStatus" class="form-select" required>
              <option value="available">Available</option>
              <option value="occupied">Occupied</option>
              <option value="maintenance">Maintenance</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Image</label>
            <input type="file" name="padImage" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Update</button>
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
    // Edit Pad
    document.querySelectorAll('.editPadBtn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('editPadId').value = this.dataset.id;
            document.getElementById('editPadName').value = this.dataset.name;
            document.getElementById('editPadDescription').value = this.dataset.description;
            document.getElementById('editPadLocation').value = this.dataset.location;
            document.getElementById('editPadRent').value = this.dataset.rent;
            document.getElementById('editPadStatus').value = this.dataset.status;
            document.getElementById('editPadForm').action = '/pads/' + this.dataset.id;
        });
    });

    // Delete Pad
    document.querySelectorAll('.deletePadBtn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('deletePadName').textContent = this.dataset.name;
            document.getElementById('deletePadForm').action = '/pads/' + this.dataset.id;
        });
    });
});
</script>
@endsection
