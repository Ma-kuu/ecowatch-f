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
  @media (max-width: 575.98px) {
    main.container { padding-top: 80px; }
  }
</style>
@endpush

@section('content')
  <!-- Register Card -->
  <main class="container">
    <div class="register-card">
      <h4 class="fw-bold mb-3 text-success text-center">Sign Up for EcoWatch</h4>

      @if($errors->any())
        <div class="alert alert-danger" role="alert">
          <ul class="mb-0">
            @foreach($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @if(session('success'))
        <div class="alert alert-success" role="alert">
          {{ session('success') }}
        </div>
      @endif

      <form id="registerForm" method="POST" action="{{ route('register') }}">
        @csrf

        <div class="mb-3">
          <label class="form-label">Full Name</label>
          <input type="text" id="name" name="name" class="form-control"
                 placeholder="Enter your full name"
                 value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Email</label>
          <input type="email" id="email" name="email" class="form-control"
                 placeholder="Enter your email"
                 value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
          <label class="form-label">Phone (Optional)</label>
          <input type="text" id="phone" name="phone" class="form-control"
                 placeholder="Enter your phone number"
                 value="{{ old('phone') }}">
        </div>
        <div class="mb-3">
          <label class="form-label">Password</label>
          <div class="input-group">
            <input type="password" id="password" name="password" class="form-control" placeholder="Create password (min 6 characters)" required>
            <button class="btn btn-outline-secondary" type="button" id="togglePassword"><i class="bi bi-eye"></i></button>
          </div>
        </div>
        <div class="mb-3">
          <label class="form-label">Confirm Password</label>
          <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" placeholder="Confirm password" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Register</button>
      </form>
      <p class="text-center mt-3">
        Already have an account? <a href="{{ route('login') }}" class="text-success fw-bold">Login here</a>
      </p>
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
