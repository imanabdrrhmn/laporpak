```vue
<template>
  <AppLayout>
    <Head title="Verifikasi"/>
    <div class="hero-section">
      <div class="container-fluid px-0">
        <div class="row g-0 min-vh-100">
          <!-- Left Side - Text Content -->
          <div class="col-lg-6 d-flex flex-column justify-content-center text-white hero-content p-4 p-sm-5">
            <h1 class="display-4 fw-bold mb-3">Verifikasi Data Terpercaya</h1>
            <div class="highlight-bar mb-4"></div>
            <p class="lead mb-4">Akses layanan verifikasi data premium untuk memastikan keabsahan dan menjaga integritas informasi bisnis Anda. Solusi cepat dan akurat untuk kebutuhan verifikasi.</p>
            
            <!-- Features for desktop -->
            <div class="d-none d-lg-block">
              <div class="row mt-4 g-3">
                <div class="col-md-4">
                  <div class="feature-box p-3">
                    <i class="bi bi-shield-check fs-2 mb-2"></i>
                    <h5>Keamanan</h5>
                    <p class="small">Data terenkripsi dan terlindungi dengan standar tinggi</p>
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
            
            <!-- Features for mobile/tablet -->
            <div class="d-block d-lg-none mt-4">
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
          
          <!-- Right Side - Form -->
          <div class="col-lg-6 d-flex align-items-center justify-content-center p-3 p-md-4 bg-light">
            <div class="form-container p-3 p-sm-4 p-lg-5 w-100">
              <h2 class="text-center mb-3 mb-md-4">Form Verifikasi Data</h2>
              <p class="text-center text-muted mb-4">Pilih jenis layanan terlebih dahulu untuk melanjutkan pengisian formulir.</p>
              
              <!-- Global Alert -->
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
                    :class="{'is-invalid': validation.serviceType}"
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
                        :class="{'is-invalid': validation.fullName}"
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
                        :class="{'is-invalid': validation.idNumber}"
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
                        :class="{'is-invalid': validation.phoneNumber}"
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
                        :class="{'is-invalid': validation.npwpNumber}"
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
                      :class="{'is-invalid': validation.termsAgreed}"
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
        </div>
      </div>
    </div>

    <Alur/>
    <!-- SECTION COMPONENT -->
    <Section />
    
    <!-- FEEDBACK COMPONENT -->
    <Feedback /> 
  </AppLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Alur from '@/Components/alurverifikasi.vue'; 
import Section from '@/Components/Section.vue';
import Feedback from '@/Components/Feedback.vue';

export default {
  components: {
    AppLayout,
    Head,
    Alur,
    Section,
    Feedback
  },
  data() {
    return {
      services: [
        { id: 'npwp-v2', name: 'NPWP Check V2', price: 'IDR 7,200', type: 'Success' },
        { id: 'income-tax', name: 'Income Tax Grade', price: 'IDR 10,000', type: 'Success' },
        { id: 'nik-trace', name: 'Digital NIK Trace (NIK to Phone)', price: 'IDR 10,000', type: 'Success' },
        { id: 'phone-trace', name: 'Phone Trace (Phone to NIK)', price: 'IDR 10,000', type: 'Success' },
        { id: 'blacklist-v1', name: 'Blacklist V1', price: 'IDR 45,000', type: 'Success' },
        { id: 'multi-apply', name: 'Multi-Apply Feature Set', price: 'IDR 3,600', type: 'Query' },
        { id: 'multi-loan', name: 'Multi-Loan Feature Set', price: 'IDR 3,600', type: 'Query' },
        { id: 'portrait', name: 'Portrait Feature Set', price: 'IDR 5,600', type: 'Query' },
        { id: 'telco', name: 'Telco Feature Set', price: 'IDR 3,600', type: 'Query' },
        { id: 'sms', name: 'SMS Feature Set', price: 'IDR 4,800', type: 'Success' },
        { id: 'npwp-v1', name: 'NPWP Check V1', price: 'IDR 7,200', type: 'Success' }
      ],
      selectedService: '',
      formData: {
        fullName: '',
        idNumber: '',
        phoneNumber: '',
        npwpNumber: '',
        termsAgreed: false
      },
      validation: {
        serviceType: '',
        fullName: '',
        idNumber: '',
        phoneNumber: '',
        npwpNumber: '',
        termsAgreed: ''
      },
      formError: '',
      loading: false,
      successMessage: ''
    }
  },
  computed: {
    showNpwpField() {
      return this.selectedService && (this.selectedService.includes('npwp') || this.selectedService === 'income-tax');
    }
  },
  methods: {
    validateAndSubmit() {
      // Reset validation messages
      this.resetValidation();
      
      // Check if service is selected
      if (!this.selectedService) {
        this.validation.serviceType = 'Silakan pilih layanan verifikasi';
        this.formError = 'Harap lengkapi semua field yang diperlukan';
        return;
      }
      
      // Validate required fields
      let isValid = true;
      
      if (!this.formData.fullName.trim()) {
        this.validation.fullName = 'Nama lengkap wajib diisi';
        isValid = false;
      }
      
      if (!this.formData.idNumber.trim()) {
        this.validation.idNumber = 'Nomor NIK wajib diisi';
        isValid = false;
      } else if (this.formData.idNumber.trim().length !== 16) {
        this.validation.idNumber = 'Nomor NIK harus 16 digit';
        isValid = false;
      }
      
      if (!this.formData.phoneNumber.trim()) {
        this.validation.phoneNumber = 'Nomor handphone wajib diisi';
        isValid = false;
      } else if (!/^08[0-9]{8,11}$/.test(this.formData.phoneNumber.trim())) {
        this.validation.phoneNumber = 'Format nomor handphone tidak valid';
        isValid = false;
      }
      
      if (this.showNpwpField && !this.formData.npwpNumber.trim()) {
        this.validation.npwpNumber = 'Nomor NPWP wajib diisi';
        isValid = false;
      }
      
      if (!this.formData.termsAgreed) {
        this.validation.termsAgreed = 'Anda harus menyetujui syarat dan ketentuan';
        isValid = false;
      }
      
      // Show global error message if any validation failed
      if (!isValid) {
        this.formError = 'Harap lengkapi semua field yang diperlukan';
        // Scroll to the top of the form to see the error message
        this.$nextTick(() => {
          const alertElement = document.querySelector('.alert');
          if (alertElement) {
            alertElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
          }
        });
        return;
      }
      
      // If all validations pass, submit the form
      this.submitForm();
    },
    
    resetValidation() {
      this.formError = '';
      this.validation = {
        serviceType: '',
        fullName: '',
        idNumber: '',
        phoneNumber: '',
        npwpNumber: '',
        termsAgreed: ''
      };
    },
    
    submitForm() {
      this.loading = true;
      
      // Simulate API call
      setTimeout(() => {
        this.loading = false;
        
        // Success message
        this.successMessage = `Verifikasi berhasil untuk layanan: ${this.getServiceName(this.selectedService)}`;
        alert(this.successMessage);
        
        // In a real application, you would send the data to your backend here
        console.log('Form Data:', {
          service: this.selectedService,
          ...this.formData
        });
        
        // Reset form after successful submission
        this.resetForm();
        
      }, 1500);
    },
    
    resetForm() {
      this.formData = {
        fullName: '',
        idNumber: '',
        phoneNumber: '',
        npwpNumber: '',
        termsAgreed: false
      };
      // Keep the selected service to improve UX
    },
    
    getServiceName(serviceId) {
      const service = this.services.find(s => s.id === serviceId);
      return service ? service.name : serviceId;
    }
  }
}
</script>

<style scoped>
.hero-section {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  min-height: 100vh;
  overflow: hidden;
  position: relative;
}

.hero-content {
  position: relative;
  z-index: 1;
}

.highlight-bar {
  height: 4px;
  width: 80px;
  background-color: #ffc107;
  border-radius: 2px;
}

.form-container {
  background: white;
  border-radius: 15px;
  max-width: 580px;
  box-shadow: 0 15px 50px rgba(0, 0, 0, 0.1);
}

.feature-box {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  backdrop-filter: blur(10px);
  transition: transform 0.3s;
}

.feature-box:hover {
  transform: translateY(-5px);
}

.mobile-feature {
  background: rgba(255, 255, 255, 0.1);
  border-radius: 8px;
  backdrop-filter: blur(5px);
  padding: 0.75rem !important;
  min-height: 60px;
}

.fade-in {
  animation: fadeIn 0.5s;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Ensure minimum font size for accessibility */
.small {
  font-size: 0.875rem; /* 14px minimum */
}

/* Improve touch target for checkbox */
.form-check-label {
  padding-left: 0.5rem;
}

/* Extra small devices (phones, 576px and down) */
@media (max-width: 576px) {
  .hero-content {
    padding: 2rem 1rem !important;
    text-align: center;
  }
  
  .highlight-bar {
    margin: 0 auto;
  }
  
  .form-container {
    width: 100%;
    margin: 0;
    border-radius: 10px;
  }
  
  .display-4 {
    font-size: 2rem;
  }
  
  .lead {
    font-size: 1rem;
  }
  
  /* Adjust dropdown size for mid-tier mobile */
  .form-select {
    font-size: 0.9rem;
    padding: 0.5rem 0.75rem;
  }
}

/* Very small devices (<360px) to prevent overflow */
@media (max-width: 360px) {
  .lead,
  .display-4 {
    word-break: break-word;
    hyphens: auto;
  }
  
  .form-select {
    font-size: 0.85rem;
    padding: 0.4rem 0.6rem;
  }
}

/* Small devices (tablets, 576px and up) */
@media (min-width: 576px) and (max-width: 767.98px) {
  .hero-content {
    padding: 2.5rem 1.5rem !important;
    text-align: center;
  }
  
  .highlight-bar {
    margin: 0 auto;
  }
  
  .display-4 {
    font-size: 2.5rem;
  }
}

/* Medium devices (tablets, 768px and up) */
@media (min-width: 768px) and (max-width: 991.98px) {
  .hero-content {
    padding: 3rem 2rem !important;
    text-align: center;
  }
  
  .highlight-bar {
    margin: 0 auto;
  }
  
  .form-container {
    max-width: 90%;
    padding: 2rem !important;
  }
}

/* Large devices (desktops, 992px and up) */
@media (min-width: 992px) {
  .hero-content {
    padding-left: 4rem !important;
  }
  
  .bg-light {
    background: transparent !important;
  }
  
  .form-container {
    z-index: 2;
  }
}

/* For devices in landscape mode */
@media (max-height: 600px) and (orientation: landscape) {
  .min-vh-100 {
    min-height: auto !important;
  }
  
  .hero-section {
    min-height: auto;
    overflow-y: auto;
  }
}
</style>
```