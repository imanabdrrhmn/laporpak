<template>
  <AppLayout>
    <Head title="Cari Laporan" />
    <div class="search-container">
      <h2 class="mb-4">Pencarian Laporan Penipuan</h2>

      <SearchBar
        v-model:searchQuery="searchQuery"
        v-model:categoryFilter="categoryFilter"
        v-model:sortOrder="sortOrder"
        :searchError="searchError"
        :loading="loading"
        :hasActiveFilters="hasActiveFilters"
        @search="searchReports"
        @debounced-search="debouncedSearch"
        @clear-search="clearSearch"
        @clear-category="categoryFilter = 'all'"
        @reset-filters="resetFilters"
      />

      <ResultsStats
        :loading="loading"
        :filteredReports="filteredReports"
        :paginatedReports="paginatedReports"
        v-model:itemsPerPage="itemsPerPage"
      />

      <ReportList
        :loading="loading"
        :filteredReports="filteredReports"
        :paginatedReports="paginatedReports"
        @reset-filters="resetFilters"
        @open-detail="openDetailModal"
      />

      <Pagination
        :currentPage="currentPage"
        :totalPages="totalPages"
        :visiblePageNumbers="visiblePageNumbers"
        :showStartEllipsis="showStartEllipsis"
        :showEndEllipsis="showEndEllipsis"
        @change-page="changePage"
      />

      <!-- Modal pelaporan -->
      <ReportDetailModal
        :isOpen="detailModalOpen"
        :report="selectedReport"
        @close="closeDetailModal"
      />

        <ReportFlagModal
        :isOpen="flagModalOpen"
        :reportId="selectedReport.id"
        @close="flagModalOpen = false"
        @flag-submitted="handleFlagSubmitted"
      />

      <Section :showSearch="false" />
      <Feedback :feedbacks="feedbacks" />
    </div>
  </AppLayout>
</template>

<script>
import { debounce } from 'lodash';
import { Inertia } from '@inertiajs/inertia';
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
  name: 'FraudReportSearch',
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
      flagModalOpen: false,
      selectedReport: {},
      isFlagLoading: false,
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
    openDetailModal(report) {
      this.selectedReport = report;
      this.detailModalOpen = true;
      document.body.style.overflow = 'hidden';
    },
    closeDetailModal() {
      this.detailModalOpen = false;
      this.selectedReport = {};
      document.body.style.overflow = '';
    },
    openFlagModal(report) {
      this.selectedReport = report;
      this.flagModalOpen = true;
      this.isFlagLoading = false;
      document.body.style.overflow = 'hidden';
    },
    closeFlagModal() {
      this.flagModalOpen = false;
      this.selectedReport = {};
      this.isFlagLoading = false;
      document.body.style.overflow = '';
    },
    submitFlag(reason, reportId) {
      if (!reportId) {
        alert('Laporan tidak valid.');
        return;
      }
      this.isFlagLoading = true;
      Inertia.post(
        route('reports.flag'),
        {
          report_id: reportId,
          reason: reason,
        },
        {
          onSuccess: () => {
            alert('Terima kasih, laporan berhasil dikirim.');
            this.closeFlagModal();
          },
          onError: (errors) => {
            alert(errors.reason || 'Terjadi kesalahan saat mengirim laporan.');
          },
          onFinish: () => {
            this.isFlagLoading = false;
          },
        }
      );
    },
  },
  mounted() {
    this.reports = usePage().props.reports || [];
    const params = new URLSearchParams(window.location.search);
    const query = params.get('query');
    if (query) {
      this.searchQuery = query;
    }
    this.searchReports();
  },
};
</script>


<style scoped>
.search-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
</style>
