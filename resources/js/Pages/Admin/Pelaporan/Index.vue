<template>
  <AppLayout>
    <Head title="Manajemen Laporan" />
    <div class="wrapper">
      <div class="container">
        <DashboardHeader :report-stats="reportStatsFromServer" />
        <FilterSection
          v-model:search-query="searchQuery"
          v-model:selected-category="selectedCategory"
          v-model:selected-service="selectedService"
          v-model:selected-status="selectedStatus"
          v-model:sort-direction="sortDirection"
          v-model:view-mode="viewMode"
          :categories="categoriesForFilter"
          :services="servicesForFilter"
          :has-active-filters="hasActiveFilters"
          :total-results="paginationData.total"
          @reset-filters="resetFiltersAndFetch"
        />

        <div v-if="isLoading" class="loading-indicator">
          <svg aria-hidden="true" class="spinner-icon" viewBox="0 0 100 101" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z" fill="currentColor"/>
            <path d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0492C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 50.1208 80.5027 69.2768 60.2711 79.6801C54.7108 82.3998 48.1338 84.3529 41.3896 85.1838C34.6454 86.0147 27.8502 85.6635 21.4064 84.1651C14.9626 82.6667 8.93458 79.9504 3.80109 76.1769C-1.3324 72.4034 -5.99974 67.5066 -9.99958 61.8511" fill="currentFill"/>
          </svg>
          <p class="loading-text">Memuat laporan...</p>
        </div>
        <div v-else-if="fetchError" class="error-indicator">
          <p class="error-title">Gagal memuat laporan!</p>
          <p class="error-message">{{ fetchError }}</p>
          <button @click="fetchReports" class="retry-button">Coba Lagi</button>
        </div>
        <ReportsContainer
          v-else-if="reports.length > 0"
          :reports="reports"
          :view-mode="viewMode"
          :loading="quickActionLoading"
          :can="userPermissions"
          :current-page="paginationData.current_page"
          :items-per-page="itemsPerPage"
          @view-report="prepareViewReport"
          @quick-action="onQuickAction"
          @reset-filters="resetFiltersAndFetch"
        />
        <div v-else class="no-reports-found">
          <i class="fas fa-folder-open empty-icon"></i>
          <h3>Tidak ada laporan yang ditemukan.</h3>
          <p>Silakan coba ubah filter pencarian Anda atau reset semua filter.</p>
          <button @click="resetFiltersAndFetch" class="reset-btn-empty">Reset Filter</button>
        </div>

        <Pagination
          v-if="!isLoading && reports.length > 0 && paginationData.last_page > 1"
          v-model:currentPage="paginationData.current_page"
          :total-pages="paginationData.last_page"
        />

        <LaporanDetailModal
          :report="currentReportForModal"
          :is-visible="showDetailModal"
          @close="closeDetailModal"
          @open-flag-summary="openFlagSummaryModal"
          @action-triggered="handleActionFromDetailModal"
        />

        <UnpublishModal
          :is-visible="showUnpublishModal"
          :loading="quickActionLoading[unpublishTargetReport?.id] || false"
          @close="closeUnpublishModal"
          @confirm="confirmUnpublish"
        />

        <ReportFlagSummaryModal
          :isOpen="showFlagSummary"
          :flags="flagsForSummaryModal"
          @close="showFlagSummary = false"
        />

        <Notification
          :show="toast.visible"
          :type="toast.type"
          :message="toast.message"
          @close="toast.visible = false"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';

import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardHeader from './Components/DashboardHeader.vue';
import FilterSection from './Components/FilterSection.vue';
import ReportsContainer from './Components/ReportsContainer.vue';
import Pagination from './Components/Pagination.vue';
import LaporanDetailModal from './Components/LaporanDetailModal.vue';
import ReportFlagSummaryModal from './Components/ReportFlagModal.vue';
import UnpublishModal from './Components/UnpublishModal.vue';
import Notification from '@/Components/Notification.vue';

const page = usePage();

const props = defineProps({
  can: {
    type: Object,
    default: () => ({}),
  },
});

const reports = ref([]);
const paginationData = ref({
  current_page: 1,
  last_page: 1,
  per_page: 15,
  total: 0,
  links: [],
});
const reportStatsFromServer = ref({
  total: 0, pending: 0, approved: 0, rejected: 0,
  published: 0, solved: 0, unpublished: 0,
});
const isLoading = ref(true);
const fetchError = ref(null);
const userPermissions = computed(() => props.can);

const searchQuery = ref('');
const selectedCategory = ref('');
const selectedService = ref('');
const selectedStatus = ref('');
const sortDirection = ref('desc');
const viewMode = ref('grid');

const categoriesForFilter = ref([]);
const servicesForFilter = ref([]);

const itemsPerPage = ref(15);

const hasActiveFilters = computed(() =>
  searchQuery.value || selectedCategory.value || selectedService.value || selectedStatus.value
);

const showDetailModal = ref(false);
const currentReportForModal = ref(null);

const showFlagSummary = ref(false);
const flagsForSummaryModal = ref([]);

const showUnpublishModal = ref(false);
const unpublishTargetReport = ref(null);

const quickActionLoading = ref({});

const toast = ref({
  visible: false, message: '', type: 'success',
});

const API_BASE_URL = '/data/reports';

const fetchReports = async () => {
  isLoading.value = true;
  fetchError.value = null;
  try {
    const params = {
      page: paginationData.value.current_page,
      per_page: itemsPerPage.value,
      search: searchQuery.value,
      category: selectedCategory.value,
      service: selectedService.value,
      status: selectedStatus.value,
      sort_direction: sortDirection.value,
    };
    const response = await axios.get(API_BASE_URL, { params });
    
    reports.value = response.data.data;

    const { data: reportDataFromResponse, meta: metaFromServer, ...paginationMetaFromResponse } = response.data;
      paginationData.value = {
        current_page: metaFromServer.current_page,
        last_page: metaFromServer.last_page,
        per_page: metaFromServer.per_page,
        total: metaFromServer.total,
        links: metaFromServer.links,
      };    
      itemsPerPage.value = paginationData.value.per_page || 15;

    if (metaFromServer && metaFromServer.stats) {
      reportStatsFromServer.value = metaFromServer.stats;
    }
    
    if (metaFromServer?.filter_options) {
      categoriesForFilter.value = metaFromServer.filter_options.categories || [];
      servicesForFilter.value = metaFromServer.filter_options.services || [];
    }

  } catch (error) {
    console.error('Error fetching reports:', error);
    const errMsg = error.response?.data?.message || error.message || 'Gagal memuat data laporan.';
    fetchError.value = errMsg;
    reports.value = [];
    reportStatsFromServer.value = { total: 0, pending: 0, approved: 0, rejected: 0, published: 0, solved: 0, unpublished: 0 };
    paginationData.value = { current_page: 1, last_page: 1, per_page: itemsPerPage.value, total: 0, links: [] };
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchReports();
  if (page.props.flash?.success) showToast(page.props.flash.success, 'success');
  if (page.props.flash?.error) showToast(page.props.flash.error, 'danger');
});

let searchDebounceTimer = null;
watch(searchQuery, () => {
  clearTimeout(searchDebounceTimer);
  searchDebounceTimer = setTimeout(() => {
    if (paginationData.value.current_page !== 1) paginationData.value.current_page = 1;
    else fetchReports();
  }, 500);
});

watch([selectedCategory, selectedService, selectedStatus, sortDirection], () => {
  if (paginationData.value.current_page !== 1) paginationData.value.current_page = 1;
  else fetchReports();
});

watch(() => paginationData.value.current_page, (newPage, oldPage) => {
  if (newPage !== oldPage && !isLoading.value) {
    fetchReports();
  }
});

const resetFiltersAndFetch = () => {
  searchQuery.value = '';
  selectedCategory.value = '';
  selectedService.value = '';
  selectedStatus.value = '';
  sortDirection.value = 'desc';
  if (paginationData.value.current_page !== 1) {
    paginationData.value.current_page = 1;
  } else {
    fetchReports();
  }
};

function showToast(message, type = 'success') {
  toast.value = { visible: true, message, type };
  setTimeout(() => { toast.value.visible = false; }, 3500);
}

const prepareViewReport = (report) => {
  if (!report) return;
  currentReportForModal.value = { ...report };
  showDetailModal.value = true;
};

const closeDetailModal = () => {
  showDetailModal.value = false;
  flagsForSummaryModal.value = [];
  setTimeout(() => { currentReportForModal.value = null; }, 300);
};

const openFlagSummaryModal = async (reportId) => {
  if (!reportId) {
    showToast('Report ID tidak valid untuk melihat flags.', 'danger');
    return;
  }
  const flagLoadingKey = `flags_loading_${reportId}`;
  quickActionLoading.value = { ...quickActionLoading.value, [flagLoadingKey]: true };
  try {
    const response = await axios.get(`${API_BASE_URL}/${reportId}/flags`);
    flagsForSummaryModal.value = response.data || [];
    showFlagSummary.value = true;
  } catch (error) {
    console.error('Error fetching report flags:', error);
    showToast(error.response?.data?.message || 'Gagal memuat data flag.', 'danger');
    flagsForSummaryModal.value = [];
  } finally {
    quickActionLoading.value = { ...quickActionLoading.value, [flagLoadingKey]: false };
  }
};

const performReportAction = async (reportId, action, payload = {}, successMessage = '') => {
  quickActionLoading.value = { ...quickActionLoading.value, [reportId]: true };
  let url = `${API_BASE_URL}/${reportId}`;
  let httpMethod = 'post';

  if (action === 'destroy') {
    httpMethod = 'delete';
  } else {
    url += `/${action}`;
  }

  try {
    let response;
    if (httpMethod === 'delete') {
      response = await axios.delete(url);
    } else {
      response = await axios.post(url, payload);
    }

    const message = response.data.message || successMessage || `Aksi ${action} berhasil.`;
    showToast(message, 'success');

    const updatedReportData = response.data;
    const index = reports.value.findIndex(r => r.id === reportId);

    if (action === 'destroy') {
      if (index !== -1) reports.value.splice(index, 1);
      if (reports.value.length === 0 && paginationData.value.current_page > 1) {
        paginationData.value.current_page--;
      } else {
        fetchReports();
      }
    } else if (updatedReportData && updatedReportData.id && index !== -1) {
      reports.value[index] = { ...reports.value[index], ...updatedReportData };
      if (currentReportForModal.value && currentReportForModal.value.id === reportId) {
        currentReportForModal.value = { ...currentReportForModal.value, ...updatedReportData };
      }
      fetchReports();
    } else {
      fetchReports();
    }
    
    if (action === 'unpublish') closeUnpublishModal();
    if (showDetailModal.value && ['accept', 'reject', 'publish', 'unpublish', 'solved'].includes(action)) {
      closeDetailModal();
    }

  } catch (error) {
    console.error(`Error performing action ${action}:`, error);
    const errorMsg = error.response?.data?.message || `Gagal melakukan aksi ${action}.`;
    showToast(errorMsg, 'danger');
    if (!(action === 'unpublish' && error.response?.status === 422)) {
      if (action === 'unpublish') closeUnpublishModal();
    }
  } finally {
    quickActionLoading.value = { ...quickActionLoading.value, [reportId]: false };
  }
};

const onQuickAction = ({ report, action: frontendAction }) => {
  if (!report?.id) {
    showToast('Laporan tidak valid.', 'danger');
    return;
  }

  let backendAction = frontendAction;
  if (frontendAction === 'approved') backendAction = 'accept';
  if (frontendAction === 'publish' && report.status === 'published') backendAction = 'unpublish';
  else if (frontendAction === 'publish' && report.status !== 'published') backendAction = 'publish';

  if (backendAction === 'unpublish') {
    unpublishTargetReport.value = report;
    showUnpublishModal.value = true;
  } else if (backendAction === 'destroy') {
    if (confirm(`Apakah Anda yakin ingin menghapus laporan "${report.description.substring(0,30)}..."?`)) {
      performReportAction(report.id, 'destroy', {}, 'Laporan berhasil dihapus.');
    }
  } else {
    performReportAction(report.id, backendAction, {}, `Laporan berhasil di-${backendAction}.`);
  }
};

const handleActionFromDetailModal = ({ reportId, action, reason }) => {
  const payload = reason ? { reason } : {};
  performReportAction(reportId, action, payload);
};

const closeUnpublishModal = () => {
  showUnpublishModal.value = false;
  unpublishTargetReport.value = null;
};

const confirmUnpublish = (reason) => {
  if (!unpublishTargetReport.value || !unpublishTargetReport.value.id) return;
  if (!reason || !reason.trim()) {
    showToast('Alasan harus diisi.', 'danger');
    return;
  }
  performReportAction(unpublishTargetReport.value.id, 'unpublish', { reason });
};
</script>

<style scoped>
.wrapper {
  padding: 20px;
  background-color: #f4f7f6;
  min-height: 100vh;
}
.container {
 max-width: 1400px;
 margin: 0 auto;
}
.loading-indicator, .error-indicator, .no-reports-found {
  text-align: center;
  padding: 40px 20px;
  margin-top: 20px;
  border-radius: 8px;
}
.loading-indicator .spinner-icon {
  width: 2.5rem; /* 40px */
  height: 2.5rem; /* 40px */
  display: inline-block;
  color: #d1d5db; /* text-gray-300 */
  fill: #3b82f6; /* fill-blue-600 */
}
.loading-indicator .loading-text {
  margin-top: 0.5rem; /* 8px */
  font-size: 0.875rem; /* 14px */
  color: #6b7280; /* text-gray-500 */
}
.error-indicator {
  background-color: #fee2e2; /* bg-red-100 */
  border: 1px solid #fecaca; /* border-red-200 */
  color: #991b1b; /* text-red-700 */
}
.error-indicator .error-title {
  font-weight: 600;
  font-size: 1.125rem; /* 18px */
}
.error-indicator .error-message {
  font-size: 0.875rem; /* 14px */
  margin-top: 0.25rem; /* 4px */
}
.error-indicator .retry-button {
  margin-top: 1rem; /* 16px */
  padding: 0.5rem 1rem; /* px-4 py-2 */
  background-color: #3b82f6; /* bg-blue-600 */
  color: white;
  border-radius: 0.375rem; /* rounded-md */
  font-size: 0.875rem; /* 14px */
  transition: background-color 0.2s;
}
.error-indicator .retry-button:hover {
  background-color: #2563eb; /* bg-blue-700 */
}
.no-reports-found {
  color: #6b7280; /* text-gray-500 */
}
.no-reports-found .empty-icon {
  font-size: 3rem; /* 48px */
  margin-bottom: 1rem; /* 16px */
  opacity: 0.6;
}
.no-reports-found h3 {
  font-size: 1.125rem; /* 18px */
  margin-bottom: 0.5rem; /* 8px */
  color: #374151; /* text-gray-700 */
}
.no-reports-found p {
  margin-bottom: 1rem; /* 16px */
  font-size: 0.875rem; /* 14px */
}
.reset-btn-empty {
  padding: 0.625rem 1.25rem; /* py-2.5 px-5 */
  background-color: #3b82f6; /* bg-blue-600 */
  color: #fff;
  border: none;
  border-radius: 0.5rem; /* rounded-lg */
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
}
.reset-btn-empty:hover {
  background-color: #2563eb;
}
</style>