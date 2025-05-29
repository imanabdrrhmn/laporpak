<template>
  <div v-if="isVisible" class="modal-overlay" @click.self="close">
    <div class="modal-content">
      <h3>Batalkan Publikasi Laporan</h3>
      <textarea
        v-model="reason"
        placeholder="Masukkan alasan pembatalan publikasi"
        rows="4"
        :disabled="loading"
        class="reason-input"
      ></textarea>
      <div class="modal-actions">
        <button @click="close" :disabled="loading">Batal</button>
        <button @click="confirm" :disabled="loading || !reason.trim()">
          <span v-if="loading">Sedang mengirim...</span>
          <span v-else>Konfirmasi</span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
  isVisible: Boolean,
  loading: Boolean,
});

const emit = defineEmits(['close', 'confirm']);

const reason = ref('');

watch(() => props.isVisible, (val) => {
  if (!val) reason.value = ''; // reset reason saat modal ditutup
});

const close = () => {
  emit('close');
};

const confirm = () => {
  emit('confirm', reason.value.trim());
};
</script>

<style scoped>
/* --- Sama seperti style modal sebelumnya --- */

.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.4);
  backdrop-filter: blur(8px);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 4000;
  padding: 1rem;
}

.modal-content {
  background: #fff;
  border-radius: 16px;
  padding: 2rem 2.5rem;
  max-width: 440px;
  width: 100%;
  box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
  display: flex;
  flex-direction: column;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.modal-content h3 {
  font-weight: 700;
  font-size: 1.6rem;
  color: #222;
  margin-bottom: 1.25rem;
  text-align: center;
}

.reason-input {
  width: 100%;
  min-height: 120px;
  padding: 0.75rem 1rem;
  font-size: 1rem;
  border-radius: 12px;
  border: 1.5px solid #ccc;
  box-shadow: inset 0 2px 6px rgba(0,0,0,0.06);
  font-family: inherit;
  transition: border-color 0.3s ease, box-shadow 0.3s ease;
  outline-offset: 2px;
  resize: vertical;
  background-color: white;
  color: #222;
}

.reason-input:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 8px rgba(13, 110, 253, 0.4);
  outline: none;
}

.modal-actions {
  margin-top: 1.5rem;
  display: flex;
  justify-content: flex-end;
  gap: 1rem;
}

.modal-actions button {
  padding: 0.65rem 1.5rem;
  border-radius: 10px;
  font-weight: 700;
  font-size: 1rem;
  cursor: pointer;
  border: none;
  font-family: inherit;
  transition: background-color 0.25s ease, box-shadow 0.25s ease;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  user-select: none;
}

.modal-actions button:first-child {
  background-color: #e2e6ea;
  color: #495057;
  box-shadow: 0 2px 8px rgba(0,0,0,0.08);
}

.modal-actions button:first-child:hover:not(:disabled) {
  background-color: #ced4da;
  color: #343a40;
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.modal-actions button:last-child {
  background-color: #dc3545;
  color: #fff;
  box-shadow: 0 4px 14px rgba(220, 53, 69, 0.5);
}

.modal-actions button:last-child:hover:not(:disabled) {
  background-color: #b02a37;
  box-shadow: 0 6px 20px rgba(176, 42, 55, 0.65);
}

.modal-actions button:disabled {
  cursor: not-allowed;
  opacity: 0.6;
  box-shadow: none;
  background-color: #adb5bd !important;
  color: #6c757d !important;
}
</style>
