```vue
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
        :class="{ 'animate-modal-enter': isEntering }"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">Verifikasi Anda Akan Diproses</h5>
            <button 
              type="button" 
              class="btn-close" 
              aria-label="Close"
              @click="close"
            ></button>
          </div>
          <div class="modal-body">
            <p>Dengan melanjutkan, Anda menyetujui bahwa laporan ini akan diproses untuk verifikasi dan membutuhkan biaya Rp. {{ formatCurrency(biayaVerifikasi) }} sesuai ketentuan layanan.</p>
          </div>
          <div class="modal-footer">
            <button 
              type="button" 
              class="btn btn-secondary" 
              @click="close"
            >
              Kembali
            </button>
            <button 
              type="button" 
              class="btn btn-primary position-relative" 
              @click="$emit('verify')"
              :disabled="isVerifying"
            >
              <span v-if="!isVerifying">Lanjutkan Verifikasi</span>
              <span v-else>
                <span class="spinner-border spinner-border-sm me-1" role="status"></span>
                Memproses...
              </span>
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  isVisible: Boolean,
  biayaVerifikasi: Number,
  isVerifying: Boolean
});

const emit = defineEmits(['close', 'verify']);

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

// Format currency
const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID').format(amount);
};

// Close modal
const close = () => {
  isEntering.value = false;
  setTimeout(() => {
    emit('close');
  }, 300);
};
</script>

<style scoped>
.animate-modal-enter {
  animation: modalFadeIn 0.4s ease-out forwards;
}

@keyframes modalFadeIn {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.modal.fade.show {
  transition: background-color 0.3s ease;
}

.modal-dialog {
  transition: transform 0.3s ease, opacity 0.3s ease;
}
</style>
```