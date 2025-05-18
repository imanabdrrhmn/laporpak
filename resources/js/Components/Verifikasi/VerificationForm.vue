<template>
  <div class="col-lg-6 d-flex align-items-center justify-content-center p-3 p-md-4 bg-light">
    <div class="form-container p-3 p-sm-4 p-lg-5 w-100">
      <h2 class="text-center mb-3 mb-md-4">Form Verifikasi Data</h2>
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
            class="form-select w-100" 
            id="serviceType" 
            v-model="selectedService" 
            :class="{ 'is-invalid': validation.serviceType }"
            aria-describedby="serviceTypeError"
            required
          >
            <option value="" disabled selected>-- Pilih Layanan Verifikasi --</option>
            <option v-for="service in services" :key="service.id" :value="service.id">
              {{ service.name }} ({{ service.price }} / {{ service.type }})
            </option>
          </select>
          <div id="serviceTypeError" class="invalid-feedback" v-if="validation.serviceType">{{ validation.serviceType }}</div>
        </div>
        
        <div v-if="selectedService" class="fade-in">
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
              Saya menyetujui <a href="#" class="text-primary">syarat dan ketentuan</a> yang berlaku
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
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, nextTick } from 'vue';

defineProps({
  services: Array
});

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

const showNpwpField = computed(() => {
  return selectedService.value && (selectedService.value.includes('npwp') || selectedService.value === 'income-tax');
});

const validateAndSubmit = () => {
  resetValidation();
  
  if (!selectedService.value) {
    validation.value.serviceType = 'Silakan pilih layanan verifikasi';
    formError.value = 'Harap lengkapi semua field yang diperlukan';
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
  
  submitForm();
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

const submitForm = () => {
  loading.value = true;
  
  setTimeout(() => {
    loading.value = false;
    
    const successMessage = `Verifikasi berhasil untuk layanan: ${getServiceName(selectedService.value)}`;
    alert(successMessage);
    
    console.log('Form Data:', {
      service: selectedService.value,
      ...formData.value
    });
    
    resetForm();
  }, 1500);
};

const resetForm = () => {
  formData.value = {
    fullName: '',
    idNumber: '',
    phoneNumber: '',
    npwpNumber: '',
    termsAgreed: false
  };
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

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 576px) {
  .form-container {
    width: 100%;
    margin: 0;
    border-radius: 10px;
  }
  
  .form-select {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
  }
}

@media (max-width: 360px) {
  .form-select {
    font-size: 0.85rem;
    padding: 0.4rem 0.6rem;
  }
}

@media (min-width: 768px) and (max-width: 991.98px) {
  .form-container {
    max-width: 90%;
    padding: 2rem !important;
  }
}

@media (min-width: 992px) {
  .bg-light {
    background: transparent !important;
  }
  
  .form-container {
    z-index: 2;
  }
}
</style>