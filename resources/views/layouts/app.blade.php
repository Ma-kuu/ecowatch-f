<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'EcoWatch - Environmental Reporting System')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

  @stack('styles')
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
          @if(isset($navLinks) && is_array($navLinks))
            @foreach($navLinks as $link)
              <li class="nav-item">
                <a class="nav-link text-dark" href="{{ $link['url'] }}">{{ $link['label'] }}</a>
              </li>
            @endforeach
          @else
            <li class="nav-item"><a class="nav-link text-dark" href="{{ route('index') }}">Home</a></li>
            <li class="nav-item"><a class="nav-link text-dark" href="{{ route('about') }}">About</a></li>
            <li class="nav-item"><a class="nav-link text-dark" href="{{ route('feed') }}">Feed</a></li>

            @auth
              <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle text-dark" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                  {{ Auth::user()->name }}
                </a>
                <ul class="dropdown-menu">
                  @if(Auth::user()->role === 'admin')
                    <li><a class="dropdown-item" href="{{ route('admin-dashboard') }}">Dashboard</a></li>
                    <li><a class="dropdown-item" href="{{ route('admin-settings') }}">Settings</a></li>
                  @elseif(Auth::user()->role === 'lgu')
                    <li><a class="dropdown-item" href="{{ route('lgu-dashboard') }}">Dashboard</a></li>
                  @else
                    <li><a class="dropdown-item" href="{{ route('user-dashboard') }}">Dashboard</a></li>
                  @endif
                  <li><hr class="dropdown-divider"></li>
                  <li>
                    <form method="POST" action="{{ route('logout') }}">
                      @csrf
                      <button type="submit" class="dropdown-item">Log Out</button>
                    </form>
                  </li>
                </ul>
              </li>
              <li class="nav-item ms-lg-3">
                <a class="btn btn-success px-4" href="{{ route('report-form') }}">Report Violation</a>
              </li>
            @else
              {{-- Guest users --}}
              <li class="nav-item"><a class="nav-link text-dark" href="{{ route('login') }}">Log in</a></li>
              <li class="nav-item ms-lg-3">
                <a class="btn btn-success px-4" href="{{ route('report-form') }}">Report Violation</a>
              </li>
            @endauth
          @endif
        </ul>
      </div>
    </div>
  </nav>

  <!-- Content -->
  @yield('content')

  <!-- Footer -->
  <footer class="bg-dark text-white py-4">
    <div class="container text-center">
      <img src="{{ asset('images/logo text.png') }}" alt="EcoWatch" height="40" class="mb-3">
      <p class="text-white-50 small mb-3">Protecting our environment through community reporting.</p>
      <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} EcoWatch. All rights reserved.</p>
    </div>
  </footer>

  <!-- Vue Notification Container -->
  <div id="vue-notifications"></div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  @stack('scripts')
</body>
</html>
