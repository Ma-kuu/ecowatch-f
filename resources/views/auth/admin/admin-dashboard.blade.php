<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - EcoWatch</title>
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
    .stat-card.total {
      border-left-color: #6c757d;
    }
    .stat-card.pending {
      border-left-color: #ffc107;
    }
    .stat-card.in-review {
      border-left-color: #0dcaf0;
    }
    .stat-card.resolved {
      border-left-color: #198754;
    }
    .table-actions .btn {
      padding: 0.25rem 0.75rem;
      font-size: 0.875rem;
    }
    #viewMap {
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
       margin-top: 80px;
       padding-top: 2rem;
       min-height: calc(100vh - 200px);
     }
    .chart-placeholder {
      height: 250px;
      background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
      border-radius: 8px;
      display: flex;
      align-items: center;
      justify-content: center;
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
           <li class="nav-item"><a class="nav-link text-dark" href="{{ route('admin-settings') }}">Settings</a></li>
           <li class="nav-item"><a class="nav-link text-dark" href="{{ route('feed') }}">Feed</a></li>
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
    <div class="container">
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
            <option value="in-review">In Review</option>
            <option value="resolved">Resolved</option>
          </select>
        </div>
        <div class="col-md-6">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" class="form-control border-start-0" placeholder="Search reports..." id="searchInput">
          </div>
        </div>
      </div>

      <!-- Reports Management Table -->
      <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white border-bottom py-3">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">Reports Management</h5>
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
                  <th class="py-3">Reporter Name</th>
                  <th class="py-3">Type of Violation</th>
                  <th class="py-3">Date Submitted</th>
                  <th class="py-3">Location</th>
                  <th class="py-3">Status</th>
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
                  </td>
                  <td class="py-3 text-center table-actions">
                    <button class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#viewReportModal">
                      <i class="bi bi-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#updateStatusModal">
                      <i class="bi bi-pencil"></i>
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
          <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom">
              <h5 class="fw-bold mb-0">Reports by Category</h5>
            </div>
            <div class="card-body">
              <div class="chart-placeholder">
                <div class="text-center">
                  <i class="bi bi-bar-chart-fill text-muted" style="font-size: 48px;"></i>
                  <p class="text-muted mt-3">Chart Visualization Placeholder</p>
                  <small class="text-muted">Bar chart showing report distribution by violation type</small>
                </div>
              </div>
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
    </div>
  </div>

  <!-- View Report Modal -->
  <div class="modal fade" id="viewReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title fw-bold">Report Details</h5>
            <small class="text-muted">Submitted on Oct 12, 2025</small>
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
                  <p class="fw-semibold mb-0">Illegal Dumping</p>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Status</h6>
                  <span class="badge bg-warning text-dark">Pending</span>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Submitted By</h6>
                  <p class="mb-0">Maria Santos</p>
                </div>
                <div class="col-md-6 mb-3">
                  <h6 class="text-muted text-uppercase small mb-1">Date Submitted</h6>
                  <p class="mb-0">Oct 12, 2025</p>
                </div>
              </div>
            </div>

            <!-- Location -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-geo-alt-fill me-1"></i>Location
              </h6>
              <p class="mb-2">Tagum City, Davao del Norte</p>
              <!-- Map Placeholder -->
              <div id="viewMap" class="mb-0"></div>
              <small class="text-muted">Interactive map showing report location</small>
            </div>

            <!-- Photo Evidence -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-camera-fill me-1"></i>Photo Evidence
              </h6>
              <img src="https://via.placeholder.com/400x300?text=Construction+Waste" alt="Report Evidence" class="report-image-preview shadow-sm">
            </div>

            <!-- Description -->
            <div class="col-12">
              <h6 class="text-muted text-uppercase small mb-2">
                <i class="bi bi-file-text-fill me-1"></i>Description
              </h6>
              <p class="text-muted">Large pile of construction debris dumped near residential area.</p>
            </div>

            <!-- Admin Remarks -->
            <div class="col-12">
              <div class="alert alert-light border mb-0">
                <h6 class="text-muted text-uppercase small mb-2">
                  <i class="bi bi-chat-square-text-fill me-1"></i>Admin Remarks
                </h6>
                <p class="mb-0">No remarks yet.</p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-success">
            <i class="bi bi-download me-1"></i>Download Report
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Update Status Modal -->
  <div class="modal fade" id="updateStatusModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <div>
            <h5 class="modal-title fw-bold">Update Report Status</h5>
            <small class="text-muted">Report #ECO-2025-042</small>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <form id="updateStatusForm">
            <div class="mb-3">
              <label for="newStatus" class="form-label fw-semibold">New Status</label>
              <select class="form-select" id="newStatus" required>
                <option value="">Select new status...</option>
                <option value="pending">Pending</option>
                <option value="in-review">In Review</option>
                <option value="resolved">Resolved</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="adminRemarks" class="form-label fw-semibold">Remarks / Action Taken</label>
              <textarea class="form-control" id="adminRemarks" rows="4" placeholder="Enter remarks or details about actions taken..."></textarea>
              <small class="text-muted">Provide details about the investigation, actions taken, or resolution.</small>
            </div>
          </form>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success">
            <i class="bi bi-check-circle me-1"></i>Save Changes
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">
      <h5 class="fw-bold mb-3">EcoWatch Admin Panel</h5>
      <p class="text-white-50 small mb-3">Protecting our environment through community reporting.</p>
      <p class="text-white-50 small mb-0">&copy; 2025 EcoWatch. All rights reserved.</p>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  
  <script>
    let viewMap;

    // Initialize map when view modal is shown
    document.getElementById('viewReportModal').addEventListener('shown.bs.modal', function () {
      if (!viewMap) {
        viewMap = L.map('viewMap').setView([7.4474, 125.8077], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(viewMap);
        L.marker([7.4474, 125.8077]).addTo(viewMap)
          .bindPopup('<b>Illegal Dumping</b><br>Tagum City, Davao del Norte')
          .openPopup();
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

    // Event listeners for filters
    document.getElementById('searchInput').addEventListener('keyup', filterTable);
    document.getElementById('statusFilter').addEventListener('change', filterTable);
    document.getElementById('typeFilter').addEventListener('change', filterTable);
  </script>
</body>
</html>