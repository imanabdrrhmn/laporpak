<template>
  <div v-if="isVisible" class="modal-overlay" @click="close">
    <div class="modal-container" @click.stop>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Bukti Pembayaran</h5>
          <button
            type="button"
            class="btn-close"
            aria-label="Tutup"
            @click="close"
          ></button>
        </div>
        <div class="modal-body p-0 text-center">
          <img v-if="proofUrl" :src="proofUrl" alt="Bukti Pembayaran" class="img-fluid w-100" />
          <p v-else class="p-4 text-muted">URL gambar tidak valid.</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted } from 'vue';

const props = defineProps({
  isVisible: {
    type: Boolean,
    required: true,
  },
  proofUrl: {
    type: String,
    default: ''
  },
});

const emit = defineEmits(['close']);

const close = () => {
  emit('close');
};

const handleKeydown = (e) => {
  if (props.isVisible && e.key === 'Escape') {
    close();
  }
};

onMounted(() => {
  window.addEventListener('keydown', handleKeydown);
});

onUnmounted(() => {
  window.removeEventListener('keydown', handleKeydown);
});
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.6);
  backdrop-filter: blur(5px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 2000; 
  padding: 1rem;
}

.modal-container {
  background: white;
  border-radius: 0.5rem; 
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
  width: 100%;
  max-width: 800px;
  max-height: 90vh;
  display: flex;
  flex-direction: column;
}


.modal-header {
  border-bottom: 1px solid #dee2e6;
  padding: 1rem 1.25rem;
}

.modal-title {
  font-size: 1.25rem;
  font-weight: 500;
}

.modal-body {
  overflow-y: auto; 
  display: flex; 
  justify-content: center;
  align-items: center;
  background-color: #f8f9fa; 
}

.img-fluid {
  max-height: calc(90vh - 120px); 
  object-fit: contain;
}
</style>
