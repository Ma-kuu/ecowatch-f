@extends('layouts.app')

@section('title', 'Login | EcoWatch')

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
  <!-- Login Card -->
  <main class="container">
    <div class="card border-0 shadow-sm" style="max-width: 420px; margin: 0 auto;">
      <div class="card-body p-5">
        <h4 class="card-title text-center mb-4" style="font-weight: 700; color: #212529;">Welcome Back</h4>
        <p class="text-center text-muted mb-4" style="font-size: 14px;">Log in to your EcoWatch account</p>

        @if($errors->any())
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <form id="loginForm" method="POST" action="{{ route('login') }}">
          @csrf

          <div class="mb-3">
            <label class="form-label fw-medium" style="font-size: 14px;">Email Address</label>
            <input type="email" id="email" name="email" class="form-control form-control-lg" placeholder="your@email.com" value="{{ old('email') }}" required>
          </div>

          <div class="mb-4">
            <label class="form-label fw-medium" style="font-size: 14px;">Password</label>
            <div class="input-group">
              <input type="password" id="password" name="password" class="form-control form-control-lg" placeholder="Enter your password" required>
              <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                <i class="bi bi-eye"></i>
              </button>
            </div>
          </div>

          <button type="submit" class="btn btn-success btn-lg w-100 mb-3">Log In</button>
        </form>

        <div class="text-center">
          <span class="text-muted" style="font-size: 14px;">Don't have an account?</span>
          <a href="{{ route('register') }}" class="text-success text-decoration-none fw-medium ms-1">Sign up</a>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
<script>
  // Show/Hide Password
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
</script>
@endpush
