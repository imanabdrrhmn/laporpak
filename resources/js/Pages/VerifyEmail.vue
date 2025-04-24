<template>
  <div>
    <h1>Verifikasi Email</h1>
    <p>Link verifikasi telah dikirim ke email Anda.</p>
    <p v-if="status === 'verification-link-sent'" class="text-green">Email berhasil dikirim ulang!</p>

    <form @submit.prevent="resend">
      <button :disabled="processing">Kirim Ulang</button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router, usePage } from '@inertiajs/vue3'

const page = usePage()
const status = ref(page.props.value?.status)
const processing = ref(false)

const resend = () => {
  processing.value = true
  router.post(route('verification.send'), {}, {
    onFinish: () => {
      processing.value = false
      status.value = 'verification-link-sent'
    }
  })
}
</script>

