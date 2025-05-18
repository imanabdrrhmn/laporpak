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

    <!-- No results message -->
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

    <!-- Results grid -->
    <transition-group name="reports-fade" tag="div" class="row">
      <div
        v-for="(report, index) in paginatedReports"
        :key="report.id || (report.contact + index)"
        class="col-md-6 col-lg-4"
      >
        <div 
          class="card report-card"
          :class="{ 'report-card--danger': report.status === 'danger' }"
        >
          <div :class="getTypeClass(report.type)" class="report-card__type-indicator"></div>
          <div class="card-header report-card__header">
            <div class="report-card__type">
              <i :class="getTypeIcon(report.type)" class="me-2" aria-hidden="true"></i>
              <span>{{ report.type }}</span>
            </div>
            <span
              :class="getStatusClass(report.status)"
              class="report-card__status-badge"
            >
              {{ getStatusLabel(report.status) }}
            </span>
          </div>
          <div class="card-body report-card__body">
            <h5 class="card-title report-card__title">{{ report.contact }}</h5>
            <p class="card-text report-card__description">{{ truncateText(report.description, 100) }}</p>

            <div class="report-card__rating mb-3">
              <div class="report-card__rating-stars" :aria-label="`Rating: ${report.rating} dari 5`">
                <i
                  v-for="i in 5"
                  :key="'star-' + i"
                  :class="getStarClass(report.rating, i)"
                  aria-hidden="true"
                ></i>
              </div>
              <span class="report-card__report-count">
                <i class="fas fa-flag me-1" aria-hidden="true"></i>
                {{ report.reportCount }} Laporan
              </span>
            </div>

            <div class="report-card__footer">
              <small class="text-muted">
                <i class="far fa-calendar-alt me-1" aria-hidden="true"></i>
                {{ report.date }}
              </small>
            </div>
          </div>
        </div>
      </div>
    </transition-group>
  </div>
</template>

<script setup>
defineProps({
  loading: Boolean,
  filteredReports: Array,
  paginatedReports: Array
});

defineEmits(['reset-filters']);

const STATUS_LABELS = {
  danger: 'Sangat Berbahaya',
  warning: 'Berbahaya',
  safe: 'Bersih'
};

const TYPE_CLASSES = {
  Telepon: 'report-card__type-indicator--phone',
  Email: 'report-card__type-indicator--email',
  Website: 'report-card__type-indicator--web',
  WhatsApp: 'report-card__type-indicator--whatsapp'
};

const TYPE_ICONS = {
  Telepon: 'fas fa-phone text-danger',
  Email: 'fas fa-envelope text-primary',
  Website: 'fas fa-globe text-warning',
  WhatsApp: 'fab fa-whatsapp text-success'
};

const STATUS_CLASSES = {
  danger: 'report-card__status-badge--danger',
  warning: 'report-card__status-badge--warning',
  safe: 'report-card__status-badge--safe'
};

const getTypeClass = (type) => {
  return TYPE_CLASSES[type] || '';
};

const getTypeIcon = (type) => {
  return TYPE_ICONS[type] || 'fas fa-question';
};

const getStatusClass = (status) => {
  return STATUS_CLASSES[status] || '';
};

const getStatusLabel = (status) => {
  return STATUS_LABELS[status] || 'Tidak Diketahui';
};

const getStarClass = (rating, index) => {
  if (rating >= index) {
    return 'fas fa-star text-warning';
  } else if (rating >= index - 0.5) {
    return 'fas fa-star-half-alt text-warning';
  }
  return 'far fa-star text-warning';
};

const truncateText = (text, length) => {
  if (text.length <= length) return text;
  return text.substring(0, length) + '...';
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
  0% { opacity: 1; }
  50% { opacity: 0.5; }
  100% { opacity: 1; }
}

.no-results {
  text-align: center;
  padding: 40px 0;
}

.report-card {
  position: relative;
  margin-bottom: 20px;
  transition: transform 0.2s;
}

.report-card:hover {
  transform: translateY(-5px);
}

.report-card--danger {
  border-color: #dc3545;
}

.report-card__type-indicator {
  width: 10px;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
}

.report-card__type-indicator--phone {
  background: #dc3545;
}

.report-card__type-indicator--email {
  background: #007bff;
}

.report-card__type-indicator--web {
  background: #ffc107;
}

.report-card__type-indicator--whatsapp {
  background: #28a745;
}

.report-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.report-card__status-badge {
  padding: 5px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
}

.report-card__status-badge--danger {
  background: #dc3545;
  color: white;
}

.report-card__status-badge--warning {
  background: #ffc107;
  color: #212529;
}

.report-card__status-badge--safe {
  background: #28a745;
  color: white;
}

.report-card__rating-stars { 
  font-size: 0.9rem;
}

.report-card__report-count {
  font-size: 0.85rem;
  color: #6c757d;
}

.report-card__footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
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