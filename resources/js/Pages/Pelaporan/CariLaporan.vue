<template>
  <AppLayout>
    <Head title="Cari Laporan" />
    <div class="search-container">
      <h2 class="mb-4 page-title">Pencarian Laporan</h2>

      <SearchBar
        v-model:searchQuery="searchQuery"
        v-model:categoryFilter="categoryFilter"
        v-model:sortOrder="sortOrder"
        :categories="initialCategories"
        :searchError="searchError"
        :loading="loading"
        :hasActiveFilters="hasActiveFilters"
        @search="searchReports"
        @debounced-search="debouncedSearch"
        @clear-search="clearSearch"
        @clear-category="clearCategoryFilter"
        @reset-filters="resetAllFilters"
      />

      <div v-if="loading && !reports.length" class="loading-indicator">
        <div class="loading-dots">
          <div class="dot"></div>
          <div class="dot"></div>
          <div class="dot"></div>
        </div>
        <p class="loading-text">Mencari laporan...</p>
      </div>
      
      <div v-else-if="searchError && !loading" class="error-indicator">
        <p class="error-title">Oops! Terjadi Kesalahan</p>
        <p class="error-message">{{ searchError }}</p>
        <button @click="searchReports" class="retry-button mt-3">Coba Lagi</button>
      </div>

      <ResultsStats
        v-if="!loading && !searchError && (reports.length > 0 || hasActiveFilters)"
        :loading="loading"
        :itemsOnPage="reports.length"
        :totalItems="paginationData.total"
        v-model:itemsPerPage="itemsPerPage"
        :showWhenEmpty="hasActiveFilters"
      />

      <ReportList
        v-if="!loading && !searchError"
        :loading="loading"
        :reportsToDisplay="reports"
        @reset-filters="resetAllFilters"
        @open-detail="openDetailModal"
        @open-flag="openFlagModalFromList"
      />

      <Pagination
        v-if="!loading && !searchError && reports.length > 0 && paginationData.last_page > 1"
        :currentPage="paginationData.current_page"
        :totalPages="paginationData.last_page"
        :visiblePageNumbers="visiblePageNumbers"
        :showStartEllipsis="showStartEllipsis"
        :showEndEllipsis="showEndEllipsis"
        @change-page="changePage"
      />

      <ReportDetailModal
        :isOpen="detailModalOpen"
        :report="selectedReport"
        :user="user"
        @close="closeDetailModal"
        @open-flag-modal="openFlagModalFromDetail"
      />

      <ReportFlagModal
        :isOpen="flagModalOpen"
        :reportId="selectedReport?.id"
        @close="closeFlagModal"
        @reported="handleFlagSubmitted"
      />
    </div>

    <!-- Pindahkan Section dan Feedback keluar dari search-container -->
    <Section 
      v-if="showFallbackSectionInfo"
      :verifiedReports="reportStats.verifiedReports"
      :totalReports="reportStats.totalReports"
      :fraudReports="reportStats.fraudReports"
      :showSearch="false" 
    />
    <Feedback :feedbacks="feedbacks" v-if="showFallbackSectionInfo" />

    <button v-if="showScrollTopButton" @click="scrollToTop" class="scroll-top-button" aria-label="Kembali ke atas">
      <i class="fas fa-arrow-up"></i>
    </button>

  </AppLayout>
</template>

<script>
import { debounce } from 'lodash';
import axios from 'axios';
import AppLayout from '@/Layouts/AppLayout.vue';
import Feedback from '@/Components/Feedback.vue';
import { usePage, Head } from '@inertiajs/vue3';
import Section from '@/Components/Section.vue';
import SearchBar from '@/Components/CariLaporan/SearchBar.vue';
import ResultsStats from '@/Components/CariLaporan/ResultsStats.vue';
import ReportList from '@/Components/CariLaporan/ReportList.vue';
import Pagination from '@/Components/CariLaporan/Pagination.vue';
import ReportDetailModal from '@/Components/CariLaporan/ReportDetailModal.vue';
import ReportFlagModal from '@/Components/CariLaporan/ReportFlagModal.vue';

export default {
  name: 'CariLaporanPage',
  components: {
    AppLayout,
    Feedback,
    Section,
    Head,
    SearchBar,
    ResultsStats,
    ReportList,
    Pagination,
    ReportDetailModal,
    ReportFlagModal,
  },
  props: {
    reportStats: { type: Object, default: () => ({ verifiedReports: 0, totalReports: 0, fraudReports: 0 }) },
    feedbacks: { type: Array, default: () => [] },
    initialCategories: { type: Array, default: () => [] },
    user: { type: Object },

  },
  data() {
    return {
      searchQuery: '',
      categoryFilter: 'all',
      sortOrder: 'newest',
      reports: [],
      loading: false,
      searchError: '',
      itemsPerPage: 12,
      paginationData: {
        current_page: 1,
        last_page: 1,
        per_page: 12,
        total: 0,
        links: {},
      },
      detailModalOpen: false,
      flagModalOpen: false,
      selectedReport: null,
      firstLoadDone: false,
      showScrollTopButton: false,
    };
  },
  computed: {
    hasActiveFilters() {
      return this.searchQuery || (this.categoryFilter && this.categoryFilter !== 'all');
    },
    visiblePageNumbers() {
      const maxPagesToShow = 5;
      const currentPage = this.paginationData.current_page;
      const totalPages = this.paginationData.last_page;
      const pages = [];
      if (!totalPages || totalPages <= 0) return [];
      if (totalPages <= maxPagesToShow) {
        for (let i = 1; i <= totalPages; i++) pages.push(i);
      } else {
        let startPage = Math.max(1, currentPage - Math.floor(maxPagesToShow / 2));
        let endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
        if (endPage - startPage + 1 < maxPagesToShow) {
          if (currentPage < (totalPages / 2)) {
            endPage = Math.min(totalPages, startPage + maxPagesToShow - 1);
          } else {
            startPage = Math.max(1, endPage - maxPagesToShow + 1);
          }
        }
        for (let i = startPage; i <= endPage; i++) pages.push(i);
      }
      return pages;
    },
    showStartEllipsis() {
      return this.visiblePageNumbers.length > 0 && this.visiblePageNumbers[0] > 1;
    },
    showEndEllipsis() {
      return this.visiblePageNumbers.length > 0 && this.visiblePageNumbers[this.visiblePageNumbers.length - 1] < this.paginationData.last_page;
    },
    showFallbackSectionInfo() {
      return !this.loading && !this.searchError && this.firstLoadDone && (!this.hasActiveFilters || (this.hasActiveFilters && this.reports.length === 0));
    }
  },
  methods: {
    async searchReports() {
      if (this.searchQuery && this.searchQuery.length > 0 && this.searchQuery.length < 3) {
        this.searchError = 'Kata kunci pencarian minimal 3 karakter.';
        this.reports = [];
        this.paginationData = { ...this.paginationData, total: 0, last_page: 1, current_page: 1 };
        return;
      }
      this.searchError = '';
      this.loading = true;

      try {
        const params = {
          query: this.searchQuery,
          category: (this.categoryFilter && this.categoryFilter !== 'all') ? this.categoryFilter : '',
          sortOrder: this.sortOrder,
          page: this.paginationData.current_page,
          perPage: this.itemsPerPage,
        };
        const response = await axios.get('/api/laporan/search', { params });

        this.reports = response.data.data || [];

        if (response.data.meta) {
          this.paginationData = {
            current_page: response.data.meta.current_page,
            last_page: response.data.meta.last_page,
            per_page: response.data.meta.per_page,
            total: response.data.meta.total,
            links: response.data.links || {},
          };
          this.itemsPerPage = response.data.meta.per_page || this.itemsPerPage;
        } else {
          this.paginationData = {
            current_page: response.data.current_page || 1,
            last_page: response.data.last_page || 1,
            per_page: response.data.per_page || this.itemsPerPage,
            total: response.data.total || this.reports.length,
            links: response.data.links || {},
          };
          this.itemsPerPage = response.data.per_page || this.itemsPerPage;
        }
        this.firstLoadDone = true;

      } catch (error) {
        console.error("Error fetching search results:", error);
        this.searchError = error.response?.data?.message || error.message || 'Terjadi kesalahan saat pencarian.';
        this.reports = [];
        this.paginationData = { current_page: 1, last_page: 1, per_page: this.itemsPerPage, total: 0, links: {} };
      } finally {
        this.loading = false;
      }
    },
    debouncedSearch: debounce(function () {
      if (this.paginationData.current_page !== 1) {
        this.paginationData.current_page = 1;
      } else {
        this.searchReports();
      }
    }, 500),
    clearSearch() {
      this.searchQuery = '';
      this.searchError = '';
      if (this.paginationData.current_page !== 1) {
        this.paginationData.current_page = 1;
      } else {
        this.searchReports();
      }
    },
    clearCategoryFilter() {
      this.categoryFilter = 'all';
    },
    resetAllFilters() {
      this.searchQuery = '';
      this.categoryFilter = 'all';
      this.sortOrder = 'newest';
      this.searchError = '';
      if (this.paginationData.current_page !== 1) {
        this.paginationData.current_page = 1;
      } else {
        this.searchReports();
      }
    },
    changePage(page) {
      if (page >= 1 && page <= this.paginationData.last_page && page !== this.paginationData.current_page) {
        this.paginationData.current_page = page;
      }
    },
    openDetailModal(report) {
      this.selectedReport = report;
      this.detailModalOpen = true;
      document.body.style.overflow = 'hidden';
    },
    closeDetailModal() {
      this.detailModalOpen = false;
      this.selectedReport = null;
      document.body.style.overflow = '';
    },
    openFlagModalFromList(report) {
      this.selectedReport = report;
      this.flagModalOpen = true;
      document.body.style.overflow = 'hidden';
    },
    openFlagModalFromDetail(reportId) {
      const reportToFlag = this.reports.find(r => r.id === reportId) || this.selectedReport;
      if (reportToFlag) {
        this.selectedReport = reportToFlag;
        this.flagModalOpen = true;
        if (!this.detailModalOpen) {
          document.body.style.overflow = 'hidden';
        }
      } else {
        console.error("Laporan untuk di-flag tidak ditemukan:", reportId);
      }
    },
    closeFlagModal() {
      this.flagModalOpen = false;
      if (!this.detailModalOpen) {
        this.selectedReport = null;
        document.body.style.overflow = '';
      }
    },
    handleFlagSubmitted() {
      console.log('Flag telah dikirim.');
    },
    handleScroll() {
      this.showScrollTopButton = window.scrollY > 300;
    },
    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  },
  watch: {
    categoryFilter(newVal, oldVal) {
      if (newVal !== oldVal && this.firstLoadDone) {
        if (this.paginationData.current_page !== 1) this.paginationData.current_page = 1;
        else this.searchReports();
      }
    },
    sortOrder(newVal, oldVal) {
      if (newVal !== oldVal && this.firstLoadDone) {
        if (this.paginationData.current_page !== 1) this.paginationData.current_page = 1;
        else this.searchReports();
      }
    },
    'paginationData.current_page'(newPage, oldPage) {
      if (newPage !== oldPage && this.firstLoadDone) {
        this.searchReports();
        window.scrollTo({ top: 0, behavior: 'smooth' });
      }
    },
    itemsPerPage(newVal, oldVal) {
      if (newVal !== oldVal && this.firstLoadDone) {
        if (this.paginationData.current_page !== 1) this.paginationData.current_page = 1;
        else this.searchReports();
      }
    }
  },
  mounted() {
    const urlParams = new URLSearchParams(window.location.search);
    this.searchQuery = urlParams.get('query') || '';
    this.categoryFilter = urlParams.get('category') || 'all';
    this.sortOrder = urlParams.get('sortOrder') || 'newest';
    this.paginationData.current_page = parseInt(urlParams.get('page')) || 1;
    this.itemsPerPage = parseInt(urlParams.get('perPage')) || 12;

    this.searchReports();
    window.addEventListener('scroll', this.handleScroll);
  },
  beforeUnmount() {
    window.removeEventListener('scroll', this.handleScroll);
  }
};
</script>

<style scoped>
.search-container {
  max-width: 1200px;
  margin: 2rem auto;
  padding: 20px;
  font-family: 'Inter', sans-serif;
  background-color: #ffffff;
  border-radius: 8px;
} 

.page-title {
  font-size: 1.75rem;
  font-weight: 700;
  color: #1f2937;
  text-align: center;
  margin-bottom: 1.5rem;
}

.loading-indicator {
  text-align: center;
  padding: 40px 20px;
  margin-top: 20px;
}

.loading-dots {
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin-bottom: 12px;
}

.dot {
  width: 8px;
  height: 8px;
  background-color: #3b82f6;
  border-radius: 50%;
  animation: pulse 1.5s ease-in-out infinite;
}

.dot:nth-child(1) {
  animation-delay: 0s;
}

.dot:nth-child(2) {
  animation-delay: 0.3s;
}

.dot:nth-child(3) {
  animation-delay: 0.6s;
}

@keyframes pulse {
  0%, 80%, 100% {
    opacity: 0.3;
    transform: scale(0.8);
  }
  40% {
    opacity: 1;
    transform: scale(1);
  }
}

.loading-text {
  font-size: 0.875rem;
  color: #6b7280;
  margin: 0;
  font-weight: 500;
}

.error-indicator {
  text-align: center;
  padding: 30px 20px;
  margin-top: 20px;
  border-radius: 8px;
  background-color: #fee2e2;
  border: 1px solid #fecaca;
  color: #991b1b;
}

.error-indicator .error-title {
  font-weight: 600;
  font-size: 1.125rem;
}

.error-indicator .error-message {
  font-size: 0.875rem;
  margin-top: 0.25rem;
}

.retry-button {
  padding: 0.5rem 1rem;
  background-color: #3b82f6;
  color: white;
  border-radius: 0.375rem;
  font-size: 0.875rem;
  transition: background-color 0.2s;
  border: none;
}

.retry-button:hover {
  background-color: #2563eb;
}

.scroll-top-button {
  position: fixed;
  bottom: 20px;
  right: 20px;
  background-color: #3b82f6;
  color: white;
  border: none;
  border-radius: 50%;
  width: 50px;
  height: 50px;
  font-size: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  box-shadow: 0 2px 10px rgba(0,0,0,0.2);
  transition: opacity 0.3s, transform 0.3s;
  opacity: 0;
  transform: translateY(100px);
  z-index: 1000;
}

.scroll-top-button:hover {
  background-color: #2563eb;
}

.scroll-top-button[style*="display: block"] {
  opacity: 1;
  transform: translateY(0);
}

/* CSS tambahan untuk responsive jika diperlukan */
@media (max-width: 768px) {
  .search-container {
    margin: 1rem auto;
    padding: 15px;
    border-radius: 4px;
  }
  
  .page-title {
    font-size: 1.5rem;
  }
}
</style>