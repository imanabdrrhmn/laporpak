<template>
  <div class="col-lg-6 d-flex align-items-center justify-content-end p-3 p-md-2 bg-light">
    <!-- Flag Icons CSS Library -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/6.6.6/css/flag-icons.min.css" />
    <div class="form-container p-3 p-sm-4 p-lg-5 w-100">
      <h5 class="text-start fw-bold mb-3">
        <i class="bi bi-exclamation-triangle text-warning me-2"></i>
        {{ serviceInfo[selectedService].formTitle }}
      </h5>
      <div class="service-tabs mb-4">
        <div class="d-flex gap-2 flex-wrap">
          <button
            v-for="service in services"
            :key="service.value"
            type="button"
            class="btn flex-fill position-relative service-btn py-2"
            :class="selectedService === service.value ? 'btn-primary text-white' : 'btn-outline-secondary'"
            @click="$emit('select-service', service.value)"
          >
            <i :class="service.icon + ' me-2'"></i>
            {{ service.label }}
            <span v-if="selectedService === service.value" class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle">
              <span class="visually-hidden">Selected</span>
            </span>
          </button>
        </div>
      </div>
      <form @submit.prevent="$emit('submit-report')" ref="formRef">
        <div class="row g-3">
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
          <div class="col-12" v-if="selectedService === 'Penipuan' && formData.category === 'Email' && formData.category">
            <label for="email" class="form-label mb-2">Alamat Email</label>
            <input
              id="email"
              v-model="formData.email"
              type="email"
              class="form-control"
              :class="{'is-invalid': validationErrors.email}"
              placeholder="Masukkan alamat email"
              @input="validateEmail"
              required
            />
            <div v-if="validationErrors.email" class="invalid-feedback">
              Alamat email tidak valid
            </div>
          </div>
          <div class="col-12" v-if="selectedService === 'Penipuan' && formData.category !== 'Email' && formData.category">
            <label for="source" class="form-label mb-2">Nomer Telepon</label>
            <div class="input-group">
              <div class="country-select-wrapper">
                <div class="dropdown" ref="countryDropdown">
                  <div class="searchable-country-select">
                    <div class="selected-country" @click="toggleDropdown">
                      <span :class="`fi fi-${selectedCountry.iso2.toLowerCase()} me-2`" class="flag-icon"></span>
                      +{{ selectedCountry.dialCode }}
                      <i class="bi bi-chevron-down ms-1"></i>
                    </div>
                    <div class="dropdown-menu-custom" v-show="showDropdown">
                      <div class="search-input-wrapper">
                        <input
                          type="text"
                          class="form-control search-input"
                          placeholder="Cari kode negara... (contoh: 62)"
                          v-model="countrySearch"
                          @input="filterCountries"
                          ref="searchInput"
                        />
                        <i class="bi bi-search search-icon"></i>
                      </div>
                      <ul class="country-list">
                        <li v-for="country in filteredCountries" :key="country.iso2">
                          <a
                            class="country-item"
                            href="#"
                            @click.prevent="selectCountry(country)"
                          >
                            <span :class="`fi fi-${country.iso2.toLowerCase()} me-2`" class="flag-icon"></span>
                            <span class="country-code">+{{ country.dialCode }}</span>
                          </a>
                        </li>
                        <li v-if="filteredCountries.length === 0" class="no-results">
                          Kode negara tidak ditemukan
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>
              </div>
              <input
                id="source"
                v-model="localPhoneNumber"
                type="tel"
                class="form-control"
                :class="{'is-invalid': validationErrors.source}"
                placeholder="Masukkan nomor telepon"
                @input="onPhoneInput"
                @keypress="onPhoneKeypress"
                required
              />
            </div>
            <div v-if="validationErrors.source" class="invalid-feedback">
              Nomor telepon tidak valid
            </div>
          </div>
          <div class="col-12">
            <label for="evidence" class="form-label mb-2">Bukti</label>
            <div class="input-group custom-file-input">
              <input
                id="evidence"
                type="file"
                class="form-control"
                accept="image/jpeg,image/jpg,image/png"
                @change="handleFileUpload"
                aria-label="Bukti"
                :disabled="isProcessingImage"
              />
              <span class="input-group-text py-0 px-2">
                <i class="bi bi-image text-primary" v-if="!isProcessingImage"></i>
                <div class="spinner-border spinner-border-sm text-primary" role="status" v-else>
                  <span class="visually-hidden">Processing...</span>
                </div>
              </span>
            </div>
            <div class="form-text">
              Format: JPEG, PNG (Max: 5MB) 
            </div>
            <div v-if="isProcessingImage" class="mt-2">
              <div class="alert alert-info d-flex align-items-center" role="alert">
                <div class="spinner-border spinner-border-sm me-2" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
                <div>Sedang memproses gambar...</div>
              </div>
            </div>
            <div v-if="convertedImageInfo" class="mt-2">
              <div class="alert alert-success" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                Gambar berhasil dikonversi ke WebP! 
                <small class="d-block mt-1">
                  Ukuran asli: {{ formatFileSize(convertedImageInfo.originalSize) }} → 
                  Ukuran WebP: {{ formatFileSize(convertedImageInfo.webpSize) }} 
                  ({{ convertedImageInfo.compressionRatio }}% lebih kecil)
                </small>
              </div>
            </div>
          </div>
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
              @input="$emit('validate-description')"
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
          <div class="col-12">
            <label for="region" class="form-label mb-2">Wilayah</label>
            <select
              id="region"
              v-model="formData.region"
              class="form-select custom-select"
              required
              :class="{ 'is-invalid': validationErrors.region }"
              @change="validationErrors.region = false"
              aria-label="Provinsi"
            >
              <option disabled value="">Pilih Wilayah</option>
              <option
                v-for="province in provinces"
                :key="province"
                :value="province"
              >
                {{ province }}
              </option>
            </select>
            <div v-if="validationErrors.region" class="invalid-feedback">
              Wilayah harus dipilih
            </div>
          </div>
          <div class="col-12">
            <MapContainer
              :form-data="formData"
              :validation-errors="validationErrors"
              :selected-service="selectedService"
              @update:location="formData.location = $event"
              @update:address="formData.address = $event"
              @get-current-location="$emit('get-current-location')"
            />
          </div>
          <div class="col-12">
            <div class="d-grid">
              <button
                type="submit"
                class="btn btn-primary position-relative overflow-hidden submit-btn py-3"
                :disabled="!isFormValid || isProcessingImage"
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
</template>

<script setup>
import MapContainer from './MapContainer.vue';
import { ref, computed, onMounted, onUnmounted } from 'vue';
import { allCountries } from 'country-telephone-data';

const props = defineProps({
  selectedService: String,
  services: Array,
  serviceInfo: Object,
  currentCategories: Array,
  formData: Object,
  validationErrors: Object,
  isFormValid: Boolean,
  provinces: Array,
});

const emit = defineEmits(['select-service', 'submit-report', 'file-upload', 'validate-description', 'get-current-location']);

defineExpose({
  formRef: ref(null)
});

const selectedCountry = ref(allCountries.find(c => c.iso2 === 'id'));
const localPhoneNumber = ref('');
const showDropdown = ref(false);
const countrySearch = ref('');
const filteredCountries = ref([]);
const searchInput = ref(null);
const countryDropdown = ref(null);

// Image processing states
const isProcessingImage = ref(false);
const convertedImageInfo = ref(null);

const sortedCountries = computed(() => {
  return allCountries.sort((a, b) => {
    if (a.iso2 === 'id') return -1;
    if (b.iso2 === 'id') return 1;
    return a.name.localeCompare(b.name);
  });
});

const filterCountries = () => {
  const search = countrySearch.value.toLowerCase();
  if (!search) {
    filteredCountries.value = sortedCountries.value;
    return;
  }
  
  filteredCountries.value = sortedCountries.value.filter(country => 
    country.dialCode.includes(search)
  );
};

const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value;
  if (showDropdown.value) {
    countrySearch.value = '';
    filteredCountries.value = sortedCountries.value;
    setTimeout(() => {
      if (searchInput.value) {
        searchInput.value.focus();
      }
    }, 100);
  }
};

const selectCountry = (country) => {
  selectedCountry.value = country;
  showDropdown.value = false;
  countrySearch.value = '';
  
  if (!localPhoneNumber.value.trim()) {
    props.validationErrors.source = false;
  } else {
    validatePhoneNumber();
  }
};

const handleClickOutside = (event) => {
  if (countryDropdown.value && !countryDropdown.value.contains(event.target)) {
    showDropdown.value = false;
  }
};

onMounted(() => {
  filteredCountries.value = sortedCountries.value;
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});

const validatePhoneNumber = () => {
  const phoneNumber = localPhoneNumber.value.replace(/\D/g, '');
  
  if (!phoneNumber) {
    props.validationErrors.source = false;
    props.formData.source = '';
    return;
  }
  
  const isValid = phoneNumber.length >= 8 && phoneNumber.length <= 15;
  
  if (isValid) {
    props.formData.source = `+${selectedCountry.value.dialCode}${phoneNumber}`;
    props.validationErrors.source = false;
  } else {
    props.validationErrors.source = true;
  }
};

const validateEmail = () => {
  const email = props.formData.email;
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

  if (emailRegex.test(email)) {
    props.validationErrors.email = false;
    props.formData.source = email; 
  } else {
    props.validationErrors.email = true;
  }
};

const onPhoneKeypress = (e) => {
  if (!/[\d]/.test(e.key)) {
    e.preventDefault();
  }
};

const onPhoneInput = (e) => {
  e.target.value = e.target.value.replace(/\D/g, '');
  localPhoneNumber.value = e.target.value;
  validatePhoneNumber();
};

// Image conversion functions
const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes';
  const k = 1024;
  const sizes = ['Bytes', 'KB', 'MB', 'GB'];
  const i = Math.floor(Math.log(bytes) / Math.log(k));
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const convertImageToWebP = (file, quality = 0.8) => {
  return new Promise((resolve, reject) => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();
    
    img.onload = () => {
      // Set canvas dimensions
      canvas.width = img.width;
      canvas.height = img.height;
      
      // Draw image on canvas
      ctx.drawImage(img, 0, 0);
      
      // Convert to WebP blob
      canvas.toBlob((blob) => {
        if (blob) {
          resolve(blob);
        } else {
          reject(new Error('Failed to convert image to WebP'));
        }
      }, 'image/webp', quality);
    };
    
    img.onerror = () => {
      reject(new Error('Failed to load image'));
    };
    
    // Load the image
    img.src = URL.createObjectURL(file);
  });
};

const handleFileUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;
  
  // Validate file type
  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
  if (!allowedTypes.includes(file.type)) {
    alert('Format file tidak didukung. Harap pilih file JPEG atau PNG.');
    event.target.value = '';
    return;
  }
  
  // Validate file size (5MB)
  const maxSize = 5 * 1024 * 1024; // 5MB in bytes
  if (file.size > maxSize) {
    alert('Ukuran file terlalu besar. Maksimal 5MB.');
    event.target.value = '';
    return;
  }
  
  try {
    isProcessingImage.value = true;
    convertedImageInfo.value = null;
    
    // Convert to WebP
    const webpBlob = await convertImageToWebP(file, 0.8);
    
    // Create new File object from blob
    const webpFile = new File([webpBlob], 
      file.name.replace(/\.(jpg|jpeg|png)$/i, '.webp'), 
      { type: 'image/webp' }
    );
    
    // Calculate compression ratio
    const compressionRatio = Math.round(((file.size - webpBlob.size) / file.size) * 100);
    
    // Store conversion info
    convertedImageInfo.value = {
      originalSize: file.size,
      webpSize: webpBlob.size,
      compressionRatio: compressionRatio > 0 ? compressionRatio : 0
    };
    
    // Create a new event object with the converted file
    const newEvent = {
      target: {
        files: [webpFile]
      }
    };
    
    // Emit to parent component
    emit('file-upload', newEvent);
    
  } catch (error) {
    console.error('Error converting image:', error);
    alert('Gagal memproses gambar. Silakan coba lagi.');
    event.target.value = '';
  } finally {
    isProcessingImage.value = false;
  }
};
</script>

<style scoped>
.bg-light {
  background: transparent !important;
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
  width: 100%;
  font-size: clamp(0.9rem, 2.5vw, 1rem); 
  padding: clamp(0.5rem, 2vw, 0.75rem);
  height: auto;
  overflow-x: hidden;
  word-wrap: break-word;
}

.country-select-wrapper {
  position: relative;
  display: flex;
  align-items: center;
}

.flag-icon {
  width: clamp(16px, 2vw, 18px);
  height: clamp(12px, 1.5vw, 14px);
  border-radius: 2px;
  display: inline-block;
  background-size: cover;
  background-position: center;
}

.searchable-country-select {
  position: relative;
  min-width: clamp(80px, 10vw, 100px);
  max-width: clamp(100px, 15vw, 120px);
}

.selected-country {
  border: 1px solid #ced4da;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: system-ui, -apple-system, sans-serif;
  padding: clamp(0.5rem, 2vw, 0.75rem) clamp(0.5rem, 2vw, 1rem);
  font-size: clamp(0.8rem, 2vw, 0.9rem);
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  background-color: white;
  border-top-left-radius: 6px;
  border-bottom-left-radius: 6px;
}

.selected-country:hover {
  background-color: #f8f9fa;
  border-color: #adb5bd;
}

.dropdown-menu-custom {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ced4da;
  border-radius: 6px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  max-height: 280px;
  overflow: hidden;
}

.search-input-wrapper {
  position: relative;
  padding: 0.5rem;
  border-bottom: 1px solid #dee2e6;
}

.search-input {
  width: 100%;
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.5rem 2rem 0.5rem 0.75rem;
  font-size: 0.875rem;
}

.search-input:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  outline: none;
}

.search-icon {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  pointer-events: none;
}

.country-list {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 200px;
  overflow-y: auto;
}

.country-item {
  display: flex;
  align-items: center;
  padding: 0.5rem 0.75rem;
  text-decoration: none;
  color: #212529;
  font-size: 0.875rem;
  gap: 0.5rem;
  transition: background-color 0.15s ease;
  justify-content: flex-start;
}

.country-item:hover {
  background-color: #f8f9fa;
  color: #212529;
}

.country-code {
  color: #212529;
  font-weight: 500;
}

.no-results {
  padding: 1rem 0.75rem;
  text-align: center;
  color: #6c757d;
  font-style: italic;
  font-size: 0.875rem;
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

.custom-file-input .form-control:disabled {
  background-color: #f8f9fa;
  opacity: 0.65;
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

.input-group .country-select-wrapper {
  flex: 0 0 auto;
}

.input-group .form-control {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

/* Custom Scrollbar for country list */
.country-list::-webkit-scrollbar {
  width: 6px;
}

.country-list::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.country-list::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.country-list::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.country-list {
  scrollbar-width: thin;
  scrollbar-color: #c1c1c1 #f1f1f1;
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

/* Responsive media queries */
@media (min-width: 576px) {
  .form-container {
    max-width: 90%;
    padding: 2rem;
    margin: 0 auto;
  }
  .selected-country {
    font-size: clamp(0.85rem, 2.2vw, 0.95rem);
    padding: clamp(0.5rem, 2vw, 0.75rem);
  }
  .searchable-country-select {
    min-width: clamp(80px, 10vw, 100px);
    max-width: clamp(100px, 15vw, 120px);
  }
}

@media (min-width: 768px) {
  .form-container {
    max-width: 85%;
    padding: 2.5rem;
  }
  .selected-country {
    font-size: clamp(0.9rem, 2.2vw, 1rem);
    padding: clamp(0.6rem, 2vw, 0.8rem);
  }
}

@media (min-width: 992px) {
  .form-container {
    max-width: 580px;
    padding: 3rem;
  }
  .selected-country {
    font-size: clamp(0.95rem, 2.2vw, 1.05rem);
    padding: clamp(0.65rem, 2vw, 0.85rem);
  }
}

@media (min-width: 1200px) {
  .form-container {
    max-width: 620px;
  }
  .selected-country {
    font-size: clamp(1rem, 2.2vw, 1.1rem);
    padding: clamp(0.7rem, 2vw, 0.9rem);
  }
  .searchable-country-select {
    min-width: clamp(90px, 10vw, 110px);
    max-width: clamp(110px, 15vw, 130px);
  }
}

@media (min-width: 1400px) {
  .form-container {
    max-width: 680px;
  }
  .selected-country {
    font-size: clamp(1.05rem, 2.2vw, 1.15rem);
    padding: clamp(0.75rem, 2vw, 1rem);
  }
  .searchable-country-select {
    min-width: clamp(100px, 10vw, 120px);
    max-width: clamp(120px, 15vw, 140px);
  }
}

@media (max-width: 575.98px) {
  .form-container {
    padding: 1.5rem;
  }
  .selected-country {
    font-size: clamp(0.75rem, 2.5vw, 0.85rem);
    padding: clamp(0.3rem, 2vw, 0.5rem);
  }
  .searchable-country-select {
    min-width: clamp(60px, 10vw, 80px);
    max-width: clamp(80px, 15vw, 100px);
  }
  .flag-icon {
    width: clamp(14px, 2vw, 16px);
    height: clamp(10px, 1.5vw, 12px);
  }
}
</style>