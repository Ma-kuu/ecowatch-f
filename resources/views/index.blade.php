@extends('layouts.app')

@section('title', 'EcoWatch - Report Environmental Violations')

@section('content')
  <!-- Hero Section -->
  <section class="vh-100 d-flex align-items-center bg-light" style="background-image: url('{{ asset('images/1.png') }}'); background-size: cover; background-position: center; background-repeat: no-repeat;">
    <div class="container">
      <div class="row">
        <div class="col-lg-6 col-md-7 text-start">
          <h1 class="display-4 fw-bold mb-4">
            Report Environmental Violations
            <span class="text-success d-block">Safely & Securely</span>
          </h1>
          <p class="lead text-dark mb-5">
            Help protect our environment by reporting environmental violations through our platform.
          </p>
          <div class="d-flex flex-column flex-sm-row gap-3">
            <a href="{{ route('report-form') }}" class="btn btn-success btn-lg px-4">Report a Violation</a>
            <a href="{{ route('about') }}" class="btn btn-outline-dark btn-lg px-4">Learn How It Works</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="py-5 bg-white text-center">
    <div class="container py-4">
      <h2 class="fw-bold mb-4">How It Works</h2>
      <p class="text-muted mb-5">Simple steps to report and make a difference.</p>
      <div class="row g-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px; height:60px;">
                <i class="bi bi-camera fs-3"></i>
              </div>
              <h5 class="fw-semibold">Document Evidence</h5>
              <p class="text-muted small">Upload photos and describe the violation.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px; height:60px;">
                <i class="bi bi-geo-alt fs-3"></i>
              </div>
              <h5 class="fw-semibold">Provide Location</h5>
              <p class="text-muted small">Pinpoint exactly where the violation occurred.</p>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
              <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:60px; height:60px;">
                <i class="bi bi-shield-check fs-3"></i>
              </div>
              <h5 class="fw-semibold">Submit Securely</h5>
              <p class="text-muted small">Your report is encrypted and securely sent to authorities.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Common Violations -->
  <section class="py-5 bg-light text-center">
    <div class="container py-4">
      <h2 class="fw-bold mb-5">Common Violations to Report</h2>
      <div class="row g-4">
        <div class="col-6 col-lg-3">
          <div class="violation-item">
            <i class="bi bi-trash text-success mb-3" style="font-size: 50px;"></i>
            <h6 class="fw-semibold">Illegal Dumping</h6>
            <p class="text-muted small">Unauthorized waste disposal in natural areas.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="violation-item">
            <i class="bi bi-droplet text-success mb-3" style="font-size: 50px;"></i>
            <h6 class="fw-semibold">Water Pollution</h6>
            <p class="text-muted small">Chemical spills or industrial runoff.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="violation-item">
            <i class="bi bi-wind text-success mb-3" style="font-size: 50px;"></i>
            <h6 class="fw-semibold">Air Pollution</h6>
            <p class="text-muted small">Smoke, industrial emissions, or open burning.</p>
          </div>
        </div>
        <div class="col-6 col-lg-3">
          <div class="violation-item">
            <i class="bi bi-tree text-success mb-3" style="font-size: 50px;"></i>
            <h6 class="fw-semibold">Deforestation</h6>
            <p class="text-muted small">Illegal logging or land clearing.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA -->
  <section class="py-5 bg-success text-center text-white">
    <div class="container">
      <h2 class="fw-bold mb-3">Make Your Report Today</h2>
      <p class="mb-4">Every report helps protect our environment and hold violators accountable.</p>
      <a href="{{ route('report-form') }}" class="btn btn-light btn-lg me-2 mb-2">Report a Violation</a>
      <a href="{{ route('about') }}" class="btn btn-outline-light btn-lg mb-2">Learn More</a>
    </div>
  </section>
@endsection
