@extends('layouts.app')

@section('title', 'Report Anonymously | EcoWatch')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css" />
<link rel="stylesheet" href="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.css" />
<style>
  body {
    background: #f4f6f8;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
  }
  main {
    flex: 1 0 auto;
  }
  footer {
    flex-shrink: 0;
  }
  .form-control::placeholder,
  .form-select::placeholder {
    font-size: 14px;
  }
  #map {
    height: 400px;
    border-radius: 8px;
  }
  @media (max-width: 991px) {
    #map {
      height: 300px;
    }
  }
</style>
@endpush

@section('content')
  <main class="container py-5" style="padding-top: 100px !important;">
    <div class="card border-0 shadow-sm">
      <div class="card-body p-4">
        <h4 class="card-title mb-2" style="font-weight: 600; color: #212529;">Submit Anonymous Report</h4>
        <p class="text-muted mb-4" style="font-size: 14px;">Report environmental violations in Davao del Norte anonymously</p>

        <form id="reportForm" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="latitude" id="latitude">
          <input type="hidden" name="longitude" id="longitude">

          <div class="row g-4">
            <!-- Left Column - Form Fields (appears first on mobile) -->
            <div class="col-lg-6">
              <div class="mb-3">
                <label class="form-label fw-medium" style="font-size: 14px;">Type of Violation</label>
                <select name="violation_type" class="form-select" required>
                  <option selected disabled value="">Select violation type</option>
                  @foreach($violationTypes as $type)
                    <option value="{{ $type->slug }}">{{ $type->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="mb-3">
                <label class="form-label fw-medium" style="font-size: 14px;">Description</label>
                <textarea name="description" class="form-control" rows="4" placeholder="Describe what you observed..." required></textarea>
              </div>

              <!-- City/Municipality -->
              <div class="mb-3">
                <label class="form-label fw-medium" style="font-size: 14px;">City/Municipality</label>
                <select name="lgu_id" id="lguSelect" class="form-select" required>
                  <option value="" selected disabled>Select city/municipality</option>
                  <!-- Populated via JavaScript -->
                </select>
              </div>

              <!-- Barangay -->
              <div class="mb-3">
                <label class="form-label fw-medium" style="font-size: 14px;">Barangay</label>
                <select name="barangay_id" id="barangaySelect" class="form-select" required disabled>
                  <option value="" selected disabled>Select city/municipality first</option>
                  <!-- Populated via JavaScript -->
                </select>
              </div>

              <!-- Purok and Sitio (Optional) -->
              <div class="row g-2 mb-3">
                <div class="col-md-6">
                  <label class="form-label fw-medium" style="font-size: 14px;">Purok <span class="text-muted">(Optional)</span></label>
                  <input type="text" name="purok" class="form-control" placeholder="Enter purok">
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-medium" style="font-size: 14px;">Sitio <span class="text-muted">(Optional)</span></label>
                  <input type="text" name="sitio" class="form-control" placeholder="Enter sitio">
                </div>
              </div>

              <div class="mb-3">
                <label class="form-label fw-medium" style="font-size: 14px;">Upload Photo <span class="text-muted">(Optional)</span></label>
                <input type="file" name="photo" accept="image/*" class="form-control" id="photoInput">
                <small class="text-muted">Max 10MB</small>
              </div>

              <div class="mb-3">
                <label class="form-label fw-medium" style="font-size: 14px;">Email Address <span class="text-muted">(Optional)</span></label>
                <input type="email" name="reporter_email" class="form-control" placeholder="your@email.com" id="emailInput">
                <small class="text-muted">Receive updates on your report</small>
              </div>
            </div>

            <!-- Right Column - Map (appears second on mobile) -->
            <div class="col-lg-6" v-pre>
              <button type="button" id="getLocationBtn" class="btn btn-outline-success w-100 mb-3">
                <i class="bi bi-crosshair me-2"></i>Use My Current Location
              </button>
              <div id="map"></div>
              <small class="text-muted d-block mt-2 text-center">Click on the map or search to pin the location</small>
            </div>
          </div>

          <!-- Submit Button - Appears at the bottom on all screen sizes -->
          <div class="row mt-4">
            <div class="col-12">
              <button type="submit" class="btn btn-success btn-lg w-100">Submit Report</button>
            </div>
          </div>
        </form>
      </div>
    </div>
  </main>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
<script src="https://unpkg.com/leaflet.fullscreen@2.4.0/Control.FullScreen.js"></script>
<script src="{{ asset('js/location-dropdowns.js') }}"></script>
<script>
  // Map Setup
  const map = L.map('map', { fullscreenControl: true }).setView([7.1907, 125.4553], 13);
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

  // Show selected file name
  const photoInput = document.getElementById('photoInput');
  photoInput.addEventListener('change', function(e) {
    const fileName = e.target.files[0]?.name;
    if (fileName) {
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

  // Form Submission
  document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();

    // Validation
    if (!marker) {
      showToast('Please pin a location on the map', 'warning', 4000);
      return;
    }

    if (!document.getElementById('barangaySelect').value) {
      showToast('Please select a barangay', 'warning', 4000);
      return;
    }

    // Submit
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Submitting...';
    submitBtn.disabled = true;

    // Create FormData for file upload
    const formData = new FormData(this);

    // Extract CSRF token from form data
    const csrfToken = formData.get('_token') || document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // AJAX submission with better error handling
    fetch('{{ route("report-anon.store") }}', {
      method: 'POST',
      body: formData,
      credentials: 'same-origin',
      headers: {
        'X-CSRF-TOKEN': csrfToken
      }
    })
    .then(response => {
      // Check if response is JSON
      const contentType = response.headers.get("content-type");
      if (contentType && contentType.indexOf("application/json") !== -1) {
        return response.json();
      } else {
        // If not JSON, throw error with status
        throw new Error(`Server returned ${response.status}: Expected JSON but got HTML. Please check if you're logged in or if there's a server error.`);
      }
    })
    .then(data => {
      if (data.success) {
        // Show success notification
        showToast('Report submitted successfully!', 'success', 5000);

        // Reset form
        reportForm.reset();
        if (marker) map.removeLayer(marker);
        marker = null;
        document.getElementById('fileName').innerHTML = '';
        
        // Reset button
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;

        // Redirect after delay
        setTimeout(() => {
          window.location.href = data.redirect;
        }, 2000);
      } else {
        throw new Error(data.message || 'Submission failed');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      showToast(error.message || 'Submission failed. Please try again.', 'danger', 5000);
      
      submitBtn.innerHTML = originalText;
      submitBtn.disabled = false;
    });
  });
</script>
@endpush
