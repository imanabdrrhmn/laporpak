<template>
  <AppLayout>
    <Head title="Dashboard Admin" />
    <div class="wrapper">
      <div class="container-fluid p-3">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="fw-bold mb-0">Dashboard Admin</h2>
        </div>

        <!-- Stat Cards -->
        <div class="row mb-4">
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

        <!-- Charts Row -->
        <div class="row mb-4">
          <TrendChart @export="exportTrenData" />
          <StatusChart @export="exportStatusData" />
        </div>

        <!-- Recent Reports Summary -->
        <RecentReports
          :reports="reports"
          @filter="showFilterModal"
          @export="exportSummaryData"
          :getStatusClass="getStatusClass"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script>
import { ref, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from './Components/StatCard.vue';

import StatusChart from './Components/StatusChart.vue';
import RecentReports from './Components/RecentReports.vue';
import axios from 'axios';

export default {
  components: {
    AppLayout,
    Head,
    StatCard,

    StatusChart,
    RecentReports,
  },
  setup() {
    // Dashboard stats
    const dashboardStats = ref({
      laporanMasuk: { count: 156, percentage: 2.3 },
      laporanTerverifikasi: { count: 800, percentage: 2.5 },
      totalPengguna: { count: 3000, percentage: 12.5 },
      saldoTopUp: { amount: 15250000, percentage: 3.5 },
    });

    // Reports data
    const reports = ref([]);

    // Helper methods
    const formatNumber = (number) => {
      return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    };

    const getStatusClass = (status) => {
      switch (status) {
        case 'Pending':
          return 'warning';
        case 'In Progress':
          return 'primary';
        case 'Resolved':
          return 'success';
        default:
          return 'secondary';
      }
    };

    // API calls
    const fetchDashboardData = async () => {
      try {
        console.log('Dashboard data fetched');
      } catch (error) {
        console.error('Error fetching dashboard data:', error);
      }
    };

    const fetchReports = async () => {
      try {
        console.log('Reports fetched');
      } catch (error) {
        console.error('Error fetching reports:', error);
      }
    };

    // Action methods
    const exportTrenData = () => {
      console.log('Exporting trend data');
    };

    const exportStatusData = () => {
      console.log('Exporting status data');
    };

    const exportSummaryData = () => {
      console.log('Exporting summary data');
    };

    const showFilterModal = () => {
      console.log('Showing filter modal');
    };

    onMounted(() => {
      fetchDashboardData();
      fetchReports();
    });

    return {
      dashboardStats,
      reports,
      formatNumber,
      getStatusClass,
      exportTrenData,
      exportStatusData,
      exportSummaryData,
      showFilterModal,
    };
  },
};
</script>

<style scoped>
.card {
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
  transition: transform 0.2s;
  overflow: hidden;
}

.custom-card {
  border-radius: 12px;
  background: #fff;
  gap: 1rem;
}

.custom-card .card-body {
  padding: 10px;
  height: 102px;
}

.text-content {
  flex-grow: 1;
  text-align: left;
}

.icon-circle {
  width: 48px;
  height: 48px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.2rem;
  background-color: #4e73df;
}

.bg-primary { background-color: #4e73df; }
.bg-warning { background-color: #f6c23e; }
.bg-success { background-color: #1cc88a; }
.bg-info { background-color: #36b9cc; }
.bg-secondary { background-color: #9370DB; }

.card:hover {
  transform: translateY(-5px);
}

.small {
  font-size: 0.8rem;
}

.report-card {
  padding: 15px;
}

.badge-danger { background-color: #e74a3b; }
.badge-warning { background-color: #f6c23e; }
.badge-info { background-color: #36b9cc; }
.badge-success { background-color: #1cc88a; }
.badge-primary { background-color: #4e73df; }

.icon-circle i {
  font-size: 1.5rem;
}

.col-md-3 {
  padding: 0 5px;
}

.mb-3 {
  margin-bottom: 0.5rem !important;
}

.row {
  margin-right: -5px;
  margin-left: -5px;
}

@media (max-width: 768px) {
  .col-md-3 {
    flex: 0 0 50%;
    max-width: 50%;
  }
  .custom-card {
    margin-right: 0;
    margin-bottom: 15px;
  }
  .d-flex.justify-content-between {
    flex-direction: column;
  }
  .d-flex.justify-content-between.align-items-center .d-flex {
    flex-direction: column;
    width: 100%;
  }
  .d-flex.justify-content-between.align-items-center .d-flex button {
    margin-right: 0 !important;
    margin-bottom: 0.5rem;
    width: 100%;
  }
}
</style>