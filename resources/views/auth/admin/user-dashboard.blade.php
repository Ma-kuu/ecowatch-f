<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard - EcoWatch</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
  <style>
    body { 
      font-family: 'Inter', sans-serif;
      background-color: #f8f9fa;
    }
    .navbar { 
      box-shadow: 0 2px 4px rgba(0,0,0,0.1); 
    }
    .stat-card {
      border-left: 4px solid;
    }
    .stat-card.pending {
      border-left-color: #ffc107;
    }
    .stat-card.in-review {
      border-left-color: #0dcaf0;
    }
    .stat-card.resolved {
      border-left-color: #fd7e14;
    }
    .stat-card.confirmed {
      border-left-color: #198754;
    }
    .stat-card.total {
      border-left-color: #6c757d;
    }
    .table-actions .btn {
      padding: 0.25rem 0.75rem;
      font-size: 0.875rem;
    }
    #map {
      height: 300px;
      background-color: #e9ecef;
      border-radius: 8px;
    }
    .report-image-preview {
      max-width: 100%;
      height: auto;
      border-radius: 8px;
    }
    .modal-header {
      background-color: #f8f9fa;
      border-bottom: 2px solid #198754;
    }
    .dashboard-content {
      margin-top: 76px;
      min-height: calc(100vh - 200px);
    }
  </style>
</head>
<body>

  <nav class="navbar navbar-expand-lg bg-white fixed-top border-bottom">
    <div class="container">
      <a class="navbar-brand fw-bold text-dark" href="{{ route('index') }}">
        <img src="{{ asset('images/logo text.png') }}" alt="EcoWatch" height="45">
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto align-items-lg-center">
          <li class="nav-item"><a class="nav-link text-dark" href="{{ route('index') }}">Home</a></li>
          <li class="nav-item"><a class="nav-link text-dark" href="{{ route('report-authenticated') }}">Report</a></li>
          <li class="nav-item ms-lg-3">
            <form method="POST" action="{{ route('logout') }}" class="d-inline">
              @csrf
              <button type="submit" class="btn btn-outline-danger px-4">
                <i class="bi bi-box-arrow-right me-1"></i>Log Out
              </button>
            </form>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Dashboard Content -->
  <div class="dashboard-content">
    <div class="container py-4">
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
                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#reportDetailsModal">
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
    </div>
  </div>

  <!-- View Report Details Modal -->
  <div class="modal fade" id="reportDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title fw-bold">Report #ECO-2025-001</h5>
            <small class="text-muted">Submitted on Oct 10, 2025</small>
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
                  <p class="fw-semibold mb-0">Illegal Dumping</p>
                </div>
                <span class="badge bg-warning text-dark">Pending</span>
              </div>
            </div>

            <!-- Location -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-geo-alt-fill me-1"></i>Location
              </h6>
              <p class="mb-2">Tagum City, Davao del Norte</p>
              <!-- Map Placeholder -->
              <div id="map" class="mb-0"></div>
              <small class="text-muted">Interactive map showing report location</small>
            </div>

            <!-- Photo Evidence -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-camera-fill me-1"></i>Photo Evidence
              </h6>
              <img src="https://via.placeholder.com/400x300?text=Illegal+Dumping" alt="Report Evidence" class="report-image-preview shadow-sm">
            </div>

            <!-- Description -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-file-text-fill me-1"></i>Description
              </h6>
              <p class="text-muted">Large pile of construction debris dumped near residential area. Hazardous materials visible.</p>
            </div>

            <!-- Remarks -->
            <div class="col-12">
              <div class="alert alert-light border mb-0">
                <h6 class="text-muted text-uppercase small mb-2">
                  <i class="bi bi-chat-square-text-fill me-1"></i>Admin Remarks
                </h6>
                <p class="mb-0">Awaiting initial review</p>
              </div>
            </div>

            <!-- Resolution Proof -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-image-fill me-1"></i>Resolution Proof Photo
              </h6>
              <img src="https://via.placeholder.com/400x300?text=Resolution+Proof" alt="Resolution Proof" class="img-fluid rounded shadow-sm" style="max-height: 300px; width: 100%; object-fit: cover;">
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success">
            <i class="bi bi-download me-1"></i>Download Report
          </button>
          <button type="button" class="btn btn-danger me-2">
            <i class="bi bi-x-circle me-1"></i>Not Resolved
          </button>
          <button type="button" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>Confirm Resolved
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
      <h5 class="fw-bold mb-3">EcoWatch</h5>
      <p class="text-white-50 small mb-3">Protecting our environment through community reporting.</p>
      <p class="text-white-50 small mb-0">&copy; 2025 EcoWatch. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
  <script>
    let map;
    let marker;

    // Initialize map when modal is shown
    document.getElementById('reportDetailsModal').addEventListener('shown.bs.modal', function () {
      if (!map) {
        map = L.map('map').setView([7.4474, 125.8077], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        marker = L.marker([7.4474, 125.8077]).addTo(map)
          .bindPopup('<b>Illegal Dumping</b><br>Tagum City, Davao del Norte')
          .openPopup();
      }
      setTimeout(function() {
        map.invalidateSize();
      }, 100);
    });

    // Filter functionality
    function filterTable() {
      const searchValue = document.getElementById('searchInput').value.toLowerCase();
      const statusValue = document.getElementById('statusFilter').value.toLowerCase();
      const table = document.getElementById('reportsTable');
      const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');

      for (let i = 0; i < rows.length; i++) {
        const row = rows[i];
        const text = row.textContent.toLowerCase();
        const status = row.getAttribute('data-status');
        
        let matchesSearch = text.includes(searchValue);
        let matchesStatus = statusValue === '' || status === statusValue;
        
        if (matchesSearch && matchesStatus) {
          row.style.display = '';
        } else {
          row.style.display = 'none';
        }
      }
    }

    // Event listeners
    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);
  </script>
</body>
</html>