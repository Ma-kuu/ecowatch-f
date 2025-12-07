// Location Dropdowns Handler
document.addEventListener('DOMContentLoaded', function() {
  const lguSelect = document.getElementById('lguSelect');
  const barangaySelect = document.getElementById('barangaySelect');

  if (!lguSelect || !barangaySelect) return;

  // Load LGUs
  fetch('/api/lgus')
    .then(res => res.json())
    .then(lgus => {
      lgus.forEach(lgu => {
        const option = document.createElement('option');
        option.value = lgu.id;
        option.textContent = lgu.name;
        lguSelect.appendChild(option);
      });
    })
    .catch(err => console.error('Failed to load LGUs:', err));

  // Load barangays when LGU changes
  lguSelect.addEventListener('change', function() {
    const lguId = this.value;
    barangaySelect.innerHTML = '<option value="" selected disabled>Loading...</option>';
    barangaySelect.disabled = true;

    if (!lguId) {
      barangaySelect.innerHTML = '<option value="" selected disabled>Select city first</option>';
      return;
    }

    fetch(`/api/lgus/${lguId}/barangays`)
      .then(res => res.json())
      .then(barangays => {
        barangaySelect.innerHTML = '<option value="" selected disabled>Select barangay</option>';
        barangays.forEach(barangay => {
          const option = document.createElement('option');
          option.value = barangay.id;
          option.textContent = barangay.name;
          barangaySelect.appendChild(option);
        });
        barangaySelect.disabled = false;
      })
      .catch(err => {
        console.error('Failed to load barangays:', err);
        barangaySelect.innerHTML = '<option value="" selected disabled>Error loading</option>';
      });
  });
});
