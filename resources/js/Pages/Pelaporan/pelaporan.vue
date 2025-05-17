<template>
  <AppLayout>
    <Head title="Pelaporan"/>
    <section class="hero-section py-4">
      <div class="container-fluid px-0">
        <div class="row g-0">
          <!-- Title + Description on Left Side - DYNAMIC CONTENT -->
          <div class="col-lg-6 d-flex flex-column justify-content text-white hero-content p-4 p-sm-5 ms-auto">
            <div class="text-section"> 
              <h1 class="display-4 fw-bold mb-3 d-flex align-items-center responsive-title">
                <i :class="serviceInfo[selectedService].titleIcon + ' me-2'"></i>
                <span>{{ serviceInfo[selectedService].title }}</span>
              </h1>
              <div class="highlight-bar mb-4"></div>
              <p class="lead mb-4">
                {{ serviceInfo[selectedService].description }}
              </p>
              <!-- Service Info Cards -->
              <div class="d-none d-md-block">
                <div class="row mt-4 g-3">
                  <div class="col-md-4">
                    <div class="feature-box p-3">
                      <i class="bi bi-shield-check fs-2 mb-2"></i>
                      <h5>Keamanan</h5>
                      <p class="small">Data terenkripsi dan terlindungi dengan baik</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="feature-box p-3">
                      <i class="bi bi-lightning fs-2 mb-2"></i>
                      <h5>Kecepatan</h5>
                      <p class="small">Hasil verifikasi instan dalam hitungan detik</p>
                    </div>
                  </div>
                  <div class="col-md-4">
                    <div class="feature-box p-3">
                      <i class="bi bi-graph-up fs-2 mb-2"></i>
                      <h5>Akurasi</h5>
                      <p class="small">Data terverifikasi dengan tingkat akurasi tinggi</p>
                    </div>
                  </div>
                </div>
              </div>
              <!-- Features for mobile -->
              <div class="d-block d-md-none">
                <div class="row g-2">
                  <div class="col-4 text-center">
                    <div class="mobile-feature p-2">
                      <i class="bi bi-shield-check fs-4"></i>
                      <div class="small mt-1">Keamanan</div>
                    </div>
                  </div>
                  <div class="col-4 text-center">
                    <div class="mobile-feature p-2">
                      <i class="bi bi-lightning fs-4"></i>
                      <div class="small mt-1">Kecepatan</div>
                    </div>
                  </div>
                  <div class="col-4 text-center">
                    <div class="mobile-feature p-2">
                      <i class="bi bi-graph-up fs-4"></i>
                      <div class="small mt-1">Akurasi</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Form on Right Side -->
          <div class="col-lg-6 d-flex align-items-center justify-content-end p-3 p-md-2 bg-light">
            <div class="form-container p-3 p-sm-4 p-lg-5 w-100">
              <h5 class="text-start fw-bold mb-3">
                <i class="bi bi-exclamation-triangle text-warning me-2"></i>
                {{ serviceInfo[selectedService].formTitle }}
              </h5>
              <!-- Service Selection with Tab-like Buttons -->
              <div class="service-tabs mb-4">
                <div class="d-flex gap-2 flex-wrap">
                  <button
                    v-for="service in services"
                    :key="service.value"
                    type="button"
                    class="btn flex-fill position-relative service-btn py-2"
                    :class="selectedService === service.value ? 'btn-primary text-white' : 'btn-outline-secondary'"
                    @click="selectService(service.value)"
                  >
                    <i :class="service.icon + ' me-2'"></i>
                    {{ service.label }}
                    <span v-if="selectedService === service.value" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
                      <span class="visually-hidden">Selected</span>
                    </span>
                  </button>
                </div>
              </div>
              <form @submit.prevent="handleSubmitReport" ref="formRef">
                <div class="row g-3">
                  <!-- Category -->
                  <div class="col-12">
                    <label for="category" class="form-label mb-2">Kategori</label>
                    <select 
                      id="category"
                      v-model="formData.category"
                      class="form-select custom-select"
                      required
                      :class="{'is-invalid': validationErrors.category}"
                      @change="validationErrors.category = false"
                      aria-label="Kategori"
                    >
                      <option disabled value="">Pilih Kategori</option>
                      <option 
                        v-for="category in currentCategories"
                        :key="category.value"
                        :value="category.value"
                      >
                        {{ category.label }}
                      </option>
                    </select>
                    <div v-if="validationErrors.category" class="invalid-feedback">
                      Kategori harus dipilih
                    </div>
                  </div>
                  <!-- Fraud Source -->
                  <div class="col-12" v-if="selectedService === 'Penipuan'">
                    <label for="source" class="form-label mb-2">Sumber Penipuan</label>
                    <input
                      id="source"
                      v-model="formData.source"
                      type="text"
                      class="form-control"
                      :class="{'is-invalid': validationErrors.source}"
                      placeholder="Contoh: telepon, WhatsApp, dll"
                      maxlength="255"
                      @input="validationErrors.source = false"
                      aria-label="Sumber Penipuan"
                      required
                    />
                    <div v-if="validationErrors.source" class="invalid-feedback">
                      Sumber penipuan harus diisi
                    </div>
                  </div>
                  <!-- Evidence -->
                  <div class="col-12">
                    <label for="evidence" class="form-label mb-2">Bukti</label>
                    <div class="input-group custom-file-input">
                      <input
                        id="evidence"
                        type="file"
                        class="form-control"
                        accept="image/*,.pdf"
                        @change="handleFileUpload"
                        aria-label="Bukti"
                      />
                      <span class="input-group-text py-0 px-2">
                        <i class="bi bi-image text-primary"></i>
                      </span>
                    </div>
                    <div class="form-text">
                      Format: JPEG, PNG, PDF (Max: 5MB)
                    </div>
                  </div>
                  <!-- Incident Description -->
                  <div class="col-12">
                    <label for="description" class="form-label mb-2">Deskripsi Kejadian</label>
                    <textarea
                      id="description"
                      v-model="formData.description"
                      class="form-control custom-textarea"
                      :class="{'is-invalid': validationErrors.description}"
                      :placeholder="serviceInfo[selectedService].descriptionPlaceholder"
                      rows="4"
                      required
                      maxlength="1500"
                      @input="validateDescription"
                      @focus="validationErrors.description = false"
                      aria-label="Deskripsi"
                    ></textarea>
                    <div class="form-text d-flex justify-content-between">
                      <span v-if="validationErrors.description" class="text-danger">
                        Deskripsi harus diisi
                      </span>
                      <span :class="{'text-danger': formData.description.length > 1500}">
                        {{ formData.description.length }}/1500
                      </span>
                    </div>
                  </div>
                  <!-- Location & OpenStreetMap -->
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
                        aria-label="Alamat"
                      />
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <div v-if="validationErrors.location" class="text-danger">
                        Lokasi harus dipilih pada peta
                      </div>
                      <button type="button" class="btn btn-outline-primary w-100 py-2 px-3" @click="getCurrentLocation">
                        <i class="bi bi-geo-fill me-1"></i> Lokasi Saya
                      </button>
                    </div>
                  </div>
                  <!-- Submit Button -->
                  <div class="col-12">
                    <div class="d-grid">
                      <button 
                        type="submit" 
                        class="btn btn-primary position-relative overflow-hidden submit-btn py-3"
                        :disabled="!isFormValid"
                      >
                        <span class="btn-animation"></span>
                        <i class="bi bi-send me-2"></i> Kirim Laporan
                      </button>
                    </div>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </section>
    <Alur/>
    <!-- SECTION COMPONENT -->
    <Section />
    <!-- FEEDBACK COMPONENT -->
    <Feedback :feedbacks="feedbacks" /> 
    <SuccessModal
      :show="showSuccessModal"
      @close="showSuccessModal = false"
    />
    <loginModal v-model:visible="showLoginModal" :is-from-report="true"></loginModal>
    <RegisterModal v-model:visible="showRegisterModal" :is-from-report="true" />
  </AppLayout>
</template>

<script setup>
import { ref, computed, reactive, watch, onMounted } from 'vue';
import { Head, usePage, router as Inertia } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alur from '@/Components/alurpelaporan.vue';
import Section from '@/Components/Section.vue';
import Feedback from '@/Components/Feedback.vue';
import SuccessModal from '@/Pages/Pelaporan/SuccessModal.vue';
import loginModal from '@/Components/modals/LoginModal.vue';
import RegisterModal from '@/Components/modals/RegisterModal.vue';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';

const page = usePage();
const feedbacks = page.props.feedbacks;
const showSuccessModal = ref(false);
const showLoginModal = ref(false);
const showRegisterModal = ref(false);

const userIsLoggedIn = computed(() => !!page.props.auth.user);

function openRegisterFromLogin() {
  showLoginModal.value = false;
  showRegisterModal.value = true;
}

// Service options with icons
const services = [
  { label: 'Pelaporan', value: 'Penipuan', icon: 'bi bi-shield-exclamation' },
  { label: 'Infrastruktur', value: 'Infrastruktur', icon: 'bi bi-building-gear' }
];

// Categories for each service type
const fraudCategories = [
  { label: 'Nomor Telepon', value: 'Nomor Hp' },
  { label: 'Email', value: 'Email' } 
];

const infrastructureCategories = [
  { label: 'Jalan Rusak', value: 'Jalan' },
  { label: 'Lampu Penerangan', value: 'Lampu' },
  { label: 'Saluran Air', value: 'Saluran Air' },
  { label: 'Fasilitas Umum', value: 'Fasilitas Umum' },
  { label: 'Lainnya', value: 'Lainnya' }
];

// Service information
const serviceInfo = {
  Penipuan: {
    badge: 'Platform Pelaporan',
    title: 'Pelaporan Akun',
    titleIcon: 'bi bi-exclamation-triangle-fill',
    description: 'Laporkan nomor HP, email, atau akun yang terindikasi mencurigakan untuk verifikasi lebih lanjut.',
    icon: 'bi bi-shield-check',
    formTitle: 'Form Pelaporan',
    descriptionPlaceholder: 'Ceritakan bagaimana kejadian yang mencurigakan terjadi dan modus yang digunakan...',

  },
  Infrastruktur: {
    badge: 'Lapor Infrastruktur',
    title: 'Pelaporan Infrastruktur',
    titleIcon: 'bi bi-wrench',
    description: 'Laporkan fasilitas umum dan infrastruktur yang rusak untuk perbaikan yang lebih cepat.',
    icon: 'bi bi-building-gear',
    formTitle: 'Form Infrastruktur',
    descriptionPlaceholder: 'Jelaskan detail kerusakan dan dampaknya terhadap lingkungan...',
 
  }
};

// Reactive data
const selectedService = ref('Penipuan');
const formData = ref({
  category: '',
  description: '',
  evidence: null,
  location: null,
  address: '',
  source: '',
});
const formRef = ref(null);
const mapRef = ref(null);

// Watch for service changes to reset form values
watch(selectedService, () => {
  formData.value.category = '';
  formData.value.description = '';
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false;
  });
});

// Validation errors tracker
const validationErrors = reactive({
  category: false,
  description: false,
  source: false,
  location: false
});

// Improved form validation
const isFormValid = computed(() => {
  return formData.value.category && 
         formData.value.category.trim() !== '' &&
         formData.value.description &&
         formData.value.description.trim() !== '' &&
         formData.value.description.length <= 1500 &&
         formData.value.location !== null;
});

// Function to validate description
const validateDescription = () => {
  if (formData.value.description.length > 1500) {
    formData.value.description = formData.value.description.substring(0, 1500);
    const textarea = document.querySelector('.custom-textarea');
    if (textarea) {
      textarea.classList.add('border-danger');
      setTimeout(() => {
        textarea.classList.remove('border-danger');
      }, 800);
    }
  }
};

const currentCategories = computed(() =>
  selectedService.value === 'Penipuan' ? fraudCategories : infrastructureCategories
);

// Service selection handler
const selectService = (value) => {
  selectedService.value = value;
  formData.value.category = '';
};

// File upload handler
const handleFileUpload = (event) => {
  const file = event.target.files[0];
  if (file) {
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran file terlalu besar. Maksimal 5MB.');
      event.target.value = '';
      return;
    }
    formData.value.evidence = file;
  }
};

// Function for reverse geocoding using Nominatim API
async function reverseGeocode(lat, lng) {
  try {
    const response = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`);
    if (!response.ok) {
      throw new Error('Gagal mendapatkan alamat');
    }
    const data = await response.json();
    return data.display_name || 'Alamat tidak ditemukan';
  } catch (error) {
    console.error('Error saat reverse geocoding:', error);
    return 'Alamat tidak tersedia';
  }
}

// Handle form submission with login check
function handleSubmitReport() {
  if (!userIsLoggedIn.value) {
    // Tampilkan modal login dengan indikasi dari halaman pelaporan
    showLoginModal.value = true;
    isFromReport.value = true;
  } else {
    // Proses pengiriman laporan
    submitReport();
  }
}

// Form submission handler with improved validation
const submitReport = () => {
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false;
  });

  let hasErrors = false;
  if (!formData.value.category || formData.value.category.trim() === '') {
    validationErrors.category = true;
    hasErrors = true;
  }

  if (!formData.value.description || formData.value.description.trim() === '') {
    validationErrors.description = true;
    hasErrors = true;
  }

  if (!formData.value.location) {
    validationErrors.location = true;
    hasErrors = true;
  }

  if (hasErrors) {
    const firstError = document.querySelector('.is-invalid, .border-danger');
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return;
  }

  const dataToSubmit = new FormData();
  dataToSubmit.append('category', formData.value.category);
  dataToSubmit.append('description', formData.value.description);
  if (formData.value.evidence) {
    dataToSubmit.append('evidence', formData.value.evidence);
  }
  dataToSubmit.append('source', formData.value.source);
  dataToSubmit.append('location[lat]', formData.value.location.lat);
  dataToSubmit.append('location[lng]', formData.value.location.lng);
  dataToSubmit.append('address', formData.value.address);
  dataToSubmit.append('service', selectedService.value);

  Inertia.post('/pelaporan/create', dataToSubmit, {
    onSuccess: () => {
      formData.value = {
        category: '',
        description: '',
        evidence: null,
        location: null,
        address: '',
        source: ''
      };
      showSuccessModal.value = true;    
    },
    onError: (error) => {
      console.error('Error:', error);
    }
  });
};

// OPENSTREETMAP IMPLEMENTATION
let map, marker;

onMounted(() => {
  setTimeout(() => {
    initMap();
    window.addEventListener('resize', debounce(handleMapResize, 200));
  }, 300);
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
      attribution: '© <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 19
    }).addTo(map);
    
    map.on('click', (e) => {
      placeMarker(e.latlng);
      validationErrors.location = false;
    });
    
    setTimeout(() => {
      map.invalidateSize();
    }, 500);
  } catch (error) {
    console.error("Error initializing map:", error);
  }
}

function handleMapResize() {
  if (map) {
    map.invalidateSize();
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
    
    // Set popup content based on selected service
    const popupContent  = selectedService.value === 'Penipuan' 
      ? '<b>Lokasi Pelaporan</b>' 
      : '<b>Lokasi Kerusakan Infrastruktur</b>';
      
    marker.bindPopup(popupContent).openPopup();
    
    formData.value.location = {
      lat: latlng.lat,
      lng: latlng.lng
    };
    
    reverseGeocode(latlng.lat, latlng.lng).then(address => {
      formData.value.address = address;
    });
  } catch (error) {
    console.error("Error placing marker:", error);
  }
}

function getCurrentLocation() {
  if (navigator.geolocation) {
    formData.value.address = 'Mendapatkan alamat...';
    
    navigator.geolocation.getCurrentPosition(
      async (position) => {
        const userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        
        map.setView([userLocation.lat, userLocation.lng], 16);
        
        placeMarker(userLocation);
        
        const address = await reverseGeocode(userLocation.lat, userLocation.lng);
        formData.value.address = address;
        
        validationErrors.location = false;
      },
      (error) => {
        let message;
        switch(error.code) {
          case error.PERMISSION_DENIED:
            message = "Akses lokasi ditolak oleh pengguna.";
            break;
          case error.POSITION_UNAVAILABLE:
            message = "Informasi lokasi tidak tersedia.";
            break;
          case error.TIMEOUT:
            message = "Waktu permintaan lokasi habis.";
            break;
          default:
            message = "Terjadi kesalahan saat mendapatkan lokasi.";
        }
        alert(message);
        formData.value.address = '';
      },
      { enableHighAccuracy: true }
    );
  } else {
    alert("Browser Anda tidak mendukung Geolocation.");
  }
}

// Debounce function to limit resize event frequency
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
</script>

<style scoped>
.text-nowrap {
  white-space: nowrap;
}

.hero-section {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  overflow: hidden;
  position: relative;
  min-height: auto;
  padding-bottom: 2rem;
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
}

.text-section {
  color: #fff;
  position: relative;
  z-index: 1;
  margin-top: 0;
  padding: 1.5rem;
}

.feature-box {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  backdrop-filter: blur(10px);
  transition: transform 0.3s ease;
  text-align: left;
  padding: 1.5rem;
  min-height: 200px; /* Ensure uniform height */
  height: 100%; /* Fill the parent container */
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.feature-box:hover {
  transform: translateY(-5px);
}

.feature-box i {
  color: #ffffff;
}

.feature-box h5 {
  color: #ffffff;
  font-weight: 600;
  font-size: 1rem;
  margin-bottom: 0.5rem;
}

.feature-box p {
  color: rgba(255, 255, 255, 0.85);
  font-size: 0.875rem;
  line-height: 1.4;
  margin-bottom: 0;
  flex-grow: 1; /* Ensure text takes available space */
}

.mobile-feature {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  backdrop-filter: blur(5px);
  padding: 0.75rem;
  min-height: 80px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.mobile-feature i {
  color: #ffffff;
}

.mobile-feature .small {
  color: rgba(255, 255, 255, 0.85);
  font-size: 0.75rem;
  line-height: 1.2;
}

.form-container {
  background: white;
  border-radius: 15px;
  max-width: 100%;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  overflow: hidden;
}

.form-control,
.form-select {
  font-size: 1rem;
  padding: 0.75rem;
  border-radius: 6px;
}

.form-control:focus,
.form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.custom-select {
  border: 1px solid #ced4da;
  cursor: pointer;
  transition: all 0.2s ease;
  max-width: 100%;
  overflow-x: hidden;
  word-wrap: break-word;
}

.custom-textarea {
  border: 1px solid #ced4da;
  border-radius: 6px;
  padding: 0.75rem;
  min-height: 100px;
  word-break: break-word;
  hyphens: auto;
}

.custom-textarea.border-danger {
  border-color: #dc3545 !important;
  animation: shake 0.5s linear;
}

.custom-file-input {
  border-radius: 6px;
  overflow: hidden;
}

.custom-file-input .form-control {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.service-btn {
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.3s ease;
  padding: 0.75rem 1rem;
  min-height: 48px;
}

.service-btn:hover:not(.btn-primary) {
  background-color: #f8f9fa;
  border-color: #6c757d;
}

.service-btn.btn-primary {
  box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
}

.btn-outline-primary {
  border-radius: 6px;
  transition: all 0.3s ease;
  padding: 0.75rem 1rem;
  min-height: 48px;
}

.submit-btn {
  border-radius: 6px;
  font-weight: 500;
  background: linear-gradient(135deg, #0d6efd, #0b5ed7);
  border: none;
  box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
  transition: all 0.3s ease;
  padding: 1rem;
  min-height: 52px;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(13, 110, 253, 0.4);
}

.map-container {
  border-radius: 6px;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
  width: 100%;
  height: 250px;
}

.btn-primary:disabled {
  background: #6c757d;
  border-color: #6c757d;
  opacity: 0.65;
  cursor: not-allowed;
}

.service-tabs {
  border-bottom: 1px solid #dee2e6;
  padding-bottom: 1rem;
}

.highlight-bar {
  width: 60px;
  height: 4px;
  background-color: #ffc107;
  border-radius: 2px;
}

@keyframes shake {
  0% { transform: translateX(0); }
  20% { transform: translateX(-5px); }
  40% { transform: translateX(5px); }
  60% { transform: translateX(-3px); }
  80% { transform: translateX(3px); }
  100% { transform: translateX(0); }
}

@keyframes btn-wave {
  0% { transform: translateX(-100%) rotate(45deg); }
  100% { transform: translateX(100%) rotate(45deg); }
}

.btn-animation {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, rgba(255,255,255,0) 0%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0) 100%);
  transform: translateX(-100%);
  animation: btn-wave 2s infinite;
}

.leaflet-default-icon-path {
  background-image: url("https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png");
}

.responsive-title {
  word-wrap: break-word;
  word-break: break-word;
  hyphens: auto;
  line-height: 1.2;
}

.responsive-title span {
  display: inline;
}

/* Ensure uniform card sizes on tablet */
@media (min-width: 768px) and (max-width: 991.98px) {
  .feature-box {
    min-height: 180px; /* Slightly smaller for tablets */
    padding: 1rem;
  }
  .feature-box h5 {
    font-size: 0.95rem;
  }
  .feature-box p {
    font-size: 0.8rem;
    line-height: 1.3;
  }
  .row.g-3 {
    margin-right: -0.5rem;
    margin-left: -0.5rem;
  }
  .col-md-4 {
    padding-right: 0.5rem;
    padding-left: 0.5rem;
  }
}

@media (min-width: 600px) and (max-width: 1199px) {
  .form-container {
    margin: 0 auto !important;
    max-width: 90% !important;
  }
  
  .hero-section {
    display: flex;
    flex-direction: column;
  }
  
  .hero-section .row .col-lg-6 {
    width: 100%;
    max-width: 100%;
    flex: 0 0 100%;
  }
  
  .text-section {
    padding: 2rem 3rem !important;
    margin-bottom: 1rem;
  }
  
  .hero-section .col-lg-6:last-child {
    display: flex;
    justify-content: center;
    padding: 1rem 2rem 3rem !important;
  }

  .bg-light {
    padding: 0 !important;
    display: flex;
    justify-content: center;
  }
}

@media (min-width: 768px) and (max-width: 834px) {
  .form-container {
    max-width: 85% !important;
    margin: 0 auto !important;
  }
}

@media (min-width: 900px) and (max-width: 920px) {
  .form-container {
    max-width: 80% !important;
  }
}

@media (min-width: 650px) and (max-width: 767px) {
  .form-container {
    max-width: 92% !important;
  }
  
  .hero-section .col-lg-6:last-child {
    padding: 1rem 1.5rem 2rem !important;
  }
}

@media (min-width: 1024px) and (max-width: 1100px) {
  .hero-section .row {
    display: flex;
    flex-direction: column;
  }
  
  .form-container {
    max-width: 75% !important;
    margin: 0 auto 2rem !important;
  }
}

@media (min-width: 600px) and (max-width: 1199px) {
  .map-container {
    height: 280px !important;
    width: 100% !important;
  }
  
  .service-btn, .submit-btn {
    min-height: 50px;
  }
  
  .form-container {
    padding: 2rem !important;
  }
}

@media (max-width: 320px) {
  .responsive-title {
    font-size: 1.5rem !important;
    line-height: 1.3;
  }
  .responsive-title i {
    font-size: 1.3rem;
  }
  .hero-content {
    padding: 1rem !important;
  }
  .form-container {
    padding: 1rem !important;
  }
  .display-4 {
    font-size: 1.8rem !important;
  }
  .lead {
    font-size: 0.9rem;
  }
  .map-container {
    height: 200px;
  }
  .custom-select {
    font-size: 0.9rem;
    padding: 0.5rem;
    height: auto;
  }
}

@media (min-width: 321px) and (max-width: 375px) {
  .responsive-title {
    font-size: 1.7rem !important;
    line-height: 1.3;
  }
  .responsive-title i {
    font-size: 1.5rem;
  }
  .hero-content {
    padding: 2rem 1.5rem !important;
  }
  .form-container {
    padding: 1.5rem !important;
  }
  .display-4 {
    font-size: 2.2rem !important;
  }
  .lead {
    font-size: 1rem;
  }
  .map-container {
    height: 220px;
  }
  .custom-select {
    font-size: 0.95rem;
    padding: 0.6rem;
    height: auto;
  }
}

@media (min-width: 376px) and (max-width: 576px) {
  .responsive-title {
    font-size: 1.9rem !important;
  }
  .hero-content {
    padding: 2rem 1.5rem !important;
  }
  .form-container {
    padding: 1.5rem !important;
  }
  .display-4 {
    font-size: 2.2rem !important;
  }
  .lead {
    font-size: 1rem;
  }
  .map-container {
    height: 220px;
  }
  .custom-select {
    font-size: 0.95rem;
    padding: 0.6rem;
    height: auto;
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .hero-content {
    padding: 2.5rem 2rem !important;
  }
  .form-container {
    max-width: 90%;
    padding: 2rem !important;
  }
  .display-4 {
    font-size: 2.5rem !important;
  }
  .map-container {
    height: 250px;
  }
  .custom-select {
    font-size: 1rem;
    padding: 0.75rem;
    height: auto;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .hero-content {
    padding: 3rem 2.5rem !important;
  }
  .form-container {
    max-width: 85%;
    padding: 2.5rem !important;
  }
  .display-4 {
    font-size: 3rem !important;
  }
  .map-container {
    height: 280px;
  }
  .custom-select {
    font-size: 1.05rem;
    padding: 0.8rem;
    height: auto;
  }
}

@media (min-width: 992px) {
  .hero-section {
    min-height: 100vh;
  }
  .hero-content {
    padding: 4rem !important;
    text-align: left;
  }
  .bg-light {
    background: transparent !important;
  }
  .form-container {
    max-width: 580px;
    z-index: 2;
    padding: 3rem !important;
  }
  .display-4 {
    font-size: 3.5rem !important;
  }
  .map-container {
    height: 300px;
  }
  .custom-select {
    font-size: 1.1rem;
    padding: 0.85rem;
    height: auto;
  }
}

@media (min-width: 1200px) {
  .form-container {
    max-width: 620px;
  }
  .display-4 {
    font-size: 2.5rem !important;
  }
  .map-container {
    height: 320px;
  }
  .custom-select {
    font-size: 1.15rem;
    padding: 0.9rem;
    height: auto;
  }
}

@media (min-width: 1400px) {
  .container-fluid {
    max-width: 1400px;
  }
  .form-container {
    max-width: 680px;
  }
  .map-container {
    height: 350px;
  }
  .custom-select {
    font-size: 1.2rem;
    padding: 1rem;
    height: auto;
  }
}

@media (max-height: 600px) and (orientation: landscape) {
  .hero-section {
    min-height: auto;
    overflow-y: auto;
  }
  .hero-content {
    padding: 2rem !important;
  }
  .form-container {
    padding: 2rem !important;
  }
  .map-container {
    height: 200px;
  }
  .custom-select {
    font-size: 0.9rem;
    padding: 0.5rem;
    height: auto;
  }
}

@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  .feature-box, .mobile-feature {
    backdrop-filter: blur(12px);
  }
  .form-container {
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  }
  .custom-select {
    font-size: 1.05rem;
    padding: 0.8rem;
    height: auto;
  }
}
</style>