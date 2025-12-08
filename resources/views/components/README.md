# Reusable Map Components

This directory contains reusable Blade components for the EcoWatch application.

## Map Lightbox Component

The `map-lightbox.blade.php` component provides a reusable lightbox/modal for displaying enlarged maps.

### Files

- **Blade Component**: `resources/views/components/map-lightbox.blade.php`
- **CSS Styles**: `public/css/map-lightbox.css`
- **JavaScript Module**: `public/js/map-lightbox.js`

### Usage

#### 1. Include the Component in Your Blade View

```blade
@extends('layouts.dashboard')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/map-lightbox.css') }}" />
@endpush

@section('content')
  <!-- Your content with a map -->
  <div style="position: relative;">
    <button class="map-enlarge-btn" id="enlargeMapBtn" title="Enlarge map">
      <i class="bi bi-arrows-fullscreen"></i>
    </button>
    <div id="viewMap"></div>
  </div>

  <!-- Include the map lightbox component -->
  <x-map-lightbox />
@endsection

@push('scripts')
<script src="{{ asset('js/map-helper.js') }}"></script>
<script src="{{ asset('js/map-lightbox.js') }}"></script>

<script>
  let myMap = null;
  let mapLightbox = null;

  // Initialize map lightbox
  document.addEventListener('DOMContentLoaded', function() {
    mapLightbox = initMapLightbox({
      enlargeButtonId: 'enlargeMapBtn',
      overlayId: 'mapLightboxOverlay',
      containerId: 'mapLightboxContainer',
      enlargedMapId: 'enlargedMap',
      sourceMap: myMap,
      getSourcePosition: function() {
        // Return the position data for the enlarged map
        const mapData = getMapData('viewMap');
        return {
          lat: mapData?.lat || 7.5,
          lng: mapData?.lng || 125.8,
          zoom: mapData?.lat ? 15 : 13,
          hasMarker: !!(mapData && mapData.lat && mapData.lng),
          markerLat: mapData?.lat,
          markerLng: mapData?.lng,
          label: mapData?.label || 'Location'
        };
      }
    });
  });
</script>
@endpush
```

#### 2. Configuration Options

The `initMapLightbox()` function accepts the following options:

- **enlargeButtonId** (string): ID of the button that triggers the lightbox
- **overlayId** (string): ID of the overlay element (default: 'mapLightboxOverlay')
- **containerId** (string): ID of the container element (default: 'mapLightboxContainer')
- **enlargedMapId** (string): ID of the enlarged map div (default: 'enlargedMap')
- **sourceMap** (object): Reference to the source Leaflet map object
- **getSourcePosition** (function): Function that returns position data with the following structure:
  ```javascript
  {
    lat: number,           // Latitude
    lng: number,           // Longitude
    zoom: number,          // Zoom level
    hasMarker: boolean,    // Whether to show a marker
    markerLat: number,     // Marker latitude (if hasMarker is true)
    markerLng: number,     // Marker longitude (if hasMarker is true)
    label: string          // Marker popup label
  }
  ```

#### 3. API Methods

The `initMapLightbox()` function returns an object with the following methods:

- **open()**: Programmatically open the lightbox
- **close()**: Programmatically close the lightbox
- **getEnlargedMap()**: Get reference to the enlarged Leaflet map object

Example:
```javascript
// Open lightbox programmatically
mapLightbox.open();

// Close lightbox programmatically
mapLightbox.close();

// Get the enlarged map to add custom controls
const enlargedMap = mapLightbox.getEnlargedMap();
if (enlargedMap) {
  // Add routing or other controls
  L.Routing.control({ ... }).addTo(enlargedMap);
}
```

### Features

- **Smooth animations**: Fade-in/fade-out transitions
- **Keyboard support**: Press ESC to close
- **Click outside to close**: Click on overlay to dismiss
- **Responsive**: Adapts to different screen sizes
- **Reusable**: Can be used in multiple dashboards

### Dependencies

- Leaflet.js (for maps)
- Bootstrap Icons (for icons)
- `map-helper.js` (for map utilities like `createMap`, `updateMapView`, etc.)

### Example Dashboards Using This Component

- **LGU Dashboard**: `resources/views/auth/lgu-dashboard.blade.php`

### Customization

You can customize the appearance by modifying `public/css/map-lightbox.css`:

- Change lightbox size by adjusting `.map-lightbox-container` width/height
- Modify overlay opacity in `.map-lightbox-overlay`
- Customize button styles in `.map-enlarge-btn`
- Adjust animation timing in transition properties
