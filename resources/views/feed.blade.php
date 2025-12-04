@extends('layouts.app')

@section('title', 'EcoWatch — Feed')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css"/>
<style>
  body {
    background: #f4f6f8;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }
  footer.bg-dark {
    margin-top: auto;
  }
</style>
@endpush

@section('content')
  <!-- Feed Section -->
  <main class="flex-grow-1" style="padding-top: 100px; padding-bottom: 40px;">
    <div class="container">
      <div class="row g-4">
        <!-- MAIN FEED -->
        <div class="col-lg-8">
          <h4 class="mb-3 fw-bold">Recent Reports</h4>

          @forelse($feedReports ?? [] as $report)
          <!-- Report Card -->
          <div class="card post-card shadow-sm mb-3">
            <div class="row g-0">
              <div class="col-md-6">
                @if($report->image_url)
                  <img src="{{ $report->image_url }}" class="post-image" alt="{{ $report->violation_type_display }}">
                @else
                  <div class="post-image d-flex align-items-center justify-content-center bg-light">
                    <div class="text-center text-muted">
                      <i class="bi bi-image" style="font-size: 48px;"></i>
                      <p class="mt-2 mb-0">No Image</p>
                    </div>
                  </div>
                @endif
              </div>
              <div class="col-md-6">
                <div class="card-body d-flex flex-column h-100">
                  <div class="d-flex justify-content-between align-items-start">
                    <h5 class="card-title mb-1">{{ $report->title ?? $report->violation_type_display }}</h5>
                    <span class="badge status-badge-{{ $report->status_badge }}">{{ $report->status_display }}</span>
                  </div>
                  <p class="text-muted small mb-1">Posted by <strong>{{ $report->reporter_name ? '@' . $report->reporter_name : 'Anonymous' }}</strong> • {{ $report->created_at->diffForHumans() }}</p>
                  <p class="card-text mb-2 text-truncate-2">{{ $report->description }}</p>
                  <div id="map{{ $report->id }}" class="map-preview mb-2"></div>
                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <button class="btn btn-sm btn-outline-secondary">💬 {{ $report->comments_count ?? 0 }} Comments</button>
                    <span class="badge bg-light text-dark category-badge">{{ $report->violation_type_display }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          @empty
          <div class="text-center py-5">
            <p class="text-muted">No reports available at the moment.</p>
          </div>
          @endforelse

          <!-- Load More -->
          @if(($feedReports ?? collect())->isNotEmpty())
          <div class="text-center my-4">
            <button class="btn btn-outline-secondary">Load more</button>
          </div>
          @endif
        </div>

        <!-- SIDEBAR -->
        <div class="col-lg-4">
          <div class="card shadow-sm">
            <div class="card-body">
              <h6 class="mb-2">Filters</h6>
              <select id="filterType" class="form-select form-select-sm mb-2">
                <option value="">All categories</option>
                <option>Illegal Dumping</option>
                <option>Water Pollution</option>
                <option>Air Pollution</option>
                <option>Deforestation</option>
              </select>
              <select id="filterStatus" class="form-select form-select-sm mb-3">
                <option value="">All statuses</option>
                <option>Pending</option>
                <option>In Progress</option>
                <option>Resolved</option>
              </select>
              <div class="d-grid gap-2 mb-3">
                <button class="btn btn-sm btn-success">Apply</button>
                <button class="btn btn-sm btn-outline-secondary">Clear</button>
              </div>
              <hr>
              <h6 class="mb-2">Top Categories</h6>
              @forelse($topCategories ?? [] as $category)
              <div class="d-flex justify-content-between mb-1"><small>{{ $category->name }}</small><strong>{{ $category->count }}</strong></div>
              @empty
              <p class="small text-muted">No data available</p>
              @endforelse
              <hr>
              <div class="text-center">
                <h6 class="mb-1">LGU Portal</h6>
                <p class="small text-muted mb-2">Responders and local government access</p>
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-success">Open Admin</a>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
<script>
  // Initialize maps for each report
  @foreach($feedReports ?? [] as $report)
  @if($report->latitude && $report->longitude)
  const map{{ $report->id }} = L.map('map{{ $report->id }}').setView([{{ $report->latitude }}, {{ $report->longitude }}], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map{{ $report->id }});
  L.marker([{{ $report->latitude }}, {{ $report->longitude }}]).addTo(map{{ $report->id }});
  @endif
  @endforeach
</script>
@endpush
