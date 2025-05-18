<template>
  <div class="filter-bar">
    <div class="row g-3 align-items-center">
      <div class="col-lg-6">
        <div class="input-group">
          <span class="input-group-text bg-white border-end-0" aria-hidden="true">
            <i class="fas fa-search text-muted"></i>
          </span>
          <input
            type="text"
            class="form-control border-start-0"
            placeholder="Cari nomor telepon, email, atau website..."
            :value="searchQuery"
            @input="$emit('update:search-query', $event.target.value); $emit('search')"
            @keyup.enter="$emit('search')"
            aria-label="Pencarian laporan"
          >
          <button
            class="btn btn-primary"
            @click="$emit('search')"
            :disabled="loading"
            aria-label="Cari laporan"
          >
            <i class="fas fa-search me-1 d-none d-sm-inline" aria-hidden="true"></i>
            Cari
          </button>
        </div>
        <div v-if="searchError" class="text-danger mt-1 small" role="alert">
          {{ searchError }}
        </div>
      </div>
      <div class="col-lg-6">
        <div class="row g-2">
          <div class="col-sm-4">
            <select
              class="form-select"
              :value="typeFilter"
              @change="$emit('update:type-filter', $event.target.value)"
              aria-label="Filter jenis laporan"
            >
              <option value="all">Semua Jenis</option>
              <option value="Telepon">Telepon</option>
              <option value="Email">Email</option>
              <option value="Website">Website</option>
              <option value="WhatsApp">WhatsApp</option>
            </select>
          </div>
          <div class="col-sm-4">
            <select
              class="form-select"
              :value="statusFilter"
              @change="$emit('update:status-filter', $event.target.value)"
              aria-label="Filter status laporan"
            >
              <option value="all">Semua Status</option>
              <option value="process">Diproses</option>
              <option value="completed">Bersih</option>
              <option value="rejected">Berbahaya</option>
            </select>
          </div>
          <div class="col-sm-4">
            <select
              class="form-select"
              :value="sortOrder"
              @change="$emit('update:sort-order', $event.target.value)"
              aria-label="Urutkan laporan"
            >
              <option value="newest">Terbaru</option>
              <option value="oldest">Terlama</option>
              <option value="most_reports">Terbanyak Dilaporkan</option>
              <option value="highest_rating">Rating Tertinggi</option>
            </select>
          </div>
        </div>
      </div>
    </div>
    
    <div class="d-flex justify-content-between align-items-center mt-3">
      <div class="active-filters" v-if="hasActiveFilters">
        <span class="me-2">Filter aktif:</span>
        <span 
          v-if="searchQuery" 
          class="badge bg-light text-dark me-1 active-filter"
        >
          "{{ searchQuery }}"
          <button @click="$emit('clear-search')" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter pencarian"></button>
        </span>
        <span 
          v-if="typeFilter !== 'all'" 
          class="badge bg-light text-dark me-1 active-filter"
        >
          {{ typeFilter }}
          <button @click="$emit('update:type-filter', 'all')" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter jenis"></button>
        </span>
        <span 
          v-if="statusFilter !== 'all'" 
          class="badge bg-light text-dark me-1 active-filter"
        >
          {{ getFilterStatusLabel(statusFilter) }}
          <button @click="$emit('update:status-filter', 'all')" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter status"></button>
        </span>
      </div>
      <div>
        <button
          class="btn btn-outline-secondary btn-sm"
          @click="$emit('reset-filters')"
          aria-label="Reset filter"
          :disabled="!hasActiveFilters"
        >
          <i class="fas fa-undo-alt me-1" aria-hidden="true"></i>
          Reset Filter
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  searchQuery: String,
  typeFilter: String,
  statusFilter: String,
  sortOrder: String,
  loading: Boolean,
  searchError: String
});

defineEmits([
  'update:search-query',
  'update:type-filter',
  'update:status-filter',
  'update:sort-order',
  'search',
  'clear-search',
  'reset-filters'
]);

const FILTER_STATUS_LABELS = {
  process: 'Diproses',
  completed: 'Bersih',
  rejected: 'Berbahaya'
};

const hasActiveFilters = computed(() => {
  return props.searchQuery || props.typeFilter !== 'all' || props.statusFilter !== 'all';
});

const getFilterStatusLabel = (status) => {
  return FILTER_STATUS_LABELS[status] || 'Semua';
};
</script>

<style scoped>
.filter-bar {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.active-filter .btn-close-sm {
  font-size: 0.6rem;
  vertical-align: middle;
}
</style>