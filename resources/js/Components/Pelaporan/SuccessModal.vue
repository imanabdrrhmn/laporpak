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
        <div class="modal-content animate-scale-in rounded-4 border-0 shadow">
          <!-- Modal body -->
          <div class="modal-body text-center position-relative p-5">
            <!-- Tombol close di pojok kanan atas -->
            <button 
              type="button" 
              class="btn-close position-absolute top-0 end-0 m-3 close-button" 
              @click="close" 
              aria-label="Close"
            ></button>

            <!-- Ikon sukses -->
            <div class="success-animation mb-4">
              <div class="success-circle">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="text-white" viewBox="0 0 16 16">
                  <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>
              </div>
            </div>

            <!-- Judul -->
            <h4 class="modal-title fw-bold mb-3 text-gradient">Laporan Berhasil Terkirim!</h4>

            <!-- Deskripsi -->
            <p class="text-muted mb-4">
              Anda dapat memantau status tindak lanjut laporan melalui halaman riwayat
            </p>

            <!-- Tombol -->
            <div class="d-grid gap-3">
              <button 
                @click="navigate"
                class="btn btn-primary py-2 rounded-3 shadow-sm hover-scale"
              >
                <i class="bi bi-clock-history me-2"></i>
                Lihat Riwayat Laporan
              </button>
              <button 
                @click="navigateToFeedback"
                class="btn btn-light py-2 rounded-3 shadow-sm hover-scale"
              >
                <i class="bi bi-star me-2"></i>
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
    default: '/laporan-saya'
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

// Method to navigate to feedback page
const navigateToFeedback = () => {
  router.visit('/feedback');
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
.modal-content {
  background: linear-gradient(145deg, #ffffff, #f8f9fa);
}

.text-gradient {
  background: linear-gradient(45deg, #2563eb, #3b82f6);
  background-clip: text;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

.success-circle {
  width: 80px;
  height: 80px;
  background: linear-gradient(45deg, #2563eb, #3b82f6);
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 auto;
  box-shadow: 0 10px 20px rgba(37, 99, 235, 0.2);
  animation: bounce 1s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes bounce {
  0%, 20%, 50%, 80%, 100% {
    transform: translateY(0);
  }
  40% {
    transform: translateY(-20px);
  }
  60% {
    transform: translateY(-10px);
  }
}

.hover-scale {
  transition: transform 0.2s ease;
}

.hover-scale:hover {
  transform: scale(1.02);
}

@keyframes scale-in {
  0% {
    opacity: 0;
    transform: scale(0.9) translateY(20px);
  }
  100% {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-scale-in {
  animation: scale-in 0.4s ease-out forwards;
}

.modal-dialog {
  max-width: 400px;
}

@media (max-width: 576px) {
  .modal-dialog {
    max-width: 320px;
    margin: 1rem auto;
  }
  
  .modal-body {
    padding: 2rem !important;
  }
}

/* Custom close button styles */
.close-button {
  background: none;
  opacity: 1;
  transition: transform 0.3s ease;
  position: relative;
  width: 24px;
  height: 24px;
}

.close-button::before,
.close-button::after {
  content: '';
  position: absolute;
  width: 2px;
  height: 16px;
  background-color: #000;
  left: 50%;
  top: 50%;
  transition: background-color 0.3s ease;
}

.close-button::before {
  transform: translate(-50%, -50%) rotate(45deg);
}

.close-button::after {
  transform: translate(-50%, -50%) rotate(-45deg);
}

.close-button:hover {
  transform: rotate(90deg);
}

.close-button:hover::before,
.close-button:hover::after {
  background-color: #666;
}
</style>