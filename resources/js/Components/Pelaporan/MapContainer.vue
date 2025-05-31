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
      :disabled="isLocationLoading"
    >
      <i class="bi bi-geo-fill me-1"></i> 
      {{ isLocationLoading ? 'Mencari...' : 'Lokasi Saya' }}
    </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch, computed, nextTick, shallowRef } from 'vue';

// Lazy loading Leaflet
let L = null;
const loadLeaflet = async () => {
  if (!L) {
    const leafletModule = await import('leaflet');
    L = leafletModule.default;
    // Load CSS dinamis
    if (!document.querySelector('link[href*="leaflet"]')) {
      const link = document.createElement('link');
      link.rel = 'stylesheet';
      link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
      document.head.appendChild(link);
    }
  }
  return L;
};

const props = defineProps({
  formData: Object,
  validationErrors: Object,
  selectedService: String,
  isFetchingLocation: Boolean
});

const emit = defineEmits(['update:location', 'update:address', 'update:validation-errors', 'error']);

// Gunakan shallowRef untuk objek yang tidak perlu deep reactivity
const mapRef = ref(null);
const map = shallowRef(null);
const marker = shallowRef(null);
const isFetchingAddress = ref(false);
const isFetchingLocationInternal = ref(false);

// Computed untuk menggabungkan loading states
const isLocationLoading = computed(() => 
  props.isFetchingLocation || isFetchingLocationInternal.value
);

// Cache untuk reverse geocoding
const geocodeCache = new Map();
const CACHE_DURATION = 5 * 60 * 1000; // 5 menit

// Optimized debounce dengan cleanup
let debounceTimeout = null;
const createDebounce = (func, wait) => {
  return function executedFunction(...args) {
    const later = () => {
      debounceTimeout = null;
      func(...args);
    };
    if (debounceTimeout) {
      clearTimeout(debounceTimeout);
    }
    debounceTimeout = setTimeout(later, wait);
  };
};

const debouncedResizeHandler = createDebounce(handleMapResize, 250);
const debouncedReverseGeocode = createDebounce(reverseGeocode, 500);

// Intersection Observer untuk lazy loading
let intersectionObserver = null;

onMounted(async () => {
  // Lazy loading dengan intersection observer
  intersectionObserver = new IntersectionObserver(
    async (entries) => {
      const [entry] = entries;
      if (entry.isIntersecting) {
        await nextTick();
        setTimeout(async () => {
          await initMap();
        }, 100); // Reduced dari 300ms
        intersectionObserver.disconnect();
      }
    },
    { rootMargin: '50px' }
  );
  
  if (mapRef.value) {
    intersectionObserver.observe(mapRef.value);
  }
  
  window.addEventListener('resize', debouncedResizeHandler, { passive: true });
});

onUnmounted(() => {
  // Cleanup yang lebih thorough
  if (debounceTimeout) {
    clearTimeout(debounceTimeout);
  }
  
  window.removeEventListener('resize', debouncedResizeHandler);
  
  if (intersectionObserver) {
    intersectionObserver.disconnect();
  }
  
  if (map.value) {
    map.value.off(); // Remove semua event listeners
    map.value.remove();
    map.value = null;
  }
  
  // Clear cache
  geocodeCache.clear();
});

async function initMap() {
  if (!mapRef.value || map.value) return;
  
  try {
    await loadLeaflet();
    
    const defaultPosition = [-6.2, 106.8];
    
    map.value = L.map(mapRef.value, {
      zoomControl: true,
      attributionControl: true,
      preferCanvas: true, // Untuk performa yang lebih baik
      renderer: L.canvas(), // Canvas renderer lebih cepat untuk banyak marker
    }).setView(defaultPosition, 12);

    // Gunakan tile layer yang lebih ringan
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 19,
      tileSize: 256,
      zoomOffset: 0,
      crossOrigin: true
    }).addTo(map.value);

    // Throttled click handler
    let clickTimeout = null;
    map.value.on('click', (e) => {
      if (clickTimeout) {
        clearTimeout(clickTimeout);
      }
      clickTimeout = setTimeout(() => {
        placeMarker(e.latlng);
        emit('update:validation-errors', { ...props.validationErrors, location: false });
      }, 100);
    });

    // Delayed resize untuk memastikan DOM sudah ready
    await nextTick();
    setTimeout(() => {
      if (map.value) {
        map.value.invalidateSize();
      }
    }, 200);
    
  } catch (error) {
    console.error("Error initializing map:", error);
    emit('error', 'Gagal menginisialisasi peta');
  }
}

function handleMapResize() {
  if (map.value) {
    map.value.invalidateSize();
  }
}

// Optimized reverse geocoding dengan cache dan rate limiting
async function reverseGeocode(lat, lng) {
  const cacheKey = `${lat.toFixed(4)},${lng.toFixed(4)}`;
  const cached = geocodeCache.get(cacheKey);
  
  // Check cache dengan expiry
  if (cached && (Date.now() - cached.timestamp < CACHE_DURATION)) {
    emit('update:address', cached.address);
    return cached.address;
  }
  
  isFetchingAddress.value = true;
  
  try {
    // AbortController untuk cancel request jika komponen di-unmount
    const controller = new AbortController();
    const timeoutId = setTimeout(() => controller.abort(), 8000);
    
    const response = await fetch(
      `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&limit=1`,
      { 
        signal: controller.signal,
        headers: {
          'Accept': 'application/json',
        }
      }
    );
    
    clearTimeout(timeoutId);
    
    if (!response.ok) {
      throw new Error(`HTTP ${response.status}`);
    }
    
    const data = await response.json();
    const address = data.display_name || 'Alamat tidak ditemukan';
    
    // Cache result
    geocodeCache.set(cacheKey, {
      address,
      timestamp: Date.now()
    });
    
    emit('update:address', address);
    return address;
    
  } catch (error) {
    if (error.name === 'AbortError') {
      console.log('Reverse geocoding dibatalkan');
      return 'Alamat tidak tersedia';
    }
    
    console.error('Error saat reverse geocoding:', error);
    emit('error', 'Gagal mendapatkan alamat');
    return 'Alamat tidak tersedia';
    
  } finally {
    isFetchingAddress.value = false;
  }
}

function placeMarker(latlng) {
  if (!map.value) return;
  
  try {
    // Remove existing marker
    if (marker.value) {
      map.value.removeLayer(marker.value);
    }
    
    // Optimized icon dengan caching
    const customIcon = L.icon({
      iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
      shadowSize: [41, 41]
    });

    marker.value = L.marker(latlng, { 
      icon: customIcon,
      riseOnHover: true // Performance optimization
    }).addTo(map.value);

    const popupContent = props.selectedService === 'Penipuan'
      ? '<b>Lokasi Pelaporan</b>'
      : '<b>Lokasi Kerusakan Infrastruktur</b>';

    marker.value.bindPopup(popupContent, {
      closeOnClick: false,
      autoClose: false
    }).openPopup();

    emit('update:location', { lat: latlng.lat, lng: latlng.lng });

    // Debounced reverse geocoding
    debouncedReverseGeocode(latlng.lat, latlng.lng);
    
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
  
  if (isFetchingLocationInternal.value) return; // Prevent multiple calls
  
  try {
    isFetchingLocationInternal.value = true;
    
    const position = await new Promise((resolve, reject) => {
      const timeoutId = setTimeout(() => {
        reject(new Error('Timeout: Gagal mendapatkan lokasi dalam 15 detik'));
      }, 15000);
      
      navigator.geolocation.getCurrentPosition(
        (pos) => {
          clearTimeout(timeoutId);
          resolve(pos);
        },
        (err) => {
          clearTimeout(timeoutId);
          reject(err);
        },
        {
          enableHighAccuracy: true,
          timeout: 12000,
          maximumAge: 60000 // Cache selama 1 menit
        }
      );
    });

    const { latitude, longitude } = position.coords;
    
    if (map.value) {
      map.value.setView([latitude, longitude], 16);
      placeMarker({ lat: latitude, lng: longitude });
      emit('update:validation-errors', { ...props.validationErrors, location: false });
    }
    
  } catch (error) {
    const errorMessage = error.code === 1 
      ? 'Akses lokasi ditolak. Mohon izinkan akses lokasi.'
      : error.code === 2 
      ? 'Lokasi tidak tersedia. Periksa GPS atau koneksi internet.'
      : error.code === 3 
      ? 'Timeout saat mendapatkan lokasi.'
      : `Gagal mendapatkan lokasi: ${error.message}`;
      
    emit('error', errorMessage);
  } finally {
    isFetchingLocationInternal.value = false;
  }
}

// Optimized watcher dengan immediate: false
watch(
  () => props.selectedService,
  (newVal) => {
    if (marker.value && map.value) {
      const newContent = newVal === 'Penipuan'
        ? '<b>Lokasi Pelaporan</b>'
        : '<b>Lokasi Kerusakan Infrastruktur</b>';
      marker.value.setPopupContent(newContent);
      if (marker.value.isPopupOpen()) {
        marker.value.openPopup();
      }
    }
  },
  { immediate: false }
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
  /* GPU acceleration untuk smooth animations */
  transform: translateZ(0);
  will-change: transform;
}

.is-loading {
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' style='margin:auto;background:none;display:block;' width='20px' height='20px' viewBox='0 0 100 100' preserveAspectRatio='xMidYMid'%3e%3ccircle cx='50' cy='50' r='32' stroke-width='8' stroke='%236c757d' stroke-dasharray='50.26548245743669 50.26548245743669' fill='none' stroke-linecap='round'%3e%3canimateTransform attributeName='transform' type='rotate' repeatCount='indefinite' dur='1s' values='0 50 50;360 50 50' keyTimes='0;1'%3e%3c/animateTransform%3e%3c/circle%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 1rem 1rem;
}

/* Optimized media queries dengan container queries jika didukung */
@container (min-width: 600px) and (max-width: 1199px) {
  .map-container {
    height: 280px;
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

/* Preload critical resources */
@media (prefers-reduced-motion: no-preference) {
  .map-container {
    transition: height 0.2s ease;
  }
}
</style>