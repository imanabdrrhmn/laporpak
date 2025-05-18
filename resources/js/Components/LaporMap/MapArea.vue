<template>
  <div class="card shadow-lg">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h3 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Peta Interaktif Laporan Penipuan</h3>
      <div class="d-flex">
        <button class="btn btn-light btn-sm me-2" @click="zoomIn" aria-label="Zoom In">
          <i class="bi bi-zoom-in"></i>
        </button>
        <button class="btn btn-light btn-sm me-2" @click="zoomOut" aria-label="Zoom Out">
          <i class="bi bi-zoom-out"></i>
        </button>
        <button class="btn btn-light btn-sm" @click="resetView" aria-label="Reset View">
          <i class="bi bi-arrow-repeat"></i>
        </button>
      </div>
    </div>
    <div class="card-body p-0 position-relative">
      <div id="map" ref="mapContainer" class="map"></div>
      <div class="map-loading" v-if="loading">
        <div class="spinner-grow text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>

  <div class="alert alert-info mt-3 shadow-sm" role="alert">
    <div class="d-flex">
      <div class="me-3">
        <i class="bi bi-info-circle-fill fs-4"></i>
      </div>
      <div>
        <h5 class="alert-heading">Peta Terupdate!</h5>
        <p class="mb-0">Data pada peta ini diperbarui secara real-time berdasarkan laporan pengguna. Terakhir diperbarui pada {{ lastUpdated }}.</p>
      </div>
    </div>
  </div>

  <div class="card mt-3 shadow-sm">
    <div class="card-body">
      <h5 class="card-title"><i class="bi bi-question-circle-fill me-2"></i>Apa itu LaporMap?</h5>
      <p class="card-text">
        LaporMap adalah platform interaktif yang memungkinkan masyarakat untuk melaporkan dan memantau kasus penipuan di berbagai lokasi. Dengan peta real-time, pengguna dapat melihat zona risiko penipuan, detail laporan, dan berkontribusi untuk menciptakan komunitas yang lebih aman.
      </p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import 'leaflet.markercluster/dist/leaflet.markercluster.js';
import 'leaflet.markercluster/dist/MarkerCluster.Default.css';

const props = defineProps({
  loading: Boolean,
  lastUpdated: String,
  locationItems: Array
});

const emit = defineEmits(['open-location-detail']);

const mapContainer = ref(null);
let map = null;
let clusterGroup = null;
const markers = ref([]);

onMounted(() => {
  initMap();
  watchLocationItems(props.locationItems);
});

onUnmounted(() => {
  if (map) map.remove();
});

const initMap = () => {
  if (!mapContainer.value) return;

  map = L.map(mapContainer.value).setView([-2.5489, 118.0149], 5);
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors',
    maxZoom: 19
  }).addTo(map);

  L.control.scale({ imperial: false }).addTo(map);

  clusterGroup = L.markerClusterGroup({
    maxClusterRadius: 50,
    disableClusteringAtZoom: 12
  });

  map.addLayer(clusterGroup);
  initMarkers();
};

const watchLocationItems = (items) => {
  watch(
    () => props.locationItems,
    () => {
      if (map && clusterGroup) initMarkers();
    },
    { deep: true }
  );
};

const initMarkers = () => {
  markers.value = [];
  if (!clusterGroup) return;
  clusterGroup.clearLayers();

  const icons = {
    high: L.divIcon({
      className: 'custom-marker high-risk',
      html: '<i class="bi bi-geo-alt-fill"></i>',
      iconSize: [30, 30],
      iconAnchor: [15, 30]
    }),
    medium: L.divIcon({
      className: 'custom-marker medium-risk',
      html: '<i class="bi bi-geo-alt-fill"></i>',
      iconSize: [30, 30],
      iconAnchor: [15, 30]
    }),
    low: L.divIcon({
      className: 'custom-marker low-risk',
      html: '<i class="bi bi-geo-alt-fill"></i>',
      iconSize: [30, 30],
      iconAnchor: [15, 30]
    })
  };

  props.locationItems.forEach(location => {
    if (!location.coordinates || !Array.isArray(location.coordinates)) return;

    const marker = L.marker(location.coordinates, {
      icon: icons[location.type] || icons.low
    }).bindPopup(`
      <div class="map-popup">
        <h6>${location.name}</h6>
        <p class="mb-1"><strong>${location.reports}</strong> laporan penipuan</p>
        <p class="mb-2">Laporan terakhir: ${location.lastReport}</p>
        <button class="btn btn-sm btn-primary w-100 view-details" data-id="${location.id}">
          Lihat Detail
        </button>
      </div>
    `);

    marker.on('popupopen', () => {
      setTimeout(() => {
        const button = document.querySelector(`.view-details[data-id="${location.id}"]`);
        if (button) {
          button.addEventListener('click', () => {
            emit('open-location-detail', location);
          });
        }
      }, 100);
    });

    clusterGroup.addLayer(marker);
    markers.value.push({ id: location.id, marker });
  });
};

const zoomIn = () => map?.zoomIn();
const zoomOut = () => map?.zoomOut();
const resetView = () => map?.setView([-2.5489, 118.0149], 5);

const focusLocation = (location) => {
  if (!map || !location?.coordinates) return;
  map.setView(location.coordinates, 12);
  const markerObj = markers.value.find(m => m.id === location.id);
  if (markerObj) markerObj.marker.openPopup();
};

const showAllLocations = (locations) => {
  const bounds = locations.map(loc => loc.coordinates).filter(Boolean);
  if (bounds.length > 0) map?.fitBounds(bounds);
};

defineExpose({
  focusLocation,
  showAllLocations
});
</script>

<style scoped>
.map {
  height: 500px;
  width: 100%;
  z-index: 1;
  overflow: hidden;
  cursor: grab;
}

.map-loading {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(255, 255, 255, 0.7);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000;
}

:global(.map-popup) {
  text-align: center;
  padding: 5px;
}

.alert {
  animation: slideIn 0.5s ease-out;
}

@keyframes slideIn {
  0% { opacity: 0; transform: translateY(20px); }
  100% { opacity: 1; transform: translateY(0); }
}

@media (max-width: 1200px) {
  .map { height: 450px; }
}

@media (max-width: 992px) {
  .map { height: 400px; }
}

@media (max-width: 768px) {
  .map { height: 350px; }
  .card-header h5 { font-size: 1rem; }
}

@media (max-width: 576px) {
  .map { height: 300px; }
  .card-header h5 { font-size: 0.9rem; }
  .btn-sm {
    font-size: 0.8rem;
    padding: 5px 10px;
  }
}

@media (max-width: 320px) {
  .map { height: 250px; }
}
</style>
