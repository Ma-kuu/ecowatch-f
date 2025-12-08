/**
 * Map Helper for Leaflet
 *
 * Simple helper functions for working with Leaflet maps.
 * Makes it easy to create, update, and manage maps in modals.
 *
 * How to use:
 * 1. Include Leaflet CSS and JS in your page
 * 2. Add this script: <script src="/js/map-helper.js"></script>
 * 3. Use the helper functions to create and manage maps
 *
 * Requirements:
 * - Leaflet library must be loaded first
 * - Map container div must exist in the DOM
 */

/**
 * Create and initialize a new Leaflet map
 * This is the main function to create a map
 *
 * @param {string} containerId - The ID of the div to put the map in
 * @param {number} lat - Latitude for center of map
 * @param {number} lng - Longitude for center of map
 * @param {number} zoom - Zoom level (higher = more zoomed in)
 * @returns {Object} - The Leaflet map object
 *
 * Example:
 * const myMap = createMap('mapDiv', 14.5995, 120.9842, 13);
 */
function createMap(containerId, lat, lng, zoom = 13) {
  // Check if Leaflet is loaded
  if (typeof L === 'undefined') {
    console.error('Leaflet library not loaded! Include leaflet.js before map-helper.js');
    return null;
  }

  // Get the container element
  const container = document.getElementById(containerId);
  if (!container) {
    console.error(`Container #${containerId} not found!`);
    return null;
  }

  // Create the map
  const map = L.map(containerId).setView([lat, lng], zoom);

  // Add the tile layer (the actual map images)
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
    maxZoom: 19
  }).addTo(map);

  return map;
}

/**
 * Add a marker to a map
 * Creates a pin at a specific location
 *
 * @param {Object} map - The Leaflet map object
 * @param {number} lat - Latitude for the marker
 * @param {number} lng - Longitude for the marker
 * @param {string} popupText - Text to show when marker is clicked (optional)
 * @returns {Object} - The marker object
 *
 * Example:
 * const marker = addMarker(myMap, 14.5995, 120.9842, 'Report Location');
 */
function addMarker(map, lat, lng, popupText = null) {
  if (!map) return null;

  // Create the marker
  const marker = L.marker([lat, lng]).addTo(map);

  // Add popup if text provided
  if (popupText) {
    marker.bindPopup(popupText);
  }

  return marker;
}

/**
 * Update map view to a new location
 * Moves the map to show a different area
 *
 * @param {Object} map - The Leaflet map object
 * @param {number} lat - New latitude
 * @param {number} lng - New longitude
 * @param {number} zoom - New zoom level (optional)
 *
 * Example:
 * updateMapView(myMap, 14.5995, 120.9842, 15);
 */
function updateMapView(map, lat, lng, zoom = null) {
  if (!map) return;

  if (zoom !== null) {
    map.setView([lat, lng], zoom);
  } else {
    map.setView([lat, lng]);
  }
}

/**
 * Fix map display issues after showing in modal
 * Call this when map becomes visible (e.g., after modal opens)
 *
 * @param {Object} map - The Leaflet map object
 *
 * Example:
 * // When modal opens:
 * modal.addEventListener('shown.bs.modal', function() {
 *   refreshMap(myMap);
 * });
 */
function refreshMap(map) {
  if (!map) return;

  // This fixes display issues when map is shown in a hidden element
  setTimeout(() => {
    map.invalidateSize();
  }, 100);
}

/**
 * Remove a marker from the map
 * Deletes a marker that was previously added
 *
 * @param {Object} map - The Leaflet map object
 * @param {Object} marker - The marker to remove
 *
 * Example:
 * removeMarker(myMap, myMarker);
 */
function removeMarker(map, marker) {
  if (!map || !marker) return;

  map.removeLayer(marker);
}

/**
 * Update a marker's position
 * Moves an existing marker to a new location
 *
 * @param {Object} marker - The marker to update
 * @param {number} lat - New latitude
 * @param {number} lng - New longitude
 *
 * Example:
 * updateMarkerPosition(myMarker, 14.5995, 120.9842);
 */
function updateMarkerPosition(marker, lat, lng) {
  if (!marker) return;

  marker.setLatLng([lat, lng]);
}

/**
 * Fit map to show all markers
 * Automatically adjusts zoom to show all points
 *
 * @param {Object} map - The Leaflet map object
 * @param {Array} points - Array of [lat, lng] coordinates
 * @param {Object} options - Padding options (optional)
 *
 * Example:
 * const points = [[14.5995, 120.9842], [14.5547, 121.0244]];
 * fitMapToPoints(myMap, points, { padding: [50, 50] });
 */
function fitMapToPoints(map, points, options = {}) {
  if (!map || !points || points.length === 0) return;

  // Create bounds from points
  const bounds = L.latLngBounds(points);

  // Fit map to bounds
  const defaultOptions = { padding: [20, 20] };
  map.fitBounds(bounds, { ...defaultOptions, ...options });
}

/**
 * Add a simple route line between two points
 * Draws a line from point A to point B
 *
 * @param {Object} map - The Leaflet map object
 * @param {Array} startPoint - [lat, lng] for start
 * @param {Array} endPoint - [lat, lng] for end
 * @param {Object} options - Line style options (optional)
 * @returns {Object} - The polyline object
 *
 * Example:
 * const route = addRouteLine(myMap,
 *   [14.5995, 120.9842],
 *   [14.5547, 121.0244],
 *   { color: 'blue', weight: 4 }
 * );
 */
function addRouteLine(map, startPoint, endPoint, options = {}) {
  if (!map) return null;

  // Default line style
  const defaultOptions = {
    color: '#198754',  // Green color
    weight: 4,         // Line thickness
    opacity: 0.7       // Transparency
  };

  // Merge default and custom options
  const lineOptions = { ...defaultOptions, ...options };

  // Create the line
  const polyline = L.polyline([startPoint, endPoint], lineOptions).addTo(map);

  return polyline;
}

/**
 * Remove a route line from the map
 * Deletes a polyline that was previously added
 *
 * @param {Object} map - The Leaflet map object
 * @param {Object} polyline - The polyline to remove
 *
 * Example:
 * removeRouteLine(myMap, myRoute);
 */
function removeRouteLine(map, polyline) {
  if (!map || !polyline) return;

  map.removeLayer(polyline);
}

/**
 * Initialize a map in a modal
 * Handles the common case of showing a map in a Bootstrap modal
 *
 * @param {string} modalId - ID of the modal
 * @param {string} mapContainerId - ID of the map container div
 * @param {Function} onModalShow - Function to call when modal opens
 * @returns {Object} - Object with map and modal references
 *
 * Example:
 * const mapData = initModalMap('viewModal', 'mapDiv', function(map) {
 *   // This runs when modal opens
 *   addMarker(map, 14.5995, 120.9842, 'Report Location');
 * });
 */
function initModalMap(modalId, mapContainerId, onModalShow) {
  const modalElement = document.getElementById(modalId);
  if (!modalElement) {
    console.error(`Modal #${modalId} not found!`);
    return null;
  }

  let map = null;

  // Create map when modal is first shown
  modalElement.addEventListener('shown.bs.modal', function() {
    if (!map) {
      // Create map with default Philippines center
      map = createMap(mapContainerId, 12.8797, 121.7740, 6);
    } else {
      // Refresh existing map
      refreshMap(map);
    }

    // Call custom function if provided
    if (onModalShow && typeof onModalShow === 'function') {
      onModalShow(map);
    }
  });

  return {
    modal: modalElement,
    getMap: () => map
  };
}

/**
 * Store map coordinates in a container element
 * Useful for passing data between modal trigger and map initialization
 *
 * @param {string} containerId - ID of the container element
 * @param {number} lat - Latitude to store
 * @param {number} lng - Longitude to store
 * @param {string} label - Label/name to store (optional)
 *
 * Example:
 * storeMapData('mapDiv', 14.5995, 120.9842, 'RPT-001');
 */
function storeMapData(containerId, lat, lng, label = '') {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.dataset.lat = lat;
  container.dataset.lng = lng;
  if (label) {
    container.dataset.label = label;
  }
}

/**
 * Get stored map coordinates from a container element
 * Retrieves data that was previously stored
 *
 * @param {string} containerId - ID of the container element
 * @returns {Object} - { lat, lng, label } or null if not found
 *
 * Example:
 * const data = getMapData('mapDiv');
 * if (data) {
 *   addMarker(myMap, data.lat, data.lng, data.label);
 * }
 */
function getMapData(containerId) {
  const container = document.getElementById(containerId);
  if (!container) return null;

  const lat = parseFloat(container.dataset.lat);
  const lng = parseFloat(container.dataset.lng);
  const label = container.dataset.label || '';

  if (isNaN(lat) || isNaN(lng)) return null;

  return { lat, lng, label };
}
