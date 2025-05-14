<template>
  <div>
    <!-- Backdrop overlay for the modal -->
    <div 
      v-if="show" 
      class="modal-backdrop fixed inset-0 bg-black bg-opacity-50 z-40 flex items-center justify-center"
      @click.self="closeOnBackdrop ? close() : null"
    >
      <!-- Modal container -->
      <div 
        class="modal-container bg-white rounded-lg shadow-xl w-11/12 max-w-md mx-auto overflow-hidden"
        :class="{'animate-scale-in': show}"
      >
        <!-- Modal header -->
        <div class="modal-header p-4 bg-primary text-white text-center relative">
          <h4 class="text-xl font-bold mb-0">
            <i class="bi bi-check-circle-fill me-2"></i>{{ title }}
          </h4>
          <button 
            @click="close"
            class="absolute top-3 right-3 text-white hover:text-gray-200 transition-colors"
            aria-label="Close modal"
          >
            <i class="bi bi-x-lg"></i>
          </button>
        </div>

        <!-- Modal body -->
        <div class="modal-body p-5 text-center">
          <!-- Success icon animation -->
          <div class="success-animation mb-4">
            <div class="checkmark-circle">
              <div class="checkmark-check"></div>
            </div>
          </div>
          
          <!-- Success message -->
          <p class="text-lg mb-4">{{ message }}</p>
          
          <!-- Optional details -->
          <p v-if="details" class="text-sm text-gray-600 mb-4">{{ details }}</p>
        </div>

        <!-- Modal footer -->
        <div class="modal-footer p-4 bg-gray-50 flex flex-col sm:flex-row justify-center gap-3">
          <button 
            @click="close"
            class="btn btn-outline-secondary w-full sm:w-auto"
          >
            <i class="bi bi-x me-1"></i>Tutup
          </button>
          <button 
            @click="navigate"
            class="btn btn-primary w-full sm:w-auto"
          >
            <i class="bi bi-file-earmark-text me-1"></i>{{ actionButtonText }}
          </button>
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
  title: {
    type: String,
    default: 'Berhasil!'
  },
  message: {
    type: String,
    default: 'Laporan Anda telah berhasil dikirim.'
  },
  details: {
    type: String,
    default: 'Kami akan memproses laporan Anda sesegera mungkin. Terima kasih atas partisipasi Anda.'
  },
  actionButtonText: {
    type: String,
    default: 'Kelola Laporan'
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
    // Add event listener when modal is shown
    document.addEventListener('keydown', handleEscKey);
  } else {
    // Remove event listener when modal is hidden
    document.removeEventListener('keydown', handleEscKey);
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

/* Success animation */
.checkmark-circle {
  width: 80px;
  height: 80px;
  position: relative;
  display: inline-block;
  vertical-align: top;
  margin-left: auto;
  margin-right: auto;
}

.checkmark-circle .background {
  width: 80px;
  height: 80px;
  border-radius: 50%;
  background: #0d6efd;
  position: absolute;
}

.success-animation {
  margin: 0 auto;
  position: relative;
}

.checkmark-check {
  width: 80px;
  height: 80px;
  background-color: #0d6efd;
  border-radius: 50%;
  display: block;
  stroke-width: 2;
  stroke: #fff;
  stroke-miterlimit: 10;
  box-shadow: inset 0px 0px 0px #0d6efd;
  animation: fill 0.4s ease-in-out 0.4s forwards, scale 0.3s ease-in-out 0.9s both;
  position: relative;
  top: 0;
  right: 0;
}

.checkmark-check:before {
  content: "";
  width: 30px;
  height: 60px;
  border-right: 6px solid white;
  border-top: 6px solid white;
  position: absolute;
  top: 10px;
  left: 28px;
  transform: scaleX(-1) rotate(135deg);
  transform-origin: left top;
  animation: check-animation 0.8s ease-in forwards;
}

@keyframes check-animation {
  0% {
    height: 0;
    width: 0;
    opacity: 1;
  }
  20% {
    height: 0;
    width: 30px;
    opacity: 1;
  }
  40% {
    height: 60px;
    width: 30px;
    opacity: 1;
  }
  100% {
    height: 60px;
    width: 30px;
    opacity: 1;
  }
}

@keyframes fill {
  100% {
    box-shadow: inset 0px 0px 0px 40px #0d6efd;
  }
}

@keyframes scale {
  0%, 100% {
    transform: none;
  }
  50% {
    transform: scale3d(1.1, 1.1, 1);
  }
}

/* Responsive adjustments */
@media (max-width: 640px) {
  .modal-footer {
    flex-direction: column-reverse;
  }
}
</style>