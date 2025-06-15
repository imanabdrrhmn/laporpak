<template>
  <AppLayout>
    <Head title="Riwayat Top Up" />
    <div class="top-up-header-bar">
      <div class="container d-flex justify-content-center align-items-center py-3">
        <h2 class="text-white fw-bold mb-0 h3">Riwayat Top Up</h2>
      </div>
    </div>
    <div class="container py-4">
      <div class="topup-history-container mx-auto">
        <!-- Remove the h1 here since we now have the header bar above -->

        <div class="d-flex flex-column flex-md-row justify-content-between gap-5 mb-4">
          <input
            type="number"
            v-model="searchTerm"
            class="form-control w-auto"
            placeholder="Cari jumlah deposit..."
            @input="goToPage(1)"
            @keypress="onlyNumbers"
            min="0"
            step="1"
          />
          <select v-model="selectedStatus" class="form-select w-auto">
            <option value="">Semua Status</option>
            <option value="verified">Verified</option>
            <option value="pending">Pending</option>
            <option value="failed">Rejected</option>
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

    <DetailModal 
      ref="detailModal"
      :top-up="selectedTopUp"
      @close="closeDetailModal"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import DetailModal from './Components/DetailModal.vue';
import { Head } from '@inertiajs/vue3';

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
const detailModal = ref(null);

const proofImageUrl = computed(() => {
  if (!selectedTopUp.value || !selectedTopUp.value.proof) return '';
  return `/storage/${selectedTopUp.value.proof}`;
});

const filteredTopUps = computed(() => {
  return props.topUps.filter((t) => {
    const statusMatch =
      !selectedStatus.value || t.status.toLowerCase() === selectedStatus.value.toLowerCase();

    const searchValue = searchTerm.value.toString().trim();
    if (!searchValue) return statusMatch;

    // Since input is now numeric, directly compare with amount
    const amountMatch = t.amount.toString().includes(searchValue);

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
    case 'success':
      return 'status-badge approved';
    case 'pending':
      return 'status-badge pending';
    case 'failed':
    case 'gagal':
      return 'status-badge rejected';
    default:
      return 'status-badge rejected';
  }
};

const capitalize = (str) => {
  if (!str) return '';
  return str.charAt(0).toUpperCase() + str.slice(1).toLowerCase();
}

// Function to ensure only numbers can be typed
const onlyNumbers = (event) => {
  const charCode = event.which ? event.which : event.keyCode;
  // Allow: backspace, delete, tab, escape, enter, period
  if ([46, 8, 9, 27, 13, 110, 190].indexOf(charCode) !== -1 ||
      // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
      (charCode === 65 && event.ctrlKey === true) ||
      (charCode === 67 && event.ctrlKey === true) ||
      (charCode === 86 && event.ctrlKey === true) ||
      (charCode === 88 && event.ctrlKey === true)) {
    return;
  }
  // Ensure that it is a number and stop the keypress
  if (charCode < 48 || charCode > 57) {
    event.preventDefault();
  }
};

const goToPage = (page) => {
  if (page < 1 || page > pageCount.value) return;
  currentPage.value = page;
};

const openDetailModal = (topUp) => {
  selectedTopUp.value = topUp;
  detailModal.value?.show();
};

const closeDetailModal = () => {
  selectedTopUp.value = null;
};

onMounted(() => {
});
</script>

<style scoped>
.top-up-header-bar {
  background: linear-gradient(135deg, #0062cc, #0078e7, #003f8a);
  box-shadow: 0 2px 10px rgba(0,0,0,0.1);
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
  color: #0d6efd 
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

.status-badge.pending {
  background-color: #fff8e1;
  color: #ffa000;
}

.status-badge.approved {
  background-color: #e8f5e9;
  color: #2e7d32;
}

.status-badge.rejected {
  background-color: #ffebee;
  color: #c62828;
}

/* Hide number input spinners/arrows */
input[type="number"]::-webkit-outer-spin-button,
input[type="number"]::-webkit-inner-spin-button {
  -webkit-appearance: none;
  appearance: none;
  margin: 0;
}

/* Firefox */
input[type="number"] {
  -moz-appearance: textfield;
  appearance: textfield;
}

@media (max-width: 767.98px) {
  .top-up-header-bar h2 {
    font-size: 1.5rem;
  }
}
</style>