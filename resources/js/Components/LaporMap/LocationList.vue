<template>
  <div class="card mb-4 shadow">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
      <h5 class="mb-0"><i class="bi bi-pin-map-fill me-2"></i>Lokasi Pelaporan</h5>
      <span class="badge bg-light text-dark">{{ locationItems.length }}</span>
    </div>
    <div class="card-body p-0">
      <div class="list-group list-group-flush location-list">
        <a
          href="#"
          v-for="(location, index) in locationItems"
          :key="index"
          class="list-group-item list-group-item-action"
          @click.prevent="$emit('focus-location', location)"
        >
          <div class="d-flex w-100 justify-content-between">
            <div>
              <span class="location-marker" :class="'marker-' + location.type"></span>
              <h6 class="mb-1">{{ location.name }}</h6>
            </div>
            <small class="text-muted">{{ location.reports }} laporan</small>
          </div>
          <small class="d-block text-muted mb-1">
            <i class="bi bi-calendar3 me-1"></i> Terbaru: {{ location.lastReport }}
          </small>
          <div class="progress" style="height: 5px;">
            <div
              class="progress-bar"
              :class="'bg-' + getProgressColor(location.type)"
              role="progressbar"
              :style="{ width: (location.reports / maxReports * 100) + '%' }"
              :aria-valuenow="location.reports"
              aria-valuemin="0"
              :aria-valuemax="maxReports"
            ></div>
          </div>
        </a>
      </div>
    </div>
    <div class="card-footer bg-light">
      <button class="btn btn-sm btn-primary w-100" @click="$emit('show-all-locations')">
        <i class="bi bi-eye me-1"></i> Tampilkan Semua Lokasi
      </button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  locationItems: Array,
  maxReports: Number
});

defineEmits(['focus-location', 'show-all-locations']);

const getProgressColor = (type) => {
  const colors = { high: 'danger', medium: 'warning', low: 'success' };
  return colors[type] || 'primary';
};
</script>

<style scoped>
.location-list {
  max-height: 250px;
  overflow-y: auto;
}

.location-marker {
  display: inline-block;
  width: 10px;
  height: 10px;
  border-radius: 50%;
  margin-right: 6px;
}

.marker-high { background-color: #dc3545; }
.marker-medium { background-color: #ffc107; }
.marker-low { background-color: #28a745; }

.location-list::-webkit-scrollbar { width: 6px; }
.location-list::-webkit-scrollbar-track { background: #f1f1f1; }
.location-list::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
.location-list::-webkit-scrollbar-thumb:hover { background: #555; }

@media (max-width: 768px) {
  .location-list {
    max-height: 200px;
  }
}

@media (max-width: 576px) {
  .location-list {
    max-height: 180px;
  }
  .btn-sm {
    font-size: 0.8rem;
    padding: 5px 10px;
  }
}
</style>