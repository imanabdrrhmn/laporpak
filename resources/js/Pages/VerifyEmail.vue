<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-100">
    <div class="bg-white rounded-lg shadow-lg p-8 max-w-sm w-full">
      <h1 class="text-2xl font-semibold text-center text-gray-800">Verifikasi Email</h1>
      <p class="text-center text-gray-600 mt-4">Kami telah mengirimkan link verifikasi ke email Anda. Silakan cek inbox Anda dan klik tautan untuk memverifikasi email Anda.</p>
      
      <!-- If email is already verified -->
      <p v-if="isVerified" class="text-green-600 mt-4 text-center">Email Anda telah terverifikasi! 🎉</p>
      
      <!-- Retry sending verification -->
      <div v-if="!isVerified" class="mt-6 text-center">
        <p class="text-gray-600">Jika Anda tidak menerima email, Anda dapat mengirim ulang link verifikasi.</p>
        <button 
          @click="resendVerification"
          :disabled="processing || countdown > 0"
          class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-md disabled:bg-gray-400 disabled:cursor-not-allowed"
        >
          <span v-if="countdown === 0">Kirim Ulang</span>
          <span v-else>Tunggu {{ countdown }} detik</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'

const visible = ref(true)
const processing = ref(false)
const countdown = ref(60)
const status = ref('')
const isVerified = ref(false)

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
  visible.value = false
}

// Resend the verification link
const resendVerification = () => {
  processing.value = true
  router.post(route('verification.send'), {}, {
    onFinish: () => {
      processing.value = false
      status.value = 'verification-link-sent'
      startCountdown()
    }
  })
}

const startCountdown = () => {
  countdown.value = 60
  const timer = setInterval(() => {
    countdown.value--
    if (countdown.value === 0) clearInterval(timer)
  }, 1000)
}
</script>
