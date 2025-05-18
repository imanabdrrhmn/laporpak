<template>
  <div class="card main-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0">Laporan Terakhir</h5>
      <a href="/laporan-saya" class="text-primary small">Liat Semua</a>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table table-hover">
          <thead>
            <tr>
              <th>No</th>
              <th>Tanggal</th>
              <th>Kategori</th>
              <th>Sumber Penipuan</th>
              <th>Deskripsi</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(item, index) in laporanTerbaru" :key="index" class="animate__animated animate__fadeIn" :style="`animation-delay: ${0.1 * index}s`">
              <td>{{ index + 1 }}</td>
              <td>{{ item.tanggal }}</td>
              <td>{{ item.jenis }}</td>
              <td>{{ item.sumber }}</td>
              <td>{{ item.description }}</td>
              <td>
                <span :class="`badge bg-${item.status === 'Selesai' ? 'success' : 'warning'}`">
                  {{ item.status }}
                </span>
              </td>
              <td>
                <button class="btn btn-sm btn-link" @click="$emit('show-modal', item.id)" :id="`actionBtn-${index}`">
                  <i class="bi bi-three-dots"></i>
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
defineProps({
  laporanTerbaru: Array
});
defineEmits(['show-modal']);
</script>

<style scoped>
.main-card {
  border: none;
  border-radius: var(--border-radius);
  box-shadow: var(--box-shadow);
  height: 100%;
  transition: var(--transition);
  background-color: var(--card-bg);
}

.main-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.08);
}

.main-card .card-header {
  background: var(--card-bg);
  border-bottom: 1px solid rgba(0,0,0,0.05);
  border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
  padding: 1.25rem 1.5rem;
  display: flex;
  align-items: center;
}

.main-card .card-header h5 {
  color: var(--text-color);
}

.table {
  margin-bottom: 0;
  color: var(--text-color);
}

.table th {
  font-weight: 600;
  color: var(--text-color);
  border-top: none;
}

.table td, .table th {
  padding: 1rem 0.75rem;
  vertical-align: middle;
}

.table td {
  font-size: 0.875rem;
}

@media (max-width: 576px) {
  .table-responsive {
    font-size: 0.875rem;
  }
}
</style>