<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('title', 'Dashboard - EcoWatch')</title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link href="{{ asset('css/app.css') }}" rel="stylesheet">

  @stack('styles')

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
    .stat-card {
      border-left: 4px solid;
    }
    .stat-card.total {
      border-left-color: #6c757d;
    }
    .stat-card.pending {
      border-left-color: #ffc107;
    }
    .stat-card.in-review, .stat-card.in-progress {
      border-left-color: #0dcaf0;
    }
    .stat-card.resolved, .stat-card.fixed {
      border-left-color: #fd7e14;
    }
    .stat-card.confirmed {
      border-left-color: #198754;
    }
    .stat-card.verified {
      border-left-color: #0d6efd;
    }
    .table-actions .btn {
      padding: 0.25rem 0.75rem;
      font-size: 0.875rem;
    }
    .modal-header {
      background-color: #f8f9fa;
      border-bottom: 2px solid #198754;
    }
    .report-image-preview {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
    }
    #viewMap {
      height: 300px;
      background-color: #e9ecef;
      border-radius: 8px;
    }
    .chart-placeholder {
      height: 250px;
      background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
    }
    .navbar-nav .nav-link {
      transition: color 0.3s ease;
    }
    .navbar-nav .nav-link:hover,
    .navbar-nav .nav-link.active {
      color: #198754 !important;
    }
    @yield('additional-styles')
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
          @hasSection('dashboard-home')
            <li class="nav-item"><a class="nav-link text-dark {{ request()->routeIs('user-dashboard', 'admin-dashboard', 'lgu-dashboard') ? 'active' : '' }}" href="@yield('dashboard-home')">Dashboard</a></li>
          @else
            <li class="nav-item"><a class="nav-link text-dark {{ request()->routeIs('index') ? 'active' : '' }}" href="{{ route('index') }}">Home</a></li>
          @endif
          @yield('nav-links')
          <li class="nav-item"><a class="nav-link text-dark {{ request()->routeIs('feed') ? 'active' : '' }}" href="{{ route('feed') }}">Feed</a></li>
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
      @yield('content')
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
      <h5 class="fw-bold mb-3">@yield('footer-title', 'EcoWatch Dashboard')</h5>
      <p class="text-white-50 small mb-3">Protecting our environment through community reporting.</p>
      <p class="text-white-50 small mb-0">&copy; {{ date('Y') }} EcoWatch. All rights reserved.</p>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Toast Notification System -->
  @include('partials.toast-notification')

  @stack('scripts')
</body>
</html>
