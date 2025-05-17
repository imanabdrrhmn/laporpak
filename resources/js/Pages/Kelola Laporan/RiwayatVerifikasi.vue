<template>
  <AppLayout>
    <Head title="Verifikasi Data" />

    <div class="container-fluid py-4">
      <div class="row mb-4">
        <div class="col-12">
          <h2 class="text-primary fw-bold mb-0">
            <i class="fas fa-check-circle me-2"></i> Verifikasi Data
          </h2>
          <p class="text-muted">Manajemen verifikasi dan validasi data sistem</p>
        </div>
      </div>

      <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-12 col-md-3">
          <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
            <div class="card-header bg-gradient-primary text-white py-3">
              <h5 class="mb-0 fw-bold text-center">Menu Verifikasi</h5>
            </div>
            <div class="card-body p-0">
              <div class="nav flex-column nav-pills">
                <button
                  class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
                  :class="{ 'active': selectedTab === 'all' }"
                  @click="selectedTab = 'all'"
                >
                  <i class="fas fa-list-alt me-3"></i>
                  <span class="fw-medium">Semua Verifikasi</span>
                  <i class="fas fa-chevron-right ms-auto"></i>
                </button>
                <button
                  class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
                  :class="{ 'active': selectedTab === 'pending' }"
                  @click="selectedTab = 'pending'"
                >
                  <i class="fas fa-clock me-3"></i>
                  <span class="fw-medium">Menunggu</span>
                  <i class="fas fa-chevron-right ms-auto"></i>
                </button>
                <button
                  class="nav-link border-0 rounded-0 py-3 d-flex align-items-center"
                  :class="{ 'active': selectedTab === 'completed' }"
                  @click="selectedTab = 'completed'"
                >
                  <i class="fas fa-check-double me-3"></i>
                  <span class="fw-medium">Selesai</span>
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
                <span class="text-secondary">Total Verifikasi</span>
                <span class="badge bg-primary rounded-pill">{{ totalVerifications }}</span>
              </div>
              <div class="progress mb-3" style="height: 6px;">
                <div class="progress-bar bg-success" role="progressbar" :style="`width: ${completionRate}%`"></div>
              </div>
              <div class="d-flex justify-content-between">
                <div class="text-center">
                  <h6 class="mb-0">{{ pendingCount }}</h6>
                  <small class="text-muted">Menunggu</small>
                </div>
                <div class="text-center">
                  <h6 class="mb-0">{{ completeCount }}</h6>
                  <small class="text-muted">Selesai</small>
                </div>
                <div class="text-center">
                  <h6 class="mb-0">{{ rejectedCount }}</h6>
                  <small class="text-muted">Ditolak</small>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="col-12 col-md-9">
          <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
              <h5 class="mb-0 fw-bold text-primary">
                {{ tabTitle }}
              </h5>
              <div class="d-flex">
                <div class="input-group input-group-sm me-2" style="width: 200px;">
                  <span class="input-group-text bg-light border-end-0">
                    <i class="fas fa-search text-muted"></i>
                  </span>
                  <input 
                    type="text" 
                    class="form-control border-start-0 bg-light" 
                    placeholder="Cari..." 
                    v-model="searchQuery"
                  >
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
                    <tr>
                      <th scope="col" class="ps-4" width="5%">No</th>
                      <th scope="col" width="15%">Tanggal</th>
                      <th scope="col" width="20%">Data</th>
                      <th scope="col">Query</th>
                      <th scope="col" width="15%">Status</th>
                      <th scope="col" width="10%" class="text-center">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in filteredVerifications" :key="index" class="border-bottom">
                      <td class="ps-4 fw-medium">{{ index + 1 }}</td>
                      <td>{{ item.tanggal }}</td>
                      <td>{{ item.data }}</td>
                      <td class="text-truncate" style="max-width: 250px;">
                        {{ truncateText(item.query, 150) }}
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <div :class="`status-indicator ${getStatusClass(item.status)} me-2`"></div>
                          <span :class="`${getStatusTextClass(item.status)} fw-medium`">{{ item.status }}</span>
                        </div>
                      </td>
                      <td class="text-center">
                        <div class="dropdown">
                          <button class="btn btn-light btn-sm rounded-circle shadow-sm" data-bs-toggle="dropdown">
                            <i class="fas fa-ellipsis-v"></i>
                          </button>
                          <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#" @click.prevent="viewDetail(item)"><i class="fas fa-eye me-2"></i> Lihat</a></li>
                            <li><a class="dropdown-item" href="#" @click.prevent="processItem(item)"><i class="fas fa-check me-2"></i> Proses</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="#" @click.prevent="deleteItem(item)"><i class="fas fa-trash me-2"></i> Hapus</a></li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                    <tr v-if="filteredVerifications.length === 0">
                      <td colspan="6" class="text-center py-4 text-muted">
                        <i class="fas fa-clipboard-check mb-2 fa-2x"></i>
                        <p>Tidak ada data verifikasi yang tersedia</p>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="card-footer bg-white d-flex justify-content-between align-items-center py-3">
              <span class="text-muted small">Menampilkan {{ filteredVerifications.length }} dari {{ totalVerifications }} entri</span>
              <nav aria-label="Page navigation">
                <ul class="pagination pagination-sm mb-0">
                  <li class="page-item disabled">
                    <a class="page-link" href="#" aria-label="Previous">
                      <span aria-hidden="true">&laquo;</span>
                    </a>
                  </li>
                  <li class="page-item active"><a class="page-link" href="#">1</a></li>
                  <li class="page-item"><a class="page-link" href="#">2</a></li>
                  <li class="page-item"><a class="page-link" href="#">3</a></li>
                  <li class="page-item">
                    <a class="page-link" href="#" aria-label="Next">
                      <span aria-hidden="true">&raquo;</span>
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
    <div v-if="selectedItem" class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detailModalLabel">Detail Verifikasi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label text-muted">Data</label>
              <p class="fw-medium">{{ selectedItem.data }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted">Query</label>
              <p>{{ selectedItem.query }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted">Tanggal</label>
              <p>{{ selectedItem.tanggal }}</p>
            </div>
            <div class="mb-3">
              <label class="form-label text-muted">Status</label>
              <p>
                <span :class="`badge ${getStatusBadgeClass(selectedItem.status)}`">{{ selectedItem.status }}</span>
              </p>
            </div>
            <div v-if="selectedItem.catatan" class="mb-3">
              <label class="form-label text-muted">Catatan</label>
              <p>{{ selectedItem.catatan }}</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            <button 
              v-if="selectedItem.status === 'Menunggu'" 
              type="button" 
              class="btn btn-primary"
              @click="processSelectedItem"
            >
              Proses Verifikasi
            </button>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';

// Sample data
const verifications = ref([
  {
    id: 1,
    tanggal: '15 Mei 2025',
    data: 'KTP: 3201XXXXXXXXXXXX',
    query: 'Validasi data KTP pada database kependudukan nasional',
    status: 'Selesai',
    catatan: 'Data sudah divalidasi dengan database Dukcapil'
  },
  {
    id: 2,
    tanggal: '16 Mei 2025',
    data: 'NPWP: 09.XXX.XXX.X-XXX.XXX',
    query: 'Verifikasi NPWP dengan data pajak',
    status: 'Menunggu',
    catatan: ''
  },
  {
    id: 3,
    tanggal: '16 Mei 2025',
    data: 'Nomor Rekening: 1234567890',
    query: 'Cross-check nomor rekening dengan database bank',
    status: 'Ditolak',
    catatan: 'Nomor rekening tidak ditemukan dalam database'
  },
  {
    id: 4,
    tanggal: '17 Mei 2025',
    data: 'SIM: 1234XXXXX',
    query: 'Validasi SIM dengan database Kepolisian',
    status: 'Selesai',
    catatan: 'Data terverifikasi sesuai database Korlantas'
  },
  {
    id: 5,
    tanggal: '17 Mei 2025',
    data: 'Email: user@example.com',
    query: 'Verifikasi email address',
    status: 'Menunggu',
    catatan: ''
  }
]);

const selectedTab = ref('all');
const searchQuery = ref('');
const selectedItem = ref(null);

// Computed values
const pendingCount = computed(() => verifications.value.filter(item => item.status === 'Menunggu').length);
const completeCount = computed(() => verifications.value.filter(item => item.status === 'Selesai').length);
const rejectedCount = computed(() => verifications.value.filter(item => item.status === 'Ditolak').length);
const totalVerifications = computed(() => verifications.value.length);
const completionRate = computed(() => Math.round((completeCount.value / totalVerifications.value) * 100));

const filteredVerifications = computed(() => {
  let filtered = [...verifications.value];
  
  // Filter by tab
  if (selectedTab.value === 'pending') {
    filtered = filtered.filter(item => item.status === 'Menunggu');
  } else if (selectedTab.value === 'completed') {
    filtered = filtered.filter(item => item.status === 'Selesai');
  }
  
  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(item => 
      item.data.toLowerCase().includes(query) || 
      item.query.toLowerCase().includes(query)
    );
  }
  
  return filtered;
});

const tabTitle = computed(() => {
  switch(selectedTab.value) {
    case 'pending': return 'Verifikasi Menunggu';
    case 'completed': return 'Verifikasi Selesai';
    default: return 'Semua Verifikasi';
  }
});

// Methods
const truncateText = (text, maxLength) => {
  if (!text) return '';
  return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
};

const getStatusClass = (status) => {
  switch(status) {
    case 'Selesai': return 'bg-success';
    case 'Menunggu': return 'bg-warning';
    case 'Ditolak': return 'bg-danger';
    default: return 'bg-secondary';
  }
};

const getStatusTextClass = (status) => {
  switch(status) {
    case 'Selesai': return 'text-success';
    case 'Menunggu': return 'text-warning';
    case 'Ditolak': return 'text-danger';
    default: return 'text-secondary';
  }
};

const getStatusBadgeClass = (status) => {
  switch(status) {
    case 'Selesai': return 'bg-success';
    case 'Menunggu': return 'bg-warning';
    case 'Ditolak': return 'bg-danger';
    default: return 'bg-secondary';
  }
};

const viewDetail = (item) => {
  selectedItem.value = item;
  // Use Bootstrap's modal API
  const modal = new bootstrap.Modal(document.getElementById('detailModal'));
  modal.show();
};

const processItem = (item) => {
  // Logic to process verification item
  if (item.status === 'Menunggu') {
    item.status = 'Selesai';
    item.catatan = 'Data terverifikasi oleh sistem';
  }
};

const processSelectedItem = () => {
  if (selectedItem.value && selectedItem.value.status === 'Menunggu') {
    selectedItem.value.status = 'Selesai';
    selectedItem.value.catatan = 'Data terverifikasi oleh sistem';
    
    // Close modal
    const modalElement = document.getElementById('detailModal');
    const modal = bootstrap.Modal.getInstance(modalElement);
    if (modal) modal.hide();
  }
};

const deleteItem = (item) => {
  if (confirm('Apakah Anda yakin ingin menghapus data ini?')) {
    const index = verifications.value.findIndex(v => v.id === item.id);
    if (index !== -1) {
      verifications.value.splice(index, 1);
    }
  }
};

onMounted(() => {
  // Bootstrap's JavaScript is required for the dropdowns and modals
  // Assumes Bootstrap JS is loaded in the application
});
</script>

<style scoped>
/* General Styles */
.bg-gradient-primary {
  background: linear-gradient(135deg, #0d6efd 0%, #0b5ed7 100%);
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

/* Modal Styles */
.modal-header {
  background-color: #f8f9fa;
}

.modal-footer {
  background-color: #f8f9fa;
}

/* Empty State */
.fa-clipboard-check {
  color: #dee2e6;
}
</style>