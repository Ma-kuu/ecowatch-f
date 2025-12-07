<!-- Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 9999;">
  <div id="notification-toast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="toast-header">
      <i id="toast-icon" class="bi me-2"></i>
      <strong id="toast-title" class="me-auto">Notification</strong>
      <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
    <div class="toast-body" id="toast-message">
      Message content
    </div>
  </div>
</div>

<!-- Toast Notification Script -->
<script>
  /**
   * Global toast notification function
   * @param {Object} options - Notification options
   * @param {string} options.title - Toast title
   * @param {string} options.message - Toast message
   * @param {string} options.type - Toast type (success, error, warning, info)
   * @param {number} options.duration - Duration in milliseconds (default: 5000)
   */
  window.showNotification = function(options) {
    const {
      title = 'Notification',
      message = '',
      type = 'info',
      duration = 5000
    } = options;

    // Get toast elements
    const toastEl = document.getElementById('notification-toast');
    const toastHeader = toastEl.querySelector('.toast-header');
    const toastIcon = document.getElementById('toast-icon');
    const toastTitle = document.getElementById('toast-title');
    const toastMessage = document.getElementById('toast-message');

    // Reset classes
    toastHeader.className = 'toast-header';
    toastIcon.className = 'bi me-2';

    // Set icon and color based on type
    switch(type) {
      case 'success':
        toastHeader.classList.add('bg-success', 'text-white');
        toastIcon.classList.add('bi-check-circle-fill');
        break;
      case 'error':
        toastHeader.classList.add('bg-danger', 'text-white');
        toastIcon.classList.add('bi-exclamation-circle-fill');
        break;
      case 'warning':
        toastHeader.classList.add('bg-warning', 'text-dark');
        toastIcon.classList.add('bi-exclamation-triangle-fill');
        break;
      case 'info':
      default:
        toastHeader.classList.add('bg-info', 'text-white');
        toastIcon.classList.add('bi-info-circle-fill');
        break;
    }

    // Set content
    toastTitle.textContent = title;
    toastMessage.textContent = message;

    // Show toast
    const toast = new bootstrap.Toast(toastEl, {
      autohide: true,
      delay: duration
    });
    toast.show();
  };
</script>
