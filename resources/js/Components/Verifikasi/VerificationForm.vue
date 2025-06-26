<template>
  <div class="col-lg-6 d-flex align-items-center justify-content-center p-3 p-md-4 bg-light">
    <div class="form-container p-3 p-sm-4 p-lg-5 w-100">
      <h2 class="text-center mb-3 mb-md-4"><i class="bi bi-person-vcard me-2"></i>Formulir Verifikasi Data</h2>
      <p class="text-center text-muted mb-4">Pilih jenis layanan terlebih dahulu untuk melanjutkan pengisian formulir.</p>
      
      <div class="alert alert-danger alert-dismissible fade show" role="alert" v-if="formError" aria-live="polite">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ formError }}
        <button type="button" class="btn-close" @click="formError = ''" aria-label="Tutup"></button>
      </div>
      
      <form @submit.prevent="validateAndSubmit" novalidate>
        <div class="mb-4">
          <label for="serviceType" class="form-label fw-bold">Pilih Layanan:</label>
          <select 
            class="form-select custom-select w-100" 
            id="serviceType" 
            v-model="selectedService" 
            :class="{ 'is-invalid': validation.serviceType }"
            aria-describedby="serviceTypeError"
            required
          >
            <option value="" disabled selected>-- Pilih Layanan Verifikasi --</option>
            <option 
              v-for="service in services" 
              :key="service.id" 
              :value="service.id"
              :disabled="service.comingSoon"
            >
              {{ service.name }} ({{ service.price }} / {{ service.type }})
              <span v-if="service.comingSoon"> - Coming Soon</span>
            </option>
          </select>
          <div id="serviceTypeError" class="invalid-feedback" v-if="validation.serviceType">{{ validation.serviceType }}</div>
        </div>
        
        <div v-if="selectedService && !isServiceComingSoon" class="fade-in">
          <div class="mb-3">
            <label for="fullName" class="form-label">Nama Lengkap</label>
            <div class="input-group has-validation">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <input 
                type="text" 
                class="form-control" 
                id="fullName" 
                placeholder="Masukkan nama lengkap" 
                v-model="formData.fullName"
                :class="{ 'is-invalid': validation.fullName }"
                aria-describedby="fullNameError"
                required
              >
              <div id="fullNameError" class="invalid-feedback" v-if="validation.fullName">{{ validation.fullName }}</div>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="idNumber" class="form-label">Nomor NIK</label>
            <div class="input-group has-validation">
              <span class="input-group-text"><i class="bi bi-card-text"></i></span>
              <input 
                type="text" 
                class="form-control" 
                id="idNumber" 
                placeholder="Masukkan 16 digit NIK" 
                v-model="formData.idNumber"
                :class="{ 'is-invalid': validation.idNumber }"
                aria-describedby="idNumberError"
                required 
                maxlength="16"
                @input="(e) => restrictToNumbers(e, 'idNumber')"
              >
              <div id="idNumberError" class="invalid-feedback" v-if="validation.idNumber">{{ validation.idNumber }}</div>
            </div>
          </div>
          
          <div class="mb-3">
            <label for="phoneNumber" class="form-label">Nomor Handphone</label>
            <div class="input-group has-validation">
              <span class="input-group-text"><i class="bi bi-phone"></i></span>
              <input 
                type="tel" 
                class="form-control" 
                id="phoneNumber" 
                placeholder="Contoh: 08123456789" 
                v-model="formData.phoneNumber"
                :class="{ 'is-invalid': validation.phoneNumber }"
                aria-describedby="phoneNumberError"
                required
                @input="(e) => restrictToNumbers(e, 'phoneNumber')"
              >
              <div id="phoneNumberError" class="invalid-feedback" v-if="validation.phoneNumber">{{ validation.phoneNumber }}</div>
            </div>
          </div>
          
          <div class="mb-4" v-if="showNpwpField">
            <label for="npwpNumber" class="form-label">Nomor NPWP</label>
            <div class="input-group has-validation">
              <span class="input-group-text"><i class="bi bi-file-earmark-text"></i></span>
              <input 
                type="text" 
                class="form-control" 
                id="npwpNumber" 
                placeholder="Masukkan nomor NPWP" 
                v-model="formData.npwpNumber"
                :class="{ 'is-invalid': validation.npwpNumber }"
                aria-describedby="npwpNumberError"
                required
                @input="(e) => restrictToNumbers(e, 'npwpNumber')"
              >
              <div id="npwpNumberError" class="invalid-feedback" v-if="validation.npwpNumber">{{ validation.npwpNumber }}</div>
            </div>
          </div>
          
          <div class="mb-3 form-check">
            <input 
              type="checkbox" 
              class="form-check-input" 
              id="termsCheck" 
              v-model="formData.termsAgreed"
              :class="{ 'is-invalid': validation.termsAgreed }"
              aria-describedby="termsCheckError"
              required
            >
            <label class="form-check-label small" for="termsCheck">
              Saya menyetujui <a href="#" class="text-primary" @click.prevent="showTermsModal">syarat dan ketentuan</a> yang berlaku
            </label>
            <div id="termsCheckError" class="invalid-feedback" v-if="validation.termsAgreed">{{ validation.termsAgreed }}</div>
          </div>
          
          <div class="d-grid gap-2">
            <button type="submit" class="btn btn-primary btn-lg" :disabled="loading">
              <span v-if="loading" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
              <i v-else class="bi bi-search me-2"></i> 
              {{ loading ? 'Memproses...' : 'Cek Sekarang' }}
            </button>
          </div>
        </div>

        <!-- Coming Soon Message -->
        <div v-if="selectedService && isServiceComingSoon" class="fade-in">
          <div class="alert alert-info text-center py-4">
            <i class="bi bi-clock-history fs-1 text-info mb-3 d-block"></i>
            <h5 class="mb-2">Layanan Segera Hadir</h5>
            <p class="mb-0 text-muted">
              Layanan <strong>{{ getServiceName(selectedService) }}</strong> sedang dalam tahap pengembangan dan akan segera tersedia. 
              Silakan pilih layanan lain yang tersedia saat ini.
            </p>
          </div>
        </div>
      </form>

      <!-- Render Terms Modal -->
      <TermsModal
        :is-visible="isTermsModalVisible"
        @close="closeTermsModal"
      />

      <!-- Render Verification Modal -->
      <VerificationModal
        :is-visible="isVerificationModalVisible"
        :biaya-verifikasi="biayaVerifikasi"
        :is-verifying="isVerifying"
        @close="closeVerificationModal"
        @verify="handleVerification"
      />

      <!-- Render Success Modal -->
      <SuccessModal
        :is-visible="isSuccessModalVisible"
        @navigate="navigateToReports"
      />
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import TermsModal from './Modals/TermsModal.vue';
import VerificationModal from './Modals/VerificationModal.vue';
import SuccessModal from './Modals/SuccessModal.vue';

const props = defineProps({
  services: Array
});

// Form state
const selectedService = ref('');
const formData = ref({
  fullName: '',
  idNumber: '',
  phoneNumber: '',
  npwpNumber: '',
  termsAgreed: false
});
const validation = ref({
  serviceType: '',
  fullName: '',
  idNumber: '',
  phoneNumber: '',
  npwpNumber: '',
  termsAgreed: ''
});
const formError = ref('');
const loading = ref(false);

// Modal state
const isTermsModalVisible = ref(false);
const isVerificationModalVisible = ref(false);
const isSuccessModalVisible = ref(false);
const isVerifying = ref(false);
const biayaVerifikasi = ref(3600); // Default verification cost

const showNpwpField = computed(() => {
  return selectedService.value && (selectedService.value.includes('npwp') || selectedService.value === 'income-tax');
});

const isServiceComingSoon = computed(() => {
  const service = props.services.find(s => s.id === selectedService.value);
  return service ? service.comingSoon : false;
});

// Restrict input to numbers only
const restrictToNumbers = (event, field) => {
  formData.value[field] = event.target.value.replace(/[^0-9]/g, '');
};

// Show terms modal
const showTermsModal = () => {
  console.log('Opening terms modal');
  isTermsModalVisible.value = true;
};

// Close terms modal
const closeTermsModal = () => {
  isTermsModalVisible.value = false;
};

const validateAndSubmit = () => {
  resetValidation();
  
  if (!selectedService.value) {
    validation.value.serviceType = 'Silakan pilih layanan verifikasi';
    formError.value = 'Harap lengkapi semua field yang diperlukan';
    return;
  }

  if (isServiceComingSoon.value) {
    formError.value = 'Layanan yang dipilih belum tersedia';
    return;
  }
  
  let isValid = true;
  
  if (!formData.value.fullName.trim()) {
    validation.value.fullName = 'Nama lengkap wajib diisi';
    isValid = false;
  }
  
  if (!formData.value.idNumber.trim()) {
    validation.value.idNumber = 'Nomor NIK wajib diisi';
    isValid = false;
  } else if (formData.value.idNumber.trim().length !== 16) {
    validation.value.idNumber = 'Nomor NIK harus 16 digit';
    isValid = false;
  }
  
  if (!formData.value.phoneNumber.trim()) {
    validation.value.phoneNumber = 'Nomor handphone wajib diisi';
    isValid = false;
  } else if (!/^08[0-9]{8,11}$/.test(formData.value.phoneNumber.trim())) {
    validation.value.phoneNumber = 'Format nomor handphone tidak valid';
    isValid = false;
  }
  
  if (showNpwpField.value && !formData.value.npwpNumber.trim()) {
    validation.value.npwpNumber = 'Nomor NPWP wajib diisi';
    isValid = false;
  }
  
  if (!formData.value.termsAgreed) {
    validation.value.termsAgreed = 'Anda harus menyetujui syarat dan ketentuan';
    isValid = false;
  }
  
  if (!isValid) {
    formError.value = 'Harap lengkapi semua field yang diperlukan';
    nextTick(() => {
      const alertElement = document.querySelector('.alert');
      if (alertElement) {
        alertElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }
    });
    return;
  }
  
  showVerificationModal();
};

const resetValidation = () => {
  formError.value = '';
  validation.value = {
    serviceType: '',
    fullName: '',
    idNumber: '',
    phoneNumber: '',
    npwpNumber: '',
    termsAgreed: ''
  };
};

// Modal functions
const showVerificationModal = () => {
  isVerificationModalVisible.value = true;
};

const closeVerificationModal = () => {
  isVerificationModalVisible.value = false;
};

const handleVerification = () => {
  isVerifying.value = true;
  
  setTimeout(() => {
    isVerifying.value = false;
    closeVerificationModal();
    
    setTimeout(() => {
      isSuccessModalVisible.value = true;
      resetForm();
    }, 400);
  }, 2000);
};

const navigateToReports = () => {
  isSuccessModalVisible.value = false;
  setTimeout(() => {
    router.visit('/laporan-saya');
  }, 300);
};

const resetForm = () => {
  formData.value = {
    fullName: '',
    idNumber: '',
    phoneNumber: '',
    npwpNumber: '',
    termsAgreed: false
  };
  selectedService.value = '';
};

const getServiceName = (serviceId) => {
  const service = props.services.find(s => s.id === serviceId);
  return service ? service.name : serviceId;
};
</script>

<style scoped>
.form-container {
  background: white;
  border-radius: 15px;
  max-width: 580px;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
  position: relative;
  z-index: 1;
}

.fade-in {
  animation: fadeIn 0.5s;
}

.form-check-label {
  padding-left: 0.5rem;
}

.small {
  font-size: 0.875rem;
}

/* Fix dropdown for mobile */
.form-select.custom-select {
  position: relative;
  z-index: 10;
  -webkit-appearance: none;
  -moz-appearance: none;
  appearance: none;
  background-color: #fff;
  background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e");
  background-repeat: no-repeat;
  background-position: right 0.75rem center;
  background-size: 16px 12px;
  border: 1px solid #ced4da;
  border-radius: 0.375rem;
  padding: 0.75rem 2rem 0.75rem 0.75rem;
  font-size: 1rem;
  font-weight: 400;
  line-height: 1.5;
  color: #212529;
  cursor: pointer;
  transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-select.custom-select:focus {
  border-color: #86b7fe;
  outline: 0;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

.form-select.custom-select:disabled {
  background-color: #e9ecef;
  opacity: 1;
}

option {
  color: #212529;
  background-color: #fff;
  padding: 0.5rem;
}

option:disabled {
  color: #6c757d;
  font-style: italic;
  background-color: #f8f9fa;
}

option:hover {
  background-color: #f8f9fa;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Mobile optimizations */
@media (max-width: 576px) {
  .form-container {
    width: 100%;
    margin: 0;
    border-radius: 10px;
    padding: 1.5rem !important;
    min-height: auto;
  }
  
  .form-select.custom-select {
    font-size: 16px; /* Prevent zoom on iOS */
    padding: 0.75rem 2.5rem 0.75rem 0.75rem;
    height: auto;
    min-height: 48px; /* Better touch target */
    background-size: 14px 10px;
    background-position: right 0.75rem center;
  }
  
  /* Ensure dropdown is clickable */
  .form-select.custom-select option {
    font-size: 16px;
    padding: 0.75rem;
    min-height: 48px;
  }
  
  .form-label {
    font-size: 0.95rem;
    font-weight: 600;
  }
}

@media (max-width: 360px) {
  .form-container {
    padding: 1rem !important;
  }
  
  .form-select.custom-select {
    font-size: 15px;
    padding: 0.6rem 2.2rem 0.6rem 0.6rem;
    background-size: 12px 8px;
  }
}

/* Tablet optimization */
@media (min-width: 768px) and (max-width: 991.98px) {
  .form-container {
    max-width: 90%;
    padding: 2rem !important;
  }
  
  .form-select.custom-select {
    font-size: 1rem;
  }
}

/* Desktop optimization */
@media (min-width: 992px) {
  .bg-light {
    background: transparent !important;
  }
  
  .form-container {
    z-index: 2;
    position: relative;
  }
  
  .form-select.custom-select {
    font-size: 1rem;
  }
}

/* Fix for iOS Safari */
@supports (-webkit-touch-callout: none) {
  .form-select.custom-select {
    font-size: 16px !important; /* Prevent zoom */
  }
}

/* Additional mobile touch improvements */
@media (max-width: 768px) {
  .form-select.custom-select {
    transform: translate3d(0, 0, 0); /* Force hardware acceleration */
    -webkit-tap-highlight-color: transparent;
  }
  
  /* Ensure proper stacking context */
  .mb-4:has(.form-select.custom-select) {
    position: relative;
    z-index: 10;
  }
}
</style>