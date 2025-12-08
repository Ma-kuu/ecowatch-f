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
      <h2 class="fw-bold mb-1">Welcome back, {{ $user->name ?? 'User' }}!</h2>
      <p class="text-muted">Here's an overview of your environmental reports</p>
    </div>
  </div>

  <!-- Summary Cards -->
  <div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
      <div class="card stat-card pending shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Pending</p>
              <h3 class="fw-bold mb-0">{{ $pendingCount ?? 0 }}</h3>
            </div>
            <div class="bg-warning bg-opacity-10 rounded p-3">
              <i class="bi bi-clock-history text-warning" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Awaiting review</p>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="card stat-card in-review shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">In Review</p>
              <h3 class="fw-bold mb-0">{{ $inReviewCount ?? 0 }}</h3>
            </div>
            <div class="bg-info bg-opacity-10 rounded p-3">
              <i class="bi bi-search text-info" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Being investigated</p>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="card stat-card resolved shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Awaiting Confirmation</p>
              <h3 class="fw-bold mb-0">{{ $awaitingConfirmationCount ?? 0 }}</h3>
            </div>
            <div class="bg-warning bg-opacity-25 rounded p-3">
              <i class="bi bi-hourglass-split text-warning" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Resolved, pending confirmation</p>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="card stat-card confirmed shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Confirmed Resolved</p>
              <h3 class="fw-bold mb-0">{{ $confirmedResolvedCount ?? 0 }}</h3>
            </div>
            <div class="bg-success bg-opacity-10 rounded p-3">
              <i class="bi bi-check-circle-fill text-success" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Successfully confirmed</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Reports Table Section -->
  <div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom py-3">
      <div class="row g-3 align-items-center">
        <div class="col-lg-4">
          <h5 class="fw-bold mb-0">My Reports</h5>
        </div>
        <div class="col-lg-8">
          <div class="row g-2">
            <div class="col-md-6">
              <div class="input-group">
                <span class="input-group-text bg-light border-end-0">
                  <i class="bi bi-search text-muted"></i>
                </span>
                <input type="text" class="form-control border-start-0" placeholder="Search reports..." id="searchInput">
              </div>
            </div>
            <div class="col-md-4">
              <select class="form-select" id="statusFilter">
                <option value="">All Status</option>
                <option value="pending">Pending</option>
                <option value="in-review">In Review</option>
                <option value="resolved-awaiting">Resolved (Awaiting Confirmation)</option>
                <option value="confirmed-resolved">Confirmed Resolved</option>
              </select>
            </div>
            <div class="col-md-2">
              <a href="{{ route('report-authenticated') }}" class="btn btn-success w-100">
                <i class="bi bi-plus-lg"></i>
              </a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="reportsTable">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3">Report ID</th>
              <th class="py-3">Type of Violation</th>
              <th class="py-3">Date Submitted</th>
              <th class="py-3">Location</th>
              <th class="py-3">Status</th>
              <th class="py-3">Remarks</th>
              <th class="py-3 text-center">Action</th>
            </tr>
          </thead>
          <tbody>
            @forelse($userReports ?? [] as $report)
            <tr data-status="{{ $report->status }}">
              <td class="px-4 py-3 fw-semibold">{{ $report->report_id }}</td>
              <td class="py-3">
                <i class="bi bi-{{ $report->icon }} text-{{ $report->color }} me-2"></i>{{ $report->violation_type_display }}
              </td>
              <td class="py-3">{{ $report->created_at->format('M d, Y') }}</td>
              <td class="py-3">{{ $report->location }}</td>
              <td class="py-3">
                <span class="badge bg-{{ $report->status_color }}">{{ $report->status_display }}</span>
              </td>
              <td class="py-3">
                <small class="text-muted">{{ $report->remarks ?? 'No remarks' }}</small>
              </td>
              <td class="py-3 text-center table-actions">
                <button class="btn btn-sm btn-outline-success"
                        data-bs-toggle="modal"
                        data-bs-target="#reportDetailsModal"
                        data-report-id="{{ $report->id }}"
                        data-report-code="{{ $report->report_id }}"
                        data-description="{{ $report->description }}"
                        data-location="{{ $report->location }}"
                        data-lat="{{ $report->latitude }}"
                        data-lng="{{ $report->longitude }}"
                        data-status="{{ $report->status_display }}"
                        data-status-raw="{{ $report->status }}"
                        data-violation="{{ $report->violation_type_display }}"
                        data-created="{{ $report->created_at->format('M d, Y') }}"
                        data-photo="{{ $report->photos?->where('is_primary', true)->first()?->file_path ? asset('storage/' . $report->photos?->where('is_primary', true)->first()?->file_path) : ($report->photos?->first()?->file_path ? asset('storage/' . $report->photos?->first()?->file_path) : '') }}"
                        data-remarks="{{ $report->remarks ?? 'No remarks' }}"
                        data-resolution-proof="{{ $report->photos?->where('is_primary', false)->first()?->file_path ? asset('storage/' . $report->photos?->where('is_primary', false)->first()?->file_path) : '' }}">
                  <i class="bi bi-eye me-1"></i>View
                </button>
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
        <small class="text-muted">Showing {{ $userReports->count() ?? 0 }} of {{ $totalUserReports ?? 0 }} reports</small>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item disabled">
              <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">4</a></li>
            <li class="page-item">
              <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>

  <!-- View Report Details Modal -->
  <div class="modal fade" id="reportDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title fw-bold" id="modalReportId">Report #</h5>
            <small class="text-muted" id="modalSubmittedDate"></small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <!-- Confirmation Notice -->
          <div class="alert alert-info mb-3">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Action Required:</strong> Please review the admin's remarks and resolution proof photo below. Confirm if the issue has been resolved or provide feedback if it hasn't.
          </div>

          <div class="row g-4">
            <!-- Report Info -->
            <div class="col-12">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                  <h6 class="text-muted text-uppercase small mb-1">Violation Type</h6>
                  <p class="fw-semibold mb-0" id="modalViolationType"></p>
                </div>
                <span class="badge" id="modalStatus"></span>
              </div>
            </div>

            <!-- Location -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-geo-alt-fill me-1"></i>Location
              </h6>
              <p class="mb-2" id="modalLocation"></p>
              <!-- Map with enlarge button -->
              <div style="position: relative;">
                <button class="map-enlarge-btn" id="enlargeMapBtn" title="Enlarge map">
                  <i class="bi bi-arrows-fullscreen"></i>
                </button>
                <div id="map" class="mb-0"></div>
              </div>
              <small class="text-muted">Interactive map showing report location</small>
            </div>

            <!-- Photo Evidence -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-camera-fill me-1"></i>Photo Evidence
              </h6>
              <img id="modalPhotoEvidence" src="" alt="Report Evidence" class="report-image-preview shadow-sm">
            </div>

            <!-- Description -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-file-text-fill me-1"></i>Description
              </h6>
              <p class="text-muted" id="modalDescription"></p>
            </div>

            <!-- Remarks -->
            <div class="col-12">
              <div class="alert alert-light border mb-0">
                <h6 class="text-muted text-uppercase small mb-2">
                  <i class="bi bi-chat-square-text-fill me-1"></i>Admin Remarks
                </h6>
                <p class="mb-0" id="modalRemarks"></p>
              </div>
            </div>

            <!-- Resolution Proof -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-image-fill me-1"></i>Resolution Proof Photo
              </h6>
              <img id="modalResolutionProof" src="" alt="Resolution Proof" class="img-fluid rounded shadow-sm" style="max-height: 300px; width: 100%; object-fit: cover;">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

          <!-- Show confirm/reject buttons only for awaiting-confirmation reports -->
          <div id="confirmationButtons" style="display: none;">
            <button type="button" class="btn btn-danger me-2" id="btnRejectResolution">
              <i class="bi bi-x-circle me-1"></i>Not Resolved
            </button>
            <button type="button" class="btn btn-success" id="btnConfirmResolution">
              <i class="bi bi-check-circle me-1"></i>Confirm Resolved
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Rejection Reason Modal -->
  <div class="modal fade" id="rejectionReasonModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
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
</script>
@endpush
