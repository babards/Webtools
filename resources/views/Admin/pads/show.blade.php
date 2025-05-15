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
                                @elseif($pad->padStatus == 'occupied') bg-danger
                                @else bg-warning text-dark
                                @endif
                            ">
                                {{ ucfirst($pad->padStatus) }}
                            </span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5 text-muted fw-bold">Number of Boarders:</div>
                        <div class="col-7">{{ $pad->number_of_boarders ?? 0 }}</div>
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
            </div>

            <div class="d-flex">
                <a href="{{ route('admin.pads.index') }}" class="btn btn-outline-secondary">
                    Back to List
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
