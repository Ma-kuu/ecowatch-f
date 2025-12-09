@extends('layouts.dashboard')

@section('title', 'User Dashboard - EcoWatch')

@section('dashboard-home', route('user-dashboard'))

@section('nav-links')
  <li class="nav-item"><a class="nav-link text-dark {{ request()->routeIs('report-authenticated') ? 'active' : '' }}" href="{{ route('report-authenticated') }}">Report</a></li>
@endsection

@section('footer-title', 'EcoWatch')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.css" />
<link rel="stylesheet" href="{{ asset('css/map-lightbox.css') }}" />
<style>
  #map {
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
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <h2 class="fw-bold mb-1">Welcome back, {{ $user->name ?? 'User' }}!</h2>
          <p class="text-muted mb-0">Here's an overview of your environmental reports</p>
        </div>
        <a href="{{ route('report-authenticated') }}" class="btn btn-success">
          <i class="bi bi-plus-circle me-1"></i>Report Violation
        </a>
      </div>
    </div>
  </div>

  <!-- Summary Cards -->
<div class="row g-4 mb-4">
  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Pending" 
      :value="$pendingCount ?? 0" 
      icon="bi-clock-history" 
      color="warning"
      subtitle="Awaiting review"
      :filter-url="route('user-dashboard', ['status' => ['pending']])"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="In Review" 
      :value="$inReviewCount ?? 0" 
      icon="bi-search" 
      color="info"
      subtitle="Being investigated"
      :filter-url="route('user-dashboard', ['status' => ['in-review', 'in-progress']])"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Awaiting Confirmation" 
      :value="$awaitingConfirmationCount ?? 0" 
      icon="bi-hourglass-split" 
      color="secondary"
      subtitle="Pending your confirmation"
      :filter-url="route('user-dashboard', ['status' => ['awaiting-confirmation']])"
    />
  </div>

  <div class="col-md-6 col-lg-3">
    <x-stat-card 
      title="Resolved" 
      :value="$confirmedResolvedCount ?? 0" 
      icon="bi-check-circle" 
      color="success"
      subtitle="Successfully resolved"
      :filter-url="route('user-dashboard', ['status' => ['resolved']])"
    />
  </div>
</div>

  <!-- Announcements from User's LGU -->
  @if($announcements->isNotEmpty())
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-white border-bottom py-3">
      <h5 class="fw-bold mb-0">
        <i class="bi bi-megaphone me-2 text-primary"></i>Announcements from {{ auth()->user()->lgu->name ?? 'Your Municipality' }}
      </h5>
    </div>
    <div class="card-body">
      @foreach($announcements as $announcement)
      <div class="alert alert-{{ $announcement->type === 'urgent' ? 'danger' : ($announcement->type === 'warning' ? 'warning' : ($announcement->type === 'success' ? 'success' : 'info')) }} border-start border-4 mb-3" role="alert">
        <div class="d-flex justify-content-between align-items-start">
          <div class="flex-grow-1">
            <h6 class="alert-heading fw-bold mb-1">
              @if($announcement->is_pinned)
                <i class="bi bi-pin-angle-fill me-1"></i>
              @endif
              {{ $announcement->title }}
            </h6>
            <p class="mb-2">{{ $announcement->content }}</p>
            <small class="text-muted">
              <i class="bi bi-calendar"></i> {{ $announcement->created_at->format('M d, Y') }}
              @if($announcement->expires_at)
                • <i class="bi bi-clock"></i> Expires: {{ $announcement->expires_at->format('M d, Y') }}
              @endif
            </small>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
  @endif

  <!-- Filters and Search -->
  <x-dashboard-filters 
    :action="route('user-dashboard')"
    :show-violation-type="true"
    :show-status="true"
    :show-barangay="false"
    :show-priority="false"
    :show-date-range="true"
    :show-flagged="false"
    :show-reporter-type="false"
    :show-lgu="false"
  />

  <!-- Reports Table Section -->
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
      <h5 class="fw-bold mb-0">My Reports</h5>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="reportsTable">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3">Report ID</th>
              <th class="py-3">Type</th>
              <th class="py-3">Date</th>
              <th class="py-3">Location</th>
              <th class="py-3">Status</th>
              <th class="py-3 text-center">Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse($reports ?? [] as $report)
            <tr>
              <td class="px-4 py-3 fw-semibold">{{ $report->report_id }}</td>
              <td class="py-3">
                <i class="bi bi-{{ $report->icon }} text-{{ $report->color }} me-2"></i>{{ $report->violation_type_display }}
              </td>
              <td class="py-3">{{ $report->created_at->format('M d, Y') }}</td>
              <td class="py-3">{{ $report->location }}</td>
              <td class="py-3">
                <span class="badge bg-{{ $report->status_color }}">{{ $report->status_display }}</span>
              </td>
              <td class="py-3 text-center">
                <button class="btn btn-sm btn-outline-primary" 
                        data-bs-toggle="modal" 
                        data-bs-target="#reportDetailsModal"
                        data-report-id="{{ $report->id }}">
                  <i class="bi bi-eye"></i> View
                </button>
              </td>
            </tr>
            @empty
            <tr>
              <td colspan="6" class="text-center py-4 text-muted">
                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                No reports found
              </td>
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

  <!-- View Report Details Modal -->
<x-modals.view-report-user />

  <!-- Rejection Reason Modal -->
  <div class="modal fade" id="rejectionReasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold">Why is this not resolved?</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="rejectionForm" method="POST">
          @csrf
          <div class="modal-body">
            <p class="text-muted mb-3">Please explain why the issue hasn't been resolved. This feedback will help the LGU address the problem properly.</p>

            <div class="mb-3">
              <label for="rejectionReason" class="form-label">Reason <span class="text-danger">*</span></label>
              <textarea
                class="form-control"
                id="rejectionReason"
                name="rejection_reason"
                rows="4"
                placeholder="Example: The garbage is still there, only partially cleaned..."
                required
                minlength="10"
                maxlength="500"></textarea>
              <div class="form-text">Minimum 10 characters, maximum 500 characters</div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">
              <i class="bi bi-send me-1"></i>Submit Feedback
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Reusable Map Lightbox Component for Enlarged Map View -->
  <x-map-lightbox />
@endsection

@push('scripts')
<!-- Load Leaflet library -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.js"></script>

<!-- Load our helper modules -->
<script src="{{ asset('js/table-filter.js') }}"></script>
<script src="{{ asset('js/map-helper.js') }}"></script>
<script src="{{ asset('js/map-lightbox.js') }}"></script>

<script>
  // Map variables
  let userMap = null;
  let userMarker = null;
  let userMapLightbox = null;

  // Variable to store current report ID for confirm/reject actions
  let currentReportId = null;

  // Handle View Report button clicks
  // Store the report coordinates so we can use them when modal opens
  document.querySelectorAll('[data-bs-target="#reportDetailsModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const lat = parseFloat(this.dataset.lat);
      const lng = parseFloat(this.dataset.lng);
      const reportCode = this.dataset.reportCode;
      const reportId = this.dataset.reportId;
      const statusRaw = this.dataset.statusRaw;
      const description = this.dataset.description;
      const violation = this.dataset.violation;
      const location = this.dataset.location;
      const created = this.dataset.created;
      const status = this.dataset.status;
      const photo = this.dataset.photo;
      const remarks = this.dataset.remarks;
      const resolutionProof = this.dataset.resolutionProof;

      // Store current report ID for confirm/reject buttons
      currentReportId = reportId;

      // Populate modal fields
      document.getElementById('modalReportId').textContent = reportCode;
      document.getElementById('modalDescription').textContent = description;
      document.getElementById('modalViolationType').textContent = violation;
      document.getElementById('modalLocation').textContent = location;
      document.getElementById('modalSubmittedDate').textContent = created;
      document.getElementById('modalStatus').textContent = status;
      document.getElementById('modalRemarks').textContent = remarks;

      // Handle photo evidence
      const photoElement = document.getElementById('modalPhotoEvidence');
      const photoSection = photoElement.closest('.col-12');
      if (photo && photo.trim() !== '') {
        photoElement.src = photo;
        photoElement.style.display = 'block';
        photoSection.style.display = 'block';
      } else {
        photoElement.style.display = 'none';
        photoSection.style.display = 'none';
      }

      // Handle resolution proof photo
      const resolutionProofElement = document.getElementById('modalResolutionProof');
      const resolutionProofSection = resolutionProofElement.closest('.col-12');
      if (resolutionProof && resolutionProof.trim() !== '') {
        resolutionProofElement.src = resolutionProof;
        resolutionProofElement.style.display = 'block';
        resolutionProofSection.style.display = 'block';
      } else {
        resolutionProofElement.style.display = 'none';
        resolutionProofSection.style.display = 'none';
      }

      // Show/hide confirmation buttons based on status
      const confirmationButtons = document.getElementById('confirmationButtons');
      if (statusRaw === 'awaiting-confirmation') {
        confirmationButtons.style.display = 'block';
      } else {
        confirmationButtons.style.display = 'none';
      }

      // Store coordinates using our map helper
      storeMapData('map', lat, lng, reportCode);
    });
  });

  // Initialize map when modal is shown
  document.getElementById('reportDetailsModal').addEventListener('shown.bs.modal', function () {
    // Get the stored map data
    const mapData = getMapData('map');

    if (!userMap) {
      // Create map first time modal opens (using our helper function)
      userMap = createMap('map', 12.8797, 121.7740, 6);

      // Add fullscreen control
      L.control.fullscreen({
        position: 'topleft'
      }).addTo(userMap);
    }

    // If we have coordinates, show them on the map
    if (mapData && mapData.lat && mapData.lng) {
      // Update map view to report location
      updateMapView(userMap, mapData.lat, mapData.lng, 15);

      // Remove old marker if exists
      if (userMarker) {
        removeMarker(userMap, userMarker);
      }

      // Add new marker at report location
      userMarker = addMarker(userMap, mapData.lat, mapData.lng, mapData.label || 'Report Location');

      // Open the popup
      if (userMarker) {
        userMarker.openPopup();
      }
    }

    // Fix map display (important for maps in modals)
    refreshMap(userMap);

    // Initialize lightbox/enlarged map behavior once the base map exists
    if (!userMapLightbox) {
      userMapLightbox = initMapLightbox({
        enlargeButtonId: 'enlargeMapBtn',
        overlayId: 'mapLightboxOverlay',
        containerId: 'mapLightboxContainer',
        enlargedMapId: 'enlargedMap',
        sourceMap: userMap,
        getSourcePosition: () => {
          const mapData = getMapData('map');
          return {
            lat: mapData?.lat || 12.8797,
            lng: mapData?.lng || 121.7740,
            zoom: mapData?.lat ? 15 : 6,
            hasMarker: !!(mapData && mapData.lat && mapData.lng),
            markerLat: mapData?.lat,
            markerLng: mapData?.lng,
            label: mapData?.label || 'Report Location',
          };
        },
      });
    }
  });

  // =============================================================================
  // USER CONFIRMATION FUNCTIONALITY
  // =============================================================================

  // Handle Confirm Resolved button click
  document.getElementById('btnConfirmResolution').addEventListener('click', function() {
    if (!currentReportId) {
      alert('Error: Report ID not found. Please refresh and try again.');
      return;
    }

    // Confirm with user
    if (confirm('Are you sure the issue has been resolved? This will mark the report as completed.')) {
      // Create and submit form to confirm resolution
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = `/user/reports/${currentReportId}/confirm`;

      // Add CSRF token
      const csrfInput = document.createElement('input');
      csrfInput.type = 'hidden';
      csrfInput.name = '_token';
      csrfInput.value = '{{ csrf_token() }}';
      form.appendChild(csrfInput);

      document.body.appendChild(form);
      form.submit();
    }
  });

  // Handle Reject Resolution button click
  document.getElementById('btnRejectResolution').addEventListener('click', function() {
    if (!currentReportId) {
      alert('Error: Report ID not found. Please refresh and try again.');
      return;
    }

    // Close the report details modal
    const reportDetailsModal = bootstrap.Modal.getInstance(document.getElementById('reportDetailsModal'));
    reportDetailsModal.hide();

    // Open rejection reason modal
    const rejectionModal = new bootstrap.Modal(document.getElementById('rejectionReasonModal'));
    rejectionModal.show();

    // Set form action for rejection
    const rejectionForm = document.getElementById('rejectionForm');
    rejectionForm.action = `/user/reports/${currentReportId}/reject`;
  });

  // Handle rejection form submission
  document.getElementById('rejectionForm').addEventListener('submit', function(e) {
    const reasonInput = document.getElementById('rejectionReason');

    // Validate reason length
    if (reasonInput.value.trim().length < 10) {
      e.preventDefault();
      alert('Please provide a reason with at least 10 characters.');
      return false;
    }

    // Form will submit normally
    // Could add loading state here if needed
  });

  // Table filtering is now handled automatically by table-filter.js
  // No need for manual event listeners - it auto-initializes!

  // Auto-open modal if URL has hash anchor (from notifications)
  window.addEventListener('load', function() {
    const hash = window.location.hash;
    if (hash && hash.startsWith('#report-')) {
      const reportId = hash.replace('#report-', '');
      // Find the view button for this report and click it
      const viewButton = document.querySelector(`[data-bs-target="#reportDetailsModal"][data-report-id="${reportId}"]`);
      if (viewButton) {
        viewButton.click();
        // Remove hash from URL after opening modal
        history.replaceState(null, null, ' ');
      }
    }
  });
</script>
@endpush
