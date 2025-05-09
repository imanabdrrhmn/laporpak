<template>
  <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body p-4 position-relative">
          <!-- Close Button -->
          <button type="button" class="close-btn" @click="hideModal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>

          <!-- Title and Subtitle with Icon -->
          <div class="text-center mb-3">
            <div class="feedback-icon-container mb-2">
              <i class="bi bi-chat-square-heart-fill"></i>
            </div>
            <h2 class="modal-title fs-4 fw-bold gradient-text" id="feedbackModalLabel">
              {{ mode === 'create' ? 'Berikan Masukkan Anda' : 'Edit Masukkan Anda' }}
            </h2>
            <p class="text-secondary mb-2">Bantu kami meningkatkan layanan pelaporan</p>
          </div>

          <!-- Star Rating with Animation and Labels -->
          <div class="d-flex flex-column align-items-center mb-3">
            <div class="star-rating">
              <span
                v-for="index in 5"
                :key="index"
                class="star mx-1"
                :class="{ 'active': index <= form.rating, 'inactive': index > form.rating }"
                @click="selectRating(index)"
                @mouseover="hoverRating = index"
                @mouseleave="hoverRating = 0"
              >★</span>
            </div>
            
            <!-- Star Rating Label -->
            <div class="rating-label mt-2">
              {{ ratingLabels[form.rating - 1] }}
            </div>
          </div>
          <div v-if="form.errors.rating" class="error-message text-center">{{ form.errors.rating }}</div>

          <!-- Service Selection Label with animated underline -->
          <div class="service-label">
            <span>Pilih Layanan</span>
            <div class="animated-underline"></div>
          </div>

          <!-- Modern Tab Select -->
          <div class="tab-select mb-3">
            <button
              type="button"
              class="tab-btn"
              :class="{ active: form.kategori === 'Pelaporan' }"
              @click="form.kategori = 'Pelaporan'"
            >
              <i class="bi bi-megaphone me-2"></i>Pelaporan
            </button>
            <button
              type="button"
              class="tab-btn"
              :class="{ active: form.kategori === 'Verifikasi' }"
              @click="form.kategori = 'Verifikasi'"
            >
              <i class="bi bi-check-circle me-2"></i>Verifikasi
            </button>
            <button
              type="button"
              class="tab-btn"
              :class="{ active: form.kategori === 'Umum' }"
              @click="form.kategori = 'Umum'"
            >
              <i class="bi bi-globe me-2"></i>Umum
            </button>
          </div>
          <div v-if="form.errors.kategori" class="error-message mb-2">{{ form.errors.kategori }}</div>

          <!-- Enhanced Feedback Textarea -->
          <div class="mb-3">
            <div class="position-relative textarea-container">
              <textarea
                class="form-control p-3 custom-scrollbar"
                v-model="form.message"
                placeholder="Bagikan pendapat Anda di sini..."
                rows="4"
                maxlength="255"
              ></textarea>
              <div class="text-counter" :class="{'text-warning': form.message.length > 200}">
                {{ form.message.length }}/255
              </div>
            </div>
            <div v-if="form.errors.message" class="error-message mt-1">{{ form.errors.message }}</div>
          </div>

          <!-- Submit Button with loading state -->
          <button 
            class="btn btn-primary w-100 py-2 submit-btn" 
            @click="submit" 
            :disabled="form.processing"
          >
            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
            {{ mode === 'create' ? 'Kirim Masukkan' : 'Update Masukkan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch, computed } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
  mode: {
    type: String,
    default: 'create'
  },
  feedbackData: {
    type: Object,
    default: () => ({})
  }
});

const emit = defineEmits(['modalClosed']);

const form = useForm({
  message: '',
  rating: 4,
  kategori: 'Pelaporan'
});

const hoverRating = ref(0);
const modal = ref(null);
let modalInstance;

// Rating labels for each star
const ratingLabels = [
  "Buruk",
  "Kurang",
  "Cukup",
  "Bagus",
  "Sangat Bagus"
];

// Animation effect when selecting rating
const selectRating = (index) => {
  form.rating = index;
  // Add a small animation effect
  const stars = document.querySelectorAll('.star');
  stars.forEach((star, i) => {
    if (i < index) {
      star.classList.add('pulse');
      setTimeout(() => {
        star.classList.remove('pulse');
      }, 300);
    }
  });
  
  // Animate the rating label
  const ratingLabel = document.querySelector('.rating-label');
  if (ratingLabel) {
    ratingLabel.style.transform = 'scale(1.1)';
    setTimeout(() => {
      ratingLabel.style.transform = 'scale(1)';
    }, 300);
  }
};

// Ensure message doesn't exceed max length
watch(() => form.message, (newValue) => {
  if (newValue && newValue.length > 255) {
    form.message = newValue.substring(0, 255);
  }
});

// Watch for changes in props to update form
watch(() => props.feedbackData, (newValue) => {
  if (props.mode === 'edit' && newValue) {
    form.message = newValue.message || '';
    form.rating = newValue.rating || 4;
    form.kategori = newValue.kategori || 'Pelaporan';
  }
}, { immediate: true });

// Method to initialize and show the modal
const showModal = async () => {
  // Reset form when opening in create mode
  if (props.mode === 'create') {
    form.reset();
    form.rating = 4; // Default to 4 stars
    form.kategori = 'Pelaporan'; // Default kategori
  }
  
  const bootstrap = await import('bootstrap');
  modalInstance = new bootstrap.Modal(modal.value);
  modalInstance.show();
};

// Method to hide the modal
const hideModal = () => {
  if (modalInstance) {
    modalInstance.hide();
    emit('modalClosed');
  }
};

const submit = () => {
  if (props.mode === 'create') {
    form.post('/feedback', {
      onSuccess: () => hideModal()
    });
  } else {
    form.put(`/feedback/${props.feedbackData.id}`, {
      onSuccess: () => hideModal()
    });
  }
};

// Expose methods to parent component
defineExpose({
  showModal,
  hideModal
});
</script>

<style scoped>
/* Custom scrollbar styling for textarea only */
.custom-scrollbar::-webkit-scrollbar {
  width: 8px;
  height: 8px;
}

.custom-scrollbar::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
  background: linear-gradient(45deg, #0d6efd, #0dcaf0);
  border-radius: 10px;
  transition: all 0.3s ease;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
  background: linear-gradient(45deg, #0b5ed7, #0babcc);
}

/* Modal styling */
.modal-content {
  border-radius: 1rem;
  border: none;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  background: #fff;
  width: 100%;
  max-width: 500px; /* Reduced form width */
}

.modal-body {
  padding: 1.5rem !important; /* Reduced padding */
}

/* Feedback icon styling */
.feedback-icon-container {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  width: 60px;
  height: 60px;
  border-radius: 50%;
  background: linear-gradient(45deg, #0d6efd, #0dcaf0);
  color: white;
  font-size: 1.8rem;
  margin-bottom: 0.8rem;
  animation: pulse-light 2s infinite;
}

/* Title with gradient */
.gradient-text {
  background: linear-gradient(45deg, #0d6efd, #0dcaf0);
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
  margin-bottom: 0.4rem;
}

/* Star rating styling */
.star-rating {
  display: flex;
  justify-content: center;
}

.star {
  cursor: pointer;
  transition: all 0.3s;
  font-size: 2rem; /* Reduced star size */
  position: relative;
}

.star.active {
  color: #ffc107;
  text-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
}

.star.inactive {
  color: #e0e0e0;
}

.star:hover {
  transform: scale(1.2);
}

.star.pulse {
  animation: star-pulse 0.3s ease-in-out;
}

/* Rating label styling */
.rating-label {
  font-size: 1rem;
  font-weight: 600;
  color: #0d6efd;
  background: -webkit-linear-gradient(45deg, #0d6efd, #0dcaf0);
  background-clip: text;
  -webkit-text-fill-color: transparent;
  padding: 4px 12px;
  border-radius: 20px;
  transition: all 0.3s;
  animation: fadeIn 0.3s ease-in-out;
  text-align: center;
  min-width: 100px;
  box-shadow: 0 2px 8px rgba(13, 110, 253, 0.1);
}

@keyframes star-pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.3); }
  100% { transform: scale(1); }
}

/* Service label styling */
.service-label {
  font-size: 1rem;
  font-weight: 500;
  margin-bottom: 0.8rem;
  color: #495057;
  position: relative;
  display: inline-block;
}

.animated-underline {
  position: absolute;
  bottom: -5px;
  left: 0;
  width: 100%;
  height: 2px;
  background: linear-gradient(90deg, #0d6efd, #0dcaf0, #0d6efd);
  background-size: 200% 100%;
  animation: gradient-move 3s linear infinite;
}

@keyframes gradient-move {
  0% { background-position: 0% 0; }
  100% { background-position: 200% 0; }
}

/* Tab buttons styling */
.tab-select {
  display: flex;
  justify-content: space-between;
  gap: 0.6rem;
}

.tab-btn {
  flex: 1;
  padding: 0.8rem;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  cursor: pointer;
  font-size: 0.9rem;
  font-weight: 500;
  transition: all 0.3s;
  background-color: white;
  color: #6c757d;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.tab-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.tab-btn.active {
  background: linear-gradient(45deg, #0d6efd, #0dcaf0);
  color: white;
  border-color: transparent;
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}

/* Close button styling */
.close-btn {
  position: absolute;
  top: 0.8rem;
  right: 0.8rem;
  background: none;
  border: none;
  font-size: 1.2rem;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.3s;
  z-index: 10;
  width: 36px;
  height: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

.close-btn:hover {
  background-color: #f8f9fa;
  color: #0d6efd;
  transform: rotate(90deg);
}

/* Textarea styling */
.textarea-container {
  border-radius: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
  position: relative;
  transition: all 0.3s;
}

.textarea-container:focus-within {
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.2);
}

.form-control {
  border-radius: 10px;
  border: 1px solid #e0e0e0;
  transition: all 0.3s;
  resize: none;
  font-size: 0.9rem;
}

.form-control:focus {
  border-color: #0d6efd;
  box-shadow: none;
}

/* Text counter styling */
.text-counter {
  position: absolute;
  bottom: 8px;
  right: 8px;
  font-size: 0.7rem;
  color: #6c757d;
  background-color: rgba(255, 255, 255, 0.9);
  padding: 2px 8px;
  border-radius: 20px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: all 0.3s;
}

/* Submit button styling */
.submit-btn {
  background: linear-gradient(45deg, #0d6efd, #0dcaf0);
  border: none;
  font-weight: 600;
  border-radius: 10px;
  transition: all 0.3s;
  position: relative;
  overflow: hidden;
  font-size: 1rem;
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}

.submit-btn:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 20px rgba(13, 110, 253, 0.4);
}

.submit-btn:active {
  transform: translateY(1px);
}

.submit-btn::after {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: linear-gradient(45deg, rgba(255,255,255,0.2), transparent);
  transition: all 0.3s;
}

.submit-btn:hover::after {
  transform: translateX(100%);
}

/* Error messages */
.error-message {
  color: #dc3545;
  font-size: 0.8rem;
  margin-top: 0.2rem;
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(-5px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes pulse-light {
  0% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0.4); }
  70% { box-shadow: 0 0 0 10px rgba(13, 110, 253, 0); }
  100% { box-shadow: 0 0 0 0 rgba(13, 110, 253, 0); }
}

/* Responsive adjustments */
@media (max-width: 576px) {
  .tab-select {
    flex-direction: column;
    gap: 0.5rem;
  }
  
  .star {
    font-size: 1.8rem;
  }
  
  .modal-body {
    padding: 1rem !important;
  }
  
  .modal-content {
    max-width: 90vw;
  }
}
</style>