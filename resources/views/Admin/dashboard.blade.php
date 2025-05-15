@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Admin Dashboard</h2>
    
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Users</h5>
                    <p class="card-text display-4">{{ \App\Models\User::count() }}</p>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Pads</h5>
                    <p class="card-text display-4">{{ \App\Models\Pad::count() }}</p>
                    <a href="{{ route('admin.pads.index') }}" class="btn btn-primary">View All Pads</a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Total Landlords</h5>
                    <p class="card-text display-4">{{ \App\Models\User::where('role', 'landlord')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
