<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FindMyPad</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    @if(auth()->check() && !request()->routeIs('login', 'register', 'password.request', 'password.reset', 'password.email'))
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar">
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
                    <a class="nav-link {{ Route::currentRouteName() === 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <i class="fas fa-chart-line me-2"></i>Dashboard
                    </a>
                    @if(auth()->user()->role === 'admin')
                        <a class="nav-link {{ Route::currentRouteName() === 'users.index' ? 'active' : '' }}" href="{{ route('users.index') }}">
                            <i class="fas fa-users-cog me-2"></i>User Management
                        </a>
                    @endif
                    @if(auth()->user()->role === 'landlord')
                        <a class="nav-link {{ Route::currentRouteName() === 'pads.index' ? 'active' : '' }}" href="{{ route('pads.index') }}">
                            <i class="fas fa-home me-2"></i>Manage Pad
                        </a>
                    @endif
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent">
                            <i class="fas fa-sign-out-alt me-2"></i>Logout
                        </button>
                    </form>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 main-content">
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
</body>
</html>

<style>
    .sidebar {
        min-height: 100vh;
        background-color: #f8f9fa;
        padding-top: 20px;
    }
    .sidebar .nav-link {
        color: #333;
        padding: 10px 20px;
        margin: 5px 0;
    }
    .sidebar .nav-link:hover {
        background-color: #e9ecef;
    }
    .main-content {
        padding: 20px;
    }
    .auth-container {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #f8f9fa;
    }
</style>