<template>
  <div v-if="isVisible" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <button class="btn-close custom-close" @click="closeModal" aria-label="Tutup"></button>

      <div v-if="report && report.id" class="modal-body">
        <div class="evidence-section">
          <div>
            <strong>Bukti:</strong><br />
            <a :href="report.evidence" target="_blank">
              <div class="evidence-image-container">
                <img
                  :src="report.evidence"
                  alt="Bukti Laporan"
                  class="evidence-image"
                  :class="{ portrait: isPortrait, landscape: !isPortrait }"
                  @load="onImageLoad"
                />
              </div>
            </a>
          </div>
        </div>

        <!-- Judul & Status -->
        <div class="modal-header">
          <h2 class="modal-title">Pelaporan {{ report.service }}</h2>
          <span :class="['status-badge', report.status]">
            {{ capitalize(report.status) }}
          </span>
        </div>

        <!-- Grid Info -->
        <div class="info-grid">
          <div>
            <strong>Tanggal Laporan:</strong><br />
            {{ formatDate(report.created_at) }}
          </div>
          <div>
            <strong>Kategori:</strong><br />
            {{ report.category }}
          </div>
          <div>
            <strong>Lokasi:</strong><br />
            {{ report.address}}
          </div>
          <div v-if="report.source">
            <strong>Sumber Penipuan:</strong><br />
            <a :href="report.source" target="_blank" class="source-link">
              {{ report.source }}
            </a>
          </div>
        </div>

        <!-- Deskripsi -->
        <div class="modal-section">
          <strong>Deskripsi:</strong>
          <div class="description">{{ report.description }}</div>
        </div>

        <div class="flag-info">
          <button class="btn btn-primary" @click="openFlagModal">
            Lihat Laporan
          </button>
        </div>

        <!-- Action Buttons -->
        <div class="modal-actions">
          <button
            v-if="report.status === 'pending'"
            @click="handleApprove"
            class="btn btn-success"
          >
            Approve
          </button>
          <button
            v-if="report.status === 'pending'"
            @click="handleReject"
            class="btn btn-danger"
          >
            Reject
          </button>
          <button
            v-if="report.status === 'approved'"
            @click="handlePublish"
            class="btn btn-primary"
          >
            Publish
          </button>
        </div>
      </div>
    </div>

    <!-- Confirmation Dialog -->
    <ConfirmationDialog
      :visible="confirmVisible"
      :title="confirmTitle"
      :message="confirmMessage"
      @confirm="onConfirm"
      @cancel="() => (confirmVisible = false)"
    />
  </div>

  <ReportFlagModal
    :isOpen="flagModalOpen"
    :flags="reportFlags"
    @close="flagModalOpen = false"
  />
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue'
import ReportFlagModal from './ReportFlagModal.vue'

const props = defineProps({
  report: Object,
  isVisible: Boolean,
})

const emit = defineEmits(['close', 'openFlagSummary', 'actionTriggered'])

const closeModal = () => emit('close')

const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1)

// Date formatting function
const formatDate = (dateString) => {
  if (!dateString) return '-'
  
  const date = new Date(dateString)
  const options = {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    timeZone: 'Asia/Jakarta'
  }
  
  return date.toLocaleDateString('id-ID', options)
}

// Confirmation dialog state & logic
const confirmVisible = ref(false)
const confirmTitle = ref('')
const confirmMessage = ref('')
const confirmAction = ref(() => {})

const confirmWithModal = (action, title, message) => {
  confirmAction.value = action
  confirmTitle.value = title
  confirmMessage.value = message
  confirmVisible.value = true
}

const onConfirm = () => {
  confirmAction.value()
  confirmVisible.value = false
}

const handleApprove = () => {
  confirmWithModal(() => {
    router.patch(`/data/reports/${props.report.id}/accept`, {}, {
      onSuccess: () => emit('close'),
    })
  }, 'Setujui Laporan', 'Apakah kamu yakin ingin menyetujui laporan ini?')
}

const handleReject = () => {
  confirmWithModal(() => {
    router.patch(`/data/reports/${props.report.id}/rejected`, {}, {
      onSuccess: () => {
        router.reload({ only: ['reports'] })
        emit('close')
      }
    })
  }, 'Tolak Laporan', 'Laporan akan ditandai sebagai ditolak.')
}

const handlePublish = () => {
  confirmWithModal(() => {
    router.patch(`/data/reports/${props.report.id}/publish`, {}, {
      onSuccess: () => emit('close')
    })
  }, 'Publikasikan Laporan', 'Laporan akan dipublikasikan ke publik.')
}

// Image aspect ratio
const isPortrait = ref(false)
const onImageLoad = (event) => {
  const { naturalWidth, naturalHeight } = event.target
  isPortrait.value = naturalHeight > naturalWidth
}

// --- Modal Flag Related ---
const flagModalOpen = ref(false)
const reportFlags = ref([])

const openFlagModal = async () => {
  if (!props.report?.id) {
    alert('Report ID tidak ditemukan');
    return;
  }
  try {
    const response = await fetch(`/data/reports/${props.report.id}/flags`);
    if (!response.ok) throw new Error('Gagal mengambil laporan flag');
    const data = await response.json();
    reportFlags.value = data.flags || [];
    flagModalOpen.value = true;
  } catch (error) {
    alert(error.message || 'Gagal mengambil laporan flag');
  }
};

const closeFlagModal = () => {
  flagModalOpen.value = false
}

</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  z-index: 1050;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 16px;
  animation: fadeIn 0.3s ease;
}

.modal-content {
  background: white;
  border-radius: 12px;
  width: 100%;
  max-width: 600px;
  max-height: 85vh;
  position: relative;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
  animation: slideIn 0.3s ease;
  display: flex;
  flex-direction: column;
}

.modal-body {
  padding: 24px;
  overflow-y: auto;
  flex: 1;
  min-height: 0; /* Penting untuk flexbox scroll */
}

.custom-close {
  position: absolute;
  top: 20px;
  right: 20px;
  border-radius: 50%;
  padding: 6px;
  background-color: #f3f4f6;
  opacity: 1;
  transition: all 0.2s ease;
  z-index: 10;
}

.custom-close:hover {
  background-color: #e5e7eb;
  transform: rotate(90deg);
}

/* Remove old close-btn styles */
/* Update responsive styles for close button */
@media (max-width: 768px) {
  .custom-close {
    top: 12px;
    right: 12px;
  }
}

@media (max-width: 480px) {
  .custom-close {
    top: 10px;
    right: 10px;
  }
}

.evidence-section {
  margin-bottom: 20px;
}

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 16px 0;
  gap: 16px;
  flex-wrap: wrap;
}

.modal-title {
  font-size: 20px;
  font-weight: 600;
  text-transform: capitalize;
  margin: 0;
  flex: 1;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 16px;
  margin: 20px 0;
  font-size: 14px;
}

.info-grid > div {
  padding: 12px;
  background: #f8f9fa;
  border-radius: 8px;
  border: 1px solid #e9ecef;
}

.modal-section {
  font-size: 15px;
  color: #444;
  line-height: 1.5;
  margin: 20px 0;
}

.description {
  margin: 8px 0 0 0;
  background: #f8f9fa;
  padding: 16px;
  border-radius: 8px;
  border: 1px solid #e9ecef;
  white-space: pre-wrap;
  word-wrap: break-word;
  line-height: 1.6;
  max-height: none; /* Remove height restriction */
}

.evidence-image-container {
  display: flex;
  justify-content: center;
  align-items: center;
  max-width: 100%;
  max-height: 200px;
  overflow: hidden;
  margin-top: 8px;
  border-radius: 8px;
  border: 1px solid #ddd;
}

.evidence-image {
  max-width: 100%;
  max-height: 100%;
  border-radius: 8px;
  object-fit: contain;
  cursor: zoom-in;
  transition: transform 0.2s ease;
}

.evidence-image:hover {
  transform: scale(1.02);
}

.evidence-image.portrait {
  max-height: 200px;
  width: auto;
}

.evidence-image.landscape {
  width: 100%;
  max-height: 200px;
}

.modal-actions {
  margin-top: 24px;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  flex-wrap: wrap;
  padding-top: 20px;
  border-top: 1px solid #e9ecef;
}

.flag-info {
  margin: 20px 0;
}

.btn {
  padding: 10px 20px;
  border-radius: 6px;
  font-weight: 600;
  border: none;
  color: white;
  cursor: pointer;
  font-size: 14px;
  transition: all 0.2s ease;
  min-width: 80px;
}

.btn-success {
  background-color: #28a745;
}
.btn-success:hover {
  background-color: #218838;
  transform: translateY(-1px);
}

.btn-danger {
  background-color: #dc3545;
}
.btn-danger:hover {
  background-color: #c82333;
  transform: translateY(-1px);
}

.btn-primary {
  background-color: #007bff;
}
.btn-primary:hover {
  background-color: #0069d9;
  transform: translateY(-1px);
}

.status-badge {
  padding: 6px 12px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
  white-space: nowrap;
}
.status-badge.pending {
  background: #fff3cd;
  color: #856404;
}
.status-badge.approved {
  background: #d1e7dd;
  color: #0f5132;
}
.status-badge.rejected {
  background: #f8d7da;
  color: #842029;
}
.status-badge.published {
  background: #cfe2ff;
  color: #084298;
}
.status-badge.unpublished {
 background-color: #e2e3e5;
color: #41464b;
}

.status-badge.solved {
 background: #d4edda;
color: #155724;
}

.modal-body::-webkit-scrollbar {
  width: 6px;
}

.modal-body::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2);
  border-radius: 3px;
}

.modal-body::-webkit-scrollbar-track {
  background-color: transparent;
}

.source-link {
  color: #007bff;
  text-decoration: none;
  word-break: break-all;
}

.source-link:hover {
  text-decoration: underline;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

@keyframes slideIn {
  from { 
    opacity: 0;
    transform: translateY(20px);
  }
  to { 
    opacity: 1;
    transform: translateY(0);
  }
}

/* Enhanced Responsive Styles - Smaller Modals */

/* iPad/Tablet - Smaller modal */
@media (max-width: 1024px) and (min-width: 769px) {
  .modal-overlay {
    padding: 20px;
  }
  
  .modal-content {
    max-width: 500px; /* Reduced from 90% */
    max-height: 80vh; /* Reduced from 90vh */
    border-radius: 10px;
  }

  .modal-body {
    padding: 18px; /* Reduced padding */
  }

  .evidence-image-container {
    max-height: 150px; /* Smaller image */
  }

  .modal-title {
    font-size: 18px; /* Smaller title */
  }

  .info-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 12px; /* Reduced gap */
  }

  .info-grid > div {
    padding: 10px; /* Reduced padding */
    font-size: 13px; /* Smaller font */
  }

  .description {
    padding: 12px; /* Reduced padding */
    font-size: 14px;
  }

  .btn {
    padding: 8px 16px; /* Smaller buttons */
    font-size: 13px;
  }

  .close-btn {
    width: 40px;
    height: 40px;
    font-size: 30px;
  }
}

/* Mobile Large - Even smaller modal */
@media (max-width: 768px) and (min-width: 481px) {
  .modal-overlay {
    padding: 16px;
  }

  .modal-content {
    max-width: 400px; /* Much smaller */
    max-height: 75vh; /* Reduced height */
    border-radius: 8px;
  }

  .modal-body {
    padding: 16px;
  }

  .evidence-image-container {
    max-height: 120px; /* Very small image */
  }

  .modal-title {
    font-size: 16px;
  }

  .info-grid {
    grid-template-columns: 1fr; /* Single column */
    gap: 10px;
  }

  .info-grid > div {
    padding: 8px;
    font-size: 12px;
  }

  .description {
    padding: 10px;
    font-size: 13px;
  }

  .modal-actions {
    gap: 8px;
    margin-top: 16px;
    padding-top: 12px;
  }

  .btn {
    padding: 7px 14px;
    font-size: 12px;
    min-width: 70px;
  }

  .close-btn {
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    font-size: 24px;
  }

  .status-badge {
    padding: 4px 8px;
    font-size: 11px;
  }
}

/* Mobile Small - Minimal modal */
@media (max-width: 480px) {
  .modal-overlay {
    padding: 12px;
  }

  .modal-content {
    max-width: 320px; /* Very small */
    max-height: 70vh; /* Even smaller height */
    border-radius: 6px;
  }

  .modal-body {
    padding: 12px;
  }

  .evidence-image-container {
    max-height: 100px; /* Tiny image */
  }

  .close-btn {
    top: 8px;
    right: 8px;
    width: 32px;
    height: 32px;
    font-size: 20px;
  }

  .modal-title {
    font-size: 14px;
  }

  .info-grid > div {
    padding: 6px;
    font-size: 11px;
  }

  .description {
    padding: 8px;
    font-size: 12px;
  }

  .btn {
    padding: 6px 12px;
    font-size: 11px;
    min-width: 60px;
  }

  .modal-actions {
    margin-top: 12px;
    padding-top: 10px;
  }

  .status-badge {
    padding: 3px 6px;
    font-size: 10px;
  }

  .modal-section {
    margin: 12px 0;
    font-size: 13px;
  }

  .flag-info {
    margin: 12px 0;
  }
}

/* Extra Small Mobile */
@media (max-width: 360px) {
  .modal-content {
    max-width: 280px; /* Even tinier */
    max-height: 65vh;
  }

  .modal-body {
    padding: 10px;
  }

  .evidence-image-container {
    max-height: 80px;
  }

  .modal-title {
    font-size: 13px;
  }

  .info-grid > div {
    padding: 5px;
    font-size: 10px;
  }

  .description {
    padding: 6px;
    font-size: 11px;
  }

  .btn {
    padding: 5px 10px;
    font-size: 10px;
    min-width: 50px;
  }
}
</style>