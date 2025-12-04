/**
 * Vanilla JavaScript Leaflet Map for EcoWatch Reports
 * No Vue.js dependencies
 */

class ReportMap {
    constructor(options = {}) {
        this.mapContainer = options.mapContainer || 'map';
        this.center = options.center || [7.1907, 125.4553];
        this.zoom = options.zoom || 13;
        this.reports = options.reports || [];
        this.mapHeight = options.mapHeight || '500px';

        this.map = null;
        this.markers = [];
        this.filterType = '';
        this.filterStatus = '';
        this.clusterEnabled = false;
        this.currentLocationMarker = null;

        this.init();
    }

    init() {
        this.setupMapContainer();
        this.initMap();
        this.setupControls();
        this.updateMarkers();
    }

    setupMapContainer() {
        const container = document.getElementById(this.mapContainer);
        if (container) {
            container.style.height = this.mapHeight;
            container.style.borderRadius = '8px';
            container.style.overflow = 'hidden';
            container.style.boxShadow = '0 2px 8px rgba(0, 0, 0, 0.1)';
        }
    }

    initMap() {
        this.map = L.map(this.mapContainer).setView(this.center, this.zoom);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
            maxZoom: 19,
        }).addTo(this.map);
    }

    setupControls() {
        // Type filter
        const typeFilter = document.getElementById('filter-type');
        if (typeFilter) {
            typeFilter.addEventListener('change', (e) => {
                this.filterType = e.target.value;
                this.updateMarkers();
            });
        }

        // Status filter
        const statusFilter = document.getElementById('filter-status');
        if (statusFilter) {
            statusFilter.addEventListener('change', (e) => {
                this.filterStatus = e.target.value;
                this.updateMarkers();
            });
        }

        // Get location button
        const locationBtn = document.getElementById('btn-get-location');
        if (locationBtn) {
            locationBtn.addEventListener('click', () => this.getCurrentLocation());
        }

        // Toggle cluster button
        const clusterBtn = document.getElementById('btn-toggle-cluster');
        if (clusterBtn) {
            clusterBtn.addEventListener('click', () => this.toggleCluster());
        }
    }

    getFilteredReports() {
        let filtered = [...this.reports];

        if (this.filterType) {
            filtered = filtered.filter(r => r.violation_type === this.filterType);
        }

        if (this.filterStatus) {
            filtered = filtered.filter(r => r.status === this.filterStatus);
        }

        return filtered;
    }

    getMarkerColor(status) {
        const colors = {
            'pending': '#ffc107',
            'in-review': '#0dcaf0',
            'in-progress': '#0d6efd',
            'resolved': '#198754',
            'verified': '#198754',
        };
        return colors[status] || '#6c757d';
    }

    createCustomIcon(report) {
        const color = this.getMarkerColor(report.status);
        return L.divIcon({
            className: 'custom-marker',
            html: `<div style="background-color: ${color}; width: 30px; height: 30px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>`,
            iconSize: [30, 30],
            iconAnchor: [15, 15],
            popupAnchor: [0, -15],
        });
    }

    updateMarkers() {
        // Clear existing markers
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];

        // Get filtered reports
        const filteredReports = this.getFilteredReports();

        // Add new markers
        filteredReports.forEach(report => {
            if (report.latitude && report.longitude) {
                const marker = L.marker([report.latitude, report.longitude], {
                    icon: this.createCustomIcon(report),
                });

                // Create popup content
                const popupContent = `
                    <div style="min-width: 200px;">
                        <h6 class="fw-bold mb-2">${report.report_id || 'Unknown'}</h6>
                        <p class="mb-1"><strong>Type:</strong> ${report.violation_type_display || report.violation_type || 'N/A'}</p>
                        <p class="mb-1"><strong>Status:</strong> <span class="badge bg-${this.getStatusColor(report.status)}">${report.status_display || report.status || 'N/A'}</span></p>
                        <p class="mb-2"><strong>Location:</strong> ${report.location || 'N/A'}</p>
                        ${report.report_id ? `<button class="btn btn-sm btn-primary w-100" onclick="viewReportDetails('${report.report_id}')">View Details</button>` : ''}
                    </div>
                `;

                marker.bindPopup(popupContent);
                this.markers.push(marker);
                marker.addTo(this.map);
            }
        });

        // Fit bounds if we have markers
        if (this.markers.length > 0) {
            const group = L.featureGroup(this.markers);
            this.map.fitBounds(group.getBounds().pad(0.1));
        }

        // Update count
        this.updateReportCount(filteredReports.length);
    }

    getStatusColor(status) {
        const colors = {
            'pending': 'warning',
            'in-review': 'info',
            'in-progress': 'primary',
            'resolved': 'success',
            'verified': 'success',
        };
        return colors[status] || 'secondary';
    }

    updateReportCount(count) {
        const countElement = document.getElementById('report-count');
        if (countElement) {
            countElement.textContent = count;
        }
    }

    getCurrentLocation() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser');
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const { latitude, longitude } = position.coords;

                // Remove old location marker
                if (this.currentLocationMarker) {
                    this.map.removeLayer(this.currentLocationMarker);
                }

                // Add new location marker
                this.currentLocationMarker = L.marker([latitude, longitude], {
                    icon: L.divIcon({
                        className: 'current-location-marker',
                        html: '<div style="background-color: #0d6efd; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 0 10px rgba(13, 110, 253, 0.5);"></div>',
                        iconSize: [20, 20],
                        iconAnchor: [10, 10],
                    }),
                }).addTo(this.map);

                this.currentLocationMarker.bindPopup('<b>Your Location</b>').openPopup();
                this.map.setView([latitude, longitude], 15);
            },
            (error) => {
                alert('Unable to retrieve your location: ' + error.message);
            }
        );
    }

    toggleCluster() {
        this.clusterEnabled = !this.clusterEnabled;
        const clusterBtn = document.getElementById('btn-toggle-cluster');
        if (clusterBtn) {
            const text = this.clusterEnabled ? 'Disable Clustering' : 'Enable Clustering';
            clusterBtn.innerHTML = `<i class="bi bi-grid-3x3 me-1"></i>${text}`;
        }
        // For now, just update markers
        // To implement actual clustering, you would need leaflet.markercluster plugin
        this.updateMarkers();
    }

    setReports(reports) {
        this.reports = reports;
        this.updateMarkers();
    }

    destroy() {
        if (this.map) {
            this.map.remove();
        }
    }
}

// Global function to view report details
function viewReportDetails(reportId) {
    // Dispatch custom event that can be caught by the page
    window.dispatchEvent(new CustomEvent('view-report', {
        detail: { reportId: reportId }
    }));

    // You can also redirect to the report page
    // window.location.href = `/report/${reportId}`;
}

// Make ReportMap available globally
window.ReportMap = ReportMap;
