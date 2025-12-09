@extends('layouts.dashboard')

@section('title', 'Settings - EcoWatch')

@section('dashboard-home', route('user-dashboard'))

@section('content')
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col">
      <h2 class="fw-bold mb-1">Account Settings</h2>
      <p class="text-muted">Manage your profile and security settings</p>
    </div>
  </div>

  <div class="row">
    <!-- Profile Settings -->
    <div class="col-lg-6 mb-4">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-bottom">
          <h5 class="mb-0 fw-bold">
            <i class="bi bi-person-circle me-2 text-success"></i>Profile Information
          </h5>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="{{ route('user.settings.profile') }}">
            @csrf

            <div class="mb-3">
              <label for="name" class="form-label fw-semibold">Full Name</label>
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
              <label for="phone" class="form-label fw-semibold">Phone Number</label>
              <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="Optional">
              @error('phone')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div>

            <div class="mb-3">
              <label for="lgu_id" class="form-label fw-semibold">
                Your Municipality/City <span class="text-danger">*</span>
              </label>
              <select class="form-select" id="lgu_id" name="lgu_id" required>
                <option value="">Select your municipality...</option>
                @foreach($lgus ?? [] as $lgu)
                  <option value="{{ $lgu->id }}" {{ old('lgu_id', $user->lgu_id) == $lgu->id ? 'selected' : '' }}>
                    {{ $lgu->name }}
                  </option>
                @endforeach
              </select>
              <small class="text-muted">
                <i class="bi bi-info-circle"></i> You'll receive announcements from your selected municipality. You can still report issues anywhere.
              </small>
              @error('lgu_id')
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
          <form method="POST" action="{{ route('user.settings.password') }}">
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
