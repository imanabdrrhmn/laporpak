<template>
  <div class="modal fade" :class="{ 'show': showModal }" tabindex="-1"
       :style="{ display: showModal ? 'block' : 'none' }" data-bs-backdrop="static">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">
            <i class="bi bi-geo-alt-fill text-danger me-2"></i>
            {{ selectedLocation ? selectedLocation.name : 'Detail Lokasi' }}
          </h5>
          <button type="button" class="btn-close" @click="$emit('close')"></button>
        </div>
        <div class="modal-body" v-if="selectedLocation">
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <h6 class="text-muted">Alamat Lengkap</h6>
                <p>{{ selectedLocation.address }}</p>
              </div>
              <div class="mb-3">
                <h6 class="text-muted">Jenis Penipuan</h6>
                <div class="d-flex flex-wrap">
                  <span v-for="(type, i) in selectedLocation.fraudTypes" :key="i"
                        class="badge bg-secondary me-1 mb-1">{{ type }}</span>
                </div>
              </div>
              <div class="mb-3">
                <h6 class="text-muted">Total Korban</h6>
                <p>{{ selectedLocation.victims }} orang</p>
              </div>
              <div class="mb-3">
                <h6 class="text-muted">Estimasi Kerugian</h6>
                <p>Rp {{ formatNumber(selectedLocation.totalLoss) }}</p>
              </div>
            </div>
            <div class="col-md-6">
              <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <strong>Status:</strong> {{ selectedLocation.status }}
              </div>
            </div>
          </div>
          <hr />
          <h6 class="mb-3">Laporan Terbaru</h6>
          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Jenis</th>
                  <th>Pelapor</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(report, i) in selectedLocation.recentReports" :key="i">
                  <td>{{ report.date }}</td>
                  <td>{{ report.type }}</td>
                  <td>{{ report.reporter }}</td>
                  <td>
                    <span class="badge" :class="'bg-' + getStatusColor(report.status)">
                      {{ report.status }}
                    </span>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" @click="$emit('close')">Tutup</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  showModal: Boolean,
  selectedLocation: Object
});

defineEmits(['close']);

const getStatusColor = (status) => {
  const colors = { 'Diproses': 'warning', 'Selesai': 'success', 'Diverifikasi': 'info' };
  return colors[status] || 'secondary';
};

const formatNumber = (num) => {
  return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};
</script>

<style scoped>
.modal-dialog {
  max-width: 90%;
  margin: 1rem auto;
}

.modal-content {
  border-radius: 10px;
}

.modal-body {
  max-height: 60vh;
  overflow-y: auto;
}

@media (max-width: 768px) {
  .modal-dialog {
    max-width: 95%;
  }
  .modal-body {
    font-size: 0.9rem;
  }
}

@media (max-width: 576px) {
  .modal-body {
    max-height: 50vh;
    font-size: 0.85rem;
  }
  .table {
    font-size: 0.8rem;
  }
}
</style>