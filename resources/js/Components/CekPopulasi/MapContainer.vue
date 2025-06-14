<template>
  <div class="map-container">
    <div class="card shadow-lg border-0">
      <div class="card-header bg-white border-0 pb-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <h5 class="card-title mb-0 mb-sm-0">
            <i class="fas fa-map-marked-alt text-primary me-2"></i>
            <span class="d-none d-sm-inline">Peta Interaktif</span>
            <span class="d-inline d-sm-none">Peta</span>
          </h5>
          <div class="map-controls d-flex">
            <button 
              class="btn btn-outline-primary btn-sm me-2"
              @click="centerToIndonesia"
              title="Kembali ke Indonesia"
            >
              <i class="fas fa-home"></i>
            </button>
            <button 
              class="btn btn-outline-secondary btn-sm"
              @click="clearSelection"
              :disabled="!selectedLocation"
              title="Hapus Seleksi"
            >
              <i class="fas fa-eraser"></i>
            </button>
          </div>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="map-wrapper">
          <div 
            id="map" 
            class="leaflet-map"
            :class="{ 'loading': loading }"
          ></div>
          <div v-if="loading" class="map-loading-overlay">
            <div class="d-flex flex-column align-items-center">
              <div class="spinner-border text-primary mb-2" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <small class="text-muted text-center px-2">Menganalisis data populasi...</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import L from 'leaflet'
import 'leaflet/dist/leaflet.css'

// Fix untuk icon default Leaflet
delete L.Icon.Default.prototype._getIconUrl
L.Icon.Default.mergeOptions({
  iconRetinaUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon-2x.png',
  iconUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-icon.png',
  shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.7.1/images/marker-shadow.png',
})

export default {
  name: 'MapContainer',
  props: {
    populationData: {
      type: Object,
      default: null
    },
    loading: {
      type: Boolean,
      default: false
    }
  },
  data() {
    return {
      map: null,
      currentMarker: null,
      currentCircle: null,
      selectedLocation: null,
      resizeObserver: null,
      isMapReady: false
    }
  },
  mounted() {
    // Delay initialization untuk memastikan DOM ready
    this.$nextTick(() => {
      setTimeout(() => {
        this.initializeMap()
        this.setupResizeObserver()
      }, 100)
    })
  },
  watch: {
    populationData: {
      handler(newData) {
        if (newData && this.isMapReady) {
          this.updateMapWithPopulationData(newData)
        }
      },
      deep: true
    }
  },
  methods: {
    initializeMap() {
      const mapElement = document.getElementById('map')
      if (!mapElement) {
        console.error('Map element not found')
        return
      }

      // Batas koordinat Indonesia yang lebih akurat
      const indonesiaBounds = [
        [-11.008694, 95.009003], // Southwest (Rote Island)
        [6.216968, 141.019555]   // Northeast (Papua)
      ]
      
      // Inisialisasi peta dengan pengaturan yang lebih baik
      this.map = L.map('map', {
        preferCanvas: true, // Untuk performa yang lebih baik
        zoomControl: false, // Kita akan menambahkan custom zoom control
        attributionControl: true,
        scrollWheelZoom: true,
        doubleClickZoom: true,
        boxZoom: true,
        keyboard: true,
        dragging: true,
        tap: true,
        touchZoom: true,
        maxBounds: indonesiaBounds,
        maxBoundsViscosity: 1.0,
        minZoom: 4,
        maxZoom: 18,
        zoomSnap: 0.5,
        zoomDelta: 0.5
      })
      
      // Set initial view
      this.map.setView([-2.5, 118], 5)
      
      // Tambahkan custom zoom control
      L.control.zoom({
        position: 'bottomright'
      }).addTo(this.map)
      
      // Tambahkan tile layer dengan kualitas yang lebih baik
      const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 18,
        tileSize: 256,
        zoomOffset: 0,
        detectRetina: true, // Untuk display retina
        crossOrigin: true
      })
      
      tileLayer.addTo(this.map)
      
      // Event listener untuk tile loading
      tileLayer.on('loading', () => {
        this.isMapReady = false
      })
      
      tileLayer.on('load', () => {
        this.isMapReady = true
        this.map.invalidateSize()
      })
      
      // Fit view ke Indonesia dengan smooth animation
      this.map.fitBounds(indonesiaBounds, { 
        padding: [20, 20],
        animate: true,
        duration: 1
      })
      
      // Event listeners
      this.map.on('click', this.onMapClick)
      this.map.on('dragend', this.handleDragEnd)
      this.map.on('zoomend', this.handleZoomEnd)
      
      // Ensure proper sizing setelah initialization
      setTimeout(() => {
        this.map.invalidateSize()
        this.isMapReady = true
      }, 500)
    },
    
    setupResizeObserver() {
      if (window.ResizeObserver) {
        this.resizeObserver = new ResizeObserver(entries => {
          if (this.map && this.isMapReady) {
            // Debounce resize
            clearTimeout(this.resizeTimeout)
            this.resizeTimeout = setTimeout(() => {
              this.map.invalidateSize({ animate: true })
            }, 150)
          }
        })
        
        const mapElement = document.getElementById('map')
        if (mapElement) {
          this.resizeObserver.observe(mapElement)
          this.resizeObserver.observe(mapElement.parentElement)
        }
      }
      
      // Backup dengan window resize event
      window.addEventListener('resize', this.handleWindowResize)
    },
    
    handleWindowResize() {
      if (this.map && this.isMapReady) {
        clearTimeout(this.resizeTimeout)
        this.resizeTimeout = setTimeout(() => {
          this.map.invalidateSize({ animate: true })
        }, 200)
      }
    },
    
    handleDragEnd() {
      const center = this.map.getCenter()
      if (!this.isInIndonesia(center)) {
        this.centerToIndonesia()
      }
    },
    
    handleZoomEnd() {
      // Pastikan view tetap dalam batas Indonesia
      const bounds = this.map.getBounds()
      if (!this.isViewInIndonesia(bounds)) {
        this.centerToIndonesia()
      }
    },
    
    onMapClick(e) {
      const location = {
        lat: parseFloat(e.latlng.lat.toFixed(6)),
        lng: parseFloat(e.latlng.lng.toFixed(6))
      }
      
      if (!this.isInIndonesia(location)) {
        this.showNotification('Pilih lokasi dalam wilayah Indonesia', 'warning')
        return
      }
      
      this.selectedLocation = location
      this.addMarkerAndCircle(location)
      this.$emit('location-selected', location)
    },
    
    isInIndonesia(location) {
      const bounds = {
        north: 6.22,
        south: -11.01,
        east: 141.02,
        west: 95.01
      }
      
      return location.lat <= bounds.north && 
             location.lat >= bounds.south && 
             location.lng <= bounds.east && 
             location.lng >= bounds.west
    },
    
    isViewInIndonesia(bounds) {
      const indonesiaBounds = {
        north: 6.22,
        south: -11.01,
        east: 141.02,
        west: 95.01
      }
      
      return bounds.getNorth() <= indonesiaBounds.north + 2 &&
             bounds.getSouth() >= indonesiaBounds.south - 2 &&
             bounds.getEast() <= indonesiaBounds.east + 2 &&
             bounds.getWest() >= indonesiaBounds.west - 2
    },
    
    showNotification(message, type = 'info') {
      const notification = document.createElement('div')
      notification.className = `alert alert-${type} position-fixed notification-toast`
      notification.style.cssText = `
        top: 80px;
        right: 20px;
        z-index: 10000;
        min-width: 280px;
        max-width: 350px;
        border-radius: 12px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.12);
        border: none;
        backdrop-filter: blur(10px);
        animation: slideInNotification 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
      `
      notification.innerHTML = `
        <div class="d-flex align-items-center">
          <i class="fas fa-${type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
          <span>${message}</span>
        </div>
      `
      
      document.body.appendChild(notification)
      
      setTimeout(() => {
        notification.style.animation = 'slideOutNotification 0.3s ease-in forwards'
        setTimeout(() => {
          if (notification.parentNode) {
            notification.parentNode.removeChild(notification)
          }
        }, 300)
      }, 3500)
    },
    
    addMarkerAndCircle(location, radius = 2000) {
      this.clearMapElements()
      
      // Custom marker icon
      const customIcon = L.divIcon({
        className: 'custom-marker',
        html: `
          <div class="marker-pin">
            <div class="marker-icon">
              <i class="fas fa-map-marker-alt"></i>
            </div>
          </div>
        `,
        iconSize: [30, 40],
        iconAnchor: [15, 40],
        popupAnchor: [0, -40]
      })
      
      // Tambahkan marker dengan animasi
      this.currentMarker = L.marker([location.lat, location.lng], {
        icon: customIcon,
        riseOnHover: true
      }).addTo(this.map)
      
      // Popup dengan styling yang lebih baik
      const popupContent = `
        <div class="custom-popup">
          <div class="popup-header">
            <i class="fas fa-location-dot text-primary me-2"></i>
            <strong>Lokasi Terpilih</strong>
          </div>
          <div class="popup-content">
            <small class="text-muted">Lat: ${location.lat.toFixed(4)}, Lng: ${location.lng.toFixed(4)}</small>
            <div class="mt-2">
              <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
              <small>Menganalisis populasi...</small>
            </div>
          </div>
        </div>
      `
      
      this.currentMarker.bindPopup(popupContent, {
        className: 'custom-popup-container',
        maxWidth: 280,
        closeButton: true,
        autoClose: false,
        closeOnClick: false
      }).openPopup()
      
      // Tambahkan circle dengan gradient effect
      this.currentCircle = L.circle([location.lat, location.lng], {
        color: '#0078e7',
        fillColor: '#0078e7',
        fillOpacity: 0.15,
        weight: 2,
        radius: radius,
        className: 'analysis-circle'
      }).addTo(this.map)
      
      // Smooth zoom ke area yang dipilih
      const bounds = this.currentCircle.getBounds()
      const padding = window.innerWidth < 768 ? [30, 30] : [50, 50]
      
      this.map.fitBounds(bounds, {
        padding: padding,
        animate: true,
        duration: 0.8
      })
    },
    
    updateMapWithPopulationData(data) {
      if (this.currentMarker && data) {
        const popupContent = `
          <div class="custom-popup">
            <div class="popup-header">
              <i class="fas fa-users text-primary me-2"></i>
              <strong>Estimasi Populasi</strong>
            </div>
            <div class="popup-content">
              <div class="population-number">
                ${data.total_population ? data.total_population.toLocaleString('id-ID') : 'N/A'} jiwa
              </div>
              <small class="text-muted">
                dalam radius ${data.radius ? (data.radius/1000).toFixed(1) : '2.0'} km
              </small>
            </div>
          </div>
        `
        
        this.currentMarker.setPopupContent(popupContent)
        
        // Update circle radius dengan animasi
        if (this.currentCircle && data.radius && data.radius !== this.currentCircle.getRadius()) {
          const steps = 20
          const currentRadius = this.currentCircle.getRadius()
          const targetRadius = data.radius
          const stepSize = (targetRadius - currentRadius) / steps
          
          let step = 0
          const animateRadius = () => {
            if (step < steps) {
              const newRadius = currentRadius + (stepSize * step)
              this.currentCircle.setRadius(newRadius)
              step++
              requestAnimationFrame(animateRadius)
            } else {
              this.currentCircle.setRadius(targetRadius)
            }
          }
          
          animateRadius()
        }
      }
    },
    
    clearMapElements() {
      if (this.currentMarker) {
        this.map.removeLayer(this.currentMarker)
        this.currentMarker = null
      }
      if (this.currentCircle) {
        this.map.removeLayer(this.currentCircle)
        this.currentCircle = null
      }
    },
    
    clearSelection() {
      this.clearMapElements()
      this.selectedLocation = null
      this.$emit('location-selected', null)
      this.centerToIndonesia()
    },
    
    centerToIndonesia() {
      if (!this.map) return
      
      const indonesiaBounds = [
        [-11.008694, 95.009003],
        [6.216968, 141.019555]
      ]
      
      this.map.fitBounds(indonesiaBounds, { 
        padding: [20, 20],
        animate: true,
        duration: 1,
        maxZoom: 5
      })
    }
  },
  
  beforeUnmount() {
    // Cleanup
    if (this.resizeTimeout) {
      clearTimeout(this.resizeTimeout)
    }
    
    if (this.resizeObserver) {
      this.resizeObserver.disconnect()
    }
    
    window.removeEventListener('resize', this.handleWindowResize)
    
    if (this.map) {
      this.map.remove()
      this.map = null
    }
  }
}
</script>

<style scoped>
.map-container {
  position: relative;
  width: 100%;
  max-width: 100%;
  overflow: hidden;
}

.card {
  border-radius: 16px;
  width: 100%;
  max-width: 100%;
  overflow: hidden;
  box-shadow: 0 10px 40px rgba(0,0,0,0.1) !important;
}

.card-header {
  padding: 1.25rem 1.5rem 1rem;
  background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%) !important;
}

.map-wrapper {
  position: relative;
  width: 100%;
  height: 500px; /* Fixed height untuk konsistensi */
  overflow: hidden;
  background: #f8f9fa;
}

.leaflet-map {
  position: absolute;
  top: 0;
  left: 0;
  width: 100% !important;
  height: 100% !important;
  border-radius: 0;
  max-width: none;
  max-height: none;
  z-index: 1;
}

.map-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(4px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  border-radius: 0;
}

.map-controls {
  flex-shrink: 0;
  gap: 8px;
}

.map-controls .btn {
  border-radius: 50%;
  width: 42px;
  height: 42px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  line-height: 1;
  transition: all 0.3s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.map-controls .btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.card-title {
  font-size: 1.25rem;
  font-weight: 600;
  color: #2c3e50;
}

/* Custom marker styles */
:deep(.custom-marker) {
  background: transparent;
  border: none;
}

:deep(.marker-pin) {
  position: relative;
  animation: markerBounce 0.6s ease-out;
}

:deep(.marker-icon) {
  width: 30px;
  height: 30px;
  background: linear-gradient(135deg, #0078e7 0%, #0056b3 100%);
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(0, 120, 231, 0.4);
  border: 3px solid white;
}

:deep(.marker-icon i) {
  color: white;
  font-size: 14px;
  transform: rotate(45deg);
}

/* Custom popup styles */
:deep(.custom-popup-container) {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 32px rgba(0,0,0,0.15);
}

:deep(.custom-popup-container .leaflet-popup-content-wrapper) {
  border-radius: 12px;
  padding: 0;
  background: white;
}

:deep(.custom-popup-container .leaflet-popup-tip) {
  background: white;
}

:deep(.custom-popup) {
  padding: 0;
  margin: 0;
  min-width: 200px;
}

:deep(.popup-header) {
  background: linear-gradient(135deg, #0078e7 0%, #0056b3 100%);
  color: white;
  padding: 12px 16px;
  font-size: 14px;
  font-weight: 600;
}

:deep(.popup-content) {
  padding: 16px;
}

:deep(.population-number) {
  font-size: 18px;
  font-weight: 700;
  color: #0078e7;
  margin-bottom: 4px;
}

/* Leaflet control customization */
:deep(.leaflet-control-zoom) {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  border: none;
}

:deep(.leaflet-control-zoom a) {
  width: 40px;
  height: 40px;
  line-height: 38px;
  font-size: 18px;
  border: none;
  background: white;
  color: #0078e7;
  transition: all 0.2s ease;
}

:deep(.leaflet-control-zoom a:hover) {
  background: #0078e7;
  color: white;
}

:deep(.leaflet-control-attribution) {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(4px);
  border-radius: 8px;
  font-size: 10px;
  margin: 8px;
  padding: 4px 8px;
}

/* Analysis circle animation */
:deep(.analysis-circle) {
  animation: circleAppear 0.8s ease-out;
}

/* Responsive design */
@media (max-width: 576px) {
  .map-wrapper {
    height: 400px;
  }
  
  .card-header {
    padding: 1rem;
  }
  
  .map-controls .btn {
    width: 36px;
    height: 36px;
    font-size: 0.875rem;
  }
  
  .card-title {
    font-size: 1.1rem;
  }
  
  :deep(.leaflet-control-zoom a) {
    width: 36px;
    height: 36px;
    line-height: 34px;
    font-size: 16px;
  }
}

@media (max-width: 768px) {
  .map-wrapper {
    height: 450px;
  }
}

@media (min-width: 992px) {
  .map-wrapper {
    height: 550px;
  }
}

@media (min-width: 1200px) {
  .map-wrapper {
    height: 600px;
  }
}

/* Notification animations */
@keyframes slideInNotification {
  from {
    transform: translateX(100%) scale(0.8);
    opacity: 0;
  }
  to {
    transform: translateX(0) scale(1);
    opacity: 1;
  }
}

@keyframes slideOutNotification {
  from {
    transform: translateX(0) scale(1);
    opacity: 1;
  }
  to {
    transform: translateX(100%) scale(0.8);
    opacity: 0;
  }
}

/* Marker animations */
@keyframes markerBounce {
  0% {
    transform: translateY(-20px) scale(0.8);
    opacity: 0;
  }
  50% {
    transform: translateY(-5px) scale(1.1);
    opacity: 0.8;
  }
  100% {
    transform: translateY(0) scale(1);
    opacity: 1;
  }
}

/* Circle animation */
@keyframes circleAppear {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.1);
    opacity: 0.6;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Loading states */
.leaflet-map.loading {
  filter: blur(1px);
  transition: filter 0.3s ease;
}

/* Notification toast styling */
:global(.notification-toast) {
  border-left: 4px solid currentColor;
  font-weight: 500;
}

:global(.notification-toast.alert-warning) {
  background: rgba(255, 193, 7, 0.1);
  color: #856404;
  border-left-color: #ffc107;
}

:global(.notification-toast.alert-info) {
  background: rgba(13, 202, 240, 0.1);
  color: #055160;
  border-left-color: #0dcaf0;
}
</style>  