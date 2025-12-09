@extends('layouts.app')

@section('title', 'Check Report Status | EcoWatch')

@push('styles')
<style>
  .map-lightbox-overlay,
  .map-lightbox-container,
  .map-lightbox-close {
    display: none !important;
  }
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
  <!-- Report Status Card -->
  <main class="container">
    <div class="card border-0 shadow-sm" style="max-width: 520px; margin: 0 auto;">
      <div class="card-body p-5">
        <h4 class="card-title text-center mb-4" style="font-weight: 700; color: #212529;">Check Report Status</h4>
        <p class="text-center text-muted mb-4" style="font-size: 14px;">Enter your tracking code to view report details</p>

        @if(session('error'))
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
          </div>
        @endif

        <!-- Lookup Form -->
        <form method="POST" action="{{ route('report-status.lookup') }}">
          @csrf

          <div class="mb-4">
            <label class="form-label fw-medium" style="font-size: 14px;">Report ID</label>
            <input 
              type="text" 
              name="tracking_code" 
              class="form-control form-control-lg text-uppercase" 
              placeholder="e.g., RPT-001" 
              value="{{ old('tracking_code') }}" 
              required
              style="letter-spacing: 2px; font-weight: 600;"
            >
            <small class="text-muted" style="font-size: 13px;">
              Enter the <strong>Report ID</strong> you received after submitting your report (e.g., RPT-001, RPT-123)
            </small>
          </div>

          <button type="submit" class="btn btn-success btn-lg w-100 mb-3">Check Status</button>
        </form>

        @if(isset($report))
          <!-- Report Details -->
          <div class="mt-4 pt-4 border-top">
            <h5 class="mb-3" style="font-weight: 600; color: #212529;">Report Details</h5>
            
            <div class="mb-3">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted" style="font-size: 14px;">Tracking Code:</span>
                <span class="fw-bold" style="font-size: 14px; letter-spacing: 0.5px;">{{ $report->report_id }}</span>
              </div>
              
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted" style="font-size: 14px;">Status:</span>
                <span class="badge bg-{{ $report->status_color }}" style="font-size: 13px;">
                  {{ $report->status_display }}
                </span>
              </div>

              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted" style="font-size: 14px;">Type:</span>
                <span style="font-size: 14px;">{{ $report->violation_type_display }}</span>
              </div>

              @if($report->barangay)
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted" style="font-size: 14px;">Location:</span>
                  <span style="font-size: 14px;">{{ $report->barangay->name }}, {{ $report->barangay->lgu->name }}</span>
                </div>
              @endif

              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted" style="font-size: 14px;">Submitted:</span>
                <span style="font-size: 14px;">{{ $report->created_at->format('M d, Y') }}</span>
              </div>

              @if($report->resolved_at)
                <div class="d-flex justify-content-between align-items-center mb-2">
                  <span class="text-muted" style="font-size: 14px;">Resolved:</span>
                  <span style="font-size: 14px;">{{ $report->resolved_at->format('M d, Y') }}</span>
                </div>
              @endif
            </div>

            @if($report->status === 'resolved')
              <div class="alert alert-success mt-3 mb-0" role="alert" style="font-size: 14px;">
                <i class="bi bi-check-circle-fill me-2"></i>
                <strong>This report has been resolved!</strong>
              </div>
            @elseif($report->status === 'in-progress')
              <div class="alert alert-info mt-3 mb-0" role="alert" style="font-size: 14px;">
                <i class="bi bi-info-circle-fill me-2"></i>
                Your report is currently being addressed by the LGU.
              </div>
            @elseif($report->status === 'pending')
              <div class="alert alert-warning mt-3 mb-0" role="alert" style="font-size: 14px;">
                <i class="bi bi-clock-fill me-2"></i>
                Your report is pending review.
              </div>
            @endif
          </div>
        @endif

        <div class="text-center mt-4">
          <span class="text-muted" style="font-size: 14px;">Have an account?</span>
          <a href="{{ route('login') }}" class="text-success text-decoration-none fw-medium ms-1">Log in</a>
        </div>
      </div>
    </div>
  </main>
@endsection
