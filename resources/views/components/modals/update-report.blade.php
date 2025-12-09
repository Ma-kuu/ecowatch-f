@props(['modalId' => 'updateStatusModal'])

<div class="modal fade" id="{{ $modalId }}" tabindex="-1">
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