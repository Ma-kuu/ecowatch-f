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
        <!-- SIDEBAR (LEFT) -->
        <div class="col-lg-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body p-3">
              <h6 class="fw-bold mb-1 text-center">Filter Reports</h6>
              <p class="text-muted text-center mb-3" style="font-size: 13px;">Narrow down by category and status</p>

              <div class="mb-2">
                <label class="form-label fw-medium mb-1" style="font-size: 13px;">Category</label>
                <select id="filterType" class="form-select form-select-sm">
                  <option value="">All categories</option>
                  <option>Illegal Dumping</option>
                  <option>Water Pollution</option>
                  <option>Air Pollution</option>
                  <option>Deforestation</option>
                </select>
              </div>

              <div class="mb-2">
                <label class="form-label fw-medium mb-1" style="font-size: 13px;">Status</label>
                <select id="filterStatus" class="form-select form-select-sm">
                  <option value="">All statuses</option>
                  <option>Pending</option>
                  <option>In Progress</option>
                  <option>Resolved</option>
                </select>
              </div>

              <div class="d-grid gap-2 mb-2">
                <button class="btn btn-success btn-sm">Apply Filters</button>
                <button class="btn btn-outline-secondary btn-sm">Clear Filters</button>
              </div>
              <hr class="my-2">
              <h6 class="mb-2" style="font-size: 14px;">Top Categories</h6>
              @forelse($topCategories ?? [] as $category)
              <div class="d-flex justify-content-between mb-1"><small style="font-size: 12px;">{{ $category->name }}</small><strong style="font-size: 12px;">{{ $category->count }}</strong></div>
              @empty
              <p class="small text-muted mb-0" style="font-size: 12px;">No data available</p>
              @endforelse
              <hr class="my-2">
              <div class="text-center">
                <h6 class="mb-1" style="font-size: 14px;">LGU Portal</h6>
                <p class="small text-muted mb-2" style="font-size: 12px;">Responders and local government access</p>
                <a href="{{ route('login') }}" class="btn btn-sm btn-outline-success">Open Admin</a>
              </div>
            </div>
          </div>
        </div>

        <!-- MAIN FEED (RIGHT) -->
        <div class="col-lg-9">
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
