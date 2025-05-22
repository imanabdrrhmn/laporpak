<template>
  <div v-if="isVisible" class="modal-overlay" @click="closeModal">
    <div class="modal-content" @click.stop>
      <button class="close-btn" @click="closeModal">×</button>

      <div v-if="report && report.id">
        <!-- Gambar Bukti -->
        <p>
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
    </p>


        <!-- Judul & Status -->
        <div class="modal-header">
          <h2 class="modal-title">{{ report.service }} Report</h2>
          <span :class="['status-badge', report.status]">
            {{ capitalize(report.status) }}
          </span>
        </div>

        <!-- Grid Info -->
        <div class="info-grid">
          <div v-if="report.service === 'fraud'" style="margin-top: 12px">
          <strong>Sumber Penipuan:</strong><br />
          <a>
            {{ report.source }}
          </a>
        </div>
          <div>
            <strong>Kategori:</strong><br />
            {{ report.category }}
          </div>
          <div>
            <strong>Lokasi:</strong><br />
            {{ report.address}}
          </div>
        </div>

        <!-- Deskripsi -->
        <div class="modal-section">
          <strong>Deskripsi:</strong>
          <p class="description">{{ report.description }}</p>
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

const isPortrait = ref(false)

const onImageLoad = (event) => {
  const { naturalWidth, naturalHeight } = event.target
  isPortrait.value = naturalHeight > naturalWidth
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
  overflow-y: auto;
  padding: 40px 16px;
}


.modal-content {
  background: white;
  padding: 24px;
  border-radius: 12px;
  width: 100%;
  max-width: 600px;
  max-height: 90vh; 
  overflow-y: auto;  
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

.modal-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin: 16px 0;
}

.modal-title {
  font-size: 20px;
  font-weight: 600;
  text-transform: capitalize;
}

.info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 12px;
  margin-bottom: 16px;
  font-size: 14px;
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


.evidence-image-container {
  display: flex;
  justify-content: center;
  align-items: center;
  max-width: 100%;
  height: 200px; 
  overflow: hidden;
}


.evidence-image {
  margin-top: 8px;
  max-width: 100%;
  border-radius: 8px;
  border: 1px solid #ddd;
  object-fit: contain;
  cursor: zoom-in;
  transition: transform 0.2s ease;
}

.evidence-image:hover {
  transform: scale(1.02);
}

/* Portrait image */
.evidence-image.portrait {
  max-height: 100%;
  width: auto;
}

/* Landscape image */
.evidence-image.landscape {
  width: 100%;
  max-height: 300px;
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

.modal-content::-webkit-scrollbar {
  width: 8px; /* Lebar scrollbar */
}

.modal-content::-webkit-scrollbar-thumb {
  background-color: rgba(0, 0, 0, 0.2); /* Warna thumb */
  border-radius: 4px; /* Sudut bulat */
}

.modal-content::-webkit-scrollbar-track {
  background-color: transparent; /* Warna track */
}

</style>
