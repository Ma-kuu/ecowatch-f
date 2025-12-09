@extends('layouts.dashboard')

@section('title', 'Settings - EcoWatch')

@section('dashboard-home', route('lgu-dashboard'))

@section('content')
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col">
      <h2 class="fw-bold mb-1">LGU Account Settings</h2>
      <p class="text-muted">Manage your office profile and security settings</p>
    </div>
  </div>

  <div class="row">
    <!-- Office Info (Read-only) -->
    <div class="col-lg-12 mb-4">
      <div class="card border-0 shadow-sm bg-light">
        <div class="card-body p-4">
          <h6 class="text-muted text-uppercase small mb-3">Office Information</h6>
          <div class="row">
            <div class="col-md-6 mb-2">
              <strong>LGU Office:</strong> {{ $lgu->name }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>Province:</strong> {{ $lgu->province }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>Code:</strong> {{ $lgu->code }}
            </div>
            <div class="col-md-6 mb-2">
              <strong>Status:</strong> 
              <span class="badge {{ $lgu->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $lgu->is_active ? 'Active' : 'Inactive' }}
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Profile Settings -->
    <div class="col-lg-6 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h5 class="mb-0 fw-bold">
            <i class="bi bi-person-circle me-2 text-success"></i>Account Information
          </h5>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="{{ route('lgu.settings.profile') }}">
            @csrf

            <div class="mb-3">
              <label for="name" class="form-label fw-semibold">Account Name</label>
              <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
              @error('name')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="email" class="form-label fw-semibold">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
              @error('email')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="phone" class="form-label fw-semibold">Contact Number</label>
              <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Optional">
              @error('phone')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <button type="submit" class="btn btn-success">
              <i class="bi bi-check-circle me-1"></i>Update Profile
            </button>
          </form>
        </div>
      </div>
    </div>

    <!-- Password Settings -->
    <div class="col-lg-6 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h5 class="mb-0 fw-bold">
            <i class="bi bi-shield-lock me-2 text-success"></i>Change Password
          </h5>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="{{ route('lgu.settings.password') }}">
            @csrf

            <div class="mb-3">
              <label for="current_password" class="form-label fw-semibold">Current Password</label>
              <input type="password" class="form-control" id="current_password" name="current_password" required>
              @error('current_password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password" class="form-label fw-semibold">New Password</label>
              <input type="password" class="form-control" id="password" name="password" required>
              <small class="text-muted">Minimum 8 characters</small>
              @error('password')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="password_confirmation" class="form-label fw-semibold">Confirm New Password</label>
              <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
            </div>

            <button type="submit" class="btn btn-success">
              <i class="bi bi-key me-1"></i>Change Password
            </button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection
