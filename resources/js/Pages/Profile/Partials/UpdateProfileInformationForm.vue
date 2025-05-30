```vue
<template>
  <div>
    <!-- Profile Information Section -->
    <div class="card border-0 shadow-sm mb-4">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="card-title mb-0 d-flex align-items-center">
          <i class="bi bi-person-fill me-2 text-primary"></i>
          Informasi Pribadi
        </h5>
      </div>
      <div class="card-body">
        <form class="space-y-6">
          <div class="row g-3">
            <div class="col-md-6">
              <label for="name" class="form-label small fw-medium">Nama Lengkap</label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i class="bi bi-person"></i>
                </span>
                <input
                  type="text"
                  class="form-control"
                  :class="{ 'is-invalid': validationErrors.name }"
                  id="name"
                  v-model="profileForm.name"
                  required
                  autocomplete="given-name"
                />
              </div>
              <div v-if="profileForm.errors.name" class="mt-2 text-sm text-red-600">
                {{ profileForm.errors.name }}
              </div>
              <div v-if="validationErrors.name" class="invalid-feedback">
                {{ validationErrors.name }}
              </div>
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label small fw-medium">Email</label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i class="bi bi-envelope"></i>
                </span>
                <input
                  type="email"
                  class="form-control"
                  :class="{ 'is-invalid': validationErrors.email }"
                  id="email"
                  v-model="profileForm.email"
                  :disabled="user.email && user.email.length > 0"
                  :required="!user.email || user.email.length === 0"
                  autocomplete="username"
                />
                <span v-if="user.email && user.email.length > 0" class="input-group-text bg-secondary">
                  <i class="bi bi-lock-fill text-white"></i>
                </span>
              </div>
              <div v-if="profileForm.errors.email" class="mt-2 text-sm text-red-600">
                {{ profileForm.errors.email }}
              </div>
              <div v-if="validationErrors.email" class="invalid-feedback">
                {{ validationErrors.email }}
              </div>
              <small v-if="user.email && user.email.length > 0" class="text-muted">
                Email tidak dapat diubah setelah diverifikasi
              </small>
            </div>
            <div class="col-md-6">
              <label for="phone" class="form-label small fw-medium">No. Telp</label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i class="bi bi-telephone"></i>
                </span>
                <input
                  type="tel"
                  class="form-control"
                  :class="{ 'is-invalid': validationErrors.no_hp }"
                  id="no_hp"
                  v-model="profileForm.no_hp"
                  :disabled="user.no_hp && user.no_hp.length > 0"
                  autocomplete="tel"
                  @input="restrictToNumbers"
                />
                <span v-if="user.no_hp && user.no_hp.length > 0" class="input-group-text bg-secondary">
                  <i class="bi bi-lock-fill text-white"></i>
                </span>
              </div>
              <div v-if="profileForm.errors.phone" class="mt-2 text-sm text-red-600">
                {{ profileForm.errors.phone }}
              </div>
              <div v-if="validationErrors.no_hp" class="invalid-feedback">
                {{ validationErrors.no_hp }}
              </div>
              <small v-if="user.no_hp && user.no_hp.length > 0" class="text-muted">
                Nomor telepon tidak dapat diubah setelah diverifikasi
              </small>
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-medium">Jenis Kelamin</label>
              <div class="d-flex gap-4 mt-2">
                <div class="form-check">
                  <input
                    class="form-check-input"
                    :class="{ 'is-invalid': validationErrors.gender }"
                    type="radio"
                    name="gender"
                    id="male"
                    value="male"
                    v-model="profileForm.gender"
                  />
                  <label class="form-check-label" for="male">Laki-laki</label>
                </div>
                <div class="form-check">
                  <input
                    class="form-check-input"
                    :class="{ 'is-invalid': validationErrors.gender }"
                    type="radio"
                    name="gender"
                    id="female"
                    value="female"
                    v-model="profileForm.gender"
                  />
                  <label class="form-check-label" for="female">Perempuan</label>
                </div>
              </div>
              <div v-if="profileForm.errors.gender" class="mt-2 text-sm text-red-600">
                {{ profileForm.errors.gender }}
              </div>
              <div v-if="validationErrors.gender" class="invalid-feedback d-block">
                {{ validationErrors.gender }}
              </div>
            </div>
          </div>

          <div v-if="mustVerifyEmail && user.email_verified_at === null">
            <p class="mt-2 text-sm text-gray-800">
              Your email address is unverified.
              <Link
                :href="route('verification.send')"
                method="post"
                as="button"
                class="text-indigo-600 underline hover:text-indigo-800"
              >
                Click here to re-send the verification email.
              </Link>
            </p>
            <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
              A new verification link has been sent to your email address.
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Password Update Section -->
    <div class="card border-0 shadow-sm">
      <div class="card-header bg-white py-3 border-0">
        <h5 class="card-title mb-0 d-flex align-items-center">
          <i class="bi bi-shield-lock me-2 text-primary"></i>
          Keamanan Akun
        </h5>
      </div>
      <div class="card-body">
        <form class="space-y-6">
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
                  v-model="passwordForm.current_password"
                  :type="showCurrentPassword ? 'text' : 'password'"
                  class="form-control"
                  placeholder="Masukkan password saat ini"
                  autocomplete="current-password"
                />
                <span class="input-group-text bg-light cursor-pointer" @click="showCurrentPassword = !showCurrentPassword">
                  <i :class="showCurrentPassword ? 'bi bi-eye' : 'bi bi-eye-slash'"></i>
                </span>
              </div>
              <div v-if="passwordForm.errors.current_password" class="mt-2 text-sm text-red-600">
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
                  v-model="passwordForm.password"
                  :type="showNewPassword ? 'text' : 'password'"
                  class="form-control"
                  :class="{ 'is-invalid': !isPasswordValid && passwordForm.password.length > 0 }"
                  placeholder="Masukkan password baru"
                  autocomplete="new-password"
                  @input="validatePassword"
                />
                <span class="input-group-text bg-light cursor-pointer" @click="showNewPassword = !showNewPassword">
                  <i :class="showNewPassword ? 'bi bi-eye' : 'bi bi-eye-slash'"></i>
                </span>
              </div>
              <div v-if="passwordForm.errors.password" class="mt-2 text-sm text-red-600">
                {{ passwordForm.errors.password }}
              </div>
              <div v-if="!isPasswordValid && passwordForm.password.length > 0" class="mt-2 text-sm text-red-600">
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
                  v-model="passwordForm.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  class="form-control"
                  :class="{ 'is-invalid': !isPasswordMatch && passwordForm.password_confirmation.length > 0 }"
                  placeholder="Konfirmasi password baru"
                  autocomplete="new-password"
                  @input="validatePasswordMatch"
                />
                <span class="input-group-text bg-light cursor-pointer" @click="showConfirmPassword = !showConfirmPassword">
                  <i :class="showConfirmPassword ? 'bi bi-eye' : 'bi bi-eye-slash'"></i>
                </span>
              </div>
              <div v-if="passwordForm.errors.password_confirmation" class="mt-2 text-sm text-red-600">
                {{ passwordForm.errors.password_confirmation }}
              </div>
              <div v-if="!isPasswordMatch && passwordForm.password_confirmation.length > 0" class="mt-2 text-sm text-red-600">
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
                    <li :class="{ 'text-success': hasMinLength, 'text-danger': !hasMinLength && passwordForm.password.length > 0 }">
                      <i :class="hasMinLength ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1'"></i>
                      Minimal 8 karakter
                    </li>
                    <li :class="{ 'text-success': hasUpperLower, 'text-danger': !hasUpperLower && passwordForm.password.length > 0 }">
                      <i :class="hasUpperLower ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1'"></i>
                      Harus mengandung huruf kecil dan huruf besar
                    </li>
                    <li :class="{ 'text-success': hasNumber, 'text-danger': !hasNumber && passwordForm.password.length > 0 }">
                      <i :class="hasNumber ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1'"></i>
                      Harus mengandung setidaknya satu angka
                    </li>
                    <li :class="{ 'text-success': hasSymbol, 'text-danger': !hasSymbol && passwordForm.password.length > 0 }">
                      <i :class="hasSymbol ? 'bi bi-check-circle-fill me-1' : 'bi bi-x-circle-fill me-1'"></i>
                      Harus mengandung setidaknya satu simbol
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          <div class="d-flex justify-content-end mt-4 gap-2">
            <button type="button" class="btn btn-outline-secondary" @click="resetForms">
              <i class="bi bi-x-lg me-1"></i>
              Batal
            </button>
            <button
              type="button"
              class="btn btn-primary px-4"
              :disabled="profileForm.processing || passwordForm.processing || (!hasProfileFormChanged && !hasPasswordFormChanged)"
              @click="validateAndShowModal"
            >
              <span v-if="profileForm.processing || passwordForm.processing" class="spinner-border spinner-border-sm me-2" role="status"></span>
              <i v-else class="bi bi-save me-2"></i>
              {{ profileForm.processing || passwordForm.processing ? 'Memproses...' : 'Simpan Perubahan' }}
            </button>
            <transition
              enter-active-class="transition ease-in-out"
              enter-from-class="opacity-0"
              leave-active-class="transition ease-in-out"
              leave-to-class="opacity-0"
            >
              <p v-if="profileForm.recentlySuccessful" class="text-sm text-gray-600 ms-2">
                Saved.
              </p>
            </transition>
          </div>
        </form>
      </div>
    </div>

    <!-- Confirmation Modal -->
    <div
      class="modal fade"
      :class="{ show: showConfirmationModal }"
      :style="{ display: showConfirmationModal ? 'block' : 'none' }"
      tabindex="-1"
      role="dialog"
      v-if="showConfirmationModal"
    >
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title d-flex align-items-center">
              <i class="bi bi-question-circle-fill me-2 text-warning"></i>
              Konfirmasi Perubahan
            </h5>
            <button
              type="button"
              class="btn-close"
              @click="closeConfirmationModal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <p class="mb-3">Apakah Anda yakin ingin menyimpan perubahan berikut?</p>
            <div class="bg-light p-3 rounded">
              <h6 class="mb-2 fw-semibold">Data yang akan disimpan:</h6>
              <ul class="mb-0 list-unstyled">
                <template v-if="hasProfileFormChanged">
                  <li><strong>Nama:</strong> {{ profileForm.name }}</li>
                  <li><strong>Email:</strong> {{ profileForm.email }}</li>
                  <li v-if="profileForm.no_hp"><strong>No. Telp:</strong> {{ profileForm.no_hp }}</li>
                  <li><strong>Jenis Kelamin:</strong> {{ profileForm.gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</li>
                </template>
                <template v-if="hasPasswordFormChanged">
                  <li><strong>Password:</strong> (Akan diperbarui)</li>
                </template>
                <li v-if="!hasProfileFormChanged && !hasPasswordFormChanged">Tidak ada perubahan.</li>
              </ul>
            </div>
          </div>
          <div class="modal-footer border-0 pt-0">
            <button
              type="button"
              class="btn btn-secondary"
              @click="closeConfirmationModal"
              :disabled="profileForm.processing || passwordForm.processing"
            >
              <i class="bi bi-x-circle me-2"></i>
              Batal
            </button>
            <button
              type="button"
              class="btn btn-primary"
              @click="submitForms"
              :disabled="profileForm.processing || passwordForm.processing"
            >
              <span v-if="profileForm.processing || passwordForm.processing">
                <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                Menyimpan...
              </span>
              <span v-else>
                <i class="bi bi-check-circle me-2"></i>
                Ya, Simpan
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Password Success Modal -->
    <div class="modal fade" id="passwordSuccessModal" tabindex="-1" aria-labelledby="passwordSuccessModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header border-0 pb-0">
            <h5 class="modal-title text-success" id="passwordSuccessModalLabel">
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

    <!-- Modal Backdrop -->
    <div
      v-if="showConfirmationModal"
      class="modal-backdrop fade show"
      @click="closeConfirmationModal"
    ></div>
  </div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, reactive, computed, nextTick } from 'vue';

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const user = usePage().props.auth.user;

// Profile Form
const showConfirmationModal = ref(false);
const validationErrors = reactive({});

const profileForm = useForm({
  name: user.name || '',
  email: user.email || '',
  no_hp: user.no_hp || '',
  gender: user.gender || 'male',
});

// Password Form
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

// Password validation states
const hasMinLength = ref(false);
const hasUpperLower = ref(false);
const hasNumber = ref(false);
const hasSymbol = ref(false);
const isPasswordMatch = ref(true);

const passwordForm = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const currentPasswordInput = ref(null);
const passwordInput = ref(null);

// Computed properties
const isPasswordValid = computed(() => {
  return hasMinLength.value && hasUpperLower.value && hasNumber.value && hasSymbol.value;
});

const canSubmit = computed(() => {
  return (
    (hasProfileFormChanged.value || hasPasswordFormChanged.value) &&
    (!hasPasswordFormChanged.value ||
      (passwordForm.current_password.length > 0 &&
        passwordForm.password.length > 0 &&
        passwordForm.password_confirmation.length > 0 &&
        isPasswordValid.value &&
        isPasswordMatch.value)) &&
    !profileForm.processing &&
    !passwordForm.processing
  );
});

const hasProfileFormChanged = computed(() => {
  return (
    profileForm.name !== (user.name || '') ||
    profileForm.email !== (user.email || '') ||
    profileForm.no_hp !== (user.no_hp || '') ||
    profileForm.gender !== (user.gender || 'male')
  );
});

const hasPasswordFormChanged = computed(() => {
  return (
    passwordForm.current_password.length > 0 ||
    passwordForm.password.length > 0 ||
    passwordForm.password_confirmation.length > 0
  );
});

// Restrict Phone Number to Numbers Only
const restrictToNumbers = (event) => {
  const input = event.target.value.replace(/[^0-9]/g, '');
  profileForm.no_hp = input;
};

// Profile Form Validation
const validateProfileForm = () => {
  Object.keys(validationErrors).forEach((key) => {
    delete validationErrors[key];
  });

  let isValid = true;

  if (!profileForm.name || profileForm.name.trim().length === 0) {
    validationErrors.name = 'Nama lengkap harus diisi';
    isValid = false;
  }

  if (
    (!user.email || user.email.length === 0) &&
    (!profileForm.email || profileForm.email.trim().length === 0)
  ) {
    validationErrors.email = 'Email harus diisi';
    isValid = false;
  } else if (profileForm.email && profileForm.email.trim().length > 0) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(profileForm.email)) {
      validationErrors.email = 'Format email tidak valid';
      isValid = false;
    }
  }

  if (profileForm.no_hp && profileForm.no_hp.trim().length > 0) {
    const phoneRegex = /^[0-9]+$/;
    if (!phoneRegex.test(profileForm.no_hp)) {
      validationErrors.no_hp = 'Nomor telepon hanya boleh berisi angka';
      isValid = false;
    }
  }

  if (!profileForm.gender) {
    validationErrors.gender = 'Jenis kelamin harus dipilih';
    isValid = false;
  }

  return isValid;
};

// Password Validation
const validatePassword = () => {
  const password = passwordForm.password;

  hasMinLength.value = password.length >= 8;
  hasUpperLower.value = /[a-z]/.test(password) && /[A-Z]/.test(password);
  hasNumber.value = /\d/.test(password);
  hasSymbol.value = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password);

  validatePasswordMatch();
};

const validatePasswordMatch = () => {
  isPasswordMatch.value =
    passwordForm.password === passwordForm.password_confirmation ||
    passwordForm.password_confirmation.length === 0;
};

// Form Reset
const resetForms = () => {
  profileForm.reset();
  profileForm.name = user.name || '';
  profileForm.email = user.email || '';
  profileForm.no_hp = user.no_hp || '';
  profileForm.gender = user.gender || 'male';
  passwordForm.reset();
  hasMinLength.value = false;
  hasUpperLower.value = false;
  hasNumber.value = false;
  hasSymbol.value = false;
  isPasswordMatch.value = true;
};

// Form Validation and Modal
const validateAndShowModal = () => {
  let isValid = true;

  if (hasProfileFormChanged.value) {
    isValid = validateProfileForm();
  }

  if (hasPasswordFormChanged.value && isValid) {
    isValid = isPasswordValid.value && isPasswordMatch.value;
  }

  if (isValid && (hasProfileFormChanged.value || hasPasswordFormChanged.value)) {
    showConfirmationModal.value = true;
    document.body.style.overflow = 'hidden';
  }
};

const closeConfirmationModal = () => {
  showConfirmationModal.value = false;
  document.body.style.overflow = 'auto';
};

const submitForms = () => {
  const promises = [];

  if (hasProfileFormChanged.value) {
    promises.push(
      new Promise((resolve, reject) => {
        profileForm.patch(route('profile.update'), {
          onSuccess: () => resolve('profile'),
          onError: () => reject('profile'),
        });
      })
    );
  }

  if (hasPasswordFormChanged.value && isPasswordValid.value && isPasswordMatch.value) {
    promises.push(
      new Promise((resolve, reject) => {
        passwordForm.put(route('password.update'), {
          preserveScroll: true,
          onSuccess: () => {
            passwordForm.reset();
            resetForms();
            nextTick(() => {
              const modal = new bootstrap.Modal(document.getElementById('passwordSuccessModal'));
              modal.show();
            });
            resolve('password');
          },
          onError: () => {
            if (passwordForm.errors.password) {
              passwordForm.reset('password', 'password_confirmation');
              passwordInput.value?.focus();
            }
            if (passwordForm.errors.current_password) {
              passwordForm.reset('current_password');
              currentPasswordInput.value?.focus();
            }
            reject('password');
          },
        });
      })
    );
  }

  Promise.allSettled(promises).then((results) => {
    closeConfirmationModal();
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
.form-control:disabled {
  background-color: #f8f9fa;
  color: #6c757d;
  cursor: not-allowed;
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
.text-gray-800 {
  color: #2d3748;
}
.text-indigo-600 {
  color: #5a67d8;
}
.text-green-600 {
  color: #059669;
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

/* Modal Styles */
.modal {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1055;
  width: 100%;
  height: 100%;
}
.modal.show {
  display: block !important;
}
.modal-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  z-index: 1050;
  width: 100vw;
  height: 100vh;
  background-color: rgba(0, 0, 0, 0.5);
}
.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
}
.modal-content {
  position: relative;
  background-color: #fff;
  border: 1px solid rgba(0, 0, 0, 0.2);
  border-radius: 0.375rem;
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}
.modal-header {
  padding: 1rem 1rem 0.5rem;
}
.modal-body {
  padding: 0.5rem 1rem;
}
.modal-footer {
  padding: 0.5rem 1rem 1rem;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}
.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  color: #000;
  opacity: 0.5;
  cursor: pointer;
}
.btn-close:hover {
  opacity: 0.75;
}
.is-invalid {
  border-color: #dc3545;
}
.invalid-feedback {
  display: block;
  width: 100%;
  margin-top: 0.25rem;
  font-size: 0.875em;
  color: #dc3545;
}
</style>
```