<template>
  <div class="col-lg-6 d-flex align-items-center justify-content-end p-3 p-md-2 bg-light">
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
            <label for="source" class="form-label mb-2">Sumber Penipuan</label>
            <div class="input-group">
              <select 
                v-model="selectedCountry" 
                class="form-select country-select"
                @change="onCountryChange"
              >
                <option 
                  v-for="country in sortedCountries" 
                  :key="country.iso2"
                  :value="country"
                >
                  {{ getFlagEmoji(country.iso2) }} +{{ country.dialCode }}
                </option>
              </select>
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
                accept="image/*,.pdf"
                @change="$emit('file-upload', $event)"
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
</template>

<script setup>
import MapContainer from './MapContainer.vue';
import { ref, computed } from 'vue';
import { allCountries } from 'country-telephone-data';

const props = defineProps({
  selectedService: String,
  services: Array,
  serviceInfo: Object,
  currentCategories: Array,
  formData: Object,
  validationErrors: Object,
  isFormValid: Boolean
});

defineEmits(['select-service', 'submit-report', 'file-upload', 'validate-description', 'get-current-location']);

defineExpose({
  formRef: ref(null)
});

const selectedCountry = ref(allCountries.find(c => c.iso2 === 'id'));
const localPhoneNumber = ref('');

const getFlagEmoji = (countryCode) => {
  const codePoints = countryCode
    .toUpperCase()
    .split('')
    .map(char => 127397 + char.charCodeAt());
  return String.fromCodePoint(...codePoints);
};

const sortedCountries = computed(() => {
  return allCountries.sort((a, b) => {
    if (a.iso2 === 'id') return -1;
    if (b.iso2 === 'id') return 1;
    return a.name.localeCompare(b.name);
  });
});

const onCountryChange = () => {
  validatePhoneNumber();
};

const validatePhoneNumber = () => {
  const phoneNumber = localPhoneNumber.value.replace(/\D/g, '');
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
  } else {
    props.validationErrors.email = true;
  }
};

const onPhoneKeypress = (e) => {
  // Allow only numeric input
  if (!/[\d]/.test(e.key)) {
    e.preventDefault();
  }
};

const onPhoneInput = (e) => {
  // Remove any non-numeric characters that might have been pasted
  e.target.value = e.target.value.replace(/\D/g, '');
  localPhoneNumber.value = e.target.value;
  validatePhoneNumber();
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

.country-select {
  min-width: 140px;
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.input-group .country-select {
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.input-group .form-control {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
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

@media (min-width: 600px) and (max-width: 1199px) {
  .form-container {
    margin: 0 auto !important;
    max-width: 90% !important;
  }
  .bg-light {
    padding: 0 !important;
    display: flex;
    justify-content: center;
  }
}

@media (min-width: 650px) and (max-width: 767px) {
  .form-container {
    max-width: 92% !important;
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

@media (min-width: 1024px) and (max-width: 1100px) {
  .form-container {
    max-width: 75% !important;
    margin: 0 auto 2rem !important;
  }
}

@media (min-width: 992px) {
  .form-container {
    max-width: 580px;
    z-index: 2;
    padding: 3rem !important;
  }
}

@media (min-width: 1200px) {
  .form-container {
    max-width: 620px;
  }
}

@media (min-width: 1400px) {
  .form-container {
    max-width: 680px;
  }
}

@media (min-width: 600px) and (max-width: 1199px) {
  .service-btn, .submit-btn {
    min-height: 50px;
  }
  .form-container {
    padding: 2rem !important;
  }
}

@media (max-width: 320px) {
  .form-container {
    padding: 1rem !important;
  }
  .custom-select {
    font-size: 0.9rem;
    padding: 0.5rem;
    height: auto;
  }
}

@media (min-width: 321px) and (max-width: 375px) {
  .form-container {
    padding: 1.5rem !important;
  }
  .custom-select {
    font-size: 0.95rem;
    padding: 0.6rem;
    height: auto;
  }
}

@media (min-width: 376px) and (max-width: 576px) {
  .form-container {
    padding: 1.5rem !important;
  }
  .custom-select {
    font-size: 0.95rem;
    padding: 0.6rem;
    height: auto;
  }
}

@media (min-width: 576px) and (max-width: 767.98px) {
  .form-container {
    max-width: 90%;
    padding: 2rem !important;
  }
  .custom-select {
    font-size: 1rem;
    padding: 0.75rem;
    height: auto;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .form-container {
    max-width: 85%;
    padding: 2.5rem !important;
  }
  .custom-select {
    font-size: 1.05rem;
    padding: 0.8rem;
    height: auto;
  }
}

@media (min-width: 1200px) {
  .custom-select {
    font-size: 1.15rem;
    padding: 0.9rem;
    height: auto;
  }
}

@media (min-width: 1400px) {
  .custom-select {
    font-size: 1.2rem;
    padding: 1rem;
    height: auto;
  }
}

@media (max-height: 600px) and (orientation: landscape) {
  .form-container {
    padding: 2rem !important;
  }
  .custom-select {
    font-size: 0.9rem;
    padding: 0.5rem;
    height: auto;
  }
}

@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
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