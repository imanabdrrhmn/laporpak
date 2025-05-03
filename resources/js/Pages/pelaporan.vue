<template>
  <AppLayout>
    <Head title="Pelaporan"/>
  <section class="hero-section py-4">
    <div class="container">
      <div class="row g-4">
        <!-- Title + Description on Left Side - DYNAMIC CONTENT -->
        <div class="col-md-5 col-12 d-flex align-items-center">
          <div class="text-section px-0 text-md-start text-center"> 
        
            <h1 class="fs-2 fw-bold mb-3">
              <i :class="serviceInfo[selectedService].titleIcon + ' me-2'"></i>
              {{ serviceInfo[selectedService].title }}
            </h1>
            <p class="text-white text-opacity-75 mb-3">
              {{ serviceInfo[selectedService].description }}
            </p>
            
            <div class="features mt-3 d-flex flex-column align-items-md-start align-items-center">
              <div class="feature-item d-flex align-items-start mb-3">
                <div class="feature-icon bg-primary bg-opacity-20 p-2 rounded-circle me-3 shadow-sm">
                  <i :class="serviceInfo[selectedService].feature1.icon + ' text-w'"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-start fw-bold">{{ serviceInfo[selectedService].feature1.title }}</h6>
                  <p class="text-white text-opacity-85 mb-0 text-start">{{ serviceInfo[selectedService].feature1.subtitle }}</p>
                </div>
              </div>
              <div class="feature-item d-flex align-items-start">
                <div class="feature-icon bg-primary bg-opacity-20 p-2 rounded-circle me-3 shadow-sm">
                  <i :class="serviceInfo[selectedService].feature2.icon + ' text-w'"></i>
                </div>
                <div>
                  <h6 class="mb-0 text-start fw-bold">{{ serviceInfo[selectedService].feature2.title }}</h6>
                  <p class="text-white text-opacity-85 mb-0 text-start">{{ serviceInfo[selectedService].feature2.subtitle }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Form on Right Side - Updated for desktop view -->
        <div class="col-md-7 col-12 d-flex justify-content-center">
          <div class="bg-white p-4 p-lg-5 rounded-3 shadow-lg border-top border-primary border-3 form-container mx-auto">
            <h5 class="text-start fw-bold mb-3">
              <i class="bi bi-exclamation-triangle text-warning me-2"></i>
              {{ serviceInfo[selectedService].formTitle }}
            </h5>

            <!-- Service Selection with Tab-like Buttons -->
            <div class="service-tabs mb-4">
              <div class="d-flex gap-2">
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

            <form @submit.prevent="handleSubmit" ref="formRef">
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
                    style="width: 100%; height: 250px;" 
                    class="mb-2 rounded-2 border map-container"
                    :class="{'border-danger': validationErrors.location}"
                  ></div>
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
  <feedback /> 
</AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, reactive, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import 'leaflet/dist/leaflet.css';
import L from 'leaflet';


// Import komponen-komponen
import Section from '@/Components/Section.vue'
import Feedback from '@/Components/Feedback.vue'
import Alur from '@/Components/alurpelaporan.vue'
import AppLayout from '@/Layouts/AppLayout.vue';

const page = usePage()
page.layout = AppLayout

// Service options with icons
const services = [
  { label: 'Pelaporan', value: 'fraud', icon: 'bi bi-shield-exclamation' },
  { label: 'Infrastruktur', value: 'infrastructure', icon: 'bi bi-building-gear' }
];

// Categories for each service type
const fraudCategories = [
  { label: 'Nomor Telepon', value: 'NomerHp' },
  { label: 'Email', value: 'Email' },
  { label: 'Akun Media Sosial', value: 'MediaSosial' },
  { label: 'Website', value: 'Website' }
];

const infrastructureCategories = [
  { label: 'Jalan Rusak', value: 'jalan' },
  { label: 'Lampu Penerangan', value: 'lampu' },
  { label: 'Saluran Air', value: 'saluran' },
  { label: 'Fasilitas Umum', value: 'fasum' },
  { label: 'Lainnya', value: 'other' }
];

// Service information
const serviceInfo = {
  fraud: {
    badge: 'Platform Pelaporan',
    title: 'Pelaporan Akun',
    titleIcon: 'bi bi-exclamation-triangle-fill',
    description: 'Laporkan nomor HP, email, atau akun yang terindikasi mencurigakan untuk verifikasi lebih lanjut.',
    icon: 'bi bi-shield-check',
    formTitle: 'Form Pelaporan',
    descriptionPlaceholder: 'Ceritakan bagaimana kejadian yang mencurigakan terjadi dan modus yang digunakan...',
    feature1: {
      title: 'Verifikasi Data',
      subtitle: 'Pemeriksaan menyeluruh',
      icon: 'bi bi-shield-check'
    },
    feature2: {
      title: 'Perlindungan Pengguna',
      subtitle: 'Keamanan terjamin',
      icon: 'bi bi-graph-up-arrow'
    }
  },
  infrastructure: {
    badge: 'Lapor Infrastruktur',
    title: 'Pelaporan Kerusakan',
    titleIcon: 'bi bi-wrench',
    description: 'Laporkan fasilitas umum dan infrastruktur yang rusak untuk perbaikan yang lebih cepat.',
    icon: 'bi bi-building-gear',
    formTitle: 'Form Infrastruktur',
    descriptionPlaceholder: 'Jelaskan detail kerusakan dan dampaknya terhadap lingkungan...',
    feature1: {
      title: 'Perbaikan Lebih Cepat',
      subtitle: 'Prioritas berdasarkan laporan',
      icon: 'bi bi-tools'
    },
    feature2: {
      title: 'Lingkungan Lebih Baik',
      subtitle: 'Infrastruktur terpelihara',
      icon: 'bi bi-tree'
    }
  }
};

// Reactive data
const selectedService = ref('fraud');
const formData = ref({
  category: '',
  description: '',
  evidence: null,
  location: null
});
const formRef = ref(null);

// Watch for service changes to reset form values
watch(selectedService, () => {
  formData.value.category = '';
  formData.value.description = '';
  // Reset validation errors
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false;
  });
});

// Validation errors tracker
const validationErrors = reactive({
  category: false,
  description: false,
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

// Fungsi untuk validasi deskripsi
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
  selectedService.value === 'fraud' ? fraudCategories : infrastructureCategories
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
    // Check file size (5MB limit)
    if (file.size > 5 * 1024 * 1024) {
      alert('Ukuran file terlalu besar. Maksimal 5MB.');
      event.target.value = '';
      return;
    }
    formData.value.evidence = file;
  }
};

// Form submission handler with improved validation
const handleSubmit = () => {
  // Reset validation errors
  Object.keys(validationErrors).forEach(key => {
    validationErrors[key] = false;
  });
  
  // Validate all required fields
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
  
  // If there are errors, highlight them and prevent form submission
  if (hasErrors) {
    const firstError = document.querySelector('.is-invalid, .border-danger');
    if (firstError) {
      firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    return;
  }

  console.log('Data dikirim:', {
    ...formData.value,
    service: selectedService.value,
    evidenceName: formData.value.evidence?.name || 'Tidak ada bukti',
    timestamp: new Date().toISOString()
  });
  
  // Show success message with appropriate service message
  const successAlert = document.createElement('div');
  successAlert.classList.add('alert', 'alert-success', 'alert-dismissible', 'fade', 'show', 'mt-3', 'py-2');
  
  // Set message based on selected service
  const successMessage = selectedService.value === 'fraud' 
    ? 'Data berhasil dikirim untuk verifikasi!' 
    : 'Laporan infrastruktur berhasil dikirim!';
  
  successAlert.innerHTML = `
    <strong><i class="bi bi-check-circle me-2"></i>${successMessage}</strong> 
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
  `;
  
  // Append the alert to the form
  if (formRef.value) {
    formRef.value.prepend(successAlert);
  }
  
  // Reset form
  formData.value = {
    category: '',
    description: '',
    evidence: null,
    location: null
  };
  
  // Remove marker from map
  if (marker) {
    map.removeLayer(marker);
    marker = null;
  }
};

// OPENSTREETMAP IMPLEMENTATION
const mapRef = ref(null);
let map, marker;

onMounted(() => {
  // Use a short timeout to ensure the DOM is fully rendered
  setTimeout(() => {
    initMap();
  }, 300); // Increased timeout for better reliability
});

function initMap() {
  // Default coordinates (Jakarta, Indonesia)
  const defaultPosition = [-6.2, 106.8];
  
  if (!mapRef.value) return;
  
  try {
    // Initialize the map
    map = L.map(mapRef.value).setView(defaultPosition, 12);
    
    // Add OpenStreetMap tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      maxZoom: 19
    }).addTo(map);
    
    // Add click event listener to the map
    map.on('click', (e) => {
      placeMarker(e.latlng);
      validationErrors.location = false;
    });
    
    // Fix map display issue by triggering a resize event
    setTimeout(() => {
      map.invalidateSize();
    }, 500);
  } catch (error) {
    console.error("Error initializing map:", error);
  }
}

function placeMarker(latlng) {
  // Remove existing marker if any
  if (marker) {
    map.removeLayer(marker);
  }
  
  try {
    // Create custom marker icon with proper path handling
    const customIcon = L.icon({
      iconUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png',
      iconSize: [25, 41],
      iconAnchor: [12, 41],
      popupAnchor: [1, -34],
      shadowUrl: 'https://unpkg.com/leaflet@1.7.1/dist/images/marker-shadow.png',
      shadowSize: [41, 41]
    });
    
    // Create new marker
    marker = L.marker(latlng, { icon: customIcon }).addTo(map);
    
    // Add popup with content based on selected service
    const popupContent = selectedService.value === 'fraud' 
      ? "<b>Lokasi Pelaporan</b>" 
      : "<b>Lokasi Kerusakan Infrastruktur</b>";
      
    marker.bindPopup(popupContent).openPopup();
    
    // Store the location in form data
    formData.value.location = {
      lat: latlng.lat,
      lng: latlng.lng
    };
  } catch (error) {
    console.error("Error placing marker:", error);
  }
}

function getCurrentLocation() {
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(
      (position) => {
        const userLocation = {
          lat: position.coords.latitude,
          lng: position.coords.longitude
        };
        
        // Center map on user location
        map.setView([userLocation.lat, userLocation.lng], 16);
        
        // Place marker at user location
        placeMarker(userLocation);
        
        // Clear location validation error
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
      },
      { enableHighAccuracy: true }
    );
  } else {
    alert("Browser Anda tidak mendukung Geolocation.");
  }
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #1a56db, #1e429f);
  min-height: auto;
  width: 100%;
  display: flex;
  align-items: center;
  position: relative;
  overflow: hidden;
  padding: 2rem 0;
}

.text-section {
  color: #fff;
  position: relative;
  z-index: 1;
}

.feature-icon {
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
}

.form-container {
  position: relative;
  z-index: 1;
  backdrop-filter: blur(10px);
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1); /* Updated box-shadow to match specs */
  max-width: 580px; /* Set max-width to 580px for desktop */
  border-radius: 15px; /* Updated border-radius to 15px */
}

/* Add the desktop-specific padding */
@media (min-width: 992px) {
  .form-container {
    padding: 3rem !important; /* p-lg-5 equivalent is 3rem or 48px */
  }
}

/* Form controls styling */
.form-control:focus, 
.form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Custom select styling */
.custom-select {
  border-radius: 4px;
  border: 1px solid #ced4da;
  cursor: pointer;
  transition: all 0.2s ease;
}

/* Custom textarea styling */
.custom-textarea {
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 12px;
  min-height: 120px;
}

.custom-textarea.border-danger {
  border-color: #dc3545 !important;
  animation: shake 0.5s linear;
}

/* Custom file input styling */
.custom-file-input {
  border-radius: 4px;
  overflow: hidden;
}

.custom-file-input .form-control {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

/* Button styling */
.service-btn {
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.service-btn:hover:not(.btn-primary) {
  background-color: #f8f9fa;
  border-color: #6c757d;
}

.service-btn.btn-primary {
  box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
}

.btn-outline-primary {
  border-radius: 4px;
  transition: all 0.3s ease;
}

.submit-btn {
  border-radius: 6px;
  font-weight: 500;
  background: linear-gradient(135deg, #0d6efd, #0b5ed7);
  border: none;
  box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
  transition: all 0.3s ease;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(13, 110, 253, 0.4);
}

/* Map container styling */
.map-container {
  border-radius: 6px !important;
  overflow: hidden;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Button disabled styling */
.btn-primary:disabled {
  background: #6c757d;
  border-color: #6c757d;
  opacity: 0.65;
  cursor: not-allowed;
}

/* Service tabs styling */
.service-tabs {
  border-bottom: 1px solid #dee2e6;
  padding-bottom: 1rem;
}

/* Animations */
@keyframes shake {
  0% { transform: translateX(0); }
  20% { transform: translateX(-5px); }
  40% { transform: translateX(5px); }
  60% { transform: translateX(-3px); }
  80% { transform: translateX(3px); }
  100% { transform: translateX(0); }
}

/* Button animation */
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

/* Fix for Leaflet marker icon */
.leaflet-default-icon-path {
  background-image: url("https://unpkg.com/leaflet@1.7.1/dist/images/marker-icon.png");
}

/* Service transition animation */
.text-section,
.form-container h5,
.features {
  transition: all 0.3s ease-in-out;
}

/* Responsive layout adjustments */
@media (max-width: 767.98px) {
  .text-section {
    margin-bottom: 2rem;
  }
  
  .features {
    flex-direction: row !important;
    justify-content: center;
    flex-wrap: wrap;
  }
  
  .feature-item {
    margin-right: 1.5rem;
  }
  
  /* Adjust spacing for better mobile display */
  .hero-section {
    padding: 1.5rem 0;
  }
  
  .form-container {
    padding: 1.5rem !important;
  }
}

/* Extra small devices */
@media (max-width: 575.98px) {
  .d-flex.justify-content-between.align-items-center {
    flex-direction: column;
    align-items: stretch !important;
  }
  
  .d-flex.justify-content-between.align-items-center .ms-auto {
    margin-left: 0 !important;
    margin-top: 0.75rem;
    width: 100%;
  }
  
  .hero-section {
    padding: 1rem 0;
  }
  
  .form-container {
    padding: 1rem !important;
  }
  
  /* Make map slightly smaller on very small screens */
  #map {
    height: 200px !important;
  }
}
</style>