<template>
  <div class="pagination-container">
    <div class="pagination-wrapper">
      <div class="pagination-info">
        
      </div>
      <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link" @click="goTo(currentPage - 1)" :disabled="currentPage === 1">
              <i class="bi bi-chevron-left"></i>
            </button>
          </li>
          <li v-for="page in displayedPages" :key="page" class="page-item" :class="{ active: page === currentPage }">
            <button class="page-link" @click="goTo(page)">{{ page }}</button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button class="page-link" @click="goTo(currentPage + 1)" :disabled="currentPage === totalPages">
              <i class="bi bi-chevron-right"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  totalPages: Number,
  currentPage: Number,
  total: Number,
  perPage: Number,
});

const emit = defineEmits(['goToPage']);

const from = computed(() => {
  return props.total ? (props.currentPage - 1) * props.perPage + 1 : 0;
});

const to = computed(() => {
  return Math.min(props.currentPage * props.perPage, props.total);
});

const displayedPages = computed(() => {
  const maxVisiblePages = 5;
  const halfVisible = Math.floor(maxVisiblePages / 2);
  let startPage = Math.max(props.currentPage - halfVisible, 1);
  let endPage = startPage + maxVisiblePages - 1;

  if (endPage > props.totalPages) {
    endPage = props.totalPages;
    startPage = Math.max(endPage - maxVisiblePages + 1, 1);
  }

  return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
});

function goTo(page) {
  if (page >= 1 && page <= props.totalPages) {
    emit('goToPage', page);
  }
}
</script>

<style scoped>
.pagination-container {
  background: #fff;
  border-top: 1px solid #e5e7eb;
  padding: 1rem;
}

.pagination-wrapper {
  display: flex;
  justify-content: space-between;
  align-items: center;
  gap: 1rem;
  flex-wrap: wrap;
}

.pagination-info {
  color: #6b7280;
  font-size: 0.875rem;
}

.pagination {
  gap: 0.25rem;
  flex-wrap: wrap;
}

.page-link {
  border: none;
  padding: 0.5rem 0.75rem;
  color: #374151;
  background: transparent;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  transition: all 0.2s ease;
  min-width: 2.5rem;
  text-align: center;
}

.page-link:hover:not(.disabled) {
  background: #f3f4f6;
  color: #1f2937;
}

.page-item.active .page-link {
  background: #2563eb;
  color: white;
}

.page-item.disabled .page-link {
  color: #9ca3af;
  pointer-events: none;
}

@media (max-width: 640px) {
  .pagination-wrapper {
    justify-content: center;
    text-align: center;
  }

  .pagination-info {
    width: 100%;
    font-size: 0.813rem;
  }

  .pagination {
    gap: 0.15rem;
  }

  .page-link {
    padding: 0.375rem 0.5rem;
    min-width: 2rem;
    font-size: 0.813rem;
  }
}

@media (max-width: 380px) {
  .pagination {
    gap: 0.1rem;
  }

  .page-link {
    padding: 0.25rem 0.375rem;
    min-width: 1.75rem;
    font-size: 0.75rem;
  }
}
</style>
