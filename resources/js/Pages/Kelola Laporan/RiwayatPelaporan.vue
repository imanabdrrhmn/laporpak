<template>
  <AppLayout>
    <Head title="Kelola Laporan" />

    <div class="container-fluid py-4">
      <div class="row mb-4">
        <div class="col-12">
          <h2 class="text-primary fw-bold mb-0">
            <i class="fas fa-chart-line me-2"></i> Laporan Saya
          </h2>
          <p class="text-muted">Manajemen dan verifikasi laporan sistem</p>
        </div>
      </div>

      <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-12 col-md-3">
          <SidebarMenu
            :selectedTab="selectedTab"
            @update:selectedTab="selectedTab = $event"
          />
          <QuickStats
            :selectedTab="selectedTab"
            :totalCount="totalCount"
          />
        </div>

        <!-- Main Content -->
        <div class="col-12 col-md-9">
          <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3">
              <h5 class="mb-0 fw-bold text-primary">
                {{ selectedTab === 'penipuan' ? 'Daftar Laporan Penipuan' : selectedTab === 'infrastruktur' ? 'Daftar Laporan Infrastruktur' : 'Daftar Verifikasi' }}
              </h5>
            </div>
            <div class="card-body p-0">
              <ReportsTable
                :selectedTab="selectedTab"
                :displayedData="displayedData"
                :formatDate="formatDate"
                :truncateText="truncateText"
                :getStatusClass="getStatusClass"
                :getStatusTextClass="getStatusTextClass"
                @openDetailModal="openDetailModal"
              />
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
              <span class="text-muted small">
                Menampilkan {{ ((currentPage - 1) * itemsPerPage) + 1 }} - 
                {{ Math.min(currentPage * itemsPerPage, totalCount) }} 
                dari {{ totalCount }} entri
              </span>
              <Pagination
                v-if="totalPages > 1"
                :totalPages="totalPages"
                :currentPage="currentPage"
                @goToPage="goToPage"
              />
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail Modal -->
    <DetailModal
      v-if="selectedItem"
      :item="selectedItem"
      :show="showModal"
      :formatDate="formatDate"
      :getStatusClass="getStatusClass"
      :getStatusTextClass="getStatusTextClass"
      @close="closeModal"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DetailModal from './Partials/Modals.vue';
import SidebarMenu from './Partials/SidebarMenu.vue';
import QuickStats from './Partials/QuickStats.vue';
import ReportsTable from './Partials/ReportsTable.vue';
import Pagination from './Partials/Pagination.vue';

const props = defineProps({
  reports: Array,
});

// Contoh data untuk verifikasi
const verifications = ref([
  {
    id: 1,
    tanggal: '2025-05-20',
    data: 'Transaksi mencurigakan melalui WhatsApp',
    query: 'Cek nomor +628123456789',
    hasil: 'rejected',
  },
  {
    id: 2,
    tanggal: '2025-05-21',
    data: 'Email phishing terdeteksi',
    query: 'Verifikasi email support@bank.com',
    hasil: 'pending',
  },
  {
    id: 3,
    tanggal: '2025-05-22',
    data: 'SMS penipuan minta transfer',
    query: 'Cek nomor +628987654321',
    hasil: 'approved',
  },
]);

const selectedTab = ref('penipuan');
const currentPage = ref(1);
const itemsPerPage = 10;

const totalCount = computed(() => {
  if (selectedTab.value === 'penipuan') {
    return props.reports?.filter(r => r.service === 'Penipuan').length || 0;
  } else if (selectedTab.value === 'infrastruktur') {
    return props.reports?.filter(r => r.service === 'Infrastruktur').length || 0;
  } else {
    return verifications.value.length;
  }
});

const totalPages = computed(() => {
  const totalItems = props.reports ? (
    selectedTab.value === 'penipuan' 
      ? props.reports.filter(r => r.service === 'Penipuan').length
      : selectedTab.value === 'infrastruktur'
        ? props.reports.filter(r => r.service === 'Infrastruktur').length
        : verifications.value.length
  ) : 0;
  
  return Math.ceil(totalItems / itemsPerPage);
});

const displayedData = computed(() => {
  if (!props.reports) return [];
  
  let data;
  if (selectedTab.value === 'penipuan') {
    data = props.reports.filter(r => r.service === 'Penipuan');
  } else if (selectedTab.value === 'infrastruktur') {
    data = props.reports.filter(r => r.service === 'Infrastruktur');
  } else {
    data = verifications.value;
  }
  
  const startIndex = (currentPage.value - 1) * itemsPerPage;
  const endIndex = startIndex + itemsPerPage;
  return data.slice(startIndex, endIndex);
});

const showModal = ref(false);
const selectedItem = ref(null);

const openDetailModal = (item) => {
  selectedItem.value = item;
  showModal.value = true;
};

const closeModal = () => {
  showModal.value = false;
  setTimeout(() => {
    selectedItem.value = null;
  }, 300);
};

const formatDate = (dateStr) => {
  const date = new Date(dateStr);
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric',
  });
};

const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

const getStatusClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'rejected':
      return 'bg-danger';
    case 'pending':
      return 'bg-warning';
    case 'approved':
      return 'bg-success';
    case 'published':
      return 'bg-primary';
    default:
      return 'bg-secondary';
  }
};

const getStatusTextClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'rejected':
      return 'text-danger';
    case 'pending':
      return 'text-warning';
    case 'approved':
      return 'text-success';
    case 'published':
      return 'text-primary';
    default:
      return 'text-secondary';
  }
};

const goToPage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    currentPage.value = page;
  }
};

// Reset page when tab changes
watch(selectedTab, () => {
  currentPage.value = 1;
});
</script>

<style scoped>
/* General Styles */
.bg-gradient-primary {
  background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
}

.icon-circle {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 30px;
  height: 30px;
  border-radius: 50%;
}

.status-indicator {
  width: 8px;
  height: 8px;
  border-radius: 50%;
}

/* Navigation Pills */
.nav-pills .nav-link {
  color: #495057;
  position: relative;
  transition: all 0.3s ease;
}

.nav-pills .nav-link:hover {
  background-color: #f8f9fa;
  color: #0d6efd;
}

.nav-pills .nav-link.active {
  background-color: rgba(13, 110, 253, 0.1);
  color: #0d6efd;
  font-weight: 600;
  border-left: 4px solid #0d6efd;
}

.nav-pills .nav-link.active .fa-chevron-right {
  opacity: 1;
}

.nav-pills .nav-link .fa-chevron-right {
  opacity: 0;
  transition: all 0.3s ease;
}

.nav-pills .nav-link:hover .fa-chevron-right {
  opacity: 0.5;
}

/* Table Styles */
.table {
  font-size: 0.9rem;
}

.table thead th {
  font-weight: 600;
  text-transform: uppercase;
  font-size: 0.75rem;
  letter-spacing: 0.5px;
  color: #6c757d;
  padding-top: 1rem;
  padding-bottom: 1rem;
}

.table tbody tr {
  transition: all 0.3s ease;
}

.table tbody tr:hover {
  background-color: rgba(13, 110, 253, 0.03);
}

/* Card Styles */
.card {
  transition: all 0.3s ease;
}

.card:hover {
  box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

/* Pagination */
.pagination .page-link {
  border-radius: 0.25rem;
  margin: 0 0.15rem;
}

.pagination .page-item.active .page-link {
  background-color: #0d6efd;
  border-color: #0d6efd;
}

/* Badge Styles */
.badge {
  padding: 0.5rem 0.75rem;
  font-weight: 500;
  letter-spacing: 0.3px;
}

/* Button Styles */
.btn-light {
  border: 1px solid #e9ecef;
}

.btn-light:hover {
  background-color: #f8f9fa;
}

/* Empty State */
.fa-folder-open {
  color: #dee2e6;
}
</style>