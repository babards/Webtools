@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h4>Welcome, {{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h4>
                <p class="text-muted">You are logged in to your account.</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">Profile Information</h6>
            </div>
            <div class="card-body text-center">
                @if(Auth::user()->avatar_url)
                    <img src="{{ Auth::user()->avatar_url }}" 
                         alt="Profile Picture" 
                         class="rounded-circle mb-3" 
                         style="width: 100px; height: 100px; object-fit: cover;">
                @else
                    <div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 100px; height: 100px;">
                        <i class="fas fa-user fa-3x text-white"></i>
                    </div>
                @endif
                <h6>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</h6>
                <p class="text-muted small">{{ Auth::user()->email }}</p>
                <span class="badge bg-primary">{{ ucfirst(Auth::user()->role) }}</span>
                <div class="mt-3">
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">
                        <i class="fas fa-edit me-1"></i>Edit Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
