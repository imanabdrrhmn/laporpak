<template>
  <nav aria-label="Navigasi halaman laporan">
    <ul class="pagination justify-content-center">
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <button 
          class="page-link" 
          @click="$emit('change-page', currentPage - 1)" 
          :disabled="currentPage === 1"
          aria-label="Halaman sebelumnya"
        >
          <i class="fas fa-chevron-left" aria-hidden="true"></i>
          <span class="d-none d-sm-inline ms-1">Sebelumnya</span>
        </button>
      </li>
      
      <li v-if="showStartEllipsis" class="page-item disabled">
        <span class="page-link">...</span>
      </li>
      
      <li 
        v-for="page in visiblePageNumbers" 
        :key="page" 
        class="page-item"
        :class="{ active: page === currentPage }"
      >
        <button 
          class="page-link" 
          @click="$emit('change-page', page)"
          :aria-current="page === currentPage ? 'page' : undefined"
        >
          {{ page }}
        </button>
      </li>
      
      <li v-if="showEndEllipsis" class="page-item disabled">
        <span class="page-link">...</span>
      </li>
      
      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
        <button 
          class="page-link" 
          @click="$emit('change-page', currentPage + 1)" 
          :disabled="currentPage === totalPages"
          aria-label="Halaman selanjutnya"
        >
          <span class="d-none d-sm-inline me-1">Selanjutnya</span>
          <i class="fas fa-chevron-right" aria-hidden="true"></i>
        </button>
      </li>
    </ul>
  </nav>
</template>

<script setup>
defineProps({
  currentPage: Number,
  totalPages: Number,
  visiblePageNumbers: Array,
  showStartEllipsis: Boolean,
  showEndEllipsis: Boolean
});

defineEmits(['change-page']);
</script>

<style scoped>
.pagination .page-link {
  cursor: pointer;
}
</style>