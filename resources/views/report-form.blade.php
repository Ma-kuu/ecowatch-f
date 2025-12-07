@extends('layouts.app')

@section('title', 'Ready to Report? - EcoWatch')

@section('content')
  <!-- Hero Section -->
  <section class="py-5 bg-light text-center" style="margin-top: 56px; padding-top: 4rem !important;">
    <div class="container">
      <div class="mb-4">
        <i class="bi bi-clipboard-check text-success" style="font-size: 64px;"></i>
      </div>
      <h1 class="display-5 fw-bold mb-3">Ready to Report?</h1>
      <p class="lead text-muted mb-5">Report environmental violations in Davao del Norte. Choose how you'd like to submit your report.</p>

      <!-- Report Options -->
      <div class="row g-4 justify-content-center mb-5">
        <div class="col-md-5">
          <div class="card report-option-card shadow-sm h-100">
            <div class="card-body p-4 text-center">
              <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px; height:80px;">
                <i class="bi bi-incognito text-success" style="font-size: 40px;"></i>
              </div>
              <h4 class="fw-bold mb-3">Report Anonymously</h4>
              <p class="text-muted mb-4">Submit your report without creating an account. Your identity will remain completely anonymous.</p>
              <ul class="list-unstyled text-start mb-4">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>No account required</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Complete anonymity</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Quick submission</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-success me-2"></i>Secure & encrypted</li>
              </ul>
              <a href="{{ route('report-anon') }}" class="btn btn-success btn-lg w-100">Report Anonymously</a>
            </div>
          </div>
        </div>

        <div class="col-md-5">
          <div class="card report-option-card shadow-sm h-100">
            <div class="card-body p-4 text-center">
              <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px; height:80px;">
                <i class="bi bi-person-badge text-primary" style="font-size: 40px;"></i>
              </div>
              <h4 class="fw-bold mb-3">Report with Account</h4>
              <p class="text-muted mb-4">Log in to track your reports, receive updates, and access your submission history.</p>
              <ul class="list-unstyled text-start mb-4">
                <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Track report status</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Receive notifications</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>View submission history</li>
                <li class="mb-2"><i class="bi bi-check-circle-fill text-primary me-2"></i>Edit pending reports</li>
              </ul>
              <a href="{{ route('login') }}" class="btn btn-primary btn-lg w-100">Log In to Report</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- What to Report Section -->
  <section class="py-5 bg-white">
    <div class="container">
      <h2 class="fw-bold text-center mb-4">What Can You Report?</h2>
      <p class="text-center text-muted mb-5">EcoWatch accepts reports for various environmental violations</p>

      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 violation-item">
            <div class="feature-icon bg-danger bg-opacity-10 mb-3">
              <i class="bi bi-trash text-danger" style="font-size: 24px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Illegal Dumping</h5>
            <p class="text-muted small">Unauthorized waste disposal, littering in natural areas, hazardous material dumping</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 violation-item">
            <div class="feature-icon bg-info bg-opacity-10 mb-3">
              <i class="bi bi-droplet text-info" style="font-size: 24px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Water Pollution</h5>
            <p class="text-muted small">Chemical spills, industrial runoff, sewage discharge, contaminated waterways</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 violation-item">
            <div class="feature-icon bg-secondary bg-opacity-10 mb-3">
              <i class="bi bi-wind text-secondary" style="font-size: 24px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Air Pollution</h5>
            <p class="text-muted small">Excessive smoke, industrial emissions, open burning, harmful fumes</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 violation-item">
            <div class="feature-icon bg-success bg-opacity-10 mb-3">
              <i class="bi bi-tree text-success" style="font-size: 24px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Deforestation</h5>
            <p class="text-muted small">Illegal logging, unauthorized land clearing, habitat destruction</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 violation-item">
            <div class="feature-icon bg-warning bg-opacity-10 mb-3">
              <i class="bi bi-soundwave text-warning" style="font-size: 24px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Noise Pollution</h5>
            <p class="text-muted small">Excessive industrial noise, construction violations, prolonged disturbances</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 violation-item">
            <div class="feature-icon bg-primary bg-opacity-10 mb-3">
              <i class="bi bi-tsunami text-primary" style="font-size: 24px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Soil Contamination</h5>
            <p class="text-muted small">Chemical leaks, improper waste storage, agricultural runoff</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 violation-item">
            <div class="feature-icon bg-danger bg-opacity-10 mb-3">
              <i class="bi bi-fire text-danger" style="font-size: 24px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Wildlife Violations</h5>
            <p class="text-muted small">Illegal hunting, habitat destruction, endangered species harm</p>
          </div>
        </div>

        <div class="col-md-6 col-lg-3">
          <div class="text-center p-3 violation-item">
            <div class="feature-icon bg-dark bg-opacity-10 mb-3">
              <i class="bi bi-building text-dark" style="font-size: 24px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Industrial Violations</h5>
            <p class="text-muted small">Permit violations, emission excess, improper waste management</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- What You Need Section -->
  <section class="py-5 bg-light">
    <div class="container">
      <h2 class="fw-bold text-center mb-4">What You Need to Report</h2>
      <p class="text-center text-muted mb-5">Prepare these items before submitting your report</p>

      <div class="row g-4">
        <div class="col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <div class="d-flex align-items-start mb-3">
                <div class="bg-success bg-opacity-10 rounded p-3 me-3">
                  <i class="bi bi-camera-fill text-success" style="font-size: 28px;"></i>
                </div>
                <div>
                  <h5 class="fw-bold mb-2">Photo Evidence</h5>
                  <span class="badge bg-warning text-dark">Recommended</span>
                </div>
              </div>
              <ul class="list-unstyled text-muted mb-0">
                <li class="mb-2"><i class="bi bi-arrow-right-short text-success"></i> Clear photos of the violation</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-success"></i> Multiple angles if possible</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-success"></i> Date and time visible (if possible)</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-success"></i> Supports JPG, PNG, HEIC formats</li>
                <li class="mb-0"><i class="bi bi-arrow-right-short text-success"></i> Maximum 10MB per image</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <div class="d-flex align-items-start mb-3">
                <div class="bg-primary bg-opacity-10 rounded p-3 me-3">
                  <i class="bi bi-geo-alt-fill text-primary" style="font-size: 28px;"></i>
                </div>
                <div>
                  <h5 class="fw-bold mb-2">Location Details</h5>
                  <span class="badge bg-danger">Required</span>
                </div>
              </div>
              <ul class="list-unstyled text-muted mb-0">
                <li class="mb-2"><i class="bi bi-arrow-right-short text-primary"></i> Exact address or coordinates</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-primary"></i> Nearby landmarks or references</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-primary"></i> City/municipality information</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-primary"></i> Use map to pinpoint location</li>
                <li class="mb-0"><i class="bi bi-arrow-right-short text-primary"></i> Accessible to authorities</li>
              </ul>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body p-4">
              <div class="d-flex align-items-start mb-3">
                <div class="bg-info bg-opacity-10 rounded p-3 me-3">
                  <i class="bi bi-file-text-fill text-info" style="font-size: 28px;"></i>
                </div>
                <div>
                  <h5 class="fw-bold mb-2">Violation Details</h5>
                  <span class="badge bg-danger">Required</span>
                </div>
              </div>
              <ul class="list-unstyled text-muted mb-0">
                <li class="mb-2"><i class="bi bi-arrow-right-short text-info"></i> Type of violation</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-info"></i> When it occurred (date/time)</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-info"></i> Detailed description</li>
                <li class="mb-2"><i class="bi bi-arrow-right-short text-info"></i> Any known violators (optional)</li>
                <li class="mb-0"><i class="bi bi-arrow-right-short text-info"></i> Additional context or evidence</li>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <!-- Additional Tips -->
      <div class="row mt-5">
        <div class="col-12">
          <div class="alert alert-success border-0 shadow-sm" role="alert">
            <div class="d-flex">
              <div class="me-3">
                <i class="bi bi-info-circle-fill" style="font-size: 24px;"></i>
              </div>
              <div>
                <h5 class="alert-heading fw-bold mb-2">Tips for Effective Reporting</h5>
                <ul class="mb-0 ps-3">
                  <li>Be as specific and detailed as possible in your description</li>
                  <li>If the violation is ongoing, note the frequency and duration</li>
                  <li>Include any potential safety hazards or immediate dangers</li>
                  <li>Don't put yourself at risk to gather evidence</li>
                  <li>Report as soon as possible after witnessing the violation</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Call to Action -->
      <div class="text-center mt-5">
        <h4 class="fw-bold mb-3">Have everything ready?</h4>
        <div class="d-flex gap-3 justify-content-center flex-wrap">
          <a href="{{ route('report-anon') }}" class="btn btn-success btn-lg px-5">
            <i class="bi bi-incognito me-2"></i>Report Anonymously
          </a>
          <a href="{{ route('login') }}" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-person-badge me-2"></i>Log In to Report
          </a>
        </div>
      </div>
    </div>
  </section>
@endsection
