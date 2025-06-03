```vue
<template>
  <AppLayout>
    <Head title="Manajemen Top Up" />
    <div class="container py-5">
      <template v-if="canViewTopUp">
        <DashboardHeader />
        <FilterControls :filters="filters" @update-filters="updateFilters" @show-export-modal="showExportModal" />
        <StatusCards :pending-count="pendingCount" :verified-count="verifiedCount" :rejected-count="rejectedCount" />
        <TransactionTable
          :top-ups="topUps"
          :format-currency="formatCurrency"
          :format-date="formatDate"
          :capitalize="capitalize"
          :status-badge-class="statusBadgeClass"
          :get-proof-url="getProofUrl"
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
        <AccessDenied />
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage, Head } from "@inertiajs/vue3";
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
  topUps: Object,
  filters: Object,
  statusCounts: Object,
  canViewTopUp: {
    type: Boolean,
    default: true,
  },
});

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
const proofModalInstance = ref(null);
const proofModalUrl = ref("");
const actionModalRef = ref(null);
const exportModalRef = ref(null);
const exportModalInstance = ref(null);
const selectedTopUp = ref(null);
const isModalOpen = ref(false);

let actionModalInstance = null;

const openActionModal = (topUp) => {
  selectedTopUp.value = topUp;
  isModalOpen.value = true;
  if (!actionModalInstance && actionModalRef.value) {
    actionModalInstance = new Modal(actionModalRef.value);
    actionModalInstance._element.addEventListener("hidden.bs.modal", () => {
      isModalOpen.value = false;
      selectedTopUp.value = null;
    });
  }
  actionModalInstance.show();
};

const closeActionModal = () => {
  actionModalInstance?.hide();
};

const showProofModal = (path) => {
  proofModalUrl.value = getProofUrl(path);
  isModalOpen.value = true;
  if (!proofModalInstance.value && proofModalRef.value) {
    proofModalInstance.value = new Modal(proofModalRef.value);
    proofModalInstance.value._element.addEventListener("hidden.bs.modal", () => {
      isModalOpen.value = false;
      proofModalUrl.value = "";
    });
  }
  proofModalInstance.value.show();
};

const closeProofModal = () => {
  proofModalInstance.value?.hide();
};

const showExportModal = () => {
  if (!exportModalInstance.value && exportModalRef.value) {
    exportModalInstance.value = new Modal(exportModalRef.value);
  }
  exportModalInstance.value.show();
};

const closeExportModal = () => {
  exportModalInstance.value?.hide();
};

const fetchTopUps = () => {
  if (isModalOpen.value) return;
  Inertia.get("/admin/top-ups", filters.value, {
    preserveState: true,
    replace: true,
    only: ["topUps"],
  });
};

let pollingInterval = null;

onMounted(() => {
  pollingInterval = setInterval(() => {
    fetchTopUps();
  }, 30000);
});

onUnmounted(() => {
  clearInterval(pollingInterval);
});

const confirmVerify = () => {
  if (!selectedTopUp.value) return;
  loadingIds.value.push(selectedTopUp.value.id);
  Inertia.post(`/admin/top-ups/${selectedTopUp.value.id}/verify`, {}, {
    onFinish: () => {
      loadingIds.value = loadingIds.value.filter((id) => id !== selectedTopUp.value.id);
      closeActionModal();
    },
  });
};

const confirmSetPending = () => {
  if (!selectedTopUp.value) return;
  loadingIds.value.push(selectedTopUp.value.id);
  Inertia.post(
    `/admin/top-ups/${selectedTopUp.value.id}/update-status`,
    { status: "pending" },
    {
      onFinish: () => {
        loadingIds.value = loadingIds.value.filter((id) => id !== selectedTopUp.value.id);
        closeActionModal();
      },
    }
  );
};

const confirmReject = () => {
  if (!selectedTopUp.value) return;
  loadingIds.value.push(selectedTopUp.value.id);
  Inertia.post(`/admin/top-ups/${selectedTopUp.value.id}/reject`, {}, {
    onFinish: () => {
      loadingIds.value = loadingIds.value.filter((id) => id !== selectedTopUp.value.id);
      closeActionModal();
    },
  });
};

const pendingCount = computed(() => props.statusCounts?.pending ?? 0);
const verifiedCount = computed(() => props.statusCounts?.verified ?? 0);
const rejectedCount = computed(() => props.statusCounts?.rejected ?? 0);

const updateFilters = () => {
  Inertia.get("/admin/top-ups", filters.value, {
    preserveState: true,
    replace: true,
  });
};

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", {
    style: "decimal",
    minimumFractionDigits: 0,
  }).format(value);

const formatDate = (date) => {
  const dateObj = new Date(date);
  return {
    date: new Intl.DateTimeFormat("id-ID", {
      day: "2-digit",
      month: "short",
      year: "numeric",
    }).format(dateObj),
    time: new Intl.DateTimeFormat("id-ID", {
      hour: "2-digit",
      minute: "2-digit",
    }).format(dateObj),
  };
};

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);

const statusBadgeClass = (status) => {
  switch (status.toLowerCase()) {
    case "pending":
      return "status-badge-warning";
    case "verified":
      return "status-badge-success";
    case "rejected":
      return "status-badge-danger";
    default:
      return "status-badge-secondary";
  }
};

const getProofUrl = (path) => `/storage/${path}`;

const goToPage = (page) => {
  if (page < 1 || page > props.topUps.last_page) return;
  Inertia.get("/admin/top-ups", { ...filters.value, page }, {
    preserveState: true,
  });
};

const getCurrentDate = () => {
  const today = new Date();
  return today.toISOString().split("T")[0];
};

const formatDatePreview = (dateString) => {
  if (!dateString) return "";
  const date = new Date(dateString);
  return new Intl.DateTimeFormat("id-ID", {
    weekday: "long",
    year: "numeric",
    month: "long",
    day: "numeric",
  }).format(date);
};

const setQuickDateRange = (range) => {
  const today = new Date();
  const todayStr = today.toISOString().split("T")[0];

  switch (range) {
    case "today":
      exportFilters.value.start_date = todayStr;
      exportFilters.value.end_date = todayStr;
      break;
    case "yesterday":
      const yesterday = new Date(today);
      yesterday.setDate(yesterday.getDate() - 1);
      const yesterdayStr = yesterday.toISOString().split("T")[0];
      exportFilters.value.start_date = yesterdayStr;
      exportFilters.value.end_date = yesterdayStr;
      break;
    case "week":
      const weekAgo = new Date(today);
      weekAgo.setDate(weekAgo.getDate() - 7);
      exportFilters.value.start_date = weekAgo.toISOString().split("T")[0];
      exportFilters.value.end_date = todayStr;
      break;
    case "month":
      const monthAgo = new Date(today);
      monthAgo.setDate(monthAgo.getDate() - 30);
      exportFilters.value.start_date = monthAgo.toISOString().split("T")[0];
      exportFilters.value.end_date = todayStr;
      break;
  }
};

const clearDateFilters = () => {
  exportFilters.value.start_date = "";
  exportFilters.value.end_date = "";
};

const exportLogs = () => {
  const params = new URLSearchParams();
  if (exportFilters.value.start_date) params.append("start_date", exportFilters.value.start_date);
  if (exportFilters.value.end_date) params.append("end_date", exportFilters.value.end_date);

  const url = `/admin/top-ups/export-logs?${params.toString()}`;
  window.open(url, "_blank");

  closeExportModal();
};

const toast = ref({
  show: false,
  message: "",
  type: "success",
});

watch(
  () => page.props.flash,
  (flash) => {
    if (flash?.success) {
      toast.value = { show: true, message: flash.success, type: "success" };
      setTimeout(() => {
        toast.value.show = false;
      }, 5000);
    } else if (flash?.error) {
      toast.value = { show: true, message: flash.error, type: "danger" };
      setTimeout(() => {
        toast.value.show = false;
      }, 5000);
    }
  },
  { immediate: true }
);
</script>

<style scoped>
.container {
  max-width: 1200px;
  padding-top: 10px;
  margin-top: -30px !important;
}
</style>
`