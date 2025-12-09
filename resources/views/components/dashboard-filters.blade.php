@props([
    'action',
    'showViolationType' => true,
    'showStatus' => true,
    'showLgu' => false,
    'showBarangay' => false,
    'showPriority' => false,
    'showDateRange' => false,
    'showFlagged' => false,
    'showReporterType' => false,
    'lgus' => [],
    'barangays' => []
])

<!-- Filter Toggle Button -->
<div class="d-flex align-items-center gap-2 mb-3">
  <button type="button" class="btn btn-primary" id="filterToggleBtn" aria-expanded="{{ request()->hasAny(['violation_type', 'status', 'lgu', 'barangay', 'priority', 'date_range', 'flagged', 'reporter_type']) ? 'true' : 'false' }}" aria-controls="advancedFilters">
    <i class="bi bi-funnel-fill me-2"></i>Filters
    @if(request()->hasAny(['violation_type', 'status', 'lgu', 'barangay', 'priority', 'date_range', 'flagged', 'reporter_type']))
      <span class="badge bg-light text-primary ms-2">{{ collect(request()->only(['violation_type', 'status', 'lgu', 'barangay', 'priority', 'date_range', 'flagged', 'reporter_type']))->filter()->count() }}</span>
    @endif
  </button>
  @if(request()->hasAny(['violation_type', 'status', 'lgu', 'barangay', 'priority', 'date_range', 'flagged', 'reporter_type', 'search']))
    <a href="{{ $action }}" class="btn btn-outline-danger">
      <i class="bi bi-x-circle me-1"></i>Clear All Filters
    </a>
  @endif
</div>

<form method="GET" action="{{ $action }}" id="filterForm" class="mb-4">
  <!-- Collapsible Advanced Filters (Closed by Default) -->
  <div class="collapse {{ request()->hasAny(['violation_type', 'status', 'lgu', 'barangay', 'priority', 'date_range', 'flagged', 'reporter_type']) ? 'show' : '' }}" id="advancedFilters">
    <div class="card mb-3">
      <div class="card-body">
        <div class="row g-3">
    
    @if($showViolationType)
    <!-- Violation Type Filter -->
    <div class="col-md-6 col-lg-4">
      <label class="form-label small text-muted fw-semibold">Violation Type</label>
      <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto;">
        @foreach(\App\Models\ViolationType::orderBy('name')->get() as $type)
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="violation_type[]" value="{{ $type->id }}" 
                   id="vtype{{ $type->id }}" 
                   {{ in_array($type->id, (array)request('violation_type', [])) ? 'checked' : '' }}
                   onchange="document.getElementById('filterForm').submit()">
            <label class="form-check-label small" for="vtype{{ $type->id }}">
              {{ $type->name }}
            </label>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($showStatus)
    <!-- Status Filter -->
    <div class="col-md-6 col-lg-4">
      <label class="form-label small text-muted fw-semibold">Status</label>
      <div class="border rounded p-2">
        @php
          $statuses = [
            'pending' => 'Pending',
            'in-review' => 'In Review',
            'in-progress' => 'In Progress',
            'awaiting-confirmation' => 'Awaiting Confirmation',
            'resolved' => 'Resolved'
          ];
        @endphp
        @foreach($statuses as $value => $label)
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="status[]" value="{{ $value }}" 
                   id="status{{ $value }}" 
                   {{ in_array($value, (array)request('status', [])) ? 'checked' : '' }}
                   onchange="document.getElementById('filterForm').submit()">
            <label class="form-check-label small" for="status{{ $value }}">
              {{ $label }}
            </label>
          </div>
        @endforeach
      </div>
    </div>
    @endif

    @if($showLgu)
    <!-- LGU Filter -->
    <div class="col-md-6 col-lg-3">
      <label class="form-label small text-muted">Assigned LGU</label>
      <select class="form-select" name="lgu" onchange="document.getElementById('filterForm').submit()">
        <option value="">All LGUs</option>
        @foreach($lgus as $lgu)
          <option value="{{ $lgu->id }}" {{ request('lgu') == $lgu->id ? 'selected' : '' }}>
            {{ $lgu->name }}
          </option>
        @endforeach
      </select>
    </div>
    @endif

    @if($showBarangay)
    <!-- Barangay Filter -->
    <div class="col-md-6 col-lg-3">
      <label class="form-label small text-muted">Barangay</label>
      <select class="form-select" name="barangay" onchange="document.getElementById('filterForm').submit()">
        <option value="">All Barangays</option>
        @foreach($barangays as $barangay)
          <option value="{{ $barangay->id }}" {{ request('barangay') == $barangay->id ? 'selected' : '' }}>
            {{ $barangay->name }}
          </option>
        @endforeach
      </select>
    </div>
    @endif

    @if($showPriority)
    <!-- Priority Filter -->
    <div class="col-md-6 col-lg-3">
      <label class="form-label small text-muted">Priority</label>
      <select class="form-select" name="priority" onchange="document.getElementById('filterForm').submit()">
        <option value="">All Priorities</option>
        <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
        <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
        <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
        <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>Urgent</option>
      </select>
    </div>
    @endif

    @if($showDateRange)
    <!-- Date Range Filter -->
    <div class="col-md-6 col-lg-3">
      <label class="form-label small text-muted">Date Range</label>
      <select class="form-select" name="date_range" onchange="document.getElementById('filterForm').submit()">
        <option value="">All Time</option>
        <option value="today" {{ request('date_range') == 'today' ? 'selected' : '' }}>Today</option>
        <option value="week" {{ request('date_range') == 'week' ? 'selected' : '' }}>This Week</option>
        <option value="month" {{ request('date_range') == 'month' ? 'selected' : '' }}>This Month</option>
        <option value="year" {{ request('date_range') == 'year' ? 'selected' : '' }}>This Year</option>
      </select>
    </div>
    @endif

    @if($showFlagged)
    <!-- Flagged Reports Filter -->
    <div class="col-md-6 col-lg-3">
      <label class="form-label small text-muted">Flagged</label>
      <select class="form-select" name="flagged" onchange="document.getElementById('filterForm').submit()">
        <option value="">All Reports</option>
        <option value="yes" {{ request('flagged') == 'yes' ? 'selected' : '' }}>Flagged Only</option>
      </select>
    </div>
    @endif

    @if($showReporterType)
    <!-- Reporter Type Filter -->
    <div class="col-md-6 col-lg-3">
      <label class="form-label small text-muted">Reporter Type</label>
      <select class="form-select" name="reporter_type" onchange="document.getElementById('filterForm').submit()">
        <option value="">All Reporters</option>
        <option value="registered" {{ request('reporter_type') == 'registered' ? 'selected' : '' }}>Registered</option>
        <option value="anonymous" {{ request('reporter_type') == 'anonymous' ? 'selected' : '' }}>Anonymous</option>
      </select>
    </div>
    @endif
        </div>
      </div>
    </div>
  </div>

  <!-- Search Bar (Always Visible) -->
  <div class="row g-3 mb-3">
    <div class="col-12">
      <div class="input-group">
        <span class="input-group-text bg-light border-end-0">
          <i class="bi bi-search text-muted"></i>
        </span>
        <input type="text" class="form-control border-start-0" placeholder="Search by Report ID, description, location..." name="search" value="{{ request('search') }}">
        <button type="submit" class="btn btn-primary">
          <i class="bi bi-search me-1"></i>Search
        </button>
      </div>
    </div>
  </div>

  <!-- Active Filters Display -->
  @if(request()->hasAny(['violation_type', 'status', 'lgu', 'barangay', 'priority', 'date_range', 'flagged', 'reporter_type', 'search']))
  <div class="mt-3">
    <small class="text-muted me-2">Active filters:</small>
    @if(request('violation_type'))
      <span class="badge bg-secondary me-1">
        Type: 
        @if(is_array(request('violation_type')))
          {{ collect(request('violation_type'))->map(fn($id) => \App\Models\ViolationType::find($id)?->name ?? $id)->join(', ') }}
        @else
          {{ \App\Models\ViolationType::find(request('violation_type'))->name ?? request('violation_type') }}
        @endif
      </span>
    @endif
    @if(request('status'))
      <span class="badge bg-secondary me-1">
        Status: 
        @if(is_array(request('status')))
          {{ collect(request('status'))->map(fn($s) => ucwords(str_replace('-', ' ', $s)))->join(', ') }}
        @else
          {{ ucwords(str_replace('-', ' ', request('status'))) }}
        @endif
      </span>
    @endif
    @if(request('lgu'))
      <span class="badge bg-secondary me-1">LGU: {{ \App\Models\Lgu::find(request('lgu'))->name ?? request('lgu') }}</span>
    @endif
    @if(request('barangay'))
      <span class="badge bg-secondary me-1">Barangay: {{ \App\Models\Barangay::find(request('barangay'))->name ?? request('barangay') }}</span>
    @endif
    @if(request('priority'))
      <span class="badge bg-secondary me-1">Priority: {{ ucfirst(request('priority')) }}</span>
    @endif
    @if(request('date_range'))
      <span class="badge bg-secondary me-1">Date: {{ ucfirst(request('date_range')) }}</span>
    @endif
    @if(request('flagged'))
      <span class="badge bg-warning text-dark me-1">Flagged Only</span>
    @endif
    @if(request('reporter_type'))
      <span class="badge bg-secondary me-1">Reporter: {{ ucfirst(request('reporter_type')) }}</span>
    @endif
    @if(request('search'))
      <span class="badge bg-info me-1">Search: "{{ request('search') }}"</span>
    @endif
  </div>
  @endif
</form>

<script>
  // Manual toggle for filter collapse
  document.addEventListener('DOMContentLoaded', function() {
    const filterBtn = document.getElementById('filterToggleBtn');
    const advancedFilters = document.getElementById('advancedFilters');
    
    if (filterBtn && advancedFilters) {
      filterBtn.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Toggle the 'show' class
        if (advancedFilters.classList.contains('show')) {
          advancedFilters.classList.remove('show');
          filterBtn.setAttribute('aria-expanded', 'false');
        } else {
          advancedFilters.classList.add('show');
          filterBtn.setAttribute('aria-expanded', 'true');
        }
      });
    }
  });
</script>
