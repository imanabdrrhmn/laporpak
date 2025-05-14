<template>
  <div>
    <!-- Backdrop overlay for the modal -->
    <div 
      v-if="show" 
      class="modal fade show d-block" 
      style="background-color: rgba(0, 0, 0, 0.5);"
      @click.self="closeOnBackdrop ? close() : null"
      tabindex="-1"
      role="dialog"
    >
      <!-- Modal container -->
      <div 
        class="modal-dialog modal-dialog-centered"
        role="document"
      >
        <div class="modal-content animate-scale-in">
          <!-- Modal body -->
          <div class="modal-body text-center position-relative p-4">
            <!-- Tombol close di pojok kanan atas -->
            <button 
              type="button" 
              class="btn-close position-absolute top-0 end-0 m-2" 
              @click="close" 
              aria-label="Close"
            ></button>

            <!-- Ikon sukses -->
            <div class="success-animation mb-3">
              <div class="blue-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="text-primary" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
              </div>
            </div>

            <!-- Judul -->
            <h5 class="modal-title fw-bold mb-2">Laporan Anda Berhasil!!</h5>

            <!-- Deskripsi -->
            <p class="text-muted mb-4 small">
              Lihat detail laporan dan status tindak lanjutnya di halaman berikut
            </p>

            <!-- Tombol -->
            <div class="d-flex flex-column gap-2">
              <button 
                @click="navigate"
                class="btn btn-primary btn-sm"
              >
                Lihat Riwayat Laporan
              </button>
              <button 
                @click="close"
                class="btn btn-outline-primary btn-sm"
              >
                Berikan Ulasan
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, watch } from 'vue';
import { router } from '@inertiajs/vue3';

const props = defineProps({
  show: {
    type: Boolean,
    default: false
  },
  redirectPath: {
    type: String,
    default: '/kelola-laporan'
  },
  closeOnBackdrop: {
    type: Boolean,
    default: true
  }
});

const emit = defineEmits(['close']);

// Method to close modal
const close = () => {
  emit('close');
};

// Method to navigate to the specified path
const navigate = () => {
  router.visit(props.redirectPath);
};

// Watch for show property changes to handle escape key
watch(() => props.show, (value) => {
  if (value) {
    document.addEventListener('keydown', handleEscKey);
    document.body.classList.add('modal-open'); // Tambahkan kelas untuk Bootstrap
  } else {
    document.removeEventListener('keydown', handleEscKey);
    document.body.classList.remove('modal-open'); // Hapus kelas saat modal ditutup
  }
});

// Handle escape key press
const handleEscKey = (event) => {
  if (event.key === 'Escape') {
    close();
  }
};
</script>

<style scoped>
/* Animation for modal entry */
@keyframes scale-in {
  0% {
    opacity: 0;
    transform: scale(0.8);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-scale-in {
  animation: scale-in 0.3s ease-out forwards;
}

/* Blue circle styles */
.blue-circle {
  width: 80px;
  height: 80px;
  margin: 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  animation: pulse 1.5s ease-in-out;
}

@keyframes pulse {
  0% {
    transform: scale(0.8);
    opacity: 0;
  }
  50% {
    transform: scale(1.2);
    opacity: 1;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}

/* Mengatur lebar modal menjadi lebih kecil */
.modal-dialog {
  max-width: 350px; /* Lebar modal lebih kecil */
}

/* Responsivitas untuk layar kecil */
@media (max-width: 576px) {
  .modal-dialog {
    max-width: 300px;
    margin: 1.75rem auto;
  }
}
</style>