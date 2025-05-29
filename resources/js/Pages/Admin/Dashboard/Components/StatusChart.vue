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

<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue'
import Chart from 'chart.js/auto'

const props = defineProps({
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
})

// Refs
const statusChart = ref(null)
const reportChart = ref(null)
const verificationChart = ref(null)
const trendChart = ref(null)

// Cleanup references
let statusChartInstance = null
let reportChartInstance = null
let verificationChartInstance = null
let trendChartInstance = null

// Definisi palet warna yang konsisten
const colors = {
  primary: '#4e73df',      // Biru utama
  success: '#28a745',      // Hijau sukses
  warning: '#ffc107',      // Kuning warning
  danger: '#dc3545',       // Merah bahaya
  info: '#17a2b8',         // Biru info
  purple: '#6f42c1',       // Ungu
  orange: '#fd7e14',       // Orange
  teal: '#20c997'          // Teal
}

// Safe access to reportData
const reportStats = computed(() => ({
  approved: props.reportData?.approved ?? 0,
  pending: props.reportData?.pending ?? 0,
  rejected: props.reportData?.rejected ?? 0,
  published: props.reportData?.published ?? 0
}))

// Chart init functions
const initStatusChart = () => {
  statusChartInstance = new Chart(statusChart.value.getContext('2d'), {
    type: 'doughnut',
    data: {
      labels: ['Pelaporan', 'Verifikasi'],
      datasets: [{
        data: [70, 30],
        backgroundColor: [colors.primary, colors.success],
        borderWidth: 0
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '60%',
      plugins: {
        legend: { position: 'right' }
      }
    }
  })
}

const initReportChart = () => {
  reportChartInstance = new Chart(reportChart.value.getContext('2d'), {
    type: 'bar',
    data: {
      labels: ['Approved', 'Pending', 'Rejected', 'Published'],
      datasets: [{
        label: 'Jumlah Laporan',
        data: [
          reportStats.value.approved,
          reportStats.value.pending,
          reportStats.value.rejected,
          reportStats.value.published
        ],
        backgroundColor: [colors.success, colors.warning, colors.danger, colors.primary],
        borderWidth: 0,
        borderRadius: 4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 10 }
        }
      }
    }
  })
}

const initVerificationChart = () => {
  verificationChartInstance = new Chart(verificationChart.value.getContext('2d'), {
    type: 'pie',
    data: {
      labels: ['Berhasil', 'Gagal'],
      datasets: [{
        data: [
          props.verificationData.success ?? 0,
          props.verificationData.failed ?? 0
        ],
        backgroundColor: [colors.success, colors.danger],
        borderWidth: 2,
        borderColor: '#ffffff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { position: 'bottom' }
      }
    }
  })
}

const initTrendChart = () => {
  trendChartInstance = new Chart(trendChart.value.getContext('2d'), {
    type: 'line',
    data: {
      labels: props.monthlyData.map(item => item.month),
      datasets: [
        {
          label: 'Laporan',
          data: props.monthlyData.map(item => item.reports),
          borderColor: colors.primary,
          backgroundColor: colors.primary + '1A', // 10% opacity
          borderWidth: 3,
          fill: true,
          tension: 0.4
        },
        {
          label: 'Verifikasi',
          data: props.monthlyData.map(item => item.verifications),
          borderColor: colors.success,
          backgroundColor: colors.success + '1A', // 10% opacity
          borderWidth: 3,
          fill: true,
          tension: 0.4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: { legend: { position: 'top' } },
      scales: {
        y: {
          beginAtZero: true,
          ticks: { stepSize: 10 }
        }
      }
    }
  })
}

const destroyCharts = () => {
  statusChartInstance?.destroy()
  reportChartInstance?.destroy()
  verificationChartInstance?.destroy()
  trendChartInstance?.destroy()
}

// Lifecycle
onMounted(() => {
  initStatusChart()
  initReportChart()
  initVerificationChart()
  initTrendChart()
})

onUnmounted(() => {
  destroyCharts()
})
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