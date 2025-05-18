<template>
  <AppLayout>
    <Head title="Cari Laporan" />
    <div class="search-container">
      <h2 class="mb-4">Pencarian Laporan Penipuan</h2>
      <FilterBar
        v-model:search-query="searchQuery"
        v-model:type-filter="typeFilter"
        v-model:status-filter="statusFilter"
        v-model:sort-order="sortOrder"
        :loading="loading"
        :search-error="searchError"
        @search="searchReports"
        @clear-search="clearSearch"
        @reset-filters="resetFilters"
      />
      <ResultsStats
        v-if="!loading && filteredReports.length > 0"
        :total-results="filteredReports.length"
        :displayed-results="paginatedReports.length"
        v-model:items-per-page="itemsPerPage"
      />
      <ReportList
        :loading="loading"
        :filtered-reports="filteredReports"
        :paginated-reports="paginatedReports"
      />
      <Pagination
        v-if="totalPages > 1"
        :current-page="currentPage"
        :total-pages="totalPages"
        :visible-page-numbers="visiblePageNumbers"
        :show-start-ellipsis="showStartEllipsis"
        :show-end-ellipsis="showEndEllipsis"
        @change-page="changePage"
      />
      <SuccessToast />
      <Section :showSearch="false" />
      <Feedback :feedbacks="feedbacks" />
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import { debounce } from 'lodash';
import { Head, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Feedback from '@/Components/Feedback.vue';
import Section from '@/Components/Section.vue';
import FilterBar from '@/Components/CariLaporan/FilterBar.vue';
import ResultsStats from '@/Components/CariLaporan/ResultsStats.vue';
import ReportList from '@/Components/CariLaporan/ReportList.vue';
import Pagination from '@/Components/CariLaporan/Pagination.vue';
import SuccessToast from '@/Components/CariLaporan/SuccessToast.vue';

const STATUS_FILTER_MAP = {
  process: 'warning',
  completed: 'safe',
  rejected: 'danger'
};

const searchQuery = ref('');
const statusFilter = ref('all');
const typeFilter = ref('all');
const sortOrder = ref('newest');
const reports = ref([]);
const loading = ref(false);
const searchError = ref('');
const itemsPerPage = ref(6);
const currentPage = ref(1);
const feedbacks = ref(usePage().props.feedbacks || []);

const sampleReports = ref([

]);

function parseReportDate(dateStr) {
  const parts = dateStr.split(' ');
  const months = {
    Jan: 0, Feb: 1, Mar: 2, Apr: 3, Mei: 4, Jun: 5,
    Jul: 6, Agu: 7, Sep: 8, Okt: 9, Nov: 10, Des: 11
  };
  return new Date(parts[2], months[parts[1]], parts[0]);
}

const filteredReports = computed(() => {
  let filtered = [...sampleReports.value];

  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(report =>
      report.contact.toLowerCase().includes(query) ||
      report.description.toLowerCase().includes(query)
    );
  }

  if (typeFilter.value !== 'all') {
    filtered = filtered.filter(report => report.type === typeFilter.value);
  }

  if (statusFilter.value !== 'all') {
    const status = STATUS_FILTER_MAP[statusFilter.value];
    filtered = filtered.filter(report => report.status === status);
  }

  switch (sortOrder.value) {
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
});

const paginatedReports = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage.value;
  const end = start + itemsPerPage.value;
  return filteredReports.value.slice(start, end);
});

const totalPages = computed(() => {
  return Math.ceil(filteredReports.value.length / itemsPerPage.value);
});

const visiblePageNumbers = computed(() => {
  const maxPages = 5;
  const pages = [];
  let startPage = Math.max(1, currentPage.value - Math.floor(maxPages / 2));
  let endPage = Math.min(totalPages.value, startPage + maxPages - 1);

  if (endPage - startPage + 1 < maxPages) {
    startPage = Math.max(1, endPage - maxPages + 1);
  }

  for (let i = startPage; i <= endPage; i++) {
    pages.push(i);
  }
  return pages;
});

const showStartEllipsis = computed(() => {
  return visiblePageNumbers.value[0] > 1;
});

const showEndEllipsis = computed(() => {
  return visiblePageNumbers.value[visiblePageNumbers.value.length - 1] < totalPages.value;
});

const searchReports = () => {
  if (searchQuery.value.length < 3 && searchQuery.value.length > 0) {
    searchError.value = 'Kata kunci pencarian minimal 3 karakter';
    return;
  }
  searchError.value = '';
  currentPage.value = 1;
  loading.value = true;
  setTimeout(() => {
    reports.value = sampleReports.value;
    loading.value = false;
  }, 500);
};

const debouncedSearch = debounce(() => {
  searchReports();
}, 300);

const clearSearch = () => {
  searchQuery.value = '';
  searchError.value = '';
  currentPage.value = 1;
  searchReports();
};

const resetFilters = () => {
  searchQuery.value = '';
  typeFilter.value = 'all';
  statusFilter.value = 'all';
  sortOrder.value = 'newest';
  currentPage.value = 1;
  searchError.value = '';
  searchReports();
};

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

onMounted(() => {
  searchReports();
});
</script>

<style scoped>
.search-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}
</style>