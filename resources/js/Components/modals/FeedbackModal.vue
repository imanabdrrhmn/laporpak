<template>
  <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body p-4 position-relative">
          <!-- Close Button -->
          <button type="button" class="close-btn" data-bs-dismiss="modal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>

          <!-- Title and Subtitle -->
          <div class="text-center mb-4">
            <h2 class="modal-title fs-2 text-primary fw-bold" id="feedbackModalLabel">Berikan Masukkan Anda</h2>
            <p class="text-secondary">Bantu kami meningkatkan layanan pelaporan</p>
          </div>

          <!-- Star Rating -->
          <div class="d-flex justify-content-center align-items-center mb-3">
            <div class="star-rating">
              <span
                v-for="index in 5"
                :key="index"
                class="star mx-1"
                :class="{ 'text-warning': index <= rating, 'text-light': index > rating }"
                @click="setRating(index)"
              >★</span>
            </div>
          </div>

          <!-- Service Selection Label -->
          <div class="service-label">Pilih Layanan</div>

          <!-- Tab Select -->
          <div class="tab-select mb-4">
            <button
              class="tab-btn"
              :class="{ active: activeTab === 'pelaporan' }"
              @click="setActiveTab('pelaporan')"
            >Pelaporan</button>
            <button
              class="tab-btn"
              :class="{ active: activeTab === 'verifikasi' }"
              @click="setActiveTab('verifikasi')"
            >Verifikasi</button>
          </div>

          <!-- Feedback Textarea -->
          <div class="mb-4">
            <textarea
              class="form-control p-3"
              v-model="feedback"
              placeholder="Masukkan anda!!"
              rows="5"
            ></textarea>
          </div>

          <!-- Submit Button -->
          <button class="btn btn-primary w-100 py-3 mb-3" @click="submitFeedback">Kirim Masukkan</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref } from 'vue';
import * as bootstrap from 'bootstrap';

export default {
  name: 'FeedbackModal',
  setup() {
    const rating = ref(4);
    const activeTab = ref('pelaporan');
    const feedback = ref('');

    const setRating = (value) => {
      rating.value = value;
    };

    const setActiveTab = (tab) => {
      activeTab.value = tab;
    };

    const submitFeedback = () => {
      console.log({
        rating: rating.value,
        type: activeTab.value,
        feedback: feedback.value,
      });
      feedback.value = '';
      const modalElement = document.getElementById('feedbackModal');
      const modal = bootstrap.Modal.getInstance(modalElement) || new bootstrap.Modal(modalElement);
      modal.hide();
    };

    return {
      rating,
      activeTab,
      feedback,
      setRating,
      setActiveTab,
      submitFeedback,
    };
  },
};
</script>

<style scoped>

.modal-content {
  border-radius: 0.75rem;
  border: none;
}

.star {
    cursor: pointer;
    transition: all 0.2s;
    font-size: 3rem;
}

.star:hover {
    transform: scale(1.2);
}

.text-light {
    color: #e0e0e0 !important;
}

.btn-primary {
    background-color: #0d6efd;
    border: none;
    font-weight: 500;
}

.tab-select {
    display: flex;
    justify-content: space-between;
    margin-bottom: 1rem;
    gap: 1rem;
}

.tab-btn {
  flex: 1;
  padding: 1rem;
  border: 1px solid #e0e0e0;
  border-radius: 8px;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 500;
  transition: all 0.3s;
  background-color: white;
  color: #6c757d;
}

.tab-btn.active {
  background-color: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

.close-btn {
  position: absolute;
  top: 1rem;
  left: 1rem;
  background: none;
  border: none;
  font-size: 1.5rem;
  color: #6c757d;
  cursor: pointer;
  transition: color 0.2s;
}

.close-btn:hover {
  color: #0d6efd;
}

.service-label {
  font-size: 1rem;
  font-weight: 500;
  margin-bottom: 0.5rem;
  color: #6c757d;
}


</style>

