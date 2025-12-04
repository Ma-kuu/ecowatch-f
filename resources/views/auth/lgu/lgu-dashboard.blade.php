@extends('layouts.dashboard')

@section('title', 'LGU Dashboard - EcoWatch')

@section('dashboard-home', route('lgu-dashboard'))

@section('footer-title', 'EcoWatch')

@section('additional-styles')
  #viewMap {
    height: 300px;
    background-color: #e9ecef;
    border-radius: 8px;
  }
@endsection

@section('content')
  <!-- Page Header -->
  <div class="row mb-4">
    <div class="col-12">
      <h2 class="fw-bold" style="color: #198754;">LGU Dashboard - Panabo City</h2>
      <p class="text-muted">Monitor and manage environmental reports in Panabo City, Davao del Norte</p>
    </div>
  </div>

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
          <p class="text-muted small mb-0 mt-2">Total in Panabo City</p>
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
      <div class="d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Assigned Reports</h5>
        <button class="btn btn-success btn-sm">
          <i class="bi bi-download me-1"></i>Export CSV
        </button>
      </div>
    </div>
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0" id="reportsTable">
          <thead class="table-light">
            <tr>
              <th class="px-4 py-3">Report ID</th>
              <th class="py-3">Type of Violation</th>
              <th class="py-3">Date Received</th>
              <th class="py-3">Location</th>
              <th class="py-3">Status</th>
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
                <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#viewReportModal">
                  <i class="bi bi-eye"></i>
                </button>
                <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#markFixedModal" {{ $report->status == 'fixed' ? 'disabled' : '' }}>
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
              <div id="viewMap" class="mb-0"></div>
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
            <i class="bi bi-check-circle"></i> Mark Report as Fixed
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="markFixedForm">
            <!-- Report ID Display -->
            <div class="alert alert-info">
              <small><i class="bi bi-info-circle"></i> You are marking this report as fixed. Please provide proof and details of actions taken.</small>
            </div>

            <!-- Upload Proof Photo -->
            <div class="mb-3">
              <label for="proofPhoto" class="form-label fw-bold">Upload Proof Photo <span class="text-danger">*</span></label>
              <input type="file" class="form-control" id="proofPhoto" accept="image/*" required>
              <div class="form-text">Upload a clear photo showing the issue has been resolved (max 5MB)</div>
            </div>

            <!-- Remarks/Actions Taken -->
            <div class="mb-3">
              <label for="lguRemarks" class="form-label fw-bold">Remarks / Actions Taken <span class="text-danger">*</span></label>
              <textarea class="form-control" id="lguRemarks" rows="5" placeholder="Describe the actions taken to resolve this issue..." required></textarea>
              <div class="form-text">Provide detailed information about how the issue was addressed</div>
            </div>

            <!-- Date Fixed -->
            <div class="mb-3">
              <label for="dateFixed" class="form-label fw-bold">Date Fixed <span class="text-danger">*</span></label>
              <input type="date" class="form-control" id="dateFixed" required>
            </div>

            <!-- Team/Personnel Involved -->
            <div class="mb-3">
              <label for="personnelInvolved" class="form-label fw-bold">Team/Personnel Involved</label>
              <input type="text" class="form-control" id="personnelInvolved" placeholder="e.g., Barangay Clean-up Team, 5 personnel">
              <div class="form-text">Optional: List the team members or units involved</div>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success">
            <i class="bi bi-send"></i> Submit for Verification
          </button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />

<script>
  let viewMap;
  let viewMarker;

  // Initialize map when view modal is shown
  document.getElementById('viewReportModal').addEventListener('shown.bs.modal', function () {
    if (!viewMap) {
      viewMap = L.map('viewMap').setView([12.8797, 121.7740], 6);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
      }).addTo(viewMap);

      viewMarker = L.marker([12.8797, 121.7740]).addTo(viewMap);
    }
    setTimeout(function() {
      viewMap.invalidateSize();
    }, 100);
  });

  // Filter functionality
  function filterTable() {
    const searchValue = document.getElementById('searchInput').value.toLowerCase();
    const statusValue = document.getElementById('statusFilter').value.toLowerCase();
    const typeValue = document.getElementById('typeFilter').value.toLowerCase();
    const table = document.getElementById('reportsTable');
    const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

    for (let i = 0; i < rows.length; i++) {
      const row = rows[i];
      const text = row.textContent.toLowerCase();
      const status = row.getAttribute('data-status');
      const type = row.getAttribute('data-type');

      let matchesSearch = text.includes(searchValue);
      let matchesStatus = statusValue === '' || status === statusValue;
      let matchesType = typeValue === '' || type === typeValue;

      if (matchesSearch && matchesStatus && matchesType) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    }
  }

  // Event listeners
  document.getElementById('searchInput').addEventListener('keyup', filterTable);
  document.getElementById('statusFilter').addEventListener('change', filterTable);
  document.getElementById('typeFilter').addEventListener('change', filterTable);
</script>
@endpush
