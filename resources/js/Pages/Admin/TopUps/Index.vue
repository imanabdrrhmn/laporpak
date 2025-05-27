<template>
  <AppLayout>
    <Head title="Manajemen Top Up" />
    <div class="container py-5">
      <template v-if="canViewTopUp">
        <!-- Dashboard Header -->
        <div class="dashboard-header mb-4">
          <h1 class="fw-bold text-dark">Panel Admin</h1>
          <h2 class="fw-light text-secondary">Manajemen Top Up</h2>
        </div>

        <!-- Filter dan Search -->
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <div class="filter-controls">
              <div class="filter-item">
                <label class="form-label text-muted small mb-1">Status Filter</label>
                <select v-model="filters.status" @change="updateFilters" class="form-select form-select-sm">
                  <option value="">Semua Status</option>
                  <option value="pending">Pending</option>
                  <option value="verified">Verified</option>
                  <option value="rejected">Rejected</option>
                </select>
              </div>

              <div class="filter-item flex-grow-1">
                <label class="form-label text-muted small mb-1">Cari User</label>
                <div class="input-group">
                  <input
                    v-model="filters.search"
                    @keyup.enter="updateFilters"
                    type="search"
                    placeholder="Cari user (nama/email)..."
                    class="form-control form-control-sm"
                  />
                  <button @click="updateFilters" class="btn btn-primary btn-sm">
                    <i class="fas fa-search me-1"></i> Cari
                  </button>
                </div>
              </div>

              <div class="filter-item">
                <label class="form-label text-muted small mb-1">Export Data</label>
                <button
                  @click="showExportModal"
                  class="btn btn-outline-success btn-sm w-100"
                >
                  <i class="fas fa-file-export me-1"></i> Export Logs
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Status Cards -->
        <div class="status-cards mb-4">
          <div class="row g-3">
            <div class="col-md-4">
              <div class="card status-card shadow-sm border-warning">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="card-subtitle mb-1 text-muted">Pending</h6>
                      <h3 class="card-title mb-0">{{ pendingCount }}</h3>
                    </div>
                    <div class="status-icon bg-warning-light text-warning">
                      <i class="fas fa-clock"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card status-card shadow-sm border-success">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="card-subtitle mb-1 text-muted">Verified</h6>
                      <h3 class="card-title mb-0">{{ verifiedCount }}</h3>
                    </div>
                    <div class="status-icon bg-success-light text-success">
                      <i class="fas fa-check-circle"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="card status-card shadow-sm border-danger">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center">
                    <div>
                      <h6 class="card-subtitle mb-1 text-muted">Rejected</h6>
                      <h3 class="card-title mb-0">{{ rejectedCount }}</h3>
                    </div>
                    <div class="status-icon bg-danger-light text-danger">
                      <i class="fas fa-times-circle"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Table Top Ups -->
        <div class="card shadow">
          <div class="card-header bg-white py-3">
            <h5 class="mb-0">Riwayat Transaksi</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover m-0">
              <thead class="table-light">
                <tr>
                  <th class="ps-4">User</th>
                  <th>Jumlah</th>
                  <th>Metode Pembayaran</th>
                  <th>Status</th>
                  <th>Tanggal</th>
                  <th>Bukti</th>
                  <th class="text-end pe-4">Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="topUp in topUps.data" :key="topUp.id" class="align-middle">
                  <td class="ps-4">
                    <div class="d-flex align-items-center">
                      <div class="user-avatar bg-primary-light text-primary me-2">
                        <img
                          :src="topUp.user.avatar_url"
                          alt="Avatar"
                          class="user-avatar rounded-circle me-2"
                          style="width: 40px; height: 40px; object-fit: cover;"
                        />
                      </div>
                      <div>
                        <div class="fw-semibold">{{ topUp.user.name }}</div>
                        <small class="text-muted">{{ topUp.user.email }}</small>
                      </div>
                    </div>
                  </td>
                  <td><span class="fw-semibold">Rp {{ formatCurrency(topUp.amount) }}</span></td>
                  <td><span class="payment-method">{{ topUp.payment_method || '-' }}</span></td>
                  <td><span :class="statusBadgeClass(topUp.status)" class="status-badge">{{ capitalize(topUp.status) }}</span></td>
                  <td>
                    <div class="d-flex flex-column">
                      <span>{{ formatDate(topUp.created_at).date }}</span>
                      <small class="text-muted">{{ formatDate(topUp.created_at).time }}</small>
                    </div>
                  </td>
                  <td>
                    <div v-if="topUp.proof" class="proof-container">
                      <img
                        :src="getProofUrl(topUp.proof)"
                        alt="Bukti"
                        class="proof-thumb"
                        @click="showProofModal(topUp.proof)"
                      />
                    </div>
                    <span v-else class="text-muted">-</span>
                  </td>
                  <td class="text-end pe-4">
                    <button
                      class="btn btn-sm btn-outline-secondary"
                      @click="openActionModal(topUp)"
                      title="Aksi"
                    >
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                  </td>
                </tr>
                <tr v-if="topUps.data.length === 0">
                  <td colspan="7" class="text-center py-4">
                    <div class="empty-state">
                      <i class="fas fa-receipt text-muted mb-2"></i>
                      <p>Tidak ada data transaksi yang ditemukan</p>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="topUps.last_page > 1" class="card-footer bg-white">
            <nav aria-label="Page navigation">
              <ul class="pagination pagination-sm justify-content-center flex-wrap mb-0">
                <li
                  class="page-item"
                  :class="{ disabled: !topUps.prev_page_url }"
                  @click.prevent="goToPage(topUps.current_page - 1)"
                >
                  <a class="page-link" href="#">
                    <i class="fas fa-chevron-left"></i>
                  </a>
                </li>
                <li
                  v-for="page in totalPages"
                  :key="page"
                  class="page-item"
                  :class="{ active: page === topUps.current_page }"
                  @click.prevent="goToPage(page)"
                >
                  <a class="page-link" href="#">{{ page }}</a>
                </li>
                <li
                  class="page-item"
                  :class="{ disabled: !topUps.next_page_url }"
                  @click.prevent="goToPage(topUps.current_page + 1)"
                >
                  <a class="page-link" href="#">
                    <i class="fas fa-chevron-right"></i>
                  </a>
                </li>
              </ul>
            </nav>
          </div>
        </div>

        <!-- Modal Aksi -->
        <div
          class="modal fade"
          id="actionModal"
          tabindex="-1"
          aria-labelledby="actionModalLabel"
          aria-hidden="true"
          ref="actionModalRef"
        >
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="actionModalLabel">Pilih Aksi</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeActionModal"></button>
              </div>
              <div class="modal-body">
                <p><strong>User:</strong> {{ selectedTopUp?.user.name }} ({{ selectedTopUp?.user.email }})</p>
                <p><strong>Jumlah:</strong> Rp {{ formatCurrency(selectedTopUp?.amount) }}</p>
                <div class="d-grid gap-2">
                  <button
                    v-if="selectedTopUp?.status === 'pending'"
                    class="btn btn-success"
                    @click="confirmVerify"
                    :disabled="loadingIds.includes(selectedTopUp?.id)"
                  >
                    <i class="fas fa-check me-2"></i> Verifikasi
                  </button>
                  <button
                    v-if="selectedTopUp && selectedTopUp.status !== 'pending'"
                    class="btn btn-warning"
                    @click="confirmSetPending"
                    :disabled="loadingIds.includes(selectedTopUp?.id)"
                  >
                    <i class="fas fa-clock me-2"></i> Atur Pending
                  </button>
                  <button
                    v-if="selectedTopUp && selectedTopUp.status !== 'rejected'"
                    class="btn btn-danger"
                    @click="confirmReject"
                    :disabled="loadingIds.includes(selectedTopUp?.id)"
                  >
                    <i class="fas fa-times me-2"></i> Tolak
                  </button>
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeActionModal">Batal</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Bukti Pembayaran -->
        <div
          class="modal fade"
          id="proofModal"
          tabindex="-1"
          aria-labelledby="proofModalLabel"
          aria-hidden="true"
          ref="proofModalRef"
        >
          <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="proofModalLabel">Bukti Pembayaran</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                  @click="closeProofModal"
                ></button>
              </div>
              <div class="modal-body p-0">
                <img :src="proofModalUrl" alt="Bukti Pembayaran" class="img-fluid w-100" />
              </div>
              <div class="modal-footer">
              </div>
            </div>
          </div>
        </div>

        <!-- Modal Export Logs -->
        <div
          class="modal fade"
          id="exportModal"
          tabindex="-1"
          aria-labelledby="exportModalLabel"
          aria-hidden="true"
          ref="exportModalRef"
        >
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="exportModalLabel">
                  <i class="fas fa-file-export me-2"></i> Ekspor Data
                </h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                  aria-label="Close"
                  @click="closeExportModal"
                ></button>
              </div>
              <div class="modal-body">
                <div class="mb-3">
                  <label for="startDate" class="form-label">Tanggal Mulai</label>
                  <input
                    type="date"
                    id="startDate"
                    v-model="exportFilters.start_date"
                    class="form-control"
                  />
                </div>
                <div class="mb-3">
                  <label for="endDate" class="form-label">Tanggal Akhir</label>
                  <input
                    type="date"
                    id="endDate"
                    v-model="exportFilters.end_date"
                    class="form-control"
                  />
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" @click="closeExportModal">Batal</button>
                <button type="button" class="btn btn-success" @click="exportLogs">
                  <i class="fas fa-download me-1"></i> Unduh CSV
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Toast Notification -->
        <div
          v-if="toast.show"
          :class="['toast-notification', 'toast-' + toast.type]"
          role="alert"
          aria-live="assertive"
          aria-atomic="true"
        >
          <div class="toast-icon">
            <i :class="toastIcon"></i>
          </div>
          <div class="toast-content">
            {{ toast.message }}
          </div>
          <button
            type="button"
            class="toast-close"
            aria-label="Close"
            @click="toast.show = false"
          >
            <i class="fas fa-times"></i>
          </button>
        </div>
      </template>

      <template v-else>
        <div class="access-denied-container text-center py-5 px-3">
          <div class="icon-wrapper mb-4">
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="icon"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="1.5"
              aria-hidden="true"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M12 9v3m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
              />
            </svg>
          </div>
          <h3 class="mb-3 fw-bold text-danger">Akses Ditolak</h3>
          <p class="text-muted fs-5 mx-auto" style="max-width: 400px;">
            Anda tidak memiliki akses untuk halaman ini.<br />
            Silakan hubungi admin untuk mendapatkan akses.
          </p>
        </div>
      </template>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage, Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
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
  start_date: '',
  end_date: '',
});

const loadingIds = ref([]);
const proofModalRef = ref(null);
const proofModalInstance = ref(null);
const proofModalUrl = ref("");

const exportModalRef = ref(null);
const exportModalInstance = ref(null);

const actionModalRef = ref(null);
let actionModalInstance = null;
const selectedTopUp = ref(null);

// Flag untuk pause polling saat modal dibuka
const isModalOpen = ref(false);

// Modal Aksi
const openActionModal = (topUp) => {
  selectedTopUp.value = topUp;
  isModalOpen.value = true;
  if (!actionModalInstance && actionModalRef.value) {
    actionModalInstance = new Modal(actionModalRef.value);
    // Deteksi saat modal ditutup untuk resume polling
    actionModalInstance._element.addEventListener('hidden.bs.modal', () => {
      isModalOpen.value = false;
      selectedTopUp.value = null;
    });
  }
  actionModalInstance.show();
};

const closeActionModal = () => {
  actionModalInstance?.hide();
};

// Modal Bukti Pembayaran
const showProofModal = (path) => {
  proofModalUrl.value = getProofUrl(path);
  isModalOpen.value = true;
  if (!proofModalInstance.value && proofModalRef.value) {
    proofModalInstance.value = new Modal(proofModalRef.value);
    proofModalInstance.value._element.addEventListener('hidden.bs.modal', () => {
      isModalOpen.value = false;
      proofModalUrl.value = "";
    });
  }
  proofModalInstance.value.show();
};

const closeProofModal = () => {
  proofModalInstance.value?.hide();
};

// Polling data topUps, skip kalau modal terbuka
const fetchTopUps = () => {
  if (isModalOpen.value) return;
  Inertia.get('/admin/top-ups', filters.value, {
    preserveState: true,
    replace: true,
    only: ['topUps'],
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

// Fungsi aksi verifikasi, reject, set pending
const confirmVerify = () => {
  if (!selectedTopUp.value) return;
  loadingIds.value.push(selectedTopUp.value.id);
  Inertia.post(`/admin/top-ups/${selectedTopUp.value.id}/verify`, {}, {
    onFinish: () => {
      loadingIds.value = loadingIds.value.filter(id => id !== selectedTopUp.value.id);
      closeActionModal();
    }
  });
};

const confirmSetPending = () => {
  if (!selectedTopUp.value) return;
  loadingIds.value.push(selectedTopUp.value.id);
  Inertia.post(`/admin/top-ups/${selectedTopUp.value.id}/update-status`, { status: 'pending' }, {
    onFinish: () => {
      loadingIds.value = loadingIds.value.filter(id => id !== selectedTopUp.value.id);
      closeActionModal();
    }
  });
};

const confirmReject = () => {
  if (!selectedTopUp.value) return;
  loadingIds.value.push(selectedTopUp.value.id);
  Inertia.post(`/admin/top-ups/${selectedTopUp.value.id}/reject`, {}, {
    onFinish: () => {
      loadingIds.value = loadingIds.value.filter(id => id !== selectedTopUp.value.id);
      closeActionModal();
    }
  });
};

// Hitung jumlah status
const pendingCount = computed(() =>
  props.statusCounts?.pending ?? 0
);
const verifiedCount = computed(() =>
  props.statusCounts?.verified ?? 0
);
const rejectedCount = computed(() =>
  props.statusCounts?.rejected ?? 0
);

// Update filter pencarian/status
const updateFilters = () => {
  Inertia.get("/admin/top-ups", filters.value, {
    preserveState: true,
    replace: true,
  });
};

// Format helper
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
    }).format(dateObj)
  };
};

const capitalize = (str) =>
  str.charAt(0).toUpperCase() + str.slice(1);

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

const totalPages = computed(() => {
  const pages = [];
  for (let i = 1; i <= props.topUps.last_page; i++) {
    pages.push(i);
  }
  return pages;
});

// Modal Export Logs
const showExportModal = () => {
  if (!exportModalInstance.value && exportModalRef.value) {
    exportModalInstance.value = new Modal(exportModalRef.value);
  }
  exportModalInstance.value.show();
};

const closeExportModal = () => {
  exportModalInstance.value?.hide();
};

const exportLogs = () => {
  const params = new URLSearchParams();
  if (exportFilters.value.start_date) params.append("start_date", exportFilters.value.start_date);
  if (exportFilters.value.end_date) params.append("end_date", exportFilters.value.end_date);

  const url = `/admin/top-ups/export-logs?${params.toString()}`;
  window.open(url, "_blank");

  closeExportModal();
};

// Toast notification
const toast = ref({
  show: false,
  message: "",
  type: "success",
});

const toastIcon = computed(() => {
  switch (toast.value.type) {
    case "success":
      return "fas fa-check-circle";
    case "danger":
      return "fas fa-exclamation-circle";
    case "warning":
      return "fas fa-exclamation-triangle";
    default:
      return "fas fa-info-circle";
  }
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

.dashboard-header h1 {
  margin-bottom: 0;
  font-size: 1.75rem;
}

.dashboard-header h2 {
  font-size: 1.4rem;
  margin-top: 0;
}

/* Filter Controls */
.filter-controls {
  display: flex;
  gap: 1rem;
  align-items: flex-end;
  flex-wrap: wrap;
}

.filter-item {
  min-width: 150px;
}

/* Status Cards */
.status-cards .card {
  border-left-width: 4px;
  transition: transform 0.2s;
}

.status-cards .card:hover {
  transform: translateY(-3px);
}

.status-icon {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
}

.bg-warning-light {
  background-color: rgba(255, 193, 7, 0.15);
}

.bg-success-light {
  background-color: rgba(40, 167, 69, 0.15);
}

.bg-danger-light {
  background-color: rgba(220, 53, 69, 0.15);
}

.bg-primary-light {
  background-color: rgba(13, 110, 253, 0.15);
}

/* User Avatar */
.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 0.9rem;
}

/* Status Badges */
.status-badge {
  display: inline-block;
  padding: 0.35em 0.65em;
  font-size: 0.75em;
  font-weight: 500;
  line-height: 1;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: 50rem;
}

.status-badge-warning {
  color: #856404;
  background-color: #fff3cd;
  border: 1px solid #ffeeba;
}

.status-badge-success {
  color: #155724;
  background-color: #d4edda;
  border: 1px solid #c3e6cb;
}

.status-badge-danger {
  color: #721c24;
  background-color: #f8d7da;
  border: 1px solid #f5c6cb;
}

.status-badge-secondary {
  color: #383d41;
  background-color: #e2e3e5;
  border: 1px solid #d6d8db;
}

/* Proof Image */
.proof-container {
  width: 60px;
  height: 40px;
  overflow: hidden;
  border-radius: 4px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

.proof-thumb {
  width: 100%;
  height: 100%;
  object-fit: cover;
  cursor: pointer;
  transition: transform 0.2s ease-in-out;
}

.proof-thumb:hover {
  transform: scale(1.08);
}

/* Empty State */
.empty-state {
  padding: 1rem;
  text-align: center;
}

.empty-state i {
  font-size: 2.5rem;
  display: block;
}

.empty-state p {
  margin-bottom: 0;
  color: #6c757d;
}

/* Toast Notification */
.toast-notification {
  position: fixed;
  bottom: 20px;
  right: 20px;
  display: flex;
  align-items: center;
  min-width: 300px;
  max-width: 400px;
  padding: 1rem;
  border-radius: 8px;
  box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
  z-index: 1200;
  animation: slideIn 0.3s ease-out;
}

.toast-success {
  background-color: #d4edda;
  border-left: 4px solid #28a745;
}

.toast-danger {
  background-color: #f8d7da;
  border-left: 4px solid #dc3545;
}

.toast-icon {
  margin-right: 0.75rem;
  font-size: 1.25rem;
}

.toast-success .toast-icon {
  color: #28a745;
}

.toast-danger .toast-icon {
  color: #dc3545;
}

.toast-content {
  flex-grow: 1;
}

.toast-close {
  background: none;
  border: none;
  font-size: 0.9rem;
  opacity: 0.5;
  transition: opacity 0.2s;
}

.toast-close:hover {
  opacity: 1;
}

@keyframes slideIn {
  from {
    transform: translateX(100%);
    opacity: 0;
  }
  to {
    transform: translateX(0);
    opacity: 1;
  }
}

/* Responsive Styles */
@media (max-width: 992px) {
  .filter-controls {
    flex-direction: column;
    align-items: stretch;
  }
  
  .filter-item {
    width: 100%;
    min-width: auto;
  }
}

@media (max-width: 768px) {
  .table th,
  .table td {
    white-space: normal;
  }
  
  .user-avatar {
    width: 32px;
    height: 32px;
    font-size: 0.8rem;
  }
}

@media (max-width: 576px) {
  .dashboard-header h1 {
    font-size: 1.5rem;
  }
  
  .dashboard-header h2 {
    font-size: 1.2rem;
  }
  
  .card-body {
    padding: 0.75rem;
  }
  
  .table {
    display: block;
    overflow-x: auto;
    white-space: nowrap;
  }
}

.action-menu {
  box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
}

.modal-body .btn {
  width: 100%;
  margin-bottom: 0.5rem;
}

.modal-body .btn:last-child {
  margin-bottom: 0;
}

.access-denied-container {
  user-select: none;
}

.icon-wrapper {
  width: 80px;
  height: 80px;
  color: #dc3545; /* Bootstrap danger color */
  margin: 0 auto;
}

.icon {
  width: 100%;
  height: 100%;
}

</style>
