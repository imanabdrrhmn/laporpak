<template>
  <div class="p-4">
    <button
      @click="confirmUserDeletion"
      class="btn btn-outline-danger w-100"
    >
      <i class="bi bi-trash me-2"></i>
      Hapus Akun
    </button>

    <!-- Modal -->
    <div v-if="confirmingUserDeletion" class="modal fade show d-block" style="background-color: rgba(13, 110, 253, 0.1);" tabindex="-1">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content custom-modal-design border-0 shadow-lg rounded-4">
          <div class="modal-header custom-header border-0">
            <div class="header-icon">
              <i class="bi bi-shield-exclamation text-white"></i>
            </div>
            <div class="header-content">
              <h5 class="modal-title fw-bold text-white mb-1">Konfirmasi Penghapusan Akun</h5>
              <p class="modal-subtitle text-white-50 mb-0 small">Tindakan ini tidak dapat dibatalkan</p>
            </div>
            <button type="button" class="btn-close btn-close-white close-animated" @click="closeModal"></button>
          </div>
          <div class="modal-body custom-body bg-white">
            <div class="warning-card mb-4">
              <div class="warning-icon">
                <i class="bi bi-exclamation-triangle-fill text-warning"></i>
              </div>
              <div class="warning-content">
                <h6 class="fw-bold mb-2 text-dark">Peringatan Penting</h6>
                <p class="text-muted mb-0 small">
                  Setelah akun Anda dihapus, semua sumber daya dan data akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                </p>
              </div>
            </div>
            
            <form @submit.prevent="deleteUser">
              <div class="mb-3">
                <label for="password" class="form-label fw-bold text-dark">
                  <i class="bi bi-key me-2 text-primary"></i>
                  Konfirmasi Kata Sandi
                </label>
                <div class="input-group">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="bi bi-lock text-muted"></i>
                  </span>
                  <input
                    id="password"
                    ref="passwordInput"
                    v-model="form.password"
                    type="password"
                    class="form-control custom-input border-start-0"
                    placeholder="Masukkan kata sandi Anda"
                    @keyup.enter="deleteUser"
                  />
                </div>
                <small v-if="form.errors.password" class="text-danger d-block mt-2">
                  <i class="bi bi-exclamation-circle me-1"></i>
                  {{ form.errors.password }}
                </small>
              </div>
            </form>
          </div>
          <div class="modal-footer custom-footer bg-light border-0">
            <button type="button" class="btn btn-outline-secondary custom-btn-cancel" @click="closeModal">
              <i class="bi bi-x-circle me-2"></i>
              Batal
            </button>
            <button
              type="submit"
              class="btn btn-danger custom-btn-delete"
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

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;
  nextTick(() => passwordInput.value.focus());
};

const deleteUser = () => {
  form.delete(route('profile.destroy'), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => passwordInput.value.focus(),
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;
  form.clearErrors();
  form.reset();
};
</script>

<style scoped>
.btn-outline-danger:hover {
  background-color: #ef476f;
  border-color: #ef476f;
}

/* Custom Modal Design */
.custom-modal-design {
  background: linear-gradient(135deg, #0d6efd 0%, #0056b3 100%);
  overflow: hidden;
}

.custom-header {
  background: transparent;
  padding: 2rem 2rem 1rem;
  display: flex;
  align-items: flex-start;
  gap: 1rem;
}

.header-icon {
  width: 50px;
  height: 50px;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.header-icon i {
  font-size: 1.5rem;
}

.header-content {
  flex: 1;
}

.modal-title {
  font-size: 1.5rem;
  margin: 0;
}

.modal-subtitle {
  font-size: 0.875rem;
  opacity: 0.8;
}

.custom-body {
  padding: 2rem;
  border-radius: 20px 20px 0 0;
  margin-top: -10px;
  position: relative;
}

.warning-card {
  background: #fff8e1;
  border: 1px solid #ffecb3;
  border-radius: 12px;
  padding: 1.25rem;
  display: flex;
  gap: 1rem;
  align-items: flex-start;
}

.warning-icon {
  width: 40px;
  height: 40px;
  background: #fff3cd;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.warning-icon i {
  font-size: 1.25rem;
}

.warning-content h6 {
  color: #856404;
  margin: 0;
}

.custom-input {
  border: 2px solid #e9ecef;
  padding: 0.75rem 1rem;
  border-radius: 0 8px 8px 0;
  transition: all 0.3s ease;
}

.custom-input:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.1);
  background-color: #f8f9ff;
}

.input-group-text {
  border: 2px solid #e9ecef;
  border-radius: 8px 0 0 8px;
  background: #f8f9fa;
}

.custom-footer {
  padding: 1.5rem 2rem;
  border-radius: 0 0 20px 20px;
  gap: 1rem;
  display: flex;
  justify-content: flex-end;
}

.custom-btn-cancel {
  border: 2px solid #6c757d;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.custom-btn-cancel:hover {
  background: #6c757d;
  color: white;
  transform: translateY(-2px);
}

.custom-btn-delete {
  background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
  border: none;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  transition: all 0.3s ease;
}

.custom-btn-delete:hover:not(:disabled) {
  background: linear-gradient(135deg, #c82333 0%, #a02834 100%);
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(220, 53, 69, 0.3);
}

.custom-btn-delete:disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

/* Animation for close button */
.close-animated {
  transition: all 0.3s ease;
  background: rgba(255, 255, 255, 0.2);
  border-radius: 8px;
  padding: 0.5rem;
}

.close-animated:hover {
  transform: rotate(90deg) scale(1.1);
  background: rgba(255, 255, 255, 0.3);
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 2rem);
}

.form-label {
  color: #0d6efd;
  font-size: 0.95rem;
}

.text-primary {
  color: #0d6efd !important;
}

/* Responsive */
@media (max-width: 576px) {
  .custom-header {
    padding: 1.5rem 1.5rem 1rem;
    flex-direction: column;
    text-align: center;
  }
  
  .custom-body {
    padding: 1.5rem;
  }
  
  .custom-footer {
    padding: 1rem 1.5rem;
    flex-direction: column;
  }
  
  .warning-card {
    flex-direction: column;
    text-align: center;
  }
}

/* Additional animations */
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal-content {
  animation: fadeInUp 0.3s ease-out;
}
</style>