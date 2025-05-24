<!-- resources/js/Pages/Pelaporan/LaporanManajemen.vue -->
<template>
  <AppLayout>
    <Head title="Manajemen Laporan" />
    <div class="wrapper">
      <div class="container">
        <DashboardHeader :report-stats="reportStats" />
        <FilterSection
          v-model:search-query="searchQuery"
          v-model:selected-category="selectedCategory"
          v-model:selected-service="selectedService"
          v-model:selected-status="selectedStatus"
          v-model:sort-direction="sortDirection"
          v-model:view-mode="viewMode"
          :categories="categories"
          :services="services"
          :has-active-filters="hasActiveFilters"
          :filtered-reports="filteredReports"
          @reset-filters="resetFilters"
        />
        <ReportsContainer
          :reports="paginatedReports"
          :view-mode="viewMode"
          :current-page="currentPage"
          :items-per-page="itemsPerPage"
          :loading="loading"
          @view-report="viewReport"
          @quick-action="quickAction"
        />
        <Pagination
          v-if="filteredReports.length > 0"
          v-model:current-page="currentPage"
          :total-pages="totalPages"
        />
        <Modal
          :report="currentReport"
          :is-visible="showModal"
          @close="closeModal"
          @update-status="updateReportStatus"
        />
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import { usePage, Head, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import DashboardHeader from './Components/DashboardHeader.vue';
import FilterSection from './Components/FilterSection.vue';
import ReportsContainer from './Components/ReportsContainer.vue';
import Pagination from './Components/Pagination.vue';
import Modal from './Components/LaporanDetailModal.vue';

const props = defineProps({
  reports: {
    type: Array,
    default: () => [],
    validator: (reports) => {
      return reports.every(report => {
        const isValid = report && typeof report.id !== 'undefined' && report.user && typeof report.user.name !== 'undefined';
        if (!isValid) {
          console.warn('Invalid report data:', report);
        }
        return isValid;
      });
    }
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
const viewMode = ref('grid');
const currentPage = ref(1);
const itemsPerPage = ref(20);
const loading = ref({});
const showModal = ref(false);
const currentReport = ref({});

// Computed properties
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
            report.description?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            report.category?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            report.service?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
            report.user?.name?.toLowerCase().includes(searchQuery.value.toLowerCase()) ||
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

const paginatedReports = computed(() => {
  const startIndex = (currentPage.value - 1) * itemsPerPage.value;
  const endIndex = startIndex + itemsPerPage.value;
  return filteredReports.value.slice(startIndex, endIndex);
});

const totalPages = computed(() => {
  return Math.ceil(filteredReports.value.length / itemsPerPage.value) || 1;
});

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
  if (!report) {
    console.error('Attempted to view undefined report');
    return;
  }
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

const quickAction = ({ report, action }) => {
  if (!report || !report.id) {
    console.error('Invalid report in quickAction:', report);
    alert('Laporan tidak valid. Silakan coba lagi.');
    return;
  }

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
  } else {
    console.error('Invalid action:', action);
    return;
  }

  loading.value[reportId] = true;

  router.patch(endpoint, {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      updateReportStatus(reportId, newStatus);
      loading.value[reportId] = false;
    },
    onError: (errors) => {
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

const formatDate = (iso) => {
  if (!iso) return '';
  const d = new Date(iso);
  return d.toLocaleString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
};

const formatDateShort = (iso) => {
  if (!iso) return '';
  const d = new Date(iso);
  return d.toLocaleString('id-ID', {
    day: 'numeric', month: 'short',
    hour: '2-digit', minute: '2-digit'
  });
};

const truncate = (text, max) => {
  return text && text.length > max ? text.substring(0, max) + '…' : text || '';
};

const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';

watch([searchQuery, selectedCategory, selectedService, selectedStatus], () => {
  currentPage.value = 1;
});

// Debugging log
watch(props.reports, (newReports) => {
  console.log('Reports received:', newReports);
});
</script>

<style scoped>
.wrapper {
  padding: 20px;
}

.container {
  max-width: 1400px;
  margin: 0 auto;
}
</style>