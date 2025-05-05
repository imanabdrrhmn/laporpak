<template>
  <AppLayout>
  <div class="container m-5">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <div class="card shadow">
          <div class="card-body">
            <h3 class="card-title text-center mb-4">Verifikasi Nomor WhatsApp</h3>
            <form @submit.prevent="submit">
              <div class="mb-3">
                <label for="code" class="form-label">Kode Verifikasi</label>
                <input
                  id="code"
                  type="text"
                  v-model="form.code"
                  class="form-control"
                  maxlength="6"
                  autocomplete="one-time-code"
                  placeholder="Masukkan kode OTP"
                />
                <div v-if="form.errors.code" class="form-text text-danger">
                  {{ form.errors.code }}
                </div>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-4">
                <button
                  type="submit"
                  class="btn btn-primary"
                  :disabled="form.processing"
                >
                  Verifikasi
                </button>

                <button
                  type="button"
                  class="btn btn-link text-decoration-none"
                  @click="resendCode"
                  :disabled="resending"
                >
                  Kirim Ulang Kode
                </button>
              </div>

              <div v-if="successMessage" class="alert alert-success mt-3 mb-0">
                {{ successMessage }}
              </div>

              <div v-if="errorMessage" class="alert alert-danger mt-3 mb-0">
                {{ errorMessage }}
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router, usePage } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

const form = useForm({ code: '' })

const page = usePage();
page.layout = AppLayout;

const successMessage = ref('')
const errorMessage = ref('')
const resending = ref(false)

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
