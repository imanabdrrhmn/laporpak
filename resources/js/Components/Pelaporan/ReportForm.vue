<template>
  <div class="col-lg-6 d-flex align-items-center justify-content-end p-3 p-md-2 bg-light">
    <link 
      rel="stylesheet" 
      href="https://cdnjs.cloudflare.com/ajax/libs/flag-icons/6.6.6/css/flag-icons.min.css" 
    />
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

      <form ref="formRef" @submit.prevent="submitForm">
        <div class="row g-3">
          <div class="col-12">
            <label for="category" class="form-label mb-2">Kategori</label>
            <select
              id="category"
              v-model="formData.category"
              class="form-select custom-select"
              :class="{'is-invalid': validationErrors.category}"
              aria-label="Kategori"
              required
              @change="validationErrors.category = false"
            >
              <option disabled value="">Pilih Kategori</option>
              <option v-for="category in currentCategories" :key="category.value" :value="category.value">
                {{ category.label }}
              </option>
            </select>
            <div v-if="validationErrors.category" class="invalid-feedback">
              Kategori harus dipilih
            </div>
          </div>

          <div v-if="selectedService === 'Penipuan' && formData.category === 'Email'" class="col-12">
            <label for="email" class="form-label mb-2">Alamat Email</label>
            <input
              id="email"
              v-model="formData.email"
              type="email"
              class="form-control"
              :class="{'is-invalid': validationErrors.email}"
              placeholder="Masukkan alamat email"
              required
              @input="validateEmail"
            />
            <div v-if="validationErrors.email" class="invalid-feedback">
              Alamat email tidak valid
            </div>
          </div>

          <div v-if="selectedService === 'Penipuan' && formData.category === 'Nomor Telepon'" class="col-12">
            <label for="phone" class="form-label mb-2">Nomor Telepon</label>
            <div class="integrated-phone-input">
              <div ref="phoneInputWrapper" class="phone-input-wrapper" :class="{'is-invalid': validationErrors.source}">
                <div ref="countrySelector" class="country-selector" @click="toggleDropdown">
                  <span :class="`fi fi-${selectedCountry.iso2.toLowerCase()}`" class="flag-icon"></span>
                  <span class="dial-code">+{{ selectedCountry.dialCode }}</span>
                  <i class="bi bi-chevron-down dropdown-arrow" :class="{ 'rotated': showDropdown }"></i>
                </div>
                <input
                  id="phone"
                  v-model="localPhoneNumber"
                  type="tel"
                  class="phone-number-input"
                  placeholder="Masukkan nomor telepon"
                  required
                  @input="onPhoneInput"
                  @keypress="onPhoneKeypress"
                  @focus="onPhoneFocus"
                  @blur="onPhoneBlur"
                />
                <div v-show="showDropdown" ref="dropdownMenu" class="country-dropdown-menu">
                  <div class="search-wrapper">
                    <input ref="searchInput" v-model="countrySearch" type="text" class="country-search-input" placeholder="Cari kode negara" @input="filterCountries" />
                    <i class="bi bi-search search-icon"></i>
                  </div>
                  <ul class="countries-list">
                    <li v-for="country in filteredCountries" :key="country.iso2">
                      <button type="button" class="country-option" @click="selectCountry(country)">
                        <span :class="`fi fi-${country.iso2.toLowerCase()}`" class="flag-icon"></span>
                        <span class="country-info">
                          <span class="country-name">{{ country.name }}</span>
                          <span class="country-dial-code">+{{ country.dialCode }}</span>
                        </span>
                      </button>
                    </li>
                    <li v-if="filteredCountries.length === 0" class="no-results">
                      Kode negara tidak ditemukan
                    </li>
                  </ul>
                </div>
              </div>
              <div v-if="validationErrors.source" class="invalid-feedback">
                Nomor telepon tidak valid
              </div>
            </div>
          </div>

          <div v-if="selectedService === 'Penipuan' && formData.category === 'Tautan'" class="col-12">
            <label for="url" class="form-label mb-2">Tautan / URL</label>
            <input
              id="url"
              v-model="formData.source"
              type="url"
              class="form-control"
              :class="{'is-invalid': validationErrors.source}"
              placeholder="https://contoh-link-berbahaya.com"
              required
              @input="onUrlInput"
              @blur="onUrlBlur"
            />
            <div v-if="validationErrors.source" class="invalid-feedback">
              Tautan / URL tidak valid
            </div>
          </div>

          <div class="col-12">
            <label for="evidence" class="form-label mb-2">Bukti</label>
            <div class="input-group custom-file-input">
              <input
                id="evidence"
                ref="fileInput"
                type="file"
                class="form-control"
                accept="image/jpeg,image/jpg,image/png"
                :disabled="isProcessingImage"
                aria-label="Bukti"
                @change="handleFileUpload"
              />
              <span class="input-group-text py-0 px-2">
                <i v-if="!isProcessingImage" class="bi bi-image text-primary"></i>
                <div v-else class="spinner-border spinner-border-sm text-primary" role="status">
                  <span class="visually-hidden">Loading...</span>
                </div>
              </span>
            </div>
            <div class="form-text">
              <div class="d-flex justify-content-between align-items-center">
                <span>Format: JPEG, PNG (Max: 5MB)</span>
                <span v-if="isProcessingImage" class="text-primary">
                  <small>Memproses gambar...</small>
                </span>
                <span v-if="uploadedFile && !isProcessingImage" class="text-success">
                  <small><i class="bi bi-check-circle me-1"></i>Siap dikirim</small>
                </span>
              </div>
            </div>
            <div v-if="previewUrl" class="mt-3">
              <div class="image-preview">
                <img :src="previewUrl" alt="Preview" class="img-fluid rounded" style="max-height: 200px; max-width: 100%;">
                <button type="button" class="btn btn-sm btn-outline-danger mt-2" @click="removeImage">
                  <i class="bi bi-trash me-1"></i>Hapus Gambar
                </button>
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
              maxlength="1500"
              required
              aria-label="Deskripsi"
              @input="$emit('validate-description')"
              @focus="validationErrors.description = false"
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
              :class="{ 'is-invalid': validationErrors.region }"
              aria-label="Provinsi"
              required
              @change="validationErrors.region = false"
            >
              <option disabled value="">Pilih Wilayah</option>
              <option v-for="province in provinces" :key="province" :value="province" placeholder="Pilih Wilayah">
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
                <i class="bi bi-send me-2"></i> 
                {{ isProcessingImage ? 'Memproses...' : 'Kirim Laporan' }}
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
import { ref, computed, onMounted, onUnmounted, nextTick, watch } from 'vue';
import { allCountries } from 'country-telephone-data';

// Props & Emits
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

const emit = defineEmits([
  'select-service',
  'submit-report',
  'file-upload',
  'validate-description',
  'get-current-location'
]);

// State
const formRef = ref(null);
const isProcessingImage = ref(false);
const uploadedFile = ref(null);
const previewUrl = ref('');
const fileInput = ref(null);

// State untuk Input Telepon
const selectedCountry = ref(allCountries.find(c => c.iso2 === 'id') || allCountries[0]);
const localPhoneNumber = ref('');
const showDropdown = ref(false);
const countrySearch = ref('');
const filteredCountries = ref([]);
const searchInput = ref(null);
const phoneInputWrapper = ref(null);
const dropdownMenu = ref(null);

// Flag untuk mencegah duplikasi
const isUrlUpdating = ref(false);

const sortedCountries = computed(() => {
  return [...allCountries].sort((a, b) => {
    if (a.iso2 === 'id') return -1;
    if (b.iso2 === 'id') return 1;
    return a.name.localeCompare(b.name);
  });
});

// Watcher untuk membersihkan input saat kategori berubah
watch(() => props.formData.category, (newCategory, oldCategory) => {
  if (newCategory !== oldCategory) {
    // Reset semua input field
    props.formData.source = '';
    props.formData.email = '';
    localPhoneNumber.value = '';
    
    // Reset semua validation errors
    props.validationErrors.source = false;
    props.validationErrors.email = false;
    
    // Reset flag
    isUrlUpdating.value = false;
  }
});

// Watcher untuk field source (URL) untuk mencegah duplikasi
watch(() => props.formData.source, (newValue) => {
  if (props.formData.category === 'Tautan' && newValue && !isUrlUpdating.value) {
    // Jika ada perubahan dan bukan dari fungsi validateUrl
    validateUrl();
  }
});

// Fungsi Konversi Gambar
const convertToWebP = (file, quality = 0.8) => {
  return new Promise((resolve, reject) => {
    const canvas = document.createElement('canvas');
    const ctx = canvas.getContext('2d');
    const img = new Image();

    img.onload = () => {
      canvas.width = img.width;
      canvas.height = img.height;
      ctx.drawImage(img, 0, 0);
      canvas.toBlob(
        (blob) => {
          if (blob) {
            const webpFile = new File(
              [blob],
              file.name.replace(/\.(jpg|jpeg|png)$/i, '.webp'),
              { type: 'image/webp' }
            );
            resolve(webpFile);
          } else {
            reject(new Error('Gagal mengkonversi gambar'));
          }
        },
        'image/webp',
        quality
      );
    };
    img.onerror = () => reject(new Error('Gagal memuat gambar'));
    img.src = URL.createObjectURL(file);
  });
};

// Fungsi Handler untuk Upload & Gambar
const handleFileUpload = async (event) => {
  const file = event.target.files[0];
  if (!file) return;

  const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
  if (!allowedTypes.includes(file.type)) {
    alert('Format file tidak didukung. Gunakan JPEG atau PNG.');
    resetFileInput();
    return;
  }

  const maxSize = 5 * 1024 * 1024; // 5MB
  if (file.size > maxSize) {
    alert('Ukuran file terlalu besar. Maksimal 5MB.');
    resetFileInput();
    return;
  }

  try {
    isProcessingImage.value = true;
    const webpFile = await convertToWebP(file, 0.8);
    previewUrl.value = URL.createObjectURL(webpFile);
    uploadedFile.value = webpFile;
    emit('file-upload', { target: { files: [webpFile] } });
  } catch (error) {
    console.error('Error converting image:', error);
    alert('Gagal memproses gambar. Silakan coba lagi.');
    resetFileInput();
  } finally {
    isProcessingImage.value = false;
  }
};

const removeImage = () => {
  uploadedFile.value = null;
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
    previewUrl.value = '';
  }
  resetFileInput();
  emit('file-upload', { target: { files: [] } });
};

const resetFileInput = () => {
  if (fileInput.value) fileInput.value.value = '';
};

// Fungsi Telepon & Dropdown Negara
const filterCountries = () => {
  const search = countrySearch.value.toLowerCase();
  if (!search) {
    filteredCountries.value = sortedCountries.value;
    return;
  }
  filteredCountries.value = sortedCountries.value.filter(country =>
    country.dialCode.includes(search) || country.name.toLowerCase().includes(search)
  );
};

const toggleDropdown = async (event) => {
  event.stopPropagation();
  showDropdown.value = !showDropdown.value;
  if (showDropdown.value) {
    countrySearch.value = '';
    filteredCountries.value = sortedCountries.value;
    await nextTick();
    if (searchInput.value) searchInput.value.focus();
  }
};

const selectCountry = (country) => {
  selectedCountry.value = country;
  showDropdown.value = false;
  validatePhoneNumber();
};

const handleClickOutside = (event) => {
  if (phoneInputWrapper.value && !phoneInputWrapper.value.contains(event.target)) {
    showDropdown.value = false;
  }
};

// Fungsi Validasi Input - DIPERBAIKI
const validatePhoneNumber = () => {
  const phoneNumber = localPhoneNumber.value.replace(/\D/g, '');
  if (!phoneNumber || phoneNumber.length === 0) {
    props.validationErrors.source = true;
    props.formData.source = '';
    return;
  }
  
  const isValid = phoneNumber.length >= 8 && phoneNumber.length <= 15;
  if (isValid) {
    props.formData.source = `+${selectedCountry.value.dialCode}${phoneNumber}`;
    props.validationErrors.source = false;
  } else {
    props.validationErrors.source = true;
    props.formData.source = '';
  }
};

const validateEmail = () => {
  const email = props.formData.email;
  if (!email || email.trim() === '') {
    props.validationErrors.email = true;
    return;
  }
  
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const isValid = emailRegex.test(email.trim());
  props.validationErrors.email = !isValid;
};

// Fungsi Validasi URL yang DIPERBAIKI
const validateUrl = () => {
  const url = props.formData.source;
  
  // Set flag untuk mencegah loop
  isUrlUpdating.value = true;
  
  try {
    // Jika URL kosong, set invalid
    if (!url || url.trim() === '') {
      props.validationErrors.source = true;
      return;
    }
    
    let urlToValidate = url.trim();
    
    // Cek apakah URL sudah memiliki protokol
    if (!urlToValidate.match(/^https?:\/\//i)) {
      urlToValidate = 'https://' + urlToValidate;
    }
    
    // Gunakan URL constructor untuk validasi
    const urlObject = new URL(urlToValidate);
    
    // Validasi hostname
    if (!urlObject.hostname || urlObject.hostname.length < 1) {
      props.validationErrors.source = true;
      return;
    }
    
    // Validasi domain (harus ada titik atau localhost)
    if (!urlObject.hostname.includes('.') && urlObject.hostname !== 'localhost') {
      props.validationErrors.source = true;
      return;
    }
    
    // Validasi protokol
    if (!['http:', 'https:'].includes(urlObject.protocol)) {
      props.validationErrors.source = true;
      return;
    }
    
    // Validasi berhasil
    props.validationErrors.source = false;
    
    // Update formData hanya jika berbeda untuk mencegah duplikasi
    if (props.formData.source !== urlToValidate) {
      props.formData.source = urlToValidate;
    }
    
  } catch (error) {
    // Fallback dengan regex jika URL constructor gagal
    try {
      let urlToValidate = url.trim();
      
      // Tambahkan protokol jika tidak ada
      if (!urlToValidate.match(/^https?:\/\//i)) {
        urlToValidate = 'https://' + urlToValidate;
      }
      
      // Regex untuk validasi URL yang lebih fleksibel
      const urlRegex = /^(https?:\/\/)?([\da-z\.-]+)\.([a-z\.]{2,6})([\/\w \.\-~:?#\[\]@!$&'()*+,;=%]*)*\/?$/i;
      const isValid = urlRegex.test(urlToValidate);
      
      if (isValid) {
        props.validationErrors.source = false;
        if (props.formData.source !== urlToValidate) {
          props.formData.source = urlToValidate;
        }
      } else {
        props.validationErrors.source = true;
      }
      
    } catch (regexError) {
      props.validationErrors.source = true;
    }
  } finally {
    // Reset flag setelah validasi selesai
    setTimeout(() => {
      isUrlUpdating.value = false;
    }, 100);
  }
};

// Event Handler untuk Input Telepon
const onPhoneKeypress = (e) => {
  // Hanya izinkan angka
  if (!/[\d]/.test(e.key)) {
    e.preventDefault();
  }
};

const onPhoneInput = (e) => {
  const sanitizedValue = e.target.value.replace(/\D/g, '');
  e.target.value = sanitizedValue;
  localPhoneNumber.value = sanitizedValue;
  validatePhoneNumber();
};

const onPhoneFocus = () => {
  // Kosongkan untuk menjaga dropdown tetap terbuka
};

const onPhoneBlur = () => {
  // Delay validasi untuk mencegah conflict dengan dropdown
  setTimeout(() => {
    validatePhoneNumber();
  }, 100);
};

// Event Handler untuk Input URL - DIPERBAIKI
const onUrlInput = (e) => {
  const value = e.target.value;
  
  // Update formData langsung tanpa validasi untuk mencegah duplikasi
  if (!isUrlUpdating.value) {
    props.formData.source = value;
    
    // Validasi dengan delay untuk performa
    setTimeout(() => {
      validateUrl();
    }, 300);
  }
};

const onUrlBlur = () => {
  // Validasi langsung saat blur
  validateUrl();
};

// Fungsi Form
const submitForm = () => {
  if (isProcessingImage.value) {
    alert('Tunggu hingga proses gambar selesai.');
    return;
  }
  
  // Validasi final sebelum submit
  if (props.formData.category === 'Tautan') {
    validateUrl();
    if (props.validationErrors.source) {
      return;
    }
  } else if (props.formData.category === 'Email') {
    validateEmail();
    if (props.validationErrors.email) {
      return;
    }
  } else if (props.formData.category === 'Nomor Telepon') {
    validatePhoneNumber();
    if (props.validationErrors.source) {
      return;
    }
  }
  
  emit('submit-report');
};

const resetForm = () => {
  if (formRef.value) formRef.value.reset();
  
  // Reset semua data form
  Object.keys(props.formData).forEach((key) => {
    if (key === 'category') {
      props.formData[key] = '';
    } else {
      props.formData[key] = '';
    }
  });
  
  // Reset state telepon
  localPhoneNumber.value = '';
  selectedCountry.value = allCountries.find(c => c.iso2 === 'id') || allCountries[0];
  countrySearch.value = '';
  filteredCountries.value = sortedCountries.value;
  showDropdown.value = false;
  
  // Reset gambar
  removeImage();
  
  // Reset validasi
  Object.keys(props.validationErrors).forEach((key) => {
    props.validationErrors[key] = false;
  });
  
  // Reset flag
  isUrlUpdating.value = false;
};

// Lifecycle Hooks
onMounted(() => {
  filteredCountries.value = sortedCountries.value;
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  if (previewUrl.value) {
    URL.revokeObjectURL(previewUrl.value);
  }
});

// Expose functions untuk parent component
defineExpose({ 
  formRef, 
  resetForm, 
  validateUrl, 
  validateEmail, 
  validatePhoneNumber,
  onUrlInput,
  onUrlBlur
});
</script>

<style scoped>
/* SEMUA STYLE ANDA TETAP DI SINI, TIDAK ADA YANG DIUBAH */
.bg-light {
  background: transparent !important;
}

.form-container {
  background: white;
  border-radius: 15px;
  max-width: 100%;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
  padding: 1.5rem;
  overflow: visible;
  position: relative;
  z-index: 1;
}

.form-control,
.form-select {
  font-size: 1rem;
  padding: 0.75rem;
  border-radius: 6px;
  position: relative;
  z-index: 2;
  height: 48px;
  /* Fixed height untuk konsistensi */
}

.form-control:focus,
.form-select:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
  z-index: 10;
}

.custom-select {
  border: 1px solid #ced4da;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 100%;
  font-size: 1rem;
  padding: 0.75rem;
  height: 48px;
  /* Fixed height yang sama */
  overflow-x: hidden;
  word-wrap: break-word;
  position: relative;
  z-index: 2;
}

/* Country Dropdown Fixes */
.country-select-wrapper {
  position: relative;
  display: flex;
  align-items: center;
  z-index: 100;
  flex-shrink: 0;
}

.flag-icon {
  width: 18px;
  height: 14px;
  border-radius: 2px;
  display: inline-block;
  background-size: cover;
  background-position: center;
  flex-shrink: 0;
  margin-right: 0 !important;
  /* Remove any margin */
}

.searchable-country-select {
  position: relative;
  width: 120px;
  /* Increased width untuk desktop */
  z-index: 100;
}

.selected-country {
  border: 1px solid #ced4da;
  cursor: pointer;
  transition: all 0.2s ease;
  font-family: system-ui, -apple-system, sans-serif;
  padding: 0.75rem;
  /* Same padding as form controls */
  font-size: 1rem;
  /* Same font size */
  display: flex;
  align-items: center;
  justify-content: space-between;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  background-color: white;
  border-top-left-radius: 6px;
  border-bottom-left-radius: 6px;
  position: relative;
  z-index: 100;
  white-space: nowrap;
  height: 48px;
  /* Same height as form controls */
  line-height: 1;
  width: 100%;
  box-sizing: border-box;
  gap: 0.25rem;
  /* REDUCED: Smaller gap between elements */
}

.selected-country:hover {
  background-color: #f8f9fa;
  border-color: #adb5bd;
}

.selected-country:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Dropdown Menu - Critical Z-index Fix */
.dropdown-menu-custom {
  position: absolute;
  top: 100%;
  left: 0;
  right: auto;
  background: white;
  border: 1px solid #ced4da;
  border-radius: 6px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  z-index: 99999 !important;
  max-height: 280px;
  overflow: hidden;
  min-width: 280px;
  transform: translateZ(0);
  -webkit-transform: translateZ(0);
}

/* Mobile specific positioning */
@media (max-width: 575.98px) {
  .dropdown-menu-custom {
    left: -10px;
    min-width: 280px;
    transform: translateX(0) !important;
  }
}

.search-input-wrapper {
  position: relative;
  padding: 0.5rem;
  border-bottom: 1px solid #dee2e6;
  background: white;
  z-index: 100000;
}

.search-input {
  width: 100%;
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.5rem 2rem 0.5rem 0.75rem;
  font-size: 0.875rem;
  position: relative;
  z-index: 100001;
}

.search-input:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  outline: none;
  z-index: 100002;
}

.search-icon {
  position: absolute;
  right: 1rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  pointer-events: none;
  z-index: 100003;
}

.country-list {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 200px;
  overflow-y: auto;
  background: white;
  z-index: 99998;
  position: relative;
}

.country-list li {
  position: relative;
  z-index: 99999;
}

.country-item {
  display: flex;
  align-items: center;
  padding: 0.5rem 0.75rem;
  text-decoration: none;
  color: #212529;
  font-size: 0.875rem;
  gap: 0.25rem;
  /* REDUCED: Much smaller gap between flag and country code */
  transition: background-color 0.15s ease;
  justify-content: flex-start;
  cursor: pointer;
  position: relative;
  z-index: 99999;
  width: 100%;
  border: none;
  background: transparent;
}

.country-item:hover {
  background-color: #f8f9fa;
}

.country-item .flag-icon {
  margin-right: 0 !important;
  /* REMOVED: No extra margin */
  flex-shrink: 0;
}

.country-code {
  color: #212529;
  font-weight: 500;
  margin-left: 0;
  white-space: nowrap;
}

.no-results {
  padding: 1rem 0.75rem;
  text-align: center;
  color: #6c757d;
  font-style: italic;
  font-size: 0.875rem;
}

/* Input Group Fixes - IMPORTANT */
.input-group {
  position: relative;
  z-index: 50;
  display: flex;
  flex-wrap: nowrap;
  align-items: stretch;
  width: 100%;
}

.input-group .country-select-wrapper {
  flex: 0 0 120px;
  /* Fixed width untuk consistency */
  z-index: 100;
}

.input-group .form-control {
  border-top-left-radius: 6px !important;
  border-bottom-left-radius: 6px !important;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  border-right: none;
  flex: 1;
  min-width: 0;
  border-left: 1px solid #ced4da !important;
  border-color: #ced4da !important;
  margin-left: 0 !important;
  height: 48px;
  /* Same height as country selector */
  padding: 0.75rem;
  /* Same padding */
  font-size: 1rem;
  /* Same font size */
  box-sizing: border-box;
}

/* FIXED: Custom File Input - Complete styling */
.custom-file-input {
  border-radius: 6px;
  overflow: hidden;
  position: relative;
  z-index: 2;
  display: flex;
  align-items: stretch;
  width: 100%;
}

.custom-file-input .form-control {
  border-top-left-radius: 6px !important;
  border-bottom-left-radius: 6px !important;
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
  border-right: none;
  flex: 1;
  min-width: 0;
  border-left: 1px solid #ced4da !important;
  /* Tampilkan border kiri kembali */
  border-color: #ced4da !important;
  /* Pastikan warna border sama */
  margin-left: 0 !important;
  /* Hilangkan margin negatif dari .input-group */
}

.custom-file-input .form-control:disabled {
  background-color: #e9ecef;
  opacity: 0.65;
}

.custom-file-input .form-control:focus {
  z-index: 10;
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* FIXED: Input group text styling */
.custom-file-input .input-group-text {
  background-color: #f8f9fa;
  border: 1px solid #ced4da;
  border-left: none;
  border-top-right-radius: 6px;
  border-bottom-right-radius: 6px;
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 0.75rem;
  height: 48px;
  min-width: 48px;
  position: relative;
  z-index: 2;
}

.custom-file-input .input-group-text:hover {
  background-color: #e9ecef;
}

/* Textarea and other form elements */
.custom-textarea {
  border: 1px solid #ced4da;
  border-radius: 6px;
  padding: 0.75rem;
  min-height: 100px;
  word-break: break-word;
  hyphens: auto;
  position: relative;
  z-index: 2;
  resize: vertical;
  font-size: 1rem;
}

.custom-textarea:focus {
  z-index: 10;
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.custom-textarea.border-danger {
  border-color: #dc3545 !important;
  animation: shake 0.5s linear;
}

.image-preview {
  text-align: center;
  padding: 1rem;
  border: 2px dashed #dee2e6;
  border-radius: 6px;
  background-color: #f8f9fa;
}

/* Service buttons */
.service-btn {
  border-radius: 6px;
  font-weight: 500;
  transition: all 0.3s ease;
  padding: 0.75rem 1rem;
  min-height: 48px;
  position: relative;
  z-index: 2;
}

.service-btn:hover:not(.btn-primary) {
  background-color: #f8f9fa;
  border-color: #6c757d;
}

.service-btn.btn-primary {
  box-shadow: 0 2px 8px rgba(13, 110, 253, 0.3);
}

.service-btn:focus {
  z-index: 10;
}

/* Submit button */
.submit-btn {
  border-radius: 6px;
  font-weight: 500;
  background: linear-gradient(135deg, #0d6efd, #0b5ed7);
  border: none;
  box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
  transition: all 0.3s ease;
  padding: 1rem;
  min-height: 52px;
  position: relative;
  z-index: 2;
}

.submit-btn:hover:not(:disabled) {
  transform: translateY(-2px);
  box-shadow: 0 6px 12px rgba(13, 110, 253, 0.4);
}

.submit-btn:focus {
  z-index: 10;
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

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}

/* Scrollbar styling */
.country-list,
.custom-select,
.custom-textarea {
  scrollbar-width: thin;
  scrollbar-color: #c1c1c1 #f1f1f1;
}

.country-list::-webkit-scrollbar,
.custom-select::-webkit-scrollbar,
.custom-textarea::-webkit-scrollbar {
  width: 6px;
}

.country-list::-webkit-scrollbar-track,
.custom-select::-webkit-scrollbar-track,
.custom-textarea::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.country-list::-webkit-scrollbar-thumb,
.custom-select::-webkit-scrollbar-thumb,
.custom-textarea::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.country-list::-webkit-scrollbar-thumb:hover,
.custom-select::-webkit-scrollbar-thumb:hover,
.custom-textarea::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Animations */
@keyframes shake {
  0% {
    transform: translateX(0);
  }
  20% {
    transform: translateX(-5px);
  }
  40% {
    transform: translateX(5px);
  }
  60% {
    transform: translateX(-3px);
  }
  80% {
    transform: translateX(3px);
  }
  100% {
    transform: translateX(0);
  }
}

@keyframes btn-wave {
  0% {
    transform: translateX(-100%) rotate(45deg);
  }
  100% {
    transform: translateX(100%) rotate(45deg);
  }
}

.btn-animation {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, rgba(255, 255, 255, 0) 0%, rgba(255, 255, 255, 0.2) 50%, rgba(255, 255, 255, 0) 100%);
  transform: translateX(-100%);
  animation: btn-wave 2s infinite;
}

/* Media Queries for Responsive Design */
@media (min-width: 576px) {
  .form-container {
    max-width: 90%;
    padding: 2rem;
    margin: 0 auto;
  }
  .searchable-country-select {
    width: 130px;
  }
  .input-group .country-select-wrapper {
    flex: 0 0 130px;
  }
}

@media (min-width: 768px) {
  .form-container {
    max-width: 85%;
    padding: 2.5rem;
  }
  .searchable-country-select {
    width: 140px;
  }
  .input-group .country-select-wrapper {
    flex: 0 0 140px;
  }
  .dropdown-menu-custom {
    left: 0;
    min-width: 280px;
  }
}

@media (min-width: 992px) {
  .form-container {
    max-width: 580px;
    padding: 3rem;
  }
  .searchable-country-select {
    width: 150px;
  }
  .input-group .country-select-wrapper {
    flex: 0 0 150px;
  }
}

@media (min-width: 1200px) {
  .form-container {
    max-width: 620px;
  }
  .searchable-country-select {
    width: 160px;
  }
  .input-group .country-select-wrapper {
    flex: 0 0 160px;
  }
}

@media (min-width: 1400px) {
  .form-container {
    max-width: 680px;
  }
  .searchable-country-select {
    width: 170px;
  }
  .input-group .country-select-wrapper {
    flex: 0 0 170px;
  }
}

/* Mobile Optimizations */
@media (max-width: 575.98px) {
  .form-container {
    padding: 1.5rem;
  }
  .form-control,
  .form-select,
  .selected-country {
    height: 44px;
    font-size: 0.9rem;
    padding: 0.6rem;
  }
  .selected-country {
    padding: 0.6rem 0.5rem;
    gap: 0.15rem;
    /* REDUCED: Even smaller gap on mobile */
  }
  .searchable-country-select {
    width: 100px;
  }
  .flag-icon {
    width: 16px;
    height: 12px;
  }
  .input-group .country-select-wrapper {
    flex: 0 0 100px;
  }
  .input-group .form-control {
    height: 44px;
    padding: 0.6rem;
    font-size: 0.9rem;
  }
  .custom-file-input .input-group-text {
    height: 44px;
    min-width: 44px;
    padding: 0.6rem;
  }
  .dropdown-menu-custom {
    left: -10px;
    min-width: 280px;
  }
  .country-item {
    gap: 0.15rem;
  }
}

/* Additional Mobile Touch Improvements */
@media (hover: none) and (pointer: coarse) {
  .country-item {
    padding: 0.75rem;
    min-height: 44px;
  }
  .selected-country {
    min-height: 44px;
  }
  .form-control,
  .form-select {
    min-height: 44px;
  }
}

/* Global fixes */
* {
  box-sizing: border-box;
}

input,
select,
textarea,
button {
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
}

/* Ensure all interactive elements are accessible */
.form-control,
.form-select,
.btn,
.selected-country,
.country-item {
  pointer-events: auto !important;
  user-select: auto !important;
  -webkit-user-select: auto !important;
  -moz-user-select: auto !important;
  -ms-user-select: auto !important;
}

/* Fix for overlay issues */
.dropdown-menu-custom,
.country-list,
.country-item {
  transform: translateZ(0);
  -webkit-transform: translateZ(0);
  will-change: transform;
}

/* Integrated Phone Input Styles */
.integrated-phone-input {
  position: relative;
  width: 100%;
}

.phone-input-wrapper {
  position: relative;
  display: flex;
  align-items: stretch;
  border: 1px solid #ced4da;
  border-radius: 6px;
  background: white;
  transition: all 0.2s ease;
  height: 48px;
  overflow: visible;
}

.phone-input-wrapper:hover {
  border-color: #adb5bd;
}

.phone-input-wrapper:focus-within {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.phone-input-wrapper.is-invalid {
  border-color: #dc3545;
}

.phone-input-wrapper.is-invalid:focus-within {
  border-color: #dc3545;
  box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

.country-selector {
  display: flex;
  align-items: center;
  padding: 0 0.75rem;
  border-top-left-radius: 5px;
  border-bottom-left-radius: 5px;
  background-color: #f8f9fa;
  border-right: 1px solid #dee2e6;
  cursor: pointer;
  min-width: 100px;
  gap: 0.5rem;
  transition: background-color 0.2s ease;
  user-select: none;
  position: relative;
  z-index: 10;
}

.country-selector:hover {
  background-color: #e9ecef;
}

.flag-icon {
  width: 20px;
  height: 15px;
  border-radius: 2px;
  display: inline-block;
  background-size: cover;
  background-position: center;
  flex-shrink: 0;
}

.dial-code {
  font-weight: 500;
  color: #495057;
  font-size: 0.9rem;
  white-space: nowrap;
}

.dropdown-arrow {
  font-size: 0.75rem;
  color: #6c757d;
  transition: transform 0.2s ease;
  margin-left: auto;
}

.dropdown-arrow.rotated {
  transform: rotate(180deg);
}

.phone-number-input {
  flex: 1;
  border: none;
  outline: none;
  padding: 0 0.75rem;
  font-size: 1rem;
  background: transparent;
  color: #495057;
  height: 100%;
  min-width: 0;
}

.phone-number-input::placeholder {
  color: #6c757d;
  opacity: 0.7;
}

.phone-number-input:focus {
  outline: none;
}

.country-dropdown-menu {
  position: absolute;
  top: calc(100% + 4px);
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
  z-index: 1000;
  max-height: 300px;
  overflow: hidden;
  animation: dropdownFadeIn 0.2s ease;
}

/* Custom scrollbar for country dropdown, match with .custom-select */
.country-dropdown-menu .countries-list {
  scrollbar-width: thin;
  scrollbar-color: #c1c1c1 #f1f1f1;
}

.country-dropdown-menu .countries-list::-webkit-scrollbar {
  width: 6px;
}

.country-dropdown-menu .countries-list::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.country-dropdown-menu .countries-list::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.country-dropdown-menu .countries-list::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

.search-wrapper {
  position: relative;
  padding: 0.75rem;
  border-bottom: 1px solid #e9ecef;
  background: #f8f9fa;
}

.country-search-input {
  width: 100%;
  border: 1px solid #ced4da;
  border-radius: 4px;
  padding: 0.5rem 2rem 0.5rem 0.75rem;
  font-size: 0.875rem;
  outline: none;
  transition: border-color 0.2s ease;
  background: #fff;
  /* Set background to white */
  color: #212529;
  /* Pastikan warna teks gelap */
}

/* White scrollbar for country-search-input */
.country-search-input::-webkit-scrollbar {
  width: 6px;
  background: #fff;
}

.country-search-input::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.country-search-input::-webkit-scrollbar-track {
  background: #fff;
}

.country-search-input {
  scrollbar-width: thin;
  scrollbar-color: #c1c1c1 #fff;
}

.search-icon {
  position: absolute;
  right: 1.25rem;
  top: 50%;
  transform: translateY(-50%);
  color: #6c757d;
  font-size: 0.875rem;
  pointer-events: none;
}

.countries-list {
  list-style: none;
  padding: 0;
  margin: 0;
  max-height: 200px;
  overflow-y: auto;
}

.country-option {
  display: flex;
  align-items: center;
  width: 100%;
  padding: 0.75rem;
  border: none;
  background: none;
  cursor: pointer;
  text-align: left;
  gap: 0.75rem;
  transition: background-color 0.15s ease;
}

.country-option:hover {
  background-color: #f8f9fa;
}

.country-option:focus {
  outline: none;
  background-color: #e7f3ff;
}

.country-info {
  display: flex;
  flex-direction: column;
  flex: 1;
  min-width: 0;
}

.country-name {
  font-size: 0.875rem;
  color: #212529;
  font-weight: 500;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.country-dial-code {
  font-size: 0.75rem;
  color: #6c757d;
  margin-top: 2px;
}

.no-results {
  padding: 1rem 0.75rem;
  text-align: center;
  color: #6c757d;
  font-style: italic;
  font-size: 0.875rem;
}

.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875rem;
  color: #dc3545;
}

@media (max-width: 575.98px) {
  .country-selector {
    min-width: 85px;
    padding: 0 0.5rem;
    gap: 0.25rem;
  }
  .dial-code {
    font-size: 0.8rem;
  }
  .flag-icon {
    width: 18px;
    height: 13px;
  }
  .country-dropdown-menu {
    left: -10px;
    right: -10px;
  }
  .phone-input-wrapper {
    height: 44px;
  }
  .phone-number-input {
    font-size: 0.9rem;
    padding: 0 0.5rem;
  }
}

@media (min-width: 768px) {
  .country-selector {
    min-width: 110px;
  }
  .country-dropdown-menu {
    max-height: 320px;
  }
}

@media (min-width: 992px) {
  .country-selector {
    min-width: 120px;
  }
}

@media (hover: none) and (pointer: coarse) {
  .country-option {
    padding: 1rem 0.75rem;
    min-height: 48px;
  }
  .phone-input-wrapper {
    min-height: 48px;
  }
}

.phone-input-wrapper:focus-within .country-selector {
  background-color: #e7f3ff;
}

.country-selector,
.phone-number-input,
.country-option {
  transition: all 0.2s ease;
}

@media (prefers-contrast: high) {
  .phone-input-wrapper {
    border-width: 2px;
  }
  .country-selector {
    border-right-width: 2px;
  }
}

@media (prefers-reduced-motion: reduce) {
  .dropdown-arrow,
  .country-dropdown-menu,
  .country-selector,
  .phone-number-input,
  .country-option {
    transition: none;
  }
  .country-dropdown-menu {
    animation: none;
  }
}
</style>