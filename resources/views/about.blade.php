@extends('layouts.app')

@section('title', 'About EcoWatch - Environmental Violation Reporting Platform')

@section('content')
  <!-- Hero Section -->
  <section class="py-5 d-flex align-items-center text-center bg-light" style="min-height: 60vh; margin-top: 56px;">
    <div class="container">
      <div class="d-flex flex-column flex-md-row align-items-center justify-content-center gap-3 mb-4">
        <h1 class="display-4 fw-bold mb-0">About</h1>
        <img src="{{ asset('images/logo-about.png') }}" alt="EcoWatch" height="70">
      </div>
      <p class="lead text-muted mb-0">
        Empowering communities across <strong>Davao del Norte</strong> to protect the environment through secure and anonymous reporting.
      </p>
    </div>
  </section>

  <!-- About Section -->
  <section class="py-5 bg-white">
    <div class="container">
      <div class="row align-items-center">
        <div class="col-lg-6 mb-4 mb-lg-0">
          <h2 class="fw-bold mb-4">Our Mission</h2>
          <p class="text-muted mb-3">
            EcoWatch is a secure platform dedicated to protecting the environment in <strong>Davao del Norte</strong> by enabling citizens
            to report environmental violations safely and anonymously. We believe that every individual
            has the power to make a difference in preserving our planet for future generations.
          </p>
          <p class="text-muted mb-3">
            Our platform connects concerned citizens with environmental authorities, ensuring that
            violations are documented, tracked, and addressed promptly. By making it easy and safe
            to report environmental crimes, we're building a community of environmental stewards.
          </p>
          <p class="text-muted">
            Whether it's illegal dumping, water pollution, air quality issues, or deforestation,
            your reports help hold violators accountable and protect our natural resources.
          </p>
        </div>
        <div class="col-lg-6">
          <div class="bg-success bg-opacity-10 p-5 rounded-3">
            <div class="row g-4 text-center">
              <div class="col-6">
                <div class="mission-feature">
                  <div class="mb-2">
                    <i class="bi bi-shield-check text-success" style="font-size: 48px;"></i>
                  </div>
                  <h4 class="fw-bold mb-1">Secure</h4>
                  <p class="text-muted small mb-0">End-to-end encryption</p>
                </div>
              </div>
              <div class="col-6">
                <div class="mission-feature">
                  <div class="mb-2">
                    <i class="bi bi-eye-slash text-success" style="font-size: 48px;"></i>
                  </div>
                  <h4 class="fw-bold mb-1">Anonymous</h4>
                  <p class="text-muted small mb-0">Your identity protected</p>
                </div>
              </div>
              <div class="col-6">
                <div class="mission-feature">
                  <div class="mb-2">
                    <i class="bi bi-clock-history text-success" style="font-size: 48px;"></i>
                  </div>
                  <h4 class="fw-bold mb-1">Real-time</h4>
                  <p class="text-muted small mb-0">Instant notifications</p>
                </div>
              </div>
              <div class="col-6">
                <div class="mission-feature">
                  <div class="mb-2">
                    <i class="bi bi-graph-up text-success" style="font-size: 48px;"></i>
                  </div>
                  <h4 class="fw-bold mb-1">Impactful</h4>
                  <p class="text-muted small mb-0">Track your reports</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- How It Works -->
  <section class="py-5 bg-light text-center">
    <div class="container">
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
              <p class="text-muted small">Upload photos and describe the violation in detail. Include as much information as possible to help authorities take action.</p>
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
              <p class="text-muted small">Pinpoint exactly where the violation occurred using our interactive map or by entering the address manually.</p>
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
              <p class="text-muted small">Your report is encrypted and securely sent to the appropriate authorities. Track its progress anytime.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Why Report Section -->
  <section class="py-5 bg-white">
    <div class="container">
      <h2 class="fw-bold text-center mb-5">Why Your Reports Matter</h2>
      <div class="row g-4">
        <div class="col-md-6 col-lg-3">
          <div class="text-center violation-item">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px; height:80px;">
              <i class="bi bi-building text-success" style="font-size: 40px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Accountability</h5>
            <p class="text-muted small">Hold businesses and individuals accountable for environmental violations.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="text-center violation-item">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px; height:80px;">
              <i class="bi bi-people text-success" style="font-size: 40px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Community</h5>
            <p class="text-muted small">Join thousands of citizens working together to protect the environment.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="text-center violation-item">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px; height:80px;">
              <i class="bi bi-heart-pulse text-success" style="font-size: 40px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Public Health</h5>
            <p class="text-muted small">Protect your community's health by reporting pollution and contamination.</p>
          </div>
        </div>
        <div class="col-md-6 col-lg-3">
          <div class="text-center violation-item">
            <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:80px; height:80px;">
              <i class="bi bi-globe text-success" style="font-size: 40px;"></i>
            </div>
            <h5 class="fw-semibold mb-2">Future Generations</h5>
            <p class="text-muted small">Preserve our planet and natural resources for those who come after us.</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="py-5 bg-success text-center text-white">
    <div class="container">
      <h2 class="fw-bold mb-3">Ready to Make a Difference?</h2>
      <p class="mb-4">Start reporting environmental violations in your community today.</p>
      <a href="{{ route('report-form') }}" class="btn btn-light btn-lg px-5">Report a Violation</a>
    </div>
  </section>
@endsection
