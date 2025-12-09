@extends('layouts.app')

@section('title', 'EcoWatch — Feed')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
<link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.css" />
<link rel="stylesheet" href="{{ asset('css/map-lightbox.css') }}" />
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
  .map-preview {
    cursor: pointer;
  }
  .upvote-btn {
    border: 2px solid #198754;
    background: white;
    color: #198754;
    padding: 4px 12px;
    border-radius: 20px;
    transition: all 0.2s;
    font-weight: 600;
  }
  .upvote-btn:hover {
    background: #f0f9f4;
    transform: translateY(-2px);
  }
  .upvote-btn.upvoted {
    background: #198754;
    color: white;
  }
  .upvote-btn i {
    font-size: 16px;
    margin-right: 4px;
  }
  .report-btn {
    border: 1px solid #6c757d;
    background: white;
    color: #6c757d;
    padding: 4px 10px;
    border-radius: 20px;
    transition: all 0.2s;
    font-size: 12px;
    margin-left: 8px;
  }
  .report-btn:hover {
    background: #6c757d;
    color: white;
  }
  .report-btn i {
    font-size: 14px;
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

              <form method="GET" action="{{ route('feed') }}">
                <div class="mb-2">
                  <label class="form-label fw-medium mb-1" style="font-size: 13px;">Sort By</label>
                  <select name="sort" class="form-select form-select-sm">
                    <option value="new" {{ ($filters['sort'] ?? 'new') === 'new' ? 'selected' : '' }}>New</option>
                    <option value="top" {{ ($filters['sort'] ?? null) === 'top' ? 'selected' : '' }}>Top (Most Upvoted)</option>
                  </select>
                </div>

                <div class="mb-2">
                  <label class="form-label fw-medium mb-1" style="font-size: 13px;">Time Range</label>
                  <select name="time" class="form-select form-select-sm">
                    <option value="all" {{ ($filters['time'] ?? 'all') === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="today" {{ ($filters['time'] ?? null) === 'today' ? 'selected' : '' }}>Today</option>
                    <option value="week" {{ ($filters['time'] ?? null) === 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ ($filters['time'] ?? null) === 'month' ? 'selected' : '' }}>This Month</option>
                  </select>
                </div>

                <div class="mb-2">
                  <label class="form-label fw-medium mb-1" style="font-size: 13px;">Category</label>
                  <select name="type" class="form-select form-select-sm">
                    <option value="">All categories</option>
                    @foreach($violationTypes as $type)
                      <option value="{{ $type->id }}" {{ ($filters['type'] ?? null) == $type->id ? 'selected' : '' }}>
                        {{ $type->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-2">
                  <label class="form-label fw-medium mb-1" style="font-size: 13px;">Status</label>
                  <select name="status" class="form-select form-select-sm">
                    <option value="">All statuses</option>
                    <option value="in-review" {{ ($filters['status'] ?? null) === 'in-review' ? 'selected' : '' }}>Verified</option>
                    <option value="in-progress" {{ ($filters['status'] ?? null) === 'in-progress' ? 'selected' : '' }}>Ongoing</option>
                    <option value="resolved" {{ ($filters['status'] ?? null) === 'resolved' ? 'selected' : '' }}>Resolved</option>
                  </select>
                </div>

                <div class="mb-2">
                  <label class="form-label fw-medium mb-1" style="font-size: 13px;">Municipality</label>
                  <select name="municipality" class="form-select form-select-sm">
                    <option value="">All municipalities</option>
                    @foreach($lgus as $lgu)
                      <option value="{{ $lgu->id }}" {{ ($filters['municipality'] ?? null) == $lgu->id ? 'selected' : '' }}>
                        {{ $lgu->name }}
                      </option>
                    @endforeach
                  </select>
                </div>

                <div class="mb-2">
                  <label class="form-label fw-medium mb-1" style="font-size: 13px;">Search</label>
                  <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" class="form-control form-control-sm" placeholder="Title, description, or code">
                </div>

                <div class="d-grid gap-2 mb-2">
                  <button type="submit" class="btn btn-success btn-sm">Apply Filters</button>
                  <a href="{{ route('feed') }}" class="btn btn-outline-secondary btn-sm">Clear Filters</a>
                </div>
              </form>
              <hr class="my-2">
              <h6 class="mb-2" style="font-size: 14px;">Top Categories</h6>
              <div class="mb-2">
                <canvas id="topCategoriesChart" style="max-height: 220px;"></canvas>
              </div>
              @if(($topCategories ?? collect())->isEmpty())
                <p class="small text-muted mb-0" style="font-size: 12px;">No data available</p>
              @endif
              
              <hr class="my-2">
              <h6 class="mb-2" style="font-size: 14px;">Reports by Status</h6>
              <div class="mb-2">
                <canvas id="statusChart" style="max-height: 200px;"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- MAIN FEED (RIGHT) -->
        <div class="col-lg-9">
          <!-- Tabs -->
          <ul class="nav nav-tabs mb-3" id="feedTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="reports-tab" data-bs-toggle="tab" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="true">
                <i class="bi bi-exclamation-triangle"></i> Reports
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="announcements-tab" data-bs-toggle="tab" data-bs-target="#announcements" type="button" role="tab" aria-controls="announcements" aria-selected="false">
                <i class="bi bi-megaphone"></i> Announcements
              </button>
            </li>
          </ul>

          <!-- Tab Content -->
          <div class="tab-content" id="feedTabContent">

            <!-- Reports Tab -->
            <div class="tab-pane fade show active" id="reports" role="tabpanel" aria-labelledby="reports-tab">
              <h5 class="mb-3 fw-bold">Recent Reports</h5>

              @forelse($feedReports ?? [] as $report)
          <!-- Report Card -->
          <div class="card post-card shadow-sm mb-3">
            <div class="row g-0">
              <div class="col-md-6">
                @php
                  $primaryPhoto = $report->photos->first();
                @endphp
                @if($primaryPhoto)
                  <img
                    src="{{ asset('storage/' . $primaryPhoto->file_path) }}"
                    class="post-image w-100"
                    style="height: 100%; object-fit: cover;"
                    alt="{{ $report->violation_type_display }}">
                @else
                  <div class="post-image d-flex align-items-center justify-content-center bg-light w-100" style="height: 100%;">
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
                    <span class="badge bg-{{ $report->status_color }}">{{ $report->status_display }}</span>
                  </div>
                  <p class="text-muted small mb-1">Report Code <strong>{{ $report->report_id }}</strong> • {{ $report->created_at->format('M d, Y') }} • {{ $report->location }}</p>
                  <p class="card-text mb-2 text-truncate-2">{{ $report->description }}</p>
                  <div id="map{{ $report->id }}" class="map-preview mb-2"></div>
                  <div class="mt-auto d-flex justify-content-between align-items-center">
                    <div>
                      <span class="badge bg-light text-dark category-badge">{{ $report->violation_type_display }}</span>
                    </div>
                    <div>
                      <button 
                        class="btn btn-sm upvote-btn {{ auth()->check() && $report->upvotes->where('user_id', auth()->id())->isNotEmpty() ? 'upvoted' : '' }}"
                        data-report-id="{{ $report->id }}"
                        data-upvoted="{{ auth()->check() && $report->upvotes->where('user_id', auth()->id())->isNotEmpty() ? 'true' : 'false' }}"
                        onclick="toggleUpvote(this)"
                        title="Upvote this report">
                        <i class="bi bi-arrow-up-circle-fill"></i>
                        <span class="upvote-count">{{ $report->upvotes_count ?? 0 }}</span>
                      </button>
                      @auth
                        <button 
                          class="btn btn-sm report-btn"
                          data-report-id="{{ $report->id }}"
                          onclick="reportPost(this)"
                          title="Report inappropriate content">
                          <i class="bi bi-flag"></i> Report
                        </button>
                      @endauth
                    </div>
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
              <div class="d-flex justify-content-center my-4">
                {{ $feedReports->links() }}
              </div>
              @endif
            </div>

            <!-- Announcements Tab -->
            <div class="tab-pane fade" id="announcements" role="tabpanel" aria-labelledby="announcements-tab">
              <h5 class="mb-3 fw-bold">Public Announcements</h5>

              @forelse($announcements ?? [] as $announcement)
              <!-- Announcement Card -->
              <div class="card shadow-sm mb-3">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-start mb-2">
                    <h5 class="card-title mb-0">{{ $announcement->title }}</h5>
                    <span class="badge bg-{{ $announcement->type === 'urgent' ? 'danger' : ($announcement->type === 'warning' ? 'warning' : ($announcement->type === 'success' ? 'success' : 'info')) }}">
                      {{ ucfirst($announcement->type) }}
                    </span>
                  </div>
                  <p class="text-muted small mb-2">
                    <i class="bi bi-calendar"></i> {{ $announcement->created_at->format('M d, Y') }}
                    @if($announcement->lgu)
                      • <i class="bi bi-building"></i> {{ $announcement->lgu->name }}
                    @else
                      • <i class="bi bi-globe"></i> System-wide
                    @endif
                    @if($announcement->is_pinned)
                      • <i class="bi bi-pin-angle-fill text-primary"></i> Pinned
                    @endif
                  </p>
                  <p class="card-text">{{ $announcement->content }}</p>
                  <div class="d-flex justify-content-between align-items-center mt-3">
                    <div>
                      @if($announcement->expires_at)
                        <small class="text-muted">
                          <i class="bi bi-clock"></i> Expires: {{ $announcement->expires_at->format('M d, Y') }}
                        </small>
                      @endif
                    </div>
                    <div>
                      <button class="btn btn-sm btn-outline-primary border-0" onclick="likeAnnouncement({{ $announcement->id }}, this)" title="Like this announcement">
                        <i class="bi bi-heart"></i> <span class="like-count">{{ $announcement->reactions_count ?? 0 }}</span>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              @empty
              <div class="text-center py-5">
                <i class="bi bi-megaphone" style="font-size: 48px; color: #dee2e6;"></i>
                <p class="text-muted mt-3">No announcements available at the moment.</p>
              </div>
              @endforelse

              <!-- Load More -->
              @if(($announcements ?? collect())->isNotEmpty())
              <div class="d-flex justify-content-center my-4">
                {{ $announcements->links() }}
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Shared Map Lightbox Component (used when clicking small maps) -->
  <x-map-lightbox />
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="{{ asset('js/map-helper.js') }}"></script>
<script src="{{ asset('js/map-lightbox.js') }}"></script>
<script>
  // Initialize maps for each report (with fullscreen control similar to dashboard)
  @foreach($feedReports ?? [] as $report)
  @if($report->latitude && $report->longitude)
  (function() {
    const mapId = 'map{{ $report->id }}';
    const lat = {{ $report->latitude }};
    const lng = {{ $report->longitude }};

    const map{{ $report->id }} = L.map(mapId).setView([lat, lng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map{{ $report->id }});
    L.marker([lat, lng]).addTo(map{{ $report->id }});

    // Make map clickable to open enlarged lightbox view
    const mapElement = document.getElementById(mapId);
    if (mapElement) {
      // Use the map div itself as the trigger element for the lightbox
      initMapLightbox({
        enlargeButtonId: mapId,
        overlayId: 'mapLightboxOverlay',
        containerId: 'mapLightboxContainer',
        enlargedMapId: 'enlargedMap',
        sourceMap: null,
        getSourcePosition: function() {
          return {
            lat: lat,
            lng: lng,
            zoom: 15,
            hasMarker: true,
            markerLat: lat,
            markerLng: lng,
            label: @json($report->location ?? 'Report Location'),
          };
        },
      });
    }
  })();
  @endif
  @endforeach
</script>

<script>
  // Top Categories Pie Chart (feed sidebar)
  (function() {
    const ctx = document.getElementById('topCategoriesChart');
    if (!ctx) return;

    const categories = @json(($topCategories ?? collect())->pluck('name'));
    const counts = @json(($topCategories ?? collect())->pluck('reports_count'));
    const colors = @json(($topCategories ?? collect())->pluck('color'));

    if (!categories.length || !counts.length) return;

    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: categories,
        datasets: [{
          data: counts,
          backgroundColor: colors,
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12,
              boxHeight: 12,
              font: { size: 11 },
            },
          },
        },
      },
    });
  })();

  // Status Breakdown Doughnut Chart (feed sidebar)
  (function() {
    const ctx = document.getElementById('statusChart');
    if (!ctx) return;

    const statusData = @json($statusBreakdown ?? []);
    if (!statusData || !statusData.labels || !statusData.labels.length) return;

    new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: statusData.labels,
        datasets: [{
          data: statusData.counts,
          backgroundColor: statusData.colors,
        }]
      },
      options: {
        plugins: {
          legend: {
            position: 'bottom',
            labels: {
              boxWidth: 12,
              boxHeight: 12,
              font: { size: 10 },
            },
          },
        },
      },
    });
  })();

  // Upvote toggle function
  function toggleUpvote(button) {
    const reportId = button.dataset.reportId;
    const isUpvoted = button.dataset.upvoted === 'true';
    const countSpan = button.querySelector('.upvote-count');
    const currentCount = parseInt(countSpan.textContent);

    // Optimistic UI update
    button.disabled = true;
    if (isUpvoted) {
      button.classList.remove('upvoted');
      countSpan.textContent = currentCount - 1;
      button.dataset.upvoted = 'false';
    } else {
      button.classList.add('upvoted');
      countSpan.textContent = currentCount + 1;
      button.dataset.upvoted = 'true';
    }

    // Send request to server
    fetch(`/feed/reports/${reportId}/upvote`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Update with actual count from server
        countSpan.textContent = data.upvotes_count;
        button.dataset.upvoted = data.upvoted ? 'true' : 'false';
        if (data.upvoted) {
          button.classList.add('upvoted');
        } else {
          button.classList.remove('upvoted');
        }
      } else {
        // Revert on error
        if (isUpvoted) {
          button.classList.add('upvoted');
          countSpan.textContent = currentCount;
          button.dataset.upvoted = 'true';
        } else {
          button.classList.remove('upvoted');
          countSpan.textContent = currentCount;
          button.dataset.upvoted = 'false';
        }
        alert(data.message || 'Failed to toggle upvote');
      }
    })
    .catch(error => {
      console.error('Upvote error:', error);
      // Revert on error
      if (isUpvoted) {
        button.classList.add('upvoted');
        countSpan.textContent = currentCount;
        button.dataset.upvoted = 'true';
      } else {
        button.classList.remove('upvoted');
        countSpan.textContent = currentCount;
        button.dataset.upvoted = 'false';
      }
      alert('Network error. Please try again.');
    })
    .finally(() => {
      button.disabled = false;
    });
  }

  // Report post function
  function reportPost(button) {
    const reportId = button.dataset.reportId;
    
    if (!confirm('Report this post as inappropriate?')) {
      return;
    }

    button.disabled = true;
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="bi bi-hourglass-split"></i> Reporting...';

    fetch(`/feed/reports/${reportId}/flag`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
      },
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        button.innerHTML = '<i class="bi bi-check-circle"></i> Reported';
        button.classList.add('disabled');
        alert('Thank you. This report has been flagged for admin review.');
      } else {
        button.innerHTML = originalText;
        button.disabled = false;
        alert(data.message || 'Failed to report post');
      }
    })
    .catch(error => {
      console.error('Report error:', error);
      button.innerHTML = originalText;
      button.disabled = false;
      alert('Network error. Please try again.');
    });
  }

  // Like announcement function
  function likeAnnouncement(announcementId, button) {
    const icon = button.querySelector('i');
    const countSpan = button.querySelector('.like-count');
    const currentCount = parseInt(countSpan.textContent);

    // Toggle heart icon
    if (icon.classList.contains('bi-heart')) {
      icon.classList.remove('bi-heart');
      icon.classList.add('bi-heart-fill');
      button.classList.add('text-danger');
      countSpan.textContent = currentCount + 1;
    } else {
      icon.classList.remove('bi-heart-fill');
      icon.classList.add('bi-heart');
      button.classList.remove('text-danger');
      countSpan.textContent = Math.max(0, currentCount - 1);
    }
  }
</script>
@endpush
