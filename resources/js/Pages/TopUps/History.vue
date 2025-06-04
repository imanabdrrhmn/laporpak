<template>
  <AppLayout>
    <div class="container py-4">
      <div class="topup-history-container mx-auto">
        <h1 class="mb-4 fw-bold" >Riwayat Top Up</h1>

        <div class="d-flex flex-column flex-md-row justify-content-between gap-5 mb-4">
          <input
            type="text"
            v-model="searchTerm"
            class="form-control w-auto"
            placeholder="Cari jumlah deposit..."
            @input="goToPage(1)"
          />
          <select v-model="selectedStatus" class="form-select w-auto">
            <option value="">Semua Status</option>
            <option value="verified">Verified</option>
            <option value="pending">Pending</option>
            <option value="failed">Gagal</option>
          </select>
        </div>

        <div v-if="paginatedTopUps.length">
          <div
            v-for="topUp in paginatedTopUps"
            :key="topUp.id"
            class="topup-item card mb-3"
            @click="openDetailModal(topUp)"
          >
            <div class="card-body d-flex justify-content-between align-items-center flex-wrap">
              <div class="details mb-2 mb-md-0">
                <div class="amount fw-bold">Rp {{ formatCurrency(topUp.amount) }}</div>
                <div class="date text-muted small">
                  {{ formatDate(topUp.created_at, 'list') }}
                </div>
              </div>
              <div>
                <span
                  class="badge rounded-pill"
                  :class="statusBadgeClass(topUp.status)"
                >
                  {{ topUp.status.toLowerCase() === 'success' ? 'Verified' : capitalize(topUp.status) }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="text-center text-muted fst-italic py-5">
          Tidak ada riwayat top up untuk kriteria ini.
        </div>

        <nav v-if="pageCount > 1" aria-label="Pagination" class="mt-4 d-flex justify-content-center">
          <ul class="pagination">
            <li :class="['page-item', { disabled: currentPage === 1 }]" @click.prevent="goToPage(currentPage - 1)">
              <a href="#" class="page-link">Previous</a>
            </li>
            <li
              v-for="page in pageCount"
              :key="page"
              :class="['page-item', { active: currentPage === page }]"
              @click.prevent="goToPage(page)"
            >
              <a href="#" class="page-link">{{ page }}</a>
            </li>
            <li :class="['page-item', { disabled: currentPage === pageCount }]" @click.prevent="goToPage(currentPage + 1)">
              <a href="#" class="page-link">Next</a>
            </li>
          </ul>
        </nav>
      </div>
    </div>

    <div
      class="modal fade"
      id="detailModal"
      tabindex="-1"
      aria-labelledby="detailModalLabel"
      aria-hidden="true"
      ref="detailModalRef"
    >
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 0.75rem;">
          <div class="modal-body p-4">
            <div v-if="selectedTopUp">
              <h3 class="text-center fw-bold mb-1" style="font-size: 1.25rem;">Detail Top Up</h3>
              <p class="text-center fw-bold mb-3" style="font-size: 1.75rem;">
                Rp {{ formatCurrency(selectedTopUp.amount) }}
              </p>

              <div class="detail-item d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Status</span>
                <span
                  class="badge rounded-pill"
                  :class="statusBadgeClass(selectedTopUp.status)"
                >
                  {{ selectedTopUp.status.toLowerCase() === 'success' ? 'Verified' : capitalize(selectedTopUp.status) }}
                </span>
              </div>
              <div class="detail-item d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">Tanggal</span>
                <span>{{ formatDate(selectedTopUp.created_at, 'modal') }}</span>
              </div>
              <div class="detail-item d-flex justify-content-between align-items-center mb-3">
                <span class="text-muted">Metode Pembayaran</span>
                <span>{{ selectedTopUp.payment_method || 'Tidak tersedia' }}</span>
              </div>

              <div v-if="proofImageUrl" class="text-center mt-3">
                <p class="fw-bold">Bukti Pembayaran:</p>
                <img
                  :src="proofImageUrl"
                  alt="Bukti Pembayaran"
                  class="img-fluid rounded shadow-sm"
                  style="max-height: 200px; max-width: 100%;"
                />
              </div>
              <div v-else-if="!proofImageUrl && selectedTopUp.status.toLowerCase() !== 'pending'" class="text-muted fst-italic text-center mt-3 small">
                 Bukti pembayaran tidak tersedia.
              </div>
            </div>
            <div class="d-grid mt-4">
              <button type="button" class="btn btn-primary" @click="closeDetailModal">Tutup</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Modal } from 'bootstrap';

const props = defineProps({
  topUps: {
    type: Array,
    default: () => [],
  },
});

const selectedStatus = ref('');
const searchTerm = ref('');
const currentPage = ref(1);
const itemsPerPage = 5;

const selectedTopUp = ref(null);
const detailModalRef = ref(null);
let detailModalInstance = null;

const proofImageUrl = computed(() => {
  if (!selectedTopUp.value || !selectedTopUp.value.proof) return '';
  return `/storage/${selectedTopUp.value.proof}`;
});

const filteredTopUps = computed(() => {
  return props.topUps.filter((t) => {
    const statusMatch =
      !selectedStatus.value || t.status.toLowerCase() === selectedStatus.value.toLowerCase();

    const searchValue = searchTerm.value.trim();
    if (!searchValue) return statusMatch;

    const searchNumber = Number(searchValue.replace(/\D/g, ''));
    const amountMatch = !isNaN(searchNumber)
      ? t.amount.toString().includes(searchNumber.toString())
      : t.amount.toString().includes(searchValue);

    return statusMatch && amountMatch;
  });
});

const pageCount = computed(() => Math.ceil(filteredTopUps.value.length / itemsPerPage));

const paginatedTopUps = computed(() => {
  const start = (currentPage.value - 1) * itemsPerPage;
  return filteredTopUps.value.slice(start, start + itemsPerPage);
});

const formatCurrency = (value) =>
  new Intl.NumberFormat('id-ID', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  }).format(value);

const formatDate = (dateString, context = 'list') => {
  const date = new Date(dateString);
  const options = {
    day: context === 'modal' ? 'numeric' : '2-digit',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
  };
  return new Intl.DateTimeFormat('id-ID', options).format(date).replace(/\./g, ':');
};

const statusBadgeClass = (status) => {
  switch (status.toLowerCase()) {
    case 'verified':
      return 'bg-verified-blue text-white';
    case 'pending':
      return 'bg-warning text-dark';
    case 'failed':
    case 'gagal':
      return 'bg-danger text-white';
    default:
      return 'bg-secondary text-white';
  }
};

const capitalize = (str) => {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

const goToPage = (page) => {
  if (page < 1 || page > pageCount.value) return;
  currentPage.value = page;
};

const openDetailModal = (topUp) => {
  selectedTopUp.value = topUp;
  if (!detailModalInstance && detailModalRef.value) {
     detailModalInstance = new Modal(detailModalRef.value);
  }
  if (detailModalInstance) {
    detailModalInstance.show();
  }
};

const closeDetailModal = () => {
  if (detailModalInstance) {
    detailModalInstance.hide();
  }
};

onMounted(() => {
  if (detailModalRef.value) {
    detailModalInstance = new Modal(detailModalRef.value);
  }
});
</script>

<style scoped>
.topup-history-container {
  max-width: 1000px;
}

.topup-history-container h1 {
  font-size: 2rem;
  font-weight: 600;
  color: #2F6CD6;
}

.topup-item.card {
  border: 1px solid #dee2e6;
  border-radius: 0.5rem;
  cursor: pointer;
  transition: box-shadow 0.2s ease-in-out;
  background-color: #fff;
  padding: 0.75rem;
  max-width: 100%;
  min-height: 150px; /* Set a minimum height for consistency */
}

.topup-item.card:hover {
  box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.topup-item .amount {
  font-size: 1.3rem;
  font-weight: 600;
  color: #2F6CD6;
}

.topup-item .date {
  font-size: 0.8rem;
  color: #6c757d;
}

.badge {
  font-size: 0.78rem;
  padding: 0.45em 0.9em;
  font-weight: 500;
}

.bg-verified-blue {
  background-color: #4361EE;
  color: #052c65;
}

.modal-content {
  box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
  border: none;
  border-radius: 0.6rem;
}

.modal-body h3.fw-bold {
  font-size: 1.2rem;
  font-weight: 600;
  color: #212529;
}

.modal-body p.text-center.fw-bold {
  font-size: 1.65rem;
  font-weight: 600;
  color: #212529;
  margin-bottom: 1.25rem !important;
}

.modal-body .detail-item {
  margin-bottom: 0.6rem !important;
}

.modal-body .detail-item span:first-child {
 color: #6c757d;
 font-weight: 400;
 font-size: 0.9rem;
}

.modal-body .detail-item span:last-child,
.modal-body .detail-item .badge {
 color: #212529;
 font-weight: 500;
 font-size: 0.9rem;
}

.btn-primary {
    background-color: #0D6EFD;
    border-color: #0D6EFD;
    padding: 0.55rem 1.2rem;
    font-weight: 500;
    font-size: 0.95rem;
}

@media (max-width: 576px) {
  .topup-history-container h1 {
    font-size: 1.4rem;
    text-align: center;
  }
  .topup-item .card-body {
    flex-direction: column;
    align-items: flex-start !important;
  }
  .topup-item .details {
    margin-bottom: 0.6rem;
  }
  .topup-item .badge {
    align-self: flex-start;
  }

   .modal-body p.text-center.fw-bold {
    font-size: 1.45rem !important;
  }
  .modal-body h3.fw-bold {
    font-size: 1.1rem !important;
  }
  .modal-body .detail-item span {
    font-size: 0.85rem;
  }
}
</style>
