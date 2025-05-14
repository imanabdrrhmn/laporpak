<template>
  <AppLayout>
    <div class="container">
      <!-- Filter Section -->
      <div class="filter-section">
        <h1>Laporan Masuk</h1>
        <div class="filters">
          <input
            v-model="searchQuery"
            type="text"
            class="filter-input"
            placeholder="Cari deskripsi atau kategori..."
          />
          <select v-model="selectedCategory" class="filter-select">
            <option value="">Semua Kategori</option>
            <option v-for="category in categories" :key="category" :value="category">
              {{ category }}
            </option>
          </select>
          <select v-model="selectedService" class="filter-select">
            <option value="">Semua Layanan</option>
            <option v-for="service in services" :key="service" :value="service">
              {{ service }}
            </option>
          </select>
          <select v-model="selectedStatus" class="filter-select">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="published">Published</option>
          </select>
          <select v-model="sortDirection" class="filter-select">
            <option value="desc">Terbaru</option>
            <option value="asc">Terlama</option>
          </select>
        </div>
      </div>

      <!-- Report Cards -->
      <div v-if="filteredReports.length" class="reports-list">
        <div v-for="report in filteredReports" :key="report.id" class="report-item">
          <!-- User Info -->
          <div class="user-info">
            <img :src="report.user.avatar_url" alt="User Avatar" class="user-avatar" />
            <span class="user-name">{{ report.user.name }}</span>
          </div>

          <!-- Report Details -->
          <p v-if="report.service == 'Penipuan'">
            <strong>Sumber Penipuan:</strong> {{ report.source || '-' }}
          </p>
          <p>
            <strong>Kategori:</strong> {{ report.category }}
          </p>
          <p>
            <strong>Layanan:</strong> {{ report.service }}
          </p>
          <p>
            <strong>Status:</strong>
            <span :class="['status-badge', report.status]">
              {{ capitalize(report.status) }}
            </span>
          </p>
          <p class="description">{{ truncate(report.description, 100) }}</p>
          <p class="timestamp">{{ formatDate(report.created_at) }}</p>
          <button @click="viewReport(report)" class="view-button">Lihat Detail</button>
        </div>
      </div>

      <div v-else>
        <p class="no-results">Tidak ada laporan ditemukan.</p>
      </div>
    </div>

    <Modal :report="currentReport" :isVisible="showModal" @close="closeModal" />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import Modal from '@/Pages/Pelaporan/LaporanDetailModal.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

const props = defineProps({
  reports: {
    type: Array,
    default: () => []
  }
})

const page = usePage()
const user = computed(() => page.props.auth?.user || {});
const searchQuery = ref('')
const selectedCategory = ref('')
const selectedService = ref('')
const selectedStatus = ref('')
const sortDirection = ref('desc') 

const showModal = ref(false)
const currentReport = ref({})

const categories = computed(() => {
  return [...new Set(props.reports.map(report => report.category))].filter(Boolean)
})

const services = computed(() => {
  return [...new Set(props.reports.map(report => report.service))].filter(Boolean)
})

const filteredReports = computed(() => {
  return props.reports
    .filter(report => {
      const matchesCategory = selectedCategory.value ? report.category === selectedCategory.value : true
      const matchesService = selectedService.value ? report.service === selectedService.value : true
      const matchesStatus = selectedStatus.value ? report.status === selectedStatus.value : true
      const matchesSearch = searchQuery.value
        ? (
          report.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
          report.category.toLowerCase().includes(searchQuery.value.toLowerCase())
        )
        : true
      return matchesCategory && matchesService && matchesStatus && matchesSearch
    })
    .sort((a, b) => {
      return sortDirection.value === 'desc'
        ? new Date(b.created_at) - new Date(a.created_at)
        : new Date(a.created_at) - new Date(b.created_at)
    })
})

const viewReport = (report) => {
  currentReport.value = report
  showModal.value = true
}

const closeModal = () => {
  showModal.value = false
  currentReport.value = {}
}

const formatDate = (iso) => {
  const d = new Date(iso)
  return d.toLocaleString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  })
}

const truncate = (text, max) => {
  return text.length > max ? text.substring(0, max) + '…' : text
}

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1)
</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 32px 20px;
}

.filter-section {
  margin-bottom: 30px;
}

h1 {
  font-size: 2rem;
  font-weight: bold;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
}

.filter-input,
.filter-select {
  padding: 10px;
  font-size: 15px;
  border: 1px solid #ddd;
  border-radius: 6px;
  min-width: 180px;
  background-color: #ffffff;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
  color: #333;
  transition: box-shadow 0.2s;
}

.filter-input:focus,
.filter-select:focus {
  outline: none;
  border-color: #0d6efd;
  box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.2);
}


.reports-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 20px;
}

.report-item {
  background-color: #fdfdfd;
  padding: 20px;
  border-radius: 14px;
  box-shadow: 0 6px 20px rgba(0, 0, 0, 0.05);
  transition: all 0.2s ease-in-out;
  display: flex;
  flex-direction: column;
}

.report-item:hover {
  transform: translateY(-3px);
}

.user-info {
  display: flex;
  align-items: center;
  margin-bottom: 12px;
}

.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  margin-right: 10px;
  object-fit: cover;
}

.user-name {
  font-weight: 600;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: bold;
  text-transform: capitalize;
  margin-left: 6px;
}

.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}
.status-badge.approved {
  background: #d1e7dd;
  color: #0f5132;
}
.status-badge.rejected {
  background: #f8d7da;
  color: #842029;
}
.status-badge.published {
  background: #cfe2ff;
  color: #084298;
}

.description {
  font-size: 14px;
  color: #444;
  margin: 10px 0;
  line-height: 1.5;
}

.timestamp {
  font-size: 13px;
  color: #777;
  margin-top: auto;
}

.view-button {
  margin-top: 12px;
  padding: 8px 16px;
  background-color: #0d6efd;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.2s;
}

.view-button:hover {
  background-color: #0b5ed7;
}

.no-results {
  font-size: 16px;
  color: #999;
  margin-top: 20px;
}
</style>
