<template>
  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="filter-controls">
        <div class="filter-item">
          <label for="statusFilter" class="form-label text-muted small mb-1">Status Filter</label>
          <!-- Perubahan: Menggunakan v-model pada prop `modelValue` -->
          <select
            id="statusFilter"
            :value="modelValue.status"
            @change="updateFilter('status', $event.target.value)"
            class="form-select form-select-sm"
          >
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="verified">Verified</option>
            <option value="rejected">Rejected</option>
          </select>
        </div>

        <div class="filter-item flex-grow-1">
          <label for="searchFilter" class="form-label text-muted small mb-1">Cari User</label>
          <div class="input-group">
            <input
              id="searchFilter"
              :value="modelValue.search"
              @input="onSearchInput($event.target.value)"
              @keyup.enter="applyFilters"
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
          <label class="form-label text-muted small mb-1">&nbsp;</label>
          <button
            @click="resetFilters"
            class="btn btn-outline-secondary btn-sm w-100"
          >
            <i class="fas fa-undo-alt me-1"></i> Reset
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
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { debounce } from 'lodash';

// Perubahan: Prop `filters` diganti menjadi `modelValue` untuk mendukung v-model
const props = defineProps({
  modelValue: {
    type: Object,
    required: true,
    default: () => ({ status: '', search: '' })
  },
});

// Perubahan: Emit disesuaikan untuk v-model
const emit = defineEmits(["update:modelValue", "show-export-modal"]);

// Fungsi untuk mengupdate satu field filter dan meng-emit perubahan
const updateFilter = (key, value) => {
  emit('update:modelValue', {
    ...props.modelValue, // Salin nilai yang ada
    [key]: value // Update field yang berubah
  });
};

// Fungsi applyFilters sekarang tidak lagi diperlukan di sini karena
// watcher di parent akan menangani perubahan. Namun, bisa dipertahankan
// jika tombol "Cari" ingin melakukan fetch segera.
const applyFilters = () => {
    // Parent akan menangani fetch melalui watcher
    // Event ini bisa dihapus jika tidak ada aksi khusus saat klik "Cari"
};

const onSearchInput = debounce((value) => {
    updateFilter('search', value.trim());
}, 500); // Debounce 500ms

const resetFilters = () => {
  emit('update:modelValue', {
    status: '',
    search: ''
  });
};
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
.btn:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>