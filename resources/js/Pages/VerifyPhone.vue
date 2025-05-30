<template>
  <AppLayout>
    <Head title="Verifikasi Nomor" />
    <div class="page-wrapper">
      <div class="card-container">
        <div class="card">
          <!-- WhatsApp Icon -->
          <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor" class="whatsapp-icon">
              <path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7.9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-3.9-10.5-6.6z"/>
            </svg>
          </div>

          <!-- Title -->
          <h1 class="title">Verifikasi Nomor</h1>

          <!-- Form -->
          <form @submit.prevent="submit" class="content">
            <!-- Description -->
            <p class="description">
              Masukkan kode OTP yang telah dikirimkan ke nomor WhatsApp Anda
            </p>

            <!-- OTP Input -->
            <div class="input-group">
              <label for="code" class="input-label">Kode Verifikasi</label>
              <input
                id="code"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                v-model="form.code"
                class="otp-input"
                maxlength="6"
                autocomplete="one-time-code"
                placeholder="000000"
                @input="validateNumericInput"
              />
              <div v-if="form.errors.code" class="error-message">
                {{ form.errors.code }}
              </div>
            </div>

            <!-- Success Message -->
            <div v-if="successMessage" class="status-container success">
              <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
              </svg>
              <span class="status-text">{{ successMessage }}</span>
            </div>

            <!-- Error Message -->
            <div v-if="errorMessage" class="status-container error">
              <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="15" y1="9" x2="9" y2="15"></line>
                <line x1="9" y1="9" x2="15" y2="15"></line>
              </svg>
              <span class="status-text">{{ errorMessage }}</span>
            </div>

            <!-- Buttons -->
            <div class="button-group">
              <button
                type="submit"
                class="verify-button"
                :disabled="form.processing"
              >
                <span v-if="form.processing" class="spinner"></span>
                <span v-else>Verifikasi</span>
              </button>

              <button
                type="button"
                class="resend-button"
                @click="resendCode"
                :disabled="resending"
              >
                <span v-if="resending" class="spinner small"></span>
                <span v-else>Kirim Ulang Kode</span>
              </button>
            </div>

            <!-- Help Text -->
            <p class="help-text">
              Butuh bantuan? 
              <a href="#" class="help-link">Hubungi support</a>
            </p>
          </form>
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
/* Reset and base styles */
* {
  box-sizing: border-box;
}

/* Page wrapper - full height with centered content */
.page-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 1rem;
  background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
}

/* Card container - responsive width */
.card-container {
  width: 100%;
  max-width: 400px;
}

/* Main card - always white background */
.card {
  background: #ffffff !important;
  border-radius: 1rem;
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
  padding: 2rem;
  text-align: center;
  animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Icon */
.icon-container {
  margin-bottom: 1.5rem;
}

.whatsapp-icon {
  width: 3rem;
  height: 3rem;
  color: #25D366;
}

/* Title */
.title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 1.5rem 0;
}

/* Content area */
.content {
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

/* Description text */
.description {
  color: #6b7280;
  font-size: 0.95rem;
  line-height: 1.5;
  margin: 0;
}

/* Input group */
.input-group {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  text-align: left;
}

.input-label {
  color: #374151;
  font-size: 0.875rem;
  font-weight: 600;
}

/* OTP Input */
.otp-input {
  width: 100%;
  padding: 0.875rem;
  border: 2px solid #e5e7eb;
  border-radius: 0.5rem;
  font-size: 1.125rem;
  letter-spacing: 0.125rem;
  text-align: center;
  font-weight: 600;
  color: #111827;
  background-color: #ffffff;
  transition: all 0.2s ease;
}

.otp-input:focus {
  outline: none;
  border-color: #3b82f6;
  box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.otp-input::placeholder {
  letter-spacing: 0.25rem;
  color: #9ca3af;
  font-weight: 400;
}

/* Error message */
.error-message {
  color: #ef4444;
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

/* Status containers */
.status-container {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
}

.status-container.success {
  background-color: #f0fdf4;
  border: 1px solid #bbf7d0;
  color: #16a34a;
}

.status-container.error {
  background-color: #fef2f2;
  border: 1px solid #fecaca;
  color: #dc2626;
}

.status-icon {
  width: 1rem;
  height: 1rem;
  flex-shrink: 0;
}

.status-text {
  font-weight: 500;
}

/* Button group */
.button-group {
  display: flex;
  flex-direction: column;
  gap: 0.75rem;
  margin-top: 0.5rem;
}

/* Verify button */
.verify-button {
  width: 100%;
  padding: 0.75rem 1rem;
  background: #0d6efd;
  color: white;
  border: none;
  border-radius: 0.5rem;
  font-size: 0.95rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 2.75rem;
}

.verify-button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  background-color: #0b5ed7;
    border-color: #0a58ca;
}

.verify-button:disabled {
  opacity: 0.6;
  cursor: not-allowed;
  transform: none;
}

/* Resend button */
.resend-button {
  width: 100%;
  padding: 0.75rem 1rem;
  background: transparent;
  color: #3b82f6;
  border: 1px solid #3b82f6;
  border-radius: 0.5rem;
  font-size: 0.9rem;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s ease;
  display: flex;
  align-items: center;
  justify-content: center;
  min-height: 2.5rem;
}

.resend-button:hover:not(:disabled) {
  background-color: rgba(59, 130, 246, 0.1);
  transform: translateY(-1px);
}

.resend-button:disabled {
  opacity: 0.5;
  cursor: not-allowed;
  transform: none;
}

/* Spinner */
.spinner {
  width: 1rem;
  height: 1rem;
  border: 2px solid rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  border-top-color: white;
  animation: spin 1s linear infinite;
}

.spinner.small {
  border: 2px solid rgba(59, 130, 246, 0.3);
  border-top-color: #3b82f6;
}

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Help text */
.help-text {
  color: #9ca3af;
  font-size: 0.875rem;
  margin: 0.5rem 0 0 0;
  line-height: 1.4;
}

.help-link {
  color: #3b82f6;
  text-decoration: none;
  font-weight: 500;
}

.help-link:hover {
  text-decoration: underline;
}

/* Responsive breakpoints */
@media (max-width: 480px) {
  .page-wrapper {
    padding: 0.75rem;
  }
  
  .card {
    padding: 1.5rem;
  }
  
  .title {
    font-size: 1.375rem;
  }
  
  .whatsapp-icon {
    width: 2.5rem;
    height: 2.5rem;
  }
  
  .description {
    font-size: 0.9rem;
  }
  
  .otp-input {
    font-size: 1rem;
    padding: 0.75rem;
  }
  
  .verify-button,
  .resend-button {
    font-size: 0.9rem;
    min-height: 2.5rem;
  }
}

@media (max-width: 360px) {
  .card {
    padding: 1.25rem;
  }
  
  .title {
    font-size: 1.25rem;
  }
  
  .description,
  .help-text {
    font-size: 0.85rem;
  }
  
  .otp-input {
    font-size: 0.95rem;
  }
}

/* Large screens */
@media (min-width: 768px) {
  .card-container {
    max-width: 420px;
  }
  
  .card {
    padding: 2.5rem;
  }
}
</style>