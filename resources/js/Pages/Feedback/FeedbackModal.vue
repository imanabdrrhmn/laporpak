<template>
  <div class="modal fade" id="feedbackModal" tabindex="-1" aria-labelledby="feedbackModalLabel" aria-hidden="true" ref="modal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body p-4 position-relative">
          <!-- Close Button -->
          <button type="button" class="close-btn" @click="hideModal" aria-label="Close">
            <i class="bi bi-x-lg"></i>
          </button>

          <!-- Title and Subtitle -->
          <div class="text-center mb-4">
            <h2 class="modal-title fs-2 text-primary fw-bold" id="feedbackModalLabel">
              {{ mode === 'create' ? 'Berikan Masukkan Anda' : 'Edit Masukkan Anda' }}
            </h2>
            <p class="text-secondary">Bantu kami meningkatkan layanan pelaporan</p>
          </div>

          <!-- Star Rating -->
          <div class="d-flex justify-content-center align-items-center mb-3">
            <div class="star-rating">
              <span
                v-for="index in 5"
                :key="index"
                class="star mx-1"
                :class="{ 'text-warning': index <= form.rating, 'text-light': index > form.rating }"
                @click="form.rating = index"
              >★</span>
            </div>
          </div>
          <div v-if="form.errors.rating" class="text-danger text-center">{{ form.errors.rating }}</div>

          <!-- Service Selection Label -->
          <div class="service-label">Pilih Layanan</div>

          <!-- Tab Select -->
          <div class="tab-select mb-4">
            <button
              type="button"
              class="tab-btn"
              :class="{ active: form.kategori === 'Pelaporan' }"
              @click="form.kategori = 'Pelaporan'"
            >Pelaporan</button>
            <button
              type="button"
              class="tab-btn"
              :class="{ active: form.kategori === 'Verifikasi' }"
              @click="form.kategori = 'Verifikasi'"
            >Verifikasi</button>
            <button
              type="button"
              class="tab-btn"
              :class="{ active: form.kategori === 'Umum' }"
              @click="form.kategori = 'Umum'"
            >Umum</button>
          </div>
          <div v-if="form.errors.kategori" class="text-danger mb-2">{{ form.errors.kategori }}</div>

          <!-- Feedback Textarea -->
          <div class="mb-4">
            <div class="position-relative">
              <textarea
                class="form-control p-3"
                v-model="form.message"
                placeholder="Masukkan anda!!"
                rows="5"
                maxlength="255"
              ></textarea>
              <div class="text-counter">{{ form.message.length }}/255</div>
            </div>
            <div v-if="form.errors.message" class="text-danger mt-1">{{ form.errors.message }}</div>
          </div>

          <!-- Submit Button -->
          <button class="btn btn-primary w-100 py-3 mb-3" @click="submit">
            {{ mode === 'create' ? 'Kirim Masukkan' : 'Update Masukkan' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';
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

const modal = ref(null);
let modalInstance;

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

.text-counter {
  position: absolute;
  bottom: 10px;
  right: 10px;
  font-size: 0.8rem;
  color: #6c757d;
  background-color: rgba(255, 255, 255, 0.8);
  padding: 2px 8px;
  border-radius: 12px;
}
</style>