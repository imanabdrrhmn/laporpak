<template>
  <div v-if="visible">
    <div class="modal-backdrop fade show" @click.self="closeModal"></div>
    <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-login-modal mx-auto">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h4 class="fw-bold modal-title">Reset Kata Sandi</h4>
              <p class="text-muted mb-4">Masukkan email atau nomor HP terdaftar</p>
            </div>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Close"></button>
          </div>

          <!-- Toggle Button for Email / No HP -->
          <div class="mb-4 login-toggle d-flex p-1 bg-light rounded-pill">
            <button
              type="button"
              class="btn btn-toggle flex-grow-1 me-1 py-2 rounded-pill"
              :class="{ active: isEmailMode }"
              @click="setMode('email')"
            >
              <i class="bi bi-envelope me-2"></i> Email
            </button>
            <button
              type="button"
              class="btn btn-toggle flex-grow-1 py-2 rounded-pill"
              :class="{ active: !isEmailMode }"
              @click="setMode('no_hp')"
            >
              <i class="bi bi-phone me-2"></i> No HP
            </button>
          </div>

          <!-- Success Message -->
          <div v-if="successMessage" class="alert alert-success">
            {{ successMessage }}
          </div>

          <!-- Error Message -->
          <div v-if="form.errors.phoneEmail" class="alert alert-danger">
            {{ form.errors.phoneEmail }}
          </div>

          <!-- Form -->
          <form @submit.prevent="handleReset">
            <div class="mb-3">
              <label class="form-label fw-bold" v-if="isEmailMode">Email</label>
              <label class="form-label fw-bold" v-else>Nomor HP</label>
              <input
                type="text"
                class="form-control border-primary"
                v-model="form.phoneEmail"
                :placeholder="isEmailMode ? 'Masukkan email terdaftar' : 'Masukkan nomor HP terdaftar'"
                :disabled="isLoading"
              />
            </div>

            <button
            type="submit"
            class="btn btn-primary w-100 py-2 fw-bold"
            :disabled="isLoading || cooldown">
            <span v-if="isLoading">Mengirim...</span>
            <span v-else-if="cooldown">Tunggu {{ cooldownTimer }} detik...</span>
            <span v-else>Kirim Link Reset Password</span>
          </button>
          </form>

          <!-- Login Prompt -->
          <p class="text-center text-muted mt-4">
            Ingat kata sandi?
            <span class="text-primary fw-bold hover-pointer" @click.prevent="triggerLogin">
              Masuk
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

// Props & Emits
const props = defineProps({
  visible: Boolean,
})
const emit = defineEmits(['update:visible', 'open-login'])

// Reactive state
const isEmailMode = ref(true)
const successMessage = ref('')
const isLoading = ref(false)

const form = useForm({
  phoneEmail: '',
})

function setMode(mode) {
  isEmailMode.value = mode === 'email'
  form.phoneEmail = ''
  form.clearErrors()
  successMessage.value = ''
}

function closeModal() {
  form.reset()
  form.clearErrors()
  successMessage.value = ''
  cooldown.value = false
  clearInterval(cooldownInterval)
  emit('update:visible', false)
}

function triggerLogin() {
  emit('update:visible', false)
  emit('open-login')
}

function handleReset() {
  form.clearErrors()
  successMessage.value = ''

  if (!form.phoneEmail) {
    form.setError('phoneEmail', isEmailMode.value ? 'Email wajib diisi' : 'Nomor HP wajib diisi')
    return
  }

  const payload = isEmailMode.value
    ? { email: form.phoneEmail }
    : { no_hp: form.phoneEmail }

  isLoading.value = true

  Inertia.post('/forgot-password', payload, {
    preserveScroll: true,
    onSuccess: (response) => {
      successMessage.value = response.props?.message || 'Link reset password berhasil dikirim.'
      form.reset()
      startCooldown()
    },
    onError: (errors) => {
      // Tangani error dari backend
      if (errors.email || errors.no_hp) {
        form.setError('phoneEmail', errors.email || errors.no_hp)
      } else {
        form.setError('phoneEmail', 'Terjadi kesalahan. Pastikan input sudah benar.')
      }
    },
    onFinish: () => {
      isLoading.value = false
    },
  })
}

const cooldown = ref(false)
const cooldownTimer = ref(60)
let cooldownInterval = null

function startCooldown() {
  cooldown.value = true
  cooldownTimer.value = 60

  cooldownInterval = setInterval(() => {
    cooldownTimer.value--
    if (cooldownTimer.value <= 0) {
      clearInterval(cooldownInterval)
      cooldown.value = false
    }
  }, 1000)
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

.btn-toggle {
  cursor: pointer;
  border: none;
  background-color: transparent;
  transition: background-color 0.3s ease;
  font-weight: 600;
  color: #212529;
}

.btn-toggle.active {
  background-color: #0d6efd; /* Bootstrap primary */
  color: white;
}

.login-toggle {
  user-select: none;
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

.hover-pointer:hover {
  cursor: pointer;
}

.btn-close {
  transition: transform 0.1s ease;
}

.btn-close:hover {
  transform: rotate(90deg);
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
