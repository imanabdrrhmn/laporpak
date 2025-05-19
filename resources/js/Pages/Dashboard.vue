<template>
  <Head title="Dashboard" />
  <AppLayout>
    <div class="dashboard-container">
      <WelcomeHeader :nama-user="namaUser" :level-user="levelUser" />
      <div class="row g-4 mb-5">
        <div class="col-md-4 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: 0.1s">
          <SummaryCard
            title="Saldo Kredit"
            icon="bi-wallet2"
            :value="formatRupiah(saldoKredit)"
            icon-bg="rgba(67, 97, 238, 0.1)"
            icon-color="var(--primary-color)"
          >
            <Link
              href="/top-ups"
              class="btn btn-sm btn-primary mt-2">
              Isi Saldo <i class="bi bi-plus-circle ms-1"></i>
            </Link>
            <div class="progress mt-3" style="height: 4px;">
              <div class="progress-bar bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
          </SummaryCard>
        </div>
        <div class="col-md-4 col-sm-6 animate__animated animate__fadeInUp" style="animation-delay: 0.2s">
          <SummaryCard
            title="Status Verifikasi"
            icon="bi-shield-check"
            icon-bg="rgba(40, 167, 69, 0.1)"
            icon-color="var(--success-color)"
          >
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
            <button class="btn btn-sm btn-outline-success w-100 mt-3" v-if="!statusVerifikasi.noHp">Verifikasi No. HP</button>
          </SummaryCard>
        </div>
        <div class="col-md-4 col-sm-12 animate__animated animate__fadeInUp" style="animation-delay: 0.3s">
          <SummaryCard
            title="Total Laporan"
            icon="bi-file-earmark-text"
            :value="reports"
            icon-bg="rgba(220, 53, 69, 0.1)"
            icon-color="var(--danger-color)"
          >
            <div class="reports-stats">
              <div class="stat-item">
                <i class="bi bi-hourglass-split text-warning"></i>
                <span>{{ in_process }} lagi diproses</span>
              </div>
              <div class="stat-item">
                <i class="bi bi-check-circle-fill text-success"></i>
                <span>{{ selected }} udah selesai</span>
              </div>
            </div>
            <Link
              href="/laporan-saya"
              class="btn btn-sm btn-outline-primary w-100 mt-3">
              Lihat Semua Laporan
            </Link>
          </SummaryCard>
        </div>
      </div>
      <LevelProgress :progres-level="progresLevel" />
      <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-4 animate__animated animate__fadeInLeft" style="animation-delay: 0.2s">
          <QuickActions :aksi-cepat="aksiCepat" />
        </div>
        <div class="col-lg-5 col-md-8 animate__animated animate__fadeIn" style="animation-delay: 0.3s">
          <RecentActivity :aktivitas="aktivitas" />
        </div>
        <div class="col-lg-4 animate__animated animate__fadeInRight" style="animation-delay: 0.4s">
          <FeedbackCard
            :feedbacks="props.feedbacks" 
            />
        </div>
      </div>
      <RecentReports :laporan-terbaru="laporanTerbaru" @show-modal="showModal" />
      <DashboardFooter />
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage, Link} from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import WelcomeHeader from '@/Components/Dashboard/WelcomeHeader.vue';
import SummaryCard from '@/Components/Dashboard/SummaryCard.vue';

import QuickActions from '@/Components/Dashboard/QuickActions.vue';
import RecentActivity from '@/Components/Dashboard/RecentActivity.vue';
import FeedbackCard from '@/Components/Dashboard/FeedbackCard.vue';
import RecentReports from '@/Components/Dashboard/RecentReports.vue';
import DashboardFooter from '@/Components/Dashboard/DashboardFooter.vue';
import { Modal } from 'bootstrap';

const props = defineProps({
  feedbacks: Array,
  auth: Object,
  stats: Object,
  reports: Array
})

// Setup layout halaman
const page = usePage();
page.layout = AppLayout;
const { reports, selected, in_process } = props.stats

// Model data
const saldoKredit = computed(() => page.props.user?.balance ?? 0);
const namaUser = computed(() => page.props.auth?.user?.name || 'User');

// Status verifikasi
const statusVerifikasi = ref(page.props.statusVerifikasi ?? {
  npwp: true,
  identitas: true,
  noHp: false,
  email: true,
  rekening: false
});

// Data laporan dari backend
const laporanTerbaru = ref(page.props.reports ?? []);

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
const showModal = (laporanId) => {
  const modalElement = document.getElementById('laporanModal');
  const modal = new Modal(modalElement);
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

@media (max-width: 992px) {
  .dashboard-container {
    padding: 1.5rem;
  }
}

@media (max-width: 768px) {
  .dashboard-container {
    padding: 1rem;
  }
}
</style>