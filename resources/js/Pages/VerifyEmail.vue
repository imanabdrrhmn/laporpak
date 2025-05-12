<template>
  <AppLayout>
  <Head title="Verifikasi Email" />
  <div class="page-wrapper p-0">
    <!-- Card content -->
    <div class="card">
      <h1 class="title">Verifikasi Email</h1>
      <p class="subtitle">Kami telah mengirimkan link verifikasi ke email Anda. Silakan cek inbox Anda dan klik tautan untuk memverifikasi email Anda.</p>

      <!-- If email is already verified -->
      <p v-if="isVerified" class="verified-text">Email Anda telah terverifikasi! 🎉</p>

      <!-- Retry sending verification -->
      <div v-if="!isVerified" class="resend-wrapper">
        <p class="muted-text">Tidak menerima email? Anda dapat mengirim ulang link verifikasi.</p>
        
        <button 
          @click="resendVerification"
          :disabled="processing || countdown > 0"
          class="btn"
        >
          <span v-if="countdown === 0">Kirim Ulang</span>
          <span v-else>Tunggu {{ countdown }} detik</span>
        </button>

        <p v-if="status === 'verification-link-sent'" class="info-text">Link verifikasi telah dikirim! Periksa email Anda.</p>
      </div>
    </div>
  </div>
</AppLayout>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage, router, Head } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const processing = ref(false)
const countdown = ref(60)
const status = ref('')
const isVerified = ref(false)
const isModalVisible = ref(false)
const modalMessage = ref('')

const page = usePage()

onMounted(() => {
  if (page.props.value?.auth?.user && page.props.value.auth.user.email_verified_at) {
    isVerified.value = true
  } else {
    isVerified.value = false
    startCountdown()
  }
})

const closeModal = () => {
  isModalVisible.value = false
}

const resendVerification = () => {
  processing.value = true
  router.post(route('verification.send'), {}, {
    onFinish: () => {
      processing.value = false
      status.value = 'verification-link-sent'
      startCountdown()
      showModal('Link verifikasi telah dikirim! Silakan cek inbox Anda.')
    }
  })
}

const startCountdown = () => {
  countdown.value = 60
  const timer = setInterval(() => {
    countdown.value--
    if (countdown.value <= 0) clearInterval(timer)
  }, 1000)
}

const showModal = (message) => {
  modalMessage.value = message
  isModalVisible.value = true
}
</script>

<style scoped>
/* Full page wrapper */
.page-wrapper {
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  background: linear-gradient(135deg, #f8f9fa, #e9ecef);
  padding: 2rem;
}

/* Card style */
.card {
  background: #ffffff;
  border-radius: 16px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.1);
  padding: 2rem;
  width: 100%;
  max-width: 450px;
  text-align: center;
}

/* Typography */
.title {
  font-size: 1.75rem;
  font-weight: bold;
  color: #343a40;
}

.subtitle {
  margin-top: 1rem;
  font-size: 1rem;
  color: #6c757d;
}

.verified-text {
  margin-top: 2rem;
  color: #28a745;
  font-weight: bold;
}

.resend-wrapper {
  margin-top: 2rem;
}

.muted-text {
  color: #6c757d;
  font-size: 0.95rem;
}

.info-text {
  margin-top: 1rem;
  color: #17a2b8;
  font-size: 0.9rem;
}

/* Button style */
.btn {
  background-color: #007bff;
  color: white;
  padding: 0.75rem 2rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  margin-top: 1rem;
  cursor: pointer;
  transition: background-color 0.3s ease, transform 0.2s ease;
  width: 100%;
}

.btn:disabled {
  background-color: #6c757d;
  cursor: not-allowed;
}

.btn:hover:not(:disabled) {
  background-color: #0056b3;
  transform: translateY(-2px);
}

/* Modal content */
.modal {
  background: #ffffff;
  border-radius: 12px;
  padding: 2rem;
  width: 90%;
  max-width: 400px;
  text-align: center;
}

.modal-title {
  font-size: 1.5rem;
  color: #343a40;
}

.modal-body {
  margin: 1rem 0;
  font-size: 1rem;
  color: #6c757d;
}

.modal-btn {
  background-color: #6c757d;
  color: #ffffff;
  padding: 0.5rem 1.5rem;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.modal-btn:hover {
  background-color: #5a6268;
}
</style>
