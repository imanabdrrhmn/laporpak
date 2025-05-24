<!-- resources/js/Components/Pelaporan/FilterSection.vue -->
<template>
  <div class="filter-section">
    <div class="section-header">
      <h2>Laporan Masuk</h2>
      <div class="view-toggle">
        <button
          @click="$emit('update:viewMode', 'grid')"
          :class="['toggle-btn', viewMode === 'grid' ? 'active' : '']"
          title="Grid View"
        >
          <i class="fas fa-th-large"></i>
        </button>
        <button
          @click="$emit('update:viewMode', 'list')"
          :class="['toggle-btn', viewMode === 'list' ? 'active' : '']"
          title="List View"
        >
          <i class="fas fa-list"></i>
        </button>
      </div>
    </div>

    <div class="search-bar">
      <div class="search-wrapper">
        <i class="fas fa-search search-icon"></i>
        <input
          :value="searchQuery"
          @input="$emit('update:searchQuery', $event.target.value)"
          type="text"
          class="search-input"
          placeholder="Cari laporan..."
        />
        <button v-if="searchQuery" @click="$emit('update:searchQuery', '')" class="clear-btn">
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>

    <div class="filters">
      <div class="dropdown-filter">
        <select 
          :value="selectedCategory" 
          @change="$emit('update:selectedCategory', $event.target.value)"
          class="filter-select"
        >
          <option value="">Semua Kategori</option>
          <option v-for="category in categories" :key="category" :value="category">
            {{ category }}
          </option>
        </select>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </div>

      <div class="dropdown-filter">
        <select 
          :value="selectedService"
          @change="$emit('update:selectedService', $event.target.value)"
          class="filter-select"
        >
          <option value="">Semua Layanan</option>
          <option v-for="service in services" :key="service" :value="service">
            {{ service }}
          </option>
        </select>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </div>

      <div class="dropdown-filter">
        <select 
          :value="selectedStatus"
          @change="$emit('update:selectedStatus', $event.target.value)"
          class="filter-select"
        >
          <option value="">Semua Status</option>
          <option value="pending">Pending</option>
          <option value="approved">Approved</option>
          <option value="rejected">Rejected</option>
          <option value="published">Published</option>
        </select>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </div>

      <div class="dropdown-filter">
        <select 
          :value="sortDirection"
          @change="$emit('update:sortDirection', $event.target.value)"
          class="filter-select"
        >
          <option value="desc">Terbaru</option>
          <option value="asc">Terlama</option>
        </select>
        <i class="fas fa-chevron-down dropdown-icon"></i>
      </div>

      <button @click="$emit('resetFilters')" class="reset-btn" :disabled="!hasActiveFilters">
        <i class="fas fa-sync-alt"></i> Reset
      </button>
    </div>

    <div v-if="hasActiveFilters" class="active-filters">
      <div class="filter-chips">
        <div v-if="selectedCategory" class="filter-chip">
          Kategori: {{ selectedCategory }}
          <button @click="$emit('update:selectedCategory', '')" class="remove-filter">×</button>
        </div>
        <div v-if="selectedService" class="filter-chip">
          Layanan: {{ selectedService }}
          <button @click="$emit('update:selectedService', '')" class="remove-filter">×</button>
        </div>
        <div v-if="selectedStatus" class="filter-chip">
          Status: {{ capitalize(selectedStatus) }}
          <button @click="$emit('update:selectedStatus', '')" class="remove-filter">×</button>
        </div>
        <div v-if="searchQuery" class="filter-chip">
          Pencarian: "{{ searchQuery }}"
          <button @click="$emit('update:searchQuery', '')" class="remove-filter">×</button>
        </div>
      </div>
      <p class="results-count">{{ filteredReports.length }} laporan ditemukan</p>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  searchQuery: String,
  selectedCategory: String,
  selectedService: String,
  selectedStatus: String,
  sortDirection: String,
  viewMode: String,
  categories: Array,
  services: Array,
  hasActiveFilters: Boolean,
  filteredReports: Array
});

const emit = defineEmits([
  'update:searchQuery',
  'update:selectedCategory',
  'update:selectedService',
  'update:selectedStatus',
  'update:sortDirection',
  'update:viewMode',
  'resetFilters'
]);

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);
</script>

<style scoped>
.filter-section {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-header h2 {
  font-size: 1.4rem;
  font-weight: 600;
  color: #333;
}

.view-toggle {
  display: flex;
  gap: 8px;
}

.toggle-btn {
  background-color: #e9ecef;
  border: none;
  border-radius: 6px;
  padding: 8px 12px;
  cursor: pointer;
  color: #4b6cb7;
  transition: all 0.2s;
}

.toggle-btn.active {
  background-color: #4b6cb7;
  color: white;
}

.search-bar {
  margin-bottom: 16px;
}

.search-wrapper {
  position: relative;
  width: 100%;
}

.search-input {
  width: 100%;
  padding: 12px 40px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  font-size: 16px;
  background-color: #f9f9f9;
  transition: all 0.3s ease;
  color: #000;
}

.search-input:focus {
  outline: none;
  border-color: #4b6cb7;
  background-color: #fff;
  box-shadow: 0 0 0 3px rgba(75, 108, 183, 0.1);
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #4b6cb7;
}

.clear-btn {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #4b6cb7;
  cursor: pointer;
  font-size: 14px;
}

.filters {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.dropdown-filter {
  position: relative;
  flex: 1;
  min-width: 160px;
}

.filter-select {
  width: 100%;
  padding: 12px 36px 12px 16px;
  font-size: 14px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  background-color: #f9f9f9;
  color: #333;
  appearance: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #4b6cb7;
  background-color: #fff;
  box-shadow: 0 0 0 3px rgba(75, 108, 183, 0.1);
}

.dropdown-icon {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #4b6cb7;
  pointer-events: none;
}

.reset-btn {
  padding: 12px 20px;
  background-color: #e9ecef;
  color: #4b6cb7;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.reset-btn:hover:not(:disabled) {
  background-color: #4b6cb7;
  color: #fff;
}

.reset-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.active-filters {
  background-color: #f9f9f9;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.filter-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 8px;
}

.filter-chip {
  background-color: #e9ecef;
  border-radius: 30px;
  padding: 6px 14px;
  font-size: 13px;
  color: #4b6cb7;
  display: flex;
  align-items: center;
  gap: 8px;
}

.remove-filter {
  background: none;
  border: none;
  color: #4b6cb7;
  font-size: 16px;
  cursor: pointer;
  line-height: 1;
  padding: 0;
}

.results-count {
  font-size: 14px;
  color: #4b6cb7;
  margin: 0;
}

@media (max-width: 768px) {
  .filters {
    flex-direction: column;
  }
  
  .dropdown-filter {
    width: 100%;
  }
}
</style>