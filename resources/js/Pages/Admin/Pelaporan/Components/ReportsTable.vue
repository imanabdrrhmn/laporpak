<!-- resources/js/Components/Pelaporan/ReportsTable.vue -->
<template>
  <table class="reports-table">
    <thead>
      <tr>
        <th>No</th>
        <th>Pelapor</th>
        <th>Kategori</th>
        <th>Layanan</th>
        <th>Status</th>
        <th>Deskripsi</th>
        <th>Tanggal</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="(report, index) in reports" :key="report.id" :class="['report-row', report.status]">
        <td class="number-cell">{{ (currentPage - 1) * itemsPerPage + index + 1 }}</td>
        <td>
          <div class="user-info">
            <img :src="report.user.avatar_url" alt="User Avatar" class="user-avatar-small" />
            <span class="user-name">{{ report.user.name }}</span>
          </div>
        </td>
        <td>{{ report.category }}</td>
        <td>{{ report.service }}</td>
        <td>
          <span :class="['status-badge', report.status]">
            {{ capitalize(report.status) }}
          </span>
        </td>
        <td class="description-cell">{{ truncate(report.description, 60) }}</td>
        <td>{{ formatDateShort(report.created_at) }}</td>
        <td>
          <div class="table-actions">
            <button @click="$emit('viewReport', report)" class="table-action view" title="Lihat Detail">
              <i class="fas fa-eye"></i>
            </button>
            <button
              @click="$emit('quickAction', { report, action: 'approved' })"
              :class="['table-action', 'approve', report.status === 'approved' ? 'active' : '', { 'loading': loading[report.id] }]"
              title="Approve"
              :disabled="loading[report.id]"
            >
              <span v-if="loading[report.id]" class="spinner"></span>
              Approve
            </button>
            <button
              @click="$emit('quickAction', { report, action: 'rejected' })"
              :class="['table-action', 'reject', report.status === 'rejected' ? 'active' : '', { 'loading': loading[report.id] }]"
              title="Reject"
              :disabled="loading[report.id]"
            >
              <span v-if="loading[report.id]" class="spinner"></span>
              Reject
            </button>
            <button
              @click="$emit('quickAction', { report, action: 'published' })"
              :class="['table-action', 'publish', report.status === 'published' ? 'active' : '', { 'loading': loading[report.id] }]"
              title="Publish"
              :disabled="loading[report.id]"
            >
              <span v-if="loading[report.id]" class="spinner"></span>
              Publish
            </button>
          </div>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  reports: Array,
  currentPage: Number,
  itemsPerPage: Number,
  loading: Object
});

const emit = defineEmits(['viewReport', 'quickAction']);

const formatDateShort = (iso) => {
  const d = new Date(iso);
  return d.toLocaleString('id-ID', {
    day: 'numeric', month: 'short',
    hour: '2-digit', minute: '2-digit'
  });
};

const truncate = (text, max) => {
  return text && text.length > max ? text.substring(0, max) + '…' : text || '';
};

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);
</script>

<style scoped>
.reports-table {
  width: 100%;
  border-collapse: collapse;
  background-color: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
}

.reports-table thead {
  background-color: #f5f7fa;
}

.reports-table th {
  padding: 16px;
  text-align: left;
  font-weight: 600;
  color: #444;
  border-bottom: 1px solid #eee;
  font-size: 13px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.reports-table th:first-child {
  text-align: center;
}

.reports-table td {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  font-size: 14px;
  color: #444;
}

.number-cell {
  text-align: center;
  width: 60px;
}

.report-row {
  transition: background-color 0.2s;
}

.report-row:hover {
  background-color: #f9f9f9;
}

.report-row.pending {
  border-left: 3px solid #ffc107;
}

.report-row.approved {
  border-left: 3px solid #28a745;
}

.report-row.rejected {
  border-left: 3px solid #dc3545;
}

.report-row.published {
  border-left: 3px solid #0d6efd;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-avatar-small {
  width: 32px;
  height: 32px;
  border-radius: 50%;
  object-fit: cover;
}

.user-name {
  font-weight: 600;
  font-size: 14px;
  color: #333;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 30px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
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

.description-cell {
  max-width: 200px;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.table-actions {
  display: flex;
  gap: 6px;
}

.table-action {
  background-color: #f5f5f5;
  border: none;
  border-radius: 4px;
  padding: 6px 12px;
  min-width: 70px;
  height: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  font-size: 12px;
  font-weight: 600;
  transition: all 0.2s;
  position: relative;
}

.table-action:hover {
  filter: brightness(0.95);
}

.table-action.view {
  color: #666;
  min-width: 30px;
}

.table-action.approve {
  color: #28a745;
}

.table-action.reject {
  color: #dc3545;
}

.table-action.publish {
  color: #0d6efd;
}

.table-action.active {
  color: white;
}

.table-action.approve.active {
  background-color: #28a745;
}

.table-action.reject.active {
  background-color: #dc3545;
}

.table-action.publish.active {
  background-color: #0d6efd;
}

.table-action.loading {
  opacity: 0.7;
  cursor: not-allowed;
}

.table-action .spinner {
  display: inline-block;
  width: 12px;
  height: 12px;
  border: 2px solid currentColor;
  border-radius: 50%;
  border-top-color: transparent;
  animation: spin 1s linear infinite;
  margin-right: 6px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

@media (max-width: 768px) {
  .reports-table {
    display: block;
    overflow-x: auto;
  }
  
  .reports-table th, 
  .reports-table td {
    padding: 12px;
  }
  
  .description-cell {
    max-width: 150px;
  }
  
  .table-actions {
    flex-wrap: wrap;
  }
  
  .table-action {
    min-width: 60px;
    padding: 6px 8px;
  }
  
  .number-cell {
    width: 40px;
  }
}
</style>