@props(['modalId' => 'viewReportModal'])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
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
              <p class="mb-0" id="modalAdminRemarks">No remarks yet.</p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        @if(auth()->check() && auth()->user()->role === 'lgu')
          <button type="button" class="btn btn-success" id="btnShowDirections">
            <i class="bi bi-signpost-2-fill me-1"></i>Show Directions from LGU HQ
          </button>
        @endif
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>