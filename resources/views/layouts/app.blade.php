<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindMyPad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
    @stack('styles')
</head>
<body>
    @if(auth()->check() && !request()->routeIs('login', 'register', 'password.request', 'password.reset', 'password.email'))
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="sidebar d-flex flex-column align-items-start py-4 position-fixed" style="top:0; left:0; height:100vh; width:240px; background-color:#f8f9fa; border-right:1px solid #dee2e6; z-index:1030;">
                <div class="text-center mb-4">
                    <h4>FindMyPad</h4>
                </div>
                <nav class="nav flex-column">
                    <div class="ps-3 mb-3">
                        <div class="fw-bold">{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</div>
                        <div class="small text-muted">{{ auth()->user()->email }}</div>
                        <div class="small">
                            <span class="badge bg-secondary">{{ ucfirst(auth()->user()->role) }}</span>
                        </div>
                    </div>
                    <!-- Admin Navigation -->
                    @if(auth()->user()->role === 'admin')
                        <a class="nav-link {{ Route::currentRouteName() === 'admin.dashboard' ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link {{ Route::currentRouteName() === 'admin.users.index' ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                            <i class="fas fa-users-cog me-2"></i>User Management
                        </a>
                        <a class="nav-link {{ Route::currentRouteName() === 'admin.pads.index' ? 'active' : '' }}" href="{{ route('admin.pads.index') }}">
                            <i class="fas fa-building me-2"></i>All Pads
                        </a>
                        <a class="nav-link {{ Route::currentRouteName() === 'admin.logs.index' ? 'active' : '' }}" href="{{ route('admin.logs.index') }}">
                            <i class="fas fa-history me-2"></i>System Logs
                        </a>
                    @endif

                    <!-- Landlord Navigation -->
                    @if(auth()->user()->role === 'landlord')
                        <a class="nav-link {{ Route::currentRouteName() === 'landlord.pads.index' ? 'active' : '' }}" href="{{ route('landlord.pads.index') }}">
                            <i class="fas fa-home me-2"></i>Manage Pads
                        </a>
                        <a class="nav-link {{ Route::currentRouteName() === 'landlord.applications.all' ? 'active' : '' }}" href="{{ route('landlord.applications.all') }}">
                            <i class="fas fa-file-alt me-2"></i>View Applications
                        </a>
                        <a class="nav-link {{ Route::currentRouteName() === 'landlord.boarders.all' ? 'active' : '' }}" href="{{ route('landlord.boarders.all') }}">
                            <i class="fas fa-users me-2"></i>View Boarders
                        </a>
                    @endif

                    <!-- Tenant Navigation -->
                    @if(auth()->user()->role === 'tenant')
                        <a class="nav-link {{ Route::currentRouteName() === 'tenant.pads.index' ? 'active' : '' }}" href="{{ route('tenant.pads.index') }}">
                            <i class="fas fa-search me-2"></i>Browse Pads
                        </a>
                        <a class="nav-link {{ Route::currentRouteName() === 'tenant.applications.index' ? 'active' : '' }}" href="{{ route('tenant.applications.index') }}">
                            <i class="fas fa-file-alt me-2"></i>My Applications
                        </a>
                    @endif

                    <!-- Logout Form -->
                    <form action="{{ route('logout') }}" method="POST" class="mt-auto">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="main-content" style="margin-left:240px; width:calc(100% - 240px);">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @yield('content')
            </div>
        </div>
    </div>
    @else
    <div class="auth-container">
        @yield('content')
    </div>
    @endif

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')

    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Success!',
                text: '{{ session('success') }}',
                icon: 'success',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
    @endif

    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                title: 'Error!',
                text: '{{ session('error') }}',
                icon: 'error',
                showConfirmButton: false,
                timer: 3000
            });
        });
    </script>
    @endif

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Remove any text node that looks like a parent-placeholder artifact
        Array.from(document.body.childNodes).forEach(function(node) {
            if (
                node.nodeType === Node.TEXT_NODE &&
                node.textContent.trim().match(/^##parent-placeholder-[a-f0-9]+##$/)
            ) {
                node.parentNode.removeChild(node);
            }
        });
    });
    </script>
</body>
</html>

<style>
    .pad-img {
        width: 100%;
        height: 200px; /* Set your desired height */
        object-fit: cover;
    }
    .sidebar {
        min-height: 100vh;
        background-color: #f8f9fa;
        padding-top: 20px;
        border-right: 1px solid #dee2e6;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 240px;
        z-index: 1030;
    }
    .sidebar .nav-link {
        color: #333;
        padding: 10px 20px;
        margin: 5px 0;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    .sidebar .nav-link:hover {
        background-color: #e9ecef;
        color: #0d6efd;
    }
    .sidebar .nav-link.active {
        background-color: #0d6efd;
        color: white;
    }
    .main-content {
        padding: 20px;
        background-color: #fff;
        margin-left: 240px;
        width: calc(100% - 240px);
    }
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
    .alert {
        margin-bottom: 20px;
    }

  
</style>