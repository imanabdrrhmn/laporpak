<template>
  <div class="pagination-container p-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
      <div class="pagination-info">
        Menampilkan {{ startIndex }}–{{ endIndex }} dari {{ totalItems }} ulasan
      </div>
      <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button
              class="page-link"
              @click="$emit('go-to-previous-page')"
              :disabled="currentPage === 1"
            >
              <i class="bi bi-chevron-left"></i>
            </button>
          </li>
          <li
            v-for="page in displayedPages"
            :key="page"
            class="page-item"
            :class="{ active: page === currentPage }"
          >
            <button
              class="page-link"
              @click="$emit('jump-to-page', { target: { value: page } })"
            >
              {{ page }}
            </button>
          </li>
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button
              class="page-link"
              @click="$emit('go-to-next-page')"
              :disabled="currentPage === totalPages"
            >
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
  currentPage: Number,
  totalPages: Number,
  startIndex: Number,
  endIndex: Number,
  totalItems: Number,
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
</script>

<style scoped>
.pagination-container {
  background: #fff;
  border-top: 1px solid #e5e7eb;
}

.pagination-info {
  color: #6b7280;
  font-size: 0.875rem;
}

.pagination {
  gap: 0.25rem;
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

@media (max-width: 767px) {
  .pagination-container {
    padding: 16px;
  }
}

@media (min-width: 768px) and (max-width: 1199px) {
  .pagination-container {
    padding: 20px;
  }
}
</style>