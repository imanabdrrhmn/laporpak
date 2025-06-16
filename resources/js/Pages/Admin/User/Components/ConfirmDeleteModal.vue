<!-- ConfirmDeleteModal.vue -->
<template>
  <div v-if="isOpen" class="modal-overlay" @click.self="close">
    <div class="modal-container">
      <div class="modal-header">
        <h5 class="modal-title">Konfirmasi Hapus</h5>
        <button class="custom-close" @click="close">
          <i class="bi bi-x"></i>
        </button>
      </div>
      <div class="modal-body">
        <p>Hapus pengguna <strong>{{ userName }}</strong>?</p>
        <p class="text-danger">Aksi ini tidak bisa dibatalkan!</p>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" @click="close">Batal</button>
        <button class="btn btn-primary" @click="confirm">Ya, hapus!</button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  userName: String,
  isOpen: Boolean
})

const emit = defineEmits(['close', 'confirm'])

const close = () => {
  emit('close')
}

const confirm = () => {
  emit('confirm')
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1000;
}

.modal-container {
  background: white;
  border-radius: 8px;
  width: 100%;
  max-width: 500px;
  margin: 1rem;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 1rem 1.5rem;
  border-bottom: 1px solid #e5e7eb;
  background: #0d6efd;
  color: white;
  border-radius: 8px 8px 0 0;
}

.modal-title {
  margin: 0;
  font-size: 1.25rem;
  font-weight: 600;
  color: white;
}

.custom-close {
  border-radius: 50%;
  padding: 6px;
  background-color: #f3f4f6;
  border: none;
  opacity: 1;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  width: 32px;
  height: 32px;
  transition: all 0.2s ease;
}

.custom-close:hover {
  background-color: #e5e7eb;
  transform: rotate(90deg);
}

.custom-close i {
  font-size: 20px;
  line-height: 1;
  color: #374151;
}

.modal-body {
  padding: 1.5rem;
}

.text-danger {
  color: #dc3545;
  font-weight: 500;
}

.modal-footer {
  padding: 1rem 1.5rem;
  border-top: 1px solid #e5e7eb;
  display: flex;
  justify-content: flex-end;
  gap: 0.5rem;
}

.btn {
  padding: 0.5rem 1rem;
  border-radius: 6px;
  border: none;
  cursor: pointer;
  font-weight: 500;
}

.btn-secondary {
  background: #6c757d;
  color: white;
}

.btn-primary {
  background: red;
  color: white;
}

.btn-secondary:hover {
  background: #5c636a;
}


</style>