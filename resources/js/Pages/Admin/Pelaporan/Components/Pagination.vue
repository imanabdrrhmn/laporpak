<!-- resources/js/Components/Pelaporan/Pagination.vue -->
<template>
  <div class="pagination">
    <button :disabled="currentPage === 1" @click="$emit('update:currentPage', currentPage - 1)" class="page-btn">
      <i class="fas fa-chevron-left"></i>
    </button>
    <span class="page-info">{{ currentPage }} dari {{ totalPages }}</span>
    <button :disabled="currentPage === totalPages" @click="$emit('update:currentPage', currentPage + 1)" class="page-btn">
      <i class="fas fa-chevron-right"></i>
    </button>
  </div>
</template>

<script setup>
defineProps({
  currentPage: Number,
  totalPages: Number
});

defineEmits(['update:currentPage']);
</script>

<style scoped>
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 24px;
  gap: 16px;
}

.page-btn {
  background-color: #f0f0f0;
  border: none;
  border-radius: 6px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #555;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  background-color: #e0e0e0;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 14px;
  color: #666;
}
</style>