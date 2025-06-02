<template>
  <nav v-if="totalPages > 1" aria-label="Navigasi halaman laporan">
    <ul class="pagination justify-content-center flex-wrap gap-1">
      <li class="page-item" :class="{ disabled: currentPage === 1 }">
        <button
          class="page-link"
          @click="changePage(currentPage - 1)"
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
          @click="changePage(page)"
          :aria-current="page === currentPage ? 'page' : undefined"
          :aria-label="`Ke halaman ${page}`"
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
          @click="changePage(currentPage + 1)"
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

<script>
export default {
  name: 'Pagination',
  props: {
    currentPage: {
      type: Number,
      required: true,
    },
    totalPages: {
      type: Number,
      required: true,
    },
    visiblePageNumbers: {
      type: Array,
      required: true,
      default: () => [],
    },
    showStartEllipsis: {
      type: Boolean,
      default: false,
    },
    showEndEllipsis: {
      type: Boolean,
      default: false,
    },
  },
  emits: ['change-page'],
  methods: {
    changePage(page) {
      if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
        this.$emit('change-page', page);
      }
    },
  },
};
</script>

<style scoped>
.pagination {
  margin-top: 1.5rem;
  margin-bottom: 1rem;
}

.page-item .page-link {
  cursor: pointer;
  user-select: none;
  border-radius: 0.375rem;
  margin: 0 2px;
  color: #0d6efd;
  border-color: #dee2e6;
}

.page-item.active .page-link {
  z-index: 3;
  color: #fff;
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.page-item.disabled .page-link {
  color: #6c757d;
  pointer-events: none;
  background-color: #fff;
  border-color: #dee2e6;
}

.page-item .page-link:hover {
  z-index: 2;
  color: #0a58ca;
  background-color: #e9ecef;
  border-color: #dee2e6;
}

.page-item.active .page-link:hover {
  background-color: #0b5ed7;
  border-color: #0a58ca;
}

.page-item.disabled span.page-link {
  padding-left: 0.75rem;
  padding-right: 0.75rem;
}
</style>
