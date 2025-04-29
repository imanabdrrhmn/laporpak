<template>
  <div v-if="visible">
    <div class="modal-backdrop fade show" @click.self="closeModal"></div>
    <div class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-login-modal mx-auto">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h4 class="fw-bold">Selamat Datang</h4>
              <p class="text-muted mb-4">Silahkan masukkan akun anda</p>
            </div>
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <form @submit.prevent="handleLogin">
            <div class="mb-3">
              <label class="form-label fw-bold">Nomor HP / Email</label>
              <input
                type="text"
                class="form-control border-primary"
                v-model="form.phoneEmail"
                placeholder="masukkan nomor HP atau email anda"
              />
              <small class="error-message d-block">{{ errors.phoneEmail }}</small>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Kata Sandi</label>
              <div class="position-relative">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  class="form-control border-primary pe-5"
                  v-model="form.password"
                  placeholder="masukkan kata sandi anda"
                />
                <i
                  :class="passwordVisible ? 'bi bi-eye' : 'bi bi-eye-slash'"
                  class="toggle-password-icon"
                  @click="togglePasswordVisibility"
                ></i>
              </div>

              <div class="d-flex justify-content-between align-items-center mt-1">
                <small class="error-message">{{ errors.password }}</small>
                <a href="#" class="text-danger small text-decoration-none link-lupa-password">
                  Lupa kata sandi?
                </a>
              </div>
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Login</button>
          </form>

          <div class="text-center text-muted my-3">ATAU</div>

          <button class="btn btn-light border w-100 py-2 d-flex align-items-center justify-content-center shadow-sm">
            <img
              src="https://www.svgrepo.com/show/475656/google-color.svg"
              alt="Google"
              width="20"
              class="me-2"
            />
            Login dengan Google
          </button>
          <p class="text-center text-muted mt-4">
            Belum punya akun?
            <span class="text-primary fw-bold cursor-pointer" @click="triggerRegister">
              Daftar
            </span>
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
  visible: Boolean,
})

const emit = defineEmits(['update:visible', 'open-register'])

// State untuk input form dari user
const form = ref({
  phoneEmail: '',
  password: ''
})

// State untuk kontrol visibilitas password
const passwordVisible = ref(false)

// Error message untuk validasi lokal dan dari server
const errors = ref({
  phoneEmail: '',
  password: ''
})

// Tutup modal & reset form
function closeModal() {
  form.value.phoneEmail = ''
  form.value.password = ''
  errors.value.phoneEmail = ''
  errors.value.password = ''
  emit('update:visible', false)
}

// Buka modal register
function triggerRegister() {
  emit('update:visible', false)
  emit('open-register')
}

// Toggle show/hide password
function togglePasswordVisibility() {
  passwordVisible.value = !passwordVisible.value
}

// Handle submit login
function handleLogin() {
  errors.value.phoneEmail = ''
  errors.value.password = ''

  if (!form.value.phoneEmail) {
    errors.value.phoneEmail = 'Email atau nomor HP harus diisi'
  }

  if (!form.value.password) {
    errors.value.password = 'Kata sandi harus diisi'
  }

  if (errors.value.phoneEmail || errors.value.password) return

  // Cek apakah input adalah email atau no HP
  const isEmail = form.value.phoneEmail.includes('@')
  const loginData = {
    email: isEmail ? form.value.phoneEmail : null,
    no_hp: !isEmail ? form.value.phoneEmail : null,
    password: form.value.password
  }

  // Kirim ke server pakai Inertia
  useForm(loginData).post(route('login'), {
    onSuccess: () => {
      closeModal()
    },
    onError: (serverErrors) => {
      if (serverErrors.email || serverErrors.no_hp) {
        errors.value.phoneEmail = serverErrors.email || serverErrors.no_hp
      }
      if (serverErrors.password) {
        errors.value.password = serverErrors.password
      }
    }
  })
}
</script>


<style scoped>
.custom-login-modal {
  width: 650px;
  min-height: 580px;
  border-radius: 24px;
  padding: 48px;
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.05);
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
  overflow-y: hidden;
  display: flex;
  align-items: center;
  justify-content: center;
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
  margin: auto;
}

.error-message {
  font-size: 0.875rem;
  color: red;
  margin: 0;
  padding: 0;
  min-height: 18px;
}

/* Hilangkan ikon validasi Bootstrap */
.form-control.is-invalid {
  background-image: none !important;
  border-color: #dc3545;
  padding-right: 0.75rem !important;
}

.form-control:focus {
  box-shadow: none !important;
}

/* Toggle icon mata */
.toggle-password-icon {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  cursor: pointer;
  color: #888;
  font-size: 18px;
}

/* Hover efek link lupa password */
.link-lupa-password {
  transition: color 0.2s ease;
  color: #dc3545;
}
.link-lupa-password:hover {
  color: #a71d2a;
}

@media (max-width: 576px) {
  .custom-login-modal {
    width: 100% !important;
    height: auto;
    margin: 1rem;
    border-radius: 16px;
    padding: 1.5rem;
  }
}
</style>