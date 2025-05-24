<template>
  <div class="row">
    <!-- Chart Distribusi Aktivitas Pengguna -->
    <div class="col-lg-6 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Distribusi Aktivitas Pengguna</h5>
            <button class="btn btn-sm btn-outline-secondary" @click="$emit('export', 'user-activity')">
              <i class="bi bi-download me-1"></i> Ekspor
            </button>
          </div>
          <div class="chart-container" style="position: relative; height: 300px;">
            <canvas ref="statusChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Status Laporan -->
    <div class="col-lg-6 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Status Laporan</h5>
            <button class="btn btn-sm btn-outline-secondary" @click="$emit('export', 'report-status')">
              <i class="bi bi-download me-1"></i> Ekspor
            </button>
          </div>
          <div class="chart-container" style="position: relative; height: 300px;">
            <canvas ref="reportChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Verifikasi -->
    <div class="col-lg-6 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Status Verifikasi</h5>
            <button class="btn btn-sm btn-outline-secondary" @click="$emit('export', 'verification-status')">
              <i class="bi bi-download me-1"></i> Ekspor
            </button>
          </div>
          <div class="chart-container" style="position: relative; height: 300px;">
            <canvas ref="verificationChart"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Chart Trend Laporan Bulanan -->
    <div class="col-lg-6 mb-3">
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">Trend Laporan Bulanan</h5>
            <button class="btn btn-sm btn-outline-secondary" @click="$emit('export', 'monthly-trend')">
              <i class="bi bi-download me-1"></i> Ekspor
            </button>
          </div>
          <div class="chart-container" style="position: relative; height: 300px;">
            <canvas ref="trendChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue';
import Chart from 'chart.js/auto';

export default {
  props: {
    reportData: {
      type: Object,
      default: () => ({
        approved: 45,
        pending: 23,
        rejected: 12,
        published: 67
      })
    },
    verificationData: {
      type: Object,
      default: () => ({
        success: 89,
        failed: 15
      })
    },
    monthlyData: {
      type: Array,
      default: () => [
        { month: 'Jan', reports: 45, verifications: 32 },
        { month: 'Feb', reports: 52, verifications: 41 },
        { month: 'Mar', reports: 48, verifications: 38 },
        { month: 'Apr', reports: 61, verifications: 45 },
        { month: 'Mei', reports: 55, verifications: 42 },
        { month: 'Jun', reports: 67, verifications: 48 }
      ]
    }
  },
  
  setup(props) {
    const statusChart = ref(null);
    const reportChart = ref(null);
    const verificationChart = ref(null);
    const trendChart = ref(null);
    
    let statusChartInstance = null;
    let reportChartInstance = null;
    let verificationChartInstance = null;
    let trendChartInstance = null;

    const initStatusChart = () => {
      const statusCtx = statusChart.value.getContext('2d');
      statusChartInstance = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
          labels: ['Pelaporan', 'Verifikasi'],
          datasets: [
            {
              data: [70, 30],
              backgroundColor: ['#4e73df', '#1cc88a'],
              borderWidth: 0,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          cutout: '60%',
          plugins: {
            legend: {
              position: 'right',
            },
          },
        },
      });
    };

    const initReportChart = () => {
      const reportCtx = reportChart.value.getContext('2d');
      reportChartInstance = new Chart(reportCtx, {
        type: 'bar',
        data: {
          labels: ['Approved', 'Pending', 'Rejected', 'Published'],
          datasets: [
            {
              label: 'Jumlah Laporan',
              data: [
                props.reportData.approved,
                props.reportData.pending,
                props.reportData.rejected,
                props.reportData.published
              ],
              backgroundColor: [
                '#28a745', // Green for approved
                '#ffc107', // Yellow for pending
                '#dc3545', // Red for rejected
                '#17a2b8'  // Blue for published
              ],
              borderWidth: 0,
              borderRadius: 4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              display: false,
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 10,
              },
            },
          },
        },
      });
    };

    const initVerificationChart = () => {
      const verificationCtx = verificationChart.value.getContext('2d');
      verificationChartInstance = new Chart(verificationCtx, {
        type: 'pie',
        data: {
          labels: ['Berhasil', 'Gagal'],
          datasets: [
            {
              data: [
                props.verificationData.success,
                props.verificationData.failed
              ],
              backgroundColor: [
                '#28a745', // Green for success
                '#dc3545'  // Red for failed
              ],
              borderWidth: 2,
              borderColor: '#ffffff',
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'bottom',
            },
          },
        },
      });
    };

    const initTrendChart = () => {
      const trendCtx = trendChart.value.getContext('2d');
      trendChartInstance = new Chart(trendCtx, {
        type: 'line',
        data: {
          labels: props.monthlyData.map(item => item.month),
          datasets: [
            {
              label: 'Laporan',
              data: props.monthlyData.map(item => item.reports),
              borderColor: '#4e73df',
              backgroundColor: 'rgba(78, 115, 223, 0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.4,
            },
            {
              label: 'Verifikasi',
              data: props.monthlyData.map(item => item.verifications),
              borderColor: '#1cc88a',
              backgroundColor: 'rgba(28, 200, 138, 0.1)',
              borderWidth: 3,
              fill: true,
              tension: 0.4,
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: {
            legend: {
              position: 'top',
            },
          },
          scales: {
            y: {
              beginAtZero: true,
              ticks: {
                stepSize: 10,
              },
            },
          },
        },
      });
    };

    const destroyCharts = () => {
      if (statusChartInstance) statusChartInstance.destroy();
      if (reportChartInstance) reportChartInstance.destroy();
      if (verificationChartInstance) verificationChartInstance.destroy();
      if (trendChartInstance) trendChartInstance.destroy();
    };

    onMounted(() => {
      initStatusChart();
      initReportChart();
      initVerificationChart();
      initTrendChart();
    });

    onUnmounted(() => {
      destroyCharts();
    });

    return {
      statusChart,
      reportChart,
      verificationChart,
      trendChart,
    };
  },
};
</script>

<style scoped>
.card {
  transition: transform 0.2s ease-in-out;
}

.card:hover {
  transform: translateY(-2px);
}

.chart-container {
  min-height: 300px;
}

.btn-outline-secondary:hover {
  background-color: #6c757d;
  color: white;
}
</style>