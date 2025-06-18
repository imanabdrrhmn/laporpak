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

// Leaflet variables - akan diisi setelah library dimuat
let L = null
let leafletLoaded = false

// Define emits and capture the emit function
const emit = defineEmits(['open-location-detail', 'map-error', 'map-ready'])

// Define component props
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

// Refs
const mapContainer = ref(null)
let map = null
let clusterGroup = null
const markers = ref(new Map())
const isMapReady = ref(false)

// Computed properties
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

// Debounce utility
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

// Load CSS files
const loadCSS = (href) => {
  return new Promise((resolve, reject) => {
    if (document.querySelector(`link[href="${href}"]`)) {
      resolve()
      return
    }
    
    const link = document.createElement('link')
    link.rel = 'stylesheet'
    link.href = href
    link.onload = () => resolve()
    link.onerror = () => reject(new Error(`Failed to load CSS: ${href}`))
    document.head.appendChild(link)
  })
}

// Improved Leaflet loading with proper dependency management
const loadLeaflet = async () => {
  if (leafletLoaded && L) return
  
  try {
    console.log('Loading Leaflet libraries...')
    
    // Load CSS files first
    await Promise.all([
      loadCSS('https://unpkg.com/leaflet@1.9.4/dist/leaflet.css'),
      loadCSS('https://unpkg.com/leaflet.markercluster@1.4.1/dist/MarkerCluster.Default.css')
    ])
    
    // Load Leaflet core first
    const leafletModule = await import('leaflet')
    L = leafletModule.default
    
    // Make sure L is available globally for markercluster
    window.L = L
    
    // Now load markercluster after L is defined
    await import('leaflet.markercluster')
    
    leafletLoaded = true
    console.log('Leaflet libraries loaded successfully')
    
  } catch (error) {
    console.error('Failed to load Leaflet:', error)
    emit('map-error', error)
    throw error
  }
}

// Map initialization
const initMap = async () => {
  if (!mapContainer.value || !L) {
    console.warn('Map container or Leaflet not ready')
    return
  }
  
  try {
    console.log('Initializing map...')
    
    // Destroy existing map if any
    if (map) {
      map.remove()
      map = null
    }

    // Create map instance
    map = L.map(mapContainer.value, {
      center: [-2.5489, 118.0149], // Indonesia center
      zoom: 5,
      zoomControl: false,
      attributionControl: false, // Disable default attribution for custom positioning
      preferCanvas: true
    })

    // Add tile layer
    const tileLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
      maxZoom: 19,
      minZoom: 3,
      updateWhenIdle: true,
      updateWhenZooming: false,
      keepBuffer: 2
    })
    
    tileLayer.addTo(map)

    // Add custom attribution control with responsive positioning
    L.control.attribution({
      position: 'bottomright',
      prefix: false
    }).addTo(map)

    // Add scale control
    L.control.scale({ 
      imperial: false, 
      position: 'bottomleft' 
    }).addTo(map)

    // Initialize marker cluster group
    if (L.markerClusterGroup) {
      clusterGroup = L.markerClusterGroup({
        maxClusterRadius: 50,
        disableClusteringAtZoom: 12,
        spiderfyOnMaxZoom: true,
        showCoverageOnHover: false,
        zoomToBoundsOnClick: true,
        removeOutsideVisibleBounds: true
      })
      
      map.addLayer(clusterGroup)
    } else {
      console.error('MarkerClusterGroup not available')
      emit('map-error', new Error('MarkerClusterGroup not available'))
      return
    }

    isMapReady.value = true
    console.log('Map initialized successfully')
    
    // Initialize markers after map is ready
    await nextTick()
    initMarkers()
    
    emit('map-ready', map)
    
  } catch (error) {
    console.error('Map initialization failed:', error)
    emit('map-error', error)
  }
}

// Create marker icons
const createMarkerIcon = (type) => {
  if (!L) return null
  
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

// Initialize markers
const initMarkers = () => {
  if (!clusterGroup || !isMapReady.value || !L) {
    console.warn('Cluster group, map, or Leaflet not ready for markers')
    return
  }

  console.log('Initializing markers...', props.locationItems?.length || 0, 'locations')

  // Clear existing markers
  clusterGroup.clearLayers()
  markers.value.clear()

  if (!props.locationItems?.length) return

  const markersToAdd = []
  
  props.locationItems.forEach(location => {
    if (!location.coordinates || !Array.isArray(location.coordinates) || location.coordinates.length !== 2) {
      console.warn('Invalid coordinates for location:', location.id)
      return
    }

    try {
      const marker = L.marker(location.coordinates, {
        icon: createMarkerIcon(location.type),
        riseOnHover: true,
        title: location.name
      })

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
    } catch (error) {
      console.error('Error creating marker for location:', location.id, error)
    }
  })

  // Add all markers at once
  if (markersToAdd.length) {
    clusterGroup.addLayers(markersToAdd)
    console.log('Added', markersToAdd.length, 'markers to map')
  }
}

// Debounced marker updates
const debouncedInitMarkers = debounce(initMarkers, 150)

// Watch for location changes
watch(
  () => props.locationItems,
  (newItems, oldItems) => {
    if (!isMapReady.value) return
    
    if (JSON.stringify(newItems) !== JSON.stringify(oldItems)) {
      debouncedInitMarkers()
    }
  },
  { deep: true }
)

// Map control methods
const zoomIn = () => {
  if (map) map.zoomIn()
}

const zoomOut = () => {
  if (map) map.zoomOut()
}

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
  try {
    await loadLeaflet()
    if (L) {
      await initMap()
    }
  } catch (error) {
    console.error('Failed to initialize map component:', error)
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
  
  // Clean up global L if we set it
  if (window.L && window.L === L) {
    delete window.L
  }
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

/* Fix untuk responsivitas kontrol Leaflet */
:global(.leaflet-control-attribution) {
  background: rgba(255, 255, 255, 0.9) !important;
  color: #333 !important;
  font-size: 10px !important;
  padding: 2px 5px !important;
  border-radius: 3px !important;
  margin: 0 !important;
  backdrop-filter: blur(5px);
}

:global(.leaflet-control-attribution a) {
  color: #0066cc !important;
  text-decoration: none !important;
}

/* Update scale control styles */
:global(.leaflet-control-scale) {
  background: rgba(255, 255, 255, 0.9) !important;
  border-radius: 3px !important;
  padding: 2px 5px !important;
  font-size: 10px !important;
  backdrop-filter: blur(5px);
  margin-bottom: 10px !important;
  margin-left: 10px !important;
  position: relative !important;
  z-index: 1000;
}

:global(.leaflet-control-scale-line) {
  border: 2px solid #777 !important;
  border-top: none !important;
  color: #333 !important;
  font-size: 10px !important;
  line-height: 1.1;
  padding: 2px 5px 1px !important;
  white-space: nowrap;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.9) !important;
  margin-top: 0 !important;
  box-sizing: content-box;
}

/* Responsive Design - Diperbaiki untuk mencegah tumpang tindih */
@media (max-width: 1200px) {
  .map { height: 450px; }
}

@media (max-width: 992px) {
  .map { height: 400px; }
  .card-header h3 { font-size: 1.1rem; }
  
  :global(.leaflet-control-attribution) {
    font-size: 9px !important;
    max-width: calc(100% - 100px) !important;
  }
}

@media (max-width: 768px) {
  .map { height: 350px; }
  .card-header {
    flex-direction: column;
    gap: 0.75rem;
    align-items: stretch;
  }
  .card-header h3 { 
    font-size: 1rem;
    text-align: center;
  }
  .card-header   {
    justify-content: center;
  }
  .btn-sm {
    font-size: 0.8rem;
    padding: 4px 8px;
  }
  
  /* Kontrol Leaflet untuk mobile */
  :global(.leaflet-control-attribution) {
    position: fixed !important;
    bottom: 5px !important;
    right: 5px !important;
    left: auto !important;
    font-size: 8px !important;
    max-width: calc(100vw - 120px) !important;
    padding: 1px 3px !important;
    z-index: 1000 !important;
  }
  
  :global(.leaflet-control-scale) {
    position: absolute !important;
    bottom: 25px !important;
    left: 10px !important;
    font-size: 8px !important;
    padding: 1px 3px !important;
    margin-bottom: 0 !important;
  }
  
  :global(.leaflet-control-scale-line) {
    border-width: 1px !important;
    padding: 2px 4px !important;
    min-width: 50px;
  }
}

@media (max-width: 576px) {
  .map { height: 300px; }
  .card-header h3 { 
    font-size: 0.9rem;
  }
  
  /* Lebih kompak untuk layar sangat kecil */
  :global(.leaflet-control-attribution) {
    font-size: 7px !important;
    max-width: calc(100vw - 100px) !important;
    right: 3px !important;
    bottom: 3px !important;
    padding: 1px 2px !important;
    line-height: 1.2 !important;
  }
  
  :global(.leaflet-control-scale) {
    bottom: 20px !important;
    left: 8px !important;
  }
  
  :global(.leaflet-control-scale-line) {
    font-size: 7px !important;
    padding: 1px 2px !important;
    min-width: 40px;
  }
}

@media (max-width: 400px) {
  .map { height: 280px; }
  
  /* Sembunyikan kontrol pada layar sangat kecil untuk menghindari tumpang tindih */
  :global(.leaflet-control-attribution) {
    display: none !important;
  }
  
  :global(.leaflet-control-scale) {
    display: block !important;
    bottom: 15px !important;
    left: 5px !important;
  }
  
  :global(.leaflet-control-scale-line) {
    font-size: 7px !important;
    padding: 1px 2px !important;
    min-width: 40px;
  }
}

@media (max-width: 320px) {
  .map { height: 250px; }
  .card-header h3 {
    font-size: 0.8rem;
  }
  .btn-sm {
    font-size: 0.7rem;
    padding: 3px 6px;
  }
}

/* Orientasi landscape pada mobile */
@media (max-height: 500px) and (orientation: landscape) {
  .map { height: 250px; }
  
  :global(.leaflet-control-attribution),
  :global(.leaflet-control-scale) {
    font-size: 7px !important;
  }
}

/* Print styles */
@media print {
  .map {
    height: 300px;
    background: #f8f9fa;
  }
  
  .map-loading,
  .card-header .btn,
  :global(.leaflet-control-attribution),
  :global(.leaflet-control-scale) {
    display: none !important;
  }
}

/* High contrast mode */
@media (prefers-contrast: high) {
  .legend-marker {
    border: 2px solid currentColor;
  }
  
  :global(.leaflet-control-attribution),
  :global(.leaflet-control-scale) {
    background: #fff !important;
    color: #000 !important;
    border: 1px solid #000 !important;
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

/* Perbaikan untuk teks yang overlap */
:global(.leaflet-container) {
  font-family: inherit;
}

:global(.leaflet-control-container) {
  pointer-events: none;
}

:global(.leaflet-control) {
  pointer-events: auto;
}

/* Pastikan kontrol tidak saling menimpa */
:global(.leaflet-bottom.leaflet-left) {
  bottom: 0;
  left: 0;
}

:global(.leaflet-bottom.leaflet-right) {
  bottom: 0;
  right: 0;
}
</style>