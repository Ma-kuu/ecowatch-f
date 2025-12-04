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
  @media (max-width: 575.98px) {
    main.container { padding-top: 80px; }
  }
</style>
@endpush

@section('content')
  <!-- Login Card -->
  <main class="container">
    <div class="login-card">
      <h4 class="fw-bold mb-3 text-success text-center">Log In to EcoWatch</h4>

      @if($errors->any())
        <div class="alert alert-danger" role="alert">
          {{ $errors->first() }}
        </div>
      @endif

      @if(session('success'))
        <div class="alert alert-success" role="alert">
          {{ session('success') }}
        </div>
      @endif

      <form id="loginForm" method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" id="email" name="email" class="form-control" placeholder="Enter your email" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <button type="submit" class="btn btn-success w-100">Login</button>
      </form>
      <p class="text-center mt-3">
        Don't have an account? <a href="{{ route('register') }}" class="text-success fw-bold">Register here</a>
      </p>
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
