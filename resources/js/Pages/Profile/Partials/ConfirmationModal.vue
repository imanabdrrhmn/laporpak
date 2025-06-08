<template>
  <div
    class="modal fade"
    :class="{ show: show }"
    :style="{ display: show ? 'block' : 'none' }"
    tabindex="-1"
    role="dialog"
  >
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-question-circle-fill me-2 text-primary"></i>
            Konfirmasi Perubahan
          </h5>
          <button
            type="button"
            class="btn-close"
            @click="$emit('close')"
            aria-label="Close"
          ></button>
        </div>
        <div class="modal-body">
          <p class="mb-3 text-muted">Apakah Anda yakin ingin menyimpan perubahan berikut?</p>
          <div class="data-summary">
            <h6 class="summary-title">Data yang akan disimpan:</h6>
            <ul class="data-list">
              <template v-if="hasProfileFormChanged">
                <li class="data-item">
                  <span class="data-label">Nama:</span> 
                  <span class="data-value">{{ profileForm.name }}</span>
                </li>
                <li class="data-item">
                  <span class="data-label">Email:</span> 
                  <span class="data-value">{{ profileForm.email }}</span>
                </li>
                <li v-if="profileForm.no_hp" class="data-item">
                  <span class="data-label">No. Telp:</span> 
                  <span class="data-value">{{ profileForm.no_hp }}</span>
                </li>
                <li class="data-item">
                  <span class="data-label">Jenis Kelamin:</span> 
                  <span class="data-value">{{ profileForm.gender === 'male' ? 'Laki-laki' : 'Perempuan' }}</span>
                </li>
              </template>
              <template v-if="hasPasswordFormChanged">
                <li class="data-item">
                  <span class="data-label">Password:</span> 
                  <span class="data-value text-success">
                    <i class="bi bi-shield-check me-1"></i>
                    Akan diperbarui
                  </span>
                </li>
              </template>
              <li v-if="!hasProfileFormChanged && !hasPasswordFormChanged" class="data-item no-changes">
                <i class="bi bi-info-circle me-2"></i>
                Tidak ada perubahan.
              </li>
            </ul>
          </div>
        </div>
        <div class="modal-footer">
          <button
            type="button"
            class="btn btn-outline-secondary"
            @click="$emit('close')"
            :disabled="profileForm.processing || passwordForm.processing"
          >
            <i class="bi bi-x-circle me-2"></i>
            Batal
          </button>
          <button
            type="button"
            class="btn btn-primary"
            @click="$emit('submit')"
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
  <div
    v-if="show"
    class="modal-backdrop fade show"
    @click="$emit('close')"
  ></div>
</template>

<script setup>
defineProps({
  show: {
    type: Boolean,
    required: true,
  },
  profileForm: {
    type: Object,
    required: true,
  },
  passwordForm: {
    type: Object,
    required: true,
  },
  hasProfileFormChanged: {
    type: Boolean, // Changed from Object to Boolean
    required: true,
  },
  hasPasswordFormChanged: {
    type: Boolean, // Changed from Object to Boolean
    required: true,
  },
  isPasswordValid: {
    type: Boolean, // Changed from Object to Boolean
    required: true,
  },
  isPasswordMatch: {
    type: Boolean, // Changed from Object to Boolean
    required: true,
  },
});

defineEmits(['close', 'submit']);
</script>

<style scoped>
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
  background-color: rgba(13, 110, 253, 0.1);
  backdrop-filter: blur(2px);
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 1rem);
}

.modal-content {
  position: relative;
  background-color: #ffffff;
  border: 2px solid rgba(13, 110, 253, 0.1);
  border-radius: 12px;
  box-shadow: 
    0 10px 25px rgba(13, 110, 253, 0.15),
    0 5px 15px rgba(0, 0, 0, 0.05);
  overflow: hidden;
}

.modal-header {
  padding: 1.5rem 1.5rem 1rem;
  background: linear-gradient(135deg, rgba(13, 110, 253, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
  border-bottom: 1px solid rgba(13, 110, 253, 0.1);
}

.modal-title {
  color: #0d6efd;
  font-weight: 600;
  font-size: 1.25rem;
  margin: 0;
  display: flex;
  align-items: center;
}

.modal-body {
  padding: 1.5rem;
  background-color: #ffffff;
}

.modal-body p {
  color: #6c757d;
  margin-bottom: 1.5rem;
}

.data-summary {
  background: linear-gradient(135deg, rgba(13, 110, 253, 0.03) 0%, rgba(248, 249, 250, 1) 100%);
  border: 1px solid rgba(13, 110, 253, 0.1);
  border-radius: 8px;
  padding: 1.25rem;
}

.summary-title {
  color: #0d6efd;
  font-weight: 600;
  margin-bottom: 1rem;
  font-size: 1rem;
}

.data-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.data-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(13, 110, 253, 0.05);
}

.data-item:last-child {
  border-bottom: none;
}

.data-label {
  font-weight: 500;
  color: #495057;
  min-width: 120px;
}

.data-value {
  color: #212529;
  font-weight: 400;
  text-align: right;
  flex: 1;
}

.no-changes {
  justify-content: center;
  color: #6c757d;
  font-style: italic;
}

.modal-footer {
  padding: 1rem 1.5rem 1.5rem;
  background-color: #ffffff;
  border-top: 1px solid rgba(13, 110, 253, 0.1);
  display: flex;
  justify-content: flex-end;
  gap: 0.75rem;
}

.btn {
  border-radius: 8px;
  font-weight: 500;
  padding: 0.625rem 1.25rem;
  transition: all 0.3s ease;
  border: 2px solid;
}

.btn-close {
  background: none;
  border: none;
  font-size: 1.5rem;
  font-weight: 700;
  line-height: 1;
  color: #6c757d;
  opacity: 0.6;
  cursor: pointer;
  transition: all 0.2s ease;
  width: 32px;
  height: 32px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 6px;
}

.btn-close:hover {
  opacity: 1;
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
}

.btn-primary {
  background-color: #0d6efd;
  border-color: #0d6efd;
  color: white;
  box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
}

.btn-primary:hover:not(:disabled) {
  background-color: #0b5ed7;
  border-color: #0b5ed7;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
}

.btn-primary:disabled {
  background-color: #6c757d;
  border-color: #6c757d;
  opacity: 0.6;
}

.btn-outline-secondary {
  background-color: transparent;
  border-color: #dee2e6;
  color: #6c757d;
}

.btn-outline-secondary:hover:not(:disabled) {
  background-color: #f8f9fa;
  border-color: #0d6efd;
  color: #0d6efd;
  transform: translateY(-1px);
}

.text-primary {
  color: #0d6efd !important;
}

.text-muted {
  color: #6c757d !important;
}

.text-success {
  color: #198754 !important;
}

.spinner-border-sm {
  width: 1rem;
  height: 1rem;
  border-width: 0.15em;
}

/* Animation untuk modal */
.modal.fade .modal-dialog {
  transition: transform 0.3s ease-out;
  transform: translate(0, -50px);
}

.modal.show .modal-dialog {
  transform: none;
}

.modal-backdrop.fade {
  opacity: 0;
}

.modal-backdrop.show {
  opacity: 1;
}
</style>