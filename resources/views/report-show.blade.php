@extends('layouts.app')

@section('title', 'Submit Report | EcoWatch')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<style>
  body {
    background: #f4f6f8;
  }
  .form-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    padding: 32px 24px;
    max-width: 600px;
    margin: 48px auto;
  }
  #map {
    height: 250px;
    border-radius: 8px;
    margin-bottom: 1rem;
  }
  @media (max-width: 767px) {
    .form-card { padding: 18px 6px; margin: 24px 0; }
    #map { height: 160px; }
  }
</style>
@endpush

@section('content')
  <!-- Report Form -->
  <main class="container" style="padding-top: 90px;">
    <div class="form-card">
      <h4 class="fw-bold mb-3 text-success">📋 Submit an Environmental Concern</h4>
      <form id="reportForm" enctype="multipart/form-data">
        @csrf
        <div class="mb-3">
          <label class="form-label">Type of Violation</label>
          <select name="violation_type" class="form-select" required>
            <option selected disabled value="">Choose type...</option>
            @foreach($violationTypes as $type)
              <option value="{{ $type->slug }}">{{ $type->name }}</option>
            @endforeach
          </select>
          <small class="text-muted">Select the type of environmental violation you want to report.</small>
        </div>
        <div class="mb-3">
          <label class="form-label">Description</label>
          <textarea name="description" class="form-control" rows="4" placeholder="Describe what you observed..." required></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Upload Photo (Optional)</label>
          <div class="upload-box" id="uploadBox">
            <p id="uploadText">📸 Click or drag an image here to upload</p>
            <input type="file" name="photo" accept="image/*" class="form-control mt-2" style="display:none;" id="photoInput">
          </div>
          <small class="text-muted d-block mt-2" id="fileName"></small>
        </div>
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <div class="mb-3">
          <label class="form-label">Pin or Search Location</label>
          <div id="map"></div>
          <button type="button" id="getLocationBtn" class="btn btn-outline-primary btn-sm mt-2 mb-2 w-100">
            <i class="bi bi-crosshair me-2"></i>Get My Current Location
          </button>
          <small class="text-muted d-block">Click the button above to use your current location, or click/search on the map to mark the location of the incident.</small>
        </div>
        <div class="d-flex justify-content-end">
          <button type="submit" class="btn btn-submit px-4">Submit Report</button>
        </div>
      </form>
    </div>
  </main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script>
  // Leaflet Map Setup
  const map = L.map('map').setView([7.1907, 125.4553], 13);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

  let marker;
  
  // Get My Location Button
  const getLocationBtn = document.getElementById('getLocationBtn');
  getLocationBtn.addEventListener('click', function() {
    if (!navigator.geolocation) {
      alert('Geolocation is not supported by your browser.');
      return;
    }

    // Show loading state
    getLocationBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Getting location...';
    getLocationBtn.disabled = true;

    navigator.geolocation.getCurrentPosition(
      function(position) {
        const lat = position.coords.latitude;
        const lng = position.coords.longitude;
        
        // Center map on user's location
        map.setView([lat, lng], 16);
        
        // Remove old marker and add new one
        if (marker) map.removeLayer(marker);
        marker = L.marker([lat, lng]).addTo(map)
          .bindPopup('📍 Your Current Location')
          .openPopup();

        // Store coordinates in hidden fields
        document.getElementById('latitude').value = lat;
        document.getElementById('longitude').value = lng;

        // Reset button
        getLocationBtn.innerHTML = '<i class="bi bi-check-circle me-2"></i>Location Found!';
        getLocationBtn.classList.remove('btn-outline-primary');
        getLocationBtn.classList.add('btn-success');
        
        setTimeout(() => {
          getLocationBtn.innerHTML = '<i class="bi bi-crosshair me-2"></i>Get My Current Location';
          getLocationBtn.classList.remove('btn-success');
          getLocationBtn.classList.add('btn-outline-primary');
          getLocationBtn.disabled = false;
        }, 2000);
      },
      function(error) {
        alert('Unable to retrieve your location: ' + error.message);
        getLocationBtn.innerHTML = '<i class="bi bi-crosshair me-2"></i>Get My Current Location';
        getLocationBtn.disabled = false;
      }
    );
  });

  // Allow user to click on map to set location
  map.on('click', function(e) {
    if (marker) map.removeLayer(marker);
    marker = L.marker(e.latlng).addTo(map)
      .bindPopup(`📍 Location: ${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`).openPopup();
  });

  // Add Search Bar
  L.Control.geocoder({
    defaultMarkGeocode: false
  })
  .on('markgeocode', function(e) {
    const latlng = e.geocode.center;
    map.setView(latlng, 15);
    if (marker) map.removeLayer(marker);
    marker = L.marker(latlng).addTo(map)
      .bindPopup(`📍 ${e.geocode.name}`).openPopup();
  })
  .addTo(map);

  // Upload box trigger
  const uploadBox = document.getElementById('uploadBox');
  const photoInput = document.getElementById('photoInput');
  uploadBox.addEventListener('click', () => photoInput.click());

  // Show selected file name
  photoInput.addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
      document.getElementById('uploadText').innerHTML = '✅ Image selected';
      document.getElementById('fileName').innerHTML = '<strong>Selected:</strong> ' + fileName;
    }
  });

  // Store marker coordinates
  map.on('click', function(e) {
    if (marker) map.removeLayer(marker);
    marker = L.marker(e.latlng).addTo(map)
      .bindPopup(`📍 Location: ${e.latlng.lat.toFixed(4)}, ${e.latlng.lng.toFixed(4)}`).openPopup();
    
    // Store coordinates in hidden fields
    document.getElementById('latitude').value = e.latlng.lat;
    document.getElementById('longitude').value = e.latlng.lng;
  });

  // Form submission handler
  const reportForm = document.getElementById('reportForm');
  reportForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate marker is set
    if (!marker) {
      window.showNotification({
        title: 'Location Required',
        message: 'Please select a location on the map or use "Get My Location" button.',
        type: 'warning',
        duration: 4000
      });
      return;
    }

    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
    submitBtn.disabled = true;

    // Create FormData for file upload
    const formData = new FormData(this);

    // AJAX submission
    fetch('{{ route("report.store") }}', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      },
      body: formData
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Show success notification
        window.showNotification({
          title: 'Report Submitted Successfully!',
          message: data.message,
          type: 'success',
          duration: 6000
        });

        // Reset form
        reportForm.reset();
        if (marker) map.removeLayer(marker);
        marker = null;
        document.getElementById('uploadText').innerHTML = '📸 Click or drag an image here to upload';
        document.getElementById('fileName').innerHTML = '';
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        // Redirect to dashboard after delay
        setTimeout(() => {
          window.location.href = data.redirect;
        }, 2000);
      } else {
        throw new Error(data.message || 'Submission failed');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      window.showNotification({
        title: 'Submission Failed',
        message: error.message || 'Please try again.',
        type: 'error',
        duration: 5000
      });
      
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  });
</script>
@endpush
