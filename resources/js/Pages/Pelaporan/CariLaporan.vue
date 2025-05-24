<template>
  <AppLayout>
    <Head title="Cari Laporan" />
    <div class="search-container">
      <h2 class="mb-4">Pencarian Laporan Penipuan</h2>

      <!-- Filter dan pencarian -->
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
                placeholder="Cari nomor telepon, email, alamat, atau deskripsi..."
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
              <div class="col-sm-6">
                <select
                  class="form-select"
                  v-model="categoryFilter"
                  aria-label="Filter kategori laporan"
                >
                  <option value="all">Semua Kategori</option>
                  <option value="Nomor Hp">Nomor Hp</option>
                  <option value="Email">Email</option>
                  <option value="Website">Website</option>
                  <option value="WhatsApp">WhatsApp</option>
                </select>
              </div>
              <div class="col-sm-6">
                <select
                  class="form-select"
                  v-model="sortOrder"
                  aria-label="Urutkan laporan"
                >
                  <option value="newest">Terbaru</option>
                  <option value="oldest">Terlama</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <!-- Filter aktif dan reset -->
        <div class="d-flex justify-content-between align-items-center mt-3">
          <div class="active-filters" v-if="hasActiveFilters">
            <span class="me-2">Filter aktif:</span>
            <span v-if="searchQuery" class="badge bg-light text-dark me-1 active-filter">
              "{{ searchQuery }}"
              <button @click="clearSearch" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter pencarian"></button>
            </span>
            <span v-if="categoryFilter !== 'all'" class="badge bg-light text-dark me-1 active-filter">
              {{ categoryFilter }}
              <button @click="categoryFilter = 'all'" class="btn-close btn-close-sm ms-1" aria-label="Hapus filter kategori"></button>
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

      <!-- Statistik hasil -->
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

      <!-- Tidak ada hasil -->
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

      <!-- Grid hasil laporan -->
      <transition-group name="reports-fade" tag="div" class="row">
        <div
          v-for="(report, index) in paginatedReports"
          :key="report.id"
          class="col-md-6 col-lg-4"
        >
          <div
            class="card report-card"
            :class="getCategoryClass(report.category)"
            @click="openDetailModal(report)"
            role="button"
            tabindex="0"
            @keyup.enter="openDetailModal(report)"
            aria-label="Lihat detail laporan"
          >
            <div :class="getCategoryClass(report.category)" class="report-card__type-indicator"></div>
            <div class="card-header report-card__header">
              <div class="report-card__type">
                <i :class="getCategoryIcon(report.category)" class="me-2" aria-hidden="true"></i>
                <span>{{ report.category }}</span>
              </div>
            </div>
            <div class="card-body report-card__body">
              <h5 class="card-title report-card__title text-truncate" :title="report.source">{{ report.source || '-' }}</h5>
              <p class="card-text report-card__description text-truncate">
                {{ truncateText(report.description, 100) }}
              </p>

              <div v-if="report.evidence" class="mb-3">
                <a
                  :href="report.evidence"
                  target="_blank"
                  rel="noopener"
                  aria-label="Bukti laporan"
                  @click.stop
                >
                  Lihat Bukti
                </a>
              </div>

              <div class="report-card__footer">
                <small class="text-muted">
                  <i class="far fa-calendar-alt me-1" aria-hidden="true"></i>
                  {{ formatDate(report.created_at) }}
                </small>
              </div>
            </div>
          </div>
        </div>
      </transition-group>

      <!-- Pagination -->
      <nav v-if="totalPages > 1" aria-label="Navigasi halaman laporan">
        <ul class="pagination justify-content-center flex-wrap gap-1">
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

      <!-- Modal Detail -->
      <div
        class="modal fade"
        tabindex="-1"
        :class="{ show: detailModalOpen }"
        style="display: block;"
        v-if="detailModalOpen"
        @click.self="closeDetailModal"
        role="dialog"
        aria-modal="true"
        aria-labelledby="modalTitle"
      >
        <div class="modal-dialog modal-dialog-scrollable modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="modalTitle">Detail Laporan</h5>
              <button
                type="button"
                class="btn-close"
                aria-label="Close"
                @click="closeDetailModal"
              ></button>
            </div>
            <div class="modal-body">
              <div class="mb-3">
                <strong>Kategori:</strong> {{ selectedReport.category }}
              </div>
              <div class="mb-3">
                <strong>Sumber:</strong> {{ selectedReport.source || '-' }}
              </div>
              <div class="mb-3">
                <strong>Alamat:</strong> {{ selectedReport.address || '-' }}
              </div>
              <div class="mb-3">
                <strong>Deskripsi:</strong>
                <p>{{ selectedReport.description || '-' }}</p>
              </div>
              <div class="mb-3 evidence-container">
                <strong>Bukti:</strong>
                <div v-if="selectedReport.evidence" class="evidence-image-wrapper">
                  <img
                    :src="selectedReport.evidence"
                    alt="Bukti Laporan"
                    class="evidence-image"
                    loading="lazy"
                  />
                </div>
                <div v-else class="no-evidence-text text-muted">
                  Tidak ada bukti yang tersedia.
                </div>
              </div>
              <div class="mb-3">
                <strong>Tanggal Laporan:</strong> {{ formatDate(selectedReport.created_at) }}
              </div>
              <div v-if="selectedReport.latitude && selectedReport.longitude" class="mb-3">
                <strong>Lokasi:</strong>
                <br />
                <small>{{ selectedReport.latitude }}, {{ selectedReport.longitude }}</small>
              </div>
            </div>
            <div class="modal-footer">
              <button class="btn btn-secondary" @click="closeDetailModal">Tutup</button>
            </div>
          </div>
        </div>
      </div>

      <Section :showSearch="false" />
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

const CATEGORY_CLASSES = {
  'Nomor Hp': 'report-card__type-indicator--phone',
  Email: 'report-card__type-indicator--email',
  Website: 'report-card__type-indicator--web',
  WhatsApp: 'report-card__type-indicator--whatsapp',
};

const CATEGORY_ICONS = {
  'Nomor Hp': 'fas fa-phone text-danger',
  Email: 'fas fa-envelope text-primary',
  Website: 'fas fa-globe text-warning',
  WhatsApp: 'fab fa-whatsapp text-success',
};

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
      categoryFilter: 'all',
      sortOrder: 'newest',
      reports: [],
      loading: false,
      searchError: '',
      itemsPerPage: 6,
      currentPage: 1,
      detailModalOpen: false,
      selectedReport: {},
    };
  },
  computed: {
    feedbacks() {
      return usePage().props.feedbacks || [];
    },
    filteredReports() {
      let filtered = [...this.reports];

      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        filtered = filtered.filter(
          (r) =>
            (r.source && r.source.toLowerCase().includes(q)) ||
            (r.description && r.description.toLowerCase().includes(q)) ||
            (r.address && r.address.toLowerCase().includes(q))
        );
      }

      if (this.categoryFilter !== 'all') {
        filtered = filtered.filter((r) => r.category === this.categoryFilter);
      }

      if (this.sortOrder === 'newest') {
        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
      } else if (this.sortOrder === 'oldest') {
        filtered.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
      }

      return filtered;
    },
    paginatedReports() {
      const start = (this.currentPage - 1) * this.itemsPerPage;
      return this.filteredReports.slice(start, start + this.itemsPerPage);
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
      return (
        this.visiblePageNumbers[this.visiblePageNumbers.length - 1] < this.totalPages
      );
    },
    hasActiveFilters() {
      return this.searchQuery || this.categoryFilter !== 'all';
    },
  },
  methods: {
    searchReports() {
      if (this.searchQuery.length > 0 && this.searchQuery.length < 3) {
        this.searchError = 'Kata kunci pencarian minimal 3 karakter';
        return;
      }
      this.searchError = '';
      this.currentPage = 1;
      this.loading = true;
      setTimeout(() => {
        this.loading = false;
      }, 200);
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
      this.categoryFilter = 'all';
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
    getCategoryClass(category) {
      return CATEGORY_CLASSES[category] || '';
    },
    getCategoryIcon(category) {
      return CATEGORY_ICONS[category] || 'fas fa-question';
    },
    truncateText(text, length) {
      if (!text) return '';
      return text.length <= length ? text : text.substring(0, length) + '...';
    },
    formatDate(dateStr) {
      const date = new Date(dateStr);
      return date.toLocaleDateString('id-ID', {
        day: 'numeric',
        month: 'short',
        year: 'numeric',
      });
    },
    openDetailModal(report) {
      this.selectedReport = report;
      this.detailModalOpen = true;
      document.body.style.overflow = 'hidden'; // disable background scroll saat modal terbuka
    },
    closeDetailModal() {
      this.detailModalOpen = false;
      this.selectedReport = {};
      document.body.style.overflow = ''; // kembalikan scroll saat modal ditutup
    },
  },
  mounted() {
    this.reports = usePage().props.reports || [];
    this.searchReports();
  },
};
</script>

<style scoped>
/* Styles kamu sebelumnya tetap */

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
  border: 1px solid #dee2e6;
  border-radius: 8px;
  padding-left: 10px;
  cursor: pointer;
}

.report-card:hover,
.report-card:focus {
  transform: translateY(-5px);
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  outline: none;
}

.report-card:focus {
  outline: 3px solid #007bff;
  outline-offset: 2px;
}

.report-card__type-indicator {
  width: 10px;
  height: 100%;
  position: absolute;
  left: 0;
  top: 0;
  border-radius: 8px 0 0 8px;
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
  justify-content: flex-start;
  align-items: center;
  padding-left: 15px;
  margin-bottom: 10px;
  font-weight: 600;
}

.report-card__type i {
  font-size: 1.2rem;
}

.report-card__title {
  margin-bottom: 0.5rem;
}

.report-card__description {
  font-size: 0.9rem;
  color: #495057;
  overflow: hidden;
  text-overflow: ellipsis;
  display: -webkit-box;
  -webkit-line-clamp: 3; /* batasi 3 baris */
  -webkit-box-orient: vertical;
}

.report-card__footer {
  display: flex;
  justify-content: flex-start;
  align-items: center;
  font-size: 0.85rem;
  color: #6c757d;
}

.report-card__footer i {
  margin-right: 5px;
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

/* Modal overlay & container */
.modal {
  background-color: rgba(0, 0, 0, 0.45);
  backdrop-filter: blur(6px);
  overflow-y: auto;
  padding: 1rem;
  position: fixed;
  inset: 0;
  z-index: 1050;
  display: flex;
  justify-content: center;
  align-items: center;
}

.modal-dialog {
  max-width: 800px;
  width: 100%;
  margin: 0 auto;
  border-radius: 0.5rem;
  box-shadow: 0 12px 24px rgba(0, 0, 0, 0.25);
}

.modal-content {
  background: #fff;
  border-radius: 0.5rem;
  max-height: 90vh;
  overflow-y: auto;
  padding: 1.5rem 2rem;
  box-shadow: 0 8px 16px rgba(0,0,0,0.1);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.modal-header,
.modal-footer {
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #dee2e6;
  padding-bottom: 0.5rem;
}

.modal-footer {
  border-top: 1px solid #dee2e6;
  border-bottom: none;
  padding-top: 0.5rem;
}

.modal-title {
  font-weight: 700;
  font-size: 1.3rem;
  color: #222;
}

.modal-body p {
  white-space: pre-wrap;
  line-height: 1.6;
  margin: 0.5rem 0 0 0;
  color: #444;
}

/* Bukti gambar */
.evidence-container {
  margin-top: 1rem;
}

.evidence-image-wrapper {
  margin-top: 0.5rem;
  display: flex;
  justify-content: center;
}

.evidence-image {
  max-width: 100%;
  max-height: 400px;
  border-radius: 8px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  object-fit: contain;
  border: 1px solid #ccc;
}

/* Teks ketika tidak ada bukti */
.no-evidence-text {
  font-style: italic;
  color: #888;
  margin-top: 0.3rem;
  text-align: center;
}

/* Tombol tutup */
.btn-close {
  cursor: pointer;
  border: none;
  background: transparent;
  font-size: 1.2rem;
  color: #555;
  transition: color 0.3s ease;
}

.btn-close:hover,
.btn-close:focus {
  color: #000;
  outline: none;
}

/* Accessibility */
.report-card:focus {
  outline: 3px solid #007bff;
  outline-offset: 2px;
}

.report-card {
  background-color: white;
}
</style>
