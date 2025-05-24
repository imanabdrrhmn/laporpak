<template>
  <AppLayout>
    <Head title="Verifikasi Email" />
    <div class="page-wrapper p-0">
      <div class="card-container">
        <div class="card">
          <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="email-icon">
              <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path>
              <polyline points="22,6 12,13 2,6"></polyline>
            </svg>
          </div>

          <h1 class="title">Verifikasi Email</h1>
          <div class="divider"></div>

          <p class="subtitle" v-if="status !== 'verification-link-sent'">
            Silakan klik tombol kirim di bawah untuk menerima email.
          </p>
          <p class="subtitle" v-else>
            Kami telah mengirimkan link verifikasi ke email Anda. Silakan cek inbox Anda dan klik tautan untuk memverifikasi akun.
          </p>

          <div v-if="isVerified" class="status-box success">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="status-icon">
              <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
              <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
            <p class="verified-text">Email Anda telah terverifikasi!</p>
          </div>

          <div v-if="!isVerified" class="resend-wrapper">
            <p class="muted-text">Tidak menerima email? Anda dapat mengirim ulang link verifikasi.</p>

            <button
              @click="resendVerification"
              :disabled="processing || countdown > 0"
              class="btn"
              :class="{ 'btn-disabled': countdown > 0 }"
            >
              <span v-if="processing" class="loading-spinner"></span>
              <span v-else-if="countdown > 0">Tunggu {{ countdown }} detik</span>
              <span v-else>{{ hasSent ? 'Kirim Ulang' : 'Kirim' }}</span>
            </button>

            <div v-if="status === 'verification-link-sent'" class="status-box info">
              <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="status-icon">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
              </svg>
              <p class="info-text">Link verifikasi telah dikirim! Periksa email Anda.</p>
            </div>
          </div>

          <div class="help-section">
            <p class="help-text">Butuh bantuan? <a href="#" class="help-link">Hubungi support</a></p>
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

const page = usePage()

function startCountdown() {
  countdown.value = 60
  if (timer) clearInterval(timer)
  timer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) {
      clearInterval(timer)
      timer = null
    }
  }, 1000)
}

const resendVerification = () => {
  if (processing.value || countdown.value > 0) return

  processing.value = true
  router.post(route('email.verify.send'), {}, {
    onFinish: () => {
      processing.value = false
      status.value = 'verification-link-sent'
      hasSent.value = true
      startCountdown()
    },
    onError: () => {
      processing.value = false
      alert('Terjadi kesalahan saat mengirim ulang link. Silakan coba lagi.')
    }
  })
}

onMounted(() => {
  if (page.props.value?.auth?.user && page.props.value.auth.user.email_verified_at) {
    isVerified.value = true
  } else {
    isVerified.value = false
  }
})
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
  margin: 1rem;
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

.email-icon {
  width: 68px;
  height: 68px;
  color: #2563EB;
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

.status-box.info {
  background-color: rgba(59, 130, 246, 0.1);
  border: 1px solid rgba(59, 130, 246, 0.3);
}

.status-icon {
  width: 24px;
  height: 24px;
  margin-right: 10px;
}

.status-box.success .status-icon {
  color: #10B981;
}

.status-box.info .status-icon {
  color: #3B82F6;
}

.verified-text {
  color: #10B981;
  font-weight: bold;
  margin: 0;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.resend-wrapper {
  margin-top: 1.5rem;
}

.muted-text {
  color: #6B7280;
  font-size: 1rem;
  margin-bottom: 1rem;
}

.info-text {
  color: #3B82F6;
  font-size: 0.95rem;
  margin: 0;
}

/* Button style */
.btn {
  background: linear-gradient(135deg, #2563EB, #3B82F6);
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

.btn:before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
  transform: translateX(-100%);
}

.btn:hover:not(:disabled):before {
  animation: shimmer 1.5s infinite;
}

@keyframes shimmer {
  100% {
    transform: translateX(100%);
  }
}

.btn-disabled {
  background: linear-gradient(90deg, #2563EB, #3B82F6);
}

.btn:disabled {
  cursor: not-allowed;
}

.btn:hover:not(:disabled) {
  transform: translateY(-3px);
  box-shadow: 0 7px 14px rgba(37, 99, 235, 0.3);
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

/* Modal styling */

@keyframes scaleIn {
  to { transform: scale(1); }
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