@extends('layouts.dashboard')

@section('title', 'User Management - EcoWatch Admin')

@section('dashboard-home', route('admin-dashboard'))

@section('nav-links')
  <li class="nav-item"><a class="nav-link text-dark {{ request()->routeIs('admin-settings') ? 'active' : '' }}" href="{{ route('admin-settings') }}">Settings</a></li>
@endsection

@section('footer-title', 'EcoWatch Admin Panel')

@section('additional-styles')
  .action-buttons {
    display: flex;
    gap: 0.5rem;
  }
@endsection

@section('content')
  <!-- Success/Error Messages -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <ul class="mb-0">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col">
      <h2 class="fw-bold mb-1">User Management</h2>
      <p class="text-muted">Manage administrators, LGU accounts, and registered users.</p>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1 small">Total Users</p>
              <h3 class="fw-bold mb-0">{{ $totalUsers }}</h3>
            </div>
            <div class="bg-primary bg-opacity-10 p-3 rounded">
              <i class="bi bi-people-fill text-primary fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1 small">Active Users</p>
              <h3 class="fw-bold mb-0">{{ $activeUsers }}</h3>
            </div>
            <div class="bg-success bg-opacity-10 p-3 rounded">
              <i class="bi bi-person-check-fill text-success fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1 small">Admins</p>
              <h3 class="fw-bold mb-0">{{ $adminCount }}</h3>
            </div>
            <div class="bg-danger bg-opacity-10 p-3 rounded">
              <i class="bi bi-shield-fill-check text-danger fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1 small">LGU</p>
              <h3 class="fw-bold mb-0">{{ $lguCount }}</h3>
            </div>
            <div class="bg-info bg-opacity-10 p-3 rounded">
              <i class="bi bi-building text-info fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <p class="text-muted mb-1 small">Citizens</p>
              <h3 class="fw-bold mb-0">{{ $userCount }}</h3>
            </div>
            <div class="bg-secondary bg-opacity-10 p-3 rounded">
              <i class="bi bi-person-fill text-secondary fs-4"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Action Bar -->
  <div class="row mb-3">
    <div class="col-md-6 mb-2 mb-md-0">
      <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#userModal">
        <i class="bi bi-person-plus-fill me-2"></i>
        Add New User
      </button>
    </div>
    <div class="col-md-6">
      <form method="GET" action="{{ route('admin-settings') }}" id="searchForm">
        <input type="hidden" name="role" value="{{ request('role') }}">
        <input type="hidden" name="status" value="{{ request('status') }}">
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="bi bi-search text-muted"></i>
          </span>
          <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="Search users…">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Filter Row -->
  <form method="GET" action="{{ route('admin-settings') }}" id="filterForm">
    <input type="hidden" name="search" value="{{ request('search') }}">
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <select class="form-select" name="role" onchange="document.getElementById('filterForm').submit()">
          <option value="">All Roles</option>
          <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
          <option value="lgu" {{ request('role') === 'lgu' ? 'selected' : '' }}>LGU</option>
          <option value="user" {{ request('role') === 'user' ? 'selected' : '' }}>User</option>
        </select>
      </div>
      <div class="col-md-3">
        <select class="form-select" name="status" onchange="document.getElementById('filterForm').submit()">
          <option value="">All Status</option>
          <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
          <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
        </select>
      </div>
    </div>
  </form>

  <!-- User Table -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
      <h5 class="fw-bold mb-0">User Accounts</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="usersTable">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3">
                <a href="{{ route('admin-settings', array_merge(request()->except(['sort', 'direction']), ['sort' => 'id', 'direction' => request('sort') === 'id' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  User ID
                  @if(request('sort') === 'id')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3">
                <a href="{{ route('admin-settings', array_merge(request()->except(['sort', 'direction']), ['sort' => 'name', 'direction' => request('sort') === 'name' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  Name
                  @if(request('sort') === 'name')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3">
                <a href="{{ route('admin-settings', array_merge(request()->except(['sort', 'direction']), ['sort' => 'email', 'direction' => request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  Email
                  @if(request('sort') === 'email')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3">
                <a href="{{ route('admin-settings', array_merge(request()->except(['sort', 'direction']), ['sort' => 'role', 'direction' => request('sort') === 'role' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  Role
                  @if(request('sort') === 'role')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3">Status</th>
              <th class="py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($users ?? [] as $user)
            <tr>
              <td class="px-4 py-3 fw-semibold">#{{ $user->id }}</td>
              <td class="py-3">{{ $user->name }}</td>
              <td class="py-3">{{ $user->email }}</td>
              <td class="py-3">
                <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'lgu' ? 'info text-dark' : 'secondary') }}">
                  {{ ucfirst($user->role) }}
                </span>
              </td>
              <td class="py-3">
                <span class="badge bg-{{ $user->is_active ? 'success' : 'warning text-dark' }}">
                  {{ $user->is_active ? 'Active' : 'Inactive' }}
                </span>
              </td>
              <td class="py-3 text-center table-actions">
                <form action="{{ route('admin.users.toggle-status', $user->id) }}" method="POST" style="display: inline;">
                  @csrf
                  <button type="submit" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}"
                          title="{{ $user->is_active ? 'Deactivate' : 'Activate' }}">
                    <i class="bi bi-person-{{ $user->is_active ? 'x' : 'check' }}"></i>
                  </button>
                </form>
                <form action="{{ route('admin.users.delete', $user->id) }}" method="POST" style="display: inline;"
                      onsubmit="return confirm('Are you sure you want to delete this user?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                    <i class="bi bi-trash"></i>
                  </button>
                </form>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">No users available</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white border-top">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $users->count() }} of {{ $totalUsers }} users</small>
        {{ $users->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>

  <!-- Add/Edit User Modal -->
  <div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #f8f9fa; border-bottom: 2px solid #198754;">
          <div>
            <h5 class="modal-title fw-bold" id="userModalLabel">
              <i class="bi bi-person-circle me-2"></i>
              Add New User
            </h5>
            <small class="text-muted">Create a new user account</small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="userForm" method="POST" action="{{ route('admin.users.create') }}">
            @csrf
            <div class="mb-3">
              <label for="userName" class="form-label fw-semibold">Name</label>
              <input type="text" class="form-control" name="name" id="userName" placeholder="Enter full name" required>
            </div>
            <div class="mb-3">
              <label for="userEmail" class="form-label fw-semibold">Email</label>
              <input type="email" class="form-control" name="email" id="userEmail" placeholder="user@example.com" required>
            </div>
            <div class="mb-3">
              <label for="userPassword" class="form-label fw-semibold">Password</label>
              <input type="password" class="form-control" name="password" id="userPassword" placeholder="Enter password (min. 8 characters)" required minlength="8">
            </div>
            <div class="mb-3">
              <label for="userPhone" class="form-label fw-semibold">Phone <span class="text-muted">(optional)</span></label>
              <input type="text" class="form-control" name="phone" id="userPhone" placeholder="Enter phone number">
            </div>
            <div class="mb-3">
              <label for="userRole" class="form-label fw-semibold">Role</label>
              <select class="form-select" name="role" id="userRole" required onchange="toggleLguField()">
                <option value="">Select role...</option>
                <option value="admin">Admin</option>
                <option value="lgu">LGU</option>
              </select>
              <small class="text-muted">Only admins and LGU accounts can be created here.</small>
            </div>
            <div class="mb-3" id="lguField" style="display: none;">
              <label for="userLgu" class="form-label fw-semibold">Assigned LGU</label>
              <select class="form-select" name="lgu_id" id="userLgu">
                <option value="">Select LGU...</option>
                @foreach(\App\Models\Lgu::where('is_active', true)->orderBy('name')->get() as $lgu)
                  <option value="{{ $lgu->id }}">{{ $lgu->name }}</option>
                @endforeach
              </select>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>
            Cancel
          </button>
          <button type="submit" form="userForm" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>
            Create User
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // Toggle LGU field visibility based on role selection
  function toggleLguField() {
    const roleSelect = document.getElementById('userRole');
    const lguField = document.getElementById('lguField');
    const lguSelect = document.getElementById('userLgu');

    if (roleSelect.value === 'lgu') {
      lguField.style.display = 'block';
      lguSelect.required = true;
    } else {
      lguField.style.display = 'none';
      lguSelect.required = false;
      lguSelect.value = '';
    }
  }

  // Reset form when modal is closed
  document.getElementById('userModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('userForm').reset();
    document.getElementById('lguField').style.display = 'none';
  });
</script>
@endpush
