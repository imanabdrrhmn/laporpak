<template>
  <div class="p-4">
    <button
      @click="confirmUserDeletion"
      class="btn btn-outline-danger w-100"
    >
      <i class="bi bi-trash me-2"></i>
      Hapus Akun
    </button>

    <!-- Simple Modal -->
    <div v-if="confirmingUserDeletion" class="modal fade show d-block" style="background-color: rgba(0, 0, 0, 0.5);" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header bg-primary text-white">
            <h5 class="modal-title">
              <i class="bi bi-exclamation-triangle me-2"></i>
              Konfirmasi Penghapusan Akun
            </h5>
            <button type="button" class="btn-close btn-close-white rotate-on-hover" @click="closeModal"></button>
          </div>
          
          <div class="modal-body">
            <div class="alert alert-warning">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <strong>Peringatan:</strong> Setelah akun dihapus, semua data akan hilang permanen dan tidak dapat dikembalikan.
            </div>

            <!-- Alert untuk password salah -->
            <div v-if="showPasswordError" class="alert alert-danger alert-dismissible fade show" role="alert">
              <i class="bi bi-exclamation-triangle-fill me-2"></i>
              <strong>Kata sandi salah!</strong> Silakan periksa kembali kata sandi Anda.
              <button type="button" class="btn-close" @click="showPasswordError = false"></button>
            </div>
            
            <form @submit.prevent="deleteUser">
              <div class="mb-3">
                <label for="password" class="form-label fw-bold">
                  Konfirmasi Kata Sandi
                </label>
                <div class="input-group">
                  <span class="input-group-text">
                    <i class="bi bi-lock"></i>
                  </span>
                  <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    :type="showPassword ? 'text' : 'password'"
                    class="form-control"
                    placeholder="Masukkan kata sandi Anda"
                    @keyup.enter="deleteUser"
                  />
                  <button
                    type="button"
                    class="btn btn-outline-secondary"
                    @click="togglePasswordVisibility"
                  >
                    <i :class="showPassword ? 'bi bi-eye' : 'bi bi-eye-slash'"></i>
                  </button>
                </div>
              </div>
            </form>
          </div>
          
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="closeModal">
              Batal
            </button>
            <button
              type="submit"
              class="btn btn-danger"
              :disabled="form.processing"
              @click="deleteUser"
            >
              <i class="bi bi-trash me-2"></i>
              <span v-if="form.processing">Menghapus...</span>
              <span v-else>Hapus Akun</span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3';
import { ref, nextTick } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);
const showPassword = ref(false);
const showPasswordError = ref(false);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
  nextTick(() => passwordInput.value.focus());
};

const togglePasswordVisibility = () => {
  showPassword.value = !showPassword.value;
};

const deleteUser = () => {
  // Reset error alert sebelum submit
  showPasswordError.value = false;
  
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: (errors) => {
      // Tampilkan alert jika ada error password
      if (errors.password || errors.current_password) {
        showPasswordError.value = true;
      }
      passwordInput.value.focus();
    },
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;
  showPasswordError.value = false;
  showPassword.value = false;
  form.clearErrors();
  form.reset();
};
</script>

<style scoped>
/* Primary color using #0d6efd */
.bg-primary {
  background-color: #0d6efd !important;
}

.btn-outline-danger:hover {
  background-color: #dc3545;
  border-color: #dc3545;
}

/* Modal animations */
.modal-content {
  animation: fadeInUp 0.3s ease-out;
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(-30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Focus states */
.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.btn:focus {
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Rotate animation for close button */
.rotate-on-hover {
  transition: transform 0.3s ease;
}

.rotate-on-hover:hover {
  transform: rotate(90deg);
}
</style>