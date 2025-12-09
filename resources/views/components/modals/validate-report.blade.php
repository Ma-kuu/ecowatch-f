@props(['modalId' => 'validateReportModal'])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
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
            <label for="validationNotes" class="form-label fw-semibold">Notes</label>
            <textarea class="form-control" name="notes" id="validationNotes" rows="4" placeholder="Add notes about your validation decision..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <i class="bi bi-check-circle me-1"></i>Submit Validation
          </button>
        </div>
      </form>
    </div>
  </div>
</div>