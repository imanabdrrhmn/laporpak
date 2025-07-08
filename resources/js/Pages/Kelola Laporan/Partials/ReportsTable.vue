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
          <td class="source-cell">
            <div class="source-wrapper">
              <span class="source-text" :class="{ 'is-url': isUrl(item.source) }" :title="item.source">
                {{ formatSource(item.source) }}
              </span>
              <button 
                v-if="item.source && item.source.length > 30" 
                class="btn btn-link btn-sm copy-btn p-0 ms-1"
                @click="copyToClipboard(item.source)"
                title="Copy full source"
              >
                <i class="fas fa-copy"></i>
              </button>
            </div>
          </td>
          <td class="description-cell">
            <div class="description-wrapper">
              <span class="description-text" :title="item.description">
                {{ truncateText(item.description, 120) }}
              </span>
              <button 
                v-if="item.description && item.description.length > 120" 
                class="btn btn-link btn-sm expand-btn p-0 ms-1"
                @click="toggleDescription(index)"
                :title="expandedDescriptions[index] ? 'Show less' : 'Show more'"
              >
                <i :class="expandedDescriptions[index] ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
              </button>
            </div>
            <div v-if="expandedDescriptions[index]" class="expanded-description mt-2">
              {{ item.description }}
            </div>
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
            <div class="description-wrapper">
              <span class="description-text" :title="item.description">
                {{ truncateText(item.description, 120) }}
              </span>
              <button 
                v-if="item.description && item.description.length > 120" 
                class="btn btn-link btn-sm expand-btn p-0 ms-1"
                @click="toggleDescription(index)"
                :title="expandedDescriptions[index] ? 'Show less' : 'Show more'"
              >
                <i :class="expandedDescriptions[index] ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
              </button>
            </div>
            <div v-if="expandedDescriptions[index]" class="expanded-description mt-2">
              {{ item.description }}
            </div>
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
            <div class="description-wrapper">
              <span class="description-text" :title="item.data">
                {{ truncateText(item.data, 120) }}
              </span>
              <button 
                v-if="item.data && item.data.length > 120" 
                class="btn btn-link btn-sm expand-btn p-0 ms-1"
                @click="toggleDescription(index)"
                :title="expandedDescriptions[index] ? 'Show less' : 'Show more'"
              >
                <i :class="expandedDescriptions[index] ? 'fas fa-chevron-up' : 'fas fa-chevron-down'"></i>
              </button>
            </div>
            <div v-if="expandedDescriptions[index]" class="expanded-description mt-2">
              {{ item.data }}
            </div>
          </td>
          <td class="description-cell">
            <span class="query-text">{{ truncateText(item.query, 100) }}</span>
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
          </div>
          
          <!-- Mobile Source with better handling -->
          <div class="mobile-source-wrapper">
            <div class="mobile-source-label">Sumber:</div>
            <div class="mobile-source-content">
              <span class="mobile-source-text" :class="{ 'is-url': isUrl(item.source) }">
                {{ formatSource(item.source, 40) }}
              </span>
              <button 
                v-if="item.source && item.source.length > 40" 
                class="btn btn-link btn-sm copy-btn p-0 ms-1"
                @click="copyToClipboard(item.source)"
                title="Copy full source"
              >
                <i class="fas fa-copy"></i>
              </button>
            </div>
          </div>
          
          <div class="card-description-wrapper">
            <p class="card-description">{{ truncateText(item.description, 80) }}</p>
            <button 
              v-if="item.description && item.description.length > 80" 
              class="btn btn-link btn-sm expand-btn p-0"
              @click="toggleMobileDescription(index)"
            >
              {{ expandedMobileDescriptions[index] ? 'Show less' : 'Show more' }}
            </button>
            <div v-if="expandedMobileDescriptions[index]" class="expanded-mobile-description mt-2">
              {{ item.description }}
            </div>
          </div>
          
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
          
          <div class="card-description-wrapper">
            <p class="card-description">{{ truncateText(item.description, 80) }}</p>
            <button 
              v-if="item.description && item.description.length > 80" 
              class="btn btn-link btn-sm expand-btn p-0"
              @click="toggleMobileDescription(index)"
            >
              {{ expandedMobileDescriptions[index] ? 'Show less' : 'Show more' }}
            </button>
            <div v-if="expandedMobileDescriptions[index]" class="expanded-mobile-description mt-2">
              {{ item.description }}
            </div>
          </div>
          
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
          
          <div class="card-description-wrapper">
            <p class="card-description">{{ truncateText(item.data, 60) }}</p>
            <button 
              v-if="item.data && item.data.length > 60" 
              class="btn btn-link btn-sm expand-btn p-0"
              @click="toggleMobileDescription(index)"
            >
              {{ expandedMobileDescriptions[index] ? 'Show less' : 'Show more' }}
            </button>
            <div v-if="expandedMobileDescriptions[index]" class="expanded-mobile-description mt-2">
              {{ item.data }}
            </div>
          </div>
          
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
import { ref, reactive } from 'vue'

defineProps({
  selectedTab: String,
  displayedData: Array,
  formatDate: Function,
  truncateText: Function,
  getStatusClass: Function,
  getStatusTextClass: Function,
});

// Reactive states for expanded descriptions
const expandedDescriptions = reactive({})
const expandedMobileDescriptions = reactive({})

// Function to check if text is URL
const isUrl = (text) => {
  if (!text) return false;
  return text.startsWith('http://') || text.startsWith('https://') || text.includes('www.') || text.includes('@');
};

// Function to format source text
const formatSource = (source, maxLength = 30) => {
  if (!source) return 'SMS';
  
  if (source.length <= maxLength) return source;
  
  // Handle URLs
  if (isUrl(source)) {
    if (source.includes('@')) {
      // Email
      const [local, domain] = source.split('@');
      if (local.length > 10) {
        return `${local.substring(0, 8)}...@${domain}`;
      }
      return source;
    } else {
      // URL
      try {
        const url = new URL(source.startsWith('http') ? source : `https://${source}`);
        return url.hostname + (url.pathname !== '/' ? '/...' : '');
      } catch {
        return source.substring(0, maxLength - 3) + '...';
      }
    }
  }
  
  return source.substring(0, maxLength - 3) + '...';
};

// Function to toggle description expansion
const toggleDescription = (index) => {
  expandedDescriptions[index] = !expandedDescriptions[index];
};

const toggleMobileDescription = (index) => {
  expandedMobileDescriptions[index] = !expandedMobileDescriptions[index];
};

// Function to copy text to clipboard
const copyToClipboard = async (text) => {
  try {
    await navigator.clipboard.writeText(text);
    // You can add toast notification here
    console.log('Copied to clipboard:', text);
  } catch (err) {
    console.error('Failed to copy:', err);
  }
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

/* Enhanced source cell styling */
.source-cell {
  max-width: 200px;
  position: relative;
}

.source-wrapper {
  display: flex;
  align-items: center;
  gap: 4px;
}

.source-text {
  font-size: 12px;
  color: #6c757d;
  background: #f8f9fa;
  padding: 4px 8px;
  border-radius: 4px;
  word-break: break-all;
  overflow-wrap: break-word;
  line-height: 1.3;
  flex: 1;
}

.source-text.is-url {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 11px;
  color: #0d6efd;
}

/* Enhanced description cell styling */
.description-cell {
  max-width: 300px;
  position: relative;
}

.description-wrapper {
  display: flex;
  align-items: flex-start;
  gap: 4px;
}

.description-text {
  flex: 1;
  word-break: break-word;
  overflow-wrap: break-word;
  line-height: 1.4;
}

.expanded-description {
  padding: 8px 12px;
  background: #f8f9fa;
  border-radius: 4px;
  font-size: 13px;
  line-height: 1.4;
  color: #495057;
  word-break: break-word;
  overflow-wrap: break-word;
  max-height: 200px;
  overflow-y: auto;
}

/* Button styles */
.copy-btn, .expand-btn {
  color: #6c757d;
  font-size: 12px;
  min-width: 20px;
  height: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
  border: none;
  background: none;
  cursor: pointer;
  transition: color 0.2s ease;
}

.copy-btn:hover, .expand-btn:hover {
  color: #0d6efd;
}

.copy-btn:focus, .expand-btn:focus {
  box-shadow: none;
  outline: none;
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
    min-height: 220px;
  }

  .card-content {
    height: 100%;
  }

  .card-description {
    font-size: 14px;
    -webkit-line-clamp: 3;
    line-clamp: 3;
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
}

/* Mobile card layout (below 768px) */
@media (max-width: 767.98px) {
  .cards-container {
    padding: 0 8px;
  }

  .mobile-card {
    min-height: 200px;
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
  min-height: 180px;
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

/* Mobile source styling */
.mobile-source-wrapper {
  margin-bottom: 12px;
  padding: 8px 12px;
  background: #f8f9fa;
  border-radius: 6px;
  border: 1px solid #dee2e6;
}

.mobile-source-label {
  font-size: 10px;
  color: #6c757d;
  font-weight: 600;
  margin-bottom: 4px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.mobile-source-content {
  display: flex;
  align-items: center;
  gap: 4px;
}

.mobile-source-text {
  font-size: 12px;
  color: #495057;
  word-break: break-all;
  overflow-wrap: break-word;
  line-height: 1.3;
  flex: 1;
}

.mobile-source-text.is-url {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
  font-size: 11px;
  color: #0d6efd;
}

/* Mobile description wrapper */
.card-description-wrapper {
  flex: 1;
  margin-bottom: 12px;
}

.card-description {
  font-size: 13px;
  color: #212529;
  line-height: 1.4;
  margin: 0;
  word-break: break-word;
  overflow-wrap: break-word;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.expanded-mobile-description {
  padding: 8px 12px;
  background: #f8f9fa;
  border-radius: 4px;
  font-size: 13px;
  line-height: 1.4;
  color: #495057;
  word-break: break-word;
  overflow-wrap: break-word;
  max-height: 150px;
  overflow-y: auto;
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
    min-height: 180px;
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

  .mobile-source-text {
    font-size: 11px;
  }

  .mobile-source-label {
    font-size: 9px;
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

  .expanded-mobile-description {
    font-size: 12px;
    max-height: 120px;
  }
}

/* Tooltip styles for truncated text */
.source-text[title]:hover,
.description-text[title]:hover {
  cursor: help;
}

/* Scrollbar styling for expanded descriptions */
.expanded-description::-webkit-scrollbar,
.expanded-mobile-description::-webkit-scrollbar {
  width: 4px;
}

.expanded-description::-webkit-scrollbar-track,
.expanded-mobile-description::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.expanded-description::-webkit-scrollbar-thumb,
.expanded-mobile-description::-webkit-scrollbar-thumb {
  background: #c1c1c1;
  border-radius: 4px;
}

.expanded-description::-webkit-scrollbar-thumb:hover,
.expanded-mobile-description::-webkit-scrollbar-thumb:hover {
  background: #a8a8a8;
}

/* Animation for expand/collapse */
.expanded-description,
.expanded-mobile-description {
  animation: fadeIn 0.3s ease-in-out;
}

@keyframes fadeIn {
  from {
    opacity: 0;
    transform: translateY(-10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* Loading state styles (optional) */
.loading-text {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: loading 1.5s infinite;
  border-radius: 4px;
  height: 1em;
  margin: 2px 0;
}

@keyframes loading {
  0% {
    background-position: 200% 0;
  }
  100% {
    background-position: -200% 0;
  }
}

/* Enhanced table row hover effect */
.table-row:hover .source-text,
.table-row:hover .description-text {
  background-color: #e3f2fd;
}

/* Focus styles for accessibility */
.copy-btn:focus,
.expand-btn:focus,
.detail-btn:focus {
  outline: 2px solid #0d6efd;
  outline-offset: 2px;
}

/* Print styles */
@media print {
  .copy-btn,
  .expand-btn,
  .detail-btn {
    display: none;
  }
  
  .expanded-description,
  .expanded-mobile-description {
    display: block !important;
    max-height: none !important;
    overflow: visible !important;
  }
  
  .mobile-cards {
    display: none;
  }
  
  .table-responsive {
    display: block !important;
  }
}
</style>