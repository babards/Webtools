@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Landlord Dashboard</h2>
    <div class="card mt-4">
        <div class="card-body">
            <p>Welcome, {{ auth()->user()->first_name }}!</p>
            <p>Here you can manage your pads and view your listings.</p>
            <a href="{{ route('landlord.pads.index') }}" class="btn btn-primary">Manage My Pads</a>
        </div>
    </div>
</div>
@endsection
