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
          <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-gradient-primary text-white py-3">
              <h5 class="mb-0 fw-bold text-center">Menu Laporan</h5>
            </div>
            <div class="card-body p-0">
              <div class="nav flex-column nav-pills">
                <button
                  class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
                  :class="{ 'active': selectedTab === 'penipuan' }"
                  @click="selectedTab = 'penipuan'"
                >
                  <i class="fas fa-file-alt me-3"></i>
                  <span class="fw-medium">Penipuan</span>
                  <i class="fas fa-chevron-right ms-auto"></i>
                </button>
                <button
                  class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
                  :class="{ 'active': selectedTab === 'infrastruktur' }"
                  @click="selectedTab = 'infrastruktur'"
                >
                  <i class="fas fa-building me-3"></i>
                  <span class="fw-medium">Infrastruktur</span>
                  <i class="fas fa-chevron-right ms-auto"></i>
                </button>
                <button
                  class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
                  :class="{ 'active': selectedTab === 'verifikasi' }"
                  @click="selectedTab = 'verifikasi'"
                >
                  <i class="fas fa-check-circle me-3"></i>
                  <span class="fw-medium">Verifikasi</span>
                  <i class="fas fa-chevron-right ms-auto"></i>
                </button>
              </div>
            </div>
          </div>

          <!-- Quick Stats Card -->
          <div class="card border-0 shadow-sm rounded-3 mt-4">
            <div class="card-body">
              <h6 class="text-muted mb-3 fw-bold text-uppercase">Statistik</h6>
              <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-secondary">Total Laporan</span>
                <span class="badge bg-primary rounded-pill">{{ selectedTab === 'penipuan' ? props.reports.filter(r => r.category === 'Penipuan').length : selectedTab === 'infrastruktur' ? props.reports.filter(r => r.category === 'Infrastruktur').length : verifications.length }}</span>
              </div>
              <div class="progress" style="height: 6px;">
                <div class="progress-bar bg-success" role="progressbar" :style="`width: ${selectedTab === 'penipuan' ? 75 : selectedTab === 'infrastruktur' ? 65 : 60}%`"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="col-12 col-md-9">
          <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-bold text-primary">
                {{ selectedTab === 'penipuan' ? 'Daftar Laporan Penipuan' : selectedTab === 'infrastruktur' ? 'Daftar Laporan Infrastruktur' : 'Daftar Verifikasi' }}
              </h5>
              <div class="d-flex">
                <div class="input-group input-group-sm me-2" style="width: 200px;">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-search text-muted"></i>
                  </span>
                  <input type="text" class="form-control border-start-0 bg-light" placeholder="Cari...">
                </div>
                <button class="btn btn-sm btn-outline-primary">
                  <i class="fas fa-filter me-1"></i> Filter
                </button>
              </div>
            </div>
            <div class="card-body p-0">
              <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                  <thead class="bg-light">
                    <tr v-if="selectedTab === 'penipuan'">
                      <th scope="col" class="ps-4" width="5%">No</th>
                      <th scope="col" width="12%">Tanggal</th>
                      <th scope="col" width="12%">Kategori</th>
                      <th scope="col" width="15%">Sumber Penipuan</th>
                      <th scope="col">Deskripsi</th>
                      <th scope="col" width="10%">Status</th>
                      <th scope="col" width="8%" class="text-center">Aksi</th>
                    </tr>
                    <tr v-else-if="selectedTab === 'infrastruktur'">
                      <th scope="col" class="ps-4" width="5%">No</th>
                      <th scope="col" width="12%">Tanggal</th>
                      <th scope="col" width="12%">Kategori</th>
                      <th scope="col">Deskripsi</th>
                      <th scope="col" width="10%">Status</th>
                      <th scope="col" width="8%" class="text-center">Aksi</th>
                    </tr>
                    <tr v-else>
                      <th scope="col" class="ps-4" width="5%">No</th>
                      <th scope="col" width="12%">Tanggal</th>
                      <th scope="col">Data</th>
                      <th scope="col">Query</th>
                      <th scope="col" width="10%">Status</th>
                      <th scope="col" width="8%" class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody v-if="selectedTab === 'penipuan'">
                    <tr v-for="(item, index) in displayedData" :key="index" class="border-bottom">
                      <td class="ps-4 fw-medium">{{ index + 1 }}</td>
                      <td>{{ formatDate(item.created_at) }}</td>
                      <td>
                        <span class="badge bg-light text-dark border">{{ item.category }}</span>
                      </td>
                      <td>{{ item.source || 'SMS' }}</td>
                      <td class="text-truncate" style="max-width: 250px;">
                        {{ truncateText(item.description, 150) }}
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="status-indicator" :class="getStatusClass(item.status)"></div>
                          <span :class="getStatusTextClass(item.status)">{{ item.status }}</span>
                        </div>
                      </td>
                      <td class="text-center">
                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" @click="openDetailModal(item)">
                          <i class="fas fa-eye"></i>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="displayedData.length === 0">
                      <td colspan="7" class="text-center py-4 text-muted">
                        <i class="fas fa-folder-open mb-2 fa-2x"></i>
                        <p>Tidak ada laporan yang tersedia</p>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else-if="selectedTab === 'infrastruktur'">
                    <tr v-for="(item, index) in displayedData" :key="index" class="border-bottom">
                      <td class="ps-4 fw-medium">{{ index + 1 }}</td>
                      <td>{{ formatDate(item.created_at) }}</td>
                      <td>
                        <span class="badge bg-light text-dark border">{{ item.category }}</span>
                      </td>
                      <td class="text-truncate" style="max-width: 250px;">
                        {{ truncateText(item.description, 150) }}
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="status-indicator" :class="getStatusClass(item.status)"></div>
                          <span :class="getStatusTextClass(item.status)">{{ item.status }}</span>
                        </div>
                      </td>
                      <td class="text-center">
                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" @click="openDetailModal(item)">
                          <i class="fas fa-eye"></i>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="displayedData.length === 0">
                      <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-folder-open mb-2 fa-2x"></i>
                        <p>Tidak ada laporan yang tersedia</p>
                      </td>
                    </tr>
                  </tbody>
                  <tbody v-else>
                    <tr v-for="(item, index) in displayedData" :key="index" class="border-bottom">
                      <td class="ps-4 fw-medium">{{ index + 1 }}</td>
                      <td>{{ formatDate(item.tanggal) }}</td>
                      <td class="text-truncate" style="max-width: 250px;">
                        {{ truncateText(item.data, 150) }}
                      </td>
                      <td class="text-truncate" style="max-width: 250px;">
                        {{ truncateText(item.query, 150) }}
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div class="status-indicator" :class="getStatusClass(item.hasil)"></div>
                          <span :class="getStatusTextClass(item.hasil)">{{ item.hasil }}</span>
                        </div>
                      </td>
                      <td class="text-center">
                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" @click="openDetailModal(item)">
                          <i class="fas fa-eye"></i>
                        </button>
                      </td>
                    </tr>
                    <tr v-if="displayedData.length === 0">
                      <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-folder-open mb-2 fa-2x"></i>
                        <p>Tidak ada verifikasi yang tersedia</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
              <span class="text-muted small">Menampilkan {{ displayedData.length }} entri</span>
              <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item disabled">
                    <a class="page-link" href="#" aria-label="Previous">
                      <span aria-hidden="true">«</span>
                    </a>
                  </li>
                  <li class="page-item active"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                      <span aria-hidden="true">»</span>
                    </a>
                  </li>
                </ul>
              </nav>
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
      @close="closeModal"
    />
  </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DetailModal from './Partials/Modals.vue';

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

const displayedData = computed(() => {
  if (selectedTab.value === 'penipuan') {
    return props.reports.filter(r => r.service === 'Penipuan');
  } else if (selectedTab.value === 'infrastruktur') {
    return props.reports.filter(r => r.service === 'Infrastruktur');
  } else {
    return verifications.value;
  }
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