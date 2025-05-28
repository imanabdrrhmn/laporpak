<template>
  <AppLayout>
    <Head title="Dashboard Admin" />
    <div class="dashboard-container">
      <template v-if="canViewDashboard">
        <h2 class="dashboard-title">Dashboard Admin</h2>

        <!-- Stat Cards -->
        <div class="stats-grid">
          <StatCard
            title="Laporan Masuk"
            :count="dashboardStats.laporanMasuk.count"
            :percentage="dashboardStats.laporanMasuk.percentage"
            icon="bi-file-earmark-arrow-down"
            bgClass="bg-primary"
          />
          <StatCard
            title="Laporan Terverifikasi"
            :count="dashboardStats.laporanTerverifikasi.count"
            :percentage="dashboardStats.laporanTerverifikasi.percentage"
            icon="bi-check-circle"
            bgClass="bg-success"
          />
          <StatCard
            title="Total Pengguna"
            :count="dashboardStats.totalPengguna.count"
            :percentage="dashboardStats.totalPengguna.percentage"
            icon="bi-people-fill"
            bgClass="bg-info"
          />
          <StatCard
            title="Saldo Top-Up Masuk"
            :count="`Rp ${formatNumber(dashboardStats.saldoTopUp.amount)}`"
            :percentage="dashboardStats.saldoTopUp.percentage"
            icon="bi-wallet2"
            bgClass="bg-secondary"
            isCurrency
          />
        </div>

        <!-- Charts -->
        <div class="charts-grid">
          <StatusChart @export="exportStatusData" :reportData="reportData" />
        </div>

        <!-- Recent Reports -->
        <RecentReports
          :reports="reports"
          @filter="showFilterModal"
          @export="exportSummaryData"
          :getStatusClass="getStatusClass"
        />
      </template>

      <template v-else>
        <AccessDenied />
      </template>
    </div>
  </AppLayout>
</template>

<script>
import { computed } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from './Components/StatCard.vue';
import StatusChart from './Components/StatusChart.vue';
import RecentReports from './Components/RecentReports.vue';
import AccessDenied from '@/Components/AccessDenied.vue';

export default {
  components: {
    AppLayout,
    Head,
    StatCard,
    StatusChart,
    RecentReports,
    AccessDenied,
  },
  setup() {
    const page = usePage();
    const dashboardStats = computed(() => page.props.dashboardStats || {});
    const reports = computed(() => page.props.reports || []);
    const reportData = computed(() => page.props.reportData || {});
    const userPermissions = computed(() => page.props.auth?.user?.permissions || []);
    const canViewDashboard = computed(() => userPermissions.value.includes('view_dashboard'));

    const formatNumber = (number) => {
      if (number === undefined || number === null) return '-';
      return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };

    const getStatusClass = (status) => {
      switch (status) {
        case 'Pending': return 'warning';
        case 'In Progress': return 'primary';
        case 'Resolved': return 'success';
        default: return 'secondary';
      }
    };

    const exportTrenData = () => console.log('Export trend data');
    const exportStatusData = () => console.log('Export status data');
    const exportSummaryData = () => console.log('Export summary data');
    const showFilterModal = () => console.log('Show filter modal');

    return {
      dashboardStats,
      reports,
      reportData,
      formatNumber,
      getStatusClass,
      exportTrenData,
      exportStatusData,
      exportSummaryData,
      showFilterModal,
      canViewDashboard,
    };
  },
};
</script>

<style scoped>
.dashboard-container {
  padding: 2rem 1rem;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  box-sizing: border-box;
}

.dashboard-title {
  font-weight: 700;
  font-size: 2rem;
  margin-bottom: 2rem;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(2, minmax(250px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

@media (max-width: 950px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
}

.charts-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

body {
  overflow-x: hidden;
}

/* Fix overflow issue */
.dashboard-container {
  padding: 2rem 1rem;
  width: 100%;
  max-width: 1200px;
  margin: 0 auto;
  box-sizing: border-box;
  overflow-x: hidden;
}

/* Prevent child elements from overflowing */
.stats-grid,
.charts-grid {
  width: 100%;
  overflow-x: hidden;
}

* {
  box-sizing: border-box;
}

/* Make sure all text wraps */
h3, p, span, div {
  word-wrap: break-word;
  white-space: normal;
}

</style>
