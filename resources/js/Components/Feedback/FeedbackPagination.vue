<template>
  <div class="pagination-wrapper mt-4">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
      <div class="text-muted small">
        Menampilkan {{ startIndex }}–{{ endIndex }} dari {{ totalItems }} ulasan
      </div>
      <div class="d-flex align-items-center gap-2">
        <button
          class="btn btn-outline-primary btn-sm"
          :disabled="currentPage === 1"
          @click="$emit('go-to-previous-page')"
          aria-label="Halaman sebelumnya"
        >
          <i class="bi bi-chevron-left"></i> Sebelumnya
        </button>
        <div class="input-group input-group-sm page-jumper">
          <span class="input-group-text">Halaman</span>
          <input
            type="number"
            class="form-control"
            :value="currentPage"
            @input="$emit('jump-to-page', $event)"
            min="1"
            :max="totalPages"
            aria-label="Nomor halaman"
          />
          <span class="input-group-text">/ {{ totalPages }}</span>
        </div>
        <button
          class="btn btn-outline-primary btn-sm"
          :disabled="currentPage === totalPages"
          @click="$emit('go-to-next-page')"
          aria-label="Halaman berikutnya"
        >
          Selanjutnya <i class="bi bi-chevron-right"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  currentPage: Number,
  totalPages: Number,
  startIndex: Number,
  endIndex: Number,
  totalItems: Number,
});

defineEmits(['go-to-previous-page', 'go-to-next-page', 'jump-to-page']);
</script>

<style scoped>
.pagination-wrapper {
  padding: 1rem 0;
}

.page-jumper {
  max-width: 150px;
}

.page-jumper .form-control {
  text-align: center;
  padding: 0.25rem;
  font-size: 0.875rem;
}

.page-jumper .input-group-text {
  font-size: 0.875rem;
  background-color: #f8f9fa;
}

.btn-sm {
  padding: 0.375rem 0.75rem;
}

.btn-outline-primary:hover:not(:disabled) {
  background-color: #0a58ca;
  color: white;
  transform: translateY(-1px);
}

.btn-outline-primary:disabled {
  cursor: not-allowed;
  opacity: 0.6;
}

@media (max-width: 575.98px) {
  .pagination-wrapper {
    padding: 0.5rem 0;
  }

  .page-jumper {
    max-width: 120px;
  }

  .text-muted {
    font-size: 0.8rem;
  }

  .btn-sm {
    padding: 0.25rem 0.5rem;
    font-size: 0.8rem;
  }
}
</style>