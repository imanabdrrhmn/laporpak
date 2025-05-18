<template>
  <div class="results-stats mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <p class="mb-0">
        Menampilkan {{ displayedResults }} dari {{ totalResults }} hasil
      </p>
      <div class="d-flex align-items-center">
        <label for="itemsPerPage" class="me-2 mb-0">Tampilkan:</label>
        <select
          id="itemsPerPage"
          class="form-select form-select-sm"
          v-model="itemsPerPage"
          style="width: auto"
          aria-label="Jumlah item per halaman"
        >
          <option value="6">6</option>
          <option value="12">12</option>
          <option value="24">24</option>
          <option value="48">48</option>
        </select>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  totalResults: Number,
  displayedResults: Number
});

defineEmits(['update:items-per-page']);
</script>

<style scoped>
.results-stats {
  font-size: 0.9rem;
  color: #6c757d;
}
</style>