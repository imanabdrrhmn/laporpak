<template>
  <div
    class="modal fade"
    id="actionModal"
    tabindex="-1"
    aria-labelledby="actionModalLabel"
    aria-hidden="true"
    ref="modalRef"
  >
    <div class="modal-dialog modal-dialog-centered modal-sm">
      <div class="modal-content border-0 shadow">
        <div class="modal-header border-0 pb-2">
          <h6 class="modal-title fw-bold text-dark" id="actionModalLabel">Pilih Aksi</h6>
          <button 
            type="button" 
            class="btn-close btn-close-custom" 
            data-bs-dismiss="modal" 
            aria-label="Close" 
            @click="emit('close-action-modal')"
          ></button>
        </div>
        <div class="modal-body pt-0">
          <div class="user-info mb-3">
            <div class="text-muted small mb-1">{{ selectedTopUp?.user.name }}</div>
            <div class="amount-display">Rp {{ formatCurrency(selectedTopUp?.amount) }}</div>
          </div>
          
          <div class="action-buttons">
            <button
              v-if="selectedTopUp?.status === 'pending'"
              class="action-btn success-btn"
              @click="emit('confirm-verify')"
              :disabled="loadingIds.includes(selectedTopUp?.id)"
            >
              <i class="fas fa-check me-2"></i> Verifikasi
            </button>
            <button
              v-if="selectedTopUp && selectedTopUp.status !== 'pending'"
              class="action-btn warning-btn"
              @click="emit('confirm-set-pending')"
              :disabled="loadingIds.includes(selectedTopUp?.id)"
            >
              <i class="fas fa-clock me-2"></i> Atur Pending
            </button>
            <button
              v-if="selectedTopUp && selectedTopUp.status !== 'rejected'"
              class="action-btn danger-btn"
              @click="emit('confirm-reject')"
              :disabled="loadingIds.includes(selectedTopUp?.id)"
            >
              <i class="fas fa-times me-2"></i> Tolak
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

defineProps({
  selectedTopUp: Object,
  loadingIds: Array,
  formatCurrency: Function,
});

const emit = defineEmits(["confirm-verify", "confirm-set-pending", "confirm-reject", "close-action-modal"]);
</script>

<style scoped>
.modal-content {
  border-radius: 12px;
  background: #ffffff;
}

.modal-header {
  padding: 1rem 1.25rem 0.5rem;
}

.modal-body {
  padding: 0.5rem 1.25rem 1.25rem;
}

.btn-close-custom {
  background-size: 1em;
  opacity: 0.6;
  transition: all 0.3s ease;
}

.btn-close-custom:hover {
  opacity: 1;
  transform: rotate(90deg);
}

.user-info {
  text-align: center;
  padding: 0.75rem;
  background: #f8f9fa;
  border-radius: 8px;
}

.amount-display {
  font-size: 1.1rem;
  font-weight: 600;
  color: #0d6efd;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}

.action-btn {
  padding: 0.75rem 1rem;
  border: none;
  border-radius: 8px;
  font-weight: 500;
  font-size: 0.9rem;
  transition: all 0.2s ease;
  cursor: pointer;
  width: 100%;
}

.action-btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

.success-btn {
  background: #0d6efd;
  color: white;
}

.success-btn:hover:not(:disabled) {
  background: #0b5ed7;
  transform: translateY(-1px);
}

.warning-btn {
  background: #0d6efd;
  color: white;
  border: none;
}

.warning-btn:hover:not(:disabled) {
  background: #0b5ed7;
  transform: translateY(-1px);
}

.danger-btn {
  background: #dc3545;
  color: white;
  border: none;
}

.danger-btn:hover:not(:disabled) {
  background: #bb2d3b;
  transform: translateY(-1px);
}

.modal-sm {
  max-width: 400px;
}

@media (max-width: 576px) {
  .modal-sm {
    max-width: 90%;
    margin: 1rem auto;
  }
}
</style>