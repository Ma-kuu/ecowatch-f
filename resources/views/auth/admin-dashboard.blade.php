@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - EcoWatch')

@section('dashboard-home', route('admin-dashboard'))

@section('nav-links')
@endsection

@section('footer-title', 'EcoWatch Admin Panel')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<link rel="stylesheet" href="{{ asset('css/map-lightbox.css') }}" />
<style>
  #viewMap {
    height: 300px;
    background-color: #e9ecef;
    border-radius: 8px;
  }
</style>
@endpush

@section('content')
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col">
      <h2 class="fw-bold mb-1">Admin Dashboard</h2>
      <p class="text-muted">Manage and monitor environmental violation reports</p>
    </div>
  </div>

  <!-- Summary Cards -->
<div class="row g-4 mb-4">
  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Total Reports" 
      :value="$totalReports ?? 0" 
      icon="bi-file-earmark-text" 
      color="secondary"
      subtitle="All time submissions"
      :filter-url="route('admin-dashboard')"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Pending Reports" 
      :value="$pendingReports ?? 0" 
      icon="bi-clock-history" 
      color="warning"
      subtitle="Awaiting review"
      :filter-url="route('admin-dashboard', ['status' => ['pending']])"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="In Review" 
      :value="$inReviewReports ?? 0" 
      icon="bi-search" 
      color="info"
      subtitle="Being investigated"
      :filter-url="route('admin-dashboard', ['status' => ['in-review', 'in-progress']])"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Resolved" 
      :value="$resolvedReports ?? 0" 
      icon="bi-check-circle" 
      color="success"
      subtitle="Successfully resolved"
      :filter-url="route('admin-dashboard', ['status' => ['resolved']])"
    />
  </div>
</div>

  <!-- Analytics Section -->
  <div class="row g-4 mb-4">
    <!-- Category Breakdown Chart -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-pie-chart me-2 text-success"></i>Reports by Category
          </h6>
        </div>
        <div class="card-body">
          <canvas id="categoryChart" height="250"></canvas>
        </div>
      </div>
    </div>

    <!-- Status Distribution Chart -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-bar-chart me-2 text-success"></i>Reports by Status
          </h6>
        </div>
        <div class="card-body">
          <canvas id="statusChart" height="250"></canvas>
        </div>
      </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-graph-up me-2 text-success"></i>Monthly Trend
          </h6>
        </div>
        <div class="card-body">
          <canvas id="monthlyChart" height="250"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Search -->
  <x-dashboard-filters 
    :action="route('admin-dashboard')"
    :show-violation-type="true"
    :show-status="true"
    :show-lgu="true"
    :show-barangay="true"
    :show-priority="true"
    :show-date-range="true"
    :show-flagged="true"
    :show-reporter-type="true"
    :lgus="\App\Models\Lgu::orderBy('name')->get()"
    :barangays="\App\Models\Barangay::orderBy('name')->get()"
  />

  <!-- Reports Management Table -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
      <h5 class="fw-bold mb-0">Reports Management</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="reportsTable">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3">
                <a href="{{ route('admin-dashboard', [...request()->except(['sort', 'direction']), 'sort' => 'report_id', 'direction' => request('sort') === 'report_id' && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  Report ID
                  @if(request('sort') === 'report_id')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3">Reporter Name</th>
              <th class="py-3">Type of Violation</th>
              <th class="py-3">
                <a href="{{ route('admin-dashboard', [...request()->except(['sort', 'direction']), 'sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  Date Submitted
                  @if(request('sort') === 'created_at')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3">Location</th>
              <th class="py-3">
                <a href="{{ route('admin-dashboard', [...request()->except(['sort', 'direction']), 'sort' => 'status', 'direction' => request('sort') === 'status' && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  Status
                  @if(request('sort') === 'status')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($reports ?? [] as $report)
            <tr data-status="{{ $report->status }}" data-type="{{ $report->violation_type }}">
              <td class="px-4 py-3 fw-semibold">{{ $report->report_id }}</td>
              <td class="py-3">{{ $report->reporter_name ?? 'Anonymous' }}</td>
              <td class="py-3">
                <i class="bi bi-{{ $report->icon }} text-{{ $report->color }} me-2"></i>{{ $report->violation_type_display }}
              </td>
              <td class="py-3">{{ $report->created_at->format('M d, Y') }}</td>
              <td class="py-3">{{ $report->location }}</td>
              <td class="py-3">
                <span class="badge bg-{{ $report->status_color }}">{{ $report->status_display }}</span>
                @if($report->flags_count > 0)
                  <span class="badge bg-warning text-dark ms-1" title="{{ $report->flags_count }} user(s) reported this as inappropriate">
                    <i class="bi bi-flag-fill"></i> Flagged ({{ $report->flags_count }})
                  </span>
                @endif
                @if($report->is_hidden)
                  <span class="badge bg-danger ms-1" title="Hidden from public feed">
                    <i class="bi bi-eye-slash"></i> Hidden
                  </span>
                @endif
                @if($report->manual_priority === 'boosted')
                  <span class="badge bg-info ms-1" title="Boosted in feed">
                    <i class="bi bi-arrow-up"></i> Boosted
                  </span>
                @elseif($report->manual_priority === 'suppressed')
                  <span class="badge bg-secondary ms-1" title="Suppressed in feed">
                    <i class="bi bi-arrow-down"></i> Suppressed
                  </span>
                @endif
              </td>
              <td class="py-3 text-center table-actions">
                <button class="btn btn-sm btn-outline-primary me-1"
                        data-bs-toggle="modal"
                        data-bs-target="#viewReportModal"
                        data-report-id="{{ $report->id }}"
                        data-report-code="{{ $report->report_id }}"
                        data-lat="{{ $report->latitude }}"
                        data-lng="{{ $report->longitude }}"
                        data-description="{{ $report->description }}"
                        data-violation-type="{{ $report->violationType->name ?? 'N/A' }}"
                        data-location="{{ $report->location }}"
                        data-reporter="{{ $report->reporter_name ?? ($report->reporter->name ?? 'Anonymous') }}"
                        data-date="{{ $report->created_at->format('M d, Y') }}"
                        data-status="{{ $report->status_display }}"
                        data-status-color="{{ $report->status_color }}"
                        data-photo="{{ $report->photos->first() ? asset('storage/' . $report->photos->first()->file_path) : '' }}"
                        data-remarks="{{ $report->validity?->notes ?? 'No remarks from admin yet.' }}">
                  <i class="bi bi-eye"></i>
                </button>
                @if($report->is_anonymous && (!$report->validity || $report->validity->status === 'pending'))
                  <!-- Validate button for anonymous reports that haven't been validated yet -->
                  <button class="btn btn-sm btn-outline-warning"
                          data-bs-toggle="modal"
                          data-bs-target="#validateReportModal"
                          data-report-id="{{ $report->id }}"
                          data-report-code="{{ $report->report_id }}"
                          title="Validate Anonymous Report">
                    <i class="bi bi-shield-check"></i>
                  </button>
                @elseif($report->is_anonymous && $report->validity && $report->validity->status === 'invalid')
                  <!-- Show invalidated badge for invalid anonymous reports (no edit button) -->
                  <span class="badge bg-danger px-3 py-2">
                    <i class="bi bi-shield-x-fill me-1"></i>
                    Invalidated
                  </span>
                @else
                  <!-- Edit button for all other reports (validated anonymous + regular) -->
                  <button class="btn btn-sm btn-outline-success"
                          data-bs-toggle="modal"
                          data-bs-target="#updateStatusModal"
                          data-report-id="{{ $report->id }}"
                          data-report-code="{{ $report->report_id }}"
                          data-report-status="{{ $report->status }}"
                          data-report-description="{{ $report->description }}"
                          data-report-priority="{{ $report->priority ?? 'medium' }}"
                          data-admin-remarks="{{ $report->validity?->notes ?? '' }}"
                          data-report-is-hidden="{{ $report->is_hidden ? '1' : '0' }}"
                          data-report-manual-priority="{{ $report->manual_priority ?? 'normal' }}"
                          title="Edit Report">
                    <i class="bi bi-pencil"></i>
                  </button>
                @endif
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="7" class="text-center py-4 text-muted">No reports available</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white border-top">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $reports->firstItem() ?? 0 }} to {{ $reports->lastItem() ?? 0 }} of {{ $reports->total() ?? 0 }} reports</small>
        {{ $reports->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>

  <!-- View Report Modal -->
  <x-modals.view-report />

  <!-- Validate Anonymous Report Modal -->
<x-modals.validate-report />

  <!-- Edit Report Modal (for all validated reports) -->
<x-modals.update-report />

  <!-- Reusable Map Lightbox Component for Enlarged Map View -->
  <x-map-lightbox />

@endsection

@push('scripts')
<!-- Load Leaflet library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script src="{{ asset('js/modal-helper.js') }}"></script>
<script src="{{ asset('js/map-helper.js') }}"></script>
<script src="{{ asset('js/map-lightbox.js') }}"></script>

<script>
  // =============================================================================
  // ANALYTICS CHARTS
  // =============================================================================

  // Category Breakdown Chart (Doughnut) - Clickable
  const categoryCtx = document.getElementById('categoryChart').getContext('2d');
  const violationTypeIds = {!! json_encode($categoryStats->pluck('id')) !!};
  
  const categoryChart = new Chart(categoryCtx, {
    type: 'doughnut',
    data: {
      labels: {!! json_encode($categoryStats->pluck('name')) !!},
      datasets: [{
        data: {!! json_encode($categoryStats->pluck('count')) !!},
        backgroundColor: {!! json_encode($categoryStats->pluck('color')) !!},
        borderWidth: 2,
        borderColor: '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            padding: 15,
            font: { size: 11 }
          }
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          cornerRadius: 6,
          callbacks: {
            label: function(context) {
              const total = context.dataset.data.reduce((a, b) => a + b, 0);
              const percent = ((context.parsed / total) * 100).toFixed(1);
              return context.label + ': ' + context.parsed + ' (' + percent + '%)';
            }
          }
        }
      },
      onClick: (event, elements) => {
        if (elements.length > 0) {
          const index = elements[0].index;
          const violationTypeId = violationTypeIds[index];
          window.location.href = '{{ route("admin-dashboard") }}?violation_type[]=' + violationTypeId;
        }
      }
    }
  });

  // Status Distribution Chart (Bar)
  const statusCtx = document.getElementById('statusChart').getContext('2d');
  new Chart(statusCtx, {
    type: 'bar',
    data: {
      labels: ['Pending', 'In Review', 'In Progress', 'Awaiting Conf.', 'Resolved'],
      datasets: [{
        label: 'Reports',
        data: [
          {{ $pendingReports }},
          {{ \App\Models\Report::where('status', 'in-review')->count() }},
          {{ \App\Models\Report::where('status', 'in-progress')->count() }},
          {{ \App\Models\Report::where('status', 'awaiting-confirmation')->count() }},
          {{ $resolvedReports }}
        ],
        backgroundColor: ['#ffc107', '#0dcaf0', '#0d6efd', '#6c757d', '#198754'],
        borderRadius: 6
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          cornerRadius: 6
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 }
        }
      }
    }
  });

  // Monthly Trend Chart (Line)
  const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
  @php
    $monthlyData = \App\Models\Report::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
      ->where('created_at', '>=', now()->subMonths(6))
      ->groupBy('month')
      ->orderBy('month')
      ->pluck('count', 'month');
    
    $last6Months = collect();
    for ($i = 5; $i >= 0; $i--) {
      $month = now()->subMonths($i)->format('Y-m');
      $last6Months[$month] = $monthlyData[$month] ?? 0;
    }
  @endphp

  new Chart(monthlyCtx, {
    type: 'line',
    data: {
      labels: {!! json_encode($last6Months->keys()->map(fn($m) => \Carbon\Carbon::parse($m)->format('M Y'))) !!},
      datasets: [{
        label: 'Reports',
        data: {!! json_encode($last6Months->values()) !!},
        borderColor: '#198754',
        backgroundColor: 'rgba(25, 135, 84, 0.1)',
        borderWidth: 3,
        fill: true,
        tension: 0.4,
        pointBackgroundColor: '#198754',
        pointBorderColor: '#fff',
        pointBorderWidth: 2,
        pointRadius: 5,
        pointHoverRadius: 7
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.8)',
          padding: 12,
          cornerRadius: 6
        }
      },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 1 }
        }
      }
    }
  });

  // =============================================================================
  // MAP AND MODAL HANDLERS
  // =============================================================================

  // Map variables
  let adminMap = null;
  let adminMarker = null;

  // Handle View Report button clicks
  document.querySelectorAll('[data-bs-target="#viewReportModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const lat = parseFloat(this.dataset.lat);
      const lng = parseFloat(this.dataset.lng);
      const reportCode = this.dataset.reportCode;
      const description = this.dataset.description;
      const violationType = this.dataset.violationType;
      const location = this.dataset.location;
      const reporter = this.dataset.reporter;
      const date = this.dataset.date;
      const status = this.dataset.status;
      const statusColor = this.dataset.statusColor;
      const photo = this.dataset.photo;
      const remarks = this.dataset.remarks;

      // Populate modal fields
      document.getElementById('modalViolationType').textContent = violationType;
      document.getElementById('modalDescription').textContent = description;
      document.getElementById('modalLocation').textContent = location;
      document.getElementById('modalReporterName').textContent = reporter;
      document.getElementById('modalDateSubmitted').textContent = date;
      document.getElementById('modalStatus').textContent = status;
      document.getElementById('modalStatus').className = 'badge bg-' + statusColor;
      document.getElementById('modalAdminRemarks').textContent = remarks || 'No remarks from admin yet.';

      // Handle photo display
      const photoSection = document.getElementById('photoSection');
      const modalPhoto = document.getElementById('modalPhoto');
      if (photo && photo !== '') {
        modalPhoto.src = photo;
        photoSection.style.display = 'block';
      } else {
        photoSection.style.display = 'none';
      }

      // Store coordinates using our map helper
      storeMapData('viewMap', lat, lng, reportCode);
    });
  });

  // Handle Validate Anonymous Report button clicks
  document.querySelectorAll('[data-bs-target="#validateReportModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const reportId = this.dataset.reportId;
      const reportCode = this.dataset.reportCode;

      // Update modal title
      document.getElementById('statusModalReportId').textContent = reportCode;

      // Update form action using our helper
      setFormAction('validateReportForm', `/admin/reports/${reportId}/validate`);

      // Reset form using our helper
      resetModalForm('validateReportForm');
    });
  });

  // Handle Edit Report button clicks
  document.querySelectorAll('[data-bs-target="#updateStatusModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const reportId = this.dataset.reportId;
      const reportCode = this.dataset.reportCode;

      // Update modal title
      document.getElementById('updateStatusModalReportId').textContent = reportCode;

      // Populate form fields using our helper
      populateModalFields(this, {
        'reportStatus': 'reportStatus',
        'reportDescription': 'reportDescription',
        'reportPriority': 'reportPriority',
        'adminRemarks': 'adminRemarks',
        'manualPriority': 'manualPriority'
      });

      // Set is_hidden checkbox
      const isHidden = this.dataset.reportIsHidden === '1';
      document.getElementById('isHiddenSwitch').checked = isHidden;

      // Update form action using our helper
      setFormAction('editReportForm', `/admin/reports/${reportId}`);

      // Store report ID in modal for delete button
      const modal = document.getElementById('updateStatusModal');
      modal.dataset.reportId = reportId;
      modal.dataset.reportCode = reportCode;
    });
  });

  // Handle Delete Report button
  document.getElementById('deleteReportBtn').addEventListener('click', function() {
    const modal = document.getElementById('updateStatusModal');
    const reportId = modal.dataset.reportId;
    const reportCode = modal.dataset.reportCode;

    if (confirm(`Are you sure you want to delete report ${reportCode}? This action cannot be undone.`)) {
      // Create and submit delete form
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/admin/reports/${reportId}`;

      // Add CSRF token
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = '{{ csrf_token() }}';
      form.appendChild(csrfInput);

      // Add DELETE method
      const methodInput = document.createElement('input');
      methodInput.type = 'hidden';
      methodInput.name = '_method';
      methodInput.value = 'DELETE';
      form.appendChild(methodInput);

      document.body.appendChild(form);
      form.submit();
    }
  });

  // Initialize map when view modal is shown
  document.getElementById('viewReportModal').addEventListener('shown.bs.modal', function () {
    // Get stored map data
    const mapData = getMapData('viewMap');

    if (!adminMap) {
      // Create map first time (using our helper)
      const defaultLat = mapData?.lat || 12.8797;
      const defaultLng = mapData?.lng || 121.7740;
      adminMap = createMap('viewMap', defaultLat, defaultLng, mapData?.lat ? 15 : 6);
    }

    // If we have coordinates, show them on the map
    if (mapData && mapData.lat && mapData.lng) {
      // Update map view (using our helper)
      updateMapView(adminMap, mapData.lat, mapData.lng, 15);

      // Remove old marker if exists (using our helper)
      if (adminMarker) {
        removeMarker(adminMap, adminMarker);
      }

      // Add new marker (using our helper)
      adminMarker = addMarker(adminMap, mapData.lat, mapData.lng, mapData.label || 'Report Location');

      // Open the popup
      if (adminMarker) {
        adminMarker.openPopup();
      }
    }

    // Fix map display (using our helper)
    refreshMap(adminMap);

    // Initialize lightbox/enlarged map behavior once the base map exists
    if (!window.adminMapLightbox) {
      window.adminMapLightbox = initMapLightbox({
        enlargeButtonId: 'enlargeMapBtn',
        overlayId: 'mapLightboxOverlay',
        containerId: 'mapLightboxContainer',
        enlargedMapId: 'enlargedMap',
        sourceMap: adminMap,
        getSourcePosition: () => {
          const center = adminMap.getCenter();
          const zoom = adminMap.getZoom();

          let hasMarker = false;
          let markerLat = null;
          let markerLng = null;

          if (adminMarker) {
            const pos = adminMarker.getLatLng();
            hasMarker = true;
            markerLat = pos.lat;
            markerLng = pos.lng;
          }

          return {
            lat: center.lat,
            lng: center.lng,
            zoom: zoom,
            hasMarker,
            markerLat,
            markerLng,
            label: 'Report Location',
          };
        },
      });
    }
  });

  // Category Horizontal Bar Chart with Unique Colors per Violation
  const ctx = document.getElementById('categoryChart').getContext('2d');

  // Helper function to create darker shade of hex color
  function darkenColor(hex, percent) {
    const num = parseInt(hex.replace('#', ''), 16);
    const r = Math.max(0, Math.floor((num >> 16) * (1 - percent)));
    const g = Math.max(0, Math.floor(((num >> 8) & 0x00FF) * (1 - percent)));
    const b = Math.max(0, Math.floor((num & 0x0000FF) * (1 - percent)));
    return '#' + ((r << 16) | (g << 8) | b).toString(16).padStart(6, '0');
  }

  // Create gradient for each violation type using actual HEX colors from database
  const colors = {!! json_encode($categoryStats->pluck('color')) !!};
  const gradients = colors.map(hexColor => {
    const gradient = ctx.createLinearGradient(0, 0, 500, 0);
    gradient.addColorStop(0, hexColor);              // Start with original color
    gradient.addColorStop(1, darkenColor(hexColor, 0.2)); // End with 20% darker shade
    return gradient;
  });

  new Chart(ctx, {
    type: 'bar',
    data: {
      labels: {!! json_encode($categoryStats->pluck('name')) !!},
      datasets: [{
        label: 'Reports',
        data: {!! json_encode($categoryStats->pluck('count')) !!},
        backgroundColor: gradients,
        borderRadius: 8,
        borderSkipped: false,
      }]
    },
    options: {
      indexAxis: 'y', // Horizontal bars
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          display: false
        },
        tooltip: {
          backgroundColor: 'rgba(0, 0, 0, 0.85)',
          padding: 14,
          cornerRadius: 8,
          titleFont: {
            size: 15,
            weight: '600'
          },
          bodyFont: {
            size: 14
          },
          displayColors: false,
          callbacks: {
            label: function(context) {
              return context.parsed.x + ' report' + (context.parsed.x !== 1 ? 's' : '');
            }
          }
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          grid: {
            color: 'rgba(0, 0, 0, 0.04)',
            drawBorder: false
          },
          ticks: {
            stepSize: 1,
            precision: 0,
            font: {
              size: 12,
              weight: '500'
            }
          },
          border: {
            display: false
          }
        },
        y: {
          grid: {
            display: false
          },
          ticks: {
            font: {
              size: 13,
              weight: '500'
            },
            padding: 12,
            color: '#495057'
          },
          border: {
            display: false
          }
        }
      },
      barThickness: 28
    }
  });

  // Auto-open modal if URL has hash anchor (from notifications)
  window.addEventListener('load', function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#report-')) {
      const reportId = hash.replace('#report-', '');
      // Find the view button for this report and click it
      const viewButton = document.querySelector(`[data-bs-target="#viewReportModal"][data-report-id="${reportId}"]`);
      if (viewButton) {
        viewButton.click();
        // Remove hash from URL after opening modal
        history.replaceState(null, null, ' ');
      }
    }
  });
</script>
@endpush
