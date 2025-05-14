<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const page = usePage();
page.layout = AppLayout;

// Sample data
const creditBalance = ref(125000);
const verificationStatus = ref({
  npwp: true,
  identity: true,
  phone: false
});
const reports = ref({
  total: 5,
  inProgress: 2,
  recent: [
    { id: 'PL1234', type: 'Penipuan Online', status: 'Diproses', date: '2 hari lalu' },
    { id: 'PL1233', type: 'Phishing', status: 'Selesai', date: '5 hari lalu' }
  ]
});
const activities = ref([
  { icon: '✅', action: 'cek NPWP', details: '', date: '1 hari lalu' },
  { icon: '⚠️', action: 'Melaporkan kasus penipuan', details: '#PL1234', date: '2 hari lalu' },
  { icon: '🔍', action: 'Cek blacklist terhadap', details: '089xxxxxxx', date: '3 hari lalu' },
  { icon: '🧾', action: 'Top up kredit sebesar', details: 'Rp 50.000', date: '5 hari lalu' }
]);
const quickActions = ref([
  { icon: '📝', label: 'Buat Laporan Baru', variant: 'primary' },
  { icon: '🔎', label: 'Cari Laporan', variant: 'info' },
  { icon: '💰', label: 'Top Up Kredit', variant: 'warning' },
  { icon: '📊', label: 'Riwayat Transaksi', variant: 'secondary' }
]);

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
};
</script>

<template>
  <Head title="Dashboard" />
  <AppLayout>
    <div class="dashboard-container">
      <!-- Welcome Header -->
      <div class="welcome-header mb-5">
        <div>
          <h1 class="fw-bold mb-2">Selamat datang kembali, User 👋</h1>
          <p class="text-muted">Apa yang ingin Anda lakukan hari ini?</p>
        </div>
        <div class="date-badge">
          <span class="day">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long' }) }}</span>
          <span class="date">{{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
        </div>
      </div>

      <!-- Summary Cards -->
      <div class="row g-4 mb-5">
        <!-- Credit Balance -->
        <div class="col-md-4">
          <div class="summary-card credit-balance">
            <div class="card-icon">
              <i class="bi bi-wallet2"></i>
            </div>
            <div>
              <h6 class="card-title">Saldo Kredit</h6>
              <h3 class="card-value">{{ formatCurrency(creditBalance) }}</h3>
              <button class="btn btn-sm btn-light mt-2">Top Up <i class="bi bi-plus-circle ms-1"></i></button>
            </div>
          </div>
        </div>

        <!-- Verification Status -->
        <div class="col-md-4">
          <div class="summary-card verification-status">
            <div class="card-icon">
              <i class="bi bi-shield-check"></i>
            </div>
            <div>
              <h6 class="card-title">Status Verifikasi</h6>
              <div class="verification-badges">
                <span class="badge bg-success me-2">NPWP</span>
                <span class="badge bg-success me-2">Identitas</span>
                <span class="badge bg-secondary">No. HP</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Reports Summary -->
        <div class="col-md-4">
          <div class="summary-card reports-summary">
            <div class="card-icon">
              <i class="bi bi-file-earmark-text"></i>
            </div>
            <div>
              <h6 class="card-title">Total Laporan</h6>
              <h3 class="card-value">{{ reports.total }}</h3>
              <p class="card-subtext">{{ reports.inProgress }} masih diproses</p>
            </div>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="row g-4 mb-4">
        <!-- Recent Activities -->
        <div class="col-lg-8">
          <div class="card main-card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="fw-bold mb-0">Aktivitas Terakhir</h5>
              <a href="#" class="text-primary small">Lihat Semua</a>
            </div>
            <div class="card-body">
              <ul class="activity-list">
                <li v-for="(activity, index) in activities" :key="index" class="activity-item">
                  <div class="activity-icon">{{ activity.icon }}</div>
                  <div class="activity-details">
                    <div class="activity-text">
                      {{ activity.action }} <strong>{{ activity.details }}</strong>
                    </div>
                    <div class="activity-date">{{ activity.date }}</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
          <div class="card main-card">
            <div class="card-header">
              <h5 class="fw-bold mb-0">Aksi Cepat</h5>
            </div>
            <div class="card-body">
              <div class="quick-actions-grid">
                <button 
                  v-for="(action, index) in quickActions" 
                  :key="index" 
                  :class="`btn btn-${action.variant} btn-action`"
                >
                  <span class="action-icon">{{ action.icon }}</span>
                  <span class="action-label">{{ action.label }}</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Reports & Testimonial -->
      <div class="row g-4">
        <!-- Recent Reports -->
        <div class="col-lg-8">
          <div class="card main-card">
            <div class="card-header d-flex justify-content-between align-items-center">
              <h5 class="fw-bold mb-0">Laporan Terakhir</h5>
              <a href="#" class="text-primary small">Lihat Semua</a>
            </div>
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-hover">
                  <thead>
                    <tr>
                      <th>ID Laporan</th>
                      <th>Jenis</th>
                      <th>Status</th>
                      <th>Tanggal</th>
                      <th></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(report, index) in reports.recent" :key="index">
                      <td>{{ report.id }}</td>
                      <td>{{ report.type }}</td>
                      <td>
                        <span :class="`badge bg-${report.status === 'Selesai' ? 'success' : 'warning'}`">
                          {{ report.status }}
                        </span>
                      </td>
                      <td>{{ report.date }}</td>
                      <td class="text-end">
                        <button class="btn btn-sm btn-outline-primary">Detail</button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>

        <!-- Testimonial -->
        <div class="col-lg-4">
          <div class="card main-card">
            <div class="card-header">
              <h5 class="fw-bold mb-0">Feedback Anda</h5>
            </div>
            <div class="card-body">
              <div class="testimonial">
                <div class="d-flex align-items-center mb-3">
                  <img src="/Default-Profile.png" alt="avatar" class="rounded-circle me-3" width="50" height="50">
                  <div>
                    <strong>User</strong><br>
                    <small class="text-muted">Kategori: Cari Laporan</small>
                  </div>
                </div>
                <div class="mb-2">
                  <span class="text-warning fs-5">★★★★★</span>
                </div>
                <p class="mb-0">"Sistem ini sangat membantu saya menemukan laporan penipuan yang valid. Proses verifikasi cepat dan akurat. Terima kasih!"</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.dashboard-container {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
}

.welcome-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.welcome-header h1 {
  font-size: 2rem;
  color: #2c3e50;
}

.date-badge {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 0.75rem 1rem;
  text-align: center;
  box-shadow: 0 2px 5px rgba(0,0,0,0.05);
}

.date-badge .day {
  display: block;
  font-weight: 600;
  color: #4361ee;
}

.date-badge .date {
  font-size: 0.85rem;
  color: #6c757d;
}

.summary-card {
  background: white;
  border-radius: 12px;
  padding: 1.5rem;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  display: flex;
  align-items: center;
  height: 100%;
  transition: transform 0.2s, box-shadow 0.2s;
}

.summary-card:hover {
  transform: translateY(-5px);
  box-shadow: 0 10px 15px rgba(0,0,0,0.1);
}

.card-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  font-size: 1.25rem;
}

.credit-balance .card-icon {
  background: rgba(67, 97, 238, 0.1);
  color: #4361ee;
}

.verification-status .card-icon {
  background: rgba(40, 167, 69, 0.1);
  color: #28a745;
}

.reports-summary .card-icon {
  background: rgba(220, 53, 69, 0.1);
  color: #dc3545;
}

.card-title {
  font-size: 0.9rem;
  color: #6c757d;
  margin-bottom: 0.5rem;
}

.card-value {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
}

.card-subtext {
  font-size: 0.85rem;
  color: #6c757d;
  margin-bottom: 0;
}

.verification-badges {
  margin-top: 0.5rem;
}

.main-card {
  border: none;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0,0,0,0.05);
  height: 100%;
}

.main-card .card-header {
  background: white;
  border-bottom: 1px solid #eee;
  border-radius: 12px 12px 0 0 !important;
  padding: 1.25rem 1.5rem;
}

.activity-list {
  list-style: none;
  padding: 0;
  margin: 0;
}

.activity-item {
  display: flex;
  padding: 0.75rem 0;
  border-bottom: 1px solid #f1f1f1;
}

.activity-item:last-child {
  border-bottom: none;
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
}

.activity-date {
  font-size: 0.8rem;
  color: #6c757d;
}

.quick-actions-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 0.75rem;
}

.btn-action {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  height: 100%;
  padding: 1rem 0.5rem;
  border-radius: 8px;
  text-align: center;
  white-space: normal;
}

.action-icon {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  display: block;
}

.action-label {
  font-size: 0.85rem;
}

.testimonial {
  background: #f8f9fa;
  border-radius: 10px;
  padding: 1.25rem;
}

.table {
  margin-bottom: 0;
}

.table th {
  font-weight: 600;
  color: #495057;
  border-top: none;
}

@media (max-width: 992px) {
  .welcome-header {
    flex-direction: column;
  }
  
  .date-badge {
    margin-top: 1rem;
    align-self: flex-start;
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: 1.5rem;
  }
  
  .quick-actions-grid {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 576px) {
  .summary-card {
    flex-direction: column;
    text-align: center;
  }
  
  .card-icon {
    margin-right: 0;
    margin-bottom: 1rem;
  }
}
</style>