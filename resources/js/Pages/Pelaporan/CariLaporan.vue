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
        :is-flagging-in-progress="isFlaggingInProgress"
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
        :is-flagging-in-progress="isFlaggingInProgress"
        @close="closeDetailModal"
        @open-flag-modal="openFlagModalFromDetail"
      />

      <ReportFlagModal
        v-if="selectedReport && flagModalOpen"
        :isOpen="flagModalOpen"
        :reportId="selectedReport.id"
        @close="closeFlagModal"
        @reported="handleFlagSubmitted"
        @submitting="handleFlagSubmitting"
      />
    </div>

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

// Constants
const SEARCH_CONFIG = {
  MIN_QUERY_LENGTH: 3,
  DEFAULT_ITEMS_PER_PAGE: 12,
  DEBOUNCE_DELAY: 500,
  SCROLL_THRESHOLD: 300,
  MAX_VISIBLE_PAGES: 5
};

const DEFAULT_FILTERS = {
  searchQuery: '',
  categoryFilter: 'all',
  sortOrder: 'newest'
};

const MODAL_TYPES = {
  DETAIL: 'detail',
  FLAG: 'flag'
};

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
    reportStats: {
      type: Object,
      default: () => ({
        verifiedReports: 0,
        totalReports: 0,
        fraudReports: 0
      })
    },
    feedbacks: {
      type: Array,
      default: () => []
    },
    initialCategories: {
      type: Array,
      default: () => []
    },
    user: {
      type: Object
    },
  },
  
  data() {
    return {
      searchQuery: DEFAULT_FILTERS.searchQuery,
      categoryFilter: DEFAULT_FILTERS.categoryFilter,
      sortOrder: DEFAULT_FILTERS.sortOrder,
      reports: [],
      loading: false,
      searchError: '',
      itemsPerPage: SEARCH_CONFIG.DEFAULT_ITEMS_PER_PAGE,
      paginationData: this.getDefaultPaginationData(),
      detailModalOpen: false,
      flagModalOpen: false,
      selectedReport: null,
      firstLoadDone: false,
      showScrollTopButton: false,
      isFlaggingInProgress: false,
    };
  },
  
  computed: {
    hasSearchQuery() {
      return Boolean(this.searchQuery?.trim());
    },

    hasCategoryFilter() {
      return this.categoryFilter && this.categoryFilter !== 'all';
    },

    hasActiveFilters() {
      return this.hasSearchQuery || this.hasCategoryFilter;
    },

    visiblePageNumbers() {
      return this.calculateVisiblePageNumbers();
    },

    showStartEllipsis() {
      return this.visiblePageNumbers.length > 0 && 
             this.visiblePageNumbers[0] > 1;
    },

    showEndEllipsis() {
      const lastVisible = this.visiblePageNumbers[this.visiblePageNumbers.length - 1];
      return this.visiblePageNumbers.length > 0 && 
             lastVisible < this.paginationData.last_page;
    },

    shouldShowFallbackSection() {
      return !this.loading && 
             !this.searchError && 
             this.firstLoadDone && 
             this.shouldShowEmptyState();
    }
  },
  
  watch: {
    'paginationData.current_page': {
      handler(newPage, oldPage) {
        if (newPage !== oldPage && this.firstLoadDone) {
          this.searchReports();
          this.scrollToTop();
        }
      }
    },

    categoryFilter: {
      handler(newVal, oldVal) {
        if (newVal !== oldVal && this.firstLoadDone) {
          this.resetToFirstPage();
        }
      }
    },

    sortOrder: {
      handler(newVal, oldVal) {
        if (newVal !== oldVal && this.firstLoadDone) {
          this.resetToFirstPage();
        }
      }
    },

    itemsPerPage: {
      handler(newVal, oldVal) {
        if (newVal !== oldVal && this.firstLoadDone) {
          this.resetToFirstPage();
        }
      }
    }
  },
  
  created() {
    this.debouncedSearch = debounce(this.handleDebouncedSearch, SEARCH_CONFIG.DEBOUNCE_DELAY);
  },
  
  mounted() {
    this.initializeFromUrl();
    this.performInitialSearch();
    this.setupScrollListener();
  },
  
  beforeUnmount() {
    this.removeScrollListener();
    this.unlockBodyScroll();
  },
  
  methods: {
    // =================
    // INITIALIZATION
    // =================
    initializeFromUrl() {
      const urlParams = new URLSearchParams(window.location.search);
      
      this.searchQuery = urlParams.get('query') || DEFAULT_FILTERS.searchQuery;
      this.categoryFilter = urlParams.get('category') || DEFAULT_FILTERS.categoryFilter;
      this.sortOrder = urlParams.get('sortOrder') || DEFAULT_FILTERS.sortOrder;
      this.paginationData.current_page = parseInt(urlParams.get('page')) || 1;
      this.itemsPerPage = parseInt(urlParams.get('perPage')) || SEARCH_CONFIG.DEFAULT_ITEMS_PER_PAGE;
    },

    performInitialSearch() {
      this.searchReports();
    },

    // =================
    // SEARCH METHODS
    // =================
    async searchReports() {
      if (!this.isValidSearchQuery()) {
        this.handleInvalidSearch();
        return;
      }

      await this.performSearch();
    },

    isValidSearchQuery() {
      return !this.searchQuery || 
             this.searchQuery.length === 0 || 
             this.searchQuery.length >= SEARCH_CONFIG.MIN_QUERY_LENGTH;
    },

    handleInvalidSearch() {
      this.searchError = `Kata kunci pencarian minimal ${SEARCH_CONFIG.MIN_QUERY_LENGTH} karakter.`;
      this.resetSearchResults();
    },

    async performSearch() {
      this.setLoadingState(true);
      
      try {
        const response = await this.fetchSearchResults();
        this.handleSearchSuccess(response.data);
      } catch (error) {
        this.handleSearchError(error);
      } finally {
        this.setLoadingState(false);
      }
    },

    setLoadingState(loading) {
      this.loading = loading;
      if (loading) {
        this.searchError = '';
      }
    },

    async fetchSearchResults() {
      const params = this.buildSearchParams();
      return await axios.get('/api/laporan/search', { params });
    },

    buildSearchParams() {
      return {
        query: this.searchQuery,
        category: this.getActiveCategory(),
        sortOrder: this.sortOrder,
        page: this.paginationData.current_page,
        perPage: this.itemsPerPage
      };
    },

    getActiveCategory() {
      return this.hasCategoryFilter ? this.categoryFilter : '';
    },

    handleSearchSuccess(data) {
      this.reports = data.data || [];
      this.updatePaginationData(data);
      this.firstLoadDone = true;
    },

    handleSearchError(error) {
      console.error("Error fetching search results:", error);
      this.searchError = this.extractErrorMessage(error);
      this.resetSearchResults();
    },

    extractErrorMessage(error) {
      return error.response?.data?.message || 
             error.message || 
             'Terjadi kesalahan saat pencarian.';
    },

    // =================
    // SEARCH CONTROLS
    // =================
    handleDebouncedSearch() {
      this.resetToFirstPage();
    },

    clearSearch() {
      this.searchQuery = '';
      this.searchError = '';
      this.resetToFirstPage();
    },

    clearCategoryFilter() {
      this.categoryFilter = 'all';
    },

    resetAllFilters() {
      this.searchQuery = DEFAULT_FILTERS.searchQuery;
      this.categoryFilter = DEFAULT_FILTERS.categoryFilter;
      this.sortOrder = DEFAULT_FILTERS.sortOrder;
      this.searchError = '';
      this.resetToFirstPage();
    },

    resetToFirstPage() {
      if (this.paginationData.current_page !== 1) {
        this.paginationData.current_page = 1;
      } else {
        this.searchReports();
      }
    },

    // =================
    // DATA MANAGEMENT
    // =================
    resetSearchResults() {
      this.reports = [];
      this.paginationData = this.getDefaultPaginationData();
    },

    getDefaultPaginationData() {
      return {
        current_page: 1,
        last_page: 1,
        per_page: this.itemsPerPage,
        total: 0,
        links: {}
      };
    },

    updatePaginationData(data) {
      if (data.meta) {
        this.updateFromMetaData(data);
      } else {
        this.updateFromLegacyData(data);
      }
    },

    updateFromMetaData(data) {
      this.paginationData = {
        current_page: data.meta.current_page,
        last_page: data.meta.last_page,
        per_page: data.meta.per_page,
        total: data.meta.total,
        links: data.links || {}
      };
      this.itemsPerPage = data.meta.per_page || this.itemsPerPage;
    },

    updateFromLegacyData(data) {
      this.paginationData = {
        current_page: data.current_page || 1,
        last_page: data.last_page || 1,
        per_page: data.per_page || this.itemsPerPage,
        total: data.total || this.reports.length,
        links: data.links || {}
      };
      this.itemsPerPage = data.per_page || this.itemsPerPage;
    },

    // =================
    // PAGINATION
    // =================
    changePage(page) {
      if (this.isValidPageChange(page)) {
        this.paginationData.current_page = page;
      }
    },

    isValidPageChange(page) {
      return page >= 1 && 
             page <= this.paginationData.last_page && 
             page !== this.paginationData.current_page;
    },

    calculateVisiblePageNumbers() {
      const { current_page: currentPage, last_page: totalPages } = this.paginationData;
      const maxPages = SEARCH_CONFIG.MAX_VISIBLE_PAGES;

      if (!totalPages || totalPages <= 0) return [];
      if (totalPages <= maxPages) {
        return Array.from({ length: totalPages }, (_, i) => i + 1);
      }

      return this.calculatePageRange(currentPage, totalPages, maxPages);
    },

    calculatePageRange(currentPage, totalPages, maxPages) {
      let startPage = Math.max(1, currentPage - Math.floor(maxPages / 2));
      let endPage = Math.min(totalPages, startPage + maxPages - 1);

      if (endPage - startPage + 1 < maxPages) {
        startPage = Math.max(1, endPage - maxPages + 1);
      }

      return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
    },

    // =================
    // MODAL MANAGEMENT
    // =================
    openDetailModal(report) {
      this.openModal(MODAL_TYPES.DETAIL, report);
    },

    closeDetailModal() {
      this.closeModal(MODAL_TYPES.DETAIL);
    },

    openFlagModalFromList(report) {
      this.openModal(MODAL_TYPES.FLAG, report);
    },

    openFlagModalFromDetail(reportId) {
      if (this.isFlaggingInProgress) return;
      
      const reportToFlag = this.findReportById(reportId);
      if (reportToFlag) {
        this.selectedReport = reportToFlag;
        this.flagModalOpen = true;
        if (!this.detailModalOpen) {
          this.lockBodyScroll();
        }
      } else {
        console.error("Laporan untuk di-flag tidak ditemukan:", reportId);
      }
    },

    closeFlagModal() {
      this.closeModal(MODAL_TYPES.FLAG);
    },

    openModal(modalType, report = null) {
      if (this.isFlaggingInProgress) return;
      
      this.selectedReport = report;
      this[`${modalType}ModalOpen`] = true;
      this.lockBodyScroll();
    },

    closeModal(modalType) {
      if (this.isFlaggingInProgress) return;
      
      this[`${modalType}ModalOpen`] = false;
      
      if (!this.hasOpenModals()) {
        this.selectedReport = null;
        this.unlockBodyScroll();
      }
    },

    hasOpenModals() {
      return this.detailModalOpen || this.flagModalOpen;
    },

    lockBodyScroll() {
      document.body.style.overflow = 'hidden';
    },

    unlockBodyScroll() {
      document.body.style.overflow = '';
    },

    // =================
    // FLAG HANDLING
    // =================
    handleFlagSubmitted() {
      if (!this.selectedReport) return;
      
      this.updateReportFlagStatus(this.selectedReport.id, true);
    },

    handleFlagSubmitting(status) {
      this.isFlaggingInProgress = status;
    },

    updateReportFlagStatus(reportId, flagged) {
      const reportIndex = this.reports.findIndex(r => r.id === reportId);
      if (reportIndex !== -1) {
        this.reports[reportIndex].has_been_flagged_by_user = flagged;
      }
      
      if (this.selectedReport && this.selectedReport.id === reportId) {
        this.selectedReport.has_been_flagged_by_user = flagged;
      }
    },

    // =================
    // UTILITY METHODS
    // =================
    findReportById(reportId) {
      return this.reports.find(r => r.id === reportId) || this.selectedReport;
    },

    shouldShowEmptyState() {
      return !this.hasActiveFilters || 
             (this.hasActiveFilters && this.reports.length === 0);
    },

    // =================
    // SCROLL HANDLING
    // =================
    setupScrollListener() {
      window.addEventListener('scroll', this.handleScroll);
    },

    removeScrollListener() {
      window.removeEventListener('scroll', this.handleScroll);
    },

    handleScroll() {
      this.showScrollTopButton = window.scrollY > SEARCH_CONFIG.SCROLL_THRESHOLD;
    },

    scrollToTop() {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
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