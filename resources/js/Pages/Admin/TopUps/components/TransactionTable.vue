<template>
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
            <td class="ps-4" data-label="User">
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
                  :src="getProofUrl(topUp.proof)"
                  alt="Bukti"
                  class="proof-thumb"
                  @click="emit('show-proof-modal', topUp.proof)"
                />
              </div>
              <span v-else class="text-muted">-</span>
            </td>
            <td class="text-end pe-4" data-label="Aksi">
              <button
                class="btn btn-sm btn-outline-secondary"
                @click="emit('open-action-modal', topUp)"
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
    <div v-if="topUps.last_page > 1" class="card-footer bg-white">
      <nav aria-label="Page navigation">
        <ul class="pagination pagination-sm justify-content-center flex-wrap mb-0">
          <li
            class="page-item"
            :class="{ disabled: !topUps.prev_page_url }"
            @click.prevent="emit('go-to-page', topUps.current_page - 1)"
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
            @click.prevent="emit('go-to-page', page)"
          >
            <a class="page-link" href="#">{{ page }}</a>
          </li>
          <li
            class="page-item"
            :class="{ disabled: !topUps.next_page_url }"
            @click.prevent="emit('go-to-page', topUps.current_page + 1)"
          >
            <a class="page-link" href="#">
              <i class="fas fa-chevron-right"></i>
            </a>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>

<script setup>
import { computed, defineProps, defineEmits } from "vue";

const props = defineProps({
  topUps: Object,
  formatCurrency: Function,
  formatDate: Function,
  capitalize: Function,
  statusBadgeClass: Function,
  getProofUrl: Function,
});

const emit = defineEmits(["show-proof-modal", "open-action-modal", "go-to-page"]);

const totalPages = computed(() => {
  const pages = [];
  for (let i = 1; i <= props.topUps.last_page; i++) {
    pages.push(i);
  }
  return pages;
});
</script>

<style scoped>
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
  color: #6c757d;
}

@media (max-width: 768px) {
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
  }

  .table tr {
    display: flex;
    flex-direction: column;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 1rem;
    border: 1px solid #e9ecef;
    transition: transform 0.2s ease-in-out;
  }

  .table tr:hover {
    transform: translateY(-2px);
  }

  .table td {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f1f1;
  }

  .table td:last-child {
    border-bottom: none;
  }

  .table td:before {
    content: attr(data-label);
    font-weight: 600;
    color: #495057;
    margin-right: 0.5rem;
    min-width: 100px;
  }

  .table td:nth-child(1) {
    flex-direction: column;
    align-items: flex-start;
  }

  .table td:nth-child(1) .d-flex {
    width: 100%;
  }

  .table td:nth-child(1):before {
    content: none;
  }

  .table td.text-end {
    justify-content: flex-end;
  }

  .table td.text-end:before {
    content: none;
  }

  .user-avatar {
    width: 32px;
    height: 32px;
    font-size: 0.8rem;
  }

  .proof-container {
    width: 80px;
    height: 50px;
  }

  .empty-state {
    padding: 2rem;
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  }
}
</style>