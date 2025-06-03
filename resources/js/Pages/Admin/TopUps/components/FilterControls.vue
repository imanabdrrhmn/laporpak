<template>
    <div class="card shadow-sm mb-4">
      <div class="card-body">
        <div class="filter-controls">
          <div class="filter-item">
            <label class="form-label text-muted small mb-1">Status Filter</label>
            <select v-model="filters.status" @change="emit('update-filters')" class="form-select form-select-sm">
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
                v-model="filters.search"
                @keyup.enter="emit('update-filters')"
                type="search"
                placeholder="Cari user (nama/email)..."
                class="form-control form-control-sm"
              />
              <button @click="emit('update-filters')" class="btn btn-primary btn-sm">
                <i class="fas fa-search me-1"></i> Cari
              </button>
            </div>
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
  import { defineProps, defineEmits } from "vue";
  
  defineProps({
    filters: Object,
  });
  
  const emit = defineEmits(["update-filters", "show-export-modal"]);
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
  </style>