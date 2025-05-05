<template>
  <div v-if="visible">
    <div class="modal-backdrop fade show" @click.self="closeModal"></div>
    <div class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-login-modal mx-auto">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h4 class="fw-bold modal-title">Reset Kata Sandi</h4>
              <p class="text-muted mb-4">Masukkan email atau nomor HP terdaftar</p>
            </div>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="alert alert-success">
            {{ successMessage }}
          </div>

          <!-- Error Message -->
          <div v-if="form.errors.phoneEmail" class="alert alert-danger">
            {{ form.errors.phoneEmail }}
          </div>

          <!-- Form to Input Email or Phone Number -->
          <form v-else @submit.prevent="handleReset">
            <div class="mb-3">
              <label class="form-label fw-bold">Nomor HP / Email</label>
              <input
                type="text"
                class="form-control border-primary"
                v-model="form.phoneEmail"
                placeholder="Masukkan email atau nomor HP"
                :disabled="isLoading"
              />
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold" :disabled="isLoading">
              <span v-if="isLoading">Mengirim...</span>
              <span v-else>Kirim Link Reset Password</span>
            </button>
          </form>

          <!-- Login Prompt -->
          <p class="text-center text-muted mt-4">
            Ingat kata sandi?
            <span class="text-primary fw-bold cursor-pointer" @click.prevent="triggerLogin">
              Login
            </span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router as Inertia } from '@inertiajs/vue3'

// Props and Emitters
const props = defineProps({
  visible: Boolean
})
const emit = defineEmits(['update:visible', 'open-login'])

// State management
const successMessage = ref('')
const isLoading = ref(false)
const form = useForm({
  phoneEmail: ''
})

// Functions
function closeModal() {
  form.reset()
  form.clearErrors()
  successMessage.value = ''
  emit('update:visible', false)
}

function triggerLogin() {
  emit('update:visible', false)
  emit('open-login')
}

// Handle the Reset Password
function handleReset() {
  form.clearErrors()
  successMessage.value = ''  // Clear the success message

  // Validate if phone/email is filled
  if (!form.phoneEmail) {
    form.setError('phoneEmail', 'Email atau nomor HP wajib diisi')
    return
  }

  // Check if it's an email or phone number
  const isEmail = form.phoneEmail.includes('@')
  const payload = isEmail
    ? { email: form.phoneEmail }
    : { no_hp: form.phoneEmail }

  // Start loading state
  isLoading.value = true

  // Send the request
  Inertia.post('/forgot-password', payload, {
    preserveScroll: true,
    onSuccess: (response) => {
      // Update success message after response
      successMessage.value = response.props?.successMessage || 'Link reset password berhasil dikirim ke email Anda.'
      form.reset()  // Reset the form after success
    },
    onError: (errors) => {
      // Optionally handle errors and display them
      form.setError('phoneEmail', 'Terjadi kesalahan. Pastikan email atau nomor HP terdaftar.')
    },
    finally: () => {
      // Stop loading state
      isLoading.value = false
    }
  })
}
</script>

<style scoped>
.custom-login-modal {
  width: 100%;
  max-width: 450px;
  min-height: auto;
  border-radius: 16px;
  padding: 24px;
  box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
  background-color: #fff;
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1040;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
}

.modal {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1050;
  width: 100%;
  height: 100%;
  overflow-y: auto;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
  margin: auto;
  width: 100%;
  padding: 0 15px;
}

.error-message {
  font-size: 0.75rem;
  color: #dc3545;
  display: block;
  margin-top: 0.25rem;
}

.alert {
  font-size: 0.875rem;
}

@media (max-width: 576px) {
  .custom-login-modal {
    max-width: 100%;
    border-radius: 12px;
    padding: 20px;
  }

  .modal-dialog-centered {
    padding: 0 10px;
  }
}
</style>
