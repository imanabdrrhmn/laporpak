<template>
  <div>
    <div v-if="loading && (!reportsToDisplay || reportsToDisplay.length === 0)" class="report-grid">
      <div v-for="n in 6" :key="`skeleton-${n}`" class="skeleton-wrapper">
        <div class="card skeleton-card">
          <div class="skeleton-type-indicator"></div>
          <div class="card-header skeleton-header"></div>
          <div class="card-body">
            <div class="skeleton-title"></div>
            <div class="skeleton-text"></div>
            <div class="skeleton-text"></div>
            <div class="skeleton-footer"></div>
          </div>
        </div>
      </div>
    </div>

    <div v-else-if="!loading && reportsToDisplay.length === 0" class="no-results">
      <div class="text-center">
        <i class="fas fa-folder-open fa-3x text-muted mb-3" aria-hidden="true"></i>
        <h5>Tidak Ada Laporan Ditemukan</h5>
        <p class="text-muted">
          Belum ada laporan yang sesuai dengan kriteria pencarian Anda.
        </p>
        <button
          v-if="showResetButtonInEmptyState" class="btn btn-outline-primary mt-2"
          @click="$emit('reset-filters')"
          aria-label="Reset semua filter"
        >
          <i class="fas fa-undo-alt me-1" aria-hidden="true"></i>
          Reset Filter
        </button>
      </div>
    </div>

    <transition-group v-else name="reports-fade" tag="div" class="report-grid">
      <div
        v-for="report in reportsToDisplay"
        :key="report.id"
        class="report-card-wrapper"
      >
        <ReportCard
          :report="report"
          @open-detail="$emit('open-detail', $event)"
          @open-flag="$emit('open-flag', $event)"
        />
      </div>
    </transition-group>
  </div>
</template>

<script>
import ReportCard from './ReportCard.vue';

export default {
  name: 'ReportList',
  components: {
    ReportCard,
  },
  props: {
    loading: Boolean,
    reportsToDisplay: {
      type: Array,
      default: () => []
    },
    showResetButtonInEmptyState: {
      type: Boolean,
      default: true
    }
  },
  emits: ['reset-filters', 'open-detail', 'open-flag'],
};
</script>

<style scoped>
.report-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
  padding: 16px;
  max-width: 1200px;
  margin: 0 auto;
}

.report-card-wrapper {
  display: flex;
  justify-content: center;
}

.skeleton-wrapper {
  display: flex;
  justify-content: center;
}

.skeleton-card {
  height: 550px;
  background: #f8f9fa;
  animation: pulse 1.5s infinite ease-in-out;
  width: 350px;
  border-radius: 16px;
  overflow: hidden;
}

.skeleton-header {
  height: 220px;
  width: 100%;
  margin-bottom: 0;
}

.card-body {
  padding: 16px;
}

.skeleton-title {
  height: 24px;
  width: 70%;
  margin-top: 12px;
  margin-bottom: 12px;
}

.skeleton-text {
  height: 16px;
  width: 90%;
  margin-bottom: 8px;
}

.skeleton-text:last-of-type {
  width: 60%;
}

.skeleton-footer {
  height: 38px;
  width: 60%;
  margin-top: 20px;
}

@keyframes pulse {
  0%, 100% {
    opacity: 1;
  }
  50% {
    opacity: 0.6;
  }
}

.no-results {
  text-align: center;
  padding: 50px 20px;
  margin-top: 20px;
  color: #6c757d;
}

.no-results .fa-folder-open {
  font-size: 3.5rem;
  margin-bottom: 1rem;
  color: #adb5bd;
}

.no-results h5 {
  font-size: 1.25rem;
  color: #495057;
  margin-bottom: 0.5rem;
}

.no-results p {
  font-size: 0.95rem;
  margin-bottom: 1.25rem;
}

.btn-outline-primary {
  color: #0d6efd;
  border-color: #0d6efd;
  font-weight: 500;
}

.btn-outline-primary:hover {
  color: #fff;
  background-color: #0d6efd;
  border-color: #0d6efd;
}

.reports-fade-enter-active,
.reports-fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}

.reports-fade-enter-from,
.reports-fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

@media (max-width: 768px) {
  .report-grid {
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 15px;
    padding: 10px;
  }
}
</style>
