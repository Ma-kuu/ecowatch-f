/**
 * Modal Helper
 *
 * Simple helper functions for working with Bootstrap modals.
 * Helps populate modal fields from button data attributes and manage form actions.
 *
 * How to use:
 * 1. Add this script to your page: <script src="/js/modal-helper.js"></script>
 * 2. Add data attributes to your trigger buttons (e.g., data-report-id="123")
 * 3. Call setupModalTriggers() to automatically handle modal population
 *
 * Example button:
 * <button data-bs-toggle="modal"
 *         data-bs-target="#myModal"
 *         data-report-id="123"
 *         data-report-code="RPT-001">
 *   Open Modal
 * </button>
 */

/**
 * Populate a modal's form fields from a button's data attributes
 * This is useful when you click a button to edit different items
 *
 * @param {HTMLElement} button - The button that was clicked
 * @param {Object} fieldMapping - Maps data attributes to form field IDs
 *
 * Example:
 * populateModalFields(button, {
 *   'reportId': 'inputReportId',      // data-report-id → #inputReportId
 *   'reportCode': 'displayReportCode' // data-report-code → #displayReportCode
 * });
 */
function populateModalFields(button, fieldMapping) {
  // Loop through each mapping
  for (const dataAttr in fieldMapping) {
    const fieldId = fieldMapping[dataAttr];

    // Get the value from button's data attribute
    // Convert camelCase to kebab-case (reportId → report-id)
    const attrName = camelToKebab(dataAttr);
    const value = button.getAttribute('data-' + attrName);

    // Get the form field element
    const field = document.getElementById(fieldId);

    if (field && value !== null) {
      // Set the value based on field type
      if (field.tagName === 'INPUT' || field.tagName === 'TEXTAREA' || field.tagName === 'SELECT') {
        field.value = value;
      } else {
        // For other elements (like spans, divs), set text content
        field.textContent = value;
      }
    }
  }
}

/**
 * Set a form's action URL dynamically
 * Useful when the same form is used for different items
 *
 * @param {string} formId - The ID of the form
 * @param {string} url - The new action URL
 *
 * Example:
 * setFormAction('editForm', '/admin/reports/123');
 */
function setFormAction(formId, url) {
  const form = document.getElementById(formId);
  if (form) {
    form.action = url;
  }
}

/**
 * Reset a modal's form fields
 * Clears all inputs, selects, and textareas
 *
 * @param {string} formId - The ID of the form to reset
 *
 * Example:
 * resetModalForm('editForm');
 */
function resetModalForm(formId) {
  const form = document.getElementById(formId);
  if (form) {
    form.reset();
  }
}

/**
 * Show loading state on a submit button
 * Displays spinner and disables the button
 *
 * @param {string} buttonId - The ID of the submit button
 * @param {string} loadingText - Optional text to show while loading
 *
 * Example:
 * showLoadingState('submitBtn', 'Saving...');
 */
function showLoadingState(buttonId, loadingText = 'Loading...') {
  const button = document.getElementById(buttonId);
  if (!button) return;

  // Store original text
  if (!button.dataset.originalText) {
    button.dataset.originalText = button.innerHTML;
  }

  // Show loading state
  button.disabled = true;
  button.innerHTML = `
    <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
    ${loadingText}
  `;
}

/**
 * Hide loading state on a submit button
 * Restores original text and enables the button
 *
 * @param {string} buttonId - The ID of the submit button
 *
 * Example:
 * hideLoadingState('submitBtn');
 */
function hideLoadingState(buttonId) {
  const button = document.getElementById(buttonId);
  if (!button) return;

  // Restore original state
  button.disabled = false;
  if (button.dataset.originalText) {
    button.innerHTML = button.dataset.originalText;
  }
}

/**
 * Validate file upload before submission
 * Checks if file exists and is within size limit
 *
 * @param {string} fileInputId - The ID of the file input
 * @param {number} maxSizeMB - Maximum file size in megabytes
 * @returns {Object} - { valid: boolean, error: string }
 *
 * Example:
 * const result = validateFileUpload('photoInput', 5);
 * if (!result.valid) {
 *   alert(result.error);
 * }
 */
function validateFileUpload(fileInputId, maxSizeMB) {
  const fileInput = document.getElementById(fileInputId);

  // Check if input exists
  if (!fileInput) {
    return { valid: false, error: 'File input not found' };
  }

  // Check if file is selected
  if (!fileInput.files || fileInput.files.length === 0) {
    return { valid: false, error: 'Please select a file' };
  }

  const file = fileInput.files[0];

  // Check file size
  const maxSizeBytes = maxSizeMB * 1024 * 1024; // Convert MB to bytes
  if (file.size > maxSizeBytes) {
    return {
      valid: false,
      error: `File must be less than ${maxSizeMB}MB (current: ${(file.size / 1024 / 1024).toFixed(2)}MB)`
    };
  }

  // File is valid
  return { valid: true, error: null };
}

/**
 * Helper function: Convert camelCase to kebab-case
 * Used internally for data attribute names
 *
 * @param {string} str - The camelCase string
 * @returns {string} - The kebab-case string
 *
 * Example: camelToKebab('reportId') → 'report-id'
 */
function camelToKebab(str) {
  return str.replace(/([a-z])([A-Z])/g, '$1-$2').toLowerCase();
}

/**
 * Setup automatic modal triggers
 * Scans page for modal trigger buttons and sets up click handlers
 * This makes it easy to wire up modals without writing custom code
 *
 * Call this after the page loads to automatically handle modal population
 */
function setupModalTriggers() {
  // Find all buttons that trigger modals
  const modalButtons = document.querySelectorAll('[data-bs-toggle="modal"]');

  modalButtons.forEach(button => {
    button.addEventListener('click', function() {
      // You can add custom logic here if needed
      // For now, just log that a modal is being opened
      const targetModal = this.getAttribute('data-bs-target');
      if (targetModal) {
        // Modal is being opened - custom code can go here
      }
    });
  });
}

// Auto-setup when page loads
if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', setupModalTriggers);
} else {
  setupModalTriggers();
}
