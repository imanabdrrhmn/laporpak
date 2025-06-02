<template>
  <nav aria-label="Page navigation">
    <ul class="pagination pagination-sm mb-0">
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <a class="page-link" href="#" @click.prevent="goTo(currentPage - 1)">
          <span aria-hidden="true">«</span>
        </a>
      </li>

      <li v-if="showFirstPage" class="page-item">
        <a class="page-link" href="#" @click.prevent="goTo(1)">1</a>
      </li>
      <li v-if="showStartEllipsis" class="page-item disabled">
        <span class="page-link">...</span>
      </li>

      <li v-for="page in visiblePages" :key="page" class="page-item" :class="{ active: page === currentPage }">
        <a class="page-link" href="#" @click.prevent="goTo(page)">{{ page }}</a>
      </li>

      <li v-if="showEndEllipsis" class="page-item disabled">
        <span class="page-link">...</span>
      </li>

      <li v-if="showLastPage" class="page-item">
        <a class="page-link" href="#" @click.prevent="goTo(totalPages)">{{ totalPages }}</a>
      </li>

      <li class="page-item" :class="{ disabled: currentPage === totalPages }">
        <a class="page-link" href="#" @click.prevent="goTo(currentPage + 1)">
          <span aria-hidden="true">»</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  totalPages: Number,
  currentPage: Number,
});

const emit = defineEmits(['goToPage']);

const pagesToShow = 5;

const visiblePages = computed(() => {
  const pages = [];
  const startPage = Math.max(1, props.currentPage - Math.floor(pagesToShow / 2));
  const endPage = Math.min(props.totalPages, startPage + pagesToShow - 1);
  
  for (let i = startPage; i <= endPage; i++) {
    pages.push(i);
  }
  return pages;
});

const showFirstPage = computed(() => visiblePages.value[0] > 1);

const showLastPage = computed(() => visiblePages.value[visiblePages.value.length - 1] < props.totalPages);

const showStartEllipsis = computed(() => visiblePages.value[0] > 2);

const showEndEllipsis = computed(() => visiblePages.value[visiblePages.value.length - 1] < props.totalPages - 1);

function goTo(page) {
  if (page >= 1 && page <= props.totalPages) {
    emit('goToPage', page);
  }
}
</script>

<style scoped>
.pagination {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  align-items: center;
}

.page-item.disabled .page-link {
  cursor: not-allowed;
}

.page-item.disabled .page-link {
  cursor: not-allowed;
}
</style>
