<template>
  <AppLayout>
    <Head title="Dashboard Admin" />
    <div class="dashboard-container">
      <template v-if="canViewDashboard">
        <div class="dashboard-header-controls">
          <h2 class="dashboard-title">Dashboard Admin</h2>
          <div class="period-filter-section">
            <label for="periodFilter" class="period-label">Pilih Periode:</label>
            <select id="periodFilter" v-model="selectedPeriod" @change="handlePeriodChange" class="period-select form-select form-select-sm">
              <option v-for="period in availablePeriods" :key="period.value" :value="period.value">
                {{ period.label }}
              </option>
            </select>
            <div v-if="selectedPeriod === 'custom'" class="custom-date-range">
              <input type="date" v-model="customStartDate" @change="handlePeriodChange" class="date-input form-control form-control-sm" aria-label="Tanggal Mulai Kustom"/>
              <span class="date-separator">-</span>
              <input type="date" v-model="customEndDate" @change="handlePeriodChange" class="date-input form-control form-control-sm" aria-label="Tanggal Akhir Kustom"/>
            </div>
          </div>
        </div>

        <div v-if="isLoading" class="loading-indicator-page">
          <div class="simple-spinner"></div>
          <p class="loading-text-page">Memuat data...</p>
        </div>
        <div v-else-if="fetchError" class="error-indicator-page">
            <p class="error-title-page">Gagal Memuat Data Dashboard</p>
            <p class="error-message-page">{{ fetchError }}</p>
            <button @click="fetchDashboardData" class="retry-button-page">Coba Lagi</button>
        </div>

        <template v-else>
          <div class="stats-grid" v-if="dashboardData.dashboardStats">
            <StatCard
              title="Laporan Masuk"
              :count="dashboardData.dashboardStats.laporanMasuk.count"
              :totalOverall="dashboardData.dashboardStats.laporanMasuk.total_overall"
              :percentage="dashboardData.dashboardStats.laporanMasuk.percentage"
              :periodLabel="dashboardData.dashboardStats.laporanMasuk.period_label"
              icon="bi-file-earmark-arrow-down"
              bgClass="bg-primary-soft"
            />
            <StatCard
              title="Laporan Terverifikasi"
              :count="dashboardData.dashboardStats.laporanTerverifikasi.count"
              :totalOverall="dashboardData.dashboardStats.laporanTerverifikasi.total_overall"
              :percentage="dashboardData.dashboardStats.laporanTerverifikasi.percentage"
              :periodLabel="dashboardData.dashboardStats.laporanTerverifikasi.period_label"
              icon="bi-check-circle"
              bgClass="bg-success-soft"
            />
            <StatCard
              title="Total Pengguna"
              :count="dashboardData.dashboardStats.totalPengguna.count"
              :totalOverall="dashboardData.dashboardStats.totalPengguna.total_overall"
              :percentage="dashboardData.dashboardStats.totalPengguna.percentage"
              :periodLabel="dashboardData.dashboardStats.totalPengguna.period_label"
              icon="bi-people-fill"
              bgClass="bg-info-soft"
            />
            <StatCard
              title="Saldo Top-Up Masuk"
              :count="`Rp ${formatNumber(dashboardData.dashboardStats.saldoTopUp.amount)}`"
              :totalOverall="`Rp ${formatNumber(dashboardData.dashboardStats.saldoTopUp.total_overall)}`"
              :percentage="dashboardData.dashboardStats.saldoTopUp.percentage"
              :periodLabel="dashboardData.dashboardStats.saldoTopUp.period_label"
              icon="bi-wallet2"
              bgClass="bg-secondary-soft"
              isCurrency
            />
          </div>

          <div class="charts-grid" v-if="dashboardData.reportStatusCounts">
            <StatusChart @export="exportStatusData" :reportData="dashboardData.reportStatusCounts" />
          </div>

          <RecentReports
            v-if="dashboardData.recentReports"
            :reports="dashboardData.recentReports"
            @filter="showFilterModal"
            @export="exportSummaryData"
            :getStatusClass="getStatusClass"
          />
        </template>
      </template>

      <template v-else>
        <AccessDenied />
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import StatCard from '@/Pages/Admin/Dashboard/Components/StatCard.vue';
import StatusChart from '@/Pages/Admin/Dashboard/Components/StatusChart.vue';
import RecentReports from '@/Pages/Admin/Dashboard/Components/RecentReports.vue';
import AccessDenied from '@/Components/AccessDenied.vue';

const page = usePage();

const props = defineProps({
  availablePeriods: {
    type: Array,
    default: () => [
        { value: 'this_month', label: 'Bulan Ini' },
        { value: 'last_month', label: 'Bulan Lalu' },
        { value: 'this_year', label: 'Tahun Ini' },
        { value: 'all_time', label: 'Semua Waktu'},
        { value: 'custom', label: 'Kustom Rentang'},
    ]
  },
  canViewDashboard: {
    type: Boolean,
    default: false, 
  },
});

const dashboardData = ref({
  dashboardStats: {
    laporanMasuk: { count: 0, total_overall: 0, percentage: 0, period_label: '' },
    laporanTerverifikasi: { count: 0, total_overall: 0, percentage: 0, period_label: '' },
    totalPengguna: { count: 0, total_overall: 0, percentage: 0, period_label: '' },
    saldoTopUp: { amount: 0, total_overall: 0, percentage: 0, period_label: '' },
    filter_period: 'this_month',
    current_period_start: null,
    current_period_end: null,
  },
  recentReports: [],
  reportStatusCounts: {},
});

const isLoading = ref(true);
const fetchError = ref(null);

const selectedPeriod = ref('this_month');
const customStartDate = ref('');
const customEndDate = ref('');

const API_DASHBOARD_URL = '/api/dashboard-data';

const fetchDashboardData = async () => {
    if (!props.canViewDashboard) {
        isLoading.value = false;
        fetchError.value = "Anda tidak memiliki izin untuk memuat data dashboard.";
        return;
    }

    isLoading.value = true;
    fetchError.value = null;
    try {
        const params = {
            period: selectedPeriod.value,
        };
        if (selectedPeriod.value === 'custom') {
            if (customStartDate.value && customEndDate.value) {
                params.start_date = customStartDate.value;
                params.end_date = customEndDate.value;
            } else {
                fetchError.value = "Untuk periode kustom, tanggal mulai dan akhir harus diisi.";
                isLoading.value = false;
                dashboardData.value = {
                    dashboardStats: { laporanMasuk: {}, laporanTerverifikasi: {}, totalPengguna: {}, saldoTopUp: {} },
                    recentReports: [], reportStatusCounts: {}
                };
                return;
            }
        }

        const response = await axios.get(API_DASHBOARD_URL, { params });
        dashboardData.value = response.data;

        if (response.data.dashboardStats?.filter_period) {
            selectedPeriod.value = response.data.dashboardStats.filter_period;
        }
        if (response.data.dashboardStats?.current_period_start) {
            customStartDate.value = response.data.dashboardStats.current_period_start;
        }
        if (response.data.dashboardStats?.current_period_end) {
            customEndDate.value = response.data.dashboardStats.current_period_end;
        }

    } catch (error) {
        console.error("Error fetching dashboard data:", error);
        // Display user-friendly message from API error, or generic one
        fetchError.value = error.response?.data?.message || error.message || "Gagal memuat data dashboard.";
        dashboardData.value = { // Reset data on error
            dashboardStats: { laporanMasuk: {}, laporanTerverifikasi: {}, totalPengguna: {}, saldoTopUp: {} },
            recentReports: [], reportStatusCounts: {}
        };
    } finally {
        isLoading.value = false;
    }
};

const handlePeriodChange = () => {
    if (selectedPeriod.value === 'custom' && (!customStartDate.value || !customEndDate.value)) {
        return; 
    }
    if (selectedPeriod.value === 'custom' && customStartDate.value && customEndDate.value) {
        if (new Date(customStartDate.value) > new Date(customEndDate.value)) {
            fetchError.value = "Tanggal mulai tidak boleh setelah tanggal akhir.";
            dashboardData.value = {
                dashboardStats: { laporanMasuk: {}, laporanTerverifikasi: {}, totalPengguna: {}, saldoTopUp: {} },
                recentReports: [], reportStatusCounts: {}
            };
            return;
        }
    }
    fetchDashboardData();
};

onMounted(() => {
    if (props.canViewDashboard) {
        if (selectedPeriod.value === 'custom' && !customStartDate.value && !customEndDate.value) {
            const today = new Date();
            const firstDayOfMonth = new Date(today.getFullYear(), today.getMonth(), 1);
            customStartDate.value = firstDayOfMonth.toISOString().split('T')[0];
            customEndDate.value = today.toISOString().split('T')[0];
        }
        fetchDashboardData();
    } else {
        isLoading.value = false;
    }
});

const formatNumber = (number) => {
    if (number === undefined || number === null || isNaN(parseFloat(number))) return '0';
    return parseFloat(number).toLocaleString('id-ID');
};

const getStatusClass = (status) => {
    switch (status?.toLowerCase()) {
        case 'pending': return 'text-warning';
        case 'approved': return 'text-primary';
        case 'published': return 'text-success';
        case 'solved': return 'text-info';
        case 'rejected': return 'text-danger';
        case 'unpublished': return 'text-secondary';
        default: return 'text-secondary';
    }
};

const exportStatusData = () => console.log('Export status data triggered', dashboardData.value.reportStatusCounts);
const exportSummaryData = () => console.log('Export summary data triggered', dashboardData.value.recentReports);
const showFilterModal = () => console.log('Show filter modal triggered');
</script>

<style scoped>
.dashboard-container {
  padding: 1.5rem;
  width: 100%;
  max-width: 1400px;
  margin: 0 auto;
  box-sizing: border-box;
  background-color: #f9fafb;
}

.dashboard-header-controls {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  margin-bottom: 2rem;
  gap: 1rem;
}

.dashboard-title {
  font-weight: 700;
  font-size: 1.75rem;
  color: #1f2937;
}

.period-filter-section {
  display: flex;
  align-items: center;
  gap: 0.75rem;
  flex-wrap: wrap;
}

.period-label {
  font-size: 0.9rem;
  color: #4b5563;
  font-weight: 500;
}

.period-select, .date-input {
  padding: 0.4rem 0.8rem;
  font-size: 0.875rem;
  border-radius: 0.375rem;
  border: 1px solid #d1d5db;
  background-color: white;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
}

.period-select {
    min-width: 150px;
}

.custom-date-range {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}

.date-input {
  max-width: 140px;
}

.date-separator {
  color: #6b7280;
}

.loading-indicator-page {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
  border-radius: 0.5rem;
  margin-top: 1rem;
}

.simple-spinner {
  width: 2rem;
  height: 2rem;
  border: 3px solid #e5e7eb;
  border-top: 3px solid #3b82f6;
  border-radius: 50%;
  animation: spin 1s linear infinite;
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
}

.loading-text-page {
  margin-top: 1rem;
  font-size: 0.9rem;
  color: #6b7280;
}

.error-indicator-page {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 3rem 1rem;
  text-align: center;
  border-radius: 0.5rem;
  margin-top: 1rem;
  background-color: #fee2e2;
  border: 1px solid #fecaca;
  color: #991b1b;
}

.error-title-page { 
  font-weight: 600; 
  font-size: 1.25rem; 
}

.error-message-page { 
  margin-top: 0.5rem; 
}

.retry-button-page {
  margin-top: 1rem; 
  padding: 0.6rem 1.2rem; 
  background-color: #3b82f6;
  color: white; 
  border-radius: 0.375rem; 
  font-size: 0.9rem; 
  transition: background-color 0.2s; 
  border: none;
  cursor: pointer;
}

.retry-button-page:hover { 
  background-color: #2563eb; 
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax( 1fr));
  gap: 1.5rem;
  margin-bottom: 2rem;
}

.charts-grid {
  display: grid;
  grid-template-columns: 1fr;
  gap: 1.5rem;
  margin-bottom: 2rem;
}

:deep(.bg-primary-soft) { background-color: rgba(59, 130, 246, 0.1); color: #2563eb; }
:deep(.bg-success-soft) { background-color: rgba(16, 185, 129, 0.1); color: #059669; }
:deep(.bg-info-soft) { background-color: rgba(56, 189, 248, 0.1); color: #0ea5e9; }
:deep(.bg-secondary-soft) { background-color: rgba(107, 114, 128, 0.1); color: #4b5563; }

@media (max-width: 500px) {
  .dashboard-header-controls {
    flex-direction: column;
    align-items: flex-start;
  }
  .period-filter-section {
    width: 100%;
    margin-top: 1rem;
  }
  .period-select, .custom-date-range {
    width: 100%;
  }
  .date-input {
    flex-grow: 1;
  }
  .stats-grid {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
  }
  .dashboard-title {
    font-size: 1.5rem;
  }

  .charts-grid {
    width: 100%;
  }
}
</style>