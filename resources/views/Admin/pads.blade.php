@extends('layouts.app')

@section('content')
<h3>All Pads (Admin)</h3>
<form method="GET" action="{{ route('admin.pads.index') }}" class="mb-3" style="max-width: 400px;">
    <div class="input-group">
        <input type="text" name="search" class="form-control" placeholder="Search pad..." value="{{ request('search') }}">
        <button class="btn btn-primary" type="submit">Search</button>
    </div>
</form>

<div class="row">
    @foreach($pads as $pad)
        <div class="col-md-3 mb-4">
            <div class="card h-100 shadow-sm">
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
                    <p class="card-text text-muted mb-1">Landlord: {{ $pad->landlord->first_name ?? 'N/A' }} {{ $pad->landlord->last_name ?? '' }}</p>
                </div>
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

<!-- Reuse your modals for create/edit/delete here, just like in landlord view -->
@endsection
