<template>
  <Teleport to="body">
    <div 
      v-if="isVisible" 
      class="modal fade show" 
      :class="{ 'd-block': isVisible }" 
      tabindex="-1" 
      role="dialog"
      style="background-color: rgba(0,0,0,0.5);"
    >
      <div 
        class="modal-dialog modal-dialog-centered" 
        :class="{ 'animate-modal-success': isEntering }"
      >
        <div class="modal-content">
          <div class="modal-body text-center py-4">
            <div class="mb-3">
              <i 
                class="bi bi-check-circle-fill text-primary display-1 animate-check-icon"
                style="font-size: 4rem;"
              ></i>
            </div>
            <h4 class="modal-title mb-3">Verifikasi Anda Berhasil!</h4>
            <p>Silakan cek informasi lengkapnya di halaman berikut.</p>
            <div class="mt-4">
              <button 
                type="button" 
                class="btn btn-primary px-4" 
                @click="$emit('navigate')"
              >
                Lihat Halaman
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  isVisible: Boolean
});

const emit = defineEmits(['navigate']);

const isEntering = ref(false);

// Watch the isVisible prop correctly
watch(
  () => props.isVisible,
  (newVal) => {
    if (newVal) {
      setTimeout(() => {
        isEntering.value = true;
      }, 50);
    } else {
      isEntering.value = false;
    }
  }
);
</script>

<style scoped>
.animate-modal-success {
  animation: modalSuccessIn 0.5s ease-out forwards;
}

@keyframes modalSuccessIn {
  from {
    opacity: 0;
    transform: scale(0.9);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

.animate-check-icon {
  animation: checkIconAnimation 0.7s ease-out forwards;
  color: #0d6efd;
}

@keyframes checkIconAnimation {
  0% {
    opacity: 0;
    transform: scale(0);
  }
  50% {
    transform: scale(1.2);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

.modal.fade.show {
  transition: background-color 0.3s ease;
}

.modal-dialog {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
</style>