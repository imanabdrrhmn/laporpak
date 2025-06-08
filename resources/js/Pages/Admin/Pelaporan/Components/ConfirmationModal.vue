<template>
  <div>
    <!-- Confirmation Modal -->
    <div
      class="modal fade"
      :class="{ show: show }"
      :style="{ display: show ? 'block' : 'none' }"
      tabindex="-1"
      role="dialog"
    >
      <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content shadow-lg">
          <!-- Header with gradient background -->
          <div class="modal-header gradient-header border-0 pb-3">
            <h5 class="modal-title d-flex align-items-center text-white fw-bold">
              <div class="icon-wrapper me-3">
                <i class="bi bi-shield-check fs-4"></i>
              </div>
              <div>
                <div class="title-main">Konfirmasi Perubahan</div>
                <small class="subtitle opacity-90">Pastikan data yang akan disimpan sudah benar</small>
              </div>
            </h5>
            <button
              type="button"
              class="btn-close btn-close-white"
              @click="$emit('close')"
              aria-label="Close"
            ></button>
          </div>

          <!-- Body with responsive card -->
          <div class="modal-body p-4">
            <div class="confirmation-message mb-4">
              <div class="d-flex align-items-center mb-2">
                <i class="bi bi-info-circle-fill text-primary me-2"></i>
                <span class="fw-semibold">Data yang akan disimpan:</span>
              </div>
              <p class="text-muted mb-0 small">Silakan periksa kembali informasi di bawah ini sebelum menyimpan.</p>
            </div>

            <!-- Responsive Data Card -->
            <div class="data-preview-card">
              <div class="card-header">
                <i class="bi bi-clipboard-data me-2"></i>
                <span class="fw-bold">Ringkasan Perubahan</span>
              </div>
              
              <div class="card-body">
                <!-- Profile Changes -->
                <template v-if="hasProfileFormChanged">
                  <div class="section-header">
                    <i class="bi bi-person-circle text-primary"></i>
                    <span>Informasi Profil</span>
                  </div>
                  <div class="data-grid">
                    <div class="data-item">
                      <div class="data-label">
                        <i class="bi bi-person-badge me-1"></i>
                        Nama
                      </div>
                      <div class="data-value" :title="profileForm.name">
                        {{ profileForm.name }}
                      </div>
                    </div>
                    
                    <div class="data-item">
                      <div class="data-label">
                        <i class="bi bi-envelope me-1"></i>
                        Email
                      </div>
                      <div class="data-value" :title="profileForm.email">
                        {{ profileForm.email }}
                      </div>
                    </div>
                    
                    <div class="data-item" v-if="profileForm.no_hp">
                      <div class="data-label">
                        <i class="bi bi-telephone me-1"></i>
                        No. Telp
                      </div>
                      <div class="data-value" :title="profileForm.no_hp">
                        {{ profileForm.no_hp }}
                      </div>
                    </div>
                    
                    <div class="data-item">
                      <div class="data-label">
                        <i class="bi bi-gender-ambiguous me-1"></i>
                        Jenis Kelamin
                      </div>
                      <div class="data-value">
                        <span class="badge" :class="profileForm.gender === 'male' ? 'badge-male' : 'badge-female'">
                          {{ profileForm.gender === 'male' ? 'Laki-laki' : 'Perempuan' }}
                        </span>
                      </div>
                    </div>
                  </div>
                </template>

                <!-- Password Changes -->
                <template v-if="hasPasswordFormChanged">
                  <div class="section-divider" v-if="hasProfileFormChanged"></div>
                  <div class="section-header">
                    <i class="bi bi-shield-lock text-warning"></i>
                    <span>Keamanan</span>
                  </div>
                  <div class="data-grid">
                    <div class="data-item">
                      <div class="data-label">
                        <i class="bi bi-key me-1"></i>
                        Password
                      </div>
                      <div class="data-value">
                        <span class="badge badge-warning">
                          <i class="bi bi-arrow-clockwise me-1"></i>
                          Akan diperbarui
                        </span>
                      </div>
                    </div>
                  </div>
                </template>

                <!-- No Changes -->
                <div v-if="!hasProfileFormChanged && !hasPasswordFormChanged" class="no-changes">
                  <div class="text-center py-4">
                    <i class="bi bi-info-circle text-muted fs-1 mb-3"></i>
                    <h6 class="text-muted">Tidak ada perubahan</h6>
                    <p class="text-muted small mb-0">Belum ada data yang diubah untuk disimpan.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Footer with enhanced buttons -->
          <div class="modal-footer border-0 pt-0 px-4 pb-4">
            <div class="d-flex gap-2 w-100 flex-column flex-sm-row justify-content-end">
              <button
                type="button"
                class="btn btn-outline-secondary btn-lg"
                @click="$emit('close')"
                :disabled="profileForm.processing || passwordForm.processing"
              >
                <i class="bi bi-x-circle me-2"></i>
                Batal
              </button>
              <button
                type="button"
                class="btn btn-primary btn-lg"
                @click="$emit('submit')"
                :disabled="profileForm.processing || passwordForm.processing || (!hasProfileFormChanged && !hasPasswordFormChanged)"
              >
                <span v-if="profileForm.processing || passwordForm.processing">
                  <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                  Menyimpan...
                </span>
                <span v-else>
                  <i class="bi bi-check-circle me-2"></i>
                  Ya, Simpan Perubahan
                </span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Enhanced Modal Backdrop -->
    <div
      v-if="show"
      class="modal-backdrop fade show"
      @click="$emit('close')"
    ></div>
  </div>
</template>

<script setup>
defineProps({
  show: Boolean,
  profileForm: Object,
  passwordForm: Object,
  hasProfileFormChanged: Object,
  hasPasswordFormChanged: Object,
  isPasswordValid: Object,
  isPasswordMatch: Object,
});

defineEmits(['close', 'submit']);
</script>

<style scoped>
/* Modal Base Styles */
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
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(2px);
}

.modal-dialog-centered {
  display: flex;
  align-items: center;
  min-height: calc(100% - 2rem);
  margin: 1rem;
}

.modal-dialog-scrollable {
  max-height: calc(100vh - 2rem);
}

.modal-dialog-scrollable .modal-content {
  max-height: 100%;
  overflow: hidden;
}

.modal-dialog-scrollable .modal-body {
  overflow-y: auto;
  max-height: 60vh;
}

/* Enhanced Modal Content */
.modal-content {
  position: relative;
  background: #ffffff;
  border: none;
  border-radius: 16px;
  box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
  overflow: hidden;
  width: 100%;
  max-width: 600px;
  margin: 0 auto;
}

/* Blue Header */
.gradient-header {
  background: #0d6efd;
  padding: 1.5rem;
  position: relative;
}

.icon-wrapper {
  background: rgba(255, 255, 255, 0.2);
  border-radius: 12px;
  padding: 0.75rem;
  backdrop-filter: blur(10px);
}

.title-main {
  font-size: 1.25rem;
  line-height: 1.2;
}

.subtitle {
  font-size: 0.875rem;
  line-height: 1.2;
}

/* Data Preview Card */
.data-preview-card {
  background: #ffffff;
  border: 1px solid #e5e7eb;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
}

.card-header {
  background: #f8f9fa;
  padding: 1rem 1.25rem;
  border-bottom: 1px solid #e5e7eb;
  font-weight: 600;
  color: #374151;
  display: flex;
  align-items: center;
}

.card-body {
  padding: 1.25rem;
}

/* Section Headers */
.section-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  margin-bottom: 1rem;
  padding-bottom: 0.5rem;
  border-bottom: 2px solid #f3f4f6;
  font-weight: 600;
  color: #374151;
}

.section-divider {
  height: 1px;
  background: linear-gradient(90deg, transparent 0%, #e5e7eb 50%, transparent 100%);
  margin: 1.5rem 0;
}

/* Responsive Data Grid */
.data-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1rem;
}

@media (min-width: 576px) {
  .data-grid {
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  }
}

.data-item {
  background: #f9fafb;
  border: 1px solid #f3f4f6;
  border-radius: 8px;
  padding: 1rem;
  transition: all 0.2s ease;
}

.data-item:hover {
  background: #f3f4f6;
  border-color: #d1d5db;
  transform: translateY(-1px);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.data-label {
  font-size: 0.875rem;
  font-weight: 600;
  color: #6b7280;
  margin-bottom: 0.5rem;
  display: flex;
  align-items: center;
}

.data-value {
  font-weight: 500;
  color: #111827;
  word-break: break-word;
  overflow-wrap: break-word;
  line-height: 1.4;
}

/* Custom Badges */
.badge {
  font-size: 0.75rem;
  padding: 0.375rem 0.75rem;
  border-radius: 6px;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
}

.badge-male {
  background: #0d6efd;
  color: white;
}

.badge-female {
  background: #dc3545;
  color: white;
}

.badge-warning {
  background: #fd7e14;
  color: white;
}

/* No Changes State */
.no-changes {
  background: #f9fafb;
  border: 2px dashed #d1d5db;
  border-radius: 12px;
  margin: 1rem 0;
}

/* Enhanced Buttons */
.btn {
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
  display: inline-flex;
  align-items: center;
  justify-content: center;
}

.btn-lg {
  padding: 0.75rem 1.5rem;
  font-size: 1rem;
}

.btn-primary {
  background: linear-gradient(135deg, #4361ee, #3a56d4);
  border: none;
  color: white;
}

.btn-primary:hover:not(:disabled) {
  background: linear-gradient(135deg, #3a56d4, #2d46c7);
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(67, 97, 238, 0.4);
}

.btn-primary:disabled {
  background: #9ca3af;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

.btn-outline-secondary {
  border: 2px solid #d1d5db;
  color: #6b7280;
  background: white;
}

.btn-outline-secondary:hover:not(:disabled) {
  background: #f9fafb;
  border-color: #9ca3af;
  color: #374151;
  transform: translateY(-1px);
}

.btn-close-white {
  filter: invert(1) grayscale(100%) brightness(200%);
}

/* Responsive Adjustments */
@media (max-width: 575.98px) {
  .modal-dialog {
    margin: 0.5rem;
  }
  
  .modal-content {
    border-radius: 12px;
  }
  
  .gradient-header {
    padding: 1rem;
  }
  
  .title-main {
    font-size: 1.1rem;
  }
  
  .subtitle {
    font-size: 0.8rem;
  }
  
  .modal-body {
    padding: 1rem;
    max-height: 50vh;
  }
  
  .card-body {
    padding: 1rem;
  }
  
  .data-item {
    padding: 0.75rem;
  }
  
  .modal-footer .d-flex {
    gap: 0.75rem;
  }
  
  .btn-lg {
    padding: 0.625rem 1.25rem;
    font-size: 0.9rem;
  }
}

@media (max-width: 359.98px) {
  .modal-dialog {
    margin: 0.25rem;
  }
  
  .gradient-header {
    padding: 0.75rem;
  }
  
  .modal-body {
    padding: 0.75rem;
  }
}

/* Animation for modal show/hide */
.modal.fade .modal-dialog {
  transition: transform 0.3s ease-out;
  transform: translate(0, -50px);
}

.modal.show .modal-dialog {
  transform: none;
}

/* Scrollbar Styling */
.modal-body::-webkit-scrollbar {
  width: 6px;
}

.modal-body::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 3px;
}

.modal-body::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}
</style>