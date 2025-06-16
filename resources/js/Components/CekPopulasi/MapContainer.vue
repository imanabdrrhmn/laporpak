<template>
  <div class="map-container">
    <div class="card shadow-lg border-0">
      <div class="card-header bg-white border-0 pb-0">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
          <h5 class="card-title mb-0 mb-sm-0">
            <i class="bi bi-geo-alt text-primary me-2"></i>
            <span class="d-none d-sm-inline">Peta Interaktif Indonesia</span>
            <span class="d-inline d-sm-none">Peta Indonesia</span>
          </h5>
        </div>
      </div>
      <div class="card-body p-0">
        <div class="map-wrapper" :class="{ 'fullscreen': isFullscreen }">
          <div 
            id="map" 
            class="leaflet-map"
            :class="{ 'loading': loading }"
          ></div>
          
          <!-- Map Layers Control -->
          <div class="map-layers-control">
            <button 
              class="btn btn-outline-secondary btn-sm layers-toggle"
              @click="toggleLayers"
            >
              <i class="bi bi-layers me-1"></i>
              Layer: {{ currentLayerLabel }}
            </button>
            <div class="layers-menu" :class="{ 'show': showLayers }">
              <div class="layer-option" 
                   v-for="layer in mapLayers" 
                   :key="layer.name"
                   @click="changeMapLayer(layer)"
                   :class="{ 'active': currentLayer === layer.name }">
                <i :class="layer.icon"></i>
                <span>{{ layer.label }}</span>
              </div>
            </div>
          </div>
          
          <!-- Loading Overlay -->
          <div v-if="loading" class="map-loading-overlay">
            <div class="d-flex flex-column align-items-center">
              <span class="text-muted">Memuat data...</span>
            </div>
          </div>
          
          <!-- Coordinates Display -->
          <div class="coordinates-display" v-if="mouseCoordinates">
            <small>{{ mouseCoordinates.lat.toFixed(4) }}°, {{ mouseCoordinates.lng.toFixed(4) }}°</small>
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
      isMapReady: false,
      isFullscreen: false,
      showLayers: false,
      currentLayer: 'default',
      mouseCoordinates: null,
      mapLayers: [
        {
          name: 'default',
          label: 'Peta Dasar',
          icon: 'bi bi-map',
          url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
          attribution: '© OpenStreetMap contributors'
        },
        {
          name: 'satellite',
          label: 'Satelit',
          icon: 'bi bi-globe',
          url: 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
          attribution: '© Esri, Maxar, GeoEye, Earthstar Geographics'
        },
        {
          name: 'terrain',
          label: 'Medan',
          icon: 'bi bi-triangle',
          url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
          attribution: '© OpenTopoMap contributors'
        },
        {
          name: 'dark',
          label: 'Gelap',
          icon: 'bi bi-moon',
          url: 'https://{s}.basemaps.cartocdn.com/dark_all/{z}/{x}/{y}{r}.png',
          attribution: '© CartoDB, © OpenStreetMap contributors'
        }
      ],
      currentTileLayer: null
    }
  },
  computed: {
    currentLayerLabel() {
      const layer = this.mapLayers.find(l => l.name === this.currentLayer)
      return layer ? layer.label : 'Peta Dasar'
    }
  },
  mounted() {
    this.$nextTick(() => {
      setTimeout(() => {
        this.initializeMap()
        this.setupResizeObserver()
        this.setupEventListeners()
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
    // Inisialisasi peta utama
    initializeMap() {
      const mapElement = document.getElementById('map')
      if (!mapElement) {
        console.error('Map element not found')
        return
      }

      const indonesiaBounds = [
        [-11.008694, 95.009003],
        [6.216968, 141.019555]
      ]
      
      this.map = L.map('map', {
        preferCanvas: true,
        zoomControl: false,
        attributionControl: false,
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
        zoomSnap: 0.25,
        zoomDelta: 0.5
      })
      
      this.map.setView([-2.5, 118], 5)
      
      // Custom zoom control
      L.control.zoom({
        position: 'bottomright'
      }).addTo(this.map)
      
      // Custom attribution
      L.control.attribution({
        position: 'bottomleft',
        prefix: false
      }).addTo(this.map)
      
      // Tambahkan default tile layer
      this.addTileLayer(this.mapLayers[0])
      
      // Event listeners
      this.map.on('click', this.onMapClick)
      this.map.on('mousemove', this.onMouseMove)
      this.map.on('mouseout', this.onMouseOut)
      this.map.on('dragend', this.handleDragEnd)
      this.map.on('zoomend', this.handleZoomEnd)
      this.map.on('load', () => {
        this.isMapReady = true
      })
      
      this.map.fitBounds(indonesiaBounds, { 
        padding: [20, 20],
        animate: true,
        duration: 1
      })
      
      setTimeout(() => {
        this.map.invalidateSize()
        this.isMapReady = true
      }, 500)
    },
    
    // Menambahkan layer peta
    addTileLayer(layerConfig) {
      if (this.currentTileLayer) {
        this.map.removeLayer(this.currentTileLayer)
      }
      
      this.currentTileLayer = L.tileLayer(layerConfig.url, {
        attribution: layerConfig.attribution,
        maxZoom: 18,
        tileSize: 256,
        zoomOffset: 0,
        detectRetina: true,
        crossOrigin: true
      })
      
      this.currentTileLayer.addTo(this.map)
      this.currentLayer = layerConfig.name
      this.showLayers = false
    },
    
    // Ganti layer peta
    changeMapLayer(layer) {
      this.addTileLayer(layer)
    },
    
    // Toggle menu layer
    toggleLayers() {
      this.showLayers = !this.showLayers
    },
    
    // Toggle fullscreen
    toggleFullscreen() {
      this.isFullscreen = !this.isFullscreen
      
      if (this.isFullscreen) {
        const mapContainer = document.querySelector('.map-container')
        if (mapContainer.requestFullscreen) {
          mapContainer.requestFullscreen()
        } else if (mapContainer.webkitRequestFullscreen) {
          mapContainer.webkitRequestFullscreen()
        } else if (mapContainer.mozRequestFullScreen) {
          mapContainer.mozRequestFullScreen()
        }
      } else {
        if (document.exitFullscreen) {
          document.exitFullscreen()
        } else if (document.webkitExitFullscreen) {
          document.webkitExitFullscreen()
        } else if (document.mozCancelFullScreen) {
          document.mozCancelFullScreen()
        }
      }
      
      setTimeout(() => {
        if (this.map) {
          this.map.invalidateSize()
        }
      }, 100)
    },
    
    // Handler pergerakan mouse
    onMouseMove(e) {
      this.mouseCoordinates = {
        lat: e.latlng.lat,
        lng: e.latlng.lng
      }
    },
    
    onMouseOut() {
      this.mouseCoordinates = null
    },
    
    // Setup resize observer
    setupResizeObserver() {
      if (window.ResizeObserver) {
        this.resizeObserver = new ResizeObserver(entries => {
          if (this.map && this.isMapReady) {
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
      
      window.addEventListener('resize', this.handleWindowResize)
    },
    
    // Setup event listeners
    setupEventListeners() {
      document.addEventListener('fullscreenchange', this.handleFullscreenChange)
      document.addEventListener('webkitfullscreenchange', this.handleFullscreenChange)
      document.addEventListener('mozfullscreenchange', this.handleFullscreenChange)
      
      document.addEventListener('click', (e) => {
        if (!e.target.closest('.map-layers-control')) {
          this.showLayers = false
        }
      })
    },
    
    handleFullscreenChange() {
      this.isFullscreen = !!(document.fullscreenElement || 
                            document.webkitFullscreenElement || 
                            document.mozFullScreenElement)
      
      setTimeout(() => {
        if (this.map) {
          this.map.invalidateSize()
        }
      }, 100)
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
      const bounds = this.map.getBounds()
      if (!this.isViewInIndonesia(bounds)) {
        this.centerToIndonesia()
      }
    },
    
    // Handler klik peta
    onMapClick(e) {
      const location = {
        lat: parseFloat(e.latlng.lat.toFixed(6)),
        lng: parseFloat(e.latlng.lng.toFixed(6))
      }
      
      if (!this.isInIndonesia(location)) {
        this.showNotification('Pilih lokasi di Indonesia', 'warning')
        return
      }
      
      this.selectedLocation = location
      this.addMarkerAndCircle(location)
      this.$emit('location-selected', location)
    },
    
    // Validasi lokasi di Indonesia
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
    
    // Menampilkan notifikasi
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
        <div class="d-flex align-items-center p-3">
          <i class="bi ${type === 'warning' ? 'bi-exclamation-triangle' : 'bi-info-circle'} me-2"></i>
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
      }, 3000)
    },
    
    // Menambahkan marker dan lingkaran
    addMarkerAndCircle(location, radius = 2000) {
      this.clearMapElements()
      
      const customIcon = L.divIcon({
        className: 'custom-marker-modern',
        html: `
          <div class="marker-container">
            <div class="marker-pin">
              <div class="marker-pulse"></div>
              <div class="marker-inner">
                <i class="bi bi-geo-alt-fill"></i>
              </div>
            </div>
          </div>
        `,
        iconSize: [36, 46],
        iconAnchor: [18, 46],
        popupAnchor: [0, -46]
      })
      
      this.currentMarker = L.marker([location.lat, location.lng], {
        icon: customIcon,
        riseOnHover: true
      }).addTo(this.map)
      
      const popupContent = `
        <div class="modern-popup">
          <div class="popup-header">
            <div class="header-icon">
              <i class="bi bi-geo-alt"></i>
            </div>
            <div class="header-content">
              <h6 class="mb-1">Lokasi Dipilih</h6>
              <small class="coordinates">
                ${location.lat.toFixed(4)}°, ${location.lng.toFixed(4)}°
              </small>
            </div>
          </div>
          <div class="popup-body">
            <div class="analysis-status">
              <span class="status-text">Memuat data...</span>
            </div>
          </div>
        </div>
      `
      
      this.currentMarker.bindPopup(popupContent, {
        className: 'modern-popup-container',
        maxWidth: 280,
        closeButton: true,
        autoClose: false,
        closeOnClick: false,
        offset: [0, -10]
      }).openPopup()
      
      this.currentCircle = L.circle([location.lat, location.lng], {
        color: '#3b82f6',
        fillColor: '#3b82f6',
        fillOpacity: 0.1,
        weight: 2,
        radius: radius,
        className: 'analysis-circle-modern',
        dashArray: '8, 4'
      }).addTo(this.map)
      
      const bounds = this.currentCircle.getBounds()
      const padding = window.innerWidth < 768 ? [30, 30] : [50, 50]
      
      this.map.fitBounds(bounds, {
        padding: padding,
        animate: true,
        duration: 1,
        easeLinearity: 0.3
      })
    },
    
    // Update data populasi
    updateMapWithPopulationData(data) {
      if (this.currentMarker && data) {
        const popupContent = `
          <div class="modern-popup">
            <div class="popup-header">
              <div class="header-icon success">
                <i class="bi bi-people-fill"></i>
              </div>
              <div class="header-content">
                <h6 class="mb-1">Data Populasi</h6>
                <small class="coordinates">
                  Radius ${data.radius ? (data.radius/1000).toFixed(1) : '2.0'} km
                </small>
              </div>
            </div>
            <div class="popup-body">
              <div class="population-result">
                <div class="population-number">
                  ${data.total_population ? data.total_population.toLocaleString('id-ID') : 'N/A'}
                </div>
                <div class="population-label">jiwa</div>
              </div>
            </div>
          </div>
        `
        
        this.currentMarker.setPopupContent(popupContent)
        
        if (this.currentCircle && data.radius && data.radius !== this.currentCircle.getRadius()) {
          this.animateCircleRadius(data.radius)
        }
      }
    },
    
    // Animasi perubahan radius lingkaran
    animateCircleRadius(targetRadius) {
      const currentRadius = this.currentCircle.getRadius()
      const duration = 800
      const startTime = Date.now()
      
      const animate = () => {
        const elapsed = Date.now() - startTime
        const progress = Math.min(elapsed / duration, 1)
        const easeOut = 1 - Math.pow(1 - progress, 3)
        const newRadius = currentRadius + (targetRadius - currentRadius) * easeOut
        
        this.currentCircle.setRadius(newRadius)
        
        if (progress < 1) {
          requestAnimationFrame(animate)
        }
      }
      
      animate()
    },
    
    // Bersihkan elemen peta
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
    
    // Bersihkan seleksi
    clearSelection() {
      this.clearMapElements()
      this.selectedLocation = null
      this.$emit('location-selected', null)
      this.centerToIndonesia()
    },
    
    // Pindahkan peta ke tengah Indonesia
    centerToIndonesia() {
      if (!this.map) return
      
      const indonesiaBounds = [
        [-11.008694, 95.009003],
        [6.216968, 141.019555]
      ]
      
      this.map.fitBounds(indonesiaBounds, { 
        padding: [20, 20],
        animate: true,
        duration: 1.2,
        maxZoom: 5,
        easeLinearity: 0.3
      })
    }
  },
  
  beforeUnmount() {
    if (this.resizeTimeout) {
      clearTimeout(this.resizeTimeout)
    }
    
    if (this.resizeObserver) {
      this.resizeObserver.disconnect()
    }
    
    window.removeEventListener('resize', this.handleWindowResize)
    document.removeEventListener('fullscreenchange', this.handleFullscreenChange)
    document.removeEventListener('webkitfullscreenchange', this.handleFullscreenChange)
    document.removeEventListener('mozfullscreenchange', this.handleFullscreenChange)
    
    if (this.map) {
      this.map.remove()
      this.map = null
    }
  }
}
</script>

<style scoped>
/* Container utama */
.map-container {
  position: relative;
  width: 100%;
  max-width: 100%;
  overflow: hidden;
}

.card {
  border-radius: 16px;
  overflow: hidden;
  box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
  border: 1px solid rgba(255,255,255,0.15);
  background: #ffffff;
}

.card-header {
  padding: 1rem 1.5rem;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%) !important;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.card-title {
  color: #1e293b;
  font-weight: 600;
  font-size: 1.25rem;
}

/* Map wrapper */
.map-wrapper {
  position: relative;
  width: 100%;
  height: 600px;
  background: #f1f5f9;
  transition: all 0.3s ease;
}

.map-wrapper.fullscreen {
  position: fixed;
  top: 0;
  left: 0;
  width: 100vw !important;
  height: 100vh !important;
  z-index: 9999;
  border-radius: 0;
}

.leaflet-map {
  position: absolute;
  top: 0;
  left: 0;
  width: 100% !important;
  height: 100% !important;
  z-index: 1;
  filter: contrast(1.05) saturate(1.1);
}

/* Loading overlay */
.map-loading-overlay {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(8px);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 1000;
}

/* Map controls */
.map-controls {
  flex-shrink: 0;
  gap: 8px;
}

.map-controls .btn {
  border-radius: 8px;
  padding: 8px 12px;
  height: 36px;
  font-size: 13px;
  font-weight: 500;
  transition: all 0.2s ease;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  border: 1px solid rgba(255,255,255,0.2);
  backdrop-filter: blur(8px);
  position: relative;
}

.map-controls .btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.map-controls .btn:active {
  transform: translateY(0);
}

.map-controls .btn:after {
  content: attr(title);
  position: absolute;
  top: -30px;
  left: 50%;
  transform: translateX(-50%);
  background: rgba(0, 0, 0, 0.8);
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  opacity: 0;
  visibility: hidden;
  transition: all 0.2s ease;
  pointer-events: none;
}

.map-controls .btn:hover:after {
  opacity: 1;
  visibility: visible;
}

/* Map layers control */
.map-layers-control {
  position: absolute;
  top: 16px;
  right: 16px;
  z-index: 1000;
}

.layers-toggle {
  border-radius: 8px;
  padding: 8px 12px;
  height: 36px;
  font-size: 13px;
  font-weight: 500;
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(255,255,255,0.2);
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  transition: all 0.2s ease;
}

.layers-toggle:hover {
  background: white;
  color: #3b82f6;
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.layers-menu {
  position: absolute;
  top: 44px;
  right: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border-radius: 8px;
  min-width: 160px;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
  border: 1px solid rgba(255,255,255,0.2);
  opacity: 0;
  visibility: hidden;
  transform: translateY(-8px) scale(0.95);
  transition: all 0.2s ease;
}

.layers-menu.show {
  opacity: 1;
  visibility: visible;
  transform: translateY(0) scale(1);
}

.layer-option {
  padding: 10px 14px;
  display: flex;
  align-items: center;
  cursor: pointer;
  transition: all 0.2s ease;
  color: #475569;
  font-size: 13px;
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

.layer-option:last-child {
  border-bottom: none;
}

.layer-option:hover {
  background: rgba(59, 130, 246, 0.1);
  color: #3b82f6;
}

.layer-option.active {
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  color: white;
}

.layer-option i {
  margin-right: 10px;
  width: 16px;
  text-align: center;
}

/* Coordinates display */
.coordinates-display {
  position: absolute;
  bottom: 16px;
  right: 16px;
  background: rgba(0, 0, 0, 0.75);
  color: white;
  padding: 6px 10px;
  border-radius: 6px;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 11px;
  backdrop-filter: blur(8px);
  z-index: 1000;
}

/* Marker styles */
:deep(.custom-marker-modern) {
  background: transparent;
  border: none;
}

:deep(.marker-container) {
  position: relative;
  animation: markerAppear 0.6s ease-out;
}

:deep(.marker-pin) {
  position: relative;
  width: 36px;
  height: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
}

:deep(.marker-pulse) {
  position: absolute;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  background: rgba(59, 130, 246, 0.3);
  animation: markerPulse 1.8s infinite ease-out;
  top: 5px;
  left: 0;
}

:deep(.marker-inner) {
  width: 28px;
  height: 28px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  border-radius: 50% 50% 50% 0;
  transform: rotate(-45deg);
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  border: 2px solid white;
  position: relative;
  z-index: 2;
}

:deep(.marker-inner i) {
  color: white;
  font-size: 14px;
  transform: rotate(45deg);
}

/* Popup styles */
:deep(.modern-popup-container) {
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 8px 24px rgba(0,0,0,0.15);
}

:deep(.modern-popup-container .leaflet-popup-content-wrapper) {
  border-radius: 12px;
  padding: 0;
  background: rgba(255, 255, 255, 0.95);
  backdrop-filter: blur(10px);
  border: 1px solid rgba(255,255,255,0.2);
}

:deep(.modern-popup-container .leaflet-popup-tip) {
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(255,255,255,0.2);
  border-top: none;
  border-right: none;
}

:deep(.modern-popup-container .leaflet-popup-close-button) {
  font-size: 16px;
  color: #94a3b8;
  top: 10px;
  right: 10px;
  width: 20px;
  height: 20px;
  line-height: 20px;
  text-align: center;
  border-radius: 50%;
  transition: all 0.2s ease;
}

:deep(.modern-popup-container .leaflet-popup-close-button:hover) {
  background: #f1f5f9;
  color: #3b82f6;
}

:deep(.modern-popup) {
  padding: 0;
  margin: 0;
  min-width: 240px;
  font-family: system-ui, -apple-system, sans-serif;
}

:deep(.popup-header) {
  display: flex;
  align-items: center;
  padding: 12px 16px;
  background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
  border-bottom: 1px solid rgba(0,0,0,0.05);
}

:deep(.header-icon) {
  width: 36px;
  height: 36px;
  border-radius: 8px;
  background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-size: 14px;
  margin-right: 10px;
  box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
}

:deep(.header-icon.success) {
  background: linear-gradient(135deg, #10b981 0%, #059669 100%);
  box-shadow: 0 2px 8px rgba(16, 185, 129, 0.3);
}

:deep(.header-content h6) {
  font-weight: 600;
  color: #1e293b;
  margin: 0;
  font-size: 15px;
}

:deep(.coordinates) {
  color: #64748b;
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 11px;
}

:deep(.popup-body) {
  padding: 16px;
}

:deep(.analysis-status) {
  display: flex;
  align-items: center;
  color: #64748b;
  font-size: 13px;
}

:deep(.status-icon) {
  margin-right: 10px;
}

/* Map controls */
:deep(.leaflet-control-zoom) {
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  border: 1px solid rgba(255,255,255,0.2);
}

:deep(.leaflet-control-zoom a) {
  width: 36px;
  height: 36px;
  line-height: 36px;
  font-size: 16px;
  border: none;
  background: rgba(255, 255, 255, 0.95);
  color: #64748b;
  transition: all 0.2s ease;
}

:deep(.leaflet-control-zoom a:hover) {
  background: #3b82f6;
  color: white;
}

:deep(.leaflet-control-zoom a:first-child) {
  border-top-left-radius: 8px;
  border-top-right-radius: 8px;
}

:deep(.leaflet-control-zoom a:last-child) {
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
}

:deep(.leaflet-control-attribution) {
  background: rgba(0, 0, 0, 0.75);
  color: rgba(255, 255, 255, 0.9);
  border-radius: 6px;
  font-size: 10px;
  margin: 6px;
  padding: 4px 8px;
  backdrop-filter: blur(8px);
}

:deep(.leaflet-control-attribution a) {
  color: rgba(255, 255, 255, 0.9);
}

/* Analysis circle */
:deep(.analysis-circle-modern) {
  animation: circleGrow 0.8s ease-out;
}

/* Bottom map controls */
.map-controls-bottom {
  display: none; /* Hide the controls section */
}

/* Responsive design */
@media (max-width: 576px) {
  .map-wrapper {
    height: 400px;
  }
  
  .card-header {
    padding: 0.8rem 1rem;
  }
  
  .map-controls .btn {
    padding: 6px 10px;
    font-size: 12px;
    height: 32px;
  }
  
  .card-title {
    font-size: 1.1rem;
  }
  
  .map-layers-control {
    top: 12px;
    right: 12px;
  }
  
  .layers-toggle {
    padding: 6px 10px;
    font-size: 12px;
    height: 32px;
  }
  
  .layers-menu {
    top: 40px;
    min-width: 140px;
  }
  
  .coordinates-display {
    bottom: 12px;
    right: 12px;
    font-size: 10px;
    padding: 4px 8px;
  }
  
  :deep(.leaflet-control-zoom a) {
    width: 32px;
    height: 32px;
    line-height: 32px;
    font-size: 14px;
  }
}

@media (max-width: 768px) {
  .map-wrapper {
    height: 450px;
  }
}

@media (min-width: 992px) {
  .map-wrapper {
    height: 650px;
  }
}

@media (min-width: 1200px) {
  .map-wrapper {
    height: 700px;
  }
}

/* Animations */
@keyframes markerAppear {
  0% {
    transform: scale(0) translateY(-40px);
    opacity: 0;
  }
  50% {
    transform: scale(1.1) translateY(-8px);
    opacity: 0.8;
  }
  100% {
    transform: scale(1) translateY(0);
    opacity: 1;
  }
}

@keyframes markerPulse {
  0% {
    transform: scale(0.8);
    opacity: 1;
  }
  100% {
    transform: scale(1.8);
    opacity: 0;
  }
}

@keyframes circleGrow {
  0% {
    transform: scale(0);
    opacity: 0;
  }
  50% {
    transform: scale(1.05);
    opacity: 0.7;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

@keyframes slideInNotification {
  from {
    transform: translateX(100%) scale(0.9);
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
    transform: translateX(100%) scale(0.9);
    opacity: 0;
  }
}

/* Print styles */
@media print {
  .map-controls,
  .mini-map-container,
  .map-layers-control,
  .coordinates-display {
    display: none;
  }
  
  .map-wrapper {
    height: 400px;
  }
}
</style>