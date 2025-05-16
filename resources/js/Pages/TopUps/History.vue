<template>
  <AppLayout>
    <div class="topup-history container py-4">
      <h1 class="mb-4 text-center fw-bold">Riwayat Top Up</h1>

      <!-- Filter dan Search -->
      <div class="d-flex flex-column flex-md-row justify-content-center gap-3 mb-4">
        <select v-model="selectedStatus" class="form-select w-auto">
          <option value="">Semua Status</option>
          <option value="success">Berhasil</option>
          <option value="pending">Pending</option>
          <option value="failed">Gagal</option>
        </select>

        <input
          type="text"
          v-model="searchTerm"
          class="form-control w-auto"
          placeholder="Cari jumlah deposit..."
          @input="goToPage(1)"
        />
      </div>

      <div v-if="paginatedTopUps.length" class="list-group shadow-sm">
        <div
          v-for="topUp in paginatedTopUps"
          :key="topUp.id"
          class="list-group-item list-group-item-action d-flex justify-content-between align-items-center flex-wrap"
          style="cursor: pointer;"
          @click="openDetailModal(topUp)"
          :title="'Klik untuk detail'"
        >
          <div class="details mb-2 mb-md-0">
            <div class="amount fw-bold fs-5">Rp {{ formatCurrency(topUp.amount) }}</div>
            <div class="date text-muted small">
              {{ formatDate(topUp.created_at) }}
            </div>
          </div>
          <div>
            <span
              class="badge"
              :class="statusBadgeClass(topUp.status)"
              style="font-size: 1rem; padding: 0.5em 0.75em;"
            >
              {{ capitalize(topUp.status) }}
            </span>
          </div>
        </div>
      </div>

      <div v-else class="text-center text-muted fst-italic py-5">
        Tidak ada riwayat top up untuk kriteria ini.
      </div>

      <!-- Pagination -->
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

      <!-- Modal Detail Top Up -->
      <div
        class="modal fade"
        id="detailModal"
        tabindex="-1"
        aria-labelledby="detailModalLabel"
        aria-hidden="true"
        ref="detailModalRef"
      >
        <div class="modal-dialog modal-dialog-centered modal-lg">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="detailModalLabel">Detail Top Up</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="closeDetailModal"></button>
            </div>
            <div class="modal-body" v-if="selectedTopUp">
              <ul class="list-group list-group-flush mb-3">
                <li class="list-group-item d-flex justify-content-between">
                  <strong>Jumlah Deposit</strong>
                  <span>Rp {{ formatCurrency(selectedTopUp.amount) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <strong>Status</strong>
                  <span>{{ capitalize(selectedTopUp.status) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <strong>Tanggal</strong>
                  <span>{{ formatDate(selectedTopUp.created_at) }}</span>
                </li>
                <li class="list-group-item d-flex justify-content-between">
                  <strong>Metode Pembayaran</strong>
                  <span>{{ selectedTopUp.payment_method || 'Tidak tersedia' }}</span>
                </li>
              </ul>

              <div v-if="proofImageUrl" class="text-center">
                <p class="fw-bold">Bukti Pembayaran:</p>
                <img
                  :src="proofImageUrl"
                  alt="Bukti Pembayaran"
                  class="img-fluid rounded shadow-sm"
                  style="max-height: 300px;"
                />
              </div>
              <div v-else class="text-muted fst-italic text-center">
                Bukti pembayaran tidak tersedia.
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" @click="closeDetailModal">Tutup</button>
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
  if (!selectedTopUp.value || !selectedTopUp.value.proof_filename) return '';
  return `/storage/${selectedTopUp.value.proof_filename}`;
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

const formatDate = (dateString) => {
  const options = {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  };
  return new Intl.DateTimeFormat('id-ID', options).format(new Date(dateString));
};

const statusBadgeClass = (status) => {
  switch (status.toLowerCase()) {
    case 'success':
    case 'berhasil':
      return 'bg-success';
    case 'pending':
      return 'bg-warning text-dark';
    case 'failed':
    case 'gagal':
      return 'bg-danger';
    default:
      return 'bg-secondary';
  }
};

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);

const goToPage = (page) => {
  if (page < 1 || page > pageCount.value) return;
  currentPage.value = page;
};

const openDetailModal = (topUp) => {
  selectedTopUp.value = topUp;
  if (!detailModalInstance && detailModalRef.value) {
    detailModalInstance = new Modal(detailModalRef.value);
  }
  detailModalInstance.show();
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
.topup-history {
  max-width: 600px;
  margin: 0 auto;
}

.list-group-item {
  border-radius: 0.5rem;
  margin-bottom: 0.75rem;
  transition: box-shadow 0.3s ease;
  cursor: default;
  background-color: #fff;
}

.list-group-item:hover {
  box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
}

.details {
  min-width: 200px;
}

@media (max-width: 480px) {
  .list-group-item {
    flex-direction: column;
    align-items: flex-start !important;
  }
  .details {
    min-width: 100%;
  }
}
</style>
