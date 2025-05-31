<template>
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h3 class="mb-0">
        <i class="bi bi-geo-alt-fill me-2" aria-hidden="true"></i>
        Peta Interaktif Laporan Penipuan
      </h3>
      <div class="d-flex" role="toolbar" aria-label="Map controls">
        <button 
          class="btn btn-light btn-sm me-2" 
          @click="zoomIn" 
          aria-label="Zoom In"
          :disabled="loading"
        >
          <i class="bi bi-zoom-in" aria-hidden="true"></i>
        </button>
        <button 
          class="btn btn-light btn-sm me-2" 
          @click="zoomOut" 
          aria-label="Zoom Out"
          :disabled="loading"
        >
          <i class="bi bi-zoom-out" aria-hidden="true"></i>
        </button>
        <button 
          class="btn btn-light btn-sm" 
          @click="resetView" 
          aria-label="Reset View"
          :disabled="loading"
        >
          <i class="bi bi-arrow-repeat" aria-hidden="true"></i>
        </button>
      </div>
    </div>
    <div class="card-body p-0 position-relative">
      <div 
        id="map" 
        ref="mapContainer" 
        class="map"
        :class="{ 'map-loading-state': loading }"
        role="application"
        aria-label="Interactive fraud report map"
      ></div>
      <Transition name="fade">
        <div v-if="loading" class="map-loading">
          <div class="spinner-grow text-primary" role="status">
            <span class="visually-hidden">Loading map data...</span>
          </div>
        </div>
      </Transition>
    </div>
    
    <!-- Map Statistics -->
    <div class="card-footer bg-light">
      <div class="row text-center">
        <div class="col-4">
          <small class="text-muted d-block">Total Lokasi</small>
          <strong>{{ locationItems?.length || 0 }}</strong>
        </div>
        <div class="col-4">
          <small class="text-muted d-block">Total Laporan</small>
          <strong>{{ totalReports }}</strong>
        </div>
        <div class="col-4">
          <small class="text-muted d-block">Zona Tinggi</small>
          <strong class="text-danger">{{ highRiskCount }}</strong>
        </div>
      </div>
    </div>
  </div>

  <Transition name="slide-up">
    <div v-if="lastUpdated" class="alert alert-info mt-3 shadow-sm" role="alert">
      <div class="d-flex">
        <div class="me-3">
          <i class="bi bi-info-circle-fill fs-4" aria-hidden="true"></i>
        </div>
        <div>
          <h5 class="alert-heading">Peta Terupdate!</h5>
          <p class="mb-0">
            Data pada peta ini diperbarui secara real-time berdasarkan laporan pengguna. 
            Terakhir diperbarui pada <time :datetime="lastUpdated">{{ formatLastUpdated }}</time>.
          </p>
        </div>
      </div>
    </div>
  </Transition>

  <div class="card mt-3 shadow-sm">
    <div class="card-body">
      <h5 class="card-title">
        <i class="bi bi-question-circle-fill me-2" aria-hidden="true"></i>
        Apa itu LaporMap?
      </h5>
      <p class="card-text">
        LaporMap adalah platform interaktif yang memungkinkan masyarakat untuk melaporkan 
        dan memantau kasus penipuan di berbagai lokasi. Dengan peta real-time, pengguna 
        dapat melihat zona risiko penipuan, detail laporan, dan berkontribusi untuk 
        menciptakan komunitas yang lebih aman.
      </p>
      <div class="row mt-3">
        <div class="col-md-4">
          <div class="legend-item">
            <span class="legend-marker high-risk"></span>
            <small>Risiko Tinggi (>10 laporan)</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="legend-item">
            <span class="legend-marker medium-risk"></span>
            <small>Risiko Sedang (5-10 laporan)</small>
          </div>
        </div>
        <div class="col-md-4">
          <div class="legend-item">
            <span class="legend-marker low-risk"></span>
            <small>Risiko Rendah (<5 laporan)</small>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed, nextTick } from 'vue'

// Lazy import untuk Leaflet - hanya load ketika dibutuhkan
let L = null
let leafletLoaded = false

const props = defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  lastUpdated: {
    type: String,
    default: ''
  },
  locationItems: {
    type: Array,
    default: () => []
  }
})

const emit = defineEmits(['map-ready', 'map-error'])

// Refs
const mapContainer = ref(null)
let map = null
let clusterGroup = null
const markers = ref(new Map()) // Use Map for O(1) lookup
const isMapReady = ref(false)

// Computed properties for performance
const totalReports = computed(() => {
  return props.locationItems?.reduce((sum, item) => sum + (item.reports || 0), 0) || 0
})

const highRiskCount = computed(() => {
  return props.locationItems?.filter(item => item.type === 'high')?.length || 0
})

const formatLastUpdated = computed(() => {
  if (!props.lastUpdated) return ''
  try {
    return new Date(props.lastUpdated).toLocaleString('id-ID')
  } catch {
    return props.lastUpdated
  }
})

// Debounced functions for better performance
const debounce = (func, wait) => {
  let timeout
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout)
      func(...args)
    }
    clearTimeout(timeout)
    timeout = setTimeout(later, wait)
  }
}

// Lazy load Leaflet
const loadLeaflet = async () => {
  if (leafletLoaded) return
  
  try {
    const [leafletModule, markerClusterModule] = await Promise.all([
      import('leaflet'),
      import('leaflet.markercluster')
    ])
    
    L = leafletModule.default
    leafletLoaded = true
    
    // Import CSS dynamically
    if (typeof document !== 'undefined') {
      const links = [
        'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css',
        'https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css'
      ]
      
      links.forEach(href => {
        if (!document.querySelector(`link[href="${href}"]`)) {
          const link = document.createElement('link')
          link.rel = 'stylesheet'
          link.href = href
          document.head.appendChild(link)
        }
      })
    }
  } catch (error) {
    console.error('Failed to load Leaflet:', error)
    emit('map-error', error)
  }
}

// Map initialization with error handling
const initMap = async () => {
  if (!mapContainer.value || !L) return
  
  try {
    // Destroy existing map if any
    if (map) {
      map.remove()
      map = null
    }

    map = L.map(mapContainer.value, {
      center: [-2.5489, 118.0149],
      zoom: 5,
      zoomControl: false, // We have custom controls
      attributionControl: true,
      preferCanvas: true // Better performance for many markers
    })

    // Add tile layer with better performance settings
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19,
      minZoom: 3,
      updateWhenIdle: true,
      updateWhenZooming: false,
      keepBuffer: 2
    }).addTo(map)

    // Add scale control
    L.control.scale({ 
      imperial: false, 
      position: 'bottomleft' 
    }).addTo(map)

    // Initialize marker cluster with optimized settings
    clusterGroup = L.markerClusterGroup({
      maxClusterRadius: 50,
      disableClusteringAtZoom: 12,
      spiderfyOnMaxZoom: true,
      showCoverageOnHover: false,
      zoomToBoundsOnClick: true,
      removeOutsideVisibleBounds: true // Performance optimization
    })

    map.addLayer(clusterGroup)
    isMapReady.value = true
    
    // Initialize markers after map is ready
    await nextTick()
    initMarkers()
    
    emit('map-ready', map)
    
  } catch (error) {
    console.error('Map initialization failed:', error)
    emit('map-error', error)
  }
}

// Optimized marker creation with object pooling
const createMarkerIcon = (type) => {
  const iconConfigs = {
    high: { className: 'custom-marker high-risk', color: '#dc3545' },
    medium: { className: 'custom-marker medium-risk', color: '#fd7e14' },
    low: { className: 'custom-marker low-risk', color: '#198754' }
  }
  
  const config = iconConfigs[type] || iconConfigs.low
  
  return L.divIcon({
    className: config.className,
    html: `<i class="bi bi-geo-alt-fill" style="color: ${config.color}"></i>`,
    iconSize: [30, 30],
    iconAnchor: [15, 30],
    popupAnchor: [0, -30]
  })
}

// Optimized marker initialization
const initMarkers = () => {
  if (!clusterGroup || !isMapReady.value) return

  // Clear existing markers efficiently
  clusterGroup.clearLayers()
  markers.value.clear()

  if (!props.locationItems?.length) return

  // Batch marker creation for better performance
  const markersToAdd = []
  
  props.locationItems.forEach(location => {
    if (!location.coordinates || !Array.isArray(location.coordinates) || location.coordinates.length !== 2) {
      console.warn('Invalid coordinates for location:', location.id)
      return
    }

    const marker = L.marker(location.coordinates, {
      icon: createMarkerIcon(location.type),
      riseOnHover: true,
      title: location.name
    })

    // Optimized popup content tanpa tombol "Lihat Detail"
    const popupContent = `
      <div class="map-popup">
        <h6 class="mb-2">${location.name}</h6>
        <div class="popup-stats">
          <span class="badge bg-${location.type === 'high' ? 'danger' : location.type === 'medium' ? 'warning' : 'success'} mb-2">
            ${location.reports} laporan
          </span>
        </div>
        <p class="mb-2 small text-muted">Terakhir: ${location.lastReport}</p>
      </div>
    `

    marker.bindPopup(popupContent, {
      maxWidth: 250,
      className: 'custom-popup'
    })

    markersToAdd.push(marker)
    markers.value.set(location.id, marker)
  })

  // Add all markers at once for better performance
  clusterGroup.addLayers(markersToAdd)
}

// Debounced marker updates
const debouncedInitMarkers = debounce(initMarkers, 150)

// Watch for location changes with deep comparison optimization
watch(
  () => props.locationItems,
  (newItems, oldItems) => {
    if (!isMapReady.value) return
    
    // Only update if data actually changed
    if (JSON.stringify(newItems) !== JSON.stringify(oldItems)) {
      debouncedInitMarkers()
    }
  },
  { deep: true }
)

// Map control methods
const zoomIn = () => map?.zoomIn()
const zoomOut = () => map?.zoomOut()
const resetView = () => {
  if (map) {
    map.setView([-2.5489, 118.0149], 5)
  }
}

// Public methods
const focusLocation = (location) => {
  if (!map || !location?.coordinates || !isMapReady.value) return
  
  map.setView(location.coordinates, 12)
  const marker = markers.value.get(location.id)
  if (marker) {
    marker.openPopup()
  }
}

const showAllLocations = (locations) => {
  if (!map || !isMapReady.value) return
  
  const bounds = locations
    ?.map(loc => loc.coordinates)
    ?.filter(coord => coord && Array.isArray(coord) && coord.length === 2)
  
  if (bounds?.length) {
    map.fitBounds(bounds, { padding: [20, 20] })
  }
}

// Lifecycle hooks
onMounted(async () => {
  await loadLeaflet()
  if (L) {
    await initMap()
  }
})

onUnmounted(() => {
  if (map) {
    map.remove()
    map = null
  }
  clusterGroup = null
  markers.value.clear()
  isMapReady.value = false
})

// Expose public methods
defineExpose({
  focusLocation,
  showAllLocations,
  isMapReady: () => isMapReady.value
})
</script>

<style scoped>
.map {
  height: 500px;
  width: 100%;
  z-index: 1;
  overflow: hidden;
  cursor: grab;
  border-radius: 0;
  transition: opacity 0.3s ease;
}

.map-loading-state {
  opacity: 0.7;
  pointer-events: none;
}

.map-loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.9);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
  backdrop-filter: blur(2px);
}

.legend-item {
  display: flex;
  align-items: center;
  margin-bottom: 0.5rem;
}

.legend-marker {
  width: 12px;
  height: 12px;
  border-radius: 50%;
  margin-right: 0.5rem;
  display: inline-block;
}

.legend-marker.high-risk {
  background-color: #dc3545;
}

.legend-marker.medium-risk {
  background-color: #fd7e14;
}

.legend-marker.low-risk {
  background-color: #198754;
}

/* Transitions */
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

.slide-up-enter-active {
  transition: all 0.4s ease-out;
}

.slide-up-enter-from {
  opacity: 0;
  transform: translateY(20px);
}

/* Global styles for Leaflet components */
:global(.map-popup) {
  text-align: center;
  padding: 8px;
  min-width: 200px;
}

:global(.map-popup .popup-stats) {
  margin-bottom: 0.5rem;
}

:global(.custom-popup .leaflet-popup-content) {
  margin: 8px 12px;
  line-height: 1.4;
}

:global(.custom-marker) {
  background: transparent;
  border: none;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
}

:global(.leaflet-cluster-anim .leaflet-marker-icon, .leaflet-cluster-anim .leaflet-marker-shadow) {
  transition: transform 0.3s ease-out, opacity 0.3s ease-in;
}

/* Responsive Design */
@media (max-width: 1200px) {
  .map { height: 450px; }
}

@media (max-width: 992px) {
  .map { height: 400px; }
  .card-header h3 { font-size: 1.1rem; }
}

@media (max-width: 768px) {
  .map { height: 350px; }
  .card-header h3 { font-size: 1rem; }
  .btn-sm {
    font-size: 0.8rem;
    padding: 4px 8px;
  }
}

@media (max-width: 576px) {
  .map { height: 300px; }
  .card-header {
    flex-direction: column;
    gap: 0.5rem;
  }
  .card-header h3 { 
    font-size: 0.9rem;
    text-align: center;
  }
}

@media (max-width: 320px) {
  .map { height: 250px; }
}

/* Print styles */
@media print {
  .map {
    height: 300px;
    background: #f8f9fa;
  }
  
  .map-loading,
  .card-header .btn {
    display: none;
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .legend-marker {
    border: 2px solid currentColor;
  }
}

/* Reduced motion */
@media (prefers-reduced-motion: reduce) {
  .map,
  .fade-enter-active,
  .fade-leave-active,
  .slide-up-enter-active {
    transition: none;
  }
  
  :global(.leaflet-cluster-anim .leaflet-marker-icon, .leaflet-cluster-anim .leaflet-marker-shadow) {
    transition: none;
  }
}
</style>