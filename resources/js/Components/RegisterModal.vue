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
            <button type="button" class="btn-close" @click="closeModal"></button>
          </div>

          <!-- TOGGLE -->
          <div class="mb-3 text-center">
            <button
              class="btn btn-outline-primary me-2"
              :class="{ active: mode === 'email' }"
              @click="mode = 'email'"
            >
              Daftar dengan Email
            </button>
            <button
              class="btn btn-outline-primary"
              :class="{ active: mode === 'no_hp' }"
              @click="mode = 'no_hp'"
            >
              Daftar dengan No HP
            </button>
          </div>

          <form @submit.prevent="handleRegister">
            <div class="mb-3">
              <label class="form-label fw-bold">Nama Lengkap</label>
              <input
                type="text"
                class="form-control border-primary"
                v-model="form.name"
                placeholder="masukkan nama lengkap anda"
              />
              <small class="error-message d-block">{{ errors.name }}</small>
            </div>

            <!-- Email atau No HP -->
            <div class="mb-3" v-if="mode === 'email'">
              <label class="form-label fw-bold">Email</label>
              <input
                type="email"
                class="form-control border-primary"
                v-model="form.email"
                placeholder="masukkan email anda"
              />
              <small class="error-message d-block">{{ errors.email }}</small>
            </div>

            <div class="mb-3" v-if="mode === 'no_hp'">
              <label class="form-label fw-bold">Nomor HP</label>
              <input
                type="text"
                class="form-control border-primary"
                v-model="form.no_hp"
                placeholder="masukkan nomor HP anda"
              />
              <small class="error-message d-block">{{ errors.no_hp }}</small>
            </div>

            <!-- Password -->
            <div class="mb-3">
              <label class="form-label fw-bold">Kata Sandi</label>
              <div class="position-relative">
                <input
                  :type="passwordVisible ? 'text' : 'password'"
                  class="form-control border-primary pe-5"
                  v-model="form.password"
                  placeholder="kata sandi"
                />
                <i
                  :class="passwordVisible ? 'bi bi-eye' : 'bi bi-eye-slash'"
                  class="toggle-password-icon"
                  @click="passwordVisible = !passwordVisible"
                ></i>
              </div>
              <small class="error-message d-block">{{ errors.password }}</small>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold">Konfirmasi Kata Sandi</label>
              <input
                :type="passwordVisible ? 'text' : 'password'"
                class="form-control border-primary"
                v-model="form.password_confirmation"
                placeholder="ulangi kata sandi"
              />
            </div>

            <button type="submit" class="btn btn-primary w-100 py-2 fw-bold">Daftar</button>
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
const emit = defineEmits(['update:visible'])

const mode = ref('email')
const passwordVisible = ref(false)

const form = useForm({
  name: '',
  email: '',
  no_hp: '',
  password: '',
  password_confirmation: '',
})

const errors = computed(() => form.errors)

function closeModal() {
  emit('update:visible', false)
  form.reset()
  passwordVisible.value = false
}

function handleRegister() {
  const url = mode.value === 'email' ? 'register.email' : 'register.no_hp'

  form.post(route (url), {
    onSuccess: () => {
      closeModal()
    }
  })
}
</script>

<style scoped>
/* gunakan style LoginModal */
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
  overflow-y: auto;
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
}

.error-message {
  font-size: 0.875rem;
  color: red;
  min-height: 18px;
}

.toggle-password-icon {
  position: absolute;
  top: 50%;
  right: 12px;
  transform: translateY(-50%);
  cursor: pointer;
  color: #888;
  font-size: 18px;
}

button.active {
  background-color: #2563EB !important;
  color: white !important;
}
</style>
