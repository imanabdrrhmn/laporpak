<template>
  <div v-if="visible">
    <div class="modal-backdrop fade show" @click.self="closeModal"></div>
    <div class="modal fade show d-block" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-login-modal mx-auto">
          <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
              <h4 class="fw-bold">Daftar Akun</h4>
              <p class="text-muted mb-4">Silakan isi data Anda</p>
            </div>
            <button type="button" class="btn-close" @click="closeModal" aria-label="Tutup"></button>
          </div>

          <!-- TOGGLE HORIZONTAL -->
          <div class="mb-4 registration-toggle d-flex">
            <button
              class="btn btn-outline-primary flex-grow-1 me-2"
              :class="{ active: mode === 'email' }"
              @click="switchMode('email')"
              type="button"
            >
              <i class="bi bi-envelope me-1"></i> Email
            </button>
            <button
              class="btn btn-outline-primary flex-grow-1"
              :class="{ active: mode === 'no_hp' }"
              @click="switchMode('no_hp')"
              type="button"
            >
              <i class="bi bi-phone me-1"></i> No HP
            </button>
          </div>

          <form @submit.prevent="handleRegister" novalidate>
            <!-- Nama -->
            <div class="mb-3">
              <label for="fullname" class="form-label fw-bold">Nama Lengkap <span class="text-danger">*</span></label>
              <input
                type="text"
                id="fullname"
                class="form-control"
                :class="errors.name ? 'is-invalid border-danger' : 'border-primary'"
                v-model="form.name"
                placeholder="Masukkan nama lengkap Anda"
                required
                @focus="clearError('name')"
              />
              <div v-if="errors.name" class="invalid-feedback d-block">{{ errors.name }}</div>
            </div>

            <!-- Email -->
            <div class="mb-3" v-if="mode === 'email'">
              <label for="email" class="form-label fw-bold">Email <span class="text-danger">*</span></label>
              <input
                type="email"
                id="email"
                class="form-control"
                :class="errors.email ? 'is-invalid border-danger' : 'border-primary'"
                v-model="form.email"
                placeholder="Masukkan email Anda"
                required
                @focus="clearError('email')"
              />
              <div v-if="errors.email" class="invalid-feedback d-block">{{ errors.email }}</div>
            </div>

            <!-- No HP -->
            <div class="mb-3" v-if="mode === 'no_hp'">
              <label for="phone" class="form-label fw-bold">Nomor HP <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text border-primary">+62</span>
                <input
                  type="tel"
                  id="phone"
                  class="form-control"
                  :class="errors.no_hp ? 'is-invalid border-danger' : 'border-primary'"
                  v-model="phoneNumber"
                  placeholder="8123456789"
                  required
                  @focus="clearError('no_hp')"
                  @input="validatePhoneNumber"
                />
              </div>
              <div v-if="errors.no_hp" class="invalid-feedback d-block">{{ errors.no_hp }}</div>
              <small class="text-muted" v-if="mode === 'no_hp'">Contoh: 81234567890 (tanpa awalan 0)</small>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label for="password" class="form-label fw-bold">Kata Sandi <span class="text-danger">*</span></label>
              <div class="position-relative">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  id="password"
                  class="form-control"
                  :class="errors.password ? 'is-invalid border-danger' : 'border-primary'"
                  v-model="form.password"
                  placeholder="Minimal 8 karakter"
                  required
                  @focus="clearError('password')"
                />
                <i
                  :class="passwordVisible ? 'bi bi-eye-fill' : 'bi bi-eye-slash-fill'"
                  class="toggle-password-icon"
                  @click="passwordVisible = !passwordVisible"
                ></i>
              </div>
              <div v-if="errors.password" class="invalid-feedback d-block">{{ errors.password }}</div>
              <div class="password-strength mt-2" v-if="form.password">
                <div class="d-flex align-items-center mb-1">
                  <div class="progress flex-grow-1" style="height: 6px;">
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
            <div class="mb-4">
              <label for="password_confirmation" class="form-label fw-bold">Konfirmasi Kata Sandi <span class="text-danger">*</span></label>
              <div class="position-relative">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  id="password_confirmation"
                  class="form-control"
                  :class="(errors.password_confirmation || passwordMismatch) ? 'is-invalid border-danger' : 'border-primary'"
                  v-model="form.password_confirmation"
                  placeholder="Ulangi kata sandi"
                  required
                />
              </div>
              <div v-if="passwordMismatch" class="invalid-feedback d-block">Kata sandi tidak cocok</div>
            </div>

            <button 
              type="submit" 
              class="btn btn-primary w-100 py-2 fw-bold"
              :disabled="isSubmitting"
            >
              <span v-if="isSubmitting">
                <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
                Mendaftar...
              </span>
              <span v-else>Daftar</span>
            </button>

            <div class="text-center mt-3">
              <p class="mb-0">Sudah punya akun? <a href="#" @click.prevent="goToLogin" class="text-primary fw-bold">Masuk</a></p>
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

const form = useForm({
  name: '',
  email: '',
  no_hp: '',
  password: '',
  password_confirmation: '',
})

const errors = computed(() => form.errors)

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
  if (form.errors[field]) {
    form.errors[field] = null
  }
  
  if (field === 'password' && passwordMismatch.value) {
    passwordMismatch.value = false
  }
}

function goToLogin() {
  closeModal()
  emit('switch-to-login')
}

// Check password match
watch(() => form.password_confirmation, (val) => {
  if (val && form.password !== val) {
    passwordMismatch.value = true
  } else {
    passwordMismatch.value = false
  }
})

function handleRegister() {
  // Validasi password match
  if (form.password !== form.password_confirmation) {
    passwordMismatch.value = true
    return
  }
  
  const url = mode.value === 'email' ? 'register.email' : 'register.no_hp'
  
  isSubmitting.value = true
  
  form.post(route(url), {
    onSuccess: () => {
      isSubmitting.value = false
      closeModal()
    },
    onError: () => {
      isSubmitting.value = false
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
  box-shadow: 0 0 40px rgba(0, 0, 0, 0.1);
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
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
}

.invalid-feedback {
  font-size: 0.875rem;
  color: #dc3545;
  display: block;
  margin-top: 0.25rem;
}

.toggle-password-icon {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  cursor: pointer;
  color: #6c757d;
  font-size: 18px;
}

button.active {
  background-color: #2563EB !important;
  color: white !important;
}

.password-requirements {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 0.5rem;
  margin-top: 0.5rem;
}

.registration-toggle .btn {
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-weight: 500;
}

.input-group-text {
  background-color: #f8f9fa;
}

/* Hapus icon alert pada input yang invalid */
.form-control.is-invalid:focus {
  box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
}

/* Hilangkan background icon alert */
.form-control.is-invalid,
.was-validated .form-control:invalid {
  background-image: none;
  padding-right: 0.75rem;
}

/* Untuk input group */
.input-group .form-control.is-invalid,
.was-validated .input-group .form-control:invalid {
  z-index: 1;
  background-image: none;
}

/* Animasi untuk tombol dan elemen interaktif */
.btn, .toggle-password-icon {
  transition: all 0.2s ease;
}

.btn:hover {
  transform: translateY(-1px);
  box-shadow: 0 4px 6px rgba(37, 99, 235, 0.1);
}

.toggle-password-icon:hover {
  color: #2563EB;
}
</style>