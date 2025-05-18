<template>
  <AppLayout>
  <Head title="Verifikasi Nomor" />
  <div class="page-wrapper p-0">
    <div class="card-container">
      <!-- Card content with improved design -->
      <div class="card">
        <!-- WhatsApp icon -->
        <div class="icon-container">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" class="whatsapp-icon">
            <!-- WhatsApp standard icon from Font Awesome -->
            <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
          </svg>
        </div>
        
        <h1 class="title">Verifikasi Nomor WhatsApp</h1>
        <div class="divider"></div>
        <p class="subtitle">Masukkan kode OTP yang telah dikirimkan ke nomor WhatsApp Anda</p>

        <!-- OTP input container -->
        <form @submit.prevent="submit">
          <div class="otp-container">
            <label for="code" class="input-label">Kode Verifikasi</label>
            <div class="otp-input-wrapper">
              <input
                id="code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                v-model="form.code"
                class="otp-input"
                maxlength="6"
                autocomplete="one-time-code"
                placeholder="Masukkan kode OTP"
                @input="validateNumericInput"
              />
            </div>
            <div v-if="form.errors.code" class="error-text">
              {{ form.errors.code }}
            </div>
          </div>

          <!-- Status indicators -->
          <div v-if="successMessage" class="status-box success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="status-icon">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <p class="success-text">{{ successMessage }}</p>
          </div>

          <div v-if="errorMessage" class="status-box error">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="status-icon">
              <circle cx="12" cy="12" r="10"></circle>
              <line x1="15" y1="9" x2="9" y2="15"></line>
              <line x1="9" y1="9" x2="15" y2="15"></line>
            </svg>
            <p class="error-text">{{ errorMessage }}</p>
          </div>

          <div class="button-group">
            <button
              type="submit"
              class="btn-verify"
              :disabled="form.processing"
            >
              <span v-if="form.processing" class="loading-spinner"></span>
              <span v-else>Verifikasi</span>
            </button>

            <button
              type="button"
              class="btn-resend"
              @click="resendCode"
              :disabled="resending"
            >
              <span v-if="resending" class="loading-spinner loading-spinner-small"></span>
              <span v-else>Kirim Ulang Kode</span>
            </button>
          </div>
        </form>
        
        <!-- Help options -->
        <div class="help-section">
          <p class="help-text">Butuh bantuan? <a href="#" class="help-link">Hubungi support</a></p>
        </div>
      </div>
    </div>
  </div>
</AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router, usePage, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({ code: '' })

const page = usePage();
page.layout = AppLayout;

const successMessage = ref('')
const errorMessage = ref('')
const resending = ref(false)

// Function to validate numeric input
const validateNumericInput = (event) => {
  // Replace any non-numeric characters with empty string
  form.code = form.code.replace(/[^0-9]/g, '')
}

const submit = () => {
  form.post('/phone/verify', {
    preserveScroll: true,
    onSuccess: () => {
      successMessage.value = 'Verifikasi berhasil!'
      errorMessage.value = ''
      router.visit('/dashboard')
    },
    onError: () => {
      errorMessage.value = form.errors.code || 'Kode salah atau kadaluarsa.'
    }
  })
}

const resendCode = async () => {
  resending.value = true
  try {
    await axios.post('/phone/send-verification')
    successMessage.value = 'Kode verifikasi berhasil dikirim ulang.'
    errorMessage.value = ''
  } catch (e) {
    errorMessage.value = 'Gagal mengirim ulang kode. Coba beberapa saat lagi.'
  } finally {
    resending.value = false
  }
}
</script>

<style scoped>
/* Full page wrapper */
.page-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #2563EB, #3B82F6);
  padding: 2rem;
}

.card-container {
  width: 100%;
  max-width: 480px;
  perspective: 1000px;
}

/* Card style with improved design */
.card {
  background: #ffffff;
  border-radius: 24px;
  box-shadow: 0 15px 35px rgba(0,0,0,0.2);
  padding: 2.5rem;
  width: 100%;
  text-align: center;
  transform-style: preserve-3d;
  transition: transform 0.5s ease, box-shadow 0.5s ease;
  animation: cardFadeIn 0.8s ease-out;
}

@keyframes cardFadeIn {
  0% {
    opacity: 0;
    transform: translateY(20px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Icon container */
.icon-container {
  display: flex;
  justify-content: center;
  margin-bottom: 1.5rem;
}

.whatsapp-icon {
  width: 68px;
  height: 68px;
  color: #25D366;
  filter: drop-shadow(0 2px 8px rgba(37, 211, 102, 0.3));
  animation: pulse 2s infinite;
}

@keyframes pulse {
  0% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.05);
  }
  100% {
    transform: scale(1);
  }
}

/* Typography */
.title {
  font-size: 2rem;
  font-weight: bold;
  color: #111827;
  margin-bottom: 1rem;
}

.divider {
  height: 3px;
  width: 60px;
  background: linear-gradient(90deg, #2563EB, #3B82F6);
  margin: 0 auto 1.5rem auto;
  border-radius: 3px;
}

.subtitle {
  font-size: 1.1rem;
  color: #4B5563;
  line-height: 1.6;
  margin-bottom: 2rem;
}

/* OTP Input styling */
.otp-container {
  margin-bottom: 1.5rem;
}

.input-label {
  display: block;
  text-align: left;
  font-size: 0.95rem;
  font-weight: 600;
  color: #4B5563;
  margin-bottom: 0.75rem;
}

.otp-input-wrapper {
  position: relative;
}

.otp-input {
  width: 100%;
  padding: 1.25rem;
  border: 2px solid #E2E8F0;
  border-radius: 12px;
  font-size: 1.25rem;
  letter-spacing: 4px;
  text-align: center;
  font-weight: 600;
  color: #1E293B;
  background-color: #F8FAFC;
  transition: all 0.3s ease;
}

.otp-input:focus {
  outline: none;
  border-color: #3B82F6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
  background-color: #FFFFFF;
}

.otp-input::placeholder {
  letter-spacing: normal;
  font-weight: normal;
  color: #94A3B8;
}

.error-text {
  margin-top: 0.5rem;
  color: #EF4444;
  font-size: 0.9rem;
  text-align: left;
}

/* Status boxes */
.status-box {
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  border-radius: 12px;
  margin: 1.5rem 0;
  animation: fadeIn 0.5s ease-out;
}

.status-box.success {
  background-color: rgba(16, 185, 129, 0.1);
  border: 1px solid rgba(16, 185, 129, 0.3);
}

.status-box.error {
  background-color: rgba(239, 68, 68, 0.1);
  border: 1px solid rgba(239, 68, 68, 0.3);
}

.status-icon {
  width: 24px;
  height: 24px;
  margin-right: 10px;
}

.status-box.success .status-icon {
  color: #10B981;
}

.status-box.error .status-icon {
  color: #EF4444;
}

.success-text {
  color: #10B981;
  font-weight: 600;
  margin: 0;
}

.error-text {
  color: #EF4444;
  font-weight: 600;
  margin: 0;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

/* Button group */
.button-group {
  display: flex;
  flex-direction: column;
  gap: 1rem;
  margin-top: 1.5rem;
}

/* Button styles */
.btn-verify {
  background: linear-gradient(90deg, #2563EB, #3B82F6);
  color: white;
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  font-size: 1rem;
  cursor: pointer;
  transition: all 0.3s ease;
  width: 100%;
  position: relative;
  overflow: hidden;
  display: flex;
  justify-content: center;
  align-items: center;
}

.btn-verify:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transform: translateX(-100%);
}

.btn-verify:hover:not(:disabled):before {
  animation: shimmer 1.5s infinite;
}

.btn-verify:disabled {
  background: linear-gradient(90deg, #93C5FD, #BFDBFE);
  cursor: not-allowed;
}

.btn-verify:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 7px 14px rgba(37, 99, 235, 0.3);
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

.btn-resend {
  background: transparent;
  color: #2563EB;
  border: 1px solid #2563EB;
  padding: 0.75rem 1.5rem;
  border-radius: 12px;
  font-weight: 600;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.3s ease;
}

.btn-resend:hover:not(:disabled) {
  background-color: rgba(37, 99, 235, 0.1);
  transform: translateY(-2px);
}

.btn-resend:disabled {
  color: #94A3B8;
  border-color: #CBD5E1;
  cursor: not-allowed;
}

/* Loading spinner */
.loading-spinner {
  display: inline-block;
  width: 24px;
  height: 24px;
  border: 3px solid rgba(255,255,255,0.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
}

.loading-spinner-small {
  width: 18px;
  height: 18px;
  border-width: 2px;
  border: 2px solid rgba(37, 99, 235, 0.3);
  border-top-color: #2563EB;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Help section */
.help-section {
  margin-top: 2rem;
  padding-top: 1.5rem;
  border-top: 1px solid #E5E7EB;
}

.help-text {
  color: #6B7280;
  font-size: 0.95rem;
}

.help-link {
  color: #2563EB;
  text-decoration: none;
  font-weight: 600;
  transition: color 0.3s ease;
}

.help-link:hover {
  color: #1D4ED8;
  text-decoration: underline;
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .card {
    padding: 2rem 1.5rem;
  }
  
  .title {
    font-size: 1.75rem;
  }
  
  .subtitle {
    font-size: 1rem;
  }
}
</style>