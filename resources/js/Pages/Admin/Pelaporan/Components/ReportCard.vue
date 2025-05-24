<!-- resources/js/Components/Pelaporan/ReportCard.vue -->
<template>
  <div class="report-card">
    <div :class="['report-badge', report.status]"></div>
    <div class="report-header">
      <div class="user-info">
        <img :src="report.user.avatar_url" alt="User Avatar" class="user-avatar" />
        <div class="user-details">
          <span class="user-name">{{ report.user.name }}</span>
          <span class="timestamp">{{ formatDate(report.created_at) }}</span>
        </div>
      </div>
      <div class="report-labels">
        <span :class="['status-badge', report.status]">
          {{ capitalize(report.status) }}
        </span>
      </div>
    </div>
    <div class="report-content">
      <div class="report-meta">
        <div class="meta-item">
          <i class="fas fa-tag meta-icon"></i>
          <span>{{ report.category }}</span>
        </div>
        <div class="meta-item">
          <i class="fas fa-cog meta-icon"></i>
          <span>{{ report.service }}</span>
        </div>
        <div v-if="report.service === 'Penipuan'" class="meta-item">
          <i class="fas fa-exclamation-triangle meta-icon"></i>
          <span>{{ report.source || 'Tidak Ada Sumber' }}</span>
        </div>
      </div>
      <p class="description">{{ truncate(report.description, 120) }}</p>
    </div>
    <div class="report-actions">
      <button @click="$emit('viewReport', report)" class="view-button">
        <i class="fas fa-eye"></i> Lihat Detail
      </button>
      <button
        :class="['publish-button', getPublishButtonClasses]"
        :disabled="loading || report.status === 'pending' || report.status === 'rejected'"
        @click="$emit('quickAction', { report, action: 'published' })"
      >
        <span v-if="loading" class="spinner"></span>
        <span v-if="report.status === 'pending'">Perlu Disetujui</span>
        <span v-else-if="report.status === 'rejected'">Ditolak</span>
        <span v-else>{{ report.status === 'published' ? 'Published' : 'Unpublished' }}</span>
      </button>
      <div class="action-buttons">
        <button
          @click="$emit('quickAction', { report, action: 'approved' })"
          :class="['quick-action', 'approve', report.status === 'approved' ? 'active' : '', { 'loading': loading }]"
          title="Approve"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner"></span>
          Approve
        </button>
        <button
          @click="$emit('quickAction', { report, action: 'rejected' })"
          :class="['quick-action', 'reject', report.status === 'rejected' ? 'active' : '', { 'loading': loading }]"
          title="Reject"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner"></span>
          Reject
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  report: Object,
  loading: Boolean
});

const emit = defineEmits(['viewReport', 'quickAction']);

const formatDate = (iso) => {
  const d = new Date(iso);
  return d.toLocaleString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit'
  });
};

const truncate = (text, max) => {
  return text && text.length > max ? text.substring(0, max) + '…' : text || '';
};

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);

const getPublishButtonClasses = computed(() => ({
  'published': props.report.status === 'published',
  'loading': props.loading,
  'disabled': props.report.status === 'pending' || props.report.status === 'rejected'
}));
</script>

<style scoped>
.report-card {
  background-color: #fff;
  border-radius: 12px;
  overflow: hidden;
  box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
  border: 1px solid #eee;
  transition: all 0.3s ease;
  position: relative;
  min-height: 380px;
  display: flex;
  flex-direction: column;
}

.report-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
}

.report-badge {
  position: absolute;
  top: 0;
  left: 0;
  width: 4px;
  height: 100%;
}

.report-badge.pending { background-color: #ffc107; }
.report-badge.approved { background-color: #28a745; }
.report-badge.rejected { background-color: #dc3545; }
.report-badge.published { background-color: #0d6efd; }

.report-header {
  padding: 16px;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-avatar {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid #fff;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.user-details {
  display: flex;
  flex-direction: column;
}

.user-name {
  font-weight: 600;
  font-size: 14px;
  color: #333;
}

.timestamp {
  font-size: 12px;
  color: #777;
}

.report-labels {
  display: flex;
  gap: 8px;
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

.report-content {
  padding: 16px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
}

.report-meta {
  margin-bottom: 12px;
  display: -webkit-box;
  -webkit-line-clamp: 3;
  line-clamp: 3;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.meta-item {
  display: flex;
  align-items: center;
  margin-bottom: 6px;
  font-size: 13px;
  color: #555;
}

.meta-icon {
  width: 16px;
  margin-right: 8px;
  color: #777;
}

.description {
  font-size: 14px;
  line-height: 1.5;
  color: #444;
  margin: 0;
  display: -webkit-box;
  -webkit-line-clamp: 4;
  line-clamp: 4;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
}

.report-actions {
  padding: 16px;
  border-top: 1px solid #f0f0f0;
  display: flex;
  flex-direction: column;
  gap: 10px;
  flex-shrink: 0;
}

.view-button {
  width: 100%;
  padding: 10px;
  background-color: #4b6cb7;
  color: #fff;
  border: none;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
}

.view-button:hover {
  background-color: #3c57a5;
}

.publish-button {
  width: 100%;
  padding: 10px;
  background-color: #f8f9fa;
  color: #6c757d;
  border: 1px solid #dee2e6;
  border-radius: 8px;
  font-weight: 600;
  font-size: 14px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;
  margin: 8px 0;
}

.publish-button.published {
  background-color: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

.publish-button.disabled {
  background-color: #e9ecef;
  color: #adb5bd;
  cursor: not-allowed;
  border-color: #dee2e6;
}

.publish-button.disabled:hover {
  background-color: #e9ecef;
  color: #adb5bd;
  border-color: #dee2e6;
}

.publish-button:hover {
  background-color: #0d6efd;
  color: white;
  border-color: #0d6efd;
}

.action-buttons {
  display: flex;
  gap: 8px;
}

.quick-action {
  flex: 1;
  padding: 8px 12px;
  border: none;
  border-radius: 6px;
  background-color: #f5f5f5;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 12px;
  font-weight: 600;
  text-align: center;
  position: relative;
}

.quick-action:hover {
  filter: brightness(0.95);
}

.quick-action.approve {
  color: #28a745;
}

.quick-action.reject {
  color: #dc3545;
}

.quick-action.publish {
  color: #0d6efd;
}

.quick-action.active {
  color: white;
}

.quick-action.approve.active {
  background-color: #28a745;
}

.quick-action.reject.active {
  background-color: #dc3545;
}

.quick-action.publish.active {
  background-color: #0d6efd;
}

.quick-action.loading {
  opacity: 0.7;
  cursor: not-allowed;
}

.quick-action .spinner {
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
</style>