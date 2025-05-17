<template>
  <Head title="Dashboard" />
  <AppLayout>
    <div class="dashboard-container">
      <!-- Header Selamat Datang -->
      <div class="welcome-header mb-5">
        <div class="animate__animated animate__fadeIn">
          <h1 class="fw-bold mb-2">Selamat datang kembali, {{ namaUser }} 👋</h1>
          <p class="text-muted">Mau ngapain hari ini?</p>
        </div>
        <div class="user-info-container animate__animated animate__fadeIn">
          <div class="level-badge">
            <i class="bi bi-award"></i>
            <span>{{ levelUser }}</span>
          </div>
          <div class="date-badge">
            <span class="day">{{ new Date().toLocaleDateString('id-ID', { weekday: 'long' }) }}</span>
            <span class="date">{{ new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) }}</span>
          </div>
        </div>
      </div>

      <!-- Kartu Ringkasan -->
      <div class="row g-4 mb-5">
        <!-- Saldo Kredit -->
        <div class="col-md-4 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
          <div class="summary-card credit-balance">
            <div class="card-content">
              <div class="card-icon">
                <i class="bi bi-wallet2"></i>
              </div>
              <div>
                <h6 class="card-title">Saldo Kredit</h6>
                <h3 class="card-value">{{ formatRupiah(saldoKredit) }}</h3>
                <button class="btn btn-sm btn-primary mt-2">Isi Saldo <i class="bi bi-plus-circle ms-1"></i></button>
              </div>
            </div>
            <div class="progress mt-3" style="height: 4px;">
              <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </div>
        </div>

        <!-- Status Verifikasi -->
        <div class="col-md-4 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
          <div class="summary-card verification-status">
            <div class="card-content">
              <div class="card-icon">
                <i class="bi bi-shield-check"></i>
              </div>
              <div>
                <h6 class="card-title">Status Verifikasi</h6>
                <div class="verification-progress">
                  <div class="progress-container">
                    <div class="progress">
                      <div class="progress-bar bg-success" role="progressbar" :style="`width: ${progresVerifikasi}%`" :aria-valuenow="progresVerifikasi" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                    <span>{{ progresVerifikasi }}%</span>
                  </div>
                </div>
                <div class="verification-badges">
                  <span class="badge" :class="statusVerifikasi.npwp ? 'bg-success' : 'bg-secondary'">NPWP</span>
                  <span class="badge" :class="statusVerifikasi.identitas ? 'bg-success' : 'bg-secondary'">Identitas</span>
                  <span class="badge" :class="statusVerifikasi.noHp ? 'bg-success' : 'bg-secondary'">No. HP</span>
                  <span class="badge" :class="statusVerifikasi.email ? 'bg-success' : 'bg-secondary'">Email</span>
                  <span class="badge" :class="statusVerifikasi.rekening ? 'bg-success' : 'bg-secondary'">Rekening</span>
                </div>
              </div>
            </div>
            <button class="btn btn-sm btn-outline-success w-100 mt-3" v-if="!statusVerifikasi.noHp">Verifikasi No. HP</button>
          </div>
        </div>

        <!-- Ringkasan Laporan -->
        <div class="col-md-4 col-sm-12 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
          <div class="summary-card reports-summary">
            <div class="card-content">
              <div class="card-icon">
                <i class="bi bi-file-earmark-text"></i>
              </div>
              <div>
                <h6 class="card-title">Total Laporan</h6>
                <h3 class="card-value">{{ laporan.total }}</h3>
                <div class="reports-stats">
                  <div class="stat-item">
                    <i class="bi bi-hourglass-split text-warning"></i>
                    <span>{{ laporan.diproses }} lagi diproses</span>
                  </div>
                  <div class="stat-item">
                    <i class="bi bi-check-circle-fill text-success"></i>
                    <span>{{ laporan.selesai }} udah selesai</span>
                  </div>
                </div>
              </div>
            </div>
            <button class="btn btn-sm btn-outline-primary w-100 mt-3">Liat Semua Laporan</button>
          </div>
        </div>
      </div>

      <!-- Progres Level -->
      <div class="level-progress-container mb-5 animate__animated animate__fadeIn" style="animation-delay: 0.4s">
        <div class="level-info">
          <div>
            <h6>Program Loyalitas</h6>
            <p class="mb-0">{{ progresLevel }}% menuju level Gold</p>
          </div>
          <button class="btn btn-sm btn-outline-warning">Liat Hadiah</button>
        </div>
        <div class="progress" style="height: 8px;">
          <div class="progress-bar bg-warning" role="progressbar" :style="`width: ${progresLevel}%`" :aria-valuenow="progresLevel" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
      </div>

      <!-- Konten Utama -->
      <div class="row g-4 mb-4">
        <!-- Aksi Cepat -->
        <div class="col-lg-3 col-md-4 animate__animated animate__fadeInLeft" style="animation-delay: 0.2s">
          <div class="card main-card">
            <div class="card-header">
              <h5 class="fw-bold mb-0">Aksi Cepat</h5>
            </div>
            <div class="card-body">
              <div class="quick-actions-grid">
                <a 
                  v-for="(aksi, index) in aksiCepat" 
                  :key="index" 
                  :href="aksi.url"
                  :class="`btn btn-${aksi.variant} btn-action`"
                >
                  <span class="action-icon">{{ aksi.icon }}</span>
                  <span class="action-label">{{ aksi.label }}</span>
                </a>
              </div>
            </div>
          </div>
        </div>

        <!-- Aktivitas Terakhir -->
        <div class="col-lg-5 col-md-8 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
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
        </div>

        <!-- Feedback Kamu -->
        <div class="col-lg-4 animate__animated animate__fadeInRight" style="animation-delay: 0.4s">
          <div class="card main-card">
            <div class="card-header">
              <h5 class="fw-bold mb-0">Ulasan Saya</h5>
            </div>
            <div class="card-body">
              <div class="testimonial">
                <div class="d-flex align-items-center mb-3">
                  <img :src="userAvatar" alt="avatar" class="rounded-circle me-3" width="50" height="50">
                  <div>
                    <strong>{{ namaUser }}</strong><br>
                    <small class="text-muted">Kategori: {{ feedbackKategori }}</small>
                  </div>
                </div>
                <div class="mb-2">
                  <span class="text-warning fs-5">★★★★★</span>
                </div>
                <p class="mb-0">{{ feedbackText }}</p>
              </div>
                <a
                  href="/feedback"
                  class="btn btn-sm btn-outline-primary w-100 mt-3"
                >
                  Kasih Ulasan
                </a>
            </div>
          </div>
        </div>
      </div>

      <!-- Tabel Laporan Terakhir -->
      <div class="row g-4 mt-2">
        <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: 0.7s">
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
                    <tr v-for="(laporan, index) in laporanTerbaru" :key="index" class="animate__animated animate__fadeIn" :style="`animation-delay: ${0.1 * index}s`">
                      <td>{{ index + 1 }}</td>
                      <td>{{ item.tanggal }}</td>
                      <td>{{ item.jenis }}</td>
                      <td>{{ item.sumber }}</td>
                      <td>{{ item.description }}</td>
                      <td>
                        <span :class="`badge bg-${item.status === 'Selesai' ? 'success' : 'warning'}`">
                          {{ laporan.status }}
                        </span>
                      </td>
                      <td>
                        <button class="btn btn-sm btn-link" @click="showModal(laporan.id)" :id="`actionBtn-${index}`">
                          <i class="bi bi-three-dots"></i>
                        </button>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <footer class="dashboard-footer mt-5">
        <div>© 2025 Sistem Anti-Penipuan. Semua hak dilindungi.</div>
        <div class="footer-links">
          <a href="#">Kebijakan Privasi</a>
          <a href="#">Syarat & Ketentuan</a>
          <a href="#">Bantuan</a>
        </div>
      </footer>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

// Setup layout halaman
const page = usePage();
page.layout = AppLayout;

// Model data
const saldoKredit = ref(page.props.saldoKredit ?? 0);
const levelUser = ref(page.props.levelUser ?? 'Silver');
const progresLevel = ref(65);
const namaUser = computed(() => page.props.auth?.user?.name || 'User'); // Nama user dinamis

// Status verifikasi
const statusVerifikasi = ref(page.props.statusVerifikasi ?? {
  npwp: true,
  identitas: true,
  noHp: false,
  email: true,
  rekening: false
});

// Data laporan dari backend
const laporanTerbaru = ref(page.props.laporanTerbaru ?? []);

// Ringkasan laporan
const laporan = ref({
  total: page.props.laporan?.total ?? 5,
  diproses: page.props.laporan?.diproses ?? 2,
  selesai: page.props.laporan?.selesai ?? 3
});

// Aktivitas
const aktivitas = ref(page.props.aktivitas ?? [
  { icon: '✅', aksi: 'Cek NPWP', detail: '', tanggal: '1 hari lalu', kategori: 'Verifikasi' },
  { icon: '⚠️', aksi: 'Lapor kasus penipuan', detail: '#PL1234', tanggal: '2 hari lalu', kategori: 'Laporan' },
  { icon: '🔍', aksi: 'Cek blacklist buat', detail: '089xxxxxxx', tanggal: '3 hari lalu', kategori: 'Blacklist' },
  { icon: '🧾', aksi: 'Isi saldo sebesar', detail: 'Rp 50.000', tanggal: '5 hari lalu', kategori: 'Pembayaran' }
]);

// Aksi cepat
const aksiCepat = ref([
  { icon: '📝', label: 'Bikin Laporan Baru', variant: 'primary', url: '/pelaporan' },
  { icon: '🔎', label: 'Cari Laporan', variant: 'info', url: '/CariLaporan' },
  { icon: '💰', label: 'Isi Credit', variant: 'warning', url: '/top-ups' },
  { icon: '📊', label: 'Laporan Saya', variant: 'secondary', url: '/laporan-saya' },
  { icon: '📱', label: 'Verifikasi Data', variant: 'danger', url: '/verifikasi' }
]);

// Feedback dinamis
const userAvatar = ref(page.props.auth?.user?.avatar ?? '/Default-Profile.png');
const feedbackKategori = ref(page.props.feedbacks?.kategori ?? 'Cari Laporan');
const feedbackText = ref(page.props.eedbacks?.text );

// Format rupiah
const formatRupiah = (jumlah) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(jumlah);
};

// Persentase verifikasi
const progresVerifikasi = computed(() => {
  const total = Object.keys(statusVerifikasi.value).length;
  const terverifikasi = Object.values(statusVerifikasi.value).filter(status => status).length;
  return Math.round((terverifikasi / total) * 100);
});

// Modal logic
import { Modal } from 'bootstrap';

const showModal = (laporanId) => {
  const modalElement = document.getElementById('laporanModal');
  const modal = new Modal(modalElement);
  modal.show();
};

const showFeedbackModal = () => {
  const feedbackModalElement = document.getElementById('feedbackModal'); // Asumsi ID modal kamu
  const modal = new Modal(feedbackModalElement);
  modal.show();
};
</script>

<style scoped>
:root {
  --primary-color: #4361ee;
  --secondary-color: #3f37c9;
  --success-color: #28a745;
  --warning-color: #f7b801;
  --danger-color: #e5383b;
  --info-color: #4cc9f0;
  --light-color: #f8f9fa;
  --dark-color: #212529;
  --gray-color: #6c757d;
  
  --card-bg: #ffffff;
  --body-bg: #f5f7fa;
  --text-color: #2c3e50;
  --text-muted: #6c757d;
  
  --border-radius: 12px;
  --box-shadow: 0 4px 15px rgba(0,0,0,0.05);
  --transition: all 0.3s ease;
}

.dashboard-container {
  padding: 2rem;
  max-width: 1400px;
  margin: 0 auto;
  background-color: var(--body-bg);
  color: var(--text-color);
  min-height: 100vh;
  transition: var(--transition);
}

/* Header Selamat Datang */
.welcome-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 2rem;
}

.welcome-header h1 {
  font-size: 2rem;
  color: var(--text-color);
  margin-bottom: 0.5rem;
}

.user-info-container {
  display: flex;
  gap: 1rem;
}

.date-badge {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 0.75rem 1rem;
  text-align: center;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
}

.date-badge:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.date-badge .day {
  display: block;
  font-weight: 600;
  color: var(--primary-color);
}

.date-badge .date {
  font-size: 0.85rem;
  color: var(--text-muted);
}

.level-badge {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 0.75rem 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  box-shadow: var(--box-shadow);
  color: var(--warning-color);
  font-weight: 600;
  transition: var(--transition);
}

.level-badge:hover {
  transform: translateY(-3px);
  box-shadow: 0 8px 15px rgba(0,0,0,0.1);
}

.level-badge i {
  font-size: 1.2rem;
}

/* Kartu Ringkasan */
.summary-card {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 1.5rem;
  box-shadow: var(--box-shadow);
  height: 100%;
  transition: var(--transition);
  border: 1px solid rgba(0,0,0,0.02);
  display: flex;
  flex-direction: column;
}

.card-content {
  display: flex;
  align-items: flex-start;
}

.summary-card:hover {
  transform: translateY(-7px);
  box-shadow: 0 15px 20px rgba(0,0,0,0.1);
}

.card-icon {
  width: 50px;
  height: 50px;
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-right: 1rem;
  font-size: 1.5rem;
  transition: var(--transition);
}

.summary-card:hover .card-icon {
  transform: scale(1.1);
}

.credit-balance .card-icon {
  background: rgba(67, 97, 238, 0.1);
  color: var(--primary-color);
}

.verification-status .card-icon {
  background: rgba(40, 167, 69, 0.1);
  color: var(--success-color);
}

.reports-summary .card-icon {
  background: rgba(220, 53, 69, 0.1);
  color: var(--danger-color);
}

.card-title {
  font-size: 0.9rem;
  color: var(--text-muted);
  margin-bottom: 0.5rem;
}

.card-value {
  font-size: 1.75rem;
  font-weight: 700;
  margin-bottom: 0.5rem;
  color: var(--text-color);
}

.card-subtext {
  font-size: 0.85rem;
  color: var(--text-muted);
  margin-bottom: 0;
}

.verification-badges {
  margin-top: 0.75rem;
  display: flex;
  flex-wrap: wrap;
  gap: 0.5rem;
}

.verification-progress {
  margin-bottom: 0.75rem;
}

.progress-container {
  display: flex;
  align-items: center;
  gap: 1rem;
}

.progress-container .progress {
  flex: 1;
  height: 6px;
  border-radius: 3px;
  background-color: rgba(0,0,0,0.05);
}

.verification-progress span {
  font-weight: 600;
  color: var(--success-color);
}

.reports-stats {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
  margin-top: 0.75rem;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  font-size: 0.875rem;
}

/* Progres Level */
.level-progress-container {
  background: var(--card-bg);
  border-radius: var(--border-radius);
  padding: 1.25rem;
  box-shadow: var(--box-shadow);
  transition: var(--transition);
}

.level-progress-container:hover {
  transform: translateY(-5px);
  box-shadow: 0 12px 20px rgba(0,0,0,0.08);
}

.level-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1rem;
}

.level-info h6 {
  font-weight: 600;
  color: var(--text-color);
  margin-bottom: 0.25rem;
}

.level-info p {
  color: var(--text-muted);
  font-size: 0.875rem;
}

/* Kartu Utama */
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

/* Aksi Cepat */
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
  transition: var(--transition);
  text-decoration: none;
}

.btn-action:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 10px rgba(0,0,0,0.1);
}

.action-icon {
  font-size: 1.5rem;
  margin-bottom: 0.5rem;
  display: block;
}

.action-label {
  font-size: 0.8rem;
}

/* Testimonial */
.testimonial {
  background: rgba(0,0,0,0.02);
  border-radius: 10px;
  padding: 1.25rem;
  transition: var(--transition);
}

.testimonial:hover {
  transform: translateY(-3px);
  box-shadow: 0 5px 15px rgba(0,0,0,0.05);
}

/* Tabel */
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

/* Footer */
.dashboard-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding-top: 2rem;
  border-top: 1px solid rgba(0,0,0,0.05);
  color: var(--text-muted);
  font-size: 0.875rem;
}

.footer-links {
  display: flex;
  gap: 1.5rem;
}

.footer-links a {
  color: var(--text-muted);
  text-decoration: none;
  transition: var(--transition);
}

.footer-links a:hover {
  color: var(--primary-color);
}

/* Animasi */
@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes fadeInLeft {
  from {
    opacity: 0;
    transform: translateX(-20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeInRight {
  from {
    opacity: 0;
    transform: translateX(20px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

.animate__animated {
  animation-duration: 0.6s;
  animation-fill-mode: both;
}

.animate__fadeIn {
  animation-name: fadeIn;
}

.animate__fadeInUp {
  animation-name: fadeInUp;
}

.animate__fadeInLeft {
  animation-name: fadeInLeft;
}

.animate__fadeInRight {
  animation-name: fadeInRight;
}

/* Desain Responsif */
@media (max-width: 992px) {
  .dashboard-container {
    padding: 1.5rem;
  }
  
  .welcome-header {
    flex-direction: column;
  }
  
  .user-info-container {
    margin-top: 1rem;
    align-self: flex-start;
  }
  
  .quick-actions-grid {
    grid-template-columns: repeat(2, 1fr);
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: 1rem;
  }
  
  .summary-card {
    padding: 1rem;
  }
  
  .card-content {
    flex-direction: column;
    text-align: center;
  }
  
  .card-icon {
    margin-right: 0;
    margin-bottom: 1rem;
    margin-left: auto;
    margin-right: auto;
  }
  
  .dashboard-footer {
    flex-direction: column;
    gap: 1rem;
    text-align: center;
  }
  
  .footer-links {
    justify-content: center;
  }
}

@media (max-width: 576px) {
  .user-info-container {
    flex-direction: column;
  }
  
  .quick-actions-grid {
    grid-template-columns: 1fr;
  }
  
  .welcome-header h1 {
    font-size: 1.75rem;
  }
  
  .card-value {
    font-size: 1.5rem;
  }
  
  .table-responsive {
    font-size: 0.875rem;
  }
}
</style>