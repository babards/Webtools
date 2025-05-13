@extends('layouts.app')

@section('content')
<div class="container py-4">
    <h2>{{ $pad->padName }}</h2>
    @if($pad->padImage)
        <img src="{{ asset('storage/' . $pad->padImage) }}" class="mb-3" style="max-width: 100%; height: 300px; object-fit: cover;">
    @else
        <img src="https://via.placeholder.com/600x300?text=No+Image" class="mb-3" style="max-width: 100%; height: 300px; object-fit: cover;">
    @endif

    <dl class="row">
        <dt class="col-sm-3">Description:</dt>
        <dd class="col-sm-9">{{ $pad->padDescription }}</dd>

        <dt class="col-sm-3">Location:</dt>
        <dd class="col-sm-9">{{ $pad->padLocation }}</dd>

        <dt class="col-sm-3">Rent:</dt>
        <dd class="col-sm-9">₱{{ number_format($pad->padRent, 2) }}</dd>

        <dt class="col-sm-3">Status:</dt>
        <dd class="col-sm-9">{{ ucfirst($pad->padStatus) }}</dd>
    </dl>

    <a href="{{ route('landlord.pads.index') }}" class="btn btn-secondary mt-3">Back to List</a>
</div>
@endsection
