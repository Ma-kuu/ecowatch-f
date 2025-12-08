// Simple reusable map lightbox helper for Leaflet maps
// Depends on map-helper.js (createMap, updateMapView, refreshMap, addMarker, removeMarker)

(function(window) {
  function initMapLightbox(options) {
    const {
      enlargeButtonId,
      overlayId,
      containerId,
      enlargedMapId,
      sourceMap,
      getSourcePosition,
    } = options;

    const enlargeBtn = document.getElementById(enlargeButtonId);
    const overlay = document.getElementById(overlayId);
    const container = document.getElementById(containerId);
    const closeBtn = document.getElementById('closeLightbox');

    // sourceMap is optional: dashboards pass an existing map, feed passes null
    if (!enlargeBtn || !overlay || !container || !getSourcePosition) {
      return;
    }

    let enlargedMap = null;
    let enlargedMarker = null;

    function openLightbox() {
      const pos = getSourcePosition();

      overlay.classList.add('active');
      setTimeout(() => container.classList.add('active'), 10);

      setTimeout(() => {
        const lat = pos.lat;
        const lng = pos.lng;
        const zoom = pos.zoom;

        if (!enlargedMap) {
          enlargedMap = createMap(enlargedMapId, lat, lng, zoom);
        } else {
          updateMapView(enlargedMap, lat, lng, zoom);
          refreshMap(enlargedMap);
        }

        if (pos.hasMarker && pos.markerLat && pos.markerLng) {
          if (enlargedMarker) {
            removeMarker(enlargedMap, enlargedMarker);
          }
          enlargedMarker = addMarker(enlargedMap, pos.markerLat, pos.markerLng, pos.label || 'Location');
          if (enlargedMarker) {
            enlargedMarker.openPopup();
          }
        }
      }, 300);
    }

    function closeLightbox() {
      container.classList.remove('active');
      setTimeout(() => overlay.classList.remove('active'), 300);
    }

    enlargeBtn.addEventListener('click', openLightbox);
    overlay.addEventListener('click', closeLightbox);
    if (closeBtn) {
      closeBtn.addEventListener('click', closeLightbox);
    }
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        closeLightbox();
      }
    });

    return {
      open: openLightbox,
      close: closeLightbox,
      getEnlargedMap: () => enlargedMap,
    };
  }

  window.initMapLightbox = initMapLightbox;
})(window);
