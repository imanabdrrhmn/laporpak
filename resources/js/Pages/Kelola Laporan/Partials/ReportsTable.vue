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
        <tr v-for="(item, index) in displayedData" :key="index" class="table-row">
          <td class="ps-4 fw-medium">{{ index + 1 }}</td>
          <td>{{ formatDate(item.created_at) }}</td>
          <td>
            <span class="category-badge">{{ item.category }}</span>
          </td>
          <td>{{ item.source || 'SMS' }}</td>
          <td class="description-cell">
            {{ truncateText(item.description, 150) }}
          </td>
          <td>
            <span class="status-badge" :class="getStatusClass(item.status)">
              {{ item.status || 'Tidak Diketahui' }}
            </span>
          </td>
          <td class="text-center">
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
              title="Lihat Detail"
            >
              <i class="fas fa-eye me-1"></i>
              Detail
            </button>
          </td>
        </tr>
        <tr v-if="displayedData.length === 0">
          <td colspan="7" class="empty-state">
            <i class="fas fa-folder-open mb-2"></i>
            <p>Tidak ada laporan yang tersedia</p>
          </td>
        </tr>
      </tbody>
      
      <tbody v-else-if="selectedTab === 'infrastruktur'">
        <tr v-for="(item, index) in displayedData" :key="index" class="table-row">
          <td class="ps-4 fw-medium">{{ index + 1 }}</td>
          <td>{{ formatDate(item.created_at) }}</td>
          <td>
            <span class="category-badge">{{ item.category }}</span>
          </td>
          <td class="description-cell">
            {{ truncateText(item.description, 150) }}
          </td>
          <td>
            <span class="status-badge" :class="getStatusClass(item.status)">
              {{ item.status || 'Tidak Diketahui' }}
            </span>
          </td>
          <td class="text-center">
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
              title="Lihat Detail"
            >
              <i class="fas fa-eye me-1"></i>
              Detail
            </button>
          </td>
        </tr>
        <tr v-if="displayedData.length === 0">
          <td colspan="6" class="empty-state">
            <i class="fas fa-folder-open mb-2"></i>
            <p>Tidak ada laporan yang tersedia</p>
          </td>
        </tr>
      </tbody>
      
      <tbody v-else>
        <tr v-for="(item, index) in displayedData" :key="index" class="table-row">
          <td class="ps-4 fw-medium">{{ index + 1 }}</td>
          <td>{{ formatDate(item.tanggal) }}</td>
          <td class="description-cell">
            {{ truncateText(item.data, 150) }}
          </td>
          <td class="description-cell">
            <span class="query-text">{{ truncateText(item.query, 150) }}</span>
          </td>
          <td>
            <span class="status-badge" :class="getStatusClass(item.hasil)">
              {{ item.hasil || 'Tidak Diketahui' }}
            </span>
          </td>
          <td class="text-center">
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
              title="Lihat Detail"
            >
              <i class="fas fa-eye me-1"></i>
              Detail
            </button>
          </td>
        </tr>
        <tr v-if="displayedData.length === 0">
          <td colspan="6" class="empty-state">
            <i class="fas fa-folder-open mb-2"></i>
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
});

defineEmits(['openDetailModal']);
</script>

<style scoped>
/* Table Header */
.table-header {
  background-color: #0d6efd !important;
  color: white;
  font-weight: 600;
  border: none;
  padding: 16px 12px;
  vertical-align: middle;
}

/* Table Structure */
.table {
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 0;
}

.table-responsive {
  border-radius: 8px;
  overflow: hidden;
}

/* Table Rows */
.table-row {
  background: white;
  transition: all 0.2s ease;
}

.table-row:hover {
  background-color: #f8f9fa;
}

.table-row td {
  padding: 16px 12px;
  border-top: 1px solid #e9ecef;
  vertical-align: middle;
}

/* Description cells with proper truncation */
.description-cell {
  max-width: 250px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Category Badge */
.category-badge {
  background: #f8f9fa;
  color: #495057;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 12px;
  font-weight: 500;
  border: 1px solid #dee2e6;
  display: inline-block;
}

/* Status Badge */
.status-badge {
  padding: 4px 8px;
  border-radius: 20px;
  font-size: 11px;
  font-weight: 600;
  text-transform: capitalize;
  display: inline-block;
  min-width: 70px;
  text-align: center;
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

/* Detail Button */
.detail-btn {
  background: #0d6efd;
  border: none;
  border-radius: 4px;
  padding: 6px 12px;
  font-weight: 500;
  font-size: 12px;
  transition: all 0.2s ease;
  box-shadow: none;
  min-width: 80px;
}

.detail-btn:hover {
  background: #0b5ed7;
  transform: none;
  box-shadow: none;
}

.detail-btn:active {
  background: #0a58ca;
}

.detail-btn i {
  font-size: 12px;
}

/* Query Text */
.query-text {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 11px;
  background: #f8f9fa;
  padding: 4px 6px;
  border-radius: 4px;
  border: 1px solid #dee2e6;
  color: #495057;
  display: inline-block;
  max-width: 100%;
  word-break: break-all;
}

/* Empty State */
.empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #6c757d;
}

.empty-state i {
  font-size: 2.5rem;
  opacity: 0.5;
  display: block;
  margin-bottom: 12px;
}

.empty-state p {
  margin: 0;
  font-size: 16px;
  font-weight: 500;
}

/* Responsive Design */
@media (max-width: 768px) {
  .table-header {
    padding: 12px 8px;
    font-size: 12px;
  }
  
  .table-row td {
    padding: 12px 8px;
  }
  
  .description-cell {
    max-width: 150px;
  }
  
  .detail-btn {
    padding: 6px 12px;
    font-size: 11px;
    min-width: 70px;
  }
  
  .category-badge,
  .status-badge {
    font-size: 10px;
    padding: 4px 8px;
  }
  
  .query-text {
    font-size: 10px;
    padding: 4px 6px;
  }
}

@media (max-width: 576px) {
  .table-responsive {
    font-size: 12px;
  }
  
  .description-cell {
    max-width: 100px;
  }
  
  .detail-btn {
    padding: 4px 8px;
    min-width: 60px;
  }
}
</style>