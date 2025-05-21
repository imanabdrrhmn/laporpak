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
          <!-- Laporan Masuk Card -->
          <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-0 shadow-sm custom-card">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div class="text-content">
                  <div class="text-muted mb-1">Laporan Masuk</div>
                  <h3 class="mb-0">{{ dashboardStats.laporanMasuk.count }}</h3>
                  <div class="text-success small">
                    <i class="bi bi-arrow-up-short"></i>
                    {{ dashboardStats.laporanMasuk.percentage }}% dari bulan lalu
                  </div>
                </div>
                <div class="icon-circle bg-primary">
                  <i class="bi bi-file-earmark-arrow-down text-white"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Laporan Terverifikasi Card -->
          <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-0 shadow-sm custom-card">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div class="text-content">
                  <div class="text-muted mb-1">Laporan Terverifikasi</div>
                  <h3 class="mb-0">{{ dashboardStats.laporanTerverifikasi.count }}</h3>
                  <div class="text-success small">
                    <i class="bi bi-arrow-up-short"></i>
                    {{ dashboardStats.laporanTerverifikasi.percentage }}% dari bulan lalu
                  </div>
                </div>
                <div class="icon-circle bg-success">
                  <i class="bi bi-check-circle text-white"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Total Pengguna Card -->
          <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-0 shadow-sm custom-card">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div class="text-content">
                  <div class="text-muted mb-1">Total Pengguna</div>
                  <h3 class="mb-0">{{ dashboardStats.totalPengguna.count }}</h3>
                  <div class="text-success small">
                    <i class="bi bi-arrow-up-short"></i>
                    {{ dashboardStats.totalPengguna.percentage }}% dari bulan lalu
                  </div>
                </div>
                <div class="icon-circle bg-info">
                  <i class="bi bi-people-fill text-white"></i>
                </div>
              </div>
            </div>
          </div>

          <!-- Saldo Top-Up Card -->
          <div class="col-md-3 col-6 mb-3">
            <div class="card h-100 border-0 shadow-sm custom-card">
              <div class="card-body d-flex justify-content-between align-items-center">
                <div class="text-content">
                  <div class="text-muted mb-1">Saldo Top-Up Masuk</div>
                  <h3 class="mb-0">Rp {{ formatNumber(dashboardStats.saldoTopUp.amount) }}</h3>
                  <div class="text-success small">
                    <i class="bi bi-arrow-up-short"></i>
                    {{ dashboardStats.saldoTopUp.percentage }}% transaksi dari bulan ini
                  </div>
                </div>
                <div class="icon-circle bg-secondary">
                  <i class="bi bi-wallet2 text-white"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Charts Row -->
        <div class="row mb-4">
          <!-- Monthly Report Trend Chart -->
          <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title mb-0">Tren Laporan dan Verifikasi</h5>
                  <button class="btn btn-sm btn-outline-secondary" @click="exportTrenData">
                    <i class="bi bi-download me-1"></i> Ekspor
                  </button>
                </div>
                <div class="chart-container" style="position: relative; height: 300px;">
                  <canvas ref="trendChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <!-- Status Distribution Chart -->
          <div class="col-lg-6 mb-3">
            <div class="card border-0 shadow-sm">
              <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <h5 class="card-title mb-0">Distribusi Aktivitas Pengguna</h5>
                  <button class="btn btn-sm btn-outline-secondary" @click="exportStatusData">
                    <i class="bi bi-download me-1"></i> Ekspor
                  </button>
                </div>
                <div class="chart-container" style="position: relative; height: 300px;">
                  <canvas ref="statusChart"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Reports Summary -->
        <div class="card border-0 shadow-sm mb-3">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Ringkasan Laporan Terbaru</h5>
              <div class="d-flex">
                <button class="btn btn-sm btn-outline-secondary mr-2" @click="showFilterModal">
                  <i class="bi bi-funnel me-1"></i> Filter
                </button>
                <button class="btn btn-sm btn-outline-secondary" @click="exportSummaryData">
                  <i class="bi bi-download me-1"></i> Ekspor
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-md-4 mb-3" v-for="report in reports.slice(0, 3)" :key="report.id">
                <div class="card h-100 border-0 shadow-sm report-card">
                  <div class="card-body">
                    <h6 class="card-subtitle mb-2 text-muted">{{ report.jenisPengaduan }}</h6>
                    <p class="card-text"><strong>Pelapor:</strong> {{ report.pelapor }}</p>
                    <p class="card-text"><strong>Status:</strong> 
                      <span :class="'badge badge-' + getStatusClass(report.status)">
                        {{ report.status }}
                      </span>
                    </p>
                    <p class="card-text"><strong>Tanggal:</strong> {{ report.tanggal }}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>


<script>
import { ref, onMounted } from 'vue';
import Chart from 'chart.js/auto';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';

export default {
  components: {
    AdminLayout,
    Head
  },
  setup() {
    // Refs for charts
    const trendChart = ref(null);
    const statusChart = ref(null);
    let trendChartInstance = null;
    let statusChartInstance = null;

    // Dashboard stats
    const dashboardStats = ref({
      laporanMasuk: { count: 156, percentage: 2.3 },
      laporanTerverifikasi: { count: 800, percentage: 2.5 },
      totalPengguna: { count: 3000, percentage: 12.5 },
      saldoTopUp: { amount: 15250000, percentage: 3.5 }
    });

    // Reports data
    const reports = ref([
   
    ]);

    // Charts initialization
    const initCharts = () => {
      // Trend Chart (Laporan Masuk dan Verifikasi)
      const trendCtx = trendChart.value.getContext('2d');
      trendChartInstance = new Chart(trendCtx, {
        type: 'line',
        data: {
          labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May'],
          datasets: [
            {
              label: 'Laporan Masuk',
              data: [120, 150, 180, 200, 220],
              borderColor: '#4e73df',
              backgroundColor: 'rgba(78, 115, 223, 0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.3
            },
            {
              label: 'Verifikasi Dilakukan',
              data: [50, 80, 100, 120, 150],
              borderColor: '#1cc88a',
              backgroundColor: 'rgba(28, 200, 138, 0.1)',
              borderWidth: 2,
              fill: true,
              tension: 0.3
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: true
            }
          },
          scales: {
            y: {
              beginAtZero: true,
              grid: {
                drawBorder: false
              }
            },
            x: {
              grid: {
                display: false
              }
            }
          }
        }
      });

      // Status Distribution Chart (Aktivitas Pengguna)
      const statusCtx = statusChart.value.getContext('2d');
      statusChartInstance = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
          labels: ['Pelaporan', 'Verifikasi'],
          datasets: [{
            data: [70, 30], // Contoh distribusi: 70% pelaporan, 30% verifikasi
            backgroundColor: ['#4e73df', '#1cc88a'],
            borderWidth: 0
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '60%',
          plugins: {
            legend: {
              position: 'right'
            }
          }
        }
      });
    };

    onMounted(() => {
      initCharts();
      fetchDashboardData();
      fetchReports();
    });

    // Helper methods
    const formatNumber = (number) => {
      return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    };

    const getStatusClass = (status) => {
      switch (status) {
        case 'Pending': return 'warning';
        case 'In Progress': return 'primary';
        case 'Resolved': return 'success';
        default: return 'secondary';
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

    return {
      trendChart,
      statusChart,
      dashboardStats,
      reports,
      formatNumber,
      getStatusClass,
      exportTrenData,
      exportStatusData,
      exportSummaryData,
      showFilterModal
    };
  }
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
  margin-bottom: 0.5rem !important; /* Reduce bottom margin */
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