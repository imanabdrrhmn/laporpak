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
      resizeObserver: null
    }
  },
  mounted() {
    this.initializeMap()
    this.setupResizeObserver()
  },
  watch: {
    populationData: {
      handler(newData) {
        if (newData) {
          this.updateMapWithPopulationData(newData)
        }
      },
      deep: true
    }
  },
  methods: {
    initializeMap() {
      // Batas koordinat Indonesia
      const indonesiaBounds = [
        [-11.5, 95], // Southwest coordinates (Rote Island)
        [6.2, 141]   // Northeast coordinates (Papua)
      ]
      
      // Inisialisasi peta dengan center di Indonesia dan batas wilayah
      this.map = L.map('map', {
        zoomControl: true,
        attributionControl: true,
        scrollWheelZoom: true,
        doubleClickZoom: true,
        boxZoom: true,
        keyboard: true,
        dragging: true,
        tap: true,
        touchZoom: true,
        maxBounds: indonesiaBounds,
        maxBoundsViscosity: 0.8, // Membuat batas lebih fleksibel
        minZoom: 4,
        maxZoom: 18
      }).setView([-2.5, 118], 5)
      
      // Fit view ke batas Indonesia
      this.map.fitBounds(indonesiaBounds, { padding: [10, 10] })
      
      // Tambahkan tile layer
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors',
        maxZoom: 18,
        tileSize: 256,
        zoomOffset: 0
      }).addTo(this.map)
      
      // Event listener untuk klik pada peta
      this.map.on('click', this.onMapClick)
      
      // Event listener untuk mencegah drag keluar Indonesia
      this.map.on('dragend', () => {
        const center = this.map.getCenter()
        if (!this.isInIndonesia(center)) {
          this.centerToIndonesia()
        }
      })
      
      // Pastikan map terinisialisasi dengan benar
      this.$nextTick(() => {
        this.map.invalidateSize()
      })
    },
    
    setupResizeObserver() {
      // Observer untuk mendeteksi perubahan ukuran container
      if (window.ResizeObserver) {
        this.resizeObserver = new ResizeObserver(() => {
          if (this.map) {
            this.map.invalidateSize()
          }
        })
        
        const mapElement = document.getElementById('map')
        if (mapElement) {
          this.resizeObserver.observe(mapElement)
        }
      }
      
      // Backup dengan window resize event
      window.addEventListener('resize', this.handleWindowResize)
    },
    
    handleWindowResize() {
      if (this.map) {
        setTimeout(() => {
          this.map.invalidateSize()
        }, 100)
      }
    },
    
    onMapClick(e) {
      const location = {
        lat: e.latlng.lat,
        lng: e.latlng.lng
      }
      
      // Cek apakah lokasi masih dalam batas Indonesia
      if (!this.isInIndonesia(location)) {
        this.showNotification('Pilih lokasi dalam wilayah Indonesia', 'warning')
        return
      }
      
      this.selectedLocation = location
      this.addMarkerAndCircle(location)
      this.$emit('location-selected', location)
    },
    
    isInIndonesia(location) {
      // Approximate bounds of Indonesia (more precise)
      const bounds = {
        north: 6.2,   // Sabang, Aceh
        south: -11.5, // Rote Island, NTT
        east: 141,    // Merauke, Papua
        west: 95      // Sabang, Aceh
      }
      
      return location.lat <= bounds.north && 
             location.lat >= bounds.south && 
             location.lng <= bounds.east && 
             location.lng >= bounds.west
    },
    
    showNotification(message, type = 'info') {
      // Simple notification - bisa diganti dengan toast library
      const notification = document.createElement('div')
      notification.className = `alert alert-${type} position-fixed`
      notification.style.cssText = `
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        animation: slideIn 0.3s ease-out;
      `
      notification.innerHTML = `
        <i class="fas fa-${type === 'warning' ? 'exclamation-triangle' : 'info-circle'} me-2"></i>
        ${message}
      `
      
      document.body.appendChild(notification)
      
      // Auto remove after 3 seconds
      setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease-in'
        setTimeout(() => {
          if (notification.parentNode) {
            notification.parentNode.removeChild(notification)
          }
        }, 300)
      }, 3000)
    },
    
    addMarkerAndCircle(location, radius = 2000) {
      // Hapus marker dan circle sebelumnya
      this.clearMapElements()
      
      // Tambahkan marker
      this.currentMarker = L.marker([location.lat, location.lng])
        .addTo(this.map)
        .bindPopup('Menganalisis populasi...')
        .openPopup()
      
      // Tambahkan circle
      this.currentCircle = L.circle([location.lat, location.lng], {
        color: '#0078e7',
        fillColor: '#0078e7',
        fillOpacity: 0.2,
        radius: radius
      }).addTo(this.map)
      
      // Sesuaikan view untuk menampilkan circle dengan padding yang responsif
      const padding = window.innerWidth < 768 ? [10, 10] : [20, 20]
      this.map.fitBounds(this.currentCircle.getBounds(), { padding })
    },
    
    updateMapWithPopulationData(data) {
      if (this.currentMarker && data) {
        const popupContent = `
          <div class="popup-content">
            <h6 class="mb-2"><i class="fas fa-users text-primary"></i> Estimasi Populasi</h6>
            <p class="mb-1"><strong>${data.total_population?.toLocaleString('id-ID') || 'N/A'}</strong> jiwa</p>
            <small class="text-muted">dalam radius ${(data.radius/1000).toFixed(1)} km</small>
          </div>
        `
        this.currentMarker.setPopupContent(popupContent)
        
        // Update circle radius jika berbeda
        if (this.currentCircle && data.radius) {
          this.currentCircle.setRadius(data.radius)
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
    },
    
    centerToIndonesia() {
      // Fit view ke Indonesia dengan padding
      const indonesiaBounds = [
        [-11.5, 95], // Southwest
        [6.2, 141]   // Northeast
      ]
      this.map.fitBounds(indonesiaBounds, { 
        padding: [20, 20],
        maxZoom: 5 
      })
    }
  },
  beforeUnmount() {
    // Cleanup
    if (this.resizeObserver) {
      this.resizeObserver.disconnect()
    }
    window.removeEventListener('resize', this.handleWindowResize)
    
    if (this.map) {
      this.map.remove()
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
  border-radius: 0.75rem;
  width: 100%;
  max-width: 100%;
  overflow: hidden;
}

.card-header {
  padding: 1rem 1.25rem 0.75rem;
}

.map-wrapper {
  position: relative;
  width: 100%;
  height: 0;
  padding-bottom: 60%; /* Aspect ratio 5:3 */
  overflow: hidden;
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
}

.map-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
  border-radius: 0;
}

.map-controls {
  flex-shrink: 0;
}

.map-controls .btn {
  border-radius: 50%;
  width: 38px;
  height: 38px;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0;
  line-height: 1;
}

.card-title {
  font-size: 1.125rem;
  font-weight: 600;
}

/* Responsive breakpoints */
@media (max-width: 576px) {
  .map-wrapper {
    padding-bottom: 70%; /* Lebih tinggi untuk mobile */
  }
  
  .card-header {
    padding: 0.75rem 1rem 0.5rem;
  }
  
  .map-controls .btn {
    width: 34px;
    height: 34px;
    font-size: 0.875rem;
  }
  
  .card-title {
    font-size: 1rem;
  }
}

@media (max-width: 768px) {
  .map-wrapper {
    padding-bottom: 65%;
  }
}

@media (min-width: 992px) {
  .map-wrapper {
    padding-bottom: 50%; /* Lebih lebar untuk desktop */
  }
}

@media (min-width: 1200px) {
  .map-wrapper {
    padding-bottom: 45%;
  }
}

/* Leaflet popup customization */
:deep(.leaflet-popup-content) {
  margin: 8px 12px;
  font-size: 0.875rem;
}

:deep(.popup-content h6) {
  color: #0062cc;
  margin-bottom: 8px;
  font-size: 0.9rem;
}

:deep(.leaflet-popup-content-wrapper) {
  border-radius: 8px;
}

:deep(.leaflet-container) {
  font-family: inherit;
}

/* Perbaikan untuk kontroler zoom */
:deep(.leaflet-control-zoom) {
  border-radius: 6px;
  overflow: hidden;
}

:deep(.leaflet-control-zoom a) {
  width: 30px;
  height: 30px;
  line-height: 28px;
  font-size: 16px;
}

/* Responsif untuk attribution */
:deep(.leaflet-control-attribution) {
  font-size: 10px;
  background: rgba(255, 255, 255, 0.8);
}

@media (max-width: 576px) {
  :deep(.leaflet-control-attribution) {
    font-size: 8px;
    max-width: 200px;
  }
}

/* Notification animations */
@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

@keyframes slideOut {
  from {
    transform: translateX(0);
    opacity: 1;
  }
  to {
    transform: translateX(100%);
    opacity: 0;
  }
}
</style>