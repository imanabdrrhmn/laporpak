<template>
  <AppLayout>
    <Head title="Manajemen Top Up" />
    <div class="container py-5">
      <!-- PERBAIKAN: Gunakan 'hasAccess' untuk kontrol utama -->
      <template v-if="hasAccess">
        <DashboardHeader />
        <StatusCards :pending-count="statusCounts.pending" :verified-count="statusCounts.verified" :rejected-count="statusCounts.rejected" />

        <FilterControls
          v-model="filters"
          @show-export-modal="showExportModal"
        />

        <div v-if="isLoading" class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Memuat data top-up...</p>
        </div>
        <div v-else-if="fetchError" class="alert alert-danger">
            Gagal memuat data: {{ fetchError }}. <button class="btn-link" @click="fetchTopUps">Coba lagi</button>
        </div>

        <TransactionTable
          v-else
          :top-ups="topUpsData"
          :pagination-data="paginationData"
          :format-currency="formatCurrency"
          :format-date="formatDate"
          :capitalize="capitalize"
          :status-badge-class="statusBadgeClass"
          @show-proof-modal="showProofModal"
          @open-action-modal="openActionModal"
          @go-to-page="goToPage"
        />

        <ActionModal
          :selected-top-up="selectedTopUp"
          :loading-ids="loadingIds"
          :format-currency="formatCurrency"
          @confirm-verify="confirmVerify"
          @confirm-set-pending="confirmSetPending"
          @confirm-reject="confirmReject"
          @close-action-modal="closeActionModal"
          ref="actionModalRef"
        />
        <ProofModal
          :proof-modal-url="proofModalUrl"
          @close-proof-modal="closeProofModal"
          ref="proofModalRef"
        />
        <ExportModal
          :export-filters="exportFilters"
          :get-current-date="getCurrentDate"
          :format-date-preview="formatDatePreview"
          @set-quick-date-range="setQuickDateRange"
          @clear-date-filters="clearDateFilters"
          @export-logs="exportLogs"
          @close-export-modal="closeExportModal"
          ref="exportModalRef"
        />
        <Notification
          :show="toast.show"
          :message="toast.message"
          :type="toast.type"
          @close="toast.show = false"
        />
      </template>
      <template v-else>
        <!-- Komponen ini akan ditampilkan jika hasAccess false -->
        <AccessDenied />
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, watch, onMounted, onUnmounted, nextTick } from "vue";
import { usePage, Head } from "@inertiajs/vue3";
import axios from 'axios';
import { debounce } from 'lodash';
import AppLayout from "@/Layouts/AppLayout.vue";
import DashboardHeader from "./components/DashboardHeader.vue";
import FilterControls from "./components/FilterControls.vue";
import StatusCards from "./components/StatusCards.vue";
import TransactionTable from "./components/TransactionTable.vue";
import ActionModal from "./components/ActionModal.vue";
import ProofModal from "./components/ProofModal.vue";
import ExportModal from "./components/ExportModal.vue";
import AccessDenied from "@/components/AccessDenied.vue";
import Notification from "@/components/Notification.vue";
import { Modal } from "bootstrap";

const page = usePage();

const props = defineProps({
  filters: Object,
  // canViewTopUp tidak lagi jadi kontrol utama, tapi bisa tetap ada untuk pengecekan awal
  canViewTopUp: {
    type: Boolean,
    default: true,
  },
});

const topUpsData = ref([]);
const paginationData = ref({
    current_page: 1,
    last_page: 1,
    per_page: 10,
    total: 0,
    links: {},
});
const statusCounts = ref({
    pending: 0,
    verified: 0,
    rejected: 0
});
const isLoading = ref(true);
const fetchError = ref(null);
const hasAccess = ref(props.canViewTopUp); // PERBAIKAN: State baru untuk kontrol akses

const filters = ref({
  status: props.filters?.status || "",
  search: props.filters?.search || "",
});

const exportFilters = ref({
  start_date: "",
  end_date: "",
});

const loadingIds = ref([]);
const proofModalRef = ref(null);
let proofModalInstance = null;
const proofModalUrl = ref("");
const actionModalRef = ref(null);
let actionModalInstance = null;
const exportModalRef = ref(null);
let exportModalInstance = null;
const selectedTopUp = ref(null);

const API_BASE_URL = '/admin/api/topups';

const fetchTopUps = async () => {
  // Hanya fetch jika user punya akses awal
  if (!hasAccess.value) {
      isLoading.value = false;
      return;
  }

  isLoading.value = true;
  fetchError.value = null;

  try {
    const params = {
        ...filters.value,
        page: paginationData.value.current_page || 1,
    };
    const response = await axios.get(API_BASE_URL, { params });
    
    topUpsData.value = response.data.data;

    if (response.data.meta) {
        paginationData.value = {
            current_page: response.data.meta.current_page,
            last_page: response.data.meta.last_page,
            per_page: response.data.meta.per_page,
            total: response.data.meta.total,
            links: response.data.links,
        };
    } else {
        paginationData.value = { current_page: 1, last_page: 1, per_page: 10, total: topUpsData.value.length, links: {} };
    }

    if (response.data.app_meta && response.data.app_meta.statusCounts) {
        statusCounts.value = response.data.app_meta.statusCounts;
    }

  } catch (error) {
    console.error("Error fetching top-ups:", error);
    
    // PERBAIKAN: Cek jika error adalah 403 Forbidden
    if (error.response && error.response.status === 403) {
        hasAccess.value = false; // Set hasAccess ke false
        fetchError.value = "Anda tidak memiliki izin untuk melihat data ini.";
    } else {
        fetchError.value = error.response?.data?.message || 'Gagal memuat data.';
    }
    
    topUpsData.value = [];
    paginationData.value = { current_page: 1, last_page: 1, per_page: 10, total: 0, links: {} };
  } finally {
    isLoading.value = false;
  }
};

onMounted(() => {
  fetchTopUps();

  let pollingInterval = setInterval(() => {
    const isAnyModalOpen = (proofModalInstance && proofModalInstance._isShown) ||
                           (actionModalInstance && actionModalInstance._isShown) ||
                           (exportModalInstance && exportModalInstance._isShown);
    if (!isAnyModalOpen && !isLoading.value) {
      fetchTopUps();
    }
  }, 30000);

  onUnmounted(() => {
    clearInterval(pollingInterval);
  });
});

const debouncedFetch = debounce(() => {
    fetchTopUps();
}, 300);

watch(filters, (newFilters, oldFilters) => {
  if (JSON.stringify(newFilters) !== JSON.stringify(oldFilters)) {
      paginationData.value.current_page = 1;
  }
  debouncedFetch();
}, { deep: true });


const goToPage = (pageNumber) => {
    if (pageNumber === paginationData.value.current_page || pageNumber < 1 || pageNumber > paginationData.value.last_page) {
      return;
    }
    paginationData.value.current_page = pageNumber;
    fetchTopUps();
};

const openActionModal = (topUp) => {
  selectedTopUp.value = topUp;
  if (!actionModalInstance) {
    nextTick(() => {
      if (actionModalRef.value && actionModalRef.value.$el) {
        actionModalInstance = new Modal(actionModalRef.value.$el);
        actionModalInstance.show();
      }
    });
  } else {
    actionModalInstance.show();
  }
};

const closeActionModal = () => {
  actionModalInstance?.hide();
  selectedTopUp.value = null;
};

const performAction = async (actionUrl, successMessage) => {
    if (!selectedTopUp.value) return;
    const topUpId = selectedTopUp.value.id;
    loadingIds.value.push(topUpId);
    try {
        await axios.post(actionUrl);
        showToast(successMessage, 'success');
        fetchTopUps();
    } catch (error) {
        const message = error.response?.data?.message || 'Terjadi kesalahan.';
        showToast(message, 'danger');
    } finally {
        loadingIds.value = loadingIds.value.filter(id => id !== topUpId);
        closeActionModal();
    }
}

const confirmVerify = () => {
  performAction(`${API_BASE_URL}/${selectedTopUp.value.id}/verify`, 'Top up berhasil diverifikasi.');
};

const confirmSetPending = () => {
  console.warn("Aksi 'Set Pending' belum memiliki endpoint API.");
};

const confirmReject = () => {
  performAction(`${API_BASE_URL}/${selectedTopUp.value.id}/reject`, 'Top up berhasil ditolak.');
};

const showProofModal = (url) => {
  proofModalUrl.value = url;
  if (!proofModalInstance) {
    nextTick(() => {
      if (proofModalRef.value && proofModalRef.value.$el) {
        proofModalInstance = new Modal(proofModalRef.value.$el);
        proofModalInstance.show();
      }
    });
  } else {
    proofModalInstance.show();
  }
};

const closeProofModal = () => {
  proofModalInstance?.hide();
};

const showExportModal = () => {
  if (!exportModalInstance) {
    nextTick(() => {
      if (exportModalRef.value && exportModalRef.value.$el) {
        exportModalInstance = new Modal(exportModalRef.value.$el);
        exportModalInstance.show();
      }
    });
  } else {
    exportModalInstance.show();
  }
};

const closeExportModal = () => {
  exportModalInstance?.hide();
};

const exportLogs = () => {
  const params = new URLSearchParams();
  if (exportFilters.value.start_date) params.append("start_date", exportFilters.value.start_date);
  if (exportFilters.value.end_date) params.append("end_date", exportFilters.value.end_date);
  const url = `/admin/top-ups/export-logs?${params.toString()}`;
  window.open(url, "_blank");
  closeExportModal();
};

const formatCurrency = (value) => new Intl.NumberFormat("id-ID", { style: "decimal", minimumFractionDigits: 0 }).format(value || 0);
const formatDate = (date) => {
  if (!date) return { date: '-', time: '-' };
  const d = new Date(date);
  return {
    date: new Intl.DateTimeFormat("id-ID", { day: "2-digit", month: "short", year: "numeric" }).format(d),
    time: new Intl.DateTimeFormat("id-ID", { hour: "2-digit", minute: "2-digit" }).format(d),
  };
};
const capitalize = (str) => str ? str.charAt(0).toUpperCase() + str.slice(1) : '';
const statusBadgeClass = (status) => {
  const statusMap = { pending: 'status-badge-warning', verified: 'status-badge-success', rejected: 'status-badge-danger' };
  return statusMap[status?.toLowerCase()] || 'status-badge-secondary';
};
const getProofUrl = (path) => path ? `/storage/${path}` : '';
const getCurrentDate = () => new Date().toISOString().split("T")[0];
const formatDatePreview = (dateString) => dateString ? new Intl.DateTimeFormat("id-ID", { weekday: "long", year: "numeric", month: "long", day: "numeric" }).format(new Date(dateString)) : "";
const setQuickDateRange = (range) => {
  const today = new Date();
  const todayStr = today.toISOString().split("T")[0];
  let startDate = new Date(today);
  switch (range) {
    case "today": exportFilters.value.start_date = todayStr; exportFilters.value.end_date = todayStr; break;
    case "yesterday":
      const yesterday = new Date(today); yesterday.setDate(yesterday.getDate() - 1);
      const yesterdayStr = yesterday.toISOString().split("T")[0];
      exportFilters.value.start_date = yesterdayStr; exportFilters.value.end_date = yesterdayStr;
      break;
    case "week": startDate.setDate(startDate.getDate() - 7); exportFilters.value.start_date = startDate.toISOString().split("T")[0]; exportFilters.value.end_date = todayStr; break;
    case "month": startDate.setDate(startDate.getDate() - 30); exportFilters.value.start_date = startDate.toISOString().split("T")[0]; exportFilters.value.end_date = todayStr; break;
  }
};
const clearDateFilters = () => {
  exportFilters.value.start_date = "";
  exportFilters.value.end_date = "";
};

const toast = ref({ show: false, message: "", type: "success" });
function showToast(message, type = "success") {
    toast.value = { show: true, message, type };
    setTimeout(() => { toast.value.show = false; }, 5000);
}
watch(() => page.props.flash, (flash) => {
  if (flash?.success) showToast(flash.success, 'success');
  else if (flash?.error) showToast(flash.error, 'danger');
}, { immediate: true });
</script>

<style scoped>
.container {
  max-width: 1200px;
  padding-top: 10px;
  margin-top: -30px !important;
}
.btn-link {
    border: none;
    background: none;
    color: #0d6efd;
    text-decoration: underline;
    padding: 0;
}
</style>
