<template>
  <div v-if="visible">
    <div class="modal-backdrop fade show" @click.self="closeModal"></div>
    <div class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-login-modal mx-auto">
          <div class="d-flex justify-content-between align-items-start mb-2">
            <div>
              <h5 class="fw-bold">Daftar Akun</h5>
              <p class="text-muted small mb-2">Silakan isi data Anda</p>
            </div>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Tutup"></button>
          </div>

          <!-- TOGGLE HORIZONTAL -->
          <div class="mb-3 registration-toggle d-flex">
            <button
              class="btn btn-outline-primary flex-grow-1 me-2 py-1"
              :class="{ active: mode === 'email' }"
              @click="switchMode('email')"
              type="button"
            >
              <i class="bi bi-envelope me-1"></i> Email
            </button>
            <button
              class="btn btn-outline-primary flex-grow-1 py-1"
              :class="{ active: mode === 'no_hp' }"
              @click="switchMode('no_hp')"
              type="button"
            >
              <i class="bi bi-phone me-1"></i> No HP
            </button>
          </div>

          <form @submit.prevent="handleRegister" novalidate>
            <!-- Nama -->
            <div class="mb-2">
              <label for="fullname" class="form-label fw-bold small">Nama Lengkap <span class="text-danger">*</span></label>
              <input
                type="text"
                id="fullname"
                class="form-control form-control-sm"
                :class="errors.name ? 'border-danger' : 'border-primary'"
                v-model="form.name"
                placeholder="Masukkan nama lengkap Anda"
                required
                @focus="clearError('name')"
                @blur="validateField('name')"
              />
              <div v-if="errors.name" class="error-message">{{ errors.name }}</div>
            </div>

            <!-- Email -->
            <div class="mb-2" v-if="mode === 'email'">
              <label for="email" class="form-label fw-bold small">Email <span class="text-danger">*</span></label>
              <input
                type="email"
                id="email"
                class="form-control form-control-sm"
                :class="errors.email ? 'border-danger' : 'border-primary'"
                v-model="form.email"
                placeholder="Masukkan email Anda"
                required
                @focus="clearError('email')"
                @blur="validateField('email')"
              />
              <div v-if="errors.email" class="error-message">{{ errors.email }}</div>
            </div>

            <!-- No HP -->
            <div class="mb-2" v-if="mode === 'no_hp'">
              <label for="phone" class="form-label fw-bold small">Nomor HP <span class="text-danger">*</span></label>
              <div class="input-group input-group-sm">
                <span class="input-group-text border-primary">+62</span>
                <input
                  type="tel"
                  id="phone"
                  class="form-control form-control-sm"
                  :class="errors.no_hp ? 'border-danger' : 'border-primary'"
                  v-model="phoneNumber"
                  placeholder="8123456789"
                  required
                  @focus="clearError('no_hp')"
                  @input="validatePhoneNumber"
                  @blur="validateField('no_hp')"
                />
              </div>
              <div v-if="errors.no_hp" class="error-message">{{ errors.no_hp }}</div>
              <small class="text-muted small" v-if="mode === 'no_hp'">Contoh: 81234567890 (tanpa awalan 0)</small>
            </div>

            <!-- Password -->
            <div class="mb-2">
              <label for="password" class="form-label fw-bold small">Kata Sandi <span class="text-danger">*</span></label>
              <div class="position-relative">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  id="password"
                  class="form-control form-control-sm"
                  :class="errors.password ? 'border-danger' : 'border-primary'"
                  v-model="form.password"
                  placeholder="Minimal 8 karakter"
                  required
                  @focus="clearError('password')"
                  @blur="validateField('password')"
                />
                <i
                  :class="passwordVisible ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill'"
                  class="toggle-password-icon"
                  @click="passwordVisible = !passwordVisible"
                ></i>
              </div>
              <div v-if="errors.password" class="error-message">{{ errors.password }}</div>
              <div class="password-strength mt-1" v-if="form.password">
                <div class="d-flex align-items-center mb-1">
                  <div class="progress flex-grow-1" style="height: 4px;">
                    <div class="progress-bar" :class="passwordStrengthClass" :style="{ width: passwordStrength + '%' }"></div>
                  </div>
                  <span class="ms-2 small" :class="passwordStrengthTextClass">{{ passwordStrengthText }}</span>
                </div>
                <div class="password-requirements small">
                  <div :class="form.password.length >= 8 ? 'text-success' : 'text-muted'">
                    <i :class="form.password.length >= 8 ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i> Minimal 8 karakter
                  </div>
                  <div :class="hasUpperCase ? 'text-success' : 'text-muted'">
                    <i :class="hasUpperCase ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i> Minimal 1 huruf besar
                  </div>
                  <div :class="hasNumber ? 'text-success' : 'text-muted'">
                    <i :class="hasNumber ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i> Minimal 1 angka
                  </div>
                </div>
              </div>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-3">
              <label for="password_confirmation" class="form-label fw-bold small">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
              <div class="position-relative">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  id="password_confirmation"
                  class="form-control form-control-sm"
                  :class="(errors.password_confirmation || passwordMismatch) ? 'border-danger' : 'border-primary'"
                  v-model="form.password_confirmation"
                  placeholder="Ulangi kata sandi"
                  required
                  @focus="clearError('password_confirmation')"
                  @blur="validateField('password_confirmation')"
                />
              </div>
              <div v-if="passwordMismatch || errors.password_confirmation" class="error-message">
                {{ passwordMismatch ? 'Kata sandi tidak cocok' : errors.password_confirmation }}
              </div>
            </div>

            <!-- Pesan Alert untuk Error -->
            <div v-if="showErrorAlert" class="alert alert-danger alert-dismissible fade show py-2 px-3 small" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-1"></i>
              <strong>Perhatian!</strong> Mohon lengkapi semua data yang diperlukan.
              <button type="button" class="btn-close btn-close-sm" @click="showErrorAlert = false" aria-label="Close"></button>
            </div>

            <button 
              type="submit" 
              class="btn btn-primary w-100 py-1 fw-bold"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting">
                <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                Mendaftar...
              </span>
              <span v-else>Daftar</span>
            </button>

            <div class="text-center mt-2">
              <p class="mb-0 small">Sudah punya akun? <a href="#" @click.prevent="goToLogin" class="text-primary fw-bold">Masuk</a></p>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'

const props = defineProps({ visible: Boolean })
const emit = defineEmits(['update:visible', 'switch-to-login'])

const mode = ref('email')
const passwordVisible = ref(false)
const isSubmitting = ref(false)
const phoneNumber = ref('')
const passwordMismatch = ref(false)
const showErrorAlert = ref(false)

const form = useForm({
  name: '',
  email: '',
  no_hp: '',
  password: '',
  password_confirmation: '',
})

const errors = ref({
  name: '',
  email: '',
  no_hp: '',
  password: '',
  password_confirmation: ''
})

// Password strength
const hasUpperCase = computed(() => /[A-Z]/.test(form.password))
const hasNumber = computed(() => /[0-9]/.test(form.password))
const hasSpecialChar = computed(() => /[!@#$%^&*(),.?":{}|<>]/.test(form.password))

const passwordStrength = computed(() => {
  if (!form.password) return 0
  
  let strength = 0
  
  if (form.password.length >= 8) strength += 25
  if (hasUpperCase.value) strength += 25
  if (hasNumber.value) strength += 25
  if (hasSpecialChar.value) strength += 25
  
  return strength
})

const passwordStrengthText = computed(() => {
  const strength = passwordStrength.value
  if (strength === 0) return ''
  if (strength <= 25) return 'Lemah'
  if (strength <= 50) return 'Sedang'
  if (strength <= 75) return 'Kuat'
  return 'Sangat Kuat'
})

const passwordStrengthClass = computed(() => {
  const strength = passwordStrength.value
  if (strength <= 25) return 'bg-danger'
  if (strength <= 50) return 'bg-warning'
  if (strength <= 75) return 'bg-info'
  return 'bg-success'
})

const passwordStrengthTextClass = computed(() => {
  const strength = passwordStrength.value
  if (strength <= 25) return 'text-danger'
  if (strength <= 50) return 'text-warning'
  if (strength <= 75) return 'text-info'
  return 'text-success'
})

function closeModal() {
  emit('update:visible', false)
  resetForm()
}

function resetForm() {
  form.reset()
  phoneNumber.value = ''
  passwordVisible.value = false
  passwordMismatch.value = false
  showErrorAlert.value = false
  Object.keys(errors.value).forEach(key => {
    errors.value[key] = ''
  })
}

function switchMode(newMode) {
  mode.value = newMode
  clearError('email')
  clearError('no_hp')
}

function validatePhoneNumber() {
  // Hanya menerima angka
  phoneNumber.value = phoneNumber.value.replace(/\D/g, '')
  
  // Update form.no_hp dengan format yang benar untuk backend
  form.no_hp = phoneNumber.value ? `+62${phoneNumber.value}` : ''
}

function clearError(field) {
  errors.value[field] = ''
  
  if (field === 'password' && passwordMismatch.value) {
    passwordMismatch.value = false
  }
}

function goToLogin() {
  emit('update:visible', false)
  emit('switch-to-login')
}

// Validasi input saat field kehilangan fokus
function validateField(field) {
  if (field === 'name' && !form.name) {
    errors.value.name = 'Nama lengkap harus diisi'
  }
  
  if (field === 'email' && !form.email && mode.value === 'email') {
    errors.value.email = 'Email harus diisi'
  } else if (field === 'email' && form.email && !isValidEmail(form.email)) {
    errors.value.email = 'Format email tidak valid'
  }
  
  if (field === 'no_hp' && !phoneNumber.value && mode.value === 'no_hp') {
    errors.value.no_hp = 'Nomor HP harus diisi'
  } else if (field === 'no_hp' && phoneNumber.value && phoneNumber.value.length < 9) {
    errors.value.no_hp = 'Nomor HP minimal 9 digit'
  }
  
  if (field === 'password' && !form.password) {
    errors.value.password = 'Kata sandi harus diisi'
  } else if (field === 'password' && form.password.length < 8) {
    errors.value.password = 'Kata sandi minimal 8 karakter'
  }
  
  if (field === 'password_confirmation' && !form.password_confirmation) {
    errors.value.password_confirmation = 'Konfirmasi kata sandi harus diisi'
  } else if (field === 'password_confirmation' && form.password !== form.password_confirmation) {
    passwordMismatch.value = true
  }
}

// Validasi email
function isValidEmail(email) {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  return re.test(String(email).toLowerCase())
}

// Check password match
watch(() => form.password_confirmation, (val) => {
  if (val && form.password !== val) {
    passwordMismatch.value = true
  } else {
    passwordMismatch.value = false
  }
})

// Validasi form sebelum submit
function validateForm() {
  let isValid = true
  
  // Validasi nama
  if (!form.name) {
    errors.value.name = 'Nama lengkap harus diisi'
    isValid = false
  }
  
  // Validasi email/no_hp berdasarkan mode
  if (mode.value === 'email') {
    if (!form.email) {
      errors.value.email = 'Email harus diisi'
      isValid = false
    } else if (!isValidEmail(form.email)) {
      errors.value.email = 'Format email tidak valid'
      isValid = false
    }
  } else {
    if (!phoneNumber.value) {
      errors.value.no_hp = 'Nomor HP harus diisi'
      isValid = false
    } else if (phoneNumber.value.length < 9) {
      errors.value.no_hp = 'Nomor HP minimal 9 digit'
      isValid = false
    }
  }
  
  // Validasi password
  if (!form.password) {
    errors.value.password = 'Kata sandi harus diisi'
    isValid = false
  } else if (form.password.length < 8) {
    errors.value.password = 'Kata sandi minimal 8 karakter'
    isValid = false
  }
  
  // Validasi konfirmasi password
  if (!form.password_confirmation) {
    errors.value.password_confirmation = 'Konfirmasi kata sandi harus diisi'
    isValid = false
  } else if (form.password !== form.password_confirmation) {
    passwordMismatch.value = true
    isValid = false
  }
  
  return isValid
}

function handleRegister() {
  // Reset alert
  showErrorAlert.value = false
  
  // Validasi form
  if (!validateForm()) {
    showErrorAlert.value = true
    return
  }
  
  const url = mode.value === 'email' ? 'register.email' : 'register.no_hp'
  
  isSubmitting.value = true
  
  form.post(route(url), {
    onSuccess: () => {
      isSubmitting.value = false
      closeModal()
    },
    onError: (serverErrors) => {
      isSubmitting.value = false
      
      // Handle server errors
      if (serverErrors.name) errors.value.name = serverErrors.name
      if (serverErrors.email) errors.value.email = serverErrors.email
      if (serverErrors.no_hp) errors.value.no_hp = serverErrors.no_hp
      if (serverErrors.password) errors.value.password = serverErrors.password
      
      showErrorAlert.value = true
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

.toggle-password-icon {
  position: absolute;
  top: 50%;
  right: 10px;
  transform: translateY(-50%);
  cursor: pointer;
  color: #6c757d;
  font-size: 16px;
}

button.active {
  background-color: #2563EB !important;
  color: white !important;
}

.password-requirements {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 0.25rem;
  margin-top: 0.25rem;
  font-size: 0.75rem;
}

.registration-toggle .btn {
  padding: 0.25rem 0.75rem;
  border-radius: 0.375rem;
  font-weight: 500;
  font-size: 0.875rem;
}

.input-group-text {
  background-color: #f8f9fa;
  font-size: 0.875rem;
}

/* Hilangkan background icon alert */
.form-control.is-invalid,
.was-validated .form-control:invalid {
  background-image: none;
  padding-right: 0.75rem;
}

/* Alert error */
.alert {
  padding: 0.5rem 0.75rem;
  margin-bottom: 0.75rem;
  border-radius: 0.375rem;
  border: 1px solid transparent;
}

.alert-danger {
  color: #721c24;
  background-color: #f8d7da;
  border-color: #f5c6cb;
}

.alert-dismissible .btn-close {
  padding: 0.5rem 0.75rem;
  font-size: 0.75rem;
}

/* Animasi untuk tombol dan elemen interaktif */
.btn, .toggle-password-icon {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(37, 99, 235, 0.1);
}

.toggle-password-icon:hover {
  color: #2563EB;
}

/* Ukuran font pada form */
.form-label {
  margin-bottom: 0.25rem;
}

.form-control-sm {
  font-size: 0.875rem;
  padding: 0.25rem 0.5rem;
}

.small {
  font-size: 0.75rem;
}

/* Media queries */
@media (max-width: 576px) {
  .custom-login-modal {
    max-width: 100%;
    border-radius: 12px;
    padding: 20px;
  }
  
  .modal-dialog-centered {
    padding: 0 10px;
  }
  
  .password-requirements {
    grid-template-columns: 1fr;
  }
  
  .form-label {
    font-size: 0.8rem;
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
    max-width: 450px;
  }
}
</style>