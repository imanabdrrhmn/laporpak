<template>
  <AppLayout>
    <div class="container">
      <!-- Filter Section -->
      <div class="filter-section">
        <h1>Laporan</h1>
        <div class="filters">
          <!-- Search Input -->
          <input
            v-model="searchQuery"
            type="text"
            class="filter-input"
            placeholder="Search by title..."
          />
  
          <!-- Filter by Category -->
          <select v-model="selectedCategory" class="filter-select">
            <option value="">All Categories</option>
            <option v-for="category in categories" :key="category" :value="category">
              {{ category }}
            </option>
          </select>
  
          <!-- Filter by Service -->
          <select v-model="selectedService" class="filter-select">
            <option value="">All Services</option>
            <option v-for="service in services" :key="service" :value="service">
              {{ service }}
            </option>
          </select>
  
          <!-- Filter by Status -->
          <select v-model="selectedStatus" class="filter-select">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="published">Published</option>
          </select>
        </div>
      </div>
  
      <!-- Reports Section -->
      <div v-if="filteredReports.length" class="reports-list">
        <div v-for="report in filteredReports" :key="report.id" class="report-item">
          <!-- User Information (Name and Avatar) -->
          <div class="user-info">
            <img :src="report.user.avatar_url" alt="User Avatar" class="user-avatar" />
            <span class="user-name">{{ report.user.name }}</span>
          </div>

          <!-- Report Information -->
          <p><strong>Service:</strong> {{ report.service }}</p>
          <p><strong>Kategori:</strong> {{ report.category }}</p>
          <p><strong>Status:</strong> {{ report.status }}</p>
          <p>{{ report.description }}</p>
          <button @click="viewReport(report)">View</button>
        </div>
      </div>
  
      <div v-else>
        <p>No reports found.</p>
      </div>
    </div>
  </AppLayout>

  <Modal :report="currentReport" :isVisible="showModal" @close="closeModal" />
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3';
import Modal from '@/Pages/Pelaporan/LaporanDetailModal.vue'
import AppLayout from '@/Layouts/AppLayout.vue'

// Receiving the 'reports' prop from Inertia
const props = defineProps({
  reports: {
    type: Array,
    default: () => [] // Default to empty array if no reports
  }
})

const page = usePage();
const user = page.props.auth.user;

const searchQuery = ref('')
const selectedCategory = ref('')
const selectedService = ref('')
const selectedStatus = ref('')

// Define state for modal visibility and the selected report
const showModal = ref(false)
const currentReport = ref({})  

// Extracting unique categories and services
const categories = computed(() => {
  const uniqueCategories = [...new Set(props.reports.map((report) => report.category))]
  return uniqueCategories
})

const services = computed(() => {
  const uniqueServices = [...new Set(props.reports.map((report) => report.service))]
  return uniqueServices
})

// Filtered reports based on selected filters
const filteredReports = computed(() => {
  return props.reports.filter((report) => {
    const matchesCategory = selectedCategory.value ? report.category === selectedCategory.value : true
    const matchesService = selectedService.value ? report.service === selectedService.value : true
    const matchesStatus = selectedStatus.value ? report.status === selectedStatus.value : true
    const matchesSearchQuery = searchQuery.value ? report.description.toLowerCase().includes(searchQuery.value.toLowerCase()) : true

    return matchesCategory && matchesService && matchesStatus && matchesSearchQuery
  })
})

// Function to open the modal with specific report details
const viewReport = (report) => {
  currentReport.value = report
  showModal.value = true
}

// Function to close the modal
const closeModal = () => {
  showModal.value = false
  currentReport.value = {}
}

</script>

<style scoped>
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* Filter Section */
.filter-section {
  margin-bottom: 30px;
}

h1 {
  font-size: 32px;
  font-weight: 700;
  margin-bottom: 20px;
}

.filters {
  display: flex;
  gap: 15px;
  flex-wrap: wrap;
  justify-content: flex-start;
}

.filter-input,
.filter-select {
  padding: 10px;
  font-size: 16px;
  border: 1px solid #ccc;
  border-radius: 8px;
  width: 220px;
}

.filter-input::placeholder,
.filter-select {
  color: #666;
}

.filter-input {
  width: 250px;
}

/* User Info Styling */
.user-info {
  display: flex;
  align-items: center;
  margin-bottom: 10px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  margin-right: 10px;
}

.user-name {
  font-weight: 600;
}

/* Reports List */
.reports-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
  gap: 20px;
}

.report-item {
  background-color: #fff;
  padding: 15px;
  border-radius: 12px;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
  height: 100%;
}

.report-item:hover {
  transform: translateY(-5px);
}

button {
  padding: 10px 20px;
  background-color: #007bff;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

button:hover {
  background-color: #0056b3;
}
</style>
