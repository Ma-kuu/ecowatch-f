@props([
    'modalId' => 'successModal',
    'isAnonymous' => true
])

<!-- Success Modal -->
<div class="modal fade" id="{{ $modalId }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header bg-success text-white border-0">
        <h5 class="modal-title fw-bold">
          <i class="bi bi-check-circle-fill me-2"></i>Report Submitted Successfully!
        </h5>
      </div>
      <div class="modal-body p-4">
        @if($isAnonymous)
          <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle-fill me-2"></i>
            <strong>Important:</strong> Save your Report ID to check your report status later.
          </div>

          <!-- Report ID -->
          <div class="mb-4 text-center">
            <label class="form-label fw-semibold text-muted small">Your Report ID</label>
            <div class="input-group input-group-lg justify-content-center">
              <input type="text" class="form-control form-control-lg fw-bold text-center" id="modalReportId" readonly style="letter-spacing: 2px; font-size: 1.5rem; color: #198754; max-width: 200px;">
              <button class="btn btn-outline-secondary" type="button" onclick="copyToClipboard('modalReportId', 'Report ID')">
                <i class="bi bi-clipboard"></i>
              </button>
            </div>
            <small class="text-muted d-block mt-2">
              <i class="bi bi-info-circle me-1"></i>Save this Report ID to check your report status anytime
            </small>
          </div>

          <hr class="my-4">
        @else
          <div class="alert alert-success mb-4">
            <i class="bi bi-check-circle-fill me-2"></i>
            <strong>Success!</strong> Your report has been submitted and you can track it in your dashboard.
          </div>
        @endif

        <!-- Report Summary -->
        <h6 class="fw-bold mb-3"><i class="bi bi-file-text me-2"></i>Report Summary</h6>
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <small class="text-muted d-block mb-1">Violation Type</small>
            <p class="mb-0 fw-semibold" id="modalSummaryViolation">-</p>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block mb-1">Location</small>
            <p class="mb-0" id="modalSummaryLocation">-</p>
          </div>
          <div class="col-12">
            <small class="text-muted d-block mb-1">Description</small>
            <p class="mb-0 text-muted small" id="modalSummaryDescription" style="max-height: 60px; overflow-y: auto;">-</p>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block mb-1">Status</small>
            <span class="badge bg-warning text-dark">Pending Review</span>
          </div>
          <div class="col-md-6">
            <small class="text-muted d-block mb-1">Submitted</small>
            <p class="mb-0" id="modalSummaryDate">-</p>
          </div>
        </div>

        @if($isAnonymous)
          <div class="alert alert-warning mb-0">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <small><strong>Note:</strong> Your report is pending admin verification before it appears in the public feed.</small>
          </div>
        @else
          <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle-fill me-2"></i>
            <small>You can view and track your report in your dashboard.</small>
          </div>
        @endif
      </div>
      <div class="modal-footer border-0 bg-light d-flex justify-content-between">
        @if($isAnonymous)
          <a href="{{ route('report-status') }}" class="btn btn-outline-success">
            <i class="bi bi-search me-1"></i>Check Status Now
          </a>
          <a href="{{ route('index') }}" class="btn btn-success">
            <i class="bi bi-house-fill me-1"></i>Go to Home
          </a>
        @else
          <a href="{{ route('user-dashboard') }}" class="btn btn-success">
            <i class="bi bi-speedometer2 me-1"></i>View Your Dashboard
          </a>
          <a href="{{ route('index') }}" class="btn btn-outline-success">
            <i class="bi bi-house-fill me-1"></i>Go to Home
          </a>
        @endif
      </div>
    </div>
  </div>
</div>

@if($isAnonymous)
<script>
  // Copy to clipboard function
  function copyToClipboard(elementId, label) {
    const element = document.getElementById(elementId);
    element.select();
    element.setSelectionRange(0, 99999); // For mobile devices
    
    navigator.clipboard.writeText(element.value).then(() => {
      showToast(label + ' copied to clipboard!', 'success', 2000);
    }).catch(err => {
      console.error('Failed to copy:', err);
      showToast('Failed to copy. Please copy manually.', 'danger', 3000);
    });
  }
</script>
@endif
