<template>
  <AppLayout>
    <div class="container py-5" style="max-width: 480px;">
      <h3 class="mb-4 text-center">Reset Password</h3>

      <form @submit.prevent="submitReset" novalidate>
        <!-- Email atau No HP (readonly) -->
        <div class="mb-3" v-if="form.email">
          <label for="email" class="form-label fw-semibold">Email</label>
          <input
            type="text"
            id="email"
            v-model="form.email"
            class="form-control bg-light"
            readonly
          />
        </div>
        <div class="mb-3" v-else-if="form.no_hp">
          <label for="no_hp" class="form-label fw-semibold">Nomor HP</label>
          <input
            type="text"
            id="no_hp"
            v-model="form.no_hp"
            class="form-control bg-light"
            readonly
          />
        </div>

        <!-- Password Baru -->
        <div class="mb-3">
          <label for="password" class="form-label fw-semibold">Password Baru</label>
          <div class="input-group">
            <input
              :type="passwordVisible ? 'text' : 'password'"
              id="password"
              v-model="form.password"
              class="form-control"
              placeholder="Masukkan password baru"
              :class="{ 'is-invalid': form.errors.password }"
              autocomplete="new-password"
              required
            />
            <button
              type="button"
              class="btn btn-outline-secondary"
              @click="togglePasswordVisibility"
              tabindex="-1"
              :aria-label="passwordVisible ? 'Sembunyikan password' : 'Tampilkan password'"
            >
              <i :class="passwordVisible ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
            </button>
          </div>
          <div class="invalid-feedback d-block">{{ form.errors.password }}</div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-4">
          <label for="password_confirmation" class="form-label fw-semibold"
            >Konfirmasi Password</label
          >
          <div class="input-group">
            <input
              :type="confirmPasswordVisible ? 'text' : 'password'"
              id="password_confirmation"
              v-model="form.password_confirmation"
              class="form-control"
              placeholder="Konfirmasi password"
              :class="{ 'is-invalid': form.errors.password_confirmation }"
              autocomplete="new-password"
              required
            />
            <button
              type="button"
              class="btn btn-outline-secondary"
              @click="toggleConfirmPasswordVisibility"
              tabindex="-1"
              :aria-label="confirmPasswordVisible
                ? 'Sembunyikan konfirmasi password'
                : 'Tampilkan konfirmasi password'"
            >
              <i :class="confirmPasswordVisible ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
            </button>
          </div>
          <div class="invalid-feedback d-block">{{ form.errors.password_confirmation }}</div>
        </div>

        <!-- Submit Button -->
        <button
          type="submit"
          class="btn btn-primary w-100 fw-semibold py-2"
          :disabled="form.processing"
        >
          <span v-if="form.processing">Memproses...</span>
          <span v-else>Reset Password</span>
        </button>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Ambil token, email, no_hp dari URL query string
const urlParams = new URLSearchParams(window.location.search)
const token = urlParams.get('token') || ''
const email = urlParams.get('email') || ''
const no_hp = urlParams.get('no_hp') || ''

const form = useForm({
  token,
  email,
  no_hp,
  password: '',
  password_confirmation: '',
})

const passwordVisible = ref(false)
const confirmPasswordVisible = ref(false)

function togglePasswordVisibility() {
  passwordVisible.value = !passwordVisible.value
}

function toggleConfirmPasswordVisibility() {
  confirmPasswordVisible.value = !confirmPasswordVisible.value
}

function submitReset() {
  form.post('/reset-password', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('password', 'password_confirmation')
    },
  })
}
</script>

<style scoped>
.container {
  min-height: 70vh;
}

input[readonly] {
  user-select: all;
}

.btn-outline-secondary {
  border-top-left-radius: 0;
  border-bottom-left-radius: 0;
}

input.is-invalid {
  border-color: #dc3545;
}

.invalid-feedback {
  font-size: 0.875rem;
}
</style>
