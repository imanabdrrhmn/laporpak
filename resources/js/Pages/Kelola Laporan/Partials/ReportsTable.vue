<template>
  <!-- Desktop View - Table (1400px and above) -->
  <div class="table-responsive d-none d-xl-block">
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

  <!-- iPad/Mobile View - Cards (Below 1400px) -->
  <div class="mobile-cards d-block d-xl-none">
    <!-- Penipuan Cards -->
    <div v-if="selectedTab === 'penipuan'" class="cards-container">
      <div v-for="(item, index) in displayedData" :key="index" class="mobile-card">
        <div class="card-content">
          <div class="card-header-simple">
            <span class="card-number">#{{ index + 1 }}</span>
            <span class="card-date">{{ formatDate(item.created_at) }}</span>
          </div>
          
          <div class="card-info">
            <div class="category-badge">{{ item.category }}</div>
            <div :class="isUrl(item.source) ? 'url-text' : 'source-text'">
              {{ item.source || 'SMS' }}
            </div>
          </div>
          
          <p class="card-description">{{ truncateText(item.description, 80) }}</p>
          
          <div class="card-bottom">
            <span class="status-badge" :class="getStatusClass(item.status)">
              {{ item.status || 'Tidak Diketahui' }}
            </span>
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
            >
              Detail
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Infrastruktur Cards -->
    <div v-else-if="selectedTab === 'infrastruktur'" class="cards-container">
      <div v-for="(item, index) in displayedData" :key="index" class="mobile-card">
        <div class="card-content">
          <div class="card-header-simple">
            <span class="card-number">#{{ index + 1 }}</span>
            <span class="card-date">{{ formatDate(item.created_at) }}</span>
          </div>
          
          <div class="card-info">
            <div class="category-badge">{{ item.category }}</div>
          </div>
          
          <p class="card-description">{{ truncateText(item.description, 80) }}</p>
          
          <div class="card-bottom">
            <span class="status-badge" :class="getStatusClass(item.status)">
              {{ item.status || 'Tidak Diketahui' }}
            </span>
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
            >
              Detail
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Verifikasi Cards -->
    <div v-else class="cards-container">
      <div v-for="(item, index) in displayedData" :key="index" class="mobile-card">
        <div class="card-content">
          <div class="card-header-simple">
            <span class="card-number">#{{ index + 1 }}</span>
            <span class="card-date">{{ formatDate(item.tanggal) }}</span>
          </div>
          
          <p class="card-description">{{ truncateText(item.data, 60) }}</p>
          
          <div class="query-simple">{{ truncateText(item.query, 50) }}</div>
          
          <div class="card-bottom">
            <span class="status-badge" :class="getStatusClass(item.hasil)">
              {{ item.hasil || 'Tidak Diketahui' }}
            </span>
            <button 
              class="btn btn-primary btn-sm detail-btn" 
              @click="$emit('openDetailModal', item)"
            >
              Detail
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Empty State for Cards -->
    <div v-if="displayedData.length === 0" class="mobile-empty-state">
      <i class="fas fa-folder-open"></i>
      <p v-if="selectedTab === 'penipuan'">Tidak ada laporan penipuan</p>
      <p v-else-if="selectedTab === 'infrastruktur'">Tidak ada laporan infrastruktur</p>
      <p v-else>Tidak ada verifikasi</p>
    </div>
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

// Function to check if text is URL
const isUrl = (text) => {
  if (!text) return false;
  return text.startsWith('http://') || text.startsWith('https://') || text.includes('www.') || text.length > 30;
};

// Function to truncate URL
const truncateUrl = (url, maxLength = 30) => {
  if (!url || url.length <= maxLength) return url;
  
  if (url.startsWith('http://') || url.startsWith('https://')) {
    const parts = url.split('/');
    const domain = parts[2];
    const path = parts.slice(3).join('/');
    
    if (domain.length > maxLength - 3) {
      return domain.substring(0, maxLength - 3) + '...';
    }
    
    if (path && (domain + '/' + path).length > maxLength) {
      return domain + '/...' + path.substring(path.length - (maxLength - domain.length - 6));
    }
  }
  
  return url.substring(0, maxLength - 3) + '...';
};

defineEmits(['openDetailModal']);
</script>

<style scoped>
/* Desktop Table Styles */
.table-header {
  background-color: #0d6efd !important;
  color: white;
  font-weight: 600;
  border: none;
  padding: 16px 12px;
  vertical-align: middle;
}

.table {
  border-collapse: separate;
  border-spacing: 0;
  margin-bottom: 0;
}

.table-responsive {
  border-radius: 8px;
  overflow: hidden;
}

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

.description-cell {
  max-width: 250px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

/* Desktop Empty State */
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

/* Query Text for Desktop Table */
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
  overflow-wrap: break-word;
}

/* Mobile Cards Styles */
.mobile-cards {
  padding: 0;
}

.cards-container {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-top: 28px;
  margin-bottom: 28px;
}

/* iPad card layout (768px to 1399px) */
@media (min-width: 768px) and (max-width: 1399.98px) {
  .cards-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 20px;
    padding: 0 12px;
  }

  .mobile-card {
    margin: 0;
    height: 100%;
    min-height: 180px;
  }

  .card-content {
    height: 100%;
  }

  .card-description {
    font-size: 14px;
    -webkit-line-clamp: 4;
    line-clamp: 4;
  }

  .card-number {
    font-size: 14px;
  }

  .card-date {
    font-size: 12px;
  }

  .category-badge,
  .status-badge {
    font-size: 11px;
  }

  .query-simple {
    font-size: 11px;
  }

  .detail-btn {
    font-size: 12px;
    padding: 8px 16px;
  }

  .source-text, .url-text, .email-text {
    font-size: 12px;
  }
}

/* Mobile card layout (below 768px) */
@media (max-width: 767.98px) {
  .cards-container {
    padding: 0 8px;
  }

  .mobile-card {
    min-height: 160px;
  }

  .card-description {
    -webkit-line-clamp: 3;
    line-clamp: 3;
  }
}

.mobile-card {
  background: white;
  border: 1px solid #e9ecef;
  border-radius: 8px;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  transition: all 0.2s ease;
  min-height: 160px;
  display: flex;
  flex-direction: column;
}

.mobile-card:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
}

.card-content {
  padding: 12px;
  display: flex;
  flex-direction: column;
  height: 100%;
  flex: 1;
}

.card-header-simple {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 8px;
  flex-shrink: 0;
}

.card-number {
  font-weight: 700;
  font-size: 13px;
  color: #0d6efd;
}

.card-date {
  font-size: 11px;
  color: #6c757d;
}

.card-info {
  display: flex;
  align-items: flex-start;
  gap: 8px;
  margin-bottom: 8px;
  flex-wrap: wrap;
}

.source-text {
  font-size: 11px;
  color: #6c757d;
  background: #f8f9fa;
  padding: 2px 6px;
  border-radius: 4px;
  word-break: break-all;
  overflow-wrap: break-word;
  max-width: 100%;
  line-height: 1.2;
  white-space: normal;
}

.url-text, .email-text {
  font-size: 11px;
  color: #6c757d;
  background: #f8f9fa;
  padding: 2px 6px;
  border-radius: 4px;
  word-break: break-all;
  overflow-wrap: break-word;
  max-width: 100%;
  line-height: 1.2;
  white-space: normal;
  display: block;
  margin-top: 4px;
}

.card-description {
  font-size: 13px;
  color: #212529;
  line-height: 1.3;
  margin: 0 0 8px 0;
  flex: 1;
  word-break: break-word;
  overflow-wrap: break-word;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.query-simple {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 10px;
  background: #f8f9fa;
  color: #495057;
  padding: 6px 8px;
  border-radius: 4px;
  margin-bottom: 8px;
  border: 1px solid #dee2e6;
  word-break: break-all;
  overflow-wrap: break-word;
  white-space: normal;
  line-height: 1.2;
  max-height: 4em;
  overflow: hidden;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
}

.card-bottom {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-top: auto;
  flex-shrink: 0;
  gap: 8px;
}

/* Category Badge */
.category-badge {
  background: #e3f2fd;
  color: #1565c0;
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
  display: inline-block;
  word-break: break-word;
  max-width: 100%;
  flex-shrink: 0;
}

/* Status Badge */
.status-badge {
  padding: 3px 8px;
  border-radius: 12px;
  font-size: 10px;
  font-weight: 600;
  text-transform: capitalize;
  display: inline-block;
  word-break: break-word;
  max-width: 100%;
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
  border-radius: 6px;
  padding: 6px 12px;
  font-weight: 600;
  font-size: 11px;
  transition: all 0.2s ease;
  box-shadow: none;
  color: white;
  flex-shrink: 0;
}

.detail-btn:hover {
  background: #0b5ed7;
  box-shadow: none;
  color: white;
}

.detail-btn:active {
  background: #0a58ca;
}

/* Mobile Empty State */
.mobile-empty-state {
  text-align: center;
  padding: 40px 20px;
  color: #6c757d;
  background: white;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.mobile-empty-state i {
  font-size: 2rem;
  opacity: 0.4;
  display: block;
  margin-bottom: 8px;
  color: #adb5bd;
}

.mobile-empty-state p {
  margin: 0;
  font-size: 14px;
  font-weight: 500;
}

/* Responsive adjustments for very small screens */
@media (max-width: 360px) {
  .mobile-card {
    margin: 0 -8px;
    border-radius: 8px;
    min-height: 140px;
  }
  
  .card-content {
    padding: 12px;
  }
  
  .card-number {
    font-size: 12px;
  }
  
  .card-date {
    font-size: 10px;
  }
  
  .card-description {
    font-size: 12px;
    -webkit-line-clamp: 2;
    line-clamp: 2;
  }

  .source-text, .url-text, .email-text {
    font-size: 10px;
  }

  .category-badge,
  .status-badge {
    font-size: 9px;
    padding: 2px 6px;
  }

  .query-simple {
    font-size: 9px;
    padding: 4px 6px;
  }

  .detail-btn {
    font-size: 10px;
    padding: 4px 8px;
  }
}
</style>