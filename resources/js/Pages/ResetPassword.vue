<template>
  <AppLayout>
    <div class="container p-5">
      <h3>Reset Password</h3>

      <form @submit.prevent="submitReset">
        <!-- Email atau No HP (readonly) -->
        <div class="mb-3" v-if="form.email">
          <label for="email">Email</label>
          <input type="text" v-model="form.email" class="form-control" readonly />
        </div>
        <div class="mb-3" v-else-if="form.no_hp">
          <label for="no_hp">Nomor HP</label>
          <input type="text" v-model="form.no_hp" class="form-control" readonly />
        </div>

        <!-- Password Baru -->
        <div class="mb-3">
          <label for="password">Password Baru</label>
          <div class="position-relative">
            <input 
              :type="passwordVisible ? 'text' : 'password'" 
              v-model="form.password" 
              class="form-control" 
              placeholder="Masukkan password baru" 
            />
            <i 
              class="bi"
              :class="passwordVisible ? 'bi-eye-slash' : 'bi-eye'" 
              @click="togglePasswordVisibility"
              style="top: 50%; right: 10px; position: absolute; transform: translateY(-50%); cursor: pointer;"
            ></i>
          </div>
          <div class="text-danger">{{ form.errors.password }}</div>
        </div>

        <!-- Konfirmasi Password -->
        <div class="mb-3">
          <label for="password_confirmation">Konfirmasi Password</label>
          <div class="position-relative">
            <input 
              :type="confirmPasswordVisible ? 'text' : 'password'" 
              v-model="form.password_confirmation" 
              class="form-control" 
              placeholder="Konfirmasi password"
            />
            <i 
              class="bi"
              :class="confirmPasswordVisible ? 'bi-eye-slash' : 'bi-eye'" 
              @click="toggleConfirmPasswordVisibility"
              style="top: 50%; right: 10px; position: absolute; transform: translateY(-50%); cursor: pointer;"
            ></i>
          </div>
          <div class="text-danger">{{ form.errors.password_confirmation }}</div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Reset Password</button>
      </form>
    </div>
  </AppLayout>
</template>

<script setup>
import { useForm, usePage } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import AppLayout from '@/Layouts/AppLayout.vue'

// Ambil token & email/no_hp dari query URL
const urlParams = new URLSearchParams(window.location.search)
const token = urlParams.get('token')
const email = urlParams.get('email')
const no_hp = urlParams.get('no_hp')

// Inisialisasi form
const form = useForm({
  token: token || '',
  email: email || '',
  no_hp: no_hp || '',
  password: '',
  password_confirmation: ''
})

// State visibility password
const passwordVisible = ref(false)
const confirmPasswordVisible = ref(false)

// Toggle password
function togglePasswordVisibility() {
  passwordVisible.value = !passwordVisible.value
}
function toggleConfirmPasswordVisibility() {
  confirmPasswordVisible.value = !confirmPasswordVisible.value
}

// Submit form
function submitReset() {
  form.post('/reset-password', {
    onFinish: () => form.reset('password', 'password_confirmation')
  })
}
</script>

<style scoped>
.position-relative {
  position: relative;
}
.bi {
  font-size: 1.25rem;
}
</style>
