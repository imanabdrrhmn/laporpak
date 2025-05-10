<template>
<<<<<<< HEAD
  <div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3 border-0">
      <h5 class="card-title mb-0 d-flex align-items-center">
        <i class="bi bi-shield-lock me-2 text-primary"></i>
        Keamanan Akun
      </h5>
    </div>
    <div class="card-body">
      <form @submit.prevent="updatePassword" class="space-y-6">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="currentPassword" class="form-label small fw-medium">Password Saat Ini</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-key"></i>
              </span>
             <input
                id="currentPassword"
                ref="currentPasswordInput"
                v-model="form.current_password"
                :type="showCurrentPassword ? 'text' : 'password'"
                class="form-control"
                placeholder="Masukkan password saat ini"
                autocomplete="current-password"
              />
              <span class="input-group-text bg-light cursor-pointer" @click="showCurrentPassword = !showCurrentPassword">
                <i :class="showCurrentPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </span>
=======
  <section>
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="card-title mb-0 d-flex align-items-center">
          <i class="bi bi-shield-lock me-2 text-primary"></i>
          Keamanan Akun
        </h5>
      </div>
      <div class="card-body">
        <form @submit.prevent="updatePassword" class="space-y-6">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="currentPassword" class="form-label small fw-medium">Password Saat Ini</label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i class="bi bi-key"></i>
                </span>
                <input
                  id="currentPassword"
                  ref="currentPasswordInput"
                  v-model="form.current_password"
                  type="password"
                  class="form-control"
                  placeholder="Masukkan password saat ini"
                  autocomplete="current-password"
                />
              </div>
              <div v-if="form.errors.current_password" class="mt-2 text-sm text-red-600">
                {{ form.errors.current_password }}
              </div>
>>>>>>> 1ddfb1bba59374174a974f84064ec31ea076b760
            </div>
            <div class="col-md-6">
              <label for="newPassword" class="form-label small fw-medium">Password Baru</label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i class="bi bi-lock"></i>
                </span>
                <input
                  id="newPassword"
                  ref="passwordInput"
                  v-model="form.password"
                  type="password"
                  class="form-control"
                  placeholder="Masukkan password baru"
                  autocomplete="new-password"
                />
              </div>
              <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
                {{ form.errors.password }}
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <label for="newPassword" class="form-label small fw-medium">Password Baru</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-lock"></i>
              </span>
              <input
                id="newPassword"
                ref="passwordInput"
                v-model="form.password"
                :type="showNewPassword ? 'text' : 'password'"
                class="form-control"
                placeholder="Masukkan password baru"
                autocomplete="new-password"
              />
              <span class="input-group-text bg-light cursor-pointer" @click="showNewPassword = !showNewPassword">
                <i :class="showNewPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </span>
            </div>
            <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
              {{ form.errors.password }}
            </div>
          </div>
          <div class="col-12">
            <label for="confirmPassword" class="form-label small fw-medium">Konfirmasi Password Baru</label>
            <div class="input-group">
              <span class="input-group-text bg-light">
                <i class="bi bi-lock-fill"></i>
              </span>
              <input
                id="confirmPassword"
                v-model="form.password_confirmation"
                :type="showConfirmPassword ? 'text' : 'password'"
                class="form-control"
                placeholder="Konfirmasi password baru"
                autocomplete="new-password"
              />
              <span class="input-group-text bg-light cursor-pointer" @click="showConfirmPassword = !showConfirmPassword">
                <i :class="showConfirmPassword ? 'bi bi-eye-slash' : 'bi bi-eye'"></i>
              </span>
            </div>
            <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600">
              {{ form.errors.password_confirmation }}
            </div>
          </div>
        </div>
        
        <div class="mt-3 p-3 rounded bg-light-subtle border border-light">
          <div class="d-flex">
            <i class="bi bi-info-circle text-primary me-2 mt-1"></i>
            <div>
              <div class="fw-medium">Persyaratan Password</div>
              <div class="small text-secondary mt-1">
                <ul class="ps-3 mb-0">
                  <li>Minimal 8 karakter</li>
                  <li>Harus mengandung huruf kecil dan huruf besar</li>
                  <li>Harus mengandung setidaknya satu angka</li>
                  <li>Harus mengandung setidaknya satu simbol</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        <div class="d-flex justify-content-end mt-4 gap-2">
          <button type="button" class="btn btn-outline-secondary" @click="form.reset()">
            <i class="bi bi-x-lg me-1"></i>
            Batal
          </button>
          <button
            type="submit"
            class="btn btn-primary px-4"
            :disabled="form.processing"
          >
            <i class="bi bi-shield-check me-2"></i>
            Perbarui Password
          </button>
          <transition
            enter-active-class="transition ease-in-out"
            enter-from-class="opacity-0"
            leave-active-class="transition ease-in-out"
            leave-to-class="opacity-0"
          >
            <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 ms-2">
              Saved.
            </p>
          </transition>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const user = usePage().props.auth.user;

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const currentPasswordInput = ref(null);
const passwordInput = ref(null);

const updatePassword = () => {
  form.put(route('password.update'), {
    preserveScroll: true,
    onSuccess: () => form.reset(),
    onError: () => {
      if (form.errors.password) {
        form.reset('password', 'password_confirmation');
        passwordInput.value.focus();
      }
      if (form.errors.current_password) {
        form.reset('current_password');
        currentPasswordInput.value.focus();
      }
    },
  });
};
</script>

<style scoped>
.btn-primary {
  background-color: #4361ee;
  border-color: #4361ee;
}
.btn-primary:hover {
  background-color: #3a56d4;
  border-color: #3a56d4;
}
.text-primary {
  color: #4361ee !important;
}
.form-control {
  background-color: #ffffff;
  color: #333333;
  border: 1px solid #ccc;
  padding: 0.75rem;
  font-size: 1rem;
  border-radius: 0.375rem;
}
.form-control:focus {
  background-color: #f7fafc;
  border-color: #5a67d8;
  outline: none;
}
.text-red-600 {
  color: #e53e3e;
}
.text-gray-600 {
  color: #718096;
}
</style>