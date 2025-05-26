<template>
  <div v-if="visible">
    <div class="modal-backdrop fade show" @click.self="closeModal"></div>
    <div class="modal fade show d-block" tabindex="-1" role="dialog" aria-modal="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-login-modal mx-auto">
          <div class="modal-header-decoration"></div>
          <div class="modal-body px-4 py-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h4 class="fw-bold modal-title">Reset Kata Sandi</h4>
                <p class="text-muted mb-4">Masukkan email atau nomor HP terdaftar</p>
              </div>
              <button type="button" class="btn-close custom-close" @click="closeModal" aria-label="Tutup"></button>
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
            <form @submit.prevent="handleReset" novalidate>
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
                class="btn btn-primary w-100 py-3 fw-bold rounded-pill"
                :disabled="isLoading || cooldown"
              >
                <span v-if="isLoading">Mengirim...</span>
                <span v-else-if="cooldown">Tunggu {{ cooldownTimer }} detik...</span>
                <span v-else>Kirim Link Reset Password</span>
              </button>
            </form>

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
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm, router as Inertia } from '@inertiajs/vue3'

const props = defineProps({ visible: Boolean })
const emit = defineEmits(['update:visible', 'open-login'])

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
  max-width: 480px;
  min-height: auto;
  border-radius: 24px;
  padding: 0;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
  background-color: #fff;
  overflow: hidden;
  position: relative;
}

/* Decorative header */
.modal-header-decoration {
  height: 8px;
  width: 100%;
  background: linear-gradient(90deg, #2563EB, #4F46E5);
}

.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1040;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(2px);
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
  border: none;
  background: transparent;
  color: #6c757d;
  font-weight: 500;
  transition: all 0.3s ease;
}

.btn-toggle.active {
  background-color: #2563EB !important;
  color: white !important;
  box-shadow: 0 2px 8px rgba(37, 99, 235, 0.3);
}

.login-toggle {
  user-select: none;
}

.form-control {
  padding: 0.75rem 1rem;
  border-radius: 10px;
  border: 1.5px solid #e2e8f0;
  transition: all 0.2s ease;
}

.form-control.border-primary {
  border-color: #2563EB;
}

.form-control:focus {
  border-color: #2563EB;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.alert {
  font-size: 0.875rem;
  margin-bottom: 1rem;
}

.btn-primary {
  background-color: #2563EB;
  border: none;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
  transition: all 0.3s ease;
  padding: 0.75rem 1rem;
  font-weight: 700;
  border-radius: 24px;
}

.btn-primary:hover {
  background-color: #1e40af;
  transform: translateY(-2px);
  box-shadow: 0 6px 16px rgba(37, 99, 235, 0.3);
}

.btn-primary:active {
  transform: translateY(0);
}

.btn-primary:disabled {
  background-color: #93c5fd;
  box-shadow: none;
  cursor: not-allowed;
}

.hover-pointer:hover {
  cursor: pointer;
  text-decoration: underline;
}

/* Custom close button */
.custom-close {
  border-radius: 50%;
  padding: 6px;
  background-color: #f3f4f6;
  opacity: 1;
  transition: all 0.2s ease;
}

.custom-close:hover {
  background-color: #e5e7eb;
  transform: rotate(90deg);
}

@media (max-width: 576px) {
  .custom-login-modal {
    max-width: 100%;
    border-radius: 16px;
  }

  .modal-dialog-centered {
    padding: 0 10px;
  }
}
</style>
