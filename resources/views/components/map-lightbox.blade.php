{{-- 
  Map Lightbox Component
  Reusable component for displaying an enlarged map in a lightbox overlay
  
  Usage:
  <x-map-lightbox />
--}}

<!-- Map Lightbox Overlay -->
<div class="map-lightbox-overlay" id="mapLightboxOverlay"></div>

<!-- Map Lightbox Container -->
<div class="map-lightbox-container" id="mapLightboxContainer">
  <button class="map-lightbox-close" id="closeLightbox" title="Close">
    <i class="bi bi-x"></i>
  </button>
  <div id="enlargedMap"></div>
</div>
