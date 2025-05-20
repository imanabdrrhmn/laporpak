<template>
  <AdminLayout>

  <div class="container">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
      <div class="header-content">
        <h1>Manajemen Laporan</h1>
        <div class="stats-bar">
          <div class="stat-item">
            <span class="stat-value">{{ reportStats.total }}</span>
            <span class="stat-label">Total</span>
          </div>
          <div class="stat-item pending">
            <span class="stat-value">{{ reportStats.pending }}</span>
            <span class="stat-label">Pending</span>
          </div>
          <div class="stat-item approved">
            <span class="stat-value">{{ reportStats.approved }}</span>
            <span class="stat-label">Approved</span>
          </div>
          <div class="stat-item rejected">
            <span class="stat-value">{{ reportStats.rejected }}</span>
            <span class="stat-label">Rejected</span>
          </div>
          <div class="stat-item published">
            <span class="stat-value">{{ reportStats.published }}</span>
            <span class="stat-label">Published</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Filter Section -->
    <div class="filter-section">
      <div class="section-header">
        <h2>Laporan Masuk</h2>
        <div class="view-toggle">
          <button 
            @click="viewMode = 'grid'" 
            :class="['toggle-btn', viewMode === 'grid' ? 'active' : '']"
            title="Grid View"
          >
            <i class="fas fa-th-large"></i>
          </button>
          <button 
            @click="viewMode = 'list'" 
            :class="['toggle-btn', viewMode === 'list' ? 'active' : '']"
            title="List View"
          >
            <i class="fas fa-list"></i>
          </button>
        </div>
      </div>
      
      <div class="search-bar">
        <div class="search-wrapper">
          <i class="fas fa-search search-icon"></i>
          <input
            v-model="searchQuery"
            type="text"
            class="search-input"
            placeholder="Cari laporan..."
          />
          <button v-if="searchQuery" @click="searchQuery = ''" class="clear-btn">
            <i class="fas fa-times"></i>
          </button>
        </div>
      </div>
      
      <div class="filters">
        <div class="dropdown-filter">
          <select v-model="selectedCategory" class="filter-select">
            <option value="">Semua Kategori</option>
            <option v-for="category in categories" :key="category" :value="category">
              {{ category }}
            </option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
        
        <div class="dropdown-filter">
          <select v-model="selectedService" class="filter-select">
            <option value="">Semua Layanan</option>
            <option v-for="service in services" :key="service" :value="service">
              {{ service }}
            </option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
        
        <div class="dropdown-filter">
          <select v-model="selectedStatus" class="filter-select">
            <option value="">Semua Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="published">Published</option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
        
        <div class="dropdown-filter">
          <select v-model="sortDirection" class="filter-select">
            <option value="desc">Terbaru</option>
            <option value="asc">Terlama</option>
          </select>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </div>
        
        <button @click="resetFilters" class="reset-btn" :disabled="!hasActiveFilters">
          <i class="fas fa-sync-alt"></i> Reset
        </button>
      </div>
      
      <div v-if="hasActiveFilters" class="active-filters">
        <div class="filter-chips">
          <div v-if="selectedCategory" class="filter-chip">
            Kategori: {{ selectedCategory }}
            <button @click="selectedCategory = ''" class="remove-filter">×</button>
          </div>
          <div v-if="selectedService" class="filter-chip">
            Layanan: {{ selectedService }}
            <button @click="selectedService = ''" class="remove-filter">×</button>
          </div>
          <div v-if="selectedStatus" class="filter-chip">
            Status: {{ capitalize(selectedStatus) }}
            <button @click="selectedStatus = ''" class="remove-filter">×</button>
          </div>
          <div v-if="searchQuery" class="filter-chip">
            Pencarian: "{{ searchQuery }}"
            <button @click="searchQuery = ''" class="remove-filter">×</button>
          </div>
        </div>
        <p class="results-count">{{ filteredReports.length }} laporan ditemukan</p>
      </div>
    </div>

    <!-- Reports Display -->
    <div v-if="paginatedReports.length" :class="['reports-container', viewMode]">
      <template v-if="viewMode === 'grid'">
        <!-- Grid View -->
        <div class="reports-grid">
          <div v-for="report in paginatedReports" :key="report.id" class="report-card">
            <!-- Report Badge -->
            <div :class="['report-badge', report.status]"></div>
            
            <!-- Report Header -->
            <div class="report-header">
              <div class="user-info">
                <img :src="report.user.avatar_url" alt="User Avatar" class="user-avatar" />
                <div class="user-details">
                  <span class="user-name">{{ report.user.name }}</span>
                  <span class="timestamp">{{ formatDate(report.created_at) }}</span>
                </div>
              </div>
              <div class="report-labels">
                <span :class="['status-badge', report.status]">
                  {{ capitalize(report.status) }}
                </span>
              </div>
            </div>
            
            <!-- Report Content -->
            <div class="report-content">
              <div class="report-meta">
                <div class="meta-item">
                  <i class="fas fa-tag meta-icon"></i>
                  <span>{{ report.category }}</span>
                </div>
                <div class="meta-item">
                  <i class="fas fa-cog meta-icon"></i>
                  <span>{{ report.service }}</span>
                </div>
                <div v-if="report.service === 'Penipuan'" class="meta-item">
                  <i class="fas fa-exclamation-triangle meta-icon"></i>
                  <span>{{ report.source || 'Tidak Ada Sumber' }}</span>
                </div>
              </div>
              <p class="description">{{ truncate(report.description, 120) }}</p>
            </div>
            
            <!-- Report Actions -->
            <div class="report-actions">
              <button @click="viewReport(report)" class="view-button">
                <i class="fas fa-eye"></i> Lihat Detail
              </button>
              <div class="action-buttons">
                <button 
                  @click="quickAction(report, 'approved')" 
                  :class="['quick-action', 'approve', report.status === 'approved' ? 'active' : '', { 'loading': loading[report.id] }]" 
                  title="Approve"
                  :disabled="loading[report.id]"
                >
                  <span v-if="loading[report.id]" class="spinner"></span>
                  Approve
                </button>
                <button 
                  @click="quickAction(report, 'rejected')" 
                  :class="['quick-action', 'reject', report.status === 'rejected' ? 'active' : '', { 'loading': loading[report.id] }]" 
                  title="Reject"
                  :disabled="loading[report.id]"
                >
                  <span v-if="loading[report.id]" class="spinner"></span>
                  Reject
                </button>
                <button 
                  @click="quickAction(report, 'published')" 
                  :class="['quick-action', 'publish', report.status === 'published' ? 'active' : '', { 'loading': loading[report.id] }]" 
                  title="Publish"
                  :disabled="loading[report.id]"
                >
                  <span v-if="loading[report.id]" class="spinner"></span>
                  Publish
                </button>
              </div>
            </div>
          </div>
        </div>
      </template>
      
      <template v-else>
        <!-- List View -->
        <table class="reports-table">
          <thead>
            <tr>
              <th>No</th>
              <th>Pelapor</th>
              <th>Kategori</th>
              <th>Layanan</th>
              <th>Status</th>
              <th>Deskripsi</th>
              <th>Tanggal</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(report, index) in paginatedReports" :key="report.id" :class="['report-row', report.status]">
              <td class="number-cell">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
              <td>
                <div class="user-info">
                  <img :src="report.user.avatar_url" alt="User Avatar" class="user-avatar-small" />
                  <span class="user-name">{{ report.user.name }}</span>
                </div>
              </td>
              <td>{{ report.category }}</td>
              <td>{{ report.service }}</td>
              <td>
                <span :class="['status-badge', report.status]">
                  {{ capitalize(report.status) }}
                </span>
              </td>
              <td class="description-cell">{{ truncate(report.description, 60) }}</td>
              <td>{{ formatDateShort(report.created_at) }}</td>
              <td>
                <div class="table-actions">
                  <button @click="viewReport(report)" class="table-action view" title="Lihat Detail">
                    <i class="fas fa-eye"></i>
                  </button>
                  <button 
                    @click="quickAction(report, 'approved')" 
                    :class="['table-action', 'approve', report.status === 'approved' ? 'active' : '', { 'loading': loading[report.id] }]" 
                    title="Approve"
                    :disabled="loading[report.id]"
                  >
                    <span v-if="loading[report.id]" class="spinner"></span>
                    Approve
                  </button>
                  <button 
                    @click="quickAction(report, 'rejected')" 
                    :class="['table-action', 'reject', report.status === 'rejected' ? 'active' : '', { 'loading': loading[report.id] }]" 
                    title="Reject"
                    :disabled="loading[report.id]"
                  >
                    <span v-if="loading[report.id]" class="spinner"></span>
                    Reject
                  </button>
                  <button 
                    @click="quickAction(report, 'published')" 
                    :class="['table-action', 'publish', report.status === 'published' ? 'active' : '', { 'loading': loading[report.id] }]" 
                    title="Publish"
                    :disabled="loading[report.id]"
                  >
                    <span v-if="loading[report.id]" class="spinner"></span>
                    Publish
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </template>
    </div>

    <div v-else class="no-results">
      <div class="empty-state">
        <i class="fas fa-folder-open empty-icon"></i>
        <h3>Tidak ada laporan ditemukan</h3>
        <p>Coba ubah filter pencarian atau reset filter</p>
        <button @click="resetFilters" class="reset-btn-empty">Reset Filter</button>
      </div>
    </div>
    
    <!-- Pagination -->
    <div v-if="filteredReports.length > 0" class="pagination">
      <button :disabled="currentPage === 1" @click="currentPage--" class="page-btn">
        <i class="fas fa-chevron-left"></i>
      </button>
      <span class="page-info">{{ currentPage }} dari {{ totalPages }}</span>
      <button :disabled="currentPage === totalPages" @click="currentPage++" class="page-btn">
        <i class="fas fa-chevron-right"></i>
      </button>
    </div>

    <!-- Enhanced Modal -->
    <Modal 
      :report="currentReport" 
      :isVisible="showModal" 
      @close="closeModal"
      @update-status="updateReportStatus" 
    />
  </div>
  </AdminLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, Head, router } from '@inertiajs/vue3';
import Modal from '@/Pages/Pelaporan/LaporanDetailModal.vue';
import AdminLayout from '@/Layouts/AdminLayout.vue';

const props = defineProps({
    reports: {
        type: Array,
        default: () => []
    }
});

// Core state
const page = usePage();
const user = computed(() => page.props.auth?.user || {});
const searchQuery = ref('');
const selectedCategory = ref('');
const selectedService = ref('');
const selectedStatus = ref('');
const sortDirection = ref('desc');
const viewMode = ref('grid'); // 'grid' or 'list'
const currentPage = ref(1);
const itemsPerPage = ref(20); // Maksimal 20 data per halaman
const loading = ref({}); // State untuk loading per report ID
const showModal = ref(false);
const currentReport = ref({});

// Computed properties for filtering
const categories = computed(() => {
    return [...new Set(props.reports.map(report => report.category))].filter(Boolean);
});

const services = computed(() => {
    return [...new Set(props.reports.map(report => report.service))].filter(Boolean);
});

const hasActiveFilters = computed(() => {
    return searchQuery.value || selectedCategory.value || selectedService.value || selectedStatus.value;
});

const filteredReports = computed(() => {
    const filtered = props.reports
        .filter(report => {
            const matchesCategory = selectedCategory.value ? report.category === selectedCategory.value : true;
            const matchesService = selectedService.value ? report.service === selectedService.value : true;
            const matchesStatus = selectedStatus.value ? report.status === selectedStatus.value : true;
            
            const matchesSearch = searchQuery.value
                ? (
                    report.description.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                    report.category.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                    report.service.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                    report.user.name.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
                    (report.source && report.source.toLowerCase().includes(searchQuery.value.toLowerCase()))
                )
                : true;
                
            return matchesCategory && matchesService && matchesStatus && matchesSearch;
        })
        .sort((a, b) => {
            return sortDirection.value === 'desc'
                ? new Date(b.created_at) - new Date(a.created_at)
                : new Date(a.created_at) - new Date(b.created_at);
        });
        
    return filtered;
});

// Paginated reports
const paginatedReports = computed(() => {
    const startIndex = (currentPage.value - 1) * itemsPerPage.value;
    const endIndex = startIndex + itemsPerPage.value;
    return filteredReports.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
    return Math.ceil(filteredReports.value.length / itemsPerPage.value) || 1;
});

// Report statistics
const reportStats = computed(() => {
    const stats = {
        total: props.reports.length,
        pending: 0,
        approved: 0,
        rejected: 0,
        published: 0
    };
    
    props.reports.forEach(report => {
        if (report.status in stats) {
            stats[report.status]++;
        }
    });
    
    return stats;
});

// Methods
const viewReport = (report) => {
    currentReport.value = { ...report };
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    setTimeout(() => {
        currentReport.value = {};
    }, 300);
};

const resetFilters = () => {
    searchQuery.value = '';
    selectedCategory.value = '';
    selectedService.value = '';
    selectedStatus.value = '';
    currentPage.value = 1;
};

const quickAction = (report, action) => {
    const reportId = report.id;
    let endpoint, newStatus;

    if (action === 'approved') {
        endpoint = `/pelaporan/${reportId}/terima`;
        newStatus = report.status === 'approved' ? 'pending' : 'approved';
    } else if (action === 'rejected') {
        endpoint = `/pelaporan/${reportId}/tolak`;
        newStatus = report.status === 'rejected' ? 'pending' : 'rejected';
    } else if (action === 'published') {
        endpoint = `/pelaporan/${reportId}/publikasikan`;
        newStatus = report.status === 'published' ? 'pending' : 'published';
    }

    // Set loading state
    loading.value[reportId] = true;

    // Send PATCH request
    router.patch(endpoint, {}, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            // Update status locally
            updateReportStatus(reportId, newStatus);
            loading.value[reportId] = false;
        },
        onError: (errors) => {
            // Show error message
            alert('Gagal memperbarui status: ' + (errors.error || 'Terjadi kesalahan'));
            loading.value[reportId] = false;
        },
    });
};

const updateReportStatus = (reportId, newStatus) => {
    const reportIndex = props.reports.findIndex(r => r.id === reportId);
    if (reportIndex !== -1) {
        props.reports[reportIndex].status = newStatus;
        
        if (currentReport.value.id === reportId) {
            currentReport.value.status = newStatus;
        }
    }
};

// Format dates
const formatDate = (iso) => {
    const d = new Date(iso);
    return d.toLocaleString('id-ID', {
        day: 'numeric', month: 'long', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};

const formatDateShort = (iso) => {
    const d = new Date(iso);
    return d.toLocaleString('id-ID', {
        day: 'numeric', month: 'short',
        hour: '2-digit', minute: '2-digit'
    });
};

// Text utilities
const truncate = (text, max) => {
    return text && text.length > max ? text.substring(0, max) + '…' : text || '';
};

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);

// Reset page when filters change
watch([searchQuery, selectedCategory, selectedService, selectedStatus], () => {
    currentPage.value = 1;
});
</script>

<style scoped>
/* Global Styles */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 24px 16px;
}

/* Dashboard Header */
.dashboard-header {
  background: linear-gradient(135deg, #4b6cb7 0%, #182848 100%);
  border-radius: 12px;
  padding: 24px;
  margin-bottom: 24px;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  color: white;
}

.header-content h1 {
  font-size: 1.8rem;
  font-weight: 700;
  margin-bottom: 16px;
}

.stats-bar {
  display: flex;
  gap: 24px;
  flex-wrap: wrap;
}

.stat-item {
  display: flex;
  flex-direction: column;
  justify-content: center;
  padding: 12px 20px;
  background-color: rgba(255, 255, 255, 0.1);
  border-radius: 10px;
  min-width: 100px;
  text-align: center;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: 700;
  margin-bottom: 4px;
}

.stat-label {
  font-size: 0.8rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  opacity: 0.9;
}

.stat-item.pending { background-color: rgba(255, 193, 7, 0.2); }
.stat-item.approved { background-color: rgba(40, 167, 69, 0.2); }
.stat-item.rejected { background-color: rgba(220, 53, 69, 0.2); }
.stat-item.published { background-color: rgba(13, 110, 253, 0.2); }

/* Filter Section */
.filter-section {
  background-color: #ffffff;
  padding: 20px;
  border-radius: 12px;
  box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
  margin-bottom: 24px;
}

.section-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 16px;
}

.section-header h2 {
  font-size: 1.4rem;
  font-weight: 600;
  color: #333;
}

.view-toggle {
  display: flex;
  gap: 8px;
}

.toggle-btn {
  background-color: #e9ecef;
  border: none;
  border-radius: 6px;
  padding: 8px 12px;
  cursor: pointer;
  color: #4b6cb7;
  transition: all 0.2s;
}

.toggle-btn.active {
  background-color: #4b6cb7;
  color: white;
}

.search-bar {
  margin-bottom: 16px;
}

.search-wrapper {
  position: relative;
  width: 100%;
}

.search-input {
  width: 100%;
  padding: 12px 40px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  font-size: 16px;
  background-color: #f9f9f9;
  transition: all 0.3s ease;
}

.search-input:focus {
  outline: none;
  border-color: #4b6cb7;
  background-color: #fff;
  box-shadow: 0 0 0 3px rgba(75, 108, 183, 0.1);
}

.search-icon {
  position: absolute;
  left: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #4b6cb7;
}

.clear-btn {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  background: none;
  border: none;
  color: #4b6cb7;
  cursor: pointer;
  font-size: 14px;
}

.filters {
  display: flex;
  gap: 12px;
  flex-wrap: wrap;
  margin-bottom: 16px;
}

.dropdown-filter {
  position: relative;
  flex: 1;
  min-width: 160px;
}

.filter-select {
  width: 100%;
  padding: 12px 36px 12px 16px;
  font-size: 14px;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  background-color: #f9f9f9;
  color: #333;
  appearance: none;
  cursor: pointer;
  transition: all 0.2s ease;
}

.filter-select:focus {
  outline: none;
  border-color: #4b6cb7;
  background-color: #fff;
  box-shadow: 0 0 0 3px rgba(75, 108, 183, 0.1);
}

.dropdown-icon {
  position: absolute;
  right: 14px;
  top: 50%;
  transform: translateY(-50%);
  color: #4b6cb7;
  pointer-events: none;
}

.reset-btn {
  padding: 12px 20px;
  background-color: #e9ecef;
  color: #4b6cb7;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  display: flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}

.reset-btn:hover:not(:disabled) {
  background-color: #4b6cb7;
  color: #fff;
}

.reset-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.active-filters {
  background-color: #f9f9f9;
  padding: 12px 16px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.filter-chips {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
  margin-bottom: 8px;
}

.filter-chip {
  background-color: #e9ecef;
  border-radius: 30px;
  padding: 6px 14px;
  font-size: 13px;
  color: #4b6cb7;
  display: flex;
  align-items: center;
  gap: 8px;
}

.remove-filter {
  background: none;
  border: none;
  color: #4b6cb7;
  font-size: 16px;
  cursor: pointer;
  line-height: 1;
  padding: 0;
}

.results-count {
  font-size: 14px;
  color: #4b6cb7;
  margin: 0;
}

/* Reports Container */
.reports-container {
  margin-bottom: 24px;
}

.reports-container.grid .reports-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: 20px;
}

/* Grid View */
.report-card {
  background-color: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  border: 1px solid #eee;
  transition: all 0.3s ease;
  position: relative;
  min-height: 380px;
  display: flex;
  flex-direction: column;
}

.report-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.report-badge {
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}

.report-badge.pending { background-color: #ffc107; }
.report-badge.approved { background-color: #28a745; }
.report-badge.rejected { background-color: #dc3545; }
.report-badge.published { background-color: #0d6efd; }

.report-header {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.user-avatar-small {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  font-size: 14px;
  color: #333;
}

.timestamp {
  font-size: 12px;
  color: #777;
}

.report-labels {
  display: flex;
  gap: 8px;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 30px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
}

.status-badge.pending {
  background-color: #fff8e1;
  color: #ffa000;
}

.status-badge.approved {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.status-badge.rejected {
  background-color: #ffebee;
  color: #c62828;
}

.status-badge.published {
  background-color: #e3f2fd;
  color: #1565c0;
}

.report-content {
  padding: 16px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.report-meta {
  margin-bottom: 12px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.meta-item {
  display: flex;
  align-items: center;
  margin-bottom: 6px;
  font-size: 13px;
  color: #555;
}

.meta-icon {
  width: 16px;
  margin-right: 8px;
  color: #777;
}

.description {
  font-size: 14px;
  line-height: 1.5;
  color: #444;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.report-actions {
  padding: 16px;
  border-top: 1px solid #f0f0f0;
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex-shrink: 0;
}

.view-button {
  width: 100%;
  padding: 10px;
  background-color: #4b6cb7;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}

.view-button:hover {
  background-color: #3c57a5;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.quick-action {
  flex: 1;
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  background-color: #f5f5f5;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 12px;
  font-weight: 600;
  text-align: center;
  position: relative;
}

.quick-action:hover {
  filter: brightness(0.95);
}

.quick-action.approve {
  color: #28a745;
}

.quick-action.reject {
  color: #dc3545;
}

.quick-action.publish {
  color: #0d6efd;
}

.quick-action.active {
  color: white;
}

.quick-action.approve.active {
  background-color: #28a745;
}

.quick-action.reject.active {
  background-color: #dc3545;
}

.quick-action.publish.active {
  background-color: #0d6efd;
}

.quick-action.loading {
  opacity: 0.7;
  cursor: not-allowed;
}

.quick-action .spinner {
  display: inline-block;
  width: 12px;
  height: 12px;
  border: 2px solid currentColor;
  border-radius: 50%;
  border-top-color: transparent;
  animation: spin 1s linear infinite;
  margin-right: 6px;
}

/* List View - Table */
.reports-table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.reports-table thead {
  background-color: #f5f7fa;
}

.reports-table th {
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: #444;
  border-bottom: 1px solid #eee;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.reports-table th:first-child {
  text-align: center;
}

.reports-table td {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  color: #444;
}

.number-cell {
  text-align: center;
  width: 60px;
}

.report-row {
  transition: background-color 0.2s;
}

.report-row:hover {
  background-color: #f9f9f9;
}

.report-row.pending {
  border-left: 3px solid #ffc107;
}

.report-row.approved {
  border-left: 3px solid #28a745;
}

.report-row.rejected {
  border-left: 3px solid #dc3545;
}

.report-row.published {
  border-left: 3px solid #0d6efd;
}

.description-cell {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.table-actions {
  display: flex;
  gap: 6px;
}

.table-action {
  background-color: #f5f5f5;
  border: none;
  border-radius: 4px;
  padding: 6px 12px;
  min-width: 70px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  transition: all 0.2s;
  position: relative;
}

.table-action:hover {
  filter: brightness(0.95);
}

.table-action.view {
  color: #666;
  min-width: 30px;
}

.table-action.approve {
  color: #28a745;
}

.table-action.reject {
  color: #dc3545;
}

.table-action.publish {
  color: #0d6efd;
}

.table-action.active {
  color: white;
}

.table-action.approve.active {
  background-color: #28a745;
}

.table-action.reject.active {
  background-color: #dc3545;
}

.table-action.publish.active {
  background-color: #0d6efd;
  color: white;
}

.table-action.loading {
  opacity: 0.7;
  cursor: not-allowed;
}

.table-action .spinner {
  display: inline-block;
  width: 12px;
  height: 12px;
  border: 2px solid currentColor;
  border-radius: 50%;
  border-top-color: transparent;
  animation: spin 1s linear infinite;
  margin-right: 6px;
}

/* Spinner Animation */
@keyframes spin {
  to { transform: rotate(360deg); }
}

/* Empty State */
.no-results {
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 60px 0;
}

.empty-state {
  text-align: center;
  color: #888;
  max-width: 400px;
}

.empty-icon {
  font-size: 48px;
  margin-bottom: 16px;
  opacity: 0.6;
}

.empty-state h3 {
  font-size: 18px;
  margin-bottom: 8px;
  color: #555;
}

.empty-state p {
  margin-bottom: 16px;
  font-size: 14px;
}

.reset-btn-empty {
  padding: 10px 20px;
  background-color: #0d6efd;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  cursor: pointer;
  margin: 0 auto;
}

/* Pagination */
.pagination {
  display: flex;
  justify-content: center;
  align-items: center;
  margin-top: 24px;
  gap: 16px;
}

.page-btn {
  background-color: #f0f0f0;
  border: none;
  border-radius: 6px;
  width: 40px;
  height: 40px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  color: #555;
  transition: all 0.2s;
}

.page-btn:hover:not(:disabled) {
  background-color: #e0e0e0;
}

.page-btn:disabled {
  opacity: 0.5;
  cursor: not-allowed;
}

.page-info {
  font-size: 14px;
  color: #666;
}

/* Responsive Styles */
@media (max-width: 768px) {
  .stats-bar {
    gap: 12px;
  }
  
  .stat-item {
    padding: 8px 12px;
    min-width: 80px;
  }
  
  .stat-value {
    font-size: 1.2rem;
  }
  
  .reports-container.grid .reports-grid {
    grid-template-columns: 1fr;
  }
  
  .report-card {
    min-height: 360px;
  }
  
  .filters {
    flex-direction: column;
  }
  
  .dropdown-filter {
    width: 100%;
  }
  
  .reports-table {
    display: block;
    overflow-x: auto;
  }
  
  .reports-table th, 
  .reports-table td {
    padding: 12px;
  }
  
  .description-cell {
    max-width: 150px;
  }
  
  .table-actions {
    flex-wrap: wrap;
  }
  
  .table-action {
    min-width: 60px;
    padding: 6px 8px;
  }
  
  .number-cell {
    width: 40px;
  }
}
</style>