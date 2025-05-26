<!-- resources/js/Components/Pelaporan/ReportsContainer.vue -->
<template>
  <div v-if="reports.length" :class="['reports-container', viewMode]">
    <template v-if="viewMode === 'grid'">
      <div class="reports-grid">
        <ReportCard
          v-for="report in reports"
          :key="report.id"
          :report="report"
          :loading="loading[report.id]"
          :can="can"
          @view-report="$emit('viewReport', report)"
          @quick-action="$emit('quickAction', $event)"
        />
      </div>
    </template>
    <template v-else>
      <ReportsTable
        :reports="reports"
        :current-page="currentPage"
        :items-per-page="itemsPerPage"
        :loading="loading"
        @view-report="$emit('viewReport', $event)"
        @quick-action="$emit('quickAction', $event)"
      />
    </template>
  </div>
  <div v-else class="no-results">
    <div class="empty-state">
      <i class="fas fa-folder-open empty-icon"></i>
      <h3>Tidak ada laporan ditemukan</h3>
      <p>Coba ubah filter pencarian atau reset filter</p>
      <button @click="$emit('resetFilters')" class="reset-btn-empty">Reset Filter</button>
    </div>
  </div>
</template>

<script setup>
import ReportCard from './ReportCard.vue';
import ReportsTable from './ReportsTable.vue';

defineProps({
  reports: Array,
  viewMode: String,
  currentPage: Number,
  itemsPerPage: Number,
  loading: Object,
  can: Object
});

defineEmits(['viewReport', 'quickAction', 'resetFilters']);
</script>

<style scoped>
.reports-container {
  margin-bottom: 24px;
}

.reports-container.grid .reports-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

.no-results {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px 0;
}

.empty-state {
  text-align: center;
  color: #888;
  max-width: 400px;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.6;
}

.empty-state h3 {
  font-size: 18px;
  margin-bottom: 8px;
  color: #555;
}

.empty-state p {
  margin-bottom: 16px;
  font-size: 14px;
}

.reset-btn-empty {
  padding: 10px 20px;
  background-color: #0d6efd;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  margin: 0 auto;
}

@media (max-width: 768px) {
  .reports-container.grid .reports-grid {
    grid-template-columns: 1fr;
  }
}
</style>