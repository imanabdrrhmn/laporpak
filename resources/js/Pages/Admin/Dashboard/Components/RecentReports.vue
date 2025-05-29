<template>
  <div class="card border-0 shadow-sm mb-3">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="card-title mb-0">Ringkasan Laporan Terbaru</h5>
        <div class="d-flex flex-wrap gap-2">
          <button class="btn btn-sm btn-outline-secondary" @click="$emit('filter')">
            <i class="bi bi-funnel me-1"></i> Filter
          </button>
          <button class="btn btn-sm btn-outline-secondary" @click="$emit('export')">
            <i class="bi bi-download me-1"></i> Ekspor
          </button>
        </div>
      </div>
      <div class="row">
        <div class="col-lg-4 col-sm-6 col-12 mb-3" v-for="report in reports.slice(0, 3)" :key="report.id">
          <div class="card h-100 border-0 shadow-sm report-card">
            <div class="card-body">
              <h6 class="card-subtitle mb-2 text-muted">{{ report.service }}</h6>
              <p class="card-text"><strong>Pelapor:</strong> {{ report.user?.name }}</p>
              <p class="card-text">
                <strong>Status:</strong>
                <span :class="'badge badge-' + getStatusClass(report.status)">
                  {{ formatStatus(report.status) }}
                </span>
              </p>
              <p class="card-text"><strong>Tanggal:</strong> {{ formatDate(report.created_at) }}</p>
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
  },
  methods: {
    formatDate(dateString) {
      if (!dateString) return '-';
      const date = new Date(dateString);
      return date.toLocaleString('id-ID', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
        timeZone: 'Asia/Jakarta',
      });
    },
    formatStatus(status) {
      if (!status) return '-';
      return status
        .toLowerCase()
        .split(' ')
        .map(word => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
    },
    getStatusClass(status) {
      switch (status.toLowerCase()) {
        case 'pending': return 'warning';
        case 'in progress': return 'primary';
        case 'approved': return 'success';
        case 'rejected': return 'danger';
        case 'published': return 'info';
        default: return 'secondary';
    }
  }
  },
};
</script>

<style scoped>
.report-card {
  background-color: #fff;
  border-radius: 12px;
  transition: box-shadow 0.2s ease;
}

.report-card:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.card-text {
  font-size: 0.9rem;
  margin-bottom: 0.4rem;
}

.badge {
  font-size: 0.75rem;
  padding: 0.4em 0.6em;
  margin-left: 0.4em;
  border-radius: 0.5rem;
  text-transform: capitalize;
}

.badge-warning {
  background-color: #f6c23e;
  color: #212529;
}

.badge-primary {
  background-color: #4e73df;
}

.badge-success {
  background-color: #1cc88a;
}

.badge-danger {
  background-color: #e74a3b;
}

.badge-info {
  background-color: #e3f2fd;
  color: #1565c0;
}

.badge-secondary {
  background-color: #858796;
}

@media (max-width: 576px) {
  .report-card {
    margin-left: auto;
    margin-right: auto;
  }
}

@media (max-width: 768px) {
  .report-card {
    margin-left: auto;
    margin-right: auto;
  }
}

</style>
