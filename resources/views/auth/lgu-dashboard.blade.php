@extends('layouts.dashboard')

@section('title', 'LGU Dashboard - EcoWatch')

@section('dashboard-home', route('lgu-dashboard'))

@section('footer-title', 'EcoWatch')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.css" />
@endpush

@section('additional-styles')
  /* Map enlarge button */
  .map-enlarge-btn {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1000;
    background: white;
    border: 2px solid rgba(0,0,0,0.2);
    border-radius: 4px;
    padding: 5px 10px;
    cursor: pointer;
    font-size: 18px;
    box-shadow: 0 1px 5px rgba(0,0,0,0.15);
  }
  .map-enlarge-btn:hover {
    background: #f4f4f4;
  }

  #viewMap {
    height: 300px;
    background-color: #e9ecef;
    border-radius: 8px;
  }

  /* Map lightbox overlay */
  .map-lightbox-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.8);
    z-index: 9998;
    opacity: 0;
    transition: opacity 0.3s ease;
  }
  .map-lightbox-overlay.active {
    display: block;
    opacity: 1;
  }

  .map-lightbox-container {
    display: none;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%) scale(0.9);
    width: 90%;
    height: 85%;
    background: white;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.3);
    z-index: 9999;
    padding: 15px;
    opacity: 0;
    transition: all 0.3s ease;
  }
  .map-lightbox-container.active {
    display: block;
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }

  .map-lightbox-close {
    position: absolute;
    top: 20px;
    right: 20px;
    background: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    font-size: 24px;
    cursor: pointer;
    z-index: 10000;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .map-lightbox-close:hover {
    background: #f0f0f0;
  }

  #enlargedMap {
    width: 100%;
    height: 100%;
    border-radius: 8px;
  }
@endsection

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
    <!-- Reports Assigned Card -->
    <div class="col-6 col-md-4 col-lg">
      <div class="card stat-card total shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Reports Assigned</p>
              <h3 class="fw-bold mb-0">{{ $totalAssigned ?? 0 }}</h3>
            </div>
            <div class="bg-secondary bg-opacity-10 rounded p-3">
              <i class="bi bi-clipboard-data text-secondary" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Total in {{ $lgu->name ?? 'your area' }}</p>
        </div>
      </div>
    </div>

    <!-- Pending Card -->
    <div class="col-6 col-md-4 col-lg">
      <div class="card stat-card pending shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Pending</p>
              <h3 class="fw-bold mb-0">{{ $pendingAssigned ?? 0 }}</h3>
            </div>
            <div class="bg-warning bg-opacity-10 rounded p-3">
              <i class="bi bi-hourglass-split text-warning" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Awaiting action</p>
        </div>
      </div>
    </div>

    <!-- In Progress Card -->
    <div class="col-6 col-md-4 col-lg">
      <div class="card stat-card in-progress shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">In Progress</p>
              <h3 class="fw-bold mb-0">{{ $inProgressAssigned ?? 0 }}</h3>
            </div>
            <div class="bg-info bg-opacity-10 rounded p-3">
              <i class="bi bi-arrow-repeat text-info" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Currently being addressed</p>
        </div>
      </div>
    </div>

    <!-- Fixed / For Verification Card -->
    <div class="col-6 col-md-6 col-lg">
      <div class="card stat-card fixed shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Fixed / For Verification</p>
              <h3 class="fw-bold mb-0">{{ $fixedAssigned ?? 0 }}</h3>
            </div>
            <div class="bg-success bg-opacity-10 rounded p-3">
              <i class="bi bi-check-circle text-success" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Awaiting admin review</p>
        </div>
      </div>
    </div>

    <!-- Verified Card -->
    <div class="col-6 col-md-6 col-lg">
      <div class="card stat-card verified shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Verified</p>
              <h3 class="fw-bold mb-0">{{ $verifiedAssigned ?? 0 }}</h3>
            </div>
            <div class="bg-primary bg-opacity-10 rounded p-3">
              <i class="bi bi-patch-check text-primary" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Completed & verified</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Search -->
  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <select class="form-select" id="typeFilter">
        <option value="">All Report Types</option>
        <option value="illegal-dumping">Illegal Dumping</option>
        <option value="water-pollution">Water Pollution</option>
        <option value="air-pollution">Air Pollution</option>
        <option value="deforestation">Deforestation</option>
        <option value="noise-pollution">Noise Pollution</option>
        <option value="soil-contamination">Soil Contamination</option>
        <option value="wildlife-violations">Wildlife Violations</option>
        <option value="industrial-violations">Industrial Violations</option>
      </select>
    </div>
    <div class="col-md-3">
      <select class="form-select" id="statusFilter">
        <option value="">All Status</option>
        <option value="pending">Pending</option>
        <option value="in-progress">In Progress</option>
        <option value="fixed">Fixed</option>
        <option value="verified">Verified</option>
      </select>
    </div>
    <div class="col-md-6">
      <div class="input-group">
        <span class="input-group-text bg-light border-end-0">
          <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" class="form-control border-start-0" placeholder="Search local reports..." id="searchInput">
      </div>
    </div>
  </div>

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
                        data-reporter="{{ $report->is_anonymous ? 'Anonymous' : ($report->reporter?->name ?? 'N/A') }}">
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
        <small class="text-muted">Showing {{ $lguReports->count() ?? 0 }} of {{ $totalAssigned ?? 0 }} reports</small>
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

  <!-- View Report Modal -->
  <div class="modal fade" id="viewReportModal" tabindex="-1" aria-labelledby="viewReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header" style="background-color: #e9f7ef; border-bottom: 2px solid #198754;">
          <h5 class="modal-title fw-bold" id="viewReportModalLabel" style="color: #198754;">
            <i class="bi bi-file-text"></i> Report Details
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <!-- Report Info -->
            <div class="col-12">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Report ID</h6>
                  <p class="fw-semibold mb-0" id="modalReportId"></p>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Status</h6>
                  <span class="badge" id="modalStatus"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Violation Type</h6>
                  <p class="mb-0" id="modalViolationType"></p>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Date Received</h6>
                  <p class="mb-0" id="modalDateReceived"></p>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Reporter</h6>
                  <p class="mb-0" id="modalReporter"></p>
                </div>
              </div>
            </div>

            <!-- Location -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-geo-alt-fill me-1"></i>Location
              </h6>
              <p class="mb-2" id="modalLocation"></p>
              <!-- Map -->
              <div style="position: relative;">
                <button class="map-enlarge-btn" id="enlargeMapBtn" title="Enlarge map">
                  <i class="bi bi-arrows-fullscreen"></i>
                </button>
                <div id="viewMap" class="mb-0 map-normal"></div>
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

            <!-- Admin Remarks -->
            <div class="col-12">
              <div class="alert alert-light border mb-0">
                <h6 class="text-muted text-uppercase small mb-2">
                  <i class="bi bi-chat-square-text-fill me-1"></i>Admin Remarks
                </h6>
                <p class="mb-0" id="modalAdminRemarks"></p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary" id="btnShowDirections">
            <i class="bi bi-signpost-2"></i> Show Directions
          </button>
          <button type="button" class="btn btn-success" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#markFixedModal">
            <i class="bi bi-check2"></i> Mark as Fixed
          </button>
        </div>
      </div>
    </div>
  </div>

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
          <div class="modal-body">
            <!-- Report ID Display -->
            <div class="alert alert-info">
              <small><i class="bi bi-info-circle"></i> You are marking this report as fixed. Please provide proof and details of actions taken.</small>
            </div>

            <!-- Upload Proof Photo -->
            <div class="mb-3">
              <label for="proofPhoto" class="form-label fw-bold">Upload Proof Photo <span class="text-danger">*</span></label>
              <input type="file" class="form-control" id="proofPhoto" name="proof_photo" accept="image/*" required>
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
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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

  <!-- Map Lightbox -->
  <div class="map-lightbox-overlay" id="mapLightboxOverlay"></div>
  <div class="map-lightbox-container" id="mapLightboxContainer">
    <button class="map-lightbox-close" id="closeLightbox" title="Close">
      <i class="bi bi-x"></i>
    </button>
    <div id="enlargedMap"></div>
  </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
  // Table filtering is now handled automatically by table-filter.js
  // No need for manual event listeners - it auto-initializes!
</script>

<!-- Load Leaflet library and routing plugin -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-routing-machine@3.2.12/dist/leaflet-routing-machine.js"></script>

<!-- Load our helper modules -->
<script src="{{ asset('js/table-filter.js') }}"></script>
<script src="{{ asset('js/modal-helper.js') }}"></script>
<script src="{{ asset('js/map-helper.js') }}"></script>

<script>
  // Map variables for modal view and lightbox
  let lguMap = null;
  let lguEnlargedMap = null;
  let lguMarker = null;
  let lguEnlargedMarker = null;
  let routingControl = null;
  const lguHqLatLng = [{{ $lgu->latitude ?? 0 }}, {{ $lgu->longitude ?? 0 }}];

  // =============================================================================
  // LIGHTBOX FUNCTIONALITY
  // =============================================================================

  // Handle map enlarge button - open lightbox with current map state
  document.getElementById('enlargeMapBtn').addEventListener('click', function() {
    const overlay = document.getElementById('mapLightboxOverlay');
    const container = document.getElementById('mapLightboxContainer');

    // Get stored map data using our helper
    const mapData = getMapData('viewMap');

    // Show lightbox overlay with smooth animation
    overlay.classList.add('active');
    setTimeout(() => container.classList.add('active'), 10);

    // Initialize or update enlarged map after animation
    setTimeout(() => {
      if (!lguEnlargedMap) {
        // Create enlarged map first time (using our helper)
        const defaultLat = mapData?.lat || 7.5;
        const defaultLng = mapData?.lng || 125.8;
        const zoom = mapData?.lat ? 15 : 13;
        lguEnlargedMap = createMap('enlargedMap', defaultLat, defaultLng, zoom);
      } else {
        // Update existing enlarged map (using our helper)
        const lat = mapData?.lat || 7.5;
        const lng = mapData?.lng || 125.8;
        const zoom = mapData?.lat ? 15 : 13;
        updateMapView(lguEnlargedMap, lat, lng, zoom);
        refreshMap(lguEnlargedMap);
      }

      // Add marker if coordinates exist
      if (mapData && mapData.lat && mapData.lng) {
        // Remove old marker if exists (using our helper)
        if (lguEnlargedMarker) {
          removeMarker(lguEnlargedMap, lguEnlargedMarker);
        }

        // Add new marker (using our helper)
        lguEnlargedMarker = addMarker(lguEnlargedMap, mapData.lat, mapData.lng, mapData.label || 'Report Location');

        // Open the popup
        if (lguEnlargedMarker) {
          lguEnlargedMarker.openPopup();
        }
      }
    }, 350);
  });

  // Close lightbox function
  function closeLightbox() {
    const overlay = document.getElementById('mapLightboxOverlay');
    const container = document.getElementById('mapLightboxContainer');
    container.classList.remove('active');
    setTimeout(() => overlay.classList.remove('active'), 300);
  }

  // Close lightbox on button click
  document.getElementById('closeLightbox').addEventListener('click', closeLightbox);

  // Close lightbox on overlay click
  document.getElementById('mapLightboxOverlay').addEventListener('click', closeLightbox);

  // Close lightbox on ESC key press
  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
      closeLightbox();
    }
  });

  // =============================================================================
  // VIEW REPORT MODAL - POPULATE & MAP DISPLAY
  // =============================================================================

  // Handle View Report button clicks
  document.querySelectorAll('[data-bs-target="#viewReportModal"]').forEach(button => {
    button.addEventListener('click', function() {
      const lat = parseFloat(this.dataset.lat);
      const lng = parseFloat(this.dataset.lng);
      const reportCode = this.dataset.reportCode;

      // Store coordinates using our map helper
      storeMapData('viewMap', lat, lng, reportCode);

      // Populate modal fields using our modal helper
      populateModalFields(this, {
        'reportCode': 'modalReportId',
        'violation': 'modalViolationType',
        'created': 'modalDateReceived',
        'reporter': 'modalReporter',
        'description': 'modalDescription',
        'location': 'modalLocation'
      });

      // Set status badge with color (custom logic for badge)
      const statusBadge = document.getElementById('modalStatus');
      statusBadge.textContent = this.dataset.status;
      statusBadge.className = `badge bg-${this.dataset.statusColor}`;
    });
  });

  // Initialize map when view modal is shown
  const viewReportModal = document.getElementById('viewReportModal');
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

    const overlay = document.getElementById('mapLightboxOverlay');
    const container = document.getElementById('mapLightboxContainer');

    // Show lightbox
    overlay.classList.add('active');
    setTimeout(() => container.classList.add('active'), 10);

    // Initialize or update enlarged map with routing
    setTimeout(() => {
      if (!lguEnlargedMap) {
        // Create enlarged map (using our helper)
        lguEnlargedMap = createMap('enlargedMap', mapData.lat, mapData.lng, 13);
      } else {
        // Remove existing routing if any
        if (routingControl) {
          lguEnlargedMap.removeControl(routingControl);
          routingControl = null;
        }
        // Update map view (using our helper)
        updateMapView(lguEnlargedMap, mapData.lat, mapData.lng, 13);
        refreshMap(lguEnlargedMap);
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
      }).addTo(lguEnlargedMap);

      // Fit map to show entire route when routing is calculated
      routingControl.on('routesfound', function(e) {
        const bounds = L.latLngBounds([lguHqLatLng, [mapData.lat, mapData.lng]]);
        lguEnlargedMap.fitBounds(bounds, { padding: [50, 50] });
      });
    }, 350);
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
</script>
@endpush
