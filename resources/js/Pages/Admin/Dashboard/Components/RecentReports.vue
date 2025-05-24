<template>
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title mb-0">Ringkasan Laporan Terbaru</h5>
        <div class="d-flex">
          <button class="btn btn-sm btn-outline-secondary mr-2" @click="$emit('filter')">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>
          <button class="btn btn-sm btn-outline-secondary" @click="$emit('export')">
            <i class="bi bi-download me-1"></i> Ekspor
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-md-4 mb-3" v-for="report in reports.slice(0, 3)" :key="report.id">
          <div class="card h-100 border-0 shadow-sm report-card">
            <div class="card-body">
              <h6 class="card-subtitle mb-2 text-muted">{{ report.jenisPengaduan }}</h6>
              <p class="card-text"><strong>Pelapor:</strong> {{ report.pelapor }}</p>
              <p class="card-text">
                <strong>Status:</strong>
                <span :class="'badge badge-' + getStatusClass(report.status)">
                  {{ report.status }}
                </span>
              </p>
              <p class="card-text"><strong>Tanggal:</strong> {{ report.tanggal }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    reports: {
      type: Array,
      required: true,
    },
    getStatusClass: {
      type: Function,
      required: true,
    },
  },
};
</script>