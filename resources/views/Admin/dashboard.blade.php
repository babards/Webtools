@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Admin Dashboard</h2>
        </div>
    </div>

    <div class="row">
        <!-- User Statistics -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">User Statistics</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Users:</span>
                        <span class="fw-bold">{{ $stats['total_users'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Landlords:</span>
                        <span class="fw-bold">{{ $stats['total_landlords'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Tenants:</span>
                        <span class="fw-bold">{{ $stats['total_tenants'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pad Statistics -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Pad Statistics</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Pads:</span>
                        <span class="fw-bold">{{ $stats['total_pads'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Available Pads:</span>
                        <span class="fw-bold">{{ $stats['available_pads'] }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Application Statistics -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Application Statistics</h5>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Total Applications:</span>
                        <span class="fw-bold">{{ $stats['total_applications'] }}</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span>Pending Applications:</span>
                        <span class="fw-bold">{{ $stats['pending_applications'] }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Links -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Quick Links</h5>
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('admin.users.index') }}" class="btn btn-primary w-100 mb-2">
                                <i class="fas fa-users me-2"></i>Manage Users
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.pads.index') }}" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-home me-2"></i>Manage Pads
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.logs.index') }}" class="btn btn-info w-100 mb-2">
                                <i class="fas fa-history me-2"></i>View Logs
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection