<template>
  <div class="col-12">
    <div class="mb-2">
      <span class="form-label fw-semibold mb-0">
        <i class="bi bi-geo-alt me-2"></i>Lokasi
      </span>
    </div>
    <div
      id="map"
      ref="mapRef"
      class="mb-2 rounded-2 border map-container"
      :class="{'border-danger': validationErrors.location}"
    ></div>
    <div class="mb-3">
      <label for="address" class="form-label mb-2">Alamat</label>
      <input
        id="address"
        v-model="formData.address"
        class="form-control"
        placeholder="Lokasi Anda"
        readonly
        :title="formData.address"
        aria-label="Alamat"
        :class="{ 'is-loading': isFetchingAddress }"
      />
    </div>
    <div class="d-flex justify-content-between align-items-center mb-3">
      <div v-if="validationErrors.location" class="text-danger" aria-live="polite">
        Lokasi harus dipilih pada peta
      </div>
     <button
  type="button"
  class="btn btn-outline-primary w-100 py-2 px-3"
  @click="goToCurrentLocation"
  :disabled="isFetchingLocation || isFetchingLocationInternal"
>
  <i class="bi bi-geo-fill me-1"></i> Lokasi Saya
</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';

const props = defineProps({
  formData: Object,
  validationErrors: Object,
  selectedService: String,
  isFetchingLocation: Boolean
});

const emit = defineEmits(['update:location', 'update:address', 'update:validation-errors', 'error']);

const mapRef = ref(null);
const isFetchingAddress = ref(false);
const isFetchingLocationInternal = ref(false);

let map = null;
let marker = null;

function debounce(func, wait) {
  let timeout;
  return function executedFunction(...args) {
    const later = () => {
      clearTimeout(timeout);
      func(...args);
    };
    clearTimeout(timeout);
    timeout = setTimeout(later, wait);
  };
}

const debouncedResizeHandler = debounce(handleMapResize, 200);

onMounted(() => {
  setTimeout(() => {
    initMap();
    window.addEventListener('resize', debouncedResizeHandler);
  }, 300);
});

onUnmounted(() => {
  window.removeEventListener('resize', debouncedResizeHandler);
  if (map) {
    map.remove();
  }
});

function initMap() {
  const defaultPosition = [-6.2, 106.8];
  if (!mapRef.value) return;
  try {
    map = L.map(mapRef.value, {
      zoomControl: true,
      attributionControl: true
    }).setView(defaultPosition, 12);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution:
        '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 19
    }).addTo(map);

    map.on('click', (e) => {
      placeMarker(e.latlng);
      emit('update:validation-errors', { ...props.validationErrors, location: false });
    });

    setTimeout(() => {
      map.invalidateSize();
    }, 500);
  } catch (error) {
    console.error("Error initializing map:", error);
    emit('error', 'Gagal menginisialisasi peta');
  }
}

function handleMapResize() {
  if (map) {
    map.invalidateSize();
  }
}

async function reverseGeocode(lat, lng) {
  isFetchingAddress.value = true;
  try {
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`
    );
    if (!response.ok) {
      throw new Error('Gagal mendapatkan alamat');
    }
    const data = await response.json();
    return data.display_name || 'Alamat tidak ditemukan';
  } catch (error) {
    console.error('Error saat reverse geocoding:', error);
    emit('error', 'Gagal mendapatkan alamat');
    return 'Alamat tidak tersedia';
  } finally {
    isFetchingAddress.value = false;
  }
}

function placeMarker(latlng) {
  if (marker) {
    map.removeLayer(marker);
  }
  try {
    const customIcon = L.icon({
      iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
      shadowSize: [41, 41]
    });

    marker = L.marker(latlng, { icon: customIcon }).addTo(map);

    const popupContent =
      props.selectedService === 'Penipuan'
        ? '<b>Lokasi Pelaporan</b>'
        : '<b>Lokasi Kerusakan Infrastruktur</b>';

    marker.bindPopup(popupContent).openPopup();

    emit('update:location', { lat: latlng.lat, lng: latlng.lng });

    reverseGeocode(latlng.lat, latlng.lng).then((address) => {
      emit('update:address', address);
    });
  } catch (error) {
    console.error("Error placing marker:", error);
    emit('error', 'Gagal menempatkan marker');
  }
}

async function goToCurrentLocation() {
  if (!navigator.geolocation) {
    emit('error', 'Geolocation tidak didukung oleh browser Anda');
    return;
  }
  try {
    isFetchingLocationInternal.value = true;
    const position = await new Promise((resolve, reject) => {
      navigator.geolocation.getCurrentPosition(resolve, reject, {
        enableHighAccuracy: true,
        timeout: 10000,
        maximumAge: 0
      });
    });

    const { latitude, longitude } = position.coords;
    if (map) {
      map.setView([latitude, longitude], 16);
      placeMarker({ lat: latitude, lng: longitude });
      emit('update:validation-errors', { ...props.validationErrors, location: false });
    }
  } catch (error) {
    emit('error', 'Gagal mendapatkan lokasi Anda: ' + error.message);
  } finally {
    isFetchingLocationInternal.value = false;
  }
}

// Jika selectedService berubah, update popup-nya
watch(
  () => props.selectedService,
  (newVal) => {
    if (marker) {
      const newContent =
        newVal === 'Penipuan'
          ? '<b>Lokasi Pelaporan</b>'
          : '<b>Lokasi Kerusakan Infrastruktur</b>';
      marker.bindPopup(newContent).openPopup();
    }
  }
);
</script>


<style scoped>
.map-container {
  border-radius: 6px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  width: 100%;
  height: 250px;
  position: relative;
}

.is-loading {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' style='margin:auto;background:none;display:block;' width='20px' height='20px' viewBox='0 0 100 100' preserveAspectRatio='xMidYMid'%3e%3ccircle cx='50' cy='50' r='32' stroke-width='8' stroke='%236c757d' stroke-dasharray='50.26548245743669 50.26548245743669' fill='none' stroke-linecap='round'%3e%3canimateTransform attributeName='transform' type='rotate' repeatCount='indefinite' dur='1s' values='0 50 50;360 50 50' keyTimes='0;1'%3e%3c/animateTransform%3e%3c/circle%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 1rem 1rem;
}

.leaflet-default-icon-path {
  background-image: url("/assets/marker-icon.png");
}

@media (min-width: 600px) and (max-width: 1199px) {
  .map-container {
    height: 280px !important;
    width: 100% !important;
  }
}

@media (max-width: 320px) {
  .map-container {
    height: 200px;
  }
}

@media (min-width: 321px) and (max-width: 375px) {
  .map-container {
    height: 220px;
  }
}

@media (min-width: 376px) and (max-width: 576px) {
  .map-container {
    height: 220px;
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .map-container {
    height: 250px;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .map-container {
    height: 280px;
  }
}

@media (min-width: 992px) {
  .map-container {
    height: 300px;
  }
}

@media (min-width: 1200px) {
  .map-container {
    height: 320px;
  }
}

@media (min-width: 1400px) {
  .map-container {
    height: 350px;
  }
}

@media (max-height: 600px) and (orientation: landscape) {
  .map-container {
    height: 200px;
  }
}
</style>
