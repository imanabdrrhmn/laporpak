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
                <h4 class="fw-bold mb-1">Daftar Akun</h4>
                <p class="text-muted small">Silakan isi data Anda untuk mulai</p>
              </div>
              <button type="button" class="btn-close custom-close" @click="closeModal" aria-label="Tutup"></button>
            </div>

            <!-- TOGGLE HORIZONTAL with Icon Background -->
            <div class="mb-4 registration-toggle d-flex p-1 bg-light rounded-pill">
              <button
                class="btn btn-toggle flex-grow-1 me-1 py-2 rounded-pill"
                :class="{ active: mode === 'email' }"
                @click="switchMode('email')"
                type="button"
              >
                <i class="bi bi-envelope me-2"></i> Email
              </button>
              <button
                class="btn btn-toggle flex-grow-1 py-2 rounded-pill"
                :class="{ active: mode === 'no_hp' }"
                @click="switchMode('no_hp')"
                type="button"
              >
                <i class="bi bi-phone me-2"></i> No HP
              </button>
            </div>

            <form @submit.prevent="handleRegister" novalidate>
              <!-- Nama -->
              <div class="form-floating mb-3">
                <input
                  type="text"
                  id="fullname"
                  class="form-control"
                  :class="errors.name ? 'is-invalid' : ''"
                  v-model="form.name"
                  placeholder="Nama Lengkap"
                  required
                  @focus="clearError('name')"
                  @blur="validateField('name')"
                />
                <label for="fullname">Nama Lengkap <span class="text-danger">*</span></label>
                <div v-if="errors.name" class="error-message text-danger">{{ errors.name }}</div>
              </div>

              <!-- Email -->
              <div class="form-floating mb-3" v-if="mode === 'email'">
                <input
                  type="email"
                  id="email"
                  class="form-control"
                  :class="errors.email ? 'is-invalid' : ''"
                  v-model="form.email"
                  placeholder="Email"
                  required
                  @focus="clearError('email')"
                  @blur="validateField('email')"
                />
                <label for="email">Email <span class="text-danger">*</span></label>
                <div v-if="errors.email" class="error-message text-danger">{{ errors.email }}</div>
              </div>

              <!-- No HP -->
              <div class="mb-3" v-if="mode === 'no_hp'">
                <label for="phone" class="form-label">Nomor HP <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">+62</span>
                  <input
                    type="tel"
                    id="phone"
                    class="form-control border-start-0"
                    :class="errors.no_hp ? 'is-invalid' : ''"
                    v-model="phoneNumber"
                    placeholder="8123456789"
                    required
                    @focus="clearError('no_hp')"
                    @input="validatePhoneNumber"
                    @blur="validateField('no_hp')"
                  />
                </div>
                <div v-if="errors.no_hp" class="error-message text-danger">{{ errors.no_hp }}</div>
                <small class="text-muted d-block mt-1" v-if="mode === 'no_hp'">Contoh: 81234567890 (tanpa awalan 0)</small>
              </div>

              <!-- Password -->
              <div class="form-floating mb-3">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  id="password"
                  class="form-control"
                  :class="errors.password ? 'is-invalid' : ''"
                  v-model="form.password"
                  placeholder="Kata Sandi"
                  required
                  @focus="clearError('password')"
                  @blur="validateField('password')"
                />
                <label for="password">Kata Sandi <span class="text-danger">*</span></label>
                <i
                  :class="passwordVisible ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill'"
                  class="toggle-password-icon"
                  @click="passwordVisible = !passwordVisible"
                ></i>
                <div v-if="errors.password" class="error-message text-danger">{{ errors.password }}</div>
              </div>

              <!-- Password Strength Meter -->
              <div class="strength-meter mb-3" v-if="form.password">
                <div class="d-flex align-items-center mb-2">
                  <div class="progress flex-grow-1" style="height: 6px;">
                    <div class="progress-bar" :class="passwordStrengthClass" :style="{ width: passwordStrength + '%' }"></div>
                  </div>
                  <span class="ms-2 small fw-bold" :class="passwordStrengthTextClass">{{ passwordStrengthText }}</span>
                </div>
                <div class="password-requirements small">
                  <div :class="form.password.length >= 8 ? 'requirement-met' : 'requirement'">
                    <i :class="form.password.length >= 8 ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i> 
                    Minimal 8 karakter
                  </div>
                  <div :class="hasUpperCase ? 'requirement-met' : 'requirement'">
                    <i :class="hasUpperCase ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i> 
                    Minimal 1 huruf besar
                  </div>
                  <div :class="hasNumber ? 'requirement-met' : 'requirement'">
                    <i :class="hasNumber ? 'bi bi-check-circle-fill' : 'bi bi-circle'"></i> 
                    Minimal 1 angka
                  </div>
                </div>
              </div>

              <!-- Konfirmasi Password -->
              <div class="form-floating mb-4">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  id="password_confirmation"
                  class="form-control"
                  :class="(errors.password_confirmation || passwordMismatch) ? 'is-invalid' : ''"
                  v-model="form.password_confirmation"
                  placeholder="Konfirmasi Kata Sandi"
                  required
                  @focus="clearError('password_confirmation')"
                  @blur="validateField('password_confirmation')"
                />
                <label for="password_confirmation">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
                <div v-if="passwordMismatch || errors.password_confirmation" class="error-message text-danger">
                  {{ passwordMismatch ? 'Kata sandi tidak cocok' : errors.password_confirmation }}
                </div>
              </div>

              <button 
                type="submit" 
                class="btn btn-primary w-100 py-3 rounded-pill fw-bold mb-3"
                :disabled="isSubmitting"
              >
                <span v-if="isSubmitting">
                  <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                  Mendaftar...
                </span>
                <span v-else>Daftar Sekarang</span>
              </button>

              <div class="text-center">
                <p class="mb-0">Sudah punya akun? <a href="#" @click.prevent="goToLogin" class="text-primary fw-bold">Masuk</a></p>
              </div>
            </form>
          </div>
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
  Object.keys(errors.value).forEach(key => {
    errors.value[key] = ''
  })
}

function switchMode(newMode) {
  if (mode.value === newMode) return
  
  mode.value = newMode
  
  Object.keys(errors.value).forEach(key => {
    errors.value[key] = ''
  })
  passwordMismatch.value = false
  
  if (newMode === 'email') {
    form.no_hp = ''
    phoneNumber.value = ''
  } else {
    form.email = ''
  }
}

function validatePhoneNumber() {
  phoneNumber.value = phoneNumber.value.replace(/\D/g, '')
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
  emit('open-login')
}

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

function isValidEmail(email) {
  const re = /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
  return re.test(String(email).toLowerCase())
}

watch(() => form.password_confirmation, (val) => {
  if (val && form.password !== val) {
    passwordMismatch.value = true
  } else {
    passwordMismatch.value = false
  }
})

function validateForm() {
  let isValid = true
  
  if (!form.name) {
    errors.value.name = 'Nama lengkap harus diisi'
    isValid = false
  }
  
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
  
  if (!form.password) {
    errors.value.password = 'Kata sandi harus diisi'
    isValid = false
  } else if (form.password.length < 8) {
    errors.value.password = 'Kata sandi minimal 8 karakter'
    isValid = false
  }
  
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
  if (!validateForm()) {
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
      
      // Handle server errors with custom messages
      if (serverErrors.name) {
        errors.value.name = serverErrors.name
      }
      if (serverErrors.email) {
        if (serverErrors.email.includes('validation.unique') || 
            serverErrors.email.toLowerCase().includes('already been taken')) {
          errors.value.email = 'Email sudah terdaftar'
        } else {
          errors.value.email = serverErrors.email
        }
      }
      if (serverErrors.no_hp) {
        errors.value.no_hp = serverErrors.no_hp
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

.form-control.is-invalid {
  background-image: none !important;
  padding-right: 1rem !important;
}

/* ✅ Hilangkan icon alert dari feedback */
.invalid-feedback i {
  display: none !important;
}

.invalid-feedback::before {
  display: none !important;
}

/* Error message styling */
.error-message {
  font-size: 0.8rem;
  font-weight: 500;
  margin-top: 0.25rem;
}

/* ✅ Jaga ukuran modal tetap saat error muncul */
.invalid-feedback.position-static {
  display: block;
  min-height: 1rem;
  line-height: 1rem;
  margin-top: 0.25rem;
  font-size: 0.75rem;
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

/* Password strength items */
.strength-meter {
  background: #f8f9fa;
  padding: 12px;
  border-radius: 12px;
}

.password-requirements {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.requirement {
  color: #6c757d;
  display: flex;
  align-items: center;
  gap: 6px;
}

.requirement-met {
  color: #198754;
  display: flex;
  align-items: center;
  gap: 6px;
  font-weight: 500;
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
  background-color: #2563EB;
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

/* Animasi dan efek */
.toggle-password-icon {
  transition: all 0.2s ease;
}

.toggle-password-icon:hover {
  color: #2563EB;
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

/* Media queries */
@media (max-width: 576px) {
  .custom-login-modal {
    max-width: 100%;
    border-radius: 16px;
  }

  .modal-dialog-centered {
    padding: 0 10px;
  }

  .password-requirements {
    grid-template-columns: 1fr;
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

/* Input group styling for phone number */
.input-group .input-group-text {
  border-radius: 10px 0 0 10px;
  border: 1.5px solid #e2e8f0;
  border-right: none;
  color: #6c757d;
  font-weight: 500;
}

.input-group .form-control {
  border-radius: 0 10px 10px 0;
}
</style>