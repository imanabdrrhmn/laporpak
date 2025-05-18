<template>
  <AppLayout>
    <Head title="Manajemen Top Up" />
    <div class="container py-4">
      <h1 class="mb-4 text-center fw-bold">Admin Panel - Top Up Management</h1>

      <!-- Filter dan Search -->
      <div class="filter-controls">
        <select v-model="filters.status" @change="updateFilters" class="form-select">
          <option value="">Semua Status</option>
          <option value="pending">Pending</option>
          <option value="verified">Verified</option>
          <option value="rejected">Rejected</option>
        </select>

        <input
          v-model="filters.search"
          @keyup.enter="updateFilters"
          type="search"
          placeholder="Cari user (nama/email)..."
          class="form-control"
        />
        <button @click="updateFilters" class="btn btn-primary flex-shrink-0">Cari</button>
      </div>

      <!-- Table Top Ups -->
      <div class="table-responsive">
        <table class="table table-striped table-hover shadow-sm">
          <thead class="table-dark">
            <tr>
              <th>User</th>
              <th>Jumlah</th>
              <th>Metode Pembayaran</th>
              <th>Status</th>
              <th>Tanggal</th>
              <th>Bukti</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="topUp in topUps.data" :key="topUp.id">
              <td data-label="User">
                {{ topUp.user.name }}<br />
                <small class="text-muted">{{ topUp.user.email }}</small>
              </td>
              <td data-label="Jumlah">Rp {{ formatCurrency(topUp.amount) }}</td>
              <td data-label="Metode Pembayaran">{{ topUp.payment_method || '-' }}</td>
              <td data-label="Status">
                <span :class="statusBadgeClass(topUp.status)" class="badge">
                  {{ capitalize(topUp.status) }}
                </span>
              </td>
              <td data-label="Tanggal">{{ formatDate(topUp.created_at) }}</td>
              <td data-label="Bukti" class="bukti">
                <img
                  v-if="topUp.proof"
                  :src="getProofUrl(topUp.proof)"
                  alt="Bukti"
                  class="proof-thumb"
                  @click="showProofModal(topUp.proof)"
                />
                <span v-else class="text-muted">-</span>
              </td>
              <td data-label="Aksi" class="aksi">
                <button
                  v-if="topUp.status === 'pending'"
                  @click="verifyTopUp(topUp.id)"
                  class="btn btn-sm btn-success me-2"
                  :disabled="loadingIds.includes(topUp.id)"
                >
                  <span v-if="loadingIds.includes(topUp.id)" class="spinner-border spinner-border-sm"></span>
                  Verifikasi
                </button>
                <button
                  v-if="topUp.status === 'pending'"
                  @click="rejectTopUp(topUp.id)"
                  class="btn btn-sm btn-danger"
                  :disabled="loadingIds.includes(topUp.id)"
                >
                  <span v-if="loadingIds.includes(topUp.id)" class="spinner-border spinner-border-sm"></span>
                  Tolak
                </button>
                <span v-if="topUp.status !== 'pending'" class="text-muted">-</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <nav v-if="topUps.last_page > 1" aria-label="Page navigation" class="mt-3">
        <ul class="pagination justify-content-center flex-wrap">
          <li
            class="page-item"
            :class="{ disabled: !topUps.prev_page_url }"
            @click.prevent="goToPage(topUps.current_page - 1)"
          >
            <a class="page-link" href="#">Previous</a>
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
            <a class="page-link" href="#">Next</a>
          </li>
        </ul>
      </nav>

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
            <div class="modal-body text-center">
              <img :src="proofModalUrl" alt="Bukti Pembayaran" class="img-fluid rounded" />
            </div>
          </div>
        </div>
      </div>

      <!-- Toast Notification -->
      <div
        v-if="toast.show"
        :class="['toast align-items-center text-bg-' + toast.type, 'position-fixed bottom-0 end-0 m-3']"
        role="alert"
        aria-live="assertive"
        aria-atomic="true"
        style="z-index: 1200; min-width: 250px;"
      >
        <div class="d-flex">
          <div class="toast-body">{{ toast.message }}</div>
          <button
            type="button"
            class="btn-close btn-close-white me-2 m-auto"
            aria-label="Close"
            @click="toast.show = false"
          ></button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from "vue";
import { Inertia } from "@inertiajs/inertia";
import { usePage, Head } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import { Modal } from "bootstrap";

const page = usePage();

const props = defineProps({
  topUps: Object,
  filters: Object,
});

const filters = ref({
  status: props.filters.status || "",
  search: props.filters.search || "",
});

const loadingIds = ref([]);
const proofModalRef = ref(null);
const proofModalInstance = ref(null);
const proofModalUrl = ref("");

const updateFilters = () => {
  Inertia.get("/admin/top-ups", filters.value, { preserveState: true, replace: true });
};

const formatCurrency = (value) =>
  new Intl.NumberFormat("id-ID", { style: "decimal", minimumFractionDigits: 0 }).format(value);

const formatDate = (date) =>
  new Intl.DateTimeFormat("id-ID", {
    day: "2-digit",
    month: "short",
    year: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  }).format(new Date(date));

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);

const statusBadgeClass = (status) => {
  switch (status.toLowerCase()) {
    case "pending":
      return "bg-warning text-dark";
    case "verified":
      return "bg-success";
    case "rejected":
      return "bg-danger";
    default:
      return "bg-secondary";
  }
};

const getProofUrl = (path) => `/storage/${path}`;

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
    } else if (flash?.error) {
      toast.value = { show: true, message: flash.error, type: "danger" };
    }
  },
  { immediate: true }
);

const verifyTopUp = async (id) => {
  loadingIds.value.push(id);
  Inertia.post(`/admin/top-ups/${id}/verify`, {}, {
    onFinish: () => {
      loadingIds.value = loadingIds.value.filter((i) => i !== id);
    },
  });
};

const rejectTopUp = async (id) => {
  loadingIds.value.push(id);
  Inertia.post(`/admin/top-ups/${id}/reject`, {}, {
    onFinish: () => {
      loadingIds.value = loadingIds.value.filter((i) => i !== id);
    },
  });
};

const goToPage = (page) => {
  if (page < 1 || page > props.topUps.last_page) return;
  Inertia.get("/admin/top-ups", { ...filters.value, page }, { preserveState: true, replace: true });
};

const totalPages = computed(() => {
  const pages = [];
  for (let i = 1; i <= props.topUps.last_page; i++) {
    pages.push(i);
  }
  return pages;
});

const showProofModal = (path) => {
  proofModalUrl.value = getProofUrl(path);
  if (!proofModalInstance.value && proofModalRef.value) {
    proofModalInstance.value = new Modal(proofModalRef.value);
  }
  proofModalInstance.value.show();
};

const closeProofModal = () => {
  proofModalInstance.value?.hide();
};
</script>

<style scoped>
.filter-controls {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 1rem;
  margin-bottom: 1.5rem;
}

.filter-controls > * {
  min-width: 200px;
  flex-grow: 1;
  max-width: 300px;
}

.table-responsive {
  overflow-x: auto;
  -webkit-overflow-scrolling: touch; /* smooth scrolling on iOS */
}

/* Tabel desktop */
.table th,
.table td {
  vertical-align: middle;
  white-space: nowrap;
}

/* Responsive: Ubah ke "card list" di mobile */
@media (max-width: 576px) {
  table thead {
    display: none;
  }
  table tbody tr {
    display: block;
    margin-bottom: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    background: #fff;
  }
  table tbody tr td {
    display: flex;
    justify-content: space-between;
    padding: 0.3rem 0;
    white-space: normal;
  }
  table tbody tr td::before {
    content: attr(data-label);
    font-weight: 600;
    flex: 1;
    color: #555;
  }
  /* Sembunyikan kolom bukti dan aksi di mobile supaya sederhana */
  table tbody tr td.bukti,
  table tbody tr td.aksi {
    display: none;
  }
}

.pagination {
  flex-wrap: wrap;
}

.proof-thumb {
  max-height: 50px;
  cursor: pointer;
  border-radius: 4px;
  transition: transform 0.2s ease-in-out;
}
.proof-thumb:hover {
  transform: scale(1.1);
}

/* Responsive button sizes on small screens */
@media (max-width: 576px) {
  .btn {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
  }
}
</style>
