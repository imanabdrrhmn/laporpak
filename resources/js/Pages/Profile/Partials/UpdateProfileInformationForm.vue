<template>
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3 border-0">
      <h5 class="card-title mb-0 d-flex align-items-center">
        <i class="bi bi-person-fill me-2 text-primary"></i>
        Informasi Pribadi
      </h5>
    </div>
    <div class="card-body">
      <form @submit.prevent="validateAndShowModal" class="space-y-6">
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
                v-model="form.name"
                required
                autocomplete="given-name"
              />
            </div>
            <div v-if="form.errors.name" class="mt-2 text-sm text-red-600">
              {{ form.errors.name }}
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
                v-model="form.email"
                :disabled="user.email && user.email.length > 0"
                :required="!user.email || user.email.length === 0"
                autocomplete="username"
              />
              <span v-if="user.email && user.email.length > 0" class="input-group-text bg-secondary">
                <i class="bi bi-lock-fill text-white"></i>
              </span>
            </div>
            <div v-if="form.errors.email" class="mt-2 text-sm text-red-600">
              {{ form.errors.email }}
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
                v-model="form.no_hp"
                :disabled="user.no_hp && user.no_hp.length > 0"
                autocomplete="tel"
              />
              <span v-if="user.no_hp && user.no_hp.length > 0" class="input-group-text bg-secondary">
                <i class="bi bi-lock-fill text-white"></i>
              </span>
            </div>
            <div v-if="form.errors.phone" class="mt-2 text-sm text-red-600">
              {{ form.errors.phone }}
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
                  v-model="form.gender"
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
                  v-model="form.gender"
                />
                <label class="form-check-label" for="female">Perempuan</label>
              </div>
            </div>
            <div v-if="form.errors.gender" class="mt-2 text-sm text-red-600">
              {{ form.errors.gender }}
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

        <div class="d-flex justify-content-end mt-4">
          <button
            type="submit"
            class="btn btn-primary px-4"
            :disabled="form.processing"
          >
            <i class="bi bi-save me-2"></i>
            Simpan Perubahan
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

  <!-- Modal Konfirmasi -->
  <div 
    class="modal fade" 
    :class="{ show: showModal }" 
    :style="{ display: showModal ? 'block' : 'none' }"
    tabindex="-1" 
    role="dialog"
    v-if="showModal"
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
            @click="closeModal"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p class="mb-3">Apakah Anda yakin ingin menyimpan perubahan data profil?</p>
          <div class="bg-light p-3 rounded">
            <h6 class="mb-2 fw-semibold">Data yang akan disimpan:</h6>
            <ul class="mb-0 list-unstyled">
              <li><strong>Nama:</strong> {{ form.name }}</li>
              <li><strong>Email:</strong> {{ form.email }}</li>
              <li v-if="form.no_hp"><strong>No. Telp:</strong> {{ form.no_hp }}</li>
              <li><strong>Jenis Kelamin:</strong> {{ form.gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</li>
            </ul>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button 
            type="button" 
            class="btn btn-secondary" 
            @click="closeModal"
            :disabled="form.processing"
          >
            <i class="bi bi-x-circle me-2"></i>
            Batal
          </button>
          <button 
            type="button" 
            class="btn btn-primary" 
            @click="submitForm"
            :disabled="form.processing"
          >
            <span v-if="form.processing">
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

  <!-- Modal Backdrop -->
  <div 
    v-if="showModal" 
    class="modal-backdrop fade show"
    @click="closeModal"
  ></div>
</template>

<script setup>
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, reactive } from 'vue';

defineProps({
  mustVerifyEmail: {
    type: Boolean,
  },
  status: {
    type: String,
  },
});

const user = usePage().props.auth.user;
const showModal = ref(false);
const validationErrors = reactive({});

const form = useForm({
  name: user.name || '',
  email: user.email || '',
  no_hp: user.no_hp || '',
  gender: user.gender || 'male',
});

const validateForm = () => {
  // Reset validation errors
  Object.keys(validationErrors).forEach(key => {
    delete validationErrors[key];
  });

  let isValid = true;

  // Validasi nama
  if (!form.name || form.name.trim().length === 0) {
    validationErrors.name = 'Nama lengkap harus diisi';
    isValid = false;
  }

  // Validasi email (hanya jika email kosong dan user belum punya email)
  if ((!user.email || user.email.length === 0) && (!form.email || form.email.trim().length === 0)) {
    validationErrors.email = 'Email harus diisi';
    isValid = false;
  } else if (form.email && form.email.trim().length > 0) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(form.email)) {
      validationErrors.email = 'Format email tidak valid';
      isValid = false;
    }
  }

  // Validasi nomor telepon (format dasar)
  if (form.no_hp && form.no_hp.trim().length > 0) {
    const phoneRegex = /^[0-9+\-\s()]+$/;
    if (!phoneRegex.test(form.no_hp)) {
      validationErrors.no_hp = 'Format nomor telepon tidak valid';
      isValid = false;
    }
  }

  // Validasi jenis kelamin
  if (!form.gender) {
    validationErrors.gender = 'Jenis kelamin harus dipilih';
    isValid = false;
  }

  return isValid;
};

const validateAndShowModal = () => {
  if (validateForm()) {
    showModal.value = true;
    // Disable body scroll when modal is open
    document.body.style.overflow = 'hidden';
  }
};

const closeModal = () => {
  showModal.value = false;
  // Re-enable body scroll when modal is closed
  document.body.style.overflow = 'auto';
};

const submitForm = () => {
  form.patch(route('profile.update'), {
    onSuccess: () => {
      closeModal();
    },
    onError: () => {
      closeModal();
    }
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
.form-control:disabled {
  background-color: #f8f9fa;
  color: #6c757d;
  cursor: not-allowed;
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

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
}
</style>