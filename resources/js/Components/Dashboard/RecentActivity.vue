<template>
  <div class="card main-card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="fw-bold mb-0">Aktivitas Terakhir</h5>
      <div class="dropdown">
        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="activityFilterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
          Filter
        </button>
        <ul class="dropdown-menu" aria-labelledby="activityFilterDropdown">
          <li><a class="dropdown-item" href="#">Semua</a></li>
          <li><a class="dropdown-item" href="#">Laporan</a></li>
          <li><a class="dropdown-item" href="#">Verifikasi</a></li>
          <li><a class="dropdown-item" href="#">Pembayaran</a></li>
        </ul>
      </div>
    </div>
    <div class="card-body">
      <ul class="activity-list">
        <li v-for="(aktivitas, index) in aktivitas" :key="index" class="activity-item animate__animated animate__fadeIn" :style="`animation-delay: ${0.1 * index}s`">
          <div class="activity-icon">{{ aktivitas.icon }}</div>
          <div class="activity-details">
            <div class="activity-text">
              {{ aktivitas.aksi }} <strong>{{ aktivitas.detail }}</strong>
            </div>
            <div class="activity-meta">
              <span class="activity-category">{{ aktivitas.kategori }}</span>
              <span class="activity-date">{{ aktivitas.tanggal }}</span>
            </div>
          </div>
        </li>
      </ul>
      <button class="btn btn-sm btn-light w-100 mt-3">Liat Lebih Banyak</button>
    </div>
  </div>
</template>

<script setup>
defineProps({
  aktivitas: Array
});
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

.activity-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.activity-item {
  display: flex;
  padding: 0.75rem 0;
  border-bottom: 1px solid rgba(0,0,0,0.05);
  transition: var(--transition);
}

.activity-item:last-child {
  border-bottom: none;
}

.activity-item:hover {
  transform: translateX(5px);
  background-color: rgba(0,0,0,0.01);
}

.activity-icon {
  font-size: 1.25rem;
  margin-right: 1rem;
  width: 30px;
  text-align: center;
}

.activity-details {
  flex: 1;
}

.activity-text {
  margin-bottom: 0.25rem;
  color: var(--text-color);
}

.activity-meta {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.activity-date {
  font-size: 0.8rem;
  color: var(--text-muted);
}

.activity-category {
  font-size: 0.75rem;
  padding: 0.1rem 0.5rem;
  background-color: rgba(67, 97, 238, 0.1);
  color: var(--primary-color);
  border-radius: 20px;
}
</style>