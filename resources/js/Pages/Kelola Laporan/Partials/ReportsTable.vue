<template>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr v-if="selectedTab === 'penipuan'">
          <th scope="col" class="ps-4 table-header" width="5%">No</th>
          <th scope="col" class="table-header" width="12%">Tanggal</th>
          <th scope="col" class="table-header" width="12%">Kategori</th>
          <th scope="col" class="table-header" width="15%">Sumber Penipuan</th>
          <th scope="col" class="table-header">Deskripsi</th>
          <th scope="col" class="table-header" width="10%">Status</th>
          <th scope="col" class="table-header text-center" width="10%">Detail</th>
        </tr>
        <tr v-else-if="selectedTab === 'infrastruktur'">
          <th scope="col" class="ps-4 table-header" width="5%">No</th>
          <th scope="col" class="table-header" width="12%">Tanggal</th>
          <th scope="col" class="table-header" width="12%">Kategori</th>
          <th scope="col" class="table-header">Deskripsi</th>
          <th scope="col" class="table-header" width="10%">Status</th>
          <th scope="col" class="table-header text-center" width="10%">Detail</th>
        </tr>
        <tr v-else>
          <th scope="col" class="ps-4 table-header" width="5%">No</th>
          <th scope="col" class="table-header" width="12%">Tanggal</th>
          <th scope="col" class="table-header">Data</th>
          <th scope="col" class="table-header">Query</th>
          <th scope="col" class="table-header" width="10%">Status</th>
          <th scope="col" class="table-header text-center" width="10%">Detail</th>
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
            <span class="status-badge" :class="getStatusClass(item.status) || 'default'">
              {{ item.status || 'Tidak Diketahui' }}
            </span>
          </td>
          <td class="text-center">
            <!-- Detail Button Only -->
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
              title="Lihat Detail"
            >
              <i class="fas fa-eye me-1"></i>
              <span class="btn-text">Detail</span>
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
            <span class="status-badge" :class="getStatusClass(item.status) || 'default'">
              {{ item.status || 'Tidak Diketahui' }}
            </span>
          </td>
          <td class="text-center">
            <!-- Detail Button Only -->
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
              title="Lihat Detail"
            >
              <i class="fas fa-eye me-1"></i>
              <span class="btn-text">Detail</span>
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
            <span class="query-text">{{ truncateText(item.query, 150) }}</span>
          </td>
          <td>
            <span class="status-badge" :class="getStatusClass(item.hasil) || 'default'">
              {{ item.hasil || 'Tidak Diketahui' }}
            </span>
          </td>
          <td class="text-center">
            <!-- Detail Button Only -->
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
              title="Lihat Detail"
            >
              <i class="fas fa-eye me-1"></i>
              <span class="btn-text">Detail</span>
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
</template>

<script setup>
defineProps({
  selectedTab: String,
  displayedData: Array,
  formatDate: Function,
  truncateText: Function,
  getStatusClass: Function,
  getStatusTextClass: Function,
});

// Define only the detail modal event
defineEmits([
  'openDetailModal'
]);
</script>

<style scoped>
.table-header {
  background-color: #0d6efd !important;
  color: rgb(255, 255, 255);
  font-weight: 500;
}

table {
  border-collapse: separate;
  border-spacing: 0 15px;
}

tbody tr {
  background: white;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  border-radius: 8px;
}

tbody td {
  padding: 15px !important;
}

tbody td:first-child {
  border-radius: 8px 0 0 8px;
}

tbody td:last-child {
  border-radius: 0 8px 8px 0;
}

.status-badge {
  padding: 3px 8px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  text-transform: capitalize;
  background-color: #f0f0f0;
  color: #333;
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

.status-badge.published {
  background-color: #e3f2fd;
  color: #1565c0;
}

.status-badge.solved {
  background: #d4edda;
  color: #155724;
}

.status-badge.open {
  background: #fff3cd;
  color: #856404;
}

.status-badge.default {
  background-color: #f0f0f0;
  color: #333;
}

/* Detail button styling */
.detail-btn {
  background: #0d6efd;
  border: none;
  border-radius: 6px;
  padding: 6px 12px;
  font-weight: 500;
  letter-spacing: 0.5px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
  min-width: 80px;
}

.detail-btn:hover {
  background: linear-gradient(45deg, #0056b3, #004085);
  transform: translateY(-2px);
  box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.detail-btn:active {
  transform: translateY(0);
  box-shadow: 0 2px 4px rgba(0, 123, 255, 0.2);
}

.detail-btn .btn-text {
  font-size: 0.875rem;
  font-weight: 500;
}

.detail-btn i {
  font-size: 0.9rem;
}

/* Dropdown styling */
.dropdown-menu {
  border: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  border-radius: 8px;
  overflow: hidden;
}

.dropdown-item {
  padding: 8px 16px;
  transition: background-color 0.2s ease;
}

.dropdown-item:hover {
  background-color: #f8f9fa;
}

.dropdown-item.text-danger:hover {
  background-color: #ffe6e6;
  color: #dc3545 !important;
}

/* Query text styling */
.query-text {
  font-family: 'Courier New', monospace;
  font-size: 0.9em;
  background: #f8f9fa;
  padding: 2px 6px;
  border-radius: 4px;
  border: 1px solid #e9ecef;
}

/* Responsive adjustments */
@media (max-width: 768px) {
  .detail-btn {
    padding: 4px 8px;
    min-width: 60px;
  }
  
  .detail-btn .btn-text {
    font-size: 0.8rem;
  }
  
  .detail-btn i {
    font-size: 0.85rem;
  }
}
</style>