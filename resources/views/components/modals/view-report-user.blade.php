@props(['modalId' => 'reportDetailsModal'])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
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

          <!-- Admin Remarks -->
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