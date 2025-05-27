<template>
  <div>
    <!-- Loading skeleton -->
    <div v-if="loading" class="row">
      <div v-for="n in 6" :key="n" class="col-md-6 col-lg-4">
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

    <!-- Tidak ada hasil -->
    <div v-if="!loading && filteredReports.length === 0" class="no-results">
      <div class="text-center">
        <i class="fas fa-search fa-3x text-muted mb-3" aria-hidden="true"></i>
        <h5>Tidak ada hasil ditemukan</h5>
        <p class="text-muted">
          Coba gunakan kata kunci yang berbeda atau reset filter pencarian.
        </p>
        <button
          class="btn btn-outline-primary mt-2"
          @click="$emit('reset-filters')"
          aria-label="Reset semua filter"
        >
          <i class="fas fa-undo-alt me-1" aria-hidden="true"></i>
          Reset Filter
        </button>
      </div>
    </div>

    <!-- Grid hasil laporan -->
    <transition-group name="reports-fade" tag="div" class="row">
      <div
        v-for="report in paginatedReports"
        :key="report.id"
        class="col-md-6 col-lg-4"
      >
        <ReportCard :report="report" @open-detail="$emit('open-detail', $event)" />
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
    filteredReports: Array,
    paginatedReports: Array,
  },
  emits: ['reset-filters', 'open-detail'],
};
</script>

<style scoped>
.skeleton-card {
  height: 200px;
  background: #f8f9fa;
  animation: pulse 1.5s infinite;
}

.skeleton-type-indicator,
.skeleton-header,
.skeleton-title,
.skeleton-text,
.skeleton-footer {
  background: #e9ecef;
  border-radius: 4px;
  margin-bottom: 10px;
}

.skeleton-type-indicator {
  width: 10px;
  height: 100%;
  position: absolute;
  left: 0;
}

.skeleton-header {
  height: 20px;
  width: 60%;
}

.skeleton-title {
  height: 24px;
  width: 80%;
}

.skeleton-text {
  height: 16px;
  width: 100%;
}

.skeleton-footer {
  height: 20px;
  width: 40%;
}

@keyframes pulse {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
  100% {
    opacity: 1;
  }
}

.no-results {
  text-align: center;
  padding: 40px 0;
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
</style>