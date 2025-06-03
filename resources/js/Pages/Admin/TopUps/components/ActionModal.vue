<template>
    <div
      class="modal fade"
      id="actionModal"
      tabindex="-1"
      aria-labelledby="actionModalLabel"
      aria-hidden="true"
      ref="modalRef"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="actionModalLabel">Pilih Aksi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="emit('close-action-modal')"></button>
          </div>
          <div class="modal-body">
            <p><strong>User:</strong> {{ selectedTopUp?.user.name }} ({{ selectedTopUp?.user.email }})</p>
            <p><strong>Jumlah:</strong> Rp {{ formatCurrency(selectedTopUp?.amount) }}</p>
            <div class="d-grid gap-2">
              <button
                v-if="selectedTopUp?.status === 'pending'"
                class="btn btn-success"
                @click="emit('confirm-verify')"
                :disabled="loadingIds.includes(selectedTopUp?.id)"
              >
                <i class="fas fa-check me-2"></i> Verifikasi
              </button>
              <button
                v-if="selectedTopUp && selectedTopUp.status !== 'pending'"
                class="btn btn-warning"
                @click="emit('confirm-set-pending')"
                :disabled="loadingIds.includes(selectedTopUp?.id)"
              >
                <i class="fas fa-clock me-2"></i> Atur Pending
              </button>
              <button
                v-if="selectedTopUp && selectedTopUp.status !== 'rejected'"
                class="btn btn-danger"
                @click="emit('confirm-reject')"
                :disabled="loadingIds.includes(selectedTopUp?.id)"
              >
                <i class="fas fa-times me-2"></i> Tolak
              </button>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="emit('close-action-modal')">Batal</button>
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
  .modal-body .btn {
    width: 100%;
    margin-bottom: 0.5rem;
  }
  
  .modal-body .btn:last-child {
    margin-bottom: 0;
  }
  </style>