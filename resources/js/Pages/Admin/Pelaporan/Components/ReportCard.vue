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
    
    <!-- CUSTOMIZED MIDDLE SECTION -->
    <div class="report-content">
      <div class="report-meta-enhanced">
        <div class="meta-row">
          <div class="meta-item-enhanced">
            <div class="meta-icon-wrapper category">
              <i class="bi bi-tag-fill"></i>
            </div>
            <div class="meta-content">
              <span class="meta-label">Kategori</span>
              <span class="meta-value">{{ report.category }}</span>
            </div>
          </div>
        </div>
        
        <div class="meta-row">
          <div class="meta-item-enhanced">
            <div class="meta-icon-wrapper service">
              <i class="bi bi-gear-fill"></i>
            </div>
            <div class="meta-content">
              <span class="meta-label">Layanan</span>
              <span class="meta-value">{{ report.service }}</span>
            </div>
          </div>
        </div>
        
        <div v-if="report.service === 'Penipuan'" class="meta-row">
          <div class="meta-item-enhanced">
            <div class="meta-icon-wrapper source">
              <i class="bi bi-exclamation-triangle-fill"></i>
            </div>
            <div class="meta-content">
              <span class="meta-label">Sumber</span>
              <span class="meta-value">{{ report.source || 'Tidak Ada Sumber' }}</span>
            </div>
          </div>
        </div>
        
        <!-- NEW: Phone Number Display -->
        <div v-if="report.phone_number" class="meta-row">
          <div class="meta-item-enhanced">
            <div class="meta-icon-wrapper phone">
              <i class="bi bi-telephone-fill"></i>
            </div>
            <div class="meta-content">
              <span class="meta-label">Nomor HP</span>
              <span class="meta-value phone-number">{{ report.phone_number }}</span>
            </div>
          </div>
        </div>
        
        <!-- NEW: Location Display -->
        <div v-if="report.location" class="meta-row">
          <div class="meta-item-enhanced">
            <div class="meta-icon-wrapper location">
              <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="meta-content">
              <span class="meta-label">Lokasi</span>
              <span class="meta-value">{{ report.location }}</span>
            </div>
          </div>
        </div>
      </div>
    </div>
    
    <div class="flag-info-enhanced">
      <div class="flag-icon-wrapper">
        <i class="bi bi-flag-fill"></i>
      </div>
      <div class="flag-content">
        <span class="flag-label">Total Laporan</span>
        <span class="flag-count">{{ flagCount }}</span>
      </div>
    </div>
    
    <div class="report-actions">
      <button @click="$emit('viewReport', report)" class="view-button">
        <i class="fas fa-eye"></i> Lihat Detail
      </button>

      <button
        v-if="can.verifyReports"
        :class="['publish-button', getPublishButtonClasses]"
        :disabled="loading || report.status === 'pending' || report.status === 'rejected'"
        @click="$emit('quickAction', { report, action: report.status === 'published' ? 'unpublish' : 'publish' })"
      >
        <span v-if="loading" class="spinner"></span>
        <span v-else>
          {{ report.status === 'published' ? 'Batalkan Publikasi' : 'Publikasikan' }}
        </span>
      </button>

      <div class="action-buttons" v-if="can.verifyReports">
        <button
          @click="$emit('quickAction', { report, action: 'approved' })"
          :class="['quick-action', 'approve', report.status === 'approved' ? 'active' : '', { loading: loading }]"
          title="Approve"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner"></span>
          Approve
        </button>
        <button
          @click="$emit('quickAction', { report, action: 'rejected' })"
          :class="['quick-action', 'reject', report.status === 'rejected' ? 'active' : '', { loading: loading }]"
          title="Reject"
          :disabled="loading"
        >
          <span v-if="loading" class="spinner"></span>
          Reject
        </button>
        <button
          v-if="report.status === 'published'"
          @click="$emit('quickAction', { report, action: 'solved' })"
          class="quick-action solve"
          :disabled="loading"
          title="Mark as Solved"
        >
          <span v-if="loading" class="spinner"></span>
          Tandai Selesai
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  report: Object,
  loading: Boolean,
  can: {
    type: Object,
    default: () => ({}),
  },
});

const emit = defineEmits(['viewReport', 'quickAction']);

const formatDate = (iso) => {
  const d = new Date(iso);
  return d.toLocaleString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric',
    hour: '2-digit', minute: '2-digit',
  });
};

const capitalize = (str) => str.charAt(0).toUpperCase() + str.slice(1);

const getPublishButtonClasses = computed(() => ({
  published: props.report.status === 'published',
  loading: props.loading,
  disabled: props.report.status === 'pending' || props.report.status === 'rejected',
}));

const flagCount = computed(() => {
  if (props.report?.flags && Array.isArray(props.report.flags)) {
    return props.report.flags.length;
  }
  if (typeof props.report?.flags_count === 'number') {
    return props.report.flags_count;
  }
  return 0;
});
</script>

<style scoped>
.report-card {
  background-color: #fff;
  border-radius: 10px;
  overflow: hidden;
  box-shadow: 0 3px 8px rgba(0, 0, 0, 0.05);
  border: 1px solid #eee;
  transition: all 0.3s ease;
  position: relative;
  min-height: 220px;
  display: flex;
  flex-direction: column;
}

.report-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
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
  padding: 10px;
  border-bottom: 1px solid #f0f0f0;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-shrink: 0;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 8px;
}

.user-avatar {
  width: 32px;
  height: 32px;
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
  font-size: 13px;
  color: #333;
}

.timestamp {
  font-size: 11px;
  color: #777;
}

.report-labels {
  display: flex;
  gap: 6px;
}

.status-badge {
  padding: 3px 8px;
  border-radius: 20px;
  font-size: 11px;
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

/* ENHANCED MIDDLE SECTION STYLES */
.report-content {
  padding: 10px;
  flex-grow: 1;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  background: linear-gradient(135deg, #f8f9fb 0%, #ffffff 100%);
}

.report-meta-enhanced {
  margin-bottom: 8px;
}

.meta-row {
  margin-bottom: 6px;
}

.meta-item-enhanced {
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 6px;
  background: rgba(255, 255, 255, 0.7);
  border-radius: 5px;
  border-left: 3px solid transparent;
  transition: all 0.2s ease;
}

.meta-item-enhanced:hover {
  background: rgba(255, 255, 255, 0.9);
  transform: translateX(2px);
}

.meta-icon-wrapper {
  width: 24px;
  height: 24px;
  border-radius: 4px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.meta-icon-wrapper.category {
  background: #6c5ce7;
  color: white;
}

.meta-icon-wrapper.service {
  background: #fd79a8;
  color: white;
}

.meta-icon-wrapper.source {
  background: #fdcb6e;
  color: #2d3436;
}

.meta-icon-wrapper.phone {
  background: #00b894;
  color: white;
}

.meta-icon-wrapper.location {
  background: #e17055;
  color: white;
}

.meta-content {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.meta-label {
  font-size: 10px;
  font-weight: 600;
  color: #666;
  text-transform: uppercase;
  letter-spacing: 0.5px;
  margin-bottom: 1px;
}

.meta-value {
  font-size: 12px;
  font-weight: 500;
  color: #333;
}

.meta-value.phone-number {
  font-family: 'Courier New', monospace;
  font-weight: 600;
  color: #00b894;
}

.flag-info-enhanced {
  padding: 8px 10px;
  border-top: 1px solid #f0f0f0;
  color: #333;
  display: flex;
  align-items: center;
  gap: 8px;
  margin: 0;
  background: white;
}

.flag-icon-wrapper {
  width: 24px;
  height: 24px;
  background: #f8f9fa;
  border-radius: 5px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #6c757d;
}

.flag-content {
  display: flex;
  flex-direction: column;
  flex-grow: 1;
}

.flag-label {
  font-size: 10px;
  font-weight: 500;
  color: #6c757d;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.flag-count {
  font-size: 14px;
  font-weight: 700;
  color: #333;
}

.report-actions {
  padding: 10px;
  border-top: 1px solid #f0f0f0;
  display: flex;
  flex-direction: column;
  gap: 6px;
  flex-shrink: 0;
}

.view-button {
  width: 100%;
  padding: 7px;
  background-color: #4b6cb7;
  color: #fff;
  border: none;
  border-radius: 5px;
  font-weight: 600;
  font-size: 12px;
  cursor: pointer;
  transition: background-color 0.2s;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 5px;
}

.view-button:hover {
  background-color: #3c57a5;
}

.publish-button {
  width: 100%;
  padding: 7px;
  background-color: #f8f9fa;
  color: #6c757d;
  border: 1px solid #dee2e6;
  border-radius: 5px;
  font-weight: 600;
  font-size: 12px;
  cursor: pointer;
  transition: all 0.2s;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 5px;
  margin: 4px 0;
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
  gap: 6px;
}

.quick-action {
  flex: 1;
  padding: 5px 8px;
  border: none;
  border-radius: 4px;
  background-color: #f5f5f5;
  cursor: pointer;
  transition: all 0.2s;
  font-size: 10px;
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
  width: 10px;
  height: 10px;
  border: 2px solid currentColor;
  border-radius: 50%;
  border-top-color: transparent;
  animation: spin 1s linear infinite;
  margin-right: 4px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.quick-action.solve {
  background-color: #17a2b8;
  color: white;
  border-radius: 4px;
  padding: 5px 8px;
  font-weight: 600;
  cursor: pointer;
  transition: background-color 0.2s;
  flex: 1;
}

.quick-action.solve:hover:not(:disabled) {
  background-color: #138496;
}

.quick-action.solve:disabled {
  opacity: 0.6;
  cursor: not-allowed;
}
</style>