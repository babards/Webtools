@extends('layouts.app')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">User Management</h4>
                    <button class="btn btn-light btn-sm text-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                        <i class="fas fa-user-plus"></i> Register New User
                    </button>
                </div>
                <div class="card-body">
                    <form method="GET" class="row g-2 mb-3 align-items-center">
                        <div class="col-md-4">
                            <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Search user...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" type="submit">Search</button>
                        </div>
                        <div class="col-md-2">
                            <select name="sort" class="form-select" onchange="this.form.submit()">
                                <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Sort: Latest</option>
                                <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Sort: Oldest</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select name="role" class="form-select" onchange="this.form.submit()">
                                <option value="all" {{ request('role') == 'all' ? 'selected' : '' }}>Role: All</option>
                                <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                                <option value="tenant" {{ request('role') == 'tenant' ? 'selected' : '' }}>Tenant</option>
                                <option value="landlord" {{ request('role') == 'landlord' ? 'selected' : '' }}>Landlord</option>
                            </select>
                        </div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th class="py-2">First Name</th>
                                    <th class="py-2">Last Name</th>
                                    <th class="py-2">Email</th>
                                    <th class="py-2">Role</th>
                                    <th class="py-2">Created At</th>
                                    <th class="py-2">Update At</th>
                                    <th class="py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="py-2">{{ $user->first_name }}</td>
                                        <td class="py-2">{{ $user->last_name }}</td>
                                        <td class="py-2">{{ $user->email }}</td>
                                        <td class="py-2">
                                            <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                        </td>
                                        <td class="py-2">{{ $user->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="py-2">{{ $user->updated_at->format('Y-m-d H:i') }}</td>  
                                        <td class="py-2">
                                            <button class="btn btn-sm btn-warning editUserBtn"
                                                data-id="{{ $user->id }}"
                                                data-first_name="{{ $user->first_name }}"
                                                data-last_name="{{ $user->last_name }}"
                                                data-email="{{ $user->email }}"
                                                data-role="{{ $user->role }}"
                                                data-bs-toggle="modal" data-bs-target="#editUserModal">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn btn-sm btn-danger deleteUserBtn"
                                                data-id="{{ $user->id }}"
                                                data-name="{{ $user->first_name }} {{ $user->last_name }}"
                                                data-bs-toggle="modal" data-bs-target="#deleteUserModal">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-3">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3">
                        {{ $users->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<div class="modal fade" id="createUserModal" tabindex="-1" aria-labelledby="createUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('admin.users.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="createUserModalLabel">Register New User</h5>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Role</label>
            <select name="role" class="form-select" required>
              <option value="admin">Admin</option>
              <option value="tenant">Tenant</option>
              <option value="landlord">Landlord</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
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

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="editUserForm">
      @csrf
      @method('PUT')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="editUserId">
          <div class="mb-3">
            <label>First Name</label>
            <input type="text" name="first_name" id="editFirstName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Last Name</label>
            <input type="text" name="last_name" id="editLastName" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" id="editEmail" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Role</label>
            <select name="role" id="editRole" class="form-select" required>
              <option value="admin">Admin</option>
              <option value="tenant">Tenant</option>
              <option value="landlord">Landlord</option>
            </select>
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

<!-- Delete User Modal -->
<div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" id="deleteUserForm">
      @csrf
      @method('DELETE')
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="deleteUserModalLabel">Delete User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete <strong id="deleteUserName"></strong>?</p>
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
    // Edit User
    document.querySelectorAll('.editUserBtn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('editUserId').value = this.dataset.id;
            document.getElementById('editFirstName').value = this.dataset.first_name;
            document.getElementById('editLastName').value = this.dataset.last_name;
            document.getElementById('editEmail').value = this.dataset.email;
            document.getElementById('editRole').value = this.dataset.role;
            document.getElementById('editUserForm').action = '/admin/users/' + this.dataset.id;
        });
    });

    // Delete User
    document.querySelectorAll('.deleteUserBtn').forEach(function (button) {
        button.addEventListener('click', function () {
            document.getElementById('deleteUserName').textContent = this.dataset.name;
            document.getElementById('deleteUserForm').action = '/admin/users/' + this.dataset.id;
        });
    });
});

// Prevent modals from opening on page load
document.addEventListener('DOMContentLoaded', function () {
    // No code here to auto-open modals!
});
</script>
@endsection
