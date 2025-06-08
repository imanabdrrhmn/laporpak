<template>
  <div class="modal fade" ref="modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header border-0 pb-2 position-relative">
          <h5 class="modal-title fw-semibold text-dark w-100 text-center" id="deleteModalLabel">
            <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
            Konfirmasi Penghapusan
          </h5>
          <button type="button" class="btn-close-custom position-absolute" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>
        </div>
        <div class="modal-body py-4">
          <p class="text-muted mb-0 fs-6">
            Apakah Anda yakin ingin menghapus feedback ini? Tindakan ini tidak dapat dibatalkan.
          </p>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal">
            Batal
          </button>
          <button type="button" class="btn btn-primary-custom" @click="$emit('confirm-delete')">
            <i class="bi bi-trash3 me-1"></i>
            Hapus
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineEmits(['confirm-delete']);
</script>

<style scoped>
/* Modal styling */
.modal-content {
  border-radius: 12px;
  overflow: hidden;
}

.modal-header {
  background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
  padding: 1.25rem 1.5rem;
}

.modal-title {
  font-size: 1.1rem;
  color: #495057;
}

.modal-body {
  padding: 1.5rem;
  background-color: #fff;
}

.modal-footer {
  padding: 1rem 1.5rem 1.5rem;
  background-color: #fff;
  justify-content: flex-end;
}

/* Custom close button */
.btn-close-custom {
  background: none;
  border: none;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
  font-size: 1rem;
  transition: all 0.3s ease;
  top: 1.25rem;
  right: 1.5rem;
}

.btn-close-custom:hover {
  color: #dc3545;
  transform: rotate(90deg);
  
}

.btn-close-custom:active {
  transform: rotate(90deg) scale(0.95);
}

/* Custom primary button */
.btn-primary-custom {
  background: #dc3545;
  border: none;
  color: white;
  padding: 0.5rem 1.25rem;
  border-radius: 8px;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.3s ease;
  box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
}

.btn-primary-custom:hover {
  background: #fc0000;
  transform: translateY(-1px);
  box-shadow: 0 4px 8px rgba(13, 110, 253, 0.3);
  color: white;
}

.btn-primary-custom:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(13, 110, 253, 0.2);
}

/* Light button styling */
.btn-light {
  background-color: #f8f9fa;
  border: 1px solid #dee2e6;
  color: #6c757d;
  padding: 0.5rem 1.25rem;
  border-radius: 8px;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.3s ease;
}

.btn-light:hover {
  background-color: #e9ecef;
  border-color: #dee2e6;
  color: #495057;
  transform: translateY(-1px);
}

.btn-light:active {
  transform: translateY(0);
}

/* Responsive design */
@media (max-width: 767.98px) {
  .modal-dialog {
    margin: 1rem;
  }
  
  .modal-header {
    padding: 1rem 1.25rem;
  }
  
  .modal-body {
    padding: 1.25rem;
  }
  
  .modal-footer {
    padding: 0.75rem 1.25rem 1.25rem;
    justify-content: center;
    gap: 0.5rem;
  }

  .modal-footer .btn {
    flex: 1;
    min-width: 100px;
  }
  
  .modal-title {
    font-size: 1rem;
  }
}

/* Animation enhancements */
.modal.fade .modal-dialog {
  transition: transform 0.3s ease-out;
  transform: translate(0, -50px);
}

.modal.show .modal-dialog {
  transform: none;
}

/* Icon animations */
.bi {
  transition: transform 0.2s ease;
}

.btn:hover .bi {
  transform: scale(1.1);
}
</style>