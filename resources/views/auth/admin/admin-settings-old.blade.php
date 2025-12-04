<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - EcoWatch Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
        }
        .dashboard-content {
            margin-top: 80px;
            padding-top: 2rem;
            min-height: calc(100vh - 200px);
        }
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }
        .table-actions .btn {
            padding: 0.25rem 0.75rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg bg-white fixed-top border-bottom">
        <div class="container">
            <a class="navbar-brand fw-bold text-dark" href="{{ route('index') }}">
                <img src="{{ asset('images/logo text.png') }}" alt="EcoWatch" height="45">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('index') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('admin-settings') }}">Settings</a></li>
                    <li class="nav-item"><a class="nav-link text-dark" href="{{ route('feed') }}">Feed</a></li>
                    <li class="nav-item ms-lg-3">
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger px-4">
                                <i class="bi bi-box-arrow-right me-1"></i>Log Out
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Dashboard Content -->
    <div class="dashboard-content">
        <div class="container">
            <!-- Page Header -->
            <div class="row mb-4">
                <div class="col">
                    <h2 class="fw-bold mb-1">User Management</h2>
                    <p class="text-muted">Manage administrators, LGU accounts, and registered users.</p>
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
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="bi bi-search text-muted"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="Search users…" id="searchInput">
                    </div>
                </div>
            </div>

            <!-- Filter Row -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <select class="form-select" id="roleFilter">
                        <option value="">All Roles</option>
                        <option value="admin">Admin</option>
                        <option value="lgu">LGU</option>
                        <option value="user">User</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="suspended">Suspended</option>
                    </select>
                </div>
            </div>

            <!-- User Table -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold mb-0">User Accounts</h5>
                        <button class="btn btn-success btn-sm">
                            <i class="bi bi-download me-1"></i>Export CSV
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="usersTable">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">User ID</th>
                                    <th class="py-3">Name</th>
                                    <th class="py-3">Email</th>
                                    <th class="py-3">Role</th>
                                    <th class="py-3">Status</th>
                                    <th class="py-3 text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users ?? [] as $user)
                                <tr data-role="{{ $user->role }}" data-status="{{ $user->status }}">
                                    <td class="px-4 py-3 fw-semibold">#{{ $user->id }}</td>
                                    <td class="py-3">{{ $user->name }}</td>
                                    <td class="py-3">{{ $user->email }}</td>
                                    <td class="py-3">
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'lgu' ? 'info text-dark' : 'secondary') }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </td>
                                    <td class="py-3">
                                        <span class="badge bg-{{ $user->status === 'active' ? 'success' : 'warning text-dark' }}">
                                            {{ ucfirst($user->status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 text-center table-actions">
                                        <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#userModal">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-{{ $user->status === 'active' ? 'warning' : 'success' }}">
                                            <i class="bi bi-person-{{ $user->status === 'active' ? 'x' : 'check' }}"></i>
                                        </button>
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
                        <small class="text-muted">Showing {{ $users->count() ?? 0 }} of {{ $totalUsers ?? 0 }} users</small>
                        <nav>
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item disabled">
                                    <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
                                </li>
                                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                <li class="page-item">
                                    <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
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
                    <form id="userForm">
                        @csrf
                        <div class="mb-3">
                            <label for="userName" class="form-label fw-semibold">Name</label>
                            <input type="text" class="form-control" id="userName" placeholder="Enter full name" required>
                        </div>
                        <div class="mb-3">
                            <label for="userEmail" class="form-label fw-semibold">Email</label>
                            <input type="email" class="form-control" id="userEmail" placeholder="user@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label for="userPassword" class="form-label fw-semibold">Password</label>
                            <input type="password" class="form-control" id="userPassword" placeholder="Enter password" required>
                        </div>
                        <div class="mb-3">
                            <label for="userRole" class="form-label fw-semibold">Role</label>
                            <select class="form-select" id="userRole" required>
                                <option value="">Select role...</option>
                                <option value="admin">Admin</option>
                                <option value="lgu">LGU</option>
                                <option value="user">User</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="userStatus" class="form-label fw-semibold">Status</label>
                            <select class="form-select" id="userStatus" required>
                                <option value="">Select status...</option>
                                <option value="active">Active</option>
                                <option value="suspended">Suspended</option>
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>
                        Cancel
                    </button>
                    <button type="button" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>
                        Save
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container text-center">
            <h5 class="fw-bold mb-3">EcoWatch Admin Panel</h5>
            <p class="text-white-50 small mb-3">Protecting our environment through community reporting.</p>
            <p class="text-white-50 small mb-0">&copy; 2025 EcoWatch. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Search and filter functionality
        document.getElementById('searchInput').addEventListener('keyup', filterTable);
        document.getElementById('statusFilter').addEventListener('change', filterTable);
        document.getElementById('roleFilter').addEventListener('change', filterTable);

        function filterTable() {
            const searchValue = document.getElementById('searchInput').value.toLowerCase();
            const statusValue = document.getElementById('statusFilter').value.toLowerCase();
            const roleValue = document.getElementById('roleFilter').value.toLowerCase();
            const table = document.getElementById('usersTable');
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                const text = row.textContent.toLowerCase();
                const status = row.getAttribute('data-status');
                const role = row.getAttribute('data-role');

                let matchesSearch = text.includes(searchValue);
                let matchesStatus = statusValue === '' || status === statusValue;
                let matchesRole = roleValue === '' || role === roleValue;

                if (matchesSearch && matchesStatus && matchesRole) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        }
    </script>
</body>
</html>
