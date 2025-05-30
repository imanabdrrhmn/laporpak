<template>
  <AppLayout>
    <Head title="Verifikasi Email" />
    <div class="page-wrapper">
      <div class="card-container">
        <div class="card">
          <!-- Email Icon -->
          <div class="icon-container">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 24 24"
              fill="none"
              stroke="currentColor"
              stroke-width="2"
              stroke-linecap="round"
              stroke-linejoin="round"
              class="email-icon"
            >
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
              <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
          </div>

          <!-- Title -->
          <h1 class="title">Verifikasi Email</h1>

          <!-- Success State -->
          <div v-if="isVerified" class="status-container success">
            <svg class="status-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <p class="status-text">Email Anda telah terverifikasi!</p>
          </div>

          <!-- Unverified State -->
          <div v-if="!isVerified" class="content">
            <!-- Description -->
            <p class="description">
              {{ status !== 'verification-link-sent' 
                ? 'Silakan klik tombol di bawah untuk menerima email verifikasi.' 
                : 'Link verifikasi telah dikirim ke email Anda. Periksa inbox dan klik tautan untuk memverifikasi akun.' }}
            </p>

            <!-- Send Button -->
            <button
              @click="resendVerification"
              :disabled="processing || countdown > 0"
              class="send-button"
            >
              <span v-if="processing" class="spinner"></span>
              <span v-else-if="countdown > 0">Tunggu {{ countdown }}s</span>
              <span v-else>{{ hasSent ? 'Kirim Ulang' : 'Kirim Email' }}</span>
            </button>

            <!-- Info Message -->
            <div v-if="status === 'verification-link-sent'" class="info-box">
              <svg class="info-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <span>Email verifikasi berhasil dikirim</span>
            </div>

            <!-- Help Text -->
            <p class="help-text">
              Tidak menerima email? Periksa folder spam atau 
              <a href="#" class="help-link">hubungi support</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage, router, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  needsEmailVerification: Boolean,
  status: String,
})

const processing = ref(false)
const countdown = ref(0)
const status = ref(props.status || '')
const isVerified = ref(false)
const hasSent = ref(false)
let timer = null

const COUNTDOWN_DURATION = 60
const STORAGE_KEY = 'emailVerificationCountdownEnd'

function startCountdown(duration = COUNTDOWN_DURATION) {
  countdown.value = duration
  const countdownEndTimestamp = Date.now() + duration * 1000
  localStorage.setItem(STORAGE_KEY, countdownEndTimestamp)

  if (timer) clearInterval(timer)
  timer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(timer)
      timer = null
      localStorage.removeItem(STORAGE_KEY)
    }
  }, 1000)
}

function restoreCountdown() {
  const storedEnd = localStorage.getItem(STORAGE_KEY)
  if (storedEnd) {
    const remaining = Math.floor((storedEnd - Date.now()) / 1000)
    if (remaining > 0) {
      hasSent.value = true
      startCountdown(remaining)
    } else {
      localStorage.removeItem(STORAGE_KEY)
    }
  }
}

const resendVerification = () => {
  if (processing.value || countdown.value > 0) return

  processing.value = true
  router.post(route('email.verify.send'), {}, {
    onFinish: () => {
      processing.value = false
      hasSent.value = true
      startCountdown()
      status.value = 'verification-link-sent'
    },
    onError: () => {
      processing.value = false
      alert('Terjadi kesalahan saat mengirim ulang link. Silakan coba lagi.')
    }
  })
}

const page = usePage()

onMounted(() => {
  restoreCountdown()

  if (page.props.value?.auth?.user && page.props.value.auth.user.email_verified_at) {
    isVerified.value = true
  } else {
    isVerified.value = false
  }
})
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

/* Main card */
.card {
  background: #ffffff;
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

.email-icon {
  width: 3rem;
  height: 3rem;
  color: #3b82f6;
}

/* Title */
.title {
  font-size: 1.5rem;
  font-weight: 700;
  color: #111827;
  margin: 0 0 1.5rem 0;
}

/* Success state */
.status-container {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 1rem;
  border-radius: 0.75rem;
  margin-bottom: 1rem;
}

.status-container.success {
  background-color: #f0fdf4;
  border: 1px solid #bbf7d0;
}

.status-icon {
  width: 2rem;
  height: 2rem;
  color: #16a34a;
  margin-bottom: 0.5rem;
}

.status-text {
  color: #16a34a;
  font-weight: 600;
  margin: 0;
  font-size: 0.95rem;
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

/* Send button */
.send-button {
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

.send-button:hover:not(:disabled) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
  background-color: #0b5ed7;
    border-color: #0a58ca;
}

.send-button:disabled {
  opacity: 0.6;
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

@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

/* Info box */
.info-box {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 0.5rem;
  padding: 0.75rem;
  background-color: #eff6ff;
  border: 1px solid #bfdbfe;
  border-radius: 0.5rem;
  color: #1d4ed8;
  font-size: 0.875rem;
}

.info-icon {
  width: 1rem;
  height: 1rem;
  flex-shrink: 0;
}

/* Help text */
.help-text {
  color: #9ca3af;
  font-size: 0.875rem;
  margin: 0;
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
  
  .email-icon {
    width: 2.5rem;
    height: 2.5rem;
  }
  
  .description {
    font-size: 0.9rem;
  }
  
  .send-button {
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

/* Force white card background always */
.card {
  background: #ffffff !important;
}
</style>