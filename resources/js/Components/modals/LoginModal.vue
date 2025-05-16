<template>
  <div v-if="visible">
    <div class="modal-backdrop fade show" @click.self="closeModal"></div>
    <div class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-login-modal mx-auto">
          <!-- Header with Decorative Element -->
          <div class="modal-header-decoration"></div>
          
          <div class="modal-body px-4 py-4">
            <div class="d-flex justify-content-between align-items-start mb-3">
              <div>
                <h4 class="fw-bold mb-1">{{ isFromReport ? 'Silahkan Login' : 'Selamat Datang' }}</h4>
                <p class="text-muted small">{{ isFromReport ? 'Login terlebih dahulu untuk melanjutkan aktivitas pelaporan Anda' : 'Silakan masukkan akun anda' }}</p>
              </div>
              <button type="button" class="btn-close custom-close" @click="closeModal" aria-label="Tutup"></button>
            </div>

            <form @submit.prevent="handleLogin" novalidate>
              <!-- Toggle Button -->
              <div class="mb-4 login-toggle d-flex p-1 bg-light rounded-pill">
                <button
                  class="btn btn-toggle flex-grow-1 me-1 py-2 rounded-pill active"
                  type="button"
                >
                  <i class="bi bi-person me-2"></i> Login
                </button>
                <button
                  class="btn btn-toggle flex-grow-1 py-2 rounded-pill"
                  @click="triggerRegister"
                  type="button"
                >
                  <i class="bi bi-person-plus me-2"></i> Daftar
                </button>
              </div>

              <!-- Email/Phone Input -->
              <div class="form-floating mb-3">
                <input
                  type="text"
                  id="phoneEmail"
                  class="form-control"
                  :class="errors.phoneEmail ? 'is-invalid' : ''"
                  v-model="form.phoneEmail"
                  placeholder="Email atau Nomor HP"
                  @focus="clearError('phoneEmail')"
                />
                <label for="phoneEmail">Nomor HP / Email <span class="text-danger">*</span></label>
                <div v-if="errors.phoneEmail" class="error-message text-danger">{{ errors.phoneEmail }}</div>
              </div>

              <!-- Password -->
              <div class="form-floating mb-4">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  id="password"
                  class="form-control"
                  :class="errors.password ? 'is-invalid' : ''"
                  v-model="form.password"
                  placeholder="Kata Sandi"
                  @focus="clearError('password')"
                />
                <label for="password">Kata Sandi <span class="text-danger">*</span></label>
                <i
                  :class="passwordVisible ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill'"
                  class="toggle-password-icon"
                  @click="togglePasswordVisibility"
                ></i>
                <div v-if="errors.password" class="error-message text-danger">{{ errors.password }}</div>
              </div>

              <!-- Forgot Password (Only keeping this part) -->
              <div class="d-flex justify-content-end align-items-center mb-4">
                <a
                  class="text-danger small text-decoration-none"
                  @click.prevent="triggerResetPassword">
                  Lupa kata sandi?
                </a>
              </div>

              <!-- Login Button -->
              <button 
                type="submit" 
                class="btn btn-primary w-100 py-3 rounded-pill fw-bold mb-3"
                :disabled="isSubmitting"
              >
                <span v-if="isSubmitting">
                  <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Masuk...
                </span>
                <span v-else>Login</span>
              </button>

              <div class="text-center text-muted my-3">ATAU</div>

              <!-- Google Login Button -->
              <a href="/login/google" class="btn btn-light border w-100 py-2 d-flex align-items-center justify-content-center shadow-sm mb-4">
                <img
                  src="https://www.svgrepo.com/show/475656/google-color.svg"
                  alt="Google"
                  width="20"
                  class="me-2"
                />
                <span>Login dengan Google</span>
              </a>
            </form>
          </div>
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
  isFromReport: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['update:visible', 'open-register', 'open-reset-password'])

// State untuk input form dari user
const form = ref({
  phoneEmail: '',
  password: '',
  remember: false // Keeping this for backend compatibility but not showing in UI
})

// State untuk kontrol visibilitas password
const passwordVisible = ref(false)
const isSubmitting = ref(false)

// Error message untuk validasi lokal dan dari server
const errors = ref({
  phoneEmail: '',
  password: ''
})

// Tutup modal & reset form
function closeModal() {
  form.value.phoneEmail = ''
  form.value.password = ''
  form.value.remember = false
  errors.value.phoneEmail = ''
  errors.value.password = ''
  emit('update:visible', false)
}

// Buka modal register
function triggerRegister() {
  emit('update:visible', false)
  emit('open-register')
}

function triggerResetPassword() {
  emit('update:visible', false)
  emit('open-reset-password')
}

// Clear error when focusing on input
function clearError(field) {
  errors.value[field] = ''
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

  isSubmitting.value = true

  // Cek apakah input adalah email atau no HP
  const isEmail = form.value.phoneEmail.includes('@')
  const loginData = {
    email: isEmail ? form.value.phoneEmail : null,
    no_hp: !isEmail ? form.value.phoneEmail : null,
    password: form.value.password,
    remember: form.value.remember
  }

  // Kirim ke server pakai Inertia
  useForm(loginData).post(route('login.submit'), {
    onSuccess: () => {
      isSubmitting.value = false
      closeModal()
    },
    onError: (serverErrors) => {
      isSubmitting.value = false
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

/* ✅ Hilangkan icon (!) dari input saat error */
.form-control.is-invalid {
  background-image: none !important;
  padding-right: 1rem !important;
}

/* Error message styling */
.error-message {
  font-size: 0.8rem;
  font-weight: 500;
  margin-top: 0.25rem;
  color: #dc3545;
}

/* Password toggle icon */
.toggle-password-icon {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  cursor: pointer;
  color: #6c757d;
  font-size: 16px;
  z-index: 10;
}

/* Toggle button styling */
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

/* Form controls */
.form-control {
  padding: 0.75rem 1rem;
  border-radius: 10px;
  border: 1.5px solid #e2e8f0;
  transition: all 0.2s ease;
}

.form-control:focus {
  border-color: #2563EB;
  box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.15);
}

.form-floating > label {
  padding: 0.75rem 1rem;
}

.form-floating > .form-control {
  height: 56px;
}

/* Submit button */
.btn-primary {
  background-color: #2563EB;
  border: none;
  box-shadow: 0 4px 12px rgba(37, 99, 235, 0.25);
  transition: all 0.3s ease;
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

/* Animation effects */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideUp {
  from { transform: translateY(20px); opacity: 0; }
  to { transform: translateY(0); opacity: 1; }
}

.modal-content {
  animation: slideUp 0.3s ease-out forwards;
}

.modal-backdrop {
  animation: fadeIn 0.3s ease-out forwards;
}

/* Media queries */
@media (max-width: 576px) {
  .custom-login-modal {
    max-width: 100%;
    border-radius: 16px;
  }

  .modal-dialog-centered {
    padding: 0 10px;
  }
}

@media (max-height: 700px) {
  .modal {
    align-items: flex-start;
    padding: 20px 0;
  }
}

@media (min-width: 992px) {
  .custom-login-modal {
    max-width: 480px;
  }
}
</style>