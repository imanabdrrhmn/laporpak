<template>
  <AppLayout>
    <Head title="Cari Laporan" />
    <div class="search-container">
      <h2 class="mb-4">Pencarian Laporan Penipuan</h2>

      <!-- Filter and search section -->
      <div class="filter-bar">
        <div class="row g-3 align-items-center">
          <div class="col-lg-6">
            <div class="input-group">
              <span class="input-group-text bg-white border-end-0" aria-hidden="true">
                <i class="fas fa-search text-muted"></i>
              </span>
              <input
                type="text"
                class="form-control border-start-0"
                placeholder="Cari nomor telepon, email, atau website..."
                v-model.trim="searchQuery"
                @keyup.enter="searchReports"
                @input="debouncedSearch"
                aria-label="Pencarian laporan"
              >
              <button
                class="btn btn-primary"
                @click="searchReports"
                :disabled="loading"
                aria-label="Cari laporan"
              >
                <i class="fas fa-search me-1 d-none d-sm-inline" aria-hidden="true"></i>
                Cari
              </button>
            </div>
            <div v-if="searchError" class="text-danger mt-1 small" role="alert">
              {{ searchError }}
            </div>
          </div>
          <div class="col-lg-6">
            <div class="row g-2">
              <div class="col-sm-4">
                <select
                  class="form-select"
                  v-model="typeFilter"
                  aria-label="Filter jenis laporan"
                >
                  <option value="all">Semua Jenis</option>
                  <option value="Telepon">Telepon</option>
                  <option value="Email">Email</option>
                  <option value="Website">Website</option>
                  <option value="WhatsApp">WhatsApp</option>
                </select>
              </div>
              <div class="col-sm-4">
                <select
                  class="form-select"
                  v-model="statusFilter"
                  aria-label="Filter status laporan"
                >
                  <option value="all">Semua Status</option>
                  <option value="process">Diproses</option>
                  <option value="completed">Bersih</option>
                  <option value="rejected">Berbahaya</option>
                </select>
              </div>
              <div class="col-sm-4">
                <select
                  class="form-select"
                  v-model="sortOrder"
                  aria-label="Urutkan laporan"
                >
                  <option value="newest">Terbaru</option>
                  <option value="oldest">Terlama</option>
                  <option value="most_reports">Terbanyak Dilaporkan</option>
                  <option value="highest_rating">Rating Tertinggi</option>
                </select>
              </div>
            </div>
          </div>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="active-filters" v-if="hasActiveFilters">
            <span class="me-2">Filter aktif:</span>
            <span 
              v-if="searchQuery" 
              class="badge bg-light text-dark me-1 active-filter"
            >
              "{{ searchQuery }}"
              <button @click="clearSearch" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter pencarian"></button>
            </span>
            <span 
              v-if="typeFilter !== 'all'" 
              class="badge bg-light text-dark me-1 active-filter"
            >
              {{ typeFilter }}
              <button @click="typeFilter = 'all'" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter jenis"></button>
            </span>
            <span 
              v-if="statusFilter !== 'all'" 
              class="badge bg-light text-dark me-1 active-filter"
            >
              {{ getFilterStatusLabel(statusFilter) }}
              <button @click="statusFilter = 'all'" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter status"></button>
            </span>
          </div>
          <div>
            <button
              class="btn btn-outline-secondary btn-sm"
              @click="resetFilters"
              aria-label="Reset filter"
              :disabled="!hasActiveFilters"
            >
              <i class="fas fa-undo-alt me-1" aria-hidden="true"></i>
              Reset Filter
            </button>
          </div>
        </div>
      </div>

      <!-- Results stats -->
      <div v-if="!loading && filteredReports.length > 0" class="results-stats mb-3">
        <div class="d-flex justify-content-between align-items-center">
          <p class="mb-0">
            Menampilkan {{ paginatedReports.length }} dari {{ filteredReports.length }} hasil
          </p>
          <div class="d-flex align-items-center">
            <label for="itemsPerPage" class="me-2 mb-0">Tampilkan:</label>
            <select
              id="itemsPerPage"
              class="form-select form-select-sm"
              v-model="itemsPerPage"
              style="width: auto"
              aria-label="Jumlah item per halaman"
            >
              <option value="6">6</option>
              <option value="12">12</option>
              <option value="24">24</option>
              <option value="48">48</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Loading skeleton -->
      <div v-if="loading" class="row">
        <div v-for="n in 6" :key="n" class="col-md-6 col-lg-4">
          <div class="card skeleton-card">
            <div class="skeleton-type-indicator"></div>
            <div class="card-header skeleton-header"></div>
            <div class="card-body">
              <div class="skeleton-title"></div>
              <div class="skeleton-text"></div>
              <div class="skeleton-text"></div>
              <div class="skeleton-footer"></div>
            </div>
          </div>
        </div>
      </div>

      <!-- No results message -->
      <div v-if="!loading && filteredReports.length === 0" class="no-results">
        <div class="text-center">
          <i class="fas fa-search fa-3x text-muted mb-3" aria-hidden="true"></i>
          <h5>Tidak ada hasil ditemukan</h5>
          <p class="text-muted">
            Coba gunakan kata kunci yang berbeda atau reset filter pencarian.
          </p>
          <button
            class="btn btn-outline-primary mt-2"
            @click="resetFilters"
            aria-label="Reset semua filter"
          >
            <i class="fas fa-undo-alt me-1" aria-hidden="true"></i>
            Reset Filter
          </button>
        </div>
      </div>

      <!-- Results grid -->
      <transition-group name="reports-fade" tag="div" class="row">
        <div
          v-for="(report, index) in paginatedReports"
          :key="report.id || (report.contact + index)"
          class="col-md-6 col-lg-4"
        >
          <div 
            class="card report-card"
            :class="{ 'report-card--danger': report.status === 'danger' }"
          >
            <div :class="getTypeClass(report.type)" class="report-card__type-indicator"></div>
            <div class="card-header report-card__header">
              <div class="report-card__type">
                <i :class="getTypeIcon(report.type)" class="me-2" aria-hidden="true"></i>
                <span>{{ report.type }}</span>
              </div>
              <span
                :class="getStatusClass(report.status)"
                class="report-card__status-badge"
              >
                {{ getStatusLabel(report.status) }}
              </span>
            </div>
            <div class="card-body report-card__body">
              <h5 class="card-title report-card__title">{{ report.contact }}</h5>
              <p class="card-text report-card__description">{{ truncateText(report.description, 100) }}</p>

              <div class="report-card__rating mb-3">
                <div class="report-card__rating-stars" aria-label="Rating: {{ report.rating }} dari 5">
                  <i
                    v-for="i in 5"
                    :key="'star-' + i"
                    :class="getStarClass(report.rating, i)"
                    aria-hidden="true"
                  ></i>
                </div>
                <span class="report-card__report-count">
                  <i class="fas fa-flag me-1" aria-hidden="true"></i>
                  {{ report.reportCount }} Laporan
                </span>
              </div>

              <div class="report-card__footer">
                <small class="text-muted">
                  <i class="far fa-calendar-alt me-1" aria-hidden="true"></i>
                  {{ report.date }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </transition-group>

      <!-- Pagination -->
      <nav v-if="totalPages > 1" aria-label="Navigasi halaman laporan">
        <ul class="pagination justify-content-center">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button 
              class="page-link" 
              @click="changePage(currentPage - 1)" 
              :disabled="currentPage === 1"
              aria-label="Halaman sebelumnya"
            >
              <i class="fas fa-chevron-left" aria-hidden="true"></i>
              <span class="d-none d-sm-inline ms-1">Sebelumnya</span>
            </button>
          </li>
          
          <li v-if="showStartEllipsis" class="page-item disabled">
            <span class="page-link">...</span>
          </li>
          
          <li 
            v-for="page in visiblePageNumbers" 
            :key="page" 
            class="page-item"
            :class="{ active: page === currentPage }"
          >
            <button 
              class="page-link" 
              @click="changePage(page)"
              :aria-current="page === currentPage ? 'page' : undefined"
            >
              {{ page }}
            </button>
          </li>
          
          <li v-if="showEndEllipsis" class="page-item disabled">
            <span class="page-link">...</span>
          </li>
          
          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button 
              class="page-link" 
              @click="changePage(currentPage + 1)" 
              :disabled="currentPage === totalPages"
              aria-label="Halaman selanjutnya"
            >
              <span class="d-none d-sm-inline me-1">Selanjutnya</span>
              <i class="fas fa-chevron-right" aria-hidden="true"></i>
            </button>
          </li>
        </ul>
      </nav>

      <!-- Success toast -->
      <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 11">
        <div 
          id="successToast" 
          class="toast" 
          role="alert" 
          aria-live="assertive" 
          aria-atomic="true"
          data-bs-delay="3000"
        >
          <div class="toast-header bg-success text-white">
            <i class="fas fa-check-circle me-2" aria-hidden="true"></i>
            <strong class="me-auto">Berhasil</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
            Laporan berhasil dikirim. Terima kasih atas kontribusi Anda!
          </div>
        </div>
      </div>

      <Section :showSearch="false" />

      <!-- Feedback component -->
      <Feedback :feedbacks="feedbacks" />
    </div>
  </AppLayout>
</template>

<script>
import { debounce } from 'lodash';
import AppLayout from '@/Layouts/AppLayout.vue';
import Feedback from '@/Components/Feedback.vue';
import { usePage, Head } from '@inertiajs/vue3';
import Section from '@/Components/Section.vue';



const STATUS_LABELS = {
  danger: 'Sangat Berbahaya',
  warning: 'Berbahaya',
  safe: 'Bersih',
};

const STATUS_FILTER_MAP = {
  process: 'warning',
  completed: 'safe',
  rejected: 'danger',
};

const FILTER_STATUS_LABELS = {
  process: 'Diproses',
  completed: 'Bersih',
  rejected: 'Berbahaya',
};

const TYPE_CLASSES = {
  Telepon: 'report-card__type-indicator--phone',
  Email: 'report-card__type-indicator--email',
  Website: 'report-card__type-indicator--web',
  WhatsApp: 'report-card__type-indicator--whatsapp',
};

const TYPE_ICONS = {
  Telepon: 'fas fa-phone text-danger',
  Email: 'fas fa-envelope text-primary',
  Website: 'fas fa-globe text-warning',
  WhatsApp: 'fab fa-whatsapp text-success',
};

const STATUS_CLASSES = {
  danger: 'report-card__status-badge--danger',
  warning: 'report-card__status-badge--warning',
  safe: 'report-card__status-badge--safe',
};

const STATUS_BG_CLASSES = {
  danger: 'bg-danger-subtle text-danger',
  warning: 'bg-warning-subtle text-warning-emphasis',
  safe: 'bg-success-subtle text-success',
};

function parseReportDate(dateStr) {
  const parts = dateStr.split(' ');
  const months = {
    Jan: 0, Feb: 1, Mar: 2, Apr: 3, Mei: 4, Jun: 5,
    Jul: 6, Agu: 7, Sep: 8, Okt: 9, Nov: 10, Des: 11,
  };
  return new Date(parts[2], months[parts[1]], parts[0]);
}

export default {
  name: 'FraudReportSearch',
  components: {
    AppLayout,
    Feedback,
    Section,
    Head,
  },
  data() {
    return {
      searchQuery: '',
      statusFilter: 'all',
      typeFilter: 'all',
      sortOrder: 'newest',
      reports: [],
      loading: false,
      searchError: '',
      itemsPerPage: 6,
      currentPage: 1,
      sampleReports: [
        // ... (data sampleReports tetap sama, tidak dipotong di sini)
      ],
    };
  },
  computed: {
    feedbacks() {
      return usePage().props.feedbacks || [];
    },
    filteredReports() {
      let filtered = [...this.sampleReports];

      if (this.searchQuery) {
        const query = this.searchQuery.toLowerCase();
        filtered = filtered.filter(report =>
          report.contact.toLowerCase().includes(query) ||
          report.description.toLowerCase().includes(query)
        );
      }

      if (this.typeFilter !== 'all') {
        filtered = filtered.filter(report => report.type === this.typeFilter);
      }

      if (this.statusFilter !== 'all') {
        const status = STATUS_FILTER_MAP[this.statusFilter];
        filtered = filtered.filter(report => report.status === status);
      }

      switch (this.sortOrder) {
        case 'newest':
          filtered.sort((a, b) => parseReportDate(b.date) - parseReportDate(a.date));
          break;
        case 'oldest':
          filtered.sort((a, b) => parseReportDate(a.date) - parseReportDate(b.date));
          break;
        case 'most_reports':
          filtered.sort((a, b) => b.reportCount - a.reportCount);
          break;
        case 'highest_rating':
          filtered.sort((a, b) => b.rating - a.rating);
          break;
      }

      return filtered;
    },
    paginatedReports() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      const end = start + this.itemsPerPage;
      return this.filteredReports.slice(start, end);
    },
    totalPages() {
      return Math.ceil(this.filteredReports.length / this.itemsPerPage);
    },
    visiblePageNumbers() {
      const maxPages = 5;
      const pages = [];
      let startPage = Math.max(1, this.currentPage - Math.floor(maxPages / 2));
      let endPage = Math.min(this.totalPages, startPage + maxPages - 1);

      if (endPage - startPage + 1 < maxPages) {
        startPage = Math.max(1, endPage - maxPages + 1);
      }

      for (let i = startPage; i <= endPage; i++) {
        pages.push(i);
      }
      return pages;
    },
    showStartEllipsis() {
      return this.visiblePageNumbers[0] > 1;
    },
    showEndEllipsis() {
      return this.visiblePageNumbers[this.visiblePageNumbers.length - 1] < this.totalPages;
    },
    hasActiveFilters() {
      return this.searchQuery || this.typeFilter !== 'all' || this.statusFilter !== 'all';
    },
  },
  methods: {
    searchReports() {
      if (this.searchQuery.length < 3 && this.searchQuery.length > 0) {
        this.searchError = 'Kata kunci pencarian minimal 3 karakter';
        return;
      }
      this.searchError = '';
      this.currentPage = 1;
      this.loading = true;
      setTimeout(() => {
        this.reports = this.sampleReports;
        this.loading = false;
      }, 500);
    },
    debouncedSearch: debounce(function () {
      this.searchReports();
    }, 300),
    clearSearch() {
      this.searchQuery = '';
      this.searchError = '';
      this.currentPage = 1;
      this.searchReports();
    },
    resetFilters() {
      this.searchQuery = '';
      this.typeFilter = 'all';
      this.statusFilter = 'all';
      this.sortOrder = 'newest';
      this.currentPage = 1;
      this.searchError = '';
      this.searchReports();
    },
    changePage(page) {
      if (page >= 1 && page <= this.totalPages) {
        this.currentPage = page;
      }
    },
    getTypeClass(type) {
      return TYPE_CLASSES[type] || '';
    },
    getTypeIcon(type) {
      return TYPE_ICONS[type] || 'fas fa-question';
    },
    getStatusClass(status) {
      return STATUS_CLASSES[status] || '';
    },
    getStatusLabel(status) {
      return STATUS_LABELS[status] || 'Tidak Diketahui';
    },
    getStatusBackgroundClass(status) {
      return STATUS_BG_CLASSES[status] || '';
    },
    getFilterStatusLabel(status) {
      return FILTER_STATUS_LABELS[status] || 'Semua';
    },
    getStarClass(rating, index) {
      if (rating >= index) {
        return 'fas fa-star text-warning';
      } else if (rating >= index - 0.5) {
        return 'fas fa-star-half-alt text-warning';
      }
      return 'far fa-star text-warning';
    },
    truncateText(text, length) {
      if (text.length <= length) return text;
      return text.substring(0, length) + '...';
    }
  },
  mounted() {
    this.searchReports();
  }
};
</script>


<style scoped>
.search-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.filter-bar {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
  margin-bottom: 20px;
}

.active-filter .btn-close-sm {
  font-size: 0.6rem;
  vertical-align: middle;
}

.results-stats {
  font-size: 0.9rem;
  color: #6c757d;
}

.skeleton-card {
  height: 200px;
  background: #f8f9fa;
  animation: pulse 1.5s infinite;
}

.skeleton-type-indicator,
.skeleton-header,
.skeleton-title,
.skeleton-text,
.skeleton-footer {
  background: #e9ecef;
  border-radius: 4px;
  margin-bottom: 10px;
}

.skeleton-type-indicator {
  width: 10px;
  height: 100%;
  position: absolute;
  left: 0;
}

.skeleton-header {
  height: 20px;
  width: 60%;
}

.skeleton-title {
  height: 24px;
  width: 80%;
}

.skeleton-text {
  height: 16px;
  width: 100%;
}

.skeleton-footer {
  height: 20px;
  width: 40%;
}

@keyframes pulse {
  0% {
    opacity: 1;
  }
  50% {
    opacity: 0.5;
  }
  100% {
    opacity: 1;
  }
}

.no-results {
  text-align: center;
  padding: 40px 0;
}

.report-card {
  position: relative;
  margin-bottom: 20px;
  transition: transform 0.2s;
}

.report-card:hover {
  transform: translateY(-5px);
}

.report-card--danger {
  border-color: #dc3545;
}

.report-card__type-indicator {
  width: 10px;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
}

.report-card__type-indicator--phone {
  background: #dc3545;
}

.report-card__type-indicator--email {
  background: #007bff;
}

.report-card__type-indicator--web {
  background: #ffc107;
}

.report-card__type-indicator--whatsapp {
  background: #28a745;
}

.report-card__header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.report-card__status-badge {
  padding: 5px 10px;
  border-radius: 12px;
  font-size: 0.8rem;
}

.report-card__status-badge--danger {
  background: #dc3545;
  color: white;
}

.report-card__status-badge--warning {
  background: #ffc107;
  color: #212529;
}

.report-card__status-badge--safe {
  background: #28a745;
  color: white;
}

.report-card__rating-stars { 
  font-size: 0.9rem;
}

.report-card__report-count {
  font-size: 0.85rem;
  color: #6c757d;
}

.report-card__footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.reports-fade-enter-active,
.reports-fade-leave-active {
  transition: opacity 0.3s, transform 0.3s;
}

.reports-fade-enter-from,
.reports-fade-leave-to {
  opacity: 0;
  transform: translateY(20px);
}

.pagination .page-link {
  cursor: pointer;
}
</style>