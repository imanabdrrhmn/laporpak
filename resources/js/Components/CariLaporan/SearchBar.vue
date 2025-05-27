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
            placeholder="Cari nomor telepon, email, alamat, atau deskripsi..."
            :value="searchQuery"
            @input="$emit('update:searchQuery', $event.target.value)"
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
          <div class="col-sm-6">
            <select
              class="form-select"
              :value="categoryFilter"
              @change="$emit('update:categoryFilter', $event.target.value)"
              aria-label="Filter kategori laporan"
            >
              <option value="all">Semua Kategori</option>
              <option value="Nomor Hp">Nomor Hp</option>
              <option value="Email">Email</option>
              <option value="Infrastruktur">Infrastruktur</option>
            </select>
          </div>
          <div class="col-sm-6">
            <select
              class="form-select"
              :value="sortOrder"
              @change="$emit('update:sortOrder', $event.target.value)"
              aria-label="Urutkan laporan"
            >
              <option value="newest">Terbaru</option>
              <option value="oldest">Terlama</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
      <div class="active-filters" v-if="hasActiveFilters">
        <span class="me-2">Filter aktif:</span>
        <span v-if="searchQuery" class="badge bg-light text-dark me-1 active-filter">
          "{{ searchQuery }}"
          <button @click="$emit('clear-search')" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter pencarian"></button>
        </span>
        <span v-if="categoryFilter !== 'all'" class="badge bg-light text-dark me-1 active-filter">
          {{ categoryFilter }}
          <button @click="$emit('clear-category')" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter kategori"></button>
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

<script>
export default {
  name: 'SearchBar',
  props: {
    searchQuery: String,
    categoryFilter: String,
    sortOrder: String,
    searchError: String,
    loading: Boolean,
    hasActiveFilters: Boolean,
  },
  emits: [
    'update:searchQuery',
    'update:categoryFilter', 
    'update:sortOrder',
    'search',
    'debounced-search',
    'clear-search',
    'clear-category',
    'reset-filters'
  ],
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