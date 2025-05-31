<template>
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
                <i :class="showCurrentPassword ? 'bi bi-eye' : 'bi bi-eye-slash'"></i>
              </span>
            </div>
            <div v-if="form.errors.current_password" class="mt-2 text-sm text-red-600">
              Password saat ini tidak benar
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
                :class="{ 'is-invalid': !isPasswordValid && form.password.length > 0 }"
                placeholder="Masukkan password baru"
                autocomplete="new-password"
                @input="validatePassword"
              />
              <span class="input-group-text bg-light cursor-pointer" @click="showNewPassword = !showNewPassword">
                <i :class="showNewPassword ? 'bi bi-eye' : 'bi bi-eye-slash'"></i>
              </span>
            </div>
            <div v-if="form.errors.password" class="mt-2 text-sm text-red-600">
              {{ form.errors.password }}
            </div>
            <div v-if="!isPasswordValid && form.password.length > 0" class="mt-2 text-sm text-red-600">
              Password tidak memenuhi persyaratan keamanan
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
                :class="{ 'is-invalid': !isPasswordMatch && form.password_confirmation.length > 0 }"
                placeholder="Konfirmasi password baru"
                autocomplete="new-password"
                @input="validatePasswordMatch"
              />
              <span class="input-group-text bg-light cursor-pointer" @click="showConfirmPassword = !showConfirmPassword">
                <i :class="showConfirmPassword ? 'bi bi-eye' : 'bi bi-eye-slash'"></i>
              </span>
            </div>
            <div v-if="form.errors.password_confirmation" class="mt-2 text-sm text-red-600">
              {{ form.errors.password_confirmation }}
            </div>
            <div v-if="!isPasswordMatch && form.password_confirmation.length > 0" class="mt-2 text-sm text-red-600">
              Konfirmasi password tidak cocok
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
                  <li :class="{ 'text-success': hasMinLength, 'text-danger': !hasMinLength && form.password.length > 0 }">
                    <i :class="hasMinLength ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1'"></i>
                    Minimal 8 karakter
                  </li>
                  <li :class="{ 'text-success': hasUpperLower, 'text-danger': !hasUpperLower && form.password.length > 0 }">
                    <i :class="hasUpperLower ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1'"></i>
                    Harus mengandung huruf kecil dan huruf besar
                  </li>
                  <li :class="{ 'text-success': hasNumber, 'text-danger': !hasNumber && form.password.length > 0 }">
                    <i :class="hasNumber ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1'"></i>
                    Harus mengandung setidaknya satu angka
                  </li>
                  <li :class="{ 'text-success': hasSymbol, 'text-danger': !hasSymbol && form.password.length > 0 }">
                    <i :class="hasSymbol ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1'"></i>
                    Harus mengandung setidaknya satu simbol
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
        
        <div class="d-flex justify-content-end mt-4 gap-2">
          <button type="button" class="btn btn-outline-secondary" @click="resetForm">
            <i class="bi bi-x-lg me-1"></i>
            Batal
          </button>
          <button
            type="submit"
            class="btn btn-primary px-4"
            :disabled="form.processing || !canSubmit"
          >
            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
            <i v-else class="bi bi-shield-check me-2"></i>
            {{ form.processing ? 'Memproses...' : 'Perbarui Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Success Modal -->
  <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0 pb-0">
          <h5 class="modal-title text-success" id="successModalLabel">
            <i class="bi bi-check-circle-fill me-2"></i>
            Berhasil!
          </h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body pt-0">
          <p class="mb-0">Password Anda berhasil diperbarui. Silakan gunakan password baru untuk login selanjutnya.</p>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, computed, nextTick } from 'vue';

const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

// Password validation states
const hasMinLength = ref(false);
const hasUpperLower = ref(false);
const hasNumber = ref(false);
const hasSymbol = ref(false);
const isPasswordMatch = ref(true);

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

// Computed properties for validation
const isPasswordValid = computed(() => {
  return hasMinLength.value && hasUpperLower.value && hasNumber.value && hasSymbol.value;
});

const canSubmit = computed(() => {
  return form.current_password.length > 0 && 
         form.password.length > 0 && 
         form.password_confirmation.length > 0 && 
         isPasswordValid.value && 
         isPasswordMatch.value &&
         !form.processing;
});

const hasFormChanged = computed(() => {
  return form.current_password.length > 0 || 
         form.password.length > 0 || 
         form.password_confirmation.length > 0;
});

// Password validation function
const validatePassword = () => {
  const password = form.password;
  
  // Check minimum length
  hasMinLength.value = password.length >= 8;
  
  // Check for uppercase and lowercase
  hasUpperLower.value = /[a-z]/.test(password) && /[A-Z]/.test(password);
  
  // Check for numbers
  hasNumber.value = /\d/.test(password);
  
  // Check for symbols
  hasSymbol.value = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);
  
  // Also validate password match when password changes
  validatePasswordMatch();
};

// Password match validation
const validatePasswordMatch = () => {
  isPasswordMatch.value = form.password === form.password_confirmation || form.password_confirmation.length === 0;
};

// Reset form function
const resetForm = () => {
  form.reset();
  hasMinLength.value = false;
  hasUpperLower.value = false;
  hasNumber.value = false;
  hasSymbol.value = false;
  isPasswordMatch.value = true;
};

// Update password function
const updatePassword = () => {
  // Check if form has changed
  if (!hasFormChanged.value) {
    return; // Don't submit if no changes
  }

  // Validate before submit
  if (!isPasswordValid.value) {
    return;
  }

  if (!isPasswordMatch.value) {
    return;
  }

  form.put(route('password.reset.update'), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset();
      resetForm();
      
      // Show success modal
      nextTick(() => {
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
      });
    },
    onError: () => {
      if (form.errors.password) {
        form.reset('password', 'password_confirmation');
        passwordInput.value?.focus();
      }
      if (form.errors.current_password) {
        form.reset('current_password');
        currentPasswordInput.value?.focus();
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
.btn-primary:disabled {
  background-color: #6c757d;
  border-color: #6c757d;
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
.form-control.is-invalid {
  border-color: #dc3545;
}
.text-red-600 {
  color: #e53e3e;
}
.text-gray-600 {
  color: #718096;
}
.text-success {
  color: #28a745 !important;
}
.text-danger {
  color: #dc3545 !important;
}
.cursor-pointer {
  cursor: pointer;
}
.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}
</style>