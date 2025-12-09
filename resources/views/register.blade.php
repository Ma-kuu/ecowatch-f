@extends('layouts.app')

@section('title', 'Register | EcoWatch')

@push('styles')
<style>
  body {
    min-height: 100vh;
    display: flex;
    flex-direction: column;
    background: #f4f6f8;
  }
  main.container {
    flex: 1 0 auto;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    padding-top: 90px;
    padding-bottom: 24px;
    min-height: 0;
  }
  footer {
    flex-shrink: 0;
  }
  .form-control::placeholder {
    font-size: 14px;
  }
  @media (max-width: 575.98px) {
    main.container { padding-top: 80px; }
  }
</style>
@endpush

@section('content')
  <!-- Register Card -->
  <main class="container">
    <div class="card border-0 shadow-sm" style="max-width: 420px; margin: 0 auto;">
      <div class="card-body p-5">
        <h4 class="card-title text-center mb-4" style="font-weight: 700; color: #212529;">Create Account</h4>
        <p class="text-center text-muted mb-4" style="font-size: 14px;">Join EcoWatch today</p>

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0 ps-3">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <form id="registerForm" method="POST" action="{{ route('register') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-medium" style="font-size: 14px;">Full Name</label>
            <input type="text" id="name" name="name" class="form-control form-control-lg"
                   placeholder="John Doe"
                   value="{{ old('name') }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium" style="font-size: 14px;">Email Address</label>
            <input type="email" id="email" name="email" class="form-control form-control-lg"
                   placeholder="your@email.com"
                   value="{{ old('email') }}" required>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium" style="font-size: 14px;">Phone <span class="text-muted">(Optional)</span></label>
            <input type="text" id="phone" name="phone" class="form-control form-control-lg"
                   placeholder="+63 912 345 6789"
                   value="{{ old('phone') }}">
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium" style="font-size: 14px;">
              Your Municipality/City <span class="text-danger">*</span>
            </label>
            <select id="lgu_id" name="lgu_id" class="form-select form-select-lg" required>
              <option value="">Select your municipality...</option>
              @foreach($lgus ?? [] as $lgu)
                <option value="{{ $lgu->id }}" {{ old('lgu_id') == $lgu->id ? 'selected' : '' }}>
                  {{ $lgu->name }}
                </option>
              @endforeach
            </select>
            <div class="form-text" style="font-size: 12px;">
              <i class="bi bi-info-circle"></i> You'll receive announcements from your local government. You can still report issues anywhere in Davao del Norte.
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-medium" style="font-size: 14px;">Password</label>
            <div class="input-group">
              <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Min 6 characters" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label fw-medium" style="font-size: 14px;">Confirm Password</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control form-control-lg" placeholder="Re-enter password" required>
          </div>

          <button type="submit" class="btn btn-success btn-lg w-100 mb-3">Sign Up</button>
        </form>

        <div class="text-center">
          <span class="text-muted" style="font-size: 14px;">Already have an account?</span>
          <a href="{{ route('login') }}" class="text-success text-decoration-none fw-medium ms-1">Log in</a>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
<script>
  // Toggle Password
  document.getElementById('togglePassword').addEventListener('click', () => {
    const passField = document.getElementById('password');
    const icon = document.querySelector('#togglePassword i');
    if (passField.type === 'password') {
      passField.type = 'text';
      icon.classList.remove('bi-eye');
      icon.classList.add('bi-eye-slash');
    } else {
      passField.type = 'password';
      icon.classList.remove('bi-eye-slash');
      icon.classList.add('bi-eye');
    }
  });

  // Client-side validation
  document.getElementById('registerForm').addEventListener('submit', (e) => {
    const pass = document.getElementById('password').value;
    const confirm = document.getElementById('confirmPassword').value;

    if (pass !== confirm) {
      e.preventDefault();
      alert('Passwords do not match!');
      return false;
    }

    if (pass.length < 8) {
      e.preventDefault();
      alert('Password must be at least 8 characters long!');
      return false;
    }
  });
</script>
@endpush
