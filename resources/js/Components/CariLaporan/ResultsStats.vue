<template>
  <div v-if="!loading && totalItems > 0" class="results-stats mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <p class="mb-0 results-text">
        Menampilkan {{ itemsOnPage }} dari {{ totalItems }} hasil
      </p>
      <div class="d-flex align-items-center items-per-page-selector">
        <label for="itemsPerPageSelect" class="me-2 mb-0 items-per-page-label">Tampilkan:</label>
        <select
          id="itemsPerPageSelect"
          class="form-select form-select-sm items-per-page-select"
          :value="itemsPerPage"
          @change="$emit('update:itemsPerPage', parseInt($event.target.value))"
          style="width: auto;"
          aria-label="Jumlah item per halaman"
        >
          <option :value="6">6</option>
          <option :value="12">12</option>
          <option :value="24">24</option>
          <option :value="48">48</option>
        </select>
      </div>
    </div>
  </div>
  <div v-else-if="!loading && totalItems === 0 && showWhenEmpty" class="results-stats mb-3">
    <p class="mb-0 results-text">Tidak ada hasil ditemukan.</p>
  </div>
</template>

<script setup>
defineProps({
  loading: Boolean,
  itemsOnPage: {
    type: Number,
    default: 0,
  },
  totalItems: {
    type: Number,
    default: 0,
  },
  itemsPerPage: {
    type: [Number, String],
    required: true,
  },
  showWhenEmpty: {
    type: Boolean,
    default: false
  }
});

defineEmits(['update:itemsPerPage']);
</script>

<style scoped>
.results-stats {
  font-size: 0.9rem;
  color: #6c757d;
  padding: 0.75rem 1rem;
  background-color: #f8f9fa;
  border-radius: 0.375rem;
  border: 1px solid #dee2e6;
}
.results-text {
  color: #495057;
}
.items-per-page-selector {
  gap: 0.5rem;
}
.items-per-page-label {
  font-weight: 500;
  color: #495057;
}
.items-per-page-select {
  border-radius: 0.25rem;
  border-color: #ced4da;
  padding-right: 2rem;
}
</style>
