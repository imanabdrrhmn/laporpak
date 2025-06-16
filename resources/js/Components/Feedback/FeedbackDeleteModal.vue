<template>
  <div class="modal fade" ref="modal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header border-0 pb-2 position-relative">
          <h5 class="modal-title fw-semibold text-dark w-100 text-center" id="deleteModalLabel">
            <i class="bi bi-exclamation-triangle-fill text-primary me-2"></i>
            Konfirmasi Penghapusan
          </h5>
          <button 
            type="button" 
            class="btn-close-custom position-absolute" 
            data-bs-dismiss="modal" 
            aria-label="Close"
          >
            <i class="bi bi-x-lg" aria-hidden="true"></i>
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
/* Modal styling with blue theme */
.modal-content {
  border-radius: 16px;
  overflow: hidden;

}

.modal-header {
  background: linear-gradient(135deg, #f8faff 0%, #e3f2fd 100%);
  padding: 1.5rem 2rem;
  border-bottom: 1px solid rgba(13, 110, 253, 0.1);
}

.modal-title {
  font-size: 1.2rem;
  color: #0d6efd;
  font-weight: 600;
}

.text-primary {
  color: #0d6efd !important;
}

.modal-body {
  padding: 2rem;
  background-color: #fff;
}

.modal-footer {
  padding: 1rem 2rem 2rem;
  background-color: #fff;
  justify-content: flex-end;
  gap: 0.75rem;
}

/* Custom close button with blue theme */
.btn-close-custom {
  background: none;
  border: none;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
  font-size: 1.1rem;
  transition: all 0.3s ease;
  top: 1.5rem;
  right: 2rem;
}

.btn-close-custom:hover {
  color: red;
  transform: rotate(90deg);
}

.btn-close-custom:active {
  transform: rotate(90deg) scale(0.95);
}

/* Custom primary button with red theme */
.btn-primary-custom {
  background: #dc3545;
  border: none;
  color: white;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 600;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  
  min-width: 100px;
}

.btn-primary-custom:hover {
  background: #c82333;
  transform: translateY(-2px);
  
  color: white;
}

.btn-primary-custom:active {
  transform: translateY(0);

}



/* Light button styling with blue accents */
.btn-light {
  background-color: #f8f9fa;
  border: 2px solid #e9ecef;
  color: #6c757d;
  padding: 0.75rem 1.5rem;
  border-radius: 10px;
  font-weight: 500;
  font-size: 0.95rem;
  transition: all 0.3s ease;
  min-width: 100px;
}

.btn-light:hover {
  background-color: rgba(13, 110, 253, 0.05);

  color: #0d6efd;
  transform: translateY(-1px);
}

.btn-light:active {
  transform: translateY(0);
}

.btn-light:focus {
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.15);
}

/* Responsive design */
@media (min-width: 1200px) {
  .modal-dialog {
    max-width: 600px;
  }
}

@media (min-width: 992px) and (max-width: 1199px) {
  .modal-dialog {
    max-width: 550px;
  }
}

@media (min-width: 768px) and (max-width: 991px) {
  .modal-dialog {
    max-width: 500px;
  }
}

@media (min-width: 576px) and (max-width: 767px) {
  .modal-dialog {
    max-width: 450px;
    margin: 1.75rem auto;
  }

  .modal-content {
    border-radius: 14px;
  }

  .modal-header {
    padding: 1.25rem 1.5rem;
  }

  .modal-body {
    padding: 1.5rem;
  }

  .modal-footer {
    padding: 1rem 1.5rem 1.5rem;
  }
}

@media (min-width: 401px) and (max-width: 575px) {
  .modal-dialog {
    margin: 1rem;
    max-width: calc(100% - 2rem);
  }

  .modal-content {
    border-radius: 12px;
  }

  .modal-header {
    padding: 1rem 1.25rem;
  }

  .modal-body {
    padding: 1.25rem;
  }

  .modal-footer {
    padding: 0.75rem 1.25rem 1.25rem;
    flex-direction: column-reverse;
    gap: 0.75rem;
  }

  .btn {
    width: 100%;
    margin: 0 !important;
  }
}

@media (max-width: 400px) {
  .modal-dialog {
    margin: 0.5rem;
    max-width: calc(100% - 1rem);
  }

  .modal-content {
    border-radius: 10px;
  }

  .modal-header {
    padding: 0.875rem 1rem;
  }

  .modal-title {
    font-size: 0.9rem;
    padding-right: 30px;
  }

  .modal-body {
    padding: 1rem;
    font-size: 0.9rem;
  }

  .modal-footer {
    padding: 0.5rem 1rem 1rem;
    flex-direction: column-reverse;
    gap: 0.5rem;
  }

  .btn {
    width: 100%;
    margin: 0 !important;
    padding: 0.625rem 1rem;
    font-size: 0.9rem;
  }

  .btn-close-custom {
    width: 28px;
    height: 28px;
    font-size: 0.9rem;
    top: 0.875rem;
    right: 1rem;
  }
}

/* Handle viewport height for smaller screens */
@media (max-height: 600px) {
  .modal-dialog {
    margin: 0.5rem auto;
  }

  .modal-body {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
  }
}

/* Animation enhancements */
.modal.fade .modal-dialog {
  transition: transform 0.4s ease-out, opacity 0.4s ease-out;
  transform: translate(0, -30px) scale(0.95);
  opacity: 0;
}

.modal.show .modal-dialog {
  transform: translate(0, 0) scale(1);
  opacity: 1;
}

/* Icon animations with blue theme */
.bi {
  transition: all 0.3s ease;
}

.btn:hover .bi {
  transform: scale(1.1);
}

.text-primary .bi {
  filter: drop-shadow(0 2px 4px rgba(13, 110, 253, 0.2));
}

/* Focus improvements for accessibility */
.btn:focus {
  outline: none;
}

.btn-close-custom:focus {
  outline: none;
}

/* Loading state (optional) */
.btn-primary-custom:disabled {
  background: #6c757d;
  cursor: not-allowed;
  transform: none;
  box-shadow: none;
}

/* High contrast mode support */
@media (prefers-contrast: high) {
  .modal-content {
    border: 2px solid #0d6efd;
  }
  
  .btn-primary-custom {
    border: 2px solid #fff;
  }
  
  .btn-light {
    border: 2px solid #0d6efd;
  }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
  .modal.fade .modal-dialog,
  .btn,
  .btn-close-custom,
  .bi {
    transition: none;
  }
}
</style>