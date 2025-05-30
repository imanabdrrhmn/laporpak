<template>
  <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-dialog-centered modal-sm mx-auto">
      <div class="modal-content">
        <div class="modal-body p-3 p-md-4 position-relative">
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
            <p class="text-secondary mb-2 fs-6">Bantu kami meningkatkan layanan pelaporan</p>
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

          <!-- Modern Tab Select - Responsive layout -->
          <div class="tab-select-container mb-3">
            <div class="tab-select">
              <button
                type="button"
                class="tab-btn"
                :class="{ active: form.kategori === 'Pelaporan' }"
                @click="form.kategori = 'Pelaporan'"
              >
                <i class="bi bi-megaphone me-1 me-md-2"></i><span>Pelaporan</span>
              </button>
              <button
                type="button"
                class="tab-btn"
                :class="{ active: form.kategori === 'Verifikasi' }"
                @click="form.kategori = 'Verifikasi'"
              >
                <i class="bi bi-check-circle me-1 me-md-2"></i><span>Verifikasi</span>
              </button>
            </div>
            <div class="tab-select mt-2">
              <button
                type="button"
                class="tab-btn"
                :class="{ active: form.kategori === 'Cari Laporan' }"
                @click="form.kategori = 'Cari Laporan'"
              >
                <i class="bi bi-search me-1 me-md-2"></i><span>Cari Laporan</span>
              </button>
              <button
                type="button"
                class="tab-btn"
                :class="{ active: form.kategori === 'Lapor Map' }"
                @click="form.kategori = 'Lapor Map'"
              >
                <i class="bi bi-geo-alt me-1 me-md-2"></i><span>Lapor Map</span>
              </button>
            </div>
            <div class="tab-select mt-2">
              <button
                type="button"
                class="tab-btn w-100"
                :class="{ active: form.kategori === 'Umum' }"
                @click="form.kategori = 'Umum'"
              >
                <i class="bi bi-globe me-1 me-md-2"></i><span>Umum</span>
              </button>
            </div>
          </div>
          <div v-if="form.errors.kategori" class="error-message mb-2">{{ form.errors.kategori }}</div>

          <!-- Enhanced Feedback Textarea -->
          <div class="mb-3">
            <div class="position-relative textarea-container">
              <textarea
                class="form-control p-2 p-md-3 custom-scrollbar"
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
/* Hide vertical scrollbar for textarea */
.custom-scrollbar {
  scrollbar-width: none; /* For Firefox */
  -ms-overflow-style: none; /* For Internet Explorer and Edge */
}

.custom-scrollbar::-webkit-scrollbar {
  display: none; /* For Chrome, Safari, and other WebKit browsers */
}

/* Modal styling */
.modal-content {
  border-radius: 1rem;
  border: none;
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
  background: #fff;
  width: 100%;
  margin: 0 auto;
}

.modal-dialog {
  max-width: 90%;
  margin: 1.75rem auto;
}

@media (min-width: 576px) {
  .modal-content {
    max-width: 450px;
  }
  
  .modal-dialog {
    max-width: 450px;
    margin: 1.75rem auto;
  }
}

.modal-body {
  padding: 1rem !important;
}

@media (min-width: 768px) {
  .modal-body {
    padding: 1.25rem !important;
  }
}

/* Feedback icon styling */
.feedback-icon-container {
  display: inline-flex;
  justify-content: center;
  align-items: center;
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: linear-gradient(45deg, #0d6efd, #0dcaf0);
  color: white;
  font-size: 1.5rem;
  margin-bottom: 0.8rem;
  animation: pulse-light 2s infinite;
}

@media (min-width: 576px) {
  .feedback-icon-container {
    width: 60px;
    height: 60px;
    font-size: 1.8rem;
  }
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
  font-size: 1.8rem;
  position: relative;
}

@media (min-width: 576px) {
  .star {
    font-size: 2rem;
  }
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
  font-size: 0.9rem;
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

@media (min-width: 576px) {
  .rating-label {
    font-size: 1rem;
  }
}

@keyframes star-pulse {
  0% { transform: scale(1); }
  50% { transform: scale(1.3); }
  100% { transform: scale(1); }
}

/* Service label styling */
.service-label {
  font-size: 0.9rem;
  font-weight: 500;
  margin-bottom: 0.8rem;
  color: #495057;
  position: relative;
  display: inline-block;
}

@media (min-width: 576px) {
  .service-label {
    font-size: 1rem;
  }
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
.tab-select-container {
  display: flex;
  flex-direction: column;
}

.tab-select {
  display: flex;
  justify-content: space-between;
  gap: 0.4rem;
}

@media (min-width: 576px) {
  .tab-select {
    gap: 0.6rem;
  }
}

.tab-btn {
  flex: 1;
  padding: 0.6rem 0.4rem;
  border: 1px solid #e0e0e0;
  border-radius: 10px;
  cursor: pointer;
  font-size: 0.8rem;
  font-weight: 500;
  transition: all 0.3s;
  background-color: white;
  color: #6c757d;
  display: flex;
  align-items: center;
  justify-content: center;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

@media (min-width: 576px) {
  .tab-btn {
    padding: 0.8rem;
    font-size: 0.9rem;
  }
}

.tab-btn span {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
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
  top: 0.6rem;
  right: 0.6rem;
  background: none;
  border: none;
  font-size: 1.1rem;
  color: #6c757d;
  cursor: pointer;
  transition: all 0.3s;
  z-index: 10;
  width: 32px;
  height: 32px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media (min-width: 576px) {
  .close-btn {
    top: 0.8rem;
    right: 0.8rem;
    font-size: 1.2rem;
    width: 36px;
    height: 36px;
  }
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
  font-size: 0.85rem;
}

@media (min-width: 576px) {
  .form-control {
    font-size: 0.9rem;
  }
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
  font-size: 0.65rem;
  color: #6c757d;
  background-color: rgba(255, 255, 255, 0.9);
  padding: 1px 6px;
  border-radius: 20px;
  box-shadow: 0 2px 5px rgba(0,0,0,0.1);
  transition: all 0.3s;
}

@media (min-width: 576px) {
  .text-counter {
    font-size: 0.7rem;
    padding: 2px 8px;
  }
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
  font-size: 0.95rem;
  box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}

@media (min-width: 576px) {
  .submit-btn {
    font-size: 1rem;
  }
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
  font-size: 0.75rem;
  margin-top: 0.2rem;
  animation: fadeIn 0.3s ease-in-out;
}

@media (min-width: 576px) {
  .error-message {
    font-size: 0.8rem;
  }
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

/* For extra small devices */
@media (max-width: 320px) {
  .star {
    font-size: 1.5rem;
    margin: 0 0.2rem !important;
  }
  
  .tab-btn {
    padding: 0.5rem 0.3rem;
    font-size: 0.75rem;
  }
  
  .feedback-icon-container {
    width: 45px;
    height: 45px;
    font-size: 1.3rem;
  }
  
  .modal-title {
    font-size: 1.1rem !important;
  }
  
  .text-secondary {
    font-size: 0.8rem !important;
  }
}

/* For landscape orientation on smaller devices */
@media (max-height: 500px) and (orientation: landscape) {
  .modal-body {
    max-height: 85vh;
    overflow-y: auto;
  }
  
  .feedback-icon-container {
    width: 40px;
    height: 40px;
    font-size: 1.2rem;
    margin-bottom: 0.4rem;
  }
  
  .star-rating {
    margin-bottom: 0.2rem;
  }
  
  .star {
    font-size: 1.5rem;
  }
  
  .modal-title {
    font-size: 1.1rem !important;
    margin-bottom: 0.2rem !important;
  }
  
  .text-secondary {
    margin-bottom: 0.2rem !important;
  }
  
  .form-control {
    padding: 0.25rem 0.5rem !important;
  }
}
</style>