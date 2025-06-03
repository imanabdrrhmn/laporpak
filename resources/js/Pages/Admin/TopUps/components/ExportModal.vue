<template>
    <div
      class="modal fade"
      id="exportModal"
      tabindex="-1"
      aria-labelledby="exportModalLabel"
      aria-hidden="true"
      ref="modalRef"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exportModalLabel">
              <i class="fas fa-file-export me-2"></i> Ekspor Data
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
              @click="emit('close-export-modal')"
            ></button>
          </div>
          <div class="modal-body">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="startDate" class="form-label">
                  <i class="fas fa-calendar-alt me-1"></i>Tanggal Mulai
                </label>
                <div class="date-input-wrapper">
                  <input
                    type="date"
                    id="startDate"
                    v-model="exportFilters.start_date"
                    class="form-control date-picker"
                    :max="exportFilters.end_date || getCurrentDate()"
                  />
                  <div class="date-icon">
                    <i class="fas fa-calendar-alt"></i>
                  </div>
                </div>
                <small class="text-muted">Pilih tanggal mulai periode</small>
              </div>
              <div class="col-md-6 mb-3">
                <label for="endDate" class="form-label">
                  <i class="fas fa-calendar-alt me-1"></i>Tanggal Akhir
                </label>
                <div class="date-input-wrapper">
                  <input
                    type="date"
                    id="endDate"
                    v-model="exportFilters.end_date"
                    class="form-control date-picker"
                    :min="exportFilters.start_date"
                    :max="getCurrentDate()"
                  />
                  <div class="date-icon">
                    <i class="fas fa-calendar-alt"></i>
                  </div>
                </div>
                <small class="text-muted">Pilih tanggal akhir periode</small>
              </div>
            </div>
            
            <div class="mb-3">
              <label class="form-label">
                <i class="fas fa-clock me-1"></i>Periode Cepat
              </label>
              <div class="quick-date-buttons">
                <button
                  type="button"
                  class="btn btn-outline-primary btn-sm"
                  @click="emit('set-quick-date-range', 'today')"
                >
                  Hari Ini
                </button>
                <button
                  type="button"
                  class="btn btn-outline-primary btn-sm"
                  @click="emit('set-quick-date-range', 'yesterday')"
                >
                  Kemarin
                </button>
                <button
                  type="button"
                  class="btn btn-outline-primary btn-sm"
                  @click="emit('set-quick-date-range', 'week')"
                >
                  7 Hari Terakhir
                </button>
                <button
                  type="button"
                  class="btn btn-outline-primary btn-sm"
                  @click="emit('set-quick-date-range', 'month')"
                >
                  30 Hari Terakhir
                </button>
              </div>
            </div>
  
            <div v-if="exportFilters.start_date || exportFilters.end_date" class="alert alert-info">
              <i class="fas fa-info-circle me-2"></i>
              <strong>Periode yang dipilih:</strong>
              <br>
              <span v-if="exportFilters.start_date">
                Dari: {{ formatDatePreview(exportFilters.start_date) }}
              </span>
              <span v-if="exportFilters.start_date && exportFilters.end_date"> - </span>
              <span v-if="exportFilters.end_date">
                Sampai: {{ formatDatePreview(exportFilters.end_date) }}
              </span>
              <span v-if="!exportFilters.start_date && !exportFilters.end_date">
                Semua data
              </span>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" @click="emit('close-export-modal')">
              <i class="fas fa-times me-1"></i> Batal
            </button>
            <button type="button" class="btn btn-outline-warning" @click="emit('clear-date-filters')">
              <i class="fas fa-eraser me-1"></i> Reset
            </button>
            <button type="button" class="btn btn-success" @click="emit('export-logs')">
              <i class="fas fa-download me-1"></i> Unduh CSV
            </button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script setup>
  import { defineProps, defineEmits } from "vue";
  
  defineProps({
    exportFilters: Object,
    getCurrentDate: Function,
    formatDatePreview: Function,
  });
  
  const emit = defineEmits(["set-quick-date-range", "clear-date-filters", "export-logs", "close-export-modal"]);
  </script>
  
  <style scoped>
  .date-input-wrapper {
    position: relative;
  }
  
  .date-picker {
    padding-right: 40px;
    border: 2px solid #e9ecef;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  }
  
  .date-picker:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
  }
  
  .date-icon {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    pointer-events: none;
    z-index: 1;
  }
  
  .quick-date-buttons {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
  }
  
  .quick-date-buttons .btn {
    border-radius: 20px;
    padding: 0.375rem 0.75rem;
    font-size: 0.875rem;
    transition: all 0.2s ease-in-out;
  }
  
  .quick-date-buttons .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  }
  
  .modal-content {
    border: none;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    background-color: #0d6efd;
  }
  
  .modal-header {
    background-color: #0d6efd;
    color: white;
    border-radius: 15px 15px 0 0;
    border-bottom: none;
  }
  
  .modal-header .btn-close {
    filter: invert(1);
  }
  
  .modal-body {
    padding: 2rem;
    background-color: white;
    border-radius: 0 0 0 0;
  }
  
  .form-label {
    font-weight: 600;
    color: #495057;
    margin-bottom: 0.5rem;
  }
  
  .modal-footer {
    background-color: white;
    border-top: 1px solid #e9ecef;
    border-radius: 0;
  }
  
  .alert-info {
    background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
    border: 1px solid #b3e5fc;
    border-radius: 10px;
    padding: 1rem;
  }
  
  .alert-info i {
    color: #0288d1;
  }
  
  @media (max-width: 576px) {
    .modal-body {
      padding: 1rem;
    }
  
    .quick-date-buttons {
      justify-content: center;
    }
  
    .quick-date-buttons .btn {
      flex: 1;
      min-width: 80px;
    }
  }
  </style>