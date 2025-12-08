/**
 * Table Filter Helper
 *
 * Simple and reusable table filtering functionality for dashboards.
 * Works with search input, status filter, and type filter.
 *
 * How to use:
 * 1. Add this script to your page: <script src="/js/table-filter.js"></script>
 * 2. Call initTableFilter() when page loads
 *
 * Requirements:
 * - Table must have id="reportsTable"
 * - Search input must have id="searchInput"
 * - Status filter must have id="statusFilter"
 * - Type filter must have id="typeFilter" (optional)
 * - Table rows must have data-status attribute
 * - Table rows must have data-type attribute (if using type filter)
 */

/**
 * Initialize table filtering
 * Sets up event listeners for search and filter inputs
 */
function initTableFilter() {
  // Get the filter elements
  const searchInput = document.getElementById('searchInput');
  const statusFilter = document.getElementById('statusFilter');
  const typeFilter = document.getElementById('typeFilter');

  // Only set up filters if elements exist
  if (searchInput) {
    searchInput.addEventListener('keyup', filterTable);
  }

  if (statusFilter) {
    statusFilter.addEventListener('change', filterTable);
  }

  if (typeFilter) {
    typeFilter.addEventListener('change', filterTable);
  }
}

/**
 * Filter table rows based on search and filter values
 * This function is called whenever user types or changes filters
 */
function filterTable() {
  // Get current filter values
  const searchValue = getFilterValue('searchInput');
  const statusValue = getFilterValue('statusFilter');
  const typeValue = getFilterValue('typeFilter');

  // Get the table and all its rows
  const table = document.getElementById('reportsTable');
  if (!table) return; // Exit if table doesn't exist

  const tbody = table.getElementsByTagName('tbody')[0];
  const rows = tbody.getElementsByTagName('tr');

  // Loop through each row and check if it matches filters
  for (let i = 0; i < rows.length; i++) {
    const row = rows[i];

    // Check if row matches all filters
    const matchesSearch = rowMatchesSearch(row, searchValue);
    const matchesStatus = rowMatchesFilter(row, 'data-status', statusValue);
    const matchesType = rowMatchesFilter(row, 'data-type', typeValue);

    // Show row only if it matches ALL filters
    if (matchesSearch && matchesStatus && matchesType) {
      row.style.display = ''; // Show the row
    } else {
      row.style.display = 'none'; // Hide the row
    }
  }
}

/**
 * Get the value from a filter element
 * Returns empty string if element doesn't exist
 *
 * @param {string} elementId - The ID of the filter element
 * @returns {string} - The filter value (lowercase)
 */
function getFilterValue(elementId) {
  const element = document.getElementById(elementId);
  if (!element) return '';
  return element.value.toLowerCase();
}

/**
 * Check if a row matches the search query
 * Searches through all text in the row
 *
 * @param {HTMLElement} row - The table row to check
 * @param {string} searchValue - The search query (lowercase)
 * @returns {boolean} - True if row matches or search is empty
 */
function rowMatchesSearch(row, searchValue) {
  if (searchValue === '') return true; // No search = show all

  // Get all text in the row and convert to lowercase
  const rowText = row.textContent.toLowerCase();

  // Check if search value is found in row text
  return rowText.includes(searchValue);
}

/**
 * Check if a row matches a specific filter
 * Compares row's data attribute with filter value
 *
 * @param {HTMLElement} row - The table row to check
 * @param {string} attribute - The data attribute to check (e.g., 'data-status')
 * @param {string} filterValue - The filter value to match (lowercase)
 * @returns {boolean} - True if row matches or filter is empty
 */
function rowMatchesFilter(row, attribute, filterValue) {
  if (filterValue === '') return true; // No filter = show all

  // Get the attribute value from the row
  const attributeValue = row.getAttribute(attribute);
  if (!attributeValue) return true; // No attribute = show row

  // Compare attribute value with filter value
  return attributeValue.toLowerCase() === filterValue;
}

// Auto-initialize when page loads
// This runs automatically when the script is loaded
if (document.readyState === 'loading') {
  // If page is still loading, wait for it to finish
  document.addEventListener('DOMContentLoaded', initTableFilter);
} else {
  // If page is already loaded, initialize now
  initTableFilter();
}
