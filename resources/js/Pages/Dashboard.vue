<template>
  <Head title="Dashboard" />
  <AppLayout>
    <div class="dashboard-container">
      <WelcomeHeader 
        :nama-user="namaUser" 
        :level-user="levelUser"
        :total-laporan="16"
        :sedang-diproses="9"
        :proses-selesai="7"
        :saldo-kredit="saldoKredit"
      />
      <div class="row g-3">
        <div class="col-12 col-md-6 col-lg-4">
          <QuickActions :aksi-cepat="aksiCepat" />
        </div>
        <div class="col-12 col-md-6 col-lg-8">
          <RecentActivity :aktivitas="aktivitas" />
        </div>
        <div class="col-12">
          <FeedbackCard :feedbacks="props.feedbacks" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import WelcomeHeader from '@/Components/Dashboard/WelcomeHeader.vue';
import QuickActions from '@/Components/Dashboard/QuickActions.vue';
import RecentActivity from '@/Components/Dashboard/RecentActivity.vue';
import FeedbackCard from '@/Components/Dashboard/FeedbackCard.vue';

const props = defineProps({
  feedbacks: Array,
  auth: Object,
  stats: Object,
  reports: Array
})

const page = usePage();
const saldoKredit = computed(() => page.props.user?.balance ?? 0);
const namaUser = computed(() => page.props.auth?.user?.name || 'User');
const statusVerifikasi = ref(page.props.statusVerifikasi ?? {
  npwp: true,
  identitas: true,
  noHp: false,
  email: true,
  rekening: false
});
const laporanTerbaru = ref(page.props.reports ?? []);
const userAvatar = ref(page.props.auth?.user?.avatar ?? '/Default-Profile.png');

const formatRupiah = (jumlah) => {
  return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(jumlah);
};

const progresVerifikasi = computed(() => {
  const total = Object.keys(statusVerifikasi.value).length;
  const terverifikasi = Object.values(statusVerifikasi.value).filter(status => status).length;
  return Math.round((terverifikasi / total) * 100);
});
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
  --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
  --transition: all 0.3s ease;
  --max-width: 1400px; /* Batas lebar maksimum yang fleksibel */
}

.dashboard-container {
  padding: clamp(1rem, 3vw, 2rem);
  margin: 0 auto;
  background-color: var(--body-bg);
  color: var(--text-color);
  min-height: 100vh;
  width: 100%;
  max-width: var(--max-width); /* Membatasi lebar maksimum */
  overflow-x: hidden; /* Mencegah overflow horizontal */
}

.row {
  margin-left: 0;
  margin-right: 0;
}

/* Responsive Design */
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

@media (max-width: 576px) {
  .dashboard-container {
    padding: 0.75rem;
  }
}
</style>