<template>
  <div class="table-container">
    <div class="table-responsive">
      <table class="table table-hover m-0">
        <thead class="table-light">
          <tr>
            <th class="text-center" style="width: 50px">No</th>
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
          <tr v-for="(topUp, index) in topUps" 
              :key="topUp.id" 
              :class="['align-middle', `status-${topUp.status}`]">
            <td class="text-center" data-label="No">
              {{ ((paginationData.current_page - 1) * paginationData.per_page) + index + 1 }}
            </td>
            <td class="ps-4 user-cell" data-label="">
              <div class="d-flex align-items-center">
                <div class="user-avatar me-2">
                  <img
                    v-if="topUp.user"
                    :src="topUp.user.avatar_url"
                    :alt="topUp.user.name"
                    class="user-avatar-image rounded-circle"
                  />
                  <div v-else class="avatar-placeholder">
                    <i class="fas fa-question"></i>
                  </div>
                </div>
                <div>
                  <div class="fw-semibold">{{ topUp.user?.name || 'User Dihapus' }}</div>
                  <small class="text-muted">{{ topUp.user?.email || '-' }}</small>
                </div>
              </div>
            </td>
            <td data-label="Jumlah"><span class="fw-semibold">Rp {{ formatCurrency(topUp.amount) }}</span></td>
            <td data-label="Metode"><span class="payment-method">{{ topUp.payment_method || '-' }}</span></td>
            <td data-label="Status"><span :class="statusBadgeClass(topUp.status)" class="status-badge">{{ capitalize(topUp.status) }}</span></td>
            <td data-label="Tanggal">
              <div class="d-flex flex-column">
                <span>{{ formatDate(topUp.created_at).date }}</span>
                <small class="text-muted">{{ formatDate(topUp.created_at).time }}</small>
              </div>
            </td>
            <td data-label="Bukti">
              <div v-if="topUp.proof" class="proof-container">
                <img
                  :src="topUp.proof"
                  alt="Bukti"
                  class="proof-thumb"
                  @click="showProof(topUp.proof)"
                />
              </div>
              <span v-else class="text-muted">-</span>
            </td>
            <td class="text-end pe-4" data-label="">
              <button
                class="btn btn-sm btn-outline-secondary action-button"
                @click="emit('open-action-modal', topUp)"
                aria-label="Buka menu aksi untuk transaksi ini"
                type="button"
              >
                <i class="fas fa-ellipsis-v action-icon" aria-hidden="true"></i>
              </button>
            </td>
          </tr>
          <tr v-if="!topUps || topUps.length === 0">
            <td colspan="8" class="text-center py-4">
              <div class="empty-state">
                <i class="fas fa-receipt text-muted mb-2"></i>
                <p>Tidak ada data transaksi yang ditemukan</p>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
  <div class="pagination-container p-4">
    <div class="d-flex justify-content-between align-items-center">
      <div class="pagination-info">
        Menampilkan {{ paginationInfo.from }}-{{ paginationInfo.to }} dari {{ paginationInfo.total }} data
      </div>
      <nav aria-label="Page navigation">
        <ul class="pagination mb-0">
          <li class="page-item" :class="{ disabled: paginationData.current_page === 1 }">
            <button class="page-link" @click="emit('go-to-page', paginationData.current_page - 1)" :disabled="paginationData.current_page === 1">
              <i class="bi bi-chevron-left"></i>
            </button>
          </li>
          <li v-for="page in displayedPages" :key="page" class="page-item" :class="{ active: page === paginationData.current_page }">
            <button class="page-link" @click="emit('go-to-page', page)">{{ page }}</button>
          </li>
          <li class="page-item" :class="{ disabled: paginationData.current_page === paginationData.last_page }">
            <button class="page-link" @click="emit('go-to-page', paginationData.current_page + 1)" :disabled="paginationData.current_page === paginationData.last_page">
              <i class="bi bi-chevron-right"></i>
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
  
  <!-- Add ProofModal component -->
  <ProofModal 
    :is-visible="isProofModalVisible"
    :proof-url="selectedProofUrl"
    @close="closeProofModal"
  />
</template>

<script setup>
import { computed, ref } from "vue";
import ProofModal from './ProofModal.vue';

const props = defineProps({
  topUps: {
    type: Array,
    required: true,
    default: () => []
  },
  paginationData: {
    type: Object,
    required: true,
    default: () => ({
        current_page: 1,
        last_page: 1,
        links: {},
        per_page: 10,
        total: 0,
        from: null,
        to: null
    })
  },
  formatCurrency: Function,
  formatDate: Function,
  capitalize: Function,
  statusBadgeClass: Function,
});

const emit = defineEmits(["show-proof-modal", "open-action-modal", "go-to-page"]);

// Add these refs for modal control
const isProofModalVisible = ref(false);
const selectedProofUrl = ref('');

// Add these methods for modal control
const showProof = (proofUrl) => {
  selectedProofUrl.value = proofUrl;
  isProofModalVisible.value = true;
};

const closeProofModal = () => {
  isProofModalVisible.value = false;
  selectedProofUrl.value = '';
};

const totalPages = computed(() => {
  if (!props.paginationData?.last_page) return [];
  const pages = [];
  for (let i = 1; i <= props.paginationData.last_page; i++) {
    pages.push(i);
  }
  return pages;
});

// Perbaikan perhitungan pagination info
const paginationInfo = computed(() => {
  const currentPage = props.paginationData.current_page || 1;
  const perPage = props.paginationData.per_page || 10;
  const total = props.paginationData.total || 0;
  const currentDataLength = props.topUps?.length || 0;
  
  // Jika ada data from dan to dari backend, gunakan itu
  if (props.paginationData.from && props.paginationData.to) {
    return {
      from: props.paginationData.from,
      to: props.paginationData.to,
      total: total
    };
  }
  
  // Jika tidak ada, hitung manual berdasarkan current page dan per page
  const from = total === 0 ? 0 : ((currentPage - 1) * perPage) + 1;
  const to = Math.min(from + currentDataLength - 1, total);
  
  return {
    from: from,
    to: to,
    total: total
  };
});

const displayedPages = computed(() => {
  if (!props.paginationData?.last_page) return [];
  
  const maxVisiblePages = 5;
  const currentPage = props.paginationData.current_page;
  const lastPage = props.paginationData.last_page;
  const halfVisible = Math.floor(maxVisiblePages / 2);
  
  let startPage = Math.max(currentPage - halfVisible, 1);
  let endPage = startPage + maxVisiblePages - 1;

  if (endPage > lastPage) {
    endPage = lastPage;
    startPage = Math.max(endPage - maxVisiblePages + 1, 1);
  }

  return Array.from({ length: endPage - startPage + 1 }, (_, i) => startPage + i);
});
</script>

<style scoped>
.table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.pagination-container {
    background: #fff;
    border-top: 1px solid #e5e7eb;
    padding: 1rem;
}

.pagination-info {
    color: #6b7280;
    font-size: 0.875rem;
}

.user-avatar-image {
    width: 40px;
    height: 40px;
    object-fit: cover;
}

.avatar-placeholder {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #f8f9fa;
    border-radius: 50%;
    color: #6c757d;
}

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
    color: #1d70ba; 
}

.pagination { 
    margin-top: 0; 
    gap: 0.25rem;
}

.page-link { 
    user-select: none;
    border: none;
    padding: 0.5rem 0.75rem;
    color: #374151;
    background: transparent;
    border-radius: 0.5rem;
    font-size: 0.875rem;
    line-height: 1.25rem;
    transition: all 0.2s ease;
}

.page-link:hover:not(.disabled) {
    background: #f3f4f6;
    color: #1f2937;
}

.page-item.active .page-link {
    background: #2563eb;
    color: white;
}

.page-item.disabled .page-link {
    color: #9ca3af;
    pointer-events: none;
}

.action-button {
    display: inline-flex;
    align-items: center;
    justify-content: center;
}

.action-icon {
    font-size: 0.875rem;
}

/* Mobile and Tablet Responsive Styles */
@media (max-width: 1024px) {
  .table-responsive {
    overflow-x: hidden;
  }

  .table {
    display: block;
  }

  .table thead {
    display: none;
  }

  .table tbody {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    padding: 1rem;
  }

  .table tr {
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    padding: 1.25rem;
    border: 1px solid #e9ecef;
    border-left: 4px solid #6c757d;
    transition: all 0.3s ease;
    position: relative;
    margin-bottom: 0;
  }

  /* Status-based border colors */
  .table tr.status-verified {
    border-left-color: #28a745;
  }

  .table tr.status-pending {
    border-left-color: #ffc107;
  }

  .table tr.status-rejected {
    border-left-color: #dc3545;
  }

  .table tr.status-cancelled {
    border-left-color: #6c757d;
  }

  .table tr:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
  }

  .table td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #f8f9fa;
    min-height: 48px;
  }


  .table td:last-child {
    border-bottom: none;
    justify-content: space-between;
    align-items: center;
    flex-direction: row;
  }

  .table td:before {
    content: attr(data-label);
    font-weight: 600;
    color: #495057;
    margin-right: 1rem;
    min-width: 120px;
    font-size: 0.875rem;
  }

  /* Special handling for User column - HIDE LABEL */
  .table td.user-cell {
    flex-direction: column;
    align-items: center;
    padding-bottom: 1rem;
    border-bottom: 2px solid #e9ecef;
    text-align: center;
  }

  .table td.user-cell .d-flex {
    width: 100%;
    margin-top: 0;
    flex-direction: column;
    align-items: center;
    text-align: center;
  }

  .table td.user-cell .user-avatar {
    margin-bottom: 0.75rem;
    margin-right: 0;
  }

  .table td.user-cell:before {
    display: none; /* Hide the "User" label */
  }

  /* Action column - HIDE LABEL */
  .table td:last-child:before {
    display: none;
  }
  
  .table td:last-child {
    justify-content: center;
    border-top: 1px solid #f8f9fa;
    margin-top: 0.75rem;
    padding-top: 1rem;
    border-bottom: none;
  }

  .table td:last-child .btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    background-color: #007bff;
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.875rem;
    transition: all 0.2s ease;
    box-shadow: 0 2px 4px rgba(0, 123, 255, 0.3);
    min-width: 120px;
  }

  .table td:last-child .btn:after {
    content: "Konfirmasi";
  }
  
  .action-icon {
    display: none;
  }

  /* Responsive adjustments */
  .user-avatar {
    width: 36px;
    height: 36px;
    font-size: 0.8rem;
  }

  .user-avatar-image {
    width: 36px;
    height: 36px;
  }

  .avatar-placeholder {
    width: 36px;
    height: 36px;
  }

  .proof-container {
    width: 70px;
    height: 45px;
  }

  .empty-state {
    padding: 2.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    margin: 1rem;
  }

  .empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
  }

  /* Pagination adjustments */
  .pagination {
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.25rem;
  }

  .page-item {
    margin: 0.125rem;
  }

  .page-link {
    padding: 0.5rem 0.75rem;
    font-size: 0.875rem;
  }

  /* Hide number column in mobile/tablet view */
  .table td[data-label="No"] {
    display: none;
  }
}

/* Tablet specific adjustments */
@media (min-width: 769px) and (max-width: 1024px) {
  .table tbody {
    padding: 1.5rem;
    gap: 1.25rem;
  }

  .table tr {
    padding: 1.5rem;
  }

  .table td {
    padding: 0.875rem 0;
  }

  .table td:before {
    min-width: 140px;
    font-size: 0.9rem;
  }

  .proof-container {
    width: 80px;
    height: 50px;
  }
}

/* Small mobile devices */
@media (max-width: 480px) {
  .table tbody {
    padding: 0.75rem;
    gap: 0.875rem;
  }

  .table tr {
    padding: 1rem;
    border-radius: 10px;
  }

  .table td {
    padding: 0.625rem 0;
    min-height: 44px;
  }

  .table td:before {
    min-width: 100px;
    font-size: 0.8rem;
  }

  .table td:last-child {
    padding: 0.75rem 0;
    min-height: 48px;
  }

  .user-avatar {
    width: 32px;
    height: 32px;
  }

  .user-avatar-image {
    width: 32px;
    height: 32px;
  }

  .avatar-placeholder {
    width: 32px;
    height: 32px;
  }

  .proof-container {
    width: 60px;
    height: 40px;
  }

  .status-badge {
    font-size: 0.7rem;
    padding: 0.3em 0.6em;
  }

  .table td:last-child .btn {
    padding: 0.625rem 1.25rem;
    font-size: 0.8rem;
    min-width: 100px;
  }
}
</style>