@extends('layouts.dashboard')

@section('title', 'Admin Dashboard - EcoWatch')

@section('dashboard-home', route('admin-dashboard'))

@section('nav-links')
  <li class="nav-item"><a class="nav-link text-dark" href="{{ route('admin-settings') }}">Settings</a></li>
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
      <div class="card stat-card total shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Total Reports</p>
              <h3 class="fw-bold mb-0">{{ $totalReports ?? 0 }}</h3>
            </div>
            <div class="bg-secondary bg-opacity-10 rounded p-3">
              <i class="bi bi-file-earmark-text text-secondary" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">All time submissions</p>
        </div>
      </div>
    </div>

    <div class="col-md-6 col-lg-3">
      <div class="card stat-card pending shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start">
            <div>
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Pending Reports</p>
              <h3 class="fw-bold mb-0">{{ $pendingReports ?? 0 }}</h3>
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
              <h3 class="fw-bold mb-0">{{ $inReviewReports ?? 0 }}</h3>
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
              <p class="text-muted text-uppercase small mb-1 fw-semibold">Resolved</p>
              <h3 class="fw-bold mb-0">{{ $resolvedReports ?? 0 }}</h3>
            </div>
            <div class="bg-success bg-opacity-10 rounded p-3">
              <i class="bi bi-check-circle text-success" style="font-size: 24px;"></i>
            </div>
          </div>
          <p class="text-muted small mb-0 mt-2">Successfully resolved</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Filters and Search -->
  <form method="GET" action="{{ route('admin-dashboard') }}" id="filterForm">
    <div class="row g-3 mb-4">
      <div class="col-md-3">
        <select class="form-select" name="violation_type" id="typeFilter" onchange="document.getElementById('filterForm').submit()">
          <option value="">All Report Types</option>
          @foreach(\App\Models\ViolationType::orderBy('name')->get() as $type)
            <option value="{{ $type->id }}" {{ request('violation_type') == $type->id ? 'selected' : '' }}>
              {{ $type->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <select class="form-select" name="status" id="statusFilter" onchange="document.getElementById('filterForm').submit()">
          <option value="">All Status</option>
          <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="in-review" {{ request('status') == 'in-review' ? 'selected' : '' }}>In Review</option>
          <option value="in-progress" {{ request('status') == 'in-progress' ? 'selected' : '' }}>In Progress</option>
          <option value="awaiting-confirmation" {{ request('status') == 'awaiting-confirmation' ? 'selected' : '' }}>Awaiting Confirmation</option>
          <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
        </select>
      </div>
      <div class="col-md-6">
        <div class="input-group">
          <span class="input-group-text bg-light border-end-0">
            <i class="bi bi-search text-muted"></i>
          </span>
          <input type="text" class="form-control border-start-0" placeholder="Search reports..." name="search" id="searchInput" value="{{ request('search') }}">
          <button type="submit" class="btn btn-primary">Search</button>
        </div>
      </div>
    </div>
  </form>

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
                        data-photo="{{ $report->photos->first() ? asset('storage/' . $report->photos->first()->file_path) : '' }}">
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
                          data-report-remarks="{{ $report->admin_remarks ?? '' }}"
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
        <small class="text-muted">Showing {{ $reports->count() ?? 0 }} of {{ $totalReports ?? 0 }} reports</small>
        <nav>
          <ul class="pagination pagination-sm mb-0">
            <li class="page-item disabled">
              <a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a>
            </li>
            <li class="page-item active"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">4</a></li>
            <li class="page-item"><a class="page-link" href="#">5</a></li>
            <li class="page-item">
              <a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a>
            </li>
          </ul>
        </nav>
      </div>
    </div>
  </div>

  <!-- Analytics Section -->
  <div class="row g-4">
    <div class="col-lg-8">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white border-bottom">
          <h5 class="fw-bold mb-0">Reports by Category</h5>
        </div>
        <div class="card-body" style="min-height: 350px;">
          <canvas id="categoryChart"></canvas>
        </div>
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-header bg-white border-bottom">
          <h5 class="fw-bold mb-0">Summary Statistics</h5>
        </div>
        <div class="card-body">
          @forelse($categoryStats ?? [] as $category)
          <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
              <span class="text-muted">{{ $category->name }}</span>
              <span class="fw-semibold">{{ $category->count }}</span>
            </div>
            <div class="progress" style="height: 8px;">
              <div class="progress-bar bg-{{ $category->color }}" style="width: {{ $category->percentage }}%"></div>
            </div>
          </div>
          @empty
          <p class="text-muted text-center">No statistics available</p>
          @endforelse
        </div>
      </div>
    </div>
  </div>

  <!-- View Report Modal -->
  <div class="modal fade" id="viewReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title fw-bold">Report Details</h5>
            <small class="text-muted" id="modalSubmittedDate"></small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="row g-4">
            <!-- Report Info -->
            <div class="col-12">
              <div class="row">
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Violation Type</h6>
                  <p class="fw-semibold mb-0" id="modalViolationType"></p>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Status</h6>
                  <span class="badge" id="modalStatus"></span>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Submitted By</h6>
                  <p class="mb-0" id="modalReporterName"></p>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Date Submitted</h6>
                  <p class="mb-0" id="modalDateSubmitted"></p>
                </div>
              </div>
            </div>

            <!-- Location -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-geo-alt-fill me-1"></i>Location
              </h6>
              <p class="mb-2" id="modalLocation"></p>
              <div style="position: relative;">
                <button class="map-enlarge-btn" id="enlargeMapBtn" title="Enlarge map">
                  <i class="bi bi-arrows-fullscreen"></i>
                </button>
                <div id="viewMap" class="mb-0 map-normal"></div>
              </div>
              <small class="text-muted">Interactive map showing report location</small>
            </div>

            <!-- Photo Evidence -->
            <div class="col-12" id="photoSection" style="display: none;">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-camera-fill me-1"></i>Photo Evidence
              </h6>
              <img id="modalPhoto" src="" alt="Report Evidence" class="report-image-preview shadow-sm">
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
                <p class="mb-0" id="modalRemarks">No remarks yet.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Validate Anonymous Report Modal -->
  <div class="modal fade" id="validateReportModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header bg-light">
          <div>
            <h5 class="modal-title fw-bold">
              <i class="bi bi-shield-check text-primary me-2"></i>Validate Report
            </h5>
            <small class="text-muted" id="statusModalReportId"></small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="validateReportForm" method="POST">
          @csrf
          <div class="modal-body">
            <div class="alert alert-info">
              <i class="bi bi-info-circle me-2"></i>
              <strong>Validation:</strong> Determine if this report is legitimate.
              <ul class="mb-0 mt-2">
                <li><strong>Valid:</strong> Report will be assigned to LGU and appear in public feed</li>
                <li><strong>Invalid:</strong> Report will be hidden from feed</li>
              </ul>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold">Validation Decision</label>
              <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="validity_status" id="validOption" value="valid" required>
                <label class="btn btn-outline-success" for="validOption">
                  <i class="bi bi-check-circle me-1"></i>Valid Report
                </label>

                <input type="radio" class="btn-check" name="validity_status" id="invalidOption" value="invalid" required>
                <label class="btn btn-outline-danger" for="invalidOption">
                  <i class="bi bi-x-circle me-1"></i>Invalid Report
                </label>
              </div>
            </div>

            <div class="mb-3">
              <label for="validationNotes" class="form-label fw-semibold">Notes <span class="text-muted">(Optional)</span></label>
              <textarea class="form-control" name="notes" id="validationNotes" rows="3" placeholder="Add any notes about your validation decision..."></textarea>
              <small class="text-muted">Explain why this report is valid or invalid.</small>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-shield-check me-1"></i>Submit Validation
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Edit Report Modal (for all validated reports) -->
  <div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header bg-light">
          <div>
            <h5 class="modal-title fw-bold">
              <i class="bi bi-pencil-square text-success me-2"></i>Edit Report
            </h5>
            <small class="text-muted" id="updateStatusModalReportId"></small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <form id="editReportForm" method="POST">
          @csrf
          @method('PUT')
          <div class="modal-body">
            <!-- Report Status -->
            <div class="mb-4">
              <label for="reportStatus" class="form-label fw-semibold">
                <i class="bi bi-flag me-1"></i>Report Status
              </label>
              <select class="form-select" name="status" id="reportStatus" required>
                <option value="pending">Pending</option>
                <option value="in-review">In Review</option>
                <option value="in-progress">In Progress</option>
                <option value="awaiting-confirmation">Awaiting Confirmation</option>
                <option value="resolved">Resolved</option>
              </select>
            </div>

            <!-- Description -->
            <div class="mb-3">
              <label for="reportDescription" class="form-label fw-semibold">
                <i class="bi bi-file-text me-1"></i>Description
              </label>
              <textarea class="form-control" name="description" id="reportDescription" rows="4" required></textarea>
            </div>

            <!-- Admin Remarks -->
            <div class="mb-3">
              <label for="adminRemarks" class="form-label fw-semibold">
                <i class="bi bi-chat-square-text me-1"></i>Admin Remarks
              </label>
              <textarea class="form-control" name="admin_remarks" id="adminRemarks" rows="3" placeholder="Add administrative notes..."></textarea>
            </div>

            <!-- Priority -->
            <div class="mb-3">
              <label for="reportPriority" class="form-label fw-semibold">
                <i class="bi bi-exclamation-triangle me-1"></i>Priority
              </label>
              <select class="form-select" name="priority" id="reportPriority">
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>

            <hr class="my-4">

            <!-- Feed Visibility Controls -->
            <div class="mb-3">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="isHiddenSwitch" name="is_hidden" value="1">
                <label class="form-check-label fw-semibold" for="isHiddenSwitch">
                  <i class="bi bi-eye-slash me-1"></i>Hidden from public feed
                </label>
                <small class="text-muted d-block mt-1">When checked, this report will not appear in the public feed</small>
              </div>
            </div>

            <!-- Manual Priority -->
            <div class="mb-3">
              <label for="manualPriority" class="form-label fw-semibold">
                <i class="bi bi-sort-down me-1"></i>Feed Ranking Priority
              </label>
              <select class="form-select" name="manual_priority" id="manualPriority">
                <option value="normal">Normal</option>
                <option value="boosted">Boosted (show higher in feed)</option>
                <option value="suppressed">Suppressed (show lower in feed)</option>
              </select>
              <small class="text-muted">Controls where this report appears in the feed regardless of upvotes</small>
            </div>
          </div>
          <div class="modal-footer d-flex justify-content-between">
            <div>
              <!-- Delete button on the left -->
              <button type="button" class="btn btn-danger" id="deleteReportBtn">
                <i class="bi bi-trash me-1"></i>Delete Report
              </button>
            </div>
            <div>
              <!-- Action buttons on the right -->
              <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-success">
                <i class="bi bi-check-circle me-1"></i>Save Changes
              </button>
            </div>
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

<!-- Load our helper modules -->
<script src="{{ asset('js/modal-helper.js') }}"></script>
<script src="{{ asset('js/map-helper.js') }}"></script>
<script src="{{ asset('js/map-lightbox.js') }}"></script>

<script>
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

      // Populate modal fields
      document.getElementById('modalViolationType').textContent = violationType;
      document.getElementById('modalDescription').textContent = description;
      document.getElementById('modalLocation').textContent = location;
      document.getElementById('modalReporterName').textContent = reporter;
      document.getElementById('modalDateSubmitted').textContent = date;
      document.getElementById('modalStatus').textContent = status;
      document.getElementById('modalStatus').className = 'badge bg-' + statusColor;

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
</script>
@endpush
