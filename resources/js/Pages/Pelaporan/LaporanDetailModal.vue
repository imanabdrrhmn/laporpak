<template>
  <div v-if="isVisible" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <button class="close-btn" @click="closeModal">×</button>

      <div v-if="report && report.id">
        <h2 class="modal-title">{{ report.service }} Report</h2>

        <div class="modal-section">
          <p><strong>Kategori:</strong> {{ report.category }}</p>
          <p>
            <strong>Status:</strong>
            <span :class="['status-badge', report.status]">
              {{ capitalize(report.status) }}
            </span>
          </p>
          <p><strong>Deskripsi:</strong></p>
          <p class="description">{{ report.description }}</p>
          <p>
            <strong>Bukti:</strong>
            <a :href="`/storage/${report.evidence}`" class="evidence-link" target="_blank">
              📎 Lihat Lampiran
            </a>
          </p>
          <p>
            <strong>Lokasi:</strong>
            Latitude: {{ report.latitude }}, Longitude: {{ report.longitude }}
          </p>
        </div>

        <!-- Action Buttons -->
        <div class="modal-actions">
          <button
            v-if="report.status === 'pending'"
            @click="handleApprove"
            class="btn btn-success"
          >
            ✅ Approve
          </button>
          <button
            v-if="report.status === 'pending'"
            @click="handleReject"
            class="btn btn-danger"
          >
            ❌ Reject
          </button>
          <button
            v-if="report.status === 'approved'"
            @click="handlePublish"
            class="btn btn-primary"
          >
            🚀 Publish
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
</template>

<script setup>
import { router } from '@inertiajs/vue3'
import { ref } from 'vue'
import ConfirmationDialog from '@/Components/ConfirmationDialog.vue'

const { report, isVisible } = defineProps({
  report: Object,
  isVisible: Boolean
})

const emit = defineEmits(['close'])

const closeModal = () => emit('close')

const capitalize = (s) => s.charAt(0).toUpperCase() + s.slice(1)

// Confirmation logic
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
    router.patch(`/pelaporan/${report.id}/terima`, {}, {
      preserveScroll: true,
      onSuccess: () => emit('close'),
    })
  }, 'Setujui Laporan', 'Apakah kamu yakin ingin menyetujui laporan ini?')
}

const handleReject = () => {
  confirmWithModal(() => {
    router.patch(`/pelaporan/${report.id}/tolak`, {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.reload({ only: ['reports'] })
        emit('close')
      }
    })
  }, 'Tolak Laporan', 'Laporan akan ditandai sebagai ditolak.')
}

const handlePublish = () => {
  confirmWithModal(() => {
    router.patch(`/pelaporan/${report.id}/publikasikan`, {}, {
      preserveScroll: true,
      onSuccess: () => emit('close')
    })
  }, 'Publikasikan Laporan', 'Laporan akan dipublikasikan ke publik.')
}
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  inset: 0;
  background: rgba(0, 0, 0, 0.45);
  display: flex;
  justify-content: center;
  align-items: center;
  z-index: 1050;
}

.modal-content {
  background: white;
  padding: 24px;
  border-radius: 12px;
  width: 90%;
  max-width: 600px;
  position: relative;
  box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
}

.close-btn {
  position: absolute;
  top: 12px;
  right: 16px;
  font-size: 1.5rem;
  background: none;
  border: none;
  color: #999;
  cursor: pointer;
  font-weight: bold;
}

.modal-title {
  margin-bottom: 20px;
  font-size: 20px;
  font-weight: 600;
}

.modal-section {
  font-size: 15px;
  color: #444;
  line-height: 1.5;
}

.description {
  margin: 5px 0 15px;
  background: #f8f9fa;
  padding: 10px;
  border-radius: 6px;
}

.evidence-link {
  color: #0d6efd;
  text-decoration: underline;
  font-weight: 500;
}

.modal-actions {
  margin-top: 24px;
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  flex-wrap: wrap;
}

.btn {
  padding: 8px 16px;
  border-radius: 6px;
  font-weight: 600;
  border: none;
  color: white;
  cursor: pointer;
  font-size: 14px;
  transition: background-color 0.2s;
}

.btn-success {
  background-color: #28a745;
}
.btn-success:hover {
  background-color: #218838;
}

.btn-danger {
  background-color: #dc3545;
}
.btn-danger:hover {
  background-color: #c82333;
}

.btn-primary {
  background-color: #007bff;
}
.btn-primary:hover {
  background-color: #0069d9;
}

.status-badge {
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 12px;
  font-weight: 600;
  text-transform: capitalize;
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
</style>
