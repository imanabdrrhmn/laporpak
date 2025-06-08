<template>
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="filter-controls">
        <div class="filter-item">
          <label class="form-label text-muted small mb-1">Status Filter</label>
          <select 
            v-model="localFilters.status" 
            @change="applyFilters" 
            class="form-select form-select-sm"
          >
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>

        <div class="filter-item flex-grow-1">
          <label class="form-label text-muted small mb-1">Cari User</label>
          <div class="input-group">
            <input
              v-model="localFilters.search"
              @keyup.enter="applyFilters"
              @input="onSearchInput"
              type="search"
              placeholder="Cari user (nama/email)..."
              class="form-control form-control-sm"
            />
            <button @click="applyFilters" class="btn btn-primary btn-sm">
              <i class="fas fa-search me-1"></i> Cari
            </button>
          </div>
        </div>

        <div class="filter-item">
          <label class="form-label text-muted small mb-1">Reset Filter</label>
          <button
            @click="resetFilters"
            class="btn btn-outline-secondary btn-sm w-100"
          >
            <i class="fas fa-refresh me-1"></i> Reset
          </button>
        </div>

        <div class="filter-item">
          <label class="form-label text-muted small mb-1">Export Data</label>
          <button
            @click="emit('show-export-modal')"
            class="btn btn-outline-success btn-sm w-100"
          >
            <i class="fas fa-file-export me-1"></i> Export Logs
          </button>
        </div>
      </div>

      <!-- Hasil pencarian info -->
      <div v-if="searchInfo" class="mt-3">
        <small class="text-muted">
          <i class="fas fa-info-circle me-1"></i>
          {{ searchInfo }}
        </small>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineProps, defineEmits, ref, watch, computed } from "vue";

const props = defineProps({
  filters: {
    type: Object,
    default: () => ({})
  },
  totalData: {
    type: Number,
    default: 0
  },
  filteredCount: {
    type: Number,
    default: 0
  }
});

const emit = defineEmits(["update-filters", "show-export-modal"]);

// Local reactive filters
const localFilters = ref({
  status: props.filters?.status || '',
  search: props.filters?.search || ''
});

// Search info untuk menampilkan hasil pencarian
const searchInfo = computed(() => {
  if (localFilters.value.search || localFilters.value.status) {
    return `Menampilkan ${props.filteredCount} dari ${props.totalData} data`;
  }
  return '';
});

// Debounce untuk search input
let searchTimeout = null;
const onSearchInput = () => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    applyFilters();
  }, 500); // Delay 500ms setelah user berhenti mengetik
};

// Apply filters
const applyFilters = () => {
  emit('update-filters', {
    status: localFilters.value.status,
    search: localFilters.value.search.trim()
  });
};

// Reset filters
const resetFilters = () => {
  localFilters.value = {
    status: '',
    search: ''
  };
  applyFilters();
};

// Watch for external filter changes
watch(() => props.filters, (newFilters) => {
  if (newFilters) {
    localFilters.value = {
      status: newFilters.status || '',
      search: newFilters.search || ''
    };
  }
}, { deep: true });
</script>

<style scoped>
.filter-controls {
  display: flex;
  gap: 1rem;
  align-items: flex-end;
  flex-wrap: wrap;
}

.filter-item {
  min-width: 150px;
}

.flex-grow-1 {
  flex: 1;
}

@media (max-width: 992px) {
  .filter-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-item {
    width: 100%;
    min-width: auto;
  }
}

/* Loading state untuk button */
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}

/* Highlight search results */
.search-highlight {
  background-color: #fff3cd;
  padding: 0.1rem 0.2rem;
  border-radius: 0.2rem;
}
</style>