@extends('layouts.dashboard')

@section('title', 'LGU Dashboard - EcoWatch')

@section('dashboard-home', route('lgu-dashboard'))

@section('footer-title', 'EcoWatch')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
@endpush

@push('styles')
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
    <div class="col-12">
      <h2 class="fw-bold" style="color: #198754;">LGU Dashboard - {{ $lgu->name ?? 'LGU' }}</h2>
      <p class="text-muted">Monitor and manage environmental reports in {{ $lgu->name ?? 'your municipality' }}, {{ $lgu->province ?? 'Davao del Norte' }}</p>
    </div>
  </div>

  <!-- Success/Error Messages -->
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>
      {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle-fill me-2"></i>
      <strong>Please fix the following errors:</strong>
      <ul class="mb-0 mt-2">
        @foreach($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

 <!-- Summary Cards -->
<div class="row g-4 mb-4">
  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Total Assigned" 
      :value="$totalAssigned ?? 0" 
      icon="bi-file-earmark-text" 
      color="secondary"
      subtitle="Reports in your area"
      :filter-url="route('lgu-dashboard')"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Pending" 
      :value="$pendingAssigned ?? 0" 
      icon="bi-clock-history" 
      color="warning"
      subtitle="Awaiting action"
      :filter-url="route('lgu-dashboard', ['status' => ['pending']])"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="In Progress" 
      :value="$inProgressAssigned ?? 0" 
      icon="bi-tools" 
      color="info"
      subtitle="Being addressed"
      :filter-url="route('lgu-dashboard', ['status' => ['in-progress']])"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Verified" 
      :value="$verifiedAssigned ?? 0" 
      icon="bi-check-circle" 
      color="success"
      subtitle="Successfully resolved"
      :filter-url="route('lgu-dashboard', ['status' => ['resolved']])"
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
            <i class="bi bi-pie-chart me-2 text-primary"></i>Reports by Category
          </h6>
        </div>
        <div class="card-body d-flex align-items-center justify-content-center">
          <canvas id="lguCategoryChart" style="max-height: 200px;"></canvas>
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
          <canvas id="lguStatusChart" style="max-height: 200px;"></canvas>
        </div>
      </div>
    </div>

    <!-- Monthly Trend Chart -->
    <div class="col-lg-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white border-bottom">
          <h6 class="mb-0 fw-bold">
            <i class="bi bi-graph-up me-2 text-info"></i>Monthly Trend
          </h6>
        </div>
        <div class="card-body">
          <canvas id="lguTrendChart" style="max-height: 200px;"></canvas>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Search -->
  <x-dashboard-filters 
    :action="route('lgu-dashboard')"
    :show-violation-type="true"
    :show-status="true"
    :show-barangay="true"
    :show-priority="true"
    :show-date-range="true"
    :show-flagged="false"
    :show-reporter-type="false"
    :show-lgu="false"
    :barangays="auth()->user()->lgu->barangays ?? collect()"
  />

  <!-- Reports Management Table -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
      <h5 class="fw-bold mb-0">Assigned Reports</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="reportsTable">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3">
                <a href="{{ route('lgu-dashboard', [...request()->except(['sort', 'direction']), 'sort' => 'report_id', 'direction' => request('sort') === 'report_id' && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  Report ID
                  @if(request('sort') === 'report_id')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3">Type of Violation</th>
              <th class="py-3">
                <a href="{{ route('lgu-dashboard', [...request()->except(['sort', 'direction']), 'sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
                   class="text-decoration-none text-dark d-flex align-items-center">
                  Date Received
                  @if(request('sort') === 'created_at')
                    <i class="bi bi-arrow-{{ request('direction') === 'asc' ? 'up' : 'down' }} ms-1"></i>
                  @else
                    <i class="bi bi-arrow-down-up ms-1 text-muted"></i>
                  @endif
                </a>
              </th>
              <th class="py-3">Location</th>
              <th class="py-3">
                <a href="{{ route('lgu-dashboard', [...request()->except(['sort', 'direction']), 'sort' => 'status', 'direction' => request('sort') === 'status' && request('direction') === 'asc' ? 'desc' : 'asc']) }}"
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
            @forelse($lguReports ?? [] as $report)
            <tr data-status="{{ $report->status }}" data-type="{{ $report->violation_type }}">
              <td class="px-4 py-3 fw-semibold">{{ $report->report_id }}</td>
              <td class="py-3">
                <i class="bi bi-{{ $report->icon }} text-{{ $report->color }} me-2"></i>{{ $report->violation_type_display }}
              </td>
              <td class="py-3">{{ $report->created_at->format('M d, Y') }}</td>
              <td class="py-3">{{ $report->location }}</td>
              <td class="py-3">
                <span class="badge bg-{{ $report->status_color }}">{{ $report->status_display }}</span>
              </td>
              <td class="py-3 text-center table-actions">
                <button class="btn btn-sm btn-outline-primary me-1"
                        data-bs-toggle="modal"
                        data-bs-target="#viewReportModal"
                        data-report-id="{{ $report->id }}"
                        data-report-code="{{ $report->report_id }}"
                        data-description="{{ $report->description }}"
                        data-location="{{ $report->location }}"
                        data-lat="{{ $report->latitude }}"
                        data-lng="{{ $report->longitude }}"
                        data-status="{{ $report->status_display }}"
                        data-status-color="{{ $report->status_color }}"
                        data-violation="{{ $report->violation_type_display }}"
                        data-created="{{ $report->created_at->format('M d, Y') }}"
                        data-reporter="{{ $report->is_anonymous ? 'Anonymous' : ($report->reporter?->name ?? 'N/A') }}"
                        data-photo="{{ $report->photos->first() ? asset('storage/' . $report->photos->first()->file_path) : '' }}"
                        data-admin-remarks="{{ $report->validity?->notes ?? 'No remarks from admin yet.' }}">
                  <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-success"
                        data-bs-toggle="modal"
                        data-bs-target="#markFixedModal"
                        data-report-id="{{ $report->id }}"
                        data-report-code="{{ $report->report_id }}"
                        {{ in_array($report->status, ['awaiting-confirmation', 'resolved']) ? 'disabled' : '' }}>
                  <i class="bi bi-pencil"></i>
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">No reports assigned</td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-white border-top">
      <div class="d-flex justify-content-between align-items-center">
        <small class="text-muted">Showing {{ $lguReports->firstItem() ?? 0 }} to {{ $lguReports->lastItem() ?? 0 }} of {{ $lguReports->total() ?? 0 }} reports</small>
        {{ $lguReports->links('pagination::bootstrap-5') }}
      </div>
    </div>
  </div>

<!-- View Report Modal -->
<x-modals.view-report />

  <!-- Mark as Fixed Modal -->
  <div class="modal fade" id="markFixedModal" tabindex="-1" aria-labelledby="markFixedModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #e9f7ef; border-bottom: 2px solid #198754;">
          <h5 class="modal-title fw-bold" id="markFixedModalLabel" style="color: #198754;">
            <i class="bi bi-check-circle"></i> Mark Report as Fixed - <span id="fixedReportCode"></span>
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="markFixedForm" method="POST" action="#" enctype="multipart/form-data">
          @csrf
          <div class="modal-body" style="max-height: 60vh; overflow-y: auto;">
            <!-- Report ID Display -->
            <div class="alert alert-info">
              <small><i class="bi bi-info-circle"></i> You are marking this report as fixed. Please provide proof and details of actions taken.</small>
            </div>

            <!-- Upload Proof Photo -->
            <div class="mb-3">
              <label for="proofPhoto" class="form-label fw-bold">Upload Proof Photo <span class="text-danger">*</span></label>
              <input type="file" class="form-control" id="proofPhoto" name="proof_photo" accept="image/jpeg,image/png,image/jpg" required>
              <div class="form-text">Upload a clear photo showing the issue has been resolved (max 5MB)</div>
            </div>

            <!-- Remarks/Actions Taken -->
            <div class="mb-3">
              <label for="lguRemarks" class="form-label fw-bold">Remarks / Actions Taken <span class="text-danger">*</span></label>
              <textarea class="form-control" id="lguRemarks" name="lgu_remarks" rows="5" placeholder="Describe the actions taken to resolve this issue..." required></textarea>
              <div class="form-text">Provide detailed information about how the issue was addressed</div>
            </div>

            <!-- Date Fixed -->
            <div class="mb-3">
              <label for="dateFixed" class="form-label fw-bold">Date Fixed <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="dateFixed" name="date_fixed" max="{{ date('Y-m-d') }}" required>
            </div>

            <!-- Team/Personnel Involved -->
            <div class="mb-3">
              <label for="personnelInvolved" class="form-label fw-bold">Team/Personnel Involved</label>
              <input type="text" class="form-control" id="personnelInvolved" name="personnel_involved" placeholder="e.g., Barangay Clean-up Team, 5 personnel">
              <div class="form-text">Optional: List the team members or units involved</div>
            </div>
          </div>
          <div class="modal-footer bg-light border-top d-flex justify-content-end align-items-center gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-info" id="markBeingAddressedBtn">
              <i class="bi bi-tools"></i> Mark as Being Addressed
            </button>
            <button type="submit" class="btn btn-success" id="submitMarkFixedBtn">
              <span class="submit-text">
                <i class="bi bi-send"></i> Submit for Verification
              </span>
              <span class="submit-loading d-none">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Submitting...
              </span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Create Announcement Modal -->
  <div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="createAnnouncementModalLabel">
            <i class="bi bi-megaphone"></i> Create Public Announcement
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="{{ route('lgu.announcements.store') }}" method="POST">
          @csrf
          <div class="modal-body">
            <div class="mb-3">
              <label for="announcementTitle" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="announcementTitle" name="title" required maxlength="200" placeholder="e.g., Community Clean-up Drive">
            </div>
            <div class="mb-3">
              <label for="announcementContent" class="form-label fw-bold">Content <span class="text-danger">*</span></label>
              <textarea class="form-control" id="announcementContent" name="content" rows="5" required placeholder="Enter the announcement details..."></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="announcementType" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                <select class="form-select" id="announcementType" name="type" required>
                  <option value="info" selected>Info</option>
                  <option value="warning">Warning</option>
                  <option value="urgent">Urgent</option>
                  <option value="success">Success</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="announcementExpires" class="form-label fw-bold">Expires On (Optional)</label>
                <input type="date" class="form-control" id="announcementExpires" name="expires_at" min="{{ date('Y-m-d') }}">
              </div>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="announcementPinned" name="is_pinned" value="1">
              <label class="form-check-label" for="announcementPinned">
                <i class="bi bi-pin-angle-fill"></i> Pin this announcement (appears at top)
              </label>
            </div>
          </div>
          <div class="modal-footer bg-light border-top d-flex justify-content-between align-items-center">
            <a href="{{ route('lgu.announcements.index') }}" class="btn btn-outline-info">
              <i class="bi bi-list-ul"></i> View My Announcements
            </a>
            <div class="d-flex gap-2">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-primary">
                <i class="bi bi-send"></i> Publish Announcement
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Announcement Modal -->
  <div class="modal fade" id="editAnnouncementModal" tabindex="-1" aria-labelledby="editAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="editAnnouncementModalLabel">
            <i class="bi bi-pencil"></i> Edit Announcement
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form id="editAnnouncementForm" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <div class="mb-3">
              <label for="editAnnouncementTitle" class="form-label fw-bold">Title <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="editAnnouncementTitle" name="title" required maxlength="200">
            </div>
            <div class="mb-3">
              <label for="editAnnouncementContent" class="form-label fw-bold">Content <span class="text-danger">*</span></label>
              <textarea class="form-control" id="editAnnouncementContent" name="content" rows="5" required></textarea>
            </div>
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="editAnnouncementType" class="form-label fw-bold">Type <span class="text-danger">*</span></label>
                <select class="form-select" id="editAnnouncementType" name="type" required>
                  <option value="info">Info</option>
                  <option value="warning">Warning</option>
                  <option value="urgent">Urgent</option>
                  <option value="success">Success</option>
                </select>
              </div>
              <div class="col-md-6 mb-3">
                <label for="editAnnouncementExpires" class="form-label fw-bold">Expires On (Optional)</label>
                <input type="date" class="form-control" id="editAnnouncementExpires" name="expires_at" min="{{ date('Y-m-d') }}">
              </div>
            </div>
            <div class="mb-3 form-check">
              <input type="checkbox" class="form-check-input" id="editAnnouncementPinned" name="is_pinned" value="1">
              <label class="form-check-label" for="editAnnouncementPinned">
                <i class="bi bi-pin-angle-fill"></i> Pin this announcement (appears at top)
              </label>
            </div>
          </div>
          <div class="modal-footer bg-light border-top d-flex justify-content-end align-items-center gap-2">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save"></i> Update Announcement
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Map Lightbox Component -->
  <x-map-lightbox />
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />


<script>
document.addEventListener('DOMContentLoaded', function() {
  @if(isset($lguCategoryStats) && $lguCategoryStats->count() > 0)
  // LGU Category Chart (Doughnut) - Clickable
  const lguCategoryCtx = document.getElementById('lguCategoryChart');
  if (lguCategoryCtx) {
    const ctx = lguCategoryCtx.getContext('2d');
    const lguViolationTypeIds = @json($lguCategoryStats->pluck('id'));
    
    new Chart(lguCategoryCtx, {
      type: 'doughnut',
      data: {
        labels: @json($lguCategoryStats->pluck('name')),
        datasets: [{
          data: @json($lguCategoryStats->pluck('count')),
          backgroundColor: @json($lguCategoryStats->pluck('color')),
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
          labels: { padding: 15, font: { size: 11 } }
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
          const violationTypeId = lguViolationTypeIds[index];
          window.location.href = '{{ route("lgu-dashboard") }}?violation_type[]=' + violationTypeId;
        }
      }
    }
  });
  }
  @endif

  // LGU Status Chart (Horizontal Bar)
  const lguStatusCtx = document.getElementById('lguStatusChart');
  if (lguStatusCtx) {
    const ctx2 = lguStatusCtx.getContext('2d');
    new Chart(ctx2, {
      type: 'bar',
      data: {
        labels: ['Pending', 'In Progress', 'Fixed', 'Verified'],
        datasets: [{
          label: 'Reports',
          data: [
            {{ $pendingAssigned ?? 0 }},
            {{ $inProgressAssigned ?? 0 }},
            {{ $fixedAssigned ?? 0 }},
            {{ $verifiedAssigned ?? 0 }}
          ],
          backgroundColor: ['#ffc107', '#0d6efd', '#6c757d', '#198754'],
          borderRadius: 4,
          barThickness: 30
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.5,
        plugins: {
          legend: { display: false }
        },
        scales: {
          x: { 
            beginAtZero: true, 
            ticks: { stepSize: 1 },
            grid: { display: true }
          },
          y: {
            grid: { display: false }
          }
        }
      }
    });
  }

  // LGU Monthly Trend Chart (Line)
  const lguTrendCtx = document.getElementById('lguTrendChart');
  if (lguTrendCtx) {
    const ctx3 = lguTrendCtx.getContext('2d');
    // Get actual monthly data from controller
    const months = @json($monthLabels ?? []);
    const trendData = @json($monthlyTrend ?? []);
    
    new Chart(ctx3, {
      type: 'line',
      data: {
        labels: months,
        datasets: [{
          label: 'Reports',
          data: trendData,
          borderColor: '#17a2b8',
          backgroundColor: 'rgba(23, 162, 184, 0.1)',
          tension: 0.4,
          fill: true,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: '#17a2b8',
          pointBorderColor: '#fff',
          pointBorderWidth: 2
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: true,
        aspectRatio: 1.5,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: function(context) {
                return 'Reports: ' + context.parsed.y;
              }
            }
          }
        },
        scales: {
          y: { 
            beginAtZero: true,
            ticks: { 
              stepSize: 1,
              precision: 0
            }
          },
          x: {
            grid: { display: false }
          }
        }
      }
    });
  }

  // =============================================================================
  // VIEW REPORT MODAL - POPULATE & MAP DISPLAY
  // =============================================================================

  // Handle View Report button clicks
  document.querySelectorAll('[data-bs-target="#viewReportModal"]').forEach(button => {
    button.addEventListener('click', function() {
      console.log('View button clicked', this.dataset);
        
        const lat = parseFloat(this.dataset.lat);
        const lng = parseFloat(this.dataset.lng);
        const reportCode = this.dataset.reportCode;

        // Store coordinates using our map helper
        if (typeof storeMapData === 'function') {
          storeMapData('viewMap', lat, lng, reportCode);
        }

        // Populate modal fields
        const fields = {
          'modalViolationType': this.dataset.violation || 'N/A',
          'modalDateSubmitted': this.dataset.created || 'N/A',
          'modalReporterName': this.dataset.reporter || 'N/A',
          'modalDescription': this.dataset.description || 'No description provided',
          'modalLocation': this.dataset.location || 'Location not specified',
          'modalAdminRemarks': this.dataset.adminRemarks || 'No remarks yet.'
        };

        for (const [id, value] of Object.entries(fields)) {
          const element = document.getElementById(id);
          if (element) {
            element.textContent = value;
            console.log(`Set ${id} to:`, value);
          } else {
            console.error(`Element not found: ${id}`);
          }
        }

        // Set status badge with color
        const statusBadge = document.getElementById('modalStatus');
        if (statusBadge) {
          statusBadge.textContent = this.dataset.status || 'Unknown';
          statusBadge.className = `badge bg-${this.dataset.statusColor || 'secondary'}`;
        }

        // Handle photo display
        const photo = this.dataset.photo;
        const photoSection = document.getElementById('photoSection');
        const modalPhoto = document.getElementById('modalPhoto');
        if (photo && photo !== '' && photoSection && modalPhoto) {
          modalPhoto.src = photo;
          photoSection.style.display = 'block';
        } else if (photoSection) {
          photoSection.style.display = 'none';
      }
    });
  });

  // Initialize map when view modal is shown
  const viewReportModal = document.getElementById('viewReportModal');
  let lightboxInitialized = false;
  
  viewReportModal.addEventListener('shown.bs.modal', function () {
    // Get stored map data
    const mapData = getMapData('viewMap');

    if (!lguMap) {
      // Create map first time (using our helper)
      const defaultLat = mapData?.lat || 7.5;
      const defaultLng = mapData?.lng || 125.8;
      const zoom = mapData?.lat ? 15 : 13;
      lguMap = createMap('viewMap', defaultLat, defaultLng, zoom);
    }

    // If we have coordinates, show them on the map
    if (mapData && mapData.lat && mapData.lng) {
      // Update map view (using our helper)
      updateMapView(lguMap, mapData.lat, mapData.lng, 15);

      // Remove old marker if exists (using our helper)
      if (lguMarker) {
        removeMarker(lguMap, lguMarker);
      }

      // Add new marker (using our helper)
      lguMarker = addMarker(lguMap, mapData.lat, mapData.lng, mapData.label || 'Report Location');

      // Open the popup
      if (lguMarker) {
        lguMarker.openPopup();
      }
    }

    // Fix map display (using our helper)
    refreshMap(lguMap);

    // Initialize map lightbox after modal is shown (button is inside modal)
    if (!lightboxInitialized) {
      mapLightbox = initMapLightbox({
        enlargeButtonId: 'enlargeMapBtn',
        overlayId: 'mapLightboxOverlay',
        containerId: 'mapLightboxContainer',
        enlargedMapId: 'enlargedMap',
        sourceMap: lguMap,
        getSourcePosition: function() {
          const mapData = getMapData('viewMap');
          return {
            lat: mapData?.lat || 7.5,
            lng: mapData?.lng || 125.8,
            zoom: mapData?.lat ? 15 : 13,
            hasMarker: !!(mapData && mapData.lat && mapData.lng),
            markerLat: mapData?.lat,
            markerLng: mapData?.lng,
            label: mapData?.label || 'Report Location'
          };
        }
      });
      lightboxInitialized = true;
    }
  });

  // Remove routing when modal closes (cleanup)
  viewReportModal.addEventListener('hidden.bs.modal', function () {
    if (routingControl) {
      lguMap.removeControl(routingControl);
      routingControl = null;
    }
  });

  // =============================================================================
  // ROUTING / DIRECTIONS FUNCTIONALITY (LGU SPECIFIC)
  // =============================================================================

  // Show directions button handler - open in lightbox with routing
  document.getElementById('btnShowDirections').addEventListener('click', function() {
    // Get stored map data
    const mapData = getMapData('viewMap');

    if (!mapData || !mapData.lat || !mapData.lng) {
      alert('Report location coordinates not available');
      return;
    }

    // Open lightbox using the reusable module
    if (mapLightbox) {
      mapLightbox.open();
    }

    // Add routing after lightbox opens
    setTimeout(() => {
      const enlargedMap = mapLightbox.getEnlargedMap();
      
      if (enlargedMap) {
        // Remove existing routing if any
        if (routingControl) {
          enlargedMap.removeControl(routingControl);
          routingControl = null;
        }

        // Create routing control from LGU HQ to report location
        routingControl = L.Routing.control({
          waypoints: [
            L.latLng(lguHqLatLng[0], lguHqLatLng[1]), // LGU headquarters
            L.latLng(mapData.lat, mapData.lng)         // Report location
          ],
          routeWhileDragging: false,
          showAlternatives: false,
          addWaypoints: false,
          lineOptions: {
            styles: [{ color: '#198754', weight: 5, opacity: 0.7 }] // Green route line
          },
          createMarker: function(i, waypoint, n) {
            // Custom markers for start (LGU HQ) and end (Report)
            const marker = L.marker(waypoint.latLng, {
              draggable: false,
              icon: L.divIcon({
                className: 'routing-marker',
                html: i === 0 ? '<i class="bi bi-building" style="font-size: 24px; color: #198754;"></i>' :
                               '<i class="bi bi-geo-alt-fill" style="font-size: 24px; color: #dc3545;"></i>',
                iconSize: [30, 30],
                iconAnchor: [15, 30]
              })
            });

            marker.bindPopup(i === 0 ? 'LGU Municipal Hall' : 'Report Location');
            return marker;
          }
        }).addTo(enlargedMap);

        // Fit map to show entire route when routing is calculated
        routingControl.on('routesfound', function(e) {
          const bounds = L.latLngBounds([lguHqLatLng, [mapData.lat, mapData.lng]]);
          enlargedMap.fitBounds(bounds, { padding: [50, 50] });
        });
      }
    }, 400);
  });

  // =============================================================================
  // MARK FIXED MODAL - POPULATE & FORM HANDLING
  // =============================================================================

  // Mark Fixed Modal - Populate report data when opening
  const markFixedModal = document.getElementById('markFixedModal');
  markFixedModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const reportId = button.getAttribute('data-report-id');
    const reportCode = button.getAttribute('data-report-code');

    if (!reportId) {
      alert('Error: Report ID not found. Please refresh the page and try again.');
      return;
    }

    // Update modal title
    document.getElementById('fixedReportCode').textContent = reportCode || 'Unknown';

    // Update form action URL using our helper
    setFormAction('markFixedForm', `{{ url('lgu/reports') }}/${reportId}/mark-fixed`);

    // Reset form fields using our helper
    resetModalForm('markFixedForm');
  });

  // Handle form submission with validation and loading state
  document.getElementById('markFixedForm').addEventListener('submit', function(e) {
    // Validate form action is properly set
    if (this.action.includes('#') || !this.action.includes('mark-fixed')) {
      e.preventDefault();
      alert('Error: Form action not properly set. Please close and reopen the modal.');
      return false;
    }

    // Validate file upload using our helper (5MB limit)
    const fileValidation = validateFileUpload('proofPhoto', 5);
    if (!fileValidation.valid) {
      e.preventDefault();
      alert(fileValidation.error);
      return false;
    }

    // Show loading state using our helper
    showLoadingState('submitMarkFixedBtn', 'Submitting...');
  });

  // Handle "Mark as Being Addressed" button
  document.getElementById('markBeingAddressedBtn').addEventListener('click', function() {
    // Extract report ID from form action URL (format: /lgu/reports/{id}/mark-fixed)
    const actionParts = document.getElementById('markFixedForm').action.split('/');
    const reportId = actionParts[actionParts.length - 2]; // Get second-to-last part (the ID)
    
    if (confirm('Mark this report as "Being Addressed"? This will update the status to In Progress.')) {
      // Create a simple form to submit
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/lgu/reports/${reportId}/mark-in-progress`;
      
      // Add CSRF token
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;
      form.appendChild(csrfInput);
      
      document.body.appendChild(form);
      form.submit();
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

  // Handle Edit Announcement Modal
  const editAnnouncementModal = document.getElementById('editAnnouncementModal');
  if (editAnnouncementModal) {
    editAnnouncementModal.addEventListener('show.bs.modal', function(event) {
      const button = event.relatedTarget;
      const id = button.getAttribute('data-id');
      const title = button.getAttribute('data-title');
      const content = button.getAttribute('data-content');
      const type = button.getAttribute('data-type');
      const pinned = button.getAttribute('data-pinned');
      const expires = button.getAttribute('data-expires');

      // Update form action
      const form = document.getElementById('editAnnouncementForm');
      form.action = `/lgu/announcements/${id}`;

      // Populate fields
      document.getElementById('editAnnouncementTitle').value = title;
      document.getElementById('editAnnouncementContent').value = content;
      document.getElementById('editAnnouncementType').value = type;
      document.getElementById('editAnnouncementPinned').checked = pinned === '1';
      document.getElementById('editAnnouncementExpires').value = expires || '';
    });
  }
}); // End DOMContentLoaded
</script>

<!-- Load Leaflet library and routing plugin -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<!-- Load our helper modules -->
<script src="{{ asset('js/table-filter.js') }}"></script>
<script src="{{ asset('js/modal-helper.js') }}"></script>
<script src="{{ asset('js/map-helper.js') }}"></script>
<script src="{{ asset('js/map-lightbox.js') }}"></script>

<script>
  // Map variables for modal view and lightbox (outside DOMContentLoaded so they're globally accessible)
  let lguMap = null;
  let lguMarker = null;
  let routingControl = null;
  let mapLightbox = null;
  const lguHqLatLng = [{{ $lgu->latitude ?? 0 }}, {{ $lgu->longitude ?? 0 }}];
</script>
@endpush
